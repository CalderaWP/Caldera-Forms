<?php
/**
 * Abstraction for creating data to pass to wp_localize_script when working with Caldera Forms REST API
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
class Caldera_Forms_API_JsConfig implements JsonSerializable{

	/**
	 * Form config
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $form;

	/**
	 * Data to return
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Caldera_Forms_API_JsConfig constructor.
	 *
	 * @param array $form Optional, form config with tying to a specific form
	 */
	public function __construct( array  $form = array() ) {
		$this->form = $form;
	}

	/**
	 * @inheritdoc
	 * @since 1.5.0
	 */
	public function jsonSerialize() {
		return $this->toArray();
	}

	/**
	 * Get data as array
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public function toArray(){
		if ( empty( $this->data ) ) {
			$this->set_data();
		}

		return $this->data;
	}

	/**
	 * Setup data to localize
	 *
	 * @since 1.5.0
	 */
	protected function set_data(){

		$this->data = array(
			'dateFormat' => Caldera_Forms::time_format(),
			'api' => array(
				'root'    => esc_url( trailingslashit( Caldera_Forms_API_Util::url() ) ),
				'form'    => esc_url( trailingslashit( Caldera_Forms_API_Util::url( 'forms' ) ) ),
				'entries' => esc_url( trailingslashit( Caldera_Forms_API_Util::url( 'entries' ) ) ),
				'entrySettings' => esc_url( trailingslashit( Caldera_Forms_API_Util::url( 'settings/entries' )  ) ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
				'token'   => 'false',
			),
		);

		if( ! empty( $this->form ) ){
			$this->data[ 'formId' ] = $this->form[ 'ID' ];
		}

		/**
		 * Modify data to localize for API
		 *
		 * @since 1.5.0
		 *
		 * @param array $data The data
		 * @param array|null $form Form config if used, might be empty
		 */
		$this->data = apply_filters( 'caldera_forms_api_js_config', $this->data, $this->form );

	}
}