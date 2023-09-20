<?php

class absolut extends BrandingItem {

	protected $_startDate = '06/03/2011';
	protected $_endDate = '06/22/2011';

	protected function _isSuitable() {
		global $page, $nRootPage;

		return ($page != 146);
	}

	public function display() {
		$rand = mt_rand(1, 100);
		if( $rand <= 10)
		{
			echo "<body id=\"body\" style=\"background: url('/img/absolut.jpg') center top no-repeat; background-color: black;\";>";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (5, 'absolut', CURDATE(), 1)
			  ON DUPLICATE KEY UPDATE impressions=impressions+1";
			
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
		}
		else
			echo "<body id=\"body\" >";
	}

}

?>