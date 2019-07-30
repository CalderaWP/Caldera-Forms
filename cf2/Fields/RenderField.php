<?php


namespace calderawp\calderaforms\cf2\Fields;


use calderawp\calderaforms\cf2\Fields\FieldTypes\FileFieldType;
use calderawp\calderaforms\cf2\Fields\FieldTypes\TextFieldType;
use calderawp\calderaforms\cf2\Traits\ConvertsMimeTypes;
use function foo\func;

class RenderField implements RenderFieldContract
{
	use ConvertsMimeTypes;
    /**
     *
     * @since 1.8.0
     *
     * @var array
     */
    protected $field;

    /**
     *
     * @since 1.8.0
     *
     * @var string
     */
    protected $formIdAttr;

    /**
     *
     * @since 1.8.0
     *
     * @var array
     */
    protected $domArgs;

    /**
     * RenderField constructor.
     *
     * @since 1.8.0
     *
     * @param string $formIdAttr Id attribute for form
     * @param array $field Field configuration (MAKE THIS AN OBJECT!)
     */
    public function __construct($formIdAttr, array $field, array $domArgs = [] )
    {
        $this->formIdAttr = $formIdAttr;
        $this->field = $field;
        $this->domArgs = $domArgs;
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
            case FileFieldType::getCf1Identifier() :
                return FileFieldType::getType();
                break;
            case TextFieldType::getCf1Identifier() :
            default:
                return TextFieldType::getType();
                break;
        }
    }

    /** @inheritdoc */
    public function data()
    {

        $data = [
            'type' => $this->getType(),
            'outterIdAttr' => $this->getOuterIdAttr(),
            'fieldId' => $this->field['ID'],
            'fieldLabel' => $this->field['label'],
			'hideLabel'	=>	! empty($this->field['hide_label'])?true:false,
            'fieldCaption' => $this->field['caption'],
            'fieldPlaceHolder' => '',
            'isRequired' => ! empty($this->field['required'])?true:false,
            'fieldDefault' => isset($this->field['config']['default'])? $this->field['config']['default'] : '',
            'fieldValue' => '',
            'fieldIdAttr' => $this->field['fieldIdAttr'],
            'configOptions' => []
        ];

        if( FileFieldType::getType() === $this->getType() ){
            $data['configOptions'] = [
                'multiple'=> ! empty($this->field[ 'config' ]['multi_upload' ]) ? $this->field[ 'config' ]['multi_upload' ] : false,
                'multiUploadText' => ! empty($this->field[ 'config' ]['multi_upload_text' ]) ? $this->field[ 'config' ]['multi_upload_text' ] : false,
                'allowedTypes' => $this->getAllowedTypes(),
                'control' => uniqid($this->getOuterIdAttr() ),
                'usePreviews' => ! empty($this->field[ 'config']['use_image_previews']) ? true : false,
				'previewWidth' => ! empty( $this->field[ 'config']['preview_width'] ) ? intval( $this->field[ 'config']['preview_width'] ) : 24,
				'previewHeight' => ! empty( $this->field[ 'config']['preview_height'] ) ? intval( $this->field[ 'config']['preview_height'] ) : 24,
				'maxFileUploadSize' => ! empty( $this->field[ 'config']['max_upload'] ) ? intval( $this->field[ 'config']['max_upload'] ) : 0,
            ];
        }

        return array_merge( $data, $this->domArgs) ;
    }

    /** @inheritdoc */
    public function getOuterIdAttr()
    {
        return sprintf('cf2-%s', $this->getFieldIdAttr());
    }

	/**
	 * Get the allowed types string
	 *
	 * @since 1.8.0
	 *
	 * @return string|bool
	 */
	protected function getAllowedTypes()
	{
		$types = !empty($this->field[ 'config' ][ 'allowed' ]) ? $this->field[ 'config' ][ 'allowed' ] : false;
		if( is_string( $types ) ){
			$types = explode(',', $types );
			$types =array_map(function ($item){
				$mimeType = $this->extensionToMimeType($item);
				if( $mimeType ){
					return $mimeType;
				}
				return $item;
			},$types);
			foreach ( $types as $i  => $type ){
				if( is_array( $type ) ){
					$types[$i] = implode(',', $type );
				}
			}
			$types = implode(',', $types );
		}
		return $types;
	}
}
