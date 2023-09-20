<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	include('template/festival_details.php');
}
if (!isset($item) || empty($item))
{
	$dToday = date(DEFAULT_DATE_DB_FORMAT);
	$dStartDate = null; // increaseDate($dToday, 0, -ARCHIVE_MONTHS);
	$dEndDate = null;
	
	if(!isset($result['festivals'])) {
		$result['festivals'] = array();
	}

	if (!isset($rsFestival))
		$rsFestival = $oFestival->ListAll($page, $city, '', $dStartDate, $dEndDate, false, 0, $aContext);
	if (mysql_num_rows($rsFestival))
	{
		//$aPages = $oPage->ListAllAsArraySimple();
		
		$curRow = 0;
		$nTotalRows = mysql_num_rows($rsFestival);
		$nStartRow = $aContext[CUR_REC];
		$nEndRow = $nStartRow + NUM_ROWS_PUBLIC;
		
		//if ($nTotalRows > NUM_ROWS_PUBLIC)
		//	echo '<div class="hr"><div class="paging">'.showPages($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_PUBLIC).'</div></div>';
		
		while($row=mysql_fetch_object($rsFestival))
		{
			$festival = array();
			if ($row->EndDate >= $dToday)
			{
				// PAGES - CATEGORIES
				/*$sPages = '';
				$aRelPages = $oFestival->ListFestivalPagesAsArray($row->FestivalID);
				if (count($aRelPages)>0)
				{
					foreach($aRelPages as $key)
					{
						if (!empty($sPages)) $sPages .= ', ';
						$sPages .= '<a href="'.setPage($key).'" title="'.$aPages[$key].'">'.$aPages[$key].'</a>';
					}
					$sPages = ' :: '.$sPages;
				}*/
				
				$festival['name'] = stripComments($row->Title);
				$festival['id'] = $row->FestivalID;
				$sMainImageFile = UPLOAD_DIR.IMG_FESTIVAL_MID.$row->FestivalID.'.'.EXT_IMG;

				if (is_file('../'.$sMainImageFile))
				{
					$festival['image'] = $sMainImageFile;
				}
				
				$sRelPlaces = '';
				$rsRelPlaces = $oProgram->ListPlacesByFestivalID($row->FestivalID, $city, $row->StartDate, $row->EndDate);
				$nPageToGo = 0;
				$places = array();
				while($rPlace = mysql_fetch_object($rsRelPlaces))
				{
					$place = array();
					$aRelPages = $oPlace->ListPlacePagesAsArray($rPlace->PlaceID);
					if(is_array($aRelPages) && count($aRelPages) > 0)
						$nPageToGo = $aRelPages[0];
					if (!empty($sRelPlaces))
						$sRelPlaces .= ', ';
					$place['id'] = $rPlace->PlaceID;
					$place['name'] = stripComments(IIF(!empty($rPlace->ShortTitle), $rPlace->ShortTitle, $rPlace->Title));
					array_push($places, $place);
				}
				$festival['places'] = $places;
				$sDate = '';
				if ($row->StartDate === $row->EndDate)
					$sDate .= formatDate($row->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
				elseif (formatDate($row->StartDate, FULL_MONTH_YEAR_DISPLAY_FORMAT) === formatDate($row->EndDate, FULL_MONTH_YEAR_DISPLAY_FORMAT))
					$sDate .= formatDate($row->StartDate, DAY_DISPLAY_FORMAT).' - '.formatDate($row->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
				else
					$sDate .= formatDate($row->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT).' - '.formatDate($row->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
				
				//$nComments = $oComment->GetCountByEntity($row->FestivalID, $aEntityTypes[ENT_FESTIVAL]);
				
				$festival['date'] = $sDate;
				$festival['lead'] = $row->Lead;
				array_push($result['festivals'], $festival);
			}
			$curRow++;
		}
	}
		
}
?>