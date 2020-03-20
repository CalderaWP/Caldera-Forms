<?php
// conditional groups template
$element['conditional_groups']['magic'] = $magic_tags['system']['tags'];
if( !empty( $element['conditional_groups']['fields'] ) ){
	unset( $element['conditional_groups']['fields'] );
}
?>

<div id="caldera-forms-conditions-panel"></div>
