<?php
if (!$bInSite) die();
//=========================================================
switch($action)
{
	case ACT_ON:
		if (isset($item) && !empty($item))
		{
			$rs = $oComment->UpdateHidden($item, B_FALSE);
		}
		break;
	case ACT_OFF:
		if (isset($item) && !empty($item))
		{
			$rs = $oComment->UpdateHidden($item, B_TRUE);
		}
		break;
	default:
		//
		break;
}
//=========================================================
$aYesNo = getLabel('aYesNo');
$aPromoEntityTypes = getLabel('aPromoEntityTypes');
?>
<form action="<?=setPage($page)?>" method="post" name="PageFilter" id="PageFilter">
<table summary="filter" class="form">
<tr>
	<td><label for="keyword"><?=getLabel('strKeyword')?></label><br />
	<input type="text" name="keyword" id="keyword" maxlength="64" class="fldfilter" value="<?=getRequestArg('keyword')?>" /></td>
	<input type="hidden" name="entity" id="entity" value="<?=getRequestArg('entity', null)?>" />
	<input type="hidden" name="entity_type" id="entity_type" value="<?=getRequestArg('entity_type', null)?>" />
	<input type="hidden" name="user" id="user" value="<?=getRequestArg('users', null)?>" /></td>
	<td><label for="entity_type"><?=getLabel('strEntityType')?></label><br />
	<select name="entity_type" id="entity_type" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aPromoEntityTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('entity_type'))
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
	$aFilter = array('keyword', 'entity', 'entity_type', 'users');

	$rs = $oComment->ListAll(getRequestArg('entity', null), getRequestArg('entity_type', null), getRequestArg('users', null), getRequestArg('keyword'), true, 0, $aContext);
	if (mysql_num_rows($rs))
	{
?>
<table summary="data" class="grid">
<thead>
<tr>
	<!--td><a href="<?=setPageContext(setContext(2,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strEntityType')?></a></td-->
	<td><a href="<?=setPageContext(setContext(1,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strCommentID')?></a></td>
	<td><a href="<?=setPageContext(setContext(4,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strCommentTitle')?></a>, <a href="<?=setPageContext(setContext(5,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strPublicationDate')?></td>
	<td><a href="<?=setPageContext(setContext(3,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strUsername')?></a></td>
	<!-- <td><a href="<?=setPageContext(setContext(8,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strHide')?></a></td> -->
	<td><a href="<?=setPageContext(setContext(7,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strLastUpdate')?></a></td>
	<td></td>
</tr>
</thead>
<tbody>
<?
		$aPublications = $oPublication->ListAllAsArray();
		$aUsers = $oUser->ListAllAsArray();
		
		$curRow = 0;
		$nTotalRows = mysql_num_rows($rs);
		$nStartRow = $aContext[CUR_REC];
		$nEndRow = $nStartRow + NUM_ROWS_ADMIN;
		while ($row = mysql_fetch_object($rs))
		{
			if ($curRow >= $nStartRow && $curRow < $nEndRow)
			{
?>
<tr class="<?=IIF($curRow%2==0, 'odd', 'even').IIF($row->IsHidden == true, ' hidden', '')?>" onmouseover="lt(this,1)" onmouseout="lt(this,0)">
	<!--td><a href="<?=setPage($aEntityTypes[$row->EntityTypeID], 0, $row->EntityID, ACT_VIEW)?>"><?=$aEntityTypes[$row->EntityTypeID]?></a></td-->
	<td><?=$row->CommentID?></td>
	<td><strong><?=$row->Title?></strong><br /><?=formatDateTime($row->CommentDate, DEFAULT_DATETIME_DISPLAY_FORMAT)?><br /><?=$row->Content?></td>
	<!-- <td><?=$aYesNo[$row->IsHidden]?></td> -->
	<td><a href="<?=setPage(ENT_USER, 0, $row->AuthorUserID, ACT_VIEW)?>"><?=$aUsers[$row->AuthorUserID]?></a></td>
	<td><?=$row->LastUpdate?></td>
	<td>
<? if ($row->IsHidden == B_TRUE) { ?>
		<a href="<?=setPage($page, 0, $row->CommentID, ACT_ON).keepFilter().keepContext()?>"><?=getLabel('show')?></a>
<? } else {?>
		<a href="<?=setPage($page, 0, $row->CommentID, ACT_OFF).keepFilter().keepContext()?>"><?=getLabel('hide')?></a>
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