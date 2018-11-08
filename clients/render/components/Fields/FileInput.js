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

	const { shouldDisable, onChange, accept, field, describedById, style, className, multiple, multiUploadText, inputProps, text, usePreviews, previewHeight, previewWidth } = props;

	const {
		outterIdAttr,
		fieldId,
		fieldLabel,
		fieldCaption,
		required,
		fieldPlaceHolder,
		fieldDefault,
		fieldIdAttr,
		isInvalid,
		fieldValue
	} = field;

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
      {multiple === false &&
      fieldValue.length > 0 ? (
				<div>{text.multipleDisabled}</div>
        ) : (
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
						aria-expanded={ulExpanded}
					>
						{text.buttonText}
					</button>
				</Dropzone>
				)}

      {fieldValue.length > 0  &&
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
										<span className="screen-reader-text sr-text">{text.removeFile}</span>
									</button>

									<div>
										{file.type.startsWith("image") === true &&
                    	usePreviews === true ?
											<img
												className="cf2-file-field-img-preview"
												width={previewWidth}
												height={previewHeight}
												src={file.preview}
												alt={file.name}
											/>
											:
											<span>{file.name}</span>
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
		PropTypes.number
	]),
	text: PropTypes.object,
	multiUploadText: PropTypes.string,
	message: PropTypes.shape({
		error: PropTypes.bool,
		message: PropTypes.string
	}),
	style: PropTypes.object,
  usePreviews:  PropTypes.bool,
	previewWidth: PropTypes.number,
  previewHeight: PropTypes.number,
	inputProps: PropTypes.object,
	className: PropTypes.string,
	accept: 	PropTypes.string
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
		removeFile: 'Remove file',
		multipleDisabled: 'Only one file upload allowed'
	},
	multiUploadText: 'Try dropping some files here, or click to select files to upload.',
	inputProps: {
		type: 'file'
	},
	disableClick: false,
	className: 'cf2-file form-control',
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
		'text'
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

    if (fieldConfig.configOptions.hasOwnProperty('multiple')) {
      if(fieldConfig.configOptions.multiple === 1 ) {
        props.multiple = true;
      }
    } else {
      props.multiple = false;
    }

    if (configOptions.hasOwnProperty('usePreviews')) {

      if ( configOptions.usePreviews === 1 ) {

				props.usePreviews=  true;
				props.previewWidth = configOptions.previewWidth;
				props.previewHeight = configOptions.previewHeight;

      } else {

				props.usePreviews = configOptions.usePreviews;

      }
    }

	}

	return props;
};
