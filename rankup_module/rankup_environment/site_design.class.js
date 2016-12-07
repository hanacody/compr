// 사이트디자인 클래스
var SITE_DESIGN = function() {
	this.designMode = 2;
	this.baseDesign = 1;
	this.picker_items = ['single_color', 'gradation_color1', 'gradation_color2'];
	this.picker = null;
	this.target = null;
	this.preview = null;
	this.sColor = '';
	this.sRGB = '';
	this.selectAttach = null;
}
// 사이트디자인용 컬러픽커 생성 / 초기화
SITE_DESIGN.prototype.initialize = function() {
	var classObj = this;
	this.picker = new YAHOO.widget.ColorPicker("yui-picker-panel", {
		showcontrols: false,
		showhsvcontrols: false,
		showhexcontrols: false,
		animate: false
	});
	// 컬러 픽커 활성화
	this.picker.on("rgbChange", function (p_oEvent) {
		classObj.sColor = "#" + this.get("hex");
		classObj.sRGB = this.get(this.OPT.RED)+","+this.get(this.OPT.GREEN)+","+this.get(this.OPT.BLUE);
		$(classObj.target).setAttribute('rgb', classObj.sRGB);
		$(classObj.target).style.backgroundColor = classObj.sColor;
		$(classObj.target.replace(/_prev/g,'')).value = classObj.sColor;
		// 설정값 디자인적용하기
		//classObj.applyDesign();
	});
	// 컬러픽커정보 초기화
	for(var i=0; i<this.picker_items.length; i++) {
		this.target = this.picker_items[i]+"_prev";
		$(this.target).style.backgroundColor = $(this.picker_items[i]).value;
		var rgb = YAHOO.util.Color.hex2rgb($(this.picker_items[i]).value.replace(/#/g,''));
		$(this.target).setAttribute("rgb", rgb.join(','));
	}
}
// 입력받은 RGB 값으로 세팅
SITE_DESIGN.prototype.set_rgb_color = function(el) {
	var color = (el.value.indexOf('#')==-1) ? '#'+el.value : el.value;
	if(color.length<7) return false;
	try {
		var obj = $(el.name+"_prev");
		obj.style.backgroundColor = color;
		var rgb = YAHOO.util.Color.hex2rgb(color.replace(/#/g,''));
		obj.setAttribute("rgb", rgb.join(','));
		el.value = color.toUpperCase();
	}
	catch(e) {
		alert("입력하신 색상값이 올바르지 않습니다."+SPACE);
		el.value = '';
		el.focus();
		return false;
	}
}
// 미리보기 페이지 초기값으로 세팅
SITE_DESIGN.prototype.initializePreview = function() {
	for(var i=0; i<this.picker_items.length; i++) {
		this.target = this.picker_items[i]+"_prev";
		this.sColor = $(this.picker_items[i]).value;
		var rgb = YAHOO.util.Color.hex2rgb($(this.picker_items[i]).value.replace(/#/g,''));
		//this.applyDesign();
	}
}
// 디자인 적용
SITE_DESIGN.prototype.applyDesign = function() {
	var pBody = $(this.preview).contentWindow.document.body;
	var table = pBody.getElementsByTagName("table")[1];
	var target = this.target.replace(/_prev/g,'');
	switch(target) {
		// 테두리색
		case "o":
			table.style.backgroundColor = this.sColor;
			break;
	}
}
// 사이트디자인 열기/닫기
SITE_DESIGN.prototype.openPicker = function(el, mode, pos) {
	if(mode==false) $('site_designDiv').style.visibility = "hidden";
	else {
		//this.change_display('imageManagerTable', false);
		this.target = el;
		var RGB = $(this.target).getAttribute('rgb');
		RGB = (RGB!=null) ? eval('['+RGB+']') : [255,255,255];
		this.picker.setValue(RGB, false);
		$('site_designDiv').style.visibility= "visible";
		// 클릭지점으로 위치시키기
		var obj = $('site_designDiv');
		var pos_x = (pos!=null) ? pos.pageX : event.x + parseInt(document.body.scrollLeft,10);
		var pos_y = (pos!=null) ? pos.pageY : event.y + parseInt(document.body.scrollTop,10);
		obj.style.top = pos_y - 10 + "px";
		obj.style.left = pos_x + 20 + "px";
	}
}
// 팔레트 블라인드 설정
SITE_DESIGN.prototype.togglePaletteBlind = function() {
	if(this.designMode==1) {
		$('palette').style.visibility = "hidden";
		$('paletteBlind').style.width = $('palette').parentNode.offsetWidth - 6;
		$('paletteBlind').style.height = $('palette').parentNode.offsetHeight - 6;
	}
	else {
		$('palette').style.visibility = "visible";
		$('paletteBlind').style.width = 0;
		$('paletteBlind').style.height = 0;
	}
}
// 등록 및 수정시 리스트 블럭킹
SITE_DESIGN.prototype.screen_blind = function(mode) {
	var blind = $('screenBlindDiv');
	if(mode===true) {
		var _height = parseInt(document.body.clientHeight,10);
		if(parseInt(document.body.scrollHeight,10)>_height) _height = parseInt(document.body.scrollHeight,10);
		blind.style.height = _height + "px";
		blind.style.marginTop = "-" + parseInt(document.body.scrollTop,10) + "px";
		this.change_display("screenBlindDiv", true);

		// SELECT 객체 숨기기
		this.sb_selects = document.getElementsByTagName("select");
		for(var i=0; i<this.sb_selects.length; i++) this.sb_selects[i].style.visibility = "hidden";
	}
	else {
		this.change_display("screenBlindDiv", false);
		if(this.sb_selects!=null) for(var i=0; i<this.sb_selects.length; i++) this.sb_selects[i].style.visibility = "visible";
	}
}
// 배너 레이어 토글
SITE_DESIGN.prototype.change_display = function(el, val) {
	if(val===true||val===false) {
		var obj = $(el);
		obj.style.display = val ? "block" : "none";
		if(val===false) {
			if(el!=="screenBlindDiv") this.screen_blind(false);
			return false;
		}
		// 페이지 중앙에 띄우기
		obj.style.top = (obj.offsetHeight>document.body.clientHeight) ? document.body.scrollTop : document.body.scrollTop+(document.body.clientHeight-obj.offsetHeight)/2 + "px";
		obj.style.left = (obj.offsetWidth>document.body.clientWidth) ? document.body.scrollLeft : document.body.scrollLeft+(document.body.clientWidth-obj.offsetWidth)/2 + "px";
		if(el!=="screenBlindDiv") this.screen_blind(true);
	}
	else {
		var _val = $(el).style.display;
		$(el).style.display = (_val=="none") ? "block" : "none";
		this.screen_blind(false);
	}
}
// 첨부파일 업로드
SITE_DESIGN.prototype.attach_post = function(el) {
	if(el.getAttribute("filter")!=null) {
		var filters = el.getAttribute("filter").toLowerCase().split(",");
		var exts = el.value.toLowerCase().split(".");
		if(in_array(exts[exts.length-1], filters)==false) {
			alert("첨부할 수 없는 파일형식입니다."+SPACE);
			document.body.focus();
			return false;
		}
	}
	document.imageManagerFrm.submit();
}
// 아이템 마우스오버/아웃시
SITE_DESIGN.prototype.toggle_className = function(event) {
	//this.className = event.type!="mouseover" ? this.getAttribute("selected")!=null ? "attachSelectItem" : "attachNormalItem" : this.getAttribute("selected")!=null ? "attachSHoverItem" : "attachHoverItem";
	this.getElementsByTagName('img')[0].className = event.type!="mouseover" ? this.getAttribute("selected")!=null ? "img_enabled" : "img_disabled" : this.getAttribute("selected")!=null ? "img_enabled" : "img_hovered";
}
// 이미지 추가
SITE_DESIGN.prototype.attach_draw = function(file_name, info) {
	if(this.selectAttach!=null) {
		this.selectAttach.removeAttribute("selected");
		this.selectAttach.className = "attachNormalItem";
		this.selectAttach.getElementsByTagName('img')[0].className = "img_disabled";
	}

	// 아이템 설정
	var new_item = document.createElement("li");
	new_item.className = "attachSelectItem";
	new_item.setAttribute("selected", "selected");
	this.selectAttach = new_item;

	new_item.setAttribute("fname", file_name);
	new_item.setAttribute("fwidth", info['width']);
	new_item.setAttribute("fheight", info['height']);
	new_item.setAttribute("ftype", info['type']);
	// 붙이기
	$('image_item_box').appendChild(new_item);
	var new_img = new Image();
	new_img.src = file_name;
	new_img.className = "img_enabled";
	new_item.appendChild(new_img);
	// 이벤트 할당
	Event.observe(new_item, 'mouseover', this.toggle_className);
	Event.observe(new_item, 'mouseout', this.toggle_className);
	Event.observe(new_img, 'click', this.select_item);
}
// 이미지 선택
SITE_DESIGN.prototype.select_item = function(event) {
	var fileObj = event.type=="click" ? Event.element(event) : event;
	if(fileObj.tagName.indexOf("IMG")!=-1) fileObj = fileObj.parentNode;
	if(fileObj===site_design.selectAttach) return false;

	if(site_design.selectAttach!=null) {
		site_design.selectAttach.removeAttribute("selected");
		site_design.selectAttach.className = "attachNormalItem";
		site_design.selectAttach.getElementsByTagName('img')[0].className = "img_disabled";
	}
	fileObj.className = "attachSelectItem";
	fileObj.setAttribute("selected", "selected");
	fileObj.getElementsByTagName('img')[0].className = "img_enabled";
	site_design.selectAttach = fileObj;
}
// 이미지 삭제
SITE_DESIGN.prototype.attach_delete = function() {
	if(this.selectAttach==null) {
		alert("삭제하실 이미지를 선택하여 주십시오."+SPACE);
		return false;
	}
	if(!confirm("선택하신 이미지를 삭제하시겠습니까?"+SPACE)) return false;
	var classObj = this;
	var part = document.imageManagerFrm.part.value;
	var filenames = this.selectAttach.getAttribute('fname').split("\/"+part+"\/");
	var url = "./multiProcess.ajax.html?mode=delete_image&part="+part+"&file="+filenames[1];
	var myRequest = new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(transport) {
			if(!transport.responseText.match(null)) {
				var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
				alert(resultData.firstChild.nodeValue+SPACE);
				if(resultData.getAttribute('result')=="success") {
					// 아이템 노드 삭제
					$('image_item_box').removeChild(classObj.selectAttach);
					classObj.selectAttach = null;
					var img_name = part=="menu_bg" ? "menu_bg" : "menu";
					if(filenames[1].indexOf(img_name+".")!=-1) $('image_prev_'+part).update('');
				}
			}
			transport = null;
		},
		onComplete: function() {myRequest=null}
	});
}
// 이미지 적용
SITE_DESIGN.prototype.apply_image = function() {
	if(this.selectAttach==null) {
		alert("메뉴 이미지로 적용하실 이미지를 선택하여 주십시오."+SPACE);
		return false;
	}
	if(!confirm("선택하신 이미지를 메뉴 이미지로 사용하시겠습니까?"+SPACE)) return false;
	var classObj = this;
	var part = document.imageManagerFrm.part.value;
	var filenames = this.selectAttach.getAttribute('fname').split("\/"+part+"\/");
	var url = "./multiProcess.ajax.html?mode=apply_image&part="+part+"&file="+filenames[1];
	var myRequest = new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(transport) {
			if(!transport.responseText.match(null)) {
				var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
				if(resultData.getAttribute('result')=="success") {
					//classObj.change_display('imageManagerTable', false);
					$('image_prev_'+part).update('');
					var img_name = part=="menu_bg" ? "menu_bg" : "menu";
					var new_img = new Image();
					var ext = filenames[1].split(".");
					new_img.style.width = classObj.selectAttach.getAttribute('fwidth');
					new_img.style.height = classObj.selectAttach.getAttribute('fheight');
					new_img.src = filenames[0]+"/"+part+"/"+img_name+"."+ext[1];
					$('image_prev_'+part).appendChild(new_img);
				}
				alert(resultData.firstChild.nodeValue+SPACE);
			}
			transport = null;
		},
		onComplete: function() {myRequest=null}
	});
}