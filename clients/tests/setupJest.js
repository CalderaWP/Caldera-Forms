global.wp = {
	shortcode: {

	},
	apiRequest: {

	}
};

Object.defineProperty( global.wp, 'element', {
	get: () => require( 'React' ),
} );
