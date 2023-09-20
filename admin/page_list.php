<?php
if (!$bInSite) die();
//=========================================================
switch($action)
{
	case ACT_UP:
		if (isset($item) && !empty($item))
		{
			$row = $oPage->MoveUp($item); // get by id
		}
		break;
	case ACT_DOWN:
		if (isset($item) && !empty($item))
		{
			$row = $oPage->MoveDown($item); // get by id
		}
		break;
	default:
		//
		break;
}

$aPages = $oPage->ListAllAsArray(null, '', '', true);
$aParentPages = $oPage->ListAllParentsAsArray(true);
//=========================================================
?>
<form action="<?=setPage($page)?>" method="post" name="PageFilter" id="PageFilter">
<table summary="filter" class="form">
<tr>
	<td><label for="keyword"><?=getLabel('strKeyword')?></label><br />
	<input type="text" name="keyword" id="keyword" maxlength="64" class="fldfilter" value="<?=getRequestArg('keyword')?>" /></td>
	<td><label for="parent_page"><?=getLabel('strParentPage')?></label><br />
	<select name="parent_page" id="parent_page" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aParentPages as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('parent_page'))
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
	$aFilter = array('parent_page', 'keyword');
	
	$rs = $oPage->ListAll(getRequestArg('parent_page',null), getRequestArg('keyword'),'', true, $aContext);
	if (mysql_num_rows($rs))
	{
?>
<table summary="data" class="grid">
<thead>
<tr>
	<td><a href="<?=setPageContext(setContext(4,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strSortOrder')?></a></td>
	<td><a href="<?=setPageContext(setContext(1,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strPageID')?></a></td>
	<td><a href="<?=setPageContext(setContext(3,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strPageName')?></a></td>
	<td><a href="<?=setPageContext(setContext(2,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strParentPage')?></a></td>
	<!-- <td><a href="<?=setPageContext(setContext(9,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strHide')?></a></td>>
	<td><a href="<?=setPageContext(setContext(12,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strLastUpdate')?></a></td -->
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
				if (!empty($row->ParentPageID))
					$sPages = $aPages[$row->ParentPageID];
				else
					$sPages = '<i>'.getLabel('strNone').'</i>';
?>
<tr class="<?=IIF($curRow%2==0, 'odd', 'even').IIF($row->IsHidden == true, ' hidden', '')?>" onmouseover="lt(this,1)" onmouseout="lt(this,0)">
	<td><a href="<?=setPage($page, 0, $row->PageID, ACT_UP).setFilterParam($aFilter).keepContext()?>"><?=getLabel('up')?></a> | <a href="<?=setPage($page, 0, $row->PageID, ACT_DOWN).setFilterParam($aFilter).keepContext()?>"><?=getLabel('down')?></a></td>
	<td><?=$row->PageID?></td>
	<td><strong><?=$row->Title?></strong></td>
	<td><?=$sPages?></td>
	<!-- <td><?=$aYesNo[$row->IsHidden]?></td>>
	<td><?=$row->LastUpdate?><br /><?=getLabel('strByUser').$aUsers[$row->LastUpdateUserID]?></td -->
	<td><a href="<?=setPage($page, 0, $row->PageID, ACT_VIEW).keepFilter().keepContext()?>"><?=getLabel('view')?></a> | <a href="<?=setPage($page, 0, $row->PageID, ACT_EDIT).keepFilter().keepContext()?>"><?=getLabel('edit')?></a>
	<?if ($row->IsRequired == B_FALSE) {?>
		 | <a href="<?=setPage($page, 0, $row->PageID, ACT_DELETE).keepFilter().keepContext()?>" onclick="return confMsg('<?=getLabel('strDeleteQ')?>');return false;"><?=getLabel('delete')?></a>
	<?}?></td>
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