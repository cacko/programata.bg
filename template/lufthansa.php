<?php
error_reporting(E_ERROR);
ini_set('display_errors', '1');
ob_start();
$standalone = false;
$order = 'normal';
define("DB_TABLE", 'lufthansa_game');

define("POLL_ROOT", sprintf("%s/poll", $_SERVER['DOCUMENT_ROOT']));
define("POLL_XML", sprintf('%s/lufthansa.xml', POLL_ROOT));
function my_autoload($class) {
	require_once sprintf("%s/%s.class.php", POLL_ROOT, strtolower($class));
}
spl_autoload_register('my_autoload');

$DB = db_driver::instance('localhost', 'prgbg_user', 'pr0gr4m4t4', 'prgbg_pro07');
$XML = simplexml_load_file(POLL_XML);

function debug($var) {
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
}

function html_header()
{
	while(@ob_end_clean());
	echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	  <html xmlns="http://www.w3.org/1999/xhtml">
	  <head>
		  <title>lufthansa game</title>
		  <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
		  <style type="text/css" media="screen">@import url("/style/lufthansa.css");</style>
		  <script type="text/javascript" src="/js/prototype.js"></script>
		  <script type="text/javascript" src="/js/scriptaculous.js?load=effects,builder"></script>
	  </head>
	  <body id="body">';
}

function http_error($error, $id=700)
{
	while(@ob_end_clean());
	header(sprintf("HTTP/1.0 %s", $id));
	header('Content-type: application/json');
	echo json_encode($error);
	exit;
}

function html_footer()
{
	echo '</body></html>';
}

function get_item($xpath, $root=NULL)
{
	global $XML;
	$result = (!$root) ? $XML->xpath($xpath) : $root->xpath($xpath);
	if(!$result) return;
	return $result[0];
}

function get_input($data)
{
	$name = trim($data['id']);
	$id = trim($data->id);
	$type = trim($data->type);
	$label = trim($data->label);
	$class = trim($data->class);
	$element = sprintf('<p class="field"><label for="%s" class="left">%s<span class="val" style="display: none">*</span></label><br/>', $id, $label);
	switch($type) {
		case 'select':
			$element .= sprintf('<select name="%s" id="%s">', $name, $id);
			$element .= '<option value="">&nbsp;</option>';
			foreach($data->options->children() as $option) {
				$element .= sprintf('<option value="%s">%s</option>', trim($option['id']), trim($option));
			}

			$element .= '</select>';
			break;

		case 'text':
			$element .= sprintf('<input id="%s" name="%s" %s/>', $id, $name, ($class) ? 'class="'.$class.'"' : '');
			break;
	}

	$element .= sprintf('<span class="val error_msg">&nbsp;</span></p>',($standalone) ? '<br/>' : '');
	return $element;
}

function get_game_html()
{
	global $standalone, $XML, $order;

	$html = '<div id="lufthansa">';
	$html .= sprintf('<h2>%s</h2>', trim($XML->title));
	$html .= '<div class="paragraph">';
	$html .= '<div id="lufthansaResult" style="display: none"></div>';
	$html .= '<form id="lufthansaForm" action="/template/lufthansa.php?action=submit" method="post">';
	$html .= '<div class="p">';
	$html .= sprintf('%s', trim($XML->intro));
	if($standalone) {
		$html .= '<input type="hidden" name="standalone" value="1"/>';
	}
	$html .= '
		</div>
		<div id="lufthansaError" style="display:none"></div>
		';

	$items = get_item("order[@id='$order']", $XML->orders);
	foreach($items->children() as $item) {
		$id = trim($item['id']);
		$html .= get_input(get_item("item[@id='$id']", $XML->items));
	}


	$html .= '
		<div class="spacer"></div>
		<p>
			<input type="checkbox" id="input_rules" name="rules" checked="checked"/>
			<label><span class="val" style="display: none">*&nbsp;</span>
			Приемам <a href="#" onclick="window.open(\'http://programata.bg/template/lufthansa.php?action=terms\',\'lufthansa_terms\',\'width=550,height=500,resizable=no,toolbar=no,location=no,scrollbars=yes,menubar=no,status=no\');">Условията на играта</a> и желая да получавам по имейл бюлетина на Lufthansa</label>
		</p>
		<p class="center">
			<input type="button" id="submitButton" value="Изпрати" disabled="disabled"/>
		</p>
	</form>
	</div>
	</div>
	<script type="text/javascript" src="/poll/lufthansa.js"></script>
	';

	return $html;
}

function submit_form($data)
{
	global $DB, $XML;

	$firstName = $data['firstName'];
	if(!$firstName) {
		http_error(array('error' => 'невалидно име', 'element' => 'input_firstName'));
	}
	$lastName = $data['lastName'];
	if(!$lastName) {
		http_error(array('error' => 'невалидно име', 'element' => 'input_lastName'));
	}
	$gender = $data['gender'];
	if(!in_array($gender, array('m', 'f'))) {
		http_error(array('error' => 'невалиден пол', 'element' => 'input_gender'));
	}
	$email = $data['email'];
	if(!preg_match('/^[^@]+@[-a-z0-9]+\.[a-z]{2,4}$/', $email)) {
		http_error(array('error' => 'невалиден имейл адрес', 'element' => 'input_email'));
	}
	$phone = $data['phone'];
	$company = $data['company'];
	$standalone = (bool) $data['standalone'];


	$args = array('ssss', $firstName, $lastName, $email, $gender);
	$query = sprintf('INSERT INTO `%s` SET `name`=?,`family`=?,`email`=?,`gender`=?', DB_TABLE);
	if($phone) {
		$args[0] .= 's';
		$args[] = $phone;
		$query .= ',`phone`=?';
	}
	if($company) {
		$args[0] .= 's';
		$args[] = $company;
		$query .= ',`company`=?';
	}
	if($standalone) {
		$args[0] .= 's';
		$args[] = 'conquiztador';
		$query .= ',`from`=?';
	}
	$st = $DB->execute($query, $args);
	if($st->errno == 1062) {
		http_error(array('error' => 'този адрес вече е изполван', 'element' => 'input_email', 'email' => $data['email']));
	}
	if($st->errno) {
		http_error(array('error' => $st->error));
	}
	while(@ob_end_clean());
	echo trim($XML->success);
	exit;
}

switch($_REQUEST['action'])
{
	case 'terms':
		while(@ob_end_clean());
		echo trim($XML->terms);
		exit;

	case 'standalone':
		$standalone = true;
		$order = 'standalone';
		html_header();
		echo get_game_html();
		html_footer();
		break;

	case 'submit':
		submit_form($_POST);
		break;

	default:
		echo get_game_html();

}




?>