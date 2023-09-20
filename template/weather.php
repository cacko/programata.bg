<?php
if (!$bInSite) die();
//=========================================================
    $sWeatherTitle = '';
    $gLocationID = '';
    $sTargetDiv = '';
    switch($city)
    {
        case 1:
            $sWeatherTitle = 'Sofia Weather Forecast, Bulgaria';
            $gLocationID = 'BUXX0005';
            $sTargetDiv = 'wx_module_7686';
            break;
        case 2:
            $sWeatherTitle = 'Plovdiv Weather Forecast, Bulgaria';
            $gLocationID = 'BUXX0004';
            $sTargetDiv = 'wx_module_5795';
            break;
        case 3:
            $sWeatherTitle = 'Varna Weather Forecast, Bulgaria';
            $gLocationID = 'BUXX0007';
            $sTargetDiv = 'wx_module_7390';
            break;
        case 4:
            $sWeatherTitle = 'Burgas Weather Forecast, Bulgaria';
            $gLocationID = 'BUXX0001';
            $sTargetDiv = 'wx_module_1395';
            break;
        case 14:
            $sWeatherTitle = 'Stara Zagora Weather Forecast, Bulgaria';
            $gLocationID = 'BUXX0006';
            $sTargetDiv = 'wx_module_9865';
            break;
    }
    
    /* Locations can be edited manually by updating 'wx_locID' below.  Please also update */
    /* the location name and link in the above div (wx_module) to reflect any changes made. */
    //var wx_locID = 'BUXX0005';
    
    /* If you are editing locations manually and are adding multiple modules to one page, each */
    /* module must have a unique div id.  Please append a unique # to the div above, as well */
    /* as the one referenced just below.  If you use the builder to create individual modules  */
    /* you will not need to edit these parameters. */
    //var wx_targetDiv = 'wx_module_7686';
    
    /* Please do not change the configuration value [wx_config] manually - your module */
    /* will no longer function if you do.  If at any time you wish to modify this */
    /* configuration please use the graphical configuration tool found at */
    /* https://registration.weather.com/ursa/wow/step2 */
    //var wx_config='SZ=300x250*WX=FHW*LNK=SSNL*UNT=C*BGI=leaves*MAP=null|null*DN=programata.bg*TIER=0*PID=1036822435*MD5=675e36069258aa521b6d71db4fce30bd';
?>
    <h4><?=getLabel('strWeather')?></h4>
    <div class="weather">
        <div id="<?=$sTargetDiv?>">
            <a href="http://www.weather.com/weather/local/<?=$gLocationID?>"><?=$sWeatherTitle?></a>
        </div>
        
        <script type="text/javascript">
        <!--
        var wx_locID = '<?=$gLocationID?>';
        var wx_targetDiv = '<?=$sTargetDiv?>';
        var wx_config='SZ=300x250*WX=FHW*LNK=SSNL*UNT=C*BGI=leaves*MAP=null|null*DN=programata.bg*TIER=0*PID=1036822435*MD5=675e36069258aa521b6d71db4fce30bd';
        
        document.write('<scr'+'ipt src="'+document.location.protocol+'//wow.weather.com/weather/wow/module/'+wx_locID+'?config='+wx_config+'&proto='+document.location.protocol+'&target='+wx_targetDiv+'"></scr'+'ipt>');  
        //-->
        </script>
    </div>