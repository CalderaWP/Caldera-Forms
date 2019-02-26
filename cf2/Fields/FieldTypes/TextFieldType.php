<?php


namespace calderawp\calderaforms\cf2\Fields\FieldTypes;


use calderawp\calderaforms\cf2\Fields\FieldType;

class TextFieldType extends FieldType
{

    /** @inheritdoc */
    public static function getType()
    {
        return 'text';
    }
    /** @inheritdoc */
    public static function getCf1Identifier()
    {
        return 'cf2_text';
    }

	/** @inheritdoc */
    public static function getCategory ()
	{
		return __( 'Basic', 'caldera-forms' );
	}

	/** @inheritdoc */
	public static function getDescription ()
	{
		return __( 'Text Field With Super Powers', 'caldera-forms' );
	}

	/** @inheritdoc */
	public static function getName ()
	{
		__( 'Text Field (CF2)', 'caldera-forms' );
	}
}
