<link rel="stylesheet" href="tchart.css">
<style>
#response {
	min-width: 50%;
	min-height: 100px;
	border: inset 2px red;
	white-space: pre-line;
	word-break: break-all;
}
#preloader {
	position: absolute;
	left: 50%;
	top: 50%;
	width: 48px;
	height: 48px;
	margin-left: -24px;
	margin-top: -24px;
	border: #31a9df 3px solid;
	border-left: none;
	border-radius: 100%;
}
.rotate {
  animation-name: rotating;
  animation-duration: 1s;
  animation-iteration-count: infinite;
  animation-timing-function: linear;
}
@keyframes rotating {
  from {
	transform:rotate(0deg);
  }
  to {
	transform:rotate(360deg);
  }
}
#chartbox{
	display:flex;
	flex-wrap: wrap;
}
.tchart {
	box-sizing: border-box;
	position: relative;
	margin-left: 0;
	margin-right: 0;
	margin-bottom: 64px;
	width: 100%;
	float: left;
}
.tchart canvas{
	height: 300px;
}
</style>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.1/axios.min.js"></script> -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script src="tchart.js"></script>
<!-- <script src="tchart.min.js" type="application/javascript" defer></script> -->

<div id="preloader" class="rotate"></div>


<form action="http://51.15.11.82:8225/api/v1/" method="get">
<!-- <button data-method="get_user_data" data-params="user=303986717">get_user_data</button> -->
</form>

<h3>Ответ сервера</h3>
<pre id="response"></pre>

<h3>User Data</h3>
<div id="data"></div>

<div id="chartbox">
<div class="tchart"></div>
<div class="tchart"></div>
<div class="tchart full"></div>
</div>


<script type="text/javascript">
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

var $charts = $('.tchart');
requestData($charts[0], 'test');
// requestData($charts[1], 'get_bot_stats');

function requestData ($tchart, method) {
	method = method || "get_user_data";
	var $form= $('form'),
		url,
		$responseNode = $('#response'),
		test = method === 'test';

	url = test ? 'responseData.json' : $form[0].action + method + location.search;

	console.log("$form= ", $form);
	// e.preventDefault();

	$.ajax({
		method: $form[0].method,
		url: url,
	})
	.done(response => {
		console.log("response", response);
		$('#preloader').remove();
		$responseNode.text(JSON.stringify(response));
		fillData(response, $tchart);
	})
	.fail(error => {
		$('#preloader').css({borderColor: 'red'});
		console.log("error", error);
		$responseNode.text(error.message);
	});

	return;
 }

 function fillData(response, $tchart) {
	var $dataNode = $('#data');
	for(var i in response) {
		var str = '<p>' + i + ' = ' + response[i] + '</p>';
		$dataNode.append(str);
	}

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

var lightTheme = true;
var charts = [];

	var chart = new TChart($tchart);
			chart.setColors(DARK_COLORS);
			chart.setData(response[0]);
}
 </script>



<?php
/**
 * Полезные ссылки:
 ** http://51.15.11.82:8225/api/doc
 ** https://coding.studio/tchart/
*/

/* [
	[
		array unix time
		// даты/время по Х в секундах
	],
	[
		array values
		// Ось игрек
	]
] */
// $data = file_get_contents('');