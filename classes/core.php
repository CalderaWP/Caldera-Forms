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
		//add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add Admin menu page
		add_action( 'admin_menu', array( $this, 'register_admin_page' ) );
		
		// Add admin scritps and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_stylescripts' ) );

		// add element & fields filters
		add_filter('caldera_forms_get_panel_extensions', array( $this, 'get_panel_extensions'));
		add_filter('caldera_forms_get_field_types', array( $this, 'get_field_types'));
		add_filter('caldera_forms_get_form_processors', array( $this, 'get_form_processors'));

		// action
		add_action('caldera_forms_submit_complete', array( $this, 'save_final_form'),1,2);

		if(is_admin()){
			add_action( 'wp_loaded', array( $this, 'save_form') );
			add_action( 'media_buttons', array($this, 'shortcode_insert_button' ), 11 );
		}else{
			// find if profile is loaded
			add_action('wp', array( $this, 'check_user_profile_shortcode'));

			// template render
			add_shortcode( 'caldera_form', array( $this, 'render_form') );
		}

		//add_action('wp_footer', array( $this, 'footer_scripts' ) );
		add_action("wp_ajax_create_form", array( $this, 'create_form') );
		add_action("wp_ajax_browse_entries", array( $this, 'browse_entries') );


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
		if(!empty($form['fields'])){
			foreach($form['fields'] as $fid=>$field){
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

		if(!empty((int) $_POST['page'])){
			$page = abs( (int) $_POST['page'] );
			if($page > $data['pages']){
				$page = $data['pages'];
			}
		}

		$data['current_page'] = $page;

		if($data['total'] > 0){
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
					$filter .= " AND `value`.`slug` IN (" . implode(',', $selects) . ") ";
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

				
				$data['entries'] = array();
				$dateformat = get_option('date_format');
				$timeformat = get_option('time_format');
				foreach($rawdata as $row){

					if(!empty($row->_user_id)){
						$user = get_userdata( $row->_user_id );
						$data['entries']['E' . $row->_entryid]['user']['ID'] = $user->ID;
						$data['entries']['E' . $row->_entryid]['user']['name'] = $user->data->display_name;
						$data['entries']['E' . $row->_entryid]['user']['email'] = $user->data->user_email;
						$data['entries']['E' . $row->_entryid]['user']['avatar'] = get_avatar( $user->ID, 64 );
					}
					$data['entries']['E' . $row->_entryid]['_entry_id'] = $row->_entryid;
					$data['entries']['E' . $row->_entryid]['_date'] = date_i18n( $dateformat.' '.$timeformat, strtotime($row->_date_submitted) );
					$label = $row->slug;
					if(!empty($field_labels[$label])){
						$label = $field_labels[$label];
						if(!isset($data['entries']['E' . $row->_entryid]['data'])){
							if(isset($field_labels)){
								foreach ($field_labels as $slug => $label) {
									// setup labels ordering
									$data['entries']['E' . $row->_entryid]['data'][$slug] = null;
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
					//ksort($data['entries']['E' . $row->_entryid]['data']);
				}
			}
		}

		header('Content-Type: application/json');
		echo json_encode( $data );
		exit;


	}

	public static function save_final_form($data, $form){
		
		global $wpdb;

		$new_entry = array(
			'form_id'	=>	$form['ID'],
			'user_id'	=>	get_current_user_id(),			
		);
		$wpdb->insert($wpdb->prefix . 'cf_form_entries', $new_entry);
		$entryid = $wpdb->insert_id;
		
		foreach($data as $field=>$values){
			if(is_array($values)){
				$keys = array_keys($values);
				if(is_int($keys[0])){
					foreach((array) $values as $value){
						/// repeatable numerik index has same key
						$field_item = array(
							'entry_id'	=> $entryid,
							'slug'		=> $field,
							'value'		=> $value
						);
						$wpdb->insert($wpdb->prefix . 'cf_form_entry_values', $field_item);
					}
				}else{
					// named index is array stored
					$field_item = array(
						'entry_id'	=> $entryid,
						'slug'		=> $field,
						'value'		=> json_encode( $values )
					);
					$wpdb->insert($wpdb->prefix . 'cf_form_entry_values', $field_item);
				}
			}else{
				$field_item = array(
					'entry_id'	=> $entryid,
					'slug'		=> $field,
					'value'		=> $values
				);
				$wpdb->insert($wpdb->prefix . 'cf_form_entry_values', $field_item);
			}
		}


		if(empty($form['mailer']['enable_mailer'])){
			return;
		}
		// do mailer!
		$attachment = null;		
		$sendername = __('Caldera Forms Notification', 'caldera-forms');
		if(!empty($form['mailer']['sender_name'])){
			$sendername = $form['mailer']['sender_name'];
		}
		if(empty($form['mailer']['sender_email'])){
			$sendermail = get_option( 'admin_email' );
		}else{
			$sendermail = $form['mailer']['sender_email'];
		}



		$headers = 'From: ' . $sendername . ' <' . $sendermail . '>' . "\r\n";
		if($form['mailer']['email_type'] == 'html'){
			$headers .= "Content-type: text/html\r\n";
		}
		if(!empty($form['mailer']['recipients'])){
			$recipients = $form['mailer']['recipients'];
		}else{
			$recipients = get_option( 'admin_email' );
		}

		$message = $form['mailer']['email_message']."\r\n";
		$subject = $form['mailer']['email_subject'];
		$submission = array();
		foreach ($data as $key=>$row) {
			if(is_array($row)){
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
			}
			$message = str_replace('%'.$key.'%', $row, $message);
			$subject = str_replace('%'.$key.'%', $row, $subject);
			
			$submission[] = $row;				
		}
		// CSV
		if(!empty($form['mailer']['csv_data'])){
			ob_start();
			$df = fopen("php://output", 'w');
			fputcsv($df, array_keys($data));
			fputcsv($df, $submission);
			fclose($df);
			$csv = ob_get_clean();
			$csvfile = wp_upload_bits( uniqid().'.csv', null, $csv );
			$attachment = $csvfile['file'];
		}

		//dump($recipients);		
		if(wp_mail($recipients, $subject, $message, $headers, $attachment )){
			// kill attachment.
			if(!empty($attachment)){
				if(file_exists($attachment)){
					unlink($attachment);
				}
			}
		}

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
		
		$this->screen_prefix = add_menu_page( 'Caldera Forms', 'Caldera Forms', 'manage_options', $this->plugin_slug, array( $this, 'render_admin' ), 'dashicons-list-view', 76 );

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

			add_action( 'admin_footer', function(){
				include CFCORE_PATH . 'ui/insert_shortcode.php';
			} );	

		}
		if( $screen->base !== $this->screen_prefix){
			return;
		}

		wp_enqueue_media();

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
	 * Save users meta groups
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
				//dump($data);

				// update form
				do_action('caldera_forms_save_form', $data);
				update_option($data['ID'], $data);

				do_action('caldera_forms_save_form_register', $data);
				update_option( '_caldera_forms', $forms );

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
			"description" 	=> $newform['description']
		);

		// add from to list
		$forms[$newform['ID']] = $newform;
		update_option( '_caldera_forms', $forms );

		// add form to db
		update_option($newform['ID'], $newform);
		
		echo $newform['ID'];
		exit;


	}

	public static function send_auto_response($data, $config, $original_data){
		

		$headers = 'From: ' . $config['sender_name'] . ' <' . $config['sender_email'] . '>' . "\r\n";
		//$headers .= "Content-type: text/html\r\n";

		$message = $config['message'];
		foreach ($config as $key => $value) {
			if($key == 'message'){
				continue;
			}
			if(isset($data[$value])){
				$value = $data[$value];
			}else{
				$value = null;
			}
			$message = str_replace('%'.$key.'%', $value, $message);
		}

		foreach ($data as $key => $value) {
			if($key == 'message'){
				continue;
			}
			if(is_array($value)){
				$value = implode(', ', $value);
			}
			$message = str_replace('%'.$key.'%', $value, $message);
		}

		wp_mail($data[$config['recipient_name']].' <'.$data[$config['recipient_email']].'>', $config['subject'], $message, $headers );

		return $data;
	}


	// get built in form processors
	public function get_form_processors($processors){
		$internal_processors = array(
			'auto_responder' => array(
				"name"				=>	__('Auto Responder', 'caldera-forms'),
				"description"		=>	__("Sends out an auto response e-mail", 'caldera-forms'),
				"post_processor"	=>	array($this, 'send_auto_response'),
				"template"			=>	CFCORE_PATH . "processors/auto_responder/config.php",
				"default"			=>	array(
					'subject'		=>	__('Thank you for contacting us', 'caldera-forms')
				),
			),
		);

		return array_merge( $processors, $internal_processors );

	}

	// get built in field types
	public function get_field_types($fields){


		$internal_fields = array(
			'text' => array(
				"field"		=>	"Single Line Text",
				"description" => __('Single Line Text', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/text/field.php",
				"category"	=>	__("Text Fields", "cladera-forms"),
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/text/preview.php"
				)
			),
			'html' => array(
				"field"		=>	"HTML",
				"description" => __('HTML', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/html/field.php",
				"category"	=>	__("Content", "cladera-forms"),
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/html/preview.php",
					"template"	=>	CFCORE_PATH . "fields/html/config_template.php",
					"not_supported"	=>	array(
						'hide_label',
						'caption',
						'required'
					)
				)
			),
			'hidden' => array(
				"field"		=>	"Hidden",
				"description" => __('Hidden', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/hidden/field.php",
				"category"	=>	__("Text Fields", "cladera-forms"),
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/hidden/preview.php",
					"not_supported"	=>	array(
						'hide_label',
						'caption',
						'required',

					)
				)
			),
			'button' => array(
				"field"		=>	"Button",
				"description" => __('Button', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/button/field.php",
				"category"	=>	__("Buttons", "cladera-forms"),
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/button/config_template.html",
					"preview"	=>	CFCORE_PATH . "fields/button/preview.php",
					"default"	=> array(
						'class'	=>	'btn btn-primary',
						'type'	=>	'button'
					),
					"not_supported"	=>	array(
						'hide_label',
						'caption',
						'required',
						'entry_list'
					)
				)
			),
			'email' => array(
				"field"		=>	"Email Address",
				"description" => __('Email Address', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/email/field.php",
				"category"	=>	__("Text Fields", "cladera-forms"),
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/email/preview.php"
				)
			),
			'paragraph' => array(
				"field"		=>	"Paragraph Textarea",
				"description" => __('Paragraph Textarea', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/paragraph/field.php",
				"category"	=>	__("Text Fields", "cladera-forms"),
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/paragraph/config_template.html",
					"preview"	=>	CFCORE_PATH . "fields/paragraph/preview.php",
					"default"	=> array(
						'rows'	=>	'4'
					),
				)
			),
			'toggle_switch' => array(
				"field"		=>	"Toggle Switch",
				"description" => __('Toggle Switch', 'caldera-forms'),
				"category"	=>	__("Select Options", "cladera-forms"),
				"file"		=>	CFCORE_PATH . "fields/toggle_switch/field.php",
				"options"	=>	"single",
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/toggle_switch/config_template.html",
					"preview"	=>	CFCORE_PATH . "fields/toggle_switch/preview.php",
					"default"	=> array(
					),
					"scripts"	=>	array(
						CFCORE_URL . "fields/toggle_switch/js/setup.js"
					),
					"styles"	=>	array(
						CFCORE_URL . "fields/toggle_switch/css/setup.css"
					),
				),
				"scripts"	=>	array(
					"jquery",
					CFCORE_URL . "fields/toggle_switch/js/toggle.js"
				),
				"styles"	=>	array(
					CFCORE_URL . "fields/toggle_switch/css/toggle.css"
				)
			),
			'dropdown' => array(
				"field"		=>	"Dropdown Select",
				"description" => __('Dropdown Select', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/dropdown/field.php",
				"category"	=>	__("Select Options", "cladera-forms"),
				"options"	=>	"single",
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/dropdown/config_template.html",
					"preview"	=>	CFCORE_PATH . "fields/dropdown/preview.php",
					"default"	=> array(

					),
					"scripts"	=>	array(
						CFCORE_URL . "fields/dropdown/js/setup.js"
					)
				)
			),
			'checkbox' => array(
				"field"		=>	"Checkbox",
				"description" => __('Checkbox', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/checkbox/field.php",
				"category"	=>	__("Select Options", "cladera-forms"),
				"options"	=>	"multiple",
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/checkbox/preview.php",
					"template"	=>	CFCORE_PATH . "fields/checkbox/config_template.html",
					"default"	=> array(

					),
					"scripts"	=>	array(
						CFCORE_URL . "fields/checkbox/js/setup.js"
					)
				),
			),
			'radio' => array(
				"field"		=>	"Radio",
				"description" => __('Radio', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/radio/field.php",
				"category"	=>	__("Select Options", "cladera-forms"),
				"options"	=>	true,
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/radio/preview.php",
					"template"	=>	CFCORE_PATH . "fields/radio/config_template.html",
					"default"	=> array(
					),
					"scripts"	=>	array(
						CFCORE_URL . "fields/radio/js/setup.js"
					)
				)
			),
			'date_picker' => array(
				"field"		=>	"Date Picker",
				"description" => __('Date Picker', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/date_picker/datepicker.php",
				"category"	=>	__("Text Fields", "cladera-forms"),
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/date_picker/preview.php",
					"template"	=>	CFCORE_PATH . "fields/date_picker/setup.html",
					"default"	=> array(
						'format'	=>	'yyyy-mm-dd'
					),
					"scripts"	=>	array(
						CFCORE_URL . "fields/date_picker/js/bootstrap-datepicker.js",
						CFCORE_URL . "fields/date_picker/js/setup.js"
					),
					"styles"	=>	array(
						CFCORE_URL . "fields/date_picker/css/datepicker.css"
					),
				),
				"scripts"	=>	array(
					"jquery",
					CFCORE_URL . "fields/date_picker/js/bootstrap-datepicker.js"
				),
				"styles"	=>	array(
					CFCORE_URL . "fields/date_picker/css/datepicker.css"
				)
			),
			'color_picker' => array(
				"field"		=>	"Color Picker",
				"description" => __('Color Picker', 'caldera-forms'),
				"category"	=>	__("Text Fields", "cladera-forms"),
				"file"		=>	CFCORE_PATH . "fields/color_picker/field.php",
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/color_picker/preview.php",
					"template"	=>	CFCORE_PATH . "fields/color_picker/setup.html",
					"default"	=> array(
						'default'	=>	'#FFFFFF'
					),
					"scripts"	=>	array(
						CFCORE_URL . "fields/color_picker/minicolors.js",
						CFCORE_URL . "fields/color_picker/setup.js"
					),
					"styles"	=>	array(
						CFCORE_URL . "fields/color_picker/minicolors.css"
					),
				),
				"scripts"	=>	array(
					"jquery",
					CFCORE_URL . "fields/color_picker/minicolors.js",
					CFCORE_URL . "fields/color_picker/setup.js"
				),
				"styles"	=>	array(
					CFCORE_URL . "fields/color_picker/minicolors.css"
				)
			)
		);
		
		return array_merge( $fields, $internal_fields );
		
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
											'label'	=> '> 767px'
										),
										"md"	=> array(
											'value'	=> 'md',
											'label'	=> '> 991px'
										),
										"lg"	=> array(
											'value'	=> 'lg',
											'label'	=> '> 1199px'
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
	// FRONT END STUFFF

	static public function check_user_profile_shortcode(){
		global $post, $front_templates, $wp_query, $processID, $form;

		//HOOK IN post
		
		if(isset($_POST['_cf_verify']) && isset( $_POST['_cf_frm'] )){
			if(wp_verify_nonce( $_POST['_cf_verify'], 'caldera_forms_front' )){
		
				$referrer = parse_url( $_POST['_wp_http_referer'] );
				if(!empty($referrer['query'])){
					parse_str($referrer['query'], $referrer['query']);
					if(isset($referrer['query']['cf_er'])){
						unset($referrer['query']['cf_er']);
					}
					if(isset($referrer['query']['cf_su'])){
						unset($referrer['query']['cf_su']);
					}
				}
				$form = get_option( $_POST['_cf_frm'] );
				if(empty($form['ID']) || $form['ID'] != $_POST['_cf_frm']){
					return;
				}

				unset($_POST['_wp_http_referer']);

				// unset stuff
				unset($_POST['_cf_frm']);
				unset($_POST['_cf_verify']);
				
				// SET process ID
				$processID = uniqid('_cf_process_');

				// get data ready
				$process_data = $data = stripslashes_deep( $_POST );

				// init filter
				$form = apply_filters('caldera_forms_submit_get_form', $form, $process_data, $referrer, $processID);
				// start action
				do_action('caldera_forms_submit_start', $data, $form, $referrer, $processID);

				// requireds
				// set transient for returns submittions
				$transdata = array(
					'transient' => $processID,
					'expire'	=> 120, 
					'data' 		=> $data
				);

				$transdata = apply_filters('caldera_forms_submit_transient', $transdata, $process_data, $form, $referrer, $processID);

				// setup processor bound requieds
				if(!empty($form['processors'])){
					$bound_fields = array(); 
					foreach($form['processors'] as $processor_id=>$processor){
						if(!empty($processor['config'])){
							foreach($processor['config'] as $slug=>&$value){
								$bound_fields = array_merge($bound_fields, self::search_array_fields($value, array_keys( $form['fields'])) );
							}
						}
					}
					foreach($bound_fields as $bound){
						$form['fields'][$bound]['required'] = 1;
					}
				}

				foreach($form['fields'] as $field){
					
					$failed = false;
					if(!empty($field['required'])){
						if(isset($data[$field['slug']])){
							if(is_array($data[$field['slug']])){
								if(count($data[$field['slug']]) <= 0){
									$failed = true;
								}
							}else{
								if(strlen($data[$field['slug']]) < 1){
									$failed = true;
								}								
							}
						}else{
							$failed = true;
						}
						if($failed === true){
							$transdata['fields'][$field['slug']] = $field['slug'] .' ' .__('is required', 'caldera-forms');
						}
					}
				}
				// check requireds
				if(!empty($transdata['fields'])){
					$transdata['type'] = 'error';
					// set error transient
					$transdata = apply_filters('caldera_forms_submit_error_transient', $transdata, $process_data, $form, $referrer, $processID);
					$transdata = apply_filters('caldera_forms_submit_error_transient_required', $transdata, $process_data, $form, $referrer, $processID);
					
					set_transient( $processID, $transdata, $transdata['expire']);
					
					// back to form
					$query_str = array(
						'cf_er' => $processID
					);
					if(!empty($referrer['query'])){
						$query_str = array_merge($referrer['query'], $query_str);
					}
					$referrer = $referrer['path'] . '?' . http_build_query($query_str);
					$referrer = apply_filters('caldera_forms_submit_error_redirect', $referrer, $process_data, $form, $processID);
					$referrer = apply_filters('caldera_forms_submit_error_redirect_required', $referrer, $process_data, $form, $processID);

					wp_redirect( $referrer );
					exit;

				}


				// has processors
				do_action('caldera_forms_submit_start_processors', $process_data, $form, $referrer, $processID);
				if(!empty($form['processors'])){
					
					// get all form processors
					$form_processors = apply_filters('caldera_forms_get_form_processors', array() );
					do_action('caldera_forms_submit_pre_process', $process_data, $form, $referrer, $processID);
					foreach($form['processors'] as $processor_id=>$processor){
						if(isset($form_processors[$processor['type']])){
							// has processor
							$process = $form_processors[$processor['type']];
							if(!isset($process['pre_processor'])){
								continue;
							}
							// set default config
							$config = array();
							if(isset($process['default'])){
								$config = $process['default'];
							}
							if(!empty($processor['config'])){

								// reset bindings
								foreach($processor['config'] as $slug=>&$value){
									if(!is_array($value)){
										// reset binding
										if(isset($form['fields'][$value])){
											$value = $form['fields'][$value]['slug'];
										}
									}
								}
								$config = array_merge($config, $processor['config']);
							}
							if(is_array($process['pre_processor'])){
								$process_line_data = $process['pre_processor'][0]::$process['pre_processor'][1]($process_data, $config, $data);
							}else{
								if(function_exists($process['pre_processor'])){
									$func = $process['pre_processor'];
									$process_line_data = $func($process_data, $config, $data, $form);	
								}
							}
							if(false === $process_line_data){
								// return an error since a processor killed it!
								return;
							}elseif(!empty($process_line_data)){
								if(isset($process_line_data['_fail'])){
									
									//type
									if(!empty($process_line_data['_fail']['type'])){
										$transdata['type'] = $process_line_data['_fail']['type'];
										// has note?
										if(!empty($process_line_data['_fail']['note'])){
											$transdata['note'] = $process_line_data['_fail']['note'];
										}																						
									}

									// fields involved?
									if(!empty($process_line_data['_fail']['fields'])){
										$transdata['fields'] = $process_line_data['_fail']['fields'];
									}
				
									// set error transient
									$transdata = apply_filters('caldera_forms_submit_error_transient', $transdata, $process_data, $form, $referrer, $processID);
									$transdata = apply_filters('caldera_forms_submit_error_transient_pre_process', $transdata, $process_data, $form, $referrer, $processID);

									set_transient( $processID, $transdata, $transdata['expire']);

									// back to form
									$query_str = array(
										'cf_er' => $processID
									);
									if(!empty($referrer['query'])){
										$query_str = array_merge($referrer['query'], $query_str);
									}
									$referrer = $referrer['path'] . '?' . http_build_query($query_str);
									$referrer = apply_filters('caldera_forms_submit_error_redirect', $referrer, $process_data, $form, $processID);
									$referrer = apply_filters('caldera_forms_submit_error_redirect_pre_process', $referrer, $process_data, $form, $processID);
									wp_redirect( $referrer );
									exit;
								}
								// processor returned data, use this instead
								$process_data = $process_line_data;
							}
						}
					}
					$process_data = apply_filters('caldera_forms_submit_pre_process', $process_data, $form, $referrer, $processID);
					/// AFTER PRE-PROCESS - check for errors etc to return else continue to process.

					do_action('caldera_forms_submit_process', $process_data, $form, $referrer, $processID);
					/// PROCESS
					foreach($form['processors'] as $processor_id=>$processor){
						if(isset($form_processors[$processor['type']])){
							// has processor
							$process = $form_processors[$processor['type']];
							if(!isset($process['processor'])){
								continue;
							}

							// set default config
							$config = array();
							if(isset($process['default'])){
								$config = $process['default'];
							}
							if(!empty($processor['config'])){

								// reset bindings
								foreach($processor['config'] as $slug=>&$value){
									if(!is_array($value)){
										// reset binding
										if(isset($form['fields'][$value])){
											$value = $form['fields'][$value]['slug'];
										}
									}
								}
								$config = array_merge($config, $processor['config']);
							}
							if(is_array($process['processor'])){
								$process_line_data = $process['processor'][0]::$process['processor'][1]($process_data, $config, $data);
							}else{
								if(function_exists($process['processor'])){
									$func = $process['processor'];
									$process_line_data = $func($process_data, $config, $data, $form);	
								}
							}
							if(!empty($process_line_data)){
								// processor returned data, use this instead
								$process_data = $process_line_data;
							}
						}
					}
					$process_data = apply_filters('caldera_forms_submit_process', $process_data, $form, $referrer, $processID);
					// AFTER PROCESS - do post process for any additional stuff

					do_action('caldera_forms_submit_post_process', $process_data, $form, $referrer, $processID);
					// POST PROCESS
					foreach($form['processors'] as $processor_id=>$processor){
						if(isset($form_processors[$processor['type']])){
							// has processor
							$process = $form_processors[$processor['type']];
							if(!isset($process['post_processor'])){
								continue;
							}								
							// set default config
							$config = array();
							if(isset($process['default'])){
								$config = $process['default'];
							}
							if(!empty($processor['config'])){

								// reset bindings
								foreach($processor['config'] as $slug=>&$value){
									if(!is_array($value)){
										// reset binding
										if(isset($form['fields'][$value])){
											$value = $form['fields'][$value]['slug'];
										}
									}
								}
								$config = array_merge($config, $processor['config']);
							}
							if(is_array($process['post_processor'])){
								$process_line_data = $process['post_processor'][0]::$process['post_processor'][1]($process_data, $config, $data);
							}else{
								if(function_exists($process['post_processor'])){
									$func = $process['post_processor'];
									$process_line_data = $func($process_data, $config, $data, $form);	
								}
							}
							if(false === $process_line_data){
								// return an error since a processor killed it!
								return;
							}elseif(!empty($process_line_data)){
								// processor returned data, use this instead
								$process_data = $process_line_data;
							}
						}
					}
					$process_data = apply_filters('caldera_forms_submit_post_process', $process_data, $form, $referrer, $processID);
				}
				
				// done do action.
				do_action('caldera_forms_submit_complete', $process_data, $form, $referrer, $processID);

				// redirect back or to result page
				$referrer['query']['cf_su'] = 1;
				$referrer = $referrer['path'] . '?' . http_build_query($referrer['query']);

				// done do action.
				do_action('caldera_forms_submit_redirect', $data, $form, $referrer, $processID);
				// filter refer
				$referrer = apply_filters('caldera_forms_submit_redirect', $referrer, $process_data, $form, $processID);
				$referrer = apply_filters('caldera_forms_submit_redirect_complete', $referrer, $process_data, $form, $processID);

				wp_redirect( $referrer );
				exit;


			}


			/// end form and redirect to submit page or result page.
		}
		if(empty($post)){
			if(isset($wp_query->queried_object)){
				$post = $wp_query->queried_object;
			}
		}
		if(empty($post)){
			//cant find form;
			return;
		}

		// get fields

		$field_types = apply_filters('caldera_forms_get_field_types', array() );

		foreach($field_types as $field_type){
			//enqueue styles
			if( !empty( $field_type['styles'])){
				foreach($field_type['styles'] as $style){
					if(filter_var($style, FILTER_VALIDATE_URL)){
						wp_enqueue_style( 'cf-' . sanitize_key( basename( $style ) ), $style, array(), self::VERSION );
					}else{
						wp_enqueue_style( $style );
					}
				}
			}

			//enqueue scripts
			if( !empty( $field_type['scripts'])){
				// check for jquery deps
				$depts[] = 'jquery';
				foreach($field_type['scripts'] as $script){
					if(filter_var($script, FILTER_VALIDATE_URL)){
						wp_enqueue_script( 'cf-' . sanitize_key( basename( $script ) ), $script, $depts, self::VERSION );
					}else{
						wp_enqueue_script( $script );
					}
				}
			}
		}
		// if depts been set- scripts are used - 
		wp_enqueue_script( 'cf-frontend-script-init', CFCORE_URL . 'assets/js/frontend-script-init.js', array('jquery'), self::VERSION, true);

		//if(isset($form['settings']['styles']['use_grid'])){
		//	if($form['settings']['styles']['use_grid'] === 'yes'){
				wp_enqueue_style( 'cf-grid-styles', CFCORE_URL . 'assets/css/caldera-grid.css', array(), self::VERSION );
		//	}
		//}
		//if(isset($form['settings']['styles']['use_form'])){
		//	if($form['settings']['styles']['use_form'] === 'yes'){
				//wp_enqueue_style( 'cf-form-styles', CFCORE_URL . 'assets/css/caldera-form.css', array(), self::VERSION );
		//	}
		//}
		//if(isset($form['settings']['styles']['use_alerts'])){
		//	if($form['settings']['styles']['use_alerts'] === 'yes'){
				wp_enqueue_style( 'cf-alert-styles', CFCORE_URL . 'assets/css/caldera-alert.css', array(), self::VERSION );
		//	}
		//}




		$codes = get_shortcode_regex();
		preg_match_all('/' . $codes . '/s', $post->post_content, $found);
		if(!empty($found[0][0])){
			foreach($found[2] as $index=>$code){
				if( 'caldera_form' === $code ){
					if(!empty($found[3][$index])){
						$atts = shortcode_parse_atts($found[3][$index]);
						if(isset($atts['id'])){
							// has form get  stuff for it
							$form = get_option( $atts['id'] );
							if(!empty($form)){
								// get list of used fields
								if(empty($form['fields'])){
									/// no filds - next form
								}

								// has a form - get field type
								if(!isset($field_types)){
									$field_types = apply_filters('caldera_forms_get_field_types', array() );
								}


								foreach($form['fields'] as $field){
									//enqueue styles
									if( !empty( $field_types[$field['type']]['styles'])){
										foreach($field_types[$field['type']]['styles'] as $style){
											if(filter_var($style, FILTER_VALIDATE_URL)){
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

								// if depts been set- scripts are used - 
								wp_enqueue_script( 'cf-frontend-script-init', CFCORE_URL . 'assets/js/frontend-script-init.js', array('jquery'), self::VERSION, true);

								if(isset($form['settings']['styles']['use_grid'])){
									if($form['settings']['styles']['use_grid'] === 'yes'){
										wp_enqueue_style( 'cf-grid-styles', CFCORE_URL . 'assets/css/caldera-grid.css', array(), self::VERSION );
									}
								}
								if(isset($form['settings']['styles']['use_form'])){
									if($form['settings']['styles']['use_form'] === 'yes'){
										wp_enqueue_style( 'cf-form-styles', CFCORE_URL . 'assets/css/caldera-form.css', array(), self::VERSION );
									}
								}
								if(isset($form['settings']['styles']['use_alerts'])){
									if($form['settings']['styles']['use_alerts'] === 'yes'){
										wp_enqueue_style( 'cf-alert-styles', CFCORE_URL . 'assets/css/caldera-alert.css', array(), self::VERSION );
									}
								}
								
							}
						}
					}
				}
			}
		}
	}


	static function search_array_fields($needle, $haystack, $found = array()){

		//dump($haystack);
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
	
	static public function get_entry($entry_id){
		
		global $wpdb;

		$rawdata = $wpdb->get_results($wpdb->prepare("
			SELECT
				`entry`.`form_id` AS `_form_id`,
				`entry`.`datestamp` AS `_date_submitted`,
				`entry`.`user_id` AS `_user_id`,
				`value`.*

			FROM `" . $wpdb->prefix ."cf_form_entries` AS `entry`
			LEFT JOIN `" . $wpdb->prefix ."cf_form_entry_values` AS `value` ON (`entry`.`id` = `value`.`entry_id`)
			WHERE `entry`.`id` = %d;", $entry_id ));

		if(empty($rawdata)){
			return array();
		}
		$data = array();
		foreach($rawdata as $row){
			$data[$row->slug] = $row->value;
		}

		return $data;
	}

	static public function render_form($atts, $entry_id = null){

		if(empty($atts)){
			return;
		}

		if(is_string($atts)){
			$atts = array( 'id' => $atts);
		}

		if(empty($atts['id'])){
			return;
		}

		$form = get_option( $atts['id'] );
		if(empty($form)){
			return;
		}

		$form = apply_filters('caldera_forms_render_get_form', $form );

		$field_types = apply_filters('caldera_forms_get_field_types', array() );

		do_action('caldera_forms_render_start', $form);

		include_once CFCORE_PATH . "classes/caldera-grid.php";

		$gridsize = 'sm';
		if(!empty($form['settings']['responsive']['break_point'])){
			$gridsize = $form['settings']['responsive']['break_point'];
		}
		$gridsize = apply_filters('caldera_forms_render_set_grid_size', $gridsize );

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
		$grid_settings = apply_filters('caldera_forms_render_grid_settings', $grid_settings, $form);

		$grid = new Caldera_Form_Grid($grid_settings);

		$grid->setLayout($form['layout_grid']['structure']);

		// setup notcies
		$notices = array();
		$note_general_classes = array(
			'alert'
		);
		$note_general_classes = apply_filters('caldera_forms_render_note_general_classes', $note_general_classes, $form);

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
		
		$note_classes = apply_filters('caldera_forms_render_note_classes', $note_classes, $form);

		$field_errors = array();
		
		// check for prev post
		$prev_data = false;
		
		// load requested data
		if(!empty($entry_id)){
			$prev_data = Caldera_Forms::get_entry($entry_id);
		}


		if(!empty($_GET['cf_er'])){
			$prev_post = get_transient( $_GET['cf_er'] );
			if(!empty($prev_post['transient'])){
				
				if($prev_post['transient'] === $_GET['cf_er']){
					$prev_data = $prev_post['data'];
				}
				if(!empty($prev_post['type']) && !empty($prev_post['note'])){
					$notices[$prev_post['type']]['note'] = $prev_post['note'];
				}
				if(!empty($prev_post['fields'])){
					$field_errors = $prev_post['fields'];
				}
			}
			// filter transient
			$prev_post = apply_filters('caldera_forms_render_get_transient', $prev_post, $form);

		}
		if(!empty($_GET['cf_su'])){
			if(empty($notices['success']['note'])){
				$notices['success']['note'] = __('Form has successfuly been submitted.', 'caldera-forms');
			}
		}

		// setup processor bound requieds
		if(!empty($form['processors'])){
			$bound_fields = array(); 
			foreach($form['processors'] as $processor_id=>$processor){
				if(!empty($processor['config'])){
					foreach($processor['config'] as $slug=>&$value){
						$bound_fields = array_merge($bound_fields, self::search_array_fields($value, array_keys( $form['fields'])) );
					}
				}
			}
			foreach($bound_fields as $bound){
				$form['fields'][$bound]['required'] = 1;
			}
		}


		if(!empty($form['layout_grid']['fields'])){

			foreach($form['layout_grid']['fields'] as $field_id=>$location){

				if(isset($form['fields'][$field_id])){
					$field = $form['fields'][$field_id];

					if(!isset($field_types[$field['type']]['file']) || !file_exists($field_types[$field['type']]['file'])){
						continue;
					}
					
					$field_classes = array(
						"control_wrapper"	=> "form-group",
						"field_label"		=> "control-label",
						"field_required_tag"=> "field_required",
						"field_wrapper"		=> "",
						"field"				=> "form-control",
						"field_caption"		=> "help-block",
						"field_error"		=> "has-error",
					);

					$field_classes = apply_filters('caldera_forms_render_field_classes', $field_classes, $form);
					$field_classes = apply_filters('caldera_forms_render_field_classes_type-' . $field['type'], $field_classes, $form);
					$field_classes = apply_filters('caldera_forms_render_field_classes_slug-' . $field['slug'], $field_classes, $form);

					$field = apply_filters('caldera_forms_render_get_field', $field, $form);
					$field = apply_filters('caldera_forms_render_get_field_type-' . $field['type'], $field, $form);
					$field = apply_filters('caldera_forms_render_get_field_slug-' . $field['slug'], $field, $form);

					$field_structure = array(
						"id"				=>	'fld_' . $field['slug'],
						"name"				=>	$field['slug'],
						"label_before"		=>	( empty($field['hide_label']) ? "<label for=\"" . $field_id . "\" class=\"" . $field_classes['field_label'] . "\">" : null ),
						"label"				=>	( empty($field['hide_label']) ? $field['label'] : null ),
						"label_required"	=>	( empty($field['hide_label']) ? ( !empty($field['required']) ? " <span class=\"" . $field_classes['field_required_tag'] . "\" style=\"color:#ff2222;\">*</span>" : "" ) : null ),
						"label_after"		=>	( empty($field['hide_label']) ? "</label>" : null ),
						"field_placeholder" =>	( !empty($field['hide_label']) ? 'placeholder="' . htmlentities( $field['label'] ) .'"' : null),
						"field_required"	=>	( !empty($field['required']) ? 'required="required"' : null),
						"field_value"		=>	null,
						"field_caption"		=>	( !empty($field['caption']) ? "<span class=\"" . $field_classes['field_caption'] . "\">" . $field['caption'] . "</span>\r\n" : ""),
					);

					$field_wrapper_class = $field_classes['control_wrapper'];
					$field_input_class = $field_classes['field_wrapper'];
					$field_class = $field_classes['field'];

					if(!empty($field_errors[$field['slug']])){
						$field_input_class .= " " . $field_classes['field_error'];
						$field_structure['field_caption'] = "<span class=\"" . $field_classes['field_caption'] . "\">" . $field_errors[$field['slug']] . "</span>\r\n";
					}
					
					// value
					if(isset($field['config']['default'])){
						$field_structure['field_value'] = $field['config']['default'];
					}

					// transient data
					if(isset($prev_data[$field['slug']])){
						$field_structure['field_value'] = $prev_data[$field['slug']];
					}

					$field_structure = apply_filters('caldera_forms_render_field_structure', $field_structure, $form);
					$field_structure = apply_filters('caldera_forms_render_field_structure_type-' . $field['type'], $field_structure, $form);
					$field_structure = apply_filters('caldera_forms_render_field_structure_slug-' . $field['slug'], $field_structure, $form);

					$field_name = $field_structure['name'];
					$field_id = $field_structure['id'];
					$field_label = $field_structure['label_before'] . $field_structure['label'] . $field_structure['label_required'] . $field_structure['label_after']."\r\n";
					$field_placeholder = $field_structure['field_placeholder'];
					$field_required = $field_structure['field_required'];
					$field_caption = $field_structure['field_caption'];
					// blank default
					$field_value = $field_structure['field_value'];

					ob_start();
					include $field_types[$field['type']]['file'];
					$field_html = apply_filters('caldera_forms_render_field', ob_get_clean(), $form);
					$field_html = apply_filters('caldera_forms_render_field_type-' . $field['type'], $field_html, $form);
					$field_html = apply_filters('caldera_forms_render_field_slug-' . $field['slug'], $field_html, $form);

					$grid->append($field_html, $location);
					
				}
			}
		}
		//
		$grid = apply_filters('caldera_forms_render_grid_structure', $grid, $form);

		$out = "<div class=\"caldera-grid\">\r\n";
		
		$notices = apply_filters('caldera_forms_render_notices', $notices, $form);

		if(!empty($notices)){
			// do notices
			foreach($notices as $note_type => $notice){
				if(!empty($notice['note'])){					
					$out .= '<div class=" '. implode(' ', $note_classes[$note_type]) . '">' . $notice['note'] .'</div>';
				}
			}

		}

		if(empty($notices['success'])){

			$form_classes = array(
				'caldera_forms_form'
			);
			$form_classes = apply_filters('caldera_forms_render_form_classes', $form_classes, $form);

			// render only non success
			$out .= "<form class=\"" . implode(' ', $form_classes) . "\" method=\"POST\" role=\"form\">\r\n";
			$out .= wp_nonce_field( "caldera_forms_front", "_cf_verify", true, false);
			$out .= "<input type=\"hidden\" name=\"_cf_frm\" value=\"" . $atts['id'] . "\">\r\n";
			$out .= $grid->renderLayout();
			$out .= "</form>\r\n";
		}
		
		$out .= "</div>\r\n";
		
		do_action('caldera_forms_render_end', $form);

		return $out;

	}

}


if(!function_exists('dump')){
	function dump($a, $d=1){
		echo '<pre>';
		print_r($a);
		echo '</pre>';
		if($d){
			die;
		}
	}
}

