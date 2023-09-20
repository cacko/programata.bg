<?php
ob_start();
// ini_set('error_reporting', E_ALL & ~E_NOTICE);
// ini_set('display_errors', 'On');
// ini_set('display_startup_errors', 'On');
// ini_set('display_errors', 'off');

$bInSite = true;

header('Content-Type: application/json; charset=utf-8');

include_once('../initialize.php');
include_once('../helper/user_action.php');

$pageList = $_REQUEST['page'];
$city = $_REQUEST['city'];
$item = $_REQUEST['id'];
$log_file = "out.log";

$callback = $_REQUEST['callback'];

switch($_REQUEST['request']) {

	case 'allCities': {
		echo getAllCities($callback);
		//file_put_contents($log_file, date('F j, Y, H:i:s').' > '.$_SERVER['QUERY_STRING'].'->'.getAllCities($callback)."\n", FILE_APPEND);
		break;
	}

	case 'mainCities': {
		echo getMainCities($callback);
		//file_put_contents($log_file, date('F j, Y, H:i:s').' > '.$_SERVER['QUERY_STRING'].'->'.getMainCities($callback)."\n", FILE_APPEND);
		break;
	}

	case 'menu': {
		echo getMainMenu($callback);
		//file_put_contents($log_file, date('F j, Y, H:i:s').' > '.$_SERVER['QUERY_STRING'].'->'.getMainMenu($callback)."\n", FILE_APPEND);
		break;
	}

	case 'profiles': {
		echo getProfiles($callback);
		//file_put_contents($log_file, date('F j, Y, H:i:s').' > '.$_SERVER['QUERY_STRING'].'->'.getProfiles($callback)."\n", FILE_APPEND);
		break;
	}

	case 'closestPlaces': {
		$result = createDataArr($_REQUEST['categories'], 'place', '');
		echoResult($result, $_REQUEST['callback']);
		break;
	}

	case 'recentEvents': {
		$result = createDataArr($_REQUEST['categories'], 'event', $city);
		usort($result['events'], create_function('$a, $b', 'return ($a["start"] == $b["start"]) ? 0 : (($a["start"] > $b["start"]) ? 1 : -1);'));
		echoResult($result, $_REQUEST['callback']);
		break;
	}

	case 'groupedRecentEvents': {
		$result = createDataArr($_REQUEST['categories'], 'event', $city);

		$grouped = array();
		if(!empty($result))
		{
			foreach($result['events'] as $res)	{
				if(!isset($res['place']['dates'])) $res['place']['dates'] = array();
				array_push($res['place']['dates'], $res['start']);
				sort($res['place']['dates']);
				if(!isset($grouped[$res['id']])){
					$grouped[$res['id']] = array();
					$grouped[$res['id']]['id'] = $res['id'];
					$grouped[$res['id']]['name'] = $res['name'];
					$grouped[$res['id']]['type'] = $res['type'];
					$grouped[$res['id']]['image'] = $res['image'];

					$grouped[$res['id']]['places'] = array();
				}
				if(!in_array($res['place'], $grouped[$res['id']]['places'])) {
					array_push($grouped[$res['id']]['places'], $res['place']);
					usort($grouped[$res['id']]['places'], create_function('$a, $b', 'return ($a["dates"][0] == $b["dates"][0]) ? 0 : (($a["dates"][0] > $b["dates"][0]) ? 1 : -1);'));
				}
			}
		}
		//dump($grouped);
		usort($grouped, create_function('$a, $b', 'return ($a["places"][0]["dates"][0] == $b["places"][0]["dates"][0]) ? 0 : (($a["places"][0]["dates"][0] > $b["places"][0]["dates"][0]) ? 1 : -1);'));
		$groupedRes['events'] = array();
		$groupedRes['events'] = array_values($grouped);

		echoResult($groupedRes, $_REQUEST['callback']);
		break;
			}

	case 'categoryList': {
		$pageID = $_REQUEST['page'];
		$nRootPage = $oPage->GetRootPageID($pageID);
		$rRootPage = $oPage->GetByID($nRootPage);\
		$result = createDataArr($pageID, '', $city);
		if(key($result) == 'events'){
	       $grouped = array();
			foreach($result['events'] as $res)	{
				if(!isset($grouped[$res['id']])){
					$grouped[$res['id']] = array();
					$grouped[$res['id']]['id'] = $res['id'];
					$grouped[$res['id']]['name'] = $res['name'];
					$grouped[$res['id']]['image'] = $res['image'];

				}
			}
			//dump($grouped);
			$result = array();
			$result['events'] = array();
			$result['events'] = array_values($grouped);
		}

		if($_REQUEST['start'] && $_REQUEST['limit'])
		{
			paging($result, $_REQUEST['start'], $_REQUEST['limit']);
		}

		$sTitleColor = $aMobTitleColors[0];
		if ($pageID != DEF_PAGE && $nRootPage != USERROOT_PAGE)
		{
			$nColorIdx = $nRootPage - 20;
			if (in_array($nColorIdx, array_keys($aMobTitleColors)))
				$sTitleColor = $aMobTitleColors[$nColorIdx];
			else
				$sTitleColor = $aMobTitleColors[0];
		}

		$result['category'] = array("id"=>$pageID, 'name'=>$GLOBALS['oPage']->GetByID($pageID)->Title, 'color'=>$sTitleColor);
		echoResult($result, $_REQUEST['callback']);
		break;
	}

	case 'eventDetails': {
		if($_REQUEST['category_id'])
		{
			$pageID = $_REQUEST['category_id'];
		} else {
			$aCategories = $oEvent->_ListEventPagesAsArray($_REQUEST['id']);
			$pageID = $aCategories[0];
		}
		$nRootPage = $oPage->GetRootPageID($pageID);
		$rRootPage = $oPage->GetByID($nRootPage);

		$sTitleColor = $aMobTitleColors[0];
		if ($pageID != DEF_PAGE && $nRootPage != USERROOT_PAGE)
		{
			$nColorIdx = $nRootPage - 20;
			if (in_array($nColorIdx, array_keys($aMobTitleColors)))
				$sTitleColor = $aMobTitleColors[$nColorIdx];
			else
				$sTitleColor = $aMobTitleColors[0];
		}
		$result['category'] = array("id"=>$pageID, 'name'=>$GLOBALS['oPage']->GetByID($pageID)->Title, 'color'=>$sTitleColor);

		$event = createDataArr($pageID, '', $city, $_REQUEST['id']);
		$result['event'] = $event;
		echoResult($result, $_REQUEST['callback']);
		break;
	}

	case 'placeDetails': {
		if($_REQUEST['category_id'])
		{
			$pageID = $_REQUEST['category_id'];
		} else {
			$aCategories = $oPlace->ListPlacePagesAsArray($_REQUEST['id']);
			$pageID = $aCategories[0];
		}
		$nRootPage = $oPage->GetRootPageID($pageID);
		$rRootPage = $oPage->GetByID($nRootPage);

		$sTitleColor = $aMobTitleColors[0];
		if ($pageID != DEF_PAGE && $nRootPage != USERROOT_PAGE)
		{
			$nColorIdx = $nRootPage - 20;
			if (in_array($nColorIdx, array_keys($aMobTitleColors)))
				$sTitleColor = $aMobTitleColors[$nColorIdx];
			else
				$sTitleColor = $aMobTitleColors[0];
		}
		$result['category'] = array("id"=>$pageID, 'name'=>$GLOBALS['oPage']->GetByID($pageID)->Title, 'color'=>$sTitleColor);

		$place = createDataArr($pageID, '', '', $_REQUEST['id']);
		$result['place'] = $place;
		echoResult($result, $_REQUEST['callback']);
		break;
	}

	case 'publicationDetails': {
		if($_REQUEST['category_id'])
		{
			$pageID = $_REQUEST['category_id'];
		} else {
			$aCategories = $oPublication->ListPublicationPagesAsArray($_REQUEST['id']);
			$pageID = $aCategories[0];
		}
		$nRootPage = $oPage->GetRootPageID($pageID);
		$rRootPage = $oPage->GetByID($nRootPage);

		$sTitleColor = $aMobTitleColors[0];
		if ($pageID != DEF_PAGE && $nRootPage != USERROOT_PAGE)
		{
			$nColorIdx = $nRootPage - 20;
			if (in_array($nColorIdx, array_keys($aMobTitleColors)))
				$sTitleColor = $aMobTitleColors[$nColorIdx];
			else
				$sTitleColor = $aMobTitleColors[0];
		}
		$result['category'] = array("id"=>$pageID, 'name'=>$GLOBALS['oPage']->GetByID($pageID)->Title, 'color'=>$sTitleColor);

		$publication = createDataArr($pageID, '', '', $_REQUEST['id']);
		$result['publication'] = $publication;
		echoResult($result, $_REQUEST['callback']);
		break;
	}

	case 'festivalDetails': {
		if($_REQUEST['category_id'])
		{
			$pageID = $_REQUEST['category_id'];
		} else {
			$aCategories = $oFestival->ListFestivalPagesAsArray($_REQUEST['id']);
			$pageID = $aCategories[0];
		}
		$nRootPage = $oPage->GetRootPageID($pageID);
		$rRootPage = $oPage->GetByID($nRootPage);

		$sTitleColor = $aMobTitleColors[0];
		if ($pageID != DEF_PAGE && $nRootPage != USERROOT_PAGE)
		{
			$nColorIdx = $nRootPage - 20;
			if (in_array($nColorIdx, array_keys($aMobTitleColors)))
				$sTitleColor = $aMobTitleColors[$nColorIdx];
			else
				$sTitleColor = $aMobTitleColors[0];
		}
		$result['category'] = array("id"=>$pageID, 'name'=>$GLOBALS['oPage']->GetByID($pageID)->Title, 'color'=>$sTitleColor);

		$festival = createDataArr($pageID, '', $city, $_REQUEST['id']);
		$result['festival'] = $festival;
		echoResult($result, $_REQUEST['callback']);
		break;
	}

	case 'newsDetails': {
		if($_REQUEST['category_id'])
		{
			$pageID = $_REQUEST['category_id'];
		} else {
			$aCategories = $oNews->ListNewsPagesAsArray($_REQUEST['id']);
			$pageID = $aCategories[0];
		}
		$nRootPage = $oPage->GetRootPageID($pageID);
		$rRootPage = $oPage->GetByID($nRootPage);

		$sTitleColor = $aMobTitleColors[0];
		if ($pageID != DEF_PAGE && $nRootPage != USERROOT_PAGE)
		{
			$nColorIdx = $nRootPage - 20;
			if (in_array($nColorIdx, array_keys($aMobTitleColors)))
				$sTitleColor = $aMobTitleColors[$nColorIdx];
			else
				$sTitleColor = $aMobTitleColors[0];
		}
		$result['category'] = array("id"=>$pageID, 'name'=>$GLOBALS['oPage']->GetByID($pageID)->Title, 'color'=>$sTitleColor);

		$news = createDataArr($pageID, '', '', $_REQUEST['id']);
		$result['publication'] = $news;
		echoResult($result, $_REQUEST['callback']);
		break;
	}

	case 'search': {
		global $oPage;
		$page = $_REQUEST['type'];
		$rCurrentPage = $oPage->GetByID($page);

		if(strstr($rCurrentPage->TemplateFile, 'place')) {
			$result = createDataArr('78', '', $city);
		}

		if(strstr($rCurrentPage->TemplateFile, 'event')) {
			$result = createDataArr('37', '', $city);
		}

		$nRootPage = $oPage->GetRootPageID($page);
		$rRootPage = $oPage->GetByID($nRootPage);

		$sTitleColor = $aMobTitleColors[0];
		if ($pageID != DEF_PAGE && $nRootPage != USERROOT_PAGE)
		{
			$nColorIdx = $nRootPage - 20;
			if (in_array($nColorIdx, array_keys($aMobTitleColors)))
				$sTitleColor = $aMobTitleColors[$nColorIdx];
			else
				$sTitleColor = $aMobTitleColors[0];
		}
		$result['category'] = array("id"=>$page, 'name'=>$GLOBALS['oPage']->GetByID($page)->Title, 'color'=>$sTitleColor);

		echoResult($result, $_REQUEST['callback']);

		break;
	}

	case 'giveMeAnIdea': {
	$bInSite = true;
	global $oPage, $oProgram, $oEvent, $oPlace, $oAddress, $oPhone, $oEmail, $oAttachment, $oLabel, $oProgramNote, $oLink;
	$pageArr = explode(',', $_REQUEST['categories']);

	$rootPageArr = array();
	foreach($pageArr as $cat) {
		$nRootPage = $oPage->GetRootPageID($cat);
		if($nRootPage)	
			array_push($rootPageArr, $nRootPage);
	}
	$rootPageArr = array_unique($rootPageArr);
	$result = array();
	$result['accents'] = array();

	foreach($rootPageArr as $page) {
		include('template/section.php');
	}
	echoResult($result, $_REQUEST['callback']);
	break;
	}

	case 'searchParameters': {
		$result = getTimeZones();
		echoResult($result, $callback);
		break;
	}
	default: {
		echo 'default request';
	}
}


function createDataArr($pageList, $pageType='', $city='', $item=''){
	$bInSite = true;
	global $oPage, $oProgram, $oEvent, $oPlace, $oAddress, $oPhone, $oEmail, $oAttachment, $oLabel, $oProgramNote, $oLink, $oPublication, $oPlaceGuide,$oNews, $oPlaceHall, $oFestival;
	$pageArr = explode(',', $pageList);
	$result = array();

	foreach($pageArr as $page) {
		$rCurrentPage = $oPage->GetByID($page);
		$nRootPage = $oPage->GetRootPageID($page);
		$rRootPage = $oPage->GetByID($nRootPage);
		if (!empty($rCurrentPage->TemplateFile))
		{
			if($pageType && !strstr($rCurrentPage->TemplateFile, $pageType)) {
				continue;
			}
			if (is_file('template/'.$rCurrentPage->TemplateFile.'.php'))
			{
				include('template/'.$rCurrentPage->TemplateFile.'.php');
			}
		}
	}
	return $result;
}

function echoResult($result, $callback){
	global $log_file;
	if($callback) {
		echo $callback.'('.json_encode($result).')';
		//file_put_contents($log_file, date('F j, Y, H:i:s').' > '.$_SERVER['QUERY_STRING'].'->'.$callback.'('.json_encode($result).')'."\n", FILE_APPEND);
	}
	else {
		echo json_encode($result);
		//file_put_contents($log_file, date('F j, Y, H:i:s').' > '.$_SERVER['QUERY_STRING'].'->'.json_encode($result)."\n", FILE_APPEND);
	}
}

function paging(&$result, $start, $limit){
		$keys = array_keys($result);
		$vals = array_values($result);
		$result = array($keys[0] => array_slice($vals[0], $start, $limits, false));
}

function getAllCities($callback) {
	if($callback) {
		return $callback.'('.json_encode(getLabel('aCitiesAll')).')';

	}
	else {
		return json_encode(getLabel('aCitiesAll'));
	}
}

function getTimeZones(){
	$time_zone = array();
	foreach($GLOBALS['aTimes'] as $key=>$val){
		$zone = array();
		$zone['id'] = $key;
		$zone['zone'] = $val;
		array_push($time_zone, $zone);
	}
	return array('time_zone'=>$time_zone);
}

function getMainCities($callback) {
	$aLngMin = array(1=>42650627, 2=>42117453, 3=>43206302, 4=>42464879, 14=>42398869);
	$aLatMin = array(1=>23220978, 2=>24688339, 3=>27839012, 4=>27400932, 14=>25585442);
	$aLngMax = array(1=>42745247, 2=>42178034, 3=>43255205, 4=>42533224, 14=>42447781);
	$aLatMax = array(1=>23413925, 2=>24784985, 3=>27977715, 4=>27493801, 14=>25651703);
	$aLngCenter = array(1=>42702875, 2=>42144187, 3=>43218062, 4=>42497922, 14=>42424470);
	$LatCenter = array(1=>23322258, 2=>24749966, 3=>27911625, 4=>27469769, 14=>25624924);
	$all = getLabel('aCities');
	$return = array();
	foreach($all as $id => $name){
		$city = array();
		$city['id'] = $id;
		$city['name'] = $name;
		$city['lngmin'] = $aLngMin[$id];
		$city['latmin'] = $aLatMin[$id];
		$city['lngmax'] = $aLngMax[$id];
		$city['latmax'] = $aLatMax[$id];
		$city['lngcenter'] = $aLngCenter[$id];
		$city['latcenter'] = $LatCenter[$id];

		array_push($return, $city);
	}
	if($callback) {
		return $callback.'('.json_encode(array("cities" => $return)).')';
	}
	else {
		return json_encode(array("cities" => $return));
	}
}

function getMainMenu($callback) {
	$rsPage = $GLOBALS['oPage']->ListAll(DEF_PAGE);
	$menuArr = array();
	while($row = mysql_fetch_object($rsPage))
	{
		if (!in_array($row->PageID, $GLOBALS['aSysNavigation']))
		{
			$filtered = $GLOBALS['oPage']->ListPageCityFiltersAsArray($row->PageID);
			if (in_array($city, $filtered) || empty($filtered))
			{
				$menu = array();
				$menu['id'] = $row->PageID;
				$menu['name'] = $row->Title;
				$menu['type'] = 0;
				$menu['sub'] = getSubmenu($row->PageID);

				array_push($menuArr, $menu);
			}
		}
	}
	if($callback) {
		return $callback.'('.json_encode(array("menu"=>$menuArr)).')';
	}
	else {
		return json_encode(array("menu"=>$menuArr));
	}
}

function getSubmenu($id){
	$strMenuArr = array();
    $rsPage = $GLOBALS['oPage']->ListAll($id, '', '', false);
	while($row = mysql_fetch_object($rsPage))
	{
		$submenu = array();
		$submenu['id'] = $row->PageID;
		$submenu['name'] = $row->Title;

		if(strstr($row->TemplateFile, "place"))
		{
			$submenu['type'] = 1;
		}
		elseif(strstr($row->TemplateFile, "event"))
		{
			$submenu['type'] = 2;
		}
		else
			$submenu['type'] = 0;

		if(!strstr($row->TemplateFile, "publication") &&
			!strstr($row->TemplateFile, "news") &&
			!strstr($row->TemplateFile, "search") &&
//			!strstr($row->TemplateFile, "festival") &&
			!strstr($row->TemplateFile, "extra")) {
			array_push($strMenuArr, $submenu);
		}
	}
	return $strMenuArr;
}

function getProfiles($callback) {
	$arrAll = array();

	$classic = array(	'name'=>"Classic",
						'pages' => array(29, 30, 40, 106, 39, 47, 52, 63, 62, 71, 98, 97, 74, 88),
						'description'=>getLabel('strClassicProfile'),
						'image' => "img/profile-classic-72");
	$trendy = array(	'name'=>"Trendy",
						'pages'=>array(29, 30, 39, 55, 52, 62, 64, 69, 71, 98, 143, 73, 72),
						'description'=>getLabel('strTrendyProfile'),
						'image' => "img/profile-trendy-72");
	$family = array(	'name'=>"Family",
						'pages'=>array(29, 30, 33, 42, 56, 97, 71, 72),
						'description'=>getLabel('strFamilyProfile'),
						'image' => 'img/profile-family-72');

	array_push($arrAll, $trendy);
	array_push($arrAll, $classic);
	array_push($arrAll, $family);
	if($callback) {
		return $callback.'('.json_encode(array('profiles' => $arrAll)).')';
	}
	else {
		return json_encode(array('profiles' => $arrAll));
	}
}
?>