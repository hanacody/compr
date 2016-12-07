// 팝업클래스
var RANKUP_POPUP = function() {
	this.version = "1.1 r091112",	// 팝업개발버전
	this.mode = "divpop";			// 팝업모드{ divpop | winpop }
	this.items = new Object;
	this.popups = [];					// 팝업데이터
	this.template = '';					// 템플릿
	this.cookies = '';					// 쿠키정보
	this.baseTop = 0;
	this.baseLeft = 0;
	this.selTop = 0;
	this.selLeft = 0;
	this.tmpHeight = 0;
	this.move_on_off = 0; //이동 활성 비활성화
	this.move_div_obj = null;//이동할 DIV
	this.move_div_obj_before = null; //이전에 이전한 아이디값
	this.move_x_margin = 0; //X 값
	this.move_y_margin = 0;//Y 값
	this.blind = null;
}
// 팝업 가져오기
RANKUP_POPUP.prototype.getPopup = function() {
	var classObj = this;
	new Ajax.Request(domain+"rankup_module/rankup_popup/multiProcess.html?mode=popup_list", {
		method: 'get',
		onSuccess: function(transport) {
			var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
			classObj.items = resultData.getElementsByTagName('item');
			classObj.formalize();
		}
	});
}
// 팝업 등록
RANKUP_POPUP.prototype.formalize = function() {
	for(var i=0; i<this.items.length; i++) {
		var item = this.items[i];
		var pNo = item.getAttribute("no");
		if(this.cookies.indexOf("popup_id"+pNo+"=checked")!=-1) continue;
		var pop = {
			no: item.getAttribute("no"),
			title: item.getElementsByTagName("title")[0].firstChild.nodeValue,
			top: item.getElementsByTagName("top")[0].firstChild.nodeValue,
			left: item.getElementsByTagName("left")[0].firstChild.nodeValue,
			width: item.getElementsByTagName("width")[0].firstChild.nodeValue,
			height: item.getElementsByTagName("height")[0].firstChild.nodeValue,
			content: item.getElementsByTagName("content")[0].firstChild.nodeValue
		};
		this.view(pop);
	}
}
// 팝업 노출
RANKUP_POPUP.prototype.view = function(pop) {
	if(!document.all) this.mode = "divpop"; // 파폭에서는 divpop 으로 고정
	switch(this.mode) {
		case "divpop":
			// 템플릿 복사
			var content = this.template.innerHTML.replace(/{:no:}/g, pop.no).replace(/{:popup_id:}/g, "divpop_id"+pop.no).replace(/{:popup_title:}/g, pop.title);
			new Insertion.After(this.template, "<div id='divpop_id"+pop.no+"'>"+content+"</div>");
			var divpop = $('divpop_id'+pop.no);
			divpop.setStyle({
				position: 'absolute',
				top: this.selTop+'px',
				left: this.selLeft+'px',
				zIndex: 200
			});
			// 팝업 컨텐츠 입력
			try {
				var pop_obj = $("popup_content"+pop.no);
				var iframe = divpop.getElementsByTagName("iframe")[0];
				var doc = iframe.contentWindow.document;
				doc.open(); doc.write(pop.content); doc.close(); // 2010.06.30 fixed
				pop_obj.innerHTML = doc.body.innerHTML;
				doc.body.innerHTML = ''; // 2010.01.29 fixed
				if(pop.width) pop_obj.style.width = pop.width;
				if(pop.height) pop_obj.style.height = pop.height;
				this.calc_popup(divpop);
			}
			catch(e) {
				//alert(e.message);
			}
			break;

		case "winpop":
			// 미구현
			break;
	}
}
// 팝업 계산
RANKUP_POPUP.prototype.calc_popup = function(divpop) {
	var content_x = divpop.getElementsByTagName("table")[0];
	var iframe_x = divpop.getElementsByTagName("iframe")[0];
	if(content_x.offsetWidth==0) {
		var classObj = this;
		return setTimeout(function() { classObj.calc_popup(divpop) }, 0);
	}
	divpop.setStyle({ top: this.selTop, left: this.selLeft }); // 2009.03.31 fixed

	// 가로 사이즈를 오버할 경우, 세로사이즈가 큰값으로 갱신
	this.selLeft += content_x.offsetWidth-1;
	if(this.selLeft>document.body.offsetWidth) {
		divpop.style.top = this.selTop + this.tmpHeight-1;
		divpop.style.left = this.baseLeft;
		this.selLeft = this.baseLeft + content_x.offsetWidth-1;
		this.selTop += this.tmpHeight-1;
		this.tmpHeight = content_x.offsetHeight; // 갱신
	}
	if(content_x.offsetHeight>this.tmpHeight) this.tmpHeight = content_x.offsetHeight;
	if(browser.kind=='IE' && browser.version<7) {
		iframe_x.style.width = content_x.offsetWidth;
		iframe_x.style.height = content_x.offsetHeight;
	}
	else {
		// 2010.06.24 fixed
		iframe_x.style.display = 'none';
	}
	content_x.style.visibility = 'visible';
}
// 이동값 초기화
RANKUP_POPUP.prototype.div_move_check = function(onoff, ev, obj) {
	this.move_div_obj = obj ? $(obj) : this.move_div_obj;
	if(this.move_div_obj == null) return false;
	if(this.move_div_obj_before == null) {
		this.move_div_obj_before = this.move_div_obj;
		this.move_div_obj.setStyle({zIndex: this.move_div_obj.getStyle('z-index') + 1});
	}
	else if(this.move_div_obj_before.id !== this.move_div_obj.id) {
		var before_zindex = this.move_div_obj_before.getStyle('z-index');
		var after_zindex = this.move_div_obj.style.zIndex;
		this.move_div_obj.setStyle({zIndex: before_zindex + 1});
		this.move_div_obj_before = this.move_div_obj;
	}
	if(onoff == 0) document.onselectstart = function() { return true };
	else {
		document.onselectstart = function() { return false };
		this.move_x_margin = Event.pointerX(ev?ev:event) - this.move_div_obj.offsetLeft;
		this.move_y_margin =  Event.pointerY(ev?ev:event) - this.move_div_obj.offsetTop;
	}
	this.move_on_off = onoff;
}
// 창이동
RANKUP_POPUP.prototype.div_move = function(ev) {
	if(this.move_on_off == 1) {
		var x_result = Event.pointerX(ev?ev:event) - this.move_x_margin;
		var y_result = Event.pointerY(ev?ev:event) - this.move_y_margin;
		if(x_result > 0){this.move_div_obj.style.left = x_result;}
		if(y_result > 0){this.move_div_obj.style.top =  y_result;}
	}
}
// 쿠키굽기
RANKUP_POPUP.prototype.setCookie = function(no) {
	var today = new Date();
	var now = today.getHours() * 3600 + today.getMinutes() * 60 + today.getSeconds();
	today.setTime(today.getTime() + ((86400 - now)*1000));
	document.cookie = "popup_id"+no+"=checked; path=/; expires="+today.toGMTString()+";";
}
// 팝업창닫기
RANKUP_POPUP.prototype.closeWin = function(no, check) {
	if(this.mode=="divpop") {
		var divPop = $("divpop_id"+no);
		if(divPop.getElementsByTagName("input")[0].checked) this.setCookie(no);
		divPop.hide();
	}
	else if(check) this.setCookie(no);
}
// 팝업 설정
RANKUP_POPUP.prototype.initialize = function(template) {
	this.template = $(template); // 템플릿
	this.cookies = document.cookie; // 쿠키로드
	this.getPopup();
	//DIV 팝업이동
	var This = this;
	document.onmouseup= function(event) { This.div_move_check(0, event) }
	document.onmousemove = this.div_move.bind(this);
}

//Mail 및 SMS 폼설정
RANKUP_POPUP.prototype.getForm = function(mode, no) {
	var classObj = this;
	classObj.template.innerHTML = '<div id="ajax_loading"><img src="/images/loading_big.gif" alt="로딩중"></div>';
	new Ajax.Request(domain+"rankup_module/rankup_popup/multiProcess.html?no="+no+"&mode="+mode, {
		method: 'get',
		onSuccess: function(transport) {
			if (!transport.responseText.match(null)) {
				//alert(transport.responseText);
				var resultData		= transport.responseXML.getElementsByTagName('resultData')[0].firstChild.nodeValue;
				if(resultData) classObj.template.innerHTML = resultData;
			}
		}
	});
}

RANKUP_POPUP.prototype.submitFrom = function(mode) {
	var classObj = this;
	if(!validate(Form.getElements('form_frame'))) return false;
	var form_datas = Form.serialize('form_frame');
	classObj.template.innerHTML = '<div id="ajax_loading"><img src="/images/loading_big.gif" alt="로딩중"></div>';
	var task = mode=="mail" ? '이메일' : '문자';
	new Ajax.Request(domain+"rankup_module/rankup_popup/multiProcess.html?"+form_datas, {
		method: 'get',
		onSuccess: function(transport) {
			if (!transport.responseText.match(null)) {
				//alert(transport.responseText);
				var resultData = transport.responseXML.getElementsByTagName("resultData")[0];
				if(resultData.getAttribute("result").match("success")) {
					alerts(task +' 발송이 완료되었습니다.');
				}else{
					alerts(task +' 발송이 실패되었습니다. \r고객센타로 문의바랍니다.');
				}
				classObj.closeForm();
			}
		}
	});
}

RANKUP_POPUP.prototype.set_form = function(mode, no) {
	this.template = $('form_frame'); // 템플릿
	this.getForm(mode, no);
	var This = this;

	setTimeout(function() {
		This.template.style.display="block";
		with(document) {

			var margin = {
				top: Prototype.Browser.IE ? body.scrollTop : 0,
				left: Prototype.Browser.IE ? body.scrollLeft : 0
			};
			This.template.setStyle({
				top: margin.top + (body.offsetHeight/2) - (This.template.offsetHeight/2),
				left: margin.left + (body.offsetWidth/2) - (This.template.offsetWidth/2)
			});
		}
	}, 100);

	document.onmouseup= function(event) {This.div_move_check(0, event)};
	document.onmousemove = this.div_move.bind(this);
}

RANKUP_POPUP.prototype.closeForm = function() {
	this.template.hide();
}

RANKUP_POPUP.prototype.emailCheck = function(rec) {
	var object = $(rec);
    elements = object.previous();
	if (object.value == "N"){
		elements.value = "";
	}else{
		elements.value = object.value;
	}
}

//메시지 입력시 string() 길이 체크
RANKUP_POPUP.prototype.checkLen = function(obj) {
	var msgtext, msglen;

	msgtext = obj.value;
	msglen = $('msglen').value;

	var i=0,l=0;
	var temp,lastl;

	//길이를 구한다.
	while(i < msgtext.length){
		temp = msgtext.charAt(i);

		if (escape(temp).length > 4) l+=2;
		else if (temp!='\r') l++;
		if(l>80){
			alert("메시지란에 허용 길이 이상의 글을 쓰셨습니다.\n 메시지란에는 한글 40자, 영문80자까지만 쓰실 수 있습니다.");
			temp = obj.value.substr(0,i);
			obj.value = temp;
			l = lastl;
			break;
		}
		lastl = l;
		i++;
	}
	$('msglen').value=l;
}

RANKUP_POPUP.prototype.submitScription = function(datas) {
	//alert(datas);
	new Ajax.Request(domain+"rankup_module/rankup_request/multiProcess.html?"+datas, {
		method: 'get',
		onSuccess: function(transport) {
			if (!transport.responseText.match(null)) {
				//alert(transport.responseText);
				var resultData = transport.responseXML.getElementsByTagName("resultData")[0];
				alert(resultData.firstChild.nodeValue+SPACE);
				document.subScription.reset();
			}
		}
	});
}

RANKUP_POPUP.prototype.subScription = function(mode) {
	if($('mailing').checked == true || $('sms').checked == true){
		if($('mailing').checked == true && !isValidEmail($('email'))) return false;
		$('hphone').value = $('hphone1').value+"-"+$('hphone2').value+"-"+$('hphone3').value;
		if($('sms').checked==true && !isValidHPhone($('hphone')))  return false;
	}else{
		alerts('이메일 또는 핸드폰을 선택해 주세요.');
		return false;
	}
	var datas = Form.serialize('form_scription')+'&mode='+mode;
	this.submitScription(datas);
}

var rankup_popup = new RANKUP_POPUP;