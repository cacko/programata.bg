<?php

class db_driver {

	public function instance($host, $user, $pass, $db, $type='mysql') {

		static $connections;

		$id = sprintf('%s_%s_%s_%s', $type, $host, $user, $db);
		if(!is_array($connections)) {
			$connections = array();
		}
		if(array_key_exists($id, $connections)) {
			return $connections[$id];
		}
		$class = sprintf('%s_driver', $type);
		$instance = eval(sprintf(" return new %s('%s','%s','%s','%s');", $class, $host, $user, $pass, $db));
		$connections[$id] = $instance;
		return $instance;
	}

}