import {FileInput} from "../../../../../render/components/Fields/FileInput";
import React from 'react';
export class MockFileFieldRenderer extends React.Component {


	constructor(props) {
		super(props);
		this.state = {
			value: [],
			message: '',
			error: false,
			isInvalid: false,
			shouldDisable: false,
		};
		this.onChange = this.onChange.bind(this);
		this.setIsInvalid = this.setIsInvalid.bind(this);
		this.setShouldDisable = this.setShouldDisable.bind(this);
		this.setMessage = this.setMessage.bind(this);
	}

	onChange(value) {
		this.setState({value});

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
		const {value, message, error, isInvalid, shouldDisable} = this.state;
		const messageProp = {error,message};
		let {field} = this.props;
		field.isRequired = true;
		return (
			<div>
				<FileInput
					field={field}
					isRequired={false}
					multiple={false}
					multiUploadText={'Hi Roy'}
					onChange={this.onChange}
					inputProps={{}}
					shouldDisable={shouldDisable}
					isInvalid={isInvalid}
					message={messageProp}
					value={value}
				/>
			</div>
		)
	}
}