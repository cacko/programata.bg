<?php
if (!$bInSite) die();
?>
<script type="text/javascript">
<!--
function srcCheck(frm)
{
	if (!valEmpty("<?=ARG_PAGE?>", "<?=getLabel('strWhere')?>")) return false;
	return true;
}
//-->
</script>
<form name="search_it" id="search_it" action="<?=setPage($page, $cat, $item, $action, $relitem, $city)?>"
								  method="get" onsubmit="return srcCheck(this);">
<select name="sel_date" id="sel_date" class="fld">
	<option value=""><?=getLabel('strThisWeek')?></option>
<?
	// Date looks like "yyyy-mm-dd"
	$dToday = date(DEFAULT_DATE_DB_FORMAT);
	$dSelDate = getQueryArg('sel_date');
	for($i = 0; $i < THIS_WEEK_DAYS; $i++)
	{
		$dte = increaseDate($dToday, $i);
		$key = formatDate($dte, DEFAULT_DATE_DB_FORMAT);
		$val = formatDate($dte, FULL_DATE_DISPLAY_FORMAT);
		//echo '<option value="'.$dte.'" '.IIF(($start_date==$dte && $start_date == $end_date)," selected","").'>';
		echo '<option value="'.$key.'"'.IIF($dSelDate==$key, ' selected="selected"','').'>'.$val.'</option>'."\n";
	}
?>
</select>

<select name="sel_time" id="sel_time" class="fld">
	<option value=""><?=getLabel('strAnyTime')?></option>
<?
	$dSelTime = getQueryArg('sel_time', '');
	foreach($aTimes as $key=>$val)
	{
		echo '<option value="'.$key.'"'.IIF($dSelTime==$key, ' selected="selected"','').'>'.$val.'</option>'."\n";
	}
?>
</select>

<select name="<?=ARG_CITY?>" id="<?=ARG_CITY?>" class="fld">
	<option value=""><?=getLabel('strWhichCity')?></option>
<?
	$aCities = getLabel('aCities');
	foreach($aCities as $key=>$val)
	{
		echo '<option value="'.$key.'"'.IIF($city==$key, ' selected="selected"','').'>'.$val.'</option>'."\n";
	}
?>
</select>

<select name="<?=ARG_PAGE?>" id="<?=ARG_PAGE?>" class="fld">
	<option value=""> <?=getLabel('strWhichSection')?></option>
<?
	$aPages = $oPage->ListAllAsArraySimple(DEF_PAGE);
	foreach ($aPages as $key=>$val)
	{
		if (!in_array($key, $aSysNavigation))
		{
			$aSubPages = $oPage->ListAllAsArraySimple($key, '', 'event_list');
			if (count($aSubPages)>0)
			{
				echo '<optgroup label="'.$val.'">'."\n";
				foreach ($aSubPages as $k=>$v)
					echo '<option value="'.$k.'"'.IIF($page==$k, ' selected="selected"','').'>'.$v.'</option>'."\n";
				echo '</optgroup>'."\n";
			}
			else
			{
				//echo '<option value="'.$key.'"'.IIF($page==$key, ' selected="selected"','').'>'.$val.'</option>'."\n";
			}
		}
	}
?>
</select>
<input type="submit" value="<?=getLabel('strGoSearch')?>" class="btn" />
</form>