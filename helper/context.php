<?php
if (!$bInSite) die();
//=========================================================
// sets filter parameters to the link
function setFilterParam($_aParam)
{
	$strFilter = '';
	if (!is_null($_aParam) && count($_aParam) > 0)
	{
		foreach($_aParam as $key)
		{
			$value = getPostedArg($key);
			if (empty($value))
				$value = getQueryArg($key);
			if (!empty($value))
			{
				$strFilter .= '&amp;'.$key.'='.$value;
			}
		}
	}
	return $strFilter;
}
//=========================================================
// preserve filter parameters for links
function keepFilter()
{
	global $aFilter;
	
	return setFilterParam($aFilter);
}
//=========================================================
// used in column titles - preserves page context setting new order rule
function setPageContext($_order)
{
	$strPage = setPage(getQueryArg(ARG_PAGE), getQueryArg(ARG_CAT), getQueryArg(ARG_ID));
	if (isset($_order) && !empty($_order))
		$strPage .= '&amp;'.ARG_ORD.'='.$_order;
	return $strPage;
}
//=========================================================
// prepares qs to reverse the order of the current column
function setContext($_sortfield=0, $_currecord=0)
{
	global $aContext;
	
	if ($aContext[CUR_REC] == $_currecord)
	{
		if ($aContext[SORT_FIELD] == $_sortfield)
		{
			if($aContext[SORT_DIR] == ORD_ASC)
			{
				$_sortdir = ORD_DESC;
			}
			else
			{
				$_sortdir = ORD_ASC;
			}
		}
		else
		{
			$_sortdir = ORD_ASC;
		}
	}
	else
	{
		$_sortdir = $aContext[SORT_DIR];
		$_sortfield = $aContext[SORT_FIELD];
	}
	$_curpage=0;
	 
	$strToReturn = $_sortfield.','.$_sortdir.','.$_curpage.','.$_currecord;
	return $strToReturn;
}
//=========================================================
// keeps query context to the current link
function keepContext()
{
	global $aContext;
	
	$_sortdir = $aContext[SORT_DIR];
	$_sortfield = $aContext[SORT_FIELD];
	$_currecord = $aContext[CUR_REC];
	$_curpage=0;
	 
	$strToReturn = '&amp;'.ARG_ORD.'='.$_sortfield.','.$_sortdir.','.$_curpage.','.$_currecord;
	return $strToReturn;
}
//=========================================================
// gets query context
function getContext()
{
	global $aSortDirections;
	
	$order = getQueryArg(ARG_ORD,'');
	$aToReturn = array();
	if (!empty($order))
	{
		$aToReturn = explode(',',$order);
	}
	if (count($aToReturn) == 0)
	{
		$aToReturn[SORT_FIELD] = 0;
		$aToReturn[SORT_DIR] = ORD_ASC;
		$aToReturn[CUR_PAGE] = 0;
		$aToReturn[CUR_REC] = 0;
	}
	else
	{
		if (!is_numeric($aToReturn[SORT_FIELD]))
		{
			$aToReturn[SORT_FIELD] = 0;
		}
		if (!is_numeric($aToReturn[SORT_DIR]) || !in_array($aToReturn[SORT_DIR], array_keys($aSortDirections)))
		{
			$aToReturn[SORT_DIR] = ORD_ASC;
		}
		if (!is_numeric($aToReturn[CUR_PAGE]))
		{
			$aToReturn[CUR_PAGE] = 0;
		}
		if (!is_numeric($aToReturn[CUR_REC]))
		{
			$aToReturn[CUR_REC] = 0;
		}
	}
	return $aToReturn;
}
//=========================================================
function showPages($nStartRecord, $nEndRecord, $nTotalRecords, $nPageSize, $aFilter=null)
{
	global $aContext;
	
	#$strToReturn = getLabel('strGoToPage');//.' |';
	$strToReturn = '';
	$nPages = ceil($nTotalRecords/$nPageSize);
	$aRange = array();
	//if ($nStartRecord == 0)
	//	$aRange = range(0, +(NUM_PAGES_PAGING-1));
	//else if($nEndRecord >= $nTotalRecords)
	//	$aRange = range(-(NUM_PAGES_PAGING-1), 0);
	//else
		$aRange = range(-(floor(NUM_PAGES_PAGING/2)), +(floor(NUM_PAGES_PAGING/2)));
	$aPagesToShow = array(1, $nPages);
	foreach($aRange as $n)
	{
		$aPagesToShow[] = ($nStartRecord/$nPageSize) + $n +1;
	}
	
	for ($i=0; $i<$nPages; $i++)
	{
		$nCurrentPageNr = $i+1;
		$bDoPrint = false;
		$nPageStartRecord = $i*$nPageSize;
		
		if ($nPages > NUM_PAGES_PAGING)
		{
			if(in_array($nCurrentPageNr, $aPagesToShow))
				$bDoPrint = true;
		}
		else
		{
			$bDoPrint = true;
		}
		
		if($bDoPrint)
		{
			if ($nStartRecord == $nPageStartRecord)
			{
				$strToReturn .= '<strong>'.$nCurrentPageNr.'</strong>';
			}
			else
			{
				$strToReturn .= '<a href="'.setPageContext(setContext($aContext[SORT_FIELD], $nPageStartRecord)).setFilterParam($aFilter).'"><span class="a">'.$nCurrentPageNr.'</span></a>';
			}
		}
		else
		{
			$sLastChar = substr($strToReturn, (strlen($strToReturn)-1));
			if ($sLastChar == '')
			{
				$strToReturn = substr($strToReturn, 0, (strlen($strToReturn)-1));
				$strToReturn .= '...';
			}
		}
	}
	$sLastChar = substr($strToReturn, (strlen($strToReturn)-1));
	if ($sLastChar == ' ')
	{
		$strToReturn = substr($strToReturn, 0, (strlen($strToReturn)-1));
	}
	return $strToReturn;
}
//=========================================================
function showPaging($nStartRecord, $nEndRecord, $nTotalRecords, $nPageSize, $aFilter=null)
{
	global $aContext;
	
	$strToReturn = '';
	// prev page
	if ($nStartRecord > 0)
	{
		$nPrevPageStartRecord = $nStartRecord - $nPageSize;
		$strToReturn .= '<a href="'.setPageContext(setContext($aContext[SORT_FIELD], $nPrevPageStartRecord)).setFilterParam($aFilter).'">'.getLabel('strPrev').'</a> | ';
	}
	// cur page stats
	$sRecords = getLabel('strRecords');
	$sRecords = str_replace('%1', ($nStartRecord+1), $sRecords);
	$sRecords = str_replace('%2', IIF($nEndRecord>$nTotalRecords, $nTotalRecords, $nEndRecord), $sRecords);
	$sRecords = str_replace('%3', $nTotalRecords, $sRecords);
	$strToReturn .= $sRecords;
	// next page
	if ($nEndRecord < $nTotalRecords)
	{
		$strToReturn .= ' | <a href="'.setPageContext(setContext($aContext[SORT_FIELD], $nEndRecord)).setFilterParam($aFilter).'">'.getLabel('strNext').'</a>';
	}
	return $strToReturn;
}
//=========================================================
function showPrev($nStartRecord, $nEndRecord, $nTotalRecords, $nPageSize, $aFilter=null)
{
	global $aContext;
	
	$strToReturn = '';
	if ($nStartRecord > 0)
	{
		$nPrevPageStartRecord = $nStartRecord - $nPageSize;
		$strToReturn .= '<a href="'.setPageContext(setContext($aContext[SORT_FIELD], $nPrevPageStartRecord)).setFilterParam($aFilter).'" title="'.getLabel('strPrev').'"><img src="img/arrow_left.png" class="arr1" alt="'.getLabel('strPrev').'" /></a>';
	}
	else
		$strToReturn .= '<img src="img/pix.png" class="arr1" alt="" />';
	return $strToReturn;
}
//=========================================================
function showNext($nStartRecord, $nEndRecord, $nTotalRecords, $nPageSize, $aFilter=null)
{
	global $aContext;
	
	$strToReturn = '';
	if ($nEndRecord < $nTotalRecords)
	{
		$strToReturn .= '<a href="'.setPageContext(setContext($aContext[SORT_FIELD], $nEndRecord)).setFilterParam($aFilter).'" title="'.getLabel('strNext').'"><img src="img/arrow_right.png" class="arr2" alt="'.getLabel('strNext').'" /></a>';
	}
	else
		$strToReturn .= '<img src="img/pix.png" class="arr2" alt="" />';
	return $strToReturn;
}
//=========================================================
?>