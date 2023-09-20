<?php
if (!$bInSite) die();
//=========================================================
if (isset($item) && !empty($item))
{
	$rExtra = $oExtra->GetByID($item);
	if ($rExtra)
	{
		$set_fb_like_to_bottom = false;	
		$nEntityType = $aEntityTypes[ENT_EXTRA];
		include('template/comment_save.php');
		
		$sItemTitle = $rExtra->Title;
		$oExtra->TrackView($item);
		
		// =========================== LINKS ===========================
		$sLink = '';
		$rsLink = $oLink->ListAll($rExtra->PublicationID, $aEntityTypes[ENT_EXTRA], array(1,2,3,4,0));
		if(mysql_num_rows($rsLink))
		{
			$sLink .= '<br />';
			$aLinkTypes = getLabel('aLinkTypes');
			while($rLink = mysql_fetch_object($rsLink))
			{
				if ($rLink->LinkTypeID == 2 || $rLink->LinkTypeID == 3)
					$sLink .= '<a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$aLinkTypes[$rLink->LinkTypeID].'</a><br />';
				if ($rLink->LinkTypeID == 4) //more info
					$sLink .= '<div class="more_info">'.getLabel('strMoreInfo').': <a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$rLink->Title.'</a></div><br />';
				else
					$sLink .= '<div class="link"><strong>'. getLabel('strUrl').'</strong> <a href="'.str_replace('&', '&amp;', $rLink->Url).'" target="_blank">'.$rLink->Title.'</a><br />';
			}
		}
		// =========================== COMMENTS ===========================
		$nComments = $oComment->GetCountByEntity($rExtra->PublicationID, $aEntityTypes[ENT_EXTRA]);
		// =========================== IMAGES ===========================
		$sMainImageFile = UPLOAD_DIR.IMG_EXTRA.$rExtra->PublicationID.'.'.EXT_IMG;
		if (!is_file($sMainImageFile))
		{
			$sMainImageFile = UPLOAD_DIR.IMG_GALLERY.'.'.EXT_PNG;
		}
	
		$sGalleryToDisplay = '';
		$sGallery = showGallery($rExtra->PublicationID, ENT_EXTRA, false, false, true);
		if (!empty($sGallery))
//			$sGalleryToDisplay .= '<h4>'.getLabel('strGallery').'</h4>
//						<a name="galleria"></a>'.$sGallery."\n";
			$sGalleryToDisplay .= '<div class="gallery"><ul>'.$sGallery .'</ul></div>';
		
		// =========================== PUBLICATION INFO =========================== 
?>
<!--		<h2 title="<?=htmlspecialchars(strip_tags($rExtra->Title))?>"><?=$rExtra->Title?></h2>
		<div class="box detail">
			<?//=//IIF(!empty($rExtra->Subtitle), '<div>'.$rExtra->Subtitle.'</div>', '')?>
			<?=drawImage($sMainImageFile, 0, 0, $row->Title)?>
			<div class="date"><?=formatDate($rExtra->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT).getLabel('strByUser').$rExtra->Author?></div>
			<!--<ul class="inline_nav">
				<li class="comment"><a href="#comment_list"><?=getLabel('strComments')?></a> (<?=$nComments?>)</li>
				<? if (!empty($sGalleryToDisplay)) {?>
				<li class="gallery"><a href="#galleria"><?=getLabel('strGallery')?></a></li>
				<? }?>
				<li class="friend"><a href="#friend"><a href="mailto:?body=<?='http://'.SITE_URL.'/'.setPage($page, $cat, $item)?>"><?=getLabel('strTellFriend')?></a></li>
			</ul>
			<?=IIF(!empty($rExtra->Lead), '<div class="lead">'.$rExtra->Lead.'</div>', '')?>
			<div><br /><?=$rExtra->Content?></div>
			<div><br /><?=$sLink?></div>
		</div>-->
		
	  <div id="container">
    	<div id="article">
        	<div class="main-preview">
            	<a href="<?=$sMainImageFile?>" id="wallpepar" title="<?php echo stripComments(htmlspecialchars(strip_tags($rExtra->Title)));?>"><?php echo drawImage($sMainImageFile, 470, 250, stripComments($row->Title)); ?></a>
                <h1><?php echo stripComments(htmlspecialchars(strip_tags($rExtra->Title)));?></h1>
            </div>		
            
           <?php
				if(!empty($sGalleryToDisplay)){
					echo substr($sGalleryToDisplay,0, 25) .'<li><a href="'. $sMainImageFile .'" title="'. $row->Title .'">'. drawImage($sMainImageFile, 138, 93, $row->Title) .'</a></li>'. substr($sGalleryToDisplay,25);	
				}           
            ?>
		
			<script type="text/javascript">
					if($(".gallery a").lightBox().length) {
						$(".main-preview a").click(function(event) {
							event.preventDefault();
							$(".gallery a[href=\'" + $(this).attr("href") + "\']").click();
						});
					} else {
						$(".main-preview a").lightBox();
					}
					
			</script>            
			
			
		   <div class="text summary"><?=formatDate($rExtra->PublicationDate, FULL_DATE_YEAR_DISPLAY_FORMAT).getLabel('strByUser').$rExtra->Author?></div>
		   <?php
				echo '<div class="fb-like" data-send="true" data-width="450" data-show-faces="true"></div>';	   
		   ?>
            
           <div class="text">
           	<?=IIF(!empty($rExtra->Lead), $rExtra->Lead .'<br /><br /><br />', '')?>
           	<?=$rExtra->Content?>
           </div>
           
       <br /><?=$sLink?>     
<?
		#echo IIF(!empty($sGalleryToDisplay), $sGalleryToDisplay.'<br class="clear" />', '');
		
			include('template/comment_list.php');	
	}
	else
		$item = 0;
		
	echo '</div>
	</div>	';			
}
?>