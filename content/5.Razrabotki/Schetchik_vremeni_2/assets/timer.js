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
		targetDate;

		switch (at.length) {
			case 3:
				if(at[0].length == 2)
					at[0] = '19' + at[0];
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
			after: new Date(this.now - targetDate),
			// before: new Date(Math.abs(targetDate - this.now)),
			before: this.setBefore(targetDate, this.now),
		}
	}, // delta

	setBefore: function(f,s) {
		var y = Math.max(f.getFullYear(), s.getFullYear()),
			d = new Date(f).setFullYear(y) ;
		d = d - s > 0 ? d : new Date(d).setFullYear(y + 1)
		// console.log(d - s);

		return new Date(d - s);
	},

	zeroFix: function(num) {
		return (num < 10 ? '0' : '') + num;
	},

	toHum: function(objD, when) {
		if(! (objD instanceof Date)) {
			// console.log(arguments);
			var objD = this.delta.apply(this, arguments)[when];

			// remove 'after' 4 later dates

			if(objD < 0)
				return null;
			// console.log(obj);
		}


		var y = objD.getFixYear(),

			mon = objD.getMonth(),
			d = new Date(objD - new Date(objD.getFullYear(), mon, 1)) .getTime() / (3.6e6 * 24),
			h = this.zeroFix(objD.getUTCHours()),
			m = this.zeroFix(objD.getMinutes()),
			s = this.zeroFix(objD.getSeconds());

		h = when === 'after' ? +h + +this.sts.gmt : h - this.sts.gmt;

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
				utc = $i.attr('data-UTC');

			if(!utc) {
				console.warn(i);
				return;
			}

			var after = self.toHum(utc, 'after'),
				print = (after ? ('<p>Прошло - ' + after + '</p>') : '') + '<p>Осталось - ' + self.toHum(utc, 'before') + '</p>';

			i.cont = i.cont || $i.html();

			// console.log(self.toHum($i.attr('data-UTC'), 'before'));

			$i.html(i.cont + print);

		});

		this.TO = setTimeout(this.init.bind(this, $jqo), 1e3);
	}

}


/* console.log(
	Timer.toHum('4-1'), '\n',
	Timer.toHum(Timer.delta('4-1-2014'))
); */