<?php
if( file_exists(__DIR__ . '/screenshot.jpeg') ){
    echo md5_file(__DIR__ . '/screenshot.jpeg');
}
exit;
