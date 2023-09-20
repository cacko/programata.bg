<?php

class guide extends BrandingItem {

	protected $_startDate = '11/16/2011';
	protected $_endDate = '11/23/2011';

	protected function _isSuitable() {
		global $page, $nRootPage;
		
		return ($nRootPage == 26);
	}

	public function display($class) {
			echo "<body id=\"body\" class=\"".$class."\" style=\"background: url('/img/guide.jpg') center top no-repeat; background-color: #edf5fa;\">
			<a class=\"branding\" href=\"http://programata.bg/img/gallery/file_30967.pdf\" target=\"_blank\" 
			style=\"display: block;
			                 height: 1400px;
							width: 100%;
							position: fixed;
							\">
			<span style=\"display: none;\">Guide 2012</span>
			</a> ";
			
			$sSQL = "INSERT INTO impressions (id,name,day, impressions) VALUES (16, 'guide2012', CURDATE(), 1)
			  ON DUPLICATE KEY UPDATE impressions=impressions+1";
			
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
	}

}

?>