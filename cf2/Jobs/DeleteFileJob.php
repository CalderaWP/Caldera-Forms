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
	 * @param string$path Path file is stored in
	 */
	public function __construct($path)
	{
		$this->path = $path;
	}

	/** @inheritdoc */
	public function handle()
	{
		unlink($this->path);
	}
}