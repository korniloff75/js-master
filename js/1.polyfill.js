Object.assign || Object.defineProperty(Object, 'assign', {
	enumerable: false,
	configurable: true,
	writable: true,
	value: function (target, firstSource) {
		if (target === undefined || target === null) { throw new TypeError('Cannot convert first argument to object'); }
		var to = Object(target);
		for (var i = 1, L = arguments.length; i < L; i++) {
			Object.keys(arguments[i]).forEach(function(p) {
				to[p] = arguments[i][p];
			})
		}
		return to;
	}
});


if (![].includes) {
	Array.prototype.includes = String.prototype.includes = function (searchElement, fromIndex) {
		fromIndex = fromIndex || 0;
		return this.indexOf(searchElement, fromIndex) > -1;
	}
}


if (!NodeList.prototype.forEach) {
	NodeList.prototype.forEach = Array.prototype.forEach;
}


// closest && fix matches
;(function(EL) {
	if(EL.closest) return;

	EL.matches = EL.matches || EL.mozMatchesSelector || EL.msMatchesSelector || EL.oMatchesSelector || EL.webkitMatchesSelector;

	EL.closest = function closest(selector) {
		if (!this) return null;
		if (this.matches(selector)) return this;
		if (!this.parentElement) {return null}
		else return this.parentElement.closest(selector)
	};

	EL.getBoundingClientRect || Object.defineProperty(EL, 'getBoundingClientRect', {
		value: function () {
			var top = 0,
				left = 0,
				elem = this;
			while (elem) {
				top += parseInt(elem.offsetTop);
				left += parseInt(elem.offsetLeft);
				elem = elem.offsetParent;
			}
			return { top: top, left: left }
		},
		writable: 1
	});
}(Element.prototype));


Object.setPrototypeOf = Object.setPrototypeOf || function (obj, proto) {
	!/MSIE [6-9]/.test(navigator.appVersion) ? (obj.__proto__ = proto) : _K.clonePpts(obj, proto, { enum: 1 });
	return obj;
};

Object.getPrototypeOf = Object.getPrototypeOf || function (obj) { return obj.__proto__ };


Object.values = Object.values || function(obj) {
	var
		allowedTypes = ["[object String]", "[object Object]", "[object Array]", "[object Function]"],
		objType = Object.prototype.toString.call(obj);

	if(obj === null || typeof obj === "undefined") {
		throw new TypeError("Cannot convert undefined or null to object");
	} else if(allowedTypes.includes(objType)) { // allowedTypes.indexOf(objType) >= 0
		return [];
	} else {
		return Object.keys(obj).map(function (key) {
			return obj[key];
		});
	}
};


window.getComputedStyle = window.getComputedStyle || function (elem) { return elem.currentStyle };

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


document.documentElement.hidden !== undefined || Object.assign(HTMLElement.prototype, {
	get hidden() { return this.style && this.style.display === 'none' },
	set hidden(a) {
		this.style && (this.style.display = !!a ? 'none' : '');
	}
});



/*
 * object.watch polyfill
 *
 * 2012-04-03
 *
 * By Eli Grey, http://eligrey.com
 * Public Domain.
 * NO WARRANTY EXPRESSED OR IMPLIED. USE AT YOUR OWN RISK.
 *
 * o.watch('p', function (id, oldval, newval) {
	  console.log('o.' + id + ' изменено с ' + oldval + ' на ' + newval);
	  return newval;
	});
 */

// object.watch
if (!Object.prototype.watch) {
	Object.defineProperty(Object.prototype, "watch", {
		  enumerable: false
		, configurable: true
		, writable: false
		, value: function (prop, handler) {
			var
			  oldval = this[prop]
			, newval = oldval
			, getter = function () {
				return newval;
			}
			, setter = function (val) {
				oldval = newval;
				return newval = handler.call(this, prop, oldval, val);
			}
			;

			if (delete this[prop]) { // can't watch constants
				Object.defineProperty(this, prop, {
					  get: getter
					, set: setter
					, enumerable: true
					, configurable: true
				});
			}
		}
	});
}

// object.unwatch
if (!Object.prototype.unwatch) {
	Object.defineProperty(Object.prototype, "unwatch", {
		  enumerable: false
		, configurable: true
		, writable: false
		, value: function (prop) {
			var val = this[prop];
			delete this[prop]; // remove accessors
			this[prop] = val;
		}
	});
}



var FormData = FormData || function(form) {
	return window.$ && $.rnd && $(form).ajaxForm();
}


String.prototype.trim = String.prototype.trim || function () { return this.replace(/^\s+|\s+$/gm, '') };
String.prototype.ltrim = String.prototype.ltrim || function () { return this.replace(/^\s+/gm, '') };
String.prototype.tabTrim = function () { return this.replace(/\t/gm, '  ') };
String.prototype.rtrim = String.prototype.rtrim || function () { return this.replace(/\s+$/gm, '') };
String.prototype.fulltrim = String.prototype.fulltrim || function () { return this.replace(/((^|\n)\s+|\s+($|\n))/gm, '').replace(/\s+/gm, ' '); };

String.prototype.toFun || Object.defineProperty(String.prototype, 'toFun', {
	get: function () { return new Function(this) }
});
Math.sign = Math.sign || function (x) {
	x = +x;
	return (x === 0 || isNaN(x)) ? x : x > 0 ? 1 : -1;
}