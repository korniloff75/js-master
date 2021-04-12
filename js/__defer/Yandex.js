"use strict";
_H.RSYa = {
	__proto__: null,

	prepare: function(w, d, n, s, t) {
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
		console.log({s});
	},

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
			|| !area
			|| noAD.test(location.href)
			//* Удаляем скрытые
			|| (this['$pars'] = this['$pars'].filter((ind, i) => !i.closest || !i.closest('[hidden]'))) && !this['$pars'].length
		) return;
		/* console.log(
			area,
			'this['$pars'] = ', this['$pars']
		); */


		Object.defineProperties(this, {
			init : { enumerable: false},
			$adBlock : { enumerable: false, writable: true},
			$pars : { enumerable: false},
		});

		// if (!Object.keys(this).length) return;

		//* Создаем рекламный блок после случайного <p> и наполняем его
		// this.$adBlock = $('<div id="y'+(Math.random()*1000+2000)+'">').insertAfter($.rnd(this['$pars']));
		this.$adBlock = $('<div id="yandex_rtb_R-A-486456-1"/>').insertAfter($.rnd(this['$pars']));

		console.log("this = ", this, "\nthis.$adBlock= ", this.$adBlock, this.$adBlock[0].id);

		// *Yandex.RTB R-A-486456-1
		$(window).on('load', this.prepare);
	// (window, window.document, "yandexContextAsyncCallbacks");


		// Отлов блокировщиков
		setTimeout(function() {
			// console.log(this['$img']);
			var $anAD;
			if (
				($anAD = that.$adBlock.closest('[hidden]')).length
				// || !that.$adBlock.html()
			) {
				$anAD= $anAD.length? $anAD : that.$adBlock;

				console.log(
					"$anAD = ", $anAD,
					"\n$anAD.closest('div') = ", $anAD.closest('div:not([id])'),
				);

				$anAD[0].hidden = false;
				$anAD.css('display', 'block');

				$('<div/>', { class: 'core message' })
				.appendTo($anAD.closest('div:not([id])'))
				.html('<h6 style="margin:0;">Друзья! У меня на сайте нет навязчивой рекламы.</h6> <p>Она выводится единым блоком в странице и помогает существовать этому сайту. Пожалуйста, добавьте адрес сайта в исключения блокировщика рекламы.</p>');
			}
			else console.info(
				"Реклама не блокируется"
				// , $('.Adv')
			);
		}, 1500);
	}
}; //_H.RSYa


_H.defer.add(_H.RSYa.init.bind(_H.RSYa));