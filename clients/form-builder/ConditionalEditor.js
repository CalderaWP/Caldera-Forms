import React from 'react';
import {ConditionalsList, ConditionalListItem} from './components/Conditionals';
import cfEditorState from "@calderajs/cf-editor-state";


/**
 *
 * @param {cfEditorState} initialState
 * @param strings
 * @returns {*}
 */
export default function ({fields, conditionals, strings, updateConditional}) {
    const [activeConditionalId, setActiveConditionalId] = React.useState(null);


    const findConditionalById = (conditionalId) => conditionals.length ? conditionals.find(conditional => conditionalId === conditional.id) : undefined;
    const activeConditional = React.useMemo(() => {
        return findConditionalById(activeConditionalId);
    }, [activeConditionalId])
    const onActivateConditional = (conditionalId) => {

            setActiveConditionalId(conditionalId);

    };

    const onNewConditional = () => {

    };


    return (
        <div>
            <div className="caldera-editor-conditions-panel" style={{marginBottom: "32px"}}>
                <ul className="active-conditions-list">
                    {conditionals.map(condition => {
                            const active = activeConditional && activeConditionalId === condition.id;
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
            </div>


            <div id={`temporary-right`}>
                {activeConditional &&
                <input id={`condition-group-name-${activeConditional.id}`} value={activeConditional.config.name}
                       onChange={(e) => {
                           return updateConditional({
                                   ...activeConditional,
                                   config: {
                                       ...activeConditional.config,
                                       name: e.target.value
                                   }
                               }
                           );
                       }}
                />
                }
            </div>
        </div>
    );
}