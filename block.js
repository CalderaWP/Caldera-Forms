
import {registerBlockType} from '@wordpress/blocks';


const block = registerBlockType( 'whatever', {
		edit({attributes, setAttributes, isSelected}) {
			const {file, title} = attributes;
			const setFile = () => {
			};
			if (isSelected) {
				return <div>
					<span>File Settings</span>
					<FileControlFromWordPress />
					<TextControl></TextControl>
				</div>
			}
			return <FileView
				file={file}
				title={title}
			/>;
		}
	}
)

const FileUI =() => {
	<FileUpload
		file={file}
		onChange={setFile}
	/>
	<FileTitle title={title}/>
}

const block = registerBlockType( 'whatever', {
		edit({attributes, setAttributes, isSelected}) {
			const {file, title} = attributes;
			const setFile = () => {
			};
			if (isSelected) {
				return <div>
					<FileUI />
				</div>
			}
			return <FileView
				file={file}
				title={title}
			/>;
		}
	}
)
