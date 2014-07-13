(function($){

	function calders_forms_check_conditions(){
		
		for(var field in caldera_conditionals){

			// each conditional
			var fieldwrapper = $('#conditional_' + field);
			
			if(!fieldwrapper.length){
				continue;
			}
			var type	=	caldera_conditionals[field].type,
				groups	=	caldera_conditionals[field].group,
				trues	=	[];
			
			// has a wrapper - bind conditions
			for(var id in groups){
				
				var truelines	= {},
					lines		= groups[id];						
				// go over each line in a group to find a false
				for(var lid in lines){
					/// get field 
					var compareelement 	= $('[data-field="' + lines[lid].field + '"]'),
						comparefield 	= [];
					
					truelines[lid] 	= false;
					
					if( compareelement.is(':radio,:checkbox')){
						compareelement = compareelement.filter(':checked');
					}
					if(!compareelement.length){
						comparefield.push("");
					}else{
						for( var i = 0; i<compareelement.length; i++){
							comparefield.push(compareelement[i].value);
						}
					}

					switch(lines[lid].compare) {
						case 'is':
							if(comparefield.length){
								if(comparefield.indexOf(lines[lid].value) >= 0){
									truelines[lid] = true;
								}
							}
							break;
						case 'isnot':
							if(comparefield.length){
								if(comparefield.indexOf(lines[lid].value) < 0){
									truelines[lid] = true;
								}
							}
							break;
						case '>':
							if( parseFloat( comparefield.reduce(function(a, b) {return a + b;}) ) > parseFloat( lines[lid].value ) ){
								truelines[lid] = true;
							}
							break;
						case '<':
							if( parseFloat( comparefield.reduce(function(a, b) {return a + b;}) ) < parseFloat( lines[lid].value ) ){
								truelines[lid] = true;
							}
							break;
						case 'startswith':
							for( var i = 0; i<comparefield.length; i++){
								if( comparefield[i].toLowerCase().substr(0, lines[lid].value.toLowerCase().length ) === lines[lid].value.toLowerCase()){
									truelines[lid] = true;
								}
							}
							break;
						case 'endswith':
							for( var i = 0; i<comparefield.length; i++){
								if( comparefield[i].toLowerCase().substr(comparefield[i].toLowerCase().length - lines[lid].value.toLowerCase().length ) === lines[lid].value.toLowerCase()){
									truelines[lid] = true;
								}
							}
							break;
						case 'contains':
							for( var i = 0; i<comparefield.length; i++){
								if( comparefield[i].toLowerCase().indexOf( lines[lid].value ) >= 0 ){
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

			

			var template	=	$('#conditional-' + field + '-tmpl').html(),
				target		=	$('#conditional_' + field),
				target_field=	$('[data-field="' + field + '"]'),
				action;
			
			if(trues.length && trues.indexOf(true) >= 0){					
				if(type === 'show'){
					action = 'show';
				}else if (type === 'hide'){
					action = 'hide';
				}
			}else{
				if(type === 'show'){
					action = 'hide';
				}else{
					action = 'show';
				}
			}

			if(action === 'show'){
				// show - get template and place it in.
				if(!target.html().length){
					target.html(template).trigger('cf.add');
				}
			}else if (action === 'hide'){
				if(target.html().length){
					target_field.val('').empty().prop('checked', false);
					target.empty().trigger('cf.remove');
				}
			}

		}	
	}
	
	if(typeof caldera_conditionals !== 'undefined'){
		
		$('.caldera_forms_form').on('change keyup', 'input,select,textarea', function(e){
			calders_forms_check_conditions();

		});
		// init
		calders_forms_check_conditions();
	}
})(jQuery);