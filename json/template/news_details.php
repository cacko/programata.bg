<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	$rNews = $oNews->GetByID($item);
	if ($rNews)
	{
		$nEntityType = $aEntityTypes[ENT_NEWS];

		$sItemTitle = $rNews->Title;

		$result['name'] = $rNews->Title;
		$result['id'] = $item;

		// =========================== IMAGES ===========================
		if($_REQUEST['w'] >= 450) {
			$sMainImageFile = UPLOAD_DIR.IMG_NEWS.$row->EventID.'.'.EXT_IMG;
		}
		else {
			$sMainImageFile = UPLOAD_DIR.IMG_NEWS_MID.$rNews->NewsID.'.'.EXT_IMG;
		}
		if (is_file('../'.$sMainImageFile))
		{
			$result['image'] = $sMainImageFile;
		}

		$result['date'] = formatDate($rNews->NewsDate, DEFAULT_DATE_DISPLAY_FORMAT);
		$result['author'] = $rNews->Author;
		$result['lead'] = stripHTMLComments($rNews->Lead);
		$result['content'] = stripHTMLComments($rNews->Content);
	}
	else
		$item = 0;
}
?>