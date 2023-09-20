<?php
if (!$bInSite) die();
//=========================================================
switch($action)
{
	case ACT_ON:
		if (isset($item) && !empty($item))
		{
			$rs = $oPlace->UpdateHidden($item, B_FALSE);
		}
		break;
	case ACT_OFF:
		if (isset($item) && !empty($item))
		{
			$rs = $oPlace->UpdateHidden($item, B_TRUE);
		}
		break;
	default:
		//
		break;
}
//=========================================================
$aPages = $oPage->ListAllAsArray(null, '', 'place_list', true);
$aPagesSimple = $oPage->ListAllAsArraySimple(null, '', 'place_list', true);
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
	
	$rs = $oPlace->ListAll(getRequestArg('parent_page',null), getRequestArg('sel_city', $nDefCity),
			       getRequestArg('place_type',$cat), null, getRequestArg('keyword'), $sLetter, false, true, 0, $aContext);
	if (mysql_num_rows($rs))
	{
?>
<table summary="data" class="grid">
<thead>
<tr>
	<td><?=getLabel('strPlaceImage')?></td>
	<td><a href="<?=setPageContext(setContext(1,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strPlaceID')?></a></td>
	<td><a href="<?=setPageContext(setContext(2,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strPlaceName')?></a> (<a href="<?=setPageContext(setContext(16,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strNrViews')?></a>)</td>
	<td><?=getLabel('strParentPage')?></td>
	<td><?=getLabel('strCity')?></td>
	<td><?=getLabel('strMap')?></td>
	<!--td><a href="<?=setPageContext(setContext(17,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strPlaceType')?></a></td-->
	<!--td><a href="<?=setPageContext(setContext(12,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strLastUpdate')?></a></td-->
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
				$sPages = '';
				$aRelPages = $oPlace->ListPlacePagesAsArray($row->PlaceID);
				if (count($aRelPages)>0)
				{
					foreach($aRelPages as $cat)
						$sPages .= $aPagesSimple[$cat].'<br/>';
				}
				else
					$sPages = '<span class="a"><em>'.getLabel('strNone').'</em></span>';
				
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_PLACE_THUMB.$row->PlaceID.'.'.EXT_IMG;
				if (!is_file($sMainImageFile))
				{
					$rsAttachment = $oAttachment->ListAll($row->PlaceID, $row->PlaceTypeID, 1); //ENT_PLACE
					while($rAttachment = mysql_fetch_object($rsAttachment))
					{
						$sMainImageFile = FILEBANK_DIR.$rAttachment->AttachmentID.'.'.EXT_IMG;//.$rAttachment->Extension;
					}
					//$sMainImageFile = '../'.UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
				}
				$bHasMap = B_FALSE;
				$rMap = $oMap->GetByEntityID($row->PlaceID);
				if (!is_null($rMap))
					$bHasMap = B_TRUE;
?>
<tr class="<?=IIF($curRow%2==0, 'odd', 'even').IIF($row->IsHidden == true, ' hidden', '')?>" onmouseover="lt(this,1)" onmouseout="lt(this,0)">
	<td><?=drawImage($sMainImageFile, W_IMG_THUMB/2)?></td>
	<td><?=$row->PlaceID?></td>
	<td><strong><?=$row->Title?></strong> (<?=$row->NrViews?>)</td>
	<td><?=$sPages?></td>
	<td><?=$aCities[$row->CityID]?></td>
	<td><?=$aYesNo[$bHasMap]?></td>
	<!--td><?=$aPlaceTypes[$row->PlaceTypeID]?></td-->
	<!--td><?=$row->LastUpdate?><br /><?=getLabel('strByUser').$aUsers[$row->LastUpdateUserID]?></td-->
	<td><a href="<?=setPage($page, 0, $row->PlaceID, ACT_VIEW).keepFilter().keepContext()?>"><?=getLabel('view')?></a> | <a href="<?=setPage($page, 0, $row->PlaceID, ACT_EDIT).keepFilter().keepContext()?>"><?=getLabel('edit')?></a> | <a href="<?=setPage($page, 0, $row->PlaceID, ACT_DELETE).keepFilter().keepContext()?>" onclick="return confMsg('<?=getLabel('strDeleteQ')?>');return false;"><?=getLabel('delete')?></a> |
<? if ($row->IsHidden == B_TRUE) { ?>
		<a href="<?=setPage($page, 0, $row->PlaceID, ACT_ON).keepFilter().keepContext()?>"><?=getLabel('show')?></a>
<? } else {?>
		<a href="<?=setPage($page, 0, $row->PlaceID, ACT_OFF).keepFilter().keepContext()?>"><?=getLabel('hide')?></a>
<? } ?>
	</td>
</tr>
<?
			}
			$curRow++;
		}
?>
</tbody>
<tfoot>
<tr>
	<td colspan="4"><?=showPaging($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_ADMIN, $aFilter);?></td>
	<td colspan="3" align="right"><?=showPages($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_ADMIN, $aFilter);?></td>
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
?>