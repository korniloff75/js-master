// Menu
var navMenu = Vue.component('menu-items', {
	// Получаемые параметры из root
	props: ['pages', 'curPage'],

  data () {
    return {
			activeItem: null
    }
	},

	methods: {
		navHandler (e) {
			// console.log(arguments);
			var t = e.target.closest('a'),
				li = t.parentNode;
			// if(t) alert(t.href);
			__content.updateContent.call(this, (t.href.split('/').filter(i=>i.trim()).pop()));

			e.currentTarget.querySelector('li.active') && e.currentTarget.querySelector('li.active').classList.remove('active');
			li.classList.add('active');

			activeItem = +(li.getAttribute('data-ind'));

		},

		isActive (ind) {
			// don't used
			console.log('this.$props = ', this.$props);
			return ind === this.$root.defineCurPage.ind
		}
	}, // methods

	created: function() {
		console.log('navMenu.curPage = ', this.curPage);
	},

	template: "#menu-items-template"

});


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
		pages: [
			['name.html', 'name 1', 'title'],
			['page2.html', 'name 2',  'О странице'],
			['page3.html', 'name 3',  'Страница 3'],
			['12 Platnje yslygi.htm', 'Подгружаем assets',  'Тест подгрузки assets'],
		]
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


	methods: {
		updateContent: function(url) {
			var _this = this;

			axios.post(__aside.folder + url)
			.then(function (response) {
				_this.doc = (new DOMParser()).parseFromString(response.data, "text/html");
				document.title = _this.doc.title;

				_this.fixSRC();

				_this.html = _this.doc.body.innerHTML;
				// _this.$el.appendChild(_this.doc.body)

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
			[].forEach.call(this.doc.querySelectorAll('img'), function(i) {
				// console.log('i.src = ', i.src);
				i.src = i.src.replace(new RegExp('(' + location.host + location.pathname + ')', 'i'), '$1content/');

				/* console.log('__aside.defineCurPage.filename = ', __aside.defineCurPage.filename.split('.')[0],
					'\ni.src = ', i.src
				); */
			});
		}

	}, // methods


	// Hooks
	created: function () {
		var cur_path = __aside.defineCurPage.filename;

		console.log(
			'cur_path = ', cur_path,
			// '\n this.$el  = ',  this.$el.innerHTML
		);

		this.updateContent(cur_path);

	}, // created


	mounted() {
		console.log(
			'\n __content.doc  = ',  this.doc,
			'\n __content.$el  = ',  this.$el,
		);
		// this.$el.appendChild(this.doc)
	}, // beforeUpdate

	updated() {

		console.log(
			'page updated from ', this.doc,
			// '\n this.$el  = ',  this.$el
		);

	},

}); // __content