<?php

/**
 * CRUD for Caldera Forms entries
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Entry {

	/**
	 * Form config
	 *
	 * @since 1.3.6
	 *
	 * @var array
	 */
	protected $form;

	/**
	 * Entry ID
	 *
	 * @since 1.3.6
	 *
	 * @var array
	 */
	protected $entry_id;


	/**
	 * Field values for entry
	 *
	 * @since 1.3.6
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 * Meta values for entry
	 *
	 * @since 1.3.6
	 *
	 * @var array
	 */
	protected $meta;

	/**
	 * Entry object
	 *
	 * @since 1.3.6
	 *
	 * @var Caldera_Forms_Entry_Entry
	 */
	protected $entry;


	/**
	 * Caldera_Forms_Entry constructor.
	 *
	 * @since 1.3.6
	 *
	 * @param array $form For config
	 * @param int|bool $entry_id Optional. Passing ID will load saved entry, leave false, the default when creating new entry.
	 */
	public function __construct( array $form, $entry_id = false ) {
		$this->form = $form;
		if( is_numeric( $entry_id  ) ){
			$this->entry_id = $entry_id;
			$this->query();
		}
	}

	/**
	 * Get entry object
	 *
	 * @since 1.3.6
	 *
	 * @param \Caldera_Forms_Entry_Entry $entry
	 */
	public function get_entry_object( Caldera_Forms_Entry_Entry $entry ){
		$this->entry = $entry;
	}

	/**
	 * Get form ID
	 *
	 * @since 1.3.6
	 *
	 * @return mixed
	 */
	public function get_form_id(){
		return $this->form[ 'id' ];
	}

	/**
	 * Get entry object
	 *
	 * Has basic info, no fields or values
	 *
	 * @since 1.3.6
	 *
	 * @return \Caldera_Forms_Entry_Entry
	 */
	public function get_entry(){
		return $this->entry;
	}

	/**
	 * Get entry ID
	 *
	 * @since 1.3.6
	 *
	 * @return int
	 */
	public function get_entry_id(){
		return $this->entry_id;
	}

	/**
	 * Get all field values
	 *
	 * @since 1.3.6
	 *
	 * @return array
	 */
	public function get_fields(){
		return $this->fields;
	}

	/**
	 * Get all meta values
	 *
	 * @since 1.3.6
	 *
	 * @return array
	 */
	public function get_meta(){
		return $this->meta;
	}


	/**
	 * Query DB for saved entry data
	 */
	public function query(){
		$this->find_entry();
		if( is_numeric(  $this->entry_id ) ){
			$this->find_fields();
			$this->find_metas();
		}
	}

	/**
	 * Find the entry in DB and set the entry property of this class
	 *
	 * @since 1.3.6
	 */
	protected function find_entry(){
		global $wpdb;
		$table = $wpdb->prefix . 'cf_form_entries';
		$sql = $wpdb->prepare( "SELECT * FROM $table WHERE `id` = %d", $this->entry_id  );
		$results = $wpdb->get_results( $sql );
		$this->entry = new Caldera_Forms_Entry_Entry( );
		$this->entry->set_form_object( $results[0] );

	}

	/**
	 * Find field values and set in fields property of this class
	 *
	 * @since 1.3.6
	 */
	protected function find_fields(){
		global $wpdb;
		$table = $wpdb->prefix . 'cf_form_entry_values';
		$sql = $wpdb->prepare( "SELECT * FROM $table WHERE `entry_id` = %d",  $this->entry_id  );
		$results = $wpdb->get_results( $sql );
		if( ! empty( $results ) ){
			foreach( $results as $result ){
				$_field = new Caldera_Forms_Entry_Field();
				$_field->set_form_object( $result );
				$this->fields[] = $_field;
			}
		}
	}

	/**
	 * Find metas and set in meta property of this class
	 *
	 * @since 1.3.6
	 */
	protected function find_metas(){
		global $wpdb;
		$table = $wpdb->prefix . 'cf_form_entry_meta';
		$sql = $wpdb->prepare( "SELECT * FROM $table WHERE `entry_id` = %d", $this->entry_id  );
		$results = $wpdb->get_results( $sql );
		if( ! empty( $results ) ){
			foreach ( $results as $result ){
				$_meta = new Caldera_Forms_Entry_Meta();
				$_meta = $_meta->set_form_object( $result );
				$this->meta[] = $_meta;
			}
		}
	}

	/**
	 * Save entry
	 *
	 * @since 1.3.6
	 *
	 * @return array|int|string
	 */
	public function save(){
		$this->create_entry();
		if( is_numeric( $this->entry_id   ) ){
			foreach ( $this->fields as $field ){
				$this->save_field( $field );

			}
			foreach ( $this->meta as $meta ){
				$this->save_meta( $meta );
			}
		}

		return $this->entry_id;
	}

	/**
	 * Update entry status
	 *
	 * @since 1.3.6
	 *
	 * @param $status
	 *
	 * @return bool
	 */
	public function update_status( $status ){
		if( ! in_array( $status, array(
			'active',
			'pending',
			'trash'
		)  ) ){
			return false;
		}
		global $wpdb;
		$wpdb->update($wpdb->prefix . 'cf_form_entries', array('status' => $status ), array( 'id' => $this->entry_id ) );
	}

	/**
	 * Add a field value to this entry
	 *
	 * @since 1.3.6
	 *
	 * @param \Caldera_Forms_Entry_Field $item
	 */
	public function add_field( Caldera_Forms_Entry_Field $item ){
		$this->fields[] = $item;
	}

	/**
	 * Add a meta to this entry
	 *
	 * @since 1.3.6
	 *
	 * @param \Caldera_Forms_Entry_Meta $item
	 */
	public function add_meta( Caldera_Forms_Entry_Meta $item ){
		$this->meta[] = $item;
	}

	/**
	 * Create entry
	 *
	 * @since 1.3.6
	 */
	protected function create_entry() {
		global $wpdb;
		if( null == $this->entry ) {
			//@todo some error or exception or something
			return;
		}
		$wpdb->insert( $wpdb->prefix . 'cf_form_entries', $this->entry->to_array() );
		$this->entry_id = $wpdb->insert_id;
	}

	/**
	 * Save a field in db
	 *
	 * @since 1.3.6
	 *
	 * @param \Caldera_Forms_Entry_Field $field
	 */
	protected function save_field( Caldera_Forms_Entry_Field $field ){
		$field->entry_id = $this->entry_id;
		global $wpdb;
		$wpdb->insert($wpdb->prefix . 'cf_form_entry_values', $field->to_array() );
	}

	/**
	 * Save a meta in DB
	 *
	 * @since 1.3.6
	 * 
	 * @param \Caldera_Forms_Entry_Meta $meta
	 */
	protected function save_meta( Caldera_Forms_Entry_Meta $meta ){
		$meta->entry_id = $this->entry_id;
		global $wpdb;
		$wpdb->insert($wpdb->prefix . 'cf_form_entry_meta',  $meta->to_array() );
	}

}









