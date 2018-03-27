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
	 * @param string $type Optional. Default is "primary" the main form. Can also be "revision" for getting a revision.
	 *
	 * @return array|null Form config array if found. If not null.
	 */
	public static function get_form( $id_name, $type = 'primary' ){
		$id_name = sanitize_text_field( $id_name );

		$forms = self::get_forms();
		$form = null;

		if ( self::is_internal_form( $id_name ) ) {
			if ( isset( $forms[ $id_name ] ) ) {
				$form = self::get_from_db( $id_name, $type );
				if( empty( $form ) ){
					$form = get_option( $forms[ $id_name ] );
					if( ! empty( $form ) ){
						$form = self::maybe_migrate( $form );
					}

				}

			} else {
				$forms = self::get_forms( true );
				foreach ( $forms as $form_id => $form_maybe ) {
					if ( trim( strtolower( $id_name ) ) == strtolower( $form_maybe[ 'name' ] ) && empty( $form_maybe[ '_external_form' ] ) ) {
						$form = self::get_from_db(  $form_maybe[ 'ID' ], $type );
						if( empty( $form ) ){
							$form = get_option( $forms[ $id_name ] );
							if( ! empty( $form ) ){
								$form = self::maybe_migrate( $form );
							}
						}
					}
				}
			}
		}

		if( empty( $form ) ){
			$external = true;
		}else{
			$form = self::db_to_return( $form );
		}

		$form = self::filter_form( $id_name, $form );

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
	public static function get_forms( $with_details = false, $internal_only = false, $orderby = false ){
		if( isset( $_GET[ 'cf-cache-clear' ] ) ){
			self::clear_cache();
		}

		if( $with_details ){
			$_forms = array();
			if( ! empty( self::$registry_cache ) ){
				$_forms = self::$registry_cache;
			}elseif( false != ( self::$registry_cache = get_transient( self::$registry_cache_key ) ) ){
				if ( is_array( self::$registry_cache ) ) {
					$_forms = self::$registry_cache;
				}

			}

			if( ! empty( $_forms ) ){
				if( ! $orderby ){
					return $_forms;
				}

				return self::order_forms( $_forms, $orderby );

			};

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

		if( $orderby && ! empty( $forms ) ){
			return self::order_forms( $forms, $orderby );
		}

		return $forms;

	}

	/**
	 * Check if a form is a revision
	 *
	 * @since 1.5.4
	 *
	 * @param array $form Form config
	 *
	 * @return bool
	 */
	public static function is_revision( array  $form ){
		return isset( $form[ 'type' ] )  && 'revision' === $form[ 'type' ];

	}

	/**
	 * Make a reivison the primary form
	 *
	 * @since 1.5.3
	 *
	 * @param int $revision_id Revision ID
	 *
	 * @return false|int
	 */
	public static function restore_revision( $revision_id ){
		return Caldera_Forms_DB_Form::get_instance( )->make_revision_primary( $revision_id );
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
	 * @param bool $trusted Is import data trusted? Default is to trust.
	 *
	 * @return string Form ID
	 */
    public static function import_form( $data, $trusted = true ){
        $forms = self::get_forms();
        if ( isset( $data[ 'ID' ] ) && array_key_exists( $data[ 'ID' ], $forms ) ) {
            // generate a new ID
            $data[ 'ID' ]   = self::create_unique_form_id();
        }

        if( isset( $data[ 'ID' ] ) ){
            $id = $data[ 'ID' ];
        }else{
            $id = $data[ 'ID' ]   = self::create_unique_form_id();
        }

        $data[ 'ID' ] = trim( $id );
        unset( $data[ 'db_id' ] );
		
        $importer = new Caldera_Forms_Import_Form($data, $trusted );
        $data = $importer->get_prepared_form();
        $new_form = self::save_form( $data );
        if( is_array( $new_form ) && isset( $new_form[ 'ID' ] ) ){
            $new_form = $new_form[ 'ID' ];
        }

        return $new_form;

    }

	/**
	 * Apply caldera_forms_get_form filters
	 *
	 * @since 1.5.3
	 *
	 * @param string $id_name Form ID or name
	 * @param array $config Form config
	 * @param bool $is_revision Optional. Is this a revision of a form? Default is false
	 *
	 * @return mixed|void
	 */
	protected static function filter_form( $id_name, $config, $is_revision = false ){

		if( ! isset( $config[ 'type' ] ) ){
			if( $is_revision ){
				$config[ 'type' ] = 'revision';
			}else{
				$config[ 'type' ] = 'primary';
			}

		}

		/**
		 * Filter settings of a form or all forms or use to define a form in file
		 *
		 * @since unknown
		 *
		 * @param array $config Form config
		 * @param string $id_name ID or name of form
		 * @param bool $is_revision Is this a revision of a form? @since 1.5.3
		 */
		$config = apply_filters( 'caldera_forms_get_form', $config, $id_name, $is_revision );

		/**
		 * Filter settings of a specific form or all forms or use to define a form in file
		 *
		 * @since unknown
		 *
		 * @param array $config Form config
		 * @param string $id_name ID or name of form
		 * @param bool $is_revision Is this a revision of a form? @since 1.5.3
		 */
		$config = apply_filters( 'caldera_forms_get_form-' . $id_name, $config, $id_name, $is_revision );

		return $config;

	}

	/**
	 * Convert array returned from database to form config array expected by admin and front-end and like everything
	 *
	 * @since 1.5.3
	 *
	 * @param $form
	 *
	 * @return mixed
	 */
	protected static function db_to_return( $form ){
		$_form            = $form[ 'config' ];
		$_form[ 'db_id' ] = $form[ 'id' ];
		$_form[ 'type' ]  = $form[ 'type' ];

		return $_form;

	}

	/**
	 * Get the filter value for maximum revisions
	 *
	 * @since 1.5.3
	 *
	 * @param array $form Form config
	 *
	 * @return int
	 */
	protected static function max_revisions( array $form ){
		/**
		 * Change the number fo form revisions to store per form
		 *
		 * @since 1.5.3
		 *
		 * @param int|bool Number of revisions to save. Set to false or 0 to disable form revisions.
		 * @param array $form Form Config
		 */
		$max_revisions = apply_filters( 'caldera_forms_max_form_revisions', 15, $form );

		return (int) $max_revisions;
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
				$valid_forms[ $form_id ][ '_external_form' ] = true;
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
	 * @param array $data Form config
	 * @param string $type Optional. Default is "primary" the main form. Can also be "revision" for saving a revision. @since 1.5.3

	 *
	 * @return string|bool Form ID if updated, false if not
	 */
	public static function save_form( $data, $type = 'primary' ){

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

		self::save_to_db( $data, $type );

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
			"success"		=>	__( 'Form has been successfully submitted. Thank you.', 'caldera-forms' ),
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
				unset( $newform[ 'db_id' ] );
			}
		}

		// add form to db
		$added = self::save_to_db( $newform, 'primary' );
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
	 * Will delete all revisions
	 *
	 * @since 1.3.4
	 *
	 * @param string $id Form ID
	 *
	 * @return bool
	 */
	public static function delete_form( $id ){
		$forms = self::get_forms();
		if( ! isset( $forms[ $id ] ) ){
			return false;
		}

		unset( $forms[ $id ] );
		delete_option( $id );
		$deleted = Caldera_Forms_DB_Form::get_instance()->delete_by_form_id( $id );
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

	/**
	 * Get all revisions of  a forms
	 *
	 * @since 1.5.3
	 *
	 * @param string $form_id Form ID
	 *
	 * @return array
	 */
	public static function get_revisions( $form_id, $simple = false ){
		$forms = Caldera_Forms_DB_Form::get_instance()->get_by_form_id( $form_id, false );
		$revisions = array();
		if( ! empty( $forms ) ){
			foreach ( $forms as $i => $form ){
				if( 'revision' === $form[ 'type' ] ){
					if( empty( $form ) || ! isset( $form[ 'id' ] ) ){
						continue;
					}
					if( $simple ){
						$revisions[] = $form[ 'id'  ];
					}else{
						$form = self::db_to_return( $form );
						$revisions[] = self::filter_form( $form[ 'ID' ], $form, true );
					}

				}
			}
		}


		return $revisions;
	}

	/**
	 * Get a revision of a form
	 *
	 * @since 1.5.3
	 *
	 * @param int $id Revision ID
	 *
	 * @return array
	 */
	public static function get_revision( $id ){
		$form = Caldera_Forms_DB_Form::get_instance()->get_record( $id );
		$form = self::db_to_return( $form );
		$form = self::filter_form( $form[ 'ID' ], $form, true );
		return $form;
	}

	/**
	 * If necessary migrate to database table added in Caldera Forms 1.5.3
	 *
	 * @since 1.5.3
	 *
	 * @param array $form Form config
	 *
	 * @return array|bool
	 */
	protected static function maybe_migrate( array $form ){
		$in_db = Caldera_Forms_DB_Form::get_instance()->get_by_form_id( $form[ 'ID' ] );
		if( empty( $in_db ) ){
			self::save_to_db( $form, 'primary' );
		}

		return self::get_from_db( $form[ 'ID' ] );
	}

	/**
	 * Save form to database
	 *
	 * @param array $form Form config
	 * @param string $type Optional. What type of form state is "primary" or "revision". Default is primary.
	 *
	 * @return bool|false|int|null
	 */
	protected static function save_to_db( array  $form, $type = 'primary' ){
		if( 'primary' !== $type ){
			$max_revisions = self::max_revisions( $form );
			if( ! $max_revisions ){
				return false;
			}
		}

		$data = array(
			'form_id' => $form[ 'ID' ],
			'config' => $form,
			'type' => $type
		);

		if( isset( $form[ 'db_id' ] ) ){
			$data[ 'db_id' ] = $form[ 'db_id' ];
			$db_id = Caldera_Forms_DB_Form::get_instance()->update( $data );
			self::maybe_delete_old_revisions( $form );
		}else{
			$db_id = Caldera_Forms_DB_Form::get_instance()->create( $data );

		}


		return $db_id;
	}

	/**
	 * Get a form or forms (IE revisions of form) from database
	 *
	 * @since 1.5.3
	 *
	 * @param string $form_id Form ID
	 * @param bool $primary_only Optional. Default is true, to only return primary form. False to include all revisions.
	 *
	 * @return array|bool
	 */
	protected static function get_from_db( $form_id, $primary_only = true ){
		return Caldera_Forms_DB_Form::get_instance()->get_by_form_id( $form_id, $primary_only );

	}

	/**
	 * Delete form revisions if there are more than the max revisions
	 *
	 *
	 * @since 1.5.3
	 *
	 * @param array $form Form config
	 */
	protected static function maybe_delete_old_revisions( array  $form ){
		$revisions = self::get_revisions( $form[ 'ID' ], true);
		if( ! empty( $revisions ) ) {
			$max_revisions = self::max_revisions( $form );

			if( $max_revisions < count( $revisions ) ){
				sort( $revisions );
				$deletes = array();
				foreach ( $revisions as $key => $id ){
					if( $key + 1 < $max_revisions ){
						$deletes[] = $id;
					}
				}
				Caldera_Forms_DB_Form::get_instance()->delete( $deletes );

			}

		}

	}

	/**
	 * Order forms by a value
	 *
	 * @since 1.5.6
	 *
	 * @param array $forms Forms to sort.
	 * @param string $by Optional. Default is "name" as of 1.5.6, no other values are allowed.
	 *
	 * @return array|mixed|void
	 */
	protected static function order_forms( array  $forms, $by = 'name' ){
		if( ! in_array( $by, array(
			'name',
		))){
			$by = 'name';
		}

		if( ! empty( $forms ) ){
			$values = array_values($forms);

			if( ! is_array( $values[0] ) ) {
				$forms = self::get_forms( true );
			}
		}

		if( empty( $forms ) ){
			return $forms;
		}

		switch( $by ) {
			case 'name' :
				default :
				$map = array_combine( wp_list_pluck( $forms, $by ), array_keys( $forms ) );

				break;
		}

		ksort( $map, SORT_ASC );
		$final  = array();
		foreach( $map as $name => $id ){
			$final[ $id ] = $forms[ $id ];
		}

		return $final;


	}

    /**
     * Create a unique form ID
     *
     * @since 1.6.0
     *
     * @return string
     */
    public static function create_unique_form_id(){
        return uniqid( 'CF' );
    }
}
