<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	include('template/urban_details.php');
}
if (!isset($item) || empty($item))
{
	$dToday = date(DEFAULT_DATE_DB_FORMAT);
	$dStartDate = null; //increaseDate($dToday, 0, -ARCHIVE_MONTHS);
	$dEndDate = $dToday;
	echo '        	<div id="container">
            	<ul class="multiple-photo-list">';
	if (!isset($rsUrbans))
		$rsUrbans = $oUrban->ListAll($page, '', false);
	if (mysql_num_rows($rsUrbans))
	{
		$aPages = $oPage->ListAllAsArraySimple();
		
		$curRow = 0;
		$nTotalRows = mysql_num_rows($rsUrbans);
		$nStartRow = $aContext[CUR_REC];
		$nEndRow = $nStartRow + NUM_ROWS_PUBLIC;
		
		$i = 0;
		
		while($row=mysql_fetch_object($rsUrbans))
		{
			if ($curRow >= $nStartRow && $curRow < $nEndRow)
			{
//				$i++;
//				if ($i > NUM_PUBLICATIONS_FULL)
//				{
//					echo '<br />
//					<h3><a href="'.setPage($page, 0, $row->UrbanID).'" title="'.htmlspecialchars(strip_tags($row->MainTitle)).'">'.$row->MainTitle.'</a></h3>'.
//					'<div class="date"><a href="'.setPage($page, 0, $row->UrbanID).'" title="'.htmlspecialchars(strip_tags($row->MainTitle)).'">
//					'.formatDate($row->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</a></div>'."\n";
//				}
//				else
//				{
					$sMainImageFile1 = UPLOAD_DIR.IMG_URBAN.$row->UrbanID.'/'.IMG_MID_THUMB.'1_1.'.EXT_IMG;
					if (!is_file($sMainImageFile1))
					{
						$sMainImageFile1 = UPLOAD_DIR.IMG_EMPTY.'.'.EXT_PNG;
					}
					
					$sMainImageFile2 = UPLOAD_DIR.IMG_URBAN.$row->UrbanID.'/'.IMG_MID_THUMB.'2_1.'.EXT_IMG;
					if (!is_file($sMainImageFile2))
					{
						$sMainImageFile2 = UPLOAD_DIR.IMG_EMPTY.'.'.EXT_PNG;
					}
					$sMainImageFile3 = UPLOAD_DIR.IMG_URBAN.$row->UrbanID.'/'.IMG_MID_THUMB.'3_1.'.EXT_IMG;
					if (!is_file($sMainImageFile3))
					{
						$sMainImageFile3 = UPLOAD_DIR.IMG_EMPTY.'.'.EXT_PNG;
					}

//					echo '<div class="box">
//					<h3>
//						<a href="'.setPage($page, 0, $row->UrbanID).'" title="'.htmlspecialchars(strip_tags($row->MainTitle)).'">
//						'.$row->MainTitle.'</a><br />
//					</h3>'.
//					'<a href="'.setPage($page, 0, $row->UrbanID).'" title="'.htmlspecialchars(strip_tags($row->Title1)).'">'.
//						drawImage($sMainImageFile1, 0, 0, $row->Title1).
//					'</a></div>';
//					
					
				echo '
                	<li>
                    	<h2><a href="'.setPage($page, 0, $row->UrbanID).'">'. htmlspecialchars(strip_tags($row->MainTitle)) .'</a></h2>
                        <p><a href="'.setPage($page, 0, $row->UrbanID).'">'.formatDate($row->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</a></p>
                        <div>
                            <a href="'.setPage($page, 0, $row->UrbanID).'">'.drawImage($sMainImageFile1, 140, 77, $row->Title1) .'</a>
                            <a href="'.setPage($page, 0, $row->UrbanID).'">'.drawImage($sMainImageFile2, 140, 77, $row->Title2) .'</a>
                            <a href="'.setPage($page, 0, $row->UrbanID).'">'.drawImage($sMainImageFile3, 140, 77, $row->Title3) .'</a>
                        </div>
                    </li>				
				';


//					echo '
//	                	<li>
//	                    	<h2><a href="'.setPage($page, 0, $row->UrbanID).'" title="'.htmlspecialchars(strip_tags($row->MainTitle)).'">'.htmlspecialchars(strip_tags($row->MainTitle)).'</a></h2>
//	                        <div class="text">
//	                        	<div class="preview">
//	                            	<a href="'.setPage($page, 0, $row->UrbanID).'" title="'.htmlspecialchars(strip_tags($row->MainTitle)).'">
//	                                	'.drawImage($sMainImageFile1, 0, 0, $row->Title1) .'
//	                                </a>
//	                            </div>
//	                        	<span><a href="'.setPage($page, 0, $row->UrbanID).'" title="'.htmlspecialchars(strip_tags($row->MainTitle)).'">'.formatDate($row->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</a></span>
//	                            <p>'.$row->Title1.'</p>
//	                        </div>
//	                    </li>					
//					';		
					
//					<a href="'.setPage($page, 0, $row->UrbanID).'" title="'.htmlspecialchars(strip_tags($row->Title2)).'">'.	
//					drawImage($sMainImageFile2, 0, 0, $row->Title2).
//					'</a>
//					<a href="'.setPage($page, 0, $row->UrbanID).'" title="'.htmlspecialchars(strip_tags($row->Title3)).'">'.	
//					drawImage($sMainImageFile3, 0, 0, $row->Title3).'</a>
//					<div class="date"><a href="'.setPage($page, 0, $row->UrbanID).'" title="'.htmlspecialchars(strip_tags($row->MainTitle)).'">
//					'.formatDate($row->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</a></div>
//					</div>'."\n";
//				}
			}
			$curRow++;
		}

		
		echo '</ul>';
		
		if ($nTotalRows > NUM_ROWS_PUBLIC)
			echo '<div class="paging">'.showPages($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_PUBLIC).'</div>'."\n";
			
		echo '</div>';
	}
	else
	{
		echo '<div>'.getLabel('strNoRecords').'</div>'."\n";
	}
}
?>