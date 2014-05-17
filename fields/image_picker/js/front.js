// Uploading files
var cu_use_uploaded_image;

cu_use_uploaded_image = function(obj){

	var clicked;

	if(typeof obj.params !== 'object'){
		var field = jQuery(obj);			

		clicked = field.prev();

		clicked.css({opacity: 0.5}).text(clicked.data('loading'));

		return true;
	}

	clicked = obj.params.trigger.prev();		

	// check for error
	if(obj.data.error){
		alert(obj.data.error);

		clicked.css({opacity: 1}).text(clicked.data('text'));		
		obj.params.trigger.prop('disabled', true);
		field.css({opacity: 0.5});
		clicked.text(clicked.data('loading'));

		return;
	}
	clicked.css({opacity: 1}).text(clicked.data('text'));

	var parent = jQuery('#' + obj.params.trigger.data('id')),
		image = parent.find('.image-picker-thumbnail'),
		id = parent.find('.image-picker-image-id'),
		thumb = parent.find('.image-picker-image-thumb');

		image.attr('src', obj.data.url);
		id.val(obj.data.ID);
		thumb.val(obj.data.url);
		obj.params.trigger.prop('disabled', true);
};

jQuery(function($){


	$('body').on('click', '.cu-image-picker', function( e ){
		
		e.preventDefault();
		$(this).next().prop('disabled', false).trigger('click');
	});

	$('body').on('click', '.cu-image-remover', function( e ){
		var clicked = $(this),
			panel = clicked.closest('.caldera-config-group'),
			thumbnail = panel.find('.image-picker-thumbnail'),
			thumbnail_val = panel.find('.image-picker-image-thumb'),
			value = panel.find('.image-picker-image-id'),
			sizer = panel.find('.image-picker-sizer'),
			remover = panel.find('.cu-image-remover');

		thumbnail.attr('src', thumbnail.data('placehold'));
		remover.prop('disabled', true);
		sizer.prop('disabled', true);
		value.prop('disabled', true);
		thumbnail_val.prop('disabled', true);
	});
	$('body').on('change', '.image-picker-allowed-size', function( e ){
		var clicked = $(this),
			panel = clicked.closest('.caldera-config-group').prev(),
			sizer = panel.find('.image-picker-sizer'),
			option = sizer.find('option[value="'+this.value+'"]'),
			bestoption = sizer.find('option').not(':disabled,option[value="'+this.value+'"]').first().val(),
			checks = clicked.closest('.caldera-config-field').find('input:checked'),
			buttons = panel.find('.image-picker-button');

			if(checks.length <= 0){
				clicked.prop('checked', true);
				return;
			}else if(checks.length === 1){
				buttons.addClass('image-picker-button-solo');
				sizer.hide();
			}else{
				buttons.removeClass('image-picker-button-solo');
				sizer.show();
			}

		if(!clicked.prop('checked')){			
			if(sizer.val() === clicked.val()){
				sizer.val(bestoption);
			}
			option.attr('disabled', 'disabled').hide();
		}else{
			option.removeAttr('disabled').show();
		}

	});

	$('body').on('change', '.image-picker-size', function( e ){

		var clicked = $(this),
			panel = clicked.closest('.caldera-config-field-setup').find('.image-picker-content'),
			thumbnail = panel.find('.image-picker-thumbnail');
		
		panel.removeClass('image-thumb').removeClass('image-thumb-lrg').addClass(clicked.val());


	});
})




