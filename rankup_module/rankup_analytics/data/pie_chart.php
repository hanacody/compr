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
$data = array();
for($i = 0;$i<sizeof($titles);$i++) {
	$data[] = new pie_value($datas[$i], $titles[$i]." (".$datas[$i].")");
}

$pie = new pie();
$pie->set_alpha(0.6);
$pie->set_start_angle(-135);
$pie->add_animation(new pie_bounce(10));
$pie->add_animation(new pie_fade());
$pie->gradient_fill();
$tooltip = '#val#°Ç(ÃÑ #total#°Ç)<br>#percent#';
$pie->set_tooltip($analytics->change_encoding($tooltip));
$pie->set_colours(array('#1F8FA1', '#FF368D', '#1C9E05', '#848484', '#cc99ff', '#92d1ed'));
$pie->set_values($data);

$chart = new open_flash_chart();
$chart->set_title($title);
$chart->set_bg_colour('#FFFFFF');
$chart->add_element($pie);
$chart->x_axis = null;

echo $chart->toPrettyString();

?>