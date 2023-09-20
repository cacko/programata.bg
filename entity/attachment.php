<?php
if (!$bInSite) die();
//=========================================================
class Attachment
{
	var $_sTable = null;
	var $_dbCon = null;
	var $_nLangID = null;
	var $_sPrimaryKey = null;
	var $_sDefSortField = null;
	var $_aFields = null;

	function Attachment($dbCon, $nLangID)
	{
		$this->_sTable = DB_TABLE_PREFIX.'tblAttachment';
		$this->_dbCon = $dbCon;
		$this->_nLangID = $nLangID;

		$this->_aFields = array();

		$this->_aFields[1] = 'AttachmentID';
		$this->_aFields[2] = 'AttachmentTypeID';
		$this->_aFields[3] = 'Filename';
		$this->_aFields[4] = 'Title';
		$this->_aFields[5] = 'ContentType';
		$this->_aFields[6] = 'EntityID';
		$this->_aFields[7] = 'EntityTypeID';
		$this->_aFields[8] = 'IsHidden';
		$this->_aFields[9] = 'LastUpdate';
		$this->_aFields[10] = 'LastUpdateUserID';
		$this->_aFields[11] = 'LanguageID';
		$this->_aFields[12] = 'Extension';

		$this->_sPrimaryKey = $this->_aFields[1];
		$this->_sDefSortField = $this->_aFields[2];
	}

	function ListAll($nEntityID=null, $nEntityTypeID=null, $xAttachmentTypeID=null, $bShowHidden = false, $aOrder=null)
	{
		global $aSortDirections;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$sSQL = 'SELECT * FROM '.$this->_sTable.' WHERE LanguageID='.$this->_nLangID.' ';
		if (!is_null($nEntityID) && !empty($nEntityID))
		{
			$sSQL .=' AND EntityID="'.$nEntityID.'" ';
		}
		if (!is_null($nEntityTypeID) && !empty($nEntityTypeID))
		{
			$sSQL .=' AND EntityTypeID="'.$nEntityTypeID.'" ';
		}
		if (!is_null($xAttachmentTypeID))
		{
			if (is_array($xAttachmentTypeID) && count($xAttachmentTypeID) > 0)
				$sSQL .= ' AND AttachmentTypeID IN ('.join(',', $xAttachmentTypeID).')';
			elseif (is_numeric($xAttachmentTypeID) && !empty($xAttachmentTypeID))
				$sSQL .= ' AND AttachmentTypeID="'.$xAttachmentTypeID.'" ';
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

	function ListAllAsArray($nEntityID=null, $nEntityTypeID=null, $nAttachmentTypeID=null, $bShowHidden = false)
	{
		$result = $this->ListAll($nEntityID, $nEntityTypeID, $nAttachmentTypeID, $bShowHidden);
		$aToReturn = array();
		while($row = mysql_fetch_object($result))
		{
			$aToReturn[$row->AttachmentID] = $row->Title;
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

	function Insert($nAttachmentTypeID, $sName, $sFilename, $sContentType, $nEntityID, $nEntityTypeID, $bIsHidden=0)
	{
		global $aLanguages, $oSession;

		foreach(func_get_args() as $xParam) stripParam($xParam);

		$nID = $this->GetMaxID() + 1;
		$sExtension = getFileExt($sFilename);
		foreach($aLanguages as $key=>$value)
		{
			$sSQL = 'INSERT INTO '.$this->_sTable.' SET
								'.$this->_sPrimaryKey.'="'.$nID.'",
								AttachmentTypeID="'.$nAttachmentTypeID.'",
								Filename="'.$sFilename.'",
								Title="'.$sName.'",
								ContentType="'.$sContentType.'",
								Extension="'.$sExtension.'",
								EntityID="'.$nEntityID.'",
								EntityTypeID="'.$nEntityTypeID.'",
								IsHidden="'.$bIsHidden.'",
								LastUpdateUserID="'.$oSession->GetValue(SS_USER_ID).'",
								LastUpdate=NOW(),
								LanguageID='.$key.' ';
			$result = mysql_query($sSQL, $this->_dbCon);
			dbAssert($result, $sSQL);
		}
		return $nID;
	}

	function Update($nID, $nAttachmentTypeID, $sName, $sFilename, $sContentType, $nEntityID, $nEntityTypeID, $bIsHidden=0, $nLangID=0)
	{
		global $oSession;

		foreach(func_get_args() as $xParam) stripParam($xParam);
		if (empty($nID) || !is_numeric($nID)) return false;
		if (empty($nLangID)) $nLangID = $this->_nLangID;

		$sExtension = getFileExt($sFilename);

		$sSQL = 'UPDATE '.$this->_sTable.' SET
							Title="'.$sName.'"
						WHERE LanguageID='.$nLangID.' AND '.$this->_sPrimaryKey.'="'.$nID.'" ';
		$result = mysql_query($sSQL, $this->_dbCon);
		dbAssert($result, $sSQL);

		// update lang independent values, if any
		$sSQL = 'UPDATE '.$this->_sTable.' SET
							AttachmentTypeID="'.$nAttachmentTypeID.'",
							'.IIF(!empty($sFilename), 'Filename="'.$sFilename.'",', '').'
							'.IIF(!empty($sContentType), 'ContentType="'.$sContentType.'",', '').'
							'.IIF(!empty($sExtension), 'Extension="'.$sExtension.'",', '').'
							EntityID="'.$nEntityID.'",
							EntityTypeID="'.$nEntityTypeID.'",
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
}
?>