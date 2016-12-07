<?php
/**
 * �ƿ��α��� ��
 *@note: design/skin/main.inc.php ���� �ε���
 */
$mo_rows = $design->get_settings('main_outlogin');
if($mo_rows['use_outlogin']!='yes') return;
?>

	<style type="text/css">
	#login_frame {margin-left:<?=$mo_rows['outlogin_left']?>px;margin-top:<?=$mo_rows['outlogin_top']?>px}
	</style>

	<div id="login_wrap">

<?php
if(!$member_info['uid']) {
	if(rankup_basic::is_demo()) {
		$login_id = 'rankup';
		$login_pw = 'rankup';
	}
	else {
		$login_id = '���̵�';
		$login_pw = '��й�ȣ';
	}
?>
		<div id="login_frame">
			<div class="login_bg">
				<form id="login_form" name="login_form" action="javascript:void(0)" onSubmit="return $login.submit(this, 'login_form')">
					<input type="hidden" name="kind" value="general" />
					<fieldset>
						<legend>���շα��� �� ȸ������</legend>
						<h2 class="h_tit tit_bullet"><img src="../design/skin/img/common/login_tit1.png" alt="ȸ���α���" /></h2>
						<dl class="login_box">
							<dd class="id_input"><input type="text" id="login_id" name="login_id" value="<?=$login_id?>" class="input_text" onFocus="this.select()" /></dd>
							<dd class="pw_input"><input type="password" id="login_pw" name="login_pw" value="<?=$login_pw?>" class="input_text" onFocus="this.select()" /></dd>
							<dd class="b_login"><input type="submit" id="keyword_txt" class="submit" /></dd>
							<dd class="g_find">
								<a href="../rankup_module/rankup_member/join_intro.html"><span class="strong_t">ȸ������</span></a><img src="../design/skin/img/common/login_line.gif" alt="�α��ζ���" /><a href="../rankup_module/rankup_member/find_login_info.html"><span>���̵�/��й�ȣã��</span></a>
							</dd>
						</dl>
					</fieldset>
				</form>
			</div>
		</div>
		<script type="text/javascript">
		//<![CDATA[
		var $login = Object.clone($form);
		$login.url = domain +'mypage';
		$login.hashes = {mode: 'login'};
		$login.handler = function(trans) { proc.response(trans) };
		//]]>
		</script>

<?php
}
// �α�����
else {
?>
		<div id="login_frame">
			<div class="login_bg">
				<fieldset>
					<legend>���շα��� �� ȸ������</legend>
					<h2 class="h_tit tit_bullet"><img src="../design/skin/img/common/logout_tit1.png" alt="members" /></h2>
					<dl class="login_box">
						<dd class="logout"><span class="m_name"><?=$member_info['name']?></span>&nbsp;<span class="l_text">�� �湮�� ȯ���մϴ�.</span></dd>
						<?php
						// �����α��� ����
						if($member_info['prev_login_infos']) {
							$login_infos = unserialize($member_info['prev_login_infos']);
							echo '<dd class="time_t">�����α��� : '.$login_infos['login_time'].'</dd>';
						}
						?>
						<dd class="g_find_out">
							<a onClick="logout()"><span class="strong_t">�α׾ƿ�</span></a><img src="../design/skin/img/common/login_line.gif" alt="�α��ζ���" /><a href="../rankup_module/rankup_member/member_modify.html"><span>ȸ����������</span></a>
						</dd>
					</dl>
				</fieldset>
			</div>
		</div>
<?php
}
?>
	</div><!-- login_wrap End -->