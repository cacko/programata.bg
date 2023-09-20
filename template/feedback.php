<?php
if (!$bInSite) die();
//=========================================================

	$page_info = $oPage->GetByID($page);

	echo '<div class="text">';
	switch($action)
	{
		case ACT_SEND:
			if (prevent_multiple_submit())
			{
				$sEmail = htmlspecialchars(strip_tags(getPostedArg('email')));
				if (empty($sEmail))
				{
					echo getLabel("strRequired");
					showform($page_info);
				}
				elseif (sendMail(getPostedArg('fullName'), 
						getPostedArg('email'), 
						getPostedArg('comment')))
				{
					echo getLabel("strSendOK");
				}
				else {
					echo getLabel("strSendFailed");
				}
			}
			else
				echo getLabel("strSendOK");
			break;
		default:
			showform($page_info);
		break;
	}
	echo '</div>';
//=========================================================
	function sendMail($firstName, $email, $sText)
	{
		$sFullText = '';
		foreach(func_get_args() as $xParam)
		{
			$xParam = htmlspecialchars(strip_tags($xParam));
			$sFullText .= $xParam.' ';
		}
		
		$from = $email;
		$name = $firstName;
		$to = MAIL_FEEDBACK;
		$bcc = "";
		$subject = getLabel('strFeedbackSubject');
		$text = strShorten($sText, TEXT_LEN_EMAIL);
		
		$ip = getenv("REMOTE_ADDR");
		$rhost = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		if(empty($rhost)) $rhost = 'n/a';
		
//===========================================================
		$bSendMessage = true;
		if (substr_count ($sFullText, 'http://') > 3 || substr_count ($sFullText, '.html') > 3 || substr_count ($sFullText, '.info') > 3)
		{
			$bSendMessage = false;
		}
		//$aWords = explode("\n", $text);
		$aWords = preg_split ("/[\s,]+/", $text);
		$nLastWord = count($aWords)-1;
		if (strlen(trim($aWords[0])) == 32 || strlen(trim($aWords[$nLastWord])) == 32)
		{
			$bSendMessage = false;
		}
		$aSpamWords = array('ringtone', 'gratis', 'prayer', 'sandra', 'lobster', 'coupon', 'index', 'nektomail', 'nekto', 'free uniform', 'moshenic', 'spears.com', 'free anti virus', 'phentermine', 'rolex', 'replica', 'howmutchlinksyouhave', 'chic', 'sleazy', 'loan', 'equity', 'teen', 'adult', 'dating', 'cock', 'hardcore', 'porn', 'nude', 'naked', 'masturb', 'suck', 'dick', 'gay', 'cock', 'hardcore', 'porn', 'nude', 'naked', 'masturb', 'suck', 'dick', 'gay', 'lesbian', 'fuck', 'sex', 'viagra', 'zoloft', 'xanax', 'valium', 'ultram', 'tramadol', 'cialis', 'diazepam', 'ativan', 'carisoprodol', 'cyclobenzaprine', 'casino', 'poker', 'roulette', 'gambling', 'blackjack', 'slot-machines', 'cash-money', 'mortgage', 'http://1', '[/url]');
		foreach($aSpamWords as $word)
		{
			if(strpos(strtolower($sFullText), strtolower($word)) === false)
			{
				//$bSendMessage = true;
			}
			else
			{
				$bSendMessage = false;
				break;
			}
		}
		if(!$bSendMessage)
		{
			return true; // don't inform spammers you ban them
			//die('spaaaaam');
		}
		else
		{
//===========================================================
		$mail_message='
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset='.DEF_ENCODING.'">
</head>
<body>
<style type="text/css">
<!--
body
{
	margin: 10px;
	font-family: Arial, Helvetica, sans-serif;
	font-size: small;
}
h1
{
	font-size: medium;
}
-->
</style>
<h1>'.$subject.'</h1>
<br />'.
getLabel('strFullName').': '.$name.'<br />'.
getLabel('strEmail').': <a href="mailto:'.$from.'">'.$from.'</a><br />'.
getLabel('strMessage').':<br /><br />
'.nl2br($text).'<br />
<br />
Sender location is '.$ip.' ('.$rhost.')<br />
This email was sent via <a href="http://'.SITE_URL.'">'.SITE_URL.'</a>.'.
'</body>
</html>';
//===========================================================
			if (!@mail($to,$subject,$mail_message,
			"bcc: ".$bcc."\nFrom: \"".$name."\" <".$from.">\nReply-To: ".$to."\nReturn-Path: ".$to."\nX-Mailer: Apache\nContent-Type: text/html; charset=\"".DEF_ENCODING."\"\nContent-Transfer-Encoding: 7bit")) {
				return false;
			}
			return true;
		}
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
	if (!valEmpty("fullName", "<?=getLabel('strEnter').getLabel('strFullName')?>")) return false;
	if (!valEmail("email", "<?=getLabel('strEnter').getLabel('strEmail')?>")) return false;
	if (!valEmpty("comment", "<?=getLabel('strEnter').getLabel('strMessage')?>")) return false;
	return true;
}
//-->
</script>
<h1 id="feedback_title"><?=$page_info->Title?></h1><hr id="feedback_line"/>
<div id="feedback">
<form method="post" action="<?=setPage($page, 0, 0, ACT_SEND)?>" name="feedback" onsubmit="return fCheck(this);">
<div class="text"><?=$page_info->Description?></div>
<b><?=getLabel("strRequired")?></b><br />
<input type="text" name="fullName" id="fullName" maxlength="255" class="fld" value="<?php echo getLabel('strFullName') . (strlen(formatVal()) > 0 ? '*' : '') ?>" onclick="if(this.value == '<?php echo getLabel('strFullName') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}"/>
<input type="text" name="email" id="email" maxlength="255" class="fld" value="<?php echo getLabel('strEmail') . (strlen(formatVal()) > 0 ? '*' : '') ?>" onclick="if(this.value == '<?php echo getLabel('strEmail') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}"/><br />
<textarea cols="36" rows="15" name="comment" id="comment" onclick="if(this.value == '<?php echo getLabel('strMessage') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}">
<?php
	echo getLabel('strMessage') . (strlen(formatVal()) > 0 ? '*' : '');
?></textarea><br />
<input type="submit" value="<?=getLabel('strSend')?>" class="btn" />
</form>
</div>
<?
	}
//=========================================================
?>