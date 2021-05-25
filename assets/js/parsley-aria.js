jQuery( function($) {
	/**
	 * Add accessibility attributes on invalid field
	 *
	 * @since 1.5.0.9
	 */
	window.Parsley.on('field:error', function() {
		this.$element.attr( 'aria-describedby', this._ui.errorsWrapperId ).attr( 'aria-invalid', 'true' );
		this._ui.$errorsWrapper.attr( 'aria-live', 'polite' );
	});

	/**
	 * If field is valid and was marked invalid, remove aria.invalid
	 *
	 * @since 1.5.0.9
	 */
	window.Parsley.on( 'field:success', function () {
		if( this.$element.attr( 'aria-invalid' ) ){
			this.$element.removeAttr( 'aria-invalid' );
		}
	});
});
