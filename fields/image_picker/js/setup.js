
Handlebars.registerHelper("is_single", function(value, options) {
	if(Object.keys(value).length !== 1){
		return false;
	}else{
		return options.fn(this);
	}
});

Handlebars.registerHelper("is", function(value, options) {
	if(options.hash.value === value){
		return options.fn(this);
	}else{
		return false;
	}
});
