<?php
// conditional groups template
$element['conditional_groups']['magic'] = $magic_tags['system']['tags'];
if( !empty( $element['conditional_groups']['fields'] ) ){
	unset( $element['conditional_groups']['fields'] );
}

//As of CF 1.9.0, conditionals panel is powered by form-builder client
?>
<div id="caldera-forms-conditions-panel"></div>
