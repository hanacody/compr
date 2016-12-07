<?
include_once '../../../Libs/_php/rankup_basic.class.php';
include '../../rankup_chart/php-ofc-library/open-flash-chart.php';
//include "../Libs/db_class.php";

if($_GET[use_date]!="on"){
	$_GET[sdate] = date("Y-m-d",  strtotime("-1 month"));
	$_GET[edate] = date("Y-m-d");
}

$num = $rankup_control->queryRows("SELECT * FROM rankuplog_date where wdate  >= '$_GET[sdate]' and  wdate  <= '$_GET[edate]' ");

if($num) {
	$fetch = $rankup_control->queryFetchRows("SELECT * FROM rankuplog_total where  wdate  >= '$_GET[sdate]' and  wdate  <= '$_GET[edate]'  ");
}



//$title =  iconv( 'CP949', 'UTF-8', "시간별집계");
$title = new title('');
$data=array();

$max_count = 0;
$k=0;
foreach($fetch as $fetch) {
	$data[] = (int)$fetch['num'];
	$labels[] =  iconv( 'CP949', 'UTF-8', $fetch['wdate']);
	if($fetch['num'] > $max_count) $max_count = $fetch['num'];
	$k++;
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
$x_labels->set_steps(86400);
// only display every other label (every other day)
$x_labels->visible_steps(2);
$x_labels->rotate(90);


$x_axis = new x_axis();
$x_axis->colour = '#d0d0d0';
$x_axis->set_labels($x_labels);
$x_axis->set_steps(86400);
$chart->set_x_axis( $x_axis );


if($max_count > 50) $range = round($max_count /10, -1);
else if($max_count > 15) $range=5;
else if($max_count < 10) $range =1;
else $range=2;


$y_axis = new y_axis();
$y_axis->set_range(0, $max_count+10, $range);
$chart->add_y_axis($y_axis);
$chart->set_bg_colour('#FFFFFF');

echo $chart->toPrettyString();
?>