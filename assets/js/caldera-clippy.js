function CalderaFormsAdminClippys2( elId, config, $ ){

	var self = this;



	this.init = function () {

		var vm;

		var linkCB = function(post,term){
			return post.link + '?utm-source=wp-admin&utm_campaign=clippy&utm_term=' + term;
		};

		var docsComponent = {
			template:  '#tmpl--caldera-help-clippy',
			props: [ 'important' ],
			methods: {
				link:linkCB
			}
		};

		var extendComponent = {
			template: '#tmpl--caldera-extend-clippy',
			props: [ 'product','title' ],
			methods: {
				link:linkCB
			}
		};


		$.when(
			get(config.cfdotcom.api.important),
			get(config.cfdotcom.api.product)
		).then(function (dImportant, dProduct) {
			var importantDocs = dImportant[0],
				products = dProduct[0];
			var product = products[ pickRandomProperty(products) ];
			vm = new Vue({
				el: '#caldera-forms-clippy',
				components: {
					docs : docsComponent,
					extend: extendComponent
				},
				data: function () {
					return {
						importantDocs: importantDocs,
						products: products,
						product: product,
						extendTitle: config.extend_title,
					}
				},
			});
		});


	};


	this.remove = function () {
		$( '#' + elId  ).remove();
	};

	function get( url ) {
		return $.get( url, {
			crossDomain: true
		} ).done( function(r){
			return r;
		}).error( function(){
			return false;
		});
	}

	function pickRandomProperty(obj) {
		var result;
		var count = 0;
		for (var prop in obj)
			if (Math.random() < 1/++count)
				result = prop;
		return result;
	}


}