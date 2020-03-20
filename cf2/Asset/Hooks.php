<?php


namespace calderawp\calderaforms\cf2\Asset;


use calderawp\calderaforms\cf2\CalderaFormsV2Contract;

class Hooks
{

    /**
     * @var Register[]
     */
    protected $handlers = [];

    /**
     * @var array
     */
    protected $handles;
    /**
     * @var array
     */
    protected $manifest;

    /**
     * @var CalderaFormsV2Contract
     */
    protected $container;

    public function __construct(array $handles, CalderaFormsV2Contract $container, array $manifest = [])
    {
        $this->handles = $handles;
        $this->manifest = $manifest;
        $this->container = $container;

    }

    public function subscribe()
    {

        $this->maybeUseManifest();
        add_action('wp_register_scripts', [$this, 'registerAssets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
    }

    /**
     * If a webpack asset-manifest.json was used, substitute URLs from that array
     */
    protected function maybeUseManifest()
    {
        if (!empty($this->manifest) && !empty($this->handles)) {
            foreach ($this->handles as $handle) {
                $assetFilePath = isset($this->manifest["{$handle}.json"]) ? $this->manifest["{$handle}.json"] : null;
                if (!is_null($assetFilePath)) {
                    $this->getHandler($handle)->setAssetsFilePath($assetFilePath);
                }
                $scriptsUrl = isset($this->manifest["{$handle}.js"]) ? $this->manifest["{$handle}.js"] : null;
                if (!is_null($scriptsUrl)) {
                    $this->getHandler($handle)->setScriptUrl($scriptsUrl);

                }


            }

        }
    }

    /**
     * @param $handle
     * @return Register|null
     */
    public function getHandler($handle)
    {
        if (in_array($handle, $this->handles)) {
            if (!array_key_exists($handle, $this->handlers)) {
                $this->handlers[$handle] = new Register(
                    $handle,
                    $this->container->getCoreUrl(),
                    $this->container->getCoreDir(),
                    []
                );
            }
            return $this->handlers[$handle];
        }
    }

    /**
     * Register all assets
     */
    public function registerAssets()
    {
        $this->getHandler('form-builder')->register();
    }

    public function enqueueAdminAssets($hook)
    {
        if ('toplevel_page_caldera-forms' !== $hook) {
            return;
        }

        if (\Caldera_Forms_Admin::is_edit()) {
            $this->getHandler('form-builder')->enqueue();
        }
    }
}