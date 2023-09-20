<?php
function objectsIntoArray($arrObjData, $arrSkipIndices = array())
{
    $arrData = array();
    
    // if input is object, convert into array
    if (is_object($arrObjData)) {
        $arrObjData = get_object_vars($arrObjData);
    }
    
    if (is_array($arrObjData)) {
        foreach ($arrObjData as $index => $value) {
            if (is_object($value) || is_array($value)) {
                $value = objectsIntoArray($value, $arrSkipIndices); // recursive call
            }
            if (in_array($index, $arrSkipIndices)) {
                continue;
            }
            $arrData[$index] = $value;
        }
    }
    return $arrData;
}

$xmlUrl = "http://kinoarena.com/movies/xml?code=19BF9C7E7579D3A4"; // XML feed file/URL
$xmlStr = file_get_contents($xmlUrl);

var_dump($xmlStr);
die();
$xmlObj = simplexml_load_string($xmlStr);

$arrXml = objectsIntoArray($xmlObj);
print_r($arrXml);

		//         $requestAddress = "http://kinoarena.com/movies/xml?code=19BF9C7E7579D3A4"; 
		// $xml = simplexml_load_file($requestAddress);
		// 
		// var_dump('koko');
		
?>