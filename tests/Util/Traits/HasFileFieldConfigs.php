<?php


namespace calderawp\calderaforms\Tests\Util\Traits;


trait HasFileFieldConfigs
{

    /**
     * Get one of our file field configs
     *
     * @since 1.8.0
     *
     * @param string $fieldSlug Field slug or ID of field config to find
     * @return array
     */
    protected function getFileFieldConfig($fieldSlug){
        return isset( $this->getFileFieldConfigs()[$fieldSlug] )
        ?$this->getFileFieldConfigs()[$fieldSlug]
            : [];
    }

    /**
     * Get file field configs with many types of options
     *
     * @since 1.8.0
     *
     * @return array
     */
    protected function getFileFieldConfigs(){
        return array (
            'required_single' =>
                array (
                    'ID' => 'required_single',
                    'type' => 'advanced_file',
                    'label' => 'Required Single',
                    'slug' => 'required_single',
                    'conditions' =>
                        array (
                            'type' => '',
                        ),
                    'required' => 1,
                    'caption' => '',
                    'config' =>
                        array (
                            'custom_class' => '',
                            'multi_upload_text' => '',
                            'allowed' => '',
                        ),
                ),
            'required_multiple_no_button_text' =>
                array (
                    'ID' => 'required_multiple_no_button_text',
                    'type' => 'advanced_file',
                    'label' => 'Required Multiple No Button Text',
                    'slug' => 'required_multiple_no_button_text',
                    'conditions' =>
                        array (
                            'type' => '',
                        ),
                    'required' => 1,
                    'caption' => '',
                    'config' =>
                        array (
                            'custom_class' => '',
                            'multi_upload' => 1,
                            'multi_upload_text' => '',
                            'allowed' => '',
                        ),
                ),
            'required_multiple_has_button_text' =>
                array (
                    'ID' => 'required_multiple_has_button_text',
                    'type' => 'advanced_file',
                    'label' => 'Required Multiple Has Button Text',
                    'slug' => 'required_multiple_has_button_text',
                    'conditions' =>
                        array (
                            'type' => '',
                        ),
                    'required' => 1,
                    'caption' => '',
                    'config' =>
                        array (
                            'custom_class' => '',
                            'multi_upload' => 1,
                            'multi_upload_text' => 'The Default Text',
                            'allowed' => '',
                        ),
                ),
            'not_required_single' =>
                array (
                    'ID' => 'not_required_single',
                    'type' => 'advanced_file',
                    'label' => 'Not Required Single',
                    'slug' => 'not_required_single',
                    'conditions' =>
                        array (
                            'type' => '',
                        ),
                    'caption' => '',
                    'config' =>
                        array (
                            'custom_class' => '',
                            'multi_upload_text' => '',
                            'allowed' => '',
                        ),
                ),
            'not_required_multiple_no_button_text' =>
                array (
                    'ID' => 'not_required_multiple_no_button_text',
                    'type' => 'advanced_file',
                    'label' => 'Not Required Multiple No Button Text',
                    'slug' => 'not_required_multiple_no_button_text',
                    'conditions' =>
                        array (
                            'type' => '',
                        ),
                    'caption' => '',
                    'config' =>
                        array (
                            'custom_class' => '',
                            'multi_upload' => 1,
                            'multi_upload_text' => '',
                            'allowed' => '',
                        ),
                ),
            'not_required_multiple_has_button_text' =>
                array (
                    'ID' => 'not_required_multiple_has_button_text',
                    'type' => 'advanced_file',
                    'label' => 'Not Required Multiple Has Button Text',
                    'slug' => 'not_required_multiple_has_button_text',
                    'conditions' =>
                        array (
                            'type' => '',
                        ),
                    'caption' => '',
                    'config' =>
                        array (
                            'custom_class' => '',
                            'multi_upload' => 1,
                            'multi_upload_text' => 'The Default Text',
                            'allowed' => '',
                        ),
                ),
        );
    }


}