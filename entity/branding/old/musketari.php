<?php

class musketari extends BrandingItem {

	protected $_startDate = '10/12/2011';
	protected $_endDate = '10/28/2011';

	protected function _isSuitable() {
		global $page, $nRootPage;
		
		$rand = mt_rand(1, 100);
		return ($page == 1 && $rand <= 20) ;
	}

	public function display() {
			echo "<body id=\"body\" style=\"background: url('/img/musketari.jpg') center top no-repeat; background-color: #f3f3f3;\">";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (9, 'musketari', CURDATE(), 1)
			  ON DUPLICATE KEY UPDATE impressions=impressions+1";
			
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
	}

}

?>