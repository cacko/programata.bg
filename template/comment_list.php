<?php
if (!$bInSite) die();
//=========================================================
	$nUserID = $oSession->GetValue(SS_USER_ID);
?>
	<!--h4><?=getLabel('strComments')?></h4-->
<!--	<a name="comment_list"></a>-->
<!--	<ul class="inline_nav">-->
<!--		<li class="comment"><?=getLabel('strComments')?> (<?=IIF(!empty($nComments), $nComments, 0)?>)</li>-->
		<? if (!empty($nUserID)) {?>
<!--		<li class="comment_add"><a href="#"><?=getLabel('strAddComment')?></a></li>-->
		<? } ?>
<!--	</ul>-->
<!--	<div class="top"><a href="#"><?=getLabel('strTop')?></a></div>-->
	<br />
	<?php 

	if($set_fb_like_to_bottom){ ?>
	<div class="fb-like" data-send="true" data-width="450" data-show-faces="true"></div>
	<?php } ?>
    <div class="comments-header">
    	<span>&nbsp;</span>
        <p>
        	<a href="#" title="<?=getLabel('strTop')?>"><?=getLabel('strTop')?></a>
        	<?= getLabel('strComments')?> (<?=IIF(!empty($nComments), $nComments, 0)?>) 
        </p>
     <?php   
    	if (empty($nUserID)){ 
     	  echo '<strong>'. getLabel('strCommentIntro') .'</strong>';
		}
     ?>
    </div>	

<?
//=========================================================
	// POST COMMENT
	if (!empty($nUserID))
	{
?>
<script type="text/javascript">
<!--
function fCheck(frm)
{
	if (!valEmpty("title", "<?=getLabel('strEnter').getLabel('strCommentTitle')?>")) return false;
	if (!valEmpty("comment", "<?=getLabel('strEnter').getLabel('strCommentText')?>")) return false;
	return true;
}
//-->
</script>
<div class="comment_box" style="display: block">
<!--	<div class="close"><a href="#"><?=getLabel('close')?></a></div>-->
<!--a name="comment_add"></a>
<h6><?=getLabel('strPostComment')?></h6-->
<!--<?=getLabel('strRequired')?><br /><br /> -->
	
<form method="post" action="<?=setPage($page, $cat, $item, ACT_SAVE)?>#comment_list" id="comment_form" name="comment_form" onsubmit="return fCheck(this);">
	<input type="hidden" name="<?=ARG_ID?>" id="<?=ARG_ID?>" value="<?=$item?>" />
	<label for="title"><?=getLabel('strCommentTitle')?><?=formatVal()?></label><br />
	<input type="text" name="title" id="title" maxlength="255" class="fld" value="Re: <?=stripComments($sItemTitle)?>" /><br />
	<br />
	<label for="comment"><?=getLabel('strCommentText')?><?=formatVal()?></label><br />
	<textarea cols="36" rows="7" name="comment" id="comment"></textarea><br />
	<br />
	<input type="submit" value="<?=getLabel('strSend')?>" class="btn" />
	<br class="clear"/>
</form>
</div>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("li.comment_add").find("a").click(function() {
		jQuery("div.comment_box").slideDown("slow");
		return false;
	})
	jQuery("div.close").find("a").click(function() {
		jQuery("div.comment_box").slideUp("slow");
		return false;
	})
})
</script>
<?
	}
//=========================================================
	// COMMENTS
	$nIdx = 0;
	//array(5, 1, 0, 0) is for publication date ASC
	$rsComments = $oComment->ListAll($item, $nEntityType, '', '', '', '', array(5, 1, 0, 0));
	if (mysql_num_rows($rsComments))
	{
		while($row = mysql_fetch_object($rsComments))
		{
			$nIdx ++;
			echo '<div class="'.IIF($nIdx%2==0, 'even', 'odd').'" id="comment_post">';
			$sCommentUser = $row->Username;
			if (!empty($row->Url) && $row->Url != 'http://')
				$sCommentUser = '<a href="'.$row->Url.'" target="_blank">'.$row->Username.'</a>';
			//$sCommentUser = '<a href="'.setPage(USERS_PAGE, 0, $row->UserID).'"><span class="a">'.$row->Username.'</span></a>';
			$sTitle = $row->Title;
			echo '<div class="date">
					<a style="color: #fff;" title="'.getLabel('strPermalink').'" class="num" href="'.setPage($page, $cat, $item).'#a'.$row->CommentID.'" name="a'.$row->CommentID.'">#'.$nIdx.'</a>
					'.formatDateTime($row->CommentDate, DEFAULT_DATETIME_DISPLAY_FORMAT).' '.getLabel('strByUser'). str_replace('<a','<a style="color: #fff;" ', $sCommentUser).'</div>
					<br /><h6><b>'.$sTitle.'</b></h6><br />
					<div class="comment_content" style="line-height: 20px; font-style: Italic; font-size: 14px;">'.nl2br($row->Content).'</div>'."\n";
			//$bIsFirst = false;
			echo '</div><br/>'."\n";
		}
		//echo '<div class="top"><a href="#">'.getLabel('strTop').'</a></div>'."\n";
	}
//=========================================================
	if (empty($nUserID))
	{
#		echo '<div class="text">'.getLabel('strCommentIntro').'</div>
#			<br class="clear" />'."\n";
	}
?>