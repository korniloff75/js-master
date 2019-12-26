"use strict";
window.BB= {
	insert: function (start, end, element) {
		element.focus();

		if (document.selection) {
			var sel = document.selection.createRange() || 0;
			sel.text = start + sel.text + end;
		} else if (element.selectionStart !== undefined) {
			var startPos = element.selectionStart, endPos = element.selectionEnd;

			// console.log("endPos= "+ endPos + '__' + (endPos + (end||start).length));
			element.value = element.value.substring(0, startPos) + start + element.value.substring(startPos, endPos) + end + element.value.substring(endPos, element.value.length);
			//== Возвращаем курсор в конец вставленного фрагмента
			var karet= endPos + start.length + end.length;
			element.setSelectionRange(karet,karet);
		} else {
			element.value += start + end;
		}

	},

	panel: function (bbId, t_area, codes) {	// BB.panel(bbId, txtId)
		var opts= ["b", "i", "u"],
		codes = codes || {
			b: ['fa-bold'],
			i: ['fa-italic'],
			u: ['fa-underline'],
			s: ['fa-strikethrough'],
		},
		$Fr= $(document.createDocumentFragment()),
		$opt;

		Object.keys(codes).forEach(function(i) {
			$('<i />', {
				class: codes[i][0] + ' fa button',
				title: codes[i][0].split('-')[1]
			})
			.appendTo($Fr)
			.on('click', function () {
				var attribs;
				if (/\[.*\]/.test(i))
					attribs = i.replace(/.*\[(.*?)\]/, ' $1');
				else attribs = '';

				i = i.replace(/\[.*?\]/, '');

				BB.insert('['+i+attribs+']', '[/'+i+']', t_area);
			})
		});

		/* for(var n=0,L=opts.length; n<L; n++) {
			$opt= $Fr.cr('img',{src:'/assets/images/bb/'+opts[n]+'.gif', alt:opts[n], class:"button"});

			$opt.on('click', function () {
				var button_id = this.getAttribute('alt'), attribs;
				if (/\[.*\]/.test(button_id))
					attribs = button_id.replace(/.*\[(.*?)\]/, ' $1');
				else attribs = '';

				button_id = button_id.replace(/\[.*?\]/, '');

				BB.insert('['+button_id+attribs+']', '[/'+button_id+']', t_area);
			});
		} */

		$(bbId).append($Fr);
	},


	smiley: function (smId, t_area){
		var smD= {'s1':' :p ','s2':' :) ','s9':' ;) ','s3':' :a ','s4':' :o ','s5':' :s ','s6':' :r ','s7':' :v ','s8':' :h ','s10':' :m '},
		codes = {
			/* ':)': ['fa-smile-o'],
			':(': ['fa-frown-o'],
			':|': ['fa-meh-o'], */
			':)': ['sm-good'],
			';)': ['sm-wink'],
			'))': ['sm-trol'],
			':(': ['sm-frow'],
			'o_O': ['sm-roll'],
			':*': ['sm-kiss'],
		},

		smFr= $(document.createDocumentFragment()), $sm,
		$Fr = smFr;

		Object.keys(codes).forEach(function(i) {
			$('<i />', {
				class: codes[i][0] + ' fa button',
				// style: "color: yellow"
			})
			.appendTo($Fr)
			.on('click', function () {
				BB.insert(' ' + i + ' ', '', t_area);
			})
		});

		/* for(var i in smD) {
			if (!smD.hasOwnProperty(i)) continue;
			$sm= smFr.cr('img',{src:'/assets/images/smiles/sm2/'+i+'.gif', alt:smD[i], style:"cursor:pointer;"});

			// console.log($sm);

			// $sm.onclick= function () { BB.insert(this.alt, '', t_area); };
			$sm.on('click', function () {
				BB.insert(this.alt, '', t_area);
			});
		} */

		$(smId).append(smFr);
	}

}
