<?php
if (!$bInSite) die();
//=========================================================
	$sImageFile = UPLOAD_DIR.'old_school_party/'.$rEvent->EventID.'/medium_1.'.EXT_IMG;

	$sBigImageFile = UPLOAD_DIR.'old_school_party/'.$rEvent->EventID.'/big_1.'.EXT_IMG;


?>
	<script type="text/javascript">
		$(document).ready(function() {
			$('#oldSchoolPartyImages a').lightBox();
		});
	</script>
	<div id="oldSchoolPartyImages">
	<a href="/<?=$sBigImageFile?>" rel="<?=$relation?>"><?=drawImage($sImageFile, 0, 0, "click to enlarge")?></a>
	<?=IIF(!empty($sRating), '<div class="rating">'.$sRating.'</div>', '')?>
	<?=IIF(!empty($sRatingForm), $sRatingForm, '')?>
	<br class="clear" />
	<?=IIF(!empty($rEvent->OriginalTitle), '<div>'.getLabel('strOriginalTitle').': '.$rEvent->OriginalTitle.'</div>', '')?>
	<div><?=IIF(!empty($sCategory), $sCategory.'<br />', '').
		$sLabels.
		IIF(!empty($rEvent->Features), str_replace(' &middot; ', '<br />', strip_tags($rEvent->Features, '<a><br>')).'<br />', '').
		IIF(!empty($rEvent->Comment), str_replace(' &middot; ', '<br />', strip_tags($rEvent->Comment, '<a><br>')), '')?></div>
	<?=IIF(!empty($rEvent->Lead), '<div class="lead">'.$rEvent->Lead.'</div>', '')?>
	<?=IIF(!empty($rEvent->Description), '<div><br />'.$rEvent->Description.'</div>', '')?>
	<br class="clear" />
<?
	foreach (array(2, 3, 4) as $part)
	{
		$sImageFile = UPLOAD_DIR.'old_school_party/'.$rEvent->EventID.'/medium_'.$part.'.'.EXT_IMG;
		$sBigImageFile = UPLOAD_DIR.'old_school_party/'.$rEvent->EventID.'/big_'.$part.'.'.EXT_IMG;

?>
		<a href="/<?=$sBigImageFile?>"><?=drawImage($sImageFile, 0, 0, "click to enlarge")?></a>
	<br class="clear"/>
<?
	}
?>
	<div><br /><?=IIF(!empty($sAddress), getLabel('strAddress').': '.$sAddress, '').$sPhone.$sEmail.$sLink?></div>
	</div>