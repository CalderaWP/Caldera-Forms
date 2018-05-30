<?php


namespace calderawp\calderaforms\pro\admin;
use calderawp\calderaforms\pro\container;


/**
 * Class scripts
 * @package calderawp\calderaforms\pro\admin
 */
class scripts {

	/** @var string  */
	protected $assets_url;

	/** @var string  */
	protected $slug;

	/** @var string  */
	protected  $version;

	/**
	 * scripts constructor.
	 *
	 * @param string $assets_url Url for  assets dir
	 * @param string $slug Slug for script/css
	 * @param string $version Current version
	 */
	public function __construct( $assets_url, $slug, $version ){
		$this->assets_url = $assets_url;
		$this->slug = $slug;
		$this->version = $version;

	}

	public function get_assets_url(){
		return $this->assets_url;
	}

	/**
	 * @param string $view_dir @deprecated
	 * @param null $context Default is for admin page. Pass "tab" for use in CF admin.
	 * @param bool $enqueue_admin
	 * @return string
	 */
	public function webpack( $view_dir, $context = null, $enqueue_admin = true ){
		\Caldera_Forms_Render_Assets::maybe_register();
		if ( $enqueue_admin ) {
			wp_enqueue_style( \Caldera_Forms_Admin_Assets::slug( 'admin', false ), \Caldera_Forms_Render_Assets::make_url( 'admin', false ) );
			\Caldera_Forms_Admin_Assets::set_cf_admin( \Caldera_Forms_Render_Assets::make_slug( 'pro' ) );
		}
		\Caldera_Forms_Render_Assets::enqueue_style( 'pro' );
		\Caldera_Forms_Render_Assets::enqueue_script( 'pro' );
		wp_localize_script( \Caldera_Forms_Render_Assets::make_slug( 'pro' ), 'CF_PRO_ADMIN', $this->data() );
		if( 'tab' === $context ){
			$id = 'cf-pro-app-tab';
		}else{
			$id = 'cf-pro-app';
		}
		return  sprintf('<div id="%s"></div>', $id );
	}

	/**
	 * Data to localize
	 *
	 * @return array
	 */
	public function data(){
		$pro_url = admin_url('admin.php?page=cf-pro');

		$data = array(
			'strings' =>  [
				'saved' => esc_html__( 'Settings Saved', 'caldera-forms' ),
				'notSaved' => esc_html__( 'Settings could not be saved', 'caldera-forms' ),
				'apiKeysViewText' => esc_html__( 'You must add your API keys to use Caldera Forms Pro', 'caldera-forms' ),
				'apiKeysViewLink' => esc_url( $pro_url ),
				'minLogLevelTitle' => esc_html__( 'Minimum Log Level', 'caldera-forms' ),
				'minLogLevelInfo' => esc_html__( 'Setting a higher level than notice may affect performance, and should only be used when instructed by support.', 'caldera-forms' ),
                'whatIsCFPro' => [
                    'firstParagraph'    => esc_html__("Caldera Forms Pro is an app + plugin that makes forms easy.", 'caldera-forms' ),
                    'hTitle'    => esc_html__('Benefits', 'caldera-forms'),
                    'firstLi'   => esc_html__('Enhanced Email Delivery', 'caldera-forms'),
                    'secondLi'   => esc_html__('Form To PDF', 'caldera-forms'),
                    'thirdLi'   => esc_html__('Priority Support.', 'caldera-forms'),
                    'fourthLi'   => esc_html__('Add-ons Included in Yearly Plans', 'caldera-forms'),
                ],
                'freeTrial' => [
                    'firstParagraph'    => esc_html__('Ready to try Caldera Forms Pro? Plans start at just 14.99/ month with a 7 day free trial.', 'caldera-forms' ),
                    'buttonLeft'    => esc_html__('View Documentation', 'caldera-forms'),
                    'buttonRight'   => esc_html__('Start Free Trial', 'caldera-forms'),
                ],
                'notConnected' => esc_html__( 'Not Connected', 'caldera-forms' ),
                'connected' => esc_html__( 'Connected', 'caldera-forms' ),
                'tabNames' => [
                    'account' => esc_html__( 'Account', 'caldera-forms' ),
                    'formSettings' => esc_html__( 'Form Settings', 'caldera-forms' ),
                    'settings' => esc_html__( 'Settings', 'caldera-forms' ),
                    'whatIsCFPro' => esc_html__( 'What is Caldera Forms Pro ?', 'caldera-forms' ),
                    'freeTrial' => esc_html__( 'Free Trial', 'caldera-forms' ),
                ]
			],
			'api' => array(
				'cf' => array(
					'url' => esc_url_raw( \Caldera_Forms_API_Util::url( 'settings/pro' ) ),
					'nonce'=> wp_create_nonce( 'wp_rest' )
				),
				'cfPro' => array(
					'url' => esc_url_raw( caldera_forms_pro_app_url() ),
					'auth' => array()
				)
			),
			'settings'  => container::get_instance()->get_settings()->toArray(),
			'logLevels' => container::get_instance()->get_settings()->log_levels()
		);

		$data[ 'formScreen' ] = \Caldera_Forms_Admin::is_edit() ? esc_attr( $_GET[ \Caldera_Forms_Admin::EDIT_KEY ] ) : false;

		return $data;
	}
}