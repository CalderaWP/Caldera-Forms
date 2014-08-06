<?php
/**
 * FooLicensing License Key Validation
 *
 * @author    Brad Vincent
 * @version   1.4
 */

if (!class_exists('foolic_validation_v1_4')) {

	class foolic_validation_v1_4 {

		protected $plugin_validation_url;
		protected $plugin_slug;

		protected $license_key;
		protected $license_valid;
		protected $license_expires;

		function foolic_validation_v1_4($plugin_validation_url, $plugin_slug) {
			$this->plugin_validation_url = $plugin_validation_url;
			$this->plugin_slug = $plugin_slug;

			if (is_admin()) {
				//output the needed css and js
				add_action('admin_enqueue_scripts', array(&$this, 'include_css') );
				add_action('admin_footer', array(&$this, 'include_js') );

				//wire up the ajax callbacks
				add_action('wp_ajax_foolic_validate_license-'.$this->plugin_slug, array($this, 'ajax_validate_license'));
				add_action('wp_ajax_foolic_license_set_validity-'.$this->plugin_slug, array($this, 'ajax_license_set_validity'));
				add_action('wp_ajax_foolic_license_store_error-'.$this->plugin_slug, array($this, 'ajax_license_store_error'));

				//output the validation HTML
				add_filter('foolic_get_validation_data-'.$this->plugin_slug, array($this, 'get_validation_data'));
			}
		}

		/**
		 * Securely get the option, sanitize and return
		 *
		 * @param $key string The key of the option we want to get
		 *
		 * @return string The option value
		 */
		function get_option_secure($key) {
			$option = get_site_option($key);

			return  htmlspecialchars($option);
		}

		function validate($license = false) {
			if ($license === false) {
				$license = apply_filters( $this->plugin_slug . '_foolic_licensekey', $this->get_option_secure( $this->plugin_slug . '_licensekey' ) );
			}

			$valid = !empty($license) ? apply_filters( $this->plugin_slug . '_foolic_valid', $this->get_option_secure( $this->plugin_slug . '_valid') ) : false;

			$expires = apply_filters( $this->plugin_slug . '_foolic_expires', $this->get_option_secure( $this->plugin_slug . '_valid_expires') );

			if (!empty($expires) && $expires !== 'never') {
				if (strtotime($expires) < strtotime(date("Y-m-d"))) {
					$valid = 'expired'; //it has expired!
				}
			}

			$this->license_key = $license;
			$this->license_valid = $valid;
			$this->license_expires = $expires;

			return array(
				'slug' => $this->plugin_slug,
				'license' => $this->license_key,
				'valid' => $this->license_valid,
				'expires' => $this->license_expires
			);
		}

		function get_validation_data() {

			$this->validate();

			$input_id = $this->plugin_slug . '_licensekey';
			$input = '<input class="foolic-input foolic-input-' . $this->plugin_slug . '' . ($this->license_valid !== false ? ($this->license_valid=='valid' ? ' foolic-valid' : ' foolic-invalid') : '') . '" type="password" id="' . $input_id . '" name="' . $this->plugin_slug . '[license]" value="' . $this->license_key . '" />';
			$button = '&nbsp;<input class="foolic-check foolic-check-' . $this->plugin_slug . ' button button-small" type="button" name="foolic-check-' . $this->plugin_slug . '" value="' . __('Validate', $this->plugin_slug) . '" />';
			$nonce = '<span style="display:none" class="foolic-nonce-' . $this->plugin_slug . '">' . wp_create_nonce($this->plugin_slug . '_foolic-ajax-nonce') . '</span>';
			if ($this->license_valid == 'expired') {
				$message = '<div class="foolic-error foolic-message-' . $this->plugin_slug . '">' . __('The license key has expired!', $this->plugin_slug) . '</div>';
			} else {
				$message = '<div style="display:none" class="foolic-message foolic-message-' . $this->plugin_slug . '"></div>';
			}
			return array(
				'slug' => $this->plugin_slug,
				'license' => $this->license_key,
				'valid' => $this->license_valid,
				'expires' => $this->license_expires,
				'input' => $input,
				'button' => $button,
				'nonce' => $nonce,
				'message' => $message,
				'html' => '<div class="foolic-validation-' . $this->plugin_slug . '">' . $input . $button . $nonce . $message . '</div>'
			);
		}

		function include_css($hook_suffix) {
			$screen = get_current_screen();
			$include = apply_filters('foolic_validation_include_css-'.$this->plugin_slug, $screen);

			//if the filter was not overridden then add the css and js on the plugin settings page
			if ($include === $screen) $include = ($hook_suffix === $this->plugin_slug || $hook_suffix === 'settings_page_' . $this->plugin_slug);
			if (!$include) return;

?>
<style type="text/css">
	.foolic-check {
		cursor: pointer;
	}

	input.foolic-input.foolic-loading {
		background-image: url(data:image/gif;base64,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);
		background-repeat: no-repeat;
		background-position: right center;
	}

	input.foolic-input.foolic-valid {
		background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjJGRkUyM0M3MjQyQjExRTNCNkQ4REE5QUMzNzA0MTkwIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjJGRkUyM0M4MjQyQjExRTNCNkQ4REE5QUMzNzA0MTkwIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MkZGRTIzQzUyNDJCMTFFM0I2RDhEQTlBQzM3MDQxOTAiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MkZGRTIzQzYyNDJCMTFFM0I2RDhEQTlBQzM3MDQxOTAiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz56A8erAAAAjklEQVR42mL6//8/AyWYiYFCwKiygmQ986F0IohgIUNzAhI/kYUCzWA2E5maQWDB7fD/iUxoiuaToll1JSM8DDD8hk8zSB6kGRaIWP2GhQ3XjCzAgiSATyPDr9v8C9hUPyaiizMhOXkBnkDEqhk9FnAZguFsXAZgMwSvZlwpMREHG3teAOUoSgDFuREgwACvJFXnJjyTjwAAAABJRU5ErkJggg==);
		background-repeat: no-repeat;
		background-position: right center;
		padding-right: 3px;
	}

	input.foolic-input.foolic-invalid {
		background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyBpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEzNDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNSBXaW5kb3dzIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjFBNzcyOTY1MjQyQTExRTNBNjJBOEYwMDAwRUUyNTJCIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjFBNzcyOTY2MjQyQTExRTNBNjJBOEYwMDAwRUUyNTJCIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MUE3NzI5NjMyNDJBMTFFM0E2MkE4RjAwMDBFRTI1MkIiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MUE3NzI5NjQyNDJBMTFFM0E2MkE4RjAwMDBFRTI1MkIiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6SfY41AAAAu0lEQVR42mL8//8/AyWAZS4/Pz55VyAG2bAHpwF4NLMC8SQoWw+If2NTxITHgEwg1oDiDFyKcBkgAsQNSPxGIBYmxYBKIBYEYkYoFoSKEWWADhDnYRHPB2JtYgzowhG4ILFuQgZ4QjEugCHPhBZtfUSknT6oWgwDYNGGDP5DMTJAiVaYAQJAXIvFNlgsoINaqB64AU3QuCcWiELTBtgAHajzsQFsXoCBLFC0MuGJNoIZERStjJRmZ4AAAwB3nBw+mnbRwgAAAABJRU5ErkJggg==);
		background-repeat: no-repeat;
		background-position: right center;
		padding-right: 3px;
	}

	.foolic-message {
		display: table-cell;
		background-color: lightYellow;
		border: solid 1px #E6DB55;
		padding: 3px 10px;
	}

	.foolic-error {
		display: table-cell;
		color: #c00;
		font-weight:bold;
		padding: 3px 10px;
	}
</style>
<?php	}

		function include_js() {
			$screen = get_current_screen();
			$include = apply_filters('foolic_validation_include_js-'.$this->plugin_slug, $screen);

			//if the filter was not overridden then add the js on the plugin settings page
			if ($include === $screen) $include = (array_key_exists('page', $_GET) && $_GET['page'] == $this->plugin_slug);
			if (!$include) return;

			$namespace = 'foolic_' . str_replace('-', '_', $this->plugin_slug);
?>
<script type="text/javascript">

(function( <?php echo $namespace; ?>, $, undefined ) {
	<?php echo $namespace; ?>.init = function() {
		$('.foolic-validation-<?php echo $this->plugin_slug; ?> input.foolic-check').click(function (e) {
			e.preventDefault();
			var $this = $(this);
			var $input = $this.prev('.foolic-input-<?php echo $this->plugin_slug; ?>');
			if ($input.val().length == 0) {
				alert('<?php echo __('Please enter a license key', $this->plugin_slug); ?>');
			} else {
				<?php echo $namespace; ?>.send_request($input, 'foolic_validate_license');
			}
		});
	};

	<?php echo $namespace; ?>.send_request = function($input, action) {
		var $message = $input.siblings('.foolic-message-<?php echo $this->plugin_slug; ?>');
		var nonce = $input.siblings('.foolic-nonce-<?php echo $this->plugin_slug; ?>').text();

		$input.removeClass('foolic-valid foolic-invalid').addClass('foolic-loading');
		$message.hide().removeClass('foolic-message foolic-error');

		var data = { action: action + '-<?php echo $this->plugin_slug; ?>', license: $input.val(), nonce: nonce, input: $input.attr('name') };

		$.ajax({
			url: ajaxurl,
			cache: false,
			type: 'POST',
			data: data,
			dataType: "json",
			success: function (data) {
				$input.removeClass('foolic-loading');
				var message = '';
				if (data.license_message) {
					message = data.license_message;
				}
				message += '<strong style="color:' + data.response.color + '">' + data.response.message + '</strong>';
				if (data.validation_message)
					message += '<div>' + data.validation_message + '</div>';
				$message.html(message).show();
				$input.addClass(data.response.valid ? 'foolic-valid' : 'foolic-invalid');
				<?php echo $namespace; ?>.set_validity(data.response.valid, data.expires, nonce);
				if (data.response.valid) {
					$('.foolic-admin-notice-<?php echo $this->plugin_slug; ?>').remove();
				}
			},
			error: function (a, b, c) {
				$message.html('Something went wrong when trying to validate your license. The error was : ' + a.responseText).show();
				$input.removeClass('foolic-loading');
				<?php echo $namespace; ?>.store_validation_error(a.responseText, nonce);
			}
		});
	}

	<?php echo $namespace; ?>.store_validation_error = function(response, nonce) {
		if (response) {
			var data = { action: 'foolic_license_store_error-<?php echo $this->plugin_slug; ?>', response: response, nonce: nonce };

			$.ajax({
				url: ajaxurl,
				cache: false,
				type: 'POST',
				data: data
			});
		}
	}

	<?php echo $namespace; ?>.set_validity = function(valid, expires, nonce) {
		var data = { action: 'foolic_license_set_validity-<?php echo $this->plugin_slug; ?>', valid: valid ? 'valid' : 'invalid', expires : expires, nonce: nonce };

		$.ajax({
			url: ajaxurl,
			cache: false,
			type: 'POST',
			data: data
		});
	}
}( window.<?php echo $namespace; ?> = window.<?php echo $namespace; ?> || {}, jQuery ));

jQuery(function($) {
	<?php echo $namespace; ?>.init();
});
</script>
<?php	}

		function ajax_license_set_validity() {
			if (wp_verify_nonce($_REQUEST['nonce'], $this->plugin_slug . '_foolic-ajax-nonce')) {
				$valid   = htmlspecialchars($_REQUEST['valid']);
				$expires = htmlspecialchars($_REQUEST['expires']);
				update_site_option($this->plugin_slug . '_valid', $valid);
				if (!empty($expires)) {
					update_site_option($this->plugin_slug . '_valid_expires', $expires);
				}
			}
		}

		function ajax_license_store_error() {
			if (wp_verify_nonce($_REQUEST['nonce'], $this->plugin_slug . '_foolic-ajax-nonce')) {
				$response = $_REQUEST['response'];
				update_site_option($this->plugin_slug . '_lasterror', $response);
			}
		}

		function ajax_validate_license() {
			try {

				if (wp_verify_nonce($_REQUEST['nonce'], $this->plugin_slug . '_foolic-ajax-nonce')) {

					$license = htmlspecialchars( $_REQUEST['license'] );
					
					$response_raw = wp_remote_post($this->plugin_validation_url, $this->prepare_validate_request($license));

					if (is_wp_error($response_raw)) {
						$error = $response_raw->get_error_message();
						$this->output_json_error(__('An error occurred while trying to validate your license key', $this->plugin_slug),
							$error);
						die;
					} else if (wp_remote_retrieve_response_code($response_raw) != 200) {
						$this->output_json_error(__('An error occurred while trying to validate your license key', $this->plugin_slug),
							sprintf(__('The response code of [%s] was not expected', $this->plugin_slug), wp_remote_retrieve_response_code($response_raw)));
					} else {

						$response = $response_raw['body'];

						$response_object = @json_decode( $response );

						if ( !empty($response_object->response) ) {

							header('Content-type: application/json');

							//only save the option if return good response from server
							update_site_option($this->plugin_slug . '_licensekey', $license);

							//try to save the setting
							if (array_key_exists('input', $_REQUEST)) {
								$setting_name = htmlspecialchars( $_REQUEST['input'] );

								if (preg_match('/([^\]]*)\[([^\]]*)\]/', $setting_name, $match)) {
									$option_name = $match[1];
									$option_key  = $match[2];

									$option = get_site_option($option_name);
									if (is_array($option)) {
										$option[$option_key] = $license;
										update_site_option($option_name, $option);
									} else {
										delete_site_option($option_name);
										add_site_option($option_name, array($option_key => $license));
									}
								}
							}

							echo $response;

						}

						die;
					}

				} else {
					$this->output_json_error(__('The validation request was invalid', $this->plugin_slug),
						__('The validation NONCE could not be validated!', $this->plugin_slug));
				}
			}
			catch (Exception $e) {
				$this->output_json_error(__('An unexpected error occurred', $this->plugin_slug),
					$e->getMessage());
			}
		}

		function output_json_error($error, $message) {
			$details = array(
				'response'           => array(
					'valid'   => false,
					'message' => $error,
					'color'   => '#ff0000',
					'error'   => true
				),
				'validation_message' => $message
			);

			header('Content-type: application/json');
			echo json_encode($details);
			die;
		}

		function prepare_validate_request($license, $action = 'validate') {
			global $wp_version;

			return array(
				'body'       => array(
					'action'  => $action,
					'license' => $license,
					'site'    => home_url()
				),
				'timeout' => 45,
				'user-agent' => 'WordPress/' . $wp_version . '; FooLicensing'
			);
		}

	}
}