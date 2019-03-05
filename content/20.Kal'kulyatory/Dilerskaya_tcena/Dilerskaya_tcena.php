<?php
include \H::$Dir  . 'assets/handler.php';
?>

<!--
<script src="/js/modules/KFF_Lite.min.js"></script> -->

<style type="text/css" media="screen">
	#calc_t {border-collapse:collapse; border-spacing: 2px;}
	#calc_t th{background:#1D5B94; color:#ddd}
	#calc_t td{padding:7px; vertical-align:top; border:1px solid #aaa;}

</style>

<!--  -->
<table id="calc_t" width="590">
 <tbody><tr align="center">
  <th colspan="3"> Выбрать раздел :
    <select id="bname" onchange="changeBase(this.value);" size="1">
    	<option value="kamaz_serial">Серийные модели</option>
    	<option value="kamaz_special">Специальные модели</option>
    	<option value="kamaz_kmu">Комплектующие</option>
		</select>

 </th></tr><tr>
  <th rowspan="2">Выберите модель:<br>
  	<input id="search" value="" style="width:150px;" placeholder="Найти..." oninput="search(this.value);" type="text">
  </th><th colspan="2">Цена завода
 </th></tr><tr>
  <th>без НДС:
  </th><th width="150">с НДС:
 </th></tr><tr align="center">
  <td>
  <select id="model" onchange="price();" size="1" style="max-width:150px;">
  </select>
  </td><td id="priceZ">
  </td><td id="priceZ+NDS">
 </td></tr><tr>
  <td id="mod_descr" colspan="3"><!-- Описание -->
 </td></tr><tr>
  <td colspan="3"><b>Дилерская скидка :</b>
   <select id="rub_pr" onchange="price();" size="1">
   	<option value="rub">Рубли</option>
   	<option value="pr" selected="selected">Проценты</option>
   </select>
   <input id="dil_sk" value="10" onkeyup="price();" type="text">
 </td></tr><tr>
  <td colspan="3"><b>Дополнительное оборудование :</b>
  <input id="dop_oborud" value="0" onkeyup="price();" type="text"> руб.
 </td></tr><tr>
  <td colspan="3"><b>Стоимость хранения :</b>
  <input id="hran" value="0" onkeyup="price();" type="text"> руб.
 </td></tr><tr>
  <td colspan="3"><b>Стоимость доставки :</b>
  <input id="dostavka" value="0" onkeyup="price();" type="text"> руб.
 </td></tr><tr>
  <td colspan="2"><b>Дополнительные Наценки (Скидки*)</b>,
  <select id="rub_pr1" onchange="price();" size="1">
   <option value="rub">Рубли</option>
   <option value="pr" selected="selected">Проценты</option>
  </select>
  <p style="font-size:12px; margin:5px;"><b>*</b> Скидки задавать отрицательными, например, <span style="padding:3px 5px;"><b>-10000</b></span></p>
  </td><td id="nacenka" style="text-align:center;"><input class="button" value=" + " title="Еще" onclick="nac_plus();" type="button"><br></td>
 </tr><tr>
  <th rowspan="2">Цена от дилера
  </th><th>без НДС:
  </th><th width="120">с НДС:
 </th></tr><tr align="center">
  <td id="price">
  </td><td id="price+NDS">
 </td></tr><tr>
  <td colspan="3">
</td></tr></tbody></table>

<div id="prim"></div>


<script src="/<?=\H::$Dir ?>assets/KamAZ_IC.js" type="text/javascript" charset="utf-8"></script>

<h3>Описание</h3>

<div>Это клиент-серверное приложение, не смотря на его скромный вид, способно парсить <i>*.xls</i> файлы с базами данных, получать из них нужные данные на сервере и отдавать клиентской части.</div>

<div>Если обратить внимание на пункт <i>Выбрать раздел</i>, то будет видно, что баз данных может быть несколько (в конкретном примере - 3). При переключении между ними происходит асинхронное соединение с сервером и данные калькулятора полностью обновляются.<br></div>
