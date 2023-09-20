<?php
if (!$bInSite) die();
//=========================================================
class Festival
{
	var $_sTable = null;
	var $_sRelCityTable = null;
	var $_sRelPageTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;
	
	function Festival($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblFestival';
		$this->_sRelPageTable = DB_TABLE_PREFIX.'relFestival2Page';
		$this->_sRelCityTable = DB_TABLE_PREFIX.'relFestival2City';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;
		
		$this->_aFields = array();
		$this->_aFields[1] = 'FestivalID';
		$this->_aFields[2] = 'Title';
		$this->_aFields[3] = 'StartDate';
		$this->_aFields[4] = 'EndDate';
		$this->_aFields[5] = 'MetaKeywords';
		$this->_aFields[6] = 'MetaDescription';
		$this->_aFields[7] = 'Lead';
		$this->_aFields[8] = 'Content';
		$this->_aFields[9] = 'Url';
		$this->_aFields[10] = 'NrViews';
		$this->_aFields[11] = 'LastUpdate';
		$this->_aFields[12] = 'LastUpdateUserID';
		$this->_aFields[13] = 'IsHidden';
		$this->_aFields[14] = 'LanguageID';
		
		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[3];
	}
	
	function ListAll($nParentID=null, $nCityID=null, $sKeyword='', $dStartDate=null, $dEndDate=null, $bShowHidden = false, $nMaxRows=0, $aOrder=null)
	{
		global $aSortDirections;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT DISTINCT a.* FROM '.$this->_sTable.' as a 
							LEFT OUTER JOIN '.$this->_sRelPageTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.FestivalID
							LEFT OUTER JOIN '.$this->_sRelCityTable.' as r2 ON a.'.$this->_sPrimaryKey.'=r2.FestivalID 
							WHERE a.LanguageID='.$this->_nLangID.' ';
		if (!is_null($nParentID) && !empty($nParentID))
		{
			$sSQL .=' AND PageID="'.$nParentID.'" ';
		}
		if (!is_null($nCityID) && !empty($nCityID))
		{
			$sSQL .=' AND CityID="'.$nCityID.'" ';
		}
		if (!empty($sKeyword)) 
		{
			$sSQL .= ' AND (Title LIKE "%'.$sKeyword.'%" OR 
					MetaKeywords LIKE "%'.$sKeyword.'%" OR 
					MetaDescription LIKE "%'.$sKeyword.'%" OR
					Lead LIKE "%'.$sKeyword.'%" OR
					Content LIKE "%'.$sKeyword.'%" OR 
					Url LIKE "%'.$sKeyword.'%") ';
		}
		if (!is_null($dStartDate) && !is_null($dEndDate))
		{
			$sSQL .= ' AND ((StartDate>="'.$dStartDate.'" AND StartDate<="'.$dEndDate.'") 
					OR (EndDate>="'.$dStartDate.'" AND EndDate<="'.$dEndDate.'") 
					OR (StartDate<="'.$dStartDate.'" AND EndDate>="'.$dEndDate.'"))';
		}
		else
		{
			//$dToday = date(DEFAULT_DATE_DB_FORMAT);
			if (!is_null($dStartDate))
			{
				$sSQL .=' (AND StartDate>="'.$dStartDate.'" OR EndDate>="'.$dStartDate.'") ';
			}
			if (!is_null($dEndDate))
			{
				$sSQL .=' AND (StartDate<="'.$dEndDate.'" OR EndDate<="'.$dEndDate.'") ';
			}
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND IsHidden="'.B_FALSE.'" ';
		}
		//order
		if (!isset($aOrder) || !is_array($aOrder) || empty($aOrder[SORT_FIELD]))
		{
			$sSQL .=' ORDER BY '.$this->_sPrimaryKey.' DESC'; //'.$this->_sDefSortField.' DESC, 
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
	
	function ListAllAsArray($nParentID=null, $nCityID=null, $dStartDate=null, $dEndDate=null, $bShowHidden = false)
	{
		$result = $this->ListAll($nParentID, $nCityID, '', $dStartDate, $dEndDate, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->FestivalID] = $row->Title;
		}
		
		return $aToReturn;
	}
	
	function GetByID($nID, $nLangID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.' 
				WHERE LanguageID='.$nLangID.' AND '.$this->_sPrimaryKey.'="'.$nID.'" LIMIT 1';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		$row = mysql_fetch_object($result);
	
		return $row;
	}
	
	function GetMaxID()
	{
		$strSQL = 'SELECT MAX('.$this->_sPrimaryKey.') FROM '.$this->_sTable.'  LIMIT 1';
		$result = mysql_query($strSQL, $this->_dbCon);
		dbAssert($result, $strSQL);
		
		$row = mysql_fetch_array($result);
		return $row[0];
	}
	
	function Insert($sName, $dStartDate, $dEndDate, $sKeywords, $sDescription, $sLead, $sText, $sUrl, $bIsHidden=0)
	{
		global $aLanguages, $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$nID = $this->GetMaxID() + 1;
		foreach($aLanguages as $key=>$value)
		{
			$sSQL = 'INSERT INTO '.$this->_sTable.' SET 
								'.$this->_sPrimaryKey.'="'.$nID.'", 
								Title="'.$sName.'",
								StartDate="'.$dStartDate.'",
								EndDate="'.$dEndDate.'", 
								MetaKeywords="'.$sKeywords.'", 
								MetaDescription="'.$sDescription.'", 
								Lead="'.$sLead.'",
								Content="'.$sText.'", 
								Url="'.$sUrl.'", 
								IsHidden="'.$bIsHidden.'", 
								LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'", 
								LastUpdate=NOW(), 
								LanguageID='.$key.' ';
			$result = mysql_query($sSQL, $this->_dbCon);
			dbAssert($result, $sSQL);
		}
		return $nID;
	}
	
	function Update($nID, $sName, $dStartDate, $dEndDate, $sKeywords, $sDescription, $sLead, $sText, $sUrl, $bIsHidden=0, $nLangID=0)
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
	
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
							Title="'.$sName.'",
							MetaKeywords="'.$sKeywords.'", 
							MetaDescription="'.$sDescription.'", 
							Lead="'.$sLead.'",
							Content="'.$sText.'" 
						WHERE LanguageID='.$nLangID.' AND '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		// update lang independent values, if any
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
							StartDate="'.$dStartDate.'", 
							EndDate="'.$dEndDate.'", 
							Url="'.$sUrl.'", 
							IsHidden="'.$bIsHidden.'", 
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'", 
							LastUpdate=NOW() 
						WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return $nID;
	}
	
	function TrackView($nID)
	{
		return $nID; //stop tracking views - by Rumyana
		/*
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
	
		$sSQL = 'UPDATE '.$this->_sTable.' SET  NrViews = NrViews + 1 
						WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return $nID;
		*/
	}
	
	function Delete($nID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		$sSQL = 'DELETE FROM '.$this->_sTable.' 
							WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		// TODO: delete relations if any
		$this->DeleteFestivalPage($nID);
		$this->DeleteFestivalCity($nID);
		
		return true;
	}
	
	function InsertFestivalPage($nFestivalID, $xPageID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nFestivalID) && (!is_numeric($xPageID) || !is_array($xPageID))) return false;
		
		if (is_array($xPageID) && count($xPageID) > 0)
		{
			// prepare list of values: ex: '(1,2),(1,3),(1,4);' where 1 is primaryID, the rest are IDs array
			$sRestSQL = '('.$nFestivalID.','.join('),('.$nFestivalID.',',$xPageID).')';
		}
		elseif (is_numeric($xPageID) && !empty($xPageID))
		{
			$sRestSQL = '('.$nFestivalID.','.$xPageID.')';
		}
		else
			return false;
		
		$sSQL = 'REPLACE INTO '.$this->_sRelPageTable.' (FestivalID, PageID) VALUES '.$sRestSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return true;
	}
	
	function DeleteFestivalPage($nFestivalID=0, $nPageID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nFestivalID) || !is_numeric($nPageID)) return false;
		if (empty($nFestivalID) && empty($nPageID)) return false;
			
		$sFilter = '';
		if (!empty($nFestivalID)) 
		{
			$sFilter .= ' AND FestivalID="'.$nFestivalID.'" ';
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
	
	function ListFestivalPagesAsArray($nFestivalID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nFestivalID) || empty($nFestivalID)) return false;
		
		$sSQL = 'SELECT * FROM '.$this->_sRelPageTable.' WHERE 
								FestivalID="'.$nFestivalID.'" 
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
	
	function InsertFestivalCity($nFestivalID, $xCityID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nFestivalID) && (!is_numeric($xCityID) || !is_array($xCityID))) return false;
		
		if (is_array($xCityID) && count($xCityID) > 0)
		{
			// prepare list of values: ex: '(1,2),(1,3),(1,4);' where 1 is primaryID, the rest are IDs array
			$sRestSQL = '('.$nFestivalID.','.join('),('.$nFestivalID.',',$xCityID).')';
		}
		elseif (is_numeric($xCityID) && !empty($xCityID))
		{
			$sRestSQL = '('.$nFestivalID.','.$xCityID.')';
		}
		else
			return false;
		
		$sSQL = 'REPLACE INTO '.$this->_sRelCityTable.' (FestivalID, CityID) VALUES '.$sRestSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return true;
	}
	
	function DeleteFestivalCity($nFestivalID=0, $nCityID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nFestivalID) || !is_numeric($nCityID)) return false;
		if (empty($nFestivalID) && empty($nCityID)) return false;
			
		$sFilter = '';
		if (!empty($nFestivalID)) 
		{
			$sFilter .= ' AND FestivalID="'.$nFestivalID.'" ';
		}
		if (!empty($nCityID)) 
		{
			$sFilter .= ' AND CityID="'.$nCityID.'" ';
		}
		
		$sSQL = 'DELETE FROM '.$this->_sRelCityTable.' 
							WHERE 1 '.$sFilter.' ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return true;
	}
	
	function ListFestivalCitiesAsArray($nFestivalID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nFestivalID) || empty($nFestivalID)) return false;
		
		$sSQL = 'SELECT * FROM '.$this->_sRelCityTable.' WHERE 
								FestivalID="'.$nFestivalID.'" 
								ORDER BY CityID ASC';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[] = $row->CityID;
		}
		return $aToReturn;
	}
}
?>