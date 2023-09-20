<?php
if (!$bInSite) die();
//=========================================================
	$aFilter = array(ARG_RELID, ARG_CAT);
	
	$rs = $oAttachment->ListAll(getRequestArg('entity_id', $relitem), getRequestArg('entity_type', $cat), null, true, $aContext);
	if (mysql_num_rows($rs))
	{
?>
<table summary="data" class="grid">
<thead>
<tr>
	<td><?=getLabel('strAttachment')?></td>
	<!--td><a href="<?=setPageContext(setContext(1,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strAttachmentID')?></a></td-->
	<td><a href="<?=setPageContext(setContext(4,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strAttachmentTitle')?></a></td>
	<td><a href="<?=setPageContext(setContext(2,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strAttachmentType')?></a></td>
	<!--td><a href="<?=setPageContext(setContext(9,$aContext[CUR_REC])).setFilterParam($aFilter)?>"><?=getLabel('strLastUpdate')?></a></td-->
	<td></td>
</tr>
</thead>
<tbody>
<?
		$aYesNo = getLabel('aYesNo');
		$aUsers = $oUser->ListAllAsArray(USER_ADMIN);
		$aAttachmentTypes = getLabel('aAttachmentTypes');
		
		$curRow = 0;
		$nTotalRows = mysql_num_rows($rs);
		$nStartRow = $aContext[CUR_REC];
		$nEndRow = $nStartRow + NUM_ROWS_ADMIN;
		while ($row = mysql_fetch_object($rs))
		{
			if ($curRow >= $nStartRow && $curRow < $nEndRow)
			{
				$bIsImage = true;
				switch($row->AttachmentTypeID)
				{
					case 1:
						$sMainImageFile = '../'.FILEBANK_DIR.IMG_SMALL.$row->AttachmentID.'.'.$row->Extension;
						break;
					case 2:
						$sMainImageFile = '../'.FILEBANK_DIR.IMG_SMALL.$row->AttachmentID.'.'.$row->Extension;
						break;
					case 3:
						$sMainImageFile = '../'.UPLOAD_DIR.IMG_GALLERY.$row->AttachmentID.'.'.$row->Extension;
						break;
					case 4:
						$sMainImageFile = '../'.UPLOAD_DIR.IMG_GALLERY.$row->AttachmentID.'.'.$row->Extension;
						break;/**/
					case 5:
						$sMainImageFile = '../'.UPLOAD_DIR.FILE_PANORAMA.$row->AttachmentID.'.'.$row->Extension;
						$bIsImage = false;
						break;
					case 6:
						$sMainImageFile = '../'.UPLOAD_DIR.FILE_ATTACHMENT.$row->AttachmentID.'.'.$row->Extension;
						$bIsImage = false;
						break;
					case 7:
						$sMainImageFile = '../'.UPLOAD_DIR.FILE_TRAILER.$row->AttachmentID.'.'.$row->Extension;
						$bIsImage = false;
						break;
					
				}
				if (!is_file($sMainImageFile))
				{
					$sMainImageFile = '../'.UPLOAD_DIR.IMG_THUMB.'.'.EXT_PNG;
				}
?>
<tr class="<?=IIF($curRow%2==0, 'odd', 'even').IIF($row->IsHidden == true, ' hidden', '')?>" onmouseover="lt(this,1)" onmouseout="lt(this,0)">
	<td><?
		if ($bIsImage)
			echo drawImage($sMainImageFile, 0, H_IMG_THUMB, $row->Title);
		else
			echo '<a href="'.$sMainImageFile.'" target="_blank">'.$row->Title.'</a>';
	?></td>
	<!--td><?=$row->AttachmentID?></td-->
	<td><?=$row->Title?></td>
	<td><?=$aAttachmentTypes[$row->AttachmentTypeID]?></td>
	<!--td><?=$row->LastUpdate?><br /><?=getLabel('strByUser').$aUsers[$row->LastUpdateUserID]?></td-->
	<td><a href="<?=setPage($page, 0, $row->AttachmentID, ACT_VIEW).keepFilter().keepContext()?>"><?=getLabel('view')?></a> | <a href="<?=setPage($page, 0, $row->AttachmentID, ACT_EDIT).keepFilter().keepContext()?>"><?=getLabel('edit')?></a> | <a href="<?=setPage($page, 0, $row->AttachmentID, ACT_DELETE).keepFilter().keepContext()?>" onclick="return confMsg('<?=getLabel('strDeleteQ')?>');return false;"><?=getLabel('delete')?></a></td>
</tr>
<?
			}
			$curRow++;
		}
?>
</tbody>
<tfoot>
<tr>
	<td colspan="2"><?=showPaging($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_ADMIN, $aFilter);?></td>
	<td colspan="2" align="right"><?=showPages($nStartRow, $nEndRow, $nTotalRows, NUM_ROWS_ADMIN, $aFilter);?></td>
</tr>
</tfoot>
</table>
<?
	}
	else
	{
?>
<div><?=getLabel('strNoRecords')?></div>
<?
	}
?>