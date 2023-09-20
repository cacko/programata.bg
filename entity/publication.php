<?php
if (!$bInSite) die();
//=========================================================
class Publication
{
	var $_sTable = null;
	var $_sRelPageTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;
	
	function Publication($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblPublication';
		$this->_sRelPageTable = DB_TABLE_PREFIX.'relPublication2Page';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;
		
		$this->_aFields = array();
		$this->_aFields[1] = 'PublicationID';
		$this->_aFields[2] = 'PublicationDate';
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
		$this->_aFields[17] = 'Subtitle';
		
		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[2];
	}
	
	function ListAll($nParentID=null, $sKeyword='', $dStartDate=null, $dEndDate=null, $bShowHidden = false, $nMaxRows=0, $aOrder=null)
	{
		global $aSortDirections;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT DISTINCT a.* FROM '.$this->_sTable.' as a 
					LEFT OUTER JOIN '.$this->_sRelPageTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.PublicationID 
					WHERE a.LanguageID='.$this->_nLangID.' ';
		if (!is_null($nParentID) && !empty($nParentID))
		{
			$sSQL .=' AND PageID="'.$nParentID.'" ';
		}
		if (!empty($sKeyword)) 
		{
			$sSQL .= ' AND (Title LIKE "%'.$sKeyword.'%" OR 
					Subtitle LIKE "%'.$sKeyword.'%" OR 
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
			$sSQL .=' AND PublicationDate>="'.$dStartDate.'" ';
		}
		if (!is_null($dEndDate))
		{
			$sSQL .=' AND PublicationDate<="'.$dEndDate.'" ';
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND IsHidden="'.B_FALSE.'" ';
			
			$today_ts = mktime (0,0,0,date("m"), date("d"), date("Y"));
			$dToday = date(DEFAULT_DATE_DB_FORMAT, $today_ts);
			$sSQL .= ' AND PublicationDate<="'.$dToday.'" ';
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
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return $result;
	}
	
	function ListAllAsArray($nParentID=null, $dStartDate=null, $dEndDate=null, $bShowHidden = false)
	{
		$result = $this->ListAll($nParentID, $dStartDate, $dEndDate, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->PublicationID] = $row->Title.' ('.$row->Subtitle.')';
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
	
	function Insert($sName, $sSubtitle, $dPublicationDate, $sKeywords, $sDescription, $sLead, $sText, $sSource, $sSourceUrl, $sAuthor, $nAuthorID, $bIsHidden=0)
	{
		global $aLanguages, $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$nID = $this->GetMaxID() + 1;
		foreach($aLanguages as $key=>$value)
		{
			$sSQL = 'INSERT INTO '.$this->_sTable.' SET 
								'.$this->_sPrimaryKey.'="'.$nID.'", 
								Title="'.$sName.'",
								Subtitle="'.$sSubtitle.'",
								PublicationDate="'.$dPublicationDate.'", 
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
	
	function Update($nID, $sName, $sSubtitle, $dPublicationDate, $sKeywords, $sDescription, $sLead, $sText, $sSource, $sSourceUrl, $sAuthor, $nAuthorID, $bIsHidden=0, $nLangID=0)
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
	
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
								Title="'.$sName.'",
								Subtitle="'.$sSubtitle.'",
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
							PublicationDate="'.$dPublicationDate.'",
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
		$this->DeletePublicationPage($nID);
		
		return true;
	}
	
	function InsertPublicationPage($nPublicationID, $xPageID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nPublicationID) && (!is_numeric($xPageID) || !is_array($xPageID))) return false;
		
		if (is_array($xPageID) && count($xPageID) > 0)
		{
			// prepare list of values: ex: '(1,2),(1,3),(1,4);' where 1 is primaryID, the rest are IDs array
			$sRestSQL = '('.$nPublicationID.','.join('),('.$nPublicationID.',',$xPageID).')';
		}
		elseif (is_numeric($xPageID) && !empty($xPageID))
		{
			$sRestSQL = '('.$nPublicationID.','.$xPageID.')';
		}
		else
			return false;
		
		$sSQL = 'REPLACE INTO '.$this->_sRelPageTable.' (PublicationID, PageID) VALUES '.$sRestSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return true;
	}
	
	function DeletePublicationPage($nPublicationID=0, $nPageID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nPublicationID) || !is_numeric($nPageID)) return false;
		if (empty($nPublicationID) && empty($nPageID)) return false;
			
		$sFilter = '';
		if (!empty($nPublicationID)) 
		{
			$sFilter .= ' AND PublicationID="'.$nPublicationID.'" ';
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
	
	function ListPublicationPagesAsArray($nPublicationID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nPublicationID) || empty($nPublicationID)) return false;
		
		$sSQL = 'SELECT * FROM '.$this->_sRelPageTable.' WHERE 
								PublicationID="'.$nPublicationID.'" 
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
}
?>