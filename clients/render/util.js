const SparkMD5 = require('spark-md5');

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

export const removeFromBlocking = (fieldId, cf2, fieldConfig = {}) => {
	const index = cf2.fieldsBlocking.findIndex(item => item === fieldId);
	if (-1 < index) {
		cf2.fieldsBlocking.splice(index, 1);
	}
	setSubmitButtonState(cf2, fieldConfig);
}

export const setBlocking = ( fieldId, cf2, fieldConfig = {}) => {
	removeFromUploadStarted(fieldId, cf2);
	removeFromPending(fieldId, cf2);
	if(cf2.fieldsBlocking.indexOf(fieldId) < 0){
		cf2.fieldsBlocking.push( fieldId );
	}
	setSubmitButtonState(cf2, fieldConfig, false);
}

/**
 * Default process to convert a file to media
 *
 * @param file
 * @param additionalData
 * @param fetch
 * @return {*}
 */
export const createMediaFromFile = (file, additionalData, fetch) => {

	// Create upload payload
	const data = new window.FormData();
	data.append('file', file, file.name || file.type.replace('/', '.'));
	data.append('title', file.name ? file.name.replace(/\.[^.]+$/, '') : file.type.replace('/', '.'));
	Object.keys(additionalData)
		.forEach(key => data.append(key, additionalData[key]));

	return fetch(additionalData.API_FOR_FILES_URL, {
		body: data,
		method: 'POST',
		headers: {
			'X-WP-Nonce': additionalData._wp_nonce
		}
	});
}

/**
 * Set Submit button state
 *
 * @param {object} cf2
 * @param {object} fieldConfig
 * @param {boolean} state
 * @return {*}
 */
export const setSubmitButtonState = (cf2, fieldConfig, state) => {

	const fieldIdAttr = fieldConfig.fieldIdAttr;

	const formIdAttr = getFormIdAttrByFieldIdAttr(cf2, fieldIdAttr);

	const form =  jQuery("#" + formIdAttr);
	//If no state param was send in the function define the state based on elements inside the fieldsBlocking Array
	if(typeof state === "undefined") {
		state = cf2.fieldsBlocking.length <= 0;
	}

	//If state === true enable submit button else disable submit button
	if(state) {
		form.find(':submit').prop('disabled',false);
	} else {
		form.find(':submit').prop('disabled',true);
	}
	return state;
}

/**
 * Find the formIdAttr corresponding to a fieldIdAttr in the cf2 object
 *
 * @param {object} cf2
 * @param {string} fieldIdAttr
 * @return {string} formIdAttr
 */
export const getFormIdAttrByFieldIdAttr = (cf2, fieldIdAttr) => {
	const entries = Object.entries(cf2);
	const formEntries = [];
	let formIdAttr;
	entries.forEach( (entry) => {
		if(entry[1].hasOwnProperty("fields") ) {
			formEntries.push(entry);
		}
	})
	formEntries.forEach( formEntry => {
		if(formEntry[1].fields.hasOwnProperty(fieldIdAttr ) ) {
			formIdAttr = formEntry[0];
		}
	})
	return formIdAttr;
}