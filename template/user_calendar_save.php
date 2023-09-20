<?php
if (!$bInSite) die();
//=========================================================
	$nUserID = $oSession->GetValue(SS_USER_ID);
	//print_r($_POST);
	switch($action)
	{
		case ACT_CAL:
			$sMsg = '';
			$nRelID = 0;
			if (!empty($nUserID) && count($_POST)>0)
			{
				$text = strShorten(getPostedArg('cal_note'), TEXT_LEN_EMAIL);
				$start_date = parseDate(getPostedArg('cal_start_date'));
				$end_date = parseDate(getPostedArg('cal_end_date'));

				//if end time is after start time, end time is set to start time
				if(strtotime($start_date) > strtotime($end_date)) $end_date = $start_date;
				if (prevent_multiple_submit())
					$nRelID = $oUser->InsertCalendarItem($nUserID,
									     getPostedArg(ARG_RELID),
									     $nEntityType, 
									     $text,
									     $start_date,
									     $end_date,
									     parseTime(getPostedArg('start_time')),
									     parseDate(getPostedArg('reminder_date')),
									     parseTime(getPostedArg('reminder_time')));
			}
			if (empty($nRelID))
				$sMsg .= getLabel('strSaveFailed'); // failed message
			else
			{
				$sMsg .= getLabel('strSaveOK').'<br />'; // ok message
			}
			//echo $sMsg;
			break;
		default:
			//
		break;
	}
//=========================================================
function displayCalendarForm($entity_id, $start_date, $end_date)
{
	global $page, $cat, $item;
	
	$sToReturn = '
	<script type="text/javascript" src="js/calendar.js"></script>
	<div class="formbox" id="formbox_save" style="display: none;">
		<div class="close"><a href="#">'.getLabel('close').'</a></div>
		<form method="post" action="'.setPage($page, $cat, $item, ACT_CAL).'" id="calendar_frm" name="calendar_frm">
		<h6>'.getLabel('strAddToCalendar').'</h6>
		<br />
		<p>'.getLabel('strCalendarIntro').'</p>
		<input type="hidden" name="'.ARG_RELID.'" id="'.ARG_RELID.'" value="'.$entity_id.'" />
		<div>
			<label for="cal_start_date">'.getLabel('strStartDate').formatVal().'</label><br />
			<input type="text" name="cal_start_date" id="cal_start_date" maxlength="10" class="fldfilter"
				   value="'.formatDate($start_date).'" 
				    onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" />
		</div>
		<div>
			<label for="cal_end_date">'.getLabel('strEndDate').'</label><br />
			<input type="text" name="cal_end_date" id="cal_end_date" maxlength="10" class="fldfilter"
				   value="'.formatDate($end_date).'" 
				    onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" />
		</div>
		<div>
			<label for="cal_note">'.getLabel('strNote').'</label><br />
			<input type="text" name="cal_note" id="cal_note" maxlength="255" class="fldfilter" />
		</div>
		<br class="clear" />

		<br />
		<input type="submit" value="'.getLabel('strDoAdd').'" class="btn" />
		<br class="clear" />
		</form>
	</div>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery("div.calendar_add").find("a").click(function() {
			jQuery("#formbox_save").slideDown("slow");
			return false;
		})
		jQuery("div.close").find("a").click(function() {
			jQuery("#formbox_save").slideUp("slow");
			return false;
		})
	})
	</script>'."\n";
	
	return $sToReturn;
}
//=========================================================
?>