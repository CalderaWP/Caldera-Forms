import React from 'react';
import {NewGroupName} from "./Conditionals";

/**
 * The section to set which fields it applies to
 *
 * @since 1.8.10
 *
 * @param Array formFields The form's fields
 * @param Array fieldsUsed The fields used by this conditional.
 * @param Array appliedFields The fields this conditional is applied to.
 * @param Array notAllowedFields The fields this conditional can NOT applied to.
 * @param strings Translation strings
 * @param onChange
 * @param groupId
 * @returns {*}
 * @constructor
 */
export const AppliesToFields = ({formFields, fieldsUsed, appliedFields, notAllowedFields,strings, onChange, groupId}) => {
    /**
     * Check if we should disable this option
     *
     * @since 1.8.10
     *
     * @param fieldId
     * @returns {*}
     */
    function isFieldDisabled(fieldId) {
        return  notAllowedFields.includes(fieldId);
    }
    /**
     * Check if field is used and should be checked.
     *
     * @since 1.8.10
     *
     * @param fieldId
     * @returns {*}
     */
    function isFieldChecked(fieldId) {
        return fieldsUsed.includes(fieldId);
    }

    return (
        <div style={{float: 'left', width: '288px', paddingLeft: '12px'}}>
            <h4 style={{borderBottom: `1px solid rgb(191, 191, 191)`, margin: `0px 0px 6px; padding: 0px 0px 6px`}}>
                {strings['applied-fields']}
            </h4>
            <p className="description">{strings['select-apply-fields']}</p>
            {formFields.map(field => (
                    <label style={{display: 'block', marginLeft: `20px`}}>
                        {field.label}
                        <input
                            style={{marginLeft: `-20px`}}
                            type="checkbox"
                            disabled={isFieldDisabled(field.ID)}
                            onClick={(e) => {
                                e.preventDefault();
                                //Is it already applied?
                                if (isFieldChecked(field.ID)) {
                                    //remove from applied fields
                                    onChange(appliedFields.filter(f => f.ID === field.ID))
                                } else {
                                    //Add to applied fields.
                                    onChange([...appliedFields, field.ID])
                                }
                            }}
                            value={groupId}
                            checked={isFieldChecked(field.ID)}
                        />
                    </label>
                )
            )}
        </div>
    );
};

const isFieldTypeWithOptions = (fieldType) => ['dropdown','checkbox', 'radio','filtered_select2','toggle_switch'];


/**
 * One line in a group
 *
 * @since 1.8.10
 *
 * @param line The single line of conditional rule.
 * @param {} strings Translation field
 * @param boolean isFirst
 * @param formFields The fields in the form. Array.
 * @returns {*}
 * @constructor
 */
export const ConditionalLine = ({line, strings, isFirst, formFields}) => {
    const {value,field,compare} = line;
    const fieldConfig = React.useMemo(() => {
        return formFields.find( f => f.ID === field )
    },[field]);

    return (
        <div key={line.id} className={`caldera-condition-line condition-line-${line.id}`}>
            <span style={{display: "inline-block"}}>
                {isFirst ? <React.Fragment>{strings['if']}</React.Fragment> : <React.Fragment>{strings['and']}</React.Fragment>}
            </span>
            <select
                style={{maxWidth: "120px", verticalAlign: 'inherit'}}
                className="condition-line-field"
                value={field}
            >
                <option/>
                <optgroup label={strings['fields']}>
                    {formFields.map(field => (
                        <option value={field.id}>
                            {field.label} [{field.slug}]
                        </option>
                    ))}
                </optgroup>
            </select>
            <select
                className="condition-line-compare"
                value={compare}
                style={{maxWidth: "120px", verticalAlign: 'inherit'}}
            >
                {['is', 'isnot', 'greater', 'smaller', 'startswith', 'endswith', 'contains'].map(compareType => (
                    <option key={compareType} value={compareType}>{compareType}</option>
                ))}
            </select>
            {isFieldTypeWithOptions(fieldConfig.type ) ? (
                <select
                    value={value}
                    style={{maxWidth: "165px", verticalAlign: 'inherit'}}>
                    <option/>
                    {formFields.map(option => (
                        <option
                            key={option.value}
                            value={option.value}>
                            {option.label}
                        </option>)
                    )}

                </select>
            ) : (
                <input
                    value={value}
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
const ConditionalLines = ({lines, strings,formFields}) => {
    let isFirst = true;
    return (
        <div className="caldera-condition-group caldera-condition-lines">
            {lines.map(line => {
                const Line = (
                    <ConditionalLine
                        line={line}
                        isFirst={isFirst}
                        strings={strings}
                        formFields={formFields}
                    />
                );
                isFirst = false;
                return <Line />
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
 */
const Conditional = ({conditional,formFields, strings,id,onAddConditional,onRemoveConditional,onUpdateConditional, fieldsNotAllowed,fieldsUsed}) => {
    const {name,type,config} = conditional;
    const group = config && config.hasOwnProperty('group' ) ? config.group : {};
    const onAddLine = () => {
    };
    return (
        <div className="caldera-editor-condition-config caldera-forms-condition-edit"
             style={{marginTop: '-27px', width: "auto"}}
        >
            {! name ? (
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
                                value={name}
                                required
                                className="required block-input condition-group-name"
                            />
                        </div>
                    </div>

                    <div className="caldera-config-group">
                        <label htmlFor={`condition-group-type-${id}`}>
                            {strings.type}
                        </label>
                        <div
                            value={type}
                            className="caldera-config-field">
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
                        onClick={e => {
                            e.preventDefault();
                            onRemoveConditional(id)
                        }}
                    >
                        {strings['remove-condition']}
                    </button>
                    <AppliesToFields fields={fields} fieldsUsed={fieldsUsed}/>
                </div>
            )}
        </div>
    )
};


export default  Conditional;