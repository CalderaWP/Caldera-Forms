import  React from 'react';
import {ConditionalsList} from './components/Conditionals';
import cfEditorState from "@calderajs/cf-editor-state";

/**
 *
 * @param {cfEditorState} state
 * @param strings
 * @returns {*}
 */
export default function ({state,strings}) {
    const [activeConditional, setActiveConditional] = React.useState(null);

    const conditionals = React.useMemo( () => {
        return state.getAllConditionals();
    }, [state]);

    const fields = React.useMemo( () => {
        return state.getAllFields();
    }, [state]);

    const findConditionalById = (conditionalId ) => conditionals.length ? conditionals.find( conditional => conditionalId === conditional.id ) : undefined;

    const onActivateConditional = (conditionalId) => {
        const active = findConditionalById( conditionalId  );
        if( active ){
            setActiveConditional(active);
        }
    };
    const onNewConditional = () => {

    };

    const onUpdateConditional = (conditional) => {
        state.updateConditional(conditional)
    };

    return (
        <div>
            <div id={`temporary-left`}>
                <ConditionalsList
                    conditionals={conditionals}
                    onChooseItem={onActivateConditional}
                    strings={strings}
                    onNewConditional={onNewConditional}
                    active={activeConditional ? activeConditional.id : null }
                />
            </div>

            <div id={`temporary-right`}>
                <ul>
                    {conditionals.map(conditional => {
                        const {name,id} = conditional;
                        return (
                            <div key={id}>
                                <input id={`condition-group-name-${id}`} value={name}
                                       onChange={(e) => {
                                            return {
                                                ...conditionals,
                                                [id] : {
                                                    ...findConditionalById(id),
                                                    name: e.target.value
                                                }
                                            }
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