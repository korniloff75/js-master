<?php
\H::protectScript(basename(__FILE__));

/* if(!headers_sent() && !isset($_SESSION)) session_start();
$_SESSION['captcha'] = \H::realIP(); */
?>


<link rel="stylesheet" type="text/css" href="/<?=\H::$Dir ?>assets/fb_form.css" />

<div id="form-outbox">

	<div class="form-container">

		<form id="feedback-form" name="feedback-form" method="post" action="/?module=php/modules/PHPMailer/handler.php">
			<input type="hidden" name="captcha" id="captcha"  value="<?=\H::realIP()?>" />
			<input type="hidden" name="MAX_FILE_SIZE" value="500000" />
			<div>
				<label for="name">Имя</label>
				<input type="text" name="name" id="name"  required />
			</div>

			<div>
				<label for="email">Email</label>
				<input type="text" name="email" id="email" required />
			</div>

			<div>
				<label for="tg">Telegram</label>
				<input type="text" name="tg" id="tg" placeholder="@NickName или https://t.me/yourLogin" />
			</div>

			<div>
				<label for="subject">Тема</label>
				<select name="subject" id="subject" required>
					<option value="" selected="selected"> - Выбрать -</option>
					<option value="Заказ">Заказ</option>
					<option value="Вопрос">Вопрос</option>
					<option value="Предложение">Предложение</option>
					<option value="Реклама">Реклама</option>
				</select>
			</div>

			<div>
				<label for="message">Сообщение</label>
				<textarea name="message" id="message" cols="35" rows="5" required></textarea>
			</div>

			<div>
				<label for="file">Вложение</label>
				<input type="file" name="file" />
			</div>

			<div>
				<input type="submit" name="submit" value="Отправить" />
				<input type="reset" name="reset" value="Очистить" />
			</div>

			<img id="loading" src="assets/img/ajax-load.gif" width="16" height="16" alt="loading" />

		</form>

	</div>

	<div id="response">

	</div>

</div>

<p>Благодаря внедрению <a href="https://core.telegram.org/api" rel="nofollow" target="_blank">Telegram API</a> с декабря 2019г. в исходный код сайта, ваши письма стали доходить ко мне значительно быстрее. Если, всё же, на какое-либо обращение вы не получили ответ - воспользуйтесь разделом ниже и отправьте мне SMS прямо из сайта.</p>
<p>Если у вас установлен Telegram, можете <a href="https://t.me/js_master_bot">написать мне</a> через него.</p>



<script>
(function() {
	var form = document.forms['feedback-form'];

	form.onsubmit = function(e) {
		e = $().e.fix(e);
		e.preventDefault();
		e.stopPropagation();

		var err = _H.form.errors(this),
			$form = $(this),
			$resp_node = $f('#response'),
			formData = new FormData(this);

		$resp_node.removeClass();
		$resp_node.html('');

		/* err.forEach(function(e) {
			if(e.length) {
				$resp_node.append(e.join('<br>') + '<br>');
			}
		}); */

		$resp_node.append(err);

		if($resp_node.text()) {
			console.log(err);
			$resp_node.removeClass();
			$resp_node.addClass('error');
			$resp_node.append('<br>Введите корректные значения и повторите отправку.');
			return;
		}


		$.ajax({
		  url: '/?module=php/modules/PHPMailer/handler.php',
		  data: formData,
		  processData: false,
		  contentType: false,
		  type: 'POST',
		}).done(function(response) {
			$resp_node.append(response);
			$form[0].elements.submit.disabled = 1;
		}).fail(function(response) {
			$resp_node.append('<div class="error">Сообщение не было отправлено. Попробуйте ещё раз.</div>');
		});
	}
	// console.log(formData);
})()
</script>


