"use strict";
_H.ADs = {
	__proto__: null,
	AliExpress: {
		// uri: '/',
		base: 'https://alitems.com',
		alt: 'Aliexpress INT',
		links: [
			'3pl9pni30ga4ec867dbe16525dc3e8',
			'nc8nd50jlda4ec867dbe16525dc3e8',
			'jh0df3sloba4ec867dbe16525dc3e8',
			'6qq5igyqyfa4ec867dbe16525dc3e8',
			'ugv2rqjiica4ec867dbe16525dc3e8',
		]
	},
	/* https://ad.admitad.com/g/npsw06cnp5a4ec867dbe369ea22811/?i=4
	<!-- admitad.banner: 7n27paepuva4ec867dbefcdd16745e Samsung [CPS] IN -->
<a target="_blank" rel="nofollow" href="https://ad.admitad.com/g/7n27paepuva4ec867dbefcdd16745e/?i=4"><img width="160" height="600" border="0" src="https://ad.admitad.com/b/7n27paepuva4ec867dbefcdd16745e/" alt="Samsung [CPS] IN"/></a>
<!-- /admitad.banner -->
	 */
	Samsung: {
		uri: 'content',
		alt: 'Samsung [CPS] IN',
		links: [
			'7n27paepuva4ec867dbefcdd16745e',
		]
	},

	Timeweb: { //== Хост
		uri: 'Javascripts|content',
		alt: 'хостинг Timeweb',
		links: [
			'n6q5j342fca4ec867dbe5fb557f5d8', 'tpflk1dgaga4ec867dbe5fb557f5d8', 'jsqzs4datla4ec867dbe5fb557f5d8', 'nhp2d1t7mga4ec867dbe5fb557f5d8', 'duvxj343x9a4ec867dbe5fb557f5d8', 'o98bdksin1a4ec867dbe5fb557f5d8'
		]
	},
	/*
	<!-- admitad.banner: eq0fuuj113a4ec867dbe8753afd1f1 Letyshops [lifetime] -->
	<a target="_blank" rel="nofollow" href="https://katuhus.com/g/eq0fuuj113a4ec867dbe8753afd1f1/?i=4"><img width="468" height="60" border="0" src="https://ad.admitad.com/b/eq0fuuj113a4ec867dbe8753afd1f1/" alt="Letyshops [lifetime]"/></a>
	<!-- /admitad.banner -->
	*/
	Magzter: {
		uri: 'content',
		alt: 'сервис Magzter [CPS] IN',
		src: 'https://www.magzter.com/static/images/maglogo/magzlogosm.png',
		links: [
			'6zlx8gln2ua4ec867dbe03fc6030ed',
		]
	},
	Letyshops: { //== Дисконт
		uri: 'Primery|content',
		alt: 'кэшбэк Letyshops',
		base: 'https://homyanus.com',
		links: [
			'nkoywaphvra4ec867dbe8753afd1f1',
			'w27poh5mmla4ec867dbe8753afd1f1',
			'koujs74zmya4ec867dbe8753afd1f1',
			'eq0fuuj113a4ec867dbe8753afd1f1',
		]
	},
	Tea101: {
		uri: 'content',
		base: null,
		alt: 'магазин 500 видов чая',
		links: [
			'ipw0vli5fua4ec867dbed55ad7d85a', '8aq5xn9ydsa4ec867dbed55ad7d85a', 'xs3x94yw7ga4ec867dbed55ad7d85a', 'n692sbotrva4ec867dbed55ad7d85a',
		],
	},

	// https://linkslot.ru/account.php
	LinkSlot: '<center><div style="overflow: hidden;"><div style="display:inline-block;"><a href="https://linkslot.ru/link.php?id=273742" target="_blank" rel="noopener">Купить ссылку здесь за <span id="linprice_273742"></span> руб.</a></div><div style="display:inline-block; margin: 0 10px;" id="linkslot_273742"><script src="https://linkslot.ru/lincode.php?id=273742" async></script></div><div style="display:inline-block;"><a href="https://linkslot.ru/?ref=KorniloFF" target="_blank" rel="noopener">Поставить к себе на сайт</a></div></center>',

	init: function addContent() {
		var
			that = this,
			area = $('body article, body #sidebar')[0],
			// Exceptions
			noAD = /Sajt_dlya_slabovidyashhix\/(PRO|Lite)|Rekvizity/i,
			parsMin = 5, interval = 3;

		// console.log(this);
		this['$pars'] = $(area).find('p');

		if (
			//== Собираем все <p> в area. Если их меньше, чем parsMin - уходим
			this['$pars'].length < parsMin
			|| !area || !/js\-/i.test(location.host)
			|| noAD.test(location.href)
		) return;
		/* console.log(
			area,
			'this['$pars'] = ', this['$pars']
		); */

		// Удаляем скрытые
		this['$pars'] = this['$pars'].filter((ind, i) => !i.closest || !i.closest('[hidden]'));

		//== Удаляем из _H.ADs свойства, не соответствующие uri
		Object.keys(this).forEach(function (k) {
			if (this[k].uri && !new RegExp(this[k].uri, 'i').test(decodeURIComponent(location.href))) {
				delete this[k];
			}
		}, this);

		Object.defineProperties(this, {
			init : { enumerable: false},
			$img : { enumerable: false, writable: true},
			$pars : { enumerable: false},
			LinkSlot : { enumerable: false},
		});
		// console.log("this = ", this);

		if (!Object.keys(this).length) return;

		//== Выбираем случайный элемент из _H.ADs, берем из него случайный links и конструируем ссылку и баннер
		var sel = this[$.rnd(Object.keys(this))],
			r = $.rnd(sel.links),
			g = (sel.base || 'https://ad.admitad.com') + '/g/' + r + '/?i=4',
			b = sel.src || (sel.base || 'https://ad.admitad.com') + '/b/' + r + '/';

		/* console.log(
			'sel = ', sel,
			'block = ', block
			); */

		//== Создаем рекламный блок после случайного <p> и наполняем его
		this['$img'] = $($.rnd(this['$pars'])).cr('div', { class: 'Adv center pointer' }, 'after').cr('img', { src: b, alt: sel.alt });
		this['$img'].parent().on('click', e => location.href = g);
		this['$img'].parent().after(this.LinkSlot);

		// Отлов блокировщиков
		setTimeout(function() {
			// console.log(this['$img']);
			var $anAD;
			if (($anAD = that['$img'].closest('[hidden]')).length) {
				console.log(
					"$anAD = ", $anAD
				);
				$anAD[0].hidden = false;
				$anAD.css('display', 'block');

				$anAD.closest('div').cr('div', { class: 'core message' }).html('<h6 style="margin:0;">Друзья! У меня на сайте нет навязчивой рекламы.</h6> <p>Более того, вся она может вам пригодиться, поскольку я сам вручную отбираю своих рекламных партнеров, а не доверяю автоматическим роботам. Все мои рекламные партнеры - фирмы с хорошей репутацией.</p><p>Если вы отключите блокировщик рекламы, возможно, вы сможете помочь и себе, и этому сайту.</p>');
			}
			else console.info(
				"Реклама не блокируется"
				// , $('.Adv')
			);
		}, 1500);
	}
}; //== /_H.ADs
///////////////////////////////////
_H.defer.add(_H.ADs.init.bind(_H.ADs));
