<?php
if (!$bInSite) die();
//=========================================================
class Label
{
	var $_sTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;
	
	function Label($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblLabel';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;
		
		$this->_aFields = array();
		$this->_aFields[1] = 'LabelID';
		$this->_aFields[2] = 'ParentLabelID';
		$this->_aFields[3] = 'Title';
		$this->_aFields[4] = 'IsHidden';
		$this->_aFields[5] = 'SortOrder';
		$this->_aFields[6] = 'LastUpdate';
		$this->_aFields[7] = 'LastUpdateUserID';
		$this->_aFields[8] = 'LanguageID';
		
		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[5];
	}
	
	function ListAll($nParentID=null, $sKeyword='', $bShowHidden = false, $aOrder=null)
	{
		global $aSortDirections;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.' WHERE LanguageID='.$this->_nLangID.' ';
		if (!is_null($nParentID))
		{
			$sSQL .=' AND ParentLabelID="'.$nParentID.'" ';
		}
		if (!empty($sKeyword)) 
		{
			$sSQL .= ' AND (Title LIKE "%'.$sKeyword.'%") ';
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
	
	// used for admin
	function ListAllParentsAsArray($bShowHidden = false)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		return $this->ListAllAsArray(0, '', $bShowHidden);
	}
	
	function ListAllAsArray($nParentID=null, $sKeyword='', $bShowHidden = false)
	{
		$result = $this->ListAll($nParentID, $sKeyword, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->LabelID] = $row->Title;
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
	
	function GetMaxOrder()
	{
		$strSQL = 'SELECT MAX('.$this->_sDefSortField.') FROM '.$this->_sTable.'  LIMIT 1';
		$result = mysql_query($strSQL, $this->_dbCon);
		dbAssert($result, $strSQL);
		
		$row = mysql_fetch_array($result);
		return $row[0];
	}
	
	function Insert($sName, $nParentID, $bIsHidden=0)
	{
		global $aLanguages, $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		//if (empty($nParentID) || !is_numeric($nParentID)) return false;
		
		$nID = $this->GetMaxID() + 1;
		$nOrder = $this->GetMaxOrder() + 1;
		foreach($aLanguages as $key=>$value)
		{
			$sSQL = 'INSERT INTO '.$this->_sTable.' SET 
									'.$this->_sPrimaryKey.'="'.$nID.'", 
									ParentLabelID="'.$nParentID.'", 
									Title="'.$sName.'", 
									'.$this->_sDefSortField.'="'.$nOrder.'", 
									IsHidden="'.$bIsHidden.'", 
									LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'", 
									LastUpdate=NOW(), 
									LanguageID='.$key.' ';
			$result = mysql_query($sSQL, $this->_dbCon);
			dbAssert($result, $sSQL);
		}
		return $nID;
	}
	
	function Update($nID, $sName, $bIsHidden=0, $nLangID=0)
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
	
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
								Title="'.$sName.'" 
							WHERE LanguageID='.$nLangID.' AND '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		// update lang independent values, if any
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
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
	
	function Move($nID, $bMoveUp=true)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		$nDelta = -1;
		if ($bMoveUp)
		{
			$nDelta = +1;
		}
		
		$item = $this->GetByID($nID);
		
		if ($bMoveUp)
		{
			if ($item->SortOrder == 1)
			{
				// cannot move item up
				return;
			}
		}
		else
		{
			$nMaxOrder = $this->GetMaxOrder();
			if ($item->SortOrder == $nMaxOrder)
			{
				// cannot move item down
				return;
			}
		}
		
		$strSQL = 	'SELECT '.$this->_sPrimaryKey.' FROM '.$this->_sTable.' WHERE '.$this->_sDefSortField.'="'.($item->SortOrder-$nDelta).'" LIMIT 1 ';
		$result = mysql_query($strSQL, $this->_dbCon);
		dbAssert($result, $strSQL);
		$itemToMove = mysql_fetch_object($result);
		
		$strSQL = 	'UPDATE '.$this->_sTable.' SET '.$this->_sDefSortField.'=('.$this->_sDefSortField.'+'.$nDelta.') WHERE '.$this->_sPrimaryKey.'="'.$itemToMove->LabelID.'" ';
		$result = mysql_query($strSQL, $this->_dbCon);
		dbAssert($result, $strSQL);
		
		$strSQL = 	'UPDATE '.$this->_sTable.' SET '.$this->_sDefSortField.'=('.$this->_sDefSortField.'-'.$nDelta.') WHERE '.$this->_sPrimaryKey.'="'.$item->LabelID.'" ';
		$result = mysql_query($strSQL, $this->_dbCon);
		dbAssert($result, $strSQL);
	}
	
	function MoveUp($nID)
	{
		$this->Move($nID, true);
	}
	
	function MoveDown($nID)
	{
		$this->Move($nID, false);
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
	
	function DeleteByParentID($nID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		$sSQL = 'DELETE FROM '.$this->_sTable.' 
							WHERE ParentLabelID="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		// TODO: delete relations if any  
		
		return true;
	}
}
?>