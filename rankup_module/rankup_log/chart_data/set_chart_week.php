<?
include_once '../../../Libs/_php/rankup_basic.class.php';
include '../../../rankup_module/rankup_chart/php-ofc-library/open-flash-chart.php';


$title = new title('');
$data=array();

$today = date("Y-m-d");


if($_GET['mode'] == 'search'){
	$searchday1 = $_GET['day1yy']."-".$_GET['day1mm']."-".$_GET['day1dd'];
	$searchday2 = $_GET['day2yy']."-".$_GET['day2mm']."-".$_GET['day2dd'];
	$connection = "and wdate >= '$searchday1' and wdate <= '$searchday2'";
}
else {
	$edate = date("Y-m-d", mktime(0,0,0,date("n"), 1, date("Y") ) );
	$connection = "and wdate >= '$edate' and wdate <= '$today'";
}

$num = $rankup_control->queryRows("SELECT no FROM rankuplog_week where  no $connection");

if($num){
	$fetch = $rankup_control->queryFetchRows("SELECT sum(date0),sum(date1),sum(date2),sum(date3),sum(date4),sum(date5),sum(date6) FROM rankuplog_week where no $connection");
}

$week = array(0=>"일요일", 1=>"월요일", 2=>"화요일", 3=>"수요일", 4=>"목요일", 5=>"금요일", 6=>"토요일");

if($fetch) {
	foreach($fetch as $fetch) {
		for($i=0; $i<=6; $i++) {
			$temp = "sum(date".$i.")";
			$count = $fetch[$temp];
			$label = $week[$i]." ($count)";

			$tmp = new pie_value( (int)$count,  iconv( 'CP949', 'UTF-8', $label)  );
			$tmp->set_label( iconv( 'CP949', 'UTF-8', $label) , '#000000' , 12);
			$data[] = $tmp;
		}
	}
}
else {
	for($i=0; $i<=6; $i++) {
		$count = 0;
		$label = $week[$i]." ($count)";

		$tmp = new pie_value( (int)$count,  iconv( 'CP949', 'UTF-8', $label)  );
		$tmp->set_label( iconv( 'CP949', 'UTF-8', $label) , '#000000' , 12);
		$data[] = $tmp;
	}
}
$pie = new pie();
$pie->set_alpha(0.5);
$pie->set_start_angle( 35 );
$pie->add_animation( new pie_bounce(10) );
$pie->add_animation( new pie_fade() );
$pie->gradient_fill();
$pie->set_tooltip(iconv( 'CP949', 'UTF-8',  '#val#건(총 #total#건)<br>#percent#' ) );
$pie->set_colours( array('#1C9E05','#FF368D','#1F8FA1','#848484','#cc99ff','#92d1ed', '#3333cc') );
$pie->set_values( $data );

$chart = new open_flash_chart();
$chart->set_title( $title);
$chart->add_element( $pie );

$chart->x_axis = null;
$chart->set_bg_colour('#FFFFFF');

echo $chart->toPrettyString();
?>