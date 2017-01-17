/* To avoid CSS expressions while still supporting IE 7 and IE 6, use this script */
/* The script tag referencing this file must be placed before the ending body tag. */

/* Use conditional comments in order to target IE 7 and older:
	<!--[if lt IE 8]><!-->
	<script src="ie7/ie7.js"></script>
	<!--<![endif]-->
*/

(function() {
	function addIcon(el, entity) {
		var html = el.innerHTML;
		el.innerHTML = '<span style="font-family: \'cfont\'">' + entity + '</span>' + html;
	}
	var icons = {
		'cfont-credit-card': '&#xf09d;',
		'cfont-slack': '&#xf198;',
		'cfont-envelope-square': '&#xf199;',
		'cfont-wordpress': '&#xf19a;',
		'cfont-ra': '&#xf1d0;',
		'cfont-rebel': '&#xf1d0;',
		'cfont-resistance': '&#xf1d0;',
		'cfont-paypal': '&#xf1ed;',
		'cfont-cc-visa': '&#xf1f0;',
		'cfont-cc-mastercard': '&#xf1f1;',
		'cfont-cc-discover': '&#xf1f2;',
		'cfont-cc-amex': '&#xf1f3;',
		'cfont-cc-paypal': '&#xf1f4;',
		'cfont-cc-stripe': '&#xf1f5;',
		'0': 0
		},
		els = document.getElementsByTagName('*'),
		i, c, el;
	for (i = 0; ; i += 1) {
		el = els[i];
		if(!el) {
			break;
		}
		c = el.className;
		c = c.match(/cfont-[^\s'"]+/);
		if (c && icons[c[0]]) {
			addIcon(el, icons[c[0]]);
		}
	}
}());
