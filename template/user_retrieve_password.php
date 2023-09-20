<?
if (!$bInSite) die();
//=========================================================
?>
<div class="text">
<?
//=========================================================
$page_info = $oPage->GetByID($page);
	switch($action)
	{
		case ACT_SEND:
			$sMsg = '';
			$rsUser = $oUser->RetrieveAccount(getPostedArg('username'), 
																		getPostedArg('email'));
			if (!$rsUser || mysql_num_rows($rsUser) == 0)
				$sMsg .= getLabel('strRetrieveFailed'); // failed message
			else
			{
				$sMsg .= getLabel('strRetrieveOK').'<br />'; // ok message
				$bSent = false;
				while($row = mysql_fetch_object($rsUser))
				{
					$sPassword = generateRandomString(8, 8, true, true, false); // generate password
					$oUser->ChangePassword($row->UserID, $sPassword);
					$bSent = $bSent | sendMail($row->Username, 
																	$sPassword, 
																	$row->FirstName.' '.$row->LastName,
																	$row->Email);
				}
				if ($bSent)
				{
					$sMsg .= getLabel("strUserDataOK").'<br />';
				}
				else
				{
					$sMsg .= getLabel("strUserDataFailed").'<br />';
				}
			}
			echo $sMsg;
			break;
		default:
			showform($page_info);
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
	function showform($page_info)
	{
		global $page;
?>
<script type="text/javascript">
<!--
function fCheck(frm)
{
	//if (!valEmpty("usrname", "<?=getLabel('strEnter').getLabel('strUsername')?>")) return false;
	if (!valEmail("email", "<?=getLabel('strEnter').getLabel('strEmail')?>")) return false;
	return true;
}
//-->
</script>
<h1 id="lost_password_title"><?=$page_info->Title?></h1><hr id="lost_password_line"/>
<div id="lost_password">
<form action="" method="post" name="retrieve" id="retrieve" onsubmit="return fCheck(this);">
<div class="text"><?=$page_info->Description?></div>
<b><?=getLabel("strRequired")?></b><br />
<input type="hidden" name="<?=ARG_ACT?>" id="<?=ARG_ACT?>" value="<?=ACT_SEND?>" />
<input type="text" name="usrname" id="usrname" maxlength="32" class="fld" value="<?php echo getLabel('strUsername') . (strlen(formatVal()) > 0 ? '*' : '');?>" onclick="if(this.value == '<?php echo getLabel('strUsername') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}"/>
<input type="text" name="email" id="email" maxlength="128" class="fld" value="<?php echo getLabel('strEmail') . (strlen(formatVal()) > 0 ? '*' : '');?>" onclick="if(this.value == '<?php echo getLabel('strEmail') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}" />
<input type="submit" value="<?=getLabel('strFind')?>" class="btn" />
</form>
</div>
<?
	}
//=========================================================
?>
</div>