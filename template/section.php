
<?php
if (!$bInSite) die();
//=========================================================
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
	//d($rsPromotion);
	$aPromoEntityTypes = getLabel('aPromoEntityTypes');
	//$sAccent = '';
	$aAccent = array();
	$sPromotions = $sPromoListLeft = $sPromoListRight = $sPromoNews = $sPromoInterviews = '';

	//adding reel_feel news 
	if($page == 24 && $lang == '1') { //music list.
        $requestAddress = "http://reelfeel.bg/public/xml/programata-rss.php?lang=bg"; 
		$xml = simplexml_load_file($requestAddress);
		
//		$sPromotions .= '
//			<h5 class="showit"><a href="'.$xml->link.'" target="_blank">'.$xml->title.'</a></h5>
//			<div class="box box-on">
//				<a href="'.$xml->link.'" target="_blank"><img src="'.$xml->image.'" width="300" title="'.$xml->title.'" alt="'.$xml->title.'"></a>
//				<h3><a href="'.$xml->link.'" target="_blank">'.$xml->title.'</a></h3>
//				<div class="date"><a href="http://www.reelfeel.bg/"  target="_blank" title="www.reelfeel.bg">www.reelfeel.bg</a></div>
//				<div>'.$xml->description.'</div>
//			</div>'."\n";
		
		$increment_list_1 = true;

        $sPromotions .= '
                	<li>
                    	<a href="'. $xml->link.'" class="tab" style="display: none;" target="_blank"><span>'.$xml->title .'</span></a>
                        
                    	<div class="description">
                    		<h4><a href="'. $xml->link.'" title="'.$xml->title.'" target="_blank"><span>'.$xml->title.'</span></a></h4>
                    		<a href="'. $xml->link .'" title="'.$xml->title.'" target="_blank"><img src="'.$xml->image.'" width="300" title="'.$xml->title.'" alt="'.$xml->title.'"></a>
                        	<div class="text">'.strShorten($xml->description, TEXT_LEN_PUBLIC).'</div>
                    	</div>
                    </li>
        	';				
	}

	$list_1 = $list_2 = $list_3 = $list_4 = $list_5 = $list_6 = $list_7 = $list_8 = $list_9 = 0;
	
	if(isset($increment_list_1) && $increment_list_1){
		$list_1 = 2;
	}
	
	while($row = mysql_fetch_object($rsPromotion))
	{
#		d($row);
		$rItem = null;
		$sEntityType = $aPromoEntityTypes[$row->EntityTypeID];
		$sMainImageFile = $sEntityText = $sEntityTextBig = '';
		$aRelPages = null;
		switch($row->EntityTypeID)
		{
			case $aEntityTypes[ENT_NEWS]:
				$rItem = $oNews->GetByID($row->EntityID);
				$sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC));
				$sEntityTextBig = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC*2));
				$aRelPages = $oNews->ListNewsPagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_NEWS.$row->EntityID.'.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_NEWS_MID.$row->EntityID.'.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);
				break;
			case $aEntityTypes[ENT_PUBLICATION]:
				$rItem = $oPublication->GetByID($row->EntityID); //'<strong>'.$rItem->Subtitle.'</strong><br />'.
				$sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC));
				$sEntityTextBig = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC*2));
				$aRelPages = $oPublication->ListPublicationPagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_PUBLICATION.$row->EntityID.'.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_PUBLICATION_MID.$row->EntityID.'.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);
				break;
			case $aEntityTypes[ENT_FESTIVAL]:
				$rItem = $oFestival->GetByID($row->EntityID);
				$sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC));
				$sEntityTextBig = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC*2));
				$aRelPages = $oFestival->ListFestivalPagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_FESTIVAL.$row->EntityID.'.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_FESTIVAL_MID.$row->EntityID.'.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);
				break;
			case $aEntityTypes[ENT_PLACE]:
				$rItem = $oPlace->GetByID($row->EntityID);
				$sEntityText = IIF(!empty($rItem->Description), strShorten($rItem->Description, TEXT_LEN_PUBLIC), '');
				$sEntityTextBig = IIF(!empty($rItem->Description), strShorten($rItem->Description, TEXT_LEN_PUBLIC), '');
				$aRelPages = $oPlace->ListPlacePagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_PLACE.$row->EntityID.'.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_PLACE_MID.$row->EntityID.'.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);
				break;
			case $aEntityTypes[ENT_EVENT]:
				$rItem = $oEvent->GetByID($row->EntityID);
				$sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Description, TEXT_LEN_PUBLIC));
				$sEntityTextBig = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Description, TEXT_LEN_PUBLIC*2));
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
				$text = $oMulty->GetPartText($rItem->MultyID, 1, $lang);
				$sEntityText = IIF(!empty($text), strShorten($text, TEXT_LEN_PUBLIC), "");
				$sEntityTextBig = IIF(!empty($text), strShorten($text, TEXT_LEN_PUBLIC*2), "");
				$aRelPages = $oMulty->ListMultyPagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_MULTY.$row->EntityID.'/'.IMG_MID.'1.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_MULTY.$row->EntityID.'/'.IMG_MID.'1.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);
				break;
			case $aEntityTypes[ENT_EXTRA]:
				$rItem = $oExtra->GetByID($row->EntityID);
				$sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), "");
				$sEntityTextBig = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC*2), "");
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
		
		#d(PRM_ACCENT);
		if ($row->PromotionTypeID == PRM_ACCENT)
		{
//			$aAccent[] = '<div class="box accent">
//				<a class="accent" href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMainImageFile, 0, 0, $sEntityTitle).'</a>
//				<h3><a class="accent" href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h3>
//				'.IIF(!empty($sEntityTextBig), '<div class="moretext" style="display:none;"><div class="date"><a href="'.setPage($nPageToGo).'" title="'.$aPages[$nPageToGo].'">'.$aPages[$nPageToGo].'</a></div>'.$sEntityTextBig.'</div>', '').'
//				</div>'."\n";
		}
		// small promo
		elseif ($row->PromotionTypeID == PRM_PREMIERE)
		{
//			$sPromotions .= '
//				<h5 class="showit"><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h5>
//				<div class="box box-on">
//					<a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMidImageFile, 0, 0, $sEntityTitle).'</a>
//					<h3><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h3>
//					<div class="date"><a href="'.setPage($nPageToGo).'" title="'.$aPages[$nPageToGo].'">'.$aPages[$nPageToGo].'</a></div>
//					<div>'.$sEntityText.'</div>
//				</div>'."\n";
			++$list_1;
            $sPromotions .= '
                    	<li>
                        	<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" class="tab" '. ($list_1 == 1 ? 'style="display: none;"' : '') .'><span>'.$sEntityTitle .'</span></a>

                        	<div class="description" '. ($list_1 > 1 ? 'style="display: none;"' : '') .'>
                        		<h4><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'"><span>'.$sEntityTitle.'</span></a></h4>
                        		<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'">'.drawImage($sMidImageFile, 300, 164, $sEntityTitle).'</a>
                            	<div class="text">'.$sEntityText.'</div>
                        	</div>
                        </li>
            	';						
		}
		// news list
		/*elseif ($row->PromotionTypeID == PRM_NEWS)
		{
			$aRelPages = $oNews->ListNewsPagesAsArray($row->NewsID);
			$nPageToGo = $aRelPages[0];
			if (empty($sPromoNews))
			    $sPromoNews .= '
				<div class="box">
				    <a href="'.setPage($nPageToGo, 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($sEntityTitle)).'">'.drawImage($sMainImageFile, 0, 0, $sEntityTitle).'</a>
				    <h3><a href="'.setPage($nPageToGo, 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($sEntityTitle)).'">'.$sEntityTitle.'</a></h3>
				    <div class="date">'.formatDate($row->NewsDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</div>
				    <div>'.$sEntityTextBig.'</div>
				</div>'."\n";
			else
			    $sPromoNews .= '
				<div class="box">
				    <h6><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h6>
				    <div class="date">'.formatDate($row->NewsDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</div>
				</div>'."\n";
		}*/
		// promo list left
		elseif ($row->PromotionTypeID == PRM_LEFTLIST)
		{
			if ($page == 26) // zavedenia - show as news
			{
				
				++$list_8;
				if (empty($sPromoNews)){
//					$sPromoNews .= '
//					    <div class="box">
//						<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.htmlspecialchars(strip_tags($sEntityTitle)).'">'.drawImage($sMainImageFile, 0, 0, $sEntityTitle).'</a>
//						<h3><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.htmlspecialchars(strip_tags($sEntityTitle)).'">'.$sEntityTitle.'</a></h3>
//						<div class="date"><a href="'.setPage($nPageToGo).'" title="'.$aPages[$nPageToGo].'">'.$aPages[$nPageToGo].'</a></div>
//						<a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMidImageFile, 0, 0, $sEntityTitle).'</a>
//						<div>'.$sEntityText.'</div>
//					    </div>'."\n";
				
					$sPromoNews .= '
	                    	<li>
	                        	<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" class="tab" '. ($list_8 == 1 ? 'style="display: none;"' : '') .'><span>'.$sEntityTitle.'</span></a>
	                            
	                        	<div class="description" '. ($list_8> 1 ? 'style="display: none;"' : '') .'>
	                        		<h4><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'"><span>'.$sEntityTitle.'</span></a></h4>
	                        		<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'">'.drawImage($sMidImageFile, 300, 164, $sEntityTitle).'</a>
	                            	<div class="text">'.$sEntityText.'</div>
	                        	</div>
	                        </li>';	
				}else{
//					$sPromoNews .= '
//					    <div class="box">
//						<h6><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h6>
//						<div class="date"><a href="'.setPage($nPageToGo).'" title="'.$aPages[$nPageToGo].'">'.$aPages[$nPageToGo].'</a></div>
//						<a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMidImageFile, 0, 0, $sEntityTitle).'</a>
//						<div>'.$sEntityText.'</div>
//					    </div>'."\n";
				
					$sPromoNews .= '
	                    	<li>
	                        	<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" class="tab" '. ($list_8 == 1 ? 'style="display: none;"' : '') .'><span>'.$sEntityTitle.'</span></a>
	                            
	                        	<div class="description" '. ($list_8> 1 ? 'style="display: none;"' : '') .'>
	                        		<h4><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'"><span>'.$sEntityTitle.'</span></a></h4>
	                        		<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'">'.drawImage($sMidImageFile, 300, 164, $sEntityTitle).'</a>
	                            	<div class="text">'.$sEntityText.'</div>
	                        	</div>
	                        </li>';	
				}				
			}
			else if ($page == 135 || $page == 167) // mixer - show with picture
			{
				++$list_9;
				if (empty($sPromoListLeft)){
//					$sPromoListLeft .= '
//					    <div class="box">
//						<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.htmlspecialchars(strip_tags($sEntityTitle)).'">'.drawImage($sMidImageFile, 0, 0, $sEntityTitle).'</a>
//						<h3><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.htmlspecialchars(strip_tags($sEntityTitle)).'">'.$sEntityTitle.'</a></h3>
//						<div class="date"><a href="'.setPage($nPageToGo).'" title="'.$aPages[$nPageToGo].'">'.$aPages[$nPageToGo].'</a></div>
//						<div>'.$sEntityText.'</div>
//					    </div>'."\n";
				
					$sPromoListLeft .= '
	                    	<li>
	                        	<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" class="tab" '. ($list_9 == 1 ? 'style="display: none;"' : '') .'><span>'.$sEntityTitle.'</span></a>
	                            
	                        	<div class="description" '. ($list_9> 1 ? 'style="display: none;"' : '') .'>
	                        		<h4><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'"><span>'.$sEntityTitle.'</span></a></h4>
	                        		<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'">'.drawImage($sMidImageFile,300, 164,$sEntityTitle).'</a>
	                            	<div class="text">'.$sEntityTextBig.'</div>
	                        	</div>
	                        </li>					
					';
				}else{
//					$sPromoListLeft .= '
//					    <div class="box">
//						<h6><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h6>
//						<div class="date"><a href="'.setPage($nPageToGo).'" title="'.$aPages[$nPageToGo].'">'.$aPages[$nPageToGo].'</a></div>
//						<a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMidImageFile, 0, 0, $sEntityTitle).'</a>
//						<div>'.$sEntityText.'</div>
//					    </div>'."\n";

					$sPromoListLeft .= '
	                    	<li>
	                        	<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" class="tab" '. ($list_9 == 1 ? 'style="display: none;"' : '') .'><span>'.$sEntityTitle.'</span></a>
	                            
	                        	<div class="description" '. ($list_9> 1 ? 'style="display: none;"' : '') .'>
	                        		<h4><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'"><span>'.$sEntityTitle.'</span></a></h4>
	                        		<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'">'.drawImage($sMidImageFile, 300, 164,$sEntityTitle).'</a>
	                            	<div class="text">'.$sEntityTextBig.'</div>
	                        	</div>
	                        </li>					
					';					
				}
			}
			else{
//				$sPromoListLeft .= '
//					<div class="box">
//						<h6><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h6>
//						<a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMidImageFile, 0, 0, $sEntityTitle).'</a>
//						<div>'.$sEntityText.'</div>
//					</div>'."\n";
//				
			++$list_2;
	            $sPromoListLeft .= '
	                    	<li>
	                        	<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" class="tab" '. ($list_2 == 1 ? 'style="display: none;"' : '') .'><span>'.$sEntityTitle.'</span></a>
	                            
	                        	<div class="description" '. ($list_2 > 1 ? 'style="display: none;"' : '') .'>
	                        		                        		<h4><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'"><span>'.$sEntityTitle.'</span></a></h4>
	                        		<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'">'.drawImage($sMidImageFile, 300, 164, $sEntityTitle).'</a>
	                            	<div class="text">'.$sEntityTextBig.'</div>
	                        	</div>
	                        </li>
	            	';							
			}
		}
		elseif ($row->PromotionTypeID == PRM_EXTRA)
		{
			if($page == 135 || $page == 167) //mixer - show as news
			{
				++$list_5;
				if (empty($sPromoNews)){
//					$sPromoNews .= '
//					    <div class="box">
//						<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.htmlspecialchars(strip_tags($sEntityTitle)).'">'.drawImage($sMainImageFile, 0, 0, $sEntityTitle).'</a>
//						<h3><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.htmlspecialchars(strip_tags($sEntityTitle)).'">'.$sEntityTitle.'</a></h3>
//						<div class="date"><a href="'.setPage($nPageToGo).'" title="'.$aPages[$nPageToGo].'">'.$aPages[$nPageToGo].'</a></div>
//						<div>'.$sEntityText.'</div>
//					    </div>'."\n";
				#++$list_5;
					$sPromoNews .= '
	                    	<li>
	                        	<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" class="tab" '. ($list_5 == 1 ? 'style="display: none;"' : '') .'><span>'.$sEntityTitle.'</span></a>
	                            
	                        	<div class="description" '. ($list_5> 1 ? 'style="display: none;"' : '') .'>
	                        		                        		<h4><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'"><span>'.$sEntityTitle.'</span></a></h4>
	                        		<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'">'.drawImage($sMidImageFile, 300, 164, $sEntityTitle).'</a>
	                            	<div class="text">'.$sEntityTextBig.'</div>
	                        	</div>
	                        </li>';
				}else{
//					$sPromoNews .= '
//					    <div class="box">
//						<h6><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h6>
//						<div class="date"><a href="'.setPage($nPageToGo).'" title="'.$aPages[$nPageToGo].'">'.$aPages[$nPageToGo].'</a></div>
//						<a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMidImageFile, 0, 0, $sEntityTitle).'</a>
//						<div>'.$sEntityText.'</div>
//					    </div>'."\n";
				#++$list_6;
					$sPromoNews .= '
	                    	<li>
	                        	<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" class="tab" '. ($list_5 == 1 ? 'style="display: none;"' : '') .'><span>'.$sEntityTitle.'</span></a>
	                            
	                        	<div class="description" '. ($list_5> 1 ? 'style="display: none;"' : '') .'>
	                        		                        		<h4><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'"><span>'.$sEntityTitle.'</span></a></h4>
	                        		<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'">'.drawImage($sMidImageFile, 300, 164, $sEntityTitle).'</a>
	                            	<div class="text">'.$sEntityText.'</div>
	                        	</div>
	                        </li>';
				}
			}
			else
				$sPromoListLeft .= '
					<div class="box">
						<h6><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h6>
						<a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMidImageFile, 300, 164, $sEntityTitle).'</a>
						<div>'.$sEntityTextBig.'</div>
					</div>'."\n";
		}
		// promo list right
		elseif ($row->PromotionTypeID == PRM_RIGHTLIST)
		{
			
			++$list_3;
	            $sPromoListRight .= '
	                    	<li>
	                        	<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" class="tab" '. ($list_3 == 1 ? 'style="display: none;"' : '') .'><span>'.$sEntityTitle.'</span></a>
	                            
	                        	<div class="description" '. ($list_3 > 1 ? 'style="display: none;"' : '') .'>
	                        		                        		<h4><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'"><span>'.$sEntityTitle.'</span></a></h4>
	                        		<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'">'.drawImage($sMidImageFile, 300, 164, $sEntityTitle).'</a>
	                            	<div class="text">'.$sEntityTextBig.'</div>
	                        	</div>
	                        </li>
	            	';		
	            			
//			$sPromoListRight .= '
//				<div class="box">
//					<h6><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h6>
//					<a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMidImageFile, 0, 0, $sEntityTitle).'</a>
//					<div>'.$sEntityText.'</div>
//				</div>'."\n";
		}
		elseif ($row->PromotionTypeID == PRM_INTERVIEW)
		{
//			$sPromoInterviews .= '
//				<h5><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h5>
//				<div class="box box-on">
//					<a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMidImageFile, 0, 0, $sEntityTitle).'</a>
//					<h3><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h3>
//					<div class="date"><a href="'.setPage($nPageToGo).'" title="'.$aPages[$nPageToGo].'">'.$aPages[$nPageToGo].'</a></div>
//					<div>'.$sEntityText.'</div>
//				</div>'."\n";
			
				++$list_4;
	            $sPromoInterviews .= '
	                    	<li>
	                        	<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" class="tab" '. ($list_4 == 1 ? 'style="display: none;"' : '') .'><span>'.$sEntityTitle.'</span></a>
	                            
	                        	<div class="description" '. ($list_4> 1 ? 'style="display: none;"' : '') .'>
	                        		                        		<h4><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'"><span>'.$sEntityTitle.'</span></a></h4>
	                        		<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'">'.drawImage($sMidImageFile, 300, 164, $sEntityTitle).'</a>
	                            	<div class="text">'.$sEntityTextBig.'</div>
	                        	</div>
	                        </li>
	            	';				
		}
		
	}
	
	
	
	echo '<div id="home">';
	
	//if (!empty($sAccent))
	if (is_array($aAccent) && count($aAccent) > 0)
	{
		shuffle($aAccent);
		echo '<h2 title="'.$aPromotionTypes[PRM_ACCENT].'">'. mb_strtolower($aPromotionTypes[PRM_ACCENT], 'utf8') .'</h2>'.$aAccent[0]."\n";
		?>
<!--		<script type="text/javascript">
		<!--
		jQuery(document).ready(function(){
			jQuery("a.accent").mouseover(function() {
				jQuery(this).parents("div.box").find(".moretext").slideDown("slow");
				return false;
			})
		})
		//-->
		<!--</script>-->
		<?
	}

	if($page == 167) //my city
	{
		$sPromotions = '';

		            $tdy = '['.date('d.m.Y').']';

		            $todayMulty = $oMulty->ListAll(null, $tdy, null, null, false, null, null);

		            if (mysql_num_rows($todayMulty))
		            {
		                 while($row = mysql_fetch_object($todayMulty))
		                    {           

		                    	$content = $oMulty->GetPartText($row->MultyID,1);

			                    $sMainImageFile = UPLOAD_DIR.IMG_MULTY.$row->MultyID.'/'.IMG_MID.'1.'.EXT_IMG;
				                    if (!is_file($sMainImageFile))
				                    {
				                            $sMainImageFile = UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
				                    }

			                    $aRelPages = $oMulty->ListMultyPagesAsArray($row->MultyID);
			                    $nPageToGo = $aRelPages[0];
			                    $nSectionToGo = $oPage->GetRootPageID($nPageToGo);
				            	$sPromotions .= '
			                    	<li>
			                        	<div class="description" >
			                        		<h4><a href="'.setPage($nPageToGo, 0, $row->MultyID).'" title="'.htmlspecialchars(strip_tags(stripComments($row->Title))).'"><span>'.htmlspecialchars(strip_tags(stripComments($row->Title))).'</span></a></h4>
			                        		<a href="'.setPage($nPageToGo, 0, $row->MultyID).'" title="'.htmlspecialchars(strip_tags(stripComments($row->Title))).'">'.drawImage($sMainImageFile, 300, 164, htmlspecialchars(strip_tags(stripComments($row->Title)))).'</a>
			                            	<div class="text">'.strShorten($content, TEXT_LEN_PUBLIC).'</div>
			                        	</div>
			                        </li>
				            	';

		                    }
		            }
	}

	if (!empty($sPromotions))
	{
//		echo '<div id="promo">
//			<h4 title="'.$aPromotionTypes[PRM_PREMIERE].'">'.$aPromotionTypes[PRM_PREMIERE].'</h4>';
//		
			echo '<div class="box">
	                    <h3 class="h3">'.mb_strtolower($aPromotionTypes[PRM_PREMIERE], 'utf8') .'</h3>            
	                    <ul class="info-tabs">'. $sPromotions.'</ul></div>'."\n";
		?>
		<script type="text/javascript">
		<!--
//		jQuery(document).ready(function(){
//			jQuery(".showit:first").hide();
//			//$("div.bb:first").addClass("box-on");
//			jQuery("div.box-on:not(:first)").hide();
//			jQuery(".showit a").mouseover(function(){
//				jQuery("div.box-on:visible").hide();//slideUp("slow");
//				jQuery(this).parent().next().show();//slideDown("slow");
//				//$(this).parent().next().addClass("box-on");
//				jQuery(".showit").show();
//				jQuery(this).parent().hide();
//				return false;
//			});
//		})
		//-->
		</script>
		
		<?
	}
	
	if (!empty($sPromoListLeft))
	{
//		echo '<div class="promoleft">
//			<h4 title="'.$aPromotionTypes[PRM_LEFTLIST].'">'.$aPromotionTypes[PRM_LEFTLIST].'</h4>
//			'.$sPromoListLeft.'</div>'."\n";
		
			echo '<div class="box">
	                    <h3 class="h3">'.mb_strtolower($aPromotionTypes[PRM_LEFTLIST], 'utf8').'</h3>            
	                    <ul class="info-tabs">'. $sPromoListLeft.'</ul></div>'."\n";
	}
	
	if (!empty($sPromoListRight))
	{
//		echo '<div class="promoright">
//			<h4 title="'.$aPromotionTypes[PRM_RIGHTLIST].'">'.$aPromotionTypes[PRM_RIGHTLIST].'</h4>
//			'.$sPromoListRight.'</div>'."\n";
		
			echo '<div class="box">
	                    <h3 class="h3">'.mb_strtolower($aPromotionTypes[PRM_RIGHTLIST], 'utf8').'</h3>            
	                    <ul class="info-tabs">'. $sPromoListRight.'</ul></div>'."\n";	
	}
	
	if($page != 28){

		if (!empty($sPromoNews))
		{
			if ($page == 26) // zavedenia - show as news
			{
	//			echo '
	//				<div id="promonews">
	//					<h4 title="'.$aPromotionTypes[PRM_LEFTLIST].'">'.$aPromotionTypes[PRM_LEFTLIST].'</h4>
	//					'.$sPromoNews.'
	//				</div>'."\n";
				echo '<div class="box">
		                    <h3 class="h3">'.mb_strtolower($aPromotionTypes[PRM_LEFTLIST], 'utf8').'</h3>            
		                    <ul class="info-tabs">'. $sPromoNews.'</ul></div>'."\n";				
			}
			else if ($page == 135 || $page == 167) // mixer - show extra as news
			{
	//			echo '
	//				<div id="promonews">
	//					<h4 title="'.$aPromotionTypes[PRM_EXTRA].'">'.$aPromotionTypes[PRM_EXTRA].'</h4>
	//					'.$sPromoNews.'
	//				</div>'."\n";
				echo '<div class="box">
		                    <h3 class="h3">'.mb_strtolower($aPromotionTypes[PRM_EXTRA], 'utf8').'</h3>            
		                    <ul class="info-tabs">'. $sPromoNews.'</ul></div>'."\n";			
			}
	
			else
	//			echo '
	//				<div id="promonews">
	//					<h4 title="'.$aPromotionTypes[PRM_NEWS].'">'.$aPromotionTypes[PRM_NEWS].'</h4>
	//					'.$sPromoNews.'
	//				</div>'."\n";
				echo '<div class="box">
		                    <h3 class="h3">'.mb_strtolower($aPromotionTypes[PRM_NEWS], 'utf8').'</h3>            
		                    <ul class="info-tabs">'. $sPromoNews.'</ul></div>'."\n";
		}
		if (empty($sPromoNews) || $page == 167)
		{
			// latest news
			$aNewsPages = $oPage->ListAllAsArraySimple($page, '', 'news_list');
			$xParents = -1;
			if (is_array($aNewsPages) && count($aNewsPages) > 0)
				$xParents = array_keys($aNewsPages);
			$rsNews = $oNews->ListAll($xParents, $city, '', null, null, false, NUM_NEWS_FRONT);
			if (mysql_num_rows($rsNews))
			{
	//			echo '<div id="promonews">
	//				<h4 title="'.getLabel('strNews').'">'.getLabel('strNews').'</h4>'."\n";
	//			
				echo '<div class="box">
		                    <h3 class="h3">'.mb_strtolower(getLabel('strNews'), 'utf8').'</h3>            
		                    <ul class="info-tabs">';
				
				$nIdx = 0;
				$output = null;
				while($row = mysql_fetch_object($rsNews))
				{
	//				$sMainImageFile = UPLOAD_DIR.IMG_NEWS_THUMB.$row->NewsID.'.'.EXT_IMG;
					$sMainImageFile = UPLOAD_DIR.IMG_NEWS.$row->NewsID.'.'.EXT_IMG;
					if (!is_file($sMainImageFile))
					{
						$sMainImageFile = UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
					}
					$aRelPages = $oNews->ListNewsPagesAsArray($row->NewsID);
	            	++$nIdx;
	            	
	            	$output .= '
	                    	<li>
	                        	<a href="'.setPage($aRelPages[0], 0, $row->NewsID).'" class="tab" '. ($nIdx == 1 ? 'style="display: none;"' : '') .'><span>'.htmlspecialchars(strip_tags($row->Title)).'</span></a>
	                            
	                        	<div class="description" '. ($nIdx > 1 ? 'style="display: none;"' : '') .'>
	                        		                        		<h4><a href="'.setPage($aRelPages[0], 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'"><span>'.htmlspecialchars(strip_tags($row->Title)).'</span></a></h4>
	                        		<a href="'.setPage($aRelPages[0], 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'. drawImage($sMainImageFile, 300, 164, $row->Title) .'</a>
	                            	<div class="text">'.strShorten(IIF(!empty($row->Lead), $row->Lead, $row->Content), TEXT_LEN_PUBLIC).'</div>
	                        	</div>
	                        </li>
	            	';				
					/*
					
						OLD VERSION
					
					if (empty($nIdx))
						echo '<div class="box">
							<a href="'.setPage($aRelPages[0], 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.drawImage($sMainImageFile, 0, 0, $row->Title).'</a>
							<h3><a href="'.setPage($aRelPages[0], 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.$row->Title.'</a></h3>
							<div class="date">'.formatDate($row->NewsDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</div>
							<div>'.strShorten(IIF(!empty($row->Lead), $row->Lead, $row->Content), TEXT_LEN_PUBLIC).'</div>
							</div>'."\n";
					else
						echo '<div class="box">
							<h6><a href="'.setPage($aRelPages[0], 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.$row->Title.'</a></h6>
							<div class="date">'.formatDate($row->NewsDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</div>
							<a href="'.setPage($aRelPages[0], 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.drawImage($sMainImageFile, 0, 0, $row->Title).'</a>
							<div>'.strShorten(IIF(!empty($row->Lead), $row->Lead, $row->Content), TEXT_LEN_PUBLIC).'</div>
							</div>'."\n";
					$nIdx++; */
				}
				echo $output. '</ul></div>'."\n";
			}
		}
	}
	
	if(!empty($sPromoInterviews))
	{
//		echo '<div class="promointerview">
//			<h4 title="'.$aPromotionTypes[PRM_INTERVIEW].'">'.$aPromotionTypes[PRM_INTERVIEW].'</h4>
//			'.$sPromoInterviews.'</div>'."\n";
		
			echo '<div class="box">
	                    <h3 class="h3">'.mb_strtolower($aPromotionTypes[PRM_INTERVIEW], 'utf8').'</h3>            
	                    <ul class="info-tabs">'. $sPromoInterviews.'</ul></div>'."\n";			
	}
	
	
	$cache = dirname(dirname(__FILE__)) .'/vkushti_tv_cache.txt';
	@eval('$programs = '. file_get_contents($cache) .';');	
	
	if(isset($programs) && is_array($programs)){
		
		// limited to four tv channels
		// change true to false and will ignore this case
		if(true && count($programs) > 4){
			shuffle($programs);
			$programs = array_slice($programs, 0, 4);
		}		
		
	    echo '<div class="box">
	                    <h3 class="h3">'. mb_strtolower(getLabel('strTV'), 'utf8') .'</h3>
	                    
	                    <div class="tv-shows">
	                    	<ul>';

	    	foreach(array_keys($programs) as $key => $program){
	        	echo '<li'. ($key == 0 ? ' class="active"': '') .'><a href="javascript:;" title="'. $program .'"><img src="'. $programs[$program][1]['tv_image'] .'" alt="'. $program .'" id="tv_logo" /></a></li>';
	    	}
	    	
	    echo '              </ul>';

	    	$increase = 0;
	    	foreach($programs as $tv => $unuse){
	    		++$increase;
	    		echo '  <ol'. ($increase > 1 ? ' style="display: none;"' : '') .'>';
	    		foreach($programs[$tv] as $program){
				    echo '
		                	<li>
		                    	<span title="'. $program['time'] .'">'. $program['time'] .'</span>
		                        <a href="'. $program['link'] .'" title="'. $program['title'] .'">'. $program['title'] .'</a>
		                    </li>
			              ';
	    		}
	    		echo '</ol>';
	    	}

	    echo '
	                    </div>
	                </div>';
	}	
	
	echo '</div> <!-- #home -->';
	
}
?>