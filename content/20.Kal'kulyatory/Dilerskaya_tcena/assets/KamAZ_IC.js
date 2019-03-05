'use strict';
// Prices_db genered in handler.php

var $model = $f('#model');

function search(s_mod) {
	$model.html('');

	Prices_db.forEach(function(i) {
		var p = i.split('|');
		// console.log(!(new RegExp('^'+s_mod, 'i')).test(p[0]));
		if(!(new RegExp('^'+s_mod, 'i')).test(p[0]))
			return;

		$('<option />', {
			value: i
		}).appendTo($model)
			.text(p[0]);
		});
		// console.log($model.html());

		price();

 };

 search('');


function price() {
	var params = $model.val().split('|');
	// console.log(params);

	var priceZ = parseInt(params[1]);
	var priceZ_NDS = parseInt(params[2]),
		$mod_descr = $f('#mod_descr'),
		$dil_sk = $f('#dil_sk'),
		format = /(\d)(?=(\d{3})+(?!\d))/g;

	// console.log(priceZ_NDS);

	$f('#priceZ').text((priceZ + ' руб.').replace(format, '$1 '));
	$f('#priceZ\\+NDS').text((priceZ_NDS + ' руб.').replace(format, '$1 '));

	// $mod_descr.html('');

	$mod_descr[0].innerHTML = params[3] ? ('<b>Базовая модель:</b> ' + params[3]) : '';

	if (params[4]) $mod_descr[0].innerHTML += '<br> <b>Описание:</b> ' + params[4];

	var all_nac = 0;
	if (isNaN(+$dil_sk.val()) || isNaN(+$f('#dostavka').val())) alert("Неверные данные!");

	$('#nacenka input').each(function (ind, i) {
		all_nac -= (i && !isNaN(i.value)) ?
			($f('#rub_pr1').val() == 'rub') ? -i.value : -priceZ * i.value / 100 :
			0;
	});

	var stoim = ($f('#rub_pr').val() == 'rub') ? priceZ - $dil_sk.val() : priceZ * (1 - $dil_sk.val() / 100);

	var priceS = (stoim - (-$f('#hran').val() - $f('#dostavka').val() - $f('#dop_oborud').val() - all_nac)).toFixed(0);

	var stoimNDS = (priceS - priceZ + priceZ_NDS).toFixed(0);

	// console.log(stoim, priceS, stoimNDS);

	$f('#price').html('<b>' + priceS.replace(format, '$1 ') + ' руб.</b>');

	$f('#price\\+NDS').html('<b>' + stoimNDS.replace(format, '$1 ') + ' руб.</b>');
};


function nac_plus() {
	var $nac = $f('#nacenka').cr('input', { type: 'text', style: 'clear:both; width:100px;', title: 'Скидки задавать отрицательными', value: "0" });

	$nac[0].oninput = price;
	$f('#nacenka').cr('br', {}, 'after');
}


function changeBase (bname) {
	$('#ajax-content').load('', {
		ajax: 1,
		bname: bname
	}, function(response) {
		$('#bname').val(bname);
	})
}