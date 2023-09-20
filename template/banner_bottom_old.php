<?php
if (!$bInSite) die();
//=========================================================
    if (!$bIsLocal)
    {
        $nLeftZone = 4;
        $nRightZone = 5;
        switch($nRootPage)
        {
            case 21: // cinema
                if ($page == 29) // premieres
                {
                    $nLeftZone = 23;
                    $nRightZone = 31;
                }
                else
                {
                    $nLeftZone = 22;
                    $nRightZone = 30;
                }
                break;
            case 22: // theatre
                $nLeftZone = 24;
                $nRightZone = 32;
                break;
            case 24: // music
                $nLeftZone = 25;
                $nRightZone = 33;
                break;
            case 25: // exhibition
                $nLeftZone = 26;
                $nRightZone = 34;
                break;
            case 26: // clubs & restaurants
                $nLeftZone = 27;
                $nRightZone = 35;
                break;
            case 27: // outdoor
                $nLeftZone = 28;
                $nRightZone = 36;
                break;
            case 28: // logos
                $nLeftZone = 29;
                $nRightZone = 37;
                break;
        }
?>
<div class="banner468double">
    <div id="banner_bottom_1" style="float:left;">
    <!--/* OpenX Javascript Tag v2.4.7 */-->
    <script type='text/javascript'><!--//<![CDATA[
       var m3_u = (location.protocol=='https:'?'https://programata.icnhost.net/www/delivery/ajs.php':'http://programata.icnhost.net/www/delivery/ajs.php');
       var m3_r = Math.floor(Math.random()*99999999999);
       if (!document.MAX_used) document.MAX_used = ',';
       document.write ("<scr"+"ipt type='text/javascript' src='"+m3_u);
       document.write ("?zoneid=<?=$nLeftZone?>");
       document.write ('&amp;cb=' + m3_r);
       if (document.MAX_used != ',') document.write ("&amp;exclude=" + document.MAX_used);
       document.write ("&amp;loc=" + escape(window.location));
       if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
       if (document.context) document.write ("&context=" + escape(document.context));
       if (document.mmm_fo) document.write ("&amp;mmm_fo=1");
       document.write ("'><\/scr"+"ipt>");
    //]]>--></script><noscript><a href='http://programata.icnhost.net/www/delivery/ck.php?n=af9f7b2e&amp;cb=<?=rand()?>' target='_blank'><img
        src='http://programata.icnhost.net/www/delivery/avw.php?zoneid=<?=$nLeftZone?>&amp;cb=<?=rand()?>&amp;n=af9f7b2e' border='0' alt='' /></a></noscript>
    </div>
    <div id="banner_bottom_2" style="float:left; padding-left: 4px;">
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
    <br class="clear" />
</div>
<?  } else { ?>
<div class="banner468double">
    <div id="banner_bottom_1" style="float:left;">
        <?=drawImage('img/banner/banner_468_60.png')?>
    </div>
    <div id="banner_bottom_2" style="float:left; padding-left: 4px;">
        <?=drawImage('img/banner/banner_468_60.png')?>
    </div>
    <br class="clear" />
</div>
<?  } ?>