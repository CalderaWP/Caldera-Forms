

( function( $){
    if( 'undefined' != typeof CF_ENTRY_VIEWER_2_CONFIG ){
        var formID = CF_ENTRY_VIEWER_2_CONFIG.formId;
        var cfEntryviewers = {};
        cfEntryviewers[ formID ] = new CFEntryViewer2( formID, CF_ENTRY_VIEWER_2_CONFIG, $, _ );
        cfEntryviewers[ formID ].init();
    }


})( jQuery );




function CFEntryViewer2View( data, config, $, _, Vue ){

    var listView;
    var self = this;
    var singleViews = {};
    var $singleEl = $( document.getElementById( config.templates.entry ) );

    this.init = function ( templateId, displayId ) {
        Vue.component('caldera-forms-entries', {
            template: '#' + config.templates.entries,
            props: {
                data: Array,
                columns: Array,
                filterKey: String,
                single: String
            },
            data: function () {
                var sortOrders = {}
                this.columns.forEach(function (key) {
                    sortOrders[key] = 1
                });
                return {
                    sortKey: '',
                    sortOrders: sortOrders
                }
            },
            computed: {
                filteredData: function () {
                    var sortKey = this.sortKey
                    var filterKey = this.filterKey && this.filterKey.toLowerCase()
                    var order = this.sortOrders[sortKey] || 1
                    var data = this.data
                    if (filterKey) {
                        data = data.filter(function (row) {
                            return Object.keys(row).some(function (key) {
                                return String(row[key]).toLowerCase().indexOf(filterKey) > -1
                            })
                        })
                    }
                    if (sortKey) {
                        data = data.slice().sort(function (a, b) {
                            a = a[sortKey]
                            b = b[sortKey]
                            return (a === b ? 0 : a > b ? 1 : -1) * order
                        })
                    }
                    return data
                }
            },
            filters: {
                capitalize: function (str) {
                    return str.charAt(0).toUpperCase() + str.slice(1)
                }
            },
            methods: {
                sortBy: function (key) {
                    this.sortKey = key
                    this.sortOrders[key] = this.sortOrders[key] * -1
                },
                showDetails : function (entryId)  {
                    if(  $singleEl.attr( 'aria-hidden' ) ){
                        $singleEl.attr( 'aria-hidden', false ).show().css( 'visibility', 'visible' );
                    }
                    return singleViewFactory(entryId );
                }

            }
        });


        listView = new Vue({
            el: '#' + config.targets.entries,
            data: {
                searchQuery: '',
                gridColumns: data.fieldOrder.headers,
                gridData: _.toArray( data.entryCollection ),
                single:false,
                perPage: 20,
                page: data.page
            }
        });

    };

    function singleViewFactory( entryID ) {
        var entryData = {};
        if (!_.has(singleViews, entryID ) ) {
            var headers = data.fieldOrder.headers;
            _.each( data.fieldsList, function( id, i ){
                headers[_.size( headers )] = {
                    id: id,
                    label: data.fields[ id ].label
                }
                entryData[ id ] = data.entryCollection[entryID][ id ];
            });

            var _d = {
                entryId: entryID,
                headers: headers,
                data: entryData,
                fields: data.fieldsList

            };
            singleViews[ entryID ] = new Vue({
                el: '#' + config.templates.entry,
                data: _d
            })


        }

        return singleViews.entryID
    }
}

function CFEntryViewer2( formId, config, $, _ ){

    var self = this,
        page = 1,
        data = {
            fieldOrder : {
                headers: [],
                all: [],
            },
            fieldsList : {},
            entryCollection: {},
            page: 1,
            formId: formId,
            fields: {}
        },
        view;

    this.init = function(){
        $.when( getForm(), getEntries( page ) ).then( function( d1, d2){
            setupViewer( d1[0], d2[0] );
        });
    };

    this.setupView = function( templateId, displayId, Vue ){
        view = new CFEntryViewer2View( data, config, $, _, Vue );
        view.init( templateId, displayId, Vue );
    };


    function setupViewer( formData, entries ){
        data.fieldsList = _.toArray( _.allKeys( formData.fields ) );
        data.fields = formData.fields;
        if( ! _.isEmpty( formData.field_details.entry_list ) ){
            data.fieldOrder.headers = config.defaultColumns;

            _.each( formData.field_details.entry_list, function( id, i ){
                data.fieldOrder.headers[_.size( data.fieldOrder.headers )] = {
                    id: id,
                    label: formData.fields[ id ].label
                }
            });

        }else{
            data.fieldOrder.headers = config.defaultColumns;
        }

        if( ! _.isEmpty( formData.field_details.order ) ){

            data.fieldOrder.all = formData.field_details.entry_list;
        }else{
            data.fieldOrder.all = data.fieldOrder.headers
        }


        if( ! _.isEmpty( entries ) ){
            _.each( entries, function( entry, i ){

                data.entryCollection[i] = {
                    id: entry.id,
                    time: entry.datestamp
                };
                _.each( entry.fields, function( field, fi ){
                    if( $.inArray( field.id, data.fieldsList ) ){
                        data.entryCollection[i][field.field_id] = field.value;
                    }
                });
            } );
        }

        view = self.setupView( 'grid-template', 'demo', Vue );
    };

    function getForm(){
        return $.get( config.api.form + formId ).success( function( r ){
            return r;
        }).error( function( r ){
        });
    }

    function getEntries( page ){
        return $.get( config.api.entries + formId + '?page=' + page ).success( function( r ){
            return r;
        }).error( function( r ){
            console.log( r );
        });
    }
}