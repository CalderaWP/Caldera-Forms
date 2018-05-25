<?php

/**
 * Access to field definitions
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Fields {

	/**
	 * Get all field definitions
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public static function get_all() {

		/**
		 * Register or remove field types
		 *
		 * @since unknown
		 *
		 * @param array $field_types Field types
		 */
		$field_types = apply_filters( 'caldera_forms_get_field_types', self::internal_types() );


		if ( ! empty( $field_types ) ) {
			foreach ( $field_types as $fieldType => $fieldConfig ) {
				// check for a viewer
				if ( isset( $fieldConfig[ 'viewer' ] ) ) {
					add_filter( 'caldera_forms_view_field_' . $fieldType, $fieldConfig[ 'viewer' ], 10, 3 );
				}
			}
		}

		return $field_types;

	}

	/**
	 * Get definition of one field
	 *
	 * @since 1.5.0
	 *
	 * @param string $type Field type
	 *
	 * @return array
	 */
	public static function definition( $type ){
		$fields = self::get_all();
		if( array_key_exists( $type, $fields ) ){
			return $fields[ $type ];
		}

		return array();

	}

	/**
	 * Check if a field definition has defined a specific "not support" argument
	 *
	 * Use to check if field of $type does $not_support
	 *
	 * @since 1.5.0
	 *
	 * @param string $type The field type
	 * @param string $not_support The not support argument, for example "entry_list"
	 *
	 * @return bool|null True if not supported, false if not not supported. Null if invalid field type
	 */
	public static function not_support( $type, $not_support ){
		$field = self::definition( $type );
		if( ! empty( $field ) ){
			if( ! isset( $field[ 'setup' ], $field[ 'setup' ][ 'not_supported' ] )  ){
				return false;
			}
			if( ! empty(  $field[ 'setup' ][ 'not_supported' ] ) &&  in_array( $not_support, $field[ 'setup' ][ 'not_supported' ] )  ){
				return true;
			}

			return false;
		}

		return null;

	}

	/**
	 * Get internal field types without filter
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	public static function internal_types() {
		$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.min' : '';

		$internal_fields = array(
			//basic
			'text'               => self::get_text_config(),
			'hidden'             => self::get_hidden_config(),
			'email'              => self::get_email_config(),
			'button'             => self::get_button_config(),
			'number'             => self::get_number_config(),
			'phone'              => self::get_phone_config(),
			'phone_better'       => self::get_phone_config_v2(),
			'paragraph'          => self::get_paragraph_config(),
			'wysiwyg'            => self::get_wysiwyg_config(),
			'url'                => self::get_url_config(),

			//eCommerce
			'credit_card_number' => self::get_credit_card_number_config(),
			'credit_card_exp'    => self::get_credit_card_expiration_config(),
			'credit_card_cvc'    => self::get_credit_card_cvc_config(),

			//special
			'calculation'        => self::get_calculation_config(),
			'range_slider'       => self::get_range_slider_config(),
			'star_rating'        => self::get_star_rating_config(),
			'utm'                => self::get_utm_config(),
			'gdpr'               => self::get_gdpr_config(),

			//file
			'file'               => self::get_file_uploader_config(),
			'advanced_file'      => self::get_advanced_file_uploader_config(),

			//content
			'html'               => self::get_html_config(),
			'summary'            => self::get_summary_config(),
			'section_break'      => self::get_section_break_config(),

			//select
			'dropdown'           => self::get_dropdown_config(),
			'checkbox'           => self::get_checkbox_config(),
			'radio'              => self::get_radio_config(),
			'filtered_select2'   => self::get_filtered_select2_config(),
			'date_picker'        => self::get_date_picker_config(),
			'toggle_switch'      => self::get_toggle_switch_config(),
			'color_picker'       => self::get_colorpicker_config(),
			'states'             => self::get_states_config(),

			//discontinued
			'recaptcha'          => self::get_recaptcha_config(),
		);

		return $internal_fields;
	}

	public static function get_text_config()
	{
		return array(
			"field"       => __( 'Single Line Text', 'caldera-forms' ),
			"description" => __( 'Single Line Text', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/generic-input.php",
			"category"    => __( 'Basic', 'caldera-forms' ),
			"setup"       => array(
				"template" => CFCORE_PATH . "fields/text/config.php",
				"preview"  => CFCORE_PATH . "fields/text/preview.php"
			),
		);
	}

	public static function get_hidden_config()
	{
		return array(
			"field"       => __( 'Hidden', 'caldera-forms' ),
			"description" => __( 'Hidden', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/hidden/field.php",
			"category"    => __( 'Basic', 'caldera-forms' ),
			"static"      => true,
			"setup"       => array(
				"preview"       => CFCORE_PATH . "fields/hidden/preview.php",
				"template"      => CFCORE_PATH . "fields/hidden/setup.php",
				"not_supported" => array(
					'hide_label',
					'caption',
					'required',
				)
			)
		);
	}

	public static function get_email_config()
	{
		return array(
			"field"       => __( 'Email Address', 'caldera-forms' ),
			"description" => __( 'Email Address', 'caldera-forms' ),
			'icon'        => CFCORE_URL . 'assets/build/images/envelope-o.svg',
			"file"        => CFCORE_PATH . "fields/generic-input.php",
			"category"    => __( 'Basic', 'caldera-forms' ),
			"setup"       => array(
				"preview"  => CFCORE_PATH . "fields/email/preview.php",
				"template" => CFCORE_PATH . "fields/email/config.php"
			)
		);
	}

	public static function get_button_config()
	{
		return array(
			"field"       => __( 'Button', 'caldera-forms' ),
			"description" => __( 'Button, Submit and Reset types', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/button/field.php",
			"category"    => __( 'Basic', 'caldera-forms' ),
			"capture"     => false,
			"setup"       => array(
				"template"      => CFCORE_PATH . "fields/button/config_template.php",
				"preview"       => CFCORE_PATH . "fields/button/preview.php",
				"default"       => array(
					'class' => 'btn btn-default',
					'type'  => 'submit'
				),
				"not_supported" => array(
					'hide_label',
					'caption',
					'required',
					'entry_list'
				)
			)
		);
	}

	public static function get_number_config()
	{
		return array(
			"field"       => __( 'Number', 'caldera-forms' ),
			"description" => __( 'Number with minimum and maximum controls', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/generic-input.php",
			"category"    => __( 'Basic', 'caldera-forms' ),
			"setup"       => array(
				"preview"  => CFCORE_PATH . "fields/number/preview.php",
				"template" => CFCORE_PATH . "fields/number/config.php"
			)
		);
	}

	public static function get_phone_config()
	{
		return array(
			"field"       => __( 'Phone Number (Basic)', 'caldera-forms' ),
			"description" => __( 'Phone number with masking', 'caldera-forms' ),
			'icon'        => CFCORE_URL . 'assets/build/images/volume-control-phone.svg',
			"file"        => CFCORE_PATH . "fields/generic-input.php",
			"category"    => __( 'Basic', 'caldera-forms' ),
			"setup"       => array(
				"template" => CFCORE_PATH . "fields/phone/config.php",
				"preview"  => CFCORE_PATH . "fields/phone/preview.php",
				"default"  => array(
					'default' => '',
					'type'    => 'local',
					'custom'  => '(999)999-9999'
				)
			)
		);
	}

	public static function get_phone_config_v2()
	{
		return array(
			"field"       => __( 'Phone Number (Better)', 'caldera-forms' ),
			"description" => __( 'Phone number with advanced options and international formatting', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/generic-input.php",
			"category"    => __( 'Basic', 'caldera-forms' ),
			'icon'        => CFCORE_URL . 'assets/build/images/mobile.svg',
			"setup"       => array(
				"template" => CFCORE_PATH . "fields/phone_better/config.php",
				"preview"  => CFCORE_PATH . "fields/phone_better/preview.php",
				"default"  => array(
					'default' => '',

				)
			),
			"scripts"     => array(
				CFCORE_URL . 'fields/phone_better/assets/js/intlTelInput.min.js',
			),
			"styles"      => array(
				CFCORE_URL . 'fields/phone_better/assets/css/intlTelInput.css'
			),
		);
	}

	public static function get_paragraph_config()
	{
		return array(
			"field"       => __( 'Paragraph Textarea', 'caldera-forms' ),
			"description" => __( 'Paragraph Textarea', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/paragraph/field.php",
			"category"    => __( 'Basic', 'caldera-forms' ),
			"setup"       => array(
				"template" => CFCORE_PATH . "fields/paragraph/config_template.php",
				"preview"  => CFCORE_PATH . "fields/paragraph/preview.php",
				"default"  => array(
					'rows' => '4'
				),
			)
		);
	}

	public static function get_wysiwyg_config()
	{
		return array(
			"field"       => __( 'Rich Editor', 'caldera-forms' ),
			"description" => __( 'TinyMCE WYSIWYG editor', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/wysiwyg/field.php",
			'icon'        => CFCORE_URL . 'assets/build/images/align-justify.svg',
			"category"    => __( 'Basic', 'caldera-forms' ),
			"setup"       => array(
				"template" => CFCORE_PATH . "fields/wysiwyg/config_template.php",
				"preview"  => CFCORE_PATH . "fields/wysiwyg/preview.php",
			),
			"scripts"     => array(
				CFCORE_URL . 'fields/wysiwyg/wysiwyg.js'
			),
			"styles"      => array(
				CFCORE_URL . "fields/wysiwyg/wysiwyg.min.css",
			),
		);
	}

	public static function get_url_config()
	{
		return array(
			"field"       => __( 'URL', 'caldera-forms' ),
			'icon'        => CFCORE_URL . 'assets/build/images/chain.svg',
			"description" => __( 'URL input for website addresses', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/generic-input.php",
			"category"    => __( 'Basic', 'caldera-forms' ),
			"setup"       => array(
				"preview"  => CFCORE_PATH . "fields/url/preview.php",
				"template" => CFCORE_PATH . "fields/url/config.php"
			)
		);
	}

	public static function get_credit_card_number_config()
	{
		return array(
			"field"       => __( 'Credit Card Number', 'caldera-forms' ),
			"description" => __( 'Credit Card Number With Validation', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/generic-input.php",
			"category"    => __( 'eCommerce', 'caldera-forms' ),
			'icon'        => CFCORE_URL . 'assets/build/images/credit-card.svg',
			"setup"       => array(
				"template" => CFCORE_PATH . "fields/credit_card_number/config.php",
				"preview"  => CFCORE_PATH . "fields/credit_card_number/preview.php"
			),
			"scripts" => array(
				CFCORE_URL . 'fields/credit_card_number/credit-card.js'
			)
		);
	}

	public static function get_credit_card_expiration_config()
	{
		return array(
			"field"       => __( 'Credit Card Expiration Date', 'caldera-forms' ),
			"description" => __( 'Credit Card Expiration Date With Validation', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/generic-input.php",
			'icon'        => CFCORE_URL . 'assets/build/images/credit-card.svg',
			"category"    => __( 'eCommerce', 'caldera-forms' ),
			"setup"       => array(
				"template" => CFCORE_PATH . "fields/credit_card_exp/config.php",
				"preview"  => CFCORE_PATH . "fields/credit_card_exp/preview.php"
			),
			"scripts" => array(
				CFCORE_URL . 'fields/credit_card_number/credit-card.js'
			)
		);
	}


	public static function get_credit_card_cvc_config()
	{
		return array(
			"field"       => __( 'Credit Card CVC', 'caldera-forms' ),
			"description" => __( 'Credit Card CVC With Validation', 'caldera-forms' ),
			'icon'        => CFCORE_URL . 'assets/build/images/credit-card.svg',
			"file"        => CFCORE_PATH . "fields/generic-input.php",
			"category"    => __( 'eCommerce', 'caldera-forms' ),
			"setup"       => array(
				"template" => CFCORE_PATH . "fields/credit_card_cvc/config.php",
				"preview"  => CFCORE_PATH . "fields/credit_card_cvc/preview.php"
			),
			"scripts" => array(
				CFCORE_URL . 'fields/credit_card_number/credit-card.js'
			)
		);
	}

	public static function get_calculation_config()
	{
		return array(
			"field"       => __( 'Calculation', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/calculation/field.php",
			"handler"     => array( Caldera_Forms::get_instance(), "run_calculation" ),
			'icon'        => CFCORE_URL . 'assets/build/images/calculator.svg',
			"category"    => __( 'Special', 'caldera-forms' ),
			"description" => __( 'Calculate values', 'caldera-forms' ),
			"setup"       => array(
				"template" => CFCORE_PATH . "fields/calculation/config.php",
				"preview"  => CFCORE_PATH . "fields/calculation/preview.php",
				"default"  => array(
					'element' => 'h3',
					'classes' => 'total-line',
					'before'  => __( 'Total', 'caldera-forms' ) . ':',
					'after'   => ''
				),

			),
		);
	}

	public static function get_range_slider_config()
	{
		return array(
			"field"       => __( 'Range Slider', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/range_slider/field.php",
			"category"    => __( 'Special', 'caldera-forms' ),
			"description" => __( 'Range Slider input field', 'caldera-forms' ),
			"setup"       => array(
				"template" => CFCORE_PATH . "fields/range_slider/config.php",
				"preview"  => CFCORE_PATH . "fields/range_slider/preview.php",
				"default"  => array(
					'default'      => 1,
					'step'         => 1,
					'min'          => 0,
					'max'          => 100,
					'showval'      => 1,
					'suffix'       => '',
					'prefix'       => '',
					'color'        => '#00ff00',
					'handle'       => '#ffffff',
					'handleborder' => '#cccccc',
					'trackcolor'   => '#e6e6e6'
				),
			),
			"styles"      => array(
				CFCORE_URL . "fields/range_slider/rangeslider.min.css",
			),
			"scripts"      => array(
				CFCORE_URL . "fields/range_slider/rangeslider.min.js",
			),
		);
	}

	public static function get_star_rating_config()
	{
		return array(
			"field"       => __( 'Star Rating', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/star-rate/field.php",
			"category"    => __( 'Special', 'caldera-forms' ),
			'icon'        => CFCORE_URL . 'assets/build/images/star.svg',
			"description" => __( 'Star rating input for feedback', 'caldera-forms' ),
			"viewer"      => array( Caldera_Forms::get_instance(), 'star_rating_viewer' ),
			"setup"       => array(
				"template" => CFCORE_PATH . "fields/star-rate/config.php",
				"preview"  => CFCORE_PATH . "fields/star-rate/preview.php",
				"default"  => array(
					'number'      => 5,
					'space'       => 3,
					'size'        => 13,
					'color'       => '#FFAA00',
					'track_color' => '#AFAFAF',
					'type'        => 'star',
				),
			),
			"scripts"     => array(
				CFCORE_URL . "fields/star-rate/jquery.raty.js",
			),
			"styles"      => array(
				CFCORE_URL . "fields/star-rate/cf-raty.css",
			),
		);
	}

	public static function get_utm_config()
	{
		return array(
			'field'       => __( 'UTM', 'caldera-forms' ),
			'file'        => CFCORE_PATH . 'fields/utm/field.php',
			'category'    => __( 'Special', 'caldera-forms' ),
			'description' => __( 'Capture all UTM tags', 'caldera-forms' ),
			'setup'       => array(
				'template'      => CFCORE_PATH . 'fields/utm/config.php',
				'preview'       => CFCORE_PATH . 'fields/utm/preview.php',
				'not_supported' => array(
					'hide_label',
					'caption',
					'required',
				)
			),
			'handler'     => array( 'Caldera_Forms_Field_Utm', 'handler' )
		);
	}

	public static function get_gdpr_config()
	{
		return array(
			"field"       => __( 'Consent Field', 'caldera-forms' ),
			"description" => __( 'Record consent to collect personally identifying information (PII) for GDPR Compliance.', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/gdpr/field.php",
			"category"    => __( 'Special', 'caldera-forms' ),
			"setup"       => array(
				"template" => CFCORE_PATH . "fields/gdpr/config_template.php",
				"preview"  => CFCORE_PATH . "fields/gdpr/preview.php",
				"not_supported" => array(
					'caption',
					'required',
				)
			),
		);
	}

	public static function get_file_uploader_config()
	{
		return array(
			"field"       => __( 'File', 'caldera-forms' ),
			"description" => __( 'File Uploader', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/file/field.php",
			'icon'        => CFCORE_URL . 'assets/build/images/cloud-upload.svg',
			"viewer"      => array( Caldera_Forms::get_instance(), 'handle_file_view' ),
			"category"    => __( 'File', 'caldera-forms' ),
			"setup"       => array(
				"preview"  => CFCORE_PATH . "fields/file/preview.php",
				"template" => CFCORE_PATH . "fields/file/config_template.php"
			)
		);
	}

	public static function get_advanced_file_uploader_config()
	{
		return array(
			"field"       => __( 'Advanced File Uploader', 'caldera-forms' ),
			'icon'        => CFCORE_URL . 'assets/build/images/cloud-upload.svg',
			"description" => __( 'Inline, multi file uploader', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/advanced_file/field.php",
			"viewer"      => array( Caldera_Forms::get_instance(), 'handle_file_view' ),
			"category"    => __( 'File', 'caldera-forms' ),
			"setup"       => array(
				"preview"  => CFCORE_PATH . "fields/advanced_file/preview.php",
				"template" => CFCORE_PATH . "fields/advanced_file/config_template.php"
			),
			"scripts"     => array(
				CFCORE_URL . 'fields/advanced_file/uploader.min.js'
			),
		);
	}

	public static function get_html_config()
	{
		return array(
			"field"       => __( 'HTML', 'caldera-forms' ),
			"description" => __( 'Add text/html content', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/html/field.php",
			"category"    => __( 'Content', 'caldera-forms' ),
			"icon"        => CFCORE_URL . "fields/html/icon.png",
			"capture"     => false,
			"setup"       => array(
				"preview"       => CFCORE_PATH . "fields/html/preview.php",
				"template"      => CFCORE_PATH . "fields/html/config_template.php",
				"not_supported" => array(
					'hide_label',
					'caption',
					'required',
					'entry_list'
				)
			)
		);
	}

	public static function get_summary_config()
	{
		return array(
			"field"       => __( 'Summary', 'caldera-forms' ),
			"description" => __( 'Live updating summary of submission', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/summary/field.php",
			"category"    => __( 'Content', 'caldera-forms' ),
			'icon'        => CFCORE_URL . 'assets/build/images/list.svg',
			"capture"     => false,
			"setup"       => array(
				"preview"       => CFCORE_PATH . "fields/summary/preview.php",
				"template"      => CFCORE_PATH . "fields/summary/config.php",
				"not_supported" => array(
					'required',
					'entry_list'
				)
			)
		);
	}

	public static function get_section_break_config()
	{
		return array(
			"field"       => __( 'Section Break', 'caldera-forms' ),
			"description" => __( 'An HR tag to separate sections of your form.', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/section-break/section-break.php",
			"category"    => __( 'Content', 'caldera-forms' ),
			"capture"     => false,
			"setup"       => array(
				"template"      => CFCORE_PATH . "fields/section-break/config.php",
				"not_supported" => array(
					'hide_label',
					'caption',
					'required',
					'entry_list'
				)
			)
		);
	}

	public static function get_dropdown_config()
	{
		return array(
			"field"       => __( 'Dropdown Select', 'caldera-forms' ),
			"description" => __( 'Dropdown Select', 'caldera-forms' ),
			'icon'        => CFCORE_URL . 'assets/build/images/plus.svg',
			"file"        => CFCORE_PATH . "fields/dropdown/field.php",
			"category"    => __( 'Select', 'caldera-forms' ),
			"options"     => "single",
			"static"      => true,
			"viewer"      => array( Caldera_Forms::get_instance(), 'filter_options_calculator' ),
			"setup"       => array(
				"template" => CFCORE_PATH . "fields/dropdown/config_template.php",
				"preview"  => CFCORE_PATH . "fields/dropdown/preview.php",
				"default"  => array(),
			)
		);
	}

	public static function get_checkbox_config()
	{
		return array(
			"field"       => __( 'Checkbox', 'caldera-forms' ),
			"description" => __( 'Checkbox', 'caldera-forms' ),
			'icon'        => CFCORE_URL . 'assets/build/images/plus.svg',
			"file"        => CFCORE_PATH . "fields/checkbox/field.php",
			"category"    => __( 'Select', 'caldera-forms' ),
			"options"     => "multiple",
			"static"      => true,
			"viewer"      => array( Caldera_Forms::get_instance(), 'filter_options_calculator' ),
			"setup"       => array(
				"preview"  => CFCORE_PATH . "fields/checkbox/preview.php",
				"template" => CFCORE_PATH . "fields/checkbox/config_template.php",

			),
		);
	}

	public static function get_radio_config()
	{
		return array(
			"field"       => __( 'Radio', 'caldera-forms' ),
			"description" => __( 'Radio', 'caldera-forms' ),
			'icon'        => CFCORE_URL . 'assets/build/images/plus.svg',
			"file"        => CFCORE_PATH . "fields/radio/field.php",
			"category"    => __( 'Select', 'caldera-forms' ),
			"options"     => true,
			"static"      => true,
			"viewer"      => array( Caldera_Forms::get_instance(), 'filter_options_calculator' ),
			"setup"       => array(
				"preview"  => CFCORE_PATH . "fields/radio/preview.php",
				"template" => CFCORE_PATH . "fields/radio/config_template.php",
			)
		);
	}

	public static function get_filtered_select2_config()
	{
		return array(
			"field"       => __( 'Autocomplete', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/select2/field/field.php",
			'icon'        => CFCORE_URL . 'assets/build/images/plus.svg',
			"category"    => __( 'Select', 'caldera-forms' ),
			"description" => 'Select2 dropdown',
			"options"     => "multiple",
			"static"      => true,
			"setup"       => array(
				"template" => CFCORE_PATH . "fields/select2/field/config.php",
				"preview"  => CFCORE_PATH . "fields/select2/field/preview.php",
			),
			"scripts"     => array(
				CFCORE_URL . "fields/select2/js/select2.min.js",
			),
			"styles"      => array(
				CFCORE_URL . "fields/select2/css/select2.css",
			)
		);
	}

	public static function get_date_picker_config()
	{
		return array(
			"field"       => __( 'Date Picker', 'caldera-forms' ),
			"description" => __( 'Date Picker', 'caldera-forms' ),
			'icon'        => CFCORE_URL . 'assets/build/images/plus.svg',
			"file"        => CFCORE_PATH . "fields/date_picker/datepicker.php",
			"category"    => __( 'Select', 'caldera-forms' ),
			"setup"       => array(
				"preview"  => CFCORE_PATH . "fields/date_picker/preview.php",
				"template" => CFCORE_PATH . "fields/date_picker/setup.php",
				"default"  => array(
					'format' => 'yyyy-mm-dd'
				),
			),
			"styles"     => array(
				CFCORE_URL . "fields/date_picker/css/datepicker.css",
			),
			"scripts"      => array(
				CFCORE_URL . "fields/date_picker/cf-datepicker.js",
			)
		);
	}

	public static function get_toggle_switch_config()
	{
		return array(
			"field"       => __( 'Toggle Switch', 'caldera-forms' ),
			"description" => __( 'Toggle Switch', 'caldera-forms' ),
			"category"    => __( 'Select', 'caldera-forms' ),
			'icon'        => CFCORE_URL . 'assets/build/images/plus.svg',
			"file"        => CFCORE_PATH . "fields/toggle_switch/field.php",
			"viewer"      => array( Caldera_Forms::get_instance(), 'filter_options_calculator' ),
			"options"     => "single",
			"static"      => true,
			"setup"       => array(
				"template" => CFCORE_PATH . "fields/toggle_switch/config_template.php",
				"preview"  => CFCORE_PATH . "fields/toggle_switch/preview.php",
			),
		);
	}

	public static function get_colorpicker_config()
	{
		return array(
			"field"       => __( 'Color Picker', 'caldera-forms' ),
			"description" => __( 'Color Picker', 'caldera-forms' ),
			'icon'        => CFCORE_URL . 'assets/build/images/paint-brush.svg',
			"category"    => __( 'Select', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/generic-input.php",
			"setup"       => array(
				"preview"  => CFCORE_PATH . "fields/color_picker/preview.php",
				"template" => CFCORE_PATH . "fields/color_picker/setup.php",
				"default"  => array(
					'default' => '#FFFFFF'
				),
			),
			'styles' => array(
				CFCORE_URL . 'fields/color_picker/minicolors.min.css'
			),
			'scripts' => array(
				CFCORE_URL . 'fields/color_picker/minicolors.js'
			)
		);
	}

	public static function get_states_config()
	{
		return array(
			"field"       => __( 'State/ Province Select', 'caldera-forms' ),
			'icon'        => CFCORE_URL . 'assets/build/images/plus.svg',
			"description" => __( 'Dropdown select for US states and Canadian provinces.', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/states/field.php",
			"category"    => __( 'Select', 'caldera-forms' ),
			"placeholder" => false,
			"setup"       => array(
				"template" => CFCORE_PATH . "fields/states/config_template.php",
				"preview"  => CFCORE_PATH . "fields/states/preview.php",
				"default"  => array(),
			)
		);
	}
	public static function get_recaptcha_config()
	{
		return array(
			"field"       => __( 'reCAPTCHA', 'caldera-forms' ),
			"description" => __( 'reCAPTCHA anti-spam field', 'caldera-forms' ),
			"file"        => CFCORE_PATH . "fields/recaptcha/field.php",
			"category"    => __( 'Discontinued', 'caldera-forms' ),
			"handler"     => array( Caldera_Forms::get_instance(), 'captcha_check' ),
			"capture"     => false,
			"setup"       => array(
				"template"      => CFCORE_PATH . "fields/recaptcha/config.php",
				"preview"       => CFCORE_PATH . "fields/recaptcha/preview.php",
				"not_supported" => array(
					'caption',
					'required'
				),
			)
		);
	}
}