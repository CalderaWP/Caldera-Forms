import axios from 'axios';
import CFProConfig from './wpConfig';
import sha1 from  'locutus/php/strings/sha1';
export const objHasProp = function(obj,prop) {
    return Object.prototype.hasOwnProperty.call(obj, prop);
};
const timeout = 30000;
export const localAPI = axios.create({
	baseURL: CFProConfig.localApiURL,
	timeout: timeout,
	headers: {'X-WP-Nonce': CFProConfig.localApiNonce}
});

export const appAPI = axios.create({
	baseURL: CFProConfig.appURL,
	timeout: timeout,
});

export  const appToken = function (apiKeys) {
	if( objHasProp(apiKeys,'public') && objHasProp(apiKeys,'secret')){
		let publicKey = apiKeys.public;
		let secret = apiKeys.secret;
		return sha1( publicKey + secret);
	}
	return '';


};