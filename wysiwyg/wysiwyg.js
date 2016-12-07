/*
 * 랭크업 위지윅 v1.0
 */
var wysiwyg_Class = function () {
	var version = '1.0 r090508';
	var textareaName = new Array(); // type이 "editor"인 textarea 배열
	this.img_upload = new Array();
	this.img_upload_num = 0;
	this.frame_use = ''; // iframe name명
	this.img = ''; // 업로드한 이미지명.
	this.edit_mode = new Array(); // 편집 모드
	this.htmlid = '';
	this.state = true;

	this.intRowNum = 10;
	this.intColNum = 10;
	this.X = -1;
	this.Y = -1;
	this.arrTableRef = new Array();
	var viewSource = false;

	/*##############################################################################################################
	위지위그 에디터의 상대경로 설정
	##############################################################################################################*/
	var loca	= domain+"wysiwyg/"; // 설치된 폴더명
	this.link_click_state = function (e) {
		if(e.style.filter=="gray() alpha(opacity=50)") return false;
	}

	//함수
	this.htmledit = function (excute, bool, value, num) {
		if(viewSource == false) {
			document.getElementById('iframe'+textareaName[num]).contentWindow.focus();
			if(navigator.appName != "Microsoft Internet Explorer" && excute=="backcolor") excute = "hilitecolor";
			if(excute=="createlink") document.getElementById('iframe'+textareaName[num]).contentWindow.document.execCommand(excute, bool);
			else document.getElementById('iframe'+textareaName[num]).contentWindow.document.execCommand(excute, bool, value);
			document.getElementById('fontfaceLayer'+num).style.display = "none";
			document.getElementById('fontsizeLayer'+num).style.display = "none";
			document.getElementById('fgLayer'+num).style.display = "none";
			document.getElementById('bgLayer'+num).style.display = "none";
			this.copyTohtml();
		}
	}

	this.viewLayer = function (num, layerId, e, e2) {
		if(e2) this.state = this.link_click_state(e2);
		if(this.state==false) return false;
		document.getElementById('fontfaceLayer'+num).style.display = "none";
		document.getElementById('fontsizeLayer'+num).style.display = "none";
		document.getElementById('fgLayer'+num).style.display = "none";
		document.getElementById('bgLayer'+num).style.display = "none";
		document.getElementById('viewPyo'+num).style.display = "none";
		if(layerId) {
			var obj = document.getElementById(layerId);
			obj.style.display = "block";
			//IE 일때와 FF일때를 구분한다.
			if(browser.kind =="IE") {
				obj.style.left =  event.x + "px";
			} else {
				var obj_button = e2;
				while(!obj_button.id.match(/toolbox/i)) obj_button = obj_button.parentNode;
				var positon = 	parseInt(obj_button.offsetWidth) - parseInt(obj_button.offsetWidth - obj.offsetWidth)
				obj.style.marginLeft = positon +"px";
			}
			var layer_word = layerId.substring(0,7);
			if(layer_word=='viewPyo') Wysiwyg.tablepyo_func2(num);
		}
	}

	/*##############################################################################################################
	이미지 업로드
	##############################################################################################################*/
	this.use_image = function (elmId, elmName, num, e) {
		if(e) this.state = this.link_click_state(e);
		if(this.state==false) return false;
		this.frame_use = eval(elmId);
		if(window.attachEvent) {
			this.img_upload[this.img_upload_num] = showModalDialog(loca+'img_add.html?iname='+elmId+'&num='+num, window, 'resizable: yes; help: no; status: no; scroll: no; dialogWidth:650px;dialogHeight:300px;');
			if(this.img_upload[this.img_upload_num]) {
				this.frame_use.focus();
				this.frame_use.document.execCommand('InsertImage', false, loca+'PEG_temp/'+this.img_upload[this.img_upload_num]);//부모창에 이미지 넣기
				this.img_upload_num++;
			}
		}
		else if(window.addEventListener) {
			window.open(loca+'img_add.html?iname='+elmId+'&num='+num+'&img_num='+this.img_upload_num, window, 'width=650,height=300,resizable=yes, help=no, status=no, scroll=no;');
		}
	}

	/*##############################################################################################################
	테이블 표 생성하기에서 onMouseOver부분
	##############################################################################################################*/
	this.tableTag_create_over = function (num, iX, iY) {
		var tdim = document.getElementById('tdim'+num);
		tdim.innerText = (iX + 1) + " × " + (iY + 1);
		var use_X = this.X;
		var use_Y = this.Y;
		var arrTableRef = this.arrTableRef;
		for(var i=use_X; i<=iX; i++) for(var j=0; j<=use_Y; j++)			arrTableRef[num][i][j].bgColor = "#D6E3F1";
		for(var j=use_Y+1; j<=iY; j++) for(var i=0; i<=use_X; i++)		arrTableRef[num][i][j].bgColor = "#D6E3F1";
		for(var i=use_X+1; i<=iX; i++) for(var j=use_Y+1; j<=iY; j++)	arrTableRef[num][i][j].bgColor = "#D6E3F1";
		for(var i=iX+1; i<=use_X; i++) for(var j=0; j<=iY; j++)			arrTableRef[num][i][j].bgColor = "#F7F7F7";
		for(var j=iY+1; j<=use_Y; j++) for (var i=0; i<=iX; i++)			arrTableRef[num][i][j].bgColor = "#F7F7F7";
		for(var i=iX+1; i<=use_X; i++) for(var j=iY+1; j<=use_Y; j++)	arrTableRef[num][i][j].bgColor = "#F7F7F7";
		this.X = iX;
		this.Y = iY;
	}

	/*##############################################################################################################
	테이블 표 생성하기에서 onMouseOut부분
	##############################################################################################################*/
	this.tableTag_create_out = function (num) {
		var tds = new String();
		var trs = new String();
		var width_num = Math.floor(100 / (this.X+1));

		for(var i=0; i<(this.X+1); i++) tds += "<td></td>"; //  width='"+width_num+"%'  width 값 제거 - 2008.06.02
		for(var i=0; i<(this.Y+1); i++) trs += "<tr>"+tds+"</tr>";
		// width, height 값 pixel 값으로 지정 - 2008.06.02
		var content = document.getElementById('iframe'+textareaName[num]).contentWindow.document.body;
		if(content.innerHTML=="<P>&nbsp;</P>") content.innerHTML = '';
		var table = "<table style='{:size:}' cellpadding=4 cellspacing=1 border=0 bgcolor=#dedede><tbody bgcolor=white>"+trs+"</tbody></table>";

		//var doc = document.getElementById('iframe'+textareaName[num]).contentWindow.document;
		document.getElementById('viewPyo'+num).style.display = 'none';
		this.frame_use = eval('iframe'+textareaName[num]);
		this.frame_use.focus();

		// 공백뒤에 테이블이 생성되어 줄이 바뀌는 현상 픽스 - 2008.06.02
		if(browser.kind =="IE") {
			this.frame_use.document.execCommand('InsertInputHidden', false, "table"); // 커서 위치에 테이블을 삽입하기 위한 꼼수!
		}
		if(content.innerHTML=='' || content.innerHTML=="<INPUT id=table type=hidden>") content.innerHTML = table.replace(/{:size:}/g, "width:99%;height:"+(this.Y+1)*24); //(content.offsetWidth-28)
		else content.innerHTML = content.innerHTML.replace(/<INPUT id=table type=hidden>/g, table.replace(/{:size:}/g, "width:99%;height:"+(this.Y+1)*24));
	}

	/*##############################################################################################################
	테이블 표 닫기
	##############################################################################################################*/
	this.view_pyo_onMouseOut = function (num) {
		document.getElementById('viewPyo'+num).style.display = 'none';
	}

	/*##############################################################################################################
	테이블 표 생성하기에서 테이블 표부분
	##############################################################################################################*/
	this.tablepyo_func = function (num) {
		var tag = new String();
		for(var row=0; row<this.intRowNum; row++) {
			for(var col=0, _tag=''; col<this.intColNum; col++) _tag += "<td width='10' height='10' id='Wysiwyg_id_"+num+"_"+col+"_"+row+"' onMouseOver=\"Wysiwyg.tableTag_create_over('"+num+"', "+col+", "+row+")\" onMouseDown=\"Wysiwyg.tableTag_create_out("+num+")\">　</td>";
			tag += "<tr>"+_tag+"</tr>";
		}
		return tag;
	}

	/*##############################################################################################################
	테이블 표 생성하기에서 테이블 표부분2
	##############################################################################################################*/
	this.tablepyo_func2 = function (num) {
		var arrTableRef = new Array();
		arrTableRef[num] = new Array();
		for(var i=0; i<this.intRowNum; i++) {
			arrTableRef[num][i] = new Array();
			for(var j=0; j<this.intColNum; j++) arrTableRef[num][i][j] = document.getElementById("Wysiwyg_id_"+num+"_"+i+"_"+j);
		}
		this.arrTableRef = arrTableRef;
	}

	/*##############################################################################################################
	ff일때는 이 함수 이용해서 이미지를 원하는 곳에 놓는다.
	##############################################################################################################*/
	this.ff_img_view		= function () {
		if(this.img) {
			this.frame_use.document.execCommand('InsertImage', false, loca+'PEG_temp/'+this.img);//부모창에 이미지 넣기
			this.img_upload[this.img_upload_num]	= this.img;
			this.img_upload_num++;
		}
	}

	/*##############################################################################################################
	뉴라인 제거 - 파폭/익스 공용 : editor 모드에서 text 모드로 전환시 이용 - 2008.03.20 추가 - 랭크업 개발팀
	##############################################################################################################*/
	this.nl2br_remove_content = function (iframe, textarea) {
		var content_string = document.getElementById(iframe).contentWindow.document.body.innerHTML;
		if(content_string.toLowerCase()=="&nbsp;<br>"||content_string.toLowerCase()=="<p>&nbsp;</p>"||content_string.toLowerCase()=="<br>") content_string = '';
		/* <P>태그 처리부분 제거 - 2009.05.08 fixed
		else { // <P> 태그 정리
			content_string = (document.all!=null) ? content_string.replace(/<P>\&nbsp;<\/P>\r\n/gi, '<BR>') : content_string.replace(/<P>\&nbsp;<\/P>\n/gi, '<BR>');
			content_string = content_string.replace(/<P>\&nbsp;<\/P>/gi, '');
			content_string = (document.all!=null) ? content_string.replace(/<\/P>\r\n/gi, '<BR>') : content_string.replace(/<\/P>\n/gi, '<BR>');
			content_string = content_string.replace(/<\/P>/gi, '').replace(/<P>/gi, '');
		}
		*/
		/* 테이블 처리부분 제거 - 2008.11.05 fixed
		var __zone = document.createElement("div"); // 임시존 생성
		__zone.id = "__zone_"+iframe;
		__zone.style.display = "none";
		__zone.innerHTML = content_string;
		document.body.appendChild(__zone);
		try {
			var zone = document.getElementById(__zone.id);
			var tables = zone.getElementsByTagName("table");
			for(var i=0; i<tables.length; i++) {
				var table = (document.all!=null) ? tables[i].parentNode.innerHTML.replace(/\r\n/g,'') : tables[i].parentNode.innerHTML.replace(/\n/g,'');
				tables[i].parentNode.innerHTML = table; //encodeURIComponent(table);
			}
			document.body.removeChild(__zone); // 임시존 제거
			content_string = decodeURIComponent(zone.innerHTML);
		}
		catch(e) {
			content_string = zone.innerHTML;
		} */
		// A 태그 머지기능 패치 - 2008.06.16 적용
		var atags = content_string.match(/(<a [^<]*href=["|']?([^ "']*)["|']?[^>])/gi);
		if(atags!=null) {
			try { var _domain = domain } catch(e) { var _domain = document.domain }
			for(var i=0; i<atags.length; i++) content_string = content_string.replace(atags[i], atags[i].replace(_domain+"rankup_module/rankup_banner/{:", "{:"));
		}
		document.getElementsByName(textarea)[0].value = content_string;
	}

	this.viewSource = function (elmId, elmName, num) {
		viewSource = true;
		var toolbox = document.getElementById('toolbox'+num);
		var tools = toolbox.getElementsByTagName('img');
		for(var i=0; i<tools.length; i++) {
			switch(tools[i].id) {
				case "viewSourceBtn": tools[i].style.display = "none"; break;
				case "viewEdirorBtn": tools[i].style.display = "inline"; break;
				default:
					tools[i].style.filter = "gray() alpha(opacity=50)";
					tools[i].style.cursor = "default";
			}
		}
		document.getElementById(elmId).style.display = "none";
		document.getElementsByName(elmName)[0].style.display = "inline";
		// 추가 2008.03.25 - 랭크업 개발팀
		this.nl2br_remove_content(elmId, elmName);
		document.getElementsByName(elmName)[0].focus();
	}

	this.viewEdiror = function (elmId,elmName, num) {
		viewSource = false;
		var toolbox = document.getElementById('toolbox'+num);
		var tools = toolbox.getElementsByTagName('img');
		for(var i=0; i<tools.length; i++) {
			switch(tools[i].id) {
				case "viewEdirorBtn": tools[i].style.display = "none"; break;
				case "viewSourceBtn": tools[i].style.display = "inline"; break;
				default:
					tools[i].style.filter = "";
					tools[i].style.cursor = "pointer";
			}
		}
		document.getElementById(elmId).style.display = "inline";
		document.getElementsByName(elmName)[0].style.display = "none";
		document.getElementById(elmId).contentWindow.document.body.innerHTML = document.getElementsByName(elmName)[0].value;
	}

	this.copyTohtml = function () {
		for (var i=0;i<textareaName.length;i++) {
			document.getElementsByName(textareaName[i])[0].value = document.getElementById("iframe"+textareaName[i]).contentWindow.document.body.innerHTML;
		}
	}

	this.copyToeditor = function () {
		for (var i=0;i<textareaName.length;i++) {
			document.getElementById("iframe"+textareaName[i]).contentWindow.document.body.innerHTML = document.getElementsByName(textareaName[i])[0].value;
		}
	}

	//추가옵션 이벤트 - 사용법 event="onclick|blank"
	this.change_event = function(doc, textarea, eventObj) {
		switch(eventObj[1]) {
			//본문을 처음에 클릭시에는 지워주기 : 다음에 클릭시에는 안지워집니다.
			case "blank": setTimeout(function() { eval("doc.body."+eventObj[0]+" = function() { doc.body.innerHTML = ''; doc.body."+eventObj[0]+" = function() { return false; }}") }, 0); break;
		}
	}

	this.createELM = function () {
		var textareaObj = document.getElementsByTagName('textarea');
		for (var i=0, c=0; i<textareaObj.length; i++) {
			if(textareaObj[i].getAttribute("type")=="editor") {
				textareaName[c] = textareaObj[i].getAttribute("name");
				c++;
			}
		}
		for(var i=0;i<textareaName.length;i++) {
			var textareaOrg = document.getElementsByName(textareaName[i])[0];
			var tbl_width_num = (textareaOrg.style.width) ? textareaOrg.style.width : 530;

			// Percentage('%') 사용할 수 있도록 수정
			var tbl_width = (String(tbl_width_num).indexOf("%")!=-1) ? tbl_width_num : (parseInt(tbl_width_num) - 0) +'px';
			textareaOrg.style.width = tbl_width_num;

			// 아이프레임을 감싸는 테이블생성
			var editor_box = document.createElement('table');
			if(document.all) {
				var editor_box_tr = editor_box.insertRow();
				var editor_box_td = editor_box_tr.insertCell();
			}
			else {
				var editor_box_tr = document.createElement('tr');
				var editor_box_td = document.createElement('td');
			}
			editor_box.setAttribute('cellSpacing', 0);
			editor_box.setAttribute('cellPadding', 0);
			editor_box.width = tbl_width_num;
			if(!document.all) {
				editor_box_tr.appendChild(editor_box_td);
				editor_box.appendChild(editor_box_tr);
			}
			textareaOrg.parentNode.insertBefore(editor_box,textareaOrg);

			// 아이프레임생성
			var editorFrameObj = document.createElement('iframe');
			editorFrameObj.setAttribute('id','iframe'+textareaName[i]);
			editorFrameObj.setAttribute('name','iframe'+textareaName[i]);
			editorFrameObj.setAttribute('scrolling','yes');
			editorFrameObj.setAttribute('frameBorder','no');
			editorFrameObj.setAttribute('wrap','virtual');
			editorFrameObj.style.width = tbl_width;
			editorFrameObj.style.height = textareaOrg.style.height;
			editorFrameObj.style.border = '1px solid #D0D0D0';
			editorFrameObj.onmouseover = new Function("Wysiwyg.viewLayer('"+i+"');");
			editor_box_td.appendChild(editorFrameObj);

			var doc = document.getElementById('iframe'+textareaName[i]).contentWindow.document;
			doc.open();
			doc.write('<html><head>');
			doc.write('<link rel="stylesheet" type="text/css" href="'+ domain +'Libs/_style/rankup_style.css">');
			doc.write('<link rel="stylesheet" type="text/css" href="'+ domain +'design/skin/skin.css">');
			doc.write('<style type="text/css"> p, body {margin:0px} body {background:none!important;overflow:auto!important} * { font-family: "맑은 고딕", "돋움", "Segoe UI", sans-serif; }}</style>')
			doc.write('</head><body bgcolor="white">'+ textareaOrg.value +'</body></html>');
			doc.close();

			// 디자인모드
			if(document.getElementsByName(textareaName[i])[0].getAttribute('readonly')==true) doc.designMode = 'off';
			else doc.designMode = 'on';

			//추가 이벤트 - event값이 있을때 실행해줍니다.
			if(textareaOrg.getAttribute('event')) {
				var eventObj = textareaOrg.getAttribute('event').split('|');
				Wysiwyg.change_event(doc, textareaOrg, eventObj);
			}

			// textarea 스타일 픽스
			textareaOrg.style.backgroundColor = '#ffffff';
			//textareaOrg.style.color = '#336699';
			//textareaOrg.style.fontSize = '11px';
			textareaOrg.style.fontFamily = 'dotum';
			textareaOrg.style.margin = '-1px 0';
			textareaOrg.style.padding = '1px 0';
			//textareaOrg.style.height = textareaOrg.offsetHeight + 2 +'px';
			textareaOrg.style.border = '#D0D0D0 1px solid';
			textareaOrg.style.display = 'none';

			// 이벤트 처리(내용 상호복제 부분)
			if(document.addEventListener) {
				doc.addEventListener('mousedown', Wysiwyg.copyTohtml, false);
				doc.addEventListener('blur', Wysiwyg.copyTohtml, false);
				textareaOrg.addEventListener('mousedown', Wysiwyg.copyToeditor, false);
				textareaOrg.addEventListener('blur', Wysiwyg.copyToeditor, false);
			}
			else if(document.attachEvent) {
				doc.attachEvent('onmousedown', Wysiwyg.copyTohtml, false);
				document.getElementById('iframe'+textareaName[i]).attachEvent('onblur', Wysiwyg.copyTohtml, false);
				textareaOrg.attachEvent('onmousedown', Wysiwyg.copyToeditor, false);
				textareaOrg.attachEvent('onblur', Wysiwyg.copyToeditor, false);
			}

			// 도구테이블생성 - 툴바 수정 - 2008.04.22 - 랭크업 개발팀
			var ctrlObj  = "\
			<div id='toolbox"+i+"' style='position:relative;width:"+tbl_width_num+";margin:0px;padding:0px;border-left:1px solid #D0D0D0;border-top:1px solid #D0D0D0;border-right:1px solid #D0D0D0;'>\
				<ul style='margin:0px;list-style:none;padding:0'>\
					<li id='toolbar'>";
						ctrlObj += "<img id='wysiwyg_id[]' src='"+loca+"editor_images/ed_fontface.gif' border='0' alt='글자체' onClick=\"Wysiwyg.viewLayer('"+i+"', 'fontfaceLayer"+i+"', arguments[0], this)\">";
						ctrlObj += "<img id='wysiwyg_id[]' src='"+loca+"editor_images/ed_fontsize.gif' border='0' alt='글자크기' onClick=\"Wysiwyg.viewLayer('"+i+"', 'fontsizeLayer"+i+"', arguments[0], this)\">";
						ctrlObj += "<img src='"+loca+"editor_images/ed_virtical.gif'> ";
						ctrlObj += "<img id='wysiwyg_id[]' src='"+loca+"editor_images/ed_format_bold.gif' border='0' alt='볼드체' onClick=\"Wysiwyg.htmledit('bold', false, null, '"+i+"')\">";
						ctrlObj += "<img id='wysiwyg_id[]' src='"+loca+"editor_images/ed_format_italic.gif' border='0' alt='기울림체' onClick=\"Wysiwyg.htmledit('italic', false, null, '"+i+"')\">";
						ctrlObj += "<img id='wysiwyg_id[]' src='"+loca+"editor_images/ed_format_underline.gif' border='0' alt='밑줄' onClick=\"Wysiwyg.htmledit('underline', false, null, '"+i+"')\">";
						ctrlObj += "<img id='wysiwyg_id[]' src='"+loca+"editor_images/ed_format_strike.gif' border='0' alt='취소선' onClick=\"Wysiwyg.htmledit('StrikeThrough', false, null, '"+i+"')\">";
						ctrlObj += "<img id='wysiwyg_id[]' src='"+loca+"editor_images/ed_color_fg.gif' border='0' alt='글자색상' onClick=\"Wysiwyg.viewLayer('"+i+"', 'fgLayer"+i+"', arguments[0], this)\">";
						ctrlObj += "<img id='wysiwyg_id[]' src='"+loca+"editor_images/ed_color_bg.gif' border='0' alt='배경색상' onClick=\"Wysiwyg.viewLayer('"+i+"', 'bgLayer"+i+"', arguments[0], this)\">";
						ctrlObj += "<img src='"+loca+"editor_images/ed_virtical.gif'> ";
						ctrlObj += "<img id='wysiwyg_id[]' src='"+loca+"editor_images/ed_align_left.gif' border='0' alt='좌측정렬' onClick=\"Wysiwyg.htmledit('justifyleft', false, null, '"+i+"')\">";
						ctrlObj += "<img id='wysiwyg_id[]' src='"+loca+"editor_images/ed_align_center.gif' border='0' alt='중심정렬' onClick=\"Wysiwyg.htmledit('justifycenter', false, null, '"+i+"')\">";
						ctrlObj += "<img id='wysiwyg_id[]' src='"+loca+"editor_images/ed_align_right.gif' border='0' alt='우측정렬' onClick=\"Wysiwyg.htmledit('justifyright', false, null, '"+i+"')\">";
						ctrlObj += "<img src='"+loca+"editor_images/ed_virtical.gif'> ";
						ctrlObj += "<img id='wysiwyg_id[]' src='"+loca+"editor_images/ed_list_number.gif' border='0' alt='숫자목록' onClick=\"Wysiwyg.htmledit('insertorderedlist', false, null, '"+i+"')\">";
						ctrlObj += "<img id='wysiwyg_id[]' src='"+loca+"editor_images/ed_list_dot.gif' border='0' alt='점표시목록' onClick=\"Wysiwyg.htmledit('insertunorderedlist', false, null, '"+i+"')\">";
						ctrlObj += "<img id='wysiwyg_id[]' src='"+loca+"editor_images/ed_format_outdent.gif' border='0' alt='들여쓰기줄이기' onClick=\"Wysiwyg.htmledit('outdent', false, null, '"+i+"')\">";
						ctrlObj += "<img id='wysiwyg_id[]' src='"+loca+"editor_images/ed_format_indent.gif' border='0' alt='들여쓰기늘이기' onClick=\"Wysiwyg.htmledit('indent', false, null, '"+i+"')\">";
						ctrlObj += "<img src='"+loca+"editor_images/ed_virtical.gif'> ";
						ctrlObj += "<img id='wysiwyg_id[]' src='"+loca+"editor_images/ed_hr.gif' border='0' alt='한줄넣기' onClick=\"Wysiwyg.htmledit('InsertHorizontalRule', false, null, '"+i+"')\">";
						ctrlObj += "<img id='wysiwyg_id[]' src='"+loca+"editor_images/linkClick.gif' border='0' alt='링크' onClick=\"Wysiwyg.htmledit('createlink', false, '', '"+i+"')\">";
						ctrlObj += "<img src='"+loca+"editor_images/ed_virtical.gif'> ";
						ctrlObj += "<img id='wysiwyg_id[]' id='pyo' src='"+loca+"editor_images/icon_tableimg.gif' alt='표만들기' onClick=\"Wysiwyg.viewLayer('"+i+"', 'viewPyo"+i+"', arguments[0], this)\" border=0>";
						ctrlObj += "<img src='"+loca+"editor_images/ed_virtical.gif'> ";
						if(textareaOrg.getAttribute('nonimage')==null) { // 2010.07.08 added
							ctrlObj += "<img id='wysiwyg_id[]' id='viewSourceBtn"+i+"' src='"+loca+"editor_images/icon_img.gif' border='0' alt='이미지 업로드' style='display:inline' onClick=\"Wysiwyg.use_image('iframe"+textareaName[i]+"', '"+textareaName[i]+"', '"+i+"', this)\">";
							ctrlObj += "<img src='"+loca+"editor_images/ed_virtical.gif'> ";
						}
						ctrlObj += "<img id='viewEdirorBtn' src='"+loca+"editor_images/ed_editor.gif' border='0' alt='편집화면' style='display:none;' onClick=\"Wysiwyg.viewEdiror('iframe"+textareaName[i]+"', '"+textareaName[i]+"', '"+i+"')\">";
						ctrlObj += "<img id='viewSourceBtn' src='"+loca+"editor_images/ed_html.gif' border='0' alt='소스보기' onClick=\"Wysiwyg.viewSource('iframe"+textareaName[i]+"', '"+textareaName[i]+"', '"+i+"')\">";
						ctrlObj += "</li>\
			<style type='text/css'>\
				#toolbar {padding:3px;}\
				#toolbar img {margin:2px 1px 2px 1px;}\
				#toolbox td {cursor:pointer;}\
				#fontfaceLayer"+i+" td {cursor:pointer;padding-top:2px;font-size:8pt;font-family:dotum;}\
				#fontsizeLayer"+i+" td {cursor:pointer;padding-top:2px;font-size:8pt;font-family:dotum;}\
				#fgLayer"+i+" td {cursor:pointer;}\
				#bgLayer"+i+" td {cursor:pointer;}\
			</style>\
			<div id='viewPyo"+i+"' style='position:absolute;width:200px;border:1px solid #777777;background-color:white;padding:4px;display:none;'>\
				<table id='viewPyo"+i+"' width='100%' border='0' cellpadding='0' cellspacing='1' bgcolor='#acacac'>\
				<tbody bgcolor='#F7F7F7'>";
					ctrlObj  += Wysiwyg.tablepyo_func(i);
					ctrlObj  += "\
				</tbody>\
				</table>\
				<div id='tdim"+i+"' style='width:100%;text-align:center;'></div>\
			</div>\
			<div id='fontfaceLayer"+i+"' style='position:absolute;width:70px; border:1px solid #999999; display:none;'>\
			 	<table width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#ffffff'>\
			 	<tr onmouseover=\"this.style.backgroundColor='#E0E0E0'\" onmouseout=\"this.style.backgroundColor=''\"><td style='padding-left:3px;' onmousedown=\"Wysiwyg.htmledit('fontname', false, '돋움', '"+i+"')\">돋움</td></tr>\
			 	<tr onmouseover=\"this.style.backgroundColor='#E0E0E0'\" onmouseout=\"this.style.backgroundColor=''\"><td style='padding-left:3px;' onmousedown=\"Wysiwyg.htmledit('fontname', false, '굴림', '"+i+"')\">굴림</td></tr>\
			 	<tr onmouseover=\"this.style.backgroundColor='#E0E0E0'\" onmouseout=\"this.style.backgroundColor=''\"><td style='padding-left:3px;' onmousedown=\"Wysiwyg.htmledit('fontname', false, '바탕', '"+i+"')\">바탕</td></tr>\
			 	<tr onmouseover=\"this.style.backgroundColor='#E0E0E0'\" onmouseout=\"this.style.backgroundColor=''\"><td style='padding-left:3px;' onmousedown=\"Wysiwyg.htmledit('fontname', false, '궁서', '"+i+"')\">궁서</td></tr>\
			 	<tr onmouseover=\"this.style.backgroundColor='#E0E0E0'\" onmouseout=\"this.style.backgroundColor=''\"><td style='padding-left:3px;' onmousedown=\"Wysiwyg.htmledit('fontname', false, 'Arial', '"+i+"')\">Arial</td></tr>\
			 	<tr onmouseover=\"this.style.backgroundColor='#E0E0E0'\" onmouseout=\"this.style.backgroundColor=''\"><td style='padding-left:3px;' onmousedown=\"Wysiwyg.htmledit('fontname', false, 'Tahoma', '"+i+"')\">Tahoma</td></tr>\
			 	<tr onmouseover=\"this.style.backgroundColor='#E0E0E0'\" onmouseout=\"this.style.backgroundColor=''\"><td style='padding-left:3px;' onmousedown=\"Wysiwyg.htmledit('fontname', false, 'Verdana', '"+i+"')\">Verdana</td></tr>\
			 	<tr onmouseover=\"this.style.backgroundColor='#E0E0E0'\" onmouseout=\"this.style.backgroundColor=''\"><td style='padding-left:3px;' onmousedown=\"Wysiwyg.htmledit('fontname', false, 'Time', '"+i+"')\">Time</td></tr>\
			 	</table>\
			</div>\
			<div id='fontsizeLayer"+i+"' style='position:absolute;width:50px; border:1px solid #999999; display:none;'>\
			 	<table width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#ffffff'>\
			 	<tr onmouseover=\"this.style.backgroundColor='#E0E0E0'\" onmouseout=\"this.style.backgroundColor=''\"><td style='padding-left:3px;' onmousedown=\"Wysiwyg.htmledit('fontsize', false, 1, '"+i+"')\"><b>1</b> (8pt)</td></tr>\
				<tr onmouseover=\"this.style.backgroundColor='#E0E0E0'\" onmouseout=\"this.style.backgroundColor=''\"><td style='padding-left:3px;' onmousedown=\"Wysiwyg.htmledit('fontsize', false, 2, '"+i+"')\"><b>2</b> (10pt)</td></tr>\
				<tr onmouseover=\"this.style.backgroundColor='#E0E0E0'\" onmouseout=\"this.style.backgroundColor=''\"><td style='padding-left:3px;' onmousedown=\"Wysiwyg.htmledit('fontsize', false, 3, '"+i+"')\"><b>3</b> (12pt)</td></tr>\
				<tr onmouseover=\"this.style.backgroundColor='#E0E0E0'\" onmouseout=\"this.style.backgroundColor=''\"><td style='padding-left:3px;' onmousedown=\"Wysiwyg.htmledit('fontsize', false, 4, '"+i+"')\"><b>4</b> (14pt)</td></tr>\
				<tr onmouseover=\"this.style.backgroundColor='#E0E0E0'\" onmouseout=\"this.style.backgroundColor=''\"><td style='padding-left:3px;' onmousedown=\"Wysiwyg.htmledit('fontsize', false, 5, '"+i+"')\"><b>5</b> (18pt)</td></tr>\
				<tr onmouseover=\"this.style.backgroundColor='#E0E0E0'\" onmouseout=\"this.style.backgroundColor=''\"><td style='padding-left:3px;' onmousedown=\"Wysiwyg.htmledit('fontsize', false, 6, '"+i+"')\"><b>6</b> (24pt)</td></tr>\
				<tr onmouseover=\"this.style.backgroundColor='#E0E0E0'\" onmouseout=\"this.style.backgroundColor=''\"><td style='padding-left:3px;' onmousedown=\"Wysiwyg.htmledit('fontsize', false, 7, '"+i+"')\"><b>7</b> (36pt)</td></tr>\
				</table>\
			</div>\
			<div id='fgLayer"+i+"' style='position:absolute;width:160px; height:75px; border:1px solid #999999; display:none;'>\
				<table width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#ffffff'>\
				<tr>\
					<td>\
						<table cellspacing='0' cellpadding='0' border='1' bordercolor='#000000' style='border-collapse:collapse; margin:7px;'>\
						<tr height='11'>\
							<td width='11' bgcolor='#FE1100' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#FE1100', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FE4C24' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#FE4C24', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FE875A' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#FE875A', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FECDA7' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#FECDA7', '"+i+"')\"></td>\
							<td width='11' bgcolor='#040967' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#040967', '"+i+"')\"></td>\
							<td width='11' bgcolor='#2D328D' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#2D328D', '"+i+"')\"></td>\
							<td width='11' bgcolor='#44499A' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#44499A', '"+i+"')\"></td>\
							<td width='11' bgcolor='#686EB8' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#686EB8', '"+i+"')\"></td>\
							<td width='11' bgcolor='#669900' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#669900', '"+i+"')\"></td>\
							<td width='11' bgcolor='#66CC00' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#66CC00', '"+i+"')\"></td>\
							<td width='11' bgcolor='#99FF00' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#99FF00', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FF99FF' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#FF99FF', '"+i+"')\"></td>\
						</tr>\
						<tr height='11'>\
							<td width='11' bgcolor='#6E0017' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#6E0017', '"+i+"')\"></td>\
							<td width='11' bgcolor='#7B243D' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#7B243D', '"+i+"')\"></td>\
							<td width='11' bgcolor='#834C6B' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#834C6B', '"+i+"')\"></td>\
							<td width='11' bgcolor='#66FFFF' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#66FFFF', '"+i+"')\"></td>\
							<td width='11' bgcolor='#006BD4' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#006BD4', '"+i+"')\"></td>\
							<td width='11' bgcolor='#0087E1' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#0087E1', '"+i+"')\"></td>\
							<td width='11' bgcolor='#37B7FE' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#37B7FE', '"+i+"')\"></td>\
							<td width='11' bgcolor='#A7DEFE' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#A7DEFE', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FFCC00' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#FFCC00', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FFFF00' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#FFFF00', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FEFE9F' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#FEFE9F', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FEFED0' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#FEFED0', '"+i+"')\"></td>\
						</tr>\
						<tr height='11'>\
							<td width='11' bgcolor='#4E003D' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#4E003D', '"+i+"')\"></td>\
							<td width='11' bgcolor='#6D2262' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#6D2262', '"+i+"')\"></td>\
							<td width='11' bgcolor='#926594' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#926594', '"+i+"')\"></td>\
							<td width='11' bgcolor='#C2A9C5' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#C2A9C5', '"+i+"')\"></td>\
							<td width='11' bgcolor='#005557' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#005557', '"+i+"')\"></td>\
							<td width='11' bgcolor='#03747B' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#03747B', '"+i+"')\"></td>\
							<td width='11' bgcolor='#579D9F' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#579D9F', '"+i+"')\"></td>\
							<td width='11' bgcolor='#A2C6CC' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#A2C6CC', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FF6600' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#FF6600', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FF9933' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#FF9933', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FECD8A' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#FECD8A', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FEE2B0' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#FEE2B0', '"+i+"')\"></td>\
						</tr>\
						<tr height='11'>\
							<td width='11' bgcolor='#1B0B73' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#1B0B73', '"+i+"')\"></td>\
							<td width='11' bgcolor='#4C379D' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#4C379D', '"+i+"')\"></td>\
							<td width='11' bgcolor='#876EBA' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#876EBA', '"+i+"')\"></td>\
							<td width='11' bgcolor='#BBBAEF' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#BBBAEF', '"+i+"')\"></td>\
							<td width='11' bgcolor='#008E37' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#008E37', '"+i+"')\"></td>\
							<td width='11' bgcolor='#26B168' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#26B168', '"+i+"')\"></td>\
							<td width='11' bgcolor='#47BE80' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#47BE80', '"+i+"')\"></td>\
							<td width='11' bgcolor='#76D3A2' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#76D3A2', '"+i+"')\"></td>\
							<td width='11' bgcolor='#B31C00' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#B31C00', '"+i+"')\"></td>\
							<td width='11' bgcolor='#B03F21' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#B03F21', '"+i+"')\"></td>\
							<td width='11' bgcolor='#AE623A' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#AE623A', '"+i+"')\"></td>\
							<td width='11' bgcolor='#AC6E54' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#AC6E54', '"+i+"')\"></td>\
						</tr>\
						<tr height='11'>\
							<td width='11' bgcolor='#FEFEFE' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#FEFEFE', '"+i+"')\"></td>\
							<td width='11' bgcolor='#E6E6E6' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#E6E6E6', '"+i+"')\"></td>\
							<td width='11' bgcolor='#CDCDCD' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#CDCDCD', '"+i+"')\"></td>\
							<td width='11' bgcolor='#B4B4B4' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#B4B4B4', '"+i+"')\"></td>\
							<td width='11' bgcolor='#A8A8A8' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#A8A8A8', '"+i+"')\"></td>\
							<td width='11' bgcolor='#8D8D8D' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#8D8D8D', '"+i+"')\"></td>\
							<td width='11' bgcolor='#747474' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#747474', '"+i+"')\"></td>\
							<td width='11' bgcolor='#595959' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#595959', '"+i+"')\"></td>\
							<td width='11' bgcolor='#4B4B4B' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#4B4B4B', '"+i+"')\"></td>\
							<td width='11' bgcolor='#303030' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#303030', '"+i+"')\"></td>\
							<td width='11' bgcolor='#0A0A0A' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#0A0A0A', '"+i+"')\"></td>\
							<td width='11' bgcolor='#000000' onmousedown=\"Wysiwyg.htmledit('forecolor', false, '#000000', '"+i+"')\"></td>\
						</tr>\
						</table>\
					</td>\
				</tr>\
				</table>\
			</div>\
			<div id='bgLayer"+i+"' style='position:absolute;width:160px; height:75px; border:1px solid #999999; display:none;'>\
				<table width='100%' cellspacing='0' cellpadding='0' border='0' bgcolor='#ffffff'>\
				<tr>\
					<td>\
						<table cellspacing='0' cellpadding='0' border='1' bordercolor='#000000' style='border-collapse:collapse; margin:7px;'>\
						<tr height='11'>\
							<td width='11' bgcolor='#FE1100' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#FE1100', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FE4C24' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#FE4C24', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FE875A' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#FE875A', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FECDA7' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#FECDA7', '"+i+"')\"></td>\
							<td width='11' bgcolor='#040967' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#040967', '"+i+"')\"></td>\
							<td width='11' bgcolor='#2D328D' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#2D328D', '"+i+"')\"></td>\
							<td width='11' bgcolor='#44499A' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#44499A', '"+i+"')\"></td>\
							<td width='11' bgcolor='#686EB8' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#686EB8', '"+i+"')\"></td>\
							<td width='11' bgcolor='#669900' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#669900', '"+i+"')\"></td>\
							<td width='11' bgcolor='#66CC00' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#66CC00', '"+i+"')\"></td>\
							<td width='11' bgcolor='#99FF00' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#99FF00', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FF99FF' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#FF99FF', '"+i+"')\"></td>\
						</tr>\
						<tr height='11'>\
							<td width='11' bgcolor='#6E0017' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#6E0017', '"+i+"')\"></td>\
							<td width='11' bgcolor='#7B243D' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#7B243D', '"+i+"')\"></td>\
							<td width='11' bgcolor='#834C6B' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#834C6B', '"+i+"')\"></td>\
							<td width='11' bgcolor='#66FFFF' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#66FFFF', '"+i+"')\"></td>\
							<td width='11' bgcolor='#006BD4' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#006BD4', '"+i+"')\"></td>\
							<td width='11' bgcolor='#0087E1' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#0087E1', '"+i+"')\"></td>\
							<td width='11' bgcolor='#37B7FE' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#37B7FE', '"+i+"')\"></td>\
							<td width='11' bgcolor='#A7DEFE' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#A7DEFE', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FFCC00' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#FFCC00', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FFFF00' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#FFFF00', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FEFE9F' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#FEFE9F', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FEFED0' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#FEFED0', '"+i+"')\"></td>\
						</tr>\
						<tr height='11'>\
							<td width='11' bgcolor='#4E003D' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#4E003D', '"+i+"')\"></td>\
							<td width='11' bgcolor='#6D2262' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#6D2262', '"+i+"')\"></td>\
							<td width='11' bgcolor='#926594' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#926594', '"+i+"')\"></td>\
							<td width='11' bgcolor='#C2A9C5' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#C2A9C5', '"+i+"')\"></td>\
							<td width='11' bgcolor='#005557' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#005557', '"+i+"')\"></td>\
							<td width='11' bgcolor='#03747B' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#03747B', '"+i+"')\"></td>\
							<td width='11' bgcolor='#579D9F' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#579D9F', '"+i+"')\"></td>\
							<td width='11' bgcolor='#A2C6CC' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#A2C6CC', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FF6600' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#FF6600', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FF9933' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#FF9933', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FECD8A' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#FECD8A', '"+i+"')\"></td>\
							<td width='11' bgcolor='#FEE2B0' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#FEE2B0', '"+i+"')\"></td>\
						</tr>\
						<tr height='11'>\
							<td width='11' bgcolor='#1B0B73' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#1B0B73', '"+i+"')\"></td>\
							<td width='11' bgcolor='#4C379D' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#4C379D', '"+i+"')\"></td>\
							<td width='11' bgcolor='#876EBA' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#876EBA', '"+i+"')\"></td>\
							<td width='11' bgcolor='#BBBAEF' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#BBBAEF', '"+i+"')\"></td>\
							<td width='11' bgcolor='#008E37' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#008E37', '"+i+"')\"></td>\
							<td width='11' bgcolor='#26B168' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#26B168', '"+i+"')\"></td>\
							<td width='11' bgcolor='#47BE80' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#47BE80', '"+i+"')\"></td>\
							<td width='11' bgcolor='#76D3A2' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#76D3A2', '"+i+"')\"></td>\
							<td width='11' bgcolor='#B31C00' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#B31C00', '"+i+"')\"></td>\
							<td width='11' bgcolor='#B03F21' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#B03F21', '"+i+"')\"></td>\
							<td width='11' bgcolor='#AE623A' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#AE623A', '"+i+"')\"></td>\
							<td width='11' bgcolor='#AC6E54' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#AC6E54', '"+i+"')\"></td>\
						</tr>\
						<tr height='11'>\
							<td width='11' bgcolor='#FEFEFE' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#FEFEFE', '"+i+"')\"></td>\
							<td width='11' bgcolor='#E6E6E6' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#E6E6E6', '"+i+"')\"></td>\
							<td width='11' bgcolor='#CDCDCD' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#CDCDCD', '"+i+"')\"></td>\
							<td width='11' bgcolor='#B4B4B4' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#B4B4B4', '"+i+"')\"></td>\
							<td width='11' bgcolor='#A8A8A8' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#A8A8A8', '"+i+"')\"></td>\
							<td width='11' bgcolor='#8D8D8D' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#8D8D8D', '"+i+"')\"></td>\
							<td width='11' bgcolor='#747474' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#747474', '"+i+"')\"></td>\
							<td width='11' bgcolor='#595959' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#595959', '"+i+"')\"></td>\
							<td width='11' bgcolor='#4B4B4B' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#4B4B4B', '"+i+"')\"></td>\
							<td width='11' bgcolor='#303030' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#303030', '"+i+"')\"></td>\
							<td width='11' bgcolor='#0A0A0A' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#0A0A0A', '"+i+"')\"></td>\
							<td width='11' bgcolor='#000000' onmousedown=\"Wysiwyg.htmledit('backcolor', false, '#000000', '"+i+"')\"></td>\
						</tr>\
						</table>\
					</td>\
				</tr>\
				</table>\
			</div>\
			</ul>\
			</div>";

			var tableObj = document.createElement("table");
			if(document.all) {
				var trObj = tableObj.insertRow();
				var tdObj = trObj.insertCell();
			}
			else {
				var trObj = document.createElement('tr');
				var tdObj = document.createElement('td');
			}
			tableObj.setAttribute("cellSpacing","0");
			tableObj.setAttribute("cellPadding","0");
			tableObj.setAttribute("border","0");
			tableObj.setAttribute("bgColor", "#EFECE3");
			tableObj.width=tbl_width_num;
			tableObj.setAttribute("id","td"+textareaName[i]);
			if(!document.all) {
				trObj.appendChild(tdObj);
				tableObj.appendChild(trObj);
			}
			editorFrameObj.parentNode.insertBefore(tableObj,editorFrameObj);
			tdObj.innerHTML = ctrlObj;

			// html (소스보기) 모드 활성화
			if(document.getElementsByName(textareaName[i])[0].getAttribute("mode")=="html") {
				setTimeout("Wysiwyg.viewSource('iframe"+textareaName[i]+"', '"+textareaName[i]+"', "+i+")", 500);
			}
			else {
				// 툴 초기화
				var toolbox = document.getElementById('toolbox'+i);
				var tools = toolbox.getElementsByTagName('img');
				for(var j=0; j<tools.length; j++) {
					tools[j].style.filter = "";
					tools[j].style.cursor = "pointer";
				}
			}
		} // end of  'for(var i=0;i<textareaName.length;i++)'
	}

	/*-----------------------------------------------------------------------------------------------
	iframe 내의 편집한 내용을 parent textarea 로 입력  및 required 시 iframe 내 편집 영역에 포커스 위치
	-------------------------------------------------------------------------------------------------*/
	this.submit_result = function (num) {
		var iname = 'iframe'+textareaName[num];
		var wname = textareaName[num];
		var doc = document.getElementById(wname) || document.getElementsByName(wname)[0]; // 2009.04.27 fixed
		var required = (doc.getAttribute("REQUIRED")!=null) ? doc.required : doc.getAttribute("REQUIRED");

		// 수정 2008.03.25 / 05.22 - 랭크업 개발팀 : iframe값을 textarea로 옮기는 작업
		if(doc.style.display=="none") this.nl2br_remove_content(iname, wname);

		//## [중요] ::<textarea name='content' type='editor' hname='내용' required nofocus></textarea> 처럼 'nofocus'를 함께 써줘야 한다!
		if(document.getElementById(iname).style.display=="none" && required != null) doc.focus(); // HTML 편집인 경우
		else if(doc.value=='' && required != null) eval(iname).focus(); // EDITOR 편집인 경우
		else return;
	}

	/*----------------------------------------------------------------------
	위지위그에디터로 작성한 컨텐츠를 submit 할때 처리사항
	----------------------------------------------------------------------*/
	this.submit_start = function () {
		for(var i=0; i<textareaName.length; i++) {
			var aa = this.submit_result(i);
			if(aa == false) return false;
		}
		return true;
	}
}

var Wysiwyg = new wysiwyg_Class;
try {
	if(direct_board!==true) {
		if(window.addEventListener) window.addEventListener("load", Wysiwyg.createELM, false);
		else if(window.attachEvent) window.attachEvent("onload", Wysiwyg.createELM);
	}
}
catch(e) {
	if(window.addEventListener) window.addEventListener("load", Wysiwyg.createELM, false);
	else if(window.attachEvent) window.attachEvent("onload", Wysiwyg.createELM);
}