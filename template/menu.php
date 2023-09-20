<?php
if (!$bInSite) die();
//=========================================================
    $strMenu = '';
    $aPath = $oPage->GetParentsPathAsArray($rCurrentPage->PageID);
    $nUserStatus = (int) $oSession->GetValue(SS_USER_STATUS);
    $strMenu = getChildren($nRootPage, false, $nUserStatus, $aPath);
    
    if($page != DEF_PAGE)// && !empty()
    {
//	echo '<ul>'."\n";
	if (!empty($strMenu) && $nRootPage != USERROOT_PAGE)
	{
	    //echo '<h4><a href="'.setPage($rRootPage->PageID).'" title="'.htmlspecialchars($rRootPage->Title).'">'.htmlspecialchars($rRootPage->Title).'</a></h4>'.$strMenu;
//	    echo '<h4 title="'.htmlspecialchars($rRootPage->Title).'">'.htmlspecialchars($rRootPage->Title).'</h4>'.$strMenu;
	    echo $strMenu;
	}
	else
	    echo '&nbsp;';
//	echo '</ul>'."\n";
    }
?>