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


		if(is_admin()){
			add_action( 'wp_loaded', array( $this, 'save_form') );
		}else{
			// find if profile is loaded
			add_action('wp', array( $this, 'check_user_profile_shortcode'));

			// template render
			add_shortcode( 'caldera_form', array( $this, 'render_form') );
		}

		//add_action('wp_footer', array( $this, 'footer_scripts' ) );
		add_action("wp_ajax_create_form", array( $this, 'create_form') );


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
			echo "<form method=\"post\" action=\"admin.php?page=" . $this->plugin_slug . "\" class=\"caldera-forms-options-form\">\r\n";
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
				update_option($data['ID'], $data);

				update_option( '_caldera_forms', $forms );

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

	public static function process_form_email($data, $config, $original_data){

		$headers = 'From: ' . $data[$config['sender']] . ' <' . $data[$config['sender_email']] . '>' . "\r\n";
		wp_mail($config['recipient'], $config['subject'], $data[$config['message']], $headers );

		return $data;
	}


	// get built in form processors
	public function get_form_processors($processors){
		$internal_processors = array(
			'form_emailer' => array(
				"name"				=>	__('Form Emailer', 'caldera-forms'),
				"description"		=>	__("Sends Form results via Email", 'caldera-forms'),
				"post_processor"	=>	array($this, 'process_form_email'),
				"template"			=>	CFCORE_PATH . "processors/form_emailer/config.php",
				"default"			=>	array(
					'recipient'		=>	"",
					'subject'		=>	__('Caldera Form Email', 'caldera-forms')
				),
			),
		);

		return array_merge( $processors, $internal_processors );

	}

	// get built in field types
	public function get_field_types($fields){


		$internal_fields = array(
			'single_line_field' => array(
				"field"		=>	"Single Line Field",
				"file"		=>	CFCORE_PATH . "fields/single_text_field/field.php"
			),
			'paragraph' => array(
				"field"		=>	"Paragraph Textarea",
				"file"		=>	CFCORE_PATH . "fields/paragraph/field.php"
			),
			/*'range_slider' => array(
				"field"		=>	"Range Slider",
				"file"		=>	CFCORE_PATH . "fields/range_slider/field.php",
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/range_slider/config_template.html",
					"default"	=> array(
						"default"	=>	"1",
						"suffix"	=>	"",
						"min"		=>	"0",
						"max"		=>	"10",
					),
					"scripts"	=>	array(
						CFCORE_URL . "fields/range_slider/js/range_slider.js",
						CFCORE_URL . "fields/range_slider/js/setup.js"
					),
					"styles"	=>	array(
						CFCORE_URL . "fields/range_slider/css/setup.css",
						CFCORE_URL . "fields/range_slider/css/simple-slider.css"
					),
				),
				"scripts"	=>	array(
					"jquery"
				),
				"styles"	=>	array(
					CFCORE_URL . "fields/range_slider/css/simple-slider.css"
				)
			),*/
			'toggle_switch' => array(
				"field"		=>	"Toggle Switch",
				"file"		=>	CFCORE_PATH . "fields/toggle_switch/field.php",
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/toggle_switch/config_template.html",
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
				"file"		=>	CFCORE_PATH . "fields/dropdown/field.php",
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/dropdown/config_template.html",
					"default"	=> array(

					),
					"scripts"	=>	array(
						CFCORE_URL . "fields/dropdown/js/setup.js"
					)
				)
			),
			'checkbox' => array(
				"field"		=>	"Checkbox",
				"file"		=>	CFCORE_PATH . "fields/checkbox/field.php",
				"setup"		=>	array(
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
				"file"		=>	CFCORE_PATH . "fields/radio/field.php",
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/radio/config_template.html",
					"default"	=> array(
					),
					"scripts"	=>	array(
						CFCORE_URL . "fields/radio/js/setup.js"
					)
				)
			),
			'image_picker' => array(
				"field"		=>	"Image Picker",
				"file"		=>	CFCORE_PATH . "fields/image_picker/field.php",
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/image_picker/setup.html",
					"default"	=> array(
						'picker' => 'image-thumb'						
					),
					"scripts"	=>	array(
						CFCORE_URL . "fields/image_picker/js/setup.js",
						CFCORE_URL . "fields/image_picker/js/admin.js"
					),
					"styles"	=>	array(
						CFCORE_URL . "fields/image_picker/css/style.css"
					),
				),
				"scripts"	=>	array(
					"jquery",
					CFCORE_URL . "fields/image_picker/js/setup.js",
					"admin" => array(
						CFCORE_URL . "fields/image_picker/js/admin.js"
					),
					"front" => array(
						CFCORE_URL . "fields/image_picker/js/front.js"
					)
				),
				"styles"	=>	array(
					CFCORE_URL . "fields/image_picker/css/style.css"
				)
			),
			'date_picker' => array(
				"field"		=>	"Date Picker",
				"file"		=>	CFCORE_PATH . "fields/date_picker/datepicker.php",
				"setup"		=>	array(
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
				"file"		=>	CFCORE_PATH . "fields/color_picker/field.php",
				"setup"		=>	array(
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
					"grid_settings" => array(
						"name" => __("Grid", 'caldera-forms'),
						"location" => "lower",
						"label" => __("Grid Settings", 'caldera-forms'),
						"repeat" => 0,
						"fields" => array(
							"use_stylesheet" => array(
								"label" => __("Use Stylesheet", 'caldera-forms'),
								"slug" => "use_stylesheet",
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
							"first" => array(
								"label" => __("First Row Class", 'caldera-forms'),
								"slug" => "first",
								"caption" => __("Class name to be added to the first row of the grid", 'caldera-forms'),
								"type" => "single_line_field",
								"config" => array(
									"default" => "first_row",
								),
							),
							"last" => array(
								"label" => __("Last Row Class", 'caldera-forms'),
								"slug" => "last",
								"caption" => __("Class name to be added to the last row of the grid", 'caldera-forms'),
								"type" => "single_line_field",
								"config" => array(
									"default" => "last_row",
								),
							),
							"single" => array(
								"label" => __("Single Row Class", 'caldera-forms'),
								"slug" => "single",
								"caption" => __("Class name to be added to a single row of the grid", 'caldera-forms'),
								"type" => "single_line_field",
								"config" => array(
									"default" => "single_row",
								),
							),
							"before" => array(
								"label" => __("Before ", 'caldera-forms'),
								"slug" => "before",
								"caption" => __("Defines the start of the row wrapper", 'caldera-forms'),
								"type" => "single_line_field",
								"config" => array(
									"default" => '<div %1$s class="row %2$s">',
								),
							),
							"after" => array(
								"label" => __("After", 'caldera-forms'),
								"slug" => "after",
								"caption" => __("Defines the end of a row wrapper", 'caldera-forms'),
								"type" => "single_line_field",
								"config" => array(
									"default" => "</div>",
								),
							),
							"column_first" => array(
								"label" => __("First Column Class", 'caldera-forms'),
								"slug" => "column_first",
								"caption" => __("Class name to be added to the first column of the grid", 'caldera-forms'),
								"type" => "single_line_field",
								"config" => array(
									"default" => "first_column",
								),
							),
							"column_last" => array(
								"label" => __("Last Column Class", 'caldera-forms'),
								"slug" => "column_last",
								"caption" => __("Class name to be added to the last column of the grid", 'caldera-forms'),
								"type" => "single_line_field",
								"config" => array(
									"default" => "last_column",
								),
							),
							"column_single" => array(
								"label" => __("Single Column Class", 'caldera-forms'),
								"slug" => "column_single",
								"caption" => __("Class name to be added to a single column of the grid", 'caldera-forms'),
								"type" => "single_line_field",
								"config" => array(
									"default" => "single_column",
								),
							),
							"column_before" => array(
								"label" => __("Before ", 'caldera-forms'),
								"slug" => "column_before",
								"caption" => __("Defines the start of the column wrapper", 'caldera-forms'),
								"type" => "single_line_field",
								"config" => array(
									"default" => '<div %1$s class="col-xs-%2$d %3$s">',
								),
							),
							"column_after" => array(
								"label" => __("After", 'caldera-forms'),
								"slug" => "column_after",
								"caption" => __("Defines the end of a column wrapper", 'caldera-forms'),
								"type" => "single_line_field",
								"config" => array(
									"default" => "</div>",
								),
							),
						),
					),
				),
			),
		);
		
		return array_merge( $panels, $internal_panels );
		
	}
	// FRONT END STUFFF

	static public function check_user_profile_shortcode(){
		global $post, $front_templates, $wp_query;

		//HOOK IN post
		
		if(isset($_POST['_cf_verify']) && isset( $_POST['_cf_frm'] )){
			if(wp_verify_nonce( $_POST['_cf_verify'], 'caldera_forms_front' )){
		
				$referrer = $_POST['_wp_http_referer'];
				unset($_POST['_wp_http_referer']);				

				// check the form exists on the page.
				$codes = get_shortcode_regex();
				preg_match_all('/' . $codes . '/s', $wp_query->queried_object->post_content, $found);
				if(!empty($found[0][0])){
					foreach($found[2] as $index=>$code){
						if( 'caldera_form' === $code ){
							if(!empty($found[3][$index])){
								$atts = shortcode_parse_atts($found[3][$index]);
								if(isset($atts['id'])){
									if($atts['id'] === $_POST['_cf_frm']){
										$form = get_option( $atts['id'] );
									}
								}
							}
						}
					}
					// unset stuff
					unset($_POST['_cf_frm']);
					unset($_POST['_cf_verify']);
					

					// has processors
					if(!empty($form['processors'])){

						// get data ready
						$process_data = $data = stripslashes_deep( $_POST );
						
						// get all form processors
						$form_processors = apply_filters('caldera_forms_get_form_processors', array() );
						
						foreach($form['processors'] as $processor_id=>$processor){
							if(isset($form_processors[$processor['type']])){
								// has processor
								$process = $form_processors[$processor['type']];
								// set default config
								$config = $process['default'];
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
										$process_line_data = $func($process_data, $config, $data);	
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
						/// AFTER PRE-PROCESS - check for errors etc to return else continue to process.


						/// PROCESS
						foreach($form['processors'] as $processor_id=>$processor){
							if(isset($form_processors[$processor['type']])){
								// has processor
								$process = $form_processors[$processor['type']];
								// set default config
								$config = $process['default'];
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
										$process_line_data = $func($process_data, $config, $data);	
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
						// AFTER PROCESS - do post process for any additional stuff


						// POST PROCESS
						foreach($form['processors'] as $processor_id=>$processor){
							if(isset($form_processors[$processor['type']])){
								// has processor
								$process = $form_processors[$processor['type']];
								// set default config
								$config = $process['default'];
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
										$process_line_data = $func($process_data, $config, $data);	
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
					}
				}


				// redirect back or to result page
				wp_redirect( $referrer );
				exit;


			}





			/// end form and redirect to submit page or result page.
		}

		$codes = get_shortcode_regex();
		preg_match_all('/' . $codes . '/s', $wp_query->queried_object->post_content, $found);
		if(!empty($found[0][0])){
			foreach($found[2] as $index=>$code){
				if( 'caldera_form' === $code ){
					if(!empty($found[3][$index])){
						$atts = shortcode_parse_atts($found[3][$index]);
						if(isset($atts['id'])){
							// has form get  stuff for it
							$form = get_option( $atts['id'] );
							if(isset($form['settings']['grid_settings']['use_stylesheet'])){
								if($form['settings']['grid_settings']['use_stylesheet'] === 'yes'){

									wp_enqueue_style( 'cf-grid-styles', CFCORE_URL . 'assets/css/caldera-grid.css', array(), self::VERSION );
									wp_enqueue_style( 'cf-form-styles', CFCORE_URL . 'assets/css/caldera-form.css', array(), self::VERSION );
								
								}
							}
						}
					}
				}
			}
		}
	}

	static public function render_form($atts){

		if(empty($atts['id'])){
			return;
		}

		$form = get_option( $atts['id'] );
		if(empty($form)){
			return;
		}

		$field_types = apply_filters('caldera_forms_get_field_types', array() );

		include_once CFCORE_PATH . "classes/caldera-grid.php";

		$grid = new Caldera_Form_Grid($form['settings']['grid_settings']);

		$grid->setLayout($form['layout_grid']['structure']);

		if(!empty($form['layout_grid']['fields'])){
			foreach($form['layout_grid']['fields'] as $field_id=>$location){

				if(isset($form['fields'][$field_id])){
					$field = $form['fields'][$field_id];

					$field_name = $field['slug'];
					$field_id = 'fld_' . $field['slug'];
					$field_label = "<label for=\"" . $field_id . "\" class=\"control-label\">" . $field['label'] . "</label>\r\n";
					$field_caption = null;
					if(!empty($field['caption'])){
						$field_caption = "<span class=\"help-block\">" . $field['caption'] . "</span>\r\n";
					}
					// blank default
					$field_value = null;

					/*if(isset($field['config']['default'])){
						$field_value = $field['config']['default'];
					}
					if(isset($element['settings'][$panel_slug][$field_slug][$group_index])){
						$field_value = $element['settings'][$panel_slug][$field_slug][$group_index];
					}*/
					
					$field_wrapper_class = "form-group";
					$field_input_class = "";
					$field_class = "form-control";
					
					ob_start();
					include $field_types[$field['type']]['file'];
					$field_html = ob_get_clean();

					$grid->append($field_html, $location);
					//dump($field);
				}
			}
		}

		$out = "<div class=\"caldera-grid\">\r\n";
		$out .= "<form method=\"POST\" role=\"form\">\r\n";
		$out .= wp_nonce_field( "caldera_forms_front", "_cf_verify", true, false);
		$out .= "<input type=\"hidden\" name=\"_cf_frm\" value=\"" . $atts['id'] . "\">\r\n";
		$out .= $grid->renderLayout();
		$out .= "<div class=\"row\">\r\n";
		$out .= "	<div class=\"col-xs-12  single_column\">\r\n";
		$out .= "		<button type=\"submit\" class=\"btn btn-default\">Submit</button>";
		$out .= "	</div>\r\n";
		$out .= "</div>\r\n";
		$out .= "</form>\r\n";
		$out .= "</div>\r\n";

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

