<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	$rFestival = $oFestival->GetByID($item);
	if ($rFestival)
	{
		$nEntityType = $aEntityTypes[ENT_FESTIVAL];
		include('template/comment_save.php');
		
		$sItemTitle = $rFestival->Title;
		$oFestival->TrackView($item);
		
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
					$sLink .= '<div class="link"><strong>'. getLabel('strUrl').'</strong> <a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$rLink->Title.'</a></div>';
			}
		}
		// =========================== COMMENTS ===========================
		$nComments = $oComment->GetCountByEntity($rFestival->FestivalID, $aEntityTypes[ENT_FESTIVAL]);
		// =========================== IMAGES ===========================
		$sMainImageFile = UPLOAD_DIR.IMG_FESTIVAL.$rFestival->FestivalID.'.'.EXT_IMG;
		if (!is_file($sMainImageFile))
		{
			$sMainImageFile = UPLOAD_DIR.IMG_GALLERY.'.'.EXT_PNG;
		}
		$sGalleryToDisplay = '';
		$sGallery = showGallery($rFestival->FestivalID, ENT_FESTIVAL, false, false, true);
		if (!empty($sGallery))
			$sGalleryToDisplay .= '<div class="gallery"><ul>'.$sGallery .'</ul></div>';		
//			$sGalleryToDisplay .= '<h4>'.getLabel('strGallery').'</h4>
//						<a name="galleria"></a>'.$sGallery."\n";
		
		// =========================== FESTIVAL INFO ===========================
		$sDate = '';
		if ($rFestival->StartDate === $rFestival->EndDate)
			$sDate .= formatDate($rFestival->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
		elseif (formatDate($rFestival->StartDate, FULL_MONTH_YEAR_DISPLAY_FORMAT) === formatDate($rFestival->EndDate, FULL_MONTH_YEAR_DISPLAY_FORMAT))
			$sDate .= formatDate($rFestival->StartDate, DAY_DISPLAY_FORMAT).' - '.formatDate($rFestival->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
		else
			$sDate .= formatDate($rFestival->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT).' - '.formatDate($rFestival->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
			
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
		//getLabel('strProgram')
		// =========================== PROGRAM BY DATE =========================== 
		$rsProgram = $oProgram->ListAllByDate(null, null, $rFestival->FestivalID, $city, $dStartDate, $dEndDate);
		$set_date = array();
		if (mysql_num_rows($rsProgram) > 0)
		{
			$aDates = array();
			$i = 0;
//			$sDateInfo = '';
			$sDateInfo = '
					<div class="program">
                    	<h5>'.getLabel('strProgram').'</h5>
	                        <ul>';
			$aPremiereTypes = getLabel('aPremiereTypes');
			while($row = mysql_fetch_object($rsProgram))
			{
				if (!empty($row->ProgramDate))
				{
					if (!in_array($row->ProgramDate, $aDates))
					{
						if ($i > 0)
							$sDateInfo .= '</li>'."\n";
						$i++;
						$aDates[] = $row->ProgramDate;
						
						$set_date[$row->ProgramID] = formatDate($row->ProgramDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
						
//						$sDateInfo .= '<div class="'.IIF($i%2==0, 'even', 'odd').'">
//							<div>'.formatDate($row->ProgramDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</div>'."\n";
						#$sDateInfo .= '<li><h6><a href="'.setPage($aPlacePages[0], 0, $row->PlaceID).'" title="'.formatDate($row->ProgramDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'">'.formatDate($row->ProgramDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</a></h6>';
					}
				}
				elseif (!empty($row->StartDate))
				{
					if ($i > 0)
						$sDateInfo .= '</li>'."\n";
					$i++;
					
					$set_date[$row->ProgramID] = formatDate($row->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT).' - '.formatDate($row->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
//					$sDateInfo .= '<div class="'.IIF($i%2==0, 'even', 'odd').'">
//						<div>'.formatDate($row->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT).' - '.formatDate($row->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</div>'."\n";
						#$sDateInfo .= '<li><h6><a href="'.setPage($aPlacePages[0], 0, $row->PlaceID).'" title="'.formatDate($row->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT).' - '.formatDate($row->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'">'.formatDate($row->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT).' - '.formatDate($row->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT).'</a></h6>';
						
						
				}
				
				
				if(!empty($set_date[$row->ProgramID])){
					$set_current_data = $set_date[$row->ProgramID];
				} else $set_current_data = formatDate($row->ProgramDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
				
				if (!empty($row->EventID))
				{
					$sPremiere = '';
					if (!empty($row->PremiereTypeID))
					{
						switch($row->PremiereTypeID)
						{
							case 1: // prepremiere
//								$sPremiere .= '<div class="prepremiere"><a>'.$aPremiereTypes[$row->PremiereTypeID].'</a></div>'."\n";
								$sPremiere .= '<span>'.$aPremiereTypes[$row->PremiereTypeID].'</span>'."\n";
								break;
							case 2: // premiere
							case 4: // official premiere
//								$sPremiere .= '<div class="premiere"><a>'.$aPremiereTypes[$row->PremiereTypeID].'</a></div>'."\n";
								$sPremiere .= '<span>'.$aPremiereTypes[$row->PremiereTypeID].'</span>'."\n";
								break;
							case 3: // exclusive
							case 5: // special screening
//								$sPremiere .= '<div class="exclusive"><a>'.$aPremiereTypes[$row->PremiereTypeID].'</a></div>'."\n";
								$sPremiere .= '<span>'.$aPremiereTypes[$row->PremiereTypeID].'</span>'."\n";
								break;
							
						}
					}
					$rMainEvent = $oEvent->GetByID($row->EventID);
					$aRelPages = $oProgram->ListProgramPagesAsArray($row->MainProgramID);
					$sDateInfo .= '<li>
									   <h6><a href="'.setPage($aRelPages[0], 0, $rMainEvent->EventID).'">'.stripComments($rMainEvent->Title).'</a></h6>'. $set_current_data ."\n".$sPremiere;
					if($row->ProgramID == "194924")
					{
						echo "<!--";
 						echo $set_current_data;
						echo "-->";
					}				
					
					// PROGRAM NOTE COMES HERE
					//$rNote = $oProgramNote->GetByProgramID($row->MainProgramID);
					//if ($rNote && !empty($rNote->Title))
					//	$sDateInfo .= $rNote->Title.'<br />'."\n";
				}
				$aRelPlaces = $oProgram->ListProgramPlacesAsArray($row->MainProgramID);
				if (is_array($aRelPlaces) && count($aRelPlaces)>0)
				{
					//$sDateInfo .= ' - ';
					foreach($aRelPlaces as $key=>$val)
					{
						$rRelPlace = $oPlace->GetByID($val);
						$aPlacePages = $oPlace->ListPlacePagesAsArray($val);
						$sDateInfo .= '<div class="guest"><a href="'.setPage($aPlacePages[0], 0, $rRelPlace->PlaceID).'">'.stripComments($rRelPlace->Title).'</a></div>'."\n";
					}
				}
				$aRelEvents = $oProgram->ListProgramEventsAsArray($row->MainProgramID);
				$sDateInfo .= '<p>';
				if (is_array($aRelEvents) && count($aRelEvents)>0)
				{
//					$sDateInfo .= ' - ';
					foreach($aRelEvents as $key=>$val)
					{
						$rRelEvent = $oEvent->GetByID($val);
						//$aEventPages = $oProgram->ListProgramPagesAsArray($row->MainProgramID);
						//$sDateInfo .= '<a href="'.setPage($aEventPages[0], 0, $rRelEvent->EventID).'">'.stripComments($rRelEvent->Title).'</a> ';
						$sDateInfo .= '<a href="'.setPage($nRootPage, 0, $rRelEvent->EventID).'">'.stripComments($rRelEvent->Title).'</a> ';
					}
//					$sDateInfo .= '<br />'."\n";
				}
				if (!empty($row->PlaceID))
				{
					$rPlace = $oPlace->GetByID($row->PlaceID);
					$aPlacePages = $oPlace->ListPlacePagesAsArray($row->PlaceID);
					$sDateInfo .= '<a href="'.setPage($aPlacePages[0], 0, $row->PlaceID).'">'.stripComments($rPlace->Title).'</a> ';
				}
				if (!empty($row->PlaceHallID))
				{
					$rPlaceHall = $oPlaceHall->GetByID($row->PlaceHallID);
					$sDateInfo .= ', '.$rPlaceHall->Title.'';
				}
				
				
				
				$sDateInfo .= IIF(!empty($row->ProgramTime), ' - '.formatTime($row->ProgramTime), '');
				//$sDateInfo .= IIF(!empty($row->Price), ' ('.formatPrice($row->Price).getLabel('strLv').')', '');
				$sDateInfo .= IIF(!empty($row->Price), ', '.$row->Price.getLabel('strLv'), '');
				$sDateInfo .= '</p>';
				// NOTE COMES HERE
				//$sDateInfo .= '<br />'."\n";
				//$sDateInfo .= '</div>'."\n";
			}
			$programa_container = IIF(!empty($sDateInfo), $sDateInfo.'</ul></div>'."\n", '');
		}

		if(!empty($programa_container)){
			$programa_container = preg_replace('/<h6>(.*)<\/h6><span>(.*)<\/span>/isU','<span>$2</span><h6>$1</h6>', $programa_container);
		}	

		$sDate = '';
		if ($rFestival->StartDate === $rFestival->EndDate)
			$sDate .= formatDate($rFestival->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
		elseif (formatDate($rFestival->StartDate, FULL_MONTH_YEAR_DISPLAY_FORMAT) === formatDate($rFestival->EndDate, FULL_MONTH_YEAR_DISPLAY_FORMAT))
			$sDate .= formatDate($rFestival->StartDate, DAY_DISPLAY_FORMAT).' - '.formatDate($rFestival->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
		else
			$sDate .= formatDate($rFestival->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT).' - '.formatDate($rFestival->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT);		
?>

	  <div id="container">
    	<div id="article">
        	<div class="main-preview">
            	<a href="<?=$sMainImageFile?>" title="<?php echo stripComments(htmlspecialchars(strip_tags($rFestival->Title)));?>"><?php echo drawImage($sMainImageFile, 470, 250, stripComments($rFestival->Title)); ?></a>
                <h1><?php echo stripComments(htmlspecialchars(strip_tags($rFestival->Title)));?></h1>
                <?php if(strlen($rEvent->OriginalTitle) > 0){?>
<!--                <h2><?php echo getLabel('strOriginalTitle').': '.$rEvent->OriginalTitle; ?></h2>-->
                <?php }else{ echo '<br /><br />'; } ?>
            </div>

            <div class="text summary"><?php echo $sDate; ?></div>
			            
            <?php 
			echo '<div class="fb-like" data-send="true" data-width="450" data-show-faces="true"></div>';
			$set_fb_like_to_bottom = false;
						
            if(!empty($rFestival->Lead) || !empty($rFestival->Content) || !empty($sLink)){ ?>
            <div class="text">
	 			<?=IIF(!empty($rFestival->Lead), $rFestival->Lead .'<br /><br /><br />', '')?>
				<?=IIF(!empty($rFestival->Content),  $rFestival->Content.'<br />', '')?>
				<?=IIF(!empty($sLink), $sLink.'<br />', '')?>           
            </div>
            <?php } ?>
         
            <script type="text/javascript">
					$(".main-preview a").lightBox();
			</script>        
<!--		<h2 title="<?=htmlspecialchars(strip_tags($rFestival->Title))?>"><?=$rFestival->Title?></h2>
		<div class="box detail">
			<?=drawImage($sMainImageFile, 0, 0, $row->Title)?>
			<div class="date"><?=$sDate?></div>
			<!--ul class="inline_nav">
				<li class="program"><a href="#program"><?=getLabel('strProgram')?></a></li>
				<li class="comment"><a href="#comment_list"><?=getLabel('strComments')?></a> (<?=$nComments?>)</li>
				<? if (!empty($sGalleryToDisplay)) {?>
				<li class="gallery"><a href="#galleria"><?=getLabel('strGallery')?></a></li>
				<? }?>
				<li class="friend"><a href="#friend"><a href="mailto:?body=<?='http://'.SITE_URL.'/'.setPage($page, $cat, $item)?>"><?=getLabel('strTellFriend')?></a></li>
			</ul-->
			<?php #IIF(!empty($rFestival->Lead), '<div class="lead">'.$rFestival->Lead.'</div>', '')?>
			<?php #IIF(!empty($rFestival->Content), '<div><br />'.$rFestival->Content.'</div>', '')?>
			<?php #IIF(!empty($sLink), '<div><br />'.$sLink.'</div>', '')?>
		
<?
		$aFestivalCities = $oFestival->ListFestivalCitiesAsArray($rFestival->FestivalID);
		if (is_array($aFestivalCities) && count($aFestivalCities)>0)
		{
			foreach ($aFestivalCities as $k)
			{
				if ($k != $city)
					echo ' &middot; <a href="'.setPage($page, $cat, $item, $action, $relitem, $k).'">'.$aCities[$k].'</a>';
			}
		}
		//echo IIF(!empty($sGalleryToDisplay), $sGalleryToDisplay.'<br class="clear" />', '');

		echo $programa_container;
		
		include('template/comment_list.php');
	}
	else
		$item = 0;
		
	echo '</div>
	</div>	';		
}
?>