<?php
header('Content-Type: text/html;charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');
?>

<style type="text/css">
	table#Calc {
		width: 220px;
		border-collapse: collapse;
		padding: 5px;
		margin: auto;
		text-align: center;
		border: 2px outset;
		color: #159;
		background: #eee;
	}

	table#Calc input,
	table#Calc select {
		color: #147 !important;
		background: #f5f5f5;
		margin: 10px 0;
	}

	table#Calc label {
		margin: 0 10px;
	}

	table#Calc #Input {
		width: 100%;
		background: #eee;
		padding: 1px 3px;
		font-size: 120%;
	}

	table#Calc #MEMD {
		width: 90px;
		padding: 1px 3px;
		font-size: 100%;
	}

	table#Calc a {
		color: #159;
	}
</style>


<table id="Calc">
	<tr>
		<td align="right">
			<div style="margin-bottom=5px;">
				<input id="Input" class="btn" type="text" value="" Size="24" onkeypress="return Calc.pkey(event);" ondblClick="this.select();"
				 placeholder="Введите данные"></div>
			<!-- // Округление результата -->
			<div><select style="width: 30px; font: 12px bold sans-serif;" id="desN" class="btn" title="точность результата">
					<option value="1">1</option>
					<option selected="selected" value="2">2</option>
					<option value="3">3</option>
					<option value="4">4</option>
					<option value="5">5</option>
				</select>
				<!-- // Память -->
				<select size="1" style="width:50px; font: 12px bold sans-serif;" id="MEMN" class="btn" title="номер стека памяти"
				 onchange="Calc.M.val();">
					<option value="0" selected="selected">М0</option>
					<option value="1">М1</option>
					<option value="2">М2</option>
					<option value="3">М3</option>
					<option value="4">М4</option>
					<option value="5">М5</option>
				</select>
				=
				<input type="text" id="MEMD" class="btn" name="MEMD" value="0" readonly="readonly" ondblClick="Calc.M.r()" /></div>
		</td>
	</tr>
	<tr>
		<!-- // Ввод степени -->
		<td> ^ <input type="text" id="std" class="btn" name="std" value="1" title="Возвести в степень" style="width: 30px;"
			 maxlength="4" onclick="this.select();" onkeypress="Calc.pkey(event);">
			<!-- // Вызов панелей -->
			<select id="PanFnN" class="btn" style="width: 130px; font-size: 12px;" size="1" onchange="Calc.selFns.call(this); "
			 title="Дополнительные функции">
				<option value="0" selected="selected">Выбор панели</option>
				<option value="1">Тригонометрия</option>
				<option value="2">Арматура</option>
			</select>
			<!-- // Кнопки памяти -->
		</td>
	</tr>
	<tr>
		<td><input TYPE="button" id="mp" class="btn" name="mp" value="M+" title="Добавить в память" style=" margin:2px;"
			 onclick="Calc.M.pl()">
			<input type="button" id="mm" class="btn" value="M-" title="Вычесть из памяти" style=" margin:2px;" onclick="Calc.M.min()">
			<input type="button" id="mr" class="btn" value="MR" title="В основное окно" style=" margin:2px;" onclick="Calc.M.r()">
			<input type="button" id="mc" class="btn" value="MC" title="Очистить память" style=" margin:2px;" onclick="Calc.M.c();">
		</td>
	</tr>
	<tr>
		<td class="bold" title="Клавишные команды">
			= - Enter<br />очистить - Spase<br />

			<!-- // Тригонометрия -->
			<tbody id="TrigFunks" hidden>
				<tr>
					<td></td>
				</tr>
			</tbody>

			<!-- // Расчет арматуры -->
			<tbody id="Ras4Arm" hidden>
				<tr>
					<td style="text-align: left;">
						<input type="radio" name="ArmN" id="Kr" value="Kr" checked onchange="Calc.Arm()"><label for="Kr">Круглая</label><br>
						<input type="radio" name="ArmN" id="Kv" value="Kv" onchange="Calc.Arm()"><label for="Kv">Квадратная</label>
					</td>
				</tr>
				<tr>
					<td>Диаметр/грань -
						<input type="text" style="width:50px;" value="10" name="dN" id="dN" class="btn" /> мм
					</td>
				</tr>
				<tr>
					<td>Длина -
						<input type="text" style="width:50px;" value="1.0" name="LaN" id="LaN" class="btn" /> м
					</td>
				</tr>
				<tr>
					<td>Количество -
						<input type="text" style="width:50px;" value="1" name="KvoN" id="KvoN" class="btn" /> шт
					</td>
				</tr>
				<tr>
					<td>Площ. S= <input type="text" style="width:60px;" onclick="this.select();" ondblClick="Calc.v.$mainField.val(this.value); Calc.v.$mainField[0].focus();" readonly="readonly" name="Se4N" id="Se4N" class="btn" title="Двойной клик - вставить в поле калькулятора" /> см<sup>2</sup>
					</td>
				</tr>
				<tr>
					<td>Масса m= <input type="text" style="width:70px;" name="MasN" id="MasN" class="btn" onclick="this.select();"
						 ondblClick="Calc.v.$mainField.val(this.value); Calc.v.$mainField[0].focus();" readonly="readonly" title="Двойной клик - вставить в поле калькулятора" />
						кг
					</td>
				</tr>
			</tbody>
		</td>
	</tr>
	<tr>
		<td style="font:12px serif;">
			<hr />
			<!-- <a href="//js-master.ru/?Калькуляторы___Математика%2B" title="Справка"target="_blank">Справка</a> &nbsp; &nbsp; -->
			<a href="//js-master.ru" title="KorniloFF-ScriptS ©" target="_blank">KorniloFF &reg;</a>
		</td>

	</tr>
</table>



<?php
// if (isset($_POST['calc'])) echo "Работает!!!";
?>