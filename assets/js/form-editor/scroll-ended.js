jQuery.fn.scrollEnd = function(callback, timeout) {
	jQuery(this).scroll(function(){
		var jQuerythis = jQuery(this);
		if (jQuerythis.data('scrollTimeout')) {
			clearTimeout(jQuerythis.data('scrollTimeout'));
		}
		jQuerythis.data('scrollTimeout', setTimeout(callback,timeout));
	});
};