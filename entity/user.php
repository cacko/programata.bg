<?php
if (!$bInSite) die();
//=========================================================
class User
{
	var $_sTable = null;
	var $_sCalendarTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;
	
	function User($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblUser';
		$this->_sCalendarTable = DB_TABLE_PREFIX.'tblUserCalendar';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;
		
		$this->_aFields = array();
		$this->_aFields[1] = 'Username';
		$this->_aFields[2] = 'Password';
		$this->_aFields[3] = 'Email';
		$this->_aFields[4] = 'FirstName';
		$this->_aFields[5] = 'LastName';
		$this->_aFields[6] = 'Company';
		$this->_aFields[7] = 'Address';
		$this->_aFields[8] = 'City';
		$this->_aFields[9] = 'Country';
		$this->_aFields[10] = 'Phone';
		$this->_aFields[11] = 'UserStatus';
		$this->_aFields[12] = 'LastLogin';
		$this->_aFields[13] = 'NrLogins';
		$this->_aFields[14] = 'LastUpdate';
		$this->_aFields[15] = 'UserID';
		$this->_aFields[16] = 'DefaultCityID';
		$this->_aFields[17] = 'Profession';
		$this->_aFields[18] = 'Note';
		$this->_aFields[19] = 'Url';
		
		$this->_sPrimaryKey = $this->_aFields[15];
		$this->_sDefSortField = $this->_aFields[15];
	}
	
	function ListAll($nStatus=null, $sKeyword='', $sCity='', $sCountry='', $aOrder=null)
	{
		global $aSortDirections;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.' WHERE 1=1 ';
		if (!is_null($nStatus) && !empty($nStatus))
		{
			$sSQL .=' AND UserStatus="'.$nStatus.'" ';
		}
		if (!empty($sCity))
		{
			$sSQL .=' AND City="'.$sCity.'" ';
		}
		if (!empty($sCountry))
		{
			$sSQL .=' AND Country="'.$sCountry.'" ';
		}
		if (!empty($sKeyword)) 
		{
			$sSQL .= ' AND (FirstName LIKE "%'.$sKeyword.'%" 
					OR LastName LIKE "%'.$sKeyword.'%"
					OR Username LIKE "%'.$sKeyword.'%" 
					OR Company LIKE "%'.$sKeyword.'%" 
					OR Profession LIKE "%'.$sKeyword.'%" 
					OR Note LIKE "%'.$sKeyword.'%" 
					OR Url LIKE "%'.$sKeyword.'%" 
					OR Address LIKE "%'.$sKeyword.'%")';
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
	
	function ListFieldAsArray($sField)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT DISTINCT '.$this->_aFields[$sField].' FROM '.$this->_sTable.' ORDER BY '.$this->_aFields[$sField].' ASC';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		$aToReturn = array();
		while($row = mysql_fetch_array($result))
		{
			if (!empty($row[0])) $aToReturn[] = $row[0];
		}
		
		return $aToReturn;
	}
	
	function ListAllAsArray($nStatus=null)
	{
		$result = $this->ListAll($nStatus);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->UserID] = $row->Username.' ('.$row->FirstName.' '.$row->LastName.')';
		}
		
		return $aToReturn;
	}
	
	function GetByID($nID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.' 
							WHERE '.$this->_sPrimaryKey.'="'.$nID.'" LIMIT 1';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		$row = mysql_fetch_object($result);
	
		return $row;
	}
	
	function CanRegister($sUsername)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT * FROM '.$this->_sTable.'  
							WHERE Username="'.$sUsername.'" LIMIT 1';
		
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		if (mysql_num_rows($result))
			return false;
		else
			return true;
	}
	
	function RetrieveAccount($sUsername='', $sEmail='')
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($sUsername) && empty($sEmail)) return false;
	
		$sSQL = 'SELECT * FROM '.$this->_sTable.' WHERE 1=1';
		if (!empty($sUsername))
		{
			$sSQL .= ' AND Username="'.$sUsername.'" ';
		}
		if (!empty($sEmail))
		{
			$sSQL .= ' AND Email="'.$sEmail.'" ';
		}
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return $result;
	}
	
	function CanLogin($sUsername,$sPassword)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($sUsername) && empty($sPassword)) return false;
		
		// LIKE BINARY provides case-sensitive query
		$sSQL = 'SELECT * FROM '.$this->_sTable.' 
							WHERE Username LIKE BINARY "'.$sUsername.'" 
								AND Password LIKE BINARY "'.md5($sPassword).'" 
								AND UserStatus>0 LIMIT 1';

		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		if (mysql_num_rows($result))
		{
			// login ok, return the user row
			return mysql_fetch_object($result);
		}
		else
			return false;
	}
	
	function TrackLogin($nID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		$ip = getenv("REMOTE_ADDR");
		
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
								NrLogins=NrLogins+1,
								LastLoginIP="'.$ip.'", 
								LastLogin=NOW() 
							WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
	}
	
	function Activate($nID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
								UserStatus="'.USER_REGULAR.'", 
								LastUpdate=NOW() 
							WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
	}
	
	function Deactivate($nID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
								UserStatus="'.USER_GUEST.'", 
								LastUpdate=NOW() 
							WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
	}
	
	function Insert($sUsername, $sPassword, $sEmail, $sFirstName, $sLastName, $sCompany='', $sAddress='', $sCity='', $sCountry='', $nPhone='',
			$nSex, $nAge, $sProfession='', $sNote='', $sUrl='', $aEventims=array(), $sConfirmKey='', $nDefaultCity=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		$sAddress = strShorten($sAddress, TEXT_LEN_EMAIL);
		
		$ip = getenv("REMOTE_ADDR");
		
		$sSQL = 'INSERT INTO '.$this->_sTable.' SET 
								Username="'.$sUsername.'", 
								Password="'.md5($sPassword).'", 
								Email="'.$sEmail.'", 
								FirstName="'.$sFirstName.'", 
								LastName="'.$sLastName.'", 
								Company="'.$sCompany.'", 
								Profession="'.$sProfession.'", 
								Address="'.$sAddress.'", 
								City="'.$sCity.'", 
								Country="'.$sCountry.'", 
								Phone="'.$nPhone.'",
								Sex="'.$nSex.'",
								Age="'.$nAge.'", 
								Url="'.$sUrl.'",
								EventimCategories="'.implode(',',$aEventims).'", 
								ConfirmKey="'.$sConfirmKey.'", 
								LastLoginIP="'.$ip.'", 
								Note="'.$sNote.'", 
								UserStatus="'.USER_GUEST.'", 
								DefaultCityID="'.$nDefaultCity.'", 
								LastUpdate=NOW() ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return mysql_insert_id($this->_dbCon);
	}
	
	function Update($nID, $sEmail, $sFirstName, $sLastName, $sCompany='', $sAddress='', $sCity='', $sCountry='', $nPhone='',
			$nSex, $nAge, $sProfession='', $sNote='', $sUrl='', $aEventims=array())
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		$sAddress = strShorten($sAddress, TEXT_LEN_EMAIL);
	
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
								Email="'.$sEmail.'", 
								FirstName="'.$sFirstName.'", 
								LastName="'.$sLastName.'", 
								Company="'.$sCompany.'", 
								Profession="'.$sProfession.'", 
								Address="'.$sAddress.'", 
								City="'.$sCity.'", 
								Country="'.$sCountry.'", 
								Phone="'.$nPhone.'",
								Sex="'.$nSex.'",
								Age="'.$nAge.'", 
								Url="'.$sUrl.'",
								EventimCategories="'.implode(',',$aEventims).'", 
								Note="'.$sNote.'", 
								LastUpdate=NOW()  
							WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $nID;
	}
	
	function UpdateStatus($nID, $nStatus, $nCity=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
	
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
								UserStatus="'.$nStatus.'",
								DefaultCityID="'.$nCity.'", 
								LastUpdate=NOW() 
							WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return $nID;
	}
	
	function ChangePassword($nID, $sPassword)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
	
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
								Password="'.md5($sPassword).'", 
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
		$this->DeleteCalendarItemByUser($nID);
		
		return true;
	}
	
	function InsertCalendarItem($nUserID, $nEntityID, $nEntityTypeID, $sNote, $dStartDate, $dEndDate, $dStartTime, $dReminderDate, $dReminderTime, $bIsHidden=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'INSERT INTO '.$this->_sCalendarTable.' SET 
								UserID="'.$nUserID.'",
								EntityID="'.$nEntityID.'",
								EntityTypeID="'.$nEntityTypeID.'",
								Note="'.$sNote.'",
								StartDate="'.$dStartDate.'",
								EndDate="'.$dEndDate.'", 
								StartTime="'.$dStartTime.'", 
								ReminderDate="'.$dReminderDate.'", 
								ReminderTime="'.$dReminderTime.'",
								IsHidden="'.$bIsHidden.'", 
								LastUpdate=NOW() ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return mysql_insert_id($this->_dbCon);
	}
	
	function UpdateCalendarItem($nUserCalendarID, $sNote, $dStartDate, $dEndDate, $dStartTime, $dReminderDate, $dReminderTime, $bIsHidden=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		

		$sSQL = 'UPDATE '.$this->_sCalendarTable.' SET 
								Note="'.$sNote.'",
								StartDate="'.$dStartDate.'",
								EndDate="'.$dEndDate.'", 
								StartTime="'.$dStartTime.'", 
								ReminderDate="'.$dReminderDate.'", 
								ReminderTime="'.$dReminderTime.'",
								IsHidden="'.$bIsHidden.'", 
								LastUpdate=NOW()
								WHERE UserCalendarID="'.$nUserCalendarID.'"';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return $nUserID;
	}
	
	function DeleteCalendarItem($nID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		$sSQL = 'DELETE FROM '.$this->_sCalendarTable.' 
							WHERE  UserCalendarID="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		// TODO: delete relations if any
		
		return true;
	}
	
	function DeleteCalendarItemByEntity($nEntityID, $nEntityTypeID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nEntityID) || !is_numeric($nEntityID)) return false;
		if (empty($nEntityTypeID) || !is_numeric($nEntityTypeID)) return false;
		
		$sSQL = 'DELETE FROM '.$this->_sCalendarTable.' 
							WHERE EntityID="'.$nEntityID.'" AND EntityTypeID="'.$nEntityTypeID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		// TODO: delete relations if any
		
		return true;
	}
	
	function DeleteCalendarItemByUser($nUserID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nUserID) || !is_numeric($nUserID)) return false;
		
		$sSQL = 'DELETE FROM '.$this->_sCalendarTable.' 
							WHERE UserID="'.$nUserID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		// TODO: delete relations if any
		
		return true;
	}
	
	function ListCalendarItems($nUserID=null, $dStartDate=null, $dEndDate=null, $nEntityID=null, $nEntityTypeID=null, $bShowHidden=false)
	{
		global $aSortDirections;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT * FROM '.$this->_sCalendarTable.' 
					WHERE 1=1 ';
		if (!is_null($nUserID) && !empty($nUserID))
		{
			$sSQL .=' AND UserID="'.$nUserID.'" ';
		}
		if (!is_null($nEntityID) && !empty($nEntityID))
		{
			$sSQL .=' AND EntityID="'.$nEntityID.'" ';
		}
		if (!is_null($nEntityTypeID) && !empty($nEntityTypeID))
		{
			$sSQL .=' AND EntityTypeID="'.$nEntityTypeID.'" ';
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
				$sSQL .=' AND ((StartDate>="'.$dStartDate.'" AND EndDate<="'.$dStartDate.'")) ';
			}
			if (!is_null($dEndDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dEndDate.'" AND EndDate<="'.$dEndDate.'")) ';
			}
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND IsHidden="'.B_FALSE.'" ';
		}
		//order
		$sSQL .=' ORDER BY UserID, StartDate ASC, EndDate ASC';
		
		//echo $sSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return $result;
	}
	
	function ListCalendarUsers($dStartDate=null, $dEndDate=null, $bShowHidden=false)
	{
		global $aSortDirections;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT a.* FROM '.$this->_sTable.' as a
				JOIN '.$this->_sCalendarTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.UserID 
					WHERE 1=1 ';
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
				$sSQL .=' AND ((StartDate>="'.$dStartDate.'" AND EndDate<="'.$dStartDate.'")) ';
			}
			if (!is_null($dEndDate))
			{
				$sSQL .=' AND ((StartDate>="'.$dEndDate.'" AND EndDate<="'.$dEndDate.'")) ';
			}
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND IsHidden="'.B_FALSE.'" ';
		}
		//order
		$sSQL .=' GROUP BY a.UserID
				ORDER BY a.UserID';
		
		//echo $sSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return $result;
	}
}
?>