<?php
if( ! defined( 'ABSPATH' ) ){
	exit;
}
?>
<div class="caldera-editor-header">
	<ul class="caldera-editor-header-nav">
		<li class="caldera-editor-logo">
			<span class="caldera-forms-name"><?php echo esc_html(  $form[ 'name' ] ); ?><span class="caldera-forms-name">
		</li>
		<?php if(!empty($form['description'])){ ?>
			<li class="caldera-element-type-label">
				<?php echo esc_html(  $form[ 'description' ] ); ?>
			</li>
		<?php } ?>
		<?php if( current_user_can( Caldera_Forms::get_manage_cap( 'admin' ) ) && empty( $form['_external_form'] ) ){ ?>
			<li class="caldera-forms-toolbar-item">
				<a class="button" href="admin.php?page=caldera-forms&edit=<?php echo $form['ID']; ?>">
					<?php esc_html_e( 'Edit' ); ?>
				</a>
			</li>
		<?php } ?>
	</ul>
</div>
<div class="form-extend-page-wrap">
<?php
if( isset( $_GET[ 'cf-alt-viewer' ] ) ){
	Caldera_Forms_Render_Assets::enqueue_script( 'entry-viewer-2' );
	Caldera_Forms_Render_Assets::enqueue_style( 'table' );

	$form = Caldera_Forms_Forms::get_form( $_GET[ 'cf-alt-viewer' ] );
	$vue = new Caldera_Forms_Entry_Vue( $form );
	$view =  $vue->display();

}else{
	?>
	<span class="form_entry_row highlight">
	<?php echo Caldera_Forms_Entry_Viewer::entry_trigger( $form[ 'ID' ] ); ?>
</span>
	<?php
	$is_pinned = true;
	include CFCORE_PATH . 'ui/entries/toolbar.php';
	?>

	<div id="form-entries-viewer"></div>
	<?php include CFCORE_PATH . 'ui/entries/pagination.php'; ?>
</div>

<?php
Caldera_Forms_Entry_Viewer::print_scripts();
?>

<?php
}
?>
</div>





