<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	$rMulty = $oMulty->GetByID($item);
	if ($rMulty)
	{
		
		$nEntityType = $aEntityTypes[ENT_MULTY];
		include('template/comment_save.php');

		$sItemTitle = stripComments($rMulty->Title);
		$oMulty->TrackView($item);

		// =========================== LINKS ===========================
		$sLink = '';
		$rsLink = $oLink->ListAll($rMulty->MultyID, $aEntityTypes[ENT_MULTY], array(1,2,3,0));
		if(mysql_num_rows($rsLink))
		{
			$sLink .= '<br />';
			$aLinkTypes = getLabel('aLinkTypes');
			while($rLink = mysql_fetch_object($rsLink))
			{
				if ($rLink->LinkTypeID == 2 || $rLink->LinkTypeID == 3)
					$sLink .= '<a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$aLinkTypes[$rLink->LinkTypeID].'</a><br />';
				else
					$sLink .= '<div class="link"><strong>'.getLabel('strUrl').'</strong> <a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$rLink->Title.'</a><br />';
			}
		}
		// =========================== COMMENTS ===========================
		$nComments = $oComment->GetCountByEntity($rMulty->MultyID, $aEntityTypes[ENT_MULTY]);
		// =========================== IMAGES ===========================
		$sGalleryToDisplay = '';
		$sGallery = showGallery($rMulty->MultyID, ENT_MULTY, false, false, true);
		if (!empty($sGallery))
//			$sGalleryToDisplay .= '<h4>'.getLabel('strGallery').'</h4>
//						<a name="galleria"></a>'.$sGallery."\n";
			$sGalleryToDisplay .= '<div class="gallery"><ul>'.$sGallery .'</ul></div>';		
	
		// =========================== PUBLICATION INFO ===========================
?>		
	  <div id="container">
    	<div id="article">			
<?php		
			$partsNum = $oMulty->GetPartsNum($rMulty->MultyID);
			for($part = 1; $part <=$partsNum; ++$part)
			{
				$image = UPLOAD_DIR.IMG_MULTY.$rMulty->MultyID.'/'.IMG_MID.$part.'.'.EXT_IMG;
				$image_big = UPLOAD_DIR.IMG_MULTY.$rMulty->MultyID.'/'.IMG_MID.$part.'.'.EXT_IMG;
				
				
				if($part == 1){
				
		        	echo '	<div class="main-preview">
		            			<a href="'. $image .'" title="'. htmlspecialchars(strip_tags(stripComments($rMulty->Title))) .'">'. drawImage($image, 470, 255) .'</a>
		                		<h1>'. htmlspecialchars(strip_tags(stripComments($rMulty->Title))) .'</h1>
		            		</div><br />';

					if($page == 168) //day by day
					{
						$title = $rMulty->Title;
						$start_pos = strpos($title, '[');
						if($start_pos)
						{
							$event_date = substr($title, strpos($title, '[') + 1, strpos($title, ']') - strpos($title, '[') - 1);
							preg_match('/^\D*(\d{1,2})\D+(\d{1,2})\D+(\d{2,4}).*$/', $event_date, $aMatches);
							
								$year = $aMatches[3];
								$month = $aMatches[2];
								$day = $aMatches[1];
								$timestamp = $year.'-'.$month.'-'.$day;
								$event_date = formatDate($timestamp, FULL_DATE_YEAR_DISPLAY_FORMAT);
						}
						else
						{
							$event_date = formatDate($row->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT);
						}

						echo '<div class="text summary">'.$event_date.getLabel('strByUser').$rMulty->Author .'</div>';
					}
					else
					{
						echo '<div class="text summary">'. formatDate($rMulty->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT).getLabel('strByUser').$rMulty->Author .'</div>';
					}
					
					echo '<div class="fb-like" data-send="true" data-width="450" data-show-faces="true"></div>';
									
					
		        	$text = $oMulty->GetPartText($rMulty->MultyID, $part, $lang);
		        	echo '<div class="text">'. $text .'</div><br />';
	
		        	continue;
				}
				
				if($page == 170) //photocity
				{
					echo '<div class="fit_images"><a href="'.$image_big.'">'.drawImage($image, 470, 255).'</a></div>';
				} else {
					echo '<div class="fit_images"><a href="'.$image_big.'">'.drawImage($image, 470, 255).'</a></div>';
				}
				$text = $oMulty->GetPartText($rMulty->MultyID, $part, $lang);

				echo '<div class="text"><br />'. $text .'</div><br />';

			}
			$set_fb_like_to_bottom = false;
		include('template/comment_list.php');
	}
	else
		$item = 0;
	echo '
			<script type="text/javascript">
					$(".main-preview a").lightBox();
					$(".fit_images a").lightBox();
			</script>	
	</div></div>';
}


//////// OLD

//if (isset($item) && !empty($item))
//{
//	$rMulty = $oMulty->GetByID($item);
//	if ($rMulty)
//	{
//		$nEntityType = $aEntityTypes[ENT_MULTY];
//		include('template/comment_save.php');
//
//		$sItemTitle = $rMulty->Title;
//		$oMulty->TrackView($item);

		// =========================== LINKS ===========================
//		$sLink = '';
//		$rsLink = $oLink->ListAll($rMulty->MultyID, $aEntityTypes[ENT_MULTY], array(1,2,3,0));
//		if(mysql_num_rows($rsLink))
//		{
//			$sLink .= '<br />';
//			$aLinkTypes = getLabel('aLinkTypes');
//			while($rLink = mysql_fetch_object($rsLink))
//			{
//				if ($rLink->LinkTypeID == 2 || $rLink->LinkTypeID == 3)
//					$sLink .= '<a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$aLinkTypes[$rLink->LinkTypeID].'</a><br />';
//				else
//					$sLink .= getLabel('strUrl').': <a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$rLink->Title.'</a><br />';
//			}
//		}
		// =========================== COMMENTS ===========================
//		$nComments = $oComment->GetCountByEntity($rMulty->MultyID, $aEntityTypes[ENT_MULTY]);
		// =========================== IMAGES ===========================
//		$sGalleryToDisplay = '';
//		$sGallery = showGallery($rMulty->MultyID, ENT_MULTY, false);
//		if (!empty($sGallery))
//			$sGalleryToDisplay .= '<h4>'.getLabel('strGallery').'</h4>
//						<a name="galleria"></a>'.$sGallery."\n";

		// =========================== PUBLICATION INFO ===========================
			// jacobs branding
//			if($page == 146){
//				$logo = 'img/jacobs_top.jpg';
//				echo drawImage($logo);
//			}
		?>
		
<!--		<h2 title="<?=htmlspecialchars(strip_tags($rMulty->Title))?>"><?=stripComments($rMulty->Title)?></h2>-->
		<?
//		if($page == 170) //photocity
//		{
		?>
		<!--<div class="box detail" id="multyImages">
			<script type="text/javascript">
				$('#multyImages').ready(function() {
					$('#multyImages a').lightBox();
				});
			</script>-->

<?
//		} else {
			?>
<!--			<div class="box detail">-->
			<?
//		} 
//			$partsNum = $oMulty->GetPartsNum($rMulty->MultyID);
//			for($part = 1; $part <=$partsNum; ++$part)
//			{
//				$image = UPLOAD_DIR.IMG_MULTY.$rMulty->MultyID.'/'.IMG_MID.$part.'.'.EXT_IMG;
//				$image_big = UPLOAD_DIR.IMG_MULTY.$rMulty->MultyID.'/'.IMG_BIG.$part.'.'.EXT_IMG;
//				if($page == 170) //photocity
//				{
//					echo '<div><a href="/'.$image_big.'">'.drawImage($image).'</a></div>';
//				} else {
//					echo '<div>'.drawImage($image).'</div>';
//				}
//				$text = $oMulty->GetPartText($rMulty->MultyID, $part, $lang);
?>
<!--				<div><br /><?=$text?></div>-->
<?
//			}
?>
			<!--<br />
			<div class="date"><?=formatDate($rMulty->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT).getLabel('strByUser').$rMulty->Author?></div>
			<div><br /><?=$sLink?></div>
		</div>-->
<?
//		echo IIF(!empty($sGalleryToDisplay), $sGalleryToDisplay.'<br class="clear" />', '');

//		include('template/comment_list.php');
//	}
//	else
//		$item = 0;
//}
?>