/**
 * ���ڻ�� ���
 */
var $lconsult = Object.clone($form);
$lconsult.hashes = {mode: 'save_lconsult'};
$lconsult.url = domain +'rankup_module/rankup_lconsult';
$lconsult.handler = function(trans) {
	if(!trans.responseText.blank()) proc.response(trans);
	else {
		alert('��ϵǾ����ϴ�.');
		$('lconsult_form').reset();
		$('lconsult_tip').show();
	}
}