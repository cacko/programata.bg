<?php
if (!$bInSite) die();
//=========================================================
class ProgramDatePeriod
{
	var $_sTable = null;
	var $_sRelProgramTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;

	function ProgramDatePeriod($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblProgramDatePeriod';
		$this->_sRelProgramTable = DB_TABLE_PREFIX.'tblProgram';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;

		$this->_aFields = array();
		$this->_aFields[1] = 'ProgramDatePeriodID';
		$this->_aFields[2] = 'ProgramID';
		$this->_aFields[3] = 'StartDate';
		$this->_aFields[4] = 'EndDate';
		$this->_aFields[5] = 'LastUpdate';
		$this->_aFields[6] = 'LastUpdateUserID';
		$this->_aFields[7] = 'IsHidden';

		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[3];
	}

	function ListAll($nProgramID=null, $dStartDate=null, $dEndDate=null, $bShowHidden = false, $nMaxRows=0, $aOrder=null)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT * FROM '.$this->_sTable.'
					WHERE 1=1 ';
		if (!is_null($nProgramID) && !empty($nProgramID))
		{
			$sSQL .=' AND ProgramID="'.$nProgramID.'" ';
		}
		if (!is_null($dStartDate) && !is_null($dEndDate))
		{
//			$sSQL .= ' AND ((StartDate>="'.$dStartDate.'" AND StartDate<="'.$dEndDate.'")
//					OR (EndDate>="'.$dStartDate.'" AND EndDate<="'.$dEndDate.'")
//					OR (StartDate<="'.$dStartDate.'" AND EndDate>="'.$dEndDate.'"))';
			//maya change. Better query
			$sSQL .= ' AND (StartDate<="'.$dEndDate.'" AND EndDate>="'.$dStartDate.'")';
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
		if (!empty($nMaxRows))
		{
			$sSQL .=' LIMIT '.$nMaxRows.' ';
		}
		//echo $sSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $result;
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

	function Insert($nProgramID, $dStartDate, $dEndDate, $bIsHidden=0)
	{
		global $oSession;

		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nProgramID) || !is_numeric($nProgramID)) return false;
		if (empty($dStartDate) || empty($dEndDate)) return false;
		if ($dStartDate == DEFAULT_DATE_DB_VALUE || $dEndDate == DEFAULT_DATE_DB_VALUE) return false;

		$sSQL = 'INSERT INTO '.$this->_sTable.' SET
							ProgramID="'.$nProgramID.'",
							StartDate="'.$dStartDate.'",
							EndDate="'.$dEndDate.'",
							IsHidden="'.$bIsHidden.'",
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
							LastUpdate=NOW() ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		$nID = mysql_insert_id($this->_dbCon);

		$sSQL = 'INSERT INTO '.$this->_sTable.'Status SET
							ProgramID="'.$nProgramID.'",
							ProgramDatePeriodID="'.$nID.'",
							ChangeType="added",
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
							LastUpdate=NOW() ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $nID;
	}

	function Update($nID, $nProgramID, $dStartDate, $dEndDate, $bIsHidden=0, $nLangID=0)
	{
		global $oSession;

		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nProgramID) || !is_numeric($nProgramID)) return false;
		if (empty($dStartDate) || empty($dEndDate)) return false;
		if ($dStartDate == DEFAULT_DATE_DB_VALUE || $dEndDate == DEFAULT_DATE_DB_VALUE) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;

		$sSQL = 'UPDATE '.$this->_sTable.' SET
							ProgramID="'.$nProgramID.'",
							StartDate="'.$dStartDate.'",
							EndDate="'.$dEndDate.'",
							IsHidden="'.$bIsHidden.'",
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
							LastUpdate=NOW()
						WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		$sSQL = 'INSERT INTO '.$this->_sTable.'Status SET
							ProgramID="'.$nProgramID.'",
							ProgramDatePeriodID="'.$nID.'",
							ChangeType="updated",
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
							LastUpdate=NOW() ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $nID;
	}

	function Delete($nID)
	{
		global $oSession;

		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;

		$row = $this->GetByID($nID);

		$sSQL = 'DELETE FROM '.$this->_sTable.'
							WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		// TODO: delete relations if any

		$sSQL = 'INSERT INTO '.$this->_sTable.'Status SET
							ProgramID="'.$row->ProgramID.'",
							ProgramDatePeriodID="'.$nID.'",
							ChangeType="deleted",
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
							LastUpdate=NOW() ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

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