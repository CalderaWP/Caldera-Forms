<template>
	<div>
		<div class="caldera-config-group">
			<label v-bind:for="layoutIdAttr">
				Email Layout
			</label>
			<div class="caldera-config-field">
				<select
					v-bind:id="layoutIdAttr"
					v-model="form.layout"
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
							v-model="form.pdf_layout"
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
					v-model="form.attach_pdf"
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
					v-model="form.pdf_link"
					v-bind:id="attachPDFIdAttr"
					@change="changeLinkPDF"
				/>
			</div>
		</div>
	</div>
</template>
<script>
	import { mapState } from 'vuex'
	import Checkbox from '../Elements/Field/Checkbox';
	import { findForm } from '../../store/util/utils';

	export default{
		components :{
			checkbox: Checkbox
		},
		props : [ 'form', 'layouts' ],
		computed :{
			layoutIdAttr(){
				return 'cf-pro-layout-' + this.form.form_id;
			},
			pdfLayoutIdAttr(){
				return 'cf-pro-layout-pdf-' + this.form.form_id;
			},
			attachPDFIdAttr(){
				return 'cf-pro-layout-' + this.form.form_id;
			},
			linkPDFIdAttr(){
				return 'cf-pro-layout-' + this.form.form_id;
			}
		},
		methods:{
			commitChange(what,value){
				this.form[what] = value;
				this.$store.commit( 'form', this.form );
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
			}
		}


	}
</script>