<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	include('template/event_details.php');
}
$nMode = getRequestArg('m', 2);
if (!is_numeric($nMode)) $nMode = 2;
if ($page == 55 && $nMode == 1) // list by place
{
	include_once('template/event_search_quick.php');
	include_once('template/event_list_weekly_by_place.php');
}
elseif (!isset($item) || empty($item))
{
	$sListContent = '';
	$aPageCityFilters = $oPage->ListPageCityFiltersAsArray($page);
	$aEventTypes = getLabel('aEventTypes');

	if (!isset($rsEvent))
	{
		if (!isset($bShowPremiere))
			$bShowPremiere = false;
		if (!isset($nPageToGo))
			$nPageToGo = $page;
		if ($rCurrentPage->TemplateFile != 'event_search')
		{
			if(isset($_REQUEST['interval']))
			{
				$dInterval = getRequestArg('interval', '');
				
				$dEndTime = increaseTime($dInterval, DEFAULT_TIME_DB_FORMAT);
			    $dEndDate = increaseTime($dInterval, DEFAULT_DATE_DB_FORMAT);

			    $dStartTime = increaseTime(5, DEFAULT_TIME_DB_FORMAT); // 5 minutes from now
			    $dStartDate = increaseTime(5, DEFAULT_DATE_DB_FORMAT);
			
			
				$rsEvent = $oProgram->ListAllEvents($nPageToGo, null, $city, $sKeyword, '', $dStartDate, $dEndDate, $dStartTime, $dEndTime, $bShowPremiere, false, array(13, 1));
			}
			//$rsEvent = $oEvent->ListAll($page, null, null, '', '', false, 0, $aContext);
			$rsEvent = $oProgram->ListAllEvents($nPageToGo, null, $city, $sKeyword, '', $dStartDate, $dEndDate, $dStartTime, $dEndTime, $bShowPremiere);

		}
		elseif((is_array($_POST) && count($_POST) > 0))
		{
			$sKeyword = getPostedArg('keyword', '');
			$nMusicStyle = getPostedArg('music_style', 0);
			$nGenre = getPostedArg('genre', 0);
			$nOrigLanguage = getPostedArg('orig_lang', 0);
			$nTranslation = getPostedArg('translation', 0);
			$nPageToGo = getPostedArg('sel_page', $page);
			/*if ($nPageToGo == 29)
			{
				$nPageToGo = 30;
				$bShowPremiere = true;
			}*/
			$nCityToGo = getPostedArg('sel_city', $city);
			$nGenreGroup = 0;
			switch($nRootPage)
			{
			    case 21:
				$nGenreGroup = GRP_MOVIE;
				break;
			    case 22:
				$nGenreGroup = GRP_PERF;
				break;
			    case 24:
				$nGenreGroup = GRP_EXHIB;
				break;
			    case 25:
				$nGenreGroup = GRP_ARTIST;
				break;
			}
			if ($rCurrentPage->TemplateFile == 'event_search')
			{
				$dToday = date(DEFAULT_DATE_DB_FORMAT);
				$dDefEndDate = increaseDate($dToday, THIS_WEEK_DAYS);
				$dSelStartDate = getPostedArg('sel_start_date', '');
				$dSelEndDate = getPostedArg('sel_end_date', '');
				if (empty($dSelStartDate))
				{
					if (!empty($sKeyword))
						$dStartDate = null;//$dToday;
					else
						$dStartDate = $dToday;
				}
				else
					$dStartDate = parseDate($dSelStartDate);
				if (empty($dSelEndDate))
				{
					if (!empty($sKeyword))
						$dEndDate = null;//$dDefEndDate;
					else
						$dEndDate = $dDefEndDate;
				}
				else
					$dEndDate = parseDate($dSelEndDate);
				if ($dSelStartDate > $dSelEndDate)
				{
					$dStartDate = $dToday;
					$dEndDate = $dDefEndDate;
				}
				$dSelTime = getPostedArg('sel_timespan', '');
				if (in_array($dSelTime, array_keys($aTimes)))
				{
				    $dSelStartTime = $aStartTimes[$dSelTime];
				    $dSelEndTime = $aEndTimes[$dSelTime];
				}
				if (empty($nPageToGo)) // empty post
				{
					$aPagesToGo = $oPage->ListAllAsArraySimple($rCurrentPage->ParentPageID, '', 'event_list');
					$nPageToGo = array_keys($aPagesToGo);
				}
				//echo $dStartDate.' - '.$dEndDate.'<br />';
			}
			if (empty($dSelStartDate) && empty($dSelEndDate) && empty($dSelTime) && empty($sKeyword) && empty($nMusicStyle) && empty($nGenre) && empty($nOrigLanguage) && empty($nTranslation)) {} // do nothing
			else
			{
				$rsEvent = $oProgram->ListAllEventsAdvanced($nPageToGo, null, $nCityToGo, $sKeyword, '',
									    $nMusicStyle, $nGenre, $nGenreGroup, $nOrigLanguage, $nTranslation,
									    $dStartDate, $dEndDate, $dSelStartTime, $dSelEndTime, $bShowPremiere);
			}
		}
		elseif(!empty($dSelStartDate))
		{
			$dSelStartDate = $dSelEndDate;
			$sQuickSearchCriteria .= '<br />'.getLabel('strSearchResults').':<br />';
			if (!empty($dSelStartDate))
				$sQuickSearchCriteria .= getLabel('strStartDate').': '.formatDate($dSelStartDate).'<br />';
			if (!empty($dSelTime))
				$sQuickSearchCriteria .= getLabel('strStartTime').': '.$aTimes[$dSelTime].'<br />';
			$rsEvent = $oProgram->ListAllEvents($page, null, $city, '', '', $dSelStartDate, $dSelEndDate, $dSelStartTime, $dSelEndTime);
		}
		else
		{
			$nDefLetter = 2;
			if ($lang == LANG_BG)
				$nDefLetter = 6;
			$nReqLetter = getRequestArg('alpha', $nDefLetter);
			if (!empty($nReqLetter))
			{
				$aAlphabetGroupsLetters = getLabel('aAlphabetGroupsLetters');
				$xReqLetter = array_values($aAlphabetGroupsLetters[$nReqLetter]);
				//print_r($xReqLetter);
				//$rsEvent = $oEvent->ListAll($page, null, null, '', $xReqLetter, false, 0, $aContext);
				$rsEvent = $oProgram->ListAllEvents($nPageToGo, null, $city, '', $xReqLetter, $dStartDate, $dEndDate, $dStartTime, $dEndTime, $bShowPremiere);
			}
		}
		//echo $dStartDate.' - '.$dEndDate.'<br />';
	}
	if (is_array($_POST) && count($_POST)>0 && $rCurrentPage->TemplateFile != 'event_search')
		echo $sSearchCriteria;
	elseif(!empty ($dSelStartDate))
	{
		echo $sQuickSearchCriteria;
	}
	if (isset($rsEvent))
	{
		if(!isset($result['events'])) $result['events'] = array();
		if (mysql_num_rows($rsEvent))
		{
			$aEvents = array();
			$aPlaces = array();

			$aLetterKeys = array();
			$aAlphabetLettersKeys = getLabel('aAlphabetLettersKeys');
			$i = 0;
			while($row=mysql_fetch_object($rsEvent)) {
				if (!empty($row->EventID))
				{
					$event = array();
					$aPlaces = array();
//					if (!in_array($row->EventID, $aEvents))
//					{
						$aEvents[] = $row->EventID;
						$event['id'] = $row->EventID;
						$event['name'] = stripComments($row->Title);

						$now = strtotime(date(DEFAULT_DATETIME_DISPLAY_FORMAT, mktime(date('H'), date("i"), 0, date('n'), date("j"), date("Y"))));
						$eventStart = strtotime($row->ProgramTime.' '.$row->ProgramDate);

						$minutesLeft = round(abs($eventStart - $now) / 60,2);
						$event['start'] = $row->ProgramDate.' '.$row->ProgramTime;

						if(strcmp($_REQUEST['request'],'categoryList')) $event['type'] = $page;

						// PAGES - CATEGORIES
						$sMainImageFile = '';
						$bMidImage = false;
						if($_REQUEST['w'] >= 450) {
							$sMainImageFile = UPLOAD_DIR.IMG_EVENT.$row->EventID.'.'.EXT_IMG;
						}
						else {
							$sMainImageFile = UPLOAD_DIR.IMG_EVENT_MID.$row->EventID.'.'.EXT_IMG;
						}
						if(is_file('../'.$sMainImageFile)) {
							$event['image'] = $sMainImageFile;
						}
//					}
//					if (!in_array($row->PlaceID, $aPlaces))
//					{
						$aPlaces[] = $row->PlaceID;
						$rPlace = $oPlace->GetByID($row->PlaceID);
						$place = array();
						$aPlacePages = $oPlace->ListPlacePagesAsArray($row->PlaceID);
						if (is_array($aPlacePages) && count($aPlacePages)>0) {
							$place["id"] = $row->PlaceID;
							$place["name"] .= stripComments($rPlace->Title);
						}
						else {
							$place["id"] = $row->PlaceID;
							$place["title"] .= stripComments($rPlace->Title);
						}
						$event['place'] = $place;
						array_push($result['events'], $event);
//					}
				}
			}
		}
	}
	unset($rsEvent);
	unset($bShowPremiere);
	unset($nPageToGo);

}
/*
die();
			while($row=mysql_fetch_object($rsEvent))
			{
				if (!empty($row->EventID))
				{
					if (!in_array($row->EventID, $aEvents))
					{
						$event = array();
						$aEvents[] = $row->EventID;
						$aPlaces = array();

						$event['id'] = $row->EventID;
						$event['title'] = $row->Title;

						// PAGES - CATEGORIES
						$sMainImageFile = '';
						$bMidImage = false;
						$sMainImageFile = UPLOAD_DIR.IMG_EVENT_MID.$row->EventID.'.'.EXT_IMG;
						$event['image'] = $sMainImageFile;
						if (!is_file($sMainImageFile))
						{
							$sMainImageFile = '';//$sMainImageFile = UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
							$rsAttachment = $oAttachment->ListAll($row->EventID, $aEntityTypes[ENT_EVENT], 1); //$row->EventTypeID
							while($rAttachment = mysql_fetch_object($rsAttachment))
							{
								$sMainImageFile = FILEBANK_DIR.$rAttachment->AttachmentID.'.'.$rAttachment->Extension;// EXT_IMG;//.
								$bMidImage = true;
							}
						}

						$sCategory = '';

						$sRelEvents = '';
						$aRelEvents = $oProgram->ListProgramEventsAsArray($row->MainProgramID);
						if (is_array($aRelEvents) && count($aRelEvents)>0)
						{
							if (!empty($sRelEvents)) $sRelEvents .= ' - ';
							$aEventsToShow = $oEvent->ListByIDsAsArray($aRelEvents);
							foreach($aEventsToShow as $key=>$val)
							{
								$sRelEvents .= ' &middot; <a href="'.setPage($nRootPage, 0, $key).'">'.stripComments($val).'</a> '."\n";
							}
							if (!empty($sRelEvents))
								$sRelEvents = '<div>'.$sRelEvents .'</div>'."\n";
						}

						$sRelPlaces = '';
						$aRelPlaces = $oProgram->ListProgramPlacesAsArray($row->MainProgramID);
						if (is_array($aRelPlaces) && count($aRelPlaces)>0)
						{
							if (!empty($sRelPlaces)) $sRelPlaces .= ' - ';
							$aPlacesToShow = $oPlace->ListByIDsAsArray($aRelPlaces);
							foreach($aPlacesToShow as $key=>$val)
							{
								$aPlacePages = $oPlace->ListPlacePagesAsArray($key);
								$sRelPlaces .= '<a href="'.setPage($aPlacePages[0], 0, $key).'">'.stripComments($val).'</a> ';
							}
							if (!empty($sRelPlaces))
								$sRelPlaces = '<div class="guest">'.$sRelPlaces .'</div>'."\n";
						}

						$sPremiere = '';
						if (!empty($row->PremiereTypeID))
						{
							$aPremiereTypes = getLabel('aPremiereTypes');
							switch($row->PremiereTypeID)
							{
								case 1: // prepremiere
									$sPremiere .= '<a href="#">'.drawImage('img/ico_prepremiere.png', 0, 0, $aPremiereTypes[$row->PremiereTypeID]).'</a>';
									break;
								case 2: // premiere
								case 4: // official premiere
									$sPremiere .= '<a href="#">'.drawImage('img/ico_premiere.png', 0, 0, $aPremiereTypes[$row->PremiereTypeID]).'</a>';
									break;
								case 3: // exclusive
								case 5: // special screening
									$sPremiere .= '<a href="#">'.drawImage('img/ico_exclusive.png', 0, 0, $aPremiereTypes[$row->PremiereTypeID]).'</a>';
									break;

							}
						}

						$sFestival = '';
						if (!empty($row->FestivalID))
						{
							$rFestival = $oFestival->GetByID($row->FestivalID);
							$aFestivalPages = $oFestival->ListFestivalPagesAsArray($row->FestivalID);
							if (is_array($aFestivalPages) && count ($aFestivalPages) > 0 && $rFestival->IsHidden == B_FALSE)
								$sFestival .= ' &middot; <a href="'.setPage($aFestivalPages[0], 0, $row->FestivalID).'">'.stripComments($rFestival->Title).'</a> ';
							else
								$sFestival .= ' &middot; <a href="#'.$row->FestivalID.'">'.stripComments($rFestival->Title).'</a> ';
						}

						$sLetter = $row->Letter;//strLCase($row->Letter);
						$nLetterKey = $aAlphabetLettersKeys[$sLetter];
						if (!in_array($nLetterKey, $aLetterKeys))
						{
							$aLetterKeys[] = $nLetterKey;
							$sListContent .= '<a name="alpha_'.$nLetterKey.'"></a>'."\n";
						}

						if ($i > 0)
							$sListContent .= '</div></div>'."\n";
						$i++;

						$nEventPageToGo = $page;
						if ($rCurrentPage->TemplateFile == 'event_search')
						{
							$nEventPageToGo = $nRootPage; // section
							$aRelProgramPages = $oProgram->ListProgramPagesAsArray($row->MainProgramID);
							if (is_array($aRelProgramPages) && count($aRelProgramPages) > 0)
								$nEventPageToGo = $aRelProgramPages[0];
						}

						$sListContent .= '<div class="box">
						'.IIF(!empty($sPremiere), '<div class="premierebox">'.$sPremiere.'</div>', '').'
						<h3><a href="'.setPage($nEventPageToGo, 0, $row->EventID).'" title="'.stripComments(htmlspecialchars(strip_tags($row->Title))).'">'.stripComments($row->Title).'</a></h3>
						'.IIF(!empty($row->OriginalTitle), '<div>'.$row->OriginalTitle.'</div>', '');
						if (!empty($sMainImageFile) && is_file($sMainImageFile))
						{
							$sListContent .= '<div class="event_guide'.IIF($bMidImage, '_mid', '').' more">'.IIF(!empty($sCategory), $sCategory, '').
							IIF(!empty($row->Features), '<br />'.$row->Features, '').
							IIF(!empty($row->Comment), '<br />'.strShorten($row->Comment, TEXT_LEN_PUBLIC), '').'</div>
							<a class="more" href="'.setPage($nEventPageToGo, 0, $row->EventID).'" title="'.stripComments(htmlspecialchars(strip_tags($row->Title))).'">'.drawImage($sMainImageFile, 0, 0, stripComments($row->Title)).'</a>';
						}
						else
						{
							$sListContent .= '<div class="event_guide_wide more">'.IIF(!empty($sCategory), $sCategory, '').
							IIF(!empty($row->Features), '<br />'.$row->Features, '').
							IIF(!empty($row->Comment), '<br />'.strShorten($row->Comment, TEXT_LEN_PUBLIC), '').'</div>';
						}
						$sListContent .= '<div class="more">'.IIF(!empty($row->Lead), $row->Lead, strShorten($row->Description, TEXT_LEN_PUBLIC)).'</div>
						<!--br class="clear" /-->
						<div class="date more">'.$sRelEvents.$sRelPlaces.$sFestival.'</div>
						<div>'."\n";
					}

					if (!in_array($row->PlaceID, $aPlaces))
					{
						$aPlaces[] = $row->PlaceID;
						$rPlace = $oPlace->GetByID($row->PlaceID);
						$aPlacePages = $oPlace->ListPlacePagesAsArray($row->PlaceID);
						if (is_array($aPlacePages) && count($aPlacePages)>0)
							$sListContent .= '<a href="'.setPage($aPlacePages[0], 0, $row->PlaceID).'">'.$rPlace->Title.'</a> &nbsp; ';
						else
							$sListContent .= '<a href="#'.$row->PlaceID.'">'.$rPlace->Title.'</a> &nbsp; ';

						$dSelTime = getQueryArg('sel_time', '');
						if (!empty($dSelStartDate) && !empty($dSelDate) && !empty($dSelTime))
						{
							$sDateTimes = '';
							$dSelEndDate = $dSelStartDate;
							$rsProgramDateTimes = $oProgramDateTime->ListAll($row->MainProgramID, $dSelStartDate, $dSelEndDate, $dSelStartTime, $dSelEndTime);
							while($rDateTime = mysql_fetch_object($rsProgramDateTimes))
							{
								if (!empty($sDateTimes)) $sDateTimes .= ', ';
								$sDateTimes .= formatTime($rDateTime->ProgramTime);
							}
							$sListContent .= $sDateTimes.'<br />';
						}
					}
				}
			}
			if(!empty($sListContent))
				$sListContent .= '</div></div>'."\n";
		}
		else
		{
			$sListContent .= '<div>'.getLabel('strNoRecords').'</div>'."\n";
		}
	}
	echo $sListContent;
}
*/
?>