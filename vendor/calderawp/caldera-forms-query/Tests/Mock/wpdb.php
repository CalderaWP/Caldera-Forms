<?php
if( class_exists( 'wpdb')){
	return;
}
//phpcs:disable
class wpdb
{

	/**
	 * @var string
	 */
	public $prefix = 'wp_';

	/**
	 * @param null $query
	 * @param string $output
	 * @return array
	 */
	public function get_results( $query = null, $output = OBJECT )
	{
		return [

		];
	}

}