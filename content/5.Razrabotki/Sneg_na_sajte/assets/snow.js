/*
<?php
header('Content-type: application/x-javascript');
# <script src="//js-master.ru/js/snow/snow.js" type="text/javascript" charset="utf-8"></script>
?>

*/
'use strict';
var Snow = {
	path: "/js/snow/",
	k:10,
	log:[]
};

Snow.log.push('start');
Snow.init = function snow() {
	var pObj = this;
	pObj.K_dim= pObj.k;
	console.log(pObj);
	pObj.log.push('pObj.K_dim= '+pObj.K_dim);
	var speed = 50, i,
		doc_width = $(window).width(),
		doc_height = $(window).height(),
		dx = [], xp = [], yp = [], am = [], stx = [], sty = [];

	for (i = 0; i < pObj.k; ++ i) {
		dx[i] = 0;
		xp[i] = Math.random()*(doc_width-50);
		yp[i] = Math.random()*(doc_height);
		am[i] = Math.random()*10+5;
		stx[i] = Math.random()/10+.05;
		sty[i] = Math.random()+.7;
		var rndPic=pObj.path+'flakes/png/'+Math.floor(1+Math.random()*5)+'.png';

		var $sn_im= $('body').cr('img', {id:'dot'+i,src:rndPic,style:"position:fixed;left:15px;top:15px;width:"+eval(10+Math.random()*10)+"px;height:auto;border:none;background:none !important;"} );

		$sn_im.on('mouseover', function() {
			if(!pObj) return false;
			$(this).css({transform:'scale(1.5)', cursor: 'url("' + pObj.path + 'oth181.cur"), crosshair'});

			pObj.K_dim--;
			setTimeout(this.remove(),50);
		});

		_H.defer.tocs.push($sn_im[0]);
	} // for

	function snow_dfd() {
		if(!pObj) {
			clearInterval(snowInit);
			return;
		}

		if(pObj.K_dim<Math.ceil(pObj.k/3)) {
			clearInterval(snowInit);
			alert('Ну, почти всех поймал(а), усложняем!!! =)')
			pObj.k*=3;
			return snow.call(Snow);
		}

		// var $dot;

		for (i = 0; i < pObj.k; ++i) {
			var $dot = $f("#dot"+i);
			if(!$dot[0]) continue;
			yp[i] += sty[i];
			xp[i] += stx[i];
			if (yp[i] > doc_height-70 || xp[i] > doc_width-20) { //== Круговорот снежинок
				xp[i] = Math.random()*doc_width; yp[i] = 0;
			}
			dx[i] += stx[i];

			$dot.css({top:yp[i] + "px", left: xp[i] + am[i]*Math.sin(dx[i]) + "px"});
		}
	} // snow_dfd

	var snowInit= setInterval (snow_dfd, speed);
}

$(Snow.init.bind(Snow));

console.log('LOG snow: \n'+Snow.log.join('\n'));