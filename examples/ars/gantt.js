var YG, XG, W = 1100, H = 600, ColorFon = "#f1f1f1",
	div_asp_text = '<div class="asp_text" id="asp_text"><div id="but_close"></div></div>',
	Pla = ["Луна", "Солнце", "Меркурий", "Венера", "Марс", "Юпитер", "Сатурн", "Уран", "Нептун", "Плутон", "Асцендент", "Середина Неба"],
	PlaN = ["LU", "SU", "ME", "VE", "MR", "JU", "ST", "UR", "NE", "PL", "AS", "MC"],
	PlaPic = ["moon", "sun", "Mercury", "venus", "Mars", "Jupiter", "Saturn", "Uranus", "Neptune", "Pluto", "asc", "mc"],
	aspPic = ["0", "60", "90", "120", "180"],
	aspName = ["Соединение", "Секстиль", " Квадратура", "Тригон", "ОппозициЯ"],
	aspGr = [0, 60, 90, 120, 180], DomZ = ["", "&#8544;", "&#8545;", "&#8546;", "&#8547;", "&#8548;", "&#8549;", "&#8550;", "&#8551;", "&#8552;", "&#8553;", "&#8554;", "&#8555;"], Red = ["187,19,19", "204,36,36", "216,52,52", "255,0,0", "255,51,51", "255,76,76", "255,102,102", "255,127,127", "255,153,153", "255,178,178"], Green = ["0,102,0", "0,127,0", "0,153,0", "0,178,0", "0,204,0", "25,204,25", "25,229,25", "50,255,50", "102,255,102", "153,255,102"], Yell = ["ffbc3a", "ffda51", "f9e58a"], isNatClr = ["transparent", "blue", "red", "green"], kw = [5, 3, 4, 4, 5], ko = [1, .7, .8, .9, .95], ka = [0, 1, -1, 1, -1], orb = []; orb[0] = [0, 13.5, 10, 10, 10, 10.5, 10.5, 9.5, 8.5, 7], orb[1] = [13.5, 0, 11.5, 11.5, 11.5, 12, 12, 11, 10, 9], orb[2] = [10, 11.5, 0, 8, 8, 8.5, 8.5, 7.5, 6.5, 5], orb[3] = [10, 11.5, 8, 0, 8, 8.5, 8.5, 7.5, 6.5, 5], orb[4] = [10, 11.5, 8, 8, 0, 8.5, 8.5, 7.5, 6.5, 5], orb[5] = [10.5, 12, 8.5, 8.5, 8.5, 0, 9, 8, 7, 6], orb[6] = [10.5, 12, 8.5, 8.5, 8.5, 9, 0, 8, 7, 6], orb[7] = [9.5, 11, 7.5, 7.5, 7.5, 8, 8, 0, 6, 5], orb[8] = [8.5, 10, 6.5, 6.5, 6.5, 7, 7, 6, 0, 5], orb[9] = [7, 9, 5, 5, 5, 6, 6, 5, 5, 0], orb[10] = [7, 9, 5, 5, 5, 6, 6, 5, 5, 5], orb[11] = [7, 9, 5, 5, 5, 6, 6, 5, 5, 5]; var ConvP = [1, 0, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11], ConvT = [0, 1, 1, 2, -1, -1, 3, -1, -1, 1],
		IntervalPrognoza = 2,
		Date1 = new Date, dd1 = Math.floor((Date1.valueOf() - 9465912e5) / 864e5), z2 = Date1.getTimezoneOffset() / 60, Tema = 0, Clnd = 0, Ves = 0, Targ = !1, Vesa = [0, 5, 7, 9], As = [0], AspTransOn = 0, Bt = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], DomN = []; for (i = 0; i <= 9; i++)DomN[i] = PlaHouse(natal[i], Dom, 0); function ChangeBackground(e, t) { switch (e) { case "tema": document.getElementById(e + "0").style.backgroundColor = "transparent", document.getElementById(e + "1").style.backgroundColor = "transparent", document.getElementById(e + "2").style.backgroundColor = "transparent", document.getElementById(e + "3").style.backgroundColor = "transparent", document.getElementById(e + "6").style.backgroundColor = "transparent"; break; case "v": document.getElementById(e + "0").style.backgroundColor = "transparent", document.getElementById(e + "1").style.backgroundColor = "transparent", document.getElementById(e + "2").style.backgroundColor = "transparent", document.getElementById(e + "3").style.backgroundColor = "transparent"; break; case "in": document.getElementById(e + "3").style.backgroundColor = "transparent", document.getElementById(e + "7").style.backgroundColor = "transparent", document.getElementById(e + "30").style.backgroundColor = "transparent", document.getElementById(e + "100").style.backgroundColor = "transparent", document.getElementById(e + "366").style.backgroundColor = "transparent", document.getElementById(e + "731").style.backgroundColor = "transparent" }try { document.getElementById(e + t).style.backgroundColor = "#d9ecfa" } catch (e) { return } } function SetCalendar(e) { GrafikPaint() } function SetInterval(e) { 0 == ephm && (e = 3, NewDateEnter()), IntervalPrognoza = e, ChangeBackground("in", e), SetCalendar(Clnd) } function MoveInterval(e) { dd1 += e * IntervalPrognoza, ephm > 0 ? SetCalendar(Clnd) : NewDateEnter() } function SetTema(e) { Tema == e ? SetMode() : (Tema = e, Clnd = 2, GrafikPaint()), ChangeBackground("tema", e) } function SetMode() { SetCalendar(Clnd) } function SetVes(e) { Ves = e, ChangeBackground("v", e), GrafikPaint() } function SetTarget() { Targ = !Targ, document.getElementById("trg").style.backgroundColor = Targ ? "#d9ecfa" : "transparent", GrafikPaint() } function NewDateEnter() { var e = document.getElementById("NewDate"); if (e.style.left = Math.round((W - 300) / 2) + "px", e.style.display = "block" == e.style.display ? "none" : "block", 0 != ephm) { var t = new Date(9465912e5 + 864e5 * dd1); document.getElementById("ndd").selectedIndex = t.getDate() - 1, document.getElementById("ndm").selectedIndex = t.getMonth(), document.getElementById("ndy").selectedIndex = t.getFullYear() - 2017 } } function NewDate() { document.getElementById("NewDate").style.display = "none"; var e = document.getElementById("ndd").selectedIndex + 1, t = document.getElementById("ndm").selectedIndex + 1, a = document.getElementById("ndy").selectedIndex + 2017; dd1 = 367 * a - Math.floor(7 * (a + Math.floor((t + 9) / 12)) / 4) + Math.floor(275 * t / 9) + e - 730530, SetCalendar(Clnd) } function IsMainTema(e, t, a) { var n, o, r = 0; if (e >= 100) 0 == (o = e - 100 * (n = Math.floor(e / 100))) ? r = B[t][n] + B[a][n] + Bt[n] : B[t][n] + B[a][n] + Bt[n] > 0 && B[t][o] + B[a][o] + Bt[o] > 0 && (r = B[t][n] + B[a][n] + Bt[n] + B[t][o] + B[a][o] + Bt[o]); else switch (e) { case 0: r = 3; break; case 1: r = B[t][6] + B[t][10] + B[a][6] + B[a][10] + Bt[6] + Bt[10]; break; case 2: r = B[t][2] + B[a][2] + Bt[2]; break; case 3: r = B[t][5] + B[t][7] + B[a][5] + B[a][7] + Bt[5] + Bt[7]; break; case 5: r = B[t][5] + B[a][5] + Bt[5]; break; case 6: r = B[t][6] + B[t][12] + B[a][6] + B[a][12] + Bt[6] + Bt[12]; break; case 9: r = B[t][9] + B[t][3] + B[a][9] + B[a][3] + Bt[9] + Bt[3] }return r } function IsTarget(e, t, a) { var n, o, r = !1; if (a > 9 && (Bt = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], a), e >= 100) r = 0 == (o = e - 100 * (n = Math.floor(e / 100))) ? B[a][n] > 5 || Bt[n] > 0 : B[a][n] > 5 && Bt[o] > 0 || B[a][o] > 5 && Bt[n] > 0; else switch (e) { case 0: r = !0; break; case 1: r = B[a][6] > 5 || B[a][10] > 5 || Bt[6] + Bt[10] > 0; break; case 2: r = B[a][2] > 5 || Bt[2] > 0; break; case 3: r = B[a][5] > 5 || B[a][7] > 5 || Bt[5] + Bt[7] > 0; break; case 5: r = B[a][5] > 5 || Bt[5] > 0; break; case 6: r = B[a][6] > 5 || B[a][12] > 5 || Bt[6] + Bt[12] > 0; break; case 9: r = B[a][9] > 5 || B[a][3] > 5 || Bt[9] + Bt[3] > 0 }return r } function IsTema(e, t, a) { var n, o, r = 0; if (a > 9 && (Bt = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], t = a), e >= 100) (0 == (o = e - 100 * (n = Math.floor(e / 100))) && B[t][n] + B[a][n] + Bt[n] > 0 || B[t][n] + B[a][n] + Bt[n] > 0 && B[t][o] + B[a][o] + Bt[o] > 0) && (r = 2, r += (B[t][n] + B[t][o]) / 5, r += (B[a][n] + B[a][o]) / 5, r += (Bt[n] + Bt[o]) / 5); else switch (e) { case 0: r = 3; break; case 1: B[t][6] + B[t][10] + B[a][6] + B[a][10] + Bt[6] + Bt[10] > 0 && (r = 2, r += (B[t][6] + B[t][10]) / 5, r += (B[a][6] + B[a][10]) / 5, r += (Bt[6] + Bt[10]) / 5); break; case 2: B[t][2] + B[a][2] + Bt[2] > 0 && (r = 2, r += B[t][2] / 5, r += B[a][2] / 5, r += Bt[2] / 5); break; case 3: B[t][5] + B[t][7] + B[a][5] + B[a][7] + Bt[5] + Bt[7] > 0 && (r = 2, r += (B[t][5] + B[t][7]) / 5, r += (B[a][5] + B[a][7]) / 5, r += (Bt[5] + Bt[7]) / 5); break; case 31: B[t][5] + B[a][5] + Bt[5] > 0 && (r = 2, r += B[t][5] / 5, r += B[a][5] / 5, r += Bt[5] / 5); break; case 32: B[t][7] + B[a][7] + Bt[7] > 0 && (r = 2, r += B[t][7] / 5, r += B[a][7] / 5, r += Bt[7] / 5); break; case 33: B[t][7] + Bt[7] + B[t][5] + Bt[5] > 0 && B[a][3] > 0 && r++, B[t][7] + Bt[7] + B[t][5] + Bt[5] > 4 && B[a][3] > 4 && r++, B[t][3] + Bt[3] > 0 && B[a][5] + B[a][7] > 0 && r++, B[t][3] + Bt[3] > 4 && B[a][5] + B[a][7] > 4 && r++, B[t][7] + Bt[7] + B[t][5] + Bt[5] > 0 && B[a][1] > 0 && r++, B[t][1] + Bt[1] > 0 && B[a][5] + B[a][7] > 0 && r++, r > 0 && (r += 3); break; case 34: B[t][7] + Bt[7] + B[t][5] + Bt[5] > 0 && B[a][10] > 0 && r++, B[t][10] + Bt[10] > 0 && B[a][5] + B[a][7] > 0 && r++, r > 0 && B[t][1] + Bt[1] + B[a][1] > 0 && r++, r > 0 && B[t][4] + Bt[4] + B[a][4] > 0 && r++, r > 0 && (r += 3); break; case 5: B[t][5] + B[a][5] + Bt[5] > 0 && (r = 2, r += B[t][5] / 5, r += B[a][5] / 5, r += Bt[5] / 5); break; case 6: B[t][6] + B[t][12] + B[a][6] + B[a][12] + Bt[6] + Bt[12] > 0 && (r = 2, r += (B[t][6] + B[t][12]) / 5, r += (B[a][6] + B[a][12]) / 5, r += (Bt[6] + Bt[12]) / 5); break; case 9: B[t][9] + B[t][3] + B[a][9] + B[a][3] + Bt[9] + Bt[3] > 0 && (r = 2, r += (B[t][9] + B[t][3]) / 5, r += (B[a][9] + B[a][3]) / 5, r += (Bt[9] + Bt[3]) / 5) }return r }
function AspTrans(e, t, a, n, o, r) {
	if (AspTransOn != -(100 * n + 70 + a)) {
		var l, s; e.pageX || e.pageY ? (l = e.pageX, s = e.pageY) : (e.clientX || e.clientY) && (l = e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft) - document.documentElement.clientLeft, s = e.clientY + (document.documentElement.scrollTop || document.body.scrollTop) - document.documentElement.clientTop);
		var c = document.getElementById("asp_text");
		c.style.display = "block";
		c.style.width = "270px", s + 30 > YG + H && (s = YG + H - 30), s < YG && (s = YG + 5),
			c.style.top = s + 10 + "px", l + 270 > XG + W && (l = XG + W - 270), l < 0 && (l = XG - 10),
			c.style.left = l + 10 + "px",
			c.innerHTML = AspHead(t, a, n, o, r), AspTransOn = 100 * n + 70 + a
	}
}

function AspOut() { AspTransOn > 0 && (document.getElementById("asp_text").style.display = "none") }
function AspGraph(e, t, a, n, o, r, l, s) {
	var c, d; e.pageX || e.pageY ? (c = e.pageX, d = e.pageY) : (e.clientX || e.clientY) && (c = e.clientX + (document.documentElement.scrollLeft || document.body.scrollLeft) - document.documentElement.clientLeft, d = e.clientY + (document.documentElement.scrollTop || document.body.scrollTop) - document.documentElement.clientTop);

	var i = document.getElementById("asp_text"); i.style.display = "block", AspTransOn = -(100 * o + 70 + n);
	var p = Math.round(.45 * W); W < 500 ? p = W - 20 : p < 460 && (p = 460), i.style.width = p + "px", d + 450 > YG + H && (d = YG + H - 450), d < YG && (d = YG + 5), i.style.top = d + 10 + "px", c + p > XG + W && (c = XG + W - p), c < 0 && (c = XG - 10), screen.width < 750 && (c = Math.round(XG - 10 + (W - p) / 2)), i.style.left = c + 10 + "px", i.innerHTML = '<div id="but_close" onclick="ClearAspText()">X</div>' + aspStr(t, a, n, o, r, l, s), document.getElementById("asptitl").style.width = p - 60 - 30 - 25 + "px"
}
function aspStr(e, t, a, n, o, r, l) {
	var s, c = 2; o > 0 ? c = 0 : o < 0 && (c = 1);

	var d = Tema, i = "", p = "", B = '<div class="asp_img">'; B += '<img src="/eee/img/plasymb/' + PlaPic[e] + '.png" alt="' + Pla[e] + '" onclick="AspHelpText(0,' + e + ')">', B += '<img src="img/plasymb/' + aspPic[a] + '.png" alt="' + aspName[a] + '" onclick="AspHelpText(1,' + a + ')">', B += '<img src="img/plasymb/' + PlaPic[n] + '.png" alt="' + Pla[n] + '" onclick="AspHelpText(2,' + n + ')"><br>', B += '<span onclick="AspHelpText(3,' + t + ')">' + DomZ[t] + "</span> ", B += '<span onclick="AspHelpText(4,' + DomN[e] + ')">' + DomZ[DomN[e]] + "</span> ", B += '<span onclick="AspHelpText(4,' + DomN[n] + ')">' + DomZ[DomN[n]] + "</span><br>", B += '<div class="asp_star"> ' + AspForce(o) + "</div></div>", p21 = 0 == n ? 1 : 1 == n ? 0 : n; var g = 100 * (c + 1) + 10 * e + p21; if (600 != d && 1200 != d || (d = 6), (d >= 200 && d < 300 || d >= 800 && d < 900) && (d = 2), p = 0 == d ? void 0 === atx[g] ? asptxt[0][e][c] + asptxn[0][n][c] : atx[g] : d >= 100 ? asptxt[0][e][c] + asptxn[0][n][c] : asptxt[d][e][c], B += '<div class="asp_titl' + c + '" id="asptitl">' + AspHead(e, a, n, r, l) + p, 0 == d && Math.abs(o) > 2) { for (i = '<hr> <div class="asp_dop"> —феры: &nbsp;&nbsp;', s = 1; s < 10; s++)(Bt = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0])[t] = 5, kAsp(s, e, a, n) > 2 && "" != tema_name[s] && (i += '<a onclick="AspDopText(' + s + "," + e + "," + c + "," + n + "," + a + ')"><img src="img/iconk/' + tema_pic[s] + '.png" alt="' + tema_name[s] + '"></a> '); i += "</div>" } return i += '<div id="asp_dop_text"></div>', (ConvT[d] > 0 || 0 == d && e > 0) && n < 10 && (ephm > 0 && (B += ' <img src="img/iconk/book.png" width="16" style="vertical-align: middle;" onclick="AspRequest(' + a + "," + e + "," + ConvP[n] + "," + ConvT[d] + ',0)">'), (0 == ephm || W < 700) && (B += ' <a =' + a + "&t=" + e + "&n=" + ConvP[n] + "&tema=" + ConvT[d] + '" target="_blank" title="дополнительный текст откроетс¤ на отдельной странице"><img src="img/iconk/book.png" width="16" style="vertical-align: middle;"></a>')), B += "</div>" + i
}

function AspHead(e, t, a, n, o) {
	var r, l, s; r = ""; var c = [" ¤нвар¤", " феврал¤", " марта", " апрел¤", " ма¤", " июн¤", " июл¤", " августа", " сент¤бр¤", " окт¤бр¤", " но¤бр¤", " декабр¤"], d = new Date(Date1.valueOf() + 24 * n * 60 * 60), i = new Date(Date1.valueOf() + 24 * o * 60 * 60); o = Math.round(o / 1e3); var p = d.getMonth(), B = i.getMonth(), g = d.getFullYear(), m = i.getFullYear(); return 0 == e ? r = "c " + d.getHours() + " до " + i.getHours() + " часов" : g != m ? (l = "c " + d.getDate() + c[p] + " " + g + " г.", s = " до " + i.getDate() + c[B] + " " + m + " г.") : p == B ? (r = 0 == n ? "c начала периода" : "c " + d.getDate(), o == IntervalPrognoza ? 0 == n ? r += " до конца периода" : r = r + c[p] + " до конца периода" : r = r + " до " + i.getDate() + c[p] + " " + g + " г.") : (l = "c " + d.getDate() + c[p], s = " до " + i.getDate() + c[B] + " " + g + " г."), 0 == n && (l = "c начала периода"), o == IntervalPrognoza && (s = " до конца периода"), "" == r && (r = l + s), r = "<p>" + ["Соединение", '<span class="green">Секстиль</span>', '<span class="red"> Квадратура</span>', '<span class="green">Тригон</span>', '<span class="red">Оппозици¤</span>'][t] + " " + Pla[e] + " - " + Pla[a] + "<br /><i>" + r + "</i></p>"
}

function AspForce(e) {
	return e = Math.round(Math.abs(e) + 1), akb = e < 10 ? 10 + e : "20", '<img src="img/stars/star0' + akb + '.png" alt="' + e / 2 + '">' }
function AspImg(e, t, a) {
	var n = '<div class="asp_img_sm">'; return n += '<img src="img/plasymb/' + PlaPic[e] + '.png" alt="' + Pla[e] + '">', n += '<img src="img/plasymb/' + aspPic[t] + '.png" alt="' + aspName[t] + '">', n += '<img src="img/plasymb/' + PlaPic[a] + '.png" alt="' + Pla[a] + '"></div>' } function AspDopText(e, t, a, n, o) { var r = document.getElementById("asp_dop_text"); r.style.paddingLeft = "60px"; var l = '<hr><div id="doptitl" class="asp_titl' + a + '">' + asptxt[e][t][a]; (ConvT[e] > 0 || 0 == e && t > 0) && n < 10 && (ephm > 0 ? l += ' <img src="img/iconk/book.png" width="16" style="vertical-align: middle;" onclick="AspRequest(' + o + "," + t + "," + ConvP[n] + "," + ConvT[e] + ',1)">' : l += ' <a ' + o + "&t=" + t + "&n=" + ConvP[n] + "&tema=" + ConvT[e] + '" target="_blank" title="дополнительный текст откроетс¤ на отдельной странице"><img src="img/iconk/book.png" width="16" style="vertical-align: middle;"></a>'), r.innerHTML = l + "</div>" } function AspHelpText(e, t) { var a = document.getElementById("asp_dop_text"), n = HelpText[e][t]; a.style.paddingLeft = "0px", a.innerHTML = '<hr><div class="asp_titl2">' + n + "</div>" } function ClearAspText() { var e = document.getElementById("asp_text"); "block" === e.style.display && (e.style.display = "none") } function BColor(e) { return (e = Math.round(Math.abs(e))) > 10 && (e = 10), e } function revm(e) { var t = e; return e > 360 && (t = e - 360 * Math.floor(e / 360)), t } function PlaHouse(e, t, a) { var n, o, r, l; for (n = 1; n <= 12; n++)if (o = 12 != n ? n + 1 : 1, r = revm(t[n] - a), (l = revm(t[o] - a)) > r) { if (e >= r && e < l) return n } else if (e >= r && e < 360 || e >= 0 && e < l) return n }

function AspCalc(e, t) {
	var a = Math.abs(e - t); return a > 180 && (a = 360 - a), a
}

function WhatAsp(e, t) {
	return e >= 0 && e <= t ?
		0 : e >= 60 - t && e <= 60 + t ?
			1 : e >= 180 - t && e <= 180 ?
				4 : e >= 90 - t && e <= 90 + t ?
					2 : e >= 120 - t && e <= 120 + t ? 3 : 360
}

function NatAsp(e, t) {
	var a = AspCalc(natal[e], natal[t]), n = orb[e][t]; return a >= 0 && a <= n ? 0 : a >= 60 - n * ko[1] && a <= 60 + n * ko[1] ? 1 : a >= 180 - n * ko[4] && a <= 180 ? 4 : a >= 90 - n * ko[2] && a <= 90 + n * ko[2] ? 2 : a >= 120 - n * ko[3] && a <= 120 + n * ko[3] ? 3 : 360
}

function kAsp(e, t, a, n) {
	var o, r, l, s, c, d; return r = kw[a], l = IsTema(e, t, n), s = 0, t != n && (aspn = NatAsp(t, n), 360 != aspn && (a == aspn || 0 == aspn || pm == ka[aspn] ? s += 1 : s -= 1)), 0, c = 0, (Acc[t] >= 3 || Acc[n] >= 3) && (c = 2), Acc[t] >= 3 && Acc[n] >= 3 && (c = 3), n < 5 && c++, d = 0, (Ess[t] > 10 || Ess[n] > 10) && (d = 2), Ess[t] < -7 && (d += -1), o = (.2 * r + .8 * l) * (1 + s / 5) * 1 * (1 + c / 5) * (1 + d / 5), t > 4 && n < 2 && (o += 2), t >= 7 && o++, (o = Math.round(o)) > 10 && (o = 10), o
}

function AspImg1(e, t, a) {
	var n = '<div class="asp_img_sm">';
	return n += '<img src="" alt="">',
		n += '<img src="../img/aspects/' + aspPic[t] + '.png" alt="' + aspName[t] + '">',
		n += '<img src="../img/planets/' + PlaPic[e] + '.png" alt="' + Pla[a] + '"></div>'
}

function AspCalc1(e, t) {
	var a = Math.abs(e - t);
	return a > 180 ? 360 - a : a;
}

// old
function WhatAsp1(e, t) {
	return e >= 0 && e <= t ? 0 ://
		e >= 60 - 0.1 && e <= 60 ? 1 :
			e >= 180 - 0.09 && e <= 180 ? 4 :
				e >= 90 - 0.1 && e <= 90 ? 2 :
					e >= 120 - 0.1 && e <= 120 ? 3 :
						360
}

function WhatAsp1(e, t) {
	var o;
	e= Math.round(e/10)*10;
	switch (e) {
		case 0: o=0;
			break;
		case 60: o=1;
			break;
		case 90: o=2;
			break;
		case 120: o=3;
			break;
		case 180: o=4;
			break;
	}
	if(e<=t) o=0;
	if(o === undefined) {
		// console.log('No value!');
	} else {
		console.log('WhatAsp1 returns ', o);
	}
	return o;
}

function GrafikPaint() {
	var e = document.getElementById("PanelR").getBoundingClientRect(); XG = e.left + pageXOffset, YG = e.top + pageYOffset, W = document.getElementById("PanelR").clientWidth;
	var t = document.getElementById("PR");
	t.width = W, screen.height < 480 && (H = 360, document.getElementById("Grafik").style.height = H), t.height = H;
	var a = t.getContext("2d");
	a.fillStyle = ColorFon, a.fillRect(0, 0, W, H);
	var n = Date1 = new Date(9465912e5 + 864e5 * dd1), o = 4;
	IntervalPrognoza < 4 && (o = 120),
		IntervalPrognoza > 31 && (o = 1);
	var r = W / IntervalPrognoza / o, l = 0; screen.height < 480 && (l = 2);
	var s, c, d, i, p, B, g, m, u, v, f, y, h, k, b, I, P, T, C, x, A, M, D = 0, E = 0, x_coord = 0, w = 15, G = 0, _ = 10, Y = 0, N = [[], [], [], [], [], [], [], [], [], []], S = ""; a.strokeStyle = "#bbb", a.lineWidth = 1, a.fillStyle = "#000", a.font = "12px sans-serif"; var X = 0, L = !1, O = !1, R = ["¤нварь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сент¤брь", "окт¤брь", "но¤брь", "декабрь"], F = ["¤нв", "фев", "мар", "апр", "май", "июн", "июл", "авг", "сен", "окт", "но¤", "дек"], V = ["01", "02", "03", "04", "05", "06", "07", "08", "09", "10", "11", "12"];
	for (k = 0; k <= IntervalPrognoza; k++)L = 1 == (n = new Date(Date1.valueOf() + 24 * k * 60 * 60 * 1e3)).getDay(), O = 1 == n.getDate(), 6 == n.getDay() || 7 == n.getDay(), (IntervalPrognoza <= 10 || 30 == IntervalPrognoza && W > 700 || 30 == IntervalPrognoza && W <= 700 && L || IntervalPrognoza <= 100 && L || IntervalPrognoza > 100 && O || 0 == k) && (X = Math.round(W / IntervalPrognoza * k), a.beginPath(), a.moveTo(X + .5, 10), a.lineTo(X + .5, H), a.stroke(), M = IntervalPrognoza > 20 && IntervalPrognoza <= 31 && !O && !L ? n.getDate() : IntervalPrognoza > 300 ? W < 600 ? F[n.getMonth()] : W > 1e3 && IntervalPrognoza < 400 ? R[n.getMonth()] + "'" + (n.getYear() - 100) : F[n.getMonth()] + "'" + (n.getYear() - 100) : n.getDate() + "." + V[n.getMonth()], a.fillText(M, X + 3, 10));
	var U = 4; for (IntervalPrognoza <= 7 ? U = 0 : IntervalPrognoza > 7 && IntervalPrognoza < 32 ? U = 1 : IntervalPrognoza > 100 && (U = 5), y = 0; y <= 9; y++)for (k = 0; k <= IntervalPrognoza * o - 1; k++) { try { if (void 0 === eph[Math.floor(dd1 - 1 + k / o)][y]) continue; C = eph[Math.floor(dd1 - 1 + k / o)][y] } catch (e) { 0 == y && 0 == k && alert("»нтервал прогноза выходит за пределы имеющихс¤ расчетных данных"); continue } try { if (void 0 === eph[Math.floor(dd1 + k / o)][y]) break; x = eph[Math.floor(dd1 + k / o)][y] } catch (e) { 0 == y && alert("»нтервал прогноза выходит за пределы имеющихс¤ расчетных данных"); break } (A = x - C) < -330 ? A += 360 : A > 330 && (A = 360 - A), (A = C + A / 24 * (12 + z2) + A * (k / o - Math.floor(k / o))) < 0 ? A += 360 : A > 360 && (A -= 360), N[y][k] = A }

	//---Построение основных аспектов-----
	for (y = U; y <= 9; y++) {
		for (Y = 1, 0 == y && (Y = 2), I = y < 4 ? 10 : 12, h = 0; h < I; h++) {
			for (s = 0, m = 0, f = 0, k = 0; k <= IntervalPrognoza * o - 1; k++)
				if (360 == (d = WhatAsp(c = AspCalc(N[y][k], natal[h]), Y))) 1 == s && (T = Math.round(k / o * 1e3), S += '<div class="asp_graph" style="top:' + (E - 1) + "px;left:" + (D - 1) + "px;width:" + (G - D + 2) + "px;x_coord-index: 3;border: solid 1px " + isNatClr[f] + ';" onclick="AspGraph(event,' + y + "," + g + "," + i + "," + h + "," + pm * m + "," + P + "," + T + ')" onmousemove="AspTrans(event,' + y + "," + i + "," + h + "," + P + "," + T + ')" onmouseout="AspOut()">' + AspImg(y, i, h) + "</div>", s = 0, i = d, m = 0, f = 0);
				else if (_ = w + 18 - l, Bt = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0], g = PlaHouse(N[y][k], Dom, 1), Bt[g] = 5, g = PlaHouse(N[y][k], Dom, 0), Bt[g] = 5, IsTema(Tema, y, h) > 0) {
					if (u = 1 - Math.abs(aspGr[d] - c) / Y, pm = ka[d], 0 == pm && (pm = kp[y]), v = 0, y != h && h < 10)
						for (360 != (B = WhatAsp(AspCalc(N[y][k], N[h][k]), 5)) && (v++, d == B || 0 == B || pm == ka[B] ? v++ : v--), 360 != (B = WhatAsp(AspCalc(N[h][k], natal[y]), 1)) && (v++, d == B || 0 == B || pm == ka[B] ? v++ : v--), b = 0; b < 10; b++)b != y && 360 != (B = WhatAsp(AspCalc(N[b][k], natal[h]), 1)) && (d == B || 0 == B || pm == ka[B] ? v++ : v--); (u += .15 * v) > 1 && (u = 1), u < .1 && (u = .1), 0 == s && (m = kAsp(Tema, y, d, h), y != h && 360 != (f = NatAsp(y, h)) && (f = 0 == f ? 1 : 2 == f || 4 == f ? 2 : 3)), x_coord = Math.round(r * k), G = Math.round(r * (k + 1)), _ = w + 18 - l, p, p = Y - Math.abs(aspGr[d] - c), (m < Vesa[Ves] || Targ && !IsTarget(Tema, y, h)) && (u = .05), 0 == d && (u /= 2), pm < 0 ? a.fillStyle = "rgba(" + Red[10 - m] + "," + u + ")" : a.fillStyle = "rgba(" + Green[10 - m] + "," + u + ")", a.fillRect(x_coord, w, r + 1, 18), 0 == d && (a.fillStyle = "rgba(0,64,255," + u + ")", a.fillRect(x_coord, w, r + 1, 18)), 0 == s && (D = x_coord, E = w, s = 1, i = d, P = Math.round(k / o * 1e3))
				}
			1 == s && (T = Math.round(k / o * 1e3),
				S += '<div class="asp_graph" style="top:' + (E - 1) + "px;left:" + (D - 1) + "px;width:" + (G - D + 1) + "px;x_coord-index: 3;border: solid 1px " + isNatClr[f] + ';" onclick="AspGraph(event,' + y + "," + g + "," + i + "," + h + "," + pm * m + "," + P + "," + T + ')" onmousemove="AspTrans(event,' + y + "," + i + "," + h + "," + P + "," + T + ')" onmouseout="AspOut()">' + AspImg(y, i, h) + "</div>", s = 0, i = 360, m = 0, f = 0),
				IntervalPrognoza > 20 && y < 4 ? w += 6 : IntervalPrognoza > 3 && 0 == y || IntervalPrognoza > 30 && y < 5 || IntervalPrognoza > 400 && y < 6 ? w += 9 : w = _ + 2
		} (IntervalPrognoza > 3 && 0 == y || IntervalPrognoza > 20 && y < 4 || IntervalPrognoza > 30 && y < 5) && (w = _ + 2)
	}

	//---конец основных аспектов-----

	//---Построение аспектов Луны---

	for (y = 1; y <= 9; y++) { //y=10/I=12/Y=1
		for (Y = 1, 0 == y && (Y = 2), I = y < 4 ? y : 1, h = 0; h < I; h++) {
			for (s = 0, m = 0, f = 0, k1 = 0; k1 <= IntervalPrognoza * o - 1; k1++)//k=150/s=m=f=0

				if (360 == (d = WhatAsp(
						c = AspCalc1(N[0][k1], N[y][k1]), Y
					)))
					1 == s && (
						//T1=Math.round(k/o*1e3),
						//console.log(N[0]),
						S += '<div class="asp_graph" style="top:' + (350 - 1) + "px;left:" + (x_coord - 40) + "px;width:" + (50) + "px;x_coord-index: 3;border: solid 1px " + isNatClr[f] + ';" onclick="AspGraph(event,' + y + "," + g + "," + i + "," + h + "," + pm * m + "," + P + "," + T + ')" onmousemove="AspTrans(event,' + y + "," + i + "," + h + "," + P + "," + T + ')" onmouseout="AspOut()">' + AspImg1(y, i, h) + "</div>", s = 0, i = d, m = 0, f = 0);

				else if (_ = w + 18 - l, Bt = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],//g=PlaHouse(N[y][k],Dom,1),Bt[g]=5,//g=PlaHouse(N[y][k],Dom,0),Bt[g]=5,
					IsTema(Tema, y, h) > 0) //
				{
					if (//u=1-Math.abs(aspGr[d]-c)/Y,					//var kp = [1,1,-1,1,1,1,1,-1,-1,-1];
						pm = ka[d], 0 == pm && (pm = kp[y]),	//v=0,
						y != h && h < 10)
						//v=2,u=0.798143425000009,//ka=[0,1,-1,1,-1] //for(//360!=(B=WhatAsp(AspCalc(N[y][k],N[h][k]),5))&&(v++,d==B||0==B||pm==ka[B]?v++:v--),//ka=[0,1,-1,1,-1]								//360!=(B=WhatAsp(AspCalc(N[h][k],natal[y]),1))&&(v++,d==B||0==B||pm==ka[B]?v++:v--),								b=0;	b<10;b++)							//B=360	//d console.log(d);360	//console.log(b);//10		//h=12				//y=10								//b!=y&&360!=(B=WhatAsp(AspCalc(N[0][k],[h]),1))&&(d==B||0==B||pm==ka[B]?v++:v--); //ka=[0,1,-1,1,-1]	//(u+=.15*v)>1&&(u=1), u<.1&&(u=.1),0==s&&(m=kAsp(Tema,y,d,h),y!=h&&360!=(f=NatAsp(y,h))&&(f=0==f?1:2==f||4==f?2:3)),//оттенки кр и зел, после комментирования исчезли рамки вокруг аспекта, border

						/**
						 * r - толщина единичного элемента отрисовки графиков
						 * k1 - порядковый номер единичного элемента
						 * x_coord - координата ед. элемента по Х
						 */
						x_coord = Math.round(r * k1),

						//если pm <0, то негат аспект и красный: иначе зеленый
						pm < 0 ? a.fillStyle = "rgba(255,8,0)" : a.fillStyle = "rgba(0,255,0)", a.fillRect(x_coord, 350, 1, 28),

						//цвет и рект для аспекта соединения
						0 == d && (
							a.fillStyle = "rgba(0,64,255," + u + ")",
							a.fillRect(x_coord, 350, r, 28)
						);

						0 == s && (D = x_coord, E = w, s = 1, i = d)
					//P=Math.round(k/o*1e3))//координаты х начальной точки аспекта
				}
			console.log(r);
			//console.log(d);//360 33 раза     //console.log(k);//34 штуки/ 150 - если интервал прогноза 3 дня, 50 - 1 день, 100 - 2 дня, 28 -7, 40 - 10, 120 - 30 дней
			//console.log(w);//высота,
			1 == s && (
				//T=Math.round(k/o*1e3),//координаты х конечной точки аспекта
				S += '<div class="asp_graph" style="top:' + (350) + "px;left:" + (x_coord - 40) + "px;width:" + (50) + "px;x_coord-index: 3;border: solid 1px " + isNatClr[f] + ';" onclick="AspGraph(event,' + y + "," + g + "," + i + "," + h + "," + pm * m + "," + P + "," + T + ')" onmousemove="AspTrans(event,' + y + "," + i + "," + h + "," + P + "," + T + ')" onmouseout="AspOut()">' + AspImg(y, i, h) + "</div>", s = 0, i = 360, m = 0, f = 0),
				//S = описание div, то что в elements. это подписи аспектов
				IntervalPrognoza > 20 && y < 4 ?
					w += 60 : IntervalPrognoza > 3 && 0 == y || IntervalPrognoza > 30 && y < 5 || IntervalPrognoza > 400 && y < 6 ?
						w += 90 : w = _ + 20
		}
		//*****
	}

	for (document.getElementById("Grafik").innerHTML = S, _ += 11, k = 0; k <= IntervalPrognoza; k++)L = 1 == (n = new Date(Date1.valueOf() + 24 * k * 60 * 60 * 1e3)).getDay(), O = 1 == n.getDate(), 6 == n.getDay() || 7 == n.getDay(), (IntervalPrognoza <= 10 || 30 == IntervalPrognoza && W > 700 || 30 == IntervalPrognoza && W <= 700 && L || IntervalPrognoza <= 100 && L || IntervalPrognoza > 100 && O || 0 == k) && (X = Math.round(W / IntervalPrognoza * k), M = IntervalPrognoza > 20 && IntervalPrognoza <= 31 && !O && !L ? n.getDate() : IntervalPrognoza > 300 ? W < 600 ? F[n.getMonth()] : W > 1e3 && IntervalPrognoza < 400 ? R[n.getMonth()] + "'" + (n.getYear() - 100) : F[n.getMonth()] + "'" + (n.getYear() - 100) : n.getDate() + "." + V[n.getMonth()], a.fillStyle = "#000", _ < 405 && (_ = 405), a.fillText(M, X + 3, _)); a.fillStyle = ColorFon, a.fillRect(0, _ + 2, W, H - _ - 2),
		document.getElementById("PanelR").style.height = _ + 22 + "px"
} DomN[10] = 1, DomN[11] = 10, GrafikPaint();


// *=====================
// *kff


import(location.href + 'mod_findInArray.js')
.then(fia => {
	// console.log('fia= ', fia);

	let f= fia.findInArr();

	$(document.body).append(
		`<h4>Исходные данные:</h4>
		<div style="overflow:auto;">
		Массив 1 - ${fia.a1}<br>
		Массив 2 - ${f.a2}<hr>
		</div>
		<h4>Решение</h4>
		<h5>Ближайшие значения:</h5>
		${f.res}
		<h5>Наиболее приближённое значение:</h5>
		${f.tmp.resMin}`
	);
})
.catch(err => {
	console.warn('fia.err.message= ', err.message);
});


import(location.href + 'mod_my_chart.js')
.then(ch => {
	console.log('ch= ', ch);

	// *Отрисовываем канвас, задаём настройки
	ch.createCanvas(null, {
		wrapper: {
			style: 'text-align: center;',
		},
		canvas: {
			width: '500',
			height: '300',
		}

	});
})
.catch(err => {
	console.warn('ch.err.message= ', err.message);
});