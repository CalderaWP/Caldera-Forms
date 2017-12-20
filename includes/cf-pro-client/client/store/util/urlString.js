export  const urlString = function (data, endpoint) {
	var str = "";
	for (var key in data) {
		if (str != "") {
			str += "&";
		}
		str += key + "=" + data[key];
	}
	return endpoint + '?' + str;
};
