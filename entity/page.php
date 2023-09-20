<?php
if (!$bInSite) die();
//=========================================================
class Page
{
	var $_sTable = null;
	var $_sRelCityFilterTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;
	
	function Page($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblPage';
		$this->_sRelCityFilterTable = DB_TABLE_PREFIX.'relPage2CityFilter';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;
		
		$this->_aFields = array();
		$this->_aFields[1] = 'PageID';
		$this->_aFields[2] = 'ParentPageID';
		$this->_aFields[3] = 'Title';
		$this->_aFields[4] = 'MetaKeywords';
		$this->_aFields[5] = 'MetaDescription';
		$this->_aFields[6] = 'Description';
		$this->_aFields[7] = 'TemplateFile';
		$this->_aFields[8] = 'IsRequired';
		$this->_aFields[9] = 'IsHidden';
		$this->_aFields[10] = 'RequiredUserStatus';
		$this->_aFields[11] = 'SortOrder';
		$this->_aFields[12] = 'LastUpdate';
		$this->_aFields[13] = 'LastUpdateUserID';
		$this->_aFields[14] = 'LanguageID';
		
		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[11];
	}
	
	function ListAll($nParentID=null, $sKeyword='', $sTemplate='', $bShowHidden = false, $aOrder=null)
	{
		global $aSortDirections;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.' WHERE LanguageID='.$this->_nLangID.' ';
		if (!is_null($nParentID) && !empty($nParentID))
		{
			$sSQL .=' AND ParentPageID="'.$nParentID.'" ';
		}
		if (!empty($sKeyword)) 
		{
			$sSQL .= ' AND (Title LIKE "%'.$sKeyword.'%" OR MetaKeywords LIKE "%'.$sKeyword.'%" OR MetaDescription LIKE "%'.$sKeyword.'%" OR Description LIKE "%'.$sKeyword.'%") ';
		}
		if (!empty($sTemplate)) 
		{
			$sSQL .=' AND TemplateFile LIKE "%'.$sTemplate.'%" ';
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
		//echo $sSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return $result;
	}
	
	function ListAllCount($nParentID=null, $bShowHidden = false)
	{
		global $aSortDirections;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT DISTINCT a.*, count(r1.PageID) as NrPublications FROM '.$this->_sTable.' as a 
									LEFT OUTER JOIN '.$this->_sRelPublicationTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.PageID 
									LEFT OUTER JOIN '.$this->_sPublicationTable.' as r2 ON r1.PublicationID=r2.PublicationID 
									WHERE LanguageID='.$this->_nLangID.' ';
		if (!is_null($nParentID))
		{
			$sSQL .=' AND a.ParentPageID="'.$nParentID.'" ';
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND a.IsHidden="'.B_FALSE.'" AND r2.IsHidden="'.B_FALSE.'" ';
		}
		$sSQL .=' GROUP BY a.Title, a.PageID ';
		//order
		if (!isset($aOrder) || !is_array($aOrder) || empty($aOrder[SORT_FIELD]))
		{
			$sSQL .=' ORDER BY Title ASC';
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
	
	// used for navigation
	function ListByIDs($aPageIDs, $bShowHidden = false)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_array($aPageIDs) || count($aPageIDs) == 0) return false;
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.' 
							WHERE LanguageID='.$this->_nLangID.' AND '.$this->_sPrimaryKey.' IN ('.join(',', $aPageIDs).') ';
		if (!$bShowHidden)
		{
			$sSQL .= ' AND IsHidden="'.B_FALSE.'" ';
		}
		$sSQL .=' ORDER BY '.$this->_sDefSortField.' ASC';
		
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return $result;
	}
	
	// used for admin
	function ListAllParentsAsArray($bShowHidden = false)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT DISTINCT ParentPageID FROM '.$this->_sTable.' ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		$aPageIDs = array();
		while($row = mysql_fetch_object($result))
		{
			$aPageIDs[] = $row->ParentPageID;
		}
		
		return $this->ListByIDsAsArray($aPageIDs, $bShowHidden);
	}
	
	function ListAllAsArray($nParentID=null, $sKeyword='', $sTemplate='', $bShowHidden = false)
	{
		$result = $this->ListAll($nParentID, $sKeyword, $sTemplate, $bShowHidden);
		
		$aParents = $this->ListAllAsArraySimple(null, '', '', true);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			if (!empty($row->ParentPageID))
			{
				if ($row->ParentPageID != DEF_PAGE)
				{
					$rParent = $this->GetByID($row->ParentPageID);
					if (!empty($rParent->ParentPageID))
						$aToReturn[$row->PageID] = $aParents[$rParent->ParentPageID].' / '.$aParents[$row->ParentPageID].' / '.$row->Title;
					else
						$aToReturn[$row->PageID] = $aParents[$row->ParentPageID].' / '.$row->Title;
				}
				else
					$aToReturn[$row->PageID] = $aParents[$row->ParentPageID].' / '.$row->Title;
			}
			else
				$aToReturn[$row->PageID] = $row->Title;
		}
		
		return $aToReturn;
	}
	
	function ListAllParentsAsArraySimple($nParentID=null, $sKeyword='', $sTemplate='', $bShowHidden = false)
	{
		$result = $this->ListAll($nParentID, $sKeyword, $sTemplate, $bShowHidden);
		
		$aParents = $this->ListAllAsArraySimple(null, '', '', true);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			if (!empty($row->ParentPageID))
			{
				$aToReturn[$row->PageID] = $aParents[$row->ParentPageID];
			}
			else
				$aToReturn[$row->PageID] = $row->Title;
		}
		
		return $aToReturn;
	}
	
	function ListAllAsArraySimple($nParentID=null, $sKeyword='', $sTemplate='', $bShowHidden = false)
	{
		$result = $this->ListAll($nParentID, $sKeyword, $sTemplate, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->PageID] = $row->Title;
		}
		
		return $aToReturn;
	}
	
	function ListByIDsAsArray($aPageIDs, $bShowHidden = false)
	{
		$result = $this->ListByIDs($aPageIDs, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->PageID] = $row->Title;
		}
		
		return $aToReturn;
	}
	
	function GetRootPageID($nID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.' 
							WHERE '.$this->_sPrimaryKey.'="'.$nID.'" LIMIT 1';
		$result = mysql_query($sSQL);
		dbAssert($result, $sSQL);
		$row = mysql_fetch_object($result);
		
		if ($row->ParentPageID == DEF_PAGE)
		{
			return $row->PageID;
		}
		else
		{
			return $this->GetRootPageID($row->ParentPageID);
		}
	}
	
	function GetParentsPathAsArray($nID, $aPath=null)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		if (!is_array($aPath))
			$aPath = array();
		$aPath[] = $nID;
		
		$strSQL = 	'SELECT * FROM '.$this->_sTable.' 
								WHERE '.$this->_sPrimaryKey.'="'.$nID.'" LIMIT 1';
		$result = mysql_query($strSQL, $this->_dbCon);
		dbAssert($result, $strSQL);
		
		$row = mysql_fetch_object($result);
		if (!$row)
			return $aPath;
		if ($row->ParentPageID == DEF_PAGE)
		{
			$aPath[] = $row->ParentPageID;
			return $aPath;
		}
		else
		{
			return $this->GetParentsPathAsArray($row->ParentPageID, $aPath);
		}
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
	
	function Insert($sName, $sKeywords, $sDescription, $sText, $nParentID, $nReqUserStatus=0, $bIsHidden=0, $sTemplate='')
	{
		global $aLanguages, $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nParentID) || !is_numeric($nParentID)) return false;
		
		$nID = $this->GetMaxID() + 1;
		$nOrder = $this->GetMaxOrder() + 1;
		foreach($aLanguages as $key=>$value)
		{
			// RequiredUserStatus="'.$nReqUserStatus.'",
			$sSQL = 'INSERT INTO '.$this->_sTable.' SET 
									'.$this->_sPrimaryKey.'="'.$nID.'", 
									ParentPageID="'.$nParentID.'", 
									Title="'.$sName.'", 
									MetaKeywords="'.$sKeywords.'", 
									MetaDescription="'.$sDescription.'", 
									Description="'.$sText.'", 
									TemplateFile="'.$sTemplate.'", 
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
	
	function Update($nID, $sName, $sKeywords, $sDescription, $sText, $nReqUserStatus=0, $bIsHidden=0, $nLangID=0)
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
	
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
								Title="'.$sName.'", 
								MetaKeywords="'.$sKeywords.'", 
								MetaDescription="'.$sDescription.'", 
								Description="'.$sText.'" 
							WHERE LanguageID='.$nLangID.' AND '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		// update lang independent values, if any
		// RequiredUserStatus="'.$nReqUserStatus.'", 
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
							IsHidden="'.$bIsHidden.'", 
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'", 
							LastUpdate=NOW() 
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
		
		$strSQL = 	'UPDATE '.$this->_sTable.' SET '.$this->_sDefSortField.'=('.$this->_sDefSortField.'+'.$nDelta.') WHERE '.$this->_sPrimaryKey.'="'.$itemToMove->PageID.'" ';
		$result = mysql_query($strSQL, $this->_dbCon);
		dbAssert($result, $strSQL);
		
		$strSQL = 	'UPDATE '.$this->_sTable.' SET '.$this->_sDefSortField.'=('.$this->_sDefSortField.'-'.$nDelta.') WHERE '.$this->_sPrimaryKey.'="'.$item->PageID.'" ';
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
		global $oNews, $oPublication, $oFestival, $oPlace, $oEvent, $oProgram;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		$sSQL = 'DELETE FROM '.$this->_sTable.' 
							WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		// TODO: delete relations if any
		$oNews->DeleteNewsPage(0, $nID);
		$oPublication->DeletePublicationPage(0, $nID);
		$oFestival->DeleteFestivalPage(0, $nID);
		$oPlace->DeletePlacePage(0, $nID);
		$oEvent->DeleteEventPage(0, $nID);
		$oProgram->DeleteProgramPage(0, $nID);
		
		return true;
	}
	
	function InsertPageCityFilter($nPageID, $xCityID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nPageID) && (!is_numeric($xCityID) || !is_array($xCityID))) return false;
		
		if (is_array($xCityID) && count($xCityID) > 0)
		{
			// prepare list of values: ex: '(1,2),(1,3),(1,4);' where 1 is primaryID, the rest are IDs array
			$sRestSQL = '('.$nPageID.','.join('),('.$nPageID.',',$xCityID).')';
		}
		elseif (is_numeric($xCityID) && !empty($xCityID))
		{
			$sRestSQL = '('.$nPageID.','.$xCityID.')';
		}
		else
			return false;
		
		$sSQL = 'REPLACE INTO '.$this->_sRelCityFilterTable.' (PageID, CityID) VALUES '.$sRestSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return true;
	}
	
	function DeletePageCityFilter($nPageID=0, $nCityID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nPageID) || !is_numeric($nCityID)) return false;
		if (empty($nPageID) && empty($nCityID)) return false;
			
		$sFilter = '';
		if (!empty($nPageID)) 
		{
			$sFilter .= ' AND PageID="'.$nPageID.'" ';
		}
		if (!empty($nCityID)) 
		{
			$sFilter .= ' AND CityID="'.$nCityID.'" ';
		}
		
		$sSQL = 'DELETE FROM '.$this->_sRelCityFilterTable.' 
							WHERE 1 '.$sFilter.' ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return true;
	}
	
	function ListPageCityFiltersAsArray($nPageID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nPageID) || empty($nPageID)) return false;
		
		$sSQL = 'SELECT * FROM '.$this->_sRelCityFilterTable.' WHERE 
								PageID="'.$nPageID.'" 
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