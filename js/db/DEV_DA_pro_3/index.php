/*
<?php
header('Content-Type: application/javascript; charset=utf-8');
?>
*/

"use strict";

	DizSel.host= punycode.toUnicode(location.host);

	// console.log('startDB');

	//== closeDomains= /().ru/;

		// (centr\.com)\.ru

	// шелаболиха|10.12 centr\.com|28.04- TEST

	if(/kpa\-|master|:90|olenenok\.ucoz\.net|school\-(podosinovets)|voi\-(orenburg)|дши\-рышково|(kamsoccentr|(ww\.|^)bsch1|ub\-rddt|muzapacha|kb1\-sterlitamak|gymnasium8perm|ub\-kcson|u\-bdmsh|pedkolledj|onko\-sochi|ivint|ub\-mdk|ub\-biblion|ub\-museum|nekrinternat|dom\-veter|orelgerocentr|newurengoy|centr\-deti|gluhovskaya|zconnow|lotos\-med24|sodaplant|svt19|biblioteka15|mboumukvilino)\.ru|s10kuragino|(шатиловский\-интернат|тмкк|сонлышко|бабайки|музейхудекова|мдди|школа-2|колушки-интернат|кцсон\-орел|рсци)\.рф|(ds-romaschka-pnk.edu22)\.info|10\.16\.0\.173|185\.117\.153\.195|grav[.:]/i

		.test(DizSel.host)) {  //

//		console.log("startSTS ");

		_K.clonePpts(DizSel.sts, { //== Настройки скрипта default

			BG_dim: {'cs-white':'БЕЛЫЙ фон', 'cs-blue':'СИНИЙ фон', 'cs-black':'ЧЕРНЫЙ фон'},

			button: {

				image:"eye.gif",

				get value () {return !DizSel.v.DAlt? "ДЛЯ СЛАБОВИДЯЩИХ": "Обычный вид" }, callback:false,

				addImg: function(imgSrc,imgStyle,imgSrc_off) {

					DizSel.SlabovidButton= _K.G(DizSel.SlabovidButtonParent ).cr("img",{src:imgSrc, style:imgStyle },"fCh");

					_K.G(DizSel.SlabovidButton,{zIndex:500000, cursor:'pointer'}) // , transform:'scale(.8)'

					if(!!imgSrc_off) DizSel.SlabovidButton.e.add("click",function() {this.src= !DizSel.v.DAlt? imgSrc_off: imgSrc});

				}

			},

			elsPU: {}, //== Элементы ПУ //== Не работает

			mem:20,

			startCol: function() {

				switch (Cook.get('diz_alt_BG') || 'cs-white') {

					case 'cs-white': return '#113355';

					case 'cs-blue': return '#ffff3f';

					case 'cs-black': return '#f5f5f5';

					// default: return '#111177';

				}

			},

			fontSize: { fixed:false, min:12, step:2, iter:3, NoTags: /^(head|script|title|link|style|iframe|img|hr|br|code)$/i, h1Size:24}, //==

			imageOff: { minImg: 60},

			sound: {apikey: '92c24be9-c348-4c66-8086-33a1327a077b', speaker:"ermil", emotion: 'good', speed: .9},

			//== ermil, zahar, jane, levitan

			floatTip: {

				st: 'max-width: 250px; min-width: 50px; min-height: 25px; background: #f5f5f5;  border: 1px solid #666666; padding: 4px; font: 12pt sans-serif; color: #123; border-radius: 3px; box-shadow:2px 2px 1px 0 #bbb;',

				distX: 20,

				distY: 15

			}

		}, {enum:true});

		_K.prot=true;

		DizSel.log.push('pro' + (/kpa\-|js\-/i.test(DizSel.host)? '_Beta':'_W')+ '; v '+DizSel.version+'\n'+ DizSel.host + '\n' + _K.prot+'\n');

		DizSel.log.push("typeof(_K)==='object'= "+ (typeof(_K) === 'object'));

	} else {

		_K={log:['A suspicion of plagiarism of the script'],v:{diz:true}, prot:false, G:function(){return {}}, DR:function(){return false}};
		DizSel.SlabovidButton && DizSel.SlabovidButton.remove();
	}



	DizSel.addons.db.remove();



/*

*** школа-2.рф ***



DizSel.SlabovidButtonParent= _K.G('#header');



_K.DR(function() {

	function menu (e, node, disp) {

		if(t = node.closest('.sublnk')) {

			e.stopPropagation();

			t.G('$ul', {display : disp})

		}

	}



	_K.G('$#altObj #topmenu').e.add({

		mouseover : function(e) {

			menu(e,_K.G(e.target), 'initial');

		},

		mouseout : function(e) {

			menu(e,_K.G(e.target), 'none');

		},

	})

});







// *** колушки-интернат.рф ***



DizSel.SlabovidButtonParent= _K.G('$.wwide.pagebg');

_K.DR(function() {

	_K.G(DizSel.SlabovidButton, {padding : '3px 5px 3px 50px'});

	// ... см. школа-2.рф ...

})





// *** lotos-med24.ru ***



DizSel.checkUrl= '/js/Diz_alt_pro/';

DizSel.SlabovidButtonParent= _K.G('#butslab');

DizSel.SlabovidButton = _K.G('#diz_alt');

// _K.G(DizSel.SlabovidButton, { backgroundColor:'transparent', border:'none' });

*/