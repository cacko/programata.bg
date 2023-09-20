<?php
if (!$bInSite) die();
//=========================================================
$sSearchCriteria = '';
if (!isset($item) || empty($item))
{
    $aCuisine = $oLabel->ListAllAsArray(GRP_CUISINE);
    $aAtmosphere = $oLabel->ListAllAsArray(GRP_ATMOS);
    $aPriceCategory = $oLabel->ListAllAsArray(GRP_PRICE);
    $aMusicStyle = $oLabel->ListAllAsArray(GRP_BGNDMUSIC);
    $aCities = getLabel('aCities');
    $aPages = $oPage->ListAllAsArraySimple($nRootPage, '', 'place_list');
    
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
    
    $nCuisine = htmlspecialchars(strip_tags(getPostedArg('cuisine', 0)));
    if (!empty($nCuisine))
        $sSearchCriteria .= getLabel('strCuisine').': '.$aCuisine[$nCuisine].'<br />';
    $nAtmosphere = htmlspecialchars(strip_tags(getPostedArg('atmosphere', 0)));
    if (!empty($nAtmosphere))
        $sSearchCriteria .= getLabel('strAtmosphere').': '.$aAtmosphere[$nAtmosphere].'<br />';
    $nPriceCategory = htmlspecialchars(strip_tags(getPostedArg('price_category', 0)));
    if (!empty($nPriceCategory))
        $sSearchCriteria .= getLabel('strPriceCategory').': '.$aPriceCategory[$nPriceCategory].'<br />';
    $nMusicStyle = htmlspecialchars(strip_tags(getPostedArg('music_style', 0)));
    if (!empty($nMusicStyle))
        $sSearchCriteria .= getLabel('strMusicStyle').': '.$aMusicStyle[$nMusicStyle].'<br />';
    
    $bIsNew = htmlspecialchars(strip_tags(getPostedArg('is_new', false)));
    if ($bIsNew)
        $sSearchCriteria .= getLabel('strNew').', ';
    $bHasWifi = htmlspecialchars(strip_tags(getPostedArg('has_wifi', false)));
    if ($bHasWifi)
        $sSearchCriteria .= getLabel('strWifi').', ';
    $bHasEntranceFee = htmlspecialchars(strip_tags(getPostedArg('has_entrance_fee', false)));
    if ($bHasEntranceFee)
        $sSearchCriteria .= getLabel('strEntranceFee').', ';
    $bHasCardPayment = htmlspecialchars(strip_tags(getPostedArg('has_card_payment', false)));
    if ($bHasCardPayment)
        $sSearchCriteria .= getLabel('strCardPayment').', ';
    $bHasFaceControl = htmlspecialchars(strip_tags(getPostedArg('has_face_control', false)));
    if ($bHasFaceControl)
        $sSearchCriteria .= getLabel('strFaceControl').', ';
    $bHasParking = htmlspecialchars(strip_tags(getPostedArg('has_parking', false)));
    if ($bHasParking)
        $sSearchCriteria .= getLabel('strParking').', ';
    $bHasDJ = htmlspecialchars(strip_tags(getPostedArg('has_dj', false)));
    if ($bHasDJ)
        $sSearchCriteria .= getLabel('strDJ').', ';
    $bHasLiveMusic = htmlspecialchars(strip_tags(getPostedArg('has_live_music', false)));
    if ($bHasLiveMusic)
        $sSearchCriteria .= getLabel('strLiveMusic').', ';
    $bHasKaraoke = htmlspecialchars(strip_tags(getPostedArg('has_karaoke', false)));
    if ($bHasKaraoke)
        $sSearchCriteria .= getLabel('strKaraoke').', ';
    //$bHasBgndMusic = htmlspecialchars(strip_tags(getPostedArg('has_bgnd_music', false)));
    //if ($bHasBgndMusic)
    //    $sSearchCriteria .= getLabel('strBgndMusic').', ';
    $bHasDelivery = htmlspecialchars(strip_tags(getPostedArg('has_delivery', false)));
    if ($bHasDelivery)
        $sSearchCriteria .= getLabel('strDelivery').', ';
    //$bHasCuisine = htmlspecialchars(strip_tags(getPostedArg('has_cuisine', false)));
    //if ($bHasCuisine)
    //    $sSearchCriteria .= getLabel('strCuisine').', ';
    $bHasTerrace = htmlspecialchars(strip_tags(getPostedArg('has_terrace', false)));
    if ($bHasTerrace)
        $sSearchCriteria .= getLabel('strTerrace').', ';
    $bHasClima = htmlspecialchars(strip_tags(getPostedArg('has_clima', false)));
    if ($bHasClima)
        $sSearchCriteria .= getLabel('strClima').', ';
    $bHasWardrobe = htmlspecialchars(strip_tags(getPostedArg('has_wardrobe', false)));
    if ($bHasWardrobe)
        $sSearchCriteria .= getLabel('strWardrobe').', ';
?>
<script type="text/javascript">
<!--
function fCheck(frm)
{
    //if (!valEmpty("keyword", "<?=getLabel('strEnter').getLabel('strKeyword')?>")) return false;
    if (!valOption("sel_page", "<?=getLabel('strSelect').getLabel('strPage')?>")) return false;
    if (!valOption("sel_city", "<?=getLabel('strSelect').getLabel('strCity')?>")) return false;
    return true;
}
//-->
</script>
<div class="formbox">
<form method="post" action="<?=setPage($page, 0, 0, ACT_SEARCH)?>" name="feedback" onsubmit="return fCheck(this);">
<h6><?=getLabel('strFilterCriteria')?></h6>
<br />
<div>
    <label for="keyword"><?=getLabel('strKeyword')?></label><br />
    <input type="text" name="keyword" id="keyword" maxlength="255" class="fldfilter" value="<?=$sKeyword?>" />
</div>
<div>
    <label for="sel_page"><?=getLabel('strPage')?><?=formatVal()?></label><br />
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
    <label for="sel_city"><?=getLabel('strCity')?><?=formatVal()?></label><br />
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
<? if ($nRootPage == 26) { ?>
<div>
    <label for="cuisine"><?=getLabel('strCuisine')?></label><br />
    <select name="cuisine" id="cuisine" class="fldfilter">
        <option value=""><?=getLabel('strAny')?></option>
    <?
        foreach($aCuisine as $key=>$val)
        {
            echo '<option value="'.$key.'"'.IIF($key==$nCuisine, ' selected="selected"', '').'>'.$val.'</option>'."\n";
        }
    ?>
    </select>
</div>
<div>
    <label for="atmosphere"><?=getLabel('strAtmosphere')?></label><br />
    <select name="atmosphere" id="atmosphere" class="fldfilter">
        <option value=""><?=getLabel('strAny')?></option>
    <?
        foreach($aAtmosphere as $key=>$val)
        {
            echo '<option value="'.$key.'"'.IIF($key==$nAtmosphere, ' selected="selected"', '').'>'.$val.'</option>'."\n";
        }
    ?>
    </select>
</div>
<div>
    <label for="music_style"><?=getLabel('strMusicStyle')?></label><br />
    <select name="music_style" id="music_style" class="fldfilter">
        <option value=""><?=getLabel('strAny')?></option>
    <?
        foreach($aMusicStyle as $key=>$val)
        {
            echo '<option value="'.$key.'"'.IIF($key==$nMusicStyle, ' selected="selected"', '').'>'.$val.'</option>'."\n";
        }
    ?>
    </select>
</div>
<br class="clear" /><br />
<div>
    <label for="price_category"><?=getLabel('strPriceCategory')?></label><br />
    <select name="price_category" id="price_category" class="fldfilter">
        <option value=""><?=getLabel('strAny')?></option>
    <?
        foreach($aPriceCategory as $key=>$val)
        {
            echo '<option value="'.$key.'"'.IIF($key==$nPriceCategory, ' selected="selected"', '').'>'.$val.'</option>'."\n";
        }
    ?>
    </select>
</div>
<div>
    <br />
    <input type="checkbox" id="is_new" name="is_new" value="1"<?=IIF($bIsNew != false, ' checked="checked"', '')?> />
    <label for="is_new"><?=getLabel('strNew')?></label><br />
</div>
<div>
    <br />
    <input type="checkbox" id="has_wifi" name="has_wifi" value="1"<?=IIF($bHasWifi != false, ' checked="checked"', '')?> />
    <label for="has_wifi"><?=getLabel('strWifi')?></label><br />
</div>
<br class="clear" /><br />
<div>
    <input type="checkbox" id="has_entrance_fee" name="has_entrance_fee" value="1"<?=IIF($bHasEntranceFee != false, ' checked="checked"', '')?> />
    <label for="has_entrance_fee"><?=getLabel('strEntranceFee')?></label><br />
    <input type="checkbox" id="has_card_payment" name="has_card_payment" value="1"<?=IIF($bHasCardPayment != false, ' checked="checked"', '')?> />
    <label for="has_card_payment"><?=getLabel('strCardPayment')?></label><br />
    <input type="checkbox" id="has_face_control" name="has_face_control" value="1"<?=IIF($bHasFaceControl != false, ' checked="checked"', '')?> />
    <label for="has_face_control"><?=getLabel('strFaceControl')?></label><br />
    <input type="checkbox" id="has_parking" name="has_parking" value="1"<?=IIF($bHasParking != false, ' checked="checked"', '')?> />
    <label for="has_parking"><?=getLabel('strParking')?></label><br />
</div>
<div>
    <input type="checkbox" id="has_dj" name="has_dj" value="1"<?=IIF($bHasDJ != false, ' checked="checked"', '')?> />
    <label for="has_dj"><?=getLabel('strDJ')?></label><br />
    <input type="checkbox" id="has_live_music" name="has_live_music" value="1"<?=IIF($bHasLiveMusic != false, ' checked="checked"', '')?> />
    <label for="has_live_music"><?=getLabel('strLiveMusic')?></label><br />
    <input type="checkbox" id="has_karaoke" name="has_karaoke" value="1"<?=IIF($bHasKaraoke != false, ' checked="checked"', '')?> />
    <label for="has_karaoke"><?=getLabel('strKaraoke')?></label><br />
    <!--input type="checkbox" id="has_bgnd_music" name="has_bgnd_music" value="1"<?//IIF($bHasBgndMusic != false, ' checked="checked"', '')?> />
    <label for="has_bgnd_music"><?=getLabel('strBgndMusic')?></label><br /-->
    <input type="checkbox" id="has_delivery" name="has_delivery" value="1"<?=IIF($bHasDelivery != false, ' checked="checked"', '')?> />
    <label for="has_delivery"><?=getLabel('strDelivery')?></label><br />
</div>
<div>
    <!--input type="checkbox" id="has_cuisine" name="has_cuisine" value="1"<?=IIF($bHasCuisine != false, ' checked="checked"', '')?> />
    <label for="has_cuisine"><?=getLabel('strCuisine')?></label><br /-->
    <input type="checkbox" id="has_terrace" name="has_terrace" value="1"<?=IIF($bHasTerrace != false, ' checked="checked"', '')?> />
    <label for="has_terrace"><?=getLabel('strTerrace')?></label><br />
    <input type="checkbox" id="has_clima" name="has_clima" value="1"<?=IIF($bHasClima != false, ' checked="checked"', '')?> />
    <label for="has_clima"><?=getLabel('strClima')?></label><br />
    <input type="checkbox" id="has_wardrobe" name="has_wardrobe" value="1"<?=IIF($bHasWardrobe != false, ' checked="checked"', '')?> />
    <label for="has_wardrobe"><?=getLabel('strWardrobe')?></label><br />
</div>
<br class="clear" /><br />
<? } ?>
<input type="submit" value="<?=getLabel('strSearch')?>" class="btn" />
<br class="clear" />
</form>
</div>
<?
    include_once('template/banner_middle.php');
}
    switch($action)
    {
        case ACT_SEARCH:
            // && empty($nSelCity) && empty($nSelPage)
            if (empty($sKeyword) && empty($nCuisine) && empty($nAtmosphere) && empty($nPriceCategory) && empty($nMusicStyle)
                 && !($bIsNew) && !($bHasEntranceFee) && !($bHasCardPayment) && !($bHasFaceControl) && !($bHasParking) && !($bHasDJ) && !($bHasLiveMusic)
                 && !($bHasKaraoke) && !($bHasBgndMusic) && !($bHasCuisine) && !($bHasTerrace) && !($bHasClima) && !($bHasWardrobe) && !($bHasWifi)) {} // do nothing
            else
            $rsPlace = $oPlace->ListAllAdvanced($nSelPage, $nSelCity, null, null, $sKeyword, '', 
                                                $nCuisine, $nAtmosphere, $nPriceCategory, $nMusicStyle, $bIsNew,
                                                $bHasEntranceFee, $bHasCardPayment, $bHasFaceControl, $bHasParking,
                                                $bHasDJ, $bHasLiveMusic, $bHasKaraoke, null, //$bHasBgndMusic
                                                $bHasCuisine, $bHasTerrace, $bHasClima, $bHasWardrobe, $bHasWifi, $bHasDelivery, 
                                                false, 0, $aContext);
            break;
        default:
            //
            break;   
    }

    if (is_array($_POST) && count($_POST)>0)
    {
        //echo $sSearchCriteria;
        include_once('template/place_list.php');
    }
?>