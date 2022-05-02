<!--

MIT License

Copyright (c) 2022 Neatnik LLC

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

--><!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Calendar</title>
<meta property="og:title" content="Calendar">
<meta property="og:url" content="https://neatnik.net/calendar">
<meta property="og:description" content="A simple printable calendar with the full year on a single page">
<style>
@import url('https://fonts.googleapis.com/css2?family=Oswald:wght@300;400&display=swap');
@import url('https://rsms.me/inter/inter.css');
@media print {
	#info {
		display: none;
	}
}
html {
	font-family: 'Oswald';
}
html, body {
	height: 100%;
	margin: 0;
	padding: 0;
}
table {
	width: 100%;
	height: calc(100% - 2.5em);
	border-collapse: separate;
	border-spacing: .5em 0;
}
td, th {
	font-weight: normal;
	text-transform: uppercase;
	border-bottom: 1px solid #888;
	padding: .3vmin .3vmin;
	font-size: .9vmin;
	font-weight: 300;
	color: #000;
}
th {
	font-size: 1.1vmin;
	padding: 0;
}
td:empty {
	border: 0;
}
.date {
	display: inline-block;
	width: 1.1em;
}
.day {
	display: inline-block;
	text-align: center;
	width: 1em;
	color: #888;
}
.weekend {
	background: #eee;
	font-weight: 400;
}
p {
	margin: 0 0 .5em 0;
	text-align: center;
}
* {
	color-adjust: exact;
	-webkit-print-color-adjust: exact;
}
#info {
	font-family: 'Inter', sans-serif;
	position: absolute;
	top: 0;
	left: 0;
	margin: 5em 2em;
	width: calc(100% - 6em);
	background: #333;
	color: #eee;
	padding: 1em 1em .5em 1em;
	font-size: 2vmax;
	border-radius: .2em;
}
#info p {
	text-align: left;
	margin: 0 0 1em 0;
	line-height: 135%;
}
#info a {
	color: inherit;
}
</style>
</head>
<body>
<div id="info">
<p>👋 <strong>Hello!</strong> If you print this page, you’ll get a nifty calendar that displays all of the year’s dates on a single page. It will automatically fit on a single sheet of paper of any size. For best results, adjust your print settings to landscape orientation and disable the header and footer.</p>
<p>Take in the year all at once. Fold it up and carry it with you. Jot down your notes on it. Plan things out and observe the passage of time. Above all else, be kind to others.</p>
<p style="font-size: 80%; color: #999;">Made by <a href="https://neatnik.net/">Neatnik</a> &#183; Source on <a href="https://github.com/neatnik/calendar">GitHub</a></p>
</div>
<?php
date_default_timezone_set('UTC');
$now = isset($_REQUEST['year']) ? strtotime($_REQUEST['year'].'-01-01') : time();
$dates = array();
$month = 1;
$day = 1;
echo '<p>'.date('Y', $now).'</p>';
echo '<table>';
echo '<thead>';
echo '<tr>';
// Add the month headings
for($i = 1; $i <= 12; $i++) {
	echo '<th>'.DateTime::createFromFormat('!m', $i)->format('M').'</th>';
}
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// Prepare a list of the first weekdays for each month of the year
$date = strtotime(date('Y', $now).'-01-01');
$first_weekdays = array();

for($x = 1; $x <= 12; $x++) {
	$first_weekdays[$x] = date('N', strtotime(date('Y', $now).'-'.$x.'-01'));
	$$x = false; // Set a flag for each month so we can track first days below
}

// Start the loop around 12 months
while($month <= 12) {
	$day = 1;
	for($x = 1; $x <= 42; $x++) {
		if(!$$month) {
			if($first_weekdays[$month] == $x) {
				$dates[$month][$x] = $day;
				$day++;
				$$month = true;
			}
			else {
				$dates[$month][$x] = 0;
			}
		}
		else {
			// Ensure that we have a valid date
			if($day > cal_days_in_month(CAL_GREGORIAN, $month, date('Y', $now))) {
				$dates[$month][$x] = 0;
				
			}
			else {
				$dates[$month][$x] = $day;
			}
			$day++;
		}
	}
	$month++;
}

// Now produce the table

$month = 1;
$day = 1;

if(isset($_REQUEST['layout']) && $_REQUEST['layout'] == 'aligned-weekdays') {
	// Start the outer loop around 42 days (6 weeks at 7 days each)
	while($day <= 42) {
		echo '<tr>';
		// Start the inner loop around 12 months
		while($month <= 12) {
			if($dates[$month][$day] == 0) {
				echo '<td></td>';
			}
			else {
				
				$date = date('Y', $now).'-'.str_pad($month, 2, '0', STR_PAD_LEFT).'-'.str_pad($dates[$month][$day], 2, '0', STR_PAD_LEFT);
				if(date('N', strtotime($date)) == '7') {
					echo '<td class="weekend">';
				}
				else {
					echo '<td>';
				}
				echo $dates[$month][$day];
				echo '</td>';
			}
			$month++;
		}
		echo '</tr>';
		$month = 1;
		$day++;
	}
	
}

else {
	// Start the outer loop around 31 days
	while($day <= 31) {
		echo '<tr>';
		// Start the inner loop around 12 months
		while($month <= 12) {
			// If we’ve reached a point in the date matrix where the resulting date would be invalid (e.g. February 30th), leave the cell blank
			if($day > cal_days_in_month(CAL_GREGORIAN, $month, date('Y', $now))) {
				echo '<td></td>';
				$month++;
				continue;
			}
			// If the day falls on a weekend, apply a specific class for styles
			if(DateTime::createFromFormat('!Y-m-d', date('Y', $now).'-'.$month.'-'.$day)->format('N') == 6 || DateTime::createFromFormat('!Y-m-d', date('Y', $now).'-'.$month.'-'.$day)->format('N') == 7) {
				echo '<td class="weekend">';
			}
			else {
				echo '<td>';
			}
			// Display the day number and day of the week
			echo '<span class="date">'.$day.'</span> <span class="day">'.substr(DateTime::createFromFormat('!Y-m-d', date('Y', $now).'-'.$month.'-'.$day)->format('D'), 0, 1).'</span>';
			echo '</td>';
			$month++;
		}
		echo '</tr>';
		$month = 1;
		$day++;
	}
}

?>
</tbody>
</table>
</body>
</html>