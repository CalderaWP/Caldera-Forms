import axios from 'axios';
import CFProConfig from './wpConfig';
import sha1 from  'locutus/php/strings/sha1';
import { objHasProp, hasProp } from './utils'

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
	if( hasProp ( apiKeys,'token' ) ) {
		return apiKeys.token;
	}else if( objHasProp(apiKeys,'public') && objHasProp(apiKeys,'secret')){
		return sha1(apiKeys.public . apiKeys.secret);
	}
	return '';


};