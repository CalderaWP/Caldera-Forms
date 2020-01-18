import React from 'react';
import Conditional from './components/Conditional';
import {NewConditionalGroup} from "./components/NewConditionalGroup";
import cfEditorState from "@calderajs/cf-editor-state";


/**
 *
 * @param {cfEditorState} initialState
 * @param strings
 * @returns {*}
 */
export default function ({formFields, conditionals, strings, updateConditional, onNewConditional,removeConditional}) {
    const [activeConditionalId, setActiveConditionalId] = React.useState(null);
    const findConditionalById = (conditionalId) => conditionals.length ? conditionals.find(conditional => conditionalId === conditional.id) : undefined;
    const activeConditional = React.useMemo(() => {
        return findConditionalById(activeConditionalId);
    }, [activeConditionalId]);

    const onActivateConditional = (conditionalId) => {
        setActiveConditionalId(conditionalId);
    };




    return (
        <div>
            <div className="caldera-editor-conditions-panel" style={{marginBottom: "32px"}}>
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
                                            onActivateConditional(condition.id)
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
                <NewConditionalGroup
                    strings={strings}
                    onNewConditional={(name,id)=> {
                        onNewConditional(id,name);
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
                onUpdateConditional={updateConditional}
                fieldsNotAllowed={[]} fieldsUsed={[]}
            />
            }

        </div>
    );
}