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
		add_filter('caldera_forms_get_field_types', array( $this, 'get_internal_field_types'));
		add_filter('caldera_forms_get_form_processors', array( $this, 'get_form_processors'));
		add_filter('caldera_forms_submit_redirect_complete', array( $this, 'do_redirect'),10, 4);
		add_action('caldera_forms_edit_end', array($this, 'calculations_templates') );

		// magic tags
		//add_filter('caldera_forms_render_magic_tag', array( $this, 'do_magic_tags'));
		// mailser
		add_filter('caldera_forms_get_magic_tags', array( $this, 'set_magic_tags'),1);
		add_filter('caldera_forms_mailer', array( $this, 'mail_attachment_check'),10, 3);

		// action
		add_action('caldera_forms_submit_complete', array( $this, 'save_final_form'),50);


		add_action("wp_ajax_get_entry", array( $this, 'get_entry') );
		// find if profile is loaded
		add_action('wp', array( $this, 'check_forms_shortcode'));

		// render shortcode
		add_shortcode( 'caldera_form', array( $this, 'render_form') );

		// check update version
		$version = get_option('_calderaforms_lastupdate');
		if(empty($version) || version_compare($version, CFCORE_VER) < 0){
			self::activate_caldera_forms();
			update_option('_calderaforms_lastupdate',CFCORE_VER);
			wp_redirect( $_SERVER['REQUEST_URI'] );
			exit;
		}

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

	/// activator
	public static function activate_caldera_forms(){
		global $wpdb;

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
			KEY `meta_key` (`meta_key`),
			KEY `entry_id` (`entry_id`)
			) DEFAULT CHARSET=utf8;";
			
			dbDelta( $meta_table );

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
			`field_id` varchar(20) NOT NULL,
			`slug` varchar(255) NOT NULL DEFAULT '',
			`value` longtext NOT NULL,
			PRIMARY KEY (`id`),
			KEY `form_id` (`entry_id`),
			KEY `field_id` (`field_id`),
			KEY `slug` (`slug`)
			) DEFAULT CHARSET=utf8;";

			dbDelta( $values_table );
		
		}else{
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
							$config = get_option($form['form_id']);						
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
			if(!in_array('status', $fields)){
				$wpdb->query( "ALTER TABLE `" . $wpdb->prefix . "cf_form_entries` ADD `status` varchar(20) NOT NULL DEFAULT 'active' AFTER `datestamp`;" );
				$wpdb->query( "CREATE INDEX `status` ON `" . $wpdb->prefix . "cf_form_entries` (`status`); ");
			}
			
		}

	}


	public static function star_rating_viewer($value, $field, $form){

		$out = "<div style=\"color: " . $field['config']['color'] . "; font-size: 10px;\" >";
		if(!empty($field['config']['number'])){
			for( $i = 1; $i <= $field['config']['number']; $i++){
				$star = 'star-off-png';
				if( $i<= $value){
					$star = 'star-on-png';
				}
				$out .= '<span data-alt="'.$i.'" class="'.$star.'" title="'.$i.'" style="margin-right: -2px;"></span> ';
			}
		}
		$out .= '</div>';

		return $out;
	}

	public static function handle_file_view($value, $field, $form){

		return '<a href="' . $value .'" target="_blank">' . basename($value) .'</a>';

	}
	
	public static function mail_attachment_check($mail, $data, $form){

		// check for 
		foreach($form['fields'] as $field_id=>$field){
			if($field['type'] == 'file' && isset($field['config']['attach'])){

				$dir = wp_upload_dir();
				$file = str_replace($dir['baseurl'], $dir['basedir'], self::get_field_data($field_id, $form));
				if(file_exists($file)){
					$mail['attachments'][] = $file;	
				}
				
			}
		}
		return $mail;
	}


	public static function captcha_check($value, $field, $form){
		
		if(empty($_POST['recaptcha_response_field'])){
			return array('_fail' => __("The reCAPTCHA field is required.", 'caldera-forms'));
		}
		include_once CFCORE_PATH . 'fields/recaptcha/recaptchalib.php';

		$resp = recaptcha_check_answer($field['config']['private_key'], $_SERVER["REMOTE_ADDR"], $_POST['recaptcha_challenge_field'], $_POST['recaptcha_response_field']);

		if (!$resp->is_valid) {
			return array('_fail' => __("The reCAPTCHA wasn't entered correctly.", 'caldera-forms'));
		}
		return true;
	}

	public static function update_field_data($field, $entry_id, $form){
		global $wpdb, $form;

		$data = self::get_field_data($field['ID'], $form);

		if(empty($data)){
			return;
		}

		if( has_filter( 'caldera_forms_save_field' ) ){
			$data = apply_filters( 'caldera_forms_update_field', $data, $field, $form );
		}

		if( has_filter( 'caldera_forms_save_field_' . $field['type'] ) ){
			$data = apply_filters( 'caldera_forms_update_field_' . $field['type'], $data, $field, $form );
		}

		$wpdb->update($wpdb->prefix . 'cf_form_entry_values', array('value' => $data), array('entry_id' => $entry_id, 'field_id' => $field['ID']));

	}


	public static function save_field_data($field, $entry_id, $form){
		global $wpdb, $form;

		$data = self::get_field_data($field['ID'], $form);
		if(empty($data)){
			return;
		}

		foreach((array) $data as $key=>$entry){

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
				'value'		=> $entry
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

	public static function save_final_form($form){
		global $wpdb, $processed_meta;

		// check submit type (new or update)
		if(isset($_POST['_cf_frm_edt'])){
			// is edit
			//check user can edit this item.
			$user_id = get_current_user_id();
			if(!empty($user_id)){
				$details = self::get_entry_detail($_POST['_cf_frm_edt'], $form);
				if(!empty($details)){
					// check user can edit
					if( current_user_can( 'edit_posts' ) || $details['user_id'] === $user_id ){
						$entryid = $_POST['_cf_frm_edt'];
					}else{
						return new WP_Error( 'error', __( "Permission denied.", "caldera-forms" ) );
					}
				}

			}

		}		

		if(!empty($form['db_support'])){
			// new entry or update
			if(empty($entryid)){
				$new_entry = array(
					'form_id'	=>	$form['ID'],
					'user_id'	=>	0,
					'datestamp' =>	date_i18n( 'Y-m-d H:i:s', time(), 0)
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

				foreach($form['fields'] as $field_id=>$field){
					// add new and update
					self::save_field_data($field, $entryid, $form);

				}

				// save form meta if any
				if(isset($processed_meta[$form['ID']])){
					foreach($processed_meta[$form['ID']] as $process_id=>$meta_data){
						
						foreach($meta_data as $meta_key=>$meta_value){
						
							if(count($meta_value) > 1){
								$meta_value = json_encode($meta_value);
							}else{
								$meta_value = $meta_value[0];
								if(is_array($meta_value) || is_object($meta_value)){
									$meta_value = json_encode($meta_value);
								}
							}

							$meta_entry = array(
								'entry_id'	 =>	$entryid,
								'process_id' => $process_id,
								'meta_key'	 =>	$meta_key,
								'meta_value' =>	$meta_value
							);

							$wpdb->insert($wpdb->prefix . 'cf_form_entry_meta', $meta_entry);	

						}

					}
				}

			}else{

				// do update
				foreach($form['fields'] as $field_id=>$field){
					// add new and update
					self::update_field_data($field, $entryid, $form);

				}
				if(isset($processed_meta[$form['ID']])){
					foreach($processed_meta[$form['ID']] as $process_id=>$meta_data){
						
						foreach($meta_data as $meta_key=>$meta_value){
						
							if(count($meta_value) > 1){
								$meta_value = json_encode($meta_value);
							}else{
								$meta_value = $meta_value[0];
							}

							$meta_entry = array(
								'entry_id'	 =>	$entryid,
								'process_id' => $process_id,
								'meta_key'	 =>	$meta_key,
								'meta_value' =>	$meta_value
							);
							$wpdb->insert($wpdb->prefix . 'cf_form_entry_meta', $meta_entry);	

						}

					}
				}
				// return
				return;		
			}

		}

		if(empty($form['mailer']['enable_mailer'])){
			return;
		}

		// do mailer!
		$sendername = __('Caldera Forms Notification', 'caldera-forms');
		if(!empty($form['mailer']['sender_name'])){
			$sendername = $form['mailer']['sender_name'];
			if( false !== strpos($sendername, '%')){
				$isname = self::get_slug_data( trim($sendername, '%'), $form);
				if(!empty( $isname )){
					$sendername = $isname;
				}
			}
		}
		if(empty($form['mailer']['sender_email'])){
			$sendermail = get_option( 'admin_email' );
		}else{
			$sendermail = $form['mailer']['sender_email'];
			if( false !== strpos($sendermail, '%')){
				$ismail = self::get_slug_data( trim($sendermail, '%'), $form);
				if(is_email( $ismail )){
					$sendermail = $ismail;
				}
			}
		}
		// use summary
		if(empty($form['mailer']['email_message'])){
			$form['mailer']['email_message'] = "{summary}";
		}

		$mail = array(
			'recipients' => array(),
			'subject'	=> self::do_magic_tags($form['mailer']['email_subject']),
			'message'	=> $form['mailer']['email_message']."\r\n",
			'headers'	=>	array(
				'From: ' . $sendername . ' <' . $sendermail . '>'
			),
			'attachments' => array()
		);

		// Filter Mailer first as not to have user input be filtered
		$mail['message'] = self::do_magic_tags($mail['message']);

		if($form['mailer']['email_type'] == 'html'){
			$mail['headers'][] = "Content-type: text/html";
			$mail['message'] = nl2br($mail['message']);
		}

		// get tags
		preg_match_all("/%(.+?)%/", $mail['message'], $hastags);
		if(!empty($hastags[1])){
			foreach($hastags[1] as $tag_key=>$tag){
				$tagval = self::get_slug_data($tag, $form);
				if(is_array($tagval)){
					$tagval = implode(', ', $tagval);
				}
				$mail['message'] = str_replace($hastags[0][$tag_key], $tagval, $mail['message']);
			}
		}

		//$mail['message']

		// ifs
		preg_match_all("/\[if (.+?)?\](?:(.+?)?\[\/if\])?/", $mail['message'], $hasifs);
		if(!empty($hasifs[1])){
			// process ifs
			foreach($hasifs[0] as $if_key=>$if_tag){

				$content = explode('[else]', $hasifs[2][$if_key]);
				if(empty($content[1])){
					$content[1] = '';
				}
				$vars = shortcode_parse_atts( $hasifs[1][$if_key]);
				foreach($vars as $varkey=>$varval){
					if(is_string($varkey)){
						$var = self::get_slug_data($varkey, $form);
						if( in_array($varval, (array) $var) ){
							// yes show code
							$mail['message'] = str_replace( $hasifs[0][$if_key], $content[0], $mail['message']);
						}else{
							// nope- no code
							$mail['message'] = str_replace( $hasifs[0][$if_key], $content[1], $mail['message']);
						}
					}else{
						$var = self::get_slug_data($varval, $form);
						if(!empty($var)){
							// show code
							$mail['message'] = str_replace( $hasifs[0][$if_key], $content[0], $mail['message']);
						}else{
							// no code
							$mail['message'] = str_replace( $hasifs[0][$if_key], $content[1], $mail['message']);
						}
					}
				}
			}

		}
		

		if(!empty($form['mailer']['recipients'])){
			$mail['recipients'] = explode(',', $form['mailer']['recipients']);
		}else{
			$mail['recipients'][] = get_option( 'admin_email' );
		}

		$data = self::get_submission_data($form);

		$submission = array();
		foreach ($data as $field_id=>$row) {
			if($row === null || !isset($form['fields'][$field_id]) ){
				continue;
			}

			$key = $form['fields'][$field_id]['slug'];

			if(is_array($row)){
				$keys = array_keys($row);
				if(is_int($keys[0])){
					$row = implode(', ', $row);
				}else{
					$tmp = array();
					foreach($row as $linekey=>$item){
						if(is_array($item)){
							$item = '( ' . implode(', ', $item).' )';
						}
						$tmp[] = $linekey.': '.$item;
					}
					$row = implode(', ', $tmp);
				}
			}
			$mail['message'] = str_replace('%'.$key.'%', $row, $mail['message']);
			$mail['subject'] = str_replace('%'.$key.'%', $row, $mail['subject']);

			$submission[] = $row;
			$labels[] = $form['fields'][$field_id]['label'];
		}
		
		// CSV
		if(!empty($form['mailer']['csv_data'])){
			ob_start();
			$df = fopen("php://output", 'w');
			fputcsv($df, $labels);
			fputcsv($df, $submission);
			fclose($df);
			$csv = ob_get_clean();
			$csvfile = wp_upload_bits( uniqid().'.csv', null, $csv );
			$mail['attachments'][] = $csvfile['file'];
		}

		if(empty($mail)){
			return;
		}

		$mail = apply_filters( 'caldera_forms_mailer', $mail, $data, $form);		

		/*// recipients
		foreach($mail['recipients'] as &$recipient){
			// trim spaces
			$recipient = trim($recipient);
		}
		$recipients = implode(',', $mail['recipients']);
		*/
		$headers = implode("\r\n", $mail['headers']);		

		do_action( 'caldera_forms_do_mailer', $mail, $data, $form);
		if(!empty($mail)){

			if(wp_mail( (array) $mail['recipients'], $mail['subject'], $mail['message'], $headers, $mail['attachments'] )){
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
						return $processor['config']['url'];
					}
				}
			}
		}
		return $referrer;
	}	

	public static function send_auto_response($config, $form){
		global $form;

		$headers = 'From: ' . $config['sender_name'] . ' <' . $config['sender_email'] . '>' . "\r\n";
		
		// remove required bounds.
		unset($config['_required_bounds']);

		$message = $config['message'];
		
		$regex = "/%(.*?)%/";
		preg_match_all($regex, $message, $matches);
		if(!empty($matches[1])){
			foreach($matches[1] as $key=>$tag){
				$entry = null;
				if(isset($config[$tag])){
					$entry = self::get_field_data($config[$tag], $form);
				}else{
					$entry = self::get_slug_data($tag, $form);
				}
				if(!empty($entry)){
					if(is_array($entry)){
						if(count($entry) === 1){
							$entry = array_shift($entry);
						}elseif(count($entry) === 2){
							$entry = implode(' & ', $entry);
						}elseif(count($entry) > 2){
							$last = array_pop($entry);
							$entry = implode(', ', $entry).' & '.$last;
						}else{
							continue;
						}
					}
				}else{
					$entry = '';
				}
				$message = str_replace($matches[0][$key], $entry, $message);
			}
		}
		$message = self::do_magic_tags( $message );
		// setup mailer
		$recipient_name = self::get_field_data($config['recipient_name'], $form);
		$recipient_email = self::get_field_data($config['recipient_email'], $form);

		do_action( 'caldera_forms_do_autoresponse', $config, $form);

		wp_mail($recipient_name.' <'.$recipient_email.'>', $config['subject'], $message, $headers );

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
				"single"			=>	false
			)
		);

		return array_merge( $processors, $internal_processors );

	}

	static public function run_calculation($value, $field, $form){

		//$data = self::get_submission_data($form);
		
		$formula = $field['config']['formular'];
		if( empty($formula)){
			return 0;
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

		$total = create_function(null, 'return '.$formula.';');
		if(isset($field['config']['fixed'])){
			return money_format('%i', $total() );
		}
		return $total();
	}

	static public function calculations_templates(){
		include CFCORE_PATH . "fields/calculation/line-templates.php";
	}

	// get built in field types
	public function get_internal_field_types($fields){


		$internal_fields = array(
			'calculation' => array(
				"field"		=>	__("Calculation", "cladera-forms"),
				"file"		=>	CFCORE_PATH . "fields/calculation/field.php",
				"handler"	=>	array($this, "run_calculation"),
				"category"	=>	__("Special,Math", "cladera-forms"),
				"description" => __('Calculate values', "cladera-forms"),
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/calculation/config.php",
					"preview"	=>	CFCORE_PATH . "fields/calculation/preview.php",
					"default"	=> array(
						'element'	=>	'h3',
						'classes'	=> 	'total-line',
						'before'	=>	__('Total', 'caldera-forms').':',
						'after'		=> ''
					),
					"styles" => array(
						CFCORE_URL . "fields/calculation/style.css",
					)
				),
				"scripts" => array(
					'jquery'
				)
			),
			'range_slider' 	=> array(
				"field"		=>	__("Range Slider", 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/range_slider/field.php",
				"category"	=>	__("Special", "cladera-forms"),
				"description" => __('Range Slider input field','caldera-forms'),
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
					"scripts" => array(
						'jquery',
						CFCORE_URL . "fields/range_slider/rangeslider.js",
					),
					"styles" => array(
						CFCORE_URL . "fields/range_slider/rangeslider.css",
					)
				),
				"scripts" => array(
					'jquery',
					CFCORE_URL . "fields/range_slider/rangeslider.js",
				),
				"styles" => array(
					CFCORE_URL . "fields/range_slider/rangeslider.css",
				)
			),
			'star_rating' 	=> array(
				"field"		=>	__("Star Rating", 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/star-rate/field.php",
				"category"	=>	__("Feedback,Special", "cladera-forms"),
				"description" => __('Star rating input for feedback','caldera-forms'),
				"viewer"	=>	array($this, 'star_rating_viewer'),
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/star-rate/config.php",
					"preview"	=>	CFCORE_PATH . "fields/star-rate/preview.php",
					"default"	=> array(
						'number'	=>	5,
						'space'		=>	3,
						'size'		=>	13,
						'color'		=> '#FFAA00'
					),
					"scripts" => array(
						'jquery',
						CFCORE_URL . "fields/star-rate/jquery.raty.js",
					),
					"styles" => array(
						CFCORE_URL . "fields/star-rate/jquery.raty.css",
					)
				),
				"scripts" => array(
					'jquery',
					CFCORE_URL . "fields/star-rate/jquery.raty.js",
				),
				"styles" => array(
					CFCORE_URL . "fields/star-rate/jquery.raty.css",
				)
			),
			'phone' => array(
				"field"		=>	__('Phone Number', 'caldera-forms'),
				"description" => __('Phone number with masking', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/phone/field.php",
				"category"	=>	__("Text Fields,Basic,User", "cladera-forms"),
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/phone/config.php",
					"preview"	=>	CFCORE_PATH . "fields/phone/preview.php",
					"default"	=>	array(
						'default'	=> '',
						'type'	=>	'local',
						'custom'=> '(999)999-9999'
					),
					"scripts"	=> array(
						CFCORE_URL . "fields/phone/masked-input.js"
					)
				),
				"scripts"	=> array(
					"jquery",
					CFCORE_URL . "fields/phone/masked-input.js"
				)
			),
			'text' => array(
				"field"		=>	"Single Line Text",
				"description" => __('Single Line Text', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/text/field.php",
				"category"	=>	__("Text Fields,Basic", "cladera-forms"),
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/text/config.php",
					"preview"	=>	CFCORE_PATH . "fields/text/preview.php"
				),
				"scripts"	=> array(
					"jquery",
					CFCORE_URL . "fields/phone/masked-input.js"
				)
			),
			'file' => array(
				"field"		=>	"File",
				"description" => __('File Uploader', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/file/field.php",
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
			'button' => array(
				"field"		=>	"Button",
				"description" => __('Button, Submit and Reset types', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/button/field.php",
				"category"	=>	__("Buttons,Basic", "cladera-forms"),
				"setup"		=>	array(
					"template"	=>	CFCORE_PATH . "fields/button/config_template.html",
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
			'email' => array(
				"field"		=>	"Email Address",
				"description" => __('Email Address', 'caldera-forms'),
				"file"		=>	CFCORE_PATH . "fields/email/field.php",
				"category"	=>	__("Text Fields,Basic", "cladera-forms"),
				"setup"		=>	array(
					"preview"	=>	CFCORE_PATH . "fields/email/preview.php",
					"template"	=>	CFCORE_PATH . "fields/email/config.php"
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
				"static"	=> true,
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
				"static"	=> true,
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
				"static"	=> true,
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
				"static"	=> true,
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

	static public function check_condition($conditions, $form){

		$trues = array();
		if(empty($conditions['group'])){
			return true;
		}
		//$data = self::get_submission_data($form);

		foreach($conditions['group'] as $groupid=>$lines){
			$truelines = array();
			
			foreach($lines as $lineid=>$line){

				$value = (array) self::get_field_data($line['field'], $form);
				if(empty($value)){
					$value = array('');
				}
				// do field value replaces
				if( false !== strpos($line['value'], '%')){
					$isslug = self::get_slug_data( trim($line['value'], '%'), $form);
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
								if( 0 === strpos($line['value'], $part)){
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
		}elseif($conditions['type'] == 'not' || $conditions['type'] == 'hide'){
			if(!in_array(true, $trues)){
				return true;
			}
		}

		// false if nothing happens
		return false;
	}

	// FRONT END STUFFF
	static public function form_redirect($type, $url, $form, $processid){

		$url = apply_filters('caldera_forms_redirect_url', $url, $form, $processid);
		$url = apply_filters('caldera_forms_redirect_url_' . $type, $url, $form, $processid);

		do_action('caldera_forms_redirect', $type, $url, $form, $processid);
		do_action('caldera_forms_redirect_' . $type, $url, $form, $processid);
				
		if(!empty($url)){
			wp_redirect( $url );
			exit;
		}
	}
	// magic tags

	// set tags
	public function set_magic_tags($tags){

		// get internal tags
		$system_tags = array(
			'ip',
			'user:id',
			'user:user_login',
			'user:firstname',
			'user:lastname',
			'user:user_email' => array(
				'text',
				'email'
			),
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
			'date:Y/d/m'

		);

		$tags['system'] = array(
			'type'	=> __('System Tags', 'caldera-forms'),
			'tags'	=> $system_tags
		);
		// get processor tags
		$processors = apply_filters('caldera_forms_get_form_processors', array() );
		if(!empty($processors)){
			foreach($processors as $processor_key=>$processor){
				if(isset($processor['magic_tags'])){					
					foreach($processor['magic_tags'] as $key_tag=>$value_tag){

						if(!isset($tags[$processor_key])){
							$tags[$processor_key] = array(
								'type'	=>	$processor['name'],
								'tags'	=>	array()
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

	static public function do_magic_tags($value){

		global $processed_meta, $form;
		/// get meta entry for magic tags defined.

		// check for magics
		preg_match_all("/\{(.+?)\}/", $value, $magics);
		if(!empty($magics[1])){
			foreach($magics[1] as $magic_key=>$magic_tag){

				$magic = explode(':', $magic_tag, 2);

				if(count($magic) == 2){
					switch (strtolower( $magic[0]) ) {
						case 'get':
							if( isset($_GET[$magic[1]])){
								$magic_tag = $_GET[$magic[1]];
							}else{
								$magic_tag = null;
							}
							break;
						case 'post':
							if( isset($_POST[$magic[1]])){
								$magic_tag = $_POST[$magic[1]];
							}else{
								$magic_tag = null;
							}
							break;
						case 'request':
							if( isset($_REQUEST[$magic[1]])){
								$magic_tag = $_REQUEST[$magic[1]];
							}else{
								$magic_tag = null;
							}
							break;
						case 'date':
							$magic_tag = date($magic[1]);
							break;
						case 'user':
							if(is_user_logged_in()){
								$user = get_userdata( get_current_user_id() );
								if(isset( $user->data->{$magic[1]} )){
									$magic_tag = $user->data->{$magic[1]};
								}else{
									if(strtolower($magic[1]) == 'id'){
										$magic_tag = $user->ID;
									}
									$magic_tag = null;
								}
							}else{
								$magic_tag = null;
							}
							break;
						case 'embed_post':
							global $post;

							if(is_object($post)){
								if(isset( $post->{$magic[1]} )){
									$magic_tag = $post->{$magic[1]};
								}else{

									// extra post data
									switch ($magic[1]) {
										case 'permalink':
											$magic_tag = get_permalink( $post->ID );
											break;

									}

								}
							}else{
								$magic_tag = null;
							}
							break;

					}
				}else{
					switch ($magic_tag) {
						case 'ip':
							$magic_tag = $_SERVER['REMOTE_ADDR'];
							break;
						case 'summary':
							if(!empty($form['fields'])){
								$out = array();
								foreach($form['fields'] as $field_id=>$field){
									$field_value = self::get_field_data($field_id, $form);
									if(is_array($field_value)){
										$field_value = implode(', ', $field_value);
									}
									if($field_value !== null && strlen($field_value) > 0){
										$out[] = $field['label'].': '.$field_value;
									}
								}
								if(!empty($out)){
									$magic_tag = implode("\r\n", $out);
								}
							}
							break;
					}
				}
				
				$filter_value = apply_filters('caldera_forms_do_magic_tag', $magic_tag, $magics[0][$magic_key]);
				if(!empty($form['ID']) && !empty($processed_meta[$form['ID']][$magic[0]])){

					foreach($processed_meta[$form['ID']][$magic[0]] as $return_array){
						foreach($return_array as $return_line){
							if(isset($return_line[$magic[1]])){
								$filter_value = $return_line[$magic[1]];
							}
						}
					}
				}
				$value = str_replace($magics[0][$magic_key], $filter_value, $value);
			}
		}

		return $value;
	}

	// get field types.
	static public function get_field_types(){
		//global $field_types;
		//if(!empty($field_types)){
		//	return $field_types;
		//}


		$field_types = apply_filters('caldera_forms_get_field_types', array() );

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
		
	static public function get_processor_by_type($type, $form){
		if(is_string($form)){
			$form = get_option($form);
			if(!empty($form['ID'])){
				if($form['ID'] !== $form || empty($form['processors'])){
					return false;
				}
			}
		}

		if(!empty($form['processors'])){
			$processors = array();
			foreach($form['processors'] as $processor){
				if($processor['type'] == $type){
					$processors[] = $processor;
				}
			}
			if(empty($processors)){
				return false;
			}
			return $processors;
		}
		return false;
	}

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

	static public function set_field_data($field_id, $data, $form, $entry_id = false){
		global $processed_data;

		$current_data = self::get_field_data($field_id, $form, $entry_id);
		
		if(is_string($form)){
			// get processed cached item using the form id
			if(isset($processed_data[$form][$field_id])){
				$processed_data[$form][$field_id] = $data;
				return true;
			}
		}
		// form object
		if(isset($form['ID'])){
			if(isset($processed_data[$form['ID']][$field_id])){
				$processed_data[$form['ID']][$field_id] = $data;
				return true;
			}
		}
	}

	static public function get_field_data($field_id, $form, $entry_id = false){
		global $processed_data;

		//dump($processed_data[$form['ID']],0);
		//echo $field_id.'<br>';
		if(is_string($form)){
			// get processed cached item using the form id
			if(isset($processed_data[$form][$field_id])){
				return $processed_data[$form][$field_id];
			}

			$form = get_option( $form );
			if(!isset($form['ID']) || $form['ID'] !== $form){

				return null;
			}
		}
		// get processed cached item
		if(isset($processed_data[$form['ID']][$field_id])){
			
			return $processed_data[$form['ID']][$field_id];
		}
		// entry fetch
		if(!empty($entry_id) && isset($form['fields'][$field_id])){

			global $wpdb;

			$entry = $wpdb->get_results($wpdb->prepare("
				SELECT `value` FROM `" . $wpdb->prefix ."cf_form_entry_values` WHERE `entry_id` = %d AND `field_id` = %s AND `slug` = %s", $entry_id, $field_id, $form['fields'][$field_id]['slug']), ARRAY_A);

			if(!empty($entry)){
				if( count( $entry ) > 1){
					$out = array();
					foreach($entry as $item){
						$out[] = $item['value'];
					}
					$processed_data[$form['ID']][$field_id] = $out;
				}else{
					$processed_data[$form['ID']][$field_id] = $entry[0]['value'];
				}
			}else{
				$processed_data[$form['ID']][$field_id] = null;
			}
			return $processed_data[$form['ID']][$field_id];
			//return $processed_data[$form['ID']][$field_id] = ;
		}

		if(isset($form['fields'][$field_id])){
			// get field
			$field = apply_filters('caldera_forms_render_setup_field', $form['fields'][$field_id], $form);

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
				if(!self::check_condition($field['conditions'], $form)){
					$processed_data[$form['ID']][$field_id] = $entry;
					return $entry;
				}
			}

			// check condition to see if field should be there first.
			// check if conditions match first. ignore vailators if not part of condition
			if(isset($_POST[$field_id])){
				$entry = stripslashes_deep($_POST[$field_id]);
			}
			// apply field filter
			if(has_filter('caldera_forms_process_field_' . $field['type'])){
				$entry = apply_filters( 'caldera_forms_process_field_' . $field['type'] , $entry, $field, $form );
				if( is_wp_error( $entry ) ) {
					$processed_data[$form['ID']][$field_id] = $entry;
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
								$out[] = self::do_magic_tags($field['config']['option'][$option_id]['value']);
							}
						}
						$processed_data[$form['ID']][$field_id] = $out;
					}else{
						if(!empty($field['config']['option'])){
							foreach($field['config']['option'] as $option){
								if($option['value'] == $entry){
									$processed_data[$form['ID']][$field_id] = self::do_magic_tags($entry);
									break;
								}
							}
						}
					}
				}else{
					$processed_data[$form['ID']][$field_id] = self::do_magic_tags($field['config']['default']);
				}
			}else{
				// dynamic
				$processed_data[$form['ID']][$field_id] = $entry;
			}
		}else{
			$is_tag = self::do_magic_tags($field_id);
			if($is_tag !== $field_id){
				$processed_data[$form['ID']][$field_id] = $is_tag;
			}
		}
		
		if(isset($processed_data[$form['ID']][$field_id])){
			return $processed_data[$form['ID']][$field_id];	
		}


		return null;
	}
	static public function get_slug_data($slug, $form, $entry_id = false){


		$out = array();
		if(false !== strpos($slug, '.')){
			$slug_parts = explode('.', $slug);
			$slug = array_shift($slug_parts);
		}

		$field_types = self::get_field_types();

		foreach($form['fields'] as $field_id=>$field){

			if($field['slug'] == $slug){
				
				if(isset($_POST[$field_id])){
					if(!empty($slug_parts)){						
						// just the part
						$line = stripslashes_deep( $_POST[$field_id] );
						foreach($slug_parts as $part){
							if(isset($line[$part])){
								$line = $line[$part];
							}
						}
						$out[] = $line;
					}else{
						//the whole thing
						$entry = stripslashes_deep( $_POST[$field_id] );

						//$entry = apply_filters('caldera_forms_view_field_' . $field['type'], $entry, $field, $form);

						/*if(isset($field_types[$field['type']]['viewer'])){

							if(is_array($field_types[$field['type']]['viewer'])){
								$entry = call_user_func_array($field_types[$field['type']]['viewer'],array($entry, $field, $form));
							}else{
								if(function_exists($field_types[$field['type']]['viewer'])){
									$func = $field_types[$field['type']]['viewer'];
									$entry = $func($entry, $field, $form);
								}
							}
						}*/
						if(is_array($entry)){
							if(isset($entry[0])){
								// list
								$entry = $field['label'].': '. implode(',' , $entry);
							}else{
								// named								
								foreach($entry as $item_key=>$item){
									if(is_array($item)){
										$item = $item_key.' ('.implode(', ', $item).')';
									}
									$out[] = $item;
								}								
							}
						}else{
							$out[] = $entry;
						}
					}					
				}
			}
		}
		if(count($out) === 1){
			$out = array_shift($out);
		}
		return $out;
	}	
	static public function get_entry_detail($entry_id, $form){
		global $wpdb;

		$entry = $wpdb->get_row($wpdb->prepare("SELECT * FROM `" . $wpdb->prefix ."cf_form_entries` WHERE `id` = %d", $entry_id), ARRAY_A);
		if(!empty($entry)){
			// get meta if any
			$entry_meta = $wpdb->get_results($wpdb->prepare("SELECT * FROM `" . $wpdb->prefix ."cf_form_entry_meta` WHERE `entry_id` = %d", $entry_id), ARRAY_A);
			if(!empty($entry_meta)){
				$processors = apply_filters('caldera_forms_get_form_processors', array() );				
				$entry['meta'] = array();
				foreach($entry_meta as $meta_index=>$meta){
					// is json?
					if( false !== strpos($meta['meta_value'], '{') || false !== strpos($meta['meta_value'], '[') ){
						$meta['meta_value'] = json_decode($meta['meta_value'], ARRAY_A);
					}else{
						$meta['meta_value'] = $meta['meta_value'];
					}

					$group = 'meta';
					$meta = apply_filters('caldera_forms_get_entry_meta', $meta, $form);

					if(isset($form['processors'][$meta['process_id']])){
						$group = $form['processors'][$meta['process_id']]['type'];
						$meta = apply_filters('caldera_forms_get_entry_meta_' . $form['processors'][$meta['process_id']]['type'], $meta, $form['processors'][$meta['process_id']]['config'], $form);
					}
					
					// allows plugins to remove it.
					if(!empty($meta)){
						if(!isset($entry['meta'][$group])){
							// is processor
							if(isset($form['processors'][$meta['process_id']]['type'])){
								$meta_name = $processors[$form['processors'][$meta['process_id']]['type']]['name'];
							}else{
								$meta_name = $meta['process_id'];
							}
							$entry['meta'][$group] = array(
								'name' => $meta_name,
								'data' => array()
							);
						}

						//if(!empty($meta['meta_title'])){
						//	$entry['meta'][$group]['data'][$meta['process_id']]['title'] = $meta['meta_title'];
						//}

						
						if(is_array($meta['meta_value'])){
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
						}

					}
				}
			}
		}
		$entry = apply_filters( 'caldera_forms_get_entry_detail', $entry, $entry_id, $form );
		return $entry;
	}

	static public function get_submission_data($form, $entry_id = false){
		global $processed_data;

		if(is_string($form)){
			// get processed cached item using the form id
			if(isset($processed_data[$form])){
				return $processed_data[$form];
			}
			$form_id = $form;
			$form = get_option( $form );
			if(!isset($form['ID']) || $form['ID'] !== $form_id){
				return new WP_Error( 'fail',  __('Invalid form ID') );
			}
		}


		// initialize process data
		foreach($form['fields'] as $field_id=>$field){			
			self::get_field_data( $field_id, $form, $entry_id);
		}

		return $processed_data[$form['ID']];
	}

	static public function process_submission(){
		global $post;
		global $front_templates;
		global $process_id;
		global $form;
		global $field_types;
		global $rawdata;
		global $processed_data;

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
		// get form and check
		$form = get_option( $_POST['_cf_frm_id'] );
		if(empty($form['ID']) || $form['ID'] != $_POST['_cf_frm_id']){
			return;
		}
		// init filter
		$form = apply_filters('caldera_forms_submit_get_form', $form);

		// instance number
		$form_instance_number = 1;
		if(isset($_POST['_cf_frm_ct'])){
			$form_instance_number = $_POST['_cf_frm_ct'];
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
			$chk = get_transient( $_GET['cf_er'] );
			if(isset($chk['transient']) && $chk['transient'] == $_GET['cf_er']){
				$process_id = $chk['transient'];
			}
		}
		if(empty($process_id)){
			$process_id = uniqid('_cf_process_');
		}


		// start action
		do_action('caldera_forms_submit_start', $form);

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
		$transdata = array(
			'transient' 	=> $process_id,
			'form_instance' => $form_instance_number,
			'expire'		=> 120,
			'data' 			=> $data
		);
		// setup transient data
		$transdata = apply_filters('caldera_forms_submit_transient_setup', $transdata);

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
							if(!isset($process_data[$value])){
								$form['fields'][$value]['required'] = 1;
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
			$user_id = get_current_user_id();
			if(empty($user_id)){
				$transdata['error'] = true;
				$transdata['note'] = __('Permission denied or entry does not exist.', 'caldera-forms');
			}else{
				
				$details = self::get_entry_detail($_POST['_cf_frm_edt'], $form);
				if(empty($details)){
					$transdata['error'] = true;
					$transdata['note'] = __('Permission denied or entry does not exist.', 'caldera-forms');
				}else{
					// check user can edit
					if( current_user_can( 'edit_posts' ) || $details['user_id'] === $user_id ){
						// can edit.
					}else{
						$transdata['error'] = true;
						$transdata['note'] = __('Permission denied or entry does not exist.', 'caldera-forms');
					}
				}

			}

		}


		// start brining in entries
		//$data = array();

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
				if(!empty($field['required'])){

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
						$transdata['fields'][$field_id] = $field['slug'] .' ' .__('is required', 'caldera-forms');
					}
				}
			}

		}
		
		// check requireds
		if(!empty($transdata['fields']) || !empty($transdata['error'])){
			$transdata['type'] = 'error';
			// set error transient
			$transdata = apply_filters('caldera_forms_submit_error_transient', $transdata, $form, $referrer, $process_id);
			$transdata = apply_filters('caldera_forms_submit_error_transient_required', $transdata, $form, $referrer, $process_id);
			
			set_transient( $process_id, $transdata, $transdata['expire']);
			
			// back to form
			$query_str = array(
				'cf_er' => $process_id
			);
			if(!empty($referrer['query'])){
				$query_str = array_merge($referrer['query'], $query_str);
			}
			$referrer = $referrer['path'] . '?' . http_build_query($query_str);
			$referrer = apply_filters('caldera_forms_submit_error_redirect', $referrer, $form, $process_id);
			$referrer = apply_filters('caldera_forms_submit_error_redirect_required', $referrer, $form, $process_id);

			return self::form_redirect('error', $referrer, $form, $process_id );
		}


		// has processors
		do_action('caldera_forms_submit_start_processors', $form, $referrer, $process_id);
		if(!empty($form['processors'])){
			
			// get all form processors
			$form_processors = apply_filters('caldera_forms_get_form_processors', array() );
			do_action('caldera_forms_submit_pre_process_start', $form, $referrer, $process_id);

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
						$process_line_data = call_user_func_array($process['pre_processor'],array($config, $form));
					}else{
						if(function_exists($process['pre_processor'])){
							$func = $process['pre_processor'];
							$process_line_data = $func($config, $form);
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
							$transdata = apply_filters('caldera_forms_submit_error_transient', $transdata, $form, $referrer, $process_id);
							$transdata = apply_filters('caldera_forms_submit_error_transient_pre_process', $transdata, $form, $referrer, $process_id);

							set_transient( $process_id, $transdata, $transdata['expire']);

							// back to form
							$query_str = array(
								'cf_er' => $process_id
							);
							if(!empty($referrer['query'])){
								$query_str = array_merge($referrer['query'], $query_str);
							}
							$referrer = $referrer['path'] . '?' . http_build_query($query_str);
							$referrer = apply_filters('caldera_forms_submit_error_redirect', $referrer, $form, $process_id);
							$referrer = apply_filters('caldera_forms_submit_error_redirect_pre_process', $referrer, $form, $process_id);
							return self::form_redirect('preprocess', $referrer, $form, $process_id );
						}
					}
				}
			}
			do_action('caldera_forms_submit_pre_process_end', $form, $referrer, $process_id);
			/// AFTER PRE-PROCESS - check for errors etc to return else continue to process.

			do_action('caldera_forms_submit_process_start', $form, $referrer, $process_id);
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
					if(isset($process['default'])){
						$config = $process['default'];
					}
					if(!empty($processor['config'])){

						$config = array_merge($config, $processor['config']);
					}
					if(is_array($process['processor'])){
						$hasmeta = call_user_func_array($process['processor'],array($config, $form));
					}else{
						if(function_exists($process['processor'])){
							$func = $process['processor'];
							$hasmeta = $func($config, $form);
						}
					}
					if($hasmeta !== null){
						foreach( (array) $hasmeta as $metakey=>$metavalue){
							self::set_submission_meta($metakey, $metavalue, $form, $processor_id);
						}
					}
				}
			}
			do_action('caldera_forms_submit_process_end', $form, $referrer, $process_id);
			// AFTER PROCESS - do post process for any additional stuff

			do_action('caldera_forms_submit_post_process', $form, $referrer, $process_id);
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
					if(isset($process['default'])){
						$config = $process['default'];
					}
					if(!empty($processor['config'])){

						$config = array_merge($config, $processor['config']);
					}
					if(is_array($process['post_processor'])){
						$hasmeta = call_user_func_array($process['post_processor'],array($config, $form));
					}else{
						if(function_exists($process['post_processor'])){
							$func = $process['post_processor'];
							$hasmeta = $func($config, $form);	
						}
					}
					if($hasmeta !== null){
						foreach( (array) $hasmeta as $metakey=>$metavalue){
							self::set_submission_meta($metakey, $metavalue, $form, $processor_id);
						}
					}

				}
			}
			do_action('caldera_forms_submit_post_process_end', $form, $referrer, $process_id);
		}
		
		// done do action.
		do_action('caldera_forms_submit_complete', $form, $referrer, $process_id);

		// redirect back or to result page
		$referrer['query']['cf_su'] = $form_instance_number;
		$referrer = $referrer['path'] . '?' . http_build_query($referrer['query']);

		// filter refer
		$referrer = apply_filters('caldera_forms_submit_redirect', $referrer, $form, $process_id);
		$referrer = apply_filters('caldera_forms_submit_redirect_complete', $referrer, $form, $process_id);

		return self::form_redirect('complete', $referrer, $form, $process_id );
	}

	static public function check_forms_shortcode(){
		global $post, $front_templates, $wp_query, $process_id, $form;

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

		$field_types = self::get_field_types();

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
									$field_types = self::get_field_types();
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
				
				$field_types = self::get_field_types();

				$fields = array();
				foreach ($form['fields'] as $field_id => $field) {
					$fields[$field['slug']] = $field;
				}
			}
			if(empty($form)){
				return array();
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
		$dateformat = get_option('date_format');
		$timeformat = get_option('time_format');
		$gmt_offset = get_option( 'gmt_offset' );

		foreach($rawdata as $row){
			if(empty($form)){
				$form = get_option($row->_form_id);
				if(empty($form)){
					return array();
				}
			}

			if(isset($fields[$row->slug])){
				$field = $fields[$row->slug];

				if(isset($field_types[$field['type']]['viewer'])){
					// is json?
					if(substr($row->value, 0,2) === '{"' && substr($row->value, strlen($row->value)-2 ) === '"}'){
						$is_value = json_decode($row->value, ARRAY_A);
						if(!empty($is_value)){
							$row->value = $is_value;
						}
					}

					$row->value = apply_filters('caldera_forms_view_field_' . $field['type'], $row->value, $field, $form);
					//$row->value = apply_filters( $tag, $value );
					/*
					if(is_array($field_types[$field['type']]['viewer'])){
						$row->value = call_user_func_array($field_types[$field['type']]['viewer'],array($row->value, $field, $form));
					}elseif(is_object($field_types[$field['type']]['viewer'])){

						$row->value = call_user_func_array($field_types[$field['type']]['viewer'],array($row->value, $field, $form));

					}elseif(function_exists($field_types[$field['type']]['viewer'])){
						$func = $field_types[$field['type']]['viewer'];
						$row->value = $func($row->value, $field, $form);
					}*/
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

				$data['date'] = date_i18n( $dateformat.' '.$timeformat, strtotime($row->_date_submitted), $gmt_offset);
				$data['user'] = $row->_user_id;

				$data['data'][$row->slug]['label'] = $field['label'];
				if(isset($data['data'][$row->slug]['value'])){
					$data['data'][$row->slug]['value'] = implode(', ', array($data['data'][$row->slug]['value'], $row->value));
				}else{
					$data['data'][$row->slug]['value'] = $row->value;
				}				

			}else{
				// front end for editing. no filtering just values to array.
				$data[$row->slug] = $row->value;
			}			
		}
		// get meta
		$entry_detail = self::get_entry_detail($entry_id, $form);
		if(!empty($entry_detail['meta'])){
			$data['meta'] = $entry_detail['meta'];
		}


		if(!empty($data['user'])){
			$user = get_userdata( $data['user'] );
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
		// allow plugins to alter the profile.
		$data['user'] = apply_filters('caldera_forms_get_entry_user', $data['user'], $entry_id, $form);

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

			$form = get_option( $atts );

			if(empty($form['ID']) || ( empty($form['ID']) && empty($form['name']) ) ){
				$forms = get_option( '_caldera_forms' );
				//dump($forms);
				return;
			}

			$atts = array( 'id' => $atts);
		}else{
			if(empty($atts['id'])){
				if(!empty($atts['name'])){
					$forms = get_option( '_caldera_forms' );
					foreach($forms as $form_id=>$form_maybe){
						if( trim(strtolower($atts['name'])) == strtolower($form_maybe['name']) ){
							$atts['id'] = $form_id;
							break;
						}
					}
				}

				if(empty($atts['id'])){
					return;
				}
			}
			$form = get_option( $atts['id'] );

		}

		
		$form = apply_filters('caldera_forms_render_get_form', $form );

		if(empty($form)){
			return;
		}

		if(empty($current_form_count)){
			$current_form_count = 0;
		}
		$current_form_count += 1;

		$field_types = self::get_field_types();

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
			if(!empty($user_id)){

				$details = self::get_entry_detail($entry_id, $form);

				if(!empty($details)){
					// check user can edit
					if( current_user_can( 'edit_posts' ) || $details['user_id'] === $user_id ){
						// can edit.
						$entry_id = (int) $details['id'];
					}else{
						$notices['error']['note'] = __('Permission denied or entry does not exist.', 'caldera-forms');
					}
				}else{
					$notices['error']['note'] = __('Permission denied or entry does not exist.', 'caldera-forms');					
				}

			}else{
				$notices['error']['note'] = __('Permission denied or entry does not exist.', 'caldera-forms');
			}

			if(!empty($notices['error']['note'])){
				$halt_render = true;
				$entry_id = false;
			}
		}

		// check for prev post
		$prev_data = apply_filters('caldera_forms_render_pre_get_entry', array(), $form, $entry_id);
		
		// load requested data
		if(!empty($entry_id)){
			$prev_data = self::get_entry($entry_id);			
			$prev_data = apply_filters('caldera_forms_render_get_entry', $prev_data, $form, $entry_id);
		}


		if(!empty($_GET['cf_er'])){
			$prev_post = get_transient( $_GET['cf_er'] );
			if(!empty($prev_post['transient'])){
				
				if($prev_post['transient'] === $_GET['cf_er']){
					foreach($prev_post['data'] as $field_id=>$field_entry){
						if(!is_wp_error( $field_entry )){
							$prev_data[$form['fields'][$field_id]['slug']] = $field_entry;
						}
					}
					
				}
				if(!empty($prev_post['type']) && !empty($prev_post['note'])){
					$notices[$prev_post['type']]['note'] = $prev_post['note'];
				}
				if(!empty($prev_post['fields'])){
					$field_errors = array();
					foreach($prev_post['fields'] as $field_id=>$field_error){
						if(is_wp_error( $field_error )){
							$field_errors[$form['fields'][$field_id]['slug']] = $field_error->get_error_message();
						}else{
							$field_errors[$form['fields'][$field_id]['slug']] = $field_error;
						}
					}
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

		// build grid & pages
		$grid->setLayout($form['layout_grid']['structure']);

		// insert page breaks
		if(!empty($page_breaks)){
			$currentpage = 1;
			if(isset($_GET['cf_pg']) && !isset($prev_post['page'])){
				$currentpage = (int) $_GET['cf_pg'];
			}elseif(isset($prev_post['page'])){
				$currentpage = (int) $prev_post['page'];
			}
			$display = 'none';
			if( $currentpage === 1){
				$display = 'block';
			}

			$total_rows = substr_count($form['layout_grid']['structure'], '|') + 1;
			$grid->before('<div data-formpage="1" class="caldera-form-page" style="display:'.$display.';">', 1);
			$grid->after('</div>', $total_rows);
			//dump($page_breaks);
			foreach($page_breaks as $page=>$break){

				$grid->after('</div>', $break);

				if($break+1 <= $total_rows ){
					$display = 'none';
					if($page+2 == $currentpage){
						$display = 'block';
					}

					$grid->before('<div data-formpage="' . ($page+2) . '" class="caldera-form-page" style="display:'.$display.';">', $break+1);
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
		if(!empty($form['layout_grid']['fields'])){

			foreach($form['layout_grid']['fields'] as $field_base_id=>$location){
				//
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

					$field_classes = apply_filters('caldera_forms_render_field_classes', $field_classes, $field, $form);
					$field_classes = apply_filters('caldera_forms_render_field_classes_type-' . $field['type'], $field_classes, $field, $form);
					$field_classes = apply_filters('caldera_forms_render_field_classes_slug-' . $field['slug'], $field_classes, $field, $form);

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

						$field_structure['field_value'] = self::do_magic_tags($field['config']['default']);
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
						
						// render conditions check- for magic tags since at this point all field data will be null
						//if(!self::check_condition($field['conditions'], $form)){
						//	dump($field['conditions'],0);
						//}

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

		$out = "<div class=\"caldera-grid\" id=\"caldera_form_" . $current_form_count ."\">\r\n";

		$notices = apply_filters('caldera_forms_render_notices', $notices, $form);

		if(!empty($notices)){
			// do notices
			foreach($notices as $note_type => $notice){
				if(!empty($notice['note'])){					
					$out .= '<div class=" '. implode(' ', $note_classes[$note_type]) . '">' . $notice['note'] .'</div>';
				}
			}

		}		
		if((empty($notices['success']) || empty($form['hide_form'])) && empty($halt_render)){

			$form_element = 'form';

			$form_classes = array(
				'caldera_forms_form'
			);
			
			$form_attributes = array(
				'method'	=>	'POST',
				'enctype'	=>	'multipart/form-data',
				'role'		=>	'form'
			);

			$form_element = apply_filters('caldera_forms_render_form_element', $form_element, $form);
			$form_classes = apply_filters('caldera_forms_render_form_classes', $form_classes, $form);
			$form_attributes = apply_filters('caldera_forms_render_form_attributes', $form_attributes, $form);

			$attributes = array();
			foreach($form_attributes as $attribute=>$value){
				$attributes[] = $attribute . '="' . htmlentities( $value ) . '"';
			}

			// render only non success
			$out .= "<" . $form_element . " class=\"" . implode(' ', $form_classes) . "\" " . implode(" ", $attributes) . ">\r\n";
			$out .= wp_nonce_field( "caldera_forms_front", "_cf_verify", true, false);
			$out .= "<input type=\"hidden\" name=\"_cf_frm_id\" value=\"" . $atts['id'] . "\">\r\n";
			$out .= "<input type=\"hidden\" name=\"_cf_frm_ct\" value=\"" . $current_form_count . "\">\r\n";
			// is edit?
			if(!empty($entry_id)){				
				$out .= "<input type=\"hidden\" name=\"_cf_frm_edt\" value=\"" . $entry_id . "\">\r\n";
			}

			// auto pagination
			if(!empty($form['auto_progress']) && count($form['page_names']) > 1){
			
			// retain query string				
			$qurystr = array();
			parse_str( $_SERVER['QUERY_STRING'], $qurystr );
			echo "<span class=\"caldera-grid\"><ol class=\"breadcrumb\" data-form=\"caldera_form_" . $current_form_count ."\">\r\n";
			$current_page = 1;
			if(!empty($_GET['cf_pg'])){
				$current_page = $_GET['cf_pg'];
			}
			foreach($form['page_names'] as $page_key=>$page_name){
				$tabclass = null;
					
				if($current_page == $page_key + 1){
					$tabclass = ' class="active"';
				}
			
				$qurystr['cf_pg'] = $page_key + 1;
				$qurystr['_rdm_'] = rand(100000, 999999);
				echo "<li" . $tabclass . "><a href=\"?". http_build_query($qurystr) . "\" data-page=\"" . ( $page_key + 1 ) ."\" data-pagenav=\"caldera_form_" . $current_form_count ."\">". $page_name . "</a></li>\r\n";
			}
			echo "</ol></span>\r\n";
			}

			$out .= $grid->renderLayout();
			$out .= "</" . $form_element . ">\r\n";
		}
		
		$out .= "</div>\r\n";
		
		// output javascript conditions.
		if(!empty($conditions_configs) && !empty($conditions_templates)){
			$conditions_str = json_encode($conditions_configs);
			// find %tags%
			preg_match_all("/%(.+?)%/", $conditions_str, $hastags);
			if(!empty($hastags[1])){
				
				foreach($hastags[1] as $tag_key=>$tag){

					foreach($form['fields'] as $field_id=>$field){
						if($field['slug'] === $tag){
							$conditions_str = str_replace('"'.$hastags[0][$tag_key].'"', "function(){ return jQuery('#".$field['ID']."').val(); }", $conditions_str);
						}
					}
				}
			}

			echo "<script type=\"text/javascript\">\r\n";
			echo "var caldera_conditionals = " . $conditions_str . ";\r\n";
			echo "</script>\r\n";
			echo implode("\r\n", $conditions_templates);

			// enqueue conditionls app.
			wp_enqueue_script( 'cf-frontend-conditionals', CFCORE_URL . 'assets/js/conditionals.js', array('jquery'), self::VERSION, true);
		}
		
		do_action('caldera_forms_render_end', $form);

		return apply_filters('caldera_forms_render_form', $out, $form);

	}

}
