<?php
if (!$bInSite) die();
//=========================================================
class News
{
	var $_sTable = null;
	var $_sRelPageTable = null;
	var $_sRelCityTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;
	
	function News($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblNews';
		$this->_sRelPageTable = DB_TABLE_PREFIX.'relNews2Page';
		$this->_sRelCityTable = DB_TABLE_PREFIX.'relNews2City';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;
		
		$this->_aFields = array();
		$this->_aFields[1] = 'NewsID';
		$this->_aFields[2] = 'NewsDate';
		$this->_aFields[3] = 'Title';
		$this->_aFields[4] = 'MetaKeywords';
		$this->_aFields[5] = 'MetaDescription';
		$this->_aFields[6] = 'Lead';
		$this->_aFields[7] = 'Content';
		$this->_aFields[8] = 'Source';
		$this->_aFields[9] = 'SourceUrl';
		$this->_aFields[10] = 'NrViews';
		$this->_aFields[11] = 'LastUpdate';
		$this->_aFields[12] = 'LastUpdateUserID';
		$this->_aFields[13] = 'IsHidden';
		$this->_aFields[14] = 'LanguageID';
		$this->_aFields[15] = 'Author';
		$this->_aFields[16] = 'AuthorUserID';
		
		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[2];
	}
	
	function ListAll($xParentID=null, $nCityID=null, $sKeyword='', $dStartDate=null, $dEndDate=null, $bShowHidden = false, $nMaxRows=0, $aOrder=null)
	{
		global $aSortDirections;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT DISTINCT a.* FROM '.$this->_sTable.' as a 
					LEFT OUTER JOIN '.$this->_sRelPageTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.NewsID
					LEFT OUTER JOIN '.$this->_sRelCityTable.' as r2 ON a.'.$this->_sPrimaryKey.'=r2.NewsID 
					WHERE a.LanguageID='.$this->_nLangID.' ';
		if (!is_null($xParentID))
		{
			if (is_array($xParentID) && count($xParentID) > 0)
				$sSQL .= ' AND PageID IN ('.join(',', $xParentID).')';
			elseif (is_numeric($xParentID) && !empty($xParentID))
				$sSQL .= ' AND PageID="'.$xParentID.'" ';
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
					Source LIKE "%'.$sKeyword.'%" OR 
					SourceUrl LIKE "%'.$sKeyword.'%" OR 
					Author LIKE "%'.$sKeyword.'%") ';
		}
		if (!is_null($dStartDate))
		{
			$sSQL .=' AND NewsDate>="'.$dStartDate.'" ';
		}
		if (!is_null($dEndDate))
		{
			$sSQL .=' AND NewsDate<="'.$dEndDate.'" ';
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND IsHidden="'.B_FALSE.'" ';
			
			//$today_ts = mktime (0,0,0,date("m"), date("d"), date("Y"));
			//$dToday = date(DEFAULT_DATE_DB_FORMAT, $today_ts);
			//$sSQL .= ' AND NewsDate<="'.$dToday.'" ';
		}
		//order
		if (!isset($aOrder) || !is_array($aOrder) || empty($aOrder[SORT_FIELD]))
		{
			$sSQL .=' ORDER BY '.$this->_sDefSortField.' DESC, '.$this->_sPrimaryKey.' DESC';
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
		$result = $this->ListAll($nParentID, $nCityID, $dStartDate, $dEndDate, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->NewsID] = $row->Title;
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
	
	function Insert($sName, $dNewsDate, $sKeywords, $sDescription, $sLead, $sText, $sSource, $sSourceUrl, $sAuthor, $nAuthorID, $bIsHidden=0)
	{
		global $aLanguages, $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$nID = $this->GetMaxID() + 1;
		foreach($aLanguages as $key=>$value)
		{
			$sSQL = 'INSERT INTO '.$this->_sTable.' SET 
								'.$this->_sPrimaryKey.'="'.$nID.'", 
								Title="'.$sName.'",
								NewsDate="'.$dNewsDate.'", 
								MetaKeywords="'.$sKeywords.'", 
								MetaDescription="'.$sDescription.'", 
								Lead="'.$sLead.'",
								Content="'.$sText.'", 
								Source="'.$sSource.'",
								SourceUrl="'.$sSourceUrl.'",
								Author="'.$sAuthor.'",
								AuthorUserID="'.$nAuthorID.'", 
								IsHidden="'.$bIsHidden.'", 
								LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'", 
								LastUpdate=NOW(), 
								LanguageID='.$key.' ';
			$result = mysql_query($sSQL, $this->_dbCon);
			dbAssert($result, $sSQL);
		}
		return $nID;
	}
	
	function Update($nID, $sName, $dNewsDate, $sKeywords, $sDescription, $sLead, $sText, $sSource, $sSourceUrl, $sAuthor, $nAuthorID, $bIsHidden=0, $nLangID=0)
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
								Content="'.$sText.'", 
								Source="'.$sSource.'",
								SourceUrl="'.$sSourceUrl.'",
								Author="'.$sAuthor.'" 
						WHERE LanguageID='.$nLangID.' AND '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		// update lang independent values, if any
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
							NewsDate="'.$dNewsDate.'",
							AuthorUserID="'.$nAuthorID.'", 
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
		$this->DeleteNewsPage($nID);
		$this->DeleteNewsCity($nID);
		
		return true;
	}
	
	function InsertNewsPage($nNewsID, $xPageID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nNewsID) && (!is_numeric($xPageID) || !is_array($xPageID))) return false;
		
		if (is_array($xPageID) && count($xPageID) > 0)
		{
			// prepare list of values: ex: '(1,2),(1,3),(1,4);' where 1 is primaryID, the rest are IDs array
			$sRestSQL = '('.$nNewsID.','.join('),('.$nNewsID.',',$xPageID).')';
		}
		elseif (is_numeric($xPageID) && !empty($xPageID))
		{
			$sRestSQL = '('.$nNewsID.','.$xPageID.')';
		}
		else
			return false;
		
		$sSQL = 'REPLACE INTO '.$this->_sRelPageTable.' (NewsID, PageID) VALUES '.$sRestSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return true;
	}
	
	function DeleteNewsPage($nNewsID=0, $nPageID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nNewsID) || !is_numeric($nPageID)) return false;
		if (empty($nNewsID) && empty($nPageID)) return false;
			
		$sFilter = '';
		if (!empty($nNewsID)) 
		{
			$sFilter .= ' AND NewsID="'.$nNewsID.'" ';
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
	
	function ListNewsPagesAsArray($nNewsID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nNewsID) || empty($nNewsID)) return false;
		
		$sSQL = 'SELECT * FROM '.$this->_sRelPageTable.' WHERE 
								NewsID="'.$nNewsID.'" 
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
	
	function InsertNewsCity($nNewsID, $xCityID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nNewsID) && (!is_numeric($xCityID) || !is_array($xCityID))) return false;
		
		if (is_array($xCityID) && count($xCityID) > 0)
		{
			// prepare list of values: ex: '(1,2),(1,3),(1,4);' where 1 is primaryID, the rest are IDs array
			$sRestSQL = '('.$nNewsID.','.join('),('.$nNewsID.',',$xCityID).')';
		}
		elseif (is_numeric($xCityID) && !empty($xCityID))
		{
			$sRestSQL = '('.$nNewsID.','.$xCityID.')';
		}
		else
			return false;
		
		$sSQL = 'REPLACE INTO '.$this->_sRelCityTable.' (NewsID, CityID) VALUES '.$sRestSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return true;
	}
	
	function DeleteNewsCity($nNewsID=0, $nCityID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nNewsID) || !is_numeric($nCityID)) return false;
		if (empty($nNewsID) && empty($nCityID)) return false;
			
		$sFilter = '';
		if (!empty($nNewsID)) 
		{
			$sFilter .= ' AND NewsID="'.$nNewsID.'" ';
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
	
	function ListNewsCitiesAsArray($nNewsID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nNewsID) || empty($nNewsID)) return false;
		
		$sSQL = 'SELECT * FROM '.$this->_sRelCityTable.' WHERE 
								NewsID="'.$nNewsID.'" 
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