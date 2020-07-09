import * as calderaApiClient from '@caldera-labs/api-client';
import {cfAdmin} from "./cfAdmin";

/**
 * 1 instance of forms client
 *
 * @since 1.7.2
 *
 * @type {FormsClient}
 */
export const formsAdminApiClient = calderaApiClient.wpClientFactory(
	cfAdmin.rest.root,
	cfAdmin.rest.nonce,
	'forms'
);

/**
 * 1 instance of privacy settings client
 *
 * @since 1.7.2
 *
 * @type {PrivacySettingsClient}
 */
export const privacySettingsClient = calderaApiClient.wpClientFactory(
	cfAdmin.rest.root,
	cfAdmin.rest.nonce,
	'privacy'
);

function removeForwardSlash(endpoint) {
	if ('' !== endpoint && '/' === endpoint.charAt(0)) {
		endpoint = endpoint.substr(1);
	}
	return endpoint;
}

//If pretty permalinks are enabled params need to be prefixed with "?"
//Else there already is a "?" so we need to add a "&"
//@see https://github.com/CalderaWP/Caldera-Forms/pull/3576#issuecomment-655563315

/**
 * Change url string generation in client to prevent having two "?" in URL
 *
 * @see https://github.com/CalderaWP/Caldera-Forms/pull/3576#issuecomment-655563315
 * @since 1.9.2
 *
 * @param {{}} data
 * @param {string} endpoint
 * @returns {string}
 */
privacySettingsClient.urlString =  function(data, endpoint = ''){
	endpoint = removeForwardSlash(endpoint);
	let str = '';
	for (let key in data) {
		if (str !== '') {
			str += '&';
		}
		str += key + '=' + data[key];
	}
	const divider = this.route.indexOf('?' ) ? '&' : '?';
	if (endpoint) {
		let uri = `${this.route}/${endpoint}${divider}${str}`;
		return uri;
	}
	return this.route + divider + str;
}