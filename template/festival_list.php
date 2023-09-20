<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	include('template/festival_details.php');
}
if (!isset($item) || empty($item))
{
	
	echo '<div id="container">
            	<ul class="photo-list">';	
	$dToday = date(DEFAULT_DATE_DB_FORMAT);
	$dStartDate = null; // increaseDate($dToday, 0, -ARCHIVE_MONTHS);
	$dEndDate = null;
	
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
			if ($curRow >= $nStartRow && $curRow < $nEndRow)
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
				$sMainImageFile = UPLOAD_DIR.IMG_FESTIVAL_MID.$row->FestivalID.'.'.EXT_IMG;
				if (!is_file($sMainImageFile))
				{
					$sMainImageFile = UPLOAD_DIR.IMG_EMPTY.'.'.EXT_PNG;
				}
				$sRelPlaces = '';
				$rsRelPlaces = $oProgram->ListPlacesByFestivalID($row->FestivalID, $city, $row->StartDate, $row->EndDate);
				$nPageToGo = 0;
				while($rPlace = mysql_fetch_object($rsRelPlaces))
				{
					$aRelPages = $oPlace->ListPlacePagesAsArray($rPlace->PlaceID);
					if(is_array($aRelPages) && count($aRelPages) > 0)
						$nPageToGo = $aRelPages[0];
					if (!empty($sRelPlaces))
						$sRelPlaces .= ', ';
					$sRelPlaces .= '<a href="'.setPage($nPageToGo, 0, $rPlace->PlaceID).'" title="'.stripComments(IIF(!empty($rPlace->ShortTitle), $rPlace->ShortTitle, $rPlace->Title)).'">'.stripComments(IIF(!empty($rPlace->ShortTitle), $rPlace->ShortTitle, $rPlace->Title)).'</a>';
				}
				$sDate = '';
				if ($row->StartDate === $row->EndDate)
					$sDate .= formatDate($row->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
				elseif (formatDate($row->StartDate, FULL_MONTH_YEAR_DISPLAY_FORMAT) === formatDate($row->EndDate, FULL_MONTH_YEAR_DISPLAY_FORMAT))
					$sDate .= formatDate($row->StartDate, DAY_DISPLAY_FORMAT).' - '.formatDate($row->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
				else
					$sDate .= formatDate($row->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT).' - '.formatDate($row->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
				
				//$nComments = $oComment->GetCountByEntity($row->FestivalID, $aEntityTypes[ENT_FESTIVAL]);
				
//				echo '<div class="box">
//				<h3><a href="'.setPage($page, 0, $row->FestivalID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.$row->Title.'</a></h3>
//				<a href="'.setPage($page, 0, $row->FestivalID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.drawImage($sMainImageFile, 0, 0, $row->Title).'</a>
//				<div class="date">'.$sDate.'</div>
//				<div>'.$row->Lead.'</div>
//				<div>'.$sRelPlaces.'</div>
//				</div>'."\n";
				
            	echo '
                	<li>
                    	<h2><a href="'.setPage($page, 0, $row->FestivalID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.htmlspecialchars(strip_tags($row->Title)).'</a></h2>
                        <div class="text">
                        	<div class="preview">
                            	<a href="'.setPage($page, 0, $row->FestivalID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">
                                	'.drawImage($sMainImageFile, 300, 164, $row->Title).'
                                </a>
                            </div>
                        	<span>'.$sDate .'</span>
                        </div>
                        <div class="text alone">
                            '. (!empty($row->Lead) ? $row->Lead .'<br />' : '') .'
                            '. (!empty($sRelPlaces) ? $sRelPlaces .'<br />' : '') .'
                        </div>
                    </li>
            	';				
				
			}
			$curRow++;
		}
		
		if ($nTotalRows > NUM_ROWS_PUBLIC)
			echo '<div class="paging" style="margin-top: 10px">'.showPages($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_PUBLIC).'</div>'."\n";
	}
	else
	{
		echo '<div>'.getLabel('strNoRecords').'</div>'."\n";
	}
	echo '</ul></div>';	
}
?>