function units() {
	var old_un = document.getElementById("lb_un").innerHTML,
		obj_un = document.getElementById("unitsid"),
		new_un = obj_un.options[obj_un.selectedIndex].text,

		b = +document.getElementById("bid").value.replace(',', "."),
		h = +document.getElementById("hid").value.replace(',', "."),
		sb1 = +document.getElementById("sb1id").value.replace(',', "."),
		sh1 = +document.getElementById("sh1id").value.replace(',', "."),
		b_new = convert(b, old_un, new_un);

	document.getElementById("bid").value = b_new;
	var h_new = convert(h, old_un, new_un);
	document.getElementById("hid").value = h_new;
	var sb1_new = convert(sb1, old_un, new_un);
	document.getElementById("sb1id").value = sb1_new;
	var sh1_new = convert(sh1, old_un, new_un);
	document.getElementById("sh1id").value = sh1_new;

	document.getElementById("lb_un").innerHTML = new_un;
	obj_un.old = new_un;
}



function calc() {
	var un = document.getElementById("lb_un").innerHTML;
	var b = document.getElementById("bid").value; b = b.replace(',', "."); b = parseFloat(b);
	var h = document.getElementById("hid").value; h = h.replace(',', "."); h = parseFloat(h);
	var sb1 = document.getElementById("sb1id").value; sb1 = sb1.replace(',', "."); sb1 = parseFloat(sb1);
	var sh1 = document.getElementById("sh1id").value; sh1 = sh1.replace(',', "."); sh1 = parseFloat(sh1);
	var obj_table = document.getElementById("tablerezid");
	
	var h1 = okrugl(h - sh1 * 2),
	b1 = okrugl((b - sb1) / 2),
	F = okrugl(2 * b * sh1 + sb1 * h1),
	Jx = okrugl((b * Math.pow(h, 3) - 2 * b1 * Math.pow(h1, 3)) / 12),
	Jy = okrugl((h1 * Math.pow(sb1, 3) + 2 * sh1 * Math.pow(b, 3)) / 12),
	Wx = okrugl((b * Math.pow(h, 3) - 2 * b1 * Math.pow(h1, 3)) / (6 * h)),
	Wy = okrugl((h1 * Math.pow(sb1, 3) + 2 * sh1 * Math.pow(b, 3)) / (6 * b)),
	ix = okrugl(Math.sqrt(Jx / F)),
	iy = okrugl(Math.sqrt(Jy / F));



	document.getElementById("rez_f").innerHTML = "F = 2 * B * h<sub>1</sub> + b<sub>1</sub> * h = 2 * " + b + " * " + sh1 + " + " + sb1 + " * " + h1 + " = <b>" + F + "</b> " + un + "<sup>2</sup>";

	document.getElementById("rez_jx").innerHTML = "Jx = (B * H<sup>3</sup> - 2 * b * h<sup>3</sup>) / 12 = (" + b + " * " + h + "<sup>3</sup> - 2 * " + b1 + " * " + h1 + "<sup>3</sup>) / 12 = <b>" + Jx + "</b> " + un + "<sup>4</sup>.";

	document.getElementById("rez_jy").innerHTML = "Jy = (h * b<sub>1</sub><sup>3</sup> + 2 * h<sub>1</sub> * B<sup>3</sup>) / 12 = (" + h1 + " * " + sb1 + "<sup>3</sup> + 2 * " + sh1 + " * " + b + "<sup>3</sup>) / 12 = <b>" + Jy + "</b> " + un + "<sup>4</sup>.";

	document.getElementById("rez_wx").innerHTML = "Wx = (B * H<sup>3</sup> - 2 * b * h<sup>3</sup>) / (6 * H) = (" + b + " * " + h + "<sup>3</sup> - 2 * " + b1 + " * " + h1 + "<sup>3</sup>) / (6 * " + h + ") = <b>" + Wx + "</b> " + un + "<sup>3</sup>.";

	document.getElementById("rez_wy").innerHTML = "Wy = (h * b<sub>1</sub><sup>3</sup> + 2 * h<sub>1</sub> * B<sup>3</sup>) / 6 * B) = (" + h1 + " * " + sb1 + "<sup>3</sup> + 2 * " + sh1 + " * " + b + "<sup>3</sup>) / (6 * " + b + ") = <b>" + Wy + "</b> " + un + "<sup>3</sup>.";

	document.getElementById("rez_ix").innerHTML = "i<sub>x</sub> = (Jx / F)<sup>0.5</sup> = (" + Jx + " / " + F + ")<sup>0.5</sup> = <b>" + ix + "</b> " + un + ".";

	document.getElementById("rez_iy").innerHTML = "i<sub>y</sub> = (Jy / F)<sup>0.5</sup> = (" + Jy + " / " + F + ")<sup>0.5</sup> = <b>" + iy + "</b> " + un + ".";

	if (obj_table.selectedIndex == 1) {
		newrow = _K.G('#table_flange_1').insertRow(); var cс = 0;
		newcell = newrow.insertCell(cс++); newcell.innerHTML = h;
		newcell = newrow.insertCell(cс++); newcell.innerHTML = b;
		newcell = newrow.insertCell(cс++); newcell.innerHTML = sb1;
		newcell = newrow.insertCell(cс++); newcell.innerHTML = sh1;
		newcell = newrow.insertCell(cс++); newcell.innerHTML = F + " " + un + "<sup>2</sup>";
		newcell = newrow.insertCell(cс++); newcell.innerHTML = Jx + " " + un + "<sup>4</sup>";
		newcell = newrow.insertCell(cс++); newcell.innerHTML = Jy + " " + un + "<sup>4</sup>";
		newcell = newrow.insertCell(cс++); newcell.innerHTML = Wx + " " + un + "<sup>3</sup>";
		newcell = newrow.insertCell(cс++); newcell.innerHTML = Wy + " " + un + "<sup>3</sup>";
		newcell = newrow.insertCell(cс++); newcell.innerHTML = ix + " " + un;
		newcell = newrow.insertCell(cс++); newcell.innerHTML = iy + " " + un;
		obj_table.selectedIndex = 0;
	}

}