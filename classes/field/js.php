<?php
/**
 * Prepares fieldjs config
 *
 * Will be placed in .cf-fieldjs-config for each field
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Field_JS implements JsonSerializable {

	/**
	 * Form config
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $form;

	/**
	 * Prepared field data
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $data;

	/**
	 * Form instance count
	 *
	 * @since 1.5.0
	 *
	 * @var int
	 */
	protected $form_count;

	/**
	 * Caldera_Forms_Render_FieldsJS constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param array $form Form config
	 * @param int $form_count Form instance count
	 */
	public function __construct( array $form, $form_count ) {
		$this->form = $form;
		$this->form_count = $form_count;
		$this->data = array();
	}

	/**
	 * Prepare data for each field
	 *
	 * @since 1.5.0
	 */
	public function prepare_data(){

		if( ! empty( $this->form[ 'fields' ] ) ){
			foreach( $this->form[ 'fields' ] as $field ){
				$type = Caldera_Forms_Field_Util::get_type( $field, $this->form );
				if( 'summary' == $type ){
					$type = 'html';
				}
				if( 'calculation' != $type && method_exists( $this, $type ) ){
					call_user_func( array( $this, $type ), $field[ 'ID' ], $field );
				}
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function jsonSerialize() {
		return $this->to_array();
	}

	/**
	 * Get array representation of this object
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public function to_array(){
		if( empty( $this->data ) ){
			$this->prepare_data();
		}

		return $this->get_data();
	}

	/**
	 * Get prepared data
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public function get_data(){
		return $this->data;
	}

	/**
	 * Callback for processing button data
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_id Field ID
	 */
	protected function button( $field_id ){
		$this->data[ $field_id ] = $this->create_config_array( $field_id, __FUNCTION__, array() );
	}

	protected function wysiwyg( $field_id ){


		$this->data[ $field_id ] = $this->create_config_array( $field_id, __FUNCTION__, array(
			'options' => $this->wysiqyg_options( $field_id )
		) );


	}

	/**
	 * @param $field_id
	 *
	 * @return string
	 */
	protected function field_id( $field_id ) {
		return Caldera_Forms_Field_Util::get_base_id( $field_id, $this->form_count, $this->form );
	}

	/**
	 * @param $field_id
	 *
	 * @return mixed|void
	 */
	protected function wysiqyg_options( $field_id ) {
		$field = $this->form[ 'fields' ][ $field_id ];
		$options = array();
		if( ! empty( $field[ 'config' ]['language' ] ) ){
			$options[ 'lang' ] = strip_tags( $field[ 'config' ]['language' ] );
		}

		/**
		 * Filter options passed to Trumbowyg when initializing the WYSIWYG editor
		 *
		 * @since 1.5.0
		 *
		 * @see https://alex-d.github.io/Trumbowyg/documentation.html#general
		 *
		 * @param array $options Options will be empty unless language was set in UI
		 * @param array $field Field config
		 * @param array $form Form Config
		 */
		$options = apply_filters( 'caldera_forms_wysiwyg_options', $options, $field, $this->form );

		return $options;
	}

	/**
	 * Setup better_phone fields
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_id Id of field
	 * @param array $field Field config
	 *
	 * @return void
	 */
	protected function phone_better( $field_id, $field ){
		$args =  array(
			'options' => $this->better_phone_field_js_options( $field ),
			'messages' => array(
				'generic' => __( 'Invalid number', 'caldera-forms' ),
				'1' => __( 'Invalid country code', 'caldera-forms' ),
				'4' => __( 'Not a number', 'caldera-forms' ),
				'3' => __( 'Number is too long', 'caldera-forms' ),
				'2' => __( 'Number is too short', 'calera-forms' )

			)
		);

		$this->data[ $field_id ] = $this->create_config_array( $field_id, __FUNCTION__, $args );



	}

	/**
	 * Prepare options for better phone fields
	 *
	 * @since 1.5.0
	 *
	 * @param array $field Field config
	 *
	 * @return array
	 */
	protected function better_phone_field_js_options( array  $field  ){
		$options = array(
			'initialCountry' => 'us',
			'autoHideDialCode' => false,
			'utilsScript' => CFCORE_URL . 'fields/phone_better/assets/js/utils.js',
			'preferredCountries' => array( 'us' )
		);

		if( empty( $field[ 'config' ][ 'nationalMode' ] ) ){
			$options[ 'nationalMode' ] = true;
			$options[ 'autoHideDialCode' ] = true;
		}else{
			$options[ 'nationalMode' ] = false;
			$options[ 'autoHideDialCode' ] = false;
		}

		/**
		 * Filter config options for better phone fields.
		 *
		 * This  will be passed in JavaScript to constructor for the jQuery plugin that powers these fields.
		 * @see https://github.com/jackocnr/intl-tel-input
		 *
		 * @since 1.5.0
		 *
		 * @param array $options Options to use for this field
		 * @param array $field Field config
		 * @param array $form config
		 *
		 */
		return apply_filters( 'caldera_forms_phone_js_options', $options, $field, $this->form );
	}


	/**
	 * Setup HTML fields
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_id Id of field
	 * @param array $field Field config
	 *
	 * @return void
	 */
	protected function html( $field_id, $field ){
		$id_attr = $this->field_id( $field_id );

		$this->data[ $field_id ] = $this->create_config_array( $field_id, Caldera_Forms_Field_Util::get_type( $field ), array(
			'sync' => false
		) );

		/** @var Caldera_Forms_Sync_HTML $syncer */
		$syncer = Caldera_Forms_Sync_Factory::get_object( $this->form, $field, $id_attr );

		if ( $syncer->can_sync() ) {
			$this->data[ $field_id ] = array_merge( $this->data[ $field_id ], array(
				'binds'      => $syncer->get_binds(),
				'sync'       => true,
				'tmplId'     => $syncer->template_id(),
				'contentId'  => $syncer->content_id(),
				'bindFields' => array(),
			) );

			foreach ( $syncer->get_binds() as $bind ){
				$this->data[ $field_id ][ 'bindFields' ][] = $bind . '_' . $this->form_count;
			}
		}

	}

	/**
	 * Setup range slider fields
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_id Id of field
	 * @param array $field Field config
	 * @return void
	 */
	public function range_slider( $field_id, $field ){


		$this->data[ $field_id ] = $this->create_config_array( $field_id, __FUNCTION__, array(
			'value' => 0,
		) );

		foreach( array(
			'handleborder',
			'trackcolor',
			'color',
			'handle',
		) as $setting ){
			if( isset( $field[ 'config'][ $setting ] ) ){
				$value = $field[ 'config'][ $setting ];
			}else{
				$value = '';
			}

			$this->data[ $field_id ][ $setting ] = $value;
		}

		if( false !== strpos( $field['config']['step'], '.' ) ) {
			$part = explode( '.', $field[ 'config' ][ 'step' ] );
			$this->data[ $field_id ][ 'value' ] = strlen( $part[1] );
		}


	}


	/**
	 * Setup star rate fields
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_id Id of field
	 * @param array $field Field config
	 * @return void
	 */
	protected function star_rating( $field_id, $field ){
		$type = $field['config']['type'];
		if ( ! isset( $field[ 'config' ][ 'track_color' ] ) ) {
			$field[ 'config' ][ 'track_color' ] = '#AFAFAF';
		}
		if ( ! isset( $field[ 'config' ][ 'type' ] ) ) {
			$field[ 'config' ][ 'type' ] = 'star';
		}

		$args = array(
			'starFieldId' => Caldera_Forms_Field_Util::star_target( Caldera_Forms_Field_Util::get_base_id( $field, $this->form_count, $this->form ) ),
			'options' => array(
				'starOn' => 'raty-'.  $type . '-on',
				'starOff' => 'raty-'.  $type . '-off',
				'spaceWidth' => $field['config']['space'],
				'number' => $field['config']['number'],
				'color' => $field['config']['color'],
				'cancel' => false,
				'single' => false,
				'targetScore' => '#' . $this->field_id( $field_id ),
				'targetKeep' => true,
				'targetType' => 'score',
				'score' => 0,
				'hints' => array( 1,2,3,4,5),
				'starType' => 'f',
				'starColor' => $field['config']['color'],
				'numberMax' => 100,
			)
		);

		if( ! empty( $field[ 'config' ][ 'default' ] ) && is_numeric( $field[ 'config' ][ 'default' ] ) ){
			$args[ 'options' ][ 'score' ] = $field[ 'config' ][ 'default' ];
		}

		$this->data[ $field_id ] = $this->create_config_array( $field_id, __FUNCTION__, $args );

		if(!empty($field['config']['cancel']) ){
			$this->data[ $field_id ][ 'options' ][ 'cancel' ] = true;
		}

		if( !empty($field['config']['single'] ) ){
			$this->data[ $field_id ][ 'options' ][ 'single' ] = true;
		}
	}

	protected function toggle_switch( $field_id, $field ){
		$selectedClassName = 'btn-success';
		if ( ! empty( $field[ 'config' ][ 'selected_class' ] ) ) {
			$selectedClassName = $field[ 'config' ][ 'selected_class' ];
		}

		$defaultClassName = 'btn-default';
		if ( ! empty( $field[ 'config' ][ 'default_class' ] ) ) {
			$defaultClassName = $field[ 'config' ][ 'default_class' ];
		}

		$options = array();
		foreach ( $field[ 'config' ][ 'option' ] as $option_key => $option ) {
			$options[] = $this->field_id( $field_id ) . '_' . $option_key;
		}

		$args = array(
			'selectedClassName' => $selectedClassName,
			'defaultClassName' => $defaultClassName,
			'options' => $options
		);

		$this->data[ $field_id ] = $this->create_config_array( $field_id, __FUNCTION__, $args );

	}

	/**
	 * For calculation fields
	 *
	 * NOTE: NOT USED AS OF 1.5
	 *
	 * @since 1.5.0
	 *
	 * @param $field_id
	 * @param $field
	 */
	protected function calculation( $field_id, $field ){
		if( !isset( $field['config']['thousand_separator'] ) ){
			$field['config']['thousand_separator'] = ',';
		}

		if( !isset( $field['config']['decimal_separator'] ) ){
			$field['config']['decimal_separator'] = '.';
		}

		$thousand_separator = $field['config']['thousand_separator'];
		$decimal_separator = $field['config']['decimal_separator'];
		/** @var Caldera_Forms_Sync_Calc $syncer */
		$syncer = Caldera_Forms_Sync_Factory::get_object( $this->form, $field, Caldera_Forms_Field_Util::get_base_id( $field, null, $this->form ) );

		//this creates binds array BTW
		$syncer->can_sync();
		$formula = $syncer->get_formula( true );
		$args = array(
			'formula' => $formula,
			'binds' => $syncer->get_binds(),
			'decimalSeparator' => $decimal_separator,
			'thousandSeparator' => $thousand_separator,
			'fixed' => false,
			'fieldBinds' => $syncer->get_bind_fields(),
		);

		$this->data[ $field_id ] = $this->create_config_array( $field_id, __FUNCTION__, $args );

		if(!empty($field['config']['fixed'])){
			$this->data[ $field_id ][ 'fixed' ] = true;
		}
	}

	/**
	 * Config for credit card number fields
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_id The field ID
	 * @param array $field The field config
	 */
	protected function credit_card_number( $field_id, $field ){

		$this->data[ $field_id ] = $this->create_config_array( $field_id, __FUNCTION__, array(
			'invalid' => __( 'Invalid Card Number', 'caldera-forms' ),
			'cvc' => false,
			'exp' => false,
		) );

		if( Caldera_Forms_Render_Assets::should_minify( false ) ){
			$this->data[ $field_id ][ 'imgPath' ] = CFCORE_URL . 'assets/build/images/';
		}else{
			$this->data[ $field_id ][ 'imgPath' ] = CFCORE_URL . 'assets/images/';
		}

		if( ! empty( $field[ 'config' ][ 'cvc'] ) ){
			$this->data[ $field_id ][ 'cvc' ] = $this->field_id( $field[ 'config' ][ 'cvc' ] );
		}

		if( ! empty( $field[ 'config' ][ 'exp'] ) ){
			$this->data[ $field_id ][ 'exp' ] = $this->field_id( $field[ 'config' ][ 'exp' ] );
		}

	}

	/**
	 * Config for credit card expiration fields
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_id The field ID
	 * @param array $field The field config
	 */
	protected function credit_card_exp( $field_id, $field ){

		$this->data[ $field_id ] = $this->create_config_array( $field_id, __FUNCTION__, array(
			'invalid' => __( 'Invalid Expiration Date', 'caldera-forms' ),
			'ccField' => false
		) );

		if( ! empty( $field[ 'config' ][ 'credit_card_field' ] ) ){
			$this->data[ $field_id ][ 'ccField'  ] = $this->field_id( $field[ 'config' ][ 'credit_card_field' ] );
		}


	}

	/**
	 * Config for credit card cvv fields
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_id The field ID
	 * @param array $field The field config
	 */
	protected function credit_card_cvc( $field_id, $field ){

		$this->data[ $field_id ] = $this->create_config_array( $field_id, __FUNCTION__, array(
			'invalid' => __( 'Invalid Security Code', 'caldera-forms' ),
			'ccField' => false
		) );

		if( ! empty( $field[ 'config' ][ 'credit_card_field' ] ) ){
			$this->data[ $field_id ][ 'ccField'  ] = $this->field_id( $field[ 'config' ][ 'credit_card_field' ] );
		}


	}



	/**
	 * Create config array
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_id Field ID
	 * @param string $type Field type
	 * @param array $args Additional data.
	 *
	 * @return array
	 */
	protected function create_config_array( $field_id, $type, $args ){
		$basic =  array(
			'type' => $type,
			'id' => $this->field_id( $field_id ),

		);
		return array_merge( $basic, wp_parse_args( $args, $this->default_config_args() ) );
	}

	protected function default_config_args(){
		/**
		 * Default values passed to field configs to be printed in DOM for field types
		 *
		 * Useful for customizing field setups in bulk
		 *
		 * @since 1.5.0
		 *
		 * @param array $args
		 */
		return apply_filters( 'caldera_forms_field_js_config_defaults', array(
			'form_id' => $this->form[ 'ID' ],
			'form_id_attr' => Caldera_Forms_Render_Util::field_id_attribute( $this->form_count )

		));
	}
}