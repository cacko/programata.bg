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
//		if (empty($cat)) $cat = 2;
		if (empty($cat)) $cat = 3; //show premiers for next month
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
?>
<!--ul class="inline_nav"-->
	<!--li class="next<?=IIF($cat==2, ' on', '')?>"><a href="<?=setPage($page, 2, $item)?>"><?=getLabel('strThisWeek')?></a></li-->
	<!--li class="this<?=IIF($cat==1, ' on', '')?>"><a href="<?=setPage($page, 1, $item)?>"><?=getLabel('strToday')?></a></li-->
	<!--li class="program"><a class="aindex" href="#"><?=getLabel('strIndexDetails')?></a></li-->
	<?//$sSelectedBullet?>
<!--/ul-->
<?
	//$nPageToGo = FILM_PAGE;
	//$bShowPremiere = true;
	//$rsEvent = $oProgram->ListAllEvents($nPageToGo, null, $city, '', '', $dStartDate, $dEndDate, $dStartTime, $dEndTime, true);
	include_once('event_list.php');
}
?>
