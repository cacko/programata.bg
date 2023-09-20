<?php
if (!$bInSite) die();
//=========================================================
	$nUserID = $oSession->GetValue(SS_USER_ID);
	//print_r($_POST);
	switch($action)
	{
		case ACT_VOTE:
			$sMsg = '';
			$nRelID = 0;
			$text = strShorten(getPostedArg('comment'), TEXT_LEN_EMAIL);
			if (!empty($nUserID) && count($_POST)>0)
			{
				if (prevent_multiple_submit())
					$nRelID = $oRate->Insert($item,
								$nEntityType,
								0,
								getPostedArg('item_rating'));
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
function displayRatingForm()
{
	global $page, $cat, $item;
	
	$sToReturn = '
	<div class="rate_box" style="display: none;">
		<div class="close"><a href="#">'.getLabel('close').'</a></div>
		<form method="post" action="'.setPage($page, $cat, $item, ACT_VOTE).'" id="vote_frm" name="vote_frm">
		<input type="hidden" name="'.ARG_ID.'" id="'.ARG_ID.'" value="'.$item.'" />'."\n";
	$aRating = getLabel('aRating');
	foreach($aRating as $k=>$v)
	{
		$sToReturn .= '<input type="radio" name="item_rating" id="rate_'.$k.'" value="'.$k.'" /><label for="rate_'.$k.'">'.$v.'</label>'."\n";
	}
	$sToReturn .= '<br /><br />
		<input type="submit" value="'.getLabel('strVote').'" class="btn" /><br />
		</form>
	</div>
	<script type="text/javascript">
	jQuery(document).ready(function(){
		jQuery("div.vote").find("a").click(function() {
			jQuery("div.rate_box").slideDown("slow");
			return false;
		})
		jQuery("div.close").find("a").click(function() {
			jQuery("div.rate_box").slideUp("slow");
			return false;
		})
	})
	</script>'."\n";
	
	return $sToReturn;
}
//=========================================================
?>