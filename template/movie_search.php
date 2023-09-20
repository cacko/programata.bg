<?php
if (!$bInSite) die();
//=========================================================
$sSearchCriteria = '';
if (!isset($item) || empty($item))
{
    $sSearchCriteria .= '<br />'.getLabel('strSearchResults').':<br />';
    $sKeyword = htmlspecialchars(strip_tags(getPostedArg('keyword', '')));
    if (!empty($sKeyword))
        $sSearchCriteria .= getLabel('strKeyword').': '.$sKeyword.'<br />';
?>
<script type="text/javascript">
<!--
function fCheck(frm)
{
    if (!valEmpty("keyword", "<?=getLabel('strEnter').getLabel('strKeyword')?>")) return false;
    return true;
}
//-->
</script>
<script type="text/javascript" src="js/calendar.js"></script>
<div class="formbox">
<form method="post" action="<?=setPage($page, 0, 0, ACT_SEARCH)?>" name="feedback" onsubmit="return fCheck(this);">
<h6><?=getLabel('strGoSearch')?></h6>
<br />
<div>
    <label for="keyword"><?=getLabel('strKeyword')?></label><br />
    <input type="text" name="keyword" id="keyword" maxlength="255" class="fldfilter" value="<?=$sKeyword?>" />
</div>
<br class="clear" /><br />
<br />
<input type="submit" value="<?=getLabel('strSearch')?>" class="btn" />
</form>
</div>
<?
}
    switch($action)
    {
        case ACT_SEARCH:
            if (empty($sKeyword)) {} // do nothing
            else
            $rsEvent = $oEvent->ListByName($sKeyword, 10); //10 is for movies
            break;
        default:
            //
            break;   
    }
    
	if (isset($rsEvent))
	{
		if (mysql_num_rows($rsEvent))
		{
			$aEvents = array();
			$aPlaces = array();

			$i = 0;
			while($row=mysql_fetch_object($rsEvent))
			{
				if (!empty($row->EventID))
				{
					if (!in_array($row->EventID, $aEvents))
					{
						$aEvents[] = $row->EventID;
						$aPlaces = array();

						// PAGES - CATEGORIES
						$sMainImageFile = '';
						$bMidImage = false;
						$bHasTrailer=false;
						$sTrailer='';
						$sMainImageFile = UPLOAD_DIR.IMG_EVENT_MID.$row->EventID.'.'.EXT_IMG;
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
						$rsAttachment = $oAttachment->ListAll($row->EventID, $aEntityTypes[ENT_EVENT], 7); //$row->EventTypeID
						while($rAttachment = mysql_fetch_object($rsAttachment))
						{
							$bHasTrailer = true;
							$sTrailer=drawImage('img/trailer.png', 0, 0, '', '', 'trailerIcon');
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
						elseif($rCurrentPage->TemplateFile == 'movie_search')
						{
							$nEventPageToGo = 30; // movies
							$page = 30;
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
							<a class="more" href="'.setPage($nEventPageToGo, 0, $row->EventID).'" title="'.stripComments(htmlspecialchars(strip_tags($row->Title))).'">
							'.drawImage($sMainImageFile, 0, 0, stripComments($row->Title)).
							IIF(($bHasTrailer), $sTrailer, '').
							'</a>';
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
		echo $sListContent;
		
	}   
?>