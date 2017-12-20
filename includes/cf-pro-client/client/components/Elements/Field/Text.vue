<template>
	<div class="caldera-config-group">
		<label v-bind:for="idAttr" v-html="label">
		</label>
		<_input
			:idAttr="idAttr"
			:value="value"
			:setting="setting"
		/>

	</div>
</template>
<script>
	import Label from '../Parts/Label/Label.vue';
	import Input from '../Parts/Input/Input.vue'
	export default{
		components: {
			_label: Label,
			_input: Input
		},
		props: {
			idAttr: {
				type: String,
				require: true
			},
			value:{
				type:String,
				required: true
			},
			setting:{
				type:String,
				required: true
			},
			label:{
				type:String,
				required: true
			},
		},

	}
</script>