<?php
if (!$bInSite) die();
//=========================================================
class Urban
{
	var $_sTable = null;
	var $_sEventTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;
	
	function Urban($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblUrban';
		$this->_sRelPageTable = DB_TABLE_PREFIX.'relUrban2Page';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;
		
		$this->_aFields = array();
		$this->_aFields[1] = 'UrbanID';
		$this->_aFields[2] = 'MainTitle';
		$this->_aFields[3] = 'PublicationDate';
		$this->_aFields[4] = 'IsHidden';
		$this->_aFields[5] = 'Title1';
		$this->_aFields[6] = 'Autor1';
		$this->_aFields[7] = 'Text1';
		$this->_aFields[8] = 'Title2';
		$this->_aFields[9] = 'Autor2';
		$this->_aFields[10] = 'Text2';
		$this->_aFields[11] = 'Title3';
		$this->_aFields[12] = 'Autor3';
		$this->_aFields[13] = 'Text3';
		$this->_aFields[14] = 'MetaKeywords';
		$this->_aFields[15] = 'LastUpdate';
		$this->_aFields[16] = 'LastUpdateUserID';
		$this->_aFields[17] = 'LanguageID';
		
		$this->_sPrimaryKey = $this->_aFields[1];
	}
	
	function ListAll($nParentID=null, $sKeyword='', $bShowHidden = false)
	{
		global $aSortDirections;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.' as a
				LEFT OUTER JOIN '.$this->_sRelPageTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.UrbanID
				WHERE LanguageID='.$this->_nLangID.' ';
		if (!is_null($nParentID) && !empty($nParentID))
		{
			$sSQL .=' AND PageID="'.$nParentID.'" ';
		}
		if (!empty($sKeyword)) 
		{
			$sSQL .= ' AND (
			(Title1 LIKE "%'.$sKeyword.'%") OR
			(Title2 LIKE "%'.$sKeyword.'%") OR
			(Title3 LIKE "%'.$sKeyword.'%") 
			)';
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND IsHidden="'.B_FALSE.'" ';
		}
		$sSQL .= 'ORDER BY a.UrbanID DESC';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return $result;
	}
	
	// used for admin
	
	function ListAllAsArray($sKeyword='', $bShowHidden = false)
	{
		$result = $this->ListAll($sKeyword, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->UrbanID] = $row->Title1;
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
	
	function Insert($sMainTitle, $sPublicationDate, $bIsHidden=0,
				$sTitle1, $sAuthor1, $sText1,
				$sTitle2, $sAuthor2, $sText2,
				$sTitle3, $sAuthor3, $sText3,
				$sKeywords)
	{
		global $aLanguages, $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
	
		$nID = $this->GetMaxID() + 1;
		foreach($aLanguages as $key=>$value)
		{
			$sSQL = 'INSERT INTO '.$this->_sTable.' SET 
									'.$this->_sPrimaryKey.'="'.$nID.'", 
									MainTitle="'.$sImainTitle.'",
									PublicationDate="'.$sPublicationDate.'",
									IsHidden="'.$bIsHidden.'", 
									Title1="'.$sTitle1.'",
									Author1="'.$sAuthor1.'",
									Text1="'.$sText1.'",
									Title2="'.$sTitle2.'",
									Author2="'.$sAuthor2.'",
									Text2="'.$sText2.'",
									Title3="'.$sTitle3.'",
									Author3="'.$sAuthor3.'",
									Text3="'.$sText3.'",
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
	
	function Update($nID, $sMainTitle, $sPublicationDate, $bIsHidden=0,
				$sTitle1, $sText1, $sAuthor1,
				$sTitle2, $sText2, $sAuthor2,
				$sTitle3, $sText3, $sAuthor3,
				$sKeywords, $nLangID=0)
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
									MainTitle="'.$sMainTitle.'",
									PublicationDate="'.$sPublicationDate.'",
									IsHidden="'.$bIsHidden.'", 
									Title1="'.$sTitle1.'",
									Author1="'.$sAuthor1.'",
									Text1="'.$sText1.'",
									Title2="'.$sTitle2.'",
									Author2="'.$sAuthor2.'",
									Text2="'.$sText2.'",
									Title3="'.$sTitle3.'",
									Author3="'.$sAuthor3.'",
									Text3="'.$sText3.'",
									MetaKeywords="'.$sKeywords.'",
									LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'", 
									LastUpdate=NOW()
							WHERE LanguageID='.$nLangID.' AND '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		return $nID;
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

	function InsertUrbanPage($nUrbanID, $xPageID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nUrbanID) && (!is_numeric($xPageID) || !is_array($xPageID))) return false;
		
		if (is_array($xPageID) && count($xPageID) > 0)
		{
			$sRestSQL = '('.$nUrbanID.','.join('),('.$nUrbanID.',',$xPageID).')';
		}
		elseif (is_numeric($xPageID) && !empty($xPageID))
		{
			$sRestSQL = '('.$nUrbanID.','.$xPageID.')';
		}
		else
			return false;
		
		$sSQL = 'REPLACE INTO '.$this->_sRelPageTable.' (UrbanID, PageID) VALUES '.$sRestSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return true;
	}
	
	function DeleteUrbanPage($nUrbanID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nUrbanID)) return false;
		$sFilter = '';
		if (!empty($nUrbanID)) 
		{
			$sFilter .= ' AND UrbanID="'.$nUrbanID.'" ';
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
		
		return true;
	}

	function ListUrbanPagesAsArray($nUrbanID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nUrbanID) || empty($nUrbanID)) return false;
		
		$sSQL = 'SELECT * FROM '.$this->_sRelPageTable.' WHERE 
								UrbanID="'.$nUrbanID.'" 
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