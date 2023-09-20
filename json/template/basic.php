<?php
if (!$bInSite) die();
//=========================================================
    if (!isset($item) || empty($item))
    {
        if ($nRootPage==USERROOT_PAGE || in_array($nRootPage, $aSysNavigation) || ($rCurrentPage->ParentPageID != DEF_PAGE && !in_array($nRootPage, $aSysNavigation)))
	{
            echo '<h2 title="'.htmlspecialchars(strip_tags($rCurrentPage->Title)).'">'.htmlspecialchars($rCurrentPage->Title).'</h2>'."\n";
        }
        if ((!isset($item) || empty($item)) && !empty($rCurrentPage->Description))
        {
            echo '<div class="text">'.$rCurrentPage->Description.'<br /></div>'."\n";
        }
        /*$sGallery = showGallery($page, ENT_PAGE, true, false);
        if (!empty($sGallery))
            echo '<h4>'.getLabel('strGallery').'</h4>'.$sGallery."\n";*/
    }
?>
