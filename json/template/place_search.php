<?php
	$sKeyword = $_REQUEST['keyword'];
	$nSelPage = $_REQUEST['type'];
	$nSelCity = $_REQUEST['city'];
        $rsPlace = $oPlace->ListAllAdvanced($nSelPage, $nSelCity, null, null, $sKeyword, '',
                                                $nCuisine, $nAtmosphere, $nPriceCategory, $nMusicStyle, $bIsNew,
                                                $bHasEntranceFee, $bHasCardPayment, $bHasFaceControl, $bHasParking,
                                                $bHasDJ, $bHasLiveMusic, $bHasKaraoke, null, //$bHasBgndMusic
                                                $bHasCuisine, $bHasTerrace, $bHasClima, $bHasWardrobe, $bHasWifi, $bHasDelivery,
                                                false, 0, $aContext);
		$bInSite = true;
        include('template/place_list.php');

?>