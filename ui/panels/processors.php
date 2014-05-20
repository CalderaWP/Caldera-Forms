<?php
global $form_processors;
//dump($element,0);
// Get Processors
$form_processors = apply_filters('caldera_forms_get_form_processors', array() );

//dump($form_processors,0);

$form_processors_defaults = array(
	"var processor_defaults = {};"
);

foreach($form_processors as $processor=>$config){
	
	if(!empty($config['default'])){
		$form_processors_defaults[] = "processor_defaults." . sanitize_key( $processor ) . "_cfg = " . json_encode($config['default']) .";";
	}

}

function processor_line_template($id = '{{id}}', $type = null){
	global $form_processors;

	$type_name = __('New Form Processor', 'caldera-forms');
	if(!empty($type)){
		if(empty($form_processors[$type])){
			return;
		}		
		if(isset($form_processors[$type]['name'])){
			$type_name = $form_processors[$type]['name'];
		}
	}

	?>
	<li class="caldera-processor-nav <?php echo $id; ?>">
		<a href="#<?php echo $id; ?>">
		<?php echo $type_name; ?>
		</a>
	</li>
	<?php
}

function processor_wrapper_template($id = '{{id}}', $type = null, $config_str = '{"default":"default value"}'){
	
	global $form_processors;

	$type_name = __('New Form Processor', 'caldera-forms');
	if(!empty($type)){
		if(empty($form_processors[$type])){
			return;
		}		

		if(isset($form_processors[$type]['name'])){
			$type_name = $form_processors[$type]['name'];
		}
	}

	if(is_array($config_str)){
		$config 	= $config_str;
		$config_str = json_encode( $config_str );

	}else{
		$config = json_decode($config_str, true);
	}

	?>
	<div class="caldera-editor-processor-config-wrapper" id="<?php echo $id; ?>" style="display:none;">
		<button class="button button-small pull-right delete-processor" type="button"><i class="icn-delete"></i></button>
		<h3 data-title="<?php echo __('New Form Processor', 'caldera-forms'); ?>" class="caldera-editor-processor-title"><?php echo $type_name; ?></h3>
		<div class="caldera-config-group" style="display:none;">
			<label for="<?php echo $id; ?>_type"><?php echo __('Processor Type', 'caldera-forms'); ?></label>
			<div class="caldera-config-field">
				<select class="block-input caldera-select-processor-type" id="<?php echo $id; ?>_type" name="config[processors][<?php echo $id; ?>][type]" data-type="<?php echo $type; ?>">					
					<?php
					echo build_processor_types($type);
					?>
				</select>
			</div>
		</div>
		<div class="caldera-config-processor-setup">
		</div>
		<input type="hidden" class="processor_config_string block-input" value="<?php echo htmlentities( $config_str ); ?>">
	</div>
	<?php
}

function build_processor_types($default = null){
	global $form_processors;
	
	$out = '';
	if(null === $default){
		$out .= '<option></option>';
	}
	foreach($form_processors as $processor=>$config){
		$sel = "";
		if($default == $processor){
			$sel = 'selected="selected"';
		}
		$out .= "<option value=\"". $processor . "\" ". $sel .">" . $config['name'] . "</option>\r\n";

	}

	return $out;

}



?>


<div class="caldera-editor-processors-panel">
	<button type="button" class="button block-button ajax-trigger" 
	data-request="new_form_processor" 
	data-modal="form_processor" 
	data-modal-title="<?php echo __('Select Form Processor', 'caldera-forms'); ?>"
	data-modal-height="500"
	data-template="#form-processors-tmpl"
	><?php echo __('Add Processor', 'caldera-forms'); ?></button>
	<ul class="active-processors-list">
		<?php
			// build processors list
			if(!empty($element['processors'])){
				foreach($element['processors'] as $processor_id=>$config){
					echo processor_line_template($processor_id, $config['type']);
				}
			}
		?>
	</ul>
</div>
<div class="caldera-editor-processor-config">
<?php

/// PROCESSORS CONFIGS
if(!empty($element['processors'])){
	foreach($element['processors'] as $processor_id=>$config){
		
		$config_str = array();
		if(!empty($config['config'])){
			$config_str = json_encode($config['config']);
		}
		processor_wrapper_template($processor_id, $config['type'], $config_str);
	}
}

?>
</div>
<div class="clear"></div>

<script type="text/html" id="form-processors-tmpl">
	<?php
		global $form_processors;

		foreach($form_processors as $processor_id=>$processor){
			echo '<div class="form-processor-add-line">';
				echo '<button type="button" class="button add-new-processor" data-type="' . $processor_id . '" style="float:right;">' . __('Use Processor', 'caldera-forms') . '</button>';
				echo '<strong>' . $processor['name'] .'</strong>';
				echo '<p class="description">' . $processor['description'] . '</p>';
			echo '</div>';
		}

	?>
</script>
<script type="text/html" id="processor-line-tmpl">
<?php echo processor_line_template(); ?>
</script>
<script type="text/html" id="processor-wrapper-tmpl">
<?php echo processor_wrapper_template(); ?>
</script>
<?php

foreach($form_processors as $processor=>$config){
	if(isset($config['template'])){
		echo "<script type=\"text/html\" id=\"" . $processor . "-tmpl\">\r\n";
			include $config['template'];
		echo "\r\n</script>\r\n";		
	}

}
?>
<script type="text/javascript">

<?php echo implode("\r\n", $form_processors_defaults); ?>

function new_form_processor(obj){

	console.log(obj)

	return {};
}


</script>











