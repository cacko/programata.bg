<?php

class fashion extends BrandingItem {

	protected $_startDate = '10/21/2011';
	protected $_endDate = '10/26/2011';

	protected function _isSuitable() {
		global $page, $nRootPage;
		
		$rand = mt_rand(1, 100);
		return ($rand <= 20) ;
	}

	public function display() {
			echo "<body id=\"body\" style=\"background: url('/img/fashion.jpg') center top no-repeat; background-color: white;\">
				<a href=\"http://clk.tradedoubler.com/click?p=191500&a=2023432&g=19995958\" target=\"_blank\" 
				style=\"display: block;
				                 height: 1400px;
								width: 100%;
								position: fixed;
								\">
				<span style=\"display: none;\">fashion</span>
				</a> ";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (10, 'fashion', CURDATE(), 1)
			  ON DUPLICATE KEY UPDATE impressions=impressions+1";
			
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
	}

}

?>