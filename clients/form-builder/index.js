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


        function App() {
          return <div>1</div>
        }

        render(
            <App state={state} strings={strings}/>, document.getElementById('caldera-forms-conditions-panel')
        )

    }
});