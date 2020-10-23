"use strict";
_H.RSYa = {
	__proto__: null,

	init: function addRSYa() {
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

		//* Удаляем скрытые
		this['$pars'] = this['$pars'].filter((ind, i) => !i.closest || !i.closest('[hidden]'));

		Object.defineProperties(this, {
			init : { enumerable: false},
			$adBlock : { enumerable: false, writable: true},
			$pars : { enumerable: false},
		});

		// if (!Object.keys(this).length) return;

		//* Создаем рекламный блок после случайного <p> и наполняем его
		// this.$adBlock = $('<div id="y'+(Math.random()*1000+2000)+'">').insertAfter($.rnd(this['$pars']));
		this.$adBlock = $('<div id="yandex_rtb_R-A-486456-1"/>').insertAfter($.rnd(this['$pars']));

		console.log("this = ", this, this.$adBlock, this.$adBlock[0].id);

		// *Yandex.RTB R-A-486456-1
		$(window).on('pageshow', function(w, d, n, s, t) {
			w= window;
			d= document;
			n= "yandexContextAsyncCallbacks";

			w[n] = w[n] || [];
			w[n].push(function() {
				Ya.Context.AdvManager.render({
						blockId: "R-A-486456-1",
						// renderTo: this.$adBlock[0].id,
						renderTo: _H.RSYa.$adBlock[0].id,
						async: true
				});
			});

			console.log(_H.RSYa.$adBlock);

			t = d.getElementsByTagName("script")[0];
			s = d.createElement("script");
			s.type = "text/javascript";
			s.src = "//an.yandex.ru/system/context.js";
			s.async = true;
			if(t){
				t.parentNode.insertBefore(s, t);
			}
			else{
				d.head.appendChild(s);
			}
	});
	// (window, window.document, "yandexContextAsyncCallbacks");


		// Отлов блокировщиков
		setTimeout(function() {
			// console.log(this['$img']);
			var $anAD;
			if (($anAD = that.$adBlock.closest('[hidden]')).length) {
				console.log(
					"$anAD = ", $anAD
				);
				$anAD[0].hidden = false;
				$anAD.css('display', 'block');

				$anAD.closest('div').append('<div/>', { class: 'core message' }).html('<h6 style="margin:0;">Друзья! У меня на сайте нет навязчивой рекламы.</h6> <p>Более того, вся она может вам пригодиться, поскольку я сам вручную отбираю своих рекламных партнеров, а не доверяю автоматическим роботам. Все мои рекламные партнеры - фирмы с хорошей репутацией.</p><p>Если вы отключите блокировщик рекламы, возможно, вы сможете помочь и себе, и этому сайту.</p>');
			}
			else console.info(
				"Реклама не блокируется"
				// , $('.Adv')
			);
		}, 1500);
	}
}; //== /_H.RSYa
///////////////////////////////////

_H.defer.add(_H.RSYa.init.bind(_H.RSYa));