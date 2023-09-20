<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	include('template/extra_details.php');
}
if (!isset($item) || empty($item))
{
	echo '<div id="container">
            	<ul class="photo-list">';	
	$dToday = date(DEFAULT_DATE_DB_FORMAT);
	$dStartDate = null; //increaseDate($dToday, 0, -ARCHIVE_MONTHS);
	$dEndDate = $dToday;
	
	if (!isset($rsExtras))
		$rsExtras = $oExtra->ListAll($page, '', $dStartDate, $dEndDate, false, 0, $aContext);
	if (mysql_num_rows($rsExtras))
	{
		$aPages = $oPage->ListAllAsArraySimple();
		
		$curRow = 0;
		$nTotalRows = mysql_num_rows($rsExtras);
		$nStartRow = $aContext[CUR_REC];
		$nEndRow = $nStartRow + NUM_ROWS_PUBLIC;
		
		//if ($nTotalRows > NUM_ROWS_PUBLIC)
		//	echo '<div class="hr"><div class="paging">'.showPages($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_PUBLIC).'</div></div>';
		$i = 0;
		while($row=mysql_fetch_object($rsExtras))
		{
			if ($curRow >= $nStartRow && $curRow < $nEndRow)
			{
//				$i++;
//				if ($i > NUM_PUBLICATIONS_FULL)
//				{
//					echo '<br />
//					<h3><a href="'.setPage($page, 0, $row->PublicationID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.$row->Title.'</a></h3>
//					'.IIF(!empty($row->Subtitle), '<div>'.$row->Subtitle.'</div>', '')."\n";
//				}
//				else
//				{
					// PAGES - CATEGORIES
					/*$sPages = '';
					$aRelPages = $oExtra->ListPublicationPagesAsArray($row->PublicationID);
					if (count($aRelPages)>0)
					{
						foreach($aRelPages as $key)
						{
							if (!empty($sPages)) $sPages .= ', ';
							$sPages .= '<a href="'.setPage($key).'" title="'.$aPages[$key].'">'.$aPages[$key].'</a>';
						}
						$sPages = ' :: '.$sPages;
					}*/
					$sMainImageFile = UPLOAD_DIR.IMG_EXTRA_MID.$row->PublicationID.'.'.EXT_IMG;
					if (!is_file($sMainImageFile))
					{
						$sMainImageFile = UPLOAD_DIR.IMG_EMPTY.'.'.EXT_PNG;
					}
					
					//$nComments = $oComment->GetCountByEntity($row->PublicationID, $aEntityTypes[ENT_EXTRA]);
					
//					echo '<div class="box">
//					<h3><a href="'.setPage($page, 0, $row->PublicationID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.$row->Title.'</a></h3>
//					'.IIF(!empty($row->Subtitle), '<div>'.$row->Subtitle.'</div>', '').'
//					<a href="'.setPage($page, 0, $row->PublicationID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.drawImage($sMainImageFile, 0, 0, $row->Title).'</a>
//					<div class="date">'.formatDate($row->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT).getLabel('strByUser').$row->Author.'</div>
//					<div>'.IIF(!empty($row->Lead), $row->Lead, strShorten($row->Content, TEXT_LEN_PUBLIC)).'</div>
//					</div>'."\n";
	            	echo '
	                	<li>
	                    	<h2><a href="'.setPage($page, 0, $row->PublicationID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.htmlspecialchars(strip_tags($row->Title)).'</a></h2>
	                        <div class="text">
	                        	<div class="preview">
	                            	<a href="'.setPage($page, 0, $row->PublicationID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">
	                                	'.drawImage($sMainImageFile, 300, 164, htmlspecialchars(strip_tags($row->Title))).'
	                                </a>
	                            </div>
	                        	<span>'. formatDate($row->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT).getLabel('strByUser') .'</span>
	                            <p>'. $row->Author .'</p>
	                        </div>
	                        '. IIF(!empty($row->Lead), '<div class="text alone">'. $row->Lead .'</div>', '<div class="text alone">'. strShorten($row->Content, TEXT_LEN_PUBLIC) .'</div>') .'
	                    </li>
	            	';
//				}
			}
			$curRow++;
		}
		
		if ($nTotalRows > NUM_ROWS_PUBLIC)
			echo '<div class="paging" style="margin-top: 10px;">'.showPages($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_PUBLIC).'</div>'."\n";
	}
	else
	{
		echo '<div>'.getLabel('strNoRecords').'</div>'."\n";
	}
	echo '</ul></div>';	
}
?>