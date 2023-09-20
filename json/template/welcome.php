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
    while($row = mysql_fetch_object($rsPromotion))
    {
        $rItem = null;
        $sEntityType = $aPromoEntityTypes[$row->EntityTypeID];
        $sMainImageFile = $sEntityText = '';
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

        // big promo
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
            $sPromotions .= '
                <h5 class="showit"><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h5>
                <div class="box box-on">
                    <a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMidImageFile, 0, 0, $sEntityTitle).'</a>
                    <h3><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h3>
                    <div><a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a> '.$sEntityText.'</div>
                </div>'."\n";
        }
        // interview
        elseif ($row->PromotionTypeID == PRM_INTERVIEW)
        {
            $sInterviews .= '
                <div class="box">
                    <a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMidImageFile, 0, 0, $sEntityTitle).'</a>
                    <h3><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h3>
                    <div class="date"><a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a>, '.formatDate($rItem->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT).getLabel('strByUser').$rItem->Author.'</div>
                    <div>'.$rItem->Lead.'</div>
                </div>'."\n";
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
                        <a href="'.setPage($nPageToGo, 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($sEntityTitle)).'">'.drawImage($sMainImageFile, 0, 0, $sEntityTitle).'</a>
                        <h3><a href="'.setPage($nPageToGo, 0, $row->NewsID).'" title="'.htmlspecialchars(strip_tags($sEntityTitle)).'">'.$sEntityTitle.'</a></h3>
                        <div class="date"><a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a>, '.formatDate($row->NewsDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</div>
                        <div>'.$sEntityTextBig.'</div>
                    </div>'."\n";
            else
                $sPromoNews .= '
                    <div class="box">
                        <h6><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h6>
                        <div class="date"><a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a>, '.formatDate($row->NewsDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</div>
                    </div>'."\n";
        }
        // promo list left
        elseif ($row->PromotionTypeID == PRM_LEFTLIST)
        {
            $sPromoListLeft .= '
                <div class="box">
                    <h6><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.htmlspecialchars(strip_tags($sEntityTitle)).'">'.$sEntityTitle.'</a></h6>
                    <a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a>
                </div>'."\n";
        }
        // promo list right
        elseif ($row->PromotionTypeID == PRM_RIGHTLIST)
        {
            $sPromoListRight .= '
                <div class="box">
                    <h6><a href="'.setPage($nPageToGo, 0, $row->EntityID).'" title="'.htmlspecialchars(strip_tags($sEntityTitle)).'">'.$sEntityTitle.'</a></h6>
                    <a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a>
                </div>'."\n";
        }
    }
    if (!is_array($aAccent) || count($aAccent) < NUM_ACCENT_FRONT)
    {
        /*$nMoreItems = NUM_ACCENT_FRONT - count($aAccent);
        $rsPromotion = $oPromotion->ListAll(null, $city, 1, $dStartDate, $dEndDate, false, $nMoreItems);
        while($row = mysql_fetch_object($rsPromotion))
        {
            $aAccent[] = '<div class="fold f'.$nSectionToGo.'"><a href="#" title="'.$aPages[$nSectionToGo].'"><img src="img/'.$lang.'/t_'.$nSectionToGo.'.png" alt="'.$aPages[$nSectionToGo].'" /></a></div>
		    <div class="foldbox"><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMainImageFile, 0, 0, $sEntityTitle).'</a>
		    <h3><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h3>
		    '.IIF(!empty($sEntityText), '<div>'.$sEntityText.'</div>', '').'</div>'."\n";
        }*/
    }
    //if (!empty($sAccent))
    if (is_array($aAccent) && count($aAccent) > 0)
    {
        echo '<h2 title="'.$aPromotionTypes[PRM_ACCENT].'">'.$aPromotionTypes[PRM_ACCENT].'</h2>
            <div id="slider">';
        shuffle($aAccent);
        for($i=0; $i<NUM_ACCENT_FRONT; $i++)
        {
            echo $aAccent[$i]."\n";
        }
        echo '</div>'."\n";
        ?>
        <script type="text/javascript">
        <!--
        var pro_slider = function() {
            var tab = $(this).parent();
            var content = tab.next();
            if(content.is(':hidden')) {
                $('div.fold').removeClass('on');
                    tab.addClass("on");
                    $("div.foldbox:visible").hide();
                    content.show();
            }
        };
        $.fn.sliderToggle = pro_slider;
        $(document).ready(function(){
                $("div.foldbox").hide();
                $("div.fold a").hover(pro_slider);
                $("div.fold a").first().sliderToggle();
        });
        //-->
        </script>
        <?
    }
    if (!empty($sPromotions))
    {
        echo '<div id="promo">
                <h2 title="'.$aPromotionTypes[PRM_PREMIERE].'">'.$aPromotionTypes[PRM_PREMIERE].'</h2>
                '.$sPromotions.'</div>'."\n";
        ?>
        <script type="text/javascript">
        <!--
        jQuery(document).ready(function(){
            jQuery(".showit:first").hide();
            //$("div.bb:first").addClass("box-on");
            jQuery("div.box-on:not(:first)").hide();
            jQuery(".showit a").mouseover(function(){
                jQuery("div.box-on:visible").hide();//slideUp("slow");
                jQuery(this).parent().next().show();//slideDown("slow");
                //$(this).parent().next().addClass("box-on");
                jQuery(".showit").show();
                jQuery(this).parent().hide();
                return false;
            });
        })
        //-->
        </script>
        <?
    }
    if (!empty($sInterviews))
    {
        echo '<div id="interview">
                <h2 title="'.$aPromotionTypes[PRM_INTERVIEW].'">'.$aPromotionTypes[PRM_INTERVIEW].'</h2>
                '.$sInterviews.'
              </div>
              <br class="clear" />'."\n";
    }
    if (!empty($sPromoListLeft) || !empty($sPromoListRight))
    {
        echo '<div id="frontlist">
                <h4 title="'.$aPromotionTypes[PRM_LEFTLIST].'">'.$aPromotionTypes[PRM_LEFTLIST].'</h4>
                <div class="left">'.$sPromoListLeft.'</div>
                <div class="right">'.$sPromoListRight.'</div>
              </div>'."\n";
    }
    if (!empty($sPromoNews))
    {
        echo '<div id="frontnews">
                <h4 title="'.$aPromotionTypes[PRM_NEWS].'">'.$aPromotionTypes[PRM_NEWS].'</h4>
                <div>'.$sPromoNews.'</div>
              </div>'."\n";
    }
    else
    {
        $rsNews = $oNews->ListAll(null, $city, '', null, null, false, NUM_NEWS_FRONT*2);
        if (mysql_num_rows($rsNews))
        {
            $nIdx = 0;
            while($row = mysql_fetch_object($rsNews))
            {
                $sMainImageFile = UPLOAD_DIR.IMG_NEWS_THUMB.$row->NewsID.'.'.EXT_IMG;
                if (!is_file($sMainImageFile))
                {
                    $sMainImageFile = UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
                }
                $aRelPages = $oNews->ListNewsPagesAsArray($row->NewsID);
                $nPageToGo = $aRelPages[0];
                $nSectionToGo = $oPage->GetRootPageID($nPageToGo);

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
            }
            echo '<div id="frontnews">
                    <h4 title="'.getLabel('strNews').'">'.getLabel('strNews').'</h4>
                    <div class="left">'.$sNewsList[0].'</div>
                    <div class="right">'.$sNewsList[1].'</div>
                  </div>'."\n";
        }
    }
?>
<br class="clear" />