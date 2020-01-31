<?php


namespace calderawp\calderaforms\Tests\Util\Traits;


use calderawp\calderaforms\cf2\CalderaFormsV2;

trait SharedFactories
{

    /**
     * @return CalderaFormsV2
     */
    protected function getContainer(){
        return new CalderaFormsV2();
    }
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