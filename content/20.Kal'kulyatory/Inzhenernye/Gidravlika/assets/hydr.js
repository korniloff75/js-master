'use strict';

function val (id) {
	return +(document.querySelector(id).value.replace(",", ".")) || 0;
}

function calc(di) {
	// Расход л/с -> м3/с
	var q = val('#q') / 1000,
		// Внутр. диаметр мм -> м
		d_y = di || (val('#d') - 2*val('#s')) / 1000,
		L = val('#L'),
		// kt уклона
		// k = val('#k'),
		t = val('#type_tube'),
		t_mid = val('#t_mid'),
		sher = val('#sher') / 1000,
		sum_ksi = val('#sum_ksi'),
		t_k, t_m, t_b,
		// к-ты по СНиП
		sn,
		ro = (-.003*Math.pow(t_mid,2)-.1511*t_mid+1003.1)/1000,
		v = 4*q/ro/Math.PI/Math.pow(d_y, 2),
		range_v = [.1, 10],
		range_d = range_v.map(function(i) {
			return Math.pow(4*q/ro/Math.PI/i, 1/2)
		}),
		i1000, Dp, Dp_Pa, S;

	if(v > range_v[1]) {
		// return alert('Скорость воды превышает ' + range_v[1] + ' м/с\nУвеличьте диаметр трубопровода.');
	}

	switch (t) {
		case 1: t_k = 1.790; t_m = 5.100; t_b = 1.900;
		sn = {m: .226, A0: 1, A1000: 15.9, C: .684};
		break;
		case 2: t_k = 1.790; t_m = 5.100; t_b = 1.900;
		sn = {m: .284, A0: 1, A1000: 14.4, C: 2.360};
		break;
		case 3: t_k = 1.735; t_m = 5.300; t_b = 2.000;
		sn = v <= 1.2 ? {m: .30, A0: 1, A1000: 17.9, C: .867} : {m: .30, A0: 1, A1000: 21.0, C: 0};
		break;
		case 4: t_k = 1.180; t_m = 4.890; t_b = 1.850;
		sn = {m: .19, A0: 1, A1000: 11.0, C: 3.51};
		break;
		case 5: t_k = 1.688; t_m = 4.890; t_b = 1.850;
		sn = {m: .19, A0: 1, A1000: 15.74, C: 3.51};
		break;
		case 6: t_k = 1.486; t_m = 4.890; t_b = 1.850;
		sn = {m: .19, A0: 1, A1000: 13.85, C: 3.51};
		break;
		case 7: t_k = 1.180; t_m = 4.890; t_b = 1.850;
		sn = {m: .19, A0: 1, A1000: 11.00, C: 3.51};
		break;
		case 8: t_k = 1.688; t_m = 4.890; t_b = 1.850;
		sn = {m: .19, A0: 1, A1000: 15.74, C: 3.51};
		break;
		case 9: t_k = 1.486; t_m = 4.890; t_b = 1.850;
		sn = {m: .19, A0: 1, A1000: 13.85, C: 3.51};
		break;
		case 10: t_k = 1.052; t_m = 4.774; t_b = 1.774;
		sn = {m: .226, A0: 0, A1000: 13.44, C: 1};
		break;
		case 11: t_k = 1.144; t_m = 4.774; t_b = 1.774;
		sn = {m: .226, A0: 0, A1000: 14.61, C: 1};
		break;
	}


	if(document.querySelector('#method').value === 'shev') {
		// Шевелев
		[].forEach.call(document.querySelectorAll('.teor,.snip'), function(i) {
			// i.style.border = '2px solid red';
			if(i.style.display !== 'none') i.od = i.style.display;
			i.style.display = 'none';
		});
		[].forEach.call(document.querySelectorAll('.shev'), function(i) {
			i.style.display = i.od || '';
		});

		// 1000i
		i1000 = t_k * (Math.pow(q, t_b) / Math.pow(d_y, t_m));

		Dp_Pa = i1000 * 9.81 * L;
		Dp = Dp_Pa * 1.0197162e-5;

	} // Шевелев
	else if(document.querySelector('#method').value === 'teor') {

		[].forEach.call(document.querySelectorAll('.shev,.snip'), function(i) {
			if(i.style.display !== 'none') i.od = i.style.display;
			i.style.display = 'none';
		});
		[].forEach.call(document.querySelectorAll('.teor'), function(i) {
			i.style.display = i.od || '';
		});

		// кинематический коэффициент вязкости рабочей жидкости, м2/с
		var nu = .0178/(1+.0337*t_mid+.000221*Math.pow(t_mid,2)) / 1e4,
			// л/с
			G = q/ro * 1000,
			Re = v * d_y / nu,
			// К-т гидравлического трения
			// ламинарное (Пуазейль) - Re < 2320
			// турбулентное (Блазиус) - 2320<Re<10^5
			// (Альтшуль) Re > 4000
			// (Шифринсон) Re > 500 * d_y/sher
			lamb = Re<=2320 ? 75/Re : Re<=4000 ? .3164/Math.pow(Re, .25) : Re <= 500 * d_y/sher ? 0.11*Math.pow(68/Re+sher/d_y, 0.25) : 0.11*Math.pow(sher/d_y, 0.25),
			// Удельные потери давления на трение кг/(см2*м)
			R = lamb*Math.pow(v, 2)*ro/2/9.81/d_y/10,
			// Потери давления на трение кг/см2
			Dp_tr = R * L,
			// Потери давления в местных сопротивлениях  кг/см2
			Dp_ms = sum_ksi*Math.pow(v, 2)*ro*1000/2/9.81/10000,
			// Потери давления в трубопроводе кг/см2
			Dp = Dp_tr + Dp_ms,
			// -> Па
			Dp_Pa =Dp*9.81*10000;

			// console.log('d_y/sher = ', d_y/sher);

	} // snip
	else if(document.querySelector('#method').value === 'snip') {

		document.querySelectorAll('.shev,.teor').forEach(function(i) {
			if(i.style.display !== 'none') i.od = i.style.display;
			i.style.display = 'none';
		});
		document.querySelectorAll('.snip').forEach(function(i) {
			i.style.display = i.od || '';
		});

		var m = sn.m, A0 = sn.A0, A1000 = sn.A1000, C = sn.C;

		i1000 = A1000/19.61*Math.pow((A0+C/v), m)/Math.pow(d_y, (m+1))*Math.pow(v, 2);
		Dp_Pa = i1000 * 9.81 * L;
		Dp = Dp_Pa * 1.0197162e-5;
	}


	S = Dp_Pa / Math.pow(q *3600, 2);

	if(!di) trass({
		ro : 'Плотность воды при t<sub>ср</sub>, т/м<sup>3</sup>',
		v : 'Скорость воды, м/с',
		// teor
		nu : 'Кинематический к-т вязкости воды (при t<sub>ср</sub>)',
		G : 'Расход воды при t<sub>ср</sub>, л/с',
		Re : 'Число Рейнольдса (определяет характер движения жидкости)',
		lamb : 'К-т гидравлического трения',
		// snip
		m : 'm',
		A0 : 'A<sub>0</sub>',
		A1000 : '1000A<sub>1</sub>',
		C : 'C',
		//
		i1000 : 'К-т гидравлического сопротивления  <b>1000i</b>, мм.вод.ст./м',
		Dp_tr : 'Потери давления на трение, кг/см<sup>2</sup>',
		Dp_ms : 'Потери давления в местных сопротивлениях, кг/см<sup>2</sup>',
		Dp : '<b>Потери давления в трубопроводе, кг/см<sup>2</sup></b>',
		Dp_Pa : '<b>Потери давления в трубопроводе, Па</b>',
		S : '<b>Характеристика гидравлического сопротивления, Па/(т/ч)<sup>2</sup></b>',
	});

	function trass (obj) {
		var pn = document.querySelector('#trass');
		pn.innerHTML = '';
		Object.keys(obj).forEach(function(i) {
			if(!eval(i)) return;
			var li = document.createElement('li');
			li.innerHTML = obj[i] + ' = ' + eval(i).toFixed(3);
			pn.appendChild(li);

			if(i === 'v' && (eval(i) < 0.25 || eval(i) > 1.5) ||
				i === 'Re' && (eval(i) > 2300 && eval(i) < 4000)
			) {
				li.classList.add('err');

			} else li.classList.remove('err');

		});

	}

	return {
		q : q,
		range_d : range_d,
		d_y : d_y,
		L : L,
		v : v,
		Re : Re,
		Dp : Dp,
		Dp_Pa : Dp_Pa
	}

}



graph();

function graph () {
	var canvas = document.querySelector('#graph'),
	ctx = canvas.getContext("2d"),
	rez = calc(),
	pad = 25,
	w = canvas.width - 2*pad,
	h = canvas.height - 2*pad,

	sc_x = Math.abs(rez.range_d[0]-rez.range_d[1])/w,

	dx = w/30,
	dy = h/10,

	dd = dx * sc_x;

	// console.log(TOOLs.printObj(rez), dx, dd);
	ctx.beginPath();

	ctx.clearRect(0,0,canvas.width,canvas.height)

	ctx.translate(pad, h+pad);

	// BG field
	ctx.fillStyle = '#eee';
	ctx.fillRect(0,0,w,-h);
	// zero point
	ctx.fillStyle = 'green';
	ctx.arc(w,0,5,0, 2*Math.PI);
	ctx.fill();



	var his = {
		mainGr : [],
	}


	for (var i=0; i <= w; i+=dx ) {
		var di = rez.range_d[0] - i*sc_x,
			rezi = calc(di),
			Dpi = rezi.Dp_Pa,
			// Dpi_ud = Dpi / rez.L,
			ord = Math.log(Dpi)*20
			// ord = Math.log(Dpi)*25 - Math.log(rezi.L)*10;


		ctx.beginPath();
		ctx.moveTo(his.mainGr[0]||0, his.mainGr[1]||-ord);

		// Переход в турбулентность
		ctx.lineWidth = 3;
		if(rezi.Re > 2320 && rezi.Re < 4000) ctx.strokeStyle = 'red';
		else ctx.strokeStyle = 'brown';
		ctx.lineTo(i, -ord);
		his.mainGr[0] = i;
		his.mainGr[1] = -ord;

		ctx.stroke();

		// ctx.beginPath();
		ctx.strokeStyle = "olive";
		ctx.lineWidth = 1;
		ctx.font='14px arial';

		// Диаметры
		if(i%10===0) {
			ctx.textBaseline="top";
			ctx.fillStyle = (rezi.v < 3 && rezi.v > .15) ?  (rezi.v < 1.5 && rezi.v > .25) ? 'green' : '#cb2' : 'red';
			ctx.textAlign = 'center';
			ctx.fillText((di*1e3).toFixed(0), i,10);
			ctx.moveTo(i,0);
			ctx.lineTo(i,-h);
		}


		// Па / кПа
		if(i%20===0) {
			ctx.textBaseline="bottom";
			ctx.textAlign = 'left';
			ctx.fillStyle = '#000';
			ctx.fillText(Dpi > 1e3 ? (Dpi/1000).toFixed(0) + ' кПa' : Dpi.toFixed(0) + ' Пa', -pad, -ord);
			ctx.textAlign = 'right';
			ctx.fillText(rezi.v.toFixed(2) + ' м/с', w+pad, -ord);

			ctx.moveTo(0,-ord);
			ctx.lineTo(w,-ord);

		}

		ctx.stroke();


	} // for

	ctx.beginPath();
	ctx.fillStyle = '#119';
	ctx.arc((rez.range_d[0] - rez.d_y)/sc_x,-Math.log(rez.Dp_Pa)*20,5,0, 2*Math.PI);
	ctx.fill();


	ctx.font='18px arial';
	ctx.textBaseline="bottom";
	ctx.fillStyle = '#159';
	ctx.fillText('Диаметры, мм', w/2, -5);
	ctx.textBaseline="top";
	ctx.textAlign = 'left';
	ctx.fillText('Pпот, (к)Па', -pad, -h-pad);
	ctx.textAlign = 'right';
	ctx.fillText('v, м/с', w+pad, -h-pad);


	ctx.stroke();

	ctx.translate(-pad, -(h+pad));
}