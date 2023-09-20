<?php
if (!$bInSite) die();
//=========================================================
    if (!$bIsLocal)
    {
        $nMiddleZone = 0;
        switch($nRootPage)
        {
            case 21: // cinema
                
                if ($page == 29) // premieres
                {
                    // no such zone
                }
                else
                {
                    $nMiddleZone = 38;
                }
                break;
            case 22: // theatre
                $nMiddleZone = 39;
                break;
            case 24: // music
                $nMiddleZone = 40;
                break;
            case 25: // exhibition
                $nMiddleZone = 41;
                break;
            case 26: // clubs & restaurants
                $nMiddleZone = 42;
                break;
            case 27: // outdoor
                $nMiddleZone = 43;
                break;
            case 28: // logos
                $nMiddleZone = 44;
                break;
        }
        if (!empty($nMiddleZone))
        {
?>
<div class="banner468">
    <!--/* OpenX Javascript Tag v2.4.7 */-->
    <script type='text/javascript'><!--//<![CDATA[
       var m3_u = (location.protocol=='https:'?'https://programata.icnhost.net/www/delivery/ajs.php':'http://programata.icnhost.net/www/delivery/ajs.php');
       var m3_r = Math.floor(Math.random()*99999999999);
       if (!document.MAX_used) document.MAX_used = ',';
       document.write ("<scr"+"ipt type='text/javascript' src='"+m3_u);
       document.write ("?zoneid=<?=$nMiddleZone?>");
       document.write ('&amp;cb=' + m3_r);
       if (document.MAX_used != ',') document.write ("&amp;exclude=" + document.MAX_used);
       document.write ("&amp;loc=" + escape(window.location));
       if (document.referrer) document.write ("&amp;referer=" + escape(document.referrer));
       if (document.context) document.write ("&context=" + escape(document.context));
       if (document.mmm_fo) document.write ("&amp;mmm_fo=1");
       document.write ("'><\/scr"+"ipt>");
    //]]>--></script><noscript><a href='http://programata.icnhost.net/www/delivery/ck.php?n=af9f7b2e&amp;cb=<?=rand()?>' target='_blank'><img
        src='http://programata.icnhost.net/www/delivery/avw.php?zoneid=<?=$nMiddleZone?>&amp;cb=<?=rand()?>&amp;n=af9f7b2e' border='0' alt='' /></a></noscript>
</div>
<?      }
    } else { ?>
<div class="banner468">
    <?=drawImage('img/banner/banner_468_60.png')?>
</div>
<?  } ?>