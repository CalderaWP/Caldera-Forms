import debounce from 'lodash.debounce';
export const accountSaver = store => {
	store.subscribe((mutation, state) => {
		const type = mutation.type;
		switch (type) {
			case 'apiKeys' :
			case 'secretKey':
			case 'publicKey':
				console.log(state.connected, state.account.apiKeys.secret, state.account.apiKeys.public);

				if (!state.connected && state.account.apiKeys.secret && state.account.apiKeys.public) {
                    console.log(state.connected, state.account.apiKeys.secret, state.account.apiKeys.public);

                    store.dispatch('testConnection');
					store.dispatch('getLayouts');
				} else if (!state.account.apiKeys.public || !state.account.apiKeys.secret) {
					store.commit('connected', 0);
				} else {
					store.commit('connected', 0);
				}
				break;
			case  'connected' :
				if (state.connected) {
					store.dispatch('getLayouts')
				}
				break;
		}

	})
};

/**
 * Plugin to save account when form settings are changed
 *
 * @since 1.0.0
 *
 * @param {Object} store
 */
export const formSaver = store => {
	/**
	 * Debounced version of saveAccount() mutation
	 * @since 1.0.0
	 *
	 * @type {Function}
	 */
	this.debounedFormMutation = debounce(
		function(){
			store.dispatch( 'saveAccount' );
		}, 100
	);

	/**
	 * When form setting is mutated trigger update
	 *
	 * @since 1.0.0
	 */
	store.subscribe((mutation, state) => {
		if( 'form' === mutation.type ){
			this.debounedFormMutation();
		}

	})
};




