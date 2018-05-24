<template>
	<input
			v-bind:type="type"
			v-bind:id="idAttr"
			@keyup="handle()"
			@change="handle()"
	/>
	<!--			v-model="value"-->
</template>
<script>
	export default{
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
			type: {
				type: String,
				default: 'text'
			}
		},
		methods: {
			handle(){
				this.$store.commit(this.setting,this.value);
			}
		},


	}
</script>