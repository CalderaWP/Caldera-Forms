<?php
if( Caldera_Forms_Admin::is_revision_edit() ){
	printf( '<div class="notice"><p>%s</p></div>', esc_html__( 'Currently Viewing A Revision', 'caldera-forms' ) );
}else{
	$revisions = Caldera_Forms_Forms::get_revisions( $element[ 'ID' ] );
	if( empty( $revisions ) ){
		esc_html_e( 'No Saved Revisions', 'caldera-forms' );
	}else{
		echo '<div id="caldera-forms-revisions-link">';
		$pattern = '<div><a class="button caldera-forms-revision-edit" href="%s" title="%s">%s</a></div>';
		foreach ( $revisions as $revision ){
			printf( $pattern,
				esc_url( Caldera_Forms_Admin::form_edit_link( $revision[ 'ID' ], $revision[ 'db_id' ] ) ),
				esc_attr( __( 'Click to edit revision ', 'caldera-forms' ) . $revision[ 'db_id' ] ),
				esc_html( __( 'Click to edit revision ', 'caldera-forms' ) . $revision[ 'db_id' ] )
			);
		}

		echo '</div>';
	}
}








