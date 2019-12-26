'use strict';

// Define globals
var
	$gl = $(window);

/* Object.defineProperties(window, {
	_S.v.$nav_items: {
		get: function() {
			return _S.v.$nav_block.find('.nav_item')
		}
	}
}) */


window.onpopstate = function(e) {
	if(!e.state) return;
	_S.v.$page_content.html();
	_S.v.$page_content.html(e.state.html);
	$.cookie.set({page_ind: e.state.page_ind});

	_S.afterLoad(_S.v.$nav_items[e.state.page_ind]);

	// console.log(e.state, nav_items[e.state.page_ind]);
};


// servise
var _S = {
	v: {},
	loadPage: function loadPage($nextItem, direct) {
		// ajax load page from $nextItem attr 'data-page'
		$nextItem = $.check($nextItem);
		direct = direct || 1;
		_S.v.$bg_mask.css({ opacity: .5, zIndex: 10 });

		// console.log('$nextItem = ', $nextItem);

		var profile = new Date(),
		page_ind = $.cookie.get('page_ind'),
		pagePath = _H.fixSlash($nextItem.attr('data-page') || $nextItem[0]['data-page']);

		// console.log('pagePath = ', pagePath);

		document.loading = 1;

		/* clean */
		{
			_S.v.$page_content.html('');
			window.myMap && myMap.destroy();
			_H.defer.clean();
		}

		// Рендеринг по ответу сервера
		_S.v.$page_content.load('/', {
			page: pagePath,
			ajax: 1
		}, function (response, status) {
			// 403, 404
			if(status !== 'success') {
				// _H.nav.next(_S.v.$nav_block);
				var $nextItem = _H.nav.serv(_S.v.$nav_block, direct);
				console.log(
					"$nextItem = ", $nextItem,
					"\ndirect = ", direct
					);
				return loadPage($nextItem);
			}
			_H.open.call(_S.v.$sidebar);

			_S.afterLoad(pagePath, response);

			// Конец рендеринга
			document.loading = 0;

			console.info("Время рендеринга страницы, мс - ", new Date() - profile, "\nresponse status = ", status);
		});
	}, // loadPage


	afterLoad: function(path, response) {

		var page_ind = _H.nav.current(_S.v.$nav_items, path);

		// console.log('page_ind = ', page_ind);

		_S.v.$menu.removeClass('opened');

		_S.v.$bg.css({
			backgroundImage: 'url(\'/' + $.rnd(sv.IMAGES.length && sv.IMAGES || sv.BG) + '\')'
		});

		// addons
		if(sv.AJAX) {
			if( window.Img) {
				Img.inited = 0;
				window.Img.init();
			}
			window.DizSel && _H.defer.add(DizSel.prevent);
			window.CC && _H.defer.add(CC.init);
			window._A && _H.defer.add(_A.init);
			_H.ADs && _H.defer.add(_H.ADs.init.bind(_H.ADs));

			// Update #adm
			if(sv.ADMIN) {
				_S.wrapAdm = _S.wrapAdm || $('#adm').wrap('<div>').parent();
				_S.wrapAdm.load('/' + sv.DIR, {
					adm: {loadSettings: 1}
				});

			}
		}

		// console.log(bg.css('backgroundImage'));

		_S.v.$bg_mask.css({ opacity: '', zIndex: 1 });

		_H.defer.eval();

		if(response) {
			history.pushState({
				html: response,
				page_ind: page_ind
			}, document.title, '/' + path);
			// }, document.title, '/?page=' + path);
		}

		// console.log(_S.v.$nav_items, page_ind);

	}, // afterLoad

}; // _S


_S.v = {
	$bg : $('#bg'),
	$bg_mask : $('div#bg_mask'),
	$logo : $('#logo'),
	$sidebar : $('#sidebar'),
	$page_content : $('div#ajax-content'),
	$menu_butt : $('#menu_butt'),
	$menu : $('#menu_block'),
	$nav_main : $('#nav_main'),
	$nav_block : $('nav#nav_block'),
}

Object.defineProperties(_S.v, {
	$menu_items: {
		get: function() {
			return this.$menu.find('li>a')
		}
	},
	$nav_items: {
		get: function() {
			return this.$nav_block.find('.nav_item')
		}
	},
});


var Doc_evs = {

	// Navigate on wheel
	wheel: function(e) {
		e = $().e.fix(e);
		var $t = $.check(e.target),
		$contentChild = $t.closest(_S.v.$page_content, _S.v.$page_content);

		// console.log('this = ', this, (this === document), this.loading);

		if(this.loading || e.target.closest('#adm, #menu_block, #edit_comm') || $contentChild.length) return;

		// console.log('$contentChild = ', $contentChild, $t);

		this.mousePos = {
			x: 0,
			y: 0
		}

		e.wheel = {
			x: 0,
			y: -e.deltaY*1e3
		}

		console.log(
			'wheel deltaY= ', e.type, e.deltaY, '\n'
			// ,'mousePos= ', this.mousePos, e.chT
			// ,'\ne= ', e
		);

		Doc_evs.mouseup.call(this, e);
	},


	// Background parallax
	mousemove: (function (e) {
		e = $().e.fix(e);
		if(!_S.v.$bg.length ||!e.target.closest || e.target.closest('#sidebar')) return;

		// console.log(e.target.closest('#logo'));

		var left = (e.pageX - _S.v.$bg.offset().left),
		top = (e.pageY - _S.v.$bg.offset().top);

		$(this).css({
			left: -0.12 * left + 'px',
			top: -(0.1 * top) + 'px',
		});
	}).bind(_S.v.$bg),


	mouseover: function(e) {
		e = $().e.fix(e);
		var t = e.target;
		if(t.closest('#logo') && !t.closest('#sidebar')) {
			// console.log(t, t.closest('#sidebar'));
			// Transform bg
			_S.v.$bg.addClass('zoom');
		}

	},

	mouseout: function(e) {
		e = $().e.fix(e);
		var t = e.target;
		if(t.closest('#logo')) {
			// Transform bg
			_S.v.$bg.removeClass('zoom');
		}

	},


	mousedown: function(e) {
		e = $().e.fix(e);
		// e.preventDefault(); e.stopPropagation();

		// save click coords
		this.mousePos = {
			x: e.pageX || e.chT.x,
			y: e.pageY || e.chT.y
		};

		// console.log(this.mousePos);
	},


	mouseup: function(e) {
		e = $().e.fix(e);
		var t = e.target,
			contentChild = $.check(t).closest(_S.v.$page_content, _S.v.$page_content);

		// console.log('loading = ', this.loading );

		if(
			this.loading
			|| !e.wheel && !e.chT
			|| t.closest('#adm, #menu_block, #edit_comm')
			|| contentChild.length
		) return;

		e.wheel = e.wheel || {};

		var d_touch= {
			x: (e.wheel.x || e.pageX || e.chT.x) - this.mousePos.x,
			y: (e.wheel.y || e.pageY || e.chT.y ) - this.mousePos.y,

			get horiz() {
				return Math.abs(this.x) > Math.abs(this.y * 2.5)
			},
			get vertical() {
				return Math.abs(this.y) > Math.abs(this.x * 2.5)
			},

		};

		// console.log('d_touch = ', d_touch, d_touch.horiz, d_touch.vertical);

		// remove random  taouchs
		if(Math.abs(d_touch.x) < 20 && Math.abs(d_touch.y) < 20) return;

		e.preventDefault();
		// e.stopPropagation();
		this.mouselive = d_touch;

		// console.log('horiz = ', this.mouselive && this.mouselive.horiz, d_touch);

		if(d_touch.horiz) {
			// horis

			// sidebar
			if(t.closest('#sidebar') && Math.sign(d_touch.x) > 0) {
				_H.close.call(_S.v.$sidebar);
			}

			else if(Math.sign(d_touch.x) < 0 && !_S.v.$sidebar.hasClass('opened')) {
				_H.open.call(_S.v.$sidebar);
			}

		} else if(d_touch.vertical) {
			// vertical

			if(t.closest('#sidebar, #menu_block, .popup_canvas')) {
				return;
			};

			/* prev / next page ... */
			{
				if(Math.sign(d_touch.y) < 0 ) {
					var $nextItem = _H.nav.next(_S.v.$nav_main);
				} else {
					var $nextItem = _H.nav.prev(_S.v.$nav_main);
				}
			}

			// console.log('$nextItem = ', $nextItem);
			_S.loadPage($nextItem, -Math.sign(d_touch.y));
		}

	},
	// mouseup

	touchstart: function(e) {
		return Doc_evs.mousedown.call(this, e);
	},

	touchend: function(e) {
		return Doc_evs.mouseup.call(this, e);
	},

	/* touchmove: function(e) {
		// occurs before mousemove
		e = $().e.fix(e);
		var t = e.target;

		// doc_evs.mousemove.call(this, e);
		// $(this).e.remove('mousemove', doc_evs.mousemove);

		// console.log(t.closest('#sidebar'));
		if(t.closest('#sidebar')) return;

		// $().e.fix(e).preventDefault();
	}, */


	click: function(e) {
		e = $().e.fix(e);
		var t = e.target,
		$t = $(t);


		if($t.closest(_S.v.$page_content).length) {
			return;
		}

		// console.log(e.which);

		// Open/Close sidebar
		if(t.closest('.toSidebar')) {
			_H.open.call(_S.v.$sidebar);

		} else if(!t.closest('aside, #edit_comm')) {
			_H.close.call(_S.v.$sidebar);
		}

		// Close menu
		if(_S.v.$menu.hasClass('opened') && (t.closest('#menu_close') || !t.closest('#menu_block'))) {
			_H.close.call(_S.v.$menu);
		}

		if(t.closest('#menu_block')) {
			e.stopPropagation();
		}

	},

	keyup: function(e) {
		e = $().e.fix(e);
		if(e.defKeyCode('esc')) _H.close.call(_S.v.$menu);
	},
} // doc_evs



_S.v.$menu_butt.on({
	click: function(e) {
		_H.open.call(_S.v.$menu);
		$().e.fix(e).stopPropagation();
	}
});

// fix scroll sidebar
_S.v.$sidebar.on({
	wheel: function(e) {
		// console.log('scroll', this.scrollHeight, w.innerHeight());
		if(this.scrollHeight*.99 <= $gl.innerHeight()) return;
		$().e.fix(e).stopPropagation();
	},
	/* touchend: function(e) {
		console.log(this);
		$().e.fix(e).stopPropagation();
	} */
});


// menu handlers
_S.v.$nav_main.on({
	// ajax 4 changing page_content
	click: function (e) {
		var t;
		if (!(t = e.target.closest('.nav_item'))) {
			// console.log('menu_blocked', t);
			return;
		}

		// console.log('sysData = ', sysData);

		_S.loadPage(t);

	},

});


// handler 4 deligation

$(document).on(Doc_evs);


$(function() {
	// onload set active menu item
	_S.v.$nav_main.find('.nav_item:first').addClass('active');

	_S.v.$menu_items.each(function(_ind,i) {
		i['data-page'] = _H.getPath(i.href);
		// console.log('page = ', i['data-page'], i);
	});

	_S.v.$menu.on({
		mouseover: function(e) {
			e = $().e.fix(e);
			var t = e.target.closest('li');

			if(!t) return;
			// console.log($(t).siblings());
			$(t).siblings().css({
				opacity: .3
			});
		},

		mouseout: function(e) {
			e = $().e.fix(e);
			var t = e.target.closest('li');

			if(!t) return;

			$(t).siblings().css({
				opacity: 1
			});
		},

		wheel: function(e) {
			e.stopPropagation();
		}
	}); // _S.v.$menu

	_S.afterLoad();

	// draw closses
	$('.close_button').each(function(_ind,i) {
		_H.closeButton(i);
	});

	// ajax on menu
	$('#menu').on('click', function(e) {
		e = $().e.fix(e);
		var t= e.target.closest('a');
		if(!t) return;

		e.preventDefault();
		// console.log(this);
		_S.loadPage(t);
	});
}); // $()