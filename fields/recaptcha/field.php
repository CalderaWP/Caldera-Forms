<div class="<?php echo $field_wrapper_class; ?>">
<?php if(false !== strpos($field_input_class, 'has-error')){
	echo '<span class="has-error">';
		echo $field_caption;
	echo '</span>';
}
?>
<input type="hidden" name="<?php echo $field_name; ?>" value="1" data-field="<?php echo $field_base_id; ?>">
<p id="<?php echo $field_id; ?>"></p>
<script type="text/javascript">
 Recaptcha.create("<?php echo $field['config']['public_key']; ?>",
    "<?php echo $field_id; ?>",
    {
      theme: "<?php echo $field['config']['theme']; ?>",
      callback: Recaptcha.focus_response_field
    }
  );
</script>
</div>