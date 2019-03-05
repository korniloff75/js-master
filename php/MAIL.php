<?php
// utf8-marker = $tx['utf-8']['marker'];
$H['protectScript'](basename(__FILE__));
/*
*	$email= new Mail([@email], [@subject]);
* Задаем адрес отправителя
*	$email->$from_mail= "no-reply@kpa-ing.ru";  # Адрес отправителя, по умолчанию - автосборка
*	$email->subject= "Комментарий со страницы...";
*	$email->send_mail($arr);
*/


// echo __DIR__;
// $kff['modules']['include']('noCaptcha');

########################

class Mail {

	public $email,
		$subject,
		$from_mail;
	protected $headers = NULL,
		$message;


	function __construct($email = null, $subject = null)
	
	{
		global $cf;
		$this->email = $email ?? $cf['mailform']['email'];
		$this->subject = $this->subject ?? $subject ?? "Без темы";
		$this->from_mail= "no-reply@".$_SERVER['HTTP_HOST'];
	}


	protected function check_for_linebreaks ($d)

	{
		return !preg_match("/%0A|\\r|%0D|\\n|%00|\\0|%09|\\t|%01|%02|%03|%04|%05|%06|%07|%08|%09|%0B|%0C|%0E|%0F|%10|%11|%12|%13/i", $d)? $d: 'Атакуешь, брат?';
	}


	protected function fixTxt ( $t, $type)
	
	{
		if(is_array($t)) {
			$out = [];

			foreach ($t as $i) {
				$tmp = $this->fixTxt($i, $type);
				$out['fix'][] = $tmp['fix'];
				$out['XSS'][] = $tmp['XSS'];
			}

			return ['fix'=> $out['fix'], 'XSS'=> $out['XSS']];
		} 

		switch ($type) {
			case 'toMail':
				$t= preg_replace("/\<br[^>]*?\>|\[br\]/","\n", trim($t)); break;
			case 'toRead':
				$t= preg_replace("/\[br\]/","<br>", trim($t)); 
				break;
			case 'toSave':
				if(!empty($this->maxLen) && (strlen($t) > $this->maxLen)) $t= substr($t,0,$this->maxLen) . '.....';
				if(!empty($this->separator)) $t= str_replace($this->separator,'~', $t);
				$t= preg_replace("/[\n\r]+|(\r\n)[^$]/","[br]",$t);
				break;
			default:
				$t=$t; break;
		}

		return ['fix'=> $t, 'XSS'=> htmlspecialchars($t,ENT_NOQUOTES|ENT_HTML401,'UTF-8',0)];
	}


	public function XSS($d, $type='toSave')
	
	{
		 # Отдает массив с fix и XSS
		$out= ['fix'=>[], 'XSS'=>[]];
		if(!is_array($d)) $d = [$d];

		foreach($d as $p => $i) {
			$i= $this->fixTxt($i,$type);

			$out['fix'][$p]= $i['fix'];
			$out['XSS'][$p]= $i['XSS'];
		}
/* 		var_dump($d);
		var_dump($out); */
		return $out;
	}


	public function send_mail($arr=array())

	{ # $arr - ассоциативный массив
		global $cf;
		$arr= $this->XSS($arr,'toMail')['XSS'];
		
		foreach($arr as $name=>$value) {
			if(!trim($value)) continue;
			if(in_array($name, array('Пост','Ответ'))) {
				$p= $name.": \n" . $value;
				continue;
			}
			if(is_string($name)) $this->message .= $name . ": " . $value . " \n";
		}

		// To: $this->email \r\n
		$headers = $this->headers ?? "Content-type: text/plain; charset=utf-8 " . PHP_EOL . "From: $this->from_mail " . PHP_EOL;
		if(!empty($this->Cc_mail)) $headers .= "Cc:$this->copy_mail " . PHP_EOL;
		if(!empty($this->Bcc_mail)) $headers .= "Bcc:$this->copy_mail " . PHP_EOL;
		$headers .= 'X-Mailer: PHP/' . phpversion();
		if(!empty($p)) $this->message.= "\n===============\n\n" . $p;
		
		# Выбираем функцию отправки
		$sf= function_exists('mb_send_mail')? 'mb_send_mail':'mail';
//		echo 'send_mail';
		return $sf($this->email, $this->subject, $this->message, $headers) ;
	}

} // class Mail
?>