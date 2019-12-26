/******************************************
Copyright KorniloFF-ScriptS ©
http://js-master.ru
*******************************************/
/*
<?php
header('content-type: application/x-javascript');
require_once 'js.php';
#$SITE_name= global $SITE_name; // выдает ошибку
ob_start("compress_js");
?>
3.5.4 - Исправлен конфликт с Яндекс-картами!
3.6.0 - Освобожден Object.prototype
*/

'use strict';
console.groupCollapsed();
if (!window._K && (!location.search.length || !/layout\=edit/.test(location.search))) {
	if (!window.getComputedStyle) window.getComputedStyle = function (elem) { return elem.currentStyle };
	window._K = {

		// get dE() { return document.documentElement },
		G: function () {
			//	_K.l("this!==_K= "+ (this!==_K));
			var obj= arguments[0];

			function addProto (o) {
				if(o && !o.clonePpts) {
					var proto= Object.getPrototypeOf(o) || o.__proto__;
					if (!proto || !Object.getPrototypeOf(proto) || proto.__proto__ === null) { // nE!
						// console.log("_K.allows= ", _K.allows);
						Object.setPrototypeOf(o, _K.allows);
					} else {
						 _K.clonePpts(proto, _K, { enum: 1 });
						// Object.assign(proto, _K.allows);
						// console.log("_K добавлен в прототип " + Object.getPrototypeOf(o));
					}
				}
				return o;
			}
			// function addProto (o) { return o};

			
			switch (arguments.length) {
				case 0:
					// return checkThis ? addProto(this) : document; //  _K.l("this= "+ this); // this.tagName
					 return addProto(this !== _K ? this : document); 
				case 1:
					var objOut, objs;
					if (this.isObject.call(obj)) objOut= obj;
					else if (typeof (obj) === "string") {
						if (obj.charAt(0) === "#") objOut= this.G().getElementById(obj.substr(1));
						else if (obj.charAt(0) === "$") objOut= (this.G().querySelector(obj.substr(1)));
						else if (obj.substr(0, 2) === 'A$') objs= this.G().querySelectorAll(obj.substr(2));
						else if (obj.charAt(0) === ".") objs= this.G().getElementsByClassName(obj.substr(1));
						//	else if(obj.charAt(0)==="|") return this.G().getElementsByTagName(obj.substr(1));
						else objOut= this.G().getElementById ? this.G().getElementById(obj) : this.G().layers ? this.G()[obj] : this.G().all[obj] ? this.G().all[obj] : false;
					} else return null;

					if(objOut) return addProto(objOut);
					else if(objs) {
						objs = [].map.call(objs, function (i) {
							return addProto(i);
						});
						return addProto(objs);
					}
					
					// _K.l("objOut= " + objOut);
					break;
				case 2:
					if (this.G(obj)) var o = this.G(obj);
					else break;
					if (arguments[1] instanceof Object) { // стили задавать в формате JS
						this.clonePpts(o.style, arguments[1], { enum: true, assert: true, plain: true }); // break;
					} else {
						if (typeof (arguments[1]) === "string" && arguments[1].substr(0, 2) === "++") o.innerHTML += arguments[1].substr(2);
						else o.innerHTML = arguments[1];
					}
					return o;
				case 3:
					if (!this.G(obj)) break;
					return this.G(obj, arguments[1]).clonePpts(arguments[2], { enum: true, assert: true, plain: true });
			}
		}, //== /G

		//=========================================/
		dPpt: function (Ppt, descriptor) { return Object.defineProperty(this, Ppt, descriptor) },
		dPpts: function (Ppts) { return Object.defineProperties(this, Ppts) },
		
		clonePpts: function (objExt, obj, sts) { //== Клонируем свойства // nE!
			// 
			if (this !== _K && arguments.length < 3) {
				objExt = this;
				obj = arguments[0];
				sts = arguments[1];
			}

			if (!_K.isObject.call(objExt) || !_K.isObject.call(obj)) {
				_K.l("_K.isObject.call(objExt)= " + objExt + " _ " + _K.isObject.call(objExt));
				_K.l("_K.isObject.call(obj)= " + _K.isObject.call(obj));
				throw new TypeError('clonePpts cannot convert some argument to object')
			};
			sts = sts || {};
			// sts.enum= sts.enum || 1;
			// console.log("Object.keys= " +  Object.keys(obj) + "\n" + "obj= " + obj + "\n");
			// var allows=[];
			(!!sts.enum ? Object.keys : Object.getOwnPropertyNames)(obj).forEach(function (name) {
				// console.log("ppt= " +  name);
				if(!objExt) console.warn("objExt in clonePpts = " + objExt);
				var d = !sts.plain && Object.getOwnPropertyDescriptor(obj, name) || 1,
					dExt= (name in objExt) && Object.getOwnPropertyDescriptor(objExt, name);
				
				if (dExt && (!sts.assert || !dExt.writable ) || _K.setts.noClonePpts.test(name) ) {
					return;
				} // else allows.push(name);
				
				// console.log("ppt in allows= " + name);
				
				if (!!sts.D) Object.keys(sts.D).forEach(function (i) { d[i] = arguments[2][i] });
				if (sts.enum && sts.enum === 'set') d && (d.enumerable = false);
				
				if (!!sts.plain) objExt[name] = obj[name];
				else Object.defineProperty(objExt, name, d);

			//	objExt[name] = obj[name];
				
				!!sts.callback && sts.callback();

			});
			// console.log("allowsInObj= " +  allows.valueOf() + "\n");
			return objExt;
		}, //== /clonePpts

		toggle: function (bool, y, n) {
			return bool ? y : n;
		},

		dToggle: function (e) {
			if (this.hidden !== undefined) {
				if (this.style.display === 'none') {
					this.hidden = 1;
					this.style.display = '';
				}
				this.hidden = !!this.hidden ? false : true;
			} else this.d.toggle(this);
			if (!!e) {
				var t = this.e.fix(e).target;
				t.title = this.d.isHidden(this) ? 'Показать' : 'Скрыть';
			}

		},
		d: {
			hide: function (el) {
				if (this.hidden !== undefined) return this.hidden = true;
				el.displayOld = el.displayOld || el.style.display;
				el.style.display = "none";
			},
			displayCache: {},
			isHidden: function (el) {
				if (this.hidden !== undefined) return this.hidden;
				var width = el.offsetWidth,
					height = el.offsetHeight,
					tr = el.nodeName.toLowerCase() === "tr";
				return width === 0 && height === 0 && !tr ?
					true : width > 0 && height > 0 && !tr ? false : getComputedStyle(el).display
			},
			toggle: function (el) {
				this.isHidden(el) ? this.show(el) : this.hide(el)
			},
			show: function (el) {
				if (this.hidden !== undefined) return this.hidden = false;
				if (getComputedStyle(el).display !== 'none') return;
				el.style.display = el.displayOld || "";
				if (getComputedStyle(el).display === "none") {
					var nodeName = el.nodeName,
						display;
					if (this.displayCache[nodeName]) {
						display = this.displayCache[nodeName]
					} else {
						var testElem = _K.body().cr(nodeName);
						display = getComputedStyle(testElem).display;
						if (display === "none") display = "block";
						_K.body().removeChild(testElem)
						this.displayCache[nodeName] = display
					}
					el.displayOld = display;
					el.style.display = display;
				}
			}
		}, //== /d

		Parent: function (ppts) {
			//== Рекурсивный поиск родительского узла с заданными свойствами. Возвращается родительский узел или false
			// ppts - объект со свойствами
			// ppts.n - глубина рекурсии (def - 3)
			if (!_K.G(ppts).isObject) {
			//	console.dir(ppts);
				throw new TypeError('Parent argument is not object');
			}
			
			ppts = (ppts || {}).clonePpts({ n: 3 });
			var np = this.parentNode || null;

			if (!np || !ppts.n--) return false;
			for (var p in ppts) {
				if (ppts[p] === 'n') continue;
				//			_K.l("p= "+ p + " np.tagName= "+ np.tagName);
				if (!ppts[p] && np[p]) return np; //== contain {ppt:0}
				if (np[p] && ppts[p] && (np[p] == ppts[p] || new RegExp(ppts[p], 'i').test(np[p]))) return np;
			};
			return np.Parent(ppts);
		},


		//== Использование _O =====================
		Append: function app(el, after) { //== fix to native method
			//	_K.l('APPEND ',this);
			// console.log("typeof el= " + typeof el);
			console.assert(!!this, el + ' не имеет ' + this + '\nОшибка в Append');
			switch (after) {
				case 1:
					; // after this
				case "after":
					!!this.nextSibling ? this.parentNode.insertBefore(el, this.nextSibling) : this.parentNode.appendChild(el);
					break;
				case 2: // before this
				case "before":
					this.parentNode.insertBefore(el, this);
					break;
				case "fCh":
					this.firstChild ? this.insertBefore(el, this.firstChild) : app.call(this, el);
					break;
				case null:
					;
				case undefined:
					this.appendChild(el);
					break;
			}
			return _K.G(el);
		},

		cr: function (elem, objAttrs, after) { //== Тег, объект с аттрибутами, родительский элемент разметки
			if (!this) return _K.l('_K.cr not have this! === ' + this);
			var el = (elem === 'textNode') ? _K.G().createTextNode() : _K.G().createElement(elem);
			if (elem === 'script') _K.G(objAttrs).clonePpts({ type: 'text/javascript' }, { plain: 1 });
			
			objAttrs && _K.G(el).setAttrs(objAttrs); // _K.l(el.setAttrs);
		//	return (!_K.isObject.call(this) || this === _K) ? _K.G(el) : _K.G(this).Append.call(this, el, after);
			return (!_K.isObject.call(this) || this === _K) ? el : this.Append.call(this, el, after);
		},

		Clone: function (o, after, deep) { //== Конфликт с MooTools - obj.clone
			return this.Append(o.cloneNode(deep || true), after);
		},

		setAttrs: function (objAttrs) { //== Добавляем аттрибуты
			if (!objAttrs) return;
			/*_K.l("this= " + this + "_" + this.src);
			console.dir(this);*/
			Object.keys(objAttrs).forEach(function (i) {
				if(!this.setAttribute) _K.l(this);
				this.setAttribute(i, objAttrs[i]);
				if (i === 'async') this.async = objAttrs[i];
			}, this);
		},
		del: function () { return this.parentNode && this.parentNode.lastChild ? this.parentNode.removeChild(this) : null; },
		//== /Использование _O =====================

		
		inner: function (node) { return !!node ? _K.G(node).innerHTML : this.innerHTML },
		
		
		getOffset: function () { //== СМЕЩЕНИЕ элемента на странице
			var elem = !!this.tagName ? this : arguments[0];
			if (elem.getBoundingClientRect) { //== "правильный" вариант
				return getOffsetRect(elem);
			} else { //== пусть работает хоть как-то
				return getOffsetSum(elem);
			}

			function getOffsetRect(elem) {
				var box = elem.getBoundingClientRect();
				var top = box.top + _K.scroll.top;
				var left = box.left + _K.scroll.left;

				return { top: Math.round(top), left: Math.round(left) }
			}

			function getOffsetSum(elem) {
				var top = 0,
					left = 0;
				while (elem) {
					top += parseInt(elem.offsetTop);
					left += parseInt(elem.offsetLeft);
					elem = elem.offsetParent;
				}
				return { top: top, left: left }
			}
		},


		/* ======================
		========== СОБЫТИЯ ==============
		====================== */
		get e() {
			 this.Event.elem = this !== _K? this: null;
			
			// console.log('this !== _K =>' + (this !== _K) + ' __ ', this);
			// console.log( "this.Event.elem= ", this.Event.elem );
			// !this.Event.elem && console.dir(this);
			return this.Event;
		},
//		event: function () { return this.e; },
		Event: {
			fix: function (e) {
				e = e || window.event;
				if (e.isFixed) return e;
				e.isFixed = true;
				//== фиксим отключение события по умолчанию	_K.Event.fix(e).preventDefault()
				e.preventDefault = e.preventDefault || function () { this.returnValue = false };
				//== фиксим отключение всплытия событий	_K.Event.fix(e).stopPropagaton()
				e.stopPropagation = e.stopPropagation || function () { this.cancelBubble = true };
				//	e.target = e.target || e.srcElement;
				if (!e.target) e.target = e.srcElement;
				if (!e.relatedTarget && e.fromElement) e.relatedTarget = e.fromElement == e.target ? e.toElement : e.fromElement;

				//== аналог pageX/pageY для тачпадов
				if (e.changedTouches) {
					e.chTX = e.changedTouches[0].pageX;
					e.chTY = e.changedTouches[0].pageY;
				}
				//		_K.l("e.changedTouches= "+ e.changedTouches);

				//== добавить pageX/pageY & which для IE
				if (e.pageX == null && !e.which && e.button) {
					e.pageX = e.clientX + (_K.body().scrollLeft || 0) - (_K.body().clientLeft || 0);
					e.pageY = e.clientY + (_K.body().scrollTop || 0) - (_K.body().clientTop || 0);
					e.which = e.button & 1 ? 1 : (e.button & 2 ? 3 : (e.button & 4 ? 2 : 0))
				};
				return e
			},
			check: function (elem, type, handler) {
				var a = [], o = {},
					el = this.elem || elem;
				this.elem = null;
				//  _K.l("this.elem = ", el );
				if (!!handler) a.push({ elem: el, type: type, handler: handler });
				else if (!elem.__proto__.__proto__) {
					Object.keys(elem).forEach(function (i) {
						a.push({ elem: el, type: i, handler: elem[i] });
					});
				} else { a.push({ elem: el, type: arguments[0], handler: arguments[1] }); }
				return a;
			},
			add: function (elem, type, handler, useCapture) {
				this.check(elem, type, handler).forEach(function (a) { addEvent(a.elem, a.type, a.handler); });

				function addEvent(elem, type, handler) {
					handler = handler.toFun || handler;
					elem.addEventListener ? elem.addEventListener(type, handler) : elem.attachEvent ? elem.attachEvent("on" + type, handler) : elem["on" + type] = handler;
				}
				
			},
			del: function (elem, type, handler) {
				this.check(elem, type, handler).forEach(function (a) { delEvent(a.elem, a.type, a.handler); });

				function delEvent(elem, type, handler) {
					handler = handler.toFun || handler;
					elem.removeEventListener ? elem.removeEventListener(type, handler, false) : elem.detachEvent ? elem.detachEvent("on" + type, handler) : elem["on" + type] = null;
				}
				
			},
			stop: function (e) {
				(this.elem || _K).Event.fix(e).stopPropagation();
			},
			wait: {}
		}, //== /Event

		
		isObject: function () { return this != null && (typeof this === 'object' || typeof this === 'function'); },

		

	}; //== /_K
	//===========================================================================================/

	// Неперечисляемые свойства объекта _K
	
	Object.defineProperties(_K, {
		__proto__: { value: Object.prototype },
		vers: {
			value: '3.6.0',
		},
		setts: { 
			value: { noClonePpts: /^(vers|__proto__|setts|allows|dE|i|l|dir|body|DR|event|v|log|prot|date|fixDate|argTransform|parseData|scroll|fns|lO|onload)$|^on/ } 
		},
		allows: {
			get: function () {
				var out = {};
				Object.keys(_K).forEach(function (ppt) {
					return _K.clonePpts(out, _K, {enum:1});
					// return Object.assign(out, _K);
				});
				return out;
			}
		},
		dE: {
			get: function() { return document.documentElement }
		},
		v: {
			value: {
				local: /:90|!\.ru/i.test(location.host),
				Inh: {
					// __proto__: Element.prototype.__proto__
				}
			}
		},
/*		prot: {
			writable:1, configurable:1
		},*/
		log: { value: [] },
		lO: { value: {} },
		l: {
			value: console.log.bind(console)
		},
		i: {
			value: console.info.bind(console)
		},
		body: {
			value: function () { return _K.G((_K.G().compatMode == 'CSS1Compat') ? _K.dE : _K.G().body) }
		},
		date: {
			value: function () {
				var date = new Date();
				return { ss: date.getSeconds(), min: date.getMinutes(), h: date.getHours(), d: date.getDate(), dn: date.getDay(), mon: date.getMonth() + 1, year: date.getFullYear(), time: date.getTime() }
			}
		},
		fixDate: {
			value: function () {
				var date = _K.date();
				Object.keys(date).forEach(function (i) { date[i] = (date[i] < 10) ? "0" + date[i] : date[i]; });
				return date;
			}
		},
		DR: {
			value: function (handler, delay) {
				var called = false;

				function ready() {
					if (called) return;
					called = true;
					delay ? setTimeout(handler, delay) : handler();
				}
				('onpageshow' in window) ? _K.Event.add(window, 'pageshow', ready) : _K.Event.add(document, "DOMContentLoaded", ready);

				//	window.addEventListener('load', ready);
			}
		},
		argTransform: {
			value: function (arg, handler) {
				handler = handler.toFun || handler;
				[].forEach.call(arg, function (a) {
					if (a instanceof Object) Object.keys(a).forEach(function (i) {
						handler(i, a[i]);
					});
					else handler(i);
				});
			}
		},
		parseData: {
			value: function (data) { //== Возвращает массив
				data = /(\D+)?(\d+)(\D+)?/.exec(data);
				data[2] = parseInt(data[2]);
				return data;
			}
		},
		scroll: {
			get: function () { //== получаем ПРОКРУТКУ страницы
				return {
					left: window.pageXOffset || _K.dE.scrollLeft,
					top: window.pageYOffset || _K.dE.scrollTop
				}
			}
		},

		fns: {
			value: {
				/*
					validate: function (cont) {
						var r= cont? cont.replace(/\</gm,'&lt;').replace(/\>/gm,'&gt;').tabTrim(): false;
						_K.l("r= "+ cont + '__' + r);
						return r;
					},
				*/

				fb: { //== _K.fns.fb.b - кнопка для feedback
					t: false,
					toMail: function () {
						if (!!_K.G('$meta[content*=KFF]')) {
							location.href = '/?mailform';
						} else if (!_K.fns.fb.t) {
							_K.G(_K.body(), '++' + Ajax.open('POST', '/PHP/FeedBack.php').resp);
							_K.fns.fb.val();
							_K.fns.fb.t = true
						};
					},
					b: '<p><input class="btn" type="button" value="Обратная связь" title="Обратная связь" onclick="_K.fns.fb.toMail() "></p>',
					val: function () { _K.G('#inputSubject').value = _K.G('#inputSubject') && _K.G('$h1') && _K.G('$h1').textContent } //== Тему из заголовка
				},

				captureEvents: function (e, tags) {
					tags = tags || /INPUT|TEXTAREA|SELECT/;
					e = _K.Event.fix(e)
					if (!tags.toUpperCase().test(e.target.tagName)) e.preventDefault();
					//	_K.l("tags.toUpperCase().test(e.target.tagName)= " + tags);
				},

				noCopy: function (tags) {
					this.onselectstart = this.onselect = this.ondblclick = this.onmousedown = this.oncontextmenu = function (e) { return _K.fns.captureEvents(e, tags) };
				},

				is_array: function () { //== Проверка массива _K.fns.is_array.call([1,2,3])
					return _K.isObject.call(this) && this instanceof Array;
				},

				select: function () { //== Выделение текста
					this.title = this.title || 'Выделить';
					var rng, sel;
					if (document.createRange) {
						rng = document.createRange(); //создаем объект область
						rng.selectNodeContents(this); //== Содержимое текущего узла (selectNode - сам узел)
						sel = window.getSelection(); //Получаем объект текущее выделение
						var strSel = sel.toString();
						if (!strSel.length) { //Если ничего не выделено
							sel.removeAllRanges(); //Очистим все выделения (на всякий случай) 
							sel.addRange(rng); //Выделим текущий узел
						}
					} else {
						document.body.createTextRange().moveToElementText(this).select();
					}
				},

				rnd: function (rmin, rmax) { return Math.floor(Math.random() * (rmax - rmin + 1)) + rmin; },
				rgbToHex: function (rgb) { //== Преобразуем цвета
					if (rgb.charAt(0) === '#') return rgb;
					var hex = '#';
					rgb.match(/\d+/g).forEach(function (i) { hex += parseInt(i).toString(16) });
					return hex;
				}

			},
			/*e: {
				enumerable:1,
				get: function () {
					 this.Event.elem = this !== _K? this: null;
					// this.Event.elem = !this.vars ? this : null;
					_K.l("this.Event.elem= " + this.Event.elem);
					!this.Event.elem && console.dir(this);
					return this.Event;
				}
			},*/
		} //== /fns
	})

	//== /_K enumerable: false
	//===========================================================================================/

	

//	Object.clonePpts || _K.clonePpts(Object.prototype, _K, { enum: 'set' });

//	Object.__proto__= _K;

	/*
	if (1) {
		Object.__proto__ = _K.clonePpts(Object.create(Object.__proto__), _K)

		_K.v.Inh= _K.clonePpts(Object.create(null), _K);
		_K.l("_K.v.Inh= "+ _K.v.Inh);
		Object.prototype.__proto__ = _K.v.Inh;

		Element.prototype.__proto__= Object.create(_K);
		
	}
	*/
// Object.__proto__ = _K.clonePpts(Object.create(Object.__proto__), _K)
	//===========================================================================================/

	//========================================= / 
	window.Cookie = window.Cook = { //== Конфликт с MooTools - window.Cookie
		set: function (dim, timeout, path) { // в днях
			timeout = (timeout || 1) * 3.6e6 * 24; //мс
			var maxAge = (timeout || 1) * 3600 * 24; //с
			path = path || "/";
			var expires = (new Date((new Date).getTime() + timeout)).toUTCString() || null;
			if (dim instanceof Array)
				for (var i = 0, L = dim.length; i < L; i++) _K.G().cookie = dim[i][0] + '=' + dim[i][1] + ';expires=' + expires + ';max-age=' + maxAge + '; path=' + path;
			else Object.keys(dim).forEach(function (i) { _K.G().cookie = i + '=' + dim[i] + ';expires=' + expires + ';max-age=' + maxAge + '; path=' + path; })
		},
		get: function (cookie_name) {
			if (!cookie_name) return;
			var m = _K.G().cookie.match('(^|;)?' + escape(cookie_name.toString()) + '=([^;]*?)(;|$)');
			return m && m.length ? unescape(m[2]) : null;
		},
		del: function (c_names) { // перечислить удаляемые кукисы через запятую
			var cookie_date = new Date(), // Текущая дата и время
				path;
			cookie_date.setTime(cookie_date.getTime() - 1);
			for (var i = 0, L = arguments.length; i < L; i++) {
				if (!Cookie.get(arguments[i])) { path = arguments[i]; continue; }
				_K.G().cookie = arguments[i] + "=; expires=" + cookie_date.toGMTString() + ";max-age=0; path=" + path || "/";
				//	 _K.i('cookie_date - '+arguments[i]+'= '+ cookie_date+'= '+cookie_date.toGMTString());
			}
		}
	}

	//=============================================================================/
	//========================================= / 

	window.Glob_vars = {
		GR_ID: 0,
		fixId: 1,
		path: function () { return window.location.href; },
		host: function () { return window.location.host; }, //хост и порт
		Get: function () { return Ajax.Get; },
		trust: function () { Cookie.set({ trust: true }, 30); return false },
		date: function () { return _K.fixDate() },
		errChk: "Нет ошибок! =)",
		sh: true
	}

	//=========================================/
	window.Check = {
		re: function (reg, str, fls) { return typeof reg.test === 'function' ? reg.test(str) : new RegExp(reg, fls || '').test(str) },
		match: function (reg, str) { return str.match(reg) },
		name: function (name) { return !!name && Check.re(/^[\w\W\d]{3,15}$/i, name) },
		mail: function (mail) { return Check.re(/^.+?@.+?\..{2,}$/i, mail) },
		mess: function (name) { return !!name && Check.re(/^[\w\W]{3,}$/i, name) }
	}

	//=========================================/
	window.Brows = {
		appName: navigator.appName,
		appCodeName: navigator.appCodeName,
		ua: navigator.userAgent,
		opts: [/MSIE/, 'Firefox', 'Opera', 'Chrome', 'Safari', 'Konqueror', 'Iceweasel', 'SeaMonkey'], // |like Gecko;
		get name() {
			for (var i = 0, item; item = this.opts[i++];) {
				if (Check.re(item, this.ua, 'i')) return i === 0 ? 'MSIE' : item;
			};
			return 'ХЗ'
		}
	};

	
	//=========================================/
	//================ PrototypeS ===================/

	//== Заполнение строки
	String.prototype.pad = String.prototype.pad || function (l, s, t) {
		if ((l -= this.length) > 0) {
			s = s || " "; //по умолчанию строка заполнитель - пробел
			t = t || 1; //по умолчанию тип заполнения справа
			s = s.repeat(Math.ceil(l / s.length));
			var i = t == 0 ? l : (t == 1 ? 0 : Math.floor(l / 2));
			s = s.substr(0, i) + this + s.substr(0, l - i);
			return s;
		} else return this;
	}

	//== повторить заданную строку n раз
	String.prototype.repeat = String.prototype.repeat || function (n) { return new Array(n + 1).join(this); }
	//== Вывод текста "печатной машинкой"
	String.prototype.delayingWrite = function (obj, delay, fnLast, timeLast) {
		var o = _K.G(obj);
		if (!o) return 0;
		if (this.length) {
			o.wr = 1;
			o.textContent += this.charAt(0);
			var s = this.substr(1);
			setTimeout(function () { s.delayingWrite(obj, delay, fnLast, timeLast); }, delay);
		} else {
			o.wr = 0;
			if (_K.isObject.call(fnLast)) {
				setTimeout(fnLast, timeLast || delay * 10);
			}
		}
	}

	String.prototype.trim = String.prototype.trim || function () { return this.replace(/^\s+|\s+$/gm, '') };
	String.prototype.ltrim = String.prototype.ltrim || function () { return this.replace(/^\s+/gm, '') };
	String.prototype.tabTrim = function () { return this.replace(/\t/gm, ' ') };
	String.prototype.rtrim = String.prototype.rtrim || function () { return this.replace(/\s+$/gm, '') };
	String.prototype.fulltrim = String.prototype.fulltrim || function () { return this.replace(/((^|\n)\s+|\s+($|\n))/gm, '').replace(/\s+/gm, ' '); };
	String.prototype.translit = String.prototype.translit || function (course) {
		var Chars = {
			'а': 'a',
			'б': 'b',
			'в': 'v',
			'г': 'g',
			'д': 'd',
			'е': 'e',
			'ё': 'yo',
			'ж': 'zh',
			'з': 'z',
			'и': 'i',
			'й': 'y',
			'к': 'k',
			'л': 'l',
			'м': 'm',
			'н': 'n',
			'о': 'o',
			'п': 'p',
			'р': 'r',
			'с': 's',
			'т': 't',
			'у': 'u',
			'ф': 'f',
			'х': 'h',
			'ц': 'c',
			'ч': 'ch',
			'ш': 'sh',
			'щ': 'shch',
			'ъ': '',
			'ы': 'y',
			'ь': '',
			'э': 'e',
			'ю': 'yu',
			'я': 'ya',
			'А': 'A',
			'Б': 'B',
			'В': 'V',
			'Г': 'G',
			'Д': 'D',
			'Е': 'E',
			'Ё': 'YO',
			'Ж': 'ZH',
			'З': 'Z',
			'И': 'I',
			'Й': 'Y',
			'К': 'K',
			'Л': 'L',
			'М': 'M',
			'Н': 'N',
			'О': 'O',
			'П': 'P',
			'Р': 'R',
			'С': 'S',
			'Т': 'T',
			'У': 'U',
			'Ф': 'F',
			'Х': 'H',
			'Ц': 'C',
			'Ч': 'CH',
			'Ш': 'SH',
			'Щ': 'SHCH',
			'Ъ': '',
			'Ы': 'Y',
			'Ь': '',
			'Э': 'E',
			'Ю': 'YU',
			'Я': 'YA'
		},
			t = this;
		for (var i in Chars) { t = t.replace(new RegExp(i, 'g'), Chars[i]); }
		return t;
	};

	String.prototype.toFun || Object.defineProperty(String.prototype, 'toFun', { get: function () { return new Function(this) } });

	Math.sign = Math.sign || function (x) {
		x = +x;
		return (x === 0 || isNaN(x)) ? x : x > 0 ? 1 : -1;
	}

	Array.prototype.inArray || Object.defineProperty(Array.prototype, 'inArray', {
		value: function (ext) { return this.some(function (i) { return i === ext }) }
	});
	Array.prototype.in_array || Object.defineProperty(Array.prototype, 'in_array', { value: Array.prototype.inArray });

	Array.prototype.isArray || Object.defineProperty(Array.prototype, 'isArray', {
		get: function () {
			return Object.prototype.toString.call(this) === '[object Array]';
		}
	});
	Array.prototype.rnd || Object.defineProperty(Array.prototype, 'rnd', {
		get: function () { return this[_K.fns.rnd(0, this.length - 1)] }
	});

/*	Array.prototype.rnd || Object.defineProperty(Array.prototype, 'rnd', {
		get: function () { return this[(function (rmin, rmax) { return Math.floor(Math.random() * (rmax - rmin + 1)) + rmin; })(0, this.length - 1)] }
	});
	[].rnd.call(document.querySelectorAll('img'));*/

	Array.prototype.Step || Object.defineProperty(Array.prototype, 'Step', {
		value: function (napr, ind) {
			var i = ind || _K.v.ind || 0;
			_K.v.ind = napr > 0 ? (i < (this.length - napr) ? i + napr : 0) : ((i >= -napr) ? i + napr : this.length - 1);
			return this[_K.v.ind];
		}
	});


	Array.prototype.Copy || _K.dPpt.call(Array.prototype, 'Copy', {
		get: function () {
			var i, clon = this.slice();
			for (i = 0; i < clon.length; i++) {
				if (Array.isArray(clon[i])) clon[i] = clon[i].Copy;
			}
			return clon;
		}

	});
	Array.prototype.Max || _K.dPpt.call(Array.prototype, 'Max', {
		get: function () {
			return Math.max.apply(null, this);
		}
	});
	//=========================================/
	Function.prototype.bind = Function.prototype.bind || function (oThis) {
		if (typeof this !== 'function') {
			// ближайший аналог внутренней функции
			// IsCallable в ECMAScript 5
			throw new TypeError('Function.prototype.bind - what is trying to be bound is not callable');
		}
		var aArgs = [].slice.call(arguments, 1),
			fToBind = this,
			fNOP = function () { },
			fBound = function () {
				return fToBind.apply(this instanceof fNOP && oThis ? this : oThis, aArgs.concat([].slice.call(arguments)));
			};
		fNOP.prototype = this.prototype;
		fBound.prototype = new fNOP();
		return fBound;
	};

	//=========================================/
	//=========================================/ ================================================================================
	//=========================================  СОВМЕСТИМОСТЬ  ========================================//
	var DOMready = DOMready || function (handler) { return _K.DR(handler) };
	//=========================================/
	Object.assign || Object.defineProperty(Object, 'assign', {
		enumerable: false,
		configurable: true,
		writable: true,
		value: function (target, firstSource) {
			if (!_K.G(target).isObject()) { throw new TypeError('Cannot convert first argument to object'); }
			var to = Object(target);
			for (var i = 1, L = arguments.length; i < L; i++) {
				var nextSource = arguments[i];
				_K.clonePpts(to, Object(nextSource), { enum: 1, D: {enumerable:1} });
			}
			return to;
		}
	});

	typeof _K.dE.hidden === undefined && (
		Object.assign(Object.prototype, {
			get hidden() { return this.style && this.style.display === 'none' },
			set hidden(a) {
				this.style && (this.style.display = !!a ? 'none' : '');
				_K.l(this);
			}
		})
	);
	//== 

	var getOffset = getOffset || function getOffset(elem) { return _K.getOffset(elem) };
	var addEvent = addEvent || function (elem, type, handler) { return _K.Event.add(elem, type, handler) };
	var cE = cE || function (elem, objAttrs, parent, after) { return _K.G(parent).cr(elem, objAttrs, after) };
	var setAttrs = setAttrs || function (elem, objAttrs) { return _K.G(elem).setAttrs(objAttrs) };

	window.G = window.G || {
		randnum: _K.fns.rnd,
		Properties: function (obj) {
			var result = "The properties for the " + obj + "\n ";
			for (var i in obj) {
				result += i + " = " + obj[i] + "\n";
			}
			return result;
		}
	};
	//========================================= 
	var _O = _O || {
		//==============
		Append: function app(el, parrent, after) { return _K.G(parrent).Append(el, after); },
		cr: function (elem, objAttrs, parrent, after) { return _K.cr.call(parrent || null, elem, objAttrs, after); }, //== NoEdit!
		clone: function (o, parrent, after, deep) { return _K.G(parrent).Clone(o, after, deep); },
		extend: function (obj, proto) { return obj.clonePpts(proto, { enum: 'set' }) },
		del: function () { return _K.G(this).del() }
	};

	Object.setPrototypeOf = Object.setPrototypeOf || function (obj, proto) {
		!/MSIE [6-9]/.test(navigator.appVersion) ? (obj.__proto__ = proto) : _K.clonePpts(obj, proto, { enum: 1 });
		return obj;
	};
	Object.getPrototypeOf = Object.getPrototypeOf || function (obj) { return obj.__proto__ };

	//========================================  / СОВМЕСТИМОСТЬ  ============================================================================================/
	//=========================================/ /
	Object.assign(_K.lO, {
		'Lib version': _K.vers,
		host: location.host,
		'Brows.name': Brows.name,
		'Brows.ua': Brows.ua,
		'Brows.appName': Brows.appName
	});
	Object.defineProperties(_K.Event.wait, {
		start: {
			value: function () {
				if (!!_K.Event.wait.init) return;
				_K.Event.wait.init = _K.G(_K.body()).cr('img', { style: 'max-width:300px;height:auto;position:fixed; z-index:20000; border:none;', src: "/images/loadbar.gif", alt: "Загрузка..." });
				_K.G(_K.Event.wait.init, {
					top: _K.body().clientHeight / 2 - parseInt(getComputedStyle(_K.Event.wait.init).height) / 2 + 'px',
					left: _K.body().clientWidth / 2 - parseInt(getComputedStyle(_K.Event.wait.init).width) / 2 + 'px'
				})
			}
		},
		end: {
			value: function () {
				if (!!_K.Event.wait.init) { _K.Event.wait.init.del() };
				_K.Event.wait.init = null;
			}
		}
	});
	//=========================================/

	/* ========================================= ALERT ========================================= */
	if (!Check.re(/msie\s[5-9]\.0/i, Brows.ua)) {
		//========================================= /
		window.alert = window.al = function (txt, sts) { // sts={width:500}
			sts = _K.G(sts || {}).clonePpts({ width: 320, col: '#159', bgCol: '#eee linear-gradient(#eaeaea, #fafafa 30%, #f1f1f1 90%) repeat scroll 0 0;' });
			if (Check.re(/\n/, txt) && typeof txt === "string") txt = txt.replace(/\n/g, "<br />");
			if (!_K.G('#alert_OK')) {
				Glob_vars.alFon = _K.G('$body').cr('div', { style: 'width:100%;height:100%;position:fixed;top:0;left:0;background:#999;z-index:49000;opacity:.8' });
				Glob_vars.alObj = _K.G('$body').cr('div', { id: "alert_OK", style: "display:block; width:" + sts.width + "px; text-align:center; font:16px/1.5 monospace,Georgia,serif; background:" + sts.bgCol + "; padding:10px; border: ridge 2px " + sts.col + "; color:" + sts.col + "; position:fixed; top:50%; left:50%; margin-left:-" + sts.width / 2 + "px; border-radius:10px; overflow:auto; cursor:default; z-index:50000; opacity:1;" });
				_K.G(Glob_vars.alObj, "<div>Сайт <a href='//js-master.ru' style='color:" + sts.col + "; font:bold 10px italic,monospace; text-decoration:none;'>" + Glob_vars.host() + "</a> сообщает:<span style='display:inline; color:#fff;background:red; padding:3px; float:right; cursor:pointer; font:normal 16px Verdana;' onmouseover='this.style.fontWeight=\"bold\"' onmouseout='this.style.fontWeight=\"normal\"'>X</span></div><hr style='clear:both;' />" + txt);
				if (Glob_vars.alObj.offsetHeight > _K.body().clientHeight * .8) Glob_vars.alObj.style.height = _K.body().clientHeight * .8 + 'px';
				Glob_vars.alObj.style.marginTop = -Glob_vars.alObj.offsetHeight / 2 + "px";

				Glob_vars.al_TO = setTimeout(cl_al, 300000);
				Glob_vars.alObj.e.add("click", cl_al);
				_K.v.cl_al = function (e) { if (_K.Event.fix(e).keyCode == 27) cl_al(); }
				_K.Event.add(_K.dE, "keyup", _K.v.cl_al);
				return true;
			} else return false;

			function cl_al() {
				if (Glob_vars.al_TO) clearTimeout(Glob_vars.al_TO);
				Glob_vars.alFon.del();
				Glob_vars.alObj.del();
				if (Glob_vars.al_TO) clearTimeout(Glob_vars.al_TO);
				_K.e.del(document, "keyup", _K.v.cl_al);
			}
		}
	}

	//========================================= ///========================================= /
	window.getW_H = {
		get W() { return !window.opera ? _K.body().clientWidth : (_K.G().parentWindow || _K.G().defaultView).innerWidth },
		get Hsait() { return (!window.opera) ? _K.body().clientHeight : (_K.G().parentWindow || _K.G().defaultView).innerHeight },
		get H() { return window.screen.availHeight }
	};

	//================================================================================== //
	window.Ajax = {
		//========================================= //
		obj: {},
		sts: {
			hds: {
				//	'X-Requested-With' : "XMLHttpRequest", //== Мешает кроссдоменному запросу
				//	'Content-type': 'application/json; charset=utf-8' //== -"-
				'Content-Type': 'application/x-www-form-urlencoded'
			}
		},
		get Get() {
			if (window.location.search.length === 0) return false;
			var out = {},
				key = location.search.replace(/\?/, "").split(/\&/),
				dimkey;
			Object.keys(key).forEach(function (i) {
				dimkey = key[i].toString().split(/\=/);
				out[dimkey[0]] = dimkey[1] && decodeURI(dimkey[1]) || null;
			});
			return out;
		},
		all_form: function (f, stringify) { // Сериализируем поля формы
			var elems = {};

			function iter(f) {
				var fields = f.childNodes;
				for (var i in fields) {
					if (fields[i].nodeType != 1) continue;
					if (fields[i].hasChildNodes()) iter(fields[i]);
					if (!fields[i].value || !fields[i].name) continue;
					elems[fields[i].name] = fields[i].value.trim();
				}
			}
			iter(f);
			/*
					_K.l("elems= "+ elems);
					_K.l("JSON.stringify(elems)= "+ JSON.stringify(elems));
			*/
			return stringify ? JSON.stringify(elems) : elems;
		},

		open: function (met, url, sending, async, fn) {
			// var aj= Ajax.open("POST",url,{param1:value,param2:value},true,{CB:function() { ... }});
			// Все действия после асинхронного запроса выполнять в callback-функциях объекта fn
			var xhr = ('onload' in new XMLHttpRequest()) ? XMLHttpRequest : XDomainRequest,
				aj = new xhr(),
				str = '';
			sending = sending || (/GET/i.test(met) ? null : /POST/i.test(met) ? "" : al("NO_metod AJAX!"));
			async = async || false;
			fn = fn || {};

			_K.Event.wait.start();
			if (sending instanceof Object) {
				Object.keys(sending).forEach(function (key) { str += key + "=" + encodeURIComponent(sending[key]) + "&"; });
				str = str.slice(0, -1);
				//	str= JSON.stringify(str);
			}

			aj.open(met, url, async);
			for (var i in Ajax.sts.hds) aj.setRequestHeader(i, Ajax.sts.hds[i]); //== Прописали заголовки


			function checkResp(str) {
				if (!aj) return onErr();
				if (xhr === XMLHttpRequest) {
					aj.onload = onSuccess;
					aj.onerror = onErr;
				} else {
					aj.onreadystatechange = function () {
						_K.l("this.readyState= " + this.readyState);
						if (this.readyState !== 4 || this.status !== 200) return;
						onSuccess();
					}
				}
				_K.l("str= " + decodeURIComponent(str));
				aj.send(str);
				return aj;
			}

			function onErr() {
				_K.Event.wait.end();
				aj.resp = Ajax.obj.xml = 'Файл "' + url + '" не доступен в данный момент. Ошибка - ' + aj.status;
				return aj;
			}

			function onSuccess() { //== Получили ответ от сервера
				_K.Event.wait.end();
				aj.resp = aj.responseText;
			//	_K.G(fn) && fn.isObject() && Object.keys(fn).forEach(function (f) {
				fn instanceof Object && Object.keys(fn).forEach(function (f) {
					(fn[f]).call(aj);
				});
				//	if(_K.isObject.call(fn.Last)) {setTimeout(fn.Last.bind(aj),0); }  //== Запускается после ответа сервера
				//	fn.CB && fn.CB.bind(aj) ;
				return aj;
			};


			//		_K.l('aj.err= ' + aj.err);
			//	return aj;
			return checkResp(str);
		},
		CreateCommentEdit: function () { //== в Comments.php
			var ef = _K.G('$body').cr("div", { id: "com_ed", style: "z-index:10000; min-width:400px; min-height:200px; background:#def; position:fixed; top:0; left:0;" });
			ef.innerHTML = this.resp;
			_K.G().e.add("keyup", function (e) { if (_K.Event.fix(e).keyCode === 27) ef.del(); });
		}

	};

	//========================================= / 
	// _K.DR(function() {
	var s = 'color:#007; background:#eef;';
	//	_K.i('KFF LOG: \n-------------------------\n%c'+_K.log.join('\n')+'\n------------------------------',s);
	//	_K.i('Глобальный объект - %O', _K);
	console.table && console.table(_K.lO);
	// });
	//========================================= / 
} else _K.i('\n!!!!!!!!!!!!!!!!!!!!\n! Попытка повторного запуска KorniloFF.js\n!!!!!!!!!!!!!!!!!!!!');
console.groupEnd();
/*
<?php ob_end_flush();?>
*/