//######################################################################
//# 프로그램명 : 랭크업 솔루션 팝업관리 프로그램
//# 버젼 : v1.0, r080528
//# 개발자 : C2tfiW ( Kurokisi )
//# 최종 업데이트 : 2008.05.28
//# 라이센스 : 솔루션구매고객이 아닌 경우 랭크업으로부터 허락을 받으셔야 합니다.
//######################################################################
// 팝업 클래스 정의
var POPUP = function() {
	this.registFrm = null;			// 등록/수정폼
	this.settingFrm = null;		// 팝업 설정폼
	this.popupBody = null;		// 팝업 바디 테이블
	this.popupItems = null;		// 팝업 아이템 테이블
	this.selClass = "selClass";	// 선택한 팝업의 스타일
	this.selObject = null;			// 선택한 팝업객체
	this.selRow = null;			// 선택한 팝업의 줄 번호
	this.selNo = null;				// 선택한 팝업의 인덱스 번호
	this.selBind = null;			// 선택한 팝업의 바인드 번호
	this.maxBind = 100;			// 바인드 가능 갯수
	this.maxWidth = null;		// 팝업 가로사이즈 제한
	this.maxPrevWidth = 736;	// 미리보기 가로사이즈 제한( 100% 했을때의 사이즈 : 등록폼이 width 값에서 -24px)
	this.minPrevWidth = 200;	// 미리보기 가로사이즈 최소값
	this.previewPop = null;		// 미리보기 팝업 객체
}

// 팝업 선택
POPUP.prototype.select_item = function(arg) {
	var el = arg.target||arg.srcElement;
	var type = el.type;
	try {
		// 오브젝트 설정
		do { el = el.parentNode; } while(el.getAttribute('id')!="item");
		var click_no = el.parentNode.getElementsByTagName("input")[0].value; // 팝업 인덱스 번호
		var obj = el.parentNode.parentNode.parentNode;
	}
	catch(e) {
		// 주어진 영역 밖을 선택한 경우 리턴
		return false;
	}
	// 선택한 팝업의 체크박스 토글
	if(type!="checkbox") el.getElementsByTagName("input")[0].checked = !el.getElementsByTagName("input")[0].checked;
	if(el.className != classObj.selClass) {
		// 기존 선택된 값 해제
		if(classObj.selNo!=null) {
			var items = document.getElementsByName("item");
			for(var i=0; i<items.length; i++) {
				if(items[i].getElementsByTagName("input")[0].value==classObj.selNo) {
					items[i].className = '';
					break;
				}
			}
		}
		el.className = classObj.selClass; // 선택한 팝업 재설정
		classObj.selObject = el;
		classObj.selNo = el.getElementsByTagName('input')[0].value; // 인덱스 번호
		var nos = document.getElementsByName('chk_no[]');
		for(var row=0; row<nos.length; row++) if(nos[row].value==classObj.selNo) { classObj.selRow = row; break; }
	}
}

// 지정한 줄 OBJECT 가져오기 - 순위 변경시 사용
POPUP.prototype.get_object = function(target) {
	var body_row = null;
	var target_obj = null;
	var target_bind = '';
	var target_row = 0;
	// 팝업 리스트 중에서 몇번째 테이블의 몇번째 아이템인지 체크
	for(var item=0; item<this.popupItems.length; item++) {
		var trs = this.popupItems[item].rows;
		for(var row=0; row<trs.length; row++) {
			if(target==target_row) {
				target_obj = trs[row].parentNode;
				target_row = row;
				//target_bind = this.popupItems[item].firstChild.getElementsByTagName('input')[1].value;
				break;
			}
			target_row++;
		}
		if(target_obj!=null) break;
	}
	// 전체 팝업바디 중에서 몇번째 ROW 에 있는지 체크
	if(target_obj!=null) {
		for(body_row=0; body_row<this.popupBody.rows.length; body_row++) {
			if(this.popupBody.rows[body_row].cells[0].innerHTML == target_obj.parentNode.parentNode.innerHTML) break;
		}
	}
	return {"obj":target_obj, "row":parseInt(target_row,0), "bind":target_bind, "body":parseInt(body_row,0)};
}

// 팝업 템플릿
POPUP.prototype.get_template = function(str) {
	var string = "\
	<table name='popupItem' id='popupItem' width='100%' cellpadding='0' cellspacing='0' border='0' bgcolor='white'>\
	<tbody>"+str+"\
	</tbody>\
	</table>";
	return string;
}

// 팝업영역 초기화
POPUP.prototype.reset_media_contents = function() {
	// 직접입력
	//var texts = document.getElementsByName('text');
	//for(var i=0; i<texts.length; i++) texts[i].innerHTML = '';
}

// 팝업 순위 바꾸기
POPUP.prototype.set_direction = function(el, mode) {
	setTimeout(function() {
		if(classObj.selRow===null) return;
		var select = classObj.get_object(classObj.selRow);
		var target = null;
		var newCell = null;
		var copyNode = null;
		var next_target = null;
		var prev_target = null;

		switch(mode) {
			//###############################################################
			//## 윗 방향로 이동시
			case "up": case "top":
			//###############################################################
				next_target = (mode=="up") ? parseInt(classObj.selRow)-1 : 0;
				if(next_target<0) next_target = 0;
				target = classObj.get_object(next_target);
				target['row'] = (target['obj'] != select['obj']) ? target['row']+1 : target['row'];

				if(classObj.selRow==0) {
					alert("최상위 항목입니다."+SPACE);
					document.body.focus();
					return false;
				}
				else {
					// 팝업영역 초기화
					classObj.reset_media_contents();
					var select_body = (select['bind']=='') ? select['body']-1 : select['body'];
					copyNode = select['obj'].rows[select['row']].cloneNode(true);
					select['obj'].deleteRow(select['row']);
					classObj.popupBody.insertRow(select_body).insertCell(0).innerHTML = classObj.get_template(copyNode.outerHTML);
					select['body'] += 1;
				}
				break;

			//###############################################################
			//## 아래 방향로 이동시
			case "down": case "bottom":
			//###############################################################
				var nos = document.getElementsByName('chk_no[]');
				next_target = (mode=="down") ? parseInt(classObj.selRow)+1 : nos.length-1;
				if(next_target>=nos.length-1) next_target = nos.length-1;
				target = classObj.get_object(next_target);

				if(classObj.selRow==nos.length-1) {
					alert("최하위 항목입니다."+SPACE);
					document.body.focus();
					return false;
				}
				else {
					// 팝업영역 초기화
					classObj.reset_media_contents();
					var target_body = (select['bind']=='') ? target['body']+1 : target['body']; 
					copyNode = select['obj'].rows[select['row']].cloneNode(true);
					select['obj'].deleteRow(select['row']);
					classObj.popupBody.insertRow(target_body).insertCell(0).innerHTML = classObj.get_template(copyNode.outerHTML);
				}
				break;
		}
		// 빈줄이 존재한다면 바디에서 삭제
		if(select['obj'].rows.length==0) classObj.popupBody.deleteRow(select['body']);
		classObj.rebuild_event(true); // 이벤트 재할당
	}, 0);
}

// 선택된 팝업 삭제 - 리스트 하단 부터 지움
POPUP.prototype.remove_item = function(datas) {
	for(var i=this.popupItems.length-1; i>=0; i--) {
		var trs = this.popupItems[i].rows;
		for(var j=trs.length-1; j>=0; j--) {
			if(in_array(trs[j].firstChild.getElementsByTagName("input")[0].value, datas)==false) continue;
			this.popupItems[i].deleteRow(j);
			// 빈줄 제거
			if(this.popupItems[i].rows.length>0) continue;
			for(var body_row=0; body_row<this.popupBody.rows.length; body_row++) {
				if(this.popupBody.rows[body_row].cells[0].innerHTML != this.popupItems[i].parentNode.innerHTML) continue;
				this.popupBody.deleteRow(body_row);
				break;
			}
		}
	}
}

// 등록 및 수정시 리스트 블럭킹
POPUP.prototype.screen_blind = function(mode) {
	if(mode===true) {
		var _height = parseInt(document.body.clientHeight,10);
		if(parseInt(document.body.scrollHeight,10)>_height) _height = parseInt(document.body.scrollHeight,10);
		$('screenBlindDiv').style.height = _height + "px";
		$('screenBlindDiv').style.marginTop = "-" + parseInt(document.body.scrollTop,10) + "px";
		this.change_display("screenBlindDiv", true);

		// SELECT 객체 숨기기
		this.sb_selects = document.getElementsByTagName("select");
		for(var i=0; i<this.sb_selects.length; i++) {
			if(in_array(this.sb_selects[i].name, Array("width_type", "height_type", "period_shour", "period_sminute", "period_ehour", "period_eminute"))) continue;
			this.sb_selects[i].style.visibility = "hidden";
		}
	}
	else {
		this.change_display("screenBlindDiv", false);
		for(var i=0; i<this.sb_selects.length; i++) this.sb_selects[i].style.visibility = "visible";
	}
}

// 팝업 레이어 토글
POPUP.prototype.change_display = function(el, val) {
	if(val===true||val===false) {
		var obj = $(el);
		obj.style.display = val ? "block" : "none";
		if(val===false) {
			if(el=="registFrmDiv") this.screen_blind(false);
			return false;
		}
		if(el=="registFrmDiv") this.screen_blind(true);
		// 페이지 중앙에 띄우기
		obj.style.top = (obj.offsetHeight>document.body.clientHeight) ? document.body.scrollTop : document.body.scrollTop+(document.body.clientHeight-obj.offsetHeight)/2 + "px";
		obj.style.left = (obj.offsetWidth>document.body.clientWidth) ? document.body.scrollLeft : document.body.scrollLeft+(document.body.clientWidth-obj.offsetWidth)/2 + "px";
	}
	else {
		var _val = $(el).style.display;
		$(el).style.display = (_val=="none") ? "block" : "none";
		this.screen_blind(false);
	}
}

// 팝업종류 별 폼 변경
POPUP.prototype.change_form = function(el) {
	var form = this.registFrm;
	// 직접입력 방식
	if(el.value =="text") {
		form.width.readOnly = form.height.readOnly = form.width_type.disabled = form.height_type.disabled = false;
		form.width.className = form.height.className = "enable";
		document.getElementById('iframecontent').contentWindow.document.body.innerHTML = '';
	}
	// 스킨입력 방식
	else {
		form.width_type.value = form.height_type.value = "exact";
		classObj.change_size_type(form.width_type);
		classObj.change_size_type(form.height_type);
		form.width.readOnly = form.height.readOnly = form.width_type.disabled = form.height_type.disabled = true;
		form.width.className = form.height.className = "disable";
		document.getElementById('iframecontent').contentWindow.document.body.innerHTML = $(el.value).innerHTML;
	}
}

// 팝업 등록/수정 - 수정시 리스트로 부터 설정
POPUP.prototype.regist_item = function(modify) {
	var form = this.registFrm;
	form.reset();

	// 에디터창 값 설정
	document.getElementById('iframecontent').contentWindow.document.body.innerHTML = '';
	if(modify===true) {
		form.no.value = this.selNo;
		form.mode.value = "update";
		// 초기값 등록폼에 세팅
		var spans = this.selObject.getElementsByTagName("span");
		var key = {'width':0, 'height':1, 'type':2, 'use_date':3, 'sdate':4, 'edate':5, 'media':6, 'content':6};

		form.title.value = this.selObject.getElementsByTagName("label")[0].innerHTML;

		// 공통값 설정
		form.width_type.value = spans[key['width']].getAttribute('type');
		form.height_type.value = spans[key['height']].getAttribute('type');
		if(!form.width_type.value) form.width_type.value = "exact";
		if(!form.height_type.value) form.height_type.value = "exact";
		this.change_size_type(form.width_type);
		this.change_size_type(form.height_type);
		form.width.value = parseInt(spans[key['width']].innerHTML, 10);
		form.height.value = parseInt(spans[key['height']].innerHTML, 10);

		// 팝업종류 설정
		for(var i=0; i<form.popup_type.length; i++) {
			if(form.popup_type[i].value != spans[key['type']].type) continue;
			form.popup_type[i].checked = true;
			break;
		}

		// 기한 설정
		for(var i=0; i<form.period.length; i++) {
			if(form.period[i].value == spans[key['use_date']].value) {
				form.period[i].checked = true;
				this.change_period_set(form.period[i]);
				// 기간설정값 입력
				if(spans[key['sdate']].innerHTML) {
					var sdate = spans[key['sdate']].innerHTML.replace(/\./g, '-').replace(/부터/g, '').split(' ');
					form.period_sdate.value = sdate[0];
					form.period_shour.value = new String(sdate[1]).replace(/시/g, '');
					form.period_sminute.value = sdate[2];
				}
				if(spans[key['edate']].innerHTML) {
					var edate = spans[key['edate']].innerHTML.replace(/\./g, '-').replace(/까지/g, '').split(' ');
					form.period_edate.value = edate[0];
					form.period_ehour.value = new String(edate[1]).replace(/시/g, '');
					form.period_eminute.value = edate[2];
				}
			}
		}

		// 등록된 팝업 정보 입력
		// 폼 전 환
		this.change_form(form.popup_type[0].checked ? form.popup_type[0] : form.popup_type[1]);
		var content = new String();
		var contentObj = this.selObject.getElementsByTagName("span");
		if(contentObj[contentObj.length-1].id=="text_data") {
			content = contentObj[contentObj.length-1].innerHTML.replace(/{:_lt:}/g, "<").replace(/{:_gt:}/g, ">");
		}
		// 등록/수정 폼 보이기
		this.change_display('registFrmDiv', true);
		// <SCRIPT 문장이 포함된 내용일 경우 텍스트 모드 활성화
		if(content.toUpperCase().indexOf("<SCRIPT")!=-1) {
			Wysiwyg.viewSource('iframecontent', 'content', 0); // 에디터 편집모드 변경 - TEXT 에디터
			form.content.value = document.all ? content.replace(/\r\n<SCRIPT/gi, "<SCRIPT") : content.replace(/\n<SCRIPT/gi, "<SCRIPT"); // textarea 값 설정
		}
		else {
			Wysiwyg.viewEdiror('iframecontent', 'content', 0); // 에디터 편집모드 변경 - HTML 에디터
			document.getElementById('iframecontent').contentWindow.document.body.innerHTML = content; // 에디터창 값 설정
		}
	}
	// 신규 등록시 폼초기화
	else {
		form.no.value = '';
		form.mode.value = "insert";
		form.content.value = '';
		form.width_type.value = form.height_type.value = "exact";
	
		this.change_size_type(form.width_type);
		this.change_size_type(form.height_type);
		this.change_form(form.popup_type[0]);
		this.change_period_set(form.period[0]);

		// 등록/수정 폼 보이기
		this.change_display('registFrmDiv', true);
	}
}

// 팝업 등록/수정시 반영
POPUP.prototype.append_popup_item = function(mode, popup_item) { // mode { insert | update }
	switch(mode) {
		// 등록 후 리스트 추가 작업
		case "insert":
			// 선택된 팝업이 없을때 - 맨처음에 추가
			if(this.selNo!=null) this.selObject.className = ''; // 기존선택된 팝업 초기화
			this.popupBody.insertRow(0).insertCell(0).innerHTML = this.get_template("<tr><td>"+popup_item['item']+"</td></tr>");
			/*
			if(this.selNo==null) this.popupBody.insertRow(0).insertCell(0).innerHTML = this.get_template("<tr><td>"+popup_item['item']+"</td></tr>");
			else {
				// 선택된 팝업이 있을때 - 선택된 팝업 아래에 추가
				this.selObject.className = ''; // 기존선택된 팝업 초기화
				if(this.selBind=='' || this.selBind==null) {
					var target = this.get_object(this.selRow+1);
					if(isNaN(target['body'])) target['body'] = this.popupBody.rows.length;
					this.popupBody.insertRow(target['body']).insertCell(0).innerHTML = this.get_template("<tr><td>"+popup_item['item']+"</td></tr>");
				}
				else {
					var target = this.get_object(this.selRow);
					target['obj'].insertRow(target['row']+1).insertCell(0).innerHTML = popup_item['item'].replace(/value=''/g, "value="+this.selBind); // 바인드 번호 추가
				}
			}
			*/
			this.selNo = popup_item['no'];
			this.selObject = this.popupBody.rows[0].cells[0].getElementsByTagName("table")[1];
			break;

		// 수정 후 리스트 갱신 작업
		case "update":
			this.selObject.parentNode.innerHTML = popup_item['item'];
			break;
	}
	this.rebuild_event();
	this.selObject.className = this.selClass;
	this.change_display('registFrmDiv', false); // 등록/수정폼 닫기
}

// 팝업 미리보기 - 팝업윈도우
POPUP.prototype.preview_popup = function(regist_mode) { // mode { true  or  false }
	setTimeout(function() {
		var obj = null;
		var width, height;
		var pop_width_add = 36; // 가로 추가(스크롤바)
		var pop_height_add = 157; // 세로 추가(타이틀바/하단바)
		var popup_title = new String();
		var popup_content = new String();

		// 팝업 등록 폼일 경우
		if(regist_mode==true) {
			var form = classObj.registFrm;
			var size_types = {"pixel": "px", "exact": "%"};
			width = form.width.value+size_types[form.width_type.value];
			height = form.height.value+size_types[form.width_type.value];
			// 줄바꿈 관련 태그 정리 - 보류!
			// Wysiwyg.nl2br_remove_content('iframecontent', 'content');
			obj = document.getElementsByName('iframecontent')[0].contentWindow.document.body;
			popup_title = form.title.value;
			popup_content = obj.innerHTML;
			if(!popup_content) {
				popup_content = form.content.innerHTML;
				obj = form.content;
			}
			popup_content = popup_content.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&nbsp;/g, '&nbsp;');
		}
		// 리스트일 경우
		else {
			var popup_infos = classObj.selObject.getElementsByTagName("span");
			obj = popup_infos[popup_infos.length-1];
			width = obj.getAttribute('width');
			height = obj.getAttribute('height');
			for(var i=0; i<popup_infos.length; i++) {
				if(popup_infos[i].id!="text") continue;
				obj = popup_infos[i];
				break;
			}
			popup_title = obj.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.getElementsByTagName("label")[0].innerHTML;
			if(!popup_title) pop_height_add -= 16;
			popup_content = obj.innerHTML;
		}

		// 자동값 모드일 경우 기본값 할당
		if(width==0 || new String(width).indexOf('%')!=-1) width = obj.offsetWidth;
		if(height==0 || new String(height).indexOf('%')!=-1) height = obj.offsetHeight;

		// 최소사이즈 보장
		if(width<classObj.minPrevWidth) width = classObj.minPrevWidth;

		// 미리보기 템플릿(상단)
		var preview_top = "<html>\
		<head>\
		<title>팝업미리보기</title>\
		<link rel='stylesheet' type='text/css' href='"+domain+"Libs/_style/rankup_style.css'>\
		</head>\
		<body>\
		<table width='100%' height='100%' cellpadding='0' cellspacing='0' border='0' style='table-layout:fixed;'>\
		<tr height='50' bgcolor='#333399'>\
			<td background='"+domain+"Libs/_images/bar_bg.gif' align='left'>\
				<table width='100%' height='100%' cellspacing='0' cellpadding='0'>\
				<tr><td style=\"background:url('./img/bar_pop_preview.gif') no-repeat;\"></td></tr>\
				</table>\
			</td>\
		</tr>\
		<tr><td height='7'></td></tr>\
		<tr height='100%' valign='top'>\
			<td style='padding:4px'>\
				<table border='0' cellpadding='1' cellspacing='1' bgcolor='#cacaca'>\
				<tr>\
					<td bgcolor='ffffff'>\
						<table width='100%' border='0' cellpadding='3' cellspacing='0' bgcolor='#f1f1f1'>\
						<tr>\
							<td nowrap style='font-weight:bolder;color:black;padding:5px 2px 0px 4px;'>"+popup_title+"</td>\
						</tr>\
						<tr>\
							<td>\
								<table width='"+width+"' height='"+height+"' cellpadding='0' cellspacing='0'>\
								<tr valign='top'>\
									<td>";

		// 미리보기 템플릿(하단)
		var preview_bottom = "</td>\
								</tr>\
								</table>\
							</td>\
						</tr>\
						<tr>\
							<td align='right' background='"+domain+"rankup_module/rankup_popup/img/dp_background.gif' nowrap>\
								<table border='0' cellspacing='0' cellpadding='0'>\
								<tr>\
									<td nowrap><input type='checkbox' disabled style='cursor:pointer;'></td>\
									<td nowrap><label disabled style='padding:2 8 0 0px;height:14px;font-size:9pt;cursor:pointer'>오늘하루 그만보기</label></td>\
									<td nowrap><img style='cursor:pointer' src='"+domain+"rankup_module/rankup_popup/img/dp_bclose.gif' border='0'></td>\
								</tr>\
								</table>\
							</td>\
						</tr>\
						</table>\
					</td>\
				</tr>\
				</table>\
			</td>\
		</tr>\
		<tr><td height='5'></td></tr>\
		<tr height='30' bgcolor='#e5e5e5' align='right'>\
			<td style='color:white' style='padding-right:7px'><a style='cursor:pointer' onClick='self.close()'><img src='../../Libs/_images/btn_close.gif' border='0' alt='닫기'></a></td>\
		</tr>\
		</table>\
		</body>\
		</html>";

		// 미리보기 창 오픈
		if(classObj.previewPop!=null) classObj.previewPop.close();
		classObj.previewPop = window.open("about:blank", "preivew_pop", "width="+(parseInt(width,10)+pop_width_add)+"px,height="+(parseInt(height,10)+pop_height_add)+"px, scrollbars=yes");

		// 미리보기 창에 내용 입력
		if(popup_content.toLowerCase().indexOf("<script")==-1) { // 단순한 HTML 문자열인 경우
			classObj.previewPop.document.write(preview_top + popup_content + preview_bottom);
		}
		else { // JavaScript 코드를 포함한 문자열인 경우
			classObj.previewPop.document.write(popup_content);
			setTimeout(function() { // JavaScript 소스를 미리보기할 경우에 대비
				var preview_content = classObj.previewPop.document.body.innerHTML;
				classObj.previewPop.document.body.innerHTML = '';
				classObj.previewPop.document.write(preview_top + preview_content + preview_bottom);
			}, 0);
		}
		classObj.previewPop.focus();

	}, 0);
}

// 팝업 사용기간 설정 변경
POPUP.prototype.change_period_set = function(el) {
	$('period_field').style.display = (el.value=="yes") ? "block" : "none";
	$('period_tip_field').style.display = (el.value=="yes") ? "none" : "block";
}

// 전체 팝업 선택/해제
POPUP.prototype.checkAll = function(val) {
	var nos = document.getElementsByName("chk_no[]");
	for(var i=0, j=0; i<nos.length; i++) {
		var item = nos[i];
		if(item.disabled==true) continue;
		item.checked = (val=="cross") ? !item.checked : val;
	}
}

// 선택된 팝업 인덱스 값 가져오기
POPUP.prototype.get_checkAll = function() {
	var items = new Array();
	var nos = document.getElementsByName("chk_no[]");
	for(var i=0, j=0; i<nos.length; i++) { 
		var item = nos[i];
		if(item.checked==true) items.push(item.value);
	}
	return items.join("__");
}

// 팝업크기 단위 변경
POPUP.prototype.change_size_type = function(el) {
	var obj = $(el.name.replace(/_type/gi, ''));
	obj.style.display = in_array(el.value, new Array("exact","auto")) ? "none" : "inline";
	switch(el.value) {
		case "pixel":
			if(obj.value=='') {
				//var info = $('mediaInfo').innerHTML.split(',');
				//info = info[0].split(' × ');
				//obj.value = (obj.name=="width") ? info[0]>this.maxPrevWidth ? this.maxPrevWidth : info[0].replace(/--/g, 100) : info[1].replace(/--/g, 100);
			}
			break;
		case "percent":
			if(obj.value=='' || obj.value>100) obj.value = 100;
			break;
	}
	this.set_resize();
}

// 팝업크기 설정값 추출
POPUP.prototype.get_media_size = function(info) {
	var form = this.registFrm;
	var width, height;
	if(info==undefined) {
		var info = {};
		var _info = $('mediaInfo').innerHTML.split(',');
		_info = _info[0].split(' × ');
		info['width'] = parseInt(_info[0], 10); // px, % 제거
		info['height'] = parseInt(_info[1], 10); // px, % 제거
	}
	// 가로 크기
	switch(form.width_type.value) {
		case "pixel":
			width = form.width.value;
			if(this.maxWidth!=null && form.width.value>this.maxWidth) {
				alert("제한 사이즈("+this.maxWidth+"pixel)를 초과하여 입력할 수 없습니다."+SPACE);
				width = form.width.value = this.maxWidth; // 가로크기 제한
			}
			if(width>this.maxPrevWidth) width = this.maxPrevWidth; // 미리보기 영역 제한 사이즈 초과시
			width = width ? width+"px" : "0px";
			break;
		case "percent":
			if(form.width.value>100) {
				alert("제한 사이즈(100%)를 초과하여 입력할 수 없습니다."+SPACE);
				form.width.value = 100; // 가로크기 제한
			}
			width = form.width.value ? form.width.value+"%" : "0%";
			break;
		case "exact":
			if(this.maxWidth!=null && info['width']>this.maxWidth) info['width'] = this.maxWidth; // 가로크기 제한
			form.width.value = info['width'];
			width = info['width']>this.maxPrevWidth ? this.maxPrevWidth+"px" : info['width']+"px"; // 미리보기 영역 제한 사이즈 초과시
			break;
		case "auto": width = ''; break;
	}
	// 세로 크기
	switch(form.height_type.value) {
		case "pixel": height = form.height.value ? form.height.value+"px" : "0px"; break;
		case "percent": 
			if(form.height.value>100) {
				alert("제한 사이즈(100%)를 초과하여 입력할 수 없습니다."+SPACE);
				form.height.value = 100; //세로크기 제한
			}
			height = form.height.value ? form.height.value+"%" : "0%"; 
			break;
		case "exact":
			height = info['height']+"px";
			form.height.value = info['height'];
			break;
		case "auto": height = ''; break;
	}
	return {'width':width, 'height':height};
}

// 팝업 사이즈 재설정
POPUP.prototype.set_resize = function() {
	if(this.registFrm.popup_type[0].checked===false) {
		document.body.focus();
		return false;
	}
}

// 변동사항 저장 - 순위 정보
POPUP.prototype.save_settings = function(silent) {
	if(silent!==true && !confirm("변경사항을 저장하시겠습니까?"+SPACE)) {
		document.body.focus();
		return false;
	}
	var form = this.settingFrm;
	var mode = form.mode.value;
	form.mode.value = "save_settings";
	form.submit();
	form.mode.value = mode;
}

// 선택된 팝업 다중 처리(사용/미사용, 새창/본창, 삭제) - Ajax 처리
POPUP.prototype.ajax_process = function(mode, multi, val) {
	setTimeout(function() {
		var modeValue = val; // setTimeout의 특성으로 인한 val 값의 변형을 막기위한 처리
		// 선행 처리
		switch(mode) {
			case "modify": // 수정시
				classObj.regist_item(true);
				return true;
				break;
			case "view": // 사용값 변경시
				var data = (multi===true) ? classObj.get_checkAll() : classObj.selNo;
				if(data.length<1) {
					alert("사용여부를 변경할 팝업을 선택하여 주십시오."+SPACE);
					document.body.focus();
					return false;
				}
				if(modeValue==undefined) {
					var cross_views = {'unused': "yes", 'use': "no"};
					var modeValue = cross_views[classObj.selObject.getElementsByTagName("img")[2].value];
				}
				var url = "./multiProcess.html?mode=view&data="+data+"&val="+modeValue;
				break;
			case "delete": // 삭제시
				var data = (multi===true) ? classObj.get_checkAll() : classObj.selNo;
				if(data.length<1) {
					alert("삭제하실 팝업을 선택하여 주십시오."+SPACE);
					document.body.focus();
					return false;
				}
				var url = "./multiProcess.html?mode=delete&data="+data;
				break;
			default:
				document.body.focus();
				return false;
		}
		// 질의
		var modes = {'view':"의 사용여부를 변경", 'delete':"을 삭제"};
		if(!confirm("선택하신 팝업"+modes[mode]+" 하시겠습니까?"+SPACE)) {
			document.body.focus();
			return false;
		}
		// Ajax 처리
		var myRequest = new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				if(!transport.responseText.match(null)) {
					var resultData = transport.responseXML.getElementsByTagName("resultData")[0];
					if(resultData.getAttribute("result").match("success")) {
						var nos = (new String(data).indexOf("__")!=-1) ? data.split('__') : [data];
						switch(mode) {
							case "view":
								var views = {'yes': "use", 'no': "unused"};
								var chk_nos = document.getElementsByName("chk_no[]");
								for(var i=0; i<chk_nos.length; i++) {
									if(in_array(chk_nos[i].value, nos)==false) continue;
									var c_img = chk_nos[i].parentNode.parentNode.getElementsByTagName("img")[2]
									c_img.src = "./img/bt_"+views[modeValue]+".gif";
									c_img.value = views[modeValue];
								}
								break;
							// 삭제된 아이템 리스트에 반영
							case "delete":
								classObj.remove_item(nos);
								break;
						}
					}
					alert(resultData.firstChild.nodeValue+SPACE); // 결과 출력
				}
				myRequest = null;
				document.body.focus();
			}
		});

	}, 0);
}

// 팝업 이벤트 할당 - 등록/삭제/순위 변동/바인드 관련
POPUP.prototype.rebuild_event = function(reset) {

	// 현재 선택된 줄 재 설정
	if(reset===true) {
		var nos = document.getElementsByName('chk_no[]');
		for(var row=0; row<nos.length; row++) {
			if(this.selNo==nos[row].value) { 
				this.selObject = nos[row].parentNode.parentNode.parentNode.parentNode;
				this.selRow = row; break; 
			}
		}
		this.save_settings(true);
	}
	else {
		// 직접입력 방식 팝업 처리
		var text_datas = document.getElementsByName('text_data');
		for(var i=0; i<text_datas.length; i++) {
			var xcontent = text_datas[i].innerHTML.replace(/{:_lt:}/g, "<").replace(/{:_gt:}/g, ">");
			try {
				var iframe = text_datas[i].parentNode.getElementsByTagName('iframe')[0]
				var xwindow = iframe.contentWindow.document.write(xcontent);
				iframe.parentNode.getElementsByTagName('span')[0].innerHTML = iframe.contentWindow.document.body.innerHTML;
				iframe.contentWindow.document.body.innerHTML = '';
			}
			catch(e) {
				//alert(e.message);
			}
		}
	}

	// 이벤트 할당
	var items = document.getElementsByName('item');
	for(var i=0; i<items.length; i++) {
		Event.stopObserving(items[i], 'click', this.select_item); // 이벤트 제거
		Event.observe(items[i], 'click', this.select_item); // 이벤트 할당
	}
	document.body.focus();
}