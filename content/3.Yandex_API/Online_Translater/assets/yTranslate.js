"use strict";

/**
* without lang panel
* $('.content').yTranslate('ru-en');
*/

$.fn.yLngs = function($node, cur) {
	if(!$node instanceof jQuery)
		$node = $($node);
	cur = cur || 'ru-en';
	var $el = this,
		$langs= $el.cr('select',{id:'langs', size:1, style:'color:#123; margin: 0 5px 0;', class: 'button'});

	// console.log('$el = ', $el, (!$node instanceof jQuery));

	function translate () {
		$node.yTranslate($langs.val());
	}

	return $.get('https://translate.yandex.net/api/v1.5/tr.json/getLangs?key=' + _H.api.yTranslate + '&ui=en', 'json').done(function(response) {
		// console.log(response, response.toString());

		// handler(response);

		$langs.cr('input',{type:'button', class: 'button', value:'Translate'}, 'after')
		.on('click', translate);

		response.dirs.forEach(function(i) {
			var l= i.split('-');
			if (l[0]!=='ru') return;
			$langs.cr('option',{value:i}).text(response.langs[l[0]] + ' - ' + response.langs[l[1]]);
		}) ;
		$langs.val(cur);
	});
}


$.fn.yTranslate = function (lang,format) {
	lang= lang || 'en'; format= format || 'plain';
	// console.log('yT = ', this);

	var el = this[0],
	url= 'https://translate.yandex.net/api/v1.5/tr.json/translate?key=' + _H.api.yTranslate + '&lang=' + lang  + '&format=' + format;

	el.cont= el.cont || (el.innerHTML);
	el.cont = el.cont.replace(/<script.+?\/script>/gi,'');

	$.post(url, {
		text: el.cont
	}, 'json').done(function(response) {
		if(response.code !== 200)
			return console.error('code - ' + response.code, response);
		if(response.toString() !== '[object Object]')
			response = JSON.parse(response);

		el.innerHTML = response.text[0] + "\n<p><a href=\"http://translate.yandex.ru/\" target=\"_blank\">Переведено „Яндекс.Переводчиком“</a></p>";
	});

}