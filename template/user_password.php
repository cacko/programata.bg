<?
if (!$bInSite) die();
//=========================================================
?>
<div class="text">
<?
//=========================================================
	switch($action)
	{
		case ACT_SAVE:
			$sMsg = '';
			$nUserID = $oSession->GetValue(SS_USER_ID);
			if (!empty($nUserID) && is_numeric($nUserID))
			{
				$rUser = $oUser->GetByID($nUserID);
				if (md5(getPostedArg('old_password')) != $rUser->Password)
				{
					$sMsg = getLabel('strMatchFailed').'<br />'.getLabel('strSaveFailed');
				}
				else
				{
					$item = $oUser->ChangePassword( $oSession->GetValue(SS_USER_ID), 
																				getPostedArg('password')); // update
					if (!$item)
						$sMsg .= getLabel('strSaveFailed'); // failed message
					else
					{
						$sMsg .= getLabel('strSaveOK').'<br />'; // ok message
						$rUser = $oUser->GetByID($oSession->GetValue(SS_USER_ID));
						if (sendMail($rUser->Username, 
											getPostedArg('password'), 
											$rUser->FirstName.' '.$rUser->LastName,
											$rUser->Email))
						{
							$sMsg .= getLabel("strUserDataOK");
						}
						else
						{
							$sMsg .= getLabel("strUserDataFailed");
						}
					}
				}
				echo $sMsg;
			}
			break;
		default:
			showform();
			break;
	}
//=========================================================
	function sendMail($username, $password, $name, $email)
	{
		foreach(func_get_args() as $xParam) $xParam = strip_tags($xParam);
		
		$from = MAIL_SENDER;
		$fromname = SITE_URL;//getLabel('strTitle');
		$to = $email;
		$toname = $name;
		$bcc = MAIL_DEBUG;
		$subject = getLabel('strPasswordSubject');
		
		$ip = getenv("REMOTE_ADDR");
		$rhost = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		if(empty($rhost)) $rhost = 'n/a';
//===========================================================
		$mail_message='
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset='.DEF_ENCODING.'">
<style type="text/css">
<!--
body
{
	margin: 10px;
	font-family: Tahoma, Geneva, Arial, Helvetica, sans-serif;
	font-size: small;
}
h1
{
	font-size: medium;
}
-->
</style>
</head>
<body>
<h1>'.$subject.'</h1>
<br />'.
getLabel('strFullName').': '.$name.'<br />'.
getLabel('strEmail').': <a href="mailto:'.$email.'">'.$email.'</a><br />'.
getLabel('strUsername').': '.$username.'<br />'.
getLabel('strPassword').': '.$password.'<br />'.
'<br />
<br />
<!-- Sender location is '.$ip.' ('.$rhost.')<br /> -->
This email was sent via <a href="http://'.SITE_URL.'">'.SITE_URL.'</a>.
</body>
</html>';
//===========================================================
		if (!@mail($to,$subject,$mail_message,
		"bcc: ".$bcc."\nFrom: \"".$fromname."\" <".$from.">\nReply-To: ".$to."\nReturn-Path: ".MAIL_RECIPIENT."\nX-Mailer: Apache\nContent-Type: text/html; charset=\"".DEF_ENCODING."\"\nContent-Transfer-Encoding: 7bit")) {
			return false;
		}
		return true;
	}
//=========================================================
	function showform()
	{
		global $page;
?>
<script type="text/javascript">
<!--
function fCheck(frm)
{
	if (!valEmpty("old_password", "<?=getLabel('strEnter').getLabel('strOldPassword')?>")) return false;
	if (!valEmpty("password", "<?=getLabel('strEnter').getLabel('strNewPassword')?>")) return false;
	if (!valEmpty("password2", "<?=getLabel('strEnter').getLabel('strNewPassword2')?>")) return false;
	if (!matchValues("password", "password2", "<?=getLabel('strMatchFailed')?>")) return false;
	return true;
}
//-->
</script>
<?=getLabel("strRequired")?><br /><br />
<form action="" method="post" name="edit_password" id="edit_password" onsubmit="return fCheck(this);">
<input type="hidden" name="<?=ARG_ACT?>" id="<?=ARG_ACT?>" value="<?=ACT_SAVE?>" />
<label for="old_password"><?=getLabel('strOldPassword')?></label><?=formatVal()?><br />
<input type="password" name="old_password" id="old_password" maxlength="32" class="fld" /><br />
<br />
<label for="password"><?=getLabel('strNewPassword')?></label><?=formatVal()?><br />
<input type="password" name="password" id="password" maxlength="32" class="fld" /><br />
<br />
<label for="password2"><?=getLabel('strNewPassword2')?></label><?=formatVal()?><br />
<input type="password" name="password2" id="password2" maxlength="32" class="fld" /><br />
<br />
<input type="submit" value="<?=getLabel('strSave')?>" class="btn" /><br />
</form>
<?
	}
//=========================================================
?>
</div>