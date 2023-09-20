<?php

class the_mall extends BrandingItem {

	protected $_startDate = '3/15/2012';
	protected $_endDate = '3/23/2012';

	protected function _isSuitable() {
		global $page, $nRootPage;
		
		return true;
	}

	public function display($class) {
		$rand = mt_rand(1, 100);
		if($rand <= 16)
		 {
			echo "
				<body id=\"body\" class=\"".$class."\" style=\"background: url('/img/the_mall.jpg') center top no-repeat; background-color: white;\">
				<a class=\"branding\" href=\"http://www.facebook.com/themall.bg?ref=ts\" target=\"_blank\" 
				style=\"display: block;
				                 height: 1400px;
								width: 100%;
								position: fixed;
								\">
				<span style=\"display: none;\">The mall</span>
				</a> 
				";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (23, 'The mall', CURDATE(), 1)
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