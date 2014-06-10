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

class Caldera_Forms_Admin {

	/**
	 * @var     string
	 */
	const VERSION = CFCORE_VER;
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
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add Admin menu page
		add_action( 'admin_menu', array( $this, 'register_admin_page' ) );
		
		// Add admin scritps and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_stylescripts' ) );

		// add element & fields filters
		add_filter('caldera_forms_get_panel_extensions', array( $this, 'get_panel_extensions'));
		add_filter('caldera_forms_entry_viewer_buttons', array( $this, 'set_viewer_buttons'),10, 4);
		
		// action
		add_action('caldera_forms_entry_actions', array( $this, 'get_entry_actions'),1);
		add_action('caldera_forms_admin_templates', array( $this, 'get_admin_templates'),1);


		add_action( 'wp_loaded', array( $this, 'save_form') );
		add_action( 'media_buttons', array($this, 'shortcode_insert_button' ), 11 );

		add_action("wp_ajax_create_form", array( $this, 'create_form') );
		add_action("wp_ajax_browse_entries", array( $this, 'browse_entries') );
		add_action("wp_ajax_save_cf_setting", array( $this, 'save_cf_setting') );
		add_action("wp_ajax_cf_dismiss_pointer", array( $this, 'update_pointer') );

		add_action( 'admin_footer', array( $this, 'add_shortcode_inserter'));	

	}

	public static function update_pointer(){

		if(!empty($_POST['pointer'])){
			add_user_meta( get_current_user_id() , 'cf_pointer_' . $_POST['pointer'] );
		}
		exit;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 */
	public function load_plugin_textdomain() {
		// TODO: Add translations as need in /languages
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
	}
	
	

	public static function add_shortcode_inserter(){
		
		$screen = get_current_screen();

		if($screen->base === 'post'){
			include CFCORE_PATH . 'ui/insert_shortcode.php';
		}
	} 

	public static function get_admin_templates(){
		include CFCORE_PATH . 'ui/admin_templates.php';
	}


	public static function get_entry_actions(){

		$viewer_buttons_array = apply_filters('caldera_forms_entry_viewer_buttons', array());
		$viewer_buttons = null;
		if(!empty($viewer_buttons_array)){
			$viewer_buttons = array();
			foreach($viewer_buttons_array as $button){
				if(is_array($button['config'])){
					$viewer_buttons[] = $button['label'].'|'.json_encode($button['config']);
				}else{
					$viewer_buttons[] = $button['label'].'|'.$button['config'];	
				}
			}

			$viewer_buttons = 'data-modal-buttons=\'' . implode(';', $viewer_buttons) . '\'';
		}

		echo '<button class="button button-small ajax-trigger" data-active-class="none" data-load-class="spinner" ' . $viewer_buttons . ' data-group="viewentry" data-entry="{{_entry_id}}" data-form="{{../form}}" data-action="get_entry" data-modal="view_entry" data-modal-width="550" data-modal-title="' . __('Entry', 'caldera-forms') . ' # {{_entry_id}}" data-template="#view-entry-tmpl" type="button">' . __('View', 'caldera-forms') . '</button> ';		
	}
	
	public static function set_viewer_buttons($buttons){
		
		$buttons['close_panel'] = array(
			'label'		=>	'Close',
			'config'	=>	'dismiss'
		);

		return $buttons;
	}

	public static function save_cf_setting(){
		if(empty($_POST['set'])){
			exit;
		}
		$style_includes = get_option( '_caldera_forms_styleincludes' );

		if(empty($style_includes[$_POST['set']])){
			$style_includes[$_POST['set']] = true;
		}else{
			$style_includes[$_POST['set']] = false;
		}
		update_option( '_caldera_forms_styleincludes', $style_includes);
		header('Content-Type: application/json');
		echo json_encode( $style_includes );
		exit;
	}

	/**
	 * Insert shortcode media button
	 *
	 *
	 */
	function shortcode_insert_button(){
		global $post;
		if(!empty($post)){
			echo "<a id=\"caldera-forms-form-insert\" title=\"".__('Add Form to Page','caldera-forms')."\" class=\"button caldera-forms-insert-button\" href=\"#inst\">\n";
			echo "	<img src=\"". CFCORE_URL . "assets/images/lgo-icon.png\" alt=\"".__("Insert Form Shortcode","caldera-forms")."\" style=\"padding: 0px 2px 0px 0px; width: 16px; margin: -2px 0px 0px;\" /> ".__('Caldera Form', 'caldera-forms')."\n";
			echo "</a>\n";
		}
	}


	/// activator
	public static function activate_caldera_forms(){
		global $wpdb;

		$tables = $wpdb->get_results("SHOW TABLES", ARRAY_A);
		foreach($tables as $table){
			$alltables[] = implode($table);
		}
		if(!in_array($wpdb->prefix.'cf_form_entries', $alltables)){
			// create tables
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

			$entry_table = "CREATE TABLE `" . $wpdb->prefix . "cf_form_entries` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`form_id` varchar(18) NOT NULL DEFAULT '',
			`user_id` int(11) NOT NULL,
			`datestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`),
			KEY `form_id` (`form_id`),
			KEY `user_id` (`user_id`),
			KEY `date_time` (`datestamp`)
			) DEFAULT CHARSET=utf8;";

			
			dbDelta( $entry_table );
			
			$values_table = "CREATE TABLE `" . $wpdb->prefix . "cf_form_entry_values` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`entry_id` int(11) NOT NULL,
			`slug` varchar(255) NOT NULL DEFAULT '',
			`value` longtext NOT NULL,
			PRIMARY KEY (`id`),
			KEY `form_id` (`entry_id`),
			KEY `slug` (`slug`)
			) DEFAULT CHARSET=utf8;";

			dbDelta( $values_table );
		
		}

	}


	public static function browse_entries(){

		global $wpdb;

		$page = 1;
		$perpage = 20;

		$form = get_option( $_POST['form'] );
			
		$field_labels = array();
		$backup_labels = array();
		$selects = array();

		// get all fieldtype
		$field_types = apply_filters('caldera_forms_get_field_types', array() );
		$fields = array();
		if(!empty($form['fields'])){
			foreach($form['fields'] as $fid=>$field){
				$fields[$field['slug']] = $field;

				if(!empty($field['entry_list'])){
					$selects[] = "'".$field['slug']."'";
					$field_labels[$field['slug']] = $field['label'];
				}
				if(count($backup_labels) < 4 && in_array($field['type'], array('text','email','date','name'))){
					// backup only first 4 fields
					$backup_labels[$field['slug']] = $field['label'];
				}
			}
		}
		if(empty($field_labels)){
			$field_labels = $backup_labels;
		}
		//ksort($field_labels);

		$data = array();

		$data['total'] = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(`id`) AS `total` FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `form_id` = %s;", $_POST['form']));

		$data['pages'] = ceil($data['total'] / $perpage );

		if(!empty($_POST['page'])){
			$page = abs( $_POST['page'] );
			if($page > $data['pages']){
				$page = $data['pages'];
			}
		}

		$data['current_page'] = $page;
		$gmt_offset = get_option( 'gmt_offset' );
		if($data['total'] > 0){
			
			$data['form'] = $_POST['form'];

			$data['fields'] = $field_labels;
			$offset = ($page - 1) * $perpage;
			$limit = $offset . ',' . $perpage;

			$rawdata = $wpdb->get_results($wpdb->prepare("
			SELECT
				`id`
			FROM `" . $wpdb->prefix ."cf_form_entries`

			WHERE `form_id` = %s ORDER BY `datestamp` DESC LIMIT " . $limit . ";", $_POST['form'] ));		

			if(!empty($rawdata)){

				$ids = array();
				foreach($rawdata as $row){
					$ids[] = $row->id;
				}

				$filter = null;
				if(!empty($selects)){
					//$filter .= " AND `value`.`slug` IN (" . implode(',', $selects) . ") ";
				}
				$rawdata = $wpdb->get_results("
				SELECT
					`entry`.`id` as `_entryid`,
					`entry`.`form_id` AS `_form_id`,
					`entry`.`datestamp` AS `_date_submitted`,
					`entry`.`user_id` AS `_user_id`,
					`value`.*

				FROM `" . $wpdb->prefix ."cf_form_entries` AS `entry`
				LEFT JOIN `" . $wpdb->prefix ."cf_form_entry_values` AS `value` ON (`entry`.`id` = `value`.`entry_id`)

				WHERE `entry`.`id` IN (" . implode(',',$ids) . ")
				" . $filter ."
				ORDER BY `entry`.`datestamp` DESC;");

				//print_r()
				$data['entries'] = array();
				$dateformat = get_option('date_format');
				$timeformat = get_option('time_format');
				foreach($rawdata as $row){
					if(!empty($row->_user_id)){
						$user = get_userdata( $row->_user_id );
						if(!empty($user)){
							$data['entries']['E' . $row->_entryid]['user']['ID'] = $user->ID;
							$data['entries']['E' . $row->_entryid]['user']['name'] = $user->data->display_name;
							$data['entries']['E' . $row->_entryid]['user']['email'] = $user->data->user_email;
							$data['entries']['E' . $row->_entryid]['user']['avatar'] = get_avatar( $user->ID, 64 );
						}
					}
					$data['entries']['E' . $row->_entryid]['_entry_id'] = $row->_entryid;
					$data['entries']['E' . $row->_entryid]['_date'] = date_i18n( $dateformat.' '.$timeformat, strtotime($row->_date_submitted), $gmt_offset);

					// setup default data array
					if(!isset($data['entries']['E' . $row->_entryid]['data'])){
						if(isset($field_labels)){
							foreach ($field_labels as $slug => $label) {
								// setup labels ordering
								$data['entries']['E' . $row->_entryid]['data'][$slug] = null;
							}
						}
					}

					if(!empty($field_labels[$row->slug])){

						$label = $field_labels[$row->slug];

						// check view handler
						$field = $fields[$row->slug];

						if(isset($field_types[$field['type']]['viewer'])){

							if(is_array($field_types[$field['type']]['viewer'])){
								$row->value = call_user_func_array($field_types[$field['type']]['viewer'],array($row->value, $field, $form));
							}else{
								if(function_exists($field_types[$field['type']]['viewer'])){
									$func = $field_types[$field['type']]['viewer'];
									$row->value = $func($row->value, $field, $form);
								}
							}
						}


						if(isset($data['entries']['E' . $row->_entryid]['data'][$row->slug])){
							// array based - add another entry
							if(!is_array($data['entries']['E' . $row->_entryid]['data'][$row->slug])){
								$tmp = $data['entries']['E' . $row->_entryid]['data'][$row->slug];
								$data['entries']['E' . $row->_entryid]['data'][$row->slug] = array($tmp);
							}
							$data['entries']['E' . $row->_entryid]['data'][$row->slug][] = $row->value;
						}else{
							$data['entries']['E' . $row->_entryid]['data'][$row->slug] = $row->value;
						}
					}

				}
			}
		}

		header('Content-Type: application/json');
		echo json_encode( $data );
		exit;


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
	 * Registers the admin page
	 *
	 */
	public function register_admin_page(){
		global $menu, $submenu;
		
		$this->screen_prefix = add_menu_page( 'Caldera Forms', 'Caldera Forms', 'manage_options', $this->plugin_slug, array( $this, 'render_admin' ), 'dashicons-list-view', 52.999 );

	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 *
	 * @return    null
	 */
	public function enqueue_admin_stylescripts() {

		$screen = get_current_screen();
		if($screen->base === 'post'){
			wp_enqueue_style( $this->plugin_slug .'-modal-styles', CFCORE_URL . 'assets/css/modals.css', array(), self::VERSION );
			wp_enqueue_script( $this->plugin_slug .'-shortcode-insert', CFCORE_URL . 'assets/js/shortcode-insert.js', array('jquery'), self::VERSION );
		}
		if( $screen->base !== $this->screen_prefix){
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script( 'wp-pointer' );
		wp_enqueue_style( 'wp-pointer' );


		wp_enqueue_script( 'password-strength-meter' );

		wp_enqueue_style( $this->plugin_slug .'-admin-styles', CFCORE_URL . 'assets/css/admin.css', array(), self::VERSION );
		wp_enqueue_style( $this->plugin_slug .'-modal-styles', CFCORE_URL . 'assets/css/modals.css', array(), self::VERSION );
		wp_enqueue_script( $this->plugin_slug .'-admin-scripts', CFCORE_URL . 'assets/js/admin.js', array(), self::VERSION );
		wp_enqueue_script( $this->plugin_slug .'-handlebars', CFCORE_URL . 'assets/js/handlebars.js', array(), self::VERSION );
		wp_enqueue_script( $this->plugin_slug .'-baldrick-handlebars', CFCORE_URL . 'assets/js/handlebars.baldrick.js', array($this->plugin_slug .'-baldrick'), self::VERSION );
		wp_enqueue_script( $this->plugin_slug .'-baldrick-modals', CFCORE_URL . 'assets/js/modals.baldrick.js', array($this->plugin_slug .'-baldrick'), self::VERSION );
		wp_enqueue_script( $this->plugin_slug .'-baldrick', CFCORE_URL . 'assets/js/jquery.baldrick.js', array('jquery'), self::VERSION );


		if(!empty($_GET['edit'])){

			// editor specific styles
			wp_enqueue_script( $this->plugin_slug .'-edit-fields', CFCORE_URL . 'assets/js/edit.js', array('jquery'), self::VERSION );
			wp_enqueue_script( 'jquery-ui-users' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-droppable' );

		}
			// Load Field Types Styles & Scripts
			$field_types = apply_filters('caldera_forms_get_field_types', array() );

			// load element types 
			$panel_extensions = apply_filters('caldera_forms_get_panel_extensions', array() );

			// merge a list
			$merged_types = array_merge($field_types, $panel_extensions);

			foreach( $merged_types as $type=>&$config){

				// set context
				if(!empty($_GET['edit'])){
					$context = &$config['setup'];
				}else{
					$context = &$config;
				}

				/// Styles
				if(!empty($context['styles'])){
					foreach($context['styles'] as $location=>$styles){

						// front only scripts
						if($location === 'front'){
							continue;
						}

						

						foreach( (array) $styles as $style){

							$key = $type . '-' . sanitize_key( basename( $style) );

							// is url
							if(false === strpos($style, "/")){
								// is reference
								wp_enqueue_style( $style );

							}else{
								// is url - 
								if(file_exists( $style )){
									// local file
									wp_enqueue_style( $key, plugin_dir_url( $style ) . basename( $style ), array(), self::VERSION );
								}else{
									// most likely remote
									wp_enqueue_style( $key, $style, array(), self::VERSION );
								}

							}
						}
					}
				}
				/// scripts
				if(!empty($context['scripts'])){

					foreach($context['scripts'] as $location=>$scripts){
						
						// front only scripts
						if($location === 'front'){
							continue;
						}

						foreach( (array) $scripts as $script){
							


							$key = $type . '-' . sanitize_key( basename( $script) );

							// is url
							if(false === strpos($script, "/")){
								// is reference
								wp_enqueue_script( $script );

							}else{
								// is url - 
								if(file_exists( $script )){
									// local file
									wp_enqueue_script( $key, plugin_dir_url( $script ) . basename( $script ), array('jquery'), self::VERSION );
								}else{
									// most likely remote
									wp_enqueue_script( $key, $script, array('jquery'), self::VERSION );
								}

							}
						}
					}
				}
			}			

		//}
	}

	/**
	 * Renders the admin pages
	 *
	*/
	public function render_admin(){
		
		echo "	<div class=\"wrap\">\r\n";
		if(!empty($_GET['edit'])){
			echo "<form method=\"POST\" action=\"admin.php?page=" . $this->plugin_slug . "\" data-load-element=\"#save_indicator\" data-sender=\"ajax\" class=\"caldera-forms-options-form edit-update-trigger\">\r\n";
				include CFCORE_PATH . 'ui/edit.php';
			echo "</form>\r\n";
		}elseif(!empty($_GET['project'])){
			include CFCORE_PATH . 'ui/project.php';
		}else{
			include CFCORE_PATH . 'ui/admin.php';
		}
		echo "	</div>\r\n";

	}

	/***
	 * Save form
	 *
	*/
	static function save_form(){

		/// check for form delete
		if(!empty($_GET['delete']) && !empty($_GET['cal_del'])){

			if ( ! wp_verify_nonce( $_GET['cal_del'], 'cf_del_frm' ) ) {
				// This nonce is not valid.
				wp_die( __('Sorry, please try again', 'caldera-forms'), __('Form Delete Error', 'caldera-forms') );
			}else{
				// ok to delete
				// get form registry
				$forms = get_option( '_caldera_forms' );
				if(isset($forms[$_GET['delete']])){
					unset($forms[$_GET['delete']]);
					if(delete_option( $_GET['delete'] )){
						do_action('caldera_forms_delete_form', $_GET['delete']);
						update_option( '_caldera_forms', $forms );	
					}
				}

				wp_redirect('admin.php?page=caldera-forms' );
				exit;

			}
			
		}

		if(!empty($_GET['export'])){

			$form = get_option( $_GET['export'] );

			global $wpdb;

			//build labels
			$labels = array();
			if(!empty($form['fields'])){
				foreach($form['fields'] as $field){
					$labels[$field['slug']] = $field['label'];
				}
			}

			$rawdata = $wpdb->get_results($wpdb->prepare("
			SELECT
				`entry`.`id` as `_entryid`,
				`entry`.`form_id` AS `_form_id`,
				`entry`.`datestamp` AS `_date_submitted`,
				`entry`.`user_id` AS `_user_id`,
				`value`.*

			FROM `" . $wpdb->prefix ."cf_form_entries` AS `entry`
			LEFT JOIN `" . $wpdb->prefix ."cf_form_entry_values` AS `value` ON (`entry`.`id` = `value`.`entry_id`)

			WHERE `entry`.`form_id` = %s
			
			ORDER BY `entry`.`datestamp` DESC;", $_GET['export']));

			$data = array();
			$headers = array();
			foreach( $rawdata as $entry){
				// check for json
				if(substr($entry->value, 0,2) === '{"' && substr($entry->value, strlen($entry->value)-2 ) === '"}'){
					$row = json_decode($entry->value, true);
					if(!empty($row)){
						$keys = array_keys($row);
						if(is_int($keys[0])){
							$row = implode(', ', $row);
						}else{
							$tmp = array();
							foreach($row as $key=>$item){
								if(is_array($item)){
									$item = '( ' . implode(', ', $item).' )';
								}
								$tmp[] = $key.': '.$item;
							}
							$row = implode(', ', $tmp);
						}
					$entry->value = $row;
					}
				}
				$data[$entry->_entryid][$entry->slug] = $entry->value;
				$label = $entry->slug;
				if(isset($labels[$entry->slug])){
					$label = $labels[$entry->slug];
				}
				$headers[$entry->slug] = $label;
			}


			
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"" . sanitize_file_name( $form['name'] ) . ".csv\";" );
			header("Content-Transfer-Encoding: binary"); 
			
			$df = fopen("php://output", 'w');
			fputcsv($df, $headers);
			foreach($data as $row){
				$csvrow = array();
				foreach($headers as $key=>$label){
					if(!isset($row[$key])){
						$row[$key] = null;
					}
					$csvrow[] = $row[$key];
				}
				fputcsv($df, $row);
			}
			fclose($df);			
			exit;			
		}

		if( isset($_POST['config']) && isset( $_POST['cf_edit_nonce'] ) ){

			// if this fails, check_admin_referer() will automatically print a "failed" page and die.
			if ( check_admin_referer( 'cf_edit_element', 'cf_edit_nonce' ) ) {

				// strip slashes
				$data = stripslashes_deep($_POST['config']);

				// get form registry
				$forms = get_option( '_caldera_forms' );
				if(empty($forms)){
					$forms = array();
				}

				// add form to registry
				$forms[$data['ID']] = $data;

				// remove undeeded settings for registry
				if(isset($forms[$data['ID']]['layout_grid'])){
					unset($forms[$data['ID']]['layout_grid']);
				}
				if(isset($forms[$data['ID']]['fields'])){
					unset($forms[$data['ID']]['fields']);
				}
				if(isset($forms[$data['ID']]['processors'])){
					unset($forms[$data['ID']]['processors']);
				}
				if(isset($forms[$data['ID']]['settings'])){
					unset($forms[$data['ID']]['settings']);
				}

				// add from to list
				update_option($data['ID'], $data);
				do_action('caldera_forms_save_form', $data);

				update_option( '_caldera_forms', $forms );
				do_action('caldera_forms_save_form_register', $data);

				if(!empty($_POST['sender'])){
					exit;
				}

				wp_redirect('admin.php?page=caldera-forms');
				die;

			}
			return;
		}
	}

	public static function create_form(){


		parse_str( $_POST['data'], $newform );

		// get form registry
		$forms = get_option( '_caldera_forms' );
		if(empty($forms)){
			$forms = array();
		}

		$newform = array(
			"ID" 			=> uniqid('CF'),
			"name" 			=> $newform['name'],
			"description" 	=> $newform['description'],
			"success"		=>	__('Form has been successfuly submitted. Thank you.')
		);

		// add from to list
		$newform = apply_filters('caldera_forms_create_form', $newform);

		$forms[$newform['ID']] = $newform;
		update_option( '_caldera_forms', $forms );

		// add form to db
		update_option($newform['ID'], $newform);
		do_action('caldera_forms_create_form', $newform);

		echo $newform['ID'];
		exit;


	}


	// get internal panel extensions

	public function get_panel_extensions($panels){

		$path = CFCORE_PATH . "ui/panels/";
		
		$internal_panels = array(
			'form_layout' => array(
				"name"			=>	__("Layout", 'caldera-forms'),
				"setup"		=>	array(
					"scripts"	=>	array(
						'jquery-ui-sortable',
						'jquery-ui-draggable',
						'jquery-ui-droppable',
						CFCORE_URL . "assets/js/processors-edit.js",
						CFCORE_URL . "assets/js/layout-grid.js"
					),
					"styles"	=>	array(
						CFCORE_URL . "assets/css/editor-grid.css",
						CFCORE_URL . "assets/css/processors-edit.css"
					),
				),
				"tabs"		=>	array(
					"layout" => array(
						"name" => __("Layout", 'caldera-forms'),
						"location" => "lower",
						"label" => __("Layout Builder", 'caldera-forms'),
						"active" => true,
						"actions" => array(
							$path . "layout_add_row.php"
						),
						"repeat" => 0,
						"canvas" => $path . "layout.php",
						"side_panel" => $path . "layout_side.php",
					),
					"processors" => array(
						"name" => __("Processors", 'caldera-forms'),
						"location" => "lower",
						"label" => __("Form Processors", 'caldera-forms'),
						"canvas" => $path . "processors.php",
					),
					"responsive" => array(
						"name" => __("Responsive", 'caldera-forms'),
						"location" => "lower",
						"label" => __("Resposive Settings", 'caldera-forms'),
						"repeat" => 0,
						"fields" => array(
							"break_point" => array(
								"label" => __("Grid Collapse", 'caldera-forms'),
								"slug" => "break_point",
								"caption" => __("Set the smallest screen size at which to collapse the grid. (based on Bootstrap 3.0)", 'caldera-forms'),
								"type" => "radio",
								"config" => array(
									"default" => "sm",
									"option"	=> array(
										"xs"	=> array(
											'value'	=> 'xs',
											'label'	=> __('Maintain grid always', 'caldera-forms'),
										),
										"sm"	=> array(
											'value'	=> 'sm',
											'label'	=> '< 767px'
										),
										"md"	=> array(
											'value'	=> 'md',
											'label'	=> '< 991px'
										),
										"lg"	=> array(
											'value'	=> 'lg',
											'label'	=> '< 1199px'
										)
									)
								),
							)
						),
					),
					"mailer" => array(
						"name" => __("Mailer", 'caldera-forms'),
						"location" => "lower",
						"label" => __("Email Notification Settings", 'caldera-forms'),
						"canvas" => $path . "emailer.php",
					),
					/*
					"styles" => array(
						"name" => __("Stylesheets", 'caldera-forms'),
						"location" => "lower",
						"label" => __("Stylesheet Includes", 'caldera-forms'),
						"repeat" => 0,
						"fields" => array(
							"use_grid" => array(
								"label" => __("Grid CSS", 'caldera-forms'),
								"slug" => "use_grid",
								"caption" => __("Include the built in grid stylesheet (based on Bootstrap 3.0)", 'caldera-forms'),
								"type" => "dropdown",
								"config" => array(
									"default" => "yes",
									"option"	=> array(
										"opt1"	=> array(
											'value'	=> 'yes',
											'label'	=> 'Yes'
										),
										"opt2"	=> array(
											'value'	=> 'no',
											'label'	=> 'No'
										)
									)
								),
							),
							"use_form" => array(
								"label" => __("Form CSS", 'caldera-forms'),
								"slug" => "use_grid",
								"caption" => __("Include the built in form stylesheet (based on Bootstrap 3.0)", 'caldera-forms'),
								"type" => "dropdown",
								"config" => array(
									"default" => "yes",
									"option"	=> array(
										"opt1"	=> array(
											'value'	=> 'yes',
											'label'	=> 'Yes'
										),
										"opt2"	=> array(
											'value'	=> 'no',
											'label'	=> 'No'
										)
									)
								),
							),
							"use_alerts" => array(
								"label" => __("Alerts CSS", 'caldera-forms'),
								"slug" => "use_alerts",
								"caption" => __("Include the built in alerts stylesheet (based on Bootstrap 3.0)", 'caldera-forms'),
								"type" => "dropdown",
								"config" => array(
									"default" => "yes",
									"option"	=> array(
										"opt1"	=> array(
											'value'	=> 'yes',
											'label'	=> 'Yes'
										),
										"opt2"	=> array(
											'value'	=> 'no',
											'label'	=> 'No'
										)
									)
								),
							),
						),
					),*/
				),
			),
		);
		
		return array_merge( $panels, $internal_panels );
		
	}

}


