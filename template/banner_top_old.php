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
<div class="banner728" id="banner_top">
    <!--/* OpenX Javascript Tag v2.4.7 */-->
    <script type='text/javascript'><!--//<![CDATA[
       var m3_u = (location.protocol=='https:'?'https://programata.icnhost.net/www/delivery/ajs.php':'http://programata.icnhost.net/www/delivery/ajs.php');
       var m3_r = Math.floor(Math.random()*99999999999);
       if (!document.MAX_used) document.MAX_used = ',';
       document.write ("<scr"+"ipt type='text/javascript' src='"+m3_u);
       document.write ("?zoneid=<?=$nTopZone?>");
       document.write ('&amp;cb=' + m3_r);
       if (document.MAX_used != ',') document.write ("&amp;exclude=" + document.MAX_used);
       document.write ("&amp;loc=" + escape(window.location));
       if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
       if (document.context) document.write ("&context=" + escape(document.context));
       if (document.mmm_fo) document.write ("&amp;mmm_fo=1");
       document.write ("'><\/scr"+"ipt>");
    //]]>--></script><noscript><a href='http://programata.icnhost.net/www/delivery/ck.php?n=af9f7b2e&amp;cb=<?=rand()?>' target='_blank'><img
        src='http://programata.icnhost.net/www/delivery/avw.php?zoneid=<?=$nTopZone?>&amp;cb=<?=rand()?>&amp;n=af9f7b2e' border='0' alt='' /></a></noscript>
</div>
<?  } else { ?>
<div class="banner728" id="banner_top">
    <?=drawImage('img/banner/banner_728_90.png')?>
</div>
<?  } ?>