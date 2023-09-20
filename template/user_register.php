<?
if (!$bInSite) die();

//=========================================================
	$nUserID = $oSession->GetValue(SS_USER_ID);

	$page_info = $oPage->GetByID($page);
	
	if (!empty($nUserID))
	{
		//include('template/user_welcome.php');
	}
	else
	{
		switch($action)
		{
			case ACT_ON:
				$sMsg = '';
				$rUser = $oUser->GetByID($item);
				if ($rUser)
				{
					if ($rUser->ConfirmKey == getQueryArg('key'))
					{
						$oUser->Activate($item);
						
						//$sMsg = getLabel('strUserConfirmOK');// confirm message
						$sMsg = str_replace('%name', $rUser->FirstName.' '.$rUser->LastName, $sMsg);
						$sMsg .= getLabel('strLoginIntro');
					}
					else
						$sMsg = getLabel('strUserConfirmFailed');// fail message
				}
				else
					$sMsg = getLabel('strUserConfirmFailed');// fail message
				echo $sMsg;
				break;
			case ACT_SAVE:
				$sMsg = '';
				$bUserCanRegister = $oUser->CanRegister(getPostedArg('usrname'));
				$blockMails = array('dona.c.amp.be.l678@gmail.com');
				$email = getPostedArg('email');
				
				if ($bUserCanRegister && !in_array($email, $blockMails))
				{
					$aNames = explode(' ', getPostedArg('fullName'));
					$sKey = generateRandomString(8, 8, true, true, false);
					
					/*$aNewsletterCategories = getLabel('aNewsletterCategories');
					$aEventim = array();
					foreach ($aNewsletterCategories as $k=>$v)
					{
						$val = getPostedArg('n'.$k);
						if (!empty($val))
							$aEventim[] = $k;
					}*/
					//$sEventim = join(',',$aEventim);
					
					$item = $oUser->Insert(getPostedArg('usrname'), 
								getPostedArg('passwrd'), 
								getPostedArg('email'), 
								$aNames[0], 
								$aNames[1], 
								'', 
								'', 
								getPostedArg('city'), 
								'', 
								'', 
								getPostedArg('sex'),
								getPostedArg('age'),
								getPostedArg('profession'),
								getPostedArg('note'), 
								getPostedArg('url'),
								array(), //$aEventim
								$sKey); // insert
					if (!$item)
						$sMsg .= getLabel('strSaveFailed'); // failed message
					else
					{
						$sMsg .= getLabel('strSaveOK').'<br />'; // ok message
						if (sendMail($item, getPostedArg('usrname'), 
								getPostedArg('passwrd'), 
								getPostedArg('fullName'),
								getPostedArg('email'), $sKey))
						{
							$sMsg .= getLabel("strUserDataOK");
						}
						else
						{
							$sMsg .= getLabel("strUserDataFailed");
						}
						$sMsg .= getLabel('strUserLogin');
					}
				}
				else
					$sMsg .= getLabel('strUsernameTaken');
				echo $sMsg;
				break;
			default:
				showform($page_info);
				break;
		}
	}
//=========================================================
	function sendMail($item, $username, $password, $name, $email, $key)
	{
		foreach(func_get_args() as $xParam) $xParam = strip_tags($xParam);
		
		$from = MAIL_SENDER;
		$fromname = SITE_URL;//getLabel('strTitle');
		$to = $email;
		$toname = $name;
		$bcc = MAIL_DEBUG;
		$subject = getLabel('strRegistrationSubject');
		
		$ip = getenv("REMOTE_ADDR");
		$rhost = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		if(empty($rhost)) $rhost = 'n/a';
//===========================================================
		$mail_text = getLabel('strRegistrationMessage');
		$mail_text = str_replace('%name', $name, $mail_text);
		$mail_text = str_replace('%user', $username, $mail_text);
		$mail_text = str_replace('%pass', $password, $mail_text);
		
		$link = '<a href="http://'.SITE_URL.'/'.setPage(USERREG_PAGE, 0, $item, ACT_ON).'&amp;key='.$key.'">http://'.SITE_URL.'/'.setPage(USERREG_PAGE, 0, $item, ACT_ON).'&amp;key='.$key.'</a>';
		$mail_text = str_replace('%link', $link, $mail_text);

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
<h1>'.getLabel('strRegistrationTitle').'</h1>
<br />'.$mail_text.'<br />
<br />
<!-- Sender location is '.$ip.' ('.$rhost.')<br /> -->
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
	if (!valEmpty("usrname", "<?=getLabel('strEnter').getLabel('strUsername')?>")) return false;
	if (!valEmpty("passwrd", "<?=getLabel('strEnter').getLabel('strPassword')?>")) return false;
	if (!valEmpty("passwrd2", "<?=getLabel('strEnter').getLabel('strNewPassword2')?>")) return false;
	if (!matchValues("passwrd", "passwrd2", "<?=getLabel('strMatchFailed')?>")) return false;
	if (!valEmpty("fullName", "<?=getLabel('strEnter').getLabel('strFullName')?>")) return false;
	if (!valEmail("email", "<?=getLabel('strEnter').getLabel('strEmail')?>")) return false;
	return true;
}
//-->
</script>
<h1 id="registration_title"><?=$page_info->Title?></h1><hr id="registration_line"/>
<div id="registration">
<form action="" method="post" name="register" id="register" onsubmit="return fCheck(this);">
<div class="text"><?=$page_info->Description?></div>
<br class="clear"/>
<b><?=getLabel("strRequired")?></b><br />
<input type="hidden" name="<?=ARG_ACT?>" id="<?=ARG_ACT?>" value="<?=ACT_SAVE?>" />
<input type="text" name="usrname" id="usrname" maxlength="32" class="fld" value="<?php echo getLabel('strUsername') . (strlen(formatVal()) > 0 ? '*' : '') ?>" onclick="if(this.value == '<?php echo getLabel('strUsername') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}"/>
<div id="one_line">
<label for="sex" id="sex"><?=getLabel('strSex')?></label>
<?
	$aSex = getLabel('aSex');
	while(list($key, $value) = each($aSex)) 
	{
		?>
		<input type="radio" name="sex" value="<?=$key?>" />
		<label for="sex_<?=$key?>" id="sex_<?=$key?>"><?=$value?></label>
<? 	} ?>
<label for="age" id="age"><?php echo getLabel('strAge') . (strlen(formatVal()) > 0 ? '*' : '') ?></label>
<input type="text" name="age" id="age" maxlength="10" class="fld"/>
</div>
<input type="text" name="passwrd" id="passwrd" maxlength="32" class="fld" value="<?php echo getLabel('strPassword') . (strlen(formatVal()) > 0 ? '*' : '') ?>" onclick="if(this.value == '<?php echo getLabel('strPassword') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}$(this).replaceWith('<input type=\'password\' name=\'passwrd\' id=\'passwrd\' maxlength=\'32\' class=\'fld\'/>'); $('#passwrd').select();" onfocus="$(this).replaceWith('<input type=\'password\' name=\'passwrd\' id=\'passwrd\' maxlength=\'32\' class=\'fld\'/>'); $('#passwrd').select();"/>
<input type="text" name="passwrd2" id="passwrd2" maxlength="32" class="fld" value="<?php echo getLabel('strNewPassword2') . (strlen(formatVal()) > 0 ? '*' : '') ?>" onclick="if(this.value == '<?php echo getLabel('strNewPassword2') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}$(this).replaceWith('<input type=\'password\' name=\'passwrd2\' id=\'passwrd2\' maxlength=\'32\' class=\'fld\'/>'); $('#passwrd2').select();" onfocus="$(this).replaceWith('<input type=\'password\' name=\'passwrd2\' id=\'passwrd2\' maxlength=\'32\' class=\'fld\'/>'); $('#passwrd2').select();"/>
<input type="text" name="fullName" id="fullName" maxlength="255" class="fld" value="<?php echo getLabel('strFullName') . (strlen(formatVal()) > 0 ? '*' : '') ?>" onclick="if(this.value == '<?php echo getLabel('strFullName') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}"/>
<input type="text" name="city" id="city" maxlength="64" class="fld" value="<?php echo getLabel('strCity') . (strlen(formatVal()) > 0 ? '*' : '') ?>" onclick="if(this.value == '<?php echo getLabel('strCity') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}"/>
<input type="text" name="email" id="email" maxlength="255" class="fld" value="<?php echo getLabel('strEmail') . (strlen(formatVal()) > 0 ? '*' : '') ?>" onclick="if(this.value == '<?php echo getLabel('strEmail') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}"/>
<input type="text" name="url" id="url" maxlength="255" class="fld" value="<?php echo getLabel('strUrl') . (strlen(formatVal()) > 0 ? '*' : '') ?>" onclick="if(this.value == '<?php echo getLabel('strUrl') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}"/>
<input type="text" name="profession" id="profession" maxlength="255" class="fld" value="<?php echo getLabel('strProfession') . (strlen(formatVal()) > 0 ? '*' : '') ?>" onclick="if(this.value == '<?php echo getLabel('strProfession') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}"/>

<textarea cols="36" rows="5" name="note" id="note" wrap="soft" onclick="if(this.value == '<?php echo getLabel('strInterests') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}"><?php echo getLabel('strInterests') . (strlen(formatVal()) > 0 ? '*' : '') ?></textarea>
<!--label><?=getLabel('strNewsletters')?></label><br />
<?
	/*$aNewsletterCategories = getLabel('aNewsletterCategories');
	foreach($aNewsletterCategories as $k=>$v)
	{
		echo '<input type="checkbox" id="n'.$k.'" name="n'.$k.'" value="1" /><label for="n'.$k.'">'.$v.'</label><br />';
	}*/
?>
<br /-->
<br class="clear"/>
<input type="submit" value="<?=getLabel('strRegister')?>" class="btn" /><br />
</form>
</div>
<?
	}
//=========================================================
?>
