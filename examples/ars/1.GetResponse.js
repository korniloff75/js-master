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



function GetResponse (method, params) {
	method = method || "get_bot_stats";

	var url = test ? 'responseData.json' : window._remoteUrl + method,
		$preloader = $('#preloader'),
		$responseNode = $('#response'),
		test = method === 'test';

	// console.log('BotStat.js ', method, test, this.url, params);

	$preloader.show();

	//*
	this.promise = new Promise((resolve, reject)=> {
		$.ajax({
			method: 'post',
			url: url,
			contentType: 'text/plain',
			dataType: "json",
			data: JSON.stringify(params),
			complete: ()=> {
				$preloader.hide();
			},
		})
		.done(response => {
			$preloader.attr('style',null);
			$responseNode.text(JSON.stringify(response));

			this[method] && this[method](response, resolve);

			console.log('this', this);
			resolve(this);

		})
		.fail((jqXHR, status, errorThrown ) => {
			$preloader.css({color: 'red', borderColor: 'red'});
			console.log("jqXHR", jqXHR);
			$responseNode.text(`${status} - ${errorThrown || 'unknown'};
			${JSON.stringify(jqXHR.responseJSON)}
			`);
			reject(this);
		});
	});


	if(method === 'get_bot_stats') {
		Object.assign(this, _g.get_bot_stats);
		this.get_bot_stats = function(response, resolve) {
			if(!response.stats.dates)
			{
				resolve(false);
				// reject('response.stats.dates is missing');
				return;
			}
			this.absc = response.stats.dates.map(i=>i*1000);
			delete response.stats.dates;
			//* Вытаскиваем ординаты графиков
			this.ords = response.stats;

			this.parseInputData = parseInputData;
		}
	}
	else if(method === 'get_bot_settings') {
		this.get_bot_settings = function(response, resolve) {
			if(!response.status || !response.__initialised__)
			{
				resolve(false);
				console.log('response= ', response);
				return;
			}
			delete response.status;
			delete response.__initialised__;

			this.data = response;
		}
	}


	function parseInputData(json, name) {
		var addFields= ["x", "y0"];

		// console.log("parseInputData = ", json, name);

		//* Выкидываем тест
		if(json[0].columns)
			return json;

		json = [
			// response.absc,
			this.absc,
			json
		].map((i, ind) => {
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
	}

} //* GetResponse




