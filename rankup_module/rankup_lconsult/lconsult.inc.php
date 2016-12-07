
<div id="consul">
	<h2 class="img_bgc"><img src="<?=$base_url.SKIN?>img/tit_consul.png" alt="consultation letter"/></h2>
	<div class="sms_box">
		<form id="lconsult_form" name="lconsult_form" action="javascript:void(0)" onSubmit="return $lconsult.submit(this, 'lconsult_form', '문의글을 등록하시겠습니까?')">
			<p><img src="<?=$base_url.SKIN?>img/sms_top.png" /></p>
			<div id="lconsult_tip" class="box_input" onClick="$(this).hide(); $('l_consult').focus()" style="position:absolute;margin:6px 0 0 4px">이곳을 통해 문자를 남겨주시면,<br />성심성의껏 상담해 드리도록<br />하겠습니다.</div>
			<textarea id="l_consult" name="l_consult" required hname="문의내용" class="simpleform box_input" style="height:54px;overflow-y:auto;color:#666" onFocus="$('lconsult_tip').hide()"></textarea>
			<dl class="info_input">
				<dd class="con_tit"><label for="l_phone">연락처</label><input type="text" id="l_phone" name="l_phone" required hname="연락처" maxlength="20" title="연락처입력" /></dd>
				<dd class="con_tit"><label for="l_name">이&nbsp;&nbsp;&nbsp;름</label><input type="text" id="l_name" name="l_name" required hname="이름" maxlength="20" title="이름입력" /></dd>
				<dd class="btn_send col_basic"><input type="image" src="<?=$base_url.SKIN?>img/tit_send.png" alt="send" /></dd>
			</dl>
		</form>
	</div>
</div>
<script type="text/javascript" src="<?=$base_url?>rankup_module/rankup_lconsult/lconsult.js"></script>
