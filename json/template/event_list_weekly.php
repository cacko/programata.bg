<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	include('template/event_details.php');
}
if (!isset($item) || empty($item))
{
	$sSelectedBullet = '';
	if (isset($dSelStartDate) && !empty($dSelStartDate))
	{
		$dStartDate = $dSelStartDate;
		$dEndDate = $dSelEndDate;
		//$sSelectedBullet .= '<ul class="inline_nav"><li class="this on"><a href="#">'.formatDate($dSelStartDate, FULL_DATE_DISPLAY_FORMAT).'</a></li></ul>';
	}
	else
	{
		if (empty($cat)) $cat = 2;
		$dToday = date(DEFAULT_DATE_DB_FORMAT);
		$dStartDate = $dToday;
		// show yesterday events before 5 a.m.
		$dCurrentTime = date(DEFAULT_TIME_DISPLAY_FORMAT);
		if ($dCurrentTime <= TODAY_START)
			$dStartDate = increaseDate($dToday, -1);
		$dEndDate = increaseDate($dToday, THIS_WEEK_DAYS-1);
		switch($cat)
		{
			case 1:
				// today
				$dEndDate = $dToday;
				break;
			case 2:
				// this week
				$dEndDate = increaseDate($dToday, THIS_WEEK_DAYS-1);
				break;
			case 3:
				// this month
				$dEndDate = increaseDate($dToday, 0, 1);
				break;
		}
	}
	$dStartTime=null;
	$dEndTime=null;
	if (isset($dSelStartTime) && !empty($dSelEndTime))
	{
		$dStartTime = $dSelStartTime;
		$dEndTime = $dSelEndTime;
	}
	//$rsEvent = $oProgram->ListAllEvents($page, null, $city, '', '', $dStartDate, $dEndDate, $dStartTime, $dEndTime);
	include('event_list.php');
}
?>
