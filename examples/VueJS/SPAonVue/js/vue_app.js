var Mixins = {
	methods: {
		updateContent: function(url) {
			var _this = __content || this;
			console.clear();

			axios.post(__aside.folder + url)
			.then(function (response) {
				_this.doc = (new DOMParser()).parseFromString(response.data, "text/html");
				document.title = _this.doc.title;

				// _this.evalScripts();
				// _this.fixSRC();

				_this.html = _this.doc.documentElement.innerHTML;

				_this.$nextTick(_this.evalScripts);

				// _this.$forceUpdate();
				// _this.html = _this.doc.body.innerHTML;


				history.pushState({
					title: document.title,
					content: _this.html
				}, document.title, '?' + url);
			})
			.catch(function (error) {
				console.error(error);
			});

			console.log(
				// url,
				// '\n this.$el  = ',  this.$el
			);
		}, // updateContent

		fixSRC: function() {
			var imgs = this.doc.querySelectorAll('img');
			if(!imgs.length) return;

			[].forEach.call(imgs, function(i) {
				// console.log('i.src = ', i.src);
				i.src = i.src.replace(new RegExp('(' + location.host + location.pathname + ')', 'i'), '$1content/');

				/* console.log('__aside.defineCurPage.filename = ', __aside.defineCurPage.filename.split('.')[0],
					'\ni.src = ', i.src
				); */
			});
		},

		evalScripts() {
			console.log('evalScripts / $nextTick\n');
			[].forEach.call(
			this.doc.querySelectorAll('script'),
			i => {
				console.log(i);
				if(i.src) {
					var s = document.createElement('script');
					s.src = i.src;
					i.remove();
					document.head.appendChild(s);
				} else eval(i.innerHTML);
			});
		},

		// Очищаем глобал перед обновлением
		clearClob() {
			if(this.cash) {
				var excludes = ['__VUE_DEVTOOLS_TOAST__'];
				Object.keys(window).forEach(k=>{
					var ind = this.cash.indexOf(k);
					if(ind === -1 && excludes.indexOf(k) === -1) {
						console.log('k_del = ', k);
						delete window[k];
					}
				});
			}
		},

	}, // methods


	created() {
		// Кешируем глобал
		this.cash = this.cash || Object.keys(window);
		// this.$forceUpdate()
	},

	// beforeUpdate
	updated() {
		// this.$nextTick(this.evalScripts);
		console.log(
			'updated\n',
			'this = ', this);
		// this.fixSRC();
	},
}; // Mixins


// Menu
var navMenu = Vue.component('menu-items', {
	// Получаемые параметры из root
	props: ['pages', 'curPage'],

  data () {
    return {
			activeItem: null
    }
	},

	mixins: [Mixins],

	methods: {
		navHandler (e) {
			// console.log(arguments);
			var t = e.target.closest('a'),
				li = t.parentNode;
			// if(t) alert(t.href);

			this.clearClob();
			this.updateContent(t.href.split('/').filter(i=>i.trim()).pop());

			e.currentTarget.querySelector('li.active') && e.currentTarget.querySelector('li.active').classList.remove('active');
			li.classList.add('active');

			activeItem = +(li.getAttribute('data-ind'));

		},

		isActive (ind) {
			// don't used
			// console.log('navMenu.$props = ', this.$props);
			return ind === this.$root.defineCurPage.ind
		}
	}, // methods

	created: function() {
		// console.log('navMenu.curPage = ', this.curPage);
	},

	template: "#menu-items-template"

}); // navMenu


window.onpopstate = function (e) {
	__content.html = e.state.content;
	document.title = e.state.title;
	// console.log("location: " + document.location, "\n state: ", e.state);
};


//
var __aside = new Vue({
	el: 'aside',
	data: {
		folder: 'content/',
		pages: Pages.map(p=>[p]),
		/* pages: [
			['name.html', 'name 1', 'title'],
			['page2.html', 'name 2',  'О странице'],
			['page3.html', 'name 3',  'Страница 3'],
			['12 Platnje yslygi.htm', 'Подгружаем assets',  'Тест подгрузки assets'],
		] */
	},

	computed: {
		defineCurPage () {
			var out = {
				ind: 0,
				filename: this.pages[0][0],
			}
			this.pages.some((i, ind) => {
				var cond = location.search.indexOf(encodeURIComponent(i[0])) === 1;
				if(cond) {
					out.ind = ind;
					out.filename = this.pages[ind][0];
				}
				return cond;
			})
			return out;
		}
	},
}); // __aside

//
var __content = new Vue({
	el: '#content',
	data: {
		doc: 'empty',
		html: ''
	},

	mixins: [Mixins],

	methods: {

	}, // methods


	// Hooks
	created: function () {
		var cur_path = __aside.defineCurPage.filename;

		console.log(
			'cur_path = ', cur_path,
			// '\n this.$el  = ',  this.$el.innerHTML
		);

		this.updateContent(cur_path);

		console.log(
			'\n __content.doc  = ',  this.doc,
			'\n __content.$el  = ',  this.$el,
		);

	}, // created

	// beforeUpdate

	updated() {
		console.log(
			'page updated from ', this.doc,
			// '\n this.$el  = ',  this.$el
		);

	},

}); // __content