
<div id="consul">
	<h2 class="img_bgc"><img src="<?=$base_url.SKIN?>img/tit_consul.png" alt="consultation letter"/></h2>
	<div class="sms_box">
		<form id="lconsult_form" name="lconsult_form" action="javascript:void(0)" onSubmit="return $lconsult.submit(this, 'lconsult_form', '���Ǳ��� ����Ͻðڽ��ϱ�?')">
			<p><img src="<?=$base_url.SKIN?>img/sms_top.png" /></p>
			<div id="lconsult_tip" class="box_input" onClick="$(this).hide(); $('l_consult').focus()" style="position:absolute;margin:6px 0 0 4px">�̰��� ���� ���ڸ� �����ֽø�,<br />���ɼ��ǲ� ����� �帮����<br />�ϰڽ��ϴ�.</div>
			<textarea id="l_consult" name="l_consult" required hname="���ǳ���" class="simpleform box_input" style="height:54px;overflow-y:auto;color:#666" onFocus="$('lconsult_tip').hide()"></textarea>
			<dl class="info_input">
				<dd class="con_tit"><label for="l_phone">����ó</label><input type="text" id="l_phone" name="l_phone" required hname="����ó" maxlength="20" title="����ó�Է�" /></dd>
				<dd class="con_tit"><label for="l_name">��&nbsp;&nbsp;&nbsp;��</label><input type="text" id="l_name" name="l_name" required hname="�̸�" maxlength="20" title="�̸��Է�" /></dd>
				<dd class="btn_send col_basic"><input type="image" src="<?=$base_url.SKIN?>img/tit_send.png" alt="send" /></dd>
			</dl>
		</form>
	</div>
</div>
<script type="text/javascript" src="<?=$base_url?>rankup_module/rankup_lconsult/lconsult.js"></script>
