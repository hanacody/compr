<?php
include_once '../../../Libs/_php/rankup_basic.class.php';
include '../../rankup_chart/php-ofc-library/open-flash-chart.php';

$todayyear = date("Y");
$todaydate = date("m");
$todayday = date("d");
$today = $todayyear."-".$todaydate."-".$todayday;

if($_GET['mode'] == 'search') {
	$searchday1 = $_GET['day1yy']."-".$_GET['day1mm']."-".$_GET['day1dd'];
	$searchday2 = $_GET['day2yy']."-".$_GET['day2mm']."-".$_GET['day2dd'];
	$connection = "and wdate >= '$searchday1' and wdate <= '$searchday2'";
}
else {
	$connection = "and wdate >= '$today' and wdate <= '$today'";
}

$num = $rankup_control->queryRows("SELECT * FROM rankuplog_time where no $connection");

if($num){
	$fetch = $rankup_control->queryFetchRows("SELECT sum(time0),sum(time1),sum(time2),sum(time3),sum(time4),sum(time5),sum(time6),sum(time7),sum(time8),sum(time9),sum(time10),sum(time11),sum(time12),sum(time13),sum(time14),sum(time15),sum(time16),sum(time17),sum(time18),sum(time19),sum(time20),sum(time21),sum(time22),sum(time23) FROM rankuplog_time where no $connection ");
}


//$title =  iconv( 'CP949', 'UTF-8', "시간별집계");
$title = new title('');
$data=array();

$max_count = 0;

if($fetch) {
	foreach($fetch as $fetch) {
		for($i=0; $i<24; $i++) {
			$temp = "sum(time".$i.")";
			$data[] = (int)$fetch[$temp];
			$labels[] =  iconv( 'CP949', 'UTF-8', $i);
			if($fetch[$temp] > $max_count) $max_count = $fetch[$temp];
		}
	}
}
else {
	for($i=0; $i<24; $i++) {
		$data[] = 0;
		$labels[] =  iconv( 'CP949', 'UTF-8', $i);
	}
}
if($max_count ==0 ) $max_count =5;
$d = new solid_dot();
$d->size(3);
$d->halo_size(1);
$d->colour('#cc0000');

$chart = new open_flash_chart();
$chart->set_title($title);

$area = new area();
$area->set_width( 2 );
$area->set_default_dot_style( $d );
$area->set_colour( '#ff6600' );
$area->set_fill_colour('#e01b49');
$area->set_fill_alpha( 0.4 );
$area->set_values( $data );
$area->on_show(new line_on_show('pop-up', '0', '0'));
$chart->add_element( $area );

$x_labels = new x_axis_labels();
$x_labels->set_labels($labels);
$x_labels->set_size(11);

$x_axis = new x_axis();
$x_axis->colour = '#d0d0d0';
$x_axis->set_labels($x_labels);
$chart->set_x_axis( $x_axis );

if($max_count > 50) $range = round($max_count /10, -1);
else if($max_count > 15) $range=5;
else if($max_count < 10) $range =1;
else $range=2;


$y_axis = new y_axis();
$y_axis->set_range(0, $max_count+1, $range);
$chart->add_y_axis($y_axis);
$chart->set_bg_colour('#FFFFFF');

echo $chart->toPrettyString();
?>