import  { objHasProp, findForm } from './util/utils';

export const GETTERS = {
	publicKey: state => {
		return state.account.apiKeys.public;
	},
	secretKey: state => {
		return  state.account.apiKeys.secret;
	},
	apiKeys: state => {
		return state.account.apiKeys;
	},
	hi: state => {
		return 'Roy'
	},
	getSetting: state => (setting,_default) => {
		if( objHasProp(state.settings, setting )){
			return state.settings[setting];
		}
		return _default;
	},
	getFormsById: (state, getters) => (id) => {
		return state.forms.find(form => form.form_id === id);
	},

	enhancedDelivery: state => {
		return state.settings.enhancedDelivery;
	},
	logLevels: state => {
		return state.logLevels;
	},
	logLevel: state => {
		return state.settings.logLevel;
	},
	connected: state => {
		return state.connected;
	},
	formScreen: state => {
		return state.formScreen;
	},
	strings: state => {
		return state.strings;
	},
	mainAlert: state => {
		return state.mainAlert;
	}
};
