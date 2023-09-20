<?php
if (!$bInSite) die();
//=========================================================
include_once("gd/ImageResizeFactory.php");
//=========================================================
function deleteFiles($itemID, $sExt, $targetFolder='')
{
	global $aImageSizes;

	if (empty($targetFolder))
		$targetFolder = UPLOAD_DIR_ABS;
	if (!is_writable($targetFolder))
	{
		return ERR_NO_FOLDER_ACCESS;
	}
	foreach (array_keys($aImageSizes) as $tail)
	{
		$uploadfile = $targetFolder.$itemID.$tail.'.'.$sExt;
		if (is_file($uploadfile))
			@unlink($uploadfile);
	}
}
//=========================================================
function deleteFile($itemID, $sExt, $targetFolder='')
{
	if (empty($targetFolder))
		$targetFolder = UPLOAD_DIR_ABS;
	if (!is_writable($targetFolder))
	{
		return ERR_NO_FOLDER_ACCESS;
	}
	$uploadfile = $targetFolder.$itemID.'.'.$sExt;
	@unlink($uploadfile);
}
//=========================================================
function duplicateFile($srcFilename, $destFilename, $sourceFolder='', $targetFolder='')
{
	if (empty($sourceFolder))
		$sourceFolder = UPLOAD_DIR_ABS;
	if (empty($targetFolder))
		$targetFolder = UPLOAD_DIR_ABS;
	$srcFile = $sourceFolder.$srcFilename;
	$destFile = $targetFolder.$destFilename;
	if (is_file($srcFile)) // this doesn't work on win32
	{
		copy($srcFile, $destFile);
		chmod($destFile, 0644);
	}
}
//=========================================================
function uploadBrowsedFile($sFieldName, $targetFileName, $sSaveExt='', $targetFolder='', $sDefaultImage='')
{
	

	if (empty($targetFolder))
		$targetFolder = UPLOAD_DIR_ABS;
	if (!is_writable($targetFolder))
	{
		//chmod ($targetFolder, 0777);
		return ERR_NO_FOLDER_ACCESS;
	}

	$uploadfile = $targetFolder.$targetFileName.'.'.$sSaveExt;
	if (!is_uploaded_file($_FILES[$sFieldName]['tmp_name']))
	{
		if (!is_file($uploadfile) && !empty($sDefaultImage))
		{
			copy($targetFolder.$sDefaultImage, $uploadfile);
			chmod($uploadfile, 0644);
		}
		return ERR_NO_FILE;
	}
	$ext = strtolower(substr($_FILES[$sFieldName]['name'], -3, 3)) ;
	if (!empty($sSaveExt) && $sSaveExt != $ext)
	{
		if (!is_file($uploadfile) && !empty($sDefaultImage))
		{
			copy($targetFolder.$sDefaultImage, $uploadfile);
			chmod($uploadfile, 0644);
		}
		return ERR_WRONG_FILETYPE;
	}
	$uploadfile = $targetFolder.$targetFileName.'.'.$ext;
	// delete old file if exists
	if (is_file($uploadfile)) @unlink($uploadfile);

	if (move_uploaded_file($_FILES[$sFieldName]['tmp_name'], $uploadfile))
	{
		chmod($uploadfile, 0644);
		return ERR_NONE;
	}
	else
	{
		return ERR_FILE_ATTACK;
	}
}
//=========================================================
function resizeImage($filename, $nWid, $nHei, $targetFolder='')
{
	if (empty($targetFolder))
		$targetFolder = UPLOAD_DIR_ABS;
	$srcFile = $targetFolder.$filename;
	$destFile = $targetFolder.'new_'.$filename;

	// do not resize if smaller than the min sizes
	$aSize = getimagesize($srcFile);
	if (($aSize[0] > $nWid && $aSize[0] > $nHei) || ($aSize[1] > $nWid && $aSize[1] > $nHei))
	{
		$boolValidExt = false;
		$allowedExtensions = array("jpg", "JPG", "JPEG", "png", "PNG", "gif", "GIF");
		$extension = pathinfo($srcFile);
		$extension = $extension["extension"];
		foreach($allowedExtensions as $key=>$ext)
		{
			if(strcasecmp($ext, $extension) == 0)
			{
				$boolValidExt = true;
				break;
			}
		}
		if($boolValidExt)
		{
			// Instantiate the correct object depending on type of image i.e jpg or png
			$objResize = ImageResizeFactory::getInstanceOf($srcFile, $destFile, $nWid, $nHei);
			// Call the method to resize the image
			$objResize->getResizedImage();
			unlink($srcFile);
			unset($objResize);
			//header("Location:" . $destFile);
			//exit;
		}
		if (is_file($destFile))
		{
			if (is_file($srcFile)) @unlink($srcFile);
			copy($destFile, $srcFile);
			chmod($srcFile, 0644);
			@unlink($destFile);
		}
	}
}
//=========================================================
function getFileExt($sFileName)
{
	$extention = strtolower(strrev($sFileName));
	$pos = strpos($extention, '.');
	$extention = substr($extention, 0, $pos);
	return strrev($extention);
}
//=========================================================
function showGallery($nEntityID, $nEntityType, $bShowThumbs = true, $bShowAdmin = false, $studiox_new = false)
{
	global $oAttachment, $aEntityTypes, $page;

	$strToReturn = '';
	//$rs = $oGallery->ListByEntity($nEntityID, $aEntityTypes[$nEntityType]);
	$rs = $oAttachment->ListAll($nEntityID, $aEntityTypes[$nEntityType], 4);
	if (mysql_num_rows($rs))
	{
		$sLinks = '';
		$sTitles = '';
		$sAuthors = '';
		$sEditLinks = '';
		$sDeleteLinks = '';
		$sImages = '';
		$sImgNr = 0;
		$list_of_images = null;
		while ($row = mysql_fetch_object($rs))
		{
			
			if($studiox_new){
				$sFilename = UPLOAD_DIR.IMG_GALLERY.$row->AttachmentID.'.'.$row->Extension;
				$list_of_images .= '<li><a href="'. $sFilename .'" title="'. $row->Title .'">'. drawImage($sFilename, 138, 93, $row->Title) .'</a></li>';
//				$list_of_images = '<li><img src="'.IIF($bShowAdmin, '../','').$sFilename.'?'.rand().'" alt="'. $row->Title .'"/></li>';
			}else{
				$sImgNr++;
				$sThumbFilename = '';//UPLOAD_DIR.IMG_THUMB.$row->GalleryID.'.'.$row->Extension;
				$sLinks .= '<li '.IIF($sImgNr==1,' class="on"','').' id="a'.$row->AttachmentID.'"><a href="#" onclick="showImg(\''.$row->AttachmentID.'\');return false;" title="'.$row->Title.'"><span class="a">'.IIF($bShowThumbs, '<img src="'.IIF($bShowAdmin, '../','').$sThumbFilename.'?'.rand().'" alt="'.$row->Title.'" />', $sImgNr).'</span></a></li>'."\n";
				$sFilename = UPLOAD_DIR.IMG_GALLERY.$row->AttachmentID.'.'.$row->Extension;
				$sImages .= '<img '.IIF($bShowThumbs, ' class="midthumb"', '').IIF($sImgNr>1,' style="display:none"','').' id="i'.$row->AttachmentID.'" src="'.IIF($bShowAdmin, '../','').$sFilename.'?'.rand().'" alt="'.$row->Title.'" />'."\n";
	
				$sTitles .= '<span '.IIF($sImgNr>1,' style="display:none"','').' id="tt'.$row->AttachmentID.'">'.$row->Title.'</span>'."\n";
				//$sAuthors .= '<span '.IIF($sImgNr>1,' style="display:none"','').' id="au'.$row->AttachmentID.'">'.IIF(!empty($row->Author),'&copy; ','').$row->Author.'</span>'."\n";
	
				if ($bShowAdmin)
				{
					if (!empty($sDeleteLinks))
					{
						$sEditLinks .= ' | ';
						$sDeleteLinks .= ' | ';
					}
					else
					{
						$sEditLinks .= '<br /><br />';
						$sDeleteLinks .= '<br /><br />';
					}
					$sEditLinks .= '<a href="'.setPage($page, 0, $nEntityID, ACT_EDIT_IMG, $row->AttachmentID).'">'.getLabel('edit').' '.$sImgNr.'</a>'."\n";
					$sDeleteLinks .= '<a href="'.setPage($page, 0, $nEntityID, ACT_DELETE_IMG, $row->AttachmentID).'" onclick="return confMsg(\''.getLabel('strDeleteQ').'\');return false;">'.getLabel('delete').' '.$sImgNr.'</a>'."\n";
				}
			}
		}
		
		if($studiox_new){
			return $list_of_images;
		}else{
			$strToReturn .= '<div id="gallery">';
			if ($bShowThumbs)
				$strToReturn .= '<ul id="nums" class="thumblist">'.$sLinks.'</ul>';
			else if($sImgNr > 1)
				$strToReturn .= '<ul id="nums">'.$sLinks.'</ul>';
			$strToReturn .= $sImages.'<div id="info">'.$sTitles.'</div>
							<div id="auth">'.$sAuthors.'</div>'.
							$sEditLinks.$sDeleteLinks.
						'</div>';
		}
	}
	return $strToReturn;
}
//=========================================================
function drawFlash($sImageFile, $nWidth=0, $nHeight=0, $sAltImageFile, $sTitle='', $sImageID='')
{
	if (!empty($sImageFile))
		return '<object type="application/x-shockwave-flash" width="'.$nWidth.'" height="'.$nHeight.'" align="middle" '.IIF(!empty($sImageID), ' id="o'.$sImageID.'"', '').' title="'.htmlspecialchars(strip_tags($sTitle)).'" data="'.$sImageFile.'">
		<param name="movie" value="'.$sImageFile.'" />
		<param name="quality" value="high" />
		<param name="wmode" value="transparent" />
		<img src="'.$sAltImageFile.'" '.IIF(!empty($sImageID), ' id="'.$sImageID.'"', '').IIF(!empty($nWidth), ' width="'.$nWidth.'"', '').IIF(!empty($nHeight), ' height="'.$nHeight.'"', '').' alt="'.htmlspecialchars(strip_tags($sTitle)).'" /></object>';
}
//=========================================================
function drawImage($sImageFile, $nWidth=0, $nHeight=0, $sTitle='', $sImageID='', $sClassName='', $studiox_return_link = false)
{
	global $logger;

	if (!empty($sImageFile))
		// replace src="/ with src="
		if($studiox_return_link){
			return 'http://'. SITE_URL .'/'. $sImageFile;
		}else{
			return '<img src="'.$sImageFile.'?'.rand().'" '.
					IIF(!empty($sImageID), ' id="'.$sImageID.'"', '').
					IIF(!empty($sClassName), ' class="'.$sClassName.'"', '').
					IIF(!empty($nWidth), ' width="'.$nWidth.'"', '').
					IIF(!empty($nHeight), ' height="'.$nHeight.'"', '').
					' title="'.htmlspecialchars(strip_tags($sTitle)).
					'" alt="'.htmlspecialchars(strip_tags($sTitle)).'" />';
		}
}
//=========================================================
function drawMovie($sImageFile, $nWidth=0, $nHeight=0, $sTitle='')
{
	if (!empty($sImageFile))
		return '<object type="video/quicktime" classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B"
	width="'.$nWidth.'" height="'.($nHeight+15).'" align="left"
	codebase="http://www.apple.com/qtactivex/qtplugin.cab"
	title="'.htmlspecialchars(strip_tags($sTitle)).'">
<param name="src" value="'.$sImageFile.'" />
<param name="autoplay" value="false" />
<param name="controller" value="true" />
<param name="cache" value="true" />
<param name="bgcolor" value="White" />
<param name="scale" value="1" />
<param name="type" value="video/quicktime" />
<embed
	src="'.$sImageFile.'"
	title="'.htmlspecialchars(strip_tags($sTitle)).'"
	width="'.$nWidth.'" height="'.($nHeight+15).'" align="left"
	autostart="false" controller="true"
	plugin="quicktimeplugin"
	pluginspage="http://www.apple.com/quicktime/download/"
	type="video/quicktime">
</embed>
</object>';
}
//=========================================================
?>