<?php
ob_start();
ini_set('error_reporting', E_ALL & ~E_NOTICE);
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 'On');

	$bInSite = true;

	function d($source){
		echo '<div style="background-color: #000; color: #fff; font-family: Tahoma; width: 100%;">
				<pre>';
		print_r($source);
		echo '  </pre>
			  </div>';
	}
	

	require_once('initialize.php');
	include_once('helper/user_action.php');

	if(strstr($_SERVER['HTTP_USER_AGENT'], 'Dalvik')) {
		Header( "Location: http://programata.bg/Dalvik.html" );
		exit;
	}
	
	if(isset($_GET['today_and_tomorrow'])){
		echo @file_get_contents('cache_today_tomorrow.txt');
		exit;
	}
	
	// check for valid query string ids
	$rCurrentPage = $oPage->GetByID($page);
	if (!$rCurrentPage)
	{
		$page = DEF_PAGE;
		$rCurrentPage = $oPage->GetByID(DEF_PAGE);
	}

	$nRootPage = $oPage->GetRootPageID($page);
	$rRootPage = $oPage->GetByID($nRootPage);

	$sItemTitle = '';
	$sItemDescription = '';
	$sItemKeywords = '';
	$template_file_arr = explode("_", $rCurrentPage->TemplateFile);
	$template_type = $template_file_arr[0];
	if (isset($item) && !empty($item))
	{
		$img_path = UPLOAD_DIR.$template_type.'_'.$item.'.'.EXT_IMG;
	}
	if (!is_file($img_path))
	{
		$img_path = "/public/images/logo-programata.png";
	}
	
	if (((strstr($rCurrentPage->TemplateFile, 'event_list') !== false) || $rCurrentPage->TemplateFile == 'section') && !empty($item))
	{
		$rItem = $oEvent->GetByID($item);
		if ($rItem)
		{
			$sItemTitle = htmlspecialchars(strip_tags($rItem->Title));
			if (!empty($rItem->MetaDescription))
				$sItemDescription = $rItem->MetaDescription.' ';
			if (!empty($rItem->MetaKeywords))
				$sItemDescription = $rItem->MetaKeywords.', ';
		}
	}
	elseif (strstr($rCurrentPage->TemplateFile, 'place_list') !== false && !empty($item))
	{
		$rItem = $oPlace->GetByID($item);
		if ($rItem)
		{
			$sItemTitle = htmlspecialchars(strip_tags($rItem->Title));
			if (!empty($rItem->MetaDescription))
				$sItemDescription = $rItem->MetaDescription.' ';
			if (!empty($rItem->MetaKeywords))
				$sItemDescription = $rItem->MetaKeywords.', ';
		}
	}
	if ($rCurrentPage->TemplateFile == 'festival_list' && !empty($item))
	{
		$rItem = $oFestival->GetByID($item);
		if ($rItem)
		{
			$sItemTitle = htmlspecialchars(strip_tags($rItem->Title));
			if (!empty($rItem->MetaDescription))
				$sItemDescription = $rItem->MetaDescription.' ';
			if (!empty($rItem->MetaKeywords))
				$sItemDescription = $rItem->MetaKeywords.', ';
		}
	}
	if ($rCurrentPage->TemplateFile == 'news_list' && !empty($item))
	{
		$rItem = $oNews->GetByID($item);
		if ($rItem)
		{
			$sItemTitle = htmlspecialchars(strip_tags($rItem->Title));
			if (!empty($rItem->MetaDescription))
				$sItemDescription = $rItem->MetaDescription.' ';
			if (!empty($rItem->MetaKeywords))
				$sItemDescription = $rItem->MetaKeywords.', ';
		}
	}
	if ($rCurrentPage->TemplateFile == 'publication_list' && !empty($item))
	{
		$rItem = $oPublication->GetByID($item);
		if ($rItem)
		{
			$sItemTitle = htmlspecialchars(strip_tags($rItem->Title));
			if (!empty($rItem->MetaDescription))
				$sItemDescription = $rItem->MetaDescription.' ';
			if (!empty($rItem->MetaKeywords))
				$sItemDescription = $rItem->MetaKeywords.', ';
		}
	}
	if ($rCurrentPage->TemplateFile == 'extra_list' && !empty($item))
	{
		$rItem = $oExtra->GetByID($item);
		if ($rItem)
		{
			$sItemTitle = htmlspecialchars(strip_tags($rItem->Title));
			if (!empty($rItem->MetaDescription))
				$sItemDescription = $rItem->MetaDescription.' ';
			if (!empty($rItem->MetaKeywords))
				$sItemDescription = $rItem->MetaKeywords.', ';
		}
	}
	$aCities = getLabel('aCities');
        
// Classes
$_names_paeges = array(
    167 => 'my-city',
    21 => 'movies',
    22 => 'stage',
    24 => 'music',
    25 => 'exhibition',
    26 => 'places',
    28 => 'logos',
);

$_section_icons = array(
	24 => '<li class="f24"><a href="javascript:void(0);" title="#24#">#24#</a></li>', // muzika
	21 => '<li class="f21"><a href="javascript:void(0);" title="#21#">#21#</a></li>', // kina
	22 => '<li class="f22"><a href="javascript:void(0);" title="#22#">#22#</a></li>', // scena
	25 => '<li class="f25"><a href="javascript:void(0);" title="#25#">#25#</a></li>', // izlojbi
	26 => '<li class="f26"><a href="javascript:void(0);" title="#26#">#26#</a></li>', // zavedeniq
	28 => '<li class="f28"><a href="javascript:void(0);" title="#28#">#28#</a></li>', // slovo
	167 => '<li class="f167"><a href="javascript:void(0);" title="#167#">#167#</a></li>' // my city
);
														  
$_section_menu = array();

	##############################################################
	##############################################################
	################### GET HOMEPAGE ACCENTS #####################
	##############################################################
	##############################################################

    $dToday = date(DEFAULT_DATE_DB_FORMAT);
	
    $dStartDate = $dToday;
    $dEndDate = $dToday;
    $aPromotionTypesFull = getLabel('aPromotionTypesFull');
    $aPromotionTypes = $aPromotionTypesFull[DEF_PAGE];

    $aPages = $oPage->ListAllAsArraySimple();


    //$rsPromotion = $oPromotion->ListAll(null, $city, 1, $dStartDate, $dEndDate, false, 5);
    $rsPromotion = $oPromotion->ListAll($page, $city, PRM_ACCENT, $dStartDate, $dEndDate);

    $aAccent = array();

    while($row = mysql_fetch_object($rsPromotion))
    {
    	
    #	d($row);
    	
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
        
        if ($row->PromotionTypeID == PRM_ACCENT)
        {
    #d($row);
//            $aAccent[] = '
//                <div class="fold f'.$nSectionToGo.'"><a href="#" title="'.$aPages[$nSectionToGo].'"><img src="img/'.$lang.'/t_'.$nSectionToGo.'.png" alt="'.$aPages[$nSectionToGo].'" /></a></div>
//		<div class="foldbox">
//                    <a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.drawImage($sMainImageFile, 0, 0, $sEntityTitle).'</a>
//		    <h3><a href="'.setPage($nPageToGo, 0, $row->EntityID).'">'.$sEntityTitle.'</a></h3>
//		    <div><a href="'.setPage($nPageToGo).'">'.$aPages[$nSectionToGo].'</a> '.$sEntityText.'</div>
//                </div>'."\n";
//		d($aPages[$nSectionToGo]);
//d($nSectionToGo);
		if(count($prepare_accents) == 0){
			$prepare_accents[$nSectionToGo][] = '   <a class="image active item_'.$row->EntityID.'" href="'. setPage($nPageToGo, 0, $row->EntityID) .'" title="'.$sEntityTitle.'">'.drawImage($sMainImageFile, 638, 332, $sEntityTitle).'</a>
							                        <h3 class="item_'.$row->EntityID.' active"><a href="'. setPage($nPageToGo, 0, $row->EntityID) .'" title="'.$sEntityTitle.'"><strong>'. $aPages[$nSectionToGo] .': </strong>'. $sEntityTitle .'</a></h3>';			
		}else{
			$prepare_accents[$nSectionToGo][] = '   <a class="image item_'.$row->EntityID.'" href="'. setPage($nPageToGo, 0, $row->EntityID) .'" title="'.$sEntityTitle.'">'.drawImage($sMainImageFile, 638, 332, $sEntityTitle).'</a>
							                        <h3 class="item_'.$row->EntityID.'"><a href="'. setPage($nPageToGo, 0, $row->EntityID) .'" title="'.$sEntityTitle.'"><strong>'. $aPages[$nSectionToGo] .': </strong>'. $sEntityTitle .'</a></h3>';
		}

//		$prepare_accents[$nSectionToGo][] = '<ul class="_'. $nSectionToGo .'">		
//											<li>
//						                        <a href="'. setPage($nPageToGo, 0, $row->EntityID) .'" title="'.$sEntityTitle.'">'.drawImage($sMainImageFile, 638, 332, $sEntityTitle).'</a>
//						                        <h3><a href="'. setPage($nPageToGo, 0, $row->EntityID) .'" title="'.$sEntityTitle.'"><strong>'. $aPages[$nSectionToGo] .': </strong>'. $sEntityTitle .'</a></h3>
//						                    </li>
//						                    </ul>
//						                    ';
	#	d($_section_menu);
	#	d($nSectionToGo);
			
			if(isset($_section_icons[$nSectionToGo])){
					$_section_menu[$nSectionToGo][] = str_replace('#'. $nSectionToGo .'#', $aPages[$nSectionToGo],$_section_icons[$nSectionToGo]);	
			}
			
        }        
    }
   #  d($prepare_accents);
   # d($_section_menu);
   # d($_section_menu);
   //$aPromotionTypes[PRM_ACCENT]
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?=$aAbbrLanguages[$lang]?>" lang="<?=$aAbbrLanguages[$lang]?>">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?=DEF_ENCODING?>" />
	<meta http-equiv="content-language" content="<?=$aAbbrLanguages[$lang]?>">

    <meta name="googlebot" content="index, follow" />
    <meta name="robots" content="index, follow" />

    <meta name="author" content="<?=getLabel('strSiteAuthor')?>" />
    <meta name="description" content="<?=$sItemDescription.$rCurrentPage->MetaDescription.' '.getLabel('strSiteDescription')?>" />
    <meta name="keywords" content="<?=$sItemKeywords.$rCurrentPage->MetaKeywords.', '.getLabel('strSiteKeywords')?>" />

	<meta property="og:image" content="http://programata.bg/<?=$img_path?>" />
	<meta property="og:title" content="<?=IIF(!empty($sItemTitle), stripComments($sItemTitle), getLabel('strSiteTitle'))?>" />
	<meta property="og:type" content="article"/>
	<meta property="og:url" content="<?='http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']?>"/>
	<meta property="og:description" content="<?=$sItemDescription.$rCurrentPage->MetaDescription.' '.getLabel('strSiteDescription')?>"/>
	<meta property="fb:admins" content="" />
	
	
    
    <title><?=IIF(!empty($sItemTitle), stripComments($sItemTitle).' : ', '').getLabel('strSiteTitle').IIF($page != DEF_PAGE, ' : '.$rCurrentPage->Title, ' - '.getLabel('strMoto')).' : '.$aCities[$city]?></title>
    
    <link rel="stylesheet" href="public/css/reset.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="public/css/default.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="public/css/print.css" type="text/css" media="print" />
    <link rel="stylesheet" href="public/css/ui/overcast/jquery-ui-1.8.16.custom.css" type="text/css" media="screen" />
    <link rel="stylesheet" href="style/jquery.lightbox-0.5.css" type="text/css" media="screen" />

    <script type="text/javascript">
    	var lightbox_path = '/new/';
    </script>
    
    <script type="text/javascript" src="public/js/jquery.js"></script>
    <script type="text/javascript" src="js/common.js"></script>
    <script type="text/javascript" src="js/val.js"></script>
    <script type="text/javascript" src="public/js/libraries/jquery.jcarousel.js"></script>
    <script type="text/javascript" src="public/js/libraries/ui/jquery-ui.js"></script>
    <script type="text/javascript" src="public/js/libraries/ui/i18n/jquery.ui.datepicker-bg.js"></script>
    <script type="text/javascript" src="js/jquery.lightbox-0.5.js"></script>

    <script type="text/javascript" src="public/js/default.js"></script>
    <script type="text/javascript" src="js/flowplayer-3.1.4.min.js"></script>

    <!--[if IE 6]>
        <link href="public/css/fixes/ie6.css" media="screen" rel="stylesheet" type="text/css" />
        <script type="text/javascript" src="public/js/pngfix8a-min.js"></script>
    <![endif]-->
    <!--[if IE 7]>
        <link href="public/css/fixes/ie7.css" media="screen" rel="stylesheet" type="text/css" />
    <![endif]-->
    
<?
	$sTitleColor = $aTitleColors[0];
	if (!in_array($nRootPage, $aSysNavigation) && $page != DEF_PAGE && $nRootPage != USERROOT_PAGE)
	{
		$nColorIdx = $nRootPage - 20;
		if (in_array($nColorIdx, array_keys($aTitleColors)))
                    $sTitleColor = $aTitleColors[$nColorIdx];
		else
                    $sTitleColor = $aTitleColors[0];
		echo '<link rel="alternate" type="application/rss+xml" title="'.$rRootPage->Title.' : '.getLabel('strRSS').'" href="http://'.SITE_URL.'/'.FEED_DIR.'/rss2-'.$nRootPage.'-'.$lang.'-'.$city.'.xml" />'."\n";
	}
	else
	{
            echo '
                <link rel="alternate" type="application/rss+xml" title="'.getLabel('strSiteTitle').' : '.getLabel('strInterviews').' : '.getLabel('strRSS').'" href="http://'.SITE_URL.'/'.FEED_DIR.'/rss2-publications-'.$lang.'.xml" />
                <link rel="alternate" type="application/rss+xml" title="'.getLabel('strSiteTitle').' : '.getLabel('strNews').' : '.getLabel('strRSS').'" href="http://'.SITE_URL.'/'.FEED_DIR.'/rss2-news-'.$lang.'.xml" />'."\n";
	}
?>
    
    <link rel="shortcut icon" href="public/images/favicon.ico" type="image/x-icon" />
    <link rel="icon" href="public/images/favicon.ico" type="image/x-icon" />
</head>

<?
	$class = 'home-page';
	if (isset($_names_paeges[$nRootPage]))
	{
    	$class = $_names_paeges[$nRootPage];
	}
    require_once('entity/branding.php');
    if($branding = Branding::getAvailable()) {
       $branding->display($class);
    }
    else {
        echo "<body id='body' class='".$class."'>";
    }
?>

<script type="text/javascript">
window.onload = function () {SetTimer()}
</script>

<div id="wrapper">
    
    <div id="header">
        <h1 title="<?=getLabel('strSiteTitle')?>">
            <a href="<?=setPage(DEF_PAGE)?>"><?=getLabel('strSiteTitle')?></a>
        </h1>

        <div class="user-navigation">
            <?=writeLang()?>
            <?php
            	if(!empty($_SESSION[SS_USER_ID])){
            		echo '<a href="javascript:void(0);" id="login_profile_button" rel="logged" title="'. getLabel('strProfile') .'">'. getLabel('strProfile') .'</a>';
            	}else{
            		echo '<a href="javascript:void(0);" id="login_profile_button" title="'. getLabel('strLogin') .'">'. getLabel('strLogin') .'</a>';
            	}
            ?>
        </div>        

        	<?php
        		include 'template/user_login.php';
        	?>
        
        <div class="cities">
            <?
                $aCities = getLabel('aCities');
                foreach($aCities as $key=>$val)
                {
                    echo '<a href="'.setPage($nRootPage, 0, 0, 0, 0, $key).'" '.IIF($city==$key, ' class="current"','').'>'.$val.'</a>';
                }
            ?>
        </div>

        <div class="applications">
        	<?php

        		$_months = array(
        							'Jan' => 'Ян',
        							'Feb' => 'Фев',
        							'Mar' => 'Мар',
        							'Apr' => 'Апр',
        							'May' => 'Май',
        							'Jun' => 'Юни',
        							'Jul' => 'Юли',
        							'Aug' => 'Авг',
        							'Sep' => 'Сеп',
        							'Oct' => 'Окт',
        							'Nov' => 'Ное',
        							'Dec' => 'Дек',
        						);
        	?>
            <span title="<?=date('d')?> <?php echo mb_strtolower(($aAbbrLanguages[$lang] == 'en' ? date('M') : $_months[date('M')]), 'utf8') ?> <?=date('H:i')?>"><?=date('d')?> <strong><?php echo mb_strtolower(($aAbbrLanguages[$lang] == 'en' ? date('M') : $_months[date('M')]), 'utf8'); ?></strong> <?=date('H:i')?></span>
 
            <a href="https://www.facebook.com/pages/PROGRAMATA/73443607114?v=wall" class="facebook" title="Facebook" target="_blank">Facebook</a>
            <a href="http://itunes.apple.com/bg/app/programata-mobile/id484011220?mt=8" class="ios" title="iOS" target="_blank">iOS</a>
            <a href="https://market.android.com/details?id=com.programata.bg" class="android" title="Android" target="_blank">Android</a>
        </div>
    </div> <!-- #header -->
    
    <div id="banners-top">
        <div class="first">
<script type='text/javascript'><!--//<![CDATA[
   var m3_u = (location.protocol=='https:'?'https://ads.vkushti.tv/new/www/delivery/ajs.php':'http://ads.vkushti.tv/new/www/delivery/ajs.php');
   var m3_r = Math.floor(Math.random()*99999999999);
   if (!document.MAX_used) document.MAX_used = ',';
   document.write ("<scr"+"ipt type='text/javascript' src='"+m3_u);
   document.write ("?zoneid=31");
   document.write ('&amp;cb=' + m3_r);
   if (document.MAX_used != ',') document.write ("&amp;exclude=" + document.MAX_used);
   document.write (document.charset ? '&amp;charset='+document.charset : (document.characterSet ? '&amp;charset='+document.characterSet : ''));
   document.write ("&amp;loc=" + escape(window.location));
   if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
   if (document.context) document.write ("&context=" + escape(document.context));
   if (document.mmm_fo) document.write ("&amp;mmm_fo=1");
   document.write ("'><\/scr"+"ipt>");
//]]>-->
</script><noscript><a href='http://ads.vkushti.tv/new/www/delivery/ck.php?n=ad6dd6c3&amp;cb=INSERT_RANDOM_NUMBER_HERE' target='_blank'><img src='http://ads.vkushti.tv/new/www/delivery/avw.php?zoneid=31&amp;cb=INSERT_RANDOM_NUMBER_HERE&amp;n=ad6dd6c3' border='0' alt='' /></a></noscript>
        </div>

        <div class="second">
            <? include('template/banner_top.php'); ?>
        </div>
    </div> <!-- #banners-top -->
    
    <div id="banners-top-big">
<script type='text/javascript'><!--//<![CDATA[
   var m3_u = (location.protocol=='https:'?'https://ads.vkushti.tv/new/www/delivery/ajs.php':'http://ads.vkushti.tv/new/www/delivery/ajs.php');
   var m3_r = Math.floor(Math.random()*99999999999);
   if (!document.MAX_used) document.MAX_used = ',';
   document.write ("<scr"+"ipt type='text/javascript' src='"+m3_u);
   document.write ("?zoneid=32");
   document.write ('&amp;cb=' + m3_r);
   if (document.MAX_used != ',') document.write ("&amp;exclude=" + document.MAX_used);
   document.write (document.charset ? '&amp;charset='+document.charset : (document.characterSet ? '&amp;charset='+document.characterSet : ''));
   document.write ("&amp;loc=" + escape(window.location));
   if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
   if (document.context) document.write ("&context=" + escape(document.context));
   if (document.mmm_fo) document.write ("&amp;mmm_fo=1");
   document.write ("'><\/scr"+"ipt>");	
//]]>-->
</script><noscript><a href='http://ads.vkushti.tv/new/www/delivery/ck.php?n=a0e31d8f&amp;cb=INSERT_RANDOM_NUMBER_HERE' target='_blank'><img src='http://ads.vkushti.tv/new/www/delivery/avw.php?zoneid=32&amp;cb=INSERT_RANDOM_NUMBER_HERE&amp;n=a0e31d8f' border='0' alt='' /></a></noscript>    
    </div> <!-- #banners-top-big -->

    <div id="navigation">
        <?
            $bIsFirst = true;
            $rsPage = $oPage->ListAll(DEF_PAGE);
            
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
                        echo writeLink($row->Title, $row->PageID, 0, "class='".$class."'");
                        $bIsFirst = false;
                    }
                }
            }
        ?>
        <a href="http://programata.bg/tv/" class="tv" title="<?=getLabel('strTV')?>" target="_blank"><?=getLabel('strTV')?></a>
    </div> <!-- #navigation -->
        
    <div id="menu" <?php if(in_array($page,$difference_pages)){ echo 'style="height: 0px; min-height: 0px;"'; }?>>
            
			   	<?
			   	
			   	if(!in_array($page, $difference_pages)){
					if ($page != DEF_PAGE){
						echo '<div class="links">';	
						include('template/menu.php');
						include('template/basic.php');
						echo '
								'. $basic_php['title'] .'
							</div>'. $basic_php['accent'];

					}else{
						
						if(is_array($prepare_accents) && count($prepare_accents) > 0){
							echo '<div id="accents">';
								
							    $find_first = 1;
							    // '. ($find_first != 1 ? 'style="visibility: hidden;"' : '') .'
							    echo '<div class="slider">
							    		<div id="next"></div>
							    		<div id="previus"></div>
							    	  ';
								foreach($prepare_accents as $key => $accent){
						            
									if(is_array($accent)){
										echo implode(' ', $accent);
									}else{
										echo $accent;
									}

									
									++$find_first;
								}
																
								echo '</div>';
								
							echo '<ol>';
							
							foreach($_section_menu as $key => $section_name){
								foreach($section_name as $section){
									echo $section;
								}
							}
							echo '</ol>
									 </div> <!-- #accents -->';
						}
					}
			   	}
			   	
				?>
				
		<?php if(!in_array($page,$difference_pages)){ ?>		
        <div id="search">
            <?php include('template/search_quick.php'); 
            	@eval('$cache_file_to_load = '. file_get_contents(dirname(__FILE__) .'/sinoptik.txt') .';');
            	
            	if(is_array($cache_file_to_load) && isset($cache_file_to_load[$city])){
		            echo '<div class="weather">
		                    <div class="today">
		                    <em>'. $cache_file_to_load[$city]['today']['image'] .'</em>
		                    <span id="min">'. $cache_file_to_load[$city]['today']['min'] .'</span>
		                    <span id="max">'. $cache_file_to_load[$city]['today']['max'] .'</span>
		                    <strong>'. getLabel('strWeatherToday') .'</strong>
		              	  </div>
		                   <div class="tomorrow">
		                    <em>'. $cache_file_to_load[$city]['tomorrow']['image'] .'</em>		                   
		                    <span id="min">'. $cache_file_to_load[$city]['tomorrow']['min'] .'</span>
		                    <span id="max">'. $cache_file_to_load[$city]['tomorrow']['max'] .'</span>
		                    <strong>'. getLabel('strWeatherTomorrow') .'</strong>
		              	  </div>
		              	 <a href="http://sinoptik.bg" title="Sinoptik.bg" id="sinoptik_logo" target="_blank">
		              	 	<img src="public/images/sinoptik.png" alt="SINOPTIK.BG"/>
		              	 </a>
		            </div>';
            	}
			?>
            <div class="banner">
                <? include('template/banner_right.php'); ?>
            </div>
         <?php
			
         	if(!in_array($page,array_merge($top_navigation, array(1)))){
         		$cache = dirname(__FILE__) .'/vkushti_tv_cache.txt';
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
         	}
         
         ?>
         <div class="clear"><br class="clear"/></div>
        </div> <!-- #search -->
		<?php } ?>
		
        <br class="clear" />
    </div> <!-- #menu -->

    
<div id="content">
    <?
        if ($page != DEF_PAGE)
        {
        	
            if (ARG_ACT !=ACT_SAVE && ARG_ACT !=ACT_SEND)
            {
            	#d($basic_php);
				#d($rCurrentPage);
            	#	d($rCurrentPage->TemplateFile);
                if (!empty($rCurrentPage->TemplateFile))
                {
                    if (is_file('template/'.$rCurrentPage->TemplateFile.'.php'))
                    {
                        include_once('template/'.$rCurrentPage->TemplateFile.'.php');
                        if($_SERVER['REMOTE_ADDR'] == '78.142.42.106'){                
                       	   d('template/'.$rCurrentPage->TemplateFile.'.php');
                        } 
                       # d(get_included_files());
                    }
                }else{
                	include('template/basic.php');
                	echo '<h1 id="basic_title">'. strip_tags($basic_php['title']) .'</h1><hr id="basic_line"/>';
	                echo '<div class="basic">';

	                	if(isset($basic_php['about_us_menu'])){
	                		echo '<ul id="about_us_menu">';
	                		foreach($basic_php['about_us_menu'] as $menu){
	                			if(preg_match('/\?p='. $_GET['p'] .'.*/ismU',$menu)){
	                				echo '<li class="active">'. $menu .'</li>';
	                			}else{
	                				echo '<li>'. $menu .'</li>';
	                			}
	                		}
	                		echo '</ul><br /><br />';
	                	}
	                	echo $basic_php['desc'];
                	echo '</div>';
                }
            }
        }
        else {
           include('template/welcome.php'); 
        }
    ?>
</div>
    
<div id="footer-banners"> 
    <? include('template/banner_bottom.php'); ?>
</div> <!-- #footer-banners -->

<div id="footer">
    
    <div>
        <address>&copy; 2002-<? echo date("Y"); ?> <?=getLabel('strCopy')?></address>

        <ul>
            <?
            $bIsFirst = true;
            $rsPage = $oPage->ListByIDs($aSysNavigation);
            $get_first = 0;
            while($row = mysql_fetch_object($rsPage))
            {
            		++$get_first;
                    echo '<li'. ($get_first == 1 ? ' class="first"' : '').'>'.writeLink($row->Title, $row->PageID).'</li>';
                    $bIsFirst = false;
            }
            ?>
        </ul>
    </div>

    <a href="http://www.studiox.bg/" target="_blank" class="studiox">Design: StudioX.bg</a>
</div>
<!--<div id="author" style="display: none;"><?=getLabel('strPixel')?>&nbsp;&nbsp;&nbsp;<?=getLabel('strICN')?></div>-->

<script type="text/javascript">isTop();</script>
<script type="text/javascript">
//<![CDATA[
/* Replacement calls. */
if(typeof sIFR == "function")
{
	sIFR.replaceElement(named({sSelector:"h2", sFlashSrc:"sifr/pro_titles.swf", sColor:"<?=$sTitleColor?>", sLinkColor:"<?=$sTitleColor?>", sBgColor:null, sHoverColor:"<?=$sTitleColor?>", nPaddingTop:0, nPaddingBottom:0, sFlashVars:null, sWmode:"transparent"}));
	sIFR.replaceElement(named({sSelector:"h4", sFlashSrc:"sifr/pro_titles.swf", sColor:"<?=$sTitleColor?>", sLinkColor:"<?=$sTitleColor?>", sBgColor:null, sHoverColor:"<?=$sTitleColor?>", nPaddingTop:0, nPaddingBottom:0, sFlashVars:null, sWmode:"transparent"}));
};
//]]>
</script>

<div id="stats">
    <?include('analytics.html');?>
	<?include('tyxo.html');?>
    <?include('counter.html');?>
</div>


</div>
    
<!--[if IE 6]>
    <script type="text/javascript">
        setTimeout( function() {
            DD_belatedPNG.fix('.fixpng, #search form ul li .input, #search form ul li .input em, #search form ul .btn a, .jcarousel-prev, .jcarousel-next, #accents ol a');
        }, 10);
    </script>
<![endif]-->

<div id="fb-root"></div>
<?php
	$set_fb_lang = ($lang == 1 ? 'bg_BG' : 'en_US');
?>
<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  var set_fb_lang = '<?php echo $set_fb_lang; ?>';
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/"+ set_fb_lang +"/all.js#xfbml=1&appId=149704515122822";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>


<!--/* OpenX Javascript Tag v2.8.7 */-->

<!--/*
  * The backup image section of this tag has been generated for use on a
  * non-SSL page. If this tag is to be placed on an SSL page, change the
  *   'http://ads.vkushti.tv/new/www/delivery/...'
  * to
  *   'https://ads.vkushti.tv/new/www/delivery/...'
  *
  * This noscript section of this tag only shows image banners. There
  * is no width or height in these banners, so if you want these tags to
  * allocate space for the ad before it shows, you will need to add this
  * information to the <img> tag.
  *
  * If you do not want to deal with the intricities of the noscript
  * section, delete the tag (from <noscript>... to </noscript>). On
  * average, the noscript tag is called from less than 1% of internet
  * users.
  */-->
<!-- TRANSITION PAGE OPENX CODE  -->
<script type='text/javascript'><!--//<![CDATA[
   var m3_u = (location.protocol=='https:'?'https://ads.vkushti.tv/new/www/delivery/ajs.php':'http://ads.vkushti.tv/new/www/delivery/ajs.php');
   var m3_r = Math.floor(Math.random()*99999999999);
   if (!document.MAX_used) document.MAX_used = ',';
   document.write ("<scr"+"ipt type='text/javascript' src='"+m3_u);
   document.write ("?zoneid=30");
   document.write ('&amp;cb=' + m3_r);
   if (document.MAX_used != ',') document.write ("&amp;exclude=" + document.MAX_used);
   document.write (document.charset ? '&amp;charset='+document.charset : (document.characterSet ? '&amp;charset='+document.characterSet : ''));
   document.write ("&amp;loc=" + escape(window.location));
   if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
   if (document.context) document.write ("&context=" + escape(document.context));
   if (document.mmm_fo) document.write ("&amp;mmm_fo=1");
   document.write ("'><\/scr"+"ipt>");
//]]>--></script><noscript><a href='http://ads.vkushti.tv/new/www/delivery/ck.php?n=a8854e86&amp;cb=INSERT_RANDOM_NUMBER_HERE' target='_blank'><img src='http://ads.vkushti.tv/new/www/delivery/avw.php?zoneid=30&amp;cb=INSERT_RANDOM_NUMBER_HERE&amp;n=a8854e86' border='0' alt='' /></a></noscript>

<!-- (C)2000-2012 Gemius SA - gemiusAudience / programata.bg / Home Page -->
<script type="text/javascript">
<!--//--><![CDATA[//><!--
var pp_gemius_identifier = new String('nGfrQkBt5EJ8AE4sElQva4XxroeFetuewrGwn0sdnJ..S7');
//--><!]]>
</script>
<script type="text/javascript" src="http://programata.bg/xgemius/xgemius.js"></script>


</body>
</html>
<?
    @mysql_free_result($result);
    @mysql_close($con);
    
    
    // cacheing data from sinoptik.bg
    $cache_file = dirname(__FILE__) .'/sinoptik.txt';
    $api = 'http://sinoptik.bg/api/xml_sanoma/ID/';
    
    $sinoptik_ids = array(
    						'100727011', // Sofia
    						'100728193', // Plovdiv
    						'100726050', // Varna
    						'100732770', // Burgas
    						'100726848' // Stara Zagora
    				);
    				
    $targets = array_combine(array_keys(getLabel('aCities')), $sinoptik_ids);				
 
    $cache_time = dirname(__FILE__) .'sinoptik_last_update.txt';
    $cache_update_time = 50; // minutes
    $cache_data = array();			
    
    if(!is_file($cache_file)){
    	file_put_contents($cache_file, null);
    }
    
    if(!is_file($cache_time)){
    	file_put_contents($cache_time, time());
    }
    
	$distance = (int)date('i', time() - (int)file_get_contents($cache_time));

	if($distance >= $cache_update_time){
		file_put_contents($cache_time, time()); 

	    // get data
	    foreach($targets as $city_id => $sinoptik_id){
	    	foreach(array(date('Y-m-d'), date('Y-m-d', strtotime('+1 days'))) as $day){
		    	$data = @simplexml_load_file(str_replace('ID',$sinoptik_id,$api) . $day);

		    	if(!is_object($data)){
		    		exit;
		    	}
		    	
		    	if(date('Y-m-d') == $day){
			    	$cache_data[$city_id]['today'] = array(
			    											  'image' => '<img src="'. $data->item->smallpic .'" alt="sinoptik.bg"/>',	
			    											  'min' => str_replace('C','',(string)$data->item->min),
			    											  'max' => str_replace('C','',(string)$data->item->max)	
			    										   );
		    	}else{
			    	$cache_data[$city_id]['tomorrow'] = array(
			    											  'image' => '<img src="'. $data->item->smallpic .'" alt="sinoptik.bg"/>',	
			    											  'min' => str_replace('C','',(string)$data->item->min),
			    											  'max' => str_replace('C','',(string)$data->item->max)	
			    										   );
		    	}
	    	}
	    }
	    
	    file_put_contents($cache_file, var_export($cache_data, true));
	}    
?> 