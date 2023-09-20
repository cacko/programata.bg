<?php
if (!$bInSite) die();
//=========================================================
    if (!$bIsLocal)
    {
        $nRightZone = 3;
        switch($nRootPage)
        {
            case 21: // cinema
                /*if ($item == 47406) // sex & the city
                {
                        $nRightZone = 45;
                }
                else*/
                if ($page == 29) // premieres
                {
                    $nRightZone = 15;
                }
                else
                {
                    $nRightZone = 14;
                }
                break;
            case 22: // theatre
                $nRightZone = 16;
                break;
            case 24: // music
                $nRightZone = 17;
                break;
            case 25: // exhibition
                $nRightZone = 21;
                break;
            case 26: // clubs & restaurants
                $nRightZone = 18;
                break;
            case 27: // outdoor
                $nRightZone = 19;
                break;
            case 28: // logos
                $nRightZone = 20;
                break;
        }
?>
<div class="banner300" id="banner_right">
    <!--/* OpenX Javascript Tag v2.4.7 */-->
    <script type='text/javascript'><!--//<![CDATA[
       var m3_u = (location.protocol=='https:'?'https://programata.icnhost.net/www/delivery/ajs.php':'http://programata.icnhost.net/www/delivery/ajs.php');
       var m3_r = Math.floor(Math.random()*99999999999);
       if (!document.MAX_used) document.MAX_used = ',';
       document.write ("<scr"+"ipt type='text/javascript' src='"+m3_u);
       document.write ("?zoneid=<?=$nRightZone?>");
       document.write ('&amp;cb=' + m3_r);
       if (document.MAX_used != ',') document.write ("&amp;exclude=" + document.MAX_used);
       document.write ("&amp;loc=" + escape(window.location));
       if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
       if (document.context) document.write ("&context=" + escape(document.context));
       if (document.mmm_fo) document.write ("&amp;mmm_fo=1");
       document.write ("'><\/scr"+"ipt>");
    //]]>--></script><noscript><a href='http://programata.icnhost.net/www/delivery/ck.php?n=af9f7b2e&amp;cb=<?=rand()?>' target='_blank'><img
        src='http://programata.icnhost.net/www/delivery/avw.php?zoneid=<?=$nRightZone?>&amp;cb=<?=rand()?>&amp;n=af9f7b2e' border='0' alt='' /></a></noscript>
</div>
<?  } else { ?>
<div class="banner300" id="banner_right">
    <?=drawImage('img/banner/banner_300_250.png')?>
</div>
<?  } ?>