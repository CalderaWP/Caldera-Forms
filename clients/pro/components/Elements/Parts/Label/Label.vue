<template>
	<label v-bind:for="idAttr" v-html="label">
	</label>
</template>
<script>
	export default{
		idAttr: {
			type: String,
			require: true
		},
		label:{
			type:String,
			required: true
		},
	}
</script>