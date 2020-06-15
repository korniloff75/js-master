'use strict';
// console.info('mod_konva runing!');

var SAVED_STS = JSON.parse(localStorage.getItem('KonvaSTS')),
	STS= SAVED_STS || {
		width: 1700,
		bottomLine: {
			height: 50,
			clrs: ['red', 'green', 'blue'],
			clrs_A: {
				0: 'blue',
				60: 'green',
				90: 'red',
				120: 'green',
				180: 'red',
				abs: 'gray',
			},
			imgs: {
				0: '0 conjunct.svg',
				60: '60 sextile.svg',
				90: '90 square.svg',
				120: '120 trine.svg',
				180: '180 opposition.svg',
			}
		},
		gradient: {
			delta: {
				Sun: {
					0: 12,
					60: 6,
					90: 10,
					120: 12,
					180: 12,
				},
				Moon: {
					0: 10,
					60: 6,
					90: 10,
					120: 8,
					180: 10,
				},
				Mercury: {
					0: 5,
					60: 55,
					90: 5,
					120: 5,
					180: 5,
				},
				Venus: {
					0: 5,
					60: 5,
					90: 5,
					120: 5,
					180: 5,
				},
				Mars: {
					0: 5,
					60: 5,
					90: 5,
					120: 5,
					180: 5,
				},
				Jupiter: {
					0: 5,
					60: 5,
					90: 5,
					120: 5,
					180: 5,
				},
				Saturn: {
					0: 5,
					60: 5,
					90: 5,
					120: 5,
					180: 5,
				},
				Uranus: {
					0: 5,
					60: 5,
					90: 5,
					120: 5,
					180: 5,
				},
				Neptune: {
					0: 5,
					60: 5,
					90: 5,
					120: 5,
					180: 5,
				},
				Pluto: {
					0: 5,
					60: 5,
					90: 5,
					120: 5,
					180: 5,
				},
			},
			delta_: 50,
			hasPlusDelta: 1,
			fraction: 20,
			get height() {
				return Math.floor(STAGE.main.height() / TSS_KEYS.length)
			}
		}
	},
	CONTAINER= document.querySelector('#konva_container'),
	// *from server
	ABS= _angles.abs= _angles.abs.Moon,
	REL= _angles.rel,
	TSS_KEYS = Object.keys(_tss).sort((a, b) => a - b),
	STAGE = new Konva.Stage({
		container: CONTAINER.id,  // индификатор div контейнера
		width: STS.width,
		height: 500
	}),
	LAYERS = {
		bottomLine: new Konva.Layer(),
		gradient: new Konva.Layer(),
	},
	LINES = [],
	GROUPS = [],
	IMGS = {
		planet:{},
		asp:{},
	};

CONTAINER.style.width = `${STS.width}px`;

Object.defineProperty(STAGE, 'main', {
	value: {
		height: ()=> STAGE.height() - STS.bottomLine.height
	},
	configurable:1
});

console.log(
	'STAGE.main.height()= ', STAGE.main.height(),
);

var
	// *Временной интервал, мс
	TIME_RANGE = (TSS_KEYS[TSS_KEYS.length-1] - TSS_KEYS[0]) * 1000,
	// *К-т длины к ts
	Kt_X = STAGE.width() / TIME_RANGE;





// *Сортируем по времени
// ABS.arr = ABS.arr.sort((a, b) => a.ts - b.ts);

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
	if(!(date instanceof Date)) {
		date = new Date(date);
	}
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

/* Object.defineProperties(ABS, {
	TIME_RANGE: {
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
}); */


console.log(
	// 'ABS= ', ABS, ABS.arr,
	// 'REL= ', REL, REL.arr,
	'TSS= ', _tss
);


/**
 * init
 */
init();

function init () {
	// redraw();

	// bottomLine();
	bottomLine_2();
	addControls();

	// ?
	// STAGE.draw();

	/* console.log(
		'STAGE= ', STAGE,
		STAGE.content,
	); */

}


function redraw() {
	/* STAGE = STAGE || new Konva.Stage({
		container: CONTAINER.id,  // индификатор div контейнера
		get width() {
			return Math.floor(parseInt(getComputedStyle(CONTAINER).width) * 1);
		},
		height: 500
	}); */

	/* Object.defineProperty(STAGE, 'main', {
		value: {
			height: ()=> STAGE.height() - STS.bottomLine.height
		},
		configurable:1
	});

	console.log(
		'STAGE.main.height()= ', STAGE.main.height(),
	);

	Kt_X = STAGE.width() / TIME_RANGE; */

	LAYERS.gradient.destroy();
	// STAGE.remove(LAYERS.gradient);
}


// note develop
function bottomLine () {
	var sts= STS.bottomLine,
		firstPoint_X = 0;

	console.log(
		'STAGE.width()= ', STAGE.width(),
		'ABS.TIME_RANGE= ', ABS.TIME_RANGE,
	);

	var layer = new Konva.Layer();

	ABS.arr.forEach((i,ind)=>{
		var next= ABS.arr[ind+1];
		if(!next) return;

		var d_X = (next.ts - ABS.first.ts) * Kt_X - firstPoint_X,
		lineSts = {
			// name: 'bottomLine',
			x: firstPoint_X,
			y: STAGE.height(),
			width: d_X,
			height: -sts.height,
			// fill: sts.clrs[ind % sts.clrs.length],
			fill: 'gray',
			stroke: 'black',
			strokeWidth: 1,
		},

		line = new Konva.Rect(lineSts);

		/* console.log(
			'next.ts= ', next.ts,
			'ABS.first.ts= ', ABS.first.ts,
			'firstPoint_X= ', firstPoint_X,
			'Kt_X= ', Kt_X,
			'(next.ts - ABS.first.ts) * Kt_X= ', (next.ts - ABS.first.ts) * Kt_X,
			'd_X= ', d_X,
		); */

		var txt = new Konva.Text({
			x: firstPoint_X + 5,
			// x: firstPoint_X + d_X / 2,
			y: STAGE.main.height(),
			text: `${i.strDate}\n${i.strTime}\n${i.a}°`,
			// align: 'center',
			fontSize: 16,
			fontFamily: 'Calibri',
			fill: 'black',
		});

		firstPoint_X += d_X;

		// console.log(Kt_X, i.ts, d_X);

		layer.add(line);
		layer.add(txt);

		//
		// var txtDate = document.createTextNode(`${i.date} -- date = ${i.strDate} -- time  = ${i.strTime} ||| `);
		// STAGE.attrs.container.parentNode.append(txtDate);
	});

	Sun:
	{
		var circle = new Konva.Circle({
			x: STAGE.width() / 2,
			get y() {return STAGE.height() + this.radius},
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


	STAGE.add(layer);
}


function bottomLine_2 () {
	var sts= STS.bottomLine,
		firstPoint_X = 0,
		// *Подъём текста
		maxWidth = 1,
		pre = document.createElement('pre'),
		popUp = document.createElement('div');

	popUp.style.position = 'absolute';
	popUp.style.width = '70px';
	popUp.style.height = 'auto';
	popUp.style.background = '#fff';
	popUp.style.zIndex = 100;
	popUp.hidden = 1;

	TSS_KEYS.forEach((i,ind)=>{
		// *i - php ts
		// *sec -> ms
		_tss[i].exact = i*1000;

		var cur = _tss[i],
			next= _tss[TSS_KEYS[ind+1]];

		if(!next) return;

		next.exact = TSS_KEYS[ind+1] * 1000;

		var d_X = (next.exact - _tss[TSS_KEYS[0]].exact) * Kt_X - firstPoint_X,
		abs = next.cat && (next.cat === 'abs'),
		lineSts = {
			// name: 'bottomLine',
			x: firstPoint_X,
			y: STAGE.height(),
			width: d_X,
			height: -sts.height,
			// fill: abs? 'gray': sts.clrs[ind % sts.clrs.length],
			fill: abs? sts.clrs_A.abs: sts.clrs_A[next.a],
			stroke: 'black',
			strokeWidth: 1,
		},

		line = new Konva.Rect(lineSts);

		line.cur = cur;
		line.next = next;
		line.d_X = d_X;


		// *cur Hover
		line.on('mouseenter', e=>{
			var t = e.target;

			popUp.hidden = 0;
			popUp.style.width = '70px';

			popUp.style.left = t.x() + 'px';
			// popUp.textContent = t.cur.date;
			popUp.textContent = `${getStrTime(t.cur.exact)} - ${getStrTime(t.next.exact)}`;

			popUp.style.top = STAGE.main.height() - parseInt(getComputedStyle(popUp).height) + 'px';

			/* console.log(
				t.next,
				'popUp= ', popUp,
			); */
		});

		line.on('mouseleave', e=>{
			popUp.hidden = 1;
		});

		line.on('click', e=>{
			var t = e.target;

			popUp.hidden = 0;
			popUp.style.width = '100px';

			var gcs_popUp = getComputedStyle(popUp);

			popUp.style.left = t.x() + t.width() - parseInt(gcs_popUp.width) + 'px';

			popUp.textContent = `${next.pl} ${next.a}°\n ${next.date} = ${next.deg} \n`;

			popUp.style.top = STAGE.main.height() - parseInt(gcs_popUp.height) + 'px';

			console.log(
				t.next,
				'popUp= ', popUp,
				'getComputedStyle(popUp).height= ', getComputedStyle(popUp).height,
			);
		});

		LINES[ind]= line;

		/* console.log(
			'next.ts= ', next.ts,
			'ABS.first.ts= ', ABS.first.ts,
			'firstPoint_X= ', firstPoint_X,
			'Kt_X= ', Kt_X,
			'(next.ts - ABS.first.ts) * Kt_X= ', (next.ts - ABS.first.ts) * Kt_X,
			'd_X= ', d_X,
		); */

		var dateRuler = setDateRuler(new Date(_tss[i].exact), Kt_X);
		LAYERS.bottomLine.add(dateRuler);
		dateRuler.zIndex(0);

		var date = new Date(next.exact),
			txt = new Konva.Text({
				// x: firstPoint_X + d_X,
				x: firstPoint_X + d_X,
				y: STAGE.main.height(),
				text: `${next.pl} - ${next.a}°\n${getStrTime(date)}`,
				// width: -50,
				// align: 'right',
				fontSize: 16,
				fontFamily: 'Calibri',
				fill: abs?'#551':'black',
			});

		// *Images

		loadImgs(line)
		.then(imgs=>{
			var imgsWidth = 0;
			// *Группа изображений
			var group = new Konva.Group({
				// x: line.x() + line.width(),
				// x: line.x(),
				y: STAGE.main.height(),
			});

			imgs.forEach((i,ind)=>{
				// *Отступ в группе
				i.offset = ind? imgs[0].width(): 0,
				imgsWidth += i.width();
				group.add(i);
			});

			group.setAttrs({
				x: line.x() + line.width() - imgsWidth,
				width: imgsWidth,
				height: Math.max(imgs[0].height(), imgs[1]&&imgs[1].height() || 0),
			});

			if(group.children.length === 1) {
				/* console.log(
					'group= ', group,
					'imgsWidth= ', imgsWidth,
				); */
			}

			GROUPS[ind]= group;

			// *Last async iteration
			if(
				GROUPS.filter(i=>!!i).length === TSS_KEYS.length - 2
			)
			{
				drawOutLines();
			}

			/* console.log(
				'groups.length= ', groups.length,
				// 'groups.filter(i=>i!=0).length= ', groups.filter(i=>!!i).length,
				// 'TSS_KEYS.length= ', TSS_KEYS.length,
			); */

		}); //* loadImgs

		// *Выноски контроль
		// note develop
		if(txt.textWidth * 1.05 > d_X)
		{
			var d_Y = STAGE.height() - sts.height * (++maxWidth);

			d_Y < sts.height * 2 && (maxWidth = 1);

			txt.setAttrs({
				y: d_Y
			});

		} else {
			maxWidth = 1;
		}

		txt.setAttrs({
			x: firstPoint_X + d_X - txt.textWidth - 3
		});

		firstPoint_X += d_X;

		// console.log('txt= ', txt);

		LAYERS.bottomLine.add(line);
		// LAYERS.bottomLine.add(txt);

		// txt.zIndex(10+ind);

		txt.align('right');

		//
		// var txtDate = document.createTextNode(`${i.strDate} -- lineSts.x= ${lineSts.x}, lineSts.width= ${lineSts.width}, d_X= ${d_X}` + ' | ');
		// var txtDate = document.createTextNode(`${cur.date} -- date = ${cur.strDate} -- time  = ${cur.strTime} ||| `);
		var txtDate = document.createTextNode(`${cur.pl} ${cur.a}° = ${cur.date} = ${cur.deg} \n`);
		pre.append(txtDate);
	}); //*TSS_KEYS.forEach

	STAGE.attrs.container.appendChild(popUp);
	document.querySelector('#konva_data').append(pre);
	STAGE.add(LAYERS.bottomLine);
}


/**
 ** Элементы управления
 */
function addControls() {
	// *checkboxDelta
	var checkboxDeltaLabel = document.createElement('label'),
		checkboxDelta = document.createElement('input');

	checkboxDelta.type= 'checkbox';
	checkboxDeltaLabel.textContent= '+Delta ';
	checkboxDeltaLabel.style.cssText= 'position:absolute; left:0; bottom:50%;';

	checkboxDelta.addEventListener('change', e=>{
		STS.gradient.hasPlusDelta = e.target.checked;
		saveSTS();
		// redraw();
		LAYERS.gradient.destroy();
		drawOutLines();
		// ?
		// STAGE.draw();
		// LAYERS.gradient.draw();
	});
	checkboxDelta.checked = STS.gradient.hasPlusDelta;
	checkboxDeltaLabel.append(checkboxDelta);
	CONTAINER.append(checkboxDeltaLabel);
	// document.querySelector('#konva_data').append(checkboxDeltaLabel);
}

/**
 * Сохранение пользовательских настроек
 */
function saveSTS () {
	localStorage.setItem('KonvaSTS', JSON.stringify(STS));
}


/**
 **Выносные линии с изображениями
 * Запускать после заполнения:
 * {Array} GROUPS - группы изображений, собираются асинхронно
 * {Array} LINES - заполнения нижней полосы, собираются асинхронно
 */
function drawOutLines() {
	var sts= STS.bottomLine,
		level= 1;

	/* console.log(
		'IMGS= ', IMGS,
	); */

	GROUPS.forEach((group,ind)=>{
		var line= LINES[ind];

		LAYERS.bottomLine.add(group);
		LAYERS.bottomLine.batchDraw();

		LAYERS.gradient.add(
			drawGradients(line.x(), ind)
		);

		/* console.log(
			'level= ', level,
			'group= ', group,
		); */

		// !stop
		if(group.width() * 1.05 <= line.width()) {
			level=1;
			return;
		}

		var d_Y = STAGE.height() - sts.height * (++level);

		group.setAttrs({
			// x: group.x() + line.width() - group.width(),
			y: d_Y,
		});

		group.y() < sts.height * 2 && (level = 1);

		// *Выноски
		var outLine = new Konva.Line({
			points: [line.x() + line.width(),STAGE.height(), line.x() + line.width(),group.y() + group.height(), group.x(),group.y() + group.height()],
			stroke: '#aaa',
			strokeWidth: 1,
			lineCap: 'round',
			lineJoin: 'round',
		});

		LAYERS.bottomLine.add(outLine);
		outLine.zIndex(0);

	});

	STAGE.add(LAYERS.gradient);
	LAYERS.gradient.zIndex(0);

}


/**
 ** Загружаем асинхронно изображения в bottomLine
 * @param {Konva.Rect} line - текущий элемент
 */
function loadImgs(line) {
	var sts = STS.bottomLine,
		name = line.next.pl,
		a = line.next.a,
		is_moon = name === 'Moon',
		out = [];

	var p1= is_moon ? null: new Promise((resolve, reject) => {
		var img = new Image();

		img.src = './img/aspects/' + sts.imgs[a];
		// img.src = './img/aspects/0 conjunct.svg';

		img.onload= ()=>{
			var kImg = new Konva.Image({
				x: 0,
				y: 0,
				image: img,
				width: 20,
				height: 20,
			});

			IMGS.asp[a] = IMGS.asp[a] || img;

			resolve(kImg);
		}
	});

	p1&&out.push(p1);

	var p2= new Promise((resolve, reject) => {
		var img = new Image();

		img.src = './img/planets/' + (is_moon ? 'moonVC': name.toLowerCase()) + '.png';
		// img.src = './img/aspects/0 conjunct.svg';

		img.onload= ()=>{
			var kImg = new Konva.Image({
				x: p1? 20: 0,
				y: 0,
				image: img,
				width: 20,
				height: 20,
			});

			IMGS.planet[name] = IMGS.planet[name] || img;

			resolve(kImg);
		}
	});

	out.push(p2);

	return Promise.all(out);
}


/**
 * Отрисовка градиентов
 * @param {float} x - coord
 * @param {int} ind - index in array
 * returns {Konva.Rect}
 */
function drawGradients (x, ind) {
	var sts = STS.gradient,
		// delta = sts.delta[name][0],
		delta = 50,
		// delta = sts.delta_,
		grad = new Konva.Rect({
			x: x - delta,
			y: sts.height * ind,
			width: sts.hasPlusDelta? delta * 2 : delta,
			height: sts.height,
			fillLinearGradientStartPoint: { x: 0, y: 0 },
			fillLinearGradientEndPoint: { x: delta * 2, y: 0 },
			fillLinearGradientColorStops: [0, '#f553', .5, '#f55f', 1, '#f553'],
		}),
		img = null;
		/* grad = new Konva.Rect({
		x: x - delta,
		y: sts.height * ind,
		width: sts.hasPlusDelta? delta * 2 : delta,
		height: sts.height,
		fillRadialGradientStartPoint: { x: delta, y: sts.height/2 },
		fillRadialGradientStartRadius: sts.height/4,
		fillRadialGradientEndPoint: { x: delta, y: sts.height/2 },
		fillRadialGradientEndRadius: delta,
		fillRadialGradientColorStops: [0, '#f55f', .3, '#f555', .9, '#f551'],
		// fillRadialGradientColorStops: [0, 'red', 1, 'transparent'],
	}); */

	return grad;
}


/**
 * @param {Date} date - дата прохождения
 * @param {float} Kt_X
 * Линии по датам прохождения Луной контрольных точек
 * на 00:00
 */
function setDateRuler (date, Kt_X) {
	var m_of_n = new Date(date.getFullYear(), date.getMonth(), date.getDate()),
	m_of_n_X = (+m_of_n - _tss[TSS_KEYS[0]].exact) * Kt_X;

	// console.log('m_of_n= ', m_of_n);

	var group = new Konva.Group({
		/* x: 120,
		y: 40,
		rotation: 20, */
	});

	var ruler = new Konva.Line({
		points: [m_of_n_X,0, m_of_n_X,STAGE.height()],
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
	var STAGE = new Konva.Stage({
		container: 'konva_container',  // индификатор div контейнера
		width: 500,
		height: 500
	});

	// console.log(STAGE);

	// далее создаём слой
	var layer = new Konva.Layer();

	// создаём фигуру
	var circle = new Konva.Circle({
		get x() {return STAGE.width() - this.radius},
		get y() {return STAGE.height() - this.radius},
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
		sceneFunc: function(ctx) {
			ctx.beginPath();
			ctx.moveTo(20, 50);
			ctx.lineTo(220, 80);
			ctx.quadraticCurveTo(150, 100, 260, 170);
			ctx.closePath();

			// special Konva.js method
			ctx.fillStrokeShape(this);
		},
		fill: '#00D2FF',
		stroke: 'black',
		strokeWidth: 4
	});

	layer.add(triangle);



	var pentagon = new Konva.RegularPolygon({
		x: STAGE.getWidth() / 2,
		y: STAGE.getHeight() / 2,
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
	STAGE.add(layer);

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