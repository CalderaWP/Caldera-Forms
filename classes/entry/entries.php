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


	protected static $instance;

	protected $entries;


	public static function get_instance(){
		if( null == self::$instance ){
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * @param $id
	 *
	 * @return Caldera_Forms_Entry|void
	 */
	public function get_entry( $id ){
		if( isset( $this->entries[ $id ] ) ){
			return $this->entries[ $id ];
		}
	}

	public function is_set( $id ){
		return isset( $this->entries[ $id ] );
	}

	public function set_entry( Caldera_Forms_Entry $entry ){
		$this->entries[ $entry->get_entry_id() ] = $entry;
 	}

	public function get_or_make( $id, $form ){
		if( $this->is_set( $id ) ){
			return $this->get_entry( $id );
		}else{
			$_entry = new Caldera_Forms_Entry( $form, $id );
			$found = $_entry->found();
			if( ! $found ){
				$this->create_entry_object( $form );
			}

		}
		
	}

	/**
	 * @param array $form
	 *
	 * @return Caldera_Forms_Entry_Entry
	 */
	protected function create_entry_object( array $form ) {
		$_entry            = new Caldera_Forms_Entry_Entry();
		$_entry->form_id   = $form[ 'ID' ];
		$_entry->datestamp = current_time( 'mysql' );
		$_entry->status    = 'pending';
		$_entry->user_id   = get_current_user_id();
		return $_entry;
	}
}
