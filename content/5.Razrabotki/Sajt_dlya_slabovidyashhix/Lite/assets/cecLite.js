"use strict";
/******************************************
Copyright KorniloFF-ScriptS ©
https://js-master.ru
*******************************************/
window.Cecutient= {
	author: "//js-master.ru",
	__proto__: _K,
	v: {
		bodyTop: _K.body().style.marginTop,
		tags4change: "A$body,body div,body p,body span,h3,body a,table,td,tr,tbody,thead,header,footer,section,body li,body ul,ol",
		fs: {
			1:{fontSize:'14px',lineHeight: '30px'},
			2:{fontSize:'18px',lineHeight: '35px'},
			3:{fontSize:'23px',lineHeight: '40px'}
		}
	},

	ident: function (h) {
		var src = (h ? h + 'db.js' : this.author + "/js/db/db_cecLite") + '?req=' + _K.fns.rnd(0,1e5);

		// console.log('sc.src = ', src);

		return _K.G('$head' ).cr('script',{src: src, async:0, charset:"utf-8"}, 'after');
	},

	path: "/js/Diz_alt_LITE/",
	mainStyle: 'allstyles.css',
	check: Cook.get("diz_alt")==='y',

	Init: function () {
		Cook.set({"diz_alt": "y", "cecFont": "fontsize2", "cecColor": "color1"});
		location.reload();
	},

	cookiesFont : Cook.get ("cecFont") || 'fontsize2',
	cookiesColor : Cook.get ("cecColor") || 'color1',
	fontSize: function(s) {
		Cook.set({"cecFont": "fontsize"+s});
		location.reload();
	},
	disableImage: function() {
		Cook.set({"cecImg": "imgnone"});
		location.reload();
	},

	enableImage: function () {
		Cook.set({"cecImg": "imgyes"});
		location.reload();
	},


	handler: function() {
		// console.log('handler START');
		if (!Cecutient.check) return;

		Cecutient.createLink = _K.G('$head').cr('link',{type:'text/css',href:Cecutient.path + Cecutient.mainStyle, rel:'Stylesheet'}).cr('link',{type:'text/css',rel:'Stylesheet'},'after');

		// Cecutient.db.onload= function() { //== nE
			// console.log('Cecutient.db.onload');
			var PU= Cecutient.PU || _K.G('#infobar_Cec');
			// _K.l("Cecutient= ", Cecutient);
			if (Cecutient.displayBar()) {
				_K.body().style.marginTop='3.5em';
				Cecutient.createLink.href= Cecutient.path + 'style' + Cecutient.cookiesColor.substr(-1) + '.css';

				Object.keys(Cecutient.v.fs).forEach(function(i,ind) {
					var a= _K.G('#fontSize').cr('i', {class:'changea s' + i });
					_K.G(a,Cecutient.v.fs[i]);
					a.pos= i;
					a.textContent= 'A';
				});
				PU.onclick= function(e) {
					var t= _K.Event.fix(e).target;
					if(t.tagName==='I') {
						if(t.parentNode.className==='color') Cecutient.color(t.className.substr(-1));
						else if(t.pos) Cecutient.fontSize(t.pos);
						else if(t.className==='enableimage') Cecutient.enableImage();
						else if(t.className==='disableimage') Cecutient.disableImage();
					}
					if(t.className==='toExit') Cecutient.reset();
				}

				var fN= Cecutient.cookiesFont.substr(-1);
				[].forEach.call(_K.G(Cecutient.v.tags4change), function(i) { _K.G(i, Cecutient.v.fs[fN])});
				_K.G('$i.changea.s'+fN).classList.add("imageActive");
				[].forEach.call(_K.G('A$h1'), function(i) {i.style.fontSize= parseInt(Cecutient.v.fs[fN].fontSize) + 10 + 'px'});
				[].forEach.call(_K.G('A$h2'), function(i) {i.style.fontSize= parseInt(Cecutient.v.fs[fN].fontSize) + 7 + 'px'});

				if (!_K.G('$i.enableimage')) return;
				if (Cook.get ("cecImg")==='imgnone') { // diz_alt_Img
					[].forEach.call(_K.G('A$img'), function(i) {i.classList.add("none")});
					_K.G('$i.disableimage').classList.add("imageActive");
					_K.G('$i.enableimage').classList.remove("imageActive");
					[].forEach.call(_K.G('A$div,span,body,table,td,tr,a,li,ul,ol'), function(i) {i.style.background= 'none'});
				} else {
					[].forEach.call(_K.G('A$img'), function(i) {i.classList.remove("none")});
					_K.G('$i.enableimage').classList.add("imageActive");
					_K.G('$i.disableimage').classList.remove("imageActive");
				}
			} else {
				_K.body().style.marginTop= Cecutient.v.bodyTop;
			}
			// };

		}


} //== /Cecutient



_K.DR(function() {
	_K.G('$head' ).cr('script',{src: Cecutient.path + 'punycode.js', async:0}).onload = function(e) {
		var sc = Cecutient.ident(Cecutient.path);

		sc.onload = Cecutient.handler;
		sc.onerror = function(e) {
			// console.log('sc error');
			Cecutient.ident().onload = Cecutient.handler;
		};
	}



})
