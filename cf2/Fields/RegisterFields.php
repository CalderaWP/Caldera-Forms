<?php


namespace calderawp\calderaforms\cf2\Fields;


class RegisterFields implements RegisterFieldsContract
{

	/**
	 * @var FieldTypeFactoryContract
	 */
	protected $factory;

	/**
	 * @var string
	 */
	protected $coreDirPath;

	/**
	 * RegisterFields constructor.
	 *
	 * @since 1.8.0
	 *
	 * @param FieldTypeFactoryContract $factory
	 * @param string $coreDirPath The path to the main plugin file
	 */
	public function __construct (FieldTypeFactoryContract $factory,$coreDirPath )
	{
		$this->factory = $factory;
		$this->coreDirPath = $coreDirPath;
	}

	/** @inheritdoc */
	public function getCoreDirPath()
	{
		return $this->coreDirPath;
	}

	/** @inheritdoc */
	public function filter($fields){
		if( ! empty( $this->factory->getAll() ) ){
			/** @var FieldType $field */
			foreach ( $this->factory->getAll() as $field ){
				$config = $field::toArray();
				foreach ( $config['setup' ] as $setUpKey => $setupItem ){
					$config['setup'][$setUpKey] = $this->getCoreDirPath() . $config['setup'][$setUpKey];
				}
				$config['cf2' ] = true;
				$fields[ $field::getCf1Identifier() ] = $config;
			}


		}
		return $fields;
	}


}
