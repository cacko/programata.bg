<?php

class rezerva extends BrandingItem {

	protected $_startDate = '12/06/2011';
	protected $_endDate = '12/10/2011';

	protected function _isSuitable() {
		global $page, $nRootPage, $city;
		return true;
	}

	public function display() {
		$rand = mt_rand(1, 100);
		if($rand <= 20) 
		{
			echo "<body id=\"body\" style=\"background: url('/img/plesio.jpg') center top no-repeat; background-color: white;\">
			<a href=\"http://plesio.bg/\" target=\"_blank\" 
			style=\"display: block;
			                 height: 1400px;
							width: 100%;
							position: fixed;
							\">
			<span style=\"display: none;\">plesio.bg</span>
			</a> ";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (17, 'plesio', CURDATE(), 1)
			  ON DUPLICATE KEY UPDATE impressions=impressions+1";
			
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
		}
		elseif($rand <= 40) 
		{
			echo "<body id=\"body\" style=\"background: url('/img/rezerva.jpg') center top no-repeat; background-color: black;\">
				<a href=\"http://www.facebook.com/pages/Zagorka/136787956389308?sk=app_299763536716829\" target=\"_blank\" 
				style=\"display: block;
				                 height: 1400px;
								width: 100%;
								position: fixed;
								\">
				<span style=\"display: none;\">Загорка </span>
				</a> ";
			
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (18, 'rezerva', CURDATE(), 1)
			  ON DUPLICATE KEY UPDATE impressions=impressions+1";
			
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
		}
elseif($rands <= 55)
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