// 고객센터클래스
var RANKUP_CALLCENTER = function() {
	this.version = "1.0 r110511", // 고객센터개발버전
	this.items = new Object;
	this.callcenters = []; // 고객센터데이터
	this.template = ''; // 템플릿
}
// 고객센터 가져오기
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
// 고객센터 등록
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
// 고객센터 노출
RANKUP_CALLCENTER.prototype.view = function(pop) {
	// 템플릿 복사
	var content = this.template.innerHTML.replace(/{:no:}/g, pop.no);
	new Insertion.After(this.template, "<div id='divpop_id"+pop.no+"'>"+content+"</div>");
	var divpop = $('divpop_id'+pop.no);
	// 고객센터 컨텐츠 입력
	try {
		var pop_obj = $("callcenter_content"+pop.no);
		pop_obj.innerHTML =  pop.content;
	}
	catch(e) {
		//alert(e.message);
	}
}
// 고객센터 설정
RANKUP_CALLCENTER.prototype.initialize = function(template) {
	this.template = $(template); // 템플릿
	this.cookies = document.cookie; // 쿠키로드
	this.getCallcenter();
}
var rankup_callcenter = new RANKUP_CALLCENTER;