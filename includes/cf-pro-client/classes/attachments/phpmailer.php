<?php


namespace calderawp\calderaforms\pro\attachments;


/**
 * Class phpmailer
 * @package calderawp\calderaforms\pro\attachments
 */
class phpmailer extends \PHPMailer
{

	/** @inheritdoc */
	public function attachAll($disposition_type, $boundary)
	{
		return parent::attachAll($disposition_type, $boundary);
	}
}
