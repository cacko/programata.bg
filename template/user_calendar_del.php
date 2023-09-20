<?php
if (!$bInSite) die();

function deleteCalendarForm($cal_id)
{
	global $page, $cat, $item;
	
	$sToReturn = '
	<script type="text/javascript" src="js/calendar.js"></script>
	<div class="formbox" id="formbox_del'.$cal_id.'" style="display: none;">
		<div class="close"><a href="#" id="close_del'.$cal_id.'">'.getLabel('close').'</a></div>
		<form method="post" action="'.setPage($page, $cat, $item, ACT_CAL_DEL).'" id="calendar_frm" name="calendar_frm">
		<h6>'.getLabel('strDeleteQ').'</h6>
		<br />
		<input type="hidden" name="'.ARG_RELID.'" id="'.ARG_RELID.'" value="'.$cal_id.'" />
		<br class="clear" />
		<br />
		<input type="submit" value="'.getLabel('strDoDel').'" class="btn" />
		<br class="clear" />
		</form>
	</div>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery("#calendar_delete_'.$cal_id.'").click(function() {
			jQuery("#formbox_del'.$cal_id.'").slideDown("slow");
			return false;
		})
		jQuery("#close_del'.$cal_id.'").click(function() {
			jQuery("#formbox_del'.$cal_id.'").slideUp("slow");
			return false;
		})
	})
	</script>'."\n";
	
	return $sToReturn;
}
//=========================================================
?>