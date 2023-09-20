<?
if (!$bInSite) die();
//=========================================================
	$page_info = $oPage->GetByID($page);
//=========================================================
	switch($action)
	{
		case ACT_SAVE:
			$sMsg = '';

			$aNames = explode(' ', getPostedArg('fullName'));
			$item = $oUser->Update( $oSession->GetValue(SS_USER_ID), 
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
						array());//getPostedArg('eventim') // update
			if (!$item)
				$sMsg .= getLabel('strSaveFailed'); // failed message
			else
			{
				$sMsg .= getLabel('strSaveOK').'<br />'; // ok message
			}
			echo $sMsg;
			break;
		default:
			$nUserID = $oSession->GetValue(SS_USER_ID);
			if (!empty($nUserID) && is_numeric($nUserID))
			{
				$rUser = $oUser->GetByID($nUserID);
				showform($rUser, $page_info);
			}
			break;
	}
//=========================================================
	function showform($row, $page_info)
	{
		global $page;
?>
<script type="text/javascript">
<!--
function fCheck(frm)
{	
	if (!valEmpty("fullName", "<?=getLabel('strEnter').getLabel('strFullName')?>")) return false;
	if (!valEmail("email", "<?=getLabel('strEnter').getLabel('strEmail')?>")) return false;
	return true;
}
//-->
</script>
<h1 id="edit_profile_title"><?=$page_info->Title?></h1><hr id="edit_profile_line"/>
<div id="edit_profile">
<form action="" method="post" name="edit_profile" id="edit_profile" onsubmit="return fCheck(this);">
<div class="text"><?=$page_info->Description?></div>
<br class="clear"/>
<b><?=getLabel("strRequired")?></b><br />
<input type="hidden" name="<?=ARG_ACT?>" id="<?=ARG_ACT?>" value="<?=ACT_SAVE?>" />
<input type="text" name="fullName" id="fullName" maxlength="255" class="fld" value="<?php echo $row->FirstName.' '.$row->LastName ?>"/>
<div id="one_line">
<label for="sex" id="sex"><?=getLabel('strSex')?></label>
<?
	$aSex = getLabel('aSex');
	while(list($key, $value) = each($aSex)) 
	{
		?>
		<input type="radio" name="sex" value="<?=$key?>"<?=IIF($key==$row->Sex,' checked="checked"','')?> />
		<label for="sex_<?=$key?>" id="sex_<?=$key?>"><?=$value?></label>
<? 	} ?>
<label for="age" id="age"><?php echo getLabel('strAge') . (strlen(formatVal()) > 0 ? '*' : '') ?></label>
<input type="text" name="age" id="age" value="<?=$row->Age?>" maxlength="10" class="fld"/>
</div>
<input type="text" name="city" id="city" maxlength="64" class="fld" value="<?php echo $row->City?>" />
<input type="text" name="email" id="email" maxlength="255" class="fld" value="<?php echo $row->Email?>" />
<input type="text" name="url" id="url" maxlength="255" class="fld" value="<?php echo IIF(!empty($row->Url), $row->Url, 'http://')?>" />
<input type="text" name="profession" id="profession" maxlength="255" class="fld" value="<?php echo $row->Profession?>" />
<textarea cols="36" rows="5" name="note" id="note" wrap="soft"><?php echo $row->Note?></textarea>
<br />
<input type="submit" value="<?=getLabel('strSave')?>" class="btn" /><br />
</form>
</div>
<?
	}
//=========================================================
?>