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

