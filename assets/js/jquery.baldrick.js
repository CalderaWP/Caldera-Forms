/* -- BaldrickJS  V2.3 | (C) David Cramer - 2013 | MIT License */
(function($){
	// try not load again
	if( baldrickCache ){
		return;
	}

	var baldrickCache 		= {},
		baldrickRequests 	= {},
		baldrickhelpers 	= {
		_plugins		: {},
		load			: {},
		bind			: {},
		event			: function(el,e){
			return el;
		},
		pre_filter			: function(opts){
			return opts.data;
		},
		filter			: function(opts){
			return opts;
		},
		target			: function(opts){
			if(typeof opts.params.success === 'string'){
				if(typeof window[opts.params.success] === 'function'){
					window[opts.params.success](opts);
				}
			}else if(typeof opts.params.success === 'function'){
				opts.params.success(opts);
			}

			if(opts.params.target){

				if(opts.params.target.is('textarea,input') && typeof opts.data === 'object'){
					opts.params.target.val( JSON.stringify(opts.data) ).trigger('change');
				}else{
					opts.params.target[opts.params.targetInsert](opts.data);
				}
				if(typeof opts.params.callback === 'string'){
					if(typeof window[opts.params.callback] === 'function'){
						return window[opts.params.callback](opts);
					}
				}else if(typeof opts.params.callback === 'function'){
					return opts.params.callback(opts);
				}
			}
		},
		request_data : function(obj){
			return obj.data;
		},
		request			: function(opts){

			if( ( opts.params.trigger.data('cacheLocal') || opts.params.trigger.data('cacheSession') ) && !opts.params.trigger.data('cachePurge') ){

				var key;

				if( opts.params.trigger.data('cacheLocal') ){
					key = opts.params.trigger.data('cacheLocal');
				}else if(opts.params.trigger.data('cacheSession')){
					key = opts.params.trigger.data('cacheSession');
				}

				// check for a recent object
				if(typeof baldrickCache[key] !== 'undefined'){
					return {data: baldrickCache[key]};
				}				

				// check if there is a stored obejct to be loaded
				if(typeof(Storage)!=="undefined"){

					var cache;
					
					// load storage
					if( opts.params.trigger.data('cacheLocal') ){
						cache = localStorage.getItem( key );
					}else if(opts.params.trigger.data('cacheSession')){
						cache = sessionStorage.getItem( key );
					}
					if(cache){
						try {
							//baldrickCache[key] = JSON.parse(cache);
							cache = JSON.parse(cache);
						} catch (e) {
							//baldrickCache[key] = cache;
							cache = cache;
						}
						return {data: cache};
					}
					
				}

			}
			if( baldrickRequests[opts.params.trigger.prop('id')] ){
				baldrickRequests[opts.params.trigger.prop('id')].abort();
			}
			baldrickRequests[opts.params.trigger.prop('id')] = $.ajax(opts.request);
			return baldrickRequests[opts.params.trigger.prop('id')];
		},
		request_complete: function(opts){
			opts.params.complete(opts);
			opts.params.loadElement.removeClass(opts.params.loadClass);
			if( baldrickRequests[opts.params.trigger.prop('id')] ){
				delete baldrickRequests[opts.params.trigger.prop('id')];
			}
		},
		request_error	: function(opts){
			opts.params.error(opts);
			opts.params.complete(opts.jqxhr,opts.textStatus);
		},
		refresh	: function(opts, defaults){
			$(defaults.triggerClass).baldrick(defaults);
		}
	};

	$.fn.baldrick = function(opts){

		var do_helper = function(h, input, ev){
			var out;
			// pull in plugins before
			for(var before in defaults.helpers._plugins){
				if(typeof defaults.helpers._plugins[before][h] === 'function'){
					out = defaults.helpers._plugins[before][h](input, defaults, ev);
					if(typeof out !== 'undefined'){ input = out;}
					if(input === false){return false;}
				}
			}
			if(typeof defaults.helpers[h] === 'function'){
				out = defaults.helpers[h](input, defaults, ev);
				if(typeof out !== 'undefined'){ input = out;}
				if(!input){return false;}
			}
			// pull in plugins after
			for(var after in defaults.helpers._plugins){
				if(typeof defaults.helpers._plugins[after]['after_' + h] === 'function'){
					out = defaults.helpers._plugins[after]['after_' + h](input, defaults, ev);
					if(typeof out !== 'undefined'){ input = out;}
					if(input === false){return false;}
				}
			}
			return input;
		},
		serialize_form	=	function(form){

			var config			= {},
				data_fields		= form.find('input,radio,checkbox,select,textarea,file'),
				objects			= [],
				arraynames		= {};

			// no fields - exit			
			if(!data_fields.length){
				return;
			}

			for( var v = 0; v < data_fields.length; v++){
				if( data_fields[v].getAttribute('name') === null){
					continue;
				}
				var field 		= $(data_fields[v]),
					basename 	= field.prop('name').replace(/\[/gi,':').replace(/\]/gi,''),//.split('[' + id + ']')[1].substr(1),
					name		= basename.split(':'),
					value 		= ( field.is(':checkbox,:radio') ? field.filter(':checked').val() : field.val() ),
					lineconf 	= {};					

				for(var i = name.length-1; i >= 0; i--){
					var nestname = name[i];
					if(nestname.length === 0){
						if( typeof arraynames[name[i-1]] === 'undefined'){
							arraynames[name[i-1]] = 0;
						}else{
							arraynames[name[i-1]] += 1;
						}
						nestname = arraynames[name[i-1]];
					}
					if(i === name.length-1){
						lineconf[nestname] = value;
					}else{
						var newobj = lineconf;
						lineconf = {};
						lineconf[nestname] = newobj;
					}		
				}
				
				$.extend(true, config, lineconf);
			};
			// give json object to trigger
			//params.data = JSON.stringify(config);
			//params.data = config;
			return config;
		},
		triggerClass	= this.selector,
		inst			= this.not('._tisBound');

		inst.addClass('_tisBound');
		if(typeof opts !== 'undefined'){
			if(typeof opts.helper === 'object'){
				baldrickhelpers._plugins._params_helpers_ = opts.helper;
			}
		}
		var defaults		= $.extend(true, opts, { helpers : baldrickhelpers}, {triggerClass:triggerClass}),
			ncb				= function(){return true;},
			callbacks		= {
				"init"		: ncb,
				"before"	: ncb,
				"callback"	: false,
				"success"	: false,
				"complete"	: ncb,
				"error"		: ncb
			},
			output;

		for(var c in callbacks){
			if(typeof defaults[c] === 'string'){
				callbacks[c] = (typeof window[defaults[c]] === 'function' ? window[defaults[c]] : ncb);
			}else if(typeof defaults[c] === 'function'){
				callbacks[c] = defaults[c];
			}
		}

		inst = do_helper('bind', inst);
		if(inst === false){return this;}
		return do_helper('ready', inst.each(function(key){
			if(!this.id){
				this.id = "baldrick_trigger_" + (new Date().getTime() + key);
			}
			var el = $(this), ev = (el.data('event') ? el.data('event') : (defaults.event ? defaults.event : ( el.is('form') ? 'submit' : 'click' )));
			el.on(ev, function(e){

				var tr = $(do_helper('event', this, e));

				if(tr.data('for')){
					var fort		= $(tr.data('for')),
						datamerge	= $.extend({}, fort.data(), tr.data());
						delete datamerge['for'];
					fort.data(datamerge);
					if( fort.is('form') ){						
						fort.submit();
						return this;
					}else{
						return fort.trigger((fort.data('event') ? fort.data('event') : ev));
					}
				}
				if(tr.is('form') && !tr.data('request') && tr.attr('action')){
					tr.data('request', tr.attr('action'));
				}
				if(tr.is('a') && !tr.data('request') && tr.attr('href')){
					if(tr.attr('href').indexOf('#') < 0){
						tr.data('request', tr.attr('href'));
					}else{
						tr.data('href', tr.attr('href'));
					}
				}

				if((tr.data('before') ? (typeof window[tr.data('before')] === 'function' ? window[tr.data('before')](this, e) : callbacks.before(this, e)) : callbacks.before(this, e)) === false){
					$(defaults.triggerClass).baldrick(defaults);
					return;
				}

				if((tr.data('init') ? (typeof window[tr.data('init')] === 'function' ? window[tr.data('init')](this, e) : callbacks.init(this, e)) : callbacks.init(this, e)) === false){
					$(defaults.triggerClass).baldrick(defaults);
					return;
				}

				var params = {
					trigger: tr,
					callback : (tr.data('callback')		? ((typeof window[tr.data('callback')] === 'function') ? window[tr.data('callback')] : tr.data('callback')) : callbacks.callback),
					success : (tr.data('success')		? ((typeof window[tr.data('success')] === 'function') ? window[tr.data('success')] : tr.data('success')) : callbacks.success),
					method : (tr.data('method')			? tr.data('method')				: (tr.attr('method')		? tr.attr('method') :(defaults.method ? defaults.method : 'GET'))),
					dataType : (tr.data('type')			? tr.data('type')				: (defaults.dataType		? defaults.dataType : false)),
					timeout : (tr.data('timeout')		? tr.data('timeout')			: 120000),
					target : (tr.data('target')			? ( tr.data('target') === '_parent' ? tr.parent() : ( tr.data('target') === '_self' ? $(tr) : $(tr.data('target')) ) )			: (defaults.target			? $(defaults.target) : $('<html>'))),
					targetInsert : (tr.data('targetInsert')	? (tr.data('targetInsert') === 'replace' ? 'replaceWith' : tr.data('targetInsert'))	: (defaults.targetInsert ? (defaults.targetInsert === 'replace' ? 'replaceWith': defaults.targetInsert) : 'html')),
					loadClass : (tr.data('loadClass')		? tr.data('loadClass')			: (defaults.loadClass		? defaults.loadClass : 'loading')),
					activeClass : (tr.data('activeClass')	? tr.data('activeClass')		: (defaults.activeClass		? defaults.activeClass : 'active')),
					activeElement : (tr.data('activeElement')	? (tr.data('activeElement') === '_parent' ? tr.parent() :$(tr.data('activeElement')))	: (defaults.activeElement ? (defaults.activeElement === '_parent' ? tr.parent() : $(defaults.activeElement)) : tr)),
					cache : (tr.data('cache')			? tr.data('cache')				: (defaults.cache			? defaults.cache : false)),
					complete : (tr.data('complete')		? (typeof window[tr.data('complete')] === 'function'		? window[tr.data('complete')] : callbacks.complete ) : callbacks.complete),
					error : (tr.data('error')		? (typeof window[tr.data('error')] === 'function'		? window[tr.data('error')] : callbacks.error ) : callbacks.error),
					resultSelector : false,
					event : ev
				};
				params.url			= (tr.data('request')		? ( tr.data('request') )			: (defaults.request			? defaults.request : params.callback));
				params.loadElement	= (tr.data('loadElement')	? (tr.data('loadElement') === '_parent' ? tr.parent() :$(tr.data('loadElement')))		: (defaults.loadElement		? ($(defaults.loadElement) ? $(defaults.loadElement) : params.target) : params.target));

				params = do_helper('params', params);
				if(params === false){return false;}

				// check if request is a function
				e.preventDefault();
				if(typeof window[params.url] === 'function'){
					
					var dt = window[params.url](params, ev);
					dt = do_helper('pre_filter', {data:dt, params: params});
					dt = do_helper('filter', {data:dt, rawData: dt, params: params});
					do_helper('target', dt);
					do_helper('refresh', {params:params});
					do_helper('request_complete', {jqxhr:null, textStatus:'complete', request:request, params:params});

					return this;
				}else{

					try{
						if( $(params.url).length ){
							var dt = $(params.url).is('input,select,radio,checkbox,file,textarea') ? $(params.url).val() : ( $(params.url).is('form') ? serialize_form( $(params.url) ) : $(params.url).html() );
						}
					}catch (e){}

					if(typeof dt !== 'undefined'){

						if(params.dataType === 'json'){
							try{
								dt = JSON.parse(dt);
							}catch (e){}
						}

						dt = do_helper('pre_filter', {data:dt, params: params});
						dt = do_helper('filter', {data:dt, rawData: dt, params: params});
						do_helper('target', dt);
						do_helper('refresh', {params:params});
						do_helper('request_complete', {jqxhr:null, textStatus:'complete', request:request, params:params});

						var dt_enabled = true;						
						return this;
					}
				}
				switch (typeof params.url){
					case 'function' : return params.url(this, e);
					case 'boolean' :
					case 'object': return;
					case 'string' :
						if(params.url.indexOf(' ') > -1){
							var rp = params.url.split(' ');
							params.url	= rp[0];
							params.resultSelector	= rp[1];
						}
				}
				
				var active = (tr.data('group') ? $('._tisBound[data-group="'+tr.data('group')+'"]').each(function(){
					var or  = $(this),
						tel = (or.data('activeElement') ? (or.data('activeElement') === '_parent' ? or.parent() :$(or.data('activeElement'))) : (defaults.activeElement ? (defaults.activeElement === '_parent' ? tr.parent() : $(defaults.activeElement)) : or) );
					tel.removeClass((or.data('activeClass') ? or.data('activeClass') : (defaults.activeClass ? defaults.activeClass : params.activeClass)));}
				) : $('._tisBound:not([data-group])').each(function(){
					var or  = $(this),
						tel = (or.data('activeElement') ? (or.data('activeElement') === '_parent' ? or.parent() :$(or.data('activeElement'))) : (defaults.activeElement ? (defaults.activeElement === '_parent' ? tr.parent() : $(defaults.activeElement)) : or) );
					tel.removeClass((or.data('activeClass') ? or.data('activeClass') : (defaults.activeClass ? defaults.activeClass : params.activeClass)));}
				));
				
				params.activeElement.addClass(params.activeClass);
				params.loadElement.addClass(params.loadClass);
				var data;
				if( typeof FormData !== 'undefined' && ( tr.is('input:file') || params.method === 'POST') ){

					params.method		=	'POST';
					params.contentType	=	false;
					params.processData	=	false;
					params.cache		=	false;
					params.xhrFields	= {
						onprogress: function (e) {
							if (e.lengthComputable) {
								//console.log('Loaded '+ (e.loaded / e.total * 100) + '%');
							} else {
								//console.log('Length not computable.');
							}
						}
					};

					if(tr.is('form')){
						data = new FormData(tr[0]);
					}else{

						data = new FormData();
					}

					if(tr.is('input,select,textarea')){
						// add value as _value for each access
						tr.data('_value', tr.val());
					}
					// make field vars
					for(var att in params.trigger.data()){
						data.append(att, params.trigger.data(att));
					}
					// convert param.data to json
					if(params.data){
						data.append('data', JSON.stringify(params.data));
					}
					// use input
					if(tr.is('input,select,textarea')){

						if(tr.is('input:file')){														
							if(tr[0].files.length > 1){								
								for( var file = 0; file < tr[0].files.length; file++){
									data.append(tr.prop('name'), tr[0].files[file]);
								}
							}else{
								data.append(tr.prop('name'), tr[0].files[0]);
							}

						}else if(tr.is('input:checkbox') || tr.is('input:radio')){
							if(tr.prop('checked')){
								data.append(tr.prop('name'), tr.val());
							}
						}else{
							data.append(tr.prop('name'), tr.val());
						}
					}
				}else{
					
					var sd = tr.serializeArray(), atts = params.trigger.data(), param = [];
					//console.log(atts);
					// insert user set params
					if(defaults.data){
						atts = $.extend(defaults.data, atts);
					}

					if(sd.length){
						$.each( sd, function(k,v) {
							param.push(v);
						});
						params.requestData = serialize_form(tr);
					}
					// convert param.data to json
					if(params.data){
						atts = $.extend(atts, params.data);
					}					
					data = atts;
					params.requestData = $.extend(tr.data(), params.requestData);
				}

				var request = {
						url		: params.url,
						data	: do_helper('request_data', {data:data, params: params }),
						cache	: params.cache,
						timeout	: params.timeout,
						type	: params.method,
						success	: function(dt, ts, xhr){
							if(params.resultSelector){
								if(typeof dt === 'object'){
									var traverse = params.resultSelector.replace(/\[/g,'.').replace(/\]/g,'').split('.'),
										data_object = dt;
									for(var i=0; i<traverse.length; i++){
										data_object = data_object[traverse[i]];
									}
									dt = data_object;
								}else if (typeof dt === 'string'){
									var tmp = $(params.resultSelector, $('<html>').html(dt));
									if(tmp.length === 1){
										dt = $('<html>').html(tmp).html();
									}else{
										dt = $('<html>');
										tmp.each(function(){
											dt.append(this);
										});
										dt = dt.html();
									}
								}
							}
							var rawdata = dt;							
							if(params.trigger.data('cacheLocal') || params.trigger.data('cacheSession')){

								
								var key;

								if( params.trigger.data('cacheLocal') ){
									key = params.trigger.data('cacheLocal');
								}else if(params.trigger.data('cacheSession')){
									key = params.trigger.data('cacheSession');
								}

								// add to local storage for later
								if(typeof(Storage)!=="undefined"){
									if( params.trigger.data('cacheLocal') ){
										try{
											localStorage.setItem( key, xhr.responseText );
										} catch (e) {
											console.log(e);
										}
									}else if( params.trigger.data('cacheSession') ){
										try{
											sessionStorage.setItem( key, xhr.responseText );
										} catch (e) {
											console.log(e);
										}

									}
								}

								// add to current cache object
								//baldrickCache[key] = dt;
								$(window).trigger('baldrick.cache', key);
							}

							dt = do_helper('pre_filter', {data:dt, request: request, params: params, xhr: xhr});
							dt = do_helper('filter', {data:dt, rawData: rawdata, request: request, params: params, xhr: xhr});
							do_helper('target', dt);
						},
						complete: function(xhr,ts){
							
							do_helper('request_complete', {jqxhr:xhr, textStatus:ts, request:request, params:params});
							
							do_helper('refresh', {jqxhr:xhr, textStatus:ts, request:request, params:params});

							if(tr.data('once')){
								tr.off(ev).removeClass('_tisBound');
							}
						},
						error: function(xhr,ts,ex){
							do_helper('request_error', {jqxhr:xhr, textStatus:ts, error:ex, request:request, params:params});
						}
					};
				if(params.dataType){
					request.dataType = params.dataType;
				}
				if(typeof params.contentType !== 'undefined'){
					request.contentType = params.contentType;
				}
				if(typeof params.processData !== 'undefined'){
					request.processData = params.processData;
				}
				if(typeof params.xhrFields !== 'undefined'){
					request.xhrFields = params.xhrFields;
				}

				request = do_helper('request_params', request, params);
				if(request === false){return inst;}

				var request_result = do_helper('request', {request: request, params: params});

				// A Request helper returns a completed object, if it contains data, push to the rest.
				if(request_result.data){

					var dt		= request_result.data,
						rawdata = dt;

					do_helper('target'				,
							do_helper('filter'		,
							do_helper('pre_filter'	, {data:dt, request: request, params: params})
						)
					);
					do_helper('request_complete', {jqxhr:false, textStatus:true, request:request, params:params});
					do_helper('refresh'			, {jqxhr:false, textStatus:true, request:request, params:params});


				}
			});
			if(el.data('autoload') || el.data('poll')){
				if(el.data('delay')){
					setTimeout(function(el, ev){
						return el.trigger(ev);
					}, el.data('delay'), el, ev);
				}else{
					el.trigger(ev);
				}
			}

			if(el.data('poll')){
				if(el.data('delay')){
					setTimeout(function(el, ev){
						return setInterval(function(el, ev){
							return el.trigger(ev);
						}, el.data('poll'), el, ev);
					}, el.data('delay'));
				}else{
					setInterval(function(el, ev){
						return el.trigger(ev);
					}, el.data('poll'), el, ev);
				}
			}
			return this;
		}));
	};
	$.fn.baldrick.cacheObject = function(id, object){
		baldrickCache[id] = object;
	};
	$.fn.baldrick.registerhelper = function(slug, helper, callback){
		var newhelper = {};
		if(typeof helper === 'object'){
			newhelper[slug] = helper;
			baldrickhelpers._plugins = $.extend(true, newhelper, baldrickhelpers._plugins);
		}else if(typeof helper === 'string' && typeof slug === 'string' && typeof callback === 'function'){
			newhelper[helper] = {};
			newhelper[helper][slug] = callback;
			baldrickhelpers._plugins = $.extend(true, newhelper, baldrickhelpers._plugins);
		}
		
	};

})(jQuery);