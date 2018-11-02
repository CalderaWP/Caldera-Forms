<?php


namespace calderawp\calderaforms\cf2\Fields;


use calderawp\calderaforms\cf2\Exceptions\NotFoundInContainerException;
use calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType;
use Psr\Container\ContainerInterface;

class FieldTypeFactory implements FieldTypeFactoryContract
{
	/**
	 * The fields
	 *
	 * @since 1.8.0
	 *
	 * @var array
	 */
	protected $fields;

	/** @inheritdoc */
	public function add(FieldTypeContract $fieldType)
	{
		if (! $this->has(FileFieldType::getCf1Identifier())) {
			$this->fields[ $fieldType::getCf1Identifier() ] = $fieldType;
		}
		return $this;

	}

	/** @inheritdoc */
	public function getAll(){
		if( is_array( $this->fields ) ){
			return $this->fields;
		}

		return [];
	}


	/** @inheritdoc */
	public function get($id){
		if( ! $this->has( $id ) ){
			throw new NotFoundInContainerException(__( 'Field type not found', 'caldera-forms'),'404');
		}
		return $this->fields[$id];
	}

	/** @inheritdoc */
	public function has($id){
		return array_key_exists( $id,$this->getAll());
	}



}
