<?php


namespace calderawp\calderaforms\cf2;


use calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType;
use calderawp\calderaforms\cf2\Fields\Handlers\FileFieldHandler;

class Hooks
{

    protected $container;
    protected $fileFieldHandler;

    public function __construct(CalderaFormsV2Contract $container )
    {
        $this->container = $container;
        $this->fileFieldHandler = new FileFieldHandler($container);
    }

    public function subscribe()
    {
        $this->addFieldHandlers();
    }


    /**
     * @return FileFieldHandler
     */
    public function getFileFieldHandler()
    {
        return $this->fileFieldHandler;
    }

    protected function addFieldHandlers()
    {

        $type = FileFieldType::getCf1Identifier();
        add_filter("caldera_forms_process_field_$type",[$this->getFileFieldHandler(),'processField'], 10, 3);
        add_filter("caldera_forms_save_field_$type",[$this->getFileFieldHandler(),'saveField'], 10, 4);
    }

}