import cfEditorState from '@calderajs/cf-editor-state';

/**
 * Prepare system tags objects for use
 *
 * @since 1.8.10
 *
 * @param tags
 * @returns {[]}
 */
function prepareSystemTags(tags) {
    const initialValues = [];
     Object.keys(tags).forEach(tagType => {
        const tagsOfType = tags[tagType];

        if( tagsOfType.length ){
            tagsOfType.forEach(tag => {
                initialValues.push({type: tagType, tag})
            });
        }

    });
    return initialValues;
}

/**
 * Utility function for adding magic types from default system_tags array.
 *
 * @since 1.8.10
 *
 * @param system_values
 * @param state
 * @returns {[]}
 */
function addInitialMagicTypes(system_values,state) {
    const other = [];
    Object.keys(system_values).forEach(magicType => {
        if(undefined !== magicType && ! ['field', 'system'].includes(magicType)){
            other.push( system_values[magicType] );
        }
    });
    if( other && other.length ){
        other.forEach(o => state.addMagicType(
            o.type, true, prepareSystemTags(o.tags)
        ));
    }
    return other;

}

/**
 * Factory for editor state management
 *
 * @since 1.8.10
 *
 * @param system_values
 * @param current_form_fields
 * @returns {{prepareSystemTags: (function(): []), createState: (function(): {getAllMagicTags: () => magicTags; getMagicTagsByType: (type: string) => magicTags; addMagicType: (typeName: string, brackets: boolean, intialTags?: (typeAndTags | undefined)) => void; removeMagicType: (typeName: string) => boolean; getAllSystemValues(): systemValues; feildEvents: EventEmitter<{addField: (fieldId: string) => void; updateField: (args: {fieldId: string; beforeUpdate: field}) => void; removeField: (field: field) => void}>; addField(field: field): boolean; getField(fieldId: string): (field | undefined); updateField(field: field): boolean; getAllFields(): fields; removeField(fieldId: string): boolean; addConditional(conditional: conditional): boolean; removeConditional(conditionalId: string): boolean; updateConditional(conditional: conditional): boolean; getConditional(conditionalId: string): (conditional | undefined); getAllConditionals(): conditionals})}}
 */
export default function (system_values,current_form_fields) {
    const systemTags = system_values.system.tags ? system_values.system.tags : [];

    const api =  {
        createState: () => {
            const state = cfEditorState({
                initialSystemValues: prepareSystemTags(systemTags)
            });
            addInitialMagicTypes(system_values,state);
            return state;
        },
        prepareSystemTags: () => prepareSystemTags(systemTags)
    };

    return api;
}