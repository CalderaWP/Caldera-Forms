/** globals system_values,current_form_fields **/
import cfEditorState from '@calderajs/cf-editor-state';

/**
 * Form builder
 *
 * Currently responsible for: conditional logic editor
 *
 * @since 1.8.10
 *
 */
document.addEventListener("DOMContentLoaded", function() {
   console.log(1,cfEditorState);

   if( 'object' === typeof  system_values && 'object' === current_form_fields ){
      console.log(system_values,current_form_fields);
   }
});