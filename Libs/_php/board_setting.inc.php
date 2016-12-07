<?php
$this->use_main_board = false; // ���ΰԽ��� ��뿩��
$this->display_subject = false; // �Խ��� ���� ��¿���
$this->use_extend_level = true; // Ȯ�� ��� ��뿩��
$this->use_board_menu = false; // �Խ��� �޴����� ��뿩��
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
return; // ������ ������ �ʿ��� ��쿡�� �ּ�ó��
$this->sconfig_table = "rankup_siteconfig"; // �ַ�� �⺻ȯ�漳�� ���̺�(smlayout �ʵ�: ������,����Ʈ,��޼���)
$this->member_table = "rankup_member"; // ȸ�� ���̺�
$this->member_extend_table = "rankup_member_extend"; // ȸ�� Ȯ�����̺�(�г���)
$this->admin_session_id = "admin_session_id"; // ������ ���� ID
$this->member_session_id = "niceId";	// ȸ�� ���� ID
$this->member_uid_field = "uid"; // ȸ�� ���̵� �ʵ��
$this->member_passwd_field = "passwd"; // ȸ�� ��й�ȣ �ʵ��
$this->member_name_field = "name"; // ȸ�� �̸� �ʵ��
$this->editor_name = "rankup_wysiwyg"; // ������ ������ ������
$this->index_name = "board"; // �Խ��� �ε��������� ��ġ�ϴ� ������
$this->include_js_class = false; // rankup_basic::include_js_class() ���� ���� - 2009.10.08 added
?>