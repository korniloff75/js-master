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

	init: function addRSYa(area) {
		area = area || 'body .content'
		if(!(area instanceof HTMLElement)){
			area= document.querySelector(area);
		}

		var
			that = this,
			// Exceptions
			noAD = /Sajt_dlya_slabovidyashhix\/(PRO|Lite)|Rekvizity|feedback/i,
			parsMin = 5;

		// console.log(this);


		if (
			!area
			//* Собираем все <p> в area и удаляем скрытые. Если их меньше, чем parsMin - уходим
			|| (this['$pars'] = $(area).find('p').filter((ind, i) => !i.closest || !i.closest('[hidden]'))) && this['$pars'].length < parsMin
			|| noAD.test(location.href)
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

		var rndNum= Math.floor(parsMin - 1 + Math.random() * (this['$pars'].length - parsMin)),
			rnd= this['$pars'][rndNum];

		// if (!Object.keys(this).length) return;

		console.log({rndNum,rnd});

		//* Создаем рекламный блок после случайного <p> и наполняем его
		// this.$adBlock = $('<div id="y'+(Math.random()*1000+2000)+'">').insertAfter($.rnd(this['$pars']));
		this.$adBlock = $('<div id="yandex_rtb_R-A-486456-1"/>').insertAfter(rnd);

		console.log("this = ", this, "\nthis.$adBlock= ", this.$adBlock, this.$adBlock[0].id);

		// *Yandex.RTB R-A-486456-1
		// $(window).on('load', this.prepare);
		this.prepare();


		// todo Отлов блокировщиков
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
	}//init()
	
}; //_H.RSYa


_H.defer.add(_H.RSYa.init.bind(_H.RSYa));