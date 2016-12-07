//######################################################################
//# ���α׷��� : ��ũ�� �ַ�� �ΰ���� ���α׷�(��ʰ��� ���α׷��� �ּ�ȭ����)
//# ���� : v1.1, r101220
//# ������ : C2tfiW ( Kurokisi )
//# ���� ������Ʈ : 2010.12.20
//# ���̼��� : �ַ�Ǳ��Ű��� �ƴ� ��� ��ũ�����κ��� ����� �����ž� �մϴ�.
//######################################################################
// �ΰ� Ŭ���� ����
var LOGO = function() {
	this.registFrm = null;			// ���/������
	this.settingFrm = null;		// �ΰ� ������
	this.bannerBody = null;		// �ΰ� �ٵ� ���̺�
	this.bannerItems = null;		// �ΰ� ������ ���̺�
	this.selClass = "selClass";	// ������ �ΰ��� ��Ÿ��
	this.selObject = null;			// ������ �ΰ�ü
	this.selRow = null;			// ������ �ΰ��� �� ��ȣ
	this.selNo = null;				// ������ �ΰ��� �ε��� ��ȣ
	this.selBind = null;			// ������ �ΰ��� ���ε� ��ȣ
	this.maxBind = 100;			// ���ε� ���� ����
	this.maxWidth = null;		// �ΰ� ���λ����� ����
	this.maxPrevWidth = 736;	// �̸����� ���λ����� ����( 100% �������� ������ : ������� width ������ -24px)
	this.previewPop = null;		// �̸����� �˾� ��ü
}

// �ΰ� ����
LOGO.prototype.select_item = function(arg) {
	var el = arg.target||arg.srcElement;
	var type = el.type;
	try {
		// ������Ʈ ����
		do { el = el.parentNode; } while(el.getAttribute('id')!="item");
		var click_no = el.parentNode.getElementsByTagName("input")[0].value; // �ΰ� �ε��� ��ȣ
		var obj = el.parentNode.parentNode.parentNode;
	}
	catch(e) {
		// �־��� ���� ���� ������ ��� ����
		return false;
	}
	// ������ �ΰ��� üũ�ڽ� ���
	if(type!="checkbox") el.getElementsByTagName("input")[0].checked = !el.getElementsByTagName("input")[0].checked;
	if(el.className != classObj.selClass) {
		// ���� ���õ� �� ����
		if(classObj.selNo!=null) {
			var items = document.getElementsByName("item");
			for(var i=0; i<items.length; i++) {
				if(items[i].getElementsByTagName("input")[0].value==classObj.selNo) {
					items[i].className = '';
					break;
				}
			}
		}
		el.className = classObj.selClass; // ������ �ΰ� �缳��
		classObj.selObject = el;
		classObj.selNo = el.getElementsByTagName('input')[0].value; // �ε��� ��ȣ
		classObj.selBind = el.getElementsByTagName('input')[1].value; // ���ε� ��ȣ

		var nos = document.getElementsByName('chk_no[]');
		for(var row=0; row<nos.length; row++) if(nos[row].value==classObj.selNo) { classObj.selRow = row; break; }
	}
}

// ������ �� OBJECT �������� - ���� ����� ���
LOGO.prototype.get_object = function(target) {
	var body_row = null;
	var target_obj = null;
	var target_bind = '';
	var target_row = 0;
	// �ΰ� ����Ʈ �߿��� ���° ���̺��� ���° ���������� üũ
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
	// ��ü �ΰ�ٵ� �߿��� ���° ROW �� �ִ��� üũ
	if(target_obj!=null) {
		for(body_row=0; body_row<this.bannerBody.rows.length; body_row++) {
			if(this.bannerBody.rows[body_row].cells[0].innerHTML == target_obj.parentNode.parentNode.innerHTML) break;
		}
	}
	return {"obj":target_obj, "row":parseInt(target_row,0), "bind":target_bind, "body":parseInt(body_row,0)};
}

// �ΰ� ���ø�
LOGO.prototype.get_template = function(str) {
	var string = "\
	<span id='bindToolBox'></span>\
	<table name='bannerItem' id='bannerItem' width='100%' cellpadding='0' cellspacing='0' border='0' bgcolor='white'>\
	<tbody>"+str+"\
	</tbody>\
	</table>";
	return string;
}

// �ΰ��� �ʱ�ȭ
LOGO.prototype.reset_media_contents = function() {
	// �̵�� - �÷���
	//var media_datas = document.getElementsByName('media_data');
	//for(var i=0; i<media_datas.length; i++) media_datas[i].parentNode.getElementsByTagName('span')[0].innerHTML = '';
	// �����Է�
	var texts = document.getElementsByName('text');
	for(var i=0; i<texts.length; i++) texts[i].innerHTML = '';
}

// ���õ� �ΰ� ���� - ����Ʈ �ϴ� ���� ����
LOGO.prototype.remove_item = function(datas) {
	for(var i=this.bannerItems.length-1; i>=0; i--) {
		var trs = this.bannerItems[i].rows;
		for(var j=trs.length-1; j>=0; j--) {
			if(in_array(trs[j].firstChild.getElementsByTagName("input")[0].value, datas)==false) continue;
			this.bannerItems[i].deleteRow(j);
			// ���� ����
			if(this.bannerItems[i].rows.length>0) continue;
			for(var body_row=0; body_row<this.bannerBody.rows.length; body_row++) {
				if(this.bannerBody.rows[body_row].cells[0].innerHTML != this.bannerItems[i].parentNode.innerHTML) continue;
				this.bannerBody.deleteRow(body_row);
				break;
			}
		}
	}
	// ���ε������� ���� �ִ� ��� ó��
	for(var i=0; i<this.bannerItems.length; i++) {
		var trs = this.bannerItems[i].rows;
		if(trs.length>1) continue;
		for(var j=0; j<trs.length; j++) {
			if(trs[j].getElementsByTagName("input")[1].value=='') continue;
			trs[j].getElementsByTagName('input')[1].value = ''; // ���ε� ��ȣ ����
			this.bannerItems[i].parentNode.parentNode.getElementsByTagName('span')[0].innerHTML = ''; // ���� ����
		}
	}
}

// ��� �� ������ ����Ʈ ��ŷ
LOGO.prototype.screen_blind = function(mode) {
	if(mode===true) {
		var _height = parseInt(document.body.clientHeight,10);
		if(parseInt(document.body.scrollHeight,10)>_height) _height = parseInt(document.body.scrollHeight,10);
		$('screenBlindDiv').style.height = _height + "px";
		$('screenBlindDiv').style.marginTop = "-" + parseInt(document.body.scrollTop,10) + "px";
		this.change_display("screenBlindDiv", true);

		// SELECT ��ü �����
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

// �ΰ� ���̾� ���
LOGO.prototype.change_display = function(el, val) {
	if(val===true||val===false) {
		var obj = $(el);
		obj.style.display = val ? "block" : "none";
		if(val===false) {
			if(el=="registFrmDiv") this.screen_blind(false);
			return false;
		}
		// ������ �߾ӿ� ����
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

// �ΰ����� �� �� ����
LOGO.prototype.change_form = function(el) {
	var form = this.registFrm;
	// �����Է� ��� ��
	if(el.value =="text") {
		$('editorBox').style.display = "block";
		$('previewBox').style.display = "none";
		form.width.readOnly = form.height.readOnly = form.width_type.disabled = form.height_type.disabled = form.attached.readOnly = true;
		form.width.className = form.height.className = form.attached.className = "disable";
		form.address.removeAttribute("required");
		form.on_attached.removeAttribute("required");
		form.content.setAttribute("required", "required");
	}
	// �̵���� ��� ��
	else {
		$('editorBox').style.display = "none";
		$('previewBox').style.display = "block";
		form.width.readOnly = form.height.readOnly = form.width_type.disabled = form.height_type.disabled = form.attached.readOnly = false;
		form.width.className = form.height.className = form.attached.className = "enable";
		form.address.setAttribute("required", "required");
		form.on_attached.setAttribute("required", "required");
		form.content.removeAttribute("required");
	}
	// ���ܻ��� ó��
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

// �ΰ� ���/���� - ������ ����Ʈ�� ���� ����
LOGO.prototype.regist_item = function(modify) {
	var form = this.registFrm;
	this.set_media_outline(true);
	form.reset();

	// ������â �� ����
	document.getElementById('iframecontent').contentWindow.document.body.innerHTML = '';
	$('mediaInfo').innerHTML = "-- �� --, --";

	// �̸����� â �ʱ�ȭ
	var itembox = $('previewItemBox');
	itembox.style.width = itembox.style.height = '';
	itembox.innerHTML = '';

	if(modify===true) {
		form.no.value = this.selNo;
		form.mode.value = "update";
		// �ʱⰪ ������� ����
		form.bind.value = this.selObject.getElementsByTagName('input')[1].value; // ���ε�
		var spans = this.selObject.getElementsByTagName("span");
		var key = {'width':0, 'height':1, 'type':2, 'use_date':3, 'sdate':4, 'edate':5, 'media':6, 'content':6};

		// ���밪 ����
		form.width_type.value = spans[key['width']].getAttribute('type');
		form.height_type.value = spans[key['height']].getAttribute('type');
		if(!form.width_type.value) form.width_type.value = "exact";
		if(!form.height_type.value) form.height_type.value = "exact";
		this.change_size_type(form.width_type);
		this.change_size_type(form.height_type);
		form.width.value = parseInt(spans[key['width']].innerHTML, 10);
		form.height.value = parseInt(spans[key['height']].innerHTML, 10);

		// �ΰ����� ����
		for(var i=0; i<form.banner_type.length; i++) {
			if(form.banner_type[i].value != spans[key['type']].type) continue;
			form.banner_type[i].checked = true;
			break;
		}

		// ��ϵ� �ΰ� ���� �Է�
		switch(spans[key['type']].type) {
			case "media":
				// ����ȯ - text ��忡�� media ���� ��ȯ�� ��ġ
				setTimeout(function() {
					classObj.change_form(form.banner_type[0].checked ? form.banner_type[0] : form.banner_type[1]);
				}, 0);
				// �̵�� �����ڽ� ����
				$('mediaInfo').innerHTML = spans[key['media']].getAttribute("width")+" �� "+spans[key['media']].getAttribute("height")+", "+spans[key['media']].getAttribute("extension");
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
					// �ΰ� �̹��� ����
					var img = new Image();
					img.style.width = img.style.height = "100%";
					img.src = spans[key['media']].getElementsByTagName("img")[0].src;
					itembox.appendChild(img);
				}
				else { // flash �ϰ�� �ּҶ� ��Ȱ��ȭ
					form.address.value = '';
					var movieObj = spans[key['media']].getElementsByTagName("object")[0];
					this.append_flash_object(itembox, movieObj.getAttribute("movie"), "100%", "100%");
				}
				// ���/���� �� ���̱�
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
				// �� �� ȯ
				this.change_form(form.banner_type[0].checked ? form.banner_type[0] : form.banner_type[1]);
				var content = new String();
				var contentObj = this.selObject.getElementsByTagName("span");
				if(contentObj[contentObj.length-1].id=="text_data") {
					content = contentObj[contentObj.length-1].innerHTML.replace(/{:_lt:}/g, "<").replace(/{:_gt:}/g, ">");
				}
				// ���/���� �� ���̱�
				this.change_display('registFrmDiv', true);
				// <SCRIPT ������ ���Ե� ������ ��� �ؽ�Ʈ ��� Ȱ��ȭ
				if(content.toUpperCase().indexOf("<SCRIPT")!=-1) {
					Wysiwyg.viewSource('iframecontent', 'content', 0); // ������ ������� ���� - TEXT ������
					form.content.value = document.all ? content.replace(/\r\n<SCRIPT/gi, "<SCRIPT") : content.replace(/\n<SCRIPT/gi, "<SCRIPT"); // textarea �� ����
				}
				else {
					Wysiwyg.viewEdiror('iframecontent', 'content', 0); // ������ ������� ���� - HTML ������
					document.getElementById('iframecontent').contentWindow.document.body.innerHTML = content; // ������â �� ����
				}
				break;
		}
	}
	// �ű� ��Ͻ� ���ʱ�ȭ
	else {
		form.no.value = '';
		form.mode.value = "insert";
		form.content.value = '';
		itembox.innerHTML = "<i disabled style=\"padding:0px 4px 0px 4px;\">NULL</i>";

		this.change_size_type(form.width_type);
		this.change_size_type(form.height_type);
		this.change_form(form.banner_type[0]);

		// ���/���� �� ���̱�
		this.change_display('registFrmDiv', true);
	}
}

// �ΰ� ���/������ �ݿ�
LOGO.prototype.append_banner_item = function(mode, banner_item) { // mode { insert | update }
	switch(mode) {
		// ��� �� ����Ʈ �߰� �۾�
		case "insert":
			// ���õ� �ΰ� ������ - ��ó���� �߰�
			if(this.selNo==null) this.bannerBody.insertRow(0).insertCell(0).innerHTML = this.get_template("<tr><td>"+banner_item['item']+"</td></tr>");
			else {
				// ���õ� �ΰ� ������ - ���õ� �ΰ� �Ʒ��� �߰�
				this.selObject.className = ''; // �������õ� �ΰ� �ʱ�ȭ
				if(this.selBind=='' || this.selBind==null) {
					var target = this.get_object(this.selRow+1);
					if(isNaN(target['body'])) target['body'] = this.bannerBody.rows.length;
					this.bannerBody.insertRow(target['body']).insertCell(0).innerHTML = this.get_template("<tr><td>"+banner_item['item']+"</td></tr>");
				}
				else {
					var target = this.get_object(this.selRow);
					target['obj'].insertRow(target['row']+1).insertCell(0).innerHTML = banner_item['item'].replace(/value=''/g, "value="+this.selBind); // ���ε� ��ȣ �߰�
				}
			}
			this.selNo = banner_item['no'];
			break;

		// ���� �� ����Ʈ ���� �۾�
		case "update":
			this.selObject.parentNode.innerHTML = banner_item['item'];
			break;
	}
	this.rebuild_event(true);
	this.selObject.className = this.selClass;
	this.change_display('registFrmDiv', false); // ���/������ �ݱ�
}

// �ΰ� �̸����� - �˾�������
LOGO.prototype.preview_banner = function(regist_mode) { // mode { true  or  false }
	setTimeout(function() {
		var obj = null;
		var width, height, outline; // 2010.12.20 added
		var pop_width_add = 25; // ���� �߰�(��ũ�ѹ�)
		var pop_height_add = 100; // ���� �߰�(Ÿ��Ʋ��/�ϴܹ�)
		var banner_content = new String();
		// �ΰ� ��� ���� ���
		if(regist_mode==true) {
			var form = classObj.registFrm;
			var size_types = {"pixel": "px", "percent": "%", "exact": "px"};
			switch(form.banner_type[0].checked) {
				// �̵��(�̹���/�÷���)��� ���
				case true:
					var info = $('mediaInfo').innerHTML.split(", "); // mediaInfo ���� flash ���� image ���� üũ
					if(info[1].toLowerCase()=="swf") {
						obj = {'id': "media_data"}; // ����Ʈ ��� ��Ī �ڵ�
						width = form.width.value+size_types[form.width_type.value];
						height = form.height.value+size_types[form.height_type.value];
						outline = (form.mediaOutlineChecker.checked==true);
						banner_content = "<div id='content_item' style='width:"+width+";height:"+height+"'></div>";
						var movie = $('previewItemBox').getElementsByTagName("object")[0].getAttribute("movie");
					}
					else {
						obj = {'id': "media"}; // ����Ʈ ��� ��Ī �ڵ�
						var img = $('previewItemBox').getElementsByTagName("img")[0];
						if(img==null) {
							alert("��ʰ� �������� �ʰų� �̸������� �� ���� �����Դϴ�."+SPACE);
							document.body.focus();
							return false;
						}
						width = form.width.value+size_types[form.width_type.value];
						height = form.height.value+size_types[form.height_type.value];
						outline = (form.mediaOutlineChecker.checked==true) ? " class='banner_outline'" : ''; // 2010.12.20 added
						banner_content = "<img src='"+img.src+"' width='"+width+"' height='"+height+"' border='0'"+outline+">";
						// ��ũ �߰�
						var address = form.address.value.replace(/{:domain:}/gi, domain);
						if(address!=null && address) {
							var target = form.popup_banner.checked==true ? "_blank" : "_self";
							target = target!=null ? " target='"+target+"'" : '';
							banner_content = "<a href='"+address+"'"+target+" title='Ŭ�� : "+address+" ����Ʈ�� �̵�'>"+banner_content+"</a>";
						}
					}
					break;
				// �����Է� ���
				case false:
					obj = {'id': "text"}; // ����Ʈ ��� ��Ī �ڵ�
					width = classObj.maxWidth;
					height = 400;
					banner_content = document.getElementsByName('iframecontent')[0].contentWindow.document.body.innerHTML;
					if(!banner_content) banner_content = form.content.innerHTML;
					banner_content = banner_content.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&nbsp;/g, '&nbsp;');
					break;
			}
		}
		// ����Ʈ�� ���
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
					// ��ũ�߰�
					var address = obj.getAttribute('address'); //.replace(/{:domain:}/gi, domain);
					if(address!=null && address) {
						var target = obj.getAttribute('target');
						target = target!=null ? " target='"+target+"'" : '';
						banner_content = "<a href='"+address+"'"+target+" title='Ŭ�� : "+address+" ����Ʈ�� �̵�'>"+banner_content+"</a>";
					}
					break;
				default: // �����Է� ���
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
		// percent ����ϰ�� �⺻�� �Ҵ�
		if(new String(width).indexOf('%')!=-1) width = classObj.maxWidth;
		if(new String(height).indexOf('%')!=-1) height = 400;

		// A �±� ���� {:domain:} ���� - 2008.06.14 �߰�
		var atags = banner_content.match(/(<a [^<]*href=["|']?([^ "']*)["|']?[^>])/gi);
		if(atags!=null) for(var i=0; i<atags.length; i++) banner_content = banner_content.replace(atags[i], atags[i].replace(domain+"rankup_module/rankup_banner/{:domain:}", "{:domain:}").replace(/{:domain:}/g, domain));

		// �̸����� ���ø�(���)
		var preview_top = "<html>\
		<head>\
		<title>�ΰ�̸�����</title>\
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

		// �̸����� ���ø�(�ϴ�)
		var preview_bottom = "</td>\
		</tr>\
		<tr><td height='5'></td></tr>\
		<tr height='30' bgcolor='#acacac' align='right'>\
			<td style='color:white' style='padding-right:7px'><a style='cursor:pointer' onClick='self.close()'><img src='./img/bt_close_s.gif' border='0'></a></td>\
		</tr>\
		</table>\
		</body>\
		</html>";

		// �̸����� â ����
		if(classObj.previewPop!=null) classObj.previewPop.close();
		classObj.previewPop = window.open("about:blank", "preivew_pop", "width="+(parseInt(width,10)+pop_width_add)+"px,height="+(parseInt(height,10)+pop_height_add)+"px, scrollbars=yes");

		// �̸����� â�� ���� �Է�
		switch(obj.id) {
			case "text":
				// �ܼ��� HTML ���ڿ��� ���
				if(banner_content.toLowerCase().indexOf("<script")==-1) {
					classObj.previewPop.document.write(preview_top + banner_content + preview_bottom);
				}
				else { // JavaScript �ڵ带 ������ ���ڿ��� ���
					classObj.previewPop.document.write(banner_content);
					setTimeout(function() { // JavaScript �ҽ��� �̸������� ��쿡 ���
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

// ��ü �ΰ� ����/����
LOGO.prototype.checkAll = function(val) {
	var nos = document.getElementsByName("chk_no[]");
	for(var i=0, j=0; i<nos.length; i++) {
		var item = nos[i];
		if(item.disabled==true) continue;
		item.checked = (val=="cross") ? !item.checked : val;
	}
}

// ���õ� �ΰ� �ε��� �� ��������
LOGO.prototype.get_checkAll = function() {
	var items = new Array();
	var nos = document.getElementsByName("chk_no[]");
	for(var i=0, j=0; i<nos.length; i++) {
		var item = nos[i];
		if(item.checked==true) items.push(item.value);
	}
	return items.join("__");
}

// ÷������ ���ε� - �̸����� ����
LOGO.prototype.post_attached = function(el) {
	if(el.getAttribute("filter")!=null) {
		var filters = el.getAttribute("filter").toLowerCase().split(",");
		var exts = el.value.toLowerCase().split(".");
		if(in_array(exts[exts.length-1], filters)==false) {
			alert("�ΰ�� ����� �� ���� �̵�� ���������Դϴ�."+SPACE);
			document.body.focus();
			return false;
		}
	}
	var form = this.registFrm;
	var mode = form.mode.value;
	var encType = form.encoding;
	form.mode.value = "post_attached";
	form.encoding = "multipart/form-data"; // ���ڵ� ���� - ����÷�� ����
	form.submit();

	// ���ڵ� ���� : application/x-www-form-urlencoded
	form.encoding = encType;
	form.mode.value = mode;
}

// �ΰ�ũ�� ���� ����
LOGO.prototype.change_size_type = function(el) {
	var obj = $(el.name.replace(/_type/gi, ''));
	obj.style.display = in_array(el.value, new Array("exact","auto")) ? "none" : "inline";
	switch(el.value) {
		case "pixel":
			if(obj.value=='') {
				var info = $('mediaInfo').innerHTML.split(',');
				info = info[0].split(' �� ');
				obj.value = (obj.name=="width") ? info[0]>this.maxPrevWidth ? this.maxPrevWidth : info[0].replace(/--/g, 100) : info[1].replace(/--/g, 100);
			}
			break;
		case "percent":
			if(obj.value=='' || obj.value>100) obj.value = 100;
			break;
	}
	this.set_resize();
}

// �ΰ�ũ�� ������ ����
LOGO.prototype.get_media_size = function(info) {
	var form = this.registFrm;
	var width, height;
	if(info==undefined) {
		var info = {};
		var _info = $('mediaInfo').innerHTML.split(',');
		_info = _info[0].split(' �� ');
		info['width'] = parseInt(_info[0], 10); // px, % ����
		info['height'] = parseInt(_info[1], 10); // px, % ����
	}
	// ���� ũ��
	switch(form.width_type.value) {
		case "pixel":
			width = form.width.value;
			if(this.maxWidth!=null && form.width.value>this.maxWidth) {
				alert("���� ������("+this.maxWidth+"pixel)�� �ʰ��Ͽ� �Է��� �� �����ϴ�."+SPACE);
				width = form.width.value = this.maxWidth; // ����ũ�� ����
			}
			if(width>this.maxPrevWidth) width = this.maxPrevWidth; // �̸����� ���� ���� ������ �ʰ���
			width = width ? width+"px" : "0px";
			break;
		case "percent":
			if(form.width.value>100) {
				alert("���� ������(100%)�� �ʰ��Ͽ� �Է��� �� �����ϴ�."+SPACE);
				form.width.value = 100; // ����ũ�� ����
			}
			width = form.width.value ? form.width.value+"%" : "0%";
			break;
		case "exact":
			if(this.maxWidth!=null && info['width']>this.maxWidth) info['width'] = this.maxWidth; // ����ũ�� ����
			form.width.value = info['width'];
			width = info['width']>this.maxPrevWidth ? this.maxPrevWidth+"px" : info['width']+"px"; // �̸����� ���� ���� ������ �ʰ���
			break;
		case "auto": width = ''; break;
	}
	// ���� ũ��
	switch(form.height_type.value) {
		case "pixel": height = form.height.value ? form.height.value+"px" : "0px"; break;
		case "percent":
			if(form.height.value>100) {
				alert("���� ������(100%)�� �ʰ��Ͽ� �Է��� �� �����ϴ�."+SPACE);
				form.height.value = 100; //����ũ�� ����
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

// �÷��� OBJECT �ٿ� �ֱ�
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

// ÷������ �̸�����
LOGO.prototype.preview_attached = function(file_name, info) {
	var form = this.registFrm;
	var url = domain+file_name; // domain : rankup_basic.class.php ���� ����
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
	// �����ڽ� ó��
	$('mediaInfo').innerHTML = info['text'];
}

// �ΰ� ������ �缳��
LOGO.prototype.set_resize = function() {
	if(this.registFrm.banner_type[0].checked===false) {
		document.body.focus();
		return false;
	}
	var itembox = $('previewItemBox');
	var item = itembox.getElementsByTagName("img")[0]; // �̹���
	if(item==undefined) item = itembox.getElementsByTagName("object")[0]; // FLASH ������Ʈ
	if(item==undefined) return false;

	var size = this.get_media_size();
	if(size['width']) itembox.style.width = size['width'];
	if(size['height']) itembox.style.height = size['height'];
}

// �ΰ� �ƿ����� Ȱ��ȭ ����
LOGO.prototype.set_media_outline = function(reset) {
	var itembox = $('previewItemBox');
	if(reset==true) {
		this.registFrm.mediaOutlineChecker.checked = false;
		itembox.style.border = "#555555 1px solid";
	}
	itembox.style.border = in_array(itembox.style.border, new Array("", "#fdfdfd 1px solid")) ? "#555555 1px solid" : "#fdfdfd 1px solid";
}

// ����Ʈ���� �ΰ� �ƿ����� Ȱ��ȭ ���
LOGO.prototype.set_banner_outline = function() {
	var media = document.getElementsByName('media');
	for(var i=0; i<media.length; i++) media[i].style.border = media[i].style.border=="" ? "#555555 1px solid" : "";
}

// �ΰ� ��������� �ȳ� ���
LOGO.prototype.open_size_guide = function() {
	var obj = $('bannerSizeGuideDiv');
	obj.style.display = obj.style.display=="block" ? "none": "block";
	if(obj.style.display=="block") obj.focus();
}

// �ΰ� ��������� ����
LOGO.prototype.apply_media_size = function(width, height) {
	// �����Է� ��忡���� ����ȵ�
	var form = this.registFrm;
	if(form.banner_type[1].checked===true) {
		alert("�����Է� ��忡���� ����� �����Ͻ� �� �����ϴ�."+SPACE);
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

// �ΰ� ���������� ����
LOGO.prototype.set_real_size = function() {
	var form = this.registFrm;
	if(form.banner_type[1].checked===true) {
		alert("�����Է� ��忡���� ����� �����Ͻ� �� �����ϴ�."+SPACE);
		document.body.focus();
		return false;
	}
	form.width_type.value = form.height_type.value = "exact";
	this.change_size_type(form.width_type);
	this.change_size_type(form.height_type);
}

// �������� ���� - ���� �� ���ε� ����
LOGO.prototype.save_settings = function(silent) {
	if(silent!==true && !confirm("��������� �����Ͻðڽ��ϱ�?"+SPACE)) {
		document.body.focus();
		return false;
	}
	var form = this.settingFrm;
	var mode = form.mode.value;
	form.mode.value = "save_settings";
	form.submit();
	form.mode.value = mode;
}

// ���õ� �ΰ� ���� ó��(���/�̻��, ��â/��â, ����) - Ajax ó��
LOGO.prototype.ajax_process = function(mode, multi, val) {
	setTimeout(function() {
		var modeValue = val; // setTimeout�� Ư������ ���� val ���� ������ �������� ó��
		// ���� ó��
		switch(mode) {
			case "modify": // ������
				classObj.regist_item(true);
				return true;
				break;
			case "view": // ��밪 �����
				var data = (multi===true) ? classObj.get_checkAll() : classObj.selNo;
				if(data.length<1) {
					alert("��뿩�θ� ������ �ΰ� �����Ͽ� �ֽʽÿ�."+SPACE);
					document.body.focus();
					return false;
				}
				if(modeValue==undefined) {
					var cross_views = {'unused': "yes", 'use': "no"};
					var modeValue = cross_views[classObj.selObject.getElementsByTagName("img")[0].value];
				}
				var url = "./multiProcess.html?mode=view&data="+data+"&val="+modeValue;
				break;
			case "delete": // ������
				var data = (multi===true) ? classObj.get_checkAll() : classObj.selNo;
				if(data.length<1) {
					alert("�����Ͻ� �ΰ� �����Ͽ� �ֽʽÿ�."+SPACE);
					document.body.focus();
					return false;
				}
				var url = "./multiProcess.html?mode=delete&data="+data;
				break;
			case "target": // ��â��� �����
				// �̵���� �� �̹����϶��� â��� ���� ����
				var data = classObj.selNo;
				if(modeValue==undefined) {
					var cross_targets = {'selfwin': "_blank", 'newwin': "_self"};
					var modeValue = cross_targets[classObj.selObject.getElementsByTagName("img")[1].value]
				}
				if(!modeValue) {
					alert("â��带 ������ �� ���� �ΰ����� �Դϴ�."+SPACE);
					document.body.focus();
					return false;
				}
				var url = "./multiProcess.html?mode=target&data="+data+"&val="+modeValue;
				break;
			case "outline": // �׵θ�ǥ�� - 2010.12.20 added
				var data = classObj.selNo;
				var obj = $(classObj.selObject).select('span[id="media"]')[0];
				if(obj==null) {
					alert('�����Է����� ����� ��ʴ� �׵θ�ǥ�ø� �� �� �����ϴ�.'+SPACE);
					return false;
				}
				var modeValue = obj.hasClassName('banner_outline') ? 'off' : 'on';
				var url = "./multiProcess.html?mode=outline&data="+data+"&val="+modeValue;
				break;
			default:
				document.body.focus();
				return false;
		}
		// ����
		var modes = {'view':"�� ��뿩�θ� ����", 'delete':"�� ����", 'target':"�� ��â��带 ����", 'outline': '�� �׵θ�ǥ�� ������ ����'};
		if(!confirm("�����Ͻ� �ΰ�"+modes[mode]+" �Ͻðڽ��ϱ�?"+SPACE)) {
			document.body.focus();
			return false;
		}
		// Ajax ó��
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
						case "delete": // ������ ������ ����Ʈ�� �ݿ�
							classObj.remove_item(nos);
							break;
					case "outline": // �׵θ�ǥ�� - 2010.12.20 added
						var obj = $(classObj.selObject).select('span[id="media"]')[0];
						if(modeValue=='on') obj.addClassName('banner_outline');
						else obj.removeClassName('banner_outline');
						var banner_infos = classObj.selObject.getElementsByTagName("span");
						banner_infos[2].setAttribute('outline', modeValue);
						break;
					}
				}
				alert(resultData.firstChild.nodeValue+SPACE); // ��� ���
				document.body.focus();
			}
		});

	}, 0);
}

// �ΰ� �̺�Ʈ �Ҵ� - ���/����/���� ����/���ε� ����
LOGO.prototype.rebuild_event = function(reset) {

	// ���� ���õ� �� �� ����
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

	// �̵� ���̵��� ó�� - this.append_flash_object() �� ���ϰ� ������ �Ѥ�;
	var media_datas = document.getElementsByName('media_data');
	for(var i=0; i<media_datas.length; i++) {
		var infos = media_datas[i].value.split('|');
		this.append_flash_object(media_datas[i].parentNode.getElementsByTagName('span')[0], infos[1], infos[2], infos[3]);
	}

	// �����Է� ��� �ΰ� ó��
	var text_datas = document.getElementsByName('text_data');
	for(var i=0; i<text_datas.length; i++) {
		var xcontent = text_datas[i].innerHTML.replace(/{:_lt:}/g, "<").replace(/{:_gt:}/g, ">").replace(/-->/g, '').replace(/<!--/g, '').replace(/\/\/-->/g, ''); // 2010.12.20 fixed
		try {
			var xwindow = text_datas[i].parentNode.getElementsByTagName('iframe')[0].contentWindow.document.write(xcontent);
			// �� ��������� ������ '�������� ���� ����' �� ������ ������ �߻��Ͽ� setTimeout() ó��
			setTimeout("classObj.set_text_content('"+text_datas[i].parentNode.getElementsByTagName('iframe')[0].id+"')", 0);
		}
		catch(e) {
			//alert(e.message);
		}
	}
	// �̺�Ʈ �Ҵ�
	var items = document.getElementsByName('item');
	for(var i=0; i<items.length; i++) {
		Event.stopObserving(items[i], 'click', this.select_item); // �̺�Ʈ ����
		Event.observe(items[i], 'click', this.select_item); // �̺�Ʈ �Ҵ�
	}
	document.body.focus();
}

// �����Է� ��� �ΰ� �ۺ���
LOGO.prototype.set_text_content = function(iframe) {
	try {
		var iframe = document.getElementsByName(iframe)[0];
		var banner_content = iframe.contentWindow.document.body.innerHTML;

		// A �±� ���� {:domain:} ���� - 2008.06.14 �߰�
		var atags = banner_content.match(/(<a [^<]*href=["|']?([^ "']*)["|']?[^>])/gi);
		if(atags!=null) for(var i=0; i<atags.length; i++) banner_content = banner_content.replace(atags[i], atags[i].replace(domain+"rankup_module/rankup_banner/{:domain:}", "{:domain:}").replace(/{:domain:}/g, domain));

		iframe.parentNode.getElementsByTagName('span')[0].innerHTML = banner_content;
		iframe.contentWindow.document.body.innerHTML = '';
	}
	catch(e) {
		// alert(e.message);
	}
}