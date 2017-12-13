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
	 * @since 1.4.0
	 *
	 * @var array
	 */
	protected $form;

	/**
	 * Entry ID
	 *
	 * @since 1.4.0
	 *
	 * @var array
	 */
	protected $entry_id;


	/**
	 * Field values for entry
	 *
	 * @since 1.4.0
	 *
	 * @var array
	 */
	protected $fields;

	/**
	 * Meta values for entry
	 *
	 * @since 1.4.0
	 *
	 * @var array
	 */
	protected $meta;

	/**
	 * Entry object
	 *
	 * @since 1.4.0
	 *
	 * @var Caldera_Forms_Entry_Entry
	 */
	protected $entry;

	/**
	 * Was an entry queried for and found?
	 *
	 * @since 1.4.0
	 *
	 * @var bool
	 */
	protected $found;

	/**
	 * Holds the map of field_id to index of $this->fields. Lazy loaded by $this->get_field_map()
	 *
	 * @since 1.5.0.7
	 *
	 * @var array
	 */
	private $field_map;

    /**
     * WPDB instance
     *
     * @since 1.5.8
     *
     * @var WPDB|false
     */
	private  $wpdb;


	/**
	 * Caldera_Forms_Entry constructor.
	 *
	 * @since 1.4.0
	 *
	 * @param array $form For config
	 * @param int|bool $entry_id Optional. Passing ID will load saved entry, leave false, the default when creating new entry.
	 * @param Caldera_Forms_Entry_Entry $entry Optional. Entry object.
	 */
	public function __construct( array $form, $entry_id = false, Caldera_Forms_Entry_Entry $entry = null ) {
		$this->form = $form;
		if( null !== $entry ){
			if( null != $entry->id ){
				$this->entry_id = $entry->id;
			}

			$this->entry = $entry;
		}elseif( is_numeric( $entry_id  ) ){
			$this->entry_id = $entry_id;
		}
		
		if( is_numeric( $this->entry_id ) ){
			$this->query();
		}
		
	}

	/**
	 * Set entry object (seriously it's not a getter, it's a setter, don't use.)
	 *
	 * @since 1.4.0
     * @deprecated 1.5.8
	 *
	 * @param \Caldera_Forms_Entry_Entry $entry
	 */
	public function get_entry_object( Caldera_Forms_Entry_Entry $entry ){
	    _deprecated_function( __FUNCTION__, '1.5.8', 'Caldera_Forms_Entry::set_entry_object' );
		$this->set_entry_object( $entry );
	}

    /**
     * Set entry object
     *
     * @since 1.5.8
     *
     * @param \Caldera_Forms_Entry_Entry $entry
     *
     * @return $this
     */
    public function set_entry_object( Caldera_Forms_Entry_Entry $entry ){
        $this->entry = $entry;
        return $this;
    }

	/**
	 * Get form ID
	 *
	 * @since 1.4.0
	 *
	 * @return mixed
	 */
	public function get_form_id(){
		return $this->form[ 'ID' ];
	}

	/**
	 * Get entry object
	 *
	 * Has basic info, no fields or values
	 *
	 * @since 1.4.0
	 *
	 * @return \Caldera_Forms_Entry_Entry
	 */
	public function get_entry(){
		return $this->entry;
	}

	/**
	 * Was a query performed and an entry found?
	 *
	 * @since 1.4.0
	 *
	 * @return bool
	 */
	public function found(){
		return $this->found;
	}

	/**
	 * Get entry ID
	 *
	 * @since 1.4.0
	 *
	 * @return int
	 */
	public function get_entry_id(){
		if( null == $this->entry_id && is_object( $this->entry ) ){
			$this->entry_id = $this->entry->id;
		}
		return $this->entry_id;
	}

	/**
	 * Get all field values
	 *
	 * @since 1.4.0
	 *
	 * @return array
	 */
	public function get_fields(){
		if ( ! empty( $this->fields ) ) {
			/** @var Caldera_Forms_Entry_Field $field */
			foreach ( $this->fields as $index => $field ) {
				$field->value           = $field->apply_filter( 'value', $field->value );
				$this->fields[ $index ] = $field;
			}

		}

		return $this->fields;
		
	}


	/**
	 * Get a specific field
	 *
	 * @since 1.4.0
	 *
	 * @param string $id Field ID
	 *
	 * @return Caldera_Forms_Entry_Field|null
	 */
	public function get_field( $id ){
		return $this->find_field_by_id( $id );
	}

	/**
	 * Get all meta values
	 *
	 * @since 1.4.0
	 *
	 * @return array
	 */
	public function get_meta(){
		return $this->meta;
	}

    /**
     * Get form config
     *
     * @since 1.5.8
     *
     * @return array
     */
	public function get_form(){
        return $this->form;
    }


	/**
	 * Query DB for saved entry data
	 *
	 * @since 1.4.0
	 */
	public function query(){
		if ( ! is_object( $this->entry ) ) {
			$this->find_entry();
		}

		if( is_numeric(  $this->entry_id ) ){
			$this->find_fields();
			$this->find_metas();
		}
	}

	/**
	 * Find the entry in DB and set the entry property of this class
	 *
	 * @since 1.4.0
	 */
	protected function find_entry(){
		$wpdb = $this->get_wpdb();
        if ( $wpdb ) {
            $table = $wpdb->prefix . 'cf_form_entries';
            $sql = $wpdb->prepare("SELECT * FROM $table WHERE `id` = %d AND `form_id` = %s", $this->entry_id, $this->form['ID']);
            $results = $wpdb->get_results($sql);
            if (!empty($results)) {
                $this->found = true;
                $this->entry = new Caldera_Forms_Entry_Entry();
                $this->entry->set_form_object($results[0]);
            } else {
                $this->found = false;
            }
        }

	}

	/**
	 * Find field values and set in fields property of this class
	 *
	 * @since 1.4.0
	 */
	protected function find_fields(){
		$wpdb = $this->get_wpdb();
        if ( $wpdb ) {
            $table = $wpdb->prefix . 'cf_form_entry_values';
            $sql = $wpdb->prepare("SELECT * FROM $table WHERE `entry_id` = %d", $this->entry_id);
            $results = $wpdb->get_results($sql);
            if (!empty($results)) {
                foreach ($results as $result) {
                    $_field = new Caldera_Forms_Entry_Field($result);
                    $this->fields[] = $_field;
                }
            }
        }
	}

	/**
	 * Find metas and set in meta property of this class
	 *
	 * @since 1.4.0
	 */
	protected function find_metas(){
		$wpdb = $this->get_wpdb();
        if ( $wpdb ) {
            $table = $wpdb->prefix . 'cf_form_entry_meta';
            $sql = $wpdb->prepare("SELECT * FROM $table WHERE `entry_id` = %d", $this->entry_id);
            $results = $wpdb->get_results($sql);
            if (!empty($results)) {
                foreach ($results as $result) {
                    $_meta = new Caldera_Forms_Entry_Meta();
                    $_meta = $_meta->set_form_object($result);
                    $this->meta[] = $_meta;
                }
            }
        }
	}

	/**
	 * Save entry
	 *
	 * @since 1.4.0
	 *
	 * @return array|int|string
	 */
	public function save(  ){
		$this->save_entry();
		if( is_numeric( $this->entry_id   ) ){
			if ( ! empty( $this->fields ) ) {
				foreach ( $this->fields as $i =>  $field ) {
					if ( $field instanceof  Caldera_Forms_Entry_Field ) {
						$this->fields[ $i ] = $this->save_field( $field );
					}

				}
				
			}

			if ( ! empty( $this->meta ) ) {
				foreach ( $this->meta as $i => $meta ) {
					if ( $meta instanceof Caldera_Forms_Entry_Meta ) {
						$this->meta[ $i ] = $this->save_meta( $meta );
					}
				}

			}
		}

		return $this->entry_id;
	}

	/**
	 * Update entry status
	 *
	 * @since 1.4.0
	 *
	 * @param string $status
	 *
	 * @return bool
	 */
	public function update_status( $status ){
		if( ! $this->allowed_status( $status ) ){
			return false;
		}

		$wpdb = $this->get_wpdb();
        if ( $wpdb ) {
            $wpdb->update($wpdb->prefix . 'cf_form_entries', array('status' => $status), array('id' => $this->entry_id));
        }
	}

	/**
	 * Add a field value to this entry
	 *
	 * @since 1.4.0
	 *
	 * @param \Caldera_Forms_Entry_Field $field
	 */
	public function add_field( Caldera_Forms_Entry_Field $field ){

		$field->entry_id = $this->entry_id;
		$key = $this->find_field_index( $field->field_id );
		if( ! is_null( $key ) ){
			$this->fields[ $key ] = $field;
		}else{
			$this->fields[] = $field;
		}


	}

	/**
	 * Add a meta to this entry
	 *
	 * @since 1.4.0
	 *
	 * @param \Caldera_Forms_Entry_Meta $item
	 */
	public function add_meta( Caldera_Forms_Entry_Meta $item ){
		$this->meta[] = $item;
	}

	/**
	 * Save entry in DB
	 *
	 * @since 1.4.0
	 */
	protected function save_entry() {
		$wpdb = $this->get_wpdb();
		if( null == $this->entry ) {
			//@todo some error or exception or something
			return;
		}
        if ( $wpdb ) {
            if (!$this->entry_id) {
                $wpdb->insert($wpdb->prefix . 'cf_form_entries', $this->entry->to_array());
                $this->entry_id = $this->entry->id = $wpdb->insert_id;
            } else {
                $wpdb->update($wpdb->prefix . 'cf_form_entries', $this->entry->to_array(), array(
                    'id' => $this->entry_id
                ));
            }
        }
	}

	/**
	 * Save a field in db
	 *
	 * @since 1.4.0
	 *
	 * @param \Caldera_Forms_Entry_Field $field
	 *
	 * @return  Caldera_Forms_Entry_Field
	 */
	protected function save_field( Caldera_Forms_Entry_Field $field ){
		$field->entry_id = $this->entry_id;
		$wpdb = $this->get_wpdb();
		if( $wpdb ) {
            $data = $field->to_array();
            if (!isset($data['id'])) {
                $field->id = $field->save();
            } else {
                Caldera_Forms_Entry_Update::update_field($field);
            }

        }

		return $field;
	}

	/**
	 * Save a meta in DB
	 *
	 * @since 1.4.0
	 *
	 * @param \Caldera_Forms_Entry_Meta $meta
	 *
	 * @return Caldera_Forms_Entry_Meta
	 */
	protected function save_meta( Caldera_Forms_Entry_Meta $meta ){
		$meta->entry_id = $this->entry_id;
		$wpdb = $this->get_wpdb();
        if( $wpdb ) {
            $data = $meta->to_array();
            unset( $data[ 'id' ] );
            $wpdb->insert( $wpdb->prefix . 'cf_form_entry_meta',  $data );
            $meta->meta_id = $wpdb->insert_id;
            return $meta;
        }

	}

	/**
	 * Check if is an allowed status
	 *
	 * @since 1.4.0
	 *
	 * @param string $status Status
	 *
	 * @return bool
	 */
	protected function allowed_status( $status ){
		if( ! in_array( $status, array(
			'active',
			'pending',
			'trash'
		)  ) ){
			return false;
		}

		return true;
	}

	/**
	 * Find field object by field ID
	 *
	 * @since 1.5.0.7
	 *
	 * @param $field_id
	 *
	 * @return Caldera_Forms_Entry_Field|null
	 */
	protected function find_field_by_id( $field_id ){
		$key = $this->find_field_index( $field_id );
		if( ! is_null( $key ) ){
			return $this->fields[ $key ];
		}

		return null;
	}

	/**
	 * Lazy-loader for "field map" that provides field_id => index of $this->field
	 *
	 * @since 1.5.0.7
	 *
	 * @return array
	 */
	protected function get_field_map(){
        $this->get_fields();
        if(
            empty( $this->field_map ) && ! empty( $this->fields )
            || ! empty( $this->fields ) && count( $this->fields ) !== count( $this->field_map )

        ){
            /** @var Caldera_Forms_Entry_Field $field */
            foreach ( $this->fields as $index => $field ){
                if ( ! isset( $this->field_map[ $field->field_id ] ) ) {
                    $this->field_map[$field->field_id] = $index;
                }
            }
        }

        return $this->field_map;

	}

    /**
     * Find field by field ID
     *
     * @since unknown
     *
     * @param $field_id
     * @return mixed|null
     */
	private function find_field_index( $field_id ){
		$this->get_field_map();
		if( ! empty( $this->field_map ) && isset( $this->field_map[ $field_id ] ) ){
			return $this->field_map[ $field_id ];
		}

		return null;
	}

    /**
     * Get the WPDB instance
     *
     * @since 1.5.8
     *
     * @return WPDB|false
     */
	protected function get_wpdb(){
        if( false !== $this->wpdb && ! is_object( $this->wpdb ) ){
            global $wpdb;
            if ( is_object( $wpdb )) {
                $this->wpdb = $wpdb;
            } else {
                $this->wpdb = false;
            }
        }

        return $this->wpdb;
    }
}









