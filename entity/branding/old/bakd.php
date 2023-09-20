<?php

class bakd extends BrandingItem {

	protected $_startDate = '11/17/2010';
	protected $_endDate = '01/05/2011';

	protected function _isSuitable() {
		global $page, $nRootPage;

		return true;
	}

	public function display() {
		$rand = mt_rand(1, 100);
		if( $rand <= 5)
			echo "<body id=\"body\" style=\"background: url('/img/bakd.jpg') center top no-repeat; background-color: #F3F3F3;\";>";
		elseif($rand >5 && $rand <= 35) {
			echo "<body id=\"body\" style=\"background: url('/img/nescafe.jpg') center top no-repeat; background-color: #F3F3F3;\";>";
				$sSQL = 'UPDATE impressions SET  impressions = impressions + 1
							WHERE id="1" ';
				$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
				dbAssert($result, $sSQL);

		}
		else
			echo "<body id=\"body\" >";
	}

}

?>