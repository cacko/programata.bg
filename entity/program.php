<?php
if (!$bInSite) die();
//=========================================================
class Program
{
	var $_sTable = null;
	var $_sRelPageTable = null;
	var $_sRelPlaceTable = null;
	var $_sRelEventTable = null;
	var $_sEventTable = null;
	var $_sPlaceTable = null;
	var $_sRelLabelTable = null;
	var $_sRelDatePeriodTable = null;
	var $_sRelDateTimeTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;

	function Program($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblProgram';
		$this->_sRelPageTable = DB_TABLE_PREFIX.'relProgram2Page';
		$this->_sRelPlaceTable = DB_TABLE_PREFIX.'relProgram2Place';
		$this->_sRelEventTable = DB_TABLE_PREFIX.'relProgram2Event';
		$this->_sEventTable = DB_TABLE_PREFIX.'tblEvent';
		$this->_sPlaceTable = DB_TABLE_PREFIX.'tblPlace';
		$this->_sRelLabelTable = DB_TABLE_PREFIX.'relEvent2Label';
		$this->_sRelDatePeriodTable = DB_TABLE_PREFIX.'tblProgramDatePeriod';
		$this->_sRelDateTimeTable = DB_TABLE_PREFIX.'tblProgramDateTime';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;

		$this->_aFields = array();
		$this->_aFields[1] = 'ProgramID';
		$this->_aFields[2] = 'ProgramTypeID';
		$this->_aFields[3] = 'FestivalID';
		$this->_aFields[4] = 'PlaceID';
		$this->_aFields[5] = 'PlaceHallID';
		$this->_aFields[6] = 'EventID';
		$this->_aFields[7] = 'PremiereTypeID';
		$this->_aFields[8] = 'LastUpdate';
		$this->_aFields[9] = 'LastUpdateUserID';
		$this->_aFields[10] = 'IsHidden';
		$this->_aFields[11] = 'Title';
		$this->_aFields[12] = 'CityID';
		$this->_aFields[13] = 'ProgramDate, ProgramTime';

		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[4];
	}

	// used in program list (admin)
	function ListAll($xParentID=null, $nProgramTypeID=null, $nFestivalID=null, $nPremiereTypeID=null, $nCityID=null, $nPlaceID=null, $nEventID=null, $dStartDate=null, $dEndDate=null, $bShowHidden = false, $nMaxRows=0, $aOrder=null)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT DISTINCT a.*, r4.Title as EventTitle, r5.Title as PlaceTitle FROM '.$this->_sTable.' as a
					LEFT OUTER JOIN '.$this->_sRelPageTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.ProgramID
					LEFT OUTER JOIN '.$this->_sEventTable.' as r4 ON a.EventID=r4.EventID
						AND r4.LanguageID='.$this->_nLangID.'
					LEFT OUTER JOIN '.$this->_sPlaceTable.' as r5 ON a.PlaceID=r5.PlaceID
						AND r5.LanguageID='.$this->_nLangID.'
					LEFT OUTER JOIN '.$this->_sRelDatePeriodTable.' as r2 ON a.'.$this->_sPrimaryKey.'=r2.ProgramID AND r2.IsHidden="'.B_FALSE.'"
					LEFT OUTER JOIN '.$this->_sRelDateTimeTable.' as r3 ON a.'.$this->_sPrimaryKey.'=r3.ProgramID AND r3.IsHidden="'.B_FALSE.'"
					WHERE 1=1 ';
		if (!is_null($xParentID))
		{
			if (is_array($xParentID) && count($xParentID) > 0)
				$sSQL .= ' AND r1.PageID IN ('.join(',', $xParentID).')';
			elseif (is_numeric($xParentID) && !empty($xParentID))
				$sSQL .= ' AND r1.PageID="'.$xParentID.'" ';
		}
		if (!is_null($nProgramTypeID) && !empty($nProgramTypeID))
		{
			$sSQL .=' AND ProgramTypeID="'.$nProgramTypeID.'" ';
		}
		if (!is_null($nFestivalID) && !empty($nFestivalID))
		{
			$sSQL .=' AND FestivalID="'.$nFestivalID.'" ';
		}
		if (!is_null($nPremiereTypeID) && !empty($nPremiereTypeID))
		{
			$sSQL .=' AND PremiereTypeID="'.$nPremiereTypeID.'" ';
		}
		if (!is_null($nCityID) && !empty($nCityID))
		{
			$sSQL .=' AND a.CityID="'.$nCityID.'" ';
		}
		if (!is_null($nPlaceID) && !empty($nPlaceID))
		{
			$sSQL .=' AND a.PlaceID="'.$nPlaceID.'" ';
		}
		if (!is_null($nEventID) && !empty($nEventID))
		{
			$sSQL .=' AND a.EventID="'.$nEventID.'" ';
		}
		if (!is_null($dStartDate) && !is_null($dEndDate))
		{
//			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND r3.ProgramDate<="'.$dEndDate.'")
//					OR (StartDate>="'.$dStartDate.'" AND StartDate<="'.$dEndDate.'")
//					OR (EndDate>="'.$dStartDate.'" AND EndDate<="'.$dEndDate.'")
//					OR (StartDate<="'.$dStartDate.'" AND EndDate>="'.$dEndDate.'"))';
			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND r3.ProgramDate<="'.$dEndDate.'")
					OR (StartDate<="'.$dEndDate.'" AND EndDate>="'.$dStartDate.'"))';
		}
		else
		{
			if (!is_null($dStartDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dStartDate.'" AND EndDate<="'.$dStartDate.'")
						OR ProgramDate="'.$dStartDate.'") ';
			}
			if (!is_null($dEndDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dEndDate.'" AND EndDate<="'.$dEndDate.'")
						OR ProgramDate="'.$dEndDate.'") ';
			}
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND a.IsHidden="'.B_FALSE.'" ';
		}
		//order
		if (!isset($aOrder) || !is_array($aOrder) || empty($aOrder[SORT_FIELD]))
		{
			$sSQL .=' ORDER BY r5.Title, r4.Title ASC';
		}
		else
		{
			$nSortField = $aOrder[SORT_FIELD];
			$nSortDir = $aOrder[SORT_DIR];
			if(in_array($nSortField, array_keys($this->_aFields)))
			{
				$sSQL .=' ORDER BY '.$this->_aFields[$nSortField].$aSortDirections[$nSortDir];
			}
		}
		if (!empty($nMaxRows))
		{
			$sSQL .=' LIMIT '.$nMaxRows.' ';
		}
		//echo $sSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $result;
	}

	function ListAllByDate($nPlaceID=null, $nEventID=null, $nFestivalID=null, $nCityID=null, $dStartDate=null, $dEndDate=null, $bShowHidden = false, $aOrder=null)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT DISTINCT a.*, a.ProgramID as MainProgramID, r2.*, r3.* FROM '.$this->_sTable.' as a
					LEFT OUTER JOIN '.$this->_sRelDatePeriodTable.' as r2 ON a.'.$this->_sPrimaryKey.'=r2.ProgramID AND r2.IsHidden="'.B_FALSE.'"
					LEFT OUTER JOIN '.$this->_sRelDateTimeTable.' as r3 ON a.'.$this->_sPrimaryKey.'=r3.ProgramID AND r3.IsHidden="'.B_FALSE.'"
					WHERE 1=1 ';
		if (!is_null($nPlaceID) && !empty($nPlaceID))
		{
			$sSQL .=' AND PlaceID="'.$nPlaceID.'" ';
		}
		if (!is_null($nEventID) && !empty($nEventID))
		{
			$sSQL .=' AND EventID="'.$nEventID.'" ';
		}
		if (!is_null($nFestivalID) && !empty($nFestivalID))
		{
			$sSQL .=' AND FestivalID="'.$nFestivalID.'" ';
		}
		if (!is_null($nCityID) && !empty($nCityID))
		{
			$sSQL .=' AND CityID="'.$nCityID.'" ';
		}
		if (!is_null($dStartDate) && !is_null($dEndDate))
		{
//			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
//					OR (StartDate>="'.$dStartDate.'" AND StartDate<="'.$dEndDate.'")
//					OR (EndDate>="'.$dStartDate.'" AND EndDate<="'.$dEndDate.'")
//					OR (StartDate<="'.$dStartDate.'" AND EndDate>="'.$dEndDate.'"))';
			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
					OR (StartDate<="'.$dEndDate.'" AND EndDate>="'.$dStartDate.'"))';
					}
		else
		{
			if (!is_null($dStartDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dStartDate.'" AND EndDate<="'.$dStartDate.'")
						OR ProgramDate="'.$dStartDate.'") ';
			}
			if (!is_null($dEndDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dEndDate.'" AND EndDate<="'.$dEndDate.'")
						OR ProgramDate="'.$dEndDate.'") ';
			}
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND a.IsHidden="'.B_FALSE.'" ';
		}
		//order
		if (!isset($aOrder) || !is_array($aOrder) || empty($aOrder[SORT_FIELD]))
		{
			$sSQL .=' ORDER BY StartDate ASC, ProgramDate ASC, ProgramTime ASC';
		}
		else
		{
			$nSortField = $aOrder[SORT_FIELD];
			$nSortDir = $aOrder[SORT_DIR];
			if(in_array($nSortField, array_keys($this->_aFields)))
			{
				$sSQL .=' ORDER BY '.$this->_aFields[$nSortField].$aSortDirections[$nSortDir];
			}
		}
		//echo '<!--'.$sSQL.'-->';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $result;
	}

	// used in all event lists in website, daily report by event
	function ListAllEvents($xParentID=null, $nProgramTypeID=null, $nCityID=null, $sKeyword='', $xLetter='', $dStartDate=null, $dEndDate=null, $dStartTime=null, $dEndTime=null, $bPremiere = false, $bShowHidden = false, $aOrder=null)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT DISTINCT a.*, a.ProgramID as MainProgramID, r1.*, r2.*, r3.*, r4.*, r5.Title as PlaceTitle, r5.ShortTitle
				FROM '.$this->_sTable.' as a
					LEFT OUTER JOIN '.$this->_sRelPageTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.ProgramID
					LEFT OUTER JOIN '.$this->_sEventTable.' as r2 ON a.EventID=r2.EventID
						AND r2.LanguageID='.$this->_nLangID.'
					LEFT OUTER JOIN '.$this->_sPlaceTable.' as r5 ON a.PlaceID=r5.PlaceID
						AND r5.LanguageID='.$this->_nLangID.'
					LEFT OUTER JOIN '.$this->_sRelDateTimeTable.' as r3 ON a.'.$this->_sPrimaryKey.'=r3.ProgramID AND r3.IsHidden="'.B_FALSE.'"
					LEFT OUTER JOIN '.$this->_sRelDatePeriodTable.' as r4 ON a.'.$this->_sPrimaryKey.'=r4.ProgramID AND r4.IsHidden="'.B_FALSE.'"
					WHERE 1=1 ';
		if (!is_null($xParentID))
		{
			if (is_array($xParentID) && count($xParentID) > 0)
			{
				/*if (count($xParentID) > 1)
				{
					$aParentsToInclude = array();
					$sParentsToExclude = array();
					foreach($key in $xParentID)
					{
						if ($key > 0)
							$aParentsToInclude[] = $key;
						else
							$sParentsToExclude[] = -$key;
					}
					$sSQL .= ' AND r1.PageID IN ('.join(',', $aParentsToInclude).') ';
					if (count($sParentsToExclude) > 0)
						$sSQL .= ' AND r1.PageID NOT IN ('.join(',', $sParentsToExclude).') ';
				}
				else*/
				$sSQL .= ' AND r1.PageID IN ('.join(',', $xParentID).')';
			}
			elseif (is_numeric($xParentID) && !empty($xParentID))
				$sSQL .= ' AND r1.PageID="'.$xParentID.'" ';
		}
		if (!is_null($nProgramTypeID) && !empty($nProgramTypeID))
		{
			$sSQL .=' AND ProgramTypeID="'.$nProgramTypeID.'" ';
		}
		if (!is_null($nCityID) && !empty($nCityID))
		{
			$sSQL .=' AND a.CityID="'.$nCityID.'" ';
		}
		if (!empty($sKeyword))
		{
			$sSQL .= ' AND (r2.Title LIKE "%'.$sKeyword.'%" OR
					r2.OriginalTitle LIKE "%'.$sKeyword.'%" OR
					r2.MetaKeywords LIKE "%'.$sKeyword.'%" OR
					r2.MetaDescription LIKE "%'.$sKeyword.'%" OR
					r2.Description LIKE "%'.$sKeyword.'%" OR
					r2.Features LIKE "%'.$sKeyword.'%" OR
					r2.Comment LIKE "%'.$sKeyword.'%") ';
		}
		//if (!empty($sLetter))
		if ($xLetter != '')
		{
			if (is_array($xLetter) && count($xLetter) > 0)
			{
				$sSQL .= ' AND r2.Letter IN ("'.join('","', $xLetter).'") ';
			}
			else
				$sSQL .= ' AND r2.Letter="'.$xLetter.'" ';
		}
		if (!is_null($dStartDate) && !is_null($dEndDate) && !is_null($dStartTime) && !is_null($dEndTime))
		{
			if($dStartDate == $dEndDate)
			{
				$sSQL .= ' 	AND ((ProgramTime>="'.$dStartTime.'" AND ProgramTime<="'.$dEndTime.'"))
							AND ((StartDate>="'.$dStartDate.'" AND EndDate<="'.$dStartDate.'")
							OR ProgramDate="'.$dStartDate.'") ';
			}
			else
			{
			$sSQL .= ' AND ((ProgramDate>"'.$dStartDate.'" AND ProgramDate<"'.$dEndDate.'")
						OR (ProgramDate="'.$dStartDate.'" AND ProgramTime>="'.$dStartTime.'")
						OR (ProgramDate="'.$dEndDate.'" AND ProgramTime<="'.$dEndTime.'"))';
			}
		}
		else
		{
			if (!is_null($dStartDate) && !is_null($dEndDate))
			{
				/*$sSQL .= ' AND (ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'") ';*/
	//			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
	//					OR ((StartDate>="'.$dStartDate.'" AND StartDate<="'.$dEndDate.'")
	//					OR (EndDate>="'.$dStartDate.'" AND EndDate<="'.$dEndDate.'")
	//					OR (StartDate<="'.$dStartDate.'" AND EndDate>="'.$dEndDate.'")))';
				$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
						OR (StartDate<="'.$dEndDate.'" AND EndDate>="'.$dStartDate.'"))';
			}
			else
			{
				if (!is_null($dStartDate))
				{
					$sSQL .=' AND ((StartDate>="'.$dStartDate.'" AND EndDate<="'.$dStartDate.'")
							OR ProgramDate="'.$dStartDate.'") ';/**/
				}
				if (!is_null($dEndDate))
				{
					$sSQL .=' AND ((StartDate>="'.$dEndDate.'" AND EndDate<="'.$dEndDate.'")
							OR ProgramDate="'.$dEndDate.'") ';/**/
				}
			}
			if (!is_null($dStartTime) && !is_null($dEndTime))
			{
				$sSQL .= ' AND ((ProgramTime>="'.$dStartTime.'" AND ProgramTime<="'.$dEndTime.'"))';
			}
			else
			{
				if (!is_null($dStartTime))
				{
					$sSQL .=' AND (ProgramTime="'.$dStartTime.'") ';
				}
				if (!is_null($dEndTime))
				{
					$sSQL .=' AND (ProgramTime="'.$dEndTime.'") ';
				}
			}
		}
		if ($bPremiere)
		{
			$sSQL .= ' AND a.PremiereTypeID>"'.B_FALSE.'" ';
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND a.IsHidden="'.B_FALSE.'" ';
		}
		$sSQL .=' GROUP BY a.ProgramID '; //a.EventID
		//order
		if (!isset($aOrder) || !is_array($aOrder) || empty($aOrder[SORT_FIELD]))
		{
			$sSQL .=' ORDER BY r2.Title ASC, r5.Title ASC, ProgramDate ASC, ProgramTime ASC'; //, StartDate ASC, ProgramTime ASC
		}
		else
		{
			$nSortField = $aOrder[SORT_FIELD];
			$nSortDir = $aOrder[SORT_DIR];
			if(in_array($nSortField, array_keys($this->_aFields)))
			{
				$sSQL .=' ORDER BY '.$this->_aFields[$nSortField].$aSortDirections[$nSortDir];
			}
		}
		//echo '<!--'.$sSQL.'-->';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $result;
	}

	// used in search results event lists in website
	function ListAllEventsAdvanced($xParentID=null, $nProgramTypeID=null, $nCityID=null, $sKeyword='', $xLetter='',
				       $nMusicStyle, $nGenre, $nGenreGroup, $nOrigLanguage, $nTranslation,
				       $dStartDate=null, $dEndDate=null, $dStartTime=null, $dEndTime=null,
				       $bPremiere = false, $bShowHidden = false, $aOrder=null)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT DISTINCT a.*, a.ProgramID as MainProgramID, r1.*, r2.*, r3.*, r4.*, r5.Title as PlaceTitle, r5.ShortTitle
				FROM '.$this->_sTable.' as a
					LEFT OUTER JOIN '.$this->_sRelPageTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.ProgramID
					LEFT JOIN '.$this->_sEventTable.' as r2 ON a.EventID=r2.EventID
						AND r2.LanguageID='.$this->_nLangID.'
					LEFT OUTER JOIN '.$this->_sRelLabelTable.' as r12 ON a.EventID=r12.EventID AND r12.ParentLabelID="'.GRP_MUSIC.'"
					LEFT OUTER JOIN '.$this->_sRelLabelTable.' as r13 ON a.EventID=r13.EventID AND r13.ParentLabelID="'.$nGenreGroup.'"
					LEFT OUTER JOIN '.$this->_sRelLabelTable.' as r14 ON a.EventID=r14.EventID AND r14.ParentLabelID="'.GRP_LANG.'"
					LEFT OUTER JOIN '.$this->_sRelLabelTable.' as r15 ON a.EventID=r15.EventID AND r15.ParentLabelID="'.GRP_TRANS.'"
					LEFT OUTER JOIN '.$this->_sPlaceTable.' as r5 ON a.PlaceID=r5.PlaceID
						AND r5.LanguageID='.$this->_nLangID.'
					LEFT OUTER JOIN '.$this->_sRelDateTimeTable.' as r3 ON a.'.$this->_sPrimaryKey.'=r3.ProgramID AND r3.IsHidden="'.B_FALSE.'"
					LEFT OUTER JOIN '.$this->_sRelDatePeriodTable.' as r4 ON a.'.$this->_sPrimaryKey.'=r4.ProgramID AND r4.IsHidden="'.B_FALSE.'"
					WHERE 1=1 ';
		if (!is_null($nMusicStyle) && !empty($nMusicStyle))
		{
			$sSQL .=' AND r12.LabelID="'.$nMusicStyle.'" AND r12.ParentLabelID="'.GRP_MUSIC.'" ';
		}
		if (!is_null($nGenre) && !empty($nGenre))
		{
			$sSQL .=' AND r13.LabelID="'.$nGenre.'" AND r13.ParentLabelID="'.$nGenreGroup.'" ';
		}
		if (!is_null($nOrigLanguage) && !empty($nOrigLanguage))
		{
			$sSQL .=' AND r14.LabelID="'.$nOrigLanguage.'" AND r14.ParentLabelID="'.GRP_LANG.'" ';
		}
		if (!is_null($nTranslation) && !empty($nTranslation))
		{
			$sSQL .=' AND r15.LabelID="'.$nTranslation.'" AND r15.ParentLabelID="'.GRP_TRANS.'" ';
		}
		if (!is_null($xParentID))
		{
			if (is_array($xParentID) && count($xParentID) > 0)
				$sSQL .= ' AND r1.PageID IN ('.join(',', $xParentID).')';
			elseif (is_numeric($xParentID) && !empty($xParentID))
				$sSQL .= ' AND r1.PageID="'.$xParentID.'" ';
		}
		if (!is_null($nProgramTypeID) && !empty($nProgramTypeID))
		{
			$sSQL .=' AND ProgramTypeID="'.$nProgramTypeID.'" ';
		}
		if (!is_null($nCityID) && !empty($nCityID))
		{
			$sSQL .=' AND a.CityID="'.$nCityID.'" ';
		}
		if (!empty($sKeyword))
		{
			$sSQL .= ' AND (r2.Title LIKE "%'.$sKeyword.'%" OR
					r2.OriginalTitle LIKE "%'.$sKeyword.'%" OR
					r2.MetaKeywords LIKE "%'.$sKeyword.'%" OR
					r2.MetaDescription LIKE "%'.$sKeyword.'%" OR
					r2.Description LIKE "%'.$sKeyword.'%" OR
					r2.Features LIKE "%'.$sKeyword.'%" OR
					r2.Comment LIKE "%'.$sKeyword.'%") ';
		}
		//if (!empty($sLetter))
		if ($xLetter != '')
		{
			if (is_array($xLetter) && count($xLetter) > 0)
			{
				$sSQL .= ' AND r2.Letter IN ("'.join('","', $xLetter).'") ';
			}
			else
				$sSQL .= ' AND r2.Letter="'.$xLetter.'" ';
		}
		if (!is_null($dStartDate) && !is_null($dEndDate))
		{
			/*$sSQL .= ' AND (ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'") ';*/
//			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
//					OR ((StartDate>="'.$dStartDate.'" AND StartDate<="'.$dEndDate.'")
//					OR (EndDate>="'.$dStartDate.'" AND EndDate<="'.$dEndDate.'")
//					OR (StartDate<="'.$dStartDate.'" AND EndDate>="'.$dEndDate.'")))';
			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
					OR (StartDate<="'.$dEndDate.'" AND EndDate>="'.$dStartDate.'"))';
					}
		else
		{
			if (!is_null($dStartDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dStartDate.'" AND EndDate<="'.$dStartDate.'")
						OR ProgramDate="'.$dStartDate.'") ';/**/
			}
			if (!is_null($dEndDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dEndDate.'" AND EndDate<="'.$dEndDate.'")
						OR ProgramDate="'.$dEndDate.'") ';/**/
			}
		}
		if (!is_null($dStartTime) && !is_null($dEndTime))
		{
			$sSQL .= ' AND ((ProgramTime>="'.$dStartTime.'" AND ProgramTime<="'.$dEndTime.'"))';
		}
		else
		{
			if (!is_null($dStartTime))
			{
				$sSQL .=' AND (ProgramTime="'.$dStartTime.'") ';
			}
			if (!is_null($dEndTime))
			{
				$sSQL .=' AND (ProgramTime="'.$dEndTime.'") ';
			}
		}
		if ($bPremiere)
		{
			$sSQL .= ' AND a.PremiereTypeID>"'.B_FALSE.'" ';
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND a.IsHidden="'.B_FALSE.'" ';
		}
		$sSQL .=' GROUP BY a.ProgramID '; //a.EventID
		//order
		if (!isset($aOrder) || !is_array($aOrder) || empty($aOrder[SORT_FIELD]))
		{
			$sSQL .=' ORDER BY r2.Title ASC, r5.Title ASC, ProgramDate ASC, ProgramTime ASC'; //, StartDate ASC, ProgramTime ASC
		}
		else
		{
			$nSortField = $aOrder[SORT_FIELD];
			$nSortDir = $aOrder[SORT_DIR];
			if(in_array($nSortField, array_keys($this->_aFields)))
			{
				$sSQL .=' ORDER BY '.$this->_aFields[$nSortField].$aSortDirections[$nSortDir];
			}
		}
		//echo $sSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $result;
	}

	// used in weekly report by event, same as ListAllEvents(), with different order
	function ListAllEventsByFestival($xParentID=null, $nProgramTypeID=null, $nCityID=null, $sKeyword='', $xLetter='', $dStartDate=null, $dEndDate=null, $dStartTime=null, $dEndTime=null, $bPremiere = false, $bShowHidden = false, $aOrder=null)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT DISTINCT a.*, a.ProgramID as MainProgramID, r1.*, r2.*, r3.*, r4.*, r5.Title as PlaceTitle, r5.ShortTitle
				FROM '.$this->_sTable.' as a
					LEFT OUTER JOIN '.$this->_sRelPageTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.ProgramID
					LEFT OUTER JOIN '.$this->_sEventTable.' as r2 ON a.EventID=r2.EventID
						AND r2.LanguageID='.$this->_nLangID.'
					LEFT OUTER JOIN '.$this->_sPlaceTable.' as r5 ON a.PlaceID=r5.PlaceID
						AND r5.LanguageID='.$this->_nLangID.'
					LEFT OUTER JOIN '.$this->_sRelDateTimeTable.' as r3 ON a.'.$this->_sPrimaryKey.'=r3.ProgramID AND r3.IsHidden="'.B_FALSE.'"
					LEFT OUTER JOIN '.$this->_sRelDatePeriodTable.' as r4 ON a.'.$this->_sPrimaryKey.'=r4.ProgramID AND r4.IsHidden="'.B_FALSE.'"
					WHERE 1=1 ';
		if (!is_null($xParentID))
		{
			if (is_array($xParentID) && count($xParentID) > 0)
				$sSQL .= ' AND PageID IN ('.join(',', $xParentID).')';
			elseif (is_numeric($xParentID) && !empty($xParentID))
				$sSQL .= ' AND PageID="'.$xParentID.'" ';
		}
		if (!is_null($nProgramTypeID) && !empty($nProgramTypeID))
		{
			$sSQL .=' AND ProgramTypeID="'.$nProgramTypeID.'" ';
		}
		if (!is_null($nCityID) && !empty($nCityID))
		{
			$sSQL .=' AND a.CityID="'.$nCityID.'" ';
		}
		if (!empty($sKeyword))
		{
			$sSQL .= ' AND (r2.Title LIKE "%'.$sKeyword.'%" OR
					r2.OriginalTitle LIKE "%'.$sKeyword.'%" OR
					r2.MetaKeywords LIKE "%'.$sKeyword.'%" OR
					r2.MetaDescription LIKE "%'.$sKeyword.'%" OR
					r2.Description LIKE "%'.$sKeyword.'%" OR
					r2.Features LIKE "%'.$sKeyword.'%" OR
					r2.Comment LIKE "%'.$sKeyword.'%") ';
		}
		//if (!empty($sLetter))
		if ($xLetter != '')
		{
			if (is_array($xLetter) && count($xLetter) > 0)
			{
				$sSQL .= ' AND r2.Letter IN ("'.join('","', $xLetter).'") ';
			}
			else
				$sSQL .= ' AND r2.Letter="'.$xLetter.'" ';
		}
		if (!is_null($dStartDate) && !is_null($dEndDate))
		{
			/*$sSQL .= ' AND (ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'") ';*/
//			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
//					OR ((StartDate>="'.$dStartDate.'" AND StartDate<="'.$dEndDate.'")
//					OR (EndDate>="'.$dStartDate.'" AND EndDate<="'.$dEndDate.'")
//					OR (StartDate<="'.$dStartDate.'" AND EndDate>="'.$dEndDate.'")))';
			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
					OR (StartDate<="'.$dEndDate.'" AND EndDate>="'.$dStartDate.'"))';
					}
		else
		{
			if (!is_null($dStartDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dStartDate.'" AND EndDate<="'.$dStartDate.'")
						OR ProgramDate="'.$dStartDate.'") ';/**/
			}
			if (!is_null($dEndDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dEndDate.'" AND EndDate<="'.$dEndDate.'")
						OR ProgramDate="'.$dEndDate.'") ';/**/
			}
		}
		if (!is_null($dStartTime) && !is_null($dEndTime))
		{
			$sSQL .= ' AND ((ProgramTime>="'.$dStartTime.'" AND ProgramTime<="'.$dEndTime.'"))';
		}
		else
		{
			if (!is_null($dStartTime))
			{
				$sSQL .=' AND (ProgramTime="'.$dStartTime.'") ';
			}
			if (!is_null($dEndTime))
			{
				$sSQL .=' AND (ProgramTime="'.$dEndTime.'") ';
			}
		}
		if ($bPremiere)
		{
			$sSQL .= ' AND a.PremiereTypeID>"'.B_FALSE.'" ';
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND a.IsHidden="'.B_FALSE.'" ';
		}
		$sSQL .=' GROUP BY a.ProgramID '; //a.EventID
		//order
		if (!isset($aOrder) || !is_array($aOrder) || empty($aOrder[SORT_FIELD]))
		{
			$sSQL .=' ORDER BY a.FestivalID ASC, r2.Title ASC, r5.Title ASC, ProgramDate ASC, ProgramTime ASC'; //, StartDate ASC, ProgramTime ASC
		}
		else
		{
			$nSortField = $aOrder[SORT_FIELD];
			$nSortDir = $aOrder[SORT_DIR];
			if(in_array($nSortField, array_keys($this->_aFields)))
			{
				$sSQL .=' ORDER BY '.$this->_aFields[$nSortField].$aSortDirections[$nSortDir];
			}
		}
		//echo $sSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $result;
	}

	// used in daily report by place, weekly report by place, party time & zavedenia pages in website (events list weekly by place)
	function ListAllPlaces($xParentID=null, $nProgramTypeID=null, $nCityID=null, $sKeyword='', $xLetter='', $dStartDate=null, $dEndDate=null, $dStartTime=null, $dEndTime=null, $bPremiere = false, $bShowHidden = false, $aOrder=null)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT DISTINCT a.*, a.ProgramID as MainProgramID, r1.*, r2.*, r3.*, r4.* FROM '.$this->_sTable.' as a
					LEFT OUTER JOIN '.$this->_sRelPageTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.ProgramID
					LEFT OUTER JOIN '.$this->_sPlaceTable.' as r2 ON a.PlaceID=r2.PlaceID
						AND r2.LanguageID='.$this->_nLangID.'
					LEFT OUTER JOIN '.$this->_sEventTable.' as r5 ON a.EventID=r5.EventID
						AND r5.LanguageID='.$this->_nLangID.'
					LEFT OUTER JOIN '.$this->_sRelDateTimeTable.' as r3 ON a.'.$this->_sPrimaryKey.'=r3.ProgramID AND r3.IsHidden="'.B_FALSE.'"
					LEFT OUTER JOIN '.$this->_sRelDatePeriodTable.' as r4 ON a.'.$this->_sPrimaryKey.'=r4.ProgramID AND r4.IsHidden="'.B_FALSE.'"
					WHERE 1=1 ';
		if (!is_null($xParentID))
		{
			if (is_array($xParentID) && count($xParentID) > 0)
				$sSQL .= ' AND PageID IN ('.join(',', $xParentID).')';
			elseif (is_numeric($xParentID) && !empty($xParentID))
				$sSQL .= ' AND PageID="'.$xParentID.'" ';
		}
		if (!is_null($nProgramTypeID) && !empty($nProgramTypeID))
		{
			$sSQL .=' AND ProgramTypeID="'.$nProgramTypeID.'" ';
		}
		if (!is_null($nCityID) && !empty($nCityID))
		{
			$sSQL .=' AND a.CityID="'.$nCityID.'" ';
		}
		if (!empty($sKeyword))
		{
			$sSQL .= ' AND (r2.Title LIKE "%'.$sKeyword.'%" OR
					r2.ShortTitle LIKE "%'.$sKeyword.'%" OR
					r2.MetaKeywords LIKE "%'.$sKeyword.'%" OR
					r2.MetaDescription LIKE "%'.$sKeyword.'%" OR
					r2.Description LIKE "%'.$sKeyword.'%" OR
					r2.Address LIKE "%'.$sKeyword.'%" OR
					r2.WorkingTime LIKE "%'.$sKeyword.'%") ';
		}
		//if (!empty($sLetter))
		if ($xLetter != '')
		{
			if (is_array($xLetter) && count($xLetter) > 0)
			{
				$sSQL .= ' AND r2.Letter IN ("'.join('","', $xLetter).'") ';
			}
			else
				$sSQL .= ' AND r2.Letter="'.$xLetter.'" ';
		}
		if (!is_null($dStartDate) && !is_null($dEndDate))
		{
			/*$sSQL .= ' AND (ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'") ';*/
//			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
//					OR ((StartDate>="'.$dStartDate.'" AND StartDate<="'.$dEndDate.'")
//					OR (EndDate>="'.$dStartDate.'" AND EndDate<="'.$dEndDate.'")
//					OR (StartDate<="'.$dStartDate.'" AND EndDate>="'.$dEndDate.'")))';
			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
					OR (StartDate<="'.$dEndDate.'" AND EndDate>="'.$dStartDate.'"))';
					}
		else
		{
			if (!is_null($dStartDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dStartDate.'" AND EndDate<="'.$dStartDate.'")
						OR ProgramDate="'.$dStartDate.'") ';/**/
			}
			if (!is_null($dEndDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dEndDate.'" AND EndDate<="'.$dEndDate.'")
						OR ProgramDate="'.$dEndDate.'") ';/**/
			}
		}
		if (!is_null($dStartTime) && !is_null($dEndTime))
		{
			$sSQL .= ' AND ((ProgramTime>="'.$dStartTime.'" AND ProgramTime<="'.$dEndTime.'"))';
		}
		else
		{
			if (!is_null($dStartTime))
			{
				$sSQL .=' AND (ProgramTime="'.$dStartTime.'") ';
			}
			if (!is_null($dEndTime))
			{
				$sSQL .=' AND (ProgramTime="'.$dEndTime.'") ';
			}
		}
		if ($bPremiere)
		{
			$sSQL .= ' AND a.PremiereTypeID>"'.B_FALSE.'" ';
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND a.IsHidden="'.B_FALSE.'" ';
		}
		$sSQL .=' GROUP BY a.ProgramID '; //a.EventID
		//order
		if (!isset($aOrder) || !is_array($aOrder) || empty($aOrder[SORT_FIELD]))
		{
			//a.FestivalID ASC,
			$sSQL .=' ORDER BY r2.Title ASC, r5.Title ASC, a.PlaceHallID ASC, ProgramDate ASC, ProgramTime ASC'; //, StartDate ASC
		}
		else
		{
			$nSortField = $aOrder[SORT_FIELD];
			$nSortDir = $aOrder[SORT_DIR];
			if(in_array($nSortField, array_keys($this->_aFields)))
			{
				$sSQL .=' ORDER BY '.$this->_aFields[$nSortField].$aSortDirections[$nSortDir];
			}
		}
		//echo '<!--'.$sSQL.'-->';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $result;
	}

	// used in place details
	function ListEventsByPlaceID($nPlaceID=null, $dStartDate=null, $dEndDate=null, $bShowHidden = false, $aOrder=null)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT DISTINCT a.*, r1.*, r2.*, r3.*, r4.PlaceID as RelPlaceID, a.ProgramID as MainProgramID FROM '.$this->_sTable.' as a
					LEFT OUTER JOIN '.$this->_sEventTable.' as r1 ON a.EventID=r1.EventID
						AND r1.LanguageID='.$this->_nLangID.'
					LEFT OUTER JOIN '.$this->_sRelPlaceTable.' as r4 ON a.ProgramID=r4.ProgramID
					LEFT OUTER JOIN '.$this->_sRelDatePeriodTable.' as r2 ON a.'.$this->_sPrimaryKey.'=r2.ProgramID AND r2.IsHidden="'.B_FALSE.'"
					LEFT OUTER JOIN '.$this->_sRelDateTimeTable.' as r3 ON a.'.$this->_sPrimaryKey.'=r3.ProgramID AND r3.IsHidden="'.B_FALSE.'"
					WHERE 1=1 ';
		if (!is_null($nPlaceID) && !empty($nPlaceID))
		{
			$sSQL .=' AND (a.PlaceID="'.$nPlaceID.'" OR r4.PlaceID="'.$nPlaceID.'") ';
		}
		if (!is_null($dStartDate) && !is_null($dEndDate))
		{
//			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
//					OR (StartDate>="'.$dStartDate.'" AND StartDate<="'.$dEndDate.'")
//					OR (EndDate>="'.$dStartDate.'" AND EndDate<="'.$dEndDate.'")
//					OR (StartDate<="'.$dStartDate.'" AND EndDate>="'.$dEndDate.'"))';
			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
					OR (StartDate<="'.$dEndDate.'" AND EndDate>="'.$dStartDate.'"))';
					}
		else
		{
			if (!is_null($dStartDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dStartDate.'" AND EndDate<="'.$dStartDate.'")
						OR ProgramDate="'.$dStartDate.'") ';
			}
			if (!is_null($dEndDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dEndDate.'" AND EndDate<="'.$dEndDate.'")
						OR ProgramDate="'.$dEndDate.'") ';
			}
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND a.IsHidden="'.B_FALSE.'" ';
		}
		//$sSQL .= ' GROUP BY a.EventID '; // da ne se slaga, shtoto ne izlizat multiple chasovete na filmite
		//order
		if (!isset($aOrder) || !is_array($aOrder) || empty($aOrder[SORT_FIELD]))
		{
			$sSQL .=' ORDER BY Title ASC, ProgramDate ASC, ProgramTime ASC, StartDate ASC';
		}
		else
		{
			$nSortField = $aOrder[SORT_FIELD];
			$nSortDir = $aOrder[SORT_DIR];
			if(in_array($nSortField, array_keys($this->_aFields)))
			{
				$sSQL .=' ORDER BY '.$this->_aFields[$nSortField].$aSortDirections[$nSortDir];
			}
		}
		//echo '<!--'.$sSQL.'-->';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $result;
	}

	// used in event details
	function ListPlacesByEventID($nEventID=null, $nCityID=null, $dStartDate=null, $dEndDate=null, $bShowHidden = false, $aOrder=null)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT DISTINCT a.*, r1.*, r2.*, r3.*, r4.EventID as RelEventID, a.ProgramID as MainProgramID FROM '.$this->_sTable.' as a
					LEFT OUTER JOIN '.$this->_sPlaceTable.' as r1 ON a.PlaceID=r1.PlaceID
						AND r1.LanguageID='.$this->_nLangID.'
					LEFT OUTER JOIN '.$this->_sRelEventTable.' as r4 ON a.ProgramID=r4.ProgramID
					LEFT OUTER JOIN '.$this->_sRelDatePeriodTable.' as r2 ON a.'.$this->_sPrimaryKey.'=r2.ProgramID AND r2.IsHidden="'.B_FALSE.'"
					LEFT OUTER JOIN '.$this->_sRelDateTimeTable.' as r3 ON a.'.$this->_sPrimaryKey.'=r3.ProgramID AND r3.IsHidden="'.B_FALSE.'"
					WHERE 1=1 ';
		if (!is_null($nCityID) && !empty($nCityID))
		{
			$sSQL .=' AND a.CityID="'.$nCityID.'" ';
		}
		if (!is_null($nEventID) && !empty($nEventID))
		{
			$sSQL .=' AND (a.EventID="'.$nEventID.'" OR r4.EventID="'.$nEventID.'") ';
		}
		if (!is_null($dStartDate) && !is_null($dEndDate))
		{
//			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
//					OR (StartDate>="'.$dStartDate.'" AND StartDate<="'.$dEndDate.'")
//					OR (EndDate>="'.$dStartDate.'" AND EndDate<="'.$dEndDate.'")
//					OR (StartDate<="'.$dStartDate.'" AND EndDate>="'.$dEndDate.'"))';
			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
					OR (StartDate<="'.$dEndDate.'" AND EndDate>="'.$dStartDate.'"))';
					}
		else
		{
			if (!is_null($dStartDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dStartDate.'" AND EndDate<="'.$dStartDate.'")
						OR ProgramDate="'.$dStartDate.'") ';
			}
			if (!is_null($dEndDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dEndDate.'" AND EndDate<="'.$dEndDate.'")
						OR ProgramDate="'.$dEndDate.'") ';
			}
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND a.IsHidden="'.B_FALSE.'" ';
		}
		//$sSQL .= ' GROUP BY a.PlaceID ';// da ne se slaga, shtoto ne izlizat multiple chasovete na filmite
		//order
		if (!isset($aOrder) || !is_array($aOrder) || empty($aOrder[SORT_FIELD]))
		{
			$sSQL .=' ORDER BY Title ASC, PlaceHallID ASC, ProgramDate ASC, ProgramTime ASC, StartDate ASC';
		}
		else
		{
			$nSortField = $aOrder[SORT_FIELD];
			$nSortDir = $aOrder[SORT_DIR];
			if(in_array($nSortField, array_keys($this->_aFields)))
			{
				$sSQL .=' ORDER BY '.$this->_aFields[$nSortField].$aSortDirections[$nSortDir];
			}
		}
		//echo '<!--'.$sSQL.'-->';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $result;
	}

	// used in event details
	function ListEventCitiesAsArray($nEventID, $dStartDate=null, $dEndDate=null, $bShowHidden = false)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nEventID) || !is_numeric($nEventID)) return false;

		$sSQL = 'SELECT DISTINCT a.CityID FROM '.$this->_sTable.' as a
					LEFT OUTER JOIN '.$this->_sRelEventTable.' as r4 ON a.ProgramID=r4.ProgramID
					LEFT OUTER JOIN '.$this->_sRelDatePeriodTable.' as r2 ON a.'.$this->_sPrimaryKey.'=r2.ProgramID AND r2.IsHidden="'.B_FALSE.'"
					LEFT OUTER JOIN '.$this->_sRelDateTimeTable.' as r3 ON a.'.$this->_sPrimaryKey.'=r3.ProgramID AND r3.IsHidden="'.B_FALSE.'"
					WHERE 1=1 ';
		if (!is_null($nEventID) && !empty($nEventID))
		{
			$sSQL .=' AND (a.EventID="'.$nEventID.'" OR r4.EventID="'.$nEventID.'") ';
		}
		if (!is_null($dStartDate) && !is_null($dEndDate))
		{
//			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
//					OR (StartDate>="'.$dStartDate.'" AND StartDate<="'.$dEndDate.'")
//					OR (EndDate>="'.$dStartDate.'" AND EndDate<="'.$dEndDate.'")
//					OR (StartDate<="'.$dStartDate.'" AND EndDate>="'.$dEndDate.'"))';
			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
					OR (StartDate<="'.$dEndDate.'" AND EndDate>="'.$dStartDate.'"))';
					}
		else
		{
			if (!is_null($dStartDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dStartDate.'" AND EndDate<="'.$dStartDate.'")
						OR ProgramDate="'.$dStartDate.'") ';
			}
			if (!is_null($dEndDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dEndDate.'" AND EndDate<="'.$dEndDate.'")
						OR ProgramDate="'.$dEndDate.'") ';
			}
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND a.IsHidden="'.B_FALSE.'" ';
		}
		//$sSQL .= ' GROUP BY a.CityID ';
		//$sSQL .=' ORDER BY CityID ASC';
		//echo '<!--'.$sSQL.'-->';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[] = $row->CityID;
		}
		return $aToReturn;
	}

	function ListPlacesByFestivalID($nFestivalID=null, $nCityID=null, $dStartDate=null, $dEndDate=null, $bShowHidden = false, $aOrder=null)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT DISTINCT r1.PlaceID, r1.Title, r1.ShortTitle FROM '.$this->_sTable.' as a
					LEFT OUTER JOIN '.$this->_sPlaceTable.' as r1 ON a.PlaceID=r1.PlaceID
						AND r1.LanguageID='.$this->_nLangID.'
					LEFT OUTER JOIN '.$this->_sRelDatePeriodTable.' as r2 ON a.'.$this->_sPrimaryKey.'=r2.ProgramID AND r2.IsHidden="'.B_FALSE.'"
					LEFT OUTER JOIN '.$this->_sRelDateTimeTable.' as r3 ON a.'.$this->_sPrimaryKey.'=r3.ProgramID AND r3.IsHidden="'.B_FALSE.'"
					WHERE 1=1 ';
		if (!is_null($nCityID) && !empty($nCityID))
		{
			$sSQL .=' AND a.CityID="'.$nCityID.'" ';
		}
		if (!is_null($nFestivalID) && !empty($nFestivalID))
		{
			$sSQL .=' AND FestivalID="'.$nFestivalID.'" ';
		}
		if (!is_null($dStartDate) && !is_null($dEndDate))
		{
//			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
//					OR (StartDate>="'.$dStartDate.'" AND StartDate<="'.$dEndDate.'")
//					OR (EndDate>="'.$dStartDate.'" AND EndDate<="'.$dEndDate.'")
//					OR (StartDate<="'.$dStartDate.'" AND EndDate>="'.$dEndDate.'"))';
			$sSQL .= ' AND ((ProgramDate>="'.$dStartDate.'" AND ProgramDate<="'.$dEndDate.'")
					OR (StartDate<="'.$dEndDate.'" AND EndDate>="'.$dStartDate.'"))';
					}
		else
		{
			if (!is_null($dStartDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dStartDate.'" AND EndDate<="'.$dStartDate.'")
						OR ProgramDate="'.$dStartDate.'") ';
			}
			if (!is_null($dEndDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dEndDate.'" AND EndDate<="'.$dEndDate.'")
						OR ProgramDate="'.$dEndDate.'") ';
			}
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND a.IsHidden="'.B_FALSE.'" ';
		}
		//order
		if (!isset($aOrder) || !is_array($aOrder) || empty($aOrder[SORT_FIELD]))
		{
			$sSQL .=' ORDER BY Title ASC';
		}
		else
		{
			$nSortField = $aOrder[SORT_FIELD];
			$nSortDir = $aOrder[SORT_DIR];
			if(in_array($nSortField, array_keys($this->_aFields)))
			{
				$sSQL .=' ORDER BY '.$this->_aFields[$nSortField].$aSortDirections[$nSortDir];
			}
		}
		//echo $sSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $result;
	}

	function GetByID($nID, $nLangID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;

		$sSQL = 'SELECT * FROM '.$this->_sTable.'
				WHERE '.$this->_sPrimaryKey.'="'.$nID.'" LIMIT 1';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		$row = mysql_fetch_object($result);

		return $row;
	}

	function Insert($nProgramTypeID, $nFestivalID, $nCityID, $nPlaceID, $nPlaceHallID, $nEventID, $nPremiereTypeID, $bIsHidden=0)
	{
		global $oSession;

		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nProgramTypeID) || empty($nCityID) || empty($nPlaceID) || empty($nEventID)) return false;

		$sSQL = 'INSERT INTO '.$this->_sTable.' SET
							ProgramTypeID="'.$nProgramTypeID.'",
							FestivalID="'.$nFestivalID.'",
							CityID="'.$nCityID.'",
							PlaceID="'.$nPlaceID.'",
							PlaceHallID="'.$nPlaceHallID.'",
							EventID="'.$nEventID.'",
							PremiereTypeID="'.$nPremiereTypeID.'",
							IsHidden="'.$bIsHidden.'",
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
							LastUpdate=NOW() ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		$nID = mysql_insert_id($this->_dbCon);

		$sSQL = 'INSERT INTO '.$this->_sTable.'Status SET
							ProgramID="'.$nID.'",
							ChangeType="added",
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
							LastUpdate=NOW() ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $nID;
	}

	function Update($nID, $nProgramTypeID, $nFestivalID, $nCityID, $nPlaceID, $nPlaceHallID, $nEventID, $nPremiereTypeID, $bIsHidden=0, $nLangID=0)
	{
		global $oSession;

		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nProgramTypeID) || empty($nCityID) || empty($nPlaceID) || empty($nEventID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;

		$sSQL = 'UPDATE '.$this->_sTable.' SET
							ProgramTypeID="'.$nProgramTypeID.'",
							FestivalID="'.$nFestivalID.'",
							CityID="'.$nCityID.'",
							PlaceID="'.$nPlaceID.'",
							PlaceHallID="'.$nPlaceHallID.'",
							EventID="'.$nEventID.'",
							PremiereTypeID="'.$nPremiereTypeID.'",
							IsHidden="'.$bIsHidden.'",
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
							LastUpdate=NOW()
						WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		$sSQL = 'INSERT INTO '.$this->_sTable.'Status SET
							ProgramID="'.$nID.'",
							ChangeType="updated",
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
							LastUpdate=NOW() ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $nID;
	}

	function Delete($nID)
	{
		global $oSession, $oProgramDatePeriod, $oProgramDateTime, $oProgramNote;

		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;

		$sSQL = 'DELETE FROM '.$this->_sTable.'
							WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		// TODO: delete relations if any
		$this->DeleteProgramPage($nID);
		$this->DeleteProgramPlace($nID);
		$this->DeleteProgramEvent($nID);
		$oProgramDatePeriod->DeleteByProgramID($nID);
		$oProgramDateTime->DeleteByProgramID($nID);
		$oProgramNote->DeleteByProgramID($nID);

		$sSQL = 'INSERT INTO '.$this->_sTable.'Status SET
							ProgramID="'.$nID.'",
							ChangeType="deleted",
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
							LastUpdate=NOW() ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return true;
	}

	function InsertProgramPage($nProgramID, $xPageID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nProgramID) && (!is_numeric($xPageID) || !is_array($xPageID))) return false;

		if (is_array($xPageID) && count($xPageID) > 0)
		{
			// prepare list of values: ex: '(1,2),(1,3),(1,4);' where 1 is primaryID, the rest are IDs array
			$sRestSQL = '('.$nProgramID.','.join('),('.$nProgramID.',',$xPageID).')';
		}
		elseif (is_numeric($xPageID) && !empty($xPageID))
		{
			$sRestSQL = '('.$nProgramID.','.$xPageID.')';
		}
		else
			return false;

		$sSQL = 'REPLACE INTO '.$this->_sRelPageTable.' (ProgramID, PageID) VALUES '.$sRestSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return true;
	}

	function DeleteProgramPage($nProgramID=0, $nPageID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nProgramID) || !is_numeric($nPageID)) return false;
		if (empty($nProgramID) && empty($nPageID)) return false;

		$sFilter = '';
		if (!empty($nProgramID))
		{
			$sFilter .= ' AND ProgramID="'.$nProgramID.'" ';
		}
		if (!empty($nPageID))
		{
			$sFilter .= ' AND PageID="'.$nPageID.'" ';
		}

		$sSQL = 'DELETE FROM '.$this->_sRelPageTable.'
							WHERE 1 '.$sFilter.' ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return true;
	}

	function ListProgramPagesAsArray($nProgramID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nProgramID) || empty($nProgramID)) return false;

		$sSQL = 'SELECT * FROM '.$this->_sRelPageTable.' WHERE
								ProgramID="'.$nProgramID.'"
								ORDER BY PageID ASC';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[] = $row->PageID;
		}
		return $aToReturn;
	}

	function InsertProgramPlace($nProgramID, $xPlaceID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nProgramID) && (!is_numeric($xPlaceID) || !is_array($xPlaceID))) return false;

		if (is_array($xPlaceID) && count($xPlaceID) > 0)
		{
			// prepare list of values: ex: '(1,2),(1,3),(1,4);' where 1 is primaryID, the rest are IDs array
			$sRestSQL = '('.$nProgramID.','.join('),('.$nProgramID.',',$xPlaceID).')';
		}
		elseif (is_numeric($xPlaceID) && !empty($xPlaceID))
		{
			$sRestSQL = '('.$nProgramID.','.$xPlaceID.')';
		}
		else
			return false;

		$sSQL = 'REPLACE INTO '.$this->_sRelPlaceTable.' (ProgramID, PlaceID) VALUES '.$sRestSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return true;
	}

	function DeleteProgramPlace($nProgramID=0, $nPlaceID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nProgramID) || !is_numeric($nPlaceID)) return false;
		if (empty($nProgramID) && empty($nPlaceID)) return false;

		$sFilter = '';
		if (!empty($nProgramID))
		{
			$sFilter .= ' AND ProgramID="'.$nProgramID.'" ';
		}
		if (!empty($nPlaceID))
		{
			$sFilter .= ' AND PlaceID="'.$nPlaceID.'" ';
		}

		$sSQL = 'DELETE FROM '.$this->_sRelPlaceTable.'
							WHERE 1 '.$sFilter.' ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return true;
	}

	function ListProgramPlacesAsArray($nProgramID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nProgramID) || empty($nProgramID)) return false;

		$sSQL = 'SELECT * FROM '.$this->_sRelPlaceTable.' WHERE
								ProgramID="'.$nProgramID.'"
								ORDER BY PlaceID ASC';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[] = $row->PlaceID;
		}
		return $aToReturn;
	}

	function InsertProgramEvent($nProgramID, $xEventID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nProgramID) && (!is_numeric($xEventID) || !is_array($xEventID))) return false;

		if (is_array($xEventID) && count($xEventID) > 0)
		{
			// prepare list of values: ex: '(1,2),(1,3),(1,4);' where 1 is primaryID, the rest are IDs array
			$sRestSQL = '('.$nProgramID.','.join('),('.$nProgramID.',',$xEventID).')';
		}
		elseif (is_numeric($xEventID) && !empty($xEventID))
		{
			$sRestSQL = '('.$nProgramID.','.$xEventID.')';
		}
		else
			return false;

		$sSQL = 'REPLACE INTO '.$this->_sRelEventTable.' (ProgramID, EventID) VALUES '.$sRestSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return true;
	}

	function DeleteProgramEvent($nProgramID=0, $nEventID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nProgramID) || !is_numeric($nEventID)) return false;
		if (empty($nProgramID) && empty($nEventID)) return false;

		$sFilter = '';
		if (!empty($nProgramID))
		{
			$sFilter .= ' AND ProgramID="'.$nProgramID.'" ';
		}
		if (!empty($nEventID))
		{
			$sFilter .= ' AND EventID="'.$nEventID.'" ';
		}

		$sSQL = 'DELETE FROM '.$this->_sRelEventTable.'
							WHERE 1 '.$sFilter.' ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return true;
	}

	function ListProgramEventsAsArray($nProgramID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nProgramID) || empty($nProgramID)) return false;

		$sSQL = 'SELECT * FROM '.$this->_sRelEventTable.' WHERE
								ProgramID="'.$nProgramID.'"
								ORDER BY EventID ASC';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[] = $row->EventID;
		}
		return $aToReturn;
	}
}
?>