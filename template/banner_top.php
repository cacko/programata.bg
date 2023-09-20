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
<!--/* OpenX iFrame Tag v2.8.7 */-->

<!--/*
  * This tag has been generated for use on a non-SSL page. If this tag
  * is to be placed on an SSL page, change the
  *   'http://ads.vkushti.tv/new/www/delivery/...'
  * to
  *   'https://ads.vkushti.tv/new/www/delivery/...'
  *
  * The backup image section of this tag has been generated for use on a
  * non-SSL page. If this tag is to be placed on an SSL page, change the
  *   'http://ads.vkushti.tv/new/www/delivery/...'
  * to
  *   'https://ads.vkushti.tv/new/www/delivery/...'
  *
  * If iFrames are not supported by the viewer's browser, then this
  * tag only shows image banners. There is no width or height in these
  * banners, so if you want these tags to allocate space for the ad
  * before it shows, you will need to add this information to the <img>
  * tag.
  */-->

<iframe id='a17ebb08' name='a17ebb08' src='http://ads.vkushti.tv/new/www/delivery/afr.php?zoneid=4&amp;cb=INSERT_RANDOM_NUMBER_HERE' frameborder='0' scrolling='no' width='728' height='90'><a href='http://ads.vkushti.tv/new/www/delivery/ck.php?n=ad7bf652&amp;cb=INSERT_RANDOM_NUMBER_HERE' target='_blank'><img src='http://ads.vkushti.tv/new/www/delivery/avw.php?zoneid=4&amp;cb=INSERT_RANDOM_NUMBER_HERE&amp;n=ad7bf652' border='0' alt='' /></a></iframe>

</div>
<?  } else { ?>
<div class="banner728" id="banner_top">
    <?=drawImage('img/banner/banner_728_90.png')?>
</div>
<?  } ?>