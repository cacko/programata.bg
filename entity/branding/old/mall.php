<?php

class Mall extends BrandingItem {

	protected $_startDate = '09/20/2010';
	protected $_endDate = '09/25/2010';

	protected function _isSuitable() {
		global $page, $nRootPage;

		return true;
	}

	public function display() {
		echo "<body id=\"body\" style=\"background: url('/img/mall.jpg') center top no-repeat; background-color: white;\";>";
	}

}

?>