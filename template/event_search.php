<?php
if (!$bInSite) die();
//=========================================================
$sSearchCriteria = '';
if (!isset($item) || empty($item))
{
    $aMusicStyle = $oLabel->ListAllAsArray(GRP_MUSIC);
    $aMovieGenres = $oLabel->ListAllAsArray(GRP_MOVIE);
    $aPerformanceGenres = $oLabel->ListAllAsArray(GRP_PERF);
    $aExhibitionGenres = $oLabel->ListAllAsArray(GRP_EXHIB);
    $aArtistGenres = $oLabel->ListAllAsArray(GRP_ARTIST);
    $aOrigLanguages = $oLabel->ListAllAsArray(GRP_LANG);
    $aTranslations = $oLabel->ListAllAsArray(GRP_TRANS);
    $aCities = getLabel('aCities');
    $aPages = $oPage->ListAllAsArraySimple($nRootPage, '', 'event_list');
    
    $sSearchCriteria .= '<br />'.getLabel('strSearchResults').':<br />';
    $sKeyword = htmlspecialchars(strip_tags(getPostedArg('keyword', '')));
    if (!empty($sKeyword))
        $sSearchCriteria .= getLabel('strKeyword').': '.$sKeyword.'<br />';

    $nSelPage = htmlspecialchars(strip_tags(getPostedArg('sel_page')));
    if (!empty($nSelPage))
        $sSearchCriteria .= getLabel('strPage').': '.$aPages[$nSelPage].'<br />';
    $nSelCity = htmlspecialchars(strip_tags(getPostedArg('sel_city', $city)));
    if (!empty($nSelCity))
        $sSearchCriteria .= getLabel('strCity').': '.$aCities[$nSelCity].'<br />';
        
    $dSelStartDate = getPostedArg('sel_start_date');
    if (!empty($dSelStartDate))
        $sSearchCriteria .= getLabel('strStartDate').': '.$dSelStartDate.'<br />';
    $dSelEndDate = getPostedArg('sel_end_date');
    if (!empty($dSelEndDate))
        $sSearchCriteria .= getLabel('strEndDate').': '.$dSelEndDate.'<br />';
?>
<script type="text/javascript">
<!--
function fCheck(frm)
{
    //if (!valEmpty("keyword", "<?=getLabel('strEnter').getLabel('strKeyword')?>")) return false;
    //if (!valOption("sel_page", "<?=getLabel('strSelect').getLabel('strPage')?>")) return false;
    //if (!valOption("sel_city", "<?=getLabel('strSelect').getLabel('strCity')?>")) return false;
    return true;
}
//-->
</script>
<script type="text/javascript" src="js/calendar.js"></script>
<div class="formbox">
<form method="post" action="<?=setPage($page, 0, 0, ACT_SEND)?>" name="feedback" onsubmit="return fCheck(this);">
<h6><?=getLabel('strFilterCriteria')?></h6>
<br />
<div>
    <label for="keyword"><?=getLabel('strKeyword')?></label><br />
    <input type="text" name="keyword" id="keyword" maxlength="255" class="fldfilter" value="<?=$sKeyword?>" />
</div>
<div>
    <label for="sel_page"><?=getLabel('strPage')?><?//formatVal()?></label><br />
    <select name="sel_page" id="sel_page" class="fldfilter">
        <option value=""><?=getLabel('strAny')?></option>
    <?
        foreach($aPages as $key=>$val)
        {
            echo '<option value="'.$key.'"'.IIF($key==$nSelPage, ' selected="selected"', '').'>'.$val.'</option>'."\n";
        }
    ?>
    </select>
</div>
<div>
    <label for="sel_city"><?=getLabel('strCity')?><?//formatVal()?></label><br />
    <select name="sel_city" id="sel_city" class="fldfilter">
        <option value=""><?=getLabel('strAny')?></option>
    <?
        foreach($aCities as $key=>$val)
        {
            echo '<option value="'.$key.'"'.IIF($key==$nSelCity, ' selected="selected"', '').'>'.$val.'</option>'."\n";
        }
    ?>
    </select>
</div>
<br class="clear" /><br />
<? if ($nRootPage == 24) {?>
<div>
    <label for="music_style"><?=getLabel('strMusicStyle')?></label><br />
    <select name="music_style" id="music_style" class="fldfilter">
        <option value=""><?=getLabel('strAny')?></option>
    <?
        $nMusicStyle = htmlspecialchars(strip_tags(getPostedArg('music_style', 0)));
        foreach($aMusicStyle as $key=>$val)
        {
            echo '<option value="'.$key.'"'.IIF($nMusicStyle == $key, ' selected="selected"', '').'>'.$val.'</option>'."\n";
        }
        if (!empty($nMusicStyle))
            $sSearchCriteria .= getLabel('strMusicStyle').': '.$aMusicStyle[$nMusicStyle].'<br />';
    ?>
    </select>
</div>
<? } elseif ($nRootPage != 25 && $nRootPage != 28) { ?>
<div>
    <label for="genre"><?=getLabel('strGenre')?></label><br />
    <select name="genre" id="genre" class="fldfilter">
        <option value=""><?=getLabel('strAny')?></option>
    <?
        switch($nRootPage)
        {
            case 21:
                $aEventGenres = $aMovieGenres;
                break;
            case 22:
                $aEventGenres = $aPerformanceGenres;
                break;
            case 24:
                $aEventGenres = $aArtistGenres;
                break;
            /*case 25:
                $aEventGenres = $aExhibitionGenres;
                break;*/
        }
        $nGenre = htmlspecialchars(strip_tags(getPostedArg('genre', 0)));
        foreach($aEventGenres as $key=>$val)
        {
            echo '<option value="'.$key.'"'.IIF($nGenre == $key, ' selected="selected"', '').'>'.$val.'</option>'."\n";
        }
        if (!empty($nGenre))
            $sSearchCriteria .= getLabel('strGenre').': '.$aEventGenres[$nGenre].'<br />';
    ?>
    </select>
</div>
<? }
    if ($nRootPage == 21) {?>
<div>
    <label for="orig_lang"><?=getLabel('strOrigLanguage')?></label><br />
    <select name="orig_lang" id="orig_lang" class="fldfilter">
        <option value=""><?=getLabel('strAny')?></option>
    <?
        $nOrigLanguage = htmlspecialchars(strip_tags(getPostedArg('orig_lang', 0)));
        foreach($aOrigLanguages as $key=>$val)
        {
            echo '<option value="'.$key.'"'.IIF($nOrigLanguage == $key, ' selected="selected"', '').'>'.$val.'</option>'."\n";
        }
        if (!empty($nOrigLanguage))
            $sSearchCriteria .= getLabel('strOrigLanguage').': '.$aOrigLanguages[$nOrigLanguage].'<br />';
    ?>
    </select>
</div>
<div>
    <label for="translation"><?=getLabel('strTranslation')?></label><br />
    <select name="translation" id="translation" class="fldfilter">
        <option value=""><?=getLabel('strAny')?></option>
    <?
        $nTranslation = htmlspecialchars(strip_tags(getPostedArg('translation', 0)));
        foreach($aTranslations as $key=>$val)
        {
            echo '<option value="'.$key.'"'.IIF($nTranslation == $key, ' selected="selected"', '').'>'.$val.'</option>'."\n";
        }
        if (!empty($nTranslation))
            $sSearchCriteria .= getLabel('strTranslation').': '.$aTranslations[$nTranslation].'<br />';
    ?>
    </select>
</div>
<? } ?>
<br class="clear" /><br />
<div>
    <label for="sel_start_date"><?=getLabel('strStartDate')?></label><br />
    <input type="text" name="sel_start_date" id="sel_start_date" maxlength="10" class="fldfilter"
	       value="<?=getPostedArg('sel_start_date');?>" 
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" />
</div>
<div>
    <label for="sel_end_date"><?=getLabel('strEndDate')?></label><br />
    <input type="text" name="sel_end_date" id="sel_end_date" maxlength="10" class="fldfilter"
	       value="<?=getPostedArg('sel_end_date');?>" 
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" />
</div>
<? if ($nRootPage != 25) {?>
<div>
    <label for="sel_timespan"><?=getLabel('strStartTime')?></label><br />
    <select name="sel_timespan" id="sel_timespan" class="fldfilter">
	<option value=""><?=getLabel('strAnyTime')?></option>
    <?
        $dSelTime = getPostedArg('sel_timespan');
        foreach($aTimes as $key=>$val)
        {
                echo '<option value="'.$key.'"'.IIF($dSelTime==$key, ' selected="selected"','').'>'.$val.'</option>'."\n";
        }
        if (!empty($dSelTime))
            $sSearchCriteria .= getLabel('strStartTime').': '.$aTimes[$dSelTime].'<br />';
    ?>
    </select>
</div>
<? } else { ?>
<div>
    <label for="genre"><?=getLabel('strExhibitionGenre')?></label><br />
    <select name="genre" id="genre" class="fldfilter">
        <option value=""><?=getLabel('strAny')?></option>
    <?
        $nGenre = htmlspecialchars(strip_tags(getPostedArg('genre', 0)));
        $aEventGenres = $aExhibitionGenres;
        foreach($aEventGenres as $key=>$val)
        {
            echo '<option value="'.$key.'"'.IIF($nGenre==$key, ' selected="selected"','').'>'.$val.'</option>'."\n";
        }
        if (!empty($nGenre))
            $sSearchCriteria .= getLabel('strExhibitionGenre').': '.$aEventGenres[$nGenre].'<br />';
    ?>
    </select>
</div>
<? } ?>
<br class="clear" />
<br />
<input type="submit" value="<?=getLabel('strSearch')?>" class="btn" />
</form>
</div>
<?
    include_once('template/banner_middle.php');
}
    switch($action)
    {
        case ACT_SEARCH:
            // && empty($nSelCity) && empty($nSelPage)
            //if (empty($sKeyword) && empty($nMusicStyle)) {} // do nothing
            //else
            /*$rsEvent = $oEvent->ListAllAdvanced($nSelPage, $nSelCity, null, null, $sKeyword, '', 
                                                $nCuisine, $nAtmosphere, $nPriceCategory, $nMusicStyle, $bIsNew,
                                                $bHasEntranceFee, $bHasCardPayment, $bHasFaceControl, $bHasParking,
                                                $bHasDJ, $bHasLiveMusic, $bHasKaraoke, $bHasBgndMusic,
                                                $bHasCuisine, $bHasTerrace, $bHasClima, $bHasWardrobe, $bHasWifi, 
                                                false, 0, $aContext);*/
            break;
        default:
            //
            break;   
    }
    if (is_array($_POST) && count($_POST)>0)
    {
        //echo $sSearchCriteria;
        include_once('template/event_list.php');
    }
?>