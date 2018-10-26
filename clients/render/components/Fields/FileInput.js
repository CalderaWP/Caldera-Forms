import {CalderaFormsFieldGroup, Fragment} from "../CalderaFormsFieldGroup";
import {CalderaFormsFieldPropType} from "../CalderaFormsFieldRender";
import PropTypes from 'prop-types';
import Dropzone from 'react-dropzone';
import { setFormInState } from '../../../state/actions/mutations'
const CryptoJS = require("crypto-js");

export class FileInput extends React.Component {

  constructor(props) {
    super(props);
    this.state = {
    	files: []
		};
  }

	onDrop(files) {

		files.forEach(file => {
			//onChange(file);
      this.setState(prevState => ({
        files: [...prevState.files, file]
      }))
		})

	}

	removeFile(file) {

		let tmpFiles = [...this.state.files];
		const index = tmpFiles.indexOf(file);
		tmpFiles.splice(index, 1);

		this.setState({files: tmpFiles});

	}

	render() {
    const { files } = this.state;
    const { shouldDisable,accept,field,describedById,onChange,style,className,multiple,multiUploadText,inputProps} = this.props;
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

		return(
			<div className="dropzone">
				<Dropzone
					id={fieldIdAttr}
					onDrop={this.onDrop.bind(this)}
					style={style}
					className={className}
					accept={accept}
					disabled={shouldDisable}
					inputProps={inputProps}
					disableClick={shouldDisable}
					multiple={multiple}
					value={files}
				>
					<button type="button" className="btn btn-block" >
						{multiUploadText}
					</button>

				</Dropzone>
				<aside>
					<ul>
							{
								files.map(
								(file, index) =>
									<li key={index} className="file-listed">
										<img width="120" height="120" src={file.preview} alt={file.name} />
										<br/>
										{file.type} - {file.size} bytes - <button onClick={this.removeFile.bind(this)} >Remove</button>

									</li>
								)
							}
					</ul>
				</aside>
			</div>

		)

	}


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