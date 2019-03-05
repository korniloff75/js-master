var _H = {
	folder: 'content/'
}


// Menu 3
var navMenu = Vue.component('menu-item', {
	// Получаемые параметры из root
	props: ['pages', 'curPage'],

  data: function () {
    return {
			activeItem: null
    }
	},

	methods: {
		navHandler: function(e) {
			// console.log(arguments);
			var t = e.target.closest('a'),
				li = t.parentNode;
			// if(t) alert(t.href);
			__content.updateContent.call(this, (t.href.split('/').pop()));

			e.currentTarget.querySelector('li.active') && e.currentTarget.querySelector('li.active').classList.remove('active');
			li.classList.add('active');

			activeItem = +(li.getAttribute('data-ind'));

		},

		navItem: function(e) {
			var t = e.target.closest('a');
			// if(t) e.preventDefault();

			// console.log(t);
			// __content.updateContent.call(this, (t.href.split('/').pop()));

		},

		isActive: function(ind) {
			console.log('this.dataInd = ', this.$props);
			return ind === this.$root.defineCurPage.ind
		}
	}, // methods

	created: function() {
		console.log( this.curPage);
	},

	template: "#menu-item-template"

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
		pages: Pages.map(i=> [i])
	},

	computed: {
		defineCurPage: function() {
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

			axios.post(_H.folder + url)
			.then(function (response) {
				_this.doc = (new DOMParser()).parseFromString(response.data, "text/html");
				document.title = _this.doc.title;

				// fix src
				[].forEach.call(_this.doc.querySelectorAll('img'), function(i) {
					// console.log('i.src = ', i.src);
					i.src = i.src.replace(new RegExp('(' + location.host + location.pathname + ')', 'i'), '$1content/');

					/* console.log('__aside.defineCurPage.filename = ', __aside.defineCurPage.filename.split('.')[0],
						'\ni.src = ', i.src
					); */
				});

				_this.html = _this.doc.body.innerHTML;

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
				// '\nthis  = ', this
			);
		},

	},


	// Hooks
	created: function () {
		var cur_path = __aside.defineCurPage.filename;

		console.log(
			'navMenu.$data = ', navMenu.$data, '\n',
			'cur_path = ', cur_path, '\n'
		);

		this.updateContent(cur_path);

	}, // created


	beforeUpdate() {

	}, // beforeUpdate

	updated() {

		console.log(
			'page updated from ', this.doc
		);

	},

}); // __content