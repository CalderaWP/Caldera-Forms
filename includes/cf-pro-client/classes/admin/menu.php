<?php


namespace calderawp\calderaforms\pro\admin;


/**
 * Class menu
 * @package calderawp\calderaforms\pro\admin
 */
class menu {

	protected $view_dir;

	/**
	 * @var scripts
	 */
	protected $scripts;

	protected  $slug;
	public function __construct( $view_dir, $slug, scripts $scripts ){
		$this->view_dir = $view_dir;
		$this->slug = $slug;
		$this->scripts = $scripts;
	}

	/**
	 * Create admin page view
	 *
	 * @since 0.1.0
	 */
	public function display() {
		add_submenu_page(
			\Caldera_Forms::PLUGIN_SLUG,
			__( 'Caldera Forms Pro', 'caldera-forms'),
			'<span class="caldera-forms-menu-dashicon"><span class="dashicons dashicons-star-filled"></span>' .__( 'Caldera Forms Pro', 'caldera-forms') . '</span>',
			'manage_options',
			$this->slug,
			[ $this, 'render' ]
		);
	}

	/**
	 * Redner admin page view
	 *
	 * @since 0.1.0
	 */
	public function render() {
		echo $this->scripts->webpack( $this->view_dir );

	}

}