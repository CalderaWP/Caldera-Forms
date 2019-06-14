<?php

define( 'CFCORE_PATH', dirname( __DIR__ ) );
define( 'CFCORE_URL', '' );
define( 'CFCORE_VER', '' );
define( 'CFCORE_EXTEND_URL', '' );
define( 'CFCORE_BASENAME', '' );
define( 'CF_DB', 0 );
define( 'CF_PRO_VER', CFCORE_VER );

function caldera_forms_load() {}
function cep_get_easy_pod( $param1 ) { return []; }
function cep_get_registry() { return []; }
function pods( $param1 ) { return ''; }
class LiteSpeed_Cache_API {
    static function esi_enabled() { return true; }
    static function v( $param1 ) { return true; }
    static function esi_url( $param1, $param2, $param3 ) {}
    static function hook_tpl_esi( $param1, $param2 ) {}
}
class Caldera_Easy_Pods {
    static function get_instance() { return new Caldera_Easy_Pods(); }
    function apply_query( $param1 ) {}
}
function wc_get_screen_ids() { return []; }
