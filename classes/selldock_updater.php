<?php
/*
 *  SellDock autoupdater class
 */

if( !class_exists('SellDock_Updater_v2') ) {
    class SellDock_Updater_v2 {
    
        var $api_url = 'https://selldock.com/products/';
        var $plugin_id = 28;
        var $plugin_path;
        var $plugin_slug;
        var $license_key;
    
        function __construct( $plugin_slug, $plugin_path, $license_key = null ) {
            $this->api_url .= $plugin_slug.'/';
            $this->plugin_slug = $plugin_slug;
            $this->plugin_path = basename( plugin_dir_path( $plugin_path ) ) . '/' . basename( $plugin_path );
            $this->license_key = $license_key;
    	

            add_filter( 'pre_set_site_transient_update_plugins', array(&$this, 'check_for_update') );
            add_filter( 'plugins_api', array(&$this, 'plugin_api_call'), 10, 3 );
            
            add_action('in_plugin_update_message-'.$plugin_path, array($this, 'modify_message'), 10, 2);

            add_action('wp_ajax_selldock_activate_' . $this->plugin_slug, array($this, 'selldock_activate') );
            // This is for testing only!
            //set_site_transient( 'update_plugins', null );
    
            // Show which variables are being requested when query plugin API
            //add_filter( 'plugins_api_result', array(&$this, 'debug_result'), 10, 3 );
        }
    

        function modify_message($a,$b){
            //echo '<div class="error"><p>Please register your copy of Caldera Engine to get automatic updates. by entering your license key or purchase to get a license. <a href="https://gum.co/calderaengine">Buy Now</a><script type="text/javascript" src="https://gumroad.com/js/gumroad.js"></script>.</p></div>';
        }

        function check_for_update( $transient ) {
            if(empty($transient->checked)) return $transient;

            $request_args = array(
                'slug'          => $this->plugin_slug,
                'version'       => $transient->checked[$this->plugin_path]
            );
            if ($this->license_key) $request_args['license'] = $this->license_key;

            
            $request_string = $this->prepare_request( 'plugin_update_check', $request_args );

            $raw_response = wp_remote_post( $this->api_url, $request_string );
            $response = null;
            if( !is_wp_error($raw_response) && ($raw_response['response']['code'] == 200) )
                $response = unserialize($raw_response['body']);
            
            if( is_object($response) && !empty($response) ) {
                // Feed the update data into WP updater
                $transient->response[$this->plugin_path] = $response;
                return $transient;
            }

            // Check to make sure there is not a similarly named plugin in the wordpress.org repository
            if ( isset( $transient->response[$this->plugin_path] ) ) {
                if ( strpos( $transient->response[$this->plugin_path]->package, 'wordpress.org' ) !== false  ) {
                    unset($transient->response[$this->plugin_path]);
                }
            }
            //dump($transient,0);
            return $transient;
        }
    
        function plugin_api_call( $def, $action, $args ) {
            if( !isset($args->slug) || $args->slug != $this->plugin_slug ) return $def;
            
            $plugin_info = get_site_transient('update_plugins');
            $request_args = array(
                'id' => $this->plugin_id,
                'slug' => $this->plugin_slug,
                'version' => (isset($plugin_info->checked)) ? $plugin_info->checked[$this->plugin_path] : 0 // Current version
            );
            
            $request_string = $this->prepare_request( $action, $request_args );
            $raw_response = wp_remote_post( $this->api_url, $request_string );

            if( is_wp_error($raw_response) ){
                $res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $raw_response->get_error_message());
            } else {
                $res = unserialize($raw_response['body']);
                if ($res === false)
                    $res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $raw_response['body']);
            }
            
            return $res;
        }
    
        function prepare_request( $action, $args ) {
            global $wp_version;
            $args = array_merge( array('action' => $action, 'license_key' => get_option('_caldera_engine_license_key'), 'url' => home_url() ), $args);
            return array(
                'body' => $args,
                'user-agent' => 'WordPress/'. $wp_version .'; '. home_url()
            );  
        }
        
        function debug_result( $res, $action, $args ) {
            echo '<pre>'.print_r($res,true).'</pre>';
            die;
            return $res;
        }
		
		function selldock_activate(){
			if(empty($_POST['license'])){
				echo '<div id="key-notice" class="error" style="display:inline-block !important; "><p>'.__('Please enter a license key', 'caldera-forms').'</p></div>';
				die;
			}
			global $wp_version;
			$request_string = array(
				'body' => array(
					'action'  => 'product_activation', 
					'license_key' => $_POST['license'],
					'version'	=> $_POST['version'],
					'url'		=>  home_url()
					),
				'user-agent' => 'WordPress/'. $wp_version .'; '. home_url()
				);

			$raw_response = wp_remote_post($this->api_url, $request_string );

			if( !is_wp_error($raw_response) && ($raw_response['response']['code'] == 200) )
				$response = unserialize($raw_response['body']);
			
			$out = array(
				'result' => $response->result
				);
			if( !empty( $response->activation_key ) ){
				update_option('_' . $this->plugin_slug . '_license_key', $response->activation_key);
				set_site_transient( 'update_plugins', null );
				echo '<div id="key-notice" class="updated" style="display:inline-block !important; "><p>'.$response->message.'</p></div>';
			}else{
				echo '<div id="key-notice" class="error" style="display:inline-block !important; "><p>'.$response->message.'</p></div>';
			}
			die;
		}
    
    }
}
