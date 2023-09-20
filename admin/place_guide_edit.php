<?php
if (!$bInSite) die();
//=========================================================
$aFilter = array('entity_id', ARG_RELID, ARG_CAT, 'start_date', 'end_date');
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
			
			$item = $oPlaceGuide->Insert($relitem, //getPostedArg('entity_id'), 
						$cat, //getPostedArg('entity_type'),
						getPostedArg('has_entrance_fee'),
						getPostedArg('nr_seats'),
						getPostedArg('has_dj'),
						getPostedArg('has_live_music'),
						getPostedArg('has_karaoke'),
						getPostedArg('has_bgnd_music'),
						getPostedArg('has_face_control'),
						getPostedArg('has_cuisine'),
						getPostedArg('has_terrace'),
						getPostedArg('has_clima'),
						getPostedArg('has_parking'),
						getPostedArg('has_wardrobe'),
						getPostedArg('has_card_payment'),
						getPostedArg('has_wifi'),
						getPostedArg('has_delivery'),
						parseDate(getPostedArg('start_date')), 
						parseDate(getPostedArg('end_date')), 
						getPostedArg('hidden')); // primary record
		}
		//foreach($aLanguages as $key=>$val)
		//{
			$oPlaceGuide->Update($item, 
						$relitem, //getPostedArg('entity_id'), 
						$cat, //getPostedArg('entity_type'),
						getPostedArg('has_entrance_fee'),
						getPostedArg('nr_seats'),
						getPostedArg('has_dj'),
						getPostedArg('has_live_music'),
						getPostedArg('has_karaoke'),
						getPostedArg('has_bgnd_music'),
						getPostedArg('has_face_control'),
						getPostedArg('has_cuisine'),
						getPostedArg('has_terrace'),
						getPostedArg('has_clima'),
						getPostedArg('has_parking'),
						getPostedArg('has_wardrobe'),
						getPostedArg('has_card_payment'),
						getPostedArg('has_wifi'),
						getPostedArg('has_delivery'),
						parseDate(getPostedArg('start_date')), 
						parseDate(getPostedArg('end_date')), 
						getPostedArg('hidden')); //, $key); // update all
		//}
		//==========================================
		if (!$item)
			$sMsg .= getLabel('strSaveFailed'); // failed message
		else
			$sMsg .= getLabel('strSaveOK'); // ok message
		echo $sMsg.'<br /><br />';
		//echo $strFilterListLink;
		$aRows = array();
		foreach($aLanguages as $key=>$val)
			$aRows[$key] = $oPlaceGuide->GetByID($item, $key); // get by id
		if (count($aRows)>0) showForm($aRows); // show preview & related links
		break;
	case ACT_ADD:
	case ACT_EDIT:
		if (isset($relitem) && !empty($relitem))
		{
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oPlaceGuide->GetByPlaceID($relitem, $key); // get by id
			if (count($aRows)>0) showForm($aRows); // load form
		}
		break;
	case ACT_DELETE:
		if (isset($item) && !empty($item))
		{
			$oPlaceGuide->Delete($item);
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
				$aRows[$key] = $oPlaceGuide->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
}
//=========================================================
function showForm($aRows=null)
{
	global $page, $oUser, $aLanguages, $aAbbrLanguages;
	
	require_once "../FCKeditor/fckeditor.php";
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->PlaceGuideID;
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
	}
	echo getLabel('strRequired').'<br />'."\n";
?>
<script type="text/javascript">
<!--
function fCheck(frm) {
<? foreach ($aLanguages as $key=>$val) {?>
	//if (!valEmpty("PlaceGuide<?=$aAbbrLanguages[$key]?>", "<?=getLabel('strEnter', $key).getLabel('strPlaceGuide', $key)?>")) return false;
<?}?>
	//if (!valEmpty("start_date", "<?=getLabel('strEnter').getLabel('strStartDate')?>")) return false;
	//if (!valEmpty("end_date", "<?=getLabel('strEnter').getLabel('strEndDate')?>")) return false;
	return true;
}
//-->
</script>
<form action="<?=setPage($page, 0, $itemID, ACT_SAVE).keepFilter().keepContext()?>" enctype="multipart/form-data" method="post" name="PlaceGuide" id="PlaceGuide" onsubmit="return fCheck(this);">

<fieldset title="<?=getLabel('strCommonData')?>">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="id"><?=getLabel('strPlaceGuideID')?></label><?=$itemID?>
	<input type="hidden" name="id" id="id" value="<?=$itemID?>" /><br />
	<br />
	
	<label for="nr_seats"><?=getLabel('strNrSeats')?></label>
	<input type="text" name="nr_seats" id="nr_seats" maxlength="10" class="fldfilter"
		value="<?=IIF(!empty($aRows[DEF_LANG]), htmlspecialchars($aRows[DEF_LANG]->NrSeats), '')?>" /><br />
	<br />
	
	<label><?=getLabel('strEntranceFee')?></label>
<?
	reset($aYesNo);
	while(list($key, $value) = each($aYesNo)) 
	{
		?>
		<input type="radio" name="has_entrance_fee" id="has_entrance_fee_<?=$key?>" value="<?=$key?>"<?=IIF($key==$aRows[DEF_LANG]->HasEntranceFee,' checked="checked"','')?> />
		<label class="list" for="has_entrance_fee_<?=$key?>"><?=$value?></label>
<? 	} ?>
	<br class="clear" />
	<br />
	
	<label><?=getLabel('strDJ')?></label>
<?
	reset($aYesNo);
	while(list($key, $value) = each($aYesNo)) 
	{
		?>
		<input type="radio" name="has_dj" id="has_dj_<?=$key?>" value="<?=$key?>"<?=IIF($key==$aRows[DEF_LANG]->HasDJ,' checked="checked"','')?> />
		<label class="list" for="has_dj_<?=$key?>"><?=$value?></label>
<? 	} ?>
	<br class="clear" />
	<br />
	
	<label><?=getLabel('strLiveMusic')?></label>
<?
	reset($aYesNo);
	while(list($key, $value) = each($aYesNo)) 
	{
		?>
		<input type="radio" name="has_live_music" id="has_live_music_<?=$key?>" value="<?=$key?>"<?=IIF($key==$aRows[DEF_LANG]->HasLiveMusic,' checked="checked"','')?> />
		<label class="list" for="has_live_music_<?=$key?>"><?=$value?></label>
<? 	} ?>
	<br class="clear" />
	<br />
	
	<label><?=getLabel('strKaraoke')?></label>
<?
	reset($aYesNo);
	while(list($key, $value) = each($aYesNo)) 
	{
		?>
		<input type="radio" name="has_karaoke" id="has_karaoke_<?=$key?>" value="<?=$key?>"<?=IIF($key==$aRows[DEF_LANG]->HasKaraoke,' checked="checked"','')?> />
		<label class="list" for="has_karaoke_<?=$key?>"><?=$value?></label>
<? 	} ?>
	<br class="clear" />
	<br />
	
	<!--label><?=getLabel('strBgndMusic')?></label>
<?
	reset($aYesNo);
	while(list($key, $value) = each($aYesNo)) 
	{
		?>
		<input type="radio" name="has_bgnd_music" id="has_bgnd_music_<?=$key?>" value="<?=$key?>"<?=IIF($key==$aRows[DEF_LANG]->HasBgndMusic,' checked="checked"','')?> />
		<label class="list" for="has_bgnd_music_<?=$key?>"><?=$value?></label>
<? 	} ?>
	<br class="clear" />
	<br /-->
	
	<label><?=getLabel('strFaceControl')?></label>
<?
	reset($aYesNo);
	while(list($key, $value) = each($aYesNo)) 
	{
		?>
		<input type="radio" name="has_face_control" id="has_face_control_<?=$key?>" value="<?=$key?>"<?=IIF($key==$aRows[DEF_LANG]->HasFaceControl,' checked="checked"','')?> />
		<label class="list" for="has_face_control_<?=$key?>"><?=$value?></label>
<? 	} ?>
	<br class="clear" />
	<br />
	
	<label><?=getLabel('strCuisine')?></label>
<?
	reset($aYesNo);
	while(list($key, $value) = each($aYesNo)) 
	{
		?>
		<input type="radio" name="has_cuisine" id="has_cuisine_<?=$key?>" value="<?=$key?>"<?=IIF($key==$aRows[DEF_LANG]->HasCuisine,' checked="checked"','')?> />
		<label class="list" for="has_cuisine_<?=$key?>"><?=$value?></label>
<? 	} ?>
	<br class="clear" />
	<br />
	
	<label><?=getLabel('strTerrace')?></label>
<?
	reset($aYesNo);
	while(list($key, $value) = each($aYesNo)) 
	{
		?>
		<input type="radio" name="has_terrace" id="has_terrace_<?=$key?>" value="<?=$key?>"<?=IIF($key==$aRows[DEF_LANG]->HasTerrace,' checked="checked"','')?> />
		<label class="list" for="has_terrace_<?=$key?>"><?=$value?></label>
<? 	} ?>
	<br class="clear" />
	<br />
	
	<label><?=getLabel('strClima')?></label>
<?
	reset($aYesNo);
	while(list($key, $value) = each($aYesNo)) 
	{
		?>
		<input type="radio" name="has_clima" id="has_clima_<?=$key?>" value="<?=$key?>"<?=IIF($key==$aRows[DEF_LANG]->HasClima,' checked="checked"','')?> />
		<label class="list" for="has_clima_<?=$key?>"><?=$value?></label>
<? 	} ?>
	<br class="clear" />
	<br />
	
	<label><?=getLabel('strParking')?></label>
<?
	reset($aYesNo);
	while(list($key, $value) = each($aYesNo)) 
	{
		?>
		<input type="radio" name="has_parking" id="has_parking_<?=$key?>" value="<?=$key?>"<?=IIF($key==$aRows[DEF_LANG]->HasParking,' checked="checked"','')?> />
		<label class="list" for="has_parking_<?=$key?>"><?=$value?></label>
<? 	} ?>
	<br class="clear" />
	<br />
	
	<label><?=getLabel('strWardrobe')?></label>
<?
	reset($aYesNo);
	while(list($key, $value) = each($aYesNo)) 
	{
		?>
		<input type="radio" name="has_wardrobe" id="has_wardrobe_<?=$key?>" value="<?=$key?>"<?=IIF($key==$aRows[DEF_LANG]->HasWardrobe,' checked="checked"','')?> />
		<label class="list" for="has_wardrobe_<?=$key?>"><?=$value?></label>
<? 	} ?>
	<br class="clear" />
	<br />
	
	<label><?=getLabel('strCardPayment')?></label>
<?
	reset($aYesNo);
	while(list($key, $value) = each($aYesNo)) 
	{
		?>
		<input type="radio" name="has_card_payment" id="has_card_payment_<?=$key?>" value="<?=$key?>"<?=IIF($key==$aRows[DEF_LANG]->HasCardPayment,' checked="checked"','')?> />
		<label class="list" for="has_card_payment_<?=$key?>"><?=$value?></label>
<? 	} ?>
	<br class="clear" />
	<br />
	
	<label><?=getLabel('strWifi')?></label>
<?
	reset($aYesNo);
	while(list($key, $value) = each($aYesNo)) 
	{
		?>
		<input type="radio" name="has_wifi" id="has_wifi_<?=$key?>" value="<?=$key?>"<?=IIF($key==$aRows[DEF_LANG]->HasWifi,' checked="checked"','')?> />
		<label class="list" for="has_wifi_<?=$key?>"><?=$value?></label>
<? 	} ?>
	<br class="clear" />
	<br />
	
	<label><?=getLabel('strDelivery')?></label>
<?
	reset($aYesNo);
	while(list($key, $value) = each($aYesNo)) 
	{
		?>
		<input type="radio" name="has_delivery" id="has_delivery_<?=$key?>" value="<?=$key?>"<?=IIF($key==$aRows[DEF_LANG]->HasDelivery,' checked="checked"','')?> />
		<label class="list" for="has_delivery_<?=$key?>"><?=$value?></label>
<? 	} ?>
	<br class="clear" />
	<br />
	
	<label for="start_date"><?=getLabel('strVacationStartDate')?></label>
	<input type="text" name="start_date" id="start_date" maxlength="10" class="fldfilter"
		value="<?=IIF(!empty($aRows[DEF_LANG]) && $aRows[DEF_LANG]->VacationStartDate != DEFAULT_DATE_DB_VALUE, formatDate(htmlspecialchars($aRows[DEF_LANG]->VacationStartDate)), '')?>" 
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" /><br />
	<br />
	
	<label for="end_date"><?=getLabel('strVacationEndDate')?></label>
	<input type="text" name="end_date" id="end_date" maxlength="10" class="fldfilter"
		value="<?=IIF(!empty($aRows[DEF_LANG]) && $aRows[DEF_LANG]->VacationEndDate != DEFAULT_DATE_DB_VALUE, formatDate(htmlspecialchars($aRows[DEF_LANG]->VacationEndDate)), '')?>" 
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
	
	<label><?=getLabel('strLastUpdate')?></label>
	<?=IIF(!empty($aRows[DEF_LANG]), $aRows[DEF_LANG]->LastUpdate, '').IIF(!is_null($rUser), getLabel('strByUser').$rUser->Username.' ('.$rUser->FirstName.' '.$rUser->LastName.')', '')?><br />
</fieldset>

<?
	foreach($aLanguages as $key=>$val)
	{
?>
<!--fieldset title="<?=$val?>" style="float:left">
	<legend><?=$val?></legend>
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
	global $page, $oUser, $aLanguages;
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->PlaceGuideID;
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
	}
?>
<fieldset title="<?=getLabel('strCommonData')?>">
	<legend><?=getLabel('strCommonData')?></legend>
	<label><?=getLabel('strPlaceGuideID')?></label>
	<?=$aRows[DEF_LANG]->PlaceGuideID?><br />
	<br />
	<label><?=getLabel('strVacationStartDate')?></label>
	<?=formatDate($aRows[DEF_LANG]->VacationStartDate, getLabel('strNone'))?><br />
	<br />
	<label><?=getLabel('strVacationEndDate')?></label>
	<?=formatDate($aRows[DEF_LANG]->VacationEndDate, getLabel('strNone'))?><br />
	<br />
	<label><?=getLabel('strHide')?></label>
	<?=$aYesNo[$aRows[DEF_LANG]->IsHidden]?><br />
	<br />
	<label><?=getLabel('strLastUpdate')?></label>
	<?=IIF(!empty($aRows[DEF_LANG]), $aRows[DEF_LANG]->LastUpdate, '').getLabel('strByUser').IIF(!empty($aRows[DEF_LANG]), $rUser->Username.' ('.$rUser->FirstName.' '.$rUser->LastName.')', '')?><br />
</fieldset>
	
<?
	foreach($aLanguages as $key=>$val)
	{
?>
<!--fieldset title="<?=$val?>">
	<legend><?=$val?></legend>
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
?>