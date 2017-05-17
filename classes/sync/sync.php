<?php

/**
 * Prepares field sync and sets in $this->binds the array to be serialized in data-binds attribute of inputs for field sync.
 *
 * IMPORTANT: Should be created using Caldera_Forms_Field_Syncfactory::get_object()
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Sync_Sync {

	/**
	 * Config of field being synced
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $field;

	/**
	 * Config of form that field is a part of
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $form;

	/**
	 * Usable magic tags
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $tags;

	/**
	 * Binds to use
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $binds;

	/**
	 * The field ID attribute
	 *
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	protected $field_base_id;

	/**
	 * Default value of field
	 *
	 * May be changed by this class
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	protected $default;

	/**
	 * The current form count
	 *
	 * @since 1.5.0.10
	 *
	 * @var
	 */
	protected  $current_form_count;

	/**
	 * Marks field as being syncable
	 *
	 * @since 1.5.0.10
	 *
	 * @var bool
	 */
	protected $can_sync;


	/**
	 * Caldera_Forms_Field_Sync constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param array $form Form config
	 * @param array $field Field config
	 * @param string $field_base_id Field ID attribute
	 * @param int|null $current_form_count Optional. Current form ID.  Global is used if not provided
	 */
	public function __construct( array $form, array  $field, $field_base_id, $current_form_count = null ) {
		$this->form = $form;
		$this->field = $field;
		$this->field_base_id = $field_base_id;
		if( ! $current_form_count ){
			$current_form_count = Caldera_Forms_Render_Util::get_current_form_count();
		}

		$this->current_form_count = $current_form_count;
		$this->initial_set_default();
		add_filter( 'caldera_forms_render_get_field', array( $this, 'reset_default' ), 25, 2 );

	}

	/**
	 * Check if this field has magic sync
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public function can_sync(){
		if( true === $this->can_sync ){
			return true;
		}
		$this->find_tags();
		$this->find_binds();
		$this->can_sync =  ! empty( $this->binds );
		return $this->can_sync;
	}

	/**
	 * Get new default value
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_default(){
		return $this->default;
	}

	/**
	 * Get the sync bindings
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public function get_binds(){
		return $this->binds;
	}

	/**
	 * Change default value in field config
	 *
	 * @since 1.5.0
	 *
	 * @uses "caldera_forms_render_get_field" filter
	 *
	 * @param array $field Field congig
	 * @param array $form Form config
	 *
	 * @return mixed
	 */
	public function reset_default( $field, $form ){
		if( $field[ 'ID' ] === $this->field[ 'ID' ] && $form[ 'ID' ] === $this->form[ 'ID' ] ) {
			//$field[ 'config' ][ 'default' ] = $this->get_default();
		}

		return $field;
	}

	/**
	 * Check if we have magic tags to work with
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	protected function has_tags(){
		if( ! empty( $this->tags ) ){
			return true;
		}else{
			return false;
		}

	}


	/**
	 * Find the magic tags applicable to this field
	 *
	 * @since 1.5.0
	 */
	protected function find_tags(){
		preg_match_all("/%(.+?)%/", $this->default, $this->tags );
	}

	/**
	 * Find the sync bindings
	 *
	 * @since 1.5.0
	 */
	protected function find_binds(){
		if ( $this->has_tags() ) {
			foreach ( $this->tags[1] as $tag_key => $tag ) {
				foreach ( $this->form[ 'fields' ] as $key_id => $fcfg ) {
					//don't sync to self
					if ( $key_id == $this->field_base_id ) {
						continue;
					}

					if ( $fcfg[ 'slug' ] === $tag ) {
						$this->handle_match( $key_id, $tag_key );
					}

				}

			}

		}

	}

	/**
	 * Add a field binding
	 * 
	 * @since 1.5.0
	 *
	 * @param string $key_id The ID of field to bound to
	 */
	protected function add_bind( $key_id ) {
		if( ! is_array( $this->binds ) ){
			$this->binds = array();
		}

		if( ! in_array( $key_id, $this->binds ) ){
			$this->binds[] = $key_id;
		}

	}

	/**
	 * Set default value for field
	 *
	 *
	 * @since 1.5.0
	 *
	 * @param string $tag_key Index in $this->tags[0] to use for replace
	 * @param string $key_id The ID of field to bound to
	 */
	protected function set_default( $tag_key, $key_id ) {
		$this->default = $this->calculate_default( $tag_key, $key_id );
	}

	/**
	 * Calculate default value for field
	 *
	 * @since 1.5.0
	 *
	 * @param string $tag_key Index in $this->tags[0] to use for replace
	 * @param string $key_id The ID of field to bound to
	 *
	 * @return string
	 */
	protected function calculate_default( $tag_key, $key_id ){
		return str_replace( $this->tags[ 0 ][ $tag_key ], '{{' . $key_id . '}}', $this->default );
	}

	/**
	 * Handle a matched tag
	 *
	 * @since 1.5.0
	 *
	 * @param string  $key_id  The ID of field to bound to
	 * @param string $tag_key Index in $this->tags[0] to use for replace
	 */
	protected function handle_match( $key_id, $tag_key ) {
		$this->add_bind( $key_id );
		$this->set_default( $tag_key, $key_id );
	}

	/**
	 * Set default property when object is constructed based on field settings
	 *
	 * Property gets changed later if sync is happening
	 *
	 * @since 1.5.0
	 *
	 */
	protected function initial_set_default() {
		if ( isset( $this->field[ 'config' ][ 'default' ] ) ) {
			$this->default = $this->field[ 'config' ][ 'default' ];
		} else {
			$this->default = '';
		}
	}


}