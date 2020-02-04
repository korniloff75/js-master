'use strict';
// $('#chartbox').hide();
var _g = {
	$bot_box: $('#bot_box'),
	$main: $('div#main'),
	$chartbox: $('#bot_box'),
	//* promised data
	$dataStat: null,

	RenderDefaults: {
		data: {},
		$window: $(window),
		DEFAULT_TXT: '<p>Данные по этому боту в настоящий момент отсутствуют. Попробуйте позже.</p>',
		NO_DATA_TXT: '<p>Данные по этому боту за указанный период времени отстутствуют.</p>',
		CHART_NAMES: {
			msgs: 'Статистика сообщений',
			chats: 'Статистика чатов',
			users: 'Статистика пользователей',
		},
		SETTINGS_NAMES: {
			chbs: {
				stats_open: 'открытая статистика',
				msgs_open: 'статистика сообщений',
				chats_open: 'статистика чатов',
				users_open: 'статистика пользователей',
			},
			inps: {
				queue_limit: 'Лимит очереди',
				check_interval: 'Интервал проверок',
			}
		},
		STYLES: `button {box-sizing:border-box}
		button.uk-button {margin:.5em; margin-left:0;}
		.menu>button:hover {box-shadow: #79a 1px -1px 2px -1px;}`,
	},

	get_bot_stats: {
		LIGHT_COLORS: {
			circleFill: '#ffffff',
			line: '#f2f4f5',
			zeroLine: '#ecf0f3',
			selectLine: '#dfe6eb',
			text: '#96a2aa',
			preview: '#eef2f5',
			previewAlpha: 0.8,
			previewBorder: '#b6cfe1',
			previewBorderAlpha: 0.5
		},

		DARK_COLORS: {
			circleFill: '#242f3e',
			line: '#293544',
			zeroLine: '#313d4d',
			selectLine: '#3b4a5a',
			text: '#546778',
			preview: '#152435',
			previewAlpha: 0.8,
			previewBorder: '#5a7e9f',
			previewBorderAlpha: 0.5
		},
	}
};

// Hammer&&(Hammer.defaults.domEvents = true);

$(()=>$('#preloader').hide());

;(()=>{
	if(!window._bots)
	{
		console.error("Информация о ботах недоступна!");
		return;
	}

	//* Показываем период
	// $('#period').removeClass('uk-invisible');
	// UIkit.util.removeClass('#period', 'uk-invisible');
	UIkit.util.$('#period').classList.remove('uk-invisible');

	var R = new Render(_g.$bot_box, _g.$chartbox, _g.$main);
	// console.log('R= ',R);
	//* Render bot list & add click 4 Request bot data
	R.create();

	//* Приветствие вместо авторизации
	$('#authlink').parent().html(
		`<img src=${_remoteUrl}ava?url=${_authData['photo_url']} data-src=${_authData['photo_url']} style='width:75px; border: 2px solid #1e87f0; border-radius:100%' class='uk-flex-last uk-animation-shake uk-position-bottom-right'>
		<span class='uk-visible@s'>Привет, ${_authData['first_name']}!</span>`
	).removeClass('uk-width').addClass('uk-width-1-3 uk-width-1-4@s');
})();


/**
 * Helper
 */
window._H = {
	/**
	 * ISO -> input[type=date]
	 * @param {string} date toISOString
	 */
	formatDate (date) {
		date = new Date(date);
		return `${date.getFullYear()}-${this.checkZero(date.getMonth()+1)}-${this.checkZero(date.getDate())}`;
	},

	checkZero (n) {
		return n<10 ? `0${n}` : n;
	}
}