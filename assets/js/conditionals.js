(function($){

	// IE8 compatibility
	if (!Array.prototype.indexOf){
		Array.prototype.indexOf = function(elt /*, from*/){
			var len = this.length >>> 0;

			var from = Number(arguments[1]) || 0;
			from = (from < 0)
			? Math.ceil(from)
			: Math.floor(from);
			if (from < 0)
				from += len;

			for (; from < len; from++){
				if (from in this &&
					this[from] === elt)
					return from;
			}
			return -1;
		};
	}

	function calders_forms_check_conditions( inst_id ){
		
		if( typeof caldera_conditionals[inst_id] === "undefined"){
			return;
		}

		for( var field in caldera_conditionals[ inst_id ] ){

			// each conditional
			var fieldwrapper = jQuery('#conditional_' + field);
			
			if(!fieldwrapper.length){
				continue;
			}
			var type	=	caldera_conditionals[ inst_id ][field].type,
			groups	=	caldera_conditionals[ inst_id ][field].group,
			trues	=	[];
			
			// has a wrapper - bind conditions
			for(var id in groups){
				
				var truelines	= {},
				lines		= groups[id];						
				// go over each line in a group to find a false
				for(var lid in lines){					
					/// get field 

					var compareelement 	= jQuery('[data-field="' + lines[lid].field + '_' + lines[lid].instance + '"]'),
					comparefield 	= [],
					comparevalue	= (typeof lines[lid].value === 'function' ? lines[lid].value() : lines[lid].value);
					
					truelines[lid] 	= false;
					
					if( compareelement.is(':radio,:checkbox')){
						compareelement = compareelement.filter(':checked');
					}else if( compareelement.is('div')){
						compareelement = jQuery('<input>').val( compareelement.html() );
					}
					if(!compareelement.length){
						comparefield.push(lines[lid].field);
					}else{
						for( var i = 0; i<compareelement.length; i++){							
							comparefield.push(compareelement[i].value);
						}
					}					
					switch(lines[lid].compare) {
						case 'is':
						if(comparefield.length){
							if(comparefield.indexOf(comparevalue.toString()) >= 0){
								truelines[lid] = true;
							}
						}
						break;
						case 'isnot':
						if(comparefield.length){
							if(comparefield.indexOf(comparevalue) < 0){
								truelines[lid] = true;
							}
						}
						break;
						case '>':
						if( parseFloat( comparefield.reduce(function(a, b) {return a + b;}) ) > parseFloat( comparevalue ) ){
							truelines[lid] = true;
						}
						break;
						case '<':
						if( parseFloat( comparefield.reduce(function(a, b) {return a + b;}) ) < parseFloat( comparevalue ) ){
							truelines[lid] = true;
						}
						break;
						case 'startswith':
						for( var i = 0; i<comparefield.length; i++){
							if( comparefield[i].toLowerCase().substr(0, comparevalue.toLowerCase().length ) === comparevalue.toLowerCase()){
								truelines[lid] = true;
							}
						}
						break;
						case 'endswith':
						for( var i = 0; i<comparefield.length; i++){
							if( comparefield[i].toLowerCase().substr(comparefield[i].toLowerCase().length - comparevalue.toLowerCase().length ) === comparevalue.toLowerCase()){
								truelines[lid] = true;
							}
						}
						break;
						case 'contains':
						for( var i = 0; i<comparefield.length; i++){
							if( comparefield[i].toLowerCase().indexOf( comparevalue ) >= 0 ){
								truelines[lid] = true;
							}
						}
						break;
					}
				}				
				// add result in
				istrue = true;
				for( var prop in truelines ){
					if(truelines[prop] === false){
						istrue = false;
						break;
					}
				}
				trues.push(istrue);

			}

			

			var template	=	jQuery('#conditional-' + field + '-tmpl').html(),
			target		=	jQuery('#conditional_' + field),
			target_field=	jQuery('[data-field="' + field + '"]'),
			action;
			
			if(trues.length && trues.indexOf(true) >= 0){					
				if(type === 'show'){
					action = 'show';
				}else if (type === 'hide'){
					action = 'hide';
				}else if (type === 'disable'){
					action = 'disable';
				}
			}else{
				if(type === 'show'){
					action = 'hide';
				}else if (type === 'disable'){
					action = 'enable';
				}else{
					action = 'show';
				}
			}

			if(action === 'show'){
				// show - get template and place it in.
				if(!target.html().length){
					target.html(template).trigger('cf.add');
					jQuery(document).trigger('cf.add');
				}
			}else if (action === 'hide'){
				if(target.html().length){
					target_field.val('').empty().prop('checked', false);
					target.empty().trigger('cf.remove');
					jQuery(document).trigger('cf.remove');
				}
			}else if (action === 'enable'){
				if(!target.html().length){
					target.html(template).trigger('cf.add');
					jQuery(document).trigger('cf.add');
				}else{
					target_field.prop('disabled', false);
				}
			}else if (action === 'disable'){
				if(!target.html().length){
					target.html(template).trigger('cf.add');
					jQuery(document).trigger('cf.add');
					jQuery('[data-field="' + field + '"]').prop('disabled', 'disabled');
				}else{
					target_field.prop('disabled', 'disabled');
					jQuery(document).trigger('cf.disable');
				}
			}

		}	
	}
	
	if(typeof caldera_conditionals !== 'undefined'){
		
		jQuery('.caldera_forms_form').on('change keyup', '[data-field]', function(e){
			
			var form 			= $(this).closest('.caldera_forms_form').prop('id');

			calders_forms_check_conditions( form );

		});
		// init
		$('.caldera_forms_form').each( function(){
			calders_forms_check_conditions( $(this).closest('.caldera_forms_form').prop('id') );
		} );
	}
})(jQuery);