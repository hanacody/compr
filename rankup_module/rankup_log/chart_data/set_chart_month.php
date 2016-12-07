<?
include_once '../../../Libs/_php/rankup_basic.class.php';
include '../../../rankup_module/rankup_chart/php-ofc-library/open-flash-chart.php';



$todayyear = date("Y");
$today = $todayyear."-".$todaydate;

if($_GET['mode'] == 'search') {
	$connection = "and wdate like '%$_GET[frontyear]%'";
}
else {
	$connection = "and wdate like '%$todayyear%'";
}

$num = $rankup_control->queryRows("SELECT * FROM rankuplog_month where no $connection");

if($num) {
	$fetch = $rankup_control->queryFetchRows("SELECT sum(month01), sum(month02), sum(month03), sum(month04), sum(month05), sum(month06), sum(month07), sum(month08), sum(month09), sum(month10), sum(month11), sum(month12) FROM rankuplog_month where no $connection");
}

//$title =  iconv( 'CP949', 'UTF-8', "객실별집계");
$title = new title('');
$data=array();

$max_count = 0;
if($fetch) {
	foreach ($fetch as $fetch) {
		for($i=1; $i<13; $i++) {
			$month = sprintf('%02d', $i);
			$temp = "sum(month".$month.")";
			$data[] = (int)$fetch[$temp];
			$labels[] =  iconv( 'CP949', 'UTF-8', $i."월");
			if($fetch[$temp] > $max_count) $max_count = $fetch[$temp];
		}
	}
}
else {
	for($i=1; $i<13; $i++) {
		$month = sprintf('%02d', $i);
		$data[] =0;
		$labels[] =  iconv( 'CP949', 'UTF-8', $i."월");
	}
}
if($max_count ==0 ) $max_count =5;

$bar = new bar_cylinder();
$bar->set_values( $data );

$bar->set_colour('#ff0000');
$bar->set_on_show(new bar_on_show('grow-up', '0.2', '0'));


$chart = new open_flash_chart();
$chart->set_title( $title);
$chart->add_element( $bar );

$x_labels = new x_axis_labels();
$x_labels->set_labels($labels);
$x_labels->set_size(12);

$x_axis = new x_axis();
$x_axis->set_3d( 5 );
$x_axis->colour = '#d0d0d0';
$x_axis->set_labels($x_labels);
$chart->set_x_axis( $x_axis );



if($max_count > 50) $range = round($max_count /10, -1);
else if($max_count > 15) $range=5;
else if($max_count < 10) $range =1;
else $range=2;


$y_axis = new y_axis();
$y_axis->set_range(0, $max_count+20, $range);
$chart->add_y_axis($y_axis);
$chart->set_bg_colour('#FFFFFF');

echo $chart->toPrettyString();
?>