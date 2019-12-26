// use $f
//	alert(getW_H.Hsait+' , '+getW_H.H)
var date = new Date,
	$date_y = $f('#date_y'),
	$id_credit_date_m = $f('#id_credit_date_m'),
	$id_credit_date_d = $f('#id_credit_date_d');

$id_credit_date_d.val(date.getDate());
$id_credit_date_m.val(date.getMonth() - 1);

for (var i = -2; i < 10; i++) {
	var y = +date.getFullYear() + i,
		$opt = $('<option />')
			.appendTo($date_y)
			.val(y).html(y);

	if ($opt.val() == date.getFullYear()) $opt.css({ fontWeight: "bold" });

}

$date_y.val(date.getFullYear());

function ras4_kr() {
	var KR = {
		date_y: $date_y,
		date_mon: $id_credit_date_m,
		date_dd: $id_credit_date_d,

		date_kr: function () {
			return new Date(this.date_y.val(), this.date_mon.val(), this.date_dd.val())
		},

		PER_KR: $f('#id_credit_percent').val(),
		SR_KR: $f('#id_credit_term').val(),
		SIZE_KR: $f('#id_credit_size').val(),
		ost: $f('#id_credit_size').val(),
		osn_s: 0, per_s: 0, sum_s: 0
	},
		$tabRez = $f('#tabRez');

	// ==========================================
	$tabRez.html('');

	$f('#vid_plat').val() == "diff" ? diff() : ann();


	function diff() {
		$f('#f_ann').hide();

		for (var i = 0; i < KR.SR_KR; i++) {
			var kr_row = $tabRez.cr('tr', {});
			var num = kr_row.cr('td', {}); num.html(i + 1);

			var nextDate = new Date(KR.date_y.val(), +KR.date_mon.val() + i + 1, KR.date_dd.val());

			kr_row.cr('td', {}).html(nextDate.toLocaleDateString());

			// В погашение долга
			var osn = kr_row.cr('td', {});
			osn.html((KR.SIZE_KR / KR.SR_KR).toFixed(2));
			// В погашение процентов
			var per = kr_row.cr('td', {});
			per.html((KR.PER_KR * (KR.SIZE_KR - KR.osn_s) / 12 / 100).toFixed(2));
			// Всего
			var sum = kr_row.cr('td', {});
			// console.log(+osn.html());
			sum.html((+osn.html() + +per.html()).toFixed(2));
			KR.sum_s -= -sum.html();
			KR.osn_s -= -osn.html();
			KR.per_s -= -per.html();
			KR.ost -= sum.html();

			var saldo = kr_row.cr('td', {});
			saldo.html((KR.SIZE_KR - KR.osn_s).toFixed(2));
		};

	}



	function ann() {
		$f('#f_ann').show();

		var I = KR.PER_KR / 12 / 100;
		var N = KR.SR_KR;
		var K = (I * Math.pow((1 + I), N)) / (Math.pow((1 + I), N) - 1)
		//al(K)
		for (var i = 0; i < KR.SR_KR; i++) {
			var kr_row = $tabRez.cr('tr', {});
			var num = kr_row.cr('td', {});
			num.html(i + 1);
			var date = kr_row.cr('td', {});
			var nextDate = new Date(KR.date_y.val(), +KR.date_mon.val() + i + 1, KR.date_dd.val());
			date.html(nextDate.toLocaleDateString());

			var osn = kr_row.cr('td', {});
			osn.html((KR.SIZE_KR / KR.SR_KR).toFixed(2));
			var per = kr_row.cr('td', {});
			var sum = kr_row.cr('td', {});
			sum.html((KR.SIZE_KR * K).toFixed(2)); KR.sum_s -= -sum.html();
			per.html((sum.html() - osn.html()).toFixed(2));
			KR.osn_s -= -osn.html();
			KR.per_s -= -per.html();
			KR.ost -= sum.html();
			var saldo = kr_row.cr('td', {});
			saldo.html((KR.SIZE_KR - KR.osn_s).toFixed(2));

		}


	}

	var sum_row = $tabRez.cr('tr', { style: 'font-weight:bold; border-top:1px solid #999;' });
	sum_row.cr('td', {}).html("Всего: ");
	sum_row.cr('td', {})
	sum_row.cr('td', {}).html((KR.osn_s).toFixed(2));
	sum_row.cr('td', {}).html((KR.per_s).toFixed(2));
	sum_row.cr('td', { style: 'text-decoration:underline;' }).html((KR.sum_s).toFixed(2));

}