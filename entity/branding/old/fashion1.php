<?php

class fashion1 extends BrandingItem {

	protected $_startDate = '11/18/2011';
	protected $_endDate = '11/23/2011';

	protected function _isSuitable() {
		global $page, $nRootPage;
		
		$rand = mt_rand(1, 100);
		return ($rand <= 20) ;
	}

	public function display() {
			echo "<body id=\"body\" style=\"background: url('/img/fashion.jpg') center top no-repeat; background-color: white;\">";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (10, 'fashion', CURDATE(), 1)
			  ON DUPLICATE KEY UPDATE impressions=impressions+1";
			
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
	}

}

?>