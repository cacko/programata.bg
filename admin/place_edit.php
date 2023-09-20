<?php
if (!$bInSite) die();
//=========================================================
$aFilter = array('parent_page', 'keyword', 'sel_city', 'place_type', 'letter', ARG_CAT);
$strFilterListLink = '<a href="'.setPage($page).keepFilter().keepContext().'">'.getLabel('strBackToList').'</a><br /><br />';
//=========================================================
switch($action)
{
	case ACT_SAVE:
		$sMsg = '';
		//print_r($_POST);
		if (isset($_POST['id']) && !empty($_POST['id']))
		{
			$item = getPostedArg('id');
		}
		else
		{
			$item = $oPlace->Insert(getPostedArg('title'.$aAbbrLanguages[DEF_LANG]), 
						getPostedArg('short'.$aAbbrLanguages[DEF_LANG]),
						getPostedArg('keywords'.$aAbbrLanguages[DEF_LANG]), 
						getPostedArg('description'.$aAbbrLanguages[DEF_LANG]), 
						getPostedArg('text'.$aAbbrLanguages[DEF_LANG]), 
						getPostedArg('address'.$aAbbrLanguages[DEF_LANG]),
						getPostedArg('work_time'.$aAbbrLanguages[DEF_LANG]), 
						getPostedArg('start_time'),
						getPostedArg('city_id'),
						getPostedArg('place_type_id'), 
						getPostedArg('hidden')); // primary record
		}
		foreach($aLanguages as $key=>$val)
		{
			$oPlace->Update($item, 
					getPostedArg('title'.$aAbbrLanguages[$key]),
					getPostedArg('short'.$aAbbrLanguages[$key]),
					getPostedArg('keywords'.$aAbbrLanguages[$key]), 
					getPostedArg('description'.$aAbbrLanguages[$key]),
					getPostedArg('text'.$aAbbrLanguages[$key]),
					getPostedArg('address'.$aAbbrLanguages[$key]),
					getPostedArg('work_time'.$aAbbrLanguages[$key]), 
					getPostedArg('start_time'),
					getPostedArg('city_id'),
					getPostedArg('place_type_id'), 
					getPostedArg('hidden'),
					$key); // update all
		}
		// save relations
		$oPlace->DeletePlacePage($item);
		$oPlace->InsertPlacePage($item, getPostedArg('parent_page_id'));
		$oPlace->DeletePlaceLabel($item);
		$oPlace->InsertPlaceLabel($item, GRP_CUISINE, getPostedArg('cuisine'));
		$oPlace->InsertPlaceLabel($item, GRP_ATMOS, getPostedArg('atmosphere'));
		$oPlace->InsertPlaceLabel($item, GRP_PRICE, getPostedArg('price_category'));
		$oPlace->InsertPlaceLabel($item, GRP_BGNDMUSIC, getPostedArg('music_style'));
		// UPLOAD FILE HERE //==========================================
		$nError = uploadBrowsedFile('main_image', IMG_PLACE.$item, EXT_IMG);/*, 'empty.jpg'*/
		if (empty($nError))
		{
			resizeImage(IMG_PLACE.$item.'.'.EXT_IMG, W_IMG_GALLERY, H_IMG_GALLERY);
			deleteFile(IMG_PLACE_MID.$item, EXT_IMG);
			duplicateFile(IMG_PLACE.$item.'.'.EXT_IMG, IMG_PLACE_MID.$item.'.'.EXT_IMG);
			resizeImage(IMG_PLACE_MID.$item.'.'.EXT_IMG, W_IMG_MIDDLE, H_IMG_MIDDLE);
			deleteFile(IMG_PLACE_THUMB.$item, EXT_IMG);
			duplicateFile(IMG_PLACE.$item.'.'.EXT_IMG, IMG_PLACE_THUMB.$item.'.'.EXT_IMG);
			resizeImage(IMG_PLACE_THUMB.$item.'.'.EXT_IMG, W_IMG_THUMB, H_IMG_THUMB);
		}
		$sMsg .= getLabel('strFile_'.$nError);
		// UPLOAD FILE HERE //==========================================
		// UPDATE FEEDS HERE //==========================================
		//include_once('generate_rss2.php');
		// UPDATE FEEDS HERE //==========================================
		if (!$item)
			$sMsg .= getLabel('strSaveFailed'); // failed message
		else
			$sMsg .= getLabel('strSaveOK'); // ok message
		echo $sMsg.'<br /><br />';
		echo $strFilterListLink;
		$aRows = array();
		foreach($aLanguages as $key=>$val)
			$aRows[$key] = $oPlace->GetByID($item, $key); // get by id
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
				$aRows[$key] = $oPlace->GetByID($item, $key); // get by id
			if (count($aRows)>0) showForm($aRows); // load form
		}
		break;
	case ACT_DELETE:
		if (isset($item) && !empty($item))
		{
			// DELETE FILE HERE //==========================================
			deleteFile(IMG_PLACE.$item, EXT_IMG);
			deleteFile(IMG_PLACE_MID.$item, EXT_IMG);
			deleteFile(IMG_PLACE_THUMB.$item, EXT_IMG);
			// DELETE FILE HERE //==========================================
			$oPlace->Delete($item);
			echo getLabel('strDeleteOK').'<br /><br />'; // show ok message
			echo $strFilterListLink;
		}
		break;
	case ACT_DELETE_IMG:
		if (isset($item) && !empty($item))
		{
			// DELETE FILE HERE //==========================================
			deleteFile(IMG_PLACE.$item, EXT_IMG);
			deleteFile(IMG_PLACE_MID.$item, EXT_IMG);
			deleteFile(IMG_PLACE_THUMB.$item, EXT_IMG);
			// DELETE FILE HERE //==========================================
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oPlace->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
	case ACT_RELOFF:
		if (isset($item) && !empty($item))
		{
			if (isset($relitem) && !empty($relitem))
			{
				$oMap->UpdateHidden($relitem, B_TRUE);
			}
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oPlace->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
	case ACT_RELON:
		if (isset($item) && !empty($item))
		{
			if (isset($relitem) && !empty($relitem))
			{
				$oMap->UpdateHidden($relitem, B_FALSE);
			}
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oPlace->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
	default:
	case ACT_VIEW:
		if (isset($item) && !empty($item))
		{
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oPlace->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
}
//=========================================================
function showForm($aRows=null)
{
	global $page, $cat, $oPage, $oPlace, $oUser, $oLabel, $aLanguages, $aAbbrLanguages, $nDefCity;
	
	require_once "../FCKeditor/fckeditor.php";
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->PlaceID;
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
	}
	echo getLabel('strRequired').'<br />'."\n";
?>
<script type="text/javascript">
<!--
function fCheck(frm) {
<? foreach ($aLanguages as $key=>$val) {?>
	if (!valEmpty("title<?=$aAbbrLanguages[$key]?>", "<?=getLabel('strEnter', $key).getLabel('strPlaceName', $key)?>")) return false;
<?}?>
	//if (!valOption("parent_page_id", "<?=getLabel('strSelect').getLabel('strParentPage')?>")) return false;
	if (!valOption("city_id", "<?=getLabel('strSelect').getLabel('strCity')?>")) return false;
	return true;
}
//-->
</script>
<form action="<?=setPage($page, 0, $itemID, ACT_SAVE).keepFilter().keepContext()?>" enctype="multipart/form-data" method="post" name="Place" id="Place" onsubmit="return fCheck(this);">
<input type="hidden" name="MAX_FILE_SIZE" value="2550000" />

<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="id"><?=getLabel('strPlaceID')?></label><?=$itemID?>
	<input type="hidden" name="id" id="id" value="<?=$itemID?>" /><br />
	<br />
	
	<label for="parent_page_id"><?=getLabel('strParentPage').formatVal().getLabel('multiple')?></label>
	<select name="parent_page_id[]" id="parent_page_id" multiple="multiple" size="6" class="fldsmall">
	<?
		$aPages = $oPage->ListAllAsArray(null, '', 'place_list', true);
		if (!empty($aRows[DEF_LANG])) 
		{
			$aRelPages = $oPlace->ListPlacePagesAsArray($itemID);
		}
		foreach($aPages as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if (in_array($key, $aRelPages))
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	
	<label for="city_id"><?=getLabel('strCity').formatVal()?></label>
	<select name="city_id" id="city_id" class="fldsmall">
		<option value="0"><?=getLabel('strSelect')?></option>
	<?
		$aCities = getLabel('aCities');
		foreach($aCities as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == $aRows[DEF_LANG]->CityID)
				echo ' selected="selected"';
			elseif (is_null($aRows) && $key == $nDefCity)
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	
	<label for="place_type_id"><?=getLabel('strPlaceType').formatVal()?></label>
	<select name="place_type_id" id="place_type_id" class="fldsmall">
		<option value="0"><?=getLabel('strSelect')?></option>
	<?
		$aPlaceTypes = getLabel('aPlaceTypes');
		foreach($aPlaceTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == $aRows[DEF_LANG]->PlaceTypeID)
				echo ' selected="selected"';
			elseif (is_null($aRows) && $key == $cat)
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	
	<hr />
	<? if (!empty($aRows[DEF_LANG]) )
		{
			$sMainImageFile = '../'.UPLOAD_DIR.IMG_PLACE_THUMB.$itemID.'.'.EXT_IMG;
			if (!is_file($sMainImageFile))
			{
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
			}
	?>
	<label><?=getLabel('strPlaceImage')?></label>
	<?=drawImage($sMainImageFile)?><br />
	<br />
	<?}?>
	<label for="main_image"><?=getLabel('strPlaceImageFull')?></label>
	<input type="file" name="main_image" id="main_image" size="47" accept="image/x-jpg" class="btn" /><br />
	<br class="clear" />
	<hr />
	<br />
	
	<!--label for="start_time?>"><?=getLabel('strStartTime')?></label>
	<input type="text" name="start_time" id="start_time" value="<?=IIF(!empty($aRows[DEF_LANG]), formatTime($aRows[DEF_LANG]->StartTime), '')?>" maxlength="5" class="fldfilter" /><br />
	<br /-->

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

<fieldset title="<?=getLabel('strCategory')?>" class="half">
	<legend><?=getLabel('strCategory')?></legend>
	<label for="cuisine"><?=getLabel('strCuisine').getLabel('multiple')?></label>
	<select name="cuisine[]" id="cuisine" multiple="multiple" size="10" class="fldsmall">
	<?
		$aCuisine = $oLabel->ListAllAsArray(GRP_CUISINE);
		if (!empty($aRows[DEF_LANG])) 
		{
			$aRelLabels = $oPlace->ListPlaceLabelsAsArray($itemID, GRP_CUISINE);
		}
		foreach($aCuisine as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if (in_array($key, $aRelLabels))
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	
	<label for="atmosphere"><?=getLabel('strAtmosphere').getLabel('multiple')?></label>
	<select name="atmosphere[]" id="atmosphere" multiple="multiple" size="4" class="fldsmall">
	<?
		$aAtmosphere = $oLabel->ListAllAsArray(GRP_ATMOS);
		if (!empty($aRows[DEF_LANG])) 
		{
			$aRelLabels = $oPlace->ListPlaceLabelsAsArray($itemID, GRP_ATMOS);
		}
		foreach($aAtmosphere as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if (in_array($key, $aRelLabels))
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	
	<label for="price_category"><?=getLabel('strPriceCategory').getLabel('multiple')?></label>
	<select name="price_category[]" id="price_category" multiple="multiple" size="4" class="fldsmall">
	<?
		$aPriceCategory = $oLabel->ListAllAsArray(GRP_PRICE);
		if (!empty($aRows[DEF_LANG])) 
		{
			$aRelLabels = $oPlace->ListPlaceLabelsAsArray($itemID, GRP_PRICE);
		}
		foreach($aPriceCategory as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if (in_array($key, $aRelLabels))
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	
	<label for="music_style"><?=getLabel('strMusicStyle').getLabel('multiple')?></label>
	<select name="music_style[]" id="music_style" multiple="multiple" size="8" class="fldsmall">
	<?
		$aMusicStyle = $oLabel->ListAllAsArray(GRP_BGNDMUSIC);
		if (!empty($aRows[DEF_LANG])) 
		{
			$aRelLabels = $oPlace->ListPlaceLabelsAsArray($itemID, GRP_BGNDMUSIC);
		}
		foreach($aMusicStyle as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if (in_array($key, $aRelLabels))
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />

</fieldset>
<br class="clear" />
<?
	foreach($aLanguages as $key=>$val)
	{
?>
<fieldset title="<?=$val?>" class="half">
	<legend><?=$val?></legend>
	<label for="title<?=$aAbbrLanguages[$key]?>"><?=getLabel('strPlaceName', $key).formatVal()?></label><br />
	<input type="text" name="title<?=$aAbbrLanguages[$key]?>" id="title<?=$aAbbrLanguages[$key]?>"
		value="<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->Title), '')?>" maxlength="255" class="fld" /><br />
	<br />
	<label for="short<?=$aAbbrLanguages[$key]?>"><?=getLabel('strShortTitle', $key)?></label><br />
	<input type="text" name="short<?=$aAbbrLanguages[$key]?>" id="short<?=$aAbbrLanguages[$key]?>"
		value="<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->ShortTitle), '')?>" maxlength="128" class="fld" /><br />
	<br />
	<label for="text<?=$aAbbrLanguages[$key]?>"><?=getLabel('strDescription', $key)?></label><br />
	<?
		$fck = new FCKeditor('text'.$aAbbrLanguages[$key], '475', '400', 'DiagonaliDefault', IIF(!empty($aRows[$key]), $aRows[$key]->Description, ''));
		$fck->Create();
	?>
	<br />
	<!--label for="address<?=$aAbbrLanguages[$key]?>"><?=getLabel('strAddress', $key)?></label><br />
	<?
		//$fck = new FCKeditor('address'.$aAbbrLanguages[$key], '475', '120', 'DiagonaliBasic', IIF(!empty($aRows[$key]), $aRows[$key]->Address, ''));
		//$fck->Create();
	?>
	<br /-->
	<label for="work_time<?=$aAbbrLanguages[$key]?>"><?=getLabel('strWorkingTime', $key)?></label><br />
	<input type="text" name="work_time<?=$aAbbrLanguages[$key]?>" id="work_time<?=$aAbbrLanguages[$key]?>"
		value="<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->WorkingTime), '')?>" maxlength="128" class="fld" /><br />
	<br />
	<!--label for="description<?=$aAbbrLanguages[$key]?>"><?=getLabel('strMetaDescription', $key)?></label><br />
	<textarea cols="36" rows="3" name="description<?=$aAbbrLanguages[$key]?>" id="description<?=$aAbbrLanguages[$key]?>"><?=IIF(!empty($aRows[$key]), $aRows[$key]->MetaDescription, '')?></textarea><br />
	<br /-->
	<label for="keywords<?=$aAbbrLanguages[$key]?>"><?=getLabel('strMetaKeywords', $key)?></label><br />
	<textarea cols="36" rows="3" name="keywords<?=$aAbbrLanguages[$key]?>" id="keywords<?=$aAbbrLanguages[$key]?>"><?=IIF(!empty($aRows[$key]), $aRows[$key]->MetaKeywords, '')?></textarea><br />
	<br />
</fieldset>
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
	global $page, $oPage, $oPlace, $oUser, $oLabel, $aLanguages, $aEntityTypes, $oMap;
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	$aCities = getLabel('aCities');
	$aPlaceTypes = getLabel('aPlaceTypes');
	$sPages = '';
	$sCuisine = '';
	$sAtmosphere = '';
	$sPriceCategory = '';
	$sMusicStyle = '';
	$sCities = '';
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->PlaceID;
	}
	if (!empty($itemID))
	{
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
		
		$aPages = $oPage->ListAllAsArray(null, '', '', true);
		$aRelPages = $oPlace->ListPlacePagesAsArray($itemID);
		if (is_array($aRelPages) && count($aRelPages)>0)
		{
			foreach($aRelPages as $cat)
				$sPages .= $aPages[$cat].'<br/>';
		}
		else
			$sPages = '<em>'.getLabel('strNone').'</em><br />';
		
		$aCuisine = $oLabel->ListAllAsArray(GRP_CUISINE);
		$aRelLabels = $oPlace->ListPlaceLabelsAsArray($itemID, GRP_CUISINE);
		if (is_array($aRelLabels) && count($aRelLabels)>0)
		{
			foreach($aRelLabels as $cat)
				$sCuisine .= $aCuisine[$cat].', ';
		}
		else
			$sCuisine = '<em>'.getLabel('strNone').'</em>';
		
		$aAtmosphere = $oLabel->ListAllAsArray(GRP_ATMOS);
		$aRelLabels = $oPlace->ListPlaceLabelsAsArray($itemID, GRP_ATMOS);
		if (is_array($aRelLabels) && count($aRelLabels)>0)
		{
			foreach($aRelLabels as $cat)
				$sAtmosphere .= $aAtmosphere[$cat].', ';
		}
		else
			$sAtmosphere = '<em>'.getLabel('strNone').'</em>';
		
		$aPriceCategory = $oLabel->ListAllAsArray(GRP_PRICE);
		$aRelLabels = $oPlace->ListPlaceLabelsAsArray($itemID, GRP_PRICE);
		if (is_array($aRelLabels) && count($aRelLabels)>0)
		{
			foreach($aRelLabels as $cat)
				$sPriceCategory .= $aPriceCategory[$cat].', ';
		}
		else
			$sPriceCategory = '<em>'.getLabel('strNone').'</em>';
		
		$aMusicStyle = $oLabel->ListAllAsArray(GRP_BGNDMUSIC);
		$aRelLabels = $oPlace->ListPlaceLabelsAsArray($itemID, GRP_BGNDMUSIC);
		if (is_array($aRelLabels) && count($aRelLabels)>0)
		{
			foreach($aRelLabels as $cat)
				$sMusicStyle .= $aMusicStyle[$cat].', ';
		}
		else
			$sMusicStyle = '<em>'.getLabel('strNone').'</em>';
		
		$sImageLink = '<br /><a href="'.setPage($page, 0, $itemID, ACT_DELETE_IMG).'">'.getLabel('delete').'</a>';
		$sMainImageFile = '../'.UPLOAD_DIR.IMG_PLACE_THUMB.$itemID.'.'.EXT_IMG;
		if (!is_file($sMainImageFile))
		{
			$sImageLink = '';
			$sMainImageFile = '../'.UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
		}
?>
<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label><?=getLabel('strPlaceID')?></label>
	<?=$aRows[DEF_LANG]->PlaceID?><br />
	<br />
	<label><?=getLabel('strParentPage')?></label>
	<div><?=$sPages?></div><br />
	
	<label><?=getLabel('strCity')?></label>
	<?=$aCities[$aRows[DEF_LANG]->CityID]?><br />
	<br />
	<label><?=getLabel('strPlaceType')?></label>
	<?=$aPlaceTypes[$aRows[DEF_LANG]->PlaceTypeID]?><br />
	<br />
	<label><?=getLabel('strPlaceImage')?></label>
	<div><?=drawImage($sMainImageFile).$sImageLink?></div>
	<br />
	<!--label><?=getLabel('strStartTime')?></label>
	<?=formatTime($aRows[DEF_LANG]->StartTime)?><br />
	<br /-->
	
	<label><?=getLabel('strCuisine')?></label>
	<div><?=$sCuisine?></div><br />
	<br />
	<label><?=getLabel('strAtmosphere')?></label>
	<div><?=$sAtmosphere?></div><br />
	<br />
	<label><?=getLabel('strPriceCategory')?></label>
	<div><?=$sPriceCategory?></div><br />
	<br />
	<label><?=getLabel('strMusicStyle')?></label>
	<div><?=$sMusicStyle?></div><br />
	<br />
	
	<label><?=getLabel('strHide')?></label>
	<?=$aYesNo[$aRows[DEF_LANG]->IsHidden]?><br />
	<br />
	<label><?=getLabel('strLastUpdate')?></label>
	<?=IIF(!empty($aRows[DEF_LANG]), $aRows[DEF_LANG]->LastUpdate, '').getLabel('strByUser').IIF(!empty($aRows[DEF_LANG]), $rUser->Username.' ('.$rUser->FirstName.' '.$rUser->LastName.')', '')?><br />
	
	<br /><hr />
	<label><?=getLabel('strMap')?></label>
<?
	$rMap = $oMap->GetByEntityID($itemID, null, true);
	//print_r($rMap);
	if (!is_null($rMap))
	{
?>
	<iframe border="0" frameborder="no" framespacing="0" name="emaps" width="322" height="262" src="http://<?=SITE_URL?>/map/place_map.php5?<?=ARG_ID?>=<?=$itemID?>&amp;<?=ARG_LANG?>=<?=DEF_LANG?>&amp;<?=ARG_ACT?>=<?=ACT_MAP?>"></iframe><br />
	<br />
	
	<label><?=getLabel('strHide')?></label>
	<?=$aYesNo[$rMap->IsHidden]?> | 
	<?=IIF($rMap->IsHidden == B_TRUE,
	       '<a href="'.setPage($page, 0, $itemID, ACT_RELON, $rMap->MapLocationID).keepFilter().keepContext().'">'.getLabel('show'),
	       '<a href="'.setPage($page, 0, $itemID, ACT_RELOFF, $rMap->MapLocationID).keepFilter().keepContext().'">'.getLabel('hide'))?></a>
	<br />
	<br />
<?
		echo getLabel('strMapEditNote');
	}
	else
	{
		echo getLabel('strNone').'<br />'.getLabel('strMapAddNote');
	}
?>
</fieldset>
<iframe name="related" style="float:left" class="related" src="<?=RELATED_PAGE.'?'.ARG_PAGE.'='.ENT_HOME.'&amp;'.ARG_RELID.'='.$itemID.'&amp;'.ARG_CAT.'='.$aEntityTypes[ENT_PLACE]?>"></iframe>
<br class="clear" />
<?
	foreach($aLanguages as $key=>$val)
	{
?>
<fieldset title="<?=$val?>" class="half">
	<legend><?=$val?></legend>
	<label><?=getLabel('strPlaceName', $key)?></label>
	<?=IIF(!empty($aRows[$key]), $aRows[$key]->Title, '')?><br />
	<br />
	
	<label><?=getLabel('strShortTitle', $key)?></label>
	<?=IIF(!empty($aRows[$key]), $aRows[$key]->ShortTitle, '')?><br />
	<br />
	
	<label><?=getLabel('strDescription', $key)?></label>
	<div><?=IIF(!empty($aRows[$key]), $aRows[$key]->Description, '')?></div><br />
	<br />
	
	<!--label><?=getLabel('strAddress', $key)?></label>
	<div><?=IIF(!empty($aRows[$key]), $aRows[$key]->Address, '')?></div><br />
	<br /-->
	
	<label><?=getLabel('strWorkingTime', $key)?></label>
	<div><?=IIF(!empty($aRows[$key]), $aRows[$key]->WorkingTime, '')?></div><br />
	<br />
	
	<!--label><?=getLabel('strMetaDescription', $key)?></label>
	<div><?=IIF(!empty($aRows[$key]), $aRows[$key]->MetaDescription, '')?></div><br />
	<br /-->
	
	<label><?=getLabel('strMetaKeywords', $key)?></label>
	<div><?=IIF(!empty($aRows[$key]), $aRows[$key]->MetaKeywords, '')?></div><br />
	<br />
</fieldset>
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