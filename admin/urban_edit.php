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
			$item = $oUrban->Insert(getPostedArg('main_title'.$aAbbrLanguages[DEF_LANG]),
						parseDate(getPostedArg('publication_date')),
						getPostedArg('hidden'),
						getPostedArg('title'.$aAbbrLanguages[DEF_LANG].'_1'),
						getPostedArg('text'.$aAbbrLanguages[DEF_LANG].'_1'),
						getPostedArg('author'.$aAbbrLanguages[DEF_LANG].'_1'),
						getPostedArg('title'.$aAbbrLanguages[DEF_LANG].'_2'),
						getPostedArg('text'.$aAbbrLanguages[DEF_LANG].'_2'),
						getPostedArg('author'.$aAbbrLanguages[DEF_LANG].'_2'),
						getPostedArg('title'.$aAbbrLanguages[DEF_LANG].'_3'),
						getPostedArg('text'.$aAbbrLanguages[DEF_LANG].'_3'),
						getPostedArg('author'.$aAbbrLanguages[DEF_LANG].'_3'),
						getPostedArg('keywords'.$aAbbrLanguages[DEF_LANG])
						); // primary record
		}
		foreach($aLanguages as $key=>$val)
		{
			$oUrban->Update(	$item,
						getPostedArg('main_title'.$aAbbrLanguages[$key]),
						parseDate(getPostedArg('publication_date')),
						getPostedArg('hidden'),
						getPostedArg('title'.$aAbbrLanguages[$key].'_1'),
						getPostedArg('text'.$aAbbrLanguages[$key].'_1'),
						getPostedArg('author'.$aAbbrLanguages[$key].'_1'),
						getPostedArg('title'.$aAbbrLanguages[$key].'_2'),
						getPostedArg('text'.$aAbbrLanguages[$key].'_2'),
						getPostedArg('author'.$aAbbrLanguages[$key].'_2'),
						getPostedArg('title'.$aAbbrLanguages[$key].'_3'),
						getPostedArg('text'.$aAbbrLanguages[$key].'_3'),
						getPostedArg('author'.$aAbbrLanguages[$key].'_3'),
						getPostedArg('keywords'.$aAbbrLanguages[$key]),
						$key); // update all
		}
		// save relations
		$oUrban->DeleteUrbanPage($item);
		$oUrban->InsertUrbanPage($item, getPostedArg('parent_page_id'));
		// UPLOAD FILE HERE //==========================================
		$target_folder = UPLOAD_DIR_ABS.IMG_URBAN.$item.'/';
		if(!is_dir($target_folder))
			mkdir($target_folder);
		//print_r($_FILES);
		foreach($_FILES as $key=>$FILE)
		{
			list($type, $part, $idx) = explode("_", $key);
			$nError = uploadBrowsedFile('mainImage_'.$part.'_'.$idx, $part.'_'.$idx, EXT_IMG, $target_folder);
			if (empty($nError))
			{
				resizeImage($part.'_'.$idx.'.'.EXT_IMG, W_IMG_BIG, H_IMG_BIG, $target_folder);
				deleteFile(IMG_BIG.$part.'_'.$idx, EXT_IMG, $target_folder);
				duplicateFile($part.'_'.$idx.'.'.EXT_IMG, IMG_BIG.$part.'_'.$idx.'.'.EXT_IMG, $target_folder, $target_folder);

				resizeImage($part.'_'.$idx.'.'.EXT_IMG, W_IMG_GALLERY, H_IMG_GALLERY, $target_folder);
				deleteFile(IMG_MID.$part.'_'.$idx, EXT_IMG, $target_folder);
				duplicateFile($part.'_'.$idx.'.'.EXT_IMG, IMG_MID.$part.'_'.$idx.'.'.EXT_IMG, $target_folder, $target_folder);

				resizeImage($part.'_'.$idx.'.'.EXT_IMG, W_IMG_MIDDLE, H_IMG_MIDDLE, $target_folder);
				deleteFile(IMG_THUMB.$part.'_'.$idx, EXT_IMG, $target_folder);
				duplicateFile($part.'_'.$idx.'.'.EXT_IMG, IMG_THUMB.$part.'_'.$idx.'.'.EXT_IMG, $target_folder, $target_folder);

				resizeImage($part.'_'.$idx.'.'.EXT_IMG, W_IMG_MID_THUMB, H_IMG_MID_THUMB, $target_folder);
				deleteFile(IMG_MID_THUMB.$part.'_'.$idx, EXT_IMG, $target_folder);
				duplicateFile($part.'_'.$idx.'.'.EXT_IMG, IMG_MID_THUMB.$part.'_'.$idx.'.'.EXT_IMG, $target_folder, $target_folder);

				deleteFile($part.'_'.$idx, EXT_IMG, $target_folder);
			}
			$sMsg .= $part.'_'.$idx.': '.getLabel('strFile_'.$nError).'<br />';

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
			$aRows[$key] = $oUrban->GetByID($item, $key); // get by id
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
				$aRows[$key] = $oUrban->GetByID($item, $key); // get by id
			if (count($aRows) > 0) showForm($aRows); // load form
		}
		break;
	case ACT_DELETE:
		if (isset($item) && !empty($item))
		{
			// DELETE FILE HERE //==========================================
			$target_folder = UPLOAD_DIR_ABS.IMG_URBAN.$item.'/';
		    $files = glob( $target_folder . '*');
		    foreach( $files as $file ){
		        if( substr( $file, -1 ) == '/' )
		            delTree( $file );
		        else
		            unlink( $file );
		    }
		    rmdir($target_folder);

			// DELETE FILE HERE //==========================================
			$oUrban->Delete($item);
			echo getLabel('strDeleteOK').'<br /><br />'; // show ok message
			echo $strFilterListLink;
		}
		break;
	case ACT_DELETE_IMG:
		if (isset($item) && !empty($item))
		{
			// DELETE FILE HERE //==========================================
			deleteFile(IMG_URBAN.$item, EXT_IMG);
			deleteFile(IMG_URBAN_MID.$item, EXT_IMG);
			deleteFile(IMG_URBAN_THUMB.$item, EXT_IMG);
			// DELETE FILE HERE //==========================================
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oUrban->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
	case ACT_VIEW:
	default:
		if (isset($item) && !empty($item))
		{
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oUrban->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
}
//=========================================================
function form_content($aRows=null, $part=1) {
	global $page, $oPage, $oUrban, $oUser, $aLanguages, $aAbbrLanguages;
	require_once "../FCKeditor/fckeditor.php";
	foreach($aLanguages as $key=>$val)
	{
		?>
		<a name="#part<?=$part?>"/>
		<fieldset title="part<?=$aAbbrLanguages[$key].'_'.$part?>" class="half">
			<legend><?=getLabel('strPart'.$part, $key)?></legend>
			<label for="title_<?=$aAbbrLanguages[$key].'_'.$part?>"><?=getLabel('strUrbanName', $key)?></label><br />
			<input type="text" name="title<?=$aAbbrLanguages[$key].'_'.$part?>" id="title<?=$aAbbrLanguages[$key].'_'.$part?>"
				value="<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->{'Title'.$part}), '')?>" maxlength="255" class="fld" /><br />
			<br />
			<label for="author<?=$aAbbrLanguages[$key].'_'.$part?>"><?=getLabel('strAuthor', $key)?></label><br />
			<input type="text" name="author<?=$aAbbrLanguages[$key].'_'.$part?>" id="author<?=$aAbbrLanguages[$key].'_'.$part?>"
				value="<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->{'Author'.$part}), '')?>" maxlength="255" class="fld" /><br />
			<br />
			<label for="text<?=$aAbbrLanguages[$key].'_'.$part?>"><?=getLabel('strUrbanText', $key)?></label><br />
			<?
				$fck = new FCKeditor('text'.$aAbbrLanguages[$key].'_'.$part, '475', '150', 'DiagonaliDefault', IIF(!empty($aRows[$key]), $aRows[$key]->{'Text'.$part}, ''));
				$fck->Create();
			?>
			<br />
		</fieldset>
		<?
	} //end lang loop
	?>
	<br class="clear"/>
	<label><?=getLabel('strUrbanImage')?></label>
	<br />
	<div id="files<?=$part?>">
	<?
	if (!empty($aRows[DEF_LANG]) )
	{
		$template_thumb = '../'.UPLOAD_DIR.IMG_URBAN.$aRows[DEF_LANG]->UrbanID.'/'.IMG_THUMB.$part.'_*.'.EXT_IMG;
		$files_thumb = glob($template_thumb);
		$cntr = 1;
		foreach($files_thumb as $file)
		{
			echo '<div>'.drawImage($file);
			$id = 'mainImage_'.$part.'_'.$cntr;
			$cntr++;
			echo '<input type="file" name="'.$id.'" id="'.$id.'" size="47" accept="image/jpg" class="btn" /><br /></div>';
		}
	} //end if
	?>
	</div>
	<a href="#part<?=$part?>" onclick="addFilePart(<?=$part?>);">add picture</a>
	<br class="clear" />
	<?
}
function showForm($aRows=null)
{
	global $page, $oPage, $oUrban, $oUser, $aLanguages, $aAbbrLanguages;

	require_once "../FCKeditor/fckeditor.php";

	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->UrbanID;
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
	}
	echo getLabel('strRequired').'<br />'."\n";
?>
<script type="text/javascript">
<!--
function fCheck(frm) {
	if (!valEmpty("publication_date", "<?=getLabel('strEnter').getLabel('strUrbanDate')?>")) return false;
	if (!valMultyOption("parent_page_id", "<?=getLabel('strSelect').getLabel('strParentPage')?>")) return false;

	return true;
}
</script>
<form action="<?=setPage($page, 0, $itemID, ACT_SAVE).keepFilter().keepContext()?>" enctype="multipart/form-data" method="post" name="Urban" id="Urban" onsubmit="return fCheck(this);">
<input type="hidden" name="MAX_FILE_SIZE" value="2550000" />

<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="id"><?=getLabel('strUrbanID')?></label><?=$itemID?>
	<input type="hidden" name="id" id="id" value="<?=$itemID?>" /><br />
	<br />

	<label for="parent_page_id"><?=getLabel('strParentPage').formatVal().getLabel('multiple')?></label>
	<select name="parent_page_id[]" id="parent_page_id" multiple="multiple" size="5" class="fldsmall">
	<?
		$aPages = $oPage->ListAllAsArray(null, '', 'urban_list', true);
		if (!empty($aRows[DEF_LANG]))
		{
			$aRelPages = $oUrban->ListUrbanPagesAsArray($itemID);
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
		$dUrbanDate = date(DEFAULT_DATE_DB_FORMAT, $today_ts);
	?>
	<label for="publication_date"><?=getLabel('strUrbanDate').formatVal()?></label>
	<input type="text" name="publication_date" id="publication_date" maxlength="10" class="fldfilter"
	       value="<?=IIF(!empty($aRows[DEF_LANG]), formatDate($aRows[DEF_LANG]->PublicationDate), formatDate($dUrbanDate))?>"
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
<fieldset title="part<?=$aAbbrLanguages[$key].'_'.$part?>" class="half">
	<label for="main_title"><?=getLabel('strMainUrbanName', $key)?></label><br />
	<input type="text" name="main_title<?=$aAbbrLanguages[$key]?>" id="main_title<?=$aAbbrLanguages[$key]?>"
		value="<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->MainTitle), '')?>" maxlength="255" class="fld" /><br />
</fieldset>
<? } ?>
<div id="content_parts">
<?
if (!empty($aRows[DEF_LANG]))
{
	$arr = array(1, 2, 3);
}
else $arr = array(1);
foreach($arr as $a)
{
	form_content($aRows, $a);
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
<script type="text/javascript" src="../js/urban_edit.js"></script>
<?
}
//=========================================================
function showPreview($aRows=null)
{
	global $page, $oPage, $oUrban, $oUser, $aLanguages, $aEntityTypes;

	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	$sPages = '';
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->UrbanID;
	}
	if (!empty($itemID))
	{
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);

		$aPages = $oPage->ListAllAsArray(null, '', '', true);
		$aRelPages = $oUrban->ListUrbanPagesAsArray($itemID);
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
	<label><?=getLabel('strUrbanID')?></label>
	<?=$aRows[DEF_LANG]->UrbanID?><br />
	<br />
	<label><?=getLabel('strParentPage')?></label>
	<div><?=$sPages?></div><br />

	<label><?=getLabel('strUrbanDate')?></label>
	<?=formatDate($aRows[DEF_LANG]->PublicationDate)?><br />
	<br />
	<label><?=getLabel('strHide')?></label>
	<?=$aYesNo[$aRows[DEF_LANG]->IsHidden]?><br />
	<br />
	<label><?=getLabel('strLastUpdate')?></label>
	<?=IIF(!empty($aRows[DEF_LANG]), $aRows[DEF_LANG]->LastUpdate, '').getLabel('strByUser').IIF(!empty($aRows[DEF_LANG]), $rUser->Username.' ('.$rUser->FirstName.' '.$rUser->LastName.')', '')?><br />
</fieldset>
<!--
<iframe name="related" style="float:left;" width="500" height="600" src="<?=RELATED_PAGE.'?'.ARG_PAGE.'='.ENT_HOME.'&amp;'.ARG_RELID.'='.$itemID.'&amp;'.ARG_CAT.'='.$aEntityTypes[ENT_URBAN]?>"></iframe>
-->
	<br class="clear" />
<?
	$aParts = Array(1, 2, 3);

	foreach($aLanguages as $key=>$val)
	{
?>
<fieldset title="<?=$val?>" class="half">
	<legend><?=$val?></legend>
	<label for="main_title"><?=getLabel('strMainUrbanName', $key)?></label>
	<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->MainTitle), '')?>
	<br /><br />

<?
	foreach($aParts as $part)
	{
?>
	<label><?=getLabel('strUrbanName', $key).' '.$part?></label>
	<?=IIF(!empty($aRows[$key]), $aRows[$key]->{'Title'.$part}, '')?><br />
	<br />

	<label><?=getLabel('strUrbanImage')?></label><br />
<?
		$template_thumb = '../'.UPLOAD_DIR.IMG_URBAN.$itemID.'/'.IMG_THUMB.$part.'_*.'.EXT_IMG;
		$files_thumb = glob($template_thumb);

		foreach($files_thumb as $file)
		{
 			echo '<div>'.drawImage($file).'</div><br />';
		}
//		$sImageLink = '<br /><a href="'.setPage($page, 0, $itemID, ACT_DELETE_IMG).'">'.getLabel('delete', $key).'</a>';
?>
	<br />

	<label><?=getLabel('strAuthor', $key).' '.$part?></label>
	<div><?=IIF(!empty($aRows[$key]), $aRows[$key]->{'Author'.$part}, '')?></div><br />
	<br />

	<label><?=getLabel('strUrbanText', $key).' '.$part?></label>
	<div><?=IIF(!empty($aRows[$key]), $aRows[$key]->{'Text'.$part}, '')?></div><br />
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