'use strict';

// Define globals
var body_main = $('section#body_main'),
	$bg = $('#bg'),
	$bg_mask = $('div#bg_mask'),
	$logo = $('#logo'),
	$sidebar = $('#sidebar'),
	$page_content = $page_content || $('div#ajax-content'),
	$menu_butt = $('#menu_butt'),
	$menu = $('#menu_block'),
	$menu_items = $menu.find('a'),
	$nav_main = $('#nav_main'),
	$nav_block = $('nav#nav_block'),
	$nav_items = $nav_block.find('.nav_item'),
	$gl = $(window);


window.onpopstate = function(e) {
	if(!e.state) return;
	$page_content.html();
	$page_content.html(e.state.html);
	$.cookie.set({page_ind: e.state.page_ind});

	_S.afterLoad($nav_items[e.state.page_ind]);

	// console.log(e.state, nav_items[e.state.page_ind]);
};


// servise
var _S = {
	loadPage: function(nextItem) {
		// ajax load page from nextItem attr 'data-page'

		$bg_mask.css({ opacity: .5, zIndex: 10 });
		var profile = new Date(),
		pagePath = _H.fixSlash(nextItem.getAttribute('data-page') || nextItem['data-page']);

		// console.log('pagePath = ', pagePath);

		document.loading = 1;

		// clean
			$page_content.html('');
			window.myMap && myMap.destroy();

			_H.defer.clean();

		$page_content.load('/', {
			page: pagePath,
			ajax: 1
		}, function (response) {
		// $page_content.load('/?page=' + pagePath + '&ajax=1', function (response) {
			// console.log('sysData = ', sysData);

			// console.log('page_ind = ', page_ind);

			/* if(response.includes('<!DOCTYPE html>')) {
				// change template
				location.reload();
				// document.documentElement.innerHTML = response;
			} */

			_H.open.call($sidebar);

			_S.afterLoad(pagePath, response);

			document.loading = 0;

			console.info("Время рендеринга страницы, мс - ", new Date() - profile);
		});
	}, // loadPage


	afterLoad: function(path, response) {
		var current = _H.findItem($nav_items, path),
		page_ind = $.cookie.get('page_ind') || 0;

		// console.log('page_ind = ', page_ind);

		$menu.removeClass('opened');
		$bg.css({
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

			// Update #adm
			if(sv.ADMIN) {
				_S.wrapAdm = _S.wrapAdm || $('#adm').wrap('<div>').parent();
				_S.wrapAdm.load('/' + sv.DIR, {
					adm: {loadSettings: 1}
				});

			}
		}

		// console.log(bg.css('backgroundImage'));

		$bg_mask.css({ opacity: '', zIndex: 1 });

		// set active menu item
		$nav_items.each(function(page_ind,i) {
			i.classList.remove('active');
		});

		_H.defer.eval();

		if(response) {
			history.pushState({
				html: response,
				page_ind: page_ind
			}, document.title, '/' + path);
			// }, document.title, '/?page=' + path);
		}

		// console.log(current, $nav_items, page_ind);
		$(current || $nav_items[page_ind]).addClass('active');

	}, // afterLoad

}; // _S


var Doc_evs = {

	// Navigate on wheel
	wheel: function(e) {
		e = $().e.fix(e);
		var $t = $(e.target),
		$contentChild = $t.closest($page_content, $page_content);

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
	mousemove: function (e) {
		e = $().e.fix(e);
		if(!$bg.length ||!e.target.closest || e.target.closest('#sidebar')) return;

		// console.log(e.target.closest('#logo'));

		var left = (e.pageX - $bg.offset().left),
		top = (e.pageY - $bg.offset().top);

		$(this).css({
			left: -0.12 * left + 'px',
			top: -(0.1 * top) + 'px',
		});

		/* $('#bg_pattern').css({
			backgroundPosition: '-' + (0.03 * left) + 'px -' + (0.03 * top) + 'px'
		}) */
	}.bind($bg),


	mouseover: function(e) {
		e = $().e.fix(e);
		var t = e.target;
		if(t.closest('#logo') && !t.closest('#sidebar')) {
			// console.log(t, t.closest('#sidebar'));
			// Transform bg
			$bg.addClass('zoom');
		}

	},

	mouseout: function(e) {
		e = $().e.fix(e);
		var t = e.target;
		if(t.closest('#logo')) {
			// Transform bg
			$bg.removeClass('zoom');
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
			contentChild = $(t).closest($page_content, $page_content);

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
		if(Math.abs(d_touch.x) < 10 && Math.abs(d_touch.y) < 10) return;

		e.preventDefault();
		// e.stopPropagation();
		this.mouselive = d_touch;

		// console.log('horiz = ', this.mouselive && this.mouselive.horiz, d_touch);

		if(d_touch.horiz) {
			// horis

			// sidebar
			if(t.closest('#sidebar') && Math.sign(d_touch.x) > 0) {
				_H.close.call($sidebar);
			}

			if(Math.sign(d_touch.x) < 0 && !$sidebar.hasClass('opened')) {
				_H.open.call($sidebar);
			}

		} else if(d_touch.vertical) {
			// vertical

			if(t.closest('#sidebar')) {
				return;
			};

			// prev / next page ...
			if(Math.sign(d_touch.y) < 0 ) {
				var nextItem = $nav_main.find('.nav_item.active').next()[0] || $nav_main.find('.nav_item:first')[0];

			} else {
				var nextItem = $nav_main.find('.nav_item.active').prev('.nav_item')[0] || $nav_main.find('.nav_item:last')[0];
			}

			// console.log('nextItem = ', nextItem);
			_S.loadPage(nextItem);
		}

	},

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


		if($t.closest($page_content).length) {
			return;
		}

		// console.log(e.which);

		// Open/Close sidebar
		if(t.closest('.toSidebar')) {
			_H.open.call($sidebar);

		} else if(!t.closest('aside, #edit_comm')) {
			_H.close.call($sidebar);
		}

		// Close menu
		if($menu.hasClass('opened') && (t.closest('#menu_close') || !t.closest('#menu_block'))) {
			_H.close.call($menu);
		}

		if(t.closest('#menu_block')) {
			e.stopPropagation();
		}

	},

	keyup: function(e) {
		e = $().e.fix(e);
		if(e.defKeyCode('esc')) _H.close.call($menu);
	},
} // doc_evs



$menu_butt.on({
	click: function(e) {
		_H.open.call($menu);
		$().e.fix(e).stopPropagation();
	}
});

// fix scroll sidebar
$sidebar.on({
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
$nav_main.on({
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
	$nav_main.find('.nav_item:first').addClass('active');

	$menu_items.each(function(ind,i) {
		i['data-page'] = _H.getPath(i.href);
		// console.log('page = ', i['data-page']);
	});

	$menu.on({
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
	}); // $menu

	_S.afterLoad();

	// draw closses
	$('.close_button').each(function(ind,i) {
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