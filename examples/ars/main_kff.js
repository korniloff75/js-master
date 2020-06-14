'use strict';

var start_time = Date.now();

(()=>{
	let styles= document.createElement('link');
	styles.rel= 'stylesheet';
	styles.href= location.href + 'assets/tchart/tchart.css';
	document.head.append(styles);
})();


var mod_my_chart_promise = import(location.href + 'mod_my_chart.js')
.then(my_chart => {
	// console.log('my_chart= ', my_chart);

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

var mod_konva_promise = import(location.href + 'mod_konva.js')
.then(konva => {
	console.log(
		'konva= ', konva,
		// init
	);
})
.catch(err => {
	console.warn('konva.err.message= ', err.message);
});

Promise.all([mod_my_chart_promise, mod_konva_promise])
.then(
	all=>{
		console.info('Время исполнения KFF= ', `${Date.now() - start_time} ms`);
	}
)