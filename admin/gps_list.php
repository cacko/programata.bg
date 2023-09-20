<?php
if (!$bInSite) die();
//=========================================================
	$aFilter = array(ARG_RELID, ARG_CAT);
	
	$rs = $oPlace->GetXY(getRequestArg('entity_id', $relitem));
	if (mysql_num_rows($rs))
	{
?>
<table summary="data" class="grid">
<thead>
<tr>
	<td><?=getLabel('strLat')?></td>
	<td><?=getLabel('strLong')?></td>
	<td></td>
</tr>
</thead>
<tbody>
<?
		$row = mysql_fetch_object($rs);
		{
?>
<tr>
	<td><?=$row->lat?></td>
	<td><?=$row->long?></td>
	<td><a href="<?=setPage($page, 0, $row->PlaceID, ACT_EDIT).keepFilter().keepContext()?>"><?=getLabel('edit')?></a></td>
</tr>
<?
		}
?>
</tbody>
</table>
<?
	}
	else
	{
?>
<div><?=getLabel('strNoRecords')?></div>
<?
	}
?>