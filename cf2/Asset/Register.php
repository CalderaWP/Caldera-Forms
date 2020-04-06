<?php


namespace calderawp\calderaforms\cf2\Asset;


class Register
{
    /**
     *
     * @since 1.9.0
     *
     * @var string
     */

    protected $handle;
    /**
     *
     * @since 1.9.0
     *
     * @var array
     */
    protected $data;

    /**
     *
     * @since 1.9.0
     *
     * @var string
     */
    protected $scriptUrl;

    /**
     *
     * @since 1.9.0
     *
     * @var string
     */
    protected $assetsFilePath;

    /**
     *
     *
     * @since 1.9.0
     *
     * @var bool
     */
    protected $registered;

    /**
     * Register constructor.
     *
     * @param $handle
     * @param $coreUrl
     * @param $corePath
     * @param array $data
     *
     * @since 1.9.0
     *
     */
    public function __construct($handle, $coreUrl, $corePath, array $data = [])
    {
        $this->handle = $handle;
        $this->data = $data;
        $this->registered = false;
        $this->setScriptUrl($coreUrl . 'clients/' . $this->handle . '/build/index.min.js');
        $this->setAssetsFilePath($corePath . 'clients/' . $this->handle . '/build/index.min.asset.json');
    }


    /**
     *
     * @return string
     * @since 1.9.0
     *
     */
    public function getScriptUrl()
    {
        return $this->scriptUrl;
    }

    /**
     *
     * @return string
     * @since 1.9.0
     *
     */
    public function getAssetFilePath()
    {
        return $this->assetsFilePath;
    }

    /**
     * @return $this
     * @since 1.9.0
     *
     */
    public function setScriptUrl($scriptUrl)
    {
        $this->scriptUrl = $scriptUrl;
        return $this;
    }

    /**
     * @return $this
     * @since 1.9.0
     *
     */
    public function setAssetsFilePath($assetsFilePath)
    {
        $this->assetsFilePath = $assetsFilePath;
        return $this;
    }

    /**
     *
     * @return array
     * @since 1.9.0
     *
     */
    protected function getLocalizeData()
    {
        return $this->data ? $this->data : [];
    }

    /**
     * @return $this
     * @since 1.9.0
     *
     */
    public function register()
    {
        if( ! file_exists($this->getAssetFilePath())){
            return;
        }
        $assetFile = file_get_contents($this->getAssetFilePath());
        $assetFile = (array)json_decode($assetFile, true);
        wp_register_script(
            $this->getHandle(),
            $this->getScriptUrl(),
            $assetFile['dependencies'],
            $assetFile['version'],
            true
        );
        wp_localize_script($this->getHandle(), strtoupper( str_replace('-', '_', $this->getHandle())),
            array_merge($this->getLocalizeData(), [
                'strings' => [
                    'if'=> esc_html__( 'If', 'caldera-forms'),
                    'and'=> esc_html__( 'And', 'caldera-forms'),
                    'name'=> esc_html__('Name', 'caldera-forms'),
                    'disable'=> esc_html__( 'Disable', 'caldera-forms'),
                    'type'=> esc_html__('Type', 'caldera-forms'),
                    'add-conditional-group'=> esc_html__( 'Add Rule', 'caldera-forms'),
                    'applied-fields'=> esc_html__( 'Applied Fields', 'caldera-forms'),
                    'select-apply-fields'=> esc_html__( 'Select the fields to apply this condition to.', 'caldera-forms'),
                    'remove-condition'=> esc_html__( 'Remove Conditon', 'caldera-forms'),
                    'remove-condfirm' => esc_html__('Are you sure you would like to remove this conditional group', 'caldera-forms'),
                    'show'=> esc_html__('Show', 'caldera-forms'),
                    'hide' => esc_html__( 'Hide', 'caldera-forms'),
                    'new-conditional'=> esc_html__( 'New Conditon', 'caldera-forms'),
                    'fields' => esc_html__('Fields', 'caldera-forms'),
                     'add-condition' => esc_html__('Add Line', 'caldera-forms')
                ]
            ]));
        $this->registered = true;
        return $this;

    }

    /**
     * @return bool
     * @since 1.9.0
     *
     */
    public function isRegistered()
    {
        return $this->registered;
    }

    /**
     * @return $this
     * @since 1.9.0
     *
     */
    public function enqueue()
    {
        if (!$this->isRegistered()) {
            $this->register();
        }
        
        wp_enqueue_script($this->getHandle());
        return $this;
    }

    protected function getHandle(){
        if( 0 === substr( $this->handle, '3')){
            return $this->handle;
        }
        return 'cf-' . $this->handle;
    }
}