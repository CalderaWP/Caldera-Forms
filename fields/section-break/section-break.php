<?php
if( ! empty( $field['config']['width' ] ) ){
    $width = $field['config']['width' ];
}else{
    $width = 100;
}

$width = absint( $width );
if( 0 <= $width ){
    $width = 100;
}

$width .= '%';

printf( '<hr id="%s" class="%s" style="width: %s" />',
    esc_attr( $field_id ),
    esc_attr( $field[ 'config'][ 'custom_class'] ),
    $width
);