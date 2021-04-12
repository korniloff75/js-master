'use strict';
/**
 ** KorniloFF - https://js-master.ru/examples/reshu.su/
 */
(function() {
	var nav= document.querySelector('aside.book-menu>nav'),
		headers= nav.querySelectorAll('li.book-section-flat'),
		tabs={},
		tabsBlock= document.createElement('div'),
		oldBlock= nav.querySelector('ul:not([id])'),
		contentBlock= document.createElement('div');

	[].forEach.call(headers, (i,ind)=>{
		var title= i.querySelector('span').textContent,
			// content= i.querySelector('ul'),
			link= document.createElement('span');

		link.content= i.querySelector('ul');
		link.classList.add('tabsitem');
		if(!ind) {
			link.classList.add('active');
			// link.classList.add('book-languages');
			contentBlock.appendChild(link.content);
		}

		tabs[title]= link.content;
		link.textContent= title;

		tabsBlock.appendChild(link);
	});

	console.log('tabs= ', tabs, oldBlock);

	nav.insertBefore(tabsBlock, oldBlock);
	nav.insertBefore(contentBlock, oldBlock);
	nav.removeChild(oldBlock);

	// *Events
	tabsBlock.addEventListener('click', changeTabs);

	function changeTabs(e) {
		var t= e.target;

		if(!t.classList.contains('tabsitem'))
			return;

		console.log(t, contentBlock.children[0]);

		this.querySelector('.active').classList.remove('active');
		t.classList.add('active');
		// if()
		// nav.replaceChild(t.content, contentBlock.children[0]);
		contentBlock.innerHTML= '';
		contentBlock.appendChild(t.content);
	}
})()