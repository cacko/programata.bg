<?php
if (!$bInSite) die();
//=========================================================
//$aPages = $oPage->ListAllAsArray(null, '', 'section', true);
$aPagesSimple = $oPage->ListAllAsArraySimple(null, '', 'section', true);
$aCities = getLabel('aCities');
//$aPromotionTypes = getLabel('aPromotionTypes');
$aPromotionTypesFull = getLabel('aPromotionTypesFull');
//=========================================================
$dToday = date(DEFAULT_DATE_DB_FORMAT);
$dStartDate = increaseDate($dToday, 0, 0);
$dEndDate = increaseDate($dToday, ADMIN_WEEK_DAYS-3, 0);

$sPagePromo = getRequestArg('parent_page_promo');
$aVals = explode('-', $sPagePromo);
$nParentPage = $nPromo = null;
if(is_array($aVals))
{
	$nParentPage = $aVals[0];
	if (count($aVals)>1)
		$nPromo = $aVals[1];
}
?>
<form action="<?=setPage($page)?>" method="post" name="PageFilter" id="PageFilter">
<table summary="filter" class="form">
<tr>
	<td><label for="start_date"><?=getLabel('strStartDate')?></label><br />
	<input type="text" name="start_date" id="start_date" maxlength="64" class="fldfilter"
	       value="<?=getRequestArg('start_date', formatDate($dStartDate, DEFAULT_DATE_DISPLAY_FORMAT, ''))?>" 
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" /></td>
	<td><label for="end_date"><?=getLabel('strEndDate')?></label><br />
	<input type="text" name="end_date" id="end_date" maxlength="64" class="fldfilter"
	       value="<?=getRequestArg('end_date', formatDate($dEndDate, DEFAULT_DATE_DISPLAY_FORMAT, ''))?>" 
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" /></td>
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
	<td><label for="parent_page_promo"><?=getLabel('strPages').'/'.getLabel('strPromotionType')?></label><br />
	<select name="parent_page_promo" id="parent_page_promo" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aPagesSimple as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == $nParentPage)
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
			$aPageAccents = $aPromotionTypesFull[$key];
			foreach($aPageAccents as $k2=>$v2)
			{
				echo '<option value="'.$key.'-'.$k2.'"';
				if ($key == $nParentPage && $k2 == $nPromo)
					echo ' selected="selected"';
				echo '> - - - '.$v2.'</option>'."\n";
			}
		}
	?>
	</select></td>
	<td><br /><input type="submit" value="<?=getLabel('strSearch')?>" class="btnfilter" /></td>
</tr>
</table>
</form>
<?
//=========================================================
	$aFilter = array('parent_page_promo', 'start_date', 'end_date', 'sel_city');
	
	$rs = $oPromotion->ListAll($nParentPage, getRequestArg('sel_city', $nDefCity), $nPromo,
				   parseDate(getRequestArg('start_date', formatDate($dStartDate, DEFAULT_DATE_DISPLAY_FORMAT)), null),
				   parseDate(getRequestArg('end_date', formatDate($dEndDate, DEFAULT_DATE_DISPLAY_FORMAT)), null),
				   true, 0, $aContext);
	if (mysql_num_rows($rs))
	{
?>
<table summary="data" class="grid">
<thead>
<tr>
	<td><?=getLabel('strPromotionImage')?></td>
	<td><a href="<?=setPageContext(setContext(1,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strPromotionID')?></a></td>
	<td><a href="<?=setPageContext(setContext(2,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strStartDate')?></a> - <a href="<?=setPageContext(setContext(3,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strEndDate')?></a></td>
	<td><?=getLabel('strPromotionTitle')?></td>
	<td><a href="<?=setPageContext(setContext(11,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strPromotionType')?></a> / <br />
	<a href="<?=setPageContext(setContext(6,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strPages')?></a></td>
	<td><?=getLabel('strCity')?></td>
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
				$rItem = null;
				$sEntityType = $aPromoEntityTypes[$row->EntityTypeID];
				$sMainImageFile = '';
				switch($row->EntityTypeID)
				{
					case $aEntityTypes[ENT_NEWS]:
						$rItem = $oNews->GetByID($row->EntityID);
						$sMainImageFile = '../'.UPLOAD_DIR.IMG_NEWS_THUMB.$row->EntityID.'.'.EXT_IMG;
						break;
					case $aEntityTypes[ENT_PUBLICATION]:
						$rItem = $oPublication->GetByID($row->EntityID);
						$sMainImageFile = '../'.UPLOAD_DIR.IMG_PUBLICATION_THUMB.$row->EntityID.'.'.EXT_IMG;
						break;
					case $aEntityTypes[ENT_FESTIVAL]:
						$rItem = $oFestival->GetByID($row->EntityID);
						$sMainImageFile = '../'.UPLOAD_DIR.IMG_FESTIVAL_THUMB.$row->EntityID.'.'.EXT_IMG;
						break;
					case $aEntityTypes[ENT_PLACE]:
						$rItem = $oPlace->GetByID($row->EntityID);
						$sMainImageFile = '../'.UPLOAD_DIR.IMG_PLACE_THUMB.$row->EntityID.'.'.EXT_IMG;
						break;
					case $aEntityTypes[ENT_EVENT]:
						$rItem = $oEvent->GetByID($row->EntityID);
						$sMainImageFile = '../'.UPLOAD_DIR.IMG_EVENT_THUMB.$row->EntityID.'.'.EXT_IMG;
						break;
					case $aEntityTypes[ENT_URBAN]:
						$rItem = $oUrban->GetByID($row->EntityID);
						$sMainImageFile = '../'.UPLOAD_DIR.IMG_URBAN.$row->EntityID.'/'.IMG_THUMB.'1_1.'.EXT_IMG;
						break;
					case $aEntityTypes[ENT_MULTY]:
						$rItem = $oMulty->GetByID($row->EntityID);
						$sMainImageFile = '../'.UPLOAD_DIR.IMG_MULTY.$row->EntityID.'/'.IMG_THUMB.'1.'.EXT_IMG;
						break;
					case $aEntityTypes[ENT_EXTRA]:
						$rItem = $oExtra->GetByID($row->EntityID);
						$sMainImageFile = '../'.UPLOAD_DIR.IMG_EXTRA_THUMB.$row->EntityID.'.'.EXT_IMG;
						break;
				}
				$sEntityTitle = $rItem->Title;
				//$sMainImageFile = '../'.UPLOAD_DIR.IMG_PROMOTION.$row->PromotionID.'.'.EXT_IMG;
				if (!is_file($sMainImageFile))
				{
					$sMainImageFile = '../'.UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
				}
				
				$sCities = '';
				$aRelCities = $oPromotion->ListPromotionCitiesAsArray($row->PromotionID);
				if (count($aRelCities)>0)
				{
					foreach($aRelCities as $cat)
						$sCities .= $aCities[$cat].'<br/>';
				}
				else
					$sCities = '<span class="a"><em>'.getLabel('strNone').'</em></span>';
?>
<tr class="<?=IIF($curRow%2==0, 'odd', 'even').IIF($row->IsHidden == true, ' hidden', '')?>" onmouseover="lt(this,1)" onmouseout="lt(this,0)">
	<td><?=drawImage($sMainImageFile, W_IMG_THUMB/2)?></td>
	<td><?=$row->PromotionID?></td>
	<td><?=formatDate($row->StartDate).' - '.formatDate($row->EndDate)?></td>
	<td><?=$sEntityType?><br /><strong><?=$sEntityTitle?></strong></td>
	<td><?=$aPromotionTypesFull[$row->PageID][$row->PromotionTypeID]?> (<?=$row->SortOrder?>)<br /><?=$aPagesSimple[$row->PageID]?></td>
	<td><?=$sCities?></td>
	<!--td><?=$row->LastUpdate?><br /><?=getLabel('strByUser').$aUsers[$row->LastUpdateUserID]?></td-->
	<td><a href="<?=setPage($page, 0, $row->PromotionID, ACT_VIEW).keepFilter().keepContext()?>"><?=getLabel('view')?></a> | <a href="<?=setPage($page, 0, $row->PromotionID, ACT_EDIT).keepFilter().keepContext()?>"><?=getLabel('edit')?></a> | <a href="<?=setPage($page, 0, $row->PromotionID, ACT_DELETE).keepFilter().keepContext()?>" onclick="return confMsg('<?=getLabel('strDeleteQ')?>');return false;"><?=getLabel('delete')?></a></td>
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