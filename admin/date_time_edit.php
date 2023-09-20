<?php
if (!$bInSite) die();
//=========================================================
$aFilter = array('entity_id', ARG_RELID, ARG_CAT, 'start_date', 'end_date');
$strFilterListLink = '<a href="'.setPage($page).keepFilter().keepContext().'">'.getLabel('strBackToList').'</a><br /><br />';
//=========================================================
switch($action)
{
	case ACT_SAVE:
		$sMsg = '';
		//print_r($_POST);
		if (isset($_POST['id']) && !empty($_POST['id']))
		{
			$item = getPostedArg('id');
		}
		else
		{
			//$cat, //getPostedArg('entity_type'),
			$item = $oProgramDateTime->Insert($relitem, //getPostedArg('entity_id'), 
						parseDate(getPostedArg('program_date')), 
						parseTime(getPostedArg('program_time')),
						getPostedArg('price'), 
						getPostedArg('hidden')); // primary record
		}
		//foreach($aLanguages as $key=>$val)
		//{
			$oProgramDateTime->Update($item, 
						$relitem, //getPostedArg('entity_id'), 
						parseDate(getPostedArg('program_date')), 
						parseTime(getPostedArg('program_time')),
						getPostedArg('price'), 
						getPostedArg('hidden')); //, $key); // update all
		//}
		//==========================================
		if (!$item)
			$sMsg .= getLabel('strSaveFailed'); // failed message
		else
			$sMsg .= getLabel('strSaveOK'); // ok message
		echo $sMsg.'<br /><br />';
		echo $strFilterListLink;
		$aRows = array();
		foreach($aLanguages as $key=>$val)
			$aRows[$key] = $oProgramDateTime->GetByID($item, $key); // get by id
		if (count($aRows)>0) showPreview($aRows); // show preview & related links
		break;
	case ACT_SAVE_MULTIPLE:
		$sMsg = '';
		//print_r($_POST);
		$aDates = getPostedArg('dates');
		$aProgramTimes = getPostedArg('program_times');
		$aTimes = getPostedArg('times');
		$aPrices = getPostedArg('prices');
		foreach($aDates as $k=>$v)
		{
			$dSelDate = $v;
			foreach($aProgramTimes as $k2=>$v2)
			{
				$dSelTime = $v2;
				if (!empty($aTimes[$k][$k2]) || !empty($aPrices[$k][$k2]))
				{
					$sSelPrice = $aPrices[$k][$k2];
					// insert data here // echo $dSelDate.' - '.$dSelTime.' - '.$sSelPrice.'<br />';
					//$cat, //getPostedArg('entity_type'),
					$item = $oProgramDateTime->Insert($relitem, //getPostedArg('entity_id'), 
									$dSelDate, 
									parseTime($dSelTime),
									$sSelPrice); // primary record
				}
			}
		}
		//==========================================
		if (!$item)
			$sMsg .= getLabel('strSaveFailed'); // failed message
		else
			$sMsg .= getLabel('strSaveOK'); // ok message
		echo $sMsg.'<br /><br />';
		echo $strFilterListLink;
		break;
	case ACT_ADD:
		showForm(); // load form
		break;
	case ACT_ADD_MULTIPLE:
		showMultiForm(); // load form
		break;
	case ACT_EDIT:
		if (isset($item) && !empty($item))
		{
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oProgramDateTime->GetByID($item, $key); // get by id
			if (count($aRows)>0) showForm($aRows); // load form
		}
		break;
	case ACT_DELETE:
		if (isset($item) && !empty($item))
		{
			$oProgramDateTime->Delete($item);
			echo getLabel('strDeleteOK').'<br /><br />'; // show ok message
			echo $strFilterListLink;
		}
		break;
	default:
	case ACT_VIEW:
		if (isset($item) && !empty($item))
		{
			$aRows = array();
			foreach($aLanguages as $key=>$val)
				$aRows[$key] = $oProgramDateTime->GetByID($item, $key); // get by id
			if (count($aRows)>0) showPreview($aRows); // show preview & related links
		}
		break;
}
//=========================================================
function showMultiForm()
{
	global $page, $oUser, $aLanguages, $aAbbrLanguages;
	
	$dToday = date(DEFAULT_DATE_DB_FORMAT);
	
	$nDaysToShow = getPostedArg('nr_days');
	$nTimesToShow = getPostedArg('nr_times');
	$dStartDate = parseDate(getPostedArg('start_date', $dToday));
?>
<script type="text/javascript">
<!--
	function checkDown(elem, targetclass)
	{
		var gNodes = document.getElementsByTagName('INPUT');
		for (var i = 0; gNodes.length > i; i ++)
		{
			if (gNodes[i].className == targetclass)
			{
				gNodes[i].checked = elem.checked;
			}
		}
	}
	function fillDown(elem, targetclass)
	{
		var gNodes = document.getElementsByTagName('INPUT');
		for (var i = 0; gNodes.length > i; i ++)
		{
			if (gNodes[i].className == targetclass)
			{
				gNodes[i].value = elem.value;
			}
		}
	}
// -->
</script>
<?
	if (empty($nDaysToShow) || empty($nTimesToShow) || empty($dStartDate))
	{
?>
<form action="<?=setPage($page, 0, 0, ACT_ADD_MULTIPLE).keepFilter().keepContext()?>" enctype="multipart/form-data" method="post" name="ProgramDateTime" id="ProgramDateTime">
<script type="text/javascript">
<!--
function fCheck(frm) {
	if (!valEmpty("start_date", "<?=getLabel('strEnter').getLabel('strStartDate')?>")) return false;
	if (!valNumber("nr_days", "<?=getLabel('strEnter').getLabel('strNrDates')?>")) return false;
	if (!valNumber("nr_times", "<?=getLabel('strEnter').getLabel('strNrTimes')?>")) return false;
	return true;
}
//-->
</script>
<table summary="filter" class="form">
<tr>
	<td><label for="start_date" class="dte"><?=getLabel('strStartDate')?><br /></label><br />
	<input type="text" name="start_date" id="start_date" maxlength="10" class="flddate" 
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" /></td>
	<td><label for="nr_days" class="list"><br /><?=getLabel('strNrDates')?></label><br />
	<input type="text" name="nr_days" id="nr_days" maxlength="2" class="flddate" /></td>
	<td><label for="nr_times" class="list"><br /><?=getLabel('strNrTimes')?></label><br />
	<input type="text" name="nr_times" id="nr_times" maxlength="2" class="flddate" /></td>
	<td><br /><br /><input type="submit" value="<?=getLabel('strGenerateGrid')?>" class="btnfilter" /></td>
</tr>
</table>
<?
	}
	else
	//if (!empty($nDaysToShow) && !empty($nTimesToShow) && !empty($dStartDate))
	{
?>
<form action="<?=setPage($page, 0, 0, ACT_SAVE_MULTIPLE).keepFilter().keepContext()?>" enctype="multipart/form-data" method="post" name="ProgramDateTime" id="ProgramDateTime">
<?
		//$nDaysToShow = getPostedArg('nr_days', 5);
		//$nTimesToShow = getPostedArg('nr_times', 4);
		//$dStartDate = getPostedArg('start_date', $dToday);
		
		echo '<table class="grid" summary="ProgramDateTimeMultiple">
			<thead>
			<tr><td>'.getLabel('strFillAll').'</td>'."\n";
		
		// DRAW HEADER CELLS
		$sHeadSelect = '';
		$sHeadTimes = '';
		$sHeadTimes .= '<tr><td>'.getLabel('strStartTime').'</td>'."\n";
		for ($j=0; $j<$nTimesToShow; $j++)
		{
			// onclick="checkDown'.$times.'(this);"
			$sHeadSelect .= '<td>
				<input type="checkbox" maxlength="8" onclick="checkDown(this, \'time_'.$j.'\');" value="1"
					name="time_selector_'.$j.'" id="time_selector_'.$j.'" /> 
				<input type="text" maxlength="16" onkeyup="fillDown(this, \'fldtime price_'.$j.'\');" class="fldtime"
					name="price_selector_'.$j.'" id="price_selector_'.$j.'" />
			</td>'."\n";
			// onkeyup="fillDown'.$times.'(this);"
			$sHeadTimes .= '<td><input type="text" class="fldtime" name="program_times['.$j.']" id="program_time_'.$j.'" /></td>'."\n";
		}
		$sHeadSelect .= '</tr>'."\n";
		$sHeadTimes .= '</tr></thead>'."\n";
		echo $sHeadSelect.$sHeadTimes;
		
		// DRAW ITEM CELLS
		$sBodyRows = '<tbody id="LookThis">';
		for($i=0; $i<$nDaysToShow; $i++)
		{
			$dDateToDisplay = increaseDate($dStartDate, $i);
			$sBodyRows .= '<tr><td>'.formatDate($dDateToDisplay, FULL_DATE_DISPLAY_FORMAT).'
				<input type="hidden" name="dates['.$i.']" id="dates_'.$i.'" value="'.$dDateToDisplay.'" /></td>'."\n";
			for ($j=0; $j<$nTimesToShow; $j++)
			{
				$sBodyRows .= '<td><input type="checkbox" value="1" class="time_'.$j.'"
					name="times['.$i.']['.$j.']" id="time_'.$i.'_'.$j.'" />
					<input type="text" maxlength="16" class="fldtime price_'.$j.'"
					name="prices['.$i.']['.$j.']" id="price_'.$i.'_'.$j.'" /></td>'."\n";//class="fldtime" 
			}
			$sBodyRows .= '</tr>'."\n";
		}
		echo $sBodyRows;
		
		// DRAW FOOTER
		echo '</tbody>
		</table>
		<br />
		<input type="submit" value="'.getLabel('strSave').'" class="btn" />';
	}
?>
</form>
<?
}
//=========================================================
function showForm($aRows=null)
{
	global $page, $oUser, $aLanguages, $aAbbrLanguages;
	
	//require_once "../FCKeditor/fckeditor.php";
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->ProgramDateTimeID;
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
	}
	echo getLabel('strRequired').'<br />'."\n";
?>
<script type="text/javascript">
<!--
function fCheck(frm) {
	if (!valEmpty("program_date", "<?=getLabel('strEnter').getLabel('strProgramDate')?>")) return false;
	if (!valEmpty("program_time", "<?=getLabel('strEnter').getLabel('strProgramTime')?>")) return false;
	return true;
}
//-->
</script>
<form action="<?=setPage($page, 0, $itemID, ACT_SAVE).keepFilter().keepContext()?>" enctype="multipart/form-data" method="post" name="ProgramDateTime" id="ProgramDateTime" onsubmit="return fCheck(this);">

<fieldset title="<?=getLabel('strCommonData')?>">
	<legend><?=getLabel('strCommonData')?></legend>
	<label for="id"><?=getLabel('strProgramDateTimeID')?></label><?=$itemID?>
	<input type="hidden" name="id" id="id" value="<?=$itemID?>" /><br />
	<br />
	
	<label for="program_date"><?=getLabel('strProgramDate').formatVal()?></label>
	<input type="text" name="program_date" id="program_date" maxlength="10" class="fldfilter"
		value="<?=IIF(!empty($aRows[DEF_LANG]), formatDate(htmlspecialchars($aRows[DEF_LANG]->ProgramDate)), '')?>" 
	        onfocus="this.select();lcs(this)" onclick="event.cancelBubble=true;this.select();lcs(this)" /><br />
	<br />
	
	<label for="program_time"><?=getLabel('strProgramTime').formatVal()?></label>
	<input type="text" name="program_time" id="program_time"
		value="<?=IIF(!empty($aRows[DEF_LANG]), formatTime(htmlspecialchars($aRows[DEF_LANG]->ProgramTime)), '')?>" maxlength="10" class="fldfilter" /><br />
	<br />
	
	<label for="price"><?=getLabel('strPrice')?></label>
	<input type="text" name="price" id="price"
		value="<?=IIF(!empty($aRows[DEF_LANG]), htmlspecialchars($aRows[DEF_LANG]->Price), '')?>" maxlength="64" class="fldfilter" /><br />
	<br />
	
	<label><?=getLabel('strHide')?></label>
<?
	reset($aYesNo);
	$bHidden = false;
	if (!empty($aRows[DEF_LANG]))
		$bHidden = $aRows[DEF_LANG]->IsHidden;
	while(list($key, $value) = each($aYesNo)) 
	{
		?>
		<input type="radio" name="hidden" id="hidden_<?=$key?>" value="<?=$key?>"<?=IIF($key==$bHidden,' checked="checked"','')?> />
		<label class="list" for="hidden_<?=$key?>"><?=$value?></label>
<? 	} ?>
	<br class="clear" />
	<br />
<?
	if (!empty($aRows[DEF_LANG]))
	{
	?>
	<label><?=getLabel('strLastUpdate')?></label>
	<?=IIF(!empty($aRows[DEF_LANG]), $aRows[DEF_LANG]->LastUpdate, '').IIF(!is_null($rUser), getLabel('strByUser').$rUser->Username.' ('.$rUser->FirstName.' '.$rUser->LastName.')', '')?><br />
<? 	} ?>
</fieldset>

<?
	//foreach($aLanguages as $key=>$val)
	//{
?>
<!--fieldset title="<?=$val?>" style="float:left">
	<legend><?=$val?></legend>
</fieldset-->
<?
	//}
?>
	<br class="clear" />
	<br />
	<input type="submit" value="<?=getLabel('strSave')?>" class="btn" />
</form>
<?
}
//=========================================================
function showPreview($aRows=null)
{
	global $page, $oUser, $aLanguages;
	
	$itemID = 0;
	$rUser = null;
	$aYesNo = getLabel('aYesNo');
	if (!empty($aRows))
	{
		$itemID = $aRows[DEF_LANG]->ProgramDateTimeID;
	}
	if (!empty($itemID))
	{
		$rUser = $oUser->GetByID($aRows[DEF_LANG]->LastUpdateUserID);
?>
<fieldset title="<?=getLabel('strCommonData')?>">
	<legend><?=getLabel('strCommonData')?></legend>
	<label><?=getLabel('strProgramDateTimeID')?></label>
	<?=$aRows[DEF_LANG]->ProgramDateTimeID?><br />
	<br />
	<label><?=getLabel('strProgramDate')?></label>
	<?=formatDate($aRows[DEF_LANG]->ProgramDate)?><br />
	<br />
	<label><?=getLabel('strProgramTime')?></label>
	<?=formatTime($aRows[DEF_LANG]->ProgramTime)?><br />
	<br />
	<label><?=getLabel('strPrice')?></label>
	<?=$aRows[DEF_LANG]->Price?><br />
	<br />
	<label><?=getLabel('strHide')?></label>
	<?=$aYesNo[$aRows[DEF_LANG]->IsHidden]?><br />
	<br />
	<label><?=getLabel('strLastUpdate')?></label>
	<?=IIF(!empty($aRows[DEF_LANG]), $aRows[DEF_LANG]->LastUpdate, '').getLabel('strByUser').IIF(!empty($aRows[DEF_LANG]), $rUser->Username.' ('.$rUser->FirstName.' '.$rUser->LastName.')', '')?><br />
</fieldset>
	
<?
	foreach($aLanguages as $key=>$val)
	{
?>
<!--fieldset title="<?=$val?>">
	<legend><?=$val?></legend>
</fieldset-->
<?
	}
?>
<ul class="nav">
	<li><a href="<?=setPage($page, 0, $itemID, ACT_EDIT).keepFilter().keepContext()?>"><?=getLabel('edit')?></a></li>
	<li><a href="<?=setPage($page, 0, $itemID, ACT_DELETE).keepFilter().keepContext()?>" onclick="return confMsg('<?=getLabel('strDeleteQ')?>');return false;"><?=getLabel('delete')?></a></li>
</ul>
<?
	}
}
?>