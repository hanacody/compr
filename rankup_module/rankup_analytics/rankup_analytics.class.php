<?php
/**
 * ��ũ�� �м��� for Google Analytics V1.0
 *@author: kurokisi
 *@authDate: 2011.10.10
 */
class rankup_analytics extends rankup_util {

	var $analytics_table = 'rankup_analytics';
	var $config_table = 'rankup_analytics_config';
	var $update_hours = 2; // ���� �����ʹ� 2�ð� ������ ����
	var $delete_month = 1; // �ֱ� ������Ʈ��¥�� 1������ ���� �����ʹ� ����
	var $configs = array();
	var $infos = null;

	function rankup_analytics() {
		parent::rankup_util();
		$this->configs = $this->get_configs();
		if($_GET['kind'] && $_GET['sdate'] && $_GET['edate']) $this->set_infos($_GET['kind'], $_GET['sdate'], $_GET['edate']);
		$this->clear_logs();
	}

	function get_configs() {
		$rows = $this->queryFetch("select * from $this->config_table");
		return $rows;
	}

	function set_configs() {
		$_vals['google_id'] = $_POST['google_id'];
		$_vals['google_pass'] = $_POST['google_pass'];
		$_vals['google_profile_id'] = $_POST['google_profile_id'];
		$_vals['google_scripts'] = $_POST['google_scripts'];
		$values = $this->change_query_string($_vals);
		if($this->configs) $this->query("update $this->config_table set $values");
		else $this->query("insert $this->config_table set $values");
	}

	function print_period_buttons($fields) {
		$fields = explode('|', $fields);
		$option_items = array(
			'today' => '���ó�¥',
			'-1 week' => '�ֱ�1����',
			'-15 day' => '�ֱ�15��',
			'-1 month' => '�ֱ�1����'
		);
		if(count($fields)>1) $add_base = ", \$('$fields[1]')";
		foreach($option_items as $option_key=>$option_value) {
			$contents .= "
			<span style='float:left'><input type='button' onClick=\"rankup_calendar.set_date('$option_key', \$('$fields[0]')$add_base)\" value=\"$option_value\"></span>";
		}
		return $contents;
	}

	/* �α��ʱ�ȭ 2012-03-22 add  */
	function reset_logs() {
		$this->query("TRUNCATE TABLE $this->analytics_table");
	}

	function clear_logs() {
		$this->query("delete from $this->analytics_table where update_time < date_sub(now(), interval ".$this->delete_month." month)");
		if(mysql_affected_rows()) $this->query("optimize table $this->analytics_table");
	}

	function set_infos($kind, $sdate, $edate) {
		$rows = $this->queryFetch("select * from $this->analytics_table where kind='$kind' and sdate='$sdate' and edate='$edate'");
		if(!$rows['no']) $rows = null;
		else {
			$stamp = strtotime($rows['update_time']);
			if(date('Y-m-d')==date('Y-m-d', $stamp)) { // ���ó�¥ �α� �˻���
				$hours = floor((time()-strtotime($rows['update_time'])) / 3600);
				if($hours>$this->update_hours) $rows = null; // Ư�� �ð������θ� �ε�
			}
			else if(strtotime($rows['edate'].' 23:59:59')>$stamp) $rows = null;
			if($rows!=null) {
				$rows['titles'] = unserialize($rows['titles']);
				$rows['datas'] = unserialize($rows['datas']);
			}
		}
		$this->infos = $rows;
	}

	function keep($_vals) {
		$rows = $this->queryFetch("select * from $this->analytics_table where kind='$_vals[kind]' and sdate='$_vals[sdate]' and edate='$_vals[edate]'");
		if($rows['no']) {
			$_vals['update_time'] = date('Y-m-d H:i:s');
			$values = $this->change_query_string($_vals);
			$this->query("update $this->analytics_table set $values where no=$rows[no]");
		}
		else {
			$_vals['regist_time'] = date('Y-m-d H:i:s');
			$_vals['update_time'] = date('Y-m-d H:i:s');
			$values = $this->change_query_string($_vals);
			$this->query("insert $this->analytics_table set $values");
		}
	}

	function draw_chart($kind, $shape, $width, $height, $loader) {
		$this->set_infos($kind, $_GET['sdate'], $_GET['edate']);
		$recent = ($this->infos===null) ? 'no' : 'yes';
		return sprintf('<div id="%s_chart" class="chart" kind="%s" shape="%s_chart" style="width:%dpx;height:%dpx" recent="%s">%s</div>', $kind, $kind, $shape, $width, $height, $recent, $loader);
	}
}
?>