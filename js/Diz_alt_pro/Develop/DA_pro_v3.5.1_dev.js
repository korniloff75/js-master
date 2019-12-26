/******************************************
Copyright KorniloFF-ScriptS ©
https://js-master.ru
*******************************************/
/*
<?php
header('content-type: application/x-javascript');
require_once 'js.php';
ob_start("compress_js");
?>
*/
"use strict"; 
window.DizSel= window.DizSel || !!_K && {
	__proto__: null,
	version: '3.5.1', v:{ch:0}, log:[],
	path: /:90|^js-/i.test(location.host)? '/': '//js-master.ru/',
	get checkUrl () { return DizSel.path + 'js/Diz_alt_pro/'},
	set checkUrl (uri) { this.dPpt('checkUrl', {get: function(){return uri}});},
	SlabovidButtonParent: _K.G('$body'), 
	
	addons: {},
	sts: {},
	Backup: {
		altObj: _K.body().cr('div',{ id:'altObj', style:'position:absolute; left:0; top:0; z-index:30000; width:100%; padding:20px; margin:0;', hidden:1}),
		altBody: this.altObj.cr('div',{id:'altBody'}),
		altBack: this.altBody.cr('div',{style:'width:100%; height:100%; position:fixed; z-index:-1; left:0; top:0;' }),
		save: function() {  
			//== Запоминаем размеры
			DizSel.fns.getNodes ( function(i) { i.setAttribute('fsIsh', Math.max(DizSel.sts.fontSize.min, parseInt(getComputedStyle(i).fontSize))) }, this.obj);
			[].forEach.call(this.obj.childNodes, function(i) {DizSel.Backup.altBody.Clone(i)});
			[].forEach.call(_K.G('A$div#altObj .DA_del' ), function(i) {i.del()});
			DizSel.fns.getNodes (function(i) {DizSel.fns.setCol(i); DizSel.fns.setFS(i) }); //== Сохранение начального размера 
		}, 
		get obj() {return _K.G('$body') }
	},
	
	init: function() { 
		DizSel.inited= DizSel.inited && DizSel.inited++ || 1; _K.l("DizSel.inited= "+ DizSel.inited);
		console.assert(_K.isObject(), '_K= ' + typeof(_K));
		if(!_K.prot || !!_K.v.diz )  return;
		DizSel.linki= _K.G('A$body link');		//== Создаем ссылки на основной и добавочный стили
		DizSel.style_BG= (DizSel.linki[DizSel.linki.length-1] || _K.G('$head')).cr('link',{href: DizSel.checkUrl+ 'Uni_3.css', rel:'StyleSheet',type:'text/css'},'after').cr('link',{href: '', rel:'StyleSheet',type:'text/css'},'after');
		//== 
		DizSel.createBGs();
		//	console.profile();
		DizSel.v.start= new Date();
		DizSel.fns.imageOff();
		DizSel.CheckCook();
		DizSel.v.speed= new Date().getTime()-DizSel.v.start.getTime();
		//== 
		//	console.profileEnd();
		DizSel.fns.floatTip.init();
	//	DizSel.fns.lightCur();
		DizSel.log.push('Скорость отработки скрипта - ' + DizSel.v.speed + ' мс');
		if(_K.G('#speedScr')) _K.G('#speedScr',DizSel.v.speed.toString());
		_K.i('Diz_alt_pro LOG: \n-------------------------\n'+DizSel.log.join('\n'));
	},
	addStyleSheet: function() {
		if(Cook.get('diz_alt')==='y') {Cook.set({diz_alt:'n'},DizSel.sts.mem) ; } 
		else {Cook.set({diz_alt:'y'},DizSel.sts.mem); }; 
		DizSel.CheckCook();
	},
	PUopacity: function() { //== Анимация прозрачности ПУ
		_K.animate.stop=false;
		DizSel.v.animate= setTimeout(function() {
			_K.animate.init(9,1, 3000, function() {DizSel.PU.style.opacity= arguments[0]/10});
		},1000);
	},
	
	CheckCook: function() { //== При загрузке и КАЖДОМ КЛИКЕ на SlabovidButton
		DizSel.v.DAlt= Cook.get('diz_alt')==='y'; //== кешируем diz_alt
		this.SlabovidButton.value= this.SlabovidButton.title= this.sts.button.value;
		
		if(!!_K.G('#puzadpn')) { //== Проверяем не Юкоз ли это? Выставляем отступ контента
			this.PU.style.top= parseInt(getComputedStyle(_K.G('#puzadpn')).height)*1.2+'px'; 
				_K.body().style.top= +(this.PU.style.top)*1.1+ parseInt(getComputedStyle(DizSel.PU).height)+'px';
DizSel.log.push('getComputedStyle(_K.G(\'#puzadpn\')).height= '+(getComputedStyle(_K.G('#puzadpn')).height || 'нету'));
			} else if(_K.G('#wpadminbar' )) { //== Проверяем не ВП ли это? Выставляем отступ контента
				this.PU.style.top= getComputedStyle(_K.G('wpadminbar')).height;
			} else DizSel.Backup.altObj.style.top= (parseInt(getComputedStyle(DizSel.PU).height) || 45) + 'px';
DizSel.log.push('getComputedStyle(DizSel.PU).height= '+getComputedStyle(DizSel.PU).height);	
		DizSel.PU.e.add({
			mouseover: 'if(!!DizSel.v.animate) clearTimeout(DizSel.v.animate); _K.animate.stop=true; this.style.opacity=1;',
			mouseout: DizSel.PUopacity
		});
////////////////////////////////////////
		//== если выбран ДИЗАЙН Д/СЛАБОВИДЯЩИХ
		
		if(DizSel.v.DAlt) { 
			DizSel.Backup.altObj.hidden=0;
			//== Исходный цвет текста
			this.changeCol.value= Cook.get('diz_alt_Col') || DizSel.sts.startCol()
			DizSel.Backup.altBody.className= Cook.get('diz_alt_class')||'';
			DizSel.style_BG.href= DizSel.checkUrl+ (Cook.get('diz_alt_BG') || 'cs-white') +'.css'; //== Назначаем заданный фон
			
			
//////////////////////////////////	
		//== если СТАНДАРТНЫЙ ДИЗАЙН
		} else if(!DizSel.v.DAlt) { 
			DizSel.Backup.altObj.hidden=1;
			_K.animate.stop=true;
			
			if(!!_K.G('#puzadpn')) DizSel.SlabovidButton.style.margin= getComputedStyle(_K.G('#puzadpn')).height+' auto -'+ getComputedStyle(_K.G('#puzadpn')).height;
			if(!!_K.G('#uzadpn')) DizSel.SlabovidButton.style.position='relative';
			
		} //== /DizSel.v.DAlt
		
		DizSel.regRu(); //== Проверяем хостинг reg.ru
	},
	noFA: function() {
		FA= ['DIV_DA_207850_wrapper'];
		FA.forEach(function(el) {el.hidden=1});
	},
	regRu: function() {
		var rRH= _K.G('$#header-content-inner div' );
		DizSel.log.push('rRH= '+ rRH);
		if (!rRH || !Check.re(/widget/i,rRH.id)) return;
		DizSel.log.push('Ага, значит сайт на хосте REG.RU');
		if(DizSel.v.DAlt) _K.G(rRH, {height:0} );
		else _K.G(rRH, {height:''} );
	},
	
	///////////////////////////////////////////////////////
	fns: { //== Вложенные ФУНКЦИИ	
		getNodes: function getNodesSelf(handler, els) { //== Метод увеличения/уменьшения кегля
			els= (els || DizSel.Backup.altBody).childNodes; 

			Object.keys(els).forEach(function(i) {
				if ( ![1].inArray(els[i].nodeType) || Check.re(/no-size/i,els[i].className) || els[i].tagName && Check.re(DizSel.sts.fontSize.NoTags,els[i].tagName)) return; //== Выкидываем ненужные Ноды, классы и теги
				handler(els[i]);
				 //== Каждому родителю текстового блока назначаем стиль
				if(els[i].hasChildNodes()) { getNodesSelf (handler, els[i]) } //== Остаются теги. Если есть потомки - рекурсия
			});

		},
		
		setCol: function(i) { i.style.color= Cook.get('diz_alt_Col') || DizSel.sts.startCol(); },  // if (DizSel.v.DAlt)
		setFS: function (i) { 
			i.fsIsh= +(i.fsIsh || Math.max(i.getAttribute('fsIsh'), DizSel.sts.fontSize.min));
			i.fs= i.fsIsh + DizSel.sts.fontSize.step * (+Cook.get('diz_alt_fs'));   
			i.style.fontSize= i.fs+'px';
		},
		toggleStyle: function (e, stName) {
			_K.l("arguments[0]= "+ arguments[0]); _K.l("arguments[1]= "+ arguments[1]); _K.l("arguments[2]= "+ arguments[2]);
//			if(stName)
			DizSel.Backup.altBody.classList.toggle(stName);
//			DizSel.Backup.altBody.classList.contains(stName)? DizSel.Backup.altBody.classList.remove(stName) : DizSel.Backup.altBody.classList.add(stName);
//			Cook.set([['diz_alt_class',DizSel.Backup.altBody.className]],DizSel.sts.mem); 
			Cook.set({diz_alt_class: DizSel.Backup.altBody.className}, DizSel.sts.mem); 
		},
		
		imageOff: function () {
			for (var i=0, imgs= DizSel.Backup.altBody.G('A$img'), l= imgs.length; i < l; i++) {
				var imgSize= getComputedStyle(imgs[i]);
				if(parseInt(imgSize.width) < DizSel.sts.imageOff.minImg || imgs[i].id==='captcha') continue;
				Cook.get('diz_alt_Img') || Cook.set({diz_alt_Img:3},DizSel.sts.mem) ;
				imgs[i].alter= imgs[i].alter || +(Cook.get('diz_alt_Img'));
				
				switch(imgs[i].alter) {
					case 4: //== Ч/Б
					imgs[i].style.filter= imgs[i].style.WebkitFilter= 'grayscale(100%)'; 
					Cook.get('diz_alt_Img')==4 || Cook.set({diz_alt_Img:4},DizSel.sts.mem);
					imgs[i].alter=1; break;
					case 1: //== ALT
					imgs[i].hidden=1; 
					_K.G(imgs[i], {filter:'grayscale(0)', WebkitFilter:'grayscale(0)'} ); 
					_K.G(imgs[i] ).cr('span', {class:'imgAlt', style:'padding:3px;border:1px solid;display: inline-block; width:'+imgSize.width+'; height:'+imgSize.height} , 'after').innerHTML= imgs[i].alt || 'IMG';
					Cook.get('diz_alt_Img')==1 || Cook.set({diz_alt_Img:1},DizSel.sts.mem);
					imgs[i].alter++; break;
					case 2: //== NONE
					imgs[i].hidden=1;  
					!_K.G('$span.imgAlt' ) || [].forEach.call(_K.G('A$span.imgAlt' ), function(a) {a.del()});
					Cook.get('diz_alt_Img')==2 || Cook.set({diz_alt_Img:2},DizSel.sts.mem);
					imgs[i].alter++; break;
					case 3: //== DEFAULT
					imgs[i].hidden=0; 
					imgs[i].alter++;
					Cook.get('diz_alt_Img')==3 || Cook.set({diz_alt_Img:3},DizSel.sts.mem);
					break;
				}
				
			};
			
		},
		sound: function() {
			function addSts (t) {
				setTimeout(function() {
				//	ya.speechkit.settings.apikey = '...'; 
					ya.tts = new ya.speechkit.Tts( DizSel.sts.sound );
				}, (t||0)+ 300)
			}
			if(!window.ya) _K.G('$head').cr('script',{type:'text/javascript', src:'https://webasr.yandex.net/jsapi/v1/webspeechkit.js'})
				.onload= function() { try { addSts() } catch (e) { addSts(300) }};
			
			DizSel.Backup.altBody.onmouseup = function(e) {
				e=_K.Event.fix(e);
				var selNode = window.getSelection().toString() ;// anchorNode.textContent
				if(e.target.nodeName==='CODE' || !selNode) return;
				_K.l("Настройки - "+DizSel.sts.sound.speaker+", проигрывается= "+ selNode);
				ya.tts.speak(selNode); selNode=false;
			}
			_K.G(this, {boxShadow: '0 0 1px 1px #999', transform: 'scale(1.2)'} )
		},
		
		floatTip: {
			init: function() {
				if(window.floatTip) return;
				this._constr();
				this.obj= _K.G('#floatTip' ) ||  _K.G('$body').cr('div',{id:'floatTip',style:'z-index:50000; position: absolute;'+DizSel.sts.floatTip.st,hidden:1});
				[].forEach.call(_K.G('A$#special-panel *[title]') , function(i) {
					i.e.add({
						'mouseover': function(e) {DizSel.fns.floatTip.toolTip.call(i,e); DizSel.fns.floatTip.moveTip(e)},
						'mouseout': 'DizSel.fns.floatTip.obj.hidden=1'
					})
					i['data-title']= i['data-title'] || i.title; i.title='';
				})
			},
			_constr: function() {
				var ppts= {
					moveTip: function(e) {
						e=_K.Event.fix(e);
						var gC= getComputedStyle(DizSel.fns.floatTip.obj),
							x=e.pageX, y=e.pageY; 
						DizSel.fns.floatTip.obj.style.left= (((x + parseInt(gC.width) + DizSel.sts.floatTip.distX) < (_K.body().clientWidth+_K.scroll.left))? x+DizSel.sts.floatTip.distX: x - parseInt(gC.width)-DizSel.sts.floatTip.distX) + 'px';
						DizSel.fns.floatTip.obj.style.top= (((y + parseInt(gC.height) + DizSel.sts.floatTip.distY) < (_K.body().clientHeight+_K.scroll.top))? y+DizSel.sts.floatTip.distY : y - parseInt(gC.height)-DizSel.sts.floatTip.distY) + 'px';
					},
					toolTip: function (e) { _K.G(DizSel.fns.floatTip.obj, this['data-title']).hidden= 0; }
				}
				return _K.clonePpts(DizSel.fns.floatTip, ppts, {enum:true});
			}
		}, //== /floatTip
		lightCur: function() { //== В проекте, не работает
			var originalBGplaypen = $("#altObj").css("background-color"),
			    x, y, xy, bgWebKit, bgMoz, 
			    lightColor = "rgba(150,150,255,0.75)",
			    gradientSize = 100;
					
				// Код демонстрации
				$('#altObj').mousemove(function(e) {
				
					x  = e.pageX - this.offsetLeft;
					y  = e.pageY - this.offsetTop;
					xy = x + " " + y;
					   
					bgWebKit = "-webkit-gradient(radial, " + xy + ", 0, " + xy + ", " + gradientSize + ", from(" + lightColor + "), to(rgba(255,255,255,0.0))), " + originalBGplaypen;
					bgMoz    = "-moz-radial-gradient(" + x + "px " + y + "px 45deg, circle, " + lightColor + " 0%, " + originalBGplaypen + " " + gradientSize + "px)";
										
//					_K.G(e.target, { background: bgMoz});

					$(this)
						.css({ background: bgWebKit })
						.css({ background: bgMoz });

					
				}).mouseleave(function() {			
					$(this).css({ background: originalBGplaypen });
				});
		},
		
		stylePU: function(el,set) { //== Украшаем ПУ стилями css3
			set= set || {opacity:.1, scale:.5 };
			el.style.opacity=set.opacity; 
		},
		zoom: function() { //== Разработка
			var base, baseZoom;
			function baseInit () {
				base= this.innerHTML;
				baseZoom= _K.G('$body' ).cr('div',{class:'zoomDiv'});
				baseZoom.innerHTML= base;
			}
		}
	}, //== /fns
	
	SaveFS: function (ch) { //== Сохраняем количество изменений размера шрифта
		if(!isNaN(ch)) {
			DizSel.v.ch= ch;
			var Next= +(Cook.get('diz_alt_fs') || 0)+ Math.sign(ch);  
			if(!DizSel.sts.fontSize.fixed) { //== Если размер итерационный
				if(!(Math.abs(Next)>DizSel.sts.fontSize.iter)) Cook.set({diz_alt_fs: Next},DizSel.sts.mem); 
			} else Cook.set({diz_alt_fs: Math.sign(ch)},DizSel.sts.mem);
			DizSel.fns.getNodes (DizSel.fns.setFS);
		} 
	},
	
/////////////////////////////////////////////////////////////
	createBGs: function() { //== Запускается ПРИ ЗАГРУЗКЕ 
		//== Backup content /////////////////////////////////
			DizSel.Backup.save();
		//== Создаем БЛОК УПРАВЛЕНИЯ
			//== Создаем фрагмент ПУ
			DizSel.PUfr= document.createDocumentFragment();
			DizSel.PU= DizSel.PUfr.cr('div',{id:'special-panel',class:'no-size', style:" opacity:.9; "});
			DizSel.inPU= DizSel.PU.cr('div', {style:'margin: 0 auto; display: inline-block;'});
			_K.fns.noCopy.bind(DizSel.PU);
			DizSel.PU.e.add('select', function(e) {_K.Event.fix(e).preventDefault()});
			//== Создаем кнопку, если ее еще нет
			DizSel.SlabovidButton= DizSel.SlabovidButton || _K.G(DizSel.SlabovidButtonParent ).cr("input",{type:'button',class:'imgPU no-size btn min700 DA_del', style:'padding: 1px 5px 1px 50px; margin:0 auto; font-size:20px; background: url("' + DizSel.checkUrl + 'eye.gif") no-repeat scroll 3px center, #ddd linear-gradient(#cccccc, #fafafa) repeat scroll 0 0;border: 1px solid #aaaaaa; border-radius:5px; z-index:9949; position:static; left:5px; top:20px; cursor:pointer;', id:'diz_alt', value:'ДЛЯ СЛАБОВИДЯЩИХ'} ,"fCh");
			DizSel.SlabovidButton.e.add("click",DizSel.addStyleSheet);
			if(typeof DizSel.sts.button.callback==='function') DizSel.sts.button.callback() ;
			//== Создаем БЛОК КНОПОК СМЕНЫ ФОНА
			this.v.BG= this.v.BG || DizSel.inPU.cr('div',{class:'no-size flex', style:'margin: 0 auto; display: flex;'}) ;
			DizSel.PU_sts= DizSel.PU_sts || DizSel.inPU.cr('div', { hidden:1 }).cr('div',{class:'flex', style:'display: flex;'});
			DizSel.v.BG.cr('div',{style:'position: absolute; right: 0;'}).innerHTML= 'v '+ DizSel.version;
			DizSel.changeCol= DizSel.v.BG.cr('input',{type:'color',class:'imgPU',title:'цвет ТЕКСТА',style:'width:50px;height:30px;float:left; margin:10px 20px;padding:0;', value: Cook.get('diz_alt_Col') || DizSel.sts.startCol()});
			//== Кукисы прописывать отдельно!
			
			Object.keys(DizSel.sts.BG_dim).forEach(function(i) {
				var fn= (function(bg) { return function() { Cook.set({diz_alt_BG:bg },DizSel.sts.mem); Cook.set({diz_alt_Col:DizSel.sts.startCol()},DizSel.sts.mem); DizSel.CheckCook(); DizSel.fns.getNodes(DizSel.fns.setCol); }})(i), // NE
				
				a= { ppts:{class:'sprite-'+i, title:DizSel.sts.BG_dim[i]}, e:fn, st:{margin:'0 5px'},in_sts:true } ;
				nextPU.call(a);
			});
			

			//== Создаем кнопки увеличения/уменьшения/кернинга/сброса font-size
			var elsPU= {
				fontSizeS: { ppts:{class:'sprite-fontBig', title:'Уменьшить'}, e:function() {DizSel.SaveFS(-DizSel.sts.fontSize.step); }, st:{marginLeft:'20px'}},
				fontSizeB: { ppts:{ class:'sprite-fontBig_50', title:'Увеличить'}, e:function() {DizSel.SaveFS(DizSel.sts.fontSize.step); } },
				fontType: { ppts:{ class:'sprite-text', title:'Тип шрифта'}, e:DizSel.fns.toggleStyle.bind(null,e,'fontType'),in_sts:true},
				kern: {ppts:{class:'sprite-kern',title:'Изменить интервал'}, e:DizSel.fns.toggleStyle.bind(null,e, 'kern'),in_sts:true},
				lh: {ppts: {class:'sprite-kern',title:'Высота строки'}, e: DizSel.fns.toggleStyle.bind(null,e, 'lineHeight'), st:{ transform: 'rotate(90deg)'},in_sts:true},
				imageOff: {ppts: {class:'sprite-imageOff',title:'Показ изображений',alt:'Показ изображений'}, e:DizSel.fns.imageOff, in_sts:true},
				sound: {ppts: {class:'sprite-sound',title:'Озвучивать выделенный текст',alt:'Озвучивать выделенный текст'}, e:DizSel.fns.sound},
				sts: {ppts: {class:'sprite-settings',title:'Настройки отображения сайта',alt:'Настройки отображения сайта'}, e:DizSel.PU_sts.dToggle.bind(DizSel.PU_sts.parentNode)},
			//	fontRes: {ppts: {class:'sprite-reset', title:'Сбросить'}, e:function() { return DizSel.SaveFS('reset') }},
				toDefault: {ppts: {class:'sprite-toDefault', title:'Обычный вид', id:'toDefault'}, e:function() { DizSel.addStyleSheet(); }, st:{margin:'3px 20px 0'}},
			};
			
			DizSel.changeCol.onchange= function() { //== Цвет текста
				Cook.set({diz_alt_Col: this.value},DizSel.sts.mem); 
				DizSel.fns.getNodes(DizSel.fns.setCol);
			};
			
			function nextPU (n) {
				var o= !!n? this[n]: this;
				var i= (o.in_sts?DizSel.PU_sts:DizSel.v.BG).cr('i',o.ppts);
				i.classList.add ('imgPU'); 
			//	i.draggable=false;
				i.focus=false;
				!!o.e && (i.onclick= o.e);
				!!o.st && _K.G(i, o.st);
			}
			Object.keys(elsPU).forEach(function(i) {nextPU.call(elsPU, i);})
		//	DizSel.PU_sts.style.top= getComputedStyle(DizSel.PU).height;
			//== Вставляем фрагмент в DOM
			DizSel.Backup.altObj.Append(DizSel.PUfr,"fCh");
			
		//== Создаем БЛОК УПРАВЛЕНИЯ - Конец
		
		
		if(DizSel.v.DAlt) DizSel.PUopacity(); 
		
	}, //== /createBGs
	
} //== /DizSel
// if(Cook.get('diz_alt')==='y') DizSel.Backup.altObj.hidden=0;

if(!window.oldStable && !DizSel.inited) {
	DizSel.addons.puny= DizSel.checkUrl && _K.G('$head').cr('script', {src: DizSel.checkUrl + 'punycode.js', async:0});
	DizSel.addons.db= _K.G('$head').cr('script',{src: DizSel.path+'js/db/db_DA_pro_3?req='+_K.fns.rnd(0,1e5), async:0});
	if(/bereghost\.ru/.test(location.host)) DizSel.noFA();
	_K.Event.add(window,'load',DizSel.init );
} 

/*************************************
<?php ob_end_flush();?>
*/
/*********************************************************************************
<script src="//js-master.ru/js/KorniloFF/KorniloFF_3.js" type="text/javascript" gr_id=$GROUP_ID$ fixId=1 charset="utf-8" ant="no"></script> 
<script src="//js-master.ru/js/Diz_alt_pro/DA_pro_v3.js" type="text/javascript" charset="utf-8"></script>
*************************************
<script src="/kff/KorniloFF_3.js" type="text/javascript" gr_id=$GROUP_ID$ fixId=1 charset="utf-8" ant="no"></script> 
<script src="/kff/DA_pro_v3.js" type="text/javascript" charset="utf-8"></script>
<!-- script src="//js-master.ru/js/Diz_alt_pro/Develop/DA_pro_v3_B.js" type="text/javascript" charset="utf-8"></script -->

//== v 5.xxx Назначить путь к локальной папке
<script type="text/javascript">
	DizSel.checkUrl= "/kff/";
</script>

4 Joomla - вставить в шаблон
{module kff|showtitle=0}

_K.G('#header').classList.add('DA_del');

	//== Изменение расположения кнопки
//	_K.G(DizSel.SlabovidButton, {margin: '0 0 -'+getComputedStyle(DizSel.SlabovidButton).height} ) ;
[].forEach.call(_K.G('A$#header_bgr .midline'), function(i) {i.classList.add('DA_del')});
_K.G('#widget-7bc00b1f-7b03-53cc-4d97-ec58b8e8b280').classList.add('DA_del');

<script type="text/javascript">
	_K.v.slB= _K.G('$.art-shapes' );
	DizSel.SlabovidButtonParent= _K.v.slB;
	_K.G(_K.v.slB, {position:'relative'} );
	_K.DR(function() {
	//	_K.G(DizSel.SlabovidButton, {left:'',top:'',right:0,position:'absolute',textShadow: '0 0 0 #159', fontSize:'1.4em',backgroundColor:'transparent',color:'#fff',borderWidth:'1px'});
	})
</script>

<script type="text/javascript">
_K.DR(function() { 
	var nav= _K.G('$header');
	posMenu ();
	DizSel.SlabovidButton.e.add( "click", posMenu);
	_K.G('#toDefault' ).e.add( "click", posMenu);
	function posMenu () {
		if(DizSel.v.DAlt) {
			_K.G(nav, {width: '1000px'});
			_K.G('#vt_main_bg', {clear: 'both'} )
		} else {
			_K.G(nav, {width: ''});
		};
	}
 })
</script>

===== Примеры переназначения: =====
===============================
<script type="text/javascript">
	//== Дополнительный обработчик на кнопке
_K.DR(function() {	
	DizSel.SlabovidButton.e.add( "click", posMenu);
	_K.G('#toDefault' ).e.add( "click", posMenu);
	var nav= _K.G('A$#uMenuDiv2');

	posMenu ();
	function posMenu () {
		if(DizSel.v.DAlt) {
			[].forEach.call(nav, function(el) { _K.G(el, {color: Cook.get('diz_alt_Col')} ) });

		} else {
			[].forEach.call(nav, function(el) { _K.G(el, {color: ''} ) });

		};
	}
})
</script>

<script type="text/javascript">
	//== Перемещение ПУ вниз
	_K.DR(function() {_K.G(DizSel.PU, {top:'unset', bottom:0}) });
</script>
	//== Работа со шрифтом
	DizSel.sts.fontSize.fixed= true; // Фиксируем размер шрифта в формат min/max. При false - итерационное изменение
	DizSel.sts.fontSize.min= 14; // Малый шрифт
	DizSel.sts.fontSize.max= 25; // Большой шрифт
	
	//== Использование изображения вместо кнопки
	DizSel.addImg("/wp-content/uploads/2015/04/zrenie.jpg","position:width:100px;cursor:pointer;border:1px solid #117;padding:0"[,"/off.jpg"]);
	
	//== Использование готового блока вместо кнопки
<script type="text/javascript">
	DizSel.SlabovidButton= _K.G('#diz_alt', {background: 'url("/.s/t/805/8.gif") 0 0 / 100% 100%'});
</script>

	//== После загрузки страницы:
	_K.DR(function() {
	//== кнопка
	_K.G(DizSel.SlabovidButton, {left:'',top:'',right:'5px',position:'absolute'});
	//== Изменение стилей ПУ
	_K.G(DizSel.PU, {top:'80px'});
	});
	
	//== Настройка формы входа UCOZ
<style type="text/css" media="screen">
	a.login-with {width:22px;}
</style>
<style type="text/css" media="screen">
div#altBody div.block2_title {padding:0;}
div#altBody #header {height: 100px;}
</style>	

*/