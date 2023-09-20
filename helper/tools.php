<?php
if (!$bInSite) die();
//=========================================================
function setPage($_page, $_cat=0, $_item=0, $_action='', $_relitem=0, $_city=0)
{
	global $lang, $city, $item, $rCurrentPage;

	$strPage = START_PAGE.'?'.ARG_PAGE.'='.$_page.'&amp;'.ARG_LANG.'='.$lang;
	if (isset($_city) && !empty($_city) && is_numeric($_city))
	$strPage .= '&amp;'.ARG_CITY.'='.$_city;
	else
	$strPage .= '&amp;'.ARG_CITY.'='.$city;
	if (isset($_cat) && !empty($_cat) && is_numeric($_cat))
	$strPage .= '&amp;'.ARG_CAT.'='.$_cat;
	if (isset($_item) && !empty($_item) && is_numeric($_item))
	{
		$strPage .= '&amp;'.ARG_ID.'='.$_item;
	}
	if (isset($_action) && !empty($_action))
	$strPage .= '&amp;'.ARG_ACT.'='.$_action;
	if (isset($_relitem) && !empty($_relitem) && is_numeric($_relitem))
	$strPage .= '&amp;'.ARG_RELID.'='.$_relitem;

	return $strPage;
}
//=========================================================
function setCurrency($nCur)
{
	global $page, $cat, $item;
	$strPage = setPage($page, $cat, $item);
	if (isset($nCur) && !empty($nCur))
	$strPage .= '&amp;'.ARG_CUR.'='.$nCur;
	return $strPage;
}
//=========================================================
function setLang($_lang=0)
{
	global $lang, $aLoadActions;

	$strPage = START_PAGE.'?';
	$sQuery = $_SERVER["QUERY_STRING"];
	if (!empty($sQuery))
	{
		$aParams = explode("&", $sQuery);
		foreach ($aParams as $key => $val)
		{
			$aArgs = explode("=", $val);
			if($aArgs[0] == ARG_LANG)
			{
				// do not save old language
			}
			elseif($aArgs[0] == ARG_ACT && !in_array($aArgs[1], $aLoadActions))
			{
				// do not save actions different than load actions
			}
			else
			{
				$strPage .= $aArgs[0].'='.$aArgs[1].'&amp;';
			}
		}
	}
	if (isset($_lang) && !empty($_lang) && is_numeric($_lang))
	$strPage .= ARG_LANG.'='.$_lang;
	else
	$strPage .= ARG_LANG.'='.$lang;

	return $strPage;
}
//=========================================================
function strShorten($sValue, $nTrimLength)
{
	$sToReturn = str_replace('</p>', ' ', $sValue);
	$sToReturn = str_replace('<br />', ' ', $sToReturn);
	$sToReturn = strip_tags($sToReturn);
	//if (mb_strlen($sToReturn) > $nTrimLength)
	if (strlen($sToReturn) > $nTrimLength)
	{
		//$sToReturn = mb_substr($sToReturn, 0, $nTrimLength);
		//$nLastSpace = mb_strrpos($sToReturn, ' ');
		//$sToReturn = mb_substr($sToReturn, 0, $nLastSpace);
		$sToReturn = substr($sToReturn, 0, $nTrimLength);
		$nLastSpace = strrpos($sToReturn, ' ');
		$sToReturn = substr($sToReturn, 0, $nLastSpace);
		$sToReturn .= ' ...';
	}
	return $sToReturn;
}
//=========================================================
function strUCase($sValue)
{
	$sUpper = getLabel('strUpper');
	$sLower = getLabel('strLower');
	$sToReturn = strtr($sValue, $sLower, $sUpper);
	return strtoupper($sToReturn);
}
//=========================================================
function strLCase($sValue)
{
	$sUpper = getLabel('strUpper');
	$sLower = getLabel('strLower');
	$sToReturn = strtr($sValue, $sUpper, $sLower);
	return strtolower($sToReturn);
}
//=========================================================
function getLabelOld($key)
{
	global $aLabel, $lang;

	if (isset($aLabel[$lang][$key]) && !empty($aLabel[$lang][$key]))
	return $aLabel[$lang][$key];
	elseif (isset($aLabel[$key]) && !empty($aLabel[$key]))
	return $aLabel[$key];
	else
	return '[no value]';
}
//=========================================================
function getLabel($key, $_lang=0)
{
	global $aLabel, $lang;

	$nLangToGo = $lang;
	if (!empty($_lang))
	$nLangToGo = $_lang;

	if (isset($aLabel[$nLangToGo][$key]) && !empty($aLabel[$nLangToGo][$key]))
	return $aLabel[$nLangToGo][$key];
	elseif (isset($aLabel[$key]) && !empty($aLabel[$key]))
	return $aLabel[$key];
	else
	return '[no value]';
}
//=========================================================
function getPostedArg($sArgName, $sDefValue='')
{
	if (!is_array($_POST) || count($_POST) == 0)
	return $sDefValue;
	if(!in_array($sArgName, array_keys($_POST)))
	return $sDefValue;
	$sNewVal = $_POST[$sArgName];
	if (isset($sNewVal)) // && !empty($sNewVal)
	return $sNewVal;
	else
	return $sDefValue;
}
//=========================================================
function getQueryArg($sArgName, $sDefValue='')
{
	if (!is_array($_GET) || count($_GET) == 0)
	return $sDefValue;
	if(!in_array($sArgName, array_keys($_GET)))
	return $sDefValue;
	$sNewVal = $_GET[$sArgName];
	if (isset($sNewVal)) // && !empty($sNewVal)
	return $sNewVal;
	else
	return $sDefValue;
}
//=========================================================
function getRequestArg($sArgName, $sDefValue='')
{
	if (!is_array($_REQUEST) || count($_REQUEST) == 0)
	return $sDefValue;
	if(!in_array($sArgName, array_keys($_REQUEST)))
	return $sDefValue;
	$sNewVal = $_REQUEST[$sArgName];
	if (isset($sNewVal)) // && !empty($sNewVal)
	return $sNewVal;
	else
	return $sDefValue;
}
//=========================================================
function getCookieArg($sArgName, $sDefValue='')
{
	if (!is_array($_COOKIE) || count($_COOKIE) == 0)
	return $sDefValue;
	if(!in_array($sArgName, array_keys($_COOKIE)))
	return $sDefValue;
	$sNewVal = $_COOKIE[$sArgName];
	$sNewVal = stripBRs(strip_tags($sNewVal));
	if (isset($sNewVal) && !empty($sNewVal))
	return $sNewVal;
	else
	return $sDefValue;
}
//=========================================================
function getSessionArg($sArgName, $sDefValue='')
{
	if (!is_array($_SESSION) || count($_SESSION) == 0)
	return $sDefValue;
	if(!in_array($sArgName, array_keys($_SESSION)))
	return $sDefValue;
	$sNewVal = $_SESSION[$sArgName];
	$sNewVal = stripBRs(strip_tags($sNewVal));
	if (isset($sNewVal) && !empty($sNewVal))
	return $sNewVal;
	else
	return $sDefValue;
}
//=========================================================
function stripBRs($str)
{
	$newstr = trim($str);
	if (!empty($newstr))
	{
		$newstr = str_replace('<br />','',$newstr);
		$newstr = str_replace('<br />','',$newstr);
		$newstr = str_replace('&lt;','<',$newstr);
		$newstr = str_replace('&gt;','>',$newstr);
	}
	return $newstr;
}
//=========================================================
function stripParam(&$param)
{
	if (is_array($param))
	{
		foreach($param as $key=>$val)
		{
			mysql_real_escape_string(stripslashes($val));
			if(strlen(strip_tags($val)) == 0) $val = '';
		}
	}
	else
	{
		mysql_real_escape_string(stripslashes($param));
		if(strlen(strip_tags($param)) == 0) $param = '';
	}
}
//=========================================================
function formatErr($msg) {
	return '<span class="err">'.$msg.'</span>';
}
//=========================================================
function formatVal($sID='') {
	return '<span class="val" '.IIF(!empty($sID), ' id="'.$sID.'"', '').'>*</span>';
}
//=========================================================
function IIF($cond,$truemsg,$falsemsg)
{
	if ($cond)
	{
		return $truemsg;
	}
	return $falsemsg;
}
//=========================================================
function dbAssert($result, $strSQL) {
	if (!$result)
	die("<!-- <br />Error in file <b>".__FILE__."</b> at line <b>".__LINE__."</b><br />SQL = <b>".$strSQL."</b><br />Error: ".mysql_error()." -->");
}
//=========================================================
function writeLink($sTitle, $nPageID, $nItemID=0, $attributes='')
{
	global $page, $item;

	$strToReturn = '';
	/*if ($nPageID == $page && $nItemID == $item)
	 {
		$strToReturn .= $sTitle;
		}
		else
		{
		$strToReturn .= '<a href="'.setPage($nPageID, 0, $nItemID).'" title="'.str_replace('"','',trim(strip_tags($sTitle))).'">'.$sTitle.'</a>';
		}*/
	//	$strToReturn .= '<a href="http://gorichka.blog.bg/" target="_blank" ';
	$strToReturn .= '<a href="'.setPage($nPageID, 0, $nItemID).'" '.$attributes.' ';
	$strToReturn .= 'title="'.str_replace('"','',trim(strip_tags($sTitle))).'">'.$sTitle.'</a>';
	return $strToReturn;
}
//=========================================================
function writePath($nPageID)
{
	global $page, $cat, $item, $oPage;

	$row = $oPage->GetByID($nPageID);
	$pageToGo = $row->PageID;

	if ($pageToGo == $page)
	{
		if (isset($cat) && $cat > 0)
		$strPath = '<a href="'.setPage($pageToGo).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.$row->Title.'</a> / ';
		elseif (isset($item) && $item > 0)
		$strPath = '<a href="'.setPage($pageToGo).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.$row->Title.'</a> / ';
		else
		$strPath = $row->Title.' ...';
	}
	else
	{
		$strPath = '<a href="'.setPage($pageToGo).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.$row->Title.'</a> / ';
	}
	if ($pageToGo != DEF_PAGE)
	{
		$strPath = writePath($row->ParentPageID).$strPath;
	}
	return $strPath;
}
//=========================================================
function writeItem($strTitle)
{
	global $rCurrentPage, $cat, $item, $oCategory, $oProduct;

	if (!empty($strTitle))
	$strTitle .= ' ...';
	/*$strTitle = '';
	 if (!isset($cat) || empty($cat))
	 {
		return $strTitle;
		}*/
	/*if ($rCurrentPage->TemplateFile == ENT_CAT.'_list')
	 {
		$catRow = $oCategory->GetByID($cat);
		if ($catRow)
		{
		if (!isset($item) || empty($item))
		{
		$strTitle .= $catRow->CategoryName;
		}
		else
		{
		$strTitle .= '<a href="'.setPage($rCurrentPage->PageID, $catRow->CategoryID).'" title="'.$catRow->CategoryName.'">'.$catRow->CategoryName.'</a> | ';
		$prRow = $oProduct->GetByID($item);
		if ($prRow)
		{
		$strTitle .= $prRow->ProductName;
		}
		}
		}
		}*/
	return stripComments($strTitle);
}
//=========================================================
function writeLang($sConfirmMsg='', $bShowAll = false)
{
	global $lang, $aLanguages, $aActiveLanguages;

	$aLangsToShow = $aActiveLanguages;
	if ($bShowAll)
	$aLangsToShow = array_keys($aLanguages);

	reset ($aLanguages);
	$strToReturn = '';
	//$i = 0; 
	
	while (list($key, $value) = each($aLanguages))
	{
		//if ($key != $lang && in_array($key, $aLangsToShow))
		if (in_array($key, $aLangsToShow))
		{
			//$i++;
			if ($key != $lang)
			{
				$strToReturn .= '<a href="'.setLang($key).'" class="language" title="'.$value.'"';
				if (!empty($sConfirmMsg))
				$strToReturn.= ' onclick="return confMsg(\''.$sConfirmMsg.'\');return false;"';
				$strToReturn .= '>'.$value.'</a>';
			}
			else
			{
				//$strToReturn .= '<li class="on" title="'.$value.'">'.$value.'</li>';
			}
			//if ($i < count($aLangsToShow))
			//$strToReturn .= '<li>|</li>';
		}
	}
	return $strToReturn;
}
//=========================================================
function getChildren($nParentPageID, $bExpandAll = false, $nUserStatus = USER_GUEST, $aPath = null, $bAdminTree = false)
{
	global $oPage, $page, $item, $aSysNavigation;

	$strToReturn = '';
	$rsPage = $oPage->ListAll($nParentPageID, '', '', $bAdminTree);
	if (mysql_num_rows($rsPage))
	{
		$strToReturn .= '<ul>'."\n";
		$pages = null;
		$nRows = mysql_num_rows($rsPage);
		$i=0;
		while($row = mysql_fetch_object($rsPage))
		{
			$bShowItem = true;
			// when not to expand menu:
			// don't show sys navigation in left menu
			if (!$bExpandAll && in_array($row->PageID, $aSysNavigation))
			$bShowItem = false;
			// don't show restructed pages when no user
			if (empty($nUserStatus) && $row->RequiredUserStatus > $nUserStatus)
			$bShowItem = false;
			// don't show public pages when user
			/*if (!empty($nUserStatus) && $row->RequiredUserStatus == USER_GUEST)
			$bShowItem = false;*/
			if ($bShowItem)
			{
				//$strToReturn .= '<li>';
				if ($bAdminTree)
				{
					$strToReturn .= '<li><a'.IIF($row->IsHidden == true, ' class="hidden"', '').' href="'.setPage($page, 0, $row->PageID, ACT_VIEW).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.htmlspecialchars(strip_tags($row->Title)).'</a>'."\n";
				}
				else
				
					if ($bExpandAll || (is_array($aPath) && in_array($row->PageID, $aPath)))
					$submenu .= getChildren($row->PageID, $bExpandAll, $nUserStatus, $aPath, $bAdminTree);
					
				#d($aSysNavigation);				
//				$strToReturn .= '<li'.IIF(is_array($aPath) && in_array($row->PageID, $aPath), ' class="on"', '').'><a'.IIF($i==0, ' style="background:none;"', '').' href="'.setPage($row->PageID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.htmlspecialchars(strip_tags($row->Title)).'</a>'."\n";
//				$strToReturn .= '<li'.IIF(is_array($aPath) && in_array($row->PageID, $aPath), ' class="active"', '').'><a href="'.setPage($row->PageID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.htmlspecialchars(strip_tags($row->Title)).'</a>'."\n";
				if(!in_array($row->PageID, array(8,9,3,SITEMAP_PAGE))){
					$strToReturn .= '<li'.IIF(is_array($aPath) && in_array($row->PageID, $aPath), ' class="active"', '').'><a '. (!empty($submenu) ? 'class="parent_page"' : '') .' href="'.setPage($row->PageID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.htmlspecialchars(strip_tags($row->Title)).'</a>'."\n";
				}
				// check if page is in parents' path

				$strToReturn .= $submenu;
				$strToReturn .= '</li>'."\n";
				$i++;
				unset($submenu);
			}
		}
		$strToReturn .= '</ul>'."\n";
	}
	return $strToReturn;
}
//=========================================================
function formatPrice($strValue)
{
	$floatValue = ereg_replace("(^[0-9]*)(\\.|,)([0-9]*)(.*)", "\\1.\\3", $strValue);
	if (!is_numeric($floatValue)) $floatValue = ereg_replace("(^[0-9]*)(.*)", "\\1", $strValue);
	if (!is_numeric($floatValue)) $floatValue = 0;
	//eregi('^[1-9]{1}[0-9]*[.]{1}[0-9]{2}$',$price)
	return $floatValue;
}
//=========================================================
function displayPrice($sPrice)
{
	$fPrice = (float) $sPrice;
	$fPrice = round($fPrice*100)/100;
	return sprintf("%01.2f", $fPrice);

}
//=========================================================
function formatQuantity($strValue)
{
	$nQuantity = (int) $strValue;
	if (empty($strValue))
	$nQuantity = 0;
	elseif (!is_numeric($strValue))
	$nQuantity = MIN_QUANTITY;
	$nQuantity =  round($nQuantity);

	return $nQuantity;
}
//=========================================================
function generateRandomString($minlength, $maxlength, $useupper, $usenumbers, $usespecial)
{
	/*
	 Author: Peter Mugane Kionga-Kamau http://mugane.homestake.net
	 Description: string str_makerand(int $minlength, int $maxlength, bool $useupper, bool $usespecial, bool $usenumbers)
	 returns a randomly generated string of length between $minlength and $maxlength inclusively.
	 Notes:
	 - If $useupper is true uppercase characters will be used; if false they will be excluded.
	 - If $usespecial is true special characters will be used; if false they will be excluded.
	 - If $usenumbers is true numerical characters will be used; if false they will be excluded.
	 - If $minlength is equal to $maxlength a string of length $maxlength will be returned.
	 - Not all special characters are included since they could cause parse errors with queries. To use all special characters change the if ($usespecial)... line below to read if ($usespecial)$charset="~!@#$%^&*()_+`-={}|\\]?[\":;'><,./"
	 Modify at will.
	 */
	$charset = "abcdefghijklmnopqrstuvwxyz";
	if ($useupper)   $charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	if ($usenumbers) $charset .= "0123456789";
	if ($usespecial) $charset .= "~@#$%^*()_+-={}|][";
	if ($minlength > $maxlength)
	{
		$length = mt_rand ($maxlength, $minlength);
	}
	else
	{
		$length = mt_rand ($minlength, $maxlength);
	}
	$key = '';
	for ($i=0; $i<$length; $i++)
	{
		$key .= $charset[(mt_rand(0,strlen($charset)-1))];
	}

	return $key;
}
//=========================================================
function stripComments($sTitle)
{
	$sTitle = str_replace('[', '<!--', $sTitle);
	$sTitle = str_replace(']', '-->', $sTitle);
	$sTitle = strip_tags($sTitle);
	return $sTitle;
}

function stripHTMLComments($buffer)
{
	return preg_replace('/<!--(.|\s)*?-->/', '', $buffer);
}
//=========================================================
function prevent_multiple_submit($type = "post", $excl = "validator")
{
	$string = "";
	foreach ($_POST as $key => $val)
	{
		// this test is new in version 1.01 to exclude a single variable
		if ($key != $excl)
		{
			$string .= $val;
		}
	}
	if (isset($_SESSION['last']))
	{
		if ($_SESSION['last'] === md5($string))
		{
			return false;
		}
		else
		{
			$_SESSION['last'] = md5($string);
			return true;
		}
	}
	else
	{
		$_SESSION['last'] = md5($string);
		return true;
	}
}
//=========================================================
function dump($var)
{
	if($_SERVER['REMOTE_ADDR'] == '78.83.250.43') {
		echo "<pre>";
		var_dump($var);
		echo "</pre>";
	}
}
function redirect($url)
{
	while(@ob_end_clean());
	header('Location: '.$url);
	exit;
}
class Timer extends DateTime {

	public function __construct($date) {
		parent::__construct($date, new DateTimeZone('Europe/Sofia'));
	}

	public function isDone() {
		return time() > (int) $this->format('U');
	}
}
//======================================================================
function getBoundingBox($lng, $lat, $distance) {

	$boundingBox = array();
	$latRadian = deg2rad($lat);

	$degLatKm = 110.574235;
	$degLongKm = 110.572833 * cos($latRadian);
    $deltaLat = $distance / 1000.0 / $degLatKm;
    $deltaLong = $distance / 1000.0 / $degLongKm;

    $minLat = $lat - $deltaLat;
    $minLong = $lng - $deltaLong;
    $maxLat = $lat + $deltaLat;
    $maxLong = $lng + $deltaLong;

    $boundingBox[0] = $minLat;
    $boundingBox[1] = $maxLat;
    $boundingBox[2] = $minLong;
    $boundingBox[3] = $maxLong;

     return $boundingBox;
}
//=========================================================================
function twoPointsDistance($lat1, $lng1, $lat2, $lng2) {
	$R = 6371; //km
	$lat1 = deg2rad($lat1);
	$lng1 = deg2rad($lng1);
	$lat2 = deg2rad($lat2);
	$lng2 = deg2rad($lng2);

	$deltaLat = $lat2 - $lat1;
	$deltaLng = $lng2 - $lng1;

	$a = pow(sin($deltaLat / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($deltaLng / 2), 2);
	$c = 2 * atan2(sqrt($a), sqrt(1 - $a));

	return round($R * $c * 1000);
}

?>