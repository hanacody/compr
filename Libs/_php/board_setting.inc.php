<?php
$this->use_main_board = false; // 메인게시판 사용여부
$this->display_subject = false; // 게시판 제목 출력여부
$this->use_extend_level = true; // 확장 등급 사용여부
$this->use_board_menu = false; // 게시판 메뉴생성 사용여부
if(!function_exists('extend_level')) {
	function extend_level($board) { // rankup_board.class.php
		global $member_info, $rankup_member;
		if($board->member_id) {
			$board->member_name = $member_info['nickname'] ? $member_info['nickname'] : $member_info['name'];
			return $member_info['level'];
		}
		else {
			return $rankup_member->lowest_level;
		}
	}
}
if(!function_exists('extend_level_point')) {
	function extend_level_point($board) { // rankup_board_admin.class.php
		global $config_info;
		return array($config_info['smlevel'], array('smpoint' => serialize(array())));
	}
}
return; // 별도의 설정이 필요할 경우에는 주석처리
$this->sconfig_table = "rankup_siteconfig"; // 솔루션 기본환경설정 테이블(smlayout 필드: 디자인,포인트,등급설정)
$this->member_table = "rankup_member"; // 회원 테이블
$this->member_extend_table = "rankup_member_extend"; // 회원 확장테이블(닉네임)
$this->admin_session_id = "admin_session_id"; // 관리자 세션 ID
$this->member_session_id = "niceId";	// 회원 세션 ID
$this->member_uid_field = "uid"; // 회원 아이디 필드명
$this->member_passwd_field = "passwd"; // 회원 비밀번호 필드명
$this->member_name_field = "name"; // 회원 이름 필드명
$this->editor_name = "rankup_wysiwyg"; // 위지윅 에디터 폴더명
$this->index_name = "board"; // 게시판 인덱스파일이 위치하는 폴더명
$this->include_js_class = false; // rankup_basic::include_js_class() 실행 여부 - 2009.10.08 added
?>