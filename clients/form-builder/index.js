import { render, useState, useEffect, useContext, useMemo, Fragment } from "@wordpress/element";
import domReady from "@wordpress/dom-ready";
import apiFetch from "@wordpress/api-fetch";
import { Button } from "@wordpress/components";
/**
 * Import CSS
 */
import '../admin/styles/form-builder.scss';
/**
 * Import most of client from form-builder repo
 *
 * https://git.saturdaydrive.io/_/caldera-forms/tools/form-builder
 * https://www.npmjs.com/package/@calderajs/form-builder
 */
import {
	FormBuilder,
	FormFieldsContext,
	ConditionalsContext,
	ProcessorsContext,
	RenderViaPortal,
	FieldConditonalSelectorWithState,
	prepareConditionalsForSave,
	prepareProcessorsForSave,
} from "@calderajs/form-builder";

/**
 * Controls for the field conditional groups
 *
 * @since 1.9.0
 */
const FieldConditionalSelectors = () => {
	const { formFields } = useContext(FormFieldsContext);
	const { conditionals } = useContext(ConditionalsContext);
	//Tries to get the node created by ui/edit.php to render portal on
	const nodeFactory = (fieldId) =>
		document.getElementById(`field-condition-type-${fieldId}`);
	return useMemo(
		() => (
			<Fragment>
				{formFields && formFields.length ? (
					formFields.map((field) => {
						const node = nodeFactory(field.ID);
						//No dom node? Return early.
						if (!node) {
							return <Fragment key={field.ID} />;
						}
						return (
							<Fragment key={field.ID}>
								<RenderViaPortal domNode={node}>
									<FieldConditonalSelectorWithState fieldId={field.ID} />
								</RenderViaPortal>
							</Fragment>
						);
					})
				) : (
					<Fragment />
				)}
			</Fragment>
		),
		[formFields, conditionals]
	);
};
/**
 * Deals with saving forms
 *
 * Including the save button and API interactions
 *
 * @since 1.9.0
 */
const HandleSave = ({ jQuery, formId }) => {

	//Reset formId if undefined
	if(typeof formId === "undefined"){
		formId = CF_ADMIN.form.ID;
	}

	//Get conditionals
	const { conditionals, hasConditionals } = useContext(
		ConditionalsContext
	);
	//Get processors
	const { processors } = useContext(ProcessorsContext);

	//Track if we're saving or not
	const [isSaving, setIsSaving] = useState(false);

	//Save handler
	const onSave = () => {

		setIsSaving(true);

		if(!check_required_bindings()){
			setIsSaving(false );
			return false;
		}

		if (typeof window.tinyMCE !== "undefined") {
			window.tinyMCE.triggerSave();
		}

		//Get data from outside of app
		let data_fields = jQuery(".caldera-forms-options-form").formJSON();

		//Legacy hook
		jQuery(document).trigger("cf.presave", {
			config: data_fields.config,
		});

		if (hasConditionals) {
			data_fields.config.conditional_groups = {
				conditions: (data_fields.conditions = prepareConditionalsForSave(
					conditionals
				)),
			};
		} else {
			data_fields.config.conditional_groups = {};
		}

		//processors includes removed processors, so check legacy state
		if (data_fields.config.processors) {
			let _processors = {};
			//Loop through processors in React state, prepare to save those known by legacy state
			Object.keys(processors).forEach( processorId => {
				if( data_fields.config.processors.hasOwnProperty(processorId ) ){
					_processors[processorId] = processors[processorId];
				}
			});
			data_fields.config.processors = prepareProcessorsForSave(_processors,data_fields.config.processors );
		} else {
			data_fields.config.processors = {};
		}


		if( conditionals.length > 0 ){

			//Clear all assignments of fields to conditionals
			if (data_fields.config.hasOwnProperty("fields")) {
				Object.keys(data_fields.config.fields).forEach((fieldId) => {
					if (data_fields.config.fields.hasOwnProperty(fieldId)) {
						data_fields.config.fields[fieldId].conditions = {
							type: "",
						};
					}
				});
			}

			//Reset assignments of fields to conditionals
			conditionals.forEach((c) => {
				const appliesTo = c.hasOwnProperty("config") ? c.config.appliesTo : [];
				if (appliesTo) {
					appliesTo.forEach((fieldId) => {
						if (data_fields.config.fields.hasOwnProperty(fieldId)) {
							data_fields.config.fields[fieldId].conditions = {
								type: c.id,
							};
						}
					});
				}
			});	
		}
		

		apiFetch({
			path: `/cf-api/v2/forms/${formId}`,
			data: {
				...data_fields,
			},
			method: "PUT",
		})
			.then(({ form_id, form }) => {
				const $notice = jQuery(".updated_notice_box");
				$notice.stop().animate({ top: 0 }, 200, function () {
					setTimeout(function () {
						$notice.stop().animate({ top: -75 }, 700);
					}, 1700);
				});
			})
			.catch((e) => console.log(e))
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

/**
 * Keeps state in sync between legacy field editor and React state.
 *
 * @since 1.9.0
 */
const SubscribeToFieldChanges = ({ jQuery }) => {
	const {
		getFieldById,
		updateFieldSetting,
		addField,
		removeField,
		updateFieldType,
	} = useContext(FormFieldsContext);

	//Watch DOM for events outside of React for field configs
	//Update React state as needed
	useEffect( () => {
		let isSubscribed = true;
		jQuery(document).on("field.config-change", (e, update) => {
			let { name, value } = update;
			if (isSubscribed) {
				updateFieldSetting(name, value);
			}
		});
		//Watch for field removed
		jQuery(document).on("field.removed", (e, data) => {
			if (isSubscribed) {
				removeField(data.fieldId);
			}
		});
		//Watch for field added
		jQuery(document).on("field.added", (e, data) => {
			if (isSubscribed) {
				const field = {
					ID: data.field.id,
					label: data.field.label,
					slug: data.field.slug,
					value: "",
					type: "",
					conditions: {
						type: "",
					},
					config: {},
				};
				addField(field);
			}
		});
		//Watch changes to field type
		jQuery(".caldera-editor-body").on(
			"change",
			".caldera-select-field-type",
			function () {
				if (isSubscribed) {
					const $this = jQuery(this);
					let fieldId = $this.attr("data-field");
					updateFieldType(fieldId, $this.val());
				}
			}
		);
		//Prevent binding when unmounted
		return () => {
			isSubscribed = false;
		};
	}, [jQuery, getFieldById]);

	return <Fragment />;
};

/**
 * Keeps state in sync between legacy processor editor and React state.
 *
 * @since 1.9.0
 */
const SubscribeToProcessorChanges = ({ jQuery }) => {
	//Access processor state
	const {
		updateProcessor,
		getProcessor,
		setActiveProcessorId,
		addProcessor
	} = useContext(ProcessorsContext);

	useEffect(() => {
		let isSubscribed = true;
		//Activate new processor on creation
		jQuery(document).on("processor.added", (event, data) => {
			if (isSubscribed) {
				setActiveProcessorId(data.processor.id);
				jQuery(".caldera-processor-nav a").on("click", function () {
					setActiveProcessorId(jQuery(this).parent().data("pid"));
				});
			}
		});

		jQuery(document).on('processor.added', (event, data) => {
			addProcessor(data.processor.type, data.processor.id);
		});

		//Activate processor when clicked on
		jQuery(".caldera-processor-nav a").on("click", function () {
			setActiveProcessorId(jQuery(this).parent().data("pid"));
		});

		//Enable processor
		jQuery(document).on("processor.enabled", (e, data) => {
			updateProcessor({
				...getProcessor(data.processorId),
				runtimes: { insert: 1 },
			});
		});

		//Disable processor
		jQuery(document).on("processor.disabled", (e, data) => {
			updateProcessor({
				...getProcessor(data.processorId),
				runtimes: { insert: 0 },
			});
		});

		//Prevents calling when unmounted
		return () => {
			isSubscribed = false;
		};
	}, [jQuery]);

	//Force re-render when active processor changes
	return <Fragment />;
};

/**
 * Wrapper for FormBuilder component for use in Caldera Forms 1.x
 *
 * @since 1.9.0
 */
const CalderaFormsBuilder = ({ savedForm, jQuery, conditionalsNode }) => {
	return (
		<FormBuilder
			strings={CF_FORM_BUILDER.strings}
			savedForm={savedForm}
			jQuery={jQuery}
			conditionalsNode={conditionalsNode}
		>
			<SubscribeToFieldChanges jQuery={jQuery} />
			<SubscribeToProcessorChanges jQuery={jQuery} />
			<FieldConditionalSelectors />
			<RenderViaPortal
				domNode={document.getElementById("caldera-header-save-button")}
			>
				<HandleSave jQuery={jQuery} />
			</RenderViaPortal>
		</FormBuilder>
	);
};

/**
 * Initialize form builder
 *
 * @since 1.9.0
 */
domReady(function () {
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
