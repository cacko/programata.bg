<?php
if (!$bInSite) die();
//=========================================================
$aFilter = array('parent_page', 'start_date', 'end_date', 'program_type', 'premiere_type', 'sel_city');
$strFilterListLink = '<a href="'.setPage($page).keepFilter().keepContext().'">'.getLabel('strBackToList').'</a><br /><br />';
//=========================================================
switch($action)
{
	case ACT_SAVE:
		$sMsg = '';
		//print_r($_POST);
		$event_item = 0;
		// NEW EVENT MULTILANG
		if (isset($_POST['new_event_id']))
		{
			if(!empty($_POST['new_event_id']))
			{
				$event_item = getPostedArg('new_event_id');
			}
			else
			{
				$sNewTitle = getPostedArg('title'.$aAbbrLanguages[DEF_LANG]);
				if (!empty($sNewTitle))
				{
					$event_item = $oEvent->Insert(getPostedArg('title'.$aAbbrLanguages[DEF_LANG]), 
								'', //getPostedArg('original'),
								getPostedArg('keywords'.$aAbbrLanguages[DEF_LANG]), 
								getPostedArg('description'.$aAbbrLanguages[DEF_LANG]), 
								getPostedArg('lead'.$aAbbrLanguages[DEF_LANG]),
								getPostedArg('text'.$aAbbrLanguages[DEF_LANG]), 
								'', //getPostedArg('features'.$aAbbrLanguages[DEF_LANG]),
								getPostedArg('comment'.$aAbbrLanguages[DEF_LANG]),
								getPostedArg('new_event_type_id'),
								0, //getPostedArg('event_subtype_id'),
								0, //getPostedArg('orig_lang_id'),
								0, //getPostedArg('translation_id'),
								0); //getPostedArg('hidden')// primary record
					$oEvent->InsertEventLabel($event_item, GRP_EXHIB, getPostedArg('genre_exhibition'));
					//$oEvent->InsertEventLabel($event_item, GRP_ARTIST, getPostedArg('genre_artist'));
					$oEvent->InsertEventLabel($event_item, GRP_MUSIC, getPostedArg('music_style'));
				}
			}
			if (!empty($event_item))
			{
				
				foreach($aLanguages as $key=>$val)
				{
					$oEvent->Update($event_item, 
							getPostedArg('title'.$aAbbrLanguages[$key]),
							'', //getPostedArg('original'),
							getPostedArg('keywords'.$aAbbrLanguages[$key]), 
							getPostedArg('description'.$aAbbrLanguages[$key]),
							getPostedArg('text'.$aAbbrLanguages[$key]),
							getPostedArg('lead'.$aAbbrLanguages[$key]),
							'', //getPostedArg('features'.$aAbbrLanguages[$key]),
							getPostedArg('comment'.$aAbbrLanguages[$key]),
							getPostedArg('new_event_type_id'),
							0, //getPostedArg('event_subtype_id'),
							0, //getPostedArg('orig_lang_id'),
							0, //getPostedArg('translation_id'),
							0, //getPostedArg('hidden'),
							$key); // update all
				}
			}
		}
		$nEventToGo = 0;
		if (!empty($event_item))
			$nEventToGo = $event_item;
		else
			$nEventToGo = getPostedArg('event_id');
		$xPlaces = getPostedArg('place_id');
		$aPlaces = explode('-', $xPlaces);
		// PROGRAM
		if (isset($_POST['id']) && !empty($_POST['id']))
		{
			$item = getPostedArg('id');
		}
		else
		{
			$item = $oProgram->Insert(getPostedArg('program_type_id'),
						  getPostedArg('festival_id'),
						  getPostedArg('city_id'),
						  $aPlaces[0], //getPostedArg('place_id'),
						  $aPlaces[1], //getPostedArg('place_hall_id'),
						  $nEventToGo,
						  getPostedArg('premiere_type_id'),
						  getPostedArg('hidden')); // primary record
		}
		//foreach($aLanguages as $key=>$val)
		//{
			$oProgram->Update($item,
						getPostedArg('program_type_id'),
						getPostedArg('festival_id'),
						getPostedArg('city_id'),
						$aPlaces[0], //getPostedArg('place_id'),
						$aPlaces[1], //getPostedArg('place_hall_id'),
						$nEventToGo,
						getPostedArg('premiere_type_id'),
						getPostedArg('hidden'));
				//$key); // update all
		//}
		// PROGRAM NOTE MULTILANG
		$bSaveNote = false;
		if(!empty($_POST['note_id']))
			$note_id = getPostedArg('note_id');
		else
		{
			$rNote = $oProgramNote->GetByProgramID($item);
			if ($rNote)
				$note_id = $rNote->ProgramNoteID;
			else
				$note_id = $oProgramNote->Insert($item, getPostedArg('note'.$aAbbrLanguages[DEF_LANG]));
		}
		foreach($aLanguages as $key=>$val)
		{
			$sProgramNote = getPostedArg('note'.$aAbbrLanguages[$key]);
			$oProgramNote->Update($note_id, $item, $sProgramNote, 0, $key);
			if (!empty($sProgramNote))
				$bSaveNote = true;
		}
		if(!$bSaveNote)
			$oProgramNote->Delete($note_id);
		// save relations
		$oProgram->DeleteProgramPage($item);
		$oProgram->InsertProgramPage($item, getPostedArg('parent_page_id'));
		$oProgram->DeleteProgramPlace($item);
		$oProgram->InsertProgramPlace($item, getPostedArg('rel_place_id'));
		$oProgram->DeleteProgramEvent($item);
		$oProgram->InsertProgramEvent($item, getPostedArg('rel_event_id'));
		$oProgramDatePeriod->Insert($item, parseDate(getPostedArg('start_date')), parseDate(getPostedArg('end_date')));
		$oProgramDateTime->Insert($item, parseDate(getPostedArg('program_date')), parseTime(getPostedArg('program_time')), getPostedArg('price'));
		///
		if (!$item)
			$sMsg .= getLabel('strSaveFailed'); // failed message
		else
			$sMsg .= getLabel('strSaveOK'); // ok message
		echo $sMsg.'<br /><br />';
		echo $strFilterListLink;
		$aRows = array();
		foreach($aLanguages as $key=>$val)
			$aRows[$key] = $oProgram->GetByID($item, $key); // get by id
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
				$aRows[$key] = $oProgram->GetByID($item, $key); // get by id
			if (count($aRows)>0) showForm($aRows); // load form
		}
		break;
	case ACT_DELETE:
		if (isset($item) && !empty($item))
		{
			$oProgram->Delete($item);
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
				$aRows[$key] = $oProgram->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
}
//=========================================================
function showForm($aRows=null)
{
	global $page, $cat, $oPage, $oProgram, $oFestival, $oPlace, $oEvent, $oLabel, $oProgramNote, $oUser, $aLanguages, $aAbbrLanguages, $aEntityTypes, $nDefCity;
	
	require_once "../FCKeditor/fckeditor.php";
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->ProgramID;
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
	}
	echo getLabel('strRequired').'<br />'."\n";
?>
<script type="text/javascript">
<!--
function fCheck(frm) {
	fSelectRelated();
<? foreach ($aLanguages as $key=>$val) {?>
	//if (!valEmpty("title<?=$aAbbrLanguages[$key]?>", "<?=getLabel('strEnter', $key).getLabel('strProgramName', $key)?>")) return false;
<?}?>
	if (!valOption("program_type_id", "<?=getLabel('strSelect').getLabel('strProgramType')?>")) return false;
	//if (!valOption("promotion_type_id", "<?=getLabel('strSelect').getLabel('strPromotionType')?>")) return false;
	//if (!valOption("parent_page_id", "<?=getLabel('strSelect').getLabel('strParentPage')?>")) return false;
	if (!valOption("city_id", "<?=getLabel('strSelect').getLabel('strCity')?>")) return false;
	if (!valOption("place_type_id", "<?=getLabel('strSelect').getLabel('strPlaceType')?>")) return false;
	if (!valEmpty("place_id", "<?=getLabel('strEnter').getLabel('strMainPlace')?>")) return false;
	if (!valOption("event_type_id", "<?=getLabel('strSelect').getLabel('strEventType')?>")) return false;
	//if (!valEmpty2("event_id", "title", "<?=getLabel('strEnter').getLabel('strMainEvent')?>")) return false;
	return true;
}

function fReload()
{
	var frm = document.getElementById('Program');
	frm.action = '';
	frm.submit();
}
//-->
</script>
<form action="<?=setPage($page, 0, $itemID, ACT_SAVE).keepFilter().keepContext()?>" enctype="multipart/form-data"
				method="post" name="Program" id="Program" onsubmit="return fCheck(this);">
<!--input type="hidden" name="MAX_FILE_SIZE" value="2550000" /-->

<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="id"><?=getLabel('strProgramID')?></label><?=$itemID?>
	<input type="hidden" name="<?=ARG_ID?>" id="<?=ARG_ID?>" value="<?=$itemID?>" /><br />
	<br />
	
	<label for="program_type_id"><?=getLabel('strProgramType').formatVal()?></label>
	<select name="program_type_id" id="program_type_id" class="fldsmall">
		<option value="0"><?=getLabel('strSelect')?></option>
	<?
		$aProgramTypes = getLabel('aProgramTypes');
		foreach($aProgramTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if ($key == $aRows[DEF_LANG]->ProgramTypeID)
					echo ' selected="selected"';
			}
			elseif (is_null($aRows) && $key == $cat)
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />

	<label for="parent_page_id"><?=getLabel('strParentPage').formatVal().getLabel('multiple')?></label>
	<select name="parent_page_id[]" id="parent_page_id" multiple="multiple" size="5" class="fldsmall">
	<?
		$aPages = $oPage->ListAllAsArray(null, '', 'event_list', true);
		if (!empty($aRows[DEF_LANG])) 
		{
			$aRelPages = $oProgram->ListProgramPagesAsArray($itemID);
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
	// DEFAULT EVENT & PLACE TYPES - ACCORDING TO PROGRAM TYPE
	$nSelPlaceType = 0;
	$nSelEventType = 0;
	$nSelType = $cat;
	$nSelCity = $nDefCity;
	if (!is_null($aRows))
	{
		$nSelType = $aRows[DEF_LANG]->ProgramTypeID;
		$nSelCity = $aRows[DEF_LANG]->CityID;
	}
	switch($nSelType)
	{
		case PROGRAM_THEATRE_PERFORMANCE: // theatre performance
			$nSelPlaceType = PLACE_THEATRE; // theatre
			$nSelEventType = EVENT_PERFORMANCE; // performance
			break;
		case PROGRAM_FILM_SCREENING: // film screening
			$nSelPlaceType = PLACE_CINEMA; // cinema
			$nSelEventType = EVENT_MOVIE; // movie
			break;
		case PROGRAM_EXHIBITION: // gallery exhibition
			$nSelPlaceType = PLACE_GALLERY; // gallery
			$nSelEventType = EVENT_EXHIBITION; // exhibition
			break;
		case PROGRAM_CLASSIC_MUSIC: // classic music
			$nSelPlaceType = PLACE_GALLERY; // gallery
			$nSelEventType = EVENT_CLASSIC_MUSIC; // classic music
			break;
		case PROGRAM_CONCERT: // concert
			$nSelPlaceType = PLACE_GALLERY; // gallery
			$nSelEventType = EVENT_LIVE_MUSIC; // musicians
			break;
		case PROGRAM_LIVE_MUSIC: // live music
			$nSelPlaceType = PLACE_CLUB; // clubs
			$nSelEventType = EVENT_LIVE_MUSIC; // musicians
			break;
		case PROGRAM_MUSIC_PARTY: // party time
			$nSelPlaceType = PLACE_CLUB; // clubs
			$nSelEventType = EVENT_MUSIC_PARTY; // parties
			break;
		case PROGRAM_LOGOS: // more / slovo
			$nSelPlaceType = PLACE_GALLERY; // gallery
			$nSelEventType = EVENT_LOGOS; // other events
			break;
	}
	if (!is_null($aRows))
	{
		$nSelPlace = $aRows[DEF_LANG]->PlaceID;
		$rPlace = $oPlace->GetByID($nSelPlace);
		$nSelPlaceType = $rPlace->PlaceTypeID;
		
		$nSelEvent = $aRows[DEF_LANG]->EventID;
		$rEvent = $oEvent->GetByID($nSelEvent);
		$nSelEventType = $rEvent->EventTypeID;
	}
	if (is_array($_POST) && count($_POST)>0)
	{
		$nNewCity = getPostedArg('city_id');
		if ($nNewCity != $nSelCity)
		{
			$nSelCity = $nNewCity;
			$nSelPlace = 0;
		}
		$nNewPlaceType = getPostedArg('place_type_id', $nSelPlaceType);
		if ($nNewPlaceType != $nSelPlaceType)
		{
			$nSelPlaceType = $nNewPlaceType;
			$nSelPlace = 0;
		}
		$nNewEventType = getPostedArg('event_type_id');
		if ($nNewEventType != $nSelEventType)
		{
			$nSelEventType = $nNewEventType;
			$nSelEvent = 0;
		}
	}
	?>
	<label for="place_type_id"><?=getLabel('strCity').formatVal().' - '.getLabel('strPlaceType').formatVal()?></label>
	<select name="city_id" id="city_id" class="fldfilter">
		<option value="0"><?=getLabel('strSelect')?></option>
	<?
		$aCities = getLabel('aCities');
		foreach($aCities as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if ($key == $nSelCity)//$aRows[DEF_LANG]->CityID)
					echo ' selected="selected"';
			}
			elseif (is_null($aRows) && $key == $nSelCity)
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select> &nbsp; 
	<select name="place_type_id" id="place_type_id" class="fldfilter">
		<option value="0"><?=getLabel('strSelect')?></option>
	<?
		$aPlaceTypes = getLabel('aPlaceTypes');
		foreach($aPlaceTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if ($key == $nSelPlaceType)
					echo ' selected="selected"';
			}
			elseif (is_null($aRows) && $key == $nSelPlaceType)
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<input style="float:right; margin: 10px 20px 20px 0px;" type="button" value="<?=getLabel('strReload')?>" onclick="fReload();" />
	<br class="clear" />
	
	
	<label for="place_id"><?=getLabel('strPlaces').formatVal()?></label>
	<select name="place_id" id="place_id" class="fldsmall">
		<option value="0"><?=getLabel('strSelect')?></option>
	<?
		$aPlaces = $oPlace->ListAllHallsAsArray(null, $nSelCity, $nSelPlaceType);
		foreach($aPlaces as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				$sPlaceHallKey = $aRows[DEF_LANG]->PlaceID.IIF(!empty($aRows[DEF_LANG]->PlaceHallID), '-'.$aRows[DEF_LANG]->PlaceHallID, '');
				if ($key == $sPlaceHallKey)
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	<?
	// THEATRE PERFORMANCE ONLY - GUEST THEATRE (RELATED PLACE) FIELD
	if ($cat == PROGRAM_THEATRE_PERFORMANCE || $aRows[DEF_LANG]->ProgramTypeID == PROGRAM_THEATRE_PERFORMANCE) // theatre performance
	{ 
	?>
	<label for="rel_place_id"><?=getLabel('strGuest').getLabel('multiple')?></label>
	<select name="rel_place_id[]" id="rel_place_id" size="5" multiple="multiple" class="fldsmall">
	<?
		$aPlaces = $oPlace->ListAllAsArray(null, null, 2); // place type = theatre
		$aRelPlaces = $oProgram->ListProgramPlacesAsArray($aRows[DEF_LANG]->ProgramID);
		foreach($aPlaces as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if (in_array($key, $aRelPlaces))
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	<?
	}
	?>
</fieldset>
<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="festival_id"><?=getLabel('strFestival')?></label>
	<select name="festival_id" id="festival_id" class="fldsmall">
		<option value="0"><?=getLabel('strSelect')?></option>
	<?
		$aFestivals = $oFestival->ListAllAsArray();
		foreach($aFestivals as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if ($key == $aRows[DEF_LANG]->FestivalID)
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	<label for="event_type_id"><?=getLabel('strEventType').formatVal()?></label>
	<select name="event_type_id" id="event_type_id" class="fldfilter">
		<option value="0"><?=getLabel('strSelect')?></option>
	<?
		$aEventTypes = getLabel('aEventTypes');
		foreach($aEventTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if ($key == $nSelEventType)
					echo ' selected="selected"';
			}
			elseif (is_null($aRows) && $key == $nSelEventType)
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select> &nbsp;
	<input type="button" value="<?=getLabel('strReload')?>" onclick="fReload();" />
	<br />
	<br />

	<label for="event_id"><?=getLabel('strEvents').formatVal()?></label>
	<select name="event_id" id="event_id" class="fldsmall">
		<option value="0"><?=getLabel('strSelect')?></option>
	<?
		$aEvents = $oEvent->ListAllAsArrayPlain($nSelEventType);
		foreach($aEvents as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if ($key == $aRows[DEF_LANG]->EventID)
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
</fieldset>
<br class="clear" />
	<?
	// NEW EVENT FIELDS - FOR ALL BUT THEATRE PERFORMANCE & FILM SCREENING
	if ($cat == PROGRAM_THEATRE_PERFORMANCE || $aRows[DEF_LANG]->ProgramTypeID == PROGRAM_THEATRE_PERFORMANCE ||
	    $cat == PROGRAM_FILM_SCREENING || $aRows[DEF_LANG]->ProgramTypeID == PROGRAM_FILM_SCREENING)
	{
		// no new event
	}
	else
	{
		$aEventTypes = getLabel('aEventTypes');
		$aEventRows = null;
		if (!empty($aRows[DEF_LANG]))
		{
			foreach($aLanguages as $key=>$val)
			{
				$aEventRows[$key] = $oEvent->GetByID($aRows[DEF_LANG]->EventID, $key);
			}
		}
	?>
	<br class="clear" />
	<label><?=$aEventTypes[$nSelEventType]?></label>
	<?=getLabel('strNew')?>: <a href="#" onclick="showHide('new_event', 1);return false;"><?=getLabel('show')?></a> | <a href="#" onclick="showHide('new_event', 0);return false;"><?=getLabel('hide')?></a>
	<br />
	<input type="hidden" id="new_event_type_id" name="new_event_type_id" value="<?=$nSelEventType?>" />
	<input type="hidden" id="new_event_id" name="new_event_id" value="<?//IIF(!empty($aRows[DEF_LANG]), $aRows[DEF_LANG]->EventID, 0)?>" />
	<br />
	<div id="new_event">
<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<?
	// ============================= CATEGORY LABELS =============================
		if ($nSelEventType == EVENT_EXHIBITION) // exhibition
		{
?>
	<label for="genre_exhibition"><?=$aEventTypes[EVENT_EXHIBITION].getLabel('multiple')?></label>
	<select name="genre_exhibition[]" id="genre_exhibition" multiple="multiple" size="8" class="fldsmall">
	<?
		$aExhibitionGenres = $oLabel->ListAllAsArray(GRP_EXHIB, '', true);
		foreach($aExhibitionGenres as $key=>$val)
		{
			echo '<option value="'.$key.'">'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
<?
		}
		if ($nSelEventType == EVENT_LIVE_MUSIC) // artist
		{
?>
	<!--label for="genre_artist"><?=$aEventTypes[EVENT_LIVE_MUSIC].getLabel('multiple')?></label>
	<select name="genre_artist[]" id="genre_artist" multiple="multiple" size="4" class="fldsmall">
	<?
		/*$aArtistGenres = $oLabel->ListAllAsArray(GRP_ARTIST, '', true);
		foreach($aArtistGenres as $key=>$val)
		{
			echo '<option value="'.$key.'">'.$val.'</option>'."\n";
		}*/
	?>
	</select><br />
	<br /-->
<?
		}
		if ($nSelEventType == EVENT_CLASSIC_MUSIC || $nSelEventType == EVENT_LIVE_MUSIC ||
		    $nSelEventType == EVENT_MUSIC_PARTY || $nSelEventType == EVENT_CONCERT)
		{
?>
	<label for="music_style"><?=getLabel('strMusicStyle').getLabel('multiple')?></label>
	<select name="music_style[]" id="music_style" multiple="multiple" size="8" class="fldsmall">
	<?
		$aMusicStyle = $oLabel->ListAllAsArray(GRP_MUSIC, '', true);
		foreach($aMusicStyle as $key=>$val)
		{
			echo '<option value="'.$key.'">'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
<?
		}
?>
</fieldset>
<br class="clear" />
<?
		foreach($aLanguages as $key=>$val)
		{
			
	?>
<fieldset title="<?=$val?>" class="half">
	<legend><?=$val?></legend>
	<label for="title<?=$aAbbrLanguages[$key]?>"><?=getLabel('strEventName', $key).formatVal()?></label><br />
	<input type="text" name="title<?=$aAbbrLanguages[$key]?>" id="title<?=$aAbbrLanguages[$key]?>"
		value="<?//IIF(!empty($aEventRows[$key]), htmlspecialchars($aEventRows[$key]->Title), '')?>" maxlength="255" class="fld" /><br />
	<br />
	
	<label for="comment<?=$aAbbrLanguages[$key]?>"><?=getLabel('strComment', $key)?></label><br />
	<?
		$fck = new FCKeditor('comment'.$aAbbrLanguages[$key], '475', '120', 'DiagonaliBasic', ''); //IIF(!empty($aEventRows[$key]), $aEventRows[$key]->Comment, '')
		$fck->Create();
	?>
	<br />
	<label for="lead<?=$aAbbrLanguages[$key]?>"><?=getLabel('strEventLead', $key)?></label><br />
	<?
		$fck = new FCKeditor('lead'.$aAbbrLanguages[$key], '475', '120', 'DiagonaliBasic', '');//IIF(!empty($aEventRows[$key]), $aEventRows[$key]->Lead, '')
		$fck->Create();
	?>
	<br />
	<label for="text<?=$aAbbrLanguages[$key]?>"><?=getLabel('strDescription', $key)?></label><br />
	<?
		$fck = new FCKeditor('text'.$aAbbrLanguages[$key], '475', '400', 'DiagonaliDefault', '');//IIF(!empty($aEventRows[$key]), $aEventRows[$key]->Description, '')
		$fck->Create();
	?>
	<br />
	<!--label for="description<?=$aAbbrLanguages[$key]?>"><?=getLabel('strMetaDescription', $key)?></label><br />
	<textarea cols="36" rows="3" name="description<?=$aAbbrLanguages[$key]?>" id="description<?=$aAbbrLanguages[$key]?>"><?//IIF(!empty($aEventRows[$key]), $aEventRows[$key]->MetaDescription, '')?></textarea><br />
	<br /-->
	<label for="keywords<?=$aAbbrLanguages[$key]?>"><?=getLabel('strMetaKeywords', $key)?></label><br />
	<textarea cols="36" rows="3" name="keywords<?=$aAbbrLanguages[$key]?>" id="keywords<?=$aAbbrLanguages[$key]?>"><?//IIF(!empty($aEventRows[$key]), $aEventRows[$key]->MetaKeywords, '')?></textarea><br />
	<br />
</fieldset>
	<script type="text/javascript">//showHide('new_event', 1);</script>
	<?
		}
	?>
	<br class="clear" />
	<br />
	</div>
	<?
	}
	?>
<fieldset title="<?=getLabel('strCommonData')?>" style="width: 960px;">
	<legend><?=getLabel('strCommonData')?></legend>	
	<?
	// RELATED EVENTS, I.E. PARTICIPANTS - FOR ALL MUSIC EVENTS
	if ($cat == PROGRAM_CLASSIC_MUSIC || $aRows[DEF_LANG]->ProgramTypeID == PROGRAM_CLASSIC_MUSIC ||
	    $cat == PROGRAM_LIVE_MUSIC || $aRows[DEF_LANG]->ProgramTypeID == PROGRAM_LIVE_MUSIC ||
	    $cat == PROGRAM_MUSIC_PARTY || $aRows[DEF_LANG]->ProgramTypeID == PROGRAM_MUSIC_PARTY ||
	    $cat == PROGRAM_CONCERT || $aRows[DEF_LANG]->ProgramTypeID == PROGRAM_CONCERT)
	{
	?>
<script type="text/javascript">
<!--
	function addOption()
	{
		var sourceelem = document.getElementById('rel_event_all');
		var targetelem = document.getElementById('rel_event_id');
		if (sourceelem && targetelem)
		{
			var opt = sourceelem.options[sourceelem.options.selectedIndex];
			targetelem.appendChild(opt);
		}
	}
	
	function removeOption()
	{
		var sourceelem = document.getElementById('rel_event_id');
		if (sourceelem)
		{
			var opt = sourceelem.options[sourceelem.options.selectedIndex];
			sourceelem.removeChild(opt);
		}
	}
	
	function fSelectRelated()
	{
		var sourceelem = document.getElementById('rel_event_id');
		if (sourceelem)
		{
			for (var i=0; i<sourceelem.options.length; i++)
			{
				sourceelem.options[i].selected = true;
			}
		}
	}
//-->
</script>
	<div style="float:left">
	<label for="rel_event_all"><?=getLabel('strAllParticipants')?></label><br />
	<select name="rel_event_all" id="rel_event_all" size="10" class="fldsmall">
	<?
		$aEvents = $oEvent->ListAllAsArrayPlain(EVENT_LIVE_MUSIC); // musicians //multiple="multiple" 
		$aRelEvents = $oProgram->ListProgramEventsAsArray($aRows[DEF_LANG]->ProgramID);
		foreach($aEvents as $key=>$val)
		{
			echo '<option value="'.$key.'">'.$val.'</option>'."\n";
			/*echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if (in_array($key, $aRelEvents))
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";*/
		}
	?>
	</select><br />
	<br />
	</div>
	<div style="float:left; width: 150px; text-align: center;">
	<br /><br />
	<input type="button" value="<?=getLabel('strAdd')?>" class="btns" onclick="addOption()" />
	<br /><br />
	<input type="button" value="<?=getLabel('strRemove')?>" class="btns" onclick="removeOption()" /> 
	<!--input type="button" value="<?=getLabel('strSelectAll')?>" class="btn" onclick="fSelectRelated()" /--> 
	</div>
	<div style="float:left">
	<label for="rel_event_id"><?=getLabel('strSelectedParticipants')?></label><br />
	<select name="rel_event_id[]" id="rel_event_id" class="fldsmall" multiple="multiple" size="10">
	<?
		foreach($aEvents as $key=>$val)
		{
			if (!empty($aRows[DEF_LANG]))
			{
				if (in_array($key, $aRelEvents))
					echo '<option value="'.$key.'" selected="selected">'.$val.'</option>'."\n";
			}
		}
	?>
	</select><br />
	<br />
	</div>
	<br class="clear" />
	<?
	}
	// PREMIERE - FOR THEATRE PERFORMANCE & FILM SCREENING
	if ($cat == PROGRAM_THEATRE_PERFORMANCE || $aRows[DEF_LANG]->ProgramTypeID == PROGRAM_THEATRE_PERFORMANCE ||
	    $cat == PROGRAM_FILM_SCREENING || $aRows[DEF_LANG]->ProgramTypeID == PROGRAM_FILM_SCREENING)
	{
	?>
	
	<label for="premiere_type_id"><?=getLabel('strPremiereType')?></label>
	<select name="premiere_type_id" id="premiere_type_id" class="fldsmall">
		<option value="0"><?=getLabel('strSelect')?></option>
	<?
		$aPremiereTypes = getLabel('aPremiereTypes');
		foreach($aPremiereTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($aRows[DEF_LANG]))
			{
				if ($key == $aRows[DEF_LANG]->PremiereTypeID)
					echo ' selected="selected"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />
	<?
	}
	// EMPTY DATE PERIOD FIELDS - FOR EXHIBITIONS & LOGOS
	if (empty($aRows[DEF_LANG]))
	{
		if ($cat == PROGRAM_EXHIBITION || $aRows[DEF_LANG]->ProgramTypeID == PROGRAM_EXHIBITION ||
		    $cat == PROGRAM_LOGOS || $aRows[DEF_LANG]->ProgramTypeID == PROGRAM_LOGOS)
		{
			$today_ts = mktime (0,0,0,date("m"), date("d"), date("Y"));
			$dStartDate = date(DEFAULT_DATE_DB_FORMAT, $today_ts);
			$dEndDate = increaseDate($dStartDate, ADMIN_WEEK_DAYS);
	?>
	<label for="start_date"><?=getLabel('strStartDate').formatVal().'<br />'.getLabel('strEndDate').formatVal()?></label>
	<input type="text" name="start_date" id="start_date" maxlength="10" class="fldfilter"
	       value="<?//IIF(!empty($aRows[DEF_LANG]), formatDate($aRows[DEF_LANG]->StartDate), formatDate($dStartDate))?>" 
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" /> &nbsp; 
	<input type="text" name="end_date" id="end_date" maxlength="10" class="fldfilter"
	       value="<?//IIF(!empty($aRows[DEF_LANG]), formatDate($aRows[DEF_LANG]->EndDate), formatDate($dEndDate))?>" 
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" /><br />
	<br />
	<?
		}
	// EMPTY DATE TIME FIELDS - FOR ALL BUT EXHIBITIONS
		if ($cat != PROGRAM_EXHIBITION && $aRows[DEF_LANG]->ProgramTypeID != PROGRAM_EXHIBITION)
		{
	?>
	<label for="program_date"><?=getLabel('strProgramDate').formatVal().' / '.getLabel('strProgramTime').formatVal()?></label>
	<input type="text" name="program_date" id="program_date" maxlength="10" class="fldfilter"
	       value="<?//IIF(!empty($aRows[DEF_LANG]), formatDate($aRows[DEF_LANG]->StartDate), formatDate($dStartDate))?>" 
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" /> &nbsp; 
	<input type="text" name="program_time" id="program_time" value="<?//IIF(!empty($aRows[DEF_LANG]), formatDate($aRows[DEF_LANG]->StartDate), formatDate($dStartDate))?>" maxlength="10" class="fldfilter" /><br />
	<br />
	
	<label for="price"><?=getLabel('strPrice')?></label>
	<input type="text" name="price" id="price" value="<?//IIF(!empty($aRows[DEF_LANG]), formatDate($aRows[DEF_LANG]->StartDate), formatDate($dStartDate))?>" maxlength="10" class="fldfilter" /><br />
	<br />
	<?
		}
	}
	?>
	
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
	// PROGRAM NOTES, MULTILANGUAGE & OPTIONAL
	$bShowNoteID = true;
	foreach($aLanguages as $key=>$val)
	{
		$rNote = $oProgramNote->GetByProgramID($itemID, $key);
		if ($bShowNoteID)
			echo '<input type="hidden" id="note_id" name="note_id" value="'.$rNote->ProgramNoteID.'" />';
?>
<fieldset title="<?=$val?>" class="half">
	<legend><?=$val?></legend>
	<label for="note<?=$aAbbrLanguages[$key]?>" class="long"><?=getLabel('strProgramNote', $key)?></label><br /><br />
	<input type="text" name="note<?=$aAbbrLanguages[$key]?>" id="note<?=$aAbbrLanguages[$key]?>"
		value="<?=IIF(($rNote), htmlspecialchars($rNote->Title), '')?>" maxlength="255" class="fld" /><br />
	<br />
</fieldset>
<?
		$bShowNoteID = false;
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
	global $page, $oPage, $oProgram, $oUser, $aLanguages, $aEntityTypes, $oProgramNote, $oNews, $oPublication, $oFestival, $oPlace, $oPlaceHall, $oEvent;
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	$aCities = getLabel('aCities');
	$aProgramTypes = getLabel('aProgramTypes');
	$aPremiereTypes = getLabel('aPremiereTypes');
	$aPages = $oPage->ListAllAsArray(null, '', 'event_list', true);
	$sPages = '';
	$sFestival = '';
	$sEvent = '';
	$sRelEvents = '';
	$sPlace = '';
	$sRelPlaces = '';
	$sHall = '';
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->ProgramID;
	}
	if (!empty($itemID))
	{
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
				
		$aRelPages = $oProgram->ListProgramPagesAsArray($itemID);
		if (is_array($aRelPages) && count($aRelPages)>0)
		{
			foreach($aRelPages as $cat)
			{
				if (!empty($sPages))
					$sPages .= '<br/>';
				$sPages .= $aPages[$cat];
			}
		}
		else
			$sPages = '<em>'.getLabel('strNone').'</em>';
			
		$aRelEvents = $oProgram->ListProgramEventsAsArray($itemID);
		if (is_array($aRelEvents) && count($aRelEvents)>0)
		{
			foreach($aRelEvents as $cat)
			{
				$rEvent = $oEvent->GetByID($cat);
				if (!empty($sRelEvents))
					$sRelEvents .= ', ';//'<br/>'
				$sRelEvents .= $rEvent->Title;
			}
		}
		else
			$sRelEvents = '<em>'.getLabel('strNone').'</em>';
			
		$aRelPlaces = $oProgram->ListProgramPlacesAsArray($itemID);
		if (is_array($aRelPlaces) && count($aRelPlaces)>0)
		{
			foreach($aRelPlaces as $cat)
			{
				$rPlace = $oPlace->GetByID($cat);
				if (!empty($sRelPlaces))
					$sRelPlaces .= '<br/>';
				$sRelPlaces .= $rPlace->Title;
			}
		}
		else
			$sRelPlaces = '<em>'.getLabel('strNone').'</em>';
		
		if (!empty($aRows[DEF_LANG]->FestivalID))
		{
			$rFestival = $oFestival->GetByID($aRows[DEF_LANG]->FestivalID);
			$sFestival = $rFestival->Title;
		}
		else
			$sFestival = '<em>'.getLabel('strNone').'</em>';
		
		if (!empty($aRows[DEF_LANG]->PlaceID))
		{
			$rPlace = $oPlace->GetByID($aRows[DEF_LANG]->PlaceID);
			$sPlace = $rPlace->Title;
		}
		else
			$sPlace = '<em>'.getLabel('strNone').'</em>';
		
		if (!empty($aRows[DEF_LANG]->PlaceHallID))
		{
			$rHall = $oPlaceHall->GetByID($aRows[DEF_LANG]->PlaceHallID);
			$sHall = $rHall->Title;
		}
		else
			$sHall = '<em>'.getLabel('strNone').'</em>';
		
		if (!empty($aRows[DEF_LANG]->EventID))
		{
			$rEvent = $oEvent->GetByID($aRows[DEF_LANG]->EventID);
			$sEvent = $rEvent->Title;
		}
		else
			$sEvent = '<em>'.getLabel('strNone').'</em>';
?>
<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label><?=getLabel('strProgramID')?></label>
	<?=$aRows[DEF_LANG]->ProgramID?><br />
	<br />
	<label><?=getLabel('strProgramType')?></label>
	<?=$aProgramTypes[$aRows[DEF_LANG]->ProgramTypeID]?><br />
	<br />
	<label><?=getLabel('strParentPage')?></label>
	<div><?=$sPages?></div><br />
	<br />
	<label><?=getLabel('strCity')?></label>
	<?=$aCities[$aRows[DEF_LANG]->CityID]?><br />
	<br />
	<label><?=getLabel('strFestival')?></label>
	<?=$sFestival?><br />
	<br />
	<label><?=getLabel('strMainPlace')?></label>
	<?=$sPlace?><br />
	<br />
	<label><?=getLabel('strHall')?></label>
	<?=$sHall?><br />
	<br />
	<?
	// THEATRE PERFORMANCE ONLY - GUEST THEATRE (RELATED PLACE) FIELD
		if ($aRows[DEF_LANG]->ProgramTypeID == PROGRAM_THEATRE_PERFORMANCE) { // theatre performance
	?>
	<label><?=getLabel('strGuest')?></label>
	<div><?=$sRelPlaces?></div><br />
	<br />
	<?
		}
	?>
	<label><?=getLabel('strMainEvent')?></label>
	<?=$sEvent?><br />
	<a target="_blank" href="<?=setPage(ENT_EVENT, $rEvent->EventTypeID, $rEvent->EventID, ACT_VIEW)?>"><?=getLabel('edit').getLabel('new_window')?></a> | 
	<a target="related" href="<?=RELATED_PAGE.'?'.ARG_PAGE.'='.ENT_ATTACHMENT.'&amp;'.ARG_RELID.'='.$rEvent->EventID.'&amp;'.ARG_CAT.'='.$aEntityTypes[ENT_EVENT]?>"><?=getLabel('strGallery')?></a><br />
	<br />
	<?
	// RELATED EVENTS, I.E. PARTICIPANTS - FOR ALL MUSIC EVENTS
		if ($aRows[DEF_LANG]->ProgramTypeID == PROGRAM_CLASSIC_MUSIC ||
		    $aRows[DEF_LANG]->ProgramTypeID == PROGRAM_LIVE_MUSIC ||
		    $aRows[DEF_LANG]->ProgramTypeID == PROGRAM_MUSIC_PARTY ||
		    $aRows[DEF_LANG]->ProgramTypeID == PROGRAM_CONCERT)
		{
	?>
	<label><?=getLabel('strParticipant')?></label>
	<div><?=$sRelEvents?></div><br />
	<br />
	<?
		}
		// PREMIERE - FOR THEATRE PERFORMANCE & FILM SCREENING
		if ($aRows[DEF_LANG]->ProgramTypeID == PROGRAM_THEATRE_PERFORMANCE ||
		    $aRows[DEF_LANG]->ProgramTypeID == PROGRAM_FILM_SCREENING)
		{
	?>
	<label><?=getLabel('strPremiereType')?></label>
	<?=$aPremiereTypes[$aRows[DEF_LANG]->PremiereTypeID]?><br />
	<br />
	<?
		}
	?>
	<a target="related" href="<?=RELATED_PAGE.'?'.ARG_PAGE.'='.IIF($aRows[DEF_LANG]->ProgramTypeID == PROGRAM_EXHIBITION, ENT_DATE_PERIOD, ENT_DATE_TIME).'&amp;'.ARG_RELID.'='.$itemID.'&amp;'.ARG_CAT.'='.$aEntityTypes[ENT_PROGRAM]?>"><?=getLabel('strProgram')?></a><br />
	<br />
	<!--label><?=getLabel('strStartDate')?></label>
	<?=formatDate($aRows[DEF_LANG]->StartDate)?><br />
	<br />
	<label><?=getLabel('strEndDate')?></label>
	<?=formatDate($aRows[DEF_LANG]->EndDate)?><br />
	<br /-->
	<label><?=getLabel('strHide')?></label>
	<?=$aYesNo[$aRows[DEF_LANG]->IsHidden]?><br />
	<br />
	<label><?=getLabel('strLastUpdate')?></label>
	<?=IIF(!empty($aRows[DEF_LANG]), $aRows[DEF_LANG]->LastUpdate, '').getLabel('strByUser').IIF(!empty($aRows[DEF_LANG]), $rUser->Username.' ('.$rUser->FirstName.' '.$rUser->LastName.')', '')?><br />
</fieldset>
<iframe name="related" style="float:left;" width="500" height="600" src="<?=RELATED_PAGE.'?'.ARG_PAGE.'='.IIF($aRows[DEF_LANG]->ProgramTypeID == PROGRAM_EXHIBITION, ENT_DATE_PERIOD, ENT_DATE_TIME).'&amp;'.ARG_RELID.'='.$itemID.'&amp;'.ARG_CAT.'='.$aEntityTypes[ENT_PROGRAM]?>"></iframe>
<br class="clear" />
<?
	foreach($aLanguages as $key=>$val)
	{
		$rNote = $oProgramNote->GetByProgramID($itemID, $key);
		if ($rNote)
		{
?>
<fieldset title="<?=$val?>" class="half">
	<legend><?=$val?></legend>
	<label><?=getLabel('strProgramNote', $key)?></label>
	<div><?=IIF(($rNote), $rNote->Title, '')?></div><br />
	<br />
</fieldset>
<?
		}
	}
?>
<br class="clear" />
<ul class="nav">
	<li><a href="<?=setPage($page, 0, $itemID, ACT_EDIT).keepFilter().keepContext()?>"><?=getLabel('edit')?></a></li>
	<li><a href="<?=setPage($page, 0, $itemID, ACT_DELETE).keepFilter().keepContext()?>" onclick="return confMsg('<?=getLabel('strDeleteQ')?>');return false;"><?=getLabel('delete')?></a></li>
</ul>
<?
	}
}
?>