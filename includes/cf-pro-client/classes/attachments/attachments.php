<?php


namespace calderawp\calderaforms\pro\attachments;


/**
 * Class attachments
 * @package calderawp\calderaforms\pro\attachments
 */
class attachments {
	/**
	 * @var phpmailer
	 */
	protected $phpMailer;
	public function __construct()
	{
		$this->phpMailer = new phpmailer;
	}

	/**
	 * Add an attachment from a path on the filesystem.
	 * Never use a user-supplied path to a file!
	 * Returns false if the file could not be found or read.
	 * @param string $path Path to the attachment.
	 * @param string $name Overrides the attachment name.
	 * @param string $encoding File encoding (see $Encoding).
	 * @param string $type File extension (MIME) type.
	 * @param string $disposition Disposition to use
	 * @throws Exception
	 * @return boolean
	 */
	public function addAttachment($path) {

		try{
			$attached = $this->phpMailer->addAttachment( $path );
		} catch ( \phpmailerException $e ){
			throw new Exception( $e->getMessage(), $e->getCode() );
		}

		return $attached;
	}

	/**
	 * Return the array of attachments.
	 * @return array
	 */
	public function getAttachments()
	{
		return $this->phpMailer->getAttachments();
	}

	public function getEncoded(){
		$this->phpMailer->attachAll( 'attachment' );
	}

}