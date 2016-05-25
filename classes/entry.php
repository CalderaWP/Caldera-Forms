<?php


class Caldera_Forms_Entry {
	protected $form;

	protected $entry_id;

	protected $fields;

	protected $meta;

	/**
	 * @var Caldera_Forms_Entry_Entry
	 */
	protected $entry;


	public function __construct( array  $form, $entry_id = false ) {
		$this->form = $form;
		if( is_numeric( $entry_id  ) ){
			$this->entry_id = $entry_id;
			$this->inflate();
		}
	}

	public function set_entry_object( Caldera_Forms_Entry_Entry $entry ){
		$this->entry = $entry;
	}

	public function get_form_id(){
		return $this->form[ 'id' ];
	}

	public function get_entry(){
		return $this->entry;
	}

	public function get_fields(){
		return $this->fields;
	}

	public function get_meta(){
		return $this->meta;
	}

	public function get_entry_id(){
		return $this->entry_id;
	}

	public function inflate(){
		$this->find_entry();
		if( is_numeric(  $this->entry_id ) ){
			$this->find_fields();
			$this->find_metas();
		}
	}

	protected function find_entry(){
		global $wpdb;
		$sql = $wpdb->prepare( "SELECT * FROM %s WHERE `entry_id` = %d", $wpdb->prefix . 'cf_form_entries', $this->entry_id  );
		$results = $wpdb->get_results( $sql, ARRAY_A );
		$this->entry = new Caldera_Forms_Entry_Entry( );
		//probably should be $results[0]
		foreach ( $results as $field => $vaule ){
			$this->entry->$field = $vaule;
		}
	}

	protected function find_fields(){
		global $wpdb;
		$sql = $wpdb->prepare( "SELECT * FROM %s WHERE `entry_id` = %d", $wpdb->prefix . 'cf_form_entry_values', $this->entry_id  );
		$results = $wpdb->get_results( $sql, ARRAY_A );
		if( ! empty( $results ) ){
			foreach( $results as $result ){
				$meta = new Caldera_Forms_Entry_Meta();
				foreach( $result as $field => $value ){
					$meta->$field = $value;
				}
				$this->meta = $meta;
			}
		}
	}

	protected function find_metas(){
		global $wpdb;
		$sql = $wpdb->prepare( "SELECT * FROM %s WHERE `entry_id` = %d", $wpdb->prefix . 'cf_form_entry_meta', $this->entry_id  );
		$results = $wpdb->get_results( $sql, ARRAY_A );
		if( ! empty( $results ) ){
			foreach( $results as $result ){
				$meta = new Caldera_Forms_Entry_Meta();
				foreach( $result as $field => $value ){
					$meta->$field = $value;
				}
				$this->meta = $meta;
			}
		}
	}

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

	public function add_field( Caldera_Forms_Entry_Field $item ){
		$this->fields[] = $item;
	}

	public function add_meta( Caldera_Forms_Entry_Meta $item ){
		$this->meta[] = $item;
	}

	protected function create_entry() {
		global $wpdb;
		if( null == $this->entry ) {
			//@todo some error or exception or something
			return;
		}
		$wpdb->insert( $wpdb->prefix . 'cf_form_entries', $this->entry->to_array() );
		$this->entry = $wpdb->insert_id;
	}

	protected function save_field( Caldera_Forms_Entry_Field $field ){
		$field->entry_id = $this->entry_id;
		global $wpdb;
		$wpdb->insert($wpdb->prefix . 'cf_form_entry_values', $field->to_array() );
	}

	protected function save_meta( Caldera_Forms_Entry_Meta $meta ){
		$meta->entry_id = $this->entry_id;
		global $wpdb;
		$wpdb->insert($wpdb->prefix . 'cf_form_entry_meta',  $meta->to_array() );
	}

}









