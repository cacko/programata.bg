<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	include('template/place_details.php');
}
if (!isset($item) || empty($item))
{
	$sListContent = '';
	$sSearchCriteria = '';
	$bShowFilter = false;
	$aPageCityFilters = $oPage->ListPageCityFiltersAsArray($page);
	if (is_array($aPageCityFilters) && count($aPageCityFilters) > 0
	    && in_array($city, $aPageCityFilters) && $rCurrentPage->TemplateFile != 'place_search')
		$bShowFilter = true;
	if (!isset($rsPlace))
	{
		if ($bShowFilter == false)
		{
			$rsPlace = $oPlace->ListAll($page, $city, null, null, '', '', false, false, 0, $aContext);
		}
		elseif(is_array($_POST) && count($_POST) > 0)
		{
			$sKeyword = htmlspecialchars(strip_tags(getPostedArg('keyword', '')));
			$nCuisine = htmlspecialchars(strip_tags(getPostedArg('cuisine', 0)));
			$nAtmosphere = htmlspecialchars(strip_tags(getPostedArg('atmosphere', 0)));
			$nPriceCategory = htmlspecialchars(strip_tags(getPostedArg('price_category', 0)));
			$nMusicStyle = htmlspecialchars(strip_tags(getPostedArg('music_style', 0)));
			$bIsNew=$bHasEntranceFee=$bHasCardPayment=$bHasFaceControl=$bHasParking=$bHasDJ=$bHasLiveMusic=$bHasKaraoke=$bHasBgndMusic=$bHasCuisine=$bHasTerrace=$bHasClima=$bHasWardrobe=$bHasWifi=$bHasDelivery = false;

			if (empty($sKeyword) && empty($nCuisine) && empty($nAtmosphere) && empty($nPriceCategory) && empty($nMusicStyle)) {} // do nothing
			else
				$rsPlace = $oPlace->ListAllAdvanced($page, $city, null, null, $sKeyword, '',
								    $nCuisine, $nAtmosphere, $nPriceCategory, $nMusicStyle, $bIsNew,
								    $bHasEntranceFee, $bHasCardPayment, $bHasFaceControl, $bHasParking,
								    $bHasDJ, $bHasLiveMusic, $bHasKaraoke, null, //$bHasBgndMusic
								    $bHasCuisine, $bHasTerrace, $bHasClima, $bHasWardrobe, $bHasWifi, $bHasDelivery,
								    false, 0, $aContext);
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
				$rsPlace = $oPlace->ListAll($page, $city, null, null, '', $xReqLetter, false, false, 0, $aContext);
			}
		}
	}
	if ($bShowFilter)
		// REMOVE COMMENT TO SHOW SEARCH FORM
		#include_once('template/place_search_quick.php');
	if (is_array($_POST) && count($_POST)>0)
		echo $sSearchCriteria;
	if (isset($rsPlace))
	{
		if (mysql_num_rows($rsPlace))
		{
			if (isset($dSelStartDate) && !empty($dSelStartDate))
			{
				$dStartDate = $dSelStartDate;
			}
			else
			{
				$dToday = date(DEFAULT_DATE_DB_FORMAT);
				$dStartDate = $dToday;
			}
			$dEndDate = increaseDate($dStartDate, THIS_WEEK_DAYS-1);

			$aAllCities = getLabel('aCitiesAll');

			$aLetterKeys = array();
			$aAlphabetLettersKeys = getLabel('aAlphabetLettersKeys');
			
			while($row=mysql_fetch_object($rsPlace))
			{
					$sAddress = '';
					$rsAddress = $oAddress->ListAll($row->PlaceID, $aEntityTypes[ENT_PLACE], 1); //$row->PlaceTypeID
					if(mysql_num_rows($rsAddress))
					{
						while($rAddress = mysql_fetch_object($rsAddress))
						{
							$sAddress .= $aAllCities[$rAddress->CityID].', ';
							$sAddress .= $rAddress->Street.'<br />';
						}
					}

					$sPhone = '';

					$sPlaceGuide = '';
					$nLastID = $oPlace->GetMaxID();
					if ($nLastID - $row->PlaceID <= NEWEST_PLACES)
					{
						$sPlaceGuide .= '<a href="javascript:void(0);" title="'. getLabel('strNew') .'">'.drawImage('img/cg_new.png', 0, 0, getLabel('strNew')).'</a>';
					}
					$rPlaceGuide = $oPlaceGuide->GetByPlaceID($row->PlaceID);
					if ($rPlaceGuide)
					{
						if (!empty($rPlaceGuide->HasWifi))
							$sPlaceGuide .= '<a href="javascript:void(0);" title="'. getLabel('strWifi') .'">'.drawImage('img/cg_wifi.png', 0, 0, getLabel('strWifi')).'</a>';
						if (!empty($rPlaceGuide->VacationStartDate) || !empty($rPlaceGuide->VacationEndDate))
						{
							$dToday = date(DEFAULT_DATE_DB_FORMAT);
							if ($rPlaceGuide->VacationStartDate <= $dToday && $rPlaceGuide->VacationEndDate >= $dToday)
								$sPlaceGuide .= '<a href="javascript:void(0);" title="'. getLabel('strVacation') .'">'.drawImage('img/cg_vacation.png', 0, 0, getLabel('strVacation')).'</a>';
						}	
					}

					if(strlen($sPlaceGuide) > 0){
						$sPlaceGuide = '<span style="width: 30px; height: 30px; float: right;">'. $sPlaceGuide .'</span>';
					}
					
					$sLetter = $row->Letter;
					$nLetterKey = $aAlphabetLettersKeys[$sLetter];
					if (!in_array($nLetterKey, $aLetterKeys))
					{
						$aLetterKeys[] = $nLetterKey;
						$sListContent .= '<a name="alpha_'.$nLetterKey.'"></a>'."\n";
					}

					$sProgramme = '';
					if ($nRootPage == 26) // zavedenia
					{
						$aRelEventIDs = array();
						$rsProgram = $oProgram->ListAllByDate($row->PlaceID, null, null, null, $dStartDate, $dEndDate);
						$sProgramme .='<br />';
						while($rrow = mysql_fetch_object($rsProgram))
						{
							if (!empty($rrow->EventID) && !in_array($rrow->EventID, $aRelEventIDs))
							{
								$aRelEventIDs[] = $rrow->EventID;
								$rMainEvent = $oEvent->GetByID($rrow->EventID);
								$sProgramme .= '&middot; <a href="'.setPage($nRootPage, 0, $rMainEvent->EventID).'">'.stripComments($rMainEvent->Title).'</a><br />'.
									IIF(!empty($rrow->PremiereTypeID), '- '.$aPremiereTypes[$rrow->PremiereTypeID], '');
							}
						}
					}

					$nPlacePageToGo = $page;
					if ($page == 78)
					{
						$nPlacePageToGo = 26; // zavedenia
					}
					elseif ($page == 85)
					{
						$nPlacePageToGo = 27; // extreme
					}
					$aRelPlacePages = $oPlace->ListPlacePagesAsArray($row->PlaceID);
					if (is_array($aRelPlacePages) && count($aRelPlacePages) > 0)
						$nPlacePageToGo = $aRelPlacePages[0];

//					$sListContent .= '<div class="box">
//					<h3><a href="'.setPage($nPlacePageToGo, 0, $row->PlaceID).'" title="'.stripComments(htmlspecialchars(strip_tags($row->Title))).'">'.stripComments($row->Title).'</a></h3>
//					'.IIF(!empty($sPlaceGuide), '<div class="place_guide">'.$sPlaceGuide.'</div>', '').'
//					<div>'.IIF(!empty($sAddress), getLabel('strAddress').': '.$sAddress, '').
//						IIF(!empty($row->WorkingTime), getLabel('strWorkingTime').': '.$row->WorkingTime.'<br />', '<br />').
//						$sPhone.$sProgramme.'</div>
//					</div>'."\n";

					$sListContent .= '
										<li>
					                    	<h2'. (!empty($sPlaceGuide) ? ' style="line-height: 29px;"' : '').'>'.IIF(!empty($sPlaceGuide), $sPlaceGuide , ' ').'<a href="'. setPage($nPlacePageToGo, 0, $row->PlaceID) .'">'. stripComments(htmlspecialchars(strip_tags($row->Title))) .'</a></h2>
					                    	<p>
						                        '.IIF(!empty($sAddress), getLabel('strAddress').': '.$sAddress, '') .'
						                        '.IIF(!empty($row->WorkingTime), getLabel('strWorkingTime').': '. $row->WorkingTime, '') .'
						                        '. IIF(!empty($sPhone), $sPhone,'') .'
						                        '. IIF(!empty($sProgramme), $sProgramme, '') .'
					                        </p>
					                    </li>';
			}
		}
		else
		{
			$sListContent .= '<div>'.getLabel('strNoRecords').'</div>'."\n";
		}
	}
	
	echo '<div id="container">
    		<ul class="simple-list">';	
	
	echo $sListContent;
	
	echo '</ul>
		</div>';	
}
?>