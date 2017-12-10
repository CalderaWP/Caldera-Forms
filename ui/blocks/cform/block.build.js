/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, {
/******/ 				configurable: false,
/******/ 				enumerable: true,
/******/ 				get: getter
/******/ 			});
/******/ 		}
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = 0);
/******/ })
/************************************************************************/
/******/ ([
/* 0 */
/***/ (function(module, exports) {

var __ = wp.i18n.__; // Import __() from wp.i18n

var registerBlockType = wp.blocks.registerBlockType; // Import registerBlockType() from wp.blocks

var BlockControls = wp.blocks.BlockControls;
var el = wp.element.createElement;
var cfForms = [];
var cfFormsOptions = {};
if (CF_FORMS.forms.length) {
    cfFormsOptions = CF_FORMS.forms;
}

if (Object.keys(cfFormsOptions).length) {
    Object.keys(cfFormsOptions).forEach(function (form) {
        cfForms.push(form.id);
    });
}

/**
 * Register Caldera Forms block
 *
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType('calderaforms/cform', {
    title: __('Caldera Form', 'caldera-forms'),
    icon: 'feedback',
    category: 'common',
    attributes: {
        formId: {
            formId: 'string',
            default: 'false'
        }
    },
    edit: function edit(_ref) {
        var attributes = _ref.attributes,
            setAttributes = _ref.setAttributes,
            className = _ref.className,
            focus = _ref.focus,
            id = _ref.id;

        var assetsAppended = {
            css: [],
            js: []
        };

        /**
         * Append CSS or JavaScript as needed if not already done
         *
         * @since 1.5.8
         *
         * @param {String} type
         * @param {String} url
         * @param {String} identifier
         */
        function appendCSSorJS(type, url, identifier) {

            switch (type) {
                case 'css':
                    if (-1 < assetsAppended.css.indexOf(identifier)) {
                        var fileref = document.createElement("link");
                        fileref.rel = "stylesheet";
                        fileref.type = "text/css";
                        fileref.href = url;
                        fileref.id = identifier;
                        document.getElementsByTagName("head")[0].appendChild(fileref);
                        assetsAppended.css.push(identifier);
                    }

                    break;
                case 'js':

                    if (-1 < assetsAppended.js.indexOf(identifier)) {
                        var _fileref = document.createElement("script");
                        _fileref.type = "text/javascript";
                        _fileref.src = url;
                        _fileref.id = identifier;
                        document.getElementsByTagName("body")[0].appendChild(_fileref);
                        assetsAppended.js.push(identifier);
                    }
            }
        }

        /**
         * Get a form preview and put where it goes
         *
         * NOTE: This is a super-hack, must replace
         *
         * @since 1.5.8
         *
         * @param {String} formId
         */
        function previewForm(formId) {
            if (false === formId || 'false' === formId || -1 < cfForms.indexOf(formId)) {
                return;
            }

            var url = CF_FORMS.previewApi.replace('-formId-', formId);
            var el = document.getElementById('caldera-forms-preview-' + id);
            wp.apiRequest({
                url: url,
                method: 'GET',
                params: {
                    preview: true
                },
                cache: true

            }).done(function (response) {

                if (null !== el) {
                    el.innerHTML = '';
                    el.innerHTML = response.html;
                    Object.keys(response.css).forEach(function (key) {
                        appendCSSorJS('css', response.css[key], key);
                    });
                    Object.keys(response.js).forEach(function (key) {
                        appendCSSorJS('js', response.js[key], key);
                    });
                }
            }).fail(function (response) {
                if (null !== el) {
                    el.innerHTML = __('Form Not Found', 'caldera-forms');
                }
            });
        }

        var previewEl = el('div', {
            id: 'caldera-forms-preview-' + id
        }, [el('span', {
            className: "spinner is-active"
        })]);
        var formId = attributes.formId;
        if (formId) {
            previewForm(formId);
        }
        var formPreview = attributes.formPreview;
        setAttributes({ formPreview: 'Load' });

        var updateFormId = function updateFormId(event) {
            formId = event.target.value;
            setAttributes({ formId: formId });

            previewForm(formId);

            event.preventDefault();
        };

        var formOptions = [el('option', {}, __('-- Choose --', 'caldera-forms'))];

        if (CF_FORMS.forms.length) {
            CF_FORMS.forms.forEach(function (form) {
                formOptions.push(el('option', {
                    value: form.formId
                }, form.name));
            });
        }

        var selectId = 'caldera-forms-form-selector-';
        var select = el('select', {
            value: formId,
            id: selectId,
            onChange: updateFormId

        }, formOptions);

        var formChooser = el('div', {}, [el('label', {
            for: selectId
        }, __('Form', 'caldera-forms')), select]);

        var focusControls = el(BlockControls, {
            key: 'controls'
        }, formChooser);

        return wp.element.createElement(
            'div',
            { className: className },
            previewEl,
            focus && focusControls
        );
    },

    save: function save(_ref2) {
        var attributes = _ref2.attributes,
            className = _ref2.className;

        return null;
    }
});

/***/ })
/******/ ]);