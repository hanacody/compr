
<style type="text/css">
.fv {font-family:verdana;color:#555;text-align:center;font-weight:bold;letter-spacing:-1px}
#limit_times {font-family:verdana;color:#3366cc;font-weight:bold}
</style>
<div id="sms_verify_frame" style="position:absolute;width:350px;display:none;z-index:2;background:#fff;padding:15px" class="Form_box">

	<div style="position:relative;margin-bottom:15px">
		<h2>휴대폰인증</h2>
		<div style="position:absolute;right:0;top:0"><a onClick="sms_verify.close()"><img src="./img/btn_close.png" /></a></div>
	</div>

	<form id="sms_verify_form" name="sms_verify_form" action="javascript:return void(0)" onSubmit="return false" style="border:1px solid #ccc">
		<table width="100%" cellpadding="6" cellspacing="0" border="0">
			<tr>
				<td width="70" bgcolor="#f4f4f4"><img src="../../Libs/_images/ic_dot1.gif" /> 휴대전화</td>
				<td>
					<ul>
						<li style="float:left">
							<input type="text" name="hphone_number[]" size="3" required hname="휴대전화번호" maxlength="3" option="hphone" span="3" glue="-" class="simpleform fv" /> -
							<input type="text" name="hphone_number[]" size="3" maxlength="4" class="simpleform fv" /> -
							<input type="text" name="hphone_number[]" size="3" maxlength="4" class="simpleform fv" />
						</li>
						<li style="float:left;margin-left:5px"><a onClick="sms_verify.send()"><img src="../rankup_sms/img/btn_confirm.gif" value="인증번호 받기" align="absmiddle" /></a></li>
					</ul>
				</td>
			</tr>
			<tr>
				<td bgcolor="#f4f4f4"><img src="../../Libs/_images/ic_dot1.gif" /> 인증번호</td>
				<td>
					<ul>
						<li style="float:left"><input type="text" id="verify_number" name="verify_number" size="8" maxlength="6" hname="인증번호" option="number" class="simpleform fv" /></li>
						<li style="float:left;margin-left:5px"><a onClick="sms_verify.check()"><img src="../rankup_sms/img/btn_ok.gif" alt="확인" /></a></li>
					</ul>
				</td>
			</tr>
			<tr>
				<td bgcolor="#f4f4f4"><img src="../../Libs/_images/ic_dot1.gif" /> 남은시간</td>
				<td id="limit_times"><span class="tip">인증번호받기로 인증번호를 받으시기 바랍니다.</span></td>
			</tr>
		</table>
	</form>

	<div style="border:0px solid #ddd;background-color:#f4f4f4;padding:10px;margin-top:8px;font-size:11px">
		· 인증번호받기를 클릭하시면 인증번호가 전송됩니다.<br />
		· 휴대전화로 전송된 인증번호를 입력란에 입력하여 주십시오.<br />
		· 인증번호받기는 하루 동안 <b class="tip">최대 <?=$rankup_member->vsms_send_limits?>회</b>까지 전송하실 수 있습니다.
	</div>

	<div style="margin-top:10px;text-align:center">
		<a onClick="sms_verify.close()"><img src="../../Libs/_images/btn_close2.gif" alt="닫기" align="absmiddle" /></a>
	</div>
</div>

<script type="text/javascript" src="sms_verify.class.js"></script>
<script type="text/javascript"> sms_verify.initialize('sms_verify_frame') </script>