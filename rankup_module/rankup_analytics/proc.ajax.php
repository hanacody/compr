<?php
/**
 * 차트 데이터 처리
 *@author: kurokisi
 *@authDate: 2011.10.11
 */
include_once '../../Libs/_php/rankup_basic.class.php';
include_once './rankup_analytics.class.php';

$_GET = $_POST; // analytics construct 시 사용
$analytics = new rankup_analytics;

switch($_POST['mode']) {
	/*2012-03-22 add*/
	case 'log_reset':
		if(rankup_basic::is_demo()) echo "데모에서는 초기화를 하실수 없습니다.";
		else $analytics->reset_logs();
		break;
	case 'set_configs':
		$analytics->set_configs();
		break;

	case 'load':
		include_once './gapi/gapi.class.php';
		$as_rows = $analytics->configs;
		$ga = new gapi($as_rows['google_id'], $as_rows['google_pass']);

		$max = 0;
		$titles = $datas = array();
		if($analytics->infos===null) {
			switch($_POST['shape']) {
				case 'area_chart':
					$ga->requestReportData($as_rows['google_profile_id'], array($_POST['kind']), array('visits'), 'date', null, $_POST['sdate'], $_POST['edate']);
					foreach($ga->getResults() as $result) {
						$dms = $result->getDimesions();
						array_push($titles, date('Y.m.d', strtotime($dms[$_POST['kind']])));
						array_push($datas, $result->getVisits());
						if($max < $result->getVisits()) $max = $result->getVisits();
					}
					break;

				case 'pie_chart':
					$ga->requestReportData($as_rows['google_profile_id'], array($_POST['kind']), array('visits'), '-visits', null, $_POST['sdate'], $_POST['edate']);
					foreach($ga->getResults() as $result) {
						$dms = $result->getDimesions();
						array_push($titles, $dms[$_POST['kind']]);
						array_push($datas, $result->getVisits());
						if($max < $result->getVisits()) $max = $result->getVisits();
					}
					break;

				case 'bar_chart':
					$ga->requestReportData($as_rows['google_profile_id'], array($_POST['kind']), array('visits'), '-visits', null, $_POST['sdate'], $_POST['edate']);
					foreach($ga->getResults() as $result) {
						$dms = $result->getDimesions();
						array_push($titles, $dms[$_POST['kind']]);
						array_push($datas, $result->getVisits());
						if($max < $result->getVisits()) $max = $result->getVisits();
					}
					break;
			}
			if(count($titles)) {
				if(!$max) $max = 5;
				$analytics->keep(array(
					'kind' => $_POST['kind'],
					'sdate' => $_POST['sdate'],
					'edate' => $_POST['edate'],
					'titles' => serialize($titles),
					'datas' => serialize($datas),
					'max' => $max
				));
			}
		}
		break;
}
?>