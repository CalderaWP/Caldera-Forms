/**
 Vue State management for main admin page
 */

import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

const STATE = {
    contentBoxData: {
        importantDocs: [],
        products: [],
        product: {}
    },
    contentExtendTitle: '',
};


import { MUTATIONS } from './mutations';

import { ACTIONS } from './actions';

import  { GETTERS } from './getters';

const PLUGINS = [

];

const store =  new Vuex.Store({
  strict: false,
  plugins: PLUGINS,
  modules: {},
  state: STATE,
  getters: GETTERS,
  mutations: MUTATIONS,
  actions: ACTIONS
});


export default store;
