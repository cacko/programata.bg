<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	$rFestival = $oFestival->GetByID($item);
	if ($rFestival)
	{
		$result['id'] = $item;

		$nEntityType = $aEntityTypes[ENT_FESTIVAL];
		
		$sItemTitle = $rFestival->Title;
		$result['name'] = stripComments($rFestival->Title);
		
		// =========================== LINKS ===========================
		$sLink = '';
		$rsLink = $oLink->ListAll($rFestival->FestivalID, $aEntityTypes[ENT_FESTIVAL], array(1,2,3,0)); //$rPlace->PlaceTypeID
		if(mysql_num_rows($rsLink))
		{
			$aLinkTypes = getLabel('aLinkTypes');
			$sLink .= '<br />';
			while($rLink = mysql_fetch_object($rsLink))
			{
				if ($rLink->LinkTypeID == 2 || $rLink->LinkTypeID == 3)
					$sLink .= '<a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$aLinkTypes[$rLink->LinkTypeID].'</a><br />';
				else
					$sLink .= getLabel('strUrl').': <a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$rLink->Title.'</a><br />';
			}
		}
		// =========================== IMAGES ===========================
		$sMainImageFile = UPLOAD_DIR.IMG_FESTIVAL.$rFestival->FestivalID.'.'.EXT_IMG;
		if (is_file('../'.$sMainImageFile))
		{
			$result['image'] = $sMainImageFile;
		}
		$sGalleryToDisplay = '';

		$rsAttachment = $oAttachment->ListAll($rFestival->FestivalID, $aEntityTypes[ENT_FESTIVAL], array( 4));
		while($rAttachment = mysql_fetch_object($rsAttachment))
		{
			if(!isset($result['images']))	$result['images'] = array();
			$gal = UPLOAD_DIR.IMG_GALLERY.$rAttachment->AttachmentID.'.'.$rAttachment->Extension;
			array_push($result['images'], $gal);
		}

		$sGallery = showGallery($rFestival->FestivalID, ENT_FESTIVAL, false);
		if (!empty($sGallery))
			$sGalleryToDisplay .= '<h4>'.getLabel('strGallery').'</h4>
						<a name="galleria"></a>'.$sGallery."\n";
		
		// =========================== FESTIVAL INFO ===========================
		$sDate = '';
		if ($rFestival->StartDate === $rFestival->EndDate)
			$sDate .= formatDate($rFestival->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
		elseif (formatDate($rFestival->StartDate, FULL_MONTH_YEAR_DISPLAY_FORMAT) === formatDate($rFestival->EndDate, FULL_MONTH_YEAR_DISPLAY_FORMAT))
			$sDate .= formatDate($rFestival->StartDate, DAY_DISPLAY_FORMAT).' - '.formatDate($rFestival->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
		else
			$sDate .= formatDate($rFestival->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT).' - '.formatDate($rFestival->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
			
		$result['date'] = $sDate;
			
		$result['lead'] = $rFestival->Lead;
		$result['content'] = $rFestival->Content;

		$aFestivalCities = $oFestival->ListFestivalCitiesAsArray($rFestival->FestivalID);
		$result['cities'] = array();
		if (is_array($aFestivalCities) && count($aFestivalCities)>0)
		{
			foreach ($aFestivalCities as $k)
			{
				if ($k != $city)
				{
					array_push($result['cities'], $aCities[$k]);
				}
			}
		}
		$result['events'] = array();
		// =========================== PROGRAM DATES ===========================
		$dStartDate = $rFestival->StartDate;
		$dEndDate = $rFestival->EndDate;
		$dToday = date(DEFAULT_DATE_DB_FORMAT);
		if (empty($dStartDate))
		{
			$dStartDate = $dToday;
			// show yesterday events before 5 a.m.
			$dCurrentTime = date(DEFAULT_TIME_DISPLAY_FORMAT);
			if ($dCurrentTime <= TODAY_START)
				$dStartDate = increaseDate($dToday, -1);
		}
		if (empty($dEndDate))
		{
			// default end date
			$dEndDate = increaseDate($dToday, THIS_WEEK_DAYS-1);
		}
		
		//if (empty($cat)) $cat = 2;
		
		// =========================== PROGRAM BY DATE =========================== 
		$rsProgram = $oProgram->ListAllByDate(null, null, $rFestival->FestivalID, $city, $dStartDate, $dEndDate);
		if (mysql_num_rows($rsProgram) > 0)
		{
			$aDates = array();
			$i = 0;
			$sDateInfo = '';
			$aPremiereTypes = getLabel('aPremiereTypes');
			while($row = mysql_fetch_object($rsProgram))
			{
				$event = array();
				
				if (!empty($row->ProgramDate))
				{
					$event['date'] = $row->ProgramDate;
					
					if (!in_array($row->ProgramDate, $aDates))
					{
						if ($i > 0)
							$sDateInfo .= '</div>'."\n";
						$i++;
						$aDates[] = $row->ProgramDate;
						$sDateInfo .= '<div class="'.IIF($i%2==0, 'even', 'odd').'">
							<div>'.formatDate($row->ProgramDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</div>'."\n";
					}
				}
				elseif (!empty($row->StartDate))
				{
					$event['date'] = formatDate($row->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT).' - '.formatDate($row->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
					if ($i > 0)
						$sDateInfo .= '</div>'."\n";
					$i++;
					$sDateInfo .= '<div class="'.IIF($i%2==0, 'even', 'odd').'">
						<div>'.formatDate($row->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT).' - '.formatDate($row->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</div>'."\n";
				}
				if (!empty($row->EventID))
				{
					$sPremiere = '';
					if (!empty($row->PremiereTypeID))
					{
						$event['type'] = $aPremiereTypes[$row->PremiereTypeID];
						
						switch($row->PremiereTypeID)
						{
							case 1: // prepremiere
								$sPremiere .= '<div class="prepremiere"><a>'.$aPremiereTypes[$row->PremiereTypeID].'</a></div>'."\n";
								break;
							case 2: // premiere
							case 4: // official premiere
								$sPremiere .= '<div class="premiere"><a>'.$aPremiereTypes[$row->PremiereTypeID].'</a></div>'."\n";
								break;
							case 3: // exclusive
							case 5: // special screening
								$sPremiere .= '<div class="exclusive"><a>'.$aPremiereTypes[$row->PremiereTypeID].'</a></div>'."\n";
								break;
							
						}
					}
					$rMainEvent = $oEvent->GetByID($row->EventID);
					$aRelPages = $oProgram->ListProgramPagesAsArray($row->MainProgramID);
					$sDateInfo .= '<h5><a href="'.setPage($aRelPages[0], 0, $rMainEvent->EventID).'">'.stripComments($rMainEvent->Title).'</a></h5>'."\n".$sPremiere;
					$event['id'] = $rMainEvent->EventID;
					$event['name'] = stripComments($rMainEvent->Title);
					
					// PROGRAM NOTE COMES HERE
					//$rNote = $oProgramNote->GetByProgramID($row->MainProgramID);
					//if ($rNote && !empty($rNote->Title))
					//	$sDateInfo .= $rNote->Title.'<br />'."\n";
				}
				$aRelPlaces = $oProgram->ListProgramPlacesAsArray($row->MainProgramID);
				$place = array();
				$place['relPlaces'] = array();
				if (is_array($aRelPlaces) && count($aRelPlaces)>0)
				{
					//$sDateInfo .= ' - ';
					foreach($aRelPlaces as $key=>$val)
					{
						$relP = array();
						$rRelPlace = $oPlace->GetByID($val);
						$aPlacePages = $oPlace->ListPlacePagesAsArray($val);
						$sDateInfo .= '<div class="guest"><a href="'.setPage($aPlacePages[0], 0, $rRelPlace->PlaceID).'">'.stripComments($rRelPlace->Title).'</a></div>'."\n";
						$relP['id'] = $rRelPlace->PlaceID;
						$relP['name'] = stripComments($rRelPlace->Title);
						array_push($place['relPlaces'], $relP);
					}
				}
				$aRelEvents = $oProgram->ListProgramEventsAsArray($row->MainProgramID);
				$event['relEvents'] = array();
				if (is_array($aRelEvents) && count($aRelEvents)>0)
				{
					$sDateInfo .= ' - ';
					foreach($aRelEvents as $key=>$val)
					{
						$relE = array();
						$rRelEvent = $oEvent->GetByID($val);
						//$aEventPages = $oProgram->ListProgramPagesAsArray($row->MainProgramID);
						//$sDateInfo .= '<a href="'.setPage($aEventPages[0], 0, $rRelEvent->EventID).'">'.stripComments($rRelEvent->Title).'</a> ';
						$sDateInfo .= '<a href="'.setPage($nRootPage, 0, $rRelEvent->EventID).'">'.stripComments($rRelEvent->Title).'</a> ';
						$relE['id'] = $rRelEvent->EventID;
						$relE['name'] = stripComments($rRelEvent->Title);
						array_push($event['relEvents'], $relE);
					}
					$sDateInfo .= '<br />'."\n";
				}
				if (!empty($row->PlaceID))
				{
					$rPlace = $oPlace->GetByID($row->PlaceID);
					$aPlacePages = $oPlace->ListPlacePagesAsArray($row->PlaceID);
					$sDateInfo .= '<a href="'.setPage($aPlacePages[0], 0, $row->PlaceID).'">'.stripComments($rPlace->Title).'</a>';
					
					$place['id'] = $row->PlaceID;
					$place['name'] = stripComments($rPlace->Title);
				}
				if (!empty($row->PlaceHallID))
				{
					$rPlaceHall = $oPlaceHall->GetByID($row->PlaceHallID);
					$sDateInfo .= ', '.$rPlaceHall->Title.'';
					$place['hall'] = $rPlaceHall->Title;
				}
				$event['places'] = array();
				array_push($event['places'], $place);
				$sDateInfo .= IIF(!empty($row->ProgramTime), ', '.formatTime($row->ProgramTime), '');
				$event['date'] .= " ".$row->ProgramTime;
				//$sDateInfo .= IIF(!empty($row->Price), ' ('.formatPrice($row->Price).getLabel('strLv').')', '');
				$sDateInfo .= IIF(!empty($row->Price), ', '.$row->Price.getLabel('strLv'), '');
				$event['price'] = $row->Price;
				// NOTE COMES HERE
				//$sDateInfo .= '<br />'."\n";
				//$sDateInfo .= '</div>'."\n";
				
				array_push($result['events'], $event);
			}
			//echo IIF(!empty($sDateInfo), $sDateInfo.'</div>'."\n", '');
		}
		//echo IIF(!empty($sGalleryToDisplay), $sGalleryToDisplay.'<br class="clear" />', '');
	}
	else
		$item = 0;
}
?>