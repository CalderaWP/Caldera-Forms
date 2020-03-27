import {
	FormBuilder,
	ProcessorsContext,
	ProcessorsProvider,
	ConditionalsContext,
	ConditionalsProvider,
	prepareProcessorsForSave,
	prepareConditionalsForSave,
	RenderViaPortal
} from "@calderajs/form-builder";
import { Button } from "@wordpress/components";

import React from "react";
import { render } from "@wordpress/element";
import domReady from "@wordpress/dom-ready";
import apiFetch from "@wordpress/api-fetch";
const HandleSave = ({ jQuery, formId }) => {
	const { conditionals } = React.useContext(ConditionalsContext);
	const { processors } = React.useContext(ProcessorsContext);
	const [isSaving, setIsSaving] = React.useState(false);
	const onSave = () => {
		setIsSaving(true);
		if (typeof window.tinyMCE !== "undefined") {
			window.tinyMCE.triggerSave();
		}

		//Get data from outside of app
		let data_fields = jQuery(".caldera-forms-options-form").formJSON();

		//Legacy hook
		jQuery(document).trigger("cf.presave", {
			config: data_fields.config
		});
		data_fields.config.conditional_groups = {
			conditions: (data_fields.conditions = prepareConditionalsForSave(
				conditionals
			))
		};
		data_fields.config.processors = prepareProcessorsForSave(processors);
		apiFetch({
			path: `/cf-api/v2/forms`,
			data: {
				...data_fields,
				form: formId
			},
			method: "POST"
		})
			.then(r => {
				const $notice = jQuery(".updated_notice_box");
				$notice.stop().animate({ top: 0 }, 200, function() {
					setTimeout(function() {
						$notice.stop().animate({ top: -75 }, 700);
					}, 1700);
				});
			})
			.catch(e => console.log(e))
			.finally(() => {
				window.setTimeout(() => {
					setIsSaving(false);
				}, 2000);
			});
	};
	return (
		<Button
			isPrimary
			isBusy={isSaving}
			className="button button-primary caldera-header-save-button"
			type="button"
			onClick={onSave}
		>
			{!isSaving ? "Save Form" : "Saving"}
		</Button>
	);
};
const CalderaFormsBuilder = ({ savedForm, jQuery, conditionalsNode }) => {
	const savedProcessors = savedForm.hasOwnProperty("processors")
		? savedForm.processors
		: {};
	const saveNode = document.getElementById("caldera-header-save-button");
	return (
		<ProcessorsProvider savedProcessors={savedProcessors}>
			<ConditionalsProvider savedForm={savedForm}>
				<RenderViaPortal domNode={saveNode}>
					<HandleSave jQuery={jQuery} formId={savedForm.ID} />
				</RenderViaPortal>
				<FormBuilder
					jQuery={jQuery}
					conditionalsNode={conditionalsNode}
					form={savedForm}
				/>
			</ConditionalsProvider>
		</ProcessorsProvider>
	);
};
domReady(function() {
	let form = CF_ADMIN.form;
	if (!form.hasOwnProperty("fields")) {
		form.fields = {};
	}

	const conditionalsNode = document.getElementById(
		"caldera-forms-conditions-panel"
	);

	render(
		<CalderaFormsBuilder
			savedForm={form}
			conditionalsNode={conditionalsNode}
			jQuery={window.jQuery}
		/>,
		document.getElementById("caldera-forms-form-builder")
	);
});
