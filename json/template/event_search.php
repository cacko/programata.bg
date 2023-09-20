<?php
if (!$bInSite) die();
//=========================================================
	$sKeyword = $_REQUEST['keyword'];
	$nSelPage = $_REQUEST['type'];
	$page = $_REQUEST['type'];
	$nSelCity = $_REQUEST['city'];
	if($_REQUEST['date']){
		$dStartDate = $dEndDate = $_REQUEST['date'];
	} else {
		$dStartDate = date(DEFAULT_DATE_DB_FORMAT);
		$dEndDate = increaseDate($dStartDate, THIS_WEEK_DAYS);
	}
	$dSelTime = $_REQUEST['timezone'];
	if (in_array($dSelTime, array_keys($GLOBALS['aTimes'])))
	{
	    $dSelStartTime = $aStartTimes[$dSelTime];
	    $dSelEndTime = $aEndTimes[$dSelTime];
	}

    $rsEvent = $oProgram->ListAllEvents($nSelPage, null, $nSelCity, $sKeyword, '', $dStartDate, $dEndDate, $dSelStartTime, $dSelEndTime);
        include_once('template/event_list.php');

        $grouped = array();
		foreach($result['events'] as $res)	{
			if(!isset($grouped[$res['id']])){
				$grouped[$res['id']] = array();
				$grouped[$res['id']]['id'] = $res['id'];
				$grouped[$res['id']]['name'] = $res['name'];
				$grouped[$res['id']]['type'] = $res['type'];
				$grouped[$res['id']]['image'] = $res['image'];

			}
		}
		//dump($grouped);
		$result = array();
		$result['events'] = array();
		$result['events'] = array_values($grouped);
		?>