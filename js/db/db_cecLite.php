<?php
$ref = $_SERVER['HTTP_REFERER'];
header('Access-Control-Allow-Origin: ' . $ref);
header("Access-Control-Allow-Origin: *");
header("Vary: Origin");
header("Content-type: application/javascript; charset=utf-8");
header("Cache-Control: no-cache");
?>

"use strict";
	Cecutient.host= punycode.toUnicode(location.host);
//	console.log('startDB');
// шелаболиха - TEST
	if(/kpa\-|master|:90|baevoschool|(kb1\-sterlitamak)\.ru|test1|(шелаболиха)\.рф/i
		.test(Cecutient.host)) {  
		Cecutient.clonePpts( { 
			get PU () {_K.body().cr('div',{id:'infobar_Cec'}).innerHTML= "<div id=\"fontSize\"><span class=min700>Размер шрифта:</span> </div><div><span class=min700>Изображения</span> <i  class=\"disableimage\">OFF</i><i class=\"enableimage\">ON</i></div><div class=color><span class=min700>Цвет сайта</span> <i class=c1>Ц</i><i class=c2>Ц</i><i class=c3>Ц</i></div><div><button class=\"toExit\"><i class=\"fa fa-eye-slash\" aria-hidden=\"true\"></i> обычная версия сайта</button></div>"},
			displayBar: function() {
				if(!Cook.get("diz_alt") || Cook.get("diz_alt")==="n") {
					(_K.G('#infobar_Cec') || Cecutient.PU).hidden=1
				} else {
					(_K.G('#infobar_Cec') || Cecutient.PU).hidden=0;
					_K.G('#enableCec').hidden=1;
				}
				return _K.G('#enableCec').hidden;
			},
			color: function (color) {
				Cook.set({"cecColor": "color"+color});
				location.reload();
			},
			reset: function() {
				Cook.set({"cecColor": "", "cecFont": "", "cecImg": "", "diz_alt": "n"});
				location.reload();
			}
		}); 
	} else _K={log:['A suspicion of plagiarism of the script'],v:{diz:true}, prot:false};

	Cecutient.db && Cecutient.db.del();