<?php

class Thumb {

	const ALTS= \MODULES['Thumb']['alts'];

	public
		$WM_dir, $orig_dir,
		$mask = "#.+\.(jpe?g|png)$#";


	public function __construct($orig_dir = \DIR, $thumb_dir = null)
	{
		# Папка для полноразмерных изображений
		$this->orig_dir = $orig_dir;

		#Папка для миниатюр
		$this->thumb_dir = $thumb_dir ?? $orig_dir . "/thumb";

		$WM_dir =  $this->orig_dir . "WM";

		if(!is_dir($this->orig_dir))  throw new Exception("<b>$this->orig_dir</b> not dir!", 1);

		$this->orig_files = (new \DirFilter($this->orig_dir , $this->mask))->natSort();

		if(!count($this->orig_files))
		{
			return;
		}

		if(!is_dir($this->thumb_dir))  mkdir($this->thumb_dir, 0777, true);

		$this->thumb_files = (new \DirFilter($this->thumb_dir , $this->mask))->natSort();



		$this->relat_dir = '/' . Path::fromRootStat($orig_dir) . '/';

	}


	#== Добавление миниатюр изображений на страницу по маске
	function toPage ($width=220)
	:string
	{
		$out = '';
		if(!count($this->orig_files)) return $out;

		// var_dump ($this->thumb_files);

		# Удаляем старые миниатюры

		if(count($this->orig_files) < count($this->thumb_files))
		{
			foreach ($this->thumb_files as $f) {

				if (in_array(str_ireplace('/thumb', '', $f), $this->orig_files)) continue;

				unlink($f);
			}
		}

		// var_dump($this->thumb_files);

		foreach($this->orig_files as $i) {
			// $o_name = $i->getFilename();
			$i = \Path::fromRootStat($i);
			$full_th_name = $this->thumbName($i);

			$out .= "<a href=\"/$i\"><img src='/" . $full_th_name . "'class='thumb toBig' style='width:".$width."px;' alt='" .self::ALTS[rand(0,count(self::ALTS) - 1)]. "' /></a>\n";

			// var_dump($full_th_name);

			if(count($this->orig_files) === count($this->thumb_files))
				continue;

			# Создаём миниатюру
			if (!in_array($full_th_name, $this->thumb_files))
			{
				$this->createThumb($i);
			}

		}

		// var_dump ($out);
		return $out;

	} // toPage


	protected function thumbName($path)
	{
		$bn = basename($path);
		return str_ireplace($bn, 'thumb/' . $bn, $path);
	}


	protected function createThumb($i)

	{
		// $th_name = "thumb/" . $i-<getFilename();

		$size= getimagesize($i);
		list($w, $h, $type)= $size;
		$imgType = substr(strrchr($size['mime'], '/'), 1);
		$icfunc = "imagecreatefrom" . $imgType;
		/* var_dump(
			$o_name, $icfunc, function_exists($icfunc), $size['mime']
		); */
		if (!function_exists($icfunc)) {
			return;
		}
		$simg = $icfunc($i);
		$ratio_orig = $w/$h;
		/*  Вывод пропорциональных миниатюр
		if ($nw/$nh > $ratio_orig) {
			 $nw = $nh*$ratio_orig;
		} else {
			 $nh = $nw/$ratio_orig;
		}
		*/
		# Вывод вырезанной квадратной центральной области в миниатюрах
		$nw = 150; $nh = 150;
		$x_o=0; $y_o=0;
		if ($ratio_orig>=1) {
			$x_o = ($w - $h) / 2;
		} else {
			$y_o = ($h - $w) / 2;
		}
		$dimg = imagecreatetruecolor($nw, $nh);
		// var_dump('create img', $i->getPath() , $i->getPath() . "/$i->th_name");
		imagecopyresampled($dimg,$simg,0,0,$x_o,$y_o,$nw,$nh,$w-$x_o*2,$h-$y_o*2);

		$outfunc = "image" . $imgType;

		$quality = 80;
		if($imgType === 'png') $quality /= 10;
		/* var_dump(
			$o_name, $icfunc, function_exists($icfunc), $size['mime']
		); */
		if (!function_exists($outfunc)) {
			return;
		}

		$outfunc($dimg, $this->thumbName($i), $quality);
		//== WM
		// if (isset($WM_files) && in_array($file, $WM_files)) continue;

	} // createThumb




	# http://ruseller.com/lessons.php?rub=37&id=953 - ORIGINAL
	function addWM ($im) { //== Добавляем водяные знаки

		if(!is_dir($WM_dir))  mkdir($WM_dir);
		// Загрузка штампа и фото, для которого применяется водяной знак (называется штамп или печать)
		$stamp = imagecreatefrompng(HOME . '/Oformlenie/stamp.png');
		# $im = imagecreatefromjpeg($src);

		// Установка полей для штампа и получение высоты/ширины штампа
		$marge_right = 10;
		$marge_bottom = 10;
		$sx = imagesx($stamp);
		$sy = imagesy($stamp);

		// Копирование изображения штампа на фотографию с помощью смещения края
		// и ширины фотографии для расчета позиционирования штампа.
		imagecopy($im, $stamp, imagesx($im) - $sx - $marge_right, imagesy($im) - $sy - $marge_bottom, 0, 0, imagesx($stamp), imagesy($stamp));

		// Вывод и освобождение памяти
		header('Content-type: image/png');
		imagepng($im);
		imagedestroy($im);
	}

} // Thumb
