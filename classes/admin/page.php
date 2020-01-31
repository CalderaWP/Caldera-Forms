<?php


class Caldera_Forms_Admin_Page
{

    /**
     * Name of dashicon for menu
     *
     * @since 1.7.0
     *
     * @var  string
     */
    protected $assets;

    /**
     * Used to form menu slug `caldera-forms-$page_postfix`
     *
     * @since 1.7.0
     *
     * @var  string
     */
    protected $page_postfix;

    /**
     * String to render in admin or callback function to create that string.
     *
     * @since 1.7.0
     *
     * @var  string|callable
     */
    protected $render;

    /**
     * Name of dashicon for menu
     *
     * @since 1.7.0
     *
     * @var  string
     */
    protected $menu_label;

    /**
     * Name of dashicon for menu
     *
     * @since 1.7.0
     *
     * @var  string
     */
    protected $menu_icon;

    /**
     * Caldera_Forms_Admin_Page constructor.
     *
     * @since 1.7.0
     *
     * @param string $page_postfix Used to form menu slug `caldera-forms-$page_postfix`
     * @param string $menu_label The label to use in the menu
     * @param string|callable $render String to render in admin or callback function to create that string.
     * @param array $assets List of handles for scripts and styles to enqueue.
     * @param null|string $menu_icon Name of dashicon for menu
     */
    public function __construct($page_postfix, $menu_label, $render, array $assets = [], $menu_icon = null)
    {
        $this->page_postfix = $this->page_prefix() . $page_postfix;
        $this->menu_label = $menu_label;
        $this->menu_icon = !$menu_icon ? 'admin-page' : trim(str_replace('dashicons-', '', $menu_icon));
        $this->assets = wp_parse_args($assets, [
            'script' => [],
            'styles' => []
        ]);

        $this->render = $render;
    }

    /**
     * Get the page prefix for this menu
     *
     * @since 1.7.0
     *
     * @return string
     */
    public function get_page_postfix()
    {
        return str_replace($this->page_prefix(), '', $this->page_postfix);
    }

    /**
     * Create admin page view
     *
     * @since 1.7.0
     */
    public function display()
    {
        $label = sprintf(
            '<span class="caldera-forms-menu-dashicon"><span class="dashicons dashicons-%s"></span>%s</span>', esc_attr($this->menu_icon), $this->menu_label);

        add_submenu_page(
            \Caldera_Forms::PLUGIN_SLUG,
            $this->menu_label,
            $label,
            'manage_options',
            $this->page_postfix,
            [$this, 'render']
        );
    }

    /**
     * Render admin page view
     *
     * @since  1.7.0
     */
    public function render()
    {

        $this->enqueue_assets();
        $handle = !empty($this->assets['scripts']) && isset($this->assets['scripts'][0]) ? $this->assets['scripts'][0] : 'admin';
        caldera_forms_print_cf_forms_var($handle);
        wp_enqueue_script('wp-api-request');
        Caldera_Forms_Admin_Assets::set_cf_admin(Caldera_Forms_Render_Assets::make_slug($handle));
        if (is_callable($this->render)) {
            call_user_func($this->render);
        } else {
            echo $this->render;
        }

        /**
         * Runs after the HTML for an admin client is outputted
         *
         * @since 1.7.0
         *
         * @param Caldera_Forms_Admin_Page $page
         */
        do_action('caldera_forms_client_element_rendered', $this);

    }

    /**
     * Enqueue assets for this page
     *
     * @since 1.7.0
     */
    public function enqueue_assets()
    {

        Caldera_Forms_Render_Assets::maybe_register();
        if (!empty($this->assets['scripts'])) {
            foreach ($this->assets['scripts'] as $handle) {
                Caldera_Forms_Render_Assets::enqueue_script($handle);
            }
        }

        if (!empty($this->assets['styles'])) {
            foreach ($this->assets['styles'] as $handle) {
                Caldera_Forms_Render_Assets::enqueue_style($handle);
            }
        }

        Caldera_Forms_Admin_Assets::enqueue_style('editor-grid');
        Caldera_Forms_Admin_Assets::enqueue_style('admin');
    }

    /**
     * @return string
     */
    private function page_prefix()
    {
        return Caldera_Forms::PLUGIN_SLUG . '-';
    }

}
