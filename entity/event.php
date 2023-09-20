<?php
if (!$bInSite) die();
//=========================================================
class Event
{
	var $_sTable = null;
	var $_sRelPageTable = null;
	var $_sRelLabelTable = null;
	var $_sProgramTable = null;
	var $_sRelProgramPageTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;

	function Event($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblEvent';
		$this->_sRelPageTable = DB_TABLE_PREFIX.'relEvent2Page';
		$this->_sRelLabelTable = DB_TABLE_PREFIX.'relEvent2Label';
		$this->_sProgramTable = DB_TABLE_PREFIX.'tblProgram';
		$this->_sRelProgramPageTable = DB_TABLE_PREFIX.'relProgram2Page';

		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;

		$this->_aFields = array();
		$this->_aFields[1] = 'EventID';
		$this->_aFields[2] = 'Title';
		$this->_aFields[3] = 'Letter';
		$this->_aFields[4] = 'OriginalTitle';
		$this->_aFields[5] = 'MetaKeywords';
		$this->_aFields[6] = 'MetaDescription';
		$this->_aFields[7] = 'Lead';
		$this->_aFields[8] = 'Description';
		$this->_aFields[9] = 'Features';
		$this->_aFields[10] = 'Comment';
		$this->_aFields[12] = 'LastUpdate';
		$this->_aFields[13] = 'LastUpdateUserID';
		$this->_aFields[14] = 'IsHidden';
		$this->_aFields[15] = 'LanguageID';
		$this->_aFields[16] = 'NrViews';
		$this->_aFields[17] = 'EventTypeID';
		$this->_aFields[18] = 'EventSubtypeID';

		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[2];
	}

	function ListAll($xParentID=null, $nEventType=null, $nEventSubtype=null, $sKeyword='', $xLetter='', $bShowHidden = false, $nMaxRows=0, $aOrder=null)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT DISTINCT a.* FROM '.$this->_sTable.' as a
							LEFT OUTER JOIN '.$this->_sRelPageTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.EventID
							WHERE a.LanguageID='.$this->_nLangID.' ';
		if (!is_null($xParentID))
		{
			if (is_array($xParentID) && count($xParentID) > 0)
				$sSQL .= ' AND PageID IN ('.join(',', $xParentID).')';
			elseif (is_numeric($xParentID) && !empty($xParentID))
				$sSQL .= ' AND PageID="'.$xParentID.'" ';
		}
		if (!is_null($nEventType) && !empty($nEventType))
		{
			$sSQL .=' AND EventTypeID="'.$nEventType.'" ';
		}
		if (!is_null($nEventSubtype) && !empty($nEventSubtype))
		{
			$sSQL .=' AND EventSubtypeID="'.$nEventSubtype.'" ';
		}
		if (!empty($sKeyword))
		{
			$sSQL .= ' AND (Title LIKE "%'.$sKeyword.'%" OR
					OriginalTitle LIKE "%'.$sKeyword.'%" OR
					MetaKeywords LIKE "%'.$sKeyword.'%" OR
					MetaDescription LIKE "%'.$sKeyword.'%" OR
					Lead LIKE "%'.$sKeyword.'%" OR
					Description LIKE "%'.$sKeyword.'%" OR
					Features LIKE "%'.$sKeyword.'%" OR
					Comment LIKE "%'.$sKeyword.'%") ';
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

	function ListAllAdvanced($xParentID=null, $nEventType=null, $nEventSubtype=null, $sKeyword='', $xLetter='',
				 $nMusicStyle, $nGenre, $nGenreGroup, $nOrigLanguage, $nTranslation,
				 $bShowHidden = false, $nMaxRows=0, $aOrder=null)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT DISTINCT a.* FROM '.$this->_sTable.' as a
							LEFT OUTER JOIN '.$this->_sRelPageTable.' as r1 ON a.'.$this->_sPrimaryKey.'=r1.EventID
							LEFT OUTER JOIN '.$this->_sRelLabelTable.' as r2 ON a.'.$this->_sPrimaryKey.'=r2.EventID AND r2.ParentLabelID="'.GRP_MUSIC.'"
							LEFT OUTER JOIN '.$this->_sRelLabelTable.' as r3 ON a.'.$this->_sPrimaryKey.'=r3.EventID AND r3.ParentLabelID="'.$nGenreGroup.'"
							LEFT OUTER JOIN '.$this->_sRelLabelTable.' as r4 ON a.'.$this->_sPrimaryKey.'=r4.EventID AND r4.ParentLabelID="'.GRP_LANG.'"
							LEFT OUTER JOIN '.$this->_sRelLabelTable.' as r5 ON a.'.$this->_sPrimaryKey.'=r5.EventID AND r5.ParentLabelID="'.GRP_TRANS.'"
							WHERE a.LanguageID='.$this->_nLangID.' ';
		if (!is_null($nMusicStyle) && !empty($nMusicStyle))
		{
			$sSQL .=' AND r2.LabelID="'.$nMusicStyle.'" AND r2.ParentLabelID="'.GRP_MUSIC.'" ';
		}
		if (!is_null($nGenre) && !empty($nGenre))
		{
			$sSQL .=' AND r3.LabelID="'.$nGenre.'" AND r3.ParentLabelID="'.$nGenreGroup.'" ';
		}
		if (!is_null($nOrigLanguage) && !empty($nOrigLanguage))
		{
			$sSQL .=' AND r4.LabelID="'.$nOrigLanguage.'" AND r4.ParentLabelID="'.GRP_LANG.'" ';
		}
		if (!is_null($nTranslation) && !empty($nTranslation))
		{
			$sSQL .=' AND r5.LabelID="'.$nTranslation.'" AND r5.ParentLabelID="'.GRP_TRANS.'" ';
		}
		if (!is_null($xParentID))
		{
			if (is_array($xParentID) && count($xParentID) > 0)
				$sSQL .= ' AND PageID IN ('.join(',', $xParentID).')';
			elseif (is_numeric($xParentID) && !empty($xParentID))
				$sSQL .= ' AND PageID="'.$xParentID.'" ';
		}
		if (!is_null($nEventType) && !empty($nEventType))
		{
			$sSQL .=' AND EventTypeID="'.$nEventType.'" ';
		}
		if (!is_null($nEventSubtype) && !empty($nEventSubtype))
		{
			$sSQL .=' AND EventSubtypeID="'.$nEventSubtype.'" ';
		}
		if (!empty($sKeyword))
		{
			$sSQL .= ' AND (Title LIKE "%'.$sKeyword.'%" OR
					OriginalTitle LIKE "%'.$sKeyword.'%" OR
					MetaKeywords LIKE "%'.$sKeyword.'%" OR
					MetaDescription LIKE "%'.$sKeyword.'%" OR
					Lead LIKE "%'.$sKeyword.'%" OR
					Description LIKE "%'.$sKeyword.'%" OR
					Features LIKE "%'.$sKeyword.'%" OR
					Comment LIKE "%'.$sKeyword.'%") ';
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

	function ListAllAsArray($xParentID=null, $nEventType=null, $nEventSubtype=null, $sLetter='', $bShowHidden = false)
	{
		$result = $this->ListAll($xParentID, $nEventType, $nEventSubtype, '', $sLetter, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->EventID] = $row->Title;
		}

		return $aToReturn;
	}

	function ListAllAsArrayPlain($nEventType=null, $nEventSubtype=null, $bShowHidden = false)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT '.$this->_sPrimaryKey.', Title FROM '.$this->_sTable.'
							WHERE LanguageID='.$this->_nLangID.' ';
		if (!is_null($nEventType) && !empty($nEventType))
		{
			$sSQL .=' AND EventTypeID="'.$nEventType.'" ';
		}
		if (!is_null($nEventSubtype) && !empty($nEventSubtype))
		{
			$sSQL .=' AND EventSubtypeID="'.$nEventSubtype.'" ';
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND IsHidden="'.B_FALSE.'" ';
		}
		//order
		$sSQL .=' ORDER BY '.$this->_sDefSortField.' ASC, '.$this->_sPrimaryKey.' DESC';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->EventID] = $row->Title;
		}

		return $aToReturn;
	}

	function ListByIDs($aEventIDs, $bShowHidden = false)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_array($aEventIDs) || count($aEventIDs) == 0) return false;

		$sSQL = 'SELECT * FROM '.$this->_sTable.'
							WHERE LanguageID='.$this->_nLangID.' AND '.$this->_sPrimaryKey.' IN ('.join(',', $aEventIDs).') ';
		if (!$bShowHidden)
		{
			$sSQL .= ' AND IsHidden="'.B_FALSE.'" ';
		}
		$sSQL .=' ORDER BY '.$this->_sDefSortField.' ASC';

		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return $result;
	}

	function ListByIDsAsArray($aEventIDs, $bShowHidden = false)
	{
		$result = $this->ListByIDs($aEventIDs, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->EventID] = $row->Title;
		}

		return $aToReturn;
	}

	function ListByName($sKeyword, $nEventType=null, $bShowHidden = false)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT * FROM '.$this->_sTable.'
							WHERE (Title LIKE "%'.$sKeyword.'%" OR
									OriginalTitle LIKE "%'.$sKeyword.'%")';
		if (!is_null($nEventType) && !empty($nEventType))
		{
			$sSQL .=' AND EventTypeID="'.$nEventType.'" ';
		}
		if (!$bShowHidden)
		{
			$sSQL .= ' AND IsHidden="'.B_FALSE.'" ';
		}
		$sSQL .=' ORDER BY '.$this->_sDefSortField.' ASC';

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

	function Insert($sName, $sOriginalTitle, $sKeywords, $sDescription, $sText, $sLead, $sFeatures, $sComment,
			$nEventType, $nEventSubtype, $nOrigLanguage, $nTranslation, $bIsHidden=0)
	{
		global $aLanguages, $oSession;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$nID = $this->GetMaxID() + 1;
		//$sLetter = substr($sName, 0, 1); // get first letter //Letter="'.$sLetter.'", - finally we restricted the field
		foreach($aLanguages as $key=>$value)
		{
			$sSQL = 'INSERT INTO '.$this->_sTable.' SET
								'.$this->_sPrimaryKey.'="'.$nID.'",
								Title="'.$sName.'",
								Letter="'.$sName.'",
								OriginalTitle="'.$sOriginalTitle.'",
								MetaKeywords="'.$sKeywords.'",
								MetaDescription="'.$sDescription.'",
								Lead="'.$sLead.'",
								Description="'.$sText.'",
								Comment="'.$sComment.'",
								Features="'.$sFeatures.'",
								EventTypeID="'.$nEventType.'",
								EventSubtypeID="'.$nEventSubtype.'",
								OriginalLanguageID="'.$nOrigLanguage.'",
								TranslationID="'.$nTranslation.'",
								IsHidden="'.$bIsHidden.'",
								LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
								LastUpdate=NOW(),
								LanguageID='.$key.' ';
			$result = mysql_query($sSQL, $this->_dbCon);
			dbAssert($result, $sSQL);

			$sSQL = 'INSERT INTO '.$this->_sTable.'Status SET
								ID="'.($nID*2 - 2 + $key).'",
								EventID="'.$nID.'",
								ChangeType="added",
								LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
								LastUpdate=NOW() ';
			$result = mysql_query($sSQL, $this->_dbCon);
			dbAssert($result, $sSQL);
		}

		return $nID;
	}

	function Update($nID, $sName, $sOriginalTitle, $sKeywords, $sDescription, $sText, $sLead, $sFeatures, $sComment,
			$nEventType, $nEventSubtype, $nOrigLanguage, $nTranslation, $bIsHidden=0, $nLangID=0)
	{
		global $oSession;

		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;

		//$sLetter = substr($sName, 0, 1); // get first letter //Letter="'.$sLetter.'", - finally we restricted the field
		$sSQL = 'UPDATE '.$this->_sTable.' SET
							Title="'.$sName.'",
							Letter="'.$sName.'",
							OriginalTitle="'.$sOriginalTitle.'",
							MetaKeywords="'.$sKeywords.'",
							MetaDescription="'.$sDescription.'",
							Lead="'.$sLead.'",
							Description="'.$sText.'",
							Comment="'.$sComment.'",
							Features="'.$sFeatures.'"
						WHERE LanguageID='.$nLangID.' AND '.$this->_sPrimaryKey.'="'.$nID.'" ';

		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		// update lang independent values, if any
		$sSQL = 'UPDATE '.$this->_sTable.' SET
							EventTypeID="'.$nEventType.'",
							EventSubtypeID="'.$nEventSubtype.'",
							OriginalLanguageID="'.$nOrigLanguage.'",
							TranslationID="'.$nTranslation.'",
							IsHidden="'.$bIsHidden.'",
							LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
							LastUpdate=NOW()
						WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		$sSQL = 'INSERT INTO '.$this->_sTable.'Status SET
							ID="'.($nID*2 - 2 + $nLangID).'",
							EventID="'.$nID.'",
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
		global $oSession, $oProgram, $aLanguages;

		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;

		$sSQL = 'DELETE FROM '.$this->_sTable.'
							WHERE '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);
		// TODO: delete relations if any
		$this->DeleteEventPage($nID);
		$this->DeleteEventLabel($nID);
		$oProgram->DeleteProgramEvent(0, $nID);

		foreach($aLanguages as $key=>$value)
		{
			$sSQL = 'INSERT INTO '.$this->_sTable.'Status SET
								ID="'.($nID*2 - 2 + $key).'",
								EventID="'.$nID.'",
								ChangeType="deleted",
								LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
								LastUpdate=NOW() ';
			$result = mysql_query($sSQL, $this->_dbCon);
			dbAssert($result, $sSQL);
		}

		return true;
	}

	function InsertEventPage($nEventID, $xPageID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nEventID) && (!is_numeric($xPageID) || !is_array($xPageID))) return false;

		if (is_array($xPageID) && count($xPageID) > 0)
		{
			// prepare list of values: ex: '(1,2),(1,3),(1,4);' where 1 is primaryID, the rest are IDs array
			$sRestSQL = '('.$nEventID.','.join('),('.$nEventID.',',$xPageID).')';
		}
		elseif (is_numeric($xPageID) && !empty($xPageID))
		{
			$sRestSQL = '('.$nEventID.','.$xPageID.')';
		}
		else
			return false;

		$sSQL = 'REPLACE INTO '.$this->_sRelPageTable.' (EventID, PageID) VALUES '.$sRestSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return true;
	}

	function DeleteEventPage($nEventID=0, $nPageID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nEventID) || !is_numeric($nPageID)) return false;
		if (empty($nEventID) && empty($nPageID)) return false;

		$sFilter = '';
		if (!empty($nEventID))
		{
			$sFilter .= ' AND EventID="'.$nEventID.'" ';
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

	function ListEventPagesAsArray($nEventID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nEventID) || empty($nEventID)) return false;

		$sSQL = 'SELECT * FROM '.$this->_sRelPageTable.' WHERE
								EventID="'.$nEventID.'"
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

	function _ListEventPagesAsArray($nEventID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nEventID) || empty($nEventID)) return false;

		$sSQL = 'SELECT DISTINCT a.PageID FROM '.$this->_sRelProgramPageTable.' as a
				LEFT OUTER JOIN '.$this->_sProgramTable.' as r1 ON a.ProgramID = r1.ProgramID
								WHERE
								EventID="'.$nEventID.'"
								ORDER BY a.PageID ASC';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[] = $row->PageID;
		}
		return $aToReturn;
	}

	function InsertEventLabel($nEventID, $nParentLabelID, $xLabelID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nEventID) && !is_numeric($nParentLabelID) && (!is_numeric($xLabelID) || !is_array($xLabelID))) return false;

		if (is_array($xLabelID) && count($xLabelID) > 0)
		{
			// prepare list of values: ex: '(1,2),(1,3),(1,4);' where 1 is primaryID, the rest are IDs array
			$sRestSQL = '('.$nEventID.','.$nParentLabelID.','.join('),('.$nEventID.','.$nParentLabelID.',',$xLabelID).')';
		}
		elseif (is_numeric($xLabelID) && !empty($xLabelID))
		{
			$sRestSQL = '('.$nEventID.','.$nParentLabelID.','.$xLabelID.')';
		}
		else
			return false;

		$sSQL = 'REPLACE INTO '.$this->_sRelLabelTable.' (EventID, ParentLabelID, LabelID) VALUES '.$sRestSQL;
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		return true;
	}

	function DeleteEventLabel($nEventID=0, $nParentLabelID=0, $nLabelID=0)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nEventID) || !is_numeric($nLabelID)) return false;
		if (empty($nEventID) && empty($nLabelID)) return false;

		$sFilter = '';
		if (!empty($nEventID))
		{
			$sFilter .= ' AND EventID="'.$nEventID.'" ';
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

	function ListEventLabelsAsArray($nEventID, $nParentLabelID)
	{
		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (!is_numeric($nEventID) || empty($nEventID)) return false;

		$sSQL = 'SELECT * FROM '.$this->_sRelLabelTable.' WHERE
								EventID="'.$nEventID.'" AND ParentLabelID="'.$nParentLabelID.'"
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