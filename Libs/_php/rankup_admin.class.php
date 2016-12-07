<?php
##�����ڸ� ó���ϱ� ���� Ŭ����
class rankup_admin extends rankup_util{
	var $admin_table = 'rankup_admin';	//������ ������ �����ϴ� ���̺�
	var $admin_id = 'id';	//���̵� �ʵ�
	var $admin_pw = 'passwd';	//�н����� �ʵ�
	var $admin_session_val = 'admin_session_val';	//������ ������ ��Ÿ���� ���� ����
	var $admin_session_value = 'rankup_administrator';	//������ �ϰ�� �߱޵Ǵ� ��
	var $admin_session_id = 'admin_session_id';	//�����ڷ� �α����� ���̵� ���� �����ϴ� ����


	//�������� ���̵� ����
	function get_admin_id(){
		return $this->queryR("select $this->admin_id from $this->admin_table");
	}

	//�������� �н����带 ����
	function get_admin_passwd(){
		$result = $this->queryR("select $this->admin_pw from $this->admin_table");
		return str_pad("", strlen($result), "*", STR_PAD_BOTH);
	}
	/*
	//������ ���̺��� �����ϴ� �Լ�
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

	//�����ڷ� �α��� �ߴ��� üũ
	function is_admin(){
		if(!$_SESSION[$this->admin_session_id] || $_SESSION[$this->admin_session_val] != $this->admin_session_value)
			return '';
		if(!$this->match_table($_SESSION[$this->admin_session_id]))
			return '';
		return true;

	}

	//������ ������ �߱�, �α��ν�
	function set_admin_session($id,$pw,$mode=''){
		if($mode=='update') :	//���̵�, ��й�ȣ ������ ���
			$_SESSION[$this->admin_session_id]=$id;	//���̵� �߱�
			$_SESSION[$this->admin_session_val]=$this->admin_session_value;		//������ ������ �ο�
		else :	//�α��ν�,
			if($id && $pw && $this->match_table($id,$pw)) : // 2010.02.16 fixed
				$_SESSION[$this->admin_session_id]=$id;	//���̵� �߱�
				$_SESSION[$this->admin_session_val]=$this->admin_session_value;		//������ ������ �ο�
				return true;
			else :
				$_SESSION[$this->admin_session_id]='';
				$_SESSION[$this->admin_session_val]='';
				return '';
			endif;
		endif;
	}

	//������ ���̺��� ���̵�, �н����尡 ��ġ�ϴ��� �˻�.
	function match_table($id,$pw=''){
		if($pw) :
			return $this->queryR("select count(*) from $this->admin_table where $this->admin_id = '$id' and $this->admin_pw = '$pw'");
		else :
			return $this->queryR("select count(*) from $this->admin_table where $this->admin_id = '$id'");
		endif;
	}

	//������ ������ ����(�α׾ƿ�)
	function delete_admin_session(){
		$_SESSION[$this->admin_session_id]='';
		$_SESSION[$this->admin_session_val]='';
	}

	//������ ������ �Է�/����
	function update_admin_table($id,$passwd){
		if($this->queryR("select count(*) from $this->admin_table"))	//������ �ִ°��,
			$pre_que = "update ";
		else
			$pre_que = "insert into ";
		$que = $pre_que." $this->admin_table set $this->admin_id = '$id', $this->admin_pw = '$passwd'";
		return $this->query($que);

	}
}
?>
