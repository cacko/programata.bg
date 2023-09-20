<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', '1');

define("PASSWORD", 'mente');
define("COOKIENAME", '_crt3425');

//if($_SERVER['REMOTE_ADDR'] != '78.83.250.43') die('opa');
ob_start();

define("POLL_ROOT", sprintf("%s/poll", $_SERVER['DOCUMENT_ROOT']));
define("POLL_XML", sprintf('%s/poll.xml', POLL_ROOT));


function my_autoload($class) {
	require_once sprintf("%s/%s.class.php", POLL_ROOT, strtolower($class));
}
spl_autoload_register('my_autoload');
$DB = db_driver::instance('localhost', 'prgbg_user', 'pr0gr4m4t4', 'prgbg_pro07');

$poll = new Poll(POLL_XML);
$map = $poll->get_poll_map();

function html_header()
{
	while(@ob_end_clean());
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	  <html xmlns="http://www.w3.org/1999/xhtml">
	  <head>
		  <title>poll results</title>
		  <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
		  <style type="text/css" media="screen">@import url("chart.css");</style>
		  <script type="text/javascript" src="/js/prototype.js"></script>
		  <script type="text/javascript" src="/js/scriptaculous.js?load=effects,builder"></script>
		  <script type="text/javascript" src="http://www.google.com/jsapi"></script>
		  <script type="text/javascript" src="/poll/chart.js"></script>
	  </head>
	  <body id="body">';
}

function questions($exclude=0) {
	global $map;
	echo '<option selected="selected">&nbsp;</option>';
	foreach($map as $item) {
		if($item['id'] != $exclude) {
			printf('<option value="%d">%s</option>', $item['id'], $item['name']);
		}

	}
}

function checks($item_id) {
	global $map;
	foreach($map[$item_id]['values'] as $value_id=>$value_name) {
		printf('<input type="checkbox" name="filters[]" value="%d" id="value_%d"/><label for="value_%d">%s</label>',
		$value_id,$value_id,  $value_id,$value_name);
	}
}

function get_title($data) {
	global $map;
	$x = (int) $data['x'];
	$y = (int) $data['y'];
	$filters = (array) $data['filters'];
	$result = '';
	if(!$y) {
		$result = $map[$x]['name'];
	}
	elseif(count($filters) > 1) {
		$result = sprintf('%s/%s', $map[$x]['name'], $map[$y]['name']);
	}
	else {
		$result = sprintf('%s - %s', $map[$y]['name'], $map[$y]['values'][$filters[0]]);
	}
	return $result;
}

function html_login() {
	echo '<div id="login"><label for="password_field">Password</label><input type="password" name="password" id="password_field"/></div>';
}


function html_body() {
	global $poll;

	$totals = $poll->get_totals();
	echo'
	<div id="header">
		<h1>Анкета</h1>
		<div class="details">';
	printf('Попълнили:&nbsp;<span id="votes" class="flash">%d</span>
			&nbsp;/&nbsp;
			Отказали:&nbsp;<span id="rejects" class="flash">%d</span>', $totals['votes'], $totals['rejects']);
	echo'</div>
	</div>
	<div id="main">
	<div id="controls">
  	<form id="chartForm" action="/poll/?action=chart" method="post">
  		<p>
  			<select id="firstQuestion" name="x">';
	questions();
	echo'</select>
  		</p>
  		<p>
  			<select id="secondQuestion" name="y" disabled="disabled">
  			<option selected="selected">&nbsp;</option>
  			</select>
  		</p>
  		<p id="values">

  		</p>
  		<p>
  			<input type="button" value="Create" id="createButton"/>
  			<input type="hidden" name="chart_id" id="ChartIdInput" value=""/>
  		</p>
  	</form>
  	</div>';
}

function html_footer() {
	echo '</div></body></html>';
}

function array2csv($data) {
	$result = '';
	$result .= implode(',', array_keys($data));
	$result .= "\n";
	$result .= implode(',', array_values($data));
	return $result;
}

function is_logged() {
	$cookie_pass = $_COOKIE[COOKIENAME];
	if(!$cookie_pass || $cookie_pass != md5(PASSWORD)) return;
	return true;
}
function do_login($data) {
	if($data['password'] != PASSWORD) {
		http_error(500);
	}
	setcookie(COOKIENAME, md5(PASSWORD));
	while(@ob_end_clean());
	html_body();
	exit;
}

function http_error($id, $msg='Custom Error')
{
	while(@ob_end_clean());
	switch($id)
	{
		case 403:
			$header = "HTTP/1.0 403 Forbidden";
			break;

		case 603:
			$header = "HTTP/1.0 600 Upload File Cannot Be Nothing";
			break;

		case 601:
			$header = "HTTP/1.0 601 Unsupported Media Type";
			break;

		case 602:
			$header = "HTTP/1.0 602 Permission Denied";
			break;

		case 604:
			$header = "HTTP/1.0 604 Not Enough Storage";
			break;

		case 500:
			$header = "HTTP/1.0 500 Internal Server Error";

		default:
			$header = sprintf("HTTP/1.0 %d %s", $id, $msg);
	}
	header($header);
	echo $header;
	exit;
}

switch($_REQUEST['action']) {

	case 'login':
		do_login($_POST);
		exit;

	case 'totals':
		while(@ob_end_clean());
		header('Content-type: application/json');
		echo json_encode($poll->get_totals());
		exit;

	case 'questions':
		while(@ob_end_clean());
		questions((int)$_REQUEST['exclude']);
		break;

	case 'checks':
		while(@ob_end_clean());
		checks((int)$_REQUEST['question']);
		break;

	case 'chart':
		$chart = new GoogleChart($_REQUEST['chart_id'], $poll->get_chart($_REQUEST),get_title($_REQUEST));
		$chart->flush(true);
		break;

	default:
		html_header();
		if(is_logged()) {
			html_body();
		}
		else {
			html_login();
		}
		html_footer();
		break;
}

?>