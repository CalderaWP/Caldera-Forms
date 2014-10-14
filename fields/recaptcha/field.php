<?php echo $wrapper_before; ?>
<?php echo $field_label; ?>
<?php if(false !== strpos($field_input_class, 'has-error')){
	echo '<span class="has-error">';
		echo $field_caption;
	echo '</span>';
}
?>
<?php echo $field_before; ?>
<input type="hidden" name="<?php echo $field_name; ?>" value="1" data-field="<?php echo $field_base_id; ?>">
<p id="<?php echo $field_id; ?>"></p>
<?php echo $field_caption; ?>
<?php echo $field_after; ?>
<?php echo $wrapper_after; ?>
<script type="text/javascript">
 Recaptcha.create("<?php echo $field['config']['public_key']; ?>",
    "<?php echo $field_id; ?>",
    {
      theme: "<?php echo $field['config']['theme']; ?>",
      callback: Recaptcha.focus_response_field
    }
  );
</script>