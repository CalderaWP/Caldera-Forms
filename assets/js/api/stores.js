/**
 * A factory for a form state containers
 *
 * Can be used constructed with CFAPI() or other data. Designed to provide data to our VueJS entry viewer, but is framework agnostic.
 *
 * @since 1.50
 *
 * @param formId The ID of the form
 * @param allFields All fields of this form
 * @param listFields The fields of this form with "Show In Entry List" checked
 *
 * @returns {{state: {formId: *, allFields: *, listFields: *}, setFormId: setFormId, setAllFields: setAllFields, setListFields: setListFields, getAllFields: getAllFields}}
 *
 * @constructor
 */
function CFFormStoreFactory( formId, allFields, listFields ){
    return {
        state: {
            formId: formId,
            allFields: allFields,
            listFields: listFields
        },
        setFormId: function(newValue) {
            this.state.formId = newValue
        },
        setAllFields: function(newValue) {
            this.state.allFields = newValue
        },
        setListFields: function(newValue) {
            this.state.listFields = newValue
        },
        getAllFields: function () {
            return this.state.allFields;
        }
    };
}

/**
 * A factory for creating a state container for a paginated collection of entries
 *
 * Can be used constructed with CFAPI() or other data. Designed to provide data to our VueJS entry viewer, but is framework agnostic.
 *
 * @since 1.50
 *
 * @param formId The ID of form entries are from
 * @param entries The entry collection
 *
 * @returns {{state: {formId: *, entries: *, total: number, totalPages: number}, setEntries: setEntries, setTotal: setTotal, setTotalPages: setTotalPages, getTotalPages: getTotalPages, getEntry: getEntry, getFieldFromEntry: getFieldFromEntry}}
 *
 * @constructor
 */
function CFEntriesStoreFactory( formId, entries ){
    return {
        state: {
            formId: formId,
            entries: entries,
            total: 0,
            totalPages: 0,
            page: 0
        },
        setEntries: function (entries) {
            this.state.entries = entries;
        },
        setTotal: function( total ){
            this.state.total = total;
        },
        getTotal: function(){
            return this.state.total;
        },
        setTotalPages: function( totalPages ){
            this.state.totalPages = totalPages;
        },
        getTotalPages: function(){
            return this.state.totalPages;
        },
        setPage: function( page ){
            this.state.page = page;
        },
        getPage: function(){
            return this.state.page;
        },
        getEntry :function( id ){
            if( 'object' == typeof this.state.entries[id] ){
                return this.state.entries[id];
            }
            return false;
        },
        getFieldFromEntry: function( entry, fieldId ){
            if( 'object' == typeof entry.fields[fieldId ]) {
                return entry.fields[fieldId];
            }
            return false;
        }
    }
}

/**
 * A factory for creating a form store for use in form  editor
 *
 * @since 1.5.1
 *
 * @param form
 * @returns {{getFields: getFields, getField: getField, getFieldType: getFieldType, addField: addField, updateField: updateField, getConditionals: getConditionals, getConditional: getConditional, getProcessors: getProcessors, getProcessor: getProcessor}}
 * @constructor
 */
function CFFormEditStore( form ) {
    return Object.assign( Object.create( CFObj.prototype ), {
        /**
         * Get all fields of form
         *
         * @returns {*}
         */
        getFields : function(){
            return form.fields;
        },
        /**
         * Get a field of a form
         *
         *  @since 1.5.1
         *
         * @param id
         * @returns {*}
         */
        getField : function ( id ) {
            if( this.has( form.fields, id ) ){
                return form.fields[id];
            }
            return {}
        },
        /**
         * Get field type by field ID
         *
         *  @since 1.5.1
         *
         * @param id
         * @returns {*}
         */
        getFieldType: function ( id ) {
            var field = this.getField(id);
            if( ! this.emptyObject( field ) ){
                return field.type;
            }
            return false;
        },
        /**
         * Add a field to collection
         *
         *  @since 1.5.1
         *
         * @param fieldId
         * @param fieldType
         * @returns {*|{}}
         */
        addField : function (fieldId,fieldType) {
            form.fields[fieldId] = fieldFactory(fieldId,fieldType);
            return this.getField(fieldId);
        },
        /**
         * Update field in collection
         *
         *  @since 1.5.1
         *
         * @param id
         * @param key
         * @param data
         * @returns {*}
         */
        updateField: function (id, key, data ) {
            var field = this.getField(id);
            if( ! this.emptyObject(field) ){
                if( this.fieldKeys.indexOf( key ) ){
                    form.fields[id][key] = data;
                    return this.getField(id);
                }

            }
            return false;
        },
        /**
         * Get conditional groups of form
         *
         * @since 1.5.1
         *
         * @returns {*}
         */
        getConditionals : function () {
            return form.conditional_groups.conditions;
        },
        /**
         * Get a conditional group by ID
         *
         *  @since 1.5.1
         *
         * @param id
         * @returns {*}
         */
        getConditional : function ( id ) {
            if( this.has( form.conditional_groups.conditions, id ) ){
                return form.conditional_groups.conditions[id];
            }
            return {}
        },
        /**
         * Get processors of form
         *
         * @since 1.5.1
         *
         * @returns {*}
         */
        getProcessors : function() {
            return form.processors;
        },
        /**
         * Get a form processor
         *
         * @since 1.5.1
         *
         * @param id
         * @returns {*}
         */
        getProcessor: function ( id ) {
            if( this.has( form.processors, id )){
                return form.processors[id];
            }
            return {}
        }

    });

}

