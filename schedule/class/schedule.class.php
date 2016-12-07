<?php
/**
 * 일정관리 클래스
 *@author: kurokisi
 *@authDate: 2012.02.03
 */
class schedule extends rankup_util {

	private $table = 'rankup_schedule';

	function __construct() {
		parent::rankup_util();

		// 기념일 - 양력
		$this->solar_days = array(
			'01-01' => array(1, '신정'),
			'03-01' => array(1, '삼일절'),
			'05-01' => array(0, '근로자날'),
			'05-05' => array(1, '어린이날'),
			'05-08' => array(0, '어버이날'),
			'05-15' => array(0, '스승의날'),
			'06-06' => array(1, '현충일'),
			'07-17' => array(0, '제헌절'),
			'08-15' => array(1, '광복절'),
			'10-03' => array(1, '개천절'),
			'12-25' => array(1, '성탄절')
		);
		// 기념일 - 음력
		$this->lunar_days = array(
			'12-30' => array(1, ' '),
			'01-01' => array(1, '설날'),
			'01-02' => array(1, ' '),
			'01-15' => array(0, '정월대보름'),
			'04-08' => array(1, '석가탄신일'),
			'05-05' => array(0, '단오'),
			'06-15' => array(0, '유두'),
			'07-07' => array(0, '칠석'),
			'08-14' => array(1, ' '),
			'08-15' => array(1, '추석'),
			'08-16' => array(1, ' '),
			'09-09' => array(0, '중양')
		);
		$this->lunar_initialize();
	}

	// 기념일 체크 - 기생루틴
	private function check_days($date) {
		list($sy, $sm, $sd) = explode('-', $date);
		$days = $this->solar_days[sprintf('%02d-%02d', $sm, $sd)];
		$lunar_day = $this->soltolun($sy, (int)$sm, (int)$sd); // 음력전환
		if(is_array($lunar_day)) {
			$lunar_days = $this->lunar_days[sprintf('%02d-%02d', $lunar_day['month'], $lunar_day['day'])];
			if($lunar_days[0]==1 && $lunar_day['leap']) $lunar_days = array();
			if($lunar_days[1]) {
				if($days[1]) $days[1] = $lunar_days[1].'/'.$days[1];
				else $days = $lunar_days;
			}
		}
		return $days;
	}

	// 음력
	private function lunar_initialize() {
		$this->kk = array(
		//1841년 ~ 1900년
		1,2,4,1,1,2,1,2,1,2,2,1,    2,2,1,2,1,1,2,1,2,1,2,1,    2,2,2,1,2,1,4,1,2,1,2,1,    2,2,1,2,1,2,1,2,1,2,1,2,    1,2,1,2,2,1,2,1,2,1,2,1,
		2,1,2,1,5,2,1,2,2,1,2,1,    2,1,1,2,1,2,1,2,2,2,1,2,    1,2,1,1,2,1,2,1,2,2,2,1,    2,1,2,3,2,1,2,1,2,1,2,2,    2,1,2,1,1,2,1,1,2,2,1,2,
		2,2,1,2,1,1,2,1,2,1,5,2,    2,1,2,2,1,1,2,1,2,1,1,2,    2,1,2,2,1,2,1,2,1,2,1,2,    1,2,1,2,1,2,5,2,1,2,1,2,    1,1,2,1,2,2,1,2,2,1,2,1,
		2,1,1,2,1,2,1,2,2,2,1,2,    1,2,1,1,5,2,1,2,1,2,2,2,    1,2,1,1,2,1,1,2,2,1,2,2,    2,1,2,1,1,2,1,1,2,1,2,2,    2,1,6,1,1,2,1,1,2,1,2,2,
		1,2,2,1,2,1,2,1,2,1,1,2,    2,1,2,1,2,2,1,2,2,3,1,2,    1,2,2,1,2,1,2,2,1,2,1,2,    1,1,2,1,2,1,2,2,1,2,2,1,    2,1,1,2,4,1,2,2,1,2,2,1,
		2,1,1,2,1,1,2,2,1,2,2,2,    1,2,1,1,2,1,1,2,1,2,2,2,    1,2,2,3,2,1,1,2,1,2,2,1,    2,2,2,1,1,2,1,1,2,1,2,1,    2,2,2,1,2,1,2,1,1,5,2,1,
		2,2,1,2,2,1,2,1,2,1,1,2,    1,2,1,2,2,1,2,1,2,2,1,2,    1,1,2,1,2,4,2,1,2,2,1,2,    1,1,2,1,2,1,2,1,2,2,2,1,    2,1,1,2,1,1,2,1,2,2,2,1,
		2,2,1,1,5,1,2,1,2,2,1,2,    2,2,1,1,2,1,1,2,1,2,1,2,    2,2,1,2,1,2,1,1,2,1,2,1,    2,2,4,2,1,2,1,1,2,1,2,1,    2,1,2,2,1,2,2,1,2,1,1,2,
		1,2,1,2,1,2,5,2,2,1,2,1,    1,2,1,2,1,2,1,2,2,1,2,2,    1,1,2,1,1,2,1,2,2,2,1,2,    2,1,1,2,3,2,1,2,2,1,2,2,    2,1,1,2,1,1,2,1,2,1,2,2,
		2,1,2,1,2,1,1,2,1,2,1,2,    2,2,1,5,2,1,1,2,1,2,1,2,    2,1,2,2,1,2,1,1,2,1,2,1,    2,1,2,2,1,2,1,2,1,2,1,2,    1,5,2,1,2,2,1,2,1,2,1,2,
		1,2,1,2,1,2,1,2,2,1,2,2,    1,1,2,1,1,5,2,2,1,2,2,2,    1,1,2,1,1,2,1,2,1,2,2,2,    1,2,1,2,1,1,2,1,2,1,2,2,    2,1,2,1,5,1,2,1,2,1,2,1,
		2,2,2,1,2,1,1,2,1,2,1,2,    1,2,2,1,2,1,2,1,2,1,2,1,    2,1,5,2,2,1,2,1,2,1,2,1,    2,1,2,1,2,1,2,2,1,2,1,2,    1,2,1,1,2,1,2,5,2,2,1,2,

		//1901년 ~ 2000년
		1,2,1,1,2,1,2,1,2,2,2,1,    2,1,2,1,1,2,1,2,1,2,2,2,    1,2,1,2,3,2,1,1,2,2,1,2,    2,2,1,2,1,1,2,1,1,2,2,1,    2,2,1,2,2,1,1,2,1,2,1,2,
		1,2,2,4,1,2,1,2,1,2,1,2,    1,2,1,2,1,2,2,1,2,1,2,1,    2,1,1,2,2,1,2,1,2,2,1,2,    1,5,1,2,1,2,1,2,2,2,1,2,    1,2,1,1,2,1,2,1,2,2,2,1,
		2,1,2,1,1,5,1,2,2,1,2,2,    2,1,2,1,1,2,1,1,2,2,1,2,    2,2,1,2,1,1,2,1,1,2,1,2,    2,2,1,2,5,1,2,1,2,1,1,2,    2,1,2,2,1,2,1,2,1,2,1,2,
		1,2,1,2,1,2,2,1,2,1,2,1,    2,3,2,1,2,2,1,2,2,1,2,1,    2,1,1,2,1,2,1,2,2,2,1,2,    1,2,1,1,2,1,5,2,2,1,2,2,    1,2,1,1,2,1,1,2,2,1,2,2,
		2,1,2,1,1,2,1,1,2,1,2,2,    2,1,2,2,3,2,1,1,2,1,2,2,    1,2,2,1,2,1,2,1,2,1,1,2,    2,1,2,1,2,2,1,2,1,2,1,1,    2,1,2,5,2,1,2,2,1,2,1,2,
		1,1,2,1,2,1,2,2,1,2,2,1,    2,1,1,2,1,2,1,2,2,1,2,2,    1,5,1,2,1,1,2,2,1,2,2,2,    1,2,1,1,2,1,1,2,1,2,2,2,    1,2,2,1,1,5,1,2,1,2,2,1,
		2,2,2,1,1,2,1,1,2,1,2,1,    2,2,2,1,2,1,2,1,1,2,1,2,    1,2,2,1,6,1,2,1,2,1,1,2,    1,2,1,2,2,1,2,2,1,2,1,2,    1,1,2,1,2,1,2,2,1,2,2,1,
		2,1,4,1,2,1,2,1,2,2,2,1,    2,1,1,2,1,1,2,1,2,2,2,1,    2,2,1,1,2,1,4,1,2,2,1,2,    2,2,1,1,2,1,1,2,1,2,1,2,    2,2,1,2,1,2,1,1,2,1,2,1,
		2,2,1,2,2,4,1,1,2,1,2,1,    2,1,2,2,1,2,2,1,2,1,1,2,    1,2,1,2,1,2,2,1,2,2,1,2,    1,1,2,4,1,2,1,2,2,1,2,2,    1,1,2,1,1,2,1,2,2,2,1,2,
		2,1,1,2,1,1,2,1,2,2,1,2,    2,5,1,2,1,1,2,1,2,1,2,2,    2,1,2,1,2,1,1,2,1,2,1,2,    2,2,1,2,1,2,3,2,1,2,1,2,    2,1,2,2,1,2,1,1,2,1,2,1,
		2,1,2,2,1,2,1,2,1,2,1,2,    1,2,1,2,4,2,1,2,1,2,1,2,    1,2,1,1,2,2,1,2,2,1,2,2,    1,1,2,1,1,2,1,2,2,1,2,2,    2,1,4,1,1,2,1,2,1,2,2,2,
		1,2,1,2,1,1,2,1,2,1,2,2,    2,1,2,1,2,1,1,5,2,1,2,2,    1,2,2,1,2,1,1,2,1,2,1,2,    1,2,2,1,2,1,2,1,2,1,2,1,    2,1,2,1,2,5,2,1,2,1,2,1,
		2,1,2,1,2,1,2,2,1,2,1,2,    1,2,1,1,2,1,2,2,1,2,2,1,    2,1,2,3,2,1,2,1,2,2,2,1,    2,1,2,1,1,2,1,2,1,2,2,2,    1,2,1,2,1,1,2,1,1,2,2,1,
		2,2,5,2,1,1,2,1,1,2,2,1,    2,2,1,2,2,1,1,2,1,2,1,2,    1,2,2,1,2,1,5,2,1,2,1,2,    1,2,1,2,1,2,2,1,2,1,2,1,    2,1,1,2,2,1,2,1,2,2,1,2,
		1,2,1,1,5,2,1,2,2,2,1,2,    1,2,1,1,2,1,2,1,2,2,2,1,    2,1,2,1,1,2,1,1,2,2,2,1,    2,2,1,5,1,2,1,1,2,2,1,2,    2,2,1,2,1,1,2,1,1,2,1,2,
		2,2,1,2,1,2,1,5,2,1,1,2,    2,1,2,2,1,2,1,2,1,2,1,1,    2,2,1,2,1,2,2,1,2,1,2,1,    2,1,1,2,1,6,1,2,2,1,2,1,    2,1,1,2,1,2,1,2,2,1,2,2,
		1,2,1,1,2,1,1,2,2,1,2,2,    2,1,2,3,2,1,1,2,2,1,2,2,    2,1,2,1,1,2,1,1,2,1,2,2,    2,1,2,2,1,1,2,1,1,5,2,2,    1,2,2,1,2,1,2,1,1,2,1,2,
		1,2,2,1,2,2,1,2,1,2,1,1,    2,1,2,2,1,5,2,2,1,2,1,2,    1,1,2,1,2,1,2,2,1,2,2,1,    2,1,1,2,1,2,1,2,2,1,2,2,    1,2,1,1,5,1,2,1,2,2,2,2,
		1,2,1,1,2,1,1,2,1,2,2,2,    1,2,2,1,1,2,1,1,2,1,2,2,    1,2,5,2,1,2,1,1,2,1,2,1,    2,2,2,1,2,1,2,1,1,2,1,2,    1,2,2,1,2,2,1,5,2,1,1,2,
		1,2,1,2,2,1,2,1,2,2,1,2,    1,1,2,1,2,1,2,2,1,2,2,1,    2,1,1,2,3,2,2,1,2,2,2,1,    2,1,1,2,1,1,2,1,2,2,2,1,    2,2,1,1,2,1,1,2,1,2,2,1,

		// 2001년 ~ 2043년
		2,2,2,3,2,1,1,2,1,2,1,2,    2,2,1,2,1,2,1,1,2,1,2,1,    2,2,1,2,2,1,2,1,1,2,1,2,    1,5,2,2,1,2,1,2,1,2,1,2,    1,2,1,2,1,2,2,1,2,2,1,1,
		2,1,2,1,2,1,5,2,2,1,2,2,    1,1,2,1,1,2,1,2,2,2,1,2,    2,1,1,2,1,1,2,1,2,2,1,2,    2,2,1,1,5,1,2,1,2,1,2,2,    2,1,2,1,2,1,1,2,1,2,1,2,
		2,1,2,2,1,2,1,1,2,1,2,1,    2,1,6,2,1,2,1,1,2,1,1,2,    2,1,2,2,1,2,1,2,1,2,1,2,    1,2,1,2,1,2,1,2,5,2,1,2,    1,2,1,1,2,1,2,2,2,1,2,2,
		1,1,2,1,1,2,1,2,2,1,2,2,    2,1,1,2,3,2,1,2,1,2,2,2,    1,2,1,2,1,1,2,1,2,1,2,2,    2,1,2,1,2,1,1,2,1,2,1,2,    2,1,2,5,2,1,1,2,1,2,1,2,
		1,2,2,1,2,1,2,1,2,1,2,1,    2,1,2,1,2,2,1,2,1,2,1,2,    1,5,2,1,2,1,2,2,1,2,1,2,    1,2,1,1,2,1,2,2,1,2,2,1,    2,1,2,1,1,5,2,1,2,2,2,1,
		2,1,2,1,1,2,1,2,1,2,2,2,    1,2,1,2,1,1,2,1,1,2,2,2,    1,2,2,1,5,1,2,1,1,2,2,1,    2,2,1,2,2,1,1,2,1,1,2,2,    1,2,1,2,2,1,2,1,2,1,2,1,
		2,1,5,2,1,2,2,1,2,1,2,1,    2,1,1,2,1,2,2,1,2,2,1,2,    1,2,1,1,2,1,5,2,2,2,1,2,    1,2,1,1,2,1,2,1,2,2,2,1,    2,1,2,1,1,2,1,1,2,2,1,2,
		2,2,1,2,1,4,1,1,2,1,2,2,    2,2,1,2,1,1,2,1,1,2,1,2,    2,2,1,2,1,2,1,2,1,1,2,1,    2,2,1,2,5,2,1,2,1,2,1,1,    2,1,2,2,1,2,2,1,2,1,2,1,
		2,1,1,2,1,2,2,1,2,2,1,2,    1,5,1,2,1,2,1,2,2,2,1,2,    1,2,1,1,2,1,1,2,2,1,2,2
		);
		$this->md  = array(31, 0, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
	}
	private function febdays($sy) {
		if(($sy%100 != 0 && $sy%4 ==0) || $sy%400 ==0) $this->md[1] = 29;
		else $this->md[1] = 28;
		return array($this->md[1]);
	}
	private function alltd($sy) {
		$td = 0;
		for($i=1841;$i<=$sy-1;$i++) {
			list($this->md[1]) = $this->febdays($i);
			for($j=0; $j<12; $j++) $td += $this->md[$j];
		}
		$td -= 22;
		return array($td);
	}
	private function soltolun($sy, $sm, $sd) {
		list($td) = $this->alltd($sy);
		list($this->md[1]) = $this->febdays($sy);
		for($i=0; $i<$sm-1; $i++) $td += $this->md[$i];
		$td += $sd;
		$month = 0;
		$temptd = $td;
		while($temptd>0) {
			$yoon = '';
			switch($this->kk[$month]) {
				case 1: $mm = 29; break;
				case 2: $mm = 30; break;
				case 3:
					list($mm, $ymm) = array(29, 28);
					if($temptd > 29) list($temptd, $yoon) = array($temptd-29, '*');
					break;
				case 4:
					list($mm, $ymm) = array(29, 30);
					if($temptd > 30) list($temptd, $yoon) = array($temptd-30, '*');
					break;
				case 5:
					list($mm, $ymm) = array(30, 29);
					if($temptd > 29) list($temptd, $yoon) = array($temptd-29, '*');
					break;
				case 6:
					list($mm, $ymm) = array(30, 30);
					if($temptd > 30) list($temptd, $yoon) = array($temptd-30, '*');
					break;
			}
			$temptd -= $mm;
			if((++$month%12)==1) $ly += 1;
		}
		if($temptd <= 0) $temptd += ($yoon=='*') ? $ymm : $mm;
		$ly += 1840;
		$lm = $month % 12;
		if($lm==0) $lm = 12;
		$ld = $temptd;
		return array('year'=>$ly, 'month'=>$lm, 'day'=>$ld, 'leap'=>$yoon);
	}

	// 일정 반환
	public function print_contents($calendar, $entry) {
		$month_stamp = $_GET['date'] ? strtotime($_GET['date'].'-01') : strtotime(date('Y-m-01'));
		$_GET['date'] = date('Y-m', $month_stamp); // verify
		list($sdate, $edate) = array($_GET['date'].'-01', sprintf('%s-%02d', $_GET['date'], date('t', $month_stamp)));

		$items = array();
		$where = q(" where calendar=%d and (sdate between '%s' and '%s' or edate between '%s' and '%s')", $calendar, $sdate, $edate, $sdate, $edate);
		$_datas = $this->query("select * from $this->table".$where);
		while($_rows = $this->fetch($_datas)) {
			// loop range reset
			if($_rows['sdate']<$sdate) $_rows['sdate'] = $sdate;
			if($_rows['edate']>$edate) $_rows['edate'] = $edate;
			$xdates = explode(',', $_rows['xdates']); // 제외날짜

			if($_rows['sdate']<$_rows['edate']) {
				$vdate = $_rows['sdate'];
				$xdays = explode(',', $_rows['xdays']);
				while(1) {
					$xday = false;
					$_rows['date'] = $vdate;
					if(in_array($vdate, $xdates)) $xday = true;
					if($_rows['xdays']) {
						$week = strtolower(date('D', strtotime($vdate))); // mon ~ sun
						$xday = in_array($week, $xdays);
						if($xday==false && in_array('rest', $xdays)) { // 국가공휴일
							$rest_day = $this->check_days($vdate);
							if($rest_day[0]) $xday = true;
						}
					}
					if(!$xday) $items[$vdate][$_rows['sdate']][$_rows['no']] = $_rows;
					if($vdate==$_rows['edate']) break;
					$vdate= date('Y-m-d', strtotime($vdate.' 1 day'));
				}
			}
			else if(!in_array($_rows['sdate'], $xdates)) {
				$_rows['date'] = $_rows['sdate'];
				$items[$_rows['sdate']][$_rows['sdate']][$_rows['no']] = $_rows;
			}
		}
		// 달력 데이터 구성
		$datas = array();
		$entry['spaces'] = $spaces = date('w', $month_stamp);
		if($entry['skin_type']!='list') {
			if($spaces) foreach(range(1, $spaces) as $x) array_push($datas, array('no'=>'', 'day'=>'')); // empty_cell
		}
		foreach(range(1, date('t', $month_stamp)) as $day) {
			$rows['day'] = $day;
			$rows['items'] = array();
			$_items = $items[$_GET['date'].'-'.sprintf('%02d', $day)];
			if($_items) {
				ksort($_items);
				foreach($_items as $key=>$schedules) $rows['items'] += $schedules;
			}
			array_push($datas, $rows);
		}
		return fetch_contents($datas, $entry, array($this, '_s165'));
	}
	public function _s165($bind) {
		extract($bind);
		if(count($rows['items'])) $rows['on_schedule'] = fetch_contents($rows['items'], $schedule_entry, array($this, '_s230'));

		if(isset($on_week_col) && $rank==1) {
			$rows['week_row'] = 1;
			if($skin_type=='list') {
				$rows['rowspan'] = 7 - $spaces;
				$rows['on_week_col'] = fetch_skin($rows, $on_week_col);
			}
			else {
				$skin = $on_week_col.$skin;
			}
		}
		if(!$rows['day']) $rows['bgcolor'] = $bgcolors['off'];
		else {
			$date = sprintf('%s-%02d', $_GET['date'], $rows['day']);
			$rest_day = $this->check_days($date);
			if($view_holyday=='yes') {
				if($rows['day'] && $rest_day[1]) $rows['on_day_text'] = fetch_skin(array('day_text'=>$rest_day[1]), $on_day_text); // 국경일
			}

			$week_num = date('w', strtotime($date));
			if($week_num==0 || $rest_day[0]) $rows['color'] = $colors['rest']; // 공휴일
			else if($week_num==6) $rows['color'] = $colors['sat']; // 토

			if(isset($on_week_col) && $rank>1 && $week_num==0) {
				if($skin_type=='list') {
					$rows['rowspan'] = 7;
					$rows['week_row'] = ceil(($rank + $spaces) / 7);
					$rows['on_week_col'] = fetch_skin($rows, $on_week_col);
				}
				else {
					$rows['week_row'] = ceil($rank / $times);
					$skin = $on_week_col.$skin;
				}
			}
			$rows['date'] = $date;
			$rows['bgcolor'] = (date('Y-m-d')==$date) ? $bgcolors['today'] : $bgcolors['on']; // 오늘
			$rows['on_date'] = fetch_skin($rows, $on_date);
		}
		return array($rows, $skin);
	}
	function _s230($bind) {
		extract($bind);
		if(in_array($print_type, array('cate', 'both')) && count($cate_texts)) {
			$rows['cate_text'] = $cate_texts[$rows['cate']];
			$rows['on_cate'] = fetch_skin($rows, $on_cate);
		}
		if($print_type=='cate') {
			$rows['on_cate'] = $rows['cate_text'];
			unset($rows['subject']);
		}
		return array($rows, $skin);
	}

	// 일정등록
	function set_schedule() {
		$_vals['calendar'] = $_POST['calendar'];
		$_vals['subject'] = $_POST['subject'];
		$_vals['cate'] = $_POST['cate'];
		$_vals['sdate'] = $_POST['sdate'];
		$_vals['edate'] = $_POST['edate'];
		$_vals['xdays'] = count($_POST['xdays']) ? implode(',', $_POST['xdays']) : null;
		$_vals['xdates'] = count($_POST['xdates']) ? implode(',', $_POST['xdates']) : null; // 제외날짜

		// 유령데이터 체크 - 토요일, 일요일, 국경일, 제외날짜
		//@======

		$_vals['comment'] = trim(strip_tags($_POST['comment']));
		$values = $this->change_query_string($_vals);
		if($_POST['no']) $this->query("update $this->table set $values where no=$_POST[no]");
		else $this->query("insert into $this->table set $values");
	}

	// 일정반환
	function get_schedule($no) {
		$rows = $this->queryFetch("select * from $this->table".q(" where no=%d", $no));
		return $rows;
	}

	// 오프 일정인지 반환
	function off_schedule($rows, $xdate='', $xday='') {
		//
		return false;
	}

	// 제외날짜추가
	function exclude_date($no, $date) {
		$rows = $this->get_schedule($no);
		if($this->off_schedule($rows, $date)) $this->del_schedule($no);
		else {
			if($rows['sdate']==$date) {
				$date = date('Y-m-d', strtotime($date.' 1 day'));
				$this->query("update $this->table set sdate='$date' where no=$no");
			}
			else if($rows['edate']==$date) {
				$date = date('Y-m-d', strtotime($date.' -1 day'));
				$this->query("update $this->table set edate='$date' where no=$no");
			}
			else {
				$xdates = explode(',', $rows['xdates']);
				$xdates[] = $date;
				$_vals['xdates'] = implode(',', array_diff(array_unique($xdates), array('', null)));
				$values = $this->change_query_string($_vals);
				$this->query("update $this->table set $values where no=$no");
			}
		}
	}

	// 일정삭제
	function del_schedule($nos) {
		$nos = implode(',', array_diff(array_unique(explode('__', $nos)), array('', null)));
		if($nos) $this->query("delete from $this->table where no in ($nos)");
	}

}
?>