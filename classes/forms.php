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
class Caldera_Forms_Forms {

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
	protected static $registry_cache_key = '_cadera_forms';

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
	 * Option key for storing registry in
	 *
	 * @since 1.3.4
	 *
	 * @var string
	 */
	protected static $registry_option_key = '_caldera_forms_forms';

	/**
	 * Fields used when converting flat registry to detailed registry
	 *
	 * @since 1.3.4
	 *
	 * @var array
	 */
	protected static $detail_fields = array(
		'ID',
		'name',
		'description',
		'success',
		'form_ajax',
		'hide_form',
		'db_support',
		'mailer',
		'pinned',
		'pin_roles',
		'hidden',
		'form_draft'
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

		if ( self::is_internal_form( $id_name ) ) {
			if ( isset( $forms[ $id_name ] ) ) {
				$form = get_option( $forms[ $id_name ] );
			} else {
				$forms = self::get_forms( true );
				foreach ( $forms as $form_id => $form_maybe ) {
					if ( trim( strtolower( $id_name ) ) == strtolower( $form_maybe[ 'name' ] ) && empty( $form_maybe[ '_external_form' ] ) ) {
						$form = get_option( $form_maybe[ 'ID' ] );
					}
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

		if( ! empty( $form ) && ! empty( $external ) ){
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
			}elseif( false != ( self::$registry_cache = get_transient( self::$registry_cache_key ) ) ){
				if ( is_array( self::$registry_cache ) ) {
					return self::$registry_cache;
				}

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
			if ( ! empty( $forms  ) && is_array( $forms )) {
				foreach ( $forms as $form_id => $form ) {
					$forms[ $form_id ] = $form_id;
				}
			}

			self::$index = $forms;

		}else{
			$forms = self::$index;
		}

		if( $with_details ){
			$forms = self::add_details( $forms );
		}

		if( ! is_array( $forms ) ){
			$forms =  array();
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
			self::$stored_forms = get_option( self::$registry_option_key, array() );
		}

		return self::$stored_forms;
	}

	/**
	 * Import form
	 *
	 * @since 1.3.4
	 *
	 * @param array $data Form config
	 *
	 * @return string Form ID
	 */
	public static function import_form( $data ){
		$forms = self::get_forms();
		if ( isset( $data[ 'ID' ] ) && array_key_exists( $data[ 'ID' ], $forms ) ) {
			// generate a new ID
			$data[ 'ID' ]   = uniqid( 'CF' );

		}

		if( isset( $data[ 'ID' ] ) ){
			$id = $data[ 'ID' ];
		}else{
			$id = $data[ 'ID' ]   = uniqid( 'CF' );
		}


		$data[ 'ID' ] = trim( $id );

		$new_form = self::save_form( $data );
		if( is_array( $new_form ) && isset( $new_form[ 'ID' ] ) ){
			$new_form = $new_form[ 'ID' ];
		}

		return $new_form;

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

		$valid_forms = array();
		
		foreach( $forms as $id => $form  ){
			$_form = self::get_form( $id );
			if( empty( $_form ) ){
				//if its empty, there is no form. we can't just make up stuff.
				continue;
			}

			$valid_forms[ $id ] = array();
			foreach( self::$detail_fields as $key ){
				if ( isset( $_form[ $key ] ) ) {
					$valid_forms[ $id ][ $key ] = $_form[ $key ];
				} elseif ( 'name' == $key ) {
					$valid_forms[ $id ][ $key ] = $id;
				} elseif ( 'mailer' == $key ) {
					$valid_forms[ $id ][ $key ] = array( 'on_insert' => 1 );
				} elseif ( in_array( $key, array( 'form_ajax', 'check_honey', 'hide_form', 'db_support' ) ) ) {
					$valid_forms[ $id ][ $key ] = 1;
				} elseif( 'form_draft' == $key ) {
					$valid_forms[ $id ][ $key ] = 0;
				}else {
					$valid_forms[ $id ][ $key ] = '';
				}



			}
		}

		$base_forms = self::get_stored_forms();

		foreach ( $valid_forms as $form_id => $form  ) {
			if ( ! isset( $base_forms[ $form_id ] ) ) {
				$forms[ $form_id ][ '_external_form' ] = true;
				if ( empty( $forms[ $form_id ][ 'ID' ] ) ) {
					$valid_forms[ $form_id ][ 'ID' ] = $form_id;
				}

			}
		}

		if ( ! empty( $valid_forms ) ) {
			set_transient( self::$registry_cache_key, $valid_forms, HOUR_IN_SECONDS );
		}

		self::$registry_cache = $valid_forms;
		return self::$registry_cache;

	}

	/**
	 * Save a form
	 *
	 * @since 1.3.4
	 *
	 * @param array $data
	 *
	 * @return string|bool Form ID if updated, false if not
	 */
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
		if ( isset( $data['layout_grid']['structure'] ) && is_array( $data['layout_grid']['structure'] ) ) {
			$data[ 'layout_grid' ][ 'structure' ] = implode( '#', $data[ 'layout_grid' ][ 'structure' ] );
		}
		// remove fields from conditions
		if( !empty( $data['conditional_groups']['fields'] ) ){
			unset( $data['conditional_groups']['fields'] );
		}
		// remove magics ( yes, not used yet.)
		if( !empty( $data['conditional_groups']['magic'] ) ){
			unset( $data['conditional_groups']['magic'] );
		}
		// sanitize condition values
		if( !empty( $data['conditional_groups']['conditions'] ) ){
			foreach( $data['conditional_groups']['conditions'] as $condition_id => &$condition ){
				if( !empty( $condition['group'] ) ){
					$condition['name'] = htmlentities( $condition['name'] );
					foreach ($condition['group'] as $group_id => &$group) {
						foreach( $group as $case_id=>&$case ){							
							$case['value'] = htmlentities( $case['value'] );
						}
					}
				}
			}
		}

		$data[ '_last_updated' ] = date('r');
		$data[ 'version' ] = CFCORE_VER;

		/**
		 * Filter form config directly before saving
		 *
		 * @since 1.4.0
		 *
		 * @param array $data Form config
		 */
		$data = apply_filters( 'caldera_forms_presave_form', $data  );

		// add form to registry
		self::update_registry( $data[ "ID" ] );

		// add from to list
		$updated = update_option( $data['ID'], $data);

		/**
		 * Fires after a form is saved
		 *
		 * @since unknown
		 *
		 * @param array $data The form data
		 * @param string $from_id The form ID
		 */
		do_action('caldera_forms_save_form', $data, $data['ID']);

		if( $updated && isset( $data[ 'ID' ] ) ){
			$updated = $data[ 'ID' ];
		}

		return $updated;
	}

	/**
	 * Create a new form
	 *
	 * @since 1.3.4
	 *
	 * @param array $newform Data for new form
	 *
	 * @return array|mixed|void
	 */
	public static function create_form( $newform ){
		require_once( CFCORE_PATH . 'classes/admin.php' );

		// get form templates (PROBABLY NEED TO MOVE METHOD INTO THIS CLASS)
		$form_templates = Caldera_Forms_Admin::internal_form_templates();


		if(!empty($newform['clone'])){
			$clone = $newform['clone'];
		}
		// load template if any
		if( !empty( $newform['template'] ) ){
			if( isset( $form_templates[ $newform['template'] ] ) && !empty( $form_templates[ $newform['template'] ]['template'] ) ){
				$form_template = $form_templates[ $newform['template'] ]['template'];
			}
		}

		$forms = self::get_forms();
		if( ! isset( $newform[ 'ID' ] ) || ( ! isset( $newform[ 'ID' ] ) && array_key_exists( $newform[ 'ID' ], $forms ) ) ) {
			$id = uniqid('CF');
		}else{
			$id = $newform[ 'ID' ];
		}

		$id = trim( $id );
		$defaults = array(
			"ID" 			=> $id,
			"name" 			=> '',
			"description" 	=> '',
			"success"		=>	__('Form has been successfully submitted. Thank you.', 'caldera-forms'),
			"form_ajax"		=> 1,
			"hide_form"		=> 1,
			"check_honey" 	=> 1,
			"db_support"    => 1,
			'mailer'		=>	array( 'on_insert' => 1 )
		);

		$newform = wp_parse_args( $newform, $defaults );

		// is template?
		if( !empty( $form_template ) && is_array( $form_template ) ){
			$newform = array_merge( $form_template, $newform );
		}

		/**
		 * Filter newly created form before saving
		 *
		 * @since unknown
		 *
		 * @param array $newform New form config
		 */
		$newform = apply_filters( 'caldera_forms_create_form', $newform);


		self::update_registry( $id );

		if(!empty($clone)){
			$clone_form = self::get_form( $clone );
			if(!empty($clone_form['ID']) && $clone == $clone_form['ID']){
				$newform = array_merge($clone_form, $newform);
			}
		}

		// add form to db
		$added = add_option( $id, $newform, false );
		if( ! $added ){
			return false;

		}

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
	 *
	 * @return bol
	 */
	public static function delete_form( $id ){
		$forms = self::get_forms();
		if( ! isset( $forms[ $id ] ) ){
			return false;
		}

		unset( $forms[ $id ] );
		$deleted = delete_option( $id );
		if ( $deleted ) {
			self::update_registry( $forms );

			return $deleted;
		}

		return false;
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
			$forms = self::get_stored_forms();
			$forms[ $new ] = $new;
		}elseif( is_array( $new ) ){
			$forms = $new;
		}else{
			return false;
		}

		update_option( self::$registry_option_key, $forms, false );
		self::clear_cache();
		self::$index = $forms;

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
		self::$index = array();
		self::$registry_cache = array();
		self::$stored_forms = array();
		wp_cache_delete( '_caldera_forms_forms', 'options' );
		delete_transient( self::$registry_cache_key );
	}

	/**
	 * Check if a form is stored in DB by name oir ID
	 *
	 * @since 1.3.4
	 *
	 * @param string $id_name Form name or ID
	 *
	 * @return bool
	 */
	public static function is_internal_form( $id_name ){
		return in_array( $id_name, self::get_stored_forms() );
	}

	/**
	 * Change a form's state form enabled to disabled or vise vera
	 *
	 * @since 1.3.5
	 *
	 * @param array $form Form config.
	 * @param bool|true $enable Optional. If true, enable form, if false, disable form.
	 */
	public static function form_state( $form, $enable = true ){
		if( $enable  ){
			$form['form_draft'] = 0;

		}else{
			$form['form_draft'] = 1;

		}

		if( is_array( self::$registry_cache ) && isset( self::$registry_cache[ $form[ 'ID' ] ] ) ){
			delete_transient( self::$registry_cache_key );
			self::$registry_cache[ $form[ 'ID' ] ][ 'form_draft' ] = $form['form_draft'];
		}

		self::save_form( $form );

	}

	/**
	 * Get all fields of a form
	 *
	 * @since 1.4.4
	 *
	 * @param array $form The form config
	 * @param bool $in_order Optional. Return in layout order, the default, or in stored order (false).
	 *
	 * @return array|mixed
	 */
	public static function get_fields( array $form, $in_order = true ){
		if( empty( $form[ 'fields' ] ) ){
			return array();
		}

		$fields = $form[ 'fields' ];

		if ( $in_order ) {

			if( isset( $form[ 'layout_grid' ][ 'fields' ] ) ){
				$order   = array_keys( $form[ 'layout_grid' ][ 'fields' ] );
			}else{
				$order = array_keys( $fields );
			}

			/**
			 * Change order of fields
			 *
			 * Very useful for reordering fields outputted with {summary} magic tag
			 *
			 * @since 1.4.5
			 *
			 * @param array $order Order -- array of field IDs
			 * @param array $form Form config
			 */
			$order = apply_filters( 'caldera_forms_get_field_order', $order, $form );

			$ordered = array();
			foreach ( $order as $key ) {
				if ( isset( $fields[ $key ] ) ) {
					$ordered[ $key ] = $fields[ $key ];
				}

			}

			return $ordered;

		}else{
			return $fields;
		}


	}

	/**
	 * Get entry list fields of a form
	 *
	 * @since 1.5.0
	 *
	 * @param array $form Form config
	 * @param bool $configs Optional. If true, field config arrays are returned. If false, the default, field IDs are returned
	 *
	 * @return array
	 */
	public static function entry_list_fields( array  $form, $configs = false ){
		$fields = self::get_fields( $form );
		$entry_list_fields = array();
		foreach ( $fields as $field_id => $field ){
			if( ! empty( $field[ 'entry_list'])){
				if ( $configs  ) {
					$entry_list_fields[ $field_id ] = $field;
				}else{
					$entry_list_fields[] = $field_id;
				}
			}
		}

		return $entry_list_fields;
	}

}
