<?php
if (!$bInSite) die();
//=========================================================
class Rate
{
	var $_sTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;
	
	function Rate($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblRate';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;
		
		$this->_aFields = array();
		
		$this->_aFields[1] = 'RateID';
		$this->_aFields[2] = 'UserID';
		$this->_aFields[6] = 'EntityID';
		$this->_aFields[7] = 'EntityTypeID';
		$this->_aFields[7] = 'RateTypeID';
		$this->_aFields[7] = 'RateValue';
		$this->_aFields[7] = 'IPAddress';
		$this->_aFields[8] = 'IsHidden';
		$this->_aFields[9] = 'LastUpdate';
		
		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[9];
	}
	
	function ListAll($nEntityID=null, $nEntityTypeID=null, $nUserID=null, $xRateTypeID=null, $bShowHidden = false, $aOrder=null)
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
		if (!is_null($nUserID) && !empty($nUserID))
		{
			$sSQL .=' AND UserID="'.$nUserID.'" ';
		}
		if (!is_null($xRateTypeID))
		{
			if (is_array($xRateTypeID) && count($xRateTypeID) > 0)
				$sSQL .= ' AND RateTypeID IN ('.join(',', $xRateTypeID).')';
			elseif (is_numeric($xRateTypeID) && !empty($xRateTypeID))
				$sSQL .= ' AND RateTypeID="'.$xRateTypeID.'" ';
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND IsHidden="'.B_FALSE.'" ';
		}
		//order
		if (!isset($aOrder) || !is_array($aOrder) || empty($aOrder[SORT_FIELD]))
		{
			$sSQL .=' ORDER BY '.$this->_sDefSortField.' DESC';
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
	
	function GetByID($nID, $nLangID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.' WHERE '.$this->_sPrimaryKey.'="'.$nID.'" LIMIT 1';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		$row = mysql_fetch_object($result);
	
		return $row;
	}
	
	function GetRating($nEntityID, $nEntityTypeID, $nRateTypeID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nEntityID) || !is_numeric($nEntityID)) return false;
		if (empty($nEntityTypeID) || !is_numeric($nEntityTypeID)) return false;
		
		$fToReturn = 0;
		
		$sSQL = 'SELECT AVG(`RateValue`) as AvgRating FROM '.$this->_sTable.'
							WHERE EntityID="'.$nEntityID.'" 
								AND EntityTypeID="'.$nEntityTypeID.'" 
								AND RateTypeID="'.$nRateTypeID.'"
								AND IsHidden="'.B_FALSE.'" 
							LIMIT 1';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		if (mysql_num_rows($result))
		{
			$row = mysql_fetch_object($result);
			$fToReturn = $row->AvgRating;
			if (empty($fToReturn)) $fToReturn = 0;
		}
		return $fToReturn;
	}
	
	function IsRated($nEntityID, $nEntityTypeID, $nRateTypeID=0)
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nEntityID) || !is_numeric($nEntityID)) return false;
		if (empty($nEntityTypeID) || !is_numeric($nEntityTypeID)) return false;
		
		$sSQL = 'SELECT DISTINCT EntityID, EntityTypeID, RateTypeID FROM '.$this->_sTable.' 
							WHERE EntityID="'.$nEntityID.'" 
								AND EntityTypeID="'.$nEntityTypeID.'" 
								AND RateTypeID="'.$nRateTypeID.'" 
								AND IsHidden="'.B_FALSE.'" 
								AND UserID="'.$oSession->GetValue(SS_USER_ID).'" 
							LIMIT 1';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		if(mysql_num_rows($result))
			return true;
		else
			return false;
	}
	
	function Insert($nEntityID, $nEntityTypeID, $nRateTypeID, $nRateValue, $bIsHidden=0)
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nRateValue) || !is_numeric($nRateValue)) return false;
		
		$ip = getenv("REMOTE_ADDR"); 
		$rhost = gethostbyaddr($_SERVER['REMOTE_ADDR']);
		$xIP = $ip.' ('.$rhost.')';
		
		$sSQL = 'INSERT INTO '.$this->_sTable.' SET 
							EntityID="'.$nEntityID.'", 
							EntityTypeID="'.$nEntityTypeID.'",
							RateTypeID="'.$nRateTypeID.'", 
							RateValue="'.$nRateValue.'",
							IPAddress="'.$xIP.'", 
							IsHidden="'.$bIsHidden.'", 
							UserID="'.$oSession->GetValue(SS_USER_ID).'", 
							LastUpdate=NOW() ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return mysql_insert_id($this->_dbCon);
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