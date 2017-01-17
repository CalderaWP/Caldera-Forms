<?php

/**
 * Magic tag sync implementation for Calculation fields
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Sync_Calc  extends Caldera_Forms_Sync_Sync {


	protected  $formula;


	protected $bind_fields;

	/**
	 * Get formula
	 *
	 * @sing 1.5.0
	 *
	 * @param bool $js Optional. If true, the default, JavaScript version is returned
	 *
	 * @return string
	 */
	public function get_formula( $js = true ){
		$this->formula = $this->find_base_formula();
		if( $js ){
			return $this->setup_javascript_formula();

		}

		return $this->formula;
	}

	/**
	 *  Get an array of fields with ID and ID attr to bind to change events of
	 *
	 * @since 1.5.0
	 *
	 * @return mixed
	 */
	public function get_bind_fields(){
		if ( is_array( $this->bind_fields ) ) {
			return $this->bind_fields;
		}

		return array();
	}

	/**
	 * Create actual JavaScript formula
	 *
	 * @since 1.5.0
	 *
	 * @param string $formula
	 *
	 * @return mixed
	 */
	protected function convert_to_js( $formula ){
		if (  ! empty( $formula ) ) {
			foreach ( $this->binds as $field_id ) {
				$formula = $this->field_id_to_js( $field_id, $formula );
			}

		}

		return $formula;
	}

	/**
	 * Replace field ID with JavaScript to get value of that field
	 *
	 * @since 1.5.0
	 *
	 * @param string $field_id Field ID to replace.
	 * @param string $formula Formulat to replace in.
	 *
	 * @return string
	 */
	protected function field_id_to_js( $field_id, $formula ){
		$field = Caldera_Forms_Field_Util::get_field( $field_id, $this->form );
		$type = Caldera_Forms_Field_Util::get_type( $field, $this->form );
		switch( $type ) {
			case 'checkbox' :
				$js = 8;
				break;
			default:
				$js = '$( document.getElementById( ' . Caldera_Forms_Field_Util::get_base_id( $field, null, $this->form ) . ' ) ).val() ';
				break;
		}

		return str_replace( $field_id, $js, $formula );

	}
	/**
	 * Find the magic tags applicable to this field
	 *
	 * @since 1.5.0
	 */
	protected function find_tags(){
		preg_match_all("/%(.+?)%/", $this->find_base_formula(), $this->tags );
	}


	/**
	 * Get base formula
	 *
	 * @since 1.5.0
	 *
	 * @return mixed
	 */
	protected function find_base_formula(){
		if( $this->is_manual() ){
			return $this->field[ 'config' ][ 'manual_formula' ];
		}else{
			return $this->field[ 'config' ][ 'formular' ];
		}
	}

	/**
	 * Is this a manual formula?
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	protected function is_manual(){
		if( ! empty( $this->field[ 'config' ][ 'manual' ])){
			return true;
		}

		return false;

	}


	/**
	 * @inheritdoc
	 */
	protected function handle_match( $key_id, $tag_key ) {
		$this->add_bind( $key_id );
		$this->bind_fields[ $key_id ] = array(
			'id' => $key_id,
			'id_attr' => Caldera_Forms_Field_Util::get_base_id( $key_id, Caldera_Forms_Render_Util::get_current_form_count(), $this->form )
		);


	}

	/**
	 * Sets up JavaScript formula
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	protected function setup_javascript_formula() {
		$this->bind_fields = array();
		$formula           = $this->formula;
		if ( $this->is_manual() ) {

			if ( ! empty( $this->tags[ 1 ] ) ) {
				$this->binds = array();
				foreach ( $this->tags[ 1 ] as $tag_key => $tag ) {
					foreach ( $this->form[ 'fields' ] as $key_id => $fcfg ) {
						if ( $fcfg[ 'slug' ] === $tag ) {

							$this->handle_match( $key_id, $tag_key );
							$formula = str_replace( $this->tags[ 0 ][ $tag_key ], $key_id, $formula );
						}

					}
				}
			}

			foreach ( Caldera_Forms_Field_Util::get_math_functions( $this->form ) as $function ) {
				$formula = str_replace( $function . '(', 'Math.' . $function . '(', $formula );
			}

		} else {
			foreach ( Caldera_Forms_Forms::get_fields( $this->form, false ) as $field_id => $config ) {
				if ( false !== strpos( $formula, $field_id ) ) {
					$this->binds[] = $field_id;
				}

			}

		}

		$formula = str_replace( "\r", '', str_replace( "\n", '', str_replace( ' ', '', trim( Caldera_Forms::do_magic_tags( $formula ) ) ) ) );


		return $this->convert_to_js( $formula );
	}


}