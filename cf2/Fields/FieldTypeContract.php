<?php


namespace calderawp\calderaforms\cf2\Fields;


interface FieldTypeContract
{
    /**
     * Get the field's type
     *
     * @since 1.8.0
     *
     * @return string
     */
    public static function getType();

    /**
     * Get the field's identifier for use in cf1
     *
     * @since 1.8.0
     *
     * @return string
     */
    public static function getCf1Identifier();

	/**
	 * Get the field's setup array
	 *
	 * @since 1.8.0
	 *
	 * @return array
	 */
    public static function getSetup();

	/**
	 * Get the field's category
	 *
	 * @since 1.8.0
	 *
	 * @return string
	 */
    public static function getCategory();

	/**
	 * Get the field's description
	 *
	 * @since 1.8.0
	 *
	 * @return string
	 */
    public static function getDescription();

	/**
	 * Get the field's name
	 *
	 * @since 1.8.0
	 *
	 * @return string
	 */
    public static function getName();

	/**
	 * Convert to an array
	 *
	 * @since 1.8.0
	 *
	 * @return array
	 */
    public static function toArray();


	/**
	 * Get field type icon
	 *
	 * @since 1.8.0
	 *
	 * @return string
	 */
    public static function getIcon();
}
