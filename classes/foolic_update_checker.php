<?php
/**
 * FooLicensing Update Checker
 *
 * @author    Brad Vincent
 * @version   1.5
 */

if (!class_exists('foolic_update_checker_v1_5')) {

	class foolic_update_checker_v1_5 {

		protected $plugin_file;
		protected $plugin_update_url;
		protected $plugin_version;
		protected $plugin_slug;
		protected $update_key;
		protected $license_key;

		function foolic_update_checker_v1_5($plugin_file, $plugin_update_url, $plugin_slug, $license_key) {
			$this->plugin_file       = $plugin_file;
			$this->plugin_update_url = $plugin_update_url;
			$this->plugin_version    = false;
			$this->plugin_slug       = $plugin_slug;
			$this->update_key        = plugin_basename($plugin_file);
			$this->license_key       = $license_key;

			add_filter('pre_set_site_transient_update_plugins', array(&$this, 'do_check'));

			add_filter('plugins_api', array(&$this, 'update_plugin_info'), 10, 3);

			add_action('install_plugins_pre_plugin-information', array($this, 'upgrade_popup'));
		}

		function get_plugin_version() {
			if ($this->plugin_version === false) {
				$data                 = get_plugin_data($this->plugin_file, false, false);
				$this->plugin_version = $data['Version'];
			}

			return $this->plugin_version;
		}

		function do_check($checked_data) {
			if (empty($checked_data) || empty($checked_data->checked)) {
				return $checked_data;
			}

			if (!empty($checked_data->response[$this->update_key])) {
				//already done a check - no need to do another one
				return $checked_data;
			}

			$version = $checked_data->checked[$this->update_key];

			$update_response_raw = $this->send_update_check_request($version);

			if (!is_wp_error($update_response_raw) && wp_remote_retrieve_response_code($update_response_raw) == 200) {
				$update_response = @unserialize(stripslashes($update_response_raw['body']));

				//merge response back into checked_data
				if ($update_response !== false && isset($update_response->new_version)) {
					$checked_data->response[$this->update_key] = $update_response;
				}
			}

			return $checked_data;
		}

		function update_plugin_info($def, $action, $args) {
			if (isset($args->slug) && ($args->slug === $this->plugin_slug)) {
				return $this->get_plugin_info();
			}

			return false;
		}

		function get_plugin_info() {
			//check for the update
			$update_response_raw = $this->send_update_check_request($this->get_plugin_version(), 'check-with-info');

			if (!is_wp_error($update_response_raw) && wp_remote_retrieve_response_code($update_response_raw) == 200) {
				//got a good response back
				$response = @unserialize($update_response_raw['body']);

				if ($response === false) {
					return new WP_Error(
						'plugins_api_failed',
						sprintf(__('An unknown error occurred while getting info for the plugin %s. Response: %s', $this->plugin_slug), $this->plugin_slug, $update_response_raw['body']),
						$update_response_raw['body']);
				} else {
					return $response;
				}
			} else {
				return new WP_Error(
					'plugins_api_failed',
					sprintf(__('An unknown error occurred while getting info for the plugin %', $this->plugin_slug), $this->plugin_slug),
					$update_response_raw);
			}
		}

		function send_update_check_request($version, $action = 'check') {
			$request_args = array(
				'slug'    => $this->plugin_slug,
				'version' => $version,
				'license' => $this->license_key,
				'site'    => home_url(),
				'ip'      => $_SERVER['REMOTE_ADDR']
			);

			//build up the string used to check for the update
			$request_string = $this->prepare_update_request($request_args, $action);

			//check for the update
			return wp_remote_post($this->plugin_update_url, $request_string);
		}

		function prepare_update_request($args, $action = 'check') {
			global $wp_version;

			return array(
				'body'       => array(
					'action'  => $action,
					'request' => serialize($args)
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url()
			);
		}

		function prepare_validate_request($license, $action = 'validate') {
			global $wp_version;

			return array(
				'body'       => array(
					'action'  => $action,
					'license' => $license,
					'site'    => home_url()
				),
				'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url()
			);
		}

		function upgrade_popup() {
			if ($_GET['plugin'] != $this->plugin_slug) return;

			$info = $this->get_plugin_info();

			if (is_wp_error($info)) {
				echo $info->get_error_message();
			} else {
				echo $info->body;
			}

			exit;
		}
	}

//For testing purposes only!  
//add_filter ('pre_set_site_transient_update_plugins', 'display_transient_update_plugins');  
//function display_transient_update_plugins ($transient)  
//{  
//    var_dump($transient);  
//}    
}
?>