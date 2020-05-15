'use strict';

(()=>{
	let styles= document.createElement('link');
	styles.rel= 'stylesheet';
	styles.href= location.href + 'assets/tchart/tchart.css';
	document.head.append(styles);
})();


import(location.href + 'mod_my_chart.js')
.then(my_chart => {
	console.log('my_chart= ', my_chart);

	// *Отрисовываем канвас, задаём настройки
	my_chart.createCanvas(null, {
		wrapper: {
			style: 'width:80%;'
		},
		canvas: {
			style: 'width:100%; height:300px;'
		},
	});
})
.catch(err => {
	console.warn('my_chart.err.message= ', err.message);
});