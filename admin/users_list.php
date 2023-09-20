<?php
if (!$bInSite) die();
//=========================================================
$aCities = getLabel('aCities');
?>
<form action="<?=setPage($page)?>" method="post" name="UserFilter" id="UserFilter">
<table summary="filter" class="form">
<tr>
	<td><label for="keyword"><?=getLabel('strKeyword')?></label><br />
	<input type="text" name="keyword" id="keyword" maxlength="64" class="fldfilter" value="<?=getRequestArg('keyword')?>" /></td>
	<td><label for="status"><?=getLabel('strUserStatus')?></label><br />
	<select name="status" id="status" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		$aStatuses = getLabel('aUserStatus');
		foreach($aStatuses as $key=>$val)
		{
			if (empty($key)) continue;
			
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('status', USER_ADMIN))
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
	$aFilter = array('keyword', 'status');

	$rs = $oUser->ListAll(getRequestArg('status', null), getRequestArg('keyword'), '', '', $aContext);
	if (mysql_num_rows($rs))
	{
?>
<table summary="data" class="grid">
<thead>
<tr>
	<td><a href="<?=setPageContext(setContext(15,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strUserID')?></a></td>
	<td><a href="<?=setPageContext(setContext(1,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strUsername')?></a></td>
	<td><a href="<?=setPageContext(setContext(3,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strEmail')?></a></td>
	<td><a href="<?=setPageContext(setContext(4,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strFirstName')?></a>, <a href="<?=setPageContext(setContext(5,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strLastName')?></a></td>
	<td><a href="<?=setPageContext(setContext(16,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strCity')?></a></td>
	<td><a href="<?=setPageContext(setContext(10,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strUserStatus')?></a></td>
	<td><a href="<?=setPageContext(setContext(12,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strLastLogin')?></a> (<a href="<?=setPageContext(setContext(13,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strNrLogins')?></a>)</td>
	<td></td>
</tr>
</thead>
<tbody>
<?
		$curRow = 0;
		$nTotalRows = mysql_num_rows($rs);
		$nStartRow = $aContext[CUR_REC];
		$nEndRow = $nStartRow + NUM_ROWS_ADMIN;
		while ($row = mysql_fetch_object($rs))
		{
			if ($curRow >= $nStartRow && $curRow < $nEndRow)
			{
				$nComments = $oComment->GetCountByAuthorUser($row->UserID, true);
?>
<tr class="<?=IIF($curRow%2==0, 'odd', 'even').IIF($row->UserStatus == USER_GUEST, ' hidden', '')?>" onmouseover="lt(this,1)" onmouseout="lt(this,0)">
	<td><?=$row->UserID?></td>
	<td><?=$row->Username?></td>
	<td><a href="mailto:<?=$row->Email?>"><?=$row->Email?></a></td>
	<td><strong><?=$row->FirstName.' '.$row->LastName?></strong></td>
	<td><?=$aCities[$row->DefaultCityID]?></td>
	<td><?=$aStatuses[$row->UserStatus]?><br /><a href="<?=setPage(ENT_COMMENT).'&amp;'.ENT_USER.'='.$row->UserID.'">'.getLabel('strComments').'</a> ('.$nComments.')'?></td>
	<td><?=$row->LastLogin.' ('.$row->NrLogins.')'?></td>
	<td><a href="<?=setPage($page, 0, $row->UserID, ACT_VIEW).keepFilter().keepContext()?>"><?=getLabel('view')?></a> | <a href="<?=setPage($page, 0, $row->UserID, ACT_EDIT).keepFilter().keepContext()?>"><?=getLabel('edit')?></a> | <a href="<?=setPage($page, 0, $row->UserID, ACT_GENERATE)?>" onclick="return confMsg('<?=getLabel('strGenerateQ')?>');return false;"><?=getLabel('generate')?></a></td>
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
	<td colspan="4" align="right"><?=showPages($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_ADMIN, $aFilter);?></td>
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