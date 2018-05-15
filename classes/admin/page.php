<?php


class Caldera_Forms_Admin_Page
{

    /**
     * @var array
     */
    protected $assets;
    /**
     * @var string
     */
    protected $slug;

    protected $render;
    protected $menu_label;
    protected $menu_icon;

    /**
     * Caldera_Forms_Admin_Page constructor.
     *
     * @param string $page_postfix
     * @param string $menu_label
     * @param string|callable $render
     * @param array $assets
     * @param null|string $menu_icon
     */
    public function __construct($page_postfix, $menu_label, $render, array $assets = [], $menu_icon = null)
    {
        $this->slug = Caldera_Forms::PLUGIN_SLUG . '-' . $page_postfix;
        $this->menu_label = $menu_label;
        $this->menu_icon = !$menu_icon ? 'admin-page' : trim(str_replace('dashicons-', '', $menu_icon));
        $this->assets = wp_parse_args($assets, [
            'script' => [],
            'styles' => []
        ]);

        $this->render = $render;
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
            $this->slug,
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
        $handle =! empty( $this->assets['scripts'] ) && isset( $this->assets['scripts'][0] ) ? $this->assets[ 'scripts' ][0] : 'admin';
        caldera_forms_print_cf_forms_var($handle);
        if (is_callable($this->render)) {
            call_user_func($this->render);
        } else {
            echo $this->render;

        }


    }


    public function enqueue_assets()
    {
        if ( ! empty( $this->assets[ 'scripts' ] ) ) {
            foreach ($this->assets['scripts'] as $handle) {
                Caldera_Forms_Render_Assets::enqueue_script($handle);
            }
        }

        if ( ! empty( $this->assets[ 'styles' ] ) ) {
            foreach ($this->assets['styles'] as $handle ) {
                Caldera_Forms_Render_Assets::enqueue_style($handle);
            }
        }
    }

}