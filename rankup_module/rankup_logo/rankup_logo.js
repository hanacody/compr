//######################################################################
//# 프로그램명 : 랭크업 솔루션 로고관리 프로그램(배너관리 프로그램의 최소화버젼)
//# 버젼 : v1.1, r101220
//# 개발자 : C2tfiW ( Kurokisi )
//# 최종 업데이트 : 2010.12.20
//# 라이센스 : 솔루션구매고객이 아닌 경우 랭크업으로부터 허락을 받으셔야 합니다.
//######################################################################
// 로고 클래스 정의
var LOGO = function() {
	this.registFrm = null;			// 등록/수정폼
	this.settingFrm = null;		// 로고 설정폼
	this.bannerBody = null;		// 로고 바디 테이블
	this.bannerItems = null;		// 로고 아이템 테이블
	this.selClass = "selClass";	// 선택한 로고의 스타일
	this.selObject = null;			// 선택한 로고객체
	this.selRow = null;			// 선택한 로고의 줄 번호
	this.selNo = null;				// 선택한 로고의 인덱스 번호
	this.selBind = null;			// 선택한 로고의 바인드 번호
	this.maxBind = 100;			// 바인드 가능 갯수
	this.maxWidth = null;		// 로고 가로사이즈 제한
	this.maxPrevWidth = 736;	// 미리보기 가로사이즈 제한( 100% 했을때의 사이즈 : 등록폼이 width 값에서 -24px)
	this.previewPop = null;		// 미리보기 팝업 객체
}

// 로고 선택
LOGO.prototype.select_item = function(arg) {
	var el = arg.target||arg.srcElement;
	var type = el.type;
	try {
		// 오브젝트 설정
		do { el = el.parentNode; } while(el.getAttribute('id')!="item");
		var click_no = el.parentNode.getElementsByTagName("input")[0].value; // 로고 인덱스 번호
		var obj = el.parentNode.parentNode.parentNode;
	}
	catch(e) {
		// 주어진 영역 밖을 선택한 경우 리턴
		return false;
	}
	// 선택한 로고의 체크박스 토글
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
		el.className = classObj.selClass; // 선택한 로고 재설정
		classObj.selObject = el;
		classObj.selNo = el.getElementsByTagName('input')[0].value; // 인덱스 번호
		classObj.selBind = el.getElementsByTagName('input')[1].value; // 바인드 번호

		var nos = document.getElementsByName('chk_no[]');
		for(var row=0; row<nos.length; row++) if(nos[row].value==classObj.selNo) { classObj.selRow = row; break; }
	}
}

// 지정한 줄 OBJECT 가져오기 - 순위 변경시 사용
LOGO.prototype.get_object = function(target) {
	var body_row = null;
	var target_obj = null;
	var target_bind = '';
	var target_row = 0;
	// 로고 리스트 중에서 몇번째 테이블의 몇번째 아이템인지 체크
	for(var item=0; item<this.bannerItems.length; item++) {
		var trs = this.bannerItems[item].rows;
		for(var row=0; row<trs.length; row++) {
			if(target==target_row) {
				target_obj = trs[row].parentNode;
				target_row = row;
				target_bind = this.bannerItems[item].firstChild.getElementsByTagName('input')[1].value;
				break;
			}
			target_row++;
		}
		if(target_obj!=null) break;
	}
	// 전체 로고바디 중에서 몇번째 ROW 에 있는지 체크
	if(target_obj!=null) {
		for(body_row=0; body_row<this.bannerBody.rows.length; body_row++) {
			if(this.bannerBody.rows[body_row].cells[0].innerHTML == target_obj.parentNode.parentNode.innerHTML) break;
		}
	}
	return {"obj":target_obj, "row":parseInt(target_row,0), "bind":target_bind, "body":parseInt(body_row,0)};
}

// 로고 템플릿
LOGO.prototype.get_template = function(str) {
	var string = "\
	<span id='bindToolBox'></span>\
	<table name='bannerItem' id='bannerItem' width='100%' cellpadding='0' cellspacing='0' border='0' bgcolor='white'>\
	<tbody>"+str+"\
	</tbody>\
	</table>";
	return string;
}

// 로고영역 초기화
LOGO.prototype.reset_media_contents = function() {
	// 미디어 - 플래시
	//var media_datas = document.getElementsByName('media_data');
	//for(var i=0; i<media_datas.length; i++) media_datas[i].parentNode.getElementsByTagName('span')[0].innerHTML = '';
	// 직접입력
	var texts = document.getElementsByName('text');
	for(var i=0; i<texts.length; i++) texts[i].innerHTML = '';
}

// 선택된 로고 삭제 - 리스트 하단 부터 지움
LOGO.prototype.remove_item = function(datas) {
	for(var i=this.bannerItems.length-1; i>=0; i--) {
		var trs = this.bannerItems[i].rows;
		for(var j=trs.length-1; j>=0; j--) {
			if(in_array(trs[j].firstChild.getElementsByTagName("input")[0].value, datas)==false) continue;
			this.bannerItems[i].deleteRow(j);
			// 빈줄 제거
			if(this.bannerItems[i].rows.length>0) continue;
			for(var body_row=0; body_row<this.bannerBody.rows.length; body_row++) {
				if(this.bannerBody.rows[body_row].cells[0].innerHTML != this.bannerItems[i].parentNode.innerHTML) continue;
				this.bannerBody.deleteRow(body_row);
				break;
			}
		}
	}
	// 바인드정보가 남아 있는 경우 처리
	for(var i=0; i<this.bannerItems.length; i++) {
		var trs = this.bannerItems[i].rows;
		if(trs.length>1) continue;
		for(var j=0; j<trs.length; j++) {
			if(trs[j].getElementsByTagName("input")[1].value=='') continue;
			trs[j].getElementsByTagName('input')[1].value = ''; // 바인드 번호 제거
			this.bannerItems[i].parentNode.parentNode.getElementsByTagName('span')[0].innerHTML = ''; // 도구 제거
		}
	}
}

// 등록 및 수정시 리스트 블럭킹
LOGO.prototype.screen_blind = function(mode) {
	if(mode===true) {
		var _height = parseInt(document.body.clientHeight,10);
		if(parseInt(document.body.scrollHeight,10)>_height) _height = parseInt(document.body.scrollHeight,10);
		$('screenBlindDiv').style.height = _height + "px";
		$('screenBlindDiv').style.marginTop = "-" + parseInt(document.body.scrollTop,10) + "px";
		this.change_display("screenBlindDiv", true);

		// SELECT 객체 숨기기
		this.sb_selects = document.getElementsByTagName("select");
		for(var i=0; i<this.sb_selects.length; i++) {
			if(in_array(this.sb_selects[i].name, Array("width_type", "height_type"))) continue;
			this.sb_selects[i].style.visibility = "hidden";
		}
	}
	else {
		this.change_display("screenBlindDiv", false);
		for(var i=0; i<this.sb_selects.length; i++) this.sb_selects[i].style.visibility = "visible";
	}
}

// 로고 레이어 토글
LOGO.prototype.change_display = function(el, val) {
	if(val===true||val===false) {
		var obj = $(el);
		obj.style.display = val ? "block" : "none";
		if(val===false) {
			if(el=="registFrmDiv") this.screen_blind(false);
			return false;
		}
		// 페이지 중앙에 띄우기
		obj.style.top = (obj.offsetHeight>document.body.clientHeight) ? document.body.scrollTop : document.body.scrollTop+(document.body.clientHeight-obj.offsetHeight)/2 + "px";
		obj.style.left = (obj.offsetWidth>document.body.clientWidth) ? document.body.scrollLeft : document.body.scrollLeft+(document.body.clientWidth-obj.offsetWidth)/2 + "px";
		if(el=="registFrmDiv") this.screen_blind(true);
	}
	else {
		var _val = $(el).style.display;
		$(el).style.display = (_val=="none") ? "block" : "none";
		this.screen_blind(false);
	}
}

// 로고종류 별 폼 변경
LOGO.prototype.change_form = function(el) {
	var form = this.registFrm;
	// 직접입력 방식 폼
	if(el.value =="text") {
		$('editorBox').style.display = "block";
		$('previewBox').style.display = "none";
		form.width.readOnly = form.height.readOnly = form.width_type.disabled = form.height_type.disabled = form.attached.readOnly = true;
		form.width.className = form.height.className = form.attached.className = "disable";
		form.address.removeAttribute("required");
		form.on_attached.removeAttribute("required");
		form.content.setAttribute("required", "required");
	}
	// 미디어등록 방식 폼
	else {
		$('editorBox').style.display = "none";
		$('previewBox').style.display = "block";
		form.width.readOnly = form.height.readOnly = form.width_type.disabled = form.height_type.disabled = form.attached.readOnly = false;
		form.width.className = form.height.className = form.attached.className = "enable";
		form.address.setAttribute("required", "required");
		form.on_attached.setAttribute("required", "required");
		form.content.removeAttribute("required");
	}
	// 예외사항 처리
	if(el.value=="text" || (el.value!="text" && $('previewItemBox').getElementsByTagName("object")[0]!=undefined)) {
		form.address.removeAttribute("required");
		form.on_attached.removeAttribute("required");
		form.address.readOnly = form.popup_banner.disabled = true;
		form.address.className = "disable";
	}
	else {
		form.address.setAttribute("required", "required");
		if(el.value!="text" && ($('previewItemBox').getElementsByTagName("object")[0]!=undefined || $('previewItemBox').getElementsByTagName("img")[0]!=undefined)) form.on_attached.removeAttribute("required");
		form.address.readOnly = form.popup_banner.disabled = false;
		form.address.className = "enable";
	}
}

// 로고 등록/수정 - 수정시 리스트로 부터 설정
LOGO.prototype.regist_item = function(modify) {
	var form = this.registFrm;
	this.set_media_outline(true);
	form.reset();

	// 에디터창 값 설정
	document.getElementById('iframecontent').contentWindow.document.body.innerHTML = '';
	$('mediaInfo').innerHTML = "-- × --, --";

	// 미리보기 창 초기화
	var itembox = $('previewItemBox');
	itembox.style.width = itembox.style.height = '';
	itembox.innerHTML = '';

	if(modify===true) {
		form.no.value = this.selNo;
		form.mode.value = "update";
		// 초기값 등록폼에 세팅
		form.bind.value = this.selObject.getElementsByTagName('input')[1].value; // 바인드
		var spans = this.selObject.getElementsByTagName("span");
		var key = {'width':0, 'height':1, 'type':2, 'use_date':3, 'sdate':4, 'edate':5, 'media':6, 'content':6};

		// 공통값 설정
		form.width_type.value = spans[key['width']].getAttribute('type');
		form.height_type.value = spans[key['height']].getAttribute('type');
		if(!form.width_type.value) form.width_type.value = "exact";
		if(!form.height_type.value) form.height_type.value = "exact";
		this.change_size_type(form.width_type);
		this.change_size_type(form.height_type);
		form.width.value = parseInt(spans[key['width']].innerHTML, 10);
		form.height.value = parseInt(spans[key['height']].innerHTML, 10);

		// 로고종류 설정
		for(var i=0; i<form.banner_type.length; i++) {
			if(form.banner_type[i].value != spans[key['type']].type) continue;
			form.banner_type[i].checked = true;
			break;
		}

		// 등록된 로고 정보 입력
		switch(spans[key['type']].type) {
			case "media":
				// 폼전환 - text 모드에서 media 모드로 전환시 패치
				setTimeout(function() {
					classObj.change_form(form.banner_type[0].checked ? form.banner_type[0] : form.banner_type[1]);
				}, 0);
				// 미디어 인포박스 설정
				$('mediaInfo').innerHTML = spans[key['media']].getAttribute("width")+" × "+spans[key['media']].getAttribute("height")+", "+spans[key['media']].getAttribute("extension");
				try {
					var size = this.get_media_size();
					if(size['width']) itembox.style.width = size['width'];
					if(size['height']) itembox.style.height = size['height'];
				}
				catch(e) {
					// alert(e.message);
				}
				if(spans[key['type']].value=="image") {
					form.address.value = spans[key['media']].getAttribute("address");
					form.popup_banner.checked = spans[key['media']].getAttribute("target")=="_blank" ? true : false;
					// 로고 이미지 설정
					var img = new Image();
					img.style.width = img.style.height = "100%";
					img.src = spans[key['media']].getElementsByTagName("img")[0].src;
					itembox.appendChild(img);
				}
				else { // flash 일경우 주소란 비활성화
					form.address.value = '';
					var movieObj = spans[key['media']].getElementsByTagName("object")[0];
					this.append_flash_object(itembox, movieObj.getAttribute("movie"), "100%", "100%");
				}
				// 등록/수정 폼 보이기
				this.change_display('registFrmDiv', true);
				// 2010.12.20 added
				var obj = $(classObj.selObject).select('span[id="media"]')[0];
				classObj.set_media_outline(true);
				if(obj.hasClassName('banner_outline')) {
					classObj.set_media_outline();
					this.registFrm.mediaOutlineChecker.checked = true;
				}
				break;

			case "text":
				// 폼 전 환
				this.change_form(form.banner_type[0].checked ? form.banner_type[0] : form.banner_type[1]);
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
				break;
		}
	}
	// 신규 등록시 폼초기화
	else {
		form.no.value = '';
		form.mode.value = "insert";
		form.content.value = '';
		itembox.innerHTML = "<i disabled style=\"padding:0px 4px 0px 4px;\">NULL</i>";

		this.change_size_type(form.width_type);
		this.change_size_type(form.height_type);
		this.change_form(form.banner_type[0]);

		// 등록/수정 폼 보이기
		this.change_display('registFrmDiv', true);
	}
}

// 로고 등록/수정시 반영
LOGO.prototype.append_banner_item = function(mode, banner_item) { // mode { insert | update }
	switch(mode) {
		// 등록 후 리스트 추가 작업
		case "insert":
			// 선택된 로고가 없을때 - 맨처음에 추가
			if(this.selNo==null) this.bannerBody.insertRow(0).insertCell(0).innerHTML = this.get_template("<tr><td>"+banner_item['item']+"</td></tr>");
			else {
				// 선택된 로고가 있을때 - 선택된 로고 아래에 추가
				this.selObject.className = ''; // 기존선택된 로고 초기화
				if(this.selBind=='' || this.selBind==null) {
					var target = this.get_object(this.selRow+1);
					if(isNaN(target['body'])) target['body'] = this.bannerBody.rows.length;
					this.bannerBody.insertRow(target['body']).insertCell(0).innerHTML = this.get_template("<tr><td>"+banner_item['item']+"</td></tr>");
				}
				else {
					var target = this.get_object(this.selRow);
					target['obj'].insertRow(target['row']+1).insertCell(0).innerHTML = banner_item['item'].replace(/value=''/g, "value="+this.selBind); // 바인드 번호 추가
				}
			}
			this.selNo = banner_item['no'];
			break;

		// 수정 후 리스트 갱신 작업
		case "update":
			this.selObject.parentNode.innerHTML = banner_item['item'];
			break;
	}
	this.rebuild_event(true);
	this.selObject.className = this.selClass;
	this.change_display('registFrmDiv', false); // 등록/수정폼 닫기
}

// 로고 미리보기 - 팝업윈도우
LOGO.prototype.preview_banner = function(regist_mode) { // mode { true  or  false }
	setTimeout(function() {
		var obj = null;
		var width, height, outline; // 2010.12.20 added
		var pop_width_add = 25; // 가로 추가(스크롤바)
		var pop_height_add = 100; // 세로 추가(타이틀바/하단바)
		var banner_content = new String();
		// 로고 등록 폼일 경우
		if(regist_mode==true) {
			var form = classObj.registFrm;
			var size_types = {"pixel": "px", "percent": "%", "exact": "px"};
			switch(form.banner_type[0].checked) {
				// 미디어(이미지/플래시)등록 방식
				case true:
					var info = $('mediaInfo').innerHTML.split(", "); // mediaInfo 에서 flash 인지 image 인지 체크
					if(info[1].toLowerCase()=="swf") {
						obj = {'id': "media_data"}; // 리스트 방식 매칭 코드
						width = form.width.value+size_types[form.width_type.value];
						height = form.height.value+size_types[form.height_type.value];
						outline = (form.mediaOutlineChecker.checked==true);
						banner_content = "<div id='content_item' style='width:"+width+";height:"+height+"'></div>";
						var movie = $('previewItemBox').getElementsByTagName("object")[0].getAttribute("movie");
					}
					else {
						obj = {'id': "media"}; // 리스트 방식 매칭 코드
						var img = $('previewItemBox').getElementsByTagName("img")[0];
						if(img==null) {
							alert("배너가 존재하지 않거나 미리보기할 수 없는 형태입니다."+SPACE);
							document.body.focus();
							return false;
						}
						width = form.width.value+size_types[form.width_type.value];
						height = form.height.value+size_types[form.height_type.value];
						outline = (form.mediaOutlineChecker.checked==true) ? " class='banner_outline'" : ''; // 2010.12.20 added
						banner_content = "<img src='"+img.src+"' width='"+width+"' height='"+height+"' border='0'"+outline+">";
						// 링크 추가
						var address = form.address.value.replace(/{:domain:}/gi, domain);
						if(address!=null && address) {
							var target = form.popup_banner.checked==true ? "_blank" : "_self";
							target = target!=null ? " target='"+target+"'" : '';
							banner_content = "<a href='"+address+"'"+target+" title='클릭 : "+address+" 사이트로 이동'>"+banner_content+"</a>";
						}
					}
					break;
				// 직접입력 방식
				case false:
					obj = {'id': "text"}; // 리스트 방식 매칭 코드
					width = classObj.maxWidth;
					height = 400;
					banner_content = document.getElementsByName('iframecontent')[0].contentWindow.document.body.innerHTML;
					if(!banner_content) banner_content = form.content.innerHTML;
					banner_content = banner_content.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&nbsp;/g, '&nbsp;');
					break;
			}
		}
		// 리스트일 경우
		else {
			var banner_infos = classObj.selObject.getElementsByTagName("span");
			obj = banner_infos[banner_infos.length-1];
			switch(obj.id) {
				case "media_data": // flash
					var info = obj.value.split('|');
					var movie = info[1];
					width = info[2];
					height = info[3];
					outline = banner_infos[2].getAttribute('outline'); // 2010.12.20 added
					banner_content = "<div id='content_item' style='width:"+width+";height:"+height+";'></div>";
					break;
				case "media": // image
					//var img = obj.getElementsByTagName("img")[0];
					width = banner_infos[0].innerHTML;
					height = banner_infos[1].innerHTML;
					outline = banner_infos[2].getAttribute('outline'); // 2010.12.20 added
					banner_content = outline.match(/on/i) ? obj.innerHTML.replace(/>/, " class='banner_outline'") : obj.innerHTML; // 2010.12.20 added
					// 링크추가
					var address = obj.getAttribute('address'); //.replace(/{:domain:}/gi, domain);
					if(address!=null && address) {
						var target = obj.getAttribute('target');
						target = target!=null ? " target='"+target+"'" : '';
						banner_content = "<a href='"+address+"'"+target+" title='클릭 : "+address+" 사이트로 이동'>"+banner_content+"</a>";
					}
					break;
				default: // 직접입력 방식
					for(var i=0; i<banner_infos.length; i++) {
						if(banner_infos[i].id!="text") continue;
						obj = banner_infos[i];
						break;
					}
					height = 400;
					width = classObj.maxWidth;
					banner_content = obj.innerHTML;
					break;
			}
		}
		// percent 모드일경우 기본값 할당
		if(new String(width).indexOf('%')!=-1) width = classObj.maxWidth;
		if(new String(height).indexOf('%')!=-1) height = 400;

		// A 태그 상의 {:domain:} 머지 - 2008.06.14 추가
		var atags = banner_content.match(/(<a [^<]*href=["|']?([^ "']*)["|']?[^>])/gi);
		if(atags!=null) for(var i=0; i<atags.length; i++) banner_content = banner_content.replace(atags[i], atags[i].replace(domain+"rankup_module/rankup_banner/{:domain:}", "{:domain:}").replace(/{:domain:}/g, domain));

		// 미리보기 템플릿(상단)
		var preview_top = "<html>\
		<head>\
		<title>로고미리보기</title>\
		<link rel='stylesheet' type='text/css' href='"+domain+"Libs/_style/rankup_style.css'>\
		</head>\
		<body>\
		<table width='100%' height='100%' cellpadding='0' cellspacing='0' border='0' style='table-layout:fixed;'>\
		<tr height='50' bgcolor='#333399'>\
			<td background='"+domain+"Libs/_images/bar_bg.gif' align='left'>\
				<table width='100%' height='100%' cellspacing='0' cellpadding='0'>\
				<tr><td style=\"background:url('./img/bar_banner_preview.gif') no-repeat;\"></td></tr>\
				</table>\
			</td>\
		</tr>\
		<tr><td height='7'></td></tr>\
		<tr height='100%' valign='top'>\
			<td style='padding:4px'>";

		// 미리보기 템플릿(하단)
		var preview_bottom = "</td>\
		</tr>\
		<tr><td height='5'></td></tr>\
		<tr height='30' bgcolor='#acacac' align='right'>\
			<td style='color:white' style='padding-right:7px'><a style='cursor:pointer' onClick='self.close()'><img src='./img/bt_close_s.gif' border='0'></a></td>\
		</tr>\
		</table>\
		</body>\
		</html>";

		// 미리보기 창 오픈
		if(classObj.previewPop!=null) classObj.previewPop.close();
		classObj.previewPop = window.open("about:blank", "preivew_pop", "width="+(parseInt(width,10)+pop_width_add)+"px,height="+(parseInt(height,10)+pop_height_add)+"px, scrollbars=yes");

		// 미리보기 창에 내용 입력
		switch(obj.id) {
			case "text":
				// 단순한 HTML 문자열인 경우
				if(banner_content.toLowerCase().indexOf("<script")==-1) {
					classObj.previewPop.document.write(preview_top + banner_content + preview_bottom);
				}
				else { // JavaScript 코드를 포함한 문자열인 경우
					classObj.previewPop.document.write(banner_content);
					setTimeout(function() { // JavaScript 소스를 미리보기할 경우에 대비
						var preview_content = classObj.previewPop.document.body.innerHTML;
						classObj.previewPop.document.body.innerHTML = '';
						classObj.previewPop.document.write(preview_top + preview_content + preview_bottom);
					}, 0);
				}
				break;
			default: // obj.id = { media_data | media }
				classObj.previewPop.document.write(preview_top+banner_content+preview_bottom);
				if(obj.id=="media_data") classObj.append_flash_object(classObj.previewPop.content_item, movie, "100%", "100%", outline);
				break;
		}
		classObj.previewPop.focus();

	}, 0);
}

// 전체 로고 선택/해제
LOGO.prototype.checkAll = function(val) {
	var nos = document.getElementsByName("chk_no[]");
	for(var i=0, j=0; i<nos.length; i++) {
		var item = nos[i];
		if(item.disabled==true) continue;
		item.checked = (val=="cross") ? !item.checked : val;
	}
}

// 선택된 로고 인덱스 값 가져오기
LOGO.prototype.get_checkAll = function() {
	var items = new Array();
	var nos = document.getElementsByName("chk_no[]");
	for(var i=0, j=0; i<nos.length; i++) {
		var item = nos[i];
		if(item.checked==true) items.push(item.value);
	}
	return items.join("__");
}

// 첨부파일 업로드 - 미리보기 구현
LOGO.prototype.post_attached = function(el) {
	if(el.getAttribute("filter")!=null) {
		var filters = el.getAttribute("filter").toLowerCase().split(",");
		var exts = el.value.toLowerCase().split(".");
		if(in_array(exts[exts.length-1], filters)==false) {
			alert("로고로 등록할 수 없는 미디어 파일형식입니다."+SPACE);
			document.body.focus();
			return false;
		}
	}
	var form = this.registFrm;
	var mode = form.mode.value;
	var encType = form.encoding;
	form.mode.value = "post_attached";
	form.encoding = "multipart/form-data"; // 인코딩 변경 - 파일첨부 가능
	form.submit();

	// 인코딩 복원 : application/x-www-form-urlencoded
	form.encoding = encType;
	form.mode.value = mode;
}

// 로고크기 단위 변경
LOGO.prototype.change_size_type = function(el) {
	var obj = $(el.name.replace(/_type/gi, ''));
	obj.style.display = in_array(el.value, new Array("exact","auto")) ? "none" : "inline";
	switch(el.value) {
		case "pixel":
			if(obj.value=='') {
				var info = $('mediaInfo').innerHTML.split(',');
				info = info[0].split(' × ');
				obj.value = (obj.name=="width") ? info[0]>this.maxPrevWidth ? this.maxPrevWidth : info[0].replace(/--/g, 100) : info[1].replace(/--/g, 100);
			}
			break;
		case "percent":
			if(obj.value=='' || obj.value>100) obj.value = 100;
			break;
	}
	this.set_resize();
}

// 로고크기 설정값 추출
LOGO.prototype.get_media_size = function(info) {
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

// 플래시 OBJECT 붙여 넣기
LOGO.prototype.append_flash_object = function(obj, m, w, h, o) { // 'outline' - 2010.12.20 added
	try {
		var outline= (o=='on') ? ' class="banner_outline"' : '';
		var item = "<OBJECT id='banner_item' classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codeBase='http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0' width='"+w+"' height='"+h+"'"+outline+" align='middle' border='0'> <param name='allowScriptAccess' value='always' /> <param name='allowFullScreen' value='true'> <param name='movie' value='"+m+"' /> <param name='quality' value='high' /> <param name='wmode' value='opaque' /> <param name='bgcolor' value='#000000' /> <embed id='banner_item' src="+m+" quality='high' bgcolor='#000000' width='"+w+"' height='"+h+"' align='middle' allowScriptAccess='always' border='0' allowFullScreen='true' quality='high' wmode='transparent' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer' /></OBJECT>";
		obj.update(item);
	}
	catch(e) {
		// alert(e.message);
		obj.innerHTML = item;
	}
}

// 첨부파일 미리보기
LOGO.prototype.preview_attached = function(file_name, info) {
	var form = this.registFrm;
	var url = domain+file_name; // domain : rankup_basic.class.php 에서 정의
	var itembox = $('previewItemBox');
	var size = this.get_media_size(info);
	if(size['width']) itembox.style.width = size['width'];
	if(size['height']) itembox.style.height = size['height'];

	form.address.readOnly = (info['type']=="swf") ? true : false;
	form.address.className = (info['type']=="swf") ? "disable" : "enable";
	form.popup_banner.disabled = (info['type']=="swf") ? true : false;

	switch(info['type']) {
		case "swf":
			this.append_flash_object(itembox, url, "100%", "100%");
			form.address.removeAttribute("required");
			form.address.value = '';
			break;

		default:
			itembox.innerHTML = '';
			var img = new Image();
			img.style.width = img.style.height = "100%";
			img.src = url;
			itembox.appendChild(img);
			if(form.address.getAttribute("DEFAULT")==form.address.value) {
				form.address.focus();
				form.address.value = form.address.getAttribute("DEFAULT");
			}
			form.address.setAttribute("required", "required");
	}
	// 인포박스 처리
	$('mediaInfo').innerHTML = info['text'];
}

// 로고 사이즈 재설정
LOGO.prototype.set_resize = function() {
	if(this.registFrm.banner_type[0].checked===false) {
		document.body.focus();
		return false;
	}
	var itembox = $('previewItemBox');
	var item = itembox.getElementsByTagName("img")[0]; // 이미지
	if(item==undefined) item = itembox.getElementsByTagName("object")[0]; // FLASH 오브젝트
	if(item==undefined) return false;

	var size = this.get_media_size();
	if(size['width']) itembox.style.width = size['width'];
	if(size['height']) itembox.style.height = size['height'];
}

// 로고 아웃라인 활성화 여부
LOGO.prototype.set_media_outline = function(reset) {
	var itembox = $('previewItemBox');
	if(reset==true) {
		this.registFrm.mediaOutlineChecker.checked = false;
		itembox.style.border = "#555555 1px solid";
	}
	itembox.style.border = in_array(itembox.style.border, new Array("", "#fdfdfd 1px solid")) ? "#555555 1px solid" : "#fdfdfd 1px solid";
}

// 리스트상의 로고에 아웃라인 활성화 토글
LOGO.prototype.set_banner_outline = function() {
	var media = document.getElementsByName('media');
	for(var i=0; i<media.length; i++) media[i].style.border = media[i].style.border=="" ? "#555555 1px solid" : "";
}

// 로고 맞춤사이즈 안내 토글
LOGO.prototype.open_size_guide = function() {
	var obj = $('bannerSizeGuideDiv');
	obj.style.display = obj.style.display=="block" ? "none": "block";
	if(obj.style.display=="block") obj.focus();
}

// 로고 맞춤사이즈 적용
LOGO.prototype.apply_media_size = function(width, height) {
	// 직접입력 모드에서는 적용안됨
	var form = this.registFrm;
	if(form.banner_type[1].checked===true) {
		alert("직접입력 모드에서는 사이즈를 설정하실 수 없습니다."+SPACE);
		document.body.focus();
		return false;
	}
	form.width.value = width;
	form.height.value = height;
	form.width_type.value = form.height_type.value = "pixel";
	if(!height) form.height_type.value = "exact";
	this.change_size_type(form.width_type, true);
	this.change_size_type(form.height_type, true);
}

// 로고 실제사이즈 설정
LOGO.prototype.set_real_size = function() {
	var form = this.registFrm;
	if(form.banner_type[1].checked===true) {
		alert("직접입력 모드에서는 사이즈를 설정하실 수 없습니다."+SPACE);
		document.body.focus();
		return false;
	}
	form.width_type.value = form.height_type.value = "exact";
	this.change_size_type(form.width_type);
	this.change_size_type(form.height_type);
}

// 변동사항 저장 - 순위 와 바인드 정보
LOGO.prototype.save_settings = function(silent) {
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

// 선택된 로고 다중 처리(사용/미사용, 새창/본창, 삭제) - Ajax 처리
LOGO.prototype.ajax_process = function(mode, multi, val) {
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
					alert("사용여부를 변경할 로고를 선택하여 주십시오."+SPACE);
					document.body.focus();
					return false;
				}
				if(modeValue==undefined) {
					var cross_views = {'unused': "yes", 'use': "no"};
					var modeValue = cross_views[classObj.selObject.getElementsByTagName("img")[0].value];
				}
				var url = "./multiProcess.html?mode=view&data="+data+"&val="+modeValue;
				break;
			case "delete": // 삭제시
				var data = (multi===true) ? classObj.get_checkAll() : classObj.selNo;
				if(data.length<1) {
					alert("삭제하실 로고를 선택하여 주십시오."+SPACE);
					document.body.focus();
					return false;
				}
				var url = "./multiProcess.html?mode=delete&data="+data;
				break;
			case "target": // 새창모드 변경시
				// 미디어방식 중 이미지일때만 창모드 설정 가능
				var data = classObj.selNo;
				if(modeValue==undefined) {
					var cross_targets = {'selfwin': "_blank", 'newwin': "_self"};
					var modeValue = cross_targets[classObj.selObject.getElementsByTagName("img")[1].value]
				}
				if(!modeValue) {
					alert("창모드를 변경할 수 없는 로고형태 입니다."+SPACE);
					document.body.focus();
					return false;
				}
				var url = "./multiProcess.html?mode=target&data="+data+"&val="+modeValue;
				break;
			case "outline": // 테두리표시 - 2010.12.20 added
				var data = classObj.selNo;
				var obj = $(classObj.selObject).select('span[id="media"]')[0];
				if(obj==null) {
					alert('직접입력으로 등록한 배너는 테두리표시를 할 수 없습니다.'+SPACE);
					return false;
				}
				var modeValue = obj.hasClassName('banner_outline') ? 'off' : 'on';
				var url = "./multiProcess.html?mode=outline&data="+data+"&val="+modeValue;
				break;
			default:
				document.body.focus();
				return false;
		}
		// 질의
		var modes = {'view':"의 사용여부를 변경", 'delete':"를 삭제", 'target':"의 새창모드를 변경", 'outline': '의 테두리표시 설정을 변경'};
		if(!confirm("선택하신 로고"+modes[mode]+" 하시겠습니까?"+SPACE)) {
			document.body.focus();
			return false;
		}
		// Ajax 처리
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				var resultData = transport.responseXML.getElementsByTagName("resultData")[0];
				if(resultData.getAttribute("result").match("success")) {
					var nos = (new String(data).indexOf("__")!=-1) ? data.split('__') : [data];
					switch(mode) {
						case "view":
							var views = {'yes': "use", 'no': "unused"};
							var chk_nos = document.getElementsByName("chk_no[]");
							for(var i=0; i<chk_nos.length; i++) {
								if(in_array(chk_nos[i].value, nos)==false) continue;
								var c_img = chk_nos[i].parentNode.parentNode.getElementsByTagName("img")[0]
								c_img.src = "./img/bt_"+views[modeValue]+".gif";
								c_img.value = views[modeValue];
							}
							break;
						case "target":
							var targets = {'_self': "selfwin", '_blank': "newwin"};
							var chk_nos = document.getElementsByName("chk_no[]");
							for(var i=0; i<chk_nos.length; i++) {
								if(in_array(chk_nos[i].value, nos)==false) continue;
								var obj = chk_nos[i].parentNode.parentNode;
								obj.getElementsByTagName("img")[1].src = "./img/bt_"+targets[modeValue]+".gif";
								obj.getElementsByTagName("img")[1].value = targets[modeValue];
								obj.getElementsByTagName("span")[6].target = modeValue;
							}
							break;
						case "delete": // 삭제된 아이템 리스트에 반영
							classObj.remove_item(nos);
							break;
					case "outline": // 테두리표시 - 2010.12.20 added
						var obj = $(classObj.selObject).select('span[id="media"]')[0];
						if(modeValue=='on') obj.addClassName('banner_outline');
						else obj.removeClassName('banner_outline');
						var banner_infos = classObj.selObject.getElementsByTagName("span");
						banner_infos[2].setAttribute('outline', modeValue);
						break;
					}
				}
				alert(resultData.firstChild.nodeValue+SPACE); // 결과 출력
				document.body.focus();
			}
		});

	}, 0);
}

// 로고 이벤트 할당 - 등록/삭제/순위 변동/바인드 관련
LOGO.prototype.rebuild_event = function(reset) {

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

	// 미디어가 보이도록 처리 - this.append_flash_object() 에 부하가 심한편 ㅡㅡ;
	var media_datas = document.getElementsByName('media_data');
	for(var i=0; i<media_datas.length; i++) {
		var infos = media_datas[i].value.split('|');
		this.append_flash_object(media_datas[i].parentNode.getElementsByTagName('span')[0], infos[1], infos[2], infos[3]);
	}

	// 직접입력 방식 로고 처리
	var text_datas = document.getElementsByName('text_data');
	for(var i=0; i<text_datas.length; i++) {
		var xcontent = text_datas[i].innerHTML.replace(/{:_lt:}/g, "<").replace(/{:_gt:}/g, ">").replace(/-->/g, '').replace(/<!--/g, '').replace(/\/\/-->/g, ''); // 2010.12.20 fixed
		try {
			var xwindow = text_datas[i].parentNode.getElementsByTagName('iframe')[0].contentWindow.document.write(xcontent);
			// ▽ 열어본페이지 설정을 '페이지를 열때 마다' 로 설정시 오류가 발생하여 setTimeout() 처리
			setTimeout("classObj.set_text_content('"+text_datas[i].parentNode.getElementsByTagName('iframe')[0].id+"')", 0);
		}
		catch(e) {
			//alert(e.message);
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

// 직접입력 방식 로고 퍼블리쉬
LOGO.prototype.set_text_content = function(iframe) {
	try {
		var iframe = document.getElementsByName(iframe)[0];
		var banner_content = iframe.contentWindow.document.body.innerHTML;

		// A 태그 상의 {:domain:} 머지 - 2008.06.14 추가
		var atags = banner_content.match(/(<a [^<]*href=["|']?([^ "']*)["|']?[^>])/gi);
		if(atags!=null) for(var i=0; i<atags.length; i++) banner_content = banner_content.replace(atags[i], atags[i].replace(domain+"rankup_module/rankup_banner/{:domain:}", "{:domain:}").replace(/{:domain:}/g, domain));

		iframe.parentNode.getElementsByTagName('span')[0].innerHTML = banner_content;
		iframe.contentWindow.document.body.innerHTML = '';
	}
	catch(e) {
		// alert(e.message);
	}
}