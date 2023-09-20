<?php
if (!$bInSite) die();
//=========================================================
    $sSearchCriteria = '';
    
    $aCuisine = $oLabel->ListAllAsArray(GRP_CUISINE);
    $aAtmosphere = $oLabel->ListAllAsArray(GRP_ATMOS);
    $aPriceCategory = $oLabel->ListAllAsArray(GRP_PRICE);
    $aMusicStyle = $oLabel->ListAllAsArray(GRP_BGNDMUSIC);
    $aCities = getLabel('aCities');
    
    $sSearchCriteria .= '<br />'.getLabel('strSearchResults').':<br />';
    $sKeyword = htmlspecialchars(strip_tags(getPostedArg('keyword', '')));
    if (!empty($sKeyword))
        $sSearchCriteria .= getLabel('strKeyword').': '.$sKeyword.'<br />';
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
?>
<script type="text/javascript">
<!--
function fCheck(frm)
{
    //if (!valEmpty("keyword", "<?=getLabel('strEnter').getLabel('strKeyword')?>")) return false;
    //if (!valOption("city", "<?=getLabel('strSelect').getLabel('strCity')?>")) return false;
    return true;
}
//-->
</script>
<div class="formbox">
<form method="post" action="<?=setPage($page, 0, 0, ACT_SEARCH)?>" name="feedback" onsubmit="return fCheck(this);">
<h6><?=getLabel('strFilterCriteria')?></h6>
<br />
<div>
    <label for="keyword"><?=getLabel('strKeyword')?><?=formatVal()?></label><br />
    <input type="text" name="keyword" id="keyword" maxlength="255" class="fldfilter" value="<?=$sKeyword?>" />
</div>
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
<div>
    <br />
    <input type="submit" value="<?=getLabel('strSearch')?>" class="btn" />
</div>
<br class="clear" />
<!--br />
<input type="submit" value="<?=getLabel('strSearch')?>" class="btn" /-->
</form>
<br />
<div class="hr"></div>
<br />
<h6><?=getLabel('strFilterAlphabet')?></h6>
<?
    //$nReqLetter = getRequestArg('alpha', 0);
    $aAlphabetGroups = getLabel('aAlphabetGroups');
    echo '<ul class="alpha">'."\n";
    foreach($aAlphabetGroups as $k=>$v)
    {
            echo '<li'.IIF($k==$nReqLetter, ' class="on"', '').'><a href="'.setPage($page, $cat).'&amp;alpha='.$k.'">'.$v.'</a></li>'."\n";
    }
    echo '</ul>'."\n";
?>
</div>
<?
    include_once('template/banner_middle.php');
?>