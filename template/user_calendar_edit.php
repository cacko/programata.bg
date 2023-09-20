<?php
if (!$bInSite) die();
function updateCalendarForm($cal_id, $start_date, $end_date, $note)
{
	global $page, $cat, $item;
	
	$sToReturn = '
	<script type="text/javascript" src="js/calendar.js"></script>
	<div class="formbox" id="formbox_'.$cal_id.'" style="display: none;">
		<div class="close"><a href="#" id="close_'.$cal_id.'">'.getLabel('close').'</a></div>
		<form method="post" action="'.setPage($page, $cat, $item, ACT_CAL_UPD).'" id="calendar_frm" name="calendar_frm">
		<h6>'.getLabel('strUpdateCalendar').'</h6>
		<br />
		<input type="hidden" name="'.ARG_RELID.'" id="'.ARG_RELID.'" value="'.$cal_id.'" />
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
			<input type="text" name="cal_note" id="cal_note" maxlength="255" class="fldfilter"
					value="'.$note.'"/>
		</div>
		<br class="clear" />
		<br />
		<input type="submit" value="'.getLabel('strSave').'" class="btn" />
		<br class="clear" />
		</form>
	</div>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery("#calendar_update_'.$cal_id.'").click(function() {
			jQuery("#formbox_'.$cal_id.'").slideDown("slow");
			return false;
		})
		jQuery("#close_'.$cal_id.'").click(function() {
			jQuery("#formbox_'.$cal_id.'").slideUp("slow");
			return false;
		})
	})
	</script>'."\n";
	
	return $sToReturn;
}
//=========================================================
?>