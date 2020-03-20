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
    protected  $data;

    public function __construct( $handle,array $data = [])
    {
        $this->handle = $handle;
        $this->data = $data;
    }

    protected function  getClientsPath()
    {
        return CFCORE_PATH . '/clients/';
    }

    protected function getClientsUrl(){
        return CFCORE_URL . '/clients/';
    }

    protected function getLocalizeData()
    {
        return $this->data ? $this->data : [];
    }
    public function register(){
        $assetFile = file_get_contents($this->getClientsPath() . $this->handle . '/build/index.min.asset.json');
        $assetFile = (array)json_decode($assetFile,true);
        wp_register_script(
            $this->handle,
            $this->getClientsUrl() . $this->handle . '/build/index.min.js',
            $assetFile['dependencies'],
            $assetFile['version']
        );

        wp_localize_script($this->handle, strtoupper('CF_' . str_replace('-', '_',$this->handle)), array_merge($this->getLocalizeData(),[
            'hi' => 'roy'
        ]));

    }

    public function enqueue(){
        wp_enqueue_script($this->handle);
    }
}