<h2>Отправить SMS</h2>

<p>Если вы отправляли послание с помощью формы выше, но не получили ответа в течение 3 суток, вы можете отправить SMS с напоминанием. Вы должны понимать, что количество SMS, передаваемых в день, ограничено, и не злоупотреблять этой возможностью без необходимости.</p>

<p>Поскольку количество символов в SMS-сообщении не может превышать 70, вводите только самые необходимые для связи с вами данные: email, имя, тему обращения или короткий комментарий. <strong>Не нужно</strong> указывать свой номер телефона.</p>

<div style="width:100%;">
	<h6>Пример SMS</h6>
	<blockquote>Иван, sidor4uk@mail.ru, жду ответа.
	Заказ кредитного калькулятора</blockquote>
</div>


<div style="display:block;">

<?php
	# SMS.RU.PHP
	require_once ('php/modules/sms.ru.php');

	$smsru = new SMSRU();

	# Узнаём к-во оставшихся SMS
	$smsru->ApiKey = "0741FE23-600E-C066-59A1-90789937904F";
	$requestSMS = $smsru->getInfo('free');
	$lastLimit= ($requestSMS->status == "OK")? ($requestSMS->total_free - $requestSMS->used_today): 0;


	if(\ADMIN || $lastLimit > 0) {
?>

	<p>На сегодня осталась возможность отправки <b> <?=$lastLimit?></b> сообщений администрации.</p>

	<form id="sendSMS" class="flex-column  center" action="/<?=\H::$Dir ?>" method="post">
		<input type="hidden" name="keyCaptcha" value="<?=\H::realIP()?>">
		<p hidden>Вы можете ввести <span class=strong id="maxLen"></span> символов</p>

		<textarea name="SMS" rows="7" onkeyup="countChars.call(this, $('#maxLen'), event)" required="required" placeholder="Введите текст SMS"></textarea>

		<div class="flex-justify-around">
			<input type="submit" class=button value="Отправить SMS" style="flex:5 0;">
			<input type="reset" class=button style="flex:1 1;">
		</div>

	</form>


	<script type="text/javascript">
	'use strict';
	$('#sendSMS').on('submit', function(e) {
		e.preventDefault();
		var $this = $(this),
			data = Object.assign($this.ajaxForm(), {
				ajax: 1
			}) ;

		// console.log();
		$('#ajax-content').load("", data)
		/* .done(function(resp) {
			console.log(resp);
		}); */
		return false;
	})

	function countChars(out,e) {
		out = out[0];
		var maxLen= 69,
			count= maxLen - this.value.length;

		out.parentNode.hidden=0;
		if (count<1) {
		//	this.disabled=1;
			count=0;
			this.blur();
			this.value= this.value.substr(0,maxLen);
		}
		out.textContent= count;
	}
	</script>



<?php
	extract($_REQUEST);

	if(!empty($SMS)) {
	# save SMS
	/* var_dump($_REQUEST);
	die; */

	$IP = \H::realIP();
	$dbFromIP = \H::json('db/sms.json', $IP);

	# gheck time
	if($dbFromIP && (time() - \CF['date']['delta'] - end($dbFromIP)[0]) < 2*60) {
		$errSMS[] = "Вы сможете отправить следующее сообщение не ранее, чем через 2 минуты.";
	}

	# data from send
	$data = new stdClass();
	# Текст сообщения
	$data->text = stripslashes(strip_tags($SMS));
	# Тестовый режим
	// $data->test = 1;

	$dbFromIP[] = [time() - \CF['date']['delta'], $SMS];

	# ADD SMS to base
	\H::json('db/sms.json', [$IP => $dbFromIP]);


	function errSMS ($code, $err) {
		return "<p>Код ошибки: <b>$code</b>.</p><p>Текст ошибки: <b>$err</b>.</p>";
	}

	# Невидимая каптча
	if (empty($keyCaptcha) || $keyCaptcha != $_SESSION['captcha']) {
		$errSMS[] = "Невидимая каптча не сработала";
		// if($adm) var_dump($captcha);
	}

	if(!count(@$errSMS)) {
		# Отправка сообщения и возврат данных в переменную
		$sms = $smsru->send_one($data);

		if ($sms->status == "OK") {
			echo "<div class=\"core message\">
			<p>Сообщение отправлено успешно.</p><p>ID сообщения: <b>$sms->sms_id</b>.</p><p>Спасибо за обращение!</p>
			</div>";
		} else {
			echo "<p class=red>Сообщение не отправлено.</p>"
			. errSMS($sms->status_code, $sms->status_text);
		}

	} else {
		echo "<p class=red>Сообщение не отправлено.</p>";
		foreach($errSMS as $err) {
			echo errSMS(000, $err);
		}

	}

	die;

} // $SMS

} // $lastLimit > 0
	else echo '<p class="core warning">К сожалению, на сегодня лимит отправляемых сообщений уже исчерпан. О</p>';
?>
</div>
