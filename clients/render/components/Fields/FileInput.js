import {CalderaFormsFieldGroup, Fragment} from "../CalderaFormsFieldGroup";
import {CalderaFormsFieldPropType} from "../CalderaFormsFieldRender";
import React from 'react';

import PropTypes from 'prop-types';
import Dropzone from 'react-dropzone';

/**
 * A FileInput
 *
 * @since 1.8.0
 *
 * @param props
 * @return {*}
 * @constructor
 */
export const FileInput = (props) => {

	const {onChange, accept, field, describedById, style, className, multiUploadText, multiple, inputProps, usePreviews, previewHeight, previewWidth, strings} = props;
	let {shouldDisable} = props;
	const {
		outterIdAttr,
		fieldId,
		fieldLabel,
		fieldCaption,
		required,
		isRequired,
		fieldPlaceHolder,
		fieldDefault,
		fieldIdAttr,
		isInvalid,
		fieldValue
	} = field;

	const valueSet = fieldValue.length > 0;
	const removeFileID = fieldIdAttr + '_file_';
	const buttonControls = fieldIdAttr + ', cf2-list-files';

	inputProps.id = fieldIdAttr;

	if (valueSet && !multiple) {
		shouldDisable = true;
		inputProps.disabled = true;
	}

	return (

		<div className="cf2-dropzone" data-field={fieldId}>
			<Dropzone
				onDrop={onChange}
				className={className}
				accept={'string' === typeof  accept ? accept : ''}
				style={style}
				disabled={shouldDisable}
				inputProps={inputProps}
				disableClick={shouldDisable}
				multiple={multiple}
			>
				<button
					type="button"
					className="btn btn-block"
					aria-controls={buttonControls}
					aria-expanded={valueSet}
					disabled={shouldDisable}
				>
					{multiUploadText}
				</button>
			</Dropzone>

			{valueSet &&
			<ul
				id="cf2-list-files"
				role="list"
			>
				{
					fieldValue.map(
						(file, index) =>
							<li
								id={removeFileID + index}
								key={index}
								className="cf2-file-listed"
								role="listitem"
								aria-posinset={index}
							>

								<button
									type="button"
									aria-controls={removeFileID + index}
									data-file={removeFileID + index}
									className="cf2-file-remove"
									onClick={(e) => onChange(e, file)}
								>
									<span className="screen-reader-text sr-text">{strings.removeFile}</span>
								</button>

								<div>
									{usePreviews === true && file.type.startsWith("image") === true ?
										<img
											className="cf2-file-field-img-preview"
											width={previewWidth}
											height={previewHeight}
											src={file.preview}
											alt={file.name}
										/>
										:
										<span className="cf2-file-name">{file.name}</span>
									}
									<br/>
									<span
										className="cf2-file-data"> {file.type} - {file.size} bytes - {file.type.startsWith("image")}</span>
								</div>
							</li>
					)
				}
			</ul>
			}
		</div>

	)

};

/**
 * Prop definitions for FileInput component
 *
 * @since 1.8.0
 *
 * @type {{field: *, onChange: (e|*), shouldDisable: *, isInvalid: *, describedById: *, multiple: *, text: *, multiUploadText: *, message: *, style: *, previewStyle: *, inputProps: *, className: *, accept: *}}
 */
FileInput.propTypes = {
	field: PropTypes.shape(CalderaFormsFieldPropType),
	onChange: PropTypes.func,
	shouldDisable: PropTypes.bool,
	isInvalid: PropTypes.bool,
	describedById: PropTypes.string,
	multiple: PropTypes.oneOfType([
		PropTypes.bool,
		PropTypes.number
	]),
	text: PropTypes.object,
	multiUploadText: PropTypes.oneOfType([
		PropTypes.bool,
		PropTypes.string
	]),
	message: PropTypes.shape({
		error: PropTypes.bool,
		message: PropTypes.string
	}),
	style: PropTypes.object,
	usePreviews: PropTypes.oneOfType([
		PropTypes.bool,
		PropTypes.string
	]),
	previewWidth: PropTypes.number,
	previewHeight: PropTypes.number,
	inputProps: PropTypes.object,
	className: PropTypes.string,
	accept: PropTypes.oneOfType([
		PropTypes.bool,
		PropTypes.string
	]),
	isRequired: PropTypes.oneOfType([
		PropTypes.bool,
		PropTypes.object
	]),
};

/**
 * Default props for a FileInput component
 *
 * @since 1.8.0
 *
 * @type {{message: {error: boolean, message: string}, text: {buttonText: string, removeFile: string}, multiUploadText: string, inputProps: {type: string}, disableClick: boolean, multiple: boolean, className: string, previewStyle: {height: string, width: string}}}
 */
FileInput.defaultProps = {
	message: {
		error: false,
		message: ''
	},
	inputProps: {
		type: 'file',
		disabled: 'false'
	},
	disableClick: false,
	className: 'cf2-file form-control'
};

/**
 * Prepare a field's config to be used as props for FileInput component
 *
 * @since 1.8.0
 *
 * @param {*} fieldConfig The field's configuration
 *
 * @return {{field: *}}
 */
FileInput.fieldConfigToProps = (fieldConfig) => {
	let props = {
		field: fieldConfig
	};
	const configOptionProps = [
		'multiple',
		'multiUploadText'
	];

	if (!props.field.hasOwnProperty('isRequired')) {
		props.field.isRequired = false;
	}
	if (fieldConfig.hasOwnProperty('configOptions')) {

		const {configOptions} = fieldConfig;

		configOptionProps.forEach(key => {
			props[key] = configOptions[key];
		});

		if (configOptions.hasOwnProperty('allowedTypes')) {

			props.inputProps = {
				type: 'file',
				accept: configOptions.allowedTypes
			};
			props.accept = configOptions.allowedTypes;

		} else {
			props.accept = '';
		}

		if (fieldConfig.configOptions.hasOwnProperty('multiple')) {
			if (fieldConfig.configOptions.multiple === 1) {
				props.multiple = true;
			} else {
				props.multiple = false;
			}
		} else {
			props.multiple = false;
		}

		if (configOptions.hasOwnProperty('usePreviews')) {

			if (configOptions.usePreviews === true) {
				props.usePreviews = true;
				props.previewWidth = configOptions.previewWidth;
				props.previewHeight = configOptions.previewHeight;
			} else {
				props.usePreviews = false;
			}

		}


	}

	return props;
};
