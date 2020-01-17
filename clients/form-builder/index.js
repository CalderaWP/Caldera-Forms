/** globals system_values,current_form_fields,CF_ADMIN **/
import React from 'react';
import stateFactory, {setConditionalsFromCfConfig} from "./stateFactory";
import Conditionals from "./components/Conditionals";
import {render} from '@wordpress/element';
import cfEditorState from "@calderajs/cf-editor-state";
/**
 * Form builder
 *
 * Currently responsible for: conditional logic editor
 *
 * @since 1.8.10
 *
 */
document.addEventListener("DOMContentLoaded", function () {
    if ('object' == typeof system_values && 'object' == typeof current_form_fields) {
        const factory = stateFactory(system_values, current_form_fields);
        const strings = CF_ADMIN.strings.conditionals;
        const App = ({factory, strings}) => {

            // Tracks state management option in away we can force updates.
            const [state,updateState] = React.useState(factory.createState());

            //Maintains list of conditionals in it's own array
            const conditionals = React.useMemo( () => {
                console.log(26);
                return state.getAllConditionals();


            },[state]);

            //Maintains list of form fields in it's own array
            const [formFields,setFormFields] = React.useState([]);

            /**
             * Set the conditionals form was loaded with
             *
             * @since 1.8.10
             */
            React.useEffect(() => {
                const conditions = CF_ADMIN.hasOwnProperty('conditions') ? CF_ADMIN.conditions : [];
                setConditionalsFromCfConfig(conditions, state);
            }, [CF_ADMIN.conditions]);

            React.useEffect( () => {
                console.log(42);
                setFormFields(state.getAllFields() );
            }, [state,setFormFields])
            /**
             * Callback for adding conditional
             *
             * @since 1.8.10
             *
             * @param conditional New Conditional to add.
             */
            const addConditional = (conditional) => {
                state.addConditional(conditional);
                updateState(state);
            };

            /**
             * Callback for updating conditional
             *
             * @since 1.8.10
             *
             * @param conditional New Conditional to add.
             */
            const updateConditional = (conditional) => {
                state.updateConditional(conditional);
                updateState(state);
            };

            const getConditional = (conditionalId ) => {
                return state.getConditional(conditionalId)
            };

            /**
             * Callback for removing conditional
             *
             * @since 1.8.10
             *
             * @param conditionalId Id of conditional to remove
             */
            const removeConditional =  (conditionalId) => {
                state.removeConditional(conditionalId);
                updateState(state);
            };

            return (
                <Conditionals
                    conditionals={conditionals}
                    state={state}
                    strings={strings}
                    formFields={formFields}
                    addConditional={addConditional}
                    removeConditional={removeConditional}
                    updateConditional={updateConditional}
                    getConditional={getConditional}
                />
            )
        };

        render(
            <App factory={factory} strings={strings}/>, document.getElementById('caldera-forms-conditions-panel')
        )

    }
});