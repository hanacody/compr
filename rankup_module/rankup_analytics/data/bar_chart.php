<?php
include_once '../../../Libs/_php/rankup_basic.class.php';
include_once '../../rankup_chart/php-ofc-library/open-flash-chart.php';
include_once '../rankup_analytics.class.php';

$analytics = new rankup_analytics;
if($analytics->infos!==null) {
	extract($analytics->infos);
}
else {
	include_once '../gapi/gapi.class.php';

	$as_rows = $analytics->configs;
	$ga = new gapi($as_rows['google_id'], $as_rows['google_pass']);
	$ga->requestReportData($as_rows['google_profile_id'], array($_GET['kind']), array('visits'), '-visits', null, $_GET['sdate'], $_GET['edate']);

	$max = 0;
	$titles = $datas = array();
	foreach($ga->getResults() as $result) {
		$dms = $result->getDimesions();
		array_push($titles, $dms[$_GET['kind']]);
		array_push($datas, $result->getVisits());
		if($max < $result->getVisits()) $max = $result->getVisits();
	}
	if(!$max) $max = 5;
	$analytics->keep(array(
		'kind' => $_GET['kind'],
		'sdate' => $_GET['sdate'],
		'edate' => $_GET['edate'],
		'titles' => serialize($titles),
		'datas' => serialize($datas),
		'max' => $max
	));
}

$title = new title('');
$rankup_control->change_encoding($titles, 'IN');
foreach($titles as $key=>$_title) {
	$titles[$key] = $rankup_control->str_cut($_title, 25);
}

$rankup_control->change_encoding($titles);

$chart = new open_flash_chart();

$tooltip = '#val#°Ç';
$hbar = new hbar('#3399cc');
$hbar->set_tooltip($analytics->change_encoding($tooltip));
$hbar->set_values($datas);

$chart->set_title($title);
$chart->add_element($hbar);

$x = new x_axis();
$x->set_offset(false);

if($max > 100) $step = round($max /10, -1);
else if($max > 30) $step = 5;
else $step = 2;

$x->set_steps($step);
$x->set_range(0, $max+2);
$chart->set_x_axis($x);

$y = new y_axis();
$y->set_offset(true);
$y->set_labels(array_reverse($titles));
$chart->add_y_axis($y);

$tooltip = new tooltip();
$tooltip->set_hover();
$chart->set_bg_colour('#ffffff');
$chart->set_tooltip($tooltip);

echo $chart->toPrettyString();
?>