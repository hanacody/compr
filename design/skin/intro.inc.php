<?php
/**
 * ��Ʈ�� ������
 *@note: /intro.html ���� �ε�
 */

if(rankup_basic::is_demo()) {
	$login_id = 'rankup';
	$login_pw = 'rankup';
}
?>

<div style="width:940px;margin:50px auto">
	<div style="margin:10px 0">
		<?php
		// �ΰ�
		echo strip_tags($rankup_control->print_logo('1'), '<img>');
		?>
	</div>

	<div style="border:#e1e1e1 1px solid;padding:50px 0">

		<div style="margin-bottom:50px;text-align:center">
			<?php
			// ��19���̻�(����)
			if($config_info['membership_age']=='19over') echo '<img src="./Libs/_images/intro_notice.gif" />';
			else echo '<img src="./Libs/_images/intro_notice2.gif" />'
			?>
		</div>

		<table width="710" border="0" align="center" cellpadding="0" cellspacing="0">
			<tr valign="top">
				<td>
					<!--ȸ���α��ν���-->
					<form id="login_form" name="login_form" action="javascript:void(0)" autocomplete="off" onSubmit="return $login.submit(this, 'login_form')">
						<input type="hidden" name="pre_page" value="<?=$pre_page?>" />
						<input type="hidden" name="kind" value="general" />
						<table border="0" cellpadding="0" cellspacing="0" width="100%">
							<tr>
								<td align="left"><img src="./Libs/_images/intro_tit01.png" alt="ȸ���α���"></td>
							</tr>
							<tr>
								<td height="10"></td>
							</tr>
							<tr>
								<td align="center" bgcolor="#f0f0f0">
									<table border="0" cellpadding="0" cellspacing="0" width="90%">
										<tr>
											<td class="" height="38" colspan="5"></td>
										</tr>
										<tr>
											<td width="50" height="25" align="left" valign="middle"><font color="#666666" style="font-size:9pt; letter-spacing:-1pt;"><b>���̵�</b></font></td>
											<td align="left" valign="middle"><input type="text" name="login_id" required hname="���̵�" option="userid" value="<?=$login_id?>" style="width:95%; height:17px;" class="simpleform" tabindex="1" /></td>
											<td width="70" rowspan="2" align="right" valign="middle"><input src="./Libs/_images/btn_intro_login.gif" tabindex="3" type="image" alt="�α���"></td>
										</tr>
										<tr>
											<td height="25" align="left" valign="middle"><font color="#666666" style="font-size:9pt; letter-spacing:-1pt;"><b>��й�ȣ</b></font></td>
											<td align="left" valign="middle"><input type="password" name="login_pw" required hname="��й�ȣ" value="<?=$login_pw?>" style="width:95%;height:17px;" class="simpleform" tabindex="2" /></td>
											<td align="left" valign="middle"></td>
										</tr>
										<tr>
											<td class="" height="38" colspan="5"></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</form>
					<!--ȸ���α��γ�-->
				</td>
<?php
// �Ǹ������� �����Ǿ� �ִ� ���
if(($auth->configs['use_jumin']=='yes' || $auth->configs['use_ipin']=='yes') &&
	($auth->pin_settings['use_jumin']=='yes' || $auth->pin_settings['use_ipin']=='yes')) {
?>
				<td width="30"></td>
				<td width="360" align="right">
					<?php
					// �Ǹ�Ȯ�� �� �ε�
					include_once './rankup_module/rankup_authentic/'.PIN_MODULE.'/verify_intro.inc.php';
					?>
				</td>
<?php
}
?>
			</tr>
			<tr>
				<td height="20" colspan="3"></td>
			</tr>
			<tr>
				<td colspan="3" align="right">
					<?php
					if($config_info['membership_age']=='19over') echo '<a href="'.$intro['out_url'].'"><img src="./Libs/_images/btn_leave_site.gif" alt="19���̸�������" /></a>';
					else echo '<a href="'.$intro['out_url'].'"><img src="./Libs/_images/btn_leave_site2.gif" alt="����Ʈ������" /></a>';
					?>
				</td>
			</tr>
		</table>

	</div>
</div>

<script type="text/javascript">
//<![CDATA[
var $login = Object.clone($form);
$login.url = domain +'mypage';
$login.hashes = {mode: 'login'}
$login.handler = function(trans) { proc.response(trans) };
//]]>
</script>
