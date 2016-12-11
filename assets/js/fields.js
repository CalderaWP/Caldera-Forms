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
!function($) {
	function isInputEventSupported(eventName) {
		var el = document.createElement("input"), evName = "on" + eventName, isSupported = evName in el;
		return isSupported || (el.setAttribute(evName, "return;"), isSupported = "function" == typeof el[evName]),
			el = null, isSupported;
	}
	function isInputTypeSupported(inputType) {
		var isSupported = "text" == inputType || "tel" == inputType;
		if (!isSupported) {
			var el = document.createElement("input");
			el.setAttribute("type", inputType), isSupported = "text" === el.type, el = null;
		}
		return isSupported;
	}
	function resolveAlias(aliasStr, options, opts) {
		var aliasDefinition = opts.aliases[aliasStr];
		return aliasDefinition ? (aliasDefinition.alias && resolveAlias(aliasDefinition.alias, void 0, opts),
			$.extend(!0, opts, aliasDefinition), $.extend(!0, opts, options), !0) : !1;
	}
	function generateMaskSet(opts, multi) {
		function analyseMask(mask) {
			function maskToken(isGroup, isOptional, isQuantifier, isAlternator) {
				this.matches = [], this.isGroup = isGroup || !1, this.isOptional = isOptional || !1,
					this.isQuantifier = isQuantifier || !1, this.isAlternator = isAlternator || !1,
					this.quantifier = {
						min: 1,
						max: 1
					};
			}
			function insertTestDefinition(mtoken, element, position) {
				var maskdef = opts.definitions[element], newBlockMarker = 0 == mtoken.matches.length;
				if (position = void 0 != position ? position : mtoken.matches.length, maskdef && !escaped) {
					for (var precheck = maskdef.prevalidator, precheckL = precheck ? precheck.length : 0, i = 1; i < maskdef.cardinality; i++) {
						var prevalidator = precheckL >= i ? precheck[i - 1] : [], validator = prevalidator.validator, cardinality = prevalidator.cardinality;
						mtoken.matches.splice(position++, 0, {
							fn: validator ? "string" == typeof validator ? new RegExp(validator) : new function() {
								this.test = validator;
							}() : new RegExp("."),
							cardinality: cardinality ? cardinality : 1,
							optionality: mtoken.isOptional,
							newBlockMarker: newBlockMarker,
							casing: maskdef.casing,
							def: maskdef.definitionSymbol || element,
							placeholder: maskdef.placeholder,
							mask: element
						});
					}
					mtoken.matches.splice(position++, 0, {
						fn: maskdef.validator ? "string" == typeof maskdef.validator ? new RegExp(maskdef.validator) : new function() {
							this.test = maskdef.validator;
						}() : new RegExp("."),
						cardinality: maskdef.cardinality,
						optionality: mtoken.isOptional,
						newBlockMarker: newBlockMarker,
						casing: maskdef.casing,
						def: maskdef.definitionSymbol || element,
						placeholder: maskdef.placeholder,
						mask: element
					});
				} else mtoken.matches.splice(position++, 0, {
					fn: null,
					cardinality: 0,
					optionality: mtoken.isOptional,
					newBlockMarker: newBlockMarker,
					casing: null,
					def: element,
					placeholder: void 0,
					mask: element
				}), escaped = !1;
			}
			for (var match, m, openingToken, currentOpeningToken, alternator, lastMatch, tokenizer = /(?:[?*+]|\{[0-9\+\*]+(?:,[0-9\+\*]*)?\})\??|[^.?*+^${[]()|\\]+|./g, escaped = !1, currentToken = new maskToken(), openenings = [], maskTokens = []; match = tokenizer.exec(mask); ) switch (m = match[0],
				m.charAt(0)) {
				case opts.optionalmarker.end:
				case opts.groupmarker.end:
					if (openingToken = openenings.pop(), openenings.length > 0) {
						if (currentOpeningToken = openenings[openenings.length - 1], currentOpeningToken.matches.push(openingToken),
								currentOpeningToken.isAlternator) {
							alternator = openenings.pop();
							for (var mndx = 0; mndx < alternator.matches.length; mndx++) alternator.matches[mndx].isGroup = !1;
							openenings.length > 0 ? (currentOpeningToken = openenings[openenings.length - 1],
								currentOpeningToken.matches.push(alternator)) : currentToken.matches.push(alternator);
						}
					} else currentToken.matches.push(openingToken);
					break;

				case opts.optionalmarker.start:
					openenings.push(new maskToken(!1, !0));
					break;

				case opts.groupmarker.start:
					openenings.push(new maskToken(!0));
					break;

				case opts.quantifiermarker.start:
					var quantifier = new maskToken(!1, !1, !0);
					m = m.replace(/[{}]/g, "");
					var mq = m.split(","), mq0 = isNaN(mq[0]) ? mq[0] : parseInt(mq[0]), mq1 = 1 == mq.length ? mq0 : isNaN(mq[1]) ? mq[1] : parseInt(mq[1]);
					if (("*" == mq1 || "+" == mq1) && (mq0 = "*" == mq1 ? 0 : 1), quantifier.quantifier = {
							min: mq0,
							max: mq1
						}, openenings.length > 0) {
						var matches = openenings[openenings.length - 1].matches;
						if (match = matches.pop(), !match.isGroup) {
							var groupToken = new maskToken(!0);
							groupToken.matches.push(match), match = groupToken;
						}
						matches.push(match), matches.push(quantifier);
					} else {
						if (match = currentToken.matches.pop(), !match.isGroup) {
							var groupToken = new maskToken(!0);
							groupToken.matches.push(match), match = groupToken;
						}
						currentToken.matches.push(match), currentToken.matches.push(quantifier);
					}
					break;

				case opts.escapeChar:
					escaped = !0;
					break;

				case opts.alternatormarker:
					openenings.length > 0 ? (currentOpeningToken = openenings[openenings.length - 1],
						lastMatch = currentOpeningToken.matches.pop()) : lastMatch = currentToken.matches.pop(),
						lastMatch.isAlternator ? openenings.push(lastMatch) : (alternator = new maskToken(!1, !1, !1, !0),
							alternator.matches.push(lastMatch), openenings.push(alternator));
					break;

				default:
					if (openenings.length > 0) {
						if (currentOpeningToken = openenings[openenings.length - 1], currentOpeningToken.matches.length > 0 && (lastMatch = currentOpeningToken.matches[currentOpeningToken.matches.length - 1],
							lastMatch.isGroup && (lastMatch.isGroup = !1, insertTestDefinition(lastMatch, opts.groupmarker.start, 0),
								insertTestDefinition(lastMatch, opts.groupmarker.end))), insertTestDefinition(currentOpeningToken, m),
								currentOpeningToken.isAlternator) {
							alternator = openenings.pop();
							for (var mndx = 0; mndx < alternator.matches.length; mndx++) alternator.matches[mndx].isGroup = !1;
							openenings.length > 0 ? (currentOpeningToken = openenings[openenings.length - 1],
								currentOpeningToken.matches.push(alternator)) : currentToken.matches.push(alternator);
						}
					} else currentToken.matches.length > 0 && (lastMatch = currentToken.matches[currentToken.matches.length - 1],
					lastMatch.isGroup && (lastMatch.isGroup = !1, insertTestDefinition(lastMatch, opts.groupmarker.start, 0),
						insertTestDefinition(lastMatch, opts.groupmarker.end))), insertTestDefinition(currentToken, m);
			}
			return currentToken.matches.length > 0 && (lastMatch = currentToken.matches[currentToken.matches.length - 1],
			lastMatch.isGroup && (lastMatch.isGroup = !1, insertTestDefinition(lastMatch, opts.groupmarker.start, 0),
				insertTestDefinition(lastMatch, opts.groupmarker.end)), maskTokens.push(currentToken)),
				maskTokens;
		}
		function generateMask(mask, metadata) {
			if (opts.numericInput && opts.multi !== !0) {
				mask = mask.split("").reverse();
				for (var ndx = 0; ndx < mask.length; ndx++) mask[ndx] == opts.optionalmarker.start ? mask[ndx] = opts.optionalmarker.end : mask[ndx] == opts.optionalmarker.end ? mask[ndx] = opts.optionalmarker.start : mask[ndx] == opts.groupmarker.start ? mask[ndx] = opts.groupmarker.end : mask[ndx] == opts.groupmarker.end && (mask[ndx] = opts.groupmarker.start);
				mask = mask.join("");
			}
			if (void 0 == mask || "" == mask) return void 0;
			if (1 == mask.length && 0 == opts.greedy && 0 != opts.repeat && (opts.placeholder = ""),
				opts.repeat > 0 || "*" == opts.repeat || "+" == opts.repeat) {
				var repeatStart = "*" == opts.repeat ? 0 : "+" == opts.repeat ? 1 : opts.repeat;
				mask = opts.groupmarker.start + mask + opts.groupmarker.end + opts.quantifiermarker.start + repeatStart + "," + opts.repeat + opts.quantifiermarker.end;
			}
			return void 0 == $.inputmask.masksCache[mask] && ($.inputmask.masksCache[mask] = {
				mask: mask,
				maskToken: analyseMask(mask),
				validPositions: {},
				_buffer: void 0,
				buffer: void 0,
				tests: {},
				metadata: metadata
			}), $.extend(!0, {}, $.inputmask.masksCache[mask]);
		}
		var ms = void 0;
		if ($.isFunction(opts.mask) && (opts.mask = opts.mask.call(this, opts)), $.isArray(opts.mask)) if (multi) ms = [],
			$.each(opts.mask, function(ndx, msk) {
				ms.push(void 0 == msk.mask || $.isFunction(msk.mask) ? generateMask(msk.toString(), msk) : generateMask(msk.mask.toString(), msk));
			}); else {
			opts.keepStatic = void 0 == opts.keepStatic ? !0 : opts.keepStatic;
			var altMask = "(";
			$.each(opts.mask, function(ndx, msk) {
				altMask.length > 1 && (altMask += ")|("), altMask += void 0 == msk.mask || $.isFunction(msk.mask) ? msk.toString() : msk.mask.toString();
			}), altMask += ")", ms = generateMask(altMask, opts.mask);
		} else opts.mask && (ms = void 0 == opts.mask.mask || $.isFunction(opts.mask.mask) ? generateMask(opts.mask.toString(), opts.mask) : generateMask(opts.mask.mask.toString(), opts.mask));
		return ms;
	}
	function maskScope(actionObj, maskset, opts) {
		function getMaskTemplate(baseOnInput, minimalPos, includeInput) {
			minimalPos = minimalPos || 0;
			var ndxIntlzr, test, testPos, maskTemplate = [], pos = 0;
			do {
				if (baseOnInput === !0 && getMaskSet().validPositions[pos]) {
					var validPos = getMaskSet().validPositions[pos];
					test = validPos.match, ndxIntlzr = validPos.locator.slice(), maskTemplate.push(includeInput === !0 ? validPos.input : getPlaceholder(pos, test));
				} else {
					if (minimalPos > pos) {
						var testPositions = getTests(pos, ndxIntlzr, pos - 1);
						testPos = testPositions[0];
					} else testPos = getTestTemplate(pos, ndxIntlzr, pos - 1);
					test = testPos.match, ndxIntlzr = testPos.locator.slice(), maskTemplate.push(getPlaceholder(pos, test));
				}
				pos++;
			} while ((void 0 == maxLength || maxLength > pos - 1) && null != test.fn || null == test.fn && "" != test.def || minimalPos >= pos);
			return maskTemplate.pop(), maskTemplate;
		}
		function getMaskSet() {
			return maskset;
		}
		function resetMaskSet(soft) {
			var maskset = getMaskSet();
			maskset.buffer = void 0, maskset.tests = {}, soft !== !0 && (maskset._buffer = void 0,
				maskset.validPositions = {}, maskset.p = 0);
		}
		function getLastValidPosition(closestTo) {
			var maskset = getMaskSet(), lastValidPosition = -1, valids = maskset.validPositions;
			void 0 == closestTo && (closestTo = -1);
			var before = lastValidPosition, after = lastValidPosition;
			for (var posNdx in valids) {
				var psNdx = parseInt(posNdx);
				(-1 == closestTo || null != valids[psNdx].match.fn) && (closestTo > psNdx && (before = psNdx),
				psNdx >= closestTo && (after = psNdx));
			}
			return lastValidPosition = closestTo - before > 1 || closestTo > after ? before : after;
		}
		function setValidPosition(pos, validTest, fromSetValid) {
			if (opts.insertMode && void 0 != getMaskSet().validPositions[pos] && void 0 == fromSetValid) {
				var i, positionsClone = $.extend(!0, {}, getMaskSet().validPositions), lvp = getLastValidPosition();
				for (i = pos; lvp >= i; i++) delete getMaskSet().validPositions[i];
				getMaskSet().validPositions[pos] = validTest;
				var j, valid = !0;
				for (i = pos; lvp >= i; i++) {
					var t = positionsClone[i];
					if (void 0 != t) {
						var vps = getMaskSet().validPositions;
						j = !opts.keepStatic && (void 0 != vps[i + 1] && getTests(i + 1, vps[i].locator.slice(), i).length > 1 || vps[i] && void 0 != vps[i].alternation) ? i + 1 : seekNext(i),
							valid = positionCanMatchDefinition(j, t.match.def) ? valid && isValid(j, t.input, !0, !0) !== !1 : null == t.match.fn;
					}
					if (!valid) break;
				}
				if (!valid) return getMaskSet().validPositions = $.extend(!0, {}, positionsClone),
					!1;
			} else getMaskSet().validPositions[pos] = validTest;
			return !0;
		}
		function stripValidPositions(start, end) {
			var i, startPos = start;
			for (void 0 != getMaskSet().validPositions[start] && getMaskSet().validPositions[start].input == opts.radixPoint && (end++,
				startPos++), i = startPos; end > i; i++) void 0 == getMaskSet().validPositions[i] || getMaskSet().validPositions[i].input == opts.radixPoint && i != getLastValidPosition() || delete getMaskSet().validPositions[i];
			for (i = end; i <= getLastValidPosition(); ) {
				var t = getMaskSet().validPositions[i], s = getMaskSet().validPositions[startPos];
				void 0 != t && void 0 == s ? (positionCanMatchDefinition(startPos, t.match.def) && isValid(startPos, t.input, !0) !== !1 && (delete getMaskSet().validPositions[i],
					i++), startPos++) : i++;
			}
			var lvp = getLastValidPosition();
			lvp >= start && void 0 != getMaskSet().validPositions[lvp] && getMaskSet().validPositions[lvp].input == opts.radixPoint && delete getMaskSet().validPositions[lvp],
				resetMaskSet(!0);
		}
		function getTestTemplate(pos, ndxIntlzr, tstPs) {
			function checkAlternationMatch(test, altNdx, altArr) {
				for (var isMatch = !1, altLocArr = test.locator[altNdx].toString().split(","), alndx = 0; alndx < altLocArr.length; alndx++) if (-1 != $.inArray(altLocArr[alndx], altArr)) {
					isMatch = !0;
					break;
				}
				return isMatch;
			}
			for (var testPos, testPositions = getTests(pos, ndxIntlzr, tstPs), lvp = getLastValidPosition(), lvTest = getMaskSet().validPositions[lvp] || getTests(0)[0], lvTestAltArr = void 0 != lvTest.alternation ? lvTest.locator[lvTest.alternation].split(",") : [], ndx = 0; ndx < testPositions.length && (testPos = testPositions[ndx],
				!(opts.greedy || testPos.match && (testPos.match.optionality === !1 || testPos.match.newBlockMarker === !1) && testPos.match.optionalQuantifier !== !0 && (void 0 == lvTest.alternation || void 0 != testPos.locator[lvTest.alternation] && checkAlternationMatch(testPos, lvTest.alternation, lvTestAltArr)))); ndx++) ;
			return testPos;
		}
		function getTest(pos) {
			return getMaskSet().validPositions[pos] ? getMaskSet().validPositions[pos].match : getTests(pos)[0].match;
		}
		function positionCanMatchDefinition(pos, def) {
			for (var valid = !1, tests = getTests(pos), tndx = 0; tndx < tests.length; tndx++) if (tests[tndx].match && tests[tndx].match.def == def) {
				valid = !0;
				break;
			}
			return valid;
		}
		function getTests(pos, ndxIntlzr, tstPs) {
			function ResolveTestFromToken(maskToken, ndxInitializer, loopNdx, quantifierRecurse) {
				function handleMatch(match, loopNdx, quantifierRecurse) {
					if (testPos > 1e4) return alert("jquery.inputmask: There is probably an error in your mask definition or in the code. Create an issue on github with an example of the mask you are using. " + getMaskSet().mask),
						!0;
					if (testPos == pos && void 0 == match.matches) return matches.push({
						match: match,
						locator: loopNdx.reverse()
					}), !0;
					if (void 0 != match.matches) {
						if (match.isGroup && quantifierRecurse !== !0) {
							if (match = handleMatch(maskToken.matches[tndx + 1], loopNdx)) return !0;
						} else if (match.isOptional) {
							var optionalToken = match;
							if (match = ResolveTestFromToken(match, ndxInitializer, loopNdx, quantifierRecurse)) {
								var latestMatch = matches[matches.length - 1].match, isFirstMatch = 0 == $.inArray(latestMatch, optionalToken.matches);
								isFirstMatch && (insertStop = !0), testPos = pos;
							}
						} else if (match.isAlternator) {
							var maltMatches, alternateToken = match, malternateMatches = [], currentMatches = matches.slice(), loopNdxCnt = loopNdx.length, altIndex = ndxInitializer.length > 0 ? ndxInitializer.shift() : -1;
							if (-1 == altIndex || "string" == typeof altIndex) {
								var altIndexArr, currentPos = testPos, ndxInitializerClone = ndxInitializer.slice();
								"string" == typeof altIndex && (altIndexArr = altIndex.split(","));
								for (var amndx = 0; amndx < alternateToken.matches.length; amndx++) {
									matches = [], match = handleMatch(alternateToken.matches[amndx], [ amndx ].concat(loopNdx), quantifierRecurse) || match,
										maltMatches = matches.slice(), testPos = currentPos, matches = [];
									for (var i = 0; i < ndxInitializerClone.length; i++) ndxInitializer[i] = ndxInitializerClone[i];
									for (var ndx1 = 0; ndx1 < maltMatches.length; ndx1++) for (var altMatch = maltMatches[ndx1], ndx2 = 0; ndx2 < malternateMatches.length; ndx2++) {
										var altMatch2 = malternateMatches[ndx2];
										if (altMatch.match.mask == altMatch2.match.mask && ("string" != typeof altIndex || -1 != $.inArray(altMatch.locator[loopNdxCnt].toString(), altIndexArr))) {
											maltMatches.splice(ndx1, 1), altMatch2.locator[loopNdxCnt] = altMatch2.locator[loopNdxCnt] + "," + altMatch.locator[loopNdxCnt],
												altMatch2.alternation = loopNdxCnt;
											break;
										}
									}
									malternateMatches = malternateMatches.concat(maltMatches);
								}
								"string" == typeof altIndex && (malternateMatches = $.map(malternateMatches, function(lmnt, ndx) {
									if (isFinite(ndx)) {
										var mamatch, altLocArr = lmnt.locator[loopNdxCnt].toString().split(",");
										lmnt.locator[loopNdxCnt] = void 0, lmnt.alternation = void 0;
										for (var alndx = 0; alndx < altLocArr.length; alndx++) mamatch = -1 != $.inArray(altLocArr[alndx], altIndexArr),
										mamatch && (void 0 != lmnt.locator[loopNdxCnt] ? (lmnt.locator[loopNdxCnt] += ",",
											lmnt.alternation = loopNdxCnt, lmnt.locator[loopNdxCnt] += altLocArr[alndx]) : lmnt.locator[loopNdxCnt] = parseInt(altLocArr[alndx]));
										if (void 0 != lmnt.locator[loopNdxCnt]) return lmnt;
									}
								})), matches = currentMatches.concat(malternateMatches), insertStop = !0;
							} else match = handleMatch(alternateToken.matches[altIndex], [ altIndex ].concat(loopNdx), quantifierRecurse);
							if (match) return !0;
						} else if (match.isQuantifier && quantifierRecurse !== !0) {
							var qt = match;
							opts.greedy = opts.greedy && isFinite(qt.quantifier.max);
							for (var qndx = ndxInitializer.length > 0 && quantifierRecurse !== !0 ? ndxInitializer.shift() : 0; qndx < (isNaN(qt.quantifier.max) ? qndx + 1 : qt.quantifier.max) && pos >= testPos; qndx++) {
								var tokenGroup = maskToken.matches[$.inArray(qt, maskToken.matches) - 1];
								if (match = handleMatch(tokenGroup, [ qndx ].concat(loopNdx), !0)) {
									var latestMatch = matches[matches.length - 1].match;
									latestMatch.optionalQuantifier = qndx > qt.quantifier.min - 1;
									var isFirstMatch = 0 == $.inArray(latestMatch, tokenGroup.matches);
									if (isFirstMatch) {
										if (qndx > qt.quantifier.min - 1) {
											insertStop = !0, testPos = pos;
											break;
										}
										return !0;
									}
									return !0;
								}
							}
						} else if (match = ResolveTestFromToken(match, ndxInitializer, loopNdx, quantifierRecurse)) return !0;
					} else testPos++;
				}
				for (var tndx = ndxInitializer.length > 0 ? ndxInitializer.shift() : 0; tndx < maskToken.matches.length; tndx++) if (maskToken.matches[tndx].isQuantifier !== !0) {
					var match = handleMatch(maskToken.matches[tndx], [ tndx ].concat(loopNdx), quantifierRecurse);
					if (match && testPos == pos) return match;
					if (testPos > pos) break;
				}
			}
			var maskTokens = getMaskSet().maskToken, testPos = ndxIntlzr ? tstPs : 0, ndxInitializer = ndxIntlzr || [ 0 ], matches = [], insertStop = !1;
			if (void 0 == ndxIntlzr) {
				for (var test, previousPos = pos - 1; void 0 == (test = getMaskSet().validPositions[previousPos]) && previousPos > -1; ) previousPos--;
				if (void 0 != test && previousPos > -1) testPos = previousPos, ndxInitializer = test.locator.slice(); else {
					for (previousPos = pos - 1; void 0 == (test = getMaskSet().tests[previousPos]) && previousPos > -1; ) previousPos--;
					void 0 != test && previousPos > -1 && (testPos = previousPos, ndxInitializer = test[0].locator.slice());
				}
			}
			for (var mtndx = ndxInitializer.shift(); mtndx < maskTokens.length; mtndx++) {
				var match = ResolveTestFromToken(maskTokens[mtndx], ndxInitializer, [ mtndx ]);
				if (match && testPos == pos || testPos > pos) break;
			}
			return (0 == matches.length || insertStop) && matches.push({
				match: {
					fn: null,
					cardinality: 0,
					optionality: !0,
					casing: null,
					def: ""
				},
				locator: []
			}), getMaskSet().tests[pos] = $.extend(!0, [], matches), getMaskSet().tests[pos];
		}
		function getBufferTemplate() {
			return void 0 == getMaskSet()._buffer && (getMaskSet()._buffer = getMaskTemplate(!1, 1)),
				getMaskSet()._buffer;
		}
		function getBuffer() {
			return void 0 == getMaskSet().buffer && (getMaskSet().buffer = getMaskTemplate(!0, getLastValidPosition(), !0)),
				getMaskSet().buffer;
		}
		function refreshFromBuffer(start, end) {
			var buffer = getBuffer().slice();
			if (start === !0) resetMaskSet(), start = 0, end = buffer.length; else for (var i = start; end > i; i++) delete getMaskSet().validPositions[i],
				delete getMaskSet().tests[i];
			for (var i = start; end > i; i++) buffer[i] != opts.skipOptionalPartCharacter && isValid(i, buffer[i], !0, !0);
		}
		function casing(elem, test) {
			switch (test.casing) {
				case "upper":
					elem = elem.toUpperCase();
					break;

				case "lower":
					elem = elem.toLowerCase();
			}
			return elem;
		}
		function isValid(pos, c, strict, fromSetValid) {
			function _isValid(position, c, strict, fromSetValid) {
				var rslt = !1;
				return $.each(getTests(position), function(ndx, tst) {
					for (var test = tst.match, loopend = c ? 1 : 0, chrs = "", i = (getBuffer(), test.cardinality); i > loopend; i--) chrs += getBufferElement(position - (i - 1));
					if (c && (chrs += c), rslt = null != test.fn ? test.fn.test(chrs, getMaskSet(), position, strict, opts) : c != test.def && c != opts.skipOptionalPartCharacter || "" == test.def ? !1 : {
							c: test.def,
							pos: position
						}, rslt !== !1) {
						var elem = void 0 != rslt.c ? rslt.c : c;
						elem = elem == opts.skipOptionalPartCharacter && null === test.fn ? test.def : elem;
						var validatedPos = position;
						if (void 0 != rslt.remove && stripValidPositions(rslt.remove, rslt.remove + 1),
								rslt.refreshFromBuffer) {
							var refresh = rslt.refreshFromBuffer;
							if (strict = !0, refreshFromBuffer(refresh === !0 ? refresh : refresh.start, refresh.end),
								void 0 == rslt.pos && void 0 == rslt.c) return rslt.pos = getLastValidPosition(),
								!1;
							if (validatedPos = void 0 != rslt.pos ? rslt.pos : position, validatedPos != position) return rslt = $.extend(rslt, isValid(validatedPos, elem, !0)),
								!1;
						} else if (rslt !== !0 && void 0 != rslt.pos && rslt.pos != position && (validatedPos = rslt.pos,
								refreshFromBuffer(position, validatedPos), validatedPos != position)) return rslt = $.extend(rslt, isValid(validatedPos, elem, !0)),
							!1;
						return 1 != rslt && void 0 == rslt.pos && void 0 == rslt.c ? !1 : (ndx > 0 && resetMaskSet(!0),
						setValidPosition(validatedPos, $.extend({}, tst, {
							input: casing(elem, test)
						}), fromSetValid) || (rslt = !1), !1);
					}
				}), rslt;
			}
			function alternate(pos, c, strict, fromSetValid) {
				var lastAlt, alternation, validPsClone = $.extend(!0, {}, getMaskSet().validPositions);
				for (lastAlt = getLastValidPosition(); lastAlt >= 0; lastAlt--) if (getMaskSet().validPositions[lastAlt] && void 0 != getMaskSet().validPositions[lastAlt].alternation) {
					alternation = getMaskSet().validPositions[lastAlt].alternation;
					break;
				}
				if (void 0 != alternation) for (var decisionPos in getMaskSet().validPositions) if (parseInt(decisionPos) > parseInt(lastAlt) && void 0 === getMaskSet().validPositions[decisionPos].alternation) {
					for (var altPos = getMaskSet().validPositions[decisionPos], decisionTaker = altPos.locator[alternation], altNdxs = getMaskSet().validPositions[lastAlt].locator[alternation].split(","), mndx = 0; mndx < altNdxs.length; mndx++) if (decisionTaker < altNdxs[mndx]) {
						for (var possibilityPos, possibilities, dp = decisionPos - 1; dp >= 0; dp--) if (possibilityPos = getMaskSet().validPositions[dp],
							void 0 != possibilityPos) {
							possibilities = possibilityPos.locator[alternation], possibilityPos.locator[alternation] = altNdxs[mndx];
							break;
						}
						if (decisionTaker != possibilityPos.locator[alternation]) {
							for (var buffer = getBuffer().slice(), i = decisionPos; i < getLastValidPosition() + 1; i++) delete getMaskSet().validPositions[i],
								delete getMaskSet().tests[i];
							resetMaskSet(!0), opts.keepStatic = !opts.keepStatic;
							for (var i = decisionPos; i < buffer.length; i++) buffer[i] != opts.skipOptionalPartCharacter && isValid(getLastValidPosition() + 1, buffer[i], !1, !0);
							possibilityPos.locator[alternation] = possibilities;
							var isValidRslt = isValid(pos, c, strict, fromSetValid);
							if (opts.keepStatic = !opts.keepStatic, isValidRslt) return isValidRslt;
							resetMaskSet(), getMaskSet().validPositions = $.extend(!0, {}, validPsClone);
						}
					}
					break;
				}
				return !1;
			}
			strict = strict === !0;
			for (var buffer = getBuffer(), pndx = pos - 1; pndx > -1 && (!getMaskSet().validPositions[pndx] || null != getMaskSet().validPositions[pndx].match.fn); pndx--) void 0 == getMaskSet().validPositions[pndx] && (!isMask(pndx) || buffer[pndx] != getPlaceholder(pndx)) && getTests(pndx).length > 1 && _isValid(pndx, buffer[pndx], !0);
			var maskPos = pos, result = !1;
			if (fromSetValid && maskPos >= getMaskLength() && resetMaskSet(!0), maskPos < getMaskLength() && (result = _isValid(maskPos, c, strict, fromSetValid),
				!strict && result === !1)) {
				var currentPosValid = getMaskSet().validPositions[maskPos];
				if (!currentPosValid || null != currentPosValid.match.fn || currentPosValid.match.def != c && c != opts.skipOptionalPartCharacter) {
					if ((opts.insertMode || void 0 == getMaskSet().validPositions[seekNext(maskPos)]) && !isMask(maskPos)) for (var nPos = maskPos + 1, snPos = seekNext(maskPos); snPos >= nPos; nPos++) if (result = _isValid(nPos, c, strict, fromSetValid),
						result !== !1) {
						maskPos = nPos;
						break;
					}
				} else result = {
					caret: seekNext(maskPos)
				};
			}
			return result === !1 && opts.keepStatic && isComplete(buffer) && (result = alternate(pos, c, strict, fromSetValid)),
			result === !0 && (result = {
				pos: maskPos
			}), result;
		}
		function isMask(pos) {
			var test = getTest(pos);
			return null != test.fn ? test.fn : !1;
		}
		function getMaskLength() {
			var maskLength;
			if (maxLength = $el.prop("maxLength"), -1 == maxLength && (maxLength = void 0),
				0 == opts.greedy) {
				var pos, lvp = getLastValidPosition(), testPos = getMaskSet().validPositions[lvp], ndxIntlzr = void 0 != testPos ? testPos.locator.slice() : void 0;
				for (pos = lvp + 1; void 0 == testPos || null != testPos.match.fn || null == testPos.match.fn && "" != testPos.match.def; pos++) testPos = getTestTemplate(pos, ndxIntlzr, pos - 1),
					ndxIntlzr = testPos.locator.slice();
				maskLength = pos;
			} else maskLength = getBuffer().length;
			return void 0 == maxLength || maxLength > maskLength ? maskLength : maxLength;
		}
		function seekNext(pos) {
			var maskL = getMaskLength();
			if (pos >= maskL) return maskL;
			for (var position = pos; ++position < maskL && !isMask(position) && (opts.nojumps !== !0 || opts.nojumpsThreshold > position); ) ;
			return position;
		}
		function seekPrevious(pos) {
			var position = pos;
			if (0 >= position) return 0;
			for (;--position > 0 && !isMask(position); ) ;
			return position;
		}
		function getBufferElement(position) {
			return void 0 == getMaskSet().validPositions[position] ? getPlaceholder(position) : getMaskSet().validPositions[position].input;
		}
		function writeBuffer(input, buffer, caretPos) {
			input._valueSet(buffer.join("")), void 0 != caretPos && caret(input, caretPos);
		}
		function getPlaceholder(pos, test) {
			test = test || getTest(pos);
			var placeholder = $.isFunction(test.placeholder) ? test.placeholder.call(this, opts) : test.placeholder;
			return void 0 != placeholder ? placeholder : null == test.fn ? test.def : opts.placeholder.charAt(pos % opts.placeholder.length);
		}
		function checkVal(input, writeOut, strict, nptvl, intelliCheck) {
			var inputValue = void 0 != nptvl ? nptvl.slice() : truncateInput(input._valueGet()).split("");
			if (resetMaskSet(), writeOut && input._valueSet(""), $.each(inputValue, function(ndx, charCode) {
					if (intelliCheck === !0) {
						var lvp = getLastValidPosition(), pos = -1 == lvp ? ndx : seekNext(lvp);
						-1 == $.inArray(charCode, getBufferTemplate().slice(lvp + 1, pos)) && keypressEvent.call(input, void 0, !0, charCode.charCodeAt(0), !1, strict, strict ? ndx : getMaskSet().p);
					} else keypressEvent.call(input, void 0, !0, charCode.charCodeAt(0), !1, strict, strict ? ndx : getMaskSet().p),
						strict = strict || ndx > 0 && ndx > getMaskSet().p;
				}), writeOut) {
				var keypressResult = opts.onKeyPress.call(this, void 0, getBuffer(), 0, opts);
				handleOnKeyResult(input, keypressResult), writeBuffer(input, getBuffer(), $(input).is(":focus") ? seekNext(getLastValidPosition(0)) : void 0);
			}
		}
		function escapeRegex(str) {
			return $.inputmask.escapeRegex.call(this, str);
		}
		function truncateInput(inputValue) {
			return inputValue.replace(new RegExp("(" + escapeRegex(getBufferTemplate().join("")) + ")*$"), "");
		}
		function unmaskedvalue($input) {
			if ($input.data("_inputmask") && !$input.hasClass("hasDatepicker")) {
				var umValue = [], vps = getMaskSet().validPositions;
				for (var pndx in vps) vps[pndx].match && null != vps[pndx].match.fn && umValue.push(vps[pndx].input);
				var unmaskedValue = (isRTL ? umValue.reverse() : umValue).join(""), bufferValue = (isRTL ? getBuffer().slice().reverse() : getBuffer()).join("");
				return $.isFunction(opts.onUnMask) && (unmaskedValue = opts.onUnMask.call($input, bufferValue, unmaskedValue, opts) || unmaskedValue),
					unmaskedValue;
			}
			return $input[0]._valueGet();
		}
		function TranslatePosition(pos) {
			if (isRTL && "number" == typeof pos && (!opts.greedy || "" != opts.placeholder)) {
				var bffrLght = getBuffer().length;
				pos = bffrLght - pos;
			}
			return pos;
		}
		function caret(input, begin, end) {
			var range, npt = input.jquery && input.length > 0 ? input[0] : input;
			if ("number" != typeof begin) {
				var data = $(npt).data("_inputmask");
				return !$(npt).is(":visible") && data && void 0 != data.caret ? (begin = data.caret.begin,
					end = data.caret.end) : npt.setSelectionRange ? (begin = npt.selectionStart, end = npt.selectionEnd) : document.selection && document.selection.createRange && (range = document.selection.createRange(),
					begin = 0 - range.duplicate().moveStart("character", -1e5), end = begin + range.text.length),
					begin = TranslatePosition(begin), end = TranslatePosition(end), {
					begin: begin,
					end: end
				};
			}
			begin = TranslatePosition(begin), end = TranslatePosition(end), end = "number" == typeof end ? end : begin;
			var data = $(npt).data("_inputmask") || {};
			data.caret = {
				begin: begin,
				end: end
			}, $(npt).data("_inputmask", data), $(npt).is(":visible") && (npt.scrollLeft = npt.scrollWidth,
			0 == opts.insertMode && begin == end && end++, npt.setSelectionRange ? (npt.selectionStart = begin,
				npt.selectionEnd = end) : npt.createTextRange && (range = npt.createTextRange(),
				range.collapse(!0), range.moveEnd("character", end), range.moveStart("character", begin),
				range.select()));
		}
		function determineLastRequiredPosition(returnDefinition) {
			var pos, testPos, buffer = getBuffer(), bl = buffer.length, lvp = getLastValidPosition(), positions = {}, lvTest = getMaskSet().validPositions[lvp], ndxIntlzr = void 0 != lvTest ? lvTest.locator.slice() : void 0;
			for (pos = lvp + 1; pos < buffer.length; pos++) testPos = getTestTemplate(pos, ndxIntlzr, pos - 1),
				ndxIntlzr = testPos.locator.slice(), positions[pos] = $.extend(!0, {}, testPos);
			var lvTestAltArr = lvTest && void 0 != lvTest.alternation ? lvTest.locator[lvTest.alternation].split(",") : [];
			for (pos = bl - 1; pos > lvp && (testPos = positions[pos].match, (testPos.optionality || testPos.optionalQuantifier || lvTest && void 0 != lvTest.alternation && void 0 != positions[pos].locator[lvTest.alternation] && -1 != $.inArray(positions[pos].locator[lvTest.alternation].toString(), lvTestAltArr)) && buffer[pos] == getPlaceholder(pos, testPos)); pos--) bl--;
			return returnDefinition ? {
				l: bl,
				def: positions[bl] ? positions[bl].match : void 0
			} : bl;
		}
		function clearOptionalTail(input) {
			for (var buffer = getBuffer(), tmpBuffer = buffer.slice(), rl = determineLastRequiredPosition(), lmib = tmpBuffer.length - 1; lmib > rl && !isMask(lmib); lmib--) ;
			tmpBuffer.splice(rl, lmib + 1 - rl), writeBuffer(input, tmpBuffer);
		}
		function isComplete(buffer) {
			if ($.isFunction(opts.isComplete)) return opts.isComplete.call($el, buffer, opts);
			if ("*" == opts.repeat) return void 0;
			var complete = !1, lrp = determineLastRequiredPosition(!0), aml = seekPrevious(lrp.l), lvp = getLastValidPosition();
			if (lvp == aml && (void 0 == lrp.def || lrp.def.newBlockMarker || lrp.def.optionalQuantifier)) {
				complete = !0;
				for (var i = 0; aml >= i; i++) {
					var mask = isMask(i);
					if (mask && (void 0 == buffer[i] || buffer[i] == getPlaceholder(i)) || !mask && buffer[i] != getPlaceholder(i)) {
						complete = !1;
						break;
					}
				}
			}
			return complete;
		}
		function isSelection(begin, end) {
			return isRTL ? begin - end > 1 || begin - end == 1 && opts.insertMode : end - begin > 1 || end - begin == 1 && opts.insertMode;
		}
		function installEventRuler(npt) {
			var events = $._data(npt).events;
			$.each(events, function(eventType, eventHandlers) {
				$.each(eventHandlers, function(ndx, eventHandler) {
					if ("inputmask" == eventHandler.namespace && "setvalue" != eventHandler.type) {
						var handler = eventHandler.handler;
						eventHandler.handler = function(e) {
							return this.readOnly || this.disabled ? void e.preventDefault : handler.apply(this, arguments);
						};
					}
				});
			});
		}
		function patchValueProperty(npt) {
			function PatchValhook(type) {
				if (void 0 == $.valHooks[type] || 1 != $.valHooks[type].inputmaskpatch) {
					var valueGet = $.valHooks[type] && $.valHooks[type].get ? $.valHooks[type].get : function(elem) {
						return elem.value;
					}, valueSet = $.valHooks[type] && $.valHooks[type].set ? $.valHooks[type].set : function(elem, value) {
						return elem.value = value, elem;
					};
					$.valHooks[type] = {
						get: function(elem) {
							var $elem = $(elem);
							if ($elem.data("_inputmask")) {
								if ($elem.data("_inputmask").opts.autoUnmask) return $elem.inputmask("unmaskedvalue");
								var result = valueGet(elem), inputData = $elem.data("_inputmask"), maskset = inputData.maskset, bufferTemplate = maskset._buffer;
								return bufferTemplate = bufferTemplate ? bufferTemplate.join("") : "", result != bufferTemplate ? result : "";
							}
							return valueGet(elem);
						},
						set: function(elem, value) {
							var result, $elem = $(elem), inputData = $elem.data("_inputmask");
							return inputData ? (result = valueSet(elem, $.isFunction(inputData.opts.onBeforeMask) ? inputData.opts.onBeforeMask.call(el, value, inputData.opts) || value : value),
								$elem.triggerHandler("setvalue.inputmask")) : result = valueSet(elem, value), result;
						},
						inputmaskpatch: !0
					};
				}
			}
			function getter() {
				var $self = $(this), inputData = $(this).data("_inputmask");
				return inputData ? inputData.opts.autoUnmask ? $self.inputmask("unmaskedvalue") : valueGet.call(this) != getBufferTemplate().join("") ? valueGet.call(this) : "" : valueGet.call(this);
			}
			function setter(value) {
				var inputData = $(this).data("_inputmask");
				inputData ? (valueSet.call(this, $.isFunction(inputData.opts.onBeforeMask) ? inputData.opts.onBeforeMask.call(el, value, inputData.opts) || value : value),
					$(this).triggerHandler("setvalue.inputmask")) : valueSet.call(this, value);
			}
			function InstallNativeValueSetFallback(npt) {
				$(npt).bind("mouseenter.inputmask", function() {
					var $input = $(this), input = this, value = input._valueGet();
					"" != value && value != getBuffer().join("") && $input.trigger("setvalue");
				});
				var events = $._data(npt).events, handlers = events.mouseover;
				if (handlers) {
					for (var ourHandler = handlers[handlers.length - 1], i = handlers.length - 1; i > 0; i--) handlers[i] = handlers[i - 1];
					handlers[0] = ourHandler;
				}
			}
			var valueGet, valueSet;
			if (!npt._valueGet) {
				if (Object.getOwnPropertyDescriptor) {
					Object.getOwnPropertyDescriptor(npt, "value");
				}
				document.__lookupGetter__ && npt.__lookupGetter__("value") ? (valueGet = npt.__lookupGetter__("value"),
					valueSet = npt.__lookupSetter__("value"), npt.__defineGetter__("value", getter),
					npt.__defineSetter__("value", setter)) : (valueGet = function() {
					return npt.value;
				}, valueSet = function(value) {
					npt.value = value;
				}, PatchValhook(npt.type), InstallNativeValueSetFallback(npt)), npt._valueGet = function() {
					return isRTL ? valueGet.call(this).split("").reverse().join("") : valueGet.call(this);
				}, npt._valueSet = function(value) {
					valueSet.call(this, isRTL ? value.split("").reverse().join("") : value);
				};
			}
		}
		function handleRemove(input, k, pos) {
			function generalize() {
				if (opts.keepStatic) {
					resetMaskSet(!0);
					var lastAlt, validInputs = [];
					for (lastAlt = getLastValidPosition(); lastAlt >= 0; lastAlt--) if (getMaskSet().validPositions[lastAlt]) {
						if (void 0 != getMaskSet().validPositions[lastAlt].alternation) break;
						validInputs.push(getMaskSet().validPositions[lastAlt].input), delete getMaskSet().validPositions[lastAlt];
					}
					if (lastAlt > 0) for (;validInputs.length > 0; ) getMaskSet().p = seekNext(getLastValidPosition()),
						keypressEvent.call(input, void 0, !0, validInputs.pop().charCodeAt(0), !1, !1, getMaskSet().p);
				}
			}
			if ((opts.numericInput || isRTL) && (k == $.inputmask.keyCode.BACKSPACE ? k = $.inputmask.keyCode.DELETE : k == $.inputmask.keyCode.DELETE && (k = $.inputmask.keyCode.BACKSPACE),
					isRTL)) {
				var pend = pos.end;
				pos.end = pos.begin, pos.begin = pend;
			}
			k == $.inputmask.keyCode.BACKSPACE && pos.end - pos.begin <= 1 ? pos.begin = seekPrevious(pos.begin) : k == $.inputmask.keyCode.DELETE && pos.begin == pos.end && pos.end++,
				stripValidPositions(pos.begin, pos.end), generalize();
			var firstMaskedPos = getLastValidPosition(pos.begin);
			firstMaskedPos < pos.begin ? (-1 == firstMaskedPos && resetMaskSet(), getMaskSet().p = seekNext(firstMaskedPos)) : getMaskSet().p = pos.begin;
		}
		function handleOnKeyResult(input, keyResult, caretPos) {
			if (keyResult && keyResult.refreshFromBuffer) {
				var refresh = keyResult.refreshFromBuffer;
				refreshFromBuffer(refresh === !0 ? refresh : refresh.start, refresh.end), resetMaskSet(!0),
				void 0 != caretPos && (writeBuffer(input, getBuffer()), caret(input, keyResult.caret || caretPos.begin, keyResult.caret || caretPos.end));
			}
		}
		function keydownEvent(e) {
			skipKeyPressEvent = !1;
			var input = this, $input = $(input), k = e.keyCode, pos = caret(input);
			k == $.inputmask.keyCode.BACKSPACE || k == $.inputmask.keyCode.DELETE || iphone && 127 == k || e.ctrlKey && 88 == k && !isInputEventSupported("cut") ? (e.preventDefault(),
			88 == k && (valueOnFocus = getBuffer().join("")), handleRemove(input, k, pos), writeBuffer(input, getBuffer(), getMaskSet().p),
			input._valueGet() == getBufferTemplate().join("") && $input.trigger("cleared"),
			opts.showTooltip && $input.prop("title", getMaskSet().mask)) : k == $.inputmask.keyCode.END || k == $.inputmask.keyCode.PAGE_DOWN ? setTimeout(function() {
				var caretPos = seekNext(getLastValidPosition());
				opts.insertMode || caretPos != getMaskLength() || e.shiftKey || caretPos--, caret(input, e.shiftKey ? pos.begin : caretPos, caretPos);
			}, 0) : k == $.inputmask.keyCode.HOME && !e.shiftKey || k == $.inputmask.keyCode.PAGE_UP ? caret(input, 0, e.shiftKey ? pos.begin : 0) : k == $.inputmask.keyCode.ESCAPE || 90 == k && e.ctrlKey ? (checkVal(input, !0, !1, valueOnFocus.split("")),
				$input.click()) : k != $.inputmask.keyCode.INSERT || e.shiftKey || e.ctrlKey ? 0 != opts.insertMode || e.shiftKey || (k == $.inputmask.keyCode.RIGHT ? setTimeout(function() {
				var caretPos = caret(input);
				caret(input, caretPos.begin);
			}, 0) : k == $.inputmask.keyCode.LEFT && setTimeout(function() {
				var caretPos = caret(input);
				caret(input, isRTL ? caretPos.begin + 1 : caretPos.begin - 1);
			}, 0)) : (opts.insertMode = !opts.insertMode, caret(input, opts.insertMode || pos.begin != getMaskLength() ? pos.begin : pos.begin - 1));
			var currentCaretPos = caret(input), keydownResult = opts.onKeyDown.call(this, e, getBuffer(), currentCaretPos.begin, opts);
			handleOnKeyResult(input, keydownResult, currentCaretPos), ignorable = -1 != $.inArray(k, opts.ignorables);
		}
		function keypressEvent(e, checkval, k, writeOut, strict, ndx) {
			if (void 0 == k && skipKeyPressEvent) return !1;
			skipKeyPressEvent = !0;
			var input = this, $input = $(input);
			e = e || window.event;
			var k = checkval ? k : e.which || e.charCode || e.keyCode;
			if (!(checkval === !0 || e.ctrlKey && e.altKey) && (e.ctrlKey || e.metaKey || ignorable)) return !0;
			if (k) {
				checkval !== !0 && 46 == k && 0 == e.shiftKey && "," == opts.radixPoint && (k = 44);
				var forwardPosition, pos = checkval ? {
					begin: ndx,
					end: ndx
				} : caret(input), c = String.fromCharCode(k), isSlctn = isSelection(pos.begin, pos.end);
				isSlctn && (getMaskSet().undoPositions = $.extend(!0, {}, getMaskSet().validPositions),
					handleRemove(input, $.inputmask.keyCode.DELETE, pos), opts.insertMode || (opts.insertMode = !opts.insertMode,
					setValidPosition(pos.begin, strict), opts.insertMode = !opts.insertMode), isSlctn = !opts.multi),
					getMaskSet().writeOutBuffer = !0;
				var p = isRTL && !isSlctn ? pos.end : pos.begin, valResult = isValid(p, c, strict);
				if (valResult !== !1) {
					if (valResult !== !0 && (p = void 0 != valResult.pos ? valResult.pos : p, c = void 0 != valResult.c ? valResult.c : c),
							resetMaskSet(!0), void 0 != valResult.caret) forwardPosition = valResult.caret; else {
						var vps = getMaskSet().validPositions;
						forwardPosition = !opts.keepStatic && (void 0 != vps[p + 1] && getTests(p + 1, vps[p].locator.slice(), p).length > 1 || void 0 != vps[p].alternation) ? p + 1 : seekNext(p);
					}
					getMaskSet().p = forwardPosition;
				}
				if (writeOut !== !1) {
					var self = this;
					if (setTimeout(function() {
							opts.onKeyValidation.call(self, valResult, opts);
						}, 0), getMaskSet().writeOutBuffer && valResult !== !1) {
						var buffer = getBuffer();
						writeBuffer(input, buffer, checkval ? void 0 : opts.numericInput ? seekPrevious(forwardPosition) : forwardPosition),
						checkval !== !0 && setTimeout(function() {
							isComplete(buffer) === !0 && $input.trigger("complete"), skipInputEvent = !0, $input.trigger("input");
						}, 0);
					} else isSlctn && (getMaskSet().buffer = void 0, getMaskSet().validPositions = getMaskSet().undoPositions);
				} else isSlctn && (getMaskSet().buffer = void 0, getMaskSet().validPositions = getMaskSet().undoPositions);
				if (opts.showTooltip && $input.prop("title", getMaskSet().mask), e && 1 != checkval) {
					e.preventDefault();
					var currentCaretPos = caret(input), keypressResult = opts.onKeyPress.call(this, e, getBuffer(), currentCaretPos.begin, opts);
					handleOnKeyResult(input, keypressResult, currentCaretPos);
				}
			}
		}
		function keyupEvent(e) {
			var $input = $(this), input = this, k = e.keyCode, buffer = getBuffer(), currentCaretPos = caret(input), keyupResult = opts.onKeyUp.call(this, e, buffer, currentCaretPos.begin, opts);
			handleOnKeyResult(input, keyupResult, currentCaretPos), k == $.inputmask.keyCode.TAB && opts.showMaskOnFocus && ($input.hasClass("focus-inputmask") && 0 == input._valueGet().length ? (resetMaskSet(),
				buffer = getBuffer(), writeBuffer(input, buffer), caret(input, 0), valueOnFocus = getBuffer().join("")) : (writeBuffer(input, buffer),
				caret(input, TranslatePosition(0), TranslatePosition(getMaskLength()))));
		}
		function pasteEvent(e) {
			if (skipInputEvent === !0 && "input" == e.type) return skipInputEvent = !1, !0;
			var input = this, $input = $(input), inputValue = input._valueGet(), caretPos = caret(input);
			if ("propertychange" == e.type && input._valueGet().length <= getMaskLength()) return !0;
			"paste" == e.type && (window.clipboardData && window.clipboardData.getData ? inputValue = inputValue.substr(0, caretPos.begin) + window.clipboardData.getData("Text") + inputValue.substr(caretPos.end, inputValue.length) : e.originalEvent && e.originalEvent.clipboardData && e.originalEvent.clipboardData.getData && (inputValue = inputValue.substr(0, caretPos.begin) + e.originalEvent.clipboardData.getData("text/plain") + inputValue.substr(caretPos.end, inputValue.length)));
			var pasteValue = $.isFunction(opts.onBeforePaste) ? opts.onBeforePaste.call(input, inputValue, opts) || inputValue : inputValue;
			return checkVal(input, !0, !1, isRTL ? pasteValue.split("").reverse() : pasteValue.split(""), !0),
				$input.click(), isComplete(getBuffer()) === !0 && $input.trigger("complete"), !1;
		}
		function mobileInputEvent(e) {
			if (skipInputEvent === !0 && "input" == e.type) return skipInputEvent = !1, !0;
			var input = this, caretPos = caret(input), currentValue = input._valueGet();
			currentValue = currentValue.replace(new RegExp("(" + escapeRegex(getBufferTemplate().join("")) + ")*"), ""),
			caretPos.begin > currentValue.length && (caret(input, currentValue.length), caretPos = caret(input)),
			getBuffer().length - currentValue.length != 1 || currentValue.charAt(caretPos.begin) == getBuffer()[caretPos.begin] || currentValue.charAt(caretPos.begin + 1) == getBuffer()[caretPos.begin] || isMask(caretPos.begin) || (e.keyCode = $.inputmask.keyCode.BACKSPACE,
				keydownEvent.call(input, e)), e.preventDefault();
		}
		function inputFallBackEvent(e) {
			if (skipInputEvent === !0 && "input" == e.type) return skipInputEvent = !1, !0;
			var input = this, caretPos = caret(input), currentValue = input._valueGet();
			caret(input, caretPos.begin - 1);
			var keypress = $.Event("keypress");
			keypress.which = currentValue.charCodeAt(caretPos.begin - 1), skipKeyPressEvent = !1,
				ignorable = !1, keypressEvent.call(input, keypress, void 0, void 0, !1);
			var forwardPosition = getMaskSet().p;
			writeBuffer(input, getBuffer(), opts.numericInput ? seekPrevious(forwardPosition) : forwardPosition),
				e.preventDefault();
		}
		function compositionupdateEvent(e) {
			skipInputEvent = !0;
			var input = this;
			return setTimeout(function() {
				caret(input, caret(input).begin - 1);
				var keypress = $.Event("keypress");
				keypress.which = e.originalEvent.data.charCodeAt(0), skipKeyPressEvent = !1, ignorable = !1,
					keypressEvent.call(input, keypress, void 0, void 0, !1);
				var forwardPosition = getMaskSet().p;
				writeBuffer(input, getBuffer(), opts.numericInput ? seekPrevious(forwardPosition) : forwardPosition);
			}, 0), !1;
		}
		function mask(el) {
			if ($el = $(el), $el.is(":input") && isInputTypeSupported($el.attr("type"))) {
				if ($el.data("_inputmask", {
						maskset: maskset,
						opts: opts,
						isRTL: !1
					}), opts.showTooltip && $el.prop("title", getMaskSet().mask), ("rtl" == el.dir || opts.rightAlign) && $el.css("text-align", "right"),
					"rtl" == el.dir || opts.numericInput) {
					el.dir = "ltr", $el.removeAttr("dir");
					var inputData = $el.data("_inputmask");
					inputData.isRTL = !0, $el.data("_inputmask", inputData), isRTL = !0;
				}
				$el.unbind(".inputmask"), $el.removeClass("focus-inputmask"), $el.closest("form").bind("submit", function() {
					valueOnFocus != getBuffer().join("") && $el.change(), $el[0]._valueGet && $el[0]._valueGet() == getBufferTemplate().join("") && $el[0]._valueSet(""),
					opts.autoUnmask && opts.removeMaskOnSubmit && $el.inputmask("remove");
				}).bind("reset", function() {
					setTimeout(function() {
						$el.trigger("setvalue");
					}, 0);
				}), $el.bind("mouseenter.inputmask", function() {
					var $input = $(this), input = this;
					!$input.hasClass("focus-inputmask") && opts.showMaskOnHover && input._valueGet() != getBuffer().join("") && writeBuffer(input, getBuffer());
				}).bind("blur.inputmask", function() {
					var $input = $(this), input = this;
					if ($input.data("_inputmask")) {
						var nptValue = input._valueGet(), buffer = getBuffer();
						$input.removeClass("focus-inputmask"), valueOnFocus != getBuffer().join("") && $input.change(),
						opts.clearMaskOnLostFocus && "" != nptValue && (nptValue == getBufferTemplate().join("") ? input._valueSet("") : clearOptionalTail(input)),
						isComplete(buffer) === !1 && ($input.trigger("incomplete"), opts.clearIncomplete && (resetMaskSet(),
							opts.clearMaskOnLostFocus ? input._valueSet("") : (buffer = getBufferTemplate().slice(),
								writeBuffer(input, buffer))));
					}
				}).bind("focus.inputmask", function() {
					var $input = $(this), input = this, nptValue = input._valueGet();
					opts.showMaskOnFocus && !$input.hasClass("focus-inputmask") && (!opts.showMaskOnHover || opts.showMaskOnHover && "" == nptValue) && input._valueGet() != getBuffer().join("") && writeBuffer(input, getBuffer(), seekNext(getLastValidPosition())),
						$input.addClass("focus-inputmask"), valueOnFocus = getBuffer().join("");
				}).bind("mouseleave.inputmask", function() {
					var $input = $(this), input = this;
					opts.clearMaskOnLostFocus && ($input.hasClass("focus-inputmask") || input._valueGet() == $input.attr("placeholder") || (input._valueGet() == getBufferTemplate().join("") || "" == input._valueGet() ? input._valueSet("") : clearOptionalTail(input)));
				}).bind("click.inputmask", function() {
					var input = this;
					$(input).is(":focus") && setTimeout(function() {
						var selectedCaret = caret(input);
						if (selectedCaret.begin == selectedCaret.end) if (opts.radixFocus && "" != opts.radixPoint && -1 != $.inArray(opts.radixPoint, getBuffer()) && getBuffer().join("") == getBufferTemplate().join("")) caret(input, $.inArray(opts.radixPoint, getBuffer())); else {
							var clickPosition = isRTL ? TranslatePosition(selectedCaret.begin) : selectedCaret.begin, lastPosition = seekNext(getLastValidPosition(clickPosition));
							lastPosition > clickPosition ? caret(input, isMask(clickPosition) ? clickPosition : seekNext(clickPosition)) : caret(input, lastPosition);
						}
					}, 0);
				}).bind("dblclick.inputmask", function() {
					var input = this;
					setTimeout(function() {
						caret(input, 0, seekNext(getLastValidPosition()));
					}, 0);
				}).bind(PasteEventType + ".inputmask dragdrop.inputmask drop.inputmask", pasteEvent).bind("setvalue.inputmask", function() {
					var input = this;
					checkVal(input, !0, !1, void 0, !0), valueOnFocus = getBuffer().join(""), (opts.clearMaskOnLostFocus || opts.clearIncomplete) && input._valueGet() == getBufferTemplate().join("") && input._valueSet("");
				}).bind("cut.inputmask", function(e) {
					skipInputEvent = !0;
					var input = this, $input = $(input), pos = caret(input);
					handleRemove(input, $.inputmask.keyCode.DELETE, pos);
					var keypressResult = opts.onKeyPress.call(this, e, getBuffer(), getMaskSet().p, opts);
					handleOnKeyResult(input, keypressResult, {
						begin: getMaskSet().p,
						end: getMaskSet().p
					}), input._valueGet() == getBufferTemplate().join("") && $input.trigger("cleared"),
					opts.showTooltip && $input.prop("title", getMaskSet().mask);
				}).bind("complete.inputmask", opts.oncomplete).bind("incomplete.inputmask", opts.onincomplete).bind("cleared.inputmask", opts.oncleared),
					$el.bind("keydown.inputmask", keydownEvent).bind("keypress.inputmask", keypressEvent).bind("keyup.inputmask", keyupEvent).bind("compositionupdate.inputmask", compositionupdateEvent),
				"paste" !== PasteEventType || msie1x || $el.bind("input.inputmask", inputFallBackEvent),
				msie1x && $el.bind("input.inputmask", pasteEvent), (android || androidfirefox || androidchrome || kindle) && ("input" == PasteEventType && $el.unbind(PasteEventType + ".inputmask"),
					$el.bind("input.inputmask", mobileInputEvent)), patchValueProperty(el);
				var initialValue = $.isFunction(opts.onBeforeMask) ? opts.onBeforeMask.call(el, el._valueGet(), opts) || el._valueGet() : el._valueGet();
				checkVal(el, !0, !1, initialValue.split(""), !0), valueOnFocus = getBuffer().join("");
				var activeElement;
				try {
					activeElement = document.activeElement;
				} catch (e) {}
				isComplete(getBuffer()) === !1 && opts.clearIncomplete && resetMaskSet(), opts.clearMaskOnLostFocus ? getBuffer().join("") == getBufferTemplate().join("") ? el._valueSet("") : clearOptionalTail(el) : writeBuffer(el, getBuffer()),
				activeElement === el && ($el.addClass("focus-inputmask"), caret(el, seekNext(getLastValidPosition()))),
					installEventRuler(el);
			}
		}
		var valueOnFocus, $el, maxLength, isRTL = !1, skipKeyPressEvent = !1, skipInputEvent = !1, ignorable = !1;
		if (void 0 != actionObj) switch (actionObj.action) {
			case "isComplete":
				return $el = $(actionObj.el), maskset = $el.data("_inputmask").maskset, opts = $el.data("_inputmask").opts,
					isComplete(actionObj.buffer);

			case "unmaskedvalue":
				return $el = actionObj.$input, maskset = $el.data("_inputmask").maskset, opts = $el.data("_inputmask").opts,
					isRTL = actionObj.$input.data("_inputmask").isRTL, unmaskedvalue(actionObj.$input);

			case "mask":
				valueOnFocus = getBuffer().join(""), mask(actionObj.el);
				break;

			case "format":
				$el = $({}), $el.data("_inputmask", {
					maskset: maskset,
					opts: opts,
					isRTL: opts.numericInput
				}), opts.numericInput && (isRTL = !0);
				var valueBuffer = ($.isFunction(opts.onBeforeMask) ? opts.onBeforeMask.call($el, actionObj.value, opts) || actionObj.value : actionObj.value).split("");
				return checkVal($el, !1, !1, isRTL ? valueBuffer.reverse() : valueBuffer, !0), opts.onKeyPress.call(this, void 0, getBuffer(), 0, opts),
					actionObj.metadata ? {
						value: isRTL ? getBuffer().slice().reverse().join("") : getBuffer().join(""),
						metadata: $el.inputmask("getmetadata")
					} : isRTL ? getBuffer().slice().reverse().join("") : getBuffer().join("");

			case "isValid":
				$el = $({}), $el.data("_inputmask", {
					maskset: maskset,
					opts: opts,
					isRTL: opts.numericInput
				}), opts.numericInput && (isRTL = !0);
				var valueBuffer = actionObj.value.split("");
				checkVal($el, !1, !0, isRTL ? valueBuffer.reverse() : valueBuffer);
				for (var buffer = getBuffer(), rl = determineLastRequiredPosition(), lmib = buffer.length - 1; lmib > rl && !isMask(lmib); lmib--) ;
				return buffer.splice(rl, lmib + 1 - rl), isComplete(buffer) && actionObj.value == buffer.join("");

			case "getemptymask":
				return $el = $(actionObj.el), maskset = $el.data("_inputmask").maskset, opts = $el.data("_inputmask").opts,
					getBufferTemplate();

			case "remove":
				var el = actionObj.el;
				$el = $(el), maskset = $el.data("_inputmask").maskset, opts = $el.data("_inputmask").opts,
					el._valueSet(unmaskedvalue($el)), $el.unbind(".inputmask"), $el.removeClass("focus-inputmask"),
					$el.removeData("_inputmask");
				var valueProperty;
				Object.getOwnPropertyDescriptor && (valueProperty = Object.getOwnPropertyDescriptor(el, "value")),
					valueProperty && valueProperty.get ? el._valueGet && Object.defineProperty(el, "value", {
						get: el._valueGet,
						set: el._valueSet
					}) : document.__lookupGetter__ && el.__lookupGetter__("value") && el._valueGet && (el.__defineGetter__("value", el._valueGet),
						el.__defineSetter__("value", el._valueSet));
				try {
					delete el._valueGet, delete el._valueSet;
				} catch (e) {
					el._valueGet = void 0, el._valueSet = void 0;
				}
				break;

			case "getmetadata":
				if ($el = $(actionObj.el), maskset = $el.data("_inputmask").maskset, opts = $el.data("_inputmask").opts,
						$.isArray(maskset.metadata)) {
					for (var alternation, lvp = getLastValidPosition(), firstAlt = lvp; firstAlt >= 0; firstAlt--) if (getMaskSet().validPositions[firstAlt] && void 0 != getMaskSet().validPositions[firstAlt].alternation) {
						alternation = getMaskSet().validPositions[firstAlt].alternation;
						break;
					}
					return void 0 != alternation ? maskset.metadata[getMaskSet().validPositions[lvp].locator[alternation]] : maskset.metadata[0];
				}
				return maskset.metadata;
		}
	}
	if (void 0 === $.fn.inputmask) {
		var msie1x = "function" == typeof ScriptEngineMajorVersion ? ScriptEngineMajorVersion() : new Function("/*@cc_on return @_jscript_version; @*/")() >= 10, ua = navigator.userAgent, iphone = null !== ua.match(new RegExp("iphone", "i")), android = null !== ua.match(new RegExp("android.*safari.*", "i")), androidchrome = null !== ua.match(new RegExp("android.*chrome.*", "i")), androidfirefox = null !== ua.match(new RegExp("android.*firefox.*", "i")), kindle = /Kindle/i.test(ua) || /Silk/i.test(ua) || /KFTT/i.test(ua) || /KFOT/i.test(ua) || /KFJWA/i.test(ua) || /KFJWI/i.test(ua) || /KFSOWI/i.test(ua) || /KFTHWA/i.test(ua) || /KFTHWI/i.test(ua) || /KFAPWA/i.test(ua) || /KFAPWI/i.test(ua), PasteEventType = isInputEventSupported("paste") ? "paste" : isInputEventSupported("input") ? "input" : "propertychange";
		$.inputmask = {
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
				oncomplete: $.noop,
				onincomplete: $.noop,
				oncleared: $.noop,
				repeat: 0,
				greedy: !0,
				autoUnmask: !1,
				removeMaskOnSubmit: !0,
				clearMaskOnLostFocus: !0,
				insertMode: !0,
				clearIncomplete: !1,
				aliases: {},
				alias: null,
				onKeyUp: $.noop,
				onKeyPress: $.noop,
				onKeyDown: $.noop,
				onBeforeMask: void 0,
				onBeforePaste: void 0,
				onUnMask: void 0,
				showMaskOnFocus: !0,
				showMaskOnHover: !0,
				onKeyValidation: $.noop,
				skipOptionalPartCharacter: " ",
				showTooltip: !1,
				numericInput: !1,
				rightAlign: !1,
				radixPoint: "",
				radixFocus: !1,
				nojumps: !1,
				nojumpsThreshold: 0,
				keepStatic: void 0,
				definitions: {
					"9": {
						validator: "[0-9]",
						cardinality: 1,
						definitionSymbol: "*"
					},
					a: {
						validator: "[A-Za-zА-яЁёÀ-ÿµ]",
						cardinality: 1,
						definitionSymbol: "*"
					},
					"*": {
						validator: "[0-9A-Za-zА-яЁёÀ-ÿµ]",
						cardinality: 1
					}
				},
				ignorables: [ 8, 9, 13, 19, 27, 33, 34, 35, 36, 37, 38, 39, 40, 45, 46, 93, 112, 113, 114, 115, 116, 117, 118, 119, 120, 121, 122, 123 ],
				isComplete: void 0
			},
			keyCode: {
				ALT: 18,
				BACKSPACE: 8,
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
				WINDOWS: 91
			},
			masksCache: {},
			escapeRegex: function(str) {
				var specials = [ "/", ".", "*", "+", "?", "|", "(", ")", "[", "]", "{", "}", "\\" ];
				return str.replace(new RegExp("(\\" + specials.join("|\\") + ")", "gim"), "\\$1");
			},
			format: function(value, options, metadata) {
				var opts = $.extend(!0, {}, $.inputmask.defaults, options);
				return resolveAlias(opts.alias, options, opts), maskScope({
					action: "format",
					value: value,
					metadata: metadata
				}, generateMaskSet(opts), opts);
			},
			isValid: function(value, options) {
				var opts = $.extend(!0, {}, $.inputmask.defaults, options);
				return resolveAlias(opts.alias, options, opts), maskScope({
					action: "isValid",
					value: value
				}, generateMaskSet(opts), opts);
			}
		}, $.fn.inputmask = function(fn, options, targetScope, targetData, msk) {
			function importAttributeOptions(npt, opts, importedOptionsContainer) {
				var $npt = $(npt);
				$npt.data("inputmask-alias") && resolveAlias($npt.data("inputmask-alias"), {}, opts);
				for (var option in opts) {
					var optionData = $npt.data("inputmask-" + option.toLowerCase());
					void 0 != optionData && ("mask" == option && 0 == optionData.indexOf("[") ? (opts[option] = optionData.replace(/[\s[\]]/g, "").split("','"),
						opts[option][0] = opts[option][0].replace("'", ""), opts[option][opts[option].length - 1] = opts[option][opts[option].length - 1].replace("'", "")) : opts[option] = "boolean" == typeof optionData ? optionData : optionData.toString(),
					importedOptionsContainer && (importedOptionsContainer[option] = opts[option]));
				}
				return opts;
			}
			targetScope = targetScope || maskScope, targetData = targetData || "_inputmask";
			var maskset, opts = $.extend(!0, {}, $.inputmask.defaults, options);
			if ("string" == typeof fn) switch (fn) {
				case "mask":
					return resolveAlias(opts.alias, options, opts), maskset = generateMaskSet(opts, targetScope !== maskScope),
						void 0 == maskset ? this : this.each(function() {
							targetScope({
								action: "mask",
								el: this
							}, $.extend(!0, {}, maskset), importAttributeOptions(this, opts));
						});

				case "unmaskedvalue":
					var $input = $(this);
					return $input.data(targetData) ? targetScope({
						action: "unmaskedvalue",
						$input: $input
					}) : $input.val();

				case "remove":
					return this.each(function() {
						var $input = $(this);
						$input.data(targetData) && targetScope({
							action: "remove",
							el: this
						});
					});

				case "getemptymask":
					return this.data(targetData) ? targetScope({
						action: "getemptymask",
						el: this
					}) : "";

				case "hasMaskedValue":
					return this.data(targetData) ? !this.data(targetData).opts.autoUnmask : !1;

				case "isComplete":
					return this.data(targetData) ? targetScope({
						action: "isComplete",
						buffer: this[0]._valueGet().split(""),
						el: this
					}) : !0;

				case "getmetadata":
					return this.data(targetData) ? targetScope({
						action: "getmetadata",
						el: this
					}) : void 0;

				case "_detectScope":
					return resolveAlias(opts.alias, options, opts), void 0 == msk || resolveAlias(msk, options, opts) || -1 != $.inArray(msk, [ "mask", "unmaskedvalue", "remove", "getemptymask", "hasMaskedValue", "isComplete", "getmetadata", "_detectScope" ]) || (opts.mask = msk),
					$.isFunction(opts.mask) && (opts.mask = opts.mask.call(this, opts)), $.isArray(opts.mask);

				default:
					return resolveAlias(opts.alias, options, opts), resolveAlias(fn, options, opts) || (opts.mask = fn),
						maskset = generateMaskSet(opts, targetScope !== maskScope), void 0 == maskset ? this : this.each(function() {
						targetScope({
							action: "mask",
							el: this
						}, $.extend(!0, {}, maskset), importAttributeOptions(this, opts));
					});
			} else {
				if ("object" == typeof fn) return opts = $.extend(!0, {}, $.inputmask.defaults, fn),
					resolveAlias(opts.alias, fn, opts), maskset = generateMaskSet(opts, targetScope !== maskScope),
					void 0 == maskset ? this : this.each(function() {
						targetScope({
							action: "mask",
							el: this
						}, $.extend(!0, {}, maskset), importAttributeOptions(this, opts));
					});
				if (void 0 == fn) return this.each(function() {
					var attrOptions = $(this).attr("data-inputmask");
					if (attrOptions && "" != attrOptions) try {
						attrOptions = attrOptions.replace(new RegExp("'", "g"), '"');
						var dataoptions = $.parseJSON("{" + attrOptions + "}");
						$.extend(!0, dataoptions, options), opts = $.extend(!0, {}, $.inputmask.defaults, dataoptions),
							opts = importAttributeOptions(this, opts), resolveAlias(opts.alias, dataoptions, opts),
							opts.alias = void 0, $(this).inputmask("mask", opts, targetScope);
					} catch (ex) {}
					if ($(this).attr("data-inputmask-mask") || $(this).attr("data-inputmask-alias")) {
						opts = $.extend(!0, {}, $.inputmask.defaults, {});
						var dataOptions = {};
						opts = importAttributeOptions(this, opts, dataOptions), resolveAlias(opts.alias, dataOptions, opts),
							opts.alias = void 0, $(this).inputmask("mask", opts, targetScope);
					}
				});
			}
		};
	}
	return $.fn.inputmask;
}(jQuery), function($) {
	return $.extend($.inputmask.defaults.definitions, {
		h: {
			validator: "[01][0-9]|2[0-3]",
			cardinality: 2,
			prevalidator: [ {
				validator: "[0-2]",
				cardinality: 1
			} ]
		},
		s: {
			validator: "[0-5][0-9]",
			cardinality: 2,
			prevalidator: [ {
				validator: "[0-5]",
				cardinality: 1
			} ]
		},
		d: {
			validator: "0[1-9]|[12][0-9]|3[01]",
			cardinality: 2,
			prevalidator: [ {
				validator: "[0-3]",
				cardinality: 1
			} ]
		},
		m: {
			validator: "0[1-9]|1[012]",
			cardinality: 2,
			prevalidator: [ {
				validator: "[01]",
				cardinality: 1
			} ]
		},
		y: {
			validator: "(19|20)\\d{2}",
			cardinality: 4,
			prevalidator: [ {
				validator: "[12]",
				cardinality: 1
			}, {
				validator: "(19|20)",
				cardinality: 2
			}, {
				validator: "(19|20)\\d",
				cardinality: 3
			} ]
		}
	}), $.extend($.inputmask.defaults.aliases, {
		"dd/mm/yyyy": {
			mask: "1/2/y",
			placeholder: "dd/mm/yyyy",
			regex: {
				val1pre: new RegExp("[0-3]"),
				val1: new RegExp("0[1-9]|[12][0-9]|3[01]"),
				val2pre: function(separator) {
					var escapedSeparator = $.inputmask.escapeRegex.call(this, separator);
					return new RegExp("((0[1-9]|[12][0-9]|3[01])" + escapedSeparator + "[01])");
				},
				val2: function(separator) {
					var escapedSeparator = $.inputmask.escapeRegex.call(this, separator);
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
				return (isNaN(enteredyear) ? !1 : enteredyear >= minyear && maxyear >= enteredyear) || (isNaN(enteredyear2) ? !1 : enteredyear2 >= minyear && maxyear >= enteredyear2);
			},
			determinebaseyear: function(minyear, maxyear, hint) {
				var currentyear = new Date().getFullYear();
				if (minyear > currentyear) return minyear;
				if (currentyear > maxyear) {
					for (var maxYearPrefix = maxyear.toString().slice(0, 2), maxYearPostfix = maxyear.toString().slice(2, 4); maxYearPrefix + hint > maxyear; ) maxYearPrefix--;
					var maxxYear = maxYearPrefix + maxYearPostfix;
					return minyear > maxxYear ? minyear : maxxYear;
				}
				return currentyear;
			},
			onKeyUp: function(e) {
				var $input = $(this);
				if (e.ctrlKey && e.keyCode == $.inputmask.keyCode.RIGHT) {
					var today = new Date();
					$input.val(today.getDate().toString() + (today.getMonth() + 1).toString() + today.getFullYear().toString());
				}
			},
			definitions: {
				"1": {
					validator: function(chrs, maskset, pos, strict, opts) {
						var isValid = opts.regex.val1.test(chrs);
						return strict || isValid || chrs.charAt(1) != opts.separator && -1 == "-./".indexOf(chrs.charAt(1)) || !(isValid = opts.regex.val1.test("0" + chrs.charAt(0))) ? isValid : (maskset.buffer[pos - 1] = "0",
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
							var isValid = 1 == chrs.length ? opts.regex.val1pre.test(chrs) : opts.regex.val1.test(chrs);
							return strict || isValid || !(isValid = opts.regex.val1.test("0" + chrs)) ? isValid : (maskset.buffer[pos] = "0",
								pos++, {
								pos: pos
							});
						},
						cardinality: 1
					} ]
				},
				"2": {
					validator: function(chrs, maskset, pos, strict, opts) {
						var frontValue = opts.mask.indexOf("2") == opts.mask.length - 1 ? maskset.buffer.join("").substr(5, 3) : maskset.buffer.join("").substr(0, 3);
						-1 != frontValue.indexOf(opts.placeholder[0]) && (frontValue = "01" + opts.separator);
						var isValid = opts.regex.val2(opts.separator).test(frontValue + chrs);
						if (!strict && !isValid && (chrs.charAt(1) == opts.separator || -1 != "-./".indexOf(chrs.charAt(1))) && (isValid = opts.regex.val2(opts.separator).test(frontValue + "0" + chrs.charAt(0)))) return maskset.buffer[pos - 1] = "0",
						{
							refreshFromBuffer: {
								start: pos - 1,
								end: pos
							},
							pos: pos,
							c: chrs.charAt(0)
						};
						if (opts.mask.indexOf("2") == opts.mask.length - 1 && isValid) {
							var dayMonthValue = maskset.buffer.join("").substr(4, 4) + chrs;
							if (dayMonthValue != opts.leapday) return !0;
							var year = parseInt(maskset.buffer.join("").substr(0, 4), 10);
							return year % 4 === 0 ? year % 100 === 0 ? year % 400 === 0 ? !0 : !1 : !0 : !1;
						}
						return isValid;
					},
					cardinality: 2,
					prevalidator: [ {
						validator: function(chrs, maskset, pos, strict, opts) {
							isNaN(maskset.buffer[pos + 1]) || (chrs += maskset.buffer[pos + 1]);
							var frontValue = opts.mask.indexOf("2") == opts.mask.length - 1 ? maskset.buffer.join("").substr(5, 3) : maskset.buffer.join("").substr(0, 3);
							-1 != frontValue.indexOf(opts.placeholder[0]) && (frontValue = "01" + opts.separator);
							var isValid = 1 == chrs.length ? opts.regex.val2pre(opts.separator).test(frontValue + chrs) : opts.regex.val2(opts.separator).test(frontValue + chrs);
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
						if (opts.isInYearRange(chrs, opts.yearrange.minyear, opts.yearrange.maxyear)) {
							var dayMonthValue = maskset.buffer.join("").substr(0, 6);
							if (dayMonthValue != opts.leapday) return !0;
							var year = parseInt(chrs, 10);
							return year % 4 === 0 ? year % 100 === 0 ? year % 400 === 0 ? !0 : !1 : !0 : !1;
						}
						return !1;
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
										opts.isInYearRange(yearPrefix + chrs, opts.yearrange.minyear, opts.yearrange.maxyear)) {
									var dayMonthValue = maskset.buffer.join("").substr(0, 6);
									if (dayMonthValue != opts.leapday) isValid = !0; else {
										var year = parseInt(chrs, 10);
										isValid = year % 4 === 0 ? year % 100 === 0 ? year % 400 === 0 ? !0 : !1 : !0 : !1;
									}
								} else isValid = !1;
								if (isValid) return maskset.buffer[pos - 1] = yearPrefix.charAt(0), maskset.buffer[pos++] = yearPrefix.charAt(1),
									maskset.buffer[pos++] = chrs.charAt(0), {
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
					var escapedSeparator = $.inputmask.escapeRegex.call(this, separator);
					return new RegExp("((0[13-9]|1[012])" + escapedSeparator + "[0-3])|(02" + escapedSeparator + "[0-2])");
				},
				val2: function(separator) {
					var escapedSeparator = $.inputmask.escapeRegex.call(this, separator);
					return new RegExp("((0[1-9]|1[012])" + escapedSeparator + "(0[1-9]|[12][0-9]))|((0[13-9]|1[012])" + escapedSeparator + "30)|((0[13578]|1[02])" + escapedSeparator + "31)");
				},
				val1pre: new RegExp("[01]"),
				val1: new RegExp("0[1-9]|1[012]")
			},
			leapday: "02/29/",
			onKeyUp: function(e) {
				var $input = $(this);
				if (e.ctrlKey && e.keyCode == $.inputmask.keyCode.RIGHT) {
					var today = new Date();
					$input.val((today.getMonth() + 1).toString() + today.getDate().toString() + today.getFullYear().toString());
				}
			}
		},
		"yyyy/mm/dd": {
			mask: "y/1/2",
			placeholder: "yyyy/mm/dd",
			alias: "mm/dd/yyyy",
			leapday: "/02/29",
			onKeyUp: function(e) {
				var $input = $(this);
				if (e.ctrlKey && e.keyCode == $.inputmask.keyCode.RIGHT) {
					var today = new Date();
					$input.val(today.getFullYear().toString() + (today.getMonth() + 1).toString() + today.getDate().toString());
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
						if ("24" == opts.hourFormat && 24 == parseInt(chrs, 10)) return maskset.buffer[pos - 1] = "0",
							maskset.buffer[pos] = "0", {
							refreshFromBuffer: {
								start: pos - 1,
								end: pos
							},
							c: "0"
						};
						var isValid = opts.regex.hrs.test(chrs);
						if (!strict && !isValid && (chrs.charAt(1) == opts.timeseparator || -1 != "-.:".indexOf(chrs.charAt(1))) && (isValid = opts.regex.hrs.test("0" + chrs.charAt(0)))) return maskset.buffer[pos - 1] = "0",
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
							return 24 == tmp ? (maskset.buffer[pos + 5] = "a", maskset.buffer[pos + 6] = "m") : (maskset.buffer[pos + 5] = "p",
								maskset.buffer[pos + 6] = "m"), tmp -= 12, 10 > tmp ? (maskset.buffer[pos] = tmp.toString(),
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
		}
	}), $.fn.inputmask;
}(jQuery), function($) {
	return $.extend($.inputmask.defaults.definitions, {
		A: {
			validator: "[A-Za-zА-яЁёÀ-ÿµ]",
			cardinality: 1,
			casing: "upper"
		},
		"#": {
			validator: "[0-9A-Za-zА-яЁёÀ-ÿµ]",
			cardinality: 1,
			casing: "upper"
		}
	}), $.extend($.inputmask.defaults.aliases, {
		url: {
			mask: "ir",
			placeholder: "",
			separator: "",
			defaultPrefix: "http://",
			regex: {
				urlpre1: new RegExp("[fh]"),
				urlpre2: new RegExp("(ft|ht)"),
				urlpre3: new RegExp("(ftp|htt)"),
				urlpre4: new RegExp("(ftp:|http|ftps)"),
				urlpre5: new RegExp("(ftp:/|ftps:|http:|https)"),
				urlpre6: new RegExp("(ftp://|ftps:/|http:/|https:)"),
				urlpre7: new RegExp("(ftp://|ftps://|http://|https:/)"),
				urlpre8: new RegExp("(ftp://|ftps://|http://|https://)")
			},
			definitions: {
				i: {
					validator: function() {
						return !0;
					},
					cardinality: 8,
					prevalidator: function() {
						for (var result = [], prefixLimit = 8, i = 0; prefixLimit > i; i++) result[i] = function() {
							var j = i;
							return {
								validator: function(chrs, maskset, pos, strict, opts) {
									if (opts.regex["urlpre" + (j + 1)]) {
										var k, tmp = chrs;
										j + 1 - chrs.length > 0 && (tmp = maskset.buffer.join("").substring(0, j + 1 - chrs.length) + "" + tmp);
										var isValid = opts.regex["urlpre" + (j + 1)].test(tmp);
										if (!strict && !isValid) {
											for (pos -= j, k = 0; k < opts.defaultPrefix.length; k++) maskset.buffer[pos] = opts.defaultPrefix[k],
												pos++;
											for (k = 0; k < tmp.length - 1; k++) maskset.buffer[pos] = tmp[k], pos++;
											return {
												pos: pos
											};
										}
										return isValid;
									}
									return !1;
								},
								cardinality: j
							};
						}();
						return result;
					}()
				},
				r: {
					validator: ".",
					cardinality: 50
				}
			},
			insertMode: !1,
			autoUnmask: !1
		},
		ip: {
			mask: "i[i[i]].i[i[i]].i[i[i]].i[i[i]]",
			definitions: {
				i: {
					validator: function(chrs, maskset, pos) {
						return pos - 1 > -1 && "." != maskset.buffer[pos - 1] ? (chrs = maskset.buffer[pos - 1] + chrs,
							chrs = pos - 2 > -1 && "." != maskset.buffer[pos - 2] ? maskset.buffer[pos - 2] + chrs : "0" + chrs) : chrs = "00" + chrs,
							new RegExp("25[0-5]|2[0-4][0-9]|[01][0-9][0-9]").test(chrs);
					},
					cardinality: 1
				}
			}
		},
		email: {
			mask: "*{1,64}[.*{1,64}][.*{1,64}][.*{1,64}]@*{1,64}[.*{2,64}][.*{2,6}][.*{1,2}]",
			greedy: !1,
			onBeforePaste: function(pastedValue) {
				return pastedValue = pastedValue.toLowerCase(), pastedValue.replace("mailto:", "");
			},
			definitions: {
				"*": {
					validator: "[0-9A-Za-z!#$%&'*+/=?^_`{|}~-]",
					cardinality: 1,
					casing: "lower"
				}
			}
		}
	}), $.fn.inputmask;
}(jQuery), function($) {
	return $.extend($.inputmask.defaults.aliases, {
		numeric: {
			mask: function(opts) {
				if (0 !== opts.repeat && isNaN(opts.integerDigits) && (opts.integerDigits = opts.repeat),
						opts.repeat = 0, opts.groupSeparator == opts.radixPoint && (opts.groupSeparator = "." == opts.radixPoint ? "," : "," == opts.radixPoint ? "." : ""),
					" " === opts.groupSeparator && (opts.skipOptionalPartCharacter = void 0), opts.autoGroup = opts.autoGroup && "" != opts.groupSeparator,
					opts.autoGroup && isFinite(opts.integerDigits)) {
					var seps = Math.floor(opts.integerDigits / opts.groupSize), mod = opts.integerDigits % opts.groupSize;
					opts.integerDigits += 0 == mod ? seps - 1 : seps;
				}
				opts.definitions[";"] = opts.definitions["~"];
				var mask = opts.prefix;
				return mask += "[+]", mask += "~{1," + opts.integerDigits + "}", void 0 != opts.digits && (isNaN(opts.digits) || parseInt(opts.digits) > 0) && (mask += opts.digitsOptional ? "[" + (opts.decimalProtect ? ":" : opts.radixPoint) + ";{" + opts.digits + "}]" : (opts.decimalProtect ? ":" : opts.radixPoint) + ";{" + opts.digits + "}"),
					mask += opts.suffix;
			},
			placeholder: "",
			greedy: !1,
			digits: "*",
			digitsOptional: !0,
			groupSeparator: "",
			radixPoint: ".",
			radixFocus: !0,
			groupSize: 3,
			autoGroup: !1,
			allowPlus: !0,
			allowMinus: !0,
			integerDigits: "+",
			prefix: "",
			suffix: "",
			rightAlign: !0,
			decimalProtect: !0,
			postFormat: function(buffer, pos, reformatOnly, opts) {
				var needsRefresh = !1, charAtPos = buffer[pos];
				if ("" == opts.groupSeparator || -1 != $.inArray(opts.radixPoint, buffer) && pos >= $.inArray(opts.radixPoint, buffer) || new RegExp("[-+]").test(charAtPos)) return {
					pos: pos
				};
				var cbuf = buffer.slice();
				charAtPos == opts.groupSeparator && (cbuf.splice(pos--, 1), charAtPos = cbuf[pos]),
					reformatOnly ? cbuf[pos] = "?" : cbuf.splice(pos, 0, "?");
				var bufVal = cbuf.join("");
				if (opts.autoGroup || reformatOnly && -1 != bufVal.indexOf(opts.groupSeparator)) {
					var escapedGroupSeparator = $.inputmask.escapeRegex.call(this, opts.groupSeparator);
					needsRefresh = 0 == bufVal.indexOf(opts.groupSeparator), bufVal = bufVal.replace(new RegExp(escapedGroupSeparator, "g"), "");
					var radixSplit = bufVal.split(opts.radixPoint);
					if (bufVal = radixSplit[0], bufVal != opts.prefix + "?0" && bufVal.length >= opts.groupSize + opts.prefix.length) {
						needsRefresh = !0;
						for (var reg = new RegExp("([-+]?[\\d?]+)([\\d?]{" + opts.groupSize + "})"); reg.test(bufVal); ) bufVal = bufVal.replace(reg, "$1" + opts.groupSeparator + "$2"),
							bufVal = bufVal.replace(opts.groupSeparator + opts.groupSeparator, opts.groupSeparator);
					}
					radixSplit.length > 1 && (bufVal += opts.radixPoint + radixSplit[1]);
				}
				buffer.length = bufVal.length;
				for (var i = 0, l = bufVal.length; l > i; i++) buffer[i] = bufVal.charAt(i);
				var newPos = $.inArray("?", buffer);
				return reformatOnly ? buffer[newPos] = charAtPos : buffer.splice(newPos, 1), {
					pos: newPos,
					refreshFromBuffer: needsRefresh
				};
			},
			onKeyDown: function(e, buffer, caretPos, opts) {
				if (e.keyCode == $.inputmask.keyCode.TAB && "0" != opts.placeholder.charAt(0)) {
					var radixPosition = $.inArray(opts.radixPoint, buffer);
					if (-1 != radixPosition && isFinite(opts.digits)) {
						for (var i = 1; i <= opts.digits; i++) (void 0 == buffer[radixPosition + i] || buffer[radixPosition + i] == opts.placeholder.charAt(0)) && (buffer[radixPosition + i] = "0");
						return {
							refreshFromBuffer: {
								start: ++radixPosition,
								end: radixPosition + opts.digits
							}
						};
					}
				} else if (opts.autoGroup && (e.keyCode == $.inputmask.keyCode.DELETE || e.keyCode == $.inputmask.keyCode.BACKSPACE)) {
					var rslt = opts.postFormat(buffer, caretPos - 1, !0, opts);
					return rslt.caret = rslt.pos + 1, rslt;
				}
			},
			onKeyPress: function(e, buffer, caretPos, opts) {
				if (opts.autoGroup) {
					var rslt = opts.postFormat(buffer, caretPos - 1, !0, opts);
					return rslt.caret = rslt.pos + 1, rslt;
				}
			},
			regex: {
				integerPart: function() {
					return new RegExp("[-+]?\\d+");
				},
				integerNPart: function() {
					return new RegExp("\\d+");
				}
			},
			signHandler: function(chrs, buffer, pos, strict, opts) {
				if (!strict && (opts.allowMinus && "-" === chrs || opts.allowPlus && "+" === chrs)) {
					var matchRslt = buffer.join("").match(opts.regex.integerPart(opts));
					if (matchRslt && matchRslt.length > 0 && "0" !== matchRslt[matchRslt.index]) return buffer[matchRslt.index] == ("-" === chrs ? "+" : "-") ? {
						pos: matchRslt.index,
						c: chrs,
						remove: matchRslt.index,
						caret: pos
					} : buffer[matchRslt.index] == ("-" === chrs ? "-" : "+") ? {
						remove: matchRslt.index,
						caret: pos - 1
					} : {
						pos: matchRslt.index,
						c: chrs,
						caret: pos + 1
					};
				}
				return !1;
			},
			radixHandler: function(chrs, maskset, pos, strict, opts) {
				if (!strict && chrs === opts.radixPoint) {
					var radixPos = $.inArray(opts.radixPoint, maskset.buffer), integerValue = maskset.buffer.join("").match(opts.regex.integerPart(opts));
					if (-1 != radixPos) return maskset.validPositions[radixPos - 1] ? {
						caret: radixPos + 1
					} : {
						pos: integerValue.index,
						c: integerValue[0],
						caret: radixPos + 1
					};
				}
				return !1;
			},
			leadingZeroHandler: function(chrs, maskset, pos, strict, opts) {
				var matchRslt = maskset.buffer.join("").match(opts.regex.integerNPart(opts)), radixPosition = $.inArray(opts.radixPoint, maskset.buffer);
				if (matchRslt && !strict && (-1 == radixPosition || matchRslt.index < radixPosition)) if (0 == matchRslt[0].indexOf("0") && pos >= opts.prefix.length) {
					if (-1 == radixPosition || radixPosition >= pos && void 0 == maskset.validPositions[radixPosition]) return maskset.buffer.splice(matchRslt.index, 1),
						pos = pos > matchRslt.index ? pos - 1 : matchRslt.index, {
						pos: pos,
						remove: matchRslt.index
					};
					if (pos > matchRslt.index && radixPosition >= pos) return maskset.buffer.splice(matchRslt.index, 1),
						pos = pos > matchRslt.index ? pos - 1 : matchRslt.index, {
						pos: pos,
						remove: matchRslt.index
					};
				} else if ("0" == chrs && pos <= matchRslt.index) return !1;
				return !0;
			},
			definitions: {
				"~": {
					validator: function(chrs, maskset, pos, strict, opts) {
						var isValid = opts.signHandler(chrs, maskset.buffer, pos, strict, opts);
						if (!isValid && (isValid = opts.radixHandler(chrs, maskset, pos, strict, opts),
							!isValid && (isValid = strict ? new RegExp("[0-9" + $.inputmask.escapeRegex.call(this, opts.groupSeparator) + "]").test(chrs) : new RegExp("[0-9]").test(chrs),
							isValid === !0 && (isValid = opts.leadingZeroHandler(chrs, maskset, pos, strict, opts),
							isValid === !0)))) {
							var radixPosition = $.inArray(opts.radixPoint, maskset.buffer);
							opts.digitsOptional === !1 && pos > radixPosition && !strict && (isValid = {
								pos: pos,
								remove: pos
							}), isValid = {
								pos: pos
							};
						}
						return isValid;
					},
					cardinality: 1,
					prevalidator: null
				},
				"+": {
					validator: function(chrs, maskset, pos, strict, opts) {
						var isValid = opts.signHandler(chrs, maskset.buffer, pos, strict, opts);
						return isValid || (isValid = opts.allowMinus && "-" == chrs || opts.allowPlus && "+" == chrs),
							isValid;
					},
					cardinality: 1,
					prevalidator: null,
					placeholder: ""
				},
				":": {
					validator: function(chrs, maskset, pos, strict, opts) {
						var isValid = opts.signHandler(chrs, maskset.buffer, pos, strict, opts);
						if (!isValid) {
							var radix = "[" + $.inputmask.escapeRegex.call(this, opts.radixPoint) + "]";
							isValid = new RegExp(radix).test(chrs), isValid && maskset.validPositions[pos] && maskset.validPositions[pos].match.placeholder == opts.radixPoint && (isValid = {
								pos: pos,
								remove: pos
							});
						}
						return isValid;
					},
					cardinality: 1,
					prevalidator: null,
					placeholder: function(opts) {
						return opts.radixPoint;
					}
				}
			},
			insertMode: !0,
			autoUnmask: !1,
			onUnMask: function(maskedValue, unmaskedValue, opts) {
				var processValue = maskedValue.replace(opts.prefix, "");
				return processValue = processValue.replace(opts.suffix, ""), processValue = processValue.replace(new RegExp($.inputmask.escapeRegex.call(this, opts.groupSeparator), "g"), "");
			},
			isComplete: function(buffer, opts) {
				var maskedValue = buffer.join(""), bufClone = buffer.slice();
				if (opts.postFormat(bufClone, 0, !0, opts), bufClone.join("") != maskedValue) return !1;
				var processValue = maskedValue.replace(opts.prefix, "");
				return processValue = processValue.replace(opts.suffix, ""), processValue = processValue.replace(new RegExp($.inputmask.escapeRegex.call(this, opts.groupSeparator), "g"), ""),
					processValue = processValue.replace($.inputmask.escapeRegex.call(this, opts.radixPoint), "."),
					isFinite(processValue);
			},
			onBeforeMask: function(initialValue, opts) {
				if (isFinite(initialValue)) return initialValue.toString().replace(".", opts.radixPoint);
				var kommaMatches = initialValue.match(/,/g), dotMatches = initialValue.match(/\./g);
				return dotMatches && kommaMatches ? dotMatches.length > kommaMatches.length ? (initialValue = initialValue.replace(/\./g, ""),
					initialValue = initialValue.replace(",", opts.radixPoint)) : kommaMatches.length > dotMatches.length && (initialValue = initialValue.replace(/,/g, ""),
					initialValue = initialValue.replace(".", opts.radixPoint)) : initialValue = initialValue.replace(new RegExp($.inputmask.escapeRegex.call(this, opts.groupSeparator), "g"), ""),
					initialValue;
			}
		},
		currency: {
			prefix: "$ ",
			groupSeparator: ",",
			radixPoint: ".",
			alias: "numeric",
			placeholder: "0",
			autoGroup: !0,
			digits: 2,
			digitsOptional: !1,
			clearMaskOnLostFocus: !1,
			decimalProtect: !0
		},
		decimal: {
			alias: "numeric"
		},
		integer: {
			alias: "numeric",
			digits: "0"
		}
	}), $.fn.inputmask;
}(jQuery), function($) {
	return $.extend($.inputmask.defaults.aliases, {
		phone: {
			url: "phone-codes/phone-codes.js",
			maskInit: "+pp(pp)pppppppp",
			mask: function(opts) {
				opts.definitions = {
					p: {
						validator: function() {
							return !1;
						},
						cardinality: 1
					},
					"#": {
						validator: "[0-9]",
						cardinality: 1
					}
				};
				var maskList = [];
				return $.ajax({
					url: opts.url,
					async: !1,
					dataType: "json",
					success: function(response) {
						maskList = response;
					}
				}), maskList = maskList.sort(function(a, b) {
					return (a.mask || a) < (b.mask || b) ? -1 : 1;
				}), maskList.splice(0, 0, opts.maskInit), maskList;
			},
			nojumps: !0,
			nojumpsThreshold: 1
		},
		phonebe: {
			alias: "phone",
			url: "phone-codes/phone-be.js",
			maskInit: "+32(pp)pppppppp",
			nojumpsThreshold: 4
		}
	}), $.fn.inputmask;
}(jQuery), function($) {
	return $.extend($.inputmask.defaults.aliases, {
		Regex: {
			mask: "r",
			greedy: !1,
			repeat: "*",
			regex: null,
			regexTokens: null,
			tokenizer: /\[\^?]?(?:[^\\\]]+|\\[\S\s]?)*]?|\\(?:0(?:[0-3][0-7]{0,2}|[4-7][0-7]?)?|[1-9][0-9]*|x[0-9A-Fa-f]{2}|u[0-9A-Fa-f]{4}|c[A-Za-z]|[\S\s]?)|\((?:\?[:=!]?)?|(?:[?*+]|\{[0-9]+(?:,[0-9]*)?\})\??|[^.?*+^${[()|\\]+|./g,
			quantifierFilter: /[0-9]+[^,]/,
			isComplete: function(buffer, opts) {
				return new RegExp(opts.regex).test(buffer.join(""));
			},
			definitions: {
				r: {
					validator: function(chrs, maskset, pos, strict, opts) {
						function regexToken(isGroup, isQuantifier) {
							this.matches = [], this.isGroup = isGroup || !1, this.isQuantifier = isQuantifier || !1,
								this.quantifier = {
									min: 1,
									max: 1
								}, this.repeaterPart = void 0;
						}
						function analyseRegex() {
							var match, m, currentToken = new regexToken(), opengroups = [];
							for (opts.regexTokens = []; match = opts.tokenizer.exec(opts.regex); ) switch (m = match[0],
								m.charAt(0)) {
								case "(":
									opengroups.push(new regexToken(!0));
									break;

								case ")":
									var groupToken = opengroups.pop();
									opengroups.length > 0 ? opengroups[opengroups.length - 1].matches.push(groupToken) : currentToken.matches.push(groupToken);
									break;

								case "{":
								case "+":
								case "*":
									var quantifierToken = new regexToken(!1, !0);
									m = m.replace(/[{}]/g, "");
									var mq = m.split(","), mq0 = isNaN(mq[0]) ? mq[0] : parseInt(mq[0]), mq1 = 1 == mq.length ? mq0 : isNaN(mq[1]) ? mq[1] : parseInt(mq[1]);
									if (quantifierToken.quantifier = {
											min: mq0,
											max: mq1
										}, opengroups.length > 0) {
										var matches = opengroups[opengroups.length - 1].matches;
										if (match = matches.pop(), !match.isGroup) {
											var groupToken = new regexToken(!0);
											groupToken.matches.push(match), match = groupToken;
										}
										matches.push(match), matches.push(quantifierToken);
									} else {
										if (match = currentToken.matches.pop(), !match.isGroup) {
											var groupToken = new regexToken(!0);
											groupToken.matches.push(match), match = groupToken;
										}
										currentToken.matches.push(match), currentToken.matches.push(quantifierToken);
									}
									break;

								default:
									opengroups.length > 0 ? opengroups[opengroups.length - 1].matches.push(m) : currentToken.matches.push(m);
							}
							currentToken.matches.length > 0 && opts.regexTokens.push(currentToken);
						}
						function validateRegexToken(token, fromGroup) {
							var isvalid = !1;
							fromGroup && (regexPart += "(", openGroupCount++);
							for (var mndx = 0; mndx < token.matches.length; mndx++) {
								var matchToken = token.matches[mndx];
								if (1 == matchToken.isGroup) isvalid = validateRegexToken(matchToken, !0); else if (1 == matchToken.isQuantifier) {
									var crrntndx = $.inArray(matchToken, token.matches), matchGroup = token.matches[crrntndx - 1], regexPartBak = regexPart;
									if (isNaN(matchToken.quantifier.max)) {
										for (;matchToken.repeaterPart && matchToken.repeaterPart != regexPart && matchToken.repeaterPart.length > regexPart.length && !(isvalid = validateRegexToken(matchGroup, !0)); ) ;
										isvalid = isvalid || validateRegexToken(matchGroup, !0), isvalid && (matchToken.repeaterPart = regexPart),
											regexPart = regexPartBak + matchToken.quantifier.max;
									} else {
										for (var i = 0, qm = matchToken.quantifier.max - 1; qm > i && !(isvalid = validateRegexToken(matchGroup, !0)); i++) ;
										regexPart = regexPartBak + "{" + matchToken.quantifier.min + "," + matchToken.quantifier.max + "}";
									}
								} else if (void 0 != matchToken.matches) for (var k = 0; k < matchToken.length && !(isvalid = validateRegexToken(matchToken[k], fromGroup)); k++) ; else {
									var testExp;
									if ("[" == matchToken.charAt(0)) {
										testExp = regexPart, testExp += matchToken;
										for (var j = 0; openGroupCount > j; j++) testExp += ")";
										var exp = new RegExp("^(" + testExp + ")$");
										isvalid = exp.test(bufferStr);
									} else for (var l = 0, tl = matchToken.length; tl > l; l++) if ("\\" != matchToken.charAt(l)) {
										testExp = regexPart, testExp += matchToken.substr(0, l + 1), testExp = testExp.replace(/\|$/, "");
										for (var j = 0; openGroupCount > j; j++) testExp += ")";
										var exp = new RegExp("^(" + testExp + ")$");
										if (isvalid = exp.test(bufferStr)) break;
									}
									regexPart += matchToken;
								}
								if (isvalid) break;
							}
							return fromGroup && (regexPart += ")", openGroupCount--), isvalid;
						}
						null == opts.regexTokens && analyseRegex();
						var cbuffer = maskset.buffer.slice(), regexPart = "", isValid = !1, openGroupCount = 0;
						cbuffer.splice(pos, 0, chrs);
						for (var bufferStr = cbuffer.join(""), i = 0; i < opts.regexTokens.length; i++) {
							var regexToken = opts.regexTokens[i];
							if (isValid = validateRegexToken(regexToken, regexToken.isGroup)) break;
						}
						return isValid;
					},
					cardinality: 1
				}
			}
		}
	}), $.fn.inputmask;
}(jQuery);

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
