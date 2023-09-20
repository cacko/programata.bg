<?php

class spirit extends BrandingItem {

	protected $_startDate = '07/25/2011';
	protected $_endDate = '08/12/2011';

	protected function _isSuitable() {
		global $page, $nRootPage;
		
		$rand = mt_rand(1, 100);
		return ($nRootPage == 24 || ($page == 1  && $rand <= 20)) ;
	}

	public function display() {
			echo "<body id=\"body\" style=\"background: url('/img/spirit.jpg') center top no-repeat; background-color: white;\">
			<a href=\"http://spiritofburgas.com/\" target=\"_blank\" 
			style=\"display: block;
			                 height: 1400px;
							width: 100%;
							position: fixed;
							\">
			<span style=\"display: none;\">Spirit of Burgas</span>
			</a> ";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (6, 'spirit', CURDATE(), 1)
			  ON DUPLICATE KEY UPDATE impressions=impressions+1";
			
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
	}

}

?>