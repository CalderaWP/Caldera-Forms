<?php


namespace calderawp\calderaforms\cf2\Asset;


class Register
{

    /**
     * @var string
     */
    protected $handle;
    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $scriptUrl;

    /**
     * @var string
     */
    protected $assetsFilePath;

    protected $registered;

    /**
     * Register constructor.
     * @param $handle
     * @param $coreUrl
     * @param $corePath
     * @param array $data
     */
    public function __construct($handle, $coreUrl, $corePath, array $data = [])
    {
        $this->handle = $handle;
        $this->data = $data;
        $this->registered = false;
        $this->setScriptUrl($coreUrl . '/clients/' . $this->handle . '/build/index.min.js');
        $this->setAssetsFilePath($corePath . '/clients/' . $this->handle . '/build/index.min.asset.json');
    }


    public function getScriptUrl(){
        return $this->scriptUrl;
    }

    public function getAssetFilePath(){
        return $this->assetsFilePath;
    }
    public function setScriptUrl($scriptUrl)
    {
        $this->scriptUrl = $scriptUrl;
        return $this;
    }

    public function setAssetsFilePath($assetsFilePath){
        $this->assetsFilePath = $assetsFilePath;
        return $this;
    }

    protected function getLocalizeData()
    {
        return $this->data ? $this->data : [];
    }

    /**
     * @return $this
     */
    public function register()
    {

        $assetFile = file_get_contents($this->getAssetFilePath() );
        $assetFile = (array)json_decode($assetFile, true);
        wp_register_script(
            $this->handle,
            $this->getScriptUrl(),
            $assetFile['dependencies'],
            $assetFile['version']
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
     */
    public function isRegistered()
    {
        return $this->registered;
    }

    public function enqueue()
    {
        wp_enqueue_script($this->handle);
        return $this;
    }
}