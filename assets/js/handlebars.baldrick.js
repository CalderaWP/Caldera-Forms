/* custom helpers */
  Handlebars.registerHelper("even", function(options) {
  	var intval = options.data.index / 2;
  	if( intval === Math.ceil( intval ) ){
  		return options.fn(this);
  	}else{
  		return false;
  	}
  	
  });
  Handlebars.registerHelper("odd", function(options) {
  	var intval = options.data.index / 2;
  	if( intval === Math.ceil( intval ) ){
  		return false;
  	}else{
  		return options.fn(this);
  	}
  });
  Handlebars.registerHelper("json", function(context) {
  		return JSON.stringify( context );
  });


/* Baldrick handlebars.js templating plugin */
(function($){
	var compiledTemplates	= {};
	$.fn.baldrick.registerhelper('handlebars', {
		bind	: function(triggers, defaults){
			var	templates = triggers.filter("[data-template-url]");
			if(templates.length){
				templates.each(function(){
					var trigger = $(this);

					if(typeof compiledTemplates[trigger.data('templateUrl')] === 'undefined'){
						compiledTemplates[trigger.data('templateUrl')] = true;

						if(typeof(Storage)!=="undefined"){

							var cache, key;
							
							if(trigger.data('cacheLocal')){
								
								key = trigger.data('cacheLocal');
								
								cache = localStorage.getItem( 'handlebars_' + key );
							
							}else if(trigger.data('cacheSession')){

								key = trigger.data('cacheSession');

								cache = sessionStorage.getItem( 'handlebars_' + key );
							}

						}
						
						if(cache){
							compiledTemplates[trigger.data('templateUrl')] = Handlebars.compile(cache);
						}else{
							$.get(trigger.data('templateUrl'), function(data, ts, xhr){
								
								if(typeof(Storage)!=="undefined"){

									var key;
									
									if(trigger.data('cacheLocal')){
										
										key = trigger.data('cacheLocal');

										localStorage.setItem( 'handlebars_' + key, xhr.responseText );
									
									}else if(trigger.data('cacheSession')){
										
										key = trigger.data('cacheSession');

										sessionStorage.setItem( 'handlebars_' + key, xhr.responseText );
									}
								}

								compiledTemplates[trigger.data('templateUrl')] = Handlebars.compile(xhr.responseText);
							});
						}
					}
				});
			}

		},
		request_params	: function(request, defaults, params){
			if((params.trigger.data('templateUrl') || params.trigger.data('template')) && typeof Handlebars === 'object'){
				request.dataType = 'json';
				return request;
			}
		},
		filter			: function(opts, defaults){			
			
			if(opts.params.trigger.data('templateUrl')){				
				if( typeof compiledTemplates[opts.params.trigger.data('templateUrl')] === 'function' ){					
					opts.data = compiledTemplates[opts.params.trigger.data('templateUrl')](opts.data);					
				}
			}else if(opts.params.trigger.data('template')){
				if( typeof compiledTemplates[opts.params.trigger.data('template')] === 'function' ){
					opts.data = compiledTemplates[opts.params.trigger.data('template')](opts.data);
				}else{
					if($(opts.params.trigger.data('template'))){
						compiledTemplates[opts.params.trigger.data('template')] = Handlebars.compile($(opts.params.trigger.data('template')).html());
						opts.data = compiledTemplates[opts.params.trigger.data('template')](opts.data);
					}
				}
			}

			return opts;
		}
	});

})(jQuery);