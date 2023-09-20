<?
  // get GPC data:
  if(isset($_REQUEST['date'])) $date = $_REQUEST['date'];
  if(isset($_REQUEST['year'])) $year = $_REQUEST['year'];
  if(isset($_REQUEST['month'])) $month = $_REQUEST['month'];
  if(isset($_REQUEST['offset'])) $offset = $_REQUEST['offset'];

  // set PHP_SELF:
  if(isset($_SERVER['PHP_SELF'])) $PHP_SELF = $_SERVER['PHP_SELF'];
?>
<html>
<head>
<title>HTML-Calendar Example</title>
</head>
<body>
<h3>HTML-Calendar Example</h3>
Select year and month:
<form action="<? echo $PHP_SELF; ?>" method="post">
<?
  // if year is empty, set year to current year:
  if($year == '') $year = date('Y');

  // if month is empty, set month to current month:
  if($month == '') $month = date('n');

  // if offset is empty, set offset to 1 (start with Sunday):
  if($offset == '') $offset = 1;
?>
<input type="text" name="year" size="4" maxlength="4" value="<? echo $year; ?>">
<select name="month">
<?
  // build selection (months):
  $months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
  for($i = 1; $i <= 12; $i++) {
    echo '<option value="' . $i . '"';
    if($i == $month) echo ' selected';
    echo '>' . $months[$i-1] . "</option>\n";
  }
?>
</select>
<select name="offset">
<option value="0"<? if($offset == 0) echo ' selected'; ?>>Start with Saturday</option>
<option value="1"<? if($offset == 1) echo ' selected'; ?>>Start with Sunday</option>
<option value="2"<? if($offset == 2) echo ' selected'; ?>>Start with Monday</option>
</select>
<input type="submit" value="Go!">
</form>
<p>
<?
  // include calendar class:
  include('calendar.inc.php');

  // create calendar:
  $cal = new CALENDAR($year, $month);
  $cal->offset = $offset;
  $cal->link = $PHP_SELF;
  echo $cal->create();

  // if a day is clicked, view that date:
  if(isset($date)) echo '<p>You clicked ' . $date . '.';
?>
</body>
</html>
