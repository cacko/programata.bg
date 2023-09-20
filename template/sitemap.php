<?php
if (!$bInSite) die();
//=========================================================
$sSitemap = '';

$titlew = '<h1>'. str_replace(' - ','',getLabel('sitemap')) .'</h1><hr/>';
$row = $oPage->GetByID(DEF_PAGE);
$tmp .= '<ul>';
$tmp .= '<li><a class="root_pages_first" href="'.setPage($row->PageID).'" title="'.$row->Title.'">'.$row->Title.'</a></li>';
unset($row);
$row = $oPage->GetByID(3);
$tmp .= '<li><a class="root_pages" href="'.setPage($row->PageID).'" title="'.$row->Title.'">'.$row->Title.'</a></li>';
unset($row);
$row = $oPage->GetByID(8);
$tmp .= '<li><a class="root_pages" href="'.setPage($row->PageID).'" title="'.$row->Title.'">'.$row->Title.'</a></li>';
unset($row);
$row = $oPage->GetByID(9);
$tmp .= '<li><a class="root_pages" href="'.setPage($row->PageID).'" title="'.$row->Title.'">'.$row->Title.'</a></li>';
unset($row);
$row = $oPage->GetByID(SITEMAP_PAGE);
$tmp .= '<li><a class="root_pages" href="'.setPage($row->PageID).'" title="'.$row->Title.'">'.$row->Title.'</a></li>';
$tmp .= '</ul>';


$nUserStatus = (int) $oSession->GetValue(SS_USER_STATUS);
$sSitemap .= getChildren(DEF_PAGE, true, $nUserStatus);
?>
	<div id="sitemap"><?php

					  echo $titlew . substr($sSitemap,0, 9) . $tmp .'</li><li>'. substr($sSitemap, 9);
				 			
				      ?><br class="clear"/></div>
