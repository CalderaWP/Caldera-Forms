//https://gist.github.com/josephhanson/372b44f93472f9c5a2d025d40e7bb4cc
function MockFile() {
};

MockFile.prototype.create = function (name, size, mimeType) {
	name = name || "mock.txt";
	size = size || 1024;
	mimeType = mimeType || 'plain/txt';

	function range(count) {
		var output = "";
		for (var i = 0; i < count; i++) {
			output += "a";
		}
		return output;
	}

	var blob = new Blob([range(size)], {type: mimeType});
	blob.lastModifiedDate = new Date();
	blob.name = name;

	return blob;
};

const size = 1024 * 1024 * 2;
const mock = new MockFile();
export const file = mock.create("pic.png", size, "image/png");

export const threeFiles = [file, file, file];

export const CF_API_DATA = {
	"rest": {
		"root": "http://localhost:8228/wp-json/cf-api/v2/",
		"rootV3":"http://localhost:8228/wp-json/cf-api/v3/",
		"fileUpload":"http://localhost:8228/wp-json/cf-api/v3/file",
		"tokens": {
			"nonce":"http://localhost:8228/wp-json/cf-api/v2/tokens/form"
		},
		"nonce":
			"f89f76cda3"
	},
	"strings": {
		"cf2FileField": {
			"removeFile":"Remove file",
			"defaultButtonText":"Drop files or click to select files to Upload",
			"fileUploadError1": "Error: ",
			"fileUploadError2": " could not be processed",
			"invalidFileResponse": "Invalid",
			"fieldIsRequired": "Field is required"
		}
	},
	"nonce":{
		"field":"_cf_verify"
	}
};
export const CFFIELD_CONFIG = {"1":{"configs":{"fld_4065869":{"type":"summary","id":"fld_4065869_1","default":"","form_id":"CF5bc8e4db50622","form_id_attr":"caldera_form_1","sync":true,"tmplId":"html-content-fld_4065869_1-tmpl","contentId":"html-content-fld_4065869_1","bindFields":[{"tag":"{{fld_5843941}}","to":"fld_5843941_1"},{"tag":"{{fld_6540633}}","to":"fld_6540633_1"},{"tag":"{{fld_9226671}}","to":"fld_9226671_1"},{"tag":"{{fld_990986}}","to":"fld_990986_1"},{"tag":"{{fld_7276122}}","to":"fld_7276122_1"}]},"fld_6660137":{"type":"button","id":"fld_6660137_1","default":"","form_id":"CF5bc8e4db50622","form_id_attr":"caldera_form_1"}},"fields":{"ids":["fld_5843941_1","fld_6540633_1","fld_4065869_1","fld_9226671_1","fld_990986_1","fld_6660137_1","fld_7276122_1"],"inputs":[{"type":"text","fieldId":"fld_5843941","id":"fld_5843941_1","options":[],"default":""},{"type":"text","fieldId":"fld_6540633","id":"fld_6540633_1","options":[],"default":"old default"},{"type":"summary","fieldId":"fld_4065869","id":"fld_4065869_1","options":[],"default":""},{"type":"cf2_file","fieldId":"fld_9226671","id":"fld_9226671_1","options":[],"default":""},{"type":"dropdown","fieldId":"fld_990986","id":"fld_990986_1","options":[],"default":"No"},{"type":"button","fieldId":"fld_6660137","id":"fld_6660137_1","options":[],"default":""},{"type":"text","fieldId":"fld_7276122","id":"fld_7276122_1","options":[],"default":""}],"groups":[],"defaults":{"fld_5843941_1":"","fld_6540633_1":"old default","fld_4065869_1":"","fld_9226671_1":"","fld_990986_1":"No","fld_6660137_1":"","fld_7276122_1":""},"calcDefaults":{"fld_5843941_1":0,"fld_6540633_1":0,"fld_4065869_1":0,"fld_9226671_1":0,"fld_990986_1":0,"fld_6660137_1":0,"fld_7276122_1":0}},"error_strings":{"mixed_protocol":"Submission URL and current URL protocols do not match. Form may not function properly.","jquery_old":"An out of date version of jQuery is loaded on the page. Form may not function properly."}}};
export const obj = {
	"$form": {
		"id": "CF5bc8e4db50622_1",
		"class": "CF5bc8e4db50622 caldera_forms_form cfajax-trigger _tisBound active",
		"data-instance": "1",
		"method": "POST",
		"enctype": "multipart/form-data",
		"data-form-id": "CF5bc8e4db50622",
		"aria-label": "CF2 Fields + Conditionals #2766",
		"data-target": "#caldera_notices_1",
		"data-template": "#cfajax_CF5bc8e4db50622-tmpl",
		"data-cfajax": "CF5bc8e4db50622",
		"data-load-element": "_parent",
		"data-load-class": "cf_processing",
		"data-post-disable": "0",
		"data-action": "cf_process_ajax_submit",
		"data-request": "http://localhost:8228/cf-api/CF5bc8e4db50622",
		"data-hiderows": "true",
		"novalidate": "",
	},
	"formIdAttr": "CF5bc8e4db50622_1",
	"fieldsBlocking": [],
	"$notice": {}
};

export const shouldBeValidating = false;
export const messages = {};

export const theComponent = {
	"props": {
		"cfState": {},
		"formId": "CF5bc8e4db50622",
		"formIdAttr": "CF5bc8e4db50622_1",
		"fieldsToControl": [
			{
				"type": "file",
				"outterIdAttr": "cf2-fld_9226671_1",
				"fieldId": "fld_9226671",
				"fieldLabel": "CF2 File fields",
				"fieldCaption": "",
				"fieldPlaceHolder": "",
				"isRequired": false,
				"fieldDefault": "",
				"fieldValue": "",
				"fieldIdAttr": "fld_9226671_1",
				"configOptions": {
					"multiple": 1,
					"multiUploadText": false,
					"allowedTypes": false,
					"control": "cf2-fld_9226671_15c1236935a947",
					"usePreviews": true,
					"previewWidth": 90,
					"previewHeight": 90
				},
				"formId": "CF5bc8e4db50622",
				"control": "cf2_file5c1236935a925"
			}
		],
		"shouldBeValidating": false,
		"messages": {},
		"strings": {
			"cf2FileField": {
				'removeFile': 'Remove file',
				'defaultButtonText': 'Drop files or click to select files to Upload',
				'fileUploadError1': 'Error: ',
				'fileUploadError2': ' could not be processed',
				'invalidFiles': 'These Files have been rejected : ',
				'checkMessage': 'Please check file type and size ',
				'invalidFileResponse': 'Unknown File Process Error',
				'fieldIsRequired': 'Field is required',
				'filesUnit': 'bytes',
				'maxUploadSizeError': 'File size exceeded, the form will not submit',
				'maxUploadSizeInstruction': 'Remove this file and upload a file of less than ',
				'allowedTypes': 'Types allowed : ',
				'maxSize': 'Max. File Size : ',
			}
		}
	}
};

export const fieldsToControl = [
	{
		"type": "file",
		"outterIdAttr": "cf2-fld_9226671_1",
		"fieldId": "fld_9226671",
		"fieldLabel": "CF2 File fields",
		"fieldCaption": "",
		"fieldPlaceHolder": "",
		"isRequired": false,
		"fieldDefault": "",
		"fieldValue": "",
		"fieldIdAttr": "fld_9226671_1",
		"configOptions": {
			"multiple": 1,
			"multiUploadText": false,
			"allowedTypes": false,
			"control": "cf2-fld_9226671_15c12375c7d22f",
			"usePreviews": true,
			"previewWidth": 90,
			"previewHeight": 90
		},
		"formId": "CF5bc8e4db50622",
		"control": "cf2_file5c12375c7d20c"
	}
];

export const values = {
	"fld_9226671": threeFiles
}
export const oneValue = {
	"fld_9226671": [file]
}
export const twoValues = {
	"fld_9226671": [file, file]
}
export const threeValues = {
	"fld_9226671": threeFiles
}
export const fiveValues = {
	"fld_9226671": [file, file, file, file, file]
}

export const cf2 = {
	"fields": {
		"fld_9226671_1": {
			"type": "file",
			"outterIdAttr": "cf2-fld_9226671_1",
			"fieldId": "fld_9226671",
			"fieldLabel": "CF2 File fields",
			"fieldCaption": "",
			"fieldPlaceHolder": "",
			"isRequired": false,
			"fieldDefault": "",
			"fieldValue": "",
			"fieldIdAttr": "fld_9226671_1",
			"configOptions": {
				"multiple": 1,
				"multiUploadText": false,
				"allowedTypes": false,
				"control": "cf2-fld_9226671_15c18e05ab0708",
				"usePreviews": true,
				"previewWidth": 100,
				"previewHeight": 100
			},
			"formId": "CF5bc8e4db50622",
			"control": "cf2_file5c18e05ab06af"
		}
	},
	"pending": [],
	"uploadStarted": [
		"fld_9226671"
	],
	"uploadCompleted": [],
	"fieldsBlocking": []
}
