<?php
/**
 * Base class for database interactions via custom table.
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
abstract class Caldera_Forms_DB_Base {

	/**
	 * Primary fields
	 *
	 * @since 1.3.5
	 *
	 * @var array
	 */
	protected $primary_fields = array();

	/**
	 * Meta fields
	 *
	 * @since 1.3.5
	 *
	 * @var array
	 */
	protected $meta_fields = array();

	/**
	 * Meta keys
	 *
	 * @since 1.3.5
	 *
	 * @var array
	 */
	protected $meta_keys = array();

	/**
	 * Name of primary index
	 *
	 * @since 1.3.5
	 *
	 * @var string
	 */
	protected $index;

	/**
	 * Name of table
	 *
	 * NOTE: Don't call this, ever. Use $this->get_table_name() so prefix and possible suffix are extended.
	 *
	 * @since 1.3.5
	 *
	 * @var string
	 */
	protected $table_name;

	/**
	 * Flag to designate that there is a meta table
	 *
	 * @since 1.5.0
	 *
	 * @var bool
	 */
	protected $has_meta = true;

	/**
	 * Constructor -- protected to force singleton upon subclasses.
	 */
	protected function __construct(){}

	/**
	 * Get name of table with prefix
	 *
	 * @since 1.3.5
	 *
	 * @param bool|false $meta Whether primary or meta table name is desired. Default is false, which returns primary table
	 *
	 * @return string
	 */
	public function get_table_name( $meta = false ){
		global $wpdb;

		$table_name = $wpdb->prefix . $this->table_name;
		if( $meta ){
			$table_name .= '_meta';
		}

		return $table_name;
	}

	/**
	 * Get names of fields for this collection
	 *
	 * @since 1.3.5
	 *
	 * @return array
	 */
	public function get_fields(){

		$fields[ 'primary' ] = array_keys( $this->primary_fields );

		if ( $this->has_meta ) {
			$fields[ 'meta_keys' ] = array_keys( $this->meta_keys );

			/**
			 * Filter the allowed meta keys that can be saved
			 *
			 * @since 1.4.0
			 *
			 * @param array $fields Allowed fields
			 * @param string $table_name Name of table
			 */
			$fields[ 'meta_fields' ] = apply_filters( 'caldera_forms_db_meta_fields', array_keys( $this->meta_fields ), $this->get_table_name( true ) );

		}

		return $fields;
	}

	/**
	 * Create a new entry with meta (if supported)
	 *
	 * @since 1.3.5
	 *
	 * @param array $data Data to save
	 *
	 * @return bool|int|null
	 */
	public function create( array $data ){

		$_data = $_meta =  array(
			'fields' => array(),
			'formats' => array()
		);
		
		foreach( $data as $field => $datum ){
			if( is_null( $datum ) || is_array( $datum ) || is_object( $datum )  ){
				$datum = '';
			}

			if( $this->valid_field( $field, 'primary' ) ){
				$_data[ 'fields' ][ $field ] = call_user_func( $this->primary_fields[ $field ][1], $datum );
				$_data[ 'formats' ][] = $this->primary_fields[ $field ][0];
			}

			if( $this->has_meta && $this->valid_field( $field, 'meta_key' ) ){
				$_meta[ 'fields' ][ $field ][ 'value' ] = call_user_func( $this->meta_keys[ $field ][1], $datum );
				$_meta[ 'fields' ][ $field ][ 'format' ] = $this->meta_keys[ $field ][0];
			}

		}

		$id = $this->save( $_data );
		if( is_numeric( $id ) && $this->has_meta && ! empty( $_meta[ 'fields' ] ) ){
			
			foreach( $_meta as $meta ){

				foreach( $this->meta_keys as $meta_key => $field ) {
					$_meta_row = array();
					if( isset( $meta[ $meta_key ], $meta[ $meta_key ][ 'value' ] ) ) {

						$_meta_row[ 'fields' ][ 'meta_key' ] = $meta_key;
						$_meta_row[ 'formats' ][] = '%s';

						$_meta_row[ 'fields' ][ 'meta_value' ] = $_meta[ 'fields' ][ $meta_key ][ 'value' ];
						$_meta_row[ 'formats' ][] = $this->meta_keys[ $meta_key ][0];


						$_meta_row[ 'fields' ][ $this->index ] = $id;
						$_meta_row[ 'formats' ][] = '%d';

						$this->save( $_meta_row, true );

					}

				}

			}


		}

		return $id;

	}

	/**
	 *  Save a row
	 *
	 * @since 1.3.5
	 *
	 * @param array $data Row data to save
	 * @param bool|false $meta
	 *
	 * @return bool|int|null
	 */
	protected function save( array $data, $meta = false ){
		if( $meta && ! $this->has_meta  ){
			return false;
		}

		if( ! isset( $data[ 'formats' ] ) ){
			foreach ( $this->primary_fields as $name => $args ){
				$data[ 'formats' ][] = $args[0];
			}
		}
		if( ! empty( $data ) ){
			global $wpdb;
			$inserted = $wpdb->insert(
				$this->get_table_name( $meta ),
				$data[ 'fields' ],
				$data[ 'formats' ]
			);
			if( $inserted ){
				return $wpdb->insert_id;
			}else{
				return false;
			}

		}else{
			return null;
		}
	}


	/**
	 * Delete an entry from DB
	 *
	 * @since 1.3.5
	 *
	 * @param int $id ID of entry
	 *
	 * @return bool
	 */
	public function delete( $id ){
		global $wpdb;
		$deleted = $wpdb->delete( $this->get_table_name(), array( 'ID' => $id ) );
		if( false != $deleted ){
			return true;

		}

	}

	/**
	 * Get a complete record or records -- primary or meta fields
	 *
	 * @since 1.3.5
	 *
	 * @param int|array $id ID of entry, or an array of IDs.
	 *
	 * @return array|null
	 */
	public function get_record( $id ){
		$primary = $this->get_primary( $id );
		if( is_array( $primary ) ) {
			if( is_array( $id ) ) {
				$data = array();
				foreach( $primary as $record ){
					if (  $this->has_meta ) {
						$meta   = $this->get_meta( $record[ 'ID' ] );
						$record = $this->add_meta_to_record( $meta, $record );
					}

					$data[] = $record;
				}

				return $data;

			}else{
				$meta = null;
				if ( $this->has_meta ) {
					$meta = $this->get_meta( $id );
				}

				$data = $primary;
				if( $this->has_meta && is_array( $meta ) ){
					return $this->add_meta_to_record( $meta, $data );

				}else{
					return $primary;
				}

			}

		}


	}

	/**
	 * Get primary entry row from DB
	 *
	 * @since 1.3.5
	 *
	 * @param int|array $id ID of entry, or an array of IDs.
	 *
	 * @return array|null
	 */
	public function get_primary( $id ){
		global $wpdb;
		$table_name = $this->get_table_name();
		if( is_array( $id ) ) {
			$single = false;
			$sql = "SELECT * FROM $table_name WHERE `ID` IN(" . $this->escape_array( $id ) . ")";
		}else{
			$single = true;
			$sql = $wpdb->prepare( "SELECT * FROM $table_name WHERE `ID` = %d", absint( $id ) );
		}


		$results = $wpdb->get_results( $sql, ARRAY_A );
		if( ! empty( $results ) && $single ){
			$results = $results[0];
		}
		return $results;
	}

	/**
	 * Get meta rows from DB
	 *
	 * @since 1.3.5
	 *
	 * @param int|array $id ID of entry, or an array of IDs.
	 * @param string|bool $key Optional. If false, the default all the metas are returned.  Use name of key to get one specific key.
	 * @return array|null|object
	 */
	public function get_meta( $id, $key = false ){
		if( ! $this->has_meta ){
			return null;
		}

		global $wpdb;
		$table_name = $this->get_table_name( true );
		if( is_array( $id ) ) {
			$sql = "SELECT * FROM $table_name WHERE`$this->index` IN(" . $this->escape_array( $id ) . ")";
		}else{
			$sql = $wpdb->prepare( "SELECT * FROM $table_name WHERE `$this->index` = %d", absint( $id ) );
		}

		$results = $wpdb->get_results( $sql, ARRAY_A );

		if( ! empty( $results ) && is_string( $key ) ){
			return $this->reduce_meta( $results, $key );

		}

		return $results;
	}

	/**
	 * Reduce a meta row to the meta value of a key
	 *
	 * @since 1.3.5
	 *
	 * @param array $results Meta row
	 * @param string $key Name of meta key
	 *
	 * @return array|void
	 */
	protected function reduce_meta( array $results, $key ){
		if( $this->valid_field( $key, 'meta_key' ) ){
			$values = array_combine( wp_list_pluck( $results, 'meta_key' ), wp_list_pluck( $results, 'meta_value' ) );
			if( isset( $values[ $key ] ) ){
				return $values[ $key ];
			}

		}
	}



	/**
	 * Check if a field is valid
	 *
	 * @since 1.3.5
	 *
	 * @param string  $field Name of field to check
	 * @param string $type Type of field. Options: primary|meta|meta_key
	 *
	 * @return bool
	 */
	protected function valid_field( $field, $type = 'primary' ) {
		switch( $type ){
			case 'primary' :
				return array_key_exists( $field, $this->primary_fields );
			break;
			case 'meta' :
				if( ! $this->has_meta ){
					return false;
				}
				return array_key_exists( $field, $this->meta_fields );
			break;
			case 'meta_key' :
				if( ! $this->has_meta ){
					return false;
				}
				return array_key_exists( $field, $this->meta_keys );
			break;
			default:
				return false;
			break;
		}

	}

	/**
	 * Prepare an array for use with IN() or NOT IN()
	 *
	 * Creates comma separated string with numeric values of the all keys.
	 *
	 * @since 1.3.5
	 *
	 * @param array $array
	 *
	 * @return string
	 */
	protected function escape_array( array $array ) {
		global $wpdb;
		$escaped = array();
		foreach ( $array as $k => $v ) {
			if ( is_numeric( $v ) ) {
				$escaped[] = $wpdb->prepare( '%d', $v );
			}
		}

		return implode( ',', $escaped );
	}

	/**
	 * Add meta value to record by key
	 *
	 * @since 1.3.5
	 *
	 * @param array $meta Meta data
	 * @param array $data Record
	 *
	 * @return mixed
	 */
	protected function add_meta_to_record( array $meta, array $data ) {
		if( ! $this->has_meta ){
			return false;
		}
		if ( ! empty( $meta ) ) {
			foreach ( $this->meta_keys as $key => $field ) {
				$data[ $key ] = $this->reduce_meta( $meta, $key );
			}

			return $data;
		}
	}

	/**
	 * @return int|null
	 */
	public function highest_id(){
		global $wpdb;
		$table_name = $this->get_table_name();
		$results = $wpdb->get_results(  "SELECT max(ID) FROM $table_name", ARRAY_N );
		if( is_array( $results ) && isset( $results[0], $results[0][0] ) ) {
			return $results[0][0];
		}

	}

	/**
	 * Query by meta key
	 *
	 * @since 1.4.5
	 *
	 * @param string $key Meta key to query by
	 * @param string $value Meta value to query for
	 *
	 * @return array|null
	 */
	protected function query_meta( $key, $value ){
		if( ! $this->has_meta ){
			return null;
		}

		global $wpdb;
		$table = $this->get_table_name( true );
		$sql = $wpdb->prepare( "SELECT * FROM $table WHERE  `meta_key` = '%s' AND `meta_value` = '%s' ", $key, $value );
		$r = $wpdb->get_results( $sql, ARRAY_A );
		return $r;

	}

}
