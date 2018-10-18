import deepmerge from 'deepmerge';

/**
 * Provides CF Pro Local settings, with defaults merged to stored values.
 *
 * @param {Object} stored Stored values
 */
export const proLocalSettingsFactory = (stored = {} ) => {
	const defaults = {
		connected: false,
		generalSettings : {
			enhancedDelivery: false,
			generatePDFs: false,
			logLevel: 200
		},
		apiKeys: {
			public: '',
			secret: '',
			token: ''
		}

	};
	return deepmerge(
		defaults,
		stored,
	);


};