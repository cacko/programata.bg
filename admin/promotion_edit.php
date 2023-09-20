<?php
if (!$bInSite) die();
//=========================================================
$aFilter = array('parent_page_promo', 'start_date', 'end_date', 'sel_city');
$strFilterListLink = '<a href="'.setPage($page).keepFilter().keepContext().'">'.getLabel('strBackToList').'</a><br /><br />';
//=========================================================
switch($action)
{
	case ACT_SAVE:
		$sMsg = '';
		//print_r($_POST);
		$sPagePromo = getPostedArg('parent_page_promo_id');
		$aVals = explode('-', $sPagePromo);
		$nParentPage = $nPromo = 0;
		if(is_array($aVals))
		{
			$nParentPage = $aVals[0];
			if (count($aVals)>1)
				$nPromo = $aVals[1];
		}
		if (isset($_POST['id']) && !empty($_POST['id']))
		{
			$item = getPostedArg('id');
		}
		else
		{
			$item = $oPromotion->Insert(	parseDate(getPostedArg('sel_start_date')),
							parseDate(getPostedArg('sel_end_date')),
							$nPromo, //getPostedArg('promotion_type_id'),
							getPostedArg('entity_type_id'),
							getPostedArg('entity_id'),
							$nParentPage, //getPostedArg('parent_page_id'),
							getPostedArg('sort_order'),
							getPostedArg('hidden')); // primary record
		}
		//foreach($aLanguages as $key=>$val)
		//{
			$oPromotion->Update(	$item,
						parseDate(getPostedArg('sel_start_date')),
						parseDate(getPostedArg('sel_end_date')),
						$nPromo, //getPostedArg('promotion_type_id'),
						getPostedArg('entity_type_id'),
						getPostedArg('entity_id'),
						$nParentPage, //getPostedArg('parent_page_id'),
						getPostedArg('sort_order'),
						getPostedArg('hidden'));
						//$key); // update all
		//}
		// save relations
		$oPromotion->DeletePromotionCity($item);
		$oPromotion->InsertPromotionCity($item, getPostedArg('city_id'));
		if (!$item)
			$sMsg .= getLabel('strSaveFailed'); // failed message
		else
			$sMsg .= getLabel('strSaveOK'); // ok message
		echo $sMsg.'<br /><br />';
		echo $strFilterListLink;
		$aRows = array();
		foreach($aLanguages as $key=>$val)
			$aRows[$key] = $oPromotion->GetByID($item, $key); // get by id
		if (count($aRows)>0) showPreview($aRows); // show preview & related links
		break;
	case ACT_ADD:
		showForm(); // load form
		break;
	case ACT_EDIT:
		if (isset($item) && !empty($item))
		{
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oPromotion->GetByID($item, $key); // get by id
			if (count($aRows)>0) showForm($aRows); // load form
		}
		break;
	case ACT_DELETE:
		if (isset($item) && !empty($item))
		{
			$oPromotion->Delete($item);
			echo getLabel('strDeleteOK').'<br /><br />'; // show ok message
			echo $strFilterListLink;
		}
		break;
	default:
	case ACT_VIEW:
		if (isset($item) && !empty($item))
		{
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oPromotion->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
}
//=========================================================
function showForm($aRows=null)
{
	global $page, $oPage, $oPromotion, $oUser, $aLanguages, $aAbbrLanguages;

	require_once "../FCKeditor/fckeditor.php";

	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->PromotionID;
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
	}
	echo getLabel('strRequired').'<br />'."\n";
?>
<script type="text/javascript">
<!--
function fCheck(frm) {
<? foreach ($aLanguages as $key=>$val) {?>
	//if (!valEmpty("title<?=$aAbbrLanguages[$key]?>", "<?=getLabel('strEnter', $key).getLabel('strPromotionName', $key)?>")) return false;
<?}?>
	if (!valEmpty("sel_start_date", "<?=getLabel('strEnter').getLabel('strStartDate')?>")) return false;
	if (!valEmpty("sel_end_date", "<?=getLabel('strEnter').getLabel('strEndDate')?>")) return false;
	if (!valOption("parent_page_promo_id", "<?=getLabel('strSelect').getLabel('strPromotionType')?>")) return false;
	if (!valOption("entity_type_id", "<?=getLabel('strSelect').getLabel('strEntityType')?>")) return false;
	if (!valEmpty("entity_id", "<?=getLabel('strEnter').getLabel('strEntity')?>")) return false;
	if (!valEmpty("sort_order", "<?=getLabel('strEnter').getLabel('strSortOrder')?>")) return false;
	return true;
}
//-->
</script>
<form action="<?=setPage($page, 0, $itemID, ACT_SAVE).keepFilter().keepContext()?>" enctype="multipart/form-data" method="post" name="Promotion" id="Promotion" onsubmit="return fCheck(this);">
<input type="hidden" name="MAX_FILE_SIZE" value="2550000" />

<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="id"><?=getLabel('strPromotionID')?></label><?=$itemID?>
	<input type="hidden" name="id" id="id" value="<?=$itemID?>" /><br />
	<br />

	<label for="parent_page_promo_id"><?=getLabel('strPages').'/'.getLabel('strPromotionType').formatVal()?></label>
	<select name="parent_page_promo_id" id="parent_page_promo_id" class="fldsmall" size="10">
		<!--option value="0"><?=getLabel('strSelect')?></option-->
	<?
		$aPages = $oPage->ListAllAsArraySimple(null, '', 'section', true);
		$aPromotionTypesFull = getLabel('aPromotionTypesFull');
		foreach($aPages as $key=>$val)
		{
			echo '<optgroup label="'.$val.'">'."\n";
			$aPageAccents = $aPromotionTypesFull[$key];
			foreach($aPageAccents as $k2=>$v2)
			{
				echo '<option value="'.$key.'-'.$k2.'"';
				if ($key == $aRows[DEF_LANG]->PageID && $k2 == $aRows[DEF_LANG]->PromotionTypeID)
					echo ' selected="selected"';
				echo '>'.$v2.'</option>'."\n";
			}
			echo '</optgroup>'."\n";
		}
	?>
	</select><br />
	<br />

	<?
		$today_ts = mktime (0,0,0,date("m"), date("d"), date("Y"));
		$dStartDate = date(DEFAULT_DATE_DB_FORMAT, $today_ts);
		$dEndDate = increaseDate($dStartDate, ADMIN_WEEK_DAYS);
	?>
	<label for="sel_start_date"><?=getLabel('strStartDate').formatVal().'<br />'.getLabel('strEndDate').formatVal()?></label>
	<input type="text" name="sel_start_date" id="sel_start_date" maxlength="10" class="fldfilter"
	       value="<?=IIF(!empty($aRows[DEF_LANG]), formatDate($aRows[DEF_LANG]->StartDate), formatDate($dStartDate))?>"
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" />
	<input type="text" name="sel_end_date" id="sel_end_date" maxlength="10" class="fldfilter"
	       value="<?=IIF(!empty($aRows[DEF_LANG]), formatDate($aRows[DEF_LANG]->EndDate), formatDate($dEndDate))?>"
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" /><br />
	<br />

	<label><?=getLabel('strHide')?></label>
<?
	reset($aYesNo);
	$bHidden = false;
	if (!empty($aRows[DEF_LANG]))
		$bHidden = $aRows[DEF_LANG]->IsHidden;
	while(list($key, $value) = each($aYesNo))
	{
		?>
		<input type="radio" name="hidden" id="hidden_<?=$key?>" value="<?=$key?>"<?=IIF($key==$bHidden,' checked="checked"','')?> />
		<label class="list" for="hidden_<?=$key?>"><?=$value?></label>
<? 	} ?>
	<br class="clear" />
	<br />
<?
	if (!empty($aRows[DEF_LANG]))
	{
	?>
	<label><?=getLabel('strLastUpdate')?></label>
	<?=IIF(!empty($aRows[DEF_LANG]), $aRows[DEF_LANG]->LastUpdate, '').IIF(!is_null($rUser), getLabel('strByUser').$rUser->Username.' ('.$rUser->FirstName.' '.$rUser->LastName.')', '')?><br />
<? 	} ?>
</fieldset>
<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="entity_type_id"><?=getLabel('strEntityType').formatVal().'<br />'.getLabel('strEntity').formatVal()?></label>
	<select name="entity_type_id" id="entity_type_id" class="fldfilter">
		<option value="0"><?=getLabel('strSelect')?></option>
	<?
		$aPromoEntityTypes = getLabel('aPromoEntityTypes');
		foreach($aPromoEntityTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if ($key == $aRows[DEF_LANG]->EntityTypeID)
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select> <input type="text" name="entity_id" id="entity_id" value="<?=IIF(!empty($aRows[DEF_LANG]), $aRows[DEF_LANG]->EntityID, '')?>" maxlength="10" class="fldfilter" /><br />
	<br />
	<label for="city_id"><?=getLabel('strCity').formatVal().getLabel('multiple')?></label>
	<select name="city_id[]" id="city_id" multiple="multiple" size="5" class="fldsmall">
	<?
		$aCities = getLabel('aCities');
		if (!empty($aRows[DEF_LANG]))
		{
			$aRelCities = $oPromotion->ListPromotionCitiesAsArray($itemID);
		}
		foreach($aCities as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if (in_array($key, $aRelCities))
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	<label for="sort_order"><?=getLabel('strSortOrder').formatVal()?></label>
	<input type="text" name="sort_order" id="sort_order" value="<?=IIF(!empty($aRows[DEF_LANG]), $aRows[DEF_LANG]->SortOrder, '0')?>" maxlength="5" class="fldfilter" /><br />
	<br />

	<!--hr />
	<? 	if (!empty($aRows[DEF_LANG]) )
		{
			$sMainImageFile = '../'.UPLOAD_DIR.IMG_PROMOTION_THUMB.$itemID.'.'.EXT_IMG;
			if (!is_file($sMainImageFile))
			{
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
			}
	?>
	<label><?=getLabel('strPromotionImage')?></label>
	<?=drawImage($sMainImageFile)?><br />
	<br />
	<?	}?>
	<label for="main_image"><?=getLabel('strMainImageFull')?></label>
	<input type="file" name="main_image" id="main_image" size="47" accept="image/x-jpg" class="btn" /><br />
	<br />
	<hr />
	<br /-->
</fieldset>
<br class="clear" />
<?
	foreach($aLanguages as $key=>$val)
	{
?>
<!--fieldset title="<?=$val?>" class="half">
	<legend><?=$val?></legend>
	<label for="title<?=$aAbbrLanguages[$key]?>"><?=getLabel('strPromotionTitle', $key).formatVal()?></label><br />
	<input type="text" name="title<?=$aAbbrLanguages[$key]?>" id="title<?=$aAbbrLanguages[$key]?>"
		value="<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->Title), '')?>" maxlength="255" class="fld" /><br />
	<br />
</fieldset-->
<?
	}
?>
	<br class="clear" />
	<br />
	<input type="submit" value="<?=getLabel('strSave')?>" class="btn" />
</form>
<?
}
//=========================================================
function showPreview($aRows=null)
{
	global $page, $oPage, $oPromotion, $oUser, $aLanguages, $aEntityTypes, $oNews, $oPublication, $oFestival, $oPlace, $oEvent, $oUrban, $oMulty;

	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	$aCities = getLabel('aCities');
	$aPromotionTypesFull = getLabel('aPromotionTypesFull');
	$aPromoEntityTypes = getLabel('aPromoEntityTypes');
	$aPages = $oPage->ListAllAsArraySimple(null, '', 'section', true);
	$sCities = '';
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->PromotionID;
	}
	if (!empty($itemID))
	{
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);

		$aRelCities = $oPromotion->ListPromotionCitiesAsArray($itemID);
		if (is_array($aRelCities) && count($aRelCities)>0)
		{
			foreach($aRelCities as $cat)
				$sCities .= $aCities[$cat].'<br/>';
		}
		else
			$sCities = '<span class="a"><em>'.getLabel('strNone').'</em></span><br />';

		$rItem = null;
		$sEntityType = $aPromoEntityTypes[$aRows[DEF_LANG]->EntityTypeID];
		$sMainImageFile = '';
		switch($aRows[DEF_LANG]->EntityTypeID)
		{
			case $aEntityTypes[ENT_NEWS]:
				$rItem = $oNews->GetByID($aRows[DEF_LANG]->EntityID);
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_NEWS_THUMB.$aRows[DEF_LANG]->EntityID.'.'.EXT_IMG;
				break;
			case $aEntityTypes[ENT_PUBLICATION]:
				$rItem = $oPublication->GetByID($aRows[DEF_LANG]->EntityID);
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_PUBLICATION_THUMB.$aRows[DEF_LANG]->EntityID.'.'.EXT_IMG;
				break;
			case $aEntityTypes[ENT_FESTIVAL]:
				$rItem = $oFestival->GetByID($aRows[DEF_LANG]->EntityID);
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_FESTIVAL_THUMB.$aRows[DEF_LANG]->EntityID.'.'.EXT_IMG;
				break;
			case $aEntityTypes[ENT_PLACE]:
				$rItem = $oPlace->GetByID($aRows[DEF_LANG]->EntityID);
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_PLACE_THUMB.$aRows[DEF_LANG]->EntityID.'.'.EXT_IMG;
				break;
			case $aEntityTypes[ENT_EVENT]:
				$rItem = $oEvent->GetByID($aRows[DEF_LANG]->EntityID);
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_EVENT_THUMB.$aRows[DEF_LANG]->EntityID.'.'.EXT_IMG;
				break;
			case $aEntityTypes[ENT_URBAN]:
				$rItem = $oUrban->GetByID($aRows[DEF_LANG]->EntityID);
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_URBAN.$aRows[DEF_LANG]->EntityID.'/'.IMG_THUMB.'1_1.'.EXT_IMG;
				break;
			case $aEntityTypes[ENT_MULTY]:
				$rItem = $oMulty->GetByID($aRows[DEF_LANG]->EntityID);
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_MULTY.$aRows[DEF_LANG]->EntityID.'/'.IMG_THUMB.'1.'.EXT_IMG;
				break;
			case $aEntityTypes[ENT_EXTRA]:
				$rItem = $oExtra->GetByID($aRows[DEF_LANG]->EntityID);
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_EXTRA_THUMB.$aRows[DEF_LANG]->EntityID.'.'.EXT_IMG;
				break;
		}
		$sEntityTitle = $rItem->Title;

		//$sMainImageFile = '../'.UPLOAD_DIR.IMG_PROMOTION.$itemID.'.'.EXT_IMG;
		if (!is_file($sMainImageFile))
		{
			$sMainImageFile = '../'.UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
		}
?>
<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label><?=getLabel('strPromotionID')?></label>
	<?=$aRows[DEF_LANG]->PromotionID?><br />
	<br />
	<label><?=getLabel('strPromotionType')?></label>
	<div><?=$aPages[$aRows[DEF_LANG]->PageID]?>/<?=$aPromotionTypesFull[$aRows[DEF_LANG]->PageID][$aRows[DEF_LANG]->PromotionTypeID]?></div><br />
	<br />
	<label><?=getLabel('strCity')?></label>
	<div><?=$sCities?></div><br />
	<label><?=getLabel('strEntityType').'<br />'.getLabel('strEntity')?></label>
	<div><?=$sEntityType?> / <?=$sEntityTitle?></div><br />
	<br />
	<br />
	<label><?=getLabel('strSortOrder')?></label>
	<?=$aRows[DEF_LANG]->SortOrder?><br />
	<br />

	<label><?=getLabel('strPromotionImage')?></label>
	<?=drawImage($sMainImageFile)?><br />
	<br />
	<label><?=getLabel('strStartDate')?></label>
	<?=formatDate($aRows[DEF_LANG]->StartDate)?><br />
	<br />
	<label><?=getLabel('strEndDate')?></label>
	<?=formatDate($aRows[DEF_LANG]->EndDate)?><br />
	<br />
	<label><?=getLabel('strHide')?></label>
	<?=$aYesNo[$aRows[DEF_LANG]->IsHidden]?><br />
	<br />
	<label><?=getLabel('strLastUpdate')?></label>
	<?=IIF(!empty($aRows[DEF_LANG]), $aRows[DEF_LANG]->LastUpdate, '').getLabel('strByUser').IIF(!empty($aRows[DEF_LANG]), $rUser->Username.' ('.$rUser->FirstName.' '.$rUser->LastName.')', '')?><br />
</fieldset>
<br class="clear" />
<?
	foreach($aLanguages as $key=>$val)
	{
?>
<!--fieldset title="<?=$val?>" class="half">
	<legend><?=$val?></legend>
	<label><?=getLabel('strPromotionTitle', $key)?></label>
	<?=IIF(!empty($aRows[$key]), $aRows[$key]->Title, '')?><br />
	<br />
</fieldset-->
<?
	}
?>
<ul class="nav">
	<li><a href="<?=setPage($page, 0, $itemID, ACT_EDIT).keepFilter().keepContext()?>"><?=getLabel('edit')?></a></li>
	<li><a href="<?=setPage($page, 0, $itemID, ACT_DELETE).keepFilter().keepContext()?>" onclick="return confMsg('<?=getLabel('strDeleteQ')?>');return false;"><?=getLabel('delete')?></a></li>
</ul>
<?
	}
}
?>