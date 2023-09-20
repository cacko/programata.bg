<?php
if (!$bInSite) die();
//=========================================================
class PlaceGuide
{
	var $_sTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;
	
	function PlaceGuide($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblPlaceGuide';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;
		
		$this->_aFields = array();
		$this->_aFields[1] = 'PlaceGuideID';
		$this->_aFields[2] = 'PlaceID';
		$this->_aFields[3] = 'PlaceTypeID';
		$this->_aFields[4] = 'Category';
		$this->_aFields[5] = 'EntranceFeeText';
		$this->_aFields[6] = 'HasEntranceFee';
		$this->_aFields[7] = 'NrSeats';
		$this->_aFields[8] = 'MusicStyle';
		$this->_aFields[9] = 'HasDJ';
		$this->_aFields[10] = 'HasLiveMusic';
		$this->_aFields[11] = 'HasKaraoke';
		$this->_aFields[12] = 'HasBgndMusic';
		$this->_aFields[13] = 'HasFaceControl';
		$this->_aFields[14] = 'HasCuisine';
		$this->_aFields[15] = 'CuisineText';
		$this->_aFields[16] = 'HasTerrace';
		$this->_aFields[17] = 'HasSmokingArea';
		$this->_aFields[18] = 'HasClima';
		$this->_aFields[19] = 'HasParking';
		$this->_aFields[20] = 'HasWardrobe';
		$this->_aFields[21] = 'HasCardPayment';
		$this->_aFields[22] = 'EntertainmentText';
		$this->_aFields[23] = 'HasWifi';
		$this->_aFields[24] = 'LastUpdate';
		$this->_aFields[25] = 'LastUpdateUserID';
		$this->_aFields[26] = 'IsHidden';
		$this->_aFields[27] = 'LanguageID';
		$this->_aFields[28] = 'VacationStartDate';
		$this->_aFields[29] = 'VacationEndDate';
		$this->_aFields[30] = 'HasDelivery';
		
		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[2];
	}
	
	function ListAll($nPlaceID=null, $nPlaceType=null, $sKeyword='', $bHasWifi = false, $bShowHidden = false, $nMaxRows=0, $aOrder=null)
	{
		global $aSortDirections;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.' 
							WHERE LanguageID='.$this->_nLangID.' ';
		if (!is_null($nPlaceID) && !empty($nPlaceID))
		{
			$sSQL .=' AND PlaceID="'.$nPlaceID.'" ';
		}
		if (!is_null($nPlaceType) && !empty($nPlaceType))
		{
			$sSQL .=' AND PlaceTypeID="'.$nPlaceType.'" ';
		}
		if (!empty($sKeyword)) 
		{
			$sSQL .= ' AND (Category LIKE "%'.$sKeyword.'%" OR 
					EntranceFeeText LIKE "%'.$sKeyword.'%" OR
					MusicStyle LIKE "%'.$sKeyword.'%" OR 
					CuisineText LIKE "%'.$sKeyword.'%" OR
					EntertainmentText LIKE "%'.$sKeyword.'%") ';
		}
		if ($bHasWifi)
		{
			$sSQL .= ' AND HasWifi="'.B_TRUE.'" ';
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND IsHidden="'.B_FALSE.'" ';
		}
		//order
		if (!isset($aOrder) || !is_array($aOrder) || empty($aOrder[SORT_FIELD]))
		{
			$sSQL .=' ORDER BY '.$this->_sDefSortField.' ASC, '.$this->_sPrimaryKey.' DESC';
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
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		return $result;
	}
	
	function ListAllAsArray($nPlaceID=null, $nPlaceType=null, $sKeyword='', $bHasWifi = false, $bShowHidden = false)
	{
		$result = $this->ListAll($nPlaceID, $nPlaceType, '', $bHasWifi, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->PlaceGuideID] = $row->PlaceID;
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
	
	function GetByPlaceID($nID, $nLangID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
		
		$sSQL = 'SELECT * FROM '.$this->_sTable.' 
				WHERE LanguageID='.$nLangID.' AND PlaceID="'.$nID.'" LIMIT 1';
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
	
	function Insert($nPlaceID, $nPlaceTypeID, $bHasEntranceFee, $sNrSeats, 
			$bHasDJ, $bHasLiveMusic, $bHasKaraoke, $bHasBgndMusic, $bHasFaceControl, $bHasCuisine, 
			$bHasTerrace, $bHasClima, $bHasParking, $bHasWardrobe, $bHasCardPayment, 
			$bHasWifi, $bHasDelivery, $dVacationStartDate, $dVacationEndDate, $bIsHidden=0)
			//$sCategory, $bHasSmokingArea, $sEntranceFeeText, $sMusicStyle, $sCuisineText, $sEntertainmentText,
	{
		global $aLanguages, $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		
		$nID = $this->GetMaxID() + 1;
		foreach($aLanguages as $key=>$value)
		{
			$sSQL = 'INSERT INTO '.$this->_sTable.' SET 
								'.$this->_sPrimaryKey.'="'.$nID.'", 
								PlaceID="'.$nPlaceID.'", 
								PlaceTypeID="'.$nPlaceTypeID.'",
								NrSeats="'.$sNrSeats.'", 
								HasEntranceFee="'.$bHasEntranceFee.'",
								HasDJ="'.$bHasDJ.'", 
								HasLiveMusic="'.$bHasLiveMusic.'",
								HasKaraoke="'.$bHasKaraoke.'",
								HasBgndMusic="'.$bHasBgndMusic.'",
								HasFaceControl="'.$bHasFaceControl.'",
								HasCuisine="'.$bHasCuisine.'",
								HasTerrace="'.$bHasTerrace.'",
								HasClima="'.$bHasClima.'",
								HasParking="'.$bHasParking.'",
								HasWardrobe="'.$bHasWardrobe.'",
								HasCardPayment="'.$bHasCardPayment.'",
								HasWifi="'.$bHasWifi.'",
								HasDelivery="'.$bHasDelivery.'",
								VacationStartDate="'.$dVacationStartDate.'",
								VacationEndDate="'.$dVacationEndDate.'", 
								IsHidden="'.$bIsHidden.'", 
								LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'", 
								LastUpdate=NOW(), 
								LanguageID='.$key.' ';
								/*
								Category="'.$sCategory.'",
								EntranceFeeText="'.$sEntranceFeeText.'",
								MusicStyle="'.$sMusicStyle.'",
								CuisineText="'.$sCuisineText.'",
								EntertainmentText="'.$sEntertainmentText.'",
								*/
			$result = mysql_query($sSQL, $this->_dbCon);
			dbAssert($result, $sSQL);
		}
		return $nID;
	}
	
	function Update($nID, $nPlaceID, $nPlaceTypeID, $bHasEntranceFee, $sNrSeats, 
			$bHasDJ, $bHasLiveMusic, $bHasKaraoke, $bHasBgndMusic, $bHasFaceControl, $bHasCuisine, 
			$bHasTerrace, $bHasClima, $bHasParking, $bHasWardrobe, $bHasCardPayment, 
			$bHasWifi, $bHasDelivery, $dVacationStartDate, $dVacationEndDate, $bIsHidden=0, $nLangID=0)
			//$sCategory, $bHasSmokingArea, $sEntranceFeeText, $sMusicStyle, $sCuisineText, $sEntertainmentText,
	{
		global $oSession;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;
		
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
							NrSeats="'.$sNrSeats.'" 
						WHERE LanguageID='.$nLangID.' AND '.$this->_sPrimaryKey.'="'.$nID.'" ';
							/*
							Category="'.$sCategory.'",
							EntranceFeeText="'.$sEntranceFeeText.'",
							MusicStyle="'.$sMusicStyle.'",
							CuisineText="'.$sCuisineText.'",
							EntertainmentText="'.$sEntertainmentText.'",
							HasSmokingArea="'.$bHasSmokingArea.'",
							*/
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		
		// update lang independent values, if any
		$sSQL = 'UPDATE '.$this->_sTable.' SET 
							PlaceID="'.$nPlaceID.'", 
							PlaceTypeID="'.$nPlaceTypeID.'",
							HasEntranceFee="'.$bHasEntranceFee.'",
							HasDJ="'.$bHasDJ.'", 
							HasLiveMusic="'.$bHasLiveMusic.'",
							HasKaraoke="'.$bHasKaraoke.'",
							HasBgndMusic="'.$bHasBgndMusic.'",
							HasFaceControl="'.$bHasFaceControl.'",
							HasCuisine="'.$bHasCuisine.'",
							HasTerrace="'.$bHasTerrace.'",
							HasClima="'.$bHasClima.'",
							HasParking="'.$bHasParking.'",
							HasWardrobe="'.$bHasWardrobe.'",
							HasCardPayment="'.$bHasCardPayment.'",
							HasWifi="'.$bHasWifi.'",
							HasDelivery="'.$bHasDelivery.'",
							VacationStartDate="'.$dVacationStartDate.'",
							VacationEndDate="'.$dVacationEndDate.'", 
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
		global $oProgram;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		$sSQL = 'DELETE FROM '.$this->_sTable.' 
							WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		// TODO: delete relations if any
		
		return true;
	}
	
	function DeleteByPlaceID($nID)
	{
		global $oProgram;
		
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		
		$sSQL = 'DELETE FROM '.$this->_sTable.' 
							WHERE PlaceID="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		// TODO: delete relations if any
		
		return true;
	}
}
?>