<?php


namespace calderawp\calderaforms\cf2\Fields\FieldTypes;


use calderawp\calderaforms\cf2\Fields\FieldType;

class FileFieldType extends FieldType
{

    /** @inheritdoc */
    public static function getType()
    {
        return 'file';
    }
    /** @inheritdoc */
    public static function getCf1Identifier()
    {
        return 'cf2_file';
    }
	/** @inheritdoc */
	public static function getCategory ()
	{
		return __( 'File', 'caldera-forms' );
	}

	/** @inheritdoc */
	public static function getDescription ()
	{
		return __( 'File Field With Super Powers', 'caldera-forms' );
	}

	/** @inheritdoc */
	public static function getName ()
	{
		return __( 'File Field (CF2)', 'caldera-forms' );
	}
}
