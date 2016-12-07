<?php
##������ ��Ʈ�θ� �ϴ� �Լ�
class rankup_control extends rankup_util{
	var $base_url; //��Ʈ�� ���������� ��� �� /main/index.html ���� src, href���� ���
	var $base_dir; //home���丮�� �������� �� ���, /home/test/public_html/Libs/dbcon.php ����, include, require, file(), opendir()��� ���

	//�޽����� �����ִ� �κ�
	var $insert_true = "���������� ����Ǿ����ϴ�.";
	var $insert_false = "������ ���� �Ͽ����ϴ�.";
	var $admin_only = "������ ���� ������ �Դϴ�.";

	var $member_only = "�˼��մϴ�. ���Բ����� ���� �α����� �Ǿ����� �ʽ��ϴ�.";
	var $company_only = "�˼��մϴ�. �߰����ڸ� �̿��� �� �ִ� �����Դϴ�.";
	var $personal_only = "�˼��մϴ�. ȸ���� �̿��� �� �ִ� �����Դϴ�.";

	var $delete_true = "���������� ���� �Ǿ����ϴ�.";
	var $delete_false = "������ �����Ͽ����ϴ�.";
	var $invalid_passwd = "�н����尡 ��Ȯ���� �ʽ��ϴ�.";
	var $change_true = "���������� ����Ǿ����ϴ�.";
	var $change_false = "������ �����Ͽ����ϴ�.";
	var $invalid_email = "�̸��� ������ ��Ȯ���� �ʽ��ϴ�.";
	var $empty_subject = "������ �Է��Ͽ� �ֽʽÿ�";
	var $empty_content = "������ �Է��Ͽ� �ֽʽÿ�";
	var $first_rank = "���� �ֻ��� �����Դϴ�.";
	var $last_rank = "���� ������ �����Դϴ�.";
	var $empty_list = "�Խù��� �������� �ʽ��ϴ�.";
	var $mail_send = "������ ���������� �߼۵Ǿ����ϴ�.";
	var $mail_fail = "���� �߼��� �����Ͽ����ϴ�.";
	var $approval_true = "������ �����Ǿ����ϴ�.";
	var $approval_false = "������ �����Ͽ����ϴ�.";

	function rankup_control() {
		parent::rankup_util();
		$this->base_url = rankup_basic::base_url();
		$this->base_dir = rankup_basic::base_dir();
	}

	//�α������� �ʾ����� ���â�� �����ִ� �Լ�
	function alert_company_js() {
		echo "
		<script type='text/javascript'>
		var move_company = function(msg, url) {
			alert(msg);
			location.href = url;
		}
		</script>";
	}

	function alert_company($type="") {
		if(!$type) $this->alert_company_js();
		$is_login = rankup_member::is_member();
		$val = rankup_member::get_value();

		if(!$is_login) $result="<a href=\"javascript:move_company('$this->company_only', '../member/login.html');\">";
		else if($val!='company')	$result="<a href=\"javascript:move_company('$this->company_only', '../member/login.html');\">";
		else $result="<a href='../company/pay_guide.html'>";
		return $result;
	}

	//��� ������ �ı�
	function delete_all_session(){
		$_SESSION=array();
		session_destroy();
	}

	//����������� üũ
	function is_demo(){
		return rankup_basic::is_demo();
	}

	//ȸ���� �ƴѰ�� �α��� �������� ���� - 2008.10.20 ����
	function check_member($kind='', $opener=false, $void=false) {
		if(!$this->is_member($kind)) {
			$pre_page = "?pre_page=".urlencode($this->base_url.substr($_SERVER['REQUEST_URI'],1));
			$go_url = $this->base_url."rankup_module/rankup_member/login.html";
			$message = !empty($kind) ? $kind=="personal" ? $this->personal_only : $this->company_only : $this->member_only;
			$this->popup_move_js($message);
			if($void==false) {
				echo "
				<script type='text/javascript'>
				var opener_val = ".(int)$opener.";
				if(opener_val) {
					opener.document.location.replace('$go_url');
					self.close();
				}
				else document.location.replace('$go_url$pre_page');
				</script>";
			}
			exit;
		}
	}

	//�����ڰ� �ƴѰ�� �α��� �������� ����
	function check_admin($admin='',$parent='') {
		if(!$this->is_admin()) {
			$go_url = $this->base_url.'RAD/index.html';
			$this->popup_move_js($this->admin_only);
			echo "
			<script type='text/javascript'>
			if(window.top) window.top.location.replace('$go_url');
			else document.location.replace('$go_url');
			</script>";
			exit;
		}
	}

	//ȸ������ �α��� �ߴ��� üũ - 2008.09.18 ����
	function is_member($kind='') {
		$member = new rankup_member();
		return $member->is_member($kind);
	}

	//�����ڷ� �α��� �ߴ��� üũ
	function is_admin(){
		$admin=new rankup_admin();
		return $admin->is_admin();
	}

	//������ ���������� <head>����
	function print_admin_head($title='', $script=true) {
		$config_info = $this->get_config_info('main');
		$charset = rankup_basic::default_charset();
		$title = str_replace('"', "'", empty($title) ? $config_info['subject'] : $title.' :: '.$config_info['subject']);
		$head = array();
		$head[] = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">';
		$head[] = '<html>';
		$head[] = '<head>';
		$head[] = '<title>'.$title.'</title>';
		$head[] = '<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'" />';
		$head[] = '<meta http-equiv="Content-Script-Type" content="text/javascript" />';
		$head[] = '<meta http-equiv="Content-Style-Type" content="text/css" />';
		$head[] = '<meta http-equiv="imagetoolbar" content="no" />';
		$head[] = '<link rel="stylesheet" type="text/css" href="'.$this->base_url.'Libs/_style/rankup_admin.css" />';
		$head[] = '<script type="text/javascript"> window.top.document.title = document.title </script>';
		echo implode("\n", $head);
		if($script==true) rankup_basic::include_js_class();
		echo "</head>\n";
	}

	//����� ���������� <head>����
	function print_user_head($title='', $script=true, $mute_title=false) {
		global $base_url;
		$config_info = $this->get_config_info('main');
		$charset = rankup_basic::default_charset();
		$title = str_replace('"', "'", empty($title) ? $config_info['subject'] : $title.' :: '.$config_info['subject']);
		$author = str_replace('"', "'", $config_info['subject']);
		$description = str_replace('"', "'", $config_info['site_name']);
		$keywords = $classification = str_replace('"', "'", $config_info['meta']);
		$head = array();
		$head[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$head[] = '<html xmlns="http://www.w3.org/1999/xhtml">';
		$head[] = '<head>';
		$head[] = '<title>'.$title.'</title>';
		$head[] = '<meta http-equiv="Content-Type" content="text/html; charset='.$charset.'" />';
		$head[] = '<meta http-equiv="Content-Script-Type" content="text/javascript" />';
		$head[] = '<meta http-equiv="Content-Style-Type" content="text/css" />';

		// nhn����API(2.0) - IE bug fix
		if($this->ie_version()) {
			if(strpos($_SERVER['PHP_SELF'], 'nhn_map.frame.html')!==false) $head[] = '<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />';
			else $head[] = '<meta http-equiv="X-UA-Compatible" content="IE=edge" />';
		}

		$head[] = '<meta http-equiv="imagetoolbar" content="no" />';
		$head[] = '<meta http-equiv="keywords" content="'.$keywords.'" />';
		#$head[] = '<meta name="description" content="'.$description.'" />';
		if($config_info['rss_use']=='yes') $head[] = '<link rel="alternate" type="application/rss+xml" title="'.$title.'" href="'.$this->base_url.'rankup_module/rankup_rss/index.html" />'; // RSS ��ũ
		$head[] = '<link rel="stylesheet" type="text/css" href="'.$this->base_url.'Libs/_style/rankup_style.css" />';
		$head[] = '<link rel="stylesheet" type="text/css" href="'.$this->base_url.SKIN.'skin.css" />';
		$head[] = '<link rel="stylesheet" type="text/css" href="'.$this->base_url.'design/site/site.css" />';
		if(!$mute_title) $head[] = '<script type="text/javascript"> window.top.document.title = document.title </script>';
		echo implode("\n", $head);
		if($script==true) rankup_basic::include_js_class();
		echo "</head>\n";
	}

	//���� ���� ��ü�� ���
	function print_notice_all($num_per_page,$page_per_block){
		$notice = new rankup_notice;
		$result = $notice->print_notice_all($num_per_page,$page_per_block);
		$notice = null;
		return $result;
	}

	//���� ���� ������ ����
	function get_notice_config(){
		$notice = new rankup_notice();
		$result = $notice->get_notice_config();
		$notice = null;
		return $result;
	}

	//���� ���� ��ȸ�� ����
	function update_notice_hit(){
		$notice = new rankup_notice();
		$result = $notice->update_notice_hit();
		$notice = null;
		return $result;
	}

	//���� ���� ���� ���� - 2008.06.09 �߰�
	function print_main_notice($num_per_page, $cut_size) {
		$base_url = rankup_basic::base_url();
		$notice = new rankup_notice;
		$result = $notice->print_main_notice($num_per_page, $cut_size);
		$notice = null;
		return $result;
	}

	// �������� ���� - �˾��� - 2008.06.09 �߰�
	function print_notice($num_per_page, $page_per_block) {
		$notice = new rankup_notice;
		$result = $notice->print_notice($num_per_page, $page_per_block);
		$notice = null;
		return $result;
	}

	//�ʿ��� �ڹٽ�ũ��Ʈ ������ ��Ŭ���
	function include_js_class($dir='') {
		rankup_basic::include_js_class($dir);
	}

	// �Ⱓ�˻� ��� - 2008.09.16 �߰�
	function print_period_search($fields, $values, $option=false, $space=" ~ ", $nolimit="") {
		return rankup_util::print_period_search($fields, $values, $option, $space, $nolimit);
	}

	// �Ⱓ�˻� �ɼ� ��� - 2008.09.16 �߰�
	function print_period_search_option($fields) {
		return rankup_util::print_period_search_option($fields);
	}

	//eidtor js�� ����ϴ� �Լ�
	function make_editor_js() {
		return parent::make_editor_js($this->base_url);
	}

	//eidtor�� ������ �����ϴ� �޼ҵ�
	function make_editor_content($content) {
		return parent::make_editor_content($content,$this->base_url);
	}

	//�޷� �ڽ��� ����ϴ� �κ�
	//���� �̸��� �;� ��
	function make_calendar_js($form) {
		return parent::make_calendar_js($form,$this->base_url);
	}

	//�޷� ������ �����ϴ� �κ�
	//������ ��Ÿ���� text �ڽ��� �̸��� ���� ��Ÿ���� �ؽ�Ʈ �ڽ��� ���� �;� ��.
	function make_calendar_content($start,$end) {
		return parent::make_calendar_content($start,$end,$this->base_url);
	}

	//���� ������ ���̺� �Է��ϴ� �޼ҵ�
	function insert_notice_table($mode,$no='') {
		$notice = new rankup_notice();
		$result = $notice->insert_notice_table($mode,$no);
		$notice=null;
		return $result;
	}

	//���� ������ ������ �ϳ��� ����
	function get_notice($no) {
		$notice = new rankup_notice();
		$result = $notice->get_notice($no);
		$notice=null;
		return $result;
	}

	//���� ������ ����
	function delete_notice_table($no) {
		$notice = new rankup_notice();
		$result=$notice->delete_notice_table($no);
		$notice=null;
		return $result;
	}

	//����Ʈ BG����
	function get_site_bg() {
		global $config_info;
		$config = new rankup_siteconfig();
		$result= $config->get_site_bg($config_info);
		$config = null;
		return $result;
	}

	// ���̹�����Ű ��ȯ
	function get_mapkey($kind='nhn') {
		$config = new rankup_siteconfig;
		$result= $config->get_mapkey($kind);
		$config = null;
		return $result;
	}

	//����Ʈ ���� ���� ����
	function get_config_info($selection=true) {
		$config = new rankup_siteconfig();
		$result= $config->get_config_info($selection);
		$config = null;
		return $result;
	}

	//����Ʈ ���� ������ ����
	function set_config_info($field,$content) {
		$config = new rankup_siteconfig();
		$result = $config->set_config_info($field,$content);
		$config = null;
		return $result;
	}

	//�Ѱ��� ������ ����
	function get_config_field($field) {
		$config = new rankup_siteconfig();
		$result = $config->get_config_field($field);
		$config = null;
		return $result;
	}

	/*
	//���������� ����
	function get_bank_info($no='') {
		$config = new rankup_siteconfig();
		$result = $config->get_bank_info($no);
		$config=null;
		return $result;
	}
	//���� ������ �Է�
	function insert_bank_table($bank_name,$bank_num,$name,$no) {
		$config = new rankup_siteconfig();
		$result = $config->insert_bank_table($bank_name,$bank_num,$name,$no);
		$config=null;
		return $result;
	}
	//���� ������ ����
	function delete_bank_info($no) {
		$config = new rankup_siteconfig();
		$result = $config->delete_bank_info($no);
		$config=null;
		return $result;
	}
	*/

	//��� ��� - 2008.05.27 ����
	function print_banner($position, $freesize=false) {
		$banner = new rankup_banner;
		$result = $banner->print_banner($position, $freesize);
		$banner = null;
		return $result;
	}

	//�ΰ� ��� - 2008.05.27 �߰�
	function print_logo($position) {
		$banner = new rankup_banner;
		$result = $banner->print_banner($position, false, true);
		$banner = null;
		return $result;
	}

	//�������縦 ���
	function insert_poll_table($subject,$answer) {
		$poll=new rankup_poll();
		$result=$poll->insert_poll_table($subject,$answer);
		$poll=null;
		return $result;
	}

	//�������� ����Ʈ�� ����
	function get_poll_all(){
		$poll=new rankup_poll();
		$result = $poll->get_poll_all();
		$poll=null;
		return $result;
	}
	//�������� ���� �����ֱ� ����
	function poll_view_treat($no){
		$poll=new rankup_poll();
		$result = $poll->poll_view_treat($no);
		$poll=null;
		return $result;
	}
	//�������� ������ ����
	function delete_poll($no){
		$poll=new rankup_poll();
		$result = $poll->delete_poll($no);
		$poll=null;
		return $result;
	}

	//�������� ���� - 2008.06.12 �߰�
	function print_poll() {
		$poll=new rankup_poll();
		$result = $poll->print_poll();
		$poll=null;
		return $result;
	}

	//�������系���� ����,
	function get_poll($no){
		$poll=new rankup_poll();
		$result=$poll->get_poll($no);
		$poll=null;
		return $result;
	}

	//�������翡 ���� �ڸ�Ʈ�� ����
	function get_poll_answer($no){
		$poll=new rankup_poll();
		$result=$poll->get_poll_answer($no);
		$poll=null;
		return $result;
	}


	//�������翡 ���� �ڸ�Ʈ �ϳ��� ����
	function get_poll_answer_one($no){
		$poll=new rankup_poll();
		$result=$poll->get_poll_answer_one($no);
		$poll=null;
		return $result;
	}

	//�������翡 ���� �ڸ�Ʈ�� ����
	function delete_poll_answer($no){
		$poll=new rankup_poll();
		$result=$poll->delete_poll_answer($no);
		$poll=null;
		return $result;
	}

	function change_view_status($no,$status){
		$popup=new rankup_popup();
		$result=$popup->change_view_status($no,$status);
		$popup=null;
		return $result;
	}

	//�˾� ����Ʈ ���� - �߰� 2008.06.03
	function get_popup_list(){
		$popup = new rankup_popup();
		$result = $popup->get_popup_list();
		$popup = null;
		return $result;
	}

	// ���޹���/���������� ����Ʈ ���� - 2008.06.10 �߰�
	function get_cooperation($nums_per_page, $page_block, $job_type) {
		$coop=new rankup_cooperation();
		$result = $coop->get_cooperation($nums_per_page, $page_block, $job_type);
		$coop=null;
		return $result;
	}

	//���޾�ü ����Ʈ�� ����
	function get_cooperation_list_all($nums,$block,$job_type) {
		$coop=new rankup_cooperation();
		$result=$coop->get_cooperation_list_all($nums,$block,$job_type);
		$coop=null;
		return $result;
	}

	//���޾�ü ����Ʈ�� �ϳ��� ����
	function get_cooperation_list($no) {
		$coop=new rankup_cooperation();
		$result=$coop->get_cooperation_list($no);
		$coop=null;
		return $result;
	}

	//���޾�ü���� ������ �߼��ϴ� ���
	function update_mail_content($name,$mail,$subject,$contents,$no){
		$coop=new rankup_cooperation();
		$result=$coop->update_mail_content($name,$mail,$subject,$contents,$no);
		$coop=null;
		return $result;
	}

	//���޾�ü ������ ����� �Լ�
	function del_cooperation_list($no){
		$coop=new rankup_cooperation();
		$result=$coop->del_cooperation_list($no);
		$coop=null;
		return $result;
	}

	//�˻��� ����� �����ϴ� �Լ�
	function get_search_all($nums,$block,$type){
		$search=new rankup_search();
		$result=$search->get_search_all($nums,$block,$type);
		$search=null;
		return $result;
	}
	//�˻�� ��� �����ϴ� �κ�
	function insert_search_table($content,$type){
		$search=new rankup_search();
		$result=$search->insert_search_table($content,$type);
		$search=null;
		return $result;
	}

	//�˻�� ��񿡼� �����ϴ� �κ�
	function delete_search_table($no,$type){
		$search=new rankup_search();
		$result=$search->delete_search_table($no,$type);
		$search=null;
		return $result;
	}
	//�˻�� ��񿡼� �����ϴ� �κ�
	function delete_search_all($type){
		$search=new rankup_search();
		$result=$search->delete_search_all($type);
		$search=null;
		return $result;
	}

	//�˻�� �����ϴ� ������
	function update_search_table($no,$content,$type){
		$search=new rankup_search();
		$result=$search->update_search_table($no,$content,$type);
		$search=null;
		return $result;
	}

	//�˻��� ��ü�� ����
	function get_search_table($type){
		$search=new rankup_search();
		$result=$search->get_search_table($type);
		$search=null;
		return $result;
	}

	// �α� �˻��� ���� - 2008.09.05 �߰� - ��ũ��������
	function get_search_keywords($mode="direct", $nums=15, $space=" ", $class="") {
		$search=new rankup_search();
		$result=$search->get_search_keywords($mode, $nums, $space, $class);
		$search=null;
		return $result;
	}

	//������ ����Ʈ ���� - �߰� 2011.05.11
	function get_callcenter_list(){
		$callcenter = new rankup_callcenter();
		$result = $callcenter->get_callcenter_list();
		$callcenter = null;
		return $result;
	}

	//������ ���������� ����Ʈ ���� - �߰� 2011.05.11
	function get_main_callcenter_list(){
		$callcenter = new rankup_callcenter();
		$result = $callcenter->get_callcenter2();
		$callcenter = null;
		return $result;
	}

	################################################################################################################
	# ��������� ���� �κ�
	################################################################################################################
	//ȸ�� ��ü ����� ����
	function get_member_list($nums,$block,$add_que){
		$member=new rankup_member();
		$result=$member->get_member_list($nums,$block,$add_que);
		$member=null;
		return $result;
	}

	//��ü ȸ���� ���� ����
	function get_member_count($add_que){
		$member=new rankup_member();
		$result=$member->get_member_count($add_que);
		$member=null;
		return $result;
	}
	//Ư�� �ʵ��� ���� ����
	function get_member_basic($field='',$uid){
		$member=new rankup_member();
		$result=$member->get_member_basic($field,$uid);
		$member=null;
		return $result;
	}
	//��ü ȸ���� ���� ����
	function set_member_basic($field,$value,$uid){
		$member=new rankup_member();
		$result=$member->set_member_basic($field,$value,$uid);
		$member=null;
		return $result;
	}
	//ȸ���� ����
	function delete_member($uid){
		$member=new rankup_member();
		$result=$member->delete_member($uid);
		$member=null;
		return $result;
	}

	//���ϸ� ����� ���� - 2008.10.21 ����
	function make_mail_que($datas) {
		$member=new rankup_member();
		$result=$member->make_mail_que($datas);
		$member=null;
		return $result;
	}

	function set_member_session($uid,$passwd){
		$member=new rankup_member();
		$result = $member->set_member_session($uid,$passwd);
		$member=null;
	}

	#############################################################################################################
	# ������ ���� �κ�
	#############################################################################################################
	//���� ȸ�縦 ����
	function get_pay_method(){
		$pay=new rankup_payment();
		$result=$pay->get_pay_method();
		$pay=null;
		return $result;
	}

	//���� ȸ�縦 ����
	function set_pay_method($val){
		$pay=new rankup_payment();
		$result=$pay->set_pay_method($val);
		$pay=null;
		return $result;
	}

	//������ ���� ������ ����
	function get_pay_info($field=''){
		$pay=new rankup_payment();
		$result=$pay->get_pay_info($field);
		$pay=null;
		return $result;
	}

	//���� ������ ����
	function set_pay_info($field,$val){
		$pay=new rankup_payment();
		$result=$pay->set_pay_info($field,$val);
		$pay=null;
		return $result;
	}

	//���� ������ ����
	function set_pay_infos($val){
		$pay=new rankup_payment();
		$result=$pay->set_pay_infos($val);
		$pay=null;
		return $result;
	}

	//������ ����� ����
	function print_goods_kind($name){
		$pay=new rankup_payment();
		$result=$pay->print_goods_kind($name);
		$pay=null;
		return $result;
	}

	//����� ������ ���� ����� ����
	function print_pay_kinds($type, $name, $addTag=''){ // type : { select | radio }
		$pay=new rankup_payment();
		$result=$pay->print_pay_kinds($type, $name, $addTag);
		$pay=null;
		return $result;
	}

	//���� ����� ����
	function print_pay_method($name){
		$pay=new rankup_payment();
		$result=$pay->print_pay_method($name);
		$pay=null;
		return $result;
	}

	//������ ���������� ���� ���� �˻���
	function get_pay_list_query($info='',$total='',$type=''){
		$pay=new rankup_payment();
		$result=$pay->get_pay_list_query($info,$total,$type);
		$pay=null;
		return $result;
	}

	//�˻��� ���� ���, �̸�, ���̵� ���� ����
	function print_search_method($value,$type=''){
		$pay=new rankup_payment();
		$result=$pay->print_search_method($value,$type);
		$pay=null;
		return $result;
	}

	//���� ������ ���� ���̺��� ó��
	//$add_value �� ������ �̷�� ������, �ٳ��̳� �̴Ͻý� ���� �ο��� �ֹ� �ڵ尡 �ѿ� ��.
	//$type�� ������ ���������� ������ ������ �ϴ� ���,
	function update_pay_table($no,$add_value='',$type=''){
		$pay=new rankup_payment();
		$result=$pay->update_pay_table($no,$add_value,$type);
		$pay=null;
		return $result;
	}

	//�Էµ� ���� �����ϴ� �κ�
	//�����ͺ��̽��� �ԷµǾ��� �ִ� ���� ����
	//goods_info �� serialize�Ǿ��� �迭�̹Ƿ�, �ٽ� ������ �迭�� ����
	function get_paytable_info($no){
		$pay=new rankup_payment();
		$result=$pay->get_paytable_info($no);
		$pay=null;
		return $result;
	}

	#################################################################################
	# ���� / ���� ���� ����Ʈ�� �����ϴ� �κ�
	#################################################################################
	function insert_customer_table($info) {
		$customer = new rankup_customer();
		$result = $customer->insert_customer_table($info);
		$customer = null;
		return $result;
	}

	function insert_cooperation_table($info) {
		$customer = new rankup_cooperation();
		$result = $customer->insert_cooperation_table($info);
		$customer = null;
		return $result;
	}

	#################################################################################
	# ���� / ���� ���� ����Ʈ�� �����ϴ� �κ�
	#################################################################################
	function set_list_value($code,$p_no='',$val){
		$list= new rankup_list();
		$result= $list->set_list_value($code,$p_no,$val);
		$list=null;
		return $result;
	}

	//���� �����ϴ� �κ�
	function update_list_value($value,$no){
		$list= new rankup_list();
		$result=$list->update_list_value($value,$no);
		$list=null;
		return $result;
	}
}
?>