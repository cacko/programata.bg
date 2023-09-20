<?php
if (!$bInSite) die();
//=========================================================
$SESS_DBHOST = DB_HOST;			/* database server hostname */
$SESS_DBNAME = DB_NAME;			/* database name */
$SESS_DBUSER = DB_USER;		/* database user */
$SESS_DBPASS = DB_PASS;		/* database password */

$SESS_DBH = "";
//$SESS_LIFE = get_cfg_var("session.gc_maxlifetime");

$SESS_LIFE = 6000; // 1 hour;

function sess_open($save_path, $session_name) 
{
	global $SESS_DBHOST, $SESS_DBNAME, $SESS_DBUSER, $SESS_DBPASS, $SESS_DBH, $REMOTE_ADDR;

	if (! $SESS_DBH = @mysql_connect($SESS_DBHOST, $SESS_DBUSER, $SESS_DBPASS)) 
	{
		return false;
	}
	if (! @mysql_select_db($SESS_DBNAME, $SESS_DBH)) 
	{
		return false;
	}
	return true;
}

function sess_close() 
{
	global $SESS_DBH;
	@mysql_close($SESS_DBH);
	return true;
}

function sess_read($key) 
{
	global $SESS_DBH, $SESS_LIFE;

	$qry = "SELECT 
						value 
					FROM 
						sessions 
					WHERE 
						sesskey = '$key' AND 
						expiry > ". time();
	$qid = @mysql_query($qry, $SESS_DBH);

	if (list($value) = @mysql_fetch_row($qid))
	{
		return (string) $value;
	}else
	{
		sess_destroy($key);
	}

	return false;
}

function sess_write($key, $val) 
{
	global $SESS_DBH, $SESS_LIFE;

	$expiry = time() + $SESS_LIFE;
	$value = addslashes($val);

	$qry = "INSERT 
						sessions 
					SET 
						 sesskey = '$key', 
						 expiry = $expiry, 
						 value = '$value'
				";
	$qid = @mysql_query($qry, $SESS_DBH);

	if (! $qid) 
	{
		$qry = "UPDATE 
							sessions 
						SET 
							expiry = $expiry, 
							value = '$value' 
						WHERE 
							sesskey = '$key' AND 
							expiry > ". time();
		$qid = @mysql_query($qry, $SESS_DBH);
	}

	return $qid;
}

function sess_destroy($key) 
{
	global $SESS_DBH;

	$qry = "DELETE 
					FROM 
						sessions 
					WHERE 
						sesskey = '$key'
				";
	$qid = @mysql_query($qry, $SESS_DBH);

	return $qid;
}

function sess_gc($maxlifetime) 
{
	global $SESS_DBH;

	$qry = 'DELETE 
					FROM 
						sessions 
					WHERE 
						expiry < ' . time();
	$qid = @mysql_query($qry, $SESS_DBH);

	return mysql_affected_rows($SESS_DBH);
}

session_set_save_handler(
	'sess_open',
	'sess_close',
	'sess_read',
	'sess_write',
	'sess_destroy',
	'sess_gc');
?>
