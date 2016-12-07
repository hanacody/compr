<?php
class rankup_statistics extends rankup_util {
	var $base_url = null;					// 상대경로
	var $base_dir = null;					// 절대경로
	var $log_table = "rankuplog_total"; //로그 테이블
	var $member_table = "rankup_member"; //멤버 테이블
	var $member_table2 = "rankup_member_extend"; //멤버 테이블
	//var $shop_introduce_data_table = "rankup_shop_introduce_data"; //업소정보 테이블
	var $payment_table = "rankup_payment"; //결제테이블
	var $partition_num = 5; //분할 갯수
	var $month = array();
	var $year = array();

	function rankup_statistics() {
		parent::rankup_util();
		$this->base_url = rankup_basic::base_url();
		$this->base_dir = rankup_basic::base_dir();
		@include $this->base_dir."Libs/_php/service_setting.inc.php";
		//서비스종류
	}

	//그래프 생성
	function graph($datas, $mode) {
		switch($mode) {
			case "month":
				//월별로 배열 정리
				foreach(range(1,12) as $no) {
					$num = sprintf('%02d', $no);
					$month[$num] = $datas[$num]['total_payments'] ?  $datas[$num]['total_payments'] : 0;
					$_bottom_menu .= "<td align='center' class='gray_bg'>". $num . "월</td>";
				}
				$maxData = max($month);
				//하위메뉴
				$bottom_menu = "<tr height='30px'>$_bottom_menu</tr>";
				$rows_pan = $this->partition_num +1;
				$val = $val < 0 ? 0 : $val;
				foreach(range(1,12) as $no) {
					$num = sprintf('%02d', $no);
					$_stic = @round(($month[$num] / $maxData) *100);
					$_stic = $_stic * 1.8;
					$_stic_data = $num."월 ( ". number_format($month[$num]) ."원 )";
					$_contents .= "
					<td align='center'><img src='{$this->base_url}Libs/_images/graph_statistics.gif' width='15' height='$_stic' alt='$_stic_data'></td>";
				}
				$contetns .= "	<tr height='200'>$_contents</tr>";
				$contetns .= $bottom_menu;
				break;
			case "year":
				if($this->chkRes($datas)) {
					foreach($datas as  $key => $val) {
						$month[$num] = $datas[$num]['total_payments'] ?  $datas[$num]['total_payments'] : 0;
						$sort_momth[$num] = $datas[$num]['total_payments'] ?  $datas[$num]['total_payments'] : 0;
						$_bottom_menu .= "<td align='center' class='gray_bg'>". $num . "월</td>";
					}
				}
				//하위메뉴
				$bottom_menu = "<tr height='30px'>$_bottom_menu</tr>";
				$rows_pan = $this->partition_num +1;
				$val = $val < 0 ? 0 : $val;
				//for($i = 1; $i <= 12; $i++) {
				foreach(range(1,12) as $no) {
					$num = sprintf('%02d', $no);
					//$_stic = round(($month[$num] / $maxData) *100);
					//$_stic = $_stic * 1.8;
					$_stic_data = $num."월 ( ". number_format($month[$num]) ."원 )";
					$_contents .= "
					<td align='center'><img src='{$this->base_url}Libs/_images/graph_statistics.gif' width='15' height='$_stic' alt='$_stic_data'></td>";
				}
				$contetns .= "	<tr height='200'>$_contents</tr>";
				$contetns .= $bottom_menu;
				break;
		}
		return $contetns;
	}
	//통계
	function statistics($datas, $mode = '') {
		//조건문
		switch($mode) {
			case "day":
				$paydate_format = "%d";
				$_search_format = "%Y-%m";
				$addWhere = " and date_format(wdate, '%Y-%m') = '$datas[edate]'";
				$memWhere = " and date_format(m1.wdate, '%Y-%m') = '$datas[edate]'";
				$shop_where = " and date_format(write_date, '%Y-%m') = '$datas[edate]'";
				$payment_where = " and date_format(payed_date, '%Y-%m') = '$datas[edate]'";
				break;
			case "month":
				$paydate_format = "%m";
				$_search_format = "%Y";
				$addWhere = " and date_format(wdate, '%Y') = '$datas[edate]'";
				$memWhere = " and date_format(m1.wdate, '%Y') = '$datas[edate]'";
				$shop_where = " and date_format(write_date, '%Y') = '$datas[edate]'";
				$payment_where = " and date_format(payed_date, '%Y') = '$datas[edate]'";
				break;
			case "year":
				$paydate_format = "%Y";
				$payment_where = " order by payed_date desc";
				break;
			case "total":
				if($datas['use_date']=="on") $addWhere = " and date_format(wdate, '%Y-%m-%d') between '$datas[sdate]' and '$datas[edate]'";
				if($datas['use_date']=="on") $memWhere = " and date_format(m1.wdate, '%Y-%m-%d') between '$datas[sdate]' and '$datas[edate]'";
				if($datas['use_date']=="on") $shop_where = " and date_format(write_date, '%Y-%m-%d') between '$datas[sdate]' and '$datas[edate]'";
				if($datas['use_date']=="on") $payment_where = " and date_format(payed_date, '%Y-%m-%d') between '$datas[sdate]' and '$datas[edate]'";
				$paydate_format = "%Y-%m-%d";
				break;
		}
		$totalCount['con'] =$this->queryR("select num from $this->log_table where num $addWhere");
		$totalCount['member'] = mysql_num_rows($this->query("select no from $this->member_table as m1 LEFT OUTER JOIN $this->member_table2 as m2 ON m1.uid = m2.uid where m2.secession='no' and (m1.uid REGEXP '='!=1) $memWhere"));
		//업소등록
		//$totalCount['shop'] = mysql_num_rows($this->query("select no from $this->shop_introduce_data_table where open='yes' $shop_where"));

		//결제 금액 정보
		$payment_infos = $this->queryFetch("select count(no) as pay, sum(goods_total_price) as payments from $this->payment_table where is_payed='yes' and (pay_company is NULL or pay_company!='admin') $payment_where");
		$totalCount['pay'] = $payment_infos['pay'];
		$totalCount['payments'] = $payment_infos['payments'];

		$payment_datas = $this->query("select goods_name, goods_info, date_format(payed_date, '$paydate_format') as pay_day from $this->payment_table where is_payed='yes' and (pay_company is NULL or pay_company!='admin') $payment_where");

		//결제 정보 배열화
		if($this->chkRes($payment_datas)) {
			while($payment_infos = $this->fetch($payment_datas)) {
				$goods_info = unserialize($payment_infos['goods_info']);
				foreach(explode(",", $payment_infos['goods_name']) as $service) {
					//종합통계가 아닐경우
					if($mode != "total") {
						list($period, $payments) = explode("|", $goods_info["payments_".$service]);
						$total_payments += $payments;
						$service_infos[$payment_infos['pay_day']][$service]['count']++; // 결제건수
						$service_infos[$payment_infos['pay_day']][$service]['payments'] += $payments; // 결제금액
						$service_infos[$payment_infos['pay_day']]['total_payments'] += $payments;
					}
					else {
						list($period, $payments) = explode("|", $goods_info["payments_".$service]);
						$total_payments += $payments;
						$service_infos[$service]['count']++; // 결제건수
						$service_infos[$service]['payments'] += $payments; // 결제금액
						$service_infos['total_payments'] += $payments;
					}
				}
			}
		}
		//결제정보 내용 작성
		if($this->chkRes($service_infos)) {
			$content = $this->formalize_statistics_contents($datas, $service_infos, $mode);
		}
		if($mode == "month" || $mode == "year") {
			$graph = $this->graph($service_infos, $mode);
		}
		if($mode != "total") {
			$date_datas = $this->query("select date_format(payed_date, '$_search_format') as pay_date from $this->payment_table where is_payed='yes' and (pay_company is NULL or pay_company!='admin') group by pay_date order by pay_date desc");
			if($this->chkRes($date_datas)) {
				while($date_infos = $this->fetch($date_datas)) {
					if($mode == "day") {
						list($year, $month) = explode("-", $date_infos['pay_date']);
						if(!$year && !$month) continue;
						$_date_options .= "<option value='$date_infos[pay_date]'>${year}년 ${month}월</option>";
					}
					else if($mode == "month") {
						if(empty($date_infos['pay_date'])) continue;
						$_date_options .= "<option value='$date_infos[pay_date]'>{$date_infos[pay_date]}년</option>";
					}
				}
			}
		}
		return  $mode=='total' ? array($totalCount, $content) : array($totalCount,$_date_options,$content, $graph);
	}
	//통계 보여줄 페이지 만들기
	function formalize_statistics_contents($datas, $service_infos, $mode) {
		switch($mode) {
			case "day":
			case "month":
				if($mode == "day") {
					$cmd_name = '일';
					$where = date("t", strtotime("{$datas[edate]}-01"));
				}
				else {
					$cmd_name = '월';
					$where = 12;
				}
				for($i=1; $i<=$where; $i++) {
					$num = sprintf('%02d', $i);
					if($mode == "day") {
						if(date("D", strtotime("{$datas[edate]}-$num")) =="Sat") $_class = "bgcolor='#F4F8F9'";
						else if(date("D", strtotime("{$datas[edate]}-$num")) =="Sun") $_class = "bgcolor='#FEFAF5'";
						else $_class = "";
					}
					//토탈 통계 금액
					$total = $service_infos[$num]['total_payments'] ?  number_format($service_infos[$num]['total_payments'])."원" : "·";
					foreach($this->service_kinds as $service => $rows) {
						$service_money = $service."_money";
						//프리미엄 금액
						$$service = $service_infos[$num][$service]['count'] ? number_format($service_infos[$num][$service]['count'])."건": "·";
						$$service_money = $service_infos[$num][$service]['payments'] ? number_format($service_infos[$num][$service]['payments'])."원" : "·";
					}
					$contents .= "
						<tr $_class>
							<td rowspan='2'>{$i}$cmd_name</td>
							<td>건수</td>
							<td>".$premium."</td>
							<td>".$best."</td>
							<td>".$recom."</td>
							<td>".$special."</td>
							<td>".$minihome."</td>
							<td>".$bestcoupon."</td>
							<td>".$line."</td>
							<td rowspan='2'>".$total."</td>
						</tr>
						<tr $_class>
							<td>금액</td>
							<td>".$premium_money."</td>
							<td>".$best_money."</td>
							<td>".$recom_money."</td>
							<td>".$special_money."</td>
							<td>".$minihome_money."</td>
							<td>".$bestcoupon_money."</td>
							<td>".$line_money."</td>
						</tr>
					";
				}
				break;
			case "year":
				foreach($service_infos as $key => $val) {
					//일자별 통계 금액
					$total = $val['total_payments'] ?  number_format($val['total_payments'])."원" : "·";
					foreach($this->service_kinds as $service => $rows) {
						$service_money = $service."_money";
						//프리미엄 금액
						$$service = $val[$service]['count'] ? number_format($val[$service]['count'])."건": "·";
						$$service_money = $val[$service]['payments'] ? number_format($val[$service]['payments'])."원" : "·";
					}
					$contents .= "
						<tr>
							<td rowspan='2'>{$key}년</td>
							<td>건수</td>
							<td>".$premium."</td>
							<td>".$best."</td>
							<td>".$recom."</td>
							<td>".$special."</td>
							<td>".$minihome."</td>
							<td>".$bestcoupon."</td>
							<td>".$line."</td>
							<td rowspan='2'>".$total."</td>
						</tr>
						<tr>
							<td>금액</td>
							<td>".$premium_money."</td>
							<td>".$best_money."</td>
							<td>".$recom_money."</td>
							<td>".$special_money."</td>
							<td>".$minihome_money."</td>
							<td>".$bestcoupon_money."</td>
							<td>".$line_money."</td>
						</tr>
					";
				}
				break;
			case "total":
				foreach($this->service_kinds as $service => $rows) {
					$service_money = $service."_money";
						//프리미엄 금액
					$$service = $service_infos[$service]['count'] ? number_format($service_infos[$service]['count'])."건": "·";
					$$service_money = $service_infos[$service]['payments'] ? number_format($service_infos[$service]['payments'])."원" : "·";
				}
				$contents .= "
					<tr>
						<td rowspan='2'>전체</td>
						<td>건수</td>
						<td>".$premium."</td>
						<td>".$best."</td>
						<td>".$recom."</td>
						<td>".$special."</td>
						<td>".$minihome."</td>
						<td>".$bestcoupon."</td>
						<td>".$line."</td>
						<td rowspan='2'>".number_format($service_infos['total_payments'])."원</td>
					</tr>
					<tr>
						<td>금액</td>
						<td>".$premium_money."</td>
						<td>".$best_money."</td>
						<td>".$recom_money."</td>
						<td>".$special_money."</td>
						<td>".$minihome_money."</td>
						<td>".$bestcoupon_money."</td>
						<td>".$line_money."</td>
					</tr>
					";
				break;
		}
		return $contents;
	}

}
?>