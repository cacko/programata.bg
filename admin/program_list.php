<?php
if (!$bInSite) die();

//=========================================================
$aPages = $oPage->ListAllAsArray(null, '', 'event_list', true);
$aPagesSimple = $oPage->ListAllAsArraySimple(null, '', 'event_list', true);
$aCities = getLabel('aCities');
$aProgramTypes = getLabel('aProgramTypes');
$aPremiereTypes = getLabel('aPremiereTypes');
//=========================================================
$dToday = date(DEFAULT_DATE_DB_FORMAT);
$dStartDate = $dToday;//increaseDate($dToday, -ADMIN_WEEK_DAYS, 0);
$dEndDate = increaseDate($dToday, ADMIN_WEEK_DAYS, 0);
/*$dStartDate = $dToday;
for ($i=0; $i<ADMIN_WEEK_DAYS; $i++)
{
	if (getWeekDay($dStartDate) == 5) //friday
		$dStartDate = $dStartDate;
	else
		$dStartDate = increaseDate($dToday, -$i);
}
$dEndDate = increaseDate($dStartDate, ADMIN_WEEK_DAYS-1);*/
?>
<form action="<?=setPage($page)?>" method="post" name="PageFilter" id="PageFilter">
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
	<td><label for="program_type"><?=getLabel('strProgramType')?></label><br />
	<select name="program_type" id="program_type" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aProgramTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('program_type', $cat))
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
		</select></td>
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
	<td><label for="premiere_type"><?=getLabel('strPremiereType')?></label><br />
	<select name="premiere_type" id="premiere_type" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aPremiereTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('premiere_type'))
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
	<td><br /><input type="submit" value="<?=getLabel('strSearch')?>" class="btnfilter" /></td>
</tr>
</table>
</form>
<?
//=========================================================
	$aFilter = array('parent_page', 'start_date', 'end_date', 'program_type', 'premiere_type', 'sel_city', ARG_CAT);
	
	$rs = $oProgram->ListAll(getRequestArg('parent_page', null), getRequestArg('program_type', $cat),
				null, getRequestArg('premiere_type'), getRequestArg('sel_city', $nDefCity), null, null, 
				parseDate(getRequestArg('start_date', formatDate($dStartDate, DEFAULT_DATE_DISPLAY_FORMAT)), null),
				parseDate(getRequestArg('end_date', formatDate($dEndDate, DEFAULT_DATE_DISPLAY_FORMAT)), null),
				true, 0, $aContext);
	if (mysql_num_rows($rs))
	{
?>
<table summary="data" class="grid">
<thead>
<tr>
	<td><a href="<?=setPageContext(setContext(1,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strProgramID')?></a></td>
	<!--td><?=getLabel('strStartDate')?> - <?=getLabel('strEndDate')?></td-->
	<td><a href="<?=setPageContext(setContext(4,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strMainPlace')?></a></td>
	<td><a href="<?=setPageContext(setContext(6,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strMainEvent')?></a></td>
	<!--td><a href="<?=setPageContext(setContext(2,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strProgramType')?></a></td-->
	<td><?=getLabel('strParentPage')?></td>
	<!--td><a href="<?=setPageContext(setContext(7,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strPremiereType')?></a></td-->
	<td><a href="<?=setPageContext(setContext(12,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strCity')?></a></td>
	<!--td><a href="<?=setPageContext(setContext(8,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strLastUpdate')?></a></td-->
	<td></td>
</tr>
</thead>
<tbody>
<?
		$aYesNo = getLabel('aYesNo');
		$aUsers = $oUser->ListAllAsArray(USER_ADMIN);
		$aPromoEntityTypes = getLabel('aPromoEntityTypes');
		
		$curRow = 0;
		$nTotalRows = mysql_num_rows($rs);
		$nStartRow = $aContext[CUR_REC];
		$nEndRow = $nStartRow + NUM_ROWS_ADMIN;
		while ($row = mysql_fetch_object($rs))
		{
			if ($curRow >= $nStartRow && $curRow < $nEndRow)
			{
				$sPages = '';
				$aRelPages = $oProgram->ListProgramPagesAsArray($row->ProgramID);
				if (count($aRelPages)>0)
				{
					foreach($aRelPages as $cat)
						$sPages .= $aPagesSimple[$cat].'<br/>';
				}
				else
					$sPages = '<span class="a"><em>'.getLabel('strNone').'</em></span>';
				
				//$rPlace = $oPlace->GetByID($row->PlaceID);
				//$rEvent = $oEvent->GetByID($row->EventID);
				
				$sEvents = '';
				$aRelEvents = $oProgram->ListProgramEventsAsArray($row->ProgramID);
				if (count($aRelEvents)>0)
				{
					foreach($aRelEvents as $cat)
					{
						$rRelEvent = $oEvent->GetByID($cat);
						if (!empty($sEvents))
							$sEvents .= ', ';
						$sEvents .= $rRelEvent->Title;
					}
					$sEvents = '<br/>'.$sEvents;
				}
?>
<tr class="<?=IIF($curRow%2==0, 'odd', 'even').IIF($row->IsHidden == true, ' hidden', '')?>" onmouseover="lt(this,1)" onmouseout="lt(this,0)">
	<td><?=$row->ProgramID?></td>
	<!--td><?//formatDate($row->StartDate).' '.formatDate($row->EndDate)?></td-->
	<td><?=$row->PlaceTitle?></td>
	<td><strong><?=$row->EventTitle?></strong><?=$sEvents?></td>
	<!--td><?=$aProgramTypes[$row->ProgramTypeID]?></td-->
	<td><?=$sPages.IIF(!empty($row->PremiereTypeID), '<div class="hidden">'.$aPremiereTypes[$row->PremiereTypeID].'</div>', '')?></td>
	<!--td><?=$aPremiereTypes[$row->PremiereTypeID]?></td-->
	<td><?=$aCities[$row->CityID]?></td>
	<!--td><?=$row->LastUpdate?><br /><?=getLabel('strByUser').$aUsers[$row->LastUpdateUserID]?></td-->
	<td><a href="<?=setPage($page, 0, $row->ProgramID, ACT_VIEW).keepFilter().keepContext()?>"><?=getLabel('view')?></a> | <a href="<?=setPage($page, 0, $row->ProgramID, ACT_EDIT).keepFilter().keepContext()?>"><?=getLabel('edit')?></a> | <a href="<?=setPage($page, 0, $row->ProgramID, ACT_DELETE).keepFilter().keepContext()?>" onclick="return confMsg('<?=getLabel('strDeleteQ')?>');return false;"><?=getLabel('delete')?></a></td>
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