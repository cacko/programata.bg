<?php
if (!$bInSite) die();
//=========================================================
function parseDate($sDate, $xException=DEFAULT_DATE_DB_VALUE)
{
	if (empty($sDate))
	{
		return $xException;
	}
	if (preg_match('/^\D*(\d{1,2})\D+(\d{1,2})\D+(\d{2,4}).*$/', $sDate, $aMatches))
	{
		$year = $aMatches[3];
		$month = $aMatches[2];
		$day = $aMatches[1];

		if ((int)$year > 1970)
		{
			$timestamp = mktime (0, 0, 0, $month, $day, $year);
			$sDate = @date(DEFAULT_DATE_DB_FORMAT, $timestamp);
			if (empty($sDate))
			    $sDate = $xException;
			return $sDate;
		}
		else
		{
			$isValid = checkdate($month, $day, $year);
			if ($isValid)
			    $sDate = $year.'-'.$month.'-'.$day;
			else
			    $sDate = $xException;
			return $sDate;
		}
	}
	return $xException;
}
//=========================================================
function formatDate($sDate, $format=DEFAULT_DATE_DISPLAY_FORMAT, $sEmpty=DEFAULT_DATE_DB_VALUE, $nTranslationType=1)
{
	global $aDefMonths, $aDefMonthsShort, $aDefDays, $aDefDaysShort;

	$str = trim($sDate);
	if ($str == DEFAULT_DATE_DB_VALUE || empty($str))
	{
		return $sEmpty;
	}
	else
	{
		//$str = @date($format,strtotime($str));
		$aParts = explode(' ',$str);
		$aParts = explode('-',$aParts[0]);
		$year = $aParts[0];
		$month = $aParts[1];
		$day = $aParts[2];

		if ((int)$year > 1970)
		{
			$timestamp = mktime (0, 0, 0, $month, $day, $year);
			$sDate = date($format, $timestamp);
			if (empty($sDate))
			    $sDate = $sEmpty;
		}
		else
		{
			$isValid = checkdate($month, $day, $year);
			if ($isValid)
			    $sDate = $day.'.'.$month.'.'.$year;
			else
			    $sDate = $sEmpty;
		}
		// imitate utf locales ;))
		$aLangMonths = getLabel('aMonths');
		$aLangDays = getLabel('aDays');
		$aLangMonthsShort = getLabel('aMonthsShort');
		$aLangDaysShort = getLabel('aDaysShort');
		// first check for long dates, then for short ones
		$sDate = str_replace(array_values($aDefMonths), array_values($aLangMonths), $sDate);
		$sDate = str_replace(array_values($aDefDays), array_values($aLangDays), $sDate);
		$sDate = str_replace(array_values($aDefMonthsShort), array_values($aLangMonthsShort), $sDate);
		$sDate = str_replace(array_values($aDefDaysShort), array_values($aLangDaysShort), $sDate);
		/*switch($nTranslationType)
		{
			case 1:
				$aLangMonths = getLabel('aMonths');
				$aLangDays = getLabel('aDays');
				$sDate = str_replace(array_values($aDefMonths), array_values($aLangMonths), $sDate);
				$sDate = str_replace(array_values($aDefDays), array_values($aLangDays), $sDate);
				break;
			case 2:
				$aLangMonths = getLabel('aMonthsShort');
				$aLangDays = getLabel('aDaysShort');
				$sDate = str_replace(array_values($aDefMonthsShort), array_values($aLangMonths), $sDate);
				$sDate = str_replace(array_values($aDefDaysShort), array_values($aLangDays), $sDate);
				break;
			default:
				//
				break;
		}*/
		//
		return $sDate;
	}
}
//=========================================================
function formatDateTime($sDate, $format=DEFAULT_DATE_DISPLAY_FORMAT, $sEmpty=DEFAULT_DATE_DB_VALUE, $bSkipTranslation = false)
{
	global $aDefMonths, $aDefDays;

	$str = trim($sDate);
	if ($str == DEFAULT_DATE_DB_VALUE || empty($str))
	{
		return $sEmpty;
	}
	else
	{
		$sDate = @date($format, strtotime($str));

		return $sDate;
	}
}
//=========================================================
function parseTime($sTime)
{
	$sTime = str_replace('.', ':', $sTime);
	$sTime = str_replace(',', ':', $sTime);

	return $sTime;
}
//=========================================================
function formatTime($sTime)
{
	return substr($sTime, 0, 5);
}
//=========================================================
function increaseDate($sDate, $nDays, $nMonths=0, $format=DEFAULT_DATE_DB_FORMAT, $sEmpty=DEFAULT_DATE_DB_VALUE)
{
	$str = trim($sDate);
	if ($str == DEFAULT_DATE_DB_VALUE || empty($str))
	{
		return $sEmpty;
	}
	else
	{
		//$str = @date($format,strtotime($str));
		$aParts = explode('-',$str);
		$year = $aParts[0];
		$month = $aParts[1];
		$day = $aParts[2];

		if ((int)$year > 1970)
		{
			$timestamp = mktime (0, 0, 0, $month+$nMonths, $day+$nDays, $year);
			$sDate = date($format, $timestamp);
			if (empty($sDate))
			    $sDate = $sEmpty;
		}
		else
		{
			$sDate = $sEmpty;
		}
		return $sDate;
	}
}
//=========================================================
function getWeekDay($sDate)
{
	$date_year=(int)substr($sDate,0,4);
        $date_month=(int)substr($sDate,5,2);
        $date_day=(int)substr($sDate,8,2);
	$tStamp = mktime(0, 0, 0, $date_month, $date_day, $date_year);
	return date('w', $tStamp);
}
//============================================================
//increase time with interval in minutes
function increaseTime($interval, $format=DEFAULT_DATE_DB_FORMAT)
{
	return date($format, mktime(date('H'), date("i")+$interval, 0, date('n'), date("j"), date("Y")));
}
?>