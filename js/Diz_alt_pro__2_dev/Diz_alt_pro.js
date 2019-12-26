/******************************************
Copyright KorniloFF-ScriptS ©
http://kpa-ing.ru
*******************************************/
/*
<?php
header('content-type: application/x-javascript');
require_once 'js.php';
ob_start("compress_js");
?>
*/
"use strict";
window.DizSel= {
	version: 2.1, v:{}, log:[],oldStable:true,
	check_K: function() {
		DizSel.log.push("typeof(_K)==='object'= "+ (typeof(_K) === 'object'));
	if (typeof(_K) === 'object') { return DizSel.init();}
	else console.info('сайт http://kpa-ing.ru не доступен...');
	},
	urlBG: Check.re(/js-master/,location.href)? '/css/Slabovid_PRO/': 'https://js-master.ru/css/Slabovid_PRO/', //== Путь к папке с добавочными стилями и изображениями
	SlabovidButtonParent: _K.G('$body'), //== Объект для вставки кнопки - default
	BG_dim: {'cs-white':'БЕЛЫЙ фон', 'cs-blue':'СИНИЙ фон', 'cs-black':'ЧЕРНЫЙ фон'}, // , 'grayscale':'GRAYSCALE'
	sts: { //== Настройки скрипта
		button: {image:"eye.gif", callback:false},
		elsPU: {}, //== Элементы ПУ //== Не работает
	//	startCol: '#111177',
		startCol: function() {
			switch (Cookie.get('diz_alt_BG')) {
				case 'cs-white': return '#111177';
				case 'cs-blue': return '#ffff3f';
				case 'cs-black': return '#f5f5f5';
				default: return '#111177';
			}
		},
		fontSize: { fixed:false, step:3, iter:3, NoTags: /head|script|title|link|style|iframe|img|hr|br|pre|code/i, h1Size:24}, //== 
		imageOff: { minImg: 60} //== ;
	},
	Backup: {
		saveObj: null,
		save: function() {this.saveObj= this.obj}, //== _O.cr('div')
		obj: _K.G('$body' )
	},
	init: function() {
	//	console.profile();
		console.assert(typeof(_K)==='object', '_K= ' + typeof(_K));
		this.linki= _K.G('|link');
		this.createBGs();
		this.v.start= new Date();
		this.CheckCook();
		this.v.speed= new Date().getTime()-this.v.start.getTime();
	//	console.profileEnd();
		DizSel.log.push('Скорость отработки скрипта - ' + this.v.speed + ' мс');
		if(_K.G('#speedScr')) _K.G('#speedScr',this.v.speed.toString());
		console.info('Diz_alt_pro LOG: \n-------------------------\n'+DizSel.log.join('\n'));
	},
	addStyleSheet: function() {
		if(Cookie.get('diz_alt')==='y') {Cookie.set({diz_alt:'n'},30) ; location.reload(); }
		else {Cookie.set({diz_alt:'y'},30); }; //== Не удалось вернуть локальные стили цвета
		DizSel.CheckCook();
	},
	PUopacity: function() { //== Анимация прозрачности ПУ
		_K.animate.stop=false;
		DizSel.v.animate= setTimeout(function() {
			_K.animate.init(9,1, 3000, function() {DizSel.PU.style.opacity= arguments[0]/10});
		},1000);
	},
	
	CheckCook: function() { //== При загрузке и КАЖДОМ КЛИКЕ на SlabovidButton
		DizSel.v.DAlt= Cookie.get('diz_alt')==='y'; //== кешируем diz_alt
		
		////////////////////////////////////////
		//== если выбран ДИЗАЙН Д/СЛАБОВИДЯЩИХ
		
		if(DizSel.v.DAlt) { 
			//== Исходный цвет текста
			this.changeCol.value= Cookie.get('diz_alt_Col') || DizSel.sts.startCol()
			Cookie.set({diz_alt_Col: this.changeCol.value});
			
			this.SaveFS('save'); //== Запоминаем текущие размеры и цвета
			DizSel.Style.href = this.urlBG+ 'Uni.css'; //== Путь к главному стилю для слабовидящих
			DizSel.style_BG.href= DizSel.urlBG+ (Cookie.get('diz_alt_BG') || 'cs-white') +'.css'; //== Назначаем заданный фон
			DizSel.SlabovidButton.value= DizSel.SlabovidButton.title= "Обычный вид";
			if(!!_K.G('#puzadpn')) { //== Проверяем не Юкоз ли это? Выставляем отступ контента
				this.PU.style.top= parseInt(getComputedStyle(_K.G('#puzadpn')).height)*1.2+'px'; 
				DizSel.SlabovidButton.style.margin= '0 auto -'+ getComputedStyle(_K.G('#puzadpn')).height;
				_K.body().style.paddingTop= +(this.PU.style.top)*1.1+ parseInt(getComputedStyle(DizSel.PU).height)+'px';
			// DizSel.log.push(getComputedStyle(_K.G('#puzadpn')).height);
DizSel.log.push('getComputedStyle(_K.G(\'#puzadpn\')).height= '+(getComputedStyle(_K.G('#puzadpn')).height || 'нету'));
			} else if(_K.G('#wpadminbar' )) { //== Проверяем не ВП ли это? Выставляем отступ контента
				this.PU.style.top= getComputedStyle(_K.G('wpadminbar')).height;
			} else _K.body().style.paddingTop= getComputedStyle(DizSel.PU).height;
DizSel.log.push('getComputedStyle(DizSel.PU).height= '+getComputedStyle(DizSel.PU).height);
			if(DizSel.SlabovidButton.tagName==='INPUT' || 'DIV') { 
				_K.G(DizSel.SlabovidButton, {display:'none', paddingLeft: '15px'} );
			//	DizSel.SlabovidButton.style.position= 'fixed';
			} else if(DizSel.SlabovidButton.tagName==='IMG') {
				_K.G(DizSel.SlabovidButton, {padding: 0, position: DizSel.SlabovidButton.style.position || 'fixed'} );
			}
			_K.G(DizSel.PU,{display:'', opacity:.9})
			// Украшательства ПУ
			//== Запускаем анимацию ПУ
			DizSel.PU.onmouseover= function() {
				if(!!DizSel.v.animate) clearTimeout(DizSel.v.animate); 
				_K.animate.stop=true;
				DizSel.PU.style.opacity=1;
			}
			DizSel.PU.onmouseout= DizSel.PUopacity;
			
		//////////////////////////////////	
		//== если СТАНДАРТНЫЙ ДИЗАЙН
		} else if(!DizSel.v.DAlt) { 
		//	DizSel.Backup.obj= DizSel.Backup.saveObj;
			_K.animate.stop=true;
			DizSel.changeCol.value= false;
			this.SaveFS('reset'); //== Сбрасываем размер шрифта
			if( !!headF.io) location.reload(); // !!headF.fs ||
			DizSel.Style.href ='';
			if(DizSel.style_BG)DizSel.style_BG.href='';
			DizSel.PU.style.display='none';
			DizSel.SlabovidButton.value= DizSel.SlabovidButton.title= "ДЛЯ СЛАБОВИДЯЩИХ";
			_K.body().style.paddingTop='';
			if(!!_K.G('#puzadpn')) DizSel.SlabovidButton.style.margin= getComputedStyle(_K.G('#puzadpn')).height+' auto -'+ getComputedStyle(_K.G('#puzadpn')).height;
			if(!!_K.G('#uzadpn')) DizSel.SlabovidButton.style.position='relative';
		//	DizSel.log.push('_K.animate.stop= '+_K.animate.stop)
			if(DizSel.SlabovidButton.tagName==='INPUT') {
				_K.G(DizSel.SlabovidButton, {display:'', paddingLeft:'50px'} )
			} else if(DizSel.SlabovidButton.tagName==='IMG') {
				_K.G(DizSel.SlabovidButton, {display:'', padding:0, margin:0} );
			} else if(DizSel.SlabovidButton.tagName==='DIV') _K.G(DizSel.SlabovidButton, {display:''} )
		//	DizSel.log.push(DizSel.SlabovidButton.tagName)
		}
		DizSel.regRu(); //== Проверяем хостинг reg.ru
		DizSel.log.push('Body.paddingTop= '+_K.body().style.paddingTop);
	},
	noFA: function() {
		FA= ['DIV_DA_207850_wrapper'];
		FA.forEach(function(el) {_K.d.hide(_K.G(el ))});
	},
	regRu: function() {
		var rRH= _K.G('$#header-content-inner div' );
		DizSel.log.push('rRH= '+ rRH);
		if (!rRH || !Check.re(/widget/i,rRH.id)) return;
		DizSel.log.push('Ага, значит сайт на хосте REG.RU');
		if(DizSel.v.DAlt) _K.G(rRH, {height:0} );
		else _K.G(rRH, {height:''} );
	},
	
	fns: { //== Вложенные функции	
		getNodes: function getNodesSelf(els) { //== Метод увеличения/уменьшения кегля
			els= (els||_K.body()).childNodes; 
			for (var i in els) {
				if ( !els.hasOwnProperty(i) || !_K.fns.in_array([1],els[i].nodeType) || Check.re(/no-size/i,els[i].className) || els[i].tagName && Check.re(DizSel.sts.fontSize.NoTags,els[i].tagName)) continue; //== Выкидываем ненужные Ноды, классы и теги
				DizSel.fns.setStyle(els[i]);
				 //== Каждому родителю текстового блока назначаем стиль
				if(els[i].hasChildNodes()) { getNodesSelf (els[i]) } //== Остаются теги. Если есть потомки - рекурсия
			};
		},
		setStyle: function (el) { 
			el.fsIsh= el.fsIsh || parseInt(getComputedStyle(el).fontSize);
			el.ColIsh= el.ColIsh || getComputedStyle(el).color;
		//	el.BgIsh= el.BgIsh || getComputedStyle(el).background;
			if(!DizSel.sts.fontSize.fixed) { //== Если размер итерационный
					el.fs= el.fsIsh + Math.abs(DizSel.v.ch)*(+Cookie.get('diz_alt_fs'));   
				} else { //== Если размер фиксированный
					el.fs= DizSel.v.ch<0? (el.fsIsh - Math.abs(DizSel.v.ch)*DizSel.sts.fontSize.iter): (el.fsIsh + Math.abs(DizSel.v.ch)*DizSel.sts.fontSize.iter);
				}
		//	el.setAttribute("Fs",el.fs+'_'+el.fsIsh+'_'+ (+getComputedStyle(el).fontSize));
			el.style.fontSize= el.fs+'px';
			
			if (DizSel.v.DAlt) el.style.color= Cookie.get('diz_alt_Col'); // || el.ColIsh ;
		},
		toggleStyle: function (stName) {
			var stNameFull= DizSel.urlBG+stName + '.css';
			var h= this.href= (Check.re(stNameFull,this.href,'i'))?  '': stNameFull;
			console.log('(this.href === stNameFull)= '+ (this.href === stNameFull));
			Cookie.set([[stName,h]],3); 
		},
		
		imageOff: function () {
			for (var i=0, imgs= _K.G('A$img' ), l= imgs.length; i < l; i++) {
				var imgSize= getComputedStyle(imgs[i]);
				if(parseInt(imgSize.width) < DizSel.sts.imageOff.minImg || imgs[i].id==='captcha') continue;
				imgs[i].def= imgs[i].def || imgs[i].src;
				
				headF.io= true; //== Флаг для перегрузки страницы
			//	Cookie.set({diz_alt_Img:imgs[i].alter || 0});
				if ( !imgs[i].alter) { //== Ч/Б
				//	imgs[i].alter= Cookie.get('diz_alt_Img');
					imgs[i].alter= 0;
					imgs[i].style.filter= imgs[i].style.WebkitFilter= 'grayscale(100%)'; 
					imgs[i].alter++; continue;
				} else if (imgs[i].alter===1) { //== ALT
					_K.d.hide(imgs[i]); 
					_K.G(imgs[i], {filter:'grayscale(0)', WebkitFilter:'grayscale(0)'} ); 
					var s= _O.cr('span', {class:'imgAlt', style:'padding:3px;border:1px solid;display: inline-block; width:'+imgSize.width+'; height:'+imgSize.height} , imgs[i] , 'after');
					s.innerHTML= imgs[i].alt || 'IMG';
					imgs[i].alter++; continue;
				} else if (imgs[i].alter===2) { //== NONE
					[].forEach.call(_K.G('A$span.imgAlt' ), function(a) {_O.del.call(a)});
					imgs[i].alter++; continue;
				} else if (imgs[i].alter===3) { //== DEFAULT
					headF.io= false;
					_K.d.show(imgs[i]);
					_K.G(imgs[i], {}, {src:imgs[i].def, alter:0} ); // imgs[i].alter=0;
					//console.info('imgs[i].src= '+ imgs[i].src);
					continue;
				}
				
			};
			
		},
		
		stylePU: function(el,set) { //== Украшаем ПУ стилями КСС3
			set= set || {opacity:.1, scale:.5 };
			el.style.opacity=set.opacity; 
		},
		zoom: function() { //== Разработка
			var base, baseZoom;
			function baseInit () {
				base= this.innerHTML;
				baseZoom= _O.cr('div',{class:'zoomDiv'},_K.G('$body' ));
				baseZoom.innerHTML= base;
			}
		}
	},
	
	SaveFS: function (ch) { //== Сохраняем количество изменений размера шрифта
		if(!isNaN(ch)) {
			DizSel.v.ch= ch;
		//	console.info('Cookie.get(diz_alt_fs)= '+ Cookie.get("diz_alt_fs"));
			var Next= +(Cookie.get('diz_alt_fs') || 0)+ Math.sign(ch);  
			if(!(Math.abs(Next)>DizSel.sts.fontSize.iter)) Cookie.set({diz_alt_fs: Next},100); DizSel.fns.getNodes ();
		} else if(ch==='reset') {
			DizSel.v.kernStyle.href=DizSel.v.lhStyle.href=DizSel.v.fontType.href='';
			Cookie.set({diz_alt_fs: 0,diz_alt_BG:'', kern:'', lineHeight:'', fontType:'', diz_alt_Col:''},1); //== Обнуление изменения размера
			Cookie.del('diz_alt_Col');
			DizSel.v.ch= 0; DizSel.fns.getNodes ();
		} else if(ch==='save') {
			DizSel.v.ch= 0; DizSel.fns.getNodes (); //== Сохранение начального размера
			DizSel.v.ch= DizSel.sts.fontSize.step; DizSel.fns.getNodes ();
		}
	},
	
	
	createBGs: function() { //== Запускается ПРИ ЗАГРУЗКЕ
		//== Backup content
			DizSel.Backup.save();
		//	console.log("DizSel.Backup.save= "+ DizSel.Backup.saveObj.innerHTML);
		//== /Backup content
		
		//== Создаем БЛОК УПРАВЛЕНИЯ
			//== Создаем фрагмент ссылок на основной и добавочный стили и кнопку смены дизайна
			DizSel.StyleFR= _K.G(document.createDocumentFragment() );
			DizSel.Style= _O.cr('link',{rel:"StyleSheet",type:"text/css"},DizSel.StyleFR);
			DizSel.style_BG= _O.clone(DizSel.Style,DizSel.StyleFR);
			DizSel.v.kernStyle= _O.clone(DizSel.Style,DizSel.StyleFR);
			DizSel.v.kernStyle.href= Cookie.get('kern') || "";
			DizSel.v.lhStyle= _O.clone(DizSel.Style,DizSel.StyleFR);
			DizSel.v.lhStyle.href= Cookie.get('lineHeight') || "";
			DizSel.v.fontType= _O.clone(DizSel.Style,DizSel.StyleFR);
			DizSel.v.fontType.href= Cookie.get('fontType') || "";
			_O.append(DizSel.StyleFR, DizSel.linki[DizSel.linki.length-1] || _K.G("$head"),1);
			
			//== Создаем фрагмент ПУ
			DizSel.PUfr= _K.G(document.createDocumentFragment() );
			DizSel.PU= DizSel.PU || _O.cr('div',{id:'special-panel',class:'no-size center', style:"display:none; opacity:.9; /* height:47px; */"},DizSel.PUfr);
			
			//== Создаем кнопку, если ее еще нет
			DizSel.SlabovidButton= DizSel.SlabovidButton || _O.cr("input",{type:'button',class:'imgPU no-size btn', style:'padding: 1px 5px 1px 50px; margin:0 auto; font-size:20px; background: #9dd1ff url("' + DizSel.urlBG + DizSel.sts.button.image + '") no-repeat 3px center; border-radius:5px; z-index:21000; position:static; left:5px; top:20px; cursor:pointer;', id:'diz_alt', value:'ДЛЯ СЛАБОВИДЯЩИХ'}, _K.G(DizSel.SlabovidButtonParent ),"fCh");
			if(DizSel.SlabovidButtonParent!==_K.body()) DizSel.SlabovidButton.style.position= 'static';
			_K.Event.add(DizSel.SlabovidButton,"click",DizSel.addStyleSheet); // (); _K.Event.stop(e)
			if(typeof DizSel.sts.button.callback==='function') DizSel.sts.button.callback() ;
			//== Создаем БЛОК КНОПОК СМЕНЫ ФОНА
			this.v.BG= this.v.BG || _O.cr('div',{class:'no-size'},DizSel.PU);
			DizSel.changeCol= _O.cr('input',{type:'color',class:'imgPU',title:'цвет ТЕКСТА',style:'width:30px;height:30px;margin:0 20px;padding:0;', value: Cookie.get('diz_alt_Col') || DizSel.sts.startCol()}, DizSel.v.BG);
		//	console.info('= '+ (Cookie.get('diz_alt_Col') || '#117'));
			for (var i in DizSel.BG_dim) {
				var fn= (function(bg) { return function() { Cookie.set({diz_alt_BG:bg },3); Cookie.set({diz_alt_Col:DizSel.sts.startCol()},3); DizSel.CheckCook(); }})(i), //== Кукисы прописывать отдельно!
				a= { ppts:{src:i+ '.png', title:DizSel.BG_dim[i]}, e:fn, st:{margin:'0 5px'} } ;
				nextPU.call(a);
			};
			

			//== Создаем кнопки увеличения/уменьшения/кернинга/сброса font-size
			var elsPU= {
				fontSizeS: { ppts:{src:'fontBig.png', title:'Уменьшить'}, e:function() {DizSel.SaveFS(-DizSel.sts.fontSize.step); }, st:{marginLeft:'20px'}},
				fontSizeB: { ppts:{ src:'fontBig.png', title:'Увеличить'}, e:function() {DizSel.SaveFS(DizSel.sts.fontSize.step); }, st:{width:'36px'}},
				fontType: { ppts:{ src:'text.png', title:'Тип шрифта'}, e:DizSel.fns.toggleStyle.bind(DizSel.v.fontType, 'fontType')},
				kern: {ppts:{src:'kern.png',title:'Изменить интервал'}, e:DizSel.fns.toggleStyle.bind(DizSel.v.kernStyle, 'kern')},
				lh: {ppts: {src:'kern.png',title:'Высота строки'}, e: DizSel.fns.toggleStyle.bind(DizSel.v.lhStyle, 'lineHeight'), st:{width:'36px', transform: 'rotate(90deg)'}},
				imageOff: {ppts: {src:'imageOff.gif',title:'Показ изображений',alt:'Показ изображений'}, e:DizSel.fns.imageOff, st:{width:'35px'}},
				fontRes: {ppts: {src:'reset.png', title:'Сбросить'}, e:function() { return DizSel.SaveFS('reset') }},
				toDefault: {ppts: {src:'toDefault.png', title:'Обычный вид', id:'toDefault'}, e:DizSel.addStyleSheet, st:{width:'35px',margin:'3px 20px 0'}},
			};
			
			DizSel.changeCol.onchange= function() { //== Цвет текста
				Cookie.set({diz_alt_Col: this.value},10); 
				DizSel.fns.getNodes()
			};
			
			function nextPU (n) {
				var o= !!n? this[n]: this;
				o.ppts.src= DizSel.urlBG + o.ppts.src;
				o.ppts.class= 'imgPU';
				var i= _O.cr('img',o.ppts,DizSel.v.BG);
				if(!!o.e) i.onclick= o.e;
				if(!!o.st) _K.G(i, o.st);
			}
			for (var i in elsPU) {
				nextPU.call(elsPU, i);
			};
			
			//== Вставляем фрагмент в DOM
			_O.append(DizSel.PUfr,_K.G('$body'),"fCh");
			
		//== Создаем БЛОК УПРАВЛЕНИЯ - Конец
		
		
		if(DizSel.v.DAlt) DizSel.PUopacity(); 
		
	},
		
	//== Дополнительные опции
	addImg: function(imgSrc,imgStyle,imgSrc_off) {
		DizSel.SlabovidButton= _K.G(DizSel.SlabovidButtonParent).cr("img",{src:imgSrc, style:imgStyle },"fCh");
		_K.G(DizSel.SlabovidButton,{zIndex:500000, cursor:'pointer'}) // , transform:'scale(.8)'
		if(!!imgSrc_off) _K.Event.add(DizSel.SlabovidButton,"click",function() {this.src= !DizSel.v.DAlt? imgSrc_off: imgSrc});
	}
}


/*
<?php ob_end_flush();?>
*/

_K.DR( function() {
	if(Check.re(/%D0%92%D0%B5%D1%80%D1%81%D0%B8%D1%8F%20%D1%81%D0%B0%D0%B9%D1%82%D0%B0%20%D0%B4%D0%BB%D1%8F%20%D1%81%D0%BB%D0%B0%D0%B1%D0%BE%D0%B2%D0%B8%D0%B4%D1%8F%D1%89%D0%B8%D1%85\.htm|%D0%94%D0%B8%D0%B7%D0%B0%D0%B9%D0%BD\d\.html|kuch\-pni|(?:дс|xn--)\d+?(?:ржд.рф|-mdddl3ee.xn--p1ai)|(?:ш|xn--)\d[^9](?:ржд.рф|-llch3c4b.xn--p1ai)|zhelbook.ru/, location.href)|| !_K.prot || !!_K.v.diz_B)  return; 
	DizSel.log.push('pro; v '+DizSel.version);
	_K.v.diz= true; //== Версия скрипта запущена
	DizSel.check_K();
	if(Check.re(/bereghost\.ru/,location.href)) DizSel.noFA();
})