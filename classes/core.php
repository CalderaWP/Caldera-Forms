<?php

/**
 * Caldera Forms.
 *
 * @package   Caldera_Forms
 * @author    David <david@digilab.co.za>
 * @license   GPL-2.0+
 * @link
 * @copyright 2014 David Cramer
 */

/**
 * Caldera_Forms Plugin class.
 * @package Caldera_Forms
 * @author  David Cramer <david@digilab.co.za>
 */

class Caldera_Forms {

	/**
	 * @var     string
	 */
	const VERSION = CFCORE_VER;

	/**
	 * @since 1.4.2
	 */
	const PLUGIN_SLUG = 'caldera-forms';

	/**
	 * @var      string
	 */
	protected $plugin_slug = 'caldera-forms';
	/**
	 * @var      string
	 */
	protected $screen_prefix = null;
	/**
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Used to track if system has initialized and prevent recursion in init_cf_internal() method.
	 *
	 * @since 1.3.5
	 *
	 * @var bool
	 */
	private  static $internal_init = false;

	/**
	 * Holds modal HTML to be loaded in footer
	 *
	 * @since 1.4.2
	 *
	 * @var string
	 */
	protected static $footer_modals;

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 */
	function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'template_redirect', array( $this, 'api_handler' ) );

		// add element & fields filters
		add_filter('caldera_forms_get_field_types', array( $this, 'get_internal_field_types'));
		add_filter('caldera_forms_get_form_processors', array( $this, 'get_form_processors'));
		add_filter('caldera_forms_submit_redirect_complete', array( $this, 'do_redirect'),10, 4);
		add_action('caldera_forms_edit_end', array($this, 'calculations_templates') );
		add_filter('caldera_forms_render_get_field', array( $this, 'auto_populate_options_field' ), 10, 2);
		add_filter('caldera_forms_render_get_field', array( $this, 'apply_conditional_groups' ), 10, 2);
		//add_filter('caldera_forms_render_get_field_type-radio', array( $this, 'auto_populate_options_field' ), 10, 2);
		//add_filter('caldera_forms_render_get_field_type-checkbox', array( $this, 'auto_populate_options_field' ), 10, 2);
		//add_filter('caldera_forms_render_get_field_type-dropdown', array( $this, 'auto_populate_options_field' ), 10, 2);
		//add_filter('caldera_forms_render_get_field_type-toggle_switch', array( $this, 'auto_populate_options_field' ), 10, 2);
		add_filter('caldera_forms_view_field_paragraph', 'wpautop' );

		// magic tags
		//add_filter('caldera_forms_render_magic_tag', array( $this, 'do_magic_tags'));
		// mailser
		add_filter('caldera_forms_get_magic_tags', array( $this, 'set_magic_tags'),1);
		add_filter('caldera_forms_mailer', array( $this, 'mail_attachment_check'),10, 3);

		// action
		add_action('caldera_forms_submit_complete', array( $this, 'save_final_form'),50);
		
		// find if profile is loaded
		add_action('wp_loaded', array( $this, 'cf_init_system'), 25 );
		add_action('wp', array( $this, 'cf_init_preview'));

		// render shortcode
		add_shortcode( 'caldera_form', array( $this, 'shortcode_handler') );
		// modal shortcode
		add_shortcode( 'caldera_form_modal', array( $this, 'shortcode_handler') );
		add_action( 'wp_footer', array( $this, 'render_footer_modals') );

		//emails
		add_action( 'caldera_forms_core_init', array( 'Caldera_Forms_Email_Settings', 'maybe_add_hooks' ) );
		add_action( 'caldera_forms_admin_footer', array( 'Caldera_Forms_Email_Settings', 'ui' ) );
		add_filter( 'pre_update_option__caldera_forms_email_api_settings', array( 'Caldera_Forms_Email_Settings', 'sanitize_save' ) );
		if( current_user_can( Caldera_Forms::get_manage_cap( 'admin' ) ) ) {
			add_action( 'wp_ajax_cf_email_save', array( 'Caldera_Forms_Email_Settings', 'save' ) );
		}

		add_action( 'caldera_forms_render_start', array( __CLASS__, 'easy_pods_queries_setup' ) );


		if( current_user_can( Caldera_Forms::get_manage_cap( 'admin' ) ) ) {
			$id = null;
			$view = false;
			if( isset( $_GET[ 'cf-email-preview' ], $_GET[ 'cf-email-preview-form' ] ) ){
				if( wp_verify_nonce( $_GET[ 'cf-email-preview' ], $_GET[ 'cf-email-preview-form' ] ) ) {
					$id = $_GET[ 'cf-email-preview-form' ];
					$view = true;
				}
			}

			new Caldera_Forms_Email_Previews( $id, $view );

		}
		

		/**
		 * Runs after Caldera Forms core is initialized
		 *
		 * @since 1.3.5.3
		 */
		do_action( 'caldera_forms_core_init' );

		/** Adding anything to this constructor after caldera_forms_core_init action is a violation of intergalactic law */

	}

	/**
	 * Load a form by ID or name
	 *
	 * @param string $id_name ID or name of form.
	 *
	 * @return array|null Form config array if found. If not null.
	 */
	public static function get_form( $id_name ){
		return Caldera_Forms_Forms::get_form( $id_name );
	}

	/**
	 * Load all forms
	 *
	 * @param bool $internal Optional. If false, the default, all forms are returned. If true, only those saved in DB are returned.
	 *
	 * @return mixed|void
	 */
	public static function get_forms( $internal = false ){
		return Caldera_Forms_Forms::get_forms( true, $internal );

	}

	/**
	 * Load a field from form
	 *
	 * @since 1.4.2
	 *
	 * @param array $form Form config
	 * @param string $field_base_id Field ID
	 *
	 * @return array
	 */
	public static function load_field( $form, $field_base_id ) {
		/**
		 * Filter the field setup before render
		 *
		 * @since unknown
		 *
		 * @param string $notice Notices HTML
		 * @param array $config Form config
		 */
		$field = apply_filters( 'caldera_forms_render_setup_field', $form[ 'fields' ][ $field_base_id ], $form );

		return $field;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain( $this->plugin_slug, FALSE, basename( CFCORE_PATH ) . '/languages');
	}

	/**
	 * Setup internals / AKA activate stuffs
	 *
	 */
	public static function init_cf_internal() {

		if( false == self::$internal_init ) {
			add_rewrite_tag('%cf_api%', '([^&]+)');
			add_rewrite_tag('%cf_entry%', '([^&]+)');
			// INIT API
			add_rewrite_rule('^cf-api/([^/]*)/([^/]*)/?','index.php?cf_api=$matches[1]&cf_entry=$matches[2]','top');
			add_rewrite_rule('^cf-api/([^/]*)/?','index.php?cf_api=$matches[1]','top');

			self::$internal_init = true;

			// check update version
			$db_version = get_option( 'CF_DB', 0 );
			$force_update = false;
			if( is_admin() && isset( $_GET[ 'cal_db_update' ] ) ) { // ensure that admin can only force update
				$force_update = (bool) wp_verify_nonce( $_GET[ 'cal_db_update' ] );
			}

			if( CF_DB > $db_version || $force_update ) {
				include_once CFCORE_PATH . 'includes/updater.php';
				if (  $db_version < 2 || $force_update  ) {
					caldera_forms_db_v2_update();
				}

				if( $db_version < 4 || $force_update ){
					self::activate_caldera_forms( true );
					caldera_forms_write_db_flag( 4 );
				}

			}


		}

	}
	/**
	 * Activate and setup plugin
	 *
	 * @param bool $force Optional. If true, tables are checked no matter what. Default is false
	 */
	public static function activate_caldera_forms( $force = false ){
		wp_schedule_event( time(), 'daily', 'caldera_forms_tracking_send_rows' );
		global $wpdb;

		// ensure urls are there
		self::init_cf_internal();

		$version = get_option('_calderaforms_lastupdate');

		// ensure rewrites
		flush_rewrite_rules();

		if ( false == $force && ! empty( $version ) ) {
			if( version_compare($version, CFCORE_VER) === 0 ){ // no change
				return;
			}
		}

		update_option('_calderaforms_lastupdate',CFCORE_VER);

		$charset_collate = '';

		if ( ! empty( $wpdb->charset ) ) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}

		if ( ! empty( $wpdb->collate ) ) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}
		
		/*
		 * Indexes have a maximum size of 767 bytes. Historically, we haven't need to be concerned about that.
		 * As of WordPress 4.2, however, we moved to utf8mb4, which uses 4 bytes per character. This means that an index which
		 * used to have room for floor(767/3) = 255 characters, now only has room for floor(767/4) = 191 characters.
		 */
		$max_index_length = 191;

		$tables = $wpdb->get_results("SHOW TABLES", ARRAY_A);
		foreach($tables as $table){
			$alltables[] = implode($table);
		}

		// meta table
		if(!in_array($wpdb->prefix.'cf_form_entry_meta', $alltables)){
			// create meta tables
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$meta_table = "CREATE TABLE `" . $wpdb->prefix . "cf_form_entry_meta` (
			`meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`entry_id` bigint(20) unsigned NOT NULL DEFAULT '0',
			`process_id` varchar(255) DEFAULT NULL,
			`meta_key` varchar(255) DEFAULT NULL,
			`meta_value` longtext,
			PRIMARY KEY (`meta_id`),
			KEY `meta_key` (meta_key(" . $max_index_length . ")),
			KEY `entry_id` (`entry_id`)
			) " . $charset_collate . ";";

			dbDelta( $meta_table );

		}

		// tracking table
		if ( ! in_array( $wpdb->prefix . 'cf_tracking', $alltables ) ) {
			// create meta tables
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			$tacking_table = "CREATE TABLE `" . $wpdb->prefix . "cf_tracking` (
			`ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`form_id` varchar(255) DEFAULT NULL,
			`process_id` varchar(255) DEFAULT NULL,
			PRIMARY KEY (`ID`)
			) " . $charset_collate . ";";

			dbDelta( $tacking_table );

		}

		//tracking meta
		if ( ! in_array( $wpdb->prefix . 'cf_tracking_meta', $alltables ) ) {
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$meta_table = "CREATE TABLE `" . $wpdb->prefix . "cf_tracking_meta` (
			`meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`event_id` bigint(20) unsigned NOT NULL DEFAULT '0',
			`meta_key` varchar(255) DEFAULT NULL,
			`meta_value` longtext,
			PRIMARY KEY (`meta_id`),
			KEY `meta_key` (`meta_key`(" . $max_index_length . ")),
			KEY `event_id` (`event_id`)
			) " . $charset_collate . ";";

			dbDelta( $meta_table );

		}


		if( !in_array($wpdb->prefix.'cf_form_entries', $alltables) || !in_array($wpdb->prefix.'cf_form_entry_values', $alltables) ){
			// create tables
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			if( !in_array($wpdb->prefix.'cf_form_entries', $alltables) ){
			
				$entry_table = "CREATE TABLE `" . $wpdb->prefix . "cf_form_entries` (
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
				) " . $charset_collate . ";";


				dbDelta( $entry_table );
			}

			if( !in_array($wpdb->prefix.'cf_form_entry_values', $alltables) ){
				
				$values_table = "CREATE TABLE `" . $wpdb->prefix . "cf_form_entry_values` (
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				`entry_id` int(11) NOT NULL,
				`field_id` varchar(20) NOT NULL,
				`slug` varchar(255) NOT NULL DEFAULT '',
				`value` longtext NOT NULL,
				PRIMARY KEY (`id`),
				KEY `form_id` (`entry_id`),
				KEY `field_id` (`field_id`),
				KEY `slug` (`slug`(" . $max_index_length . "))
				) " . $charset_collate . ";";

				dbDelta( $values_table );
			}

		}else{
			if($version >= '1.1.5'){
				return; // only if 1.1.4 or lower
			}
			// check for field_id from 1.0.4
			$columns = $wpdb->get_results("SHOW COLUMNS FROM `" . $wpdb->prefix . "cf_form_entry_values`", ARRAY_A);
			$fields = array();
			foreach($columns as $column){
				$fields[] = $column['Field'];
			}
			if(!in_array('field_id', $fields)){
				$wpdb->query( "ALTER TABLE `" . $wpdb->prefix . "cf_form_entry_values` ADD `field_id` varchar(20) NOT NULL AFTER `entry_id`;" );
				$wpdb->query( "CREATE INDEX `field_id` ON `" . $wpdb->prefix . "cf_form_entry_values` (`field_id`); ");
				// update all entries
				$forms = $wpdb->get_results("SELECT `id`,`form_id` FROM `" . $wpdb->prefix . "cf_form_entries`", ARRAY_A);
				$known = array();
				if( !empty($forms)){
					foreach($forms as $form){
						if(!isset($known[$form['form_id']])){
							$config = Caldera_Forms_Forms::get_form( $form['form_id'] );
							if(empty($config)){
								continue;
							}
							$known[$form['form_id']] = $config;
						}else{
							$config = $known[$form['form_id']];
						}

						foreach($config['fields'] as $field_id=>$field){
							$wpdb->update($wpdb->prefix . "cf_form_entry_values", array('field_id'=>$field_id), array('entry_id' => $form['id'], 'slug' => $field['slug']));
						}

					}
				}
			}
			// add status
			$columns = $wpdb->get_results("SHOW COLUMNS FROM `" . $wpdb->prefix . "cf_form_entries`", ARRAY_A);
			$fields = array();

			if(!in_array('status', $fields) && $version < '1.2.0' ){
				$wpdb->query( "ALTER TABLE `" . $wpdb->prefix . "cf_form_entries` ADD `status` varchar(20) NOT NULL DEFAULT 'active' AFTER `datestamp`;" );
				$wpdb->query( "CREATE INDEX `status` ON `" . $wpdb->prefix . "cf_form_entries` (`status`); ");
			}

		}

	}

	/**
	 * View a star rating form value
	 *
	 * @param int $value Value for star ratring
	 * @param array $field Field config
	 * @param array $form Form config
	 *
	 * @return string HTML markup
	 */
	public static function star_rating_viewer($value, $field, $form){

		$out = "<div style=\"color: " . $field['config']['color'] . "; font-size: 10px;display: inline;\" >";
		if(!empty($field['config']['number'])){
			for( $i = 1; $i <= $field['config']['number']; $i++){
				$star = 'raty-'.$field['config']['type'].'-off';
				if( $i<= $value){
					$star = 'raty-'.$field['config']['type'].'-on';
				}
				$out .= '<span data-alt="'.$i.'" class="'.$star.'" title="'.$i.'" style="margin-right: -2px;"></span> ';
			}
		}
		$out .= '</div>';

		return $out;
	}

	/**
	 * Output markup for file fields
	 *
	 * @param array $value Saved file paths
	 * @param array $field Field config
	 * @param array $form Form config
	 *
	 * @return string
	 */
	public static function handle_file_view($value, $field, $form){
		$out = array();
		foreach( (array) $value as $file_url ){
			$out[] = '<a href="' . $file_url .'" target="_blank">' . basename($file_url) .'</a>';
		}
		return implode(', ', $out );
	}



	/**
	 * Prepare email attachments
	 *
	 * @param array $mail Email data
	 * @param array $data Form data
	 * @param array $form For config
	 *
	 * @return array
	 */
	public static function mail_attachment_check($mail, $data, $form){
		foreach ( $form[ 'fields' ] as $field_id => $field ) {
			if ( ( $field[ 'type' ] == 'file' || $field[ 'type' ] == 'advanced_file' )  && isset( $field[ 'config' ][ 'attach' ] ) ) {
				$dir  = wp_upload_dir();
				$file = str_replace( $dir[ 'baseurl' ], $dir[ 'basedir' ], self::get_field_data( $field_id, $form ) );
				if ( is_array( $file ) ) {
					foreach ( $file as $a_file ) {
						if ( is_string( $a_file ) && file_exists( $a_file ) ) {
							$mail[ 'attachments' ][] = $a_file;

						}
					}

				} elseif ( is_string( $file ) && file_exists( $file ) ) {
					$mail[ 'attachments' ][] = $file;
				} else {
					if ( isset( $data[ $field_id ] ) && filter_var( $data[ $field_id ], FILTER_VALIDATE_URL ) ) {
						$mail[ 'attachments' ][] = $data[ $field_id ];
					} elseif ( isset( $_POST[ $field_id ] ) && filter_var( $_POST[ $field_id ], FILTER_VALIDATE_URL ) && 0 === strpos( $_POST[ $field_id ], $dir[ 'url' ] ) ) {
						$mail[ 'attachments' ][] = $_POST[ $field_id ];

					} else {
						continue;

					}
				}

			}
		}

		return $mail;

	}

	/**
	 * Check a captcha
	 *
	 * @param string $value Attempted captcha value
	 * @param array $field Field config
	 * @param array $form Form config
	 *
	 * @return bool|\WP_Error True if valid, WP_Error if not
	 */
	public static function captcha_check($value, $field, $form){
		return true;

		if( !isset( $_POST['g-recaptcha-response'] ) || empty( $_POST['g-recaptcha-response'] )){
			return new WP_Error( 'error' );
		}

		$args = array(
			'secret'	=>	$field['config']['private_key'],
			'response'	=>	sanitize_text_field( $_POST['g-recaptcha-response'] )
		);

		$request = wp_remote_get( 'https://www.google.com/recaptcha/api/siteverify?' . build_query($args) );
		$result = json_decode( wp_remote_retrieve_body( $request ) );
		if( empty( $result->success ) ){
			return new WP_Error( 'error', __( "The wasn't entered correct.", 'caldera-forms' ) . ' <a href="#" class="reset_' . sanitize_text_field( $_POST[ $field['ID'] ] ) . '">' . __( 'Reset', 'caldera-forms' ) . '<a>.' );
		}



	}

	/**
	 * Update saved entry data for a field.
	 *
	 * @param array $field Field config
	 * @param int $entry_id The entry ID
	 * @param array $form Form config
	 */
	public static function update_field_data($field, $entry_id, $form){
		global $wpdb, $form;

		$field_types = self::get_field_types();
		// is capture?
		if(isset($field_types[$form['fields'][$field['ID']]['type']]['setup']['not_supported'])){
			if(in_array('entry_list', $field_types[$form['fields'][$field['ID']]['type']]['setup']['not_supported'])){
				return;
			}
		}

		$new_data 		= self::get_field_data($field['ID'], $form);
		$original_data 	= self::get_field_data($field['ID'], $form, $entry_id);

		if($original_data === $new_data){
			// no change 
			return;
		}

		if( has_filter( 'caldera_forms_save_field' ) ){
			$new_data = apply_filters( 'caldera_forms_update_field', $new_data, $field, $form );
		}

		if( has_filter( 'caldera_forms_save_field_' . $field['type'] ) ){
			$new_data = apply_filters( 'caldera_forms_update_field_' . $field['type'], $new_data, $field, $form );
		}

		if($original_data !== null){
			$wpdb->delete($wpdb->prefix . 'cf_form_entry_values', array('entry_id' => $entry_id, 'field_id' => $field['ID'] ) );
		}

		foreach( (array) $new_data as $entry_data ){
			// no entry - add first
			$new_entry = array(
				'entry_id'	=>	$entry_id,
				'field_id'	=>	$field['ID'],
				'slug'	 	=>	$field['slug'],
				'value'		=>	$entry_data,
			);
			$wpdb->insert($wpdb->prefix . 'cf_form_entry_values', $new_entry);
		}

	}

	/**
	 * Save entry data for a field.
	 *
	 * @param array $field Field config
	 * @param int $entry_id The entry ID
	 * @param array $form Form config
	 */
	public static function save_field_data($field, $entry_id, $form){
		global $wpdb, $form;

		if(!empty($field['conditions']['type'])){
			if(!self::check_condition($field['conditions'], $form)){
				return;
			}
		}

		$data = self::get_field_data($field['ID'], $form );

		if( empty($data) && 0 != $data ){
			return;
		}

		foreach((array) $data as $key=>$raw_entry){

			$entry = Caldera_Forms_Sanitize::sanitize( $raw_entry );

			if( has_filter( 'caldera_forms_save_field' ) ){
				$entry = apply_filters( 'caldera_forms_save_field', $entry, $field );
			}

			if( has_filter( 'caldera_forms_save_field_' . $field['type'] ) ){
				$entry = apply_filters( 'caldera_forms_save_field_' . $field['type'], $entry, $field );
			}

			$field_item = array(
				'entry_id'	=> $entry_id,
				'field_id'	=> $field['ID'],
				'slug'		=> $field['slug'],
				'value'		=> self::do_magic_tags( $entry )
			);

			// named key kets .key to slug
			if(!is_int($key)){
				// Keyed
				$keyed = true;
				$field_item['slug'] .= '.'.$key;
			}
			// Save
			$wpdb->insert($wpdb->prefix . 'cf_form_entry_values', $field_item);
		}

		if(!empty($keyed)){

			if( has_filter( 'caldera_forms_save_field_combined' . $field['type'] ) ){
				$data = apply_filters( 'caldera_forms_save_field_combined' . $field['type'], $entry, $field );
			}

			$field_item = array(
				'entry_id'	=> $entry_id,
				'field_id'	=> $field['ID'],
				'slug'		=> $field['slug'],
				'value'		=> json_encode( $data )
			);
			$wpdb->insert($wpdb->prefix . 'cf_form_entry_values', $field_item);
		}

	}

	/**
	 * Save final form data
	 *
	 * @param array $form Form config
	 *
	 * @return void|\WP_Error
	 */
	public static function save_final_form($form){
		global $transdata;

		$entryid = null;
		// check submit type (new or update)
		if(isset($_POST['_cf_frm_edt'])){
			// is edit
			//check user can edit this item.
			$user_id = get_current_user_id();
			$details = Caldera_Forms::get_entry_detail($_POST['_cf_frm_edt'], $form);

			// check token
			if(isset($_POST['_cf_frm_edt_tkn'])){

				// build token
				$token_array = array(
					'id'		=>	(int) $details['id'],
					'datestamp'	=>	$details['datestamp'],
					'user_id'	=>	(int) $details['user_id'],
					'form_id'	=>	$form['ID']
				);
				if( sha1( json_encode( $token_array ) ) !== trim( $_POST['_cf_frm_edt_tkn'] ) ){
					return new WP_Error( 'error', __( "Permission denied.", 'caldera-forms' ) );
				}else{
					$entryid = (int) $details['id'];
					$edit_token = sha1( json_encode( $token_array ) );
				}

			}else{

				if(!empty($user_id)){
					if(!empty($details)){
						// check user can edit
						if( current_user_can( 'edit_posts' ) || $details['user_id'] === $user_id ){
							$entryid = $_POST['_cf_frm_edt'];
						}else{
							return new WP_Error( 'error', __( "Permission denied.", 'caldera-forms' ) );
						}
					}

				}
			}

		}
		
		if(! empty( $form[ 'db_support' ] ) ) {
			Caldera_Forms_Save_Final::save_in_db( $form, $entryid );
		}

		if( !empty( $transdata['edit'] ) ){
			// update
			if( empty($form['mailer']['on_update'] ) ){
				return;
			}
		}else{
			// insert
			if( empty( $form['mailer']['enable_mailer'] ) && empty($form['mailer']['on_insert'] ) ){
				return;
			}
		}

		Caldera_Forms_Save_Final::do_mailer( $form, $entryid );


	}

	/**
	 * Creates a send log to debug mailer problems
	 *
	 * @param object $phpmailer The phpmailer object
	 */
	public static function debug_mail_send( $phpmailer ) {
		global $transdata, $wpdb;

		// this is a hack since there is not filter / action for a failed mail... yet
		//$phpmailer->SMTPDebug = 3;
		ob_start();
		$phpmailer->SMTPDebug = 3;
		try {
			$phpmailer->Send();
		} catch ( phpmailerException $e ) {
			print_r( $phpmailer->ErrorInfo );
		}
		print_r( $phpmailer );
		$phpmailer->SMTPDebug = 0;
		$result = ob_get_clean();

		$meta_entry = array(
			'entry_id'	 =>	$transdata['entry_id'],
			'process_id' => '_debug_log',
			'meta_key'	 =>	'debug_log',
			'meta_value' =>	$result
		);

		$wpdb->insert($wpdb->prefix . 'cf_form_entry_meta', $meta_entry);



	}
	/**
	 * Return an instance of this class.
	 *
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Change redirect notices using magic tags.
	 *
	 * @param array $notices Current notices
	 * @param array $form Form config
	 *
	 * @return array
	 */
	public static function override_redirect_notice($notices, $form){

		if(isset($form['processors'])){
			foreach($form['processors'] as $processor){
				if($processor['type'] == 'form_redirect'){

					if(isset($processor['conditions']) && !empty($processor['conditions']['type'])){
						if(!self::check_condition($processor['conditions'], $form)){
							continue;
						}
					}

					$notices['success']['note'] = self::do_magic_tags($processor['config']['message']);
				}
			}
		}

		return $notices;
	}

	/**
	 * Do the redirect
	 *
	 * @param string $referrer Reffering URL
	 * @param array $form Form config
	 * @param $processid
	 *
	 * @return string URL to redirect to.
	 */
	public static function do_redirect($referrer, $form, $processid){
		if(isset($form['processors'])){
			foreach($form['processors'] as $processor){
				if($processor['type'] == 'form_redirect'){

					if(isset($processor['conditions']) && !empty($processor['conditions']['type'])){
						if(!self::check_condition($processor['conditions'], $form)){
							continue;
						}
					}
					if(!empty($processor['config']['url'])){

						// set message
						add_filter('caldera_forms_render_notices', array('Caldera_Forms', 'override_redirect_notice' ), 10, 2 );

						//passback urls
						$referrer = parse_url( $referrer );
						$query_vars = array();
						if(!empty($referrer['query'])){
							parse_str($referrer['query'], $referrer['query']);
							if(isset($referrer['query']['cf_su'])){
								unset($referrer['query']['cf_su']);
							}
							$query_vars = array_merge($query_vars, $referrer['query']);
						}
						// get vars in url
						$base_redirect = self::do_magic_tags( $processor['config']['url'] );
						$redirect = parse_url( $base_redirect );

						if(!empty($redirect['query'])){
							parse_str($redirect['query'], $redirect['query']);
							$base_redirect = explode('?', $base_redirect, 2);
							$query_vars = array_merge($redirect['query'], $query_vars);
							$redirect = $base_redirect[0] . '?' . http_build_query($query_vars);
						}else{
							$redirect = $base_redirect . '?' . http_build_query($query_vars);
						}

						return $redirect;
					}
				}
			}
		}
		return $referrer;
	}

	/**
	 * Process the auto-responder
	 *
	 * @since unknown
	 *
	 * @param array $config Processor config
	 * @param array $form Form config
	 */
	public static function send_auto_response($config, $form){
		global $form;

		// new filter to alter the config.
		$config = apply_filters( 'caldera_forms_autoresponse_config', $config, $form);
		// remove required bounds.
		unset($config['_required_bounds']);

		$message = $config['message'];
		foreach( $config as $tag => &$value ){
			if($tag !== 'message'){
				$message = str_replace('%'.$tag.'%', $value, $message);
				$value = self::do_magic_tags( $value );
			}
		}
		// set header
		$headers[] = 'From: ' . $config['sender_name'] . ' <' . $config['sender_email'] . '>';

		if( ! isset( $config[ 'html'] ) || true == $config['html'] ){
			$headers[] = "Content-type: text/html";
			$message = wpautop( self::do_magic_tags( $message ) );

		}else{
			$message = self::do_magic_tags( $message );
		}


		// setup mailer
		$subject = $config['subject'];

		$email_message = array(
			'recipients'	=> array(
				$config['recipient_name'].' <'.$config['recipient_email'].'>'
			),
			'subject'		=>	$subject,
			'message'		=>	$message,
			'headers'		=>	$headers,
			'attachments' 	=> array()
		);

		if( ! is_email( $config[ 'sender_email' ] ) ){
			$config[ 'sender_email' ] = get_option( 'admin_email' );
		}

		$email_message = apply_filters( 'caldera_forms_autoresponse_mail', $email_message, $config, $form);

		if( 'wp' !== Caldera_Forms_Email_Settings::get_method() ){
			$email_message[ 'from'] = $email_message[ 'replyto' ] = $config[ 'sender_email' ];
			$email_message[ 'from_name' ] = $config['sender_name'];
			$email_message[ 'bcc' ] = $email_message[ 'csv' ] = false;

			Caldera_Forms_Save_Final::do_mailer( $form, null, null, $email_message );
			return;
		}

		do_action( 'caldera_forms_do_autoresponse', $config, $form);

		// send mail		
		$sent = wp_mail(
			$email_message['recipients'],
			$email_message['subject'],
			implode( "\r\n", (array) $email_message['message'] ),
			implode("\r\n", (array) $email_message['headers']),
			$email_message['attachments']
		);

		if ( ! $sent ) {
			/**
			 * Fires if wp_mail returns false in autoresponder
			 *
			 * @since 1.2.3
			 *
			 * @param array $email_message Email data
			 * @param array $config Auto responder settings
			 * @param array $form The form config
			 */
			do_action( 'caldera_forms_autoresponder_failed', $email_message, $config, $form );
		}

	}


	/**
	 * Load built-in form processors
	 *
	 * @param array $processors
	 *
	 * @return array
	 */
	public function get_form_processors($processors){
		$internal_processors = array(
			'auto_responder' => array(
				"name"				=>	__( 'Auto Responder', 'caldera-forms' ),
				"description"		=>	__( 'Sends out an auto response e-mail', 'caldera-forms' ),
				"post_processor"	=>	array($this, 'send_auto_response'),
				"template"			=>	CFCORE_PATH . "processors/auto_responder/config.php",
				"default"			=>	array(
					'subject'		=>	__( 'Thank you for contacting us', 'caldera-forms' )
				),
			),
			'form_redirect' => array(
				"name"				=>	__( 'Redirect', 'caldera-forms' ),
				"description"		=>	__( 'Redirects user to URL on successful submit', 'caldera-forms' ),
				"template"			=>	CFCORE_PATH . "processors/redirect/config.php",
				"single"			=>	false
			),
			'increment_capture' => array(
				"name"              =>  __( 'Increment Value', 'caldera-forms' ),
				"description"       =>  __( 'Increment a value per entry.', 'caldera-forms' ),
				"processor"     	=>  array( $this, 'increment_value' ),
				"template"          =>  CFCORE_PATH . "processors/increment/config.php",
				"single"			=>	true,
				"conditionals"		=>	false,
				"magic_tags"    =>  array(
					'increment_value'
				)
			)
		);
		// akismet 
		$wp_api_key = get_option( 'wordpress_api_key' );
		if(!empty($wp_api_key)){
			$internal_processors['akismet'] = array(
				"name"				=>	__( 'Akismet', 'caldera-forms' ),
				"description"		=>	__( 'Anti-spam filtering', 'caldera-forms' ),
				"pre_processor"		=>	array( $this, 'akismet_scanner'),
				"template"			=>	CFCORE_PATH . "processors/akismet/config.php",
				"single"			=>	false,
			);
		}

		if( ! is_array( $processors ) || empty( $processors ) ){
			return $internal_processors;
		}

		return array_merge( $processors, $internal_processors );

	}

	/**
	 * Increment an internal value
	 *
	 * @param array $config Processor config
	 * @param array $form Form config
	 *
	 * @return array Key is new value.
	 */
	public function increment_value( $config, $form ){

		// get increment value;
		$increment_value = get_option('_increment_' . $config['processor_id'], $config['start'] );

		update_option( '_increment_' . $config['processor_id'], $increment_value + 1 );

		if( !empty( $config['field'] ) ){
			self::set_field_data( $config['field'], $increment_value, $form );
		}

		return array('increment_value' => $increment_value );

	}


	/**
	 * Apply Akismets
	 *
	 * @param array $config Processor config
	 * @param array $form Form config
	 *
	 * @return array
	 */
	static public function akismet_scanner($config, $form){
		global $post;

		$wp_api_key = get_option( 'wordpress_api_key' );
		if(empty($wp_api_key)){
			return array('type' => 'error', 'note' => __( 'Akismet not setup.'));
		}
		// set permalink
		if($post->ID){
			$permalink = get_permalink( $post->ID );
		}else{
			$permalink = get_home_url();
		}
		// is contact form or reg form
		$regform = self::get_processor_by_type('user_register', $form);
		if(!empty($regform)){
			$type = 'signup';
		}else{
			$type = 'contact-form';
		}
		// Call to comment check
		$data = array(
			'blog' 					=> get_home_url(),
			'user_ip' 				=> $_SERVER['REMOTE_ADDR'],
			'user_agent' 			=> $_SERVER['HTTP_USER_AGENT'],
			'referrer'				=> $_SERVER['HTTP_REFERER'],
			'permalink' 			=> $permalink,
			'comment_type' 			=> $type,
			'comment_author' 		=> self::do_magic_tags($config['sender_name']),
			'comment_author_email'	=> self::do_magic_tags($config['sender_email'])
		);

		if(!empty($config['url'])){
			$data['comment_author_url']	= self::do_magic_tags($config['url']);
		};
		if(!empty($config['content'])){
			$data['comment_content']	= self::do_magic_tags($config['content']);
		};

		$request = http_build_query($data);

		$host = $http_host = $wp_api_key.'.rest.akismet.com';
		$path = '/1.1/comment-check';
		$port = 80;
		$akismet_ua = "WordPress/3.8.1 | Akismet/2.5.9";
		$content_length = strlen( $request );
		$http_request = "POST $path HTTP/1.0\r\n";
		$http_request .= "Host: $host\r\n";
		$http_request .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$http_request .= "Content-Length: {$content_length}\r\n";
		$http_request .= "User-Agent: {$akismet_ua}\r\n";
		$http_request .= "\r\n";
		$http_request .= $request;
		$response = '';
		if( false != ( $fs = @fsockopen( $http_host, $port, $errno, $errstr, 10 ) ) ) {

			fwrite( $fs, $http_request );

			while ( !feof( $fs ) )
				$response .= fgets( $fs, 1160 ); // One TCP-IP packet
			fclose( $fs );

			$response = explode( "\r\n\r\n", $response, 2 );

		}

		if ( 'true' == $response[1] ){
			return array('type' => 'error', 'note' => self::do_magic_tags($config['error']));
		}

	}

	/**
	 * Process a calculation field.
	 *
	 * @param string $value The calculation to run
	 * @param array $field Field config
	 * @param array $form Form config
	 *
	 * @return int|string
	 */
	static public function run_calculation($value, $field, $form){

		$formula = $field['config']['formular'];

		// get manual
		if(!empty($field['config']['manual'])){
			$formula = $field['config']['manual_formula'];
			preg_match_all("/%(.+?)%/", $formula, $hastags);
			if(!empty($hastags[1])){
				$binds = array();

				foreach($hastags[1] as $tag_key=>$tag){

					foreach($form['fields'] as $key_id=>$fcfg){
						if($fcfg['slug'] === $tag){
							$binds[] = '#'.$key_id;
							$bindfields[] = '"'.$key_id.'"';
							$formula = str_replace($hastags[0][$tag_key], $key_id, $formula);
						}
					}
				}
			}

		}
		if( empty($formula)){
			return 0;
		}

		$formula = self::do_magic_tags( $formula, null, $form );
		if( false !== strpos( $formula, 'Math.') ){
			$formula = str_replace( 'Math.', '', $formula );
		}
		foreach($form['fields'] as $fid=>$cfg){
			if(false !== strpos($formula, $fid)){
				$entry_value = self::get_field_data($fid, $form);

				if(is_array($entry_value)){
					$number = floatval( array_sum( $entry_value ) );
				}else{
					$number = floatval( $entry_value );
				}

				$formula = str_replace($fid, $number, $formula);
			}
		}

		$total_function = create_function(null, 'return '.$formula.';');
		$total = $total_function();

		if( ! is_numeric( $total ) ){
			return new WP_Error( $field[ 'ID' ] . '-calculation', __( 'Calculation is invalid' ) );
		}

		if(isset($field['config']['fixed'])){
			if( function_exists( 'money_format' ) ){
				return money_format('%i', $total );
			}else{
				return sprintf('%01.2f', $total );
			}

		}
		return $total;
	}

	/**
	 * Include the template for a calculation field
	 *
	 * @return string HTML for field.
	 */
	static public function calculations_templates(){
		include CFCORE_PATH . "fields/calculation/line-templates.php";
	}

	/**
	 * Load built-in fields
	 *
	 * @since unknown
	 *
	 * @uses "caldera_forms_get_field_types" filter
	 *
	 * @param array $fields
	 *
	 * @return array
	 */
	public function get_internal_field_types($fields){

		$internal_fields = array(
			//basic
			'text' => array(
				"field"		=>	__( 'Single Line Text', 'caldera-forms' ),
				"description" => __( 'Single Line Text', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/text/field.php",
				"category"	=>	__( 'Basic', 'caldera-forms' ),
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/text/config.php",
					"preview"	=>	CFCORE_PATH . "fields/text/preview.php"
				),

			),
			'hidden' => array(
				"field"		=>	__( 'Hidden', 'caldera-forms' ),
				"description" => __( 'Hidden', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/hidden/field.php",
				"category"	=>	__( 'Basic', 'caldera-forms' ),
				"static"	=> true,
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/hidden/preview.php",
					"template"	=>	CFCORE_PATH . "fields/hidden/setup.php",
					"not_supported"	=>	array(
						'hide_label',
						'caption',
						'required',
					)
				)
			),
			'email' => array(
				"field"		=>	__( 'Email Address', 'caldera-forms' ),
				"description" => __( 'Email Address', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/email/field.php",
				"category"	=>	__( 'Basic', 'caldera-forms' ),
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/email/preview.php",
					"template"	=>	CFCORE_PATH . "fields/email/config.php"
				)
			),
			'button' => array(
				"field"		=>	__( 'Button', 'caldera-forms' ),
				"description" => __( 'Button, Submit and Reset types', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/button/field.php",
				"category"	=>	__( 'Basic', 'caldera-forms' ),
				"capture"	=>	false,
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/button/config_template.php",
					"preview"	=>	CFCORE_PATH . "fields/button/preview.php",
					"default"	=> array(
						'class'	=>	'btn btn-default',
						'type'	=>	'submit'
					),
					"not_supported"	=>	array(
						'hide_label',
						'caption',
						'required',
						'entry_list'
					)
				)
			),
			'phone' => array(
				"field"		=>	__( 'Phone Number', 'caldera-forms' ),
				"description" => __( 'Phone number with masking', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/phone/field.php",
				"category"	=>	__( 'Basic', 'caldera-forms' ),
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/phone/config.php",
					"preview"	=>	CFCORE_PATH . "fields/phone/preview.php",
					"default"	=>	array(
						'default'	=> '',
						'type'	=>	'local',
						'custom'=> '(999)999-9999'
					)
				)
			),
			'paragraph' => array(
				"field"		=>	__( 'Paragraph Textarea', 'caldera-forms' ),
				"description" => __( 'Paragraph Textarea', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/paragraph/field.php",
				"category"	=>	__( 'Basic', 'caldera-forms' ),
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/paragraph/config_template.php",
					"preview"	=>	CFCORE_PATH . "fields/paragraph/preview.php",
					"default"	=> array(
						'rows'	=>	'4'
					),
				)
			),


			//special
			'calculation' => array(
				"field"		=>	__( 'Calculation', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/calculation/field.php",
				"handler"	=>	array($this, "run_calculation"),
				"category"	=>	__( 'Special', 'caldera-forms' ),
				"description" => __( 'Calculate values', 'caldera-forms' ),
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/calculation/config.php",
					"preview"	=>	CFCORE_PATH . "fields/calculation/preview.php",
					"default"	=> array(
						'element'	=>	'h3',
						'classes'	=> 	'total-line',
						'before'	=>	__( 'Total', 'caldera-forms' ).':',
						'after'		=> ''
					),

				),
			),
			'range_slider' 	=> array(
				"field"		=>	__( 'Range Slider', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/range_slider/field.php",
				"category"	=>	__( 'Special', 'caldera-forms' ),
				"description" => __( 'Range Slider input field','caldera-forms' ),
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/range_slider/config.php",
					"preview"	=>	CFCORE_PATH . "fields/range_slider/preview.php",
					"default"	=> array(
						'default'	=>	1,
						'step'		=>	1,
						'min'		=>	0,
						'max'		=> 100,
						'showval'	=> 1,
						'suffix'	=> '',
						'prefix'	=> '',
						'color'		=> '#00ff00',
						'handle'	=> '#ffffff',
						'handleborder'	=> '#cccccc',
						'trackcolor' => '#e6e6e6'
					),
				)
			),
			'star_rating' 	=> array(
				"field"		=>	__( 'Star Rating', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/star-rate/field.php",
				"category"	=>	__( 'Special', 'caldera-forms' ),
				"description" => __( 'Star rating input for feedback','caldera-forms' ),
				"viewer"	=>	array($this, 'star_rating_viewer'),
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/star-rate/config.php",
					"preview"	=>	CFCORE_PATH . "fields/star-rate/preview.php",
					"default"	=> array(
						'number'	=>	5,
						'space'		=>	3,
						'size'		=>	13,
						'color'		=> '#FFAA00',
						'track_color'=> '#AFAFAF',
						'type'=> 'star',
					),
				)
			),

			//file
			'file' => array(
				"field"		=>	__( 'File', 'caldera-forms' ),
				"description" => __( 'File Uploader', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/file/field.php",
				"viewer"	=>	array($this, 'handle_file_view'),
				"category"	=>	__( 'File', 'caldera-forms' ),
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/file/preview.php",
					"template"	=>	CFCORE_PATH . "fields/file/config_template.php"
				)
			),
			'advanced_file' => array(
				"field"		=>	__( 'Advanced File Uploader', 'caldera-forms' ),
				"description" => __( 'Inline, multi file uploader', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/advanced_file/field.php",
				"viewer"	=>	array($this, 'handle_file_view'),
				"category"	=>	__( 'File', 'caldera-forms' ),
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/advanced_file/preview.php",
					"template"	=>	CFCORE_PATH . "fields/advanced_file/config_template.php"
				),
				"scripts"	=> array(
					CFCORE_URL . 'fields/advanced_file/uploader.js'
				),

			),

			//content
			'html' => array(
				"field"		=>	__( 'HTML', 'caldera-forms' ),
				"description" => __( 'Add text/html content', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/html/field.php",
				"category"	=>	__( 'Content', 'caldera-forms' ),
				"icon"		=>	CFCORE_URL . "fields/html/icon.png",
				"capture"	=>	false,
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/html/preview.php",
					"template"	=>	CFCORE_PATH . "fields/html/config_template.php",
					"not_supported"	=>	array(
						'hide_label',
						'caption',
						'required',
						'entry_list'
					)
				)
			),

			//select
			'dropdown' => array(
				"field"		=>	__( 'Dropdown Select', 'caldera-forms' ),
				"description" => __( 'Dropdown Select', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/dropdown/field.php",
				"category"	=>	__( 'Select', 'caldera-forms' ),
				"options"	=>	"single",
				"static"	=> true,
				"viewer"	=>	array($this, 'filter_options_calculator'),
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/dropdown/config_template.php",
					"preview"	=>	CFCORE_PATH . "fields/dropdown/preview.php",
					"default"	=> array(

					),
				)
			),
			'checkbox' => array(
				"field"		=>	__( 'Checkbox', 'caldera-forms' ),
				"description" => __( 'Checkbox', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/checkbox/field.php",
				"category"	=>	__( 'Select', 'caldera-forms' ),
				"options"	=>	"multiple",
				"static"	=> true,
				"viewer"	=>	array($this, 'filter_options_calculator'),
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/checkbox/preview.php",
					"template"	=>	CFCORE_PATH . "fields/checkbox/config_template.php",

				),
			),
			'radio' => array(
				"field"		=>	__( 'Radio', 'caldera-forms' ),
				"description" => __( 'Radio', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/radio/field.php",
				"category"	=>	__( 'Select', 'caldera-forms' ),
				"options"	=>	true,
				"static"	=> true,
				"viewer"	=>	array($this, 'filter_options_calculator'),
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/radio/preview.php",
					"template"	=>	CFCORE_PATH . "fields/radio/config_template.php",
				)
			),
			'filtered_select2' => array(
				"field"		=>	__( 'Autocomplete', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/select2/field/field.php",
				"category"	=>	__( 'Select', 'caldera-forms' ),
				"description" => 'Select2 dropdown',
				"options"	=>	"multiple",
				"static"	=> true,
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/select2/field/config.php",
					"preview"	=>	CFCORE_PATH . "fields/select2/field/preview.php",
				),
				"scripts"	=> array(
					CFCORE_URL . "fields/select2/js/select2.min.js",
				),
				"styles"	=> array(
					CFCORE_URL . "fields/select2/css/select2.css",
				)
			),
			'date_picker' => array(
				"field"		=>	__( 'Date Picker', 'caldera-forms' ),
				"description" => __( 'Date Picker', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/date_picker/datepicker.php",
				"category"	=>	__( 'Select', 'caldera-forms' ),
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/date_picker/preview.php",
					"template"	=>	CFCORE_PATH . "fields/date_picker/setup.php",
					"default"	=> array(
						'format'	=>	'yyyy-mm-dd'
					),
				)
			),
			'toggle_switch' => array(
				"field"		=>	__( 'Toggle Switch', 'caldera-forms' ),
				"description" => __( 'Toggle Switch', 'caldera-forms' ),
				"category"	=>	__( 'Select', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/toggle_switch/field.php",
				"viewer"	=>	array($this, 'filter_options_calculator'),
				"options"	=>	"single",
				"static"	=> true,
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/toggle_switch/config_template.php",
					"preview"	=>	CFCORE_PATH . "fields/toggle_switch/preview.php",
				),
			),
			'color_picker' => array(
				"field"		=>	__( 'Color Picker', 'caldera-forms' ),
				"description" => __( 'Color Picker', 'caldera-forms' ),
				"category"	=>	__( 'Select', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/color_picker/field.php",
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/color_picker/preview.php",
					"template"	=>	CFCORE_PATH . "fields/color_picker/setup.php",
					"default"	=> array(
						'default'	=>	'#FFFFFF'
					),

				),
			),
			'states' => array(
				"field"		=>	__( 'State/ Province Select', 'caldera-forms' ),
				"description" => __( 'Dropdown select for US states and Canadian provinces.', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/states/field.php",
				"category"	=>	__( 'Select', 'caldera-forms' ),
				"placeholder" => false,
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/states/config_template.php",
					"preview"	=>	CFCORE_PATH . "fields/states/preview.php",
					"default"	=> array(

					),
				)
			),


			//discontinued
			'recaptcha' => array(
				"field"		=>	__( 'reCAPTCHA', 'caldera-forms' ),
				"description" => __( 'reCAPTCHA anti-spam field', 'caldera-forms' ),
				"file"		=>	CFCORE_PATH . "fields/recaptcha/field.php",
				"category"	=>	__( 'Discontinued', 'caldera-forms' ),
				"handler"	=>	array($this, 'captcha_check'),
				"capture"	=>	false,
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/recaptcha/config.php",
					"preview"	=>	CFCORE_PATH . "fields/recaptcha/preview.php",
					"not_supported"	=>	array(
						'caption',
						'required'
					),
				)
			),

		);

		return array_merge( $fields, $internal_fields );

	}

	/**
	 * Check to see if the field is used in a calculation (use to display the label)
	 *
	 * @param string $value Value to filter.
	 * @param array $field Field config.
	 * @param array $form Form congig.
	 *
	 * @return array|string
	 */
	function filter_options_calculator($value,$field,$form){
		//
		if(!empty($form)){
			foreach($form['fields'] as $field_id => $field_conf){
				if($field_conf['type'] !== 'calculation'){
					continue;
				}
				// auto
				if(!empty($field_conf['config'])){
					$binddown = json_encode( $field_conf['config']['config'] );
					if( false !== strpos($binddown, $field['ID']) || false !== strpos($field_conf['config']['manual_formula'], $field['ID']) ){
						foreach($field['config']['option'] as $option_id=>$option){
							if(is_array($value)){
								if( in_array( $option['value'], $value) ){
									$key = array_search($option['value'], $value);
									$value[$key] = $option['label'] . '&nbsp;<small class="view_option_value">('.$value[$key].')</small>';
								}
							}else{
								if($option['value'] == $value){
									return $option['label'] . '&nbsp;<small class="view_option_value">('.$value.')</small>';
								}
							}
						}
						//return $field['label'];
					}
					if(is_array($value)){
						$value = implode('<br>', $value);
					}
				}
			}
		}
		return $value;
	}

	/**
	 * Applies the inline rules for fields conditionals
	 *
	 * @param array $field Field config
	 * @param array $form Form config
	 *
	 * @return array Options for field
	 */
	public function apply_conditional_groups($field, $form){

		if( !empty( $form['conditional_groups']['conditions'][ $field['conditions']['type'] ] ) ){
			$group = $form['conditional_groups']['conditions'][ $field['conditions']['type'] ];
			if( ! isset( $field['conditions']['group'] ) ){
				$field['conditions']['group'] = array();
			}

			if( ! isset( $group['group'] ) ) {
				$group['group'] = array();
			}

			$field['conditions']['type'] = $group['type'];
			$field['conditions']['group'] = $group['group'];
		}

		return $field;
	}
	/**
	 * Default callback for auto populating select fields
	 *
	 * @param array $field Field config
	 * @param array $form Form config
	 *
	 * @return array Options for field
	 */
	public function auto_populate_options_field($field, $form){

		if(!empty($field['config']['auto'])){
			$field['config']['option'] = array();
			switch($field['config']['auto_type']){
				case 'post_type':
				case 'easy-query' :

					if( ! isset( $field[ 'config' ][ 'orderby_post' ] ) ) {
						$field[ 'config' ][ 'orderby_post' ] = 'date';
					}

					if( ! isset( $field[ 'config' ][ 'order' ] ) ) {
						$field[ 'config' ][ 'order' ] = 'ASC';
					}

					$args = array(
						'post_type' => $field['config']['post_type'],
						'post_status' => 'publish',
						'posts_per_page' => -1,
						'order' => $field[ 'config' ][ 'order' ],
						'orderby' => $field[ 'config' ][ 'orderby_post' ]
					);

					/**
					 * Modify arguments for WP_Query used to auto-populate post type fields
					 *
					 * @since unknown
					 *
					 * @param array $args  Args for WP_Query
					 * @param array $form Form config
					 */
					$args  = apply_filters( 'caldera_forms_autopopulate_post_type_args', $args, $field );

					$posts = get_posts( $args );

					if( $field[ 'config' ][ 'value_field' ] === 'id' ){
						$field[ 'config' ][ 'value_field' ] = 'ID';
					}elseif( $field[ 'config' ][ 'value_field' ] === 'name' ){
						$field[ 'config' ][ 'value_field' ] = 'post_name';
					}

					/**
					 * Filter which field is used for the VALUE when getting autopopulate option values when autopopulating options from post types
					 *
					 * Value can be any WP_Post field, or a meta key (be careful will return an empty string if that meta key isn't set for the post.
					 *
					 * @since 1.2.2
					 *
					 * @param string $field What field to use for the value. Default is "ID".
					 * @param array $field Config for the field.
					 * @param array $form Config for the form.
					 * @param array $posts Current post collection.
					 */
					$field_for_value = apply_filters( 'caldera_forms_autopopulate_options_post_value_field', $field[ 'config' ][ 'value_field' ], $field, $form, $posts  );
					$field[ 'config' ][ 'value_field' ] = $field_for_value;

					/**
					 * Filter which field is used for the LABEL when getting autopopulate option values when autopopulating options from post types
					 *
					 * Value can be any WP_Post field, or a meta key (be careful will return an empty string if that meta key isn't set for the post.
					 *
					 * @since 1.2.2
					 *
					 * @param string $field What field to use for the label. Default is "post_title".
					 * @param array $field Config for the field.
					 * @param array $form Config for the form.
					 * @param array $posts Current post collection.
					 */
					$field_for_label = apply_filters( 'caldera_forms_autopopulate_options_post_label_field', 'post_title', $field, $form, $posts  );
					foreach($posts as $post_item){
						$field['config']['option'][$post_item->ID] = array(
							'value'	=>	$post_item->{$field_for_value},
							'label' =>	$post_item->{$field_for_label}
						);
					}

					break;
				case 'taxonomy':
					if( $field[ 'config' ][ 'value_field' ] === 'id' ){
						$field[ 'config' ][ 'value_field' ] = 'term_id';
					}

					if( ! isset( $field[ 'config' ][ 'orderby_tax' ] ) ) {
						$field[ 'config' ][ 'orderby_tax' ] = 'count';
					}

					if( ! isset( $field[ 'config' ][ 'order' ] ) ) {
						$field[ 'config' ][ 'order' ] = 'ASC';
					}

					$args = array(
						'orderby' => $field[ 'config' ][ 'orderby_tax' ],
						'order' => $field[ 'config' ][ 'order' ],
						'hide_empty' => 0
					);


					/**
					 * Modify arguments for get_terms() used to auto-populate taxononmy type fields
					 *
					 * @since unknown
					 *
					 * @param array $args  Args for get_terms()
					 * @param array $form Form config
					 */
					$args  = apply_filters( 'caldera_forms_autopopulate_taxonomy_args', $args );

					$terms = get_terms( $field['config']['taxonomy'], $args );

					/**
					 * Filter which field is used for the VALUE when getting autopopulate option values when autopopulating options from post types
					 *
					 * Value must be a standard taxonomy term field.
					 *
					 * @since 1.2.2
					 *
					 * @param string $field What field to use for the value. Default is "term_id".
					 * @param array $field Config for the field.
					 * @param array $form Config for the form.
					 * @param array $posts Current term collection.
					 */
					$field_for_value = apply_filters( 'caldera_forms_autopopulate_options_taxonomy_value_field', $field[ 'config' ][ 'value_field' ], $field, $form, $terms  );
					$field[ 'config' ][ 'value_field' ] = $field_for_value;

					/**
					 * Filter which field is used for the LABEL when getting autopopulate option values when autopopulating options from post types
					 *
					 * Value must be a standard taxonomy term field.
					 *
					 * @since 1.2.2
					 *
					 * @param string $field What field to use for the label. Default is "name".
					 * @param array $field Config for the field.
					 * @param array $form Config for the form.
					 * @param array $posts Current term collection.
					 */
					$field_for_label = apply_filters( 'caldera_forms_autopopulate_options_taxonomy_label_field', 'name', $field, $form, $terms  );

					foreach( $terms as $term){
						$field['config']['option'][$term->term_id] = array(
							'value'	=>	$term->{$field_for_value},
							'label' =>	$term->{$field_for_label}
						);
					}
					break;

			}

		}else{
			$field = self::format_select_options( $field );
		}

		return $field;

	}


	/**
	 * Verify and format select options
	 *
	 * @since 1.3.2
	 *
	 * @param array $field Field config
	 *
	 * @return array
	 */
	static public function format_select_options( $field ) {


		if( !empty( $field[ 'config' ][ 'option' ] ) ){
			foreach( $field[ 'config' ][ 'option' ] as &$option){
				if ( empty( $field[ 'config' ]['show_values'] ) || strlen( $option[ 'value' ] ) === 0 ){
					$option[ 'value' ] = $option[ 'label' ] = self::do_magic_tags( $option[ 'label' ] );
				}else{
					$option[ 'value' ] = self::do_magic_tags( $option[ 'value' ] );
					$option[ 'label' ] = self::do_magic_tags( $option[ 'label' ] );
				}
			}
		}


		return $field;

	}

	/**
	 * Evaluate a conditional.
	 *
	 * @param array $conditions Conditions.
	 * @param array $form Form config.
	 * @param null|int $entry_id Optional. Entry ID to test by.
	 *
	 * @return bool
	 */
	static public function check_condition($conditions, $form, $entry_id=null){

		$trues = array();
		if(empty($conditions['group'])){
			return true;
		}
		//$data = self::get_submission_data($form);

		foreach($conditions['group'] as $groupid=>$lines){
			$truelines = array();

			foreach($lines as $lineid=>$line){

				if( isset( $form['fields'][$line['field']]['config']['option'][$line['value']] )){
					$line['value'] = $form['fields'][$line['field']]['config']['option'][$line['value']]['value'];
				}

				$line['value'] = self::do_magic_tags( $line['value'] );

				$value = (array) self::get_field_data($line['field'], $form, $entry_id);
				if(empty($value)){
					$value = array('');
				}
				// do field value replaces
				if( false !== strpos($line['value'], '%')){
					$isslug = self::get_slug_data( trim($line['value'], '%'), $form, $entry_id);
					if( $isslug !== null ){
						$line['value'] = $isslug;
					}
				}


				$truelines[$lineid] = false;

				switch ($line['compare']) {
					case 'is':
						if(is_array($value)){
							if(in_array($line['value'], $value)){
								$truelines[$lineid] = true;
							}
						}else{
							if($value == $line['value']){
								$truelines[$lineid] = true;
							}
						}
						break;
					case 'isnot':
						if(is_array($value)){
							if(!in_array($line['value'], $value)){
								$truelines[$lineid] = true;
							}
						}else{
							if($value != $line['value']){
								$truelines[$lineid] = true;
							}
						}
						break;
					case '>':
					case 'greater':
						if(is_array($value)){
							if(array_sum($value) > $line['value']){
								$truelines[$lineid] = true;
							}
						}else{
							if($value > $line['value']){
								$truelines[$lineid] = true;
							}
						}
						break;
					case '<':
					case 'smaller':
						if(is_array($value)){
							if(array_sum($value) < $line['value']){
								$truelines[$lineid] = true;
							}
						}else{
							if($value < $line['value']){
								$truelines[$lineid] = true;
							}
						}
						break;
					case 'startswith':
						if(is_array($value)){
							foreach($value as $part){
								if( 0 === strpos($part,$line['value'])){
									$truelines[$lineid] = true;
								}
							}
						}else{
							if( substr( $value, 0, strlen($line['value']) ) == $line['value']){
								$truelines[$lineid] = true;
							}
						}
						break;
					case 'endswith':
						if(is_array($value)){
							foreach($value as $part){
								if( substr( $part, strlen($part)-strlen($line['value']) ) == $line['value']){
									$truelines[$lineid] = true;
								}
							}
						}else{
							if( substr( $value, strlen($value)-strlen($line['value']) ) == $line['value']){
								$truelines[$lineid] = true;
							}
						}
						break;
					case 'contains':
						if(is_array($value)){
							if( false !== strpos( implode('', $value), $line['value'] ) ){
								$truelines[$lineid] = true;
							}
						}else{
							if( false !== strpos( $value, $line['value'] ) ){
								$truelines[$lineid] = true;
							}
						}
						break;
				}

			}

			$trues[$groupid] = in_array(false, $truelines) ? false : true;
		}

		if($conditions['type'] == 'use' || $conditions['type'] == 'show'){
			if(in_array(true, $trues)){
				return true;
			}
		}elseif($conditions['type'] == 'not' || $conditions['type'] == 'hide' || $conditions['type'] == 'disable'){
			if(!in_array(true, $trues)){
				return true;
			}
		}

		// false if nothing happens
		return false;
	}

	// FRONT END STUFFF
	/**
	 * Perform redirect
	 *
	 * @param string $type Type of redirect being performed.
	 * @param string $url URL to redirect to.
	 * @param array $form Form config.
	 * @param string $processid Process ID for process calling the redirect.
	 */
	static public function form_redirect($type, $url, $form, $processid){

		$url = apply_filters( 'caldera_forms_redirect_url', $url, $form, $processid);
		$url = apply_filters( 'caldera_forms_redirect_url_' . $type, $url, $form, $processid);

		if( headers_sent() ){
			remove_action('caldera_forms_redirect', 'cf_ajax_redirect', 10 );
		}

		do_action('caldera_forms_redirect', $type, $url, $form, $processid);
		do_action('caldera_forms_redirect_' . $type, $url, $form, $processid);

		if(!empty($url)){
			cf_redirect( $url, 302 );
			exit;
		}

	}

	/**
	 * Add default magic tags
	 *
	 * @param array $tags
	 *
	 * @return array
	 */
	public function set_magic_tags($tags){

		// get internal tags
		$system_tags = array(
			'entry_id',
			'entry_token',
			'ip',
			'user:id',
			'user:user_login',
			'user:first_name',
			'user:last_name',
			'user:user_email' => array(
				'text',
				'email'
			),
			'get:*',
			'post:*',
			'request:*',
			'post_meta:*',
			'embed_post:ID',
			'embed_post:post_title',
			'embed_post:permalink',
			'embed_post:post_date' => array(
				'text',
				'date_picker'
			),
			'date:Y-m-d H:i:s' => array(
				'text',
				'date_picker'
			),
			'date:Y/m/d',
			'date:Y/d/m',
			'login_url',
			'logout_url',
			'register_url',
			'lostpassword_url'

		);

		$tags['system'] = array(
			'type'	=> __( 'System Tags', 'caldera-forms' ),
			'tags'	=> $system_tags,
			'wrap'	=>	array('{','}')
		);

		// get processor tags
		$processors = Caldera_Forms_Processor_Load::get_instance()->get_processors();
		if(!empty($processors)){
			foreach($processors as $processor_key=>$processor){
				if(isset($processor['magic_tags'])){
					foreach($processor['magic_tags'] as $key_tag=>$value_tag){

						if(!isset($tags[$processor_key])){
							$tags[$processor_key] = array(
								'type'	=>	$processor['name'],
								'tags'	=>	array(),
								'wrap'	=>	array('{','}')
							);
						}
						if(is_array($value_tag)){

							// compatibility specific
							$tag = $processor_key.':'.$key_tag;
							if(!isset($tags[$processor_key]['tags'][$tag])){
								if(!in_array('text', $value_tag)){
									$value_tag[] = 'text';
								}
								$tags[$processor_key]['tags'][$tag] = $value_tag;
							}
						}else{
							// compatibility text
							$tag = $processor_key.':'.$value_tag;
							if(!in_array($tag, $tags)){
								$tags[$processor_key]['tags'][] = $tag;
							}

						}
					}
				}
			}
		}

		return $tags;
	}

	/**
	 * Parse magic tags
	 *
	 * @param string $value
	 * @param null|int $entry_id Optional. Entry ID to test by.
	 * @param array $magic_caller The form/processorr/entry to evaluate against. May also be a powerful wizard.
	 *
	 * @return mixed
	 */
	static public function do_magic_tags($value, $entry_id = null, $magic_caller = array() ){

		global $processed_meta, $form, $referrer;
		/// get meta entry for magic tags defined.

		$input_value = $value;
		$this_form = $form;
		// pull in the metadata for entry ID
		if( null !== $entry_id ){
			$entry_details = self::get_entry_detail( $entry_id );
			$this_form = Caldera_Forms_Forms::get_form( $entry_details['form_id'] );
			if( !empty( $entry_details['meta'] ) ){
				foreach( $entry_details['meta'] as $meta_block ){
					if( !empty( $meta_block['data'] ) ){
						foreach( $meta_block['data'] as $meta_process_id=>$proces_meta_data ){
							foreach( $proces_meta_data['entry'] as $process_meta_key=>$process_meta_entry ){
								$processed_meta[$this_form['ID']][$meta_process_id][$process_meta_key] = $process_meta_entry['meta_value'];
							}
						}
					}
				}
			}
		}

		if ( is_string( $value ) ) {
			// check for magics
			preg_match_all( "/\{(.+?)\}/", $value, $magics );
			if ( ! empty( $magics[ 1 ] ) ) {
				foreach ( $magics[ 1 ] as $magic_key => $magic_tag ) {

					$magic = explode( ':', $magic_tag, 2 );

					if ( count( $magic ) == 2 ) {
						switch ( strtolower( $magic[ 0 ] ) ) {
							case 'get':
								if ( isset( $_GET[ $magic[ 1 ] ] ) ) {
									$magic_tag = Caldera_Forms_Sanitize::sanitize( $_GET[ $magic[ 1 ] ] );
								} else {
									// check on referer.
									if ( isset( $referrer[ 'query' ][ $magic[ 1 ] ] ) ) {
										$magic_tag = $referrer[ 'query' ][ $magic[ 1 ] ];
									} else {
										$magic_tag = null;
									}
								}
								break;
							case 'post':
								if ( isset( $_POST[ $magic[ 1 ] ] ) ) {
									$magic_tag = Caldera_Forms_Sanitize::sanitize( $_POST[ $magic[ 1 ] ] );
								} else {
									$magic_tag = null;
								}
								break;
							case 'request':
								if ( isset( $_REQUEST[ $magic[ 1 ] ] ) ) {
									$magic_tag = Caldera_Forms_Sanitize::sanitize( $_REQUEST[ $magic[ 1 ] ] );
								} else {
									$magic_tag = null;
								}
								break;
							case 'variable':
								if ( ! empty( $this_form[ 'variables' ][ 'keys' ] ) ) {
									foreach ( $this_form[ 'variables' ][ 'keys' ] as $var_index => $var_key ) {
										if ( $var_key == $magic[ 1 ] ) {
											if ( ! in_array( $magic_tag, $magic_caller ) ) {
												$magic_caller[] = $magic_tag;
												$magic_tag      = self::do_magic_tags( $this_form[ 'variables' ][ 'values' ][ $var_index ], $entry_id, $magic_caller );
											} else {
												$magic_tag = $this_form[ 'variables' ][ 'values' ][ $var_index ];
											}
										}
									}
								}
								break;
							case 'date':
								$magic_tag = get_date_from_gmt( date( 'Y-m-d H:i:s' ), $magic[ 1 ] );
								break;
							case 'user':
								if ( is_user_logged_in() ) {
									$user = get_userdata( get_current_user_id() );
									if ( isset( $user->data->{$magic[ 1 ]} ) ) {
										$magic_tag = $user->data->{$magic[ 1 ]};
									} else {
										if ( strtolower( $magic[ 1 ] ) == 'id' ) {
											$magic_tag = $user->ID;
										} else {
											$magic_tag = get_user_meta( $user->ID, $magic[ 1 ], true );
										}
									}
								} else {
									$magic_tag = null;
								}
								break;
							case 'embed_post':
								global $post;

								if ( is_object( $post ) ) {
									if ( isset( $post->{$magic[ 1 ]} ) ) {
										$magic_tag = $post->{$magic[ 1 ]};
									} else {

										// extra post data
										switch ( $magic[ 1 ] ) {
											case 'permalink':
												$magic_tag = get_permalink( $post->ID );
												break;

										}

									}
								} else {
									$magic_tag = null;
								}
								break;
							case 'post_meta':
								global $post;

								if ( is_object( $post ) ) {
									$post_metavalue = get_post_meta( $post->ID, $magic[ 1 ] );
									if ( false !== strpos( $magic[ 1 ], ':' ) ) {
										$magic[ 3 ] = explode( ':', $magic[ 1 ] );
									}
									if ( empty( $post_metavalue ) ) {
										$magic_tag = null;
									} else {
										if ( empty( $magic[ 3 ] ) ) {
											$magic_tag = implode( ', ', $post_metavalue );
										} else {
											$outmagic = array();
											foreach ( $magic[ 3 ] as $subkey => $subvalue ) {
												foreach ( (array) $post_metavalue as $subsubkey => $subsubval ) {
													if ( isset( $subsubval[ $subvalue ] ) ) {
														$outmagic[] = $post_metavalue;
													}
												}
											}
											$magic_tag = implode( ', ', $outmagic );
										}
									}
								} else {
									$magic_tag = null;
								}
								break;
						}
					} else {
						switch ( $magic_tag ) {
							case 'entry_id':
								$magic_tag = self::get_field_data( '_entry_id', $this_form );
								if ( $magic_tag === null ) {
									// check if theres an entry
									if ( ! empty( $_GET[ 'cf_ee' ] ) ) {
										$entry = self::get_entry_detail( $_GET[ 'cf_ee' ], $this_form );
										if ( ! empty( $entry ) ) {
											$magic_tag = $entry[ 'id' ];
										}
									}
								}
								break;
							case 'entry_token':
								$magic_tag = self::get_field_data( '_entry_token', $this_form );
								break;
							case 'ip':

								$ip = $_SERVER[ 'REMOTE_ADDR' ];
								if ( ! empty( $_SERVER[ 'HTTP_CLIENT_IP' ] ) ) {
									$ip = $_SERVER[ 'HTTP_CLIENT_IP' ];
								} elseif ( ! empty( $_SERVER[ 'HTTP_X_FORWARDED_FOR' ] ) ) {
									$ip = $_SERVER[ 'HTTP_X_FORWARDED_FOR' ];
								}

								$magic_tag = $ip;

								break;
							case 'ua':
								$magic_tag = $_SERVER[ 'HTTP_USER_AGENT' ];
								break;
							case 'summary':
								if ( ! empty( $this_form[ 'fields' ] ) ) {
									if ( ! isset( $this_form[ 'mailer' ][ 'email_type' ] ) || $this_form[ 'mailer' ][ 'email_type' ] == 'html' ) {
										$html    = true;
										$pattern = '<strong>%s</strong><div style="margin-bottom:20px;">%s</div>';

									} else {
										$html = false;
									}

									$out = array();
									foreach ( $this_form[ 'fields' ] as $field_id => $field ) {

										if ( in_array( $field[ 'type' ], array( 'button', 'recaptcha', 'html' ) ) ) {
											continue;
										}
										// filter the field to get field data
										$field = apply_filters( 'caldera_forms_render_get_field', $field, $this_form );
										$field = apply_filters( 'caldera_forms_render_get_field_type-' . $field[ 'type' ], $field, $this_form );
										$field = apply_filters( 'caldera_forms_render_get_field_slug-' . $field[ 'slug' ], $field, $this_form );

										$field_values = (array) self::get_field_data( $field_id, $this_form );

										if ( isset( $field_values[ 'label' ] ) ) {
											$field_values = $field_values[ 'value' ];
										} else {
											foreach ( $field_values as $field_key => $field_value ) {
												if ( isset( $field_value[ 'label' ] ) && isset( $field_value[ 'value' ] ) ) {
													$field_value[ $field_key ] = $field_value[ 'value' ];
												}

											}
										}

										$field_value = implode( ', ', (array) $field_values );

										if ( $field_value !== null && strlen( $field_value ) > 0 ) {
											if ( $html ) {
												$out[] = sprintf( $pattern, $field[ 'label' ], $field_value );
											} else {
												$out[] = $field[ 'label' ] . ': ' . $field_value;
											}
										}
									}

									// vars
									if ( ! empty( $this_form[ 'variables' ] ) ) {
										foreach ( $this_form[ 'variables' ][ 'keys' ] as $var_key => $var_label ) {
											if ( $this_form[ 'variables' ][ 'types' ][ $var_key ] == 'entryitem' ) {
												$label = ucfirst( str_replace( '_', ' ', $var_label ) );
												if ( $html ) {
													$out[] = sprintf( $pattern, $label, $this_form[ 'variables' ][ 'values' ][ $var_key ] );
												} else {
													$out[] = $label . ': ' . $this_form[ 'variables' ][ 'values' ][ $var_key ];
												}
											}
										}
									}
									if ( ! empty( $out ) ) {
										$magic_tag = implode( "\r\n", $out );
									} else {
										$magic_tag = '';
									}
								}
								break;
							case 'login_url' :
								$magic_tag = wp_login_url();
								break;
							case 'logout_url' :
								$magic_tag = wp_logout_url();
								break;
							case 'register_url' :
								$magic_tag = wp_registration_url();
								break;
							case 'lostpassword_url' :
								$magic_tag = wp_lostpassword_url();
								break;


						}
					}

					$filter_value = apply_filters( 'caldera_forms_do_magic_tag', $magic_tag, $magics[ 0 ][ $magic_key ] );

					if ( ! empty( $this_form[ 'ID' ] ) ) {

						// split processor

						if ( ! empty( $magic[ 1 ] ) ) {
							if ( false !== strpos( $magic[ 1 ], ':' ) ) {
								$magic = array_reverse( explode( ':', $magic[ 1 ] ) );
							}
						}
						// check if its a process id or processor slug
						if ( empty( $processed_meta[ $this_form[ 'ID' ] ][ $magic[ 0 ] ] ) && ! empty( $this_form[ 'processors' ] ) ) {

							// if not a direct chec if theres a slug
							foreach ( $this_form[ 'processors' ] as $processid => $processor ) {
								if ( $processor[ 'type' ] === $magic[ 0 ] ) {
									if ( ! empty( $processed_meta[ $this_form[ 'ID' ] ][ $processid ] ) ) {
										$magic[ 0 ] = $processid;
										break;
									}
								}
							}
						}
						if ( ! empty( $processed_meta[ $this_form[ 'ID' ] ][ $magic[ 0 ] ] ) ) {

							if ( isset( $processed_meta[ $this_form[ 'ID' ] ][ $magic[ 0 ] ][ $magic[ 1 ] ] ) ) {
								// direct fined
								$filter_value = implode( ', ', (array) $processed_meta[ $this_form[ 'ID' ] ][ $magic[ 0 ] ][ $magic[ 1 ] ] );
							} else {
								foreach ( $processed_meta[ $this_form[ 'ID' ] ][ $magic[ 0 ] ] as $return_array ) {
									foreach ( $return_array as $return_line ) {
										if ( isset( $return_line[ $magic[ 1 ] ] ) ) {
											$filter_value = $return_line[ $magic[ 1 ] ];
										}
									}
								}
							}
						}
					}

					if ( $filter_value != $magics[ 1 ][ $magic_key ] ) {
						$value = str_replace( $magics[ 0 ][ $magic_key ], $filter_value, $value );
					}

				}
			}

			// fields
			$regex = "/%([a-zA-Z0-9_:]*)%/";

			preg_match_all( $regex, $value, $matches );
			if ( ! empty( $matches[ 1 ] ) ) {
				foreach ( $matches[ 1 ] as $key => $tag ) {
					// check for parts
					$part_tags = explode( ':', $tag );
					if ( ! empty( $part_tags[ 1 ] ) ) {
						$tag = $part_tags[ 0 ];
					}
					$entry = self::get_slug_data( $tag, $this_form, $entry_id );

					if ( $entry !== null ) {
						$field = self::get_field_by_slug( $tag, $this_form );
					}


					if ( ! empty( $field ) && ! empty( $part_tags[ 1 ] ) && $part_tags[ 1 ] == 'label' ) {
						if ( ! is_array( $entry ) ) {
							$entry = (array) $entry;
						}
						foreach ( (array) $entry as $entry_key => $entry_line ) {
							if ( ! empty( $field[ 'config' ][ 'option' ] ) ) {
								foreach ( $field[ 'config' ][ 'option' ] as $option ) {
									if ( $option[ 'value' ] == $entry_line ) {
										$entry[ $entry_key ] = $option[ 'label' ];
									}
								}
							}
						}
					}

					if ( is_array( $entry ) ) {

						if ( count( $entry ) === 1 ) {
							$entry = array_shift( $entry );
						}elseif(count($entry) === 2){
							$entry = implode(', ', $entry);
						}elseif(count($entry) > 2){
							$last = array_pop($entry);
							$entry = implode(', ', $entry).', '.$last;
						}else{
							$entry = null;
						}
					}

					$value = str_replace($matches[0][$key], $entry, $value);
				}
			}
		}

		return $value;
	}

	/**
	 * Get all types of fields currently available.
	 *
	 * @return array Array of field types.
	 */
	static public function get_field_types(){


		$field_types = apply_filters( 'caldera_forms_get_field_types', array() );

		if(!empty($field_types)){
			foreach($field_types as $fieldType=>$fieldConfig){
				// check for a viewer
				if(isset($fieldConfig['viewer'])){
					add_filter('caldera_forms_view_field_' . $fieldType, $fieldConfig['viewer'], 10, 3);
				}
			}
		}

		return $field_types;

	}

	/**
	 * Get all processors, in a form, of a specific type
	 *
	 * @param string $type Processor type.
	 * @param array $form Form config
	 *
	 * @return array|bool Processor config if found. False if not.
	 */
	static public function get_processor_by_type($type, $form){
		if(is_string($form)){
			$form_cfg = Caldera_Forms_Forms::get_form( $form );
			if(!empty($form_cfg['ID'])){
				if($form_cfg['ID'] !== $form || empty($form_cfg['processors'])){
					return false;
				}
			}
			$form = $form_cfg;
		}

		if(!empty($form['processors'])){
			$processors = array();
			foreach($form['processors'] as $processor){
				if($processor['type'] == $type){
					$processors[] = $processor;
					$processors[ $processor['ID'] ] = $processor;
				}
			}
			if(empty($processors)){
				return false;
			}
			return $processors;
		}
		return false;
	}

	/**
	 * Set a specific meta key from form meta.
	 *
	 * @param string $key Name of key.
	 * @param mixed $value Value to save.
	 * @param string|array form Form config array or ID of form.
	 * @param string $processor_id Optional. ID of processor. Default is "meta"
	 *
	 * @return bool
	 */
	static public function set_submission_meta($key, $value, $form, $processor_id='meta'){
		global $processed_meta;

		if(is_string($form)){
			$form['ID'] = $form;
		}

		// set value
		if(isset($form['ID'])){
			if(isset($processed_meta[$form['ID']][$processor_id][$key])){
				if(in_array($value, $processed_meta[$form['ID']][$processor_id][$key])){
					return true;
				}
			}
			$processed_meta[$form['ID']][$processor_id][$key][] = $value;
			return true;
		}
	}

	/**
	 * Set a field's data to be saved form a form entry.
	 *
	 * @param string $field_id ID of field.
	 * @param mixed $data Data to save.
	 * @param string|array form Form config array or ID of form.
	 * @param bool|false $entry_id Optional. Entry ID to save in.
	 *
	 * @return bool
	 */
	static public function set_field_data($field_id, $data, $form, $entry_id = false){
		global $processed_data;

		$current_data = self::get_field_data($field_id, $form, $entry_id);

		if(is_string($form)){
			$form = Caldera_Forms_Forms::get_form( $form );
		}

		// form object
		if(isset($form['ID'])){
			if( isset( $form['fields'][$field_id] ) ){
				$processed_data[$form['ID']][$field_id] = $data;
				return true;
			}else{
				// is field_id a slug perhaps?
				foreach ($form['fields'] as $field) {
					if( $field['slug'] == $field_id ){
						$processed_data[$form['ID']][ $field['ID'] ] = $data;
						return true;
					}
				}
			}
		}

		// generic field data
		$processed_data[$form['ID']][$field_id] = $data;
		return true;
	}

	/**
	 * Get a field's data.
	 *
	 * @param string $field_id ID of field.
	 * @param string|array $form Form config array or ID of form.
	 * @param bool|false $entry_id Optional. Entry ID to save in.
	 *
	 * @return bool
	 */
	static public function get_field_data($field_id, $form, $entry_id = false){
		global $processed_data;

		//echo $field_id.'<br>';
		if(is_string($form)){
			$form = Caldera_Forms_Forms::get_form( $form );
			if(!isset($form['ID']) || $form['ID'] !== $form){
				return null;
			}
		}

		$indexkey = $form['ID'];
		if(!empty($entry_id)){
			$indexkey = $form['ID'] . '_' . $entry_id;
		}
		// is ID or slug?
		if( !isset( $form['fields'][$field_id] ) ){
			foreach ($form['fields'] as $field) {
				if( $field['slug'] == $field_id ){
					$field_id = $field['ID'];
					break;
				}
			}
		}

		// get processed cached item
		if(isset($processed_data[$indexkey][$field_id])){
			return $processed_data[$indexkey][$field_id];
		}

		// entry fetch
		if(!empty($entry_id) && isset($form['fields'][$field_id])){

			global $wpdb;

			$entry = $wpdb->get_results($wpdb->prepare("
				SELECT `value` FROM `" . $wpdb->prefix ."cf_form_entry_values` WHERE `entry_id` = %d AND `field_id` = %s AND `slug` = %s", $entry_id, $field_id, $form['fields'][$field_id]['slug']), ARRAY_A);

			//allow plugins to alter the value
			$entry = apply_filters( 'caldera_forms_get_field_entry', $entry, $field_id, $form, $entry_id);

			if(!empty($entry)){
				if( count( $entry ) > 1){
					$out = array();
					foreach($entry as $item){
						$out[] = $item['value'];
					}
					$processed_data[$indexkey][$field_id] = $out;
				}else{
					$processed_data[$indexkey][$field_id] = $entry[0]['value'];
				}
			}else{
				$processed_data[$indexkey][$field_id] = null;
			}
			return $processed_data[$indexkey][$field_id];
			//return $processed_data[$indexkey][$field_id] = ;
		}

		if(isset($form['fields'][$field_id])){

			// get field
			$field = apply_filters( 'caldera_forms_render_setup_field', $form['fields'][$field_id], $form);

			if(empty($field) || !isset($field['ID'])){
				return null;
			}
			// get field types
			$field_types = self::get_field_types();

			if(!isset($field_types[$field['type']])){
				return null;
			}
			$entry = null;
			// dont bother if conditions say it shouldnt be here.

			if(!empty($field['conditions']['type'])){
				if(!self::check_condition($field['conditions'], $form, $entry_id)){
					$processed_data[$indexkey][$field_id] = $entry;
					return $entry;
				}
			}


			// check condition to see if field should be there first.
			// check if conditions match first. ignore vailators if not part of condition
			if(isset($_POST[$field_id])){
				$entry = stripslashes_deep( $_POST[$field_id] );

			}elseif(isset($_POST[$field['slug']])){
				// is slug maybe?
				$entry = stripslashes_deep( $_POST[$field['slug']] );
			}
			// apply field filter
			if(has_filter('caldera_forms_process_field_' . $field['type'])){
				$entry = apply_filters( 'caldera_forms_process_field_' . $field['type'] , $entry, $field, $form );
				if( is_wp_error( $entry ) ) {
					$processed_data[$indexkey][$field_id] = $entry;
					return $entry;
				}
			}

			if(is_string( $entry ) && strlen( $entry ) <= 0){
				$entry = null;
			}
			// is static
			if(!empty($field_types[$field['type']]['static'])){
				// is options or not
				if(!empty($field_types[$field['type']]['options'])){
					if(is_array($entry)){
						$out = array();
						foreach($entry as $option_id=>$option){
							if(isset($field['config']['option'][$option_id])){
								if(!isset($field['config']['option'][$option_id]['value'])){
									$field['config']['option'][$option_id]['value'] = $field['config']['option'][$option_id]['label'];
								}
								$out[ $option_id ] = self::do_magic_tags($field['config']['option'][$option_id]['value']);
								//$out[ $option_id ] = array( 'value' => self::do_magic_tags($field['config']['option'][$option_id]['value']), 'label' => $field['config']['option'][$option_id]['label'] );
							}elseif( isset($field['config']['option'][$option] ) ){
								if(!isset($field['config']['option'][$option]['value'])){
									$field['config']['option'][$option]['value'] = $field['config']['option'][$option]['label'];
								}
								$out[ $option_id ] = self::do_magic_tags($field['config']['option'][$option]['value']);
								//$out[ $option_id ] = array( 'value' => self::do_magic_tags($field['config']['option'][$option]['value']), 'label' => $field['config']['option'][$option]['label'] );
							}else{
								// array based / check value agains submitted array.
								foreach ( $field['config']['option'] as $option_id => $set_option) {
									if( $set_option['value'] === $option ){
										$out[] = self::do_magic_tags( $set_option['value'] );
									}
								}

							}

						}
						$processed_data[$indexkey][$field_id] = $out;
					}else{
						if(!empty($field['config']['option'])){
							foreach($field['config']['option'] as $option){
								if($option['value'] == $entry){
									$processed_data[$indexkey][$field_id] = self::do_magic_tags($entry);
									break;
								}
							}
						}
					}
				}else{
					$processed_data[$indexkey][$field_id] = self::do_magic_tags($field['config']['default']);
				}
			}else{
				// dynamic
				$processed_data[$indexkey][$field_id] = $entry;
			}
		}else{

			$is_tag = self::do_magic_tags($field_id);
			if($is_tag !== $field_id){
				$processed_data[$indexkey][$field_id] = $is_tag;
			}
		}

		if(isset($processed_data[$indexkey][$field_id])){
			return $processed_data[$indexkey][$field_id];
		}


		return null;
	}

	/**
	 * Get the configuration for a field.
	 *
	 * @param string $slug Slug of field to get config for.
	 * @param array $form Form config array.
	 *
	 * @return bool|mixed|void
	 */
	static public function get_field_by_slug($slug, $form){

		foreach($form['fields'] as $field_id=>$field){

			if($field['slug'] == $slug){

				return apply_filters( 'caldera_forms_render_get_field', $field, $form );

			}
		}

		return false;

	}

	/**
	 * Get field data, by slug, and by entry.
	 *
	 * @param string $slug Slug of field to get config for.
	 * @param array $form Form config array.
	 * @param bool|false $entry_id Optional. The entry ID.
	 *
	 * @return bool|array
	 */
	static public function get_slug_data($slug, $form, $entry_id = false){


		$out = array();
		if(false !== strpos($slug, '.')){
			$slug_parts = explode('.', $slug);
			$slug = array_shift($slug_parts);
		}

		$field_types = self::get_field_types();

		foreach($form['fields'] as $field_id=>$field){

			if($field['slug'] == $slug){

				return self::get_field_data( $field_id, $form, $entry_id);

			}
		}

	}

	/**
	 * Get saved data for a form entry
	 *
	 * @param int $entry_id Entry ID
	 * @param null|array $form Optional. Form config.
	 *
	 * @return array|null|void
	 */
	static public function get_entry_detail($entry_id, $form = null){
		global $wpdb, $form;

		$entry = $wpdb->get_row($wpdb->prepare("SELECT * FROM `" . $wpdb->prefix ."cf_form_entries` WHERE `id` = %d", $entry_id ), ARRAY_A);
		if(!empty($entry)){
			if( null === $form ){
				$form = Caldera_Forms_Forms::get_form( $entry['form_id'] );
				if( empty($form) ){
					return null;
				}
			}
			// get meta if any
			$meta = self::get_entry_meta($entry_id, $form);
			if(!empty($meta)){
				$entry['meta'] = $meta;
			}
		}
		$entry = apply_filters( 'caldera_forms_get_entry_detail', $entry, $entry_id, $form );
		return $entry;
	}

	/**
	 * Get all meta for an entry.
	 *
	 * @param int $entry_id Entry ID
	 * @param array $form Form config.
	 * @param null|string $type Optional. Type of meta to get. If null, the default, all meta is returned.
	 *
	 * @return array
	 */
	static public function get_entry_meta($entry_id, $form, $type = null){
		global $wpdb;

		$entry_meta = array();

		$entry_meta_data = $wpdb->get_results($wpdb->prepare("SELECT * FROM `" . $wpdb->prefix ."cf_form_entry_meta` WHERE `entry_id` = %d", $entry_id), ARRAY_A);

		if(!empty($entry_meta_data)){
			$processors = Caldera_Forms_Processor_Load::get_instance()->get_processors();
			foreach($entry_meta_data as $meta_index=>$meta){

				// is json?
				$is_json = @json_decode($meta['meta_value'], ARRAY_A);
				if( !empty( $is_json ) ){
					$meta['meta_value'] = $is_json;
				}

				$group = 'meta';
				$meta = apply_filters( 'caldera_forms_get_entry_meta', $meta, $form);

				if( isset( $form['processors'][$meta['process_id']] ) || $meta['process_id'] == '_debug_log' ){

					if( $meta['process_id'] == '_debug_log' ){
						$meta['meta_value'] = '<pre>' . $meta['meta_value'] . '</pre>';
						$entry_meta['debug'] = array(
							'name' => __( 'Mailer Debug', 'caldera-forms' ),
							'data' => array(
								'_debug_log' => array(
									'entry' => array(
										'log' => $meta
									)
								)
							)
						);
						continue;
					}

					$process_config = array();
					if(isset($form['processors'][$meta['process_id']]['config'])){
						$process_config = $form['processors'][$meta['process_id']]['config'];
					}

					$group = $form['processors'][$meta['process_id']]['type'];
					if(!empty($type)){
						if($group != $type){
							continue;
						}
					}
					$meta = apply_filters( 'caldera_forms_get_entry_meta_' . $form['processors'][$meta['process_id']]['type'], $meta, $process_config , $form);


					// allows plugins to remove it.
					if(!empty($meta)){
						if(!isset($entry_meta[$group])){
							// is processor
							if(isset($form['processors'][$meta['process_id']]['type']) && isset( $processors[$form['processors'][$meta['process_id']]['type']] ) ){
								$meta_name = $processors[$form['processors'][$meta['process_id']]['type']]['name'];
							}else{
								if( $meta['process_id'] == '_debug_log' ){
									$meta_name = __( 'Mailer Debug', 'caldera-forms' );
								}else{
									$meta_name = $meta['process_id'];
								}

							}
							$entry_meta[$group] = array(
								'name' => $meta_name,
								'data' => array()
							);
							// custom template
							if( isset( $processors[$form['processors'][$meta['process_id']]['type']]['meta_template'] ) && file_exists( $processors[$form['processors'][$meta['process_id']]['type']]['meta_template'] ) ){
								$entry_meta[$group][$group.'_template'] = $entry_meta[$group]['template'] = true;
							}
						}

						//if(!empty($meta['meta_title'])){
						//	$entry_meta[$group]['data'][$meta['process_id']]['title'] = $meta['meta_title'];
						//}

						$entry_meta[$group]['data'][$meta['process_id']]['entry'][$meta['meta_key']] = $meta;


						/*if(is_array($meta['meta_value'])){
							foreach($meta['meta_value'] as $mkey=>$mval){
								$entry['meta'][$group]['data'][$meta['process_id']]['title'] = $meta['meta_key'];
								$entry['meta'][$group]['data'][$meta['process_id']]['entry'][] = array(
									'meta_key'		=> $mkey,
									'meta_value' 	=> $mval
								);
							}
						}else{
							$entry['meta'][$group]['data'][$meta['process_id']]['entry'][] = array(
								'meta_key'		=> $meta['meta_key'],
								'meta_value' 	=> $meta['meta_value']
							);
						}*/

					}
				}
			}
		}
		// if type
		if(!empty($type)){
			//return only type
			if(!empty($entry_meta[$type]['data'])){
				return $entry_meta[$type]['data'];
			}
		}
		return $entry_meta;
	}

	/**
	 * Get submission data from a form being submitted or a saved entry
	 * @param array $form Form Config.
	 * @param bool|false $entry_id Optional. Entry ID to get data for, or if false, the default, get form current submission.
	 *
	 * @return array|\WP_Error
	 */
	static public function get_submission_data($form, $entry_id = false){
		global $processed_data;

		if(is_string($form)){
			$form_id = $form;
			$form = Caldera_Forms_Forms::get_form( $form );
			if(!isset($form['ID']) || $form['ID'] !== $form_id){
				return new WP_Error( 'fail',  __( 'Invalid form ID', 'caldera-forms' ) );
			}
		}

		$indexkey = $form['ID'];
		if(!empty($entry_id)){
			$indexkey = $form['ID'] . '_' . $entry_id;
		}

		// get processed cached item using the form id
		if(isset($processed_data[$indexkey])){
			return $processed_data[$indexkey];
		}
		// prep data array
		$processed_data[$indexkey] = array();

		// initialize process data
		foreach($form['fields'] as $field_id=>$field){
			// get data
			if(!empty($field['conditions']['type'])){
				if(!self::check_condition($field['conditions'], $form, $entry_id)){
					continue;
				}
			}

			self::get_field_data( $field_id, $form, $entry_id);
		}

		return $processed_data[$indexkey];
	}

	/**
	 * Process current POST data as form submission.
	 */
	static public function process_submission(){
		//ob_flush();
		global $post;
		global $process_id;
		global $form;
		global $field_types;
		global $rawdata;
		global $processed_data;
		global $transdata;
		global $wpdb;
		global $referrer;

		// clean out referrer
		if(empty($_POST['_wp_http_referer_true'])){
			$_POST['_wp_http_referer_true'] = $_SERVER['HTTP_REFERER'];
		}

		$referrer = parse_url( $_POST['_wp_http_referer_true'] );
		if(!empty($referrer['query'])){
			parse_str($referrer['query'], $referrer['query']);
			if(isset($referrer['query']['cf_er'])){
				unset($referrer['query']['cf_er']);
			}
			if(isset($referrer['query']['cf_su'])){
				unset($referrer['query']['cf_su']);
			}
		}
		if( ( isset( $_POST['_cf_cr_pst'] ) && ! is_object( $post ) ) || ( isset( $_POST['_cf_cr_pst'] ) && $post->ID !== (int) $_POST['_cf_cr_pst'] ) ){
			$post = get_post( (int) $_POST['_cf_cr_pst'] );
		}
		// get form and check
		$form = Caldera_Forms_Forms::get_form( $_POST['_cf_frm_id'] );
		if(empty($form['ID']) || $form['ID'] != $_POST['_cf_frm_id']){
			return;
		}

		// instance number
		$form_instance_number = 1;
		if(isset($_POST['_cf_frm_ct'])){
			$form_instance_number = $_POST['_cf_frm_ct'];
		}

		// check honey
		if(isset($form['check_honey'])){
			// use multiple honey words
			$honey_words = apply_filters( 'caldera_forms_get_honey_words', array('web_site', 'url', 'email', 'company', 'name'));
			foreach($_POST as $honey_word=>$honey_value){

				if(!is_array( $honey_value ) && strlen($honey_value) && in_array($honey_word, $honey_words)){
					// yupo - bye bye
					$referrer['query']['cf_su'] = $form_instance_number;
					$query_str = array(
						'cf_er' => $process_id
					);
					if(!empty($referrer['query'])){
						$query_str = array_merge($referrer['query'], $query_str);
					}
					$referrer = $referrer['path'] . '?' . http_build_query($query_str);
					return self::form_redirect('complete', $referrer, $form, uniqid('_cf_bliss_') );
				}
			}
		}
		// init filter
		$form = apply_filters( 'caldera_forms_submit_get_form', $form);


		/**
		 * Runs at beginning of process of submitting form
		 *
		 * @since unknown
		 *
		 * @param array $form Form config
		 * @param string $process_id Process ID, may not be set yet.
		 *
		 */
		do_action('caldera_forms_submit_start', $form, $process_id );


		if(!empty($form['fields'])){
			foreach($form['fields'] as $field_id=>$field){
				$field = apply_filters( 'caldera_forms_render_get_field', $field, $form);
				$field = apply_filters( 'caldera_forms_render_get_field_type-' . $field['type'], $field, $form);
				$field = apply_filters( 'caldera_forms_render_get_field_slug-' . $field['slug'], $field, $form);
				if( ! is_array( $field ) || empty( $field ) ){
					unset( $form['fields'][$field_id] );
				}else{
					$form['fields'][$field_id] = $field;
				}

			}
		}

		// check source is ajax to overide
		if( !empty($_POST['cfajax']) && $_POST['cfajax'] == $form['ID'] ){
			$form['form_ajax'] = 1;
		}

		// get all fieldtype
		$field_types = self::get_field_types();

		// setup fieldtypes field submissions
		if(!empty($field_types)){
			foreach($field_types as $fieldType=>$fieldConfig){
				// check for a handler
				if(isset($fieldConfig['handler'])){
					add_filter('caldera_forms_process_field_' . $fieldType, $fieldConfig['handler'], 10, 3);
				}
				// check for a hash
				if(isset($fieldConfig['save'])){
					add_filter('caldera_forms_save_field_' . $fieldType, $fieldConfig['save'], 10, 3);
				}
				// check for a hash
				if(isset($fieldConfig['validate'])){
					add_filter('caldera_forms_validate_field_' . $fieldType, $fieldConfig['validate'], 10, 3);
				}
			}
		}

		// SET process ID
		if(isset($_GET['cf_er'])){
			$_POST['_cf_frm_tr'] = $_GET['cf_er'];
		}


		if(isset($_POST['_cf_frm_tr'])){
			$pretransient = get_transient( $_POST['_cf_frm_tr'] );
			if(	!empty( $pretransient['transient'] ) && $pretransient['transient'] === $_POST['_cf_frm_tr']){
				$transdata = $pretransient;
				$process_id = $transdata['transient'];
				// unset error details
				if(isset($transdata['type'])){
					unset( $transdata['type'] );
				}
				if(isset($transdata['note'])){
					unset( $transdata['note'] );
				}
				if(isset($transdata['error'])){
					unset( $transdata['error'] );
				}
				if(isset($transdata['fields'])){
					unset( $transdata['fields'] );
				}

			}
		}
		if(empty($process_id)){
			$process_id = uniqid('_cf_process_');
		}

		// initialize data
		$entry_id = false;
		if(isset($_POST['_cf_frm_edt'])){
			$entry_id = (int) $_POST['_cf_frm_edt'];
		}
		// dont get data with ID else update wont work. since it will update the same thing
		//$data = self::get_submission_data($form, $entry_id);
		$data = self::get_submission_data($form);
		//dump($data);
		// requireds
		// set transient for returns submittions
		if(empty($transdata)){
			$transdata = array(
				'transient' 	=> $process_id,
				'form_instance' => $form_instance_number,
				'expire'		=> 120,
				'data' 			=> array_merge($_POST, $data),
			);
		}
		// remove AJAX value for tp_
		if(isset($transdata['data']['cfajax'])){
			unset($transdata['data']['cfajax']);
		}
		// setup transient data
		$transdata = apply_filters( 'caldera_forms_submit_transient_setup', $transdata);

		// setup processor bound requieds
		if(!empty($form['processors'])){
			$bound_fields = array();
			foreach($form['processors'] as $processor_id=>$processor){

				if(!empty($processor['config']['_required_bounds'])){
					foreach($processor['config'] as $slug=>&$value){
						if($slug == '_required_bounds'){
							continue;
						}

						if(in_array($slug, $processor['config']['_required_bounds'])){
							if(isset($form['fields'][$value])){
								if(!isset($process_data[$value])){
									$form['fields'][$value]['required'] = 1;
								}
							}
						}
					}
				}
			}
		}

		// check submit type (new or update)
		if(isset($_POST['_cf_frm_edt'])){
			// is edit
			//check user can edit this item.
			$transdata['edit'] = (int) $_POST['_cf_frm_edt'];
			// set entry_id
			self::set_field_data('_entry_id', $transdata['edit'], $form);
			$details = self::get_entry_detail($_POST['_cf_frm_edt'], $form);
			$user_id = get_current_user_id();

			// check token
			if(isset($_POST['_cf_frm_edt_tkn'])){

				// build token
				$token_array = array(
					'id'		=>	(int) $entry_id,
					'datestamp'	=>	$details['datestamp'],
					'user_id'	=>	(int) $details['user_id'],
					'form_id'	=>	$form['ID']
				);
				if( sha1( json_encode( $token_array ) ) !== trim( $_POST['_cf_frm_edt_tkn'] ) ){
					return new WP_Error( 'error', __( "Permission denied.", 'caldera-forms' ) );
				}else{
					$entry_id = (int) $details['id'];
					$edit_token = sha1( json_encode( $token_array ) );
				}

			}else{

				if(empty($user_id)){
					$transdata['error'] = true;
					$transdata['note'] = __( 'Permission denied or entry does not exist.', 'caldera-forms' );
				}else{

					if(empty($details)){
						$transdata['error'] = true;
						$transdata['note'] = __( 'Permission denied or entry does not exist.', 'caldera-forms' );
					}else{
						// check user can edit
						if( current_user_can( 'edit_posts' ) || $details['user_id'] === $user_id ){
							// can edit.
						}else{
							$transdata['error'] = true;
							$transdata['note'] = __( 'Permission denied or entry does not exist.', 'caldera-forms' );
						}
					}

				}

			}
		}


		// start brining in entries
		foreach($form['fields'] as $field_id=>$field){

			$entry = self::get_field_data($field_id, $form);

			if ( is_wp_error( $entry )){
				$transdata['fields'][$field_id] = $entry->get_error_message();
			}else{
				// required check
				$failed = false;
				// run validators
				if(has_filter('caldera_forms_validate_field_' . $field['type'])){
					$entry = apply_filters( 'caldera_forms_validate_field_' . $field['type'], $entry, $field, $form );
				}
				// if required, check the validators returned errors or not.
				if( !empty( $field['required'] ) ){
					// check is supported
					if( isset( $field_types[ $field['type'] ]['setup']['not_supported'] ) && in_array( 'required', (array) $field_types[ $field['type'] ]['setup']['not_supported'] ) ){
						continue;
					}
					// check if conditions match first. ignore vailators if not part of condition
					if(!empty($field['conditions']['type'])){
						if(!self::check_condition($field['conditions'], $form)){
							continue;
						}
					}
					// if error - return so
					if ( is_wp_error( $entry )){
						$transdata['fields'][$field_id] = $entry->get_error_message();
					}elseif($entry === null){
						$transdata['fields'][$field_id] = $field['label'] .' ' .__( 'is required', 'caldera-forms' );
					}
				}
			}

		}

		// check requireds
		if(!empty($transdata['fields']) || !empty($transdata['error'])){
			$transdata['type'] = 'error';
			// set error transient
			$transdata = apply_filters( 'caldera_forms_submit_return_transient', $transdata, $form, $referrer, $process_id);
			$transdata = apply_filters( 'caldera_forms_submit_return_transient_required', $transdata, $form, $referrer, $process_id);

			// back to form
			$query_str = array(
				'cf_er' => $process_id
			);
			if(!empty($referrer['query'])){
				$query_str = array_merge($referrer['query'], $query_str);
			}
			$referrer = $referrer['path'] . '?' . http_build_query($query_str);
			$referrer = apply_filters( 'caldera_forms_submit_return_redirect', $referrer, $form, $process_id);
			$referrer = apply_filters( 'caldera_forms_submit_return_redirect_required', $referrer, $form, $process_id);

			set_transient( $process_id, $transdata, $transdata['expire']);

			return self::form_redirect('error', $referrer, $form, $process_id );
		}


		// has processors
		do_action('caldera_forms_submit_start_processors', $form, $referrer, $process_id);
		if(!isset($form['processors'])){
			$form['processors'] = array();
		}

		// get all form processors
		$form_processors = Caldera_Forms_Processor_Load::get_instance()->get_processors();

		/**
		 * Runs before the 1st stage of processors "pre-process"
		 *
		 * @since unknown
		 *
		 * @param array $form Form config
		 * @param array $referrer URL form was submitted via -- is passed through parse_url() before this point.
		 * @param string $process_id Unique ID for this processing
		 */
		do_action('caldera_forms_submit_pre_process_start', $form, $referrer, $process_id);

		/**
		 * Remove processors that are not allowed to run on this pass
		 *
		 * @since 1.3.2
		 *
		 */
		foreach($form['processors'] as $processor_id=>$processor){
			// the cf_version value in the form was introduced in 1.3.2
			// so if its set, its safe to asume the runtimes are set.
			if( !isset( $form['cf_version'] ) ){
				// nope
				if( !empty( $transdata['edit'] ) ){
					unset( $form['processors'][ $processor_id ] );
				}

				continue;
			}
			// normal check within version
			// chec if editing and is allowed
			if( !empty( $transdata['edit'] ) && empty( $processor['runtimes']['update'] ) ){
				// is editing and is set to not allow it so remove processor
				unset( $form['processors'][ $processor_id ] );
				continue;
			}
			if( empty( $transdata['edit'] ) && empty( $processor['runtimes']['insert'] ) ){
				// is editing and is set to not allow it
				unset( $form['processors'][ $processor_id ] );
				continue;
			}

		}

		// PRE PROCESS
		foreach($form['processors'] as $processor_id=>$processor){

			if(isset($form_processors[$processor['type']])){

				// Do Conditional
				if(isset($processor['conditions']) && !empty($processor['conditions']['type'])){
					if(!self::check_condition($processor['conditions'], $form)){
						continue;
					}
				}

				// has processor
				$process = $form_processors[$processor['type']];
				if(!isset($process['pre_processor'])){
					continue;
				}


				// set default config
				$config = array();
				$config['processor_id'] = $processor_id;

				if(isset($process['default'])){
					$config = $process['default'];
				}
				if(!empty($processor['config'])){

					$config = array_merge($config, $processor['config']);
				}
				if(is_array($process['pre_processor'])){
					$process_line_data = call_user_func_array($process['pre_processor'],array($config, $form, $process_id));
				}else{
					if(function_exists($process['pre_processor'])){
						$func = $process['pre_processor'];
						$process_line_data = $func($config, $form, $process_id);
					}
				}
				// pre processors should not return unless a break in action for further
				// Returned something - check it
				if(!empty($process_line_data)){
					if(is_array($process_line_data)){
						//type
						if(!empty($process_line_data['type'])){
							$transdata['type'] = $process_line_data['type'];
							// has note?
							if(!empty($process_line_data['note'])){
								$transdata['note'] = $process_line_data['note'];
							}
						}

						// fields involved?
						if(!empty($process_line_data['fields'])){
							$transdata['fields'] = $process_line_data['fields'];
						}

						// set error transient
						$transdata = apply_filters( 'caldera_forms_submit_return_transient', $transdata, $form, $referrer, $process_id);
						$transdata = apply_filters( 'caldera_forms_submit_return_transient_pre_process', $transdata, $form, $referrer, $process_id);

						// back to form
						$query_str = array(
							'cf_er' => $process_id
						);
						if(!empty($referrer['query'])){
							$query_str = array_merge($referrer['query'], $query_str);
						}
						$referrer = $referrer['path'] . '?' . http_build_query($query_str);
						$referrer = apply_filters( 'caldera_forms_submit_return_redirect', $referrer, $form, $process_id);
						$referrer = apply_filters( 'caldera_forms_submit_return_redirect-'.$processor['type'], $referrer, $config, $form, $process_id);

						// set transient data
						set_transient( $process_id, $transdata, $transdata['expire']);

						return self::form_redirect('preprocess', $referrer, $form, $process_id );
					}
				}
			}
		}

		/**
		 * Runs after the 1st stage of processors "pre-process"
		 *
		 * @since unknown
		 *
		 * @param array $form Form config
		 * @param array $referrer URL form was submitted via -- is passed through parse_url() before this point.
		 * @param string $process_id Unique ID for this processing
		 */
		do_action('caldera_forms_submit_pre_process_end', $form, $referrer, $process_id);
		/// AFTER PRE-PROCESS - check for errors etc to return else continue to process.
		if( empty( $transdata['edit'] ) && !empty($form['db_support']) ){
			// CREATE ENTRY
			$new_entry = array(
				'form_id'	=>	$form['ID'],
				'user_id'	=>	0,
				'datestamp' =>	date_i18n( 'Y-m-d H:i:s', time(), 0),
				'status'	=>	'pending'
			);
			// if user logged in
			if(is_user_logged_in()){
				$new_entry['user_id'] = get_current_user_id();
			}else{
				if(isset($data['_user_id'])){
					$new_entry['user_id'] = $data['_user_id'];
				}
			}

			$wpdb->insert($wpdb->prefix . 'cf_form_entries', $new_entry);
			$entryid = $wpdb->insert_id;

			/**
			 * Runs after an entry is saved
			 *
			 * @since 1.2.1
			 *
			 * @param int $entryid The ID of the entry that was just saved.
			 * @param array $new_entry Data that was saved
			 * @param array $form Form being processed
			 */
			do_action( 'caldera_forms_entry_saved', $entryid, $new_entry, $form );

			// save entry_id
			self::set_field_data('_entry_id', $entryid, $form);
			// set entry token
			$token_array = array(
				'id'		=>	(int) $entryid,
				'datestamp'	=>	$new_entry['datestamp'],
				'user_id'	=>	(int) $new_entry['user_id'],
				'form_id'	=>	$form['ID']
			);
			// set edit token
			self::set_field_data('_entry_token', sha1( json_encode( $token_array ) ), $form);

		}elseif( !empty( $transdata['edit'] ) ){
			$entryid = $transdata['edit'];
		}else{
			$entryid = false;
		}

		/**
		 * Runs before the 2nd stage of processors "process"
		 *
		 * @since unknown
		 *
		 * @param array $form Form config
		 * @param array $referrer URL form was submitted via -- is passed through parse_url() before this point.
		 * @param string $process_id Unique ID for this processing
		 * @param int|false $entryid Current entry ID or false if not set or being saved.
		 */
		do_action('caldera_forms_submit_process_start', $form, $referrer, $process_id, $entryid );
		/// PROCESS
		foreach($form['processors'] as $processor_id=>$processor){
			if(isset($form_processors[$processor['type']])){
				// has processor
				// Do Conditional
				if(isset($processor['conditions']) && !empty($processor['conditions']['type'])){
					if(!self::check_condition($processor['conditions'], $form)){
						continue;
					}
				}

				$process = $form_processors[$processor['type']];
				if(!isset($process['processor'])){
					continue;
				}
				$hasmeta = null;
				// set default config
				$config = array();
				$config['processor_id'] = $processor_id;
				if(isset($process['default'])){
					$config = $process['default'];
				}
				if(!empty($processor['config'])){

					$config = array_merge($config, $processor['config']);
				}
				if(is_array($process['processor'])){
					$hasmeta = call_user_func_array($process['processor'],array($config, $form, $process_id));
				}else{
					if(function_exists($process['processor'])){
						$func = $process['processor'];
						$hasmeta = $func($config, $form, $process_id);
					}
				}
				if($hasmeta !== null){
					foreach( (array) $hasmeta as $metakey=>$metavalue){
						$meta_process_id = $processor_id;
						// single processors are generallay used so not processor id is needed
						if(!empty($form_processors[$processor['type']]['single'])){
							$meta_process_id = $processor['type'];
						}
						self::set_submission_meta($metakey, $metavalue, $form, $processor_id);
					}
				} // check for transdata errors

				if(!empty($transdata['error'])){
					// remove pending entry
					if( !empty( $entryid ) && !empty($new_entry) && $new_entry['status'] == 'pending'  ){
						// kill it with fire
						$wpdb->delete($wpdb->prefix . 'cf_form_entries', array( 'id'=>$entryid ) );
					}
					// set error transient
					$transdata = apply_filters( 'caldera_forms_submit_error_transient', $transdata, $form, $referrer, $process_id);
					$transdata = apply_filters( 'caldera_forms_submit_error_transient_pre_process', $transdata, $form, $referrer, $process_id);

					// back to form
					$query_str = array(
						'cf_er' => $process_id
					);
					if(!empty($referrer['query'])){
						$query_str = array_merge($referrer['query'], $query_str);
					}
					$referrer = $referrer['path'] . '?' . http_build_query($query_str);
					$referrer = apply_filters( 'caldera_forms_submit_error_redirect', $referrer, $form, $process_id);
					$referrer = apply_filters( 'caldera_forms_submit_error_redirect_pre_process', $referrer, $form, $process_id);

					// set transient data
					set_transient( $process_id, $transdata, $transdata['expire']);

					return self::form_redirect('error', $referrer, $form, $process_id );
				}

			}
		}

		/**
		 * Runs after the 2nd stage of processors "process"
		 *
		 * @since unknown
		 *
		 * @param array $form Form config
		 * @param array $referrer URL form was submitted via -- is passed through parse_url() before this point.
		 * @param string $process_id Unique ID for this processing
		 * @param int|false $entryid Current entry ID or false if not set or being saved.
		 */
		do_action('caldera_forms_submit_process_end', $form, $referrer, $process_id);
		// AFTER PROCESS - do post process for any additional stuff

		/**
		 * Runs before the 3rd and final stage of processors "post-process"
		 *
		 * @since unknown
		 *
		 * @param array $form Form config
		 * @param array $referrer URL form was submitted via -- is passed through parse_url() before this point.
		 * @param string $process_id Unique ID for this processing
		 * @param int|false $entryid Current entry ID or false if not set or being saved.
		 */
		do_action('caldera_forms_submit_post_process', $form, $referrer, $process_id, $entry_id );
		// POST PROCESS
		foreach($form['processors'] as $processor_id=>$processor){
			if(isset($form_processors[$processor['type']])){
				// has processor
				// Do Conditional
				if(isset($processor['conditions']) && !empty($processor['conditions']['type'])){
					if(!self::check_condition($processor['conditions'], $form)){
						continue;
					}
				}

				$process = $form_processors[$processor['type']];
				if(!isset($process['post_processor'])){
					continue;
				}
				// set default config
				$config = array();
				$config['processor_id'] = $processor_id;
				if(isset($process['default'])){
					$config = $process['default'];
				}
				if(!empty($processor['config'])){

					$config = array_merge($config, $processor['config']);
				}
				if(is_array($process['post_processor'])){
					$hasmeta = call_user_func_array($process['post_processor'],array($config, $form, $process_id));
				}else{
					if(function_exists($process['post_processor'])){
						$func = $process['post_processor'];
						$hasmeta = $func($config, $form, $process_id);
					}
				}
				if($hasmeta !== null){
					foreach( (array) $hasmeta as $metakey=>$metavalue){
						self::set_submission_meta($metakey, $metavalue, $form, $processor_id);
					}
				}

			}
		}

		/**
		 * Runs after the 3rd and final stage of processors "post-process"
		 *
		 * @since unknown
		 *
		 * @param array $form Form config
		 * @param array $referrer URL form was submitted via -- is passed through parse_url() before this point.
		 * @param string $process_id Unique ID for this processing
		 * @param int|false $entryid Current entry ID or false if not set or being saved.
		 */
		do_action('caldera_forms_submit_post_process_end', $form, $referrer, $process_id);

		/**
		 * Runs after all processing for form completes
		 *
		 * @since unknown
		 *
		 * @param array $form Form config
		 * @param array $referrer URL form was submitted via -- is passed through parse_url() before this point.
		 * @param string $process_id Unique ID for this processing
		 */
		do_action('caldera_forms_submit_complete', $form, $referrer, $process_id);

		// redirect back or to result page
		$referrer['query']['cf_su'] = $form_instance_number;

		// last entry id - new only
		if( empty( $transdata['edit'] ) ){
			$cf_id = self::do_magic_tags( '{entry_id}' );
			if( !empty( $cf_id ) ){
				$referrer['query']['cf_id'] = self::do_magic_tags( '{entry_id}' );
			}
		}

		// passback values
		if( !empty( $form['variables']['types'] ) ){
			foreach($form['variables']['types'] as $variable_index=>$behavior_type){
				if($behavior_type == 'passback'){
					$referrer['query'][$form['variables']['keys'][$variable_index]] = self::do_magic_tags( $form['variables']['values'][$variable_index] );
				}
			}
		}
		$referrer = $referrer['path'] . '?' . http_build_query($referrer['query']);

		// filter refer
		$referrer = apply_filters( 'caldera_forms_submit_redirect', $referrer, $form, $process_id);
		$referrer = apply_filters( 'caldera_forms_submit_redirect_complete', $referrer, $form, $process_id);

		// kill transient data
		delete_transient( $process_id );

		return self::form_redirect('complete', $referrer, $form, $process_id );
	}



	/**
	 * Makes Caldera Forms load the preview
	 */
	static public function cf_init_preview(){

		global $post, $form;

		if(!empty($_GET['cf_preview'])){
			$form = Caldera_Forms_Forms::get_form( $_GET['cf_preview'] );

			$userid = get_current_user_id();
			if( !empty( $userid ) ){

				if(empty($form['ID']) || $form['ID'] !== $_GET['cf_preview']){
					return;
				}
				if( empty($post) || $post->post_title !== 'Caldera Forms Preview' ){
					$temp_page = get_page_by_title('Caldera Forms Preview');
					if(empty($temp_page)){
						// create page
						$post = array(
							'post_content'   => '',
							'post_name'      => 'caldera_forms_preview',
							'post_title'     => 'Caldera Forms Preview',
							'post_status'    => 'draft',
							'post_type'      => 'page',
							'ping_status'    => 'closed',
							'comment_status' => 'closed'
						);
						$page_id = wp_insert_post( $post );
						wp_redirect( trailingslashit( get_home_url() ) . '?page_id='.$page_id.'&preview=true&cf_preview='.$_GET['cf_preview'] );
						exit;
					}
					if( $temp_page->post_status !== 'draft'){
						wp_update_post( array( 'ID' => $temp_page->ID, 'post_status' => 'draft' ) );
					}
					wp_redirect( trailingslashit( get_home_url() ) . '?page_id='.$temp_page->ID.'&preview=true&cf_preview='.$_GET['cf_preview'] );
					exit;
				}
				$post->post_title = $form['name'];
				$post->post_content = '[caldera_form id="'.$_GET['cf_preview'].'"]';

			}
		}

		// not a post-
		if(!isset($post->post_content)){
			return;
		}

		$page_forms = array();

		// check active widgets
		$sidebars     = get_option( 'sidebars_widgets' );
		if ( is_array( $sidebars ) && ! empty( $sidebars )  ) {
			$form_widgets = get_option( 'widget_caldera_forms_widget' );
			unset( $sidebars[ 'wp_inactive_widgets' ] );
			foreach ( $sidebars as $sidebar => $set ) {
				if ( is_active_sidebar( $sidebar ) ) {
					foreach ( $set as $setup ) {
						if ( false !== strpos( $setup, 'caldera_forms_widget-' ) ) {
							$widget_instance = str_replace( 'caldera_forms_widget-', '', $setup );
							if ( ! empty( $form_widgets[ $widget_instance ][ 'form' ] ) ) {
								$form_id                = $form_widgets[ $widget_instance ][ 'form' ];
								$page_forms[ $form_id ] = $form_id;
							}
						}
					}
				}
			}
		}

		$codes = get_shortcode_regex();
		preg_match_all('/' . $codes . '/s', $post->post_content, $found);

		if(!empty($found[0][0])){
			foreach($found[2] as $index=>$code){
				if( 'caldera_form' === $code || $code == 'caldera_form_modal' ){
					if(!empty($found[3][$index])){
						$atts = shortcode_parse_atts($found[3][$index]);
						if(isset($atts['id'])){
							$page_forms[ $atts['id'] ] = $atts['id'];
						}
					}
				}
			}
		}
		//none!
		if( empty( $page_forms ) ){
			return;
		}

		//theres forms, bring in the globals
		wp_enqueue_style( 'cf-field-styles' );

		Caldera_Forms_Render_Assets::optional_style_includes();

		foreach( $page_forms as $form_id ){
			// has form get  stuff for it
			$form = Caldera_Forms_Forms::get_form( $form_id );
			if(!empty($form)){
				// get list of used fields
				if(empty($form['fields'])){
					/// no filds - next form
				}

				// has a form - get field type
				if(!isset($field_types)){
					$field_types = self::get_field_types();
				}

				if(!empty($form['fields'])){
					foreach($form['fields'] as $field){
						//enqueue styles
						if( !empty( $field_types[$field['type']]['styles'])){
							foreach($field_types[$field['type']]['styles'] as $style){
								if(filter_var($style, FILTER_VALIDATE_URL)){
									wp_enqueue_style( 'cf-' . sanitize_key( basename( $style ) ), $style, array(), self::VERSION );
									wp_enqueue_style( 'cf-' . sanitize_key( basename( $style ) ), $style, array(), self::VERSION );
								}else{
									wp_enqueue_style( $style );
								}
							}
						}

						//enqueue scripts
						if( !empty( $field_types[$field['type']]['scripts'])){
							// check for jquery deps
							$depts[] = 'jquery';
							foreach($field_types[$field['type']]['scripts'] as $script){
								if(filter_var($script, FILTER_VALIDATE_URL)){
									wp_enqueue_script( 'cf-' . sanitize_key( basename( $script ) ), $script, $depts, self::VERSION );
								}else{
									wp_enqueue_script( $script );
								}
							}
						}
					}
				}

			}

		}


	}

	public function api_handler(){
		global $wp_query;
		// check for API
		// if this is not a request for json or a singular object then bail
		if ( ! isset( $wp_query->query_vars['cf_api'] ) ){
			return;
		}
		// check if form exists
		$form = Caldera_Forms_Forms::get_form( $wp_query->query_vars['cf_api'] );
		$atts = array(
			'id' => $wp_query->query_vars['cf_api'],
			'ajax' => true
		);
		if( !empty( $_REQUEST['cf_instance'] ) ){
			$atts['instance'] = $_REQUEST['cf_instance'];
		}
		// push 200 status. in some cases plugins or permalink config may cause a 404 before going out
		header("HTTP/1.1 200 OK", true );
		if(!empty($form['ID'])){
			if($form['ID'] === $wp_query->query_vars['cf_api']){
				// got it!
				// need entry?
				if(!empty($wp_query->query_vars['cf_entry'])){
					$atts['entry'] = (int) $wp_query->query_vars['cf_entry'];
					//$entry = Caldera_Forms::get_entry($wp_query->query_vars['cf_entry'], $form);
					//wp_send_json( $entry );
				}
				// is a post?
				if( $_SERVER['REQUEST_METHOD'] === 'POST' ){

					if( !empty( $_POST['control'] ) ){
						$transient_name = sanitize_key( $_POST['control'] );
						$transdata = get_transient( $transient_name );
						if( false === $transdata ){
							$transdata = array();
						}
						if( !empty( $_FILES ) && !empty( $_POST['field'] ) ){
							$form = Caldera_Forms_Forms::get_form( $wp_query->query_vars['cf_api'] );
							$field = $form[ 'fields'][ $_POST['field'] ];

							$data = cf_handle_file_upload( true, $field, $form );
							if( is_wp_error( $data ) ){
								wp_send_json_error( $data->get_error_message() );
							}

							$transdata[] = $data;
							//set
							set_transient( $transient_name, $transdata, DAY_IN_SECONDS );
							// maybe put in some checks on file then can say yea or nei
							wp_send_json_success( array(

							) );
						}
					}

					$_POST['_wp_http_referer_true'] = 'api';
					$_POST['_cf_frm_id'] 			=  $_POST['cfajax']	= $wp_query->query_vars['cf_api'];


					$submission = Caldera_Forms::process_submission();
					wp_send_json( $submission );
				}
			}
		}

		echo self::render_form( $atts );
		exit;
	}

	/**
	 * Retrieves the URL to the endpoint.
	 *
	 * Note: The returned URL is NOT escaped.
	 *
	 * @since 1.3.2
	 *
	 * @param string $form_id  form ID
	 * @return string Full URL
	 */
	static function get_submit_url( $form_id  = '' ) {

		if ( is_multisite() && get_blog_option( null, 'permalink_structure' ) || get_option( 'permalink_structure' ) ) {
			$url = get_home_url( );
			$url .= '/cf-api/' . ltrim( $form_id, '/' );
		} else {
			$url = trailingslashit( get_home_url( ) );
			$url = add_query_arg( 'cf_api', $form_id, $url );
		}
		if ( is_ssl() ) {
			if ( $_SERVER['SERVER_NAME'] === parse_url( get_home_url( ), PHP_URL_HOST ) ) {
				$url = set_url_scheme( $url, 'https' );
			}
		}
		/**
		 * Filter the Caldera Forms APU url
		 *
		 * @since 1.3.2
		 *
		 * @param string $url     URL.
		 * @param string $form_id  ID of form.
		 */
		return apply_filters( 'caldera_forms_submission_url', $url, $form_id );
		
	}

	/**
	 * Makes Caldera Forms go in front-end!
	 */
	static public function cf_init_system(){

		global $post, $wp_query, $process_id, $form;

		// setup script and style urls
		$style_urls = array(
			'modals' => CFCORE_URL . 'assets/css/remodal.min.css',
			'modals-theme' => CFCORE_URL . 'assets/css/remodal-default-theme.min.css',
			'grid' => CFCORE_URL . 'assets/css/caldera-grid.css',
			'form' => CFCORE_URL . 'assets/css/caldera-form.css',
			'alert' => CFCORE_URL . 'assets/css/caldera-alert.css',
			'field' => CFCORE_URL . 'assets/css/fields.min.css',
		);
		$script_urls = array(
			'dynamic'	=>	CFCORE_URL . 'assets/js/formobject.min.js',
			'modals'	=>	CFCORE_URL . 'assets/js/remodal.min.js',
			'baldrick'	=>	CFCORE_URL . 'assets/js/jquery.baldrick.min.js',
			'ajax'		=>	CFCORE_URL . 'assets/js/ajax-core.min.js',
			'field'	=>	CFCORE_URL . 'assets/js/fields.min.js',
			'conditionals' => CFCORE_URL . 'assets/js/conditionals.min.js',
			'validator-i18n' => null,
			'validator' => CFCORE_URL . 'assets/js/parsley.min.js',
			//'polyfiller' => CFCORE_URL . 'assets/js/polyfiller.min.js',
			'init'		=>	CFCORE_URL . 'assets/js/frontend-script-init.min.js',
		);

		$script_style_urls = array();

		// check to see language and include the language add on
		$locale = get_locale();
		if( $locale !== 'en_US' ){
			// not default lets go find if there is a translation available
			if( file_exists( CFCORE_PATH . 'assets/js/i18n/' . $locale . '.js' ) ){
				// nice- its there
				$locale_file = $locale;
			}elseif ( file_exists( CFCORE_PATH . 'assets/js/i18n/' . strtolower( $locale ) . '.js' ) ){
				$locale_file = strtolower( $locale );
			}elseif ( file_exists( CFCORE_PATH . 'assets/js/i18n/' . strtolower( str_replace('_', '-', $locale ) ) . '.js' ) ){
				$locale_file = strtolower( str_replace('_', '-', $locale ) );
			}elseif ( file_exists( CFCORE_PATH . 'assets/js/i18n/' . strtolower( substr( $locale,0, 2 ) ) . '.js' ) ){
				$locale_file = strtolower( substr( $locale,0, 2 ) );
			}elseif ( file_exists( CFCORE_PATH . 'assets/js/i18n/' . strtolower( substr( $locale,3 ) ) . '.js' ) ){
				$locale_file = strtolower( substr( $locale,3 ) );
			}
			if( !empty( $locale_file ) ){
				$script_urls['validator-i18n'] = CFCORE_URL . 'assets/js/i18n/' . $locale_file . '.js';
				$script_style_urls['config']['validator_lang'] = $locale_file;
			}

		}

		/**
		 * Filter script URLS for Caldera Forms on the frontend, before they are enqueued.
		 *
		 * @since 1.3.1
		 *
		 * @param array $script_urls array containing all urls to register
		 */
		$script_style_urls['script'] = apply_filters( 'caldera_forms_script_urls', $script_urls );

		/**
		 * Filter style URLS for Caldera Forms on the frontend, before they are enqueued.
		 *
		 * @since 1.3.1
		 *
		 * @param array $script_urls array containing all urls to register
		 */
		$script_style_urls['style'] = apply_filters( 'caldera_forms_style_urls', $style_urls );

		// register styles
		foreach( $script_style_urls['style'] as $style_key => $style_url ){
			if( empty( $style_url ) ){
				continue;
			}
			wp_register_style( 'cf-' . $style_key . '-styles', $style_url, array(), CFCORE_VER );
		}
		// register scripts
		foreach( $script_style_urls['script'] as $script_key => $script_url ){
			if( empty( $script_url ) ){
				continue;
			}
			wp_register_script( 'cf-' . $script_key, $script_url, array('jquery'), CFCORE_VER, true );
		}

		// localize for dynamic form generation
		wp_localize_script( 'cf-dynamic', 'cfModals', $script_style_urls );

		// catch a transient process
		if(!empty($_GET['cf_tp'])){

			// process a transient stored entry
			$data = get_transient( $_GET['cf_tp'] );
			if(!empty($data) && $data['transient'] === $_GET['cf_tp'] && isset($data['data'])){
				// create post values
				$_POST = array_merge( $_POST, $data['data']);
				// set transient id
				$_POST['_cf_frm_tr'] = $data['transient'];
			}
		}


		// hook into submission
		if(isset($_POST['_cf_verify']) && isset( $_POST['_cf_frm_id'] )){
			if(wp_verify_nonce( $_POST['_cf_verify'], 'caldera_forms_front' )){

				self::process_submission();
				exit;

			}
			exit;
			/// end form and redirect to submit page or result page.
		}

	}

	/**
	 * Recursive array search.
	 *
	 * @param string|array $needle Value to search for.
	 * @param array $haystack Array to search in
	 * @param array $found Optional. Array to add found items to. Default is an empty array.
	 *
	 * @return array Array of found needles.
	 */
	static function search_array_fields($needle, $haystack, $found = array()){

		if(is_array($needle)){
			foreach($needle as $pin){
				$found = array_merge($found, self::search_array_fields($pin, $haystack));
			}
		}else{
			if(in_array($needle, $haystack)){
				$found[] = $needle;
			}
		}
		return $found;
	}

	/**
	 * Load a saved entry.
	 *
	 * @param int $entry_id Entry ID
	 * @param string|array $form Optional. Config array, or ID of form.
	 *
	 * @return array
	 */
	static public function get_entry($entry_id, $form){
		if ( is_string( $form ) ) {
			$form_id = $form;
			$form    = Caldera_Forms_Forms::get_form( $form );
			if ( ! is_array( $form ) || ! isset( $form[ 'ID' ] ) || $form[ 'ID' ] !== $form_id ) {
				return new WP_Error( 'fail', esc_html__( 'Invalid form ID', 'caldera-forms' ) );
			}
		}

		if ( empty( $form ) ) {
			return;
		}


		// get fields
		$field_types = self::get_field_types();

		$entry = self::get_submission_data($form, $entry_id);
		$data = array(
			'data' => array()
		);

		foreach($entry as $field_id=>$field_value){

			if(!isset($form['fields'][$field_id]) || !isset($field_types[$form['fields'][$field_id]['type']])){
				continue;
			}

			if(isset($field_types[$form['fields'][$field_id]['type']]['setup']['not_supported'])){
				if(in_array('entry_list', $field_types[$form['fields'][$field_id]['type']]['setup']['not_supported'])){
					continue;
				}
			}
			//not_supported

			$field = $form['fields'][$field_id];

			/**
			 * Filter field config.
			 *
			 * @param array $field The field config.
			 * @param array $form The form config.
			 */
			$field = apply_filters( 'caldera_forms_render_get_field', $field, $form);

			/**
			 * Filter field config for fields of a given type.
			 *
			 * Filter name is dynamic, based on field type. For example "caldera_forms_render_get_field_type-hidden" or "caldera_forms_render_get_field_type-radio"
			 *
			 * @param array $field The field config.
			 * @param array $form The form config.
			 */
			$field = apply_filters( 'caldera_forms_render_get_field_type-' . $field['type'], $field, $form);

			/**
			 * Filter field config for fields with a given slug
			 *
			 * Filter name is dynamic, based on field type. For example "caldera_forms_render_get_field_slug-salsa" or "caldera_forms_render_get_field_slug-chips"
			 *
			 * @param array $field The field config.
			 * @param array $form The form config.
			 */
			$field = apply_filters( 'caldera_forms_render_get_field_slug-' . $field['slug'], $field, $form);

			if( is_string( $field_value ) ){
				// maybe json?
				$is_json = json_decode( $field_value, ARRAY_A );
				if( !empty( $is_json ) && is_array( $is_json ) ){
					$field_value = $is_json;
				}else{
					$field_value = esc_html( stripslashes_deep( $field_value ) );
				}
			}
			// set view
			$field_view = apply_filters( 'caldera_forms_view_field_' . $field['type'], $field_value, $field, $form);

			// has options?
			if( ! empty( $field['config']['option'] ) ){
				$i = 0;
				foreach( $field['config']['option'] as $opt => $option ){
					if( $option['value'] == $field_view ){
						$field_view = $option['label'];

						if( is_array( $field_value ) ){
							if ( isset( $field_value[ $opt ]) ) {
								$field_value = $field_value[ $opt ];
							}  else {
								$field_value = '';
							}

						}

						if( empty( $field_types[ $field['type'] ]['options'] ) ){
							$data['data'][$field_id . '_' . $i ] = array(
								'label' => $field['label'],
								'view'	=> $field_view,
								'value' => $field_value
							);
							$i ++;
						}
					}
				}

			}

			$data['data'][$field_id] = array(
				'label' => $field['label'],
				'view'	=> $field_view,
				'value' => $field_value
			);



		}

		// get meta
		$entry_detail = self::get_entry_detail($entry_id, $form);
		$data['date'] = self::localize_time( $entry_detail['datestamp'] );

		if(!empty($entry_detail['meta'])){
			$data['meta'] = $entry_detail['meta'];
		}


		if(!empty($entry_detail['user_id'])){
			$user = get_userdata( $entry_detail['user_id'] );
			if(!empty($user)){
				$data['user'] = array(
					'ID' 		=> $user->ID,
					'name' 		=> $user->data->display_name,
					'email' 	=> $user->data->user_email,
					'avatar' 	=> get_avatar( $user->ID, 150, 'identicon'),
				);
			}
		}else{
			$avatar_field = null;
			if(!empty($form['avatar_field'])){
				$avatar_field = self::get_field_data($form['avatar_field'], $form, $entry_id);
			}
			$data['user'] = array(
				'avatar' 	=> get_avatar( $avatar_field, 150),
			);
		}

		if( !empty( $form['variables']['types'] ) ){
			foreach( $form['variables']['types'] as $var_key=>$var_type ){
				if( $var_type == 'entryitem' ){
					$var_val = Caldera_Forms::do_magic_tags( $form['variables']['values'][$var_key], $entry_id );
					$data['data'][ '_var_' . $form['variables']['keys'][$var_key] ] = array(
						'label' => ucwords( str_replace('_', ' ', $form['variables']['keys'][$var_key] ) ),
						'view'	=> $var_val,
						'value' => sanitize_text_field( $var_val )
					);
				}
			}
		}

		// allow plugins to alter the profile.
		$data['user'] = apply_filters( 'caldera_forms_get_entry_user', $data['user'], $entry_id, $form);

		// set the entry status
		$data['status'] = $entry_detail['status'];

		// allow plugins to alter the entry
		$data = apply_filters( 'caldera_forms_get_entry', $data, $entry_id, $form);

		
		return $data;
	}

	/**
	 * Load a Caldera Form in a modal.
	 *
	 * @since unknown
	 *
	 * @deprecated 1.3.1
	 *
	 * @param string|array $atts Shortcode atts or form ID
	 * @param string $content Content to use in trigger link.
	 *
	 * @return string
	 */
	static public function render_modal_form($atts, $content){

		if(empty($atts['id'])){
			return $content;
		}
		$form = Caldera_Forms_Forms::get_form( $atts['id'] );
		if(empty($form['ID']) || $form['ID'] != $atts['id']){
			return $content;
		}

		$form_atts = array('id'=>$form['ID'],'ajax'=>true);
		if( !empty( $atts['entry'] ) ){
			$form_atts['entry'] = $atts['entry'];
		}
		$modal_button_classes = array(
			"cf_modal_button"
		);

		if(!empty($atts['classes'])){
			$modal_button_classes = array_merge($modal_button_classes, explode(',', $atts['classes']));
		}

		$modal_id = uniqid($form['ID']);

		$out = "<a class=\" " . implode(' ', $modal_button_classes) . "\" href=\"#" . $modal_id . "\">" . $content . "</a>\r\n";

		$current_state = 'style="display:none;"';
		if(!empty($_GET['cf_er'])){
			$transdata = get_transient( $_GET['cf_er'] );
			if($transdata['transient'] == $_GET['cf_er']){
				$current_state = 'style="display:block;"';
			}
		}
		if(!empty($_GET['cf_su'])){
			// disable notices
			unset($_GET['cf_su']);
		}

		$width = '';
		if(!empty($atts['width'])){
			$width = ' width: ' . floatval( $atts['width'] ).'px; margin-left: -' . ( floatval( $atts['width'] ) / 2 ) . 'px;';
		}

		ob_start();
		?>
		<div id="<?php echo esc_attr( $modal_id ); ?>" class="caldera-front-modal-container" <?php echo $current_state; ?> data-form-id="<?php echo esc_attr( $modal_id ); ?>">
			<div class="caldera-backdrop"></div>
			<div id="<?php echo $modal_id; ?>_modal_wrap" tabindex="-1" arialabelled-by="<?php echo $modal_id; ?>_modal_label" class="caldera-modal-wrap caldera-front-modal-wrap" style="display: block; <?php echo $width; ?>">
				<div class="caldera-modal-title" id="<?php echo $modal_id; ?>_modal_title" style="display: block;">
					<a href="#close" class="caldera-modal-closer caldera-front-modal-closer" data-dismiss="modal" aria-hidden="true" id="<?php echo $modal_id; ?>_modal_closer">&times;</a>
					<h3 class="modal-label" id="<?php echo $modal_id; ?>_modal_label"><?php echo $form['name']; ?></h3>
				</div>
				<div class="caldera-modal-body caldera-front-modal-body" id="<?php echo $modal_id; ?>_modal_body">
					<?php echo self::render_form( $form_atts ); ?>
				</div>
			</div>
		</div>
		<?php
		self::$footer_modals .= ob_get_clean();
		return $out;
	}

	/**
	 * Print modal content in footer.
	 *
	 * @since unknown
	 *
	 * @uses "wp_footer"
	 */
	static public function render_footer_modals(){
		$footer_modals = self::$footer_modals;
		if ( ! empty( $footer_modals ) && is_string( $footer_modals ) ) {
			echo $footer_modals;
		}

	}

	/**
	 * Create HTML markup for a field.
	 *
	 * @since unknown
	 *
	 * @param array|string $field Form ID or shortcode atts or form config array
	 * @param array|null $form Optional Form to load field from. Not necessary, but helps the filters out.
	 * @param array $entry_data Optional. Entry data to populate field with. Null, the default, loads form for creating a new entry.
	 *
	 * @return void|string HTML for form, if it was able to be loaded,
	 */
	static public function render_field($field, $form = null, $entry_data = array(), $field_errors = array() ){
		global $current_form_count, $grid;

		$field_classes = array(
			"control_wrapper"	=> array("form-group"),
			"field_label"		=> array("control-label"),
			"field_required_tag"=> array("field_required"),
			"field_wrapper"		=> array(),
			"field"				=> array("form-control"),
			"field_caption"		=> array("help-block"),
			"field_error"		=> array("has-error"),
		);

		$field_classes = apply_filters( 'caldera_forms_render_field_classes', $field_classes, $field, $form);
		$field_classes = apply_filters( 'caldera_forms_render_field_classes_type-' . $field['type'], $field_classes, $field, $form);
		$field_classes = apply_filters( 'caldera_forms_render_field_classes_slug-' . $field['slug'], $field_classes, $field, $form);



		$field_wrapper_class = implode(' ',$field_classes['control_wrapper']);
		$field_input_class = implode(' ',$field_classes['field_wrapper']);
		$field_class = implode(' ',$field_classes['field']);

		// add error class
		if ( ! empty( $field_errors ) ) {
			$field_wrapper_class .= " " . implode( ' ', $field_classes[ 'field_error' ] );
		}

		$field_structure = array(
			"field"			=>	$field,
			"id"				=>	$field['ID'],//'fld_' . $field['slug'],
			"name"				=>	$field['ID'],//$field['slug'],
			"wrapper_before"	=>	"<div role=\"field\" data-field-wrapper=\"" . $field['ID'] . "\" class=\"" . $field_wrapper_class . "\">\r\n",
			"field_before"		=>	"<div class=\"" . $field_input_class ."\">\r\n",
			"label_before"		=>	( empty($field['hide_label']) ? "<label id=\"" . $field['ID'] ."Label\" for=\"" . $field['ID'].'_'.$current_form_count . "\" class=\"" . implode(' ', $field_classes['field_label'] ) . "\">" : null ),
			"label"				=>	( empty($field['hide_label']) ? $field['label'] : null ),
			"label_required"	=>	( empty($field['hide_label']) ? ( !empty($field['required']) ? " <span aria-hidden=\"true\" role=\"presentation\" class=\"" . implode(' ', $field_classes['field_required_tag'] ) . "\" style=\"color:#ff2222;\">*</span>" : "" ) : null ),
			"label_after"		=>	( empty($field['hide_label']) ? "</label>" : null ),
			"field_placeholder" =>	( !empty($field['hide_label']) ? 'placeholder="' . htmlentities( $field['label'] ) .'"' : null),
			"field_required"	=>	( !empty($field['required']) ? 'required="required"' : null),
			"field_value"		=>	null,
			"field_caption"		=>	( !empty($field['caption']) ? "<span id=\"" . $field['ID'] ."Caption\" class=\"" . implode(' ', $field_classes['field_caption'] ) . "\">" . $field['caption'] . "</span>\r\n" : ""),
			"field_after"		=>  "</div>\r\n",
			"wrapper_after"		=>  "</div>\r\n",
			"aria"				=> 	array()
		);
		// if has label
		if( empty( $field['hide_label'] ) ){
			// visible label, set labelled by
			$field_structure['aria']['labelledby'] = $field['ID'] . 'Label';
		}else{
			// hidden label, aria label instead
			$field_structure['aria']['label'] = $field['label'];
		}
		// if has caption
		if( !empty( $field['caption'] ) ){
			$field_structure['aria']['describedby'] = $field['ID'] . 'Caption';
		}
 
		// add error
		if ( ! empty( $field_errors ) ) {
			if( is_string( $field_errors ) ){
				$field_errors = array( $field_errors );
			}

			foreach( $field_errors as $error ){
				$field_structure[ 'field_caption' ] = "<span class=\"" . implode( ' ', $field_classes[ 'field_caption' ] ) . "\">" . $error . "</span>\r\n";
			}

		}

		// value
		if(isset($field['config']['default'])){

			$field_structure['field_value'] = self::do_magic_tags($field['config']['default']);
		}

		// transient data
		if(isset($entry_data[$field['ID']])){
			$field_structure['field_value'] = $entry_data[$field['ID']];
		}

		$field_structure = apply_filters( 'caldera_forms_render_field_structure', $field_structure, $form);
		$field_structure = apply_filters( 'caldera_forms_render_field_structure_type-' . $field['type'], $field_structure, $form);
		$field_structure = apply_filters( 'caldera_forms_render_field_structure_slug-' . $field['slug'], $field_structure, $form);

		// compile aria tags
		if( !empty( $field_structure['aria'] ) ){
			$aria_atts = null;
			foreach ($field_structure['aria'] as $att => $att_val) {
				$aria_atts .= ' aria-' . $att . '="' . esc_attr( $att_val ) . '"';
			}
			$field_structure['aria'] = $aria_atts;
		}

		$field_name = $field_structure['name'];
		$field_id = $field_structure['id'] . '_' .$current_form_count;
		$wrapper_before = $field_structure['wrapper_before'];
		$field_before = $field_structure['field_before'];
		$field_label = $field_structure['label_before'] . $field_structure['label'] . $field_structure['label_required'] . $field_structure['label_after']."\r\n";
		$field_placeholder = $field_structure['field_placeholder'];
		$field_required = $field_structure['field_required'];
		$field_caption = $field_structure['field_caption'];
		$field_after = $field_structure['field_after'];
		$wrapper_after = $field_structure['wrapper_after'];
		// blank default
		$field_value = $field_structure['field_value'];
		// setup base instance ID
		$field_base_id = $field['ID'];

		// register strings
		$form_field_strings[ $field_structure['id'] ] = array( 'id' => $field_structure['id'], 'instance' => $current_form_count, 'slug' => $field['slug'], 'label' => $field['label'] );

		$field_types = self::get_field_types();

		$field_file = $field_types[$field['type']]['file'];
		/**
		 * Filter the file path to be used for a field's HTML in front-end
		 *
		 * @since 1.3.4
		 *
		 * @param string $field_file The path to the file
		 * @param string $field_type The field type
		 * @param string $field The field ID.
		 * @param string $field_structure Data to be used in field
		 * @param array $form Current form (NOTE: May be null)
		 */
		$field_file = apply_filters( 'caldera_forms_render_field_file', $field_file, $field[ 'type' ], $field[ 'ID' ], $field_file, $field_structure, $form );

		ob_start();
		include $field_file;
		$field_html = apply_filters( 'caldera_forms_render_field', ob_get_clean(), $form);
		$field_html = apply_filters( 'caldera_forms_render_field_type-' . $field['type'], $field_html, $form);
		$field_html = apply_filters( 'caldera_forms_render_field_slug-' . $field['slug'], $field_html, $form);

		return $field_html;
	}
	/**
	 * Create HTML markup for a form.
	 * @param array|string $atts Form ID or shortcode atts or form config array
	 * @param null|int $entry_id Optional. Entry ID to load data from. Null, the default, loads form for creating a new entry.
	 * @param null $shortcode No longer used.
	 *
	 * @return void|string HTML for form, if it was able to be laoded
	 */
	static public function render_form($atts, $entry_id = null, $shortcode = null){

		global $current_form_count, $form, $post;

		if(empty($atts)){
			return;
		}

		if(is_string($atts)){

			$form = Caldera_Forms_Forms::get_form( $atts );
			$atts = array();

		}elseif( is_array( $atts ) && isset( $atts[ 'ID' ] ) ){
			$form = Caldera_Forms_Forms::get_form( $atts[ 'ID' ] );
		}else{

			if(empty($atts['id'])){
				if(!empty($atts['name'])){
					$form = Caldera_Forms_Forms::get_form( $atts['name'] );
				}
			}elseif( !empty( $atts['id'] ) ){
				$form = Caldera_Forms_Forms::get_form( $atts['id'] );
			}
		}

		if( empty( $form ) ){
			return;
		}

		// is this form allowed to render ( check state )
		if( isset( $form['form_draft'] ) ){
			if( !isset( $_GET['cf_preview'] ) || $_GET['cf_preview'] != $form['ID'] ){
				if( isset( $_POST['action'] ) && $_POST['action'] == 'cf_get_form_preview' ){
					echo '<p style="color: #cf0000;">' . __( 'Form is currently not active.', 'caldera-forms' ) . '</p>';
				}else{
					return;
				}

			}else{
				echo '<div class="caldera-grid"><p class="alert alert-error alert-danger">' . __( 'Form is currently not active.', 'caldera-forms' ) . '</p></div>';
			}
		}

		if(isset($atts['ajax'])){
			if(!empty($atts['ajax'])){
				$form['form_ajax'] = 1;
			}else{
				$form['form_ajax'] = 0;
			}
		}
		// set entry edit
		if(!empty($atts['entry'])){
			$entry_id = self::do_magic_tags( $atts['entry'] );
		}

		/**
		 * Filter form settings, before rendering form.
		 *
		 * @since unknown
		 *
		 * @param int $entry_id The entry ID.
		 * @param array $form Form config.
		 */
		$form = apply_filters( 'caldera_forms_render_get_form', $form );

		/**
		 * Set entry ID when loading form
		 *
		 * @since 1.2.3
		 *
		 * @param int $entry_id The entry ID.
		 * @param array $form Form config.
		 */
		$entry_id = apply_filters( 'caldera_forms_render_entry_id', $entry_id, $form );

		if(empty($form)){
			return;
		}

		/**
		 * Runs after form is loaded and before rendering starts
		 *
		 * NOTE: An excellent way to conditionally abort form loading.
		 *
		 * @since 1.3.4
		 *
		 * @param null|string $html By defualt, null. If string is returned, method will immediately return that string.
		 * @param int $entry_id The entry ID.
		 * @param array $form Form config.
		 */
		$html = apply_filters( 'caldera_forms_pre_render_form', null, $entry_id, $form );
		if( is_string( $html ) ){
			return $html;

		}

		if(empty($current_form_count)){
			$current_form_count = 0;
		}
		$current_form_count += 1;

		// set instance
		if( !empty( $atts['instance'] ) ){
			$current_form_count = absint( $atts['instance'] );
		}

		$field_types = self::get_field_types();

		do_action('caldera_forms_render_start', $form);

		Caldera_Forms_Render_Assets::optional_style_includes();

		// fallback for function based rendering in case it missed detection
		wp_enqueue_style( 'cf-field-styles' );



		include_once CFCORE_PATH . "classes/caldera-grid.php";

		$gridsize = 'sm';
		if(!empty($form['settings']['responsive']['break_point'])){
			$gridsize = $form['settings']['responsive']['break_point'];
		}

		/**
		 * What size grid to use for the grid
		 *
		 * @param string $size Size for grid. Default is "sm"
		 */
		$gridsize = apply_filters( 'caldera_forms_render_set_grid_size', $gridsize );

		// set grid render engine
		$grid_settings = array(
			"first"				=> 'first_row',
			"last"				=> 'last_row',
			"single"			=> 'single',
			"before"			=> '<div %1$s class="row %2$s">',
			"after"				=> '</div>',
			"column_first"		=> 'first_col',
			"column_last"		=> 'last_col',
			"column_single"		=> 'single',
			"column_before"		=> '<div %1$s class="col-'.$gridsize.'-%2$d %3$s">',
			"column_after"		=> '</div>',
		);

		// filter settings
		$grid_settings = apply_filters( 'caldera_forms_render_grid_settings', $grid_settings, $form);

		$form['grid_object'] = new Caldera_Form_Grid($grid_settings);

		// Build Pages Breaks
		if( false !== strpos($form['layout_grid']['structure'], '#')){
			// setup pages
			$pages = explode('#', $form['layout_grid']['structure']);
			$page_breaks = array();
			foreach($pages as $page_no=>$page){
				$point = substr_count($page, '|') + 1;
				if(isset($page_breaks[$page_no-1])){
					$point += $page_breaks[$page_no-1];
				}
				$page_breaks[$page_no] = $point;
			}
			$form['layout_grid']['structure'] = str_replace('#', '|', $form['layout_grid']['structure']);
		}

		// setup notcies
		$notices = array();
		$note_general_classes = array(
			'alert'
		);
		$note_general_classes = apply_filters( 'caldera_forms_render_note_general_classes', $note_general_classes, $form);

		$note_classes = array(
			'success'	=> array_merge($note_general_classes, array(
				'alert-success'
			)),
			'error'	=> array_merge($note_general_classes, array(
				'alert-error'
			)),
			'info'	=> array_merge($note_general_classes, array(
				'alert-info'
			)),
			'warning'	=> array_merge($note_general_classes, array(
				'alert-warning'
			)),
			'danger'	=> array_merge($note_general_classes, array(
				'alert-danger'
			)),
		);

		$note_classes = apply_filters( 'caldera_forms_render_note_classes', $note_classes, $form);

		$field_errors = array();

		// edit entry from url
		if(!empty($_GET['cf_ee'])){
			$entry_id = $_GET['cf_ee'];
		}

		// attr entry id
		if(!empty($atts['entry'])){
			$entry_id = $atts['entry'];
		}

		if(!empty($entry_id)){
			//check user can edit this item.
			$user_id = get_current_user_id();
			$details = self::get_entry_detail($entry_id, $form);
			if( !empty( $_GET['cf_et'] ) ){
				// build token
				$token_array = array(
					'id'		=>	(int) $entry_id,
					'datestamp'	=>	$details['datestamp'],
					'user_id'	=>	(int) $details['user_id'],
					'form_id'	=>	$form['ID']
				);
				if( sha1( json_encode( $token_array ) ) !== trim( $_GET['cf_et'] ) ){
					$notices['error']['note'] = __( 'Permission denied or entry does not exist.', 'caldera-forms' );
				}else{
					$entry_id = (int) $details['id'];
					$edit_token = sha1( json_encode( $token_array ) );
				}

			}else{

				if(!empty($user_id)){

					if(!empty($details)){
						// check user can edit
						if( current_user_can( 'edit_posts' ) || $details['user_id'] === $user_id ){
							// can edit.
							$entry_id = (int) $details['id'];
						}else{
							$notices['error']['note'] = __( 'Permission denied or entry does not exist.', 'caldera-forms' );
						}
					}else{
						$notices['error']['note'] = __( 'Permission denied or entry does not exist.', 'caldera-forms' );
					}

				}else{
					$notices['error']['note'] = __( 'Permission denied or entry does not exist.', 'caldera-forms' );
				}
			}

			if(!empty($notices['error']['note'])){
				$halt_render = true;
				$entry_id = false;
			}
		}

		// check for prev post
		$prev_data = apply_filters( 'caldera_forms_render_pre_get_entry', array(), $form, $entry_id);

		// load requested data
		if(!empty($entry_id)){
			$prev_entry = self::get_entry($entry_id, $form);
			$prev_data = array();
			self::set_field_data('_entry_id', $entry_id, $form);
			foreach($prev_entry['data'] as $field_id=>$entry_data){
				$prev_data[$field_id] = $entry_data['value'];
			}
			$prev_data = apply_filters( 'caldera_forms_render_get_entry', $prev_data, $form, $entry_id);
		}

		if(!empty($_GET['cf_er'])){
			$prev_post = get_transient( $_GET['cf_er'] );
			if(!empty($prev_post['transient'])){

				if($prev_post['transient'] === $_GET['cf_er']){
					foreach($prev_post['data'] as $field_id=>$field_entry){

						if(!isset($form['fields'][$field_id])){
							continue; // ignore non field data
						}

						if(!is_wp_error( $field_entry )){
							$prev_data[$field_id] = $field_entry;
						}
					}
				}
				if(!empty($prev_post['type']) && !empty($prev_post['note'])){
					$notices[$prev_post['type']]['note'] = $prev_post['note'];
				}
				if(!empty($prev_post['error']) && !empty($prev_post['note'])){
					$notices['error']['note'] = $prev_post['note'];
				}
				if(!empty($prev_post['fields'])){
					$field_errors = array();
					foreach($prev_post['fields'] as $field_id=>$field_error){

						if(is_wp_error( $field_error )){
							$field_errors[$field_id] = $field_error->get_error_message();
						}else{
							$field_errors[$field_id] = $field_error;
						}
					}
				}
			}
			// filter transient
			$prev_post = apply_filters( 'caldera_forms_render_get_transient', $prev_post, $form);

		}
		if(!empty($_GET['cf_su']) && $current_form_count == $_GET['cf_su']){
			if(empty($notices['success']['note'])){
				$notices['success']['note'] = $form['success'];
			}
		}

		// build grid & pages
		$form['grid_object']->setLayout($form['layout_grid']['structure']);

		// insert page breaks
		if(!empty($page_breaks)){
			$currentpage = 1;
			if(isset($_GET['cf_pg']) && !isset($prev_post['page'])){
				$currentpage = (int) $_GET['cf_pg'];
			}elseif(isset($prev_post['page'])){
				$currentpage = (int) $prev_post['page'];
			}
			$display = 'none';
			$hidden = 'true';
			if( $currentpage === 1){
				$display = 'block';
				$hidden = 'false';
			}

			$total_rows = substr_count($form['layout_grid']['structure'], '|') + 1;
			$form['grid_object']->before('<div id="form_page_' . $current_form_count . '_pg_1" data-formpage="1" class="caldera-form-page" style="display:'.$display.';" role="region" aria-labelledby="breadcrumb_' . $current_form_count . '_pg_1" aria-hidden="' . $hidden . '">', 1);
			$form['grid_object']->after('</div>', $total_rows);
			//dump($page_breaks);
			foreach($page_breaks as $page=>$break){

				$form['grid_object']->after('</div>', $break);

				if($break+1 <= $total_rows ){
					$display = 'none';
					$hidden = 'true';
					if($page+2 == $currentpage){
						$display = 'block';
						$hidden = 'false';
					}

					$form['grid_object']->before('<div id="form_page_' . $current_form_count . '_pg_' . ($page+2) . '" data-formpage="' . ($page+2) . '" role="region" aria-labelledby="breadcrumb_' . $current_form_count . '_pg_' . ( $page + 2 ) . '" aria-hidden="' . $hidden . '" class="caldera-form-page" style="display:'.$display.';">', $break+1);
				}
			}
			//dump($page_breaks,0);
			//dump( $grid );
		}


		// setup processor bound requieds
		if(!empty($form['processors'])){
			$bound_fields = array();
			foreach($form['processors'] as $processor_id=>$processor){
				if(!empty($processor['config']['_required_bounds'])){
					foreach($processor['config'] as $slug=>&$value){
						if($slug == '_required_bounds'){
							continue;
						}
						if(in_array($slug, $processor['config']['_required_bounds'])){
							$bound_fields = array_merge($bound_fields, self::search_array_fields($value, array_keys( $form['fields'])) );
						}
					}
				}
			}
			foreach($bound_fields as $bound){
				$form['fields'][$bound]['required'] = 1;
			}
		}

		$conditions_templates = array();
		$conditions_configs = array();
		$used_slugs = array();
		$form_field_strings = array();

		if(!empty($form['fields'])){
			// prepare fields
			foreach($form['fields'] as $field_id=>$field){
				$field = apply_filters( 'caldera_forms_render_get_field', $field, $form);
				$field = apply_filters( 'caldera_forms_render_get_field_type-' . $field['type'], $field, $form);
				$field = apply_filters( 'caldera_forms_render_get_field_slug-' . $field['slug'], $field, $form);
				$form['fields'][$field_id] = $field;
			}
		}


		if(!empty($form['layout_grid']['fields'])){

			foreach($form['layout_grid']['fields'] as $field_base_id=>$location){

				if(isset($form['fields'][$field_base_id]) ) {
					$field = self::load_field( $form, $field_base_id );


					if(empty($field) || !isset($field_types[$field['type']]['file']) || !file_exists($field_types[$field['type']]['file'])){
						continue;
					}

					$field['grid_location'] = $location;


					Caldera_Forms_Render_Assets::enqueue_field_scripts( $field_types, $field );


					$field_base_id = $field['ID'] . '_' . $current_form_count;

					$field_error = array();
					if( isset( $field_errors[ $field[ 'ID' ] ] ) ){
						$field_error = $field_errors[ $field[ 'ID' ] ];
					}

					$field_html = self::render_field( $field, $form, $prev_data, $field_error );
					// conditional wrapper
					if(!empty($field['conditions']['group']) && !empty($field['conditions']['type']) ){

						$conditions_configs[$field_base_id] = $field['conditions'];

						if( $field['conditions']['type'] !== 'disable' ){
							// wrap it up
							$conditions_templates[$field_base_id] = "<script type=\"text/html\" id=\"conditional-" . $field_base_id . "-tmpl\">\r\n" . $field_html . "</script>\r\n";
							// add in instance number
							if(!empty($field['conditions']['group'])){
								foreach($field['conditions']['group'] as &$group_row){
									foreach( $group_row as &$group_line){
										// add instance value
										$group_line['instance'] = $current_form_count;
									}
								}
							}
						}

						if($field['conditions']['type'] == 'show' || $field['conditions']['type'] == 'wdisable'){
							// show if indicates hidden by default until condition is matched.
							$field_html = null;
						}
						// wrapp it up
						$field_html = '<span class="caldera-forms-conditional-field" role="region" aria-live="polite" id="conditional_' . $field_base_id . '">' . $field_html . '</span>';
					}

					$form['grid_object']->append($field_html, $field['grid_location']);

				}
			}

		}

		// form object strings
		wp_localize_script( 'cf-dynamic', $form['ID'] . '_' . $current_form_count, $form_field_strings );

		// do grid
		$form['grid_object'] = apply_filters( 'caldera_forms_render_grid_structure', $form['grid_object'], $form);
		// wrapper classes
		$form_wrapper_classes = array(
			"caldera-grid"
		);

		/**
		 * Change classes of elements wrapping the grid
		 *
		 * @since unknown
		 *
		 * @param array $form_wrapper_classes Array of classes
		 * @param array $config Form config
		 */
		$form_wrapper_classes = apply_filters( 'caldera_forms_render_form_wrapper_classes', $form_wrapper_classes, $form);

		$form_wrap_id = "caldera_form_" . $current_form_count;
		$out = sprintf( '<div class="%s" id="%s" data-cf-ver="%s" data-cf-form-id="%s">', esc_attr( implode(' ', $form_wrapper_classes) ), esc_attr( $form_wrap_id ), esc_attr( CFCORE_VER ), esc_attr( $form['ID'] ) );

		/**
		 * Filter final HTML for notices
		 *
		 * @since unknown
		 *
		 * @param string $notice Notices HTML
		 * @param array $config Form config
		 */
		$notices = apply_filters( 'caldera_forms_render_notices', $notices, $form);

		// set debug notice
		if( !empty( $form['mailer']['enable_mailer'] ) && !empty( $form['debug_mailer'] ) ){
			$notices['error'] = array( 'note' => __( 'WARNING: Form is in Mailer Debug mode. Disable before going live.', 'caldera-forms' ) );
		}

		$out .= '<div id="caldera_notices_'.$current_form_count.'" data-spinner="'. admin_url( 'images/spinner.gif' ).'">';
		if(!empty($notices)){
			// do notices
			// entry id
			if( isset( $_GET['cf_id'] ) ){
				$notice_entry_id = (int) $_GET['cf_id'];
			}elseif( !empty( $entry_id ) ){
				$notice_entry_id = $entry_id;
			}else{
				$notice_entry_id = null;
			}

			foreach($notices as $note_type => $notice){
				if(!empty($notice['note'])){
					$out .= '<div class=" '. implode(' ', $note_classes[$note_type]) . '">' . self::do_magic_tags( $notice['note'], $notice_entry_id ) .'</div>';
				}
			}

		}
		$out .= '</div>';
		if((empty($notices['success']) || empty($form['hide_form'])) && empty($halt_render)){

			$form_element = 'form';

			$form_classes = array(
				$form['ID'],
				'caldera_forms_form',
			);

			$form_attributes = array(
				'method'	=>	'POST',
				'enctype'	=>	'multipart/form-data',
				'role'		=>	'form',
				'id'		=>	$form['ID'] . '_' . $current_form_count
			);

			/**
			 * Change what type of element form is in.
			 *
			 * Note: Using anything besides "form" here will make form cease to function as a form.
			 *
			 * @since unknown
			 *
			 * @param string $form_element Form element type.
			 * @param array $config Form config
			 */
			$form_element = apply_filters( 'caldera_forms_render_form_element', $form_element, $form);

			/**
			 * Modify classes applied to form element
			 *
			 * @since unknown
			 *
			 * @param array $form_classes Array of classes
			 * @param array $config Form config
			 */
			$form_classes = apply_filters( 'caldera_forms_render_form_classes', $form_classes, $form);

			/**
			 * Modify HTML attributes applied to form element
			 *
			 * @since unknown
			 *
			 * @param array $form_attributes Array of HTML attributes
			 * @param array $config Form config
			 */
			$form_attributes = apply_filters( 'caldera_forms_render_form_attributes', $form_attributes, $form);

			$attributes = array();
			foreach($form_attributes as $attribute=>$value){
				$attributes[] = $attribute . '="' . htmlentities( $value ) . '"';
			}

			// render only non success
			$out .= "<" . $form_element . " data-instance=\"" . $current_form_count . "\" class=\"" . implode(' ', $form_classes) . "\" " . implode(" ", $attributes) . ">\r\n";
			$out .= wp_nonce_field( "caldera_forms_front", "_cf_verify", true, false);
			$out .= "<input type=\"hidden\" name=\"_cf_frm_id\" value=\"" . $form['ID'] . "\">\r\n";
			$out .= "<input type=\"hidden\" name=\"_cf_frm_ct\" value=\"" . $current_form_count . "\">\r\n";
			if( !empty( $form['form_ajax'] ) ){
				$out .= "<input type=\"hidden\" name=\"cfajax\" value=\"" . $form['ID'] . "\">\r\n";
			}
			if( is_object($post) ){
				$out .= "<input type=\"hidden\" name=\"_cf_cr_pst\" value=\"" . $post->ID . "\">\r\n";
			}

			// user transient for continuation
			if(!empty($prev_post['transient'])){
				$out .= "<input type=\"hidden\" name=\"_cf_frm_tr\" value=\"" . $prev_post['transient'] . "\">\r\n";
			}
			// is edit?
			if(!empty($entry_id)){
				$out .= "<input type=\"hidden\" name=\"_cf_frm_edt\" value=\"" . $entry_id . "\">\r\n";
			}

			// is edit via token?
			if(!empty($edit_token)){
				$out .= "<input type=\"hidden\" name=\"_cf_frm_edt_tkn\" value=\"" . $edit_token . "\">\r\n";
			}


			// auto pagination
			if(!empty($form['auto_progress']) && count($form['page_names']) > 1){

				// retain query string
				$qurystr = array();
				parse_str( $_SERVER['QUERY_STRING'], $qurystr );
				$out .= "<span class=\"caldera-grid\"><ol class=\"breadcrumb\" data-form=\"caldera_form_" . $current_form_count ."\">\r\n";
				$current_page = 1;
				if(!empty($_GET['cf_pg'])){
					$current_page = $_GET['cf_pg'];
				}
				foreach($form['page_names'] as $page_key=>$page_name){
					$tabclass = null;
					$expanded = 'false';
					if($current_page == $page_key + 1){
						$tabclass = ' class="active"';
						$expanded = 'true';
					}

					$qurystr['cf_pg'] = $page_key + 1;
					$out .= "<li" . $tabclass . "><a aria-controls=\"form_page_" . $current_form_count ."_pg_" . ( $page_key + 1 ) . "\" aria-expanded=\"" . $expanded . "\" id=\"breadcrumb_" . $current_form_count ."_pg_" . ( $page_key + 1 ) . "\" href=\"?". http_build_query($qurystr) . "\" data-page=\"" . ( $page_key + 1 ) ."\" data-pagenav=\"caldera_form_" . $current_form_count ."\" title=\"" . sprintf( __( 'Navigate to %s', 'caldera-forms' ), $page_name ) ."\">". $page_name . "</a></li>\r\n";
				}
				$out .= "</ol></span>\r\n";
			}

			// sticky sticky honey
			if(isset($form['check_honey'])){
				$out .= "<div class=\"hide\" style=\"display:none; overflow:hidden;height:0;width:0;\">\r\n";

				/**
				 * Change which words are used to form honey pot
				 *
				 * @since unknown
				 *
				 * @param array $words An array of words.
				 */
				$honey_words = apply_filters( 'caldera_forms_get_honey_words', array('web_site', 'url', 'email', 'company', 'name'));
				$word = $honey_words[rand(0, count($honey_words) - 1 )];
				$out .= "<label>". ucwords( str_replace('_', ' ', $word) ) ."</label><input type=\"text\" name=\"".$word."\" value=\"\" autocomplete=\"off\">\r\n";
				$out .= "</div>";
			}

			$out .= $form['grid_object']->renderLayout();

			$out .= "</" . $form_element . ">\r\n";
		}

		$out .= "</div>\r\n";

		// output javascript conditions.
		if(!empty($conditions_configs)){
			// sortout magics
			foreach($conditions_configs as &$condition_field_conf){
				if(!empty($condition_field_conf['group'])){
					foreach($condition_field_conf['group'] as &$condition_group){
						if(!empty($condition_group)){
							foreach($condition_group as &$condition_line){

								if( isset( $form['fields'][$condition_line['field']]['config']['option'][$condition_line['value']] )){
									$condition_line['label'] = $form['fields'][$condition_line['field']]['config']['option'][$condition_line['value']]['label'];
									$condition_line['value'] = $form['fields'][$condition_line['field']]['config']['option'][$condition_line['value']]['value'];
								}else{

									if( false !== strpos( $condition_line['field'] , '{') && false !== strpos( $condition_line['field'] , '}') ){
										$condition_line['field'] = self::do_magic_tags( $condition_line['field'] );
									}
								}

								//strip out fields
								$regex = "/%([a-zA-Z0-9_:]*)%/";
								preg_match_all($regex, $condition_line['value'], $matches);
								if(!empty($matches[1])){
									foreach( $matches[1] as $field_slug ){
										$value_field = self::get_field_by_slug( $field_slug, $form );
										$condition_line['selectors'][ $value_field['ID'] ] = '[data-field="' . $value_field['ID'] .'"]';
										$condition_line['value'] = str_replace( '%' . $field_slug . '%', $value_field['ID'], $condition_line['value'] );
									}
								}else{
									$condition_line['value'] = self::do_magic_tags( $condition_line['value'] );
								}
							}
						}
					}
				}
			}

			$conditions_str = json_encode($conditions_configs);
			// find %tags%
			preg_match_all("/%(.+?)%/", $conditions_str, $hastags);
			if(!empty($hastags[1])){

				foreach($hastags[1] as $tag_key=>$tag){

					foreach($form['fields'] as $field_id=>$field){
						if($field['slug'] === $tag){
							$conditions_str = str_replace('"'.$hastags[0][$tag_key].'"', "function(){ return jQuery('#".$field['ID'].'_'.$current_form_count."').val(); }", $conditions_str);
						}
					}
				}
			}

			$out .= "<script type=\"text/javascript\">\r\n";
			$out .= 'if( typeof caldera_conditionals === "undefined" ){ var caldera_conditionals = {}; }';
			$out .= "caldera_conditionals." . $form['ID'].'_'.$current_form_count . " = " . $conditions_str . ";\r\n";
			$out .= "</script>\r\n";
			if( !empty($conditions_templates) ){
				$out .= implode("\r\n", $conditions_templates);
			}

			// enqueue conditionls app.
			wp_enqueue_script( 'cf-conditionals' );
		}

		/**
		 * Runs after form is rendered
		 *
		 * @since unknown
		 *
		 * @param array $config Form config
		 */
		do_action('caldera_forms_render_end', $form);

		wp_enqueue_script( 'cf-field' );
		wp_enqueue_script( 'cf-validator' );
		wp_enqueue_script( 'cf-validator-i18n' );

		wp_enqueue_script( 'cf-init' );

		/**
		 * Filter final HTML of form
		 *
		 * @since unknow
		 *
		 * @param string $out The HTML
		 * @param array $config Form config
		 */
		return apply_filters( 'caldera_forms_render_form', $out, $form);

	}

	/**
	 * Returns the capability to manage Caldera Forms
	 *
	 * By default, returns "manage_options" can be filtered with "caldera_forms_manage_cap"
	 *
	 * @since 1.3.1
	 *
	 * @param string $context Optional. Context for checking capabilities.
	 *
	 * @return mixed|void
	 */
	public static function get_manage_cap( $context = 'admin', $form = false ) {
		if( is_string( $form ) ){
			$form = Caldera_Forms_Forms::get_form($form );
		}

		/**
		 * Change capability for managing Caldera Forms
		 *
		 * @since 1.3.1
		 *
		 * @param string $cap A capability. By default "manage_options"
		 * @param string $context Context to check in.
		 * @param array|null $form Form config if it was passed.
		 */
		return apply_filters( 'caldera_forms_manage_cap', 'manage_options', $context, $form );

	}

	/**
	 * Handler for shortcode
	 *
	 * @since 1.3.1
	 *
	 * @param array $atts Array of shortcode attributes
	 * @param string $content Enclosed content
	 * @param string $shortcode Shortcode type caldera_forms|caldera_forms_modal
	 *
	 * @return string|void
	 */
	public static function shortcode_handler( $atts, $content, $shortcode ) {
		if ( ! isset( $atts[ 'id' ] ) ) {
			return;
		}
		if( $shortcode === 'caldera_form_modal' ){
			$atts[ 'modal' ] = true;
		}

		if( isset( $atts[ 'modal' ] ) && $atts[ 'modal' ] ) {
			$form = Caldera_Forms_Forms::get_form( $atts[ 'id' ] );
			if( ! is_array( $form ) ) {
				return;
			}

			if( empty( $content ) ) {
				$content = $form[ 'name' ];
			}

			$tag_atts = sprintf( 'data-form="%1s"', $form['ID'] );
			if( empty( $atts['preview'] ) ){
				$tag_atts .= sprintf( 'data-remodal-target="modal-%1s"', $form['ID'] );
			}
			if( !empty( $atts['width'] ) ){
				$tag_atts .= sprintf( ' data-width="%1s"', $atts['width'] );
			}
			if( !empty( $atts['height'] ) ){
				$tag_atts .= sprintf( ' data-height="%1s"', $atts['height'] );
			}


			$title = __( sprintf( 'Click to open the form %1s in a modal',  $form[ 'name' ] ), 'caldera-forms' );
			if( !empty( $atts['type'] ) && $atts['type'] == 'button' ){
				$form = sprintf( '<button class="caldera-forms-modal" %1s title="%2s">%3s</button>', $tag_atts, $title, $content );
			}else{
				$form = sprintf( '<a href="#" class="caldera-forms-modal" %1s title="%2s">%3s</a>', $tag_atts, $title, $content );
			}

			wp_enqueue_script( 'cf-dynamic' );
		}else{
			$form = self::render_form( $atts );
		}

		return $form;

	}

	/**
	 * Convert time entry was submitted (as MySQL timestamp in UTC) to local display time
	 *
	 * @since 1.4.0
	 *
	 * @param string $submitted Timestamp
	 *
	 * @return string
	 */
	public static function localize_time( $submitted ){
		$dateformat = get_option('date_format');
		$timeformat = get_option('time_format');

		$format = $dateformat.' '.$timeformat;
		$time = get_date_from_gmt( $submitted, $format );

		return $time;
	}

	/**
	 * Setup auto-population options for Easy Pods and Easy Queries
	 *
	 * @since 1.4.3
	 *
	 * @uses "caldera_forms_render_start" action
	 */
	public static function easy_pods_queries_setup(){
		if ( version_compare( phpversion(), '5.3.0', '>=' )  ) {
			if ( function_exists( 'cep_get_easy_pod' ) || defined( 'CAEQ_PATH' ) ) {
				$setup = new Caldera_Forms_Render_AutoPopulation();
				$setup->add_hooks();
			}
		}


	}

}
