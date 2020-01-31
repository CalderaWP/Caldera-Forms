<?php


class Caldera_Forms_Admin_Factory
{
    /*
    * Add a menu page
    * @param string $page_postfix
    * @param string $menu_label
    * @param string|callable $render
    * @param array $assets
    * @param null|string $menu_icon
    */
    public static function menu_page($page_postfix, $menu_label, $render, array $assets = [], $menu_icon = null)
    {
        $page = new Caldera_Forms_Admin_Page($page_postfix, $menu_label, $render, $assets, $menu_icon);
        add_action('admin_menu', [$page, 'display']);
    }
}