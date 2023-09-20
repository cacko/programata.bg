<?php
if (!$bInSite) die();
//=========================================================
$aPages = $oPage->ListAllAsArray(null, '', 'event_list', true);
$aCities = getLabel('aCities');
$aProgramTypes = getLabel('aProgramTypes');
$aPremiereTypes = getLabel('aPremiereTypes');
$aReportTypes = getLabel('aReportTypes');
//=========================================================
$dToday = date(DEFAULT_DATE_DB_FORMAT);
$dStartDate = increaseDate($dToday, 0, 0);
$dEndDate = increaseDate($dToday, ADMIN_WEEK_DAYS, 0);

$dStartTime = $dEndTime = null;
$dStartDateToGo = parseDate(getRequestArg('start_date', formatDate($dStartDate, DEFAULT_DATE_DISPLAY_FORMAT)), null);
$dEndDateToGo = parseDate(getRequestArg('end_date', formatDate($dEndDate, DEFAULT_DATE_DISPLAY_FORMAT)), null);

$dStartDateToDisplay = getRequestArg('start_date', formatDate($dStartDate, DEFAULT_DATE_DISPLAY_FORMAT, ''));
$dEndDateToDisplay = getRequestArg('end_date', formatDate($dEndDate, DEFAULT_DATE_DISPLAY_FORMAT, ''));

$nSelReportType = getPostedArg('report_type');
if ($nSelReportType == 3 || $nSelReportType == 4)
{
	$dEndDateToDisplay = $dStartDateToDisplay;
	$dEndDateToGo = $dStartDateToGo;
}
	
?>
<script type="text/javascript">
<!--
function fCheck(frm) {
	if (!valEmpty("start_date", "<?=getLabel('strEnter').getLabel('strStartDate')?>")) return false;
	if (!valEmpty("end_date", "<?=getLabel('strEnter').getLabel('strEndDate')?>")) return false;
	if (!valOption("report_type", "<?=getLabel('strSelect').getLabel('strReportType')?>")) return false;
	if (!valOption("sel_city", "<?=getLabel('strSelect').getLabel('strCity')?>")) return false;
	if (!valOption("program_type", "<?=getLabel('strSelect').getLabel('strProgramType')?>")) return false;
	return true;
}
//-->
</script>
<form action="<?=setPage($page, $cat, 0, ACT_REPORT)?>" method="post" name="PageFilter" id="PageFilter" onsubmit="return fCheck(this);">
<table summary="filter" class="form">
<tr>
	<td><label for="start_date" class="dte"><?=getLabel('strStartDate').formatVal()?></label><br />
	<input type="text" name="start_date" id="start_date" maxlength="10" class="flddate"
	       value="<?=$dStartDateToDisplay?>" 
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" /></td>
	<td><label for="end_date" class="dte"><?=getLabel('strEndDate')?></label><br />
	<input type="text" name="end_date" id="end_date" maxlength="10" class="flddate"
	       value="<?=$dEndDateToDisplay?>" 
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" /></td>
	<td><label for="report_type"><?=getLabel('strReportType').formatVal()?></label><br />
	<select name="report_type" id="report_type" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aReportTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('report_type'))
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select></td>
	<td><label for="sel_city"><?=getLabel('strCity').formatVal()?></label><br />
	<select name="sel_city" id="sel_city" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aCities as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('sel_city', $nDefCity))
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select></td>
	<td><label for="program_type"><?=getLabel('strProgramType').formatVal()?></label><br />
	<select name="program_type" id="program_type" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aProgramTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('program_type', $cat))
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select></td>
	<td><label for="parent_page"><?=getLabel('strParentPage')?></label><br />
	<select name="parent_page" id="parent_page" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aPages as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('parent_page'))
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select></td>
	<td><br /><input type="submit" value="<?=getLabel('strGenerateGrid')?>" class="btnfilter" /></td>
</tr>
</table>
</form>
<?
//=========================================================
$aFilter = array('parent_page', 'start_date', 'end_date', 'program_type', 'premiere_type', 'report_type', 'sel_city', ARG_CAT);

switch($nSelReportType)
{
//===============================================================================================================
	case 1: // weekly by place
		$rsPlace = $oProgram->ListAllPlaces(getRequestArg('parent_page', null), getRequestArg('program_type', $cat),
					getRequestArg('sel_city', $nDefCity), '', '',
					$dStartDateToGo, $dEndDateToGo,
					$dStartTime, $dEndTime, getRequestArg('premiere_type', false));
		if (mysql_num_rows($rsPlace))
		{
			$aEvents = array();
			$aPlaces = array();
			$aFestivals = array();
			$aDates = array();
			while($row=mysql_fetch_object($rsPlace))
			{
				if (!empty($row->PlaceID))
				{
					if (!in_array($row->PlaceID, $aPlaces))
					{
						$aPlaces[] = $row->PlaceID;
						$aFestivals = array();
						$aEvents = array();
						$aDates = array();
					
					$sAddress = '';
					$rsAddress = $oAddress->ListAll($row->PlaceID, $aEntityTypes[ENT_PLACE], 1); //$row->PlaceTypeID
					if(mysql_num_rows($rsAddress))
					{
						while($rAddress = mysql_fetch_object($rsAddress))
						{
							$sAddress .= $rAddress->Street.'<br />';
						}
					}
					
					$sPhone = '';
					$rsPhone = $oPhone->ListAll($row->PlaceID, $aEntityTypes[ENT_PLACE], array(1,2,3,6)); //$row->PlaceTypeID
					if(mysql_num_rows($rsPhone))
					{
						$aPhoneTypes = getLabel('aPhoneTypes');
						while($rPhone = mysql_fetch_object($rsPhone))
						{
							if (!empty($sPhone))
								$sPhone .= ', ';
							$sPhone .= '('.$rPhone->Area.') '.$rPhone->Phone.IIF(!empty($rPhone->Ext), ' '.$rPhone->Ext, '');
						}
					}
					
					$sListContent .= '<br />
					<div class="main">'.stripComments($row->Title).'</div>
					<div>'.IIF(!empty($sAddress), $sAddress, '').
						IIF(!empty($sPhone), $sPhone.'<br />', '').
						IIF(!empty($row->WorkingTime), $row->WorkingTime, '').'</div>'."\n";
					}
					if (!empty($row->FestivalID) && !in_array($row->FestivalID, $aFestivals))
					{
						$aFestivals[] = $row->FestivalID;
						$aEvents = array();
						$aDates = array();
						$rFestival = $oFestival->GetByID($row->FestivalID);
						$sListContent .= '<br /><div class="main">'.stripComments($rFestival->Title).'</div>'."\n";
					}
					if (!empty($row->PremiereTypeID))
						$sListContent .= '<em>'.$aPremiereTypes[$row->PremiereTypeID].'</em> ';
					
					if (!empty($row->EventID) && !in_array($row->EventID, $aEvents))
					{
						$aEvents[] = $row->EventID;
						$aDates = array();
						$rEvent = $oEvent->GetByID($row->EventID);
						$sListContent .= '<strong>'.stripComments($rEvent->Title).'</strong><br />';
						if ($row->ProgramTypeID != 16) // film screening
						{
							$sListContent .= IIF(!empty($rEvent->Features), $rEvent->Features.'<br />', '').
									IIF(!empty($rEvent->Comment), $rEvent->Comment.'<br />', '').
									IIF(!empty($rEvent->Lead), $rEvent->Lead.'<br />', '').
									IIF(!empty($rEvent->Description), $rEvent->Description.'<br />', '');
						}
					}
					
					$sRelPlaces = '';
					$aRelPlaces = $oProgram->ListProgramPlacesAsArray($row->MainProgramID);
					if (is_array($aRelPlaces) && count($aRelPlaces)>0)
					{
						if (!empty($sRelPlaces)) $sRelPlaces .= ' - ';
						$aPlacesToShow = $oPlace->ListByIDsAsArray($aRelPlaces);
						foreach($aPlacesToShow as $key=>$val)
						{
							$sRelPlaces .= ''.stripComments($val).' ';
						}
						/*foreach($aRelPlaces as $key=>$val)
						{
							$rRelPlace = $oPlace->GetByID($val);
							$sRelPlaces .= ''.stripComments($rRelPlace->Title).' ';
						}*/
						if (!empty($sRelPlaces))
							$sRelPlaces = '<div><em>'.getLabel('strGuest').'</em> '.$sRelPlaces .'</div>'."\n";
					}
					if (!empty($sRelPlaces))
						$sListContent .= $sRelPlaces;
					
					$sRelEvents = '';
					$aRelEvents = $oProgram->ListProgramEventsAsArray($row->MainProgramID);
					if (is_array($aRelEvents) && count($aRelEvents)>0)
					{
						if (!empty($sRelEvents))
							$sRelEvents .= '';
						else
							$sRelEvents .= '<br />';
						$aEventsToShow = $oEvent->ListByIDsAsArray($aRelEvents);
						foreach($aEventsToShow as $key=>$val)
						{
							$sRelEvents .= ' &middot; <strong>'.stripComments($val).'</strong> ';
						}
						/*foreach($aRelEvents as $key=>$val)
						{
							$rRelEvent = $oEvent->GetByID($val);
							$sRelEvents .= '<strong>'.stripComments($rRelEvent->Title).'</strong> ';
						}*/
					}
					
					if (!empty($sRelEvents))
						$sListContent .= $sRelEvents.'<br />';
					
					if(!empty($row->PlaceHallID))
					{
						$rHall = $oPlaceHall->GetByID($row->PlaceHallID);
						$sListContent .= $rHall->Title.', ';
					}
					if(!empty($row->StartDate) && $row->StartDate!=DEFAULT_DATE_DB_VALUE)
					{
						$sListContent .= formatDate($row->StartDate, SHORT_DATE_DISPLAY_FORMAT, DEFAULT_DATE_DB_VALUE, 2).' - '.formatDate($row->EndDate, SHORT_DATE_DISPLAY_FORMAT, DEFAULT_DATE_DB_VALUE, 2);
					}
				
					$rsDates = $oProgramDateTime->ListAll($row->MainProgramID, $dStartDateToGo, $dEndDateToGo, $dStartTime, $dEndTime);
					if ($rsDates)
					{
						//$sListContent .= '<br />';
						$sDateInfo = '';
						while($rDate = mysql_fetch_object($rsDates))
						{
							if (!in_array($rDate->ProgramDate, $aDates))
							{
								$aDates[] = $rDate->ProgramDate;
								if ($row->ProgramTypeID != 16) // film screening
								{
									if (!empty($sDateInfo))
										$sDateInfo .= ', ';
								}
								else
								{
									if (!empty($sDateInfo))
										$sDateInfo .= '<br />';
								}
								$sDateInfo .= formatDate($rDate->ProgramDate, SHORT_DATE_DISPLAY_FORMAT, DEFAULT_DATE_DB_VALUE, 2);
							}
							$sDateInfo .= ' '.formatTime($rDate->ProgramTime); //', '
							$sDateInfo .= IIF(!empty($rDate->Price), ', '.$rDate->Price.' '.getLabel('strLv'), '');//getLabel('strPrice').'<br />';
						}
						$sListContent .= $sDateInfo.'<br />';
					}
					// PROGRAM NOTE COMES HERE
					$rNote = $oProgramNote->GetByProgramID($row->MainProgramID);
					if ($rNote && !empty($rNote->Title))
						$sListContent .= $rNote->Title.'<br />'."\n";
				}
			}
		}
		else
		{
			$sListContent .= '<div>'.getLabel('strNoRecords').'</div>'."\n";
		}
		echo $sListContent;
		break;
//===============================================================================================================
	case 2: // weekly by event
		$rsEvent = $oProgram->ListAllEventsByFestival(getRequestArg('parent_page', null), getRequestArg('program_type', $cat),
					getRequestArg('sel_city', $nDefCity), '', '',
					$dStartDateToGo, $dEndDateToGo,
					$dStartTime, $dEndTime);
		if (mysql_num_rows($rsEvent))
		{
			$aFestivals = array();
			$aEvents = array();
			$aPlaces = array();
			$aDates = array();
			//$aEventSubtypes = getLabel('aEventSubtypes');
			//$aOrigLanguages = getLabel('aOrigLanguages');
			//$aTranslations = getLabel('aTranslations');
			$sListContent .= '<div>';
			while($row=mysql_fetch_object($rsEvent))
			{
				if (!empty($row->EventID))
				{
					if (!empty($row->FestivalID) && !in_array($row->FestivalID, $aFestivals))
					{
						$sListContent .= '</div>';
						$aFestivals[] = $row->FestivalID;
						$rFestival = $oFestival->GetByID($row->FestivalID);
						$sListContent .= '<br /><br />
							<div class="fest">'.stripComments($rFestival->Title).'</div>'."\n";
						$aEvents = array();
						$aPlaces = array();
						$aDates = array();
						$sListContent .= '<div>';
					}
					if (!in_array($row->EventID, $aEvents))
					{
						$sListContent .= '</div><br /><div'.IIF(!empty($row->PremiereTypeID), ' class="prem"', '').'>';
						$aEvents[] = $row->EventID;
						$aPlaces = array();
						$aDates = array();
						
						$sCategory = '';
						if (!empty($row->EventSubtypeID))
						{
							if (!empty($sCategory)) $sCategory .= ', ';
							$sCategory .= $aEventSubtypes[$row->EventSubtypeID];
						}
						/*if (!empty($row->OriginalLanguageID))
						{
							if (!empty($sCategory)) $sCategory .= ', ';
							$sCategory .= $aOrigLanguages[$row->OriginalLanguageID];
						}
						if (!empty($row->TranslationID))
						{
							if (!empty($sCategory)) $sCategory .= ', ';
							$sCategory .= $aTranslations[$row->TranslationID];
						}*/
						
						if ($row->ProgramTypeID != PROGRAM_LIVE_MUSIC) // live
							$sListContent .= '<div class="main">'.stripComments($row->Title).'</div>'.
								IIF(!empty($row->OriginalTitle), $row->OriginalTitle.'<br />', '').
								IIF(!empty($row->Features), $row->Features.', ', '').
								IIF(!empty($sCategory), $sCategory.', ', '').
								IIF(!empty($row->Comment), $row->Comment.'<br />', '').
								IIF(!empty($row->Lead), $row->Lead.'<br />', '').
								IIF(!empty($row->Description), $row->Description.'<br />', '')."\n";
						else
							$sListContent .= '<span class="main">'.stripComments($row->Title).'</span>'."\n";
							
						if (!empty($row->PremiereTypeID))
						{
							$sListContent .= '<em>'.$aPremiereTypes[$row->PremiereTypeID].'</em> ';
						}
					}
					
					$sRelEvents = '';
					$aRelEvents = $oProgram->ListProgramEventsAsArray($row->MainProgramID);
					if (is_array($aRelEvents) && count($aRelEvents)>0)
					{
						//if (!empty($sRelEvents)) $sRelEvents .= ' - ';
						$aEventsToShow = $oEvent->ListByIDsAsArray($aRelEvents);
						foreach($aEventsToShow as $key=>$val)
						{
							$sRelEvents .= ' &middot; <em>'.stripComments($val).'</em> ';
						}
						/*foreach($aRelEvents as $key=>$val)
						{
							$rRelEvent = $oEvent->GetByID($val);
							$sRelEvents .= ' &middot; <em>'.stripComments($rRelEvent->Title).'</em> ';
						}*/
						if (!empty($sRelEvents))
							$sListContent .= ' '.$sRelEvents.''."\n";//.'<br />'
					}
					if ($row->ProgramTypeID == PROGRAM_LIVE_MUSIC) // live
						$sListContent .= '<br />'."\n";
					
					if (!in_array($row->PlaceID, $aPlaces))
					{
						if (count($aPlaces)>0)
							$sListContent .= ', ';
						$aPlaces[] = $row->PlaceID;
						$aDates = array();
						//$rPlace = $oPlace->GetByID($row->PlaceID);
						//$sListContent .= '<strong>'.stripComments(IIF(!empty($rPlace->ShortTitle), $rPlace->ShortTitle, $rPlace->Title)).'</strong>';//.', ';
						$sListContent .= '<strong>'.stripComments(IIF(!empty($row->ShortTitle), $row->ShortTitle, $row->PlaceTitle)).'</strong>';//;.', '
					}
					
					$sRelPlaces = '';
					$aRelPlaces = $oProgram->ListProgramPlacesAsArray($row->MainProgramID);
					if (is_array($aRelPlaces) && count($aRelPlaces)>0)
					{
						if (!empty($sRelPlaces)) $sRelPlaces .= ' - ';
						$aPlacesToShow = $oPlace->ListByIDsAsArray($aRelPlaces);
						foreach($aPlacesToShow as $key=>$val)
						{
							$sRelPlaces .= ''.stripComments($val).' ';
						}
						/*foreach($aRelPlaces as $key=>$val)
						{
							$rRelPlace = $oPlace->GetByID($val);
							$sRelPlaces .= stripComments($rRelPlace->Title).' ';
						}*/
						if (!empty($sRelPlaces))
							$sListContent .= ' <em>'.getLabel('strGuest').'</em> '.$sRelPlaces."\n";//.'<br />'
					}
					// dates for live music
					if ($row->ProgramTypeID == PROGRAM_LIVE_MUSIC) // live
					{
						$rsDates = $oProgramDateTime->ListAll($row->MainProgramID, $dStartDateToGo, $dEndDateToGo, $dStartTime, $dEndTime);
						if ($rsDates)
						{
							$sListContent .= '<br />';
							$sDateInfo = '';
							while($rDate = mysql_fetch_object($rsDates))
							{
								if (!in_array($rDate->ProgramDate, $aDates))
								{
									$aDates[] = $rDate->ProgramDate;
									if ($row->ProgramTypeID != 16) // film screening
									{
										if (!empty($sDateInfo))
											$sDateInfo .= ', ';
									}
									else
									{
										if (!empty($sDateInfo))
											$sDateInfo .= '<br />';
									}
									$sDateInfo .= ' '.formatDate($rDate->ProgramDate, SHORT_DATE_DISPLAY_FORMAT, DEFAULT_DATE_DB_VALUE, 2);
								}
								$sDateInfo .= ' '.formatTime($rDate->ProgramTime); //', '
								$sDateInfo .= IIF(!empty($rDate->Price), ', '.$rDate->Price.' '.getLabel('strLv'), '');//getLabel('strPrice').'<br />';
							}
							$sListContent .= $sDateInfo.'<br />';
						}
					}
				}
			}
			$sListContent .= '</div>';
		}
		else
		{
			$sListContent .= '<div>'.getLabel('strNoRecords').'</div>'."\n";
		}
		echo $sListContent;
		break;
//===============================================================================================================
	case 3: // daily by place
		$rsPlace = $oProgram->ListAllPlaces(getRequestArg('parent_page', null), getRequestArg('program_type', $cat),
					getRequestArg('sel_city', $nDefCity), '', '',
					$dStartDateToGo, $dEndDateToGo,
					$dStartTime, $dEndTime, getRequestArg('premiere_type', false));
		if (mysql_num_rows($rsPlace))
		{
			$aEvents = array();
			$aPlaces = array();
			$aHalls = array();
			$aDates = array();
			while($row=mysql_fetch_object($rsPlace))
			{
				if (!empty($row->PlaceID))
				{
					if (!in_array($row->PlaceID, $aPlaces))
					{
						$aPlaces[] = $row->PlaceID;
						$aHalls = array();
						$aEvents = array();
						$aDates = array();
						//<br />
						$sListContent .= '<br />
						<span class="main">'.stripComments($row->Title).'</span> '."\n";
					}
					if ($row->ProgramTypeID == PROGRAM_FILM_SCREENING) // film screening
					{
						if(!empty($row->PlaceHallID))
						{
							if (!in_array($row->PlaceHallID, $aHalls))
							{
								$aHalls[] = $row->PlaceHallID;
								$aEvents = array();
								$aDates = array();
								$rHall = $oPlaceHall->GetByID($row->PlaceHallID);
								$sListContent .= '<br />'.$rHall->Title.' ';
							}
						}
					}
					
					if (!empty($row->EventID) && !in_array($row->EventID, $aEvents))
					{
						if ($row->ProgramTypeID != PROGRAM_LIVE_MUSIC) // live
							$sListContent .= '<br />';
						if (!empty($row->PremiereTypeID))
							$sListContent .= '<em>'.$aPremiereTypes[$row->PremiereTypeID].'</em> ';
						$aEvents[] = $row->EventID;
						$aDates = array();
						$rEvent = $oEvent->GetByID($row->EventID);
						$sListContent .= '<strong>'.stripComments($rEvent->Title).'</strong> ';
					}
					
					$sRelPlaces = '';
					$aRelPlaces = $oProgram->ListProgramPlacesAsArray($row->MainProgramID);
					if (is_array($aRelPlaces) && count($aRelPlaces)>0)
					{
						//if (!empty($sRelPlaces))
						//	$sRelPlaces .= ' &middot; ';
						$aPlacesToShow = $oPlace->ListByIDsAsArray($aRelPlaces);
						foreach($aPlacesToShow as $key=>$val)
						{
							$sRelPlaces .= ''.stripComments($val).' ';
						}
						/*foreach($aRelPlaces as $key=>$val)
						{
							$rRelPlace = $oPlace->GetByID($val);
							$sRelPlaces .= ''.stripComments($rRelPlace->Title).' ';
						}*/
						if (!empty($sRelPlaces))
							$sRelPlaces = '<em>'.getLabel('strGuest').'</em> '.$sRelPlaces .' '."\n";
					}
					if (!empty($sRelPlaces))
						$sListContent .= $sRelPlaces;
					
					$sRelEvents = '';
					$aRelEvents = $oProgram->ListProgramEventsAsArray($row->MainProgramID);
					if (is_array($aRelEvents) && count($aRelEvents)>0)
					{
						//if (!empty($sRelEvents))
						//	$sRelEvents .= ' &middot; ';
						$aEventsToShow = $oEvent->ListByIDsAsArray($aRelEvents);
						foreach($aEventsToShow as $key=>$val)
						{
							$sRelEvents .= ' &middot; <em>'.stripComments($val).'</em> ';
						}
						/*foreach($aRelEvents as $key=>$val)
						{
							$rRelEvent = $oEvent->GetByID($val);
							$sRelEvents .= ' &middot; <em>'.stripComments($rRelEvent->Title).'</em> ';
						}*/
					}
					if (!empty($sRelEvents))
						$sListContent .= $sRelEvents.' ';
					
					
					if(!empty($row->StartDate) && $row->StartDate!=DEFAULT_DATE_DB_VALUE)
					{
						$sListContent .= formatDate($row->StartDate, SHORT_DATE_DISPLAY_FORMAT, DEFAULT_DATE_DB_VALUE, 2).' - '.formatDate($row->EndDate, SHORT_DATE_DISPLAY_FORMAT, DEFAULT_DATE_DB_VALUE, 2);
					}
					
					$rsDates = $oProgramDateTime->ListAll($row->MainProgramID, $dStartDateToGo, $dEndDateToGo, $dStartTime, $dEndTime);
					if ($rsDates)
					{
						$sDateInfo = '';
						while($rDate = mysql_fetch_object($rsDates))
						{
							if (!in_array($rDate->ProgramDate, $aDates))
							{
								if ($dStartDateToGo != $dEndDateToGo)
								{
									$aDates[] = $rDate->ProgramDate;
									if (!empty($sDateInfo))
										$sDateInfo .= ', ';
									$sDateInfo .= formatDate($rDate->ProgramDate, SHORT_DATE_DISPLAY_FORMAT, DEFAULT_DATE_DB_VALUE, 2);
								}
							}
							if (!empty($sDateInfo))
								$sDateInfo .= ', ';
							$sDateInfo .= formatTime($rDate->ProgramTime);
							//$sDateInfo .= IIF(!empty($rDate->Price), ' '.$rDate->Price.' '.getLabel('strLv'), '');//.' '.getLabel('strPrice').'<br />';
						}
						$sListContent .= $sDateInfo.' ';//'<br />';
					}
					// PROGRAM NOTE COMES HERE
					/*$rNote = $oProgramNote->GetByProgramID($row->MainProgramID);
					if ($rNote && !empty($rNote->Title))
						$sListContent .= $rNote->Title.'<br />'."\n";*/
				}
			}
		}
		else
		{
			$sListContent .= '<div>'.getLabel('strNoRecords').'</div>'."\n";
		}
		echo $sListContent;
		break;
//===============================================================================================================
	case 4: // daily by event
		$rsEvent = $oProgram->ListAllEventsByFestival(getRequestArg('parent_page', null), getRequestArg('program_type', $cat),
					getRequestArg('sel_city', $nDefCity), '', '',
					$dStartDateToGo, $dEndDateToGo,
					$dStartTime, $dEndTime);
		if (mysql_num_rows($rsEvent))
		{
			$aFestivals = array();
			$aEvents = array();
			$aPlaces = array();
			$aDates = array();
			//$aEventSubtypes = getLabel('aEventSubtypes');
			//$aOrigLanguages = getLabel('aOrigLanguages');
			//$aTranslations = getLabel('aTranslations');
			$sListContent .= '<div>';
			while($row=mysql_fetch_object($rsEvent))
			{
				if (!empty($row->EventID))
				{
					if (!empty($row->FestivalID) && !in_array($row->FestivalID, $aFestivals))
					{
						$sListContent .= '</div>';
						$aFestivals[] = $row->FestivalID;
						$rFestival = $oFestival->GetByID($row->FestivalID);
						$sListContent .= '<br /><br />
							<div class="fest">'.stripComments($rFestival->Title).'</div>'."\n";
						$aEvents = array();
						$aPlaces = array();
						$aDates = array();
						$sListContent .= '<div>';
					}
					if (!in_array($row->EventID, $aEvents))
					{
						$aEvents[] = $row->EventID;
						$aPlaces = array();
						$aDates = array();
						
						$sCategory = '';
						if (!empty($row->EventSubtypeID))
						{
							if (!empty($sCategory)) $sCategory .= ', ';
							$sCategory .= $aEventSubtypes[$row->EventSubtypeID];
						}
						/*if (!empty($row->OriginalLanguageID))
						{
							if (!empty($sCategory)) $sCategory .= ', ';
							$sCategory .= $aOrigLanguages[$row->OriginalLanguageID];
						}
						if (!empty($row->TranslationID))
						{
							if (!empty($sCategory)) $sCategory .= ', ';
							$sCategory .= $aTranslations[$row->TranslationID];
						}*/
						//<br />
						$sListContent .= '<br /><br />
							<span class="main">'.stripComments($row->Title).'</span>'."\n";
						
						if (!empty($row->PremiereTypeID))
						{
							$sListContent .= ' <em>'.$aPremiereTypes[$row->PremiereTypeID].'</em> ';
						}	
					}
					
					$sRelEvents = '';
					$aRelEvents = $oProgram->ListProgramEventsAsArray($row->MainProgramID);
					if (is_array($aRelEvents) && count($aRelEvents)>0)
					{
						//if (!empty($sRelEvents)) $sRelEvents .= ' - ';
						$aEventsToShow = $oEvent->ListByIDsAsArray($aRelEvents);
						foreach($aEventsToShow as $key=>$val)
						{
							$sRelEvents .= ' &middot; <em>'.stripComments($val).'</em> ';
						}
						/*foreach($aRelEvents as $key=>$val)
						{
							$rRelEvent = $oEvent->GetByID($val);
							$sRelEvents .= '<em>'.stripComments($rRelEvent->Title).'</em> ';
						}*/
						if (!empty($sRelEvents))
							$sListContent .= $sRelEvents.' ';//.'<br />'."\n";
					}
					
					$sRelPlaces = '';
					$aRelPlaces = $oProgram->ListProgramPlacesAsArray($row->MainProgramID);
					if (is_array($aRelPlaces) && count($aRelPlaces)>0)
					{
						//if (!empty($sRelPlaces)) $sRelPlaces .= ' - ';
						/*$aPlacesToShow = $oPlace->ListByIDsAsArray($aRelPlaces);
						foreach($aPlacesToShow as $key=>$val)
						{
							$sRelPlaces .= ''.stripComments($val).' ';
						}*/
						/*foreach($aRelPlaces as $key=>$val)
						{
							$rRelPlace = $oPlace->GetByID($val);
							$sRelPlaces .= stripComments($rRelPlace->Title).', ';
						}*/
						/*if (!empty($sRelPlaces))
							$sListContent .= getLabel('strGuest').' '.$sRelPlaces.' ';//.'<br />'."\n";*/
						$sListContent .= '<em>'.getLabel('strGuest').'</em> '."\n";
					}
					
					if (!in_array($row->PlaceID, $aPlaces))
					{
						//if ($row->ProgramTypeID == PROGRAM_FILM_SCREENING) // film screening
							$sListContent .= '<br />';//'';
						//elseif (count($aPlaces)>0)
						//	$sListContent .= ', ';
						$aPlaces[] = $row->PlaceID;
						$aDates = array();
						$rPlace = $oPlace->GetByID($row->PlaceID);
						
						$sListContent .= stripComments(IIF(!empty($rPlace->ShortTitle), $rPlace->ShortTitle, $rPlace->Title)).' ';//.', ';
					}
					
					if ($row->ProgramTypeID == PROGRAM_FILM_SCREENING) // film screening
					{
						if(!empty($row->PlaceHallID))
						{
							$rHall = $oPlaceHall->GetByID($row->PlaceHallID);
							$sListContent .= $rHall->Title.', ';
						}
					}
					if(!empty($row->StartDate) && $row->StartDate!=DEFAULT_DATE_DB_VALUE)
					{
						$sListContent .= formatDate($row->StartDate, SHORT_DATE_DISPLAY_FORMAT, DEFAULT_DATE_DB_VALUE, 2).' - '.formatDate($row->EndDate, SHORT_DATE_DISPLAY_FORMAT, DEFAULT_DATE_DB_VALUE, 2);
					}
					$rsDates = $oProgramDateTime->ListAll($row->MainProgramID, $dStartDateToGo, $dEndDateToGo, $dStartTime, $dEndTime);
					if ($rsDates)
					{
						$sDateInfo = '';
						while($rDate = mysql_fetch_object($rsDates))
						{
							if (!in_array($rDate->ProgramDate, $aDates))
							{
								if ($dStartDateToGo != $dEndDateToGo)
								{
									$aDates[] = $rDate->ProgramDate;
									if (!empty($sDateInfo))
										$sDateInfo .= ', ';
									$sDateInfo .= formatDate($rDate->ProgramDate, SHORT_DATE_DISPLAY_FORMAT, DEFAULT_DATE_DB_VALUE, 2);
								}
							}
							if (!empty($sDateInfo))
								$sDateInfo .= ', ';
							$sDateInfo .= formatTime($rDate->ProgramTime);
							//$sDateInfo .= IIF(!empty($rDate->Price), ' '.$rDate->Price.' '.getLabel('strLv'), '');//.' '.getLabel('strPrice').'<br />';
						}
						$sListContent .= $sDateInfo.' ';//'<br />';
					}
					// PROGRAM NOTE COMES HERE
					/*$rNote = $oProgramNote->GetByProgramID($row->MainProgramID);
					if ($rNote && !empty($rNote->Title))
						$sListContent .= $rNote->Title.'<br />'."\n";*/
				}
			}
			$sListContent .= '</div>';
		}
		else
		{
			$sListContent .= '<div>'.getLabel('strNoRecords').'</div>'."\n";
		}
		echo $sListContent;
		break;
//===============================================================================================================
}
?>