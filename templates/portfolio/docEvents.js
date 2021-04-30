var Doc_evs = ()=>{
	let Doc_evs= {

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

		// Close menu
		if(_S.v.$menu.hasClass('opened') && (t.closest('#menu_close') || !t.closest('#menu_block'))) {
			_H.close.call(_S.v.$menu);
		}

		if(t.closest('#menu_block')) {
			e.stopPropagation();
		}

		// console.log(e.which);

		// Open/Close sidebar
		if(t.closest('.toSidebar')) {
			_H.open.call(_S.v.$sidebar);

		} else if(!t.closest('aside, #edit_comm, .uk-modal-page')) {
		// } else if(t.closest('#bg_wraper')) {
			_H.close.call(_S.v.$sidebar);
		}

		console.log({t});

	},

	keyup: function(e) {
		e = $().e.fix(e);
		if(e.defKeyCode('esc')) _H.close.call(_S.v.$menu);
	},
}
return Doc_evs;
}; // Doc_evs


$(()=>{
	$(document).on(Doc_evs());

	// *menu handlers
	_S.v.$nav_main.on({
		// ajax 4 changing page_content
		click: function (e) {
			var t = e.target.closest('.nav_item');
			if (!t) {
				// console.log('menu_blocked', t);
				return;
			}

			// console.log('sysData = ', sysData);

			_S.loadPage(t);

		},

	});


	/* // *fix scroll sidebar
	_S.v.$sidebar.on({
		wheel: function(e) {
			// console.log('scroll', this.scrollHeight, w.innerHeight());
			if(this.scrollHeight*.99 <= $gl.innerHeight()) return;
			$().e.fix(e).stopPropagation();
		},
	}); */
});