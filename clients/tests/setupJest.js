import React from 'react';
import jquery from 'jquery';

global.wp = {
	shortcode: {

	},
	apiRequest: {

	}
};

Object.defineProperty( global.wp, 'element', {
	get: () => React,
} );

/**
 * Setup fetch mocking
 * @link https://www.npmjs.com/package/jest-fetch-mock#usage
 */
global.fetch = require('jest-fetch-mock');

/**
 * Setup jQuery
 */
global.$ = global.jQuery = jquery;