<?php

$location = 'http://programata.bg';

switch(true) {

	case preg_match('/Android/', $_SERVER['HTTP_USER_AGENT']):
		$location = 'market://details?id=com.programata.bg';
		break;
		
	case preg_match('/(iPhone|iPod)/', $_SERVER['HTTP_USER_AGENT']):
		$location = 'http://itunes.apple.com/bg/app/programata-mobile/id484011220?mt=8';
		break;
	
}

header("Location: {$location}");

?>