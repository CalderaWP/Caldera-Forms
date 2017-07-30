/** globals Vue **/

Vue.component( 'cf-status-indicator', {
	template: '<div class="cf-alert-wrap cf-hide"><p class="cf-alert cf-alert-success" v-if="show && success">{{message}}</p><p class="cf-alert cf-alert-warning" v-if="show && ! success">{{message}}</p></div>',
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
});

