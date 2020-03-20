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
     */
    public function subscribe()
    {
        $this->addFieldHandlers();
        $register = new RegisterFields(
        	$this->container->getFieldTypeFactory(),
			$this->container->getCoreDir()
		);
        add_filter('caldera_forms_get_field_types', [$register, 'filter' ], 2 );
        add_action('wp_register_scripts', [$this,'registerAssets']);
        add_action('admin_enqueue_scripts', [$this,'enqueueAdminAssets']);
    }

    /**
     * @var Asset\Register[]
     */
    protected $assets = [];
    public function registerAssets(){
        if( empty( $this->assets ) ){
            $this->assets['form-builder'] = new Asset\Register('form-builder', []);

        }
        $this->assets['form-builder']->register();
    }

    public function enqueueAdminAssets($hook){
        if( 'toplevel_page_caldera-forms' !== $hook ){
            return;
        }
        if( empty( $this->assets ) ){
            $this->registerAssets();
        }
        if( \Caldera_Forms_Admin::is_edit()) {
            $this->assets['form-builder']->enqueue();
        }
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
