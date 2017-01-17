/*!
* Parsleyjs
* Guillaume Potier - <guillaume@wisembly.com>
* Version 2.2.0-rc2 - built Tue Oct 06 2015 10:20:13
* MIT Licensed
*
*/
!(function (factory) {
  if (typeof define === 'function' && define.amd) {
    // AMD. Register as an anonymous module depending on jQuery.
    define(['jquery'], factory);
  } else if (typeof exports === 'object') {
    // Node/CommonJS
    module.exports = factory(require('jquery'));
  } else {
    // Register plugin with global jQuery object.
    factory(jQuery);
  }
}(function ($) {
  // small hack for requirejs if jquery is loaded through map and not path
  // see http://requirejs.org/docs/jquery.html
  if ('undefined' === typeof $ && 'undefined' !== typeof window.jQuery)
    $ = window.jQuery;
// ParsleyConfig definition if not already set
window.ParsleyConfig = window.ParsleyConfig || {};
window.ParsleyConfig.i18n = window.ParsleyConfig.i18n || {};
// Define then the messages
window.ParsleyConfig.i18n.sq = jQuery.extend(window.ParsleyConfig.i18n.sq || {}, {
  defaultMessage: "Kjo vlere eshte e pasakte.",
  type: {
    email:        "Duhet te jete nje email i vlefshem.",
    url:          "Duhet te jete nje URL e vlefshme.",
    number:       "Duhet te jete numer.",
    integer:      "Kjo vlere duhet te jete integer.",
    digits:       "Kjo vlere duhet te permbaje digit.",
    alphanum:     "Kjo vlere duhet te permbaje vetel alphanumeric."
  },
  notblank:       "Nuk mund te lihet bosh.",
  required:       "Eshte e detyrueshme.",
  pattern:        "Kjo vlere eshte e pasakte.",
  min:            "Duhet te jete me e madhe ose baraz me %s.",
  max:            "Duhet te jete me e vogel ose baraz me %s.",
  range:          "Duhet te jete midis %s dhe %s.",
  minlength:      "Kjo vlere eshte shume e shkurter. Ajo duhet te permbaje min %s karaktere.",
  maxlength:      "Kjo vlere eshte shume e gjate. Ajo duhet te permbaje max %s karaktere.",
  length:         "Gjatesia e kesaj vlere eshte e pasakte. Ajo duhet te jete midis %s dhe %s karakteresh.",
  mincheck:       "Ju duhet te zgjidhni te pakten %s vlere.",
  maxcheck:       "Ju duhet te zgjidhni max %s vlera.",
  check:          "Ju mund te zgjidhni midis %s dhe %s vlerash.",
  equalto:        "Kjo vlere duhet te jete e njejte."
});
// If file is loaded after Parsley main file, auto-load locale
if ('undefined' !== typeof window.ParsleyValidator)
  window.ParsleyValidator.addCatalog('sq', window.ParsleyConfig.i18n.sq, true);
}));