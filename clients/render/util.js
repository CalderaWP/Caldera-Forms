export const SparkMD5 = require('spark-md5');

export const getFieldConfigBy = (fieldConfigs, findBy, findWhere) => {
	return fieldConfigs.find(field => findWhere === field[findBy]);

};


export const hashFile = (file, callback) => {
	let blobSlice = File.prototype.slice || File.prototype.mozSlice || File.prototype.webkitSlice,
		chunkSize = 2097152,                             // Read in chunks of 2MB
		chunks = Math.ceil(file.size / chunkSize),
		currentChunk = 0,
		spark = new SparkMD5.ArrayBuffer(),
		fileReader = new FileReader();

	fileReader.onload = function (e) {
		spark.append(e.target.result);                   // Append array buffer
		currentChunk++;

		if (currentChunk < chunks) {
			loadNext();
		} else {
			callback(spark.end());
		}
	};

	fileReader.onerror = function () {
		console.warn('oops, something went wrong.');
	};

	function loadNext() {
		var start = currentChunk * chunkSize,
			end = ((start + chunkSize) >= file.size) ? file.size : start + chunkSize;

		fileReader.readAsArrayBuffer(blobSlice.call(file, start, end));
	}

	loadNext();
}


export const createMediaFromFile = (file, additionalData, API_FOR_FILES_URL) => {

	// Create upload payload
	const data = new window.FormData();
	data.append('file', file, file.name || file.type.replace('/', '.'));
	data.append('title', file.name ? file.name.replace(/\.[^.]+$/, '') : file.type.replace('/', '.'));
	Object.keys(additionalData)
		.forEach(key => data.append(key, additionalData[key]));

	return fetch(API_FOR_FILES_URL, {
		body: data,
		method: 'POST',
		headers: {
			'X-WP-Nonce': additionalData._wp_nonce
		}
	});

}

export const removeFromPending = (fieldId, cf2) => {
	const index = cf2.pending.findIndex(item => item === fieldId);
	if (-1 < index) {
		cf2.pending.splice(index, 1);
	}
}

export const removeFromUploadStarted = (fieldId, cf2) => {
	const index = cf2.uploadStarted.findIndex(item => item === fieldId);
	if (-1 < index) {
		cf2.uploadStarted.splice(index, 1);
	}
}

export const removeFromBlocking = (fieldId, cf2) => {
	const index = cf2.fieldsBlocking.findIndex(item => item === fieldId);
	if (-1 < index) {
		cf2.fieldsBlocking.splice(index, 1);
	}

}

export const setBlocking = ( fieldId, cf2 ) => {
	removeFromUploadStarted(fieldId, cf2);
	removeFromPending(fieldId, cf2);
	cf2.fieldsBlocking.push( fieldId );
}

/**
 * Hash a file then upload it
 *
 * @since 1.8.0
 *
 * @param {File} file File blob
 * @param {string} verify Nonce token
 * @param {object} field field config
 * @param {string} fieldId ID for field
 */
export const hashAndUpload = (files, verify, field, fieldId, cf2, API_FOR_FILES_URL, _wp_nonce, obj, createMediaFromFile, hashFile ) => {

	files.forEach((file, index, array) => {
		if (file instanceof File) {
			hashFile(file, (hash) => {
				createMediaFromFile(file, {
						hashes: [hash],
						verify,
						formId: field.formId,
						fieldId: field.fieldId,
						control: field.control,
						_wp_nonce
					},
					API_FOR_FILES_URL ).then(
					response => response.json()
				).then(
					response => {
						if ('object' !== typeof  response) {
							removeFromUploadStarted(fieldId, cf2);
							removeFromPending(fieldId, cf2);
							throw response;
						}
						else if (response.hasOwnProperty('control')) {
							removeFromPending(fieldId, cf2);
							removeFromBlocking(fieldId, cf2);
							cf2.uploadCompleted.push(fieldId);
							if (index === array.length - 1){
								obj.$form.submit()
								return response;
							}
						} else {
							if (response.hasOwnProperty('message')) {
								messages[field.fieldIdAttr] = {
									error: true,
									message: response.hasOwnProperty('message') ? response.message : 'Invalid'
								};
							}
							removeFromUploadStarted(fieldId, cf2);
							removeFromPending(fieldId, cf2);
							throw response;
						}

					}
				).catch(
					error => console.log(error)
				);
			})
		}
	})
}

export const onRequest = ( obj, cf2, shouldBeValidating, messages, theComponent, values, fieldsToControl, CF_API_DATA,
					createMediaFromFile, hashFile, hashAndUpload, setBlocking, removeFromBlocking, removeFromUploadStarted, removeFromPending ) => {

	const API_FOR_FILES_URL = CF_API_DATA.rest.fileUpload;
	const _wp_nonce = CF_API_DATA.rest.nonce;

	shouldBeValidating = true;


	const {displayFieldErrors,$notice,$form,fieldsBlocking} = obj;

	if ('object' !== typeof cf2) {
		return;
	}

	cf2.pending = cf2.pending || [];
	cf2.uploadStarted = cf2.uploadStarted || [];
	cf2.uploadCompleted = cf2.uploadCompleted || [];
	cf2.fieldsBlocking = cf2.fieldsBlocking || [];

	if (Object.keys(values).length) {
		Object.keys(values).forEach(fieldId => {
			const field = fieldsToControl.find(field => fieldId === field.fieldId);
			if (field) {
				const {fieldIdAttr} = field;
				if ('file' === field.type) {
					//do not upload after complete
					if ( cf2.uploadCompleted.includes(fieldId)) {
						removeFromPending(fieldId, cf2);
						removeFromBlocking(fieldId, cf2);
						return;
					}
					//do not start upload if it has started uploading
					if (-1 <= cf2.uploadStarted.indexOf(_fieldId => _fieldId === fieldId )
						&& -1 <= cf2.pending.indexOf(_fieldId => _fieldId === fieldId)
					) {
						cf2.uploadStarted.push(fieldId);
						obj.$form.data(fieldId, field.control);
						cf2.pending.push(fieldId);
						const verify = jQuery(`#_cf_verify_${field.formId}`).val();
						if( '' === values[fieldId] ){
							if( theComponent.isFieldRequired(fieldIdAttr) ){
								theComponent.addFieldMessage( fieldIdAttr, "Field is required" );
								shouldBeValidating = true;
								setBlocking(fieldId, cf2);
							}
							removeFromPending(fieldId, cf2);
							return;
						}
						removeFromBlocking(fieldId, cf2);
						const files = [values[fieldId]][0];
						if(Array.isArray(files)){
							hashAndUpload(files, verify, field, fieldId, cf2, API_FOR_FILES_URL, _wp_nonce, obj, createMediaFromFile, hashFile);
						}


					}


				}
			}
		});
	} else {
		obj.$form.data(fieldId, values[fieldId]);
	}

};
