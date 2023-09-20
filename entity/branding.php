<?php

abstract class BrandingItem {

	protected $_startTimer;
	protected $_endTimer;
	protected $_startDate;
	protected $_endDate;

	public function __construct() {
		$this->_startTimer = new Timer($this->_startDate);
		$this->_endTimer = new Timer($this->_endDate);
	}

	public function isSuitable() {
		if($this->_startTimer->isDone() && !$this->_endTimer->isDone()) {
			return $this->_isSuitable();
		}
		return false;
	}

	protected abstract function _isSuitable();
	public abstract function display($class);

}


class Branding {

	private static $_brandings = null;

	private static function _initBrandings() {
		global $logger;

		$itemsPath  = $_SERVER['DOCUMENT_ROOT'].'/entity/branding';
		foreach(scandir($itemsPath) as $brandingFile) {
			list($class, $ext) = explode('.', $brandingFile, 2);
			if($ext != 'php') {
				continue;
			}
			try {
				require_once($itemsPath.'/'.$brandingFile);
				self::$_brandings[] = new $class();
			} catch(Exception $e) {
				$logger->error($e);
			}
		}
	}

	static function getAvailable() {

		if(!BRANDING_ENABLED) {
			return;
		}

		if(self::$_brandings == null) {
			self::_initBrandings();
		}
		foreach((array) self::$_brandings as $branding) {
			if($branding->isSuitable()) {
				return $branding;
			}
		}
	}

}


?>