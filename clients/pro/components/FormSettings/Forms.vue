<template>
	<div>
		<div class="caldera-config-group">
			<label for="cf-pro-form-setting-chooser">
				Choose Form
			</label>
			<div class="caldera-config-field">
				<select
					id="cf-pro-form-setting-chooser"
					v-model="editForm"
				>
					<option></option>
					<option v-for="form in forms" v-bind:value="form.form_id">
						{{ form.name }}
					</option>
				</select>
			</div>
		</div>


		<div v-if="editForm">
			<form-setting
				:form="form"
				:layouts="layouts"
			>
			</form-setting>
		</div>
	</div>


</template>
<script>
	import  Form from './Form';
	import { mapState } from 'vuex';
	import { mapGetters } from 'vuex'
	import { mapActions } from 'vuex'
	export default{
		components:{
			'form-setting': Form
		},
		computed: mapState({
			forms: state => state.forms,
			layouts: state => state.layouts,
		}),
		methods: {
			...mapActions([
				'getLayouts'
			]),
		},
		beforeMount(){
			this.getLayouts();
		},
		data(){
			return{
				editForm: 0,
				form: {}
			}
		},
		watch: {
			editForm(v){
				this.form = this.$store.getters.getFormsById(v);
			}
		}

	}
</script>