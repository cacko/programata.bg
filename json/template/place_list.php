<?php
if (!$bInSite) die();
//=========================================================

if (isset($item) && !empty($item))
{
	include('template/place_details.php');
}
if (!isset($item) || empty($item))
{
	if(!isset($result['places'])) {
		$result['places'] = array();
	}
 	if($_REQUEST['request'] == 'search'){
 	}
 	elseif($_REQUEST['lng'] && $_REQUEST['lat'] && $_REQUEST['r']){
 		$aDistance = getBoundingBox($_REQUEST['lng']/1000000, $_REQUEST['lat']/1000000, $_REQUEST['r']);
 		$rsPlace = $GLOBALS['oPlace']->ListAll($page, $city, null, null, '', '', false, false, 0, $GLOBALS['aContext'], $aDistance[0], $aDistance[1], $aDistance[2], $aDistance[3]);
 	} else {
		$rsPlace = $GLOBALS['oPlace']->ListAll($page, $city, null, null, '', '', false, false, 0, $GLOBALS['aContext']);
 	}

	$aAllCities = getLabel('aCitiesAll');
	while($row=mysql_fetch_object($rsPlace))
	{
			$sAddress = '';
			$rsAddress = $GLOBALS['oAddress']->ListAll($row->PlaceID, $aEntityTypes[ENT_PLACE], 1);
			if(mysql_num_rows($rsAddress))
			{
				while($rAddress = mysql_fetch_object($rsAddress))
				{
					$sAddress .= $aAllCities[$rAddress->CityID].', ';
					$sAddress .= $rAddress->Street;
				}
			}
			$place = array();
			if($sAddress)	$place['address'] = $sAddress;
			if($row->Title)	$place['name'] = stripComments($row->Title);
			$place['id'] = $row->PlaceID;

			//gps
			$place['lng'] = substr(str_pad(str_replace(".", "", $row->long), 8, '0', STR_PAD_RIGHT), 0, 8);
			$place['lat'] = substr(str_pad(str_replace(".", "", $row->lat), 8, '0', STR_PAD_RIGHT), 0, 8);

			if($_REQUEST['w'] >= 450) {
				$sMainImageFile = UPLOAD_DIR.IMG_PLACE.$row->EventID.'.'.EXT_IMG;
			}
			else {
				$sMainImageFile = UPLOAD_DIR.IMG_PLACE_MID.$row->PlaceID.'.'.EXT_IMG;
			}
			if (is_file('../'.$sMainImageFile))
			{
				$place['image'] = $sMainImageFile;
			}

			//working time
			if($row->WorkingTime)	$place['working_time'] = $row->WorkingTime;

			//phone
			$rsPhone = $GLOBALS['oPhone']->ListAll($row->PlaceID, $aEntityTypes[ENT_PLACE], array(1,2,3,6));
			if(mysql_num_rows($rsPhone))
			{
				$aPhoneTypes = getLabel('aPhoneTypes');
				while($rPhone = mysql_fetch_object($rsPhone))
				{
					$place['phone'] = str_replace(" ", "", $rPhone->Area.$rPhone->Phone.$rPhone->Ext);
				}
			}

			if($_REQUEST['r']){
				$nRootPage = $oPage->GetRootPageID($page);
				$place['main_type'] = $nRootPage;
				$place['type'] = $page;

				$place['range'] = twoPointsDistance($_REQUEST['lat']/1000000, $_REQUEST['lng']/1000000, $row->lat, $row->long);
				if($place['range'] > $_REQUEST['r']) continue;
			}

			array_push($result['places'], $place);

	}
}
?>