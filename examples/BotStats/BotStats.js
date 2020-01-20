'use strict';

var _GET = location.search.replace( '?', '')
	.split('&')
	.reduce(
		(p,e) => {
			var a = e.split('=');
			p[ decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
			return p;
		}, {}
	);

// requestData('test');
requestData('get_bot_stats');


function requestData (method) {
	method = method || "get_bot_stats";
	var $form= $('form'),
		url,
		$responseNode = $('#response'),
		test = method === 'test';

	url = test ? 'responseData.json' : $form[0].action + method + location.search;

	console.log("$form= ", $form, method, test, url);
	// e.preventDefault();

	$.ajax({
		method: $form[0].method,
		url: url,
	})
	.done(response => {
		$('#preloader').remove();
		$responseNode.text(JSON.stringify(response));

		console.log("response = ", response);
		var ords = Object.keys(response.stats).filter(i=>i!=='dates');

		ords.forEach(k => {
			console.log("arr[k], k", response.stats[k], k);
			fillData([response.stats['dates'], response.stats[k]], k);
		});
		// fillData(response, $tchart);
	})
	.fail(error => {
		$('#preloader').css({borderColor: 'red'});
		console.log("error", error);
		$responseNode.text(error.message);
	});

	return;
}



function fillData(response, name) {
	response = parseInputData(response, name);

	var $chartbox = $('<div class="tchart"/>').appendTo('#chartbox');

	$chartbox[0].name = name;
	console.log("parseInputData = ", response, "$chartbox = ", $chartbox);

	var LIGHT_COLORS = {
		circleFill: '#ffffff',
		line: '#f2f4f5',
		zeroLine: '#ecf0f3',
		selectLine: '#dfe6eb',
		text: '#96a2aa',
		preview: '#eef2f5',
		previewAlpha: 0.8,
		previewBorder: '#b6cfe1',
		previewBorderAlpha: 0.5
	};

	var DARK_COLORS = {
		circleFill: '#242f3e',
		line: '#293544',
		zeroLine: '#313d4d',
		selectLine: '#3b4a5a',
		text: '#546778',
		preview: '#152435',
		previewAlpha: 0.8,
		previewBorder: '#5a7e9f',
		previewBorderAlpha: 0.5
	};

	var CHART_NAMES = {
			msgs: 'Статистика сообщений',
			chats: 'Статистика чатов',
			users: 'Статистика пользователей',
		},
		chart = new TChart($chartbox[0]),
		chartboxName = CHART_NAMES[name] || 'ХЗ';
	chart.setColors(DARK_COLORS);
	chart.setData(response[0]);

	$chartbox.wrap('<div/>');
	$chartbox.before('<h3>' + chartboxName + '</h3>');
}


function parseInputData (json, name) {
	var addFields= ["x", "y0"];

	//* Выкидываем тест
	if(json[0].columns)
		return json;

	json = json.map((i, ind) => {
		if(ind === 0) i = i.map(i=>i*1000);
		return [addFields[ind]].concat(i);
	});

	return [{
		columns: json,
		types: {
			"y0": "line",
			"x": "x"
		},
		names: {
			"y0": name
		},
		colors: {
			"y0": "#5544EE"
		}
	}];


	/* var stats= json[key],
		out= [{
			columns: [
				["x"], ["y0"]
			],
			types: {
				"y0": "line",
				"x": "x"
			},
			names: {
				"y0": key
			},
			colors: {
				"y0": "#5544EE"
			}
		}],
		absc = out[0].columns[0],
		ord = out[0].columns[1];
	if(!(stats instanceof Object))
		return json;

	Object.keys(stats).forEach((k, ind) => {
		absc.push(Date.parse(k));
		ord.push(stats[k]);
	});

	return out; */
}

$(() => {

});