<?php

class sev_up3 extends BrandingItem {

	protected $_startDate = '12/1/2011';
	protected $_endDate = '12/6/2011';

	protected function _isSuitable() {
		global $page, $nRootPage;
		
		return true;
	}

	public function display() {
		$rand = mt_rand(1, 100);
		if($rand <= 15)
		{
			echo "<body id=\"body\" style=\"background: url('/img/7up.png') center top no-repeat; background-color: white;\">
				<a href=\"http://pepsi.bg/\" target=\"_blank\" 
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
	}

}

?>