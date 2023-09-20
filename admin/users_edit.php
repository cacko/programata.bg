<?php
if (!$bInSite) die();
//=========================================================
$aFilter = array('keyword', 'status');
$strFilterListLink = '<a href="'.setPage($page).keepFilter().keepContext().'">'.getLabel('strBackToList').'</a><br /><br />';
//=========================================================
switch($action)
{
	case ACT_SAVE:
		$sMsg = '';
		if (isset($_POST['id']) && !empty($_POST['id']))
		{
			$item = $oUser->UpdateStatus(getPostedArg('id'), 
						getPostedArg('status'), 
						getPostedArg('city_id')); // update
		}
		/*else
		{
			$item = $oUser->Insert(	getPostedArg('title'), 
						getPostedArg('parent_page')); // insert
		}*/
		if (!$item)
			$sMsg .= getLabel('strSaveFailed'); // failed message
		else
			$sMsg .= getLabel('strSaveOK'); // ok message
		echo $sMsg.'<br /><br />';
		echo $strFilterListLink;
		$row = $oUser->GetByID($item); // get by id
		if ($row) showPreview($row); // show preview & related links
		break;
	/*case ACT_ADD:
		showForm(); // load form
		break;*/
	case ACT_EDIT:
		if (isset($item) && !empty($item))
		{
			$row = $oUser->GetByID($item); // get by id
			showForm($row); // load form
		}
		break;
	/*case ACT_DELETE:
		if (isset($item) && !empty($item))
		{
			$oUser->Delete($item);
			echo getLabel('strDeleteOK').'<br /><br />'; // show ok message
			echo $strFilterListLink;
		}
		break;*/
	case ACT_GENERATE:
		if (isset($item) && !empty($item))
		{
			$sPassword = generateRandomString(8, 8, true, true, false); // generate password
			$rUser = $oUser->GetByID($item);
			$oUser->ChangePassword($item, $sPassword);
			// send email
			sendPasswordMail( $rUser->Username, 
					$sPassword, 
					$rUser->FirstName.' '.$rUser->LastName, 
					$rUser->Email);
			echo getLabel('strGeneratePassOK').'<br /><br />'; // show ok message
		}
		break;
	default:
	case ACT_VIEW:
		if (isset($item) && !empty($item))
		{
			$row = $oUser->GetByID($item); // get by id
			if ($row) showPreview($row); // show preview & related links
		}
		break;
}
//=========================================================
function showForm($row=0)
{
	global $page;
	
	$itemID = IIF(!empty($row), $row->UserID, 0);
	echo getLabel('strRequired').'<br /><br />';
?>
<script type="text/javascript">
<!--
function fCheck(frm) {
	if (!valOption("status", "<?=getLabel('strSelect').getLabel('strUserStatus')?>")) return false;
	return true;
}
//-->
</script>
<form action="<?=setPage($page, 0, $itemID, ACT_SAVE).keepFilter().keepContext()?>" method="post" name="User" id="User" onSubmit="return fCheck(this);">

<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="id"><?=getLabel('strUserID')?></label><?=$row->UserID?>
	<input type="hidden" name="id" id="id" value="<?=$itemID?>" /><br />
	<br />
	
	<label for="status"><?=getLabel('strUserStatus')?><?=formatVal()?></label>
	<select name="status" id="status" class="fldsmall">
	<?
		$aStatuses = getLabel('aUserStatus');
		foreach($aStatuses as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($row))
			{
				if ($key == $row->UserStatus)
					echo ' selected="yes"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
	<br />

	<label for="city_id"><?=getLabel('strCity')?></label>
	<select name="city_id" id="city_id" class="fldsmall">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		$aCities = getLabel('aCities');
		foreach($aCities as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if (!empty($row))
			{
				if ($key == $row->DefaultCityID)
					echo ' selected="yes"';
			}
			echo '>'.$val.'</option>'."\n";
		}
	?>
	</select><br />
</fieldset>

	<br class="clear" />
	<br />
	<input type="submit" value="<?=getLabel('strSave')?>" class="btn" />
</form>
<?
}
//=========================================================
function showPreview($row=0)
{
	global $page, $oComment;
	
	$itemID = IIF(!empty($row), $row->UserID, 0);
	
	if (!empty($itemID))
	{
		$aStatuses = getLabel('aUserStatus');
		$nComments = $oComment->GetCountByAuthorUser($row->UserID, true);
?>
<fieldset title="<?=getLabel('strCommonData')?>" class="half">
	<legend><?=getLabel('strCommonData')?></legend>

	<label><?=getLabel('strUserID')?></label>
	<?=$row->UserID?><br />
	<br />
	
	<label><?=getLabel('strUsername')?></label>
	<?=$row->Username?><br />
	<br />
	
	<label><?=getLabel('strEmail')?></label>
	<a href="mailto:<?=$row->Email?>"><?=$row->Email?></a><br />
	<br />
	
	<label><?=getLabel('strFirstName').', '.getLabel('strLastName')?></label>
	<?=$row->FirstName.' '.$row->LastName?><br />
	<br />
	
	<label><?=getLabel('strCompany')?></label>
	<?=$row->Company?><br />
	<br />
	
	<label><?=getLabel('strAddress')?></label>
	<?=nl2br($row->Address)?><br />
	<br />
	
	<label><?=getLabel('strCity')?></label>
	<?=$row->City?><br />
	<br />
	
	<label><?=getLabel('strCountry')?></label>
	<?=$row->Country?><br />
	<br />
	
	<label><?=getLabel('strPhone')?></label>
	<?=$row->Phone?><br />
	<br />
	
	<label><?=getLabel('strUserStatus')?></label>
	<?=$aStatuses[$row->UserStatus]?> | 
	<a href="<?=setPage(ENT_COMMENT).'&amp;'.ENT_USER.'='.$row->UserID.'">'.getLabel('strComments').'</a> ('.$nComments.')'?><br />
	<br />
	
	<label><?=getLabel('strLastLogin').' ('.getLabel('strNrLogins').')'?></label>
	<?=$row->LastLogin.' ('.$row->NrLogins.')'?><br />
	<br />
	<br />
	
	<label><?=getLabel('strLastUpdate')?></label>
	<?=IIF(!empty($row), $row->LastUpdate, '')?><br />
	<br />
</fieldset>
<!--iframe name="related" style="float:left;" width="500" height="500" src="<?=RELATED_PAGE.'?'.ARG_PAGE.'='.ENT_HOME.'&amp;'.ARG_RELID.'='.$itemID.'&amp;'.ARG_CAT.'='.$aEntityTypes[ENT_USER]?>"></iframe-->
<br class="clear" />

<ul class="nav">
	<li><a href="<?=setPage($page, 0, $itemID, ACT_GENERATE)?>" onclick="return confMsg('<?=getLabel('strGenerateQ')?>');return false;"></li>
	<li><a href="<?=setPage($page, 0, $itemID, ACT_EDIT).keepFilter().keepContext()?>"><?=getLabel('edit')?></a></li>
	<!--li><a href="<?=setPage($page, 0, $itemID, ACT_DELETE).keepFilter().keepContext()?>" onclick="return confMsg('<?=getLabel('strDeleteQ')?>');return false;"><?=getLabel('delete')?></a></li-->
</ul>

<?
	}
}
//=========================================================
	function sendPasswordMail($username, $password, $name, $email)
	{
		foreach(func_get_args() as $xParam) $xParam = strip_tags($xParam);
		
		$from = MAIL_SENDER;
		$fromname = SITE_URL;//getLabel('strSiteTitle');
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
</head>
<body>
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
<h1>'.$subject.'</h1>
<br />'.
getLabel('strFirstName').': '.$name.'<br />'.
getLabel('strEmail').': <a href="mailto:'.$to.'">'.$to.'</a><br />'.
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
?>