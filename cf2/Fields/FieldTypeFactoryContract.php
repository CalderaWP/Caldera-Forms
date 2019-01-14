<?php


namespace calderawp\calderaforms\cf2\Fields;


use Psr\Container\ContainerInterface;

interface FieldTypeFactoryContract extends ContainerInterface
{
	/**
	 * Add a a field type
	 *
	 * @since 1.8.0
	 *
	 * @param FieldTypeContract $fieldType
	 *
	 * @return $this
	 */
	public function add(FieldTypeContract $fieldType);

	/**
	 * Get all fields
	 *
	 * @since 1.8.0
	 *
	 * @return array
	 */
	public function getAll();
}
