<?php
global $field_config_panels;
?>
<div id="field_config_panels"><?php

if(!empty($field_config_panels)){
	echo implode("\r\n", $field_config_panels);
}

?></div>