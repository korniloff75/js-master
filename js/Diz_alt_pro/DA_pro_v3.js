"use strict";window.DizSel=window.DizSel||!!_K&&{__proto__:_K,version:"3.6.0",v:{ch:0},log:[],path:/:90|^js-/i.test(location.host)?"/":"//js-master.ru/",get checkUrl(){return DizSel.path+"js/Diz_alt_pro/"},set checkUrl(t){Object.defineProperty(this,"checkUrl",{get:function(){return t}})},SlabovidButtonParent:_K.G("$body"),addons:{},sts:{},Backup:{altObj:_K.body().cr("div",{id:"altObj",style:"position:absolute; left:0; top:0; z-index:30000; width:100%; padding:20px; margin:0;",hidden:1}),altBody:_K.G(this.altBody)||this.altObj.cr("div",{id:"altBody"}),altBack:this.altBody.cr("div",{style:"width:100%; height:100%; position:fixed; z-index:-1; left:0; top:0;"}),save:function(){DizSel.v.start=new Date,DizSel.Backup.altBody.innerHTML="",DizSel.fns.getNodes(function(t){t.setAttribute("fsIsh",Math.max(DizSel.sts.fontSize.min,parseInt(getComputedStyle(t).fontSize)))},this.obj),[].forEach.call(this.obj.childNodes,function(t){DizSel.Backup.altBody.Clone(t)}),_K.G("A$div#altObj .DA_del").forEach(function(t){_K.G(t).del()}),DizSel.fns.getNodes(function(t){DizSel.fns.setCol(t),DizSel.fns.setFS(t)}),DizSel.v.speed=(new Date).getTime()-DizSel.v.start.getTime();var t=_K.G("#speedScr");t&&(t.textContent=DizSel.v.speed)},get obj(){return _K.G("$body")}},init:function(){return DizSel.inited=DizSel.inited&&DizSel.inited++||1,DizSel.log.push("DizSel.inited= "+DizSel.inited),_K.isObject()?void(_K.prot&&!_K.v.diz&&(DizSel.linki=_K.G("A$body link"),DizSel.style_BG=(DizSel.linki[DizSel.linki.length-1]||_K.G("$head")).cr("link",{href:DizSel.checkUrl+"Uni_3.css",rel:"StyleSheet",type:"text/css"},"after").cr("link",{href:"",rel:"StyleSheet",type:"text/css"},"after"),DizSel.createBGs(),DizSel.fns.imageOff(),DizSel.CheckCook(),DizSel.fns.floatTip.init(),DizSel.log.push("Скорость отработки скрипта - "+DizSel.v.speed+" мс"),_K.i("Diz_alt_pro LOG: \n-------------------------\n"+DizSel.log.join("\n")))):void console.error("missing _K")},addStyleSheet:function(){"y"===Cook.get("diz_alt")?Cook.set({diz_alt:"n"},DizSel.sts.mem):Cook.set({diz_alt:"y"},DizSel.sts.mem),DizSel.CheckCook()},PUopacity:function(){DizSel.PUanimate=setInterval(function(){return DizSel.PU.style.opacity<=.1?void clearInterval(DizSel.PUanimate):void(DizSel.PU.style.opacity=DizSel.PU.style.opacity-.015)},30)},CheckCook:function(){DizSel.v.DAlt="y"===Cook.get("diz_alt"),this.SlabovidButton.value=this.SlabovidButton.title=this.sts.button.value,_K.G("#puzadpn")?(this.PU.style.top=1.2*parseInt(getComputedStyle(_K.G("#puzadpn")).height)+"px",_K.body().style.top=1.1*+this.PU.style.top+parseInt(getComputedStyle(DizSel.PU).height)+"px",DizSel.log.push("getComputedStyle(_K.G('#puzadpn')).height= "+(getComputedStyle(_K.G("#puzadpn")).height||"нету"))):_K.G("#wpadminbar")?this.PU.style.top=getComputedStyle(_K.G("wpadminbar")).height:_K.G(DizSel.Backup.altObj).style.paddingTop=(parseInt(getComputedStyle(DizSel.PU).height)||45)+"px",DizSel.log.push("getComputedStyle(DizSel.PU).height= "+getComputedStyle(DizSel.PU).height),DizSel.PU.e.add({mouseover:"clearInterval (DizSel.PUanimate); this.style.opacity=1;",mouseout:DizSel.PUopacity}),DizSel.v.DAlt?(DizSel.Backup.altObj.hidden=0,this.changeCol.value=Cook.get("diz_alt_Col")||DizSel.sts.startCol(),DizSel.Backup.altBody.className=Cook.get("diz_alt_class")||"",_K.G(DizSel.style_BG).href=DizSel.checkUrl+(Cook.get("diz_alt_BG")||"cs-white")+".css"):DizSel.v.DAlt||(DizSel.Backup.altObj.hidden=1,clearInterval(DizSel.PUanimate),_K.G("#puzadpn")&&(DizSel.SlabovidButton.style.margin=getComputedStyle(_K.G("#puzadpn")).height+" auto -"+getComputedStyle(_K.G("#puzadpn")).height),_K.G("#uzadpn")&&(DizSel.SlabovidButton.style.position="relative")),DizSel.regRu()},noFA:function(){FA=["DIV_DA_207850_wrapper"],FA.forEach(function(t){t.hidden=1})},regRu:function(){var t=_K.G("$#header-content-inner div");t&&/widget/i.test(t.id)&&(DizSel.log.push("rRH= "+t),DizSel.log.push("Ага, значит сайт на хосте REG.RU"),DizSel.v.DAlt?_K.G(t,{height:0}):_K.G(t,{height:""}))},fns:{getNodes:function t(e,i){i=(i||DizSel.Backup.altBody).childNodes,Object.keys(i).forEach(function(l){![1].includes(i[l].nodeType)||/no-size/i.test(i[l].className)||i[l].tagName&&DizSel.sts.fontSize.NoTags.test(i[l].tagName)||(e(i[l]),i[l].hasChildNodes()&&t(e,i[l]))})},setCol:function(t){t.style.color=Cook.get("diz_alt_Col")||DizSel.sts.startCol()},setFS:function(t){t.fsIsh=+(t.fsIsh||Math.max(t.getAttribute("fsIsh"),DizSel.sts.fontSize.min)),t.fs=t.fsIsh+DizSel.sts.fontSize.step*+Cook.get("diz_alt_fs"),t.style.fontSize=t.fs+"px"},toggleStyle:function(t){_K.l("arguments= ",arguments),DizSel.Backup.altBody.classList.toggle(t),Cook.set({diz_alt_class:DizSel.Backup.altBody.className},DizSel.sts.mem)},imageOff:function(){for(var t=0,e=DizSel.Backup.altBody.G("A$img"),i=e.length;i>t;t++){var l=getComputedStyle(e[t]);if(!(parseInt(l.width)<DizSel.sts.imageOff.minImg||"captcha"===e[t].id))switch(Cook.get("diz_alt_Img")||Cook.set({diz_alt_Img:3},DizSel.sts.mem),e[t].alter=e[t].alter||+Cook.get("diz_alt_Img"),e[t].alter){case 4:e[t].style.filter=e[t].style.WebkitFilter="grayscale(100%)",4==Cook.get("diz_alt_Img")||Cook.set({diz_alt_Img:4},DizSel.sts.mem),e[t].alter=1;break;case 1:e[t].hidden=1,_K.G(e[t],{filter:"grayscale(0)",WebkitFilter:"grayscale(0)"}),_K.G(e[t]).cr("span",{"class":"imgAlt",style:"padding:3px;border:1px solid;display: inline-block; width:"+l.width+"; height:"+l.height},"after").innerHTML=e[t].alt||"IMG",1==Cook.get("diz_alt_Img")||Cook.set({diz_alt_Img:1},DizSel.sts.mem),e[t].alter++;break;case 2:e[t].hidden=1,!_K.G("$span.imgAlt")||[].forEach.call(_K.G("A$span.imgAlt"),function(t){t.del()}),2==Cook.get("diz_alt_Img")||Cook.set({diz_alt_Img:2},DizSel.sts.mem),e[t].alter++;break;case 3:e[t].hidden=0,e[t].alter++,3==Cook.get("diz_alt_Img")||Cook.set({diz_alt_Img:3},DizSel.sts.mem)}}},sound:function(){function t(t){setTimeout(function(){ya.tts=new ya.speechkit.Tts(DizSel.sts.sound)},(t||0)+300)}window.ya||(_K.G("$head").cr("script",{src:"https://webasr.yandex.net/jsapi/v1/webspeechkit.js"}).onload=function(){try{t()}catch(e){t(300)}}),DizSel.Backup.altBody.onmouseup=function(t){t=_K.Event.fix(t);var e=window.getSelection().toString();"CODE"!==t.target.nodeName&&e&&(_K.l("Настройки - "+DizSel.sts.sound.speaker+", проигрывается= "+e),ya.tts.speak(e),e=!1)},_K.G(this,{boxShadow:"0 0 1px 1px #999",transform:"scale(1.2)"})},floatTip:{init:function(){window.floatTip||(this._constr(),this.obj=_K.G("#floatTip")||_K.G("$body").cr("div",{id:"floatTip",style:"z-index:50000; position: absolute;"+DizSel.sts.floatTip.st,hidden:1}),[].forEach.call(_K.G("A$#special-panel *[title]"),function(t){t.e.add({mouseover:function(e){DizSel.fns.floatTip.toolTip.call(t,e),DizSel.fns.floatTip.moveTip(e)},mouseout:"DizSel.fns.floatTip.obj.hidden=1"}),t["data-title"]=t["data-title"]||t.title,t.title=""}))},_constr:function(){var t={moveTip:function(t){t=_K.Event.fix(t);var e=getComputedStyle(DizSel.fns.floatTip.obj),i=t.pageX,l=t.pageY;DizSel.fns.floatTip.obj.style.left=(i+parseInt(e.width)+DizSel.sts.floatTip.distX<_K.body().clientWidth+_K.scroll.left?i+DizSel.sts.floatTip.distX:i-parseInt(e.width)-DizSel.sts.floatTip.distX)+"px",DizSel.fns.floatTip.obj.style.top=(l+parseInt(e.height)+DizSel.sts.floatTip.distY<_K.body().clientHeight+_K.scroll.top?l+DizSel.sts.floatTip.distY:l-parseInt(e.height)-DizSel.sts.floatTip.distY)+"px"},toolTip:function(){_K.G(DizSel.fns.floatTip.obj,this["data-title"]).hidden=0}};return _K.clonePpts(DizSel.fns.floatTip,t,{"enum":!0})}},lightCur:function(){},stylePU:function(t,e){e=e||{opacity:.1,scale:.5},t.style.opacity=e.opacity},zoom:function(){}},SaveFS:function(t){if(!isNaN(t)){DizSel.v.ch=t;var e=+(Cook.get("diz_alt_fs")||0)+Math.sign(t);DizSel.sts.fontSize.fixed?Cook.set({diz_alt_fs:Math.sign(t)},DizSel.sts.mem):Math.abs(e)>DizSel.sts.fontSize.iter||Cook.set({diz_alt_fs:e},DizSel.sts.mem),DizSel.fns.getNodes(DizSel.fns.setFS)}},createBGs:function(){function t(t){var e=t?this[t]:this,i=(e.in_sts?DizSel.PU_sts:DizSel.v.BG).cr("i",e.ppts);i.classList.add("imgPU"),i.focus=!1,!!e.e&&(i.onclick=e.e),!!e.st&&_K.G(i,e.st)}DizSel.Backup.save(),DizSel.PUfr=_K.G(document.createDocumentFragment()),DizSel.PU=DizSel.PUfr.cr("div",{id:"special-panel","class":"no-size",style:" opacity:.9; "}),DizSel.inPU=DizSel.PU.cr("div",{style:"margin: 0 auto; display: inline-block;"}),DizSel.PU.e.add("select",function(t){_K.Event.fix(t).preventDefault()}),DizSel.SlabovidButton=DizSel.SlabovidButton||_K.G(DizSel.SlabovidButtonParent).cr("input",{type:"button","class":"imgPU no-size btn min700 DA_del",style:'padding: 1px 5px 1px 50px; margin:0 auto; font-size:20px; background: url("'+DizSel.checkUrl+'eye.gif") no-repeat scroll 3px center, #ddd linear-gradient(#cccccc, #fafafa) repeat scroll 0 0;border: 1px solid #aaaaaa; border-radius:5px; z-index:9949; position:static; left:5px; top:20px; cursor:pointer;',id:"diz_alt",value:"ДЛЯ СЛАБОВИДЯЩИХ"},"fCh"),DizSel.SlabovidButton.e.add("click",DizSel.addStyleSheet),"function"==typeof DizSel.sts.button.callback&&DizSel.sts.button.callback(),this.v.BG=this.v.BG||DizSel.inPU.cr("div",{"class":"no-size flex",style:"margin: 0 auto; display: flex;"}),DizSel.PU_sts=DizSel.PU_sts||DizSel.inPU.cr("div",{hidden:1}).cr("div",{"class":"flex",style:"display: flex;"}),_K.G("$script[src*='DA_pro_v3_B.js']")&&(DizSel.version=DizSel.version+"_beta"),DizSel.v.BG.cr("div",{style:"position: absolute; right: 0;"}).innerHTML="v "+DizSel.version,DizSel.changeCol=DizSel.v.BG.cr("input",{type:"color","class":"imgPU",title:"цвет ТЕКСТА",style:"width:50px;height:30px;float:left; margin:10px 20px;padding:0;",value:Cook.get("diz_alt_Col")||DizSel.sts.startCol()}),Object.keys(DizSel.sts.BG_dim).forEach(function(e){var i=function(t){return function(){Cook.set({diz_alt_BG:t},DizSel.sts.mem),Cook.set({diz_alt_Col:DizSel.sts.startCol()},DizSel.sts.mem),DizSel.CheckCook(),DizSel.fns.getNodes(DizSel.fns.setCol)}}(e),l={ppts:{"class":"sprite-"+e,title:DizSel.sts.BG_dim[e]},e:i,st:{margin:"0 5px"},in_sts:!0};t.call(l)});var e=[{fontSizeS:{ppts:{"class":"sprite-fontBig",title:"Уменьшить"},e:function(){DizSel.SaveFS(-DizSel.sts.fontSize.step)},st:{marginLeft:"20px"}}},{fontSizeB:{ppts:{"class":"sprite-fontBig_50",title:"Увеличить"},e:function(){DizSel.SaveFS(DizSel.sts.fontSize.step)}}},{fontType:{ppts:{"class":"sprite-text",title:"Тип шрифта"},e:DizSel.fns.toggleStyle.bind(null,"fontType"),in_sts:!0}},{kern:{ppts:{"class":"sprite-kern",title:"Изменить интервал"},e:DizSel.fns.toggleStyle.bind(null,"kern"),in_sts:!0}},{lh:{ppts:{"class":"sprite-kern",title:"Высота строки"},e:DizSel.fns.toggleStyle.bind(null,"lineHeight"),st:{transform:"rotate(90deg)"},in_sts:!0}},{imageOff:{ppts:{"class":"sprite-imageOff",title:"Показ изображений",alt:"Показ изображений"},e:DizSel.fns.imageOff,in_sts:!0}},{sound:{ppts:{"class":"sprite-sound",title:"Озвучивать выделенный текст",alt:"Озвучивать выделенный текст"},e:DizSel.fns.sound}},{sts:{ppts:{"class":"sprite-settings",title:"Настройки отображения сайта",alt:"Настройки отображения сайта"},e:function(){DizSel.PU_sts.parentNode.hidden=!DizSel.PU_sts.parentNode.hidden}}},{toDefault:{ppts:{"class":"sprite-toDefault",title:"Обычный вид",id:"toDefault"},e:function(){DizSel.addStyleSheet()},st:{margin:"3px 20px 0"}}}];DizSel.changeCol.onchange=function(){Cook.set({diz_alt_Col:this.value},DizSel.sts.mem),DizSel.fns.getNodes(DizSel.fns.setCol)},e.forEach(function(e){t.call(e,Object.keys(e)[0])}),DizSel.Backup.altObj.Append(DizSel.PUfr,"fCh"),DizSel.v.DAlt&&DizSel.PUopacity()}},window.oldStable||DizSel.inited||(DizSel.addons.puny=DizSel.checkUrl&&_K.G("$head").cr("script",{src:DizSel.checkUrl+"punycode.js",async:0}),DizSel.addons.db=_K.G("$head").cr("script",{src:DizSel.path+"js/db/db_DA_pro_3?req="+_K.fns.rnd(0,1e5),async:0}),/bereghost\.ru/.test(location.host)&&DizSel.noFA(),_K.Event.add(window,"load",DizSel.init));