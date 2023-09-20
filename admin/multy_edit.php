<?php
if (!$bInSite) die();
//=========================================================
$aFilter = array('parent_page', 'keyword');
$strFilterListLink = '<a href="'.setPage($page).keepFilter().keepContext().'">'.getLabel('strBackToList').'</a><br /><br />';
//=========================================================
switch($action)
{
	case "addPart":
		while(@ob_end_clean());
		form_content(null, (int)$_GET['part']);
		exit;
	case ACT_SAVE:
		$sMsg = '';
//print_r($_POST);
		if (isset($_POST['id']) && !empty($_POST['id']))
		{
			$item = getPostedArg('id');
		}
		else
		{
			$item = $oMulty->Insert(getPostedArg('title'.$aAbbrLanguages[DEF_LANG]),
						parseDate(getPostedArg('publication_date')), 
						getPostedArg('hidden'),
						getPostedArg('author'.$aAbbrLanguages[DEF_LANG]),
						getPostedArg('keywords'.$aAbbrLanguages[DEF_LANG])
						); // primary record
		}
		foreach($aLanguages as $key=>$val)
		{
			$oMulty->Update(	$item, 
						getPostedArg('title'.$aAbbrLanguages[$key]), 
						parseDate(getPostedArg('publication_date')), 
						getPostedArg('hidden'),
						getPostedArg('author'.$aAbbrLanguages[$key]),
						getPostedArg('keywords'.$aAbbrLanguages[$key]),
						$key); // update all
						
			foreach(getPostedArg('text'.$aAbbrLanguages[$key]) as $part=>$text)
			{
				$oMulty->InsertParts($item, $part, $text, $key);
			}			
		}
		// save relations
		$oMulty->DeleteMultyPage($item);
		$oMulty->InsertMultyPage($item, getPostedArg('parent_page_id'));
		// UPLOAD FILE HERE //==========================================
		$target_folder = UPLOAD_DIR_ABS.IMG_MULTY.$item.'/';
		if(!is_dir($target_folder))
			mkdir($target_folder);
//print_r($_FILES);		
		foreach($_FILES as $key=>$FILE)
		{
			list($type, $part) = explode("_", $key);
			$nError = uploadBrowsedFile('mainImage_'.$part, $part, EXT_IMG, $target_folder);
			if (empty($nError))
			{
				resizeImage($part.'.'.EXT_IMG, W_IMG_BIG, H_IMG_BIG, $target_folder);
				deleteFile(IMG_BIG.$part, EXT_IMG, $target_folder);
				duplicateFile($part.'.'.EXT_IMG, IMG_BIG.$part.'.'.EXT_IMG, $target_folder, $target_folder);

				resizeImage($part.'.'.EXT_IMG, W_IMG_GALLERY, H_IMG_GALLERY, $target_folder);
				deleteFile(IMG_MID.$part, EXT_IMG, $target_folder);
				duplicateFile($part.'.'.EXT_IMG, IMG_MID.$part.'.'.EXT_IMG, $target_folder, $target_folder);
				
				resizeImage($part.'.'.EXT_IMG, W_IMG_MIDDLE, H_IMG_MIDDLE, $target_folder);
				deleteFile(IMG_THUMB.$part, EXT_IMG, $target_folder);
				duplicateFile($part.'.'.EXT_IMG, IMG_THUMB.$part.'.'.EXT_IMG, $target_folder, $target_folder);
				
				deleteFile($part, EXT_IMG, $target_folder);
			}
			$sMsg .= $part.': '.getLabel('strFile_'.$nError).'<br />';
			
		}
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
			$aRows[$key] = $oMulty->GetByID($item, $key); // get by id
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
				$aRows[$key] = $oMulty->GetByID($item, $key); // get by id
			if (count($aRows) > 0) showForm($aRows); // load form
		}
		break;
	case ACT_DELETE:
		if (isset($item) && !empty($item))
		{
			// DELETE FILE HERE //==========================================
			$target_folder = UPLOAD_DIR_ABS.IMG_MULTY.$item.'/';
		    $files = glob( $target_folder . '*'); 
		    foreach( $files as $file ){ 
		        if( substr( $file, -1 ) == '/' ) 
		            delTree( $file ); 
		        else 
		            unlink( $file ); 
		    } 
		    rmdir($target_folder);

			// DELETE FILE HERE //==========================================
			$oMulty->Delete($item);
			echo getLabel('strDeleteOK').'<br /><br />'; // show ok message
			echo $strFilterListLink;
		}
		break;
	case ACT_DELETE_IMG:
		if (isset($item) && !empty($item))
		{
			//TODO
			// DELETE FILE HERE //==========================================
			deleteFile(IMG_MULTY.$item, EXT_IMG);
			deleteFile(IMG_MULTY_MID.$item, EXT_IMG);
			deleteFile(IMG_MULTY_THUMB.$item, EXT_IMG);
			// DELETE FILE HERE //==========================================
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oMulty->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
	case ACT_VIEW:
	default:
		if (isset($item) && !empty($item))
		{
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oMulty->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
}
//=========================================================
function form_content($aRows=null, $part=1)
{
	global $page, $oPage, $oMulty, $oUser, $aLanguages, $aAbbrLanguages;
	require_once "../FCKeditor/fckeditor.php";
	foreach($aLanguages as $key=>$val)
	{
		?>
		<a name="#part<?=$part?>"/>
		<fieldset title="part<?=$aAbbrLanguages[$key].'_'.$part?>" class="half">
			<legend><?=getLabel('strCurrPart', $key).$part?></legend>
			<label for="text<?=$aAbbrLanguages[$key].'_'.$part?>"><?=getLabel('strMultyText', $key)?></label><br />
			<?
				$fck = new FCKeditor('text'.$aAbbrLanguages[$key].'['.$part.']', '475', '150', 'DiagonaliDefault', IIF(!empty($aRows[$key]), $oMulty->GetPartText($aRows[$key]->MultyID, $part, $key), ''));
				$fck->Create();
			?>
			<br />
		</fieldset>
		<?
	} //end lang loop
	?>
	<br class="clear"/>
	<label><?=getLabel('strMultyImage')?></label> 
	<br />
	<div id="files<?=$part?>">
	<? 	
	if (!empty($aRows[DEF_LANG]) )
	{
		$thumb = '../'.UPLOAD_DIR.IMG_MULTY.$aRows[DEF_LANG]->MultyID.'/'.IMG_THUMB.$part.'.'.EXT_IMG;
		echo '<div>'.drawImage($thumb).'</div>';
	} //end if 
	$id = 'mainImage_'.$part;
	echo '<input type="file" name="'.$id.'" id="'.$id.'" size="47" accept="image/jpg" class="btn" /><br />';
	?> 
	</div>
	<br class="clear" />
	<?
}
function showForm($aRows=null)
{
	global $page, $oPage, $oMulty, $oUser, $aLanguages, $aAbbrLanguages;
	
	require_once "../FCKeditor/fckeditor.php";
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->MultyID;
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
	}
	echo getLabel('strRequired').'<br />'."\n";
?>
<script type="text/javascript">
<!--
function fCheck(frm) {
	if (!valEmpty("publication_date", "<?=getLabel('strEnter').getLabel('strMultyDate')?>")) return false;
	return true;
}
</script>
<form action="<?=setPage($page, 0, $itemID, ACT_SAVE).keepFilter().keepContext()?>" enctype="multipart/form-data" method="post" name="Multy" id="Multy" onsubmit="return fCheck(this);">
<input type="hidden" name="MAX_FILE_SIZE" value="2550000" />

<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="id"><?=getLabel('strMultyID')?></label><?=$itemID?>
	<input type="hidden" name="id" id="id" value="<?=$itemID?>" /><br />
	<br />
	
	<label for="parent_page_id"><?=getLabel('strParentPage').formatVal().getLabel('multiple')?></label>
	<select name="parent_page_id[]" id="parent_page_id" multiple="multiple" size="5" class="fldsmall">
	<?
		$aPages = $oPage->ListAllAsArray(null, '', 'multy_list', true);
		if (!empty($aRows[DEF_LANG])) 
		{
			$aRelPages = $oMulty->ListMultyPagesAsArray($itemID);
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
		$dMultyDate = date(DEFAULT_DATE_DB_FORMAT, $today_ts);
	?>
	<label for="publication_date"><?=getLabel('strMultyDate').formatVal()?></label>
	<input type="text" name="publication_date" id="publication_date" maxlength="10" class="fldfilter"
	       value="<?=IIF(!empty($aRows[DEF_LANG]), formatDate($aRows[DEF_LANG]->PublicationDate), formatDate($dMultyDate))?>" 
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
<br />

<?
foreach($aLanguages as $key=>$val)
{
?>

<fieldset title="part<?=$aAbbrLanguages[$key]?>" class="half">	
	<label for="title"><?=getLabel('strMultyName', $key)?></label><br />
	<input type="text" name="title<?=$aAbbrLanguages[$key]?>" id="title<?=$aAbbrLanguages[$key]?>"
		value="<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->Title), '')?>" maxlength="255" class="fld" /><br />
	<br />
	<label for="author<?=$aAbbrLanguages[$key]?>"><?=getLabel('strAuthor', $key)?></label><br />
	<input type="text" name="author<?=$aAbbrLanguages[$key]?>" id="author<?=$aAbbrLanguages[$key]?>"
		value="<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->{'Author'}), '')?>" maxlength="255" class="fld" /><br />
	<br />
</fieldset>
<? } ?>
<div id="content_parts">
<?
	if(!empty($aRows[$key]))
	{
		$partsNum = $oMulty->GetPartsNum($itemID, $key);
	}
	else
	{
		$partsNum = 1;
	}
for($part=1; $part<=$partsNum; ++$part)
{
	form_content($aRows, $part);
}
?>
</div>
<br class="clear" />
<a name="#bot"/>
<a href="#bot" id="add_part">add new part</a>
<br class="clear"/>
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
<script type="text/javascript" src="../js/multy_edit.js"></script>
<?
}
//=========================================================
function showPreview($aRows=null)
{
	global $page, $oPage, $oMulty, $oUser, $aLanguages, $aEntityTypes;
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	$sPages = '';
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->MultyID;
	}
	if (!empty($itemID))
	{
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
		
		$aPages = $oPage->ListAllAsArray(null, '', '', true);
		$aRelPages = $oMulty->ListMultyPagesAsArray($itemID);
		if (is_array($aRelPages) && count($aRelPages)>0)
		{
			foreach($aRelPages as $cat)
				$sPages .= $aPages[$cat].'<br/>';
		}
		else
			$sPages = '<span class="a"><em>'.getLabel('strNone').'</em></span><br />';
		
?>
<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label><?=getLabel('strMultyID')?></label>
	<?=$aRows[DEF_LANG]->MultyID?><br />
	<br />
	<label><?=getLabel('strParentPage')?></label>
	<div><?=$sPages?></div><br />
	
	<label><?=getLabel('strMultyDate')?></label>
	<?=formatDate($aRows[DEF_LANG]->PublicationDate)?><br />
	<br />
	<label><?=getLabel('strHide')?></label>
	<?=$aYesNo[$aRows[DEF_LANG]->IsHidden]?><br />
	<br />
	<label><?=getLabel('strLastUpdate')?></label>
	<?=IIF(!empty($aRows[DEF_LANG]), $aRows[DEF_LANG]->LastUpdate, '').getLabel('strByUser').IIF(!empty($aRows[DEF_LANG]), $rUser->Username.' ('.$rUser->FirstName.' '.$rUser->LastName.')', '')?><br />
</fieldset>
<!--
<iframe name="related" style="float:left;" width="500" height="600" src="<?=RELATED_PAGE.'?'.ARG_PAGE.'='.ENT_HOME.'&amp;'.ARG_RELID.'='.$itemID.'&amp;'.ARG_CAT.'='.$aEntityTypes[ENT_MULTY]?>"></iframe>
-->
	<br class="clear" />
<?
	
	foreach($aLanguages as $key=>$val)
	{

?>
<fieldset title="<?=$val?>" class="half">
	<legend><?=$val?></legend>
	<label for="main_title"><?=getLabel('strMultyName', $key)?></label>
	<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->Title), '')?>
	<br /><br />
	<label><?=getLabel('strAuthor', $key)?></label>
	<div><?=IIF(!empty($aRows[$key]), $aRows[$key]->{'Author'}, '')?></div><br />
	<br />


<?
	$partsNum = $oMulty->GetPartsNum($itemID, $key);

	for($part=1; $part<=$partsNum; ++$part)	
	{
?>	
	<label><?=getLabel('strMultyImage').' '.$part?></label><br />
<?	
		$thumb = '../'.UPLOAD_DIR.IMG_MULTY.$itemID.'/'.IMG_THUMB.$part.'.'.EXT_IMG;
		echo '<div>'.drawImage($thumb).'</div><br />';
//		$sImageLink = '<br /><a href="'.setPage($page, 0, $itemID, ACT_DELETE_IMG).'">'.getLabel('delete', $key).'</a>';
?>
	<br />

	<label><?=getLabel('strMultyText', $key).' '.$part?></label>
	<div><?=IIF(!empty($aRows[$key]), $oMulty->GetPartText($itemID, $part, $key), '')?></div><br />
	<br />
<?
	}		
?>	
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