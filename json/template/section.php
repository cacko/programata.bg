<?php
if (!$bInSite) die();
//=========================================================
global $aPromotionTypes;

if (isset($item) && !empty($item))
{
	include('template/event_details.php');
}
if (!isset($item) || empty($item))
{
	$dToday = date(DEFAULT_DATE_DB_FORMAT);
	$dStartDate = $dToday;
	$dEndDate = $dToday;
	$aPromotionTypesFull = getLabel('aPromotionTypesFull');
	$aPromotionTypes = $aPromotionTypesFull[$nRootPage];

	$aPages = $oPage->ListAllAsArraySimple($page);

	$rsPromotion = $oPromotion->ListAll($page, $city, null, $dStartDate, $dEndDate);
	$aPromoEntityTypes = getLabel('aPromoEntityTypes');
	//$sAccent = '';
	$aAccent = array();
	$sPromotions = $sPromoListLeft = $sPromoListRight = $sPromoNews = '';
	while($row = mysql_fetch_object($rsPromotion))
	{
		$rItem = null;
		$sEntityType = $aPromoEntityTypes[$row->EntityTypeID];
		$sMainImageFile = $sEntityText = $sEntityTextBig = '';
		$aRelPages = null;
		$next = false;
		switch($row->EntityTypeID)
		{
			case $aEntityTypes[ENT_NEWS]:
				$rItem = $oNews->GetByID($row->EntityID);
				$sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC));
				$sEntityTextBig = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC*3));
				$aRelPages = $oNews->ListNewsPagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_NEWS.$row->EntityID.'.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_NEWS_MID.$row->EntityID.'.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);
				$type = 'news';
				break;
			case $aEntityTypes[ENT_PUBLICATION]:
				$rItem = $oPublication->GetByID($row->EntityID); //'<strong>'.$rItem->Subtitle.'</strong><br />'.
				$sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC));
				$sEntityTextBig = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC*3));
				$aRelPages = $oPublication->ListPublicationPagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_PUBLICATION.$row->EntityID.'.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_PUBLICATION_MID.$row->EntityID.'.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);
				$type = 'publication';
				break;
			case $aEntityTypes[ENT_FESTIVAL]:
				$rItem = $oFestival->GetByID($row->EntityID);
				$sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC));
				$sEntityTextBig = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC*3));
				$aRelPages = $oFestival->ListFestivalPagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_FESTIVAL.$row->EntityID.'.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_FESTIVAL_MID.$row->EntityID.'.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);
				$type = 'festival';
				break;
			case $aEntityTypes[ENT_PLACE]:
				$rItem = $oPlace->GetByID($row->EntityID);
				$sEntityText = IIF(!empty($rItem->Description), strShorten($rItem->Description, TEXT_LEN_PUBLIC), '');
				$sEntityTextBig = IIF(!empty($rItem->Description), strShorten($rItem->Description, TEXT_LEN_PUBLIC*3), '');
				$aRelPages = $oPlace->ListPlacePagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_PLACE.$row->EntityID.'.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_PLACE_MID.$row->EntityID.'.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);
				$type = 'place';
				break;
			case $aEntityTypes[ENT_EVENT]:
				$rItem = $oEvent->GetByID($row->EntityID);
				$sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Description, TEXT_LEN_PUBLIC));
				$sEntityTextBig = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Description, TEXT_LEN_PUBLIC*3));
				// list program pages
				$dEndDate = increaseDate($dToday, 0, 1);
				$rsProgram = $oProgram->ListAllByDate(null, $row->EntityID, null, null, increaseDate($dStartDate, -THIS_WEEK_DAYS), increaseDate($dEndDate, THIS_WEEK_DAYS));
				$nProgramID = 0;
				while($rPro = mysql_fetch_object($rsProgram))
				{
					$nProgramID = $rPro->MainProgramID;
					continue;
				}
				$aRelPages = $oProgram->ListProgramPagesAsArray($nProgramID);
				$sMainImageFile = UPLOAD_DIR.IMG_EVENT.$row->EntityID.'.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_EVENT_MID.$row->EntityID.'.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);
				$type = 'event';
				break;
			case $aEntityTypes[ENT_URBAN]:
				$next = true;
				$rItem = $oUrban->GetByID($row->EntityID);
//				$sEntityText = IIF(!empty($rItem->MainTitle), strShorten($rItem->MainTitle, TEXT_LEN_PUBLIC), "");
				$sEntityText = ""; //the same as title, so this one is skipped
				$sEntityTextBig = IIF(!empty($rItem->MainTitle), strShorten($rItem->MainTitle, TEXT_LEN_PUBLIC), "");
				$aRelPages = $oUrban->ListUrbanPagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_URBAN.$row->EntityID.'/'.IMG_MID.'1_1.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_URBAN.$row->EntityID.'/'.IMG_MID.'1_1.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->MainTitle);
				$type = 'urban';
				break;
			case $aEntityTypes[ENT_MULTY]:
				$next = true;
				$rItem = $oMulty->GetByID($row->EntityID);
				$sEntityText = "";
				$sEntityTextBig = IIF(!empty($rItem->Title), strShorten($rItem->Title, TEXT_LEN_PUBLIC), "");
				$aRelPages = $oMulty->ListMultyPagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_MULTY.$row->EntityID.'/'.IMG_MID.'1.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_MULTY.$row->EntityID.'/'.IMG_MID.'1.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);
				$type = 'multy';
				break;
			case $aEntityTypes[ENT_EXTRA]:
				$next = true;
				$rItem = $oExtra->GetByID($row->EntityID);
				$sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), "");
				$sEntityTextBig = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), "");
				$aRelPages = $oExtra->ListExtraPagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_EXTRA.$row->EntityID.'.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_EXTRA_MID.$row->EntityID.'.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);
				$type = 'extra';
				break;
		}
		if($next) continue;
		//$sMainImageFile = UPLOAD_DIR.IMG_PROMOTION.$row->PromotionID.'.'.EXT_IMG;
		$nPageToGo = $nRootPage;
		if (is_array($aRelPages) && count($aRelPages)>0)
			$nPageToGo = $aRelPages[0];

		// big promo
		if ($row->PromotionTypeID == PRM_ACCENT)
		{
			$accent=array();
			$accent['id'] = $row->EntityID;
			if($_REQUEST['w'] >= 450) {
				$imageFile = $sMainImageFile;
			}
			else {
				$imageFile = $sMidImageFile;
			}
			if (is_file('../'.$imageFile))
			{
				$accent['image'] = $imageFile;
			}

			$accent['title'] = $sEntityTitle;
			$accent['description'] = $sEntityText;
			$accent['type'] = $type;

			$category['id'] = $nPageToGo;
			$category['name'] = $oPage->GetByID($nPageToGo)->Title;
			$sTitleColor = $aMobTitleColors[0];
			if ($page != DEF_PAGE && $page != USERROOT_PAGE)
			{
				$nColorIdx = $page - 20;
				if (in_array($nColorIdx, array_keys($aMobTitleColors)))
					$sTitleColor = $aMobTitleColors[$nColorIdx];
				else
					$sTitleColor = $aMobTitleColors[0];
			}
			$category['color']  = $sTitleColor;

			$accent['category'] = $category;

			$included = false;
			foreach($result['accents'] as $elem)
			{
				if($elem['id'] == $accent['id'] && $elem['type'] == $type)
					$included = true;
			}
			if(!$included)
				array_push($result['accents'], $accent);
		}
	}
}
?>