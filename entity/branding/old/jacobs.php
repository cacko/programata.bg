<?php

class Jacobs extends BrandingItem {

	protected $_startDate = '01/1/2011';
	protected $_endDate = '01/1/2012';

	protected function _isSuitable() {
		global $page, $nRootPage;

		return ($page == 146);
	}

	public function display() {
		echo "<body id=\"body\" style=\"background: url('/img/jacobs.jpg') center top no-repeat; background-color: #E25625;\";>";
	}

}

?>