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
			$item = $oPhone->Insert(getPostedArg('phone_type_id'),
						getPostedArg('area'),
						getPostedArg('phone'),
						getPostedArg('ext'), 
						$relitem, //getPostedArg('entity_id'), 
						$cat, //getPostedArg('entity_type'),
						getPostedArg('hidden')); // primary record
		}
		foreach($aLanguages as $key=>$val)
		{
			$oPhone->Update($item, 
					getPostedArg('phone_type_id'),
					getPostedArg('area'),
					getPostedArg('phone'),
					getPostedArg('ext'), 
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
			$aRows[$key] = $oPhone->GetByID($item, $key); // get by id
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
				$aRows[$key] = $oPhone->GetByID($item, $key); // get by id
			if (count($aRows)>0) showForm($aRows); // load form
		}
		break;
	case ACT_DELETE:
		if (isset($item) && !empty($item))
		{
			$oPhone->Delete($item);
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
				$aRows[$key] = $oPhone->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
}
//=========================================================
function showForm($aRows=null)
{
	global $page, $oUser, $aLanguages, $aAbbrLanguages;
	
	//require_once "../FCKeditor/fckeditor.php";
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->PhoneID;
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
	}
	echo getLabel('strRequired').'<br />'."\n";
?>
<script type="text/javascript">
<!--
function fCheck(frm) {
	if (!valEmpty("phone", "<?=getLabel('strEnter').getLabel('strPhone')?>")) return false;
	if (!valOption("phone_type_id", "<?=getLabel('strSelect').getLabel('strPhoneType')?>")) return false;
	return true;
}
//-->
</script>
<form action="<?=setPage($page, 0, $itemID, ACT_SAVE).keepFilter().keepContext()?>" enctype="multipart/form-data" method="post" name="Phone" id="Phone" onsubmit="return fCheck(this);">

<fieldset title="<?=getLabel('strCommonData')?>">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="id"><?=getLabel('strPhoneID')?></label><?=$itemID?>
	<input type="hidden" name="id" id="id" value="<?=$itemID?>" /><br />
	<br />
	
	<label for="phone_type_id"><?=getLabel('strPhoneType').formatVal()?></label><br />
	<select name="phone_type_id" id="phone_type_id" class="fldsmall">
		<option value="0"><?=getLabel('strSelect');?></option>
	<?
		$aPhoneTypes = getLabel('aPhoneTypes');
		foreach($aPhoneTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if ($aRows[DEF_LANG]->PhoneTypeID == $key)
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	
	<label for="phone"><?=getLabel('strArea').'-'.getLabel('strPhone').formatVal().'-'.getLabel('strExt')?></label><br />
	<input type="text" name="area" id="area"
		value="<?=IIF(!empty($aRows[DEF_LANG]), htmlspecialchars($aRows[DEF_LANG]->Area), '')?>" maxlength="8" class="fldsmall" style="width:50px" />&nbsp;-&nbsp;<input type="text" name="phone" id="phone"
		value="<?=IIF(!empty($aRows[DEF_LANG]), htmlspecialchars($aRows[DEF_LANG]->Phone), '')?>" maxlength="16" class="fldsmall" style="width:100px" />&nbsp;-&nbsp;<input type="text" name="ext" id="ext"
		value="<?=IIF(!empty($aRows[DEF_LANG]), htmlspecialchars($aRows[DEF_LANG]->Ext), '')?>" maxlength="8" class="fldsmall" style="width:50px" /><br />
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
	//foreach($aLanguages as $key=>$val)
	//{
?>
<!--fieldset title="<?=$val?>" style="float:left">
	<legend><?=$val?></legend>
</fieldset-->
<?
	//}
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
	$aPhoneTypes = getLabel('aPhoneTypes');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->PhoneID;
	}
	if (!empty($itemID))
	{
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
?>
<fieldset title="<?=getLabel('strCommonData')?>">
	<legend><?=getLabel('strCommonData')?></legend>
	<label><?=getLabel('strPhoneID')?></label>
	<?=$aRows[DEF_LANG]->PhoneID?><br />
	<br />
	<label><?=getLabel('strPhoneType')?></label>
	<?=$aPhoneTypes[$aRows[DEF_LANG]->PhoneTypeID]?><br />
	<br />
	<label><?=getLabel('strArea').'-'.getLabel('strPhone').'-'.getLabel('strExt')?></label></label>
	<?='('.$aRows[DEF_LANG]->Area.') '.$aRows[DEF_LANG]->Phone.' '.$aRows[DEF_LANG]->Ext?><br />
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
}
?>