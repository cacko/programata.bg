<?php
if (!$bInSite) die();
//=========================================================
$aPages = $oPage->ListAllAsArray(null, '', 'extra_list', true);
$aSectionPages = $oPage->ListAllParentsAsArraySimple(null, '', 'extra_list', true);
//=========================================================
$dToday = date(DEFAULT_DATE_DB_FORMAT);
$dStartDate = increaseDate($dToday, 0, -ARCHIVE_MONTHS);
$dEndDate = increaseDate($dToday, 0, ARCHIVE_MONTHS);
?>
<form action="<?=setPage($page)?>" method="post" name="PageFilter" id="PageFilter">
<table summary="filter" class="form">
<tr>
	<td><label for="keyword"><?=getLabel('strKeyword')?></label><br />
	<input type="text" name="keyword" id="keyword" maxlength="64" class="fldfilter" value="<?=getRequestArg('keyword')?>" /></td>
	<td><label for="start_date" class="dte"><?=getLabel('strStartDate')?></label><br />
	<input type="text" name="start_date" id="start_date" maxlength="10" class="flddate"
	       value="<?=getRequestArg('start_date');?>" 
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" /></td>
	<td><label for="end_date" class="dte"><?=getLabel('strEndDate')?></label><br />
	<input type="text" name="end_date" id="end_date" maxlength="10" class="flddate"
	       value="<?=getRequestArg('end_date');?>" 
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" /></td>
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
	<td><br /><input type="submit" value="<?=getLabel('strSearch')?>" class="btnfilter" /></td>
</tr>
</table>
</form>
<?
//=========================================================
	$aFilter = array('parent_page', 'keyword');
	
	$rs = $oExtra->ListAll(getRequestArg('parent_page',null), getRequestArg('keyword'),
				     parseDate(getRequestArg('start_date'), null),
				     parseDate(getRequestArg('end_date'), null), true, 0, $aContext);
	if (mysql_num_rows($rs))
	{
?>
<table summary="data" class="grid">
<thead>
<tr>
	<td><?=getLabel('strPublicationImage')?></td>
	<td><a href="<?=setPageContext(setContext(1,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strPublicationID')?></a></td>
	<td><a href="<?=setPageContext(setContext(2,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strPublicationDate')?></a></td>
	<td><a href="<?=setPageContext(setContext(3,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strPublicationName')?></a> (<a href="<?=setPageContext(setContext(10,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strNrViews')?></a>)</td>
	<td><?=getLabel('strParentPage')?></td>
	<!--td><a href="<?=setPageContext(setContext(11,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strLastUpdate')?></a></td-->
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
				$aRelPages = $oExtra->ListExtraPagesAsArray($row->PublicationID);
				if (count($aRelPages)>0)
				{
					foreach($aRelPages as $cat)
						$sPages .= $aSectionPages[$cat].'<br/>';
				}
				else
					$sPages = '<span class="a"><em>'.getLabel('strNone').'</em></span>';
				
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_EXTRA_THUMB.$row->PublicationID.'.'.EXT_IMG;
				if (!is_file($sMainImageFile))
				{
					$sMainImageFile = '../'.UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
				}
?>
<tr class="<?=IIF($curRow%2==0, 'odd', 'even').IIF($row->IsHidden == true, ' hidden', '')?>" onmouseover="lt(this,1)" onmouseout="lt(this,0)">
	<td><?=drawImage($sMainImageFile, W_IMG_THUMB/2)?></td>
	<td><?=$row->PublicationID?></td>
	<td><?=formatDate($row->PublicationDate)?></td>
	<td><strong><?=$row->Title?></strong><br /><?=$row->Subtitle?> (<?=$row->NrViews?>)</td>
	<td><?=$sPages?></td>
	<!--td><?=$row->LastUpdate?><br /><?=getLabel('strByUser').$aUsers[$row->LastUpdateUserID]?></td-->
	<td><a href="<?=setPage($page, 0, $row->PublicationID, ACT_VIEW).keepFilter().keepContext()?>"><?=getLabel('view')?></a> | <a href="<?=setPage($page, 0, $row->PublicationID, ACT_EDIT).keepFilter().keepContext()?>"><?=getLabel('edit')?></a> | <a href="<?=setPage($page, 0, $row->PublicationID, ACT_DELETE).keepFilter().keepContext()?>" onclick="return confMsg('<?=getLabel('strDeleteQ')?>');return false;"><?=getLabel('delete')?></a></td>
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