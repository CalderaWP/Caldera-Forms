import {CalderaFormsFieldGroup, Fragment} from "../CalderaFormsFieldGroup";
import {CalderaFormsFieldPropType} from "../CalderaFormsFieldRender";
import PropTypes from 'prop-types';
import Dropzone from 'react-dropzone';


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
			const reader = new FileReader();
			reader.onload = () => {
				onChange(reader.result);
			};
			reader.onabort = () => console.log('file reading was aborted');
			reader.onerror = () => console.log('file reading has failed');
			reader.readAsBinaryString(file);
		});
	};

	const createImageFromFieldValue = (fieldValue) =>{
		const img = new Image();
		img.src = fieldValue;
		return img;
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
			<p>{multiUploadText}</p>
			{fieldValue &&
				<ul>
					<li>
						<img src={fieldValue} />
					</li>
				</ul>
			}
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
	style: {
		margin: "0 auto",
		position: "relative",
		width: "200px",
		height: "200px",
		borderWidth: "2px",
		borderColor: "rgb(102, 102, 102)",
		borderStyle: "dashed",
		borderRadius: "5px"
	},
	inputProps: {
		type: 'file'
	},
	disableClick: false,
	multiple: true,
	className: 'cf2-file form-control'
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