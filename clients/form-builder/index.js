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
            /**
             * Track state management in a ref
             *
             * @since 1.8.10
             *
             * @type {React.MutableRefObject<cfEditorState>}
             */
            const state = React.useRef(factory.createState());

            /**
             * Set the conditionals form was loaded with
             *
             * @since 1.8.10
             */
            React.useEffect(() => {
                const conditions = CF_ADMIN.hasOwnProperty('conditions') ? CF_ADMIN.conditions : [];
                setConditionalsFromCfConfig(conditions, state.current);
            }, [CF_ADMIN.conditions]);
            return (
                <Conditionals
                    state={state.current}
                    strings={strings}
                    formFields={state.current.getAllFields()}
                />
            )
        };

        render(
            <App factory={factory} strings={strings}/>, document.getElementById('caldera-forms-conditions-panel'))

    }
});