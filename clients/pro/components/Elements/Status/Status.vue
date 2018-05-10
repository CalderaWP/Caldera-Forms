<template>
	<div class="cf-alert-wrap cf-hide">
		<p class="cf-alert cf-alert-success" v-if="show && success">
			{{message}}
		</p>
		<p class="cf-alert cf-alert-warning" v-if="show && ! success">
			{{message}}
		</p>
	</div>
</template>
<script>
export default {
	props: [
		'success',
		'message',
		'show'
	],
	watch : {
		show: function () {
			if( this.show ){
				this.$el.className = "cf-alert-wrap cf-show";
			}else{
				this.$el.className = "cf-alert-wrap cf-hide";
			}
		}
	}

}
</script>
<style>
	.cf-alert {
		display: inline-block;
		padding: 4px;
		border-radius: 4px;
	}
	.cf-alert-success {
		background: #a3bf61;
		color: #fff;
	}
	.cf-alert-error {
		background: #ff0000;
		color: #fff;
	}

	.cf-alert-wrap{
		margin-top: -5px;
	}

	.cf-alert p {
		padding: 0;
	}
</style>