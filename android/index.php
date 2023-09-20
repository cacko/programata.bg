<?php
/**
 * file name
 */
if(mb_stripos($_SERVER['HTTP_USER_AGENT'], "Android") !== false){
    header("Location: market://details?id=com.programata.bg");
} elseif(mb_stripos($_SERVER['HTTP_USER_AGENT'], "iPhone") !== false || mb_stripos($_SERVER['HTTP_USER_AGENT'], "iPod") !== false){
    header("Location: programata://".$_SERVER['REQUEST_URI']);
} else { //for devices which are not Android
    header("Location: http://programata.bg/?p=177&l=1&c=1&id=69858"); //zabito e da se opravi v posledstvie
}
?>