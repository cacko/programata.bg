<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	$rEvent = $oEvent->GetByID($item);
	if ($rEvent)
	{
		$set_fb_like_to_bottom = true;
		
		$nEntityType = $aEntityTypes[ENT_EVENT];
		include('template/comment_save.php');

		$sItemTitle = $rEvent->Title;
		$oEvent->TrackView($item);
		// =========================== ADDRESS ===========================
		$sAddress = '';
		$rsAddress = $oAddress->ListAll($rEvent->EventID, $aEntityTypes[ENT_EVENT], 1); //$rEvent->EventTypeID
		if(mysql_num_rows($rsAddress))
		{
			while($rAddress = mysql_fetch_object($rsAddress))
			{
				$sAddress .= $rAddress->Street.'<br />';
			}
		}
		// =========================== PHONE ===========================
		$sPhone = '';
		$rsPhone = $oPhone->ListAll($rEvent->EventID, $aEntityTypes[ENT_EVENT], array(1,2,3,6)); //$rEvent->EventTypeID
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
		$rsEmail = $oEmail->ListAll($rEvent->EventID, $aEntityTypes[ENT_EVENT], 1); //$rEvent->EventTypeID
		if(mysql_num_rows($rsEmail))
		{
			while($rEmail = mysql_fetch_object($rsEmail))
			{
				$sEmail .= getLabel('strEmail').': <a href="mailto:'.$rEmail->Email.'">'.$rEmail->Email.'</a><br />';
			}
		}
		// =========================== LINKS ===========================
		$sLink = '';
		$sMoreInfoLink = '';
		$rsLink = $oLink->ListAll($rEvent->EventID, $aEntityTypes[ENT_EVENT], array(1,2,3,4,0)); //$rEvent->EventTypeID
		if(mysql_num_rows($rsLink))
		{
			$aLinkTypes = getLabel('aLinkTypes');
			while($rLink = mysql_fetch_object($rsLink))
			{
				$sImage = '';
				switch($rLink->LinkTypeID)
				{
					case 2: // eventim
						$sLink .= '<div class="link"><strong>'.getLabel('strUrl').'</strong> <a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$aLinkTypes[$rLink->LinkTypeID].'</a></div>';
						break;
					case 3: // map
						$sLink .= '<div class="link"><strong>'.getLabel('strUrl').'</strong> <a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$aLinkTypes[$rLink->LinkTypeID].'</a></div>';
						break;
					case 4: //more info
						//$sLinkImageName = $oPage->GetMainImageName($rLink->Url);
						//if(is_file($sLinkImageName)) $sImage = drawImage($sLinkImageName, 0, 0, '');
						//$sMoreInfoLink .= '<div class="more_info">'.getLabel('strMoreInfo').': <a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$sImage.$rLink->Title.'</a></div>';
                                                $sMoreInfoLink .= '<div class="more_info">'.getLabel('strMoreInfo').': <a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$rLink->Title.'</a></div>';
						break;
					default:
//						$sLink .= '<div class="link">'.getLabel('strUrl').': <a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$rLink->Title.'</a></div>';
						$sLink .= ' 
									<div class="link">
                    					<strong>'.getLabel('strUrl').'</strong> <a href="'.str_replace('&', '&amp;', $rLink->Url).'" title="'.$rLink->Title.'" target="_blank">'.$rLink->Title.'</a>
                    				</div>';
						break;
				}
			}
		}
		// =========================== COMMENTS ===========================
		$nComments = $oComment->GetCountByEntity($rEvent->EventID, $aEntityTypes[ENT_EVENT]);
		// =========================== IMAGES ===========================
		$sGalleryToDisplay = '';
		$sMainImageFile = UPLOAD_DIR.IMG_EVENT.$rEvent->EventID.'.'.EXT_IMG; //IMG_EVENT_MID

		$sThumbnails = '';
		$nNrThumbs = 0;
		$sPanoramaFile = '';
		if (!is_file($sMainImageFile))
		{
			$sMainImageFile = UPLOAD_DIR.'g-'.$nRootPage.'.'.EXT_IMG;//UPLOAD_DIR.IMG_EMPTY.'.'.EXT_PNG;
		}

		$rsAttachment = $oAttachment->ListAll($rEvent->EventID, $aEntityTypes[ENT_EVENT], array(1,2, 7)); //$rEvent->EventTypeID
//		$sGalleryToDisplay = '<ul>';
		while($rAttachment = mysql_fetch_object($rsAttachment))
		{
			
			
			if ($rAttachment->AttachmentTypeID == 5)
				$sPanoramaFile = UPLOAD_DIR.$rAttachment->AttachmentID.'.'.$rAttachment->Extension;
			elseif ($rAttachment->AttachmentTypeID == 1 && empty($sMainImageFile))
				$sMainImageFile = FILEBANK_DIR.$rAttachment->AttachmentID.'.'.$rAttachment->Extension;
			elseif ($rAttachment->AttachmentTypeID == 7){
				$sTrailerFile = UPLOAD_DIR.FILE_TRAILER.$rAttachment->AttachmentID.'.'.$rAttachment->Extension;
			}
			elseif ($nNrThumbs < 3)
			{
				$sThumbImageFile = FILEBANK_DIR.$rAttachment->AttachmentID.'.'.$rAttachment->Extension;
				$sThumbnails .= drawImage($sThumbImageFile);
				$nNrThumbs ++;
			}
		}
		if (!empty($sPanoramaFile))
			$sGalleryToDisplay .= '<h4>'.getLabel('strPanorama').'</h4>
						<a name="panorama"></a>'.drawMovie($sPanoramaFile, W_IMG_GALLERY, H_IMG_GALLERY).'
						<br class="clear" />'.getLabel('strQuicktimePlugin')."\n";
		else
		{
			$sGallery = showGallery($rEvent->EventID, ENT_EVENT, false, false, true);
			
			if (!empty($sGallery)){
//				$sGalleryToDisplay .= '<h4>'.getLabel('strGallery').'</h4>
//							<a name="galleria"></a>'.$sGallery."\n";
//				$sGalleryToDisplay .= '<li>'. $sGallery .'</li>';
				$sGalleryToDisplay .= '<div class="gallery"><ul>'.$sGallery .'</ul></div>';
			}elseif(!empty($sThumbnails)){
//				$sGalleryToDisplay .= '<h4>'.getLabel('strGallery').'</h4>
//							<a name="galleria"></a>
//							<div class="thumbnail">'.$sThumbnails.'</div>'."\n";
//				$sGalleryToDisplay .= '<li>'. $sThumbnails.'</li>';
			}
		}
		
//		$sGalleryToDisplay .= '</ul>';
		
//		d($sGalleryToDisplay);
		// =========================== RATING ===========================
		$sRating = $sRatingForm = '';
		//if ($nRootPage == 26)
		//{
			include('template/rate_save.php');
			$fRating = $oRate->GetRating($rEvent->EventID, $aEntityTypes[ENT_EVENT]);
			//$sRating .= '<h6>'.getLabel('strRating').': '.$fRating.'</h6>';
			$sRating .= drawImage('img/rating_'.ceil($fRating).'.png', 0, 0, getLabel('strRating').': '.$fRating);
			$nUserID = $oSession->GetValue(SS_USER_ID);
			if (!empty($nUserID))
			{
				$bUserRated = $oRate->IsRated($rEvent->EventID, $aEntityTypes[ENT_EVENT]);
				if (!$bUserRated)
				{
					$sRating .= '<div class="vote"><a href="#">'.getLabel('strVote').'</a></div>';
					$sRatingForm = displayRatingForm();
				}
			}
		//}
		// =========================== LABELS (INTERNAL-OLD) ===========================
		$sCategory = '';
		/*
		$aEventSubtypes = getLabel('aEventSubtypes');
		$aOrigLanguages = getLabel('aOrigLanguages');
		$aTranslations = getLabel('aTranslations');
		if (!empty($rEvent->EventSubtypeID))
		{
			if (!empty($sCategory)) $sCategory .= ', ';
			$sCategory .= $aEventSubtypes[$rEvent->EventSubtypeID];
		}
		if (!empty($rEvent->OriginalLanguageID))
		{
			if (!empty($sCategory)) $sCategory .= ', ';
			$sCategory .= $aOrigLanguages[$rEvent->OriginalLanguageID];
		}
		if (!empty($rEvent->TranslationID))
		{
			if (!empty($sCategory)) $sCategory .= ', ';
			$sCategory .= $aTranslations[$rEvent->TranslationID];
		}*/
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
			if (!empty($sNewLabels))
				$sLabels .= '<br />'.getLabel('strOrigLanguage').': '.$sNewLabels."\n";
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
			if (!empty($sNewLabels))
				$sLabels .= '<br />'.getLabel('strTranslation').': '.$sNewLabels."\n";
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
			if (!empty($sNewLabels))
				$sLabels .= '<br />'.getLabel('strMusicStyle').': '.$sNewLabels."\n";
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
			if (!empty($sNewLabels))
				$sLabels .= '<br />'.getLabel('strGenre').': '.$sNewLabels."\n";
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
			if (!empty($sNewLabels))
				$sLabels .= '<br />'.$sNewLabels."\n";//getLabel('strGenre').': '.
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
		// if ($rEvent->EventTypeID == 10) // film
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
//		}

//		$sProgramNav = '<h4>'.getLabel('strProgram').'</h4>
//		<a name="program"></a>'."\n";
		
		$sProgramNav = '
					<div class="program">
                    	<h5>'.getLabel('strProgram').'</h5>
	                        <ul>';

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
					if ($i > 0)
						$sDateInfo .= '</li>'."\n";
					$i++;
					$aPlaces[] = $row->PlaceID;

					$aPlacePages = $oPlace->ListPlacePagesAsArray($row->PlaceID);
//					$sDateInfo .= '<div class="'.IIF($i%2==0, 'even', 'odd').'">
//						<h5><a href="'.setPage($aPlacePages[0], 0, $row->PlaceID).'">'.stripComments($row->Title).'</a></h5>'."\n";
//
					$sDateInfo .= '<li><h6><a href="'.setPage($aPlacePages[0], 0, $row->PlaceID).'" title="'.stripComments($row->Title).'">'.stripComments($row->Title).'</a></h6>';

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
//										$sPremiere .= '<div class="prepremiere"><span>'.$aPremiereTypes[$row->PremiereTypeID].'</span></div>';
										$sPremiere .= '<span>'.$aPremiereTypes[$row->PremiereTypeID].'</span>';
										break;
									case 2: // premiere
									case 4: // official premiere
//										$sPremiere .= '<div class="premiere"><span>'.$aPremiereTypes[$row->PremiereTypeID].'</span></div>';
										$sPremiere .= '<span>'.$aPremiereTypes[$row->PremiereTypeID].'</span>';
										break;
									case 3: // exclusive
									case 5: // special screening
//										$sPremiere .= '<div class="exclusive"><span>'.$aPremiereTypes[$row->PremiereTypeID].'</span></div>';
										$sPremiere .= '<span>'.$aPremiereTypes[$row->PremiereTypeID].'</span>';
										break;

								}
								$aPremieres[] = $row->PremiereTypeID;
							}
						}
						//d($aDates);
						if (count($aDates) > 0)
							$sDateInfo .= '</p>';
						$sDateInfo .= $sPremiere;
						$aDates[] = $row->ProgramDate;

						//if ($dStartDate != $dEndDate)
							$sDateInfo .= '<p>'. formatDate($row->ProgramDate, FULL_DATE_DISPLAY_FORMAT);
						$aHalls = array();
						$aRelEventIDs = array();
					}
			
				}
				elseif (!empty($row->StartDate) && $row->StartDate != DEFAULT_DATE_DB_VALUE)
				{
					$sDateInfo .= formatDate($row->StartDate, FULL_DATE_DISPLAY_FORMAT).' - '.formatDate($row->EndDate, FULL_DATE_DISPLAY_FORMAT);
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
			}
			$programa_container = IIF(!empty($sDateInfo), $sProgramNav.$sDateInfo.'</ul></div>'."\n", '');			
		}
		else
		{
			$aEventCities = $oProgram->ListEventCitiesAsArray($rEvent->EventID, $dStartDate, $dEndDate);
			if (is_array($aEventCities) && count($aEventCities)>0)
			{
				echo $sProgramNav.'<br />';
				foreach ($aEventCities as $k)
				{
					if ($k != $city)
						echo ' &middot; <a href="'.setPage($page, $cat, $item, $action, $relitem, $k).'">'.$aCities[$k].'</a>';
				}
			}
		}		
		
		if(!empty($programa_container)){
			$programa_container = preg_replace('/<h6>(.*)<\/h6><span>(.*)<\/span>/isU','<span>$2</span><h6>$1</h6>', $programa_container);
		}		
		
?>
<!--		<h2 title="<?=stripComments(htmlspecialchars(strip_tags($rEvent->Title)))?>"><?=stripComments($rEvent->Title)?></h2>-->
<!--		<div class="box detail">-->
	  <div id="container">
    	<div id="article">
        	<div class="main-preview">
        	<?
        	$has_trailer = false;
			if(is_file($sTrailerFile))
			{
				$has_trailer = true;
				?>
					<div style="position: relative; z-index: 99999999; display: none; margin-top: 0px; width: 470px; height: 250px;" class="player" href="<?= $sTrailerFile?>"></div>
					<div id="p_icon" style="position: absolute;width: 470px;height: 250px;display: block;cursor: pointer;" onclick="$(this).hide(); $('#wallpepar').hide(); $('.player').show();">
						<img src="img/play_large.png" style="width: 40px;height: 100px;position: absolute;left: 220px;top: 75px;cursor: pointer;"/>
					</div>
			<script>
			//flowplayer("div.player", "flowplayer-3.2.2.swf");
			flowplayer("div.player", "http://programata.bg:8080/flowplayer-3.2.2.swf");
			</script>
		  <?}?>
            	<a href="<?=$sMainImageFile?>" id="wallpepar" title="<?php echo stripComments(htmlspecialchars(strip_tags($rEvent->Title)));?>"><?php echo drawImage($sMainImageFile, 470, 250, stripComments($rEvent->Title)); ?></a>
                <h1 style="position: relative;"><?php echo stripComments(htmlspecialchars(strip_tags($rEvent->Title)));?></h1>
                <?php if(strlen($rEvent->OriginalTitle) > 0){?>
<!--                <h2><?php echo getLabel('strOriginalTitle').': '.$rEvent->OriginalTitle; ?></h2>-->
                <?php }else{ echo '<br />'; } ?>
            </div>
		
<?

		if(!empty($sGalleryToDisplay)){
			echo substr($sGalleryToDisplay,0, 25) .'<li'. ($has_trailer ? ' id="this_has_trailer"' : '') .'><a href="'. $sMainImageFile .'" title="'. $row->Title .'">'. drawImage($sMainImageFile, 138, 93, $row->Title) .'</a></li>'. substr($sGalleryToDisplay,25);	
		}
		
		if(($item == 56066) || ($item == 56912))
		{
			include('template/old_school_party.php');
		}
		else
		{
			if(false && is_file($sTrailerFile))
			{
			?><div class="player"
				href="<?= $sTrailerFile?>"
				style="display:block;width:460px;height:250px;border:0px; background-image:url(<?=$sMainImageFile?>)">

				<!-- play button -->
				<img src="img/play_large.png"/>

			</div>
			<script>
			flowplayer("div.player", "flowplayer-3.2.2.swf");
			</script>
<?
			} else {
				//$top_image = drawImage($sMainImageFile, 0, 0, stripComments($row->Title));
			}
?>
		<?php
			#IIF(!empty($sRating), '<div class="rating">'.$sRating.'</div>', '')
		?>
			<?=IIF(!empty($sRatingForm), $sRatingForm, '')?>
			<br class="clear" />
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
<!--			<?=IIF(!empty($rEvent->OriginalTitle), '<div>'.getLabel('strOriginalTitle').': '.$rEvent->OriginalTitle.'</div>', '')?>-->
		<!--	<div><?=IIF(!empty($sCategory), $sCategory.'<br />', '').
				$sLabels.
				IIF(!empty($rEvent->Features), str_replace(' &middot; ', '<br />', strip_tags($rEvent->Features, '<a><br>')).'<br />', '').
				IIF(!empty($rEvent->Comment), str_replace(' &middot; ', '<br />', strip_tags($rEvent->Comment, '<a><br>')), '')?></div>-->
			
				<?php 
					// text summary
					
					if(!empty($rEvent->OriginalTitle) || !empty($sCategory) || !empty($rEvent->Features) || !empty($rEvent->Comment)){
						echo '<div class="text summary">'
							 . IIF(!empty($rEvent->OriginalTitle), '<div>'.getLabel('strOriginalTitle').': '.$rEvent->OriginalTitle.'</div>', '') 
							 . IIF(!empty($sCategory), $sCategory.'<br />', '') 
							 . IIF(!empty($rEvent->Features), str_replace(' &middot; ', '<br />', strip_tags($rEvent->Features, '<a><br>')).'<br />', '') 
							 . IIF(!empty($rEvent->Comment), str_replace(' &middot; ', '<br />', strip_tags($rEvent->Comment, '<a><br>')), '') .
							 '</div>';
						echo '<div class="fb-like" data-send="true" data-width="450" data-show-faces="true"></div>';
						$set_fb_like_to_bottom = false;
					}
				
				?>
			<?php if(!empty($rEvent->Lead) || !empty($rEvent->Description)){ ?>
				<div class="text">
					<?=IIF(!empty($rEvent->Lead), $rEvent->Lead .'<br /><br /><br />', '')?>
					<?=IIF(!empty($rEvent->Description), $rEvent->Description, '')?>
				</div>	
			<?php } ?>
			<?=IIF(!empty($sMoreInfoLink), '<br />'.$sMoreInfoLink, '')?>
			<div><br /><?=IIF(!empty($sAddress), getLabel('strAddress').': '.$sAddress, '').
						$sPhone.$sEmail.$sLink?></div>
		<? 
			echo $programa_container;
		} ?>

<?

		// add to calendar
		if (false && !empty($nUserID) && !empty($sDateInfo)) //
		{
			include('template/user_calendar_save.php');
			echo '<div class="calendar_add"><a href="#">'.getLabel('strAddToCalendar').'</a></div>'."\n";
			echo displayCalendarForm($rEvent->EventID, $aDates[0], $aDates[count($aDates)-1]);
		}
		// gallery
//		echo IIF(!empty($sGalleryToDisplay), $sGalleryToDisplay.'<br class="clear" />', '');

		include('template/comment_list.php');
	}
	else{
		$item = 0;
	}
	
	echo '	
			<script type="text/javascript">
					if($(".gallery a").lightBox().length) {
						$(".main-preview a").click(function(event) {
							event.preventDefault();
							$(".gallery a[href=\'" + $(this).attr("href") + "\']").click();
						});
					} else {
						$(".main-preview a").lightBox();
					}
					
			</script>     
	</div>
	</div>	';
}
?>