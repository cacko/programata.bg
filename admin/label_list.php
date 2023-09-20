<?php
if (!$bInSite) die();
//=========================================================
switch($action)
{
	case ACT_ON:
		if (isset($item) && !empty($item))
		{
			$rs = $oLabel->UpdateHidden($item, B_FALSE);
		}
		break;
	case ACT_OFF:
		if (isset($item) && !empty($item))
		{
			$rs = $oLabel->UpdateHidden($item, B_TRUE);
		}
		break;
	/*case ACT_UP:
		if (isset($item) && !empty($item))
		{
			$row = $oLabel->MoveUp($item); // get by id
		}
		break;
	case ACT_DOWN:
		if (isset($item) && !empty($item))
		{
			$row = $oLabel->MoveDown($item); // get by id
		}
		break;*/
	default:
		//
		break;
}
//=========================================================
$aLabels = $oLabel->ListAllAsArray(null, '', true);
$aParentLabels = $oLabel->ListAllParentsAsArray(true);
//=========================================================
?>
<form action="<?=setPage($page)?>" method="post" name="LabelFilter" id="LabelFilter">
<table summary="filter" class="form">
<tr>
	<td><label for="keyword"><?=getLabel('strKeyword')?></label><br />
	<input type="text" name="keyword" id="keyword" maxlength="64" class="fldfilter" value="<?=getRequestArg('keyword')?>" /></td>
	<td><label for="parent_label"><?=getLabel('strParentLabel')?></label><br />
	<select name="parent_label" id="parent_label" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aParentLabels as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('parent_label'))
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
	$aFilter = array('parent_label', 'keyword');
	
	$rs = $oLabel->ListAll(getRequestArg('parent_label',null), getRequestArg('keyword'), true, $aContext);
	if (mysql_num_rows($rs))
	{
?>
<table summary="data" class="grid">
<thead>
<tr>
	<!--td><a href="<?=setPageContext(setContext(4,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strSortOrder')?></a></td-->
	<td><a href="<?=setPageContext(setContext(1,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strLabelID')?></a></td>
	<td><a href="<?=setPageContext(setContext(2,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strParentLabel')?></a></td>
	<td><a href="<?=setPageContext(setContext(3,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strLabelName')?></a></td>
	<!-- <td><a href="<?=setPageContext(setContext(4,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strHide')?></a></td> 
	<td><a href="<?=setPageContext(setContext(6,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strLastUpdate')?></a></td>-->
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
				$sLabels = '';
				if (!empty($row->ParentLabelID))
					$sLabels = $aLabels[$row->ParentLabelID];
				else
					$sLabels = '<i>'.getLabel('strNone').'</i>';
?>
<tr class="<?=IIF($curRow%2==0, 'odd', 'even').IIF($row->IsHidden == true, ' hidden', '')?>" onmouseover="lt(this,1)" onmouseout="lt(this,0)">
	<!--td><a href="<?=setPage($page, 0, $row->LabelID, ACT_UP).setFilterParam($aFilter).keepContext()?>"><?=getLabel('up')?></a> | <a href="<?=setPage($page, 0, $row->LabelID, ACT_DOWN).setFilterParam($aFilter).keepContext()?>"><?=getLabel('down')?></a></td-->
	<td><?=$row->LabelID?></td>
	<td><?=$sLabels?></td>
	<td><strong><?=$row->Title?></strong></td>
	<!-- <td><?=$aYesNo[$row->IsHidden]?></td>
	<td><?=$row->LastUpdate?><br /><?=getLabel('strByUser').$aUsers[$row->LastUpdateUserID]?></td> -->
	<td><a href="<?=setPage($page, 0, $row->LabelID, ACT_VIEW).keepFilter().keepContext()?>"><?=getLabel('view')?></a> | <a href="<?=setPage($page, 0, $row->LabelID, ACT_EDIT).keepFilter().keepContext()?>"><?=getLabel('edit')?></a> | 
<? if (!empty($row->ParentLabelID) && !in_array($row->ParentLabelID, array(GRP_LANG, GRP_TRANS, GRP_MOVIE, GRP_PERF, GRP_ARTIST))) { ?>
		<a href="<?=setPage($page, 0, $row->LabelID, ACT_DELETE).keepFilter().keepContext()?>" onclick="return confMsg('<?=getLabel('strDeleteQ')?>');return false;"><?=getLabel('delete')?></a> | 
<? } ?>
<? if ($row->IsHidden == B_TRUE) { ?>
		<a href="<?=setPage($page, 0, $row->LabelID, ACT_ON).keepFilter().keepContext()?>"><?=getLabel('show')?></a>
<? } else {?>
		<a href="<?=setPage($page, 0, $row->LabelID, ACT_OFF).keepFilter().keepContext()?>"><?=getLabel('hide')?></a>
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
	<td colspan="2"><?=showPaging($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_ADMIN, $aFilter);?></td>
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