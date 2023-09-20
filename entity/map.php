<?php
if (!$bInSite) die();
//=========================================================
class Map
{
	var $_sTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;
	
	function Map($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblMapLocation';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;
		
		$this->_aFields = array();
		
		$this->_aFields[1] = 'MapLocationID';
		$this->_aFields[2] = 'Url';
		$this->_aFields[3] = 'EntityID';
		$this->_aFields[4] = 'EntityTypeID';
		$this->_aFields[5] = 'EntitySubtypeID';
		
		$this->_aFields[6] = 'MapX';
		$this->_aFields[7] = 'MapY';
		$this->_aFields[8] = 'MinX';
		$this->_aFields[9] = 'MinY';
		$this->_aFields[10] = 'MaxX';
		$this->_aFields[11] = 'MaxY';
		
		$this->_aFields[12] = 'CityID';
		$this->_aFields[13] = 'Ekatte';
		$this->_aFields[14] = 'TitleBg';
		$this->_aFields[15] = 'TitleEn';
		$this->_aFields[16] = 'LastUpdate';
		$this->_aFields[17] = 'LastUpdateUserID';
		$this->_aFields[18] = 'IsHidden';
		
		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[14];
	}
	
	function ListAll($nEntityID=null, $nEntityTypeID=null, $xEntitySubtypeID=null, $bShowHidden = false, $aOrder=null)
	{
		global $aSortDirections;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.' WHERE LanguageID='.$this->_nLangID.' ';
		if (!is_null($nEntityID) && !empty($nEntityID))
		{
			$sSQL .=' AND EntityID="'.$nEntityID.'" ';
		}
		if (!is_null($nEntityTypeID) && !empty($nEntityTypeID))
		{
			$sSQL .=' AND EntityTypeID="'.$nEntityTypeID.'" ';
		}
		if (!is_null($xEntitySubtypeID))
		{
			if (is_array($xEntitySubtypeID) && count($xEntitySubtypeID) > 0)
				$sSQL .= ' AND EntitySubtypeID IN ('.join(',', $xEntitySubtypeID).')';
			elseif (is_numeric($xMapTypeID) && !empty($xEntitySubtypeID))
				$sSQL .= ' AND EntitySubtypeID="'.$xEntitySubtypeID.'" ';
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND IsHidden="'.B_FALSE.'" ';
		}
		//order
		if (!isset($aOrder) || !is_array($aOrder) || empty($aOrder[SORT_FIELD]))
		{
			$sSQL .=' ORDER BY '.$this->_sDefSortField.' ASC';
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
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return $result;
	}
	
	function ListAllAsArray($nEntityID=null, $nEntityTypeID=null, $xEntitySubtypeID=null, $bShowHidden = false)
	{
		$result = $this->ListAll($nEntityID, $nEntityTypeID, $xEntitySubtypeID, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->MapLocationID] = $row->Title.' ('.Url.')';
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
	
	function GetByEntityID($nEntityID, $nEntityTypeID=null, $bShowHidden = false, $nLangID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nEntityID) || !is_numeric($nEntityID)) return null;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.'
					WHERE EntityID="'.$nEntityID.'" ';
		if (!is_null($nEntityTypeID) && !empty($nEntityTypeID))
		{
			$sSQL .=' AND EntityTypeID="'.$nEntityTypeID.'" ';
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND IsHidden="'.B_FALSE.'" ';
		}
		$sSQL .= 'LIMIT 1';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		if (mysql_num_rows($result) > 0)
		{
			$row = mysql_fetch_object($result);
			return $row;
		}
		else
			return null;
	}
	
	function GetMaxID()
	{
		$strSQL = 'SELECT MAX('.$this->_sPrimaryKey.') FROM '.$this->_sTable.'  LIMIT 1';
		$result = mysql_query($strSQL, $this->_dbCon);
		dbAssert($result, $strSQL);
		
		$row = mysql_fetch_array($result);
		return $row[0];
	}
	
	function Insert($nMapTypeID, $sTitle, $sUrl, $nEntityID, $nEntityTypeID, $bIsHidden=0)
	{
		global $aLanguages, $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$nID = $this->GetMaxID() + 1;
		foreach($aLanguages as $key=>$value)
		{
			$sSQL = 'INSERT INTO '.$this->_sTable.' SET 
								'.$this->_sPrimaryKey.'="'.$nID.'", 
								MapTypeID="'.$nMapTypeID.'", 
								Title="'.$sTitle.'", 
								Url="'.$sUrl.'", 
								EntityID="'.$nEntityID.'", 
								EntityTypeID="'.$nEntityTypeID.'", 
								IsHidden="'.$bIsHidden.'", 
								LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'", 
								LastUpdate=NOW(), 
								LanguageID='.$key.' ';
			$result = mysql_query($sSQL, $this->_dbCon);
			dbAssert($result, $sSQL);
		}
		return $nID;
	}
	
	function Update($nID, $nMapTypeID, $sTitle, $sUrl, $nEntityID, $nEntityTypeID, $bIsHidden=0, $nLangID=0)
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
	
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
							Title="'.$sTitle.'", 
							Url="'.$sUrl.'" 
						WHERE LanguageID='.$nLangID.' AND '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		// update lang independent values, if any
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
							MapTypeID="'.$nMapTypeID.'", 
							EntityID="'.$nEntityID.'", 
							EntityTypeID="'.$nEntityTypeID.'", 
							IsHidden="'.$bIsHidden.'", 
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'", 
							LastUpdate=NOW() 
						WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
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
}
?>