<?php

class serdika extends BrandingItem {

	protected $_startDate = '11/3/2011';
	protected $_endDate = '11/11/2011';

	protected function _isSuitable() {
		global $page, $nRootPage, $city;
		
		$rand = mt_rand(1, 100);
		return ($city == 1 && $rand <= 60) ;
	}

	public function display() {
			echo "<body id=\"body\" style=\"background: url('/img/serdika.jpg') center top no-repeat; background-color: #5F5F5F;\">
			<a href=\"http://serdikacenter.bg/events/422.aspx\" target=\"_blank\" 
			style=\"display: block;
			                 height: 1400px;
							width: 100%;
							position: fixed;
							\">
			<span style=\"display: none;\">Сердика Център</span>
			</a> ";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (13, 'serdika', CURDATE(), 1)
			  ON DUPLICATE KEY UPDATE impressions=impressions+1";
			
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
	}

}

?>