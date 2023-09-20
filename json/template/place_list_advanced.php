<?php
if (!$bInSite) die();
//=========================================================
	// used in event_list_weekly_by_place.php - for clubs
	if (!isset($item) || empty($item))
	{
		$sListContent = '';
		if (is_array($_POST) && count($_POST)>0)
			echo $sSearchCriteria;
		if (!isset($rsPlace))
			$rsPlace = $oPlace->ListAll($page, $city, null, null, '', '', false, 0, $aContext);
		if (mysql_num_rows($rsPlace))
		{
			$aEvents = array();
			$aPlaces = array();
			//$aPages = $oPage->ListAllAsArraySimple();
			
			//$curRow = 0;
			//$nTotalRows = mysql_num_rows($rsPlace);
			//$nStartRow = $aContext[CUR_REC];
			//$nEndRow = $nStartRow + NUM_ROWS_PUBLIC;
			
			//if ($nTotalRows > NUM_ROWS_PUBLIC)
			//	echo '<div class="hr"><div class="paging">'.showPages($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_PUBLIC).'</div></div>';
			
			$aAlphabetGroups = getLabel('aAlphabetGroups');
			/*$nTotalRows = mysql_num_rows($rsPlace);
			if ($nTotalRows > NUM_ROWS_PUBLIC)
			{
				echo '<ul class="alpha">'."\n";
				foreach($aAlphabetGroups as $k=>$v)
				{
					echo '<li><a href="'.setPage($page, $cat).'#alpha_'.$k.'">'.$v.'</a></li>'."\n";
				}
				echo '</ul>'."\n";
			}*/
			
			//$aLetters = array();
			$aLetterKeys = array();
			$aAlphabetLettersKeys = getLabel('aAlphabetLettersKeys');
			$i = 0;
			while($row=mysql_fetch_object($rsPlace))
			{
				if (!empty($row->PlaceID))
				{
					if (!in_array($row->PlaceID, $aPlaces))
					{
						$aPlaces[] = $row->PlaceID;
						//$aEvents = array();
				//if ($curRow >= $nStartRow && $curRow < $nEndRow)
				//{
					//$sMainImageFile = '';
					/*$sMainImageFile = UPLOAD_DIR.IMG_PLACE_MID.$row->PlaceID.'.'.EXT_IMG;
					if (!is_file($sMainImageFile))
					{
						$sMainImageFile = '';//$sMainImageFile = UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
						$rsAttachment = $oAttachment->ListAll($row->PlaceID, $aEntityTypes[ENT_PLACE], 1); //$row->PlaceTypeID
						while($rAttachment = mysql_fetch_object($rsAttachment))
						{
							$sMainImageFile = FILEBANK_DIR.$rAttachment->AttachmentID.'.'.$rAttachment->Extension; //EXT_IMG;//.
						}
					}*/
					
					$sAddress = '';
					/*$rsAddress = $oAddress->ListAll($row->PlaceID, $aEntityTypes[ENT_PLACE], 1); //$row->PlaceTypeID
					if(mysql_num_rows($rsAddress))
					{
						while($rAddress = mysql_fetch_object($rsAddress))
						{
							$sAddress .= $rAddress->Street.'<br />';
						}
					}*/
					
					$sPhone = '';
					/*if ($page == 98) // delivery
					{
						$rsPhone = $oPhone->ListAll($row->PlaceID, $aEntityTypes[ENT_PLACE], 6); //$row->PlaceTypeID
						if(mysql_num_rows($rsPhone))
						{
							$aPhoneTypes = getLabel('aPhoneTypes');
							while($rPhone = mysql_fetch_object($rsPhone))
							{
								$sPhone .= $aPhoneTypes[$rPhone->PhoneTypeID].': '.$rPhone->Area.' '.$rPhone->Phone.' '.$rPhone->Ext.'<br />';
							}
						}
					}*/
					
					//$nComments = $oComment->GetCountByEntity($row->PlaceID, $aEntityTypes[ENT_PLACE]);
					
					
					
					$sPlaceGuide = '';
					/*$nLastID = $oPlace->GetMaxID();
					if ($nLastID - $row->PlaceID <= NEWEST_PLACES)
					{
						$sPlaceGuide .= '<a href="#">'.drawImage('img/cg_new.png', 0, 0, getLabel('strNew')).'</a>';
					}
					$rPlaceGuide = $oPlaceGuide->GetByPlaceID($row->PlaceID);
					if ($rPlaceGuide)
					{
						if (!empty($rPlaceGuide->HasWifi))
							$sPlaceGuide .= '<a href="#">'.drawImage('img/cg_wifi.png', 0, 0, getLabel('strWifi')).'</a>';
						if (!empty($rPlaceGuide->VacationStartDate) || !empty($rPlaceGuide->VacationEndDate))
						{
							$dToday = date(DEFAULT_DATE_DB_FORMAT);
							if ($rPlaceGuide->VacationStartDate <= $dToday && $rPlaceGuide->VacationEndDate >= $dToday)
								$sPlaceGuide .= '<a href="#">'.drawImage('img/cg_vacation.png', 0, 0, getLabel('strVacation')).'</a>';
						}
					}*/
					
					$sLetter = $row->Letter;//strLCase($row->Letter);
					//if (!in_array($sLetter, $aLetters))
					//{
						//$aLetters[] = $sLetter;
						$nLetterKey = $aAlphabetLettersKeys[$sLetter];
						//$sListContent .= $sLetter.$nLetterKey.'<br />';
						if (!in_array($nLetterKey, $aLetterKeys))
						{
							$aLetterKeys[] = $nLetterKey;
							$sListContent .= '<a name="alpha_'.$nLetterKey.'"></a>'."\n";
						}
					//}
					
					if ($i > 0)
						$sListContent .= '</div></div>'."\n";
					$i++;
					
					$nPlacePageToGo = 26; // zavedenia
					$aRelPlacePages = $oPlace->ListPlacePagesAsArray($row->PlaceID);
					if (is_array($aRelPlacePages) && count($aRelPlacePages) > 0)
						$nPlacePageToGo = $aRelPlacePages[0];
					
					//'.IIF(!empty($sMainImageFile), '<a href="'.setPage($page, 0, $row->EventID).'" title="'.stripComments(htmlspecialchars(strip_tags($row->Title))).'">'.drawImage($sMainImageFile, 0, 0, stripComments($row->Title)).'</a>', '').'
					$sListContent .= '<div class="box">
					'.IIF(!empty($sPlaceGuide), '<div class="place_guide">'.$sPlaceGuide.'</div>', '').'
					<h3><a href="'.setPage($nPlacePageToGo, 0, $row->PlaceID).'" title="'.stripComments(htmlspecialchars(strip_tags($row->Title))).'">'.stripComments($row->Title).'</a></h3>
					<!--div>'.IIF(!empty($sAddress), getLabel('strAddress').': '.$sAddress, '').
						IIF(!empty($row->WorkingTime), getLabel('strWorkingTime').': '.$row->WorkingTime, '').'<br />'.$sPhone.'</div-->
					<div>'."\n";
				//}
				//$curRow++;
									}
					
					//if (!empty($row->EventID) && !in_array($row->EventID, $aEvents))
					if (!empty($row->EventID))
					{
						//$aEvents[] = $row->EventID;
						$rEvent = $oEvent->GetByID($row->EventID);
						$aEventPages = $oEvent->ListEventPagesAsArray($row->EventID);
						if (is_array($aEventPages) && count($aEventPages)>0)
							$sListContent .= '<a href="'.setPage($aEventPages[0], 0, $row->EventID).'">'.$rEvent->Title.'</a>';
						else
							$sListContent .= '<a href="'.setPage($page, 0, $row->EventID).'">'.$rEvent->Title.'</a>';
						
					}
					
					$sRelEvents = '';
					$aRelEvents = $oProgram->ListProgramEventsAsArray($row->MainProgramID);
					if (is_array($aRelEvents) && count($aRelEvents)>0)
					{
						//if (!empty($sRelEvents)) $sRelEvents .= ' - ';
						$aEventsToShow = $oEvent->ListByIDsAsArray($aRelEvents);
						foreach($aEventsToShow as $key=>$val)
						{
							$sRelEvents .= ' &middot; <a href="'.setPage($nRootPage, 0, $key).'">'.stripComments($val).'</a>'."\n";
						}
						/*foreach($aRelEvents as $key=>$val)
						{
							$rRelEvent = $oEvent->GetByID($val);
							$sRelEvents .= ' &middot; <a href="'.setPage($nRootPage, 0, $val).'">'.stripComments($rRelEvent->Title).'</a>';
						}*/
						/*if (!empty($sRelEvents))
							$sRelEvents = '<div class="guest">'.$sRelEvents .'</div>'."\n";*/
					}
					
					$sRelPlaces = '';
					/*$aRelPlaces = $oProgram->ListProgramPlacesAsArray($row->MainProgramID);
					if (is_array($aRelPlaces) && count($aRelPlaces)>0)
					{
						if (!empty($sRelPlaces)) $sRelPlaces .= ' - ';
						foreach($aRelPlaces as $key=>$val)
						{
							$rRelPlace = $oPlace->GetByID($val);
							$aPlacePages = $oPlace->ListPlacePagesAsArray($val);
							$sRelPlaces .= '<a href="'.setPage($aPlacePages[0], 0, $rRelPlace->PlaceID).'">'.stripComments($rRelPlace->Title).'</a> ';
						}
						if (!empty($sRelPlaces))
							$sRelPlaces = '<div class="guest">'.$sRelPlaces .'</div>'."\n";
					}*/
					if (!empty($sRelEvents))
						$sListContent .= $sRelEvents;//.' - '; //.$sRelPlaces
				}
				/*$rsDates = $oProgramDateTime->ListAll($row->MainProgramID, $dStartDate, $dEndDate, $dStartTime, $dEndTime);
				if ($rsDates)
				{
					//$sListContent .= '<br />';
					$sDateInfo = '';
					while($rDate = mysql_fetch_object($rsDates))
					{
						if (!empty($sDateInfo))
							$sDateInfo .= ', ';
						$sDateInfo .= formatDate($rDate->ProgramDate, FULL_DATE_DISPLAY_FORMAT).' '.formatTime($rDate->ProgramTime).IIF(!empty($rDate->Price), ' '.getLabel('strPrice').' '.$rDate->Price, '');//.'<br />';
					}
					$sListContent .= $sDateInfo.'<br />';
				}*/
				$sListContent .= $sDateInfo.'<br />';
				//$sListContent .= formatDate($row->ProgramDate, FULL_DATE_DISPLAY_FORMAT).' '.formatTime($row->ProgramTime).IIF(!empty($row->Price), ' '.getLabel('strPrice').$row->Price, '').'<br />';
			}
			if(!empty($sListContent))
				$sListContent .= '</div></div>'."\n";
			//if ($nTotalRows > NUM_ROWS_PUBLIC)
			//	$sListContent .= '<div class="paging">'.showPages($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_PUBLIC).'</div>'."\n";
		}
		else
		{
			$sListContent .= '<div>'.getLabel('strNoRecords').'</div>'."\n";
		}
		echo $sListContent;
	}
	else
	{
		//include('template/place_details.php');
	}
?>