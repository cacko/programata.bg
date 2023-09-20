<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{ 
	$rPlace = $oPlace->GetByID($item);
	if($rPlace)
	{
						$set_fb_like_to_bottom = false; 
		$nEntityType = $aEntityTypes[ENT_PLACE];
		include('template/comment_save.php');

		$sItemTitle = $rPlace->Title;
		$oPlace->TrackView($item);
		// =========================== ADDRESS ===========================
		$sAddress = '';
		$rsAddress = $oAddress->ListAll($rPlace->PlaceID, $aEntityTypes[ENT_PLACE], 1); //$rPlace->PlaceTypeID
		if(mysql_num_rows($rsAddress))
		{
			$aAllCities = getLabel('aCitiesAll');
			while($rAddress = mysql_fetch_object($rsAddress))
			{
				$sAddress .= $aAllCities[$rAddress->CityID].', ';
				$sAddress .= $rAddress->Street.'<br />';
			}
		}
		// =========================== PHONE ===========================
		$sPhone = '';
		$rsPhone = $oPhone->ListAll($rPlace->PlaceID, $aEntityTypes[ENT_PLACE], array(1,2,3,6)); //$rPlace->PlaceTypeID
		if(mysql_num_rows($rsPhone))
		{
			$aPhoneTypes = getLabel('aPhoneTypes');
			while($rPhone = mysql_fetch_object($rsPhone))
			{
				$sPhone .= $aPhoneTypes[$rPhone->PhoneTypeID].': '.$rPhone->Area.' '.$rPhone->Phone.' '.$rPhone->Ext.'<br />';
			}
		}
		// =========================== EMAIL ===========================
		$sEmail = '';
		$rsEmail = $oEmail->ListAll($rPlace->PlaceID, $aEntityTypes[ENT_PLACE], 1); //$rPlace->PlaceTypeID
		if(mysql_num_rows($rsEmail))
		{
			while($rEmail = mysql_fetch_object($rsEmail))
			{
				$sEmail .= getLabel('strEmail').': <a href="mailto:'.$rEmail->Email.'">'.$rEmail->Email.'</a><br />';
			}
		}
		// =========================== LINKS ===========================
		$sLink = '';
		$rsLink = $oLink->ListAll($rPlace->PlaceID, $aEntityTypes[ENT_PLACE], array(1,2,3,0)); //$rPlace->PlaceTypeID
		if(mysql_num_rows($rsLink))
		{
			$aLinkTypes = getLabel('aLinkTypes');
			while($rLink = mysql_fetch_object($rsLink))
			{
				switch($rLink->LinkTypeID)
				{
					case 2: // eventim
						$sLink .= '<div class="eventim"><a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$aLinkTypes[$rLink->LinkTypeID].'</a></div>';
						break;
					case 3: // map
						//$sLink .= '<div class="map"><a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$aLinkTypes[$rLink->LinkTypeID].'</a></div>';
						break;
					default:
						$sLink .= '<div class="link"><strong>'.getLabel('strUrl').'</strong>: <a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$rLink->Title.'</a></div>';
						break;
				}
			}
		}
		// =========================== COMMENTS ===========================
		$nComments = $oComment->GetCountByEntity($rPlace->PlaceID, $aEntityTypes[ENT_PLACE]);
		// =========================== PLACE GUIDE ===========================
		$sPlaceGuide = '';
		$sVacation = '';
		$nLastID = $oPlace->GetMaxID();
		if ($nLastID - $rPlace->PlaceID <= NEWEST_PLACES)
		{
			$sPlaceGuide .= '<a href="#" style="float: left; width: 30px; height: 30px;">'.drawImage('img/cg_new.png', 0, 0, getLabel('strNew')).'</a>';
		}
		$rPlaceGuide = $oPlaceGuide->GetByPlaceID($rPlace->PlaceID);
		if ($rPlaceGuide)
		{
			/*if (!empty($rPlaceGuide->Category))
				$sPlaceGuide .= '<a href="#">'.drawImage('img/cg_category.png', 0, 0, getLabel('strCategory').': '.$rPlaceGuide->Category).'</a>';*/
			if (!empty($rPlaceGuide->HasEntranceFee))
				$sPlaceGuide .= '<a href="#" style="float: left; width: 30px; height: 30px;">'.drawImage('img/cg_entrance.png', 0, 0, getLabel('strEntranceFee').': '.$rPlaceGuide->EntranceFeeText).'</a>';
			if (!empty($rPlaceGuide->HasDJ))
				$sPlaceGuide .= '<a href="#" style="float: left; width: 30px; height: 30px;">'.drawImage('img/cg_dj.png', 0, 0, getLabel('strDJ')).'</a>';
			if (!empty($rPlaceGuide->HasLiveMusic))
				$sPlaceGuide .= '<a href="#" style="float: left; width: 30px; height: 30px;">'.drawImage('img/cg_live_music.png', 0, 0, getLabel('strLiveMusic')).'</a>';
			if (!empty($rPlaceGuide->HasKaraoke))
				$sPlaceGuide .= '<a href="#" style="float: left; width: 30px; height: 30px;">'.drawImage('img/cg_karaoke.png', 0, 0, getLabel('strKaraoke')).'</a>';
			if (!empty($rPlaceGuide->HasDelivery))
				$sPlaceGuide .= '<a href="#" style="float: left; width: 30px; height: 30px;">'.drawImage('img/cg_delivery.png', 0, 0, getLabel('strDelivery')).'</a>';
			/*if (!empty($rPlaceGuide->HasBgndMusic))
				$sPlaceGuide .= '<a href="#">'.drawImage('img/cg_bgnd_music.png', 0, 0, getLabel('strBgndMusic')).'</a>';*/
			if (!empty($rPlaceGuide->HasFaceControl))
				$sPlaceGuide .= '<a href="#" style="float: left; width: 30px; height: 30px;">'.drawImage('img/cg_face_control.png', 0, 0, getLabel('strFaceControl')).'</a>';
			/*if (!empty($rPlaceGuide->HasCuisine))
				$sPlaceGuide .= '<a href="#">'.drawImage('img/cg_cuisine.png', 0, 0, getLabel('strCuisine').': '.$rPlaceGuide->CuisineText).'</a>';*/
			if (!empty($rPlaceGuide->HasCuisine))
				$sPlaceGuide .= '<a href="#" style="float: left; width: 30px; height: 30px;">'.drawImage('img/cg_cuisine.png', 0, 0, getLabel('strCuisine')).'</a>';
			if (!empty($rPlaceGuide->HasTerrace))
				$sPlaceGuide .= '<a href="#" style="float: left; width: 30px; height: 30px;">'.drawImage('img/cg_terrace.png', 0, 0, getLabel('strTerrace')).'</a>';
			/*if (!empty($rPlaceGuide->HasSmokingArea))
				$sPlaceGuide .= '<a href="#">'.drawImage('img/cg_smoking.png', 0, 0, getLabel('strSmokingArea')).'</a>';*/
			if (!empty($rPlaceGuide->HasClima))
				$sPlaceGuide .= '<a href="#" style="float: left; width: 30px; height: 30px;">'.drawImage('img/cg_clima.png', 0, 0, getLabel('strClima')).'</a>';
			if (!empty($rPlaceGuide->HasParking))
				$sPlaceGuide .= '<a href="#" style="float: left; width: 30px; height: 30px;">'.drawImage('img/cg_parking.png', 0, 0, getLabel('strParking')).'</a>';
			if (!empty($rPlaceGuide->HasWardrobe))
				$sPlaceGuide .= '<a href="#" style="float: left; width: 30px; height: 30px;">'.drawImage('img/cg_wardrobe.png', 0, 0, getLabel('strWardrobe')).'</a>';
			if (!empty($rPlaceGuide->HasCardPayment))
				$sPlaceGuide .= '<a href="#" style="float: left; width: 30px; height: 30px;">'.drawImage('img/cg_card.png', 0, 0, getLabel('strCardPayment')).'</a>';
			/*if (!empty($rPlaceGuide->EntertainmentText))
				$sPlaceGuide .= '<a href="#">'.drawImage('img/cg_entertainment.png', 0, 0, getLabel('strEntertainment').$rPlaceGuide->EntertainmentText).'</a>';*/
			if (!empty($rPlaceGuide->HasWifi))
				$sPlaceGuide .= '<a href="#" style="float: left; width: 30px; height: 30px;">'.drawImage('img/cg_wifi.png', 0, 0, getLabel('strWifi')).'</a>';
			if (!empty($rPlaceGuide->VacationStartDate) || !empty($rPlaceGuide->VacationEndDate))
			{
				$dToday = date(DEFAULT_DATE_DB_FORMAT);
				if ($rPlaceGuide->VacationStartDate <= $dToday && $rPlaceGuide->VacationEndDate >= $dToday)
				{
					$sPlaceGuide .= '<a href="#" style="float: left; width: 30px; height: 30px;">'.drawImage('img/cg_vacation.png', 0, 0, getLabel('strVacation')).'</a>';
					$sVacation .= getLabel('strVacation').': '.formatDate($rPlaceGuide->VacationStartDate).' - '.formatDate($rPlaceGuide->VacationEndDate).'<br />';
				}
			}
		}
		// =========================== IMAGES ===========================
		$sGalleryToDisplay = '';
		$sMainImageFile = UPLOAD_DIR.IMG_PLACE.$rPlace->PlaceID.'.'.EXT_IMG;
		$sThumbnails = '';
		$nNrThumbs = 0;
		$sPanoramaFile = '';
		if (!is_file($sMainImageFile))
		{
			$sMainImageFile = '';//UPLOAD_DIR.IMG_EMPTY.'.'.EXT_PNG;
		}
		$rsAttachment = $oAttachment->ListAll($rPlace->PlaceID, $aEntityTypes[ENT_PLACE], array(1,2,5)); ////$rPlace->PlaceTypeID
		while($rAttachment = mysql_fetch_object($rsAttachment))
		{
			if ($rAttachment->AttachmentTypeID == 5)
				$sPanoramaFile = UPLOAD_DIR.FILE_PANORAMA.$rAttachment->AttachmentID.'.'.$rAttachment->Extension;
			elseif ($rAttachment->AttachmentTypeID == 1 && empty($sMainImageFile)) {
				$sMainImageFile = FILEBANK_DIR.IMG_SMALL.$rAttachment->AttachmentID.'.'.$rAttachment->Extension;
			}
			elseif ($nNrThumbs < 3)
			{
				$sThumbImageFile = FILEBANK_DIR.IMG_SMALL.$rAttachment->AttachmentID.'.'.$rAttachment->Extension;
				$sThumbnails .= drawImage($sThumbImageFile);
				$nNrThumbs ++;
			}
		}

		if (!empty($sPanoramaFile))
		{
			$panorama_source .= '<h4>'.getLabel('strPanorama').'</h4>
						<a name="panorama"></a><br />'.drawMovie($sPanoramaFile, W_IMG_GALLERY, H_IMG_GALLERY).'
						<br class="clear" />'.getLabel('strQuicktimePlugin')."\n";
		}
		
		$sGallery = showGallery($rPlace->PlaceID, ENT_PLACE, false, false, true);
		
		if (!empty($sGallery))
//			$sGalleryToDisplay .= '<h4>'.getLabel('strGallery').'</h4>
//						<a name="galleria"></a>'.$sGallery."\n";
			if(empty($sGalleryToDisplay)){
				$sGalleryToDisplay = '<div class="gallery"><ul>'.$sGallery .'</ul></div>';
			}else{
				$sGalleryToDisplay = '<div class="gallery"><ul>'.$sGallery .'</ul></div>'. $sGalleryToDisplay .'<br /><br /><br />';	
			}
			
		elseif(!empty($sThumbnails))
//			$sGalleryToDisplay .= '<h4>'.getLabel('strGallery').'</h4>
//						<a name="galleria"></a>
//						<div class="thumbnail">'.$sThumbnails.'</div>'."\n";

		// =========================== RATING ===========================
		$sRating = $sRatingForm = '';
		if ($nRootPage == 26)
		{
			include('template/rate_save.php');
			$fRating = $oRate->GetRating($rPlace->PlaceID, $aEntityTypes[ENT_PLACE]);
			//$sRating .= '<h6>'.getLabel('strRating').': '.$fRating.'</h6>';
			$sRating .= drawImage('img/rating_'.ceil($fRating).'.png', 0, 0, getLabel('strRating').': '.$fRating);
			$nUserID = $oSession->GetValue(SS_USER_ID);
			if (!empty($nUserID))
			{
				$bUserRated = $oRate->IsRated($rPlace->PlaceID, $aEntityTypes[ENT_PLACE]);
				if (!$bUserRated)
				{
					$sRating .= '<div class="vote"><a href="#">'.getLabel('strVote').'</a></div>';
					$sRatingForm = displayRatingForm();
				}
			}
		}
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
				$sNewLabels .= $aCuisine[$key];
			}
			if (!empty($sNewLabels))
				$sLabels .= '<br />'.getLabel('strCuisine').': '.$sNewLabels."\n";
		}
		/*$aAtmosphere = $oLabel->ListAllAsArray(GRP_ATMOS);
		$aRelLabels = $oPlace->ListPlaceLabelsAsArray($rPlace->PlaceID, GRP_ATMOS);
		if (count($aRelLabels)>0)
		{
			$sNewLabels = '';
			foreach($aRelLabels as $key)
			{
				if (!empty($sNewLabels)) $sNewLabels .= ', ';
				$sNewLabels .= $aAtmosphere[$key];
			}
			if (!empty($sNewLabels))
				$sLabels .= '<br />'.getLabel('strAtmosphere').': '.$sNewLabels."\n";
		}
		$aPriceCategory = $oLabel->ListAllAsArray(GRP_PRICE);
		$aRelLabels = $oPlace->ListPlaceLabelsAsArray($rPlace->PlaceID, GRP_PRICE);
		if (count($aRelLabels)>0)
		{
			$sNewLabels = '';
			foreach($aRelLabels as $key)
			{
				if (!empty($sNewLabels)) $sNewLabels .= ', ';
				$sNewLabels .= $aPriceCategory[$key];
			}
			if (!empty($sNewLabels))
				$sLabels .= '<br />'.getLabel('strPriceCategory').': '.$sNewLabels."\n";
		}*/
		$aMusicStyle = $oLabel->ListAllAsArray(GRP_BGNDMUSIC);
		$aRelLabels = $oPlace->ListPlaceLabelsAsArray($rPlace->PlaceID, GRP_BGNDMUSIC);
		if (count($aRelLabels)>0)
		{
			$sNewLabels = '';
			foreach($aRelLabels as $key)
			{
				if (!empty($sNewLabels)) $sNewLabels .= ', ';
				$sNewLabels .= $aMusicStyle[$key];
			}
			if (!empty($sNewLabels))
				$sLabels .= '<br />'.getLabel('strMusicStyle').': '.$sNewLabels."\n";
		}
		if (!empty($sLabels))
			$sLabels .= '<br />'."\n";
		// =========================== PLACE INFO ===========================
		
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

		// if ($nRootPage == 26 || $nRootPage == 21 || $page == 55) // kina, zavedenia & party time
		// {
		// 	//$dEndDate = $dToday;
		// 	//if (empty($cat)) $cat = 1;
		// 	$dEndDate = increaseDate($dStartDate, THIS_WEEK_DAYS-1);
		// 	if (empty($cat)) $cat = 2;
		// }
		// else
		// {
			//$dEndDate = increaseDate($dToday, THIS_WEEK_DAYS-1);
			//if (empty($cat)) $cat = 2;
			$dEndDate = increaseDate($dStartDate, 0, 1);
			if (empty($cat)) $cat = 3;
		// }

//		$sProgramNav = '<h4>'.getLabel('strProgram').'</h4>
//		<a name="program"></a>
//		<!--ul class="inline_nav"-->'."\n";
		$sProgramNav = '
					<div class="program">
                    	<h5>'.getLabel('strProgram').'</h5>
	                        <ul>';
		//if ($rPlace->PlaceTypeID == 1)
		/*if ($nRootPage == 26 || $nRootPage == 21 || $page == 55) // kina, zavedenia & party time
		{
			$sProgramNav .= '
			<li class="next'.IIF($cat==2, ' on', '').'"><a href="'.setPage($page, 2, $item).'#program">'.getLabel('strThisWeek').'</a></li>
			<li class="this'.IIF($cat==1, ' on', '').'"><a href="'.setPage($page, 1, $item).'#program">'.getLabel('strToday').'</a></li>'."\n";
		}
		else
		{
			$sProgramNav .= '
			<li class="next'.IIF($cat==3, ' on', '').'"><a href="'.setPage($page, 3, $item).'#program">'.getLabel('strNextMonth').'</a></li>
			<li class="this'.IIF($cat==2, ' on', '').'"><a href="'.setPage($page, 2, $item).'#program">'.getLabel('strThisWeek').'</a></li>'."\n";
		}*/
		//$sProgramNav .= '</ul>'."\n";

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
			while($row = mysql_fetch_object($rsProgram))
			{
				if (!in_array($row->EventID, $aEvents))
				{
					if ($i > 0)
						$sDateInfo .= '</li>'."\n";
					$i++;
					$aEvents[] = $row->EventID;
					$aRelPages = $oProgram->ListProgramPagesAsArray($row->MainProgramID);
					//print_r($aRelPages);
//					$sDateInfo .= '<div class="'.IIF($i%2==0, 'even', 'odd').'">
//						<h5><a href="'.setPage($aRelPages[0], 0, $row->EventID).'">'.stripComments($row->Title).'</a></h5>'."\n";

					$sDateInfo .= '<li><h6><a href="'.setPage($aRelPages[0], 0, $row->EventID).'" title="'.stripComments($row->Title).'">'.stripComments($row->Title).'</a></h6>';
 
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
						/*foreach($aRelPlaces as $key=>$val)
							if ($val != $item)
							{
								$rRelPlace = $oPlace->GetByID($val);
								$aPlacePages = $oPlace->ListPlacePagesAsArray($val);
								$sDateInfo .= '<div class="guest"><a href="'.setPage($aPlacePages[0], 0, $rRelPlace->PlaceID).'">'.stripComments($rRelPlace->Title).'</a></div>'."\n";
							}
							else
							{
								$rRelPlace = $oPlace->GetByID($row->PlaceID);
								$aPlacePages = $oPlace->ListPlacePagesAsArray($row->PlaceID);
								$sDateInfo .= '<div><a href="'.setPage($aPlacePages[0], 0, $rRelPlace->PlaceID).'">'.stripComments($rRelPlace->Title).'</a></div>'."\n";
							}
						}*/
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
					/*foreach($aRelEvents as $key=>$val)
					{
						$rRelEvent = $oEvent->GetByID($val);
						//$aEventPages = $oProgram->ListProgramPagesAsArray($row->MainProgramID);
						//$sDateInfo .= '<a href="'.setPage($aEventPages[0], 0, $rRelEvent->EventID).'">'.stripComments($rRelEvent->Title).'</a> ';
						$sDateInfo .= ' &middot; <a href="'.setPage($nRootPage, 0, $rRelEvent->EventID).'">'.stripComments($rRelEvent->Title).'</a>'."\n";
					}*/
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
//										$sPremiere .= '<div class="prepremiere"><span>'.$aPremiereTypes[$row->PremiereTypeID].'</span></div>'."\n";
										$sPremiere .= '<span>'.$aPremiereTypes[$row->PremiereTypeID].'</span>'."\n";
										break;
									case 2: // premiere
									case 4: // official premiere
//										$sPremiere .= '<div class="premiere"><span>'.$aPremiereTypes[$row->PremiereTypeID].'</span></div>'."\n";
										$sPremiere .= '<span>'.$aPremiereTypes[$row->PremiereTypeID].'</span>'."\n";
										break;
									case 3: // exclusive
									case 5: // special screening
//										$sPremiere .= '<div class="exclusive"><span>'.$aPremiereTypes[$row->PremiereTypeID].'</span></div>'."\n";
										$sPremiere .= '<span>'.$aPremiereTypes[$row->PremiereTypeID].'</span>'."\n";
										break;
								}
								$aPremieres[] = $row->PremiereTypeID;
							}
						}
						if (count($aDates) > 0)
							$sDateInfo .= '<p/>';
						$sDateInfo .= $sPremiere;
						$aDates[] = $row->ProgramDate;
						//if ($dStartDate != $dEndDate)
							$sDateInfo .= '<p>'. formatDate($row->ProgramDate, FULL_DATE_DISPLAY_FORMAT);
						$aHalls = array();
					}
				}
				elseif (!empty($row->StartDate) && $row->StartDate != DEFAULT_DATE_DB_VALUE)
				{
					$sDateInfo .= formatDate($row->StartDate, FULL_DATE_DISPLAY_FORMAT).' - '.formatDate($row->EndDate, FULL_DATE_DISPLAY_FORMAT);
				}
				if (!empty($row->PlaceHallID) && !in_array($row->PlaceHallID, $aHalls))
				{
					$aHalls[] = $row->PlaceHallID;
					$rPlaceHall = $oPlaceHall->GetByID($row->PlaceHallID);
					$sDateInfo .= ', '.$rPlaceHall->Title;
				}
				$sDateInfo .= IIF(!empty($row->ProgramTime) && $row->ProgramTime != DEFAULT_TIME_DB_VALUE, ', '.formatTime($row->ProgramTime), '');
				//$sDateInfo .= IIF(!empty($row->Price), ' ('.formatPrice($row->Price).getLabel('strLv').')', '');
				$sDateInfo .= IIF(!empty($row->Price), ', '.$row->Price.getLabel('strLv'), '');
			}

			// PROGRAMATA
			$programa_container = IIF(!empty($sDateInfo), $sProgramNav.$sDateInfo.'</ul></div>'."\n", '');
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
				if (!empty($row->ProgramDate) && $row->ProgramDate != DEFAULT_DATE_DB_VALUE)
				{
					if (!in_array($row->ProgramDate, $aDates))
					{
						if ($i > 0)
							$sDateInfo .= '</li>'."\n";
						$i++;
						$aDates[] = $row->ProgramDate;
//						$sDateInfo .= '<div class="'.IIF($i%2==0, 'even', 'odd').'">
//							<h5>'.formatDate($row->ProgramDate, FULL_DATE_DISPLAY_FORMAT).'</h5>'."\n";
						$sDateInfo .= '<li><h6><a href="javascript:void(0);" title="'.formatDate($row->ProgramDate, FULL_DATE_DISPLAY_FORMAT).'">'.formatDate($row->ProgramDate, FULL_DATE_DISPLAY_FORMAT).'</a></h6>';
					}
				}
				elseif (!empty($row->StartDate) && $row->StartDate != DEFAULT_DATE_DB_VALUE)
				{
					if ($i > 0)
						$sDateInfo .= '</li>'."\n";
					$i++;
//					$sDateInfo .= '<div class="'.IIF($i%2==0, 'even', 'odd').'">
//						<h5>'.formatDate($row->StartDate).' - '.formatDate($row->EndDate).'</h5>'."\n";
					$sDateInfo .= '<li><h6><a href="javascript:void(0);" title="'.formatDate($row->StartDate).' - '.formatDate($row->EndDate).'">'.formatDate($row->StartDate).' - '.formatDate($row->EndDate).'</a></h6>';					
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

					// PROGRAM NOTE COMES HERE
					/*$rNote = $oProgramNote->GetByProgramID($row->MainProgramID);
					if ($rNote && !empty($rNote->Title))
						$sDateInfo .= $rNote->Title.'<br />'."\n";*/
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
					/*foreach($aRelPlaces as $key=>$val)
					{
						$rRelPlace = $oPlace->GetByID($val);
						$aPlacePages = $oPlace->ListPlacePagesAsArray($val);
						$sDateInfo .= '<div class="guest"><a href="'.setPage($aPlacePages[0], 0, $rRelPlace->PlaceID).'">'.stripComments($rRelPlace->Title).'</a></div>'."\n";
					}*/
				}
				$aRelEvents = $oProgram->ListProgramEventsAsArray($row->MainProgramID);
				if (is_array($aRelEvents) && count($aRelEvents)>0)
				{
					//$sDateInfo .= ' - ';
					$aEventsToShow = $oEvent->ListByIDsAsArray($aRelEvents);
					foreach($aEventsToShow as $key=>$val)
					{
						$sDateInfo .= ', <a href="'.setPage($nRootPage, 0, $key).'">'.stripComments($val).'</a>'."\n";
					}
					/*foreach($aRelEvents as $key=>$val)
					{
						$rRelEvent = $oEvent->GetByID($val);
						//$aEventPages = $oProgram->ListProgramPagesAsArray($row->MainProgramID);
						//$sDateInfo .= '<a href="'.setPage($aEventPages[0], 0, $rRelEvent->EventID).'">'.stripComments($rRelEvent->Title).'</a> ';
						$sDateInfo .= '<a href="'.setPage($nRootPage, 0, $rRelEvent->EventID).'">'.stripComments($rRelEvent->Title).'</a> '."\n";
					}*/
				}
				//$sDateInfo .= IIF(!empty($row->Price), ' ('.formatPrice($row->Price).getLabel('strLv').')', '');
				$sDateInfo .= IIF(!empty($row->Price), ' '.$row->Price.getLabel('strLv'), '');
				$sDateInfo .= '<br />'."\n";
			}
			$programa_container = IIF(!empty($sDateInfo), $sProgramNav.$sDateInfo.'</div>'."\n", '');
		}

		if(!empty($programa_container)){
			$programa_container = preg_replace('/<h6>(.*)<\/h6><span>(.*)<\/span>/isU','<span>$2</span><h6>$1</h6>', $programa_container);
		}
?>

	<div id="container">
	<div id="article">
    	<div class="main-preview">
    		<?php
    			$draw_img = drawImage($sMainImageFile, 0, 0, stripComments($row->Title));
    			if(strlen($draw_img) > 1){
    				echo '<a href="'. $sMainImageFile .'" id="wallpepar">'. $draw_img .'</a>';
    			}else{
    				echo IIF(!empty($sAddress), '<iframe style="border: 0px;" src="http://www.bgmaps.com/templates/programata/poi/'.$rPlace->PlaceID.'" width="100%" height="300">
		  				<p>Your browser does not support iframes.</p>
					</iframe>', '');
    			}
    		
    		?>
<!--        	<a href="javascript:void(0);" id="wallpepar"><?=drawImage($sMainImageFile, 0, 0, stripComments($row->Title))?></a>-->
            <h1><?=stripComments(htmlspecialchars(strip_tags($rPlace->Title)))?></h1>
            <?=IIF(!empty($sPlaceGuide), '<h2>'.$sPlaceGuide.'<div style="clear:both;"></div></h2>', '')?>
            <br />
        </div>
		
        <?php
        
		if(!empty($sGalleryToDisplay)){
			echo substr($sGalleryToDisplay,0, 25) .'<li><a href="'. $sMainImageFile .'" title="'. $row->Title .'">'. drawImage($sMainImageFile, 138, 93, $row->Title) .'</a></li>'. substr($sGalleryToDisplay,25);	
		}
		
		if(isset($panorama_source)){
			
			echo '<div id="snapshot"><h4>'.getLabel('strPanorama') .'</h4><br /><img src="img/play_large.png" id="play_button"/>'. drawImage($sMainImageFile, 0, 0) .'<br /><br /></div>';
			
			echo '<div id="hidden_panorama">'. $panorama_source .'<br /><br /></div>';
		}
       	?>
        
       	<?php
       		if(!empty($sAddress) || !empty($rPlace->WorkingTime) || !empty($rPlaceGuide) || !empty($sVacation) || !empty($sPhone) || !empty($sEmail) || !empty($sLabels)){
       			echo '<div class="text summary">'.
					IIF(!empty($sAddress), getLabel('strAddress').': '.$sAddress, '').
					IIF(!empty($rPlace->WorkingTime), getLabel('strWorkingTime').': '.$rPlace->WorkingTime.'<br />', '').
					IIF($rPlaceGuide, getLabel('strNrSeats').': '.$rPlaceGuide->NrSeats.'<br />', '').
					$sVacation.$sPhone.$sEmail.$sLabels.       			
       			 '</div>';
				echo '<div class="fb-like" data-send="true" data-width="450" data-show-faces="true"></div>';

       		}
		?>
			<script type="text/javascript">
					$(".main-preview #wallpepar").lightBox();
					$(".gallery a").lightBox();
			</script>             
        
		<?php
			if(strlen($draw_img) > 1){
				echo IIF(!empty($sAddress), '<h4><a name="map"></a></h4>
				<br /><iframe style="border: 0px;" src="http://www.bgmaps.com/templates/programata/poi/'.$rPlace->PlaceID.'" width="100%" height="300">
	  				<p>Your browser does not support iframes.</p>
				</iframe>
				<br /><br />', '');
			}
		?> 	       
         
        <?=IIF(!empty($rPlace->Description), '<div class="text">'.$rPlace->Description.'</div>', '')?>
        
		<?=$sLink?>
		
		<?php
			echo $programa_container;
		?>
		
		
	<!--	<div class="box detail">
			<?=IIF(!empty($sPlaceGuide), '<div class="place_guide"><br />'.$sPlaceGuide.'</div>', '')?>
			<?=drawImage($sMainImageFile, 0, 0, stripComments($row->Title))?>
			<?=IIF(!empty($sRating), '<div class="rating">'.$sRating.'</div>', '')?>
			<?=IIF(!empty($sRatingForm), $sRatingForm, '')?>
			<div class="address"><?=IIF(!empty($sAddress), getLabel('strAddress').': '.$sAddress, '').
				IIF(!empty($rPlace->WorkingTime), getLabel('strWorkingTime').': '.$rPlace->WorkingTime.'<br />', '').
				IIF($rPlaceGuide, getLabel('strNrSeats').': '.$rPlaceGuide->NrSeats.'<br />', '').
				$sVacation.$sPhone.$sEmail.$sLink.$sLabels?>
			</div>-->

			<!--ul class="inline_nav">
				<li class="program"><a href="#program"><?=getLabel('strProgram')?></a></li>
				<li class="comment"><a href="#comment_list"><?=getLabel('strComments')?></a> (<?=$nComments?>)</li>
				<li class="vote"><a href="#vote"><?=getLabel('strVote')?></a></li>
				<? if (!empty($sPanoramaFile)) { ?>
				<li class="panorama"><a href="#panorama"><?=getLabel('strPanorama')?></a></li>
				<? } elseif (!empty($sGalleryToDisplay)) {?>
				<li class="gallery"><a href="#galleria"><?=getLabel('strGallery')?></a></li>
				<? }?>
				<li class="friend"><a href="#friend"><a href="mailto:?body=<?='http://'.SITE_URL.'/'.setPage($page, $cat, $item)?>"><?=getLabel('strTellFriend')?></a></li>
			</ul-->
	
			<? include('template/comment_list.php'); ?>
		</div>
	</div>
<?
		// add to calendar
		if (false && !empty($nUserID) && !empty($sDateInfo)) //
		{
			include('template/user_calendar_save.php');
			echo '<div class="calendar_add"><a href="#">'.getLabel('strAddToCalendar').'</a></div>'."\n";
			echo displayCalendarForm($rPlace->PlaceID, $aDates[0], $aDates[count($aDates)-1]);
		}
//		echo IIF(!empty($sGalleryToDisplay), $sGalleryToDisplay.'<br class="clear" />', '');

//		include('template/comment_list.php');
	}
	else
		$item = 0;
}
?>