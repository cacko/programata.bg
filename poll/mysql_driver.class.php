<?php
require_once('db.interface.php');
class mysql_driver extends mysqli implements dbInterface{

	function __construct($host, $user, $pass, $db) {
		parent::__construct($host, $user, $pass, $db);

		if (mysqli_connect_error()) {
			die('Connect Error (' . mysqli_connect_errno() . ') '
			. mysqli_connect_error());
		}
		$this->set_charset('UTF8');
	}


	public function query($query, $params_r=array(), $get_result=true)
	{
		if($stmt = $this->prepare($query)) {
			$this->bindParameters($stmt, $params_r);
			if ($stmt->execute()) {
				if($get_result){
					$stmt->store_result();
				}
			}
			return $stmt;
		}
	}

	public function execute($query, $params_r=array())
	{
		return $this->query($query, $params_r, false);
	}

	public function fetch_field($query, $field, $params_r=array())
	{
		$row = $this->preparedSelect($query, $params_r, 1, $field);
		return $row[$field];
	}

	public function fetch_fields($query, $field, $params_r=array())
	{
		$rows = $this->preparedSelect($query, $params_r, NULL, $field);
		return $rows;
	}

	public function fetch_row($query, $params_r=array())
	{
		return $this->preparedSelect($query, $params_r, 1);
	}
	public function fetch($query, $params_r=array())
	{
		return $this->preparedSelect($query, $params_r);
	}

	private function preparedSelect($query, $bind_params_r=array(), $rows=0, $field=NULL)
	{
		$select = $query instanceof mysqli_stmt ? $query : $this->query($query, $bind_params_r);
		$fields_r = $this->fetchFields($select);

		foreach ($fields_r as $fieldname) {
			if($field != NULL && $fieldname != $field) continue;
			$bind_result_r[] = &${$fieldname};
		}

		$this->bindResult($select, $bind_result_r);

		$result_r = array();
		$i = 0;
		while ($select->fetch()) {
			foreach ($fields_r as $field) {
				$result_r[$i][$field] = $$field;
			}
			$i++;
			if($rows && $rows == $i) break;
		}
		$select->close();
		return $rows == 1 ? $result_r[0] : $result_r;
	}

	private function fetchFields($selectStmt)
	{
		$metadata = $selectStmt->result_metadata();
		$fields_r = array();
		while ($field = $metadata->fetch_field()) {
			$fields_r[] = $field->name;
		}

		return $fields_r;
	}
	private function preparedQuery($query, $params_r)
	{
		$stmt = $this->prepare($query);
		if(!$stmt) {
			die($this->error);
		}
		$this->bindParameters($stmt, $params_r);
		$stmt->execute();
		return $stmt;
	}

	private function bindParameters(&$obj, &$bind_params_r)
	{
		if(count($bind_params_r))
		call_user_func_array(array($obj, "bind_param"), $bind_params_r);
	}

	private function bindResult(&$obj, &$bind_result_r)
	{
		call_user_func_array(array($obj, "bind_result"), $bind_result_r);
	}

	public function compile_db_insert_string($data) {

		$field_names  = "";
		$field_values = "";

		foreach ($data as $k => $v) {
			$v = preg_replace( "/'/", "\\'", $v );
			$field_names  .= "$k,";
			$field_values .= "'$v',";
		}

		$field_names  = preg_replace( "/,$/" , "" , $field_names  );
		$field_values = preg_replace( "/,$/" , "" , $field_values );

		return array( 'FIELD_NAMES'  => $field_names,
					  'FIELD_VALUES' => $field_values,
		);
	}
}

?>
