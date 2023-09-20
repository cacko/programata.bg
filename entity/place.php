<?php
if (!$bInSite) die();
//=========================================================
class Place
{
	var $_sTable = null;
	var $_sRelPageTable = null;
	var $_sRelLabelTable = null;
	var $_sGuideTable = null;
	var $_sHallTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;

	function Place($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblPlace';
		$this->_sRelPageTable = DB_TABLE_PREFIX.'relPlace2Page';
		$this->_sRelLabelTable = DB_TABLE_PREFIX.'relPlace2Label';
		$this->_sGuideTable = DB_TABLE_PREFIX.'tblPlaceGuide';
		$this->_sHallTable = DB_TABLE_PREFIX.'tblHall';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;

		$this->_aFields = array();
		$this->_aFields[1] = 'PlaceID';
		$this->_aFields[2] = 'Title';
		$this->_aFields[3] = 'Letter';
		$this->_aFields[4] = 'ShortTitle';
		$this->_aFields[5] = 'MetaKeywords';
		$this->_aFields[6] = 'MetaDescription';
		$this->_aFields[7] = 'Description';
		$this->_aFields[8] = 'Address';
		$this->_aFields[9] = 'WorkingTime';
		$this->_aFields[10] = 'StartTime';
		$this->_aFields[11] = 'CityID';
		$this->_aFields[12] = 'LastUpdate';
		$this->_aFields[13] = 'LastUpdateUserID';
		$this->_aFields[14] = 'IsHidden';
		$this->_aFields[15] = 'LanguageID';
		$this->_aFields[16] = 'NrViews';
		$this->_aFields[17] = 'PlaceTypeID';
		$this->_aFields[18] = 'PlaceSubtypeID';
		$this->_aFields[19] = 'x';
		$this->_aFields[20] = 'y';

		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[2];
	}

	function ListAll($xParentID=null, $nCityID=null, $nPlaceType=null, $nPlaceSubtype=null, $sKeyword='', $xLetter='', $bIsNew=false, $bShowHidden = false, $nMaxRows=0, $aOrder=null, $minLat=null, $maxLat=null, $minLng=null, $maxLng=null)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT DISTINCT a.* FROM '.$this->_sTable.' as a
							LEFT OUTER JOIN '.$this->_sRelPageTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.PlaceID
							WHERE a.LanguageID='.$this->_nLangID.' ';
		if (!is_null($xParentID))
		{
			if (is_array($xParentID) && count($xParentID) > 0)
				$sSQL .= ' AND PageID IN ('.join(',', $xParentID).')';
			elseif (is_numeric($xParentID) && !empty($xParentID))
				$sSQL .= ' AND PageID="'.$xParentID.'" ';
		}
		if (!is_null($nCityID) && !empty($nCityID))
		{
			$sSQL .=' AND CityID="'.$nCityID.'" ';
		}
		if (!is_null($nPlaceType) && !empty($nPlaceType))
		{
			$sSQL .=' AND PlaceTypeID="'.$nPlaceType.'" ';
		}
		if (!is_null($nPlaceSubtype) && !empty($nPlaceSubtype))
		{
			$sSQL .=' AND PlaceSubtypeID="'.$nPlaceSubtype.'" ';
		}
		if ($bIsNew)
		{
			$nMaxID = $this->GetMaxID();
			$sSQL .= ' AND a.'.$this->_sPrimaryKey.'>="'.($nMaxID-NEWEST_PLACES).'" ';
		}
		if (!empty($sKeyword))
		{
			$sSQL .= ' AND (Title LIKE "%'.$sKeyword.'%" OR
					ShortTitle LIKE "%'.$sKeyword.'%" OR
					MetaKeywords LIKE "%'.$sKeyword.'%" OR
					MetaDescription LIKE "%'.$sKeyword.'%" OR
					Description LIKE "%'.$sKeyword.'%" OR
					WorkingTime LIKE "%'.$sKeyword.'%" OR
					Address LIKE "%'.$sKeyword.'%") ';
		}
		//if (!empty($sLetter))
		if ($xLetter != '')
		{
			if (is_array($xLetter) && count($xLetter) > 0)
			{
				$sSQL .= ' AND Letter IN ("'.join('","', $xLetter).'") ';
			}
			else
				$sSQL .= ' AND Letter="'.$xLetter.'" ';
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND a.IsHidden="'.B_FALSE.'" ';
		}
		if(!is_null($minLat) && !is_null($minLng) && !is_null($maxLat) && !is_null($maxLng))
		{
			$sSQL .= ' AND a.lat > "'.$minLat.'"
					   AND a.lat < "'.$maxLat.'"
					   AND a.long > "'.$minLng.'"
					   AND a.long < "'.$maxLng.'"';
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
		//echo '<!--'.$sSQL.'-->';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $result;
	}

	function ListAllAdvanced($xParentID=null, $nCityID=null, $nPlaceType=null, $nPlaceSubtype=null, $sKeyword='', $xLetter='',
				$nCuisine=null, $nAtmosphere=null, $nPriceCategory=null, $nMusicStyle=null, $bIsNew=false,
				$bHasEntranceFee=false, $bHasCardPayment=false, $bHasFaceControl=false, $bHasParking=false,
				$bHasDJ=false, $bHasLiveMusic=false, $bHasKaraoke=false, $bHasBgndMusic=false,
				$bHasCuisine=false, $bHasTerrace=false, $bHasClima=false, $bHasWardrobe=false, $bHasWifi=false, $bHasDelivery=false,
				$bShowHidden = false, $nMaxRows=0, $aOrder=null)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT DISTINCT a.* FROM '.$this->_sTable.' as a
							LEFT OUTER JOIN '.$this->_sRelPageTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.PlaceID
							LEFT OUTER JOIN '.$this->_sRelLabelTable.' as r2 ON a.'.$this->_sPrimaryKey.'=r2.PlaceID AND r2.ParentLabelID="'.GRP_CUISINE.'"
							LEFT OUTER JOIN '.$this->_sRelLabelTable.' as r3 ON a.'.$this->_sPrimaryKey.'=r3.PlaceID AND r3.ParentLabelID="'.GRP_ATMOS.'"
							LEFT OUTER JOIN '.$this->_sRelLabelTable.' as r4 ON a.'.$this->_sPrimaryKey.'=r4.PlaceID AND r4.ParentLabelID="'.GRP_PRICE.'"
							LEFT OUTER JOIN '.$this->_sRelLabelTable.' as r5 ON a.'.$this->_sPrimaryKey.'=r5.PlaceID AND r5.ParentLabelID="'.GRP_BGNDMUSIC.'"
							LEFT OUTER JOIN '.$this->_sGuideTable.' as r6 ON a.'.$this->_sPrimaryKey.'=r6.PlaceID
							WHERE a.LanguageID='.$this->_nLangID.' ';
		if (!is_null($nCuisine) && !empty($nCuisine))
		{
			$sSQL .=' AND r2.LabelID="'.$nCuisine.'" AND r2.ParentLabelID="'.GRP_CUISINE.'" ';
		}
		if (!is_null($nAtmosphere) && !empty($nAtmosphere))
		{
			$sSQL .=' AND r3.LabelID="'.$nAtmosphere.'" AND r3.ParentLabelID="'.GRP_ATMOS.'" ';
		}
		if (!is_null($nPriceCategory) && !empty($nPriceCategory))
		{
			$sSQL .=' AND r4.LabelID="'.$nPriceCategory.'" AND r4.ParentLabelID="'.GRP_PRICE.'" ';
		}
		if (!is_null($nMusicStyle) && !empty($nMusicStyle))
		{
			$sSQL .=' AND r5.LabelID="'.$nMusicStyle.'" AND r5.ParentLabelID="'.GRP_BGNDMUSIC.'" ';
		}
		if ($bIsNew)
		{
			$nMaxID = $this->GetMaxID();
			$sSQL .= ' AND a.'.$this->_sPrimaryKey.'>="'.($nMaxID-NEWEST_PLACES).'" ';
		}
		if ($bHasEntranceFee)
		{
			$sSQL .= ' AND r6.HasEntranceFee="'.B_TRUE.'" ';
		}
		if ($bHasCardPayment)
		{
			$sSQL .= ' AND r6.HasCardPayment="'.B_TRUE.'" ';
		}
		if ($bHasFaceControl)
		{
			$sSQL .= ' AND r6.HasFaceControl="'.B_TRUE.'" ';
		}
		if ($bHasParking)
		{
			$sSQL .= ' AND r6.HasParking="'.B_TRUE.'" ';
		}
		if ($bHasDJ)
		{
			$sSQL .= ' AND r6.HasDJ="'.B_TRUE.'" ';
		}
		if ($bHasLiveMusic)
		{
			$sSQL .= ' AND r6.HasLiveMusic="'.B_TRUE.'" ';
		}
		if ($bHasKaraoke)
		{
			$sSQL .= ' AND r6.HasKaraoke="'.B_TRUE.'" ';
		}
		if ($bHasBgndMusic)
		{
			$sSQL .= ' AND r6.HasBgndMusic="'.B_TRUE.'" ';
		}
		if ($bHasCuisine)
		{
			$sSQL .= ' AND r6.HasCuisine="'.B_TRUE.'" ';
		}
		if ($bHasTerrace)
		{
			$sSQL .= ' AND r6.HasTerrace="'.B_TRUE.'" ';
		}
		if ($bHasClima)
		{
			$sSQL .= ' AND r6.HasClima="'.B_TRUE.'" ';
		}
		if ($bHasWardrobe)
		{
			$sSQL .= ' AND r6.HasWardrobe="'.B_TRUE.'" ';
		}
		if ($bHasWifi)
		{
			$sSQL .= ' AND r6.HasWifi="'.B_TRUE.'" ';
		}
		if ($bHasDelivery)
		{
			$sSQL .= ' AND r6.HasDelivery="'.B_TRUE.'" ';
		}
		if (!is_null($xParentID))
		{
			if (is_array($xParentID) && count($xParentID) > 0)
				$sSQL .= ' AND PageID IN ('.join(',', $xParentID).')';
			elseif (is_numeric($xParentID) && !empty($xParentID))
				$sSQL .= ' AND PageID="'.$xParentID.'" ';
		}
		if (!is_null($nCityID) && !empty($nCityID))
		{
			$sSQL .=' AND CityID="'.$nCityID.'" ';
		}
		if (!is_null($nPlaceType) && !empty($nPlaceType))
		{
			$sSQL .=' AND PlaceTypeID="'.$nPlaceType.'" ';
		}
		if (!is_null($nPlaceSubtype) && !empty($nPlaceSubtype))
		{
			$sSQL .=' AND PlaceSubtypeID="'.$nPlaceSubtype.'" ';
		}
		if (!empty($sKeyword))
		{
			$sSQL .= ' AND (Title LIKE "%'.$sKeyword.'%" OR
					ShortTitle LIKE "%'.$sKeyword.'%" OR
					MetaKeywords LIKE "%'.$sKeyword.'%" OR
					MetaDescription LIKE "%'.$sKeyword.'%" OR
					Description LIKE "%'.$sKeyword.'%" OR
					WorkingTime LIKE "%'.$sKeyword.'%" OR
					Address LIKE "%'.$sKeyword.'%") ';
		}
		//if (!empty($sLetter))
		if ($xLetter != '')
		{
			if (is_array($xLetter) && count($xLetter) > 0)
			{
				$sSQL .= ' AND Letter IN ("'.join('","', $xLetter).'") ';
			}
			else
				$sSQL .= ' AND Letter="'.$xLetter.'" ';
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND a.IsHidden="'.B_FALSE.'" ';
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
		//echo '<!--$sSQL-->';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $result;
	}

	function ListAllHalls($xParentID=null, $nCityID=null, $nPlaceType=null, $nPlaceSubtype=null, $sKeyword='', $sLetter='', $bShowHidden = false, $nMaxRows=0, $aOrder=null)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT a.*, r2.HallID, r2.Title as HallTitle FROM '.$this->_sTable.' as a
							LEFT OUTER JOIN '.$this->_sRelPageTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.PlaceID
							LEFT OUTER JOIN '.$this->_sHallTable.' as r2 ON a.'.$this->_sPrimaryKey.'=r2.EntityID AND a.LanguageID=r2.LanguageID
							WHERE a.LanguageID='.$this->_nLangID.' ';
		if (!is_null($xParentID))
		{
			if (is_array($xParentID) && count($xParentID) > 0)
				$sSQL .= ' AND PageID IN ('.join(',', $xParentID).')';
			elseif (is_numeric($xParentID) && !empty($xParentID))
				$sSQL .= ' AND PageID="'.$xParentID.'" ';
		}
		if (!is_null($nCityID) && !empty($nCityID))
		{
			$sSQL .=' AND CityID="'.$nCityID.'" ';
		}
		if (!is_null($nPlaceType) && !empty($nPlaceType))
		{
			$sSQL .=' AND PlaceTypeID="'.$nPlaceType.'" ';
		}
		if (!is_null($nPlaceSubtype) && !empty($nPlaceSubtype))
		{
			$sSQL .=' AND PlaceSubtypeID="'.$nPlaceSubtype.'" ';
		}
		if (!empty($sKeyword))
		{
			$sSQL .= ' AND (Title LIKE "%'.$sKeyword.'%" OR
					ShortTitle LIKE "%'.$sKeyword.'%" OR
					MetaKeywords LIKE "%'.$sKeyword.'%" OR
					MetaDescription LIKE "%'.$sKeyword.'%" OR
					Description LIKE "%'.$sKeyword.'%" OR
					WorkingTime LIKE "%'.$sKeyword.'%" OR
					Address LIKE "%'.$sKeyword.'%") ';
		}
		//if (!empty($sLetter))
		if ($sLetter != '')
		{
			$sSQL .= ' AND Letter="'.$sLetter.'" ';
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND a.IsHidden="'.B_FALSE.'" ';
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
		//echo $sSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $result;
	}

	function ListAllHallsAsArray($xParentID=null, $nCityID=null, $nPlaceType=null, $bShowHidden = false)
	{
		$result = $this->ListAllHalls($xParentID, $nCityID, $nPlaceType, null, '', '', $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->PlaceID.IIF(!empty($row->HallID), '-'.$row->HallID, '')] = $row->Title.IIF(!empty($row->HallID), ' - '.$row->HallTitle, '');
		}

		return $aToReturn;
	}

	function ListAllAsArray($xParentID=null, $nCityID=null, $nPlaceType=null, $nPlaceSubtype=null, $sLetter='', $bShowHidden = false)
	{
		$result = $this->ListAll($xParentID, $nCityID, $nPlaceType, $nPlaceSubtype, '', $sLetter, false, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->PlaceID] = $row->Title;
		}

		return $aToReturn;
	}

	function ListByIDs($aPlaceIDs, $bShowHidden = false)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_array($aPlaceIDs) || count($aPlaceIDs) == 0) return false;

		$sSQL = 'SELECT * FROM '.$this->_sTable.'
							WHERE LanguageID='.$this->_nLangID.' AND '.$this->_sPrimaryKey.' IN ('.join(',', $aPlaceIDs).') ';
		if (!$bShowHidden)
		{
			$sSQL .= ' AND IsHidden="'.B_FALSE.'" ';
		}
		$sSQL .=' ORDER BY '.$this->_sDefSortField.' ASC';

		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $result;
	}

	function ListByIDsAsArray($aPlaceIDs, $bShowHidden = false)
	{
		$result = $this->ListByIDs($aPlaceIDs, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->PlaceID] = $row->Title;
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

	function Insert($sName, $sShort, $sKeywords, $sDescription, $sText, $sAddress, $sWorkingTime, $dStartTime, $nCity, $nPlaceType, $bIsHidden=0)//$nPlaceType, $nPlaceSubtype,
	{
		global $aLanguages, $oSession;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$nID = $this->GetMaxID() + 1;
		//$sLetter = substr($sName, 0, 1); // get first letter // Letter="'.$sLetter.'", - finally we restricted the field
		foreach($aLanguages as $key=>$value)
		{
			$sSQL = 'INSERT INTO '.$this->_sTable.' SET
								'.$this->_sPrimaryKey.'="'.$nID.'",
								Title="'.$sName.'",
								Letter="'.$sName.'",
								ShortTitle="'.$sShort.'",
								MetaKeywords="'.$sKeywords.'",
								MetaDescription="'.$sDescription.'",
								Description="'.$sText.'",
								Address="'.$sAddress.'",
								WorkingTime="'.$sWorkingTime.'",
								StartTime="'.$dStartTime.'",
								CityID="'.$nCity.'",
								PlaceTypeID="'.$nPlaceType.'",
								IsHidden="'.$bIsHidden.'",
								LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
								LastUpdate=NOW(),
								LanguageID='.$key.' ';
						//PlaceSubtypeID="'.$nPlaceSubtype.'",
			$result = mysql_query($sSQL, $this->_dbCon);
			dbAssert($result, $sSQL);

			$sSQL = 'INSERT INTO '.$this->_sTable.'Status SET
								ID="'.($nID*2 - 2 + $key).'",
								PlaceID="'.$nID.'",
								ChangeType="added",
								LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
								LastUpdate=NOW() ';
			$result = mysql_query($sSQL, $this->_dbCon);
			dbAssert($result, $sSQL);
		}
		return $nID;
	}

	function Update($nID, $sName, $sShort, $sKeywords, $sDescription, $sText, $sAddress, $sWorkingTime, $dStartTime, $nCity, $nPlaceType, $bIsHidden=0, $nLangID=0)//$nPlaceType, $nPlaceSubtype,
	{
		global $oSession;

		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;

		//$sLetter = substr($sName, 0, 1); // get first letter// Letter="'.$sLetter.'", - finally we restricted the field
		$sSQL = 'UPDATE '.$this->_sTable.' SET
							Title="'.$sName.'",
							Letter="'.$sName.'",
							ShortTitle="'.$sShort.'",
							MetaKeywords="'.$sKeywords.'",
							MetaDescription="'.$sDescription.'",
							Description="'.$sText.'",
							Address="'.$sAddress.'",
							WorkingTime="'.$sWorkingTime.'"
						WHERE LanguageID='.$nLangID.' AND '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		// update lang independent values, if any
		$sSQL = 'UPDATE '.$this->_sTable.' SET
							StartTime="'.$dStartTime.'",
							CityID="'.$nCity.'",
							PlaceTypeID="'.$nPlaceType.'",
							IsHidden="'.$bIsHidden.'",
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
							LastUpdate=NOW()
						WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
						//PlaceSubtypeID="'.$nPlaceSubtype.'",
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		$sSQL = 'INSERT INTO '.$this->_sTable.'Status SET
							ID="'.($nID*2 - 2 + $nLangID).'",
							PlaceID="'.$nID.'",
							ChangeType="updated",
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
							LastUpdate=NOW() ';
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

	function UpdateXY($nID, $long, $lat)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;

		$sSQL = 'UPDATE '.$this->_sTable.' SET
							long="'.$long.'",
							lat="'.$lat.'"
						WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $nID;
	}
	
	function TrackView($nID)
	{
		return $nID; //stop tracking views - by Rumyana
		/*
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;

		$sSQL = 'UPDATE '.$this->_sTable.' SET  NrViews = NrViews + 1
						WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $nID;
		*/
	}

	function Delete($nID)
	{
		global $oSession, $oProgram, $oPlaceGuide, $aLanguages;

		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;

		$sSQL = 'DELETE FROM '.$this->_sTable.'
							WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		// TODO: delete relations if any
		$this->DeletePlacePage($nID);
		$this->DeletePlaceLabel($nID);
		$oProgram->DeleteProgramPlace(0, $nID);
		$oPlaceGuide->DeleteByPlaceID($nID);

		foreach($aLanguages as $key=>$value)
		{
			$sSQL = 'INSERT INTO '.$this->_sTable.'Status SET
								ID="'.($nID*2 - 2 + $key).'",
								PlaceID="'.$nID.'",
								ChangeType="deleted",
								LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
								LastUpdate=NOW() ';
			$result = mysql_query($sSQL, $this->_dbCon);
			dbAssert($result, $sSQL);
		}

		return true;
	}

	function InsertPlacePage($nPlaceID, $xPageID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nPlaceID) && (!is_numeric($xPageID) || !is_array($xPageID))) return false;

		if (is_array($xPageID) && count($xPageID) > 0)
		{
			// prepare list of values: ex: '(1,2),(1,3),(1,4);' where 1 is primaryID, the rest are IDs array
			$sRestSQL = '('.$nPlaceID.','.join('),('.$nPlaceID.',',$xPageID).')';
		}
		elseif (is_numeric($xPageID) && !empty($xPageID))
		{
			$sRestSQL = '('.$nPlaceID.','.$xPageID.')';
		}
		else
			return false;

		$sSQL = 'REPLACE INTO '.$this->_sRelPageTable.' (PlaceID, PageID) VALUES '.$sRestSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return true;
	}

	function DeletePlacePage($nPlaceID=0, $nPageID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nPlaceID) || !is_numeric($nPageID)) return false;
		if (empty($nPlaceID) && empty($nPageID)) return false;

		$sFilter = '';
		if (!empty($nPlaceID))
		{
			$sFilter .= ' AND PlaceID="'.$nPlaceID.'" ';
		}
		if (!empty($nPageID))
		{
			$sFilter .= ' AND PageID="'.$nPageID.'" ';
		}

		$sSQL = 'DELETE FROM '.$this->_sRelPageTable.'
							WHERE 1 '.$sFilter.' ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return true;
	}

	function ListPlacePagesAsArray($nPlaceID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nPlaceID) || empty($nPlaceID)) return false;

		$sSQL = 'SELECT * FROM '.$this->_sRelPageTable.' WHERE
								PlaceID="'.$nPlaceID.'"
								ORDER BY PageID ASC';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[] = $row->PageID;
		}
		return $aToReturn;
	}

	function InsertPlaceLabel($nPlaceID, $nParentLabelID, $xLabelID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nPlaceID) && !is_numeric($nParentLabelID) && (!is_numeric($xLabelID) || !is_array($xLabelID))) return false;

		if (is_array($xLabelID) && count($xLabelID) > 0)
		{
			// prepare list of values: ex: '(1,2),(1,3),(1,4);' where 1 is primaryID, the rest are IDs array
			$sRestSQL = '('.$nPlaceID.','.$nParentLabelID.','.join('),('.$nPlaceID.','.$nParentLabelID.',',$xLabelID).')';
		}
		elseif (is_numeric($xLabelID) && !empty($xLabelID))
		{
			$sRestSQL = '('.$nPlaceID.','.$nParentLabelID.','.$xLabelID.')';
		}
		else
			return false;

		$sSQL = 'REPLACE INTO '.$this->_sRelLabelTable.' (PlaceID, ParentLabelID, LabelID) VALUES '.$sRestSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return true;
	}

	function DeletePlaceLabel($nPlaceID=0, $nParentLabelID=0, $nLabelID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nPlaceID) || !is_numeric($nLabelID)) return false;
		if (empty($nPlaceID) && empty($nLabelID)) return false;

		$sFilter = '';
		if (!empty($nPlaceID))
		{
			$sFilter .= ' AND PlaceID="'.$nPlaceID.'" ';
		}
		if (!empty($nParentLabelID))
		{
			$sFilter .= ' AND ParentLabelID="'.$nParentLabelID.'" ';
		}
		if (!empty($nLabelID))
		{
			$sFilter .= ' AND LabelID="'.$nLabelID.'" ';
		}

		$sSQL = 'DELETE FROM '.$this->_sRelLabelTable.'
							WHERE 1 '.$sFilter.' ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return true;
	}

	function ListPlaceLabelsAsArray($nPlaceID, $nParentLabelID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nPlaceID) || empty($nPlaceID)) return false;

		$sSQL = 'SELECT * FROM '.$this->_sRelLabelTable.' WHERE
								PlaceID="'.$nPlaceID.'" AND ParentLabelID="'.$nParentLabelID.'"
								ORDER BY LabelID ASC';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[] = $row->LabelID;
		}
		return $aToReturn;
	}
}
?>