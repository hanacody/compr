<?php
/**
 * �Ǹ����� ������
 */
?>

<!--��ȸ����������-->
<link rel="stylesheet" type="text/css" href="<?=$base_url?>rankup_module/rankup_authentic/nice/verify_intro.css" />
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align="left"><img src="./Libs/_images/intro_tit02.png" alt="��ȸ������"></td>
	</tr>
	<tr>
		<td height="10"></td>
	</tr>
	<tr>
		<td class="non_mem" align="center" bgcolor="#f0f0f0">

			<table border="0" cellpadding="0" cellspacing="0" width="90%" class="name_box" >
				<tr>
					<td height="50" align="left" valign="top">

<?php
if($auth->configs['use_jumin']=='yes' && $auth->configs['use_ipin']=='yes' &&
	$auth->pin_settings['use_jumin']=='yes' && $auth->pin_settings['use_ipin']=='yes') {
?>
						<div class="tab">
							<h1>��������</h1>
							<div class="tip"><a onClick="verify.tip(this, 'ipin_tip')">������ ����</a></div>
							<div id="ipin_tip" style="display:none">
								<div class="close"><a onClick="$('ipin_tip').hide()">��</a></div>
								<div class="wrap">
									<b>������(I-Pin)�̶�?</b>
									<p>���ͳ� �󿡼� �ֹε�Ϲ�ȣ�� ����Ͽ� �������� Ȯ�� ���� �� �ִ� ���̹� �ſ� Ȯ�ι�ȣ�Դϴ�.</p>
								</div>
							</div>
							<ul>
								<li onClick="verify.tab(this)" class="on"><input type="radio" name="vmKind" checked value="jumin" id="vmKind_jumin" style="display:none;" /><label for="vmKind_jumin">�Ǹ�Ȯ��</label></li>
								<li onClick="verify.tab(this)" class="end"><input type="radio" name="vmKind" value="ipin" id="vmKind_ipin" style="display:none;" /><label for="vmKind_ipin">������(I-Pin)</label></li>
							</ul>
						</div>
<?php
}

// �Ǹ������� ��� ����
$jumin_display = ($auth->configs['use_jumin']!='yes') ? 'none' : '';
?>

						<div id="jumin_frame" style="display:<?=$jumin_display?>">
							<h1>�Ǹ�����</h1>
							<form id="jumin_form" name="jumin_form" action="javascript:void(0)" onSubmit="return jumin_validate(this) && $jumin.submit(this, 'jumin_form')" autocomplete="off">
								<div class="kind_mem">
								<?php

								// ��Ʈ�������������� �ǳ���
								if(strpos($config_info['membership_types'], '14')!==false && strpos($config_info['membership_types'], 'inforeign')!==false) {
									echo '<input type="radio" class="input_box2" name="foreigner" onClick="verify.foreigner(this.value)" checked value="1" id="foreigner_1"><label for="foreigner_1">�Ϲ�ȸ��<span class="tip"> (������)</span></label>';
									echo ' <input type="radio" class="input_box2" name="foreigner" onClick="verify.foreigner(this.value)" value="2" id="foreigner_2"><label for="foreigner_2">�����ܱ���ȸ��</label>';
								}
								else if(strpos($config_info['membership_types'], '14')!==false) echo '<input type="hidden" name="foreigner" value="1" />';
								else if(strpos($config_info['membership_types'], 'inforeign')!==false) echo ' <input type="hidden" name="foreigner" value="2" />';

								// ������ȣ �ؽ�Ʈ ����
								if(strpos($config_info['membership_types'], '14')!==false) $userno_text = '�ֹε�Ϲ�ȣ';
								else if(strpos($config_info['membership_types'], 'inforeign')!==false) $userno_text = '�ܱ��ε�Ϲ�ȣ';

								?>
								</div>
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<col width="85" />
									<col />
									<col width="70" />
									<tr>
										<td height="25" align="left"><font color="#666666" class="form_tit"><b>�̸�</b></font></td>
										<td align="left"><input type="text" name="userNm" size="20" maxlength="30" style="width:95%; height:16px;" class="simpleform" tabindex="4" /></td>
										<td rowspan="2" align="right"><input src="./Libs/_images/ok_btn.gif" tabindex="3" type="image" alt="�α���" /></td>
									</tr>
									<tr align="left">
										<td height="25"><font color="#666666" class="form_tit" style="font-size:9pt; letter-spacing:-1pt;"><b id="userno_text"><?=$userno_text?></b></font></td>
										<td>
											<input type="text" name="userNo1" maxlength="6" style="width:41%; height:16px;" class="simpleform" tabindex="5" onKeyUp="if(this.value.length==6) $('userNo2').focus()" /><span class="hipen">-</span><input type="password" id="userNo2" name="userNo2" maxlength="7" style="width:41%; height:16px;" class="simpleform" tabindex="6" />
										</td>
									</tr>
								</table>
							</form>
						</div>
<?php
// ������(I-Pin)���� �� ��� ����
if($auth->configs['use_ipin'] == 'yes' && $auth->configs['use_jumin'] == "yes") $ipin_display = "none";
else $ipin_display = ($auth->configs['use_ipin'] != 'yes') ? 'none' : '';
?>
						<div id="ipin_frame" style="display:<?=$ipin_display?>">
							<h1>������(I-Pin)����</h1>
							<form name="request_form" method="POST">
								<input type="hidden" id="SendInfo" name="SendInfo" />
								<input type="hidden" id="ProcessType" name="ProcessType" />
							</form>
							<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ip_box">
								<tr>
									<td align="left" style="padding-left:3px">
										<font color="#666666" style="font-size:9pt; letter-spacing:-1pt;">
											������ ������ ���� ����Ʈ �̿��� ���Ͻø�<br />
											������ ���� ��ư�� ���� �Ǹ������� ���ּ���.
										</font>
									</td>
									<td width="70" align="right"><a onclick="ipin_pop()"><img src="./Libs/_images/btn_intro_ipin.gif" align="absmiddle" /></a></td>
								</tr>
							</table>
						</div>

					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<!--��ȸ��������-->

<input type="hidden" id="pin_kind" name="pin_kind" value="<?=$auth->pin_kind?>" />

<script type="text/javascript" src="http://secure.nuguya.com/nuguya/nice.nuguya.oivs.crypto.js"></script>
<script type="text/javascript" src="http://secure.nuguya.com/nuguya/<?php echo (rankup_basic::default_charset()!='euc-kr') ? 'nice.nuguya.oivs.msgg.utf8.js' : 'nice.nuguya.oivs.msg.js' ?>"></script>
<script type="text/javascript" src="http://secure.nuguya.com/nuguya/nice.nuguya.oivs.util.js"></script>
<script type="text/javascript" src="<?=$config_info['domain']?>rankup_module/rankup_authentic/nice/verify.js"></script>
