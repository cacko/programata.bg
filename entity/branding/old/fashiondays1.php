<?php

class fashiondays1 extends BrandingItem {

	protected $_startDate = '3/5/2012';
	protected $_endDate = '3/6/2012';

	protected function _isSuitable() {
		global $page, $nRootPage;
		
		return true;
	}

	public function display($class) {
		$rand = mt_rand(1, 100);

		if($rand <= 20)
		{
			echo "
				<body id=\"body\" class=\"".$class."\" style=\"background: url('/img/traviata.jpg') center top no-repeat; background-color: white;\">
				<a class=\"branding\" href=\"http://www.ljubkabiagioni.de\" target=\"_blank\" 
				style=\"display: block;
				                 height: 1400px;
								width: 100%;
								position: fixed;
								\">
				<span style=\"display: none;\">La Traviata</span>
				</a> 
				";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (22, 'La Traviata', CURDATE(), 1)
			  ON DUPLICATE KEY UPDATE impressions=impressions+1";
			
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
			
		}
		elseif($rand <= 40)
		 {
			echo "
				<body id=\"body\" class=\"".$class."\" style=\"background: url('/img/fashiondays.jpg') center top no-repeat; background-color: white;\">
				<a class=\"branding\" href=\"http://clk.tradedoubler.com/click?p=191500&a=2023432&g=20304424\" target=\"_blank\" 
				style=\"display: block;
				                 height: 1400px;
								width: 100%;
								position: fixed;
								\">
				<span style=\"display: none;\">Fashion Days</span>
				</a> 
				";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (21, 'FashionDays', CURDATE(), 1)
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