<?php
if( ! defined( 'ABSPATH' ) ){
	exit;
}
$entry_perpage = Caldera_Forms_Entry_Viewer::entries_per_page();
?>
<div class="tablenav caldera-table-nav" style="display:none;">

	<div class="tablenav-pages">
		<label class="screen-reader-text" id="cf-entries-list-items">
			<?php esc_html__( 'Posts Per Page', 'caldera-forms' ); ?>
		</label>
		<input title="<?php echo esc_attr( esc_html__( 'Entries Per Page', 'caldera-forms' ) ); ?>" id="cf-entries-list-items" type="number" value="<?php echo esc_attr( $entry_perpage ); ?>" class="screen-per-page" data-perpage="<?php echo esc_attr( $entry_perpage ); ?>" min="1" />
		<span class="pagination-links">
				<a href="#first" title="<?php esc_attr_e( 'Go to the first page', 'caldera-forms' ); ?>" data-page="first" class="first-page">«</a>
				<a href="#prev" title="<?php esc_attr_e( 'Go to the previous page', 'caldera-forms' ); ?>" data-page="prev" class="prev-page">‹</a>
				<span class="paging-input"><input type="text" size="1" name="paged" title="Current page" class="current-page"> <?php esc_html_e( 'of'); ?> <span class="total-pages"></span></span>
				<a href="#next" title="<?php esc_attr_e( 'Go to the next page', 'caldera-forms' ); ?>" data-page="next" class="next-page">›</a>
				<a href="#last" title="<?php esc_attr_e( 'Go to the last page', 'caldera-forms' ); ?>" data-page="last" class="last-page">»</a>
			</span>
	</div>
</div>
