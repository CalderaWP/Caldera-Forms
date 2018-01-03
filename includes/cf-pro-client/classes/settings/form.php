<?php


namespace calderawp\calderaforms\pro\settings;
use calderawp\calderaforms\pro\container;
use calderawp\calderaforms\pro\json_arrayable;
use calderawp\calderaforms\pro\settings;


/**
 * Class form
 *
 * Settings for an individual form
 *
 * @package calderawp\calderaforms\pro\settings
 */
class form extends json_arrayable {

	/**
	 * Allowed properites of this repo
	 *
	 * @since 0.0.1
	 *
	 * @var array
	 */
	protected $properties = array(
		'attach_pdf',
		'pdf_link',
		'layout',
		'pdf_layout',
		'send_local'
	);

	/**
	 * Values in the repo
	 *
	 * @since 0.0.1
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * @var settings
	 */
	protected $settings;

	/**
	 * Form ID for this setting
	 *
	 * @since 0.0.1
	 *
	 * @var string
	 */
	protected $form_id;

	/**
	 * Form config
	 *
	 * Don't use directly, lazy-load with $this->get_form()
	 *
	 * @since 0.5.0
	 *
	 * @var array
	 */
	protected $form;

	/**
	 * form constructor.
	 *
	 * @since 0.0.1
	 *
	 * @param string $form_id
	 */
	public function __construct(  $form_id ){

		$this->form_id = $form_id;
	}

	/**
	 * Factory to create from saved settings in database
	 *
	 * @since 0.0.1
	 *
	 * @param $form_id
	 *
	 * @return form|null
	 */
	public static function from_saved( $form_id ){
		$obj = new form( $form_id );
		$saved = get_option( $obj->option_key() );

		$obj->form_id = $form_id;
		if( is_array( $saved ) ){
			foreach ( $saved as $prop => $value ){
				$obj->$prop = $value;
			}

			return $obj;
		}

		return null;

	}

	/**
	 * Magic setter for allowed properties
	 *
	 * @since 0.0.1
	 *
	 * @param $name
	 * @param $value
	 *
	 * @return form
	 */
	public function __set( $name, $value ){
		if( array_key_exists( $name, array_flip( $this->properties )) ){
			$this->set_property( $name, $value );
		}

		return $this;
	}

	/**
	 * Save to database
	 *
	 * @since 0.0.1
	 */
	public function save(){
		update_option( $this->option_key(), $this->toArray(), 'no' );

	}

	/**
	 * @inheritdoc
	 * @since 0.0.1
	 */
	public function toArray(){
		$array = array();
		foreach ( $this->properties as $property ){
			$cb = 'get_' . $property;
			if( method_exists( $this, $cb   ) ){
				$array[ $property ] = $this->$cb();
			}else{
				$array[ $property ] = $this->get_property( $property, 'int' );
			}
		}
		$array[ 'form_id' ] = $this->form_id;
		$array[ 'name' ] = $this->get_name();
		return $array;
	}

	/**
	 * Get an array of properties -- settings we can save for form
	 *
	 * @since 0.0.1
	 *
	 * @return array
	 */
	public function get_properties(){
		return $this->properties;
	}



	/**
	 * Get form ID for this setting
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 */
	public function get_form_id(){
		return $this->form_id;
	}

	/**
	 * Getter for send_local setting in form
	 *
	 * @since 1.5.9
	 *
	 * @return bool
	 */
	public function get_send_local(){
		return $this->get_property( 'send_local' );
	}

	/**
	 * Getter for send_local setting
	 *
	 * @since 0.0.1
	 *
	 * @return bool
	 */
	public function should_send_local(){
		/**
		 * Change if an individual form should use local email system or not.
		 *
		 * Default is system value
		 *
		 * @param bool $enable Enabled. True to send local, false to send remote - if possible.
		 * @param string $form_id Form ID
		 *
		 */
		$_send_local = $this->get_send_local();
		if( $_send_local === true ) {
			return apply_filters( 'caldera_forms_pro_send_local', true, $this->form_id );
		} else {
			return apply_filters( 'caldera_forms_pro_send_local', container::get_instance()->get_settings()->send_local(), $this->form_id );
		}
	}

	/**
	 * Check if this form should attach PDFs to emails
	 *
	 * @since 0.0.1
	 *
	 * @return bool
	 */
	public function should_attatch_pdf(){
		return $this->get_attach_pdf();
	}
	/**
	 * Check if this form should add PDF links after submissions
	 *
	 * @since 0.0.1
	 *
	 * @return bool
	 */
	public function should_add_pdf_link(){
		return $this->get_property( 'pdf_link', 'bool' );
	}

	/**
	 * Getter for attatch_pdf setting
	 *
	 * @since 0.0.1
	 *
	 * @return bool
	 */
	public function get_attach_pdf(){
		return $this->get_property( 'attach_pdf'  );
	}

	/**
	 * Get PDF layout for form
	 *
	 * @since 0.0.1
	 *
	 * @return bool
	 */
	public function get_pdf_layout(){
		return $this->get_property( 'pdf_layout', 'int'  );

	}

	/**
	 * Getter for layout setting
	 *
	 * @since 0.0.1
	 *
	 * @return int
	 */
	public function get_layout(){
		return $this->get_property( 'layout' , 'int' );

	}

	/**
	 * Should we use HTML layout?
	 *
	 * @return bool
	 */
	public function use_html_layout(){
		if( ! container::get_instance()->get_settings()->is_basic() && ! 0 < $this->get_layout() ){
			return true;
		}

		return false;
	}

	/**
	 * Getter for form name
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 */
	public function get_name(){
		$form = $this->get_form();
		return isset( $form[ 'name' ] ) ? $form[ 'name' ] : $form[ 'ID'  ];
	}

	/**
	 * Get property from the rep0
	 *
	 * @since 0.0.1
	 *
	 * @param $prop
	 *
	 * @return bool
	 */
	protected function get_property( $prop, $cast = 'bool' ){
		if ( ! isset( $this->attributes[ $prop ] ) ) {
			$this->attributes[ $prop ] = false;
		}

		switch( $cast ) {
			case 'int' :
				return intval( $this->attributes[ $prop ] );
				break;
			default:
				return boolval( $this->attributes[ $prop ] );
				break;
		}
	}

	/**
	 * Set property in repo
	 *
	 * @since 0.0.1
	 *
	 * @param string $prop Property to set
	 * @param mixed $value Value to set
	 * @return bool|form
	 */
	protected function set_property( $prop, $value ){

		if( in_array( $prop, [
			'layout',
			'pdf_layout',
		]) ){
			$value = absint( $value );
		}else{
			$value = rest_sanitize_boolean( $value );
		}
		$this->attributes[ $prop ] = $value;

		return $this;
	}



	/**
	 * Option key to save in
	 *
	 * @since 0.0.1
	 *
	 * @return string
	 */
	protected function option_key(){
		return '_cf_pro_' . $this->form_id;
	}

	/**
	 * Getter for from property, acts as lazy-loader
	 *
	 * @since 0.5.0
	 *
	 * @return array
	 */
	protected function get_form(){
		if( empty( $this->form ) ){
			$this->form = \Caldera_Forms_Forms::get_form( $this->form_id );
		}
		return $this->form;
	}
}