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
			$item = $oAttachment->Insert(getPostedArg('attachment_type_id'),
						getPostedArg('title'.$aAbbrLanguages[DEF_LANG]), 
						getPostedArg('filename', ''),
						getPostedArg('content_type', ''), 
						$relitem, //getPostedArg('entity_id'), 
						$cat, //getPostedArg('entity_type'),
						getPostedArg('hidden')); // primary record
		}
		//
		//print_r($_FILES);
		// UPLOAD FILE HERE //==========================================
		$nAttachmentType = getPostedArg('attachment_type_id');
		$sExt = '';
		$nError = -1;
		if (is_uploaded_file($_FILES['image']['tmp_name']))
		{
			$sExt = getFileExt($_FILES['image']['name']);
			switch($nAttachmentType)
			{
				case 1: // old logo / title image - resize to 160px width
					$nError = uploadBrowsedFile('image', IMG_SMALL.$item, $sExt, FILEBANK_DIR_ABS);/*, 'empty.jpg'*/
					if (empty($nError))
					{
						resizeImage(IMG_SMALL.$item.'.'.$sExt, W_IMG_SMALL, H_IMG_SMALL, FILEBANK_DIR_ABS);
						deleteFile(IMG_SMALL_THUMB.$item, $sExt, FILEBANK_DIR_SMALL_ABS);
						duplicateFile(IMG_SMALL.$item.'.'.$sExt, IMG_SMALL_THUMB.$item.'.'.$sExt, FILEBANK_DIR_ABS, FILEBANK_DIR_SMALL_ABS);
						resizeImage(IMG_SMALL_THUMB.$item.'.'.$sExt, W_IMG_SMALL_THUMB, H_IMG_SMALL_THUMB, FILEBANK_DIR_SMALL_ABS);
					}
					$sMsg .= getLabel('strFile_'.$nError);
					break;
				case 2: // old illustration / small image - resize to 160px width
					$nError = uploadBrowsedFile('image', IMG_SMALL.$item, $sExt, FILEBANK_DIR_ABS);/*, 'empty.jpg'*/
					if (empty($nError))
					{
						resizeImage(IMG_SMALL.$item.'.'.$sExt, W_IMG_SMALL, H_IMG_SMALL, FILEBANK_DIR_ABS);
						deleteFile(IMG_SMALL_THUMB.$item, $sExt, FILEBANK_DIR_SMALL_ABS);
						duplicateFile(IMG_SMALL.$item.'.'.$sExt, IMG_SMALL_THUMB.$item.'.'.$sExt, FILEBANK_DIR_ABS, FILEBANK_DIR_SMALL_ABS);
						resizeImage(IMG_SMALL_THUMB.$item.'.'.$sExt, W_IMG_SMALL_THUMB, H_IMG_SMALL_THUMB, FILEBANK_DIR_SMALL_ABS);
					}
					$sMsg .= getLabel('strFile_'.$nError);
					break;
				case 3: // new title image - size depends on entity type, max 460px width
					$nError = uploadBrowsedFile('image', IMG_GALLERY.$item, $sExt);/*, 'empty.jpg'*/
					if (empty($nError))
					{
						resizeImage(IMG_GALLERY.$item.'.'.$sExt, W_IMG_GALLERY, H_IMG_GALLERY);
						//deleteFile(IMG_THUMB.$item, $sExt);
						//duplicateFile(IMG_GALLERY.$item.'.'.$sExt, IMG_THUMB.$item.'.'.$sExt);
						//resizeImage(IMG_THUMB.$item.'.'.$sExt, W_IMG_THUMB, H_IMG_THUMB);
					}
					$sMsg .= getLabel('strFile_'.$nError);
					break;
				case 4: // gallery image - resize to 460px width
					$nError = uploadBrowsedFile('image', IMG_GALLERY.$item, $sExt);/*, 'empty.jpg'*/
					if (empty($nError))
					{
						resizeImage(IMG_GALLERY.$item.'.'.$sExt, W_IMG_GALLERY, H_IMG_GALLERY);
						//deleteFile(IMG_THUMB.$item, $sExt);
						//duplicateFile(IMG_GALLERY.$item.'.'.$sExt, IMG_THUMB.$item.'.'.$sExt);
						//resizeImage(IMG_THUMB.$item.'.'.$sExt, W_IMG_THUMB, H_IMG_THUMB);
					}
					$sMsg .= getLabel('strFile_'.$nError);
					break;
				case 5: // panorama image - 460px width
					$nError = uploadBrowsedFile('image', FILE_PANORAMA.$item, $sExt);/*, 'empty.jpg'*/
					if (empty($nError))
					{
						// do nothing
					}
					$sMsg .= getLabel('strFile_'.$nError);
					break;
				case 6: // file attachment
					$nError = uploadBrowsedFile('image', FILE_ATTACHMENT.$item, $sExt);/*, 'empty.jpg'*/
					if (empty($nError))
					{
						// do nothing
					}
					$sMsg .= getLabel('strFile_'.$nError);
					break;
				case 7: // file attachment
					$nError = uploadBrowsedFile('image', FILE_TRAILER.$item, $sExt);/*, 'empty.jpg'*/
					if (empty($nError))
					{
						// do nothing
					}
					$sMsg .= getLabel('strFile_'.$nError);
					break;
			}
		}
		foreach($aLanguages as $key=>$val)
		{
			$oAttachment->Update($item, 
					getPostedArg('attachment_type_id'),
					getPostedArg('title'.$aAbbrLanguages[$key]), 
					$_FILES['image']['name'], //getPostedArg('filename'),
					$_FILES['image']['type'], //getPostedArg('content_type'), 
					$relitem, //getPostedArg('entity_id'), 
					$cat, //getPostedArg('entity_type'),
					getPostedArg('hidden'),
					$key); // update all
		}
		$sMsg .= getLabel('strFile_'.$nError);
		// UPLOAD FILE HERE //==========================================
		if (!$item)
			$sMsg .= getLabel('strSaveFailed'); // failed message
		else
			$sMsg .= getLabel('strSaveOK'); // ok message
		echo $sMsg.'<br /><br />';
		echo $strFilterListLink;
		$aRows = array();
		foreach($aLanguages as $key=>$val)
			$aRows[$key] = $oAttachment->GetByID($item, $key); // get by id
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
				$aRows[$key] = $oAttachment->GetByID($item, $key); // get by id
			if (count($aRows)>0) showForm($aRows); // load form
		}
		break;
	case ACT_DELETE:
		if (isset($item) && !empty($item))
		{
			// DELETE FILE HERE //==========================================
			$row = $oAttachment->GetByID($item);
			switch($row->AttachmentTypeID)
			{
				case 1: // old logo / title image - resize to 160px width
					deleteFile(IMG_SMALL.$item, $row->Extension, FILEBANK_DIR_ABS);
					deleteFile(IMG_SMALL_THUMB.$item, $row->Extension, FILEBANK_DIR_SMALL_ABS);
					break;
				case 2: // old illustration / small image - resize to 160px width
					deleteFile(IMG_SMALL.$item, $row->Extension, FILEBANK_DIR_ABS);
					deleteFile(IMG_SMALL_THUMB.$item, $row->Extension, FILEBANK_DIR_SMALL_ABS);
					break;
				case 3: // new title image - size depends on entity type, max 460px width
					deleteFile(IMG_GALLERY.$item, $row->Extension);
					deleteFile(IMG_THUMB.$item, $row->Extension);
					break;
				case 4: // gallery image - resize to 460px width
					deleteFile(IMG_GALLERY.$item, $row->Extension);
					deleteFile(IMG_THUMB.$item, $row->Extension);
				case 5: // panorama image - 460px width
					deleteFile(FILE_PANORAMA.$item, $row->Extension);
					break;
				case 6: // file attachment
					deleteFile(FILE_ATTACHMENT.$item, $row->Extension);
					break;
					case 7: // file trailer
						deleteFile(FILE_TRAILER.$item, $row->Extension);
						break;
			}
			// DELETE FILE HERE //==========================================
			$oAttachment->Delete($item);
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
				$aRows[$key] = $oAttachment->GetByID($item, $key); // get by id
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
		$itemID = $aRows[DEF_LANG]->AttachmentID;
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
	}
	echo getLabel('strRequired').'<br />'."\n";
?>
<script type="text/javascript">
<!--
function fCheck(frm) {
<? foreach ($aLanguages as $key=>$val) {?>
	if (!valEmpty("title<?=$aAbbrLanguages[$key]?>", "<?=getLabel('strEnter', $key).getLabel('strAttachmentTitle', $key)?>")) return false;
<?}?>
	if (!valOption("attachment_type_id", "<?=getLabel('strSelect').getLabel('strAttachmentType')?>")) return false;
	return true;
}
//-->
</script>
<form action="<?=setPage($page, 0, $itemID, ACT_SAVE).keepFilter().keepContext()?>" enctype="multipart/form-data" method="post" name="Attachment" id="Attachment" onsubmit="return fCheck(this);">
<input type="hidden" name="MAX_FILE_SIZE" value="21500000" />
<fieldset title="<?=getLabel('strCommonData')?>">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="id"><?=getLabel('strAttachmentID')?></label><?=$itemID?>
	<input type="hidden" name="id" id="id" value="<?=$itemID?>" /><br />
	<br />
	
	<label for="attachment_type_id"><?=getLabel('strAttachmentType').formatVal()?></label><br />
	<select name="attachment_type_id" id="attachment_type_id" class="fldsmall">
		<option value="0"><?=getLabel('strSelect');?></option>
	<?
		$aAttachmentTypes = getLabel('aAttachmentTypes');
		foreach($aAttachmentTypes as $key=>$val)
		{			
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if ($aRows[DEF_LANG]->AttachmentTypeID == $key)
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	
	<hr />
	<?
		$sMainImageFile = '';
		if (!empty($aRows[DEF_LANG]) )
		{
			$bIsImage = true;
			switch($aRows[DEF_LANG]->AttachmentTypeID)
			{
				case 1:
					$sMainImageFile = '../'.FILEBANK_DIR.IMG_SMALL.$itemID.'.'.$aRows[DEF_LANG]->Extension;
					break;
				case 2:
					$sMainImageFile = '../'.FILEBANK_DIR.IMG_SMALL.$itemID.'.'.$aRows[DEF_LANG]->Extension;
					break;
				case 3:
					$sMainImageFile = '../'.UPLOAD_DIR.IMG_GALLERY.$itemID.'.'.$aRows[DEF_LANG]->Extension;
					break;
				case 4:
					$sMainImageFile = '../'.UPLOAD_DIR.IMG_GALLERY.$itemID.'.'.$aRows[DEF_LANG]->Extension;
					break;/**/
				case 5:
					$sMainImageFile = '../'.UPLOAD_DIR.FILE_PANORAMA.$itemID.'.'.$aRows[DEF_LANG]->Extension;
					$bIsImage = false;
					break;
				case 6:
					$sMainImageFile = '../'.UPLOAD_DIR.FILE_ATTACHMENT.$itemID.'.'.$aRows[DEF_LANG]->Extension;
					$bIsImage = false;
					break;
				case 7:
					$sMainImageFile = '../'.UPLOAD_DIR.FILE_TRAILER.$itemID.'.'.$aRows[DEF_LANG]->Extension;
					$bIsImage = false;
					break;
			
			}
			if (!is_file($sMainImageFile))
			{
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_EMPTY.'.'.EXT_PNG;
			}
	?>
	<label><?=getLabel('strAttachment')?></label>
	<?
		if ($bIsImage)
			echo drawImage($sMainImageFile, 0, 0, $aRows[DEF_LANG]->Title);
		else
			echo '<a href="'.$sMainImageFile.'" target="_blank">'.$aRows[DEF_LANG]->Title.'</a>';
	?><br />
	<br />
	<?	}?>
	<label for="image"><?=getLabel('strAttachmentFull')?></label><br />
	<input type="file" name="image" id="image" size="47" accept="image/x-jpg" class="btn" /><br />
	<br />
	<hr />
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
	<label for="title<?=$aAbbrLanguages[$key]?>"><?=getLabel('strAttachmentTitle', $key).formatVal()?></label><br />
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
	global $page, $oUser, $aLanguages, $aAbbrLanguages;
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	$aAttachmentTypes = getLabel('aAttachmentTypes');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->AttachmentID;
	}
	if (!empty($itemID))
	{
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
		
		$bIsImage = true;
		$sMainImageFile = '';
		switch($aRows[DEF_LANG]->AttachmentTypeID)
		{
			case 1:
				$sMainImageFile = '../'.FILEBANK_DIR.IMG_SMALL.$itemID.'.'.$aRows[DEF_LANG]->Extension;
				break;
			case 2:
				$sMainImageFile = '../'.FILEBANK_DIR.IMG_SMALL.$itemID.'.'.$aRows[DEF_LANG]->Extension;
				break;
			case 3:
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_GALLERY.$itemID.'.'.$aRows[DEF_LANG]->Extension;
				break;
			case 4:
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_GALLERY.$itemID.'.'.$aRows[DEF_LANG]->Extension;
				break;/**/
			case 5:
				$sMainImageFile = '../'.UPLOAD_DIR.FILE_PANORAMA.$itemID.'.'.$aRows[DEF_LANG]->Extension;
				$bIsImage = false;
				break;
			case 6:
				$sMainImageFile = '../'.UPLOAD_DIR.FILE_ATTACHMENT.$itemID.'.'.$aRows[DEF_LANG]->Extension;
				$bIsImage = false;
				break;
			case 7:
				$sMainImageFile = '../'.UPLOAD_DIR.FILE_TRAILER.$itemID.'.'.$aRows[DEF_LANG]->Extension;
				$bIsImage = false;
				break;
			
		}
		if (!is_file($sMainImageFile))
		{
			$sMainImageFile = '../'.UPLOAD_DIR.IMG_EMPTY.'.'.EXT_PNG;
		}/**/
?>
<fieldset title="<?=getLabel('strCommonData')?>">
	<legend><?=getLabel('strCommonData')?></legend>
	<label><?=getLabel('strAttachmentID')?></label>
	<?=$aRows[DEF_LANG]->AttachmentID?><br />
	<br />
	
	<label><?=getLabel('strAttachment')?></label>
	<?
		if ($bIsImage)
			echo drawImage($sMainImageFile, 0, 0, $aRows[DEF_LANG]->Title);
		else
			echo '<a href="'.$sMainImageFile.'" target="_blank">'.$aRows[DEF_LANG]->Title.'</a>';
	?><br />
	<br />
	<label><?=getLabel('strAttachmentType')?></label>
	<?=$aAttachmentTypes[$aRows[DEF_LANG]->AttachmentTypeID]?><br />
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
	<label><?=getLabel('strAttachmentTitle', $key)?></label>
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