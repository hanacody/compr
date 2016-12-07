<?
include_once '../../../Libs/_php/rankup_basic.class.php';
include '../../rankup_chart/php-ofc-library/open-flash-chart.php';
//include "../Libs/db_class.php";

$today = date("Y-m-d");
$edate = date("Y-m-d", mktime(0,0,0,date("n"), date("j")-7, date("Y")) );

$connection = "and wdate between  '$edate' AND '$today' ";

$num = $rankup_control->queryRows("SELECT * FROM rankuplog_total where no $connection");

if($num) {
	$fetch = $rankup_control->queryFetchRows("SELECT wdate, num FROM rankuplog_total where no $connection");
}


//$title =  iconv( 'CP949', 'UTF-8', "시간별집계");
$title = new title('');
$data=array();

$max_count = 0;
foreach($fetch as $fetch) {
	$data[] = (int)$fetch['num'];
	$labels[] =  iconv( 'CP949', 'UTF-8', str_replace("-", ".", substr($fetch['wdate'],5,10)) );
	if($fetch['num'] > $max_count) $max_count = $fetch['num'];

}

if($max_count ==0 ) $max_count =5;
$d = new solid_dot();
$d->size(3);$d->halo_size(1);$d->colour('#cc0000');

/*
$line = new line();
$line->set_default_dot_style($d);
$line->set_values( $data );
$line->set_width( 2 );
$line->set_colour( '#ff6600' );
$line->on_show(new line_on_show('pop-up', '0', '0'));
*/

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
$x_axis->set_3d( 3 );
$x_axis->colour = '#d0d0d0';
$x_axis->set_labels($x_labels);
$chart->set_x_axis( $x_axis );

$y_axis = new y_axis();

if($max_count > 50) $range = round($max_count /10, -1);
else if($max_count > 15) $range=5;
else if($max_count < 10) $range =1;
else $range=2;

$y_axis->set_range(0, $max_count+10, $range);
$chart->add_y_axis($y_axis);
$chart->set_bg_colour('#FFFFFF');

echo $chart->toPrettyString();