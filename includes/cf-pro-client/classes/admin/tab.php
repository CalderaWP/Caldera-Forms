<?php


namespace calderawp\calderaforms\pro\admin;


/**
 * Class tab
 * @package calderawp\calderaforms\pro\admin
 */
class tab {
	/**
	 * Path to index.html
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $template_path;

	/**
	 * tab constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param $template_path
	 */
	public function __construct( $template_path ){
		$this->template_path = $template_path;
	}

	/**
	 * Add pro tab to admin
	 *
	 * @since 1.0.0
	 *
	 * @uses "caldera_forms_get_panel_extensions" filter
	 *
	 * @param array $panels
	 *
	 * @return array
	 */
	public function add_tab( array  $panels  = array() ){
		$panels['form_layout']['tabs'][ 'cf-pro' ] = array(
			'name' => __( 'Pro', 'caldera-forms' ),
			'label' => __( 'Caldera Forms Pro Settings For This Form', 'caldera-forms' ),
			'location' => 'lower',
			'actions' => array(),
			'side_panel' => null,
			'canvas' => $this->template_path
		);

		return $panels;
	}


}