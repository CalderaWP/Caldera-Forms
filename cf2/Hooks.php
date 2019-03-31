<?php


namespace calderawp\calderaforms\cf2;


use calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType;
use calderawp\calderaforms\cf2\Fields\Handlers\FileFieldHandler;
use calderawp\calderaforms\cf2\Fields\RegisterFields;

class Hooks
{

    protected $container;
    protected $fileFieldHandler;


    public function __construct(CalderaFormsV2Contract $container )
    {
        $this->container = $container;
        $this->fileFieldHandler = new FileFieldHandler($container);
    }

    /**
     * Subscribe to all events
	 *
	 * @since 1.8.0
     */
    public function subscribe()
    {
        $this->addFieldHandlers();
        $register = new RegisterFields(
        	$this->container->getFieldTypeFactory(),
			$this->container->getCoreDir()
		);
        add_filter('caldera_forms_get_field_types', [$register, 'filter' ], 2 );
        add_action( 'caldera_forms_rest_api_init', [$this, 'addJwtToApi' ], 10, 2 );
        add_filter( 'calderaForms/restApi/createEntry/addField',
			function( \Caldera_Forms_Entry_Field $entryField, \Caldera_Forms_Entry $entry, array  $fieldConfig){
				$entryField->value = \Caldera_Forms::validate_field_with_filters($fieldConfig,$entryField->get_value(),$entry->get_form());
				return $entryField;
			},
			10, 3
		);
    }

	/**
	 * Attach Form JWT to endpoints
	 *
	 * @since 1.9.0
	 *
	 * @uses "caldera_forms_rest_api_init" filter
	 *
	 * @param \Caldera_Forms_API_Load $v1
	 * @param RestApi\Register $v2
	 */
    public function addJwtToApi(\Caldera_Forms_API_Load $v1, \calderawp\calderaforms\cf2\RestApi\Register $v2 )
	{
		$v2->setJwt(
			$this->container->getFormJwt()
		);

	}

    /**
     * @return FileFieldHandler
     */
    public function getFileFieldHandler()
    {
        return $this->fileFieldHandler;
    }

    /**
     * Add field handlers filters
     *
     * @since 1.8.0
     */
    protected function addFieldHandlers()
    {

        $fieldType = FileFieldType::getCf1Identifier();
        add_filter("caldera_forms_process_field_$fieldType",[$this->getFileFieldHandler(),'processField'], 10, 3);
        add_filter("caldera_forms_save_field_$fieldType",[$this->getFileFieldHandler(),'saveField'], 10, 4);
        add_filter("caldera_forms_view_field_$fieldType",[$this->getFileFieldHandler(),'viewField'], 10, 4);
    }

}
