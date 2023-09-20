<?php

class Zagorka extends BrandingItem {

	protected $_startDate = '06/1/2010';
	protected $_endDate = '06/12/2010';

	protected function _isSuitable() {
		global $page, $nRootPage;

		return ($page == 21 || $nRootPage == 21);
	}

	public function display() {
		echo "<body id=\"body\" style=\"background: url('/img/zagorka".rand(1, 3).".jpg') center top no-repeat; background-color: black;\";>";
	}

}

?>