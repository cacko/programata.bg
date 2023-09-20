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
		
			$oPlace->UpdateXY($item, 
					getPostedArg('long'),
					getPostedArg('lat')); // update all
		//==========================================
		if (!$item)
			$sMsg .= getLabel('strSaveFailed'); // failed message
		else
			$sMsg .= getLabel('strSaveOK'); // ok message
		echo $sMsg.'<br /><br />';
		echo $strFilterListLink;
		break;
	case ACT_EDIT:
		if (isset($item) && !empty($item))
		{
			$rs = $oPlace->GetXY($item);
			if(mysql_num_rows($rs))
			{
				$aRows = array();
				$aRows = mysql_fetch_object($rs);
				showForm($aRows);
			}
		}
		break;
	default:
		break;
}
//=========================================================
function showForm($aRows=null)
{
	global $page, $oPage, $oPlace, $oUser, $aLanguages, $aAbbrLanguages;
	
	//require_once "../FCKeditor/fckeditor.php";
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	if (!empty($aRows))
	{
		$itemID = $aRows->PlaceID;
	}
	echo getLabel('strRequired').'<br />'."\n";
?>
<script type="text/javascript">
<!--
function fCheck(frm) {
	if (!valEmpty("lat", "<?=getLabel('strEnter', $key).getLabel('strLat', $key)?>")) return false;
	if (!valEmpty("long", "<?=getLabel('strEnter', $key).getLabel('strLong', $key)?>")) return false;
	return true;
}
//-->
</script>
<form action="<?=setPage($page, 0, $itemID, ACT_SAVE).keepFilter().keepContext()?>" enctype="multipart/form-data" method="post" name="GPS" id="GPS" onsubmit="return fCheck(this);">

<fieldset title="<?=getLabel('strCommonData')?>">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="id"><?=getLabel('strPlaceID')?></label><?=$itemID?>
	<input type="hidden" name="id" id="id" value="<?=$itemID?>" /><br />
	<br />
	
		
	<label for="long?>"><?=getLabel('strLong')?></label>
	<input type="text" name="long" id="long"
		value="<?=IIF(!empty($aRows), htmlspecialchars($aRows->long), '')?>" maxlength="10" class="fldfilter" /><br />
	<br />
	
	<label for="lat?>"><?=getLabel('strLat')?></label>
	<input type="text" name="lat" id="lat"
		value="<?=IIF(!empty($aRows), htmlspecialchars($aRows->lat), '')?>" maxlength="10" class="fldfilter" /><br />
	<br />
	<br class="clear" />
	<br />
</fieldset>
	<br class="clear" />
	<br />
	<input type="submit" value="<?=getLabel('strSave')?>" class="btn" />
</form>
<?
}

?>