/** globals system_values,current_form_fields,CF_ADMIN **/
import conditionalEditor from './conditional-editor';
import stateFactory from "./stateFactory";
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
        const state = factory.createState();
        const conditions = CF_ADMIN.hasOwnProperty( 'conditions' ) ? CF_ADMIN.conditions : [];
        conditions.forEach(condition => state.addConditional(condition ) );

        const onNewConditional = (name) => {

        };

        const onEditConditional = () => {

        };

        render(<Conditionals state={state} strings={CF_ADMIN.strings.conditionals} /> )

    }
});