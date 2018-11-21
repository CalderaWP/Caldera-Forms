<?php


namespace calderawp\calderaforms\cf2\Jobs;


/**
 * Class DeleteFileJob
 *
 * Delete a file at a later time
 *
 * @since 1.8.0
 */
class DeleteFileJob extends Job
{

	/**
	 * Path file is stored in
	 *
	 * @since 1.8.0
	 *
	 * @var string
	 */
	protected $path;

	/**
	 * DeleteFileJob constructor.
	 *
	 * @since 1.8.0
	 *
	 * @param string $path Path file is stored in
	 */
	public function __construct($path)
	{
		$this->path = $path;
	}

	/** @inheritdoc */
	public function handle()
	{
		if ( file_exists($this->path) ) {
			unlink($this->path);
		}

		if( file_exists( $this->dirName() ) && $this->isEmptyDir() ){
			rmdir(dirname($this->path));
		}

	}

	/**
	 * Check if is empty directory
	 *
	 * @since 1.8.0
	 *
	 * @return bool
	 */
	protected function isEmptyDir()
	{
		foreach ( new \DirectoryIterator($this->dirName()) as $fileInfo ) {
			if ( $fileInfo->isDot() ) {
				continue;
			};
			return false;
		}
		return true;
	}

	/**
	 * Get name of directory file is in
	 *
	 * @since 1.8.0
	 *
	 * @return string
	 */
	protected function dirName()
	{
		return dirname($this->path);
	}
}