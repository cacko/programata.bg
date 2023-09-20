<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	include('template/event_details.php');
}
elseif (!isset($item) || empty($item))
{

}
else
{
	$sListContent .= '<div>'.getLabel('strNoRecords').'</div>'."\n";
}
	echo $sListContent;
?>