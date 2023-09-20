<?php

class bakd1 extends BrandingItem {

	protected $_startDate = '04/18/2011';
	protected $_endDate = '06/01/2011';

	protected function _isSuitable() {
		global $page, $nRootPage;

		return ($page != 146);
	}

	public function display() {
		$rand = mt_rand(1, 100);
		if( $rand <= 17)
		{
			echo "<body id=\"body\" style=\"background: url('/img/bakd1.jpg') center top no-repeat; background-color: white;\";>";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (4, 'bakd1', CURDATE(), 1)
			  ON DUPLICATE KEY UPDATE impressions=impressions+1";
			
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
		}
		else
			echo "<body id=\"body\" >";
	}

}

?>