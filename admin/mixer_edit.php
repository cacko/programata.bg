<?php
if (!$bInSite) die();
//=========================================================
$aFilter = array('parent_page', 'keyword');
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
			$item = $oMixer->Insert(getPostedArg('title'.$aAbbrLanguages[DEF_LANG]), 
						parseDate(getPostedArg('publication_date')), 
						getPostedArg('keywords'.$aAbbrLanguages[DEF_LANG]), 
						getPostedArg('description'.$aAbbrLanguages[DEF_LANG]), 
						getPostedArg('text'.$aAbbrLanguages[DEF_LANG]), 
						getPostedArg('source'), 
						getPostedArg('source_url'),
						getPostedArg('author'.$aAbbrLanguages[DEF_LANG]),
						0, 
						getPostedArg('hidden')); // primary record
		}
		foreach($aLanguages as $key=>$val)
		{
			$oMixer->Update(	$item, 
					getPostedArg('title'.$aAbbrLanguages[$key]),
					parseDate(getPostedArg('publication_date')), 
					getPostedArg('keywords'.$aAbbrLanguages[$key]), 
					getPostedArg('description'.$aAbbrLanguages[$key]),
					getPostedArg('text'.$aAbbrLanguages[$key]), 
					getPostedArg('source'), 
					getPostedArg('source_url'),
					getPostedArg('author'.$aAbbrLanguages[$key]),
					0, 
					getPostedArg('hidden'),
					$key); // update all
		}
		// save relations
		$oMixer->DeleteMixerPage($item);
		$oMixer->InsertMixerPage($item, getPostedArg('parent_page_id'));
		// UPLOAD FILE HERE //==========================================
		$nError = uploadBrowsedFile('main_image', IMG_PUBLICATION.$item, EXT_IMG);/*, 'empty.jpg'*/
		if (empty($nError))
		{
			resizeImage(IMG_MIXER.$item.'.'.EXT_IMG, W_IMG_GALLERY, H_IMG_GALLERY);
			deleteFile(IMG_MIXER_MID.$item, EXT_IMG);
			duplicateFile(IMG_MIXER.$item.'.'.EXT_IMG, IMG_MIXER_MID.$item.'.'.EXT_IMG);
			resizeImage(IMG_MIXER_MID.$item.'.'.EXT_IMG, W_IMG_MIDDLE, H_IMG_MIDDLE);
			deleteFile(IMG_MIXER_THUMB.$item, EXT_IMG);
			duplicateFile(IMG_MIXER.$item.'.'.EXT_IMG, IMG_MIXER_THUMB.$item.'.'.EXT_IMG);
			resizeImage(IMG_MIXER_THUMB.$item.'.'.EXT_IMG, W_IMG_THUMB, H_IMG_THUMB);
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
			$aRows[$key] = $oMixer->GetByID($item, $key); // get by id
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
				$aRows[$key] = $oMixer->GetByID($item, $key); // get by id
			if (count($aRows)>0) showForm($aRows); // load form
		}
		break;
	case ACT_DELETE:
		if (isset($item) && !empty($item))
		{
			// DELETE FILE HERE //==========================================
			deleteFile(IMG_MIXER.$item, EXT_IMG);
			deleteFile(IMG_MIXER_MID.$item, EXT_IMG);
			deleteFile(IMG_MIXER_THUMB.$item, EXT_IMG);
			// DELETE FILE HERE //==========================================
			$oMixer->Delete($item);
			echo getLabel('strDeleteOK').'<br /><br />'; // show ok message
			echo $strFilterListLink;
		}
		break;
	case ACT_DELETE_IMG:
		if (isset($item) && !empty($item))
		{
			// DELETE FILE HERE //==========================================
			deleteFile(IMG_MIXER.$item, EXT_IMG);
			deleteFile(IMG_MIXER_MID.$item, EXT_IMG);
			deleteFile(IMG_MIXER_THUMB.$item, EXT_IMG);
			// DELETE FILE HERE //==========================================
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oMixer->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
	default:
	case ACT_VIEW:
		if (isset($item) && !empty($item))
		{
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oMixer->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
}
//=========================================================
function showForm($aRows=null)
{
	global $page, $oPage, $oMixer, $oUser, $aLanguages, $aAbbrLanguages;
	
	require_once "../FCKeditor/fckeditor.php";
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->MixerID;
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
	}
	echo getLabel('strRequired').'<br />'."\n";
?>
<script type="text/javascript">
<!--
function fCheck(frm) {
<? foreach ($aLanguages as $key=>$val) {?>
	if (!valEmpty("title<?=$aAbbrLanguages[$key]?>", "<?=getLabel('strEnter', $key).getLabel('strMixerName', $key)?>")) return false;
<?}?>
	if (!valEmpty("mixer_date", "<?=getLabel('strEnter').getLabel('strMixerDate')?>")) return false;
	//if (!valOption("parent_page_id", "<?=getLabel('strSelect').getLabel('strParentPage')?>")) return false;
	return true;
}
//-->
</script>
<form action="<?=setPage($page, 0, $itemID, ACT_SAVE).keepFilter().keepContext()?>" enctype="multipart/form-data" method="post" name="Mixer" id="Mixer" onsubmit="return fCheck(this);">
<input type="hidden" name="MAX_FILE_SIZE" value="2550000" />

<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="id"><?=getLabel('strMixerID')?></label><?=$itemID?>
	<input type="hidden" name="id" id="id" value="<?=$itemID?>" /><br />
	<br />
	
	<label for="parent_page_id"><?=getLabel('strParentPage').formatVal().getLabel('multiple')?></label>
	<select name="parent_page_id[]" id="parent_page_id" multiple="multiple" size="5" class="fldsmall">
	<?
		$aPages = $oPage->ListAllAsArray(null, '', 'mixer_list', true);
		if (!empty($aRows[DEF_LANG])) 
		{
			$aRelPages = $oMixer->ListMixerPagesAsArray($itemID);
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
	
	<?
		$today_ts = mktime (0,0,0,date("m"), date("d"), date("Y"));
		$dMixerDate = date(DEFAULT_DATE_DB_FORMAT, $today_ts);
	?>
	<label for="mixer_date"><?=getLabel('strMixerDate').formatVal()?></label>
	<input type="text" name="mixer_date" id="mixer_date" maxlength="10" class="fldfilter"
	       value="<?=IIF(!empty($aRows[DEF_LANG]), formatDate($aRows[DEF_LANG]->MixerDate), formatDate($dMixerDate))?>" 
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
<br class="clear" />
<?
	foreach($aLanguages as $key=>$val)
	{
?>
<fieldset title="<?=$val?>" class="half">
	<legend><?=$val?></legend>
	<label for="title<?=$aAbbrLanguages[$key]?>"><?=getLabel('strMixerName', $key).formatVal()?></label><br />
	<input type="text" name="title<?=$aAbbrLanguages[$key]?>" id="title<?=$aAbbrLanguages[$key]?>"
		value="<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->Title), '')?>" maxlength="255" class="fld" /><br />
	<br />
	<? 	if (!empty($aRows[DEF_LANG]) )
		{
			$sMainImageFile = '../'.UPLOAD_DIR.IMG_MIXER_THUMB.$itemID.'.'.EXT_IMG;
			if (!is_file($sMainImageFile))
			{
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
			}
	?>
	<label><?=getLabel('strMixerImage')?></label>
	<?=drawImage($sMainImageFile)?><br />
	<br />
	<?	}?>
	<label for="main_image"><?=getLabel('strMixerImageFull', $key)?></label>
	<input type="file" name="main_image" id="main_image" size="47" accept="image/x-jpg" class="btn" /><br />
	<br class="clear" />
	<label for="author<?=$aAbbrLanguages[$key]?>"><?=getLabel('strAuthor', $key)?></label><br />
	<input type="text" name="author<?=$aAbbrLanguages[$key]?>" id="authoraAbbrLanguages[$key]?>"
		value="<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->Author), '')?>" maxlength="255" class="fld" /><br />
	<br />
	<label for="text<?=$aAbbrLanguages[$key]?>"><?=getLabel('strMixerText', $key)?></label><br />
	<?
		$fck = new FCKeditor('text'.$aAbbrLanguages[$key], '475', '250', 'DiagonaliDefault', IIF(!empty($aRows[$key]), $aRows[$key]->Content, ''));
		$fck->Create();
	?>
	<br />
</fieldset>
<?
	}
?>
<br class="clear" />
<?
	foreach($aLanguages as $key=>$val)
	{
?>
<fieldset title="<?=getLabel('strMetaKeywords', $key)?>" class="half">
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
	global $page, $oPage, $oMixer, $oUser, $aLanguages, $aEntityTypes;
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	$sPages = '';
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->MixerID;
	}
	if (!empty($itemID))
	{
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
		
		$aPages = $oPage->ListAllAsArray(null, '', '', true);
		$aRelPages = $oMixer->ListMixerPagesAsArray($itemID);
		if (is_array($aRelPages) && count($aRelPages)>0)
		{
			foreach($aRelPages as $cat)
				$sPages .= $aPages[$cat].'<br/>';
		}
		else
			$sPages = '<span class="a"><em>'.getLabel('strNone').'</em></span><br />';
		
		$sImageLink = '<br /><a href="'.setPage($page, 0, $itemID, ACT_DELETE_IMG).'">'.getLabel('delete').'</a>';
		$sMainImageFile = '../'.UPLOAD_DIR.IMG_MIXER_THUMB.$itemID.'.'.EXT_IMG;
		if (!is_file($sMainImageFile))
		{
			$sImageLink = '';
			$sMainImageFile = '../'.UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
		}
?>
<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label><?=getLabel('strMixerID')?></label>
	<?=$aRows[DEF_LANG]->MixerID?><br />
	<br />
	<label><?=getLabel('strParentPage')?></label>
	<div><?=$sPages?></div><br />
	
	<label><?=getLabel('strMixerImage')?></label>
	<div><?=drawImage($sMainImageFile).$sImageLink?></div>
	<br />
	<label><?=getLabel('strMixerDate')?></label>
	<?=formatDate($aRows[DEF_LANG]->MixerDate)?><br />
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
	<label><?=getLabel('strMixerName', $key)?></label>
	<?=IIF(!empty($aRows[$key]), $aRows[$key]->Title, '')?><br />
	<br />
	
	<label><?=getLabel('strAuthor', $key)?></label>
	<div><?=IIF(!empty($aRows[$key]), $aRows[$key]->Author, '')?></div><br />
	<br />
	
	<label><?=getLabel('strMixerText', $key)?></label>
	<div><?=IIF(!empty($aRows[$key]), $aRows[$key]->Content, '')?></div><br />
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