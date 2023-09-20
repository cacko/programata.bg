<?php
if (!$bInSite) die();
//=========================================================
$aFilter = array('parent_page', 'keyword', 'event_type', 'letter', ARG_CAT);
$strFilterListLink = '<a href="'.setPage($page).keepFilter().keepContext().'">'.getLabel('strBackToList').'</a><br /><br />';
//=========================================================
switch($action)
{
	case ACT_SAVE:
		$sMsg = '';
		//print_r($_POST);
		$xTypes = getPostedArg('event_type_id');
		$aTypes = explode('-', $xTypes);
		if (isset($_POST['id']) && !empty($_POST['id']))
		{
			$item = getPostedArg('id');
		}
		else
		{
			$item = $oEvent->Insert(getPostedArg('title'.$aAbbrLanguages[DEF_LANG]), 
						getPostedArg('original'),
						getPostedArg('keywords'.$aAbbrLanguages[DEF_LANG]), 
						getPostedArg('description'.$aAbbrLanguages[DEF_LANG]), 
						getPostedArg('lead'.$aAbbrLanguages[DEF_LANG]),
						getPostedArg('text'.$aAbbrLanguages[DEF_LANG]), 
						getPostedArg('features'.$aAbbrLanguages[DEF_LANG]),
						getPostedArg('comment'.$aAbbrLanguages[DEF_LANG]),
						$aTypes[0], //getPostedArg('event_type_id'),
						$aTypes[1], //getPostedArg('event_subtype_id'),
						getPostedArg('orig_lang_id'),
						getPostedArg('translation_id'),
						getPostedArg('hidden')); // primary record
		}
		foreach($aLanguages as $key=>$val)
		{
			$oEvent->Update($item, 
					getPostedArg('title'.$aAbbrLanguages[$key]),
					getPostedArg('original'),
					getPostedArg('keywords'.$aAbbrLanguages[$key]), 
					getPostedArg('description'.$aAbbrLanguages[$key]),
					getPostedArg('text'.$aAbbrLanguages[$key]),
					getPostedArg('lead'.$aAbbrLanguages[$key]),
					getPostedArg('features'.$aAbbrLanguages[$key]),
					getPostedArg('comment'.$aAbbrLanguages[$key]),
					$aTypes[0], //getPostedArg('event_type_id'),
					$aTypes[1], //getPostedArg('event_subtype_id'),
					getPostedArg('orig_lang_id'),
					getPostedArg('translation_id'),
					getPostedArg('hidden'),
					$key); // update all
		}
		// save relations
		//$oEvent->DeleteEventPage($item);
		//$oEvent->InsertEventPage($item, getPostedArg('parent_page_id'));
		$oEvent->DeleteEventLabel($item);
		$oEvent->InsertEventLabel($item, GRP_MOVIE, getPostedArg('genre_movie'));
		$oEvent->InsertEventLabel($item, GRP_PERF, getPostedArg('genre_performance'));
		$oEvent->InsertEventLabel($item, GRP_EXHIB, getPostedArg('genre_exhibition'));
		//$oEvent->InsertEventLabel($item, GRP_ARTIST, getPostedArg('genre_artist'));
		$oEvent->InsertEventLabel($item, GRP_TRANS, getPostedArg('translation'));
		$oEvent->InsertEventLabel($item, GRP_LANG, getPostedArg('orig_lang'));
		$oEvent->InsertEventLabel($item, GRP_MUSIC, getPostedArg('music_style'));
		// UPLOAD FILE HERE //==========================================
		$nError = uploadBrowsedFile('main_image', IMG_EVENT.$item, EXT_IMG);/*, 'empty.jpg'*/
		if (empty($nError))
		{
			resizeImage(IMG_EVENT.$item.'.'.EXT_IMG, W_IMG_GALLERY, H_IMG_GALLERY);
			deleteFile(IMG_EVENT_MID.$item, EXT_IMG);
			duplicateFile(IMG_EVENT.$item.'.'.EXT_IMG, IMG_EVENT_MID.$item.'.'.EXT_IMG);
			resizeImage(IMG_EVENT_MID.$item.'.'.EXT_IMG, W_IMG_MIDDLE, H_IMG_MIDDLE);
			deleteFile(IMG_EVENT_THUMB.$item, EXT_IMG);
			duplicateFile(IMG_EVENT.$item.'.'.EXT_IMG, IMG_EVENT_THUMB.$item.'.'.EXT_IMG);
			resizeImage(IMG_EVENT_THUMB.$item.'.'.EXT_IMG, W_IMG_THUMB, H_IMG_THUMB);
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
			$aRows[$key] = $oEvent->GetByID($item, $key); // get by id
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
				$aRows[$key] = $oEvent->GetByID($item, $key); // get by id
			if (count($aRows)>0) showForm($aRows); // load form
		}
		break;
	case ACT_DELETE:
		if (isset($item) && !empty($item))
		{
			// DELETE FILE HERE //==========================================
			deleteFile(IMG_EVENT.$item, EXT_IMG);
			deleteFile(IMG_EVENT_MID.$item, EXT_IMG);
			deleteFile(IMG_EVENT_THUMB.$item, EXT_IMG);
			// DELETE FILE HERE //==========================================
			$oEvent->Delete($item);
			echo getLabel('strDeleteOK').'<br /><br />'; // show ok message
			echo $strFilterListLink;
		}
		break;
	case ACT_DELETE_IMG:
		if (isset($item) && !empty($item))
		{
			// DELETE FILE HERE //==========================================
			deleteFile(IMG_EVENT.$item, EXT_IMG);
			deleteFile(IMG_EVENT_MID.$item, EXT_IMG);
			deleteFile(IMG_EVENT_THUMB.$item, EXT_IMG);
			// DELETE FILE HERE //==========================================
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oEvent->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
	default:
	case ACT_VIEW:
		if (isset($item) && !empty($item))
		{
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oEvent->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
}
//=========================================================
function showForm($aRows=null)
{
	global $page, $cat, $oPage, $oEvent, $oUser, $oLabel, $aLanguages, $aAbbrLanguages;
	
	require_once "../FCKeditor/fckeditor.php";
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->EventID;
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
	}
	echo getLabel('strRequired').'<br />'."\n";
?>
<script type="text/javascript">
<!--
function fCheck(frm) {
<? foreach ($aLanguages as $key=>$val) {?>
	if (!valEmpty("title<?=$aAbbrLanguages[$key]?>", "<?=getLabel('strEnter', $key).getLabel('strEventName', $key)?>")) return false;
<?}?>
	//if (!valOption("parent_page_id", "<?=getLabel('strSelect').getLabel('strParentPage')?>")) return false;
	return true;
}

function showGenre(event_type)
{
	var gNodes = document.getElementById('category').childNodes;
	for (var i = 0; gNodes.length > i; i ++)
	{
		if (gNodes[i].tagName == "DIV")
		{
			if (gNodes[i].attributes['class'].value == 'e' + event_type)
				gNodes[i].style.display = "block";
			else
				gNodes[i].style.display = "none";
		}
	}
	var mNode = document.getElementById('any_music');
	if (event_type == 13 || event_type == 14 || event_type == 24 || event_type == 27)
	{
		mNode.style.display = "block";
	}
}
//-->
</script>
<form action="<?=setPage($page, 0, $itemID, ACT_SAVE).keepFilter().keepContext()?>" enctype="multipart/form-data" method="post" name="Event" id="Event" onsubmit="return fCheck(this);">
<input type="hidden" name="MAX_FILE_SIZE" value="2550000" />

<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="id"><?=getLabel('strEventID')?></label><?=$itemID?>
	<input type="hidden" name="id" id="id" value="<?=$itemID?>" /><br />
	<br />
	
	<!--label for="parent_page_id"><?=getLabel('strParentPage').formatVal().getLabel('multiple')?></label><br />
	<select name="parent_page_id[]" id="parent_page_id" multiple="multiple" size="6" class="fldsmall">
	<?
		$aPages = $oPage->ListAllAsArray(null, '', 'event_list', true);
		if (!empty($aRows[DEF_LANG])) 
		{
			$aRelPages = $oEvent->ListEventPagesAsArray($itemID);
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
	<br /-->
	
	<label for="event_type_id"><?=getLabel('strEventType').formatVal()?></label>
	<select name="event_type_id" id="event_type_id" class="fldsmall" onchange="showGenre(this.value);return false;">
		<option value="0"><?=getLabel('strSelect')?></option>
	<?
		$aEventTypes = getLabel('aEventTypes');
		/*$aEventSubtypes = getLabel('aEventSubtypes');
		$aAllowedEventSubtypes = getLabel('aAllowedEventSubtypes');
		foreach($aEventTypes as $key=>$val)
		{
			if (is_array($aAllowedEventSubtypes[$key]))
			{
				echo '<optgroup label="'.$val.'">'."\n";
				foreach($aEventSubtypes as $k=>$v)
				{
					if (in_array($k, $aAllowedEventSubtypes[$key]))
					{
						echo '<option value="'.$key.'-'.$k.'"';
						if ($k == $aRows[DEF_LANG]->EventSubtypeID)
							echo ' selected="selected"';
						echo '>'.$v.'</option>'."\n";
					}
				}
				echo '</optgroup>'."\n";
			}
			else
			{
				echo '<option value="'.$key.'-"';
				if ($key == $aRows[DEF_LANG]->EventTypeID)
					echo ' selected="selected"';
				elseif (is_null($aRows) && $key == $cat)
					echo ' selected="selected"';
				echo '>'.$val.'</option>'."\n";
			}
		}*/
		foreach($aEventTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == $aRows[DEF_LANG]->EventTypeID)
				echo ' selected="selected"';
			elseif (is_null($aRows) && $key == $cat)
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	
	
	
	<!--label for="translation_id"><?=getLabel('strTranslation')?></label>
	<select name="translation_id" id="translation_id" class="fldsmall">
		<option value="0"><?=getLabel('strNone')?></option>
	<?
		$aTranslations = getLabel('aTranslations');
		foreach($aTranslations as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == $aRows[DEF_LANG]->TranslationID)
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br /-->
	
	<!--label for="orig_lang_id"><?=getLabel('strOrigLanguage')?></label>
	<select name="orig_lang_id" id="orig_lang_id" class="fldsmall">
		<option value="0"><?=getLabel('strNone')?></option>
	<?
		/*$aOrigLanguages = getLabel('aOrigLanguages');
		foreach($aOrigLanguages as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == $aRows[DEF_LANG]->OriginalLanguageID)
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}*/
	?>
	</select><br />
	<br /-->
	
	<hr />
	<? if (!empty($aRows[DEF_LANG]) )
		{
			$sMainImageFile = '../'.UPLOAD_DIR.IMG_EVENT_THUMB.$itemID.'.'.EXT_IMG;
			if (!is_file($sMainImageFile))
			{
				$sMainImageFile = '../'.UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
			}
	?>
	<label><?=getLabel('strEventImage')?></label>
	<?=drawImage($sMainImageFile)?><br />
	<br />
	<?}?>
	<label for="main_image"><?=getLabel('strEventImageFull')?></label>
	<input type="file" name="main_image" id="main_image" size="47" accept="image/x-jpg" class="btn" /><br />
	<br class="clear" />
	<hr />
	<br />
	
	<label for="original"><?=getLabel('strOriginalTitle')?></label>
	<input type="text" name="original" id="original" maxlength="255" class="fld"
		value="<?=IIF(!empty($aRows[DEF_LANG]), htmlspecialchars($aRows[DEF_LANG]->OriginalTitle), '')?>" /><br />
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

<fieldset title="<?=getLabel('strCategory')?>" class="half" id="category">
	<legend><?=getLabel('strCategory')?></legend>

	<div class="e10">
	<label for="genre_movie"><?=$aEventTypes[10].getLabel('multiple')?></label>
	<select name="genre_movie[]" id="genre_movie" multiple="multiple" size="8" class="fldsmall">
	<?
		$aMovieGenres = $oLabel->ListAllAsArray(GRP_MOVIE);
		if (!empty($aRows[DEF_LANG])) 
		{
			$aRelLabels = $oEvent->ListEventLabelsAsArray($itemID, GRP_MOVIE);
		}
		foreach($aMovieGenres as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if (in_array($key, $aRelLabels))
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	
	<label for="orig_lang"><?=getLabel('strOrigLanguage').getLabel('multiple')?></label>
	<select name="orig_lang[]" id="orig_lang" multiple="multiple" size="8" class="fldsmall">
	<?
		$aOrigLanguages = $oLabel->ListAllAsArray(GRP_LANG);
		if (!empty($aRows[DEF_LANG])) 
		{
			$aRelLabels = $oEvent->ListEventLabelsAsArray($itemID, GRP_LANG);
		}
		foreach($aOrigLanguages as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if (in_array($key, $aRelLabels))
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	
	<label for="translation"><?=getLabel('strTranslation').getLabel('multiple')?></label>
	<select name="translation[]" id="translation" multiple="multiple" size="4" class="fldsmall">
	<?
		$aTranslations = $oLabel->ListAllAsArray(GRP_TRANS);
		if (!empty($aRows[DEF_LANG])) 
		{
			$aRelLabels = $oEvent->ListEventLabelsAsArray($itemID, GRP_TRANS);
		}
		foreach($aTranslations as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if (in_array($key, $aRelLabels))
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	</div>
	
	<div class="e11">
	<label for="genre_performance"><?=$aEventTypes[11].getLabel('multiple')?></label>
	<select name="genre_performance[]" id="genre_performance" multiple="multiple" size="8" class="fldsmall">
	<?
		$aPerformanceGenres = $oLabel->ListAllAsArray(GRP_PERF);
		if (!empty($aRows[DEF_LANG])) 
		{
			$aRelLabels = $oEvent->ListEventLabelsAsArray($itemID, GRP_PERF);
		}
		foreach($aPerformanceGenres as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if (in_array($key, $aRelLabels))
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	</div>
	
	<div class="e12">
	<label for="genre_exhibition"><?=$aEventTypes[12].getLabel('multiple')?></label>
	<select name="genre_exhibition[]" id="genre_exhibition" multiple="multiple" size="8" class="fldsmall">
	<?
		$aExhibitionGenres = $oLabel->ListAllAsArray(GRP_EXHIB);
		if (!empty($aRows[DEF_LANG])) 
		{
			$aRelLabels = $oEvent->ListEventLabelsAsArray($itemID, GRP_EXHIB);
		}
		foreach($aExhibitionGenres as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if (in_array($key, $aRelLabels))
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	</div>
	
	<!--div class="e14">
	<label for="genre_artist"><?=$aEventTypes[14].getLabel('multiple')?></label>
	<select name="genre_artist[]" id="genre_artist" multiple="multiple" size="4" class="fldsmall">
	<?
		/*$aArtistGenres = $oLabel->ListAllAsArray(GRP_ARTIST);
		if (!empty($aRows[DEF_LANG])) 
		{
			$aRelLabels = $oEvent->ListEventLabelsAsArray($itemID, GRP_ARTIST);
		}
		foreach($aArtistGenres as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if (in_array($key, $aRelLabels))
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}*/
	?>
	</select><br />
	<br />
	</div-->
	
	<div id="any_music" class="e0">
	<label for="music_style"><?=getLabel('strMusicStyle').getLabel('multiple')?></label>
	<select name="music_style[]" id="music_style" multiple="multiple" size="8" class="fldsmall">
	<?
		$aMusicStyle = $oLabel->ListAllAsArray(GRP_MUSIC);
		if (!empty($aRows[DEF_LANG])) 
		{
			$aRelLabels = $oEvent->ListEventLabelsAsArray($itemID, GRP_MUSIC);
		}
		foreach($aMusicStyle as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if (in_array($key, $aRelLabels))
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	</div>
</fieldset>
<br class="clear" style="clear:left;" />
<?
	$nDivToShow = 0;
	if (is_null($aRows))
		$nDivToShow = $cat;
	else
		$nDivToShow = $aRows[DEF_LANG]->EventTypeID;
?>
<script type="text/javascript">showGenre(<?=$nDivToShow?>);</script>

<?
	foreach($aLanguages as $key=>$val)
	{
?>
<fieldset title="<?=$val?>" class="half">
	<legend><?=$val?></legend>
	<label for="title<?=$aAbbrLanguages[$key]?>"><?=getLabel('strEventName', $key).formatVal()?></label><br />
	<input type="text" name="title<?=$aAbbrLanguages[$key]?>" id="title<?=$aAbbrLanguages[$key]?>"
		value="<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->Title), '')?>" maxlength="255" class="fld" /><br />
	<br />
	
	<label for="comment<?=$aAbbrLanguages[$key]?>"><?=getLabel('strComment', $key)?></label><br />
	<?
		$fck = new FCKeditor('comment'.$aAbbrLanguages[$key], '475', '120', 'DiagonaliBasic', IIF(!empty($aRows[$key]), $aRows[$key]->Comment, ''));
		$fck->Create();
	?>
	<br />
	<?
		if ($cat == 10 || $aRows[DEF_LANG]->EventTypeID == 10) { // movies only
	?>
	<label for="features<?=$aAbbrLanguages[$key]?>"><?=getLabel('strFeatures', $key)?></label><br />
	<input type="text" name="features<?=$aAbbrLanguages[$key]?>" id="features<?=$aAbbrLanguages[$key]?>"
		value="<?=IIF(!empty($aRows[$key]), htmlspecialchars($aRows[$key]->Features), '')?>" maxlength="255" class="fld" /><br />
	<br />
	<?
		}
	?>
	<label for="lead<?=$aAbbrLanguages[$key]?>"><?=getLabel('strEventLead', $key)?></label><br />
	<?
		$fck = new FCKeditor('lead'.$aAbbrLanguages[$key], '475', '120', 'DiagonaliBasic', IIF(!empty($aRows[$key]), $aRows[$key]->Lead, ''));
		$fck->Create();
	?>
	<br />
	<label for="text<?=$aAbbrLanguages[$key]?>"><?=getLabel('strDescription', $key)?></label><br />
	<?
		$fck = new FCKeditor('text'.$aAbbrLanguages[$key], '475', '400', 'DiagonaliDefault', IIF(!empty($aRows[$key]), $aRows[$key]->Description, ''));
		$fck->Create();
	?>
	<br />
	
	<!--label for="description<?=$aAbbrLanguages[$key]?>"><?=getLabel('strMetaDescription', $key)?></label><br />
	<textarea cols="36" rows="3" name="description<?=$aAbbrLanguages[$key]?>" id="description<?=$aAbbrLanguages[$key]?>"><?=IIF(!empty($aRows[$key]), $aRows[$key]->MetaDescription, '')?></textarea><br />
	<br /-->
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
	global $page, $oPage, $oEvent, $oUser, $oLabel, $aLanguages, $aEntityTypes;
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	$aEventTypes = getLabel('aEventTypes');
	//$aEventSubtypes = getLabel('aEventSubtypes');
	//$aTranslations = getLabel('aTranslations');
	//$aOrigLanguages = getLabel('aOrigLanguages');
	$sPages = '';
	$sMusicStyle = '';
	$sMovieGenre = '';
	$sPerformanceGenre = '';
	$sExhibitionGenre = '';
	//$sArtistGenre = '';
	$sTranslation = '';
	$sOrigLanguage = '';
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->EventID;
	}
	if (!empty($itemID))
	{
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
		
		/*$aPages = $oPage->ListAllAsArray(null, '', '', true);
		$aRelPages = $oEvent->ListEventPagesAsArray($itemID);
		if (count($aRelPages)>0)
		{
			foreach($aRelPages as $cat)
				$sPages .= $aPages[$cat].'<br/>';
		}
		else
			$sPages = '<em>'.getLabel('strNone').'</em><br />';*/
		
		$aMovieGenres = $oLabel->ListAllAsArray(GRP_MOVIE);
		$aRelLabels = $oEvent->ListEventLabelsAsArray($itemID, GRP_MOVIE);
		if (is_array($aRelLabels) && count($aRelLabels)>0)
		{
			foreach($aRelLabels as $cat)
				$sMovieGenre .= $aMovieGenres[$cat].', ';
		}
		else
			$sMovieGenre = '<em>'.getLabel('strNone').'</em>';
		
		$aOrigLanguages = $oLabel->ListAllAsArray(GRP_LANG);
		$aRelLabels = $oEvent->ListEventLabelsAsArray($itemID, GRP_LANG);
		if (is_array($aRelLabels) && count($aRelLabels)>0)
		{
			foreach($aRelLabels as $cat)
				$sOrigLanguage .= $aOrigLanguages[$cat].', ';
		}
		else
			$sOrigLanguage = '<em>'.getLabel('strNone').'</em>';
		
		$aTranslations = $oLabel->ListAllAsArray(GRP_TRANS);
		$aRelLabels = $oEvent->ListEventLabelsAsArray($itemID, GRP_TRANS);
		if (is_array($aRelLabels) && count($aRelLabels)>0)
		{
			foreach($aRelLabels as $cat)
				$sTranslation .= $aTranslations[$cat].', ';
		}
		else
			$sTranslation = '<em>'.getLabel('strNone').'</em>';
		
		$aPerformanceGenres = $oLabel->ListAllAsArray(GRP_PERF);
		$aRelLabels = $oEvent->ListEventLabelsAsArray($itemID, GRP_PERF);
		if (is_array($aRelLabels) && count($aRelLabels)>0)
		{
			foreach($aRelLabels as $cat)
				$sPerformanceGenre .= $aPerformanceGenres[$cat].', ';
		}
		else
			$sPerformanceGenre = '<em>'.getLabel('strNone').'</em>';
		
		$aExhibitionGenres = $oLabel->ListAllAsArray(GRP_EXHIB);
		$aRelLabels = $oEvent->ListEventLabelsAsArray($itemID, GRP_EXHIB);
		if (is_array($aRelLabels) && count($aRelLabels)>0)
		{
			foreach($aRelLabels as $cat)
				$sExhibitionGenre .= $aExhibitionGenres[$cat].', ';
		}
		else
			$sExhibitionGenre = '<em>'.getLabel('strNone').'</em>';
		
		/*$aArtistGenres = $oLabel->ListAllAsArray(GRP_ARTIST);
		$aRelLabels = $oEvent->ListEventLabelsAsArray($itemID, GRP_ARTIST);
		if (count($aRelLabels)>0)
		{
			foreach($aRelLabels as $cat)
				$sArtistGenre .= $aArtistGenres[$cat].', ';
		}
		else
			$sArtistGenre = '<em>'.getLabel('strNone').'</em>';*/
		
		$aMusicStyle = $oLabel->ListAllAsArray(GRP_MUSIC);
		$aRelLabels = $oEvent->ListEventLabelsAsArray($itemID, GRP_MUSIC);
		if (is_array($aRelLabels) && count($aRelLabels)>0)
		{
			foreach($aRelLabels as $cat)
				$sMusicStyle .= $aMusicStyle[$cat].', ';
		}
		else
			$sMusicStyle = '<em>'.getLabel('strNone').'</em>';
		
		$sImageLink = '<br /><a href="'.setPage($page, 0, $itemID, ACT_DELETE_IMG).'">'.getLabel('delete').'</a>';
		$sMainImageFile = '../'.UPLOAD_DIR.IMG_EVENT_THUMB.$itemID.'.'.EXT_IMG;
		if (!is_file($sMainImageFile))
		{
			$sImageLink = '';
			$sMainImageFile = '../'.UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
		}
?>
<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label><?=getLabel('strEventID')?></label>
	<?=$aRows[DEF_LANG]->EventID?><br />
	<br />
	<!--label><?=getLabel('strParentPage')?></label>
	<div><?=$sPages?></div><br /-->
	<label><?=getLabel('strEventType')?></label>
	<?=$aEventTypes[$aRows[DEF_LANG]->EventTypeID]?><br />
	<br />
	<!--label><?=getLabel('strEventSubtype')?></label>
	<?//$aEventSubtypes[$aRows[DEF_LANG]->EventSubtypeID]?><br />
	<br />
	<label><?=getLabel('strTranslation')?></label>
	<?//$aTranslations[$aRows[DEF_LANG]->TranslationID]?><br />
	<br />
	<label><?=getLabel('strOrigLanguage')?></label>
	<?//$aOrigLanguages[$aRows[DEF_LANG]->OriginalLanguageID]?><br />
	<br /-->
<? if ($aRows[DEF_LANG]->EventTypeID == 10) {?>
	<label><?=$aEventTypes[10]?></label>
	<div><?=$sMovieGenre?></div><br />
	<br />
	<label><?=getLabel('strTranslation')?></label>
	<div><?=$sTranslation?></div><br />
	<br />
	<label><?=getLabel('strOrigLanguage')?></label>
	<div><?=$sOrigLanguage?></div><br />
	<br />
<? } elseif ($aRows[DEF_LANG]->EventTypeID == 11) { ?>
	<label><?=$aEventTypes[11]?></label>
	<div><?=$sPerformanceGenre?></div><br />
	<br />
<? } elseif ($aRows[DEF_LANG]->EventTypeID == 12) { ?>
	<label><?=$aEventTypes[12]?></label>
	<div><?=$sExhibitionGenre?></div><br />
	<br />
<? } elseif ($aRows[DEF_LANG]->EventTypeID == 14) { ?>
	<!--label><?=$aEventTypes[14]?></label>
	<div><?//$sArtistGenre?></div><br />
	<br /-->
<? } 
   if ($aRows[DEF_LANG]->EventTypeID == 13 || $aRows[DEF_LANG]->EventTypeID == 14 ||
       $aRows[DEF_LANG]->EventTypeID == 24 || $aRows[DEF_LANG]->EventTypeID == 27) { ?>
	<label><?=getLabel('strMusicStyle')?></label>
	<div><?=$sMusicStyle?></div><br />
	<br />
<? } ?>
	<label><?=getLabel('strEventImage')?></label>
	<div><?=drawImage($sMainImageFile).$sImageLink?></div>
	<br />
	<label><?=getLabel('strOriginalTitle')?></label>
	<?=IIF(!empty($aRows[DEF_LANG]), $aRows[DEF_LANG]->OriginalTitle, '')?><br />
	<br />
	<label><?=getLabel('strHide')?></label>
	<?=$aYesNo[$aRows[DEF_LANG]->IsHidden]?><br />
	<br />
	<label><?=getLabel('strLastUpdate')?></label>
	<?=IIF(!empty($aRows[DEF_LANG]), $aRows[DEF_LANG]->LastUpdate, '').getLabel('strByUser').IIF(!empty($aRows[DEF_LANG]), $rUser->Username.' ('.$rUser->FirstName.' '.$rUser->LastName.')', '')?><br />
</fieldset>
<iframe name="related" style="float:left" class="related" src="<?=RELATED_PAGE.'?'.ARG_PAGE.'='.ENT_HOME.'&amp;'.ARG_RELID.'='.$itemID.'&amp;'.ARG_CAT.'='.$aEntityTypes[ENT_EVENT]?>"></iframe>
<br class="clear" />
<?
	foreach($aLanguages as $key=>$val)
	{
?>
<fieldset title="<?=$val?>" class="half">
	<legend><?=$val?></legend>
	<label><?=getLabel('strEventName', $key)?></label>
	<?=IIF(!empty($aRows[$key]), $aRows[$key]->Title, '')?><br />
	<br />
	
	<label><?=getLabel('strComment', $key)?></label>
	<div><?=IIF(!empty($aRows[$key]), $aRows[$key]->Comment, '')?></div><br />
	<br />
	
	<label><?=getLabel('strFeatures', $key)?></label>
	<div><?=IIF(!empty($aRows[$key]), $aRows[$key]->Features, '')?></div><br />
	<br />
	
	<label><?=getLabel('strEventLead', $key)?></label>
	<div><?=IIF(!empty($aRows[$key]), $aRows[$key]->Lead, '')?></div><br />
	<br />
	
	<label><?=getLabel('strDescription', $key)?></label>
	<div><?=IIF(!empty($aRows[$key]), $aRows[$key]->Description, '')?></div><br />
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