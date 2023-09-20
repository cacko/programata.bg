<?php
if (!$bInSite) die();
//=========================================================
$aFilter = array(ARG_RELID, ARG_CAT);
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
			$item = $oAddress->Insert(getPostedArg('address_type_id'),
						getPostedArg('city_id'),
						getPostedArg('street'.$aAbbrLanguages[DEF_LANG]), 
						getPostedArg('zip'), 
						$relitem, //getPostedArg('entity_id'), 
						$cat, //getPostedArg('entity_type'),
						getPostedArg('hidden')); // primary record
		}
		foreach($aLanguages as $key=>$val)
		{
			$oAddress->Update($item, 
					getPostedArg('address_type_id'),
					getPostedArg('city_id'),
					getPostedArg('street'.$aAbbrLanguages[$key]), 
					getPostedArg('zip'), 
					$relitem, //getPostedArg('entity_id'), 
					$cat, //getPostedArg('entity_type'),
					getPostedArg('hidden'),
					$key); // update all
		}
		//==========================================
		if (!$item)
			$sMsg .= getLabel('strSaveFailed'); // failed message
		else
			$sMsg .= getLabel('strSaveOK'); // ok message
		echo $sMsg.'<br /><br />';
		echo $strFilterListLink;
		$aRows = array();
		foreach($aLanguages as $key=>$val)
			$aRows[$key] = $oAddress->GetByID($item, $key); // get by id
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
				$aRows[$key] = $oAddress->GetByID($item, $key); // get by id
			if (count($aRows)>0) showForm($aRows); // load form
		}
		break;
	case ACT_DELETE:
		if (isset($item) && !empty($item))
		{
			$oAddress->Delete($item);
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
				$aRows[$key] = $oAddress->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
}
//=========================================================
function showForm($aRows=null)
{
	global $page, $oPage, $oAddress, $oUser, $aLanguages, $aAbbrLanguages;
	
	//require_once "../FCKeditor/fckeditor.php";
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->AddressID;
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
	}
	echo getLabel('strRequired').'<br />'."\n";
?>
<script type="text/javascript">
<!--
function fCheck(frm) {
<? foreach ($aLanguages as $key=>$val) {?>
	if (!valEmpty("street<?=$aAbbrLanguages[$key]?>", "<?=getLabel('strEnter', $key).getLabel('strStreet', $key)?>")) return false;
<?}?>
	if (!valOption("address_type_id", "<?=getLabel('strSelect').getLabel('strAddressType')?>")) return false;
	if (!valOption("city_id", "<?=getLabel('strSelect').getLabel('strCity')?>")) return false;
	return true;
}
//-->
</script>
<form action="<?=setPage($page, 0, $itemID, ACT_SAVE).keepFilter().keepContext()?>" enctype="multipart/form-data" method="post" name="Address" id="Address" onsubmit="return fCheck(this);">

<fieldset title="<?=getLabel('strCommonData')?>">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="id"><?=getLabel('strAddressID')?></label><?=$itemID?>
	<input type="hidden" name="id" id="id" value="<?=$itemID?>" /><br />
	<br />
	
	<label for="address_type_id"><?=getLabel('strAddressType').formatVal()?></label><br />
	<select name="address_type_id" id="address_type_id" class="fldsmall">
		<option value="0"><?=getLabel('strSelect');?></option>
	<?
		$aAddressTypes = getLabel('aAddressTypes');
		foreach($aAddressTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if ($aRows[DEF_LANG]->AddressTypeID == $key)
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	
	<label for="city_id"><?=getLabel('strCity').formatVal()?></label><br />
	<select name="city_id" id="city_id" class="fldsmall">
		<option value="0"><?=getLabel('strSelect');?></option>
	<?
		$aCities = getLabel('aCitiesAll');
		foreach($aCities as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if ($aRows[DEF_LANG]->CityID == $key)
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	
	<label for="zip?>"><?=getLabel('strZip')?></label>
	<input type="text" name="zip" id="zip"
		value="<?=IIF(!empty($aRows[DEF_LANG]), htmlspecialchars($aRows[DEF_LANG]->Zip), '')?>" maxlength="10" class="fldfilter" /><br />
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

<?
	foreach($aLanguages as $key=>$val)
	{
?>
<fieldset title="<?=$val?>" style="float:left">
	<legend><?=$val?></legend>
	<label for="street<?=$aAbbrLanguages[$key]?>"><?=getLabel('strStreet', $key).formatVal()?></label><br />
	<input type="text" name="street<?=$aAbbrLanguages[$key]?>" id="street<?=$aAbbrLanguages[$key]?>"
		value="<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->Street), '')?>" maxlength="255" class="fldsmall" /><br />
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
	global $page, $oUser, $aLanguages;
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	$aAddressTypes = getLabel('aAddressTypes');
	$aCities = getLabel('aCitiesAll');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->AddressID;
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
	}
?>
<fieldset title="<?=getLabel('strCommonData')?>">
	<legend><?=getLabel('strCommonData')?></legend>
	<label><?=getLabel('strAddressID')?></label>
	<?=$aRows[DEF_LANG]->AddressID?><br />
	<br />
	
	<label><?=getLabel('strAddressType')?></label>
	<?=$aAddressTypes[$aRows[DEF_LANG]->AddressTypeID]?><br />
	<br />
	<label><?=getLabel('strCity')?></label>
	<?=$aCities[$aRows[DEF_LANG]->CityID]?><br />
	<br />
	<label><?=getLabel('strZip')?></label>
	<?=$aRows[DEF_LANG]->Zip?><br />
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
<fieldset title="<?=$val?>">
	<legend><?=$val?></legend>
	<label><?=getLabel('strStreet', $key)?></label>
	<?=IIF(!empty($aRows[$key]), $aRows[$key]->Street, '')?><br />
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
?>