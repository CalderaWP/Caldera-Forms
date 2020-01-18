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

    const [activeConditional, setActiveConditional] = React.useState(null);

    const findConditionalById = (conditionalId) => conditionals.length ? conditionals.find(conditional => conditionalId === conditional.id) : undefined;

    const onActivateConditional = (conditionalId) => {
        const active = findConditionalById(conditionalId);
        if (active) {
            setActiveConditional(active);
        }
    };
    const onNewConditional = () => {

    };


    return (
        <div>
            <div className="caldera-editor-conditions-panel" style={{marginBottom: "32px"}}>
                <ul className="active-conditions-list">
                    {conditionals.map(condition => (
                            <li className={`caldera-condition-nav`} key={condition.id}>
                                  <span id={`condition-group-${condition.id}`}>
                                        {condition.config.name}
                                    </span>
                            </li>
                        )
                    )}
                </ul>
            </div>


            <div id={`temporary-right`}>
                <ul>
                    {conditionals.map(conditional => {
                        const {id, config} = conditional;
                        const {name} = config;
                        return (
                            <div key={id}>
                                <input id={`condition-group-name-${id}`} value={name}
                                       onChange={(e) => {
                                           return updateConditional({
                                                   ...findConditionalById(id),
                                                   config: {
                                                       ...findConditionalById(id).config,
                                                       name: e.target.value
                                                   }
                                               }
                                           );
                                       }}
                                />
                            </div>
                        )
                    })}
                </ul>
            </div>
        </div>
    );
}