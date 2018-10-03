<template>
	<div>
		<div class="caldera-config-group">
			<label v-if="singleForm.send_local != true" v-bind:for="sendLocalIdAttr">
					Disable enhanced delivery for this form
			</label>
			<label v-else v-bind:for="sendLocalIdAttr">
					Enhanced delivery is disabled for this form
			</label>
			<div class="caldera-config-field">
				<input
						type="checkbox"
						v-model="singleForm.send_local"
						v-bind:id="sendLocalIdAttr"
						@change="changeSendLocal"
				/>
			</div>
		</div>
		<div v-if="singleForm.send_local != true">
			<div class="caldera-config-group">
				<label v-bind:for="layoutIdAttr">
					Email Layout 
				</label>
				<div class="caldera-config-field">
					<select
						v-bind:id="layoutIdAttr"
						v-model="singleForm.layout"
						@change="changeLayout"
					>
						<option></option>
						<option v-for="option in layouts" v-bind:value="option.id">
							{{ option.name }}
						</option>
					</select>
				</div>
			</div>
			<div class="caldera-config-group">
				<label v-bind:for="pdfLayoutIdAttr">
					PDF Layout
				</label>
					<div class="caldera-config-field">
						<select
								v-bind:id="pdfLayoutIdAttr"
								v-model="singleForm.pdf_layout"
								@change="changePDFLayout"
						>
							<option></option>
							<option v-for="option in layouts" v-bind:value="option.id">
								{{ option.name }}
							</option>
						</select>
					</div>
			</div>
			<div class="caldera-config-group">
				<label v-bind:for="attachPDFIdAttr">
					Attach PDF To Main Mailer
				</label>
				<div class="caldera-config-field">
					<input
						type="checkbox"
						v-model="singleForm.attach_pdf"
						v-bind:id="attachPDFIdAttr"
						@change="changeAttachPDF"
					/>
				</div>
			</div>
			<div class="caldera-config-group">
				<label v-bind:for="attachPDFIdAttr">
					Add PDF Link
				</label>
				<div class="caldera-config-field">
					<input
						type="checkbox"
						v-model="singleForm.pdf_link"
						v-bind:id="attachPDFIdAttr"
						@change="changeLinkPDF"
					/>
				</div>
			</div>
		</div>
	</div>
</template>
<script>
	import { mapState } from 'vuex';
	import { mapGetters } from 'vuex';
	import Checkbox from '../Elements/Field/Checkbox';

	export default{
		components :{
			checkbox: Checkbox
		},
		props : [ 'form', 'layouts' ],
		computed : {
			...mapState({
				formScreen: state => state.formScreen
			}),
			formID(){
				if(this.form.form_id){
					return this.form.form_id;
				} else {
					return this.formScreen;
				}
			},
			singleForm(){
				if(this.form.form_id){
					return this.form;
				} else {
					return this.$store.getters.getFormsById(this.formID);
				}
			},
			layoutIdAttr(){
				return 'cf-pro-layout-' + this.formID;
			},
			pdfLayoutIdAttr(){
				return 'cf-pro-layout-pdf-' + this.formID;
			},
			attachPDFIdAttr(){
				return 'cf-pro-layout-' + this.formID;
			},
			linkPDFIdAttr(){
				return 'cf-pro-layout-' + this.formID;
			},
			sendLocalIdAttr(){
				return 'cf-pro-send-local-' + this.formID;
			},
		},
		methods:{
			commitChange(what,value){
				this.singleForm[what] = value;
				this.$store.commit( 'form', this.singleForm );
			},
			changeLayout(ev){
				this.commitChange(ev.target.value,'layout');
			},
			changePDFLayout(ev){
				this.commitChange(ev.target.value,'pdf_layout');
			},
			changeAttachPDF(ev){
				this.commitChange(ev.target.value,'attach_pdf');
			},
			changeLinkPDF(ev){
				this.commitChange(ev.target.value,'pdf_link');
			},
            changeSendLocal(ev){
                this.commitChange(ev.target.value,'send_local');
            }
		}


	}
</script>