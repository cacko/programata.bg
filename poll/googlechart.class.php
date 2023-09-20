<?php

class GoogleChart {

	private $data;
	private $id;
	private $title;
	private $js;

	public function __construct($id, $data, $title)
	{
		$this->data = $data;
		$this->id = $id;
		$this->title = $title;
		$this->js = $this->get_js();

	}

	public function flush($standalone=false)
	{
		if($standalone) {
			while(@ob_end_clean());
			header('Content-type: application/javascript');
		}
		echo $this->js;
		if($standalone) {
			exit;
		}
	}

	private function get_js()
	{
		$js = "var data = new google.visualization.DataTable();\n";
		$headers = $this->data['types'];
		foreach($headers as $header) {
			list($type, $name) = explode('|', $header);
			$js .= sprintf("data.addColumn('%s', '%s');\n", $type, $name);
		}
		$js .= sprintf("data.addRows(%d);\n", count($this->data['data']));
		$idx = 0;
		foreach($this->data['data'] as $record) {
			$cidx = 0;
			foreach($headers as $header) {
				list($type, $name) = explode('|', $header);
				switch($type) {
					case 'string':
						$js .= sprintf("data.setValue(%d, %d, '%s');\n", $idx, $cidx, $record[$cidx]);
						break;
					case 'number':
						$js .= sprintf("data.setValue(%d, %d, %d);\n", $idx, $cidx, $record[$cidx]);
						break;
				}
				$cidx++;
			}
			$idx++;
		}
		$js .= sprintf("var chart = new google.visualization.%sChart(document.getElementById('%s'));
	chart.draw(data, {
		width: 1000,
		height: 350,
		is3D: true,
		isVertical: true,
		title: '%s',
		legendFontSize: 14,
		backgroundColor: '#DAE3EE'
	});", (count($headers) > 2) ? 'Column' : 'Pie', $this->id, $this->title);
		return $js;
	}
}
?>