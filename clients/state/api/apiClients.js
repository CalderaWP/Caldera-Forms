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





/**
 * Change url string generation in client to prevent having two "?" in URL
 *
 * @see https://github.com/CalderaWP/Caldera-Forms/pull/3576#issuecomment-655563315
 * @since 1.9.2
 */
privacySettingsClient.urlString = urlString;
formsAdminApiClient.urlString = urlString;

function urlString(data, endpoint = ''){
	function removeForwardSlash(endpoint) {
		if ('' !== endpoint && '/' === endpoint.charAt(0)) {
			endpoint = endpoint.substr(1);
		}
		return endpoint;
	}

	endpoint = removeForwardSlash(endpoint);
	let str = '';
	for (let key in data) {
		if (str !== '') {
			str += '&';
		}
		str += key + '=' + data[key];
	}
	const divider =  -1 !== this.route.indexOf('?')  ? '&' : '?';
	if (endpoint) {
		let uri = `${this.route}/${endpoint}${divider}${str}`;
		return uri;
	}
	return this.route + divider + str;
}