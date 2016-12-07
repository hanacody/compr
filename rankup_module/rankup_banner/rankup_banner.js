//######################################################################
//# ���α׷��� : ��ũ�� �ַ�� ��ʰ��� ���α׷�
//# ���� : v1.5, r101220
//# ������ : C2tfiW ( Kurokisi )
//# ���� ������Ʈ : 2010.12.20
//# ���̼��� : �ַ�Ǳ��Ű��� �ƴ� ��� ��ũ�����κ��� ����� �����ž� �մϴ�.
//######################################################################
// ��� Ŭ���� ����
var BANNER = function() {
	this.registFrm = null;			// ���/������
	this.settingFrm = null;		// ��� ������
	this.bannerBody = null;		// ��� �ٵ� ���̺�
	this.bannerItems = null;		// ��� ������ ���̺�
	this.selClass = "selClass";	// ������ ����� ��Ÿ��
	this.selObject = null;			// ������ ��ʰ�ü
	this.selRow = null;			// ������ ����� �� ��ȣ
	this.selNo = null;				// ������ ����� �ε��� ��ȣ
	this.selBind = null;			// ������ ����� ���ε� ��ȣ
	this.maxBind = 100;			// ���ε� ���� ����
	this.maxWidth = null;		// ��� ���λ����� ����
	this.maxPrevWidth = 736;	// �̸����� ���λ����� ����( 100% �������� ������ : ������� width ������ -24px)
	this.previewPop = null;		// �̸����� �˾� ��ü
}

// ��� ����
BANNER.prototype.select_item = function(arg) {
	var el = arg.target||arg.srcElement;
	var type = el.type;
	try {
		// ������Ʈ ����
		do { el = el.parentNode; } while(el.getAttribute('id')!="item");
		var click_no = el.parentNode.getElementsByTagName("input")[0].value; // ��� �ε��� ��ȣ
		var obj = el.parentNode.parentNode.parentNode;
	}
	catch(e) {
		// �־��� ���� ���� ������ ��� ����
		return false;
	}
	// ������ ����� üũ�ڽ� ���
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
		el.className = classObj.selClass; // ������ ��� �缳��
		classObj.selObject = el;
		classObj.selNo = el.getElementsByTagName('input')[0].value; // �ε��� ��ȣ
		classObj.selBind = el.getElementsByTagName('input')[1].value; // ���ε� ��ȣ

		var nos = document.getElementsByName('chk_no[]');
		for(var row=0; row<nos.length; row++) if(nos[row].value==classObj.selNo) { classObj.selRow = row; break; }
	}
}

// ������ �� OBJECT �������� - ���� ����� ���
BANNER.prototype.get_object = function(target) {
	var body_row = null;
	var target_obj = null;
	var target_bind = '';
	var target_row = 0;
	// ��� ����Ʈ �߿��� ���° ���̺��� ���° ���������� üũ
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
	// ��ü ��ʹٵ� �߿��� ���° ROW �� �ִ��� üũ
	if(target_obj!=null) {
		for(body_row=0; body_row<this.bannerBody.rows.length; body_row++) {
			if(this.bannerBody.rows[body_row].cells[0].innerHTML == target_obj.parentNode.parentNode.innerHTML) break;
		}
	}
	return {"obj":target_obj, "row":parseInt(target_row,0), "bind":target_bind, "body":parseInt(body_row,0)};
}

// ��� ���ø�
BANNER.prototype.get_template = function(str, bind) {
	var bind_tool = (bind===true) ? "<div><img src='./img/order_high.gif' onClick=\"classObj.set_bind_direction(this,'up')\" align='absmiddle' hspace='1'><img src='./img/order_low.gif' onClick=\"classObj.set_bind_direction(this,'down')\" align='absmiddle' hspace='1'><img src='./img/bt_bind_x.gif'onClick=\"classObj.resolve_item(this)\" align='absmiddle' hspace='1'></div>" : '';
	var string = "\
	<span id='bindToolBox'>"+bind_tool+"</span>\
	<table name='bannerItem' id='bannerItem' width='100%' cellpadding='0' cellspacing='0' border='0' bgcolor='white'>\
	<tbody>"+str+"\
	</tbody>\
	</table>";
	return string;
}

// ��ʿ��� �ʱ�ȭ
BANNER.prototype.reset_media_contents = function() {
	// �̵�� - �÷���
	//var media_datas = document.getElementsByName('media_data');
	//for(var i=0; i<media_datas.length; i++) media_datas[i].parentNode.getElementsByTagName('span')[0].innerHTML = '';
	// �����Է�
	var texts = document.getElementsByName('text');
	for(var i=0; i<texts.length; i++) texts[i].innerHTML = '';
}

// ��� ���� �ٲٱ�
BANNER.prototype.set_direction = function(el, mode) {
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
					if(select['obj'].getElementsByTagName('input')[1].value=='') {
						alert("�ֻ��� �׸��Դϴ�."+SPACE);
						document.body.focus();
						return false;
					}
					else {
						// ��ʿ��� �ʱ�ȭ
						classObj.reset_media_contents();
						// �ֻ������� ���ε�� ��ʸ� ������� �� ���
						select['obj'].rows[select['row']].getElementsByTagName('input')[1].value = ''; // ���ε� ��ȣ ����
						copyNode = select['obj'].rows[select['row']].cloneNode(true);
						select['obj'].deleteRow(select['row']);
						classObj.bannerBody.insertRow(0).insertCell(0).innerHTML = classObj.get_template(copyNode.outerHTML);
					}
				}
				else {
					// �̿��ϴ� ���ε� ��ʿ� �������� ���� ���
					if(target['bind'] && (target['body']==select['body'] || (target['body']!=select['body'] && confirm("�̿��ϴ� ������� �׷쿡 �����Ͻðڽ��ϱ�?"+SPACE)))) {
						// ��ʿ��� �ʱ�ȭ
						classObj.reset_media_contents();
						// Ÿ�ٿ� ���õ� ��� �߰�
						select['obj'].rows[select['row']].getElementsByTagName('input')[1].value = target['bind']; // ���ε� ��ȣ �ο�
						copyNode = select['obj'].rows[select['row']].cloneNode(true);
						select['obj'].deleteRow(select['row']);
						target['obj'].insertRow(target['row']).replaceNode(copyNode);
					}
					else {
						// ��ʿ��� �ʱ�ȭ
						classObj.reset_media_contents();
						// ���ε�� ��� �� ��ġ�� ����
						var select_body = (select['bind']=='') ? select['body']-1 : select['body'];
						select['obj'].rows[select['row']].getElementsByTagName('input')[1].value = ''; // ���ε� ��ȣ ����
						copyNode = select['obj'].rows[select['row']].cloneNode(true);
						select['obj'].deleteRow(select['row']);
						classObj.bannerBody.insertRow(select_body).insertCell(0).innerHTML = classObj.get_template(copyNode.outerHTML);
						select['body'] += 1;
					}
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
					if(select['obj'].getElementsByTagName('input')[1].value=='') {
						alert("������ �׸��Դϴ�."+SPACE);
						document.body.focus();
						return false;
					}
					else {
						// ��ʿ��� �ʱ�ȭ
						classObj.reset_media_contents();
						// ���������� ���ε� ��ʸ� ������� �� ���
						select['obj'].rows[select['row']].getElementsByTagName('input')[1].value = ''; // ���ε� ��ȣ ����
						copyNode = select['obj'].rows[select['row']].cloneNode(true);
						select['obj'].deleteRow(select['row']);
						newCell = classObj.bannerBody.insertRow(classObj.bannerBody.rows.length).insertCell(0);
						newCell.innerHTML = classObj.get_template(copyNode.outerHTML);
					}
				}
				else {
					// �̿��ϴ� ���ε�� ��ʿ� �������� ���� ���
					if(target['bind'] && (target['body']==select['body'] || (target['body']!=select['body'] && confirm("�̿��ϴ� ������� �׷쿡 �����Ͻðڽ��ϱ�?"+SPACE)))) {
						// ��ʿ��� �ʱ�ȭ
						classObj.reset_media_contents();
						// Ÿ�ٿ� ���õ� ��� �߰�
						select['obj'].rows[select['row']].getElementsByTagName('input')[1].value = target['bind']; // ���ε� ��ȣ �ο�
						copyNode = select['obj'].rows[select['row']].cloneNode(true);
						select['obj'].deleteRow(select['row']);
						target['obj'].insertRow(target['row']).replaceNode(copyNode);
					}
					else {
						// ��ʿ��� �ʱ�ȭ
						classObj.reset_media_contents();
						// ���ε�� ��� ���� ��ġ�� ����
						var target_body = (select['bind']=='') ? target['body']+1 : target['body'];
						select['obj'].rows[select['row']].getElementsByTagName('input')[1].value = ''; // ���ε� ��ȣ ����
						copyNode = select['obj'].rows[select['row']].cloneNode(true);
						select['obj'].deleteRow(select['row']);
						classObj.bannerBody.insertRow(target_body).insertCell(0).innerHTML = classObj.get_template(copyNode.outerHTML);
					}
				}
				break;
		}
		// ������ �����Ѵٸ� �ٵ𿡼� ����
		switch(select['obj'].rows.length) {
			case 0: classObj.bannerBody.deleteRow(select['body']); break;
			case 1: // ���ε尡 Ǯ�� ��쿡 �ش�
				select['obj'].getElementsByTagName('input')[1].value = ''; // ���ε� ��ȣ ����
				select['obj'].parentNode.parentNode.getElementsByTagName('span')[0].innerHTML = ''; // ���� ����
				break;
		}
		classObj.rebuild_event(true); // �̺�Ʈ ���Ҵ�
	}, 0);
}

// ���õ� ��� ���� - ����Ʈ �ϴ� ���� ����
BANNER.prototype.remove_item = function(datas) {
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

// ���ε� ��� ���� �ٲٱ�
BANNER.prototype.set_bind_direction = function(el, mode) {
	// ���� ���õ� bannerBody.row �� üũ
	var select = null;
	var copyNode = null;
	for(var i=0; i<this.bannerBody.rows.length; i++) {
		if(this.bannerBody.rows[i].firstChild.innerHTML!=el.parentNode.parentNode.parentNode.innerHTML) continue;
		select = i; break;
	}
	switch(mode) {
		//###############################################################
		//## �� ����� �̵���
		case "up":
		//###############################################################
			if(select!=null && select<=0) {
				alert("�ֻ��� �׷��Դϴ�."+SPACE);
				document.body.focus();
				return false;
			}
			// ��ʿ��� �ʱ�ȭ
			this.reset_media_contents();
			copyNode = this.bannerBody.rows[select].cloneNode(true);
			this.bannerBody.deleteRow(select);
			this.bannerBody.insertRow(select-1).replaceNode(copyNode);
			break;

		//###############################################################
		//## �Ʒ� ����� �̵���
		case "down":
		//###############################################################
			if(select!=null && select>=this.bannerBody.rows.length-1) {
				alert("������ �׷��Դϴ�."+SPACE);
				document.body.focus();
				return false;
			}
			// ��ʿ��� �ʱ�ȭ
			this.reset_media_contents();
			copyNode = this.bannerBody.rows[select].cloneNode(true);
			this.bannerBody.deleteRow(select);
			this.bannerBody.insertRow(select+1).replaceNode(copyNode);
			break;
	}
	this.rebuild_event(true);
}

// ���ε�� ��� - ���⸦ �ϸ� ����Ʈ ��ܿ� ��ġ
BANNER.prototype.bind_item = function() {
	var datas = new Array();
	var items = document.getElementsByName("chk_no[]");
	for(var i=0; i<items.length; i++) if(items[i].checked==true) datas.push(items[i].value);
	if(datas.length<2) {
		alert("������ʷ� ������ ��ʸ� 2�� �̻� �����Ͽ� �ֽʽÿ�."+SPACE);
		document.body.focus();
		return false;
	}
	if(!confirm("�����Ͻ� ��ʸ� ������ʷ� �����Ͻðڽ��ϱ�?"+SPACE)) {
		document.body.focus();
		return false;
	}

	// ��ʿ��� �ʱ�ȭ
	this.reset_media_contents();

	// ���ε� ��ȣ ����
	var newBind = null;
	var bindInfos = {'items':null, 'nos':new Array()};
	bindInfos['items'] = document.getElementsByName('bind_no[]');
	for(var i=0; i<bindInfos['items'].length; i++) {
		if(bindInfos['items'][i].value=='') continue;
		bindInfos['nos'].push(bindInfos['items'][i].value);
	}
	for(i=1; i<=this.maxBind; i++) if(!in_array(i, bindInfos['nos'])) { newBind = i; break; }
	if(newBind == null) {
		alert("������ʷ� ������ �� �ִ� �ִ� ����( "+this.maxBind+"�� )�� �ʰ��Ͽ����ϴ�."+SPACE);
		document.body.focus();
		return false;
	}
	// ������ ��� ����
	var bind_items = '';
	for(var i=0; i<this.bannerItems.length; i++) {
		var trs = this.bannerItems[i].rows;
		for(var j=0; j<trs.length; j++) {
			if(in_array(trs[j].firstChild.getElementsByTagName("input")[0].value, datas)==false) continue;
			trs[j].getElementsByTagName('input')[1].value = newBind; // ���ε� ��ȣ �Ҵ�
			bind_items += trs[j].cloneNode(true).outerHTML;
		}
	}
	// �ٻ��� - ����Ʈ �ϴ� ���� ����
	this.remove_item(datas);

	// ������ ��ʸ� ��ܿ� �߰�
	var new_cell = this.bannerBody.insertRow(0).insertCell(0);
	new_cell.innerHTML = this.get_template(bind_items, true); // ���ε�
	document.body.scrollTop = 0;
	this.rebuild_event(true);
}

// ���ε� ���� - ���� ��ġ�� �������� ����
BANNER.prototype.resolve_item = function(el) {
	if(!confirm("�����Ͻ� �׷��� ������� ������ �����Ͻðڽ��ϱ�?"+SPACE)) {
		document.body.focus();
		return false;
	}
	el = el.parentNode.parentNode.parentNode; // 2010.12.20 modified

	// ��ü ��ʹٵ� �߿��� ���° ROW �� �ִ��� üũ
	for(var body_row=0; body_row<this.bannerBody.rows.length; body_row++) {
		if(this.bannerBody.rows[body_row].cells[0].innerHTML == el.innerHTML) break;
	}
	body_row+=1;
	var obj = el.getElementsByTagName("table")[0];

	for(var row=obj.rows.length-1; row>=0; row--) {
		obj.rows[row].getElementsByTagName("input")[1].value = ''; // ���ε� �� ����
		if(row==0) el.getElementsByTagName("span")[0].innerHTML = '';
		else {
			var copyNode = obj.rows[row].cloneNode(true);
			obj.deleteRow(row);
			this.bannerBody.insertRow(body_row).insertCell(0).innerHTML = this.get_template(copyNode.outerHTML);
		}
	}
	this.rebuild_event(true);
}

// ��� �� ������ ����Ʈ ��ŷ
BANNER.prototype.screen_blind = function(mode) {
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

// ��� ���̾� ���
BANNER.prototype.change_display = function(el, val) {
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

// ������� �� �� ����
BANNER.prototype.change_form = function(el) {
	var form = this.registFrm;
	// �����Է� ��� ��
	if(el.value =="text") {
		$('editorBox').style.display = "block";
		$('previewBox').style.display = "none";
		form.width.readOnly = form.height.readOnly = form.width_type.disabled = form.height_type.disabled = form.attached.readOnly = true;
		form.width.className = form.height.className = form.attached.className = "disable";
		//form.address.removeAttribute("required");
		form.on_attached.removeAttribute("required");
		form.content.setAttribute("required", "required");
	}
	// �̵���� ��� ��
	else {
		$('editorBox').style.display = "none";
		$('previewBox').style.display = "block";
		form.width.readOnly = form.height.readOnly = form.width_type.disabled = form.height_type.disabled = form.attached.readOnly = false;
		form.width.className = form.height.className = form.attached.className = "enable";
		//form.address.setAttribute("required", "required"); - 2009.03.06 ����
		form.on_attached.setAttribute("required", "required");
		form.content.removeAttribute("required");
	}
	// ���ܻ��� ó��
	if(el.value=="text" || (el.value!="text" && $('previewItemBox').getElementsByTagName("object")[0]!=undefined)) {
		//form.address.removeAttribute("required");
		form.on_attached.removeAttribute("required");
		form.address.readOnly = form.popup_banner.disabled = true;
		form.address.className = "disable";
	}
	else {
		//form.address.setAttribute("required", "required");
		if(el.value!="text" && ($('previewItemBox').getElementsByTagName("object")[0]!=undefined || $('previewItemBox').getElementsByTagName("img")[0]!=undefined)) form.on_attached.removeAttribute("required");
		form.address.readOnly = form.popup_banner.disabled = false;
		form.address.className = "enable";
	}
}

// ��� ���/���� - ������ ����Ʈ�� ���� ����
BANNER.prototype.regist_item = function(modify) {
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

		// ������� ����
		for(var i=0; i<form.banner_type.length; i++) {
			if(form.banner_type[i].value != spans[key['type']].type) continue;
			form.banner_type[i].checked = true;
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

		// ��ϵ� ��� ���� �Է�
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
					var address = spans[key['media']].getAttribute("address");
					form.address.value = address ? address : form.address.getAttribute('default');
					form.popup_banner.checked = spans[key['media']].getAttribute("target")=="_blank" ? true : false;
					// ��� �̹��� ����
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
		itembox.innerHTML = "<i disabled style=\"padding:0px 4px 0px 4px;\"><nobr>NULL</nobr></i>";

		this.change_size_type(form.width_type);
		this.change_size_type(form.height_type);
		this.change_form(form.banner_type[0]);
		this.change_period_set(form.period[0]);

		// ���/���� �� ���̱�
		this.change_display('registFrmDiv', true);
	}
}

// ��� ���/������ �ݿ�
BANNER.prototype.append_banner_item = function(mode, banner_item) { // mode { insert | update }
	switch(mode) {
		// ��� �� ����Ʈ �߰� �۾�
		case "insert":
			// ���õ� ��ʰ� ������ - ��ó���� �߰�
			if(this.selNo==null) this.bannerBody.insertRow(0).insertCell(0).innerHTML = this.get_template("<tr><td>"+banner_item['item']+"</td></tr>");
			else {
				// ���õ� ��ʰ� ������ - ���õ� ��� �Ʒ��� �߰�
				this.selObject.className = ''; // �������õ� ��� �ʱ�ȭ
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

// ��� �̸����� - �˾�������
BANNER.prototype.preview_banner = function(regist_mode) { // mode { true  or  false }
	setTimeout(function() {
		var obj = null;
		var width, height, outline; // 2010.12.20 added
		var pop_width_add = 25; // ���� �߰�(��ũ�ѹ�)
		var pop_height_add = 100; // ���� �߰�(Ÿ��Ʋ��/�ϴܹ�)
		var banner_content = new String();
		// ��� ��� ���� ���
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
		<title>��ʹ̸�����</title>\
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

// ��� ���Ⱓ ���� ����
BANNER.prototype.change_period_set = function(el) {
	$('period_field').style.display = (el.value=="yes") ? "block" : "none";
	$('period_tip_field').style.display = (el.value=="yes") ? "none" : "block";
}

// ��ü ��� ����/����
BANNER.prototype.checkAll = function(val) {
	var nos = document.getElementsByName("chk_no[]");
	for(var i=0, j=0; i<nos.length; i++) {
		var item = nos[i];
		if(item.disabled==true) continue;
		item.checked = (val=="cross") ? !item.checked : val;
	}
}

// ���õ� ��� �ε��� �� ��������
BANNER.prototype.get_checkAll = function() {
	var items = new Array();
	var nos = document.getElementsByName("chk_no[]");
	for(var i=0, j=0; i<nos.length; i++) {
		var item = nos[i];
		if(item.checked==true) items.push(item.value);
	}
	return items.join("__");
}

// ÷������ ���ε� - �̸����� ����
BANNER.prototype.post_attached = function(el) {
	if(el.getAttribute("filter")!=null) {
		var filters = el.getAttribute("filter").toLowerCase().split(",");
		var exts = el.value.toLowerCase().split(".");
		if(in_array(exts[exts.length-1], filters)==false) {
			alert("��ʷ� ����� �� ���� �̵�� ���������Դϴ�."+SPACE);
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

// ���ũ�� ���� ����
BANNER.prototype.change_size_type = function(el) {
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

// ���ũ�� ������ ����
BANNER.prototype.get_media_size = function(info) {
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
BANNER.prototype.append_flash_object = function(obj, m, w, h, o) { // 'outline' - 2010.12.20 added
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
BANNER.prototype.preview_attached = function(file_name, info) {
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
			//form.address.removeAttribute("required");
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
			//form.address.setAttribute("required", "required");
	}
	// �����ڽ� ó��
	$('mediaInfo').innerHTML = info['text'];
}

// ��� ������ �缳��
BANNER.prototype.set_resize = function() {
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

// ��� �ƿ����� Ȱ��ȭ ����
BANNER.prototype.set_media_outline = function(reset) {
	var itembox = $('previewItemBox');
	if(reset==true) {
		this.registFrm.mediaOutlineChecker.checked = false;
		itembox.style.border = "#555555 1px solid";
	}
	itembox.style.border = in_array(itembox.style.border, new Array("", "#fdfdfd 1px solid")) ? "#555555 1px solid" : "#fdfdfd 1px solid";
}

// ����Ʈ���� ��ʿ� �ƿ����� Ȱ��ȭ
BANNER.prototype.set_banner_outline = function() {
	var media = document.getElementsByName('media');
	//for(var i=0; i<media.length; i++) media[i].style.border = media[i].style.border=="" ? "#555555 1px solid" : ""; // ���
	for(var i=0; i<media.length; i++) {
		if(!$(media[i]).hasClassName('banner_outline')) $(media[i]).addClassName('banner_outline');
	}
}

// ��� ��������� �ȳ� ���
BANNER.prototype.open_size_guide = function() {
	var obj = $('bannerSizeGuideDiv');
	obj.style.display = obj.style.display=="block" ? "none": "block";
	if(obj.style.display=="block") obj.focus();
}

// ��� ��������� ����
BANNER.prototype.apply_media_size = function(width, height) {
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

// ��� ���������� ����
BANNER.prototype.set_real_size = function() {
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

// ��� Ǯ������ ����
BANNER.prototype.set_full_size = function() {
	var form = this.registFrm;
	if(form.banner_type[1].checked===true) {
		alert("�����Է� ��忡���� ����� �����Ͻ� �� �����ϴ�."+SPACE);
		document.body.focus();
		return false;
	}
	form.width.value = form.height.value = 100;
	form.width_type.value = form.height_type.value = "percent";
	this.change_size_type(form.width_type);
	this.change_size_type(form.height_type);
}

// �������� ���� - ���� �� ���ε� ����
BANNER.prototype.save_settings = function(silent) {
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

// ���õ� ��� ���� ó��(���/�̻��, ��â/��â, ����) - Ajax ó��
BANNER.prototype.ajax_process = function(mode, multi, val) {
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
					alert("��뿩�θ� ������ ��ʸ� �����Ͽ� �ֽʽÿ�."+SPACE);
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
					alert("�����Ͻ� ��ʸ� �����Ͽ� �ֽʽÿ�."+SPACE);
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
					var modeValue = cross_targets[classObj.selObject.getElementsByTagName("img")[3].value]
				}
				if(!modeValue) {
					alert("â��带 ������ �� ���� ������� �Դϴ�."+SPACE);
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
		if(!confirm("�����Ͻ� ���"+modes[mode]+" �Ͻðڽ��ϱ�?"+SPACE)) {
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
								var c_img = chk_nos[i].parentNode.parentNode.getElementsByTagName("img")[2]
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
								obj.getElementsByTagName("img")[3].src = "./img/bt_"+targets[modeValue]+".gif";
								obj.getElementsByTagName("img")[3].value = targets[modeValue];
								obj.getElementsByTagName("span")[6].target = modeValue;
							}
							break;
						case "delete": // ������ ������ ����Ʈ�� �ݿ�
							classObj.remove_item(nos);
							classObj.save_settings(true); // ����/���ε����� ����
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

// ��� �̺�Ʈ �Ҵ� - ���/����/���� ����/���ε� ����
BANNER.prototype.rebuild_event = function(reset) {

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

	// �����Է� ��� ��� ó��
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

// �����Է� ��� ��� �ۺ���
BANNER.prototype.set_text_content = function(iframe) {
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