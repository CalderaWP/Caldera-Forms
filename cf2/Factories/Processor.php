<?php


namespace calderawp\calderaforms\cf2\Factories;

/**
 * Class Processor
 *
 * Factory for Caldera Forms processors
 */
class Processor implements ProcessorFactory
{

    /**
     * Factory for processor data
     *
     * @since 1.8.10
     *
     * Designed to be used in callback for processors
     *
     * @param array $config Saved settings for processor
     * @param array $form Saved settings for this form
     * @param array $fields Processor settings field configuration
     *
     * @return \Caldera_Forms_Processor_Data
     */
    public function dataFactory(array $config,array $form,array $fields){
        return new \Caldera_Forms_Processor_Get_Data($config,$form,$fields);
    }

}