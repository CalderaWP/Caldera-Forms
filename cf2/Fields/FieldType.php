<?php


namespace calderawp\calderaforms\cf2\Fields;


abstract class FieldType implements FieldTypeContract
{

	/** @inheritdoc */
	public static function toArray ()
	{
		return [
			"cf2" => TRUE,
			"field" => static::getName(),
			"description" => static::getDescription(),
			"category" => static::getCategory(),
			"setup" => static::getSetup(),
		];
	}

	/** @inheritdoc */
	public static function getSetup ()
	{
		return [
			'template' => 'fields/' . static::getCf1Identifier() . '/config.php',
			'preview' => 'fields/' . static::getCf1Identifier() . '/preview.php',
		];
	}
}
