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
        $this->setAssetsFilePath($corePath . '/clients/' . $this->handle . '/build/index.min.asset.json');
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

        $assetFile = file_get_contents($this->getAssetFilePath());
        $assetFile = (array)json_decode($assetFile, true);
        wp_register_script(
            $this->getHandle(),
            $this->getScriptUrl(),
            $assetFile['dependencies'],
            $assetFile['version'],
            true
        );
       

        wp_localize_script($this->handle, strtoupper('CF_' . str_replace('-', '_', $this->handle)),
            array_merge($this->getLocalizeData(), [
                'hi' => 'roy'
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