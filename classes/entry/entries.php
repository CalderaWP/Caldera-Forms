<?php
use ingot\api\internal\test_group\get;

/**
 * Singleton for holding Caldera_Forms_Entry instances currently in use
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Entry_Entries {

	protected $form;

	protected $entries = array();

	protected $totals = array();

	protected $statuses = array(
		'active',
		'pending',
		'trash'
	);

	protected $perpage;

	public function __construct( array $form,  $perpage  ){
		$this->form = $form;
		$this->perpage = $perpage;
		$this->prepare_entries_prop();

	}

	protected function prepare_entries_prop(){
		foreach( $this->statuses as $status ){
			$total = $this->query_total( $status );
			$this->totals[ $status ] = $total;
			$pages = ceil( $total / $this->perpage );
			$this->entries[ $status ] = array();
			if( 1 == $pages ){
				$this->entries[ $status ] = array( 1 => array( ) );
			}else{
				$this->entries[ $status ] = array_fill( 1, $pages, array() );
			}

		}

	}

	public function get_page( $page = 1, $status = 'active' ){
		$page = (int) $page;
		if( ! in_array( $status, $this->statuses   )
		   // || ! array_key_exists( $page, $this->entries[ $status ][ $page ])
		){
			return array();
		}

		if( empty(  $this->entries[ $status][ $page ] ) ){
			$this->query_page( $page, $status );
		}

		return $this->entries[ $status ][ $page ];
	}

	public function get_rows( $page, $entry_id, $status = 'active' ){
		$page = (int) $page;
		if( ! in_array( $status, $this->statuses ) ){
			return array();
		}

		if( ! isset( $this->entries[ $status ][ $page ] ) ){
			$this->query_page( $page, $status );
		}

		if( ! isset( $this->entries[ $status ][ $page ] ) || ! isset( $this->entries[ $status ][ $page ][ $entry_id ] ) ){
			return array();
		}

		$data =  $_entry = array();
		/** @var Caldera_Forms_Entry $entry */
		$entry = $this->entries[ $status ][ $page ][ $entry_id ];

		foreach( $entry->get_entry()->to_array() as $key => $value ){
			$_entry[  '_' . $key ] = $value;
		}

		/** @var Caldera_Forms_Entry_Field $_field */
		foreach( $entry->get_fields() as $_field ){
			$field = array_merge( $_field->to_array(), $_entry  );
			$data[] = (object) $field;
		}

		return $data;




	}

	public function get_total( $status ){
		if( ! in_array( $status, $this->statuses   ) ){
			return 0;
		}

		return $this->totals[ $status ];
	}

	protected function query_page( $page, $status  ){
		global $wpdb;
		$table = $wpdb->prefix ."cf_form_entries";
		$offset = ($page - 1) * $this->perpage;
		$limit = $offset . ',' . $this->perpage;
		$sql = $wpdb->prepare("SELECT * FROM $table WHERE `form_id` = %s AND `status` = %s ORDER BY `datestamp` DESC LIMIT " . $limit . ";", $this->form[ 'ID' ], $status );
		$_entries = $wpdb->get_results( $sql );
		if( ! empty( $_entries ) ){
			$this->entries[ $status ][ $page ] = array();
			foreach( $_entries as $_entry ){
				$entry = new Caldera_Forms_Entry_Entry( $_entry );
				$this->entries[ $status ][ $page ][ (int) $_entry->id ] = new Caldera_Forms_Entry( $this->form, $entry->id, $entry );
			}

		}
	}



	protected function query_total( $status ){
		global $wpdb;
		$sql = $wpdb->prepare("SELECT COUNT(`id`) AS `total` FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `form_id` = %s AND `status` = %s;", $this->form[ 'ID' ], $status );
		$total = $wpdb->get_var( $sql );
		return (int) $total;
	}
}
