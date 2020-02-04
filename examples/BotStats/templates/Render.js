'use strict';

//* use jQ
var Render = function Render($bot_box, $chartbox, $main)
{
	Object.assign(this, {
		//* Блок с ботами
		$bot_box: $bot_box || _g.$bot_box,
		//* Блок с графиками
		$chartbox: $chartbox || _g.$chartbox,
		//* Общий блок
		$main: $main || _g.$main,
		data: {},
		DEFAULT_TXT: '<p>Данные по этому боту в настоящий момент отсутствуют. Попробуйте позже.</p>',
		CHART_NAMES: {
			msgs: 'Статистика сообщений',
			chats: 'Статистика чатов',
			users: 'Статистика пользователей',
		},
		STYLES: `button {box-sizing:border-box}
		button.uk-button {margin:.5em; margin-left:0;}
		.menu>button:hover {box-shadow: #79a 1px -1px 2px -1px;}`,
	});


	this.create = function () {
		this.BTNS= [
			{
				html: '<button class="settings" style="color:#eee;background:red;">Настройки</button>',
				event: this.getSettings,
			},
			{
				html: '<button class="stat" style="color:#eee;background:#008080;">Статистика</button>',
				event: this.getCharts,
			},
			{
				html: '<button class="servs" style="color:#111;background:#6cf;">Сервисы</button>',
				event: null,
			},
		];

		$('<style/>').insertBefore($bot_box)
		.text(this.STYLES);

		this.$main.on('select', ()=>false);

		_bots.forEach((info, ind) => {
			//* create BTNS
			// var $curBox = $(`<li/>`).appendTo(this.$bot_box);
			var $title_block = $(`<div class="uk-accordion-title"/>`).appendTo(this.$bot_box);

			$title_block[0].info = info;

			var $name_block = $(`<h4>${info.name}</h4>
			<div>PRO ID ${info.bot_id} Статистика ${info.min_date? 'доступна с '+info.min_date : 'не доступна'}</div>
			`).appendTo($title_block),

				$but_block = $(`<div class="menu"/>`).appendTo($title_block);

			this.BTNS.forEach(btn=>{
				var $btn = $(btn.html).appendTo($but_block);
				$btn.addClass('uk-button');
				$btn[0].event= btn.event;

				// console.log('$btn = ', $btn);
			});

			// console.log('margin= ', UIkit.margin($but_block, {margin: 'uk-margin'}));

			//* wraper needed 4 accordion
			$title_block.wrap('<div>');

			//* create content blocks
			var $cont_block = $(`<div id=${info.bot_id} class="uk-accordion-content uk-flex uk-flex-wrap uk-flex-around uk-flex-wrap-around uk-child-width-1-2@m uk-child-width-1-3@l uk-animation-fade uk-animation-slide-bottom-medium"/>`).insertAfter($title_block).html(this.DEFAULT_TXT);

			$cont_block[0].render = {};
			// console.log('this = ',this);
		}, this); //*each

		// UIkit.util.on(this.$bot_box, 'beforeshow', ()=>this.$cont_block.empty());

		this.$bot_box.on('click', 'div.uk-accordion-title', this.titleEventsRouter.bind(this));

		/* worked
		UIkit.util.on(this.$bot_box, 'beforeshow', (e)=>{
			// console.log('e=', e);
			this.$chartbox.find('canvas').attr('height','auto');
		}); */

		return this.$bot_box;
	}


	this.titleEventsRouter = function(e) {
		console.clear();
		var t = e.target.closest('.uk-button'),
			title_block = e.currentTarget;

		this.info = title_block.info;
		if (!this.info) return;

		// console.log('t = ', t, t.closest('.uk-open'));
		//* если не кнопка
		if(!t)
		{
			t = title_block;
		} else {

			//* Сохраняем событие
			title_block.event = t.event;

			if(t.closest('.uk-open'))
			{
				// e.stopPropagation();
				// e.preventDefault();
				UIkit.accordion(this.$bot_box, {
					beforehide: false
				});
				// UIkit.util.on(this.$bot_box, 'beforehide', ()=>false);
			}
		}

		// console.log('e = ', e);

		// this.$cont_block = this.$chartbox.find(`#${this.info.bot_id}`);
		this.$cont_block = $(title_block).parent().find(`div.uk-accordion-content`);

		//* fix 4 animate
		this.$cont_block[0].hidden= 1;

		// this.$cont_block.empty().append(t.event.call(this,e) || this.DEFAULT_TXT);

		new Promise((resolve)=>{
			t.event?
				t.event.call(this,e,resolve):
				resolve(this.DEFAULT_TXT);
		})
		.then($dfr=>{
			// console.log(this.$cont_block, $dfr);
			this.$cont_block[0].hidden= 0;
			this.$cont_block.empty().append($dfr||this.DEFAULT_TXT);
			// console.log(this.$cont_block, $dfr);
		});
	} //* titleEventsRouter


	this.getSettings = function (e,promise) {
		var cache = this.$cont_block[0].render;
		console.log('getSettings= ', cache.getSettings);

		if(!cache.getSettings) {
			new Promise((resolve, reject)=> {

			})
			.then();
		} else {

		}
	}


	this.getCharts = function (e,promise) {
		var cache = this.$cont_block[0].render;
		console.log('getCharts= ', cache.getCharts);

		if (!cache.getCharts) {
			var period = $('#period')[0],
				min_date_str= _H.formatDate(this.info.min_date),
				min_date_ms= Date.parse(this.info.min_date);

			var from = isNaN(Date.parse(period.from.value)) ? Math.max(Date.now() - 7 * 24 * 3600000, min_date_ms) : Math.max(Date.parse(period.from.value), min_date_ms),
				to = isNaN(Date.parse(period.to.value)) ? Date.now() : Date.parse(period.to.value);

			period.from.value= _H.formatDate(new Date(from).toISOString());
			period.from.setAttribute('min', min_date_str);

			// console.log('Date = ', period.from.value, new Date(from).toISOString(), _H.formatDate(new Date(from).toISOString()));

			var promiseStat = new Promise((resolve, reject) => {
					this.responseData = new GetResponse('get_bot_stats', {
						bot_id: this.info.bot_id,
						from: Math.floor(from / 1000),
						to: Math.floor(to / 1000),
					}, resolve, reject);
				})
				.then(
					(resolve, reject) => {
						// console.log('this= ', this);
						promise(this.drawCharts());
						cache.getCharts = resolve;

					}
				);

			// console.log('promiseStat = ', promiseStat);
		}
		else {
			Object.assign(this.responseData, cache.getCharts);

			promise(this.drawCharts());
		}
		console.log('showStat_2= ', cache.getCharts);
	} //* getCharts


	this.drawCharts = function() {
		//* use this.responseData
		var $dfr = $(document.createDocumentFragment());
		// console.log("response__ = ", this.responseData, this.responseData.ords);

		if(!this.responseData.ords)
		{
			console.warn("А нэту ординат!");
			return;
		}

		//* clean
		this.$cont_block.find('.tchart').each((ind,i)=>{
			// console.log('this.responseData = ', this.responseData);
			i.tchart.destroy();
		});

		//* Перебираем ординаты
		Object.keys(this.responseData.ords).forEach((k) => {

			var data = this.responseData.parseInputData(this.responseData.ords[k], k);
			// console.log("each", this.responseData.ords[k], k, data);

			var $curChartbox = $('<div class="tchart"/>').appendTo($dfr);

			// console.log('$curChartbox', $curChartbox, $curChartbox[0]);

			$curChartbox[0].tchart = new TChart($curChartbox[0], this.$main[0]);

			var chartboxName = this.CHART_NAMES[k] || 'ХЗ';

			$curChartbox[0].tchart.setColors(this.responseData.DARK_COLORS);
			$curChartbox[0].tchart.setData(data[0]);

			$dfr.append($curChartbox);
			$curChartbox[0].caption = $curChartbox.wrap('<div class="uk-padding-small"/>').parent();
			$curChartbox.before('<h3>' + chartboxName + '</h3>');

			$curChartbox[0].caption.hammer({direction: Hammer.DIRECTION_HORIZONTAL}).on('tap click pan swipe', (e)=>{
				e.preventDefault();
				// console.log(e.type);
			});

		}); //* each

		$dfr.find('.tchart').last().parent().addClass('uk-width uk-width-1-3@l');

		return $dfr;
	} //* drawCharts
}