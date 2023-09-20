<?php

interface dbInterface {
	public function query($query, $params_r=array(), $get_result=true);
	public function fetch_field($query, $field, $params_r=array());
	public function fetch_fields($query, $field, $params_r=array());
	public function fetch_row($query, $params_r=array());
	public function fetch($query, $params_r=array());
	public function execute($query, $params_r=array());
}