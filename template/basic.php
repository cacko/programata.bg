<?php
if (!$bInSite) die();
//=========================================================
	$basic_php = array();

    if (!isset($item) || empty($item))
    {
			if(in_array($page,array(2,15,16,17,18,19))){
	            $bIsFirst = true;
	            $rsPage = $oPage->ListAll(2);
	            
	            while($row = mysql_fetch_object($rsPage))
	            {
	                if (!in_array($row->PageID, $aSysNavigation))
	                {
	                    $filtered = $oPage->ListPageCityFiltersAsArray($row->PageID);
	                    if (in_array($city, $filtered) || empty($filtered))
	                    {
	                        $class = '';
	                        if (isset($_names_paeges[$row->PageID]))
	                        {
	                            $class = $_names_paeges[$row->PageID];
	                        }
	                        $about_us_menu[] = writeLink($row->Title, $row->PageID, 0, "class='".$class."'");
	                        $bIsFirst = false;
	                    }
	                }
	            }    	
	    	
	            if(isset($about_us_menu) && is_array($about_us_menu)){
	            	$basic_php['about_us_menu'] = $about_us_menu;
	            }
			}
            
        if ($nRootPage==USERROOT_PAGE || in_array($nRootPage, $aSysNavigation) || (!in_array($nRootPage, $aSysNavigation)))
		{
//            echo '<h2 title="'.htmlspecialchars(strip_tags($rCurrentPage->Title)).'">'.htmlspecialchars($rCurrentPage->Title).'</h2>'."\n";
			  $all_page = $oPage->ListAllAsArraySimple();
	
			  if($rCurrentPage->ParentPageID > 1){
			  	$set_big_title = htmlspecialchars($all_page[$rCurrentPage->ParentPageID]);
			  }else{
			  	$set_big_title = htmlspecialchars($rCurrentPage->Title);
			  }
			  
			  $basic_php['title'] = '<h2 class="bigger">'. $set_big_title .'</h2>';
        }
        
        if ((!isset($item) || empty($item)) && !empty($rCurrentPage->Description))
        {
//            echo '<div class="text">'.$rCurrentPage->Description.'<br /></div>'."\n";
			  $basic_php['desc'] = '<div class="text">'. $rCurrentPage->Description .'</div>';
        }
        /*$sGallery = showGallery($page, ENT_PAGE, true, false);
        if (!empty($sGallery))
            echo '<h4>'.getLabel('strGallery').'</h4>'.$sGallery."\n";*/
        /**
         * ACCENT
         */
//	    $dToday = date(DEFAULT_DATE_DB_FORMAT);
//		$dStartDate = $dToday;
//		$dEndDate = $dToday;

		$aPromotionTypesFull = getLabel('aPromotionTypesFull');
		$aPromotionTypes = $aPromotionTypesFull[$nRootPage];
	
		$aPages = $oPage->ListAllAsArraySimple($page);

		$records = $oPromotion->ListAll($page, $city, 1, date(DEFAULT_DATE_DB_FORMAT), date(DEFAULT_DATE_DB_FORMAT));   
			while($row = mysql_fetch_object($records))
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
						$text = $oMulty->GetPartText($rItem->MultyID, 1, $lang);
						$sEntityText = IIF(!empty($text), strShorten($text, TEXT_LEN_PUBLIC), strShorten($text, TEXT_LEN_PUBLIC));
						$sEntityTextBig = IIF(!empty($text), strShorten($text, TEXT_LEN_PUBLIC), strShorten($text, TEXT_LEN_PUBLIC));
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
        
//	        $basic_php['accent'] = '
//	        	<div class="box accent">
//					<a class="accent" href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMainImageFile, 0, 0, $sEntityTitle).'</a>
//					<h3><a class="accent" href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h3>
//					'.IIF(!empty($sEntityTextBig), '<div class="moretext" style="display:none;"><div class="date"><a href="'.setPage($nPageToGo).'" title="'.$aPages[$nPageToGo].'">'.$aPages[$nPageToGo].'</a></div>'.$sEntityTextBig.'</div>', '').'
//				</div>';
	        $basic_php['desc'] = '<div class="text">'. $sEntityText .'</div>';
	        $basic_php['accent'] = '<div class="top">
						                <a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'">'.drawImage($sMainImageFile, 470, 0, $sEntityTitle).'</a>
						                <h3><a href="'.setPage($nPageToGo, 0, $row->EntityID).'"><span id="set_image_'.$page.'" title="'. htmlspecialchars($rCurrentPage->Title) .'">'. htmlspecialchars($rCurrentPage->Title) .'</span>: '. $sEntityTitle .'</a><div class="clear"></div></h3>
						                '. $basic_php['desc'] .'
					            	</div>';
		}
    }else{
			  $all_page = $oPage->ListAllAsArraySimple();
	
			  if($rCurrentPage->ParentPageID > 1){
			  	$set_big_title = htmlspecialchars($all_page[$rCurrentPage->ParentPageID]);
			  }else{
			  	$set_big_title = htmlspecialchars($rCurrentPage->Title);
			  }
			  
			  $basic_php['title'] = '<h2 class="bigger">'. $set_big_title .'</h2>';    	
    }
?>
