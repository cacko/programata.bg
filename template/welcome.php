<?php
if (!$bInSite) die();
//=========================================================
    $dToday = date(DEFAULT_DATE_DB_FORMAT);
    $dStartDate = $dToday;
    $dEndDate = $dToday;
    $aPromotionTypesFull = getLabel('aPromotionTypesFull');
    $aPromotionTypes = $aPromotionTypesFull[DEF_PAGE];

    $aPages = $oPage->ListAllAsArraySimple();

    //$rsPromotion = $oPromotion->ListAll(null, $city, 1, $dStartDate, $dEndDate, false, 5);
    $rsPromotion = $oPromotion->ListAll($page, $city, null, $dStartDate, $dEndDate);
    $aAccent = array();
    //$sAccent = '';
    $sPromotions = $sInterviews = $sPromoListLeft = $sPromoListRight = $sPromoNews = '';
    
    $list_1 = $list_2 = $list_3 = 0;
    $increment = 0;
    while($row = mysql_fetch_object($rsPromotion))
    {
    	++$increment;
        $rItem = null;
        $sEntityType = $aPromoEntityTypes[$row->EntityTypeID];
        $sMainImageFile = $sEntityText = '';
        $aRelPages = null;
        switch($row->EntityTypeID)
        {
            case $aEntityTypes[ENT_NEWS]:
                $rItem = $oNews->GetByID($row->EntityID);
                $sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC));
                $sEntityTextBig = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC));
                $aRelPages = $oNews->ListNewsPagesAsArray($row->EntityID);
                $sMainImageFile = UPLOAD_DIR.IMG_NEWS.$row->EntityID.'.'.EXT_IMG;
                $sMidImageFile = UPLOAD_DIR.IMG_NEWS_MID.$row->EntityID.'.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);

                break;
            case $aEntityTypes[ENT_PUBLICATION]:
                $rItem = $oPublication->GetByID($row->EntityID); //'<strong>'.$rItem->Subtitle.'</strong><br />'.
                $sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC));
                $sEntityTextBig = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC));
                $aRelPages = $oPublication->ListPublicationPagesAsArray($row->EntityID);
                $sMainImageFile = UPLOAD_DIR.IMG_PUBLICATION.$row->EntityID.'.'.EXT_IMG;
                $sMidImageFile = UPLOAD_DIR.IMG_PUBLICATION_MID.$row->EntityID.'.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->Title);

                break;
            case $aEntityTypes[ENT_FESTIVAL]:
                $rItem = $oFestival->GetByID($row->EntityID);
                $sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC));
                $sEntityTextBig = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC));
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
                $sEntityTextBig = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Description, TEXT_LEN_PUBLIC));
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
				$sEntityText = IIF(!empty($rItem->MainTitle), strShorten($rItem->MainTitle, TEXT_LEN_PUBLIC), "");
				$sEntityTextBig = IIF(!empty($rItem->MainTitle), strShorten($rItem->MainTitle, TEXT_LEN_PUBLIC), "");
				$aRelPages = $oUrban->ListUrbanPagesAsArray($row->EntityID);
				$sMainImageFile = UPLOAD_DIR.IMG_URBAN.$row->EntityID.'/'.IMG_MID.'1_1.'.EXT_IMG;
				$sMidImageFile = UPLOAD_DIR.IMG_URBAN.$row->EntityID.'/'.IMG_MID.'1_1.'.EXT_IMG;
				$sEntityTitle = stripComments($rItem->MainTitle);
				break;
			case $aEntityTypes[ENT_MULTY]:
				$rItem = $oMulty->GetByID($row->EntityID);
				$sEntityText = IIF(!empty($rItem->Title), strShorten($rItem->Title, TEXT_LEN_PUBLIC), "");
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
        $nPageToGo = 0;
        if (is_array($aRelPages) && count($aRelPages)>0)
            $nPageToGo = $aRelPages[0];
        if (empty($nPageToGo) && $row->EntityTypeID == $aEntityTypes[ENT_EVENT])
        {
            switch($rItem->EventTypeID)
            {
                case EVENT_MOVIE: // movie
                    $nPageToGo = 21;
                    break;
                case EVENT_PERFORMANCE: // performance
                    $nPageToGo = 22;
                    break;
                case EVENT_EXHIBITION: // exhibition
                    $nPageToGo = 25;
                    break;
                case EVENT_CLASSIC_MUSIC: // classic
                case EVENT_LIVE_MUSIC: // group
                case EVENT_MUSIC_PARTY: // party
                case EVENT_CONCERT: // concert
                    $nPageToGo = 24;
                    break;
                case EVENT_LOGOS: // logos / other
                    $nPageToGo = 28;
                    break;
            }
        }
        $nSectionToGo = $oPage->GetRootPageID($nPageToGo);
        
        if($increment == 1){
			echo '<div id="home">';
        }
		
        // big promo ACCENT SLIDER
        if ($row->PromotionTypeID == PRM_ACCENT)
        {
            $aAccent[] = '
                <div class="fold f'.$nSectionToGo.'"><a href="#" title="'.$aPages[$nSectionToGo].'"><img src="img/'.$lang.'/t_'.$nSectionToGo.'.png" alt="'.$aPages[$nSectionToGo].'" /></a></div>
		<div class="foldbox">
                    <a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMainImageFile, 0, 0, $sEntityTitle).'</a>
		    <h3><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h3>
		    <div><a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a> '.$sEntityText.'</div>
                </div>'."\n";
        }
        // small promo
        elseif ($row->PromotionTypeID == PRM_PREMIERE)
        {
        	
        	/*
            if (!is_array($sPromotions)) {
                $sPromotions = array();
            }
            
            $first_link = 'style="display: block;"';
            $first_description = 'style="display: none;"';
            if (empty($sPromotions))
            {
                $first_link = 'style="display: none;"';
                $first_description = 'style="display: block;"';
            }
            
            $sPromotions[] = '
                <li>
                    <a href="javascript:;" class="tab" '.$first_link.'><span>'.$sEntityTitle.'</span></a>
                    
                    <div class="description" '.$first_description.'>
                        <a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMidImageFile, 0, 0, $sEntityTitle).'</a>
                        <h4><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h4>
                        <a href="'.setPage($nPageToGo).'" class="category">'.$aPages[$nSectionToGo].'</a>
                        <div class="text">'.$sEntityText.'</div>
                    </div>
                </li>';
            
            $sPromotions = implode(' ', $sPromotions);
            */
//            $sPromotions .= '
//                <h5 class="showit"><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h5>
//                <div class="box box-on">
//                    <a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMidImageFile, 0, 0, $sEntityTitle).'</a>
//                    <h3><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h3>
//                    <div><a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a> '.$sEntityText.'</div>
//                </div>'."\n";        
//            

			++$list_1;
            $sPromotions .= '
                    	<li>
                        	<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" class="tab" '.($list_1 == 1 ? 'style="display: none;"' :'').'><span>'.$sEntityTitle.'</span></a>
                            
                        	<div class="description" '. ($list_1 > 1 ? 'style="display: none;"' : '') .'>
                        	                        		<h4><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'"><span>'.$sEntityTitle.'</span></a></h4>
                        		<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'">'.drawImage($sMidImageFile, 0, 0, $sEntityTitle).'</a>
                            	<div class="text">'.$sEntityTextBig.'</div>
                        	</div>
                        </li>            
            ';  	
        }
        // interview
        elseif ($row->PromotionTypeID == PRM_INTERVIEW)
        {
        	/*
        	
        		OLD VERSION
            $sInterviews .= '
                <div class="box">
                    <a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMidImageFile, 0, 0, $sEntityTitle).'</a>
                    <h3><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h3>
                    <div class="date"><a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a>, '.formatDate($rItem->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT).getLabel('strByUser').$rItem->Author.'</div>
                    <div>'.$rItem->Lead.'</div>
                </div>'."\n";
            */
        	
        	++$list_3;
        	
        	$sInterviews .= '
                    	<li>
                        	<a href="javascript:;" class="tab" '.($list_3 == 1 ? 'style="display: none;"' :'').'><span>'.$sEntityTitle.'</span></a>
                            
                        	<div class="description" '. ($list_3 > 1 ? 'style="display: none;"' : '') .'>
                        	                        		<h4><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'"><span>'.$sEntityTitle.'</span></a></h4>
                        		<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'">'.drawImage($sMidImageFile, 0, 0, $sEntityTitle).'</a>
                            	<div class="text">'.$sEntityTextBig.'</div>
                        	</div>
                        </li>
                    
        	';
        }
        // news list
        elseif ($row->PromotionTypeID == PRM_NEWS)
        {
            $aRelPages = $oNews->ListNewsPagesAsArray($row->NewsID);
            $nPageToGo = $aRelPages[0];
            $nSectionToGo = $oPage->GetRootPageID($nPageToGo);
            if (empty($sPromoNews))
                $sPromoNews .= '
                    <div class="box">
                                            <h3><a href="'.setPage($nPageToGo, 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($sEntityTitle)).'">'.$sEntityTitle.'</a></h3>
                        <a href="'.setPage($nPageToGo, 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($sEntityTitle)).'">'.drawImage($sMainImageFile, 0, 0, $sEntityTitle).'</a>
                        <div class="date"><a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a>, '.formatDate($row->NewsDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</div>
                        <div>'.$sEntityTextBig.'</div>
                    </div>'."\n";
            else
                $sPromoNews .= '
                    <div class="box">
                        <h6><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h6>
                        <div class="date"><a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a>, '.formatDate($row->NewsDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</div>
                    </div>'."\n";
//        }
        }elseif($row->PromotionTypeID == PRM_LEFTLIST || $row->PromotionTypeID == PRM_RIGHTLIST){
        	++$list_2;
        	$sPromoList .= '
                    	<li>
                        	<a href="javascript:;" class="tab" '.($list_2 == 1 ? 'style="display: none;"' :'').'><span>'.$sEntityTitle.'</span></a>
                            
                        	<div class="description" '. ($list_2 > 1 ? 'style="display: none;"' : '') .'>
                        	                        		<h4><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'"><span>'.$sEntityTitle.'</span></a></h4>
                        		<a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.$sEntityTitle.'">'.drawImage($sMainImageFile, 300, 164, $sEntityTitle).'</a>
                            	<div class="text">'.$sEntityTextBig.'</div>
                        	</div>
                        </li>
        	';        	
        }
        // promo list left
//        elseif ($row->PromotionTypeID == PRM_LEFTLIST)
//        {
//        	#d(drawImage($sMainImageFile, 0, 0, $sEntityTitle));
//            $sPromoListLeft .= '
//                <div class="box">
//                    <h6><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.htmlspecialchars(strip_tags($sEntityTitle)).'">'.$sEntityTitle.'</a></h6>
//                    <a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a>
//                </div>'."\n";
//        }
//        // promo list right
//        elseif ($row->PromotionTypeID == PRM_RIGHTLIST)
//        {
//            $sPromoListRight .= '
//                <div class="box">
//                    <h6><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.htmlspecialchars(strip_tags($sEntityTitle)).'">'.$sEntityTitle.'</a></h6>
//                    <a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a>
//                </div>'."\n";
//        }
    }
    if (!is_array($aAccent) || count($aAccent) < NUM_ACCENT_FRONT)
    {
    	/*
        $nMoreItems = NUM_ACCENT_FRONT - count($aAccent);
        $rsPromotion = $oPromotion->ListAll(null, $city, 1, $dStartDate, $dEndDate, false, $nMoreItems);
        while($row = mysql_fetch_object($rsPromotion))
        {
            $aAccent[] = '<div class="fold f'.$nSectionToGo.'"><a href="#" title="'.$aPages[$nSectionToGo].'"><img src="img/'.$lang.'/t_'.$nSectionToGo.'.png" alt="'.$aPages[$nSectionToGo].'" /></a></div>
		    <div class="foldbox"><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMainImageFile, 0, 0, $sEntityTitle).'</a>
		    <h3><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h3>
		    '.IIF(!empty($sEntityText), '<div>'.$sEntityText.'</div>', '').'</div>'."\n";
        }
        */
    }
    
    //if (!empty($sAccent))
    
    if (false && is_array($aAccent) && count($aAccent) > 0)
    {
    	
    	#####################
    	#### Accent Part ####
    	#####################

        echo '<h2 title="'.$aPromotionTypes[PRM_ACCENT].'">'.$aPromotionTypes[PRM_ACCENT].'</h2>
            <div id="slider">';
        shuffle($aAccent);
        for($i=0; $i<NUM_ACCENT_FRONT; $i++)
        {
            echo $aAccent[$i]."\n";
            
        }
        echo '</div>'."\n";
    }
    
    
    // Today and Tomorrow
	if($city == 1)
	{		
            $tdy = '['.date('d.m.Y').']';
            $tomorrow  = '['.date('d.m.Y', strtotime('+1 days')).']';
          #  $tdy = '[08.12.2011]';
          # $tomorrow  = '[08.12.2011]';
            $cache_data = null;
	 
            $todayMulty = $oMulty->ListAll(null, $tdy, null, null, false, null, null);
            $tomorrowMulty = $oMulty->ListAll(null, $tomorrow, null, null, false, null, null);
            

            if (mysql_num_rows($todayMulty))
            {
            	 $today_increase = 0;
                 while($row = mysql_fetch_object($todayMulty))
                    {           

                    	$content = $oMulty->GetPartText($row->MultyID,1);
                    	
                    	++$today_increase;	
	                    $sMainImageFile = UPLOAD_DIR.IMG_MULTY.$row->MultyID.'/'.IMG_MID.'1.'.EXT_IMG;
		                    if (!is_file($sMainImageFile))
		                    {
		                            $sMainImageFile = UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
		                    }

	                    $aRelPages = $oMulty->ListMultyPagesAsArray($row->MultyID);
	                    $nPageToGo = $aRelPages[0];
	                    $nSectionToGo = $oPage->GetRootPageID($nPageToGo);
	
//	                    $sTodayNews .= '<div class="box">
//	                            <a href="'.setPage($nPageToGo, 0, $row->MultyID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.drawImage($sMainImageFile, 0, 0, $row->Title).'</a>
//	                            <h3><a href="'.setPage($nPageToGo, 0, $row->MultyID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.stripComments($row->Title).'</a></h3>
//	                            <div class="date"><a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a></div>
//	                            <div>'.strShorten(IIF(!empty($row->Lead), $row->Lead, $row->Content), TEXT_LEN_PUBLIC).'</div>
//	                            </div>'."\n";
						
						$cache_data['today'][] = array(
							                       'title' => htmlspecialchars(strip_tags(stripComments($row->Title))),
							                       'image' => drawImage($sMainImageFile, 300, 164, htmlspecialchars(strip_tags(stripComments($row->Title))), '','', true),
							                       'content' => strShorten($content, TEXT_LEN_PUBLIC),
						                      	   'link' => 'http://'. SITE_URL .'/'. setPage($nPageToGo, 0, $row->MultyID)
							                     );

		            	$sTodayNews .= '
	                    	<li>
	                        	<a href="javascript:;" class="tab" '.($today_increase == 1 ? 'style="display: none;"' :'').'><span>'.htmlspecialchars(strip_tags($row->Title)).'</span></a>
	                            
	                        	<div class="description" '. ($today_increase > 1 ? 'style="display: none;"' : '') .'>
	                        		<h4><a href="'.setPage($nPageToGo, 0, $row->MultyID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'"><span>'.htmlspecialchars(strip_tags(stripComments($row->Title))).'</span></a></h4>
	                        		<a href="'.setPage($nPageToGo, 0, $row->MultyID).'" title="'.htmlspecialchars(strip_tags(stripComments($row->Title))).'">'.drawImage($sMainImageFile, 300, 164, htmlspecialchars(strip_tags(stripComments($row->Title)))).'</a>
	                            	<div class="text">'.strShorten($content, TEXT_LEN_PUBLIC).'</div>
	                        	</div>
	                        </li>	            	
		            	';

                    }

					echo '<div class="box">
			                <h3 class="h3">'. mb_strtolower(getLabel('strTodayNews'), 'utf8').'</h3>            
			                <ul class="info-tabs">
			                '. $sTodayNews .'
			              </div>';                    
//                    echo '<div id="todayNews">
//                    <h4 title="'.getLabel('strTodayNews').'">'.getLabel('strTodayNews').'</h4>
//                    <div>'.$sTodayNews.'</div>
//                  </div>'."\n";

            }

            
            if (mysql_num_rows($tomorrowMulty))
            {
            	$tomorrow_increase = 0;
	        	while($row = mysql_fetch_object($tomorrowMulty))
	        	{
                    $content = $oMulty->GetPartText($row->MultyID,1);	        		
	        		++$tomorrow_increase;
	                $sMainImageFile = UPLOAD_DIR.IMG_MULTY.$row->MultyID.'/'.IMG_MID.'1.'.EXT_IMG;
		            	if (!is_file($sMainImageFile))
		            	{
		                	$sMainImageFile = UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
		            	}
	            	$aRelPages = $oMulty->ListMultyPagesAsArray($row->MultyID);
	            	$nPageToGo = $aRelPages[0];
	            	$nSectionToGo = $oPage->GetRootPageID($nPageToGo);
	
//	            	$sTomorrowNews .= '<div class="box">
//	                        <a href="'.setPage($nPageToGo, 0, $row->MultyID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.drawImage($sMainImageFile, 0, 0, $row->Title).'</a>
//	                        <h3><a href="'.setPage($nPageToGo, 0, $row->MultyID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.stripComments($row->Title).'</a></h3>
//	                        <div class="date"><a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a></div>
//	                        <div>'.strShorten(IIF(!empty($row->Lead), $row->Lead, $row->Content), TEXT_LEN_PUBLIC).'</div>
//	                        </div>'."\n";
	            	
					$cache_data['tomorrow'][] = array(
						                       'title' => htmlspecialchars(strip_tags(stripComments($row->Title))),
						                       'image' => drawImage($sMainImageFile, 300, 164, htmlspecialchars(strip_tags(stripComments($row->Title))), '','', true),
						                       'content' => strShorten($content, TEXT_LEN_PUBLIC),
						                       'link' => 'http://'. SITE_URL .'/'. setPage($nPageToGo, 0, $row->MultyID)
						                     );
	            	
	            	$sTomorrowNews .= '
                    	<li>
                        	<a href="javascript:;" class="tab" '.($tomorrow_increase == 1 ? 'style="display: none;"' :'').'><span>'.htmlspecialchars(strip_tags($row->Title)).'</span></a>
                            
                        	<div class="description" '. ($tomorrow_increase > 1 ? 'style="display: none;"' : '') .'>
                        	                        		<h4><a href="'.setPage($nPageToGo, 0, $row->MultyID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'"><span>'.htmlspecialchars(strip_tags(stripComments($row->Title))).'</span></a></h4>
                        		<a href="'.setPage($nPageToGo, 0, $row->MultyID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.drawImage($sMainImageFile, 300, 164, htmlspecialchars(strip_tags(stripComments($row->Title)))).'</a>
                            	<div class="text">'.strShorten($content, TEXT_LEN_PUBLIC).'</div>
                        	</div>
                        </li>	            	
	            	';
	        	}
	        	            
//	        	echo '<div id="tomorrowNews">
//	                <h4 title="'.getLabel('strTomorrowNews').'">'.getLabel('strTomorrowNews').'</h4>
//	                <div>'.$sTomorrowNews.'</div>
//	              </div>'."\n";

				echo '<div class="box">
		                <h3 class="h3">'. mb_strtolower(getLabel('strTomorrowNews'), 'utf8').'</h3>            
		                <ul class="info-tabs">
		                '. $sTomorrowNews .'
		              </div>';	        	
            }
            
            if(is_array($cache_data)){
				$cache_file = dirname(dirname(__FILE__)) .'/cache_today_tomorrow.txt';
				$time_file = dirname(dirname(__FILE__)) .'/last_update_today_tomorrow.txt';
				$time = 10; // minutes
				if(!is_file($cache_file)){
					file_put_contents($cache_file, var_export($cache_data, true));
				}

				if(!is_file($time_file)){
					file_put_contents($time_file, time());
				}
				
				$distance = (int)date('i', time() - (int)file_get_contents($time_file));
				
				if($distance > $time){
					file_put_contents($time_file, time());
					file_put_contents($cache_file, var_export($cache_data, true));
				}
            }            
    }
    
    if (!empty($sPromoNews))
    {
    	
    #############################
    //Tova ne go namerih kude e//
    #############################    	
        echo '<div id="frontnews">
                <h4 title="'.$aPromotionTypes[PRM_NEWS].'">'. mb_strtolower($aPromotionTypes[PRM_NEWS], 'utf8').'</h4>
                <div>'.$sPromoNews.'</div>
              </div>'."\n";
    }
    else
    {

    	if($city == 1)
		{
        	$rsNews = $oNews->ListAll(null, $city, '', null, null, false, 6);
			$news_count = 0;
			$news1 = rand(1, 6);
			$news2 = rand(1, 6);
			while($news1 == $news2)
				$news2 = rand(1, 6);
        	if (mysql_num_rows($rsNews))
        	{
            	$nIdx = 0;
            	$output = null;
            	while($row = mysql_fetch_object($rsNews))
            	{
//                	$sMainImageFile = UPLOAD_DIR.IMG_NEWS_THUMB.$row->NewsID.'.'.EXT_IMG;
					$news_count++;
					if($news_count != $news1 && $news_count != $news2) continue;
                	$sMainImageFile = UPLOAD_DIR.IMG_NEWS.$row->NewsID.'.'.EXT_IMG;
                	if (!is_file($sMainImageFile))
                	{
                    	$sMainImageFile = UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
                	}
                	$aRelPages = $oNews->ListNewsPagesAsArray($row->NewsID);
                	$nPageToGo = $aRelPages[0];
                	$nSectionToGo = $oPage->GetRootPageID($nPageToGo);
					
                	++$nIdx;
                	
                	$output .= '
	                    	<li>
	                        	<a href="'.setPage($nPageToGo, 0, $row->NewsID).'" class="tab" '. ($nIdx == 1 ? 'style="display: none;"' : '') .'><span>'.htmlspecialchars(strip_tags($row->Title)).'</span></a>
	                            
	                        	<div class="description" '. ($nIdx > 1 ? 'style="display: none;"' : '') .'>
	                        		                        		<h4><a href="'.setPage($nPageToGo, 0, $row->NewsID).'"><span>'.htmlspecialchars(strip_tags($row->Title)).'</span></a></h4>
	                        		<a href="'.setPage($nPageToGo, 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'. drawImage($sMainImageFile, 300, 164, $row->Title) .'</a>
	                            	<div class="text">'.strShorten(IIF(!empty($row->Lead), $row->Lead, $row->Content), TEXT_LEN_PUBLIC).'</div>
	                        	</div>
	                        </li>
                	';
                	
                	
                	/*
                	
                		OLD VERSION
                	
                	$nNewsIndex = 1;
                	if ($nIdx%2 == 0)
                    	$nNewsIndex = 0;
                	if (empty($sNewsList[$nNewsIndex]))
                    	$sNewsList[$nNewsIndex] .= '<div class="box">
                            <a href="'.setPage($nPageToGo, 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.drawImage($sMainImageFile, 0, 0, $row->Title).'</a>
                            <h3><a href="'.setPage($nPageToGo, 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.$row->Title.'</a></h3>
                            <div class="date"><a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a>, '.formatDate($row->NewsDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</div>
                            <div>'.strShorten(IIF(!empty($row->Lead), $row->Lead, $row->Content), TEXT_LEN_PUBLIC).'</div>
                            </div>'."\n";
                	else
                    	$sNewsList[$nNewsIndex] .= '<div class="box">
                            <h6><a href="'.setPage($nPageToGo, 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.$row->Title.'</a></h6>
                            <div class="date"><a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a>, '.formatDate($row->NewsDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</div>
                            </div>'."\n";
                		$nIdx++;
                	*/
            	}
            	/*
            	
            		OLDL VERSION
            	
                    echo '<div id="frontnews">
                    <h4 title="'.getLabel('strNews').'">'.getLabel('strNews').'</h4>
                    <div class="left">'.$sNewsList[0].'</div>
                    <div class="right">'.$sNewsList[1].'</div>
                  </div>'."\n";
                */
                
            	echo '
					<div class="box">
	                    <h3 class="h3">'. mb_strtolower(getLabel('strNews'), 'utf8').'</h3>            
	                    <ul class="info-tabs">
	               			'. $output .'
	                    </ul>
	                </div>            	
            	';
        	}
		}
    }     
	
    if (!empty($sPromotions))
    {
		echo '<div class="box">
                <h3 class="h3">'. mb_strtolower($aPromotionTypes[PRM_PREMIERE], 'utf8').'</h3>            
                <ul class="info-tabs">
                '. $sPromotions .'
              </div>';
    }    
    if (!empty($sInterviews))
    {
        echo '<div class="box">
                <h3 class="h3">'. mb_strtolower(getLabel('strPromoPublications'), 'utf8') .'</h3>
                    <ul class="info-tabs">
                '.$sInterviews.'
                	</ul>
              </div>';
    }
    
//    if (!empty($sPromoListLeft) || !empty($sPromoListRight))
    if (false && !empty($sPromoList))
    {
 
        	
//        echo '<div id="frontlist">
//                <h4 title="'.$aPromotionTypes[PRM_LEFTLIST].'">'.$aPromotionTypes[PRM_LEFTLIST].'</h4>
//                <div class="left">'.$sPromoListLeft.'</div>
//                <div class="right">'.$sPromoListRight.'</div>
//              </div>'."\n";

		echo '<div class="box">
                <h3 class="h3">'. mb_strtolower(getLabel('strPromoLists'), 'utf8') .'</h3>            
                <ul class="info-tabs">
                '. $sPromoList .'
              </div>';

    }
    
    // cache zone
    $target = 'http://vkushti.tv/?parse=1';
    $last_update = dirname(dirname(__FILE__)) .'/last_update.txt';
    $cache = dirname(dirname(__FILE__)) .'/vkushti_tv_cache.txt';
    $now = time();
    $distance_from_last_cache = 10; // minutes
    if(!is_file($last_update)){
    	file_put_contents($last_update, time());
    }
    
    if(!is_file($cache)){
    	@eval('$programs = '. file_get_contents($target) .';');
    	file_put_contents($cache,(isset($programs) && is_array($programs) && count($programs) > 0 ? var_export($programs, true) : null));
    	unset($programs);
    }
    
	$time_difference = (int)date('i', $now - file_get_contents($last_update));

	if($time_difference >= $distance_from_last_cache){
    	@eval('$programs = '. file_get_contents($target) .';');
    	file_put_contents($cache,(isset($programs) && is_array($programs) && count($programs) > 0 ? var_export($programs, true) : null));
    	file_put_contents($last_update, time());
    	unset($programs);
	}
	
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

    if($increment == 1){
	   echo '</div> <!-- #home -->'; 
    }
?>