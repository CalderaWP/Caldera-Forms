<?php
if ( ! isset( $field[ 'config' ] [ 'default' ] ) ) {
	$field[ 'config' ][ 'default' ] = 2;
}

if ( $field[ 'config' ][ 'number' ] < $field[ 'config' ][ 'default' ] ) {
	$field[ 'config' ][ 'default' ] = $field[ 'config' ][ 'number' ];
}

$default = intval( $field[ 'config' ][ 'default' ] );

$attrs = array(
	'type'        => 'text',
	'name'        => $field_name,
	'data-field'  => $field_base_id,
	'class'       => $field_class,
	'id'          => $field_id,
	'value'      => $default,
	'data-type' => 'star'
);

$attr_string =  caldera_forms_field_attributes( $attrs, $field, $form );
$star_target = Caldera_Forms_Field_Util::star_target( Caldera_Forms_Field_Util::get_base_id( $field, null, $form ) );

?><?php echo $wrapper_before; ?>
	<?php echo $field_label; ?>
	<?php echo $field_before; ?>
		<div style="position: relative;">
			<div id="<?php echo esc_attr( $star_target  ); ?>" style="color:<?php echo esc_attr( $field['config']['track_color'] ); ?>;font-size:<?php echo floatval( $field['config']['size'] ); ?>px;" ></div>
			<input <?php echo $attr_string . ' ' . $field_required; ?> style="position: absolute; width: 0px; height: 0px; padding: 0px; bottom: 0px; left: 12px; opacity: 0; z-index: -1000;" value="<?php esc_attr( $default ); ?>" />
		</div>
		<?php echo $field_caption; ?>
	<?php echo $field_after; ?>
<?php echo $wrapper_after;
	ob_start();
?>

	<script type="text/javascript">
		var <?php echo $star_target; ?>Score = 2;
		function <?php echo $field_id; ?>_stars(){
			jQuery( '#<?php echo $field_id; ?>_stars').raty({
				starOff	: 'raty-<?php echo $field['config']['type']; ?>-off',
				starOn : 'raty-<?php echo $field['config']['type']; ?>-on',
				target: '#<?php echo $field_id; ?>',
				spaceWidth: <?php echo $field['config']['space']; ?>,
				targetKeep: true, targetType: 'score',
				<?php if(!empty($field_value)){ echo "score: ".$field_value.","; }; ?>
				hints: [1,2,3,4,5],
				number: <?php echo $field['config']['number']; ?>,
				starType: 'f',
				score: <?php echo $default; ?>,
				starColor: '<?php echo $field['config']['color']; ?>',
				numberMax: 100,
				click :function(nScore){
					<?php echo $star_target; ?>Score = nScore;
					jQuery( '#<?php echo $field_id; ?>').trigger( 'change' );
				}
				<?php if(!empty($field['config']['cancel'])){ echo ",cancel: true"; }; ?>
				<?php if(!empty($field['config']['single'])){ echo ",single: true"; }; ?>
			});
		}

		window.addEventListener("load", function(){

			<?php echo $field_id; ?>_stars();
			jQuery( document ).on('cf.add', <?php echo $field_id; ?>_stars );
		});
	</script>
<?php
Caldera_Forms_Render_Util::add_inline_data( ob_get_clean(), $form );

