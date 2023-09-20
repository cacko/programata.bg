<?php
if (!$bInSite) die();
//=========================================================
    $sSearchCriteria = '';
    
    $aMusicStyle = $oLabel->ListAllAsArray(GRP_MUSIC);
    $aMovieGenres = $oLabel->ListAllAsArray(GRP_MOVIE);
    $aPerformanceGenres = $oLabel->ListAllAsArray(GRP_PERF);
    $aExhibitionGenres = $oLabel->ListAllAsArray(GRP_EXHIB);
    $aArtistGenres = $oLabel->ListAllAsArray(GRP_ARTIST);
    $aOrigLanguages = $oLabel->ListAllAsArray(GRP_LANG);
    $aTranslations = $oLabel->ListAllAsArray(GRP_TRANS);
    
    $sSearchCriteria .= '<br />'.getLabel('strSearchResults').':<br />';
    $sKeyword = htmlspecialchars(strip_tags(getPostedArg('keyword', '')));
    if (!empty($sKeyword))
        $sSearchCriteria .= getLabel('strKeyword').': '.$sKeyword.'<br />';
    /*$nMusicStyle = htmlspecialchars(strip_tags(getPostedArg('music_style', 0)));
    $nGenre = htmlspecialchars(strip_tags(getPostedArg('genre', 0)));
    $nOrigLanguage = htmlspecialchars(strip_tags(getPostedArg('orig_lang', 0)));
    $nTranslation = htmlspecialchars(strip_tags(getPostedArg('translation', 0)));*/
    
    if (!empty($dStartDate))
        $sSearchCriteria .= getLabel('strStartDate').': '.formatDate($dStartDate).'<br />';
    if (!empty($dEndDate))
        $sSearchCriteria .= getLabel('strEndDate').': '.formatDate($dEndDate).'<br />';
    $dSelTime = getPostedArg('sel_timespan');
    if (!empty($dSelTime))
            $sSearchCriteria .= getLabel('strStartTime').': '.$aTimes[$dSelTime].'<br />';
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
<form method="post" action="<?=setPage($page, 0, 0, ACT_SEND)?>" name="feedback" onsubmit="return fCheck(this);">
<h6><?=getLabel('strFilterCriteria')?></h6>
<br />
<div>
    <label for="keyword"><?=getLabel('strKeyword')?><?=formatVal()?></label><br />
    <input type="text" name="keyword" id="keyword" maxlength="255" class="fldfilter" value="<?=$sKeyword?>" />
</div>
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
<? } elseif ($nRootPage == 25) { ?>
<div>
    <label for="genre"><?=getLabel('strExhibitionGenre')?></label><br />
    <select name="genre" id="genre" class="fldfilter">
        <option value=""><?=getLabel('strAny')?></option>
    <?
        $aEventGenres = $aExhibitionGenres;
        $nGenre = htmlspecialchars(strip_tags(getPostedArg('genre', 0)));
        foreach($aEventGenres as $key=>$val)
        {
            echo '<option value="'.$key.'"'.IIF($key==$nGenre, ' selected="selected"', '').'>'.$val.'</option>'."\n";
        }
        if (!empty($nGenre))
            $sSearchCriteria .= getLabel('strExhibitionGenre').': '.$aEventGenres[$nGenre].'<br />';
    ?>
    </select>
</div>
<?  }
    elseif ($nRootPage != 28)
    {
        $sGenreTitle = getLabel('strGenre');
        switch($nRootPage)
        {
            case 21:
                $aEventGenres = $aMovieGenres;
                break;
            case 22:
                $aEventGenres = $aPerformanceGenres;
                $sGenreTitle = getLabel('strType');
                break;
            case 24:
                $aEventGenres = $aArtistGenres;
                break;
            //case 25:
            //    $aEventGenres = $aExhibitionGenres;
            //    break;
        }
?>
<div>
    <label for="genre"><?=$sGenreTitle?></label><br />
    <select name="genre" id="genre" class="fldfilter">
        <option value=""><?=getLabel('strAny')?></option>
    <?
        $nGenre = htmlspecialchars(strip_tags(getPostedArg('genre', 0)));
        foreach($aEventGenres as $key=>$val)
        {
            echo '<option value="'.$key.'"'.IIF($key==$nGenre, ' selected="selected"', '').'>'.$val.'</option>'."\n";
        }
        if (!empty($nGenre))
            $sSearchCriteria .= $sGenreTitle.': '.$aEventGenres[$nGenre].'<br />';
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
<br class="clear" /><br />
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


<?
    $nMode = 2;
    if ($page == 55) // party time
    {
?>
<div style="float:right">
<h6><?=getLabel('strFilterOrder')?></h6>
    <?
        $nMode = getRequestArg('m', 2);
        if (!is_numeric($nMode)) $nMode = 2;
        $aFilterOrder = getLabel('aFilterOrders');
        echo '<ul class="alpha">'."\n";
        foreach($aFilterOrder as $k=>$v)
        {
                echo '<li'.IIF($k==$nMode, ' class="on"', '').'><a href="'.setPage($page, $cat).'&amp;m='.$k.'">'.$v.'</a></li>'."\n";
        }
        echo '</ul>'."\n";
    ?>
</div>
<?
    }
?>


<h6><?=getLabel('strFilterAlphabet')?></h6>
<?
    //$nReqLetter = getRequestArg('alpha', 0);
    $aAlphabetGroups = getLabel('aAlphabetGroups');
    echo '<ul class="alpha">'."\n";
    foreach($aAlphabetGroups as $k=>$v)
    {
            echo '<li'.IIF($k==$nReqLetter && $nMode == 2, ' class="on"', '').'><a href="'.setPage($page, $cat).'&amp;m=2&amp;alpha='.$k.'">'.$v.'</a></li>'."\n";
    }
    echo '</ul>'."\n";
?>
</div>
<?
    include_once('template/banner_middle.php');
?>