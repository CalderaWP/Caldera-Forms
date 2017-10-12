<?php
/**
 * A collection of entries for one form
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Entry_Entries {

	/**
	 * Form config
	 *
	 * @since 1.4.0
	 *
	 * @var array
	 */
	protected $form;

	/**
	 * Holds all entries, index by status and then page
	 *
	 * @since 1.4.0
	 *
	 * @var array
	 */
	protected $entries = array();

	/**
	 * Holds totals by page
	 *
	 * @since 1.4.0
	 *
	 * @var array
	 */
	protected $totals = array();

	/**
	 * Allowed statuses
	 *
	 * @todo need to make sure this matches in other places, use filter or something
	 *
	 * @since 1.4.0
	 *
	 * @var array
	 */
	protected $statuses = array(
		'active',
		'pending',
		'trash'
	);

	/**
	 * Total per page
	 *
	 * @since 1.4.0
	 *
	 * @var int
	 */
	protected $perpage;

	/**
	 * Caldera_Forms_Entry_Entries constructor.
	 *
	 * @since 1.4.0
	 *
	 * @param array $form Form config
	 * @param int $perpage Number of entries per pags
	 */
	public function __construct( array $form, $perpage ) {
		$this->form    = $form;
		$this->perpage = $perpage;
		$this->prepare_entries_prop();

	}

	/**
	 * Get one page of results
	 *
	 * @since 1.4.0
	 *
	 * @param int $page
	 * @param string $status
	 *
	 * @return array
	 */
	public function get_page( $page = 1, $status = 'active' ) {
		$page = (int) $page;
		if ( ! in_array( $status, $this->statuses ) ) {
			return array();
		}

		if ( empty( $this->entries[ $status ][ $page ] ) ) {
			$this->query_page( $page, $status );
		}

		return $this->entries[ $status ][ $page ];
	}

	/**
	 * Get an entry row merging entry and field value
	 *
	 * This is for use inside of Caldera_Forms_Admin::get_entries() for backwards compat reasons
	 *
	 * @since 1.4.0
	 *
	 * @param int $page What page of results to get
	 * @param int $entry_id ID of entry
	 * @param string $status Optional. Status, default is 'active'
	 *
	 * @return array
	 */
	public function get_rows( $page, $entry_id, $status = 'active' ) {
		$page = (int) $page;
		if ( ! in_array( $status, $this->statuses ) ) {
			return array();
		}

		if ( ! isset( $this->entries[ $status ][ $page ] ) || empty(  $this->entries[ $status ][ $page ] ) ) {
			$this->query_page( $page, $status );
		}

		if ( ! isset( $this->entries[ $status ][ $page ] ) || ! isset( $this->entries[ $status ][ $page ][ $entry_id ] ) ) {
			return array();
		}

		$data = $_entry = array();
		/** @var Caldera_Forms_Entry $entry */
		$entry = $this->entries[ $status ][ $page ][ $entry_id ];

		foreach ( $entry->get_entry()->to_array() as $key => $value ) {
			$_entry[ '_' . $key ] = $value;
		}

		$field_values = $entry->get_fields();
		if ( ! empty( $field_values ) ) {
			/** @var Caldera_Forms_Entry_Field $_field */
			foreach ( $field_values as $_field ) {
				$field  = array_merge( $_field->to_array( false ), $_entry );
				$data[] = (object) $field;
			}
		}

		return $data;

	}

	/**
	 * Get total number of entries with a given status
	 *
	 * @since 1.4.0
	 *
	 * @param string $status Which status
	 *
	 * @return int
	 */
	public function get_total( $status ) {
		if ( ! in_array( $status, $this->statuses ) ) {
			return 0;
		}

		return $this->totals[ $status ];
	}

	/**
	 * Query for a page of results
	 *
	 * @since 1.4.0
	 *
	 * @param int $page What page
	 * @param string $status Which status
	 */
	protected function query_page( $page, $status ) {
		global $wpdb;
		$table    = $wpdb->prefix . "cf_form_entries";
		$offset   = ( $page - 1 ) * $this->perpage;
		$limit    = $offset . ',' . $this->perpage;
		$sql      = $wpdb->prepare( "SELECT * FROM $table WHERE `form_id` = %s AND `status` = %s ORDER BY `datestamp` DESC LIMIT " . $limit . ";", $this->form[ 'ID' ], $status );
		$_entries = $wpdb->get_results( $sql );
		if ( ! empty( $_entries ) ) {
			$this->entries[ $status ][ $page ] = array();
			foreach ( $_entries as $_entry ) {
				$entry                                                  = new Caldera_Forms_Entry_Entry( $_entry );
				$this->entries[ $status ][ $page ][ (int) $_entry->id ] = new Caldera_Forms_Entry( $this->form, $entry->id, $entry );
			}

		}
	}

	/**
	 * Query for total rows
	 *
	 * @since 1.4.0
	 *
	 * @param string $status Which status
	 *
	 * @return int
	 */
	protected function query_total( $status ) {
		return Caldera_Forms_Entry_Bulk::count( $this->form[ 'ID' ], $status );
	}

	/**
	 * Populates the entries property of this class with arrays per status and per page as needed.
	 *
	 * @since 1.4.0
	 */
	protected function prepare_entries_prop() {
		foreach ( $this->statuses as $status ) {
			$total                    = $this->query_total( $status );
			$this->totals[ $status ]  = $total;
			$pages                    = ceil( $total / $this->perpage );
			$this->entries[ $status ] = array();
			if ( 1 >= $pages ) {
				$this->entries[ $status ] = array( 1 => array() );
			} else {
				$this->entries[ $status ] = array_fill( 1, $pages, array() );
			}

		}

	}

}
