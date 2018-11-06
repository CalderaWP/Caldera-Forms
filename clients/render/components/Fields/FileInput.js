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


	const {shouldDisable, accept, field, describedById, onChange, style, className, multiple, multiUploadText, inputProps, text, previewStyle} = props;
	const {
		outterIdAttr,
		fieldId,
		fieldLabel,
		fieldCaption,
		required,
		fieldPlaceHolder,
		fieldDefault,
		fieldIdAttr,
		isInvalid
	} = field;

	let {
		fieldValue
	} = field;

	if (Array.isArray(fieldValue) === false) {
		fieldValue = [];
	}

	/**
	 * Handle a file being added
	 *
	 * @since 1.8.0
	 *
	 * @param accepted
	 */
	const onDrop = (accepted) => {
		accepted.forEach(file => {
			fieldValue.push(file);
		});

		onChange(fieldValue);

	};

	/**
	 * Handle a file being removed
	 *
	 * @since 1.8.0
	 *
	 * @param e
	 * @param file
	 */
	const removeFile = (e, file) => {

		const index = fieldValue.indexOf(file);
		fieldValue.splice(index, 1);

		onChange(fieldValue);

	};

	let ulExpanded = fieldValue.length > 0;
	if (fieldValue.length > 0) {
		ulExpanded = true;
	} else {
		ulExpanded = false;
	}


	const removeFileID = fieldIdAttr + '_file_';
	const buttonControls = fieldIdAttr + ', cf2-list-files';

	inputProps.id = fieldIdAttr;

	return (
		<div className="cf2-dropzone" data-field={fieldId}>
			<Dropzone
				onDrop={onDrop}
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
					aria-expanded={ulExpanded}
				>
					{text.buttonText}
				</button>
			</Dropzone>
			{fieldValue.length > 0 &&
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
									onClick={(e) => removeFile(e, file)}
								>
									<span className="screen-reader-text sr-text">{text.removeFile}</span>
								</button>

								<div>
									{file.type.startsWith("image") === true
										? <img
											className="cf2-file-field-img-preview"
											width={previewStyle.width}
											height={previewStyle.height}
											src={file.preview}
											alt={file.name}
										/>
										: <span>{file.name}</span>
									}
									<br/>
									<span className="cf2-file-data"> {file.type} - {file.size} bytes</span>
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
	onChange: PropTypes.func.isRequired,
	shouldDisable: PropTypes.bool,
	isInvalid: PropTypes.bool,
	describedById: PropTypes.string,
	multiple: PropTypes.oneOfType([
		PropTypes.bool,
		PropTypes.string
	]),
	text: PropTypes.object,
	multiUploadText: PropTypes.string,
	message: PropTypes.shape({
		error: PropTypes.boolean,
		message: PropTypes.string
	}),
	style: PropTypes.object,
	previewStyle: PropTypes.object,
	inputProps: PropTypes.object,
	className: PropTypes.string,
	accept: PropTypes.oneOfType([
		PropTypes.array,
		PropTypes.string,
		PropTypes.bool
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
	text: {
		buttonText: 'Try dropping some files here, or click to select files to upload.',
		removeFile: 'Remove file'
	},
	multiUploadText: 'Try dropping some files here, or click to select files to upload.',
	inputProps: {
		type: 'file'
	},
	disableClick: false,
	multiple: true,
	className: 'cf2-file form-control',
	previewStyle: {
		height: '24',
		width: '24'
	},
	accept: '',
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
		'multiUploadText',
		'text',
		'previewStyle'
	];
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

		props.previewStyle = {
			height: configOptions.previewHeight ? configOptions.previewHeight : 24,
			width: configOptions.previewWidth ? configOptions.previewWidth : 24
		};

	}

	return props;
};
