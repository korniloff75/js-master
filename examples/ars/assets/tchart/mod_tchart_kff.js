/**
 * *https://coding.studio/tchart/
 * vendor's guid
 * https://coding.studio/tchart/README.txt
 *
 * var ch = new TChart(container);
 * ch.canvas - динамически созданный canvas
 *
 * @param {Node|jQ} container
 * note container.caption - родительский блок, включающий название чертежа
 * @param {Node|jQ} eventContainer - общий элемент для обработчиков событий
 */

export function TChart(container, eventContainer) {
	'use strict';

	extractMainNodes();

	var MONTH_NAMES = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
		DAY_NAMES = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
		eventBounds = eventContainer.getBoundingClientRect();

	// console.log('eventBounds = ', eventBounds, eventContainer);
	function extractMainNodes() {
		container = extractFromJQ(container);
		eventContainer = extractFromJQ(eventContainer) || container.parentNode;
		container.caption = extractFromJQ(container.caption);

		// console.log('tchart container= ', container,  container.caption);
	}

	function extractFromJQ(obj) {
		return obj ? obj[0] || obj : false;
	}

	function formatDate(time, short) {
		var date = new Date(time),
			s = MONTH_NAMES[date.getMonth()] + ' ' + date.getDate();
		if (short) return s;
		return DAY_NAMES[date.getDay()] + ', ' + s + ' - ' +  date.getHours() + ':' + date.getMinutes();
	}

	function formatNumber(n, short) {
		var abs = Math.abs(n);
		if (abs > 1e9 && short) return (n / 1e9).toFixed(2) + 'B';
		if (abs > 1e6 && short) return (n / 1e6).toFixed(2) + 'M';
		if (abs > 1e3 && short) return (n / 1e3).toFixed(1) + 'K';

		if (abs > 1) {
			var s = abs.toFixed(0),
				formatted = n < 0 ? '-' : '';
			for (var i = 0; i < s.length; i++) {
				formatted += s.charAt(i);
				if ((s.length - 1 - i) % 3 === 0) formatted += ' ';
			}
			return formatted;
		}

		return n && n.toString()
	}

	function createElement(parent, tag, clazz) {
		var element = document.createElement(tag);
		if (clazz) element.classList.add(clazz);
		parent.appendChild(element);
		return element;
	}

	function removeAllChild(parent) {
		while (parent.firstChild) {
			parent.removeChild(parent.firstChild);
		}
	}

	function addEventListener(element, event, listener) {
		element.addEventListener(event, listener, false);
	}

	function removeEventListener(element, event, listener) {
		// console.log('element= ', element);
		element.removeEventListener(event, listener);
	}

	function createAnimation(value, duration) {
		return {
			fromValue: value,
			toValue: value,
			value: value,
			startTime: 0,
			duration: duration,
			delay: 0
		}
	}

	function play(anim, toValue) {
		anim.startTime = time;
		anim.toValue = toValue;
		anim.fromValue = anim.value;
	}

	function updateAnimation(anim) {
		if (anim.value === anim.toValue) return false;
		var progress = ((time - anim.startTime) - anim.delay) / anim.duration;
		if (progress < 0) progress = 0;
		if (progress > 1) progress = 1;
		var ease = -progress * (progress - 2);
		anim.value = anim.fromValue + (anim.toValue - anim.fromValue) * ease;
		return true;
	}

	var canvas = this.canvas = createElement(container, 'canvas'),
		ctx = canvas.getContext('2d');
	var checksContainer = createElement(container, 'div', 'checks'),
		popup = createElement(container, 'div', 'popup');
	popup.style.display = 'none';
	var popupTitle = null;

	/**
	 * defaults
	 ** redefine with this.setColors(colors)
	 */
	var colors = {
		// *фон для цифр
		background: getComputedStyle(container).backgroundColor,
		line: '#293544',
		zeroLine: '#313d4d',
		selectLine: '#3b4a5a',
		text: '#546778',
		preview: '#152435',
		previewAlpha: 0.8,
		previewBorder: '#5a7e9f',
		previewBorderAlpha: 0.5
	};

	if(!colors.background || colors.background === "rgba(0, 0, 0, 0)")
		colors.background = '#fff';

	var data = null,
		xColumn = null,
		columns = null,
		popupColumns = null,
		popupValues = null,

		width = 0,
		height = 0,
		mainHeight = 0,

		textCountX = 6,
		textCountY = 6,

		SCALE_DURATION = 400,
		TEXT_X_FADE_DURATION = 200;

	var pixelRatio = window.devicePixelRatio,
		previewMarginTop = 32 * pixelRatio,
		previewHeight = 38 * pixelRatio,
		mouseArea = 20 * pixelRatio,
		previewUiW = 4 * pixelRatio,
		previewUiH = 1 * pixelRatio,
		lineWidth = 1 * pixelRatio,
		previewLineWidth = 1 * pixelRatio,
		mainLineWidth = 2 * pixelRatio,
		circleRadius = 3 * pixelRatio,
		circleLineWidth = 3 * pixelRatio,
		font = (10 * pixelRatio) + 'px Arial',
		textYMargin = -6 * pixelRatio,
		textXMargin = 16 * pixelRatio,
		textXWidth = 30 * pixelRatio,
		textYHeight = 45 * pixelRatio,
		mainPaddingTop = 21 * pixelRatio,
		paddingHor = 11 * pixelRatio,
		popupLeftMargin = -5;
	// var popupLeftMargin = -25;
	var popupTopMargin = !('ontouchstart' in window) ? 0 : 10;
	// var popupTopMargin = !('ontouchstart' in window) ? 8 : 40;

	var intervalX = 0,
		forceMinY = 0;

	var mainMinX = 0,
		mainMinY = 0;
	var mainMaxX = 0,
		mainMaxY = 0;
	var mainRangeX = 0,
		mainRangeY = createAnimation(0, SCALE_DURATION);
	var mainScaleX = 1,
		mainScaleY = 1;
	var mainOffsetX = 0,
		mainOffsetY = 0;

	var mainMinI = 0,
		mainMaxI = 0;

	var previewMinX = 0,
		previewMinY = 0;
	var previewMaxX = 0,
		previewMaxY = 0;
	var previewRangeX = 0,
		previewRangeY = createAnimation(0, SCALE_DURATION);
	var previewScaleX = 1,
		previewScaleY = 1;
	var previewOffsetX = 0,
		previewOffsetY = 0;

	var selectX = 0,
		selectY = 0;
	var selectI = 0,

		oldTextX = {delta: 1, alpha: createAnimation(0, TEXT_X_FADE_DURATION)};
	var newTextX = {delta: 1, alpha: createAnimation(0, TEXT_X_FADE_DURATION)},
		oldTextY = {delta: 1, alpha: createAnimation(0, SCALE_DURATION)};
	var newTextY = {delta: 1, alpha: createAnimation(0, SCALE_DURATION)},

		needRedrawMain = true;
	var needRedrawPreview = true,

		canvasBounds = {left: 0, top: 0};

	var mouseX = 0,
		mouseY = 0;
	var newMouseX = 0,
		newMouseY = 0;
	var mouseStartX = 0,
		mouseRange = 0;
	var previewUiMin = 0,
		previewUiMax = 0;

	var time = 0,
		NONE = 0;

	var DRAG_START = 1,
		DRAG_END = 2,
		DRAG_ALL = 3,

		mouseMode = NONE;

	function onMouseDown(e) {
		// e.stopPropagation();

		// e.currentTarget.clicked = 1;
		newMouseX = mouseX = (e.clientX - canvasBounds.left) * pixelRatio;
		newMouseY = mouseY = (e.clientY - canvasBounds.top) * pixelRatio;

		var inPreview = (mouseY > height - previewHeight) && (mouseY < height) && (mouseX > -mouseArea) && (mouseX < width + mouseArea);
		if (inPreview) {
			if (mouseX > previewUiMin - mouseArea && mouseX < previewUiMin + mouseArea / 2) {
				mouseMode = DRAG_START;
			} else if (mouseX > previewUiMax - mouseArea / 2 && mouseX < previewUiMax + mouseArea) {
				mouseMode = DRAG_END;
			} else if (mouseX > previewUiMin + mouseArea / 2 && mouseX < previewUiMax - mouseArea / 2) {
				mouseMode = DRAG_ALL;

				mouseStartX = previewUiMin - mouseX;
				mouseRange = mainMaxX - mainMinX;
			}
		}
	}

	function onTouchDown(e) {
		onMouseDown(e.touches[0])
	}

	function onMouseMove(e) {
		// e.stopPropagation();
		// e.preventDefault();

		//* Fix outside click
		if (Math.sign(e.clientX - eventBounds.left) < 0) {
			// console.log('eventBounds.left = ', eventBounds.left, e.clientX);
			onMouseUp(e);
			return;
		}

		newMouseX = (e.clientX - canvasBounds.left) * pixelRatio;
		newMouseY = (e.clientY - canvasBounds.top) * pixelRatio;
	}

	function onTouchMove(e) {
		onMouseMove(e.touches[0])
	}

	function onMouseUp(e) {
		// e.currentTarget.clicked = 0;
		mouseMode = NONE;
	}

	function prevent(e) {
		if(window.Hummer&&!Hammer.Swipe({
			direction: Hammer.DIRECTION_VERTICAL,
		})) return;

		// e.preventDefault();
	}

	addEventListener(container.caption||container, 'mousedown', prevent);
	addEventListener(container.caption||container, 'touchstart', prevent);

	addEventListener(eventContainer, 'mousedown', onMouseDown);
	addEventListener(eventContainer, 'touchstart', onTouchDown);
	addEventListener(eventContainer, 'mousemove', onMouseMove);
	addEventListener(eventContainer, 'touchmove', onTouchMove);
	addEventListener(eventContainer, 'mouseup', onMouseUp);
	addEventListener(eventContainer, 'touchend', onMouseUp);
	addEventListener(eventContainer, 'touchcancel', onMouseUp);

	var destroyed = false;

	this.destroy = function () {
		destroyed = true;
		extractMainNodes();

		container.caption && container.caption.remove() || removeAllChild(container);

		removeEventListener(container.caption||container, 'mousedown', prevent);
		removeEventListener(container.caption||container, 'touchstart', prevent);

		removeEventListener( eventContainer, 'mousedown', onMouseDown);
		removeEventListener( eventContainer, 'touchstart', onTouchDown);
		removeEventListener( eventContainer, 'mousemove', onMouseMove);
		removeEventListener( eventContainer, 'touchmove', onTouchMove);
		removeEventListener( eventContainer, 'mouseup', onMouseUp);
		removeEventListener( eventContainer, 'touchend', onMouseUp);
		removeEventListener( eventContainer, 'touchcancel', onMouseUp);
		container && container.remove();
	};

	requestAnimationFrame(render);

	function screenToMainX(screenX) {
		return (screenX - mainOffsetX) / mainScaleX;
	}

	function mainToScreenX(x) {
		return x * mainScaleX + mainOffsetX;
	}

	function mainToScreenY(y) {
		return y * mainScaleY + mainOffsetY;
	}

	function screenToPreviewX(screenX) {
		return (screenX - previewOffsetX) / previewScaleX;
	}

	function previewToScreenX(x) {
		return x * previewScaleX + previewOffsetX;
	}

	this.setColors = function (newColors) {
		Object.assign(colors, newColors);
		needRedrawMain = needRedrawPreview = true;
	};

	this.setData = function (newData) {
		function findNameOfX(types) {
			for (var name in types) {
				if (types[name] === 'x') return name;
			}
			return null;
		}

		popupColumns = [];
		popupValues = [];
		columns = [];

		removeAllChild(checksContainer);
		removeAllChild(popup);
		popupTitle = createElement(popup, 'div', 'title');

		if (newData.columns.length < 2 || newData.columns[0].length < 3) {
			data = null;
			return;
		}

		data = newData;
		var nameOfX = findNameOfX(data.types);
		// console.log('nameOfX= ', nameOfX);

		for (var c = 0; c < data.columns.length; c++) {
			var columnData = data.columns[c],
				name = columnData[0];
			var column = {
				name: name,
				data: columnData,
				min: forceMinY !== undefined ? forceMinY : columnData[1],
				max: columnData[1],
				alpha: createAnimation(1, SCALE_DURATION),
				previewAlpha: createAnimation(1, SCALE_DURATION / 2)
			};

			// console.log('name= ', name, columnData);
			if (name === nameOfX) {
				column.min = columnData[1];
				column.max = columnData[columnData.length - 1];
				xColumn = column
				// console.log('xColumn= ', xColumn);
			} else {
				for (var i = 2; i < columnData.length; i++) {
					var value = columnData[i];
					if (value < column.min) column.min = value;
					else if (value > column.max) column.max = value;
				}
				columns.push(column);

				// create checkbox

				if (data.columns.length > 2) {
					var label = createElement(checksContainer, 'label', 'checkbox');
					label.innerText = data.names[name];

					var input = createElement(label, 'input');
					input.setAttribute('data-id', columns.length - 1);
					input.checked = true;
					input.type = 'checkbox';
					addEventListener(input, 'change', function (e) {
						var id = e.currentTarget.getAttribute('data-id'),
							checked = e.currentTarget.checked;
						var checkedColumn = columns[id];
						checkedColumn.saveScaleY = previewScaleY;
						checkedColumn.saveOffsetY = previewOffsetY;

						play(checkedColumn.alpha, checked ? 1 : 0);

						checkedColumn.previewAlpha.delay = checked ? SCALE_DURATION / 2 : 0;
						play(checkedColumn.previewAlpha, checked ? 1 : 0);

						needRedrawMain = needRedrawPreview = true;
						updatePreviewRangeY();
						updateMainRangeY();
					});

					var span = createElement(label, 'span', 'circle');
					span.style.borderColor = data.colors[name];

					span = createElement(label, 'span', 'symbol');
				}

				// create popup column

				var popupColumn = createElement(popup, 'div', 'column');
				popupColumn.style.color = data.colors[name];
				popupColumns.push(popupColumn);

				var popupValue = createElement(popupColumn, 'div', 'value');
				popupValues.push(popupValue);

				var popupLabel = createElement(popupColumn, 'div', 'label');
				popupLabel.innerText = data.names[name];
			}
		}

		intervalX = xColumn.data[2] - xColumn.data[1];
		previewMinX = xColumn.min;
		previewMaxX = xColumn.max;
		previewRangeX = previewMaxX - previewMinX;

		onResize();
		previewRangeY.value = previewRangeY.toValue;

		setMainMinMax(previewMaxX - previewRangeX / 4, previewMaxX);
		mainRangeY.value = mainRangeY.toValue;

		needRedrawMain = needRedrawPreview = true;
	};

	function updateMainRangeX() {
		mainRangeX = mainMaxX - mainMinX;
		mainScaleX = (width - paddingHor * 2) / mainRangeX;
		mainOffsetX = -mainMinX * mainScaleX + paddingHor;

		var delta = mainRangeX / intervalX / textCountX,

			pow = 1;
		while (pow <= delta) pow *= 2;
		delta = pow;

		if (delta < newTextX.delta) { // add dates
			oldTextX.delta = newTextX.delta;
			oldTextX.alpha.value = 1;
			play(oldTextX.alpha, 1);

			newTextX.delta = delta;
			newTextX.alpha.value = 0;
			play(newTextX.alpha, 1);
		} else if (delta > newTextX.delta) {  // remove dates
			oldTextX.delta = newTextX.delta;
			oldTextX.alpha.value = newTextX.alpha.value;
			play(oldTextX.alpha, 0);

			newTextX.delta = delta;
			newTextX.alpha.value = 1;
			play(newTextX.alpha, 1);
		}
	}

	function updateMainRangeY() {
		mainMinY = forceMinY !== undefined ? forceMinY : Number.MAX_VALUE;
		mainMaxY = Number.MIN_VALUE;

		for (var c = 0; c < columns.length; c++) {
			var column = columns[c];
			if (column.alpha.toValue === 0) continue;
			for (var i = mainMinI; i < mainMaxI; i++) {
				var y = column.data[i];
				if (y < mainMinY) mainMinY = y;
				if (y > mainMaxY) mainMaxY = y;
			}
		}

		if (mainMaxY === Number.MIN_VALUE) mainMaxY = 1;

		var range = mainMaxY - mainMinY;
		if (mainRangeY.toValue !== range) {
			play(mainRangeY, range);

			oldTextY.delta = newTextY.delta;
			oldTextY.alpha.value = newTextY.alpha.value;
			play(oldTextY.alpha, 0);

			newTextY.delta = Math.floor(mainRangeY.toValue / textCountY);
			newTextY.alpha.value = 1 - oldTextY.alpha.value;
			play(newTextY.alpha, 1);
		}
	}

	function updatePreviewRangeX() {
		previewScaleX = (width - paddingHor * 2) / previewRangeX;
		previewOffsetX = -previewMinX * previewScaleX + paddingHor;
	}

	function updatePreviewRangeY() {
		previewMinY = forceMinY !== undefined ? forceMinY : Number.MAX_VALUE;
		previewMaxY = Number.MIN_VALUE;

		for (var c = 0; c < columns.length; c++) {
			var column = columns[c];
			if (column.alpha.toValue === 0) continue;
			if (column.min < previewMinY) previewMinY = column.min;
			if (column.max > previewMaxY) previewMaxY = column.max;
		}

		if (previewMaxY === Number.MIN_VALUE) previewMaxY = 1;

		play(previewRangeY, previewMaxY - previewMinY);
	}

	function setMainMinMax(min, max) {
		var changed = false;

		if (min !== null && mainMinX !== min) {
			mainMinX = min;
			mainMinI = Math.floor((mainMinX - previewMinX - paddingHor / mainScaleX) / intervalX) + 1;
			if (mainMinI < 1) mainMinI = 1;
			changed = true;
		}

		if (max !== null && mainMaxX !== max) {
			mainMaxX = max;
			mainMaxI = Math.ceil((mainMaxX - previewMinX + paddingHor / mainScaleX) / intervalX) + 2;
			if (mainMaxI > xColumn.data.length) mainMaxI = xColumn.data.length;
			changed = true;
		}

		if (changed) {
			updateMainRangeX();
			updateMainRangeY();
			needRedrawPreview = needRedrawMain = true;
		}
	}

	function select(mouseX, mouseY) {
		var popupBounds;
		if (selectX !== mouseX) {
			selectX = mouseX;
			needRedrawMain = true;

			if (selectX === null) {
				selectI = -1;
				popup.style.display = 'none';
			} else {
				popup.style.display = 'block';

				var newSelectI = Math.round((mouseX - previewMinX) / intervalX) + 1;
				if (newSelectI < 1) newSelectI = 1;
				if (newSelectI > xColumn.data.length - 1) newSelectI = xColumn.data.length - 1;

				if (selectI !== newSelectI) {
					selectI = newSelectI;
					var x = xColumn.data[selectI];
					popupTitle.innerText = formatDate(x, false);

					for (var c = 0; c < columns.length; c++) {
						var yColumn = columns[c],
							y = yColumn.data[selectI];
						popupColumns[c].style.display = yColumn.alpha.toValue === 0 ? 'none' : 'block';
						popupValues[c].innerText = formatNumber(y, false);
					}
				}

				popupBounds = popup.getBoundingClientRect();
				// var dx = 20;
				var popupX = (mainToScreenX(mouseX) / pixelRatio) + popupLeftMargin;
				if (popupX < paddingHor) popupX = paddingHor;
				// if (popupX < 0) popupX = 0;
				if (popupX + popupBounds.width > canvasBounds.width) popupX = canvasBounds.width - popupBounds.width;
				popup.style.left = popupX + 'px';
			}
		}

		if (selectY !== mouseY) {
			selectY = mouseY;
			popupBounds = popupBounds || popup.getBoundingClientRect();
			var popupY = mouseY / pixelRatio + 39 - popupBounds.height - popupTopMargin;
			if (popupY < 0) popupY = mouseY / pixelRatio + 39 + popupTopMargin;
			popup.style.top = popupY + 'px';
		}
	}

	function onResize() {
		canvasBounds = canvas.getBoundingClientRect();
		var newWidth = canvasBounds.width * pixelRatio,
			newHeight = canvasBounds.height * pixelRatio;

		if (width !== newWidth || height !== newHeight) {
			width = newWidth;
			height = newHeight;
			mainHeight = height - previewHeight - previewMarginTop;
			textCountX = Math.max(1, Math.floor(width / (textXWidth * 2)));
			textCountY = Math.max(1, Math.floor(mainHeight / textYHeight));

			canvas.setAttribute('width', width);
			canvas.setAttribute('height', height || 'auto');
			updateMainRangeX();
			updateMainRangeY();
			updatePreviewRangeX();
			updatePreviewRangeY();

			needRedrawMain = needRedrawPreview = true;
		}
	}

	function render(t) {
		if (destroyed) return;
		time = t;

		if (data !== null) {

			//* resize
			onResize();

			if (width > 0 && height > 0) {
				// mouse

				if (mouseMode > 0) {
					mouseX += (newMouseX - mouseX) * 0.5;
					mouseY += (newMouseY - mouseY) * 0.5;
				} else {
					mouseX = newMouseX;
					mouseY = newMouseY;
				}

				if (mouseMode === DRAG_START) {
					var x = mouseX;
					if (x > previewUiMax - mouseArea * 2) x = previewUiMax - mouseArea * 2;
					var newMinX = screenToPreviewX(x);
					if (newMinX < previewMinX) newMinX = previewMinX;
					setMainMinMax(newMinX, null);
				} else if (mouseMode === DRAG_END) {
					var x = mouseX;
					if (x < previewUiMin + mouseArea * 2) x = previewUiMin + mouseArea * 2;
					var newMaxX = screenToPreviewX(x);
					if (newMaxX > previewMaxX) newMaxX = previewMaxX;
					setMainMinMax(null, newMaxX);
				} else if (mouseMode === DRAG_ALL) {
					var startX = mouseX + mouseStartX,
						newMinX = screenToPreviewX(startX);
					if (newMinX < previewMinX) newMinX = previewMinX;
					if (newMinX > previewMaxX - mouseRange) newMinX = previewMaxX - mouseRange;
					setMainMinMax(newMinX, newMinX + mouseRange);
				}

				var inMain = (mouseY > 0) && (mouseY < height - previewHeight) && (mouseX > 0) && (mouseX < width);
				if (inMain) {
					select(screenToMainX(Math.floor(mouseX)), Math.floor(mouseY));
				} else {
					select(null, null);
				}

				// animation

				if (updateAnimation(oldTextX.alpha)) needRedrawMain = true;
				if (updateAnimation(newTextX.alpha)) needRedrawMain = true;
				if (updateAnimation(oldTextY.alpha)) needRedrawMain = true;
				if (updateAnimation(newTextY.alpha)) needRedrawMain = true;
				if (updateAnimation(mainRangeY)) needRedrawMain = true;
				if (updateAnimation(previewRangeY)) needRedrawPreview = true;

				for (var c = 0; c < columns.length; c++) {
					var yColumn = columns[c];
					if (updateAnimation(yColumn.alpha)) needRedrawMain = true;
					if (updateAnimation(yColumn.previewAlpha)) needRedrawPreview = true;
				}

				// render

				if (needRedrawPreview) {
					needRedrawPreview = false;
					renderPreview();
				}
				if (needRedrawMain) {
					needRedrawMain = false;
					renderMain();
				}

			}
		}

		requestAnimationFrame(render);
	}

	function renderTextsX(textX, skipStep) {
		if (textX.alpha.value > 0) {
			ctx.globalAlpha = textX.alpha.value;

			var delta = textX.delta;
			if (skipStep) delta *= 2;

			var endI = Math.min(Math.ceil(mainMaxX / intervalX / delta) * delta, xColumn.data.length);
			if (skipStep) endI -= textX.delta;
			var startI = Math.max(mainMinI - 1, 1);

			for (var i = endI - 1; i >= startI; i -= delta) {
				var value = xColumn.data[i],
					x = mainToScreenX(value);
				var offsetX = 0;
				if (i === xColumn.data.length - 1) {
					offsetX = -textXWidth;
				} else if (i > 1) {
					offsetX = -(textXWidth / 2);
				}
				ctx.fillText(formatDate(value, true), x + offsetX, mainHeight + textXMargin);
			}
		}
	}

	function renderTextsY(textY) {
		if (textY.alpha.value > 0) {
			ctx.globalAlpha = textY.alpha.value;

			for (var i = 1; i < textCountY; i++) {
				var value = mainMinY + textY.delta * i,
					y = mainToScreenY(value),
					txtY= formatNumber(value, true);
				ctx.fillStyle= colors.background;
				ctx.fillRect(paddingHor-2, y + textYMargin/2, ctx.measureText(txtY).width + 4, -17*pixelRatio);
				// console.log(17*pixelRatio, ctx.measureText(txtY));

				ctx.fillStyle= colors.text;
				ctx.fillText(txtY, paddingHor, y + textYMargin);
			}
		}
	}

	function renderLinesY(textY) {
		if (textY.alpha.value > 0) {
			ctx.globalAlpha = textY.alpha.value;

			for (var i = 1; i < textCountY; i++) {
				var value = mainMinY + textY.delta * i,
					y = mainToScreenY(value);
				ctx.beginPath();
				ctx.moveTo(paddingHor, y);
				ctx.lineTo(width - paddingHor, y);
				ctx.stroke();
			}
		}
	}

	function renderPreview() {
		ctx.clearRect(0, height - previewHeight - 1, width, previewHeight + 1);

		// paths

		previewScaleY = -previewHeight / previewRangeY.value;
		previewOffsetY = height - previewMinY * previewScaleY;

		for (var c = 0; c < columns.length; c++) {
			var yColumn = columns[c];

			if (yColumn.previewAlpha.value === 0) continue;

			var columnScaleY = previewScaleY,
				columnOffsetY = previewOffsetY;

			if (yColumn.alpha.toValue === 0) {
				columnScaleY = yColumn.saveScaleY;
				columnOffsetY = yColumn.saveOffsetY;
			} else {
				var columnRangeY = yColumn.max - yColumn.min;
				if (columnRangeY > previewRangeY.value) {
					columnScaleY = -previewHeight / columnRangeY;
					columnOffsetY = height - previewMinY * columnScaleY;
				}
			}

			ctx.globalAlpha = yColumn.previewAlpha.value;
			ctx.lineWidth = previewLineWidth;
			renderPath(yColumn, 1, yColumn.data.length, previewScaleX, columnScaleY, previewOffsetX, columnOffsetY)
		}

		// draw preview ui

		previewUiMin = previewToScreenX(mainMinX);
		previewUiMax = previewToScreenX(mainMaxX);

		ctx.globalAlpha = colors.previewAlpha;
		ctx.beginPath();
		ctx.rect(paddingHor, height - previewHeight, previewUiMin - paddingHor, previewHeight);
		ctx.rect(previewUiMax, height - previewHeight, width - previewUiMax - paddingHor, previewHeight);
		ctx.fillStyle = colors.preview;
		ctx.fill();

		ctx.globalAlpha = colors.previewBorderAlpha;
		ctx.beginPath();
		ctx.rect(previewUiMin, height - previewHeight, previewUiW, previewHeight);
		ctx.rect(previewUiMax - previewUiW, height - previewHeight, previewUiW, previewHeight);
		ctx.rect(previewUiMin, height - previewHeight, previewUiMax - previewUiMin, previewUiH);
		ctx.rect(previewUiMin, height - previewUiH, previewUiMax - previewUiMin, previewUiH);
		ctx.fillStyle = colors.previewBorder;
		ctx.fill();
	}

	function renderMain() {
		ctx.clearRect(0, 0, width, mainHeight + previewMarginTop);

		mainScaleY = -(mainHeight - mainPaddingTop) / mainRangeY.value;
		mainOffsetY = mainHeight - mainMinY * mainScaleY;

		// lines

		ctx.strokeStyle = colors.line;
		ctx.lineWidth = lineWidth;

		renderLinesY(oldTextY);
		renderLinesY(newTextY);

		ctx.globalAlpha = 1;
		ctx.strokeStyle = colors.zeroLine;
		ctx.beginPath();
		ctx.moveTo(paddingHor, mainHeight);
		ctx.lineTo(width - paddingHor, mainHeight);
		ctx.stroke();

		// paths

		for (var c = 0; c < columns.length; c++) {
			var yColumn = columns[c];

			if (yColumn.alpha.value === 0) continue;

			ctx.globalAlpha = yColumn.alpha.value;
			ctx.lineWidth = mainLineWidth;

			renderPath(yColumn, mainMinI, mainMaxI, mainScaleX, mainScaleY, mainOffsetX, mainOffsetY);
		}



		// select

		if (selectX) {
			ctx.globalAlpha = 1;
			ctx.strokeStyle = colors.selectLine;
			ctx.lineWidth = lineWidth;
			ctx.beginPath();
			var x = mainToScreenX(selectX);
			ctx.moveTo(x, 0);
			ctx.lineTo(x, mainHeight);
			ctx.stroke();

			var x = xColumn.data[selectI];
			for (var c = 0; c < columns.length; c++) {
				var yColumn = columns[c];
				if (yColumn.alpha.toValue === 0) continue;
				var y = yColumn.data[selectI];
				ctx.strokeStyle = data.colors[yColumn.name];
				ctx.fillStyle = colors.background;
				ctx.lineWidth = circleLineWidth;
				ctx.beginPath();
				ctx.arc(x * mainScaleX + mainOffsetX, y * mainScaleY + mainOffsetY, circleRadius, 0, Math.PI * 2);
				ctx.stroke();
				ctx.fill();
			}
		}

		// text

		ctx.fillStyle = colors.text;
		ctx.font = font;
		var skipStepNew = oldTextX.delta > newTextX.delta;
		renderTextsX(oldTextX, !skipStepNew);
		renderTextsX(newTextX, skipStepNew);

		renderTextsY(oldTextY);
		renderTextsY(newTextY);

		ctx.globalAlpha = 1;
		ctx.fillText(formatNumber(mainMinY), paddingHor, mainHeight + textYMargin);
	}

	function renderPath(yColumn, minI, maxI, scaleX, scaleY, offsetX, offsetY) {
		ctx.strokeStyle = data.colors[yColumn.name];

		ctx.beginPath();
		ctx.lineJoin = 'bevel';
		ctx.lineCap = 'butt';

		var firstX = xColumn.data[minI],
			firstY = yColumn.data[minI];
		ctx.moveTo(firstX * scaleX + offsetX, firstY * scaleY + offsetY);

		var step = Math.floor((maxI - minI) / (width - paddingHor * 2));
		if (step < 1) step = 1;

		for (var i = minI + 1; i < maxI; i += step) {
			var x = xColumn.data[i],
				y = yColumn.data[i];
			ctx.lineTo(x * scaleX + offsetX, y * scaleY + offsetY);
		}
		ctx.stroke();
	}


	// todo
	// this.addTokens = function (yColumn, minI, maxI, scaleX, scaleY, offsetX, offsetY) {
	/**
	 * @param {Array} tokens
	 */
	function addTokens (tokens, yColumn, minI, maxI, scaleX, scaleY, offsetX, offsetY) {
		// ctx.strokeStyle = colors.line;

		ctx.beginPath();
		ctx.lineJoin = 'bevel';
		ctx.lineCap = 'butt';

		tokens.forEach(function(tx) {
			ctx.moveTo(tx * mainScaleX + mainOffsetX, 0);
			ctx.lineTo(0, height - mainOffsetY);
		})

		/* var firstX = xColumn.data[minI],
			firstY = yColumn.data[minI];
		ctx.moveTo(firstX * scaleX + offsetX, firstY * scaleY + offsetY);

		var step = Math.floor((maxI - minI) / (width - paddingHor * 2));
		if (step < 1) step = 1;

		for (var i = minI + 1; i < maxI; i += step) {
			var x = xColumn.data[i],
				y = yColumn.data[i];
			ctx.lineTo(x * scaleX + offsetX, y * scaleY + offsetY);
		} */
		ctx.stroke();
	}
}