<?php
##실제로 컨트로를 하는 함수
class rankup_control extends rankup_util{
	var $base_url; //루트를 기준으로한 경로 즉 /main/index.html 형태 src, href에서 사용
	var $base_dir; //home디렉토리를 기준으로 한 경우, /home/test/public_html/Libs/dbcon.php 형태, include, require, file(), opendir()등에서 사용

	//메시지를 보여주는 부분
	var $insert_true = "성공적으로 저장되었습니다.";
	var $insert_false = "저장이 실패 하였습니다.";
	var $admin_only = "관리자 전용 페이지 입니다.";

	var $member_only = "죄송합니다. 고객님께서는 현재 로그인이 되어있지 않습니다.";
	var $company_only = "죄송합니다. 중개업자만 이용할 수 있는 서비스입니다.";
	var $personal_only = "죄송합니다. 회원만 이용할 수 있는 서비스입니다.";

	var $delete_true = "성공적으로 삭제 되었습니다.";
	var $delete_false = "삭제가 실패하였습니다.";
	var $invalid_passwd = "패스워드가 정확하지 않습니다.";
	var $change_true = "성공적으로 변경되었습니다.";
	var $change_false = "변경이 실패하였습니다.";
	var $invalid_email = "이메일 정보가 정확하지 않습니다.";
	var $empty_subject = "제목을 입력하여 주십시요";
	var $empty_content = "내용을 입력하여 주십시요";
	var $first_rank = "현재 최상위 순위입니다.";
	var $last_rank = "현재 최하위 순위입니다.";
	var $empty_list = "게시물이 존재하지 않습니다.";
	var $mail_send = "메일이 정상적으로 발송되었습니다.";
	var $mail_fail = "메일 발송이 실패하였습니다.";
	var $approval_true = "승인이 성공되었습니다.";
	var $approval_false = "승인이 실패하였습니다.";

	function rankup_control() {
		parent::rankup_util();
		$this->base_url = rankup_basic::base_url();
		$this->base_dir = rankup_basic::base_dir();
	}

	//로그인하지 않았을때 경고창을 보여주는 함수
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

	//모든 세션을 파괴
	function delete_all_session(){
		$_SESSION=array();
		session_destroy();
	}

	//데모버젼인지 체크
	function is_demo(){
		return rankup_basic::is_demo();
	}

	//회원이 아닌경우 로그인 페이지로 보냄 - 2008.10.20 수정
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

	//관리자가 아닌경우 로그인 페이지로 보냄
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

	//회원으로 로그인 했는지 체크 - 2008.09.18 수정
	function is_member($kind='') {
		$member = new rankup_member();
		return $member->is_member($kind);
	}

	//관리자로 로그인 했는지 체크
	function is_admin(){
		$admin=new rankup_admin();
		return $admin->is_admin();
	}

	//관리자 페이지에서 <head>구성
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

	//사용자 페이지에서 <head>구성
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

		// nhn지도API(2.0) - IE bug fix
		if($this->ie_version()) {
			if(strpos($_SERVER['PHP_SELF'], 'nhn_map.frame.html')!==false) $head[] = '<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7" />';
			else $head[] = '<meta http-equiv="X-UA-Compatible" content="IE=edge" />';
		}

		$head[] = '<meta http-equiv="imagetoolbar" content="no" />';
		$head[] = '<meta http-equiv="keywords" content="'.$keywords.'" />';
		#$head[] = '<meta name="description" content="'.$description.'" />';
		if($config_info['rss_use']=='yes') $head[] = '<link rel="alternate" type="application/rss+xml" title="'.$title.'" href="'.$this->base_url.'rankup_module/rankup_rss/index.html" />'; // RSS 링크
		$head[] = '<link rel="stylesheet" type="text/css" href="'.$this->base_url.'Libs/_style/rankup_style.css" />';
		$head[] = '<link rel="stylesheet" type="text/css" href="'.$this->base_url.SKIN.'skin.css" />';
		$head[] = '<link rel="stylesheet" type="text/css" href="'.$this->base_url.'design/site/site.css" />';
		if(!$mute_title) $head[] = '<script type="text/javascript"> window.top.document.title = document.title </script>';
		echo implode("\n", $head);
		if($script==true) rankup_basic::include_js_class();
		echo "</head>\n";
	}

	//공지 사항 전체를 출력
	function print_notice_all($num_per_page,$page_per_block){
		$notice = new rankup_notice;
		$result = $notice->print_notice_all($num_per_page,$page_per_block);
		$notice = null;
		return $result;
	}

	//공지 사항 설정을 추출
	function get_notice_config(){
		$notice = new rankup_notice();
		$result = $notice->get_notice_config();
		$notice = null;
		return $result;
	}

	//공지 사항 조회수 증가
	function update_notice_hit(){
		$notice = new rankup_notice();
		$result = $notice->update_notice_hit();
		$notice = null;
		return $result;
	}

	//메인 공지 사항 추출 - 2008.06.09 추가
	function print_main_notice($num_per_page, $cut_size) {
		$base_url = rankup_basic::base_url();
		$notice = new rankup_notice;
		$result = $notice->print_main_notice($num_per_page, $cut_size);
		$notice = null;
		return $result;
	}

	// 공지사항 추출 - 팝업용 - 2008.06.09 추가
	function print_notice($num_per_page, $page_per_block) {
		$notice = new rankup_notice;
		$result = $notice->print_notice($num_per_page, $page_per_block);
		$notice = null;
		return $result;
	}

	//필요한 자바스크립트 파일을 인클루드
	function include_js_class($dir='') {
		rankup_basic::include_js_class($dir);
	}

	// 기간검색 출력 - 2008.09.16 추가
	function print_period_search($fields, $values, $option=false, $space=" ~ ", $nolimit="") {
		return rankup_util::print_period_search($fields, $values, $option, $space, $nolimit);
	}

	// 기간검색 옵션 출력 - 2008.09.16 추가
	function print_period_search_option($fields) {
		return rankup_util::print_period_search_option($fields);
	}

	//eidtor js를 출력하는 함수
	function make_editor_js() {
		return parent::make_editor_js($this->base_url);
	}

	//eidtor를 실제로 구현하는 메소드
	function make_editor_content($content) {
		return parent::make_editor_content($content,$this->base_url);
	}

	//달력 자스를 출력하는 부분
	//폼의 이름이 와야 함
	function make_calendar_js($form) {
		return parent::make_calendar_js($form,$this->base_url);
	}

	//달력 내용을 추출하는 부분
	//시작을 나타내는 text 박스의 이름과 끝을 나타내는 텍스트 박스의 끝이 와야 함.
	function make_calendar_content($start,$end) {
		return parent::make_calendar_content($start,$end,$this->base_url);
	}

	//공지 사항을 테이블에 입력하는 메소드
	function insert_notice_table($mode,$no='') {
		$notice = new rankup_notice();
		$result = $notice->insert_notice_table($mode,$no);
		$notice=null;
		return $result;
	}

	//공지 사항의 내용중 하나를 추출
	function get_notice($no) {
		$notice = new rankup_notice();
		$result = $notice->get_notice($no);
		$notice=null;
		return $result;
	}

	//공지 사항을 제거
	function delete_notice_table($no) {
		$notice = new rankup_notice();
		$result=$notice->delete_notice_table($no);
		$notice=null;
		return $result;
	}

	//사이트 BG추출
	function get_site_bg() {
		global $config_info;
		$config = new rankup_siteconfig();
		$result= $config->get_site_bg($config_info);
		$config = null;
		return $result;
	}

	// 네이버지도키 반환
	function get_mapkey($kind='nhn') {
		$config = new rankup_siteconfig;
		$result= $config->get_mapkey($kind);
		$config = null;
		return $result;
	}

	//사이트 설정 정보 추출
	function get_config_info($selection=true) {
		$config = new rankup_siteconfig();
		$result= $config->get_config_info($selection);
		$config = null;
		return $result;
	}

	//사이트 설정 정보를 저장
	function set_config_info($field,$content) {
		$config = new rankup_siteconfig();
		$result = $config->set_config_info($field,$content);
		$config = null;
		return $result;
	}

	//한개의 정보를 추출
	function get_config_field($field) {
		$config = new rankup_siteconfig();
		$result = $config->get_config_field($field);
		$config = null;
		return $result;
	}

	/*
	//계좌정보를 추출
	function get_bank_info($no='') {
		$config = new rankup_siteconfig();
		$result = $config->get_bank_info($no);
		$config=null;
		return $result;
	}
	//계좌 정보를 입력
	function insert_bank_table($bank_name,$bank_num,$name,$no) {
		$config = new rankup_siteconfig();
		$result = $config->insert_bank_table($bank_name,$bank_num,$name,$no);
		$config=null;
		return $result;
	}
	//계좌 정보를 삭제
	function delete_bank_info($no) {
		$config = new rankup_siteconfig();
		$result = $config->delete_bank_info($no);
		$config=null;
		return $result;
	}
	*/

	//배너 출력 - 2008.05.27 수정
	function print_banner($position, $freesize=false) {
		$banner = new rankup_banner;
		$result = $banner->print_banner($position, $freesize);
		$banner = null;
		return $result;
	}

	//로고 출력 - 2008.05.27 추가
	function print_logo($position) {
		$banner = new rankup_banner;
		$result = $banner->print_banner($position, false, true);
		$banner = null;
		return $result;
	}

	//설문조사를 등록
	function insert_poll_table($subject,$answer) {
		$poll=new rankup_poll();
		$result=$poll->insert_poll_table($subject,$answer);
		$poll=null;
		return $result;
	}

	//설문조사 리스트를 리턴
	function get_poll_all(){
		$poll=new rankup_poll();
		$result = $poll->get_poll_all();
		$poll=null;
		return $result;
	}
	//설문조사 내용 보여주기 여부
	function poll_view_treat($no){
		$poll=new rankup_poll();
		$result = $poll->poll_view_treat($no);
		$poll=null;
		return $result;
	}
	//설문조사 내용을 삭제
	function delete_poll($no){
		$poll=new rankup_poll();
		$result = $poll->delete_poll($no);
		$poll=null;
		return $result;
	}

	//설문조사 노출 - 2008.06.12 추가
	function print_poll() {
		$poll=new rankup_poll();
		$result = $poll->print_poll();
		$poll=null;
		return $result;
	}

	//설문조사내용을 추출,
	function get_poll($no){
		$poll=new rankup_poll();
		$result=$poll->get_poll($no);
		$poll=null;
		return $result;
	}

	//설문조사에 대한 코멘트를 추출
	function get_poll_answer($no){
		$poll=new rankup_poll();
		$result=$poll->get_poll_answer($no);
		$poll=null;
		return $result;
	}


	//설문조사에 대한 코멘트 하나를 추출
	function get_poll_answer_one($no){
		$poll=new rankup_poll();
		$result=$poll->get_poll_answer_one($no);
		$poll=null;
		return $result;
	}

	//설문조사에 대한 코멘트를 삭제
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

	//팝업 리스트 추출 - 추가 2008.06.03
	function get_popup_list(){
		$popup = new rankup_popup();
		$result = $popup->get_popup_list();
		$popup = null;
		return $result;
	}

	// 제휴문의/고객지원센터 리스트 추출 - 2008.06.10 추가
	function get_cooperation($nums_per_page, $page_block, $job_type) {
		$coop=new rankup_cooperation();
		$result = $coop->get_cooperation($nums_per_page, $page_block, $job_type);
		$coop=null;
		return $result;
	}

	//제휴업체 리스트를 추출
	function get_cooperation_list_all($nums,$block,$job_type) {
		$coop=new rankup_cooperation();
		$result=$coop->get_cooperation_list_all($nums,$block,$job_type);
		$coop=null;
		return $result;
	}

	//제휴업체 리스트중 하나를 추출
	function get_cooperation_list($no) {
		$coop=new rankup_cooperation();
		$result=$coop->get_cooperation_list($no);
		$coop=null;
		return $result;
	}

	//제휴업체에게 메일을 발송하는 경우
	function update_mail_content($name,$mail,$subject,$contents,$no){
		$coop=new rankup_cooperation();
		$result=$coop->update_mail_content($name,$mail,$subject,$contents,$no);
		$coop=null;
		return $result;
	}

	//제휴업체 내역을 지우는 함수
	function del_cooperation_list($no){
		$coop=new rankup_cooperation();
		$result=$coop->del_cooperation_list($no);
		$coop=null;
		return $result;
	}

	//검색어 목록을 추출하는 함수
	function get_search_all($nums,$block,$type){
		$search=new rankup_search();
		$result=$search->get_search_all($nums,$block,$type);
		$search=null;
		return $result;
	}
	//검색어를 디비에 저장하는 부분
	function insert_search_table($content,$type){
		$search=new rankup_search();
		$result=$search->insert_search_table($content,$type);
		$search=null;
		return $result;
	}

	//검색어를 디비에서 제거하는 부분
	function delete_search_table($no,$type){
		$search=new rankup_search();
		$result=$search->delete_search_table($no,$type);
		$search=null;
		return $result;
	}
	//검색어를 디비에서 제거하는 부분
	function delete_search_all($type){
		$search=new rankup_search();
		$result=$search->delete_search_all($type);
		$search=null;
		return $result;
	}

	//검색어를 수정하는 페이지
	function update_search_table($no,$content,$type){
		$search=new rankup_search();
		$result=$search->update_search_table($no,$content,$type);
		$search=null;
		return $result;
	}

	//검색어 전체를 리턴
	function get_search_table($type){
		$search=new rankup_search();
		$result=$search->get_search_table($type);
		$search=null;
		return $result;
	}

	// 인기 검색어 추출 - 2008.09.05 추가 - 랭크업개발팀
	function get_search_keywords($mode="direct", $nums=15, $space=" ", $class="") {
		$search=new rankup_search();
		$result=$search->get_search_keywords($mode, $nums, $space, $class);
		$search=null;
		return $result;
	}

	//고객센터 리스트 추출 - 추가 2011.05.11
	function get_callcenter_list(){
		$callcenter = new rankup_callcenter();
		$result = $callcenter->get_callcenter_list();
		$callcenter = null;
		return $result;
	}

	//고객센터 메인페이지 리스트 추출 - 추가 2011.05.11
	function get_main_callcenter_list(){
		$callcenter = new rankup_callcenter();
		$result = $callcenter->get_callcenter2();
		$callcenter = null;
		return $result;
	}

	################################################################################################################
	# 멤버관리에 관한 부분
	################################################################################################################
	//회원 전체 목록을 리턴
	function get_member_list($nums,$block,$add_que){
		$member=new rankup_member();
		$result=$member->get_member_list($nums,$block,$add_que);
		$member=null;
		return $result;
	}

	//전체 회원의 수를 리턴
	function get_member_count($add_que){
		$member=new rankup_member();
		$result=$member->get_member_count($add_que);
		$member=null;
		return $result;
	}
	//특정 필드의 값을 리턴
	function get_member_basic($field='',$uid){
		$member=new rankup_member();
		$result=$member->get_member_basic($field,$uid);
		$member=null;
		return $result;
	}
	//전체 회원의 수를 리턴
	function set_member_basic($field,$value,$uid){
		$member=new rankup_member();
		$result=$member->set_member_basic($field,$value,$uid);
		$member=null;
		return $result;
	}
	//회원을 삭제
	function delete_member($uid){
		$member=new rankup_member();
		$result=$member->delete_member($uid);
		$member=null;
		return $result;
	}

	//메일링 목록을 추출 - 2008.10.21 수정
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
	# 결제에 관한 부분
	#############################################################################################################
	//결제 회사를 추출
	function get_pay_method(){
		$pay=new rankup_payment();
		$result=$pay->get_pay_method();
		$pay=null;
		return $result;
	}

	//결제 회사를 저장
	function set_pay_method($val){
		$pay=new rankup_payment();
		$result=$pay->set_pay_method($val);
		$pay=null;
		return $result;
	}

	//결제에 관한 정보를 추출
	function get_pay_info($field=''){
		$pay=new rankup_payment();
		$result=$pay->get_pay_info($field);
		$pay=null;
		return $result;
	}

	//결제 정보를 저장
	function set_pay_info($field,$val){
		$pay=new rankup_payment();
		$result=$pay->set_pay_info($field,$val);
		$pay=null;
		return $result;
	}

	//결제 정보를 저장
	function set_pay_infos($val){
		$pay=new rankup_payment();
		$result=$pay->set_pay_infos($val);
		$pay=null;
		return $result;
	}

	//서비스의 목록을 추출
	function print_goods_kind($name){
		$pay=new rankup_payment();
		$result=$pay->print_goods_kind($name);
		$pay=null;
		return $result;
	}

	//사용자 페이지 결제 방법을 노출
	function print_pay_kinds($type, $name, $addTag=''){ // type : { select | radio }
		$pay=new rankup_payment();
		$result=$pay->print_pay_kinds($type, $name, $addTag);
		$pay=null;
		return $result;
	}

	//결제 방법을 추출
	function print_pay_method($name){
		$pay=new rankup_payment();
		$result=$pay->print_pay_method($name);
		$pay=null;
		return $result;
	}

	//관리자 페이지에서 결제 내역 검색시
	function get_pay_list_query($info='',$total='',$type=''){
		$pay=new rankup_payment();
		$result=$pay->get_pay_list_query($info,$total,$type);
		$pay=null;
		return $result;
	}

	//검색할 결제 목록, 이름, 아이디 등을 추출
	function print_search_method($value,$type=''){
		$pay=new rankup_payment();
		$result=$pay->print_search_method($value,$type);
		$pay=null;
		return $result;
	}

	//결제 정보를 결제 테이블에서 처리
	//$add_value 는 결제가 이루어 지고나서, 다날이나 이니시스 에서 부여한 주문 코드가 넘오 옴.
	//$type은 관리자 페이지에서 무통장 승인을 하는 경우,
	function update_pay_table($no,$add_value='',$type=''){
		$pay=new rankup_payment();
		$result=$pay->update_pay_table($no,$add_value,$type);
		$pay=null;
		return $result;
	}

	//입력된 값을 추출하는 부분
	//데이터베이스에 입력되어져 있는 값을 리턴
	//goods_info 는 serialize되어진 배열이므로, 다시 원래의 배열로 리턴
	function get_paytable_info($no){
		$pay=new rankup_payment();
		$result=$pay->get_paytable_info($no);
		$pay=null;
		return $result;
	}

	#################################################################################
	# 지역 / 직종 등의 리스트를 구현하는 부분
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
	# 지역 / 직종 등의 리스트를 구현하는 부분
	#################################################################################
	function set_list_value($code,$p_no='',$val){
		$list= new rankup_list();
		$result= $list->set_list_value($code,$p_no,$val);
		$list=null;
		return $result;
	}

	//값을 변경하는 부분
	function update_list_value($value,$no){
		$list= new rankup_list();
		$result=$list->update_list_value($value,$no);
		$list=null;
		return $result;
	}
}
?>