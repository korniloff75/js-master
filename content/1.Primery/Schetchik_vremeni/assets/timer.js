'use strict';

Date.prototype.getFixYear = function() {
	// console.log(this.getFullYear() - 1970);
	return this.getFullYear() - 1970;
}

var Timer = {
	sts: {
		gmt: $('#gmt').val() || 3,

	},

	default: $('#hollData').html(),

	/*
	str @time dd-mm[-yyyy]
	*/
	delta: function(time) {
		// toUTC
		var at = time.split('-').reverse(),
		targetDate, objD;

		switch (at.length) {
			case 3:
				targetDate = new Date(at.join('-'));
				break;

			case 2:
				targetDate = new Date(this.now.getFullYear() + '-' + at.join('-'));
				// console.log(targetDate);
				break;

			default:
				break;
		}

		// console.log(objD);

		return {
			before: this.setBefore(targetDate, this.now),
			// after: new Date(Math.abs(this.now - targetDate)),
			after: new Date(this.now - targetDate),
		}
	}, // delta

	setBefore: function(f,s) {
		var d = new Date(f - s > 0 ? f - s : new Date(f).setFullYear(s.getFullYear() + 0) - s);
		return d - s > 0 ? d : new Date(new Date(f).setFullYear(s.getFullYear() + 1) - s);
	},

	zeroFix: function(num) {
		return (num < 10 ? '0' : '') + num;
	},

	toHum: function(objD, when) {
		if(! (objD instanceof Date)) {
			var objD = this.delta.apply(this, arguments)[when];

			// remove 'after' 4 later dates
			if(objD < 0)
				return null;
			// console.log(obj);
		}


		var y = objD.getFixYear(),

			mon = objD.getMonth(),
			d = new Date(objD - new Date(objD.getFullYear(), mon, 1)) .getTime() / (3.6e6 * 24),
			h = this.zeroFix(objD.getHours()),
			m = this.zeroFix(objD.getMinutes()),
			s = this.zeroFix(objD.getSeconds());

		/* console.log(
			'toHum',
			new Date(objD.getFullYear(), mon, 1),
			new Date(objD - new Date(objD.getFullYear(), mon, 1)) .getTime() / (3.6e6 * 24)
		); */

		return ((y ? (y + ' лет, ') : '') + mon + ' месяцев, ' + Math.floor(d) + ' дней, ' + h + ':' + m + ':' + s).replace(/(\d+)/g, '<span class="green strong">$1</span>');
	}, // toHum


	init: function($jqo) {
		var self = this;

		clearTimeout(this.TO);
		this.now = new Date;

		$jqo.each(function(ind, i) {
			var $i = $(i),
				after = self.toHum($i.attr('data-UTC'), 'after'),
				print = (after ? ('<p>Прошло - ' + after + '</p>') : '') + '<p>Осталось - ' + self.toHum($i.attr('data-UTC'), 'before') + '</p>';

			i.cont = i.cont || $i.html() + ' :';

			// console.log(self.toHum($i.attr('data-UTC'), 'before'));

			$i.html(i.cont + print);

		});

		this.TO = setTimeout(this.init.bind(this, $jqo), 2e3);
	}

}


/* console.log(
	Timer.toHum('4-1'), '\n',
	Timer.toHum(Timer.delta('4-1-2014'))
); */