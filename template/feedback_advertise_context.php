<?php
if (!$bInSite) die();
//=========================================================
?>
	<!--h4 title="<?=getLabel('strDownload')?>"><?=getLabel('strDownload')?></h4>
	<div class="text">
		<ul>
			<li><a href="doc/market_survey_<?=$aAbbrLanguages[$lang]?>_2004.pdf"><?=getLabel('strProgramataTGI')?></a></li>
			<li><a href="doc/readers_profile_<?=$aAbbrLanguages[$lang]?>_2004.pdf"><?=getLabel('strReadersProfile')?></a></li>
			<li><a href="doc/prices.xls"><?=getLabel('strPricelistOffline')?></a></li>
			<li><a href="doc/www_prices.pdf"><?=getLabel('strPricelistOnline')?></a></li>
		</ul>
	</div-->
	<?
	$rsAttachment = $oAttachment->ListAll($page, $aEntityTypes[ENT_PAGE], 6);
	if (mysql_num_rows($rsAttachment)>0)
	{
	?>
	<h4 title="<?=getLabel('strDownload')?>" class="first"><?=getLabel('strDownload')?></h4>
	<div class="text">
		<ul>
		<?
			while($row = mysql_fetch_object($rsAttachment))
			{
				$sMainFile = UPLOAD_DIR.FILE_ATTACHMENT.$row->AttachmentID.'.'.$row->Extension;
				echo '<li><a href="'.$sMainFile.'" target="_blank">'.$row->Title.'</a></li>'."\n";
			}
		?>
		</ul>
	</div>
	<?
	}
	?>
	<h4 title="<?=getLabel('strStats')?>"><?=getLabel('strStats')?></h4>
	<div class="text">
		<ul>
			<li><a href="http://www.tyxo.bg/?11669" target="_blank">tyxo.bg</a></li>
			<li><a href="http://bgcounter.com/?_id=programa" target="_blank">bgcounter.com</a></li>
			<li><a href="http://www.alexa.com/data/details/main?url=http://www.programata.bg+++++" target="_blank">alexa.com</a></li>
		</ul>
	</div>

