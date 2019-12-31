<?php
/**
 * Test locale set to enqueue assets
 *
 * @package Caldera_Forms
 * @since 1.8.7
 * @license   GPL-2.0+
 */
class TestLocaleRenderAssets extends Caldera_Forms_Test_Case
{
    /**
     * Test a locale which corresponding file exists within assets/js/i18n/
     * 
     * @since 1.8.7
     *
     * @covers  \Caldera_Forms_Render_Assets::set_locale_code($locale)
     * @covers  \Caldera_Forms_Render_Assets::get_validator_locale_url($locale)
     */
    public function testKnownLocale()
    {
        
        $locale = "fr";
        $code =   Caldera_Forms_Render_Assets::set_locale_code($locale);
        $validator_url = Caldera_Forms_Render_Assets::get_validator_locale_url($locale);

        $this->assertSame( $code, "fr" );
        $this->assertSame( substr($validator_url, -10), "i18n/fr.js" );
 
    }

     /**
      * Test a locale which corresponding file doesn't exist within assets/js/i18n/
      * 
      * @since 1.8.7
      *
      * @covers  \Caldera_Forms_Render_Assets::set_locale_code($locale)
      * @covers  \Caldera_Forms_Render_Assets::get_validator_locale_url($locale)
     */
    public function testUnknownLocale()
    {
        
        $locale = "bo";
        $code =   Caldera_Forms_Render_Assets::set_locale_code($locale);
        $validator_url = Caldera_Forms_Render_Assets::get_validator_locale_url($locale);

        $this->assertSame( $code, "en" );
        $this->assertSame( substr($validator_url, -10), "i18n/en.js" );
    }

}
