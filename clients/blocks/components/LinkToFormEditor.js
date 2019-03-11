import { __ } from '@wordpress/i18n';

/**
 * Basic component to link the form selected in the Gutenberg Block to its edition page
 *
 * @param props
 * @return {XML}
 * @constructor
 */
export const LinkToFormEditor = (props) => {

const href = "/wp-admin/admin.php?edit=" + props.formId + "&page=caldera-forms";
	return (
		<div>
			<a
				href={href}
				title={ __("Edit Caldera Form")}
			>
				{ __('Edit Form') }
			</a>
		</div>
	);
};