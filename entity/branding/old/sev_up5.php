<?php

class sev_up5 extends BrandingItem {

	protected $_startDate = '12/31/2011';
	protected $_endDate = '1/3/2012';

	protected function _isSuitable() {
		global $page, $nRootPage;
		
		return true;
	}

	public function display($class) {
		global $nRootPage;
		$rand = mt_rand(1, 100);

		if($nRootPage == 21)
		{
			echo "<body id=\"body\" class=\"".$class."\" style=\"background: url('/img/hbo.jpg') center top no-repeat; background-color: white;\">
				<a class=\"branding\" href=\"http://www.hbo.bg/\" target=\"_blank\" 
				style=\"display: block;
			                 height: 1400px;
							width: 100%;
							position: fixed;
							\">
				<span style=\"display: none;\">hbo</span>
				</a> ";
		
		
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (19, 'hbo', CURDATE(), 1)
		  		ON DUPLICATE KEY UPDATE impressions=impressions+1";
		
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
		}
		elseif($rand <= 15)
		{
			echo "<body id=\"body\" class=\"".$class."\" style=\"background: url('/img/7up.png') center top no-repeat; background-color: white;\">
				<a class=\"branding\" href=\"http://pepsi.bg/\" target=\"_blank\" 
				style=\"display: block;
				                 height: 1400px;
								width: 100%;
								position: fixed;
								\">
				<span style=\"display: none;\">7up</span>
				</a> ";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (12, '7up', CURDATE(), 1)
			  ON DUPLICATE KEY UPDATE impressions=impressions+1";
			
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
		}
		else
		{
			echo "<body id=\"body\" class=\"".$class."\">";			
		}
	}

}

?>