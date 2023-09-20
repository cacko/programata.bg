<?php
if (!$bInSite) die();

require_once(dirname(__FILE__) .'/library/pear/PEAR/FirePHPCore/FirePHP.class.php');
$logger = FirePHP::getInstance(true);
//=========================================================
require_once('config.php');
//=========================================================
require_once('entity/session_mysql.php');
require_once('helper/session.php');
$oSession = new Session(SS_NAME, $aSessionVars);
//=========================================================
require_once('helper/tools.php');
require_once('helper/date.php');
require_once('helper/file.php');
require_once('helper/context.php');
//=========================================================
require_once('label/common.php');
foreach ($aLanguages as $key=>$val)
require_once('label/resource_'.$key.'.php');

$page = getRequestArg(ARG_PAGE, DEF_PAGE);

$item = getQueryArg(ARG_ID, 0);
if (!is_numeric($item)) $item = 0;

$cat = getQueryArg(ARG_CAT, 0);
if (!is_numeric($cat)) $cat = 0;

/*$city = getRequestArg(ARG_CITY, DEF_CITY);
 if (!is_numeric($city)) $city = DEF_CITY;

 $nDefLang = DEF_LANG;
 $lang = getQueryArg(ARG_LANG, $nDefLang);
 if (!is_numeric($lang)) $lang = $nDefLang;
 if (!in_array($lang, $aActiveLanguages)) $lang = $nDefLang;*/
//print_r($_COOKIE);

//--- city parsing
//dump($_SERVER);
$defined_cities = array();
foreach($aLabel[LANG_EN]['aCities'] as $key=>$def_city_name) {
	$defined_cities[$key] = str_replace(' ', '', strtolower($def_city_name));
}
$city = 0;
$url_parts = explode(".", $_SERVER['HTTP_HOST'], 3);
if(count($url_parts) == 3) {
	$url_city = strtolower($url_parts[0]);
	if(in_array($url_city, array_values($defined_cities))) {
		$city = array_search($url_city, $defined_cities);
	}
}
$nCookieCity = getCookieArg(CC_CITY, 0);
$request_city = (int) getRequestArg(ARG_CITY, $nCityToShow);
if(!$city) {
	$nCityToShow = DEF_CITY;
	if (!empty($nCookieCity)) {
		$nCityToShow = $nCookieCity;
	}
	$city = (!$request_city) ? $nCityToShow : $request_city;
}
else if ($request_city && $request_city != $city) {
	redirect(sprintf('http://%s.programata.bg%s', $defined_cities[$request_city],$_SERVER['REQUEST_URI']));
}
if (empty($nCookieCity) || $nCookieCity != $city){
	setcookie(CC_CITY, $city, time()+(3600*24*31), '/', 'programata.bg');
}
//-- end city parsing

$nLangToShow = DEF_LANG;
$nCookieLang = getCookieArg(CC_LANG, 0);
if (!empty($nCookieLang))
$nLangToShow = $nCookieLang;
$lang = getQueryArg(ARG_LANG, $nLangToShow);
if (!is_numeric($lang)) $lang = $nLangToShow;
if (!in_array($lang, $aActiveLanguages)) $lang = $nLangToShow;
if (empty($nCookieLang) || $nCookieLang != $lang)
setcookie(CC_LANG, $lang, time()+(3600*24*31), '/', 'programata.bg');

$action = getQueryArg(ARG_ACT, '');
if (empty($action)) $action = getPostedArg(ARG_ACT, '');

$relitem = getQueryArg(ARG_RELID, 0);
if (!is_numeric($relitem)) $relitem = 0;

$aContext = getContext();
//=========================================================
// search parameters
$dToday = date(DEFAULT_DATE_DB_FORMAT);
$dEndDate = increaseDate($dToday, THIS_WEEK_DAYS);
$dSelStartDate = getQueryArg('sel_date', '');
$nUserID = $oSession->GetValue(SS_USER_ID);
if (empty($nUserID))
{
	if (empty($dSelStartDate) || $dSelStartDate < $dToday || $dSelStartDate > $dEndDate)
	$dSelStartDate = '';
}
$dSelEndDate = $dSelStartDate;
// search by time - only with dropdown values
$dSelTime = getQueryArg('sel_time', '');
if (in_array($dSelTime, array_keys($aTimes)))
{
	$dSelStartTime = $aStartTimes[$dSelTime];
	$dSelEndTime = $aEndTimes[$dSelTime];
}
$sQuickSearchCriteria = '';
//=========================================================
$con = @mysql_connect(DB_HOST, DB_USER, DB_PASS);
if(!is_resource($con))
{
	include_once("_temp/server.html");
	die('<!-- Could not connect: ' . mysql_error().' -->');
}
mysql_query('SET NAMES '.DB_CONN_ENCODING, $con);
if(!@mysql_select_db(DB_NAME, $con))
{
	include_once("_temp/server.html");
	die ('<!-- Can\'t use '.DB_NAME.' : ' . mysql_error().' -->');
}
set_magic_quotes_runtime(0);

//=========================================================
/*
$con_vkushti = @mysql_connect(DB_HOST_VKUSHTI, DB_USER_VKUSHTI, DB_PASS_VKUSHTI, true);
if(!is_resource($con_vkushti))
{
	include_once("_temp/server.html");
	die('<!-- Could not connect: ' . mysql_error().' -->');
}
mysql_query('SET NAMES '.DB_CONN_ENCODING, $con_vkushti);
if(!@mysql_select_db(DB_NAME_VKUSHTI, $con_vkushti))
{
	include_once("_temp/server.html");
	die ('<!-- Can\'t use '.DB_NAME_VKUSHTI.' : ' . mysql_error().' -->');
}
*/
//=========================================================

require_once("entity/page.php");
//require_once("entity/job.php");
require_once("entity/news.php");
//require_once("entity/lookup.php");
require_once("entity/label.php");
require_once("entity/publication.php");
require_once("entity/comment.php");
require_once("entity/rate.php");
require_once("entity/festival.php");
require_once("entity/place.php");
require_once("entity/place_hall.php");
require_once("entity/place_guide.php");
require_once("entity/event.php");
require_once("entity/program.php");
require_once("entity/program_date_period.php");
require_once("entity/program_date_time.php");
require_once("entity/program_note.php");
require_once("entity/promotion.php");
require_once("entity/user.php");
require_once("entity/address.php");
require_once("entity/attachment.php");
require_once("entity/email.php");
require_once("entity/link.php");
require_once("entity/phone.php");
require_once("entity/map.php");
require_once("entity/mixer.php");
require_once("entity/urban.php");
require_once("entity/extra.php");
require_once("entity/multy.php");
//=========================================================
$oUser = new User($con, $lang);
$oPage = new Page($con, $lang);
//$oJob = new Job($con, $lang);
$oNews = new News($con, $lang);
//$oLookup = new Lookup($con, $lang);
$oLabel = new Label($con, $lang);
$oPublication = new Publication($con, $lang);
$oComment = new Comment($con, $lang);
$oRate = new Rate($con, $lang);
$oFestival = new Festival($con, $lang);
$oPlace = new Place($con, $lang);
$oPlaceHall = new PlaceHall($con, $lang);
$oPlaceGuide = new PlaceGuide($con, $lang);
$oEvent = new Event($con, $lang);
$oProgram = new Program($con, $lang);
$oProgramDatePeriod = new ProgramDatePeriod($con, $lang);
$oProgramDateTime = new ProgramDateTime($con, $lang);
$oProgramNote = new ProgramNote($con, $lang);
$oPromotion = new Promotion($con, $lang);
$oAddress = new Address($con, $lang);
$oAttachment = new Attachment($con, $lang);
$oEmail = new Email($con, $lang);
$oLink = new Link($con, $lang);
$oPhone = new Phone($con, $lang);
$oMap = new Map($con, $lang);
$oMixer = new Mixer($con, $lang);
$oUrban = new Urban($con, $lang);
$oExtra = new Extra($con, $lang);
$oMulty = new Multy($con, $lang);

//=========================================================
$mode = getRequestArg('mode');
if ($mode == 'ala')
{


	//


}

?>