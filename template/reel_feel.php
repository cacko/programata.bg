<?php
if (!$bInSite) die();
//=========================================================
	$dToday = date(DEFAULT_DATE_DB_FORMAT);
	$dStartDate = $dToday;
	$dEndDate = $dToday;
	$aPromotionTypesFull = getLabel('aPromotionTypesFull');
	$aPromotionTypes = $aPromotionTypesFull[$nRootPage];

	$aPages = $oPage->ListAllAsArraySimple($page);

	$rsPromotion = $oPromotion->ListAll(24, $city, null, $dStartDate, $dEndDate); // 24 is for Music
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
				break;
			case $aEntityTypes[ENT_PUBLICATION]:
				$rItem = $oPublication->GetByID($row->EntityID); //'<strong>'.$rItem->Subtitle.'</strong><br />'.
				$sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC));
				$sEntityTextBig = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC*3));
				$aRelPages = $oPublication->ListPublicationPagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_PUBLICATION.$row->EntityID.'.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_PUBLICATION_MID.$row->EntityID.'.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);
				break;
			case $aEntityTypes[ENT_FESTIVAL]:
				$rItem = $oFestival->GetByID($row->EntityID);
				$sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC));
				$sEntityTextBig = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC*3));
				$aRelPages = $oFestival->ListFestivalPagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_FESTIVAL.$row->EntityID.'.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_FESTIVAL_MID.$row->EntityID.'.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);
				break;
			case $aEntityTypes[ENT_PLACE]:
				$rItem = $oPlace->GetByID($row->EntityID);
				$sEntityText = IIF(!empty($rItem->Description), strShorten($rItem->Description, TEXT_LEN_PUBLIC), '');
				$sEntityTextBig = IIF(!empty($rItem->Description), strShorten($rItem->Description, TEXT_LEN_PUBLIC*3), '');
				$aRelPages = $oPlace->ListPlacePagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_PLACE.$row->EntityID.'.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_PLACE_MID.$row->EntityID.'.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);
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
				break;
			case $aEntityTypes[ENT_URBAN]:
				$rItem = $oUrban->GetByID($row->EntityID);
//				$sEntityText = IIF(!empty($rItem->MainTitle), strShorten($rItem->MainTitle, TEXT_LEN_PUBLIC), "");
				$sEntityText = ""; //the same as title, so this one is skipped
				$sEntityTextBig = IIF(!empty($rItem->MainTitle), strShorten($rItem->MainTitle, TEXT_LEN_PUBLIC), "");
				$aRelPages = $oUrban->ListUrbanPagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_URBAN.$row->EntityID.'/'.IMG_MID.'1_1.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_URBAN.$row->EntityID.'/'.IMG_MID.'1_1.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->MainTitle);
				break;
			case $aEntityTypes[ENT_MULTY]:
				$rItem = $oMulty->GetByID($row->EntityID);
				$sEntityText = "";
				$sEntityTextBig = IIF(!empty($rItem->Title), strShorten($rItem->Title, TEXT_LEN_PUBLIC), "");
				$aRelPages = $oMulty->ListMultyPagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_MULTY.$row->EntityID.'/'.IMG_MID.'1.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_MULTY.$row->EntityID.'/'.IMG_MID.'1.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);
				break;
			case $aEntityTypes[ENT_EXTRA]:
				$rItem = $oExtra->GetByID($row->EntityID);
				$sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), "");
				$sEntityTextBig = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), "");
				$aRelPages = $oExtra->ListExtraPagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_EXTRA.$row->EntityID.'.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_EXTRA_MID.$row->EntityID.'.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);
				break;
		}
		//$sMainImageFile = UPLOAD_DIR.IMG_PROMOTION.$row->PromotionID.'.'.EXT_IMG;
		if (!is_file($sMainImageFile))
		{
			$sMainImageFile = UPLOAD_DIR.IMG_EMPTY.'.'.EXT_PNG;
		}
		$nPageToGo = $nRootPage;
		if (is_array($aRelPages) && count($aRelPages)>0)
			$nPageToGo = $aRelPages[0];

		// big promo
		if ($row->PromotionTypeID == PRM_ACCENT)
		{
			$aAccent[] = '<div class="box accent">
				<a class="accent" href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMainImageFile, 0, 0, $sEntityTitle).'</a>
				<h3><a class="accent" href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h3>
				'.IIF(!empty($sEntityTextBig), '<div class="moretext" style="display:none;"><div class="date"><a href="'.setPage($nPageToGo).'" title="'.$aPages[$nPageToGo].'">'.$aPages[$nPageToGo].'</a></div>'.$sEntityTextBig.'</div>', '').'
				</div>'."\n";
		}
		// small promo
	}
	//if (!empty($sAccent))
	if (is_array($aAccent) && count($aAccent) > 0)
	{
		shuffle($aAccent);
		echo '<h2 title="'.$aPromotionTypes[PRM_ACCENT].'">'.$aPromotionTypes[PRM_ACCENT].'</h2>'.$aAccent[0]."\n";
		?>
	<script type="text/javascript">
	<!--
	jQuery(document).ready(function(){
		jQuery("a.accent").mouseover(function() {
			jQuery(this).parents("div.box").find(".moretext").slideDown("slow");
			return false;
		})
	})
	//-->
	</script>
	<?
}
?>