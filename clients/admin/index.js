//Import CSS
import './index.scss';


/** Vue App(s) **/
import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);
import store from './store/index';
import AdminRight from './Views/AdminRight.vue';



jQuery(document).ready(function($){

    //setup clippy on admin, not edit
    if( null !== document.getElementById( 'caldera-forms-clippy') ){
        new Vue({
            el:'#caldera-forms-clippy',
            render(h){
                return h(AdminRight);
            },
            store
        });

        $( '.cf-entry-viewer-link' ).on( 'click', function(){
            jQuery( '.caldera-forms-clippy-zone' ).remove();
        });
    }

});


