if(domain==undefined) var domain = "http://"+document.domain+"/";

// 메시지 얼럿트
var alerts = document.alerts = function(str) { str.alert(); return false; }
var confirms = document.confirms = function(str) { return str.confirm() }

// 브라우져 정보
var browser = {};
/MSIE ([0-9]{1,})\.?/.exec(navigator.userAgent);
browser.version = RegExp.$1;
browser.kind = navigator.userAgent.match(/MSIE/) ? "IE" : "GEKO";

var rss_icon_func = function(obj) {
	window.clipboardData.setData('text', obj.href);
	alert('RSS 주소가 복사되었습니다.');
}

// IE 전용 스크립트 - 2008.12.11 fixed
var setPng24 = function(el, save) {
	if(el.src=="about:blank") return;
	if(browser.kind=="GEKO" || (browser.kind=="IE" && browser.version>6)) return;
	if(save!==false) el.setAttribute('default', el.src);

	el.style.height = '1px';
	el.style.filter = 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src='+el.src+')';
	el.src = "about:blank";
}

// 2009.02.03 개선 - CSS : expression 사용안함
var rankup_setPng24 = function(el) {
	if(browser.kind=="GEKO" || (browser.kind=="IE" && browser.version>6)) return false;
	if(el==undefined) {
		if(!document.body.id) document.body.id = '_tmp_body_';
		el = document.body.id;
	}
	var imgs = $(el).select('img[class~="png24"]');
	imgs.each(function(img) {
		img.setAttribute('default', img.src);
		img.style.height = '1px';
		img.style.filter = 'progid:DXImageTransform.Microsoft.AlphaImageLoader(src='+img.src+')';
		img.src = 'about:blank';
	});
}

//##################################################################################
function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
function MM_findObj(n, d) { //v4.01
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && d.getElementById) x=d.getElementById(n); return x;
}
function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

//숫자 아닌값을 숫자로 바꾸는 스크립트
function onlyDigit(el) {
	el.value = el.value.replace(/\D/g,'');
}

var leftMenu = function(que, width) {
	if(width==undefined) width = 130;
	document.write("<embed id='leftMenu' src='"+domain+"Libs/_images/left_menu.swf?q="+encodeURI(que)+"' loop='false' menu='false' quality='high' bgcolor='#ffffff' width='"+width+"' height='26' name='leftMenu' align='middle' allowScriptAccess='sameDomain' allowFullScreen='false' loop='false' menu='false' wmode='transparent' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer' />");
}

var titleBar = function(que, width) {
	if(width==undefined) width = 270;
	document.write("<embed id='titleBar' src='"+domain+"Libs/_images/bar.swf?q="+encodeURI(que)+"' loop='false' menu='false' quality='high' bgcolor='#ffffff' width='"+width+"' height='47' name='titleBar' align='middle' allowScriptAccess='sameDomain' allowFullScreen='false' loop='false' menu='false' wmode='transparent' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer' />");
}

// flash object 처리
var flashDraw = function(i,s,w,h,t,v) {
	document.write("<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\" width="+w+" height="+h+" id="+i+"><param name=\"allowFullScreen\" value=\"true\" /><param name=wmode value="+t+" /><param name=flashvars value="+v+" /><param name=allowScriptAccess value=sameDomain /><param name=movie value="+s+" /><param name=quality value=high /><param name=menu value=false /><param name=loop value=true /><embed src="+s+" name="+i+" quality=high wmode="+t+" flashvars="+v+" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" width="+w+" height="+h+" allowScriptAccess=sameDomain allowFullScreen=true loop=true menu=false></object>");
}

// media player object 처리
var mediaDraw = function(i,s,w,h) {
	document.write("<object id='"+i+"' width='"+w+"' height='"+h+"' classid='CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95' codebase='http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701' standby='Loading Microsoft Windows Media Player components...' type='application/x-oleobject'><param NAME='AudioStream' VALUE='-1'><param NAME='AutoSize' VALUE='0'><param NAME='AutoStart' VALUE='-1'><param NAME='AnimationAtStart' VALUE='-1'><param NAME='AllowScan' VALUE='-1'><param NAME='AllowChangeDisplaySize' VALUE='-1'><param NAME='AutoRewind' VALUE='0'><param NAME='Balance' VALUE='0'><param NAME='BaseURL' VALUE=''><param NAME='BufferingTime' VALUE='100'><param NAME='CaptioningID' VALUE=''><param NAME='ClickToPlay' VALUE='0'><param NAME='CursorType' VALUE='0'><param NAME='CurrentPosition' VALUE='-1'><param NAME='CurrentMarker' VALUE='0'><param NAME='DefaultFrame' VALUE='Slide'><param NAME='DisplayBackColor' VALUE='0'><param NAME='DisplayForeColor' VALUE='16777215'><param NAME='DisplayMode' VALUE='0'><param NAME='DisplaySize' VALUE='4'><param NAME='Enabled' VALUE='-1'><param NAME='EnableContextMenu' VALUE='0'><param NAME='EnablePositionControls' VALUE='-1'><param NAME='EnableFullScreenControls' VALUE='0'><param NAME='EnableTracker' VALUE='-1'><param NAME='Filename' VALUE='"+s+"'><param NAME='InvokeURLs' VALUE='-1'><param NAME='Language' VALUE='-1'><param NAME='Mute' VALUE='0'><param NAME='PlayCount' VALUE='1'><param NAME='PreviewMode' VALUE='0'><param NAME='Rate' VALUE='1'><param NAME='SAMILang' VALUE=''><param NAME='SAMIStyle' VALUE=''><param NAME='SAMIFileName' VALUE=''><param NAME='SelectionStart' VALUE='-1'><param NAME='SelectionEnd' VALUE='-1'><param NAME='SendOpenStateChangeEvents' VALUE='-1'><param NAME='SendWarningEvents' VALUE='-1'><param NAME='SendErrorEvents' VALUE='-1'><param NAME='SendKeyboardEvents' VALUE='0'><param NAME='SendMouseClickEvents' VALUE='0'><param NAME='SendMouseMoveEvents' VALUE='0'><param NAME='SendPlayStateChangeEvents' VALUE='-1'><param NAME='ShowCaptioning' VALUE='0'><param NAME='ShowControls' VALUE='1'><param NAME='ShowAudioControls' VALUE='1'><param NAME='ShowDisplay' VALUE='0'><param NAME='ShowGotoBar' VALUE='0'><param NAME='ShowPositionControls' VALUE='0'><param NAME='ShowStatusBar' VALUE='0'><param NAME='ShowTracker' VALUE='1'><param NAME='TransparentAtStart' VALUE='-1'><param NAME='VideoBorderWidth' VALUE='2'><param NAME='VideoBorderColor' VALUE='0'><param NAME='VideoBorder3D' VALUE='0'><param NAME='Volume' VALUE='-600'><param NAME='WindowlessVideo' VALUE=''></object>");
}

// 페이지 스크롤바 위치
function getPageScroll() {
	var yScroll;
	if(self.pageYOffset) yScroll = self.pageYOffset;
	else if(document.documentElement && document.documentElement.scrollTop) yScroll = document.documentElement.scrollTop; // Explorer 6 Strict
	else if(document.body) yScroll = document.body.scrollTop; // all other Explorers
	arrayPageScroll = new Array('',yScroll)
	return arrayPageScroll;
}

// 페이지 사이즈
function getPageSize(){
	var xScroll, yScroll;
	if(window.innerHeight && window.scrollMaxY) {
		xScroll = document.body.scrollWidth;
		yScroll = window.innerHeight + window.scrollMaxY;
	}
	else if(document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
		xScroll = document.body.scrollWidth;
		yScroll = document.body.scrollHeight;
	}
	else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
		xScroll = document.body.offsetWidth;
		yScroll = document.body.offsetHeight;
	}
	var windowWidth, windowHeight;
	if(self.innerHeight) {	// all except Explorer
		windowWidth = self.innerWidth;
		windowHeight = self.innerHeight;
	}
	else if(document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
		windowWidth = document.documentElement.clientWidth;
		windowHeight = document.documentElement.clientHeight;
	}
	else if(document.body) { // other Explorers
		windowWidth = document.body.clientWidth;
		windowHeight = document.body.clientHeight;
	}
	pageHeight = (yScroll < windowHeight) ? windowHeight : yScroll;
	pageWidth = (xScroll < windowWidth) ? windowWidth : xScroll;
	arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight)
	return arrayPageSize;
}

// 블라인드
var sb_selects;
var screenBlind = function(mode) {
	if(mode == "on") {
		var arrayPageSize = getPageSize();
		var arrayPageScroll = getPageScroll();
		var sendingImage = document.createElement("img");
		sendingImage.src = domain+"images/sending.gif";
		sendingImage.setAttribute('id','sendingImage');
		sendingImage.style.position = "absolute";
		sendingImage.width = "195";
		sendingImage.height = "18";
		sendingImage.style.zIndex = "150";
		sendingImage.style.top = arrayPageScroll[1] + ((arrayPageSize[3] - 35 - sendingImage.height) / 2) + 'px';
		sendingImage.style.left = ((arrayPageSize[0] - sendingImage.width) / 2) + 'px';
		sendingImage.style.display = "block";
		$('screenBlind').appendChild(sendingImage);
		$('screenBlind').style.height = (arrayPageSize[1] + 'px');
		$('screenBlind').style.display = "block";
		sb_selects = document.getElementsByTagName("select");
		for(var i=0; i<sb_selects.length; i++) sb_selects[i].style.visibility = "hidden";
	}
	else if(mode=='off') {
		$('screenBlind').update('');
		$('screenBlind').style.display = "none";
		for(var i=0; i<sb_selects.length; i++) sb_selects[i].style.visibility = "visible";
	}
}

//##################################################################################
//## 콤보박스 클래스 정의
//##################################################################################
var COMBOBOX = function() {
	this.items = new Array(); // 오브젝트
	this.drawMode = "multi";  // 드로우 방식 : single  or  multi
	this.width = 0;
	this.pulldown_flag = new Array(); // 풀다운 체크
	this.left = "<img src='"+domain+"images/top01_search01.gif'>";
	this.body = "url('"+domain+"images/top01_search02.gif') repeat-x";
	this.right = "<img src='"+domain+"images/top01_search_icon.gif'>";
	this.styles = { // 스킨에 적용할 스타일
		"base": "border:#dedede 0px solid;font-size:9pt;cursor:pointer;padding:0px;",
		"option": "border:#dedede 1px solid;font-size:9pt;background-color:white;padding:2px;", // 옵션배경
		"item": "border:0;font-size:9pt;font-family:dotum;color:black;line-height:120%;background-color:white;padding:2 0 0 5px;" // 아이템
	};
	this.base_color = { // 기본색상 : this.styles['item'] 설정값과 동일하게 설정
		"background": "white",
		"color": "black"
	};
	this.hover_color = { // 호버색상
		"background": "#336699",
		"color": "white"
	};
}
// 콤보박스 선택
COMBOBOX.prototype.select_item = function(obj) {
	var item = $(obj.parentNode.id.replace(/_option$/g,''));
	item.value = obj.value;
	$(item.name+'_text').update(obj.innerHTML);
	this.pulldown(item.name);
}
// 콤보박스 아이템 호버
COMBOBOX.prototype.item_hover = function(obj, mode) {
	var color = (mode=="hover") ? this.hover_color : this.base_color;
	var name = obj.parentNode.id.replace(/_option$/g,'');
	var item = $(name);
	if(this.pulldown_flag[parseInt(item.getAttribute('no'))]==true) { // 선택값 초기화
		var option = obj.parentNode.getElementsByTagName('div');
		for(var i=0; i<option.length; i++) {
			if(option[i].value==item.value) {
				this.pulldown_flag[parseInt(item.getAttribute('no'))] = false;
				this.item_hover(option[i], 'base');
				break;
			}
		}
	}
	for(var idx in color) obj.style.setAttribute(idx, color[idx]);
}
// 콤보박스 풀다운
COMBOBOX.prototype.pulldown = function(obj) {
	var item = $(obj+'_option');
	item.style.display = (item.style.display=="block") ? "none" : "block";
	if(item.style.display=="block") {
		var option = item.getElementsByTagName('div');
		for(var i=0; i<option.length; i++) {
			if(option[i].value==$(obj).value) {
				this.pulldown_flag[$(obj).getAttribute('no')] = true;
				for(var idx in this.hover_color) option[i].style.setAttribute(idx, this.hover_color[idx]);
				break;
			}
		}
		var frame = item.getElementsByTagName('iframe')[0];
		frame.style.width = parseInt(item.offsetWidth, 10)-2;
		frame.style.height = parseInt(item.offsetHeight, 10)-2;
	}
}
// 콤보박스 스킨
COMBOBOX.prototype.skin = function(obj, no) {
	var skins = { // 스킨
		"base": "<input type='hidden' name=\"{:name:}\" value=\"{:value:}\" no='{:no:}'><div style='"+this.styles['base']+"' id=\"{:name:}\" onClick=\"combobox.pulldown('{:name:}')\"><table width='100%' border='0' cellpadding='0' cellspacing='0'><tr><td>"+this.left+"</td><td style=\"background:"+this.body+";width:{:width:}px;font-family:verdana;letter-spacing:-1px;padding:3 3 0 1px;overflow:hidden\" id='{:name:}_text'>{:text:}</td><td align='right'>"+this.right+"</td></tr></table></div>",
		"option": "<div style='margin-top:2px;position:absolute;display:none;"+this.styles['option']+"' id=\"{:name:}_option\"><iframe src=\"about:blank\" style=\"position:absolute;z-index:0;left:-1px;top:-1px;"+this.styles['option']+"\" frameborder=\"0\"></iframe>{:item:}</div>",
		"item": "<div style='position:relative;z-index:1;cursor:pointer;width:100%;"+this.styles['item']+"' value=\"{:value:}\" onMouseOver=\"combobox.item_hover(this, 'hover')\" onMouseOut=\"combobox.item_hover(this, 'base')\" onClick=\"combobox.select_item(this)\">{:text:}</div>"
	};
	var option = obj.options;
	var skinHTML = skins['base'].replace(/{:name:}/g, obj.name).replace(/{:no:}/g, no);
	skinHTML = skinHTML.replace(/{:value:}/g, obj.value).replace(/{:text:}/g, obj.options[obj.selectedIndex].text); // 현재 선택된 값;
	for(var i=0, item='', max_width=0; i<option.length; i++) {
		if(max_width < option[i].text.bytes()) max_width = option[i].text.bytes();
		item += skins['item'].replace(/{:value:}/g, option[i].value).replace(/{:text:}/g, option[i].text);
	}
	return skinHTML.replace(/{:width:}/g, Math.ceil(parseInt(max_width)*6)) + skins['option'].replace(/{:name:}/g, obj.name).replace(/{:item:}/g, item);
}
// 콤보박스 생성
COMBOBOX.prototype.draw = function(obj) {
	if(obj) this.items[0] = obj;
	for(var i=0; i<this.items.length; i++) {
		if(this.items[i].outerHTML) this.items[i].outerHTML = this.skin(this.items[i], i);
		else {
			// 파폭 - 미구현
			var xs = new XMLSerializer;
			xs.serializeToString(this.items[i]).innerHTML = this.skin(this.items[i], i);
		}
	}
}
// 콤보박스 초기화
COMBOBOX.prototype.initialize = function() {
	var items = document.getElementsByTagName('select');
	for(var i=0; i<items.length; i++) {
		if(items[i].type=="combobox") {
			this.pulldown_flag[this.items.length] = false;
			if(this.drawMode=="multi") this.items.push(items[i]);
			else this.draw(items[i]); // 드로우
		}
	}
	if(this.items.length) this.draw();
}

/**
 * 플로터
 */
var floater = {
	frame: null,
	baseY: 0, // y축 기본여백
	cycle: 10, // 재귀호출간격
	pro: 10, // 개체이동속도
	initialize: function(frame, baseY, pro, cycle) {
		this.frame = $(frame); // 개체
		this.frame.style.position = 'absolute';
		if(baseY) this.baseY = baseY;
		if(pro) this.pro = pro;
		if(cycle) this.cycle = cycle;

		this.move();
	},
	move: function () {
		Position.prepare();
		var frameTop = this.frame.style.top ? parseInt(this.frame.style.top,10) : this.frame.offsetTop;
		var docTop = (Position.deltaY>this.baseY) ? Position.deltaY : this.baseY;
		var moveY = Math.ceil(Math.abs(frameTop - docTop) / this.pro);
		this.frame.style.top = (frameTop<docTop) ? frameTop + moveY + 'px' : frameTop - moveY + 'px';

		var self = this;
		setTimeout(function() { self.move() }, this.cycle);
	}
}

/*
 * 스크롤러
 */
var scroller = {
	frame: null,
	initialize: function(frame, delay) {
		this.frame = $(frame);
		this.delay = delay==undefined ? 3000 : delay; // 기본 3초 간격
		this.timer = null; // 타이머
		this.hover = false; // 마우스 오버
		this.done = true; // 스크롤 완료
		var self = this;
		if(this.frame.cleanWhitespace().hasChildNodes()===true) {
			Event.observe(this.frame, 'mouseover', function() { self.hover = true });
			Event.observe(this.frame, 'mouseout', function() { self.hover = false });
			setInterval(function() {
				if(self.done==false || self.hover==false) {
					var element = $(self.frame.getElementsByTagName('dl')[0]);
					var new_dl = document.createElement('dl');
					$(new_dl).setStyle({ color: element.getStyle('color'), backgroundColor: element.getStyle('backgroundColor') }).addClassName(element.classNames());
					new_dl.innerHTML = element.innerHTML;
					self.frame.appendChild(new_dl);
					self.timer = setInterval(function() { self.scrolling(element) }, 10);
				}
			}, this.delay);
		}
	},
	scrolling: function(element) {
		var height = parseInt(element.getStyle('height'), 10);
		var curMargin = parseInt(element.getStyle('marginTop'), 10);
		if(Math.abs(curMargin)<height) {
			this.done = false;
			element.setStyle({marginTop: (curMargin-1) +'px'});
		}
		else {
			this.done = true;
			try { this.frame.removeChild(element); }
			catch(e) { }
			clearInterval(this.timer); // kill timer
		}
	}
}

/**
 * 퀵배너
 */
var quick_banner = {
	container: null,
	frame: null,
	initialize: function(container, frame) {
		this.container = $(container);
		this.frame = $(frame);
		this.frame.setStyle({
			position: 'absolute',
			display: 'none',
			top: 0
		});
		var self = this;
		Event.observe(window, 'load', function() {
			var offset = self.init();
			self.frame.show();
			floater.initialize(self.frame, offset.top);
		});
		Event.observe(window, 'resize', this.init.bind(this));
	},
	init: function() {
		var offset = this.container.positionedOffset();
		var left = offset.left + this.container.offsetWidth + 15;
		this.frame.setStyle({ left: left +'px', top: offset.top +'px' });
		return offset;
	}
}

//##################################################################################
//##  글자 크기 제어 - 2009.01.14
//##################################################################################
var font_size = {
	size: {def:9, min:8, max:20},
	plus: function(el) {
		var obj = $(el);
		var fsize = obj.style.fontSize ? parseInt(obj.style.fontSize, 10) : this.size.def;
		if(fsize < this.size.max) obj.setStyle({ 'fontSize': (fsize+1)+'pt', 'lineHeight': '180%' });
	},
	minus: function(el) {
		var obj = $(el);
		var fsize = obj.style.fontSize ? parseInt(obj.style.fontSize, 10) : this.size.def;
		if(fsize > this.size.min) obj.setStyle({ 'fontSize': (fsize-1)+'pt', 'lineHeight': '180%' });
	}
}

//##################################################################################
//## 달력 클래스 정의 - 2012.03.09 fixed
//##################################################################################
var RANKUP_CALENDAR = Class.create({
	handler: null, // 핸들러
	initialize: function() {
		this.toDay = new Date();
		this.selYear = this.toDay.getUTCFullYear();
		this.selMonth = this.toDay.getUTCMonth() + 1;
		this.selDay = this.toDay.getUTCDate();
		this.lastDays = ['', 31,28,31,30,31,30,31,31,30,31,30,31];
		this.calendarBody = 'calendarBody';
		this.selComponent = null;
		this.compYear = null;
		this.compMonth = null;
		this.compDay = null;
		this.drawStatus = false;
		this.minDate = null; // 하한선
		this.maxDate = null; // 상한선
		this.displayRestday = null; // 공휴일표시 - 2010.12.23 added
		this.rest_days = {}; // 공휴일 - 2010.12.23 added
	},
	// 달력 범위 설정
	get_calendar_range: function(mode) {
		var calendar_options = '';
		switch(mode) {
			case 'year':
				this.toDay = new Date();
				var now_year = this.toDay.getUTCFullYear();
				if(this.minDate==null) {
					var min_year = parseInt(now_year)-1;
					this.minDate = String(min_year) +'-01-01';
				}
				else {
					var date_infos = this.minDate.split('-');
					var min_year = date_infos[0];
				}
				if(this.maxDate==null) {
					var max_year = parseInt(now_year)+1;
					this.maxDate = String(max_year) +'-12-31';
				}
				else {
					var date_infos = this.maxDate.split('-');
					var max_year = date_infos[0];
				}
				for(var year=max_year; year>=min_year; year--) calendar_options += '<option value="'+ year +'">'+ year +'년</option>';
				break;
			case 'month':
				for(var month=1; month<=12; month++) {
					calendar_options += '<option value="'+ month +'">'+ month.toPaddedString(2) +'월</option>';
				}
				break;
		}
		return calendar_options;
	},
	// 달력 그리기
	draw_calendar: function(el, base, spot) {
		var obj = $('calendar_div');
		this.selComponent = el;
		this.minDate = el.getAttribute('mindate');
		this.maxDate = el.getAttribute('maxdate');
		this.displayRestday = el.getAttribute('displayRestday'); // 2010.12.23 added
		if(this.maxDate!=null && base==undefined && el.value=='') base = {'value':this.maxDate};

		if(obj!=null) rankup_calendar.remove_calendar();
		this.drawStatus = false;

		var width = Prototype.Browser.IE ? 160 : 148;
		var html = "\
		<div id='calendar_div' style='position:absolute;z-index:200;'>\
			<div style='position:absolute;width:160px;height:179px;left:0px;top:0px'><iframe src='about:blank' style='width:100%;height:100%;' frameborder='0'></iframe></div>\
			<div style='width:"+width+"px;position:relative;border:#cdcdcd 1px solid;top:0;left:0;padding:4px;background-color:#f8f8f8;'>\
				<table cellpadding='0' cellspacing='0' border='0' style='margin-bottom:4px;'>\
				<tr>\
					<td style='padding-right:3px;'><select style='width:64px' onChange=\"rankup_calendar.change_date_II(this)\">"+this.get_calendar_range('year')+"</select></td>\
					<td style='padding-right:3px;'><select style='width:52px' onChange=\"rankup_calendar.change_date_II(this)\">"+this.get_calendar_range('month')+"</select></td>\
					<td align='right'><a onClick='rankup_calendar.remove_calendar()'><img src='"+domain+"Libs/_images/btn_close_s.gif' align='absmiddle'></a></td>\
				</tr>\
				</table>\
				<span style='display:none'><button onClick=\"rankup_calendar.change_date('-1 year')\" style=\"background:url('"+domain+"images/btn_prev.gif');width:13px;height:16px;border:0;\"></button> <input type='text' size='3' readOnly style='letter-spacing:0px;background-color:#f8f8f8;'> <button onClick=\"rankup_calendar.change_date('1 year')\" style=\"background:url('"+domain+"images/btn_next.gif');width:13px;height:16px;border:0;\"></button>\
				&nbsp;&nbsp;<button onClick=\"rankup_calendar.change_date('-1 month')\" style=\"background:url('"+domain+"images/btn_prev.gif');width:13px;height:16px;border:0;\"></button> <input type='text' size='1' readOnly style='background-color:#f8f8f8;'> <button onClick=\"rankup_calendar.change_date('1 month')\" style=\"background:url('"+domain+"images/btn_next.gif');width:13px;height:16px;border:0;\"></button></span>\
				<table cellpadding='0' cellspacing='1' bgcolor='#C9C9C9' border='0' style='margin-top:3px;' width='100%'>\
				<tr bgcolor='#f4f8fb' align='center'>\
					<td><font color='red'>일</font></td>\
					<td>월</td>\
					<td>화</td>\
					<td>수</td>\
					<td>목</td>\
					<td>금</td>\
					<td><font color='#3366cc'>토</font></td>\
				</tr>\
				<tbody bgcolor='white' align='center' id='"+this.calendarBody+"'>\
				</tbody>\
				</table>\
			</div>\
		</div>";
		new Insertion.Before(this.selComponent, html);
		if(!el.value.blank()) this.change_date(el.value);
		else [null,undefined].include(base) ? this.change_date(el.value) : this.change_date(base.value);
		$('calendar_div').style.marginTop = this.selComponent.offsetHeight + 'px';
	},
	change_date_II: function(el) {
		var info_selects = $('calendar_div').getElementsByTagName('select');
		this.change_date(info_selects[0].value+'-'+info_selects[1].value+'-'+this.selDay);
	},
	change_date: function(date) { // date { 1 year | -1 year | -1 month | 1 month }
		if(date=='') {
			this.toDay = new Date();
			this.selYear = this.toDay.getUTCFullYear();
			this.selMonth = this.toDay.getUTCMonth() + 1;
			this.selDay = this.toDay.getUTCDate();
			this.compYear = this.compMonth = this.compDay = '';
		}
		else {
			switch(date) {
				case '-1 year': this.selYear -= 1; break;
				case '1 year': this.selYear += 1; break;
				case '-1 month':
					if(this.selMonth!=1) this.selMonth -= 1;
					else { // 0 : 1월
						this.selYear -= 1;
						this.selMonth = 12;
					}
					break;
				case '1 month':
					if(this.selMonth!=12) this.selMonth += 1;
					else { // 11 : 12월
						this.selYear += 1;
						this.selMonth = 1;
					}
					break;
				default: // 날짜값을 받은 경우 : 2008-09-12
					var date_infos = date.split('-');
					with(Math) {
						this.compYear = floor(date_infos[0]);
						this.compMonth = floor(date_infos[1]);
						this.compDay = floor(date_infos[2]);
					}
					if(this.drawStatus===true && this.compYear==this.selYear && this.compMonth==this.selMonth && this.compDay==this.selDay) return true;
					this.selYear = this.compYear;
					this.selMonth = this.compMonth;
					this.selDay = this.compDay;
			}
		}
		var calendar_body = $(this.calendarBody);
		this.toDay = new Date(this.selYear, this.selMonth-1, this.selDay);
		this.lastDays[2] = (this.selYear%4)==0 && ((this.selYear%100)!=0 || (this.selYear%400)==0) ? 29 : 28;
		var info_inputs = $('calendar_div').getElementsByTagName('input');
		var info_selects = $('calendar_div').getElementsByTagName('select');
		info_inputs[0].value = info_selects[0].value = this.selYear;
		info_inputs[1].value = info_selects[1].value = this.selMonth;

		calendar_body.update(); // 초기화

		// 2010.12.23 added
		var today = new Date();
		var ndate = [today.getUTCFullYear(), today.getUTCMonth()+1, today.getUTCDate()].join('-');
		var cdate = [this.selYear, this.selMonth].join('-');

		// 공휴일 로드
		if(this.displayRestday!=null) {
			var self = this;
			if(this.rest_days[cdate]!=null) {
				if(this.rest_days[cdate]) setTimeout(function() { self.apply_rest_day(self.rest_days[cdate]) }, 0);
			}
			else {
				var url = domain +'Libs/_js/calculate_date.ajax.php?mode=check_days&year='+ this.selYear +'&month='+ this.selMonth;
				new Ajax.Request(url, { method: 'get',
					onSuccess: function(transport) {
						self.rest_days[cdate] = transport.responseXML.getElementsByTagName('resultData')[0].firstChild.nodeValue.split(',');
						self.apply_rest_day(self.rest_days[cdate]);
					}
				});
			}
		}
		var first_day_cell = 0;
		var first_cell = ((this.toDay.getDay()+7)-(this.selDay-1)%7)%7;

		for(var row=0, day=1; row<6; row++) {
			var new_row = calendar_body.insertRow(calendar_body.rows.length);
			for(var cell=0; cell<7; cell++) {
				if(cell == first_cell) first_day_cell += 1;
				var new_cell = new_row.insertCell(cell);
				new_cell.id = 'day_'+ day;
				switch(cell) {
					case 0: new_cell.style.color = '#ff0000'; break;
					case 6: new_cell.style.color = '#3366cc'; break;
				}
				if(first_day_cell>=1 && day<=this.lastDays[this.selMonth]) {
					new_cell.innerHTML = day;
					new_cell.style.cursor = 'pointer';
					if(ndate==cdate+'-'+day) {
						new_cell.style.backgroundColor = '#FFFF00';
					}
					if(day++==this.compDay && this.compMonth==this.selMonth && this.compYear==this.selYear) {
						new_cell.className = 'selectCell';
						new_cell.setAttribute('selected', 'true');
						this.selCell = new_cell;
					}
					else new_cell.className = 'normalCell';

					Event.observe(new_cell, 'mouseover', this.toggle_className);
					Event.observe(new_cell, 'mouseout', this.toggle_className);
					Event.observe(new_cell, 'click', this.apply_date.bind(this));
				}
				else {
					new_cell.innerHTML = '';
					new_cell.style.backgroundColor = '#F5F5F5';
				}
				new_cell.height = '20px';
				new_row.appendChild(new_cell);
			}
		}
		this.drawStatus = (calendar_body.rows.length==6);
	},
	// 날짜 변경 - 단축키 - 2012.03.20 fixed
	set_date: function(mode, dest, base) {
		if(base==undefined || base=='') {
			var toDay = new Date();
			var baseYear = toDay.getUTCFullYear();
			var baseMonth = toDay.getUTCMonth() + 1;
			var baseDay = toDay.getUTCDate()
			var base_date = baseYear+'-'+baseMonth+'-'+baseDay;
		}
		else {
			var base_date = base.value;
		}
		switch(mode) {
			case 'today':
				var xdate = new Date();
				break;
			case 'yesterday':
				var xdate = new Date();
				xdate.setDate(xdate.getDate()-1);
				break;
			default:
				var dates = base_date.split('-');
				var xdate = new Date(dates[0], dates[1]-1, dates[2]);
				var period = mode.split(' ');
				switch(period[1]) {
					case 'day':
						xdate.setDate(xdate.getDate() + parseInt(period[0]));
						break;
					case 'month':
						xdate.setMonth(xdate.getMonth() + parseInt(period[0]));
						break;
				}
		}
		var dest_date = new Template('#{year}-#{month}-#{day}').evaluate({
			year: xdate.getUTCFullYear(),
			month: (xdate.getUTCMonth() + 1).toPaddedString(2),
			day: xdate.getUTCDate().toPaddedString(2)
		});
		if(['today','yesterday'].include(mode)) dest.value = base.value = dest_date;
		else dest.value = dest_date;
		if(this.selComponent===dest) this.change_date(dest.value);
	},
	// 분류 설정시 아이템 오버/아웃시에 사용
	toggle_className: function(event) {
		this.className = (event.type!='mouseover') ? this.getAttribute('selected')!=null ? 'selectCell' : 'normalCell' : this.getAttribute('selected')!=null ? 'shoverCell' : 'hoverCell';
	},
	// 달력박스 감추기
	hidden_calendar: function() {
		var frame = $('calendar_div');
		if(frame!=null) frame.hide();
	},
	// 달력개체 제거
	remove_calendar: function() {
		var frame = $('calendar_div');
		if(frame!=null) frame.remove();
	},
	// 선택한 날짜값 반환
	apply_date: function(event) {
		var el = Event.element(event);
		var year = this.selYear;
		var month = Math.floor(this.selMonth);
		var day = Math.floor(el.innerHTML);
		var selDate = year+'-'+month.toPaddedString(2)+'-'+day.toPaddedString(2);
		if(selDate<this.minDate || selDate>this.maxDate) {
			return alerts('날짜는 '+ this.minDate +' 부터 '+ this.maxDate +' 까지 입력가능합니다.');
		}
		else {
			this.selComponent.value = selDate;
			this.hidden_calendar();
		}
	},
	// 공휴일 반영
	apply_rest_day: function(rest_days) {
		$A(rest_days).each(function(rest) {
			var obj = $('day_'+ rest);
			obj.style.color = '#ff0000';
		});
	}
});
var rankup_calendar = new RANKUP_CALENDAR;

/*
## 랭크업 달력 클래스 사용 방법 ##
☞ 스타일 : .calendar {width:80px;text-align:center;font-family:verdana;letter-spacing:-2px;font-weight:bolder;cursor:pointer;}
☞ 사용법 : <span style='float:left'><input type="text" name="sdate" readOnly value="2008-09-12" onClick="rankup_calendar.draw_calendar(this)" class="calendar"></span>
*/

//##################################################################################
//## 랭크업 CSS 버튼 생성 - 2008.12.18 // 2009.02.03 fixed
//##################################################################################
var rankup_css_button = {
	initialize: function() {
		if(!document.body.id) document.body.id = '_tmp_body_';
		var buttons = $(document.body.id).getElementsBySelector('a[class="css_button"]');
		buttons.each(function(button) { rankup_css_button.formalize(button) });
	},
	apply: function(el) {
		var buttons = $(el).getElementsBySelector('a[class="css_button"]');
		buttons.each(function(button) { rankup_css_button.formalize(button) });
	},
	formalize: function(button) {
		var shape = button.getAttribute('shape');
		var shapes = shape==null ? [] : shape.split(' ');
		if(!in_array(shapes, ['normal', 'small'])) shapes.push('normal');
		for(var i=0; i<shapes.length; i++) {
			if(!shapes[i]) continue;
			var class_name = 'css_'+shapes[i]+'_button';
			if(button.id) $(button.id).addClassName(class_name);
			else { button.id = '_tmp_button_'; $(button.id).addClassName(class_name); button.removeAttribute('id'); }
		}
		if(!button.href) button.href = 'javascript:void(0)';
		button.onmouseover = function() {window.status=''; return true}
		button.update('<span>'+button.innerHTML+'</span>');
	}
}
/*
## 랭크업 CSS 버튼 사용 방법 ##
☞ 스타일 :
	a.css_normal_button{padding-left:8px;background:url('./images/btn1_bg.gif');text-decoration:none;font:12px 굴림;color:#777777;letter-spacing:0px;line-height:27px;display:inline-block;}
	a.css_normal_button:hover{background: url('./images/btn1_bg.gif') 0 -27px;color:#000000;}
	a.css_normal_button span{padding-right:8px;background:url('./images/btn1_bg.gif') 100% 0;cursor:pointer;padding-top:7px;padding-bottom:7px;line-height:27px;}
	a.css_normal_button:hover span{background:url('./images/btn1_bg.gif') 100% -27px;}
☞ 사용법 : <a class="css_button">랭크업CSS버튼</a>
*/

//##################################################################################
//## 즐겨찾기 파이어 폭스 호환 버젼
//##################################################################################
var rankup_favorite = function(url, title) {
	if(window.sidebar) window.sidebar.addPanel(title, url, "");
	else if(window.opera && window.print) {
		var elem = document.createElement('a');
		elem.setAttribute('href',url);
		elem.setAttribute('title',title);
		elem.setAttribute('rel','sidebar');
		elem.click();
	}
	else if(document.all) window.external.AddFavorite(url, title);
	else if(navigator.userAgent.match(/Chrome/)){
		alert("즐겨찾기에 추가하시려면 CTRL+D 키를 눌러주세요");
	}
}

// 스팸방지 보안코드 이미지 갱신 - 2010.10.13 added
var confirm_code_reset = function() {
	var keystring = $('keystring');
	if(keystring) {
		keystring.value = '';
		var confirm_image = $('confirm_image').select('img')[0];
		var url = confirm_image.src.split('?');
		confirm_image.src = url[0]+ '?dummy='+ Math.random();
	}
}

/**
 * Common initialized
 *@Author: kurokisi
 *@AuthDate: 2010.11.23
 */
var proc = {
	url: '.',
	proc: '/proc.ajax.php',
	method: 'post',
	parameters: function(params, form) {
		if(typeof(params)=='string' && params.blank()) params = {};
		this.params = (form==undefined) ? $H(params) : $H(params).merge(Form.serialize(form, true));
	},
	process: function(proc, debug, url) {
		var self = this;
		if(url==undefined) url = this.url;
		if(debug==true) alert('[url] '+ url + this.proc +'\n[parameters] '+ this.params.toQueryString());
		new Ajax.Request(url + this.proc, {
			method: this.method,
			parameters: this.params,
			onSuccess: function(trans) {
				if(debug==true) alert('[responseText] : '+ trans.responseText);
				if(typeof(proc)=='function') proc(trans);
				else if(!trans.responseText.blank()) self.proc.response(trans);
			},
			onFailure: function(trans) {
				alert('[failure-responseText] '+ trans.responseText);
			}
		});
	},
	response: function(trans) {
		if(trans.responseText.blank()) return;
		trans.responseText.match(/<script/i) ? trans.responseText.evalScripts() : alert(trans.responseText);
	},
	isXML: function(trans) { // 2011.12.20 added
		try { return (trans.responseXML.firstChild.nodeType < Node.DOCUMENT_NODE) }
		catch(e) { return false }
	}
}
// 선택
var checker = {
	initialize: function(spot, selector, top, bottom) {
		this.selector = selector;
		this.objects = {
			spot: $(spot),
			top: $(top==undefined ? 'checker_top' : top),
			bottom: $(bottom==undefined ? 'checker_bottom' : bottom),
			status: {'false': 'btn_select_all', 'true': 'btn_select_cancel'}
		}
	},
	all: function(all) { // 선택반전
		with(this.objects) {
			if(all==undefined) all = top.checked = !top.checked;
			if(bottom!=null) bottom.innerHTML = bottom.innerHTML.replace(eval('/'+status[!all]+'/'), status[all]);
		}
		$A($(this.objects.spot.parentNode).select(this.selector)).each(function(item) { if(item.disabled==false) item.checked = all });
	},
	get: function(extend) { // 선택 값 반환
		var items = [], objects = [];
		$A($(this.objects.spot.parentNode).select(this.selector)).each(function(item) {
			if(item.checked==true) {
				objects.push(item);
				items.push(item.value);
			}
		});
		return (extend==true) ? {items: items.join('__'), objects: objects} : items.uniq().join('__');
	}
}
// 레이어 위치
var position = {
	center: function(frame) { // frame: string
		this.set(frame, 'c', 'c');
	},
	set: function(frame, x, y) {
		frame = $(frame);
		Position.prepare();
		var dms = frame.getDimensions();
		var top = 0, left = 0, marginTop = 0, marginLeft = 0;
		switch(x) {
			case 'l': left = 0; marginLeft = Position.deltaX; break;
			case 'c': left = '50%'; marginLeft = Position.deltaX -(dms.width/2); break;
			case 'r': left = '100%'; marginLeft = Position.deltaX - dms.width; break;
		}
		switch(y) {
			case 't': top = 0; marginTop = Position.deltaY; break;
			case 'c': top = '50%'; marginTop = Position.deltaY - (dms.height/2); break;
			case 'b': top = '100%'; marginTop = Position.deltaY - dms.height; break;
		}
		frame.setStyle({ top: top, left: left, marginTop: marginTop +'px', marginLeft: marginLeft +'px' });
	}
}
// 블라인드
var blind = {
	frame: null,
	draw: function(opacity, zIndex) { // 그리기
		with(document) {
			this.frame = createElement('div');
			body.appendChild(this.frame);
			opacity = (opacity!=undefined) ? opacity : 50;
			$(this.frame).setStyle({
				position: 'absolute',
				backgroundColor: 'black',
				width: parseInt(body.scrollWidth),
				height: parseInt(body.clientHeight), //parseInt(body.scrollHeight),
				filter: 'alpha(opacity='+ opacity +')',
				opacity: (opacity/100),
				zIndex: (zIndex!=undefined) ? zIndex : 1,
				left: 0,
				top: 0
			});
			try {
				this.resizing();
				Event.observe(window, 'resize', this.resizing.bind(this));
			}
			catch(e) {
				//FF 3.6.13 error
				//alert(e.message);
			}
		}
	},
	resizing: function() { // 윈도우 크기 변경시 처리
		with(document) {
			var x1 = parseInt(body.offsetWidth);
			var x2 = parseInt(body.scrollWidth);
			var gap = (x1>x2) ? x1 - x2 : 0;
			var width = body.clientWidth;
			var height = body.clientHeight;
			$(this.frame).setStyle({ width: parseInt(body.offsetWidth) - gap +'px', height: parseInt(body.scrollHeight) +'px' });
		}
	},
	remove: function() { // 블라인드 제거
		Event.stopObserving(window, 'resize', this.resizing.bind(this));
		if(this.frame) this.frame.remove();
	}
}
// 폼 전송 - 2011.08.19 added
var $form = {
	hashes: {}, // optional
	handler: null, // function - optional
	debug: false, // boolean - debugging mode
	blind: false, // blind
	url: '.',
	waiting: '<div id="sending_box"><p>처리중</p><div>잠시만 기다려 주십시오</div></div>',
	submit: function(el, frame, confirmMsg) {
		if(typeof wysiwyg_Class == 'function') Wysiwyg.submit_start();
		if(!validate(Form.getElements(frame))) return false;
		if(confirmMsg!=undefined && !confirm(confirmMsg)) return false;
		if(el.nodeName.match(/form/i)) this.waiting = '';
		if(this.waiting) {
			var wrap = $(el).up(), content = wrap.innerHTML;
			wrap.update(this.waiting);
		}
		if(this.blind==true) blind.draw();
		var self = this;
		proc.parameters(this.hashes, frame);
		proc.process(function(trans) {
			if(typeof self.handler == 'function') self.handler(trans);
			if(self.blind==true) blind.remove();
			if(self.waiting) wrap.update(content);
		}, this.debug, this.url);
	}
}
/**
 * Escaper
 *@authDate: 2012.02.08
 *@usage: Escape.add('class.close()');
 *@usage: Escape.remove('class.close()'); // manual option!
 */
var $esc = {
	stacks: [],
	listening: false,
	add: function(handler) {
		if(!this.stacks.length) {
			Event.observe(document, 'keyup', this.listener);
			this.listening = true;
		}
		if(!this.stacks.include(handler)) this.stacks.push(handler);
	},
	remove: function(handler) {
		this.stacks = this.stacks.without(handler);
		this.kill();
	},
	listener: function(event) {
		if(event.keyCode!=Event.KEY_ESC) return;
		if($esc.stacks.length) { eval($esc.stacks.pop()), $esc.kill() }
	},
	kill: function() {
		if(!this.stacks.length && this.listening) {
			Event.stopObserving(document, 'keyup', this.listener);
			this.listening = false;
		}
	}
}

// 월의 마지막 날 반영
var draw_day = function(y, m, d) {
	y = $(y).value, m = parseInt($(m).value, 10), d = $(d), d.options.length = 1;
	if(!y || !m) return;

	var days = $w('_ 31 28 31 30 31 30 31 31 30 31 30 31');
	if((y%4)==0 && ((y%100)!=0 || (y%400)==0)) days[2] = 29; // 윤달
	$R(1, days[m]).each(function(day) { d.options[d.options.length] = new Option(day+'일', day.toPaddedString(2)) }); // draw
}

// 로그아웃
var logout = function() {
	proc.parameters({mode: 'logout'});
	proc.process(function(trans) { proc.response(trans) }, false, domain +'mypage');
}

// 메뉴 핸들러
var menu_handler = function(pid) {
	proc.parameters({ pid: pid });
	proc.process(function(trans) { proc.response(trans) }, false, domain +'design');
}

var init = {}
