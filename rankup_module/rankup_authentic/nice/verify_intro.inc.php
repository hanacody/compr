<?php
/**
 * 실명인증 페이지
 */
?>

<!--비회원인증시작-->
<link rel="stylesheet" type="text/css" href="<?=$base_url?>rankup_module/rankup_authentic/nice/verify_intro.css" />
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align="left"><img src="./Libs/_images/intro_tit02.png" alt="비회원인증"></td>
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
							<h1>인증종류</h1>
							<div class="tip"><a onClick="verify.tip(this, 'ipin_tip')">아이핀 도움말</a></div>
							<div id="ipin_tip" style="display:none">
								<div class="close"><a onClick="$('ipin_tip').hide()">×</a></div>
								<div class="wrap">
									<b>아이핀(I-Pin)이란?</b>
									<p>인터넷 상에서 주민등록번호를 대신하여 본인임을 확인 받을 수 있는 사이버 신원 확인번호입니다.</p>
								</div>
							</div>
							<ul>
								<li onClick="verify.tab(this)" class="on"><input type="radio" name="vmKind" checked value="jumin" id="vmKind_jumin" style="display:none;" /><label for="vmKind_jumin">실명확인</label></li>
								<li onClick="verify.tab(this)" class="end"><input type="radio" name="vmKind" value="ipin" id="vmKind_ipin" style="display:none;" /><label for="vmKind_ipin">아이핀(I-Pin)</label></li>
							</ul>
						</div>
<?php
}

// 실명인증폼 출력 설정
$jumin_display = ($auth->configs['use_jumin']!='yes') ? 'none' : '';
?>

						<div id="jumin_frame" style="display:<?=$jumin_display?>">
							<h1>실명인증</h1>
							<form id="jumin_form" name="jumin_form" action="javascript:void(0)" onSubmit="return jumin_validate(this) && $jumin.submit(this, 'jumin_form')" autocomplete="off">
								<div class="kind_mem">
								<?php

								// 인트로페이지에서는 탭노출
								if(strpos($config_info['membership_types'], '14')!==false && strpos($config_info['membership_types'], 'inforeign')!==false) {
									echo '<input type="radio" class="input_box2" name="foreigner" onClick="verify.foreigner(this.value)" checked value="1" id="foreigner_1"><label for="foreigner_1">일반회원<span class="tip"> (내국인)</span></label>';
									echo ' <input type="radio" class="input_box2" name="foreigner" onClick="verify.foreigner(this.value)" value="2" id="foreigner_2"><label for="foreigner_2">국내외국인회원</label>';
								}
								else if(strpos($config_info['membership_types'], '14')!==false) echo '<input type="hidden" name="foreigner" value="1" />';
								else if(strpos($config_info['membership_types'], 'inforeign')!==false) echo ' <input type="hidden" name="foreigner" value="2" />';

								// 인증번호 텍스트 정의
								if(strpos($config_info['membership_types'], '14')!==false) $userno_text = '주민등록번호';
								else if(strpos($config_info['membership_types'], 'inforeign')!==false) $userno_text = '외국인등록번호';

								?>
								</div>
								<table width="100%" border="0" cellpadding="0" cellspacing="0">
									<col width="85" />
									<col />
									<col width="70" />
									<tr>
										<td height="25" align="left"><font color="#666666" class="form_tit"><b>이름</b></font></td>
										<td align="left"><input type="text" name="userNm" size="20" maxlength="30" style="width:95%; height:16px;" class="simpleform" tabindex="4" /></td>
										<td rowspan="2" align="right"><input src="./Libs/_images/ok_btn.gif" tabindex="3" type="image" alt="로그인" /></td>
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
// 아이핀(I-Pin)인증 폼 출력 설정
if($auth->configs['use_ipin'] == 'yes' && $auth->configs['use_jumin'] == "yes") $ipin_display = "none";
else $ipin_display = ($auth->configs['use_ipin'] != 'yes') ? 'none' : '';
?>
						<div id="ipin_frame" style="display:<?=$ipin_display?>">
							<h1>아이핀(I-Pin)인증</h1>
							<form name="request_form" method="POST">
								<input type="hidden" id="SendInfo" name="SendInfo" />
								<input type="hidden" id="ProcessType" name="ProcessType" />
							</form>
							<table width="100%" border="0" cellpadding="0" cellspacing="0" class="ip_box">
								<tr>
									<td align="left" style="padding-left:3px">
										<font color="#666666" style="font-size:9pt; letter-spacing:-1pt;">
											아이핀 인증을 통한 사이트 이용을 원하시면<br />
											아이핀 인증 버튼을 눌러 실명인증을 해주세요.
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
<!--비회원인증끝-->

<input type="hidden" id="pin_kind" name="pin_kind" value="<?=$auth->pin_kind?>" />

<script type="text/javascript" src="http://secure.nuguya.com/nuguya/nice.nuguya.oivs.crypto.js"></script>
<script type="text/javascript" src="http://secure.nuguya.com/nuguya/<?php echo (rankup_basic::default_charset()!='euc-kr') ? 'nice.nuguya.oivs.msgg.utf8.js' : 'nice.nuguya.oivs.msg.js' ?>"></script>
<script type="text/javascript" src="http://secure.nuguya.com/nuguya/nice.nuguya.oivs.util.js"></script>
<script type="text/javascript" src="<?=$config_info['domain']?>rankup_module/rankup_authentic/nice/verify.js"></script>
