<?php
##관리자를 처리하기 위한 클래스
class rankup_admin extends rankup_util{
	var $admin_table = 'rankup_admin';	//관리자 정보를 저장하는 테이블
	var $admin_id = 'id';	//아이디 필드
	var $admin_pw = 'passwd';	//패스워드 필드
	var $admin_session_val = 'admin_session_val';	//관리자 인지를 나타내는 세션 변수
	var $admin_session_value = 'rankup_administrator';	//관리자 일경우 발급되는 값
	var $admin_session_id = 'admin_session_id';	//관리자로 로그인한 아이디 값을 저장하는 변수


	//관리자의 아이디를 리턴
	function get_admin_id(){
		return $this->queryR("select $this->admin_id from $this->admin_table");
	}

	//관리자의 패스워드를 리턴
	function get_admin_passwd(){
		$result = $this->queryR("select $this->admin_pw from $this->admin_table");
		return str_pad("", strlen($result), "*", STR_PAD_BOTH);
	}
	/*
	//관리자 테이블을 생성하는 함수
	function create_admin_table(){
		$this->query("drop table if exists $this->admin_table");
		$que = "create table $this->admin_table (
				id char(20),
				passwd char(41)
				) type = MyIsam
				";
		return $this->query($que);
	}
	*/

	//관리자로 로그인 했는지 체크
	function is_admin(){
		if(!$_SESSION[$this->admin_session_id] || $_SESSION[$this->admin_session_val] != $this->admin_session_value)
			return '';
		if(!$this->match_table($_SESSION[$this->admin_session_id]))
			return '';
		return true;

	}

	//관리자 세션을 발급, 로그인시
	function set_admin_session($id,$pw,$mode=''){
		if($mode=='update') :	//아이디, 비밀번호 변경인 경우
			$_SESSION[$this->admin_session_id]=$id;	//아이디를 발급
			$_SESSION[$this->admin_session_val]=$this->admin_session_value;		//관리자 인증을 부여
		else :	//로그인시,
			if($id && $pw && $this->match_table($id,$pw)) : // 2010.02.16 fixed
				$_SESSION[$this->admin_session_id]=$id;	//아이디를 발급
				$_SESSION[$this->admin_session_val]=$this->admin_session_value;		//관리자 인증을 부여
				return true;
			else :
				$_SESSION[$this->admin_session_id]='';
				$_SESSION[$this->admin_session_val]='';
				return '';
			endif;
		endif;
	}

	//관리자 테이블에서 아이디, 패스워드가 일치하는지 검사.
	function match_table($id,$pw=''){
		if($pw) :
			return $this->queryR("select count(*) from $this->admin_table where $this->admin_id = '$id' and $this->admin_pw = '$pw'");
		else :
			return $this->queryR("select count(*) from $this->admin_table where $this->admin_id = '$id'");
		endif;
	}

	//관리자 세션을 제거(로그아웃)
	function delete_admin_session(){
		$_SESSION[$this->admin_session_id]='';
		$_SESSION[$this->admin_session_val]='';
	}

	//관리자 정보를 입력/변경
	function update_admin_table($id,$passwd){
		if($this->queryR("select count(*) from $this->admin_table"))	//정보가 있는경우,
			$pre_que = "update ";
		else
			$pre_que = "insert into ";
		$que = $pre_que." $this->admin_table set $this->admin_id = '$id', $this->admin_pw = '$passwd'";
		return $this->query($que);

	}
}
?>
