<?php
die("not used any more");

ob_start();
ini_set('error_reporting', E_ALL & ~E_NOTICE);
ini_set('display_errors', 'On');
ini_set('display_startup_errors', 'On');

$bInSite = true;

include_once('../initialize.php');
include_once('../helper/user_action.php');

// Explode text file and store each row of the file into the array elements
function explodeRows($data) {
  $rowsArr = explode("\n", $data);
  return $rowsArr;
}

// Explode the columns according to tabs
function explodeTabs($singleLine) {
  $tabsArr = explode("\t", $singleLine);
  return $tabsArr;
}

// Open the text file and get the content
$filename = "gps.csv";
$handle   = fopen($filename, 'r');
$data     = fread($handle, filesize($filename));
$rowsArr  = explodeRows($data);

global $oPlace;
// Display content which is exploded by regular expression parameters \n and \t
for($i=0;$i<count($rowsArr);$i++) {
  $lineDetails = explodeTabs($rowsArr[$i]);

  $oPlace->UpdateXY($lineDetails[0], $lineDetails[1], $lineDetails[2]);
	//echo $lineDetails[0].'"	"'. $lineDetails[1].'"	"'.$lineDetails[2].'<br />';

}


?>