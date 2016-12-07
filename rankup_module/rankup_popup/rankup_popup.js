//######################################################################
//# ���α׷��� : ��ũ�� �ַ�� �˾����� ���α׷�
//# ���� : v1.0, r080528
//# ������ : C2tfiW ( Kurokisi )
//# ���� ������Ʈ : 2008.05.28
//# ���̼��� : �ַ�Ǳ��Ű��� �ƴ� ��� ��ũ�����κ��� ����� �����ž� �մϴ�.
//######################################################################
// �˾� Ŭ���� ����
var POPUP = function() {
	this.registFrm = null;			// ���/������
	this.settingFrm = null;		// �˾� ������
	this.popupBody = null;		// �˾� �ٵ� ���̺�
	this.popupItems = null;		// �˾� ������ ���̺�
	this.selClass = "selClass";	// ������ �˾��� ��Ÿ��
	this.selObject = null;			// ������ �˾���ü
	this.selRow = null;			// ������ �˾��� �� ��ȣ
	this.selNo = null;				// ������ �˾��� �ε��� ��ȣ
	this.selBind = null;			// ������ �˾��� ���ε� ��ȣ
	this.maxBind = 100;			// ���ε� ���� ����
	this.maxWidth = null;		// �˾� ���λ����� ����
	this.maxPrevWidth = 736;	// �̸����� ���λ����� ����( 100% �������� ������ : ������� width ������ -24px)
	this.minPrevWidth = 200;	// �̸����� ���λ����� �ּҰ�
	this.previewPop = null;		// �̸����� �˾� ��ü
}

// �˾� ����
POPUP.prototype.select_item = function(arg) {
	var el = arg.target||arg.srcElement;
	var type = el.type;
	try {
		// ������Ʈ ����
		do { el = el.parentNode; } while(el.getAttribute('id')!="item");
		var click_no = el.parentNode.getElementsByTagName("input")[0].value; // �˾� �ε��� ��ȣ
		var obj = el.parentNode.parentNode.parentNode;
	}
	catch(e) {
		// �־��� ���� ���� ������ ��� ����
		return false;
	}
	// ������ �˾��� üũ�ڽ� ���
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
		el.className = classObj.selClass; // ������ �˾� �缳��
		classObj.selObject = el;
		classObj.selNo = el.getElementsByTagName('input')[0].value; // �ε��� ��ȣ
		var nos = document.getElementsByName('chk_no[]');
		for(var row=0; row<nos.length; row++) if(nos[row].value==classObj.selNo) { classObj.selRow = row; break; }
	}
}

// ������ �� OBJECT �������� - ���� ����� ���
POPUP.prototype.get_object = function(target) {
	var body_row = null;
	var target_obj = null;
	var target_bind = '';
	var target_row = 0;
	// �˾� ����Ʈ �߿��� ���° ���̺��� ���° ���������� üũ
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
	// ��ü �˾��ٵ� �߿��� ���° ROW �� �ִ��� üũ
	if(target_obj!=null) {
		for(body_row=0; body_row<this.popupBody.rows.length; body_row++) {
			if(this.popupBody.rows[body_row].cells[0].innerHTML == target_obj.parentNode.parentNode.innerHTML) break;
		}
	}
	return {"obj":target_obj, "row":parseInt(target_row,0), "bind":target_bind, "body":parseInt(body_row,0)};
}

// �˾� ���ø�
POPUP.prototype.get_template = function(str) {
	var string = "\
	<table name='popupItem' id='popupItem' width='100%' cellpadding='0' cellspacing='0' border='0' bgcolor='white'>\
	<tbody>"+str+"\
	</tbody>\
	</table>";
	return string;
}

// �˾����� �ʱ�ȭ
POPUP.prototype.reset_media_contents = function() {
	// �����Է�
	//var texts = document.getElementsByName('text');
	//for(var i=0; i<texts.length; i++) texts[i].innerHTML = '';
}

// �˾� ���� �ٲٱ�
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
			//## �� ����� �̵���
			case "up": case "top":
			//###############################################################
				next_target = (mode=="up") ? parseInt(classObj.selRow)-1 : 0;
				if(next_target<0) next_target = 0;
				target = classObj.get_object(next_target);
				target['row'] = (target['obj'] != select['obj']) ? target['row']+1 : target['row'];

				if(classObj.selRow==0) {
					alert("�ֻ��� �׸��Դϴ�."+SPACE);
					document.body.focus();
					return false;
				}
				else {
					// �˾����� �ʱ�ȭ
					classObj.reset_media_contents();
					var select_body = (select['bind']=='') ? select['body']-1 : select['body'];
					copyNode = select['obj'].rows[select['row']].cloneNode(true);
					select['obj'].deleteRow(select['row']);
					classObj.popupBody.insertRow(select_body).insertCell(0).innerHTML = classObj.get_template(copyNode.outerHTML);
					select['body'] += 1;
				}
				break;

			//###############################################################
			//## �Ʒ� ����� �̵���
			case "down": case "bottom":
			//###############################################################
				var nos = document.getElementsByName('chk_no[]');
				next_target = (mode=="down") ? parseInt(classObj.selRow)+1 : nos.length-1;
				if(next_target>=nos.length-1) next_target = nos.length-1;
				target = classObj.get_object(next_target);

				if(classObj.selRow==nos.length-1) {
					alert("������ �׸��Դϴ�."+SPACE);
					document.body.focus();
					return false;
				}
				else {
					// �˾����� �ʱ�ȭ
					classObj.reset_media_contents();
					var target_body = (select['bind']=='') ? target['body']+1 : target['body']; 
					copyNode = select['obj'].rows[select['row']].cloneNode(true);
					select['obj'].deleteRow(select['row']);
					classObj.popupBody.insertRow(target_body).insertCell(0).innerHTML = classObj.get_template(copyNode.outerHTML);
				}
				break;
		}
		// ������ �����Ѵٸ� �ٵ𿡼� ����
		if(select['obj'].rows.length==0) classObj.popupBody.deleteRow(select['body']);
		classObj.rebuild_event(true); // �̺�Ʈ ���Ҵ�
	}, 0);
}

// ���õ� �˾� ���� - ����Ʈ �ϴ� ���� ����
POPUP.prototype.remove_item = function(datas) {
	for(var i=this.popupItems.length-1; i>=0; i--) {
		var trs = this.popupItems[i].rows;
		for(var j=trs.length-1; j>=0; j--) {
			if(in_array(trs[j].firstChild.getElementsByTagName("input")[0].value, datas)==false) continue;
			this.popupItems[i].deleteRow(j);
			// ���� ����
			if(this.popupItems[i].rows.length>0) continue;
			for(var body_row=0; body_row<this.popupBody.rows.length; body_row++) {
				if(this.popupBody.rows[body_row].cells[0].innerHTML != this.popupItems[i].parentNode.innerHTML) continue;
				this.popupBody.deleteRow(body_row);
				break;
			}
		}
	}
}

// ��� �� ������ ����Ʈ ��ŷ
POPUP.prototype.screen_blind = function(mode) {
	if(mode===true) {
		var _height = parseInt(document.body.clientHeight,10);
		if(parseInt(document.body.scrollHeight,10)>_height) _height = parseInt(document.body.scrollHeight,10);
		$('screenBlindDiv').style.height = _height + "px";
		$('screenBlindDiv').style.marginTop = "-" + parseInt(document.body.scrollTop,10) + "px";
		this.change_display("screenBlindDiv", true);

		// SELECT ��ü �����
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

// �˾� ���̾� ���
POPUP.prototype.change_display = function(el, val) {
	if(val===true||val===false) {
		var obj = $(el);
		obj.style.display = val ? "block" : "none";
		if(val===false) {
			if(el=="registFrmDiv") this.screen_blind(false);
			return false;
		}
		if(el=="registFrmDiv") this.screen_blind(true);
		// ������ �߾ӿ� ����
		obj.style.top = (obj.offsetHeight>document.body.clientHeight) ? document.body.scrollTop : document.body.scrollTop+(document.body.clientHeight-obj.offsetHeight)/2 + "px";
		obj.style.left = (obj.offsetWidth>document.body.clientWidth) ? document.body.scrollLeft : document.body.scrollLeft+(document.body.clientWidth-obj.offsetWidth)/2 + "px";
	}
	else {
		var _val = $(el).style.display;
		$(el).style.display = (_val=="none") ? "block" : "none";
		this.screen_blind(false);
	}
}

// �˾����� �� �� ����
POPUP.prototype.change_form = function(el) {
	var form = this.registFrm;
	// �����Է� ���
	if(el.value =="text") {
		form.width.readOnly = form.height.readOnly = form.width_type.disabled = form.height_type.disabled = false;
		form.width.className = form.height.className = "enable";
		document.getElementById('iframecontent').contentWindow.document.body.innerHTML = '';
	}
	// ��Ų�Է� ���
	else {
		form.width_type.value = form.height_type.value = "exact";
		classObj.change_size_type(form.width_type);
		classObj.change_size_type(form.height_type);
		form.width.readOnly = form.height.readOnly = form.width_type.disabled = form.height_type.disabled = true;
		form.width.className = form.height.className = "disable";
		document.getElementById('iframecontent').contentWindow.document.body.innerHTML = $(el.value).innerHTML;
	}
}

// �˾� ���/���� - ������ ����Ʈ�� ���� ����
POPUP.prototype.regist_item = function(modify) {
	var form = this.registFrm;
	form.reset();

	// ������â �� ����
	document.getElementById('iframecontent').contentWindow.document.body.innerHTML = '';
	if(modify===true) {
		form.no.value = this.selNo;
		form.mode.value = "update";
		// �ʱⰪ ������� ����
		var spans = this.selObject.getElementsByTagName("span");
		var key = {'width':0, 'height':1, 'type':2, 'use_date':3, 'sdate':4, 'edate':5, 'media':6, 'content':6};

		form.title.value = this.selObject.getElementsByTagName("label")[0].innerHTML;

		// ���밪 ����
		form.width_type.value = spans[key['width']].getAttribute('type');
		form.height_type.value = spans[key['height']].getAttribute('type');
		if(!form.width_type.value) form.width_type.value = "exact";
		if(!form.height_type.value) form.height_type.value = "exact";
		this.change_size_type(form.width_type);
		this.change_size_type(form.height_type);
		form.width.value = parseInt(spans[key['width']].innerHTML, 10);
		form.height.value = parseInt(spans[key['height']].innerHTML, 10);

		// �˾����� ����
		for(var i=0; i<form.popup_type.length; i++) {
			if(form.popup_type[i].value != spans[key['type']].type) continue;
			form.popup_type[i].checked = true;
			break;
		}

		// ���� ����
		for(var i=0; i<form.period.length; i++) {
			if(form.period[i].value == spans[key['use_date']].value) {
				form.period[i].checked = true;
				this.change_period_set(form.period[i]);
				// �Ⱓ������ �Է�
				if(spans[key['sdate']].innerHTML) {
					var sdate = spans[key['sdate']].innerHTML.replace(/\./g, '-').replace(/����/g, '').split(' ');
					form.period_sdate.value = sdate[0];
					form.period_shour.value = new String(sdate[1]).replace(/��/g, '');
					form.period_sminute.value = sdate[2];
				}
				if(spans[key['edate']].innerHTML) {
					var edate = spans[key['edate']].innerHTML.replace(/\./g, '-').replace(/����/g, '').split(' ');
					form.period_edate.value = edate[0];
					form.period_ehour.value = new String(edate[1]).replace(/��/g, '');
					form.period_eminute.value = edate[2];
				}
			}
		}

		// ��ϵ� �˾� ���� �Է�
		// �� �� ȯ
		this.change_form(form.popup_type[0].checked ? form.popup_type[0] : form.popup_type[1]);
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
	}
	// �ű� ��Ͻ� ���ʱ�ȭ
	else {
		form.no.value = '';
		form.mode.value = "insert";
		form.content.value = '';
		form.width_type.value = form.height_type.value = "exact";
	
		this.change_size_type(form.width_type);
		this.change_size_type(form.height_type);
		this.change_form(form.popup_type[0]);
		this.change_period_set(form.period[0]);

		// ���/���� �� ���̱�
		this.change_display('registFrmDiv', true);
	}
}

// �˾� ���/������ �ݿ�
POPUP.prototype.append_popup_item = function(mode, popup_item) { // mode { insert | update }
	switch(mode) {
		// ��� �� ����Ʈ �߰� �۾�
		case "insert":
			// ���õ� �˾��� ������ - ��ó���� �߰�
			if(this.selNo!=null) this.selObject.className = ''; // �������õ� �˾� �ʱ�ȭ
			this.popupBody.insertRow(0).insertCell(0).innerHTML = this.get_template("<tr><td>"+popup_item['item']+"</td></tr>");
			/*
			if(this.selNo==null) this.popupBody.insertRow(0).insertCell(0).innerHTML = this.get_template("<tr><td>"+popup_item['item']+"</td></tr>");
			else {
				// ���õ� �˾��� ������ - ���õ� �˾� �Ʒ��� �߰�
				this.selObject.className = ''; // �������õ� �˾� �ʱ�ȭ
				if(this.selBind=='' || this.selBind==null) {
					var target = this.get_object(this.selRow+1);
					if(isNaN(target['body'])) target['body'] = this.popupBody.rows.length;
					this.popupBody.insertRow(target['body']).insertCell(0).innerHTML = this.get_template("<tr><td>"+popup_item['item']+"</td></tr>");
				}
				else {
					var target = this.get_object(this.selRow);
					target['obj'].insertRow(target['row']+1).insertCell(0).innerHTML = popup_item['item'].replace(/value=''/g, "value="+this.selBind); // ���ε� ��ȣ �߰�
				}
			}
			*/
			this.selNo = popup_item['no'];
			this.selObject = this.popupBody.rows[0].cells[0].getElementsByTagName("table")[1];
			break;

		// ���� �� ����Ʈ ���� �۾�
		case "update":
			this.selObject.parentNode.innerHTML = popup_item['item'];
			break;
	}
	this.rebuild_event();
	this.selObject.className = this.selClass;
	this.change_display('registFrmDiv', false); // ���/������ �ݱ�
}

// �˾� �̸����� - �˾�������
POPUP.prototype.preview_popup = function(regist_mode) { // mode { true  or  false }
	setTimeout(function() {
		var obj = null;
		var width, height;
		var pop_width_add = 36; // ���� �߰�(��ũ�ѹ�)
		var pop_height_add = 157; // ���� �߰�(Ÿ��Ʋ��/�ϴܹ�)
		var popup_title = new String();
		var popup_content = new String();

		// �˾� ��� ���� ���
		if(regist_mode==true) {
			var form = classObj.registFrm;
			var size_types = {"pixel": "px", "exact": "%"};
			width = form.width.value+size_types[form.width_type.value];
			height = form.height.value+size_types[form.width_type.value];
			// �ٹٲ� ���� �±� ���� - ����!
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
		// ����Ʈ�� ���
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

		// �ڵ��� ����� ��� �⺻�� �Ҵ�
		if(width==0 || new String(width).indexOf('%')!=-1) width = obj.offsetWidth;
		if(height==0 || new String(height).indexOf('%')!=-1) height = obj.offsetHeight;

		// �ּһ����� ����
		if(width<classObj.minPrevWidth) width = classObj.minPrevWidth;

		// �̸����� ���ø�(���)
		var preview_top = "<html>\
		<head>\
		<title>�˾��̸�����</title>\
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

		// �̸����� ���ø�(�ϴ�)
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
									<td nowrap><label disabled style='padding:2 8 0 0px;height:14px;font-size:9pt;cursor:pointer'>�����Ϸ� �׸�����</label></td>\
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
			<td style='color:white' style='padding-right:7px'><a style='cursor:pointer' onClick='self.close()'><img src='../../Libs/_images/btn_close.gif' border='0' alt='�ݱ�'></a></td>\
		</tr>\
		</table>\
		</body>\
		</html>";

		// �̸����� â ����
		if(classObj.previewPop!=null) classObj.previewPop.close();
		classObj.previewPop = window.open("about:blank", "preivew_pop", "width="+(parseInt(width,10)+pop_width_add)+"px,height="+(parseInt(height,10)+pop_height_add)+"px, scrollbars=yes");

		// �̸����� â�� ���� �Է�
		if(popup_content.toLowerCase().indexOf("<script")==-1) { // �ܼ��� HTML ���ڿ��� ���
			classObj.previewPop.document.write(preview_top + popup_content + preview_bottom);
		}
		else { // JavaScript �ڵ带 ������ ���ڿ��� ���
			classObj.previewPop.document.write(popup_content);
			setTimeout(function() { // JavaScript �ҽ��� �̸������� ��쿡 ���
				var preview_content = classObj.previewPop.document.body.innerHTML;
				classObj.previewPop.document.body.innerHTML = '';
				classObj.previewPop.document.write(preview_top + preview_content + preview_bottom);
			}, 0);
		}
		classObj.previewPop.focus();

	}, 0);
}

// �˾� ���Ⱓ ���� ����
POPUP.prototype.change_period_set = function(el) {
	$('period_field').style.display = (el.value=="yes") ? "block" : "none";
	$('period_tip_field').style.display = (el.value=="yes") ? "none" : "block";
}

// ��ü �˾� ����/����
POPUP.prototype.checkAll = function(val) {
	var nos = document.getElementsByName("chk_no[]");
	for(var i=0, j=0; i<nos.length; i++) {
		var item = nos[i];
		if(item.disabled==true) continue;
		item.checked = (val=="cross") ? !item.checked : val;
	}
}

// ���õ� �˾� �ε��� �� ��������
POPUP.prototype.get_checkAll = function() {
	var items = new Array();
	var nos = document.getElementsByName("chk_no[]");
	for(var i=0, j=0; i<nos.length; i++) { 
		var item = nos[i];
		if(item.checked==true) items.push(item.value);
	}
	return items.join("__");
}

// �˾�ũ�� ���� ����
POPUP.prototype.change_size_type = function(el) {
	var obj = $(el.name.replace(/_type/gi, ''));
	obj.style.display = in_array(el.value, new Array("exact","auto")) ? "none" : "inline";
	switch(el.value) {
		case "pixel":
			if(obj.value=='') {
				//var info = $('mediaInfo').innerHTML.split(',');
				//info = info[0].split(' �� ');
				//obj.value = (obj.name=="width") ? info[0]>this.maxPrevWidth ? this.maxPrevWidth : info[0].replace(/--/g, 100) : info[1].replace(/--/g, 100);
			}
			break;
		case "percent":
			if(obj.value=='' || obj.value>100) obj.value = 100;
			break;
	}
	this.set_resize();
}

// �˾�ũ�� ������ ����
POPUP.prototype.get_media_size = function(info) {
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

// �˾� ������ �缳��
POPUP.prototype.set_resize = function() {
	if(this.registFrm.popup_type[0].checked===false) {
		document.body.focus();
		return false;
	}
}

// �������� ���� - ���� ����
POPUP.prototype.save_settings = function(silent) {
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

// ���õ� �˾� ���� ó��(���/�̻��, ��â/��â, ����) - Ajax ó��
POPUP.prototype.ajax_process = function(mode, multi, val) {
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
					alert("��뿩�θ� ������ �˾��� �����Ͽ� �ֽʽÿ�."+SPACE);
					document.body.focus();
					return false;
				}
				if(modeValue==undefined) {
					var cross_views = {'unused': "yes", 'use': "no"};
					var modeValue = cross_views[classObj.selObject.getElementsByTagName("img")[2].value];
				}
				var url = "./multiProcess.html?mode=view&data="+data+"&val="+modeValue;
				break;
			case "delete": // ������
				var data = (multi===true) ? classObj.get_checkAll() : classObj.selNo;
				if(data.length<1) {
					alert("�����Ͻ� �˾��� �����Ͽ� �ֽʽÿ�."+SPACE);
					document.body.focus();
					return false;
				}
				var url = "./multiProcess.html?mode=delete&data="+data;
				break;
			default:
				document.body.focus();
				return false;
		}
		// ����
		var modes = {'view':"�� ��뿩�θ� ����", 'delete':"�� ����"};
		if(!confirm("�����Ͻ� �˾�"+modes[mode]+" �Ͻðڽ��ϱ�?"+SPACE)) {
			document.body.focus();
			return false;
		}
		// Ajax ó��
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
							// ������ ������ ����Ʈ�� �ݿ�
							case "delete":
								classObj.remove_item(nos);
								break;
						}
					}
					alert(resultData.firstChild.nodeValue+SPACE); // ��� ���
				}
				myRequest = null;
				document.body.focus();
			}
		});

	}, 0);
}

// �˾� �̺�Ʈ �Ҵ� - ���/����/���� ����/���ε� ����
POPUP.prototype.rebuild_event = function(reset) {

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
	else {
		// �����Է� ��� �˾� ó��
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

	// �̺�Ʈ �Ҵ�
	var items = document.getElementsByName('item');
	for(var i=0; i<items.length; i++) {
		Event.stopObserving(items[i], 'click', this.select_item); // �̺�Ʈ ����
		Event.observe(items[i], 'click', this.select_item); // �̺�Ʈ �Ҵ�
	}
	document.body.focus();
}