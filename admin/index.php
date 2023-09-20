<?
	ob_start();
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
	<script type="text/javascript" src="../js/prototype.js"></script>
	<link rel="stylesheet" type="text/css" href="../style/admin.css" />
</head>

<body>
<?
	$nUserID = $oSession->GetValue(SS_USER_ID);
	if (!empty($nUserID))
	{
		echo '<div id="menu">
			<h1><img src="../img/logo_small.png" alt="'.getLabel('strAdminTitle').'" /></h1>'."\n";
		if ($oSession->GetValue(SS_USER_STATUS) == USER_ADMIN)
		{
                        $rUser = $oUser->GetByID($nUserID);
                        $aCities = getLabel('aCities');
                        $dToday = date(DEFAULT_DATE_DB_FORMAT);
                        $nDefCity = $rUser->DefaultCityID;
			echo '<div>'.$rUser->FirstName.' '.$rUser->LastName.' ('.$rUser->Username.')<br />
                                '.getLabel('strCity').': '.$aCities[$rUser->DefaultCityID].'<br />
                                '.formatDate($dToday, FULL_DATE_DISPLAY_FORMAT).'<br />
                                </div>
                                <ul class="nav">
				<!--li>'.getLabel('strSwitch').'</li-->'.
				writeLang(IIF($action==ACT_ADD || $action==ACT_EDIT,getLabel('strSwitchQ'),''), true).
				'<li><a href="'.setPage(ENT_HOME, 0, 0, ACT_LOGOUT).'">'.getLabel('strLogout').'</a></li>
                                </ul>
				<hr />
				<ul class="nav">'."\n";
                        $aEventTypes = getLabel('aEventTypes');
                        $aPlaceTypes = getLabel('aPlaceTypes');
                        $aProgramTypes = getLabel('aProgramTypes');
						$aMixerTypes = getLabel('aMixerTypes');
			foreach($aTemplate[$lang] as $key=>$val)
			{
				//if ($key != ENT_HOME)
                                if (in_array($key, $aMainAdminPages))
                                {
                                        if ($key == ENT_PLACE)
                                        {
                                                echo '<li'.IIF($key == $page, ' class="on"', '').'>'.$val.getLabel('strEntPlaceNote')."\n";
                                                echo '<ul>'."\n";
                                                foreach($aPlaceTypes as $k=>$v)
                                                {
                                                        echo '<li'.IIF($k == $cat, ' class="on"', ' class="off"').'><a href="'.setPage($key, $k).'">'.$v.'</a></li>'."\n";
                                                }
                                                echo '</ul>
                                                </li>'."\n";
                                        }
                                        elseif ($key == ENT_EVENT)
                                        {
                                                echo '<li'.IIF($key == $page, ' class="on"', '').'>'.$val.getLabel('strEntEventNote')."\n";
                                                echo '<ul>'."\n";
                                                foreach($aEventTypes as $k=>$v)
                                                {
                                                        echo '<li'.IIF($k == $cat, ' class="on"', ' class="off"').'><a href="'.setPage($key, $k).'">'.$v.'</a></li>'."\n";
                                                }
                                                echo '</ul>
                                                </li>'."\n";
                                        }
                                        elseif ($key == ENT_MIXER)
                                        {
                                                echo '<li'.IIF($key == $page, ' class="on"', '').'>'.$val."\n";
                                                echo '<ul>'."\n";
                                                foreach($aMixerTypes as $k=>$v)
                                                {
                                                        echo '<li'.IIF($k == $cat, ' class="on"', ' class="off"').'><a href="'.setPage($key, $k).'">'.$v.'</a></li>'."\n";
                                                }
                                                echo '</ul>
                                                </li>'."\n";
                                        }
                                        elseif ($key == ENT_PROGRAM)
                                        {
                                                echo '<li'.IIF($key == $page, ' class="on"', '').'>'.$val.getLabel('strEntProgramNote')."\n";
                                                echo '<ul>'."\n";
                                                foreach($aProgramTypes as $k=>$v)
                                                {
                                                        echo '<li'.IIF($key == $page && $k == $cat, ' class="on"', ' class="off"').'><a href="'.setPage($key, $k).'">'.$v.'</a></li>'."\n";
                                                }
                                                echo '</ul>
                                                </li>'."\n";
                                        }
                                        /*elseif ($key == ENT_REPORT)
                                        {
                                                echo '<li'.IIF($key == $page, ' class="on"', '').'>'.$val.getLabel('strEntReportNote')."\n";
                                                echo '<ul>'."\n";
                                                foreach($aProgramTypes as $k=>$v)
                                                {
                                                        echo '<li'.IIF($key == $page && $k == $cat, ' class="on"', ' class="off"').'><a href="'.setPage($key, $k).'">'.$v.'</a></li>'."\n";
                                                }
                                                echo '</ul>
                                                </li>'."\n";
                                        }*/
                                        else
                                        {
                                                echo '<li'.IIF($key == $page, ' class="on"', '').'><a href="'.setPage($key).'">'.$val.'</a></li>'."\n";
                                        }
                                }
			}
			echo '</ul>
			</div>
			<div id="maincontent">'."\n";
			if (in_array($page, array_keys($aTemplate[$lang])))
			{
				echo '<h2>'.$aTemplate[$lang][$page];
                                $sSubtitle = '';
                                if(!empty($cat))
                                {
                                        switch($page)
                                        {
                                                case ENT_PLACE:
                                                        $sSubtitle = $aPlaceTypes[$cat];
                                                        break;
                                                case ENT_EVENT:
                                                        $sSubtitle = $aEventTypes[$cat];
                                                        break;
                                                //case ENT_REPORT:
                                                case ENT_PROGRAM:
                                                        $sSubtitle = $aProgramTypes[$cat];
                                                        break;
                                                case ENT_MIXER:
                                                        $sSubtitle = $aMixerTypes[$cat];
                                                        break;

                                        }
                                }
                                echo IIF(!empty($sSubtitle), ' - '.$sSubtitle, '').'</h2>'."\n";
				if ($page != ENT_HOME)
				{
					echo '<ul class="nav">'."\n";
					$aItem = explode(',',$item);
					echo '<li><a href="'.setPage($page, $cat, $aItem[0]).'">'.$aTemplate[$lang][$page].getLabel('list').'</a></li>'."\n";
					if (!in_array($page, $aMainAdminReports))
						echo '<li><a href="'.setPage($page, $cat, $aItem[0], ACT_ADD).'">'.$aTemplate[$lang][$page].getLabel('add').'</a></li>'."\n";
					if ($page == ENT_PAGE)
						echo '<li><a href="'.setPage($page, $cat, $aItem[0], ACT_MAP).'">'.$aTemplate[$lang][$page].getLabel('sitemap').'</a></li>'."\n";
                                        if ($page == ENT_PLACE || $page == ENT_PROGRAM)
						echo '<li><a href="'.setPage($page, $cat, $aItem[0], ACT_REPORT).'">'.$aTemplate[$lang][$page].getLabel('report').'</a></li>'."\n";
					echo '</ul>'."\n";
				}
				if (!isset($action) || empty($action) || $action==ACT_LOGIN || $action==ACT_UP || $action==ACT_DOWN || $action==ACT_ON || $action==ACT_OFF)
				{
					if (is_file($page.'_list.php'))
						include_once($page.'_list.php');
				}
                                elseif ($action==ACT_REPORT)
				{
					if (is_file($page.'_report.php'))
						include_once($page.'_report.php');
				}
				elseif ($action==ACT_MAP)
				{
					if (is_file($page.'_tree.php'))
						include_once($page.'_tree.php');
				}
				else
				{
					if (is_file($page.'_edit.php'))
						include_once($page.'_edit.php');
				}
			}
			echo '</div>'."\n";
		}
		else
		{
			showLogin(getLabel('strInvalid'));
		}
	}
	else
	{
		showLogin($sLoginMsg);
	}
?>
</body>
</html>
<?
	@mysql_free_result($result);
	@mysql_close($con);
?>
<?
//=========================================================
	function showLogin($sMsg)
	{
?>
<div id="login">
	<h1><img src="../img/logo_small.png" alt="'.getLabel('strAdminTitle').'" /></h1>
	<?=$sMsg?><br /><br />
	<script type="text/javascript">
	<!--
	function fCheck(frm) {
		if (!valEmpty("username", "<?=getLabel('strEnter').getLabel('strUsername')?>")) return false;
		if (!valEmpty("password", "<?=getLabel('strEnter').getLabel('strPassword')?>")) return false;
		return true;
	}
	//-->
	</script>
	<form action="<?=setPage(ENT_HOME, 0, 0, ACT_LOGIN)?>" method="post" name="Login" id="Login" onSubmit="return fCheck(this);">
	<fieldset>
		<!-- <input type="Hidden" name="action" value="login"> -->
		<label for="username"><?=getLabel('strUsername')?><?=formatVal()?></label><br />
		<input type="text" name="username" id="username" maxlength="255" class="fldsmall" /><br />
		<br />
		<label for="password"><?=getLabel('strPassword')?><?=formatVal()?></label><br />
		<input type="password" name="password" id="password" maxlength="255" class="fldsmall" /><br />
		<br />
		<input type="submit" value="<?=getLabel('strLogin')?>" class="btn" />
	</fieldset>
	</form>
</div>
<?
	}
//=========================================================
?>
