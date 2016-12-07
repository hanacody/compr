// ������Ŭ����
var RANKUP_CALLCENTER = function() {
	this.version = "1.0 r110511", // �����Ͱ��߹���
	this.items = new Object;
	this.callcenters = []; // �����͵�����
	this.template = ''; // ���ø�
}
// ������ ��������
RANKUP_CALLCENTER.prototype.getCallcenter = function() {
	var classObj = this;
	new Ajax.Request(domain+"rankup_module/rankup_callcenter/multiProcess.html?mode=callcenter_list", {
		method: 'get',
		onSuccess: function(transport) {
			var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
			classObj.items = resultData.getElementsByTagName('item');
			classObj.formalize();
		}
	});
}
// ������ ���
RANKUP_CALLCENTER.prototype.formalize = function() {
	for(var i=0; i<this.items.length; i++) {
		var item = this.items[i];
		var pNo = item.getAttribute("no");
		var pop = {
			no: item.getAttribute("no"),
			content: item.getElementsByTagName("content")[0].firstChild.nodeValue
		};
		this.view(pop);
	}
}
// ������ ����
RANKUP_CALLCENTER.prototype.view = function(pop) {
	// ���ø� ����
	var content = this.template.innerHTML.replace(/{:no:}/g, pop.no);
	new Insertion.After(this.template, "<div id='divpop_id"+pop.no+"'>"+content+"</div>");
	var divpop = $('divpop_id'+pop.no);
	// ������ ������ �Է�
	try {
		var pop_obj = $("callcenter_content"+pop.no);
		pop_obj.innerHTML =  pop.content;
	}
	catch(e) {
		//alert(e.message);
	}
}
// ������ ����
RANKUP_CALLCENTER.prototype.initialize = function(template) {
	this.template = $(template); // ���ø�
	this.cookies = document.cookie; // ��Ű�ε�
	this.getCallcenter();
}
var rankup_callcenter = new RANKUP_CALLCENTER;