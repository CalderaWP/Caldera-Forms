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
		return __( 'File upload field with more features than standard HTML5 input.', 'caldera-forms' );
	}

	/** @inheritdoc */
	public static function getName ()
	{
		return __( 'Advanced File Uploader (2.0)', 'caldera-forms' );
	}

	/** @inheritdoc */
	public static function getIcon()
	{
		return caldera_forms_get_v2_container()->getCoreUrl() . 'assets/images/cloud-upload.svg';
	}
}
