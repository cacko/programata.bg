<?php
if (!$bInSite) die();
//=========================================================
class Promotion
{
	var $_sTable = null;
	var $_sRelCityTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;
	
	function Promotion($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblPromotion';
		$this->_sRelCityTable = DB_TABLE_PREFIX.'relPromotion2City';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;
		
		$this->_aFields = array();
		$this->_aFields[1] = 'PromotionID';
		$this->_aFields[2] = 'StartDate';
		$this->_aFields[3] = 'EndDate';
		$this->_aFields[4] = 'EntityTypeID';
		$this->_aFields[5] = 'EntityID';
		$this->_aFields[6] = 'PageID';
		$this->_aFields[7] = 'SortOrder';
		$this->_aFields[8] = 'LastUpdate';
		$this->_aFields[9] = 'LastUpdateUserID';
		$this->_aFields[10] = 'IsHidden';
		$this->_aFields[11] = 'PromotionTypeID';
		
		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[7];
	}
	
	function ListAll($nParentID=null, $nCityID=null, $nPromotionTypeID=null, $dStartDate=null, $dEndDate=null, $bShowHidden = false, $nMaxRows=0, $aOrder=null)
	{
		global $aSortDirections;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT DISTINCT a.* FROM '.$this->_sTable.' as a 
					LEFT OUTER JOIN '.$this->_sRelCityTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.PromotionID 
					WHERE 1=1 ';
		if (!is_null($nParentID) && !empty($nParentID))
		{
			$sSQL .=' AND PageID="'.$nParentID.'" ';
		}
		if (!is_null($nCityID) && !empty($nCityID))
		{
			$sSQL .=' AND CityID="'.$nCityID.'" ';
		}
		if (!is_null($nPromotionTypeID) && !empty($nPromotionTypeID))
		{
			$sSQL .=' AND PromotionTypeID="'.$nPromotionTypeID.'" ';
		}
		if (!is_null($dStartDate) && !is_null($dEndDate))
		{
			$sSQL .= ' AND ((StartDate>="'.$dStartDate.'" AND StartDate<="'.$dEndDate.'") 
					OR (EndDate>="'.$dStartDate.'" AND EndDate<="'.$dEndDate.'") 
					OR (StartDate<="'.$dStartDate.'" AND EndDate>="'.$dEndDate.'"))';
		}
		else
		{
			if (!is_null($dStartDate))
			{
				$sSQL .=' AND StartDate>="'.$dStartDate.'" AND EndDate<="'.$dStartDate.'" ';
			}
			if (!is_null($dEndDate))
			{
				$sSQL .=' AND StartDate>="'.$dEndDate.'" AND EndDate<="'.$dEndDate.'" ';
			}
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND IsHidden="'.B_FALSE.'" ';
		}
		if (!empty($nMaxRows))
		{
			$sSQL .=' ORDER BY RAND() LIMIT '.$nMaxRows.'';
		}
		else
		{
			//order
			if (!isset($aOrder) || !is_array($aOrder) || empty($aOrder[SORT_FIELD]))
			{
				$sSQL .=' ORDER BY '.$this->_sDefSortField.' ASC, '.$this->_sPrimaryKey.' ASC';
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
		}
		//echo $sSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return $result;
	}
	
	function ListAllAsArray($nParentID=null, $nCityID=null, $nPromotionTypeID=null, $dStartDate=null, $dEndDate=null, $bShowHidden = false)
	{
		$result = $this->ListAll($nParentID, $nCityID, $nPromotionTypeID, $dStartDate, $dEndDate, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->PromotionID] = $row->EntityID;
		}
		
		return $aToReturn;
	}
	
	function GetByID($nID, $nLangID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.' 
				WHERE '.$this->_sPrimaryKey.'="'.$nID.'" LIMIT 1';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		$row = mysql_fetch_object($result);
	
		return $row;
	}
	
	function GetMaxOrder()
	{
		$strSQL = 'SELECT MAX('.$this->_sDefSortField.') FROM '.$this->_sTable.'  LIMIT 1';
		$result = mysql_query($strSQL, $this->_dbCon);
		dbAssert($result, $strSQL);
		
		$row = mysql_fetch_array($result);
		return $row[0];
	}
	
	function Insert($dStartDate, $dEndDate, $nPromotionTypeID, $nEntityTypeID, $nEntityID, $nPageID, $nOrder, $bIsHidden=0)
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nPromotionTypeID) || !is_numeric($nPromotionTypeID)) return false;
		if (empty($nEntityTypeID) || !is_numeric($nEntityTypeID)) return false;
		if (empty($nEntityID) || !is_numeric($nEntityID)) return false;
		if (empty($nPageID) || !is_numeric($nPageID)) return false;
		
		if (empty($nOrder))
			$nOrder = $this->GetMaxOrder() + 1;
		$sSQL = 'INSERT INTO '.$this->_sTable.' SET 
							StartDate="'.$dStartDate.'",
							EndDate="'.$dEndDate.'", 
							PromotionTypeID="'.$nPromotionTypeID.'",
							EntityTypeID="'.$nEntityTypeID.'", 
							EntityID="'.$nEntityID.'", 
							PageID="'.$nPageID.'",
							SortOrder="'.$nOrder.'", 
							IsHidden="'.$bIsHidden.'", 
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'", 
							LastUpdate=NOW() ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return mysql_insert_id($this->_dbCon);
	}
	
	function Update($nID, $dStartDate, $dEndDate, $nPromotionTypeID, $nEntityTypeID, $nEntityID, $nPageID, $nOrder, $bIsHidden=0, $nLangID=0)
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nPromotionTypeID) || !is_numeric($nPromotionTypeID)) return false;
		if (empty($nEntityTypeID) || !is_numeric($nEntityTypeID)) return false;
		if (empty($nEntityID) || !is_numeric($nEntityID)) return false;
		if (empty($nPageID) || !is_numeric($nPageID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
	
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
							StartDate="'.$dStartDate.'",
							EndDate="'.$dEndDate.'",
							PromotionTypeID="'.$nPromotionTypeID.'", 
							EntityTypeID="'.$nEntityTypeID.'", 
							EntityID="'.$nEntityID.'", 
							PageID="'.$nPageID.'",
							'.IIF(!empty($nOrder), 'SortOrder="'.$nOrder.'", ', '').'
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
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		$sSQL = 'DELETE FROM '.$this->_sTable.' 
							WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		// TODO: delete relations if any
		$this->DeletePromotionCity($nID);
		
		return true;
	}
	
	function InsertPromotionCity($nPromotionID, $xCityID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nPromotionID) && (!is_numeric($xCityID) || !is_array($xCityID))) return false;
		
		if (is_array($xCityID) && count($xCityID) > 0)
		{
			// prepare list of values: ex: '(1,2),(1,3),(1,4);' where 1 is primaryID, the rest are IDs array
			$sRestSQL = '('.$nPromotionID.','.join('),('.$nPromotionID.',',$xCityID).')';
		}
		elseif (is_numeric($xCityID) && !empty($xCityID))
		{
			$sRestSQL = '('.$nPromotionID.','.$xCityID.')';
		}
		else
			return false;
		
		$sSQL = 'REPLACE INTO '.$this->_sRelCityTable.' (PromotionID, CityID) VALUES '.$sRestSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return true;
	}
	
	function DeletePromotionCity($nPromotionID=0, $nCityID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nPromotionID) || !is_numeric($nCityID)) return false;
		if (empty($nPromotionID) && empty($nCityID)) return false;
			
		$sFilter = '';
		if (!empty($nPromotionID)) 
		{
			$sFilter .= ' AND PromotionID="'.$nPromotionID.'" ';
		}
		if (!empty($nCityID)) 
		{
			$sFilter .= ' AND CityID="'.$nCityID.'" ';
		}
		
		$sSQL = 'DELETE FROM '.$this->_sRelCityTable.' 
							WHERE 1 '.$sFilter.' ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return true;
	}
	
	function ListPromotionCitiesAsArray($nPromotionID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nPromotionID) || empty($nPromotionID)) return false;
		
		$sSQL = 'SELECT * FROM '.$this->_sRelCityTable.' WHERE 
								PromotionID="'.$nPromotionID.'" 
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