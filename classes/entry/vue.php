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
	 * @param array $config Optional. Viewer config default overrides
	 */
	public function __construct( array  $form, array $config = array()) {
		$this->form = $form;
		$this->set_config( $config );
		$this->enqueue();
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
		return ob_get_clean();
	}
	/**
	 * Setup scripts/styles for entry viewer
	 *
	 * @since 1.5.0
	 */
	public function enqueue(){
		Caldera_Forms_Render_Assets::maybe_register();
		Caldera_Forms_Render_Assets::enqueue_style( 'table' );
		Caldera_Forms_Render_Assets::enqueue_style( 'modals' );
		Caldera_Forms_Render_Assets::enqueue_style( 'entry-viewer-2' );
		Caldera_Forms_Render_Assets::enqueue_style( 'modals-theme' );
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
		$this->config = wp_parse_args( $config, $this->config_defaults() );
		if( isset( $config[ 'token' ] ) ){
			$this->config[ 'api' ][ 'token' ] = $config[ 'token' ];
		}

		/**
		 * Filter configuration (passed to JavaScript) for Entry Viewer (v2)
		 *
		 * @since 1.5.0
		 *
		 * @param array $config Configuration to filter
		 * @param array $form Form config
		 *
		 */
		$this->config = apply_filters( 'caldera_forms_entry_viewer_2_config', $this->config, $this->form );

	}

	/**
	 * The defaults for config
	 *
	 * @since 1.5.0
	 *
	 * @return array
	 */
	protected function config_defaults(){
		$config = new Caldera_Forms_API_JsConfig( $this->form );
		$default = array_merge( $config->toArray(), array(
				'templates' => array(
					'entries' => 'caldera-forms-entries-tmpl',
	                'entry' =>  'caldera-forms-entry-tmpl'
				),
				'targets' => array(
					'entries' => 'caldera-forms-entries',
	                'entry' => 'caldera-forms-entry'
				),
				'perPage' => absint( Caldera_Forms_Entry_Viewer::entries_per_page() ),
				'strings' => array(
					'no_entries' => esc_html__( 'No Entries To Display', 'caldera-forms' ),
					'not_allowed' => esc_html__( 'You are not allowed to view this.', 'caldera-forms' )
				)
			)
		);
		return $default;
	}
}