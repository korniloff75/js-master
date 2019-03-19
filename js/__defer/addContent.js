"use strict";
$.addContent = function() {
	var ADs= {
		__proto__: null,
		AliExpress: {
			uri: '/',
//			link: '<a href="" target="_blank" rel="nofollow">'+ADs.title+'</a>',
			base: 'https://alitems.com',
			alt: 'Aliexpress INT',

			links: [
				'f84p7uuriqa4ec867dbe16525dc3e8',
				'ea128ec2b1a4ec867dbe16525dc3e8',
				'pw950vjs7pa4ec867dbe16525dc3e8'
			]
		},

		Tinydeal: {
			/* https://www.admitad.com/ru/webmaster/websites/572937/offers/6420/?start_date=14.09.2015&end_date=24.06.2018&page=6&order_by=-ecpc&rpp=20&region=RU#banners */
			uri: '/',
			alt: 'Tinydeal',
			links: [
				'yaak6ligwua4ec867dbe91a72d4870',
				'ke86kpzv49a4ec867dbe91a72d4870',
				'k697k16odqa4ec867dbe91a72d4870',
				'r1rxe4e551a4ec867dbe91a72d4870'
			]
		},

/*

 */

		LPgenerator: { //== Landing Page
			uri: 'Javascripts|Веб-мастеру',
			alt: 'LPgenerator',
			links: [
				'6k4nkydf6ga4ec867dbe369ea22811','z5nk7xfzmpa4ec867dbe369ea22811','npsw06cnp5a4ec867dbe369ea22811',
				'gk0v656qgva4ec867dbe369ea22811','iz3ko6iy3ra4ec867dbe369ea22811','vj5jskl69ea4ec867dbe369ea22811'
			]

		},

		Timeweb: { //== Хост
			uri: 'Javascripts|Веб-мастеру|PHP',
			alt: 'Timeweb',
			links: [
				'n6q5j342fca4ec867dbe5fb557f5d8','tpflk1dgaga4ec867dbe5fb557f5d8','jsqzs4datla4ec867dbe5fb557f5d8','nhp2d1t7mga4ec867dbe5fb557f5d8','duvxj343x9a4ec867dbe5fb557f5d8','o98bdksin1a4ec867dbe5fb557f5d8'
			]

		},

		Store77 : { // Бытовая техника, электроника
			uri: 'Windows|Информ-раздел|Javascripts',
			alt : 'Store77',
			links: [
				'uil6q6hkh5a4ec867dbebe0600316f',
				'dosyz70l67a4ec867dbebe0600316f',
				'p5t8jytw40a4ec867dbebe0600316f',
				's6yv5n1w3ia4ec867dbebe0600316f',
				'dllbj2lr5za4ec867dbebe0600316f',
				'npz4r7vjuha4ec867dbebe0600316f',
				'ofmv7gu1x0a4ec867dbebe0600316f',
				'u485l8vnr8a4ec867dbebe0600316f',
				'mf8tvnwp4za4ec867dbebe0600316f'
			]
		},

/*

*/

/*
		Letyshops: { //== Дисконт
			uri: 'Javascripts',
			alt: 'Letyshops',
			base: 'https://homyanus.com',
			links: [
				'nxezo4ieo1a4ec867dbe8753afd1f1'
			]

		},
*/

		Tea101: {
			uri: 'Веб-мастеру|Информ-раздел',
			base: null,
			alt: '500 видов чая',
			links: [
				'ipw0vli5fua4ec867dbed55ad7d85a','8aq5xn9ydsa4ec867dbed55ad7d85a','xs3x94yw7ga4ec867dbed55ad7d85a','n692sbotrva4ec867dbed55ad7d85a'

			],
		}
	}; //== /ADs
	///////////////////////////////////

//== Собираем все <p> в <article>. Если их меньше, чем parsMin - уходим
	var area = $('body article, body #sidebar')[0];

	if(!area || !/js\-/i.test(location.host)) return;
	var $pars= $(area).find('p'),
		parsMin= 5, interval= 3;
	if ($pars.length < parsMin) return;


	//== Удаляем из ADs свойства, не соответствующие uri
	console.log(
		// decodeURIComponent(location.href)
	);
	Object.keys(ADs).forEach(function(k) {
		if(!new RegExp(ADs[k].uri,'i').test(decodeURIComponent(location.href))) {
			console.log(
				decodeURIComponent(location.href)
			);
			delete ADs[k];
		}

	});

	console.log(
		area,
		ADs
	);

	if(!Object.keys(ADs).length) return;


//== Выбираем случайный элемент из ADs, берем из него случайный links и конструируем ссылку и баннер
	var sel= ADs[$.rnd(Object.keys(ADs))],
		r= $.rnd(sel.links),
		g= (sel.base || 'https://ad.admitad.com') +'/g/'+ r + '/?i=4',
		b= (sel.base || 'https://ad.admitad.com') +'/b/'+ r +'/';
	//== Создаем рекламный блок после случайного <p> и наполняем его
	console.log(
		'sel = ', sel,
		'$pars = ', $pars,
		$.rnd($pars).after("<div class=\"Adv\"><a href=\"${g}\"><img src=\"${b}\" /></a></div>")
	);

	// $($pars.rnd).cr('div',{class:'Adv'}, 'after').cr('a',{href:g, target:'_blank', rel:'nofollow'}).cr('img',{src:b,alt:sel.alt});



	$(function() {
		var $img = $('.Adv img');
		if($img.prop('hidden')) $pars.rnd.cr('div', {class:'warning'},'after').innerHTML= '<p>Друзья! У меня на сайте нет навязчивой рекламы.</p> <p>Более того, вся она может вам пригодиться, поскольку я сам вручную отбираю своих рекламных партнеров, а не доверяю автоматическим роботам. Все мои рекламные партнеры - фирмы с хорошей репутацией.</p><p>Если вы отключите блокировщик рекламы, возможно, вы сможете помочь и себе, и этому сайту.</p>'
	})
};

$.addContent();