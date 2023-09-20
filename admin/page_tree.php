<?php
if (!$bInSite) die();
//=========================================================
$sSitemap = '';

$row = $oPage->GetByID(DEF_PAGE);
$sSitemap .= '<a href="'.setPage($row->PageID).'" title="'.$row->Title.'">'.$row->Title.'</a>';

$sSitemap .= getChildren(DEF_PAGE, true, USER_ADMIN, null, true);

echo '<div id="sitemap">'.$sSitemap.'</div>';
?>