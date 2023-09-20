<?php

class blizoo extends BrandingItem {

	protected $_startDate = '02/11/2011';
	protected $_endDate = '02/19/2011';

	protected function _isSuitable() {
		global $page, $nRootPage;

		return ($page != 146);
	}

	public function display() {
		$rand = mt_rand(1, 100);
		if( $rand <= 25)
		{
			echo "<body id=\"body\" style=\"background: url('/img/blizoo.jpg') center top no-repeat; background-color: white;\";>";
				$sSQL = 'UPDATE impressions SET  impressions = impressions + 1
							WHERE id="3" ';
				$result = mysql_query($sSQL, @mysql_connect(DB_HOST, DB_USER, DB_PASS));
				dbAssert($result, $sSQL);

		}
		else
			echo "<body id=\"body\" >";
	}

}

?>