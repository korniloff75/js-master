﻿/*добавление текста в список*/
function addOption (oListbox, text, value, isDefaultSelected, isSelected) {
  var oOption = document.createElement("option");
  oOption.textContent = text;
  oOption.setAttribute("value", value);

  if (isDefaultSelected) oOption.defaultSelected = true;
  else if (isSelected) oOption.selected = true;

  oListbox.appendChild(oOption);
};

/*Очистка списка*/
function clear_sel(oListbox) {
   while (oListbox.childNodes.length) {
   if (oListbox.firstChild.tagName == 'OPTGROUP') {
     while (oListbox.firstChild.childNodes.length) {
     oListbox.firstChild.removeChild(oListbox.firstChild.firstChild);
     }
   }
 oListbox.removeChild(oListbox.firstChild);
 } 
}	

/* javascript-код голосования из примера*/
function vote() {
	// (1) создать объект для запроса к серверу
	var req = getXmlHttp()  
    // (2)
	// span рядом с кнопкой
	// в нем будем отображать ход выполнения
	var statusElem = document.getElementById('vote_status') 
	
	req.onreadystatechange = function() {  
        // onreadystatechange активируется при получении ответа сервера

		if (req.readyState == 4) { 
            // если запрос закончил выполняться

			statusElem.innerHTML = req.statusText // показать статус (Not Found, ОК..)

			if(req.status == 200) { 
            // если статус 200 (ОК) - выдать ответ пользователю
			var obj_temp = document.getElementById("tempid");	

			obj_temp.value = req.responseText;	
			}
			// тут можно добавить else с обработкой ошибок запроса
		}

	}

       // (3) задать адрес подключения
	req.open('GET', '/PROB_PAGES/prob.php', true);  

	// объект запроса подготовлен: указан адрес и создана функция onreadystatechange
	// для обработки ответа сервера
	 
        // (4)
	req.send(null);  // отослать запрос
  
        // (5)
	statusElem.innerHTML = 'Ожидаю ответа сервера...' 
}

/*Создание объекта XMLHttpRequest*/
function CreateRequest() {
   var Request = false;

   if (window.XMLHttpRequest) {
   /*Gecko-совместимые браузеры, Safari, Konqueror*/
     Request = new XMLHttpRequest();
     }
   else if (window.ActiveXObject) {
   /*Internet explorer*/
   try
    {
    Request = new ActiveXObject("Microsoft.XMLHTTP");
    } 
   catch (CatchException)
     {
     Request = new ActiveXObject("Msxml2.XMLHTTP");
     }
   }
 if (!Request) {alert("Невозможно создать XMLHttpRequest");}
 return Request;
}

function text_del(t, del) {
var i = 0;
var t_str = t.split('');
var x = 1;
var arr = new Array();

alert("работаем в функции text_del");
alert(t_str);
for (var k = 0; k < t_str.length; k++)
    {
	/*alert(t_str[k]);*/
    if (t_str[k] != del) {
	   if (x == 0) {a[i] = arr[i]+t_str[k]; alert(arr[i]);} else
	   {arr[i] = t_str[k]; 
	    x = 0;
		alert(arr[i]);}

	} else {x = 1; i = i+1;}
	}
	alert(arr);
	return arr;
}

// преревод ед длины
function convert(old_value, old_un, new_un)
{   
	var m_un = new Array(
				["м",1],
				["дм",10],
				["см",100],
				["мм",1000]);
	var k_old = 1;
	var k_new = 1;
	for (var k = 0; k < 4; k++){ 
		if (m_un[k][0] == old_un){k_old = m_un[k][1];} 
		if (m_un[k][0] == new_un){k_new = m_un[k][1];} 
	}  
	var new_value = 0;
	new_value = old_value*k_new/k_old; 
	return new_value;
}

// округление 
function okrugl(value)
{
	var rez = +value;
	if (value > 100) {rez = value.toFixed(1);} else
	if (value > 10) {rez = value.toFixed(2);} else
	if (value > 1) {rez = value.toFixed(3);} else
	if (value == 1) {rez = value.toFixed(3);} else
	if ((value < 1)&&(value > 0.1)) {rez = value.toFixed(3);} else
	if ((value < 0.1)&&(value > 0.01)) {rez = value.toFixed(4);} else
	if ((value < 0.01)&&(value > 0.001)) {rez = value.toFixed(5);} else
	if ((value < 0.001)&&(value > 0.0001)) {rez = value.toFixed(6);} else
	if ((value < 0.0001)&&(value > 0.00001)) {rez = value.toFixed(7);} else
	if ((value < 0.00001)&&(value > 0.000001)) {rez = value.toFixed(8);} else
	if ((value < 0.000001)&&(value > 0.0000001)) {rez = value.toFixed(9);} else
	if ((value < 1e-7)&&(value > 1e-8)) {rez = value*1e8; rez = rez.toFixed(3); rez = rez+"e-8"} else
	if ((value < 1e-8)&&(value > 1e-9)) {rez = value*1e9; rez = rez.toFixed(3); rez = rez+"e-9"} else
	if ((value < 1e-9)&&(value > 1e-10)) {rez = value*1e10; rez = rez.toFixed(3); rez = rez+"e-10"} else
	if ((value < 1e-10)&&(value > 1e-11)) {rez = value*1e11; rez = rez.toFixed(3); rez = rez+"e-11"} else
	if ((value < 1e-11)&&(value > 1e-12)) {rez = value*1e12; rez = rez.toFixed(3); rez = rez+"e-12"} else
	if ((value < 1e-12)&&(value > 1e-13)) {rez = value*1e13; rez = rez.toFixed(3); rez = rez+"e-13"} else
	if ((value < 1e-13)&&(value > 1e-14)) {rez = value*1e14; rez = rez.toFixed(3); rez = rez+"e-14"} else
	if ((value < 1e-14)&&(value > 1e-15)) {rez = value*1e15; rez = rez.toFixed(3); rez = rez+"e-15"} else
	if ((value < 1e-15)&&(value > 1e-16)) {rez = value*1e16; rez = rez.toFixed(3); rez = rez+"e-16"} else
	if ((value < 1e-16)&&(value > 1e-17)) {rez = value*1e17; rez = rez.toFixed(3); rez = rez+"e-17"} else
	if ((value < 1e-17)&&(value > 1e-18)) {rez = value*1e18; rez = rez.toFixed(3); rez = rez+"e-18"} else
	if ((value < 1e-18)&&(value > 1e-19)) {rez = value*1e19; rez = rez.toFixed(3); rez = rez+"e-19"}
	return(rez);
	
	
}