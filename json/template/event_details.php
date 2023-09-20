<?php
if (!$bInSite) die();
global $aEntityTypes;
//=========================================================
if (isset($item) && !empty($item))
{
	$rEvent = $oEvent->GetByID($item);
	if ($rEvent)
	{
		$nEntityType = $aEntityTypes[ENT_EVENT];

		$sItemTitle = $rEvent->Title;
		$result['name'] = stripComments($sItemTitle);
		$result['id'] = $rEvent->EventID;
		
		// =========================== ADDRESS ===========================
		$sAddress = '';
		$rsAddress = $oAddress->ListAll($rEvent->EventID, $aEntityTypes[ENT_EVENT], 1); //$rEvent->EventTypeID
		if(mysql_num_rows($rsAddress))
		{
			while($rAddress = mysql_fetch_object($rsAddress))
			{
				$sAddress .= $rAddress->Street.'<br />';
			}
			$result['address'] = $sAddress;
		}
		// =========================== PHONE ===========================
		$sPhone = '';
		$rsPhone = $oPhone->ListAll($rEvent->EventID, $aEntityTypes[ENT_EVENT], array(1,2,3,6)); //$rEvent->EventTypeID
		if(mysql_num_rows($rsPhone))
		{
			$aPhoneTypes = getLabel('aPhoneTypes');
			while($rPhone = mysql_fetch_object($rsPhone))
			{
				$sPhone .= $aPhoneTypes[$rPhone->PhoneTypeID].': '.$rPhone->Area.$rPhone->Phone.$rPhone->Ext.'<br />';
			}
			$result['phone'] = $sPhone;
		}
		// =========================== EMAIL ===========================
		$sEmail = '';
		$rsEmail = $oEmail->ListAll($rEvent->EventID, $aEntityTypes[ENT_EVENT], 1); //$rEvent->EventTypeID
		if(mysql_num_rows($rsEmail))
		{
			while($rEmail = mysql_fetch_object($rsEmail))
			{
				$result['email'] = $rEmail->Email;
			}
		}
		// =========================== IMAGES ===========================
		$sGalleryToDisplay = '';
		if($_REQUEST['w'] >= 450) {
			$sMainImageFile = UPLOAD_DIR.IMG_EVENT.$rEvent->EventID.'.'.EXT_IMG;
		}
		else {
			$sMainImageFile = UPLOAD_DIR.IMG_EVENT_MID.$rEvent->EventID.'.'.EXT_IMG; //IMG_EVENT_MID
		}

		if (!is_file('../'.$sMainImageFile))
		{
			$sMainImageFile = '';//UPLOAD_DIR.IMG_EMPTY.'.'.EXT_PNG;
		}
		$sThumbnails = '';

		$nNrThumbs = 0;
		$sPanoramaFile = '';

		$rsAttachment = $oAttachment->ListAll($rEvent->EventID, $aEntityTypes[ENT_EVENT], array(1,2, 4, 7)); //$rEvent->EventTypeID
		while($rAttachment = mysql_fetch_object($rsAttachment))
		{
			if(!isset($result['images']))	$result['images'] = array();
			if ($rAttachment->AttachmentTypeID == 1 && empty($sMainImageFile))
				$sMainImageFile = FILEBANK_DIR.$rAttachment->AttachmentID.'.'.$rAttachment->Extension;
			elseif ($rAttachment->AttachmentTypeID == 7){
				$sTrailerFile = UPLOAD_DIR.FILE_TRAILER.$rAttachment->AttachmentID.'.'.$rAttachment->Extension;
			}
			elseif ($rAttachment->AttachmentTypeID == 4){
				$gal = UPLOAD_DIR.IMG_GALLERY.$rAttachment->AttachmentID.'.'.$rAttachment->Extension;
				array_push($result['images'], $gal);
			}
			elseif ($nNrThumbs < 3)
			{
				$sThumbImageFile = FILEBANK_DIR.$rAttachment->AttachmentID.'.'.$rAttachment->Extension;
				array_push($result['images'], $sThumbImageFile);
				$nNrThumbs ++;
			}
		}

		if (is_file('../'.$sMainImageFile))
		{
			$result['image'] = $sMainImageFile;
		}
		// =========================== LABELS ===========================
		$sLabels = '';
		$aOrigLanguages = $oLabel->ListAllAsArray(GRP_LANG);
		$aRelLabels = $oEvent->ListEventLabelsAsArray($rEvent->EventID, GRP_LANG);
		if (count($aRelLabels)>0)
		{
			$sNewLabels = '';
			foreach($aRelLabels as $key)
			{
				if (!empty($sNewLabels)) $sNewLabels .= ', ';
				$sNewLabels .= $aOrigLanguages[$key];
			}
			if (!empty($sNewLabels)) {
				$result['language'] = $sNewLabels;
			}
		}
		$aTranslations = $oLabel->ListAllAsArray(GRP_TRANS);
		$aRelLabels = $oEvent->ListEventLabelsAsArray($rEvent->EventID, GRP_TRANS);
		if (count($aRelLabels)>0)
		{
			$sNewLabels = '';
			foreach($aRelLabels as $key)
			{
				if (!empty($sNewLabels)) $sNewLabels .= ', ';
				$sNewLabels .= $aTranslations[$key];
			}
			if (!empty($sNewLabels)) {
				$result['translation'] = $sNewLabels;
			}
		}
		$aMusicStyle = $oLabel->ListAllAsArray(GRP_MUSIC);
		$aRelLabels = $oEvent->ListEventLabelsAsArray($rEvent->EventID, GRP_MUSIC);
		if (count($aRelLabels)>0)
		{
			$sNewLabels = '';
			foreach($aRelLabels as $key)
			{
				if (!empty($sNewLabels)) $sNewLabels .= ', ';
				$sNewLabels .= $aMusicStyle[$key];
			}
			if (!empty($sNewLabels)) {
				$result['music'] = $sNewLabels;
			}
		}
		$aMovieGenres = $oLabel->ListAllAsArray(GRP_MOVIE);
		$aRelLabels = $oEvent->ListEventLabelsAsArray($rEvent->EventID, GRP_MOVIE);
		if (count($aRelLabels)>0)
		{
			$sNewLabels = '';
			foreach($aRelLabels as $key)
			{
				if (!empty($sNewLabels)) $sNewLabels .= ', ';
				$sNewLabels .= $aMovieGenres[$key];
			}
			if (!empty($sNewLabels)) {
				$result['genre'] = $sNewLabels;
			}
		}
		$aPerformanceGenres = $oLabel->ListAllAsArray(GRP_PERF);
		$aRelLabels = $oEvent->ListEventLabelsAsArray($rEvent->EventID, GRP_PERF);
		if (count($aRelLabels)>0)
		{
			$sNewLabels = '';
			foreach($aRelLabels as $key)
			{
				if (!empty($sNewLabels)) $sNewLabels .= ', ';
				$sNewLabels .= $aPerformanceGenres[$key];
			}
			if (!empty($sNewLabels)) {
				$sLabels .= '<br />'.$sNewLabels."\n";//getLabel('strGenre').': '.
			}
		}
		$aArtistGenres = $oLabel->ListAllAsArray(GRP_ARTIST);
		$aRelLabels = $oEvent->ListEventLabelsAsArray($rEvent->EventID, GRP_ARTIST);
		if (count($aRelLabels)>0)
		{
			$sNewLabels = '';
			foreach($aRelLabels as $key)
			{
				if (!empty($sNewLabels)) $sNewLabels .= ', ';
				$sNewLabels .= $aArtistGenres[$key];
			}
			if (!empty($sNewLabels))
				$sLabels .= '';//'<br />'.getLabel('strGenre').': '.$sNewLabels."\n";
		}
		$aExhibitionGenres = $oLabel->ListAllAsArray(GRP_EXHIB);
		$aRelLabels = $oEvent->ListEventLabelsAsArray($rEvent->EventID, GRP_EXHIB);
		if (count($aRelLabels)>0)
		{
			$sNewLabels = '';
			foreach($aRelLabels as $key)
			{
				if (!empty($sNewLabels)) $sNewLabels .= ', ';
				$sNewLabels .= $aExhibitionGenres[$key];
			}
			if (!empty($sNewLabels))
				$sLabels .= '<br />'.getLabel('strGenre').': '.$sNewLabels."\n";
		}
		if (!empty($sLabels))
			$sLabels .= '<br />'."\n";
		// =========================== EVENT INFO ===========================
			if(is_file('../'.$sTrailerFile))
			{
				$result['trailer'] = $sTrailerFile;
			}
			if(!empty($rEvent->OriginalTitle))
			{
				$result['originalTitle'] = $rEvent->OriginalTitle;
			}

			if(!empty($rEvent->Features))
			{
				$result['features'] = $rEvent->Features;
			}

			if(!empty($rEvent->Comment))
			{
				$result['comment'] = $rEvent->Comment;
			}

			if(!empty($rEvent->Lead))
			{
				$result['lead'] = $rEvent->Lead;
			}

			if(!empty($rEvent->Description))
			{
				$result['description'] = stripHTMLComments($rEvent->Description);
			}


			// =========================== PROGRAM DATES ===========================
		if (isset($dSelStartDate) && !empty($dSelStartDate))
		{
			$dStartDate = $dSelStartDate;
		}
		else
		{
			$dToday = date(DEFAULT_DATE_DB_FORMAT);
			$dStartDate = $dToday;
		}
		// show yesterday events before 5 a.m.
		$dCurrentTime = date(DEFAULT_TIME_DISPLAY_FORMAT);
		if ($dCurrentTime <= TODAY_START)
			$dStartDate = increaseDate($dStartDate, -1);
		// default end date
		if ($rEvent->EventTypeID == 10) // film
		{
			//$dEndDate = $dToday;
			//if (empty($cat)) $cat = 1;
			$dEndDate = increaseDate($dStartDate, THIS_WEEK_DAYS-1);
			if (empty($cat)) $cat = 2;
		}
		else
		{
			//$dEndDate = increaseDate($dToday, THIS_WEEK_DAYS-1);
			//if (empty($cat)) $cat = 2;
			$dEndDate = increaseDate($dStartDate, 0, 1);
			if (empty($cat)) $cat = 3;
		}


		switch($cat)
		{
			case 1:
				// today
				$dEndDate = $dStartDate;
				break;
			case 2:
				// this week
				$dEndDate = increaseDate($dStartDate, THIS_WEEK_DAYS-1);
				break;
			case 3:
				// this month
				$dEndDate = increaseDate($dStartDate, 0, 1);
				break;
		}
		// =========================== PROGRAM BY PLACE ===========================
		$temp_places = array();
		$rsProgram = $oProgram->ListPlacesByEventID($rEvent->EventID, $city, $dStartDate, $dEndDate);
		if (mysql_num_rows($rsProgram) > 0)
		{
			$i = 0;

			$aPlaces = array();
			$aHalls = array();
			$aFestivals = array();
			$aDates = array();
			$aPremieres = array();
			$aRelEventIDs = array();

			$sDateInfo = '';
			$aPremiereTypes = getLabel('aPremiereTypes');
			while($row = mysql_fetch_object($rsProgram))
			{
				if (!in_array($row->PlaceID, $aPlaces))
				{
					$place = array();
					$place['id'] = $row->PlaceID;
					$place['name'] = stripComments($row->Title);
					//gps
					$place['lng'] = substr(str_pad(str_replace(".", "", $row->long), 8, '0', STR_PAD_RIGHT), 0, 8);
					$place['lat'] = substr(str_pad(str_replace(".", "", $row->lat), 8, '0', STR_PAD_RIGHT), 0, 8);

					$place['dates'] = array();
					if ($i > 0)
						$sDateInfo .= '</div>'."\n";
					$i++;
					$aPlaces[] = $row->PlaceID;

					$aPlacePages = $oPlace->ListPlacePagesAsArray($row->PlaceID);
					$sDateInfo .= '<div class="'.IIF($i%2==0, 'even', 'odd').'">
						<h5><a href="'.setPage($aPlacePages[0], 0, $row->PlaceID).'">'.stripComments($row->Title).'</a></h5>'."\n";

					// PROGRAM NOTE COMES HERE
					$rNote = $oProgramNote->GetByProgramID($row->MainProgramID);
					if ($rNote && !empty($rNote->Title))
						$sDateInfo .= $rNote->Title.'<br />'."\n";

					$aFestivals = array();
					$aPremieres = array();
					$aDates = array();
					$aHalls = array();
					$aRelEventIDs = array();
				}

				if (!empty($row->FestivalID))
				{
					if (!in_array($row->FestivalID, $aFestivals))
					{
						$aFestivals[] = $row->FestivalID;
						$rFestival = $oFestival->GetByID($row->FestivalID);
						$aRelPages = $oFestival->ListFestivalPagesAsArray($row->FestivalID);
						$sDateInfo .= '<div><a href="'.setPage($aRelPages[0], 0, $row->FestivalID).'">'.stripComments($rFestival->Title).'</a></div>'."\n";

						$aPremieres = array();
						$aDates = array();
						$aHalls = array();
						$aRelEventIDs = array();
					}
				}

				//$bShowLine = false;
				//$bShowLine = true;
				if (!empty($row->ProgramDate) && $row->ProgramDate != DEFAULT_DATE_DB_VALUE)
				{
					if (!in_array($row->ProgramDate, $aDates))
					{
						$sPremiere = '';
						if (!empty($row->PremiereTypeID))
						{
							if (!in_array($row->PremiereTypeID, $aPremieres))
							{
								switch($row->PremiereTypeID)
								{
									case 1: // prepremiere
										$sPremiere .= '<div class="prepremiere"><span>'.$aPremiereTypes[$row->PremiereTypeID].'</span></div>';
										break;
									case 2: // premiere
									case 4: // official premiere
										$sPremiere .= '<div class="premiere"><span>'.$aPremiereTypes[$row->PremiereTypeID].'</span></div>';
										break;
									case 3: // exclusive
									case 5: // special screening
										$sPremiere .= '<div class="exclusive"><span>'.$aPremiereTypes[$row->PremiereTypeID].'</span></div>';
										break;

								}
								$aPremieres[] = $row->PremiereTypeID;
							}
						}
						if (count($aDates) > 0)
							$sDateInfo .= '<br />';
						$sDateInfo .= $sPremiere;
						$aDates[] = $row->ProgramDate;
						//if ($dStartDate != $dEndDate)
							$sDateInfo .= formatDate($row->ProgramDate, FULL_DATE_DISPLAY_FORMAT);
							$date = $row->ProgramDate;
							//array_push($place['dates'], array('date' => formatDate($row->ProgramDate, FULL_DATE_DISPLAY_FORMAT)));
						$aHalls = array();
						$aRelEventIDs = array();
					}
				}
				elseif (!empty($row->StartDate) && $row->StartDate != DEFAULT_DATE_DB_VALUE)
				{
					$sDateInfo .= formatDate($row->StartDate, FULL_DATE_DISPLAY_FORMAT).' - '.formatDate($row->EndDate, FULL_DATE_DISPLAY_FORMAT);
					$date = '';
					array_push($place['dates'], formatDate($row->StartDate, FULL_DATE_DISPLAY_FORMAT).' - '.formatDate($row->EndDate, FULL_DATE_DISPLAY_FORMAT));
					$aDates[] = $row->StartDate;
					//$aDates[] = $row->EndDate;
					$aHalls = array();
					$aRelEventIDs = array();
				}
				if (!empty($row->PlaceHallID) && !in_array($row->PlaceHallID, $aHalls))
				{
					$aHalls[] = $row->PlaceHallID;
					$aRelEventIDs = array();
					$rPlaceHall = $oPlaceHall->GetByID($row->PlaceHallID);
					$sDateInfo .= ', '.$rPlaceHall->Title;
				}
				if (is_null($row->RelEventID))
				{
					$sDateInfo .= IIF(!empty($row->ProgramTime) && $row->ProgramTime != DEFAULT_TIME_DB_VALUE, ', '.formatTime($row->ProgramTime), '');
					if($date) array_push($place['dates'], $date.' '.IIF(!empty($row->ProgramTime) && $row->ProgramTime != DEFAULT_TIME_DB_VALUE, $row->ProgramTime, ''));

					$sDateInfo .= IIF(!empty($row->Price), ', '.$row->Price.getLabel('strLv'), '');

					$aRelPlaces = $oProgram->ListProgramPlacesAsArray($row->MainProgramID);
					if (is_array($aRelPlaces) && count($aRelPlaces)>0)
					{
						//$sDateInfo .= ' - ';
						$aPlacesToShow = $oPlace->ListByIDsAsArray($aRelPlaces);
						foreach($aPlacesToShow as $key=>$val)
						{
							$aPlacePages = $oPlace->ListPlacePagesAsArray($key);
							$sDateInfo .= '<div class="guest"><a href="'.setPage($aPlacePages[0], 0, $key).'">'.stripComments($val).'</a></div>'."\n";
						}
					}
				}
				elseif (!is_null($row->RelEventID) && !in_array($row->RelEventID, $aRelEventIDs))
				{
					$sDateInfo .= IIF(!empty($row->ProgramTime) && $row->ProgramTime != DEFAULT_TIME_DB_VALUE, ', '.formatTime($row->ProgramTime), '');
					if($date) array_push($place['dates'], $date.' '.IIF(!empty($row->ProgramTime) && $row->ProgramTime != DEFAULT_TIME_DB_VALUE, $row->ProgramTime, ''));

					$sDateInfo .= IIF(!empty($row->Price), ', '.$row->Price.getLabel('strLv'), '');

					$aRelPlaces = $oProgram->ListProgramPlacesAsArray($row->MainProgramID);
					if (is_array($aRelPlaces) && count($aRelPlaces)>0)
					{
						//$sDateInfo .= ' - ';
						$aPlacesToShow = $oPlace->ListByIDsAsArray($aRelPlaces);
						foreach($aPlacesToShow as $key=>$val)
						{
							$aPlacePages = $oPlace->ListPlacePagesAsArray($key);
							$sDateInfo .= '<div class="guest"><a href="'.setPage($aPlacePages[0], 0, $key).'">'.stripComments($val).'</a></div>'."\n";
						}
					}
					$aRelEvents = $oProgram->ListProgramEventsAsArray($row->MainProgramID);
					//print_r($aRelEvents);
					if (is_array($aRelEvents) && count($aRelEvents)>0)
					{
						//$sDateInfo .= ' - ';
						$aEventsToShow = $oEvent->ListByIDsAsArray($aRelEvents);
						foreach($aEventsToShow as $key=>$val)
						{
							if ($key != $item)
							{
								$aRelEventIDs[] = $key;
								$sDateInfo .= '&middot; <a href="'.setPage($nRootPage, 0, $key).'">'.stripComments($val).'</a> ';
							}
							else
							{
								$aRelEventIDs[] = $row->EventID;
								$rRelEvent = $oEvent->GetByID($row->EventID);
								$sDateInfo .= '&middot; <a href="'.setPage($nRootPage, 0, $rRelEvent->EventID).'">'.stripComments($rRelEvent->Title).'</a> ';
							}

						}
					}
				}
				//array_push($place['dates'], $dates);
				$temp_places[$row->PlaceID] = $place;
			}
			if(!empty($temp_places)) {
				$result['places'] = array();
				$result['places'] = array_values($temp_places);
			}
			//echo IIF(!empty($sDateInfo), $sProgramNav.$sDateInfo.'</div>'."\n", '');
		}
	}
	else
		$item = 0;
}
?>