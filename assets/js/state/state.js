/**
 * State management for front-end
 *
 * @since 1.5.3
 *
 * @param formId {String} ID of form this is tracking state for.
 * @param $ {jquery} jQuery
 *
 * @constructor
 */
function CFState(formId, $ ){

	var
		self = this,
		fields = {},
		events = new CFEvents(this),
		unBound = {},
		fieldVals  = {};


	/**
	 * Initialized ( or re-initialize) state with specific fields.
	 *
	 * @since 1.5.3
	 *
	 * @param formFields {Object} Should be flat field ID attribute : Field default
	 */
	this.init = function (formFields) {
		var $field,
			$el;
		for ( var id in formFields ){
			if( bindField(id)){
				fieldVals[id] = formFields[id];
			}else{
				fieldVals[id] = '';
				unBound[id] = true;
			}

		}

	};

	/**
	 * Get current state for a field
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Field id attribute
	 * @returns {String|Array}
	 */
	this.getState = function(id){
		if( ! inState(id) ){
			return false;
		}

		return fieldVals[id];
	};

	/**
	 * Change state for a field
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Field id attribute
	 * @param value {String|Array} New value
	 */
	this.mutateState = function(id, value ){

		if( ! inState(id) ){
			return false;
		}

		if( fieldVals[id] != value ){
			fieldVals[id] = value;
			events.trigger(id,value);
		}

		return true;
	};

	/**
	 * Unbind field -- used when hiding via conditional logic
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Field id attribute
	 */
	this.unbind = function(id){
		self.mutateState(id,'');
		unBound[id] = true;
	};

	/**
	 * Rebind field -- used when unhiding via conditional logic
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Field id attribute
	 */
	this.rebind = function(id){
		bindField(id);
		delete unBound[id];
	};

	/**
	 * Accessor for the CFEvents object used for this state
	 *
	 * @since 1.5.3
	 *
	 * @returns {{subscribe: subscribe, detach: detach}}
	 */
	this.events = function(){
		return {
			/**
			 * Attach an event to change of an input in the state
			 *
			 * @since 1.5.3
			 *
			 * @param id {String} Field ID attribute
			 * @param callback {Function} The callback function
			 */
			subscribe: function( id, callback ){
				if( inState(id)){
					events.subscribe(id,callback);

				}

			},
			/**
			 * Detach an event to change of an input in the state
			 *
			 * @since 1.5.3
			 *
			 * @param id {String} Field ID attribute
			 * @param callback {Function|null} The callback function. Pass null to detach all.
			 */
			detach: function(id,callback){
				events.detach(id,callback);
			}
		}
	};


	/**
	 * Check if value is tracked in state
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Field ID attribute
	 *
	 * @returns {boolean}
	 */
	function inState(id){
		return fieldVals.hasOwnProperty(id);
	}

	/**
	 * Bind a field's change events
	 *
	 * @since 1.5.3
	 *
	 * @param id {String}
	 * @returns {boolean}
	 */
	function bindField(id) {
		var $field = $('#' + id);
		if ($field.length) {
			$field.on('change keyup', function () {
				var $el = $(this);
				self.mutateState([$el.attr('id')],$el.val());
			});
			return true;
		} else {
			$field = $('.' + id);
			if ($field.length) {


				$field.on('change', function () {
					var val = [];
					var $el = $(this),
					 	id,
						$collection,
						type = $el.attr( 'type' );

					switch ( type ){
						case 'radio' :
							id = $el.data( 'radio-field' );
							$collection = $( '[data-radio-field=' + id +']' );
							val = '';
							break;
						case 'checkbox' :
							id = $el.data( 'checkbox-field' );
							$collection = $( '[data-checkbox-field=' + id +']' );
							break;
						default :
							id = $el.data( 'field' );
							$collection = $( '[data-field=' + id +']' );
							break;
					}


					$collection.each( function( i, el ){
						var $this = $( el );

						if( $this.prop( 'checked' ) ){
							if( 'radio' === type ){
								val = $this.val();
							}else{

								val.push($this.val());

							}
						}
					});


					self.mutateState(id,val);

				});
				return true;
			}


		}

		self.unbind(id);

		return false;

	}


}