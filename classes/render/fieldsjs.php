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
class Caldera_Forms_Render_FieldsJS implements JsonSerializable {

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
				if( method_exists( $this, $field[ 'type' ] ) ){
					call_user_func( array( $this, $field[ 'type' ] ), $field[ 'ID' ], $field );
				}
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function jsonSerialize() {
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
		$this->data[ $field_id ] = array(
			'type' => 'button',
			'id' => $this->field_id( $field_id )
		);
	}

	protected function wysiwyg( $field_id ){
		$options = $this->wysiqyg_options( $field_id );

		$this->data[ $field_id ] = array(
			'type' => 'wysiwyg',
			'id' => $this->field_id( $field_id ),
			'options' => $options
		);
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
		$this->data[ $field_id ] = array(
			'type' => 'phone_better',
			'id' => $this->field_id( $field_id ),
			'validator' => Caldera_Forms_Field_Util::better_phone_validator( $field_id, $this->form_count ),
			'options' => array(
				'autoHideDialCode' => false,
				'utilsScript' => CFCORE_URL . 'fields/phone_better/assets/js/utils.js'
			)
		);

		if( isset( $field[ 'config' ][ 'invalid_message' ] ) ){
			$this->data[ $field_id ][ 'options' ][ 'invalid' ] = $field[ 'config' ][ 'invalid_message' ];
		}else{
			$this->data[ $field_id ][ 'options' ][ 'invalid' ] = esc_html__( 'Invalid number', 'caldera-forms' );
		}


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
		/** @var Caldera_Forms_Field_SyncHTML $syncer */
		$syncer = Caldera_Forms_Field_Syncfactory::get_object( $this->form, $field, $id_attr );
		if ( $syncer->can_sync() ) {
			$this->data[ $field_id ] = array(
				'type'       => 'html',
				'id'         => $id_attr,
				'binds'      => $syncer->get_binds(),
				'sync'       => true,
				'tmplId'     => $syncer->template_id(),
				'contentId'  => $syncer->content_id(),
				'bindFields' => array(),
			);

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
		$this->data[ $field_id ] = array(
			'type' => 'range_slider',
			'id' => $this->field_id( $field_id ),
			'value' => 0,
		);

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

		$this->data[ $field_id ] = array(
			'type' => 'star_rating',
			'id' => $this->field_id( $field_id ),
			'options' => array(
				'starOn' => 'raty-'.  $type . '-on',
				'starOff' => 'raty-'.  $type . '-off',
				'spaceWidth' => $field['config']['space'],
				'number' => $field['config']['number'],
				'color' => $field['config']['color'],
				'cancel' => false,
				'single' => false,
				'target' => '#' . Caldera_Forms_Field_Util::star_target( $this->field_id( $field_id ) ),
				'targetKeep' => true,
				'targetType' => 'score',
				'score' => $field[ 'config' ][ 'default' ],
				'hints' => array( 1,2,3,4,5),
				'starType' => 'f',
				'starColor' => $field['config']['color'],
				'numberMax' => 100,

			)

		);

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

		$this->data[ $field_id ] = array(
			'type' => 'toggle_switch',
			'id' => $this->field_id( $field_id ),
			'selectedClassName' => $selectedClassName,
			'defaultClassName' => $defaultClassName,
			'options' => $options

		);
	}
}