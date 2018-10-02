import Vue from 'vue'
import Vuex from 'vuex'

Vue.use(Vuex);

import CFProConfig from './util/wpConfig'

const STATE = {
	loading: false == CFProConfig.token ? false : true,
	connected: false,
	forms: [
	],
	formScreen: '',
	settings : {
		enhancedDelivery: false,
		generatePDFs: false,
		logLevel: 250
	},
	layouts : [

	],
	account: {
		plan: String,
		id: Number,
		apiKeys: {
			public: CFProConfig.public,
			secret: CFProConfig.secret,
			token: CFProConfig.token
		}
	},
	strings: CFProConfig.strings,
	mainAlert : {
		success: false,
		message : '',
		show: false
	},
	logLevels: CFProConfig.logLevels
};


import { MUTATIONS } from './mutations';

import { ACTIONS } from './actions';

import  { GETTERS } from './getters';


import { accountSaver, formSaver } from './plugins';

const PLUGINS = [
	accountSaver,
	formSaver
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
