/******************************************
Copyright KorniloFF-ScriptS ©
http://kpa-ing.ru
*******************************************/
/*
<?php
header('content-type: application/x-javascript');
require_once 'js.php';
#$SITE_name= global $SITE_name; // выдает ошибку
ob_start("compress_js");
# <script src="http://kpa-ing.ru/js/KorniloFF.js" type="text/javascript" gr_id=$GROUP_ID$ fixId=1 charset="utf-8" ant='no'></script>
# <script src="http://kpa-ing.ru/js/KorniloFF_beta.js" type="text/javascript" charset="utf-8" ant='no'></script>
?>
*/

"use strict";
if(!window.getComputedStyle) getComputedStyle= function(elem) { return elem.currentStyle };
 window._K= { 
	version:'3.2.0',
/*	SITE_name: (function() { return "<?=$SITE_name?>" || "KorniloFF-ScriptS©" })(), */
	v: {},
	log: [],
	sts: {noClonePpts: /^(version|body|v|log|G|prot|date|rnd_obj|scroll|fns)$/},
	G: function() {
		var arr;
		switch(arguments.length) {
			case 0: return _K.G(document ); // this!==window? this : document;
			break;
			case 1: 
				 if(typeof(arguments[0])=== "object" && arguments[0]!=null) return arguments[0].setPpts ? arguments[0] : this.clonePpts(arguments[0],_K);
				else if(typeof(arguments[0])=== "string") {
					if(arguments[0].charAt(0)==="#") return _K.G(_K.G( ).getElementById(arguments[0].substr(1))); 
					else if(arguments[0].charAt(0)==="$") return _K.G(_K.G( ).querySelector(arguments[0].substr(1)) );
					else if(arguments[0].substr(0,2)==='A$') arr= _K.G( ).querySelectorAll(arguments[0].substr(2));
					else if(arguments[0].charAt(0)===".") arr= _K.G( ).getElementsByClassName(arguments[0].substr(1));
					else if(arguments[0].charAt(0)==="|") arr= _K.G( ).getElementsByTagName(arguments[0].substr(1));
					else return _K.G().getElementById? _K.G().getElementById(arguments[0]):_K.G().layers? _K.G()[arguments[0]]: _K.G().all[arguments[0]]? _K.G().all[arguments[0]]: false;
				} else return false;
				if(!!arr)  {
					[].forEach.call(arr, function(i) {i=_K.G(i )});
					return arr;
				}
			break;
			case 2:
				if(!_K.G(arguments[0])) break;
				if(arguments[1] instanceof Object) { // стили задавать в формате JS
					for(var i in arguments[1]) {
						if (!arguments[1].hasOwnProperty(i)) continue;
						_K.G(arguments[0]).style[i]= arguments[1][i];
					}
				} else {
					if(typeof(arguments[1])==="string" && arguments[1].substr(0,2)==="++") _K.G(arguments[0]).innerHTML+=arguments[1].substr(2);
					else _K.G(arguments[0]).innerHTML=arguments[1];
				}
			break;
			case 3: 
				if(!_K.G(arguments[0])) break;
				_K.G(arguments[0],arguments[1]);
				if(!this.clonePpts(_K.G(arguments[0]),arguments[2]) ) _K.G(arguments[0])[arguments[1]]=arguments[2];
				return _K.G(arguments[0]);
			break;
		}
	}, //== /G
	prot: true,
	//=========================================/
	setPpts: function(pts,assert) { //== 
		pts= pts || {};
		return this.clonePpts(this,pts,assert);
	},
	clonePpts: function(objExt,obj,assert) { //== Клонируем свойства // Не изменять!
		Object.getOwnPropertyNames(obj).forEach( function(name) { 
		if (!!objExt[name] && !assert || _K.sts.noClonePpts.test(name)) { /* console.info('name exist= '+ name); */ return;}
			Object.defineProperty(objExt, name, Object.getOwnPropertyDescriptor(obj, name)); 
		});
		return objExt;
	},

	toggle: function(bool,y,n) {
		return bool? y: n;
	},
	dToggle: function(e) {
		if (this.hidden!==undefined) {
			if (this.style.display==='none') {this.hidden='hidden'; this.style.display='';} 
			this.hidden=!!this.hidden? false: 'hidden' ;
		} else this.d.toggle(this);
		if(!!e) this.event().fix(e).target.title= this.d.isHidden(this)? 'Показать': 'Скрыть';
	},
	d: {	
		hide: function (el) {
			if (!el.displayOld) {
				el.displayOld= el.style.display;
			}
			el.style.display = "none";
		},
		displayCache: {},
		isHidden: function (el) {
			if (this.hidden!==undefined ) return this.hidden;
			var width = el.offsetWidth, height = el.offsetHeight,
				tr = el.nodeName.toLowerCase() === "tr";
			return width === 0 && height === 0 && !tr ?
				true : width > 0 && height > 0 && !tr ? false :   getComputedStyle(el).display
		},
		toggle: function (el) {
			this.isHidden(el) ? this.show(el) : this.hide(el)
		},
		show: function (el) {
			if (getComputedStyle(el).display !== 'none') return;
			el.style.display = el.displayOld || "";
			if ( getComputedStyle(el).display === "none" ) {
				var nodeName = el.nodeName,  display;
				if ( this.displayCache[nodeName] ) {
					display = this.displayCache[nodeName]
				} else {
					var testElem = document.createElement(nodeName)
					_K.body().appendChild(testElem)
					display = getRealDisplay(testElem)
					if (display === "none" ) display = "block";
					_K.body().removeChild(testElem)
					this.displayCache[nodeName] = display
				}
				el.displayOld= display;
				el.style.display = display;
			}
		}
		
	},
	
	//== Использование _O =====================
	Append: function app(el,after) {
		console.assert(!!this, el+' не имеет '+this+'\nОшибка в _O.Append');
		switch (after) { 
			case 1: ; // after this
			case "after": this.nextSibling? this.parentNode.insertBefore(el, this.nextSibling): this.parentNode.appendChild(el); break;
			case 2: ; // before this
			case "before": this.parentNode.insertBefore(el, this); break;
			case "fCh": this.firstChild? this.insertBefore(el, this.firstChild): app(el); break; // первый потомок от this или просто потомок
			case undefined: (this||_K.body( )).appendChild(el); break;
		}
		return el;
	},
	cr: function(elem,objAttrs,after) { //== Тег, объект с аттрибутами, родительский элемент разметки
		var el= _K.G(_K.G().createElement(elem) );
		el.setAttrs(objAttrs); //.bind(elem)
		return (!this || typeof(this.Append)!=='function')? el: this.Append(el,after); // console.log(this)
	},
	Clone: function(o,after,deep) { //== Конфликт с MooTools - obj.clone
		return this.Append(o.cloneNode(deep||true),after);
	},
/*
	Append: function(el,after) {
		return _O.Append(el,this,after);
	},
*/
	setAttrs: function(objAttrs) { //== Добавляем аттрибуты
	 	for (var key in objAttrs) { 
			if (!objAttrs.hasOwnProperty(key)) continue;
			this.setAttribute(key.toString(), objAttrs[key]); 
		}
	},
	del: function() { return typeof (this.parentNode) ==='object'? this.parentNode.removeChild(this): null;},
	//== /Использование _O =====================
	
	body: function() { return (_K.G().compatMode=='CSS1Compat' )? _K.G(_K.G().documentElement  ): _K.G(_K.G().body ) },
	inner: function(node) { return !!node? _K.G(node).innerHTML: this.innerHTML },
	rnd_obj: function (kod,dim,id,delay) { // (0-innerHTML 1-href 2-src),массив,id,интервал(не обязательно)
		function ch() { 
			var rnd= Math.round(Math.random()*(dim.length-1));
			switch(kod) {
				case 0: _K.G(id,dim[rnd]); break;
				case 1: _K.G(id,"href",dim[rnd]); break;
				case 2: _K.G(id,"src",dim[rnd]); break;
				default: _K.G(id,kod,dim[rnd]);
			}
		};	ch();
		delay= delay || 3000;
		return setInterval(ch,delay);
	},
	date: function() { var date= new Date(); 
		return { ss:date.getSeconds(), min:date.getMinutes(), h:date.getHours(), d:date.getDate(), dn:date.getDay(), mon:date.getMonth()+1, year:date.getFullYear(), time:date.getTime()} 
	},
	fixDate: function () {
		var date= _K.date();
		for(var i in date) {
			if (!date.hasOwnProperty(i)) continue;
			date[i]= (date[i] < 10)? "0" + date[i]: date[i];
		}
		return date;
	},
	scroll: function() {  //== Кроссброузерно получаем ПРОКРУТКУ страницы
		return (window.pageXOffset != undefined) ?
		{ left: pageXOffset, top: pageYOffset }: scrollIE();
		scrollIE= function() {
			var top = this.body().scrollTop || 0;
			top -= this.body().clientTop;
			var left = this.body().scrollLeft || 0;
			left -= this.body().clientLeft;
			return { top: top, left: left };
		}
	},
	getOffset: function () { //== РАЗМЕРЫ элемента на странице
		var elem= !!this.tagName? this: arguments[0];
		if (elem.getBoundingClientRect) {   //== "правильный" вариант
			return getOffsetRect(elem);
		} else {      //== пусть работает хоть как-то
			return getOffsetSum(elem);
		}
		function getOffsetRect(elem) {
			var box = elem.getBoundingClientRect();
			var top  = box.top +  _K.scroll().top ;
			var left = box.left + _K.scroll().left ;

			return { top: Math.round(top), left: Math.round(left) }
		}
		function getOffsetSum(elem) {
			var top=0, left=0;
			while(elem) {
				top += parseInt(elem.offsetTop);
				left += parseInt(elem.offsetLeft);
				elem = elem.offsetParent;
			}
			return {top: top, left: left}
		}
	},
	Animate: function() {
		this.animate.init.call(this,arguments)
	},
	animate: {
		init: function(from, to, duration, fn, fnLast) { // duration ,ms
		//	console.info('this.stop= '+ this.stop);
			var start = new Date().getTime(),log=[]; // Время старта
			from= _K.parseData(from);
			to= _K.parseData(to);
			log.push('int= ',from,to,'/int');
			fnLast= fnLast || function() { };
			var pref= from[1] || to[1] || "", EI= from[3] || to[3] || "";
			var result //, animateIter, ;
			this.animateIter= false;
			if(!!this.stop) return;
			if(!this.animateIter) iter.call(this); // if(!_K.v.anTO) 
			function iter() { // функция итераций анимации
			//	console.info('!!! - 2 !!this.stop - '+ !!this.stop);
				if (!!this.stop) { this.animateIter= false; return};
				this.animateIter= true;
			    var now = (new Date().getTime()) - start; // Текущее время
			    var progress = now / duration; // Прогресс анимации
		//	 	result = Math.round((to[2] - from[2]) * _K.animate.delta(progress) + from[2]);
				result = ((to[2] - from[2]) * progress + from[2]);
				log.push('progress='+progress,'result='+result);
				
				fn(pref+result+EI,result);
				if(!!_K.v.anTO) clearTimeout(_K.v.anTO);
				if (progress < 1 ) { // Если анимация не закончилась или принудительно не остановлена, следующая итерация
					 _K.v.anTO= setTimeout(iter.bind(this), 20);
				} else { 
					this.animateIter= false;
					log.push('_K.v.anTO= '+_K.v.anTO);
					
					fnLast(); 
				}
			//	_K.log.push('animateIter bool= '+!!_K.animate.animateIter+'/'+_K.animate.animateIter);
			}
			this.stop=false;
		//	console.log(log);
		},
		delta: function(progress) {
			return progress; // Можно ввести любую функцию зависимости
		}
		
	},
	parseData: function(data) { //== Возвращает массив
		data= /(\D+)?(\d+)(\D+)?/.exec(data);
		data[2]= parseInt(data[2]);
		return data;
	},
	/*
	========== СОБЫТИЯ ==============
	*/
	event: function() {
		this.Event.elem= this;
		return this.Event;
	},
	Event: {
		fix: function(e) {
			e = e || window.event;
			if ( e.isFixed )   return e;
			e.isFixed = true;
			//== фиксим отключение события по умолчанию	_K.Event.fix(e).preventDefault()
			e.preventDefault = e.preventDefault || function(){this.returnValue = false}; 
			//== фиксим отключение всплытия событий	_K.Event.fix(e).stopPropagaton()
			e.stopPropagation = e.stopPropagaton || function(){this.cancelBubble = true};
		//	e.target = e.target || e.srcElement;
		    if (!e.relatedTarget && e.fromElement) e.relatedTarget = e.fromElement == e.target ? e.toElement : e.fromElement; 
			//== добавить pageX/pageY для IE
			if ( e.pageX == null && e.clientX != null ) {
				e.pageX = e.clientX + (_K.body().scrollLeft || 0) - (_K.body().clientLeft || 0);
				e.pageY = e.clientY + (_K.body().scrollTop || 0) - (_K.body().clientTop || 0);
			};
			//== добавить which для IE
			if (!e.which && e.button) {
				e.which = e.button & 1 ? 1 : ( e.button & 2 ? 3 : ( e.button & 4 ? 2 : 0 ) )
			};
		return e
		},
	/* 
		_K.Event.add(_K.G('frF16'), 'click', function() {spam('message',1,10)} ); 
	*/
		add: function(elem, type, handler) {  
			if (!!this.elem) { elem= this.elem; type=arguments[0]; handler=arguments[1]; }
			elem.addEventListener? elem.addEventListener(type, handler, false): elem.attachEvent? elem.attachEvent("on"+type, handler): elem["on"+type]=handler;
			this.elem= false;
		}, 
		
		del: function(elem, type, handler) {  
			if (!!this.elem) { elem= this.elem; type=arguments[0]; handler=arguments[1]; }
			elem.removeEventListener? elem.removeEventListener(type, handler, false): elem.detachEvent? elem.detachEvent("on"+type, handler): elem["on"+type]=null;
			this.elem= false;
		},
		stop: function(e) { (this.elem || _K).Event.fix(e).stopPropagation(); }
	},
	DR: function (handler,delay) {
		var called = false;
		function ready() { 
			if (called) return; called = true; 
			if (delay) return setTimeout( handler ,delay); else handler(); // if(typeof(handler)!='function')
		}
		if ( _K.G().addEventListener ) { 
			_K.G().addEventListener( "DOMContentLoaded", function(){ ready() }, false )
		} else if ( _K.G().attachEvent ) {  
			if ( _K.G().documentElement.doScroll && window == window.top ) tryScroll();
			_K.G().attachEvent("onreadystatechange", function(){
				if ( _K.G().readyState === "complete" )  ready();
			})
		}
		if (called) return;
		_K.Event.add(window,'pageshow', ready);
		if (called) return;
		_K.Event.add(window,'load', ready);
		function tryScroll() {
			if (called || !_K.body()) return;
			try {
				_K.G().documentElement.doScroll("left")
				ready()
			} catch(e) {	setTimeout(tryScroll, 20)	}
		}
	},

	
	fns: {
		showHighlight: function() { //== highlightjs.readthedocs.org/en/latest/api.html
			if (!_K.G('$code' ) || hljs.init) return;
			hljs.configure({useBR: false});
			[].forEach.call(_K.G('A$code' ), function(i) { if(i.hljs || i.parentNode==='PRE') return; i.title='выделить код'; i.onclick= _K.fns.select.bind(i);  _K.G( i,_K.fns.validate(_K.inner(i)));  hljs.highlightBlock(i); i.hljs= true; });
		
			if(!_K.G('$pre>code' )  ) return; 
		//== Использование /scripts/highlight/highlight.pack.js
			[].forEach.call(_K.G('A$pre>code' ), function(cA) {
				if(!!cA.getAttribute('for')) {
					var tmpDiv= _O.cr('div');
					_K.G(tmpDiv, _K.inner(cA.getAttribute('for')) );
					[].forEach.call(tmpDiv.querySelectorAll('div'), function(i) { if (i.style.display==='none') _K.G(i).del() });
					_K.G(cA, _K.fns.validate(tmpDiv.inner()) );
				}
				var headCode= cA.cr('div',{class:"center", style:'position:relative;'},'before');
				if(!cA.getAttribute('saldom')) _K.G(headCode, '<p class="center green"><span class="copy_yes"></span>Код свободен для использования. Вы можете заказать адаптацию скрипта для своего сайта.</p>' + _K.fns.fb.b);
				if(!cA.getAttribute('saldom') || cA.getAttribute('saldom')!=='noLib') {
					_K.G(headCode, '++<span style="font-weight:bold;">Я впервые ставлю скрипт с этого сайта <input type="checkbox" onchange="_K.fns.addLib.call(this)"/></span> ' );
					_K.G(headCode, '++<img class="helpLib" class="none" alt="help" title="help" src="/Oformlenie/Keys/Help.png" style="width:20px; cursor: help; margin-bottom: -5px; position:absolute;" /><blockquote class="left" style="display:none;">Отметьте это поле, если вы ни разу не обращались к моим услугам и самостоятельно не копировали код с моего сайта. В дальнейшем, при установке других скриптов, это поле отмечать уже не нужно.</blockquote>' );
				}
				hljs.highlightBlock(cA);
				cA.hljs= true;
			});
			[].forEach.call(_K.G('A$.helpLib' ), function(i) {var b=i.parentNode.querySelector('blockquote'); i.onclick= _K.G(b).dToggle.bind(b) }); 
			//	hljs.initHighlighting(); // initHighlightingOnLoad()
			hljs.init= true;
		},
		addLib: function  () {
			var p=  this.parentNode,
				lib= _K.fns.validate('<script src="//kpa-ing.ru/js/KorniloFF.js" type="text/javascript" gr_id=$GROUP_ID$ fixId=1 charset="utf-8" ant="no"></script> \n ');
			while (p.tagName!=='PRE') {
				p= p.parentNode;
			}
			var code= p.querySelector('code');
			this.cash= this.cash || code.innerHTML;
			code.innerHTML= this.checked? lib+this.cash : this.cash;
			hljs.highlightBlock(code);
		},
		validate: function (cont) {
			return cont.replace(/\</gm,'&lt;').replace(/\>/gm,'&gt;').tabTrim();
		},
		
		
		fb: { //== _K.fns.fb.b - кнопка для feedback
			t:false,
			b:'<p><input class="btn" type="button" value="Обратная связь" title="Обратная связь" onclick=" if (!_K.fns.fb.t) {_K.G(_K.body(), \'++\'+Ajax.open(\'POST\',\'/Txts/FeedBack.php\').resp); _K.fns.fb.val(); _K.fns.fb.t=true} "></p>',
			val: function() { if(_K.G('$h1')) _K.G('#inputSubject' ).value= _K.G('$h1').textContent } //== Тему из заголовка
		//	val: function() { if(_K.G('$h1')) _K.G('#inputSubject' ).value= _K.G('$h1').innerHTML.replace(/^([\s\S]+?)\&[\s\S]+$/,'$1`') } //== работает
		},
		
		
		
		captureEvents: function (e,tags) { 
			tags= tags || 'INPUT|TEXTAREA|SELECT'; ; e=_K.Event.fix(e)
			if (Check.re(tags.toUpperCase(), e.target.tagName, 'i')) e.preventDefault();
		},
		noCopy: function(tags) {
			this.onselectstart =this.onselect =this.ondblclick =this.onmousedown =this.oncontextmenu = function(e) {return _K.fns.captureEvents(e,tags)};
		},
		
		is_array: function () { //== Проверка массива _K.fns.is_array.call([1,2,3])
			return (typeof this === "object") && (this instanceof Array);
		},
		in_array: function(dim,ext) { //== Аналог функции in_array() в РНР
			for(var i = 0, item; item = dim[i++];)	{ if(item == ext) return true; }
			return false;
		},
		select: function() { //== Выделение текста
			var rng, sel;
			if ( document.createRange ) {
				rng = document.createRange();//создаем объект область
				rng.selectNodeContents( this ); //== Содержимое текущего узла (selectNode - сам узел)
				sel = window.getSelection();//Получаем объект текущее выделение
				var strSel = sel.toString(); 
				if (!strSel.length) { //Если ничего не выделено
					sel.removeAllRanges();//Очистим все выделения (на всякий случай) 
					sel.addRange( rng ); //Выделим текущий узел
				}
			} else {
				document.body.createTextRange().moveToElementText( this ).select();
			}
		},
		rgbToHex:  function(rgb) { //== Преобразуем цвета
			if (rgb.charAt(0)==='#') return rgb;
			var hex= '#';
			rgb.match(/\d+/g).forEach(function(i) {hex += parseInt(i).toString(16)});
			return hex;
		}

	} //== /fns

}; //== /_K

//===========================================================================================/
//========================================= / 
//===========================================================================================/
window.Cookie= window.Cook= { //== Конфликт с MooTools - window.Cookie
	set: function( dim, timeout, path ) { // в днях
			timeout = (timeout || 1)* 1000*60*60*24;
			path= path || "/";
			var expires = (new Date((new Date).getTime() + timeout)).toUTCString();
			if(dim instanceof Array) for (var i=0, L=dim.length; i<L; i++) _K.G().cookie = dim[i][0] + '=' + dim[i][1] + ';expires=' + expires + "; path="+path ;
			else if(dim instanceof Object) { 
				for (var i in dim) {
					if (!dim.hasOwnProperty(i)) continue;
					_K.G().cookie = i + '=' + dim[i] + ';expires=' + expires + '; path=' + path ;
				};
			}
	},
	get: function (cookie_name) { 
		if(!cookie_name) return;
		var m= _K.G( ).cookie.match ( '(^|;)?' + escape(cookie_name.toString()) + '=([^;]*?)(;|$)' );
		return m && m.length? unescape(m[2]): null;
	},
	del: function (c_names) { // перечислить удаляемые кукисы через запятую
			var cookie_date = new Date (),  // Текущая дата и время
				path;
			cookie_date.setTime ( cookie_date.getTime() - 1 );
			for(var i = 0, L=arguments.length; i<L; i++) {
				if(!Cookie.get(arguments[i])) {path= arguments[i]; continue;}
				 _K.G().cookie = arguments[i] + "=; expires=" + cookie_date.toGMTString() + "; path=" + path || "/";
			//	 console.info('cookie_date - '+arguments[i]+'= '+ cookie_date+'= '+cookie_date.toGMTString());
			}
		}
}

//===========================================================================================/
//========================================= / 

window.Glob_vars= {
	GR_ID:0, fixId:1, i:0,
	path: function() { return window.location.href; },
	host: function() { return window.location.host; }, //хост и порт
	Get: function() {	/* Glob_vars.Get().key */
		if(window.location.search.length===0) return false;
		var out= {}, key= location.search.replace(/\?/,"").split(/\&/), dimkey;
		for (var i in key) { 
			if (!key.hasOwnProperty(i)) continue;
			dimkey= key[i].toString().split(/\=/);
			if(dimkey.length==2) out[dimkey[0]] = decodeURI(dimkey[1])
		//	else if()
		};	return out;
	},
	trust: function() { Cookie.set({trust: true},30); return false},
	date: function() { return _K.fixDate() },
	errChk: "Нет ошибок! =)",
	sh: true
} 

//=========================================/
window.Check= {
	re: function(reg,str,fls)	{ return typeof reg.test==='function'? reg.test(str): new RegExp(reg,fls||'').test(str) },
	match: function(reg,str) { return  str.match(reg)},
	name: function(name) { return Check.re(/^[\w\W\d]{3,15}$/i, name) },
	mail: function(mail) { return Check.re(/^.+?@.+?\.[\w\W\.]{2,}$/i, mail) },
	mess: function(name) { return Check.re(/^[\w\W]{3,}$/i, name) }
}

//=========================================/
window.Brows= {
	appName: navigator.appName,
	appCodeName: navigator.appCodeName,
	ua: navigator.userAgent,
	opts: [/MSIE/,'Firefox','Opera','Chrome','Safari','Konqueror','Iceweasel','SeaMonkey'], // |like Gecko;
	name: function() {
		for (var i = 0, item; item = this.opts[i++];) {
			if(Check.re(item,this.ua,'i')) return i===0? 'MSIE': item;
		};
	return 'ХЗ'
	}
};
_K.log.push('v '+_K.version+'\n'+Glob_vars.host());
_K.log.push('Brows.name()= '+ Brows.name() + '\nBrows.ua= '+navigator.userAgent+'\nBrows.appName= '+ Brows.appName);
//=========================================/
//================ PrototypeS ===================/

//== Заполнение строки
String.prototype.pad = String.prototype.pad || function(l, s, t) {
	if ((l -= this.length) > 0) {
		s = s || " "; //по умолчанию строка заполнитель - пробел
		t = t || 1; //по умолчанию тип заполнения справа
		s = s.repeat(Math.ceil(l / s.length));
		var i = t==0 ? l : (t == 1? 0 : Math.floor(l / 2));
		s= s.substr(0, i) + this + s.substr(0, l - i);
		return s;
	} else return this;
}

//== повторить заданную строку n раз
String.prototype.repeat = String.prototype.repeat || function(n) { return new Array( n + 1 ).join(this); }
// Вывод текста "печатной машинкой"
String.prototype.delayingWrite = function(obj, delay,fnLast, timeLast ) { 
	if (this.length) { 
		_K.G(obj,"++"+ this.charAt(0)); 
		var s = this.substr(1); 
		setTimeout(function(){s.delayingWrite(obj, delay,fnLast, timeLast);},delay);
	} else if (typeof(fnLast)==="function") {
		setTimeout(fnLast,timeLast || delay*10);
	}	
}

String.prototype.trim= String.prototype.trim || function(){return this.replace(/^\s+|\s+$/gm, '')};
String.prototype.ltrim= String.prototype.ltrim || function(){return this.replace(/^\s+/gm,'')};
String.prototype.tabTrim= function(){return this.replace(/\t/gm,' ')};
String.prototype.rtrim= String.prototype.rtrim || function(){return this.replace(/\s+$/gm,'')};
String.prototype.fulltrim= String.prototype.fulltrim || function(){return this.replace(/(?:(?:^|\n)\s+|\s+(?:$|\n))/gm,'').replace(/\s+/gm,' ');};
String.prototype.translit= String.prototype.translit || function (course) { 
	var Chars = {
		'а': 'a', 'б': 'b', 'в': 'v', 'г': 'g', 'д': 'd',  'е': 'e', 'ё': 'yo', 'ж': 'zh', 'з': 'z', 'и': 'i', 'й': 'y', 'к': 'k', 'л': 'l', 'м': 'm',  'н': 'n', 'о': 'o', 'п': 'p',  'р': 'r', 'с': 's', 'т': 't', 'у': 'u', 'ф': 'f', 'х': 'h',  'ц': 'c', 'ч': 'ch','ш': 'sh', 'щ': 'shch', 'ъ': '',  'ы': 'y', 'ь': '',  'э': 'e', 'ю': 'yu', 'я': 'ya',
		'А': 'A', 'Б': 'B',  'В': 'V', 'Г': 'G', 'Д': 'D', 'Е': 'E', 'Ё': 'YO',  'Ж': 'ZH', 'З': 'Z', 'И': 'I', 'Й': 'Y',  'К': 'K', 'Л': 'L', 'М': 'M', 'Н': 'N', 'О': 'O',  'П': 'P',  'Р': 'R', 'С': 'S', 'Т': 'T',  'У': 'U', 'Ф': 'F', 'Х': 'H', 'Ц': 'C', 'Ч': 'CH', 'Ш': 'SH', 'Щ': 'SHCH', 'Ъ': '', 'Ы': 'Y', 'Ь': '', 'Э': 'E', 'Ю': 'YU', 'Я': 'YA'
	},t=this;
	for (var i in Chars) { t= t.replace(new RegExp(i,'g'), Chars[i]);  }
	return t;
};

Math.sign = Math.sign || function(x) {
  x = +x; // преобразуем в число
  return (x === 0 || isNaN(x))? x: x > 0 ? 1 : -1;
}
//=========================================/
Function.prototype.bind = Function.prototype.bind || function(oThis) {
	if (typeof this !== 'function') {
	  // ближайший аналог внутренней функции
	  // IsCallable в ECMAScript 5
	  throw new TypeError('Function.prototype.bind - what is trying to be bound is not callable');
	}
	var aArgs = Array.prototype.slice.call(arguments, 1),
		fToBind = this,
		fNOP    = function() {},
		fBound  = function() {
		  return fToBind.apply(this instanceof fNOP && oThis? this: oThis, aArgs.concat(Array.prototype.slice.call(arguments)));
		};
	fNOP.prototype = this.prototype;
	fBound.prototype = new fNOP();
	return fBound;
 };
 
//=========================================/
//=========================================/ ============================================================================================/
//=========================================  СОВМЕСТИМОСТЬ  ========================================//

var bodyF= bodyF || function() {return _K.G('$body') } ;
var headF= (function() {return _K.G('$head')}());
var DOMready = DOMready || function (handler){ return _K.DR(handler)};
//=========================================/
var setCookie= setCookie || function ( dim, timeout ) { Cookie.set( dim, timeout ); };
var getCookie= getCookie || function (cookie_name) { return Cookie.get(cookie_name)};
var vis= vis || {	
	y: function(el) {_K.d.show(el);},
	n: function(el) {_K.d.hide(el);}
};

function regTest(reg,str,fl)	{ return Check.re(reg,str,fl) } ;
function getOffset(elem) { return _K.getOffset(elem) };

var Event= Event || _K.Event;
var fixEvent= fixEvent || function (e) {return _K.Event.fix(e)};
var addEvent= addEvent || function (elem, type, handler) {return _K.Event.add(elem, type, handler)};
_K.log.push('Event= '+ Event);
_K.log.push('typeof(Event.add)= '+ typeof(window.Event.add));
var cE= cE || function (elem,objAttrs,parrent,after) { return _K.G(parrent ).cr(elem,objAttrs,after) };
var setAttrs= setAttrs || function (elem,objAttrs) {return _K.G(elem ).setAttrs(objAttrs)};
var insertAfter= insertAfter || function (elem, refElem) { return _K.G(refElem ).Append( elem,'after' ); };
window.G= {	
	id: function(n) {return _K.G(n)},
	inner: function(id,n) { _K.G(id,n)},
	cl: function(n) {return _K.G("."+n) || false},
	tag: function(n) {return _K.G("|"+n)},
	style: function(n) {return _K.G(n).style},
	IE: '\v'=='v', // true only in IE)   !+"\v1"
	randnum: function(rmin, rmax) { return Math.floor(Math.random() * (rmax - rmin + 1)) + rmin; },
	Properties: function(obj) {
		var result = "The properties for the " + obj + "\r\n ";
		for (var i in obj) {
			result += i + " = " + obj[i] + "\n";}
		return result;
	}
};
//========================================= 
var _O= _O || {
//==============
	append: function app(el,parrent,after) { return _K.G(parrent ).Append(el,after); },
	cr: function(elem,objAttrs,parrent,after) { return _K.cr.call(parrent||null,elem,objAttrs,after); }, //== NoEdit!
	clone: function(o,parrent,after,deep) { return _K.G(parrent ).Clone(o,after,deep);},
	extend: function (obj,proto) { return _K.G(obj ).setPpts( proto); },
	del: function() { return _K.G(this).del() }
};

//========================================  / СОВМЕСТИМОСТЬ  =======================================//
//=========================================/ ============================================================================================/
//=========================================/ /
_K.Event.wait= { // Создаем изображение во время ожидания
	start: function() { 
		if(!!_K.Event.wait.init) return;
		_K.Event.wait.init= _O.cr('img', {style:'max-width:300px;height:auto;position:fixed; z-index:20000; border:none;',src:"//kpa-ing.ru/Oformlenie/wait3.gif",alt:"Загрузка..."},_K.body()); 
		_K.G(_K.Event.wait.init, {
			top: _K.body().clientHeight/2-parseInt(getComputedStyle(_K.Event.wait.init).height)/2+'px', 
			left: _K.body().clientWidth/2-parseInt(getComputedStyle(_K.Event.wait.init).width)/2+'px' 
		} )
	},
		end: function() { if(!!_K.Event.wait.init)  {_O.del.call(_K.Event.wait.init)}; _K.Event.wait.init=null; }
};
//=========================================/
/* ========================================= ALERT ========================================= */
if(!Check.re(/msie\s[5-9]\.0/i, Brows.ua) ) {
//========================================= /
window.alert= function(txt,sts) { // sts={width:500}
		sts= _K.clonePpts(sts||{},{ width:320, col:'#fff', bgCol:'#33e'});
		if(Check.re(/\n/,txt) && typeof txt==="string") txt=txt.replace(/\n/g, "<br />");
		if(!_K.G('#alert_OK')) { 
			Glob_vars.alFon= _K.G('$body' ).cr('div',{style:'width:100%;height:100%;position:fixed;top:0;left:0;background:#999;z-index:49000;opacity:.8'} );
			Glob_vars.alObj= _K.G('$body' ).cr('div',{id:"alert_OK", style:"display:block; width:"+sts.width+"px; text-align:center; font:16px/1.5 monospace,Georgia,serif; background:"+sts.bgCol+"; padding:10px; border: ridge 2px "+sts.col+"; color:"+sts.col+"; position:fixed; top:50%; left:50%; margin-left:-"+sts.width/2+"px; border-radius:10px; overflow:auto; cursor:default; z-index:50000; opacity:1;"} );
			_K.G(Glob_vars.alObj, "<div>Сайт <a href='http://kpa-ing.ru' style='color:"+sts.col+"; font:bold 10px italic,monospace; text-decoration:none;'>"+Glob_vars.host()+"</a> сообщает:<span style='display:inline; color:#fff;background:red; padding:3px; float:right; cursor:pointer; font:normal 16px Verdana;' onmouseover='this.style.fontWeight=\"bold\"' onmouseout='this.style.fontWeight=\"normal\"'>X</span></div><hr style='clear:both;' />"+ txt);
			if(Glob_vars.alObj.offsetHeight>_K.body().clientHeight*.8) Glob_vars.alObj.style.height= _K.body().clientHeight*.8+'px';
			Glob_vars.alObj.style.marginTop= -Glob_vars.alObj.offsetHeight/2+"px";

			Glob_vars.al_TO= setTimeout(cl_al,300000);
			Glob_vars.alObj.event().add("click",cl_al);
			_K.v.cl_al= function(e) { if (_K.Event.fix(e).keyCode == 27) cl_al(); }
			_K.G().event().add("keyup", _K.v.cl_al);
			return true;
			} else return false;
		function cl_al() {
			if(Glob_vars.al_TO) clearTimeout(Glob_vars.al_TO);
			Glob_vars.alFon.del(); Glob_vars.alObj.del(); if(Glob_vars.al_TO) clearTimeout(Glob_vars.al_TO);
			_K.G().event().del("keyup", _K.v.cl_al);
		} 
	}
}

function al(txt,sts)
{ txt= txt || "Предупреждение!!!\nНет аргумента."; 
  return alert(txt,sts);
};
/*/========================================= /
//========================================= /
	 Подключение: _K.Event.add(document,"copy",function(e) {return Protection.copy(e,1)}) // 1- к скопированному контенту прибавляется ссылка на сайт
	 Или: Protection.copyInit(1);
//========================================= /*/

window.Protection= {
	copy: function (e,cont,tags) {
	//	tags= tags || 
		if (Check.re(/INPUT|TEXTAREA/i, _K.Event.fix(e).target.tagName)) return null;
		cont= (cont)? "": "visibility:hidden;";
		var sel = (window.getSelection)? window.getSelection(): _K.G().selection.createRange(); 
		var txt='';
		for (var i in sel) {
			if (!sel.hasOwnProperty(i)) continue;
			txt+=i+' = '+sel[i];
		};
		var pagelink = "<p>Источник: <a href='"+_K.G().location.href+"'>"+location.href+"</a> ("+_K.SITE_name+")</p>";
		var copytext = sel + pagelink;
		var newdiv = _O.cr('div',{ style:"position:absolute; left:-9999px; "+cont },_K.G('$body'));
		_K.G(newdiv,copytext);
		sel.selectAllChildren(newdiv);
		window.setTimeout( function() { _K.G('$body').removeChild(newdiv);}, 50);
	},

	copyInit: function(cont) { 
		_K.DR(function() { _K.Event.add(_K.G(),"copy",function(e) {return Protection.copy(e,cont)}) }); // cont=cont||null;
	},

	opl: function () {  
		function captureEvents(e) { 
			if (!Check.re(/INPUT|TEXTAREA|SELECT/i, _K.Event.fix(e).target.tagName))  return false; 
			return true;
		};
		var filter= {
			wl:[ 
			//	|mbou-1
				'skazka29\\.taba\\.ru' ,'artem.katrich.net', 'kuch\\-pni' , /(?:chool3|(?:schka|zka|schko|ibka|erez)\-pnk|komitet)\.edu22|(?:detsad11r)\.ucoz/
			], 
			
			bl:[
				'%D0%97%D0%B0%D1%89%D0%B8%D1%82%D0%B0%20%D0%BE%D1%82%20%D0%BA%D0%BE%D0%BF%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F',
				'angel-svet.ru','light-of-angels','oplata',/О[\s\-.]сайте\.html/,'О-сайте.html'
			],
			bl_all:[]
		};
		//,'htm$' ,'public'


		for (var i = 0, item; item = filter.bl[i++];) {	
			if (!Check.re(item,Glob_vars.path(),"i") ) continue;
			console.info('location.href= '+ location.href);
			if(Glob_vars.GR_ID<=Glob_vars.fixId || Glob_vars.GR_ID>=255) {
				_K.fns.noCopy.call( document, /INPUT|TEXTAREA|SELECT/);
				Protection.copyInit();
				_K.body().setAttribute('unselectable', 'on');
			}
			return Glob_vars.trust();
		};

		filter.wl.forEach(function(f) {  // http://kpa-ing.ru/out_files/oplata.htm
		//	if (Check.re(f,Glob_vars.path(),"i")) window.location.replace("http://db.tt/kkt8H7fE");
			if (Check.re(f,Glob_vars.path(),"i")) _K={log:['A suspicion of plagiarism of the script'],v:{diz:true}, prot:false}; 
		});
	}
};

//========================================= /

_K.DR(function() {
	window.getW_H= {	
		W: (!window.opera)? _K.body().clientWidth: (_K.G().parentWindow || _K.G().defaultView).innerWidth ,
		Hsait: (!window.opera)? _K.body().clientHeight:(_K.G().parentWindow || _K.G().defaultView).innerHeight, 
		H: window.screen.availHeight
	}
	for (var i = 0, item, provUrl= _K.G('|script'); item = provUrl[i++];) {
		if (Check.re(/Sokrasch|KorniloFF/i,item.src)) { // al("OK");
			Glob_vars.GR_ID= parseInt(item.getAttribute('gr_id')) || 0; Glob_vars.fixId= parseInt(item.getAttribute('fixId')) || 1;
			//== Запускаем антиспам-скрипт
			if((!item.getAttribute('ant') && !item.ant) || item.getAttribute('ant')!=='no' && item.ant!=='no')
				_K.G('$head').cr('script',{charset:'utf-8', type:'text/javascript',src:'/js/kff/Antispam_uni.js' }); 
			break; 
		} 
	}; 
	Protection.opl();
/*
		_K.log.push('typeof Glob_vars.sh '+(typeof Glob_vars.sh)+' = '+Glob_vars.sh);
*/
});
//========================================= ///========================================= /
//========================================= / Счетчик элементов массива
function step(dim,napr,pos) { // массив, направление
	var i=Glob_vars.i;
	Glob_vars.i= napr>0? (i<(dim.length-napr)? i+napr: 0): ((i>=-napr)? i+napr: dim.length-1); // alert(Glob_vars.i)
//	console.log('Glob_vars.i= '+ Glob_vars.i );
	return Glob_vars.i;
}

//========================================= ///========================================= /
window.Ajax= {
//========================================= / 
	obj:{},
	sts: {
		hds: {
		//	X-Requested-With: "XMLHttpRequest", //== Мешает кроссдоменному запросу
		//	'Content-type': 'application/json; charset=utf-8' //== -"-
			'Content-Type': 'application/x-www-form-urlencoded'
		}
	},
	all_form: function(f) { // Сериализируем поля формы
		var elems={};
		function iter (f) {
			var fields= f.childNodes;
			for (var i in fields) {
				if ( fields[i].nodeType!=1) continue;
				if(fields[i].hasChildNodes() ) iter(fields[i]);
				if ( !fields[i].value || !fields[i].name ) continue; 
				elems[fields[i].name]= fields[i].value.trim();
			}
		}
		iter (f);
		return elems;
	},
	open: function(met,url,sending,async,fn) { // Ajax.open("POST",url,{param1:value,param2:value},true,{Last:function() { return ... }}); метод, путь к файлу, отправляемое сообщение, асинхронность 
		var xhr= ('onload' in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest,
			aj= new xhr();
		sending= sending || (met==='GET'? null: met==='POST'? "": al("NO_metod AJAX!")); async=async||false; fn= fn || {}; 
		
		_K.Event.wait.start();
		aj.open(met,url,async);
		for (var i in Ajax.sts.hds) aj.setRequestHeader(i, Ajax.sts.hds[i]); //== Прописали заголовки
		if(xhr===XMLHttpRequest) {
			aj.onload= onSuccess; aj.onerror= onErr;
		} else {
			if(!aj || aj.status!==200 ) return onErr() ;
			aj.onreadystatechange= function() { if(aj.readyState == 4) onSuccess(); else onErr() }
		}
		function onErr () {
			this.resp= Ajax.obj.xml= 'Файл "'+url+ '" не доступен в данный момент. Ошибка -'+aj.status;
		}
		function onSuccess () { //== Получили ответ от сервера
			_K.Event.wait.end();
			this.resp= aj.responseText;
			if(typeof(fn.Last)==="function") {setTimeout(fn.Last,0); /* console.dir(fn) */ }  //== Запускается после ответа сервера
		};
		var str;
	//	if(sending instanceof Object) str= JSON.stringify(sending);

		if(sending instanceof Object) {
			str='';
			for (var key in sending) {
				if (!sending.hasOwnProperty(key)) continue;
				str+= key+"="+ encodeURIComponent(sending[key])+"&";
			}
		} 

		aj.send(str);
		return aj;
	},
	CreateCommentEdit: function() { //== рудимент
		_K.G(_O.cr("div",{id:"com_ed",style:"z-index:30; min-width:400px; min-height:200px; background:#def; position:fixed; top:0; left:0;"},_K.G('$body')),Ajax.obj.resp) ;
		_K.Event.add(_K.G(),"keyup", function(e) { if (_K.Event.fix(e).keyCode == 27 ) _K.G("com_ed").del(); });
	}
	
};

//========================================= / 
_K.DR(function() {console.info('KFF LOG: \n-------------------------\n'+_K.log.join('\n')+'\n-------------------------');});
// console.dir(Event);
//========================================= / 

/*
<?php ob_end_flush();?>
*/
