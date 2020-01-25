'use strict';
// $('#chartbox').hide();
var _g = {
	$bot_box: $('#bot_box'),
	$main: $('div#main'),
	$chartbox: $('#bot_box'),
	//* promised data
	$dataStat: null,
}

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