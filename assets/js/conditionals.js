(function($){

	function calders_forms_check_conditions(el){
		
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
				
				var truelines	= [],
					lines		= groups[id];						
				// go over each line in a group to find a false
				for(var lid in lines){
					/// get field 
					var comparefield 	= $('[data-field="' + lines[lid].field + '"]'),
						linetmp 		= false;
					if( comparefield.is(':radio,:checkbox')){
						comparefield = comparefield.filter(':checked');
					}

					for( var i = 0; i<comparefield.length; i++){
						
						switch(lines[lid].compare) {
							case 'is':
								if(comparefield[i].value.toLowerCase() === lines[lid].value.toLowerCase()){
									linetmp = true;
								}
								break;
							case 'isnot':
								if(comparefield[i].value.toLowerCase() !== lines[lid].value.toLowerCase()){
									linetmp = true;
								}
								break;
							case '>':
								if( parseFloat( comparefield[i].value ) > parseFloat( lines[lid].value ) ){
									linetmp = true;
								}
								break;
							case '<':
								if( parseFloat( comparefield[i].value ) < parseFloat( lines[lid].value ) ){
									linetmp = true;
								}
								break;
							case 'startswith':
								if( comparefield[i].value.toLowerCase().substr(0, lines[lid].value.toLowerCase().length ) === lines[lid].value.toLowerCase()){
									linetmp = true;
								}
								break;
							case 'endswith':
								if( comparefield[i].value.toLowerCase().substr(comparefield[i].value.toLowerCase().length - lines[lid].value.toLowerCase().length ) === lines[lid].value.toLowerCase()){
									linetmp = true;
								}
								break;
							case 'contains':
								if( comparefield[i].value.toLowerCase().indexOf( lines[lid].value ) >= 0 ){
									linetmp = true;
								}
								break;
						}
					}

					truelines.push(linetmp);
				}
				// add result in
				if(truelines.length && truelines.indexOf(false) < 0){
					trues.push(true);
				}else{
					trues.push(false);
				}

			}

			

			var template	=	$('#conditional-' + field + '-tmpl').html(),
				target		=	$('#conditional_' + field),
				action;
			console.log(target);
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
					target.html(template);
				}						
			}else if (action === 'hide'){
				target.empty();
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