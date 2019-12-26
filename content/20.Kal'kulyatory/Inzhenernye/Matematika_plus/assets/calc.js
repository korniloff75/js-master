/******************************************
Copyright KorniloFF-ScriptS ©
//js-master.ru
*******************************************/
"use strict";
window.Calc = {
	__proto__: null,
	sts: {
		bg: "#f5f5f5",
		bg_hover: "#cef",

	},

	v: {
		$mainField: $('#Input'),
	},

	get GI() { return +Calc.v.$mainField.val() },

	Deg: function (rad) { return rad * Math.PI / 180 },
	Rad: function (deg) { return deg * 180 / Math.PI },
	TrF: {
		//	__proto__: null,
		' sin ': function () { return Math.sin(Calc.Deg(Calc.GI)) }, ' cos ': function () { return Math.cos(Calc.Deg(Calc.GI)) },
		' tg ': function () { return Math.tan(Calc.Deg(Calc.GI)) }, ' ctg ': function () { return 1 / Math.tan(Calc.Deg(Calc.GI)) },
		'asin': function () { return Calc.Rad(Math.asin(Calc.GI)) }, 'acos': function () { return Calc.Rad(Math.acos(Calc.GI)) },
		'atg': function () { return Calc.Rad(Math.atan(Calc.GI)) }, 'actg': function () { return 90 - Calc.Rad(Math.atan(Calc.GI)) }
	},
	Trig: '',

	Arm: function () { //== Параметры арматуры
		var d = $ ('#dN').val(), Se4, Mas,
			La = $ ('#LaN').val(),
			Kvo = $ ('#KvoN').val();

		if ($('#Kr')[0].checked == true) { Se4 = Math.PI * d * d * Kvo / (4 * 100) };
		if ($('#Kv')[0].checked == true) { Se4 = d * d * Kvo / 100 }

		$ ('#Se4N').val(Se4.toFixed(2)) ;
		Mas = Se4 * La * 0.785;
		$ ('#MasN').val(Mas.toFixed(2)) ;
		$('#Ras4Arm input').on('keyup', Calc.Arm);
	},

	pkey: function (e) {
		var keyCode = $().e.fix(e).which,
		self = e.target;

		// console.log(keyCode, e.target.value);

		if([1091, 1077].includes(keyCode)) {
			console.log(self.value);
			return 'e';
		}

		// fix "e"
		self.value = self.value.replace(/[уе]/, 'e');

		if ((keyCode >= 42 && keyCode <= 57) || [8, 101].includes(keyCode)) return true;
		else if (keyCode == 13) {
			Calc.Ent();
		} else if (keyCode == 32) {
			self.value = '';
			self.placeholder = 0;
		}
		return false;
	},

	Ent: function () { // Enter
		var des = $ ('#desN').val(),
			$arg = Calc.v.$mainField,
			$std = $('#std');

		$arg.val(+eval($arg.val()));
		$arg.val((!isNaN($arg.val())) ? Math.pow($arg.val(), +$std.val()).toFixed(des) : "Данные не верны");
		$std.val(1);
		$arg[0].focus();
	},

	tbg: function () {
		Calc.v.$mainField.css({ background: Calc.sts.bg_hover });
	},

	tbgout: function () {
		Calc.v.$mainField.css({ background: Calc.sts.bg });
	},

	M: { /* Работа с памятью */
		stek: $.cookie.get('Calc_memStek') && $.cookie.get('Calc_memStek').split(',') || [0, 0, 0, 0, 0, 0],
		val: function () {
			Calc.v.Mi = +$ ('#MEMN').val();
			$ ('#MEMD').val(Calc.M.stek[Calc.v.Mi]) ;
			Calc.v.$mainField[0].focus();
		},
		save: function () {
			$.cookie.set({ Calc_memStek: Calc.M.stek }, 10)
			console.info('Calc.M.stek= ' + Calc.M.stek);
		},
		pl: function () {
			Calc.M.val();
			$ ('#MEMD').val(Calc.M.stek[Calc.v.Mi] -= -Calc.v.$mainField.val());
			Calc.M.save();
		},
		min: function () {
			Calc.M.val();
			$ ('#MEMD').val(Calc.M.stek[Calc.v.Mi] -= Calc.v.$mainField.val());
			Calc.M.save();
		},
		r: function () {
			Calc.M.val();
			Calc.v.$mainField[0].value += Calc.M.stek[Calc.v.Mi]; Calc.M.save();
		},
		c: function () {
			Calc.M.val();
			$ ('#MEMD').val(Calc.M.stek[Calc.v.Mi] = 0);
			Calc.M.save();
		}
	},

	selFns: function () {
		['#TrigFunks', '#Ras4Arm'].forEach(function(i, ind) {
			$(i)[0].hidden = this.value != ind + 1;
			if(ind === 0) Calc.Arm();
			Calc.v.$mainField[0].focus();
		}, this);

	}
};

//== Тригонометрия
Object.keys(Calc.TrF).forEach(function (i) {
	var $but = $('#TrigFunks td').cr('input', { value: i, type: 'button', class: 'btn', style: 'float:left;' });

	// console.log($but);

	$but.on('click', function () {
		Calc.v.$mainField.val(Calc.TrF[i]());
		Calc.Ent();
	});
});

$ ('#MEMD').val(Calc.M.stek[0]) ;

Calc.v.$mainField.on({
	focus: Calc.tbg,
	blur: Calc.tbgout
});
