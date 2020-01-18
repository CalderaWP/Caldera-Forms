import React, {Fragment} from 'react';

/**
 * The section to set which fields it applies to
 *
 * @since 1.8.10
 *
 * @param Array formFields The form's fields
 * @param Array fieldsUsed The fields this conditional is applied to.
 * @param Array notAllowedFields The fields this conditional can NOT applied to.
 * @param strings Translation strings
 * @param onChange
 * @param groupId
 * @returns {*}
 * @constructor
 */
export const AppliesToFields = ({formFields, fieldsUsed, notAllowedFields, strings, onChange, groupId}) => {
    /**
     * Check if we should disable this option
     *
     * @since 1.8.10
     *
     * @param fieldId
     * @returns {*}
     */
    function isFieldDisabled(fieldId) {
        if (undefined === notAllowedFields) {
            return false;
        }
        return notAllowedFields.includes(fieldId);
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
                                    onChange(fieldsUsed.filter(f => f.ID === field.ID))
                                } else {
                                    //Add to applied fields.
                                    onChange([...fieldsUsed, field.ID])
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

const isFieldTypeWithOptions = (fieldType) => ['dropdown', 'checkbox', 'radio', 'filtered_select2', 'toggle_switch'];


/**
 * One line in a group
 *
 * @since 1.8.10
 *
 * @param line The single line of conditional rule.
 * @param {} strings Translation field
 * @param boolean isFirst
 * @param formFields
 * @param onRemoveLine
 * @param onUpdateLine
 * @returns {*}
 * @constructor
 */
export const ConditionalLine = ({line, strings, isFirst, formFields, onRemoveLine, onUpdateLine}) => {
    const {value, field, compare} = line;
    const lineId = line.id;
    const fieldConfig = React.useMemo(() => {
        const config = formFields.find(f => f.ID === field);
        if (config) {
            return config;
        }
        return {
            type: 'text'
        }
    }, [field]);

    /**
     * Callback for changing which field this line is based on
     *
     * @param field
     */
    const onChangeField = (field) => {
        onUpdateLine({
            ...line,
            field,
        })
    };

    /**
     * Callback for changing a line's value
     *
     * @since 1.8.10
     *
     * @param value
     */
    const onChangeValue = (value) => {
        onUpdateLine({
            ...line,
            value
        })
    };

    /**
     * Callback for changing a line's comparison type
     *
     * @since 1.8.10
     *
     * @param compare
     */
    const onChangeCompare = (compare) => {
        onUpdateLine({
            ...line,
            compare
        })
    };


    return (
        <div key={lineId} className={`caldera-condition-line condition-line-${lineId}`}>
            <span style={{display: "inline-block"}}>
                {isFirst ? <React.Fragment>{strings['if']}</React.Fragment> :
                    <React.Fragment>{strings['and']}</React.Fragment>}
            </span>
            <select
                style={{maxWidth: "120px", verticalAlign: 'inherit'}}
                className="condition-line-field"
                value={field}
                onChange={(e) => onChangeField(e.target.value, lineId)}
            >
                <option/>
                <optgroup label={strings['fields']}>
                    {formFields.map(field => (
                        <option value={field.ID}>
                            {field.label} [{field.slug}]
                        </option>
                    ))}
                </optgroup>
            </select>
            <select
                className="condition-line-compare"
                value={compare}
                onChange={(e) => onChangeCompare(e.target.value)}
                style={{maxWidth: "120px", verticalAlign: 'inherit'}}
            >
                {['is', 'isnot', 'greater', 'smaller', 'startswith', 'endswith', 'contains'].map(compareType => (
                    <option key={compareType} value={compareType}>{compareType}</option>
                ))}
            </select>
            {isFieldTypeWithOptions(fieldConfig.type) ? (
                <select
                    value={value}
                    onChange={(e) => onChangeValue(e.target.value)}
                    style={{maxWidth: "165px", verticalAlign: 'inherit'}}>
                    <option/>
                    {formFields.map(option => {
                        //should be fieldConfig.option, except that doesn't exist.
                        return (
                            <option
                                key={option.value}
                                value={option.value}
                            >
                                {option.label}
                            </option>
                        )
                    })}
                </select>
            ) : (
                <input
                    onChange={onChangeValue}
                    value={value}
                    type="text"
                    className="magic-tag-enabled block-input"
                />
            )}
            <button
                onClick={(e) => {
                    e.preventDefault();
                    onRemoveLine(lineId,line.parent);
                }}
                className="button pull-right condition-line-remove" type="button"
            >
                <i className="icon-join"/>
            </button>
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
 * @param formFields
 * @param onRemoveLine
 * @param onUpdateLine
 * @returns {*}
 * @constructor
 */
export const ConditionalLines = ({lines, strings, formFields, onRemoveLine, onUpdateLine, onAddLine, groupId}) => {
    return (
        <div className="caldera-condition-group caldera-condition-lines">
            {lines.map((line, index) => {
                return (
                    <ConditionalLine
                        key={line.id}
                        line={line}
                        isFirst={0 === index}
                        onUpdateLine={(update) => onUpdateLine(update, line.id)}
                        {...{
                            strings,
                            formFields,
                            onRemoveLine,
                        }}
                    />
                )
            })}

            <div style={{margin: "12px 0 0"}}>
                <button
                    className="button button-small condition-group-add-line"
                    type="button"
                    onClick={(e) => {
                        e.preventDefault();
                        onAddLine(groupId);
                    }}
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
const Conditional = ({conditional, formFields, strings, onRemoveConditional, onUpdateConditional, fieldsNotAllowed, fieldsUsed}) => {
    const {type, config, id} = conditional;
    const group = config && config.hasOwnProperty('group') ? config.group : {};
    const name = conditional.hasOwnProperty('config') && conditional.config.hasOwnProperty('name') ? conditional.config.name : '';
    const newLineId = ( ) => `cl_${Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 12)}`;
    const onAddGroup = () => {
        const groupId = `rw${Math.random().toString(36).replace(/[^a-z]+/g, '').substr(0, 12)}`;
        onUpdateConditional({
            id,
            type,
            config: {
                ...conditional.config,
                group: {
                    ...conditional.config.group,
                    [groupId]: {
                        [newLineId()]: {
                            field: '',
                            value: '',
                            type: 'is',
                            parent: groupId,
                        }
                    }

                }
            }
        });
    }
    const onAddLine = (groupId) => {
        const lineId = newLineId();
        onUpdateConditional({
            id,
            type,
            config: {
                ...conditional.config,
                group: {
                    ...conditional.config.group,
                    [groupId]: {
                        ...conditional.config.group[groupId],
                        [lineId]: {
                            field: '',
                            value: '',
                            type: '',
                            parent: groupId,
                        }
                    }

                }
            }
        });
    };

    /**
     * Callback for updating a line in a conditional group
     *
     * @since 1.8.10
     *
     * @param line
     * @param lineId
     */
    const onUpdateLine = (line, lineId, groupId) => {
        let group = conditional.config.group;
        onUpdateConditional({
            ...conditional,
            config: {
                ...conditional.config,
                group: {
                    ...group,
                    [groupId]: {
                        ...group[groupId],
                        [lineId]: line
                    }
                }
            }
        });
    };

    /**
     *  Callback for removing a line from a line group
     *
     *  @since 1.8.10
     *
     * @param lineId
     * @param groupId
     */
    const onRemoveLine = (lineId, groupId) => {
        let groups = group;

        //Delete line from line group
        delete groups[groupId][lineId];

        //no lines left?
        if (!Object.values(groups[groupId]).length) {
            //delete line group
            delete groups[groupId];
        }
        onUpdateConditional({
            ...conditional,
            config: {
                ...conditional.config,
                group: groups
            }
        });
    };


    /**
     * Callback for updating name of conditional group
     *
     * @since 1.8.10
     *
     * @param name
     */
    const onUpdateName = (name) => {
        onUpdateConditional({
            ...conditional,
            config: {
                ...conditional.config,
                name,
            }
        })
    };

    /**
     * Callback for changing type of conditional
     *
     * @since 1.8.10
     *
     * @param type
     * @returns {*}
     */
    const onChangeType = (type) => {
        return onUpdateConditional({
            ...conditional,
            type
        })
    };


    return (
        <div className="caldera-editor-condition-config caldera-forms-condition-edit"
             style={{marginTop: '-27px', width: "auto"}}
        >

            <div
                className={`condition-point-${id}`}
                style={{float: 'left', width: "550px"}}
            >
                <div
                    className="caldera-config-group"
                >
                    <label htmlFor={`condition-group-name-${id}`}>
                        {strings.name}
                    </label>
                    <div
                        className="caldera-config-field"
                    >
                        <input
                            type="text"
                            name={`conditions[${id}][name]`}
                            id={`condition-group-name-${id}`}
                            value={name}
                            onChange={(e) => onUpdateName(e.target.value)}
                            required
                            className="required block-input condition-group-name"
                        />
                    </div>
                </div>

                <div className="caldera-config-group">
                    <label
                        htmlFor={`condition-group-type-${id}`}
                    >
                        {strings.type}
                    </label>
                    <div

                        className="caldera-config-field"
                    >
                        <select
                            value={type}
                            onChange={(e) => onChangeType(e.target.value)}
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
                            onClick={(e) => {
                                e.preventDefault();
                                onAddGroup();
                            }}
                        >
                            {strings['add-conditional-group']}
                        </button>
                        }
                    </div>
                </div>
                {Object.keys(group).map(groupId => {
                    const lineGroups = group[groupId];
                    const lines = Object.keys(lineGroups).map(
                        (lineGroupId) => {
                            return {
                                ...lineGroups[lineGroupId],
                                id: lineGroupId,
                            }
                        }
                    );
                    return (
                        <Fragment>
                            <ConditionalLines
                                lines={lines}
                                strings={strings}
                                formFields={formFields}
                                onUpdateLine={(update, lineId) => onUpdateLine(update, lineId, groupId)}
                                onRemoveLine={(lineId) => onRemoveLine(lineId, groupId)}
                                onAddLine={() => onAddLine(groupId)}
                                groupId={groupId}
                            />
                        </Fragment>
                    )
                })

                }
                <button
                    style={{margin: "12px 0 12px"}}
                    type="button"
                    className="block-input button condition-remove"
                    data-confirm={strings['confirm-remove']}
                    onClick={e => {
                        e.preventDefault();
                        onRemoveConditional(id)
                    }}
                >
                    {strings['remove-condition']}
                </button>
            </div>

        </div>
    )
};


export default Conditional;