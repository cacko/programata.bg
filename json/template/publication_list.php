<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	include('template/publication_details.php');
}
/*
if (!isset($item) || empty($item))
{
	$dToday = date(DEFAULT_DATE_DB_FORMAT);
	$dStartDate = null; //increaseDate($dToday, 0, -ARCHIVE_MONTHS);
	$dEndDate = $dToday;

	if (!isset($rsPublications))
		$rsPublications = $oPublication->ListAll($page, '', $dStartDate, $dEndDate, false, 0, $aContext);
	if (mysql_num_rows($rsPublications))
	{
		$aPages = $oPage->ListAllAsArraySimple();

		$curRow = 0;
		$nTotalRows = mysql_num_rows($rsPublications);
		$nStartRow = $aContext[CUR_REC];
		$nEndRow = $nStartRow + NUM_ROWS_PUBLIC;

		//if ($nTotalRows > NUM_ROWS_PUBLIC)
		//	echo '<div class="hr"><div class="paging">'.showPages($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_PUBLIC).'</div></div>';
		$i = 0;
		while($row=mysql_fetch_object($rsPublications))
		{
			if ($curRow >= $nStartRow && $curRow < $nEndRow)
			{
				$i++;
				if ($i > NUM_PUBLICATIONS_FULL)
				{
					echo '<br />
					<h3><a href="'.setPage($page, 0, $row->PublicationID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.$row->Title.'</a></h3>
					'.IIF(!empty($row->Subtitle), '<div>'.$row->Subtitle.'</div>', '')."\n";
				}
				else
				{
					$sMainImageFile = UPLOAD_DIR.IMG_PUBLICATION_MID.$row->PublicationID.'.'.EXT_IMG;
					if (!is_file($sMainImageFile))
					{
						$sMainImageFile = UPLOAD_DIR.IMG_EMPTY.'.'.EXT_PNG;
					}

					//$nComments = $oComment->GetCountByEntity($row->PublicationID, $aEntityTypes[ENT_PUBLICATION]);

					echo '<div class="box">
					<h3><a href="'.setPage($page, 0, $row->PublicationID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.$row->Title.'</a></h3>
					'.IIF(!empty($row->Subtitle), '<div>'.$row->Subtitle.'</div>', '').'
					<a href="'.setPage($page, 0, $row->PublicationID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.drawImage($sMainImageFile, 0, 0, $row->Title).'</a>
					<div class="date">'.formatDate($row->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT).getLabel('strByUser').$row->Author.'</div>
					<div>'.IIF(!empty($row->Lead), $row->Lead, strShorten($row->Content, TEXT_LEN_PUBLIC)).'</div>
					</div>'."\n";
				}
			}
			$curRow++;
		}

		if ($nTotalRows > NUM_ROWS_PUBLIC)
			echo '<div class="paging">'.showPages($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_PUBLIC).'</div>'."\n";
	}
	else
	{
		echo '<div>'.getLabel('strNoRecords').'</div>'."\n";
	}
}
*/
?>