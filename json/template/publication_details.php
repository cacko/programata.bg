<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	$rPublication = $oPublication->GetByID($item);
	if ($rPublication)
	{
		$nEntityType = $aEntityTypes[ENT_PUBLICATION];

		$sItemTitle = $rPublication->Title;
		$result['name'] = $rPublication->Title;
		$result['id'] = $item;

		// =========================== LINKS ===========================
		$sLink = '';
		$rsLink = $oLink->ListAll($rPublication->PublicationID, $aEntityTypes[ENT_PUBLICATION], array(1,2,3,0));
		if(mysql_num_rows($rsLink))
		{
			$sLink .= '<br />';
			$aLinkTypes = getLabel('aLinkTypes');
			while($rLink = mysql_fetch_object($rsLink))
			{
				if ($rLink->LinkTypeID == 2 || $rLink->LinkTypeID == 3)
					$sLink .= '<a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$aLinkTypes[$rLink->LinkTypeID].'</a><br />';
				else
					$sLink .= getLabel('strUrl').': <a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$rLink->Title.'</a><br />';
			}
		}
		// =========================== IMAGES ===========================
		if($_REQUEST['w'] >= 450) {
			$sMainImageFile = UPLOAD_DIR.IMG_PUBLICATION.$row->EventID.'.'.EXT_IMG;
		}
		else {
			$sMainImageFile = UPLOAD_DIR.IMG_PUBLICATION_MID.$rPublication->PublicationID.'.'.EXT_IMG;
		}
		if (is_file('../'.$sMainImageFile))
		{
			$result['image'] = $sMainImageFile;
		}
//		$result['date'] = formatDate($rPublication->PublicationDate, DEFAULT_DATE_DISPLAY_FORMAT);
//		$result['author'] = $rPublication->Author;
		$result['lead'] = $rPublication->Lead;
		$result['content'] = stripHTMLComments($rPublication->Content);
	}
	else
		$item = 0;
}
?>