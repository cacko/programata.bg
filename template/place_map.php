<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
    $rMap = $oMap->GetByEntityID($item);
    if (!is_null($rMap))
    {
        
?>
    <h4><?=getLabel('strMap')?></h4>
    <div class="text"><iframe border="0" frameborder="no" framespacing="0" name="emaps" width="322" height="262" src="http://<?=SITE_URL?>/map/place_map.php5?<?=ARG_ID?>=<?=$item?>&amp;<?=ARG_LANG?>=<?=$lang?>"></iframe></div>
<?
    }
}
?>