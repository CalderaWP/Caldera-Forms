import React, {Fragment} from 'react';
import Conditional from './components/Conditional';
import {NewConditionalGroup} from "./components/NewConditionalGroup";
import cfEditorState from "@calderajs/cf-editor-state";

/**
 * Caldera Forms Conditional Logic Editor
 *
 * @since 1.8.10
 *
 * @param formFields
 * @param conditionals
 * @param strings
 * @param updateConditional
 * @param onNewConditional
 * @param removeConditional
 * @returns {*}
 * @constructor
 */
const ConditionalEditor = ({formFields, conditionals, strings, updateConditional, onNewConditional, removeConditional}) => {
    //The id (or null) of currently active conditional
    const [activeConditionalId, setActiveConditionalId] = React.useState(null);
    const [activeConditional, setActiveConditional] = React.useState(null);

    /**
     * Find conditional by Id
     *
     * @since 1.8.10
     *
     * @param conditionalId
     * @returns {undefined}
     */
    const findConditionalById = (conditionalId) => conditionals.length ? conditionals.find(conditional => conditionalId === conditional.id) : undefined;



    const onUpdateConditional = (conditional) => {
        updateConditional(conditional);
        setActiveConditional(conditional);
    };

    return (
        <div>
            <div className="caldera-editor-conditions-panel" style={{marginBottom: "32px"}}>
                {conditionals.length &&
                <ul className="active-conditions-list">
                    {conditionals.map(condition => {
                            const active = activeConditionalId === condition.id;
                            return (
                                <li
                                    key={condition.id}
                                    className={`caldera-condition-nav ${active ? 'active' : ''} caldera-forms-condition-group condition-point-${condition.id}`}
                                >
                                    <a
                                        id={`condition-open-group-${condition.id}`}
                                        className="condition-open-group"
                                        onClick={(e) => {
                                            e.preventDefault();
                                            setActiveConditionalId(condition.id);
                                            setActiveConditional(findConditionalById(condition.id));
                                        }}
                                        style={{cursor: "pointer"}}
                                    >
                                <span id={`condition-group-${condition.id}`}>
                                    {condition.config.name}
                                </span>
                                        <span className="condition-line-number"/>
                                    </a>
                                </li>
                            )
                        }
                    )}
                </ul>
                }
                <NewConditionalGroup
                    strings={strings}
                    onNewConditional={(name, id) => {
                        onNewConditional(id, name);
                        setActiveConditionalId(id)
                    }}
                />
            </div>
            {activeConditional &&
            <Conditional
                conditional={activeConditional}
                formFields={formFields}
                strings={strings}
                onRemoveConditional={removeConditional}
                onUpdateConditional={onUpdateConditional}
                fieldsNotAllowed={[]} fieldsUsed={[]}
            />
            }

        </div>
    );
};

/**
 * Complete conditional logic editor with list and state management
 *
 * @since 1.8.10
 *
 * @param state
 * @param strings
 * @returns {*}
 * @constructor
 */
export const ConditionalEditorApp = ({state, strings}) => {
    const [fields, setFields] = React.useState(state.getAllFields());
    const [conditionals, setConditionals] = React.useState(state.getAllConditionals());
    const findConditionalIndexId = (conditionalId) => conditionals.length ? conditionals.findIndex(conditional => conditionalId === conditional.id) : undefined;
    /**
     * Callback for updating conditional in list
     *
     * @since 1.8.10
     *
     * @param conditional
     */
    const updateConditional = (conditional) => {
        const index = findConditionalIndexId(conditional.id);
        setConditionals([
            ...conditionals.slice(0, index),
            ...[conditional],
            ...conditionals.slice(index + 1),
        ]);
    };

    /**
     * Callback for removing conditionals from list
     *
     * @since 1.8.10
     *
     * @param conditionalId
     */
    const removeConditional = (conditionalId) => {
        const index = findConditionalIndexId(conditionalId);

        if (index === conditionals.length) {
            setConditionals([
                ...conditionals.slice(0, index),
            ]);
        } else {
            setConditionals([
                ...conditionals.slice(0, index),
                ...conditionals.slice(index + 1),
            ]);
        }


    };

    const onNewConditional = (id, name) => {
        setConditionals([...conditionals, {id, type: 'show', config: {name}}]);
    };

    return (<ConditionalEditor
        strings={strings} onNewConditional={onNewConditional}
        conditionals={conditionals}
        formFields={fields}
        updateConditional={updateConditional}
        removeConditional={removeConditional}

    />);

};


/**
 *
 * @param {cfEditorState} initialState
 * @param strings
 * @returns {*}
 */
export default ConditionalEditor;