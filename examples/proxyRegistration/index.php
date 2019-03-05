<?php
define('OWNER', strpos($_SERVER['REMOTE_ADDR'], '194.28.91.') === 0);
/*
Запрос ответа со стороннего хоста
@url - запрашиваемая страница
RegExp @patt - регулярное выражение для парсинга ответа
returns соответствие $patt, либо response

*/

function getFromServer ( $postFields = "a=4&b=7", string $url = "https://api.olymptrade.com/v2/user/registration-by-password", $patt=null)
{
	global $headers;
	# Emulation AJAX

	$headers = [
		"Content-Type: application/json; charset=utf-8",
		"x-request-project: bo",
		"x-request-type: Api-Request",
		"Origin: https://static.olymptrade.com",
		"Referer: https://static.olymptrade.com/lands/affiliate-new-form/index.html?af_siteid=affiliate-new-form&affiliate_id=191954&dark=false&horizontal=false&lang=ru&lref=&lrefch=affiliate&pixel=0&square=false&subid1=&subid2=&jwt_traffic=1",
		"Cookie: ".session_name()."=".session_id()
	];

	// $postFields = (is_array($postFields)) ? http_build_query($postFields) : $postFields;
	# Отправляем JSON
	$postFields = (is_array($postFields)) ? json_encode($postFields) : $postFields;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

	# Получаем нужную страницу в переменную $answer
	$answer = curl_exec($ch);
	curl_close($ch);
	if (!empty($patt)) {
		preg_match($patt, $answer, $m);
		return $m;
	} else return $answer;
};


// extract($_REQUEST);

# if AJAX
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && !empty($_REQUEST['email']))
{
	ob_start();
	#

	#
	echo "<h3>Это ajax запрос для проксирования</h3>:\n\n";
	$data = ['data' => $_POST];
	var_dump($data);


	echo "\n\n<hr><h3>Это ответ целевого сервера</h3>:\n\n";
	print_r($answer = json_decode(getFromServer($data),1));

	echo "\n\n<hr><h3>Это ответ проксирующего сервера <b>{$_SERVER['SERVER_ADDR']}</b></h3>:\n\n";
	echo "Мой IP - {$_SERVER['REMOTE_ADDR'] }\n";

	if(OWNER)
	{
		echo "<div style='border:2px solid olive'>
		Headers:\n";
		print_r($headers);
		echo '$_SERVER';
		print_r($_SERVER);
		echo "</div>\n";
	}

	$output = ob_get_clean();

	if (!headers_sent()) {
		header('Access-Control-Allow-Origin: *');
		header("Content-type: application/json; charset=utf-8");
	}

	echo json_encode([
		'url' => urldecode($_GET['targetLink']),
		'notes' => $output,
		'answer' => $answer
	], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
	exit;
}

?>


<script type="text/javascript" src="3.jquery-3.3.1.min.js" data-addons=""></script>
<link rel="stylesheet" href="app.bundle.min.css">

<div style="flex-basis:100%;">
	<!-- /exemples/proxyRegistration/ -->
	<p style="text-align: center;"><a href="?af_siteid=affiliate-new-form&affiliate_id=191954&dark=false&horizontal=false&lang=ru&lref=&lrefch=affiliate&pixel=0&square=false&subid1=&subid2=" target="_self">Localization RU</a> &middot; <a href="?af_siteid=affiliate-new-form&affiliate_id=191954&dark=false&horizontal=false&lref=&lrefch=affiliate&pixel=0" target="_self">Localization EN</a></p>
</div>

<form class="olymp-register__form">
	<div style="text-align: center;">
		<img class="olymp-register__logo" src="https://static.olymptrade.com/lands/common/olymp-logo_black.svg" alt="Olymp Trade" />
	</div>

  <div class="olymp-register__row">
		<!-- name="olymp-register-email" -->
		<input type="text" class="olymp-register__text-input olymp-register__element" autocomplete="nope" id="olymp-register__email" name="email" placeholder="Введите ваш e-mail" value="" required>
		<!-- name="olymp-register-password"  -->
    <input type="password" class="olymp-register__text-input olymp-register__element" autocomplete="nope" id="olymp-register__password" name="password" placeholder="Придумайте пароль" value="" required>
	</div>

  <div class="olymp-register__row">
    <div class="olymp-register__currency">
      <div class="olymp-register__currency-title olymp-register__element" id="olymp-register__currencyTitle">Выберите валюту счета</div>
      <div class="olymp-register__currency-options olymp-register__element">
				<input type="radio" value="usd" id="olymp-register__currency-radio-2" name="currency">
        <label for="olymp-register__currency-radio-2">$</label>
        <input type="radio" value="eur" id="olymp-register__currency-radio-3" name="currency">
        <label for="olymp-register__currency-radio-3">€</label>
      </div>
    </div>
    <div class="olymp-register__agreement olymp-register__element">
      <div class="olymp-register__agreement-checkbox">
        <input type="checkbox" name="olymp-register-agreement" id="olymp-register__agreement">
        <label for="olymp-register__agreement"></label>
      </div>
      <label for="olymp-register__agreement" id="olymp-register__agreementTitle" class="olymp-register__agreement-title">Я&nbsp;совершеннолетний, ознакомился и&nbsp;принимаю <a href="https://olymptrade.com/terms" target="_blank" rel="nofollow"> соглашение об&nbsp;оказании услуг</a></label>
    </div>
	</div>

  <button href="#" class="btn olymp-register__submit-button" type="submit">
    <span id="olymp-register__button">Зарегистрироваться</span>
    <span class="chevron"></span>
	</button>

  <div class="olymp-register__success">
    <div class="olymp-register__success-title">Вы успешно зарегистрировались на Olymp Trade.</div>
    <a href="https://olymptrade.com/platform/tutorial" target="_blank" class="btn olymp-register__success-button">
      <span id="olymp-register__success-button">Перейти</span>
      <span class="chevron"></span>
    </a>
	</div>

  <!-- <div class="popup popup_hide">
    <div class="popup__title">Поздравляем!</div>
		<p class="popup__success">Вы успешно зарегистрировались на торговой платформе <b>Olymp Trade</b>.</p>
		<p><a href="https://olymptrade.com" target="_blank" rel="nofollow">Autorize in <b>Olymp Trade</b></a></p>
    <p hidden class="popup__redirect">В течение 3х секунд Вы будете переадресованы на платформу, где сможете заключать сделки.</p>
  </div> -->
</form>



<script src="wording.js"></script>

<script>
  var $GET = Object();
  location.search.substr(1).split("&").forEach(function (string) {
    var tmp = string.split("=");
    $GET[tmp[0]] = tmp[1];
  });

  if ($GET.lang == 'pt') {
    $GET.lang = 'br'
  }

  if ($GET.lang == 'in') {
    $GET.lang = 'id'
  }

  if ($GET.lang == 'sp') {
    $GET.lang = 'es';
  }
  if ($GET.lang == 'ar') {
    document.body.classList.add('rtl');
  }

  if (!$GET.lang || !wording.email[$GET.lang]) {
    $GET.lang = 'en';
  }

  var $olympRegisterLogo = $('.olymp-register__logo');

  if ($GET.dark == 'true') {
    $olympRegisterLogo.prop('src', 'https://static.olymptrade.com/lands/common/olymp-logo_white.svg');
  } else {
    $olympRegisterLogo.prop('src', 'https://static.olymptrade.com/lands/common/olymp-logo_black.svg');
	}

  var $olympRegisterEmail = $('#olymp-register__email'),
		$olympRegisterPassword = $('#olymp-register__password'),
		$olympRegisterCurrencyTitle = $('#olymp-register__currencyTitle'),
		$olympRegisterAgreementTitle = $('#olymp-register__agreementTitle');
  var olympRegisterButton = document.querySelector('#olymp-register__button');
  var olympRegisterSuccessBlock = document.querySelector('.olymp-register__success');
  var olympRegisterSuccessTitle = document.querySelector('.olymp-register__success-title');
  var olympRegisterSuccessButton = document.querySelector('.olymp-register__success-button');
  var olympRegisterSuccessButtonTitle = document.querySelector('#olymp-register__success-button');

  $olympRegisterEmail.prop('placeholder', wording.email[$GET.lang]);
  $olympRegisterPassword.prop('placeholder', wording.password[$GET.lang]);
  $olympRegisterCurrencyTitle.html(wording.currencyTitle[$GET.lang]) ;
  $olympRegisterAgreementTitle.html(wording.agreementTitle[$GET.lang]) ;
  olympRegisterSuccessTitle.innerHTML = wording.successTitle[$GET.lang];
  olympRegisterSuccessButtonTitle.innerHTML = wording.successButton[$GET.lang];

  if ($GET.freereg == 'true') {
    olympRegisterButton.innerHTML = wording.freeRegister[$GET.lang];
  } else {
    olympRegisterButton.innerHTML = wording.button[$GET.lang];
  }

  var $olympRegisterCurrencyRadio2 = $('#olymp-register__currency-radio-2'),
  	olympRegisterCurrencyOptions = document.querySelector('.olymp-register__currency-options');


	// Create locale currency

  if ($GET.lang === 'ru' && $GET.local_currency !== 'false') {

		$olympRegisterCurrencyRadio2.prop('checked',0);

		// name=olymp-register-currency
		$olympRegisterCurrencyRadio2.before('<input id="olymp-register__currency-radio-1" type=radio value=rub name=currency checked>\n<label for="olymp-register__currency-radio-1"><img src="" alt="" id="olymp-register__rouble-sign"/></label>\n')


    if ($GET.dark == "true") {
      document.getElementById('olymp-register__rouble-sign').setAttribute('src', 'https://static.olymptrade.com/lands/affiliate-new-form/img/rouble_white.svg');
    } else {
      document.getElementById('olymp-register__rouble-sign').setAttribute('src', 'https://static.olymptrade.com/lands/affiliate-new-form/img/rouble_black.svg');
    }
	}


  if ($GET.lang === 'br' && $GET.local_currency !== 'false') {
		$olympRegisterCurrencyRadio2.prop('checked',0);

		$olympRegisterCurrencyRadio2.before('<input id="olymp-register__currency-radio-1" type=radio value=brl name=currency checked>\n<label for="olymp-register__currency-radio-1">R$</label>\n');
	}

  if ($GET.lang === 'tr') {
    $olympRegisterCurrencyRadio2.prop('checked',0);
    $('olymp-register__currency-radio-3').prop('checked',1);
    olympRegisterCurrencyOptions.classList.add('olymp-register__currency-options--tr');
	}

	// /Create locale currency



//
//
//


  var isSafari = navigator.vendor &&
    navigator.vendor.indexOf('Apple') > -1 &&
    navigator.userAgent &&
    navigator.userAgent.indexOf('CriOS') === -1 &&
		navigator.userAgent.indexOf('FxiOS') === -1;


	/**
	 * Тут как-то программно собирается адрес
	 * пока игнорирую
	 *
	 * Адрес для отсылки формы
	 * var linkPixel = "https://olymptrade.com/l/affiliate-new-form/affiliate?affiliate_id=191954&dark=false&horizontal=false&lang=ru&square=false&subid1=&subid2=&jwt_traffic=1";
	 */

  function getLink() {
    var protocol = window.location.protocol + '//';
    var host = 'olymptrade.com';
    if (window.location.host.indexOf('id-olymptrade.com') >= 0) {
      host = 'id-olymptrade.com';
    }
    var params = window.location.search
      .slice(1).split('&')
      .reduce(
        function (p, e) {
          var a = e.split('=');
          if (a[0] !== 'lref' && a[0] !== 'lrefch' && a[0] !== 'pixel' && a[0] !== 'af_siteid') {
            p += e + '&';
          }
          return p;
        },
        ''
      );
    var search = '/l/affiliate-new-form/affiliate?' + params;
    var result = protocol + host + search;
    return result;
  };
  var linkPixel = getLink() + 'jwt_traffic=1';



	jQuery(function($) {

		var $answer = $('#answer'),
		$form = $('form.olymp-register__form'),
		$popup = $('.popup');

		// ADD Timezone
		$form.append('<input type=hidden name=timezone value="' + -(new Date().getTimezoneOffset()/60) + '">');

		/**
		 * Обработчик на submit
		 */
		$form.submit(function (event) {
		event.preventDefault();

		// Easy validation
		// console.log($form);
		if($form[0].password.value.length < 5) {
			alert('A password must contain more than 4 characters\nTry again.');
			return;
		}

		// console.log(linkPixel);
		// return;

		$.post(location.origin + location.pathname + '?targetLink=' + encodeURIComponent(linkPixel), $form.serialize(), 'json')
			.done(function(resp) {
				var print;

				resp = resp instanceof String ? JSON.parse(resp) : resp;
				console.log('parsed response = ', resp);

				if(resp.answer.errors) {
					// Fail
					olympRegisterSuccessBlock.classList.remove('visible');
					print = '<div class=error><p>' + resp.answer.errors[0].code + '</p></p>' + resp.answer.errors[0].msg + '</p></div>';
				} else {
					// Success
					// $popup.removeClass('popup_hide');
					olympRegisterSuccessBlock.classList.add('visible');
					$form.find('.btn olymp-register__submit-button').prop('hidden', 1);
					print = JSON.stringify(resp.answer.data)
				}

				$answer.html(resp.notes + '<hr>' + print);
				// $answer.after('<pre>' + JSON.parse(resp).msg + '</pre>');
			});

		});

		$popup.find('.popup__title').html(wording.popup.title[$GET.lang]) ;
	  $popup.find('.popup__success').html(wording.popup.success[$GET.lang]) ;
	  $popup.find('.popup__redirect').html(wording.popup.redirect[$GET.lang]) ;
		$olympRegisterCurrencyTitle.html(wording.currencyTitle[$GET.lang]) ;
	})




  $('.popup__title').html(wording.popup.title[$GET.lang]) ;
  $('.popup__success').html(wording.popup.success[$GET.lang]) ;
  $('.popup__redirect').html(wording.popup.redirect[$GET.lang]) ;
	$olympRegisterCurrencyTitle.html(wording.currencyTitle[$GET.lang]) ;


</script>


<style>
#testing pre {
	word-wrap: break-word;
	white-space: pre-wrap;
	word-break: break-all;
}

#testing .error {
	border: 5px solid red;
	padding: 5px;
	color: #a33;
}
</style>

<div id="testing">
	<pre id="answer"></pre>

	<? if(OWNER): ?>
	<hr>
	<h3>Response success registration on target server </h3>
	<pre>
	{"data":{"data":null,"duo_auth":null,"duo_auth_state":null,"balance":null,"money_group":null,"session":null,"options":null,"avatar":null,"politics":null,"bonuses":null,"analytics":null,"jivo_settings":null,"payment_systems":null,"vip_status_amount":null,"has_kyc_applicant":null,"is_kyc_available":null,"successful_payment_count":null,"deals":null,"pairs":null,"risk_free_deals":null}}
	</pre>
	<? endif ?>
</div>