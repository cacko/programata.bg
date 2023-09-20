<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	$rPublication = $oPublication->GetByID($item);
	if ($rPublication)
	{
						$set_fb_like_to_bottom = false; 
		$nEntityType = $aEntityTypes[ENT_PUBLICATION];
		include('template/comment_save.php');
		
		$sItemTitle = $rPublication->Title;
		$oPublication->TrackView($item);
		
		// =========================== LINKS ===========================
		$sLink = '';
		$rsLink = $oLink->ListAll($rPublication->PublicationID, $aEntityTypes[ENT_PUBLICATION], array(1,2,3,0));
		if(mysql_num_rows($rsLink))
		{
			$sLink .= '<br />';
			$aLinkTypes = getLabel('aLinkTypes');
			while($rLink = mysql_fetch_object($rsLink))
			{
				if ($rLink->LinkTypeID == 2 || $rLink->LinkTypeID == 3)
					$sLink .= '<a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$aLinkTypes[$rLink->LinkTypeID].'</a><br />';
				else
					$sLink .= '<div class="link"><strong>'. getLabel('strUrl').'</strong> <a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$rLink->Title.'</a></div>';
			}
		}
		// =========================== COMMENTS ===========================
		$nComments = $oComment->GetCountByEntity($rPublication->PublicationID, $aEntityTypes[ENT_PUBLICATION]);
		// =========================== IMAGES ===========================
		$sMainImageFile = UPLOAD_DIR.IMG_PUBLICATION.$rPublication->PublicationID.'.'.EXT_IMG;
		if (!is_file($sMainImageFile))
		{
			$sMainImageFile = UPLOAD_DIR.IMG_GALLERY.'.'.EXT_PNG;
		}
		$sGalleryToDisplay = '';
		$sGallery = showGallery($rPublication->PublicationID, ENT_PUBLICATION, false, false, true);
		if (!empty($sGallery))
			$sGalleryToDisplay .= '<div class="gallery"><ul>'.$sGallery .'</ul></div>';		
//			$sGalleryToDisplay .= '<h4>'.getLabel('strGallery').'</h4>
//						<a name="galleria"></a>'.$sGallery."\n";
		
		// =========================== PUBLICATION INFO =========================== 
?>
<!--		<h2 title="<?=htmlspecialchars(strip_tags($rPublication->Title))?>"><?=$rPublication->Title?></h2>
		<div class="box detail">
			<?//IIF(!empty($rPublication->Subtitle), '<div>'.$rPublication->Subtitle.'</div>', '')?>
			<?=drawImage($sMainImageFile, 0, 0, $row->Title)?>
			<div class="date"><?=formatDate($rPublication->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT).getLabel('strByUser').$rPublication->Author?></div>
			<!--ul class="inline_nav">
				<li class="comment"><a href="#comment_list"><?=getLabel('strComments')?></a> (<?=$nComments?>)</li>
				<? if (!empty($sGalleryToDisplay)) {?>
				<li class="gallery"><a href="#galleria"><?=getLabel('strGallery')?></a></li>
				<? }?>
				<li class="friend"><a href="#friend"><a href="mailto:?body=<?='http://'.SITE_URL.'/'.setPage($page, $cat, $item)?>"><?=getLabel('strTellFriend')?></a></li>
			</ul
			<?=IIF(!empty($rPublication->Lead), '<div class="lead">'.$rPublication->Lead.'</div>', '')?>
			<div><br /><?=$rPublication->Content?></div>
			<div><br /><?=$sLink?></div>
		</div>-->
		
	  <div id="container">
    	<div id="article">
        	<div class="main-preview">
            	<a href="<?=$sMainImageFile?>" id="wallpepar" title="<?php echo htmlspecialchars(strip_tags($rPublication->Title));?>"><?php echo drawImage($sMainImageFile, 470, 250, $row->Title); ?></a>
                <h1><?php echo htmlspecialchars(strip_tags($rPublication->Title));?></h1>
            </div>			
<?
		
		if(!empty($sGalleryToDisplay)){
			echo substr($sGalleryToDisplay,0, 25) .'<li><a href="'. $sMainImageFile .'" title="'. $row->Title .'">'. drawImage($sMainImageFile, 138, 93, $row->Title) .'</a></li>'. substr($sGalleryToDisplay,25);	
		}else{
			echo '<br /><br />';
		}
//		echo IIF(!empty($sGalleryToDisplay), substr(,$sGalleryToDisplay), '');

		echo '<div class="text summary">'. formatDate($rPublication->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT).getLabel('strByUser').$rPublication->Author .'</div>';
		echo '<div class="fb-like" data-send="true" data-width="450" data-show-faces="true"></div>';
		
		if(!empty($rPublication->Lead) || !empty($rPublication->Content)){
		   echo '<div class="text">';
		   echo IIF(!empty($rPublication->Lead), $rPublication->Lead .'<br /><br /><br />', '') . $rPublication->Content;
		   echo '</div>';
		}

echo '			<script type="text/javascript">
				if($(".gallery a").lightBox().length) {
					$(".main-preview a").click(function(event) {
						event.preventDefault();
						$(".gallery a[href=\'" + $(this).attr("href") + "\']").click();
					});
				} else {
					$(".main-preview a").lightBox();
				}
				
				</script>     ';		
		
//		echo IIF(!empty($sGalleryToDisplay), $sGalleryToDisplay.'<br class="clear" />', '');

		include('template/comment_list.php');
	echo '</div>
	  </div>';
	}
	else
		$item = 0;
}
?>