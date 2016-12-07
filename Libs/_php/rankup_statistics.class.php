<?php
class rankup_statistics extends rankup_util {
	var $base_url = null;					// �����
	var $base_dir = null;					// ������
	var $log_table = "rankuplog_total"; //�α� ���̺�
	var $member_table = "rankup_member"; //��� ���̺�
	var $member_table2 = "rankup_member_extend"; //��� ���̺�
	//var $shop_introduce_data_table = "rankup_shop_introduce_data"; //�������� ���̺�
	var $payment_table = "rankup_payment"; //�������̺�
	var $partition_num = 5; //���� ����
	var $month = array();
	var $year = array();

	function rankup_statistics() {
		parent::rankup_util();
		$this->base_url = rankup_basic::base_url();
		$this->base_dir = rankup_basic::base_dir();
		@include $this->base_dir."Libs/_php/service_setting.inc.php";
		//��������
	}

	//�׷��� ����
	function graph($datas, $mode) {
		switch($mode) {
			case "month":
				//������ �迭 ����
				foreach(range(1,12) as $no) {
					$num = sprintf('%02d', $no);
					$month[$num] = $datas[$num]['total_payments'] ?  $datas[$num]['total_payments'] : 0;
					$_bottom_menu .= "<td align='center' class='gray_bg'>". $num . "��</td>";
				}
				$maxData = max($month);
				//�����޴�
				$bottom_menu = "<tr height='30px'>$_bottom_menu</tr>";
				$rows_pan = $this->partition_num +1;
				$val = $val < 0 ? 0 : $val;
				foreach(range(1,12) as $no) {
					$num = sprintf('%02d', $no);
					$_stic = @round(($month[$num] / $maxData) *100);
					$_stic = $_stic * 1.8;
					$_stic_data = $num."�� ( ". number_format($month[$num]) ."�� )";
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
						$_bottom_menu .= "<td align='center' class='gray_bg'>". $num . "��</td>";
					}
				}
				//�����޴�
				$bottom_menu = "<tr height='30px'>$_bottom_menu</tr>";
				$rows_pan = $this->partition_num +1;
				$val = $val < 0 ? 0 : $val;
				//for($i = 1; $i <= 12; $i++) {
				foreach(range(1,12) as $no) {
					$num = sprintf('%02d', $no);
					//$_stic = round(($month[$num] / $maxData) *100);
					//$_stic = $_stic * 1.8;
					$_stic_data = $num."�� ( ". number_format($month[$num]) ."�� )";
					$_contents .= "
					<td align='center'><img src='{$this->base_url}Libs/_images/graph_statistics.gif' width='15' height='$_stic' alt='$_stic_data'></td>";
				}
				$contetns .= "	<tr height='200'>$_contents</tr>";
				$contetns .= $bottom_menu;
				break;
		}
		return $contetns;
	}
	//���
	function statistics($datas, $mode = '') {
		//���ǹ�
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
		//���ҵ��
		//$totalCount['shop'] = mysql_num_rows($this->query("select no from $this->shop_introduce_data_table where open='yes' $shop_where"));

		//���� �ݾ� ����
		$payment_infos = $this->queryFetch("select count(no) as pay, sum(goods_total_price) as payments from $this->payment_table where is_payed='yes' and (pay_company is NULL or pay_company!='admin') $payment_where");
		$totalCount['pay'] = $payment_infos['pay'];
		$totalCount['payments'] = $payment_infos['payments'];

		$payment_datas = $this->query("select goods_name, goods_info, date_format(payed_date, '$paydate_format') as pay_day from $this->payment_table where is_payed='yes' and (pay_company is NULL or pay_company!='admin') $payment_where");

		//���� ���� �迭ȭ
		if($this->chkRes($payment_datas)) {
			while($payment_infos = $this->fetch($payment_datas)) {
				$goods_info = unserialize($payment_infos['goods_info']);
				foreach(explode(",", $payment_infos['goods_name']) as $service) {
					//������谡 �ƴҰ��
					if($mode != "total") {
						list($period, $payments) = explode("|", $goods_info["payments_".$service]);
						$total_payments += $payments;
						$service_infos[$payment_infos['pay_day']][$service]['count']++; // �����Ǽ�
						$service_infos[$payment_infos['pay_day']][$service]['payments'] += $payments; // �����ݾ�
						$service_infos[$payment_infos['pay_day']]['total_payments'] += $payments;
					}
					else {
						list($period, $payments) = explode("|", $goods_info["payments_".$service]);
						$total_payments += $payments;
						$service_infos[$service]['count']++; // �����Ǽ�
						$service_infos[$service]['payments'] += $payments; // �����ݾ�
						$service_infos['total_payments'] += $payments;
					}
				}
			}
		}
		//�������� ���� �ۼ�
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
						$_date_options .= "<option value='$date_infos[pay_date]'>${year}�� ${month}��</option>";
					}
					else if($mode == "month") {
						if(empty($date_infos['pay_date'])) continue;
						$_date_options .= "<option value='$date_infos[pay_date]'>{$date_infos[pay_date]}��</option>";
					}
				}
			}
		}
		return  $mode=='total' ? array($totalCount, $content) : array($totalCount,$_date_options,$content, $graph);
	}
	//��� ������ ������ �����
	function formalize_statistics_contents($datas, $service_infos, $mode) {
		switch($mode) {
			case "day":
			case "month":
				if($mode == "day") {
					$cmd_name = '��';
					$where = date("t", strtotime("{$datas[edate]}-01"));
				}
				else {
					$cmd_name = '��';
					$where = 12;
				}
				for($i=1; $i<=$where; $i++) {
					$num = sprintf('%02d', $i);
					if($mode == "day") {
						if(date("D", strtotime("{$datas[edate]}-$num")) =="Sat") $_class = "bgcolor='#F4F8F9'";
						else if(date("D", strtotime("{$datas[edate]}-$num")) =="Sun") $_class = "bgcolor='#FEFAF5'";
						else $_class = "";
					}
					//��Ż ��� �ݾ�
					$total = $service_infos[$num]['total_payments'] ?  number_format($service_infos[$num]['total_payments'])."��" : "��";
					foreach($this->service_kinds as $service => $rows) {
						$service_money = $service."_money";
						//�����̾� �ݾ�
						$$service = $service_infos[$num][$service]['count'] ? number_format($service_infos[$num][$service]['count'])."��": "��";
						$$service_money = $service_infos[$num][$service]['payments'] ? number_format($service_infos[$num][$service]['payments'])."��" : "��";
					}
					$contents .= "
						<tr $_class>
							<td rowspan='2'>{$i}$cmd_name</td>
							<td>�Ǽ�</td>
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
							<td>�ݾ�</td>
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
					//���ں� ��� �ݾ�
					$total = $val['total_payments'] ?  number_format($val['total_payments'])."��" : "��";
					foreach($this->service_kinds as $service => $rows) {
						$service_money = $service."_money";
						//�����̾� �ݾ�
						$$service = $val[$service]['count'] ? number_format($val[$service]['count'])."��": "��";
						$$service_money = $val[$service]['payments'] ? number_format($val[$service]['payments'])."��" : "��";
					}
					$contents .= "
						<tr>
							<td rowspan='2'>{$key}��</td>
							<td>�Ǽ�</td>
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
							<td>�ݾ�</td>
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
						//�����̾� �ݾ�
					$$service = $service_infos[$service]['count'] ? number_format($service_infos[$service]['count'])."��": "��";
					$$service_money = $service_infos[$service]['payments'] ? number_format($service_infos[$service]['payments'])."��" : "��";
				}
				$contents .= "
					<tr>
						<td rowspan='2'>��ü</td>
						<td>�Ǽ�</td>
						<td>".$premium."</td>
						<td>".$best."</td>
						<td>".$recom."</td>
						<td>".$special."</td>
						<td>".$minihome."</td>
						<td>".$bestcoupon."</td>
						<td>".$line."</td>
						<td rowspan='2'>".number_format($service_infos['total_payments'])."��</td>
					</tr>
					<tr>
						<td>�ݾ�</td>
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