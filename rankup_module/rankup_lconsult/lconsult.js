/**
 * 문자상담 등록
 */
var $lconsult = Object.clone($form);
$lconsult.hashes = {mode: 'save_lconsult'};
$lconsult.url = domain +'rankup_module/rankup_lconsult';
$lconsult.handler = function(trans) {
	if(!trans.responseText.blank()) proc.response(trans);
	else {
		alert('등록되었습니다.');
		$('lconsult_form').reset();
		$('lconsult_tip').show();
	}
}