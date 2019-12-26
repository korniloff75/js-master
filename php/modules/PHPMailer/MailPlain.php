<?php
# https://github.com/PHPMailer/PHPMailer
# https://medium.com/@shpaginkirill/вменяемая-инструкция-к-phpmailer-отправка-писем-и-файлов-на-почту-b462f8ff9b5c

/**
 *
 */

if (version_compare(PHP_VERSION, '7.0', '<') ) exit("Извини, брат, с пыхом ниже 7 - не судьба!\n");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/src/Exception.php';
require_once __DIR__ . '/src/PHPMailer.php';
require_once __DIR__ . '/src/SMTP.php';


class MailPlain extends PHPMailer

{
	const
		ADMIN = \ADMIN ?? 0,
		T_SUCCESS_SEND = "Ваше сообщение успешно отправлено!<br>Ожидайте ответа на указанный email",
		T_FAIL_SEND = "<div class=\"error\">Ваше сообщение не было доставлено.<br>Просим прощения за неудобство. При следующей отправке скопируйте текст сообщения в буфер обмена или в текстовый документ.</div>",

		TG_TOKEN = "1028281410:AAHBV_yuvrXcjNNg4FvSfuR1-2vjKNVfwys",
		// TG_CHAT_ID = -1001433749294; // Группа js-master
		TG_CHAT_ID = -1001245760492; // Cannel

	public
		# Custom params

		# SMTP auth
		$Username  = "korniloff_proekt@mail.ru",
		$Password = "1975kp@1975",
		$Host = "ssl://smtp.mail.ru",
		/* $Username  = "fb@js-master.ru",
		$Password = "1975kp@1975",
		$Host = "web01-cp.marosnet.net", */
		$to_emails = [/* str || aray with emails */],

		# Common

		/* $Port = 465,
		$SMTPAuth = true,
		$SMTPSecure = "ssl", */
		$CharSet    = 'UTF-8',

		$attach = null;

	private
		$SMTP = [
			"on" => true,
			"isHTML" => true,
		];


	public function __construct($subject, $message, $from_mail, $from_name = null)

	{
		$this->setLanguage('ru', __DIR__ . '/language/');
		parent::__construct(true);

		# 4 test
		if(!empty($GLOBALS['test'])) $this->to_emails = 'support@js-master.ru';

		$this->validated = $this->valid([
			'name' => $from_name,
			'email' => $from_mail,
			'subject' => $subject,
			'message' => $message
		]);

		if(empty($this->validated)) {
			die("Отправляемые данные не прошли серверную валидацию. Попробуйте ещё раз.");
		}

		$this->FromName = $this->validated['name'];
		$this->Subject = $this->validated['subject'];
		$this->isHTML($this->SMTP['isHTML']);

		$this->validated['messageNL'] = $this->validated['message'] = "<pre>{$this->validated['message']}</pre>\n"
		. "\nTime - " . date(\CF['date']['format'])
		. "\nEmail - {$this->validated['email']}"
		. "\nIP - " . \H::realIP()
		. ($_REQUEST['tg'] ? "\nTelegram - {$_REQUEST['tg']}" : "");

		if($this->SMTP['isHTML']) {
			$this->validated['message'] = nl2br($this->validated['message']);
		}

		$this->Body = $this->validated['message'];
		$this->AltBody = strip_tags($this->validated['messageNL']);

		if ($this->SMTP['on'])
		{
			$this->IsSMTP();
			$this->Port = 465;
			$this->SMTPAuth = true;
			$this->SMTPSecure = "ssl";
			$this->Mailer = 'smtp';
			$this->SMTPDebug = self::ADMIN ? 2 : 0;
			$this->setFrom($this->Username);
			// $this->setFrom($this->validated['email'], $this->validated['name']);
		}
		else
		{
			$this->From = $this->Username;
		}

	} // __construct


	protected function valid(array $input_vars)

	{
		# Прогоняем данные через фильтры
		return filter_var_array($input_vars, [
			'name' => [
				'filter' => FILTER_SANITIZE_SPECIAL_CHARS
			],
			'email' => [
				'filter' => FILTER_SANITIZE_EMAIL
			],
			'subject' => [
				'filter' => FILTER_SANITIZE_SPECIAL_CHARS
			],
			'message' => [
				'filter' => FILTER_DEFAULT
			],
		]);
	}


	# MailPlain::save($mailPlain->validated);
	# in handler.php
	/**
	 * Save input data in DB
	 */
	public static function save(array $data)
	{
		$answer = '';
		$m_save = [
			date(\CF['date']['format']) => [
				$data['name'], $data['email'], $data['subject'], $data['message'], $answer
			]
		];

		\H::json('db/email.json', $m_save);
	}


	/**
	 * collect body of main message
	 * in developing ...
	 * now use in the comments
	 */
	public static function collectMessage($arr)

	{
		$message = '';

		foreach($arr as $name=>$value) {
			if(
				!trim($value)
				|| in_array($name, ['time', 'email', 'IP'], 1)
			) continue;

			if(in_array($name, ['Post', 'Ответ'], 1))
				$name = "===============\n" . $name . ": \n";
			elseif(is_string($name))
				$name = $name . ": ";
			else $name = '';

			$message .= $name . $value . " \n";
		}

		return $message;
	}


	/**
	 * Telegram API
	 *
	 * https://api.telegram.org/bot1028281410:AAHBV_yuvrXcjNNg4FvSfuR1-2vjKNVfwys/getUpdates
	 *
	 * # To Me
	 * MailPlain::toTG(673976740);
	 * # To Cat
	 * MailPlain::toTG(677430081);
	 *
	 * Отправка GET-запросом
	 * $sendToTelegram = fopen("https://api.telegram.org/bot{$token}/sendMessage?chat_id={$tg_chat_id}&parse_mode=html&text={$tg_txt}","r");
	 */
	public static function toTG(
		$text = null,
		$chat_id = null
	)
	{
		require_once \HOME . 'php/tg/tg.class.php';
		$tg = new TG(self::TG_TOKEN);
		$chat_id = $chat_id ?? self::TG_CHAT_ID;

		return $tg->request([
			'chat_id' => $chat_id,
			'parse_mode' => 'html',
			'text' => $text,
		]);

	}


	public function uploads()
	{
		foreach($_FILES as $fd) {
			if($fd['error'] !== UPLOAD_ERR_OK)
				continue;

			$blacklist = ['phtml', 'php\d?', '.html?'];
			if(count(array_filter($blacklist, function($i) use($fd) {
				return preg_match('/\.' . $i . '$/i',$fd['name']);
			})))
			{
				throw new phpmailerException ("Недопустимый тип файла - {$fd['name']}");
				continue;
			}

			if($fd["size"] > ($maxSize = \H::getMaxSizeUpload()))
			{
				throw new phpmailerException ("Размер файла {$fd['name']} превышает $maxSize байт");
				continue;
			}

			$this->AddAttachment($fd['tmp_name'], $fd['name']);
		}
	}


	public function TrySend()

	{
		$to = $this->to_emails;
		if(is_string($to)) $to = [$to];

		if(!is_array($to) && !count($to))
		{
			if(defined('OWNER'))
				$to = \OWNER['email'];
			else die('Нет адреса получателя ' . __FILE__ . __LINE__);
		}

		foreach($to as $email) {
			$this->AddAddress($email);
		}

		if(defined('ADM_EMAIL'))
			$this->AddBCC(ADM_EMAIL, 'Developer');

		// var_dump($_FILES);
		if(count($_FILES))
			$this->uploads();

		# Send to Telegram
		$textToTG = "<b>{$this->validated['subject']}</b>\n {$this->validated['messageNL']}";
		$response = self::toTG($textToTG);

		/* \H::$tmp['tg'] = [
			'response' => $response,
		];
		\H::log([
			'echo \'response = \'',
			'var_dump(self::$tmp[\'tg\'][\'response\'])',
		]); */

		file_put_contents('response.json', json_encode($response, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK), LOCK_EX);

		/* var_dump(
			$response
			// , $this->validated
		); */

		if(isset($_REQUEST['NoSendEmail'])) return;


		try {
			$this->Send();
			return true;

		} catch (phpmailerException $e) {
			if($this->SMTP['isHTML'])
				try {
					$this->isHTML(false);
					$this->Send();
					return true;
				} catch (phpmailerException $e) {
					// var_dump($e);
					echo 'Mailer Error: ' . $this->ErrorInfo;
					return false;
					// return $e->xdebug_message;
				}
		} catch (Exception $e) {
			// var_dump($e);
			return false;
		}
	}
} // MailPlain



/** EXAMPLE

$subject = $_REQUEST['subject'] . ' - Обратная связь с ' . HOST;
$message = $_REQUEST['name'] . " пишет: \n\n{$_REQUEST['message']}";

$mailPlain = new MailPlain ($subject, $message, $_REQUEST['email'], $_REQUEST['name']);

if($send_succ = $mailPlain->TrySend())
{
	# Success
	echo "Ваше сообщение успешно отправлено!<br>Ожидайте ответа на указанный email";
}
else
{
	# Fail
	echo "Ваше сообщение не было доставлено.";
}
*/