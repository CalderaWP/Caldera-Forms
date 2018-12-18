import {removeFromBlocking, removeFromPending, removeFromUploadStarted} from "../../../render/util";

export const handleFileUploadResponse = (response,cf2,$form,messages,field) => {
	const {fieldId} = field;
	if( 'object' !== typeof  response ){
		removeFromUploadStarted(fieldId,cf2);
		removeFromPending(fieldId,cf2);
		throw 'Upload Error';
	}
	else if (response.hasOwnProperty('control')) {
		removeFromPending(fieldId,cf2);
		removeFromBlocking(fieldId,cf2);
		cf2.uploadCompleted.push(fieldId);
		$form.submit();
	}else{
		if( response.hasOwnProperty('message') ){
			messages[field.fieldIdAttr] = {
				error: true,
				message: response.hasOwnProperty('message') ? response.message : 'Invalid'
			};
		}
		removeFromUploadStarted(fieldId,cf2);
		removeFromPending(fieldId,cf2);
		throw response;
	}
};

export  const handleFileUploadError = (error) => {

};
