<?php
if (!$bInSite) die();
//=========================================================
$aFilter = array(ARG_RELID, ARG_CAT);
$strFilterListPlaceHall = '<a href="'.setPage($page).keepFilter().keepContext().'">'.getLabel('strBackToList').'</a><br /><br />';
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
			$item = $oPlaceHall->Insert(getPostedArg('title'.$aAbbrLanguages[DEF_LANG]), 
						$relitem, //getPostedArg('entity_id'), 
						$cat, //getPostedArg('entity_type'),
						getPostedArg('hidden')); // primary record
		}
		foreach($aLanguages as $key=>$val)
		{
			$oPlaceHall->Update($item, 
					getPostedArg('title'.$aAbbrLanguages[$key]), 
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
		echo $strFilterListPlaceHall;
		$aRows = array();
		foreach($aLanguages as $key=>$val)
			$aRows[$key] = $oPlaceHall->GetByID($item, $key); // get by id
		if (count($aRows)>0) showPreview($aRows); // show preview & related PlaceHalls
		break;
	case ACT_ADD:
		showForm(); // load form
		break;
	case ACT_EDIT:
		if (isset($item) && !empty($item))
		{
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oPlaceHall->GetByID($item, $key); // get by id
			if (count($aRows)>0) showForm($aRows); // load form
		}
		break;
	case ACT_DELETE:
		if (isset($item) && !empty($item))
		{
			$oPlaceHall->Delete($item);
			echo getLabel('strDeleteOK').'<br /><br />'; // show ok message
			echo $strFilterListPlaceHall;
		}
		break;
	default:
	case ACT_VIEW:
		if (isset($item) && !empty($item))
		{
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oPlaceHall->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related PlaceHalls
		}
		break;
}
//=========================================================
function showForm($aRows=null)
{
	global $page, $oPage, $oPlaceHall, $oUser, $aLanguages, $aAbbrLanguages;
	
	//require_once "../FCKeditor/fckeditor.php";
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->HallID;
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
	}
	echo getLabel('strRequired').'<br />'."\n";
?>
<script type="text/javascript">
<!--
function fCheck(frm) {
<? foreach ($aLanguages as $key=>$val) {?>
	if (!valEmpty("title<?=$aAbbrLanguages[$key]?>", "<?=getLabel('strEnter', $key).getLabel('strPlaceHallTitle', $key)?>")) return false;
<?}?>
	return true;
}
//-->
</script>
<form action="<?=setPage($page, 0, $itemID, ACT_SAVE).keepFilter().keepContext()?>" enctype="multipart/form-data" method="post" name="PlaceHall" id="PlaceHall" onsubmit="return fCheck(this);">

<fieldset title="<?=getLabel('strCommonData')?>">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="id"><?=getLabel('strPlaceHallID')?></label><?=$itemID?>
	<input type="hidden" name="id" id="id" value="<?=$itemID?>" /><br />
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
	<label for="title<?=$aAbbrLanguages[$key]?>"><?=getLabel('strHallTitle', $key).formatVal()?></label><br />
	<input type="text" name="title<?=$aAbbrLanguages[$key]?>" id="title<?=$aAbbrLanguages[$key]?>"
		value="<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->Title), '')?>" maxlength="255" class="fldsmall" /><br />
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
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->HallID;
	}
	if (!empty($itemID))
	{
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
?>
<fieldset title="<?=getLabel('strCommonData')?>">
	<legend><?=getLabel('strCommonData')?></legend>
	<label><?=getLabel('strPlaceHallID')?></label>
	<?=$aRows[DEF_LANG]->HallID?><br />
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
	<label><?=getLabel('strHallTitle', $key)?></label>
	<?=IIF(!empty($aRows[$key]), $aRows[$key]->Title, '')?><br />
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