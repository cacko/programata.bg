<?
/*
 +-------------------------------------------------------------------+
 |                  H T M L - C A L E N D A R   (v2.8)               |
 |                                                                   |
 | Copyright Gerd Tentler                www.gerd-tentler.de/tools   |
 | Created: May 27, 2003                 Last modified: Jan. 5, 2008 |
 +-------------------------------------------------------------------+
 | This program may be used and hosted free of charge by anyone for  |
 | personal purpose as long as this copyright notice remains intact. |
 |                                                                   |
 | Obtain permission before selling the code for this program or     |
 | hosting this software on a commercial website or redistributing   |
 | this software over the Internet or in any other medium. In all    |
 | cases copyright must remain intact.                               |
 +-------------------------------------------------------------------+

 EXAMPLE #1:  $myCal = new CALENDAR();
              echo $myCal->create();

 EXAMPLE #2:  $myCal = new CALENDAR(2004, 12);
              echo $myCal->create();

 EXAMPLE #3:  $myCal = new CALENDAR();
              $myCal->year = 2004;
              $myCal->month = 12;
              echo $myCal->create();
              

 Returns HTML code
==========================================================================================================
 Script modified in Feb 2008
 - User interface cleaned up from unnecessary formatting
 - All styled moved to external stylesheet
 - XHTML compatibility
 - Selected day integrated (different from today)
 - Uses external function for translating day headers
==========================================================================================================
  Example:

  // view seminar "How to use HTML-Calendar" from 6th to 8th with color #E0E0FF
  $myCal->viewEvent(6, 8, "#E0E0FF", "Seminar &quot;How to use HTML-Calendar&quot;");

  // view trip to Hawaii from 15th to 19th with color #D0FFD0 and link
  $myCal->viewEvent(15, 19, "#D0FFD0", "Trip to Hawaii!", "/trips/hawaii/index.php");
==========================================================================================================
*/
  $cal_ID = 0;

  class CALENDAR {
//========================================================================================================
// Configuration
//========================================================================================================
    var $tdBorderColor = '#C00';      // today: border color
    var $tdSelBorderColor = '#999';      // today: border color

    var $link = '';                      // page to link to when day is clicked
    var $offset = 2;                     // week start: 0 - 6 (0 = Saturday, 1 = Sunday, 2 = Monday ...)
    var $weekNumbers = false;             // view week numbers: true = yes, false = no

//--------------------------------------------------------------------------------------------------------
// You should change these variables only if you want to translate them into your language:
//--------------------------------------------------------------------------------------------------------
    // weekdays: must start with Saturday because January 1st of year 1 was a Saturday
    var $weekdays = array('Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri');

    // months: must start with January
    var $months = array('January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December');
    // error messages
    var $error = array('Year must be 1 - 3999!', 'Month must be 1 - 12!');

//--------------------------------------------------------------------------------------------------------
// Don't change from here:
//--------------------------------------------------------------------------------------------------------
    var $year, $month, $day, $size;
    var $mDays = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
    var $specDays = array();

//========================================================================================================
// Functions
//========================================================================================================
    function CALENDAR($year = '', $month = '', $day = '') {
      if($year == '' && $month == '' && $day == '') {
        $year = date('Y');
        $month = date('n');
	$day = date('j');
      }
      else if($year != '' && $month == '') $month = 1;
      $this->year = (int) $year;
      $this->month = (int) $month;
      $this->day = (int) $day;
    }

    function leap_year($year) {
      return (!($year % 4) && ($year < 1582 || $year % 100 || !($year % 400))) ? true : false;
    }

    function get_weekday($year, $days) {
      $a = $days;
      if($year) $a += ($year - 1) * 365;
      for($i = 1; $i < $year; $i++) if($this->leap_year($i)) $a++;
      if($year > 1582 || ($year == 1582 && $days >= 277)) $a -= 10;
      if($a) $a = ($a - $this->offset) % 7;
      else if($this->offset) $a += 7 - $this->offset;

      return $a;
    }

    function get_week($year, $days) {
      $firstWDay = $this->get_weekday($year, 0);
      return floor(($days + $firstWDay) / 7) + ($firstWDay <= 3);
    }

    function table_cell($content, $class='', $date = '', $style = '') {
      global $cal_ID;

      $html = '<td';
      if (!empty($class))
        $html .= ' class="'.$class.'"';
      //if($content != '&nbsp;' && stristr($class, 'day')) {
      if ($class != 'off') {
        $link = $this->link;

        if($this->specDays[$content]) {
          if($this->specDays[$content][0]) {
            $style .= 'background-color:' . $this->specDays[$content][0] . ';';
          }
          if($this->specDays[$content][1]) {
            $html .= ' title="' . $this->specDays[$content][1] . '"';
          }
          if($this->specDays[$content][2]) $link = $this->specDays[$content][2];
        }
        if($link) {
          $html .= ' onclick="document.location.href=\'' . $link . '&amp;sel_date=' . $date . '\'"';
        }
      }
      if(!empty($style))
        $html .= ' style="' . $style . '"';
      $html .= '>' . $content . '</td>'."\n";

      return $html;
    }

    function table_head($content='') {
      global $cal_ID, $aDefDaysShort;
      
      $aLangDaysShort = getLabel('aDaysShort');

      $cols = $this->weekNumbers ? 8 : 7;
      //$html = '<tr><td colspan="' . $cols . '" class="cssTitle' . $cal_ID . '" align="center"><b>' .
      //        $content . '</b></td></tr><tr>';
      $html .= '<thead><tr>';
      for($i = 0; $i < count($this->weekdays); $i++) {
        $ind = ($i + $this->offset) % 7;
        $wDay = $this->weekdays[$ind];
	$wDay = str_replace(array_values($aDefDaysShort), array_values($aLangDaysShort), $wDay);
        $html .= $this->table_cell($wDay, 'off');//, 'cssHeading' . $cal_ID
      }
      if($this->weekNumbers) $html .= $this->table_cell('&nbsp;', 'off');//, 'cssHeading' . $cal_ID
      $html .= '</tr></thead>'."\n";

      return $html;
    }

    function viewEvent($from, $to, $color, $title, $link = '') {
      if($from > $to) return;
      if($from < 1 || $from > 31) return;
      if($to < 1 || $to > 31) return;

      while($from <= $to) {
        $this->specDays[$from] = array($color, $title, $link);
        $from++;
      }
    }

    function create() {
      global $cal_ID;

      $this->size = ($this->hFontSize > $this->dFontSize) ? $this->hFontSize : $this->dFontSize;
      if($this->wFontSize > $this->size) $this->size = $this->wFontSize;

      list($curYear, $curMonth, $curDay) = explode('-', date('Y-m-d'));

      if($this->year < 1 || $this->year > 3999) $html = '<b>' . $this->error[0] . '</b>';
      else if($this->month < 1 || $this->month > 12) $html = '<b>' . $this->error[1] . '</b>';
      else {
        if($this->leap_year($this->year)) $this->mDays[1] = 29;
        for($i = $days = 0; $i < $this->month - 1; $i++) $days += $this->mDays[$i];

        $start = $this->get_weekday($this->year, $days);
        $stop = $this->mDays[$this->month-1];

        $html .= '<table border="0" cellspacing="0" cellpadding="3" summary="calendar">'."\n";
        //$title = htmlentities($this->months[$this->month-1]) . ' ' . $this->year;
        $html .= $this->table_head();//$title
        $daycount = 1;

        if(($this->year == $curYear) && ($this->month == $curMonth)) $inThisMonth = true;
        else $inThisMonth = false;
	
	$html .= '<tbody>';
        if($this->weekNumbers) $weekNr = $this->get_week($this->year, $days);
      
        while($daycount <= $stop) {
          $html .= '<tr>';

          for($i = $wdays = 0; $i <= 6; $i++) {
            $ind = ($i + $this->offset) % 7;
            if($ind == 0) $class = '';//'cssSaturdays';
            else if($ind == 1) $class = '';//'cssSundays';
            else $class = '';//'cssDays';

            //$style = '';
            $class = '';
            $date = $this->year . '-' . sprintf("%02d", $this->month) . '-' . sprintf("%02d", $daycount);

            if(($daycount == 1 && $i < $start) || $daycount > $stop) {
              $content = '&nbsp;';
              $class = 'off';
            }
            else {
              $content = $daycount;
              if($inThisMonth && $daycount == $curDay) {
                //$style = 'padding: 0px; border: 3px solid ' . $this->tdBorderColor . ';';
                $class = 'cal_today';
              }
	      elseif($this->day == $daycount) {
                //$style = 'padding: 0px; border: 3px solid ' . $this->tdSelBorderColor . ';';
                $class = 'cal_date';
              }
              else if($this->year == 1582 && $this->month == 10 && $daycount == 4) $daycount = 14;
              $daycount++;
              $wdays++;
            }
            $html .= $this->table_cell($content, $class, $date);//$class . $cal_ID, $date, $style
          }

          if($this->weekNumbers) {
            if(!$weekNr) {
              if($this->year == 1) $content = '&nbsp;';
              else if($this->year == 1583) $content = 52;
              else $content = $this->get_week($this->year - 1, 365);
            }
            else if($this->month == 12 && $weekNr >= 52 && $wdays < 4) $content = 1;
            else $content = $weekNr;

            $html .= $this->table_cell($content, 'off');//, 'cssWeeks' . $cal_ID
            $weekNr++;
          }
          $html .= '</tr>'."\n";
        }
	$html .= '</tbody></table>'."\n";
      }
      return $html;
    }
  }
?>
