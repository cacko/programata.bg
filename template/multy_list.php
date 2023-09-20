<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	include('template/multy_details.php');
}
if (!isset($item) || empty($item))
{
	echo '<div id="container">
            	<ul class="photo-list">';
	$dToday = date(DEFAULT_DATE_DB_FORMAT);
	$dStartDate = null; //increaseDate($dToday, 0, -ARCHIVE_MONTHS);
	$dEndDate = $dToday;

	if (!isset($rsMulty))
		$rsMulty = $oMulty->ListAll($page, '', $dStartDate, $dEndDate, false, 0, $aContext);
	if (mysql_num_rows($rsMulty))
	{
		$aPages = $oPage->ListAllAsArraySimple();

		$curRow = 0;
		$nTotalRows = mysql_num_rows($rsMulty);
		$nStartRow = $aContext[CUR_REC];
		$nEndRow = $nStartRow + NUM_ROWS_PUBLIC;

		$i = 0;

		while($row=mysql_fetch_object($rsMulty))
		{
			if ($curRow >= $nStartRow && $curRow < $nEndRow)
			{
				#d($row);
//				$i++;
//				if ($i > NUM_PUBLICATIONS_FULL)
//				{
//					echo '<br />
//					<h3><a href="'.setPage($page, 0, $row->MultyID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.$row->Title.'</a></h3>'."\n";
//				}
//				else
//				{
					$sMainImageFile = UPLOAD_DIR.IMG_MULTY.$row->MultyID.'/'.IMG_THUMB.'1.'.EXT_IMG;
					if (!is_file($sMainImageFile))
					{
						$sMainImageFile = UPLOAD_DIR.IMG_EMPTY.'.'.EXT_PNG;
					}

//					echo '<div class="box">
//					<h3><a href="'.setPage($page, 0, $row->MultyID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.stripComments($row->Title).'</a></h3>
//					<a href="'.setPage($page, 0, $row->MultyID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.drawImage($sMainImageFile, 0, 0, $row->Title).'</a>
//					<div class="date">'.formatDate($row->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT).getLabel('strByUser').$row->Author.'</div>
//					</div>'."\n";
					if($page == 168) //day by day
					{
						$content = $oMulty->GetPartText($row->MultyID,1);
						$title = $row->Title;
						$start_pos = strpos($title, '[');
						if($start_pos)
						{
							$event_date = substr($title, strpos($title, '[') + 1, strpos($title, ']') - strpos($title, '[') - 1);
							preg_match('/^\D*(\d{1,2})\D+(\d{1,2})\D+(\d{2,4}).*$/', $event_date, $aMatches);
							
								$year = $aMatches[3];
								$month = $aMatches[2];
								$day = $aMatches[1];
								$timestamp = $year.'-'.$month.'-'.$day;
								$event_date = formatDate($timestamp, FULL_DATE_YEAR_DISPLAY_FORMAT);
						}
						else
						{
							$event_date = formatDate($row->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
						}

		            	echo '
		                	<li>
		                    	<h2><a href="'.setPage($page, 0, $row->MultyID).'" title="'.htmlspecialchars(strip_tags(stripComments($row->Title))).'">'.htmlspecialchars(strip_tags(stripComments($row->Title))).'</a></h2>
		                        <div class="text">
		                        	<div class="preview">
		                            	<a href="'.setPage($page, 0, $row->MultyID).'" title="'.htmlspecialchars(strip_tags(stripComments($row->Title))).'">
		                                	'.drawImage($sMainImageFile, 300, 164, stripComments($row->Title)).'
		                                </a>
		                            </div>
		                        	<span>'.$event_date.getLabel('strByUser') .'</span>
		                            <p>'. $row->Author .'</p>
		                        </div>
		                        '. IIF(!empty($content), '<div class="text alone">'. $content .'</div>', '') .'
		                    </li>
		            	';
					}
					else 
					{
	            	echo '
	                	<li>
	                    	<h2><a href="'.setPage($page, 0, $row->MultyID).'" title="'.htmlspecialchars(strip_tags(stripComments($row->Title))).'">'.htmlspecialchars(strip_tags(stripComments($row->Title))).'</a></h2>
	                        <div class="text">
	                        	<div class="preview">
	                            	<a href="'.setPage($page, 0, $row->MultyID).'" title="'.htmlspecialchars(strip_tags(stripComments($row->Title))).'">
	                                	'.drawImage($sMainImageFile, 300, 164, stripComments($row->Title)).'
	                                </a>
	                            </div>
	                        	<span>'.formatDate($row->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT).getLabel('strByUser') .'</span>
	                            <p>'. $row->Author .'</p>
	                        </div>
	                    </li>
	            	';
					}
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