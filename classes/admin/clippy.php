<?php

/**
 * Sets up admin helpful messages
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Admin_Clippy {

	/**
	 * Handle for Javascript
	 *
	 * @since 1.4.5
	 *
	 * @var string
	 */
	protected $script_handle;

	/**
	 * Hashed version of URL to send
	 *
	 * @since 1.4.5
	 *
	 * @var string
	 */
	protected $url_hash;

	/**
	 * Title of extend clippy
	 *
	 * @since 1.5.3
	 *
	 * @var string
	 */
	protected $extend_title;
	/**
	 * Caldera_Forms_Admin_Clippy constructor.
	 *
	 * @since 1.4.5
	 *
	 * @param string $plugin_slug Plugin slug
	 * @param string $url Current site URL
	 */
	public function __construct( $plugin_slug, $url  ) {
		$this->script_handle = $plugin_slug . '-clippy';
		$this->url_hash      = md5( $url );
		add_action( 'admin_footer', array( $this, 'print_templates' ) );

	}

	/**
	 * Setup JavaScript
	 *
	 * @since 1.4.5
	 */
	public function assets(){

		Caldera_Forms_Render_Assets::enqueue_script( 'vue' );
		wp_enqueue_script( $this->script_handle, CFCORE_URL . 'assets/js/caldera-clippy.js', array( 'jquery', Caldera_Forms_Render_Assets::make_slug( 'vue' ) ), Caldera_Forms::VERSION );
		wp_localize_script( $this->script_handle, 'CF_CLIPPY', $this->localizer() );

	}

	/**
	 * Prepare data to localize
	 *
	 * @since 1.4.5
	 *
	 * @return array
	 */
	protected function localizer(){

		$data = array(
			'cfdotcom' => array(
				'api' => array(
					'search' => 'https://calderaforms.com/wp-json/wp/v2/doc?search=',
					'important' => 'https://calderaforms.com/wp-json/calderawp_api/v2/docs/important',
					'product' => $this->product_endpoint()
				),
			),
			'fallback' => $this->fallback_clippy(),
			'extend_title' => $this->extend_title
		);

		$forms = Caldera_Forms_Forms::get_forms();
		if( empty( $forms ) ){
			$data[ 'no_forms' ] = $this->create_form_clippy();
			unset( $data[ 'email_clippy' ] );
		}

		return $data;
	}

	/**
	 * Create random string of digits for p tag
	 *
	 * @since 1.4.5
	 *
	 * @param int $length Optional. Length of string. Default is 4.
	 *
	 * @return string
	 */
	protected function randoms( $length = 4 ){
		$str = '';
		for( $i = 0; $i <= $length; $i++ ){
			$str .= rand( 1,9 );
		}

		return $str;
	}


	/**
	 * Handlebars template for a clippy
	 *
	 * @since 1.4.5
	 *
	 * @return string
	 */
	protected function template(){
		/** Don't unmifiy this!! */
		return '<script type="text/html" id="tmpl--caldera-clippy"><div class="caldera-forms-clippy-zone-inner-wrap"><div class="caldera-forms-clippy" ><h2>{{title}}</h2><p>{{content}}</p><a href="{{link.url}}" target="_blank" class="bt-btn btn btn-organge">{{ btn.content }}</a></div></div></script>';
	}

	public function print_templates(){
		include CFCORE_PATH . 'ui/support/clippy_templates.php';
	}


	/**
	 *  Email stats clippy
	 *
	 * @since 1.4.5
	 *
	 * @return array
	 */
	protected function email_clippy(){
		$clippy = array(
			'title' => array(
				'content' => __( 'Email Stats', 'caldera-forms' ),
			),
			'content' => array(
				'content' => '',
			),
			'btn' => array(
				'content' => __( 'Email Resources', 'caldera-forms' ),
				'grey' => 'orange'
			),
			'link' => array(
				'url' => 'https://calderaforms.com/caldera-forms-emails?utm_source=obs&utm_campaign=admin-page&utm_medium=caldera-forms&utm_term=fallback',
				'bt' => 'email-stats'
			)
		);

		if( Caldera_Forms_Tracking::tracking_allowed() ){
			$stats = new Caldera_Forms_Email_TrackData();
			$stats = $stats->get_stats(  );
			$clippy[ 'content' ][ 'content' ] = sprintf( __( 'Success rate for emails is %d percent of %s total emails.', 'caldera-forms' ), 100 * $stats[ 'success_rate' ], $stats[ 'total' ] );
		}else{
			$url = Caldera_Forms_Tracking::allow_url();
			$clippy[ 'content' ][ 'content' ] = __( 'Enable usage tracking to get email stats.', 'caldera-forms' );
			$clippy[ 'link' ][ 'url' ] = add_query_arg( 'clippy', 1, $url );
			$clippy[ 'link' ][ 'bt' ] = 'tracking-optin';

		}

		return $clippy;
	}

	protected function create_form_clippy()
	{
		$clippy = array(
			'title' => array(
				'content' => __( 'Need Help Creating A Form?', 'caldera-forms' ),
			),
			'content' => array(
				'content' => 'Click the "New Form" button at the top of the page, or read or getting started guide for creating forms.',
			),
			'btn' => array(
				'content' => __( 'Read The Guide', 'caldera-forms' ),
				'color' => 'orange'
			),
			'link' => array(
				'url' => 'https://calderaforms.com/doc/creating-new-form?utm_source=obs&utm_campaign=admin-page&utm_medium=caldera-forms&utm_term=no_forms',
				'bt' => 'email-stats'
			)
		);

		return $clippy;
	}

	/**
	 * Fallback clippy with link to getting started guide for when the API can't br reached
	 *
	 * @since 1.4.5
	 *
	 * @return array
	 */
	protected function fallback_clippy(){
		return array(
			'title' => array(
				'content' => __( 'New To Caldera Forms?', 'caldera-forms' ),
			),
			'content' => array(
				'content' => __( 'We have a complete getting started guide for new users.', 'caldera-forms' ),
			),
			'btn' => array(
				'content' => __( 'Read Now', 'caldera-forms' ),
				'color' => 'orange'
			),
			'link' => array(
				'url' => 'https://calderaforms.com/getting-started?utm_source=obs&utm_campaign=clippy&utm_medium=caldera-forms&utm_term=fallback'
			)
		);
	}


	/**
	 * Get endpoint to use in products clippy
	 *
	 * @since 1.5.3
	 *
	 * @return string
	 */
	protected function product_endpoint(){
		$endpoints = array(
			'free' => 'https://calderaforms.com/wp-json/calderawp_api/v2/products/cf-addons?category=free',
			'featured' => 'https://calderaforms.com/wp-json/calderawp_api/v2/products/cf-addons?category=featured',
			'pro' => 'https://calderaforms.com/wp-json/calderawp_api/v2/products/64101'
		);
		$key = '_cf_clippy_first';
		$first_time = get_option( '_cf_clippy_first', 0 );

		if( 0 === $first_time ){
			update_option( $key, time(), false );
			return $endpoints[ 'free' ];
		}

		if ( function_exists( 'date_diff' ) ) {
			$date_diff = date_diff( DateTime::createFromFormat( 'U', $first_time ), DateTime::createFromFormat( 'U', time() ) );
		} else {
			$date_diff = new stdClass();
			$date_diff->d = rand( 5, 15 );
		}

		if( 10 > $date_diff->d ){
			$this->extend_title = __( 'Get A Free Add-on For Caldera Forms', 'caldera-forms' );
			return $endpoints[ 'free' ];
		}


		if( 1 == rand(1,10) ){
			$this->extend_title = __( 'Extend Caldera Forms With Our Add-ons', 'caldera-forms' );
			return $endpoints[ 'featured' ];
		}else{
			$this->extend_title = __( 'Have You Tried Caldera Forms Pro Yet?', 'caldera-forms' );
			return $endpoints[ 'pro' ];
		}

	}

}