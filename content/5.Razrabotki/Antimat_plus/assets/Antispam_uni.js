var Glob_vars = {
	unlim: _H.qs.parse()['unlim']
},
$DB= $.getScript('/' + sv.DIR  + 'assets/Antispam_DB.php'),
date = new Date;

if(Glob_vars.unlim && Glob_vars.unlim == date.getDate()) {
$.cookie.del('spnum','spamDate');
$.cookie.set({
	spnum: 2,
	spamDate: date.getTime()
});

// location.replace(Glob_vars.path().replace(/.unlim.+$/i,''));
}

$DB.done(function() { //alert(DB)
var spams= Glob_vars.spams, mat= Glob_vars.mat;

$.fn.spam= function(time,submit)  {
	var $mes = this,
	// mes= document.forms[fn].elements[n],
	mes = this[0];

	if(!Glob_vars.GR_ID || Glob_vars.GR_ID <= Glob_vars.fixId)  {
		time= time || 7;
		if (!(spnum = $.cookie.get('spnum')) || isNaN(spnum))  window.spnum = 0 ;

		if (spnum>2) {
			var ost_time= time - ((date.getTime() - $.cookie.get('spamDate'))/ (1000*60*60*24)).toFixed(0);
			alert('Вы были предупреждены и теперь не можете больше писать здесь сообщений <b>'+ost_time+'</b> дней.\nАдминистрация сайта.', {width:370});
			mes.value=''; return false;
		}

		if (spams.some(	function (i) {
				return new RegExp(i, 'i').test(mes.value)
			}) )  {
				spnum++; // alert(spnum)
				if (spnum==1) alert('Вас заметили в распространении спама!\n Мы следим за вашим поведением на сайте.', {width:350});

				mes.value='Сообщение удалено [url=//js-master.ru/content/5.Razrabotki/Antimat_plus/]Антиспамом[/url]. [b]'+ spnum +'-е[/b] предупреждение Автору за спам! После 3-го предупреждения Вы не сможете добавлять посты на этом ресурсе [b]'+time+'[/b] дней. \\n Если вы считаете, что скрипт обшибся, напишите в [url=' + _H.mailform + ']Форму обратной связи[/url] свою претензию.';

				$.cookie.set( [['spnum',spnum],['spamDate', date.getTime()]], time );
				$.cookie.set({
					spnum: spnum,
					spamDate: date.getTime()
				}, {
					expiries: time
				});

				// console.log(mes.form);
				if(mes.form) mes.form.onsubmit = function(e) {
					e.preventDefault();
					return false;
				}
				return false;
		} else console.log('no spam in messege');

		// Борьба с реферальными ссылками на форуме
		mes.value = mes.value.replace(/[\?&]\w{0,10}=\w+[^\W\s\]\[]|\/\d+\/r\d+/gi,'');
	}


	// Антимат
	var zam=' [цензура] ';

	for (var j in mat) {
		if (!mat.hasOwnProperty(j)) continue;

		var outmes= mes.value= (mes.value).replace(new RegExp(mat[j],'gi'),zam);
	}

		return outmes;
}
});


/** examole 4 form
$(document.forms['addForm']).on('submit', function(e) {
e.preventDefault();
var message = $(this.elements.message).spam(10);
if(message) this.submit();
});
*/