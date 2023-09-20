<?php
if (!$bInSite) die();
//=========================================================
	#echo '<div id="login">'."\n";
	$nUserID = $oSession->GetValue(SS_USER_ID);
	$nUserStatus = (int) $oSession->GetValue(SS_USER_STATUS);
	if (empty($nUserID))
	{
		showLoginForm($sLoginMsg, $oPage);
	}
	else
	{
		$rUser = $rUser = $oUser->GetByID($nUserID);
//		echo '<h4>'.getLabel('strLogin').'</h4>
//			<div class="box">
//			<p>'.str_replace('%name', $rUser->FirstName.' '.$rUser->LastName, getLabel('strUserWelcome')).'</p>';


		$new_form = '<div id="login_form">
					'. str_replace('%name', $rUser->FirstName.' '.$rUser->LastName, getLabel('strUserWelcome'));

		/*echo '<form action="" method="post" name="logoutfrm" id="logoutfrm">
			<input type="hidden" name="'.ARG_ACT.'" id="'.ARG_ACT.'" value="'.ACT_LOGOUT.'" />
			<input type="submit" id="logout" name="logout" class="btns" value="'.getLabel('strDoLogout').'" />
		</form>';*/
	}
	//$bIsFirst = true;
	if(isset($new_form)){
		$bShowItem = true;
		$rsUserPages = $oPage->ListAll(USERROOT_PAGE);
		if ($rsUserPages)
		{
			//echo '<div class="box">'."\n";
			$new_form .= '<ul>'."\n";
			while($row = mysql_fetch_object($rsUserPages))
			{
				$bShowItem = true;
				// don't show restructed pages when no user
				if (empty($nUserStatus) && $row->RequiredUserStatus > $nUserStatus)
					$bShowItem = false;
				// don't show public pages when user
				if (!empty($nUserStatus) && $row->RequiredUserStatus == USER_GUEST)
					$bShowItem = false;
				if ($bShowItem)
				{
					if (empty($nUserID))
					{
						$new_form .= '<li><a href="'.setPage($row->PageID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.$row->Title.'</a></li>';
						//$bIsFirst = false; IIF(!$bIsFirst, ' | ', '').
					}
					else
					{
						$new_form .= '<li><a href="'.setPage($row->PageID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.$row->Title.'</a></li>';
					}
				}
			}
			$new_form .= '<li><a href="'.setPage(USERROOT_PAGE).'" title="'.getLabel('strQuestions').'">'.getLabel('strQuestions').'</a></li>';
			if (!empty($nUserID))
				$new_form .= '<li><a href="'.setPage($page, $cat, $item, ACT_LOGOUT).'" title="'.getLabel('strLogout').'">'.getLabel('strLogout').'</a></li>';
			$new_form .= '</ul>'."\n";
			#echo null; 
			
			
			echo $new_form .'</div>';
		}
	}
	#echo '</div>'."\n";
//=========================================================
	function showLoginForm($msg, $oPage)
	{
		global $page;
?>
	<script type="text/javascript">
	<!--
	function lCheck(frm)
	{
		if (!valEmpty("username", "<?=getLabel('strEnter').getLabel('strUsername')?>")) return false;
		if (!valEmpty("password", "<?=getLabel('strEnter').getLabel('strPassword')?>")) return false;
		return true;
	}
	//-->
	</script>
	<!--<h4><?=getLabel('strLogin')?></h4>
	<div class="box">
		<p><?=$msg?></p>
		<form action="<?=setPage($page, $cat, $item, ACT_LOGIN)?>" method="post" name="loginfrm" id="loginfrm" onsubmit="return lCheck(this);">
			<input type="hidden" name="<?=ARG_ACT?>" id="<?=ARG_ACT?>" value="<?=ACT_LOGIN?>" />
			<input type="text" name="username" id="username_" maxlength="32" class="fld" value="<?=getLabel('strUsername')?>" onfocus="this.value=''" />
			<input type="password" name="password" id="password_" maxlength="255" class="fld" value="" />
			<div class="remem"><input type="checkbox" class="chk" name="remember" id="remember" value="1" /><label for="remember"><?=getLabel('strRememberMe')?></label></div>
			<input type="submit" value="<?=getLabel('strDoLogin')?>" class="btn" />
		</form>
		<p>&nbsp;</p>-->
	<!--/div-->
    <div id="login_form">
    	<script type="text/javascript">
			var msg = '<?=$msg?>';
			
    		if(msg != ''){
    			alert(msg);
    		}
    	</script>
    	<form action="<?=setPage($page, $cat, $item, ACT_LOGIN)?>" method="post" onsubmit="return lCheck(this);"/>
			<input type="hidden" name="<?=ARG_ACT?>" id="<?=ARG_ACT?>" value="<?=ACT_LOGIN?>" />            	
    		<input type="text" name="username" value="<?=getLabel('strUsername')?>" id="username" onclick="if(this.value == '<?php echo getLabel('strUsername')?>'){ this.value = '';}"/><br />
    		<input type="text" name="password" value="<?=getLabel('strPassword')?>" id="password" onclick="if(this.value == '<?php echo getLabel('strPassword')?>'){ this.value = '';} $(this).replaceWith('<input type=\'password\' name=\'password\' id=\'password\' maxlength=\'32\' class=\'fld\'/>'); $('#password').select();" onfocus="$(this).replaceWith('<input type=\'password\' name=\'password\' id=\'password\' maxlength=\'32\' class=\'fld\'/>'); $('#password').select();"/><br class="clear"/>	
    		<ul>
    			<?php
    				$rsUserPages = $oPage->ListAll(USERROOT_PAGE);
    				while($row = mysql_fetch_object($rsUserPages)){
						if (empty($nUserID) && in_array($row->PageID,array(11,12)))
						{
							echo '<li><a href="'.setPage($row->PageID).'" title="'.htmlspecialchars(strip_tags($row->Title)).'">'.$row->Title.'</a></li>';
						}
    				}         				
    			?>
    		</ul>
       		<input type="submit" value="<?=getLabel('strDoLogin')?>" class="btn" />
    	</form>
    </div>	
<?
	}
//=========================================================
?>
