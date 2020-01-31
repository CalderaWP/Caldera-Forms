<template>
	<div id="cf-pro-form-settings">
		<div v-if="connected">
			<form-setting :form="form" :layouts="layouts"></form-setting>
		</div>
		<div v-if="!connected">
			<link-keys></link-keys>
		</div>
		<div id="cf-pro-alert-wrap">
			<status
					:message="mainAlert.message"
					:success="mainAlert.success"
					:show="mainAlert.show"
			>
			</status>
		</div>
	</div>
</template>
<script>

	import { mapState } from 'vuex'
	import { mapActions } from 'vuex'
	import debounce from 'lodash.debounce';
	import Status from '../components/Elements/Status/Status';
	import  formSetting from '../components/FormSettings/Form.vue';
    import  linkKeys from '../components/Link/linkKeys.vue';

	export default{
		components: {
			'form-setting' : formSetting,
            'link-keys' : linkKeys,
			'status' : Status
		},
		methods: {
			...mapActions([
				'getLayouts',
				'getAccount',
				'saveAccount',
				'testConnection'
			]),
		},
		beforeMount(){
			//If token is false, CF Pro is not connected
			if (this.apiKeys.token) {
				this.testConnection().then(() => {
				
				}, error => {
					//error is already handled. But must be caught to avoid a new error
				});
			}
		},
		computed: mapState({
			layouts: state => state.layouts,
			connected: state => state.connected,
			formScreen: state => state.formScreen,
			mainAlert: state => state.mainAlert,
			apiKeys: state => state.account.apiKeys
		}),
		data(){
			return{
				form: {},
			}
		}


	}
</script>