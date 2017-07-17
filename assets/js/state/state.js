function CFState() {

	//Important, state variable should always be modified through setState()
	var
		self = this,
		state = {},
		els = {},
		defaults = {},
		events = new CFEvents(this);

	/**
	 * Initialize state from fields
	 *
	 * @since 1.5.3
	 *
	 * @param inputAndSelectFields {Array} Array of field IDs for fields that are not checkboxes or radios or other types of multi-input fields.
	 * @param groupFields {Array} Array of field IDs for fields that are checkboxes or radios or other types of multi-input fields.
	 * @param fieldDefaults {object}
	 */
	this.init = function ( inputAndSelectFields, groupFields, fieldDefaults ) {

		inputAndSelectFields.forEach(function (id) {
			defaults[id] = {
				value: fieldDefaults.hasOwnProperty( 'id' ) ? fieldDefaults.id : '',
				type: 'input'
			};
			addInput(id);
		});
		groupFields.forEach(function (id) {
			addGroup(id);
			defaults[id] = {
				value: fieldDefaults.hasOwnProperty( 'id' ) ? fieldDefaults.id : '',
				type: 'group'
			};
		});


	};

	/**
	 * Change a fields state
	 * 
	 * Will trigger bound events if the new value is not the same as the old value
	 * 
	 * @since 1.5.3
	 * 
	 * @param id {String} Field ID attribute
	 * @param value
	 * @returns {boolean}
	 */
	this.mutateState = function (id, value) {
		if ( ! inState(id) && ! maybeAddLate(id) ) {
			return false;
		}

		if (state[id] !== value) {
			setState(id, value);
			events.trigger(id, value);

		}
		
		return true;
	};

	/**
	 * Get a field's current value
	 * 
	 * @since 1.5.3
	 *
	 * @param id {String} Field ID attribute
	 * @returns {*}
	 */
	this.getState = function (id) {
		if ( ! inState(id) && ! maybeAddLate(id) ) {
			return false;
		}


		return state[id].value;
	};

	/**
	 * Get a field's current calcualtion value
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Field ID attribute
	 * @returns {*}
	 */
	this.getCalcValue = function (id) {
		if (!inState(id)) {
			return false;
		}

		return getCalcValue(els[id]);
	};


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
	 * Add a group field to state tracking
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Field ID attribute
	 */
	function addGroup(id){
		if (inState(id) ){
			return;
		}
		var el = document.getElementById(id + '-wrap');
		if( null != el ){
			els[id] = {};
			group = el.getElementsByTagName('input');
			if( group.length ){
				var inputId,
					type = group[0].type,
					initalValue = 'checkbox' == type ? [] : '';

				for( var i = 0; i <= group.length; i++ ){
					if( group[i] ){
						inputId = group[i].getAttribute('id');
						els[id][inputId] = group[i];
						if( els[id][inputId].checked ){
							if( 'checkbox' == type ){
								initalValue.push(getValue(els[id][inputId]));
							}else{
								initalValue = getValue([id][inputId]);
							}
						}

						els[id][inputId].onchange = function (e) {
							var inputId = this.getAttribute('id');
							if( 'checkbox' === els[id][inputId].type ){

								var newValue = [];
								for( var i in els[id] ){
									if( els[id][i].checked ){
										newValue.push(getValue(els[id][i]));
									}
								}
								self.mutateState(id,newValue);


							}else{
								self.mutateState(id, getValue(this));
							}
						};

					}

				}

			}

			addToState(id,initalValue);

		}

	};


	/**
	 * Add a input (non-group) field to state tracking
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Field ID attribute
	 */
	function addInput(id) {
		if (inState(id) ){
			return;
		}

		els[id] = document.getElementById(id);

		if (null != els[id]) {
			//for calculation field, get the hidden field, not display
			if (els[id].hasAttribute('data-calc-display')) {
				var _id = els[id].getAttribute('data-calc-display');
				els[id] = document.getElementById(_id);
			}

			if ('INPUT' === els[id].nodeName) {

				els[id].oninput = function (e) {
					self.mutateState(id, getValue(els[id]));
				};

			}

			els[id].onchange = function (e) {
				self.mutateState(id, getValue(els[id]));
			};



			addToState(id,getValue(els[id]));


		}
	}

	/**
	 * Add a field to the state late
	 *
	 * Needed if field was not in DOM (ie removed by condititonals when init() ran
	 *
	 * @since 1.5.3
	 *
	 * @param id {String}
	 * @returns {boolean}
	 */
	function maybeAddLate(id){
		if( defaults.hasOwnProperty( id ) ){
			if( 'group' == defaults[id].type ) {
				addGroup(id);
			}else{
				addInput(id);
			}

			return true;

		}

		return false;
	}


	/**
	 * Set state for field
	 *
	 * Used internally to change state - don't ever access state property directly.
	 *
	 * this.mutateState() is the public access method
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Field ID attribute
	 * @param newValue New value to set
	 */
	function setState(id, newValue) {
		if(inState(id)){
			state[id].value=newValue;
		}

	}

	/**
	 * Whitelists an ID to be tracked in state
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Field ID attribute
	 * @param initalValue Initial value to set
	 */
	function addToState(id, initalValue){
		if( inState(id)){
			return false;
		}

		state[id] = {
			value:initalValue
		}
	}

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
		return state.hasOwnProperty(id);
	}

	/**
	 * Get value from DOM node or other object with value property
	 *
	 * @since 1.5.3
	 *
	 *  @param el {Object} DOM node or other object with value property
	 */
	function getValue(el){
		if( 'object' !== typeof  el || ! el.hasOwnProperty( 'value' ) ) {
			return '';
		}

		return el.value;
	}

	/**
	 * Get calculation value from DOM node or comatible property
	 *
	 * @since 1.5.3
	 *
	 *  @param el {Object} DOM node or other compatible object
	 */
	function getCalcValue(el){
		if( 'object' !== typeof  el || ! el.hasOwnProperty( 'value' ) ) {
			return '';
		}

		return el.hasAttribute( 'data-calc-val' ) ? el.getAttribute( 'data-calc-value' ) : el.value;
	}



}