<?php

/**
 * Attachment object
 *
 * @package Caldera_Forms
 * @author    Josh Pollock <Josh@CalderaWP.com>
 * @license   GPL-2.0+
 * @link
 * @copyright 2016 CalderaWP LLC
 */
class Caldera_Forms_Email_Attachment extends Caldera_Forms_Object {

	/**
	 * Content type
	 * 
	 * @since 1.4.0
	 * 
	 * @var string
	 */
	protected $type;

	/**
	 * Actual file contents
	 *
	 * @since 1.4.0
	 *
	 * @var string
	 */
	protected $content;

	/**
	 * The filename
	 *
	 * @since 1.4.0
	 * 
	 * @var string
	 */
	protected $filename;

	/**
	 * @inheritdoc
	 */
	protected function get_prefix(){
		return 'attachment';
	}

	/**
	 *  Get file contents as a base64 encoded string
	 * 
	 * @since 1.4.0
	 * 
	 * @return string
	 */
	public function get_encoded(){
		if( false != ( $file = file_get_contents( $this->filename ) ) ){

			return base64_encode( $file );
		}
		
	}

	/**
	 * Make calls to $this->filename only return basename
	 * 
	 * @since 1.4.0
	 * 
	 * @return string
	 */
	protected function filename_get(){
		return basename( $this->filename );
	}

	/**
	 * Called when setting content property, and sets everything if value is a filepath
	 * 
	 * @since 1.4.0
	 * 
	 * @param $content
	 */
	protected function content_set( $content ){
		if( is_file( $content ) ){
			$this->content = file_get_contents( $content );
			$this->filename = $content;
			$this->type = mime_content_type( $content );
			$info = pathinfo( $this->filename );
			if( 'csv' == $info[ 'extension' ] ){
				$this->type = 'text/csv';
			}
			
		}else{
			$this->content = $content;
		}

	}


}