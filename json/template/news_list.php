<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	include('template/news_details.php');
}
/*
if (!isset($item) || empty($item))
{
	$dToday = date(DEFAULT_DATE_DB_FORMAT);
	$dStartDate = null; //increaseDate($dToday, 0, -ARCHIVE_MONTHS);
	$dEndDate = $dToday;

	if (!isset($rsNews))
		$rsNews = $oNews->ListAll($page, $city, '', $dStartDate, $dEndDate, false, 0, $aContext);
	if (mysql_num_rows($rsNews))
	{
		$aPages = $oPage->ListAllAsArraySimple();

		$curRow = 0;
		$nTotalRows = mysql_num_rows($rsNews);
		$nStartRow = $aContext[CUR_REC];
		$nEndRow = $nStartRow + NUM_ROWS_PUBLIC;

		//if ($nTotalRows > NUM_ROWS_PUBLIC)
		//	echo '<div class="hr"><div class="paging">'.showPages($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_PUBLIC).'</div></div>';

		while($row=mysql_fetch_object($rsNews))
		{
			if ($curRow >= $nStartRow && $curRow < $nEndRow)
			{
				$sMainImageFile = UPLOAD_DIR.IMG_NEWS_MID.$row->NewsID.'.'.EXT_IMG;
				if (!is_file($sMainImageFile))
				{
					$sMainImageFile = UPLOAD_DIR.IMG_MID.'.'.EXT_PNG;
				}


				//$nComments = $oComment->GetCountByEntity($row->NewsID, $aEntityTypes[ENT_NEWS]);

				echo '<div class="box">
				<h3><a href="'.setPage($page, 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.$row->Title.'</a></h3>
				<a href="'.setPage($page, 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.drawImage($sMainImageFile, 0, 0, $row->Title).'</a>
				<div class="date">'.formatDate($row->NewsDate, FULL_DATE_YEAR_DISPLAY_FORMAT).getLabel('strByUser').$row->Author.'</div>
				<div>'.IIF(!empty($row->Lead), $row->Lead, strShorten($row->Content, TEXT_LEN_PUBLIC)).'</div>
				</div>'."\n";
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