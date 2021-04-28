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
	loadPage: function loadPage($targetItem, direct) {
		// ajax load page from $nextItem attr 'data-page'
		$targetItem = $.check($targetItem);
		direct = direct || 1;
		_S.v.$bg_mask.css({ opacity: .5, zIndex: 10 });

		// console.log('$nextItem = ', $nextItem);

		var profileTS = Date.now(),
		// page_ind = $.cookie.get('page_ind'),
		pagePath = _H.fixSlash($targetItem.attr('data-page') || $targetItem[0]['data-page']);

		// console.log('pagePath = ', pagePath);

		document.loading = 1;

		//* clean
		{
			_S.v.$page_content.html('');
			window.myMap && myMap.destroy();
			_H.defer.clear();
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

			console.info("Время рендеринга страницы, мс - ", Date.now() - profileTS, "\nresponse status = ", status);
		});
	}, // loadPage


	afterLoad: function(path, response) {

		var page_ind = _H.nav.current(_S.v.$nav_items, path);

		// console.log('page_ind = ', page_ind);

		_S.v.$menu.removeClass('opened');

		/* sv.IMAGES && _S.v.$bg.css({
			backgroundImage: 'url(\'/' + $.rnd(sv.IMAGES.length && sv.IMAGES || sv.BG) + '\')'
		}); */

		// addons
		if(sv.AJAX) {
			if( window.Img) {
				Img.inited = 0;
				window.Img.init();
			}
			window.DizSel && _H.defer.add(DizSel.prevent);
			window.CC && _H.defer.add(CC.init);
			window._A && _H.defer.add(_A.init);
			// _H.ADs && _H.defer.add(_H.ADs.init.bind(_H.ADs));
			_H.RSYa && _H.defer.add(_H.RSYa.init.bind(_H.RSYa));

			// Update #adm
			/* if(sv.ADMIN) {
				_S.wrapAdm = _S.wrapAdm || $('#adm').wrap('<div>').parent();
				_S.wrapAdm.load('/' + sv.DIR, {
					adm: {loadSettings: 1}
				});

			} */
			if(sv.ADMIN) {
				_S.wrapAdm = _S.wrapAdm || $('#adm').wrap('<div>').parent();
				_S.wrapAdm.load('/', {
					page: sv.DIR + '?updAdminBlock',
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




_S.v.$menu_butt.on({
	click: function(e) {
		_H.open.call(_S.v.$menu);
		$().e.fix(e).stopPropagation();
	}
});




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