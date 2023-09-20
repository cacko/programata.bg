<?php
if (!$bInSite) die();
//=========================================================
$aFilter = array('parent_label', 'keyword');
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
			$item = $oLabel->Insert(getPostedArg('title'.$aAbbrLanguages[DEF_LANG]), 
						getPostedArg('parent_label_id'), 
						getPostedArg('hidden')); // primary record
		}
		foreach($aLanguages as $key=>$val)
		{
			$oLabel->Update($item, 
					getPostedArg('title'.$aAbbrLanguages[$key]), 
					getPostedArg('hidden'),
					$key); // update all
		}
		if (!$item)
			$sMsg .= getLabel('strSaveFailed'); // failed message
		else
			$sMsg .= getLabel('strSaveOK'); // ok message
		echo $sMsg.'<br /><br />';
		echo $strFilterListLink;
		$aRows = array();
		foreach($aLanguages as $key=>$val)
			$aRows[$key] = $oLabel->GetByID($item, $key); // get by id
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
				$aRows[$key] = $oLabel->GetByID($item, $key); // get by id
			if (count($aRows)>0) showForm($aRows); // load form
		}
		break;
	case ACT_DELETE:
		if (isset($item) && !empty($item))
		{
			$oLabel->Delete($item);
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
				$aRows[$key] = $oLabel->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
}
//=========================================================
function showForm($aRows=null)
{
	global $page, $oLabel, $oUser, $aLanguages, $aAbbrLanguages;
	
	//require_once "../FCKeditor/fckeditor.php";
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->LabelID;
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
	}
	echo getLabel('strRequired').'<br />'."\n";
?>
<script type="text/javascript">
<!--
function fCheck(frm) {
<? foreach ($aLanguages as $key=>$val) {?>
	if (!valEmpty("title<?=$aAbbrLanguages[$key]?>", "<?=getLabel('strEnter', $key).getLabel('strLabelName', $key)?>")) return false;
<?}?>
<? if (empty($aRows[DEF_LANG]) ) {?>
	if (!valOption("parent_label_id", "<?=getLabel('strSelect').getLabel('strParentLabel')?>")) return false;
<?}?>
	return true;
}
//-->
</script>
<form action="<?=setPage($page, 0, $itemID, ACT_SAVE).keepFilter().keepContext()?>" method="post" name="Label" id="Label" onsubmit="return fCheck(this);">

<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="id"><?=getLabel('strLabelID')?></label><?=$itemID?>
	<input type="hidden" name="id" id="id" value="<?=$itemID?>" /><br />
	<br />
	
<? 	if (empty($aRows[DEF_LANG]) )
	{
?>
	<label for="parent_label_id"><?=getLabel('strParentLabel').formatVal()?></label>
	<select name="parent_label_id" id="parent_label_id" class="fldsmall">
		<option value="0"><?=getLabel('strSelect')?></option>
	<?
		$aLabels = $oLabel->ListAllParentsAsArray(true);
		//$aLabels = $oLabel->ListAllAsArray(null, '', '', true);
		foreach($aLabels as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if ($key == $aRows[DEF_LANG]->ParentLabelID)
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
<?
	}
	else
	{
		$aLabels = $oLabel->ListAllParentsAsArray(true);
		
		$sLabels = '';
		if (!empty($aRows[DEF_LANG]->ParentLabelID))
			$sLabels = $aLabels[$aRows[DEF_LANG]->ParentLabelID];
		else
			$sLabels = '<em>'.getLabel('strNone').'</em>';
?>
	<label><?=getLabel('strParentLabel')?></label><?=$sLabels?><br />
<?	}	?>
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
<br class="clear" />
<?
	foreach($aLanguages as $key=>$val)
	{
?>
<fieldset title="<?=$val?>" class="half">
	<legend><?=$val?></legend>
	<label for="title<?=$aAbbrLanguages[$key]?>"><?=getLabel('strLabelName', $key).formatVal()?></label><br />
	<input type="text" name="title<?=$aAbbrLanguages[$key]?>" id="title<?=$aAbbrLanguages[$key]?>"
		value="<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->Title), '')?>" maxlength="255" class="fld" /><br />
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
	global $page, $oLabel, $oUser, $aLanguages;
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	$sLabels = '';
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->LabelID;
	}
	if (!empty($itemID))
	{
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
		
		$aLabels = $oLabel->ListAllAsArray(null, '', '', true);
		
		if (!empty($aRows[DEF_LANG]->ParentLabelID))
			$sLabels = $aLabels[$aRows[DEF_LANG]->ParentLabelID];
		else
			$sLabels = '<em>'.getLabel('strNone').'</em>';
?>
<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label><?=getLabel('strLabelID')?></label>
	<?=$aRows[DEF_LANG]->LabelID?><br />
	<br />
	<label><?=getLabel('strParentLabel')?></label>
	<?=$sLabels?><br />
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
<fieldset title="<?=$val?>" class="half">
	<legend><?=$val?></legend>
	<label><?=getLabel('strLabelName', $key)?></label>
	<?=IIF(!empty($aRows[$key]), $aRows[$key]->Title, '')?><br />
	<br />
</fieldset>
<?
	}
?>
<ul class="nav">
	<li><a href="<?=setPage($page, 0, $itemID, ACT_EDIT).keepFilter().keepContext()?>"><?=getLabel('edit')?></a></li>
	<!--li><a href="<?=setPage($page, 0, $itemID, ACT_DELETE).keepFilter().keepContext()?>" onclick="return confMsg('<?=getLabel('strDeleteQ')?>');return false;"><?=getLabel('delete')?></a></li-->
</ul>
<?
	}
}
?>