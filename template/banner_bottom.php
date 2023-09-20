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
    <div class="first" id="banner_bottom_1" >
		<!--/* OpenX iFrame Tag v2.8.7 */-->
		<iframe id='ac80fcf0' name='ac80fcf0' src='http://ads.vkushti.tv/new/www/delivery/afr.php?zoneid=6&amp;cb=INSERT_RANDOM_NUMBER_HERE' frameborder='0' scrolling='no' width='468' height='60'><a href='http://ads.vkushti.tv/new/www/delivery/ck.php?n=af4230ee&amp;cb=INSERT_RANDOM_NUMBER_HERE' target='_blank'><img src='http://ads.vkushti.tv/new/www/delivery/avw.php?zoneid=6&amp;cb=INSERT_RANDOM_NUMBER_HERE&amp;n=af4230ee' border='0' alt='' /></a></iframe>

    </div>
    <div id="banner_bottom_2">
		<!--/* OpenX iFrame Tag v2.8.7 */-->
		<iframe id='afbc38cc' name='afbc38cc' src='http://ads.vkushti.tv/new/www/delivery/afr.php?zoneid=7&amp;cb=INSERT_RANDOM_NUMBER_HERE' frameborder='0' scrolling='no' width='468' height='60'><a href='http://ads.vkushti.tv/new/www/delivery/ck.php?n=ab930aa0&amp;cb=INSERT_RANDOM_NUMBER_HERE' target='_blank'><img src='http://ads.vkushti.tv/new/www/delivery/avw.php?zoneid=7&amp;cb=INSERT_RANDOM_NUMBER_HERE&amp;n=ab930aa0' border='0' alt='' /></a></iframe>

    </div>

<?  } else { ?>

<div class="first" id="banner_bottom_1" >
    <?=drawImage('img/banner/banner_468_60.png')?>
</div>
<div id="banner_bottom_2">
    <?=drawImage('img/banner/banner_468_60.png')?>
</div>
<?  } ?>