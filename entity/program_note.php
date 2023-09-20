<?php
if (!$bInSite) die();
//=========================================================
class ProgramNote
{
	var $_sTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;
	
	function ProgramNote($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblProgramNote';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;
		
		$this->_aFields = array();
		
		$this->_aFields[1] = 'ProgramNoteID';
		$this->_aFields[2] = 'ProgramID';
		$this->_aFields[3] = 'Title';
		$this->_aFields[4] = 'IsHidden';
		$this->_aFields[5] = 'LastUpdate';
		$this->_aFields[6] = 'LastUpdateUserID';
		$this->_aFields[7] = 'LanguageID';
		
		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[2];
	}
	
	function ListAll($nProgramID=null, $bShowHidden = false, $aOrder=null)
	{
		global $aSortDirections;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.' WHERE LanguageID='.$this->_nLangID.' ';
		if (!is_null($nProgramID) && !empty($nProgramID))
		{
			$sSQL .=' AND ProgramID="'.$nProgramID.'" ';
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
	
	function ListAllAsArray($nProgramID=null, $bShowHidden = false)
	{
		$result = $this->ListAll($nProgramID, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->ProgramNoteID] = $row->Title;
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
	
	function GetByProgramID($nProgramID, $nLangID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nProgramID) || !is_numeric($nProgramID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.'
					WHERE LanguageID='.$nLangID.' AND ProgramID="'.$nProgramID.'" LIMIT 1';
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
	
	function Insert($nProgramID, $sTitle, $bIsHidden=0)
	{
		global $aLanguages, $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$nID = $this->GetMaxID() + 1;
		foreach($aLanguages as $key=>$value)
		{
			$sSQL = 'INSERT INTO '.$this->_sTable.' SET 
								'.$this->_sPrimaryKey.'="'.$nID.'", 
								ProgramID="'.$nProgramID.'", 
								Title="'.$sTitle.'", 
								IsHidden="'.$bIsHidden.'", 
								LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'", 
								LastUpdate=NOW(), 
								LanguageID='.$key.' ';
			$result = mysql_query($sSQL, $this->_dbCon);
			dbAssert($result, $sSQL);
		}
		return $nID;
	}
	
	function Update($nID, $nProgramID, $sTitle, $bIsHidden=0, $nLangID=0)
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
	
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
							Title="'.$sTitle.'" 
						WHERE LanguageID='.$nLangID.' AND '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		// update lang independent values, if any
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
							ProgramID="'.$nProgramID.'", 
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
	
	function DeleteByProgramID($nID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		$sSQL = 'DELETE FROM '.$this->_sTable.' 
							WHERE ProgramID="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		// TODO: delete relations if any
		
		return true;
	}
}
?>