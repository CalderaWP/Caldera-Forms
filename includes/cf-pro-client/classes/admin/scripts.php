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

	public function webpack( $view_dir, $context = null, $enqueue_admin = true ){
		$inline = \Caldera_Forms_Render_Util::create_cdata('var CF_PRO_ADMIN= ' . wp_json_encode( $this->data() ) . ';' );
		if ( $enqueue_admin ) {
			wp_enqueue_style( \Caldera_Forms_Admin_Assets::slug( 'admin', false ), \Caldera_Forms_Render_Assets::make_url( 'admin', false ) );
		}
		ob_start();
		include $view_dir . '/index.php';
		$str = ob_get_clean();
		foreach ( [
			'styles',
			'manifest',
			'vendor',
			'client'
		] as $thing ){
			$str = str_replace( '/' . $thing, $this->get_assets_url() . $thing, $str );
		}

		if ( $context ) {
			$str = str_replace( 'cf-pro-app', 'cf-pro-app-' . $context, $str );
		}

		return $inline .str_replace([
				'<head>',
				'</head>'
			], '', $str );
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