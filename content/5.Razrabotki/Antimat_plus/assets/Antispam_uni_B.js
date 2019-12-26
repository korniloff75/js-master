'use strict';

var Antispam = {
		date: new Date,
		unlim: _H.qs.parse()['unlim'],
		db: $.getScript('/' + sv.DIR  + 'assets/Antispam_DB.php'),
		zam: ' [цензура] '
	};


if(Antispam.unlim && Antispam.unlim == Antispam.date.getDate()) {
	$.cookie.del('spnum','spamDate');
	$.cookie.set({
		spnum: 2,
		spamDate: Antispam.date.getTime()
	});

	// location.replace(Glob_vars.path().replace(/.unlim.+$/i,''));
}

Antispam.db.done(function() {
	var spams= Antispam.spams, mat= Antispam.mat;

	$.fn.spam= function(time,submit)  {
		var mes = this[0],
		spnum = $.cookie.get('spnum') || 0;

		if(!Antispam.GR_ID || Antispam.GR_ID <= Antispam.fixId)  {
			time= time || 7;

			if (spnum>2) {
				var ost_time= time - ((Antispam.date.getTime() - $.cookie.get('spamDate'))/ (1000*60*60*24)).toFixed(0);
				alert('Вы были предупреждены и теперь не можете больше писать здесь сообщений <b>'+ost_time+'</b> дней.\nАдминистрация сайта.', {width:370});
				return mes.value = '';
			}

			if (spams.some(	function (i) {
					return new RegExp(i, 'i').test(mes.value)
				}) )  {
					spnum++;
					// console.log('spnum = ', spnum);
					if (spnum == 1) alert('Вас заметили в распространении спама!\n Мы следим за вашим поведением на сайте.');

					mes.value = 'Сообщение удалено [url=//js-master.ru/content/5.Razrabotki/Antimat_plus/]Антиспамом[/url]. [b]'+ spnum +'-е[/b] предупреждение Автору за спам! После 3-го предупреждения Вы не сможете добавлять посты на этом ресурсе [b]'+time+'[/b] дней. \\n Если вы считаете, что скрипт обшибся, напишите в [url=//js-master.ru/content/1000.Contacts/feedback/]Форму обратной связи[/url] свою претензию.';

					$.cookie.set({
						spnum: spnum,
						spamDate: Antispam.date.getTime()
					}, {
						expiries: time
					});

					// console.log(mes.form);
					/* if(mes.form) mes.form.onsubmit = function(e) {
						e.preventDefault();
						return false;
					} */
					// return false;
			} else console.log('no spam in messege');

			// Борьба с реферальными ссылками на форуме
			mes.value = mes.value.replace(/[\?&]\w{0,10}=\w+[^\W\s\]\[]|\/\d+\/r\d+/gi,'');
		}


		// Антимат
		for (var j in mat) {
			if (!mat.hasOwnProperty(j)) continue;

			var outmes= mes.value= (mes.value).replace(new RegExp(mat[j],'gi'), Antispam.zam);
		}

			return outmes;
	}
});


/** example 4 form
$(document.forms['addForm']).on('submit', function(e) {
	e.preventDefault();
	var message;
	if((message = $(this.elements.message).spam(10))) this.submit();
});
*/