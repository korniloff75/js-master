<?php
# https://github.com/PHPMailer/PHPMailer
# https://medium.com/@alfakrai/%D0%B2%D0%BC%D0%B5%D0%BD%D1%8F%D0%B5%D0%BC%D0%B0%D1%8F-%D0%B8%D0%BD%D1%81%D1%82%D1%80%D1%83%D0%BA%D1%86%D0%B8%D1%8F-%D0%BA-phpmailer-51bf4530e2e4

if (version_compare(PHP_VERSION, '7.0', '<') ) exit("Извини, брат, с пыхом ниже 7 - не судьба!\n");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/src/Exception.php';
require_once __DIR__ . '/src/PHPMailer.php';
require_once __DIR__ . '/src/SMTP.php';


class MailPlain extends PHPMailer

{
	const
		ADMIN = \ADMIN ?? 0;

	public
		# Custom params

		# SMTP auth
		$Username  = "fb@js-master.ru",
		$Password = "1975kp@1975",
		$Host = "web01-cp.marosnet.net",
		$to_emails = [/* str || aray with emails */],

		# Common

		$Port = 465,
		$SMTPAuth = true,
		$SMTPSecure = "ssl",
		$CharSet    = 'UTF-8',

		$attach = null;


	public function __construct($subject, $message, $from_mail, $from_name = null)

	{
		$this->setLanguage('ru', __DIR__ . '/language/');
		parent::__construct(true);

		# 4 test
		if(!empty($GLOBALS['test'])) $this->to_emails = 'support@js-master.ru';

		$__smtp = [
			"on" => true,
			"isHTML" => true,
		];

		$this->validated = $this->valid([
			'name' => $from_name,
			'email' => $from_mail,
			'subject' => $subject,
			'message' => $message
		]);

		if(empty($this->validated)) {
			die("Отправляемые данные не прошли серверную валидацию. Попробуйте ещё раз.");
		}

		$this->FromName = $from_name;
		$this->Subject = $this->validated['subject'];
		$this->isHTML($__smtp['isHTML']);

		if($__smtp['isHTML']) {
			$this->validated['message'] = nl2br($this->validated['message']);
		}
		$this->Body = $this->validated['message'];

		if ($__smtp['on'])
		{
			$this->Mailer = 'smtp';
			$this->SMTPDebug = self::ADMIN ? 2 : 0;
			$this->IsSMTP();
			$this->setFrom($this->validated['email'], $this->validated['name']);
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
	public static function save($data)
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
	 * Telegram API
	 * MailPlain::toTG();
	 *
	 * Отправка GET-запросом
	 * $sendToTelegram = fopen("https://api.telegram.org/bot{$token}/sendMessage?chat_id={$tg_chat_id}&parse_mode=html&text={$tg_txt}","r");
	 */
	public static function toTG($chat_id = -1001433749294)
	{
		$token = "1028281410:AAHBV_yuvrXcjNNg4FvSfuR1-2vjKNVfwys";
		// $tg_txt = "<b>{$subject}</b> <pre>$message</pre> {$_REQUEST['email']}";

		$ch = curl_init();
		curl_setopt_array(
			$ch,
			[
				CURLOPT_URL => "https://api.telegram.org/bot{$token}/sendMessage",
				CURLOPT_POST => TRUE,
				CURLOPT_RETURNTRANSFER => TRUE,
				CURLOPT_TIMEOUT => 10,
				CURLOPT_POSTFIELDS => [
					'chat_id' => $chat_id,
					'parse_mode' => 'html',
					'text' => "<b>{$_REQUEST['subject']}\n{$_REQUEST['name']}</b> пишет:\n <pre>{$_REQUEST['message']}</pre>\n {$_REQUEST['tg']}\n{$_REQUEST['email']}",
				],
			]
		);
		/* var_dump(
			// $subject
		); */
		return curl_exec($ch);
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


		try {
			$this->Send();
			return true;

		} catch (phpmailerException $e) {
			if($__smtp['isHTML']) try {
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