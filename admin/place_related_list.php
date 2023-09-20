<?php
$bInSite = true;
require_once('../initialize.php');
//=========================================================
define('SITES_ROOT','/raid/www/programata.bg');

function validateFiles($sPath, $sFilename, $mode=false) {
	$dir = SITES_ROOT.$sPath;
	// Open a known directory, and proceed to read its contents
	if (is_dir($dir))
	{
		if ($dh = opendir($dir))
		{
			
			while (($file = readdir($dh)) != false)
			{
				if (strpos($file, $sFilename) != false)
				{
					if ($mode==false)
						echo $file. "<br />";
					else
					{
						unlink($file);
					}
				}
			}
			closedir($dh);
		}
	}
}
$mode = getRequestArg('mode');
$relid = getRequestArg('relid');
$handle = getRequestArg('handle');
if (!empty($mode) && !empty($relid)  && !empty($handle))
{
	validateFiles($relid, $handle, IIF($mode == 'ala', true, false));
}
else
{
//=========================================================
$aPages = $oPage->ListAllAsArray(null, '', 'place_list', true);
$aCities = getLabel('aCities');
$aPlaceTypes = getLabel('aPlaceTypes');
//=========================================================
?>
<form action="<?=setPage($page, $cat)?>" method="post" name="PageFilter" id="PageFilter">
<table summary="filter" class="form">
<tr>
	<td><label for="keyword"><?=getLabel('strKeyword')?></label><br />
	<input type="text" name="keyword" id="keyword" maxlength="64" class="fldfilter" value="<?=getRequestArg('keyword')?>" /></td>
	<td><label for="parent_page"><?=getLabel('strParentPage')?></label><br />
	<select name="parent_page" id="parent_page" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aPages as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('parent_page'))
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
		</select></td>
</tr>
<tr>
	<td><label for="sel_city"><?=getLabel('strCity')?></label><br />
	<select name="sel_city" id="sel_city" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aCities as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('sel_city', $nDefCity))
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
		</select></td>
	<td><label for="place_type"><?=getLabel('strPlaceType')?></label><br />
	<select name="place_type" id="place_type" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aPlaceTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('place_type', $cat))
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
		</select></td>
	<td><br /><input type="submit" value="<?=getLabel('strSearch')?>" class="btnfilter" /></td>
</tr>
</table>
</form>
<?
//=========================================================
	$aAlphabet = getLabel('aAlphabet');
	
	$sLetter = '';
	$nLetter = getRequestArg('letter');
	if (!empty($nLetter))
		$sLetter = $aAlphabet[$nLetter];

	echo '<ul class="alpha">'."\n";
	foreach($aAlphabet as $key=>$letter)
	{
		echo '<li'.IIF(!empty($nLetter) && $nLetter==$key, ' class="on"', '').'><a href="'.setPage($page, $cat).'&amp;letter='.$key.'">'.$letter.'</a></li>'."\n";
	}
	echo '</ul>'."\n";
//=========================================================
	$aFilter = array('parent_page', 'keyword', 'sel_city', 'place_type', 'letter', ARG_CAT);
	
	$rs = $oPlace->ListAllHalls(getRequestArg('parent_page',null), getRequestArg('sel_city', $nDefCity),
			       getRequestArg('place_type',$cat), null, getRequestArg('keyword'), $sLetter, true, 0, $aContext);
	if (mysql_num_rows($rs))
	{
?>
<script type="text/javascript">
<!--
	function setPrimary(place_id, hall_id, place_title)
	{
		var nodePlaceID = parent.document.getElementById('place_id');
		var nodeHallID = parent.document.getElementById('place_hall_id');
		var nodePlaceTitle = parent.document.getElementById('place_title');
		if (nodePlaceID)
		{
			nodePlaceID.value = place_id;
			if (nodeHallID)
				nodeHallID.value = hall_id;
		}
		if (nodePlaceTitle)
		{
			nodePlaceTitle.innerHTML = place_title;
		}
	}
	
	function setSecondary(place_id, place_title)
	{
		var nodePlaceID = parent.document.getElementById('rel_place_id');
		var nodePlaceTitle = parent.document.getElementById('rel_place_title');
		if (nodePlaceID)
		{
			if (nodePlaceID.value != '')
				nodePlaceID.value += ',' ;
			nodePlaceID.value += place_id;
		}
		if (nodePlaceTitle)
		{
			nodePlaceTitle.innerHTML += place_title + '<br />';
		}
	}
// -->
</script>
<table summary="data" class="grid">
<thead>
<tr>
	<td></td>
	<td><a href="<?=setPageContext(setContext(1,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strPlaceID')?></a></td>
	<td><a href="<?=setPageContext(setContext(2,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strPlaceName')?></a></td>
	<td><?=getLabel('strCity')?></td>
	<td></td>
</tr>
</thead>
<tbody>
<?
		$aYesNo = getLabel('aYesNo');
		$aUsers = $oUser->ListAllAsArray(USER_ADMIN);
		
		$curRow = 0;
		$nTotalRows = mysql_num_rows($rs);
		$nStartRow = $aContext[CUR_REC];
		$nEndRow = $nStartRow + NUM_ROWS_ADMIN;
		while ($row = mysql_fetch_object($rs))
		{
			if ($curRow >= $nStartRow && $curRow < $nEndRow)
			{
				$sHallTitle = '';
				$nHallID = $row->HallID;
				if (!empty($nHallID))
				{
					$rHall = $oPlaceHall->GetByID($row->HallID);
					$sHallTitle = '<br /> - '.$rHall->Title;
				}
				else
					$nHallID = 0;
?>
<tr onmouseover="lt(this,1)" onmouseout="lt(this,0)"<?=IIF($row->IsHidden == true, ' class="hidden"', '')?>>
	<td><a href="#" onclick="setPrimary(<?=$row->PlaceID?>, <?=$nHallID?>, '<?=$row->Title.$sHallTitle?>');return false;"><?=getLabel('strSelectAsPrimary')?></a><br />
		<a href="#" onclick="setSecondary(<?=$row->PlaceID?>, '<?=$row->Title?>');return false;"><?=getLabel('strSelectAsSecondary')?></a></td>
	<td><?=$row->PlaceID?></td>
	<td><?=$row->Title.$sHallTitle?></td>
	<td><?=$aCities[$row->CityID]?></td>
	<td><a target="_blank" href="index.php<?=setPage($page, 0, $row->PlaceID, ACT_VIEW).keepFilter().keepContext()?>"><?=getLabel('view').getLabel('new_window')?></a></td>
</tr>
<?
			}
			$curRow++;
		}
?>
</tbody>
<tfoot>
<tr>
	<td colspan="3"><?=showPaging($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_ADMIN, $aFilter);?></td>
	<td colspan="2" align="right"><?=showPages($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_ADMIN, $aFilter);?></td>
</tr>
</tfoot>
</table>
<?
	}
	else
	{
?>
<div><?=getLabel('strNoRecords')?></div>
<?
	}
}
?>