<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	$rNews = $oNews->GetByID($item);
	if ($rNews)
	{
						$set_fb_like_to_bottom = false;
		$nEntityType = $aEntityTypes[ENT_NEWS];
		include('template/comment_save.php');

		$sItemTitle = $rNews->Title;
		$oNews->TrackView($item);

		// =========================== LINKS ===========================
		$sLink = '';
		$rsLink = $oLink->ListAll($rNews->NewsID, $aEntityTypes[ENT_NEWS], array(1,2,3,0));
		if(mysql_num_rows($rsLink))
		{
			$sLink .= '<br />';
			$aLinkTypes = getLabel('aLinkTypes');
			while($rLink = mysql_fetch_object($rsLink))
			{
				if ($rLink->LinkTypeID == 2 || $rLink->LinkTypeID == 3)
					$sLink .= '<a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$aLinkTypes[$rLink->LinkTypeID].'</a><br />';
				else
					$sLink .= getLabel('strUrl').': <a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$rLink->Title.'</a><br />';
			}
		}
		// =========================== COMMENTS ===========================
		$nComments = $oComment->GetCountByEntity($rNews->NewsID, $aEntityTypes[ENT_NEWS]);
		// =========================== IMAGES ===========================
		$sMainImageFile = UPLOAD_DIR.IMG_NEWS.$rNews->NewsID.'.'.EXT_IMG;
		if (!is_file($sMainImageFile))
		{
			$sMainImageFile = UPLOAD_DIR.IMG_EMPTY.'.'.EXT_PNG;
		}
		$rsAttachment = $oAttachment->ListAll($rNews->NewsID, $aEntityTypes[ENT_NEWS], 7); //$rEvent->EventTypeID
		while($rAttachment = mysql_fetch_object($rsAttachment))
		{
			if($rAttachment->AttachmentTypeID == 7){
				$sTrailerFile = UPLOAD_DIR.FILE_TRAILER.$rAttachment->AttachmentID.'.'.$rAttachment->Extension;
			}
		}

		$sGalleryToDisplay = '';
		$sGallery = showGallery($rNews->NewsID, ENT_NEWS, false, false, true);

		if (!empty($sGallery))
				$sGalleryToDisplay .= '<div class="gallery"><ul>'.$sGallery .'</ul></div>';		
//			$sGalleryToDisplay .= '<h4>'.getLabel('strGallery').'</h4>
//						<a name="galleria"></a>'.$sGallery."\n";
			
		// =========================== NEWS INFO ===========================
?>
		<!--<h2 title="<?=htmlspecialchars(strip_tags($rNews->Title))?>"><?=$rNews->Title?></h2>
		<div class="box detail">-->
	  <div id="container">
    	<div id="article">
        	<div class="main-preview">
            	<a href="<?=$sMainImageFile?>" id="wallpepar" title="<?php echo stripComments(htmlspecialchars(strip_tags($rNews->Title)));?>"><?php echo drawImage($sMainImageFile, 470, 250, stripComments($rNews->Title)); ?></a>
                <h1><?php echo stripComments(htmlspecialchars(strip_tags($rNews->Title)));?></h1>
                <?php if(strlen($rNews->OriginalTitle) > 0){?>
<!--                <h2><?php echo getLabel('strOriginalTitle').': '.$rNews->OriginalTitle; ?></h2>-->
                <?php }else{ echo '<br/>'; } ?>
            </div>		
<?

			if(!empty($sGalleryToDisplay)){
				echo substr($sGalleryToDisplay,0, 25) .'<li><a href="'. $sMainImageFile .'" title="'. $row->Title .'">'. drawImage($sMainImageFile, 138, 93, $row->Title) .'</a></li>'. substr($sGalleryToDisplay,25);	
			}else{
				echo '<br/><br/>';
			}

		
			echo '
			<script type="text/javascript">
					if($(".gallery a").lightBox().length) {
						$(".main-preview a").click(function(event) {
							event.preventDefault();
							$(".gallery a[href=\'" + $(this).attr("href") + "\']").click();
						});
					} else {
						$(".main-preview a").lightBox();
					}
					
			</script>';
			
			if(is_file($sTrailerFile))
			{
			?><div class="player"
				href="<?= $sTrailerFile?>"
				style="display:block;width:460px;height:250px;border:0px;background-image:url(<?=$sMainImageFile?>)">

				<!-- play button -->
				<img src="img/play_large.png"/>

			</div>
			<script>
			flowplayer("div.player", "flowplayer-3.2.2.swf");
			</script>
<?
			} else {
				//echo drawImage($sMainImageFile, 0, 0, stripComments($row->Title));
			}
?>
			<!--ul class="inline_nav">
				<li class="comment"><a href="#comment_list"><?=getLabel('strComments')?></a> (<?=$nComments?>)</li>
				<? if (!empty($sGalleryToDisplay)) {?>
				<li class="gallery"><a href="#galleria"><?=getLabel('strGallery')?></a></li>
				<? }?>
				<li class="friend"><a href="#friend"><a href="mailto:?body=<?='http://'.SITE_URL.'/'.setPage($page, $cat, $item)?>"><?=getLabel('strTellFriend')?></a></li>
			</ul-->
			<div class="text summary"><?=formatDate($rNews->NewsDate, FULL_DATE_YEAR_DISPLAY_FORMAT).getLabel('strByUser').$rNews->Author?></div>
			<?php
				echo '<div class="fb-like" data-send="true" data-width="450" data-show-faces="true"></div>';
			?>
			<div class="text">
			<?=IIF(!empty($rNews->Lead), $rNews->Lead .'<br /><br /><br />', '')?>
			<?=$rNews->Content?></div>
<?
		/*if (!empty($rNews->Source))
		{
			echo '<div class="date">';
			if (!empty($rNews->SourceUrl) && ($rNews->SourceUrl != 'http://'))
				echo '<a href="'.$rNews->SourceUrl.'" target="_blank" title="'.$rNews->Source.'">'.$rNews->Source.'</a>';
			else
				echo $rNews->Source;
			echo '</div>';
		}*/
?>
			<br /><?=$sLink?>
<?
		//echo IIF(!empty($sGalleryToDisplay), $sGalleryToDisplay.'<br class="clear" />', '');

		include('template/comment_list.php');
		echo '</div></div>';
	}
	else
		$item = 0;
}
?>