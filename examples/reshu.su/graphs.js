'use strict';

// export var Styles= {
export default {
	opts: {},
	graphs: null,
	items: [],
	canvases: [],
	collectItems: function(graphs) {
		this.graphs= graphs;
		[].forEach.call(graphs, i=>{
			var expr= i.textContent.split('=').map(expr=>expr.trim());
			this.items.push(expr);
			this.drawCanvas(i, expr[expr.length-1]);
		});

		// this.renderCanvases(i);
	},

	parseExpression: function() {

	},

	drawCanvas: function(node, expr) {
		var cBlock= document.createElement('div'),
			cEl= document.createElement('canvas');

		cBlock.appendChild(cEl);
		this.canvases.push(cBlock);
		this.evalCanvas(cEl, expr);
		insertAfter(cBlock, node);
	},

	evalCanvas: function(cEl, expr) {
		var ctx= cEl.getContext('2d'),
			range= [-10,10];

		Object.keys(this.operations).forEach(i=>{
			if(!expr.includes(i)) return;

			var ev= expr.split(i);
			expr= `${this.operations[i]}(${ev.join(',')})`;
		});

		console.log(expr);

		// ctx.beginPath();

		// axis
		ctx.moveTo(-cEl.width/2, cEl.height/2);
		ctx.lineTo(cEl.width, cEl.height/2);
		ctx.moveTo(cEl.width/2, cEl.height);
		ctx.lineTo(cEl.width/2, 0);
		ctx.stroke();

		ctx.beginPath();

		// ctx.transform(1, 0, 0, -1, 0, cEl.height);
		ctx.transform(3, 0, 0, -1, cEl.width/2, cEl.height/2);
		eval(`ctx.moveTo(${-range[0]},${ctx.height})`);

		for (let i = range[0]; i <= range[1]; i++) {
			console.log(eval(expr.replace('x', i)));
			eval(`ctx.lineTo(${i},${expr.replace('x', i)})`);
			;
		}

		ctx.stroke();
	},

	renderCanvases: function() {

	},

	operations: {
		'**': 'Math.pow',
	}
}

;(function () {
	console.info('Подключен файл graphs.js');
})()