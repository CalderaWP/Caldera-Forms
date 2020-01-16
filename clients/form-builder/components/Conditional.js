import {NewGroupName} from "./Conditionals";

/**
 * The section to set which fields it applies to
 *
 * @since 1.8.10
 *
 * @param Array fields The form's fields
 * @param Array fieldsUsed The fields used by this conditional.
 * @param Array appliedFields The fields this conditional is applied to.
 * @param strings Translation strings
 * @param onChange
 * @param groupId
 * @returns {*}
 * @constructor
 */
export const AppliesToFields = ({fields, fieldsUsed, appliedFields, strings, onChange, groupId}) => {

    return (
        <div style={{float: 'left', width: '288px', paddingLeft: '12px'}}>
            <h4 style={{borderBottom: `1px solid rgb(191, 191, 191)`, margin: `0px 0px 6px; padding: 0px 0px 6px`}}>
                {strings['applied-fields']}
            </h4>
            <p className="description">{strings['select-apply-fields']}</p>
            {fields.map(field => (
                    <label style={{display: 'block', marginLeft: `20px`}}>
                        {field.label}
                        <input
                            style={{marginLeft: `-20px`}}
                            type="checkbox"
                            disabled={fieldsUsed.includes(field.id)}
                            onClick={(e) => {
                                e.preventDefault();
                                if (!appliedFields || !appliedFields.length) {
                                    onChange([field.id])

                                } else if (appliedFields.include(field.id)) {
                                    onChange(appliedFields.filter(f => f.id === field.id))
                                } else {
                                    onChange([...appliedFields, field.id])
                                }
                            }}
                            value={groupId}
                        />
                    </label>
                )
            )}
        </div>
    );
};

/**
 * One line in a group
 *
 * @since 1.8.10
 *
 * @param line
 * @param strings
 * @param isFirst
 * @param fieldOptions
 * @returns {*}
 * @constructor
 */
const ConditionalLine = ({line, strings, isFirst, fieldOptions}) => {

    return (
        <div key={line.id} className={`caldera-condition-line condition-line-${line.id}`}>
                        <span style={{display: "inline-block"}}>
                            {isFirst ? <React.Fragment>{strings['if']}</React.Fragment> :
                                <React.Fragment>{strings['and']}</React.Fragment>}
                        </span>

            <select
                style={{maxWidth: "120px", verticalAlign: 'inherit'}}
                className="condition-line-field"
            >
                <option/>
                <optgroup label={strings['fields']}>
                    {fields.map(field => (
                        <option value={field.id}>
                            {field.label} [{field.slug}]
                        </option>
                    ))}
                </optgroup>
            </select>
            <select
                className="condition-line-compare"
                style={{maxWidth: "120px", verticalAlign: 'inherit'}}
            >
                {['is', 'isnot', 'greater', 'smaller', 'startswith', 'endswith', 'contains'].map(compareType => (
                    <option key={compareType} value={compareType}>{compareType}</option>
                ))}

            </select>
            {fieldOptions ? (<select
                    style={{maxWidth: "165px", verticalAlign: 'inherit'}}>
                    <option/>
                    {fieldOptions.map(option => <option key={option.value}
                                                        value={option.value}>{option.label}</option>)}

                </select>
            ) : (
                <input
                    type="text"
                    className="magic-tag-enabled block-input"
                />
            )}
        </div>
    );
};

/**
 * All of the lines of one conditional
 *
 * @since 1.8.10
 *
 * @param lines
 * @param strings
 * @returns {*}
 * @constructor
 */
const ConditionalLines = ({lines, strings}) => {

    let isFirst = true;
    return (
        <div className="caldera-condition-group caldera-condition-lines">
            {lines.map(line => {
                const r = (
                    <ConditionalLine line={line} isFirst={isFirst} strings={strings}/>
                );
                isFirst = false;
                return r;
            })}

            <div style={{margin: "12px 0 0"}}>
                <button
                    className="button button-small condition-group-add-line"
                    type="button"
                >
                    {strings['add-condition']}
                </button>
            </div>
        </div>)
};

/**
 * One conditional
 *
 * @since 1.8.10
 *
 * @param name
 * @param strings
 * @param id
 * @param type
 * @param group
 * @param fields
 * @returns {*}
 * @constructor
 */
const Conditional = ({name, strings, id, type, group, fields}) => {
    const fieldsUsed = React.useMemo(() => {
        return this.getUsedFields(fields);
    }, [id]);
    const onAddLine = () => {
    };
    return (
        <div className="caldera-editor-condition-config caldera-forms-condition-edit"
             style={{marginTop: '-27px', width: "auto"}}
        >
            {name ? (
                <NewGroupName placeholder={strings['new-group-name']} id={id}/>
            ) : (
                <div
                    className={`condition-point-${id}`}
                    style={{float: 'left', width: "550px"}}
                >

                    <div className="caldera-config-group">
                        <label htmlFor={`condition-group-name-${id}`}>
                            {strings.name}
                        </label>
                        <div className="caldera-config-field">
                            <input
                                type="text"
                                name={`conditions[${id}][name]`}
                                id={`condition-group-name-${id}`}
                                value="{{name}}"
                                required
                                className="required block-input condition-group-name"
                            />
                        </div>
                    </div>

                    <div className="caldera-config-group">
                        <label htmlFor={`condition-group-type-${id}`}>
                            {strings.type}
                        </label>
                        <div className="caldera-config-field">
                            <select
                                id={`condition-group-type-${id}`}
                                name={`conditions[${id}][type]`}
                                className="condition-group-type"
                            >
                                <option value=""/>
                                <option value="show">
                                    {strings.show}
                                </option>
                                <option value="hide">
                                    {strings.hide}
                                </option>
                                <option value="disable">
                                    {strings.disable}
                                </option>
                            </select>
                            {type &&
                            <button
                                type="button"
                                className="pull-right button button-small condition-group-add-lines"
                                onClick={onAddLine}
                            >
                                {strings['add-conditional-line']}
                            </button>
                            }
                        </div>
                    </div>
                    <ConditionalLines lines={group.lines} strings={strings}/>
                    <button
                        style={{margin: "12px 0 12px"}}
                        type="button"
                        className="block-input button"
                        data-confirm={strings['confirm-remove']}
                    >
                        {strings['remove-condition']}
                    </button>
                    <AppliesToFields fields={fields} fieldsUsed={fieldsUsed}/>
                </div>
            )}
        </div>
    )
};

/**
 * Get the fields that this conditional group applies to.
 *
 * @since 1.8.10
 *
 * @param fields
 * @param group
 * @returns {[]}
 */
Conditional.getNotAllowedFields = (fields,group) => {
    const applied = [];
    return applied;
};

/**
 * Get the fields used in the rules of this conditional, which can therefore NOT be applied
 *
 * @since 1.8.0
 *
 * @param fields
 * @param group
 * @returns {[]}
 */
Conditional.getUsedFields = (fields,group) => {
    const used = [];
    return used;
};

export default  Conditional;