<style type="text/css" media="screen">
	.api {width:40%; min-width: 190px; white-space:pre-wrap; word-break: break-all; font: bold 120% monospace; border:1px solid; float:left}
	.rem {width:60%; min-width: 185px; float:right; padding: 0 0 0.5em 1em;}
	.rem::after {content:''; clear:both;}
	.api, .rem { margin: 10px 0; }
	@media (max-width: 500px) {
		.rem {word-break: break-all;}
	}
</style>


<h2>Мануал по API скрипта. Версия PRO-3</h2>

<div style="padding: 20px;">
	ПУ - панель управления.
	РДС - режим для слабовидящих.
	<blockquote><p>На всякий случай напоминаю, что все скриптовые настройки должны находиться в блоке между тегами &lt;script type="text/javascript"&gt; и &lt;/script&gt;. Сам блок лучше размещать в нижней части сайта (футере). Иначе сайт может перестать корректно работать, а в отладочной консоли появится запись, что DizSel не определен. При такой ситуации блок с настройками нужно разместить еще "ниже" в общей верстке страницы, то есть - ближе к тегу <b>&lt;/body&gt;</b> </p>
	</blockquote>


	<h3>Настройка кнопки вызова скрипта</h3>

	<h4>Использование своего изображения вместо стандартной кнопки</h4>
		<div class="api" >DizSel.sts.button.addImg ("адрес изображения", "стили CSS для изображения")</div>
		<div class="rem">Кнопка по умолчанию поменяется на изображение. Например, это может выглядеть так: <b>DizSel.sts.button.addImg("/on.jpg", "width:100px;cursor:pointer;border:1px solid #117;padding:0")</b></div>
		<div class="clear"></div>

	<h4>Переопределение положения кнопки</h4>
	<p>Если нужно переместить кнопку вызова скрипта в конкретный блок сайта, достаточно выполнить следующие действия:</p>
		<div class="api" >&lt;div id="<span class="red">but</span>"&gt; Тут может быть и другое содержимое &lt;/div&gt;</div>
		<div class="rem">Создать блочный элемент внутри тела html-разметки (между тегами &lt;body&gt; и &lt;/body&gt;) с назначенным идентификатором, либо назначить идентификатор существующему блоку</div>
		<div class="clear"></div>
		<div class="api" >DizSel.SlabovidButtonParent= _K.G('#<span class="red">but</span>');</div>
		<div class="rem">Указать в настройках скрипта идентификатор нужного блока в приведенном формате. В результате кнопка переместится в указанный блок, став его первым потомком. Работает после выполнения предыдущих инструкций.</div>
		<div class="clear"></div>

	<h4>Изменение/добавление стилей кнопки</h4>
		<div class="api" >_K.G(DizSel.SlabovidButton, {position:'fixed', top:'5px', right:'5px'});</div>
		<div class="rem">Изменение стилей кнопки вызова скрипта. В данном примере - фиксирование ее положения на странице, игнорируя вертикальную прокрутку.</div>
		<div class="clear"></div>
		<div class="api" >_K.G(DizSel.SlabovidButton, { backgroundColor:'transparent', border:'none' });</div>
		<div class="rem">В этом примере убираем фон и рамку кнопки, оставляя только текст и изображение. Вообще данным приемом можно менять любые стили кнопки, полностью преображая ее.</div>
		<div class="clear"></div>
		<div class="api" >DizSel.sts.button.image= 'blind.png';</div>
		<div class="rem">Смена изображения на кнопке с глаза на очки <img class="none" src="/css/Slabovid_PRO_v3/blind.png" style="margin:0 7px;" alt="Для слабовидящих" /></div>
		<div class="clear"></div>


	<h3>Настройка Панели Управления (ПУ) скриптом</h3>

	<h4>Определение срока сохранения настроек</h4>
		<p>Введенные настройки броузер сохраняет определенное время, после которого они меняются на дефолтные. Определить время сохрнанения настроек в днях поможет следующая опция.</p>
		<div class="api" >DizSel.sts.mem=15;</div>
		<div class="rem">Броузер будет сохранять введенные настройки в ПУ указанное количество дней.</div>
		<div class="clear"></div>

		<h4>Анимация прозрачности ПУ</h4>
		<p>По умолчанию - ПУ становится почти прозрачной после отведения от неё курсора. По просьбам пользователей, с версии 3.6.2 добавлена настройка, регулирующая это поведение.</p>
		<div class="api" >DizSel.sts.PUanimate=false;</div>
		<div class="rem">Отключение анимации прозрачности. ПУ будет всегда непрозрачной.</div>
		<div class="clear"></div>


	<h4>Изменение положения ПУ</h4>
		<p>Если по каким-то соображениям вам будет необходимо перенести ПУ вниз области экрана, это легко сделать следующим образом.</p>
		<div class="api" >DizSel.created(function() {_K.G(DizSel.PU, {top:'unset', bottom:0}) });</div>
		<div class="rem">Отменяем привязку ПУ к верху сайта (положение по умолчанию) и перемещаем вниз области видимости сайта. Позиция ее остается по-прежнему фиксированной при вертикальной прокрутке страницы. При очень малых разрешениях экрана панель переместится в нижнюю область сайта автоматически.</div>
		<div class="clear"></div>

	<h4>Уменьшение/увеличение размера шрифта <img class="none" src="/css/Slabovid_PRO_v3/fontBig.png" alt="Уменьшить шрифт" style="width:30px; margin:0 7px;" /><img class="none" src="/css/Slabovid_PRO_v3/fontBig.png" alt="Увеличить шрифт" style="margin:0 7px;" /></h4>
		<p>Вариантов изменения размера шрифта предусмотрено два: фиксированный размер при нажатию на каждую из пиктограмм и итерационный, изменяющий размер постепенно до max/min значения. В настройках по умолчанию установлен 2-й вариант, но это можно изменить следующим кодом: </p>
		<div class="api" >DizSel.sts.fontSize.min= 12;</div>
		<div class="rem">Минимальный размер шрифта в РДС</div>
		<div class="clear"></div>
		<div class="api" >DizSel.sts.fontSize.step= 2;</div>
		<div class="rem">Шаг изменения размера шрифта (в px) при итерационном изменении</div>
		<div class="clear"></div>
		<div class="api" >DizSel.sts.fontSize.iter= 3;</div>
		<div class="rem">Количество шагов изменения размера шрифта при итерационном изменении. То есть шрифт изменится на 3рх не более 3 раз как в сторону увеличения, так и в сторону уменьшения. На мой взгляд это является оптимальной настройкой.</div>
		<div class="clear"></div>
		<div class="api" >DizSel.sts.fontSize.fixed= true;</div>
		<div class="rem">Фиксируем размер шрифта в фиксированный формат min/max. При true - предыдущая настройка (<b>DizSel.sts.fontSize.iter</b>) перестает работать, поскольку при первом же нажатии на кнопку Увеличить/Уменьшить размер шрифта сразу же примет значение, указанное в настройке <b>DizSel.sts.fontSize.step</b>, добавленное/вычетое к стандартному значению величины шрифта. При false - итерационное изменение (по умолчанию).<br /></div>
<div class="clear"></div>
		<div>
			Стоит уточнить, что при фиксированном изменении размер шрифта будет увеличен/уменьшен на величину <b>DizSel.sts.fontSize.step</b> относительно своего первоначального размера. То есть, при дефолтных настройках величина шрифта при увеличении / уменьшении изменится на +2/-2 рх соответственно.
		</div>

	<h4>Показ изображений <img class="none" src="/css/Slabovid_PRO_v3/imageOff.png" style="margin:0 7px;" alt="Показ изображений" /></h4>
		<div class="api" >DizSel.sts.imageOff.minImg= 70;</div>
		<div class="rem">Изменение минимальной ширины изображений, с которыми работает модуль <b>Показ изображений</b>.</div>
		<div class="clear"></div>

	<h4>Настройка модуля озвучивания сайта <img class="imgPU" src="/css/Slabovid_PRO_v3/sound.png" alt="Озвучивать выделенный текст" /></h4>
		<div class="api" >DizSel.created( function() {DizSel.sts.sound.speaker= "ermil"; })</div>
		<div class="rem">Изменение голоса диктора. Возможные варианты можно <a href="https://webasr.yandex.net/speakers?engine=ytcp&lang=ru" target="_blank" rel="nofollow">посмотреть здесь</a>. На этой странице используется "ermil".</div>
		<div class="clear"></div>
		<div class="api" >DizSel.created( function() {DizSel.sts.sound.emotion= "good"; })</div>
		<div class="rem">Изменение интонации голоса диктора</div>
		<div class="clear"></div>
		<div class="api" >DizSel.created( function() {DizSel.sts.sound.speed= 1.1; })</div>
		<div class="rem">Изменение скорости чтения текста</div>
		<div class="clear"></div>
		<div class="api" >DizSel.created( function() {DizSel.sts.sound= {speaker:"zahar", speed:.9}; })</div>
		<div class="rem">Пример комплексной настройки модуля озвучивания</div>
		<div class="clear"></div>

	<h3>Кастомизация отображения в РДС</h3>
	<h4>Удаление "лишних" блоков присваиванием класса "DA_del"</h4>
		<div class="api">class="DA_del"</div>
		<div class="rem">Если вы не хотите отображения каких-то блоков в РДС, например, где-то возникли пустые места вместо фоновых изображений, - присвойте им class="DA_del". Мультиклассовость поддерживается, то есть, можно добавлять к существующему классу class="My_Class DA_del".</div>
		<div class="clear"></div>
	<h4>Изменение стилей