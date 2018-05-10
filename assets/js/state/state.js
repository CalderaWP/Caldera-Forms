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
		fieldVals  = {},
		calcVals = {};


	/**
	 * Initialized ( or re-initialize) state with specific fields.
	 *
	 * @since 1.5.3
	 *
	 * @param formFields {Object} Should be flat field ID attribute : Field default
	 */
	this.init = function (formFields, calcDefaults) {

		for ( var id in formFields ){
			if( 'object' === typeof  calcDefaults[id] ){
				if( 'calculation' == calcDefaults[id].type ){
					bindCalcField(id,calcDefaults[id])
				}

			}else if( bindField(id)){
				fieldVals[id] = formFields[id];
				if( calcDefaults.hasOwnProperty(id) ){
					calcVals[id] = calcDefaults[id];
				}else{
					calcVals[id] = null;
				}
			}else{
				fieldVals[id] = '';
				unBound[id] = true;
				calcVals[id] = null;
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
	 *Get calculation value for a field
	 *
	 * @since 1.5.6
	 *
	 * @param id {String} Field id attribute
	 * @param highest {Boolean}
	 * @returns {float}
	 */
	this.getCalcValue = function (id,highest) {
		var val = 0;

		if (! inState( id )) {
			return val;
		}

		if( highest ){
			highest = 0;
			var value = highest,
				$item;
			$( '#' + id ).each(function(){
				value = 0;
				$item = $( this );
				if(  $item.prop('checked' ) ){
					value = findCalcVal( $item );
					if( parseFloat( value ) > parseFloat( highest ) ){
						highest = parseFloat( value );
					}
				}

			});
			return parseFloat( highest );
		}

		if (calcVals.hasOwnProperty(id) ) {
			if( false === calcVals[id] || null === calcVals[id] ){
				//@TODO use let here, when ES6.
				var _val = findCalcVal( $( document.getElementById( id ) ) );
				if( isString( _val )  ) {
					_val = parseFloat( _val );
				}

				if( isNumber( _val ) ){
					calcVals[id] = _val;
				}
			}

			val = calcVals[id];
		} else {
			val = self.getState(id);

			if ($.isArray(val)) {
				val = val.reduce( function ( a, b) {
					return parseFloat( a ) + parseFloat( b );
				}, 0);
			}

			if( isNumber( val ) ){
				calcVals[id] = val;
			}
		}

		return parseFloat( val );
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
		delete calcVals[id];
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
	 * @param {String} id
	 * @returns {boolean}
	 */
	function bindField(id) {
		var $field = $('#' + id);
		if ($field.length) {
			$field.on('change keyup', function () {
				var $el = $(this);
				calcVals[$el.attr('id')] = findCalcVal( $el );
				self.mutateState([$el.attr('id')],$el.val());
			});
			calcVals[id] = findCalcVal( $( document.getElementById( id ) ) );
			self.mutateState([$field.attr('id')],$field.val());

			return true;
		} else {
			$field = $('.' + id);
			if ($field.length) {

                                //Rebind checkbox options when the checkbow field is unhidden
                                    if( 'object' == typeof  $field  ){
                                        var val = [];
                                        var allSums = 0;
                                        $field.each(function ( i, el ) {
                                            var $this = $(el);
                                            var sum = 0;
                                            if ($this.prop('checked')) {
                                                sum += parseFloat(findCalcVal($this));
                                                allSums += sum;
                                                val.push($this.val());
                                            }
                                            calcVals[id] = allSums;
                                        });
                                    }


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

					if ( 'checkbox' === type ) {
						var $v, sum = 0;
						if ( $collection.length ) {
							$collection.each(function (k, v) {
								$v = $(v);
								if ($v.prop('checked')) {
									sum += parseFloat(findCalcVal($v));
									val.push($v.val());
								}
							});
						}else{
							val = [];
						}

						calcVals[id] = sum;
					} else if( ! $collection.length ){
						val = 0;

					} else if ( 1 == $collection.length ){
						val = findCalcVal( $($collection[0]));
					} else{
						$collection.each(function (i, el) {
							var $this = $(el);

							if ($this.prop('checked')) {
								if ('radio' === type) {
									calcVals[id] = findCalcVal($this);
									val = $this.val();
								} else {
									val.push($this.val());
								}
							}
						});
					}


					self.mutateState(id,val);

				});
				return true;
			}


		}

		self.unbind(id);

		return false;

	}

	/**
	 * Bind change on a calculation field so that when state changes, calc value changes with it.
	 *
	 * @since 1.5.6.2
	 *
	 * @param {String} id
	 * @param {Object} config
	 */
	function bindCalcField(id,config) {
		fieldVals[id] = 0;
		calcVals[id] = 0;
		self.events().subscribe(id,function (value,id) {
			calcVals[id] = value;
		});
	}

	/**
	 * Find calculation value for an element
	 *
	 * @since 1.5.6
	 * @param {jQuery} $field
	 * @returns {float}
	 */
	function findCalcVal( $field ) {
		if( $field.is( 'select' ) && $field.has( 'option' ) ){
			$field = $field.find(':selected');
		}

		if( ! $field.length ){
			return 0;
		}

		if( $field.is( 'hidden' ) ){
			return $field.val();
		}

		var val = 0;

		var attr = $field.attr('data-calc-value');

		if (typeof attr !== typeof undefined && attr !== false && ! isNaN(attr)) {
			val = $field.data( 'calc-value' );
		}else{
			val = $field.val();
		}

		return parseFloat(val);
	}

	/**
	 * Parse float if we can parse float, else 0.
	 *
	 * @since 1.5.6
	 *
	 * @param number
	 * @returns {*}
	 */
	function parseFloat( number ) {
		if( ! number || isNaN( number) ){
			return 0.0;
		}
		return window.parseFloat( number );
	}

	/**
	 * Parse integer if we can parse integer, else 0.
	 *
	 * @since 1.5.6
	 *
	 * @param number
	 * @returns {*}
	 */
	function parseInt( number ) {
		if( ! number || isNaN( number) ){
			return 0;
		}
		return window.parseInt( number );
	}


	/**
	 * Determine if a value is a Number
	 *
	 * @since 1.5.6.2
	 *
	 * Copied from axios/lib/utils.js
	 *
	 * @param {Object} val The value to test
	 * @returns {boolean} True if value is a Number, otherwise false
	 */
	function isNumber(val) {
		return typeof val === 'number';
	}

	/**
	 * Determine if a value is a String
	 *
	 * @since 1.5.6.2
	 *
	 * Copied from axios/lib/utils.js

	 * @param {Object} val The value to test
	 * @returns {boolean} True if value is a String, otherwise false
	 */
	function isString(val) {
		return typeof val === 'string';
	}



}