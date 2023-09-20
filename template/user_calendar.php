<?
if (!$bInSite) die();

//=========================================================
	$nUserID = $oSession->GetValue(SS_USER_ID);
	//print_r($_POST);
	switch($action)
	{
		case ACT_CAL_UPD:
			$nRelID = 0;
			if (!empty($nUserID) && count($_POST)>0)
			{
				$text = strShorten(getPostedArg('cal_note'), TEXT_LEN_EMAIL);
				$start_date = parseDate(getPostedArg('cal_start_date'));
				$end_date = parseDate(getPostedArg('cal_end_date'));

				//if end time is after start time, end time is set to start time
				if(strtotime($start_date) > strtotime($end_date)) $end_date = $start_date;

				if (prevent_multiple_submit())
				{
					$nRelID = $oUser->UpdateCalendarItem(getPostedArg('rid'),
									     $text,
									     $start_date,
									     $end_date,
									     parseTime(getPostedArg('start_time')),
									     parseDate(getPostedArg('reminder_date')),
									     parseTime(getPostedArg('reminder_time')));
				}
			}
			break;
		case ACT_CAL_DEL:
			$nRelID = 0;
			if (!empty($nUserID) && count($_POST)>0)
			{
				if (prevent_multiple_submit())
				{
					$nRelID = $oUser->DeleteCalendarItem(getPostedArg('rid'));
				}
			}
			break;
		default:
			//
		break;
	}
//=========================================================
    // include calendar class:
    include('helper/cal/calendar.inc.php');
    
    if (!empty($nUserID))
    {
        $dToday = date(DEFAULT_DATE_DB_FORMAT);
        $dSelCalDate = getQueryArg('sel_date', ''); //sel_date $dSelStartDate in initialise is limited to current week only
        if (empty($dSelCalDate))
	    $dCalDate = $dToday;
        else
	    $dCalDate = $dSelCalDate;
?>
	<h4><?=getLabel('strCalendar')?></h4>
	<!--div class="text"><?=drawImage('img/calendar.png')?></div-->
        <div id="calendar">
            <div class="day">
                <div id="date"><?=formatDate($dCalDate, 'j');?></div>
                <div id="month"><?=formatDate($dCalDate, 'F');?></div>
                <div id="day"><?=formatDate($dCalDate, 'l');?></div>
                <div id="prev-year"><a href="<?=setPage($page, $cat, $item).'&amp;sel_date='.increaseDate($dCalDate, 0, -12)?>" title="<?=getLabel('strPrev')?>"><?=drawImage('img/pix.png', 0, 0, getLabel('strPrev'));?></a></div>
                <div id="next-year"><a href="<?=setPage($page, $cat, $item).'&amp;sel_date='.increaseDate($dCalDate, 0, 12)?>" title="<?=getLabel('strNext')?>"><?=drawImage('img/pix.png', 0, 0, getLabel('strNext'));?></a></div>
                <div id="year"><?=formatDate($dCalDate, 'Y');?></div>
            </div>
            <div class="cal">
                <div id="prev-month"><a href="<?=setPage($page, $cat, $item).'&amp;sel_date='.increaseDate($dCalDate, 0, -1)?>" title="<?=getLabel('strPrev')?>"><?=drawImage('img/pix.png', 0, 0, getLabel('strPrev'));?></a></div>
                <div id="next-month"><a href="<?=setPage($page, $cat, $item).'&amp;sel_date='.increaseDate($dCalDate, 0, 1)?>" title="<?=getLabel('strNext')?>"><?=drawImage('img/pix.png', 0, 0, getLabel('strNext'));?></a></div>
    <?
        $year = formatDate($dCalDate, 'Y'); //set year to current year
        $month = formatDate($dCalDate, 'n'); // set month to current month
        $day = formatDate($dCalDate, 'j');
        // create calendar:
        $cal = new CALENDAR($year, $month, $day);
        $cal->link = setPage($page, $cat, $item);//$_SERVER['PHP_SELF']
        
        // show personal reminders
        $dCalStart = formatDate($dCalDate, 'Y-m-').'01';
        $dCalEnd = increaseDate($dCalStart, -1, 1);
        //echo $dCalStart.'-'.$dCalEnd;
        $rsItems = $oUser->ListCalendarItems($nUserID, $dCalStart, $dCalEnd);
        if (mysql_num_rows($rsItems) > 0)
        {
            while($row = mysql_fetch_object($rsItems))
            {
                $nStart = formatDate($row->StartDate, 'j');
                $nEnd = formatDate($row->EndDate, 'j');
//				if(strtotime($dCalDate) > strtotime($row->EndDate)) continue;
                //echo $nStart.'-'.$nEnd;
                //$cal->viewEvent($nStart, $nEnd, "#D0FFD0", $row->Note, 'http://www.programata.bg/');
                $cal->viewEvent($nStart, $nEnd, "#E3E3E3", $row->Note);
            }
        }
        echo $cal->create();
        
    ?>
            </div>
        <br class="clear" />
        </div>
    <?
        if (mysql_num_rows($rsItems) > 0)
        {
            mysql_data_seek($rsItems, 0);
            echo '<div id="calendar_items">
                <ul>'."\n";
            $bIsFirst = true;
            while($row2 = mysql_fetch_object($rsItems))
            {
				//previous events are not listed
				if(strtotime($dCalDate) > strtotime($row2->EndDate)) continue;

				//$dStartDate = formatDate($row2->StartDate, DEFAULT_DATE_DISPLAY_FORMAT);
                //$dEndDate = formatDate($row2->EndDate, DEFAULT_DATE_DISPLAY_FORMAT);
                switch($row2->EntityTypeID)
                {
                    case $aEntityTypes[ENT_NEWS]:
                        $rItem = $oNews->GetByID($row2->EntityID);
                        $sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC));
                        $aRelPages = $oNews->ListNewsPagesAsArray($row2->EntityID);
                        break;
                    case $aEntityTypes[ENT_PUBLICATION]:
                        $rItem = $oPublication->GetByID($row2->EntityID); //'<strong>'.$rItem->Subtitle.'</strong><br />'.
                        $sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC));
                        $aRelPages = $oPublication->ListPublicationPagesAsArray($row2->EntityID);
                        break;
                    case $aEntityTypes[ENT_FESTIVAL]:
                        $rItem = $oFestival->GetByID($row2->EntityID);
                        $sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Content, TEXT_LEN_PUBLIC));
                        $aRelPages = $oFestival->ListFestivalPagesAsArray($row2->EntityID);
                        break;
                    case $aEntityTypes[ENT_PLACE]:
                        $rItem = $oPlace->GetByID($row2->EntityID);
                        $sEntityText = IIF(!empty($rItem->Description), strShorten($rItem->Description, TEXT_LEN_PUBLIC), '');
                        $aRelPages = $oPlace->ListPlacePagesAsArray($row2->EntityID);
                        break;
                    case $aEntityTypes[ENT_EVENT]:
                        $rItem = $oEvent->GetByID($row2->EntityID);
                        $sEntityText = IIF(!empty($rItem->Lead), strShorten($rItem->Lead, TEXT_LEN_PUBLIC), strShorten($rItem->Description, TEXT_LEN_PUBLIC));
                        // list program pages
                        $dEndDate = increaseDate($dToday, 0, 1);
                        $rsProgram = $oProgram->ListAllByDate(null, $row->EntityID, null, null, increaseDate($row2->StartDate, -THIS_WEEK_DAYS), increaseDate($row2->EndDate, THIS_WEEK_DAYS));
                        $nProgramID = 0;
                        while($rPro = mysql_fetch_object($rsProgram))
                        {
                                $nProgramID = $rPro->MainProgramID;
                                continue;
                        }
                        $aRelPages = $oProgram->ListProgramPagesAsArray($nProgramID);
                        break;
                }
                $sEntityTitle = stripComments($rItem->Title);
                $nPageToGo = $nRootPage;
                if (is_array($aRelPages) && count($aRelPages)>0)
                    $nPageToGo = $aRelPages[0];
                
                $sDate = '';
                if ($row2->StartDate === $row2->EndDate)
                    $sDate .= formatDate($row2->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
                elseif (formatDate($row2->StartDate, FULL_MONTH_YEAR_DISPLAY_FORMAT) === formatDate($row2->EndDate, FULL_MONTH_YEAR_DISPLAY_FORMAT))
                    $sDate .= formatDate($row2->StartDate, DAY_DISPLAY_FORMAT).' - '.formatDate($row2->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
                else
                    $sDate .= formatDate($row2->StartDate, FULL_DATE_YEAR_DISPLAY_FORMAT).' - '.formatDate($row2->EndDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
                
                echo '<li'.IIF($bIsFirst, ' class="first"', '').'><div class="date">'.$sDate.'</div>
                    <a href="'.setPage($nPageToGo, 0, $row2->EntityID).'">'.$sEntityTitle.'</a>
                     '.IIF(!empty($row2->Note), '<br />'.$row2->Note, '').'</li>'."\n";

// update calendar start
				include_once ('user_calendar_edit.php');
				include_once ('user_calendar_del.php');
				echo '<div class="calendar_update">';
				echo '<a href="#"  id="calendar_update_'.$row2->UserCalendarID.'">'.getLabel('strUpdateCalendar').'</a>';
				echo '&nbsp <a href="#"  id="calendar_delete_'.$row2->UserCalendarID.'">'.getLabel('strDeleteCalendar').'</a>';
				echo '</div>'."\n";
				echo updateCalendarForm( $row2->UserCalendarID, $row2->StartDate, $row2->EndDate, $row2->Note);
				echo deleteCalendarForm( $row2->UserCalendarID);
// update calendar end
                $bIsFirst = false;
            }
            echo '</ul>
                </div>'."\n";
        }
    }
?>