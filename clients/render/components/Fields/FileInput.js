import {CalderaFormsFieldGroup, Fragment} from "../CalderaFormsFieldGroup";
import {CalderaFormsFieldPropType} from "../CalderaFormsFieldRender";
import PropTypes from 'prop-types';
import Dropzone from 'react-dropzone';
const CryptoJS = require("crypto-js");

export const FileInput = (props) => {
	const{shouldDisable,accept,field,describedById,onChange,style,className,multiple,multiUploadText,inputProps} = props;
	const {
		outterIdAttr,
		fieldId,
		fieldLabel,
		fieldCaption,
		required,
		fieldPlaceHolder,
		fieldDefault,
		fieldValue,
		fieldIdAttr,
		isInvalid
	} = field;

	const onDrop = (acceptedFiles, rejectedFiles) => {
		acceptedFiles.forEach(file => {
			onChange(file);

		});
	};

	return(

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
        <button type="button" className="btn btn-block">
					{multiUploadText}
        </button>
			</Dropzone>

	)
};

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
    	display: 'inline-block',
		height: '100%',
		width: '100%',
		backgroundColor: 'transparent',
		color: '#fff',
		border: 'none',
		borderRadius: '0px',
		padding: '0px',
		margin: '0px'
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