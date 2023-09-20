<?
	header ("Expires: Mon, 26 Jul 1997 05:00:00 GMT");// Date in the past
	header ("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");// always modified
	header ("Cache-Control: no-cache, must-revalidate");// HTTP/1.1
	header ("Pragma: no-cache");// HTTP/1.0
	
	$bInSite = true;
	
	require_once('../initialize.php');
	include_once('../helper/user_action.php');
?>
<!DOCTYPE html 
     PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
     "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
	<title><?=getLabel('strAdminTitle')?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?=DEF_ENCODING?>" />
	<meta name="Description" content="<?=getLabel('strSiteDescription')?>" />
	<meta name="Keywords" content="<?=getLabel('strSiteKeywords')?>" />
	<meta name="Author" content="<?=getLabel('strSiteAuthor')?>" />
	<script type="text/javascript" src="../js/common.js"></script>
	<script type="text/javascript" src="../js/val.js"></script>
	<script type="text/javascript" src="../js/calendar.js"></script>
	<link rel="stylesheet" type="text/css" href="../style/admin.css" />
</head>

<body>
<?
	$nUserID = $oSession->GetValue(SS_USER_ID);
	if (!empty($nUserID))
	{
		if ($oSession->GetValue(SS_USER_STATUS) == USER_ADMIN)
		{
                        $rUser = $oUser->GetByID($nUserID);
                        //$aCities = getLabel('aCities');
                        $dToday = date(DEFAULT_DATE_DB_FORMAT);
                        $nDefCity = $rUser->DefaultCityID;
                        echo '<div>
                        <ul class="relents">'."\n";
                        //echo '<li><a href="'.setPage('home').'">'.getLabel('strHome').'</a></li>'."\n";
			foreach($aRelatedTemplate[$lang] as $key=>$val)
			{
                                if (@in_array($key, $aRelatedAdminPages[$cat]))
                                {
                                        echo '<li'.IIF($key == $page, ' class="on"', '').'><a href="'.setPage($key, $cat, 0, '', $relitem).'">'.$val.'</a></li>'."\n";
                                }
                        }
			echo '</ul>
			</div>
			<div id="maincontent">'."\n";
			if (in_array($page, array_keys($aRelatedTemplate[$lang])))
			{
				echo '<h3>'.$aRelatedTemplate[$lang][$page].'</h3>'."\n";
				if ($page != ENT_HOME)
				{
					echo '<ul class="nav">'."\n";
					$aItem = explode(',',$item);
					if ($page == ENT_PLACE_GUIDE)
					{
						echo '<li><a href="'.setPage($page, $cat, $aItem[0], ACT_ADD, $relitem).'">'.$aRelatedTemplate[$lang][$page].' - '.getLabel('edit').'</a></li>'."\n";
					}
					else
					{
						echo '<li><a href="'.setPage($page, $cat, $aItem[0], '', $relitem).'">'.$aRelatedTemplate[$lang][$page].getLabel('list').'</a></li>'."\n";
						echo '<li><a href="'.setPage($page, $cat, $aItem[0], ACT_ADD, $relitem).'">'.$aRelatedTemplate[$lang][$page].getLabel('add').'</a></li>'."\n";
                                                if ($page == ENT_DATE_TIME)
                                                {
                                                        echo '<li><a href="'.setPage($page, $cat, $aItem[0], ACT_ADD_MULTIPLE, $relitem).'">'.$aRelatedTemplate[$lang][$page].getLabel('addmore').'</a></li>'."\n";
                                                }
					}
					echo '</ul>'."\n";
				}
				if (!isset($action) || empty($action) || $action==ACT_UP || $action==ACT_DOWN || $action==ACT_ON || $action==ACT_OFF)
				{
					if (is_file($page.'_list.php'))
						include_once($page.'_list.php');
				}
				else
				{
					if (is_file($page.'_edit.php'))
					{
						include_once($page.'_edit.php');
					}
				}
			}
                        else
                        {
                                if (is_file($page.'_related_list.php'))
					include_once($page.'_related_list.php');
                        }
			echo '</div>'."\n";
		}
	}
?>
</body>
</html>
