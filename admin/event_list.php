<?php
if (!$bInSite) die();
//=========================================================
switch($action)
{
	case ACT_ON:
		if (isset($item) && !empty($item))
		{
			$rs = $oEvent->UpdateHidden($item, B_FALSE);
		}
		break;
	case ACT_OFF:
		if (isset($item) && !empty($item))
		{
			$rs = $oEvent->UpdateHidden($item, B_TRUE);
		}
		break;
	default:
		//
		break;
}
//=========================================================
$aPages = $oPage->ListAllAsArray(null, '', 'event_list', true);
$aEventTypes = getLabel('aEventTypes');
$aEventSubtypes = getLabel('aEventSubtypes');
//=========================================================
?>
<form action="<?=setPage($page, $cat)?>" method="post" name="PageFilter" id="PageFilter">
<table summary="filter" class="form">
<tr>
	<td><label for="keyword"><?=getLabel('strKeyword')?></label><br />
	<input type="text" name="keyword" id="keyword" maxlength="64" class="fldfilter" value="<?=getRequestArg('keyword')?>" /></td>
	<!--td><label for="parent_page"><?=getLabel('strParentPage')?></label><br />
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
		</select></td-->
	<td><label for="event_type"><?=getLabel('strEventType')?></label><br />
	<select name="event_type" id="event_type" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aEventTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('event_type', $cat))
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
	$aFilter = array('parent_page', 'keyword', 'event_type', 'letter', ARG_CAT);
	
	$rs = $oEvent->ListAll(getRequestArg('parent_page',null), getRequestArg('event_type',$cat), null,
			       getRequestArg('keyword'), $sLetter, true, 0, $aContext);
	if (mysql_num_rows($rs))
	{
?>
<table summary="data" class="grid">
<thead>
<tr>
	<td><?=getLabel('strEventImage')?></td>
	<td><a href="<?=setPageContext(setContext(1,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strEventID')?></a></td>
	<td><a href="<?=setPageContext(setContext(2,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strEventName')?></a> (<a href="<?=setPageContext(setContext(16,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strNrViews')?></a>)</td>
	<!--td><?=getLabel('strParentPage')?></td-->
	<!--td><a href="<?=setPageContext(setContext(17,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strEventType')?></td-->
	<!--td><a href="<?=setPageContext(setContext(18,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strEventSubtype')?></a></td-->
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
				/*$aRelPages = $oEvent->ListEventPagesAsArray($row->EventID);
				if (count($aRelPages)>0)
				{
					foreach($aRelPages as $cat)
						$sPages .= $aPages[$cat].'<br/>';
				}
				else
					$sPages = '<span class="a"><em>'.getLabel('strNone').'</em></span>';*/
				
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_EVENT_THUMB.$row->EventID.'.'.EXT_IMG;
				if (!is_file($sMainImageFile))
				{
					$rsAttachment = $oAttachment->ListAll($row->EventID, $row->EventTypeID, 1); //ENT_Event
					while($rAttachment = mysql_fetch_object($rsAttachment))
					{
						$sMainImageFile = FILEBANK_DIR.$rAttachment->AttachmentID.'.'.EXT_IMG;//.$rAttachment->Extension;
					}
					//$sMainImageFile = '../'.UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
				}
?>
<tr class="<?=IIF($curRow%2==0, 'odd', 'even').IIF($row->IsHidden == true, ' hidden', '')?>" onmouseover="lt(this,1)" onmouseout="lt(this,0)">
	<td><?=drawImage($sMainImageFile, W_IMG_THUMB/2)?></td>
	<td><?=$row->EventID?></td>
	<td><strong><?=$row->Title?></strong> (<?=$row->NrViews?>)</td>
	<!--td><?=$sPages?></td-->
	<!--td><?=$aEventTypes[$row->EventTypeID]?></td-->
	<!--td><?=$aEventSubtypes[$row->EventSubtypeID]?></td-->
	<!--td><?=$row->LastUpdate?><br /><?=getLabel('strByUser').$aUsers[$row->LastUpdateUserID]?></td-->
	<td><a href="<?=setPage($page, 0, $row->EventID, ACT_VIEW).keepFilter().keepContext()?>"><?=getLabel('view')?></a> | <a href="<?=setPage($page, 0, $row->EventID, ACT_EDIT).keepFilter().keepContext()?>"><?=getLabel('edit')?></a> | <a href="<?=setPage($page, 0, $row->EventID, ACT_DELETE).keepFilter().keepContext()?>" onclick="return confMsg('<?=getLabel('strDeleteQ')?>');return false;"><?=getLabel('delete')?></a> |
<? if ($row->IsHidden == B_TRUE) { ?>
		<a href="<?=setPage($page, 0, $row->EventID, ACT_ON).keepFilter().keepContext()?>"><?=getLabel('show')?></a>
<? } else {?>
		<a href="<?=setPage($page, 0, $row->EventID, ACT_OFF).keepFilter().keepContext()?>"><?=getLabel('hide')?></a>
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
?>