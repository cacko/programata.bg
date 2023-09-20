<?php
if (!$bInSite) die();
//=========================================================
	class Session
	{
		function Session($sName, $aVars)
		{
			session_name($sName);
			session_start();
			//session_register($aVars);
			
			//$this->SetDefaults();
		}
		
		function SetValue($key, $value)
		{
			$_SESSION[$key] = $value;
		}
		
		function GetValue($key)
		{
			if (isset($_SESSION[$key]))
				return $_SESSION[$key];
		}
		
		function UnsetValue($key)
		{
			//unset($_SESSION[$key]);
			$_SESSION[$key] = '';
		}
		
		function SetDefaults()
		{
			$aDetails = array();
//			$aDetails[S_DELIVERY_MSG] = '';
//			$_SESSION[SS_DETAILS] = $aDetails;
		}
	}
?>