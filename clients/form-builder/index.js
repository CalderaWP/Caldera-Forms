/** globals system_values,current_form_fields,CF_ADMIN **/
import React from 'react';
import stateFactory, {setConditionalsFromCfConfig} from "./stateFactory";
import Conditionals from "./components/Conditionals";
import {render} from '@wordpress/element';
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
        const state = factory.createState();
        const App = ({state, strings}) => {

            const [conditionals,setConditionals] = React.useState(state.getAllConditionals());

            //Maintains list of form fields in it's own array
            const [formFields,setFormFields] = React.useState(state.getAllFields());

            /**
             * Set the conditionals form was loaded with
             *
             * @since 1.8.10
             */
            React.useEffect(() => {
                const conditions = CF_ADMIN.hasOwnProperty('conditions') ? CF_ADMIN.conditions : [];
                setConditionalsFromCfConfig(conditions, state);
                setConditionals(state.getAllConditionals());
                setFormFields(state.getAllFields() );
            }, [CF_ADMIN.conditions]);


            /**
             * Find array index of conditional in collection
             *
             * @since 1.8.10
             *
             * @param conditionalId
             */
            const findConditionalIndex = (
                conditionalId
            ) => {
                if (!conditionals.length) {
                    return undefined;
                }
                return conditionals.findIndex((c) => c.id === conditionalId);
            };

            /**
             * Callback for adding conditional
             *
             * @since 1.8.10
             *
             * @param conditional New Conditional to add.
             */
            const addConditional = (conditional) => {
                state.addConditional(conditional);
                setConditionals([...conditionals,conditional]);
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
                setConditionals([...state.getAllConditionals()] );

            };

            /**
             * Callback for removing conditional
             *
             * @since 1.8.10
             *
             * @param conditionalId Id of conditional to remove
             */
            const removeConditional =  (conditionalId) => {
                const index = findConditionalIndex(conditionalId);
                setConditionals([
                    ...conditionals.slice(0, index),
                    ...conditionals.slice(index+1)
                ]);

                state.removeConditional(conditionalId);
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
                />
            )
        };

        render(
            <App state={state} strings={strings}/>, document.getElementById('caldera-forms-conditions-panel')
        )

    }
});