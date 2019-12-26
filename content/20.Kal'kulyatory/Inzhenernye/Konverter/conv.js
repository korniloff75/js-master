'use strict';

var _num = {
	val : function (id) {
		return +(document.querySelector(id).value.replace(",", ".")) || 0;
	},

	toPrecision : function (num, precision) {
		// remove the extra zeros
		return num.toPrecision(precision).replace(/(\..+?)0+($|e)/, '$1$2');
	}
}


function fillData(partName) {
	var data = calcData[partName],
		f_data = document.querySelector('#f_data'),
		s_data = document.querySelector('#s_data'),
		f_sel = document.querySelector('#f_sel'),
		s_sel = document.querySelector('#s_sel');

	f_sel.innerHTML = s_sel.innerHTML = '';

	data.forEach(function (i, ind) {

		var un = document.createElement('option')

		un.textContent = i.n;
		un.value = ind;

		f_sel.appendChild(un);
		s_sel.appendChild(un.cloneNode(true));

	})

	f_data.value = s_data.value = 1;

	document.querySelector('#field').oninput = calc.bind(null, data);

}


function calc(data, e) {
	var inp = e.target,
	prec = document.querySelector('#prec');

	// console.log(inp);

	if (!inp.closest('#f_sel') && inp.closest('#f_block') || inp.closest('#s_sel') ) {
		var inp_data = _num.val('#f_data'),
		ind_1 = _num.val('#f_sel'),
		ind_2 = _num.val('#s_sel'),
		conv_data = document.querySelector('#s_data');
		console.log('first');

	} else if(inp.closest('#f_sel') || inp.closest('#s_block')) {
		var inp_data = _num.val('#s_data'),
		ind_1 = _num.val('#s_sel'),
		ind_2 = _num.val('#f_sel'),
		conv_data = document.querySelector('#f_data')
		console.log('second');
	} else return;

	conv_data.value = _num.toPrecision((inp_data - (data[ind_1].const || 0)) / data[ind_2].v * data[ind_1].v + (data[ind_2].const || 0), prec.value);

/* 	console.log(
	'ind_1 = ', ind_1, '\n',
	'ind_2 = ', ind_2, '\n',
	'data[ind_1].v  = ', data[ind_1].v , '\n',
	'data[ind_2].v  = ', data[ind_2].v , '\n',
	'data[ind_1].const = ', data[ind_1].const , '\n',
	'data[ind_2].const = ', data[ind_2].const , '\n'
	); */
}



var calcData = {
	load : [
		{
			n: 'Ньютон (Н)',
			v: 1
		},
		{
			n: 'кН',
			v: 1000
		},
		{
			n: 'МН',
			v: 1e6
		},
		{
			n: 'Грамм (г)',
			v: 9.807e-3
		},
		{
			n: 'кг',
			v: 9.807
		},
		{
			n: 'тонна (т)',
			v: 9.807e3
		},
	],


	load_udel : [
		{
			n: 'Н/м',
			v: 1
		},
		{
			n: 'Н/см',
			v: 100
		},
		{
			n: 'кН/м',
			v: 1000
		},
		{
			n: 'кН/см',
			v: 1e5
		},
		{
			n: 'кг/м',
			v: 9.807
		},
		{
			n: 'кг/см',
			v: 980.7
		},
		{
			n: 'т/м',
			v: 9.807e3
		},
		{
			n: 'т/см',
			v: 9.807e5
		},
	],


	pressure: [
		{
			n: 'Паскаль',
			v: 1
		},
		{
			n: 'Атмосфера',
			v: 101325
		},
		{
			n: 'Бар',
			v: 100000
		},
		{
			n: 'См. ртутного ст.',
			v: 1333.22387415
		},
		{
			n: 'См. водяного ст.',
			v: 98.0638
		},
		{
			n: 'Мм. ртутного ст.',
			v: 133.322387415
		},
		{
			n: 'Мм. водяного ст.',
			v: 9.80638
		},
		{
			n: 'Кгс/м2',
			v: 9.80665
		},
		{
			n: 'Кгс/см2',
			v: 98066.5
		},
		{
			n: 'Кгс/мм2',
			v: 9806650
		},
		{
			n: 'Н/м2',
			v: 1
		},
		{
			n: 'Н/см2',
			v: 10000
		},
		{
			n: 'Н/мм2',
			v: 1e6
		},
	],


	speed: [
		{
			n: 'м/с',
			v: 1
		},
		{
			n: 'м/мин',
			v: 1/60
		},
		{
			n: 'м/ч',
			v: 1/3600
		},
		{
			n: 'км/с',
			v: 1000
		},
		{
			n: 'км/мин',
			v: 16.67
		},
		{
			n: 'км/ч',
			v: 1 / 3.6
		},

	],


	area: [
		{
			n: 'м2',
			v: 1
		},
		{
			n: 'см2',
			v: 1e-4
		},
		{
			n: 'мм2',
			v: 1e-6
		},
		{
			n: 'км2',
			v: 1e6
		},
		{
			n: 'Гектар',
			v: 1e4
		},
		{
			n: 'Ар (сотка)',
			v: 100
		},
	],


	volume : [
		{
			n: 'м3',
			v: 1
		},
		{
			n: 'см3',
			v: 1e-6
		},
		{
			n: 'мм3',
			v: 1e-9
		},
		{
			n: 'Литр (л)',
			v: 1e-3
		},
		{
			n: 'дл',
			v: 1e-2
		},
	],


	temperature : [
		{
			n: 'градус Цельсия',
			v: 1
		},
		{
			n: 'градус Фаренгейта',
			v: 5/9,
			const : 32
		},
		{
			n: 'градус Кельвина',
			v: 1,
			const : 273.15
		},
	]
}

fillData(document.querySelector('#partName').value);

