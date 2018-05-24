import Vue from 'vue'
import store from './store'
import SettingsView from './views/Settings.vue';
import {Tabs, Tab} from 'vue-tabs-component';


Vue.component('tabs', Tabs);
Vue.component('tab', Tab);

//@TODO Remove this hack-ass way of selecting which app to run
if( document.getElementById( 'cf-pro-app' ) ){
	const mainSettingsApp = new Vue({
		el: '#cf-pro-app',
		store,
		components: {
			'settings': SettingsView
		},
		render(h) {
			return h(
				'div',
				{
					attrs: {
						id: 'cf-pro-settings'
					}
				},
				[
					h( 'settings')
				]
			)
		}

	});
}

//@TODO and this.
import  FormTab from './views/Tab.vue';

if( document.getElementById( 'cf-pro-app-tab'  ) ){
	const tabApp = new Vue({
		el: '#cf-pro-app-tab',
		store,
		components: {
			'settings': FormTab
		},
		render(h) {
			return h(
				'div',
				{
					attrs: {
						id: 'cf-pro-settings-tab'
					}
				},
				[
					h( 'settings')
				]
			)
		}
	});
}



export { store }
