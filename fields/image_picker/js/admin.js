// Uploading files
var image_picker_frame, cu_use_uploaded_image;

cu_use_uploaded_image = function(obj){

	// check for error
	if(obj.data.error){
		alert(obj.data.error);
		obj.params.trigger.prop('disabled', true);
		return;
	}

	var parent = jQuery('#' + obj.params.trigger.data('id')),
		image = parent.find('.image-picker-thumbnail'),
		id = parent.find('.image-picker-image-id'),
		thumb = parent.find('.image-picker-image-thumb');

		image.attr('src', obj.data.url);
		id.val(obj.data.ID);
		thumb.val(obj.data.url);
		obj.params.trigger.prop('disabled', true);
}

jQuery(function($){

	var media = wp.media, l10n, cu_media;

	cu_media = wp.Caldera = function( attributes ) {
		var MediaFrame = media.view.MediaFrame,
			frame = new MediaFrame.Caldera( attributes );
			media.frame = frame;
			return frame;
	}
	// Link any localized strings.
	l10n = media.view.l10n = typeof _wpMediaViewsL10n === 'undefined' ? {} : _wpMediaViewsL10n;

	// define the caldera frame
	media.view.MediaFrame.Caldera = media.view.MediaFrame.Select.extend({
		initialize: function() {
			/**
			 * call 'initialize' directly on the parent class
			 */
			media.view.MediaFrame.prototype.initialize.apply( this, arguments );

			_.defaults( this.options, {
				selection: [],
				library:   {},
				multiple:  false,
				state:    'library'
			});

			this.createSelection();
			this.createStates();
			this.bindHandlers();
		},

		createSelection: function() {
			var selection = this.options.selection;

			if ( ! (selection instanceof media.model.Selection) ) {
				this.options.selection = new media.model.Selection( selection, {
					multiple: this.options.multiple
				});
			}

			this._selection = {
				attachments: new media.model.Attachments(),
				difference: []
			};
		},

		createStates: function() {
			var options = this.options;

			if ( this.options.states ) {
				return;
			}

			// Add the default states.
			this.states.add([
				// Main states.
				new media.controller.Library({
					library:   media.query( options.library ),
					multiple:  options.multiple,
					title:     options.title,
					priority:  20,
					editable:   true//toolbar:    'main-insert'					
				}),
				//new media.controller.EditImage( { model: options.editImage } )
			]);
		},

		bindHandlers: function() {
			this.on( 'router:create:browse', this.createRouter, this );
			this.on( 'router:render:browse', this.browseRouter, this );
			this.on( 'content:create:browse', this.browseContent, this );
			this.on( 'content:render:upload', this.uploadContent, this );
			//this.on( 'content:render:edit-image', this.editImageContent, this );
			this.on( 'toolbar:create:select', this.createSelectToolbar, this );			
		},

		// Routers
		browseRouter: function( view ) {
			view.set({
				upload: {
					text:     l10n.uploadFilesTitle,
					priority: 20
				},
				browse: {
					text:     l10n.mediaLibraryTitle,
					priority: 40
				}
			});
		},

		/**
		 * Content
		 *
		 * @param {Object} content
		 * @this wp.media.controller.Region
		 */
		browseContent: function( content ) {
			var state = this.state();

			this.$el.removeClass('hide-toolbar');

			// Browse our library of attachments.
			content.view = new media.view.AttachmentsBrowser({
				controller: this,
				collection: state.get('library'),
				selection:  state.get('selection'),
				model:      state,
				sortable:   state.get('sortable'),
				search:     state.get('searchable'),
				filters:    state.get('filterable'),
				display:    state.get('displaySettings'),
				dragInfo:   state.get('dragInfo'),

				suggestedWidth:  state.get('suggestedWidth'),
				suggestedHeight: state.get('suggestedHeight'),

				AttachmentView: state.get('AttachmentView')
			});
		},

		/**
		 *
		 * @this wp.media.controller.Region
		 */
		uploadContent: function() {
			this.$el.removeClass('hide-toolbar');
			this.content.set( new media.view.UploaderInline({
				controller: this
			}) );
		},

		/**
		 * Toolbars
		 *
		 * @param {Object} toolbar
		 * @param {Object} [options={}]
		 * @this wp.media.controller.Region
		 */
		createSelectToolbar: function( toolbar, options ) {
			options = options || this.options.button || {};
			options.controller = this;

			toolbar.view = new media.view.Toolbar.Select( options );
		},


		editImageContent: function() {
			var image = this.state().get('image'),
				view = new media.view.EditImage( { model: image, controller: this } ).render();

			this.content.set( view );

			// after creating the wrapper view, load the actual editor via an ajax call
			view.loadEditor();

		}
	});



	$('body').on('click', '.cu-image-picker,.cu-image-picker-select', function( e ){
		
		e.preventDefault();
		var clicked = $(this),
			panel = clicked.closest('.caldera-config-group'),
			thumbnail = panel.find('.image-picker-thumbnail'),
			thumbnail_val = panel.find('.image-picker-image-thumb'),
			sizer = panel.find('.image-picker-sizer'),
			value = panel.find('.image-picker-image-id'),
			picksize = panel.find('.image-picker-content'),
			remover = panel.find('.cu-image-remover');
		
		if(clicked.hasClass('cu-image-picker-select')){
			clicked.next().prop('disabled', false).trigger('click');
			return;
		}

		if ( !image_picker_frame ) {

			// Create the media frame.

			image_picker_frame = cu_media({
				title: clicked.data( 'title' ),
				button: {
					text: clicked.data( 'button' ),
				},
				library: { type: 'image'},
				multiple: false
			});
		}
		var select_handler = function(e){
			attachment = image_picker_frame.state().get('selection').first().toJSON();
			sizer.prop('disabled', false);
			value.prop('disabled', false);
			value.val(attachment.id);
			thumbnail_val.prop('disabled', false);
			console.log(thumbnail_val);
			if(picksize.hasClass('image-thumb-lrg')){
				if(attachment.sizes.large){
					thumbnail.attr('src', attachment.sizes.large.url);
					thumbnail_val.val(attachment.sizes.large.url);
				}else if(attachment.sizes.medium){
					thumbnail.attr('src', attachment.sizes.medium.url);
					thumbnail_val.val(attachment.sizes.medium.url);
				}else{
					thumbnail.attr('src', attachment.sizes.full.url);
					thumbnail_val.val(attachment.sizes.full.url);
				}
			}else{
				thumbnail.attr('src', attachment.sizes.thumbnail.url);
				thumbnail_val.val(attachment.sizes.thumbnail.url);
			}			
			remover.prop('disabled', false);
			image_picker_frame.off( 'select', select_handler);
		};
		image_picker_frame.on( 'select', select_handler);

		image_picker_frame.open();
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
