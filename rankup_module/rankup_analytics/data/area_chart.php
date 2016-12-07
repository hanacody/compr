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
	$ga->requestReportData($as_rows['google_profile_id'], array($_GET['kind']), array('visits'), 'date', null, $_GET['sdate'], $_GET['edate']);

	$max = 0;
	$titles = $datas = array();
	foreach($ga->getResults() as $result) {
		$dms = $result->getDimesions();
		array_push($titles, date('Y.m.d', strtotime($dms[$_GET['kind']])));
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

function dot($col) {
	global $analytics;
	switch($_GET['kind']) {
		case 'language':
			$tooltip = '사용언어: #x_label#<br>방문자수: #val#명';
			break;
		default:
			$tooltip = '접속일자: #x_label#<br>방문자수: #val#명';
	}
	$d = new solid_dot();
	$d->size(3);
	$d->halo_size(1);
	$d->colour($col);
	$d->tooltip($analytics->change_encoding($tooltip));
	return $d;
}

// 팔레트
$palettes = array(
	array(
		'dot' => '#cc0000',
		'line' => '#ff0099',
		'fill' => '#FF368D',
		'rotate' => 90
	),
	array(
		'dot' => '#3366cc',
		'line' => '#0099cc',
		'fill' => '#1F8FA1',
		'rotate' => 0
	)
);
$palette = ($_GET['kind']=='language') ? $palettes[1] : $palettes[0];

$d = dot($palette['dot']);

$area = new area();
$area->set_width( 2 );
$area->set_default_dot_style( $d );
$area->set_colour( $palette['line'] );
$area->set_fill_colour( $palette['fill'] );
$area->set_fill_alpha( 0.4 );
$area->set_values( $datas );
$area->on_show(new line_on_show('pop-up', '0', '0'));

$chart = new open_flash_chart();
$chart->set_title($title);
$chart->add_element( $area );

$x_labels = new x_axis_labels();
$x_labels->set_labels($titles);
$x_labels->set_size(11);
$x_labels->set_steps(86400);
$x_labels->visible_steps(1);
$x_labels->rotate( $palette['rotate'] );

$x_axis = new x_axis();
$x_axis->colour = '#d0d0d0';
$x_axis->set_labels($x_labels);
$x_axis->set_steps(86400);
$chart->set_x_axis( $x_axis );


if($max > 50) $range = round($max /10);
else if($max > 15) $range=5;
else if($max < 10) $range=1;
else $range=2;


$y_axis = new y_axis();
$y_axis->set_range(0, $max + 10, $range);
$chart->add_y_axis($y_axis);
$chart->set_bg_colour('#FFFFFF');

echo $chart->toPrettyString();
?>