<?php


namespace calderawp\calderaforms\cf2\Fields;


interface RenderFieldContract
{

    /**
     * Get id attribute for field
     *
     * @since 1.8.0
     *
     * @return string
     */
    public function getFieldIdAttr();


    /**
     * Get id attribute for form field is being rendered in
     *
     * @since 1.8.0
     *
     * @return string
     */
    public function getFormIdAttr();

    /**
     * Create element for React to render on
     *
     * @since 1.8.0
     *
     * @return string
     */
    public function render();
    /**
     * Get data to pass to browser
     *
     * @since 1.8.0
     *
     * @return array
     */
    public function data();

    /**
     * Get id attribute for outter field
     *
     * This is the element that React renders on
     *
     * @since 1.8.0
     *
     * @return string
     */
    public function getOuterIdAttr();

}