<?php
global $form_processors;
//dump($element,0);
// Get Processors
$form_processors = $processors = Caldera_Forms_Processor_Load::get_instance()->get_processors();

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
	<li class="<?php echo esc_attr('caldera-processor-nav ' . $id); ?> <?php if(!empty($type)){ echo 'processor_type_'.$type; }; ?>" data-pid="<?php echo esc_attr($id); ?>">
		<a href="#<?php echo $id; ?>">
		<?php echo $type_name; ?>
		<span class="processor-line-number"></span>
		</a>
		<input type="hidden" name="config[processors][<?php echo $id; ?>][ID]" value="<?php echo $id; ?>">
	</li>
	<?php
}

function processor_wrapper_template($id = '{{id}}', $type = '{{type}}', $config_str = '{"default":"default value"}', $run_times = false, $conditions_str = '{"type" : ""}'){
	
	global $form_processors;
	if( false === $run_times || !is_array( $run_times ) ){
		$run_times = array(
			'insert' => 1
		);
	}
	
	$type_name = __('New Form Processor', 'caldera-forms');
	if(!empty($type) && $type != '{{type}}'){
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

	$condition_type = '';
	if(!empty($conditions_str)){
		$conditions = json_decode($conditions_str, true);
		if(!empty($conditions['type'])){
			$condition_type = $conditions['type'];
		}
		if(!empty($conditions['group'])){
			$groups = array();
			foreach ($conditions['group'] as $groupid => $group) {
				$group_tmp = array(
					'id' => $groupid,
					'type'	=> 'processors',
					'lines' => array()
				);
				if(!empty($group)){
					foreach($group as $line_id => $line){
						$group_line = $line;
						$group_line['id'] = $line_id;
						$group_tmp['lines'][] = $group_line;
					}
				}
				$groups[] = $group_tmp;
			}
			$conditions['group'] = $groups;
			$conditions_str = json_encode($conditions);
		}
	}

	$enabled = ! isset( $run_times['insert'] ) || $run_times['insert'];
	?>
	<div class="caldera-editor-processor-config-wrapper caldera-editor-config-wrapper processor-<?php echo $type; ?>" id="<?php echo $id; ?>" data-type="<?php echo $type; ?>" style="display:none;">

		<div class="toggle_option_tab" data-pid="<?php echo esc_attr($id); ?>">
			<a href="<?php echo  esc_attr('#' .$id . '_settings_pane' ); ?>" class="button button-primary"><?php echo esc_html__( 'Settings', 'caldera-forms' ); ?></a>
			<a href="<?php echo  esc_attr('#' .$id . '_conditions_pane' ); ?>" class="button set-conditions" data-pid="<?php echo  esc_attr($id); ?>">
                <?php echo esc_html__( 'Conditions', 'caldera-forms' ); ?>
            </a>
		</div>
		<h3 data-title="<?php esc_html_e( 'New Form Processor', 'caldera-forms'); ?>" class="caldera-editor-processor-title"><?php echo $type_name; ?></h3>
		<div id="<?php echo esc_attr( $id . "_settings_pane"); ?>" class="wrapper-instance-pane">
			<div class="toggle_processor_event">
				<label
                    title="<?php echo esc_attr( __('Enable / Disable Processor', 'caldera-forms') ); ?>"
                    class="button button-small <?php if( !empty( $run_times['insert'] )){ echo 'activated'; } ?>"
                    data-pid="<?php echo esc_attr($id); ?>"
                >
                    <input
                        type="checkbox"
                        style="display:none;"
                        value="1"
                        name="config[processors][<?php echo esc_attr($id); ?>][runtimes][insert]"
                        <?php if( $enabled ){ echo 'checked="checked"'; } ?>
                    />
                    <span
                        class="is_active"
                        style="<?php if( ! $enabled ){ ?> display:none;<?php } ?>"
                    >
                        <?php esc_html_e( 'Disable Processor', 'caldera-forms' ); ?>
                    </span>
                    <span
                        class="not_active"
                        style="<?php if(  $enabled ){ ?> display:none;<?php } ?>"
                    >
                        <?php esc_html_e( 'Enable Processor', 'caldera-forms' ); ?>
                    </span>
				</label>
            </div>
			<div class="caldera-config-processor-notice" style="<?php if( $enabled ){ ?> display:none;<?php } ?>clear: both; padding: 20px 0px 0px;">
				<p style="padding:12px; text-align:center;background:#e7e7e7;" class="description">
                    <?php esc_html_e('Processor is currently disabled', 'caldera-forms'); ?>
                </p>
			</div>
			<div class="caldera-config-group" style="display:none;">
				<label for="<?php echo esc_attr($id . "_type" ); ?>">
                    <?php esc_html_e( 'Processor Type', 'caldera-forms'); ?>
                </label>
				<div class="caldera-config-field">
					<select class="block-input caldera-select-processor-type" id="<?php echo esc_attr($id . "_type" ); ?>" name="config[processors][<?php echo esc_attr($id); ?>][type]" data-type="<?php echo esc_attr($type); ?>">
						<?php
						    echo build_processor_types($type);
						?>
					</select>
				</div>
			</div>
			<div class="caldera-config-processor-setup" <?php if( ! $enabled ){ ?> style="display:none;"<?php } ?>>
			</div>
			<input type="hidden" class="processor_config_string block-input" value="<?php echo htmlentities( $config_str ); ?>" />
			<br>
			<br>
			<button class="button block-button delete-processor" data-confirm="<?php esc_html_e( 'Are you sure you want to remove this processor?', 'caldera-forms'); ?>" type="button">
                <?php esc_html_e( 'Remove Processor', 'caldera-forms'); ?>
            </button>
		</div>
		<div id="<?php echo esc_attr( $id . '_conditions_pane' );?>" style="display:none;" class="wrapper-instance-pane">
		</div>
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

<div class="caldera-editor-processors-panel-wrap">
	<div class="caldera-editor-processors-panel">
		<button type="button" class="new-processor-button button block-button ajax-trigger" 
		data-request="new_form_processor" 
		data-modal="form_processor"
		data-load-class="none"
		data-modal-title="<?php esc_html_e( 'Form Processors', 'caldera-forms'); ?>"
		data-modal-height="700"
		data-modal-width="600"
		data-template="#form-processors-tmpl"
		data-callback="hide_single_processors"
		><?php esc_html_e( 'Add Processor', 'caldera-forms'); ?></button>
		<ul class="active-processors-list">
			<?php
				// build processors list
				if(!empty($element['processors'])){
					foreach($element['processors'] as $processor_id=>$config){
						if(!empty($config['type'])){
							echo processor_line_template($processor_id, $config['type']);
						}
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
			if(!empty($config['type'])){

				$config_str = array();
				if(!empty($config['config'])){
					$config_str = json_encode( $config['config'] );
				}

				if ( ! empty( $config[ 'conditions' ] ) ) {
					$conditions = wp_json_encode( $config[ 'conditions' ] );
				} else {
					$conditions = '{}';
				}

				// runtime conditions where introduced in 1.3.2
				// as was the cf_version in form config. so its safe to say that id this value is set, its the same version or higher
				if( empty( $element['cf_version'] ) ){
					$run_times = false;
				}else{
					$run_times = array();
				}				
				if(!empty($config['runtimes'])){
					$run_times = $config['runtimes'];
				}
				processor_wrapper_template($processor_id, $config['type'], $config_str, $run_times, $conditions);
			}
		}
	}

	?>
	</div>
</div>
<div class="clear"></div>

<script type="text/html" id="form-processors-tmpl">
	<?php
		global $form_processors;

		foreach($form_processors as $processor_id=>$processor){
			$icon = CFCORE_URL . "assets/images/processor.png";
			if(!empty($processor['icon'])){
				$icon = $processor['icon'];
			}

			echo '<div class="form-modal-add-line'. ( !empty($processor['single']) ? ' is_single_processor' : null ) . '" data-type="' . $processor_id . '">';
				echo '<button type="button" class="button info-button add-new-processor" data-type="' . $processor_id . '">' . __('Use Processor', 'caldera-forms') . '</button>';
				echo '<img src="'. $icon .'" class="form-modal-lgo" width="45" height="45">';
				echo '<strong>' . $processor['name'] .'</strong> ';
				if(!empty($processor['author'])){
					echo '<small><span class="description">';
					echo '&nbsp' . esc_html__( 'by', 'caldera-forms' ) . '&nbsp';
					if(!empty($processor['author_url'])){
						echo '<a href="' . esc_url( $processor[ 'author_url' ] ) .'" target="_blank">';
						echo esc_html( $processor[ 'author' ] );
						echo '</a>';
					}else{
						echo esc_html( $processor[ 'author' ] );
					}
					echo '</span></small>';

				}
				echo '<p class="description">';
				if(!empty($processor['description'])){
					 echo $processor['description'];
				}else{
					echo '&nbsp;';
				}
				echo '</p>';
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

do_action('caldera_forms_processor_templates', $form_processors);

foreach($form_processors as $processor=>$config){
	echo "<script type=\"text/html\" id=\"" . $processor . "-tmpl\">\r\n";
	if(isset($config['description'])){
		echo "<p class=\"description\">" . $config['description'] ."</p><br>\r\n";
	}
	if(isset($config['conditionals'])){
		if(empty($config['conditionals'])){
			echo '<span class="no-conditions"></span>';
		}
	}	
	
	//Output config options from template, pre-rendered HTML or an notice of no options.
	if(isset($config['template'])){
		include $config['template'];
	}elseif( isset($config['html'])){
		echo esc_html( $config['html'] );
	}else{
		echo '<p>' . esc_html__('This processor has no configurable options.', 'caldera-forms') . '</p>';
	}
	echo "\r\n</script>\r\n";

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











