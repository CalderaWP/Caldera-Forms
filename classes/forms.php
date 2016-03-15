<?php
/**
 * Gets form configs in and out of the database
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@JoshPress.net>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */

/**
 * Class Caldera_Forms_Forms
 *
 * @since 1.3.4
 */
final class Caldera_Forms_Forms {

	/**
	 * Holds registry of forms
	 *
	 * @since 1.3.4
	 *
	 * @var array
	 */
	protected static $registry_cache;

	/**
	 * Cache key for storing form registry in
	 *
	 * @since 1.3.4
	 *
	 * @var string
	 */
	protected static $registry_cache_key;

	/**
	 * Holds simple index of form IDs
	 *
	 * @since 1.3.4
	 *
	 * @var array
	 */
	protected static $index;

	/**
	 * Holds stored forms
	 *
	 * @since 1.3.4
	 *
	 * @var array
	 */
	protected static $stored_forms;

	/**
	 * Fields used when converting flat registry to detailed registry
	 *
	 * @since 1.3.4
	 *
	 * @var array
	 */
	protected static $detail_fields = array(
		'name',
		'description',
		'success',
		'form_ajax',
		'hide_form',
		'mailer'
	);

	/**
	 * Load a form by ID or name
	 *
	 * @since 1.3.4
	 *
	 * @param string $id_name ID or name of form.
	 *
	 * @return array|null Form config array if found. If not null.
	 */
	public static function get_form( $id_name ){
		$id_name = sanitize_text_field( $id_name );

		$forms = self::get_forms();
		$form = null;

		if( isset( $forms[ $id_name ] ) ){
			$form = get_option( $forms[ $id_name ] );
		}else{
			foreach($forms as $form_id=>$form_maybe){
				if( trim(strtolower($id_name)) == strtolower($form_maybe['name']) && empty( $form_maybe['_external_form'] ) ){
					$form = get_option( $form_maybe['ID'] );
				}
			}
		}

		if( empty( $form ) ){
			$external = true;
		}

		/**
		 * Filter settings of a form or all forms or use to define a form in file
		 *
		 * @param array $form Form config
		 * @param string $id_name ID or name of form
		 */
		$form = apply_filters( 'caldera_forms_get_form', $form, $id_name );

		/**
		 * Filter settings of a specific form or all forms or use to define a form in file
		 *
		 * @param array $form Form config
		 * @param string $id_name ID or name of form
		 */
		$form = apply_filters( 'caldera_forms_get_form-' . $id_name, $form, $id_name );

		if( is_array( $form ) && empty( $form['ID'] ) ){
			$form['ID'] = $id_name;
		}
		if( !empty( $form ) && !empty( $external ) ){
			$form['_external_form'] = true;
		}

		// remove submit on editing
		if( !empty( $_GET['modal'] ) && $_GET['modal'] == 'view_entry' && !empty( $_GET['group'] ) && $_GET['group'] == 'editentry' ){
			if( !empty( $form['fields'] ) ){
				foreach( $form['fields'] as $field_id=>$field ){
					if( $field['type'] == 'button' && $field['config']['type'] == 'submit' ){
						unset( $form['fields'][ $field_id ] );
					}
				}
			}
		}

		return $form;
	}

	/**
	 * Get registry of forms
	 *
	 * @since 1.3.4
	 *
	 * @param bool|false $with_details Optional. If false, the default, just form IDs are returned. If true, basic details of each are returned.
	 * @param bool|false $internal_only Optional. If false, the default, all forms -- in DB and in files system -- are returned -- If true, only those in DB are returned.
	 *
	 * @return array|mixed|void
	 */
	public static function get_forms( $with_details = false, $internal_only = false ){
		if( isset( $_GET[ 'cf-cache-clear' ] ) ){
			self::clear_cache();
		}

		if( $with_details ){
			if( ! empty( self::$registry_cache ) ){
				return self::$registry_cache;
			}else if( false != ( self::$registry_cache = get_transient( self::$registry_cache ) ) ){
				return self::$registry_cache;
			}

		}

		if ( empty( self::$index ) ) {
			$base_forms = self::get_stored_forms();
			if ( true === $internal_only ) {
				return $base_forms;
			}

			/**
			 * Runs after getting internal forms, use to add forms defined in file system
			 *
			 * @since unknown
			 *
			 * @param array $base_forms Forms saved in DB
			 */
			$forms = apply_filters( 'caldera_forms_get_forms', $base_forms );
			foreach ( $forms as $form_id => $form ) {
				$forms[ $form_id ] = $form_id;
			}

			self::$index = $forms;

		}else{
			$forms = self::$index;
		}

		if( $with_details ){
			$forms = self::add_details( $forms );
		}

		if( empty( $forms ) ){
			return array();
		}



		return $forms;

	}

	/**
	 * Get forms stored in DB
	 *
	 * @since 1.3.4
	 *
	 * @return array|void
	 */
	protected static function get_stored_forms() {
		if ( empty( self::$stored_forms ) ) {
			self::$stored_forms = get_option( '_caldera_forms', array() );
		}

		return self::$stored_forms;
	}

	/**
	 * Use to switch form arrays to ID strings
	 *
	 * @since 1.3.4
	 *
	 * @param array|int $val Index to convert
	 *
	 * @return string
	 */
	protected function force_string( $val ){
		if( is_array( $val ) ){
			$val = $val[ 'ID' ];
		}

		return $val;
	}

	/**
	 * Add details to form registry
	 *
	 * @since 1.3.4
	 *
	 * @param array $forms
	 *
	 * @return array
	 */
	protected static function add_details( $forms ){
		foreach( $forms as $id => $form  ){
			$_form = self::get_form( $id );
			if( empty( $_form ) ){
				$_form = array( 'ID' => $id );
			}

			$forms[ $id ] = array();
			foreach( self::$detail_fields as $key ){
				if ( isset( $_form[ $key ] ) ) {
					$forms[ $id ][ $key ] = $_form[ $key ];
				}else{
					if( 'name' == $key ){
						$forms[ $id ][ $key ] = $id;
					}else{
						$forms[ $id ][ $key ] = '';
					}

				}

			}
		}

		$base_forms = self::get_stored_forms();

		foreach ( $forms as $form_id => $form  ) {
			if ( ! isset( $base_forms[ $form_id ] ) ) {
				$forms[ $form_id ][ '_external_form' ] = true;
				if ( empty( $forms[ $form_id ][ 'ID' ] ) ) {
					$forms[ $form_id ][ 'ID' ] = $form_id;
				}

			}
		}

		set_transient( self::$registry_cache_key, $forms, HOUR_IN_SECONDS );
		self::$registry_cache = $forms;
		return self::$registry_cache;

	}

	public static function save_form( $data ){

		// option value labels
		if(!empty($data['fields'])){
			foreach($data['fields'] as &$field){
				if(!empty($field['config']['option']) && is_array($field['config']['option'])){
					foreach($field['config']['option'] as &$option){
						if(!isset($option['value'])){
							$option['value'] = $option['label'];
						}
					}
				}
			}
		}

		// combine structure pages
		$data['layout_grid']['structure'] = implode('#', $data['layout_grid']['structure']);
		// remove fields from conditions
		if( !empty( $data['conditional_groups']['fields'] ) ){
			unset( $data['conditional_groups']['fields'] );
		}
		// remove magics ( yes, not used yet.)
		if( !empty( $data['conditional_groups']['magic'] ) ){
			unset( $data['conditional_groups']['magic'] );
		}

		// add form to registry
		self::update_registry( $data[ "ID" ] );

		// add from to list
		update_option( $data['ID'], $data);

		/**
		 * Fires after a form is saved
		 *
		 * @since unknown
		 *
		 * @param array $data The form data
		 * @param string $from_id The form ID
		 */
		do_action('caldera_forms_save_form', $data, $data['ID']);


	}

	public static function create_form( $newform ){
		// get form templates (PROBABLY NEED TO MOVE METHOD INTO THIS CLASS)
		$form_templates = Caldera_Forms_Admin::internal_form_templates();

		// get form registry
		$forms = self::get_forms( true, true );

		if(!empty($newform['clone'])){
			$clone = $newform['clone'];
		}
		// load template if any
		if( !empty( $newform['template'] ) ){
			if( isset( $form_templates[ $newform['template'] ] ) && !empty( $form_templates[ $newform['template'] ]['template'] ) ){
				$form_template = $form_templates[ $newform['template'] ]['template'];
			}
		}
		$newform = array(
			"ID" 			=> uniqid('CF'),
			"name" 			=> $newform['name'],
			"description" 	=> $newform['description'],
			"success"		=>	__('Form has been successfully submitted. Thank you.', 'caldera-forms'),
			"form_ajax"		=> 1,
			"hide_form"		=> 1,
			"check_honey" 	=> 1,
			'mailer'		=>	array( 'on_insert' => 1 )
		);
		// is template?
		if( !empty( $form_template ) && is_array( $form_template ) ){
			$newform = array_merge( $form_template, $newform );
		}

		/**
		 * Filter newly created form before saving
		 *
		 * @since unkown
		 *
		 * @param array $newform New form config
		 */
		$newform = apply_filters( 'caldera_forms_create_form', $newform);

		$forms[$newform['ID']] = $newform;
		self::update_registry( $newform[ 'ID' ] );

		if(!empty($clone)){
			$clone_form = get_option( $clone );
			if(!empty($clone_form['ID']) && $clone == $clone_form['ID']){
				$newform = array_merge($clone_form, $newform);
			}
		}

		// add form to db
		add_option( $newform['ID'], $newform, 'no' );

		/**
		 * Runs after form is created
		 *
		 * @since unkown
		 *
		 * @param array $newform New form config
		 */
		do_action('caldera_forms_create_form', $newform);
		return $newform;
	}

	/**
	 * Delete a form
	 *
	 * @since 1.3.4
	 *
	 * @param string $id Form ID
	 */
	public static function delete_form( $id ){
		$forms = self::get_forms();
		unset( $forms[ $id ] );
		delete_option( $id );
		self::update_registry( $forms );
	}

	/**
	 * Update form registry
	 *
	 * @since 1.3.4
	 *
	 * @param string|array $new If is string, new index will be added, if is array, whole registry will be updated.
	 *
	 * @return bool
	 */
	protected static function update_registry( $new ){
		if( is_string( $new ) ){
			$forms = self::get_forms();
			$forms[ $new ] = $new;
		}elseif( is_array( $new ) ){
			$forms = $new;
		}else{
			return false;
		}

		self::$index = $forms;

		delete_transient( self::$registry_cache_key );
		update_option( '_caldera_forms', $forms, false );

		/**
		 * Fires after form registry is updated by saving a from
		 *
		 * @since unknown
		 *
		 * @param array $deprecated
		 * @param array $forms Array of forms in registry
		 */
		do_action('caldera_forms_save_form_register', array(), $forms );
	}

	/**
	 * Clear the caching performed by this class
	 *
	 * @since 1.3.4
	 */
	protected static function clear_cache(){
		self::$index = null;
		self::$registry_cache = null;
		delete_transient( self::$registry_cache_key );
	}
	
}
