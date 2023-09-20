<?php
if (!$bInSite) die();
//=========================================================
class Phone
{
	var $_sTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;
	
	function Phone($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblPhone';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;
		
		$this->_aFields = array();
		
		$this->_aFields[1] = 'PhoneID';
		$this->_aFields[2] = 'PhoneTypeID';
		$this->_aFields[3] = 'Area';
		$this->_aFields[4] = 'Phone';
		$this->_aFields[5] = 'Ext';
		$this->_aFields[6] = 'EntityID';
		$this->_aFields[7] = 'EntityTypeID';
		$this->_aFields[8] = 'IsHidden';
		$this->_aFields[9] = 'LastUpdate';
		$this->_aFields[10] = 'LastUpdateUserID';
		$this->_aFields[11] = 'LanguageID';
		
		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[2];
	}
	
	function ListAll($nEntityID=null, $nEntityTypeID=null, $xPhoneTypeID=null, $bShowHidden = false, $aOrder=null)
	{
		global $aSortDirections;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.' WHERE 1=1 ';
		if (!is_null($nEntityID) && !empty($nEntityID))
		{
			$sSQL .=' AND EntityID="'.$nEntityID.'" ';
		}
		if (!is_null($nEntityTypeID) && !empty($nEntityTypeID))
		{
			$sSQL .=' AND EntityTypeID="'.$nEntityTypeID.'" ';
		}
		if (!is_null($xPhoneTypeID))
		{
			if (is_array($xPhoneTypeID) && count($xPhoneTypeID) > 0)
				$sSQL .= ' AND PhoneTypeID IN ('.join(',', $xPhoneTypeID).')';
			elseif (is_numeric($xPhoneTypeID) && !empty($xPhoneTypeID))
				$sSQL .= ' AND PhoneTypeID="'.$xPhoneTypeID.'" ';
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
	
	function ListAllAsArray($nEntityID=null, $nEntityTypeID=null, $nPhoneTypeID=null, $bShowHidden = false)
	{
		$result = $this->ListAll($nEntityID, $nEntityTypeID, $nPhoneTypeID, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->PhoneID] = $row->Area.' '.$row->Number.' '.$row->Extension;
		}
		
		return $aToReturn;
	}
	
	function GetByID($nID, $nLangID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.' WHERE '.$this->_sPrimaryKey.'="'.$nID.'" LIMIT 1';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		$row = mysql_fetch_object($result);
	
		return $row;
	}
	
	function Insert($nPhoneTypeID, $sArea, $sPhone, $sExtension, $nEntityID, $nEntityTypeID, $bIsHidden=0)
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'INSERT INTO '.$this->_sTable.' SET 
							PhoneTypeID="'.$nPhoneTypeID.'", 
							Area="'.$sArea.'", 
							Phone="'.$sPhone.'", 
							Ext="'.$sExtension.'", 
							EntityID="'.$nEntityID.'", 
							EntityTypeID="'.$nEntityTypeID.'", 
							IsHidden="'.$bIsHidden.'", 
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'", 
							LastUpdate=NOW() ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return mysql_insert_id($this->_dbCon);
	}
	
	function Update($nID, $nPhoneTypeID, $sArea, $sPhone, $sExtension, $nEntityID, $nEntityTypeID, $bIsHidden=0, $nLangID=0)
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
	
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
							PhoneTypeID="'.$nPhoneTypeID.'", 
							Area="'.$sArea.'", 
							Phone="'.$sPhone.'", 
							Ext="'.$sExtension.'", 
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