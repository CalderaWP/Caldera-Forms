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
	protected $screen_prefix = array();
	/**
	 * @var      string
	 */
	protected $sub_prefix = null;
	/**
	 * @var      string
	 */
	protected $addons = array();
	/**
	 * @var      object
	 */
	protected static $instance = null;
	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 */
	private function __construct() {

		add_filter( 'all_plugins', array( $this, 'prepare_filter_addons' ) );

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Add Admin menu page
		add_action( 'admin_menu', array( $this, 'register_admin_page' ), 9 );
		
		// Add admin scritps and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_stylescripts' ) );

		// add element & fields filters
		add_filter('caldera_forms_get_panel_extensions', array( $this, 'get_panel_extensions'), 1);
		add_filter('caldera_forms_entry_viewer_buttons', array( $this, 'set_viewer_buttons'),10, 4);
		
		// action
		add_action('caldera_forms_entry_actions', array( $this, 'get_entry_actions'),1);
		add_action('caldera_forms_admin_templates', array( $this, 'get_admin_templates'),1);
		add_action('caldera_forms_entry_meta_templates', array( $this, 'get_admin_meta_templates'),1);

		add_action( 'init', array( $this, 'save_form') );
		add_action( 'media_buttons', array($this, 'shortcode_insert_button' ), 11 );
		add_filter( 'wp_fullscreen_buttons', array($this, 'shortcode_insert_button_fs' ), 11 );


		if( current_user_can( 'manage_options' ) ){
			// create forms
			add_action("wp_ajax_create_form", array( $this, 'create_form') );
		}

		add_action("wp_ajax_toggle_form_state", array( $this, 'toggle_form_state') );
		add_action("wp_ajax_browse_entries", array( $this, 'browse_entries') );		
		add_action("wp_ajax_save_cf_setting", array( $this, 'save_cf_setting') );
		add_action("wp_ajax_cf_dismiss_pointer", array( $this, 'update_pointer') );
		add_action("wp_ajax_cf_bulk_action", array( $this, 'bulk_action') );
		add_action("wp_ajax_cf_get_form_preview", array( $this, 'get_form_preview') );
		

		add_action( 'admin_footer', array( $this, 'add_shortcode_inserter'));


		$this->addons = apply_filters( 'caldera_forms_get_active_addons', array() );


		add_action('admin_footer-edit.php', array( $this, 'render_editor_template')); // Fired on the page with the posts table
		add_action('admin_footer-post.php', array( $this, 'render_editor_template')); // Fired on post edit page
		add_action('admin_footer-post-new.php', array( $this, 'render_editor_template')); // Fired on add new post page		

		add_action( 'caldera_forms_new_form_template_end', array( $this, 'load_new_form_templates') );

	}

	public function render_editor_template(){
		?>
	<script type="text/html" id="tmpl-editor-caldera-forms">
		<# if ( data.html ) { #>
			{{{ data.html }}}
		<# } else { #>
			<div class="wpview-error">
				<div class="dashicons dashicons-cf-logo"></div><p style="font-size: 13px;"><?php _e( 'Invalid Form.', 'caldera-forms' ); ?></p>
			</div>
		<# } #>
	</script>
	<?php

	}

	/**
	 * Returns the array of internal form templates
	 *
	 * @since 1.2.3
	 *
	 * @return    array | form templates
	 */
	public static function internal_form_templates(){
		
		$internal_templates = array(
			'starter_contact_form'	=>	array(
				'name'	=>	__( 'Starter Contact Form', 'caldera-forms' ),
				'template'	=>	include CFCORE_PATH . 'includes/templates/starter-contact-form.php'
			)
		);
		/**
		 * Filter form templates
		 *
		 * @since 1.2.3
		 *
		 * @param array internal form templates array
		 */
		return apply_filters( 'caldera_forms_get_form_templates', $internal_templates );
	}
	
	public function load_new_form_templates(){

		$form_templates = self::internal_form_templates();
		
		?>
		<div class="caldera-config-group">
			<label for=""><?php echo __('Form Template', 'caldera-forms'); ?></label>
			<div class="caldera-config-field">
				<select class="new-form-template block-input field-config" name="template" value="">
				<option value="0"><?php echo __('no template - blank form', 'caldera-forms'); ?></option>
				<?php

				foreach( $form_templates as $template_slug => $template ){
					if( !empty( $template['template'] ) && !empty( $template['name'] ) ){
						echo '<option value="' . $template_slug . '">' . $template['name'] . '</option>';
					}
				}

				?>
				</select>
			</div>
		</div>
		<?php
	}

	public function get_form_preview(){
		global $post;

		add_filter('caldera_forms_render_form_element', array( $this, 'set_preview_form_element') );
		$post = get_post( (int) $_POST['post_id'] );
		if( isset($_POST['atts']['named']['id']) ){
			$form = $_POST['atts']['named']['id'];
		}elseif( isset($_POST['atts']['named']['name']) ){
			$form = $_POST['atts']['named']['name'];
		}
		if( !empty( $form ) ){
			ob_start();
			echo Caldera_Forms::render_form( $form );
			$html = ob_get_clean();
		}
		$out = array();
		if( !empty( $html ) ){
			$out['html'] = $html;
		}
		
		wp_send_json_success( $out );
	}

	public function set_preview_form_element($element){
		return 'div';
	}

	public function prepare_filter_addons($plugins){
		global $wp_list_table, $status;

		if( !empty( $this->addons ) ){
			$addons = array();
			foreach( $this->addons as $addon ){
				$plugin_slug = basename( dirname( $addon['file'] ) ) .'/'.basename( $addon['file'] );
				if( isset( $plugins[$plugin_slug] ) ){
					if( isset( $addon['slug'] ) ){
						$plugins[$plugin_slug]['slug'] = $addon['slug'];
					}
				}				
			}
		}
		if( isset( $_REQUEST['plugin_status'] ) && $_REQUEST['plugin_status'] === 'caldera_forms' ){
			$status = 'caldera_forms';
		}

		return $plugins;
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
						if( current_user_can( 'delete_others_posts' ) ){
							$result = $wpdb->query( "DELETE FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `id` IN (".implode(',', $items).");" );
							$result = $wpdb->query( "DELETE FROM `" . $wpdb->prefix . "cf_form_entry_values` WHERE `entry_id` IN (".implode(',', $items).");" );
							$result = $wpdb->query( "DELETE FROM `" . $wpdb->prefix . "cf_form_entry_meta` WHERE `entry_id` IN (".implode(',', $items).");" );
						}
						$out['status'] = 'reload';
						wp_send_json( $out );
						break;
					
					default:
						if( current_user_can( 'edit_others_posts' ) ){
							$result = $wpdb->query( $wpdb->prepare( "UPDATE `" . $wpdb->prefix . "cf_form_entries` SET `status` = %s WHERE `id` IN (".implode(',', $items).");", $_POST['do'] ) );
						}
						break;
				}
				
				if( $result ){

					$out['status'] = $_POST['do'];
					$out['undo'] = ( $_POST['do'] === 'trash' ? 'active' : __('Trash') );
					$out['undo_text'] = ( $_POST['do'] === 'trash' ? __('Restore', 'caldera-forms') : __('Trash') );

					$out['entries'] = implode(',',$selectors);
					$out['total']	= $wpdb->get_var($wpdb->prepare("SELECT COUNT(`id`) AS `total` FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `form_id` = %s && `status` = 'active';", $_POST['form']));
					$out['trash']	= $wpdb->get_var($wpdb->prepare("SELECT COUNT(`id`) AS `total` FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `form_id` = %s && `status` = 'trash';", $_POST['form']));
					wp_send_json( $out );
				}
				exit();

				break;
			case 'export':

				$transientid = uniqid('cfe');
				set_transient( $transientid, $_POST['items'], 180 );				
				$out['url'] = "admin.php?page=caldera-forms&export=" . $_POST['form'] . "&tid=" . $transientid;
				wp_send_json( $out );
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

		$viewer_buttons_array = apply_filters( 'caldera_forms_entry_viewer_buttons', array());
		$viewer_buttons = null;
		if(!empty($viewer_buttons_array)){
			$viewer_buttons = array();
			foreach($viewer_buttons_array as $button){

				if(is_array($button['config'])){
					$config = $button['label'].'|'.json_encode($button['config']);
				}else{
					$config = $button['label'].'|'.$button['config'];	
				}
				if( isset( $button['class'] ) ){
					$config .= '|' . $button['class'];
				}
				$viewer_buttons[] = $config;
			}

			$viewer_buttons = 'data-modal-buttons=\'' . implode(';', $viewer_buttons) . '\'';
		}

		echo '{{#if ../../is_active}}<button class="button button-small ajax-trigger view-entry-btn" data-active-class="none" data-load-class="spinner" ' . $viewer_buttons . ' data-group="viewentry" data-entry="{{_entry_id}}" data-form="{{../../form}}" data-action="get_entry" data-modal="view_entry" data-modal-width="700" data-modal-height="700" data-modal-title="' . __('Entry', 'caldera-forms') . ' # {{_entry_id}}" data-template="#view-entry-tmpl" type="button">' . __('View') . '</button> {{/if}}';		
		if( current_user_can( 'delete_others_posts' ) ){
			echo '<button type="button" class="button button-small ajax-trigger" data-load-class="active" data-panel="{{#if ../../is_trash}}trash{{/if}}{{#if ../../is_active}}active{{/if}}" data-do="{{#if ../../is_trash}}active{{/if}}{{#if ../../is_active}}trash{{/if}}" data-callback="cf_refresh_view" data-form="{{../../form}}" data-active-class="disabled" data-group="row{{_entry_id}}" data-load-element="#entry_row_{{_entry_id}}" data-action="cf_bulk_action" data-items="{{_entry_id}}">{{#if ../../is_trash}}' . __('Restore', 'caldera-forms') . '{{/if}}{{#if ../../is_active}}' . __('Trash') . '{{/if}}</button>';
		}

	}
	
	public static function set_viewer_buttons($buttons){
		
		$buttons['close_panel'] = array(
			'label'		=>	'Close',
			'config'	=>	'dismiss',
			'class'		=>	'right'
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
		wp_send_json( $style_includes );
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
	function shortcode_insert_button_fs($buttons){
		
		$buttons['caldera-forms'] = array(
			"title"		=>	__('Add Form to Page','caldera-forms'),
			"both"		=> true
		);
		return $buttons;
	}


	public static function toggle_form_state(){
		
		$forms = Caldera_Forms::get_forms( true );
		$form = sanitize_text_field( $_POST['form'] );
		$form = Caldera_Forms::get_form( $form );
		if( empty( $form ) || empty( $form['ID'] ) || empty( $forms[ $form['ID'] ]) ){
			wp_send_json_error( );
		}

		if( isset( $form['form_draft'] ) ){
			unset( $form['form_draft'] );
			unset( $forms[ $form['ID'] ]['form_draft'] );		
			$state = 'active-form';
			$label = __('Deactivate', 'caldera-forms');
		}else{
			$forms[ $form['ID'] ]['form_draft'] = $form['form_draft'] = 1;
			$state = 'draft-form';
			$label = __('Activate', 'caldera-forms');
		}

		update_option( '_caldera_forms', $forms );
		update_option( $form['ID'], $form );
		
		wp_send_json_success( array( 'ID' => $form['ID'], 'state' => $state, 'label' => $label ) );
	}

	/**
	 * Show entries in admin
	 *
	 * @since unknown
	 */
	public static function browse_entries(){

		if ( isset( $_POST[ 'page' ] ) && 0 < $_POST[ 'page' ] ) {
			$page = absint( $_POST[ 'page' ] );
		}else{
			$page = 1;
		}

		if ( isset( $_POST[ 'perpage' ] ) && 0 < $_POST[ 'perpage' ] ) {
			$perpage = absint( $_POST[ 'perpage' ] );
		}else{
			$perpage = 20;
		}

		if ( isset( $_POST[ 'status' ] ) ) {
			$status = strip_tags( $_POST[ 'status' ] );
		}else{
			$status = 'active';
		}



		$form = Caldera_Forms::get_form( $_POST['form'] );
			
		$data = self::get_entries( $form, $page, $perpage, $status );

		// set status output
		$data['is_' . $status ] = true;

		wp_send_json( $data );
		exit;


	}

	/**
	 * Get entries from a form
	 *
	 * @since 1.2.1
	 *
	 * @param string|array $form Form ID or form config.
	 * @param int $page Optional. Page of entries to get per page. Default is 1.
	 * @param int $perpage Optional. Number of entries per page. Default is 20.
	 * @param string $status Optional. Form status. Default is active.
	 *
	 * @return array
	 */
	public static function get_entries( $form, $page = 1, $perpage = 20, $status = 'active' ) {

		if ( is_string( $form ) ) {
			$form = Caldera_Forms::get_form( $form );
		}

		if ( isset( $form[ 'ID' ])) {
			$form_id = $form[ 'ID' ];
		}else{
			return;
		}

		global $wpdb;

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
				$has_vars = array();
				if( !empty( $form['variables']['types'] ) ){
					$has_vars = $form['variables']['types'];
				}
				if( ( count($backup_labels) < 4 && !in_array( 'entryitem', $has_vars ) ) && in_array($field['type'], array('text','email','date','name'))){
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

		// status
		if(!empty($status)){
			$status = $wpdb->prepare("%s", $status );
		}

		$data['trash'] = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(`id`) AS `total` FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `form_id` = %s AND `status` = 'trash';", $form_id ) );
		$data['active'] = (int) $wpdb->get_var($wpdb->prepare("SELECT COUNT(`id`) AS `total` FROM `" . $wpdb->prefix . "cf_form_entries` WHERE `form_id` = %s AND `status` = 'active';", $form_id ) );


		// set current total
		if(!empty( $status ) && isset($data[ $status ])){
			$data['total'] = $data[ $status ];
		}else{
			$data['total'] = $data['active'];
		}


		$data['pages'] = ceil($data['total'] / $perpage );

		if(!empty( $page )){
			$page = abs( $page );
			if($page > $data['pages']){
				$page = $data['pages'];
			}
		}

		$data['current_page'] = $page;
		$gmt_offset = get_option( 'gmt_offset' );
		if($data['total'] > 0){

			$data['form'] = $form_id;

			$data['fields'] = $field_labels;
			$offset = ($page - 1) * $perpage;
			$limit = $offset . ',' . $perpage;



			$rawdata = $wpdb->get_results($wpdb->prepare("
			SELECT
				`id`,
				`form_id`
			FROM `" . $wpdb->prefix ."cf_form_entries`

			WHERE `form_id` = %s AND `status` = ".$status." ORDER BY `datestamp` DESC LIMIT " . $limit . ";", $form_id));

			if(!empty($rawdata)){

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
					$data['entries']['E' . $row->_entryid]['_date'] = date_i18n( $dateformat.' '.$timeformat, get_date_from_gmt( $row->_date_submitted, 'U'));

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
						// filter the field to get field data
						$field = apply_filters( 'caldera_forms_render_get_field', $field, $form);
						$field = apply_filters( 'caldera_forms_render_get_field_type-' . $field['type'], $field, $form);
						$field = apply_filters( 'caldera_forms_render_get_field_slug-' . $field['slug'], $field, $form);

						// maybe json?
						$is_json = json_decode( $row->value, ARRAY_A );
						if( !empty( $is_json ) ){
							$row->value = $is_json;
						}

						if( is_string( $row->value ) ){
							$row->value = esc_html( stripslashes_deep( $row->value ) );
						}else{
							$row->value = stripslashes_deep( Caldera_Forms_Sanitize::sanitize( $row->value ) );
						}

						$row->value = apply_filters( 'caldera_forms_view_field_' . $field['type'], $row->value, $field, $form);


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

					if( !empty( $form['variables']['types'] ) ){
						foreach( $form['variables']['types'] as $var_key=>$var_type ){
							if( $var_type == 'entryitem' ){
								$data['fields'][$form['variables']['keys'][$var_key]] = ucwords( str_replace( '_', ' ', $form['variables']['keys'][$var_key] ) );
								$data['entries']['E' . $row->_entryid]['data'][$form['variables']['keys'][$var_key]] = Caldera_Forms::do_magic_tags( $form['variables']['values'][$var_key], $row->_entryid );
							}
						}
					}


				}
			}
		}


		return $data;

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
		
		$forms = Caldera_Forms::get_forms();

		// get current user
		if( current_user_can( 'manage_options' ) ){
		
			$this->screen_prefix[] = add_menu_page( __('Caldera Forms', 'caldera-forms'), __('Caldera Forms', 'caldera-forms'), 'manage_options', $this->plugin_slug, array( $this, 'render_admin' ), 'dashicons-cf-logo', 52.999 );
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
		}else{
			// not an admin - pin for user
			if( ! empty( $forms ) ){
				$user = wp_get_current_user();
				if(empty($user->roles)){
					// no role - bye bye.
					return;
				}

				foreach($forms as $form_id=>$form){
					$capability = null;
					if(!empty($form['pinned']) && !empty( $form['pin_roles'] ) ){

						foreach ($form['pin_roles']['access_role'] as $role => $enabled) {
							if( in_array( $role, $user->roles ) ){
								$role_details = get_role( $role );
								if(empty($role_details->capabilities)){
									continue;
								}
								$capabilities = array_keys( $role_details->capabilities );
								$capability = $capabilities[0];
								break;
							}
						}

						if( empty($capability)){
							// not this one.
							continue;
						}

						if( empty( $this->screen_prefix ) ){
							// make top menu
							$main_slug = $this->plugin_slug . '-pin-' . $form_id;
							$this->screen_prefix[] = add_menu_page( __('Caldera Forms', 'caldera-forms'), __('Caldera Forms', 'caldera-forms'), $capability, $main_slug, array( $this, 'render_admin' ), 'dashicons-cf-logo', 52.999 );
							
						}

						$this->screen_prefix[] 	 = add_submenu_page( $main_slug, __('Caldera Forms', 'caldera-forms').' - ' . $form['name'], $form['name'], $capability, $this->plugin_slug . '-pin-' . $form_id, array( $this, 'render_admin' ) );
					
					}
				}
			}
		}


	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 *
	 * @return    null
	 */
	public function enqueue_admin_stylescripts() {

		$screen = get_current_screen();

		wp_enqueue_style( $this->plugin_slug .'-admin-icon-styles', CFCORE_URL . 'assets/css/dashicon.css', array(), self::VERSION );

		if($screen->base === 'post'){
			wp_enqueue_style( $this->plugin_slug .'-modal-styles', CFCORE_URL . 'assets/css/modals.css', array(), self::VERSION );
			wp_enqueue_script( $this->plugin_slug .'-shortcode-insert', CFCORE_URL . 'assets/js/shortcode-insert.min.js', array('jquery'), self::VERSION );
			//add_editor_style( CFCORE_URL . 'assets/css/caldera-form.css' );
			add_editor_style( CFCORE_URL . 'assets/css/caldera-grid.css' );
			add_editor_style( CFCORE_URL . 'assets/css/dashicon.css' );
			// get fields

			$field_types = Caldera_Forms::get_field_types();

			foreach($field_types as $field_type){
				//enqueue styles
				if( !empty( $field_type['styles'])){
					foreach($field_type['styles'] as $style){
						add_editor_style( $style );
					}
				}

			}

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
		wp_enqueue_style( $this->plugin_slug .'-field-styles', CFCORE_URL . 'assets/css/fields.min.css', array(), self::VERSION );

		/* standalone scripts
		wp_enqueue_script( $this->plugin_slug .'-admin-scripts', CFCORE_URL . 'assets/js/admin.js', array(), self::VERSION );
		wp_enqueue_script( $this->plugin_slug .'-handlebars', CFCORE_URL . 'assets/js/handlebars.js', array(), self::VERSION );
		wp_enqueue_script( $this->plugin_slug .'-baldrick-handlebars', CFCORE_URL . 'assets/js/handlebars.baldrick.js', array($this->plugin_slug .'-baldrick'), self::VERSION );
		wp_enqueue_script( $this->plugin_slug .'-baldrick-modals', CFCORE_URL . 'assets/js/modals.baldrick.js', array($this->plugin_slug .'-baldrick'), self::VERSION );
		wp_enqueue_script( $this->plugin_slug .'-baldrick', CFCORE_URL . 'assets/js/jquery.baldrick.js', array('jquery'), self::VERSION );
		*/
		wp_enqueue_script( $this->plugin_slug .'-baldrick', CFCORE_URL . 'assets/js/wp-baldrick-full.js', array('jquery'), self::VERSION );
		wp_enqueue_script( $this->plugin_slug .'-admin-scripts', CFCORE_URL . 'assets/js/admin.min.js', array( $this->plugin_slug .'-baldrick' ), self::VERSION );

		if(!empty($_GET['edit'])){

			/*// editor specific styles
			wp_enqueue_script( $this->plugin_slug .'-edit-fields', CFCORE_URL . 'assets/js/edit.js', array('jquery'), self::VERSION );
			*/
			wp_enqueue_script( $this->plugin_slug .'-edit-fields', CFCORE_URL . 'assets/js/fields.min.js', array('jquery'), self::VERSION );
			
			wp_enqueue_script( $this->plugin_slug .'-edit-editor', CFCORE_URL . 'assets/js/edit.min.js', array('jquery'), self::VERSION );


			wp_enqueue_script( 'jquery-ui-users' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-droppable' );

		}
		if(!empty($_GET['edit-entry'])){
			wp_enqueue_style( 'cf-grid-styles', CFCORE_URL . 'assets/css/caldera-grid.css', array(), self::VERSION );
		}

		
			// Load Field Types Styles & Scripts
			$field_types = apply_filters( 'caldera_forms_get_field_types', array() );

			// load panels
			$panel_extensions = apply_filters( 'caldera_forms_get_panel_extensions', array() );

			// load processors
			$form_processors = apply_filters( 'caldera_forms_get_form_processors', array() );

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
								if('//' != substr( $style, 0, 2) && file_exists( $style )){
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
								if('//' != substr( $script, 0, 2) && file_exists( $script )){
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
			$form = Caldera_Forms::get_form( $formID );
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
		if(!empty($_GET['delete']) && !empty($_GET['cal_del']) && current_user_can( 'manage_options' ) ){

			if ( ! wp_verify_nonce( $_GET['cal_del'], 'cf_del_frm' ) ) {
				// This nonce is not valid.
				wp_die( __('Sorry, please try again', 'caldera-forms'), __('Form Delete Error', 'caldera-forms') );
			}else{
				// ok to delete
				// get form registry
				$forms = Caldera_Forms::get_forms( true );
				if(isset($forms[$_GET['delete']])){
					unset($forms[$_GET['delete']]);
					$form = Caldera_Forms::get_form( $_GET['delete'] );
					if(empty($form)){
						do_action('caldera_forms_delete_form', $_GET['delete']);
						update_option( '_caldera_forms', $forms );
					}else{
						if( delete_option( $_GET['delete'] ) ){
							do_action('caldera_forms_delete_form', $_GET['delete']);
							update_option( '_caldera_forms', $forms );						
						}
					}
				}

				wp_redirect('admin.php?page=caldera-forms' );
				exit;

			}
			
		}
		if( isset($_POST['cfimporter']) && current_user_can( 'manage_options' ) ){

			if ( check_admin_referer( 'cf-import', 'cfimporter' ) ) {
				if(!empty($_FILES['import_file']['size'])){
					$loc = wp_upload_dir();
					if(move_uploaded_file($_FILES['import_file']['tmp_name'], $loc['path'].'/cf-form-import.json')){
						$data = json_decode(file_get_contents($loc['path'].'/cf-form-import.json'), true);
						if(isset($data['ID']) && isset($data['name']) ){

							// generate a new ID
							$data['ID'] = uniqid('CF');
							$data['name'] = $_POST['name'];


							// rebuild field IDS
							/*
							if( !empty( $data['fields'] ) ){
								$old_fields = array();
								$fields 	= $data['fields'];								
								$layout_fields = $data['layout_grid']['fields'];
								$data['layout_grid']['fields'] = $data['fields'] = array();
								foreach( $fields as $field ){									
									$field_id = uniqid('fld_');
									$old_fields[$field['ID']] = $field_id;

									$data['layout_grid']['fields'][$field_id] = $layout_fields[$field['ID']];
									$field['ID'] = $field_id;
									$data['fields'][$field_id] = $field;

								}


								foreach( $data['fields'] as $field ){
									// rebind conditions
									if( !empty( $field['conditions']['group'] ) ){
										foreach( $field['conditions']['group'] as $group_id=>$group ){
											foreach( $group as $group_line_id=>$group_line ){
												$data['fields'][$field['ID']]['conditions']['group'][$group_id][$group_line_id]['field'] = $old_fields[$group_line['field']];
											}
										}
									}
								}

							}
							// rebuild processor IDS
							if( !empty( $data['processors'] ) ){
								
								$processors 	= $data['processors'];								
								$data['processors'] = array();
								$old_processors = array();
								foreach( $processors as $processor ){
									$processor_id = uniqid('fp_');
									$old_processors[$processor['ID']] = $processor_id;
									$processor['ID'] = $processor_id;									
									// fix binding
									if( !empty( $processor['config'] ) && !empty( $data['fields'] ) ){
										foreach( $processor['config'] as $config_key => &$config_value ){
											if( is_string($config_value) ){
												foreach( $old_fields as $old_field=>$new_field ){
													$config_value = str_replace( $old_field, $new_field, $config_value );
												}
											}
										}
									}
									$data['processors'][$processor_id] = $processor;
								}
								// fix processor bindings
								foreach( $data['processors'] as &$processor ){
									if( !empty( $processor['config'] ) ){
										foreach( $processor['config'] as $config_key => &$config_value ){
											if( is_string($config_value) ){
												foreach( $old_processors as $old_proc=>$new_proc ){
													$config_value = str_replace( $old_proc, $new_proc, $config_value );
												}
											}
										}
									}
								}
								// fix field - processor bindings
								if( !empty( $data['fields'] ) ){
									foreach( $data['fields'] as &$field ){
										if( !empty( $field['config'] ) ){
											foreach( $field['config'] as $config_key => &$config_value ){
												if( is_string($config_value) ){
													foreach( $old_processors as $old_proc=>$new_proc ){
														$config_value = str_replace( $old_proc, $new_proc, $config_value );
													}
												}
											}
										}
									}
								}
							}
							*/
							// get form registry
							$forms = Caldera_Forms::get_forms( true );
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
		if(!empty($_GET['export-form']) && current_user_can( 'manage_options' )){

			$form = Caldera_Forms::get_form( $_GET['export-form'] );

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

		if(!empty($_GET['export']) && current_user_can( 'manage_options') ){

			$form = Caldera_Forms::get_form( $_GET['export'] );

			global $wpdb;

			//build labels
			$labels = array();
			$structure = array();
			$field_types = apply_filters( 'caldera_forms_get_field_types', array());
			if(!empty($form['fields'])){
				$headers['date_submitted'] = 'Submitted';
				foreach($form['fields'] as $field_id=>$field){
					if(isset($field_types[$field['type']]['capture']) &&  false === $field_types[$field['type']]['capture']){
						continue;
					}
					$headers[$field['slug']] = $field['label'];
					$structure[$field['slug']] = $field_id;
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
				`entry`.`user_id` AS `_user_id`

			FROM `" . $wpdb->prefix ."cf_form_entries` AS `entry`
			

			WHERE `entry`.`form_id` = %s
			" . $filter . "
			AND `entry`.`status` = 'active'
			ORDER BY `entry`.`datestamp` DESC;", $_GET['export']));

			$data = array();

			foreach( $rawdata as $entry){
				$submission = Caldera_Forms::get_entry( $entry->_entryid, $form);
				$data[$entry->_entryid]['date_submitted'] = $entry->_date_submitted;

				foreach ($structure as $slug => $field_id) {
					$data[$entry->_entryid][$slug] = ( isset( $submission['data'][$field_id]['value'] ) ? $submission['data'][$field_id]['value'] : null );
				}
				//dump($data);
			}

			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private",false);
			header("Content-Type: text/csv charset=utf-8;");
			header("Content-Disposition: attachment; filename=\"" . sanitize_file_name( $form['name'] ) . ".csv\";" );
			header("Content-Transfer-Encoding: binary");
			$df = fopen("php://output", 'w');
			fputcsv($df, $headers);
			foreach($data as $row){
				$csvrow = array();
				foreach($headers as $key=>$label){
					if(!isset($row[$key])){
						$row[$key] = null;
					}else{
						if( is_array( $row[$key] ) && isset( $row[$key]['label'] ) ){
							$row[$key] = $row[$key]['value'];
						}elseif( is_array( $row[$key] ) && count( $row[$key] ) === 1 ){
							$row[$key] = $row[$key][0];
						}elseif( is_array( $row[$key] ) ){
							$subs = array();
							foreach( $row[$key] as $row_part ){
								if( is_array( $row_part ) && isset( $row_part['label'] ) ){
									$subs[] = $row_part['value'];
								}else{
									$subs[] = $row_part;
								}
							}
							$row[$key] = implode(', ', $subs );
						}
					}

					$csvrow[] = $row[$key];
				}
				fputcsv($df, $row);
			}
			fclose($df);			
			exit;			
		}

		if( isset($_POST['config']) && isset( $_POST['cf_edit_nonce'] ) && current_user_can( 'manage_options' ) ){
			
			// if this fails, check_admin_referer() will automatically print a "failed" page and die.
			if ( check_admin_referer( 'cf_edit_element', 'cf_edit_nonce' ) ) {
				
				// strip slashes
				$data = json_decode( stripslashes_deep($_POST['config']) , ARRAY_A );
				// get form registry
				$forms = Caldera_Forms::get_forms( true );
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

		// get form templates
		$form_templates = self::internal_form_templates();

		// get form registry
		$forms = Caldera_Forms::get_forms( true );
		if(empty($forms)){
			$forms = array();
		}
		if(!empty($newform['clone'])){
			$clone = $newform['clone'];
		}
		// load template if any
		if( !empty( $newform['template'] ) ){
			if( isset( $form_templates[ $newform['template'] ] ) && !empty( $form_templates[ $newform['template'] ]['template'] ) ){
				$form_template = $form_templates[ $newform['template'] ]['template'];
			}
		}
		$newform = array(
			"ID" 			=> uniqid('CF'),
			"name" 			=> $newform['name'],
			"description" 	=> $newform['description'],
			"success"		=>	__('Form has been successfully submitted. Thank you.', 'caldera-forms'),
			"form_ajax"		=> 1,
			"hide_form"		=> 1
		);
		// is template?
		if( !empty( $form_template ) && is_array( $form_template ) ){
			$newform = array_merge( $form_template, $newform );
		}
		// add from to list
		$newform = apply_filters( 'caldera_forms_create_form', $newform);

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
						//CFCORE_URL . "assets/js/processors-edit.js",
						//CFCORE_URL . "assets/js/layout-grid.js"
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


