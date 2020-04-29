'use strict';
/**
 ** KorniloFF - https://js-master.ru/examples/reshu.su/
 */
(function() {
	var nav= document.querySelector('aside.book-menu>nav'),
		headers= nav.querySelectorAll('li.book-section-flat'),
		tabs={},
		tabsBlock= document.createElement('div'),
		tabsBlockHidden= document.createElement('ul'),
		oldBlock= nav.querySelector('ul:not([id])'),
		contentBlock= document.createElement('div');

	tabsBlockHidden.hidden= 1;
	tabsBlock.id= 'tabsBlock';

	[].forEach.call(headers, (i,ind)=>{
		var title= i.querySelector('span').textContent,
			// content= i.querySelector('ul'),
			link= document.createElement('li');

		link.content= i.querySelector('ul');
		link.classList.add('tabsitem');
		if(!ind) {
			link.classList.add('active');
			// link.classList.add('book-languages');
			contentBlock.appendChild(link.content);
			tabsBlock.appendChild(link);
		} else {
			tabsBlockHidden.appendChild(link);
		}

		// tabs[title]= link.content;
		link.textContent= title;

	});

	tabsBlock.appendChild(tabsBlockHidden);

	console.log('tabs= ', tabs, oldBlock);

	nav.insertBefore(tabsBlock, oldBlock);
	nav.insertBefore(contentBlock, oldBlock);
	nav.removeChild(oldBlock);

	// *Events on tabsBlock
	tabsBlock.addEventListener('click', changeTabs);
	tabsBlock.addEventListener('mouseover', e=>{
		tabsBlockHidden.hidden= 0;
	});
	tabsBlock.addEventListener('mouseout', e=>{
		tabsBlockHidden.hidden= 1;
	});

	function changeTabs(e) {
		var t= e.target;

		if(!t.classList.contains('tabsitem'))
			return;

		console.log(t, contentBlock.children[0]);

		this.querySelector('.active').classList.remove('active');
		t.classList.add('active');

		tabsBlockHidden.appendChild(tabsBlock.querySelector('li'));
		tabsBlock.insertBefore(t,tabsBlockHidden);
		tabsBlockHidden.hidden= 1;

		// nav.replaceChild(t.content, contentBlock.children[0]);
		contentBlock.innerHTML= '';
		contentBlock.appendChild(t.content);
	}

	// *Scroll to current menu item

	var navItems= contentBlock.querySelectorAll('a');
	[].forEach.call(navItems, i=>{
		// if(location.href !== i.href)
		console.log(i.href, location.pathname, i.href.includes(location.pathname));
		if(!(i.href+'/').includes(location.pathname))
			return;

		var bcr= i.getBoundingClientRect(),
		top= bcr.top + nav.scrollTop - document.body.clientHeight/2;

		nav.scrollTo(0,top);

		console.log('bcr= ', bcr, top, document.body.clientHeight/2, nav.scrollTop);
	});
})()