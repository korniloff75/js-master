<?php
class Download
{
	public function __construct()
	{
		$filename = $_REQUEST['filename'] ?? $_REQUEST['file'];

		if (empty($filename)) die ('Укажите корректный запрос в формате ?file=...');

		$filename = \DR."/$filename";

		tolog(__METHOD__,null,['$filename'=>$filename]);

		if(!file_exists($filename) && !is_array($filename))
		{
			die ("Не существует файла <b>" . $filename . "</b>");
		}


		ob_clean();

		if(isset($_REQUEST['toZip']) || is_dir($filename))
			$filename = $this->pack($filename);


		switch( pathinfo($filename, PATHINFO_EXTENSION) )
		{
			case "pdf": $ctype="application/pdf"; break;
			case "exe": $ctype="application/octet-stream"; break;
			case "zip": $ctype="application/zip"; break;
			case "doc": $ctype="application/msword"; break;
			case "xls": $ctype="application/vnd.ms-excel"; break;
			case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
			case "mp3": $ctype="audio/mp3"; break;
			case "gif": $ctype="image/gif"; break;
			case "png": $ctype="image/png"; break;
			case "jpeg":
			case "jpg": $ctype="image/jpg"; break;
			default: $ctype="application/force-download";
		}


		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // нужен для некоторых браузеров
		header("Content-Type: $ctype");
		header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($filename)); // необходимо доделать подсчет размера файла по абсолютному пути

		ob_end_flush();

		// var_dump($filename);

		readfile("$filename");

		exit;

	} // __construct


	public function pack($filename)
	{
		if(!empty($_GET['nameZIP'])){
			Pack::$name = filter_var($_GET['nameZIP'], FILTER_SANITIZE_STRING);
		}
		return (new Pack)->Directory($filename);

		// var_dump ($filename);
	}

} // Download

new Download;
