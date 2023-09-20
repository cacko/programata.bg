<?php

class kamenitza extends BrandingItem {

	protected $_startDate = '07/11/2011';
	protected $_endDate = '07/25/2011';

	protected function _isSuitable() {
		global $page, $nRootPage;

		return ($nRootPage == 26);
	}

	public function display() {
			echo "<body id=\"body\" style=\"background: url('/img/kamenitza.jpg') center top no-repeat; background-color: #F3F3F3;\">
			<a href=\"http://www.kamenitzafanclub.com\" target=\"_blank\" 
			style=\"display: block;
			                 height: 1200px;
							width: 100%;
							position: fixed;
							\">
			<span style=\"display: none;\">Каменица</span>
			</a> ";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (5, 'kamenitza', CURDATE(), 1)
			  ON DUPLICATE KEY UPDATE impressions=impressions+1";
			
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
	}

}

?>