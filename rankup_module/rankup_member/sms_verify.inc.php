
<style type="text/css">
.fv {font-family:verdana;color:#555;text-align:center;font-weight:bold;letter-spacing:-1px}
#limit_times {font-family:verdana;color:#3366cc;font-weight:bold}
</style>
<div id="sms_verify_frame" style="position:absolute;width:350px;display:none;z-index:2;background:#fff;padding:15px" class="Form_box">

	<div style="position:relative;margin-bottom:15px">
		<h2>�޴�������</h2>
		<div style="position:absolute;right:0;top:0"><a onClick="sms_verify.close()"><img src="./img/btn_close.png" /></a></div>
	</div>

	<form id="sms_verify_form" name="sms_verify_form" action="javascript:return void(0)" onSubmit="return false" style="border:1px solid #ccc">
		<table width="100%" cellpadding="6" cellspacing="0" border="0">
			<tr>
				<td width="70" bgcolor="#f4f4f4"><img src="../../Libs/_images/ic_dot1.gif" /> �޴���ȭ</td>
				<td>
					<ul>
						<li style="float:left">
							<input type="text" name="hphone_number[]" size="3" required hname="�޴���ȭ��ȣ" maxlength="3" option="hphone" span="3" glue="-" class="simpleform fv" /> -
							<input type="text" name="hphone_number[]" size="3" maxlength="4" class="simpleform fv" /> -
							<input type="text" name="hphone_number[]" size="3" maxlength="4" class="simpleform fv" />
						</li>
						<li style="float:left;margin-left:5px"><a onClick="sms_verify.send()"><img src="../rankup_sms/img/btn_confirm.gif" value="������ȣ �ޱ�" align="absmiddle" /></a></li>
					</ul>
				</td>
			</tr>
			<tr>
				<td bgcolor="#f4f4f4"><img src="../../Libs/_images/ic_dot1.gif" /> ������ȣ</td>
				<td>
					<ul>
						<li style="float:left"><input type="text" id="verify_number" name="verify_number" size="8" maxlength="6" hname="������ȣ" option="number" class="simpleform fv" /></li>
						<li style="float:left;margin-left:5px"><a onClick="sms_verify.check()"><img src="../rankup_sms/img/btn_ok.gif" alt="Ȯ��" /></a></li>
					</ul>
				</td>
			</tr>
			<tr>
				<td bgcolor="#f4f4f4"><img src="../../Libs/_images/ic_dot1.gif" /> �����ð�</td>
				<td id="limit_times"><span class="tip">������ȣ�ޱ�� ������ȣ�� �����ñ� �ٶ��ϴ�.</span></td>
			</tr>
		</table>
	</form>

	<div style="border:0px solid #ddd;background-color:#f4f4f4;padding:10px;margin-top:8px;font-size:11px">
		�� ������ȣ�ޱ⸦ Ŭ���Ͻø� ������ȣ�� ���۵˴ϴ�.<br />
		�� �޴���ȭ�� ���۵� ������ȣ�� �Է¶��� �Է��Ͽ� �ֽʽÿ�.<br />
		�� ������ȣ�ޱ�� �Ϸ� ���� <b class="tip">�ִ� <?=$rankup_member->vsms_send_limits?>ȸ</b>���� �����Ͻ� �� �ֽ��ϴ�.
	</div>

	<div style="margin-top:10px;text-align:center">
		<a onClick="sms_verify.close()"><img src="../../Libs/_images/btn_close2.gif" alt="�ݱ�" align="absmiddle" /></a>
	</div>
</div>

<script type="text/javascript" src="sms_verify.class.js"></script>
<script type="text/javascript"> sms_verify.initialize('sms_verify_frame') </script>