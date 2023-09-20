<?php

class fortisimo extends BrandingItem {

	protected $_startDate = '10/18/2011';
	protected $_endDate = '10/26/2011';

	protected function _isSuitable() {
		global $page, $nRootPage;

		return ($nRootPage == 24);
	}

	public function display() {
		$rand = mt_rand(1, 100);
		if( $rand <= 10)
		{
			echo "<body id=\"body\" style=\"background: url('/img/fortisimo.jpg') center top no-repeat; background-color: black;\">
			<a href=\"http://www.fortissimofest.com\" target=\"_blank\" 
			style=\"display: block;
			                 height: 800px;
							width: 100%;
							position: fixed;
							\">
			<span style=\"display: none;\">Фортисимо</span>
			</a> ";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (7, 'fortisimo', CURDATE(), 1)
			  ON DUPLICATE KEY UPDATE impressions=impressions+1";
			
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
		}
		else
			echo "<body id=\"body\" >";
	}

}

?>