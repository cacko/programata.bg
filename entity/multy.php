<?php
if (!$bInSite) die();
//=========================================================
class Multy
{
	var $_sTable = null;
	var $_sPartsTable = null;
	var $_sEventTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;
	
	function Multy($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblMulty';
		$this->_sPartsTable = DB_TABLE_PREFIX.'tblMultyParts';
		$this->_sRelPageTable = DB_TABLE_PREFIX.'relMulty2Page';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;
		
		$this->_aFields = array();
		$this->_aFields[1] = 'MultyID';
		$this->_aFields[2] = 'Title';
		$this->_aFields[3] = 'PublicationDate';
		$this->_aFields[4] = 'IsHidden';
		$this->_aFields[5] = 'Autor';
		$this->_aFields[6] = 'MetaKeywords';
		$this->_aFields[7] = 'MetaDescription';
		$this->_aFields[8] = 'LastUpdate';
		$this->_aFields[9] = 'LastUpdateUserID';
		$this->_aFields[10] = 'LanguageID';
		
		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[3];
	}
	
	function ListAll($nParentID=null, $sKeyword='', $dStartDate=null, $dEndDate=null, $bShowHidden = false, $nMaxRows=0, $aOrder=null)
	{
		global $aSortDirections;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT DISTINCT a.* FROM '.$this->_sTable.' as a 
					LEFT OUTER JOIN '.$this->_sRelPageTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.MultyID 
					WHERE a.LanguageID='.$this->_nLangID.' ';
		if (!is_null($nParentID) && !empty($nParentID))
		{
			$sSQL .=' AND PageID="'.$nParentID.'" ';
		}
		if (!empty($sKeyword)) 
		{
			$sSQL .= ' AND (
			(Title LIKE "%'.$sKeyword.'%")
			)';
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

	// used for admin
	
	function ListAllAsArray($nParentID=null, $dStartDate=null, $dEndDate=null, $bShowHidden = false)
	{
		$result = $this->ListAll($nParentID, $dStartDate, $dEndDate, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->MultyID] = $row->Title;
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
	
	function Insert($sTitle, $sPublicationDate, $bIsHidden=0, $sAuthor, $sKeywords)
	{
		global $aLanguages, $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
	
		$nID = $this->GetMaxID() + 1;
		foreach($aLanguages as $key=>$value)
		{
			$sSQL = 'INSERT INTO '.$this->_sTable.' SET 
									'.$this->_sPrimaryKey.'="'.$nID.'", 
									PublicationDate="'.$sPublicationDate.'",
									IsHidden="'.$bIsHidden.'", 
									Title="'.$sTitle.'",
									Author="'.$sAuthor.'",
									MetaKeywords="'.$sKeywords.'",
									AuthorUserID="'.$oSession->GetValue(SS_USER_ID).'", 
									LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'", 
									LastUpdate=NOW(), 
									LanguageID='.$key.' ';
			$result = mysql_query($sSQL, $this->_dbCon);
			dbAssert($result, $sSQL);
		}
		return $nID;
	}
	
	function Update($nID, $sTitle, $sPublicationDate, $bIsHidden=0, $sAuthor, $sKeywords, $nLangID=0)
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
									Title="'.$sTitle.'",
									PublicationDate="'.$sPublicationDate.'",
									IsHidden="'.$bIsHidden.'", 
									Author="'.$sAuthor.'",
									MetaKeywords="'.$sKeywords.'",
									LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'", 
									LastUpdate=NOW()
							WHERE LanguageID='.$nLangID.' AND '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		return $nID;
	}
	
	function InsertParts($nParetnID, $nPartId, $sText, $nLangID=0)
	{
		global $oSession;
		
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nParetnID) || !is_numeric($nParetnID)) return false;
		if (empty($nPartId) || !is_numeric($nPartId)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
		$sSQL = 'INSERT INTO '.$this->_sPartsTable.' SET 
								'.$this->_sPrimaryKey.'="'.$nParetnID.'", 
								SubID="'.$nPartId.'",
								Text="'.$sText.'",
								LanguageID='.$nLangID.'
								ON DUPLICATE KEY UPDATE Text="'.$sText.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
	}

	function UpdateHidden($nID, $bIsHidden=0)
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
	
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
							IsHidden="'.$bIsHidden.'" 
						WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return $nID;
	}

	function InsertMultyPage($nMultyID, $xPageID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nMultyID) && (!is_numeric($xPageID) || !is_array($xPageID))) return false;
		
		if (is_array($xPageID) && count($xPageID) > 0)
		{
			$sRestSQL = '('.$nMultyID.','.join('),('.$nMultyID.',',$xPageID).')';
		}
		elseif (is_numeric($xPageID) && !empty($xPageID))
		{
			$sRestSQL = '('.$nMultyID.','.$xPageID.')';
		}
		else
			return false;
		
		$sSQL = 'REPLACE INTO '.$this->_sRelPageTable.' (MultyID, PageID) VALUES '.$sRestSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return true;
	}
	
	function DeleteMultyPage($nMultyID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nMultyID)) return false;
		$sFilter = '';
		if (!empty($nMultyID)) 
		{
			$sFilter .= ' AND MultyID="'.$nMultyID.'" ';
		}
	
		$sSQL = 'DELETE FROM '.$this->_sRelPageTable.' 
							WHERE 1 '.$sFilter.' ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return true;
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
		
		$sSQL = 'DELETE FROM '.$this->_sPartsTable.' 
							WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return true;
	}

	function ListMultyPagesAsArray($nMultyID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nMultyID) || empty($nMultyID)) return false;
		
		$sSQL = 'SELECT * FROM '.$this->_sRelPageTable.' WHERE 
								MultyID="'.$nMultyID.'" 
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

	function GetPartText($nParentID, $nPartID, $nLangID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nParentID) || empty($nParentID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;

		
		$sSQL = 'SELECT Text FROM '.$this->_sPartsTable.' 
				WHERE 
				MultyID="'.$nParentID.'" AND LanguageID="'.$nLangID.'" AND SubID="'.$nPartID.'" 
				ORDER BY SubID ASC';
		
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		$row = mysql_fetch_array($result);
		return $row[0];
	}
	
	function GetPartsNum($nParentID)
	{
		$strSQL = 'SELECT MAX(SubID) FROM '.$this->_sPartsTable.' WHERE MultyID='.$nParentID.' LIMIT 1';
		$result = mysql_query($strSQL, $this->_dbCon);
		dbAssert($result, $strSQL);
		
		$row = mysql_fetch_array($result);
		return $row[0];
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

}
	
?>