'use strict';

export var _ch= {
	sts: {}
}

export function createCanvas (pNode, sts) {
	var block= document.createElement('div');
	block.style.display= 'flex';
	block.style.justifyContent= 'center';

	_ch.wrapper= document.createElement('div');

	// _ch.wrapper.className= 'wrapper';
	_ch.wrapper.className= 'tchart';
	_ch.wrapper.style.position= 'relalive';
	// _ch.canvas= document.createElement('canvas');
	// _ch.wrapper.append(_ch.canvas);

	Object.assign(_ch.wrapper, sts.wrapper);
	Object.assign(_ch.sts, sts);

	import(location.href + 'assets/tchart/mod_tchart_kff.js')
	.then(tchart => {
		chartInit(tchart);
	})
	.catch(err => {
		console.warn('tchart.err.message= ', err.message);
	});

	block.append(_ch.wrapper);
	(pNode || document.body).append(block);
}


function setColor (clr) {
	let arrColor= clr.split(',').map(i=>{
		let next= Math.random()*215 + 30;
		return next<=255? next: 11;
	});

	return `rgb(${arrColor.join(',')})`;
}


function chartInit (tchart) {
	let clrs= ["#cb513a","#73c03a","#65b9ac","#4682b4", "#339900", "#FF9900", "#996600", "#990033", "#000099", "#663366", "#99CC00",];
	console.log('tchart= ', tchart, '_ch= ', _ch);

	// *Строим тестовый график
	_ch.tchart= new tchart.TChart(_ch.wrapper);

	let color= "rgb(22,55,77)",
	arrColor= color.split(',').map(i=>parseInt(i));

	// *prepare _json

	Object.assign(_json, {
		colors: {},
		names: {},
		types: {},
	});

	_json.columns= Object.keys(_json.columns).map((i,ind)=>{
		if(isNaN(i)) {
			_json.columns[i].unshift(i);
			_json.colors[i]= clrs[ind]||clrs[ind-clrs.length];
			_json.names[i]= i;
			_json.types[i]= 'line';
			return _json.columns[i];
		}
		else {
			_json.types['x']= 'x';
			// *s->ms
			return _json.columns[i].map(ts=>{
				return isNaN(ts)? ts : ts*1e3;
			});
		}
	});

	console.log('_json.columns= ', _json.columns, _json);


	_ch.tchart.setData(_json);
	/* //* test
		_ch.tchart.setData({
		columns: [
			['x', 1542412800000,1542499200000,1542585600000,1542672000000,1542758400000],
			['y0', 10, 20, 5, 440, 15]
		],
		types: {
			"y0": "line",
			"x": "x"
		},
		names: {
			"y0": 'test'
		},
		colors: {
			"y0": "#5544EE"
		}
	}); */

	Object.assign(_ch.tchart.canvas, _ch.sts.canvas);

}
