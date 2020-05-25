'use strict';
// console.info('mod_konva runing!');

var ABS= _angles.abs= _angles.abs.Moon;

var stage = new Konva.Stage({
	container: 'konva_container',  // индификатор div контейнера
	get width() {
		// return 500;
		return Math.floor(parseInt(getComputedStyle(document.querySelector(`#${this.container}`)).width)) * .9;
	},
	// width: '90%',
	height: 500
});


// *define ABS.arr
Object.defineProperty(ABS, 'arr', {value:[], enumerable:0, writable:1,configurable:1});

Object.keys(ABS).forEach(i=>{
	// *i - abs angle
	// *sec -> ms
	var curTS = ABS[i][0].exact * 1000,
		date = new Date(curTS);

	ABS.arr.push({
		a: i,
		ts: curTS,
		date: date,
		// strDate: getStrDate(date),
		strDate: `${fixZero(date.getDate())}.${fixZero(date.getMonth() + 1)}`,
		strTime: getStrTime(date),
	});
});

// *Сортируем по времени
ABS.arr = ABS.arr.sort((a, b) => a.ts - b.ts);

/**
 *
 * @param {Date} date - ms
 */
function getStrDate (date) {
	return `${fixZero(date.getDate())}.${fixZero(date.getMonth() + 1)}`;
}

/**
 * @param {Date} date - ms
 * минуты округляем по секундам
 */
function getStrTime (date) {
	var hour = date.getHours(),
		min = date.getMinutes(),
		sec = date.getSeconds();
	sec > 30 && min++;
	min === 60 && (min = 0, hour++);

	return `${fixZero(hour)}:${fixZero(min)}`;
}


function fixZero (n) {
	return n<10 ? `0${n}`: n;
}


// *Calc time range

Object.defineProperties(ABS, {
	timeRange: {
		get() {
			return this.last.ts - this.first.ts;
		}
	},
	first: {
		value: ABS.arr[0],
	},
	last: {
		value: ABS.arr[ABS.arr.length-1],
	},
});


console.log('ABS= ', ABS, ABS.arr,);


init();

function init () {
	bottomLine({
		height: 50,
	});

	console.log(
		'stage= ', stage,
		stage.content,

	);

}


function bottomLine (sts) {
	// *defaults
	sts = Object.assign({
		height: 50,
	}, sts);

	var
		clrs= ['red', 'green', 'blue'],
		kt_X = stage.width() / ABS.timeRange,
		firstPoint_X = 0;

	console.log(
		'stage.width()= ', stage.width(),
		'ABS.timeRange= ', ABS.timeRange,
	);

	var layer = new Konva.Layer();

	ABS.arr.forEach((i,ind)=>{
		var next= ABS.arr[ind+1];
		if(!next) return;

		var d_X = (next.ts - ABS.first.ts) * kt_X - firstPoint_X,
		lineSts = {
			// name: 'bottomLine',
			x: firstPoint_X,
			y: stage.height(),
			width: d_X,
			height: -sts.height,
			fill: clrs[ind % clrs.length],
			stroke: 'black',
			strokeWidth: 1,
		},

		line = new Konva.Rect(lineSts);

		/* console.log(
			'next.ts= ', next.ts,
			'ABS.first.ts= ', ABS.first.ts,
			'firstPoint_X= ', firstPoint_X,
			'kt_X= ', kt_X,
			'(next.ts - ABS.first.ts) * kt_X= ', (next.ts - ABS.first.ts) * kt_X,
			'd_X= ', d_X,
		); */

		var dateRuler = setDateRuler(new Date(i.ts), kt_X);
		layer.add(dateRuler);
		dateRuler.zIndex(0);

		var txt = new Konva.Text({
			x: firstPoint_X + 5,
			// x: firstPoint_X + d_X / 2,
			y: stage.height() - sts.height,
			text: `${i.strDate}\n${i.strTime}\n${i.a}°`,
			// align: 'center',
			fontSize: 16,
			fontFamily: 'Calibri',
			fill: 'black',
		});

		firstPoint_X += d_X;

		// console.log(kt_X, i.ts, d_X);

		layer.add(line);
		layer.add(txt);

		//
		// var txtDate = document.createTextNode(`${i.strDate} -- lineSts.x= ${lineSts.x}, lineSts.width= ${lineSts.width}, d_X= ${d_X}` + ' | ');
		var txtDate = document.createTextNode(`${i.date} -- date = ${i.strDate} -- time  = ${i.strTime} ||| `);
		stage.attrs.container.append(txtDate);
	});

	Sun:
	{
		var circle = new Konva.Circle({
			x: stage.width() / 2,
			get y() {return stage.height() + this.radius},
			radius: 70,
			fill: 'red',
			stroke: 'black',
			strokeWidth: 4,
			draggable: true,
			// zIndex: 0
		});


		layer.add(circle);
		circle.zIndex(0);

		(new Konva.Tween({
			node: circle,
			duration: 3,
			y: circle.attrs.radius,
			radius: circle.attrs.radius / 2,
			// rotation: Math.PI * 2,
			// opacity: 1,
			strokeWidth: 2
		})).play();
	}

	// console.log('circle= ', circle);


	stage.add(layer);
}


/**
 * @param {Date} date - дата прохождения
 * @param {float} kt_X
 * Линии по датам прохождения Луной контрольных точек
 * на 00.00
 */
function setDateRuler (date, kt_X) {
	var m_of_n = new Date(date.getFullYear(), date.getMonth(), date.getDate()),
	m_of_n_X = (+m_of_n - ABS.first.ts) * kt_X;

	// console.log('m_of_n= ', m_of_n);

	var group = new Konva.Group({
		/* x: 120,
		y: 40,
		rotation: 20, */
	});

	var ruler = new Konva.Line({
		points: [m_of_n_X,0, m_of_n_X,stage.height()],
		stroke: 'gray',
		strokeWidth: 1,
		lineCap: 'round',
		lineJoin: 'round',
	});

	var txt = new Konva.Text({
		x: m_of_n_X + 2,
		// x: firstPoint_X + d_X / 2,
		y: 0,
		text: getStrDate(m_of_n),
		// text: getStrDate(new Date(i.ts)),
		// align: 'center',
		fontSize: 12,
		fontFamily: 'Calibri',
		fill: 'gray',
	});

	group.add(ruler);
	group.add(txt);

	return group;

}


/**
 *
 */
function test() {
	// console.info('mod_konva test runing!');
	// сначала создаём контейнер
	var stage = new Konva.Stage({
		container: 'konva_container',  // индификатор div контейнера
		width: 500,
		height: 500
	});

	// console.log(stage);

	// далее создаём слой
	var layer = new Konva.Layer();

	// создаём фигуру
	var circle = new Konva.Circle({
		get x() {return stage.width() - this.radius},
		get y() {return stage.height() - this.radius},
		radius: 70,
		fill: 'red',
		stroke: 'black',
		strokeWidth: 4
	});

	circle.draggable('true');

	circle.on('mouseout touchend', function() {
		alert('user input');
	});

	circle.on('xChange', function() {
    console.log('position change');
	});

	circle.on('dragend', function() {
    console.log('drag stopped');
	});

	console.log(circle);

	// добавляем круг на слой
	layer.add(circle);

	var triangle = new Konva.Shape({
		sceneFunc: function(context) {
			context.beginPath();
			context.moveTo(20, 50);
			context.lineTo(220, 80);
			context.quadraticCurveTo(150, 100, 260, 170);
			context.closePath();

			// special Konva.js method
			context.fillStrokeShape(this);
		},
		fill: '#00D2FF',
		stroke: 'black',
		strokeWidth: 4
	});

	layer.add(triangle);



	var pentagon = new Konva.RegularPolygon({
    x: stage.getWidth() / 2,
    y: stage.getHeight() / 2,
    sides: 5,
    radius: 70,
    fill: 'red',
    stroke: 'black',
    strokeWidth: 4,
    shadowOffsetX : 20,
    shadowOffsetY : 25,
    shadowBlur : 40,
    opacity : 0.5
	});

	// pentagon.draw();
	layer.add(pentagon);

	// добавляем слой
	stage.add(layer);

	var tween = new Konva.Tween({
		node: pentagon,
		duration: 1,
		x: 140,
		rotation: Math.PI * 2,
		opacity: 1,
		strokeWidth: 8
	});
	tween.play();
}