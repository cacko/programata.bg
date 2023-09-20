<?php

class hbo extends BrandingItem {

	protected $_startDate = '12/13/2011';
	protected $_endDate = '12/31/2011';

	protected function _isSuitable() {
		global $page, $nRootPage;
		
		return ($nRootPage == 21);
	}

	public function display($class) {
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

}

?>