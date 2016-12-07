<?php
/**
 * �Ǹ����� ������
 */
?>


<link rel="stylesheet" type="text/css" href="<?=$base_url?>rankup_module/rankup_authentic/nice/verify_join.css" />

<div id="join_title">
	<h2><?=str_replace(array(' ', '(', ')'), array('', '<font style="color:#777">(', ')</font>'), $rankup_member->member_types[$_SESSION['member_type']])?> ��������</h2>
	<h3>������ ȸ�������� ���Ͽ� ���������� ������ �ֽʽÿ�.</h3>
</div>

<?php

// �⺻����
//@note: ����+���� �� Ȯ���Ѵ�.
if($_SESSION['member_type']=='outforeign' ||
	($auth->configs['use_jumin']!='yes' && $auth->configs['use_ipin']!='yes') ||
	($auth->pin_settings['use_jumin']!='yes' && $auth->pin_settings['use_ipin']!='yes')) {
?>

<div id="basic_frame">
	<form id="basic_form" name="basic_form" action="javascript:void(0)" onSubmit="return false">
		<input type="hidden" name="foreigner" value="<?=($_SESSION['member_type']=='inforeign') ? 2 : 1 ?>" />

		<div style="position:relative;padding-right:50%;border:1px solid #ccc;padding:20px 50% 25px 20px">
			<div style="width:100%">
				<table width="100%" border="0" cellpadding="0" cellspacing="0" style="table-layout: fixed">
					<col width="130" />
					<col />
					<tr><td class="Form_top" colspan="2">&nbsp;</td></tr>
					<tr>
						<td class="Form_ess" class="height:25px;">�̸�</td>
						<td class="Form_right" class="height:25px;"><input type="text" name="userNm" required hname="�̸�" size="30" maxlength="40" class="input" /></td>
					</tr>
					<tr>
						<td class="Form_ess">�������</td>
						<td class="Form_right">
							<select id="year" name="birthday[]" required hname="�⵵" title="�⵵ ����" onChange="draw_day('year', 'month', 'day')" style="width:75px">
								<option value="">�⵵</option>
								<?php
								// �⵵
								$years = array();
								$age_limits = date('Y');
								if($config_info['membership_age']=='14over') $age_limits -= 14;
								else if($config_info['membership_age']=='19over') $age_limits -= 19;
								foreach(range(1900, $age_limits) as $year) {
									array_push($years, sprintf('<option value="%d">%d��</option>', $year, $year));
								}
								echo implode('', array_reverse($years));
								?>
							</select>
							<select id="month" name="birthday[]" required hname="��" title="�� ����" onChange="draw_day('year', 'month', 'day')" style="width:60px">
								<option value="">��</option>
								<?php
								// ��
								foreach(range(1, 12) as $month) {
									echo sprintf('<option value="%02d">%d��</option>', $month, $month);
								}
								?>
							</select>
							<select id="day" name="birthday[]" required hname="��" title="�� ����" style="width:60px">
								<option value="">��</option>
							</select>
						</td>
					</tr>
					<tr>
						<td class="Form_ess">����</td>
						<td class="Form_right">
							<input type="radio" class="input_box" name="gender" checked value="1" id="gender_male" /><label for="gender_male">����<label>
							<input type="radio" class="input_box" name="gender" value="2" id="gender_female" /><label for="gender_female">����<label>
						</td>
					</tr>
				</table>
			</div>
			<div style="width:48%;position:absolute;top:0;right:0;padding:20px 0 20px">
				<ol class="hint_mass">
					<li>�ֹε�Ϲ�ȣ �Է� ���� ȸ�������� �����մϴ�.</li>
					<li>�̸�, ������ϰ� ������ <span>���ԿϷ� �� ������ �� �����ϴ�.</span></li>
				</ol>
			</div>
		</div>

		<div style="margin-top:30px;text-align:center;">
			<a onClick="joint.go()"><img src="./img/btn_ok.gif" /></a>
			<a onClick="location.replace('./join_intro.html')"><img src="./img/btn_cancel.gif" alt="�������" /></a>
		</div>
	</form>
</div>

<script type="text/javascript">
//<!CDATA[
var joint = {
	go: function() {
		if(!validate(Form.getElements('basic_form'))) return false;
		proc.parameters({mode:'verify_join', step: 3}, 'basic_form');
		proc.process(function(trans) { proc.response(trans) });
	}
}
//]]>
</script>

<?php

	return;
}
?>

<?php
// �Ǹ�Ȯ��/������(I-Pin) �����
if($auth->configs['use_jumin']=='yes' && $auth->configs['use_ipin']=='yes') {
	if($auth->pin_settings['use_jumin']=='yes' && $auth->pin_settings['use_ipin']=='yes') {
?>
<div class="tab">
	<h1>��������</h1>
	<div class="tip"><a onClick="verify.tip(this, 'ipin_tip')">������ ����</a></div>
	<div id="ipin_tip" style="display:none">
		<div class="close"><a onClick="$('ipin_tip').hide()">��</a></div>
		<div class="wrap">
			<b>������(I-Pin)�̶�?</b>
			<p>���ͳ� �󿡼� �ֹε�Ϲ�ȣ�� ����Ͽ� �������� Ȯ�� ���� �� �ִ�<br />���̹� �ſ� Ȯ�ι�ȣ�Դϴ�.</p>
		</div>
	</div>
	<ul>
		<li onClick="verify.tab(this)" class="on"><input type="radio" name="vmKind" class="input_box" checked value="jumin" id="vmKind_jumin" /><label for="vmKind_jumin">�Ǹ�Ȯ��</label></li>
		<li onClick="verify.tab(this)" class="end"><input type="radio" name="vmKind"  class="input_box" value="ipin" id="vmKind_ipin" /><label for="vmKind_ipin">������(I-Pin)</label></li>
	</ul>
</div>
<?php
	}
}

// �Ǹ������� ��� ����

$jumin_display = ($auth->configs['use_jumin']!='yes') ? 'none' : '';

$userno_text = ($_SESSION['member_type']=='inforeign') ? '�ܱ��ε�Ϲ�ȣ' : '�ֹε�Ϲ�ȣ';
?>
<div id="jumin_frame" class="box" style="display:<?=$jumin_display?>">
	<h1>�Ǹ�Ȯ��</h1>
	<form id="jumin_form" name="jumin_form" action="javascript:void(0)" onSubmit="return jumin_validate(this) && $jumin.submit(this, 'jumin_form')" autocomplete="off">
		<input type="hidden" name="foreigner" value="<?=($_SESSION['member_type']=='inforeign') ? 2 : 1 ?>" />
		<dl class="box">
			<dt>����</dt><dd><input type="text" name="userNm" size="20" maxlength="30" class="input" /></dd>
			<dt id="userno_text"><?=$userno_text?></dt><dd><input type="text" name="userNo1" maxlength="6" class="input sno" onKeyUp="if(this.value.length==6) $('userNo2').focus()" /></dd>
			<dt>-</dt>
			<dd><input type="password" id="userNo2" name="userNo2" maxlength="7" class="input sno" /></dd>
			<dt><input type="image" src="<?=$base_url?>rankup_module/rankup_authentic/img/btn_verify.gif" alt="�Ǹ�Ȯ��" align="absmiddle" /></dt>
		</dl>
		<ul class="hint">
			<li>Ÿ���� �ֹε�Ϲ�ȣ�� ���Ƿ� ����ϸ� '�ֹε�Ϲ�'�� ���� 3�� ������ ¡�� �Ǵ� 1õ���� ������ ������ �ΰ��� �� �ֽ��ϴ�.</li>
			<li>�Է��Ͻ� <?=$userno_text?>�� <b style="color:#f60"><?=$config_info['site_name']?>�� ���� ������� ������</b>, �ſ��򰡱���� ���� �Ǹ�Ȯ�ο����θ� �̿�˴ϴ�.</li>
		</ul>
	</form>
</div>

<?php
// ������(I-Pin)���� �� ��� ����
if($auth->configs['use_ipin'] == 'yes' && $auth->configs['use_jumin'] == "yes") $ipin_display = "none";
else $ipin_display = ($auth->configs['use_ipin'] != 'yes') ? 'none' : '';
?>
<div id="ipin_frame" style="display:<?=$ipin_display?>" class="box">
	<h1>������(I-Pin)����</h1>
	<form name="request_form" method="POST">
		<input type="hidden" id="SendInfo" name="SendInfo" />
		<input type="hidden" id="ProcessType" name="ProcessType" />
	</form>
	<p class="box">
		������ ������ ���� ������ ���Ͻø�<br>
		������ ���� ��ư�� ���� <b><?=$config_info['site_name']?></b> ȸ�������� ������ �ֽʽÿ�.
	</p>
	<center>
		<a onClick="ipin_pop()"><img src="http://image.creditbank.co.kr/static/img/vno/new_img/bt_26.gif" align="absmiddle" /></a>
	</center>
</div>
<input type="hidden" id="pin_kind" name="pin_kind" value="<?=$auth->pin_kind?>" />

<script type="text/javascript" src="http://secure.nuguya.com/nuguya/nice.nuguya.oivs.crypto.js"></script>
<script type="text/javascript" src="http://secure.nuguya.com/nuguya/<?php echo (rankup_basic::default_charset()!='euc-kr') ? 'nice.nuguya.oivs.msgg.utf8.js' : 'nice.nuguya.oivs.msg.js' ?>"></script>
<script type="text/javascript" src="http://secure.nuguya.com/nuguya/nice.nuguya.oivs.util.js"></script>
<script type="text/javascript" src="<?=$config_info['domain']?>rankup_module/rankup_authentic/nice/verify.js"></script>

<div style="margin-top:30px;text-align:center">
	<a onClick="location.replace('./join_intro.html')"><img src="./img/btn_cancel.gif" alt="�������" /></a>
</div>
