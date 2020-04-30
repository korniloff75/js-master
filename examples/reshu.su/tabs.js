'use strict';
/**
 ** KorniloFF - https://js-master.ru/examples/reshu.su/
 */
(function() {
	var nav= document.querySelector('aside.book-menu>nav'),
		headers= nav.querySelectorAll('li.book-section-flat'),
		currentHeader= 'Алгебра',
		allItems={},
		tabsBlock= document.createElement('div'),
		tabsBlockHidden= document.createElement('ul'),
		oldBlock= nav.querySelector('ul:not([id])'),
		contentBlock= document.createElement('div');

	tabsBlockHidden.hidden= 1;
	tabsBlock.id= 'tabsBlock';
	contentBlock.id= 'contentBlock';

	[].forEach.call(headers, (i,ind)=>{
		var title= i.querySelector('span').textContent,
			// content= i.querySelector('ul'),
			link= document.createElement('li');

		link.content= i.querySelector('ul');
		link.classList.add('tabsitem');

		var active= i.querySelector(`a[href*='${location.pathname.slice(0,-1)}']`);

		/* if(active) {
			tabsBlock.appendChild(link);
		} else {
			tabsBlockHidden.appendChild(link);
		} */
		(active? tabsBlock: tabsBlockHidden).appendChild(link);

		allItems[title]= link.content;
		link.textContent= title;

	});

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

		// tabsBlock.removeEventListener('mouseover', displayHeaders);

		console.log(t, contentBlock.children[0]);

		/* tabsBlock.querySelector('.active').classList.remove('active');
		t.classList.add('active');

		tabsBlockHidden.appendChild(tabsBlock.querySelector('li'));
		tabsBlock.insertBefore(t,tabsBlockHidden);
		tabsBlockHidden.hidden= 1; */

		setActiveHeader(t);

	}

	/* function displayHeaders () {
		tabsBlockHidden.hidden= 0;
	} */


	// *Define currentHeader

	/* Object.keys(allItems).forEach(h=>{
		// console.log(location.pathname.slice(0,-1));
		var fh= allItems[h].querySelector(`a[href*='${location.pathname.slice(0,-1)}']`);

		if(fh) {
			currentHeader= h;
			console.log('fh= ', fh, currentHeader);
		}
	}); */

	// todo
	// setActiveHeader();


	// *Render

	tabsBlock.appendChild(tabsBlockHidden);

	console.log('allItems= ', allItems, oldBlock);

	nav.insertBefore(tabsBlock, oldBlock);
	nav.insertBefore(contentBlock, oldBlock);
	// note
	nav.removeChild(oldBlock);


	// *Scroll to current menu item

	var curItem= contentBlock.querySelector(`a[href*='${location.pathname.slice(0,-1)}']`);

	if(curItem) {
		var bcrItem= curItem.getBoundingClientRect(),
			bcrBlock= contentBlock.getBoundingClientRect(),

			top= bcrItem.top - bcrBlock.top + contentBlock.scrollTop - (bcrBlock.height + bcrItem.height)/2;
			// top= bcrItem.top + nav.scrollTop - document.body.clientHeight/2;

		curItem.classList.add('active');

		contentBlock.scrollTo(0,top);

		console.log(
			'bcrItem= ', bcrItem, top,
			document.body.clientHeight/2,
			contentBlock.scrollTop,
			'curItem= ', curItem
		);
	}



	// *=================
	// *Helpers

	function setActiveHeader (item) {
		/* if(!item) {
			var cond= tabsBlockHidden.querySelector(`a[href*='${location.pathname.slice(0,-1)}']`);

			item= cond? cond.closest('li'): tabsBlock.querySelector('li');
		} */

		console.log(
			'item= ', item,
			'tabsBlockHidden= ', tabsBlockHidden
		);

		tabsBlock.querySelector('.active') && tabsBlock.querySelector('.active').classList.remove('active');
		item.classList.add('active');

		tabsBlockHidden.appendChild(tabsBlock.querySelector('li'));
		tabsBlock.insertBefore(item,tabsBlockHidden);

		// *Change content
		contentBlock.innerHTML= '';
		contentBlock.appendChild(item.content);
	}
})()