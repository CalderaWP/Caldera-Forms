function CalderaFormsAdminClippys2( elId, config, $ ){

	var self = this;



	this.init = function () {

		var vm;

		var docsComponent = {
			template:  '#tmpl--caldera-help-clippy',
			props: [ 'important' ],

		};

		var extendComponent = {
			template: '#tmpl--caldera-extend-clippy',
			props: [ 'product','title' ]
		};

		var supportComponent = {
			template: '#tmpl--caldera-support-clippy',
			props: [ 'type' ],


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
					support: supportComponent,
					extend: extendComponent
				},
				data: function () {
					return {
						importantDocs: importantDocs,
						products: products,
						product: product,
						extendTitle: config.extend_title,
						support: 'wantPriority',
						selected: 'A',
						options: [
							{ text: 'One', value: 'A' },
							{ text: 'Two', value: 'B' },
							{ text: 'Three', value: 'C' }
						]
					}
				},
			});
		});


	};


	this.remove = function () {
		$( document.getElementById( elId ).remove() );
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