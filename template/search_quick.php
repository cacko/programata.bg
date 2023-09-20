<?php
if (!$bInSite) die();
?>
<script type="text/javascript">
<!--
function srcCheck(frm)
{
    var check = $('#search-category');
    if (check.val() == '')
    {
        alert("<?=getLabel('strWhere')?>");
        return false;
    }
    return true;
}
//-->
</script>

<form action="<?=setPage($page, $cat, $item, $action, $relitem, $city)?>" method="get" name="search_it" id="search_it" onsubmit="return srcCheck(this);">
    <fieldset>
    <input name="<?=ARG_CITY?>" type="hidden" value="<?=$city;?>" />
    <ul>
        <li class="category">
            <input type="hidden" name="<?=ARG_PAGE?>" id="search-category" value="" />
            <a href="javascript:;" class="input"><span><?=getLabel('strWhichSection')?></span></a>

            <div class="popup" style="display: none;">
                    <div class="list">
                    <a href="javascript:;" rel=""><?=getLabel('strWhichSection')?></a>
                    <?
                        $aPages = $oPage->ListAllAsArraySimple(DEF_PAGE);
                        foreach ($aPages as $key=>$val)
                        {
                            if (!in_array($key, $aSysNavigation))
                            {
                                $aSubPages = $oPage->ListAllAsArraySimple($key, '', 'event_list');
                                if (count($aSubPages)>0)
                                {
                                        echo '<strong>'.$val.'</strong>'."\n";
                                        foreach ($aSubPages as $k=>$v)
                                        {
                                            echo '<a rel="'.$k.'"'.IIF($page==$k, ' class="active"','').'>'.$v.'</a>'."\n";
                                        }
                                }
                                else
                                {
                                        //echo '<option value="'.$key.'"'.IIF($page==$key, ' selected="selected"','').'>'.$val.'</option>'."\n";
                                }
                            }
                        }
                    ?>
                </div>
            </div>
        </li>
		<?
			$hourNow = date('H');
			$dayNow = date('Y-m-d');
			$timeframe = '';

		        if ($hourNow >= 8 and $hourNow < 12) {
		            $timeframe = 1;
		        } else if ($hourNow >= 12 and $hourNow < 16) {
		            $timeframe = 2;
		        } else if ($hourNow >= 16 and $hourNow < 20) {
		            $timeframe = 3;
		        } else if ($hourNow >= 20 and $hourNow < 24) {
		            $timeframe = 4;
		        } else if ($hourNow >= 00 and $hourNow < 8) {
		            $timeframe = 5;
				}
		?>        
        <li class="date-time">
            <input type="hidden" name="sel_date" id="search-date" value="<?=$dayNow?>" />
            <input type="hidden" name="sel_time" id="search-time" value="<?=$timeframe?>" />
            <a href="javascript:;" class="input"><span><?=getLabel('strAnyTime')?></span><em>&nbsp;</em></a>

            <div class="popup" style="display: none;">
                    <div class="list">
                    <a href="javascript:;" rel=""><?=getLabel('strAnyTime')?></a>
                    <?
                        $dSelTime = getQueryArg('sel_time', '');
                        foreach($aTimes as $key=>$val)
                        {
                            echo '<a href="javascript:;" rel="'.$key.'"'.IIF($dSelTime==$key, ' class="active"','').'>'.$val.'</a>'."\n";
                        }
                    ?>
                </div>  
                <div class="picker"></div>                                  
            </div>
        </li>
        <li class="btn">
            <a href="javascript:;"><?=getLabel('strGoSearch')?></a>
        </li>
    </ul>

    <br class="clear" />
</fieldset>
</form>