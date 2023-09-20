<?php
if (!$bInSite) die();
//=========================================================

	$page_info = $oPage->GetByID($page);

	switch($action)
	{
		case ACT_SEND:
			if (prevent_multiple_submit())
			{
				$sEmail = htmlspecialchars(strip_tags(getPostedArg('email')));
				if (empty($sEmail))
				{
					echo getLabel("strRequired");
					showform($page_info, $oAttachment);
				}
				elseif (sendMail(getPostedArg('fullName'),
						getPostedArg('phone'),
						getPostedArg('email'),
						getPostedArg('city'),
						getPostedArg('company'),
						getPostedArg('company_activity'),
						getPostedArg('position'),
						getPostedArg('ad_type'),
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
			showform($page_info, $oAttachment);
		break;
	}
//=========================================================
	function sendMail($firstName, $phone, $email, $city, $company, $company_activity, $position, $ad_type, $sText)
	{
		$sFullText = '';
		foreach(func_get_args() as $xParam)
		{
			$xParam = htmlspecialchars(strip_tags($xParam));
			$sFullText .= $xParam.' ';
		}

		$from = $email;
		$name = $firstName;
		$to = MAIL_SALES.', '.MAIL_GIFT_BG;
		$bcc = "";
		$subject = getLabel('strFeedbackSubject');
		$text = strShorten($sText, TEXT_LEN_EMAIL);

		$ip = getenv("REMOTE_ADDR");
		$rhost = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		if(empty($rhost)) $rhost = 'n/a';

		$aAdTypes = getLabel('aAdTypes');

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
getLabel('strPhone').': '.$phone.'<br />'.
getLabel('strEmail').': <a href="mailto:'.$from.'">'.$from.'</a><br />'.
getLabel('strCity').': '.$city.'<br />'.
getLabel('strCompany').': '.$company.'<br />'.
getLabel('strCompanyActivity').': '.nl2br($company_activity).'<br />'.
getLabel('strPosition').': '.$position.'<br />'.
getLabel('strAdType').': '.$aAdTypes[$ad_type].'<br />'.
getLabel('strAdDescription').': <br /><br />'.nl2br($text).'<br />
<br />
<!-- Sender location is '.$ip.' ('.$rhost.')<br /> -->
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
	function showform($page_info, $oAttachment)
	{
		global $page;
?>
<script type="text/javascript">
<!--
function fCheck(frm)
{
	if (!valEmpty("fullName", "<?=getLabel('strEnter').getLabel('strFullName')?>")) return false;
	if (!valEmpty("phone", "<?=getLabel('strEnter').getLabel('strPhone')?>")) return false;
	if (!valEmail("email", "<?=getLabel('strEnter').getLabel('strEmail')?>")) return false;
	if (!valEmpty("city", "<?=getLabel('strEnter').getLabel('strCity')?>")) return false;
	if (!valEmpty("company", "<?=getLabel('strEnter').getLabel('strCompany')?>")) return false;
	if (!valOption("ad_type", "<?=getLabel('strSelect').getLabel('strAdType')?>")) return false;
	if (!valEmpty("comment", "<?=getLabel('strEnter').getLabel('strAdDescription')?>")) return false;
	return true;
}
//-->
</script>
<h1 id="advertise_title"><?=$page_info->Title?></h1><hr id="advertise_line"/>
<div id="advertise">
<form method="post" action="<?=setPage($page, 0, 0, ACT_SEND)?>" name="feedback" onsubmit="return fCheck(this);">
<div class="text"><?=$page_info->Description?></div>
<br class="clear"/>
<b><?=getLabel("strRequired")?></b><br />
<input type="text" name="fullName" id="fullName" maxlength="255" class="fld" value="<?php echo getLabel('strFullName') . (strlen(formatVal()) > 0 ? '*' : '') ?>" onclick="if(this.value == '<?php echo getLabel('strFullName') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}" />
<textarea cols="36" rows="4" name="company_activity" id="company_activity" onclick="if(this.value == '<?php echo getLabel('strCompanyActivity') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}"><?php echo getLabel('strCompanyActivity') . (strlen(formatVal()) > 0 ? '*' : '') ?></textarea>
<input type="text" name="phone" id="phone" maxlength="255" class="fld" value="<?php echo getLabel('strPhone') . (strlen(formatVal()) > 0 ? '*' : '') ?>" onclick="if(this.value == '<?php echo getLabel('strPhone') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}"/>
<input type="text" name="email" id="email" maxlength="255" class="fld" value="<?php echo getLabel('strEmail') . (strlen(formatVal()) > 0 ? '*' : '') ?>" onclick="if(this.value == '<?php echo getLabel('strEmail') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}"/>
<input type="text" name="city" id="city" maxlength="255" class="fld" value="<?php echo getLabel('strCity') . (strlen(formatVal()) > 0 ? '*' : '') ?>" onclick="if(this.value == '<?php echo getLabel('strCity') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}"/>
<input type="text" name="company" id="company" maxlength="255" class="fld" value="<?php echo getLabel('strCompany') . (strlen(formatVal()) > 0 ? '*' : '') ?>" onclick="if(this.value == '<?php echo getLabel('strCompany') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}"/>
<br class="clear"/>
<input type="text" name="position" id="position" maxlength="255" class="fld" value="<?php echo getLabel('strPosition') . (strlen(formatVal()) > 0 ? '*' : '') ?>" onclick="if(this.value == '<?php echo getLabel('strPosition') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}"/>
<select name="ad_type" id="ad_type" class="fld">
	<option value="0"><?php echo getLabel('strAdType') . (strlen(formatVal()) > 0 ? '*' : '') ?></option>
<?
	$aAdTypes = getLabel('aAdTypes');
	foreach($aAdTypes as $key=>$val)
	{
		echo '	<option value="'.$key.'">'.$val.'</option>'."\n";
	}
?>
</select>
<br class="clear"/>
<textarea cols="36" rows="10" name="comment" id="comment" onclick="if(this.value == '<?php echo getLabel('strAdDescription') . (strlen(formatVal()) > 0 ? '*' : '');?>'){ this.value = '';}"><?php echo getLabel('strAdDescription') . (strlen(formatVal()) > 0 ? '*' : '') ?></textarea>
<br />
<input type="submit" value="<?=getLabel('strSend')?>" class="btn" />
</form>
<br class="clear" />
</div>
<div id="right_bar_menu">
	<?php 
		$rsAttachment = $oAttachment->ListAll($page, $aEntityTypes[ENT_PAGE], 6);
		if(mysql_num_rows($rsAttachment) > 0 ){
	?>
		<h1><?=getLabel('strDownload')?></h1>
		<hr/>
			<ul>
				<?php
					while($row = mysql_fetch_object($rsAttachment))
					{
						$sMainFile = UPLOAD_DIR.FILE_ATTACHMENT.$row->AttachmentID.'.'.$row->Extension;
						echo '<li><a href="'.$sMainFile.'" target="_blank">'.$row->Title.'</a></li>'."\n";
					}				
				?>
			</ul>
		<br />
	<?php } ?>
	<h1><?=getLabel('strStats')?></h1>
	<hr/>
		<ul>
			<li><a href="http://www.tyxo.bg/?11669" target="_blank">tyxo.bg</a></li>
			<li><a href="http://bgcounter.com/?_id=programa" target="_blank">bgcounter.com</a></li>
			<li><a href="http://www.alexa.com/data/details/main?url=http://www.programata.bg+++++" target="_blank">alexa.com</a></li>
		</ul>	
</div>
<br class="clear" />
<?
	}
//=========================================================
?>