'use strict';

// Main jQ context
var $f = function(selector) {
	return $('#ajax-content').find(selector);
},
// $Content = $('#ajax-content'),


// helpers
_H = {
	mailform: '//js-master.ru/content/1000.Contacts/feedback/',
	api: {
		yTranslate: 'trnsl.1.1.20160322T135831Z.724a4334bf15348b.9d46f4011f729a523031c9d5854f7955478e94e7'
	},

	open:	function() {
		this.addClass('opened');
	},

	close: function() {
		this.removeClass('opened');
	},


	fixSlash: function(path) {
		return path ? path.replace(/\\/g, '/') : '';
	},

	getPath: function(str) {
		var path = decodeURI(str);
		path = _H.qs.parse(path)['page'] || path.replace(/^\/(.+)$/, '$1');
		// path = _H.qs.parse(path)['page'] || path.replace(/^\/(.+)\/$/, '$1');
		return this.fixSlash(path).replace(location.protocol + '//' + location.host + '/', '');
	},

	nav : {
		// define current page in nav
		current: function($list, path) {
			$list = $.check($list);
			path = path || _H.getPath(location.pathname);
			// console.log('path = ', path);

			var current = $list.filter(function(ind,i) {
				var page = i.getAttribute('data-page') || i['data-page'];
				return path === page;
			})[0],
			page_ind = $list.index(current);

			$list.removeClass('active');
			$list[page_ind].classList.add('active');
			$.cookie.set({page_ind: page_ind});

			// console.log('current = ', current, page_ind, $list);
			return page_ind;
		},

		next: function($list) {
			return this.serv($list, +1);
		},

		prev: function($list) {
			return this.serv($list, -1);
		},

		// общая функция для next & prev
		serv: function($list, direct) {
			var $oldItem = $list.find('.nav_item.active'),
				$current = direct > 0 ? (
					$oldItem.next('.nav_item').length && $oldItem.next('.nav_item') || $list.find('.nav_item:first')
				) : (
					$oldItem.prev('.nav_item').length && $oldItem.prev('.nav_item') || $list.find('.nav_item:last')
				);

			$oldItem.removeClass('active');
			$current.addClass('active');
			$.cookie.set({page_ind: $list.index($current)});
			// console.log($list, $oldItem, $current);
			return $current;
		},
	},
	// nav

	findItem: null,

	// create .close_button
	closeButton: function(node, param) {
		param = Object.assign({
			canvSize: 50,
			size: 20,
			lineWidth: 3,
			bg: '#333',
			color: '#fff'
		}, (param || {}));

		var $node = $(node),
			c = $node.addClass('close_button').cr('canvas', {width: param.canvSize, height: param.canvSize})[0],
			padding = (param.canvSize - param.size)/2;
		// console.log(i, c);
		if (!c.getContext) return;
		var ctx = c.getContext("2d"),
			r = 1.0 * param.canvSize/2;

		// ctx.globalAlpha = '.2';
		ctx.beginPath();
		// ctx.fillRect(0, 0, param.size, param.size);
		ctx.arc(param.canvSize/2,param.canvSize/2,r,0,Math.PI*2,true);
		ctx.fillStyle = param.bg;
		ctx.fill();
		// ctx.closePath();

		ctx.beginPath();
		ctx.moveTo(padding, padding);
		ctx.lineTo(param.size + padding, param.size + padding);
		ctx.moveTo(padding, param.size + padding);
		ctx.lineTo(param.size+padding, padding);
		ctx.lineWidth = param.lineWidth;
		ctx.strokeStyle = param.color;
		ctx.stroke();
		// ctx.globalAlpha = '.0';

		$node.css({
			width: param.canvSize + 'px',
			height: param.canvSize + 'px',
			// 'transform-origin': 'center center',
		});

	},


	qs: {
		// parse query string || @str in object @out
		parse: function(str) {
			str = str || window.location.search;
			if (!str) return false;
			var out = {};

			str.replace(/.*\?/, "").split(/\&/).map(function(i) {
				var pG = i.split(/\=/);
				out[pG[0]] = pG[1] && decodeURI(pG[1]) || null;
				return pG;
			});

			return out;
		},
	}, // qs



	form: {
		validate: function (form, opts) {
			var err = [];

			$.each(form.elements, function(ind, i) {
				err[ind] = [];

				i.$label = $(form).find("label[for='" + i.id + "']");

				if(i.value !== undefined && i.type !== 'file') i.value = i.value.trim();
				// console.log(i.name);

				switch (i.name) {
					case 'email':
						if(!/.+@.+\..+/i.test(i.value)) err[ind].push('Неверно указан email!');
						break;

					case 'entry':
					case 'message':
						if(i.value.length < 3) err[ind].push('Нет сообщения!');
						break;
					/*
					default:
						break; */
				}

				if(i.required && i.value == 0) {
					err[ind].push('Заполните поле ' + (i.$label.text() || i.placeholder));
				}

				// console.log(err[ind]);
				if(err[ind].length) {
					i.$label.css({color: 'red'});
				} else {
					i.$label.css({color: 'green'});
				}
			});
			return err; // arr
		},

		errors: function(form, opts) {
			opts = Object.assign({
				breaks: '<br>',
			}, opts || {});
			var str = '';

			this.validate(form).forEach(function(e) {
				if(e.length) {
					str += (e.join(opts.breaks) + opts.breaks);
				}
			});
			return str;
		}
	},


	popup: function(o) {
		var def = {
				tag: 'div',
				html: 'Hello!',
				style: 'display: inline-block;'
		};


		// console.log(this);

		function close (e) {
			e = $().e.fix(e);
			var bool = e.defKeyCode('esc') || e.type === 'click';
			/* console.log(
				"bool = ", bool,
				"\nthis = ", this,
				"\ne = ", e
			); */
			if(!bool) return;

			e.stopPropagation();
			e.preventDefault();
			(this === e.target || e.target.closest('.close_button')) && $('.popup_canvas').each(function(ind, i) {
				i.remove();
			});

			$(document).off('keypress', close);
		}

		var addStyle = '<style>'
		+ '.popup_canvas{width:100%;min-height:100%;background-color: rgba(0,0,0,0.5);overflow:hidden;position:fixed;top:0px;text-align: center; z-index:20;}'
		+ '.popup{display: inline-block;margin:40px auto 0px auto;width:initial;max-width:90%;max-height: 80%;padding:10px;background-color: #c5c5c5;border-radius:5px;box-shadow: 0px 0px 10px #000;}'
		+ '</style>';

		document.head.insertAdjacentHTML('beforeend', addStyle);

		// var df = document.createDocumentFragment(), //  onclick="close()"
		var pw = '<div class="popup_canvas">'
		+ '<div class=popup><div class=close_button style="position: absolute;right: 1em;"></div>';
		// document.createElement('div')

		Object.keys(o).forEach(function(name) {
			this[name] = Object.assign(def, this[name]);
			pw += (!name.includes('empty') ?
			name + ' - ' :
			'') + '<' + this[name].tag + ' style= "' + this[name].style + '">' + (this[name].html || '') + '</' + this[name].tag + '>\n';

			// f.innerHTML = i.html;
			// pw.appendChild(f);
		}, o);

		pw += '</div></div>';

		document.body.insertAdjacentHTML('beforeend', pw);

		$(document).on('keypress', close);
		// $(document).e.add('keyup', close);
		$('.popup_canvas').on('click', close);
		$('.popup_canvas .close_button').each(function(ind, i) {
			_H.closeButton(i);
		});
	},
	// popup

	/**
	 * Динамическая подгрузка скриптов
	 * beta
	 */
	/* loadScript: function (src, callback) {
		var script = document.createElement('script'),
				out = {};
			script.src = src;
			document.head.append(script);

		if(window.Promise) {
			return new Promise(function(resolve, reject) {

				script.onload = () => resolve(script);
				script.onerror = () => reject(new Error(`Ошибка загрузки скрипта ${src}`));
			});

		} else {
			script.onload = () => callback(null, script);
			script.onerror = () => callback(new Error(`Ошибка загрузки скрипта ${src}`));

			return out;
		}

	}, */


	defer: {
		// Storage 4 evaluating after every $.load()
		funcs: [],
		tocs: [],
		complete: 0,
		add: function(fn) {
			var toc = fn.toString().trim();
			// console.log(toc);
			if(this.tocs.indexOf(toc) !== -1) {
				console.log('Функция ', toc, ' уже сохранена!');
				return;
			}
			this.tocs.push(toc);
			this.funcs.push(fn);
			return this;
		},
		clean: function() {
			this.funcs = [];
			this.tocs = [];
			this.complete = 0;
			return this;
		},
		eval: function() {
			if(this.complete) return;
			this.funcs.forEach(function(i) {
				if(typeof i === 'function') {
					// console.log(i);
					i();
				}
			});
			this.complete = 1;
			console.log(
				'_H.defer.complete = ' + this.complete
				, "\nfuncs ", this.funcs
			);
		},
	}

}; // _H


Object.defineProperties(_H, {
	defer: {
		writable: true
	}

});
