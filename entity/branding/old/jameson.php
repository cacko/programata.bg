<?php

class Jameson extends BrandingItem {

	protected $_startDate = '01/10/2011';
	protected $_endDate = '02/01/2011';

	protected function _isSuitable() {
		global $page, $nRootPage;

		return ($page == 21 || $nRootPage == 21);
	}

	public function display() {
		echo "<body id=\"body\" style=\"background: url('/img/diss_bg.jpg') center top no-repeat; background-color: #CACBCF;\";>";

		$sSQL = 'UPDATE impressions SET  impressions = impressions + 1
						WHERE id="2" ';
			$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
			dbAssert($result, $sSQL);
	}

}

?>