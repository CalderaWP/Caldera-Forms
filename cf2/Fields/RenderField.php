<?php


namespace calderawp\calderaforms\cf2\Fields;


class RenderField implements RenderFieldContract
{

    /**
     * @var array
     */
    protected $field;

    /**
     * @var string
     */
    protected $formIdAttr;

    /**
     * RenderField constructor.
     * @param string $formIdAttr Id attribute for form
     * @param array $field Field configuration (MAKE THIS AN OBJECT!)
     */
    public function __construct($formIdAttr, array $field)
    {
        $this->formIdAttr = $formIdAttr;
        $this->field = $field;
    }

    /** @inheritdoc */
    public function getFieldIdAttr()
    {
        return $this->field['fieldIdAttr'];
    }

    /** @inheritdoc */
    public function getFormIdAttr()
    {
        return $this->formIdAttr;
    }

    /** @inheritdoc */
    public function render()
    {
        //this concern does not belong here
        if (function_exists('wp_add_inline_script')) {
            wp_add_inline_script('cf-render',
                sprintf('window.cf2 = window.cf2 || {}; window.cf2.%1s = window.cf2.%2s || {fields:{}}; window.cf2.%3s.fields.%4s=%5s;',
                    esc_js($this->getFormIdAttr()),
                    esc_js($this->getFormIdAttr()),
                    esc_js($this->getFormIdAttr()),
                    esc_js($this->getFieldIdAttr()),
                    json_encode($this->data()))
            );
        }

        return sprintf('<div id="%s" class="cf2-field-wrapper" data-field-id="%s"></div>',
            esc_attr($this->getOuterIdAttr()),
            esc_attr($this->getFieldIdAttr())
        );

    }

    /**
     * Get type of field
     *
     * @since 1.8.0
     *
     * @return string
     */
    protected function getType()
    {
        switch ($this->field['type']) {
            case 'cf2_file' :
                return 'file';
                break;
            case 'text2':
            default:
                return 'text';
                break;
        }
    }

    /** @inheritdoc */
    public function data()
    {
        return [
            'type' => $this->getType(),
            'outterIdAttr' => $this->getOuterIdAttr(),
            'fieldId' => $this->field['ID'],
            'fieldLabel' => $this->field['label'],
            'fieldCaption' => $this->field['caption'],
            'fieldPlaceHolder' => '',
            'required' => $this->field['required'],
            'fieldDefault' => $this->field['config']['default'],
            'fieldValue' => '',
            'fieldIdAttr' => $this->field['fieldIdAttr'],

        ];
    }


    public function getOuterIdAttr()
    {
        return sprintf('cf2-%s', $this->getFieldIdAttr());
    }
}