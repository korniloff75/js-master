<?php
class WallMain {
	function __construct($filename= 'foto', $size= ['min' => '100px', 'max' => 500*1024], $path= 'uploadsWall/') {
		if(!is_dir($path)) mkdir($path, 0777, true);
		$this->db= $path . 'db.json';
		$this->min= $size['min'];
		$this->wallID= 'map';
		// echo '$size[\'min\']= ' . $size['min'];

		echo '<div id="' . $this->wallID . '" style=" position:relative; " title="Выберите место для добавления материала">';

		if(isset($_FILES[$filename])) {
			if(!$_FILES[$filename]['error']) {
				// Если есть изображениее
				$this->upload_file($filename, $path) ?? "Фото не загрузилось" ;
			}elseif($_FILES[$filename]['error'] == 4) $this->addStick();
			else print_r ($_FILES[$filename]);
		}

		$this->outputImgs($path);

		echo '</div>';

?>
<div>
	<style>
		#<?=$this->wallID ?> >* {position:absolute; }
		#<?=$this->wallID ?>, #<?=$this->wallID ?> >* {box-shadow: #3A2C20 0 0 9px 3px;}
		#<?=$this->wallID ?> .stick {overflow: hidden; background:#123; color:#afa; display:inline-block;}
	</style>

	<form name="addFoto" action="" method="POST" enctype="multipart/form-data" hidden> <!-- hidden -->
		<h5>Выберите фото для загрузки</h5>
		<input name=name type="text" required placeholder="Ваше имя">
		<input name=foto type="file" > <span>(max size - <b><?= $size['max']/1024 ?></b> kB)</span>
		<textarea name="comm" style="resize: vertical; height:90px;" placeholder="Ваш комментарий"></textarea>
		<input name=coord type="hidden" >
		<button>Загрузить фото</button>
	</form>
</div>
<?php

		$this->addJS();
	} #__construct


	private function rdb () {
		// read data base
		$rdb= json_decode(file_get_contents($this->db), 1);
		$rdb['comments']= $rdb['comments'] ?? [];

		foreach ($rdb as $key => &$value) {
			if(($key === 'comments') || (strlen($key)>=4)) continue;
			// echo 'key= ' . $key;
			$rdb['comments'][]= $value;
			unset($rdb[$key]);
		}
		unset($value);
		// var_dump($rdb);

		return $rdb;
	}

	// private

	private function outputImgs($path) {
		$rdb= $this->rdb();

		function getData ($cont) {
			if(!is_array($cont)) return var_dump($cont);

			$o= explode(',', $cont['coord'] );
			$o= array_map( function($i) {return $i . 'px';}, $o );
			$o[]=  " data-X=$o[0] data-Y=$o[1] data-author=\"{$cont['name']}\" data-comm=\"{$cont['comm']}\"";
			return $o;
		}

		foreach ($rdb as $name => $cont) {
			if($name === 'comments') continue;

			list($X,$Y,$attrs)= getData($cont);
			list($w, $h,$type)= getimagesize($path . $name);

			if(!$w || !$h)
			{
				# Вирусняк
				/* unlink($path . $name);
				continue; */
			}
			else
			{
				$orient= $w/$h >= 1;
				$wh= $orient? "width: $this->min": "height: $this->min";
				echo "<img src=\"/$path$name\" style=\"$wh; position:absolute; top: $Y; left: $X;\"  data-orient= \"" . $orient . "\" $attrs  title=\"{$cont['comm']}\" />\n";
			}

		}

		foreach ($rdb['comments'] as $cont) {
			list($X,$Y,$attrs)= getData($cont);
			echo "<div class=\"stick\" style=\"top: $Y; left: $X; width:$this->min; height:$this->min;\"  data-orient=1 $attrs title={$cont['name']}>"
				. "<b>{$cont['name']}:</b><br>"
				. preg_replace("/\n/u","<br>" , $cont['comm'] ?? 'NO comment')
				. '</div>';
		}
	}

	private function addToBD($filename=null) {
		$rdb= $this->rdb();
		$temp=[];

		foreach ($_POST as $prop => $value) {
			$temp[$prop]= strip_tags($value);
		}

 		if($filename && !$_FILES[$filename]['error']) {
			// Проверяем наличие файла
			if(empty($rdb[$_FILES[$filename]["name"]])) {
				$rdb[$_FILES[$filename]["name"]]= $temp;
			} else return null;

		} else {
			$rdb['comments'][]= $temp;
		}

		return $rdb;
	}

	private function addStick() {
		file_put_contents($this->db, json_encode($this->addToBD(), JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK), LOCK_EX);
	}

	function upload_file($filename, $path, $legalD=[]) {
/*		if (!count($_FILES)) echo "Массив _FILES пуст!<br>";
	//	else {
			echo 'FILES= '.'<br>';
			print_r($_FILES);
			echo '<br>';
			echo '_POST= '.'<br>';
			print_r($_POST);
			echo '<br>';
	//	}*/

		if($_FILES[$filename]["size"] > ($maxSize = \H::getMaxSizeUpload()))
		{
			throw new Exception ("Размер файла {$filename} превышает $maxSize байт");
		}

		if(strpos($_FILES[$filename]["type"], 'image') === false) {
			$ch=0;
			echo $_FILES[$filename]["type"];
		} else if(count($legalD)==0) $ch=1;
		else {
			foreach($legalD as $i) {
				if(preg_match("/$i/",$_FILES[$filename]['name'])) {
				$ch=1; break;
				}
			}
		}


	/*	echo '$this->rdb= ';
		print_r ($this->rdb);*/

		if(!$rdb= $this->addToBD($filename)) $ch=0;

		if(!$ch) {
		echo "<font color=red size=5><b>Файл " . $_FILES[$filename]["name"] . " неверный или уже присутствует в базе! Попробуйте еще раз...</b></font> <script type=\"text/javascript\">//setTimeout(function(){history.go(-1)},3500)</script>"; return false;
		}

	// Проверяем загружен ли файл
		if(is_uploaded_file($_FILES[$filename]["tmp_name"])) {
			list($w,$h) = getimagesize($_FILES[$filename]["tmp_name"]);

			# Фильтруем вирусы
			if(!$w || !$h)
			{
				var_dump($_FILES[$filename]["tmp_name"]);
				unlink($_FILES[$filename]["tmp_name"]);
				$this->upload_file(basename($_FILES[$filename]["tmp_name"]), $_FILES[$filename]["tmp_name"]);
			}
			else
				move_uploaded_file($_FILES[$filename]["tmp_name"], $path.$_FILES[$filename]["name"]);

			# echo"<font color=green size=5><b>Поздравляем! Файл успешно загружен!</b></font>";
			$coord= explode (',', $_POST['coord']);

			// Записываем файл в базу
			file_put_contents($this->db, json_encode($rdb, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK), LOCK_EX); // FILE_APPEND |

		} else {
			// Errors
			echo "<font color=red size=5><b>Ошибка загрузки файла! Попробуйте еще раз...</b></font>"; // .
		}
	} // upload_file




	function addJS() {
?>

<script>
	'use strict';

	var map= document.getElementById('<?= $this->wallID; ?>');
	[].forEach.call(map.querySelectorAll('img, .stick'), function (i) {
		i.defWidth= i.style.width || i.getBoundingClientRect().width+'px';
		i.defHeight= i.style.height || i.getBoundingClientRect().height+'px';
	}) ;
	var Wall= {
		eRouter: function(e) {
			var t= e.target;

			t= t.parentNode.hasAttribute('data-x')? t.parentNode: t;
			if(t.hasAttribute('data-x')) t.item= t.getAttribute('data-x');

			if(e.type === 'click') {
				if(t.hasOwnProperty('item')) Wall.clickItem(e,t);
				else  Wall.clickMap(e,t);
				// console.log("t.hasOwnProperty('item')= " , t.item);
			} else if(e.type === 'contextmenu') {
				if(t.item) Wall.cMenuItem(e,t);
				return false;
			}

			else if(e.type === 'mouseover' && t.item) Wall.hoverItem(e,t);
			else if(e.type === 'mouseout' && t.item) Wall.blurItem(e,t);

		},

		hoverItem: function(e,t) {
			t.style.transform= 'scale(1.5)';
			t.style.zIndex= 1000;
		},
		blurItem: function(e,t) {
			t.style.transform= 'scale(1)';
			t.style.zIndex= 5;
		},

		cMenuItem: function(e,t) {
			alert(t.getAttribute('data-author') + ":\n" + (t.getAttribute('data-comm') || 'NO comment'));
			return false;
		},

		clickItem:	function(e,t) {
			// var orient= t.width >= t.height? 'land': 'port';
			// data-orient == width/height >= 1
			this.hideForm(1);
			var orient= t.getAttribute('data-orient');
			// console.log("t.style.width= ", t.style.width, "\nt.item= ", t.item);
			if(t.defWidth === t.style.width || t.defHeight === t.style.height) {

				if(orient) {
					t.style.width=  map.getBoundingClientRect().width+'px';

					t.style.height= 'auto';
				} else {
					t.style.height=  map.getBoundingClientRect().height+'px';
					t.style.width= 'auto';
				}
				t.style.top= 0;
				t.style.left= 0;
				t.style.zIndex= 500;
				t.style.transform= 'scale(1)';
				map.onmouseover= map.onmouseout= function(e) { return false; }

			} else {
				if(orient) {
					t.style.width=  t.defWidth;
					t.style.height= 'auto';
				} else {
					t.style.height=  t.defHeight;
					t.style.width= 'auto';
				}

				t.style.top= t.getAttribute('data-Y');
				t.style.left= t.getAttribute('data-X');
				t.style.zIndex= 5;
				map.onmouseover= map.onmouseout= Wall.eRouter;
			}

			e.stopPropagation();
			return;
		},

		hideForm: function(h) {
			var F= document.forms['addFoto'];
			F.hidden= h;
			return F;
		},

		clickMap: function(e,t) {
			var	relX= e.pageX - window.pageXOffset - t.getBoundingClientRect().left,
				relY= e.pageY - window.pageYOffset - t.getBoundingClientRect().top;
			this.hideForm(0).coord.value= relX.toFixed() + ',' + relY.toFixed();
		}
	}; // Wall


		map.onclick= map.oncontextmenu= map.onmouseover= map.onmouseout= Wall.eRouter;

</script>


<?php
	}

} // WallMain


class Wall extends WallMain {
/* 	function __construct($filename= 'foto', $size= ['min' => '100px', 'max' => 500*1024], $path= 'uploadsWall/') {

	} */
}

?>



