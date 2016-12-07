/**
 * 나이스 실명인증 처리 스크립트
 *@author: kurokisi
 *@authDate: 2012.01.12
 */
// 인증페이지 구성
var verify = {
	tab: function(el) {
		el = $(el);
		if(el.hasClassName('on')) return;

		var pre = el.up().select('li[class~="on"]')[0];
		pre.removeClassName('on');
		$(pre.select('input')[0].value+'_frame').hide();

		el.addClassName('on');
		$(el.select('input')[0].value+'_frame').show();
		el.select('input')[0].checked = true;
	},
	foreigner: function(value) {
		$('userno_text').update((value=='1') ? '주민등록번호' : '외국인등록번호');
	},
	tip: function(el, frame) {
		var frame = $(frame);
		frame.show();
		var offset = $(el).positionedOffset();
		frame.setStyle({
			left: offset.left + $(el).offsetWidth - frame.offsetWidth + 'px',
			top: offset.top + 'px'
		});
	}
}
// 실명인증폼 검증
function jumin_validate(form) {
	var userNm = form.userNm;
	var userNo1 = form.userNo1;
	var userNo2 = form.userNo2;
	var foreigners = $(form).select('input[name="foreigner"]');
	if(foreigners.length>1) {
		var foreigner = foreigners[1].checked ? foreigners[1].value : foreigners[0].value;
	}
	else {
		var foreigner = foreigners[0].value;
	}
	var userNo = userNo1.value + userNo2.value;

	if(userNm.value=='') {
		alert(getCheckMessage('S23'));
		userNm.focus();
		return false;
	}
	if(userNo1.value=='') {
		if(foreigner=='2') alert(getCheckMessage('S27'));
		else alert(getCheckMessage('S21'));
		userNo1.focus();
		return false;
	}
	if(userNo2.value=='') {
		if(foreigner=='2') alert( getCheckMessage('S27'));
		else alert( getCheckMessage('S21'));
		userNo2.focus();
		return false;
	}
	if(foreigner=='2') {
		if(checkForeignNm(userNm.value)==false) {
			alert(getCheckMessage('S28'));
			userNm.focus();
			return false;
		}
		if(checkForeignNo(userNo)==false) {
			alert(getCheckMessage('S26'));
			userNo2.focus();
			return false;
		}
	}
	else {
		if(checkString(userNm.value)==false) {
			alert(getCheckMessage('S24'));
			userNm.focus();
			return false;
		}
		if(checkNumeric(userNo)==false) {
			alert(getCheckMessage('S25'));
			userNo1.focus();
			return false;
		}
	}
	return true;
}

var $jumin = Object.clone($form);
$jumin.url = domain + 'rankup_module/rankup_authentic/nice';
$jumin.hashes = {mode:'jumin_validate', pin_kind:$F('pin_kind')}
$jumin.handler = function(trans) { proc.response(trans) }

// 나이스아이핀인증 팝업
function ipin_pop() {
	proc.parameters({mode:'ipin_validate', dest:'request_form', pin_kind:$F('pin_kind')});
	proc.process(function(trans) { proc.response(trans) }, false, domain +'rankup_module/rankup_authentic/nice');
}
// 인증정보 검증
var verify_infos = function(mode, value) {
	proc.parameters({mode:mode, value:value, pin_kind:$F('pin_kind')});
	proc.process(function(trans) { proc.response(trans) }, false, domain +'rankup_module/rankup_authentic/nice');
}