<?php
/*
  Plugin Name: Caldera Forms
  Plugin URI: https://CalderaForms.com
  Description: Easy to use, grid based responsive form builder for creating simple to complex forms.
  Author: Caldera Forms
  Version: 1.9.6
  Author URI: https://CalderaForms.com
  Text Domain: caldera-forms
  GitHub Plugin URI: https://github.com/CalderaWP/caldera-forms
  Release Asset: true
*/

// If this file is called directly, abort.
if ( !defined('WPINC') ) {
	die;
}

//Check version minimums first
global $wp_version;
if ( !version_compare(PHP_VERSION, '5.6.0', '>=') ) {
	function caldera_forms_php_version_nag()
	{
		?>
        <div class="notice notice-error">
            <p>
				<?php esc_html_e('Your version of PHP is incompatible with Caldera Forms and can not be used.',
					'caldera-forms'); ?>
				<?php printf(' <a href="https://calderaforms.com/php?utm_source=wp-admin&utm_campaign=php_deprecated&utm_source=admin-nag" target="__blank">%s</a>',
					esc_html__('Learn More', 'caldera-forms')
				) ?></p>
        </div>
		<?php
	}

	add_shortcode('caldera_form', 'caldera_forms_fallback_shortcode');
	add_shortcode('caldera_form_modal', 'caldera_forms_fallback_shortcode');
	add_action('admin_notices', 'caldera_forms_php_version_nag');
} elseif ( !version_compare($wp_version, '5.1.0', '>=') ) {
	function caldera_forms_wp_version_nag(){
		?>
		<div class="notice notice-error">
		    <p>
			<?php esc_html_e('Your version of WordPress is incompatible with Caldera Forms and can not be used.', 'caldera-forms'); ?>
		    </p>
		</div>
		<?php
	}

	add_shortcode('caldera_form', 'caldera_forms_fallback_shortcode');
	add_shortcode('caldera_form_modal', 'caldera_forms_fallback_shortcode');
	add_action('admin_notices', 'caldera_forms_wp_version_nag');
} else {
	define('CFCORE_PATH', plugin_dir_path(__FILE__));
	define('CFCORE_URL', plugin_dir_url(__FILE__));
	define( 'CFCORE_VER', '1.9.6' );
	define('CFCORE_EXTEND_URL', 'https://api.calderaforms.com/1.0/');
	define('CFCORE_BASENAME', plugin_basename(__FILE__));

	/**
	 * Caldera Forms DB version
	 *
	 * @since 1.3.4
	 *
	 * PLEASE keep this an integer
	 */
	define('CF_DB', 8);

	// init internals of CF
	include_once CFCORE_PATH . 'classes/core.php';

	add_action('init', [ 'Caldera_Forms', 'init_cf_internal' ]);
	// table builder
	register_activation_hook(__FILE__, [ 'Caldera_Forms', 'activate_caldera_forms' ]);


	// load system
	add_action('plugins_loaded', 'caldera_forms_load', 0);
	function caldera_forms_load()
	{
		include_once CFCORE_PATH . 'classes/autoloader.php';
		include_once CFCORE_PATH . 'classes/widget.php';
		Caldera_Forms_Autoloader::add_root('Caldera_Forms_DB', CFCORE_PATH . 'classes/db');
		Caldera_Forms_Autoloader::add_root('Caldera_Forms_Entry', CFCORE_PATH . 'classes/entry');
		Caldera_Forms_Autoloader::add_root('Caldera_Forms_Email', CFCORE_PATH . 'classes/email');
		Caldera_Forms_Autoloader::add_root('Caldera_Forms_Admin', CFCORE_PATH . 'classes/admin');
		Caldera_Forms_Autoloader::add_root('Caldera_Forms_Render', CFCORE_PATH . 'classes/render');
		Caldera_Forms_Autoloader::add_root('Caldera_Forms_Sync', CFCORE_PATH . 'classes/sync');
		Caldera_Forms_Autoloader::add_root('Caldera_Forms_CSV', CFCORE_PATH . 'classes/csv');
		Caldera_Forms_Autoloader::add_root('Caldera_Forms_Processor_Interface', CFCORE_PATH . 'processors/classes/interfaces');
		Caldera_Forms_Autoloader::add_root('Caldera_Forms_API', CFCORE_PATH . 'classes/api');
		Caldera_Forms_Autoloader::add_root('Caldera_Forms_Field', CFCORE_PATH . 'classes/field');
		Caldera_Forms_Autoloader::add_root('Caldera_Forms_Magic', CFCORE_PATH . 'classes/magic');
		Caldera_Forms_Autoloader::add_root('Caldera_Forms_Processor', CFCORE_PATH . 'processors/classes');
		Caldera_Forms_Autoloader::add_root('Caldera_Forms_Shortcode', CFCORE_PATH . 'classes/shortcode');
		Caldera_Forms_Autoloader::add_root('Caldera_Forms_CDN', CFCORE_PATH . 'classes/cdn');
		Caldera_Forms_Autoloader::add_root('Caldera_Forms_Settings', CFCORE_PATH . 'classes/settings');
		Caldera_Forms_Autoloader::add_root('Caldera_Forms_Import', CFCORE_PATH . 'classes/import');
		Caldera_Forms_Autoloader::add_root('Caldera_Forms_Query', CFCORE_PATH . 'classes/query');
		Caldera_Forms_Autoloader::add_root('Caldera_Forms', CFCORE_PATH . 'classes');
		Caldera_Forms_Autoloader::register();


		// includes
		include_once CFCORE_PATH . 'includes/ajax.php';
		include_once CFCORE_PATH . 'includes/field_processors.php';
		include_once CFCORE_PATH . 'includes/custom_field_class.php';
		include_once CFCORE_PATH . 'includes/filter_addon_plugins.php';
		include_once CFCORE_PATH . 'includes/compat.php';
		include_once CFCORE_PATH . 'processors/functions.php';
		include_once CFCORE_PATH . 'includes/functions.php';
		include_once CFCORE_PATH . 'ui/blocks/init.php';
		include_once CFCORE_PATH . 'vendor/autoload.php';
		include_once CFCORE_PATH . 'includes/cf-pro-client/cf-pro-init.php';
		include_once CFCORE_PATH . 'sendwp/init.php';

		/**
		 * Runs after all of the includes and autoload setup is done in Caldera Forms core
		 *
		 * @since 1.3.5.3
		 */
		do_action('caldera_forms_includes_complete');

		add_filter( 'script_loader_src',
		 [Caldera_Forms_Render_Assets::class,'maybe_remove_version_query_arg'],
		15, 2 );
		add_filter( 'style_loader_src',
		 [Caldera_Forms_Render_Assets::class,'maybe_remove_version_query_arg'],
		15, 2 );

		add_action( 'init', [Caldera_Forms_Render_Assets::class,'maybe_redirect_to_dist']);
		/**
		 * Start cf2 system
		 *
		 * @since 1.8.0
		 */
		add_action('caldera_forms_v2_init', 'caldera_forms_v2_container_setup' );
		caldera_forms_get_v2_container();
	}

	add_action('plugins_loaded', [ 'Caldera_Forms', 'get_instance' ]);


	// Admin & Admin Ajax stuff.
	if ( is_admin() || defined('DOING_AJAX') ) {
		add_action('plugins_loaded', [ 'Caldera_Forms_Admin', 'get_instance' ]);
		add_action('plugins_loaded', [ 'Caldera_Forms_Support', 'get_instance' ]);
		include_once CFCORE_PATH . 'includes/plugin-page-banner.php';
	}


	//@see https://github.com/CalderaWP/Caldera-Forms/issues/2855
	add_filter( 'caldera_forms_pro_log_mode', '__return_false' );
	add_filter( 'caldera_forms_pro_mail_debug', '__return_false' );
}

/**
 * Shortcode handler to be used when Caldera Forms can not be loaded
 *
 * @since 1.7.0
 *
 * @return string
 */
function caldera_forms_fallback_shortcode()
{
	if ( current_user_can('edit_posts') ) {
		return esc_html__('Your version of WordPress or PHP is incompatible with Caldera Forms.', 'caldera-forms');
	}

	return esc_html__('Form could not be loaded. Contact the site administrator.', 'caldera-forms');

}