'use strict';
/**
 ** KorniloFF - https://js-master.ru/examples/reshu.su/
 */
(function() {
	// *Пути к стилям дизайна
	var DEF_CSS= '',
	// var DEF_CSS= '_defaults.css',
		DARK_CSS= '_defaults black_my.css';
		// DARK_CSS= '_defaults black.css';

	var DARK_MODE= 1;

	var nav= document.querySelector('aside.book-menu>nav'),
		headers= nav.querySelectorAll('li.book-section-flat'),
		// allItems={},
		tabsBlock= document.createElement('div'),
		tabsBlockHidden= document.createElement('ul'),
		oldBlock= nav.querySelector('ul:not([id])'),
		contentBlock= document.createElement('div');

	tabsBlockHidden.hidden= 1;
	tabsBlock.id= 'tabsBlock';
	contentBlock.id= 'contentBlock';

	tabsBlock.innerHTML='';

	[].forEach.call(headers, (i,ind)=>{
		var title= i.querySelector('span').textContent,
			link= document.createElement('li');

		link.content= i.querySelector('ul');
		link.classList.add('tabsitem');

		var active= !tabsBlock.children.length && i.querySelector(`a[href*='${location.pathname.slice(0,-1)}']`);

		(active? tabsBlock: tabsBlockHidden).appendChild(link);

		// allItems[title]= link.content;
		link.textContent= title;

	});

	// *Define currentHeader

	if(!tabsBlock.children.length)
		tabsBlock.appendChild(tabsBlockHidden.children[0]);

	tabsBlock.children[0].classList.add('active');
	contentBlock.appendChild(tabsBlock.children[0].content);


	// *Events on tabsBlock

	tabsBlock.addEventListener('click', changeTabs);
	tabsBlock.addEventListener('mouseleave', e=>{
		tabsBlockHidden.hidden= 1;
	});

	function changeTabs(e) {
		var t= e.target;

		tabsBlockHidden.hidden= !tabsBlockHidden.hidden;

		if(!t.classList.contains('tabsitem')) {
			// tabsBlock.addEventListener('mouseenter', displayHeaders);
			return;
		}

		// console.log(t, contentBlock.children[0]);

		setActiveHeader(t);

	}


	// *Render

	tabsBlock.appendChild(tabsBlockHidden);

	// console.log('allItems= ', allItems, oldBlock);

	nav.insertBefore(tabsBlock, oldBlock);
	nav.insertBefore(contentBlock, oldBlock);
	// note
	nav.removeChild(oldBlock);


	// *Scroll to current menu item

	var curItem= contentBlock.querySelector(`a[href*='${location.pathname.slice(0,-1)}']`);

	// fix 4 /ua/
	if(curItem && location.pathname.length > 5) {
		var bcrItem= curItem.getBoundingClientRect(),
			bcrBlock= contentBlock.getBoundingClientRect(),

			top= bcrItem.top - bcrBlock.top + contentBlock.scrollTop - (bcrBlock.height + bcrItem.height)/2;
			// top= bcrItem.top + nav.scrollTop - document.body.clientHeight/2;

		curItem.classList.add('active');

		contentBlock.scrollTo(0,top);

		/* console.log(
			'bcrItem= ', bcrItem, top,
			document.body.clientHeight/2,
			contentBlock.scrollTop,
			'curItem= ', curItem
		); */
	}



	// *Styles

	if(DARK_MODE) darkModeInit();

	function darkModeInit () {
		var LiDark= document.createElement('li'),
			Btn= document.createElement('a');

		Btn.classList.add('styles');
		Btn.href= '#';

		Btn.addEventListener('click', setDark);

		LiDark.appendChild(Btn);
		nav.lastElementChild.appendChild(LiDark);

		var styles= document.querySelectorAll('link[href$="css"]'),
			// defStyle= document.querySelector('link[href*="book."]'),
			lastStyle= styles[styles.length-1],
			newStyle= document.createElement('link');

		newStyle.rel= 'stylesheet';
		console.log('styles', styles, lastStyle/*,  defStyle */);

		// defStyle.id='defStyle';
		// defStyle= document.querySelector('#defStyle');


		insertAfter(newStyle, lastStyle);

		setDark();

		function setDark (e) {
			var t;

			if(e) {
				t= e.target;
				e.stopPropagation();
				e.preventDefault();
				// t.dark= !decodeURI(defStyle.href).includes(DARK_CSS);
				t.dark= !decodeURI(newStyle.href).includes(DARK_CSS);
				localStorage.setItem('dark', t.dark);
			} else {
				t= Btn;
				t.dark= localStorage.getItem('dark') === 'true';
			}

			newStyle.href= t.dark? DARK_CSS: DEF_CSS;
			// defStyle.href= t.dark? DARK_CSS: DEF_CSS;

			t.textContent= t.dark? 'Включить свет': 'Выключить свет';

			// console.log(t.dark, !!t.dark, newStyle.href, localStorage.getItem('dark'));
		}
	}



	// *=================
	// *Helpers

	function setActiveHeader (item) {

		/* console.log(
			'item= ', item,
			'tabsBlockHidden= ', tabsBlockHidden
		); */

		tabsBlock.querySelector('.active') && tabsBlock.querySelector('.active').classList.remove('active');
		item.classList.add('active');

		tabsBlockHidden.appendChild(tabsBlock.querySelector('li'));
		tabsBlock.insertBefore(item,tabsBlockHidden);

		// *Change content
		contentBlock.innerHTML= '';
		contentBlock.appendChild(item.content);
	}


	function insertAfter (node, refNode) {
		if(refNode.nextSibling)
			refNode.parentNode.insertBefore(node, refNode.nextSibling);
		else
			refNode.parentNode.appendChild(node);

		return refNode;
	}

})();




/* import('/examples/reshu.su/dark.js')
.then(dark=>{
	dark= dark.default
	console.info('dark= ', dark, dark.clickBtn);
})
.catch(err => {
	console.info('err.message= ', err.message);
});
*/

/* (function () {

})() */