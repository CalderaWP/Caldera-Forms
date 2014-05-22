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
	<li class="caldera-processor-nav <?php echo $id; ?> <?php if(!empty($type)){ echo 'processor_type_'.$type; }; ?>">
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
		<button class="button button-small pull-right delete-processor" data-confirm="<?php echo __('Are you sure you want to remove this processor?', 'caldera-forms'); ?>" type="button"><i class="icn-delete"></i></button>
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
	data-modal-title="<?php echo __('Form Processors', 'caldera-forms'); ?>"
	data-modal-height="500"
	data-template="#form-processors-tmpl"
	data-callback="hide_single_processors"
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
			$icon = CFCORE_URL . "/assets/images/processor.png";
			if(!empty($processor['icon'])){
				$icon = $processor['icon'];
			}
			echo '<div class="form-modal-add-line'. ( !empty($processor['single']) ? ' is_single_processor' : null ) . '" data-type="' . $processor_id . '">';
				echo '<button type="button" class="button info-button add-new-processor" data-type="' . $processor_id . '">' . __('Use Processor', 'caldera-forms') . '</button>';
				echo '<img src="'. $icon .'" class="form-modal-lgo" width="45" height="45">';
				echo '<strong>' . $processor['name'] .'</strong> ';
				if(!empty($processor['author'])){
					echo '<small><span class="description">';
					echo __('by', 'caldera-forms') . ' ';
					if(!empty($processor['author_url'])){
						echo '<a href="' . $processor['author_url'] .'" target="_blank">';
						echo $processor['author'];
						echo '</a>';
					}else{
						echo $processor['author'];
					}
					echo '</span></small>';

				}
				echo '<p class="description">' . $processor['description'] . '</p>';
				if(!empty($processor['links'])){
					echo '<p>';
					foreach($processor['links'] as $link){
						if(!empty($link['url']) && !empty($link['label'])){
							//echo '<div style="float:right; margin-top:5px;">';
							echo '<a style="" href="'.$link['label'].'" target="_blank" class="' . (!empty($link['class']) ? $link['class'] : null ) . '">' . $link['label'] . '</a>';
							//echo '<div>';
						}
					}
					echo '</p>';
				}

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
			if(isset($config['description'])){
				echo "<p class=\"description\">" . $config['description'] ."</p><br>\r\n";
			}
			include $config['template'];
		echo "\r\n</script>\r\n";		
	}

}
?>
<script type="text/javascript">

<?php echo implode("\r\n", $form_processors_defaults); ?>

function hide_single_processors(){
	jQuery('.is_single_processor').each(function(k,v){
		var  line = jQuery(v);

		if(jQuery('.processor_type_' + line.data('type')).length){
			line.css('opacity', 0.5).find('.add-new-processor').removeClass('.add-new-processor').prop('disabled', true);
		}

	});
}

function new_form_processor(obj){

	return {};
}


</script>











