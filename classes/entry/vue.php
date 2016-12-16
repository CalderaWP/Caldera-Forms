<?php

/**
 * Create entry viewer v2
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Entry_Vue {

	/**
	 * Viewer config
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected  $config;

	/**
	 * Form config
	 *
	 * @since 1.5.0
	 *
	 * @var array
	 */
	protected $form;

	/**
	 * Caldera_Forms_Entry_Vue constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param array $form Form config
	 * @param array $config Optional. Viewer confing default overides
	 */
	public function __construct( array  $form, array $config = array()) {
		$this->form = $form;
		$this->set_config( $config );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Display the entry viewer
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function display(){
		$this->enqueue();
		ob_start();
		include  CFCORE_PATH .'ui/viewer-two/viewer.php';
		return ob_start();
	}
	/**
	 * Setup scripts/styles for entry viewer
	 *
	 * @since 1.5.0
	 */
	public function enqueue(){
		Caldera_Forms_Render_Assets::enqueue_style( 'table' );
		Caldera_Forms_Render_Assets::enqueue_style( 'modals' );
		Caldera_Forms_Render_Assets::enqueue_script( 'modals' );
		Caldera_Forms_Render_Assets::enqueue_script( 'entry-viewer-2' );
		wp_localize_script( Caldera_Forms_Render_Assets::make_slug( 'entry-viewer-2' ), 'CF_ENTRY_VIEWER_2_CONFIG', $this->config );
	}


	/**
	 * Set config property with defaults and ovverides passed ot constructor
	 *
	 * @since 1.5.0
	 *
	 * @param array $config
	 */
	protected function set_config($config ){
		$this->config = wp_parse_args( $config, $this->config_defualts() );
	}

	/**
	 * The defaults for config
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	protected function config_defualts(){
		return array(
			'formId' => $this->form[ 'ID' ],
			'dateFormat' => Caldera_Forms::time_format(),
			'api' => array(
				'root'    => esc_url( trailingslashit( Caldera_Forms_API_Util::url() ) ),
				'form'    => esc_url( trailingslashit( Caldera_Forms_API_Util::url( 'forms' ) ) ),
				'entries' => esc_url( trailingslashit( Caldera_Forms_API_Util::url( 'entries' ) ) ),
				'nonce'   => wp_create_nonce( 'wp_rest' ),
			),

			'templates' => array(
				'entries' => 'caldera-forms-entries-tmpl',
                'entry' =>  'caldera-forms-entry-tmpl'
			),
			'targets' => array(
				'entries' => 'caldera-forms-entries',
                'entry' => 'caldera-forms-entry'
			),
			'perPage' => absint( Caldera_Forms_Entry_Viewer::entries_per_page() )
		);
	}
}