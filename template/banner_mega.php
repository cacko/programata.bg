<?php
if (!$bInSite) die();
//=========================================================
    if (!$bIsLocal)
    {
        $nTopZone = 1;
        switch($nRootPage)
        {
            case 21: // cinema
                if ($page == 29) // premieres
                {
                    $nTopZone = 13;
                }
                else
                {
                    $nTopZone = 6;
                }
                break;
            case 22: // theatre
                $nTopZone = 7;
                break;
            case 24: // music
                $nTopZone = 8;
                break;
            case 25: // exhibition
                $nTopZone = 9;
                break;
            case 26: // clubs & restaurants
                $nTopZone = 10;
                break;
            case 27: // outdoor
                $nTopZone = 11;
                break;
            case 28: // logos
                $nTopZone = 12;
                break;
        }
?>
<div class="banner920" id="banner_mega">
	<!--/* OpenX Javascript Tag v2.8.7 */-->

	<!--/*
	  * The backup image section of this tag has been generated for use on a
	  * non-SSL page. If this tag is to be placed on an SSL page, change the
	  *   'http://ads.vkushti.tv/new/www/delivery/...'
	  * to
	  *   'https://ads.vkushti.tv/new/www/delivery/...'
	  *
	  * This noscript section of this tag only shows image banners. There
	  * is no width or height in these banners, so if you want these tags to
	  * allocate space for the ad before it shows, you will need to add this
	  * information to the <img> tag.
	  *
	  * If you do not want to deal with the intricities of the noscript
	  * section, delete the tag (from <noscript>... to </noscript>). On
	  * average, the noscript tag is called from less than 1% of internet
	  * users.
	  */-->

	<script type='text/javascript'><!--//<![CDATA[
	   var m3_u = (location.protocol=='https:'?'https://ads.vkushti.tv/new/www/delivery/ajs.php':'http://ads.vkushti.tv/new/www/delivery/ajs.php');
	   var m3_r = Math.floor(Math.random()*99999999999);
	   if (!document.MAX_used) document.MAX_used = ',';
	   document.write ("<scr"+"ipt type='text/javascript' src='"+m3_u);
	   document.write ("?zoneid=29");
	   document.write ('&amp;cb=' + m3_r);
	   if (document.MAX_used != ',') document.write ("&amp;exclude=" + document.MAX_used);
	   document.write (document.charset ? '&amp;charset='+document.charset : (document.characterSet ? '&amp;charset='+document.characterSet : ''));
	   document.write ("&amp;loc=" + escape(window.location));
	   if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
	   if (document.context) document.write ("&context=" + escape(document.context));
	   if (document.mmm_fo) document.write ("&amp;mmm_fo=1");
	   document.write ("'><\/scr"+"ipt>");
	//]]>--></script><noscript><a href='http://ads.vkushti.tv/new/www/delivery/ck.php?n=adf7aa13&amp;cb=INSERT_RANDOM_NUMBER_HERE' target='_blank'><img src='http://ads.vkushti.tv/new/www/delivery/avw.php?zoneid=29&amp;cb=INSERT_RANDOM_NUMBER_HERE&amp;n=adf7aa13' border='0' alt='' /></a></noscript>


</div>
<?  } ?>