/**
 * Simple event bindings for form state
 *
 * In general, access through CFState.events() not directly.
 *
 * @since 1.5.3
 *
 * @param state {CFState} State object to subscribe to
 * @constructor
 */
function CFEvents(state) {
	var events = {};

	/**
	 * Attach an event (add_action)
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Any string, but generally input ID
	 * @param callback {Function} The callback function
	 */
	this.subscribe = function (id, callback) {

		if (!hasEvents(id)) {
			events[id] = [];
		}
		events[id].push(callback);
	};

	/**
	 * Trigger an event (do_action)
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Any string, but generally input ID
	 * @param value {*} The value to pass to callback
	 */
	this.trigger = function (id, value) {
		if (!hasEvents(id)) {
			return;
		}

		events[id].forEach(function (callback) {

			if(typeof value === 'undefined'){
				value = state.getState(id);
			}

			callback(id, value);
		});

	};

	this.emit = function (eventName, payload) {
		if (!hasEvents(eventName)) {
			return;
		}

		events[eventName].forEach(function (callback) {
			callback(payload,eventName);
		});

	};
	/**
	 * Detach a bound event (remove_action)
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Any string, but generally input ID
	 * @param callback {Function|null} The callback function you wish to detatch or null to detach all events.
	 */
	this.detach = function(id,callback){
		if( hasEvents(id)){
			if( null === callback ){
				delete events[id];
			}else{
				for (var key in events[id]) {
					if (callback === key) {
						events[id].splice(key, 1);
					}
				}
			}

		}
	};

	/**
	 * Check if there are events attatched to an identifier
	 *
	 * @since 1.5.23
	 *
	 *
	 * @param id {String} Identifying string
	 * @returns {boolean}
	 */
	function hasEvents(id) {
		return events.hasOwnProperty(id);
	}

}


