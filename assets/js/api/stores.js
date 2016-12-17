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