import { FormBuilder, conditionalsFromCfConfig } from "@calderajs/form-builder";
import React from "react";
import { render } from "@wordpress/element";
import domReady from "@wordpress/dom-ready";

domReady(function() {
	let form = CF_ADMIN.form;
	if (!form.hasOwnProperty("fields")) {
		form.fields = {};
	}

	let initialConditionals = [];
	const conditionalsNode = document.getElementById(
		"caldera-forms-conditions-panel"
	);
	if (
		form.hasOwnProperty("conditional_groups") &&
		form.conditional_groups.hasOwnProperty("conditions")
	) {
		initialConditionals = conditionalsFromCfConfig(
			form.conditional_groups.conditions,
			form.fields
		);
	}

	render(
		<FormBuilder
			initialConditionals={initialConditionals}
			form={form}
			conditionalsNode={conditionalsNode}
			jQuery={window.jQuery}
		/>,
		document.getElementById("caldera-forms-form-builder")
	);
});
