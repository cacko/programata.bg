<?php
if (!$bInSite) die();
//=========================================================
	$nUserID = $oSession->GetValue(SS_USER_ID);
	//print_r($_POST);
	switch($action)
	{
		case ACT_SAVE:
			$sMsg = '';
			$nRelID = 0;
			$title = getPostedArg('title');
			$text = strShorten(getPostedArg('comment'), TEXT_LEN_EMAIL);
			if (!empty($nUserID) && count($_POST)>0)
			{
				if (prevent_multiple_submit())
					$nRelID = $oComment->Insert(	htmlspecialchars(strip_tags($title)), 
									htmlspecialchars(strip_tags($text)), 
									$item,
									$nEntityType); //getPostedArg('id')
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
?>