'use strict';

//* use jQ
var Render = function Render($bot_box, $chartbox, $main)
{
	Object.assign(this, _g.RenderDefaults, {
		//* Блок с ботами
		$bot_box: $bot_box || _g.$bot_box,
		//* Блок с графиками
		$chartbox: $chartbox || _g.$chartbox,
		//* Общий блок
		$main: $main || _g.$main,
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

	this.drawSettings = drawSettings.bind(this);

	//* Получаем настройки
	this.getSettings = function (e,setDFR) {
		var cache = this.$cont_block[0].render;
		console.log('getSettings= ', cache.getSettings);

		if(!cache.getSettings) {
			this.responseData = new GetResponse('get_bot_settings', {
				bot_id: this.info.bot_id,
			});
			this.responseData.promise.then(
				(resolve, reject) => {
					// console.log('this= ', this);
					setDFR(this.drawSettings());
					cache.getSettings = resolve;
				}
			);
		} else {
			Object.assign(this.responseData, cache.getSettings);

			setDFR(this.drawSettings());
		}
	}


	//* Получаем данные для графиков
	this.getCharts = function (e,getDFR) {
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

			this.drawCharts = drawCharts.bind(this);

			this.responseData = new GetResponse('get_bot_stats', {
				bot_id: this.info.bot_id,
				from: Math.floor(from / 1000),
				to: Math.floor(to / 1000),
			});
			this.responseData.promise.then(
				(resolve, reject) => {
					// console.log('this= ', this);
					getDFR(
						resolve ?
							this.drawCharts():
							this.NO_DATA_TXT
					);
					cache.getCharts = resolve;
				}
			);

			// console.log('promiseStat = ', promiseStat);
		}
		else {
			Object.assign(this.responseData, cache.getCharts);

			getDFR(this.drawCharts());
		}
		console.log('showStat_2= ', cache.getCharts);
	} //* getCharts


	//* Отрисовываем настройки
	function drawSettings() {
		var $dfr = $(document.createDocumentFragment()),
			data = Object.setPrototypeOf(this.responseData.data, this.info),
		// $dfr.append(JSON.stringify(data) + JSON.stringify(this.info)),
			$stsBox = $('<div class="uk-flex uk-flex-wrap"/>').appendTo($dfr);

		//* Checkboxes
		// uk-list-large
		var $ulChbs = $('<ul class="ulChbs uk-width uk-width-1-2@s uk-list uk-list-striped uk-padding-small"/>').appendTo($stsBox);

		Object.keys(data).forEach(i=>{
			var $label = $(`<label class="uk-flex uk-flex-wrap uk-flex-middle uk-form-label"><input class="uk-checkbox uk-margin-right" type="checkbox" data-prop="${i}" ${data[i]? 'checked': ''}></label>`).appendTo($ulChbs);
			$label.append(this.SETTINGS_NAMES.chbs[i]).wrap(`<li class="${i}">`);
			// console.log(data[i]);

		});

		var $stats_open = $ulChbs.find('.stats_open');
		if(!$stats_open.find('input.uk-checkbox')[0].checked)
		{
			$stats_open.siblings().prop('hidden', 1);
		}

		//* Toggle from stats_open
		$stats_open.on('click', e=>{
			var ct = e.currentTarget,
				chb = ct.querySelector('input.uk-checkbox');
			// console.log(ct, $stats_open.length);
			// $stats_open.siblings().attr('hidden', !chb.checked? 1: null);
			$stats_open.siblings().prop('hidden', !chb.checked? 1: 0);
		});

		//* Inputs
		var $ulInps = $($ulChbs[0].cloneNode(false)).appendTo($stsBox),
			dataInps = ['queue_limit', 'check_interval'];

		$ulInps.removeClass('ulChbs');
		$ulInps.addClass('ulInps');

		dataInps.forEach(i=>{
			var $label = $(`<label class="uk-flex uk-flex-wrap uk-flex-middle uk-form-label"><input class="uk-input uk-width-1-4@s uk-margin-right" type="number" data-prop="${i}" value="${this.info[i]}" min=0></label>`).appendTo($ulInps);
			$label.append(this.SETTINGS_NAMES.inps[i]).wrap(`<li class="${i}">`);
			// console.log(data[i]);

		});

		$('<input type="button" class="uk-button uk-button-primary send" value="Save" disabled>').appendTo($ulInps);

		// var $ulSts = $ulChbs.clone().empty().appendTo($dfr);

		$stsBox.on('input', 'input', handlerSettings.bind(this));
		$stsBox.on('click', 'input.send', handlerSettings.bind(this));

		console.log($ulInps);

		return $dfr;
	} //* drawSettings


	//* Обрабатываем события на настройках
	function handlerSettings(e)
	{
		var t = e.target,
			main = e.delegateTarget,
			sendButt = main.querySelector('.send'),
			dataFields = main.querySelectorAll('input[data-prop]');

		sendButt.classList.remove('uk-form-danger','uk-form-success');

		//* set_bot_settings
		if(e.type === 'click')
		{
			if(t === sendButt)
			{
				var newSts = {stats:{}, bot_id:this.info.bot_id};

				[].forEach.call(dataFields, i=> {
					var name= i.getAttribute('data-prop');
					if(i.type === 'checkbox')
						i.value = i.checked? 1: 0;
					if(i.closest('.ulChbs')) newSts.stats[name] = i.value;
					// if(['stats_open','msgs_open','users_open','chats_open',].includes(name)) newSts.stats[name] = i.value;
					// else newSts[name] = i.value;
					else if(i.value != this.responseData.data[name])
					{
						newSts[name] = i.value
					};
				});

				console.log('newSts= ', newSts);

				var Send = new GetResponse('set_bot_settings', newSts);
				Send.promise.then(resolve=> {
					if(!resolve) sendButt.classList.add('uk-form-danger');
					else {
						sendButt.disabled = true;
						sendButt.classList.add('uk-form-success');

					}
				});
			}
			return;
		} //* click

		//* Check input data
		if(
			[].every.call(dataFields, i=> {
				if(i.type === 'checkbox')
					i.value = i.checked? 1: 0;
				return i.value == this.responseData.data[i.getAttribute('data-prop')]
			})
		) {
			sendButt.disabled = true;
		} else {
			sendButt.disabled = false;
		}

		console.log(t, t.value);
	} //* handlerSettings


	//* Отрисовываем графики
	function drawCharts() {
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
			var $wrap = $curChartbox.wrap('<div/>');
			//todo $curChartbox.data.caption
			$curChartbox[0].caption = $wrap.parent();
			$curChartbox.before('<h3>' + chartboxName + '</h3>');
			if(this.$window.width() > 600)
				$wrap.addClass('uk-padding-small');

			// console.log('$curChartbox[0].caption=', $curChartbox[0].caption);

			$curChartbox[0].caption.hammer({direction: Hammer.DIRECTION_HORIZONTAL}).on('tap click pan swipe', (e)=>{
				e.preventDefault();
				// console.log(e.type);
			});

		}); //* each

		$dfr.find('.tchart').last().parent().addClass('uk-width uk-width-1-3@l');

		return $dfr;
	} //* drawCharts
}