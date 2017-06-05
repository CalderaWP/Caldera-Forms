<?php


/**
 * Class Caldera_Forms_DB_Tables
 */
class Caldera_Forms_DB_Tables {

	/**
	 * @var wpdb
	 */
	protected  $wpdb;

	protected  $charset_collate;

	protected  $max_index_length = 191;


	public function __construct( wpdb $wpdb ){
		$this->wpdb = $wpdb;


	}

	/**
	 * Add CF tables if they are missing
	 *
	 * @since 1.5.1
	 */
	public function add_if_needed(){
		$missing = $this->find_missing_tables();
		if( empty( $missing ) ){
			return;
		}

		$this->set_charset();

		$search = $this->wpdb->prefix . 'cf_';
		foreach( $missing as $table ){
			call_user_func( array( $this, str_replace( $search, '', $table ) ) );
		}


	}

	/**
	 * Find any missing Caldera Forms tables
	 *
	 * @return array
	 */
	protected function find_missing_tables(){
		$tables = $this->wpdb->get_results( "SHOW TABLES", ARRAY_A );
		$alltables = array();
		foreach ( $tables as $table ) {
			$alltables[] = implode( $table );
		}


		$missing_tables = array();
		foreach ( $this->get_tables_list()  as  $cf_table ){
			if( ! in_array( $cf_table, $alltables ) ){
				$missing_tables[] = $cf_table;
			}

		}

		return $missing_tables;

	}

	/**
	 * Get the list of Caldera Forms tables, with wpdb prefix
	 *
	 * @since 1.5.1
	 *
	 * @return array
	 */
	protected function get_tables_list(){

		$tables = array(
			'cf_form_entry_values',
			'cf_form_entry_meta',
			'cf_tracking',
			'cf_tracking_meta',
			'cf_form_entries',
			'cf_form_entry_values'
		);
		foreach ( $tables as &$table ){
			$table = $this->wpdb->prefix . $table;
		}

		return $tables;
	}

	/**
	 * Add cf_form_entry_values table
	 *
	 * Warning: does not check if it exists first, which could cause SQL errors.
	 *
	 * @since 1.5.1
	 */
	public function form_entry_values(){
		$this->set_charset();

		$values_table = "CREATE TABLE `" . $this->wpdb->prefix . "cf_form_entry_values` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`entry_id` int(11) NOT NULL,
				`field_id` varchar(20) NOT NULL,
				`slug` varchar(255) NOT NULL DEFAULT '',
				`value` longtext NOT NULL,
				PRIMARY KEY (`id`),
				KEY `form_id` (`entry_id`),
				KEY `field_id` (`field_id`),
				KEY `slug` (`slug`(" . $this->max_index_length . "))
				) " . $this->charset_collate . ";";

		dbDelta( $values_table );
	}

	/**
	 * Add cf_form_entry_meta table
	 *
	 * Warning: does not check if it exists first, which could cause SQL errors.
	 *
	 * @since 1.5.1
	 */
	public function form_entry_meta(){
		$this->set_charset();

		$meta_table = "CREATE TABLE `" . $this->wpdb->prefix . "cf_form_entry_meta` (
			`meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`entry_id` bigint(20) unsigned NOT NULL DEFAULT '0',
			`process_id` varchar(255) DEFAULT NULL,
			`meta_key` varchar(255) DEFAULT NULL,
			`meta_value` longtext,
			PRIMARY KEY (`meta_id`),
			KEY `meta_key` (meta_key(" . $this->max_index_length . ")),
			KEY `entry_id` (`entry_id`)
			) " . $this->charset_collate . ";";

		dbDelta( $meta_table );

	}
	/**
	 * Add the cf_tracking table
	 *
	 * Warning: does not check if it exists first, which could cause SQL errors.
	 *
	 * @since 1.5.1
	 */
	public function tracking(){
		$this->set_charset();
		$tacking_table = "CREATE TABLE `" . $this->wpdb->prefix . "cf_tracking` (
			`ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`form_id` varchar(255) DEFAULT NULL,
			`process_id` varchar(255) DEFAULT NULL,
			PRIMARY KEY (`ID`)
			) " . $this->charset_collate . ";";

		dbDelta( $tacking_table );
	}

	/**
	 * Add the cf_tracking_meta table
	 *
	 * Warning: does not check if it exists first, which could cause SQL errors.
	 *
	 * @since 1.5.1
	 */
	public function tracking_meta(){
		$this->set_charset();
		$meta_table = "CREATE TABLE `" . $this->wpdb->prefix . "cf_tracking_meta` (
			`meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`event_id` bigint(20) unsigned NOT NULL DEFAULT '0',
			`meta_key` varchar(255) DEFAULT NULL,
			`meta_value` longtext,
			PRIMARY KEY (`meta_id`),
			KEY `meta_key` (`meta_key`(" . $this->max_index_length . ")),
			KEY `event_id` (`event_id`)
			) " . $this->charset_collate . ";";

		dbDelta( $meta_table );
	}

	/**
	 * Add the cf_form_entries table
	 *
	 * Warning: does not check if it exists first, which could cause SQL errors.
	 *
	 * @since 1.5.1
	 */
	public function form_entries(){
		$this->set_charset();
		$entry_table = "CREATE TABLE `" . $this->wpdb->prefix . "cf_form_entries` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`form_id` varchar(18) NOT NULL DEFAULT '',
				`user_id` int(11) NOT NULL,
				`datestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`status` varchar(20) NOT NULL DEFAULT 'active',
				PRIMARY KEY (`id`),
				KEY `form_id` (`form_id`),
				KEY `user_id` (`user_id`),
				KEY `date_time` (`datestamp`),
				KEY `status` (`status`)
				) " . $this->charset_collate . ";";


		dbDelta( $entry_table );
	}

	/**
	 * Add cf_form_entries table
	 *
	 * Warning: does not check if it exists first, which could cause SQL errors.
	 *
	 * @since 1.5.1
	 */
	public function  cf_form_entries(){
		$this->set_charset();

		$entry_table = "CREATE TABLE `" . $this->wpdb->prefix . "cf_form_entries` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`form_id` varchar(18) NOT NULL DEFAULT '',
				`user_id` int(11) NOT NULL,
				`datestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`status` varchar(20) NOT NULL DEFAULT 'active',
				PRIMARY KEY (`id`),
				KEY `form_id` (`form_id`),
				KEY `user_id` (`user_id`),
				KEY `date_time` (`datestamp`),
				KEY `status` (`status`)
				) " . $this->charset_collate . ";";


		dbDelta( $entry_table );
	}


	/**
	 * Set the charset_collate property if not set
	 *
	 * @since 1.5.1
	 */
	protected function set_charset(  ){
		if( is_string( $this->charset_collate ) ){
			return;
		}
		$charset_collate = '';

		if ( ! empty( $this->wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET " . $this->wpdb->charset;
		}

		if ( ! empty( $this->wpdb->collate ) ) {
			$charset_collate .= " COLLATE " .$this->wpdb->collate;
		}

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$this->charset_collate = $charset_collate;
	}
}