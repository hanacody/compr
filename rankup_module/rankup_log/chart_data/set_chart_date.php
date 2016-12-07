<?
include_once '../../../Libs/_php/rankup_basic.class.php';
include '../../rankup_chart/php-ofc-library/open-flash-chart.php';
//include "../Libs/db_class.php";

$todayyear = date("Y");
$todaydate = date("m");
$today = $todayyear."-".$todaydate;
if($_GET['mode'] == 'search') {
	$searchday1 = $_GET['frontyear']."-".$_GET['frontmonth'];
	$connection = "and wdate like '%$searchday1%'";
}
else {
	$connection = "and wdate like '%$today%'";
}

$num = $rankup_control->queryRows("SELECT * FROM rankuplog_date where no $connection");

if($num) {
	$fetch = $rankup_control->queryFetchRows("SELECT sum(date1), sum(date2), sum(date3), sum(date4), sum(date5), sum(date6), sum(date7), sum(date8), sum(date9), sum(date10), sum(date11), sum(date12), sum(date13), sum(date14), sum(date15), sum(date16), sum(date17), sum(date18), sum(date19), sum(date20), sum(date21), sum(date22), sum(date23), sum(date24), sum(date25), sum(date26), sum(date27), sum(date28), sum(date29), sum(date30), sum(date31) FROM rankuplog_date where no $connection");
}


//$title =  iconv( 'CP949', 'UTF-8', "시간별집계");
$title = new title('');
$data=array();

$max_count = 0;
if($fetch) {
	foreach($fetch as $fetch) {
		for($i=1; $i<=31; $i++) {
			$temp = "sum(date".$i.")";
			$data[] = (int)$fetch[$temp];
			$labels[] =  iconv( 'CP949', 'UTF-8', $i);
			if($fetch[$temp] > $max_count) $max_count = $fetch[$temp];
		}
	}
}
else {
		for($i=1; $i<=31; $i++) {
			$data[] =0;
			$labels[] =  iconv( 'CP949', 'UTF-8', $i);
		}
}
if($max_count ==0 ) $max_count =5;
$d = new solid_dot();
$d->size(3)->halo_size(1)->colour('#cc0000');

/*
$line = new line();
$line->set_default_dot_style($d);
$line->set_values( $data );
$line->set_width( 2 );
$line->set_colour( '#ff6600' );
$line->on_show(new line_on_show('pop-up', '0', '0'));
*/
$area = new area();
$area->set_width( 2 );
$area->set_default_dot_style( $d );
$area->set_colour( '#ff6600' );
$area->set_fill_colour('#e01b49');
$area->set_fill_alpha( 0.4 );
$area->set_values( $data );
$area->on_show(new line_on_show('pop-up', '0', '0'));


$chart = new open_flash_chart();
$chart->set_title($title);
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
$y_axis->set_range(0, $max_count+3, $range);
$chart->add_y_axis($y_axis);
$chart->set_bg_colour('#FFFFFF');

echo $chart->toPrettyString();
?>