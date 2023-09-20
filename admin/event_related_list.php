<?php
$bInSite = true;
require_once('../initialize.php');
//=========================================================
define('SITES_ROOT','/raid/www/programata.bg');

function validateFiles($sPath, $sFilename, $mode=false) {
	$dir = SITES_ROOT.$sPath;
	// Open a known directory, and proceed to read its contents
	if (is_dir($dir))
	{
		if ($dh = opendir($dir))
		{
			
			while (($file = readdir($dh)) != false)
			{
				if (strpos($file, $sFilename) != false)
				{
					if ($mode==false)
						echo $file. "<br />";
					else
					{
						unlink($file);
					}
				}
			}
			closedir($dh);
		}
	}
}
$mode = getRequestArg('mode');
$relid = getRequestArg('relid');
$handle = getRequestArg('handle');
if (!empty($mode) && !empty($relid)  && !empty($handle))
{
	validateFiles($relid, $handle, IIF($mode == 'ala', true, false));
}
else
{
//=========================================================
$aPages = $oPage->ListAllAsArray(null, '', 'event_list', true);
$aEventTypes = getLabel('aEventTypes');
$aEventSubtypes = getLabel('aEventSubtypes');
//=========================================================
?>
<form action="<?=setPage($page, $cat)?>" method="post" name="PageFilter" id="PageFilter">
<table summary="filter" class="form">
<tr>
	<td><label for="keyword"><?=getLabel('strKeyword')?></label><br />
	<input type="text" name="keyword" id="keyword" maxlength="64" class="fldfilter" value="<?=getRequestArg('keyword')?>" /></td>
	<!--td><label for="parent_page"><?=getLabel('strParentPage')?></label><br />
	<select name="parent_page" id="parent_page" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aPages as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('parent_page'))
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
		</select></td-->
	<td><label for="event_type"><?=getLabel('strEventType')?></label><br />
	<select name="event_type" id="event_type" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aEventTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('event_type', $cat))
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
		</select></td>
	<td><br /><input type="submit" value="<?=getLabel('strSearch')?>" class="btnfilter" /></td>
</tr>
</table>
</form>
<?
//=========================================================
	$aAlphabet = getLabel('aAlphabet');
	
	$sLetter = '';
	$nLetter = getRequestArg('letter');
	if (!empty($nLetter))
		$sLetter = $aAlphabet[$nLetter];

	echo '<ul class="alpha">'."\n";
	foreach($aAlphabet as $key=>$letter)
	{
		echo '<li'.IIF(!empty($nLetter) && $nLetter==$key, ' class="on"', '').'><a href="'.setPage($page, $cat).'&amp;letter='.$key.'">'.$letter.'</a></li>'."\n";
	}
	echo '</ul>'."\n";
//=========================================================
	$aFilter = array('parent_page', 'keyword', 'event_type', 'letter', ARG_CAT);
	$nEventType = getRequestArg('event_type');
	$sKeyword = getRequestArg('keyword');
	if (empty($nEventType) && empty($sKeyword) && empty($sLetter))
	{
		echo getLabel('strSearchRequired');
	}
	else
	{
	$rs = $oEvent->ListAll(getRequestArg('parent_page',null), getRequestArg('event_type',$cat), null,
			       getRequestArg('keyword'), $sLetter, true, 0, $aContext);
	if (mysql_num_rows($rs))
	{
?>
<script type="text/javascript">
<!--
	function setPrimary(event_id, event_title)
	{
		var nodeEventID = parent.document.getElementById('event_id');
		var nodeEventTitle = parent.document.getElementById('event_title');
		if (nodeEventID)
		{
			nodeEventID.value = event_id;
		}
		if (nodeEventTitle)
		{
			nodeEventTitle.innerHTML = event_title;
		}
	}
	
	function setSecondary(event_id, event_title)
	{
		var nodeEventID = parent.document.getElementById('rel_event_id');
		var nodeEventTitle = parent.document.getElementById('rel_event_title');
		if (nodeEventID)
		{
			if (nodeEventID.value != '')
				nodeEventID.value += ',' ;
			nodeEventID.value += event_id;
		}
		if (nodeEventTitle)
		{
			nodeEventTitle.innerHTML += event_title + '<br />';
		}
	}
// -->
</script>
<table summary="data" class="grid">
<thead>
<tr>
	<td></td>
	<td><a href="<?=setPageContext(setContext(1,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strEventID')?></a></td>
	<td><a href="<?=setPageContext(setContext(2,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strEventName')?></a> (<a href="<?=setPageContext(setContext(16,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strNrViews')?></a>)</td>
	<td></td>
</tr>
</thead>
<tbody>
<?
		$aYesNo = getLabel('aYesNo');
		$aUsers = $oUser->ListAllAsArray(USER_ADMIN);
		
		$curRow = 0;
		$nTotalRows = mysql_num_rows($rs);
		$nStartRow = $aContext[CUR_REC];
		$nEndRow = $nStartRow + NUM_ROWS_ADMIN;
		while ($row = mysql_fetch_object($rs))
		{
			if ($curRow >= $nStartRow && $curRow < $nEndRow)
			{
?>
<tr onmouseover="lt(this,1)" onmouseout="lt(this,0)"<?=IIF($row->IsHidden == true, ' class="hidden"', '')?>>
	<td><a href="#" onclick="setPrimary(<?=$row->EventID?>, '<?=$row->Title?>');return false;"><?=getLabel('strSelectAsPrimary')?></a><br />
		<a href="#" onclick="setSecondary(<?=$row->EventID?>, '<?=$row->Title?>');return false;"><?=getLabel('strSelectAsSecondary')?></a></td>
	<td><?=$row->EventID?></td>
	<td><?=$row->Title?></td>
	<td><a target="_blank" href="index.php<?=setPage($page, 0, $row->EventID, ACT_VIEW).keepFilter().keepContext()?>"><?=getLabel('view').getLabel('new_window')?></a></td>
</tr>
<?
			}
			$curRow++;
		}
?>
</tbody>
<tfoot>
<tr>
	<td colspan="2"><?=showPaging($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_ADMIN, $aFilter);?></td>
	<td colspan="3" align="right"><?=showPages($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_ADMIN, $aFilter);?></td>
</tr>
</tfoot>
</table>
<?
	}
	else
	{
?>
<div><?=getLabel('strNoRecords')?></div>
<?
	}
	}
}
?>