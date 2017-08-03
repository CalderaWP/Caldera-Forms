<?php

/**
 * DB abstraction for form configs -- DON'T use directly. Use Caldera_Forms_Forms class
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2017 CalderaWP LLC
 */
class Caldera_Forms_DB_Form extends Caldera_Forms_DB_Base {

	/**
	 * Name of primary index
	 *
	 * @since 1.5.3
	 *
	 * @var string
	 */
	protected $index = 'id';

	/**
	 * Name of table
	 *
	 * @since 1.5.3
	 *
	 * @var string
	 */
	protected $table_name = 'cf_forms';

	/**
	 * Main instance
	 *
	 * @since 1.5.3
	 *
	 * @var Caldera_Forms_DB_Form
	 */
	protected static $instance;

	/**
	 * Caldera_Forms_DB_Form constructor.
	 *
	 * @since 1.5.3
	 */
	protected function __construct(){
		$this->has_meta = false;
		$this->set_primary_fields();
	}


	/**
	 * Set primary fields property
	 *
	 * @since 1.5.3
	 */
	protected function set_primary_fields(){
		$this->primary_fields  = array(
			'form_id'    => array(
				'%s',
				'strip_tags'
			),
			'type'    => array(
				'%s',
				array( $this, 'validate_type' )
			),
			'config' => array(
				'%s',
				'trim'
			)
		);
	}

	/**
	 * Get main instance
	 *
	 * @since 1.5.3
	 *
	 * @return Caldera_Forms_DB_Form
	 */
	public static function get_instance(){
		if( ! self::$instance ){
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Create new entry
	 *
	 * @since 1.5.3
	 *
	 * @param array $data
	 *
	 * @return bool|int|null
	 */
	public function create( array $data ){
		$data[ 'config' ] = $this->prepare_config( $data[ 'config' ] );
		return parent::create( $data );
	}

	/**
	 * Get a saved form by DB ID not form ID
	 *
	 * @since 1.5.3
	 *
	 * @param array|int $id
	 *
	 * @return array|null
	 */
	public function get_record( $id ){
		$record = parent::get_record( $id );
		$record = $this->prepare_found( $record );

		return $record;
 	}


	/**
	 * Update saved
	 *
	 * @since 1.5.3
	 *
	 * @param array $data
	 * @param bool $convert_primary Optional. If true, the default, current primary record will be changed to a revision.
	 *
	 * @return false|int
	 */
 	public function update( array $data ){
	    global $wpdb;
	    $table_name = $this->get_table_name();

	    $form_id = $data[ 'form_id' ];
	    $db_id = $data[ 'db_id' ];

	    unset( $data[ 'db_id' ] );
	    unset( $data[ 'config' ][ 'db_id' ] );
	    unset( $data[ 'config' ][ 'type' ] );

	    /**
		 * Should form revision be saved?
		 *
		 * @since 1.5.4
		 *
		 * @param bool $save_revision Should revision be saved?
		 * @param string $form_id ID of form being saved
		 *
		 */
	    $save_revision = apply_filters( 'caldera_forms_save_revision', true, $form_id );

	    if( $save_revision ){
		    $old_update = $wpdb->update( $table_name, array( 'type' => 'revision' ), array( 'form_id' => $form_id ) );
		    $data[ 'config' ] = $this->prepare_config( $data[ 'config' ] );
		    $data[ 'type' ] = 'primary';
		    $updated = parent::create( $data );

	    }else{
	    	$updated = $wpdb->update( $table_name,
			    array( 'config' => $this->prepare_config( $data[ 'config' ] ) ),
		        array( 'id' => $db_id )
		    );
	    }



	    return $updated;

    }

	/**
	 * Update type
	 *
	 * @since 1.5.4
	 *
	 * @param string $type Type primary|revision
	 * @param int $id Row ID
	 *
	 * @return false|int
	 */
    protected function update_type( $type, $id ){
	    global $wpdb;
	    $table_name = $this->get_table_name();
	    return $wpdb->update( $table_name, array( 'type' => $type ), array( 'id' => $id ) );
    }

    /**
     * Make a revision the primary for this form
     *
     * @since 1.5.3
     *
     * @param int $revision_id Revision ID
     *
	 * @return false|int
	 */
    public function make_revision_primary( $revision_id ){

	    $revision = $this->get_record( $revision_id );
	    if( ! empty( $revision ) ){
		    $primary = $this->get_by_form_id( $revision[ 'form_id' ] );
		    $old_update = $this->update_type( 'revision', $primary[ 'id' ] );
	    }

	    return $this->update_type( 'primary', $revision_id );

    }

	/**
	 * Get a form -- or a collection of form revisions by form ID
	 *
	 * @since 1.5.3
	 *
	 * @param string $form_id Form ID
	 * @param bool $primary_only Optional. If only primary form should be returned.
	 *
	 * @return array|bool
	 */
 	public function get_by_form_id( $form_id, $primary_only = true ){
 		global $wpdb;
 		$table_name = $this->get_table_name();
 		$sql = $wpdb->prepare( "SELECT * FROM $table_name WHERE `form_id` = '%s'", $form_id );
	    if( $primary_only ){
	    	$sql .= ' AND `type` = "primary" ORDER BY `id` DESC';
	    }

	    $found = $wpdb->get_results( $sql, ARRAY_A );

	    if( empty( $found ) ){
	    	return false;
	    }

	    if( $primary_only ){
	    	if( 1 < count( $found ) ){
				foreach ( $found as $i => $data ){
					if( 0 == $i ){
						continue;
					}
					$this->update_type( 'revision', $data[ 'id' ] );

				}
		    }
		    return $this->prepare_found( $found[0] );
	    }

	    $forms = array();
	    foreach (  $found as $form_data ){
	    	$forms[] = $this->prepare_found( $form_data );
	    }

	    return $forms;



    }

	/**
	 * Delete all forms, including revisions, by form ID
	 *
	 * @since 1.5.3
	 *
	 * @param string $form_id Form ID
	 *
	 * @return bool
	 */
    public function delete_by_form_id($form_id ){
    	global  $wpdb;
	    $rows =  $wpdb->delete( $this->get_table_name(), array(
	    	'form_id' => $form_id
	    ));
	    if( $rows >= 1 ){
	    	return true;
	    }

	    return false;
    }

	/**
	 * Delete one or more form configs, including revisions by id(s)
	 *
	 * @since 1.5.3
	 *
	 * @param int|array $ids Id or array of IDs -- DB id not form ID
	 *
	 * @return bool|false|int
	 */
    public function delete( $ids ){
    	if( is_numeric( $ids ) ){
    		$ids = array( $ids );
	    }

	    if( ! is_array( $ids ) ){
	    	return false;
	    }

	    global  $wpdb;

	    foreach ( $ids as &$id ){
	    	$id = absint( $id );
	    }

	    $in = implode( ',', $ids );

	    $table = $this->get_table_name();
	    $sql = $wpdb->prepare( "DELETE FROM $table WHERE `id` IN( %s )", $in );
	    return $wpdb->query($sql);

    }

	/**
	 * Validate type when saving
	 *
	 * @since 1.5.3
	 *
	 * @param string $type Type valid is primary or revison
	 *
	 * @return string
	 */
	public function validate_type( $type ){
		$types = array(
			'primary',
			'revision',
		);
		if( ! in_array( $type, $types ) ){
			return 'primary';
		}

		return $type;
	}

	/**
	 * Prepare form condfig to be saved
	 *
	 * @since 1.5.3
	 *
	 * @param array $config Form config
	 *
	 * @return string
	 */
	protected function prepare_config( $config ){
		return serialize( stripslashes_deep( $config ) );

	}

	/**
	 * After query from DB preprare record for use
	 *
	 * @since 1.5.3
	 *
	 * @param $record
	 *
	 * @return array
	 */
	protected function prepare_found( $record ){
		if ( is_array( $record ) && isset( $record[ 'config' ] ) ) {
			$record[ 'config' ] = maybe_unserialize( $record[ 'config' ] );

			return $record;
		}

		return $record;

	}

}