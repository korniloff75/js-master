'use strict';

//* use jQ
var Render = function Render($but_box, $chartbox, $main) {
	//* Блок с ботами
	this.$but_box = $but_box || _g.$bot_box;
	//* Блок с графиками
	this.$chartbox = $chartbox || _g.$chartbox;
	//* Общий блок
	this.$main = $main || _g.$main;

	this.create = function () {
		_bots.forEach((info, ind) => {
			//* create btns
			// var $curBox = $(`<li/>`).appendTo(this.$but_box);
			var $but = $(`<button class="uk-button uk-button-primary">${info.name}</button>`).appendTo(this.$but_box);
			$but[0].info = info;
			//* create empty blocks
			$(`<div id=${info.bot_id} class="uk-flex uk-flex-wrap uk-flex-around uk-flex-wrap-around uk-animation-fade uk-animation-slide-bottom-medium"/>`).appendTo(_g.$chartbox).html('<p>Данные по этому боту в настоящий момент отсутствуют. Попробуйте позже.</p>');
		});
		return this.$but_box;
	}

	this.btnEvents = function (e) {
		// e.preventDefault();

		console.log(e.target, 'info= ', e.target.info);
		var info = e.target.info;
		if (!info) return;

		this.info = info;

		//* Fix uk
		// uk-hidden
		_g.$main.find('.uk-active').removeClass('uk-active');
		e.target.classList.add('uk-active');
		//* Блок графиков, связанный с кнопкой
		var $chartBlock = $(`#${info.bot_id}`);
		$chartBlock.addClass('uk-active');

		if (!$chartBlock[0].response) {
			var from = isNaN(Date.parse($('#period')[0].from.value)) ? Date.now() - 7 * 24 * 3600000 : Date.parse($('#period')[0].from.value),
				to = isNaN(Date.parse($('#period')[0].to.value)) ? Date.now() : Date.parse($('#period')[0].to.value),
				promiseStat = new Promise((resolve, reject) => {
					_g.dataStat = new BotStats('get_bot_stats', {
						bot_id: info.bot_id,
						from: Math.floor(from / 1000),
						to: Math.floor(to / 1000),
					}, resolve, reject);
				})
				.then(
					(resolve, reject) => {
						console.log('reject= ', reject);
						this.onResponse();
						// this.onResponse(resolve, reject);
						$chartBlock[0].response = resolve;
					}
				);

			console.log('promiseStat = ', promiseStat);
		}
		else {
			_g.dataStat = Object.assign(_g.dataStat, $chartBlock[0].response);
			// _g.dataStat = response;
			this.onResponse();
		}

	}


	this.onResponse = function() {
		//* use _g.dataStat
		var info = this.info;
		console.log("response__ = ", _g.dataStat, _g.dataStat.ords);

		if(!_g.dataStat.ords)
		{
			console.warn("А нэту ординат!");
			return;
		}

		//* current block
		var $chartBlock = _g.$chartbox.find(`#${info.bot_id}`);
		//* clean
		// console.log('$chartBlock.find(\'.tchart\')= ', $chartBlock.find('.tchart'), $chartBlock);
		$chartBlock.find('.tchart').each((ind,i)=>{
			console.log('_g.dataStat = ', _g.dataStat);
			i.tchart.destroy();
		});
		$chartBlock.html('');

		//* Перебираем ординаты
		Object.keys(_g.dataStat.ords).forEach((k, ind) => {

			var data = _g.dataStat.parseInputData(_g.dataStat.ords[k], k);
			// console.log("each", _g.dataStat.ords[k], k, data, ind);

			var $curChartbox = $('<div class="tchart"/>').appendTo($chartBlock);

			// console.log('$curChartbox', $curChartbox, $curChartbox[0]);

			$curChartbox[0].tchart = new TChart($curChartbox[0], _g.$main[0]);
			var chartboxName = _g.dataStat.CHART_NAMES[k] || 'ХЗ';

			$curChartbox[0].tchart.setColors(_g.dataStat.DARK_COLORS);
			$curChartbox[0].tchart.setData(data[0]);

			$chartBlock.append($curChartbox);
			$curChartbox.wrap('<div/>');
			$curChartbox.before('<h3>' + chartboxName + '</h3>');

		});
	} //* onResponse
}