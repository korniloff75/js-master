
/** KorniloFF
* //js-master.ru
* Изменение стандартного CHECKBOX
*
* $(selector).CheckBoxes();
*/

$.fn.CheckBoxes = function (checkboxStyles) {
	"use strict";

	// Default settings
	checkboxStyles = Object.assign({
		width: '22px',
		boxShadow: '0 2px 4px 1px rgba(0, 0, 0, 0.3) inset, 0 1px 0 #ffffff, 0 1px 0 #ffffff inset',
		border: '1px solid #bbb', borderRadius: '30px',
		position: 'relative', top: '3px', display: 'inline-block', cursor: 'pointer'
	}, checkboxStyles || {});

	var st = 'radial-gradient(40% 35%, #cde, #bbb 60%)', //== Отключенный
		stCh = 'radial-gradient(40% 35%, #5aef5a, #25d025 60%)', //== Включенный
		title = "Выбрать";

	checkboxStyles.height = checkboxStyles.height || checkboxStyles.width;

	function checkbox (ch) {
		checkboxStyles.background= !ch? st: stCh;
		checkboxStyles.borderRadius= parseInt(checkboxStyles.width)/2 + 'px';
		this.css(checkboxStyles);
	};

	// console.log("this = ", this);

	this.find('input[type=checkbox]').each( function(ind,i) {
		var $i = $(i);
		i.checked = i.checked || $i.attr('checked');

		var $p = $i.hasClass('chb') ? $i.parent() : $i.wrap('<label title = "' + title + '" />').parent();

		checkbox.call($p, i.checked);

		if ($i.hasClass('chb')) return;

		i.hidden= true;
		$i.addClass('chb');

		$p.on('change', function() {
			checkbox.call($p, i.checked)
		});
	}); // find

};