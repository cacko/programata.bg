<?php
if (!$bInSite) die();
//=========================================================
// site labels used in Public Interface
//=========================================================
    $aLabel[LANG_BG]['strSiteTitle'] = 'Програмата + ТВ';
    $aLabel[LANG_BG]['strMoto'] = 'безплатен културен гайд';
    $aLabel[LANG_BG]['strSiteDescription'] = 'Програмата е ежедневно обновявана програма на културните събития в София, Варна, Бургас, Пловдив и Стара Загора: филми, постановки, изложби, изложения, класическа и съвременна музика, концерти, партита, както и подробна информация за заведенията.';
    $aLabel[LANG_BG]['strSiteKeywords'] = 'кино, филми, театър, изложба, музей, класика, концерти, култура, фестивал, музика на живо, клубове, барове, ресторанти';
    $aLabel[LANG_BG]['strCopy'] = 'Програмата Медия Груп. Всички права запазени.';
//=========================================================
// entities used in pages of Admin Interface
//=========================================================
    $aTemplate[LANG_BG][ENT_HOME] = 'Начало';
    $aTemplate[LANG_BG][ENT_USER] = 'Потребители';
    $aTemplate[LANG_BG][ENT_PAGE] = 'Страници';
    //$aTemplate[LANG_BG][ENT_LOOKUP] = 'Категории';
    $aTemplate[LANG_BG][ENT_NEWS] = 'Новини';
    $aTemplate[LANG_BG][ENT_PUBLICATION] = 'Интервюта';
    $aTemplate[LANG_BG][ENT_FESTIVAL] = 'Фестивали';
	$aTemplate[LANG_BG][ENT_URBAN] = 'Градски + х3';
	$aTemplate[LANG_BG][ENT_MULTY] = 'Jacobs + Разходка + План';
	$aTemplate[LANG_BG][ENT_EXTRA] = 'Екстра';
    $aTemplate[LANG_BG][ENT_COMMENT] = 'Коментари';
    $aTemplate[LANG_BG][ENT_LABEL] = 'Категории';
    $aTemplate[LANG_BG][ENT_PLACE] = 'Места';
    $aTemplate[LANG_BG][ENT_EVENT] = 'Събития';
    $aTemplate[LANG_BG][ENT_PROMOTION] = 'Заглавни';
    //$aTemplate[LANG_BG][ENT_JOB] = 'Обяви за работа';
    $aTemplate[LANG_BG][ENT_PROGRAM] = 'Програма';
    $aLabel[LANG_BG]['strEntPlaceNote'] = ' (Къде)';
    $aLabel[LANG_BG]['strEntEventNote'] = ' (Какво)';
    $aLabel[LANG_BG]['strEntProgramNote'] = ' (Какво, Къде, Кога)';
//=========================================================
// related entities used in pages of Admin Interface
//=========================================================
    $aRelatedTemplate[LANG_BG][ENT_ATTACHMENT] = 'Файлове';
    $aRelatedTemplate[LANG_BG][ENT_LINK] = 'Линкове';
    $aRelatedTemplate[LANG_BG][ENT_EMAIL] = 'E-mail-и';
    $aRelatedTemplate[LANG_BG][ENT_ADDRESS] = 'Адреси';
    $aRelatedTemplate[LANG_BG][ENT_PHONE] = 'Телефони';
    $aRelatedTemplate[LANG_BG][ENT_PLACE_HALL] = 'Зали';
    $aRelatedTemplate[LANG_BG][ENT_DATE_PERIOD] = 'Периоди';
    $aRelatedTemplate[LANG_BG][ENT_DATE_TIME] = 'Дата-час';
    $aRelatedTemplate[LANG_BG][ENT_PLACE_GUIDE] = 'Place guide';
//=========================================================
// order statuses
//=========================================================
    $aLabel[LANG_BG]['aUserStatus'] = array(USER_GUEST=>'Неактивен потребител',
                                            USER_REGULAR=>'Регистриран потребител',
                                            USER_ADMIN=>'Администратор');
//=========================================================
// intro labels used in Admin Interface
//=========================================================
    $aLabel[LANG_BG]['strAdmin'] = "Администрация";
    $aLabel[LANG_BG]['strAdminManual'] = 'Ръководство за администрация';
    $aLabel[LANG_BG]['strAdminTitle'] = 'Programata.bg - Администрация';
    $aLabel[LANG_BG]['strAdminWelcome'] = 'Добре дошли в администрацията на уебсайта на Programata.bg. Моля изберете елемент от менюто, за да продължите.';
//=========================================================
// Login labels
//=========================================================
    $aLabel[LANG_BG]['strUsername'] = 'Потребителско име';
    $aLabel[LANG_BG]['strPassword'] = 'Парола';
    $aLabel[LANG_BG]['strOldPassword'] = 'Стара парола';
    $aLabel[LANG_BG]['strNewPassword'] = 'Нова парола';
    $aLabel[LANG_BG]['strNewPassword2'] = 'Повтори паролата';
    $aLabel[LANG_BG]['strMatchFailed'] = 'Паролите не съвпадат.';
    $aLabel[LANG_BG]['strLogin'] = 'вход';
    $aLabel[LANG_BG]['strDoLogin'] = 'вход';
    $aLabel[LANG_BG]['strLogout'] = 'изход';
    $aLabel[LANG_BG]['strDoLogout'] = 'изход';
    $aLabel[LANG_BG]['strQuestions'] = 'Въпроси';
    $aLabel[LANG_BG]['strWhyToRegister'] = 'Защо да се регистрирам?';
    $aLabel[LANG_BG]['strRememberMe'] = 'Запомни ме';// на този компютър
    $aLabel[LANG_BG]['strInvalid'] = 'Въвели сте грешно потребителско име и/или парола, или не сте активирали паролата си. Моля опитайте отново.';
    $aLabel[LANG_BG]['strLogoutOK'] = 'Вие излязохте успешно от Programata.bg.';
    $aLabel[LANG_BG]['strLoginOK'] = 'Вие влязохте успешно в Programata.bg.';
    $aLabel[LANG_BG]['strLoginIntro'] = 'Моля въведете вашите потребителско име и парола.';
    $aLabel[LANG_BG]['strNewsletters'] = 'Абонамент';
    $aLabel[LANG_BG]['aNewsletterCategories'] = array(1=>'Classic',
                                                      2=>'Family &amp; Entertainment',
                                                      3=>'Folk',
                                                      4=>'Highlights from all different categories',
                                                      5=>'Music',
                                                      6=>'&Ouml;T Special Offers',
                                                      7=>'Sports',
                                                      8=>'Theatre &amp; Cabaret');
//=========================================================
// site labels used in forms
//=========================================================
    $aLabel[LANG_BG]['strRequired'] = 'Моля попълнете всички полета, отбелязани със звездички ('.formatVal().').';
    $aLabel[LANG_BG]['strAnyRequired'] = 'Моля попълнете поне едно от полетата, отбелязани със звездички ('.formatVal().').';
    $aLabel[LANG_BG]['strSendOK'] = 'Вашето съобщение беше изпратено успешно.';
    $aLabel[LANG_BG]['strSendFailed'] = 'Вашето съобщение не беше изпратено. Моля <a href="#"  onclick="history.back();return false;">върнете се</a> и опитайте отново.';
    $aLabel[LANG_BG]['strSend'] = 'Изпрати';
    $aLabel[LANG_BG]['strRetrieveOK'] = 'Намерени са данни, отговарящи на вашите условия.';
    $aLabel[LANG_BG]['strRetrieveFailed'] = 'Не са намерени данни, отговарящи на вашите условия. Моля <a href="#"  onclick="history.back();return false;">върнете се</a> и опитайте отново.';
    $aLabel[LANG_BG]['strFind'] = 'Намери';
    $aLabel[LANG_BG]['strUserDataOK'] = 'Съобщението с данните ви за влизане беше изпратено успешно на посочения от вас e-mail адрес.';
    $aLabel[LANG_BG]['strUserDataFailed'] = 'Съобщение с данните ви за влизане не беше изпратено на посочения от вас e-mail адрес.';
    $aLabel[LANG_BG]['strUserLogin'] = 'Можете да продължите работата си като регистриран потребител, след като влезете в сайта.';
    $aLabel[LANG_BG]['strUsernameTaken'] = 'Въведеното потребителско име е заето. Моля <a href="#"  onclick="history.back();return false;">върнете се</a> и опитайте отново.';
    $aLabel[LANG_BG]['strUserWelcome'] = 'Здравейте, <strong>%name</strong>!<br /><br />След като успешно влязохте в сайта, можете да коментирате събитията, статиите, новините, да оценявате заведенията, да добавяте събития в личния си календар.';
    $aLabel[LANG_BG]['strRegister'] = 'Регистрирам се';
    $aLabel[LANG_BG]['strSaveOK'] = 'Въведените от вас данни са запазени.';
    $aLabel[LANG_BG]['strSaveFailed'] = '<span class="err">Въведените от вас данни не са запазени.</span> Моля <a href="#"  onclick="history.back();return false;">върнете се</a> и опитайте отново.';
    $aLabel[LANG_BG]['strSave'] = 'Запази';
    $aLabel[LANG_BG]['strReload'] = 'Презареди';
//=========================================================
// Registration
//=========================================================
    $aLabel[LANG_BG]['strRegistrationTitle'] = 'Добре дошли в Programata.bg';
    $aLabel[LANG_BG]['strRegistrationMessage'] = 'Здравейте, %name,<br />
Благодарим ви, че се регистрирахте в уебсайта на Programata.bg.<br />
<br />
Потребителското ви име е: %user<br />
Паролата ви е: %pass<br />
<br />
За да активирате паролата си, моля посетете следния линк (или го копирайте в адресната лента на браузъра си):<br />
%link<br />
<br />
Екипът на Programata.bg.<br />
<a href="http://'.SITE_URL.'">'.SITE_URL.'</a>.<br />';
//=========================================================
// Reminder
//=========================================================
    $aLabel[LANG_BG]['strReminderMessage'] = 'Здравейте, %name,<br />
Личният Ви календар в сайта на Programata.bg напомня, че имате план за деня.<br />
Можете да ни посетите на адрес <a href="http://'.SITE_URL.'">'.SITE_URL.'</a>.
<br />
Екипът на Programata.bg.<br />';
//=========================================================
    $aLabel[LANG_BG]['strFirstName'] = 'Име';
    $aLabel[LANG_BG]['strLastName'] = 'Фамилия';
    $aLabel[LANG_BG]['strFullName'] = 'Име и фамилия';
    $aLabel[LANG_BG]['strProfession'] = 'Професия';
    $aLabel[LANG_BG]['strInterests'] = 'Интереси';
    $aLabel[LANG_BG]['strSex'] = 'Пол';
    $aLabel[LANG_BG]['aSex'] = array(1=>'Мъж', 2=>'Жена');
    $aLabel[LANG_BG]['strAge'] = 'Възраст';
    $aLabel[LANG_BG]['strBirthday'] = 'Дата на раждане';
    $aLabel[LANG_BG]['strCompany'] = 'Фирма';
    $aLabel[LANG_BG]['strCompanyActivity'] = 'Дейност на фирмата';
    $aLabel[LANG_BG]['strPosition'] = 'Длъжност';
    $aLabel[LANG_BG]['strAdType'] = 'Вид реклама';
    $aLabel[LANG_BG]['aAdTypes'] = array(1=>'online, в уебсайта на Програмата',
                                         2=>'offline, в печатните издания',
                                         3=>'и в двете издания');
    $aLabel[LANG_BG]['strAdDescription'] = 'Кратко описание на идеята ви за реклама';
    $aLabel[LANG_BG]['strPhone'] = 'Телефон';
    $aLabel[LANG_BG]['strEmail'] = 'E-mail';
    $aLabel[LANG_BG]['strMessage'] = 'Предложения, въпроси или препоръки';//'Съобщение';
    $aLabel[LANG_BG]['strAddress'] = 'Адрес';
    $aLabel[LANG_BG]['strCity'] = 'Град';
    $aLabel[LANG_BG]['strCountry'] = 'Държава';
    $aLabel[LANG_BG]['strDownload'] = 'Полезна информация';
    //$aLabel[LANG_BG]['strProgramataTGI'] = 'Програмата Таргет Груп Индекс 2004';
    //$aLabel[LANG_BG]['strReadersProfile'] = 'Читателски профил 2004';
    //$aLabel[LANG_BG]['strPricelistOffline'] = 'Ценова листа печатни издания';
    //$aLabel[LANG_BG]['strPricelistOnline'] = 'Ценова листа уебсайт';
    $aLabel[LANG_BG]['strStats'] = 'Статистика за уебсайта';
    $aLabel[LANG_BG]['strFriendName'] = 'Име на получателя';
    $aLabel[LANG_BG]['strFriendEmail'] = 'E-mail на получателя';
    $aLabel[LANG_BG]['strDefaultDate'] = ' <span class="note">(ДД.ММ.ГГГГ)</span>';
    $aLabel[LANG_BG]['strDefaultTime'] = '08:00 - 18:00';
    $aLabel[LANG_BG]['aMonths'] = array(1=>'януари', 2=>'февруари', 3=>'март', 4=>'април', 5=>'май', 6=>'юни', 7=>'юли', 8=>'август', 9=>'септември', 10=>'октомври', 11=>'ноември', 12=>'декември');
    $aLabel[LANG_BG]['aMonthsShort'] = array(1=>'ян', 2=>'фев', 3=>'март', 4=>'апр', 5=>'май', 6=>'юни', 7=>'юли', 8=>'авг', 9=>'септ', 10=>'окт', 11=>'ноем', 12=>'дек');
    $aLabel[LANG_BG]['aDays'] = array(1=>'понеделник', 2=>'вторник', 3=>'сряда', 4=>'четвъртък', 5=>'петък', 6=>'събота', 7=>'неделя');
    $aLabel[LANG_BG]['aDaysShort'] = array(1=>'пон', 2=>'вто', 3=>'сря', 4=>'чет', 5=>'пет', 6=>'съб', 7=>'нед');
    $aLabel[LANG_BG]['aAlphabet'] = array(
            0=>'Всички', 1=>'1', 2=>'2', 3=>'3', 4=>'4', 5=>'5', 6=>'6', 7=>'7', 8=>'8', 9=>'9', 10=>'0',
            11=>'a', 12=>'b', 13=>'c', 14=>'d', 15=>'e', 16=>'f', 17=>'g', 18=>'h', 19=>'i', 20=>'j',
            21=>'k', 22=>'l', 23=>'m', 24=>'n', 25=>'o', 26=>'p', 27=>'q', 28=>'r', 29=>'s', 30=>'t',
            31=>'u', 32=>'v', 33=>'w', 34=>'x', 35=>'y', 36=>'z', //37=>'', 38=>'', 39=>'', 40=>'',
            41=>'а', 42=>'б', 43=>'в', 44=>'г', 45=>'д', 46=>'е', 47=>'ж', 48=>'з', 49=>'и', 50=>'й',
            51=>'к', 52=>'л', 53=>'м', 54=>'н', 55=>'о', 56=>'п', 57=>'р', 58=>'с', 59=>'т', 60=>'у',
            61=>'ф', 62=>'х', 63=>'ц', 64=>'ч', 65=>'ш', 66=>'щ', 67=>'ъ', 68=>'ь', 69=>'ю', 70=>'я');
    $aLabel[LANG_BG]['aAlphabetGroups'] = array(1=>'0-9', 2=>'A-H', 3=>'I-N', 4=>'O-T', 5=>'U-Z', 6=>'А-З', 7=>'И-О', 8=>'П-У', 9=>'Ф-Я');
    $aLabel[LANG_BG]['aAlphabetGroupsLetters'] = array(
		1=>array('1', '2', '3', '4', '5', '6', '7', '8', '9', '0'),
		2=>array('a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'),
		3=>array('i', 'j', 'k', 'l', 'm', 'n'),
		4=>array('o', 'p', 'q', 'r', 's', 't'),
		5=>array('u', 'v', 'w', 'x', 'y', 'z'),
		6=>array('а','б','в','г','д','е','ж','з'),
		7=>array('и','й','к','л','м','н','о'),
		8=>array('п','р','с','т','у'),
		9=>array('ф','х','ц','ч','ш','щ','ъ','ь','ю','я')
    );
    $aLabel[LANG_BG]['aAlphabetLettersKeys'] = array(
		'1'=>1, '2'=>1, '3'=>1, '4'=>1, '5'=>1, '6'=>1, '7'=>1, '8'=>1, '9'=>1, '0'=>1,
		'a'=>2, 'b'=>2, 'c'=>2, 'd'=>2, 'e'=>2, 'f'=>2, 'g'=>2, 'h'=>2,
                'A'=>2, 'B'=>2, 'C'=>2, 'D'=>2, 'E'=>2, 'F'=>2, 'G'=>2, 'H'=>2,
		'i'=>3, 'j'=>3, 'k'=>3, 'l'=>3, 'm'=>3, 'n'=>3,
                'I'=>3, 'J'=>3, 'K'=>3, 'L'=>3, 'M'=>3, 'N'=>3,
		'o'=>4, 'p'=>4, 'q'=>4, 'r'=>4, 's'=>4, 't'=>4,
                'O'=>4, 'P'=>4, 'Q'=>4, 'R'=>4, 'S'=>4, 'T'=>4,
		'u'=>5, 'v'=>5, 'w'=>5, 'x'=>5, 'y'=>5, 'z'=>5,
                'U'=>5, 'V'=>5, 'W'=>5, 'X'=>5, 'Y'=>5, 'Z'=>5,
		'а'=>6, 'б'=>6, 'в'=>6, 'г'=>6, 'д'=>6, 'е'=>6, 'ж'=>6, 'з'=>6,
                'А'=>6, 'Б'=>6, 'В'=>6, 'Г'=>6, 'Д'=>6, 'Е'=>6, 'Ж'=>6, 'З'=>6,
		'и'=>7, 'й'=>7, 'к'=>7, 'л'=>7, 'м'=>7, 'н'=>7, 'о'=>7,
                'И'=>7, 'Й'=>7, 'К'=>7, 'Л'=>7, 'М'=>7, 'Н'=>7, 'О'=>7,
		'п'=>8, 'р'=>8, 'с'=>8, 'т'=>8, 'у'=>8,
                'П'=>8, 'Р'=>8, 'С'=>8, 'Т'=>8, 'У'=>8,
		'ф'=>9, 'х'=>9, 'ц'=>9, 'ч'=>9, 'ш'=>9, 'щ'=>9, 'ъ'=>9, 'ь'=>9, 'ю'=>9, 'я'=>9,
		'Ф'=>9, 'Х'=>9, 'Ц'=>9, 'Ч'=>9, 'Ш'=>9, 'Щ'=>9, 'Ъ'=>9, 'Ь'=>9, 'Ю'=>9, 'Я'=>9);
    $aLabel[LANG_BG]['strUpper'] = 'АБВГДЕЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЬЮЯЭЫ';
    $aLabel[LANG_BG]['strLower'] = 'абвгдежзийклмнопрстуфхцчшщъьюяэы';
    $aLabel[LANG_BG]['aCities'] = array(1=>'София', 2=>'Пловдив', 3=>'Варна', 4=>'Бургас', 14=>'Стара Загора');
    $aLabel[LANG_BG]['aCitiesAll'] = array(1=>'София', 2=>'Пловдив', 3=>'Варна', 4=>'Бургас', 5=>'Несебър',
                                           6=>'Созопол', 7=>'Албена', 8=>'Слънчев бряг', 9=>'Балчик', 10=>'Златни пясъци',
                                           11=>'Приморско', 12=>'Китен', 13=>'Царево', 14=>'Стара Загора', 15=>'Поморие',
                                           16=>'Кранево', 17=>'Каварна', 18=>'Бяла', 19=>'Обзор', 20=>'Лозенец', 21=>'Черноморец',
                                           22=>'Св. Св. Константин и Елена', 23=>'Брестовица', 24=>'Старозагорски бани', 25=>'Шабла', 26=>'Св. Влас');
    $aLabel[LANG_BG]['strThisWeek'] = 'през цялата седмица';
    $aLabel[LANG_BG]['strAnyTime'] = 'По всяко време';
    $aLabel[LANG_BG]['strWhichCity'] = 'в град';
    $aLabel[LANG_BG]['strWhichSection'] = 'По категории';
//=========================================================
    $aLabel[LANG_BG]['strRSS'] = 'Feeds (RSS 2.0)';
    $aLabel[LANG_BG]['strAdvertisement'] = 'Реклама';
    $aLabel[LANG_BG]['strAccent'] = 'Акцент';
    $aLabel[LANG_BG]['strPremieres'] = 'Премиери';
    $aLabel[LANG_BG]['strPromoLists'] = 'Избрано';
    $aLabel[LANG_BG]['strPromoNews'] = 'Новини';
    $aLabel[LANG_BG]['strPromoPublications'] = 'Интервюта';
	$aLabel[LANG_BG]['strPicture'] = 'Снимка';
	$aLabel[LANG_BG]['strCity'] = 'Градска';
	$aLabel[LANG_BG]['strWeekendPlan'] = 'Уикенд план';
	$aLabel[LANG_BG]['strTodayNews'] = 'Днес';
	$aLabel[LANG_BG]['strTomorrowNews'] = 'Утре';
	
//=========================================================
// common validation labels for alertboxes
//=========================================================
    $aLabel[LANG_BG]['strEnter'] = 'Моля въведете ';
    $aLabel[LANG_BG]['strSelect'] = 'Моля изберете ';
//=========================================================
// common list labels
//=========================================================
    $aLabel[LANG_BG]['strNoRecords'] = 'Няма намерени данни.';
    $aLabel[LANG_BG]['strAll'] = 'Всички ';
    $aLabel[LANG_BG]['strMore'] = 'Прочети повече';
    $aLabel[LANG_BG]['strBack'] = 'Назад';
    $aLabel[LANG_BG]['strBackToList'] = 'Обратно в последния списък';
    $aLabel[LANG_BG]['strTop'] = 'Горе';
    $aLabel[LANG_BG]['strNext'] = 'Следваща';
    $aLabel[LANG_BG]['strPrev'] = 'Предишна';
    $aLabel[LANG_BG]['strRecords'] = 'показва %1 - %2 от %3 записа';
    $aLabel[LANG_BG]['strGoToPage'] = 'Страници: ';//отиди на страница
//=========================================================
// Page labels
//=========================================================
    $aLabel[LANG_BG]['strPageID'] = 'Страница ID';
    $aLabel[LANG_BG]['strParentPage'] = 'Намира се в страница';
    $aLabel[LANG_BG]['strPageName'] = 'Заглавие';
    $aLabel[LANG_BG]['strMetaDescription'] = 'Мета описание';
    $aLabel[LANG_BG]['strMetaKeywords'] = 'Мета ключови думи';
    $aLabel[LANG_BG]['strPageText'] = 'Съдържание';
    $aLabel[LANG_BG]['strNrViews'] = 'Брой посещения';
    $aLabel[LANG_BG]['strSortOrder'] = 'Последователност';
    $aLabel[LANG_BG]['strLastUpdate'] = 'Последна промяна';
    $aLabel[LANG_BG]['strTemplate'] = 'Темплейт';
    $aLabel[LANG_BG]['strCityFilter'] = 'Показвай филтър';
    $aLabel[LANG_BG]['strReqUserStatus'] = 'Само за регистрирани';
    $aLabel[LANG_BG]['strHide'] = 'Скрито на уебсайта';
    $aLabel[LANG_BG]['aYesNo'] = array(0=>'Не', 1=>'Да');
    $aLabel[LANG_BG]['strPages'] = 'Страници';
    $aLabel[LANG_BG]['strRubriques'] = 'Рубрики';
//=========================================================
// Job labels
//=========================================================
    $aLabel[LANG_BG]['strJobID'] = 'Обява ID';
    $aLabel[LANG_BG]['strJobTitle'] = 'Име на обявата';
    $aLabel[LANG_BG]['strDescription'] = 'Описание';
    $aLabel[LANG_BG]['strCompany'] = 'Фирма';
    $aLabel[LANG_BG]['strStartDate'] = 'Начална дата';
    $aLabel[LANG_BG]['strEndDate'] = 'Крайна дата';
    $aLabel[LANG_BG]['strJobs'] = 'Обяви';
//=========================================================
// News labels
//=========================================================
    $aLabel[LANG_BG]['strNewsID'] = 'Новина ID';
    $aLabel[LANG_BG]['strNewsName'] = 'Заглавие';
    $aLabel[LANG_BG]['strNewsDate'] = 'Дата на новината';
    $aLabel[LANG_BG]['strNewsLead'] = 'Интро текст';
    $aLabel[LANG_BG]['strNewsText'] = 'Съдържание';
    $aLabel[LANG_BG]['strNews'] = 'Новини';
//=========================================================
// Publication labels
//=========================================================
    $aLabel[LANG_BG]['strPublicationID'] = 'Публикация ID';
    $aLabel[LANG_BG]['strPublicationName'] = 'Име на публикацията';
    $aLabel[LANG_BG]['strPublicationDate'] = 'Дата на публикуване';
    $aLabel[LANG_BG]['strPublicationSubtitle'] = 'Подзаглавие';
    $aLabel[LANG_BG]['strPublicationLead'] = 'Увод';
    $aLabel[LANG_BG]['strPublicationText'] = 'Съдържание';
    $aLabel[LANG_BG]['strSource'] = 'Източник';
    $aLabel[LANG_BG]['strSourceUrl'] = 'Уебсайт на източника';
    $aLabel[LANG_BG]['strAuthor'] = 'Автор';
    $aLabel[LANG_BG]['strPublications'] = 'Публикации';
    $aLabel[LANG_BG]['strInterviews'] = 'Интервюта';
    $aLabel[LANG_BG]['strComments'] = 'Коментари';
    $aLabel[LANG_BG]['strCommentID'] = 'Коментар ID';
    $aLabel[LANG_BG]['strCommentTitle'] = 'Заглавие';
    $aLabel[LANG_BG]['strCommentText'] = 'Коментар';
    $aLabel[LANG_BG]['strPostComment'] = 'Добави коментар';
    $aLabel[LANG_BG]['strPermalink'] = 'Връзка към този коментар';
    $aLabel[LANG_BG]['strCommentIntro'] = 'Ако искате да добавяте коментари, моля <a href="'.setPage(USERREG_PAGE).'">регистрирайте се</a> и влезте с вашите потребителско име и парола.';
//=========================================================
// Festival labels
//=========================================================
    $aLabel[LANG_BG]['strFestivalID'] = 'Фестивал ID';
    $aLabel[LANG_BG]['strFestivalName'] = 'Име на фестивала';
    $aLabel[LANG_BG]['strStartDate'] = 'Начална дата';
    $aLabel[LANG_BG]['strRemainderDate'] = 'Дата';
    $aLabel[LANG_BG]['strEndDate'] = 'Крайна дата';
    $aLabel[LANG_BG]['strFestivalLead'] = 'Увод';
    $aLabel[LANG_BG]['strFestivalText'] = 'Съдържание';
    $aLabel[LANG_BG]['strUrl'] = 'Уебсайт';
    $aLabel[LANG_BG]['strFestivals'] = 'Фестивали';
//=========================================================
// Place labels
//=========================================================
    $aLabel[LANG_BG]['strPlaceID'] = 'Място ID';
    $aLabel[LANG_BG]['strPlaceName'] = 'Име';
    $aLabel[LANG_BG]['strShortTitle'] = 'Кратко име';
    $aLabel[LANG_BG]['strDescription'] = 'Описание';
    $aLabel[LANG_BG]['strAddress'] = 'Адрес';
    $aLabel[LANG_BG]['strWorkingTime'] = 'Работно време';
    $aLabel[LANG_BG]['strStartTime'] = 'Час на започване';
    $aLabel[LANG_BG]['strPlaceType'] = 'Вид място';
    $aLabel[LANG_BG]['strPlaceSubtype'] = 'Подвид място';
    $aLabel[LANG_BG]['strPlaces'] = 'Места';
    $aLabel[LANG_BG]['aPlaceTypes'] = array(1=>'Кина',
                                            2=>'Театри',
                                            3=>'Галерии, музеи, зали',
                                            5=>'Ресторанти',
                                            6=>'Клубове и барове',
                                            7=>'Други места',
                                            28=>'В движение');
                                            /*4=>'Зали',
                                            29=>'Интернет клубове',
                                            32=>'Хотели');*/
    $aLabel[LANG_BG]['strCuisine'] = 'Кухня';
    $aLabel[LANG_BG]['strAtmosphere'] = 'Обстановка';
    $aLabel[LANG_BG]['strPriceCategory'] = 'Ценова категория';
    $aLabel[LANG_BG]['strMusicStyle'] = 'Музика';
//=========================================================
// Place Guide labels
//=========================================================
    $aLabel[LANG_BG]['strPlaceGuideID'] = 'Place guide ID';
    $aLabel[LANG_BG]['strCategory'] = 'Категория';
    $aLabel[LANG_BG]['strEntranceFee'] = 'Входна такса';
    $aLabel[LANG_BG]['strNrSeats'] = 'Брой места';
    $aLabel[LANG_BG]['strMusicStyle'] = 'Тип музика';
    $aLabel[LANG_BG]['strDJ'] = 'DJ';
    $aLabel[LANG_BG]['strLiveMusic'] = 'Музика на живо';
    $aLabel[LANG_BG]['strKaraoke'] = 'Караоке';
    $aLabel[LANG_BG]['strBgndMusic'] = 'Фонова музика';
    $aLabel[LANG_BG]['strDelivery'] = 'С доставка';
    $aLabel[LANG_BG]['strFaceControl'] = 'Face control';
    $aLabel[LANG_BG]['strCuisine'] = 'Кухня';
    $aLabel[LANG_BG]['strTerrace'] = 'Тераса / Градина';
    $aLabel[LANG_BG]['strSmokingArea'] = 'Място за непушачи';
    $aLabel[LANG_BG]['strClima'] = 'Климатик';
    $aLabel[LANG_BG]['strParking'] = 'Паркинг';
    $aLabel[LANG_BG]['strWardrobe'] = 'Гардероб';
    $aLabel[LANG_BG]['strCardPayment'] = 'Плащане с карти';
    $aLabel[LANG_BG]['strEntertainment'] = 'Забавления';
    $aLabel[LANG_BG]['strWifi'] = 'Безжичен интернет';
    $aLabel[LANG_BG]['strNew'] = 'Ново';
    $aLabel[LANG_BG]['strVacation'] = 'Ваканция / Ремонт';
    $aLabel[LANG_BG]['strVacationStartDate'] = 'Ваканция - начална дата';
    $aLabel[LANG_BG]['strVacationEndDate'] = 'Ваканция - крайна дата';
//=========================================================
// Place related labels
//=========================================================
    $aLabel[LANG_BG]['strLegend'] = 'Легенда';
    $aLabel[LANG_BG]['strMap'] = 'Карта';
    $aLabel[LANG_BG]['strMapEditNote'] = 'Инструкции за поправяне на карта:<br />
    1. Скрий картата от линка под нея (ако не е скрита).<br />
    2. Отиди в <a href="http://www.emaps.bg" target="_blank">сайта на eMaps</a> и намери адреса, който търсиш, през формичката за търсене в ляво.<br />
    3. Цъкни на бутона "маркирай" отгоре на картата, и посочи вярната точка върху картата. Трябва да се отбележи с червен маркер.<br />
    4. Цъкни на бутона "изпрати линк" отгоре на картата, и вземи/копирай от прозорчето генерирания линк.<br />
    5. Върси се в сайта на Програмата и редактирай линка за карта (в списъка с линкове).<br />
    6. При следващия ъпдейт към eMaps ще има карта с новия адрес.<br />';
    $aLabel[LANG_BG]['strMapAddNote'] = 'Инструкции за слагане на нова карта:<br />
    1. Отиди в <a href="http://www.emaps.bg" target="_blank">сайта на eMaps</a> и намери адреса, който търсиш, през формичката за търсене в ляво.<br />
    2. Цъкни на бутона "маркирай" отгоре на картата, и посочи вярната точка върху картата. Трябва да се отбележи с червен маркер.<br />
    3. Цъкни на бутона "изпрати линк" отгоре на картата, и вземи/копирай от прозорчето генерирания линк.<br />
    4. Върси се в сайта на Програмата и редактирай линка за карта (в списъка с линкове).<br />
    5. При следващия ъпдейт към eMaps ще има карта.<br />';
    $aLabel[LANG_BG]['strCalendar'] = 'Календар';
    $aLabel[LANG_BG]['strNote'] = 'Забележка';
    $aLabel[LANG_BG]['strCalendarIntro'] = 'Моля въведете предпочитаните дати и забележка при добавяне в календара.';
    $aLabel[LANG_BG]['strAddToCalendar'] = 'Добави в календара';
    $aLabel[LANG_BG]['strUpdateCalendar'] = 'Промени в календара';
    $aLabel[LANG_BG]['strDeleteCalendar'] = 'Изтрий от календара';
    $aLabel[LANG_BG]['strAddRemainderToCalendar'] = 'Добави напомняне';
    $aLabel[LANG_BG]['strDoDel'] = 'Изтрий';
    $aLabel[LANG_BG]['strDoAdd'] = 'Добави';
    $aLabel[LANG_BG]['strWeather'] = 'Времето';
    $aLabel[LANG_BG]['strProgram'] = 'програма';
    $aLabel[LANG_BG]['strPrevMonth'] = 'Програма за изминалия месец';
    $aLabel[LANG_BG]['strPrevWeek'] = 'Програма за изминалата седмица';
    $aLabel[LANG_BG]['strToday'] = 'Програмата днес';
    $aLabel[LANG_BG]['strThisWeek'] = 'Програма за седмицата';
    $aLabel[LANG_BG]['strNextWeek'] = 'Програма за следваща седмица';
    $aLabel[LANG_BG]['strNextMonth'] = 'Програма за месеца';
    $aLabel[LANG_BG]['strIndexDetails'] = 'Индекс / Пълен списък';
    $aLabel[LANG_BG]['strComments'] = 'Коментари';
    $aLabel[LANG_BG]['strAddComment'] = 'Добави коментар';
    $aLabel[LANG_BG]['strVote'] = 'Гласувай';
    $aLabel[LANG_BG]['strRating'] = 'Рейтинг';
    $aLabel[LANG_BG]['aRating'] = array(1=>'1',
                                        2=>'2',
                                        3=>'3',
                                        4=>'4',
                                        5=>'5',
                                        6=>'6',
                                        7=>'7',
                                        8=>'8',
                                        9=>'9',
                                        10=>'10');
    $aLabel[LANG_BG]['strTellFriend'] = 'Изпрати на приятел';
    $aLabel[LANG_BG]['strPrice'] = 'Билети';//'Цена'
    $aLabel[LANG_BG]['strLv'] = 'лв';// лв.
    $aLabel[LANG_BG]['strCalendarAdd'] = 'Добави в календара';
    $aLabel[LANG_BG]['strTranslation'] = 'Превод';
    $aLabel[LANG_BG]['strOrigLanguage'] = 'Език';
    $aLabel[LANG_BG]['strGenre'] = 'Жанр';
    $aLabel[LANG_BG]['strType'] = 'Вид';
    $aLabel[LANG_BG]['strExhibitionGenre'] = 'Техника';
//=========================================================
// Place Hall labels
//=========================================================
    $aLabel[LANG_BG]['strPlaceHallID'] = 'Зала ID';
    $aLabel[LANG_BG]['strHallTitle'] = 'Име на залата';
    $aLabel[LANG_BG]['strHall'] = 'Зала';
//=========================================================
// Event labels
//=========================================================
    $aLabel[LANG_BG]['strEventID'] = 'Събитие ID';
    $aLabel[LANG_BG]['strEventName'] = 'Заглавие';
    $aLabel[LANG_BG]['strOriginalTitle'] = 'Оригинално заглавие';
    $aLabel[LANG_BG]['strEventLead'] = 'Интро текст';
    $aLabel[LANG_BG]['strDescription'] = 'Описание';
    $aLabel[LANG_BG]['strFeatures'] = 'Година/минути/държава';
    $aLabel[LANG_BG]['strComment'] = 'Състав';
    $aLabel[LANG_BG]['strEventType'] = 'Вид събитие';
    $aLabel[LANG_BG]['strEventSubtype'] = 'Подвид събитие';
    $aLabel[LANG_BG]['strEvents'] = 'Събития';
    $aLabel[LANG_BG]['aEventTypes'] = array(10=>'Филми',
                                            11=>'Постановки',
                                            12=>'Изложби',
                                            13=>'Класическа музика',
                                            14=>'Групи и изпълнители',
                                            21=>'Слово и други', //Други събития
                                            24=>'Партита',
                                            27=>'Концерти');//,30=>'CD Ревюта'
    $aLabel[LANG_BG]['aAllowedEventSubtypes'] = array(
                    10=>array(1, 2, 3, 4, 5, 6, 7, 8, 9, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 42, 43, 44),
                    11=>array(11, 12, 13, 14, 15, 16, 17, 18, 45),
                    14=>array(21, 22, 23, 24, 41, 46));
//=========================================================
// Mixer labels
//=========================================================
    $aLabel[LANG_BG]['strMixerID'] = 'Статия ID';
    $aLabel[LANG_BG]['strParentMixer'] = 'Група';
    $aLabel[LANG_BG]['strMixerName'] = 'Заглавие';
    $aLabel[LANG_BG]['strMixer'] = 'Статии';
	$aLabel[LANG_BG]['strCommonData'] = 'Общи данни';
    $aLabel[LANG_BG]['strMixerDate'] = 'Дата на публикуване';
    $aLabel[LANG_BG]['strMixerSubtitle'] = 'Подзаглавие';
    $aLabel[LANG_BG]['strMixerLead'] = 'Увод';
    $aLabel[LANG_BG]['strMixerText'] = 'Текст';
    $aLabel[LANG_BG]['strMixers'] = 'Публикации';
    $aLabel[LANG_BG]['strMixerImage'] = 'Снимка';
    $aLabel[LANG_BG]['strMixerImageFull'] = 'Снимка';
   $aLabel[LANG_BG]['aMixerTypes'] = array(1=>'Снимки',
                                            2=>'Градски',
                                            3=>'Разходка',
                                            4=>'Уикенд план',
                                            5=>'Събитие');

//=========================================================
// Urban labels
//=========================================================
    $aLabel[LANG_BG]['strUrbanID'] = 'Статия ID';
    $aLabel[LANG_BG]['strUrbanName'] = 'Заглавие';
    $aLabel[LANG_BG]['strMainUrbanName'] = 'Общо заглавие';
    $aLabel[LANG_BG]['strUrban'] = 'Статии';
    $aLabel[LANG_BG]['strUrbanDate'] = 'Дата на публикуване';
    $aLabel[LANG_BG]['strUrbanText'] = 'Текст';

	$aLabel[LANG_BG]['strPart1'] = 'Първа част';
    $aLabel[LANG_BG]['strPart2'] = 'Втора част';
    $aLabel[LANG_BG]['strPart3'] = 'Трета част';
    $aLabel[LANG_BG]['strPart'] = 'Част от статията';

	$aLabel[LANG_BG]['strUrbans'] = 'Публикации';
    $aLabel[LANG_BG]['strUrbanImage'] = 'Снимки';
    $aLabel[LANG_BG]['strUrbanImageFull'] = 'Снимка';
    $aLabel[LANG_BG]['strUrbanFooter'] = 'Ако нещо ви хване окото на улицата и искате да го споделите с нас, ще се радваме да ни го изпратите на';
//=========================================================
// Multy labels
//=========================================================
    $aLabel[LANG_BG]['strMultyID'] = 'Статия ID';
    $aLabel[LANG_BG]['strMultyName'] = 'Заглавие';
    $aLabel[LANG_BG]['strMulty'] = 'Статии';
    $aLabel[LANG_BG]['strMultyDate'] = 'Дата на публикуване';
    $aLabel[LANG_BG]['strMultyText'] = 'Текст';

	$aLabel[LANG_BG]['strCurrPart'] = 'Част';
    $aLabel[LANG_BG]['strPart'] = 'Част от статията';

	$aLabel[LANG_BG]['strMulties'] = 'Публикации';
    $aLabel[LANG_BG]['strMultyImage'] = 'Снимка';
    $aLabel[LANG_BG]['strMultyImageFull'] = 'Снимка';
//=========================================================
// Link labels
//=========================================================
    $aLabel[LANG_BG]['strLinkID'] = 'Линк ID';
    $aLabel[LANG_BG]['strLinkTitle'] = 'Име';
    $aLabel[LANG_BG]['strLinkType'] = 'Вид връзка';
    $aLabel[LANG_BG]['aLinkTypes'] = array(1=>'Уебсайт',
                                           2=>'Поръчай билет от Eventim',
                                           3=>'Виж на картата от Emaps',
										   4=>'Още по темата',
                                           0=>'Друг линк');
    $aLabel[LANG_BG]['strUrl'] = 'Уебсайт';
    $aLabel[LANG_BG]['strMoreInfo'] = 'Още по темата';
    $aLabel[LANG_BG]['strLinks'] = 'Връзки';
//=========================================================
// E-mail labels
//=========================================================
    $aLabel[LANG_BG]['strEmailID'] = 'E-mail ID';
    $aLabel[LANG_BG]['strEmailType'] = 'Вид E-mail';
    $aLabel[LANG_BG]['aEmailTypes'] = array(1=>'E-mail',
                                            2=>'Личен e-mail',
                                            0=>'Друг e-mail');
    $aLabel[LANG_BG]['strEmail'] = 'E-mail';
    $aLabel[LANG_BG]['strEmails'] = 'E-mail-и';
//=========================================================
// Attachment labels
//=========================================================
    $aLabel[LANG_BG]['strAttachmentID'] = 'Файл ID';
    $aLabel[LANG_BG]['strAttachmentTitle'] = 'Име';
    $aLabel[LANG_BG]['strAttachment'] = 'Файл';
    $aLabel[LANG_BG]['strAttachmentFull'] = 'Нов файл';
    $aLabel[LANG_BG]['strAttachmentType'] = 'Вид файл';
    $aLabel[LANG_BG]['aAttachmentTypes'] = array(1=>'Старо лого/заглавна ('.W_IMG_SMALL.'/'.H_IMG_SMALL.'px)',
                                                 2=>'Стара илюстрация/малка картинка ('.W_IMG_SMALL.'/'.H_IMG_SMALL.'px)',
                                                 //3=>'Нова заглавна',
                                                 4=>'Галерия ('.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px)',
                                                 5=>'Панорама *.mov ('.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px)',
                                                 6=>'Приложен файл',
                                                 7=>'Трейлър *.flv');
    $aLabel[LANG_BG]['strAttachment'] = 'Файл';
    $aLabel[LANG_BG]['strAttachments'] = 'Файлове';
//=========================================================
// Address labels
//=========================================================
    $aLabel[LANG_BG]['strAddressID'] = 'Адрес ID';
    $aLabel[LANG_BG]['strAddressType'] = 'Вид адрес';
    $aLabel[LANG_BG]['aAddressTypes'] = array(1=>'Адрес',
                                              2=>'Адрес за кореспонденция',
                                              3=>'Личен адрес');
    $aLabel[LANG_BG]['strStreet'] = 'Улица';
    $aLabel[LANG_BG]['strCity'] = 'Град';
    $aLabel[LANG_BG]['strZip'] = 'Пощ. код';
    $aLabel[LANG_BG]['strAddresses'] = 'Адреси';
//=========================================================
// Phone labels
//=========================================================
    $aLabel[LANG_BG]['strPhoneID'] = 'Телефон ID';
    $aLabel[LANG_BG]['strPhoneType'] = 'Вид телефон';
    $aLabel[LANG_BG]['aPhoneTypes'] = array(1=>'Телефон',
                                            2=>'Факс',
                                            3=>'Мобилен',
                                            4=>'Личен телефон',
                                            5=>'Е-факс',
                                            6=>'За доставки');
    $aLabel[LANG_BG]['strArea'] = 'Тел. код';
    $aLabel[LANG_BG]['strPhone'] = 'Телефон';
    $aLabel[LANG_BG]['strExt'] = 'Вътр.';
    $aLabel[LANG_BG]['strPhones'] = 'Телефони';
//=========================================================
// Promotion labels
//=========================================================
    $aLabel[LANG_BG]['strPromotionID'] = 'Промоция ID';
    $aLabel[LANG_BG]['strPromotionTitle'] = 'Име на промоцията';
    $aLabel[LANG_BG]['strPromotionType'] = 'Вид промоция';
    $aLabel[LANG_BG]['aPromotionTypes'] = array(1=>'Голям акцент',
                                                2=>'Малък акцент',
                                                3=>'Списък',
                                                4=>'Новини');
    $aLabel[LANG_BG]['aPromotionTypesFull'] = array(
        DEF_PAGE => array(PRM_ACCENT=>'Акценти', PRM_PREMIERE=>'премиери', PRM_INTERVIEW=>'интервю'), //, PRM_NEWS=>'Новини'
        21 => array(PRM_ACCENT=>'Акцент', PRM_PREMIERE=>'премиери', PRM_LEFTLIST=>'нови филми', PRM_RIGHTLIST=>'за децата', PRM_INTERVIEW=>'интервюта'), // cinema //, PRM_NEWS=>'Новини'
        22 => array(PRM_ACCENT=>'Акцент', PRM_PREMIERE=>'избрано', PRM_LEFTLIST=>'нови постановки', PRM_RIGHTLIST=>'за децата', PRM_INTERVIEW=>'интервюта'), // performance //, PRM_NEWS=>'Новини'
        24 => array(PRM_ACCENT=>'Акцент', PRM_PREMIERE=>'избрано', PRM_LEFTLIST=>'парти', PRM_RIGHTLIST=>'класика', PRM_INTERVIEW=>'интервюта'), // music //, PRM_NEWS=>'Новини'
        25 => array(PRM_ACCENT=>'Акцент', PRM_PREMIERE=>'нови изложби'), // exhibition //, PRM_NEWS=>'Новини'
        26 => array(PRM_ACCENT=>'Акцент', PRM_PREMIERE=>'избрани', PRM_LEFTLIST=>'нови'), // clubs & restaurants //, PRM_NEWS=>'Новини', PRM_RIGHTLIST=>'С доставка'
//        27 => array(PRM_ACCENT=>'Акцент', PRM_PREMIERE=>'Избрано'), // outdoors //PRM_NEWS=>'Новини'
        28 => array(PRM_ACCENT=>'Акцент', PRM_LEFTLIST=>'избрано', PRM_INTERVIEW=>'интервюта'), // logos //, PRM_NEWS=>'Новини'
//        135 => array(PRM_ACCENT=>'Акцент', PRM_PREMIERE=>'Градски образи / х3', PRM_LEFTLIST=>'Уикенд план', PRM_RIGHTLIST=>'Дългата разходка', PRM_EXTRA=>'Екстра'),
	        167 => array(PRM_ACCENT=>'Акцент', PRM_PREMIERE=>'ден по ден', PRM_LEFTLIST=>'фото', PRM_RIGHTLIST=>'трафик', PRM_EXTRA=>'лица')
); // mixer //, PRM_NEWS=>'Новини'
    $aLabel[LANG_BG]['aPromoEntityTypes'] = array(  $aEntityTypes[ENT_NEWS]=>$aTemplate[LANG_BG][ENT_NEWS],
                                                    $aEntityTypes[ENT_PUBLICATION]=>$aTemplate[LANG_BG][ENT_PUBLICATION],
                                                    $aEntityTypes[ENT_FESTIVAL]=>$aTemplate[LANG_BG][ENT_FESTIVAL],
                                                    $aEntityTypes[ENT_PLACE]=>$aTemplate[LANG_BG][ENT_PLACE],
                                                    $aEntityTypes[ENT_EVENT]=>$aTemplate[LANG_BG][ENT_EVENT],
													$aEntityTypes[ENT_URBAN]=>$aTemplate[LANG_BG][ENT_URBAN],
													$aEntityTypes[ENT_MULTY]=>$aTemplate[LANG_BG][ENT_MULTY],
													$aEntityTypes[ENT_EXTRA]=>$aTemplate[LANG_BG][ENT_EXTRA]);
    $aLabel[LANG_BG]['strEntityType'] = 'Вид данни';
    $aLabel[LANG_BG]['strEntity'] = 'ID от избрания вид';
//=========================================================
// Program labels
//=========================================================
    $aLabel[LANG_BG]['strProgramID'] = 'Програмация ID';
    $aLabel[LANG_BG]['strProgramType'] = 'Вид програмация';
    $aLabel[LANG_BG]['aProgramTypes'] = array(16=>'Филмова прожекция',
                                              15=>'Представление - театър, опера, балет',
                                              18=>'Музейни експозиции и изложби',
                                              19=>'Класическа музика',
                                              26=>'Концерти',
                                              20=>'Групи на живо',
                                              25=>'Клубна музика и партита',
                                              23=>'Слово (Книги, лекции, дебати)');
                                              /*17=>'Видео &amp; DVD',
                                              31=>'CD Премиера',
                                              23=>'Още (Книги, лекции, дебати)');*/
    $aLabel[LANG_BG]['strFestival'] = 'Фестивал';
    $aLabel[LANG_BG]['strMainPlace'] = 'Място';
    $aLabel[LANG_BG]['strPlaceHall'] = 'Зала';
    $aLabel[LANG_BG]['strSecondaryPlaces'] = 'Още места (второстепенни)';
    $aLabel[LANG_BG]['strGuest'] = 'Гостува'; //'Гостуващ театър'
    $aLabel[LANG_BG]['strParticipant'] = 'Изпълнители';
    $aLabel[LANG_BG]['strAllParticipants'] = 'Всички изпълнители';
    $aLabel[LANG_BG]['strSelectedParticipants'] = 'Избрани изпълнители';
    $aLabel[LANG_BG]['strAdd'] = 'Добави >';
    $aLabel[LANG_BG]['strRemove'] = '< Изтрий';
    $aLabel[LANG_BG]['strMainEvent'] = 'Събитие (водещ изпълнител)';
    $aLabel[LANG_BG]['strSecondaryEvents'] = 'Още събития (второстепенни изпълнители)';
    $aLabel[LANG_BG]['strPremieres'] = 'Премиери';
    $aLabel[LANG_BG]['strPremiereType'] = 'Вид премиера';
    $aLabel[LANG_BG]['aPremiereTypes'] = array(1=>'Предпремиера',
                                              2=>'Премиера',
                                              3=>'Ексклузивно',
                                              4=>'Официална премиера',
                                              5=>'Специална прожекция');
    $aLabel[LANG_BG]['strProgramDatePeriodID'] = 'Период ID';
    $aLabel[LANG_BG]['strProgramDateTimeID'] = 'Дата/час ID';
    $aLabel[LANG_BG]['strProgramDate'] = 'Дата';
    $aLabel[LANG_BG]['strProgramTime'] = 'Час';
    $aLabel[LANG_BG]['strNrDates'] = 'Брой дати';
    $aLabel[LANG_BG]['strNrTimes'] = 'Брой часове';
    $aLabel[LANG_BG]['strGenerateGrid'] = 'Генерирай';
    $aLabel[LANG_BG]['strSelectPlace'] = 'Избери място и зала (къде се случва) от списък';
    $aLabel[LANG_BG]['strSelectEvent'] = 'Избери събитие (какво се случва) от списък';
    $aLabel[LANG_BG]['strSelected'] = 'Избрано';
    $aLabel[LANG_BG]['strSelectAsPrimary'] = 'Избери като водещо';
    $aLabel[LANG_BG]['strSelectAsSecondary'] = 'Избери като второстепенно';
    $aLabel[LANG_BG]['strReportType'] = 'Вид report';
    $aLabel[LANG_BG]['aReportTypes'] = array(1=>'Седмичен по място',
                                             2=>'Седмичен по събитие',
                                             3=>'Дневен по място',
                                             4=>'Дневен по събитие');
    $aLabel[LANG_BG]['strProgramNote'] = 'Забележка към програмацията (напр. вход свободен / групови цени за всички дати)';
    $aLabel[LANG_BG]['strNote'] = 'Забележка';
//=========================================================
// Label labels
//=========================================================
    $aLabel[LANG_BG]['strLabelID'] = 'Категория ID';
    $aLabel[LANG_BG]['strLabelName'] = 'Име';
    $aLabel[LANG_BG]['strParentLabel'] = 'Група';
//=========================================================
// User labels
//=========================================================
    $aLabel[LANG_BG]['strUserID'] = 'Потребител ID';
    $aLabel[LANG_BG]['strNrLogins'] = 'Брой влизания';
    $aLabel[LANG_BG]['strLastLogin'] = 'Последно влизане';
    $aLabel[LANG_BG]['strUserStatus'] = 'Статус на потребителя';
//=========================================================
// Image labels
//=========================================================
    $aLabel[LANG_BG]['strPanorama'] = 'Панорама';
    $aLabel[LANG_BG]['strGallery'] = 'Фото галерия';
    $aLabel[LANG_BG]['strQuicktimePlugin'] = 'За да разгледате интерактивната панорама, трябва да имате инсталиран <a href="http://www.apple.com/quicktime/download/" target="_blank">Quicktime Player</a>.';
    $aLabel[LANG_BG]['strPromotionImage'] = 'Картинка';
    $aLabel[LANG_BG]['strPromotionImageFull'] = 'Нова картинка<br/>(jpg - '.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px)';
    $aLabel[LANG_BG]['strNewsImage'] = 'Картинка';
    $aLabel[LANG_BG]['strNewsImageFull'] = 'Нова картинка<br/>(jpg - '.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px или поне '.W_IMG_MIDDLE.'/'.H_IMG_MIDDLE.'px)';
    $aLabel[LANG_BG]['strPlaceImage'] = 'Картинка';
    $aLabel[LANG_BG]['strPlaceImageFull'] = 'Нова картинка<br/>(jpg - '.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px или поне '.W_IMG_MIDDLE.'/'.H_IMG_MIDDLE.'px)';
    $aLabel[LANG_BG]['strEventImage'] = 'Картинка';
    $aLabel[LANG_BG]['strEventImageFull'] = 'Нова картинка<br/>(jpg - '.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px или поне '.W_IMG_MIDDLE.'/'.H_IMG_MIDDLE.'px)';
    $aLabel[LANG_BG]['strPublicationImage'] = 'Картинка';
    $aLabel[LANG_BG]['strPublicationImageFull'] = 'Нова картинка<br/>(jpg - '.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px или поне '.W_IMG_MIDDLE.'/'.H_IMG_MIDDLE.'px)';
    $aLabel[LANG_BG]['strFestivalImage'] = 'Картинка';
    $aLabel[LANG_BG]['strFestivalImageFull'] = 'Нова картинка<br/>(jpg - '.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px или поне '.W_IMG_MIDDLE.'/'.H_IMG_MIDDLE.'px)';
    $aLabel[LANG_BG]['strGalleryImage'] = 'Галерия от снимки';
    $aLabel[LANG_BG]['strGalleryImageFull'] = 'Нова снимка в галерията<br/>(gif, jpg, png - '.W_IMG_GALLERY.'/'.H_IMG_GALLERY.'px)';
    $aLabel[LANG_BG]['strGalleryImageName'] = 'Заглавие на снимката';
    $aLabel[LANG_BG]['strGalleryImageAuthor'] = 'Автор на снимката';
//=========================================================
// File upload labels
//=========================================================
    $aLabel[LANG_BG]['strFile_'.ERR_NO_UPLOAD] = 'Не е качена нова снимка. ';
    $aLabel[LANG_BG]['strFile_'.ERR_NONE] = 'Снимката беше качена успешно. ';
    $aLabel[LANG_BG]['strFile_'.ERR_NO_FOLDER_ACCESS] = 'Не е позволено запазването на файлове в избраната папка на сървъра. ';
    $aLabel[LANG_BG]['strFile_'.ERR_NO_FILE] = 'Не е качена нова снимка. ';
    $aLabel[LANG_BG]['strFile_'.ERR_WRONG_FILETYPE] = 'Непозволен тип файл. ';
    $aLabel[LANG_BG]['strFile_'.ERR_FILE_ATTACK] = 'Непозволено качване на файл. ';
//=========================================================
// common action labels used in Admin Interface
//=========================================================
    $aLabel[LANG_BG]['new_window'] = ' (в нов прозорец)';
    $aLabel[LANG_BG]['add'] = ' - добави';
    $aLabel[LANG_BG]['addmore'] = ' - добави (много)';
    $aLabel[LANG_BG]['edit'] = 'промени';
    $aLabel[LANG_BG]['delete'] = 'изтрий';
    $aLabel[LANG_BG]['generate'] = 'смени парола';
    $aLabel[LANG_BG]['view'] = 'виж';
    $aLabel[LANG_BG]['activate'] = 'активирай';
    $aLabel[LANG_BG]['deactivate'] = 'деактивирай';
    $aLabel[LANG_BG]['show'] = 'покажи';
    $aLabel[LANG_BG]['hide'] = 'скрий';
    $aLabel[LANG_BG]['list'] = ' - списък';
    $aLabel[LANG_BG]['sitemap'] = ' - карта на сайта';
    $aLabel[LANG_BG]['report'] = ' - report';
    $aLabel[LANG_BG]['up'] = 'нагоре';
    $aLabel[LANG_BG]['down'] = 'надолу';
    $aLabel[LANG_BG]['close'] = 'Затвори';
    $aLabel[LANG_BG]['multiple'] = '<br /><span class="note">(задръж Ctrl, за да отмаркираш или избереш повече от едно)</span>';
//=========================================================
// common action labels used in Admin Interface
//=========================================================
    $aLabel[LANG_BG]['strCommonData'] = 'Общи данни';
    $aLabel[LANG_BG]['strSelectAll'] = 'Избери всички';
    $aLabel[LANG_BG]['strFillAll'] = 'Попълни всички';
    $aLabel[LANG_BG]['strDeleteSelected'] = 'Изтрий избраните';
    $aLabel[LANG_BG]['strByUser'] = ' ';//' от ';
    $aLabel[LANG_BG]['strNone'] = 'Няма';
    $aLabel[LANG_BG]['strAny'] = 'Всички';
    $aLabel[LANG_BG]['strKeyword'] = 'Ключова дума';
    $aLabel[LANG_BG]['strCategory'] = 'Категория';
    $aLabel[LANG_BG]['strPage'] = 'Страница';
    $aLabel[LANG_BG]['strWhere'] = 'Избери раздел';
    $aLabel[LANG_BG]['strGoSearch'] = 'Търси';
    $aLabel[LANG_BG]['strSearch'] = 'Търсене';
    $aLabel[LANG_BG]['strFilterCriteria'] = 'Търси по критерии';
    $aLabel[LANG_BG]['strFilterAlphabet'] = 'Виж в азбучника';
    $aLabel[LANG_BG]['strFilterOrder'] = 'Подреди';
    $aLabel[LANG_BG]['aFilterOrders'] = array(1=>'По клуб', 2=>'По парти');
    $aLabel[LANG_BG]['strSearchResults'] = 'Резултат от търсенето';
    $aLabel[LANG_BG]['strSearchRequired'] = 'Моля въведете критерии за търсене.';
    $aLabel[LANG_BG]['strDeleteQ'] = 'Сигурни ли сте, че искате да изтриете избраното?';
    $aLabel[LANG_BG]['strGenerateQ'] = 'Сигурни ли сте, че искате да генерирате нова парола за избрания потребител, и да я изпратите на e-mail адреса му/й?';
    $aLabel[LANG_BG]['strSwitch'] = 'Премини на: ';
    $aLabel[LANG_BG]['strSwitchQ'] = 'При преминаването на друг език последните промени няма да се запазят. Сигурни ли сте, че искате да продължите?';
    $aLabel[LANG_BG]['strDeleteOK'] = 'Избраните от вас данни бяха изтрити.';
    $aLabel[LANG_BG]['strGeneratePassOK'] = 'За избрания потребител беше генерирана нова парола, която му/й беше изпратена по e-mail.';
//=========================================================
// profile descriptions
//=========================================================
	$aLabel[LANG_BG]['strProfile'] = 'профил';
	$aLabel[LANG_BG]['strClassicProfile'] = 'Ако предпочитате театър пред техно и италианска кухня пред итало диско';
	$aLabel[LANG_BG]['strTrendyProfile'] = 'Ако предпочитате диско пред Джейн Остин и шотландско пред шопска';
	$aLabel[LANG_BG]['strFamilyProfile'] = 'Ако не обичате да оставяте някой от семейството сам вкъщи';
//=========================================================
// index label
//=========================================================
	$aLabel[LANG_BG]['strNewAddress'] = 'Имаме нов адрес: София 1463, ул. Доспат 43';
//=========================================================
// other labels
//=========================================================	
	$aLabel[LANG_BG]['strTV'] = 'ТВ';
	$aLabel[LANG_BG]['strWeatherToday'] = 'днес';
	$aLabel[LANG_BG]['strWeatherTomorrow'] = 'утре';
?>