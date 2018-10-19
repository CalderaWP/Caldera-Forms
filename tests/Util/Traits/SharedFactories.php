<?php


namespace calderawp\calderaforms\Tests\Util\Traits;


trait SharedFactories
{

    public function fieldFactory($type,$fieldId = 'fld_12345')
    {
        switch($type){
            case 'email':
            default:
                return  array(
                    'ID'         => $fieldId,
                    'type'       => 'email',
                    'label'      => 'Email',
                    'slug'       => 'email',
                    'conditions' =>
                        array(
                            'type' => '',
                        ),
                    'required'   => 1,
                    'caption'    => 'Make emails',
                    'entry_list' => 1,
                    'config'     =>
                        array(
                            'custom_class' => '',
                            'placeholder'  => '',
                            'default'      => '',
                        ),
                );
                break;
        }
    }

}