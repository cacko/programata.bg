<?php
ob_start();

define("POLL_ROOT", sprintf("%s/poll", $_SERVER['DOCUMENT_ROOT']));
define("POLL_XML", sprintf('%s/poll.xml', POLL_ROOT));

function my_autoload($class) {
	require_once sprintf("%s/%s.class.php", POLL_ROOT, strtolower($class));
}
spl_autoload_register('my_autoload');

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
	echo $msg;
	exit;
}

function get_poll()
{
	global $poll;
	return $poll->get_poll_html();

}

function save_results($DATA)
{
	global $poll;
	$error = $poll->save_entry($DATA);
	return ($error) ? $error : 'Благодарим ви, че участвахте и не забравяйте да наминавате!';
}

function save_reject($DATA)
{
	global $poll;
	$error = $poll->save_entry($DATA, true);
	return ($error) ? $error : 'Благодарим Ви, за отделеното време. След <span id="timeout_span">10</span> секунди ще бъдете автоматично прехвърлени обратно към заглавната страница';
}

function get_reject()
{
	global $poll;
	return $poll->get_reject_html();
}

function container($body)
{
	echo '<script type="text/javascript" src="/poll/poll.js"></script>';
	echo '<div id="poll">';
	echo $body;
	echo '</div><div id="debug"></div><div id="pollInfoBox" style="display: none"></div>';
}

function get_intro()
{
	return '<div id="pollPopupBox" style="display: none">
		<script type="text/javascript" src="/poll/poll.js"></script>
		<h1>Анкета</h1>
		<div id="poll">
			<dl>
				<dt>
					Мили читатели,
					<br /><br />
					Програмата иска да чуе вашето мнение и да се запознае по-отблизо с вас.
					<br /><br />
					Затова стартираме онлайн проучване на аудиторията  ни и нагласите й към изданието.
					И взимаме това начинание съвсем насериозно – защото знаем, че всичко, което правим просто няма смисъл без вас, читателите.
					Надяваме се и вие да ни отговорите със същото и без да ни жалите да попълните анкетата по-долу.
					<br /><br />
					Обещаваме, няма да отнемем  повече от десетина минути от времето  ви и ще спечелите огромните ни сърдечни благодарности,
					a с малко късмет – и луксозен парфюм от DK – един за дама и един за кавалер.
					<br /><br />
					Анкетата е анонимна и ще е тук до 1 април. След това печелившите ще бъдат изтеглени  по всички правила на честната игра пред нотариус,
					а резултатите ще споделим след тяхното обработване.
					<br /><br />
					Ще продължите ли да попълвате въпросника?
				</dt>
				<dd>
					<input type="radio" name="continue" value="1" id="continue_yes" disabled="disabled"/>
					<label for="continue_yes">Да</label>
				</dd>
				<dd>
					<input type="radio" name="continue" value="0" id="continue_no" disabled="disabled"/>
					<label for="continue_no">Не</label>
				</dd>
			</dl>

		</div>
		<div id="debug" style="display: none"></div>
		<div id="pollInfoBox" style="display: none"></div>
	</div>';
}

$poll = new Poll(POLL_XML);
$DB = db_driver::instance('localhost', 'prgbg_user', 'pr0gr4m4t4', 'prgbg_pro07');

switch($_REQUEST['action']) {

	case 'submit':
		while(@ob_end_clean());
		echo save_results($_POST);
		exit;

	case 'poll':
		show_poll();
		break;

	case 'reject':
		echo get_reject();
		break;

	case 'rejecting':
		while(@ob_end_clean());
		echo save_reject($_POST);
		exit;

	case 'intro':
		echo get_intro();
		exit;

	default:
		container(get_poll());
		break;

}
?>
