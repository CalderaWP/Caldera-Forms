/*! GENERATED SOURCE FILE caldera-forms - v1.5.6.2 - 2017-10-12 *//**
 * Simple event bindings for form state
 *
 * In general, access through CFState.events() not directly.
 *
 * @since 1.5.3
 *
 * @param state {CFState} State object to subscribe to
 * @constructor
 */
function CFEvents(state) {
	var events = {};

	/**
	 * Attach an event (add_action)
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Any string, but generally input ID
	 * @param callback {Function} The callback function
	 */
	this.subscribe = function (id, callback) {
		if (!hasEvents(id)) {
			events[id] = [];
		}
		events[id].push(callback);
	};

	/**
	 * Trigger an event (do_action)
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Any string, but generally input ID
	 * @param value {*} The value to pass to callback
	 */
	this.trigger = function (id, value) {
		if (!hasEvents(id)) {
			return;
		}

		events[id].forEach(function (callback) {
			callback(state.getState(id),id);
		});

	};

	/**
	 * Detach a bound event (remove_action)
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Any string, but generally input ID
	 * @param callback {Function|null} The callback function you wish to detatch or null to detach all events.
	 */
	this.detach = function(id,callback){
		if( hasEvents(id)){
			if( null === callback ){
				delete events[id];
			}else{
				for (var key in events[id]) {
					if (callback === key) {
						events[id].splice(key, 1);
					}
				}
			}

		}
	};

	/**
	 * Check if there are events attatched to an identifier
	 *
	 * @since 1.5.23
	 *
	 *
	 * @param id {String} Identifying string
	 * @returns {boolean}
	 */
	function hasEvents(id) {
		return events.hasOwnProperty(id);
	}

}



/**
 * State management for front-end
 *
 * @since 1.5.3
 *
 * @param formId {String} ID of form this is tracking state for.
 * @param $ {jquery} jQuery
 *
 * @constructor
 */
function CFState(formId, $ ){

	var
		self = this,
		fields = {},
		events = new CFEvents(this),
		unBound = {},
		fieldVals  = {},
		calcVals = {};


	/**
	 * Initialized ( or re-initialize) state with specific fields.
	 *
	 * @since 1.5.3
	 *
	 * @param formFields {Object} Should be flat field ID attribute : Field default
	 */
	this.init = function (formFields, calcDefaults) {

		for ( var id in formFields ){
			if( 'object' === typeof  calcDefaults[id] ){
				if( 'calculation' == calcDefaults[id].type ){
					bindCalcField(id,calcDefaults[id])
				}

			}else if( bindField(id)){
				fieldVals[id] = formFields[id];
				if( calcDefaults.hasOwnProperty(id) ){
					calcVals[id] = calcDefaults[id];
				}else{
					calcVals[id] = null;
				}
			}else{
				fieldVals[id] = '';
				unBound[id] = true;
				calcVals[id] = null;
			}

		}

	};

	/**
	 * Get current state for a field
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Field id attribute
	 * @returns {String|Array}
	 */
	this.getState = function(id){
		if( ! inState(id) ){
			return false;
		}

		return fieldVals[id];
	};

	/**
	 *Get calculation value for a field
	 *
	 * @since 1.5.6
	 *
	 * @param id {String} Field id attribute
	 * @param highest {Boolean}
	 * @returns {float}
	 */
	this.getCalcValue = function (id,highest) {
		var val = 0;

		if (! inState( id )) {
			return val;
		}

		if( highest ){
			highest = 0;
			var value = highest,
				$item;
			$( '#' + id ).each(function(){
				value = 0;
				$item = $( this );
				if(  $item.prop('checked' ) ){
					value = findCalcVal( $item );
					if( parseFloat( value ) > parseFloat( highest ) ){
						highest = parseFloat( value );
					}
				}

			});
			return parseFloat( highest );
		}

		if (calcVals.hasOwnProperty(id) ) {
			if( false === calcVals[id] || null === calcVals[id] ){
				//@TODO use let here, when ES6.
				var _val = findCalcVal( $( document.getElementById( id ) ) );
				if( isString( _val )  ) {
					_val = parseFloat( _val );
				}

				if( isNumber( _val ) ){
					calcVals[id] = _val;
				}
			}

			val = calcVals[id];
		} else {
			val = self.getState(id);

			if ($.isArray(val)) {
				val = val.reduce( function ( a, b) {
					return parseFloat( a ) + parseFloat( b );
				}, 0);
			}

			if( isNumber( val ) ){
				calcVals[id] = val;
			}
		}

		return parseFloat( val );
	};

	/**
	 * Change state for a field
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Field id attribute
	 * @param value {String|Array} New value
	 */
	this.mutateState = function(id, value ){

		if( ! inState(id) ){
			return false;
		}

		if( fieldVals[id] != value ){
			fieldVals[id] = value;
			events.trigger(id,value);
		}

		return true;
	};

	/**
	 * Unbind field -- used when hiding via conditional logic
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Field id attribute
	 */
	this.unbind = function(id){
		self.mutateState(id,'');
		unBound[id] = true;
		delete calcVals[id];
	};

	/**
	 * Rebind field -- used when unhiding via conditional logic
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Field id attribute
	 */
	this.rebind = function(id){
		bindField(id);

		delete unBound[id];
	};


	/**
	 * Accessor for the CFEvents object used for this state
	 *
	 * @since 1.5.3
	 *
	 * @returns {{subscribe: subscribe, detach: detach}}
	 */
	this.events = function(){
		return {
			/**
			 * Attach an event to change of an input in the state
			 *
			 * @since 1.5.3
			 *
			 * @param id {String} Field ID attribute
			 * @param callback {Function} The callback function
			 */
			subscribe: function( id, callback ){
				if( inState(id)){
					events.subscribe(id,callback);
				}

			},
			/**
			 * Detach an event to change of an input in the state
			 *
			 * @since 1.5.3
			 *
			 * @param id {String} Field ID attribute
			 * @param callback {Function|null} The callback function. Pass null to detach all.
			 */
			detach: function(id,callback){
				events.detach(id,callback);
			}
		}
	};


	/**
	 * Check if value is tracked in state
	 *
	 * @since 1.5.3
	 *
	 * @param id {String} Field ID attribute
	 *
	 * @returns {boolean}
	 */
	function inState(id){
		return fieldVals.hasOwnProperty(id);
	}

	/**
	 * Bind a field's change events
	 *
	 * @since 1.5.3
	 *
	 * @param {String} id
	 * @returns {boolean}
	 */
	function bindField(id) {
		var $field = $('#' + id);
		if ($field.length) {
			$field.on('change keyup', function () {
				var $el = $(this);
				console.log( $field.attr( 'type' ) );
				console.log( $el.attr( 'type' ) );
				calcVals[$el.attr('id')] = findCalcVal( $el );
				self.mutateState([$el.attr('id')],$el.val());
			});
			calcVals[id] = findCalcVal( $( document.getElementById( id ) ) );
			self.mutateState([$field.attr('id')],$field.val());

			return true;
		} else {
			$field = $('.' + id);
			if ($field.length) {


				$field.on('change', function () {
					var val = [];
					var $el = $(this),
					 	id,
						$collection,
						type = $el.attr( 'type' );

					switch ( type ){
						case 'radio' :
							id = $el.data( 'radio-field' );
							$collection = $( '[data-radio-field=' + id +']' );
							val = '';
							break;
						case 'checkbox' :
							id = $el.data( 'checkbox-field' );
							$collection = $( '[data-checkbox-field=' + id +']' );
							break;
						default :
							id = $el.data( 'field' );
							$collection = $( '[data-field=' + id +']' );
							break;
					}

					if( ! $collection.length ){
						val = 0;
					} else if ( 1 == $collection.length){
						val = findCalcVal( $($collection[0]));
					} else if ( 'checkbox' === type ) {
						var $v, sum = 0;
						$collection.each(function (k, v) {
							$v = $(v);
							if( $v.prop('checked')){
								sum += parseFloat(findCalcVal($v));
							}
							val.push($v.val());
						});
						calcVals[id] = sum;
					}else{
						$collection.each(function (i, el) {
							var $this = $(el);

							if ($this.prop('checked')) {
								if ('radio' === type) {
									calcVals[id] = findCalcVal($this);
									val = $this.val();
								} else {
									val.push($this.val());
								}
							}
						});
					}


					self.mutateState(id,val);

				});
				return true;
			}


		}

		self.unbind(id);

		return false;

	}

	/**
	 * Bind change on a calculation field so that when state changes, calc value changes with it.
	 *
	 * @since 1.5.6.2
	 *
	 * @param {String} id
	 * @param {Object} config
	 */
	function bindCalcField(id,config) {
		fieldVals[id] = 0;
		calcVals[id] = 0;
		self.events().subscribe(id,function (value,id) {
			calcVals[id] = value;
		});
	}

	/**
	 * Find calculation value for an element
	 *
	 * @since 1.5.6
	 * @param {jQuery} $field
	 * @returns {float}
	 */
	function findCalcVal( $field ) {
		if( $field.is( 'select' ) && $field.has( 'option' ) ){
			$field = $field.find(':selected');
		}

		if( ! $field.length ){
			return 0;
		}

		if( $field.is( 'hidden' ) ){
			return $field.val();
		}

		var val = 0;

		var attr = $field.attr('data-calc-value');

		if (typeof attr !== typeof undefined && attr !== false && ! isNaN(attr)) {
			val = $field.data( 'calc-value' );
		}else{
			val = $field.val();
		}

		return parseFloat(val);
	}

	/**
	 * Parse float if we can parse float, else 0.
	 *
	 * @since 1.5.6
	 *
	 * @param number
	 * @returns {*}
	 */
	function parseFloat( number ) {
		if( ! number || isNaN( number) ){
			return 0.0;
		}
		return window.parseFloat( number );
	}

	/**
	 * Parse integer if we can parse integer, else 0.
	 *
	 * @since 1.5.6
	 *
	 * @param number
	 * @returns {*}
	 */
	function parseInt( number ) {
		if( ! number || isNaN( number) ){
			return 0;
		}
		return window.parseInt( number );
	}


	/**
	 * Determine if a value is a Number
	 *
	 * @since 1.5.6.2
	 *
	 * Copied from axios/lib/utils.js
	 *
	 * @param {Object} val The value to test
	 * @returns {boolean} True if value is a Number, otherwise false
	 */
	function isNumber(val) {
		return typeof val === 'number';
	}

	/**
	 * Determine if a value is a String
	 *
	 * @since 1.5.6.2
	 *
	 * Copied from axios/lib/utils.js

	 * @param {Object} val The value to test
	 * @returns {boolean} True if value is a String, otherwise false
	 */
	function isString(val) {
		return typeof val === 'string';
	}



}
/*!
 * jquery.inputmask.bundle.js
 * https://github.com/RobinHerbots/Inputmask
 * Copyright (c) 2010 - 2017 Robin Herbots
 * Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
 * Version: 4.0.1-37
 */

!function(modules) {
	function __webpack_require__(moduleId) {
		if (installedModules[moduleId]) return installedModules[moduleId].exports;
		var module = installedModules[moduleId] = {
			i: moduleId,
			l: !1,
			exports: {}
		};
		return modules[moduleId].call(module.exports, module, module.exports, __webpack_require__),
			module.l = !0, module.exports;
	}
	var installedModules = {};
	__webpack_require__.m = modules, __webpack_require__.c = installedModules, __webpack_require__.i = function(value) {
		return value;
	}, __webpack_require__.d = function(exports, name, getter) {
		__webpack_require__.o(exports, name) || Object.defineProperty(exports, name, {
			configurable: !1,
			enumerable: !0,
			get: getter
		});
	}, __webpack_require__.n = function(module) {
		var getter = module && module.__esModule ? function() {
			return module.default;
		} : function() {
			return module;
		};
		return __webpack_require__.d(getter, "a", getter), getter;
	}, __webpack_require__.o = function(object, property) {
		return Object.prototype.hasOwnProperty.call(object, property);
	}, __webpack_require__.p = "", __webpack_require__(__webpack_require__.s = 9);
}([ function(module, exports, __webpack_require__) {
	"use strict";
	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;
	"function" == typeof Symbol && Symbol.iterator;
	!function(factory) {
		__WEBPACK_AMD_DEFINE_ARRAY__ = [ __webpack_require__(2) ], __WEBPACK_AMD_DEFINE_FACTORY__ = factory,
		void 0 !== (__WEBPACK_AMD_DEFINE_RESULT__ = "function" == typeof __WEBPACK_AMD_DEFINE_FACTORY__ ? __WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__) : __WEBPACK_AMD_DEFINE_FACTORY__) && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__);
	}(function($) {
		return $;
	});
}, function(module, exports, __webpack_require__) {
	"use strict";
	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__, _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(obj) {
		return typeof obj;
	} : function(obj) {
		return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
	};
	!function(factory) {
		__WEBPACK_AMD_DEFINE_ARRAY__ = [ __webpack_require__(0), __webpack_require__(11), __webpack_require__(10) ],
			__WEBPACK_AMD_DEFINE_FACTORY__ = factory, void 0 !== (__WEBPACK_AMD_DEFINE_RESULT__ = "function" == typeof __WEBPACK_AMD_DEFINE_FACTORY__ ? __WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__) : __WEBPACK_AMD_DEFINE_FACTORY__) && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__);
	}(function($, window, document, undefined) {
		function Inputmask(alias, options, internal) {
			if (!(this instanceof Inputmask)) return new Inputmask(alias, options, internal);
			this.el = undefined, this.events = {}, this.maskset = undefined, this.refreshValue = !1,
			!0 !== internal && ($.isPlainObject(alias) ? options = alias : (options = options || {},
				options.alias = alias), this.opts = $.extend(!0, {}, this.defaults, options), this.noMasksCache = options && options.definitions !== undefined,
				this.userOptions = options || {}, this.isRTL = this.opts.numericInput, resolveAlias(this.opts.alias, options, this.opts));
		}
		function resolveAlias(aliasStr, options, opts) {
			var aliasDefinition = Inputmask.prototype.aliases[aliasStr];
			return aliasDefinition ? (aliasDefinition.alias && resolveAlias(aliasDefinition.alias, undefined, opts),
				$.extend(!0, opts, aliasDefinition), $.extend(!0, opts, options), !0) : (null === opts.mask && (opts.mask = aliasStr),
				!1);
		}
		function generateMaskSet(opts, nocache) {
			function generateMask(mask, metadata, opts) {
				var regexMask = !1;
				if (null !== mask && "" !== mask || (regexMask = null !== opts.regex, regexMask ? (mask = opts.regex,
						mask = mask.replace(/^(\^)(.*)(\$)$/, "$2")) : (regexMask = !0, mask = ".*")), 1 === mask.length && !1 === opts.greedy && 0 !== opts.repeat && (opts.placeholder = ""),
					opts.repeat > 0 || "*" === opts.repeat || "+" === opts.repeat) {
					var repeatStart = "*" === opts.repeat ? 0 : "+" === opts.repeat ? 1 : opts.repeat;
					mask = opts.groupmarker.start + mask + opts.groupmarker.end + opts.quantifiermarker.start + repeatStart + "," + opts.repeat + opts.quantifiermarker.end;
				}
				var masksetDefinition, maskdefKey = regexMask ? "regex_" + opts.regex : opts.numericInput ? mask.split("").reverse().join("") : mask;
				return Inputmask.prototype.masksCache[maskdefKey] === undefined || !0 === nocache ? (masksetDefinition = {
					mask: mask,
					maskToken: Inputmask.prototype.analyseMask(mask, regexMask, opts),
					validPositions: {},
					_buffer: undefined,
					buffer: undefined,
					tests: {},
					metadata: metadata,
					maskLength: undefined
				}, !0 !== nocache && (Inputmask.prototype.masksCache[maskdefKey] = masksetDefinition,
					masksetDefinition = $.extend(!0, {}, Inputmask.prototype.masksCache[maskdefKey]))) : masksetDefinition = $.extend(!0, {}, Inputmask.prototype.masksCache[maskdefKey]),
					masksetDefinition;
			}
			if ($.isFunction(opts.mask) && (opts.mask = opts.mask(opts)), $.isArray(opts.mask)) {
				if (opts.mask.length > 1) {
					opts.keepStatic = null === opts.keepStatic || opts.keepStatic;
					var altMask = opts.groupmarker.start;
					return $.each(opts.numericInput ? opts.mask.reverse() : opts.mask, function(ndx, msk) {
						altMask.length > 1 && (altMask += opts.groupmarker.end + opts.alternatormarker + opts.groupmarker.start),
							msk.mask === undefined || $.isFunction(msk.mask) ? altMask += msk : altMask += msk.mask;
					}), altMask += opts.groupmarker.end, generateMask(altMask, opts.mask, opts);
				}
				opts.mask = opts.mask.pop();
			}
			return opts.mask && opts.mask.mask !== undefined && !$.isFunction(opts.mask.mask) ? generateMask(opts.mask.mask, opts.mask, opts) : generateMask(opts.mask, opts.mask, opts);
		}
		function maskScope(actionObj, maskset, opts) {
			function getMaskTemplate(baseOnInput, minimalPos, includeMode) {
				minimalPos = minimalPos || 0;
				var ndxIntlzr, test, testPos, maskTemplate = [], pos = 0, lvp = getLastValidPosition();
				do {
					!0 === baseOnInput && getMaskSet().validPositions[pos] ? (testPos = getMaskSet().validPositions[pos],
						test = testPos.match, ndxIntlzr = testPos.locator.slice(), maskTemplate.push(!0 === includeMode ? testPos.input : !1 === includeMode ? test.nativeDef : getPlaceholder(pos, test))) : (testPos = getTestTemplate(pos, ndxIntlzr, pos - 1),
						test = testPos.match, ndxIntlzr = testPos.locator.slice(), (!1 === opts.jitMasking || pos < lvp || "number" == typeof opts.jitMasking && isFinite(opts.jitMasking) && opts.jitMasking > pos) && maskTemplate.push(!1 === includeMode ? test.nativeDef : getPlaceholder(pos, test))),
						pos++;
				} while ((maxLength === undefined || pos < maxLength) && (null !== test.fn || "" !== test.def) || minimalPos > pos);
				return "" === maskTemplate[maskTemplate.length - 1] && maskTemplate.pop(), getMaskSet().maskLength = pos + 1,
					maskTemplate;
			}
			function getMaskSet() {
				return maskset;
			}
			function resetMaskSet(soft) {
				var maskset = getMaskSet();
				maskset.buffer = undefined, !0 !== soft && (maskset.validPositions = {}, maskset.p = 0);
			}
			function getLastValidPosition(closestTo, strict, validPositions) {
				var before = -1, after = -1, valids = validPositions || getMaskSet().validPositions;
				closestTo === undefined && (closestTo = -1);
				for (var posNdx in valids) {
					var psNdx = parseInt(posNdx);
					valids[psNdx] && (strict || !0 !== valids[psNdx].generatedInput) && (psNdx <= closestTo && (before = psNdx),
					psNdx >= closestTo && (after = psNdx));
				}
				return -1 !== before && closestTo - before > 1 || after < closestTo ? before : after;
			}
			function stripValidPositions(start, end, nocheck, strict) {
				var i, startPos = start, positionsClone = $.extend(!0, {}, getMaskSet().validPositions), needsValidation = !1;
				for (getMaskSet().p = start, i = end - 1; i >= startPos; i--) getMaskSet().validPositions[i] !== undefined && (!0 !== nocheck && (!getMaskSet().validPositions[i].match.optionality && function(pos) {
					var posMatch = getMaskSet().validPositions[pos];
					if (posMatch !== undefined && null === posMatch.match.fn) {
						var prevMatch = getMaskSet().validPositions[pos - 1], nextMatch = getMaskSet().validPositions[pos + 1];
						return prevMatch !== undefined && nextMatch !== undefined;
					}
					return !1;
				}(i) || !1 === opts.canClearPosition(getMaskSet(), i, getLastValidPosition(undefined, !0), strict, opts)) || delete getMaskSet().validPositions[i]);
				for (resetMaskSet(!0), i = startPos + 1; i <= getLastValidPosition(); ) {
					for (;getMaskSet().validPositions[startPos] !== undefined; ) startPos++;
					if (i < startPos && (i = startPos + 1), getMaskSet().validPositions[i] === undefined && isMask(i)) i++; else {
						var t = getTestTemplate(i);
						!1 === needsValidation && positionsClone[startPos] && positionsClone[startPos].match.def === t.match.def ? (getMaskSet().validPositions[startPos] = $.extend(!0, {}, positionsClone[startPos]),
							getMaskSet().validPositions[startPos].input = t.input, delete getMaskSet().validPositions[i],
							i++) : positionCanMatchDefinition(startPos, t.match.def) ? !1 !== isValid(startPos, t.input || getPlaceholder(i), !0) && (delete getMaskSet().validPositions[i],
							i++, needsValidation = !0) : isMask(i) || (i++, startPos--), startPos++;
					}
				}
				resetMaskSet(!0);
			}
			function determineTestTemplate(tests, guessNextBest) {
				for (var testPos, testPositions = tests, lvp = getLastValidPosition(), lvTest = getMaskSet().validPositions[lvp] || getTests(0)[0], lvTestAltArr = lvTest.alternation !== undefined ? lvTest.locator[lvTest.alternation].toString().split(",") : [], ndx = 0; ndx < testPositions.length && (testPos = testPositions[ndx],
				!(testPos.match && (opts.greedy && !0 !== testPos.match.optionalQuantifier || (!1 === testPos.match.optionality || !1 === testPos.match.newBlockMarker) && !0 !== testPos.match.optionalQuantifier) && (lvTest.alternation === undefined || lvTest.alternation !== testPos.alternation || testPos.locator[lvTest.alternation] !== undefined && checkAlternationMatch(testPos.locator[lvTest.alternation].toString().split(","), lvTestAltArr))) || !0 === guessNextBest && (null !== testPos.match.fn || /[0-9a-bA-Z]/.test(testPos.match.def))); ndx++) ;
				return testPos;
			}
			function getTestTemplate(pos, ndxIntlzr, tstPs) {
				return getMaskSet().validPositions[pos] || determineTestTemplate(getTests(pos, ndxIntlzr ? ndxIntlzr.slice() : ndxIntlzr, tstPs));
			}
			function getTest(pos) {
				return getMaskSet().validPositions[pos] ? getMaskSet().validPositions[pos] : getTests(pos)[0];
			}
			function positionCanMatchDefinition(pos, def) {
				for (var valid = !1, tests = getTests(pos), tndx = 0; tndx < tests.length; tndx++) if (tests[tndx].match && tests[tndx].match.def === def) {
					valid = !0;
					break;
				}
				return valid;
			}
			function getTests(pos, ndxIntlzr, tstPs) {
				function resolveTestFromToken(maskToken, ndxInitializer, loopNdx, quantifierRecurse) {
					function handleMatch(match, loopNdx, quantifierRecurse) {
						function isFirstMatch(latestMatch, tokenGroup) {
							var firstMatch = 0 === $.inArray(latestMatch, tokenGroup.matches);
							return firstMatch || $.each(tokenGroup.matches, function(ndx, match) {
								if (!0 === match.isQuantifier && (firstMatch = isFirstMatch(latestMatch, tokenGroup.matches[ndx - 1]))) return !1;
							}), firstMatch;
						}
						function resolveNdxInitializer(pos, alternateNdx, targetAlternation) {
							var bestMatch, indexPos;
							if (getMaskSet().validPositions[pos - 1] && targetAlternation && getMaskSet().tests[pos]) for (var vpAlternation = getMaskSet().validPositions[pos - 1].locator, tpAlternation = getMaskSet().tests[pos][0].locator, i = 0; i < targetAlternation; i++) if (vpAlternation[i] !== tpAlternation[i]) return vpAlternation.slice(targetAlternation + 1);
							return (getMaskSet().tests[pos] || getMaskSet().validPositions[pos]) && $.each(getMaskSet().tests[pos] || [ getMaskSet().validPositions[pos] ], function(ndx, lmnt) {
								var alternation = targetAlternation !== undefined ? targetAlternation : lmnt.alternation, ndxPos = lmnt.locator[alternation] !== undefined ? lmnt.locator[alternation].toString().indexOf(alternateNdx) : -1;
								(indexPos === undefined || ndxPos < indexPos) && -1 !== ndxPos && (bestMatch = lmnt,
									indexPos = ndxPos);
							}), bestMatch ? bestMatch.locator.slice((targetAlternation !== undefined ? targetAlternation : bestMatch.alternation) + 1) : targetAlternation !== undefined ? resolveNdxInitializer(pos, alternateNdx) : undefined;
						}
						if (testPos > 1e4) throw "Inputmask: There is probably an error in your mask definition or in the code. Create an issue on github with an example of the mask you are using. " + getMaskSet().mask;
						if (testPos === pos && match.matches === undefined) return matches.push({
							match: match,
							locator: loopNdx.reverse(),
							cd: cacheDependency
						}), !0;
						if (match.matches !== undefined) {
							if (match.isGroup && quantifierRecurse !== match) {
								if (match = handleMatch(maskToken.matches[$.inArray(match, maskToken.matches) + 1], loopNdx)) return !0;
							} else if (match.isOptional) {
								var optionalToken = match;
								if (match = resolveTestFromToken(match, ndxInitializer, loopNdx, quantifierRecurse)) {
									if (latestMatch = matches[matches.length - 1].match, !isFirstMatch(latestMatch, optionalToken)) return !0;
									insertStop = !0, testPos = pos;
								}
							} else if (match.isAlternator) {
								var maltMatches, alternateToken = match, malternateMatches = [], currentMatches = matches.slice(), loopNdxCnt = loopNdx.length, altIndex = ndxInitializer.length > 0 ? ndxInitializer.shift() : -1;
								if (-1 === altIndex || "string" == typeof altIndex) {
									var amndx, currentPos = testPos, ndxInitializerClone = ndxInitializer.slice(), altIndexArr = [];
									if ("string" == typeof altIndex) altIndexArr = altIndex.split(","); else for (amndx = 0; amndx < alternateToken.matches.length; amndx++) altIndexArr.push(amndx);
									for (var ndx = 0; ndx < altIndexArr.length; ndx++) {
										if (amndx = parseInt(altIndexArr[ndx]), matches = [], ndxInitializer = resolveNdxInitializer(testPos, amndx, loopNdxCnt) || ndxInitializerClone.slice(),
											!0 !== (match = handleMatch(alternateToken.matches[amndx] || maskToken.matches[amndx], [ amndx ].concat(loopNdx), quantifierRecurse) || match) && match !== undefined && altIndexArr[altIndexArr.length - 1] < alternateToken.matches.length) {
											var ntndx = $.inArray(match, maskToken.matches) + 1;
											maskToken.matches.length > ntndx && (match = handleMatch(maskToken.matches[ntndx], [ ntndx ].concat(loopNdx.slice(1, loopNdx.length)), quantifierRecurse)) && (altIndexArr.push(ntndx.toString()),
												$.each(matches, function(ndx, lmnt) {
													lmnt.alternation = loopNdx.length - 1;
												}));
										}
										maltMatches = matches.slice(), testPos = currentPos, matches = [];
										for (var ndx1 = 0; ndx1 < maltMatches.length; ndx1++) {
											var altMatch = maltMatches[ndx1], dropMatch = !1;
											altMatch.alternation = altMatch.alternation || loopNdxCnt;
											for (var ndx2 = 0; ndx2 < malternateMatches.length; ndx2++) {
												var altMatch2 = malternateMatches[ndx2];
												if ("string" != typeof altIndex || -1 !== $.inArray(altMatch.locator[altMatch.alternation].toString(), altIndexArr)) {
													if (function(source, target) {
															return source.match.nativeDef === target.match.nativeDef || source.match.def === target.match.nativeDef || source.match.nativeDef === target.match.def;
														}(altMatch, altMatch2)) {
														dropMatch = !0, altMatch.alternation === altMatch2.alternation && -1 === altMatch2.locator[altMatch2.alternation].toString().indexOf(altMatch.locator[altMatch.alternation]) && (altMatch2.locator[altMatch2.alternation] = altMatch2.locator[altMatch2.alternation] + "," + altMatch.locator[altMatch.alternation],
															altMatch2.alternation = altMatch.alternation), altMatch.match.nativeDef === altMatch2.match.def && (altMatch.locator[altMatch.alternation] = altMatch2.locator[altMatch2.alternation],
															malternateMatches.splice(malternateMatches.indexOf(altMatch2), 1, altMatch));
														break;
													}
													if (altMatch.match.def === altMatch2.match.def) {
														dropMatch = !1;
														break;
													}
													if (function(source, target) {
															return null === source.match.fn && null !== target.match.fn && target.match.fn.test(source.match.def, getMaskSet(), pos, !1, opts, !1);
														}(altMatch, altMatch2) || function(source, target) {
															return null !== source.match.fn && null !== target.match.fn && target.match.fn.test(source.match.def.replace(/[\[\]]/g, ""), getMaskSet(), pos, !1, opts, !1);
														}(altMatch, altMatch2)) {
														altMatch.alternation === altMatch2.alternation && -1 === altMatch.locator[altMatch.alternation].toString().indexOf(altMatch2.locator[altMatch2.alternation].toString().split("")[0]) && (altMatch.na = altMatch.na || altMatch.locator[altMatch.alternation].toString(),
														-1 === altMatch.na.indexOf(altMatch.locator[altMatch.alternation].toString().split("")[0]) && (altMatch.na = altMatch.na + "," + altMatch.locator[altMatch2.alternation].toString().split("")[0]),
															dropMatch = !0, altMatch.locator[altMatch.alternation] = altMatch2.locator[altMatch2.alternation].toString().split("")[0] + "," + altMatch.locator[altMatch.alternation],
															malternateMatches.splice(malternateMatches.indexOf(altMatch2), 0, altMatch));
														break;
													}
												}
											}
											dropMatch || malternateMatches.push(altMatch);
										}
									}
									"string" == typeof altIndex && (malternateMatches = $.map(malternateMatches, function(lmnt, ndx) {
										if (isFinite(ndx)) {
											var alternation = lmnt.alternation, altLocArr = lmnt.locator[alternation].toString().split(",");
											lmnt.locator[alternation] = undefined, lmnt.alternation = undefined;
											for (var alndx = 0; alndx < altLocArr.length; alndx++) -1 !== $.inArray(altLocArr[alndx], altIndexArr) && (lmnt.locator[alternation] !== undefined ? (lmnt.locator[alternation] += ",",
												lmnt.locator[alternation] += altLocArr[alndx]) : lmnt.locator[alternation] = parseInt(altLocArr[alndx]),
												lmnt.alternation = alternation);
											if (lmnt.locator[alternation] !== undefined) return lmnt;
										}
									})), matches = currentMatches.concat(malternateMatches), testPos = pos, insertStop = matches.length > 0,
										match = malternateMatches.length > 0, ndxInitializer = ndxInitializerClone.slice();
								} else match = handleMatch(alternateToken.matches[altIndex] || maskToken.matches[altIndex], [ altIndex ].concat(loopNdx), quantifierRecurse);
								if (match) return !0;
							} else if (match.isQuantifier && quantifierRecurse !== maskToken.matches[$.inArray(match, maskToken.matches) - 1]) for (var qt = match, qndx = ndxInitializer.length > 0 ? ndxInitializer.shift() : 0; qndx < (isNaN(qt.quantifier.max) ? qndx + 1 : qt.quantifier.max) && testPos <= pos; qndx++) {
								var tokenGroup = maskToken.matches[$.inArray(qt, maskToken.matches) - 1];
								if (match = handleMatch(tokenGroup, [ qndx ].concat(loopNdx), tokenGroup)) {
									if (latestMatch = matches[matches.length - 1].match, latestMatch.optionalQuantifier = qndx > qt.quantifier.min - 1,
											isFirstMatch(latestMatch, tokenGroup)) {
										if (qndx > qt.quantifier.min - 1) {
											insertStop = !0, testPos = pos;
											break;
										}
										return !0;
									}
									return !0;
								}
							} else if (match = resolveTestFromToken(match, ndxInitializer, loopNdx, quantifierRecurse)) return !0;
						} else testPos++;
					}
					for (var tndx = ndxInitializer.length > 0 ? ndxInitializer.shift() : 0; tndx < maskToken.matches.length; tndx++) if (!0 !== maskToken.matches[tndx].isQuantifier) {
						var match = handleMatch(maskToken.matches[tndx], [ tndx ].concat(loopNdx), quantifierRecurse);
						if (match && testPos === pos) return match;
						if (testPos > pos) break;
					}
				}
				function filterTests(tests) {
					if (opts.keepStatic && pos > 0 && tests.length > 1 + ("" === tests[tests.length - 1].match.def ? 1 : 0) && !0 !== tests[0].match.optionality && !0 !== tests[0].match.optionalQuantifier && null === tests[0].match.fn && !/[0-9a-bA-Z]/.test(tests[0].match.def)) {
						if (getMaskSet().validPositions[pos - 1] === undefined) return [ determineTestTemplate(tests) ];
						if (getMaskSet().validPositions[pos - 1].alternation === tests[0].alternation) return [ determineTestTemplate(tests) ];
						if (getMaskSet().validPositions[pos - 1]) return [ determineTestTemplate(tests) ];
					}
					return tests;
				}
				var latestMatch, maskTokens = getMaskSet().maskToken, testPos = ndxIntlzr ? tstPs : 0, ndxInitializer = ndxIntlzr ? ndxIntlzr.slice() : [ 0 ], matches = [], insertStop = !1, cacheDependency = ndxIntlzr ? ndxIntlzr.join("") : "";
				if (pos > -1) {
					if (ndxIntlzr === undefined) {
						for (var test, previousPos = pos - 1; (test = getMaskSet().validPositions[previousPos] || getMaskSet().tests[previousPos]) === undefined && previousPos > -1; ) previousPos--;
						test !== undefined && previousPos > -1 && (ndxInitializer = function(tests) {
							var locator = [];
							return $.isArray(tests) || (tests = [ tests ]), tests.length > 0 && (tests[0].alternation === undefined ? (locator = determineTestTemplate(tests.slice()).locator.slice(),
							0 === locator.length && (locator = tests[0].locator.slice())) : $.each(tests, function(ndx, tst) {
								if ("" !== tst.def) if (0 === locator.length) locator = tst.locator.slice(); else for (var i = 0; i < locator.length; i++) tst.locator[i] && -1 === locator[i].toString().indexOf(tst.locator[i]) && (locator[i] += "," + tst.locator[i]);
							})), locator;
						}(test), cacheDependency = ndxInitializer.join(""), testPos = previousPos);
					}
					if (getMaskSet().tests[pos] && getMaskSet().tests[pos][0].cd === cacheDependency) return filterTests(getMaskSet().tests[pos]);
					for (var mtndx = ndxInitializer.shift(); mtndx < maskTokens.length; mtndx++) {
						if (resolveTestFromToken(maskTokens[mtndx], ndxInitializer, [ mtndx ]) && testPos === pos || testPos > pos) break;
					}
				}
				return (0 === matches.length || insertStop) && matches.push({
					match: {
						fn: null,
						cardinality: 0,
						optionality: !0,
						casing: null,
						def: "",
						placeholder: ""
					},
					locator: [],
					cd: cacheDependency
				}), ndxIntlzr !== undefined && getMaskSet().tests[pos] ? filterTests($.extend(!0, [], matches)) : (getMaskSet().tests[pos] = $.extend(!0, [], matches),
					filterTests(getMaskSet().tests[pos]));
			}
			function getBufferTemplate() {
				return getMaskSet()._buffer === undefined && (getMaskSet()._buffer = getMaskTemplate(!1, 1),
				getMaskSet().buffer === undefined && (getMaskSet().buffer = getMaskSet()._buffer.slice())),
					getMaskSet()._buffer;
			}
			function getBuffer(noCache) {
				return getMaskSet().buffer !== undefined && !0 !== noCache || (getMaskSet().buffer = getMaskTemplate(!0, getLastValidPosition(), !0)),
					getMaskSet().buffer;
			}
			function refreshFromBuffer(start, end, buffer) {
				var i, p;
				if (!0 === start) resetMaskSet(), start = 0, end = buffer.length; else for (i = start; i < end; i++) delete getMaskSet().validPositions[i];
				for (p = start, i = start; i < end; i++) if (resetMaskSet(!0), buffer[i] !== opts.skipOptionalPartCharacter) {
					var valResult = isValid(p, buffer[i], !0, !0);
					!1 !== valResult && (resetMaskSet(!0), p = valResult.caret !== undefined ? valResult.caret : valResult.pos + 1);
				}
			}
			function casing(elem, test, pos) {
				switch (opts.casing || test.casing) {
					case "upper":
						elem = elem.toUpperCase();
						break;

					case "lower":
						elem = elem.toLowerCase();
						break;

					case "title":
						var posBefore = getMaskSet().validPositions[pos - 1];
						elem = 0 === pos || posBefore && posBefore.input === String.fromCharCode(Inputmask.keyCode.SPACE) ? elem.toUpperCase() : elem.toLowerCase();
						break;

					default:
						if ($.isFunction(opts.casing)) {
							var args = Array.prototype.slice.call(arguments);
							args.push(getMaskSet().validPositions), elem = opts.casing.apply(this, args);
						}
				}
				return elem;
			}
			function checkAlternationMatch(altArr1, altArr2, na) {
				for (var naNdx, altArrC = opts.greedy ? altArr2 : altArr2.slice(0, 1), isMatch = !1, naArr = na !== undefined ? na.split(",") : [], i = 0; i < naArr.length; i++) -1 !== (naNdx = altArr1.indexOf(naArr[i])) && altArr1.splice(naNdx, 1);
				for (var alndx = 0; alndx < altArr1.length; alndx++) if (-1 !== $.inArray(altArr1[alndx], altArrC)) {
					isMatch = !0;
					break;
				}
				return isMatch;
			}
			function isValid(pos, c, strict, fromSetValid, fromAlternate, validateOnly) {
				function isSelection(posObj) {
					var selection = isRTL ? posObj.begin - posObj.end > 1 || posObj.begin - posObj.end == 1 : posObj.end - posObj.begin > 1 || posObj.end - posObj.begin == 1;
					return selection && 0 === posObj.begin && posObj.end === getMaskSet().maskLength ? "full" : selection;
				}
				function _isValid(position, c, strict) {
					var rslt = !1;
					return $.each(getTests(position), function(ndx, tst) {
						for (var test = tst.match, loopend = c ? 1 : 0, chrs = "", i = test.cardinality; i > loopend; i--) chrs += getBufferElement(position - (i - 1));
						if (c && (chrs += c), getBuffer(!0), !1 !== (rslt = null != test.fn ? test.fn.test(chrs, getMaskSet(), position, strict, opts, isSelection(pos)) : (c === test.def || c === opts.skipOptionalPartCharacter) && "" !== test.def && {
								c: getPlaceholder(position, test, !0) || test.def,
								pos: position
							})) {
							var elem = rslt.c !== undefined ? rslt.c : c;
							elem = elem === opts.skipOptionalPartCharacter && null === test.fn ? getPlaceholder(position, test, !0) || test.def : elem;
							var validatedPos = position, possibleModifiedBuffer = getBuffer();
							if (rslt.remove !== undefined && ($.isArray(rslt.remove) || (rslt.remove = [ rslt.remove ]),
									$.each(rslt.remove.sort(function(a, b) {
										return b - a;
									}), function(ndx, lmnt) {
										stripValidPositions(lmnt, lmnt + 1, !0);
									})), rslt.insert !== undefined && ($.isArray(rslt.insert) || (rslt.insert = [ rslt.insert ]),
									$.each(rslt.insert.sort(function(a, b) {
										return a - b;
									}), function(ndx, lmnt) {
										isValid(lmnt.pos, lmnt.c, !0, fromSetValid);
									})), rslt.refreshFromBuffer) {
								var refresh = rslt.refreshFromBuffer;
								if (refreshFromBuffer(!0 === refresh ? refresh : refresh.start, refresh.end, possibleModifiedBuffer),
									rslt.pos === undefined && rslt.c === undefined) return rslt.pos = getLastValidPosition(),
									!1;
								if ((validatedPos = rslt.pos !== undefined ? rslt.pos : position) !== position) return rslt = $.extend(rslt, isValid(validatedPos, elem, !0, fromSetValid)),
									!1;
							} else if (!0 !== rslt && rslt.pos !== undefined && rslt.pos !== position && (validatedPos = rslt.pos,
									refreshFromBuffer(position, validatedPos, getBuffer().slice()), validatedPos !== position)) return rslt = $.extend(rslt, isValid(validatedPos, elem, !0)),
								!1;
							return (!0 === rslt || rslt.pos !== undefined || rslt.c !== undefined) && (ndx > 0 && resetMaskSet(!0),
								setValidPosition(validatedPos, $.extend({}, tst, {
									input: casing(elem, test, validatedPos)
								}), fromSetValid, isSelection(pos)) || (rslt = !1), !1);
						}
					}), rslt;
				}
				function setValidPosition(pos, validTest, fromSetValid, isSelection) {
					if (isSelection || opts.insertMode && getMaskSet().validPositions[pos] !== undefined && fromSetValid === undefined) {
						var i, positionsClone = $.extend(!0, {}, getMaskSet().validPositions), lvp = getLastValidPosition(undefined, !0);
						for (i = pos; i <= lvp; i++) delete getMaskSet().validPositions[i];
						getMaskSet().validPositions[pos] = $.extend(!0, {}, validTest);
						var j, valid = !0, vps = getMaskSet().validPositions, needsValidation = !1, initialLength = getMaskSet().maskLength;
						for (i = j = pos; i <= lvp; i++) {
							var t = positionsClone[i];
							if (t !== undefined) for (var posMatch = j; posMatch < getMaskSet().maskLength && (null === t.match.fn && vps[i] && (!0 === vps[i].match.optionalQuantifier || !0 === vps[i].match.optionality) || null != t.match.fn); ) {
								if (posMatch++, !1 === needsValidation && positionsClone[posMatch] && positionsClone[posMatch].match.def === t.match.def) getMaskSet().validPositions[posMatch] = $.extend(!0, {}, positionsClone[posMatch]),
									getMaskSet().validPositions[posMatch].input = t.input, fillMissingNonMask(posMatch),
									j = posMatch, valid = !0; else if (positionCanMatchDefinition(posMatch, t.match.def)) {
									var result = isValid(posMatch, t.input, !0, !0);
									valid = !1 !== result, j = result.caret || result.insert ? getLastValidPosition() : posMatch,
										needsValidation = !0;
								} else if (!(valid = !0 === t.generatedInput) && posMatch >= getMaskSet().maskLength - 1) break;
								if (getMaskSet().maskLength < initialLength && (getMaskSet().maskLength = initialLength),
										valid) break;
							}
							if (!valid) break;
						}
						if (!valid) return getMaskSet().validPositions = $.extend(!0, {}, positionsClone),
							resetMaskSet(!0), !1;
					} else getMaskSet().validPositions[pos] = $.extend(!0, {}, validTest);
					return resetMaskSet(!0), !0;
				}
				function fillMissingNonMask(maskPos) {
					for (var pndx = maskPos - 1; pndx > -1 && !getMaskSet().validPositions[pndx]; pndx--) ;
					var testTemplate, testsFromPos;
					for (pndx++; pndx < maskPos; pndx++) getMaskSet().validPositions[pndx] === undefined && (!1 === opts.jitMasking || opts.jitMasking > pndx) && (testsFromPos = getTests(pndx, getTestTemplate(pndx - 1).locator, pndx - 1).slice(),
					"" === testsFromPos[testsFromPos.length - 1].match.def && testsFromPos.pop(), (testTemplate = determineTestTemplate(testsFromPos)) && (testTemplate.match.def === opts.radixPointDefinitionSymbol || !isMask(pndx, !0) || $.inArray(opts.radixPoint, getBuffer()) < pndx && testTemplate.match.fn && testTemplate.match.fn.test(getPlaceholder(pndx), getMaskSet(), pndx, !1, opts)) && !1 !== (result = _isValid(pndx, getPlaceholder(pndx, testTemplate.match, !0) || (null == testTemplate.match.fn ? testTemplate.match.def : "" !== getPlaceholder(pndx) ? getPlaceholder(pndx) : getBuffer()[pndx]), !0)) && (getMaskSet().validPositions[result.pos || pndx].generatedInput = !0));
				}
				strict = !0 === strict;
				var maskPos = pos;
				pos.begin !== undefined && (maskPos = isRTL && !isSelection(pos) ? pos.end : pos.begin);
				var result = !0, positionsClone = $.extend(!0, {}, getMaskSet().validPositions);
				if ($.isFunction(opts.preValidation) && !strict && !0 !== fromSetValid && !0 !== validateOnly && (result = opts.preValidation(getBuffer(), maskPos, c, isSelection(pos), opts)),
					!0 === result) {
					if (fillMissingNonMask(maskPos), isSelection(pos) && (handleRemove(undefined, Inputmask.keyCode.DELETE, pos, !0, !0),
							maskPos = getMaskSet().p), maskPos < getMaskSet().maskLength && (maxLength === undefined || maskPos < maxLength) && (result = _isValid(maskPos, c, strict),
						(!strict || !0 === fromSetValid) && !1 === result && !0 !== validateOnly)) {
						var currentPosValid = getMaskSet().validPositions[maskPos];
						if (!currentPosValid || null !== currentPosValid.match.fn || currentPosValid.match.def !== c && c !== opts.skipOptionalPartCharacter) {
							if ((opts.insertMode || getMaskSet().validPositions[seekNext(maskPos)] === undefined) && !isMask(maskPos, !0)) for (var nPos = maskPos + 1, snPos = seekNext(maskPos); nPos <= snPos; nPos++) if (!1 !== (result = _isValid(nPos, c, strict))) {
								!function(originalPos, newPos) {
									var vp = getMaskSet().validPositions[newPos];
									if (vp) for (var targetLocator = vp.locator, tll = targetLocator.length, ps = originalPos; ps < newPos; ps++) if (getMaskSet().validPositions[ps] === undefined && !isMask(ps, !0)) {
										var tests = getTests(ps).slice(), bestMatch = determineTestTemplate(tests, !0), equality = -1;
										"" === tests[tests.length - 1].match.def && tests.pop(), $.each(tests, function(ndx, tst) {
											for (var i = 0; i < tll; i++) {
												if (tst.locator[i] === undefined || !checkAlternationMatch(tst.locator[i].toString().split(","), targetLocator[i].toString().split(","), tst.na)) {
													var targetAI = targetLocator[i], bestMatchAI = bestMatch.locator[i], tstAI = tst.locator[i];
													targetAI - bestMatchAI > Math.abs(targetAI - tstAI) && (bestMatch = tst);
													break;
												}
												equality < i && (equality = i, bestMatch = tst);
											}
										}), bestMatch = $.extend({}, bestMatch, {
											input: getPlaceholder(ps, bestMatch.match, !0) || bestMatch.match.def
										}), bestMatch.generatedInput = !0, setValidPosition(ps, bestMatch, !0), getMaskSet().validPositions[newPos] = undefined,
											_isValid(newPos, vp.input, !0);
									}
								}(maskPos, result.pos !== undefined ? result.pos : nPos), maskPos = nPos;
								break;
							}
						} else result = {
							caret: seekNext(maskPos)
						};
					}
					!1 === result && opts.keepStatic && !strict && !0 !== fromAlternate && (result = function(pos, c, strict) {
						var lastAlt, alternation, altPos, prevAltPos, i, validPos, altNdxs, decisionPos, validPsClone = $.extend(!0, {}, getMaskSet().validPositions), isValidRslt = !1, lAltPos = getLastValidPosition();
						for (prevAltPos = getMaskSet().validPositions[lAltPos]; lAltPos >= 0; lAltPos--) if ((altPos = getMaskSet().validPositions[lAltPos]) && altPos.alternation !== undefined) {
							if (lastAlt = lAltPos, alternation = getMaskSet().validPositions[lastAlt].alternation,
								prevAltPos.locator[altPos.alternation] !== altPos.locator[altPos.alternation]) break;
							prevAltPos = altPos;
						}
						if (alternation !== undefined) {
							decisionPos = parseInt(lastAlt);
							var decisionTaker = prevAltPos.locator[prevAltPos.alternation || alternation] !== undefined ? prevAltPos.locator[prevAltPos.alternation || alternation] : altNdxs[0];
							decisionTaker.length > 0 && (decisionTaker = decisionTaker.split(",")[0]);
							var possibilityPos = getMaskSet().validPositions[decisionPos], prevPos = getMaskSet().validPositions[decisionPos - 1];
							$.each(getTests(decisionPos, prevPos ? prevPos.locator : undefined, decisionPos - 1), function(ndx, test) {
								altNdxs = test.locator[alternation] ? test.locator[alternation].toString().split(",") : [];
								for (var mndx = 0; mndx < altNdxs.length; mndx++) {
									var validInputs = [], staticInputsBeforePos = 0, staticInputsBeforePosAlternate = 0, verifyValidInput = !1;
									if (decisionTaker < altNdxs[mndx] && (test.na === undefined || -1 === $.inArray(altNdxs[mndx], test.na.split(",")) || -1 === $.inArray(decisionTaker.toString(), altNdxs))) {
										getMaskSet().validPositions[decisionPos] = $.extend(!0, {}, test);
										var possibilities = getMaskSet().validPositions[decisionPos].locator;
										for (getMaskSet().validPositions[decisionPos].locator[alternation] = parseInt(altNdxs[mndx]),
												 null == test.match.fn ? (possibilityPos.input !== test.match.def && (verifyValidInput = !0,
												 !0 !== possibilityPos.generatedInput && validInputs.push(possibilityPos.input)),
													 staticInputsBeforePosAlternate++, getMaskSet().validPositions[decisionPos].generatedInput = !/[0-9a-bA-Z]/.test(test.match.def),
													 getMaskSet().validPositions[decisionPos].input = test.match.def) : getMaskSet().validPositions[decisionPos].input = possibilityPos.input,
												 i = decisionPos + 1; i < getLastValidPosition(undefined, !0) + 1; i++) validPos = getMaskSet().validPositions[i],
											validPos && !0 !== validPos.generatedInput && /[0-9a-bA-Z]/.test(validPos.input) ? validInputs.push(validPos.input) : i < pos && staticInputsBeforePos++,
											delete getMaskSet().validPositions[i];
										for (verifyValidInput && validInputs[0] === test.match.def && validInputs.shift(),
												 resetMaskSet(!0), isValidRslt = !0; validInputs.length > 0; ) {
											var input = validInputs.shift();
											if (input !== opts.skipOptionalPartCharacter && !(isValidRslt = isValid(getLastValidPosition(undefined, !0) + 1, input, !1, fromSetValid, !0))) break;
										}
										if (isValidRslt) {
											getMaskSet().validPositions[decisionPos].locator = possibilities;
											var targetLvp = getLastValidPosition(pos) + 1;
											for (i = decisionPos + 1; i < getLastValidPosition() + 1; i++) ((validPos = getMaskSet().validPositions[i]) === undefined || null == validPos.match.fn) && i < pos + (staticInputsBeforePosAlternate - staticInputsBeforePos) && staticInputsBeforePosAlternate++;
											pos += staticInputsBeforePosAlternate - staticInputsBeforePos, isValidRslt = isValid(pos > targetLvp ? targetLvp : pos, c, strict, fromSetValid, !0);
										}
										if (isValidRslt) return !1;
										resetMaskSet(), getMaskSet().validPositions = $.extend(!0, {}, validPsClone);
									}
								}
							});
						}
						return isValidRslt;
					}(maskPos, c, strict)), !0 === result && (result = {
						pos: maskPos
					});
				}
				if ($.isFunction(opts.postValidation) && !1 !== result && !strict && !0 !== fromSetValid && !0 !== validateOnly) {
					var postResult = opts.postValidation(getBuffer(!0), result, opts);
					if (postResult.refreshFromBuffer && postResult.buffer) {
						var refresh = postResult.refreshFromBuffer;
						refreshFromBuffer(!0 === refresh ? refresh : refresh.start, refresh.end, postResult.buffer);
					}
					result = !0 === postResult ? result : postResult;
				}
				return result && result.pos === undefined && (result.pos = maskPos), !1 !== result && !0 !== validateOnly || (resetMaskSet(!0),
					getMaskSet().validPositions = $.extend(!0, {}, positionsClone)), result;
			}
			function isMask(pos, strict) {
				var test = getTestTemplate(pos).match;
				if ("" === test.def && (test = getTest(pos).match), null != test.fn) return test.fn;
				if (!0 !== strict && pos > -1) {
					var tests = getTests(pos);
					return tests.length > 1 + ("" === tests[tests.length - 1].match.def ? 1 : 0);
				}
				return !1;
			}
			function seekNext(pos, newBlock) {
				var maskL = getMaskSet().maskLength;
				if (pos >= maskL) return maskL;
				var position = pos;
				for (getTests(maskL + 1).length > 1 && (getMaskTemplate(!0, maskL + 1, !0), maskL = getMaskSet().maskLength); ++position < maskL && (!0 === newBlock && (!0 !== getTest(position).match.newBlockMarker || !isMask(position)) || !0 !== newBlock && !isMask(position)); ) ;
				return position;
			}
			function seekPrevious(pos, newBlock) {
				var tests, position = pos;
				if (position <= 0) return 0;
				for (;--position > 0 && (!0 === newBlock && !0 !== getTest(position).match.newBlockMarker || !0 !== newBlock && !isMask(position) && (tests = getTests(position),
				tests.length < 2 || 2 === tests.length && "" === tests[1].match.def)); ) ;
				return position;
			}
			function getBufferElement(position) {
				return getMaskSet().validPositions[position] === undefined ? getPlaceholder(position) : getMaskSet().validPositions[position].input;
			}
			function writeBuffer(input, buffer, caretPos, event, triggerInputEvent) {
				if (event && $.isFunction(opts.onBeforeWrite)) {
					var result = opts.onBeforeWrite.call(inputmask, event, buffer, caretPos, opts);
					if (result) {
						if (result.refreshFromBuffer) {
							var refresh = result.refreshFromBuffer;
							refreshFromBuffer(!0 === refresh ? refresh : refresh.start, refresh.end, result.buffer || buffer),
								buffer = getBuffer(!0);
						}
						caretPos !== undefined && (caretPos = result.caret !== undefined ? result.caret : caretPos);
					}
				}
				input !== undefined && (input.inputmask._valueSet(buffer.join("")), caretPos === undefined || event !== undefined && "blur" === event.type ? renderColorMask(input, caretPos, 0 === buffer.length) : android && event && "input" === event.type ? setTimeout(function() {
					caret(input, caretPos);
				}, 0) : caret(input, caretPos), !0 === triggerInputEvent && (skipInputEvent = !0,
					$(input).trigger("input")));
			}
			function getPlaceholder(pos, test, returnPL) {
				if (test = test || getTest(pos).match, test.placeholder !== undefined || !0 === returnPL) return $.isFunction(test.placeholder) ? test.placeholder(opts) : test.placeholder;
				if (null === test.fn) {
					if (pos > -1 && getMaskSet().validPositions[pos] === undefined) {
						var prevTest, tests = getTests(pos), staticAlternations = [];
						if (tests.length > 1 + ("" === tests[tests.length - 1].match.def ? 1 : 0)) for (var i = 0; i < tests.length; i++) if (!0 !== tests[i].match.optionality && !0 !== tests[i].match.optionalQuantifier && (null === tests[i].match.fn || prevTest === undefined || !1 !== tests[i].match.fn.test(prevTest.match.def, getMaskSet(), pos, !0, opts)) && (staticAlternations.push(tests[i]),
							null === tests[i].match.fn && (prevTest = tests[i]), staticAlternations.length > 1 && /[0-9a-bA-Z]/.test(staticAlternations[0].match.def))) return opts.placeholder.charAt(pos % opts.placeholder.length);
					}
					return test.def;
				}
				return opts.placeholder.charAt(pos % opts.placeholder.length);
			}
			function checkVal(input, writeOut, strict, nptvl, initiatingEvent) {
				function isTemplateMatch(ndx, charCodes) {
					return -1 !== getBufferTemplate().slice(ndx, seekNext(ndx)).join("").indexOf(charCodes) && !isMask(ndx) && getTest(ndx).match.nativeDef === charCodes.charAt(charCodes.length - 1);
				}
				var inputValue = nptvl.slice(), charCodes = "", initialNdx = -1, result = undefined;
				if (resetMaskSet(), strict || !0 === opts.autoUnmask) initialNdx = seekNext(initialNdx); else {
					var staticInput = getBufferTemplate().slice(0, seekNext(-1)).join(""), matches = inputValue.join("").match(new RegExp("^" + Inputmask.escapeRegex(staticInput), "g"));
					matches && matches.length > 0 && (inputValue.splice(0, matches.length * staticInput.length),
						initialNdx = seekNext(initialNdx));
				}
				if (-1 === initialNdx ? (getMaskSet().p = seekNext(initialNdx), initialNdx = 0) : getMaskSet().p = initialNdx,
						$.each(inputValue, function(ndx, charCode) {
							if (charCode !== undefined) if (getMaskSet().validPositions[ndx] === undefined && inputValue[ndx] === getPlaceholder(ndx) && isMask(ndx, !0) && !1 === isValid(ndx, inputValue[ndx], !0, undefined, undefined, !0)) getMaskSet().p++; else {
								var keypress = new $.Event("_checkval");
								keypress.which = charCode.charCodeAt(0), charCodes += charCode;
								var lvp = getLastValidPosition(undefined, !0), lvTest = getMaskSet().validPositions[lvp], nextTest = getTestTemplate(lvp + 1, lvTest ? lvTest.locator.slice() : undefined, lvp);
								if (!isTemplateMatch(initialNdx, charCodes) || strict || opts.autoUnmask) {
									var pos = strict ? ndx : null == nextTest.match.fn && nextTest.match.optionality && lvp + 1 < getMaskSet().p ? lvp + 1 : getMaskSet().p;
									result = EventHandlers.keypressEvent.call(input, keypress, !0, !1, strict, pos),
										initialNdx = pos + 1, charCodes = "";
								} else result = EventHandlers.keypressEvent.call(input, keypress, !0, !1, !0, lvp + 1);
								if (!1 !== result && !strict && $.isFunction(opts.onBeforeWrite)) {
									var origResult = result;
									if (result = opts.onBeforeWrite.call(inputmask, keypress, getBuffer(), result.forwardPosition, opts),
										(result = $.extend(origResult, result)) && result.refreshFromBuffer) {
										var refresh = result.refreshFromBuffer;
										refreshFromBuffer(!0 === refresh ? refresh : refresh.start, refresh.end, result.buffer),
											resetMaskSet(!0), result.caret && (getMaskSet().p = result.caret, result.forwardPosition = result.caret);
									}
								}
							}
						}), writeOut) {
					var caretPos = undefined;
					document.activeElement === input && result && (caretPos = opts.numericInput ? seekPrevious(result.forwardPosition) : result.forwardPosition),
						writeBuffer(input, getBuffer(), caretPos, initiatingEvent || new $.Event("checkval"), initiatingEvent && "input" === initiatingEvent.type);
				}
			}
			function unmaskedvalue(input) {
				if (input) {
					if (input.inputmask === undefined) return input.value;
					input.inputmask && input.inputmask.refreshValue && EventHandlers.setValueEvent.call(input);
				}
				var umValue = [], vps = getMaskSet().validPositions;
				for (var pndx in vps) vps[pndx].match && null != vps[pndx].match.fn && umValue.push(vps[pndx].input);
				var unmaskedValue = 0 === umValue.length ? "" : (isRTL ? umValue.reverse() : umValue).join("");
				if ($.isFunction(opts.onUnMask)) {
					var bufferValue = (isRTL ? getBuffer().slice().reverse() : getBuffer()).join("");
					unmaskedValue = opts.onUnMask.call(inputmask, bufferValue, unmaskedValue, opts);
				}
				return unmaskedValue;
			}
			function caret(input, begin, end, notranslate) {
				function translatePosition(pos) {
					if (!0 !== notranslate && isRTL && "number" == typeof pos && (!opts.greedy || "" !== opts.placeholder)) {
						pos = getBuffer().join("").length - pos;
					}
					return pos;
				}
				var range;
				if (begin === undefined) return input.setSelectionRange ? (begin = input.selectionStart,
					end = input.selectionEnd) : window.getSelection ? (range = window.getSelection().getRangeAt(0),
				range.commonAncestorContainer.parentNode !== input && range.commonAncestorContainer !== input || (begin = range.startOffset,
					end = range.endOffset)) : document.selection && document.selection.createRange && (range = document.selection.createRange(),
					begin = 0 - range.duplicate().moveStart("character", -input.inputmask._valueGet().length),
					end = begin + range.text.length), {
					begin: translatePosition(begin),
					end: translatePosition(end)
				};
				if (begin.begin !== undefined && (end = begin.end, begin = begin.begin), "number" == typeof begin) {
					begin = translatePosition(begin), end = translatePosition(end), end = "number" == typeof end ? end : begin;
					var scrollCalc = parseInt(((input.ownerDocument.defaultView || window).getComputedStyle ? (input.ownerDocument.defaultView || window).getComputedStyle(input, null) : input.currentStyle).fontSize) * end;
					if (input.scrollLeft = scrollCalc > input.scrollWidth ? scrollCalc : 0, mobile || !1 !== opts.insertMode || begin !== end || end++,
							input.setSelectionRange) input.selectionStart = begin, input.selectionEnd = end; else if (window.getSelection) {
						if (range = document.createRange(), input.firstChild === undefined || null === input.firstChild) {
							var textNode = document.createTextNode("");
							input.appendChild(textNode);
						}
						range.setStart(input.firstChild, begin < input.inputmask._valueGet().length ? begin : input.inputmask._valueGet().length),
							range.setEnd(input.firstChild, end < input.inputmask._valueGet().length ? end : input.inputmask._valueGet().length),
							range.collapse(!0);
						var sel = window.getSelection();
						sel.removeAllRanges(), sel.addRange(range);
					} else input.createTextRange && (range = input.createTextRange(), range.collapse(!0),
						range.moveEnd("character", end), range.moveStart("character", begin), range.select());
					renderColorMask(input, {
						begin: begin,
						end: end
					});
				}
			}
			function determineLastRequiredPosition(returnDefinition) {
				var pos, testPos, buffer = getBuffer(), bl = buffer.length, lvp = getLastValidPosition(), positions = {}, lvTest = getMaskSet().validPositions[lvp], ndxIntlzr = lvTest !== undefined ? lvTest.locator.slice() : undefined;
				for (pos = lvp + 1; pos < buffer.length; pos++) testPos = getTestTemplate(pos, ndxIntlzr, pos - 1),
					ndxIntlzr = testPos.locator.slice(), positions[pos] = $.extend(!0, {}, testPos);
				var lvTestAlt = lvTest && lvTest.alternation !== undefined ? lvTest.locator[lvTest.alternation] : undefined;
				for (pos = bl - 1; pos > lvp && (testPos = positions[pos], (testPos.match.optionality || testPos.match.optionalQuantifier && testPos.match.newBlockMarker || lvTestAlt && (lvTestAlt !== positions[pos].locator[lvTest.alternation] && null != testPos.match.fn || null === testPos.match.fn && testPos.locator[lvTest.alternation] && checkAlternationMatch(testPos.locator[lvTest.alternation].toString().split(","), lvTestAlt.toString().split(",")) && "" !== getTests(pos)[0].def)) && buffer[pos] === getPlaceholder(pos, testPos.match)); pos--) bl--;
				return returnDefinition ? {
					l: bl,
					def: positions[bl] ? positions[bl].match : undefined
				} : bl;
			}
			function clearOptionalTail(buffer) {
				for (var validPos, rl = determineLastRequiredPosition(), bl = buffer.length, lv = getMaskSet().validPositions[getLastValidPosition()]; rl < bl && !isMask(rl, !0) && (validPos = lv !== undefined ? getTestTemplate(rl, lv.locator.slice(""), lv) : getTest(rl)) && !0 !== validPos.match.optionality && (!0 !== validPos.match.optionalQuantifier && !0 !== validPos.match.newBlockMarker || rl + 1 === bl && "" === (lv !== undefined ? getTestTemplate(rl + 1, lv.locator.slice(""), lv) : getTest(rl + 1)).match.def); ) rl++;
				for (;(validPos = getMaskSet().validPositions[rl - 1]) && validPos && validPos.match.optionality && validPos.input === opts.skipOptionalPartCharacter; ) rl--;
				return buffer.splice(rl), buffer;
			}
			function isComplete(buffer) {
				if ($.isFunction(opts.isComplete)) return opts.isComplete(buffer, opts);
				if ("*" === opts.repeat) return undefined;
				var complete = !1, lrp = determineLastRequiredPosition(!0), aml = seekPrevious(lrp.l);
				if (lrp.def === undefined || lrp.def.newBlockMarker || lrp.def.optionality || lrp.def.optionalQuantifier) {
					complete = !0;
					for (var i = 0; i <= aml; i++) {
						var test = getTestTemplate(i).match;
						if (null !== test.fn && getMaskSet().validPositions[i] === undefined && !0 !== test.optionality && !0 !== test.optionalQuantifier || null === test.fn && buffer[i] !== getPlaceholder(i, test)) {
							complete = !1;
							break;
						}
					}
				}
				return complete;
			}
			function handleRemove(input, k, pos, strict, fromIsValid) {
				if ((opts.numericInput || isRTL) && (k === Inputmask.keyCode.BACKSPACE ? k = Inputmask.keyCode.DELETE : k === Inputmask.keyCode.DELETE && (k = Inputmask.keyCode.BACKSPACE),
						isRTL)) {
					var pend = pos.end;
					pos.end = pos.begin, pos.begin = pend;
				}
				k === Inputmask.keyCode.BACKSPACE && (pos.end - pos.begin < 1 || !1 === opts.insertMode) ? (pos.begin = seekPrevious(pos.begin),
				getMaskSet().validPositions[pos.begin] !== undefined && getMaskSet().validPositions[pos.begin].input === opts.groupSeparator && pos.begin--) : k === Inputmask.keyCode.DELETE && pos.begin === pos.end && (pos.end = isMask(pos.end, !0) && getMaskSet().validPositions[pos.end] && getMaskSet().validPositions[pos.end].input !== opts.radixPoint ? pos.end + 1 : seekNext(pos.end) + 1,
				getMaskSet().validPositions[pos.begin] !== undefined && getMaskSet().validPositions[pos.begin].input === opts.groupSeparator && pos.end++),
					stripValidPositions(pos.begin, pos.end, !1, strict), !0 !== strict && function() {
					if (opts.keepStatic) {
						for (var validInputs = [], lastAlt = getLastValidPosition(-1, !0), positionsClone = $.extend(!0, {}, getMaskSet().validPositions), prevAltPos = getMaskSet().validPositions[lastAlt]; lastAlt >= 0; lastAlt--) {
							var altPos = getMaskSet().validPositions[lastAlt];
							if (altPos) {
								if (!0 !== altPos.generatedInput && /[0-9a-bA-Z]/.test(altPos.input) && validInputs.push(altPos.input),
										delete getMaskSet().validPositions[lastAlt], altPos.alternation !== undefined && altPos.locator[altPos.alternation] !== prevAltPos.locator[altPos.alternation]) break;
								prevAltPos = altPos;
							}
						}
						if (lastAlt > -1) for (getMaskSet().p = seekNext(getLastValidPosition(-1, !0)); validInputs.length > 0; ) {
							var keypress = new $.Event("keypress");
							keypress.which = validInputs.pop().charCodeAt(0), EventHandlers.keypressEvent.call(input, keypress, !0, !1, !1, getMaskSet().p);
						} else getMaskSet().validPositions = $.extend(!0, {}, positionsClone);
					}
				}();
				var lvp = getLastValidPosition(pos.begin, !0);
				if (lvp < pos.begin) getMaskSet().p = seekNext(lvp); else if (!0 !== strict && (getMaskSet().p = pos.begin,
					!0 !== fromIsValid)) for (;getMaskSet().p < lvp && getMaskSet().validPositions[getMaskSet().p] === undefined; ) getMaskSet().p++;
			}
			function initializeColorMask(input) {
				function findCaretPos(clientx) {
					var caretPos, e = document.createElement("span");
					for (var style in computedStyle) isNaN(style) && -1 !== style.indexOf("font") && (e.style[style] = computedStyle[style]);
					e.style.textTransform = computedStyle.textTransform, e.style.letterSpacing = computedStyle.letterSpacing,
						e.style.position = "absolute", e.style.height = "auto", e.style.width = "auto",
						e.style.visibility = "hidden", e.style.whiteSpace = "nowrap", document.body.appendChild(e);
					var itl, inputText = input.inputmask._valueGet(), previousWidth = 0;
					for (caretPos = 0, itl = inputText.length; caretPos <= itl; caretPos++) {
						if (e.innerHTML += inputText.charAt(caretPos) || "_", e.offsetWidth >= clientx) {
							var offset1 = clientx - previousWidth, offset2 = e.offsetWidth - clientx;
							e.innerHTML = inputText.charAt(caretPos), offset1 -= e.offsetWidth / 3, caretPos = offset1 < offset2 ? caretPos - 1 : caretPos;
							break;
						}
						previousWidth = e.offsetWidth;
					}
					return document.body.removeChild(e), caretPos;
				}
				var computedStyle = (input.ownerDocument.defaultView || window).getComputedStyle(input, null), template = document.createElement("div");
				template.style.width = computedStyle.width, template.style.textAlign = computedStyle.textAlign,
					colorMask = document.createElement("div"), colorMask.className = "im-colormask",
					input.parentNode.insertBefore(colorMask, input), input.parentNode.removeChild(input),
					colorMask.appendChild(template), colorMask.appendChild(input), input.style.left = template.offsetLeft + "px",
					$(input).on("click", function(e) {
						return caret(input, findCaretPos(e.clientX)), EventHandlers.clickEvent.call(input, [ e ]);
					}), $(input).on("keydown", function(e) {
					e.shiftKey || !1 === opts.insertMode || setTimeout(function() {
						renderColorMask(input);
					}, 0);
				});
			}
			function renderColorMask(input, caretPos, clear) {
				function handleStatic() {
					isStatic || null !== test.fn && testPos.input !== undefined ? isStatic && (null !== test.fn && testPos.input !== undefined || "" === test.def) && (isStatic = !1,
						maskTemplate += "</span>") : (isStatic = !0, maskTemplate += "<span class='im-static'>");
				}
				function handleCaret(force) {
					!0 !== force && pos !== caretPos.begin || document.activeElement !== input || (maskTemplate += "<span class='im-caret' style='border-right-width: 1px;border-right-style: solid;'></span>");
				}
				var test, testPos, ndxIntlzr, maskTemplate = "", isStatic = !1, pos = 0;
				if (colorMask !== undefined) {
					var buffer = getBuffer();
					if (caretPos === undefined ? caretPos = caret(input) : caretPos.begin === undefined && (caretPos = {
							begin: caretPos,
							end: caretPos
						}), !0 !== clear) {
						var lvp = getLastValidPosition();
						do {
							handleCaret(), getMaskSet().validPositions[pos] ? (testPos = getMaskSet().validPositions[pos],
								test = testPos.match, ndxIntlzr = testPos.locator.slice(), handleStatic(), maskTemplate += buffer[pos]) : (testPos = getTestTemplate(pos, ndxIntlzr, pos - 1),
								test = testPos.match, ndxIntlzr = testPos.locator.slice(), (!1 === opts.jitMasking || pos < lvp || "number" == typeof opts.jitMasking && isFinite(opts.jitMasking) && opts.jitMasking > pos) && (handleStatic(),
								maskTemplate += getPlaceholder(pos, test))), pos++;
						} while ((maxLength === undefined || pos < maxLength) && (null !== test.fn || "" !== test.def) || lvp > pos || isStatic);
						-1 === maskTemplate.indexOf("im-caret") && handleCaret(!0), isStatic && handleStatic();
					}
					var template = colorMask.getElementsByTagName("div")[0];
					template.innerHTML = maskTemplate, input.inputmask.positionColorMask(input, template);
				}
			}
			maskset = maskset || this.maskset, opts = opts || this.opts;
			var undoValue, $el, maxLength, colorMask, inputmask = this, el = this.el, isRTL = this.isRTL, skipKeyPressEvent = !1, skipInputEvent = !1, ignorable = !1, mouseEnter = !1, EventRuler = {
				on: function(input, eventName, eventHandler) {
					var ev = function(e) {
						if (this.inputmask === undefined && "FORM" !== this.nodeName) {
							var imOpts = $.data(this, "_inputmask_opts");
							imOpts ? new Inputmask(imOpts).mask(this) : EventRuler.off(this);
						} else {
							if ("setvalue" === e.type || "FORM" === this.nodeName || !(this.disabled || this.readOnly && !("keydown" === e.type && e.ctrlKey && 67 === e.keyCode || !1 === opts.tabThrough && e.keyCode === Inputmask.keyCode.TAB))) {
								switch (e.type) {
									case "input":
										if (!0 === skipInputEvent) return skipInputEvent = !1, e.preventDefault();
										break;

									case "keydown":
										skipKeyPressEvent = !1, skipInputEvent = !1;
										break;

									case "keypress":
										if (!0 === skipKeyPressEvent) return e.preventDefault();
										skipKeyPressEvent = !0;
										break;

									case "click":
										if (iemobile || iphone) {
											var that = this, args = arguments;
											return setTimeout(function() {
												eventHandler.apply(that, args);
											}, 0), !1;
										}
								}
								var returnVal = eventHandler.apply(this, arguments);
								return !1 === returnVal && (e.preventDefault(), e.stopPropagation()), returnVal;
							}
							e.preventDefault();
						}
					};
					input.inputmask.events[eventName] = input.inputmask.events[eventName] || [], input.inputmask.events[eventName].push(ev),
						-1 !== $.inArray(eventName, [ "submit", "reset" ]) ? null !== input.form && $(input.form).on(eventName, ev) : $(input).on(eventName, ev);
				},
				off: function(input, event) {
					if (input.inputmask && input.inputmask.events) {
						var events;
						event ? (events = [], events[event] = input.inputmask.events[event]) : events = input.inputmask.events,
							$.each(events, function(eventName, evArr) {
								for (;evArr.length > 0; ) {
									var ev = evArr.pop();
									-1 !== $.inArray(eventName, [ "submit", "reset" ]) ? null !== input.form && $(input.form).off(eventName, ev) : $(input).off(eventName, ev);
								}
								delete input.inputmask.events[eventName];
							});
					}
				}
			}, EventHandlers = {
				keydownEvent: function(e) {
					var input = this, $input = $(input), k = e.keyCode, pos = caret(input);
					if (k === Inputmask.keyCode.BACKSPACE || k === Inputmask.keyCode.DELETE || iphone && k === Inputmask.keyCode.BACKSPACE_SAFARI || e.ctrlKey && k === Inputmask.keyCode.X && !function(eventName) {
							var el = document.createElement("input"), evName = "on" + eventName, isSupported = evName in el;
							return isSupported || (el.setAttribute(evName, "return;"), isSupported = "function" == typeof el[evName]),
								el = null, isSupported;
						}("cut")) e.preventDefault(), handleRemove(input, k, pos), writeBuffer(input, getBuffer(!0), getMaskSet().p, e, input.inputmask._valueGet() !== getBuffer().join("")),
						input.inputmask._valueGet() === getBufferTemplate().join("") ? $input.trigger("cleared") : !0 === isComplete(getBuffer()) && $input.trigger("complete"); else if (k === Inputmask.keyCode.END || k === Inputmask.keyCode.PAGE_DOWN) {
						e.preventDefault();
						var caretPos = seekNext(getLastValidPosition());
						opts.insertMode || caretPos !== getMaskSet().maskLength || e.shiftKey || caretPos--,
							caret(input, e.shiftKey ? pos.begin : caretPos, caretPos, !0);
					} else k === Inputmask.keyCode.HOME && !e.shiftKey || k === Inputmask.keyCode.PAGE_UP ? (e.preventDefault(),
						caret(input, 0, e.shiftKey ? pos.begin : 0, !0)) : (opts.undoOnEscape && k === Inputmask.keyCode.ESCAPE || 90 === k && e.ctrlKey) && !0 !== e.altKey ? (checkVal(input, !0, !1, undoValue.split("")),
						$input.trigger("click")) : k !== Inputmask.keyCode.INSERT || e.shiftKey || e.ctrlKey ? !0 === opts.tabThrough && k === Inputmask.keyCode.TAB ? (!0 === e.shiftKey ? (null === getTest(pos.begin).match.fn && (pos.begin = seekNext(pos.begin)),
						pos.end = seekPrevious(pos.begin, !0), pos.begin = seekPrevious(pos.end, !0)) : (pos.begin = seekNext(pos.begin, !0),
						pos.end = seekNext(pos.begin, !0), pos.end < getMaskSet().maskLength && pos.end--),
					pos.begin < getMaskSet().maskLength && (e.preventDefault(), caret(input, pos.begin, pos.end))) : e.shiftKey || !1 === opts.insertMode && (k === Inputmask.keyCode.RIGHT ? setTimeout(function() {
						var caretPos = caret(input);
						caret(input, caretPos.begin);
					}, 0) : k === Inputmask.keyCode.LEFT && setTimeout(function() {
						var caretPos = caret(input);
						caret(input, isRTL ? caretPos.begin + 1 : caretPos.begin - 1);
					}, 0)) : (opts.insertMode = !opts.insertMode, caret(input, opts.insertMode || pos.begin !== getMaskSet().maskLength ? pos.begin : pos.begin - 1));
					opts.onKeyDown.call(this, e, getBuffer(), caret(input).begin, opts), ignorable = -1 !== $.inArray(k, opts.ignorables);
				},
				keypressEvent: function(e, checkval, writeOut, strict, ndx) {
					var input = this, $input = $(input), k = e.which || e.charCode || e.keyCode;
					if (!(!0 === checkval || e.ctrlKey && e.altKey) && (e.ctrlKey || e.metaKey || ignorable)) return k === Inputmask.keyCode.ENTER && undoValue !== getBuffer().join("") && (undoValue = getBuffer().join(""),
						setTimeout(function() {
							$input.trigger("change");
						}, 0)), !0;
					if (k) {
						46 === k && !1 === e.shiftKey && "" !== opts.radixPoint && (k = opts.radixPoint.charCodeAt(0));
						var forwardPosition, pos = checkval ? {
							begin: ndx,
							end: ndx
						} : caret(input), c = String.fromCharCode(k);
						getMaskSet().writeOutBuffer = !0;
						var valResult = isValid(pos, c, strict);
						if (!1 !== valResult && (resetMaskSet(!0), forwardPosition = valResult.caret !== undefined ? valResult.caret : checkval ? valResult.pos + 1 : seekNext(valResult.pos),
								getMaskSet().p = forwardPosition), !1 !== writeOut && (setTimeout(function() {
								opts.onKeyValidation.call(input, k, valResult, opts);
							}, 0), getMaskSet().writeOutBuffer && !1 !== valResult)) {
							var buffer = getBuffer();
							writeBuffer(input, buffer, opts.numericInput && valResult.caret === undefined ? seekPrevious(forwardPosition) : forwardPosition, e, !0 !== checkval),
							!0 !== checkval && setTimeout(function() {
								!0 === isComplete(buffer) && $input.trigger("complete");
							}, 0);
						}
						if (e.preventDefault(), checkval) return !1 !== valResult && (valResult.forwardPosition = forwardPosition),
							valResult;
					}
				},
				pasteEvent: function(e) {
					var tempValue, input = this, ev = e.originalEvent || e, $input = $(input), inputValue = input.inputmask._valueGet(!0), caretPos = caret(input);
					isRTL && (tempValue = caretPos.end, caretPos.end = caretPos.begin, caretPos.begin = tempValue);
					var valueBeforeCaret = inputValue.substr(0, caretPos.begin), valueAfterCaret = inputValue.substr(caretPos.end, inputValue.length);
					if (valueBeforeCaret === (isRTL ? getBufferTemplate().reverse() : getBufferTemplate()).slice(0, caretPos.begin).join("") && (valueBeforeCaret = ""),
						valueAfterCaret === (isRTL ? getBufferTemplate().reverse() : getBufferTemplate()).slice(caretPos.end).join("") && (valueAfterCaret = ""),
						isRTL && (tempValue = valueBeforeCaret, valueBeforeCaret = valueAfterCaret, valueAfterCaret = tempValue),
						window.clipboardData && window.clipboardData.getData) inputValue = valueBeforeCaret + window.clipboardData.getData("Text") + valueAfterCaret; else {
						if (!ev.clipboardData || !ev.clipboardData.getData) return !0;
						inputValue = valueBeforeCaret + ev.clipboardData.getData("text/plain") + valueAfterCaret;
					}
					var pasteValue = inputValue;
					if ($.isFunction(opts.onBeforePaste)) {
						if (!1 === (pasteValue = opts.onBeforePaste.call(inputmask, inputValue, opts))) return e.preventDefault();
						pasteValue || (pasteValue = inputValue);
					}
					return checkVal(input, !1, !1, isRTL ? pasteValue.split("").reverse() : pasteValue.toString().split("")),
						writeBuffer(input, getBuffer(), seekNext(getLastValidPosition()), e, undoValue !== getBuffer().join("")),
					!0 === isComplete(getBuffer()) && $input.trigger("complete"), e.preventDefault();
				},
				inputFallBackEvent: function(e) {
					var input = this, inputValue = input.inputmask._valueGet();
					if (getBuffer().join("") !== inputValue) {
						var caretPos = caret(input);
						if (inputValue = function(input, inputValue, caretPos) {
								return "." === inputValue.charAt(caretPos.begin - 1) && "" !== opts.radixPoint && (inputValue = inputValue.split(""),
									inputValue[caretPos.begin - 1] = opts.radixPoint.charAt(0), inputValue = inputValue.join("")),
									inputValue;
							}(input, inputValue, caretPos), inputValue = function(input, inputValue, caretPos) {
								if (iemobile) {
									var inputChar = inputValue.replace(getBuffer().join(""), "");
									if (1 === inputChar.length) {
										var iv = inputValue.split("");
										iv.splice(caretPos.begin, 0, inputChar), inputValue = iv.join("");
									}
								}
								return inputValue;
							}(input, inputValue, caretPos), caretPos.begin > inputValue.length && (caret(input, inputValue.length),
								caretPos = caret(input)), getBuffer().join("") !== inputValue) {
							var buffer = getBuffer().join(""), offset = inputValue.length > buffer.length ? -1 : 0, frontPart = inputValue.substr(0, caretPos.begin), backPart = inputValue.substr(caretPos.begin), frontBufferPart = buffer.substr(0, caretPos.begin + offset), backBufferPart = buffer.substr(caretPos.begin + offset), selection = caretPos, entries = "", isEntry = !1;
							if (frontPart !== frontBufferPart) {
								for (var fpl = (isEntry = frontPart.length >= frontBufferPart.length) ? frontPart.length : frontBufferPart.length, i = 0; frontPart.charAt(i) === frontBufferPart.charAt(i) && i < fpl; i++) ;
								isEntry && (0 === offset && (selection.begin = i), entries += frontPart.slice(i, selection.end));
							}
							if (backPart !== backBufferPart && (backPart.length > backBufferPart.length ? entries += backPart.slice(0, 1) : backPart.length < backBufferPart.length && (selection.end += backBufferPart.length - backPart.length,
								isEntry || "" === opts.radixPoint || "" !== backPart || frontPart.charAt(selection.begin + offset - 1) !== opts.radixPoint || (selection.begin--,
									entries = opts.radixPoint))), writeBuffer(input, getBuffer(), {
									begin: selection.begin + offset,
									end: selection.end + offset
								}), entries.length > 0) $.each(entries.split(""), function(ndx, entry) {
								var keypress = new $.Event("keypress");
								keypress.which = entry.charCodeAt(0), ignorable = !1, EventHandlers.keypressEvent.call(input, keypress);
							}); else {
								selection.begin === selection.end - 1 && (selection.begin = seekPrevious(selection.begin + 1),
									selection.begin === selection.end - 1 ? caret(input, selection.begin) : caret(input, selection.begin, selection.end));
								var keydown = new $.Event("keydown");
								keydown.keyCode = Inputmask.keyCode.DELETE, EventHandlers.keydownEvent.call(input, keydown);
							}
							e.preventDefault();
						}
					}
				},
				setValueEvent: function(e) {
					this.inputmask.refreshValue = !1;
					var input = this, value = input.inputmask._valueGet(!0);
					$.isFunction(opts.onBeforeMask) && (value = opts.onBeforeMask.call(inputmask, value, opts) || value),
						value = value.split(""), checkVal(input, !0, !1, isRTL ? value.reverse() : value),
						undoValue = getBuffer().join(""), (opts.clearMaskOnLostFocus || opts.clearIncomplete) && input.inputmask._valueGet() === getBufferTemplate().join("") && input.inputmask._valueSet("");
				},
				focusEvent: function(e) {
					var input = this, nptValue = input.inputmask._valueGet();
					opts.showMaskOnFocus && (!opts.showMaskOnHover || opts.showMaskOnHover && "" === nptValue) && (input.inputmask._valueGet() !== getBuffer().join("") ? writeBuffer(input, getBuffer(), seekNext(getLastValidPosition())) : !1 === mouseEnter && caret(input, seekNext(getLastValidPosition()))),
					!0 === opts.positionCaretOnTab && !1 === mouseEnter && "" !== nptValue && (writeBuffer(input, getBuffer(), caret(input)),
						EventHandlers.clickEvent.apply(input, [ e, !0 ])), undoValue = getBuffer().join("");
				},
				mouseleaveEvent: function(e) {
					var input = this;
					if (mouseEnter = !1, opts.clearMaskOnLostFocus && document.activeElement !== input) {
						var buffer = getBuffer().slice(), nptValue = input.inputmask._valueGet();
						nptValue !== input.getAttribute("placeholder") && "" !== nptValue && (-1 === getLastValidPosition() && nptValue === getBufferTemplate().join("") ? buffer = [] : clearOptionalTail(buffer),
							writeBuffer(input, buffer));
					}
				},
				clickEvent: function(e, tabbed) {
					function doRadixFocus(clickPos) {
						if ("" !== opts.radixPoint) {
							var vps = getMaskSet().validPositions;
							if (vps[clickPos] === undefined || vps[clickPos].input === getPlaceholder(clickPos)) {
								if (clickPos < seekNext(-1)) return !0;
								var radixPos = $.inArray(opts.radixPoint, getBuffer());
								if (-1 !== radixPos) {
									for (var vp in vps) if (radixPos < vp && vps[vp].input !== getPlaceholder(vp)) return !1;
									return !0;
								}
							}
						}
						return !1;
					}
					var input = this;
					setTimeout(function() {
						if (document.activeElement === input) {
							var selectedCaret = caret(input);
							if (tabbed && (isRTL ? selectedCaret.end = selectedCaret.begin : selectedCaret.begin = selectedCaret.end),
								selectedCaret.begin === selectedCaret.end) switch (opts.positionCaretOnClick) {
								case "none":
									break;

								case "radixFocus":
									if (doRadixFocus(selectedCaret.begin)) {
										var radixPos = getBuffer().join("").indexOf(opts.radixPoint);
										caret(input, opts.numericInput ? seekNext(radixPos) : radixPos);
										break;
									}

								default:
									var clickPosition = selectedCaret.begin, lvclickPosition = getLastValidPosition(clickPosition, !0), lastPosition = seekNext(lvclickPosition);
									if (clickPosition < lastPosition) caret(input, isMask(clickPosition, !0) || isMask(clickPosition - 1, !0) ? clickPosition : seekNext(clickPosition)); else {
										var lvp = getMaskSet().validPositions[lvclickPosition], tt = getTestTemplate(lastPosition, lvp ? lvp.match.locator : undefined, lvp), placeholder = getPlaceholder(lastPosition, tt.match);
										if ("" !== placeholder && getBuffer()[lastPosition] !== placeholder && !0 !== tt.match.optionalQuantifier && !0 !== tt.match.newBlockMarker || !isMask(lastPosition, !0) && tt.match.def === placeholder) {
											var newPos = seekNext(lastPosition);
											(clickPosition >= newPos || clickPosition === lastPosition) && (lastPosition = newPos);
										}
										caret(input, lastPosition);
									}
							}
						}
					}, 0);
				},
				dblclickEvent: function(e) {
					var input = this;
					setTimeout(function() {
						caret(input, 0, seekNext(getLastValidPosition()));
					}, 0);
				},
				cutEvent: function(e) {
					var input = this, $input = $(input), pos = caret(input), ev = e.originalEvent || e, clipboardData = window.clipboardData || ev.clipboardData, clipData = isRTL ? getBuffer().slice(pos.end, pos.begin) : getBuffer().slice(pos.begin, pos.end);
					clipboardData.setData("text", isRTL ? clipData.reverse().join("") : clipData.join("")),
					document.execCommand && document.execCommand("copy"), handleRemove(input, Inputmask.keyCode.DELETE, pos),
						writeBuffer(input, getBuffer(), getMaskSet().p, e, undoValue !== getBuffer().join("")),
					input.inputmask._valueGet() === getBufferTemplate().join("") && $input.trigger("cleared");
				},
				blurEvent: function(e) {
					var $input = $(this), input = this;
					if (input.inputmask) {
						var nptValue = input.inputmask._valueGet(), buffer = getBuffer().slice();
						"" !== nptValue && (opts.clearMaskOnLostFocus && (-1 === getLastValidPosition() && nptValue === getBufferTemplate().join("") ? buffer = [] : clearOptionalTail(buffer)),
						!1 === isComplete(buffer) && (setTimeout(function() {
							$input.trigger("incomplete");
						}, 0), opts.clearIncomplete && (resetMaskSet(), buffer = opts.clearMaskOnLostFocus ? [] : getBufferTemplate().slice())),
							writeBuffer(input, buffer, undefined, e)), undoValue !== getBuffer().join("") && (undoValue = buffer.join(""),
							$input.trigger("change"));
					}
				},
				mouseenterEvent: function(e) {
					var input = this;
					mouseEnter = !0, document.activeElement !== input && opts.showMaskOnHover && input.inputmask._valueGet() !== getBuffer().join("") && writeBuffer(input, getBuffer());
				},
				submitEvent: function(e) {
					undoValue !== getBuffer().join("") && $el.trigger("change"), opts.clearMaskOnLostFocus && -1 === getLastValidPosition() && el.inputmask._valueGet && el.inputmask._valueGet() === getBufferTemplate().join("") && el.inputmask._valueSet(""),
					opts.removeMaskOnSubmit && (el.inputmask._valueSet(el.inputmask.unmaskedvalue(), !0),
						setTimeout(function() {
							writeBuffer(el, getBuffer());
						}, 0));
				},
				resetEvent: function(e) {
					el.inputmask.refreshValue = !0, setTimeout(function() {
						$el.trigger("setvalue");
					}, 0);
				}
			};
			Inputmask.prototype.positionColorMask = function(input, template) {
				input.style.left = template.offsetLeft + "px";
			};
			var valueBuffer;
			if (actionObj !== undefined) switch (actionObj.action) {
				case "isComplete":
					return el = actionObj.el, isComplete(getBuffer());

				case "unmaskedvalue":
					return el !== undefined && actionObj.value === undefined || (valueBuffer = actionObj.value,
						valueBuffer = ($.isFunction(opts.onBeforeMask) ? opts.onBeforeMask.call(inputmask, valueBuffer, opts) || valueBuffer : valueBuffer).split(""),
						checkVal(undefined, !1, !1, isRTL ? valueBuffer.reverse() : valueBuffer), $.isFunction(opts.onBeforeWrite) && opts.onBeforeWrite.call(inputmask, undefined, getBuffer(), 0, opts)),
						unmaskedvalue(el);

				case "mask":
					!function(elem) {
						EventRuler.off(elem);
						var isSupported = function(input, opts) {
							var elementType = input.getAttribute("type"), isSupported = "INPUT" === input.tagName && -1 !== $.inArray(elementType, opts.supportsInputType) || input.isContentEditable || "TEXTAREA" === input.tagName;
							if (!isSupported) if ("INPUT" === input.tagName) {
								var el = document.createElement("input");
								el.setAttribute("type", elementType), isSupported = "text" === el.type, el = null;
							} else isSupported = "partial";
							return !1 !== isSupported ? function(npt) {
								function getter() {
									return this.inputmask ? this.inputmask.opts.autoUnmask ? this.inputmask.unmaskedvalue() : -1 !== getLastValidPosition() || !0 !== opts.nullable ? document.activeElement === this && opts.clearMaskOnLostFocus ? (isRTL ? clearOptionalTail(getBuffer().slice()).reverse() : clearOptionalTail(getBuffer().slice())).join("") : valueGet.call(this) : "" : valueGet.call(this);
								}
								function setter(value) {
									valueSet.call(this, value), this.inputmask && $(this).trigger("setvalue");
								}
								var valueGet, valueSet;
								if (!npt.inputmask.__valueGet) {
									if (!0 !== opts.noValuePatching) {
										if (Object.getOwnPropertyDescriptor) {
											"function" != typeof Object.getPrototypeOf && (Object.getPrototypeOf = "object" === _typeof("test".__proto__) ? function(object) {
												return object.__proto__;
											} : function(object) {
												return object.constructor.prototype;
											});
											var valueProperty = Object.getPrototypeOf ? Object.getOwnPropertyDescriptor(Object.getPrototypeOf(npt), "value") : undefined;
											valueProperty && valueProperty.get && valueProperty.set ? (valueGet = valueProperty.get,
												valueSet = valueProperty.set, Object.defineProperty(npt, "value", {
												get: getter,
												set: setter,
												configurable: !0
											})) : "INPUT" !== npt.tagName && (valueGet = function() {
												return this.textContent;
											}, valueSet = function(value) {
												this.textContent = value;
											}, Object.defineProperty(npt, "value", {
												get: getter,
												set: setter,
												configurable: !0
											}));
										} else document.__lookupGetter__ && npt.__lookupGetter__("value") && (valueGet = npt.__lookupGetter__("value"),
											valueSet = npt.__lookupSetter__("value"), npt.__defineGetter__("value", getter),
											npt.__defineSetter__("value", setter));
										npt.inputmask.__valueGet = valueGet, npt.inputmask.__valueSet = valueSet;
									}
									npt.inputmask._valueGet = function(overruleRTL) {
										return isRTL && !0 !== overruleRTL ? valueGet.call(this.el).split("").reverse().join("") : valueGet.call(this.el);
									}, npt.inputmask._valueSet = function(value, overruleRTL) {
										valueSet.call(this.el, null === value || value === undefined ? "" : !0 !== overruleRTL && isRTL ? value.split("").reverse().join("") : value);
									}, valueGet === undefined && (valueGet = function() {
										return this.value;
									}, valueSet = function(value) {
										this.value = value;
									}, function(type) {
										if ($.valHooks && ($.valHooks[type] === undefined || !0 !== $.valHooks[type].inputmaskpatch)) {
											var valhookGet = $.valHooks[type] && $.valHooks[type].get ? $.valHooks[type].get : function(elem) {
												return elem.value;
											}, valhookSet = $.valHooks[type] && $.valHooks[type].set ? $.valHooks[type].set : function(elem, value) {
												return elem.value = value, elem;
											};
											$.valHooks[type] = {
												get: function(elem) {
													if (elem.inputmask) {
														if (elem.inputmask.opts.autoUnmask) return elem.inputmask.unmaskedvalue();
														var result = valhookGet(elem);
														return -1 !== getLastValidPosition(undefined, undefined, elem.inputmask.maskset.validPositions) || !0 !== opts.nullable ? result : "";
													}
													return valhookGet(elem);
												},
												set: function(elem, value) {
													var result, $elem = $(elem);
													return result = valhookSet(elem, value), elem.inputmask && $elem.trigger("setvalue"),
														result;
												},
												inputmaskpatch: !0
											};
										}
									}(npt.type), function(npt) {
										EventRuler.on(npt, "mouseenter", function(event) {
											var $input = $(this);
											this.inputmask._valueGet() !== getBuffer().join("") && $input.trigger("setvalue");
										});
									}(npt));
								}
							}(input) : input.inputmask = undefined, isSupported;
						}(elem, opts);
						if (!1 !== isSupported && (el = elem, $el = $(el), maxLength = el !== undefined ? el.maxLength : undefined,
							-1 === maxLength && (maxLength = undefined), !0 === opts.colorMask && initializeColorMask(el),
							android && (el.hasOwnProperty("inputmode") && (el.inputmode = opts.inputmode, el.setAttribute("inputmode", opts.inputmode)),
							"rtfm" === opts.androidHack && (!0 !== opts.colorMask && initializeColorMask(el),
								el.type = "password")), !0 === isSupported && (EventRuler.on(el, "submit", EventHandlers.submitEvent),
								EventRuler.on(el, "reset", EventHandlers.resetEvent), EventRuler.on(el, "mouseenter", EventHandlers.mouseenterEvent),
								EventRuler.on(el, "blur", EventHandlers.blurEvent), EventRuler.on(el, "focus", EventHandlers.focusEvent),
								EventRuler.on(el, "mouseleave", EventHandlers.mouseleaveEvent), !0 !== opts.colorMask && EventRuler.on(el, "click", EventHandlers.clickEvent),
								EventRuler.on(el, "dblclick", EventHandlers.dblclickEvent), EventRuler.on(el, "paste", EventHandlers.pasteEvent),
								EventRuler.on(el, "dragdrop", EventHandlers.pasteEvent), EventRuler.on(el, "drop", EventHandlers.pasteEvent),
								EventRuler.on(el, "cut", EventHandlers.cutEvent), EventRuler.on(el, "complete", opts.oncomplete),
								EventRuler.on(el, "incomplete", opts.onincomplete), EventRuler.on(el, "cleared", opts.oncleared),
								android || !0 === opts.inputEventOnly ? el.removeAttribute("maxLength") : (EventRuler.on(el, "keydown", EventHandlers.keydownEvent),
									EventRuler.on(el, "keypress", EventHandlers.keypressEvent)), EventRuler.on(el, "compositionstart", $.noop),
								EventRuler.on(el, "compositionupdate", $.noop), EventRuler.on(el, "compositionend", $.noop),
								EventRuler.on(el, "keyup", $.noop), EventRuler.on(el, "input", EventHandlers.inputFallBackEvent),
								EventRuler.on(el, "beforeinput", $.noop)), EventRuler.on(el, "setvalue", EventHandlers.setValueEvent),
								undoValue = getBufferTemplate().join(""), "" !== el.inputmask._valueGet(!0) || !1 === opts.clearMaskOnLostFocus || document.activeElement === el)) {
							var initialValue = $.isFunction(opts.onBeforeMask) ? opts.onBeforeMask.call(inputmask, el.inputmask._valueGet(!0), opts) || el.inputmask._valueGet(!0) : el.inputmask._valueGet(!0);
							"" !== initialValue && checkVal(el, !0, !1, isRTL ? initialValue.split("").reverse() : initialValue.split(""));
							var buffer = getBuffer().slice();
							undoValue = buffer.join(""), !1 === isComplete(buffer) && opts.clearIncomplete && resetMaskSet(),
							opts.clearMaskOnLostFocus && document.activeElement !== el && (-1 === getLastValidPosition() ? buffer = [] : clearOptionalTail(buffer)),
								writeBuffer(el, buffer), document.activeElement === el && caret(el, seekNext(getLastValidPosition()));
						}
					}(el);
					break;

				case "format":
					return valueBuffer = ($.isFunction(opts.onBeforeMask) ? opts.onBeforeMask.call(inputmask, actionObj.value, opts) || actionObj.value : actionObj.value).split(""),
						checkVal(undefined, !0, !1, isRTL ? valueBuffer.reverse() : valueBuffer), actionObj.metadata ? {
						value: isRTL ? getBuffer().slice().reverse().join("") : getBuffer().join(""),
						metadata: maskScope.call(this, {
							action: "getmetadata"
						}, maskset, opts)
					} : isRTL ? getBuffer().slice().reverse().join("") : getBuffer().join("");

				case "isValid":
					actionObj.value ? (valueBuffer = actionObj.value.split(""), checkVal(undefined, !0, !0, isRTL ? valueBuffer.reverse() : valueBuffer)) : actionObj.value = getBuffer().join("");
					for (var buffer = getBuffer(), rl = determineLastRequiredPosition(), lmib = buffer.length - 1; lmib > rl && !isMask(lmib); lmib--) ;
					return buffer.splice(rl, lmib + 1 - rl), isComplete(buffer) && actionObj.value === getBuffer().join("");

				case "getemptymask":
					return getBufferTemplate().join("");

				case "remove":
					if (el && el.inputmask) {
						$el = $(el), el.inputmask._valueSet(opts.autoUnmask ? unmaskedvalue(el) : el.inputmask._valueGet(!0)),
							EventRuler.off(el);
						Object.getOwnPropertyDescriptor && Object.getPrototypeOf ? Object.getOwnPropertyDescriptor(Object.getPrototypeOf(el), "value") && el.inputmask.__valueGet && Object.defineProperty(el, "value", {
							get: el.inputmask.__valueGet,
							set: el.inputmask.__valueSet,
							configurable: !0
						}) : document.__lookupGetter__ && el.__lookupGetter__("value") && el.inputmask.__valueGet && (el.__defineGetter__("value", el.inputmask.__valueGet),
							el.__defineSetter__("value", el.inputmask.__valueSet)), el.inputmask = undefined;
					}
					return el;

				case "getmetadata":
					if ($.isArray(maskset.metadata)) {
						var maskTarget = getMaskTemplate(!0, 0, !1).join("");
						return $.each(maskset.metadata, function(ndx, mtdt) {
							if (mtdt.mask === maskTarget) return maskTarget = mtdt, !1;
						}), maskTarget;
					}
					return maskset.metadata;
			}
		}
		var ua = navigator.userAgent, mobile = /mobile/i.test(ua), iemobile = /iemobile/i.test(ua), iphone = /iphone/i.test(ua) && !iemobile, android = /android/i.test(ua) && !iemobile;
		return Inputmask.prototype = {
			dataAttribute: "data-inputmask",
			defaults: {
				placeholder: "_",
				optionalmarker: {
					start: "[",
					end: "]"
				},
				quantifiermarker: {
					start: "{",
					end: "}"
				},
				groupmarker: {
					start: "(",
					end: ")"
				},
				alternatormarker: "|",
				escapeChar: "\\",
				mask: null,
				regex: null,
				oncomplete: $.noop,
				onincomplete: $.noop,
				oncleared: $.noop,
				repeat: 0,
				greedy: !0,
				autoUnmask: !1,
				removeMaskOnSubmit: !1,
				clearMaskOnLostFocus: !0,
				insertMode: !0,
				clearIncomplete: !1,
				alias: null,
				onKeyDown: $.noop,
				onBeforeMask: null,
				onBeforePaste: function(pastedValue, opts) {
					return $.isFunction(opts.onBeforeMask) ? opts.onBeforeMask.call(this, pastedValue, opts) : pastedValue;
				},
				onBeforeWrite: null,
				onUnMask: null,
				showMaskOnFocus: !0,
				showMaskOnHover: !0,
				onKeyValidation: $.noop,
				skipOptionalPartCharacter: " ",
				numericInput: !1,
				rightAlign: !1,
				undoOnEscape: !0,
				radixPoint: "",
				radixPointDefinitionSymbol: undefined,
				groupSeparator: "",
				keepStatic: null,
				positionCaretOnTab: !0,
				tabThrough: !1,
				supportsInputType: [ "text", "tel", "password" ],
				ignorables: [ 8, 9, 13, 19, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46, 93, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123, 0, 229 ],
				isComplete: null,
				canClearPosition: $.noop,
				preValidation: null,
				postValidation: null,
				staticDefinitionSymbol: undefined,
				jitMasking: !1,
				nullable: !0,
				inputEventOnly: !1,
				noValuePatching: !1,
				positionCaretOnClick: "lvp",
				casing: null,
				inputmode: "verbatim",
				colorMask: !1,
				androidHack: !1,
				importDataAttributes: !0
			},
			definitions: {
				"9": {
					validator: "[0-9\uff11-\uff19]",
					cardinality: 1,
					definitionSymbol: "*"
				},
				a: {
					validator: "[A-Za-z\u0410-\u044f\u0401\u0451\xc0-\xff\xb5]",
					cardinality: 1,
					definitionSymbol: "*"
				},
				"*": {
					validator: "[0-9\uff11-\uff19A-Za-z\u0410-\u044f\u0401\u0451\xc0-\xff\xb5]",
					cardinality: 1
				}
			},
			aliases: {},
			masksCache: {},
			mask: function(elems) {
				function importAttributeOptions(npt, opts, userOptions, dataAttribute) {
					if (!0 === opts.importDataAttributes) {
						var option, dataoptions, optionData, p, importOption = function(option, optionData) {
							null !== (optionData = optionData !== undefined ? optionData : npt.getAttribute(dataAttribute + "-" + option)) && ("string" == typeof optionData && (0 === option.indexOf("on") ? optionData = window[optionData] : "false" === optionData ? optionData = !1 : "true" === optionData && (optionData = !0)),
								userOptions[option] = optionData);
						}, attrOptions = npt.getAttribute(dataAttribute);
						if (attrOptions && "" !== attrOptions && (attrOptions = attrOptions.replace(new RegExp("'", "g"), '"'),
								dataoptions = JSON.parse("{" + attrOptions + "}")), dataoptions) {
							optionData = undefined;
							for (p in dataoptions) if ("alias" === p.toLowerCase()) {
								optionData = dataoptions[p];
								break;
							}
						}
						importOption("alias", optionData), userOptions.alias && resolveAlias(userOptions.alias, userOptions, opts);
						for (option in opts) {
							if (dataoptions) {
								optionData = undefined;
								for (p in dataoptions) if (p.toLowerCase() === option.toLowerCase()) {
									optionData = dataoptions[p];
									break;
								}
							}
							importOption(option, optionData);
						}
					}
					return $.extend(!0, opts, userOptions), ("rtl" === npt.dir || opts.rightAlign) && (npt.style.textAlign = "right"),
					("rtl" === npt.dir || opts.numericInput) && (npt.dir = "ltr", npt.removeAttribute("dir"),
						opts.isRTL = !0), opts;
				}
				var that = this;
				return "string" == typeof elems && (elems = document.getElementById(elems) || document.querySelectorAll(elems)),
					elems = elems.nodeName ? [ elems ] : elems, $.each(elems, function(ndx, el) {
					var scopedOpts = $.extend(!0, {}, that.opts);
					importAttributeOptions(el, scopedOpts, $.extend(!0, {}, that.userOptions), that.dataAttribute);
					var maskset = generateMaskSet(scopedOpts, that.noMasksCache);
					maskset !== undefined && (el.inputmask !== undefined && (el.inputmask.opts.autoUnmask = !0,
						el.inputmask.remove()), el.inputmask = new Inputmask(undefined, undefined, !0),
						el.inputmask.opts = scopedOpts, el.inputmask.noMasksCache = that.noMasksCache, el.inputmask.userOptions = $.extend(!0, {}, that.userOptions),
						el.inputmask.isRTL = scopedOpts.isRTL || scopedOpts.numericInput, el.inputmask.el = el,
						el.inputmask.maskset = maskset, $.data(el, "_inputmask_opts", scopedOpts), maskScope.call(el.inputmask, {
						action: "mask"
					}));
				}), elems && elems[0] ? elems[0].inputmask || this : this;
			},
			option: function(options, noremask) {
				return "string" == typeof options ? this.opts[options] : "object" === (void 0 === options ? "undefined" : _typeof(options)) ? ($.extend(this.userOptions, options),
				this.el && !0 !== noremask && this.mask(this.el), this) : void 0;
			},
			unmaskedvalue: function(value) {
				return this.maskset = this.maskset || generateMaskSet(this.opts, this.noMasksCache),
					maskScope.call(this, {
						action: "unmaskedvalue",
						value: value
					});
			},
			remove: function() {
				return maskScope.call(this, {
					action: "remove"
				});
			},
			getemptymask: function() {
				return this.maskset = this.maskset || generateMaskSet(this.opts, this.noMasksCache),
					maskScope.call(this, {
						action: "getemptymask"
					});
			},
			hasMaskedValue: function() {
				return !this.opts.autoUnmask;
			},
			isComplete: function() {
				return this.maskset = this.maskset || generateMaskSet(this.opts, this.noMasksCache),
					maskScope.call(this, {
						action: "isComplete"
					});
			},
			getmetadata: function() {
				return this.maskset = this.maskset || generateMaskSet(this.opts, this.noMasksCache),
					maskScope.call(this, {
						action: "getmetadata"
					});
			},
			isValid: function(value) {
				return this.maskset = this.maskset || generateMaskSet(this.opts, this.noMasksCache),
					maskScope.call(this, {
						action: "isValid",
						value: value
					});
			},
			format: function(value, metadata) {
				return this.maskset = this.maskset || generateMaskSet(this.opts, this.noMasksCache),
					maskScope.call(this, {
						action: "format",
						value: value,
						metadata: metadata
					});
			},
			analyseMask: function(mask, regexMask, opts) {
				function MaskToken(isGroup, isOptional, isQuantifier, isAlternator) {
					this.matches = [], this.openGroup = isGroup || !1, this.alternatorGroup = !1, this.isGroup = isGroup || !1,
						this.isOptional = isOptional || !1, this.isQuantifier = isQuantifier || !1, this.isAlternator = isAlternator || !1,
						this.quantifier = {
							min: 1,
							max: 1
						};
				}
				function insertTestDefinition(mtoken, element, position) {
					position = position !== undefined ? position : mtoken.matches.length;
					var prevMatch = mtoken.matches[position - 1];
					if (regexMask) 0 === element.indexOf("[") || escaped && /\\d|\\s|\\w]/i.test(element) || "." === element ? mtoken.matches.splice(position++, 0, {
						fn: new RegExp(element, opts.casing ? "i" : ""),
						cardinality: 1,
						optionality: mtoken.isOptional,
						newBlockMarker: prevMatch === undefined || prevMatch.def !== element,
						casing: null,
						def: element,
						placeholder: undefined,
						nativeDef: element
					}) : (escaped && (element = element[element.length - 1]), $.each(element.split(""), function(ndx, lmnt) {
						prevMatch = mtoken.matches[position - 1], mtoken.matches.splice(position++, 0, {
							fn: null,
							cardinality: 0,
							optionality: mtoken.isOptional,
							newBlockMarker: prevMatch === undefined || prevMatch.def !== lmnt && null !== prevMatch.fn,
							casing: null,
							def: opts.staticDefinitionSymbol || lmnt,
							placeholder: opts.staticDefinitionSymbol !== undefined ? lmnt : undefined,
							nativeDef: lmnt
						});
					})), escaped = !1; else {
						var maskdef = (opts.definitions ? opts.definitions[element] : undefined) || Inputmask.prototype.definitions[element];
						if (maskdef && !escaped) {
							for (var prevalidators = maskdef.prevalidator, prevalidatorsL = prevalidators ? prevalidators.length : 0, i = 1; i < maskdef.cardinality; i++) {
								var prevalidator = prevalidatorsL >= i ? prevalidators[i - 1] : [], validator = prevalidator.validator, cardinality = prevalidator.cardinality;
								mtoken.matches.splice(position++, 0, {
									fn: validator ? "string" == typeof validator ? new RegExp(validator, opts.casing ? "i" : "") : new function() {
										this.test = validator;
									}() : new RegExp("."),
									cardinality: cardinality || 1,
									optionality: mtoken.isOptional,
									newBlockMarker: prevMatch === undefined || prevMatch.def !== (maskdef.definitionSymbol || element),
									casing: maskdef.casing,
									def: maskdef.definitionSymbol || element,
									placeholder: maskdef.placeholder,
									nativeDef: element
								}), prevMatch = mtoken.matches[position - 1];
							}
							mtoken.matches.splice(position++, 0, {
								fn: maskdef.validator ? "string" == typeof maskdef.validator ? new RegExp(maskdef.validator, opts.casing ? "i" : "") : new function() {
									this.test = maskdef.validator;
								}() : new RegExp("."),
								cardinality: maskdef.cardinality,
								optionality: mtoken.isOptional,
								newBlockMarker: prevMatch === undefined || prevMatch.def !== (maskdef.definitionSymbol || element),
								casing: maskdef.casing,
								def: maskdef.definitionSymbol || element,
								placeholder: maskdef.placeholder,
								nativeDef: element
							});
						} else mtoken.matches.splice(position++, 0, {
							fn: null,
							cardinality: 0,
							optionality: mtoken.isOptional,
							newBlockMarker: prevMatch === undefined || prevMatch.def !== element && null !== prevMatch.fn,
							casing: null,
							def: opts.staticDefinitionSymbol || element,
							placeholder: opts.staticDefinitionSymbol !== undefined ? element : undefined,
							nativeDef: element
						}), escaped = !1;
					}
				}
				function verifyGroupMarker(maskToken) {
					maskToken && maskToken.matches && $.each(maskToken.matches, function(ndx, token) {
						var nextToken = maskToken.matches[ndx + 1];
						(nextToken === undefined || nextToken.matches === undefined || !1 === nextToken.isQuantifier) && token && token.isGroup && (token.isGroup = !1,
						regexMask || (insertTestDefinition(token, opts.groupmarker.start, 0), !0 !== token.openGroup && insertTestDefinition(token, opts.groupmarker.end))),
							verifyGroupMarker(token);
					});
				}
				function defaultCase() {
					if (openenings.length > 0) {
						if (currentOpeningToken = openenings[openenings.length - 1], insertTestDefinition(currentOpeningToken, m),
								currentOpeningToken.isAlternator) {
							alternator = openenings.pop();
							for (var mndx = 0; mndx < alternator.matches.length; mndx++) alternator.matches[mndx].isGroup = !1;
							openenings.length > 0 ? (currentOpeningToken = openenings[openenings.length - 1],
								currentOpeningToken.matches.push(alternator)) : currentToken.matches.push(alternator);
						}
					} else insertTestDefinition(currentToken, m);
				}
				function reverseTokens(maskToken) {
					maskToken.matches = maskToken.matches.reverse();
					for (var match in maskToken.matches) if (maskToken.matches.hasOwnProperty(match)) {
						var intMatch = parseInt(match);
						if (maskToken.matches[match].isQuantifier && maskToken.matches[intMatch + 1] && maskToken.matches[intMatch + 1].isGroup) {
							var qt = maskToken.matches[match];
							maskToken.matches.splice(match, 1), maskToken.matches.splice(intMatch + 1, 0, qt);
						}
						maskToken.matches[match].matches !== undefined ? maskToken.matches[match] = reverseTokens(maskToken.matches[match]) : maskToken.matches[match] = function(st) {
							return st === opts.optionalmarker.start ? st = opts.optionalmarker.end : st === opts.optionalmarker.end ? st = opts.optionalmarker.start : st === opts.groupmarker.start ? st = opts.groupmarker.end : st === opts.groupmarker.end && (st = opts.groupmarker.start),
								st;
						}(maskToken.matches[match]);
					}
					return maskToken;
				}
				var match, m, openingToken, currentOpeningToken, alternator, lastMatch, groupToken, tokenizer = /(?:[?*+]|\{[0-9\+\*]+(?:,[0-9\+\*]*)?\})|[^.?*+^${[]()|\\]+|./g, regexTokenizer = /\[\^?]?(?:[^\\\]]+|\\[\S\s]?)*]?|\\(?:0(?:[0-3][0-7]{0,2}|[4-7][0-7]?)?|[1-9][0-9]*|x[0-9A-Fa-f]{2}|u[0-9A-Fa-f]{4}|c[A-Za-z]|[\S\s]?)|\((?:\?[:=!]?)?|(?:[?*+]|\{[0-9]+(?:,[0-9]*)?\})\??|[^.?*+^${[()|\\]+|./g, escaped = !1, currentToken = new MaskToken(), openenings = [], maskTokens = [];
				for (regexMask && (opts.optionalmarker.start = undefined, opts.optionalmarker.end = undefined); match = regexMask ? regexTokenizer.exec(mask) : tokenizer.exec(mask); ) {
					if (m = match[0], regexMask) switch (m.charAt(0)) {
						case "?":
							m = "{0,1}";
							break;

						case "+":
						case "*":
							m = "{" + m + "}";
					}
					if (escaped) defaultCase(); else switch (m.charAt(0)) {
						case opts.escapeChar:
							escaped = !0, regexMask && defaultCase();
							break;

						case opts.optionalmarker.end:
						case opts.groupmarker.end:
							if (openingToken = openenings.pop(), openingToken.openGroup = !1, openingToken !== undefined) if (openenings.length > 0) {
								if (currentOpeningToken = openenings[openenings.length - 1], currentOpeningToken.matches.push(openingToken),
										currentOpeningToken.isAlternator) {
									alternator = openenings.pop();
									for (var mndx = 0; mndx < alternator.matches.length; mndx++) alternator.matches[mndx].isGroup = !1,
										alternator.matches[mndx].alternatorGroup = !1;
									openenings.length > 0 ? (currentOpeningToken = openenings[openenings.length - 1],
										currentOpeningToken.matches.push(alternator)) : currentToken.matches.push(alternator);
								}
							} else currentToken.matches.push(openingToken); else defaultCase();
							break;

						case opts.optionalmarker.start:
							openenings.push(new MaskToken(!1, !0));
							break;

						case opts.groupmarker.start:
							openenings.push(new MaskToken(!0));
							break;

						case opts.quantifiermarker.start:
							var quantifier = new MaskToken(!1, !1, !0);
							m = m.replace(/[{}]/g, "");
							var mq = m.split(","), mq0 = isNaN(mq[0]) ? mq[0] : parseInt(mq[0]), mq1 = 1 === mq.length ? mq0 : isNaN(mq[1]) ? mq[1] : parseInt(mq[1]);
							if ("*" !== mq1 && "+" !== mq1 || (mq0 = "*" === mq1 ? 0 : 1), quantifier.quantifier = {
									min: mq0,
									max: mq1
								}, openenings.length > 0) {
								var matches = openenings[openenings.length - 1].matches;
								match = matches.pop(), match.isGroup || (groupToken = new MaskToken(!0), groupToken.matches.push(match),
									match = groupToken), matches.push(match), matches.push(quantifier);
							} else match = currentToken.matches.pop(), match.isGroup || (regexMask && null === match.fn && "." === match.def && (match.fn = new RegExp(match.def, opts.casing ? "i" : "")),
								groupToken = new MaskToken(!0), groupToken.matches.push(match), match = groupToken),
								currentToken.matches.push(match), currentToken.matches.push(quantifier);
							break;

						case opts.alternatormarker:
							if (openenings.length > 0) {
								currentOpeningToken = openenings[openenings.length - 1];
								var subToken = currentOpeningToken.matches[currentOpeningToken.matches.length - 1];
								lastMatch = currentOpeningToken.openGroup && (subToken.matches === undefined || !1 === subToken.isGroup && !1 === subToken.isAlternator) ? openenings.pop() : currentOpeningToken.matches.pop();
							} else lastMatch = currentToken.matches.pop();
							if (lastMatch.isAlternator) openenings.push(lastMatch); else if (lastMatch.alternatorGroup ? (alternator = openenings.pop(),
									lastMatch.alternatorGroup = !1) : alternator = new MaskToken(!1, !1, !1, !0), alternator.matches.push(lastMatch),
									openenings.push(alternator), lastMatch.openGroup) {
								lastMatch.openGroup = !1;
								var alternatorGroup = new MaskToken(!0);
								alternatorGroup.alternatorGroup = !0, openenings.push(alternatorGroup);
							}
							break;

						default:
							defaultCase();
					}
				}
				for (;openenings.length > 0; ) openingToken = openenings.pop(), currentToken.matches.push(openingToken);
				return currentToken.matches.length > 0 && (verifyGroupMarker(currentToken), maskTokens.push(currentToken)),
				(opts.numericInput || opts.isRTL) && reverseTokens(maskTokens[0]), maskTokens;
			}
		}, Inputmask.extendDefaults = function(options) {
			$.extend(!0, Inputmask.prototype.defaults, options);
		}, Inputmask.extendDefinitions = function(definition) {
			$.extend(!0, Inputmask.prototype.definitions, definition);
		}, Inputmask.extendAliases = function(alias) {
			$.extend(!0, Inputmask.prototype.aliases, alias);
		}, Inputmask.format = function(value, options, metadata) {
			return Inputmask(options).format(value, metadata);
		}, Inputmask.unmask = function(value, options) {
			return Inputmask(options).unmaskedvalue(value);
		}, Inputmask.isValid = function(value, options) {
			return Inputmask(options).isValid(value);
		}, Inputmask.remove = function(elems) {
			$.each(elems, function(ndx, el) {
				el.inputmask && el.inputmask.remove();
			});
		}, Inputmask.escapeRegex = function(str) {
			var specials = [ "/", ".", "*", "+", "?", "|", "(", ")", "[", "]", "{", "}", "\\", "$", "^" ];
			return str.replace(new RegExp("(\\" + specials.join("|\\") + ")", "gim"), "\\$1");
		}, Inputmask.keyCode = {
			ALT: 18,
			BACKSPACE: 8,
			BACKSPACE_SAFARI: 127,
			CAPS_LOCK: 20,
			COMMA: 188,
			COMMAND: 91,
			COMMAND_LEFT: 91,
			COMMAND_RIGHT: 93,
			CONTROL: 17,
			DELETE: 46,
			DOWN: 40,
			END: 35,
			ENTER: 13,
			ESCAPE: 27,
			HOME: 36,
			INSERT: 45,
			LEFT: 37,
			MENU: 93,
			NUMPAD_ADD: 107,
			NUMPAD_DECIMAL: 110,
			NUMPAD_DIVIDE: 111,
			NUMPAD_ENTER: 108,
			NUMPAD_MULTIPLY: 106,
			NUMPAD_SUBTRACT: 109,
			PAGE_DOWN: 34,
			PAGE_UP: 33,
			PERIOD: 190,
			RIGHT: 39,
			SHIFT: 16,
			SPACE: 32,
			TAB: 9,
			UP: 38,
			WINDOWS: 91,
			X: 88
		}, Inputmask;
	});
}, function(module, exports) {
	module.exports = jQuery;
}, function(module, exports, __webpack_require__) {
	"use strict";
	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;
	"function" == typeof Symbol && Symbol.iterator;
	!function(factory) {
		__WEBPACK_AMD_DEFINE_ARRAY__ = [ __webpack_require__(0), __webpack_require__(1) ],
			__WEBPACK_AMD_DEFINE_FACTORY__ = factory, void 0 !== (__WEBPACK_AMD_DEFINE_RESULT__ = "function" == typeof __WEBPACK_AMD_DEFINE_FACTORY__ ? __WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__) : __WEBPACK_AMD_DEFINE_FACTORY__) && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__);
	}(function($, Inputmask) {
		function isLeapYear(year) {
			return isNaN(year) || 29 === new Date(year, 2, 0).getDate();
		}
		return Inputmask.extendAliases({
			"dd/mm/yyyy": {
				mask: "1/2/y",
				placeholder: "dd/mm/yyyy",
				regex: {
					val1pre: new RegExp("[0-3]"),
					val1: new RegExp("0[1-9]|[12][0-9]|3[01]"),
					val2pre: function(separator) {
						var escapedSeparator = Inputmask.escapeRegex.call(this, separator);
						return new RegExp("((0[1-9]|[12][0-9]|3[01])" + escapedSeparator + "[01])");
					},
					val2: function(separator) {
						var escapedSeparator = Inputmask.escapeRegex.call(this, separator);
						return new RegExp("((0[1-9]|[12][0-9])" + escapedSeparator + "(0[1-9]|1[012]))|(30" + escapedSeparator + "(0[13-9]|1[012]))|(31" + escapedSeparator + "(0[13578]|1[02]))");
					}
				},
				leapday: "29/02/",
				separator: "/",
				yearrange: {
					minyear: 1900,
					maxyear: 2099
				},
				isInYearRange: function(chrs, minyear, maxyear) {
					if (isNaN(chrs)) return !1;
					var enteredyear = parseInt(chrs.concat(minyear.toString().slice(chrs.length))), enteredyear2 = parseInt(chrs.concat(maxyear.toString().slice(chrs.length)));
					return !isNaN(enteredyear) && (minyear <= enteredyear && enteredyear <= maxyear) || !isNaN(enteredyear2) && (minyear <= enteredyear2 && enteredyear2 <= maxyear);
				},
				determinebaseyear: function(minyear, maxyear, hint) {
					var currentyear = new Date().getFullYear();
					if (minyear > currentyear) return minyear;
					if (maxyear < currentyear) {
						for (var maxYearPrefix = maxyear.toString().slice(0, 2), maxYearPostfix = maxyear.toString().slice(2, 4); maxyear < maxYearPrefix + hint; ) maxYearPrefix--;
						var maxxYear = maxYearPrefix + maxYearPostfix;
						return minyear > maxxYear ? minyear : maxxYear;
					}
					if (minyear <= currentyear && currentyear <= maxyear) {
						for (var currentYearPrefix = currentyear.toString().slice(0, 2); maxyear < currentYearPrefix + hint; ) currentYearPrefix--;
						var currentYearAndHint = currentYearPrefix + hint;
						return currentYearAndHint < minyear ? minyear : currentYearAndHint;
					}
					return currentyear;
				},
				onKeyDown: function(e, buffer, caretPos, opts) {
					var $input = $(this);
					if (e.ctrlKey && e.keyCode === Inputmask.keyCode.RIGHT) {
						var today = new Date();
						$input.val(today.getDate().toString() + (today.getMonth() + 1).toString() + today.getFullYear().toString()),
							$input.trigger("setvalue");
					}
				},
				getFrontValue: function(mask, buffer, opts) {
					for (var start = 0, length = 0, i = 0; i < mask.length && "2" !== mask.charAt(i); i++) {
						var definition = opts.definitions[mask.charAt(i)];
						definition ? (start += length, length = definition.cardinality) : length++;
					}
					return buffer.join("").substr(start, length);
				},
				postValidation: function(buffer, currentResult, opts) {
					var dayMonthValue, year, bufferStr = buffer.join("");
					return 0 === opts.mask.indexOf("y") ? (year = bufferStr.substr(0, 4), dayMonthValue = bufferStr.substring(4, 10)) : (year = bufferStr.substring(6, 10),
						dayMonthValue = bufferStr.substr(0, 6)), currentResult && (dayMonthValue !== opts.leapday || isLeapYear(year));
				},
				definitions: {
					"1": {
						validator: function(chrs, maskset, pos, strict, opts) {
							if ("3" == chrs.charAt(0)) {
								if (new RegExp("[2-9]").test(chrs.charAt(1))) return chrs = "30", maskset.buffer[pos] = "0",
									pos++, {
									pos: pos
								};
							}
							var isValid = opts.regex.val1.test(chrs);
							return strict || isValid || chrs.charAt(1) !== opts.separator && -1 === "-./".indexOf(chrs.charAt(1)) || !(isValid = opts.regex.val1.test("0" + chrs.charAt(0))) ? isValid : (maskset.buffer[pos - 1] = "0",
							{
								refreshFromBuffer: {
									start: pos - 1,
									end: pos
								},
								pos: pos,
								c: chrs.charAt(0)
							});
						},
						cardinality: 2,
						prevalidator: [ {
							validator: function(chrs, maskset, pos, strict, opts) {
								var pchrs = chrs;
								isNaN(maskset.buffer[pos + 1]) || (pchrs += maskset.buffer[pos + 1]);
								var isValid = 1 === pchrs.length ? opts.regex.val1pre.test(pchrs) : opts.regex.val1.test(pchrs);
								if (!strict && !isValid) {
									if (isValid = opts.regex.val1.test(chrs + "0")) return maskset.buffer[pos] = chrs,
										maskset.buffer[++pos] = "0", {
										pos: pos,
										c: "0"
									};
									if (isValid = opts.regex.val1.test("0" + chrs)) return maskset.buffer[pos] = "0",
										pos++, {
										pos: pos
									};
								}
								return isValid;
							},
							cardinality: 1
						} ]
					},
					"2": {
						validator: function(chrs, maskset, pos, strict, opts) {
							var frontValue = opts.getFrontValue(maskset.mask, maskset.buffer, opts);
							if (-1 !== frontValue.indexOf(opts.placeholder[0]) && (frontValue = "01" + opts.separator),
								"1" == chrs.charAt(0)) {
								if (new RegExp("[3-9]").test(chrs.charAt(1))) return chrs = "10", maskset.buffer[pos] = "0",
									pos++, {
									pos: pos
								};
							}
							var isValid = opts.regex.val2(opts.separator).test(frontValue + chrs);
							return strict || isValid || chrs.charAt(1) !== opts.separator && -1 === "-./".indexOf(chrs.charAt(1)) || !(isValid = opts.regex.val2(opts.separator).test(frontValue + "0" + chrs.charAt(0))) ? isValid : (maskset.buffer[pos - 1] = "0",
							{
								refreshFromBuffer: {
									start: pos - 1,
									end: pos
								},
								pos: pos,
								c: chrs.charAt(0)
							});
						},
						cardinality: 2,
						prevalidator: [ {
							validator: function(chrs, maskset, pos, strict, opts) {
								isNaN(maskset.buffer[pos + 1]) || (chrs += maskset.buffer[pos + 1]);
								var frontValue = opts.getFrontValue(maskset.mask, maskset.buffer, opts);
								-1 !== frontValue.indexOf(opts.placeholder[0]) && (frontValue = "01" + opts.separator);
								var isValid = 1 === chrs.length ? opts.regex.val2pre(opts.separator).test(frontValue + chrs) : opts.regex.val2(opts.separator).test(frontValue + chrs);
								return strict || isValid || !(isValid = opts.regex.val2(opts.separator).test(frontValue + "0" + chrs)) ? isValid : (maskset.buffer[pos] = "0",
									pos++, {
									pos: pos
								});
							},
							cardinality: 1
						} ]
					},
					y: {
						validator: function(chrs, maskset, pos, strict, opts) {
							return opts.isInYearRange(chrs, opts.yearrange.minyear, opts.yearrange.maxyear);
						},
						cardinality: 4,
						prevalidator: [ {
							validator: function(chrs, maskset, pos, strict, opts) {
								var isValid = opts.isInYearRange(chrs, opts.yearrange.minyear, opts.yearrange.maxyear);
								if (!strict && !isValid) {
									var yearPrefix = opts.determinebaseyear(opts.yearrange.minyear, opts.yearrange.maxyear, chrs + "0").toString().slice(0, 1);
									if (isValid = opts.isInYearRange(yearPrefix + chrs, opts.yearrange.minyear, opts.yearrange.maxyear)) return maskset.buffer[pos++] = yearPrefix.charAt(0),
									{
										pos: pos
									};
									if (yearPrefix = opts.determinebaseyear(opts.yearrange.minyear, opts.yearrange.maxyear, chrs + "0").toString().slice(0, 2),
											isValid = opts.isInYearRange(yearPrefix + chrs, opts.yearrange.minyear, opts.yearrange.maxyear)) return maskset.buffer[pos++] = yearPrefix.charAt(0),
										maskset.buffer[pos++] = yearPrefix.charAt(1), {
										pos: pos
									};
								}
								return isValid;
							},
							cardinality: 1
						}, {
							validator: function(chrs, maskset, pos, strict, opts) {
								var isValid = opts.isInYearRange(chrs, opts.yearrange.minyear, opts.yearrange.maxyear);
								if (!strict && !isValid) {
									var yearPrefix = opts.determinebaseyear(opts.yearrange.minyear, opts.yearrange.maxyear, chrs).toString().slice(0, 2);
									if (isValid = opts.isInYearRange(chrs[0] + yearPrefix[1] + chrs[1], opts.yearrange.minyear, opts.yearrange.maxyear)) return maskset.buffer[pos++] = yearPrefix.charAt(1),
									{
										pos: pos
									};
									if (yearPrefix = opts.determinebaseyear(opts.yearrange.minyear, opts.yearrange.maxyear, chrs).toString().slice(0, 2),
											isValid = opts.isInYearRange(yearPrefix + chrs, opts.yearrange.minyear, opts.yearrange.maxyear)) return maskset.buffer[pos - 1] = yearPrefix.charAt(0),
										maskset.buffer[pos++] = yearPrefix.charAt(1), maskset.buffer[pos++] = chrs.charAt(0),
									{
										refreshFromBuffer: {
											start: pos - 3,
											end: pos
										},
										pos: pos
									};
								}
								return isValid;
							},
							cardinality: 2
						}, {
							validator: function(chrs, maskset, pos, strict, opts) {
								return opts.isInYearRange(chrs, opts.yearrange.minyear, opts.yearrange.maxyear);
							},
							cardinality: 3
						} ]
					}
				},
				insertMode: !1,
				autoUnmask: !1
			},
			"mm/dd/yyyy": {
				placeholder: "mm/dd/yyyy",
				alias: "dd/mm/yyyy",
				regex: {
					val2pre: function(separator) {
						var escapedSeparator = Inputmask.escapeRegex.call(this, separator);
						return new RegExp("((0[13-9]|1[012])" + escapedSeparator + "[0-3])|(02" + escapedSeparator + "[0-2])");
					},
					val2: function(separator) {
						var escapedSeparator = Inputmask.escapeRegex.call(this, separator);
						return new RegExp("((0[1-9]|1[012])" + escapedSeparator + "(0[1-9]|[12][0-9]))|((0[13-9]|1[012])" + escapedSeparator + "30)|((0[13578]|1[02])" + escapedSeparator + "31)");
					},
					val1pre: new RegExp("[01]"),
					val1: new RegExp("0[1-9]|1[012]")
				},
				leapday: "02/29/",
				onKeyDown: function(e, buffer, caretPos, opts) {
					var $input = $(this);
					if (e.ctrlKey && e.keyCode === Inputmask.keyCode.RIGHT) {
						var today = new Date();
						$input.val((today.getMonth() + 1).toString() + today.getDate().toString() + today.getFullYear().toString()),
							$input.trigger("setvalue");
					}
				}
			},
			"yyyy/mm/dd": {
				mask: "y/1/2",
				placeholder: "yyyy/mm/dd",
				alias: "mm/dd/yyyy",
				leapday: "/02/29",
				onKeyDown: function(e, buffer, caretPos, opts) {
					var $input = $(this);
					if (e.ctrlKey && e.keyCode === Inputmask.keyCode.RIGHT) {
						var today = new Date();
						$input.val(today.getFullYear().toString() + (today.getMonth() + 1).toString() + today.getDate().toString()),
							$input.trigger("setvalue");
					}
				}
			},
			"dd.mm.yyyy": {
				mask: "1.2.y",
				placeholder: "dd.mm.yyyy",
				leapday: "29.02.",
				separator: ".",
				alias: "dd/mm/yyyy"
			},
			"dd-mm-yyyy": {
				mask: "1-2-y",
				placeholder: "dd-mm-yyyy",
				leapday: "29-02-",
				separator: "-",
				alias: "dd/mm/yyyy"
			},
			"mm.dd.yyyy": {
				mask: "1.2.y",
				placeholder: "mm.dd.yyyy",
				leapday: "02.29.",
				separator: ".",
				alias: "mm/dd/yyyy"
			},
			"mm-dd-yyyy": {
				mask: "1-2-y",
				placeholder: "mm-dd-yyyy",
				leapday: "02-29-",
				separator: "-",
				alias: "mm/dd/yyyy"
			},
			"yyyy.mm.dd": {
				mask: "y.1.2",
				placeholder: "yyyy.mm.dd",
				leapday: ".02.29",
				separator: ".",
				alias: "yyyy/mm/dd"
			},
			"yyyy-mm-dd": {
				mask: "y-1-2",
				placeholder: "yyyy-mm-dd",
				leapday: "-02-29",
				separator: "-",
				alias: "yyyy/mm/dd"
			},
			datetime: {
				mask: "1/2/y h:s",
				placeholder: "dd/mm/yyyy hh:mm",
				alias: "dd/mm/yyyy",
				regex: {
					hrspre: new RegExp("[012]"),
					hrs24: new RegExp("2[0-4]|1[3-9]"),
					hrs: new RegExp("[01][0-9]|2[0-4]"),
					ampm: new RegExp("^[a|p|A|P][m|M]"),
					mspre: new RegExp("[0-5]"),
					ms: new RegExp("[0-5][0-9]")
				},
				timeseparator: ":",
				hourFormat: "24",
				definitions: {
					h: {
						validator: function(chrs, maskset, pos, strict, opts) {
							if ("24" === opts.hourFormat && 24 === parseInt(chrs, 10)) return maskset.buffer[pos - 1] = "0",
								maskset.buffer[pos] = "0", {
								refreshFromBuffer: {
									start: pos - 1,
									end: pos
								},
								c: "0"
							};
							var isValid = opts.regex.hrs.test(chrs);
							if (!strict && !isValid && (chrs.charAt(1) === opts.timeseparator || -1 !== "-.:".indexOf(chrs.charAt(1))) && (isValid = opts.regex.hrs.test("0" + chrs.charAt(0)))) return maskset.buffer[pos - 1] = "0",
								maskset.buffer[pos] = chrs.charAt(0), pos++, {
								refreshFromBuffer: {
									start: pos - 2,
									end: pos
								},
								pos: pos,
								c: opts.timeseparator
							};
							if (isValid && "24" !== opts.hourFormat && opts.regex.hrs24.test(chrs)) {
								var tmp = parseInt(chrs, 10);
								return 24 === tmp ? (maskset.buffer[pos + 5] = "a", maskset.buffer[pos + 6] = "m") : (maskset.buffer[pos + 5] = "p",
									maskset.buffer[pos + 6] = "m"), tmp -= 12, tmp < 10 ? (maskset.buffer[pos] = tmp.toString(),
									maskset.buffer[pos - 1] = "0") : (maskset.buffer[pos] = tmp.toString().charAt(1),
									maskset.buffer[pos - 1] = tmp.toString().charAt(0)), {
									refreshFromBuffer: {
										start: pos - 1,
										end: pos + 6
									},
									c: maskset.buffer[pos]
								};
							}
							return isValid;
						},
						cardinality: 2,
						prevalidator: [ {
							validator: function(chrs, maskset, pos, strict, opts) {
								var isValid = opts.regex.hrspre.test(chrs);
								return strict || isValid || !(isValid = opts.regex.hrs.test("0" + chrs)) ? isValid : (maskset.buffer[pos] = "0",
									pos++, {
									pos: pos
								});
							},
							cardinality: 1
						} ]
					},
					s: {
						validator: "[0-5][0-9]",
						cardinality: 2,
						prevalidator: [ {
							validator: function(chrs, maskset, pos, strict, opts) {
								var isValid = opts.regex.mspre.test(chrs);
								return strict || isValid || !(isValid = opts.regex.ms.test("0" + chrs)) ? isValid : (maskset.buffer[pos] = "0",
									pos++, {
									pos: pos
								});
							},
							cardinality: 1
						} ]
					},
					t: {
						validator: function(chrs, maskset, pos, strict, opts) {
							return opts.regex.ampm.test(chrs + "m");
						},
						casing: "lower",
						cardinality: 1
					}
				},
				insertMode: !1,
				autoUnmask: !1
			},
			datetime12: {
				mask: "1/2/y h:s t\\m",
				placeholder: "dd/mm/yyyy hh:mm xm",
				alias: "datetime",
				hourFormat: "12"
			},
			"mm/dd/yyyy hh:mm xm": {
				mask: "1/2/y h:s t\\m",
				placeholder: "mm/dd/yyyy hh:mm xm",
				alias: "datetime12",
				regex: {
					val2pre: function(separator) {
						var escapedSeparator = Inputmask.escapeRegex.call(this, separator);
						return new RegExp("((0[13-9]|1[012])" + escapedSeparator + "[0-3])|(02" + escapedSeparator + "[0-2])");
					},
					val2: function(separator) {
						var escapedSeparator = Inputmask.escapeRegex.call(this, separator);
						return new RegExp("((0[1-9]|1[012])" + escapedSeparator + "(0[1-9]|[12][0-9]))|((0[13-9]|1[012])" + escapedSeparator + "30)|((0[13578]|1[02])" + escapedSeparator + "31)");
					},
					val1pre: new RegExp("[01]"),
					val1: new RegExp("0[1-9]|1[012]")
				},
				leapday: "02/29/",
				onKeyDown: function(e, buffer, caretPos, opts) {
					var $input = $(this);
					if (e.ctrlKey && e.keyCode === Inputmask.keyCode.RIGHT) {
						var today = new Date();
						$input.val((today.getMonth() + 1).toString() + today.getDate().toString() + today.getFullYear().toString()),
							$input.trigger("setvalue");
					}
				}
			},
			"hh:mm t": {
				mask: "h:s t\\m",
				placeholder: "hh:mm xm",
				alias: "datetime",
				hourFormat: "12"
			},
			"h:s t": {
				mask: "h:s t\\m",
				placeholder: "hh:mm xm",
				alias: "datetime",
				hourFormat: "12"
			},
			"hh:mm:ss": {
				mask: "h:s:s",
				placeholder: "hh:mm:ss",
				alias: "datetime",
				autoUnmask: !1
			},
			"hh:mm": {
				mask: "h:s",
				placeholder: "hh:mm",
				alias: "datetime",
				autoUnmask: !1
			},
			date: {
				alias: "dd/mm/yyyy"
			},
			"mm/yyyy": {
				mask: "1/y",
				placeholder: "mm/yyyy",
				leapday: "donotuse",
				separator: "/",
				alias: "mm/dd/yyyy"
			},
			shamsi: {
				regex: {
					val2pre: function(separator) {
						var escapedSeparator = Inputmask.escapeRegex.call(this, separator);
						return new RegExp("((0[1-9]|1[012])" + escapedSeparator + "[0-3])");
					},
					val2: function(separator) {
						var escapedSeparator = Inputmask.escapeRegex.call(this, separator);
						return new RegExp("((0[1-9]|1[012])" + escapedSeparator + "(0[1-9]|[12][0-9]))|((0[1-9]|1[012])" + escapedSeparator + "30)|((0[1-6])" + escapedSeparator + "31)");
					},
					val1pre: new RegExp("[01]"),
					val1: new RegExp("0[1-9]|1[012]")
				},
				yearrange: {
					minyear: 1300,
					maxyear: 1499
				},
				mask: "y/1/2",
				leapday: "/12/30",
				placeholder: "yyyy/mm/dd",
				alias: "mm/dd/yyyy",
				clearIncomplete: !0
			},
			"yyyy-mm-dd hh:mm:ss": {
				mask: "y-1-2 h:s:s",
				placeholder: "yyyy-mm-dd hh:mm:ss",
				alias: "datetime",
				separator: "-",
				leapday: "-02-29",
				regex: {
					val2pre: function(separator) {
						var escapedSeparator = Inputmask.escapeRegex.call(this, separator);
						return new RegExp("((0[13-9]|1[012])" + escapedSeparator + "[0-3])|(02" + escapedSeparator + "[0-2])");
					},
					val2: function(separator) {
						var escapedSeparator = Inputmask.escapeRegex.call(this, separator);
						return new RegExp("((0[1-9]|1[012])" + escapedSeparator + "(0[1-9]|[12][0-9]))|((0[13-9]|1[012])" + escapedSeparator + "30)|((0[13578]|1[02])" + escapedSeparator + "31)");
					},
					val1pre: new RegExp("[01]"),
					val1: new RegExp("0[1-9]|1[012]")
				},
				onKeyDown: function(e, buffer, caretPos, opts) {}
			}
		}), Inputmask;
	});
}, function(module, exports, __webpack_require__) {
	"use strict";
	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;
	"function" == typeof Symbol && Symbol.iterator;
	!function(factory) {
		__WEBPACK_AMD_DEFINE_ARRAY__ = [ __webpack_require__(0), __webpack_require__(1) ],
			__WEBPACK_AMD_DEFINE_FACTORY__ = factory, void 0 !== (__WEBPACK_AMD_DEFINE_RESULT__ = "function" == typeof __WEBPACK_AMD_DEFINE_FACTORY__ ? __WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__) : __WEBPACK_AMD_DEFINE_FACTORY__) && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__);
	}(function($, Inputmask) {
		return Inputmask.extendDefinitions({
			A: {
				validator: "[A-Za-z\u0410-\u044f\u0401\u0451\xc0-\xff\xb5]",
				cardinality: 1,
				casing: "upper"
			},
			"&": {
				validator: "[0-9A-Za-z\u0410-\u044f\u0401\u0451\xc0-\xff\xb5]",
				cardinality: 1,
				casing: "upper"
			},
			"#": {
				validator: "[0-9A-Fa-f]",
				cardinality: 1,
				casing: "upper"
			}
		}), Inputmask.extendAliases({
			url: {
				definitions: {
					i: {
						validator: ".",
						cardinality: 1
					}
				},
				mask: "(\\http://)|(\\http\\s://)|(ftp://)|(ftp\\s://)i{+}",
				insertMode: !1,
				autoUnmask: !1,
				inputmode: "url"
			},
			ip: {
				mask: "i[i[i]].i[i[i]].i[i[i]].i[i[i]]",
				definitions: {
					i: {
						validator: function(chrs, maskset, pos, strict, opts) {
							return pos - 1 > -1 && "." !== maskset.buffer[pos - 1] ? (chrs = maskset.buffer[pos - 1] + chrs,
								chrs = pos - 2 > -1 && "." !== maskset.buffer[pos - 2] ? maskset.buffer[pos - 2] + chrs : "0" + chrs) : chrs = "00" + chrs,
								new RegExp("25[0-5]|2[0-4][0-9]|[01][0-9][0-9]").test(chrs);
						},
						cardinality: 1
					}
				},
				onUnMask: function(maskedValue, unmaskedValue, opts) {
					return maskedValue;
				},
				inputmode: "numeric"
			},
			email: {
				mask: "*{1,64}[.*{1,64}][.*{1,64}][.*{1,63}]@-{1,63}.-{1,63}[.-{1,63}][.-{1,63}]",
				greedy: !1,
				onBeforePaste: function(pastedValue, opts) {
					return pastedValue = pastedValue.toLowerCase(), pastedValue.replace("mailto:", "");
				},
				definitions: {
					"*": {
						validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~-]",
						cardinality: 1,
						casing: "lower"
					},
					"-": {
						validator: "[0-9A-Za-z-]",
						cardinality: 1,
						casing: "lower"
					}
				},
				onUnMask: function(maskedValue, unmaskedValue, opts) {
					return maskedValue;
				},
				inputmode: "email"
			},
			mac: {
				mask: "##:##:##:##:##:##"
			},
			vin: {
				mask: "V{13}9{4}",
				definitions: {
					V: {
						validator: "[A-HJ-NPR-Za-hj-npr-z\\d]",
						cardinality: 1,
						casing: "upper"
					}
				},
				clearIncomplete: !0,
				autoUnmask: !0
			}
		}), Inputmask;
	});
}, function(module, exports, __webpack_require__) {
	"use strict";
	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;
	"function" == typeof Symbol && Symbol.iterator;
	!function(factory) {
		__WEBPACK_AMD_DEFINE_ARRAY__ = [ __webpack_require__(0), __webpack_require__(1) ],
			__WEBPACK_AMD_DEFINE_FACTORY__ = factory, void 0 !== (__WEBPACK_AMD_DEFINE_RESULT__ = "function" == typeof __WEBPACK_AMD_DEFINE_FACTORY__ ? __WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__) : __WEBPACK_AMD_DEFINE_FACTORY__) && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__);
	}(function($, Inputmask, undefined) {
		function autoEscape(txt, opts) {
			for (var escapedTxt = "", i = 0; i < txt.length; i++) Inputmask.prototype.definitions[txt.charAt(i)] || opts.definitions[txt.charAt(i)] || opts.optionalmarker.start === txt.charAt(i) || opts.optionalmarker.end === txt.charAt(i) || opts.quantifiermarker.start === txt.charAt(i) || opts.quantifiermarker.end === txt.charAt(i) || opts.groupmarker.start === txt.charAt(i) || opts.groupmarker.end === txt.charAt(i) || opts.alternatormarker === txt.charAt(i) ? escapedTxt += "\\" + txt.charAt(i) : escapedTxt += txt.charAt(i);
			return escapedTxt;
		}
		return Inputmask.extendAliases({
			numeric: {
				mask: function(opts) {
					if (0 !== opts.repeat && isNaN(opts.integerDigits) && (opts.integerDigits = opts.repeat),
							opts.repeat = 0, opts.groupSeparator === opts.radixPoint && opts.digits && "0" !== opts.digits && ("." === opts.radixPoint ? opts.groupSeparator = "," : "," === opts.radixPoint ? opts.groupSeparator = "." : opts.groupSeparator = ""),
						" " === opts.groupSeparator && (opts.skipOptionalPartCharacter = undefined), opts.autoGroup = opts.autoGroup && "" !== opts.groupSeparator,
						opts.autoGroup && ("string" == typeof opts.groupSize && isFinite(opts.groupSize) && (opts.groupSize = parseInt(opts.groupSize)),
							isFinite(opts.integerDigits))) {
						var seps = Math.floor(opts.integerDigits / opts.groupSize), mod = opts.integerDigits % opts.groupSize;
						opts.integerDigits = parseInt(opts.integerDigits) + (0 === mod ? seps - 1 : seps),
						opts.integerDigits < 1 && (opts.integerDigits = "*");
					}
					opts.placeholder.length > 1 && (opts.placeholder = opts.placeholder.charAt(0)),
					"radixFocus" === opts.positionCaretOnClick && "" === opts.placeholder && !1 === opts.integerOptional && (opts.positionCaretOnClick = "lvp"),
						opts.definitions[";"] = opts.definitions["~"], opts.definitions[";"].definitionSymbol = "~",
					!0 === opts.numericInput && (opts.positionCaretOnClick = "radixFocus" === opts.positionCaretOnClick ? "lvp" : opts.positionCaretOnClick,
						opts.digitsOptional = !1, isNaN(opts.digits) && (opts.digits = 2), opts.decimalProtect = !1);
					var mask = "[+]";
					if (mask += autoEscape(opts.prefix, opts), !0 === opts.integerOptional ? mask += "~{1," + opts.integerDigits + "}" : mask += "~{" + opts.integerDigits + "}",
						opts.digits !== undefined) {
						opts.radixPointDefinitionSymbol = opts.decimalProtect ? ":" : opts.radixPoint;
						var dq = opts.digits.toString().split(",");
						isFinite(dq[0] && dq[1] && isFinite(dq[1])) ? mask += opts.radixPointDefinitionSymbol + ";{" + opts.digits + "}" : (isNaN(opts.digits) || parseInt(opts.digits) > 0) && (opts.digitsOptional ? mask += "[" + opts.radixPointDefinitionSymbol + ";{1," + opts.digits + "}]" : mask += opts.radixPointDefinitionSymbol + ";{" + opts.digits + "}");
					}
					return mask += autoEscape(opts.suffix, opts), mask += "[-]", opts.greedy = !1, mask;
				},
				placeholder: "",
				greedy: !1,
				digits: "*",
				digitsOptional: !0,
				enforceDigitsOnBlur: !1,
				radixPoint: ".",
				positionCaretOnClick: "radixFocus",
				groupSize: 3,
				groupSeparator: "",
				autoGroup: !1,
				allowMinus: !0,
				negationSymbol: {
					front: "-",
					back: ""
				},
				integerDigits: "+",
				integerOptional: !0,
				prefix: "",
				suffix: "",
				rightAlign: !0,
				decimalProtect: !0,
				min: null,
				max: null,
				step: 1,
				insertMode: !0,
				autoUnmask: !1,
				unmaskAsNumber: !1,
				inputmode: "numeric",
				preValidation: function(buffer, pos, c, isSelection, opts) {
					if ("-" === c || c === opts.negationSymbol.front) return !0 === opts.allowMinus && (opts.isNegative = opts.isNegative === undefined || !opts.isNegative,
						"" === buffer.join("") || {
							caret: pos,
							dopost: !0
						});
					if (!1 === isSelection && c === opts.radixPoint && opts.digits !== undefined && (isNaN(opts.digits) || parseInt(opts.digits) > 0)) {
						var radixPos = $.inArray(opts.radixPoint, buffer);
						if (-1 !== radixPos) return !0 === opts.numericInput ? pos === radixPos : {
							caret: radixPos + 1
						};
					}
					return !0;
				},
				postValidation: function(buffer, currentResult, opts) {
					var suffix = opts.suffix.split(""), prefix = opts.prefix.split("");
					if (currentResult.pos === undefined && currentResult.caret !== undefined && !0 !== currentResult.dopost) return currentResult;
					var caretPos = currentResult.caret !== undefined ? currentResult.caret : currentResult.pos, maskedValue = buffer.slice();
					opts.numericInput && (caretPos = maskedValue.length - caretPos - 1, maskedValue = maskedValue.reverse());
					var charAtPos = maskedValue[caretPos];
					if (charAtPos === opts.groupSeparator && (caretPos += 1, charAtPos = maskedValue[caretPos]),
						caretPos === maskedValue.length - opts.suffix.length - 1 && charAtPos === opts.radixPoint) return currentResult;
					charAtPos !== undefined && charAtPos !== opts.radixPoint && charAtPos !== opts.negationSymbol.front && charAtPos !== opts.negationSymbol.back && (maskedValue[caretPos] = "?",
						opts.prefix.length > 0 && caretPos >= (!1 === opts.isNegative ? 1 : 0) && caretPos < opts.prefix.length - 1 + (!1 === opts.isNegative ? 1 : 0) ? prefix[caretPos - (!1 === opts.isNegative ? 1 : 0)] = "?" : opts.suffix.length > 0 && caretPos >= maskedValue.length - opts.suffix.length - (!1 === opts.isNegative ? 1 : 0) && (suffix[caretPos - (maskedValue.length - opts.suffix.length - (!1 === opts.isNegative ? 1 : 0))] = "?")),
						prefix = prefix.join(""), suffix = suffix.join("");
					var processValue = maskedValue.join("").replace(prefix, "");
					if (processValue = processValue.replace(suffix, ""), processValue = processValue.replace(new RegExp(Inputmask.escapeRegex(opts.groupSeparator), "g"), ""),
							processValue = processValue.replace(new RegExp("[-" + Inputmask.escapeRegex(opts.negationSymbol.front) + "]", "g"), ""),
							processValue = processValue.replace(new RegExp(Inputmask.escapeRegex(opts.negationSymbol.back) + "$"), ""),
						isNaN(opts.placeholder) && (processValue = processValue.replace(new RegExp(Inputmask.escapeRegex(opts.placeholder), "g"), "")),
						processValue.length > 1 && 1 !== processValue.indexOf(opts.radixPoint) && ("0" === charAtPos && (processValue = processValue.replace(/^\?/g, "")),
							processValue = processValue.replace(/^0/g, "")), processValue.charAt(0) === opts.radixPoint && "" !== opts.radixPoint && !0 !== opts.numericInput && (processValue = "0" + processValue),
						"" !== processValue) {
						if (processValue = processValue.split(""), (!opts.digitsOptional || opts.enforceDigitsOnBlur && "blur" === currentResult.event) && isFinite(opts.digits)) {
							var radixPosition = $.inArray(opts.radixPoint, processValue), rpb = $.inArray(opts.radixPoint, maskedValue);
							-1 === radixPosition && (processValue.push(opts.radixPoint), radixPosition = processValue.length - 1);
							for (var i = 1; i <= opts.digits; i++) opts.digitsOptional && (!opts.enforceDigitsOnBlur || "blur" !== currentResult.event) || processValue[radixPosition + i] !== undefined && processValue[radixPosition + i] !== opts.placeholder.charAt(0) ? -1 !== rpb && maskedValue[rpb + i] !== undefined && (processValue[radixPosition + i] = processValue[radixPosition + i] || maskedValue[rpb + i]) : processValue[radixPosition + i] = currentResult.placeholder || opts.placeholder.charAt(0);
						}
						if (!0 !== opts.autoGroup || "" === opts.groupSeparator || charAtPos === opts.radixPoint && currentResult.pos === undefined && !currentResult.dopost) processValue = processValue.join(""); else {
							var addRadix = processValue[processValue.length - 1] === opts.radixPoint && currentResult.c === opts.radixPoint;
							processValue = Inputmask(function(buffer, opts) {
								var postMask = "";
								if (postMask += "(" + opts.groupSeparator + "*{" + opts.groupSize + "}){*}", "" !== opts.radixPoint) {
									var radixSplit = buffer.join("").split(opts.radixPoint);
									radixSplit[1] && (postMask += opts.radixPoint + "*{" + radixSplit[1].match(/^\d*\??\d*/)[0].length + "}");
								}
								return postMask;
							}(processValue, opts), {
								numericInput: !0,
								jitMasking: !0,
								definitions: {
									"*": {
										validator: "[0-9?]",
										cardinality: 1
									}
								}
							}).format(processValue.join("")), addRadix && (processValue += opts.radixPoint),
							processValue.charAt(0) === opts.groupSeparator && processValue.substr(1);
						}
					}
					if (opts.isNegative && "blur" === currentResult.event && (opts.isNegative = "0" !== processValue),
							processValue = prefix + processValue, processValue += suffix, opts.isNegative && (processValue = opts.negationSymbol.front + processValue,
							processValue += opts.negationSymbol.back), processValue = processValue.split(""),
						charAtPos !== undefined) if (charAtPos !== opts.radixPoint && charAtPos !== opts.negationSymbol.front && charAtPos !== opts.negationSymbol.back) caretPos = $.inArray("?", processValue),
						caretPos > -1 ? processValue[caretPos] = charAtPos : caretPos = currentResult.caret || 0; else if (charAtPos === opts.radixPoint || charAtPos === opts.negationSymbol.front || charAtPos === opts.negationSymbol.back) {
						var newCaretPos = $.inArray(charAtPos, processValue);
						-1 !== newCaretPos && (caretPos = newCaretPos);
					}
					opts.numericInput && (caretPos = processValue.length - caretPos - 1, processValue = processValue.reverse());
					var rslt = {
						caret: charAtPos === undefined || currentResult.pos !== undefined ? caretPos + (opts.numericInput ? -1 : 1) : caretPos,
						buffer: processValue,
						refreshFromBuffer: currentResult.dopost || buffer.join("") !== processValue.join("")
					};
					return rslt.refreshFromBuffer ? rslt : currentResult;
				},
				onBeforeWrite: function(e, buffer, caretPos, opts) {
					if (e) switch (e.type) {
						case "keydown":
							return opts.postValidation(buffer, {
								caret: caretPos,
								dopost: !0
							}, opts);

						case "blur":
						case "checkval":
							var unmasked;
							if (function(opts) {
									opts.parseMinMaxOptions === undefined && (null !== opts.min && (opts.min = opts.min.toString().replace(new RegExp(Inputmask.escapeRegex(opts.groupSeparator), "g"), ""),
									"," === opts.radixPoint && (opts.min = opts.min.replace(opts.radixPoint, ".")),
										opts.min = isFinite(opts.min) ? parseFloat(opts.min) : NaN, isNaN(opts.min) && (opts.min = Number.MIN_VALUE)),
									null !== opts.max && (opts.max = opts.max.toString().replace(new RegExp(Inputmask.escapeRegex(opts.groupSeparator), "g"), ""),
									"," === opts.radixPoint && (opts.max = opts.max.replace(opts.radixPoint, ".")),
										opts.max = isFinite(opts.max) ? parseFloat(opts.max) : NaN, isNaN(opts.max) && (opts.max = Number.MAX_VALUE)),
										opts.parseMinMaxOptions = "done");
								}(opts), null !== opts.min || null !== opts.max) {
								if (unmasked = opts.onUnMask(buffer.join(""), undefined, $.extend({}, opts, {
										unmaskAsNumber: !0
									})), null !== opts.min && unmasked < opts.min) return opts.isNegative = opts.min < 0,
									opts.postValidation(opts.min.toString().replace(".", opts.radixPoint).split(""), {
										caret: caretPos,
										dopost: !0,
										placeholder: "0"
									}, opts);
								if (null !== opts.max && unmasked > opts.max) return opts.isNegative = opts.max < 0,
									opts.postValidation(opts.max.toString().replace(".", opts.radixPoint).split(""), {
										caret: caretPos,
										dopost: !0,
										placeholder: "0"
									}, opts);
							}
							return opts.postValidation(buffer, {
								caret: caretPos,
								placeholder: "0",
								event: "blur"
							}, opts);

						case "_checkval":
							return {
								caret: caretPos
							};
					}
				},
				regex: {
					integerPart: function(opts, emptyCheck) {
						return emptyCheck ? new RegExp("[" + Inputmask.escapeRegex(opts.negationSymbol.front) + "+]?") : new RegExp("[" + Inputmask.escapeRegex(opts.negationSymbol.front) + "+]?\\d+");
					},
					integerNPart: function(opts) {
						return new RegExp("[\\d" + Inputmask.escapeRegex(opts.groupSeparator) + Inputmask.escapeRegex(opts.placeholder.charAt(0)) + "]+");
					}
				},
				definitions: {
					"~": {
						validator: function(chrs, maskset, pos, strict, opts, isSelection) {
							var isValid = strict ? new RegExp("[0-9" + Inputmask.escapeRegex(opts.groupSeparator) + "]").test(chrs) : new RegExp("[0-9]").test(chrs);
							if (!0 === isValid) {
								if (!0 !== opts.numericInput && maskset.validPositions[pos] !== undefined && "~" === maskset.validPositions[pos].match.def && !isSelection) {
									var processValue = maskset.buffer.join("");
									processValue = processValue.replace(new RegExp("[-" + Inputmask.escapeRegex(opts.negationSymbol.front) + "]", "g"), ""),
										processValue = processValue.replace(new RegExp(Inputmask.escapeRegex(opts.negationSymbol.back) + "$"), "");
									var pvRadixSplit = processValue.split(opts.radixPoint);
									pvRadixSplit.length > 1 && (pvRadixSplit[1] = pvRadixSplit[1].replace(/0/g, opts.placeholder.charAt(0))),
									"0" === pvRadixSplit[0] && (pvRadixSplit[0] = pvRadixSplit[0].replace(/0/g, opts.placeholder.charAt(0))),
										processValue = pvRadixSplit[0] + opts.radixPoint + pvRadixSplit[1] || "";
									var bufferTemplate = maskset._buffer.join("");
									for (processValue === opts.radixPoint && (processValue = bufferTemplate); null === processValue.match(Inputmask.escapeRegex(bufferTemplate) + "$"); ) bufferTemplate = bufferTemplate.slice(1);
									processValue = processValue.replace(bufferTemplate, ""), processValue = processValue.split(""),
										isValid = processValue[pos] === undefined ? {
											pos: pos,
											remove: pos
										} : {
											pos: pos
										};
								}
							} else strict || chrs !== opts.radixPoint || maskset.validPositions[pos - 1] !== undefined || (maskset.buffer[pos] = "0",
								isValid = {
									pos: pos + 1
								});
							return isValid;
						},
						cardinality: 1
					},
					"+": {
						validator: function(chrs, maskset, pos, strict, opts) {
							return opts.allowMinus && ("-" === chrs || chrs === opts.negationSymbol.front);
						},
						cardinality: 1,
						placeholder: ""
					},
					"-": {
						validator: function(chrs, maskset, pos, strict, opts) {
							return opts.allowMinus && chrs === opts.negationSymbol.back;
						},
						cardinality: 1,
						placeholder: ""
					},
					":": {
						validator: function(chrs, maskset, pos, strict, opts) {
							var radix = "[" + Inputmask.escapeRegex(opts.radixPoint) + "]", isValid = new RegExp(radix).test(chrs);
							return isValid && maskset.validPositions[pos] && maskset.validPositions[pos].match.placeholder === opts.radixPoint && (isValid = {
								caret: pos + 1
							}), isValid;
						},
						cardinality: 1,
						placeholder: function(opts) {
							return opts.radixPoint;
						}
					}
				},
				onUnMask: function(maskedValue, unmaskedValue, opts) {
					if ("" === unmaskedValue && !0 === opts.nullable) return unmaskedValue;
					var processValue = maskedValue.replace(opts.prefix, "");
					return processValue = processValue.replace(opts.suffix, ""), processValue = processValue.replace(new RegExp(Inputmask.escapeRegex(opts.groupSeparator), "g"), ""),
					"" !== opts.placeholder.charAt(0) && (processValue = processValue.replace(new RegExp(opts.placeholder.charAt(0), "g"), "0")),
						opts.unmaskAsNumber ? ("" !== opts.radixPoint && -1 !== processValue.indexOf(opts.radixPoint) && (processValue = processValue.replace(Inputmask.escapeRegex.call(this, opts.radixPoint), ".")),
							processValue = processValue.replace(new RegExp("^" + Inputmask.escapeRegex(opts.negationSymbol.front)), "-"),
							processValue = processValue.replace(new RegExp(Inputmask.escapeRegex(opts.negationSymbol.back) + "$"), ""),
							Number(processValue)) : processValue;
				},
				isComplete: function(buffer, opts) {
					var maskedValue = buffer.join("");
					if (buffer.slice().join("") !== maskedValue) return !1;
					var processValue = maskedValue.replace(opts.prefix, "");
					return processValue = processValue.replace(opts.suffix, ""), processValue = processValue.replace(new RegExp(Inputmask.escapeRegex(opts.groupSeparator), "g"), ""),
					"," === opts.radixPoint && (processValue = processValue.replace(Inputmask.escapeRegex(opts.radixPoint), ".")),
						isFinite(processValue);
				},
				onBeforeMask: function(initialValue, opts) {
					if (opts.isNegative = undefined, initialValue = initialValue.toString().charAt(initialValue.length - 1) === opts.radixPoint ? initialValue.toString().substr(0, initialValue.length - 1) : initialValue.toString(),
						"" !== opts.radixPoint && isFinite(initialValue)) {
						var vs = initialValue.split("."), groupSize = "" !== opts.groupSeparator ? parseInt(opts.groupSize) : 0;
						2 === vs.length && (vs[0].length > groupSize || vs[1].length > groupSize || vs[0].length <= groupSize && vs[1].length < groupSize) && (initialValue = initialValue.replace(".", opts.radixPoint));
					}
					var kommaMatches = initialValue.match(/,/g), dotMatches = initialValue.match(/\./g);
					if (dotMatches && kommaMatches ? dotMatches.length > kommaMatches.length ? (initialValue = initialValue.replace(/\./g, ""),
							initialValue = initialValue.replace(",", opts.radixPoint)) : kommaMatches.length > dotMatches.length ? (initialValue = initialValue.replace(/,/g, ""),
							initialValue = initialValue.replace(".", opts.radixPoint)) : initialValue = initialValue.indexOf(".") < initialValue.indexOf(",") ? initialValue.replace(/\./g, "") : initialValue.replace(/,/g, "") : initialValue = initialValue.replace(new RegExp(Inputmask.escapeRegex(opts.groupSeparator), "g"), ""),
						0 === opts.digits && (-1 !== initialValue.indexOf(".") ? initialValue = initialValue.substring(0, initialValue.indexOf(".")) : -1 !== initialValue.indexOf(",") && (initialValue = initialValue.substring(0, initialValue.indexOf(",")))),
						"" !== opts.radixPoint && isFinite(opts.digits) && -1 !== initialValue.indexOf(opts.radixPoint)) {
						var valueParts = initialValue.split(opts.radixPoint), decPart = valueParts[1].match(new RegExp("\\d*"))[0];
						if (parseInt(opts.digits) < decPart.toString().length) {
							var digitsFactor = Math.pow(10, parseInt(opts.digits));
							initialValue = initialValue.replace(Inputmask.escapeRegex(opts.radixPoint), "."),
								initialValue = Math.round(parseFloat(initialValue) * digitsFactor) / digitsFactor,
								initialValue = initialValue.toString().replace(".", opts.radixPoint);
						}
					}
					return initialValue;
				},
				canClearPosition: function(maskset, position, lvp, strict, opts) {
					var vp = maskset.validPositions[position], canClear = vp.input !== opts.radixPoint || null !== maskset.validPositions[position].match.fn && !1 === opts.decimalProtect || vp.input === opts.radixPoint && maskset.validPositions[position + 1] && null === maskset.validPositions[position + 1].match.fn || isFinite(vp.input) || position === lvp || vp.input === opts.groupSeparator || vp.input === opts.negationSymbol.front || vp.input === opts.negationSymbol.back;
					return !canClear || "+" !== vp.match.nativeDef && "-" !== vp.match.nativeDef || (opts.isNegative = !1),
						canClear;
				},
				onKeyDown: function(e, buffer, caretPos, opts) {
					var $input = $(this);
					if (e.ctrlKey) switch (e.keyCode) {
						case Inputmask.keyCode.UP:
							$input.val(parseFloat(this.inputmask.unmaskedvalue()) + parseInt(opts.step)), $input.trigger("setvalue");
							break;

						case Inputmask.keyCode.DOWN:
							$input.val(parseFloat(this.inputmask.unmaskedvalue()) - parseInt(opts.step)), $input.trigger("setvalue");
					}
				}
			},
			currency: {
				prefix: "$ ",
				groupSeparator: ",",
				alias: "numeric",
				placeholder: "0",
				autoGroup: !0,
				digits: 2,
				digitsOptional: !1,
				clearMaskOnLostFocus: !1
			},
			decimal: {
				alias: "numeric"
			},
			integer: {
				alias: "numeric",
				digits: 0,
				radixPoint: ""
			},
			percentage: {
				alias: "numeric",
				digits: 2,
				digitsOptional: !0,
				radixPoint: ".",
				placeholder: "0",
				autoGroup: !1,
				min: 0,
				max: 100,
				suffix: " %",
				allowMinus: !1
			}
		}), Inputmask;
	});
}, function(module, exports, __webpack_require__) {
	"use strict";
	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;
	"function" == typeof Symbol && Symbol.iterator;
	!function(factory) {
		__WEBPACK_AMD_DEFINE_ARRAY__ = [ __webpack_require__(0), __webpack_require__(1) ],
			__WEBPACK_AMD_DEFINE_FACTORY__ = factory, void 0 !== (__WEBPACK_AMD_DEFINE_RESULT__ = "function" == typeof __WEBPACK_AMD_DEFINE_FACTORY__ ? __WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__) : __WEBPACK_AMD_DEFINE_FACTORY__) && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__);
	}(function($, Inputmask) {
		function maskSort(a, b) {
			var maska = (a.mask || a).replace(/#/g, "9").replace(/\)/, "9").replace(/[+()#-]/g, ""), maskb = (b.mask || b).replace(/#/g, "9").replace(/\)/, "9").replace(/[+()#-]/g, ""), maskas = (a.mask || a).split("#")[0], maskbs = (b.mask || b).split("#")[0];
			return 0 === maskbs.indexOf(maskas) ? -1 : 0 === maskas.indexOf(maskbs) ? 1 : maska.localeCompare(maskb);
		}
		var analyseMaskBase = Inputmask.prototype.analyseMask;
		return Inputmask.prototype.analyseMask = function(mask, regexMask, opts) {
			function reduceVariations(masks, previousVariation, previousmaskGroup) {
				previousVariation = previousVariation || "", previousmaskGroup = previousmaskGroup || maskGroups,
				"" !== previousVariation && (previousmaskGroup[previousVariation] = {});
				for (var variation = "", maskGroup = previousmaskGroup[previousVariation] || previousmaskGroup, i = masks.length - 1; i >= 0; i--) mask = masks[i].mask || masks[i],
					variation = mask.substr(0, 1), maskGroup[variation] = maskGroup[variation] || [],
					maskGroup[variation].unshift(mask.substr(1)), masks.splice(i, 1);
				for (var ndx in maskGroup) maskGroup[ndx].length > 500 && reduceVariations(maskGroup[ndx].slice(), ndx, maskGroup);
			}
			function rebuild(maskGroup) {
				var mask = "", submasks = [];
				for (var ndx in maskGroup) $.isArray(maskGroup[ndx]) ? 1 === maskGroup[ndx].length ? submasks.push(ndx + maskGroup[ndx]) : submasks.push(ndx + opts.groupmarker.start + maskGroup[ndx].join(opts.groupmarker.end + opts.alternatormarker + opts.groupmarker.start) + opts.groupmarker.end) : submasks.push(ndx + rebuild(maskGroup[ndx]));
				return 1 === submasks.length ? mask += submasks[0] : mask += opts.groupmarker.start + submasks.join(opts.groupmarker.end + opts.alternatormarker + opts.groupmarker.start) + opts.groupmarker.end,
					mask;
			}
			var maskGroups = {};
			return opts.phoneCodes && (opts.phoneCodes && opts.phoneCodes.length > 1e3 && (mask = mask.substr(1, mask.length - 2),
				reduceVariations(mask.split(opts.groupmarker.end + opts.alternatormarker + opts.groupmarker.start)),
				mask = rebuild(maskGroups)), mask = mask.replace(/9/g, "\\9")), analyseMaskBase.call(this, mask, regexMask, opts);
		}, Inputmask.extendAliases({
			abstractphone: {
				groupmarker: {
					start: "<",
					end: ">"
				},
				countrycode: "",
				phoneCodes: [],
				mask: function(opts) {
					return opts.definitions = {
						"#": Inputmask.prototype.definitions[9]
					}, opts.phoneCodes.sort(maskSort);
				},
				keepStatic: !0,
				onBeforeMask: function(value, opts) {
					var processedValue = value.replace(/^0{1,2}/, "").replace(/[\s]/g, "");
					return (processedValue.indexOf(opts.countrycode) > 1 || -1 === processedValue.indexOf(opts.countrycode)) && (processedValue = "+" + opts.countrycode + processedValue),
						processedValue;
				},
				onUnMask: function(maskedValue, unmaskedValue, opts) {
					return maskedValue.replace(/[()#-]/g, "");
				},
				inputmode: "tel"
			}
		}), Inputmask;
	});
}, function(module, exports, __webpack_require__) {
	"use strict";
	var __WEBPACK_AMD_DEFINE_FACTORY__, __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__, _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function(obj) {
		return typeof obj;
	} : function(obj) {
		return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
	};
	!function(factory) {
		__WEBPACK_AMD_DEFINE_ARRAY__ = [ __webpack_require__(2), __webpack_require__(1) ],
			__WEBPACK_AMD_DEFINE_FACTORY__ = factory, void 0 !== (__WEBPACK_AMD_DEFINE_RESULT__ = "function" == typeof __WEBPACK_AMD_DEFINE_FACTORY__ ? __WEBPACK_AMD_DEFINE_FACTORY__.apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__) : __WEBPACK_AMD_DEFINE_FACTORY__) && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__);
	}(function($, Inputmask) {
		return void 0 === $.fn.inputmask && ($.fn.inputmask = function(fn, options) {
			var nptmask, input = this[0];
			if (void 0 === options && (options = {}), "string" == typeof fn) switch (fn) {
				case "unmaskedvalue":
					return input && input.inputmask ? input.inputmask.unmaskedvalue() : $(input).val();

				case "remove":
					return this.each(function() {
						this.inputmask && this.inputmask.remove();
					});

				case "getemptymask":
					return input && input.inputmask ? input.inputmask.getemptymask() : "";

				case "hasMaskedValue":
					return !(!input || !input.inputmask) && input.inputmask.hasMaskedValue();

				case "isComplete":
					return !input || !input.inputmask || input.inputmask.isComplete();

				case "getmetadata":
					return input && input.inputmask ? input.inputmask.getmetadata() : void 0;

				case "setvalue":
					$(input).val(options), input && void 0 === input.inputmask && $(input).triggerHandler("setvalue");
					break;

				case "option":
					if ("string" != typeof options) return this.each(function() {
						if (void 0 !== this.inputmask) return this.inputmask.option(options);
					});
					if (input && void 0 !== input.inputmask) return input.inputmask.option(options);
					break;

				default:
					return options.alias = fn, nptmask = new Inputmask(options), this.each(function() {
						nptmask.mask(this);
					});
			} else {
				if ("object" == (void 0 === fn ? "undefined" : _typeof(fn))) return nptmask = new Inputmask(fn),
					void 0 === fn.mask && void 0 === fn.alias ? this.each(function() {
						if (void 0 !== this.inputmask) return this.inputmask.option(fn);
						nptmask.mask(this);
					}) : this.each(function() {
						nptmask.mask(this);
					});
				if (void 0 === fn) return this.each(function() {
					nptmask = new Inputmask(options), nptmask.mask(this);
				});
			}
		}), $.fn.inputmask;
	});
}, function(module, exports, __webpack_require__) {
	var content = __webpack_require__(12);
	"string" == typeof content && (content = [ [ module.i, content, "" ] ]);
	__webpack_require__(14)(content, {});
	content.locals && (module.exports = content.locals);
}, function(module, exports, __webpack_require__) {
	"use strict";
	function _interopRequireDefault(obj) {
		return obj && obj.__esModule ? obj : {
			default: obj
		};
	}
	__webpack_require__(8), __webpack_require__(3), __webpack_require__(4), __webpack_require__(5),
		__webpack_require__(6);
	var _inputmask = __webpack_require__(1), _inputmask2 = _interopRequireDefault(_inputmask), _inputmask3 = __webpack_require__(0), _inputmask4 = _interopRequireDefault(_inputmask3), _jquery = __webpack_require__(2), _jquery2 = _interopRequireDefault(_jquery);
	_inputmask4.default === _jquery2.default && __webpack_require__(7), window.Inputmask = _inputmask2.default;
}, function(module, exports, __webpack_require__) {
	"use strict";
	var __WEBPACK_AMD_DEFINE_RESULT__;
	"function" == typeof Symbol && Symbol.iterator;
	void 0 !== (__WEBPACK_AMD_DEFINE_RESULT__ = function() {
		return document;
	}.call(exports, __webpack_require__, exports, module)) && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__);
}, function(module, exports, __webpack_require__) {
	"use strict";
	var __WEBPACK_AMD_DEFINE_RESULT__;
	"function" == typeof Symbol && Symbol.iterator;
	void 0 !== (__WEBPACK_AMD_DEFINE_RESULT__ = function() {
		return window;
	}.call(exports, __webpack_require__, exports, module)) && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__);
}, function(module, exports, __webpack_require__) {
	exports = module.exports = __webpack_require__(13)(void 0), exports.push([ module.i, "span.im-caret {\r\n    -webkit-animation: 1s blink step-end infinite;\r\n    animation: 1s blink step-end infinite;\r\n}\r\n\r\n@keyframes blink {\r\n    from, to {\r\n        border-right-color: black;\r\n    }\r\n    50% {\r\n        border-right-color: transparent;\r\n    }\r\n}\r\n\r\n@-webkit-keyframes blink {\r\n    from, to {\r\n        border-right-color: black;\r\n    }\r\n    50% {\r\n        border-right-color: transparent;\r\n    }\r\n}\r\n\r\nspan.im-static {\r\n    color: grey;\r\n}\r\n\r\ndiv.im-colormask {\r\n    display: inline-block;\r\n    border-style: inset;\r\n    border-width: 2px;\r\n    -webkit-appearance: textfield;\r\n    -moz-appearance: textfield;\r\n    appearance: textfield;\r\n}\r\n\r\ndiv.im-colormask > input {\r\n    position: absolute;\r\n    display: inline-block;\r\n    background-color: transparent;\r\n    color: transparent;\r\n    -webkit-appearance: caret;\r\n    -moz-appearance: caret;\r\n    appearance: caret;\r\n    border-style: none;\r\n    left: 0; /*calculated*/\r\n}\r\n\r\ndiv.im-colormask > input:focus {\r\n    outline: none;\r\n}\r\n\r\ndiv.im-colormask > div {\r\n    color: black;\r\n    display: inline-block;\r\n    width: 100px; /*calculated*/\r\n}", "" ]);
}, function(module, exports) {
	function cssWithMappingToString(item, useSourceMap) {
		var content = item[1] || "", cssMapping = item[3];
		if (!cssMapping) return content;
		if (useSourceMap && "function" == typeof btoa) {
			var sourceMapping = toComment(cssMapping);
			return [ content ].concat(cssMapping.sources.map(function(source) {
				return "/*# sourceURL=" + cssMapping.sourceRoot + source + " */";
			})).concat([ sourceMapping ]).join("\n");
		}
		return [ content ].join("\n");
	}
	function toComment(sourceMap) {
		return "/*# sourceMappingURL=data:application/json;charset=utf-8;base64," + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + " */";
	}
	module.exports = function(useSourceMap) {
		var list = [];
		return list.toString = function() {
			return this.map(function(item) {
				var content = cssWithMappingToString(item, useSourceMap);
				return item[2] ? "@media " + item[2] + "{" + content + "}" : content;
			}).join("");
		}, list.i = function(modules, mediaQuery) {
			"string" == typeof modules && (modules = [ [ null, modules, "" ] ]);
			for (var alreadyImportedModules = {}, i = 0; i < this.length; i++) {
				var id = this[i][0];
				"number" == typeof id && (alreadyImportedModules[id] = !0);
			}
			for (i = 0; i < modules.length; i++) {
				var item = modules[i];
				"number" == typeof item[0] && alreadyImportedModules[item[0]] || (mediaQuery && !item[2] ? item[2] = mediaQuery : mediaQuery && (item[2] = "(" + item[2] + ") and (" + mediaQuery + ")"),
					list.push(item));
			}
		}, list;
	};
}, function(module, exports, __webpack_require__) {
	function addStylesToDom(styles, options) {
		for (var i = 0; i < styles.length; i++) {
			var item = styles[i], domStyle = stylesInDom[item.id];
			if (domStyle) {
				domStyle.refs++;
				for (var j = 0; j < domStyle.parts.length; j++) domStyle.parts[j](item.parts[j]);
				for (;j < item.parts.length; j++) domStyle.parts.push(addStyle(item.parts[j], options));
			} else {
				for (var parts = [], j = 0; j < item.parts.length; j++) parts.push(addStyle(item.parts[j], options));
				stylesInDom[item.id] = {
					id: item.id,
					refs: 1,
					parts: parts
				};
			}
		}
	}
	function listToStyles(list) {
		for (var styles = [], newStyles = {}, i = 0; i < list.length; i++) {
			var item = list[i], id = item[0], css = item[1], media = item[2], sourceMap = item[3], part = {
				css: css,
				media: media,
				sourceMap: sourceMap
			};
			newStyles[id] ? newStyles[id].parts.push(part) : styles.push(newStyles[id] = {
				id: id,
				parts: [ part ]
			});
		}
		return styles;
	}
	function insertStyleElement(options, styleElement) {
		var styleTarget = getElement(options.insertInto);
		if (!styleTarget) throw new Error("Couldn't find a style target. This probably means that the value for the 'insertInto' parameter is invalid.");
		var lastStyleElementInsertedAtTop = styleElementsInsertedAtTop[styleElementsInsertedAtTop.length - 1];
		if ("top" === options.insertAt) lastStyleElementInsertedAtTop ? lastStyleElementInsertedAtTop.nextSibling ? styleTarget.insertBefore(styleElement, lastStyleElementInsertedAtTop.nextSibling) : styleTarget.appendChild(styleElement) : styleTarget.insertBefore(styleElement, styleTarget.firstChild),
			styleElementsInsertedAtTop.push(styleElement); else {
			if ("bottom" !== options.insertAt) throw new Error("Invalid value for parameter 'insertAt'. Must be 'top' or 'bottom'.");
			styleTarget.appendChild(styleElement);
		}
	}
	function removeStyleElement(styleElement) {
		styleElement.parentNode.removeChild(styleElement);
		var idx = styleElementsInsertedAtTop.indexOf(styleElement);
		idx >= 0 && styleElementsInsertedAtTop.splice(idx, 1);
	}
	function createStyleElement(options) {
		var styleElement = document.createElement("style");
		return options.attrs.type = "text/css", attachTagAttrs(styleElement, options.attrs),
			insertStyleElement(options, styleElement), styleElement;
	}
	function createLinkElement(options) {
		var linkElement = document.createElement("link");
		return options.attrs.type = "text/css", options.attrs.rel = "stylesheet", attachTagAttrs(linkElement, options.attrs),
			insertStyleElement(options, linkElement), linkElement;
	}
	function attachTagAttrs(element, attrs) {
		Object.keys(attrs).forEach(function(key) {
			element.setAttribute(key, attrs[key]);
		});
	}
	function addStyle(obj, options) {
		var styleElement, update, remove;
		if (options.singleton) {
			var styleIndex = singletonCounter++;
			styleElement = singletonElement || (singletonElement = createStyleElement(options)),
				update = applyToSingletonTag.bind(null, styleElement, styleIndex, !1), remove = applyToSingletonTag.bind(null, styleElement, styleIndex, !0);
		} else obj.sourceMap && "function" == typeof URL && "function" == typeof URL.createObjectURL && "function" == typeof URL.revokeObjectURL && "function" == typeof Blob && "function" == typeof btoa ? (styleElement = createLinkElement(options),
			update = updateLink.bind(null, styleElement, options), remove = function() {
			removeStyleElement(styleElement), styleElement.href && URL.revokeObjectURL(styleElement.href);
		}) : (styleElement = createStyleElement(options), update = applyToTag.bind(null, styleElement),
			remove = function() {
				removeStyleElement(styleElement);
			});
		return update(obj), function(newObj) {
			if (newObj) {
				if (newObj.css === obj.css && newObj.media === obj.media && newObj.sourceMap === obj.sourceMap) return;
				update(obj = newObj);
			} else remove();
		};
	}
	function applyToSingletonTag(styleElement, index, remove, obj) {
		var css = remove ? "" : obj.css;
		if (styleElement.styleSheet) styleElement.styleSheet.cssText = replaceText(index, css); else {
			var cssNode = document.createTextNode(css), childNodes = styleElement.childNodes;
			childNodes[index] && styleElement.removeChild(childNodes[index]), childNodes.length ? styleElement.insertBefore(cssNode, childNodes[index]) : styleElement.appendChild(cssNode);
		}
	}
	function applyToTag(styleElement, obj) {
		var css = obj.css, media = obj.media;
		if (media && styleElement.setAttribute("media", media), styleElement.styleSheet) styleElement.styleSheet.cssText = css; else {
			for (;styleElement.firstChild; ) styleElement.removeChild(styleElement.firstChild);
			styleElement.appendChild(document.createTextNode(css));
		}
	}
	function updateLink(linkElement, options, obj) {
		var css = obj.css, sourceMap = obj.sourceMap, autoFixUrls = void 0 === options.convertToAbsoluteUrls && sourceMap;
		(options.convertToAbsoluteUrls || autoFixUrls) && (css = fixUrls(css)), sourceMap && (css += "\n/*# sourceMappingURL=data:application/json;base64," + btoa(unescape(encodeURIComponent(JSON.stringify(sourceMap)))) + " */");
		var blob = new Blob([ css ], {
			type: "text/css"
		}), oldSrc = linkElement.href;
		linkElement.href = URL.createObjectURL(blob), oldSrc && URL.revokeObjectURL(oldSrc);
	}
	var stylesInDom = {}, isOldIE = function(fn) {
		var memo;
		return function() {
			return void 0 === memo && (memo = fn.apply(this, arguments)), memo;
		};
	}(function() {
		return window && document && document.all && !window.atob;
	}), getElement = function(fn) {
		var memo = {};
		return function(selector) {
			return void 0 === memo[selector] && (memo[selector] = fn.call(this, selector)),
				memo[selector];
		};
	}(function(styleTarget) {
		return document.querySelector(styleTarget);
	}), singletonElement = null, singletonCounter = 0, styleElementsInsertedAtTop = [], fixUrls = __webpack_require__(15);
	module.exports = function(list, options) {
		if ("undefined" != typeof DEBUG && DEBUG && "object" != typeof document) throw new Error("The style-loader cannot be used in a non-browser environment");
		options = options || {}, options.attrs = "object" == typeof options.attrs ? options.attrs : {},
		void 0 === options.singleton && (options.singleton = isOldIE()), void 0 === options.insertInto && (options.insertInto = "head"),
		void 0 === options.insertAt && (options.insertAt = "bottom");
		var styles = listToStyles(list);
		return addStylesToDom(styles, options), function(newList) {
			for (var mayRemove = [], i = 0; i < styles.length; i++) {
				var item = styles[i], domStyle = stylesInDom[item.id];
				domStyle.refs--, mayRemove.push(domStyle);
			}
			if (newList) {
				addStylesToDom(listToStyles(newList), options);
			}
			for (var i = 0; i < mayRemove.length; i++) {
				var domStyle = mayRemove[i];
				if (0 === domStyle.refs) {
					for (var j = 0; j < domStyle.parts.length; j++) domStyle.parts[j]();
					delete stylesInDom[domStyle.id];
				}
			}
		};
	};
	var replaceText = function() {
		var textStore = [];
		return function(index, replacement) {
			return textStore[index] = replacement, textStore.filter(Boolean).join("\n");
		};
	}();
}, function(module, exports) {
	module.exports = function(css) {
		var location = "undefined" != typeof window && window.location;
		if (!location) throw new Error("fixUrls requires window.location");
		if (!css || "string" != typeof css) return css;
		var baseUrl = location.protocol + "//" + location.host, currentDir = baseUrl + location.pathname.replace(/\/[^\/]*$/, "/");
		return css.replace(/url\s*\(((?:[^)(]|\((?:[^)(]+|\([^)(]*\))*\))*)\)/gi, function(fullMatch, origUrl) {
			var unquotedOrigUrl = origUrl.trim().replace(/^"(.*)"$/, function(o, $1) {
				return $1;
			}).replace(/^'(.*)'$/, function(o, $1) {
				return $1;
			});
			if (/^(#|data:|http:\/\/|https:\/\/|file:\/\/\/)/i.test(unquotedOrigUrl)) return fullMatch;
			var newUrl;
			return newUrl = 0 === unquotedOrigUrl.indexOf("//") ? unquotedOrigUrl : 0 === unquotedOrigUrl.indexOf("/") ? baseUrl + unquotedOrigUrl : currentDir + unquotedOrigUrl.replace(/^\.\//, ""),
			"url(" + JSON.stringify(newUrl) + ")";
		});
	};
} ]);
/*
 * jQuery miniColors: A small color selector
 *
 * Copyright 2011 Cory LaViska for A Beautiful Site, LLC. (http://abeautifulsite.net/)
 *
 * Dual licensed under the MIT or GPL Version 2 licenses
 *
 */
if(jQuery)(function($){$.extend($.fn,{miniColors:function(o,data){var create=function(input,o,data){var color=expandHex(input.val());if(!color)color='ffffff';var hsb=hex2hsb(color);var trigger=$('<span class="input-group-addon" style="background-color: #'+color+'" href="#"></span>');trigger.insertAfter(input);input.addClass('miniColors').data('original-maxlength',input.attr('maxlength')||null).data('original-autocomplete',input.attr('autocomplete')||null).data('letterCase',o.letterCase?o.letterCase:'uppercase').data('trigger',trigger).data('hsb',hsb).data('change',o.change?o.change:null).data('close',o.close?o.close:null).data('open',o.open?o.open:null).attr('maxlength',7).attr('autocomplete','off').val('#'+convertCase(color,o.letterCase)).trigger('change');if(o.readonly)input.prop('readonly',true);if(o.disabled)disable(input);trigger.on('click.miniColors',function(event){event.preventDefault();if(input.val()==='')input.val('#').trigger('change');show(input)});input.on('focus.miniColors',function(event){if(input.val()==='')input.val('#').trigger('change');show(input)});input.on('blur.miniColors',function(event){var hex=expandHex(hsb2hex(input.data('hsb')));input.val(hex?'#'+convertCase(hex,input.data('letterCase')):'').trigger('change')});input.on('keydown.miniColors',function(event){if(event.keyCode===9)hide(input)});input.on('keyup.miniColors',function(event){setColorFromInput(input)});input.on('paste.miniColors',function(event){setTimeout(function(){setColorFromInput(input)},5)})};var destroy=function(input){hide();input=$(input);input.data('trigger').remove();input.attr('autocomplete',input.data('original-autocomplete')).attr('maxlength',input.data('original-maxlength')).removeData().removeClass('miniColors').off('.miniColors');$(document).off('.miniColors')};var enable=function(input){input.prop('disabled',false).data('trigger').css('opacity',1)};var disable=function(input){hide(input);input.prop('disabled',true).data('trigger').css('opacity',0.5)};var show=function(input){if(input.prop('disabled'))return false;hide();var selector=$('<div class="miniColors-selector"></div>');selector.append('<div class="miniColors-colors" style="background-color: #FFF;"><div class="miniColors-colorPicker"><div class="miniColors-colorPicker-inner"></div></div>').append('<div class="miniColors-hues"><div class="miniColors-huePicker"></div></div>').css('display','none').addClass(input.attr('class')).removeClass('form-control');var hsb=input.data('hsb');selector.find('.miniColors-colors').css('backgroundColor','#'+hsb2hex({h:hsb.h,s:100,b:100}));var colorPosition=input.data('colorPosition');if(!colorPosition)colorPosition=getColorPositionFromHSB(hsb);selector.find('.miniColors-colorPicker').css('top',colorPosition.y+'px').css('left',colorPosition.x+'px');var huePosition=input.data('huePosition');if(!huePosition)huePosition=getHuePositionFromHSB(hsb);selector.find('.miniColors-huePicker').css('top',huePosition.y+'px');input.data('selector',selector).data('huePicker',selector.find('.miniColors-huePicker')).data('colorPicker',selector.find('.miniColors-colorPicker')).data('mousebutton',0);$('BODY').append(selector);var trigger=input.data('trigger'),hidden=!input.is(':visible'),top=hidden?trigger.offset().top+trigger.outerHeight():input.offset().top+input.outerHeight(),left=hidden?trigger.offset().left:input.offset().left,selectorWidth=selector.outerWidth(),selectorHeight=selector.outerHeight(),triggerWidth=trigger.outerWidth(),triggerHeight=trigger.outerHeight(),windowHeight=$(window).height(),windowWidth=$(window).width(),scrollTop=$(window).scrollTop(),scrollLeft=$(window).scrollLeft();if((top+selectorHeight)>windowHeight+scrollTop)top=top-selectorHeight-triggerHeight;if((left+selectorWidth)>windowWidth+scrollLeft)left=left-selectorWidth+triggerWidth;selector.css({top:top,left:left}).fadeIn(100);selector.on('selectstart',function(){return false});if(!$.browser.msie||($.browser.msie&&$.browser.version>=9)){$(window).on('resize.miniColors',function(event){hide(input)})}$(document).on('mousedown.miniColors touchstart.miniColors',function(event){input.data('mousebutton',1);var testSubject=$(event.target).parents().andSelf();if(testSubject.hasClass('miniColors-colors')){event.preventDefault();input.data('moving','colors');moveColor(input,event)}if(testSubject.hasClass('miniColors-hues')){event.preventDefault();input.data('moving','hues');moveHue(input,event)}if(testSubject.hasClass('miniColors-selector')){event.preventDefault();return}if(testSubject.hasClass('miniColors'))return;hide(input)});$(document).on('mouseup.miniColors touchend.miniColors',function(event){event.preventDefault();input.data('mousebutton',0).removeData('moving')}).on('mousemove.miniColors touchmove.miniColors',function(event){event.preventDefault();if(input.data('mousebutton')===1){if(input.data('moving')==='colors')moveColor(input,event);if(input.data('moving')==='hues')moveHue(input,event)}});if(input.data('open')){input.data('open').call(input.get(0),'#'+hsb2hex(hsb),hsb2rgb(hsb))}};var hide=function(input){if(!input)input=$('.miniColors');input.each(function(){var selector=$(this).data('selector');$(this).removeData('selector');$(selector).fadeOut(100,function(){if(input.data('close')){var hsb=input.data('hsb'),hex=hsb2hex(hsb);input.data('close').call(input.get(0),'#'+hex,hsb2rgb(hsb))}$(this).remove()})});$(document).off('.miniColors')};var moveColor=function(input,event){var colorPicker=input.data('colorPicker');colorPicker.hide();var position={x:event.pageX,y:event.pageY};if(event.originalEvent.changedTouches){position.x=event.originalEvent.changedTouches[0].pageX;position.y=event.originalEvent.changedTouches[0].pageY}position.x=position.x-input.data('selector').find('.miniColors-colors').offset().left-5;position.y=position.y-input.data('selector').find('.miniColors-colors').offset().top-5;if(position.x<=-5)position.x=-5;if(position.x>=144)position.x=144;if(position.y<=-5)position.y=-5;if(position.y>=144)position.y=144;input.data('colorPosition',position);colorPicker.css('left',position.x).css('top',position.y).show();var s=Math.round((position.x+5)*0.67);if(s<0)s=0;if(s>100)s=100;var b=100-Math.round((position.y+5)*0.67);if(b<0)b=0;if(b>100)b=100;var hsb=input.data('hsb');hsb.s=s;hsb.b=b;setColor(input,hsb,true)};var moveHue=function(input,event){var huePicker=input.data('huePicker');huePicker.hide();var position={y:event.pageY};if(event.originalEvent.changedTouches){position.y=event.originalEvent.changedTouches[0].pageY}position.y=position.y-input.data('selector').find('.miniColors-colors').offset().top-1;if(position.y<=-1)position.y=-1;if(position.y>=149)position.y=149;input.data('huePosition',position);huePicker.css('top',position.y).show();var h=Math.round((150-position.y-1)*2.4);if(h<0)h=0;if(h>360)h=360;var hsb=input.data('hsb');hsb.h=h;setColor(input,hsb,true)};var setColor=function(input,hsb,updateInput){input.data('hsb',hsb);var hex=hsb2hex(hsb);if(updateInput)input.val('#'+convertCase(hex,input.data('letterCase'))).trigger('change');input.data('trigger').css('backgroundColor','#'+hex);if(input.data('selector'))input.data('selector').find('.miniColors-colors').css('backgroundColor','#'+hsb2hex({h:hsb.h,s:100,b:100}));if(input.data('change')){if(hex===input.data('lastChange'))return;input.data('change').call(input.get(0),'#'+hex,hsb2rgb(hsb));input.data('lastChange',hex)}};var setColorFromInput=function(input){input.val('#'+cleanHex(input.val())).trigger('change');var hex=expandHex(input.val());if(!hex)return false;var hsb=hex2hsb(hex);var currentHSB=input.data('hsb');if(hsb.h===currentHSB.h&&hsb.s===currentHSB.s&&hsb.b===currentHSB.b)return true;var colorPosition=getColorPositionFromHSB(hsb);var colorPicker=$(input.data('colorPicker'));colorPicker.css('top',colorPosition.y+'px').css('left',colorPosition.x+'px');input.data('colorPosition',colorPosition);var huePosition=getHuePositionFromHSB(hsb);var huePicker=$(input.data('huePicker'));huePicker.css('top',huePosition.y+'px');input.data('huePosition',huePosition);setColor(input,hsb);return true};var convertCase=function(string,letterCase){if(letterCase==='lowercase')return string.toLowerCase();if(letterCase==='uppercase')return string.toUpperCase();return string};var getColorPositionFromHSB=function(hsb){var x=Math.ceil(hsb.s/0.67);if(x<0)x=0;if(x>150)x=150;var y=150-Math.ceil(hsb.b/0.67);if(y<0)y=0;if(y>150)y=150;return{x:x-5,y:y-5}};var getHuePositionFromHSB=function(hsb){var y=150-(hsb.h/2.4);if(y<0)h=0;if(y>150)h=150;return{y:y-1}};var cleanHex=function(hex){return hex.replace(/[^A-F0-9]/ig,'')};var expandHex=function(hex){hex=cleanHex(hex);if(!hex)return null;if(hex.length===3)hex=hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];return hex.length===6?hex:null};var hsb2rgb=function(hsb){var rgb={};var h=Math.round(hsb.h);var s=Math.round(hsb.s*255/100);var v=Math.round(hsb.b*255/100);if(s===0){rgb.r=rgb.g=rgb.b=v}else{var t1=v;var t2=(255-s)*v/255;var t3=(t1-t2)*(h%60)/60;if(h===360)h=0;if(h<60){rgb.r=t1;rgb.b=t2;rgb.g=t2+t3}else if(h<120){rgb.g=t1;rgb.b=t2;rgb.r=t1-t3}else if(h<180){rgb.g=t1;rgb.r=t2;rgb.b=t2+t3}else if(h<240){rgb.b=t1;rgb.r=t2;rgb.g=t1-t3}else if(h<300){rgb.b=t1;rgb.g=t2;rgb.r=t2+t3}else if(h<360){rgb.r=t1;rgb.g=t2;rgb.b=t1-t3}else{rgb.r=0;rgb.g=0;rgb.b=0}}return{r:Math.round(rgb.r),g:Math.round(rgb.g),b:Math.round(rgb.b)}};var rgb2hex=function(rgb){var hex=[rgb.r.toString(16),rgb.g.toString(16),rgb.b.toString(16)];$.each(hex,function(nr,val){if(val.length===1)hex[nr]='0'+val});return hex.join('')};var hex2rgb=function(hex){hex=parseInt(((hex.indexOf('#')>-1)?hex.substring(1):hex),16);return{r:hex>>16,g:(hex&0x00FF00)>>8,b:(hex&0x0000FF)}};var rgb2hsb=function(rgb){var hsb={h:0,s:0,b:0};var min=Math.min(rgb.r,rgb.g,rgb.b);var max=Math.max(rgb.r,rgb.g,rgb.b);var delta=max-min;hsb.b=max;hsb.s=max!==0?255*delta/max:0;if(hsb.s!==0){if(rgb.r===max){hsb.h=(rgb.g-rgb.b)/delta}else if(rgb.g===max){hsb.h=2+(rgb.b-rgb.r)/delta}else{hsb.h=4+(rgb.r-rgb.g)/delta}}else{hsb.h=-1}hsb.h*=60;if(hsb.h<0){hsb.h+=360}hsb.s*=100/255;hsb.b*=100/255;return hsb};var hex2hsb=function(hex){var hsb=rgb2hsb(hex2rgb(hex));if(hsb.s===0)hsb.h=360;return hsb};var hsb2hex=function(hsb){return rgb2hex(hsb2rgb(hsb))};switch(o){case'readonly':$(this).each(function(){if(!$(this).hasClass('miniColors'))return;$(this).prop('readonly',data)});return $(this);case'disabled':$(this).each(function(){if(!$(this).hasClass('miniColors'))return;if(data){disable($(this))}else{enable($(this))}});return $(this);case'value':if(data===undefined){if(!$(this).hasClass('miniColors'))return;var input=$(this),hex=expandHex(input.val());return hex?'#'+convertCase(hex,input.data('letterCase')):null}$(this).each(function(){if(!$(this).hasClass('miniColors'))return;$(this).val(data).trigger('change');setColorFromInput($(this))});return $(this);case'destroy':$(this).each(function(){if(!$(this).hasClass('miniColors'))return;destroy($(this))});return $(this);default:if(!o)o={};$(this).each(function(){if($(this)[0].tagName.toLowerCase()!=='input')return;if($(this).data('trigger'))return;create($(this),o,data)});return $(this)}}})})(jQuery);


function color_picker_init(){
	jQuery('.minicolor-picker').miniColors();
}

document.addEventListener('load', color_picker_init , false);

jQuery( document ).ajaxComplete(function() {
	color_picker_init();
});

/* =========================================================
 * bootstrap-cfdatepicker.js
 * Repo: https://github.com/eternicode/bootstrap-cfdatepicker/
 * Demo: http://eternicode.github.io/bootstrap-cfdatepicker/
 * Docs: http://bootstrap-cfdatepicker.readthedocs.org/
 * Forked from http://www.eyecon.ro/bootstrap-cfdatepicker
 * =========================================================
 * Started by Stefan Petre; improvements by Andrew Rowls + contributors
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================= */

(function($, undefined){

	var $window = $(window);

	function UTCDate(){
		return new Date(Date.UTC.apply(Date, arguments));
	}
	function UTCToday(){
		var today = new Date();
		return UTCDate(today.getFullYear(), today.getMonth(), today.getDate());
	}
	function alias(method){
		return function(){
			return this[method].apply(this, arguments);
		};
	}

	var DateArray = (function(){
		var extras = {
			get: function(i){
				return this.slice(i)[0];
			},
			contains: function(d){
				// Array.indexOf is not cross-browser;
				// $.inArray doesn't work with Dates
				var val = d && d.valueOf();
				for (var i=0, l=this.length; i < l; i++)
					if (this[i].valueOf() === val)
						return i;
				return -1;
			},
			remove: function(i){
				this.splice(i,1);
			},
			replace: function(new_array){
				if (!new_array)
					return;
				if (!$.isArray(new_array))
					new_array = [new_array];
				this.clear();
				this.push.apply(this, new_array);
			},
			clear: function(){
				this.splice(0);
			},
			copy: function(){
				var a = new DateArray();
				a.replace(this);
				return a;
			}
		};

		return function(){
			var a = [];
			a.push.apply(a, arguments);
			$.extend(a, extras);
			return a;
		};
	})();


	// Picker object

	var Datepicker = function(element, options){
		this.dates = new DateArray();
		this.viewDate = UTCToday();
		this.focusDate = null;

		this._process_options(options);

		this.element = $(element);
		this.isInline = false;
		this.isInput = this.element.is('input');
		this.component = this.element.is('.date') ? this.element.find('.add-on, .input-group-addon, .btn') : false;
		this.hasInput = this.component && this.element.find('input').length;
		if (this.component && this.component.length === 0)
			this.component = false;

		this.picker = $(DPGlobal.template);
		this._buildEvents();
		this._attachEvents();

		if (this.isInline){
			this.picker.addClass('cfdatepicker-inline').appendTo(this.element);
		}
		else {
			this.picker.addClass('cfdatepicker-dropdown dropdown-menu');
		}

		if (this.o.rtl){
			this.picker.addClass('cfdatepicker-rtl');
		}

		this.viewMode = this.o.startView;

		if (this.o.calendarWeeks)
			this.picker.find('tfoot th.today')
				.attr('colspan', function(i, val){
					return parseInt(val) + 1;
				});

		this._allow_update = false;

		this.setStartDate(this._o.startDate);
		this.setEndDate(this._o.endDate);
		this.setDaysOfWeekDisabled(this.o.daysOfWeekDisabled);

		this.fillDow();
		this.fillMonths();

		this._allow_update = true;

		this.update();
		this.showMode();

		if (this.isInline){
			this.show();
		}
	};

	Datepicker.prototype = {
		constructor: Datepicker,

		_process_options: function(opts){
			// Store raw options for reference
			this._o = $.extend({}, this._o, opts);
			// Processed options
			var o = this.o = $.extend({}, this._o);

			// Check if "de-DE" style date is available, if not language should
			// fallback to 2 letter code eg "de"
			var lang = o.language;
			if (!dates[lang]){
				lang = lang.split('-')[0];
				if (!dates[lang])
					lang = defaults.language;
			}
			o.language = lang;

			switch (o.startView){
				case 2:
				case 'decade':
					o.startView = 2;
					break;
				case 1:
				case 'year':
					o.startView = 1;
					break;
				default:
					o.startView = 0;
			}

			switch (o.minViewMode){
				case 1:
				case 'months':
					o.minViewMode = 1;
					break;
				case 2:
				case 'years':
					o.minViewMode = 2;
					break;
				default:
					o.minViewMode = 0;
			}

			o.startView = Math.max(o.startView, o.minViewMode);

			// true, false, or Number > 0
			if (o.multidate !== true){
				o.multidate = Number(o.multidate) || false;
				if (o.multidate !== false)
					o.multidate = Math.max(0, o.multidate);
				else
					o.multidate = 1;
			}
			o.multidateSeparator = String(o.multidateSeparator);

			o.weekStart %= 7;
			o.weekEnd = ((o.weekStart + 6) % 7);

			var format = DPGlobal.parseFormat(o.format);
			if (o.startDate !== -Infinity){
				if (!!o.startDate){
					if (o.startDate instanceof Date)
						o.startDate = this._local_to_utc(this._zero_time(o.startDate));
					else
						o.startDate = DPGlobal.parseDate(o.startDate, format, o.language);
				}
				else {
					o.startDate = -Infinity;
				}
			}
			if (o.endDate !== Infinity){
				if (!!o.endDate){
					if (o.endDate instanceof Date)
						o.endDate = this._local_to_utc(this._zero_time(o.endDate));
					else
						o.endDate = DPGlobal.parseDate(o.endDate, format, o.language);
				}
				else {
					o.endDate = Infinity;
				}
			}

			o.daysOfWeekDisabled = o.daysOfWeekDisabled||[];
			if (!$.isArray(o.daysOfWeekDisabled))
				o.daysOfWeekDisabled = o.daysOfWeekDisabled.split(/[,\s]*/);
			o.daysOfWeekDisabled = $.map(o.daysOfWeekDisabled, function(d){
				return parseInt(d, 10);
			});

			var plc = String(o.orientation).toLowerCase().split(/\s+/g),
				_plc = o.orientation.toLowerCase();
			plc = $.grep(plc, function(word){
				return (/^auto|left|right|top|bottom$/).test(word);
			});
			o.orientation = {x: 'auto', y: 'auto'};
			if (!_plc || _plc === 'auto')
				; // no action
			else if (plc.length === 1){
				switch (plc[0]){
					case 'top':
					case 'bottom':
						o.orientation.y = plc[0];
						break;
					case 'left':
					case 'right':
						o.orientation.x = plc[0];
						break;
				}
			}
			else {
				_plc = $.grep(plc, function(word){
					return (/^left|right$/).test(word);
				});
				o.orientation.x = _plc[0] || 'auto';

				_plc = $.grep(plc, function(word){
					return (/^top|bottom$/).test(word);
				});
				o.orientation.y = _plc[0] || 'auto';
			}
		},
		_events: [],
		_secondaryEvents: [],
		_applyEvents: function(evs){
			for (var i=0, el, ch, ev; i < evs.length; i++){
				el = evs[i][0];
				if (evs[i].length === 2){
					ch = undefined;
					ev = evs[i][1];
				}
				else if (evs[i].length === 3){
					ch = evs[i][1];
					ev = evs[i][2];
				}
				el.on(ev, ch);
			}
		},
		_unapplyEvents: function(evs){
			for (var i=0, el, ev, ch; i < evs.length; i++){
				el = evs[i][0];
				if (evs[i].length === 2){
					ch = undefined;
					ev = evs[i][1];
				}
				else if (evs[i].length === 3){
					ch = evs[i][1];
					ev = evs[i][2];
				}
				el.off(ev, ch);
			}
		},
		_buildEvents: function(){
			if (this.isInput){ // single input
				this._events = [
					[this.element, {
						focus: $.proxy(this.show, this),
						keyup: $.proxy(function(e){
							if ($.inArray(e.keyCode, [27,37,39,38,40,32,13,9]) === -1)
								this.update();
						}, this),
						keydown: $.proxy(this.keydown, this)
					}]
				];
			}
			else if (this.component && this.hasInput){ // component: input + button
				this._events = [
					// For components that are not readonly, allow keyboard nav
					[this.element.find('input'), {
						focus: $.proxy(this.show, this),
						keyup: $.proxy(function(e){
							if ($.inArray(e.keyCode, [27,37,39,38,40,32,13,9]) === -1)
								this.update();
						}, this),
						keydown: $.proxy(this.keydown, this)
					}],
					[this.component, {
						click: $.proxy(this.show, this)
					}]
				];
			}
			else if (this.element.is('div')){  // inline cfdatepicker
				this.isInline = true;
			}
			else {
				this._events = [
					[this.element, {
						click: $.proxy(this.show, this)
					}]
				];
			}
			this._events.push(
				// Component: listen for blur on element descendants
				[this.element, '*', {
					blur: $.proxy(function(e){
						this._focused_from = e.target;
					}, this)
				}],
				// Input: listen for blur on element
				[this.element, {
					blur: $.proxy(function(e){
						this._focused_from = e.target;
					}, this)
				}]
			);

			this._secondaryEvents = [
				[this.picker, {
					click: $.proxy(this.click, this)
				}],
				[$(window), {
					resize: $.proxy(this.place, this)
				}],
				[$(document), {
					'mousedown touchstart': $.proxy(function(e){
						// Clicked outside the cfdatepicker, hide it
						if (!(
								this.element.is(e.target) ||
								this.element.find(e.target).length ||
								this.picker.is(e.target) ||
								this.picker.find(e.target).length
							)){
							this.hide();
						}
					}, this)
				}]
			];
		},
		_attachEvents: function(){
			this._detachEvents();
			this._applyEvents(this._events);
		},
		_detachEvents: function(){
			this._unapplyEvents(this._events);
		},
		_attachSecondaryEvents: function(){
			this._detachSecondaryEvents();
			this._applyEvents(this._secondaryEvents);
		},
		_detachSecondaryEvents: function(){
			this._unapplyEvents(this._secondaryEvents);
		},
		_trigger: function(event, altdate){
			var date = altdate || this.dates.get(-1),
				local_date = this._utc_to_local(date);

			this.element.trigger({
				type: event,
				date: local_date,
				dates: $.map(this.dates, this._utc_to_local),
				format: $.proxy(function(ix, format){
					if (arguments.length === 0){
						ix = this.dates.length - 1;
						format = this.o.format;
					}
					else if (typeof ix === 'string'){
						format = ix;
						ix = this.dates.length - 1;
					}
					format = format || this.o.format;
					var date = this.dates.get(ix);
					return DPGlobal.formatDate(date, format, this.o.language);
				}, this)
			});
		},

		show: function(){
			if (!this.isInline)
				this.picker.appendTo('body');
			this.picker.show();
			this.place();
			this._attachSecondaryEvents();
			this._trigger('show');
		},

		hide: function(){
			if (this.isInline)
				return;
			if (!this.picker.is(':visible'))
				return;
			this.focusDate = null;
			this.picker.hide().detach();
			this._detachSecondaryEvents();
			this.viewMode = this.o.startView;
			this.showMode();

			if (
				this.o.forceParse &&
				(
					this.isInput && this.element.val() ||
					this.hasInput && this.element.find('input').val()
				)
			)
				this.setValue();
			this._trigger('hide');
		},

		remove: function(){
			this.hide();
			this._detachEvents();
			this._detachSecondaryEvents();
			this.picker.remove();
			delete this.element.data().cfdatepicker;
			if (!this.isInput){
				delete this.element.data().date;
			}
		},

		_utc_to_local: function(utc){
			return utc && new Date(utc.getTime() + (utc.getTimezoneOffset()*60000));
		},
		_local_to_utc: function(local){
			return local && new Date(local.getTime() - (local.getTimezoneOffset()*60000));
		},
		_zero_time: function(local){
			return local && new Date(local.getFullYear(), local.getMonth(), local.getDate());
		},
		_zero_utc_time: function(utc){
			return utc && new Date(Date.UTC(utc.getUTCFullYear(), utc.getUTCMonth(), utc.getUTCDate()));
		},

		getDates: function(){
			return $.map(this.dates, this._utc_to_local);
		},

		getUTCDates: function(){
			return $.map(this.dates, function(d){
				return new Date(d);
			});
		},

		getDate: function(){
			return this._utc_to_local(this.getUTCDate());
		},

		getUTCDate: function(){
			return new Date(this.dates.get(-1));
		},

		setDates: function(){
			var args = $.isArray(arguments[0]) ? arguments[0] : arguments;
			this.update.apply(this, args);
			this._trigger('changeDate');
			this.setValue();
		},

		setUTCDates: function(){
			var args = $.isArray(arguments[0]) ? arguments[0] : arguments;
			this.update.apply(this, $.map(args, this._utc_to_local));
			this._trigger('changeDate');
			this.setValue();
		},

		setDate: alias('setDates'),
		setUTCDate: alias('setUTCDates'),

		setValue: function(){
			var formatted = this.getFormattedDate();
			if (!this.isInput){
				if (this.component){
					this.element.find('input').val(formatted).change();
				}
			}
			else {
				this.element.val(formatted).change();
			}
		},

		getFormattedDate: function(format){
			if (format === undefined)
				format = this.o.format;

			var lang = this.o.language;
			return $.map(this.dates, function(d){
				return DPGlobal.formatDate(d, format, lang);
			}).join(this.o.multidateSeparator);
		},

		setStartDate: function(startDate){
			this._process_options({startDate: startDate});
			this.update();
			this.updateNavArrows();
		},

		setEndDate: function(endDate){
			this._process_options({endDate: endDate});
			this.update();
			this.updateNavArrows();
		},

		setDaysOfWeekDisabled: function(daysOfWeekDisabled){
			this._process_options({daysOfWeekDisabled: daysOfWeekDisabled});
			this.update();
			this.updateNavArrows();
		},

		place: function(){
			if (this.isInline)
				return;
			var calendarWidth = this.picker.outerWidth(),
				calendarHeight = this.picker.outerHeight(),
				visualPadding = 10,
				windowWidth = $window.width(),
				windowHeight = $window.height(),
				scrollTop = $window.scrollTop();


			var formID = jQuery( this.element  ).data( 'form-id' );
			var maybeModal = document.getElementById( 'modal-' + formID + '-content' );
			var zIndex;

			if( null !== maybeModal ){
				zIndex = 10000;
			}else{
				zIndex = parseInt(this.element.parents().filter(function(){
						return $(this).css('z-index') !== 'auto';
					}).first().css('z-index'))+10;
			}

			var offset = this.component ? this.component.parent().offset() : this.element.offset();
			var height = this.component ? this.component.outerHeight(true) : this.element.outerHeight(false);
			var width = this.component ? this.component.outerWidth(true) : this.element.outerWidth(false);
			var left = offset.left,
				top = offset.top;

			this.picker.removeClass(
				'cfdatepicker-orient-top cfdatepicker-orient-bottom '+
				'cfdatepicker-orient-right cfdatepicker-orient-left'
			);

			if (this.o.orientation.x !== 'auto'){
				this.picker.addClass('cfdatepicker-orient-' + this.o.orientation.x);
				if (this.o.orientation.x === 'right')
					left -= calendarWidth - width;
			}
			// auto x orientation is best-placement: if it crosses a window
			// edge, fudge it sideways
			else {
				// Default to left
				this.picker.addClass('cfdatepicker-orient-left');
				if (offset.left < 0)
					left -= offset.left - visualPadding;
				else if (offset.left + calendarWidth > windowWidth)
					left = windowWidth - calendarWidth - visualPadding;
			}

			// auto y orientation is best-situation: top or bottom, no fudging,
			// decision based on which shows more of the calendar
			var yorient = this.o.orientation.y,
				top_overflow, bottom_overflow;
			if (yorient === 'auto'){
				top_overflow = -scrollTop + offset.top - calendarHeight;
				bottom_overflow = scrollTop + windowHeight - (offset.top + height + calendarHeight);
				if (Math.max(top_overflow, bottom_overflow) === bottom_overflow)
					yorient = 'top';
				else
					yorient = 'bottom';
			}
			this.picker.addClass('cfdatepicker-orient-' + yorient);
			if (yorient === 'top')
				top += height;
			else
				top -= calendarHeight + parseInt(this.picker.css('padding-top'));

			this.picker.css({
				top: top,
				left: left,
				zIndex: zIndex
			});
		},

		_allow_update: true,
		update: function(){
			if (!this._allow_update)
				return;

			var oldDates = this.dates.copy(),
				dates = [],
				fromArgs = false;
			if (arguments.length){
				$.each(arguments, $.proxy(function(i, date){
					if (date instanceof Date)
						date = this._local_to_utc(date);
					dates.push(date);
				}, this));
				fromArgs = true;
			}
			else {
				dates = this.isInput
					? this.element.val()
					: this.element.data('date') || this.element.find('input').val();
				if (dates && this.o.multidate)
					dates = dates.split(this.o.multidateSeparator);
				else
					dates = [dates];
				delete this.element.data().date;
			}

			dates = $.map(dates, $.proxy(function(date){
				return DPGlobal.parseDate(date, this.o.format, this.o.language);
			}, this));
			dates = $.grep(dates, $.proxy(function(date){
				return (
					date < this.o.startDate ||
					date > this.o.endDate ||
					!date
				);
			}, this), true);
			this.dates.replace(dates);

			if (this.dates.length)
				this.viewDate = new Date(this.dates.get(-1));
			else if (this.viewDate < this.o.startDate)
				this.viewDate = new Date(this.o.startDate);
			else if (this.viewDate > this.o.endDate)
				this.viewDate = new Date(this.o.endDate);

			if (fromArgs){
				// setting date by clicking
				this.setValue();
			}
			else if (dates.length){
				// setting date by typing
				if (String(oldDates) !== String(this.dates))
					this._trigger('changeDate');
			}
			if (!this.dates.length && oldDates.length)
				this._trigger('clearDate');

			this.fill();
		},

		fillDow: function(){
			var dowCnt = this.o.weekStart,
				html = '<tr>';
			if (this.o.calendarWeeks){
				var cell = '<th class="cw">&nbsp;</th>';
				html += cell;
				this.picker.find('.cfdatepicker-days thead tr:first-child').prepend(cell);
			}
			while (dowCnt < this.o.weekStart + 7){
				html += '<th class="dow">'+dates[this.o.language].daysMin[(dowCnt++)%7]+'</th>';
			}
			html += '</tr>';
			this.picker.find('.cfdatepicker-days thead').append(html);
		},

		fillMonths: function(){
			var html = '',
				i = 0;
			while (i < 12){
				html += '<span class="month">'+dates[this.o.language].monthsShort[i++]+'</span>';
			}
			this.picker.find('.cfdatepicker-months td').html(html);
		},

		setRange: function(range){
			if (!range || !range.length)
				delete this.range;
			else
				this.range = $.map(range, function(d){
					return d.valueOf();
				});
			this.fill();
		},

		getClassNames: function(date){
			var cls = [],
				year = this.viewDate.getUTCFullYear(),
				month = this.viewDate.getUTCMonth(),
				today = new Date();
			if (date.getUTCFullYear() < year || (date.getUTCFullYear() === year && date.getUTCMonth() < month)){
				cls.push('old');
			}
			else if (date.getUTCFullYear() > year || (date.getUTCFullYear() === year && date.getUTCMonth() > month)){
				cls.push('new');
			}
			if (this.focusDate && date.valueOf() === this.focusDate.valueOf())
				cls.push('focused');
			// Compare internal UTC date with local today, not UTC today
			if (this.o.todayHighlight &&
				date.getUTCFullYear() === today.getFullYear() &&
				date.getUTCMonth() === today.getMonth() &&
				date.getUTCDate() === today.getDate()){
				cls.push('today');
			}
			if (this.dates.contains(date) !== -1)
				cls.push('active');
			if (date.valueOf() < this.o.startDate || date.valueOf() > this.o.endDate ||
				$.inArray(date.getUTCDay(), this.o.daysOfWeekDisabled) !== -1){
				cls.push('disabled');
			}
			if (this.range){
				if (date > this.range[0] && date < this.range[this.range.length-1]){
					cls.push('range');
				}
				if ($.inArray(date.valueOf(), this.range) !== -1){
					cls.push('selected');
				}
			}
			return cls;
		},

		fill: function(){
			var d = new Date(this.viewDate),
				year = d.getUTCFullYear(),
				month = d.getUTCMonth(),
				startYear = this.o.startDate !== -Infinity ? this.o.startDate.getUTCFullYear() : -Infinity,
				startMonth = this.o.startDate !== -Infinity ? this.o.startDate.getUTCMonth() : -Infinity,
				endYear = this.o.endDate !== Infinity ? this.o.endDate.getUTCFullYear() : Infinity,
				endMonth = this.o.endDate !== Infinity ? this.o.endDate.getUTCMonth() : Infinity,
				todaytxt = dates[this.o.language].today || dates['en'].today || '',
				cleartxt = dates[this.o.language].clear || dates['en'].clear || '',
				tooltip;
			this.picker.find('.cfdatepicker-days thead th.cfdatepicker-switch')
				.text(dates[this.o.language].months[month]+' '+year);
			this.picker.find('tfoot th.today')
				.text(todaytxt)
				.toggle(this.o.todayBtn !== false);
			this.picker.find('tfoot th.clear')
				.text(cleartxt)
				.toggle(this.o.clearBtn !== false);
			this.updateNavArrows();
			this.fillMonths();
			var prevMonth = UTCDate(year, month-1, 28),
				day = DPGlobal.getDaysInMonth(prevMonth.getUTCFullYear(), prevMonth.getUTCMonth());
			prevMonth.setUTCDate(day);
			prevMonth.setUTCDate(day - (prevMonth.getUTCDay() - this.o.weekStart + 7)%7);
			var nextMonth = new Date(prevMonth);
			nextMonth.setUTCDate(nextMonth.getUTCDate() + 42);
			nextMonth = nextMonth.valueOf();
			var html = [];
			var clsName;
			while (prevMonth.valueOf() < nextMonth){
				if (prevMonth.getUTCDay() === this.o.weekStart){
					html.push('<tr>');
					if (this.o.calendarWeeks){
						// ISO 8601: First week contains first thursday.
						// ISO also states week starts on Monday, but we can be more abstract here.
						var
							// Start of current week: based on weekstart/current date
							ws = new Date(+prevMonth + (this.o.weekStart - prevMonth.getUTCDay() - 7) % 7 * 864e5),
							// Thursday of this week
							th = new Date(Number(ws) + (7 + 4 - ws.getUTCDay()) % 7 * 864e5),
							// First Thursday of year, year from thursday
							yth = new Date(Number(yth = UTCDate(th.getUTCFullYear(), 0, 1)) + (7 + 4 - yth.getUTCDay())%7*864e5),
							// Calendar week: ms between thursdays, div ms per day, div 7 days
							calWeek =  (th - yth) / 864e5 / 7 + 1;
						html.push('<td class="cw">'+ calWeek +'</td>');

					}
				}
				clsName = this.getClassNames(prevMonth);
				clsName.push('day');

				if (this.o.beforeShowDay !== $.noop){
					var before = this.o.beforeShowDay(this._utc_to_local(prevMonth));
					if (before === undefined)
						before = {};
					else if (typeof(before) === 'boolean')
						before = {enabled: before};
					else if (typeof(before) === 'string')
						before = {classes: before};
					if (before.enabled === false)
						clsName.push('disabled');
					if (before.classes)
						clsName = clsName.concat(before.classes.split(/\s+/));
					if (before.tooltip)
						tooltip = before.tooltip;
				}

				clsName = $.unique(clsName);
				html.push('<td class="'+clsName.join(' ')+'"' + (tooltip ? ' title="'+tooltip+'"' : '') + '>'+prevMonth.getUTCDate() + '</td>');
				if (prevMonth.getUTCDay() === this.o.weekEnd){
					html.push('</tr>');
				}
				prevMonth.setUTCDate(prevMonth.getUTCDate()+1);
			}
			this.picker.find('.cfdatepicker-days tbody').empty().append(html.join(''));

			var months = this.picker.find('.cfdatepicker-months')
				.find('th:eq(1)')
				.text(year)
				.end()
				.find('span').removeClass('active');

			$.each(this.dates, function(i, d){
				if (d.getUTCFullYear() === year)
					months.eq(d.getUTCMonth()).addClass('active');
			});

			if (year < startYear || year > endYear){
				months.addClass('disabled');
			}
			if (year === startYear){
				months.slice(0, startMonth).addClass('disabled');
			}
			if (year === endYear){
				months.slice(endMonth+1).addClass('disabled');
			}

			html = '';
			year = parseInt(year/10, 10) * 10;
			var yearCont = this.picker.find('.cfdatepicker-years')
				.find('th:eq(1)')
				.text(year + '-' + (year + 9))
				.end()
				.find('td');
			year -= 1;
			var years = $.map(this.dates, function(d){
					return d.getUTCFullYear();
				}),
				classes;
			for (var i = -1; i < 11; i++){
				classes = ['year'];
				if (i === -1)
					classes.push('old');
				else if (i === 10)
					classes.push('new');
				if ($.inArray(year, years) !== -1)
					classes.push('active');
				if (year < startYear || year > endYear)
					classes.push('disabled');
				html += '<span class="' + classes.join(' ') + '">'+year+'</span>';
				year += 1;
			}
			yearCont.html(html);
		},

		updateNavArrows: function(){
			if (!this._allow_update)
				return;

			var d = new Date(this.viewDate),
				year = d.getUTCFullYear(),
				month = d.getUTCMonth();
			switch (this.viewMode){
				case 0:
					if (this.o.startDate !== -Infinity && year <= this.o.startDate.getUTCFullYear() && month <= this.o.startDate.getUTCMonth()){
						this.picker.find('.prev').css({visibility: 'hidden'});
					}
					else {
						this.picker.find('.prev').css({visibility: 'visible'});
					}
					if (this.o.endDate !== Infinity && year >= this.o.endDate.getUTCFullYear() && month >= this.o.endDate.getUTCMonth()){
						this.picker.find('.next').css({visibility: 'hidden'});
					}
					else {
						this.picker.find('.next').css({visibility: 'visible'});
					}
					break;
				case 1:
				case 2:
					if (this.o.startDate !== -Infinity && year <= this.o.startDate.getUTCFullYear()){
						this.picker.find('.prev').css({visibility: 'hidden'});
					}
					else {
						this.picker.find('.prev').css({visibility: 'visible'});
					}
					if (this.o.endDate !== Infinity && year >= this.o.endDate.getUTCFullYear()){
						this.picker.find('.next').css({visibility: 'hidden'});
					}
					else {
						this.picker.find('.next').css({visibility: 'visible'});
					}
					break;
			}
		},

		click: function(e){
			e.preventDefault();
			var target = $(e.target).closest('span, td, th'),
				year, month, day;
			if (target.length === 1){
				switch (target[0].nodeName.toLowerCase()){
					case 'th':
						switch (target[0].className){
							case 'cfdatepicker-switch':
								this.showMode(1);
								break;
							case 'prev':
							case 'next':
								var dir = DPGlobal.modes[this.viewMode].navStep * (target[0].className === 'prev' ? -1 : 1);
								switch (this.viewMode){
									case 0:
										this.viewDate = this.moveMonth(this.viewDate, dir);
										this._trigger('changeMonth', this.viewDate);
										break;
									case 1:
									case 2:
										this.viewDate = this.moveYear(this.viewDate, dir);
										if (this.viewMode === 1)
											this._trigger('changeYear', this.viewDate);
										break;
								}
								this.fill();
								break;
							case 'today':
								var date = new Date();
								date = UTCDate(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0);

								this.showMode(-2);
								var which = this.o.todayBtn === 'linked' ? null : 'view';
								this._setDate(date, which);
								break;
							case 'clear':
								var element;
								if (this.isInput)
									element = this.element;
								else if (this.component)
									element = this.element.find('input');
								if (element)
									element.val("").change();
								this.update();
								this._trigger('changeDate');
								if (this.o.autoclose)
									this.hide();
								break;
						}
						break;
					case 'span':
						if (!target.is('.disabled')){
							this.viewDate.setUTCDate(1);
							if (target.is('.month')){
								day = 1;
								month = target.parent().find('span').index(target);
								year = this.viewDate.getUTCFullYear();
								this.viewDate.setUTCMonth(month);
								this._trigger('changeMonth', this.viewDate);
								if (this.o.minViewMode === 1){
									this._setDate(UTCDate(year, month, day));
								}
							}
							else {
								day = 1;
								month = 0;
								year = parseInt(target.text(), 10)||0;
								this.viewDate.setUTCFullYear(year);
								this._trigger('changeYear', this.viewDate);
								if (this.o.minViewMode === 2){
									this._setDate(UTCDate(year, month, day));
								}
							}
							this.showMode(-1);
							this.fill();
						}
						break;
					case 'td':
						if (target.is('.day') && !target.is('.disabled')){
							day = parseInt(target.text(), 10)||1;
							year = this.viewDate.getUTCFullYear();
							month = this.viewDate.getUTCMonth();
							if (target.is('.old')){
								if (month === 0){
									month = 11;
									year -= 1;
								}
								else {
									month -= 1;
								}
							}
							else if (target.is('.new')){
								if (month === 11){
									month = 0;
									year += 1;
								}
								else {
									month += 1;
								}
							}
							this._setDate(UTCDate(year, month, day));
						}
						break;
				}
			}
			if (this.picker.is(':visible') && this._focused_from){
				$(this._focused_from).focus();
			}
			delete this._focused_from;
		},

		_toggle_multidate: function(date){
			var ix = this.dates.contains(date);
			if (!date){
				this.dates.clear();
			}
			else if (ix !== -1){
				this.dates.remove(ix);
			}
			else {
				this.dates.push(date);
			}
			if (typeof this.o.multidate === 'number')
				while (this.dates.length > this.o.multidate)
					this.dates.remove(0);
		},

		_setDate: function(date, which){
			if (!which || which === 'date')
				this._toggle_multidate(date && new Date(date));
			if (!which || which  === 'view')
				this.viewDate = date && new Date(date);

			this.fill();
			this.setValue();
			this._trigger('changeDate');
			var element;
			if (this.isInput){
				element = this.element;
			}
			else if (this.component){
				element = this.element.find('input');
			}
			if (element){
				element.change();
			}
			if (this.o.autoclose && (!which || which === 'date')){
				this.hide();
			}
		},

		moveMonth: function(date, dir){
			if (!date)
				return undefined;
			if (!dir)
				return date;
			var new_date = new Date(date.valueOf()),
				day = new_date.getUTCDate(),
				month = new_date.getUTCMonth(),
				mag = Math.abs(dir),
				new_month, test;
			dir = dir > 0 ? 1 : -1;
			if (mag === 1){
				test = dir === -1
					// If going back one month, make sure month is not current month
					// (eg, Mar 31 -> Feb 31 == Feb 28, not Mar 02)
					? function(){
					return new_date.getUTCMonth() === month;
				}
					// If going forward one month, make sure month is as expected
					// (eg, Jan 31 -> Feb 31 == Feb 28, not Mar 02)
					: function(){
					return new_date.getUTCMonth() !== new_month;
				};
				new_month = month + dir;
				new_date.setUTCMonth(new_month);
				// Dec -> Jan (12) or Jan -> Dec (-1) -- limit expected date to 0-11
				if (new_month < 0 || new_month > 11)
					new_month = (new_month + 12) % 12;
			}
			else {
				// For magnitudes >1, move one month at a time...
				for (var i=0; i < mag; i++)
					// ...which might decrease the day (eg, Jan 31 to Feb 28, etc)...
					new_date = this.moveMonth(new_date, dir);
				// ...then reset the day, keeping it in the new month
				new_month = new_date.getUTCMonth();
				new_date.setUTCDate(day);
				test = function(){
					return new_month !== new_date.getUTCMonth();
				};
			}
			// Common date-resetting loop -- if date is beyond end of month, make it
			// end of month
			while (test()){
				new_date.setUTCDate(--day);
				new_date.setUTCMonth(new_month);
			}
			return new_date;
		},

		moveYear: function(date, dir){
			return this.moveMonth(date, dir*12);
		},

		dateWithinRange: function(date){
			return date >= this.o.startDate && date <= this.o.endDate;
		},

		keydown: function(e){
			if (this.picker.is(':not(:visible)')){
				if (e.keyCode === 27) // allow escape to hide and re-show picker
					this.show();
				return;
			}
			var dateChanged = false,
				dir, newDate, newViewDate,
				focusDate = this.focusDate || this.viewDate;
			switch (e.keyCode){
				case 27: // escape
					if (this.focusDate){
						this.focusDate = null;
						this.viewDate = this.dates.get(-1) || this.viewDate;
						this.fill();
					}
					else
						this.hide();
					e.preventDefault();
					break;
				case 37: // left
				case 39: // right
					if (!this.o.keyboardNavigation)
						break;
					dir = e.keyCode === 37 ? -1 : 1;
					if (e.ctrlKey){
						newDate = this.moveYear(this.dates.get(-1) || UTCToday(), dir);
						newViewDate = this.moveYear(focusDate, dir);
						this._trigger('changeYear', this.viewDate);
					}
					else if (e.shiftKey){
						newDate = this.moveMonth(this.dates.get(-1) || UTCToday(), dir);
						newViewDate = this.moveMonth(focusDate, dir);
						this._trigger('changeMonth', this.viewDate);
					}
					else {
						newDate = new Date(this.dates.get(-1) || UTCToday());
						newDate.setUTCDate(newDate.getUTCDate() + dir);
						newViewDate = new Date(focusDate);
						newViewDate.setUTCDate(focusDate.getUTCDate() + dir);
					}
					if (this.dateWithinRange(newDate)){
						this.focusDate = this.viewDate = newViewDate;
						this.setValue();
						this.fill();
						e.preventDefault();
					}
					break;
				case 38: // up
				case 40: // down
					if (!this.o.keyboardNavigation)
						break;
					dir = e.keyCode === 38 ? -1 : 1;
					if (e.ctrlKey){
						newDate = this.moveYear(this.dates.get(-1) || UTCToday(), dir);
						newViewDate = this.moveYear(focusDate, dir);
						this._trigger('changeYear', this.viewDate);
					}
					else if (e.shiftKey){
						newDate = this.moveMonth(this.dates.get(-1) || UTCToday(), dir);
						newViewDate = this.moveMonth(focusDate, dir);
						this._trigger('changeMonth', this.viewDate);
					}
					else {
						newDate = new Date(this.dates.get(-1) || UTCToday());
						newDate.setUTCDate(newDate.getUTCDate() + dir * 7);
						newViewDate = new Date(focusDate);
						newViewDate.setUTCDate(focusDate.getUTCDate() + dir * 7);
					}
					if (this.dateWithinRange(newDate)){
						this.focusDate = this.viewDate = newViewDate;
						this.setValue();
						this.fill();
						e.preventDefault();
					}
					break;
				case 32: // spacebar
					// Spacebar is used in manually typing dates in some formats.
					// As such, its behavior should not be hijacked.
					break;
				case 13: // enter
					focusDate = this.focusDate || this.dates.get(-1) || this.viewDate;
					this._toggle_multidate(focusDate);
					dateChanged = true;
					this.focusDate = null;
					this.viewDate = this.dates.get(-1) || this.viewDate;
					this.setValue();
					this.fill();
					if (this.picker.is(':visible')){
						e.preventDefault();
						if (this.o.autoclose)
							this.hide();
					}
					break;
				case 9: // tab
					this.focusDate = null;
					this.viewDate = this.dates.get(-1) || this.viewDate;
					this.fill();
					this.hide();
					break;
			}
			if (dateChanged){
				if (this.dates.length)
					this._trigger('changeDate');
				else
					this._trigger('clearDate');
				var element;
				if (this.isInput){
					element = this.element;
				}
				else if (this.component){
					element = this.element.find('input');
				}
				if (element){
					element.change();
				}
			}
		},

		showMode: function(dir){
			if (dir){
				this.viewMode = Math.max(this.o.minViewMode, Math.min(2, this.viewMode + dir));
			}
			this.picker
				.find('>div')
				.hide()
				.filter('.cfdatepicker-'+DPGlobal.modes[this.viewMode].clsName)
				.css('display', 'block');
			this.updateNavArrows();
		}
	};

	var DateRangePicker = function(element, options){
		this.element = $(element);
		this.inputs = $.map(options.inputs, function(i){
			return i.jquery ? i[0] : i;
		});
		delete options.inputs;

		$(this.inputs)
			.cfdatepicker(options)
			.bind('changeDate', $.proxy(this.dateUpdated, this));

		this.pickers = $.map(this.inputs, function(i){
			return $(i).data('cfdatepicker');
		});
		this.updateDates();
	};
	DateRangePicker.prototype = {
		updateDates: function(){
			this.dates = $.map(this.pickers, function(i){
				return i.getUTCDate();
			});
			this.updateRanges();
		},
		updateRanges: function(){
			var range = $.map(this.dates, function(d){
				return d.valueOf();
			});
			$.each(this.pickers, function(i, p){
				p.setRange(range);
			});
		},
		dateUpdated: function(e){
			// `this.updating` is a workaround for preventing infinite recursion
			// between `changeDate` triggering and `setUTCDate` calling.  Until
			// there is a better mechanism.
			if (this.updating)
				return;
			this.updating = true;

			var dp = $(e.target).data('cfdatepicker'),
				new_date = dp.getUTCDate(),
				i = $.inArray(e.target, this.inputs),
				l = this.inputs.length;
			if (i === -1)
				return;

			$.each(this.pickers, function(i, p){
				if (!p.getUTCDate())
					p.setUTCDate(new_date);
			});

			if (new_date < this.dates[i]){
				// Date being moved earlier/left
				while (i >= 0 && new_date < this.dates[i]){
					this.pickers[i--].setUTCDate(new_date);
				}
			}
			else if (new_date > this.dates[i]){
				// Date being moved later/right
				while (i < l && new_date > this.dates[i]){
					this.pickers[i++].setUTCDate(new_date);
				}
			}
			this.updateDates();

			delete this.updating;
		},
		remove: function(){
			$.map(this.pickers, function(p){ p.remove(); });
			delete this.element.data().cfdatepicker;
		}
	};

	function opts_from_el(el, prefix){
		// Derive options from element data-attrs
		var data = $(el).data(),
			out = {}, inkey,
			replace = new RegExp('^' + prefix.toLowerCase() + '([A-Z])');
		prefix = new RegExp('^' + prefix.toLowerCase());
		function re_lower(_,a){
			return a.toLowerCase();
		}
		for (var key in data)
			if (prefix.test(key)){
				inkey = key.replace(replace, re_lower);
				out[inkey] = data[key];
			}
		return out;
	}

	function opts_from_locale(lang){
		// Derive options from locale plugins
		var out = {};
		// Check if "de-DE" style date is available, if not language should
		// fallback to 2 letter code eg "de"
		if (!dates[lang]){
			lang = lang.split('-')[0];
			if (!dates[lang])
				return;
		}
		var d = dates[lang];
		$.each(locale_opts, function(i,k){
			if (k in d)
				out[k] = d[k];
		});
		return out;
	}

	var old = $.fn.cfdatepicker;
	$.fn.cfdatepicker = function(option){
		var args = Array.apply(null, arguments);
		args.shift();
		var internal_return;
		this.each(function(){
			var $this = $(this),
				data = $this.data('cfdatepicker'),
				options = typeof option === 'object' && option;
			if (!data){
				var elopts = opts_from_el(this, 'date'),
					// Preliminary otions
					xopts = $.extend({}, defaults, elopts, options),
					locopts = opts_from_locale(xopts.language),
					// Options priority: js args, data-attrs, locales, defaults
					opts = $.extend({}, defaults, locopts, elopts, options);
				if ($this.is('.input-daterange') || opts.inputs){
					var ropts = {
						inputs: opts.inputs || $this.find('input').toArray()
					};
					$this.data('cfdatepicker', (data = new DateRangePicker(this, $.extend(opts, ropts))));
				}
				else {
					$this.data('cfdatepicker', (data = new Datepicker(this, opts)));
				}
			}
			if (typeof option === 'string' && typeof data[option] === 'function'){
				internal_return = data[option].apply(data, args);
				if (internal_return !== undefined)
					return false;
			}
		});
		if (internal_return !== undefined)
			return internal_return;
		else
			return this;
	};

	var defaults = $.fn.cfdatepicker.defaults = {
		autoclose: false,
		beforeShowDay: $.noop,
		calendarWeeks: false,
		clearBtn: false,
		daysOfWeekDisabled: [],
		endDate: Infinity,
		forceParse: true,
		format: 'mm/dd/yyyy',
		keyboardNavigation: true,
		language: 'en',
		minViewMode: 0,
		multidate: false,
		multidateSeparator: ',',
		orientation: "auto",
		rtl: false,
		startDate: -Infinity,
		startView: 0,
		todayBtn: false,
		todayHighlight: false,
		weekStart: 0
	};
	var locale_opts = $.fn.cfdatepicker.locale_opts = [
		'format',
		'rtl',
		'weekStart'
	];
	$.fn.cfdatepicker.Constructor = Datepicker;
	var dates = $.fn.cfdatepicker.dates = {
		en: {
			days: ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
			daysShort: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"],
			daysMin: ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa", "Su"],
			months: ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"],
			monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
			today: "Today",
			clear: "Clear"
		}
	};

	var DPGlobal = {
		modes: [
			{
				clsName: 'days',
				navFnc: 'Month',
				navStep: 1
			},
			{
				clsName: 'months',
				navFnc: 'FullYear',
				navStep: 1
			},
			{
				clsName: 'years',
				navFnc: 'FullYear',
				navStep: 10
			}],
		isLeapYear: function(year){
			return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0));
		},
		getDaysInMonth: function(year, month){
			return [31, (DPGlobal.isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
		},
		validParts: /dd?|DD?|mm?|MM?|yy(?:yy)?/g,
		nonpunctuation: /[^ -\/:-@\[\u3400-\u9fff-`{-~\t\n\r]+/g,
		parseFormat: function(format){
			// IE treats \0 as a string end in inputs (truncating the value),
			// so it's a bad format delimiter, anyway
			var separators = format.replace(this.validParts, '\0').split('\0'),
				parts = format.match(this.validParts);
			if (!separators || !separators.length || !parts || parts.length === 0){
				throw new Error("Invalid date format.");
			}
			return {separators: separators, parts: parts};
		},
		parseDate: function(date, format, language){
			if (!date)
				return undefined;
			if (date instanceof Date)
				return date;
			if (typeof format === 'string')
				format = DPGlobal.parseFormat(format);
			var part_re = /([\-+]\d+)([dmwy])/,
				parts = date.match(/([\-+]\d+)([dmwy])/g),
				part, dir, i;
			if (/^[\-+]\d+[dmwy]([\s,]+[\-+]\d+[dmwy])*$/.test(date)){
				date = new Date();
				for (i=0; i < parts.length; i++){
					part = part_re.exec(parts[i]);
					dir = parseInt(part[1]);
					switch (part[2]){
						case 'd':
							date.setUTCDate(date.getUTCDate() + dir);
							break;
						case 'm':
							date = Datepicker.prototype.moveMonth.call(Datepicker.prototype, date, dir);
							break;
						case 'w':
							date.setUTCDate(date.getUTCDate() + dir * 7);
							break;
						case 'y':
							date = Datepicker.prototype.moveYear.call(Datepicker.prototype, date, dir);
							break;
					}
				}
				return UTCDate(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(), 0, 0, 0);
			}
			parts = date && date.match(this.nonpunctuation) || [];
			date = new Date();
			var parsed = {},
				setters_order = ['yyyy', 'yy', 'M', 'MM', 'm', 'mm', 'd', 'dd'],
				setters_map = {
					yyyy: function(d,v){
						return d.setUTCFullYear(v);
					},
					yy: function(d,v){
						return d.setUTCFullYear(2000+v);
					},
					m: function(d,v){
						if (isNaN(d))
							return d;
						v -= 1;
						while (v < 0) v += 12;
						v %= 12;
						d.setUTCMonth(v);
						while (d.getUTCMonth() !== v)
							d.setUTCDate(d.getUTCDate()-1);
						return d;
					},
					d: function(d,v){
						return d.setUTCDate(v);
					}
				},
				val, filtered;
			setters_map['M'] = setters_map['MM'] = setters_map['mm'] = setters_map['m'];
			setters_map['dd'] = setters_map['d'];
			date = UTCDate(date.getFullYear(), date.getMonth(), date.getDate(), 0, 0, 0);
			var fparts = format.parts.slice();
			// Remove noop parts
			if (parts.length !== fparts.length){
				fparts = $(fparts).filter(function(i,p){
					return $.inArray(p, setters_order) !== -1;
				}).toArray();
			}
			// Process remainder
			function match_part(){
				var m = this.slice(0, parts[i].length),
					p = parts[i].slice(0, m.length);
				return m === p;
			}
			if (parts.length === fparts.length){
				var cnt;
				for (i=0, cnt = fparts.length; i < cnt; i++){
					val = parseInt(parts[i], 10);
					part = fparts[i];
					if (isNaN(val)){
						switch (part){
							case 'MM':
								filtered = $(dates[language].months).filter(match_part);
								val = $.inArray(filtered[0], dates[language].months) + 1;
								break;
							case 'M':
								filtered = $(dates[language].monthsShort).filter(match_part);
								val = $.inArray(filtered[0], dates[language].monthsShort) + 1;
								break;
						}
					}
					parsed[part] = val;
				}
				var _date, s;
				for (i=0; i < setters_order.length; i++){
					s = setters_order[i];
					if (s in parsed && !isNaN(parsed[s])){
						_date = new Date(date);
						setters_map[s](_date, parsed[s]);
						if (!isNaN(_date))
							date = _date;
					}
				}
			}
			return date;
		},
		formatDate: function(date, format, language){
			if (!date)
				return '';
			if (typeof format === 'string')
				format = DPGlobal.parseFormat(format);
			var val = {
				d: date.getUTCDate(),
				D: dates[language].daysShort[date.getUTCDay()],
				DD: dates[language].days[date.getUTCDay()],
				m: date.getUTCMonth() + 1,
				M: dates[language].monthsShort[date.getUTCMonth()],
				MM: dates[language].months[date.getUTCMonth()],
				yy: date.getUTCFullYear().toString().substring(2),
				yyyy: date.getUTCFullYear()
			};
			val.dd = (val.d < 10 ? '0' : '') + val.d;
			val.mm = (val.m < 10 ? '0' : '') + val.m;
			date = [];
			var seps = $.extend([], format.separators);
			for (var i=0, cnt = format.parts.length; i <= cnt; i++){
				if (seps.length)
					date.push(seps.shift());
				date.push(val[format.parts[i]]);
			}
			return date.join('');
		},
		headTemplate: '<thead>'+
		'<tr>'+
		'<th class="prev">&laquo;</th>'+
		'<th colspan="5" class="cfdatepicker-switch"></th>'+
		'<th class="next">&raquo;</th>'+
		'</tr>'+
		'</thead>',
		contTemplate: '<tbody><tr><td colspan="7"></td></tr></tbody>',
		footTemplate: '<tfoot>'+
		'<tr>'+
		'<th colspan="7" class="today"></th>'+
		'</tr>'+
		'<tr>'+
		'<th colspan="7" class="clear"></th>'+
		'</tr>'+
		'</tfoot>'
	};
	DPGlobal.template = '<div class="cfdatepicker">'+
		'<div class="cfdatepicker-days">'+
		'<table class=" table-condensed">'+
		DPGlobal.headTemplate+
		'<tbody></tbody>'+
		DPGlobal.footTemplate+
		'</table>'+
		'</div>'+
		'<div class="cfdatepicker-months">'+
		'<table class="table-condensed">'+
		DPGlobal.headTemplate+
		DPGlobal.contTemplate+
		DPGlobal.footTemplate+
		'</table>'+
		'</div>'+
		'<div class="cfdatepicker-years">'+
		'<table class="table-condensed">'+
		DPGlobal.headTemplate+
		DPGlobal.contTemplate+
		DPGlobal.footTemplate+
		'</table>'+
		'</div>'+
		'</div>';

	$.fn.cfdatepicker.DPGlobal = DPGlobal;


	/* DATEPICKER NO CONFLICT
	 * =================== */

	$.fn.cfdatepicker.noConflict = function(){
		$.fn.cfdatepicker = old;
		return this;
	};

	/* DATEPICKER DATA-API
	 * ================== */
	$(document).on(
		'focus.cfdatepicker.data-api click.cfdatepicker.data-api',
		'[data-provide="cfdatepicker"]',
		function(e){
			var $this = $(this);
			if ($this.data('cfdatepicker'))
				return;

			// component click requires us to explicitly show it
			e.preventDefault();
			$this.cfdatepicker('show')
				.on('show', function(){ $(this).trigger('blur'); })
				.on('hide', function(){ $(this).attr("disabled", false); })
		}
	);

}(window.jQuery));


/*
 * jquery.inputmask.bundle
 * http://github.com/RobinHerbots/jquery.inputmask
 * Copyright (c) 2010 - 2014 Robin Herbots
 * Licensed under the MIT license (http://www.opensource.org/licenses/mit-license.php)
 * Version: 3.1.27
 */


jQuery(document).ready(function($){
	jQuery(document).on('cf.add', function(){
		$("[data-inputmask]").inputmask();
	});
	jQuery(document).trigger('cf.add');
});


/*! rangeslider.js - v0.3.1 | (c) 2014 @andreruffert | MIT license | https://github.com/andreruffert/rangeslider.js */
'use strict';

(function(factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD. Register as an anonymous module.
		define(['jquery'], factory);
	}
	else if (typeof exports === 'object') {
		// CommonJS
		factory(require('jquery'));
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function($) {

	/**
	 * Range feature detection
	 * @return {Boolean}
	 */
	function supportsRange() {
		var input = document.createElement('input');
		input.setAttribute('type', 'range');
		return input.type !== 'text';
	}

	var pluginName = 'rangeslider',
		pluginInstances = [],
		inputrange = supportsRange(),
		defaults = {
			polyfill: true,
			rangeClass: 'rangeslider',
			disabledClass: 'rangeslider--disabled',
			fillClass: 'rangeslider__fill',
			handleClass: 'rangeslider__handle',
			startEvent: ['mousedown', 'touchstart', 'pointerdown'],
			moveEvent: ['mousemove', 'touchmove', 'pointermove'],
			endEvent: ['mouseup', 'touchend', 'pointerup']
		};

	/**
	 * Delays a function for the given number of milliseconds, and then calls
	 * it with the arguments supplied.
	 *
	 * @param  {Function} fn   [description]
	 * @param  {Number}   wait [description]
	 * @return {Function}
	 */
	function delay(fn, wait) {
		var args = Array.prototype.slice.call(arguments, 2);
		return setTimeout(function(){ return fn.apply(null, args); }, wait);
	}

	/**
	 * Returns a debounced function that will make sure the given
	 * function is not triggered too much.
	 *
	 * @param  {Function} fn Function to debounce.
	 * @param  {Number}   debounceDuration OPTIONAL. The amount of time in milliseconds for which we will debounce the function. (defaults to 100ms)
	 * @return {Function}
	 */
	function debounce(fn, debounceDuration) {
		debounceDuration = debounceDuration || 100;
		return function() {
			if (!fn.debouncing) {
				var args = Array.prototype.slice.apply(arguments);
				fn.lastReturnVal = fn.apply(window, args);
				fn.debouncing = true;
			}
			clearTimeout(fn.debounceTimeout);
			fn.debounceTimeout = setTimeout(function(){
				fn.debouncing = false;
			}, debounceDuration);
			return fn.lastReturnVal;
		};
	}

	/**
	 * Plugin
	 * @param {String} element
	 * @param {Object} options
	 */
	function Plugin(element, options) {
		this.$window    = $(window);
		this.$document  = $(document);
		this.$element   = $(element);
		this.options    = $.extend( {}, defaults, options );
		this._defaults  = defaults;
		this._name      = pluginName;
		this.startEvent = this.options.startEvent.join('.' + pluginName + ' ') + '.' + pluginName;
		this.moveEvent  = this.options.moveEvent.join('.' + pluginName + ' ') + '.' + pluginName;
		this.endEvent   = this.options.endEvent.join('.' + pluginName + ' ') + '.' + pluginName;
		this.polyfill   = this.options.polyfill;
		this.onInit     = this.options.onInit;
		this.onSlide    = this.options.onSlide;
		this.onSlideEnd = this.options.onSlideEnd;

		// Plugin should only be used as a polyfill
		if (this.polyfill) {
			// Input range support?
			if (inputrange) { return false; }
		}

		this.identifier = 'js-' + pluginName + '-' +(+new Date());
		this.min        = parseFloat(this.$element[0].getAttribute('min') || 0);
		this.max        = parseFloat(this.$element[0].getAttribute('max') || 100);
		this.value      = parseFloat(this.$element[0].value || this.min + (this.max-this.min)/2);
		this.step       = parseFloat(this.$element[0].getAttribute('step') || 1);
		this.$fill      = $('<div class="' + this.options.fillClass + '" />');
		this.$handle    = $('<div class="' + this.options.handleClass + '" />');
		this.$range     = $('<div class="' + this.options.rangeClass + '" id="' + this.identifier + '" />').insertAfter(this.$element).prepend(this.$fill, this.$handle);

		// visually hide the input
		this.$element.css({
			'position': 'absolute',
			'width': '1px',
			'height': '1px',
			'overflow': 'hidden',
			'opacity': '0'
		});

		// Store context
		this.handleDown = $.proxy(this.handleDown, this);
		this.handleMove = $.proxy(this.handleMove, this);
		this.handleEnd  = $.proxy(this.handleEnd, this);

		this.init();

		// Attach Events
		var _this = this;
		this.$window.on('resize' + '.' + pluginName, debounce(function() {
			// Simulate resizeEnd event.
			delay(function() { _this.update(); }, 300);
		}, 20));

		this.$document.on(this.startEvent, '#' + this.identifier + ':not(.' + this.options.disabledClass + ')', this.handleDown);

		// Listen to programmatic value changes
		this.$element.on('change' + '.' + pluginName, function(e, data) {
			if (data && data.origin === pluginName) {
				return;
			}

			var value = e.target.value,
				pos = _this.getPositionFromValue(value);
			_this.setPosition(pos);
		});
	}

	Plugin.prototype.init = function() {
		if (this.onInit && typeof this.onInit === 'function') {
			this.onInit();
		}
		this.update();
	};

	Plugin.prototype.update = function() {
		this.handleWidth    = this.$handle[0].offsetWidth;
		this.rangeWidth     = this.$range[0].offsetWidth;
		this.maxHandleX     = this.rangeWidth - this.handleWidth;
		this.grabX          = this.handleWidth / 2;
		this.position       = this.getPositionFromValue(this.value);

		// Consider disabled state
		if (this.$element[0].disabled) {
			this.$range.addClass(this.options.disabledClass);
		} else {
			this.$range.removeClass(this.options.disabledClass);
		}

		this.setPosition(this.position);
	};

	Plugin.prototype.handleDown = function(e) {
		e.preventDefault();
		this.$document.on(this.moveEvent, this.handleMove);
		this.$document.on(this.endEvent, this.handleEnd);

		// If we click on the handle don't set the new position
		if ((' ' + e.target.className + ' ').replace(/[\n\t]/g, ' ').indexOf(this.options.handleClass) > -1) {
			return;
		}

		var posX = this.getRelativePosition(this.$range[0], e),
			handleX = this.getPositionFromNode(this.$handle[0]) - this.getPositionFromNode(this.$range[0]);

		this.setPosition(posX - this.grabX);

		if (posX >= handleX && posX < handleX + this.handleWidth) {
			this.grabX = posX - handleX;
		}
	};

	Plugin.prototype.handleMove = function(e) {
		e.preventDefault();
		var posX = this.getRelativePosition(this.$range[0], e);
		this.setPosition(posX - this.grabX);
	};

	Plugin.prototype.handleEnd = function(e) {
		e.preventDefault();
		this.$document.off(this.moveEvent, this.handleMove);
		this.$document.off(this.endEvent, this.handleEnd);

		var posX = this.getRelativePosition(this.$range[0], e);
		if (this.onSlideEnd && typeof this.onSlideEnd === 'function') {
			this.onSlideEnd(posX - this.grabX, this.value);
		}
	};

	Plugin.prototype.cap = function(pos, min, max) {
		if (pos < min) { return min; }
		if (pos > max) { return max; }
		return pos;
	};

	Plugin.prototype.setPosition = function(pos) {
		var value, left;

		// Snapping steps
		value = (this.getValueFromPosition(this.cap(pos, 0, this.maxHandleX)) / this.step) * this.step;
		left = this.getPositionFromValue(value);

		// Update ui
		this.$fill[0].style.width = (left + this.grabX)  + 'px';
		this.$handle[0].style.left = left + 'px';
		this.setValue(value);

		// Update globals
		this.position = left;
		this.value = value;

		if (this.onSlide && typeof this.onSlide === 'function') {
			this.onSlide(left, value);
		}
	};

	Plugin.prototype.getPositionFromNode = function(node) {
		var i = 0;
		while (node !== null) {
			i += node.offsetLeft;
			node = node.offsetParent;
		}
		return i;
	};

	Plugin.prototype.getRelativePosition = function(node, e) {
		return (e.pageX || e.originalEvent.clientX || e.originalEvent.touches[0].clientX || e.currentPoint.x) - this.getPositionFromNode(node);
	};

	Plugin.prototype.getPositionFromValue = function(value) {
		var percentage, pos;
		percentage = (value - this.min)/(this.max - this.min);
		pos = percentage * this.maxHandleX;
		return pos;
	};

	Plugin.prototype.getValueFromPosition = function(pos) {
		var percentage, value;
		percentage = ((pos) / (this.maxHandleX || 1));
		value = this.step * Math.ceil((((percentage) * (this.max - this.min)) + this.min) / this.step);
		return Number((value).toFixed(2));
	};

	Plugin.prototype.setValue = function(value) {
		if (value !== this.value) {
			this.$element.val(value).trigger('change', {origin: pluginName});
		}
	};

	Plugin.prototype.destroy = function() {
		this.$document.off(this.startEvent, '#' + this.identifier, this.handleDown);
		this.$element
			.off('.' + pluginName)
			.removeAttr('style')
			.removeData('plugin_' + pluginName);

		// Remove the generated markup
		if (this.$range && this.$range.length) {
			this.$range[0].parentNode.removeChild(this.$range[0]);
		}

		// Remove global events if there isn't any instance anymore.
		pluginInstances.splice(pluginInstances.indexOf(this.$element[0]),1);
		if (!pluginInstances.length) {
			this.$window.off('.' + pluginName);
		}
	};

	// A really lightweight plugin wrapper around the constructor,
	// preventing against multiple instantiations
	$.fn[pluginName] = function(options) {
		return this.each(function() {
			var $this = $(this),
				data  = $this.data('plugin_' + pluginName);

			// Create a new instance.
			if (!data) {
				$this.data('plugin_' + pluginName, (data = new Plugin(this, options)));
				pluginInstances.push(this);
			}

			// Make it possible to access methods from public.
			// e.g `$element.rangeslider('method');`
			if (typeof options === 'string') {
				data[options]();
			}
		});
	};

}));

/*!
 * jQuery Raty - A Star Rating Plugin
 *
 * The MIT License
 *
 * @author  : Washington Botelho
 * @doc     : http://wbotelhos.com/raty
 * @version : 2.6.0
 *
 */

;(function($) {
	'use strict';

	var methods = {
		init: function(options) {
			return this.each(function() {
				this.self = $(this);

				methods.destroy.call(this.self);

				this.opt = $.extend(true, {}, $.fn.raty.defaults, options);

				methods._adjustCallback.call(this);

				methods._adjustNumber.call(this);

				if (this.opt.starType !== 'img') {
					methods._adjustStarType.call(this);
				}

				methods._adjustPath.call(this);
				methods._createStars.call(this);

				if (this.opt.cancel) {
					methods._createCancel.call(this);
				}

				if (this.opt.precision) {
					methods._adjustPrecision.call(this);
				}

				methods._createScore.call(this);
				methods._apply.call(this, this.opt.score);
				methods._target.call(this, this.opt.score);

				if (this.opt.readOnly) {
					methods._lock.call(this);
				} else {
					this.style.cursor = 'pointer';

					methods._binds.call(this);
				}

				this.self.data('options', this.opt);
			});
		},

		_adjustCallback: function() {
			var options = ['number', 'readOnly', 'score', 'scoreName'];

			for (var i = 0; i < options.length; i++) {
				if (typeof this.opt[options[i]] === 'function') {
					this.opt[options[i]] = this.opt[options[i]].call(this);
				}
			}
		},

		_adjustNumber: function() {
			this.opt.number = methods._between(this.opt.number, 1, this.opt.numberMax);
		},

		_adjustPath: function() {
			this.opt.path = this.opt.path || '';

			if (this.opt.path && this.opt.path.charAt(this.opt.path.length - 1) !== '/') {
				this.opt.path += '/';
			}
		},

		_adjustPrecision: function() {
			this.opt.half       = true;
			this.opt.targetType = 'score';
		},

		_adjustStarType: function() {
			this.opt.path = '';

			var replaces = ['cancelOff', 'cancelOn', 'starHalf', 'starOff', 'starOn'];

			for (var i = 0; i < replaces.length; i++) {
				this.opt[replaces[i]] = this.opt[replaces[i]].replace('.', '-');
			}
		},

		_apply: function(score) {
			methods._fill.call(this, score);

			if (score) {
				if (score > 0) {
					this.score.val(methods._between(score, 0, this.opt.number));
				}

				methods._roundStars.call(this, score);
			}
		},

		_between: function(value, min, max) {
			return Math.min(Math.max(parseFloat(value), min), max);
		},

		_binds: function() {
			if (this.cancel) {
				methods._bindOverCancel.call(this);
				methods._bindClickCancel.call(this);
				methods._bindOutCancel.call(this);
			}

			methods._bindOver.call(this);
			methods._bindClick.call(this);
			methods._bindOut.call(this);
		},

		_bindClick: function() {
			var that = this;

			that.stars.on('click.raty', function(evt) {
				var star = $(this);

				that.score.val((that.opt.half || that.opt.precision) ? that.self.data('score') : (this.alt || star.data('alt')));

				if (that.opt.click) {
					that.opt.click.call(that, +that.score.val(), evt);
				}
			});
		},

		_bindClickCancel: function() {
			var that = this;

			that.cancel.on('click.raty', function(evt) {
				that.score.removeAttr('value');

				if (that.opt.click) {
					that.opt.click.call(that, null, evt);
				}
			});
		},

		_bindOut: function() {
			var that = this;

			that.self.on('mouseleave.raty', function(evt) {
				var score = +that.score.val() || undefined;

				methods._apply.call(that, score);
				methods._target.call(that, score, evt);

				if (that.opt.mouseout) {
					that.opt.mouseout.call(that, score, evt);
				}
			});
		},

		_bindOutCancel: function() {
			var that = this;

			that.cancel.on('mouseleave.raty', function(evt) {
				var
					cancel    = $(this),
					cancelOff = that.opt.path + that.opt.cancelOff;

				if (that.opt.starType === 'img') {
					cancel.attr('src', cancelOff);
				} else {
					var cancelOn = that.opt.path + that.opt.cancelOn;

					cancel.removeClass(cancelOn).addClass(cancelOff);
				}

				if (that.opt.mouseout) {
					var score = +that.score.val() || undefined;

					that.opt.mouseout.call(that, score, evt);
				}
			});
		},

		_bindOver: function() {
			var that   = this,
				action = that.opt.half ? 'mousemove.raty' : 'mouseover.raty';

			that.stars.on(action, function(evt) {
				var score = methods._getScoreByPosition.call(that, evt, this);

				methods._fill.call(that, score);

				if (that.opt.half) {
					methods._roundStars.call(that, score);

					that.self.data('score', score);
				}

				methods._target.call(that, score, evt);

				if (that.opt.mouseover) {
					that.opt.mouseover.call(that, score, evt);
				}
			});
		},

		_bindOverCancel: function() {
			var that = this;

			that.cancel.on('mouseover.raty', function(evt) {
				var
					cancelOn  = that.opt.path + that.opt.cancelOn,
					star      = $(this),
					starOff   = that.opt.path + that.opt.starOff;

				if (that.opt.starType === 'img') {
					star.attr('src', cancelOn);
					that.stars.attr('src', starOff);
				} else {
					that.stars.attr('class', starOff);

					var cancelOff = that.opt.path + that.opt.cancelOff;

					star.removeClass(cancelOff).addClass(cancelOn).css('color', that.opt.starColor);
				}

				methods._target.call(that, null, evt);

				if (that.opt.mouseover) {
					that.opt.mouseover.call(that, null);
				}
			});
		},

		_buildScoreField: function() {
			return $('<input />', { name: this.opt.scoreName, type: 'hidden' }).appendTo(this);
		},

		_createCancel: function() {
			var icon   = this.opt.path + this.opt.cancelOff,
				cancel = $('<' + this.opt.starType + ' />', { title: this.opt.cancelHint, 'class': 'raty-cancel' }).css('marginRight', this.opt.space ? this.opt.spaceWidth + 'px' : '0');

			if (this.opt.starType === 'img') {
				cancel.attr({ src: icon, alt: 'x' });
			} else {
				// TODO: use $.data
				cancel.attr('data-alt', 'x').addClass(icon);
			}

			if (this.opt.cancelPlace === 'left') {
				this.self.prepend('&#160;').prepend(cancel);
			} else {
				this.self.append('&#160;').append(cancel);
			}

			this.cancel = cancel;
		},

		_createScore: function() {
			var score = $(this.opt.targetScore);

			this.score = score.length ? score : methods._buildScoreField.call(this);
		},

		_createStars: function() {
			for (var i = 1; i <= this.opt.number; i++) {
				var
					attrs ,
					icon  = (this.opt.score && this.opt.score >= i) ? 'starOn' : 'starOff',
					title = methods._getHint.call(this, i);

				// TODO: extract as icon: && alt:
				icon = this.opt.path + this.opt[icon];

				if (this.opt.starType !== 'img') {
					// TODO: use $.data.
					attrs = { 'data-alt': i, 'class': icon };
				} else {
					attrs = { src: icon, alt: i };
				}

				attrs.title = title;

				$('<' + this.opt.starType + ' />', attrs).css('marginRight', i < this.opt.number && this.opt.space ? this.opt.spaceWidth + 'px' : '0').appendTo(this);

				if (this.opt.space) {
					// this.self.append(i < this.opt.number ? '&#160;' : '');
				}
			}

			this.stars = this.self.children(this.opt.starType);
		},

		_error: function(message) {
			$(this).text(message);

			$.error(message);
		},

		_fill: function(score) {
			var hash = 0;

			for (var i = 1; i <= this.stars.length; i++) {
				var
					icon,
					star   = this.stars.eq(i - 1),
					turnOn = methods._turnOn.call(this, i, score);

				if (this.opt.iconRange && this.opt.iconRange.length > hash) {
					var irange = this.opt.iconRange[hash];

					icon = methods._getIconRange.call(this, irange, turnOn);

					if (i <= irange.range) {
						// TODO: extract.
						if (this.opt.starType === 'img') {
							star.attr('src', icon);
						} else {
							star.attr('class', icon);
						}
					}

					if (i === irange.range) {
						hash++;
					}
				} else {
					icon = this.opt.path + this.opt[turnOn ? 'starOn' : 'starOff'];
					// TODO: extract.
					if (this.opt.starType === 'img') {
						star.attr('src', icon);
					} else {
						star.attr('class', icon);
					}
					// Set Color
					if (turnOn) {
						star.css('color', this.opt.starColor);
					}else{
						star.css('color', '');
					}
				}
			}
		},

		_getIconRange: function(irange, turnOn) {
			return this.opt.path + (turnOn ? irange.on || this.opt.starOn : irange.off || this.opt.starOff);
		},

		_getScoreByPosition: function(evt, icon) {
			var
				star  = $(icon),
				score = parseInt(icon.alt || star.data('alt'), 10);

			if (this.opt.half) {
				var
					size    = methods._getSize.call(this),
					percent = parseFloat((evt.pageX - star.offset().left) / size);

				if (this.opt.precision) {
					score = score - 1 + percent;
				} else {
					score = score - 1 + (percent > 0.5 ? 1 : 0.5);
				}
			}


			return score;
		},

		_getSize: function() {
			var size;

			if (this.opt.starType === 'img') {
				size = this.stars[0].width;
			} else {
				size = parseFloat(this.stars.eq(0).css('font-size'));
			}

			if (!size) {
				methods._error.call(this, 'Could not be possible get the icon size!');
			}

			return size;
		},

		_turnOn: function(i, score) {
			return this.opt.single ? (i === score) : (i <= score);
		},

		_getHint: function(score) {
			var hint = this.opt.hints[score - 1];

			return hint === '' ? '' : hint || score;
		},

		_lock: function() {
			var score = parseInt(this.score.val(), 10), // TODO: 3.1 >> [['1'], ['2'], ['3', '.1', '.2']]
				hint  = score ? methods._getHint.call(this, score) : this.opt.noRatedMsg;

			this.style.cursor   = '';
			this.title          = hint;

			this.score.prop('readonly', true);
			this.stars.prop('title', hint);

			if (this.cancel) {
				this.cancel.hide();
			}

			this.self.data('readonly', true);
		},

		_roundStars: function(score) {
			var rest = (score % 1).toFixed(2);

			if (rest > this.opt.round.down) {                      // Up:   [x.76 .. x.99]
				var icon = 'starOn';

				if (this.opt.halfShow && rest < this.opt.round.up) { // Half: [x.26 .. x.75]
					icon = 'starHalf';
				} else if (rest < this.opt.round.full) {             // Down: [x.00 .. x.5]
					icon = 'starOff';
				}

				var star = this.stars[Math.ceil(score) - 1];

				if (this.opt.starType === 'img') {
					star.src = this.opt.path + this.opt[icon];
				} else {
					star.style.className = this.opt[icon];
				}
			}                                                      // Full down: [x.00 .. x.25]
		},

		_target: function(score, evt) {
			if (this.opt.target) {
				var target = $(this.opt.target);

				if (!target.length) {
					methods._error.call(this, 'Target selector invalid or missing!');
				}

				var mouseover = evt && evt.type === 'mouseover';

				if (score === undefined) {
					score = this.opt.targetText;
				} else if (score === null) {
					score = mouseover ? this.opt.cancelHint : this.opt.targetText;
				} else {
					if (this.opt.targetType === 'hint') {
						score = methods._getHint.call(this, Math.ceil(score));
					} else if (this.opt.precision) {
						score = parseFloat(score).toFixed(1);
					}

					var mousemove = evt && evt.type === 'mousemove';

					if (!mouseover && !mousemove && !this.opt.targetKeep) {
						score = this.opt.targetText;
					}
				}

				if (score) {
					score = this.opt.targetFormat.toString().replace('{score}', score);
				}

				if (target.is(':input')) {
					target.val(score);
				} else {
					target.html(score);
				}
			}
		},

		_unlock: function() {
			this.style.cursor = 'pointer';
			this.removeAttribute('title');

			this.score.removeAttr('readonly');

			this.self.data('readonly', false);

			for (var i = 0; i < this.opt.number; i++) {
				this.stars[i].title = methods._getHint.call(this, i + 1);
			}

			if (this.cancel) {
				this.cancel.css('display', '');
			}
		},

		cancel: function(click) {
			return this.each(function() {
				var el = $(this);

				if (el.data('readonly') !== true) {
					methods[click ? 'click' : 'score'].call(el, null);

					this.score.removeAttr('value');
				}
			});
		},

		click: function(score) {
			return this.each(function() {
				if ($(this).data('readonly') !== true) {
					methods._apply.call(this, score);

					if (this.opt.click) {
						this.opt.click.call(this, score, $.Event('click'));
					}

					methods._target.call(this, score);
				}
			});
		},

		destroy: function() {
			return this.each(function() {
				var self = $(this),
					raw  = self.data('raw');

				if (raw) {
					self.off('.raty').empty().css({ cursor: raw.style.cursor }).removeData('readonly');
				} else {
					self.data('raw', self.clone()[0]);
				}
			});
		},

		getScore: function() {
			var score = [],
				value ;

			this.each(function() {
				value = this.score.val();

				score.push(value ? +value : undefined);
			});

			return (score.length > 1) ? score : score[0];
		},

		move: function(score) {
			return this.each(function() {
				var
					integer  = parseInt(score, 10),
					opt      = $(this).data('options'),
					decimal  = (+score).toFixed(1).split('.')[1];

				if (integer >= opt.number) {
					integer = opt.number - 1;
					decimal = 10;
				}

				var
					size    = methods._getSize.call(this),
					point   = size / 10,
					star    = $(this.stars[integer]),
					percent = star.offset().left + point * parseInt(decimal, 10),
					evt     = $.Event('mousemove', { pageX: percent });

				star.trigger(evt);
			});
		},

		readOnly: function(readonly) {
			return this.each(function() {
				var self = $(this);

				if (self.data('readonly') !== readonly) {
					if (readonly) {
						self.off('.raty').children('img').off('.raty');

						methods._lock.call(this);
					} else {
						methods._binds.call(this);
						methods._unlock.call(this);
					}

					self.data('readonly', readonly);
				}
			});
		},

		reload: function() {
			return methods.set.call(this, {});
		},

		score: function() {
			var self = $(this);

			return arguments.length ? methods.setScore.apply(self, arguments) : methods.getScore.call(self);
		},

		set: function(options) {
			return this.each(function() {
				var self   = $(this),
					actual = self.data('options'),
					news   = $.extend({}, actual, options);

				self.raty(news);
			});
		},

		setScore: function(score) {
			return this.each(function() {
				if ($(this).data('readonly') !== true) {
					methods._apply.call(this, score);
					methods._target.call(this, score);
				}
			});
		}
	};

	$.fn.raty = function(method) {
		if (methods[method]) {
			return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
		} else if (typeof method === 'object' || !method) {
			return methods.init.apply(this, arguments);
		} else {
			$.error('Method ' + method + ' does not exist!');
		}
	};

	$.fn.raty.defaults = {
		cancel       : false,
		cancelHint   : 'Cancel this rating!',
		cancelOff    : 'raty-cancel-off',
		cancelOn     : 'raty-cancel-on',
		cancelPlace  : 'left',
		click        : undefined,
		half         : false,
		halfShow     : true,
		hints        : ['1'],
		iconRange    : undefined,
		mouseout     : undefined,
		mouseover    : undefined,
		noRatedMsg   : 'Not rated yet!',
		number       : 5,
		numberMax    : 20,
		path         : undefined,
		precision    : false,
		readOnly     : false,
		round        : { down: 0.25, full: 0.6, up: 0.76 },
		score        : undefined,
		scoreName    : 'score',
		single       : false,
		space        : true,
		spaceWidth   : 3,
		starColor    : '#ff00aa',
		starHalf     : 'star-half.png',
		starOff      : 'raty-dot-off',
		starOn       : 'raty-dot-on',
		starType     : 'img',
		target       : undefined,
		targetFormat : '{score}',
		targetKeep   : false,
		targetScore  : undefined,
		targetText   : '',
		targetType   : 'hint'
	};

})(jQuery);


jQuery(function($){
	$('body').on('click', '.cf-toggle-group-buttons a', function(){

		var clicked = $(this),
			parent = clicked.closest('.caldera-config-field'),
			input = parent.find('[data-ref="'+clicked.attr('id')+'"]');


		parent.find('.btn').removeClass(clicked.data('active')).addClass(clicked.data('default'));
		clicked.addClass(clicked.data('active')).removeClass(clicked.data('default'));
		input.prop('checked', true).trigger('change');
	});
});

function toggle_button_init(id, el){

	var field 		= jQuery(el),
		checked		= field.find('.cf-toggle-group-radio:checked');

	if(checked.length){
		jQuery('#' + checked.data('ref') ).trigger('click');
	}

}

/**
 * Dynamic Field Configuration
 *
 * @since 1.5.0
 *
 * @param configs
 * @param $form
 * @param $ {jQuery}
 * @param state {CFState} @since 1.5.3
 *
 * @constructor
 */
 function Caldera_Forms_Field_Config( configs, $form, $, state ){
     var self = this;

     var fields = {};

     var formInstance = $form.data( 'instance' );

     var $submits = $form.find(':submit, .cf-page-btn-next' );

     /**
      * Start system
      *
      * @since 1.5.0
      */
     this.init = function(){
         $.each( configs, function( i, config ){
             fields[ config.id ] = self[config.type]( config );
         } );
     };

     /**
      * Validation handler for adding/removing errors for field types
      *
      * @since 1.5.0
      *
      * @param valid
      * @param $field
      * @param message
      * @param extraClass
      * @returns {boolean}
      */
     function handleValidationMarkup( valid, $field, message, extraClass ){
         var $parent = $field.parent().parent();
         $parent.removeClass( 'has-error' );
         $parent.find( '.help-block' ).remove();
         if( ! valid ){
             $parent.addClass( 'has-error' ).append( '<span id="cf-error-'+ $field.attr('id') +'" class="help-block ' + extraClass +'">' + message  + '</span>' );
             if ( $field.prop( 'required' ) ) {
                 disableAdvance();
             }
             $field.addClass( 'parsely-error' );
             return false;
         }else{
             $parent.removeClass( 'has-error' );
             allowAdvance();
             return true;
         }
     }

     /**
      * Utility method for preventing advance (next page/submit)
      *
      * @since 1.5.0
      */
     function disableAdvance(){
         $submits.prop( 'disabled',true).attr( 'aria-disabled', true  );
     }

     /**
      * Utility method for allowing advance (next page/submit)
      *
      * @since 1.5.0
      */
     function allowAdvance(){
         $submits.prop( 'disabled',false).attr( 'aria-disabled', false  );
     }

     function debounce(func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };


     /**
      * Handler for button fields
      *
      * @since 1.5.0
      *
      * @param field
      */
     this.button = function( field ){
         var field_id  = field.id;
         $(document).on('click dblclick', '#' + field_id, function( e ){
             $('#' + field_id + '_btn').val( e.type ).trigger('change');
         });
     };


     /**
      * Handler for HTML fields (and summary fields since this.summary is alias of this.html)
      *
      * @since 1.5.0
      *
      * @param fieldConfig
      */
     this.html = function ( fieldConfig ) {
         if( false == fieldConfig.sync ){
             return;
         }

		 var templates = {},
			 bindMap = fieldConfig.bindFields,
			 templateSystem,
			 $target = $( document.getElementById( fieldConfig.contentId ) ),
			 regex = {};
		 templateSystem = function () {

		     if( ! $target.length ){
                 $target = $( document.getElementById( fieldConfig.contentId ) );
             }

             if( ! $target.length ){
                 return;
             }

			 if (undefined == templates[fieldConfig.tmplId]) {
				 templates[fieldConfig.tmplId] = $(document.getElementById(fieldConfig.tmplId)).html();
			 }
			 var output = templates[fieldConfig.tmplId];

			 var value;
			 for (var i = 0; i <= bindMap.length; i++) {
			 	if( 'object' === typeof   bindMap[i] &&  bindMap[i].hasOwnProperty( 'to' ) && bindMap[i].hasOwnProperty( 'tag' )){


					value = state.getState(bindMap[i].to);
                    if( ! isNaN( value ) ){
                        value = value.toString();
                    } else if( 'string' === typeof  value ){
						value = value.replace(/(?:\r\n|\r|\n)/g, '<br />');
					}else  if( ! value || undefined == value.join || undefined === value || 'undefined' == typeof value){
						value = '';
					} else{
						value = value.join(', ');
					}
					output = output.replace( bindMap[i].tag, value );

				}


			 }

			 $target.html(output).trigger('change');
		 };

		 (function bind() {
			 for (var i = 0; i <= bindMap.length; i++) {
			 	if( 'object' === typeof  bindMap[i] && bindMap[i].hasOwnProperty( 'to' ) ){
					state.events().subscribe(bindMap[i].to, templateSystem);
				}
			 }
             $(document).on('cf.pagenav cf.modal', templateSystem );
		 }());

         templateSystem();
	 };

     /**
      * Handler to summary fields
      *
      * A copy of handler for HTML fields
      *
      * @since 1.5.0
      *
      * @type {any}
      */
     this.summary = this.html;

    var rangeSliders = {};

     /**
      * Handler for range slider fields
      *
      * @since 1.5.0
      *
      * @param field
      */
     this.range_slider = function( field ){
         var $el = $(document.getElementById(field.id));

         function setCss($el){
             $el.parent().find('.rangeslider').css('backgroundColor', field.trackcolor);
             $el.parent().find('.rangeslider__fill').css('backgroundColor', field.color);
             $el.parent().find('.rangeslider__handle').css('backgroundColor', field.handle).css('borderColor', field.handleborder);
         }

         function init() {


             if ('object' != rangeSliders[field.id]) {
                 rangeSliders[field.id] = {
                     value: field.default,
                     init: {},
					 inited : false
                 };
             }



             var init = {
				 onSlide: function (position, value) {
                     state.mutateState(field.id, value );
                     rangeSliders[field.id].value = value;
				 },
                 onInit: function () {
                     this.value = state.getState(field.id);
					 rangeSliders[field.id].inited = true;
                     setCss($el);
                 },
                 polyfill: false
             };

             rangeSliders[field.id].init = init;
             state.events().subscribe(field.id, function (value) {
                 $('#' + field.id + '_value').html(value);
             });

             if( ! $el.is( ':visible') ){
                 return;
             }

             $el.rangeslider(init);


         }






         $(document).on('cf.pagenav cf.add cf.disable cf.modal', function () {
             var el = document.getElementById(field.id);
             if (null != el) {

                 var $el = $(el),
                     val = rangeSliders[field.id].value;
                 if( ! $el.is( ':visible') ){
                     return;
                 }

                 $el.val( val );
				 $el.rangeslider('destroy');
				 $el.rangeslider(rangeSliders[field.id].init);
                 $el.val( val ).change();
                 setCss($el);

                 state.mutateState(field.id, val );
             }
         });

		 init();


     };

     /**
      * Handler for star ratings fields
      *
      * @since 1.5.0
      *
      * @param field
      */
     this.star_rating = function( field ){

         var score = field.options.score;
         var $el = $( document.getElementById( field.starFieldId ) );
         var $input = $( document.getElementById( field.id ) );
         var init =  function(){
             var options = field.options;

             options[ 'click' ] = function(){
                 score = $el.raty('score');
                 $el.trigger( 'change' );
             };
             $el.raty(
                 options
             );


             $el.raty('score', score );
         };

         init();
         var updating = false;
        jQuery( document ).on('cf.add', function(){

            if( false === updating ){
                updating = true;
                if( $el.length ){
                    $el.raty( 'destroy' );
                    init();
                }
                setTimeout(function(){
                    updating = false
                }, 500 );
            }



        } );
     };

     /**
      * Handler for new toggle swich fields
      *
      * @since 1.5.0
      *
      * @param field
      */
     this.toggle_switch = function( field ) {
         $( document ).on('reset', '#' + field.id, function(e){
             $.each( field.options, function( i, option ){
                 $( document.getElemenetById( option ) ).removeClass( field.selectedClassName ).addClass( field.defaultClassName );
             });
             $( document.getElementById( field.id )).prop('checked','');
         } );
     };

     /**
      * Handler for new phone fields
      *
      * @since 1.5.0
      *
      * @param field
      */
     this.phone_better = function( field ){

         var $field = $( document.getElementById( field.id ) );


         var reset = function(){
             var error = document.getElementById( 'cf-error-'+ field.id );
             if(  null != error ){
                 error.remove();
             }
         };

         var validation = function () {
             reset();
             var valid;
             if ($.trim($field.val())) {
                 if ($field.intlTelInput("isValidNumber")) {
                     valid = true;
                 } else {
                     valid = false;
                 }
             }

             var message;
             var errorCode = $field.intlTelInput("getValidationError");
             if (0 == errorCode) {
                 valid = true;
                 message = '';
             } else {
                 if ('undefined' != field.messages[errorCode]) {
                     message = field.messages[errorCode]
                 } else {
                     message = field.messages.generic;
                 }
             }


             handleValidationMarkup(valid, $field, message, 'help-block-phone_better');
             return valid;
         };

         var init = function() {
             if( ! $field.length ){
                 $field = $( document.getElementById( field.id ) );
             }

             $field.intlTelInput( field.options );
             $field.on( 'keyup change', reset );

             $field.blur(function() {
                 reset();
                 validation();
             });

             $field.on( 'change', validation );
             $form.on( 'submit', function(){
                 validation();
             })

         };

         $(document).on('cf.pagenav cf.add cf.disable cf.modal', init );

         init();



     };

     /**
      * Handler for WYSIWYG fields
      *
      * @since 1.5.0
      *
      * @param field
      */
     this.wysiwyg = function( field ){

         var actual_field = document.getElementById( field.id );
         if( null != actual_field ){
             var $field = $( actual_field );
             $field.trumbowyg(field.options);
             var $editor = $field.parent().find( '.trumbowyg-editor');

             $editor.html( $field.html() );
             $editor.bind('input propertychange', function(){
                 $field.html( $editor.html() );
             });
         }

     };

     /**
      * Handler for credit card fields
      *
      * @since 1.5.0
      *
      * @param fieldConfig
      */
     this.credit_card_number = function( fieldConfig ){
         var $field = $( document.getElementById( fieldConfig.id ) );

         if( false != fieldConfig.exp || false != fieldConfig.cvc ){
             setupLink();
         }

         if( $field.length ){
             $field.payment('formatCardNumber');
             $field.blur( function(){
                 var val =  $field.val();
                 var valid = $.payment.validateCardNumber( val );
                 var type = $.payment.cardType(val);
                 handleValidationMarkup( valid, $field, fieldConfig.invalid, 'help-block-credit_card_number help-block-credit_card' );
                 if( valid ){
                     setImage( type );
                 }
             })
         }

         /**
          * Link fields in credit card group
          *
          * @since 1.5.0
          *
          */
         function setupLink(){
             disableAdvance();
             var $cvcField = $( document.getElementById( fieldConfig.cvc ) ),
                 $expField = $( document.getElementById( fieldConfig.exp ) );
             $cvcField.blur( function(){
                 if ( $cvcField.val() ) {
                     self.creditCardUtil.validateCVC($field, $cvcField);
                 }
                 if ( $expField.val() ) {
                     self.creditCardUtil.validateExp($expField);
                 }
             });
         }

         /**
          * If possible change the icon in the credit card input
          *
          * @since 1.5.0
          *
          * @param type
          */
         function setImage( type ){
             var iconTypes = {
                 0: 'amex',
                 1: 'discover',
                 2: 'visa',
                 3: 'discover',
                 4: 'mastercard'
             };
             var icon = 'credit-card.svg';
             $.each( iconTypes, function( i, card ){
                if( 0 === type.indexOf( card ) ){
                    icon = 'cc-' + card + '.svg';
                    return false;
                }
             });

             $field.css( 'background', 'url("' + fieldConfig.imgPath + icon + '")' );
             
         }

     };

     /**
      * Handler for credit card expiration fields
      *
      * @since 1.5.0
      *
      * @param fieldConfig
      */
     this.credit_card_exp = function ( fieldConfig ) {
         var $field = $( document.getElementById( fieldConfig.id ) );
         if( $field.length ){
             $field.payment('formatCardExpiry');
             $field.blur( function () {
                 var valid = self.creditCardUtil.validateExp( $field );
                 handleValidationMarkup( valid, $field, fieldConfig.invalid, 'help-block-credit_card_exp help-block-credit_card' );
             });
         }
     };

     /**
      * Handler for credit card secret code fields
      *
      * @since 1.5.0
      *
      * @param fieldConfig
      */
     this.credit_card_cvc = function ( fieldConfig ) {
         var $field = $( document.getElementById( fieldConfig.id ) );
         if( $field.length ){
             $field.payment('formatCardCVC');
             if( false !== fieldConfig.ccField ) {
                 var $ccField = $( document.getElementById( fieldConfig.ccField ) );
                 $field.blur( function () {
                     var valid = self.creditCardUtil.validateExp( $ccField, $field);
                     handleValidationMarkup(valid, $field, fieldConfig.invalid, 'help-block-credit_card_cvc help-block-credit_card');
                 });
             }

         }
     };

     /**
      * Validators for credit card CVC and expirations
      *
      * @since 1.5.0
      *
      * @type {{validateCVC: Caldera_Forms_Field_Config.creditCardUtil.validateCVC, validateExp: Caldera_Forms_Field_Config.creditCardUtil.validateExp}}
      */
     this.creditCardUtil = {
         validateCVC: function( $ccField, $cvcField ){
             var val =  $cvcField.val();
             var cardValid = $.payment.validateCardNumber( $ccField.val() );
             var valid = false;
             if ( cardValid ) {
                 var type = $.payment.cardType( $ccField.val() );
                 valid = $.payment.validateCardCVC( val, type)
             }

             return valid;
         },
         validateExp: function ($expField) {
             var val = $expField.val().split('/');
             if (  val && 2 == val.length ) {
                 return $.payment.validateCardExpiry(val[0].trim(), val[1].trim());
             }
         }

     };
     
     this.color_picker = function ( fieldConfig ) {
         $( document.getElementById( fieldConfig.id ) ).miniColors( fieldConfig.settings );
         $(document).on('cf.pagenav cf.add cf.disable cf.modal', function () {
             $(document.getElementById(fieldConfig.id)).miniColors(fieldConfig.settings);
         });
     };

	/**
	 * Process a calculation field
	 *
	 * @since 1.5.6
	 *
	 * @param fieldConfig
	 */
	this.calculation = function (fieldConfig) {
		var lastValue = null,
			/**
			 * Debounced version of the run() function below
			 *
			 * @since 1.5.6
			 */
            debouncedRunner = debounce(
                function(){
                    run(state)
                }, 250
            );

		/**
		 * Adds commas or whatever to the display fo value
		 *
		 * @since 1.5.6
		 *
		 * @param {string} nStr
		 * @returns {string}
		 */
		function addCommas(nStr){
			nStr += '';
			var x = nStr.split('.'),
				x1 = x[0],
				x2 = x.length > 1 ? fieldConfig.decimalSeparator + x[1] : '',
				rgx = /(\d+)(\d{3})/;
			while (rgx.test(x1)) {

				x1 = x1.replace(rgx, '$1' + fieldConfig.thousandSeparator + '$2');
			}
			return x1 + x2;
		}


		/**
         * Function that triggers calcualtion and updates state/DOM if it changed
         * NOTE: Don't use directly, use debounced version
         *
         * @since 1.5.6
         */
        var run = function(){
			var result = window[fieldConfig.callback].apply(null, [state] );
			if( ! isFinite( result ) ){
				result = 0;
			}

			if ( null === lastValue || result !== lastValue ) {
				lastValue = result;
				state.mutateState( fieldConfig.id, result );
                if( 'number' != typeof  result ){
                    result = parseInt( result, 10 );
                }

                if( fieldConfig.moneyFormat ){
                    result = result.toFixed(2);
                }

				$( '#' + fieldConfig.id ).html( addCommas( result ) ).data( 'calc-value', result );
				$('#' + fieldConfig.targetId ).val( result ).trigger( 'change' );
			}
		};

		//Update when any field that is part of the formula changes
		$.each( fieldConfig.fieldBinds,  function (feild,feildId) {
			state.events().subscribe( feildId, debouncedRunner );
		});

		//Run on CF page change, field added, field removed or modal opened.
		$(document).on('cf.pagenav cf.add cf.remove cf.modal', function (e,obj) {
		    if( 'cf' == e.type && 'remove' === e.namespace && 'object' === typeof  obj && obj.hasOwnProperty('field' ) && obj.field === fieldConfig.id ){
		    	//If calculation field is removed, make sure if it comes back, an update to DOM/state will be triggered.
				lastValue = null;
            }else{
            	//If trigger wasn't being removed, run.
                debouncedRunner();

            }
		});

		debouncedRunner();

	}


 }



var cf_jsfields_init, cf_presubmit;
(function($){

	// validation
	cf_validate_form = function( form ){
		return form.parsley({
			errorsWrapper : '<span class="help-block caldera_ajax_error_block"></span>',
			errorTemplate : '<span></span>',
			errorsContainer : function( field ){
				return field.$element.closest('.form-group');
			}
		}).on('field:error', function( fieldInstance ) {

            if ( 'number' == this.$element.attr( 'type' ) && 0 == this.$element.attr( 'min' )  ) {
                var val = this.$element.val();
                if( 0 <= val && ( undefined == this.$element.attr( 'max' ) || val <= this.$element.attr( 'max' )  ) ){
                    fieldInstance.validationResult = true;
                }

                return;
            }

            this.$element.closest('.form-group').addClass('has-error');
			$( document ).trigger( 'cf.validate.fieldError', {
				inst: fieldInstance,
				form: form,
				el: this.$element
			} );
        }).on('field:success', function( fieldInstance ) {
        	if( 'star' === this.$element.data( 'type' ) && this.$element.prop('required') && 0 == this.$element.val() ){
				fieldInstance.validationResult = false;
				return;
			}
			this.$element.closest('.form-group').removeClass('has-error');
			$( document ).trigger( 'cf.validate.fieldSuccess', {
				inst: fieldInstance,
				form: form,
				el: this.$element
			} );
		}).on('form:success', function ( formInstance ) {
			$( document ).trigger( 'cf.validate.FormSuccess', {
				inst: formInstance,
				form: form,
				el: this.$element
			} );
		}).on( 'form:error', function ( formInstance ) {
			$( document ).trigger( 'cf.validate.FormError', {
				inst: formInstance,
				form: form,
				el: this.$element
			} );
		})
	};

	$( document ).on('change keypress', "[data-sync]", function(){
		$(this).data( 'unsync', true );
	});

	// make init function
	cf_jsfields_init = function(){
		$('.init_field_type[data-type]').each(function(k,v){
			var ftype = $(v);
			if( typeof window[ftype.data('type') + '_init'] === 'function' ){
				window[ftype.data('type') + '_init'](ftype.prop('id'), ftype[0]);
			}
		});

		window.Parsley.on('field:validated', function() {
			setTimeout( function(){$(document).trigger('cf.error');}, 15 );
		});
		if( typeof resBaldrickTriggers === 'undefined' && $('.caldera_forms_form').length ){

		}

		$( document ).trigger( 'cf.fieldsInit' );

		function setLocale( locale ){
			if ('undefined' != typeof window.Parsley._validatorRegistry.catalog[locale] ){
				window.Parsley.setLocale( locale );
			}

		}

	};

	$('document').ready(function(){
		// check for init function
		cf_jsfields_init();
	});

	// if pages, disable enter
	if( $('.caldera-form-page').length ){
		$('.caldera-form-page').on('keypress', '[data-field]:not(textarea)', function( e ){
			if( e.keyCode === 13 ){
				e.preventDefault();
			}
		});
	}
	// modals activation
	$(document).on('click', '.cf_modal_button', function(e){
		e.preventDefault();
		var clicked = $(this);
		$(clicked.attr('href')).show();
	});
	$(document).on('click', '.caldera-front-modal-closer', function(e){
		e.preventDefault();
		var clicked = $(this);
			clicked.closest('.caldera-front-modal-container').hide();
	});
	// stuff trigger
	$(document).on('cf.add cf.enable cf.disable cf.pagenav', cf_jsfields_init );

	// Page navigation
	$(document).on('click', '[data-page]', function(e){

		var clicked = $(this),
			page_box = clicked.closest('.caldera-form-page'),
			form 	 = clicked.closest('form.caldera_forms_form'),
			form_id = form.attr( 'id' ),
			instance = form.data('instance'),
			current_page = form.find('.caldera-form-page:visible').data('formpage'),
			page	 = page_box.data('formpage') ? page_box.data('formpage') : clicked.data('page') ,
			breadcrumb = $('.breadcrumb[data-form="caldera_form_' + instance + '"]'),
			next,
			prev,
			fields,
			run = true,
			focusPage = current_page;

		if( !form.length ){
			return;
		}

		cf_validate_form( form ).destroy();

		fields = form.find('[data-field]');
		form.find('.has-error').removeClass('has-error');

		if( clicked.data('page') !== 'prev' && page >= current_page ){
			fields =  $('#caldera_form_' + instance + ' [data-formpage="' + current_page + '"] [data-field]'  );

			var this_field,
				valid,
				_valid;
			for (var f = 0; f < fields.length; f++) {
				this_field = $(fields[f]);
				_valid = this_field.parsley().validate();
				valid = this_field.parsley().isValid({force: true});

				//@see https://github.com/CalderaWP/Caldera-Forms/issues/1765
				if( ! valid && true === _valid && 'email' === this_field.attr( 'type' ) ){
					continue;
				}

				if (true === valid) {
					continue;
				}

				e.preventDefault();
				run = false;

			}

			if( true === run && page > current_page ){
				for( var i = page - 1; i >= 1; i -- ){
					fields =  $('#caldera_form_' + instance + ' [data-formpage="' + i + '"] [data-field]'  );

					for (var f = 0; f < fields.length; f++) {
						this_field = $(fields[f]);
						this_field.parsley().validate();
						valid = this_field.parsley().isValid({force: true});
						if (true === valid) {
							continue;
						}

						e.preventDefault();
						run = false;
						if( i > focusPage ){
							focusPage = i;
						}

					}
				}

			}


		}




		if( false === run ){
			if( focusPage !== current_page ){
				$( '#form_page_' + instance + '_pg_' + current_page ).hide().attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' );
				$( '#form_page_' + instance + '_pg_' + focusPage ).show().attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' );
			}
			cf_validate_form( form ).validate();
			return false;
		}

		if( clicked.data('page') === 'next'){

			if(breadcrumb){
				breadcrumb.find('li.active').removeClass('active').children().attr('aria-expanded', 'false');
			}
			next = form.find('.caldera-form-page[data-formpage="'+ ( page + 1 ) +'"]');
			if(next.length){
				page_box.hide().attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' );
				next.show().attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' );
				if(breadcrumb){
					breadcrumb.find('a[data-page="'+ ( page + 1 ) +'"]').attr('aria-expanded', 'true').parent().addClass('active');
				}
			}
		}else if(clicked.data('page') === 'prev'){
			if(breadcrumb){
				breadcrumb.find('li.active').removeClass('active').children().attr('aria-expanded', 'false');
			}
			prev = form.find('.caldera-form-page[data-formpage="'+ ( page - 1 ) +'"]');
			if(prev.length){
				page_box.hide().attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' );
				prev.show().attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' );
				if(breadcrumb){
					breadcrumb.find('a[data-page="'+ ( page - 1 ) +'"]').attr('aria-expanded', 'true').parent().addClass('active');
				}
			}
		}else{
			if(clicked.data('pagenav')){
				e.preventDefault();
				clicked.closest('.breadcrumb').find('li.active').removeClass('active').children().attr('aria-expanded', 'false');
				$('#' + clicked.data('pagenav') + ' .caldera-form-page').hide().attr( 'aria-hidden', 'true' ).css( 'visibility', 'hidden' );
				$('#' + clicked.data('pagenav') + '	.caldera-form-page[data-formpage="'+ ( clicked.data('page') ) +'"]').show().attr( 'aria-hidden', 'false' ).css( 'visibility', 'visible' );
				clicked.parent().addClass('active').children().attr('aria-expanded', 'true');
			}

		}
		$('html, body').animate({
			scrollTop: form.offset().top - 100
		}, 200);

		$(document).trigger('cf.pagenav');

	});

	// init page errors
	var tab_navclick;
	$('.caldera-grid .breadcrumb').each(function(k,v){
		$(v).find('a[data-pagenav]').each(function(i,e){
			var tab		= $(e),
				form 	= tab.data('pagenav'),
				page	= $('#'+ form +' .caldera-form-page[data-formpage="' + tab.data('page') + '"]');

			if(page.find('.has-error').length){
				tab.parent().addClass('error');
				if(typeof tab_navclick === 'undefined'){
					tab.trigger('click');
					tab_navclick = true;
				}

			}

		});
	});
	// trigger last page

	// validator
	$( document ).on('click', 'form.caldera_forms_form [type="submit"]', function( e ){
		var $clicked = $( this ),
			$form = $clicked.closest('.caldera_forms_form'),
			validator = cf_validate_form( $form );
		$( document ).trigger( 'cf.form.submit', {
			e:e,
			$form:$form
		} );



		if( ! validator.validate() ){
			if( $('.caldera-form-page').length ) {
				var currentPage = $clicked.parents('.caldera-form-page').data('formpage');

				var invalids = [],
					future = [];
				validator.fields.forEach(function (field, i) {
					if( true === field.validationResult ){
						return;
					}
					var $pageParent = field.$element.parents('.caldera-form-page');
					if (undefined != $pageParent && $pageParent.length && field.$element.parents('.caldera-form-page').data('formpage') > currentPage) {
						future.push(field.$element.data(  'field' ) );
						return;
					}

					invalids.push( field );
				});
				if( ! invalids.length ){
					if( future.length ){
						$form.append( '<input type="hidden" name="_cf_future" value="' + future.toString() + '">' );

					}


					validator.destroy();
					return;

				}

			}

			e.preventDefault();
		}else{
			$( document ).trigger( 'cf.form.validated', {
				e:e,
				$form:$form
			} );
			validator.destroy();
		}
	});




})(jQuery);

/** Setup Form Front-end **/
window.addEventListener("load", function(){
	(function( $ ) {
		'use strict';

		window.CALDERA_FORMS = {};

		/** Check nonce **/
		if( 'object' === typeof CF_API_DATA ) {
			var nonceCheckers = {},
				$el, formId;
			$('.caldera_forms_form').each(function (i, el) {
				$el = $(el);
				formId = $el.data( 'form-id' );
				nonceCheckers[ formId ] = new CalderaFormsResetNonce( formId, CF_API_DATA, $ );
				nonceCheckers[ formId ].init();
			});
		}

		/** Setup forms */
		if( 'object' === typeof CFFIELD_CONFIG ) {
			var form_id, config_object, config, instance, $el, state, protocolCheck, jQueryCheck, $form,
				jQueryChecked = false,
				protocolChecked = false;
			$('.caldera_forms_form').each(function (i, el) {
				$el = $(el);

				form_id = $el.attr('id');
				instance = $el.data('instance');

				if ('object' === typeof CFFIELD_CONFIG[instance] ) {
					$form = $( document.getElementById( form_id ));

					if ( ! protocolChecked ) {
						//check for protocol mis-match on submit url
						protocolCheck = new CalderaFormsCrossOriginWarning($el, $, CFFIELD_CONFIG[instance].error_strings);
						protocolCheck.maybeWarn();

						//don't check twice
						protocolChecked = true;
					}

					if ( ! jQueryChecked &&  CFFIELD_CONFIG[instance].error_strings.hasOwnProperty( 'jquery_old' ) ) {
						//check for old jQuery
						jQueryCheck = new CalderaFormsJQueryWarning($el, $, CFFIELD_CONFIG[instance].error_strings);
						jQueryCheck.maybeWarn();

						//don't check twice
						jQueryChecked = true;
					}

					formId = $el.data( 'form-id' );
					config = CFFIELD_CONFIG[instance].configs;

					var state = new CFState(formId, $ );
					state.init( CFFIELD_CONFIG[instance].fields.defaults,CFFIELD_CONFIG[instance].fields.calcDefaults );

					if( 'object' !== typeof window.cfstate ){
						window.cfstate = {};
					}

					window.cfstate[ form_id ] = state;

					$form.find( '[data-sync]' ).each( function(){
						var $field = $( this );
						new CalderaFormsFieldSync( $field, $field.data('binds'), $form, $ , state);
					});

					
					config_object = new Caldera_Forms_Field_Config( config, $(document.getElementById(form_id)), $, state );
					config_object.init();
					$( document ).trigger( 'cf.form.init',{
						idAttr:  form_id,
						formId: formId,
						state: state,
						fieldIds: CFFIELD_CONFIG[instance].fields.hasOwnProperty( 'ids' ) ? CFFIELD_CONFIG[instance].fields.ids : []
					});



				}
			});

		}





	})( jQuery );


});


/**
 * Sets up field synce
 *
 * @since 1.5.0
 *
 * @param $field jQuery object for field
 * @param binds Field IDs to bind to
 * @param $form jQuery object for form
 * @param $ jQuery
 * @param {CFState} state
 * @constructor
 */
function CalderaFormsFieldSync( $field, binds, $form, $, state  ){
	for( var i = 0; i < binds.length; i++ ){

		$( document ).on('keyup change blur mouseover', "[data-field='" + binds[ i ] + "']", function(){
			var str = $field.data('sync')
			id = $field.data('field'),
				reg = new RegExp( "\{\{([^\}]*?)\}\}", "g" ),
				template = str.match( reg );
			if( $field.data( 'unsync' ) || undefined == template || ! template.length ){
				return;
			}

			for( var t = 0; t < template.length; t++ ){
				var select = template[ t ].replace(/\}/g,'').replace(/\{/g,'');
				var re = new RegExp( template[ t ] ,"g");
				var sync = $form.find( "[data-field='" + select + "']" );
				var val = '';
				for( var i =0; i < sync.length; i++ ){
					var this_field = $( sync[i] );
					if( ( this_field.is(':radio') || this_field.is(':checkbox') ) && ! this_field.is(':checked') ){
						// skip.
					}else{
						val += this_field.val();
					}

				}
				str = str.replace( re , val );
			}
			state.mutateState( $field.attr( 'id' ), val );
			$field.val( str );
		} );
		$("[data-field='" + binds[ i ] + "']").trigger('change');

	}
}

/**
 * Handles nonce refresh for forms
 *
 * @since 1.5.0
 *
 * @param formId ID of form
 * @param config API/nonce config (Probably the CF_API_DATA CDATA)
 * @param $ jQuery
 * @constructor
 */
function CalderaFormsResetNonce( formId, config, $ ){

	var $nonceField;

	/**
	 * Run system, replace nonce if needed
	 *
	 * @since 1.5.0
     */
	this.init = function(){
		$nonceField = $( '#' + config.nonce.field + '_' + formId );
		if( isNonceOld( $nonceField.data( 'nonce-time' ) ) ){
			replaceNonce();
		}
	};

	/**
	 * Check if nonce is more than an hour old
	 *
	 * If not, not worth the HTTP request
	 *
	 * @since 1.5.0
	 *
	 * @param time Time nonce was generated
	 * @returns {boolean}
     */
	function isNonceOld( time ){
		var now = new Date().getTime();
		if( now - 36000 > time ){
			return true;
		}
		return false;
	}

	/**
	 * Replace nonce via AJAX
	 *
	 * @since 1.5.0
     */
	function replaceNonce(){
		$.ajax({
			url:config.rest.tokens.nonce,
			method: 'POST',
			beforeSend: function ( xhr ) {
				xhr.setRequestHeader( 'X-WP-Nonce', config.rest.nonce );
			},data:{
				form_id: formId
			}
		}).success( function( r){
			$nonceField.val( r.nonce );
			$nonceField.data( 'nonce-time', new Date().getTime() );
		});
	}
}

/**
 * Check if URL is same protocol as same page
 *
 * @since 1.5.3
 *
 * @param url {String} Url to compare against
 *
 * @returns {boolean} True if same protocol, false if not
 */
function caldera_forms_check_protocol( url ){
	var pageProtocol = window.location.protocol;
	var parser = document.createElement('a');
	parser.href = url;
	return parser.protocol === pageProtocol;

}

/**
 * Add a warning about cross-origin requests
 *
 * @since 1.5.3
 *
 * @param $form {jQuery} Form element
 * @param $ {jQuery}
 * @param errorStrings {Object} Localized error strings for this form
 * @constructor
 */
function CalderaFormsCrossOriginWarning( $form, $, errorStrings ){

	/**
	 * Do the check and warn if needed
	 *
	 * @since 1.5.3
	 */
	this.maybeWarn = function () {
		if( $form.find( '[name="cfajax"]').length ){
			var url = $form.data( 'request' );
			if( ! caldera_forms_check_protocol( url ) ){
				showNotice();
			}

		}

	};

	/**
	 * Append notice
	 *
	 * @since 1.5.3
	 */
	function showNotice() {
		var $target = $( $form.data( 'target' ) );
		$target.append( '<div class="alert alert-warning">' + errorStrings.mixed_protocol + '</div>' );
	}
}

/**
 * Add a warning about bad jQuery versions
 *
 * @since 1.5.3
 *
 * @param $form {jQuery} Form element
 * @param $ {jQuery}
 * @param errorStrings {Object} Localized error strings for this form
 * @constructor
 */
function CalderaFormsJQueryWarning( $form, $, errorStrings ){

	/**
	 * Do the check and warn if needed
	 *
	 * @since 1.5.3
	 */
	this.maybeWarn = function () {
		var version =  $.fn.jquery;
		if(  'string' === typeof  version && '1.12.4' != version ) {
			if( isOld( version ) ){
				showNotice();
			}
		}

	};

	/**
	 * Append notice
	 *
	 * @since 1.5.3
	 */
	function showNotice() {
		var $target = $( $form.data( 'target' ) );
		$target.append( '<div class="alert alert-warning">' + errorStrings.jquery_old + '</div>' );
	}

	/**
	 * Check if version is older than 1.12.4
	 *
	 * @since 1.5.3
	 *
	 * @param version
	 * @returns {boolean}
	 */
	function isOld(version) {
		var split = version.split( '.' );
		if( 1 == split[0] ){
			if( 12 > split[2] ){
				return true;
			}

			if( 4 > split[2]){
				return true;
			}

		}

		return false;

	}
}