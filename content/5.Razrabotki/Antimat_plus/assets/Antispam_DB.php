<?php
header('Access-Control-Allow-Origin: *');
header('content-type: application/x-javascript; charset=utf-8');
?>

/* utf8-marker = äöüß */
var Antispam = Antispam || {};

Antispam.spams= [
	'(?:вoзьмитe|выберите) пepвый нoмep кoшeлькa|удалить из списка первый кошел[её]к|первый кошелёк из списка|необходимо отправить на каждый|(?:Отправить на эти|НА КАЖДЫЙ ИЗ ЭТИХ|надо послать по \\d+ руб\\.? на) \\d\\s?.*?кошельков|внесите меня в список (?:Yandex|Webmoney)\\s?\\-\\s?кошельков|ВАШ ВКЛАД СОСТАВЛЯЕТ ВСЕГО|в ЦЬОМУ випадку я ДІЙСНО можу заробити|МОЖНА ЗРОБИТИ ТИСЯЧІ ГРИВЕНЬ|СКОРИСТ[АУ]ЙТЕСЯ РЕАЛЬНИМ ШАНСОМ ЗАРОБИТИ|превратятся в реальные деньги|ДЕНЬГИ БЕЗ УСИЛИЙ|рассылать эти объявления по(?: разным)? форумам',
	'ВАШ УСПЕХ|ЧЕМ БОЛЬШЕ Вы разместите, ТЕМ|возможност[иь] изменить свою жизнь',
	'практически не требует вложений|и (?:другие )?люди начинают отправлять Вам деньги|Первую неделю денег поступает очень мало|Волшебный кошел[её]к',
	'Продаю ключи от смс рассылки|Постинг по форумам[\\s\\S]+прогон по профилям',
	'каждый житель планеты|Gold.?Line|Голд.?лайн|распределяются между \\d+? уровнями рефереров',
	'(?:рассылаете|разошл[её]те)(?: эту)? информацию своим (?:друзьям|знакомым)',
	'поделиться этой супер\W*|деньги практически ничего ни делая|cyberseller.ru',
	'Если ВАМ НУЖНЫ ДЕНЬГИ|v-vakhrusheva.narod.ru',
	'Венера Некст|Каждый получает деньги|Вы платите только (?:1|один) раз',
	'Wmzona|ВМЗОНА',
	'(?:[3z]a)?[rp]a[b8]ot|platim|.*invest.*\\.|ruletka|casino|Онлайн Казино',

	'((infoarena|testu\\-klici)\\.ucoz|(zerkalo77|vetersanya|z-27-naro)\\.narod2|anubis26.jino|seosprintinfo|cash\\-fast|millioner666.fo|greemleen\\.mybb|daripodarki|1ite-net|vefx|avto-razvi|free1ite|allmlm|megastock|job|mnogo.a5|2xqiwi|c-fast|clck|\\.fo|urla|verywellads|alkerz|goldgnomes|air-pump|allinway|daode|perviicapital|jd4|ekzoticsad.3dn|misol|trafka|finance\\d+|ucssocks|mawa-medvedy|fermasosedi|asfat|webrabota\\d+|metro-ccc|pgmx|kbmoskva|homeconstructions/gruz-rm|docteam|llmanikur|niagarastar)\\.ru',
	'(eic\\-ee|2druga|sunflowersptr|instantptr|disserlib|proforexunion|KisAB.dragdvd.e-autopay|pochty|ssilka.ucoz|rustabilityfx|jobweblanc|uves\\-uqokegutiz.freewaywebhost|org\\-job|jimdo|gazku|cashmagnat|business\\-corp|orilider|ref\\-potok|SBNLife|real-fishing|globus-inter|socpublic|d4mp1n@gmail|proxylistpro)\\.com',
	'(redatumax|cashcenter|ru-internet)\\.info',
	'(piramida\\-fenix|globalmutual|100x100x100|outcomebet)\\.biz',
	'((academic|zarabitoknadomu)\\.ucoz|200euromails|silvanamails|seosprint|2dors|bobfilm)\\.net',
	'u.to(\\/bm7JDg)',
	'vk\\..{2,3}\\/(5dMKVV|2dmFlp|3momeQ|partnerkipro)',

	'shift.sonett.in|freelancer.webork.eu|sunets.eu|texnolend.su|goo.gl|gd-s.org|yadi.sk|tamakarova1968mail\\-ru|worldis.me|clan.su|freesoftpost.hol.es|lady\\-job2012|Money\\-Maker|goldenbirds|good-farm\\.tk|unionofcharity\\.org|mir\\-carding.blogspot.de|serverpremium.work|MOCKERY60.RU|Forex4you|userclick.su|cashbackmarketing.online|freeshopping.club|perfectsear.ch',
	'dijakorob1933@seznam.cz|Предлагаем рассылку|ПОДОБРАТЬ ДЛЯ ВАС ПАРОЛЬ|(Профессиональный|Анонимный)? взлом почты (без предоплаты)?|(профессиональные услуги по|срок|доказательства) взлом[уа]|real.hacking',
	'За каждый пост на форуме вы будете получать|2524a24ba8914c',
	'public51121301|service7581|RoboForex|forex\\s?bonus',
	'СПБ|ЦМТ|8.905.596.55.49',
	'GLOBAL.ONE|Regulest|CloNez',
	'leha.utegenov|Webtransfer'
];

Antispam.mat=[
	'(?:\\s|^|>|\\]).{0,4}?[хx][\\s_]*?[уy](?![бж])[\\s_]*?[ийеёюя](?![з])', //(?:.|.?\\[.+?\\].?)?
	'п(?![ор]).?[еёиі].{0,2}?[зz3].{0,2}?д[а@]?',
	'(?:[^аеор]|\\s|^)[бм]и?ля[дт]ь?|п[еи][дg][ао]?р',
	'г[ао]вно?|г[оа]ндон|жоп[аеу]|[^о]мандав?[^лрт]',
	'(?:[^вджл-нр-тчш]|^|\\s)[ьъ]?[еёїє]б\\W*?[^ы\\s]',
	'сра[лт]ь?|залупа?|дроч',
	// фразы
	'сос[иу] (?:член|хуй|хер)|(?:член|хуй|хер) сос[иу]'
];