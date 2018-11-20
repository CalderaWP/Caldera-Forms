import {FileInput} from "../../../../../render/components/Fields/FileInput";
import React from 'react';
export class MockFileFieldRenderer extends React.Component {


	constructor(props) {
		super(props);
		this.state = {
			value: props.field.fieldValue,
			message: '',
			error: false,
			isInvalid: false,
			shouldDisable: false,
			field: props.field,
			strings: props.strings
		};
		this.onChange = this.onChange.bind(this);
    this.setField = this.setField.bind(this);
		this.setIsInvalid = this.setIsInvalid.bind(this);
		this.setShouldDisable = this.setShouldDisable.bind(this);
		this.setMessage = this.setMessage.bind(this);
	}

	onChange(value) {
		this.setState({value});
		let { field } = this.state;
		field.fieldValue = value;
    this.setState({field});
	}

  setField(field) {
    this.setState({field});
  }

	setIsInvalid(isInvalid) {
		this.setState({isInvalid})
	}

	setShouldDisable(isDisabled) {
		this.setState({isDisabled})
	}

	setMessage(messageText, isError) {
		this.setState({

			error: isError,
			message:messageText,

		});
	}

	render() {
		const {value, message, error, isInvalid, shouldDisable, field, strings} = this.state;
		const messageProp = {error,message};

		return (
			<div>
				<FileInput
					field={field}
					isRequired={false}
					multiUploadText={field.configOptions.multiUploadText}
					onChange={this.onChange}
					inputProps={{}}
					shouldDisable={shouldDisable}
					isInvalid={isInvalid}
					message={messageProp}
					usePreviews={this.props.usePreviews}
					strings={strings}
				/>
			</div>
		)
	}
}