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
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );


		// add element & fields filters
		add_filter('caldera_forms_get_field_types', array( $this, 'get_field_types'));
		add_filter('caldera_forms_get_form_processors', array( $this, 'get_form_processors'));
		add_filter('caldera_forms_submit_redirect_complete', array( $this, 'do_redirect'),10, 4);
		
		// mailser
		add_filter('caldera_forms_mailer', array( $this, 'mail_attachment_check'),10, 3);

		// action
		add_action('caldera_forms_submit_complete', array( $this, 'save_final_form'),1,2);


		add_action("wp_ajax_get_entry", array( $this, 'get_entry') );
		// find if profile is loaded
		add_action('wp', array( $this, 'check_forms_shortcode'));

		// render shortcode
		add_shortcode( 'caldera_form', array( $this, 'render_form') );

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
	


	public static function handle_file_upload($value, $field, $data, $form){

		if(!empty($_FILES[$field['slug']]['size'])){
			// check is allowed 
			if(!empty($field['config']['allowed'])){
				$types = explode(',',$field['config']['allowed']);

				foreach($types as &$type){
					$type=trim($type);
				}
				$check = pathinfo($_FILES[$field['slug']]['name']);
				if(!in_array( $check['extension'], $types)){
					if(count($types) > 1){
						return array('_fail'=>__('File type not allowed. Allowed types are: ', 'caldera-forms') . ' '. implode(', ', $types) );
					}else{
						return array('_fail'=>__('File type needs to be', 'caldera-forms') . ' .' . $types[0] );	
					}
				}

			}


			$upload = wp_upload_bits( $_FILES[$field['slug']]['name'], null, file_get_contents($_FILES[$field['slug']]['tmp_name']) );

			if(empty($upload['error'])){
				return $upload['url'];
			}else{
				return array('_fail'=>$upload['error']);
			}
		}
		return null;
	}


	public static function handle_file_view($value, $field, $form){

		return '<a href="' . $value .'" target="_blank">' . basename($value) .'</a>';

	}
	
	public static function mail_attachment_check($mail, $data, $form){

		// check for 
		foreach($form['fields'] as $field){
			if($field['type'] == 'file' && isset($field['config']['attach'])){


				$dir = wp_upload_dir();
				$file = str_replace($dir['baseurl'], $dir['basedir'], $data[$field['slug']]);
				if(file_exists($file)){
					$mail['attachments'][] = $file;	
				}
				
			}
		}
		return $mail;
	}


	public static function captcha_check($value, $field, $data, $form){

		if(empty($data['recaptcha_response_field'])){
			return array('_fail' => __("The reCAPTCHA field is required.", 'caldera-forms'));
		}
		include_once CFCORE_PATH . 'fields/recaptcha/recaptchalib.php';

		$resp = recaptcha_check_answer ($field['config']['private_key'], $_SERVER["REMOTE_ADDR"], $data['recaptcha_challenge_field'], $data['recaptcha_response_field']);

		if (!$resp->is_valid) {
			return array('_fail' => __("The reCAPTCHA wasn't entered correctly.", 'caldera-forms'));
		}
		return true;
	}



	public static function save_final_form($data, $form){
		if(!empty($form['db_support'])){

			global $wpdb;

			$new_entry = array(
				'form_id'	=>	$form['ID'],
				'user_id'	=>	get_current_user_id(),
				'datestamp' =>	date_i18n( 'Y-m-d H:i:s', time(), 0)
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

		}

		if(empty($form['mailer']['enable_mailer'])){
			return;
		}

		// do mailer!
		$sendername = __('Caldera Forms Notification', 'caldera-forms');
		if(!empty($form['mailer']['sender_name'])){
			$sendername = $form['mailer']['sender_name'];
		}
		if(empty($form['mailer']['sender_email'])){
			$sendermail = get_option( 'admin_email' );
		}else{
			$sendermail = $form['mailer']['sender_email'];
		}

		$mail = array(
			'recipients' => array(),
			'subject'	=> $form['mailer']['email_subject'],
			'message'	=> $form['mailer']['email_message']."\r\n",
			'headers'	=>	array(
				'From: ' . $sendername . ' <' . $sendermail . '>'
			),
			'attachments' => array()
		);

		if($form['mailer']['email_type'] == 'html'){
			$mail['headers'][] = "Content-type: text/html";
		}

		if(!empty($form['mailer']['recipients'])){
			$mail['recipients'] = explode(',', $form['mailer']['recipients']);
		}else{
			$mail['recipients'][] = get_option( 'admin_email' );
		}

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
			$mail['message'] = str_replace('%'.$key.'%', $row, $mail['message']);
			$mail['subject'] = str_replace('%'.$key.'%', $row, $mail['subject']);

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
			$mail['attachments'][] = $csvfile['file'];
		}

		$mail = apply_filters( 'caldera_forms_mailer', $mail, $data, $form);

		if(empty($mail)){
			return;
		}
		// recipients
		foreach($mail['recipients'] as &$recipient){
			// trim spaces
			$recipient = trim($recipient);
		}
		$recipients = implode(',', $mail['recipients']);
		$headers = implode("\r\n", $mail['headers']);		

		do_action( 'caldera_forms_do_mailer', $mail, $data, $form);
		if(!empty($mail)){
			if(wp_mail($recipients, $mail['subject'], $mail['message'], $headers, $mail['attachments'] )){
				// kill attachment.
				if(!empty($csvfile['file'])){
					if(file_exists($csvfile['file'])){
						unlink($csvfile['file']);
					}
				}
			}
		}else{
			if(!empty($csvfile['file'])){
				if(file_exists($csvfile['file'])){
					unlink($csvfile['file']);
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



	public static function do_redirect($referrer, $data, $form, $processid){
		if(isset($form['processors'])){
			foreach($form['processors'] as $processor){
				if($processor['type'] == 'form_redirect'){

					if(isset($processor['conditions']) && !empty($processor['conditions']['type'])){
						if(!self::check_condition($processor['conditions'], $data, $form)){
							continue;
						}
					}

					if(!empty($processor['config']['url'])){
						return $processor['config']['url'];
					}
				}
			}
		}
		return $referrer;
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
			'form_redirect' => array(
				"name"				=>	__('Redirect', 'caldera-forms'),
				"description"		=>	__("Redirects user to URL on successful submit", 'caldera-forms'),
				"template"			=>	CFCORE_PATH . "processors/redirect/config.php",
				"single"			=>	true
			)
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
				"category"	=>	__("Text Fields,Basic", "cladera-forms"),
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/text/preview.php"
				)
			),
			'file' => array(
				"field"		=>	"File",
				"description" => __('File Uploader', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/file/field.php",
				"handler"	=>	array($this, 'handle_file_upload'),
				"viewer"	=>	array($this, 'handle_file_view'),
				"category"	=>	__("Basic,File", "cladera-forms"),
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/file/preview.php",
					"template"	=>	CFCORE_PATH . "fields/file/config_template.php"
				)
			),
			'recaptcha' => array(
				"field"		=>	"reCAPTCHA",
				"description" => __('reCAPTCHA anti-spam field', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/recaptcha/field.php",
				"category"	=>	__("Special", "cladera-forms"),
				"handler"	=>	array($this, 'captcha_check'),
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/recaptcha/config.php",
					"preview"	=>	CFCORE_PATH . "fields/recaptcha/preview.php",
					"not_supported"	=>	array(
						'hide_label',
						'caption',
						'required'
					),
					"scripts"	=> array(
						"http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"
					)
				),
				"scripts"	=> array(
					"http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"
				),
				"styles"	=> array(
					CFCORE_URL . "fields/recaptcha/style.css"
				),
			),
			'html' => array(
				"field"		=>	"HTML",
				"description" => __('Add text/html content', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/html/field.php",
				"category"	=>	__("Content", "cladera-forms"),
				"icon"		=>	CFCORE_URL . "fields/html/icon.png",
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
				"category"	=>	__("Text Fields,Basic", "cladera-forms"),
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
			'button' => array(
				"field"		=>	"Button",
				"description" => __('Button, Submit and Reset types', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/button/field.php",
				"category"	=>	__("Buttons,Basic", "cladera-forms"),
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/button/config_template.html",
					"preview"	=>	CFCORE_PATH . "fields/button/preview.php",
					"default"	=> array(
						'class'	=>	'btn btn-primary',
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
			'email' => array(
				"field"		=>	"Email Address",
				"description" => __('Email Address', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/email/field.php",
				"category"	=>	__("Text Fields,Basic", "cladera-forms"),
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/email/preview.php"
				)
			),
			'paragraph' => array(
				"field"		=>	"Paragraph Textarea",
				"description" => __('Paragraph Textarea', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/paragraph/field.php",
				"category"	=>	__("Text Fields,Basic", "cladera-forms"),
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
				"category"	=>	__("Select Options,Special", "cladera-forms"),
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
				"category"	=>	__("Select Options,Basic", "cladera-forms"),
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
				"category"	=>	__("Select Options,Basic", "cladera-forms"),
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
				"category"	=>	__("Select Options,Basic", "cladera-forms"),
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
				"category"	=>	__("Text Fields,Pickers", "cladera-forms"),
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
				"category"	=>	__("Text Fields,Pickers", "cladera-forms"),
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

	static public function check_condition($conditions, $data, $form){

		$trues = array();
		if(empty($conditions['group'])){
			return true;
		}
		foreach($conditions['group'] as $groupid=>$lines){
			$truelines = array();
			
			foreach($lines as $lineid=>$line){
				$truelines[$lineid] = false;

				if(!empty($line['field']) && isset($form['fields'][$line['field']])){

					// if not sent - preset it to a null value.
					if(!isset($data[$form['fields'][$line['field']]['slug']])){						
						$prevalue = '';
					}else{
						$prevalue = $data[$form['fields'][$line['field']]['slug']];
					}

					foreach( (array) $prevalue as $value){

						switch ($line['compare']) {
							case 'is':
								if($value == $line['value']){
									$truelines[$lineid] = true;
								}
								break;
							case 'isnot':

								if($value != $line['value']){
									$truelines[$lineid] = true;
								}
								break;
							case '>':
								if($value > $line['value']){
									$truelines[$lineid] = true;
								}
								break;
							case '<':
								if($value < $line['value']){
									$truelines[$lineid] = true;
								}
								break;
							case 'startswith':
								if( substr( $value, 0, strlen($line['value']) ) == $line['value']){
									$truelines[$lineid] = true;
								}
								break;
							case 'endswith':
								if( substr( $value, strlen($value)-strlen($line['value']) ) == $line['value']){
									$truelines[$lineid] = true;
								}
								break;
							case 'contains':
								if( false !== strpos( $value, $line['value'] ) ){
									$truelines[$lineid] = true;
								}
								break;
						}

					}
				}
			}

			$trues[$groupid] = in_array(false, $truelines) ? false : true;
		}
		
		if($conditions['type'] == 'use' || $conditions['type'] == 'show'){
			if(in_array(true, $trues)){
				return true;
			}
		}elseif($conditions['type'] == 'not' || $conditions['type'] == 'hide'){
			if(!in_array(true, $trues)){
				return true;
			}
		}

		// false if nothing happens
		return false;
	}

	// FRONT END STUFFF
	static public function form_redirect($type, $url, $data, $form, $processid){

		do_action('caldera_forms_redirect', $type, $url, $data, $form, $processid);
		do_action('caldera_forms_redirect_' . $type, $url, $data, $form, $processid);
		
		$url = apply_filters('caldera_forms_redirect', $url, $data, $form, $processid);
		$url = apply_filters('caldera_forms_redirect' . $type, $url, $data, $form, $processid);
		
		if(!empty($url)){
			wp_redirect( $url );
			exit;
		}
	}

	static public function process_submission(){
		global $post, $front_templates, $wp_query, $processID, $form;

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
		$form = get_option( $_POST['_cf_frm_id'] );
		if(empty($form['ID']) || $form['ID'] != $_POST['_cf_frm_id']){
			return;
		}

		unset($_POST['_wp_http_referer']);

		// unset stuff
		unset($_POST['_cf_frm_id']);
		unset($_POST['_cf_verify']);

		$form_instance_number = 1;
		if(isset($_POST['_cf_frm_ct'])){
			$form_instance_number = $_POST['_cf_frm_ct'];
		}
		
		
		// SET process ID
		$processID = uniqid('_cf_process_');

		// get data ready
		$rawdata = stripslashes_deep( $_POST );
		$data = array();
		foreach($form['fields'] as $field_slug=>$field){
			if(isset($_POST[$field_slug])){
				$data[$field['slug']] = $_POST[$field_slug];
			}
		}				
		$process_data = $data;

		// init filter
		$form = apply_filters('caldera_forms_submit_get_form', $form, $process_data, $referrer, $processID);

		// get all fieldtype
		$field_types = apply_filters('caldera_forms_get_field_types', array() );

		// start action
		do_action('caldera_forms_submit_start', $data, $form, $referrer, $processID);

		// requireds
		// set transient for returns submittions
		$transdata = array(
			'transient' 	=> $processID,
			'form_instance' => $form_instance_number,
			'expire'		=> 120,
			'data' 			=> $data
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

		// field handlers + required checks
		foreach($form['fields'] as $field){

			// setfield
			$field = apply_filters('caldera_forms_render_setup_field', $field, $form);
			if(empty($field)){
				continue;
			}
			// handler
			if(isset($field_types[$field['type']]['handler']) && isset($process_data[$field['slug']])){

				if(is_array($field_types[$field['type']]['handler'])){
					$process_data[$field['slug']] = call_user_func_array($field_types[$field['type']]['handler'],array($process_data[$field['slug']], $field, $data, $form));
				}else{
					if(function_exists($field_types[$field['type']]['handler'])){
						$func = $field_types[$field['type']]['handler'];
						$process_data[$field['slug']] = $func($process_data[$field['slug']], $field, $process_data, $form);	
					}
				}
				if(is_array($process_data[$field['slug']]) && isset($process_data[$field['slug']]['_fail'])){
					$transdata['fields'][$field['slug']] = $process_data[$field['slug']]['_fail'];
				}
			}

			// required check
			$failed = false;
			if(!empty($field['required'])){
				
				// check if conditions match first.
				if(!empty($field['conditions']['type'])){							
					if(!self::check_condition($field['conditions'], $process_data, $form)){
						continue;
					}
				}


				if(isset($process_data[$field['slug']])){
					if(is_array($process_data[$field['slug']])){
						if(count($process_data[$field['slug']]) <= 0){
							$failed = true;
						}
					}else{
						if(strlen($process_data[$field['slug']]) < 1){
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

			return self::form_redirect('error', $referrer, $process_data, $form, $processID );
		}


		// has processors
		do_action('caldera_forms_submit_start_processors', $process_data, $form, $referrer, $processID);
		if(!empty($form['processors'])){
			
			// get all form processors
			$form_processors = apply_filters('caldera_forms_get_form_processors', array() );
			do_action('caldera_forms_submit_pre_process', $process_data, $form, $referrer, $processID);
			foreach($form['processors'] as $processor_id=>$processor){
				
				if(isset($form_processors[$processor['type']])){

					// Do Conditional
					if(isset($processor['conditions']) && !empty($processor['conditions']['type'])){
						if(!self::check_condition($processor['conditions'], $process_data, $form)){
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
						$process_line_data = call_user_func_array($process['pre_processor'],array($process_data, $config, $data, $form));
					}else{
						if(function_exists($process['pre_processor'])){
							$func = $process['pre_processor'];
							$process_line_data = $func($process_data, $config, $data, $form);	
						}
					}
					if(empty($process_line_data) || false === $process_line_data){
						// processor killed it- replace with original data.
						$process_line_data = $data;
					}elseif(!empty($process_line_data)){
						if(is_array($process_line_data) && isset($process_line_data['_fail'])){
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
							return self::form_redirect('fail', $referrer, $process_data, $form, $processID );
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
					// Do Conditional
					if(isset($processor['conditions']) && !empty($processor['conditions']['type'])){
						if(!self::check_condition($processor['conditions'], $process_data, $form)){
							continue;
						}
					}

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
						$process_line_data = call_user_func_array($process['processor'],array($process_data, $config, $data, $form));
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
					// Do Conditional
					if(isset($processor['conditions']) && !empty($processor['conditions']['type'])){
						if(!self::check_condition($processor['conditions'], $process_data, $form)){
							continue;
						}
					}

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
						$process_line_data = call_user_func_array($process['post_processor'],array($process_data, $config, $data, $form));
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
		$referrer['query']['cf_su'] = $form_instance_number;
		$referrer = $referrer['path'] . '?' . http_build_query($referrer['query']);

		// filter refer
		$referrer = apply_filters('caldera_forms_submit_redirect', $referrer, $process_data, $form, $processID);
		$referrer = apply_filters('caldera_forms_submit_redirect_complete', $referrer, $process_data, $form, $processID);

		return self::form_redirect('complete', $referrer, $process_data, $form, $processID );
	}

	static public function check_forms_shortcode(){
		global $post, $front_templates, $wp_query, $processID, $form;

		//HOOK IN post
		
		if(isset($_POST['_cf_verify']) && isset( $_POST['_cf_frm_id'] )){
			if(wp_verify_nonce( $_POST['_cf_verify'], 'caldera_forms_front' )){
		
				self::process_submission();
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
		$style_includes = get_option( '_caldera_forms_styleincludes' );

		if(!empty($style_includes['grid'])){
			wp_enqueue_style( 'cf-grid-styles', CFCORE_URL . 'assets/css/caldera-grid.css', array(), self::VERSION );
		}
		if(!empty($style_includes['form'])){
			wp_enqueue_style( 'cf-form-styles', CFCORE_URL . 'assets/css/caldera-form.css', array(), self::VERSION );
		}
		if(!empty($style_includes['alert'])){
			wp_enqueue_style( 'cf-alert-styles', CFCORE_URL . 'assets/css/caldera-alert.css', array(), self::VERSION );
		}
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

								if(!empty($form['fields'])){
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
	
	static public function get_entry($entry_id = null){
		
		if(empty($entry_id)){
			if(!empty($_POST['form'])){
				$entry_id = $_POST['entry'];
				$form = get_option( $_POST['form'] );
				if(empty($form)){
					return;
				}
				
				$field_types = apply_filters('caldera_forms_get_field_types', array() );

				$fields = array();
				foreach ($form['fields'] as $field_id => $field) {
					$fields[$field['slug']] = $field;
				}
			}
		}

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

			if(isset($fields[$row->slug])){
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


				if(substr($row->value, 0,2) === '{"' && substr($row->value, strlen($row->value)-2 ) === '"}'){
					$line = json_decode($row->value, true);
					if(!empty($line)){
						$keys = array_keys($line);
						if(is_int($keys[0])){
							$line = implode(', ', $line);
						}else{
							$tmp = array();
							foreach($line as $key=>$item){
								if(is_array($item)){
									$item = '( ' . implode(', ', $item).' )';
								}
								$tmp[] = $key.': '.$item;
							}
							$line = implode(', ', $tmp);
						}
					$row->value = $line;
					}
				}
				if(is_array($row->value)){
					$keys = array_keys($line);
					if(is_int($keys[0])){
						$line = implode(', ', $line);
					}else{
						$tmp = array();
						foreach($line as $key=>$item){
							if(is_array($item)){
								$item = '( ' . implode(', ', $item).' )';
							}
							$tmp[] = $key.': '.$item;
						}
						$line = implode(', ', $tmp);
					}
					$row->value = $line;
				}


				$data['date'] = $row->_date_submitted;
				$data['user'] = $row->_user_id;

				$data['data'][$row->slug]['label'] = $field['label'];
				if(isset($data['data'][$row->slug]['value'])){
					$data['data'][$row->slug]['value'] = implode(', ', array($data['data'][$row->slug]['value'], $row->value));
				}else{
					$data['data'][$row->slug]['value'] = $row->value;
				}				

			}else{
				$data[$row->slug] = $row->value;
			}			
		}
		if(!empty($_POST['form'])){
			header('Content-Type: application/json');
			echo json_encode( $data );
			exit;
		}
		return $data;
	}

	static public function render_form($atts, $entry_id = null){

		global $current_form_count;

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

		if(empty($current_form_count)){
			$current_form_count = 0;
		}
		$current_form_count += 1;

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
			$prev_data = apply_filters('caldera_forms_render_get_entry', $prev_data, $form, $entry_id);
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
		if(!empty($_GET['cf_su']) && $current_form_count == $_GET['cf_su']){
			if(empty($notices['success']['note'])){
				$notices['success']['note'] = $form['success'];
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

		$conditions_templates = array();
		$conditions_configs = array();
		if(!empty($form['layout_grid']['fields'])){

			foreach($form['layout_grid']['fields'] as $field_base_id=>$location){

				if(isset($form['fields'][$field_base_id])){
					
					$field = apply_filters('caldera_forms_render_setup_field', $form['fields'][$field_base_id], $form);

					if(empty($field) || !isset($field_types[$field['type']]['file']) || !file_exists($field_types[$field['type']]['file'])){
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
						"id"				=>	$field_base_id,//'fld_' . $field['slug'],
						"name"				=>	$field_base_id,//$field['slug'],
						"label_before"		=>	( empty($field['hide_label']) ? "<label for=\"" . $field_base_id . "\" class=\"" . $field_classes['field_label'] . "\">" : null ),
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

					// conditional wrapper
					if(!empty($field['conditions']['group']) && !empty($field['conditions']['type'])){
						// wrap it up
						
						$conditions_templates[$field_base_id] = "<script type=\"text/html\" id=\"conditional-" . $field_base_id . "-tmpl\">\r\n" . $field_html . "</script>\r\n";
						$conditions_configs[$field_base_id] = $field['conditions'];
						if($field['conditions']['type'] == 'show'){
							// show if indicates hidden by default until condition is matched.
							$field_html = null;
						}
						// wrapp it up
						$field_html = '<span class="caldera-forms-conditional-field" id="conditional_' . $field_base_id . '">' . $field_html . '</span>';
					}

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
			
			$form_attributes = array(
				'method'	=>	'POST',
				'enctype'	=>	'multipart/form-data',
				'role'		=>	'form'
			);

			$form_classes = apply_filters('caldera_forms_render_form_classes', $form_classes, $form);
			$form_attributes = apply_filters('caldera_forms_render_form_attributes', $form_attributes, $form);

			$attributes = array();
			foreach($form_attributes as $attribute=>$value){
				$attributes[] = $attribute . '="' . htmlentities( $value ) . '"';
			}

			// render only non success
			$out .= "<form class=\"" . implode(' ', $form_classes) . "\" " . implode(" ", $attributes) . ">\r\n";
			$out .= wp_nonce_field( "caldera_forms_front", "_cf_verify", true, false);
			$out .= "<input type=\"hidden\" name=\"_cf_frm_id\" value=\"" . $atts['id'] . "\">\r\n";
			$out .= "<input type=\"hidden\" name=\"_cf_frm_ct\" value=\"" . $current_form_count . "\">\r\n";
			$out .= $grid->renderLayout();
			$out .= "</form>\r\n";
		}
		
		$out .= "</div>\r\n";
		
		// output javascript conditions.
		if(!empty($conditions_configs) && !empty($conditions_templates)){
			echo "<script>\r\n";
			echo "var caldera_conditionals = " . json_encode($conditions_configs) . ";\r\n";
			echo "</script>\r\n";
			echo implode("\r\n", $conditions_templates);

			// enqueue conditionls app.
			wp_enqueue_script( 'cf-frontend-conditionals', CFCORE_URL . 'assets/js/conditionals.js', array('jquery'), self::VERSION, true);
		}

		do_action('caldera_forms_render_end', $form);

		return apply_filters('caldera_forms_render_form', $out, $form);

	}

}
