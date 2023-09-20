<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	$rUrban = $oUrban->GetByID($item);
	if ($rUrban)
	{
		$nEntityType = $aEntityTypes[ENT_URBAN];
		include('template/comment_save.php');

		$sItemTitle = $rUrban->Title1;
		$oUrban->TrackView($item);

		// =========================== LINKS ===========================
		$sLink = '';
		$rsLink = $oLink->ListAll($rUrban->UrbanID, $aEntityTypes[ENT_URBAN], array(1,2,3,4,0));
		if(mysql_num_rows($rsLink))
		{
			$sLink .= '<br />';
			$aLinkTypes = getLabel('aLinkTypes');
			while($rLink = mysql_fetch_object($rsLink))
			{
				if ($rLink->LinkTypeID == 2 || $rLink->LinkTypeID == 3)
				$sLink .= '<a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$aLinkTypes[$rLink->LinkTypeID].'</a><br />';
				if ($rLink->LinkTypeID == 4)
				$sMoreInfoLink .= '<div class="more_info">'.getLabel('strMoreInfo').': <a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$rLink->Title.'</a></div>';
				else
				$sLink .= '<div class="link"><strong>'.getLabel('strUrl').'</strong> <a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$rLink->Title.'</a><br />';
			}
		}
		// =========================== COMMENTS ===========================
		$nComments = $oComment->GetCountByEntity($rUrban->UrbanID, $aEntityTypes[ENT_URBAN]);
		// =========================== IMAGES ===========================
		$sGalleryToDisplay = '';

		$sGallery = showGallery($rUrban->PublicationID, ENT_URBAN, false, false, true);
		if (!empty($sGallery))
//		$sGalleryToDisplay .= '<h4>'.getLabel('strGallery').'</h4>
//						<a name="galleria"></a>'.$sGallery."\n";
			$sGalleryToDisplay .= '<div class="gallery"><ul>'.$sGallery .'</ul></div>';
		// =========================== URBAN INFO ===========================

		?>
		
<!--<div class="box detail" id="UrbanDetailsImages">-->
<div id="container">
	<div id="article">
	<?php
		foreach (array(1, 2, 3) as $k => $part)
		{	

			$sMainImageFile = UPLOAD_DIR.IMG_URBAN.$item.'/'.IMG_BIG.$part.'_1.'.EXT_IMG;
			if (!is_file($sMainImageFile))
			{
				$sMainImageFile = UPLOAD_DIR.IMG_GALLERY.'.'.EXT_PNG;
			}			
			
			if(!empty($rUrban->{'Title'.$part})){
				$set_title = "<h1>".$rUrban->{'Title'.$part}."</h1>";
				$set_text = IIF($rUrban->{'Text'.$part}, $rUrban->{'Text'.$part},$rUrban->{'Title'.$part});
				if(!empty($rUrban->{'Author'.$part})){
					$another_title = '<h2>'. getLabel('strGalleryImageAuthor').' '.$rUrban->{'Author'.$part} .'</h2>';
				}else{
					$another_title = null;	
				}
			}else{
				$set_title = null;
			}

			echo '<div id="part_'. $k .'" style="background: url(public/images/dot.gif) repeat-x left bottom; margin-bottom: 20px;">
					<div class="main-preview">
				    	<a href="'. str_replace('mid','big',$sMainImageFile) .'" class="wallpepar" title="'. htmlspecialchars(strip_tags($set_text)) .'">'. drawImage($sMainImageFile, 470, 250, htmlspecialchars(strip_tags($set_title))) .'</a>';
			
			echo $set_title . $another_title;
			
			echo '	</div>';
			
			$sImageFile = UPLOAD_DIR.IMG_URBAN.$item.'/'.IMG_BIG.$part.'_1.'.EXT_IMG;
		
			$template_big = UPLOAD_DIR.IMG_URBAN.$item.'/'.IMG_BIG.$part.'_*.'.EXT_IMG;
			$files_big = glob($template_big);
		
			$template_mid_thumb = UPLOAD_DIR.IMG_URBAN.$item.'/'.IMG_BIG.$part.'_*.'.EXT_IMG;
			$files_mid_thumb = glob($template_mid_thumb);

			if(count($files_big) > 1){
				echo '<div class="gallery"><ul><li><a href="'. str_replace('mid','big',$sMainImageFile) .'" rel="part_'. $k .'" title="'. htmlspecialchars(strip_tags($set_title)) .'">'. drawImage($sMainImageFile, 138, 93, $set_title) .'</a></li>';
				$counter=1;
				for(;$counter<count($files_big); $counter++)
				{
					$sBigImageFile = $files_big[$counter];
					$sImageThumbFile = $files_mid_thumb[$counter];
	//				echo '<a href="/'.$sBigImageFile.'" title="'.$rUrban->{'Title'.$part}.'">'.drawImage($sImageThumbFile, 0, 0, $img->Title).'</a>';
					echo '<li><a href="'. $sImageThumbFile .'" rel="part_'. $k .'" title="'. $set_title .'">'. drawImage($sImageThumbFile, 138, 93, $set_title) .'</a></li>';
				}			
				
				echo '</ul></div>';
			}else{
				echo '<br />';
			}
			
			echo '
			<script type="text/javascript">
					$("#part_'. $k .' .main-preview a,#part_'. $k .' .gallery a").lightBox();
//					$("#part_'. $k .' .gallery a").lightBox();
			</script>			
			
			</div>';
		}
		
		if($page == 137){ //Urban Sketches
			echo getLabel('strUrbanFooter') .'<a href="mailto:snimki@programata.bg"> snimki@programata.bg</a>';
		}
		
		echo IIF(!empty($sMoreInfoLink), '<br />'. $sMoreInfoLink, '');
		$set_fb_like_to_bottom = true;
		include('template/comment_list.php');
	?>	
	</div>
</div>
<?


//foreach (array(1, 2, 3) as $part)
//{
//	if(!empty($rUrban->{'Title'.$part}))
//	{
//		echo "<h2 title=\"" . htmlspecialchars(strip_tags($rUrban->{'Title'.$part})) . "\">".$rUrban->{'Title'.$part}."</h2>";
//	}
//
//	$sMainImageFile = UPLOAD_DIR.IMG_URBAN.$item.'/'.IMG_MID.$part.'_1.'.EXT_IMG;
//	if (!is_file($sMainImageFile))
//	{
//		$sMainImageFile = UPLOAD_DIR.IMG_GALLERY.'.'.EXT_PNG;
//	}
//	
//	$sImageFile = UPLOAD_DIR.IMG_URBAN.$item.'/'.IMG_BIG.$part.'_1.'.EXT_IMG;
//
//	switch($page) {
//		case 136://x3
//			$relation = 'x3part'.$part.'Images';
			?>
<!--			<script type="text/javascript">
				$('#<?=$relation?>').ready(function() {
					$('#<?=$relation?> a').lightBox();
				});

			</script>-->
			<?php
//			break;
//
//		default:
//			$relation = "UrbanDetailsImages_".$part;
			?>
			<!--<script type="text/javascript">
				$('#UrbanDetailsImages').ready(function() {
					$('#UrbanDetailsImages a').lightBox();
				});
			</script>-->
			<?php
//	}
//
//
//	$template_big = UPLOAD_DIR.IMG_URBAN.$item.'/'.IMG_BIG.$part.'_*.'.EXT_IMG;
//	$files_big = glob($template_big);
//
//	$template_mid_thumb = UPLOAD_DIR.IMG_URBAN.$item.'/'.IMG_MID_THUMB.$part.'_*.'.EXT_IMG;
//	$files_mid_thumb = glob($template_mid_thumb);

	?> <!--<div id="<?=$relation?>"><a href="<?=$sImageFile?>"
	title="<?=htmlspecialchars(strip_tags(IIF($rUrban->{'Text'.$part}, $rUrban->{'Text'.$part},$rUrban->{'Title'.$part})))?>"><?=drawImage($sMainImageFile, 0, 0, $rUrban->{'Title'.$part})?></a>-->
	<?
//	$counter=1;
//	for(;$counter<count($files_big); $counter++)
//	{
//		$sBigImageFile = $files_big[$counter];
//		$sImageThumbFile = $files_mid_thumb[$counter];
//		echo '<a href="/'.$sBigImageFile.'" title="'.$rUrban->{'Title'.$part}.'">'.drawImage($sImageThumbFile, 0, 0, $img->Title).'</a>';
//	}

	?>
<!--<div class="date"><?=getLabel('strGalleryImageAuthor').' '.$rUrba{'Author'.$part}?></div>
<br />
<br />	</div>-->
	<? } ?><!--</div>-->
	<?
//	if($page == 137) //Urban Sketches
//	echo getLabel('strUrbanFooter').'<a href="mailto:snimki@programata.bg"> snimki@programata.bg</a>';
	?>
	<?php
		#IIF(!empty($sMoreInfoLink), '<div><br />'.$sMoreInfoLink.'</div>', '')
	?>
	<?
	#echo IIF(!empty($sGalleryToDisplay), $sGalleryToDisplay.'<br class="clear" />', '');
	?>

	<?php
	#include('template/comment_list.php');
	#}
	#else
	#$item = 0;
}
?>