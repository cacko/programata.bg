<?php
if (!$bInSite) die();
global $aEntityTypes;
//=========================================================
if (isset($item) && !empty($item))
{
	$rPlace = $GLOBALS['oPlace']->GetByID($item);

	//id
	$result['id'] = $item;

	//title
	$result['name'] = stripComments($rPlace->Title);

	//address
	$rsAddress = $GLOBALS['oAddress']->ListAll($rPlace->PlaceID, $aEntityTypes[ENT_PLACE], 1);
	if(mysql_num_rows($rsAddress))
	{
		$aAllCities = getLabel('aCitiesAll');
		while($rAddress = mysql_fetch_object($rsAddress))
		{
			$result['address'] .= $aAllCities[$rAddress->CityID].', ';
			$result['address'] .= $rAddress->Street;
		}
	}

	//working time
	if($rPlace->WorkingTime) $result['working_time'] = $rPlace->WorkingTime;

	//phone
	$rsPhone = $GLOBALS['oPhone']->ListAll($rPlace->PlaceID, $aEntityTypes[ENT_PLACE], array(1,2,3,6));
	if(mysql_num_rows($rsPhone))
	{
		$aPhoneTypes = getLabel('aPhoneTypes');
		while($rPhone = mysql_fetch_object($rsPhone))
		{
			$result['phone'] = str_replace(" ", "", $rPhone->Area.$rPhone->Phone.$rPhone->Ext);
		}
	}

	//email
//	$sEmail = '';
//	$rsEmail = $oEmail->ListAll($rPlace->PlaceID, $aEntityTypes[ENT_PLACE], 1); //$rPlace->PlaceTypeID
//	if(mysql_num_rows($rsEmail))
//	{
//		while($rEmail = mysql_fetch_object($rsEmail))
//		{
//			$result['email'] = $rEmail->Email;
//		}
//	}

	//image
	if($_REQUEST['w'] >= 450) {
		$sMainImageFile = UPLOAD_DIR.IMG_PLACE.$row->EventID.'.'.EXT_IMG;
	}
	else {
		$sMainImageFile = UPLOAD_DIR.IMG_PLACE_MID.$rPlace->PlaceID.'.'.EXT_IMG;
	}
	if (is_file('../'.$sMainImageFile))
	{
		$result['image'] = $sMainImageFile;
	}

	$rsAttachment = $oAttachment->ListAll($rPlace->PlaceID, $aEntityTypes[ENT_PLACE], array(4)); ////$rPlace->PlaceTypeID

	while($rAttachment = mysql_fetch_object($rsAttachment))
	{
		if(!isset($result['images'])) $result['images'] = array();
		$gal = UPLOAD_DIR.IMG_GALLERY.$rAttachment->AttachmentID.'.'.$rAttachment->Extension;
		array_push($result['images'], $gal);
	}

	$result['description'] = stripHTMLComments(str_replace("<br />", " ", $rPlace->Description));

	//gps
	$result['lng'] = substr(str_pad(str_replace(".", "", $rPlace->long), 8, '0', STR_PAD_RIGHT), 0, 8);
	$result['lat'] = substr(str_pad(str_replace(".", "", $rPlace->lat), 8, '0', STR_PAD_RIGHT), 0, 8);

	// =========================== PLACE GUIDE ===========================
	$sPlaceGuide = '';
	$sVacation = '';
	$nLastID = $oPlace->GetMaxID();
	$extras = array();
	if ($nLastID - $rPlace->PlaceID <= NEWEST_PLACES)
	{
		array_push($extras, "cg_new");
	}
	$rPlaceGuide = $oPlaceGuide->GetByPlaceID($rPlace->PlaceID);
	if ($rPlaceGuide)
	{
		if (!empty($rPlaceGuide->HasEntranceFee))
			$result['entranceFee'] = $rPlaceGuide->EntranceFeeText;
		if (!empty($rPlaceGuide->HasDJ))
			array_push($extras, "cg_dj");
		if (!empty($rPlaceGuide->HasLiveMusic))
			array_push($extras, "cg_live_music");
		if (!empty($rPlaceGuide->HasKaraoke))
			array_push($extras, "cg_karaoke");
		if (!empty($rPlaceGuide->HasDelivery))
			array_push($extras, "cg_delivery");
		if (!empty($rPlaceGuide->HasFaceControl))
			array_push($extras, "cg_face_control");
		if (!empty($rPlaceGuide->HasCuisine))
			array_push($extras, "cg_cuisine");
		if (!empty($rPlaceGuide->HasTerrace))
			array_push($extras, "cg_terrace");
		if (!empty($rPlaceGuide->HasClima))
			array_push($extras, "cg_clima");
		if (!empty($rPlaceGuide->HasParking))
			array_push($extras, "cg_parking");
		if (!empty($rPlaceGuide->HasWardrobe))
			array_push($extras, "cg_wardrobe");
		if (!empty($rPlaceGuide->HasCardPayment))
			array_push($extras, "cg_card");
		if (!empty($rPlaceGuide->HasWifi))
			array_push($extras, "cg_wifi");
		if (!empty($rPlaceGuide->VacationStartDate) || !empty($rPlaceGuide->VacationEndDate))
		{
			$dToday = date(DEFAULT_DATE_DB_FORMAT);
			if ($rPlaceGuide->VacationStartDate <= $dToday && $rPlaceGuide->VacationEndDate >= $dToday)
			{
				$result("vacationDate", formatDate($rPlaceGuide->VacationStartDate).' - '.formatDate($rPlaceGuide->VacationEndDate));
			}
		}
	}

	if(!empty($extras)) $result['extras'] = $extras;

	// =========================== LABELS ===========================
	$sLabels = '';
	$aCuisine = $oLabel->ListAllAsArray(GRP_CUISINE);
	$aRelLabels = $oPlace->ListPlaceLabelsAsArray($rPlace->PlaceID, GRP_CUISINE);

	if (count($aRelLabels)>0)
	{
		$sNewLabels = '';
		foreach($aRelLabels as $key)
		{
			if (!empty($sNewLabels)) $sNewLabels .= ', ';
			$criusine .= $aCuisine[$key];
		}
		if (!empty($sNewLabels))
			$result['criusine'] = $criusine;
	}
	$aMusicStyle = $oLabel->ListAllAsArray(GRP_BGNDMUSIC);
	$aRelLabels = $oPlace->ListPlaceLabelsAsArray($rPlace->PlaceID, GRP_BGNDMUSIC);
	if (count($aRelLabels)>0)
	{
		$sNewLabels = '';
		foreach($aRelLabels as $key)
		{
			if (!empty($sNewLabels)) $sNewLabels .= ', ';
			$musicStyle .= $aMusicStyle[$key];
		}
		if (!empty($sNewLabels))
			$result['musicStyle'] = $musicStyle;
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
		//if ($rPlace->PlaceTypeID == 1)
		if ($nRootPage == 26 || $nRootPage == 21 || $page == 55) // kina, zavedenia & party time
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
		// =========================== PROGRAM BY EVENT ===========================
		//if ($rPlace->PlaceTypeID == 1) // cinema daily program
		if ($nRootPage != 26 && $page != 55) // bez zavedenia & party time
		{
			$rsProgram = $oProgram->ListEventsByPlaceID($rPlace->PlaceID, $dStartDate, $dEndDate);
			$i = 0;
			$aEvents = array();
			$aHalls = array();
			$aDates = array();
			$aPremieres = array();
			$sDateInfo = '';
			$aPremiereTypes = getLabel('aPremiereTypes');
			$temp_dares=array();
			while($row = mysql_fetch_object($rsProgram))
			{
				if (!in_array($row->EventID, $aEvents))
				{
					$event = array();
					$event['id'] = $row->EventID;
					$event['name'] = stripComments($row->Title);
					$event['dates'] = array();

					if ($i > 0)
						$sDateInfo .= '</div>'."\n";
					$i++;
					$aEvents[] = $row->EventID;

					$aRelPages = $oProgram->ListProgramPagesAsArray($row->MainProgramID);
					//print_r($aRelPages);
					$sDateInfo .= '<div class="'.IIF($i%2==0, 'even', 'odd').'">
						<h5><a href="'.setPage($aRelPages[0], 0, $row->EventID).'">'.stripComments($row->Title).'</a></h5>'."\n";

					// PROGRAM NOTE COMES HERE
					$rNote = $oProgramNote->GetByProgramID($row->MainProgramID);
					if ($rNote && !empty($rNote->Title))
						$sDateInfo .= $rNote->Title.'<br />'."\n";

					$aDates = array();
					$aHalls = array();
					$aPremieres = array();

					$aRelPlaces = $oProgram->ListProgramPlacesAsArray($row->MainProgramID);
					if (is_array($aRelPlaces) && count($aRelPlaces)>0)
					{
						//$sDateInfo .= ' - ';
						$aPlacesToShow = $oPlace->ListByIDsAsArray($aRelPlaces);
						foreach($aPlacesToShow as $key=>$val)
						{
							if ($key != $item)
							{
								$aPlacePages = $oPlace->ListPlacePagesAsArray($key);
								$sDateInfo .= '<div class="guest"><a href="'.setPage($aPlacePages[0], 0, $key).'">'.stripComments($val).'</a></div>'."\n";
							}
							else
							{
								$rRelPlace = $oPlace->GetByID($row->PlaceID);
								$aPlacePages = $oPlace->ListPlacePagesAsArray($row->PlaceID);
								$sDateInfo .= '<div><a href="'.setPage($aPlacePages[0], 0, $rRelPlace->PlaceID).'">'.stripComments($rRelPlace->Title).'</a></div>'."\n";
							}
						}
					}
				}
				if (!empty($row->FestivalID))
				{
					$rFestival = $oFestival->GetByID($row->FestivalID);
					$aRelPages = $oFestival->ListFestivalPagesAsArray($row->FestivalID);
					$sDateInfo .= '<div><a href="'.setPage($aRelPages[0], 0, $row->FestivalID).'">'.stripComments($rFestival->Title).'</a></div>'."\n";
				}
				$aRelEvents = $oProgram->ListProgramEventsAsArray($row->MainProgramID);
				if (is_array($aRelEvents) && count($aRelEvents)>0)
				{
					//$sDateInfo .= ' - ';
					$aEventsToShow = $oEvent->ListByIDsAsArray($aRelEvents);
					foreach($aEventsToShow as $key=>$val)
					{
						$sDateInfo .= ' &middot; <a href="'.setPage($nRootPage, 0, $key).'">'.stripComments($val).'</a>'."\n";
					}
				}
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
										$sPremiere .= '<div class="prepremiere"><span>'.$aPremiereTypes[$row->PremiereTypeID].'</span></div>'."\n";
										break;
									case 2: // premiere
									case 4: // official premiere
										$sPremiere .= '<div class="premiere"><span>'.$aPremiereTypes[$row->PremiereTypeID].'</span></div>'."\n";
										break;
									case 3: // exclusive
									case 5: // special screening
										$sPremiere .= '<div class="exclusive"><span>'.$aPremiereTypes[$row->PremiereTypeID].'</span></div>'."\n";
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
						$aHalls = array();
					}
				}
				elseif (!empty($row->StartDate) && $row->StartDate != DEFAULT_DATE_DB_VALUE)
				{
					$sDateInfo .= formatDate($row->StartDate, FULL_DATE_DISPLAY_FORMAT).' - '.formatDate($row->EndDate, FULL_DATE_DISPLAY_FORMAT);
					array_push($event['dates'], formatDate($row->StartDate, FULL_DATE_DISPLAY_FORMAT).' - '.formatDate($row->EndDate, FULL_DATE_DISPLAY_FORMAT));
				}
				if (!empty($row->PlaceHallID) && !in_array($row->PlaceHallID, $aHalls))
				{
					$aHalls[] = $row->PlaceHallID;
					$rPlaceHall = $oPlaceHall->GetByID($row->PlaceHallID);
					$sDateInfo .= ', '.$rPlaceHall->Title;
				}
				$sDateInfo .= IIF(!empty($row->ProgramTime) && $row->ProgramTime != DEFAULT_TIME_DB_VALUE, ', '.formatTime($row->ProgramTime), '');
				array_push($event['dates'], $date.' '.$row->ProgramTime);
				//$sDateInfo .= IIF(!empty($row->Price), ' ('.formatPrice($row->Price).getLabel('strLv').')', '');
				$sDateInfo .= IIF(!empty($row->Price), ', '.$row->Price.getLabel('strLv'), '');
				$temp_dates[$row->EventID] = $event;
			}
			if(!isset($result['events']) && !empty($temp_dates)) $result['events'] = array();
			if(!empty($temp_dates))	$result['events'] = array_values($temp_dates);
			//echo IIF(!empty($sDateInfo), $sProgramNav.$sDateInfo.'</div>'."\n", '');
		}
		// =========================== PROGRAM BY DATE ===========================
		else
		{
			$rsProgram = $oProgram->ListAllByDate($rPlace->PlaceID, null, null, null, $dStartDate, $dEndDate);
			$aDates = array();
			$i = 0;
			$sDateInfo = '';
			while($row = mysql_fetch_object($rsProgram))
			{
				$event = array();
				if (!empty($row->ProgramDate) && $row->ProgramDate != DEFAULT_DATE_DB_VALUE)
				{
					if (!in_array($row->ProgramDate, $aDates))
					{
						if ($i > 0)
							$sDateInfo .= '</div>'."\n";
						$i++;
						$aDates[] = $row->ProgramDate;
						$day = $row->ProgramDate;
						$sDateInfo .= '<div class="'.IIF($i%2==0, 'even', 'odd').'">
							<h5>'.formatDate($row->ProgramDate, FULL_DATE_DISPLAY_FORMAT).'</h5>'."\n";
						if(!isset($event['dates']))	$event['dates'] = array();
						array_push($event['dates'], $row->ProgramDate.' '.$row->ProgramTime);
					}
				}
				elseif (!empty($row->StartDate) && $row->StartDate != DEFAULT_DATE_DB_VALUE)
				{
					if ($i > 0)
						$sDateInfo .= '</div>'."\n";
					$i++;
					$sDateInfo .= '<div class="'.IIF($i%2==0, 'even', 'odd').'">
						<h5>'.formatDate($row->StartDate).' - '.formatDate($row->EndDate).'</h5>'."\n";
					if(!isset($event['dates']))	$event['dates'] = array();
						array_push($event['dates'], formatDate($row->StartDate).' - '.formatDate($row->EndDate));
				}
				$sDateInfo .= IIF(!empty($row->ProgramTime) && $row->ProgramTime != DEFAULT_TIME_DB_VALUE, ' '.formatTime($row->ProgramTime), '');
				if (!empty($row->PlaceHallID))
				{
					$rPlaceHall = $oPlaceHall->GetByID($row->PlaceHallID);
					$sDateInfo .= ' ('.$rPlaceHall->Title.')';
				}
				if (!empty($row->EventID))
				{
					$rMainEvent = $oEvent->GetByID($row->EventID);
					$aRelPages = $oProgram->ListProgramPagesAsArray($row->MainProgramID);
					$sDateInfo .= ' - <a href="'.setPage($aRelPages[0], 0, $rMainEvent->EventID).'">'.stripComments($rMainEvent->Title).'</a>'.
						IIF(!empty($row->PremiereTypeID), ' - '.$aPremiereTypes[$row->PremiereTypeID], '');
						$event['id'] = $rMainEvent->EventID;
						$event['name'] = $rMainEvent->Title;

				}
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
				if (is_array($aRelEvents) && count($aRelEvents)>0)
				{
					//$sDateInfo .= ' - ';
					$aEventsToShow = $oEvent->ListByIDsAsArray($aRelEvents);
					foreach($aEventsToShow as $key=>$val)
					{
						$sDateInfo .= ' &middot; <a href="'.setPage($nRootPage, 0, $key).'">'.stripComments($val).'</a>'."\n";
					}
				}
				//$sDateInfo .= IIF(!empty($row->Price), ' ('.formatPrice($row->Price).getLabel('strLv').')', '');
				$sDateInfo .= IIF(!empty($row->Price), ' '.$row->Price.getLabel('strLv'), '');
				$sDateInfo .= '<br />'."\n";

				if(!empty($event)){
					if(!isset($result['events']))  $result['events'] = array();
					array_push($result['events'], $event);
					//$result['events'] = $event;
				}
			}
			//echo IIF(!empty($sDateInfo), $sProgramNav.$sDateInfo.'</div>'."\n", '');
		}
}
?>