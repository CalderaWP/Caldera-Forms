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
	}

	/**
	 * Setup JavaScript
	 *
	 * @since 1.4.5
	 */
	public function assets(){

		wp_enqueue_script( $this->script_handle, CFCORE_URL . 'assets/js/caldera-clippy.js', array( 'jquery' ), Caldera_Forms::VERSION );
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
			'api' => 'https://octaviabutler.caldera.space',
			'p1' => $this->randoms(),
			'p2' => $this->randoms(),
			'p3' => $this->randoms(),
			'url' => $this->url_hash,
			'l' => get_locale(),
			'fallback' => $this->fallback_clippy(),
			'template' => $this->template(),
			'email_clippy' => $this->email_clippy()
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
		return '<div class="caldera-forms-clippy-zone-inner-wrap"><div class="caldera-forms-clippy" ><h2>{{title.content}}</h2><p>{{content.content}}</p><a href="{{link.url}}" data-bt={{link.bt}} target="_blank" class="bt-btn btn btn-{{btn.color}}">{{ btn.content }}</a></div></div>';
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
			$clippy[ 'content' ][ 'content' ] = __( sprintf( 'Success rate for emails is %d percent of %s total emails', 100 * $stats[ 'success_rate' ], $stats[ 'total' ] ), 'caldera-forms' );
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
				'url' => 'https://calderaforms.com/getting-started?utm_source=obs&utm_campaign=admin-page&utm_medium=caldera-forms&utm_term=fallback'
			)
		);
	}

}