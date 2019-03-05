'use strict';
var epure_tmp = {};

// add inputs
function addMore (node) {
	var li = node.cloneNode(true);
	if(node.querySelectorAll('button').length < 2) li.innerHTML += '<button onclick="this.parentNode.remove(); draw();">-</button>';
	node.parentNode.insertBefore(li, node.nextSibling);
	// node.Append(li, 'after');
}

draw();

// main function
function draw() {
	var canvas = document.getElementById("beam");
	if (!canvas.getContext) return;

	var ctx = canvas.getContext("2d");

	// ctx.beginPath();
	ctx.clearRect(0, 0, canvas.width, canvas.height);
/* 	ctx.translate(canvas.width, 0)
	ctx.scale(-1,1); */


	var pad_L = 70, // offset X
		pad_R = 20,
		draw_field = canvas.width - pad_L - pad_R,
		L_b = limit_range('L_b', 0, 100)[0],

		// step diff
		delt = 5,
		// boundary conditions
		b_conds = document.querySelector('#b_conds').value,

		L_cons = b_conds === 'consol' ? 0 : limit_range('L_cons', 0, L_b/2)[0],
		R_cons = b_conds === 'consol' ? 0 : limit_range('R_cons', 0, L_b/2)[0],

		// the main conversion coefficient values in the graph
		kt_mx = draw_field / (L_cons + R_cons + L_b),

		L_b_px = L_b * kt_mx,
		L_cons_px = kt_mx *L_cons,
		R_cons_px = kt_mx *R_cons,

		// coords of beam
		x1_b = pad_L + L_cons * kt_mx,
		x2_b = x1_b + L_b_px,
		y_b = 60,

		// load value & coord
		q = limit_range('q', -1e5, 1e5)[0],
		x1_q = limit_range('x1_q', -L_cons, L_b + R_cons - delt/kt_mx)[0],
		x2_q = limit_range('x2_q', x1_q + delt/kt_mx, L_b + R_cons)[0],
		L_q = x2_q - x1_q,
		P = limit_range('P', -1e5, 1e5),
		x_P = limit_range('x_P', -L_cons, L_b + R_cons),
		x_P_px = [],
		M = limit_range('M', -1e5, 1e5)[0],
		x_M = limit_range('x_M', -L_cons, L_b + R_cons)[0],
		x_M_px = x_M * kt_mx,

		// supports reactions
		Rv1_q = q * L_q * (1 - (x2_q + x1_q) /2 / L_b),
		Rv2_q = q * L_q - Rv1_q,
		Rv1_P = [0], Rv2_P = [0],
		Rv1_P_sum = 0,
		Rv2_P_sum = 0,
		Rv1_M = M / L_b,
		Rv2_M = -M / L_b,
		Rv1 = 0,
		Rv2 = 0,

		// supports geom
		r_op = 5,
		h_op = 15,
		h_q = 20,
		// высота эпюры px
		h_ep = 75,
		// SUM offsets
		offset_x = 0,
		offset_y = 0,

		// decor
		col_bg = '#eee',
		col_load = "red",
		col_notes = "olive",

		maxM = 0,
		maxQ = 0
	;


	// console.log('L_b ', L_b, L_b_px);
	// console.log('consoles ', kt_mx, L_cons, L_cons_px, "\n", R_cons, R_cons_px);


	ctx.strokeStyle = ctx.fillStyle = col_bg;
	// ctx.fillRect(pad_L, 0, draw_field, canvas.height);
	ctx.fillRect(0, 0, canvas.width, canvas.height);

	// limit
	function limit_range (id, min, max) {
		var _$ = document.getElementById(id) || document.querySelectorAll('body .' + id), r_arr = [];
		if(!_$.length) _$ = [_$];
		// console.log(_$);
		[].forEach.call(_$, function(i, ind) {
			var $ = i.value.replace(',','.');
			if($ != '' || $ != '.') {
				$ = +$;

				if(max !== undefined && $ > max) {
					$ = max;
				} else if(min !== null && $ < min) {
					$ = min;
				}
			}

			if(!isNaN($)) {
				i.value = $;
				r_arr[ind] = $;
			} else r_arr[ind] = 0;
		});

		return r_arr;

	}


	// 1 support
	offset_x += x1_b; offset_y += y_b;
	ctx.translate(x1_b, y_b);

	ctx.strokeStyle = "black";
	ctx.lineWidth = 4;

	if(b_conds === 'consol') {

		ctx.beginPath();
/* 		L_cons = L_b;
		L_cons_px = L_b_px;*/
		R_cons = R_cons_px = 0;

		ctx.moveTo(0, 0);
		ctx.lineTo(L_b_px, 0);
		ctx.stroke();
		document.querySelector('#L_cons').parentNode.hidden = document.querySelector('#R_cons').parentNode.hidden = 1;

		ctx.fillStyle = 'black';
		ctx.fillRect(L_b_px, 2*h_op, 10, -4*h_op);

	} else if (b_conds === 'hinge') {

		// Main beam & consoles
		ctx.beginPath();
		ctx.moveTo(-L_cons_px, 0);
		ctx.lineTo(L_b_px + R_cons_px, 0);
		ctx.stroke();
		// Supports
		ctx.beginPath();
		document.querySelector('#L_cons').parentNode.hidden = document.querySelector('#R_cons').parentNode.hidden = 0;
		ctx.arc(0, r_op,r_op,0,2*Math.PI);

		ctx.moveTo( -h_op, 2*r_op + h_op);
		ctx.lineTo(0, 2*r_op);
		ctx.lineTo(h_op, 2*r_op + h_op);
		ctx.closePath();
		ctx.stroke();


		// c возвратом, на 2 опору
		ctx.translate(L_b_px, 0);
		ctx.beginPath();
		ctx.arc(0, r_op,r_op,0,2*Math.PI);

		ctx.moveTo( -h_op, 2*r_op + h_op);
		ctx.lineTo(0, 2*r_op);
		ctx.lineTo(h_op, 2*r_op + h_op);
		ctx.closePath();
		ctx.stroke();

		ctx.lineWidth = 3;
		ctx.moveTo( -h_op * 1.5, 2*r_op + h_op * 1.4);
		ctx.lineTo( h_op * 1.5, 2*r_op + h_op * 1.4);
		ctx.translate(-L_b_px, 0);
		ctx.stroke();
	}


	// grids
	ctx.beginPath();
	ctx.lineWidth = 1;
	ctx.strokeStyle = "grey";
  ctx.moveTo(0, 0);
	ctx.lineTo(0, canvas.height);
	ctx.moveTo(L_b_px, 0);
	ctx.lineTo(L_b_px, canvas.height);
  ctx.stroke();


	// НАГРУЗКИ
	ctx.beginPath();
	// offset_x += x2_b; offset_y += y_b;
	// ctx.translate(x1_b, y_b);

	ctx.strokeStyle = ctx.fillStyle = col_load;
	ctx.font="25px Arial";

	if(q) {
		// ctx.beginPath();
		ctx.textBaseline="bottom";
		ctx.fillText("q=" + q,(x1_q + x2_q)*kt_mx/2,-h_q * 1.1);
		ctx.moveTo(x1_q*kt_mx, -h_q);
		ctx.lineTo(x2_q*kt_mx, -h_q);

		for(var i = x1_q*kt_mx; i <= x2_q*kt_mx; i += L_b_px/10) {
			ctx.moveTo(i, -h_q);
			ctx.lineTo(i, 0);
			arrow(h_q, i, Math.sign(q));
		}

		notePos(x1_q, .5*h_q);
		notePos(x2_q, .5*h_q);

/* 		ctx.beginPath();
		ctx.font="14px Arial";
		ctx.textAlign = 'center';
		ctx.textBaseline="top";
		ctx.fillText(x1_q.toFixed(1) ,x1_q*kt_mx, .5*h_q);
		ctx.fillText(L_q.toFixed(1) ,x2_q*kt_mx, .5*h_q); */

	} // q


	if(P[0]) {
		// console.log(P);
		P.forEach(function(P, ind) {
			ctx.beginPath();
			ctx.strokeStyle = ctx.fillStyle = "red";
			ctx.font="25px Arial";
			ctx.textBaseline="middle";
			ctx.textBaseline="bottom";

			Rv1_P_sum += Rv1_P[ind] = P * (1 - x_P[ind] / L_b);
			Rv2_P_sum += Rv2_P[ind] = P - Rv1_P[ind];

			x_P_px[ind] = x_P[ind] * kt_mx;

			ctx.fillText("P=" + P, x_P_px[ind], -h_q*1.5);

			ctx.lineWidth = 3;
			ctx.moveTo(x_P_px[ind], -h_q*2);
			ctx.lineTo(x_P_px[ind], 0);
			arrow(h_q*2, x_P_px[ind], Math.sign(P));

			notePos(x_P[ind], 1*h_q);

			})

	} // P


	if(M) {
		ctx.beginPath();
		ctx.strokeStyle = ctx.fillStyle = "red";
		ctx.font="25px Arial";
		ctx.textBaseline="middle";
		ctx.textAlign = "center";
		ctx.fillText("M=" + M, x_M_px-h_q, -h_q);

		ctx.lineWidth = 3;
		ctx.moveTo(x_M_px, 0);
		ctx.lineTo(x_M_px + h_q*1.5, -h_q*2);
		ctx.arcTo(x_M_px, -h_q*3, x_M_px - h_q*1.5, -h_q*2, h_q*3);
		ctx.translate(x_M_px-h_q*1.5, -h_q*2);
		ctx.rotate(Math.PI/3);
		arrow(h_q*1.5, 0, Math.sign(M));
		ctx.rotate(-Math.PI/3);
		ctx.translate(-x_M_px+h_q*1.5, h_q*2);

		notePos(x_M, 1.5*h_q);
	}

	ctx.stroke();

	Rv1 = Rv1_q + Rv1_P_sum + Rv1_M,
	Rv2 = Rv2_q + Rv2_P_sum + Rv2_M;

	document.querySelector('#Rv1').innerHTML = Rv1.toFixed(2);
	document.querySelector('#Rv2').innerHTML = Rv2.toFixed(2);


	function arrow (h, x, sign) {
		ctx.stroke();
		ctx.beginPath();
		ctx.strokeStyle = ctx.fillStyle = col_load;

		if(sign < 0) ctx.scale(1, -1);

		ctx.moveTo(x - h*.2, -h * .4);
		ctx.lineTo(x + h*.2, -h * .4);
		ctx.lineTo(x, 0);
		ctx.closePath();
		ctx.fill();

		if(sign < 0) ctx.scale(1, -1);
	}

	function notePos (x_pos, h) {
		h = h || h_q;

		ctx.lineWidth = 1;
		ctx.strokeStyle = ctx.fillStyle = col_notes;
		ctx.moveTo(x_pos * kt_mx, -h_q);
		ctx.lineTo(x_pos * kt_mx, canvas.height);
		ctx.stroke();

		ctx.beginPath();
		ctx.font="14px Arial";
		ctx.textAlign = 'center';
		ctx.textBaseline="top";
		ctx.fillText(x_pos.toFixed(1) ,x_pos*kt_mx, h);
	}

	function txtLen (txt, dec) {
		if(typeof txt === 'number') txt = txt.toFixed(dec || 1);
		return ctx.measureText(txt).width;
	}

	function signColor (a) {
		return ctx.strokeStyle = ctx.fillStyle = a > 0 ? "red" : "blue";
	}



	// ЭПЮРЫ

	function epure (fn, arr_max, name, plot_sign) {

		arr_max = arr_max.map(function(i) {
			return Math.abs(i);
		});
		// plot_sign = -1 меняет знаки эпюры
		plot_sign = plot_sign || 1;
		// arr_max.push(.3*h_ep);
		// console.log('arr_max= ', arr_max);
		var tmp_arr = [], dy,
			// к-т отношения высоты графика к величине напряжения
			kt_y = Math.max.apply(null, arr_max)/h_ep;

		// console.log('kt_y = ', kt_y, arr_max);

		ctx.stroke();
		ctx.beginPath();

		// 0 - line
		ctx.lineWidth = 1;
		ctx.strokeStyle = "olive";
		ctx.moveTo(-L_cons_px, 0);
		ctx.lineTo(L_b_px + R_cons_px, 0);
		ctx.stroke();

		ctx.beginPath();
		ctx.strokeStyle = "blue";
		ctx.lineCap="round";


		for(var i = -L_cons_px; i <= L_b_px + R_cons_px; i += delt) {

			if(isNaN(dy = fn(i, kt_mx))) continue;

			var dy_px = dy / kt_y;
			// console.log(i - delt, dy, kt_y);

			// fill on P
			var dl_1 = tmp_arr[tmp_arr.length - 1] && tmp_arr[tmp_arr.length - 1][1] || 0,
				dl_2 = tmp_arr[tmp_arr.length - 2] && tmp_arr[tmp_arr.length - 2][1] || 0;

			ctx.beginPath();

			// Обработка скачков
			if(Math.abs((dy - dl_1) / (dl_1 - dl_2)) > 3
			|| i === 0|| i === L_b_px + R_cons_px) {
				signColor(dy * plot_sign);
				ctx.fillRect(i - delt, dl_1/kt_y, delt, (dy - dl_1)/kt_y);
				// console.log(i, i/kt_mx, dy_px, dl_1, dl_2);

				ctx.textAlign = "center";
				ctx.font="14px Arial";
				ctx.textBaseline = "middle";

				if(Math.abs(dy - dl_1)/kt_y > 7) {
/* 					ctx.fillStyle = "white";
					ctx.fillRect(i - txtLen(plot_sign * dy)/2, dy/kt_y + 19 * Math.sign(dy), txtLen(plot_sign * dy), 20); */

					signColor(dl_1 * plot_sign);
					ctx.fillText((plot_sign * dl_1).toFixed(1), i, dl_1/kt_y + 12 * Math.sign(dl_1));
					// ctx.fillText((plot_sign * fn(i - delt, kt_mx)).toFixed(1), i, dl_1/kt_y + 20 * Math.sign(dl_1));
				}

				signColor(dy * plot_sign);
				ctx.fillText((plot_sign * dy).toFixed(1), i, dy_px + 12 * Math.sign(dy));



			}


			ctx.beginPath();

			signColor(dy * plot_sign);
			ctx.moveTo(i - delt, dl_1/kt_y);
			ctx.lineWidth = 3;
			ctx.lineTo(i, dy_px);
			ctx.stroke();

			ctx.moveTo(i, 0);
			ctx.lineWidth = 1;
			ctx.lineTo(i, dy_px);
			ctx.stroke();

			// console.log(dy_px);
			tmp_arr.push([i,dy]);
		} // Main for



		var r_arr = [],
			absRes = (function() {
				var t = 0, r = [0,0];
				tmp_arr.forEach(function(i) {
					r_arr.push(i[1] * plot_sign);
					if(Math.abs(i[1]) > Math.abs(t)) {
						t = i[1];
						r = i;
					}
				});
				r[1] = Math.round(r[1]*10)/10;
				return r;
			})(),
			maxRes = Math.round(Math.max.apply(null, r_arr)*10)/10,
			minRes = Math.round(Math.min.apply(null, r_arr)*10)/10;


		ctx.textAlign = "left";
		ctx.textBaseline="bottom";
		ctx.font="30px Arial";
		ctx.fillStyle = "blue";
		ctx.fillText(name, -x1_b, 0);

		ctx.font="18px Arial";
		ctx.fillStyle = "red";
		ctx.fillText('max ' + maxRes, -x1_b, 25);
		ctx.fillStyle = "blue";
		ctx.fillText('min ' + minRes, -x1_b, 45);

		ctx.fillStyle = col_bg;
		ctx.textAlign = "center";
		ctx.fillRect(absRes[0] - txtLen(absRes[1],3)/2, absRes[1] /kt_y + 5*Math.sign(absRes[1]), txtLen(absRes[1], 3), 22 * Math.sign(absRes[1]));
		// ctx.strokeRect(absRes[0] - txtLen(absRes[1],2)/2, absRes[1] /kt_y + 5*Math.sign(absRes[1]), txtLen(absRes[1], 2), 22 * Math.sign(absRes[1]));
		signColor(absRes[1] * plot_sign);
		ctx.textBaseline="middle";
		ctx.fillText(plot_sign * absRes[1], absRes[0], absRes[1] /kt_y + 15*Math.sign(absRes[1]));

		return maxRes;

	} // epure


	function M_q (dx, kt) {
		// console.log(Rv1_q, Q);
		// console.log('dx/kt = ', dx/kt, 'x1_q = ', x1_q);

		if((dx/kt < 0 ) || (b_conds === 'consol')) { // L_cons
			if(dx/kt <= x1_q) {
				return 0;
			} else if(dx/kt < x2_q) {
				// console.log(dx, dx/kt, (x2_q + x1_q) /2, (dx/kt - (x2_q + x1_q)) /2);
				return -q * Math.pow(dx/kt - x1_q, 2) /2;
			} else return -q * L_q * (dx/kt - (x2_q + x1_q) /2)

		} else if((dx)/kt <= L_b) {
			if (dx/kt < x1_q) {
				return Rv1_q * dx/kt;
			} else if (dx/kt <= x2_q) {
				return Rv1_q * dx/kt - q * Math.pow((dx/kt - x1_q), 2) / 2;
			} else return Rv1_q * dx/kt - q * L_q * (dx/kt - (x2_q + x1_q) /2);

		} else {
			if (dx/kt <= x2_q) {
				return -q* Math.pow(x2_q - dx/kt, 2) /2;
			} else return 0;

		}

	}


	function M_P (ind, dx, kt) {
		// console.log('dx = ', dx);

		if((dx < 0) || (b_conds === 'consol')) { // L_cons
			if(dx/kt > x_P[ind]) {
				return -P[ind] * Math.abs(dx / kt - x_P[ind]);
			} else return 0;

		} else if((dx)/kt <= L_b) { // Main beam
			if(dx/kt <= x_P[ind]) {
				// before P[ind]
				return Rv1_P[ind] * dx/kt;
			} else if(dx/kt > x_P[ind]) {
				// after P[ind]
				return Rv1_P[ind] * dx/kt - P[ind] * (dx/kt - x_P[ind]);
			}

		} else {
			if(dx/kt <= x_P[ind]) return -P[ind] * (x_P[ind] - dx/kt);
			else return 0;
		}

		// console.log('P[ind] ', dx/kt, x_P[ind]);
	}


	function M_M (dx, kt) {
		// console.log('dx = ', dx);

		if((dx < 0) || (b_conds === 'consol')) { // L_cons
			if(dx/kt > x_M) {
				return M;
			} else return 0;

		} else if(dx/kt <= L_b) { // Main beam
			if(dx/kt <= x_M) {
				// before P
				return Rv1_M * dx/kt;
			} else if(dx/kt > x_M) {
				// after P
				return Rv1_M * dx/kt - M;
			}

		} else {
			// R_cons
			if(dx/kt <= x_M) return M;
			else return 0;
		}

		// console.log('P ', dx/kt, x_P);
	}


	function M_SUM (dx, kt) {
		var y = 0;
		kt = kt || 1;

		if(q) {
			y += M_q(dx, kt);
		}
/* 		if(P) {
			y += M_P(dx, kt);
		} */
		if(P.length) {
			P.forEach(function(P, ind) {
				y += M_P(ind, dx, kt);
				// console.log(P, x_P[ind], y);
			})
		}

		if(M) {
			y += M_M(dx, kt);
		}

		// console.log('sum = ', y);
		return y;
	}


	function Q_q (dx, kt) {
		if((dx < 0) || (b_conds === 'consol')) { // L_cons
			if(dx/kt <= x1_q) {
				return 0;
			} else if(dx/kt <= x2_q) {
				return q*( dx/kt - x1_q);
			} else return q*L_q;

		} else if((dx)/kt <= L_b) {
			// Main beam
			if (dx/kt < x1_q) {
				// Q_SUM.P = 1;
				return -Rv1_q;
			} else if (dx/kt <= x2_q) {
				return -Rv1_q + q * (dx/kt - x1_q);
			} else return -Rv1_q + q * L_q;

		} else {
			// R_cons
			if (dx/kt <= x2_q) {
				return - q * ( x2_q - dx/kt);
			} else return 0;

		}
	}

	function Q_P (ind, dx, kt) {
		if((dx < 0) || (b_conds === 'consol')) { // L_cons
			if(dx/kt > x_P[ind]) {
				return P[ind];
			} else return 0;

		} else if(dx/kt <= L_b) {
			// Main beam
			if(dx/kt <= x_P[ind]) {
				// before P
				return -Rv1_P[ind];
			} else if(dx/kt > x_P[ind]) {
				// after P
				return -Rv1_P[ind] + P[ind];
			}

		} else {
			if(dx/kt <= x_P[ind]) return -P[ind];
			else return 0;
		}

	}

	function Q_M (dx, kt) {
		if((dx < 0) || (b_conds === 'consol')) { // L_cons
			return 0;

		} else if(dx/kt <= L_b) {
			// Main beam
			return -Rv1_M;

		} else {
			return 0;
		}

	}


	function Q_SUM (dx, kt) {
		kt = kt || 1;
		var y = 0;

		if(q) {
			y += Q_q(dx, kt);
		}

		if(P.length) {
			P.forEach(function(P, ind) {
				y += Q_P(ind, dx, kt);
				// console.log(P, x_P[ind], y);
			})
		}

		if(M) {
			y += Q_M(dx, kt);
		}
		// console.log('sum = ', y);
		return y;
	}


	// M эпюра

	offset_y += (y_b + h_ep) * 1.5;
	ctx.translate(0, (y_b + h_ep) * 1.5);

	// values

	maxM = (b_conds === 'consol') ? [q*L_b*L_b/2, Math.max.apply(null, P)*L_b, M] : [q*L_b*L_b/8, Math.max.apply(null, P)*L_b/4, M];
	// console.log(maxM);
	epure(M_SUM, maxM, "M");


	// Q эпюра

	offset_y += y_b + 2.2 * h_ep;
	ctx.translate(0, y_b + 2.2 * h_ep);

	// values
	maxQ = (b_conds === 'consol') ? [q*L_b/2, Math.max.apply(null, P), Rv1, Rv2] : [q*L_b/2, Math.max.apply(null, P), Rv1, Rv2];
	epure(Q_SUM, maxQ, "Q", -1);
	// console.log(ctx.measureText(maxQ));

	ctx.translate(-offset_x, -offset_y);

}