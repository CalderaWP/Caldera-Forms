import cfEditorState from '@calderajs/cf-editor-state';

/**
 * Prepare tags objects, as represented in system_values global
 *
 * @since 1.8.10
 *
 * @param tags
 * @returns {[]}
 */
function prepareTags(tags) {
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
 * Prepare field magic tags, from object supplied in global current_form_fields
 *
 * @since 1.8.10
 *
 * @param currentFormFields
 * @returns {Array}
 */
function prepareFields(currentFormFields){
    return currentFormFields && Object.keys(currentFormFields).length ? Object.keys(currentFormFields).map(
        id => {
            const field = currentFormFields[id];
            return {...field,ID:id,tag: '%' + field.slug + '%'}
        }
    ) : [];
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
            o.type, true, prepareTags(o.tags)
        ));
    }
    return other;

}



/**
 * Get all fields used by all conditional groups
 *
 * @since 1.8.10
 *
 * @param {cfEditorState }state
 * @returns {*}
 */
export const getAllFieldsUsed = (state) => {
    const groups =  state.getAllConditionals();
    if( ! groups.length ){
        return [];
    }

    let fields = [];
     groups.forEach( ( conditional) => {
        if( conditional.hasOwnProperty('config') && conditional.config.hasOwnProperty('fields') ){
            const _fields =  Object.values(conditional.config.fields);
            if( _fields.length ){
                _fields.map( _f => fields.push(_f));
            }
        }

    });
     return  fields;
};

/**
 * Get the fields this conditional group applies to.
 *
 * @since 1.8.10
 *
 * @param conditionalId
 * @param {cfEditorState }state
 */
export const getFieldsUsedByConditional = (conditionalId,state) => {
    const conditional = state.getConditional(conditionalId);
    if( ! conditional ){
        return [];
    }

    if( ! conditional.hasOwnProperty('config') || !  conditional.config.hasOwnProperty('fields') ) {
        return [];
    }
    return  Object.values(conditional.config.fields);
};

/**
 * Get the fields that can not be used by a conditional group.
 *
 * @since 1.8.10
 *
 * @param conditionalId
 * @param {cfEditorState }state
 */
export const getFieldsNotAllowedForConditional = (conditionalId,state) => {
    const allFieldsUsed = getAllFieldsUsed(state);
    //No fields used by ANY conditional? If so, all fields can be used.
    if( ! allFieldsUsed.length ){
        return [];
    }
    const fieldsUsedByConditional = getFieldsUsedByConditional(conditionalId,state);
    //No fields used by THIS conditional? If so, return allFieldsUsed un filtered
    if( ! fieldsUsedByConditional.length ){
       // return allFieldsUsed;
    }
    //Filter out fields used by OTHER conditional groups
    return allFieldsUsed.filter( fieldId => !fieldsUsedByConditional.includes(fieldId) );

};


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
    const systemTags = system_values && system_values.system.tags ? system_values.system.tags : [];
    const initialFields = prepareFields(current_form_fields);
    const api =  {
        prepareFields,
        createState: () => {
            const state = cfEditorState({
                initialSystemValues: prepareTags(systemTags),
                initialFields
            });
            addInitialMagicTypes(system_values,state);
            return  state;

        },
        prepareSystemTags: () => prepareTags(systemTags),
        /**
         * Convert conditional group from CF form config to the correct format for this system
         *
         * @since 1.8.10
         *
         * @param conditionalGroup
         * @returns {{id: *, type: (*|string), config: {fields: (*|[]), group: *}}}
         */
        conditionalFromCfConfig: (conditionalGroup) =>{
            return  {
                id: conditionalGroup.id,
                type: conditionalGroup.type ? conditionalGroup.type : 'show',
                config: {
                    fields: conditionalGroup.hasOwnProperty('fields' ) ? Object.values(conditionalGroup.fields) : [],
                    group: conditionalGroup.hasOwnProperty('group') ? conditionalGroup.group : {},
                }
            };
        }
    };

    return api;
}