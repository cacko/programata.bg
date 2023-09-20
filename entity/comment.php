<?php
if (!$bInSite) die();
//=========================================================
class Comment
{
	var $_sTable = null;
	var $_sRelUserTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;
	
	function Comment($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblComment';
		$this->_sRelUserTable = DB_TABLE_PREFIX.'tblUser';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;
		
		$this->_aFields = array();
		$this->_aFields[1] = 'CommentID';
		$this->_aFields[2] = 'EntityID';
		$this->_aFields[9] = 'EntityTypeID';
		$this->_aFields[3] = 'AuthorUserID';
		$this->_aFields[4] = 'Title';
		$this->_aFields[5] = 'CommentDate';
		$this->_aFields[6] = 'Content';
		$this->_aFields[7] = 'LastUpdate';
		$this->_aFields[8] = 'IsHidden';
		
		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[5];
	}
	
	function ListAll($nEntityID=null, $nEntityTypeID=null, $nAuthorUserID=null, $sKeyword='', $bShowHidden = false, $nMaxRows=0, $aOrder=null)
	{
		global $aSortDirections, $page;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT DISTINCT a.*, r1.UserID, r1.FirstName, r1.LastName, r1.Username, r1.Url FROM '.$this->_sTable.' as a 
							LEFT OUTER JOIN '.$this->_sRelUserTable.' as r1 ON a.AuthorUserID=r1.UserID 
							WHERE 1=1 ';
		if (!is_null($nEntityID) && !empty($nEntityID))
		{
			$sSQL .=' AND EntityID="'.$nEntityID.'" ';
		}
		if (!is_null($nEntityTypeID) && !empty($nEntityTypeID))
		{
			$sSQL .=' AND EntityTypeID="'.$nEntityTypeID.'" ';
		}
		if (!is_null($nAuthorUserID) && !empty($nAuthorUserID))
		{
			$sSQL .=' AND AuthorUserID="'.$nAuthorUserID.'" ';
		}
		if (!empty($sKeyword)) 
		{
			$sSQL .= ' AND (Title LIKE "%'.$sKeyword.'%" OR Content LIKE "%'.$sKeyword.'%") ';
		}
		if (!$bShowHidden)
		{
			$sSQL .= 'AND IsHidden="'.B_FALSE.'" ';
		}
		//order
		if (!isset($aOrder) || !is_array($aOrder) || empty($aOrder[SORT_FIELD]))
		{
			$sSQL .=' ORDER BY '.$this->_sDefSortField.' DESC, CommentID ASC';
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
		//echo '<!-- '.$sSQL.' -->';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return $result;
	}
	
	function ListAllAsArray($nEntityID=null, $nEntityTypeID=null, $nAuthorUserID=null, $bShowHidden = false)
	{
		$result = $this->ListAll($nEntityID, $nEntityTypeID, $nAuthorUserID, '', $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->CommentID] = $row->Title;
		}
		
		return $aToReturn;
	}
	
	function GetByID($nID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.' 
							WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		if (!$bShowHidden)
		{
			$sSQL .= 'AND IsHidden="'.B_FALSE.'" ';
		}
		$sSQL .= ' LIMIT 1';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		$row = mysql_fetch_object($result);
	
		return $row;
	}
	
	function GetCountByEntity($nEntityID, $nEntityTypeID, $bShowHidden = false)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nEntityID) || !is_numeric($nEntityID)) return false;
		if (empty($nEntityTypeID) || !is_numeric($nEntityTypeID)) return false;
		
		$sSQL = 'SELECT COUNT('.$this->_sPrimaryKey.') FROM '.$this->_sTable.' 
							WHERE EntityID="'.$nEntityID.'" AND EntityTypeID="'.$nEntityTypeID.'" ';
		if (!$bShowHidden)
		{
			$sSQL .= 'AND IsHidden="'.B_FALSE.'" ';
		}
		$sSQL .= 'LIMIT 1';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		$row = mysql_fetch_array($result);
		return $row[0];
	}
	
	function GetCountByAuthorUser($nID, $bShowHidden = false)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		$sSQL = 'SELECT COUNT('.$this->_sPrimaryKey.') FROM '.$this->_sTable.' 
							WHERE AuthorUserID="'.$nID.'" ';
		if (!$bShowHidden)
		{
			$sSQL .= 'AND IsHidden="'.B_FALSE.'" ';
		}
		$sSQL .= 'LIMIT 1';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		$row = mysql_fetch_array($result);
		return $row[0];
	}
	
	function Insert($sName, $sText, $nEntityID, $nEntityTypeID, $bIsHidden=0)
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nEntityID) || !is_numeric($nEntityID)) return false;
		if (empty($nEntityTypeID) || !is_numeric($nEntityTypeID)) return false;

		$sSQL = 'INSERT INTO '.$this->_sTable.' SET 
							Title="'.$sName.'", 
							Content="'.$sText.'", 
							EntityID="'.$nEntityID.'",
							EntityTypeID="'.$nEntityTypeID.'", 
							CommentDate=NOW(), 
							AuthorUserID="'.$oSession->GetValue(SS_USER_ID).'", 
							IsHidden="'.$bIsHidden.'", 
							LastUpdate=NOW(), 
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
							LanguageID="'.$this->_nLangID.'"';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return mysql_insert_id($this->_dbCon);
	}
	
	function Update($nID, $sName, $sText, $bIsHidden=0)
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
	
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
							Title="'.$sName.'", 
							Content="'.$sText.'", 
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
	
	function DeleteByEntity($nEntityID, $nEntityTypeID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nEntityID) || !is_numeric($nEntityID)) return false;
		if (empty($nEntityTypeID) || !is_numeric($nEntityTypeID)) return false;
		
		$sSQL = 'DELETE FROM '.$this->_sTable.' 
							WHERE EntityID="'.$nEntityID.'" AND EntityTypeID="'.$nEntityTypeID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		// TODO: delete relations if any  
		
		return true;
	}
}
?>