<?php
if (!$bInSite) die();
//=========================================================
$aPages = $oPage->ListAllAsArray(null, '', 'place_list', true);
$aCities = getLabel('aCities');
$aPlaceTypes = getLabel('aPlaceTypes');
//=========================================================
?>
<form action="<?=setPage($page, $cat, 0, ACT_REPORT)?>" method="post" name="PageFilter" id="PageFilter">
<table summary="filter" class="form">
<tr>
	<td><label for="sel_city"><?=getLabel('strCity')?></label><br />
	<select name="sel_city" id="sel_city" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aCities as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('sel_city', $nDefCity))
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
		</select></td>
	<td><label for="place_type"><?=getLabel('strPlaceType')?></label><br />
	<select name="place_type" id="place_type" class="fldfilter">
		<option value=""><?=getLabel('strAny')?></option>
	<?
		foreach($aPlaceTypes as $key=>$val)
		{
			echo '<option value="'.$key.'"';
			if ($key == getRequestArg('place_type', $cat))
				echo ' selected="selected"';
			echo '>'.$val.'</option>'."\n";
		}
	?>
		</select></td>
	<td><label for="parent_page"><?=getLabel('strParentPage')?></label><br />
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
		</select></td>
	<td><br /><input type="submit" value="<?=getLabel('strSearch')?>" class="btnfilter" /></td>
</tr>
</table>
</form>
<?
//=========================================================
	$aFilter = array('parent_page', 'sel_city', 'place_type', ARG_CAT);
	
	$nSelPage = getRequestArg('parent_page');
	$nSelType = getRequestArg('place_type', $cat);
	$nSelCity = getRequestArg('sel_city', $nDefCity);
	if (!empty($nSelPage) || !empty($nSelType) || !empty($nSelCity))
	{
		$rs = $oPlace->ListAll(getRequestArg('parent_page',null), getRequestArg('sel_city', $nDefCity),
				       getRequestArg('place_type',$cat), null, '', '', false, true, 0, $aContext);
		if (mysql_num_rows($rs))
		{
			while ($row = mysql_fetch_object($rs))
			{
				$sAddress = '';
				$rsAddress = $oAddress->ListAll($row->PlaceID, $aEntityTypes[ENT_PLACE], 1); //$row->PlaceTypeID
				if(mysql_num_rows($rsAddress))
				{
					while($rAddress = mysql_fetch_object($rsAddress))
					{
						$sAddress .= $rAddress->Street.'<br />';
					}
				}
				
				$sPhone = '';
				if ($nSelPage == 98) // delivery
				{
					$rsPhone = $oPhone->ListAll($row->PlaceID, $aEntityTypes[ENT_PLACE], 6); //$row->PlaceTypeID
					if(mysql_num_rows($rsPhone))
					{
						$aPhoneTypes = getLabel('aPhoneTypes');
						while($rPhone = mysql_fetch_object($rsPhone))
						{
							$sPhone .= $aPhoneTypes[$rPhone->PhoneTypeID].': '.$rPhone->Area.' '.$rPhone->Phone.' '.$rPhone->Ext.'<br />';
						}
					}
				}
				
				echo '<br />
				<div><strong>'.stripComments($row->Title).'</strong></div>
				<div>'.IIF(!empty($sAddress), getLabel('strAddress').': '.$sAddress, '').
					IIF(!empty($row->WorkingTime), getLabel('strWorkingTime').': '.$row->WorkingTime.'<br />', '').$sPhone.'</div>'."\n";
			}
		}
		else
		{
			echo '<div>'.getLabel('strNoRecords').'</div>'."\n";
		}
	}
?>