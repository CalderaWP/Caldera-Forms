import {CalderaFormsFieldGroup, Fragment} from "../CalderaFormsFieldGroup";
import {CalderaFormsFieldPropType} from "../CalderaFormsFieldRender";
import React from 'react';

import PropTypes from 'prop-types';
import Dropzone from 'react-dropzone';

export const FileInput = ( props )  => {

    const { shouldDisable,accept,field,describedById,onChange,style,className,multiple,multiUploadText,inputProps} = props;
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

  if( Array.isArray(fieldValue) === false ) {
    fieldValue = [];
  }

  const listItemStyles = {
  	listStyleType: 'none',
		margin: '10px 0'
	}

  const onDrop = (accepted) => {

		accepted.forEach( file => {
			fieldValue.push(file);
		})

    onChange( fieldValue );

  };

  const removeFile = (e, file) => {

		const index = fieldValue.indexOf(file);
		fieldValue.splice(index, 1);

		onChange( fieldValue );

	}

		return(

			<div className="cf2-dropzone">
				<Dropzone
					id={fieldIdAttr}
					onDrop={onDrop}
					style={style}
					className={className}
					accept={accept}
					disabled={shouldDisable}
					inputProps={inputProps}
					disableClick={shouldDisable}
					multiple={multiple}
				>
					<button type="button" className="btn btn-block" >
						{multiUploadText}
					</button>

				</Dropzone>

				<ul>
          {
            fieldValue.map(
              ( file, index ) =>
                <li key={index} className="cf2-file-listed" style={listItemStyles}>
                  <span className="cf2-remove-file" onClick={(e) => removeFile(e,file)} >X  </span>
                  {file.type.startsWith("image") === true
										? <img width="120" height="120" src={file.preview} alt={file.name} />
										: <span>{file.name}</span>
                  }
                  <br/>
                  {file.type} - {file.size} bytes
                </li>
            )
          }
				</ul>

			</div>

		)

}

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
	multiUploadText: PropTypes.string,
	message: PropTypes.shape({
		error: PropTypes.boolean,
		message: PropTypes.string
	}),
	style: PropTypes.object,
	inputProps: PropTypes.object,
	className: PropTypes.string,
	accept: PropTypes.oneOfType([
		PropTypes.array,
		PropTypes.string
	]),
};

FileInput.defaultProps = {
	message: {
		error: false,
		message: ''
	},
	multiUploadText: 'Try dropping some files here, or click to select files to upload.',
	inputProps: {
		type: 'file'
	},
	disableClick: false,
	multiple: true,
	className: 'cf2-file form-control',
	style: {
		border: 'none',
		padding: '0px'
	}
};

FileInput.fieldConfigToProps = (fieldConfig ) => {
	let props = {
		field: fieldConfig
	};
	const configOptionProps = [
		'multiple',
		'multiUploadText'
	];
	if (fieldConfig.hasOwnProperty('configOptions')) {
		configOptionProps.forEach(key => {
			props[key] = fieldConfig.configOptions[key];

		});
		if (fieldConfig.configOptions.hasOwnProperty('allowedTypes')) {

			props.inputProps = {
				type: 'file',
				accept: fieldConfig.configOptions.allowedTypes
			};
			props.accept = fieldConfig.configOptions.allowedTypes;

		}else{
			props.accept = '';
		}
		delete(fieldConfig.configOptions);
	}
	props.accept = '';
	configOptionProps.forEach(key => {
		if( ! props.hasOwnProperty(key)){
			props[key] = "false";
		}
	});
	return props;
};
