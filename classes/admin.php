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
	 * @var     string
	 */
	const UPDATE_URL   = 'http://fooplugins.com/api/';

	/**
	 * @var      string
	 */
	protected $plugin_slug = 'caldera-forms';
	/**
	 * @var      string
	 */
	protected $screen_prefix = array();
	/**
	 * @var      string
	 */
	protected $sub_prefix = null;
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
		add_filter('caldera_forms_get_panel_extensions', array( $this, 'get_panel_extensions'), 1);
		add_filter('caldera_forms_entry_viewer_buttons', array( $this, 'set_viewer_buttons'),10, 4);
		
		// action
		add_action('caldera_forms_entry_actions', array( $this, 'get_entry_actions'),1);
		add_action('caldera_forms_admin_templates', array( $this, 'get_admin_templates'),1);
		add_action('caldera_forms_entry_meta_templates', array( $this, 'get_admin_meta_templates'),1);

		add_action( 'wp_loaded', array( $this, 'save_form') );
		add_action( 'media_buttons', array($this, 'shortcode_insert_button' ), 11 );

		add_action("wp_ajax_create_form", array( $this, 'create_form') );
		add_action("wp_ajax_browse_entries", array( $this, 'browse_entries') );
		add_action("wp_ajax_save_cf_setting", array( $this, 'save_cf_setting') );
		add_action("wp_ajax_cf_dismiss_pointer", array( $this, 'update_pointer') );
		add_action("wp_ajax_cf_bulk_action", array( $this, 'bulk_action') );

		add_action( 'admin_footer', array( $this, 'add_shortcode_inserter'));


		$addons = apply_filters( 'caldera_forms_get_addons', array() );

		foreach($addons as $slug=>$file){

			//initialize plugin update checks with fooplugins.com
			new foolic_update_checker_v1_5(
				$file, //the plugin file
				self::UPDATE_URL . $slug . '/check', //the URL to check for updates
				$slug, //the plugin slug
				get_site_option($slug . '_licensekey') //the stored license key
			);

			//initialize license key validation with fooplugins.com
			new foolic_validation_v1_4(
				self::UPDATE_URL . $slug . '/check', //the URL to validate
				$slug
			);

			add_filter('foolic_validation_include_css-' . $slug, array(&$this, 'include_foolic_files'));
			add_filter('foolic_validation_include_js-' . $slug, array(&$this, 'include_foolic_files'));

		}

	}


	//make sure the foo license validation CSS & JS are included on the correct page
	function include_foolic_files($screen) {
		return $screen->id === 'caldera-forms_page_caldera-forms-exend';
	}


	public function bulk_action(){

		if(empty($_POST['do'])){
			die;
		}

		switch ($_POST['do']) {
			case 'active':
			case 'trash':
			case 'delete':
				global $wpdb;

				// clean out
				$items = array();
				$selectors = array();
				foreach((array) $_POST['items'] as $item_id){
					$items[] = (int) $item_id;
					$selectors[] = '#entry_row_' . (int) $item_id;
				}
				switch ($_POST['do']) {
					case 'delete':
						$result = $wpdb->query( "DELETE FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `id` IN (".implode(',', $items).");" );
						$result = $wpdb->query( "DELETE FROM `" . $wpdb->prefix . "cf_form_entry_values` WHERE `entry_id` IN (".implode(',', $items).");" );
						$result = $wpdb->query( "DELETE FROM `" . $wpdb->prefix . "cf_form_entry_meta` WHERE `entry_id` IN (".implode(',', $items).");" );
						header('Content-Type: application/json');
						$out['status'] = 'reload';
						echo json_encode($out);
						break;
					
					default:
						$result = $wpdb->query( $wpdb->prepare( "UPDATE `" . $wpdb->prefix . "cf_form_entries` SET `status` = %s WHERE `id` IN (".implode(',', $items).");", $_POST['do'] ) );
						break;
				}
				
				if( $result ){
					header('Content-Type: application/json');
					$out['status'] = $_POST['do'];
					$out['undo'] = ( $_POST['do'] === 'trash' ? 'active' : __('Trash') );
					$out['undo_text'] = ( $_POST['do'] === 'trash' ? __('Restore', 'caldera-forms') : __('Trash') );

					$out['entries'] = implode(',',$selectors);
					$out['total']	= $wpdb->get_var($wpdb->prepare("SELECT COUNT(`id`) AS `total` FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `form_id` = %s && `status` = 'active';", $_POST['form']));
					$out['trash']	= $wpdb->get_var($wpdb->prepare("SELECT COUNT(`id`) AS `total` FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `form_id` = %s && `status` = 'trash';", $_POST['form']));
					echo json_encode($out);
				}
				exit();

				break;
			case 'export':

				$transientid = uniqid('cfe');
				set_transient( $transientid, $_POST['items'], 180 );
				header('Content-Type: application/json');
				$out['url'] = "admin.php?page=caldera-forms&export=" . $_POST['form'] . "&tid=" . $transientid;
				echo json_encode($out);
				exit();
				break;
			default:
				# code...
				break;
		}
		exit();	
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
		load_plugin_textdomain( $this->plugin_slug, FALSE, basename( CFCORE_PATH ) . '/languages');
	}
	
	

	public static function add_shortcode_inserter(){
		
		$screen = get_current_screen();

		if($screen->base === 'post'){
			include CFCORE_PATH . 'ui/insert_shortcode.php';
		}
	} 

	public static function get_admin_templates(){
		include CFCORE_PATH . 'ui/news_templates.php';
		include CFCORE_PATH . 'ui/admin_templates.php';
	}
	public static function get_admin_meta_templates(){
		
		$processors = apply_filters( 'caldera_forms_get_form_processors', array() );
		if(!empty($processors)){
			foreach($processors as $processor_type=>$processor_config){
				if( isset( $processor_config['meta_template'] ) && file_exists( $processor_config['meta_template'] ) ){
					echo "{{#if ".$processor_type."_template}}\r\n";
						echo "{{#each data}}\r\n";
							echo "{{#if title}}\r\n";
								echo "<h4>{{title}}</h4>\r\n";
							echo "{{/if}}\r\n";
							echo "{{#each entry}}\r\n";
								include $processor_config['meta_template'];
							echo "{{/each}}\r\n";
						echo "{{/each}}\r\n";
					echo "{{/if}}\r\n";
				}
			}
		}
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

		echo '{{#if ../../is_active}}<button class="button button-small ajax-trigger view-entry-btn" data-active-class="none" data-load-class="spinner" ' . $viewer_buttons . ' data-group="viewentry" data-entry="{{_entry_id}}" data-form="{{../../form}}" data-action="get_entry" data-modal="view_entry" data-modal-width="600" data-modal-title="' . __('Entry', 'caldera-forms') . ' # {{_entry_id}}" data-template="#view-entry-tmpl" type="button">' . __('View') . '</button> {{/if}}';		
		echo '<button type="button" class="button button-small ajax-trigger" data-load-class="active" data-panel="{{#if ../../is_trash}}trash{{/if}}{{#if ../../is_active}}active{{/if}}" data-do="{{#if ../../is_trash}}active{{/if}}{{#if ../../is_active}}trash{{/if}}" data-callback="cf_refresh_view" data-form="{{../../form}}" data-active-class="disabled" data-group="row{{_entry_id}}" data-load-element="#entry_row_{{_entry_id}}" data-action="cf_bulk_action" data-items="{{_entry_id}}">{{#if ../../is_trash}}' . __('Restore', 'caldera-forms') . '{{/if}}{{#if ../../is_active}}' . __('Trash') . '{{/if}}</button>';
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


	public static function browse_entries(){

		global $wpdb;

		$page = 1;
		$perpage = 20;

		$form = get_option( $_POST['form'] );
			
		$field_labels = array();
		$backup_labels = array();
		$selects = array();

		// get all fieldtype
		$field_types = Caldera_Forms::get_field_types();


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

		$filter = null;
		if(!empty($selects)){
			//$filter .= " AND `value`.`slug` IN (" . implode(',', $selects) . ") ";
		}
		// status
		$status = "'active'";
		if(!empty($_POST['status'])){
			$status = $wpdb->prepare("%s", $_POST['status']);
		}


		$data['trash'] = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(`id`) AS `total` FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `form_id` = %s AND `status` = 'trash';", $_POST['form']));
		$data['active'] = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(`id`) AS `total` FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `form_id` = %s AND `status` = 'active';", $_POST['form']));
	

		// set current total
		if(!empty($_POST['status']) && isset($data[$_POST['status']])){
			$data['total'] = $data[$_POST['status']];
		}else{
			$data['total'] = $data['active'];
		}


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

			//if(!empty($selects)){
			//$filter .= " AND `entry`.`status` = ".$status." ";
			//}


			$rawdata = $wpdb->get_results($wpdb->prepare("
			SELECT
				`id`,
				`form_id`
			FROM `" . $wpdb->prefix ."cf_form_entries`

			WHERE `form_id` = %s AND `status` = ".$status." ORDER BY `datestamp` DESC LIMIT " . $limit . ";", $_POST['form'] ));		

			if(!empty($rawdata)){

				foreach($rawdata as $entry){
					//$data = Caldera_Forms::get_submission_data($entry->form_id, $entry->id);
				}

				$ids = array();
				foreach($rawdata as $row){
					$ids[] = $row->id;
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

						$row->value = apply_filters('caldera_forms_view_field_' . $field['type'], $row->value, $field, $form);


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

		// set status output
		$data['is_' . $_POST['status']] = true;

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
		
		$forms = get_option( '_caldera_forms' );

		$this->screen_prefix[] = add_menu_page( __('Caldera Forms', 'caldera-forms'), __('Caldera Forms', 'caldera-forms'), 'manage_options', $this->plugin_slug, array( $this, 'render_admin' ), 'dashicons-list-view', 52.999 );
		add_submenu_page( $this->plugin_slug, __('Caldera Forms Admin', 'caldera-forms'), __('Forms', 'caldera-forms'), 'manage_options', $this->plugin_slug, array( $this, 'render_admin' ) );
		
		if( ! empty( $forms ) ){
			foreach($forms as $form_id=>$form){
				if(!empty($form['pinned'])){
					$this->screen_prefix[] 	 = add_submenu_page( $this->plugin_slug, __('Caldera Forms', 'caldera-forms').' - ' . $form['name'], '- '.$form['name'], 'manage_options', $this->plugin_slug . '-pin-' . $form_id, array( $this, 'render_admin' ) );
				}
			}
		}		


		$this->screen_prefix[] 	 = add_submenu_page( $this->plugin_slug, __('Caldera Forms', 'caldera-forms') .' - '. __('Community', 'caldera-forms'), __('Community', 'caldera-forms'), 'manage_options', $this->plugin_slug . '-community', array( $this, 'render_admin' ) );
		$this->screen_prefix[] 	 = add_submenu_page( $this->plugin_slug, __('Caldera Forms', 'caldera-forms') . ' - ' . __('Extend', 'caldera-forms'), __('Extend', 'caldera-forms'), 'manage_options', $this->plugin_slug . '-exend', array( $this, 'render_admin' ) );


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
		if( !in_array( $screen->base, $this->screen_prefix ) ){
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
		if(!empty($_GET['edit-entry'])){
			wp_enqueue_style( 'cf-grid-styles', CFCORE_URL . 'assets/css/caldera-grid.css', array(), self::VERSION );
		}

		
			// Load Field Types Styles & Scripts
			$field_types = apply_filters('caldera_forms_get_field_types', array() );

			// load panels
			$panel_extensions = apply_filters('caldera_forms_get_panel_extensions', array() );

			// load processors
			$form_processors = apply_filters('caldera_forms_get_form_processors', array() );

			// merge a list
			$merged_types = array_merge($field_types, $panel_extensions, $form_processors);

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
		}elseif(!empty($_GET['page']) && $_GET['page'] == 'caldera-forms-exend'){
			include CFCORE_PATH . 'ui/extend.php';
		}elseif(!empty($_GET['page']) && $_GET['page'] == 'caldera-forms-community'){
			include CFCORE_PATH . 'ui/community.php';
		}elseif(!empty($_GET['page']) && false !== strpos($_GET['page'], 'caldera-forms-pin-')){
			$formID = substr($_GET['page'], 18);
			$form = get_option($formID);
			include CFCORE_PATH . 'ui/entries.php';

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
		if( isset($_POST['cfimporter']) ){

			if ( check_admin_referer( 'cf-import', 'cfimporter' ) ) {
				if(!empty($_FILES['import_file']['size'])){
					$loc = wp_upload_dir();
					if(move_uploaded_file($_FILES['import_file']['tmp_name'], $loc['path'].'/cf-form-import.json')){
						$data = json_decode(file_get_contents($loc['path'].'/cf-form-import.json'), true);
						if(isset($data['ID']) && isset($data['name']) && isset($data['fields'])){

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
							do_action('caldera_forms_import_form', $data);

							update_option( '_caldera_forms', $forms );
							do_action('caldera_forms_save_form_register', $data);

							wp_redirect( 'admin.php?page=caldera-forms&edit=' . $data['ID'] );
							exit;

						}else{
							wp_die( __('Sorry, File is not valid.', 'caldera-forms'), __('Form Import Error', 'caldera-forms') );
						}
					}
				}else{
					wp_die( __('Sorry, File not uploaded.', 'caldera-forms'), __('Form Import Error', 'caldera-forms') );
				}

			}else{

				wp_die( __('Sorry, please try again', 'caldera-forms'), __('Form Import Error', 'caldera-forms') );
			}

		}
		if(!empty($_GET['export-form'])){

			$form = get_option( $_GET['export-form'] );

			if(empty($form)){
				wp_die( __('Form does not exist.', 'caldera-forms') );
			}

			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: application/json");
			header("Content-Disposition: attachment; filename=\"" . sanitize_file_name( strtolower( $form['name'] ) ) . "-export.json\";" );
			echo json_encode($form);
			exit;

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
			$filter = null;
			// export set - transient
			if(!empty($_GET['tid'])){
				$items = get_transient( $_GET['tid'] );
				if(!empty($items)){
					$filter = ' AND `entry`.`id` IN (' . implode(',', $items) . ') ';
				}else{
					wp_die( __('Export selection has expired', 'caldera-forms') , __('Export Expired', 'caldera-forms') );
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
			" . $filter . "
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
				$data = json_decode( stripslashes_deep($_POST['config']) , ARRAY_A );
				// get form registry
				$forms = get_option( '_caldera_forms' );
				if(empty($forms)){
					$forms = array();
				}
				// option value labels
				if(!empty($data['fields'])){
					foreach($data['fields'] as &$field){
						if(!empty($field['config']['option']) && is_array($field['config']['option'])){
							foreach($field['config']['option'] as &$option){
								if(!isset($option['value'])){
									$option['value'] = $option['label'];
								}
							}
						}
					}
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

				foreach($forms as $form_id=>$form_config){
					if(empty($form_config)){
						unset( $forms[$form_id] );
					}
				}
				// combine structure pages
				$data['layout_grid']['structure'] = implode('#', $data['layout_grid']['structure']);
				
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
		if(!empty($newform['clone'])){
			$clone = $newform['clone'];
		}
		$newform = array(
			"ID" 			=> uniqid('CF'),
			"name" 			=> $newform['name'],
			"description" 	=> $newform['description'],
			"success"		=>	__('Form has been successfuly submitted. Thank you.', 'caldera-forms'),
			"hide_form"		=> 1
		);

		// add from to list
		$newform = apply_filters('caldera_forms_create_form', $newform);

		$forms[$newform['ID']] = $newform;
		update_option( '_caldera_forms', $forms );
		
		if(!empty($clone)){
			$clone_form = get_option( $clone );
			if(!empty($clone_form['ID']) && $clone == $clone_form['ID']){
				$newform = array_merge($clone_form, $newform);
			}
		}
		
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
					"pages" => array(
						"name" => __("Pages", 'caldera-forms'),
						"location" => "lower",
						"label" => __("Form Pages", 'caldera-forms'),
						"canvas" => $path . "pages.php",
					),
					"processors" => array(
						"name" => __("Processors", 'caldera-forms'),
						"location" => "lower",
						"label" => __("Form Processors", 'caldera-forms'),
						"canvas" => $path . "processors.php",
					),
					"variables" => array(
						"name" => __("Variables", 'caldera-forms'),
						"location" => "lower",
						"label" => __("Variables", 'caldera-forms'),
						"canvas" => $path . "variables.php",
						"actions" => array(
							$path . "variable_add.php"
						)
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


