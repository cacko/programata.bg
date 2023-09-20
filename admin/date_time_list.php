<?php
if (!$bInSite) die();
//=========================================================
	$sMsg = '';
	switch($action)
	{
		case ACT_OFF:
			//print_r($_POST);
			foreach ($_POST as $k=>$v)
			{
				$val = getPostedArg($k);
				if (!empty($val))
				{
					if (strpos($k, 'id') !== false)
					{
						$nItemToDelete = str_replace('id', '', $k);
						$oProgramDateTime->Delete($nItemToDelete);
						$sMsg = getLabel('strDeleteOK').'<br /><br />'; // show ok message
					}
				}
			}
			break;
	}
	echo $sMsg;
	
	$dToday = date(DEFAULT_DATE_DB_FORMAT);
	$dStartDate = increaseDate($dToday, -ADMIN_WEEK_DAYS, 0);
	$dEndDate = increaseDate($dToday, ADMIN_WEEK_DAYS, 0);
	/*$dStartDate = $dToday;
	for ($i=0; $i<ADMIN_WEEK_DAYS; $i++)
	{
		if (getWeekDay($dStartDate) == 5) //friday
			continue;
		else
			$dStartDate = increaseDate($dToday, -$i);
	}
	$dEndDate = increaseDate($dStartDate, ADMIN_WEEK_DAYS-1);*/
?>
<form action="<?=setPage($page, $cat, $item, $action, $relitem)?>" method="post" name="PageFilter" id="PageFilter">
<table summary="filter" class="form">
<tr>
	<td><label for="start_date" class="dte"><?=getLabel('strStartDate')?></label><br />
	<input type="text" name="start_date" id="start_date" maxlength="10" class="flddate"
	       value="<?=getRequestArg('start_date', formatDate($dStartDate, DEFAULT_DATE_DISPLAY_FORMAT, ''))?>" 
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" /></td>
	<td><label for="end_date" class="dte"><?=getLabel('strEndDate')?></label><br />
	<input type="text" name="end_date" id="end_date" maxlength="10" class="flddate"
	       value="<?=getRequestArg('end_date', formatDate($dEndDate, DEFAULT_DATE_DISPLAY_FORMAT, ''))?>" 
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" /></td>
	<td><br /><input type="submit" value="<?=getLabel('strSearch')?>" class="btnfilter" /></td>
</tr>
</table>
</form>
<?
//=========================================================
	$aFilter = array('entity_id', ARG_RELID, ARG_CAT, 'start_date', 'end_date');
	
	//getRequestArg('entity_type', $cat),
	$rs = $oProgramDateTime->ListAll(getRequestArg('entity_id', $relitem),
				parseDate(getRequestArg('start_date', formatDate($dStartDate, DEFAULT_DATE_DISPLAY_FORMAT)), null),
				parseDate(getRequestArg('end_date', formatDate($dEndDate, DEFAULT_DATE_DISPLAY_FORMAT)), null),
				null, null, true, 0, $aContext);
	if (mysql_num_rows($rs))
	{
?>
<script type="text/javascript">
<!--
	function checkDown(elem, targetclass)
	{
		var gNodes = document.getElementsByTagName('INPUT');
		for (var i = 0; gNodes.length > i; i ++)
		{
			if (gNodes[i].className == targetclass)
			{
				gNodes[i].checked = elem.checked;
			}
		}
	}
// -->
</script>
<form action="<?=setPage($page, $cat, $item, ACT_OFF, $relitem)?>" method="post" name="DateList" id="DateList" onsubmit="return confMsg('<?=getLabel('strDeleteQ')?>');return false;">
<input type="hidden" name="start_date" id="start_date" value="<?=getRequestArg('start_date', formatDate($dStartDate, DEFAULT_DATE_DISPLAY_FORMAT, ''))?>" />
<input type="hidden" name="end_date" id="end_date" value="<?=getRequestArg('end_date', formatDate($dEndDate, DEFAULT_DATE_DISPLAY_FORMAT, ''))?>" />
<table summary="data" class="grid">
<thead>
<tr>
	<td><input type="checkbox" name="selector" id="selector" onclick="checkDown(this, 'to_select');" /><label class="list" for="selector"><?=getLabel('strSelectAll')?></label></a></td>
	<!--td><a href="<?=setPageContext(setContext(1,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strProgramDateTimeID')?></a></td>
	<td><a href="<?=setPageContext(setContext(2,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strProgramID')?></a></td-->
	<td><a href="<?=setPageContext(setContext(3,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strProgramDate')?></a></td>
	<td><a href="<?=setPageContext(setContext(4,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strProgramTime')?></a></td>
	<td><a href="<?=setPageContext(setContext(5,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strPrice')?></a></td>
	<!--td><a href="<?=setPageContext(setContext(6,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strLastUpdate')?></a></td-->
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
?>
<tr class="<?=IIF($curRow%2==0, 'odd', 'even').IIF($row->IsHidden == true, ' hidden', '')?>" onmouseover="lt(this,1)" onmouseout="lt(this,0)">
	<td><input type="checkbox" class="to_select" id="id<?=$row->ProgramDateTimeID?>" name="id<?=$row->ProgramDateTimeID?>" value="1" /></td>
	<!--td><?=$row->ProgramDateTimeID?></td>
	<td><?=$row->ProgramID?></td-->
	<td><?=formatDate($row->ProgramDate)?></td>
	<td><?=formatTime($row->ProgramTime)?></td>
	<td><?=$row->Price?></td>
	<!--td><?=$row->LastUpdate?><br /><?=getLabel('strByUser').$aUsers[$row->LastUpdateUserID]?></td-->
	<td><a href="<?=setPage($page, 0, $row->ProgramDateTimeID, ACT_VIEW).keepFilter().keepContext()?>"><?=getLabel('view')?></a> | <a href="<?=setPage($page, 0, $row->ProgramDateTimeID, ACT_EDIT).keepFilter().keepContext()?>"><?=getLabel('edit')?></a> | <a href="<?=setPage($page, 0, $row->ProgramDateTimeID, ACT_DELETE).keepFilter().keepContext()?>" onclick="return confMsg('<?=getLabel('strDeleteQ')?>');return false;"><?=getLabel('delete')?></a></td>
</tr>
<?
			}
			$curRow++;
		}
?>
</tbody>
<tfoot>
<tr>
	<td colspan="3"><input type="submit" value="<?=getLabel('strDeleteSelected')?>" class="btnfilter" /><br /><?=showPaging($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_ADMIN, $aFilter);?></td>
	<td colspan="2" align="right"><?=showPages($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_ADMIN, $aFilter);?></td>
</tr>
</tfoot>
</table>
</form>
<?
	}
	else
	{
?>
<div><?=getLabel('strNoRecords')?></div>
<?
	}
?>