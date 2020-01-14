/** globals system_values,current_form_fields **/
import conditionalEditor from './conditional-editor';
import stateFactory from "./stateFactory";
/**
 * Form builder
 *
 * Currently responsible for: conditional logic editor
 *
 * @since 1.8.10
 *
 */
document.addEventListener("DOMContentLoaded", function() {
   if( 'object' == typeof system_values && 'object' == typeof current_form_fields ){
      const factory = stateFactory(system_values,current_form_fields);
      const state = factory.createState();
      conditionalEditor(state,jQuery,window.document);
   }
});