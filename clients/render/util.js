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