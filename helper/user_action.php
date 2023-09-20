<?php
if (!$bInSite) die();
//=========================================================
	$sLoginMsg = '';
	$nCookieUser = getCookieArg(CC_NAME, 0);
	if (!empty($nCookieUser) && empty($action))
	{
		$action = ACT_AUTOLOGIN;
	}
	switch($action)
	{
		case ACT_AUTOLOGIN:
			$rUser = $oUser->GetByID($nCookieUser);
			if ($rUser)
			{
				$oSession->SetValue(SS_USER_ID, $rUser->UserID);
				$oSession->SetValue(SS_USER_STATUS, $rUser->UserStatus);
				//if (in_array($page, $aUserPages))
				//	$page = DEF_PAGE;
				$sLoginMsg = getLabel('strLoginOK');
				setcookie(CC_NAME, $rUser->UserID, time()+(3600*24*31), '/', 'programata.bg');
			}
			else
			{
				$sLoginMsg = getLabel('strInvalid');
			}
			break;
		case ACT_LOGIN:

			$rUser = $oUser->CanLogin(getPostedArg('username'),
						getPostedArg('password'));
			if ($rUser)
			{
				$oSession->SetValue(SS_USER_ID, $rUser->UserID);
				$oSession->SetValue(SS_USER_STATUS, $rUser->UserStatus);
				$oUser->TrackLogin($rUser->UserID);
				if (in_array($page, $aUserPages))
					$page = DEF_PAGE;
				$sLoginMsg = getLabel('strLoginOK');
				if (getPostedArg('remember') == B_TRUE)
				{
					setcookie(CC_NAME, $rUser->UserID, time()+(3600*24*31), '/', 'programata.bg');
				}
			}
			else
			{
				$sLoginMsg = getLabel('strInvalid');
			}
			break;
		case ACT_LOGOUT:
			$oSession->UnsetValue(SS_USER_ID);
			$oSession->UnsetValue(SS_USER_STATUS);
			if (in_array($page, $aUserPages))
				$page = DEF_PAGE;
			$sLoginMsg = getLabel('strLogoutOK');
			setcookie(CC_NAME, 0, time()-3600, '/', 'programata.bg');
			break;
		default:
			//$sLoginMsg = getLabel('strLoginIntro');
			break;
	}
?>