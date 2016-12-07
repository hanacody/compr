//#########################################################################
//## ��ũ�� �Խ��� Ŭ����
//#########################################################################
var RANKUP_BOARD = function() {
	this.version = "v2.1 r090909";		// ����(String)
	this.selColor = "#d6e3f1";			// ���� ����(String)
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	this.boardSelectMenu = null;		// �޴� ����Ʈ ������ ��ü(Object)
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	this.boardRegistFrm = null;			// �Խ��� �����(Object)
	this.boardSettingFrm = null;		// �Խ��� ������(Object)
	this.boardSelObject = null;			// �Խ��� ����Ʈ ������ ��ü(Object)
	this.boardItemList = null;			// �Խ��� ����Ʈ ����(Object)
	this.boardItems = null;				// �Խ��� ���� - ����/������ ����(Object)
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	this.boardCno = null;					// �Խ����� �޴� ��(Integer)
	this.boardPCno = null;				// �Խ����� �����޴� ��(Integer)
	this.boardNo = null;					// �Խ��� ��ȣ(Integer)
	this.index_url = null;					// �Խ��� ��ġ(String);
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	this.nextBoardCategoryNo = 0;	// �Խ��� �з� ��ȣ(Integer)
	this.selectBoardCategory = null;	// ���õ� �Խ��� �з�(Object)
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	this.xHandler = null;					// Ÿ�� �ڵ鷯1(Integer)
	this.yHandler = null;					// Ÿ�� �ڵ鷯2(Integer)
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	this.setCategory = null;				// ���� ���� �޴�(ī�װ�)(String)
	this.boardSettingItems = null;		// �Խ��� ���� ���� - ���ΰԽ��� ������ ����(Object)
	this.selectBoard = null;				// ���õ� �Խ���(Object);
	this.boardExtension = null;			// �Խ��� Ȯ��(Boolean);
	this.printStyles = {'':"�����ϱ�", 'text': "�ؽ�Ʈ��", 'image': "�̹�����", 'both':"ȥ����"};
}

//#########################################################################
//#  Part. I - �޴�(ī�װ�) ����/���� �κ�
//#########################################################################
// �޴� ������ ����
RANKUP_BOARD.prototype.select_item = function(event, el) { // 2011.08.11 'el' added
	if(el==undefined) el = Event.element(event); // 2011.08.11 'if(el==undefined)' added
	while(!el.tagName.match("TD")) el = el.parentNode;
	classObj.boardSelectMenu = el; // ������ �޴� ��ü

	if(el.bgColor != classObj.selColor) {
		var obj = el.parentNode.parentNode.parentNode;
		var selRow = obj.getAttribute("selRow");
		if(selRow!=null) obj.rows[parseInt(selRow, 10)].cells[0].bgColor = '';
		classObj.xHandler = null; // �۷ι� Ÿ�̸� ����

		el.bgColor = classObj.selColor;
		obj.removeAttribute("selNo");
		obj.removeAttribute("selRow");
		for(var i=0; i<obj.rows.length; i++) {
			if(el !== obj.rows[i].cells[0]) continue;
			var selNo = obj.rows[i].cells[0].getElementsByTagName("input")[0].value;
			obj.setAttribute("selNo", selNo);
			obj.setAttribute("selRow", i);
			classObj.get_items(obj.id, selNo);
			break;
		}
		// ���õ� �޴��� �ݿ�
		classObj.check_boards(el);
	}
}

// ���õ� �� ��������
RANKUP_BOARD.prototype.get_selRow = function(obj) {
	var selRow = obj.getAttribute("selRow");
	if(selRow===null||selRow==='') {
		alert("�޴��� �����ϼž� �մϴ�."+SPACE);
		return null;
	}
	return selRow;
}

// �޴� ���� �ٲٱ�
RANKUP_BOARD.prototype.set_direction = function(el, mode) {
	var obj = $(el);
	var selRow = this.get_selRow(obj);
	if(selRow===null) return;
	switch(mode) {
		case "up": case "top":
			if(selRow==0) {
				alert("�ֻ��� �޴��Դϴ�."+SPACE);
				return false;
			}
			var target = (mode=="up") ? parseInt(selRow, 10)-1 : 0;
			break;
		case "down": case "bottom":
			if(selRow==obj.rows.length-1) {
				alert("������ �޴��Դϴ�."+SPACE);
				return false;
			}
			var target = (mode=="down") ? parseInt(selRow, 10)+1 : obj.rows.length-1;
			break;
	}
	var xNode = obj.rows[selRow].cells[0].cloneNode(true);
	obj.deleteRow(selRow);
	obj.insertRow(target).insertCell(0).replaceNode(xNode);
	obj.setAttribute("selRow", target);
}

// �޴� ������ ����
RANKUP_BOARD.prototype.delete_item = function(el) {
	var obj = $(el);
	var selRow = this.get_selRow(obj);
	if(selRow===null) return false;

	var selObj = obj.rows[selRow].cells[0].getElementsByTagName("span")[1].firstChild.nodeValue;
	if(!confirm("�����Ͻ� '"+selObj+"' �޴��� �����Ͻðڽ��ϱ�?\n\n������ �ش� �޴��� �����޴����� �˻��� �� ���� �˴ϴ�."+SPACE+"\n[ ���� : üũ�ڽ� ���� - �ش�޴� �̻�� ]")) return;

	var parentObj = $("step"+(parseInt(obj.id.replace(/step/g,''), 10)-1));
	var parentNo = parentObj!=null ? parentObj.getAttribute("selNo") : 0;
	var url = "./multiProcess.ajax.html?mode=delete_category&no="+obj.getAttribute("selNo")+"&pno="+parentNo+"&items="+obj.rows.length;
	new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(transport) {
			if(transport.responseXML.getElementsByTagName("resultData")[0].getAttribute("result").match("success")) {
				obj.deleteRow(selRow);
				if(obj.rows.length>selRow) obj.rows[selRow].cells[0].bgColor = classObj.selColor;
				else if(obj.rows.length==0) obj.removeAttribute("selRow");
				else {
					selRow = obj.rows.length-1;
					obj.setAttribute("selRow", selRow);
					obj.rows[selRow].cells[0].bgColor = classObj.selColor;
				}
				selRow = obj.getAttribute("selRow");
				if(selRow==null) {
					var sp = parseInt(obj.id.replace(/step/g,''), 10);
					if(sp<=1) {
						classObj.reset_menu_option("step1"); // �ϴ� �޺��ڽ� option ������
						classObj.initialize_step($("step1"));
						classObj.check_boards(true);
					}
					else {
						pObj = $("step"+(sp-1));
						pSelRow = pObj.getAttribute("selRow");
						var pSpan = pObj.rows[pSelRow].cells[0].getElementsByTagName("span");
						for(var i=0; i<pSpan.length; i++) {
							if(pSpan[i].className!="sub") continue;
							pSpan[i].innerHTML = '';
							break;
						}
						classObj.reset_menu_option("step"+sp); // �ϴ� �޺��ڽ� option ������
						classObj.initialize_step($("step"+sp));
						classObj.check_boards(pObj.rows[pSelRow].cells[0]);
					}
				}
				else {
					var selNo = obj.rows[selRow].cells[0].getElementsByTagName("input")[0].value;
					if(selNo) {
						obj.setAttribute("selNo", selNo);
						classObj.get_items(obj.id, selNo);
					}
					classObj.check_boards(obj.rows[selRow].cells[0]);
					classObj.reset_menu_option(obj.id); // �ϴ� �޺��ڽ� option ������
				}
				alert("���������� �����Ǿ����ϴ�."+SPACE);
			}
		}
	});
}

// �޴� �� �ʱ�ȭ
RANKUP_BOARD.prototype.initialize_step = function(obj) {
	var sp = parseInt(obj.id.replace(/step/g,''), 10);
	while(obj!=null) {
		obj.removeAttribute("selNo");
		obj.removeAttribute("selRow");
		var tbody = obj.getElementsByTagName("tbody")[0];
		if(tbody.innerHTML!='') {
			obj.removeChild(tbody);
			obj.appendChild(document.createElement("tbody"));
		}
		obj = $("step"+(++sp));
	}
}

// ���� �޴� ������ ��������
RANKUP_BOARD.prototype.get_items = function(el, no, auto) { // 2011.08.11 'auto' added
	var nextStep = "step"+(parseInt(el.replace(/step/g,''), 10)+1);
	var obj = $(nextStep);
	if(obj==null) return;
	new Ajax.Request("./multiProcess.ajax.html?mode=load_category&pno="+no, {
		method: 'get',
		onSuccess: function(transport) {
			if(transport.responseXML.getElementsByTagName("resultData")[0].getAttribute("result")=="failure") {
				alert(transport.responseXML.getElementsByTagName("resultData")[0].firstChild.nodeValue+SPACE);
				window.top.location.replace("../../RAD/index.html");
				return false;
			}
			classObj.initialize_step(obj);
			var items = transport.responseXML.getElementsByTagName("item");
			if(nextStep=="step1") { // ���� �޴��� ��� �ϴ� �޺��ڽ� �ʱ�ȭ
				var move_pcate = $('move_pcate');
				if(items.length>0) move_pcate.options.length = 0;
			}
			for(var i=0; i<items.length; i++) {
				var item = items[i];
				var no = item.getAttribute("no");
				var board = classObj.get_nodeValue(item, 'bval');
				var child = classObj.get_nodeValue(item, 'cval');
				var view = classObj.get_nodeValue(item, 'pval');
				var content = classObj.get_nodeValue(item, 'content');
				var view_check = (view=="yes") ? " checked" : '';
				var child_check = (child=="yes") ? "��" : '';
				obj.insertRow(i).insertCell(0).innerHTML = "<input type='hidden' name='no[]' value='"+no+"'><input type='hidden' name='bval["+no+"]' value='"+board+"'><span class='view'><input type='checkbox' name='view["+no+"]'"+view_check+"></span><span class='cate'>"+content+"</span><span class='sub'>"+child_check+"</span>";
				// ���� �޴��� ��� �ϴ� �޺��ڽ� ä���
				if(nextStep=="step1") {
					move_pcate.options[move_pcate.options.length] = new Option(content, no);
					if(move_pcate.options.length==1) classObj.change_category($('move_pcate'), $('move_cate'));
				}
			}
			// �̺�Ʈ �ο�
			$$("#"+nextStep+" td").each(function(td) { Event.observe(td, 'click', classObj.select_item); });
			if(auto==true) { // 2011.08.11 added
				classObj.select_item(null, $('step1').rows[0].cells[0]);
			}
		}
	});
}

// �޴� ���/����
RANKUP_BOARD.prototype.regist_item = function(el, modify) {
	var obj = $(el);
	var tbl = $("registTable");
	var selNo = obj.getAttribute("selNo");
	var selRow = obj.getAttribute("selRow");
	menuRegistTable.style.display = "none";
	registFrm.reset();
	registFrm.step.value = el;

	var parentObj = $("step"+(parseInt(obj.id.replace(/step/g,''), 10)-1));
	var parentNo = parentObj!=null ? parentObj.getAttribute("selNo") : 0;
	if(modify===true) { // ����
		if(selNo==null) {
			alert("�޴��� �����ϼž� �մϴ�."+SPACE);
			return false;
		}
		registFrm.no.value = selNo;
		registFrm.pno.value = parentNo;
		var span = obj.rows[selRow].cells[0].getElementsByTagName("span");
		for(var i=0; i<span.length; i++) {
			if(span[i].className!="cate") continue;
			registFrm.content.value = span[i].firstChild.nodeValue;
			break;
		}
	}
	else { // ���
		if(parentNo==null) {
			alert("���� �޴��� �����ϼž� �մϴ�."+SPACE);
			return false;
		}
		registFrm.cval.value = obj.id!="step1" ? obj.rows.length ? "yes" : "no" : "no";
		registFrm.pno.value = parentNo;
		registFrm.no.value = '';
	}
	this.change_display('menuRegistTable', true);
	registFrm.content.focus();
	registFrm.content.select();
}

// �޴� ���/���� �Ϸ�� ó������
RANKUP_BOARD.prototype.redraw_item = function(el) {
	this.change_display('menuRegistTable', false);
	var obj = $(el.step.value);
	var selRow = obj.getAttribute("selRow");
	var url = "./multiProcess.ajax.html?mode=regist_category&content="+encodeURIComponent(el.content.value)+"&pno="+el.pno.value+"&no="+el.no.value+"&cval="+el.cval.value;
	new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(transport) {
			if(el.no.value) { // ����
				var span = obj.rows[selRow].cells[0].getElementsByTagName("span");
				for(var i=0; i<span.length; i++) {
					if(span[i].className!="cate") continue;
					span[i].innerHTML = el.content.value;
					break;
				}
				// �Խ��� ��� ��ܿ� ��� ����
				var move_pcate = $('move_pcate');
				switch(obj.id) {
					case "step1":
						// �Խ��� ��� ����� ĸ�Ǽ���
						boardPositionDiv.innerHTML = el.content.value;
						// �Խ����� �������� ���� ��� - ��� �� �޽��� ���� ################

						// �ϴ� �޺��ڽ� �� ����
						for(var i=0; i<move_pcate.options.length; i++) {
							if(move_pcate.options[i].value!=el.no.value) continue;
							move_pcate.options[i].text = el.content.value;
							break;
						}
						break;
					case "step2":
						// �Խ��� ��� ����� ĸ�Ǽ���
						var string = boardPositionDiv.innerHTML.split(" &gt; ");
						boardPositionDiv.innerHTML = string[0] + " &gt; " + el.content.value;
						// �Խ����� �������� ���� ��� - ��� �� �޽��� ���� ################

						// �ϴ� �޺��ڽ� �� ����
						if(move_pcate.value==el.pno.value) {
							var move_cate = $('move_cate');
							for(var i=0; i<move_cate.options.length; i++) {
								if(move_cate.options[i].value!=el.no.value) continue;
								move_cate.options[i].text = el.content.value;
								break;
							}
						}
						break;
				}
				alert("���������� �����Ǿ����ϴ�."+SPACE);
			}
			else { // ���
				var no = transport.responseXML.getElementsByTagName("resultData")[0].getAttribute("no");
				if(selRow==null) selRow = obj.rows.length;
				else {
					obj.rows[selRow].cells[0].bgColor='';
					if(selRow<=obj.rows.length) selRow++;
				}
				obj.setAttribute("selNo", no);
				obj.setAttribute("selRow", selRow);
				var newPos = obj.insertRow(selRow).insertCell(0);
				newPos.bgColor = classObj.selColor;
				newPos.innerHTML = "<input type='hidden' name='no[]' value='"+no+"'><input type='hidden' name='bval["+no+"]' value='no'><span class='view'><input type='checkbox' name='view["+no+"]' checked></span><span class='cate'>"+el.content.value+"</span><span class='sub'></span>";
				Event.observe(newPos, 'click', classObj.select_item);
				classObj.boardSelectMenu = newPos;

				if(obj.rows.length==1) {
					var sp = parseInt(obj.id.replace(/step/g,''), 10);
					if(sp>1) {
						pObj = $("step"+(sp-1));
						pSelRow = pObj.getAttribute("selRow");
						var pSpan = pObj.rows[pSelRow].cells[0].getElementsByTagName("span");
						for(var i=0; i<pSpan.length; i++) {
							if(pSpan[i].className!="sub") continue;
							pSpan[i].innerHTML = "��";
							break;
						}
					}
				}
				if(no) classObj.get_items(obj.id, no);
				eval(obj.id+"Frm").submit();
				classObj.check_boards(newPos); // �Խ��� ����Ʈ �ʱ�ȭ
				classObj.reset_menu_option(el.step.value); // �ϴ� �޺��ڽ� ������
			}
		}
	});
}

// �Խ��� ���̵� �ߺ�üũ
RANKUP_BOARD.prototype.verify_board = function(board_id) {
	if(!isBlank(board_id) || !isValidUserid(board_id)) return false;
	var url = "./multiProcess.ajax.html?mode=verify_board&bid="+board_id.value;
	new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(transport) {
			var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
			eval(resultData.firstChild.nodeValue);
		}
	});
}

// �Խ��� ��� ���� ����
RANKUP_BOARD.prototype.change_board_style = function(el) {
	var form = this.boardRegistFrm;
	//�⺻��+������
	var normal_skin_check_true = ["use_condense","use_comment", "use_reply", "use_new_icon", "use_attach_icon", "use_reply_icon","use_near_article", "use_detail_list"];
	var normal_skin_check_false = ["use_secret"];
	var normal_skin_disable_false =["use_attach_icon", "use_reply", "use_reply_icon", "use_comment", "use_hit_best","hit_best_num", "use_vote","use_report","use_near_article","use_detail_list","use_only_good","use_snssend"];
	//��������
	var gallery_skin_check_true = ["use_comment", "use_new_icon","use_near_article", "use_detail_list","use_attach"];
	var gallery_skin_check_false = ["use_secret", "use_condense","use_attach_icon", "use_reply","use_reply_icon"];
	var gallery_skin_disabled_true = ["use_attach_icon","use_reply", "use_reply_icon"];
	var gallery_skin_disabled_false = ["use_comment", "use_hit_best", "hit_best_num", "use_vote", "use_report","use_near_article", "use_detail_list","use_only_good","use_snssend"];
	//�ϴ�����
	var mantoman_skin_check_true = ["use_secret","use_condense","use_reply", "use_new_icon", "use_attach_icon", "use_reply_icon"];
	var mantoman_skin_check_false = ["use_comment","use_near_article", "use_detail_list", "use_hit_best", "use_vote", "use_only_good","use_report","use_snssend"];
	var mantoman_skin_disabled_false = ["use_attach_icon","use_reply", "use_reply_icon"];
	var mantoman_skin_disabled_true = ["use_comment", "use_hit_best", "use_vote", "use_report","use_near_article", "use_detail_list","use_only_good","use_snssend"];
	switch(el.value) {
		case "normal":
		case "webzin":
			if(el.value == "webzin")	{
				if(form.board_id.readOnly===false) form.subject_length.value = 30;
				$('gallerySettingForm').style.display = "block";
				$('thumb_nums_frame').style.display = "none";
			} else {
				if(form.board_id.readOnly===false) form.subject_length.value = 40;
				$('gallerySettingForm').style.display = "none";
			}
			normal_skin_disable_false.each(function(val) {
				var check_element = "form."+val;
				if(eval(check_element)) eval(check_element).disabled = false;
			});
			normal_skin_check_true.each(function(val) {
				var check_element = "form."+val;
				if(eval(check_element)) eval(check_element).checked = true;
			});
			normal_skin_check_false.each(function(val) { var check_element = "form."+val; if(eval(check_element)) eval(check_element).checked = false; });
			break;
		case "gallery":
			if(form.board_id.readOnly===false) form.subject_length.value = 13;
			gallery_skin_disabled_true.each(function(val) { var check_element = "form."+val; if(eval(check_element)) eval(check_element).disabled = true; });
			gallery_skin_disabled_false.each(function(val) { var check_element = "form."+val; if(eval(check_element)) eval(check_element).disabled = false; });
			gallery_skin_check_true.each(function(val) { var check_element = "form."+val; if(eval(check_element)) eval(check_element).checked = true; });
			gallery_skin_check_false.each(function(val) { var check_element = "form."+val; if(eval(check_element)) eval(check_element).checked = false; });
			$('gallerySettingForm').style.display = "block";
			$('thumb_nums_frame').style.display = "block";
			break;
		case "mantoman":
			if(form.board_id.readOnly===false) form.subject_length.value = 40;
			mantoman_skin_disabled_true.each(function(val) { var check_element = "form."+val; if(eval(check_element)) eval(check_element).disabled = true; });
			mantoman_skin_disabled_false.each(function(val) { var check_element = "form."+val; if(eval(check_element)) eval(check_element).disabled = false; });
			mantoman_skin_check_true.each(function(val) { var check_element = "form."+val; if(eval(check_element)) eval(check_element).checked = true; });
			mantoman_skin_check_false.each(function(val) { var check_element = "form."+val; if(eval(check_element)) eval(check_element).checked = false; });
			$('gallerySettingForm').style.display = "none";
			break;
	}
	// ��� ���̾ ���̸� �Խ��� ����� ���̿� ����
	$('screenBlindDiv').style.height = $('boardRegistTable').offsetHeight;
}

// ÷������ ��� ���� üũ - �������� �϶����� ����÷�α���� �ʼ��̹Ƿ� ���� �� �� ����.
RANKUP_BOARD.prototype.check_gallery_style = function(el) {
	if(this.boardRegistFrm.board_style[1].checked) {
		el.checked = true;
		alert("������ ������ ��쿡�� ÷������ ����� �ʼ��� �����Ǿ�� �մϴ�."+SPACE);
	}
}

// ��б� ��� ���� üũ - �ϴ����� �϶����� ��б۱���� �ʼ��̹Ƿ� ���� �� �� ����.
RANKUP_BOARD.prototype.check_mantoman_style = function(el) {
	if(this.boardRegistFrm.board_style[3].checked) {
		el.checked = true;
		alert("1:1 ������ ��쿡�� ��б� ����� �ʼ��� �����Ǿ�� �մϴ�."+SPACE);
	}
}

// �޺��ڽ� �� ����
RANKUP_BOARD.prototype.change_category = function(el, target) {
	var url = "./multiProcess.ajax.html?mode=load_category&cmd=combobox&pno="+el.value;
	new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(transport) {
			target.options.length = 0;
			var items = transport.responseXML.getElementsByTagName('item');
			for(var i=0; i<items.length; i++) {
				var no = items[i].getAttribute('no');
				var content = items[i].getElementsByTagName('content')[0].firstChild.nodeValue;
				target.options[target.options.length] = new Option(content, no);
			}
			if(target.options.length===0) target.options[0] = new Option("�޴�����", '');
		}
	});
}



//#########################################################################
//# Part. II - �Խ��� ����/���� �κ�
//#########################################################################
// Node �� ����
RANKUP_BOARD.prototype.get_nodeValue = function(node, name) {
	try {return node.getElementsByTagName(name)[0].firstChild.nodeValue}
	catch(e) {return ''}
}

// ��ü �Խ��� ����/����
RANKUP_BOARD.prototype.checkAll = function(val) {
	var nos = document.getElementsByName("bno[]");
	for(var i=0, j=0; i<nos.length; i++) {
		var item = nos[i];
		if(item.disabled==true) continue;
		item.checked = val=="cross" ? !item.checked : val;
	}
}

// ���õ� �Խ��� �ε��� �� �������� - �̻��
RANKUP_BOARD.prototype.get_checkAll = function() {
	var items = new Array();
	var nos = document.getElementsByName("bno[]");
	for(var i=0, j=0; i<nos.length; i++) {
		var item = nos[i];
		if(item.checked==true) items.push(item.value);
	}
	return items.join("__");
}

// ��� �� ������ ����Ʈ ��ŷ
RANKUP_BOARD.prototype.screen_blind = function(mode) {
	var screenBlindDiv = $('screenBlindDiv');
	if(mode===true) {
		screenBlindDiv.style.display = "block";
		var _height = parseInt(document.body.clientHeight,10);
		if(parseInt(document.body.scrollHeight,10)>_height) _height = parseInt(document.body.scrollHeight,10);
		//$('screenBlindDiv').style.height = (document.body.scrollHeight > document.body.clientHeight) ? _height + "px" : "100%";
		screenBlindDiv.style.height = parseInt(document.body.scrollTop,10) + _height + "px";// : "100%";
		screenBlindDiv.style.marginTop = "-" + parseInt(document.body.scrollTop,10) + "px";
		this.change_alpha(screenBlindDiv, 0, 30, 10, "stop"); // this.change_display("screenBlindDiv", true);

		// SELECT ��ü �����
		this.sb_selects = document.getElementsByTagName("select");
		for(var i=0; i<this.sb_selects.length; i++) {
			if(!in_array(this.sb_selects[i].name, new Array("move_pcate", "move_cate", "main_skin", "left_skin"))) continue;
			this.sb_selects[i].style.visibility = "hidden";
		}
	}
	else {
		this.change_alpha(screenBlindDiv, 30, 0, 10, true); // this.change_display("screenBlindDiv", false);
		setTimeout(function() {
			for(var i=0; i<classObj.sb_selects.length; i++) classObj.sb_selects[i].style.visibility = "visible";
		}, 100);
	}
}

// ���̾� ���
RANKUP_BOARD.prototype.change_display = function(el, val) {
	var obj = $(el);
	if(val===true||val===false) {
		obj.style.display = val ? "block" : "none";
		if(val===false) {
			// ���ʱ�ȭ
			if(!in_array(el, ["menuRegistTable", "boardRegistTable", "setCategoryTable", "setPermissionTable", "setPointTable", "setMainBoardTable", "setMainStyleTable"])) return false;
			obj.style.left = "-2000px";
			obj.style.top = "-1000px";
			switch(el) {
				case "boardRegistTable": this.boardRegistFrm.reset(); break;
				case "setPermissionTable": document.setPermissionFrm.reset(); break;
				case "setCategoryTable":
					document.setCategoryFrm.reset();
					this.selectBoardCategory = null;
					// �з� ������ ����
					var cDiv = $('setCategoryItemDiv');
					var lis = cDiv.getElementsByTagName('li');
					for(var i=parseInt(lis.length, 10)-2; i>=0; i--) cDiv.removeChild(lis[i]);
					break;
				case "setPointTable":
					document.setPointFrm.reset();
					// ���̺� Ȱ��ȭ
					var xTds = $('setPointTable').getElementsByTagName('td');
					for(var i=0; i<xTds.length; i++) {
						if(xTds[i].disabled===false) continue;
						xTds[i].disabled = false;
					}
					break;
				case "setMainStyleTable":
					// ������� �� �ʱ�ȭ
					$('setMainStyleTable').removeAttribute("bno");
					var lis = $('setMainStyleItemDiv').getElementsByTagName('li');
					for(var i=0; i<lis.length; i++) {
						lis[i].getElementsByTagName('input')[0].checked = false;
						lis[i].removeAttribute("selected");
						lis[i].className="normalRow";
					}
					break;
			}
			this.screen_blind(false);
			return true;
		}
		if(!in_array(el, ["menuRegistTable", "boardRegistTable", "setCategoryTable", "setPermissionTable", "setPointTable", "setMainBoardTable", "setMainStyleTable", "authorInfoTable"])) return false;
		// ������ �߾ӿ� ����
		obj.style.top = (obj.offsetHeight>document.body.clientHeight) ? document.body.scrollTop : document.body.scrollTop+(document.body.clientHeight-obj.offsetHeight)/2 + "px";
		if(el=="authorInfoTable") obj.style.left = (obj.offsetWidth>document.body.clientWidth) ? document.body.scrollLeft : document.body.scrollLeft+(document.body.clientWidth-obj.offsetWidth)/2 + "px";
		else obj.style.left = "0px";
		this.screen_blind(true);
		switch(el) {
			case "boardRegistTable":
				this.boardRegistFrm.cno.value = this.boardCno;
				this.boardRegistFrm.pcno.value = this.boardPCno;
				this.boardRegistFrm.boardId.focus();
				break;
			case "setCategoryTable": $('cname').focus(); break;
		}
		obj.style.display = val ? "block" : "none";
	}
	else {
		obj.style.display = style.display=="none" ? "block" : "none";
		this.screen_blind(false);
	}
}

// ���̾� ���� ���� - ���׼��� �ʿ� #####
RANKUP_BOARD.prototype.change_alpha = function(obj, begin, end, step, stop, handler) {
	if(begin===end) {
		if(stop===true) {
			obj.style.display = "none";
			obj.style.height = "0px";
			return false;
		}
		else {
			if(stop==="stop") return false;
			setTimeout(function() {
				if(classObj.popNotice_beginHandler==handler) {
					classObj.popNotice_beginHandler = null;
					classObj.change_alpha(obj, 100, 0, 5, true);
				}
			}, 2000); // 2�ʰ� ����
		}
	}
	else {
		var opacity = begin>end ? begin-step : begin+step;
		obj.style.filter = " alpha(opacity="+opacity+" Style=4 FinishOpacity=90)";
		if(classObj.popNotice_beginHandler==null) {
			classObj.popNotice_beginHandler = setTimeout(function() {
				classObj.change_alpha(obj, opacity, end, step, stop);
			}, 20);
		}
		else {
			setTimeout(function() {
				classObj.change_alpha(obj, opacity, end, step, stop, classObj.popNotice_beginHandler);
			}, 20);
		}
	}
}

// �˸��� ����
RANKUP_BOARD.prototype.popup_notice = function(msg, normal) {
	var noticePop = $('noticePopupDiv');
	noticePop.innerHTML = msg;
	noticePop.style.display = "block";
	noticePop.className = normal===true ? "green" : "red";
	this.popNotice_beginHandler = null;
	this.change_alpha(noticePop, 0, 85, 5);
}

// �Խ��� ����
RANKUP_BOARD.prototype.select_board_item = function(event) {
	var obj = Event.element(event);
	while(!obj.tagName.match("TD")) obj = obj.parentNode;
	if(classObj.boardSelObject!=null) classObj.boardSelObject.bgColor='';
	classObj.boardSelObject = obj;
	obj.bgColor = classObj.selColor;
	// ������ �Խ��� ��ȣ ����
	classObj.boardNo = obj.getElementsByTagName('input')[0].value;
}

// �Խ��� ���� ����
RANKUP_BOARD.prototype.set_board_direction = function(el) {
	setTimeout(function() {
		var selRow = 0;
		var obj = el.parentNode.parentNode;
		for(selRow=0; selRow<classObj.boardItemList.rows.length; selRow++) {
			if(obj===classObj.boardItemList.rows[selRow].cells[0]) break;
		}
		switch(el.id) {
			case "up":
				if(selRow==0) {
					alert("�ֻ��� �׸� �Դϴ�."+SPACE);
					return false;
				}
				break;
			case "down":
				if(selRow==classObj.boardItemList.rows.length-1) {
					alert("������ �׸� �Դϴ�."+SPACE);
					return false;
				}
				break;
		}
		var nearRow = el.id=="up" ? selRow-1 : selRow+1;
		/* 2009.08.28 remarked
		// �ش� �Խ��ǰ� �̿��ϴ� �Խ����� ��ȣ �缳��
		var curNo = classObj.boardItemList.rows[selRow].cells[0].getElementsByTagName("div")[1].innerHTML;
		var nearNo = classObj.boardItemList.rows[nearRow].cells[0].getElementsByTagName("div")[1].innerHTML;
		classObj.boardItemList.rows[selRow].cells[0].getElementsByTagName("div")[1].innerHTML = nearNo;
		classObj.boardItemList.rows[nearRow].cells[0].getElementsByTagName("div")[1].innerHTML = curNo;
		*/
		// ��� ü����
		var xNode = classObj.boardItemList.rows[selRow].cells[0].cloneNode(true);
		classObj.boardItemList.deleteRow(selRow);
		classObj.boardItemList.insertRow(nearRow).insertCell(0).replaceNode(xNode);
		// ���õ� ��ü ����
		classObj.boardSelObject = classObj.boardItemList.rows[nearRow].cells[0];
		// ������� ����
		classObj.multiProcessor("classObj.save_settings()", 2000, classObj.boardSelectMenu.getElementsByTagName('input')[0].value, "classObj.boardSelectMenu.getElementsByTagName('input')[0].value");
	}, 80);
}

// ��Ƽ ���μ���
RANKUP_BOARD.prototype.multiProcessor = function(func, handler, flag, compare) {
	// �÷��װ� �ٲ� ��� ó������ ����
	if(flag!==eval(compare)) return false;

	if(classObj.xHandler!==null && classObj.xHandler<handler) {
		classObj.xHandler = parseInt(handler, 10);
		return true;
	}
	if(classObj.yHandler==null) classObj.yHandler = handler;
	classObj.xHandler = parseInt(handler, 10);
	if(classObj.xHandler===0) {
		classObj.xHandler = classObj.yHandler;
		eval(func);
	}
	else {
		classObj.xHandler -= 500;
		setTimeout(function() {
			classObj.multiProcessor(func, classObj.xHandler, flag, compare);
		}, 500);
	}
}

// �Խ��� ���� ����
RANKUP_BOARD.prototype.save_settings = function() {
	classObj.popup_notice("�ڵ����� - ����� �Խ��� ������ �����Ͽ����ϴ�.", true);
	classObj.boardSettingFrm.submit();
}

// ���õ� �޴��� ���� �Խ��� ����Ʈ üũ
RANKUP_BOARD.prototype.check_boards = function(el) {
	//var boardPositionDiv = $('boardPositionDiv');
	if(this.boardItemList==null) this.boardItemList = $('boardItemList');
	if(el===true) { // ����
		boardPositionDiv.innerHTML = "���õ� �޴��� �����ϴ�.";
		var template_item = "\
		<tr disabled>\
			<td>\
				<div class='col1'><input type='checkbox' disabled></div>\
				<div class='colZ'>�޴��� �����Ͽ� �ֽʽÿ�.</div>\
				<div class='colB'>����</div>\
			</td>\
		</tr>";
		this.boardItemList.update(template_item);
		return true;
	}
	var spans = el.getElementsByTagName("span");
	var obj = el.parentNode.parentNode.parentNode;
	switch(obj.id) {
		case "step1":
			boardPositionDiv.innerHTML = spans[1].innerHTML;
			break;
		case "step2":
			var string = boardPositionDiv.innerHTML.split(" &gt; ");
			boardPositionDiv.innerHTML = string[0] + " &gt; " + spans[1].innerHTML;
			break;
	}
	var vals = el.getElementsByTagName("input");
	// �����޴��� ������ ��� �Խ��� �߰� ��ư ��Ȱ��ȭ / ��Ʈ ����
	if(spans[spans.length-1].innerHTML=="��") {
		$('registBoardBtn').disabled = true;
		$('registBoardBtn').className = "tool_disabled";
		//this.popup_notice("�Խ����� �����Ͻ÷��� �����޴��� �����Ͽ� �ֽʽÿ�.");
	}
	else {
		$('registBoardBtn').disabled = false;
		$('registBoardBtn').className = "tool_enabled";
	}

	// ������ �޴�(ī�װ�) �� �Ҵ�
	this.boardCno = vals[0].value;
	this.boardPCno = obj.id=="step1" ? 0 : $('step1').getAttribute('selNo');
	this.boardSelObject = null;

	// �޴��� ��ϵǾ� �ִ� �Խ������� ����Ʈ ����
	var tools = $('step2Tools').getElementsByTagName("img");
	if(vals[1].value=="yes") {
		this.get_board_items(vals[0].value); // �Խ��� �ε�
		if(obj.id=="step1") { // �޴�1�ܰ��ϰ��
			for(var i=0; i<tools.length; i++) {
				tools[i].className = "tool_disabled";
				tools[i].disabled = true;
			}
			//this.popup_notice("�����޴��� ������ �� �����ϴ�.");
		}
		else {
			for(var i=0; i<tools.length; i++) {
				tools[i].className = "tool_enabled";
				tools[i].disabled = false;
			}
		}
	}
	else {
		// ���� ����Ʈ ����
		if(spans[spans.length-1].innerHTML) {
			var template_item = "\
			<tr disabled>\
				<td>\
					<div class='col1'><input type='checkbox' disabled></div>\
					<div class='colZ'>���� �޴��� �����Ͽ� �ֽʽÿ�.</div>\
					<div class='colB'>����</div>\
				</td>\
			</tr>";
		}
		else {
			var template_item = "\
			<tr disabled>\
				<td>\
					<div class='col1'><input type='checkbox' disabled></div>\
					<div class='colZ'>'"+boardPositionDiv.innerHTML+"' �޴��� ��ϵ� �Խ����� �����ϴ�.</div>\
					<div class='colB'>����</div>\
				</td>\
			</tr>";
		}
		this.boardItemList.update(template_item);
		this.boardItems = null;
		// �Խ��� �߰� ��ư Ȱ��ȭ
		for(var i=0; i<tools.length; i++) {
			tools[i].className = "tool_enabled";
			tools[i].disabled = false;
		}
	}
}

// �Խ��� ��� �ε�
RANKUP_BOARD.prototype.get_board_items = function(cno) {
	new Ajax.Request("./multiProcess.ajax.html?mode=load_board&cno="+cno, {
		method: 'get',
		onSuccess: function(transport) {
			classObj.boardItemList.update('');
			classObj.boardItems = transport.responseXML.getElementsByTagName("item");
			var totRows = classObj.boardItems.length;
			for(var i=0; i<classObj.boardItems.length; i++) {
				var item = classObj.boardItems[i];
				var bno = item.getAttribute('no');
				var board_id = classObj.get_nodeValue(item, 'board_id');
				var board_name = classObj.get_nodeValue(item, 'board_name');
				var board_used = classObj.get_nodeValue(item, 'board_use');
				var anum = classObj.get_nodeValue(item, 'anum');
				var cno = classObj.get_nodeValue(item, 'cno');
				var pcno = classObj.get_nodeValue(item, 'pcno');
				var rank = classObj.get_nodeValue(item, 'rank');
				var newCell = classObj.boardItemList.insertRow(i).insertCell(0);

				// �̻�� �Խ������� üũ - 2009.08.28 modified
				var used = board_used=='no' ? "<span class='unused'>�̻��</span>" : "<span class='used'>���</span>";

				newCell.innerHTML = "\
				<div class='col1'><input type='checkbox' name='bno[]' value='"+bno+"'><input type='hidden' name='rank[]' value='"+bno+"'><input type='hidden' name='cno' value='"+cno+"'><input type='hidden' name='pcno' value='"+pcno+"'></div>\
				<div class='col3'><img src='./img/btn_order_up.gif' border='0' align='absmiddle' id='up' onClick=\"rankup_board.set_board_direction(this)\"> <img src='./img/btn_order_down.gif' border='0' align='absmiddle' id='down' onClick=\"rankup_board.set_board_direction(this)\"></div>\
				<div class='"+(classObj.boardExtension==true ? "col4" : "colC")+"' nowrap><a onClick=\"rankup_board.regist_board("+i+")\">"+board_name+"</a></div>\
				<div class='col5'>"+anum+"</div>\
				<div class='col6'><a onClick=\"rankup_board.update_board("+i+", 'category')\">����</a></div>\
				<div class='col7'><a onClick=\"rankup_board.update_board("+i+", 'permission')\">����</a></div>\
				"+(classObj.boardExtension==true ? "<div class='col8'><a onClick=\"rankup_board.update_board("+i+", 'point')\">����</a></div>" : '')+"\
				<div class='col9'><a onClick=\"rankup_board.preview_board('"+board_id+"')\">�̸�����</a></div>\
				<div class='col2'><a onClick=\"rankup_board.update_board("+i+", 'used')\">"+ used +"</a></div>\
				<div class='colA'><a onClick=\"rankup_board.regist_board("+i+")\">����</a></div>\
				<div class='colB'><a onClick=\"rankup_board.delete_board("+cno+", '"+board_id+"')\">����</a></div>";
				// �̺�Ʈ �ο�
				Event.observe(newCell, 'click', classObj.select_board_item);
				// ����Ʈ ���Ž�
				if(classObj.boardSelObject!=null && bno==classObj.boardNo) {
					newCell.bgColor = classObj.selColor;
					classObj.boardSelObject = newCell;
				}
			}
			classObj.boardRegistFrm.rank.value = parseInt(rank, 10)+1;
		}
	});
}

// �Խ��� ����
RANKUP_BOARD.prototype.delete_board = function(cno, id) {
	setTimeout(function() {
		if(!confirm("�����Ͻ� �Խ����� �����Ͻðڽ��ϱ�?"+SPACE)) return false;
		new Ajax.Request("./multiProcess.ajax.html?mode=delete_board&cno="+cno+"&id="+id, {
			method: 'get',
			onSuccess: function(transport) {
				var resultData = transport.responseXML.getElementsByTagName("resultData")[0];
				alert(resultData.firstChild.nodeValue+SPACE);
				if(resultData.getAttribute("result")=="success") {
					// 1�� ���� �Խ����� ������ ���
					if(classObj.boardItemList.rows.length==1) {
						// �޴�(ī�װ�) ���� bval �缳��
						classObj.boardSelectMenu.getElementsByTagName('input')[1].value = "no";// bval
						classObj.boardItemList.update('');
						// ���� ����Ʈ ����
						var template_item = "\
						<tr disabled>\
							<td>\
								<div class='col1'><input type='checkbox' disabled></div>\
								<div class='colZ'>'"+boardPositionDiv.innerHTML+"' �޴��� ��ϵ� �Խ����� �����ϴ�.</div>\
								<div class='colB'>����</div>\
							</td>\
						</tr>";
						classObj.boardItemList.update(template_item);
						classObj.boardItems = null;
						// �Խ��� �߰� ��ư Ȱ��ȭ
						var tools = $('step2Tools').getElementsByTagName("img");
						for(var i=0; i<tools.length; i++) {
							tools[i].className = "tool_enabled";
							tools[i].disabled = false;
						}
					}
					// ������ �Խ��� ����Ʈ���� ����
					else classObj.boardItemList.removeChild(classObj.boardSelObject.parentNode);

					// ������ ���� ����
					//classObj.boardSettingFrm.submit();
					classObj.boardSelObject = null; // ������ �Խ��� ��ü �ʱ�ȭ
					classObj.boardNo = null; // �Խ��� ��ȣ �ʱ�ȭ
				}
			}
		});
	}, 0);
}

// �Խ��� ���/����
RANKUP_BOARD.prototype.regist_board = function(index) {
	// ���
	var form = this.boardRegistFrm;
	form.reset();
	form.use_attach_icon.disabled = form.use_reply.disabled = form.use_reply_icon.disabled = false;
	form.use_comment.disabled = form.use_hit_best.disabled = form.hit_best_num.disabled = false;
	form.use_vote.disabled = form.use_near_article.disabled = false;
	form.use_detail_list.disabled = form.use_only_good.disabled = false;
	if(form.use_report) form.use_report.disabled = false;
	if(index==undefined) {
		form.board_id.value = '';
		form.boardId.readOnly = false;
		form.boardId.className = "require";
		form.board_use.checked = true;
		$('verifyButtonDiv').style.display = "block";
		for(var i=0; i<form.board_skin.options.length; i++) {
			var skin = form.board_skin.options[i];
			if(skin.text.indexOf('gray')==-1) continue;
			form.board_skin.options[i].selected = true;
			break;
		}
		form.subject_length.value = 40;
		form.use_condense.checked = true;
		form.use_attach_icon.disabled = false; // ÷������ ������ Ȱ��ȭ
		form.use_reply.disabled = false; // ��� ��� Ȱ��ȭ
		form.use_reply_icon.disabled = false; // ��� ������ ��� Ȱ��ȭ
		this.change_display("boardRegistTable", true);
		$('gallerySettingForm').style.display = "none";
		return true;
	}
	// ����
	if(this.boardItems!=null) {
		var item = this.boardItems[index];
		form.no.value = item.getAttribute('no');
		form.cno.value = this.get_nodeValue(item, 'cno');
		form.pcno.value = this.get_nodeValue(item, 'pcno');
		$('verifyButtonDiv').style.display = "none";
		form.boardId.value = form.board_id.value = this.get_nodeValue(item, 'board_id');
		form.boardId.className = "disable";
		form.boardId.readOnly = true;
		form.board_name.value = this.get_nodeValue(item, 'board_name'); // �Խ��� �̸�
		form.board_skin.value = this.get_nodeValue(item, 'board_skin'); // ��Ų ���� �̸�
		// �Խ��� ��Ÿ�� - �Խ��� or ������ or ����
		switch(this.get_nodeValue(item, 'board_style')) {
			case 'normal':
				$('gallerySettingForm').style.display = "none";
				break;
			case 'gallery':
				form.board_style[1].checked = true
				$('gallerySettingForm').style.display = "block";
				$('thumb_nums_frame').style.display = "block";
				if(form.board_style[1].checked) {
					form.use_condense.checked = form.use_attach_icon.checked = form.use_reply.checked = form.use_reply_icon.checked = false;
					form.use_attach_icon.disabled = form.use_reply.disabled = form.use_reply_icon.disabled = true;
				}
				break;
			case 'webzin':
				form.board_style[2].checked = true;
				$('gallerySettingForm').style.display = "block";
				$('thumb_nums_frame').style.display = "none";
				break;
			case "mantoman":
				form.board_style[3].checked = true;
				$('gallerySettingForm').style.display = "none";
				if(form.board_style[3].checked) {
					form.use_comment.disabled = form.use_hit_best.disabled = form.use_detail_list.disabled = form.use_only_good.disabled =true;
					form.use_vote.disabled = form.use_near_article.disabled = true;
					if(form.use_report) form.use_report.disabled = false;
				}
				break;
		}
		form.board_width.value = this.get_nodeValue(item, 'board_width'); // �Խ��� ����ũ��
		form.subject_length.value = this.get_nodeValue(item, 'subject_length'); // ��� ������� ����
		form.use_condense.checked = this.get_nodeValue(item, 'use_condense').match("on"); // �� ���ӱ�ȣ ���
		form.article_rows.value = this.get_nodeValue(item, 'article_rows'); // �������� �Խù� ��
		form.board_use.checked = this.get_nodeValue(item, 'board_use').match("yes"); // �Խ��� ���

		form.use_category.checked = this.get_nodeValue(item, 'use_category').match("on"); // �з� ���
		form.use_duplicate_hit.checked = this.get_nodeValue(item, 'use_duplicate_hit').match("on"); // ��� ���
		form.use_comment.checked = this.get_nodeValue(item, 'use_comment').match("on"); // ��� ���
		form.use_reply.checked = this.get_nodeValue(item, 'use_reply').match("on"); // ��� ���
		form.use_vote.checked = this.get_nodeValue(item, 'use_vote').match("on"); // ��õ/�ݴ� ���
		form.use_only_good.checked = this.get_nodeValue(item, 'use_only_good').match("on"); // ��õ��ɸ� ���

		try { form.use_report.checked = this.get_nodeValue(item, 'use_report').match("on"); } catch(e) {} // �Ű� ���
		form.use_secret.checked = this.get_nodeValue(item, 'use_secret').match("on"); // ��б� ���
		form.use_print.checked = this.get_nodeValue(item, 'use_print').match("on"); // �μ���

		if(this.get_nodeValue(item, 'use_writer') == "uid") form.use_writer[1].checked = true; //���ۼ��� ����
		form.use_snssend.checked = this.get_nodeValue(item, 'use_snssend').match("on"); //SSN��ư ��뼳��
		if(this.get_nodeValue(item, 'use_articledel') == "now") form.use_articledel[0].checked = true; //�ٷλ�������
		form.use_watermark.checked = this.get_nodeValue(item, 'use_watermark').match("on"); //���͸�ũ ���

		form.board_header_file.value = this.get_nodeValue(item, 'board_header_file'); // �Խ��� ��� ����
		form.board_footer_file.value = this.get_nodeValue(item, 'board_footer_file'); // �Խ��� �ϴ� ����

		form.use_hit_best.checked = this.get_nodeValue(item, 'use_hit_best').match("on"); // ��ȸ�� BEST ���
		form.hit_best_num.value = this.get_nodeValue(item, 'hit_best_num'); // ��ȸ�� BEST ��� ���� - 2009.08.31 added
		form.use_new_icon.checked = this.get_nodeValue(item, 'use_new_icon').match("on"); // new ������ ���
		form.recent_time.value = this.get_nodeValue(item, 'recent_time'); // �ֱ� �Խù��� ������ �Ⱓ
		form.use_attach_icon.checked = this.get_nodeValue(item, 'use_attach_icon').match("on"); // ÷������ ������ ���
		form.use_reply_icon.checked = this.get_nodeValue(item, 'use_reply_icon').match("on"); // ��� ������ ���
		form.use_near_article.checked = this.get_nodeValue(item, 'use_near_article').match("on"); // ������/������ ���
		form.use_detail_list.checked = this.get_nodeValue(item, 'use_detail_list').match("on"); // �������� ��� ���

		form.use_attach.checked = this.get_nodeValue(item, 'use_attach').match("on"); // ÷������ ���
		form.use_detail_attach.checked = this.get_nodeValue(item, 'use_detail_attach').match("on"); // ÷������ ���
		form.attach_nums.value = this.get_nodeValue(item, 'attach_nums'); // ÷������ ����
		form.attach_size.value = this.get_nodeValue(item, 'attach_size'); // ÷������ �ִ� ũ��
		form.attach_extension.value = this.get_nodeValue(item, 'attach_extension'); // ÷������ Ȯ����

		form.thumb_width.value = this.get_nodeValue(item, 'thumb_width'); // ��� �ִ� ����ũ��
		form.thumb_height.value = this.get_nodeValue(item, 'thumb_height'); // ��� �ִ� ����ũ��
		form.picture_width.value = this.get_nodeValue(item, 'picture_width'); // �̹��� �ִ� ����ũ��
		form.thumb_nums.value = this.get_nodeValue(item, 'thumb_nums'); // �ٴ� �̹��� ��

		document.getElementsByName('iframeboard_content')[0].contentWindow.document.body.innerHTML = this.get_nodeValue(item, 'board_content'); // ���� �⺻��
		form.board_filter.value = this.get_nodeValue(item, 'board_filter'); // �ܾ� ����
		form.ip_block.value = this.get_nodeValue(item, 'ip_block'); // �����Ǻ�

		classObj.change_display("boardRegistTable", true);
	}
}

// ������ �Խ��� ���� - multiProcess.ajax.html ���� ȣ��
RANKUP_BOARD.prototype.apply_registered_board = function(cno) {
	this.boardSelectMenu.getElementsByTagName('input')[1].value = "yes"; // bval
	this.change_display('boardRegistTable', false);
	this.get_board_items(cno);
	var tools = $('step2Tools').getElementsByTagName("img");
	for(var i=0; i<tools.length; i++) {
		tools[i].className = "tool_disabled";
		tools[i].disabled = true;
	}
	//this.popup_notice("���� �޴��� �Խ����� �����Ͽ� �����޴��� ������ �� �����ϴ�.", "RED");
}

// �з� ������ ������ ����/�ƿ��ÿ� ��� - 2009.09.18 modified
RANKUP_BOARD.prototype.toggle_className = function(event) {
	var obj = Event.element(event);
	while(!obj.tagName.match(/li/i)) obj = obj.parentNode;
	obj.className = event.type!="mouseover" ? obj.getAttribute("selected")!=null ? "selectRow" : "normalRow" : obj.getAttribute("selected")!=null ? "shoverRow" : "hoverRow";
}

// �з� ���� �� ���ΰԽ��� ���� ������ ������ Ŭ���� - 2009.09.18 modified
RANKUP_BOARD.prototype.toggle_checkBox = function(event) {
	var obj = el =Event.element(event);
	if(!obj.parentNode) return false;
	while(!obj.tagName.match(/li/i)) obj = obj.parentNode;
	// ���⼭ radio ��� ���� ���ΰԽ��� ���� ���� �ǹ�
	if(obj.getElementsByTagName('input')[0].type.match(/radio/i)) {
		if(classObj.selectBoard!==null) {
			classObj.selectBoard.className = "normalRow";
			classObj.selectBoard.removeAttribute("selected");
		}
		obj.setAttribute("selected", "true");
		obj.className = "selectRow";
		obj.getElementsByTagName('input')[0].checked = true;
		classObj.selectBoard = obj;
	}
	else if(!el.tagName.match(/img/i)) {
		if(!el.tagName.match(/input/i)) {
			var item = obj.getElementsByTagName('input')[0];
			item.checked = !item.checked;
		}
		// �з��� ���� �Խù� �̵� �޺��ڽ����� option �缳��
		classObj.reset_category_option(obj.parentNode.getElementsByTagName('li'));
	}
}

// �з��� ���� �Խù� �̵� �޺��ڽ� option ����
RANKUP_BOARD.prototype.reset_category_option = function(el) {
	this.nextBoardCategoryNo = 0;
	var obj = document.setCategoryFrm.change_category;
	obj.options.length = 0;
	for(var i=0; i<el.length-1; i++) { // obj ������ ��ü�� �Է����̶� ����
		var divs = el[i].getElementsByTagName('div');
		var inputs = el[i].getElementsByTagName('input');
		this.nextBoardCategoryNo = parseInt(inputs[0].value, 10); // �з� ��ȣ
		if(inputs[0].checked===true) continue;
		obj.options[obj.options.length] = new Option(divs[2].innerHTML, inputs[0].value); // 2009.09.18 modified
	}
	this.nextBoardCategoryNo += 1;
	if(obj.options.length===0) obj.options[0] = new Option("�з�����", '');
}

// �޴� ����/���/������ �ϴ� �޺��ڽ� option �缳��
RANKUP_BOARD.prototype.reset_menu_option = function(step) {
	switch(step) {
		case "step1":
			var obj = $('move_pcate');
			var prev_val = obj.value;
			obj.options.length = 0;
			var tds = $(step).getElementsByTagName('td');
			var selected = false; // �÷���
			for(var i=0; i<tds.length; i++) {
				var text = tds[i].getElementsByTagName('span')[1].innerHTML;
				var value = tds[i].getElementsByTagName('input')[0].value;
				obj.options[obj.options.length] = new Option(text, value); // option �߰�
				if(prev_val==value) {
					obj.options[obj.options.length-1].selected = true; // ������ ���û��� ����
					selected = true;
				}
			}
			if(obj.options.length===0) obj.options[0] = new Option("�޴�����", '');
			// �����޴� ���ε�
			if(selected===false) this.change_category($('move_pcate'), $('move_cate'));
			break;

		case "step2":
			if($('move_pcate').value!=this.boardPCno) break; // �����޴��� ���� ������ ����
			var obj = $('move_cate');
			var prev_val = obj.value;
			obj.options.length = 0;
			var tds = $(step).getElementsByTagName('td');
			for(var i=0; i<tds.length; i++) {
				var text = tds[i].getElementsByTagName('span')[1].innerHTML;
				var value = tds[i].getElementsByTagName('input')[0].value;
				obj.options[obj.options.length] = new Option(text, value); // option �߰�
				if(prev_val==value) obj.options[obj.options.length-1].selected = true; // ������ ���û��� ����
			}
			if(obj.options.length===0) obj.options[0] = new Option("�޴�����", '');
			break;
	}
}

// �Խ��� ���� ���� ( �з�/����/����Ʈ ) - 2009.09.18 modified
RANKUP_BOARD.prototype.update_board = function(index, mode) {
	var item = this.boardItems[index];
	switch(mode) {
		case "category":
			var form = document.setCategoryFrm;
			form.bno.value = item.getAttribute('no');
			var categories = item.getElementsByTagName('categories')[0];
			if(categories.hasChildNodes()) {
				var category = categories.getElementsByTagName('category');
				for(var i=0; i<category.length; i++) {
					var c_item = category[i];
					var c_no = c_item.getAttribute('no');
					var c_anum = c_item.getAttribute('anum');
					var c_name = c_item.firstChild.nodeValue;
					var new_item= "\
					<li class='normalRow' onMouseOver='rankup_board.toggle_className(event)' onMouseOut='rankup_board.toggle_className(event)' onClick='rankup_board.toggle_checkBox(event)'>\
						<div class='col1'><input type='checkbox' name='cno[]' value='"+c_no+"'></div>\
						<div class='col6'><a onClick='rankup_board.direction_up(this)'><img src='./img/btn_order_up.gif' align='absmiddle' hspace='1'></a><a onClick='rankup_board.direction_down(this)'><img src='./img/btn_order_down.gif' align='absmiddle' hspace='1'></a></div>\
						<div class='col2' nowrap>"+c_name+"</div>\
						<div class='col3'>"+c_anum+"</div>\
						<div class='col4'><a onClick='rankup_board.modify_board_category(this)'>����</a></div>\
						<div class='col5'><a onClick='rankup_board.delete_board_category(this)'>����</a><input type='hidden' name='rank[]' value='"+c_no+"'></div>\
					</li>";
					new Insertion.Before($('setCategoryInputDiv'), new_item);
				}
			}
			// �з��� ���� �Խù� �̵� �޺��ڽ� option ����
			this.reset_category_option($('setCategoryItemDiv').getElementsByTagName('li'));
			this.change_display('setCategoryTable', true);
			$('setCategoryTable').getElementsByTagName("span")[0].innerHTML = this.get_nodeValue(item, 'board_name');
			break;

		case "permission":
			var form = document.setPermissionFrm;
			form.bno.value = item.getAttribute('no');
			form.cno.value = this.get_nodeValue(item, 'cno');
			form.list_level.value = this.get_nodeValue(item, 'list_level'); // ����Ʈ ���� ����
			form.read_level.value = this.get_nodeValue(item, 'read_level'); // �Խù� �б� ����
			form.write_level.value = this.get_nodeValue(item, 'write_level'); // �Խù� ���� ����
			form.comment_level.value = this.get_nodeValue(item, 'comment_level'); // ��� ���� ����
			form.reply_level.value = this.get_nodeValue(item, 'reply_level'); // �亯�� ���� ����
			form.delete_level.value = this.get_nodeValue(item, 'delete_level'); // �Խù� ���� ����
			form.notice_level.value = this.get_nodeValue(item, 'notice_level'); // ������ ���� ����
			form.secret_level.value = this.get_nodeValue(item, 'secret_level'); // ��б� �б� ����
			this.change_display('setPermissionTable', true);
			$('setPermissionTable').getElementsByTagName("span")[0].innerHTML = this.get_nodeValue(item, 'board_name');
			break;

		case "point":
			var form = document.setPointFrm;
			form.bno.value = item.getAttribute('no');
			form.cno.value = this.get_nodeValue(item, 'cno');
			var point = item.getElementsByTagName("point")[0];
			for(var i=0; i<point.childNodes.length; i++) {
				var xValue = point.childNodes(i).firstChild.nodeValue;
				var xObject = document.getElementsByName(point.childNodes(i).nodeName)[0];
				if(xObject===undefined) continue; // Ȥ�ö� ���ǵǾ� ���� ���� ��� ��ŵ
				var xInputs = xObject.parentNode.parentNode.getElementsByTagName('input');
				if(xValue==='') {
					var xTds = xObject.parentNode.parentNode.getElementsByTagName('TD');
					xInputs[0].checked = false;
					xObject.value = 0;
					xTds[1].disabled = xTds[2].disabled = true;
				}
				else {
					xInputs[0].checked = true;
					if(xInputs[2]!=null) xInputs[2].checked = xValue<0;
					xObject.value = Math.abs(xValue);
				}
			}
			this.change_display('setPointTable', true);
			$('setPointTable').getElementsByTagName("span")[0].innerHTML = this.get_nodeValue(item, 'board_name');
			break;

		case "used": // 2009.08.28 added
			var item = this.boardItems[index];
			var id = this.get_nodeValue(item, 'board_id');
			var used = this.get_nodeValue(item, 'board_use');
			var cno = this.get_nodeValue(item, 'cno');
			var anti_used = {yes: {val: 'no', text: '�̻��'}, no: {val: 'yes', text: '���'}}
			setTimeout(function() {
				if(!confirm("�����Ͻ� �Խ����� ��뿩�θ� '"+ anti_used[used].text +"'���� �����Ͻðڽ��ϱ�?"+SPACE)) return false;
				new Ajax.Request("./multiProcess.ajax.html?mode=update_board&cmd=set_used&id="+id+"&use="+anti_used[used].val, {
					method: 'get',
					onSuccess: function(transport) {
						var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
						alert(resultData.firstChild.nodeValue+SPACE);
						if(resultData.getAttribute("result")=="success") {
							// �Խ��� ����Ʈ ���ε�
							classObj.get_board_items(cno);
						}
					}
				});
			}, 0);
			break;
	}
}

// �Խ��� �з� ���� ���� - 2009.09.18 added
RANKUP_BOARD.prototype.direction_up = function(el) {this.category_direction.up(this, el)},
RANKUP_BOARD.prototype.direction_down = function(el) {this.category_direction.down(this, el)},
RANKUP_BOARD.prototype.category_direction = {
	position: {
		check: function(el) { // ��ġüũ
			while(!el.nodeName.match(/li/i)) el = $(el).up();
			return { prev: el.previousSibling, next: el.nextSibling, obj: el }
		},
		replace: function(post, paste) { // ��ġ����
			var checkbox = paste.getElementsByTagName('input')[0].checked
			paste.parentNode.removeChild(paste);
			new Insertion.After(post, paste.cloneNode(true));
			$(paste.id).getElementsByTagName('input')[0].checked = checkbox;
			classObj.reset_category_option($('setCategoryItemDiv').getElementsByTagName('li'));
		}
	},
	up: function(This, el) { // �� ����
		var nThis = this;
		var items = this.position.check(el);
		setTimeout(function() {
			if(items.prev==null) {
				alert('�ֻ��� �׸��Դϴ�.'+SPACE);
				return false;
			}
			else {
				items.prev.id = String(Math.random()).substr(2);
				nThis.position.replace(items.obj, items.prev);
				nThis.save();
			}
		}, 50);
	},
	down: function(This, el) { // �Ʒ� ����
		var nThis = this;
		var items = this.position.check(el);
		setTimeout(function() {
			if(items.next==null) {
				alert('������ �׸��Դϴ�.'+SPACE);
				return false;
			}
			else {
				items.obj.id = String(Math.random()).substr(2);
				nThis.position.replace(items.next, items.obj);
				nThis.save();
			}
		}, 50);
	},
	save: function() { // ���� ���� - ��׶��� �۾�
		var url = 'multiProcess.ajax.html?mode=reset_category_rank&bno='+document.setCategoryFrm.bno.value+'&'+ Form.serialize('setCategoryItemDiv');
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(trans) {
				alert('�Խ��� �з� ������ ����Ǿ����ϴ�.'+SPACE);
				classObj.get_board_items(classObj.boardCno)
			}
		});
	}
}

// �Խ��� �з� ���/����
RANKUP_BOARD.prototype.regist_board_category = function(form) {
	if(!confirm("�Է��Ͻ� �з��� �Խ��ǿ� �ݿ��Ͻðڽ��ϱ�?"+SPACE)) return false;
	if(form.modify.value=='') form.cno.value = this.nextBoardCategoryNo;
	var url = "./multiProcess.ajax.html?mode="+form.mode.value+"&cmd="+form.cmd.value+"&bno="+form.bno.value+"&cno="+form.cno.value+"&cname="+encodeURIComponent(form.cname.value);
	new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(transport) {
			var resultData = transport.responseXML.getElementsByTagName("resultData")[0];
			alert(resultData.firstChild.nodeValue+SPACE);
			if(resultData.getAttribute("result")=="success") {
				// ������
				if(form.modify.value=="true") {
					classObj.selectBoardCategory.getElementsByTagName('div')[2].innerHTML = form.cname.value; // 2009.09.18 modified
					// ���� ���� �ʱ�ȭ
					if(classObj.selectBoardCategory!=null) {
						classObj.selectBoardCategory.className = "normalRow";
						classObj.selectBoardCategory.removeAttribute("selected");
						classObj.selectBoardCategory.getElementsByTagName('a')[2].innerHTML = "����"; // 2009.09.18 modified
					}
					classObj.selectBoardCategory = null;
					form.cno.value = classObj.nextBoardCategoryNo;
					form.modify.value = '';
				}
				// ��Ͻ�
				else {
					if(!form.cname.value) return false;
					var new_item = "\
					<li class='normalRow' onMouseOver='rankup_board.toggle_className(event)' onMouseOut='rankup_board.toggle_className(event)' onClick='rankup_board.toggle_checkBox(event)'>\
						<div class='col1'><input type='checkbox' name='cno[]' value='"+form.cno.value+"'></div>\
						<div class='col6'><a onClick='rankup_board.direction_up(this)'><img src='./img/btn_order_up.gif' align='absmiddle' hspace='1'></a><a onClick='rankup_board.direction_down(this)'><img src='./img/btn_order_down.gif' align='absmiddle' hspace='1'></a></div>\
						<div class='col2' nowrap>"+form.cname.value+"</div>\
						<div class='col3'>0</div>\
						<div class='col4'><a onClick='rankup_board.modify_board_category(this)'>����</a></div>\
						<div class='col5'><a onClick='rankup_board.delete_board_category(this)'>����</a><input type='hidden' name='rank[]' value='"+form.cno.value+"'></div>\
					</li>";
					new Insertion.Before($('setCategoryInputDiv'), new_item);
				}
				// �з��� ���� �Խù� �̵� �޺��ڽ� option ����
				classObj.reset_category_option($('setCategoryItemDiv').getElementsByTagName('li'));

				// �Է� �� �ʱ�ȭ
				form.cname.focus();
				form.cname.value = '';
				// �Խ��� ����Ʈ ���ε�
				classObj.get_board_items(classObj.boardCno);
			}
		}
	});
}

// �Խ��� �з� ����
RANKUP_BOARD.prototype.modify_board_category = function(el) {
	var obj = el.parentNode.parentNode;
	var form = document.setCategoryFrm;
	if(this.selectBoardCategory!=null) {
		this.selectBoardCategory.className = "normalRow";
		this.selectBoardCategory.removeAttribute("selected");
		this.selectBoardCategory.getElementsByTagName('a')[2].innerHTML = "����"; // 2009.09.18 modified
	}
	if(obj===this.selectBoardCategory) { // ��� ����
		this.selectBoardCategory = null;
		form.cno.value = this.nextBoardCategoryNo;
		form.modify.value = form.cname.value = '';
		form.cname.focus();
		return false;
	}
	this.selectBoardCategory = obj;
	el.innerHTML = "<b>���</b>";
	obj.setAttribute("selected", "true");
	obj.className = "selectRow";
	form.cname.focus();
	form.modify.value = "true";
	form.cno.value = obj.getElementsByTagName('input')[0].value;
	form.cname.value = obj.getElementsByTagName('div')[2].innerHTML; // 2009.09.18 modified
	form.cname.select();
}

// �Խ��� �з� ����
RANKUP_BOARD.prototype.delete_board_category = function(el) {
	var obj = el.parentNode.parentNode;
	var form = document.setCategoryFrm;
	if(!confirm("�����Ͻ� �Խ��� �з��� �����Ͻðڽ��ϱ�?"+SPACE)) return false;
	var url = "./multiProcess.ajax.html?mode="+form.mode.value+"&cmd="+form.cmd.value+"&bno="+form.bno.value+"&cno="+obj.getElementsByTagName('input')[0].value+"&cname=";
	new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(transport) {
			var resultData = transport.responseXML.getElementsByTagName("resultData")[0];
			alert(resultData.firstChild.nodeValue+SPACE);
			if(resultData.getAttribute("result")=="success") {
				if(obj===classObj.selectBoardCategory) { // ��� ����
					var form = document.setCategoryFrm;
					classObj.selectBoardCategory = null;
					form.modify.value = form.cname.value = '';
					form.cname.focus();
				}
				var xNode = el.parentNode.parentNode;
				xNode.parentNode.removeChild(xNode); // �з� ������ ����
				classObj.reset_category_option($('setCategoryItemDiv').getElementsByTagName('li')); // option �缳��
				classObj.get_board_items(classObj.boardCno); // �Խ��� ����Ʈ ���ε�
			}
		}
	});
}

// üũ���� ���� �ʵ� Ȱ��ȭ/��Ȱ��ȭ ����
RANKUP_BOARD.prototype.check_point = function(el) {
	var obj = el.parentNode.parentNode;
	var input = obj.cells[2].getElementsByTagName('input')[0];
	obj.cells[1].disabled = obj.cells[2].disabled = !el.checked;
	input.readOnly = !el.checked;
	input.value = input.value.trim().length ? input.value : 0;
}

// ������ �з��� �Խù� �̵��ϱ�
RANKUP_BOARD.prototype.change_board_category = function(el) {
	var checkedObjects = new Array();
	var targetObject = null;
	var datas = new Array();
	var cnos = document.getElementsByName('cno[]');
	for(var i=0; i<cnos.length; i++) {
		if(cnos[i].value==el.value) targetObject = cnos[i];
		if(cnos[i].checked!==true) continue;
		checkedObjects.push(cnos[i]);
		datas.push(cnos[i].value);
	}
	if(datas.length===0) {
		alert("�Խù��� �̵���ų �з��� �����Ͽ� �ֽʽÿ�."+SPACE);
		return false;
	}
	if(!el.value) {
		alert("�����Ͻ� �з��� �Խù��� �̵���ų ��� �з��� �������� �ʽ��ϴ�."+SPACE);
		return false;
	}
	if(!confirm("�����Ͻ� �з��� �Խù��� '"+el.options[el.selectedIndex].text+"' �з��� �̵��Ͻðڽ��ϱ�?"+SPACE)) return false;
	var url = "./multiProcess.ajax.html?mode=update_board&cmd=move_articles&bno="+el.form.bno.value+"&cno="+el.value+"&datas="+datas.join('__');
	new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(transport) {
			var resultData = transport.responseXML.getElementsByTagName("resultData")[0];
			alert(resultData.firstChild.nodeValue+SPACE);
			if(resultData.getAttribute("result")=="success") {
				// �����ߴ� �з��� �Խù���(anum) ���� 0 ���� �ʱ�ȭ
				var totAnum = 0;
				for(var i=0; i<checkedObjects.length; i++) {
					var obj = checkedObjects[i].parentNode.parentNode.getElementsByTagName('div')[3];
					totAnum += parseInt(obj.innerHTML, 10);
					checkedObjects[i].checked = false;
					obj.innerHTML = '0';
				}
				// �̵���� �з��� �Խù� �� �����Ű��
				var obj = targetObject.parentNode.parentNode.getElementsByTagName('div')[3];
				obj.innerHTML = parseInt(obj.innerHTML, 10) + totAnum;
				classObj.reset_category_option($('setCategoryItemDiv').getElementsByTagName('li')); // option �缳��
				classObj.get_board_items(classObj.boardCno); // �Խ��� ����Ʈ ���ε�
			}
		}
	});
}

// �Խ��� �̵�
RANKUP_BOARD.prototype.move_board = function(pcno, cno) {
	var checkedObjects = new Array();
	var datas = new Array();
	var bnos = document.getElementsByName('bno[]');
	for(var i=0; i<bnos.length; i++) {
		if(bnos[i].checked!==true) continue;
		checkedObjects.push(bnos[i]);
		datas.push(bnos[i].value);
	}
	if(datas.length===0) {
		alert("�̵���ų �Խ����� �����Ͽ� �ֽʽÿ�."+SPACE);
		return false;
	}
	var cvalue = cno.value=='' ? pcno.value : cno.value;
	var target_category = pcno.options[pcno.selectedIndex].text + (cno.options[cno.selectedIndex].value ? " > "+cno.options[cno.selectedIndex].text : '');
	if(this.boardSelectMenu.getElementsByTagName('input')[0].value==cvalue) {
		alert("������ �޴�('"+target_category+"')�δ� �Խ����� �̵���ų �� �����ϴ�."+SPACE);
		return false;
	}
	if(!confirm("�����Ͻ� �Խ����� '"+target_category+"' �޴��� �̵��ϰڽ��ϱ�?"+SPACE)) return false;
	var url = "./multiProcess.ajax.html?mode=update_board&cmd=move_board&prev_cno="+this.boardCno+"&pcno="+pcno.value+"&cno="+cno.value+"&datas="+datas.join('__');
	new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(transport) {
			var resultData = transport.responseXML.getElementsByTagName("resultData")[0];
			alert(resultData.firstChild.nodeValue+SPACE);
			if(resultData.getAttribute("result")=="success") {
				// ������ �Խ��� ����
				for(var i=checkedObjects.length-1; i>=0; i--) classObj.boardItemList.removeChild(checkedObjects[i].parentNode.parentNode.parentNode);
				if(classObj.boardItemList.rows.length) {
					// ��ȣ ����
					for(var i=0; i<classObj.boardItemList.rows.length; i++) {
						var item = classObj.boardItemList.rows[i];
						item.getElementsByTagName('div')[1].innerHTML = parseInt(classObj.boardItemList.rows.length, 10)-parseInt(i, 10);
					}
					// ����� ���� ����
					classObj.boardSettingFrm.submit();
				}
				else {
					// �޴�(ī�װ�) ���� bval �缳��
					classObj.boardSelectMenu.getElementsByTagName('input')[1].value = "no";// bval
					classObj.boardItemList.update('');
					// ���� ����Ʈ ����
					var template_item = "\
					<tr disabled>\
						<td>\
							<div class='col1'><input type='checkbox' disabled></div>\
							<div class='colZ'>'"+boardPositionDiv.innerHTML+"' �޴��� ��ϵ� �Խ����� �����ϴ�.</div>\
							<div class='colB'>����</div>\
						</td>\
					</tr>";
					classObj.boardItemList.update(template_item);
					classObj.boardItems = null;
					// �Խ��� �߰� ��ư Ȱ��ȭ
					var tools = $('step2Tools').getElementsByTagName("img");
					for(var i=0; i<tools.length; i++) {
						tools[i].className = "tool_enabled";
						tools[i].disabled = false;
					}
				}
				// �Խ����� �߰��� �޴�ó�� bval Ȱ��ȭ
				var tds = $(cno.value=='' ? 'step1' : 'step2').getElementsByTagName('td');
				for(var i=0; i<tds.length; i++) {
					var inputs = tds[i].getElementsByTagName('input');
					if(inputs[0].value!==cvalue) continue;
					inputs[1].value = "yes";
					break;
				}
				classObj.boardSelObject = null; // ������ �Խ��� ��ü �ʱ�ȭ
				classObj.boardNo = null; // �Խ��� ��ȣ �ʱ�ȭ
			}
		}
	});
}

// �Խ��� �̸�����
RANKUP_BOARD.prototype.preview_board = function(id) {
	var board_wnd = window.open(this.index_url+"/index.html?id="+id, "preview_board");
	board_wnd.focus();
}



//#########################################################################
//# Part. III - ���� �Խ��� ���� �κ�
//#########################################################################
// ���������� ��뿩�� üũ
RANKUP_BOARD.prototype.check_main_layout = function(el, init) {
	var inputs = $('boardSettingItemList').getElementsByTagName("input");
	if(el.value=="yes") {
		// �Խ��� ��� ���̱�
		$('settingBoardItemDiv').style.display = "block";
		$('boardMainSettingDiv').style.display = "block";
		$('mainBoardDiv').style.display = "none";
		//if(init!=true) this.popup_notice("�Ʒ��� �Խ��� ��Ͽ��� ������������ ����� �Խ����� ������ �ֽñ� �ٶ��ϴ�.");
		document.getElementsByName('mbno')[0].removeAttribute("required");
		for(var i=0; i<inputs.length; i++) {
			var input = inputs[i];
			if(input.className=="disable") continue;
			input.disabled = false;
		}
	}
	else {
		// �Խ��� ��� ���߱�
		setTimeout(function() {
			document.getElementsByName('mbno')[0].setAttribute("required", "required");
			for(var i=0; i<inputs.length; i++) {
				var input = inputs[i];
				if(input.className=="disable") continue;
				input.disabled = true;
			}
			$('settingBoardItemDiv').style.display = "none";
			$('boardMainSettingDiv').style.display = "none";
			$('mainBoardDiv').style.display = "block";
			if(init!=true) {
				//this.popup_notice("�޴�Ŭ���� ������������ ����� �Խ����� ������ �ֽñ� �ٶ��ϴ�.");
				//classObj.set_main_board();
			}
		}, 0);
	}
}

// ���ΰԽ��� ���� - �Խ��� ����Ʈ�� ����� ����
RANKUP_BOARD.prototype.set_main_board = function() {
	var setMainBoardItemDiv = $('setMainBoardItemDiv');
	setMainBoardItemDiv.update('');
	var mbno = document.getElementsByName('mbno')[0].value;
	if(this.boardSettingItems.length) {
		for(var i=0; i<this.boardSettingItems.length; i++) {
			var board = this.boardSettingItems[i];
			var b_no = board.getAttribute('no');
			var b_anum = board.getAttribute('anum');
			var b_name = this.get_nodeValue(board, 'board_name');
			var new_item = document.createElement('li');
			var checked = (mbno==b_no) ? " checked" : '';
			new_item.className = "normalRow";
			// ���õ� �Խ����� ���� ���
			if(mbno==b_no) {
				new_item.className = "selectRow";
				new_item.setAttribute("selected", "true");
				this.selectBoard = new_item;
			}
			new_item.innerHTML = "\
			<div class='col1'><input type='radio' name='bno'"+checked+" value='"+b_no+"'></div>\
			<div class='col2' nowrap>"+b_name+"</div>\
			<div class='col3'>"+b_anum+"</div>";
			setMainBoardItemDiv.appendChild(new_item);
		}
		// �̺�Ʈ �Ҵ� - �����ʸ� �����Ͽ� �̺�Ʈ ���Ű� �ʿ����� ����
		$$("#setMainBoardItemDiv li").each(function(li) {
			Event.observe(li, "mouseover", classObj.toggle_className);
			Event.observe(li, "mouseout", classObj.toggle_className);
			Event.observe(li, "click", classObj.toggle_checkBox);
		});
	}
	else {
		var new_item = document.createElement('li');
		new_item.className = "normalRow";
		new_item.innerHTML = "\
		<div class='col1'><input type='radio' disabled></div>\
		<div class='col2' style='width:581px' nowrap>��ϵ� �Խ����� �����ϴ�.</div>\
		<div class='col3'>0</div>";
		setMainBoardItemDiv.appendChild(new_item);
		Event.observe(new_item, "mouseover", classObj.toggle_className);
		Event.observe(new_item, "mouseout", classObj.toggle_className);
	}
	this.change_display('setMainBoardTable', true);
}

// ������� ����
RANKUP_BOARD.prototype.set_print_style = function(index) {
	setTimeout(function() {
		$('board_name').innerHTML = classObj.get_nodeValue(classObj.boardSettingItems[index], 'board_name');
		$('setMainStyleTable').setAttribute('bno', classObj.boardSettingItems[index].getAttribute('no'));
		var inputs = classObj.boardSelObject.getElementsByTagName('input');
		var lis = $('setMainStyleItemDiv').getElementsByTagName('li');
		var xIndexs = {'text':0, 'both':1, 'image':2};
		var xIndex = xIndexs[inputs[inputs.length-1].value];
		if(xIndex!==undefined) {
			classObj.selectBoard = lis[xIndex];
			lis[xIndex].className = "selectRow";
			lis[xIndex].setAttribute("selected", "true");
			lis[xIndex].getElementsByTagName('input')[0].checked = true;
		}
		classObj.change_display('setMainStyleTable', true);
	}, 80);
}

// �� �Է�
RANKUP_BOARD.prototype.set_value_table = function(inputs, v1, v2, v3, v4) {
	inputs[1].value = v1; // �������
	inputs[2].value = v2; // �Խù���
	inputs[3].value = v3; // �̹��� ����ũ��
	inputs[4].value = v4; // �̹��� ����ũ��
}

// ������ ����
RANKUP_BOARD.prototype.apply_settings = function(el) {
	switch(el) {
		// ��� ���� ����
		case "setMainStyleTable":
			var b_no = $(el).getAttribute('bno');
			var divs = this.boardSelObject.getElementsByTagName('div');
			var inputs = this.boardSelObject.getElementsByTagName('input');
			var main_style = document.getElementsByName('main_style');
			for(var i=0; i<main_style.length; i++) {
				if(main_style[i].checked!==true) continue;
				divs[divs.length-1].getElementsByTagName('a')[0].innerHTML = this.printStyles[main_style[i].value];

				// �ؽ�Ʈ���� ��� �̹��� ������ �� disabled
				inputs[inputs.length-1].value = main_style[i].value;
				if(main_style[i].value==="text") {
					inputs[3].className = inputs[4].className = "disable";
					inputs[3].readOnly = inputs[4].readOnly = true;
					inputs[3].removeAttribute("required");
					inputs[4].removeAttribute("required");
					this.set_value_table(inputs, 40, 5, '', ''); // �ؽ�Ʈ�� �⺻��
				}
				// ȥ����/�̹����� �⺻�� ����
				else {
					inputs[3].className = inputs[4].className = "require";
					inputs[3].readOnly = inputs[4].readOnly = false;
					inputs[3].setAttribute("required", "required");
					inputs[4].setAttribute("required", "required");
					if(main_style[i].value=="both") this.set_value_table(inputs, 25, 6, 110, 80); // ȥ���� �⺻��
					else this.set_value_table(inputs, 15, 3, 110, 80); // �̹����� �⺻��
				}
				break;
			}
			this.change_display(el, false);
			break;

		// ���ΰԽ��� ����
		case "setMainBoardTable":
			var bnos = document.getElementsByName('bno');
			for(var i=0; i<bnos.length; i++) {
				if(bnos[i].checked!=true) continue;
				document.getElementsByName('mbno')[0].value = bnos[i].value;
				$('mainBoardItem').update("' <b>"+bnos[i].parentNode.parentNode.getElementsByTagName('div')[1].innerHTML+"</b> ' �Խ���");
				break;
			}
			this.change_display(el, false);
			break;

		// ������ ����
		default:
			var obj = Event.element(el).previousSibling; // input
			var inputs = obj.parentNode.getElementsByTagName('input');
			var xIndex = inputs.length>1 ? inputs[0].name==obj.name ? 0 : 1 : 0;
			// �Է��� �� üũ
			if(!inputs[xIndex].value.trim()) return doError(inputs[xIndex], NO_BLANK);
			if(!isNumeric(inputs[xIndex])) return false;
			if(!confirm(inputs[xIndex].getAttribute('hname')+"�� ������ ��('"+inputs[xIndex].value+"')���� �����Ͻðڽ��ϱ�?"+SPACE)) return false;
			// �� ����
			var items = $$('div[class="'+obj.parentNode.className+'"]');
			for(var i=1; i<items.length; i++) { // 1��° �������� ���� �ش�
				var item = items[i].getElementsByTagName('input')[xIndex];
				if(item.readOnly===true || item.disabled===true) continue;
				item.value = inputs[xIndex].value;
			}
			inputs[xIndex].select();
			inputs[xIndex].focus();
	}
}

// ���� �� ���� ��ư ����
RANKUP_BOARD.prototype.set_union = function(el) {
	if(el!==undefined && (el.readOnly===true || el.disabled===true)) return false;
	var obj = $('unionSettingSpan'); // ���� ��ư ����
	if(obj!=null) obj.parentNode.removeChild(obj);
	if(el==undefined || el.tagName.toLowerCase()!="input") return false;
	var newSpan = document.createElement('span');
	newSpan.id = "unionSettingSpan";
	newSpan.innerHTML = "��ä���";
	Event.observe(newSpan, 'click', classObj.apply_settings);
	el.parentNode.insertBefore(newSpan, el.nextSibling);
}

// �Խ��� ������(mval, pcmval, smlayout, spcmlayout) �ε�
RANKUP_BOARD.prototype.get_board_settings = function() {
	var boardSettingItemList = $('boardSettingItemList');
	boardSettingItemList.update('');
	var url = "./multiProcess.ajax.html?mode=load_setting&category="+this.setCategory;
	new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(transport) {
			var resultData = transport.responseXML.getElementsByTagName("resultData")[0];
			if(resultData.getAttribute("result")=="success") {
				classObj.boardSettingItems = resultData.getElementsByTagName('item');
				var form = document.boardSettingFrm;

				// ���������� ������
				if(classObj.setCategory=="main") {
					var categories = new Array();
					var _categories = $('categoryDiv').getElementsByTagName('a');
					for(var i=0; i<_categories.length; i++) {
						var _key = _categories[i].getAttribute('cno');
						if(_key!==null) categories[_key] = _categories[i].innerHTML;
					}
					var old_cno = 0;

					var mainSetting = resultData.getElementsByTagName('main')[0];
					var mskin = classObj.get_nodeValue(mainSetting, 'mskin');
					if(mskin) form.main_skin.value = mskin;
					var mbnum = classObj.get_nodeValue(mainSetting, 'mbnum');
					var mbnums = document.getElementsByName('mbnum');
					mbnums[mbnum-1].checked = true;

					// 2009.08.28 modified
					$w('narticle').each(function(item) {
						if(!classObj.get_nodeValue(mainSetting, item).match(/yes/i)) document.getElementsByName(item)[0].checked = true;
						$(item+'_num').value = classObj.get_nodeValue(mainSetting, item+'_num');
					});
				}
				// �޴��� ���������� ������ ����
				else {
					var mainSetting = resultData.getElementsByTagName('main')[0];
					var mboard = mainSetting.getElementsByTagName('mboard')[0];
					var mskin = classObj.get_nodeValue(mainSetting, 'mskin');
					var lskin = classObj.get_nodeValue(mainSetting, 'lskin');
					if(mskin) form.main_skin.value = mskin;
					if(lskin) form.left_skin.value = lskin;
					form.mbno.value = mboard.getAttribute('no');
					if(mboard.firstChild!=null) $('mainBoardItem').update("' <b>"+mboard.firstChild.nodeValue+"</b> ' �Խ���");

					var mbnum = classObj.get_nodeValue(mainSetting, 'mbnum');
					var mval = classObj.get_nodeValue(mainSetting, 'mval');
					var pcmvals = document.getElementsByName('pcmval');
					var mbnums = document.getElementsByName('mbnum');
					mbnums[mbnum-1].checked = true;
					pcmvals[mval!="yes"?1:0].checked = true;

					// ��¼��� - 2009.08.28 modified
					$w('wbest hcbest narticle').each(function(item) {
						if(!classObj.get_nodeValue(mainSetting, item).match(/yes/i)) document.getElementsByName(item)[0].checked = true;
						$(item+'_num').value = classObj.get_nodeValue(mainSetting, item+'_num');
					});
					classObj.check_main_layout(pcmvals[mval!="yes"?1:0], true);
				}
				for(var i=0; i<classObj.boardSettingItems.length; i++) {
					var item = classObj.boardSettingItems[i];
					var b_no = item.getAttribute('no');
					var b_name = classObj.get_nodeValue(item, 'board_name');
					var b_used = classObj.get_nodeValue(item, 'uval');
					var newCell = boardSettingItemList.insertRow(i).insertCell(0);
					var layout = item.getElementsByTagName(classObj.setCategory=="main" ? 'smlayout' : 'spcmlayout')[0];
					var subject_length = classObj.get_nodeValue(layout, 'subject_length');
					var article_rows = classObj.get_nodeValue(layout, 'article_rows');
					var print_style = classObj.get_nodeValue(layout, 'print_style');
					var image_width = classObj.get_nodeValue(layout, 'image_width');
					var image_height = classObj.get_nodeValue(layout, 'image_height');

					// �̻�� �Խ������� üũ
					if(b_used=='no') b_name = "<strike class='unused'>"+b_name+"</strike><span class='unused'>�̻��</span>";

					// ���������� ������ ���
					if(classObj.setCategory=="main") {
						var mval = classObj.get_nodeValue(item, 'mval');
						var cno = classObj.get_nodeValue(item, 'cno');
						var pcno = classObj.get_nodeValue(item, 'pcno');
						var c_no = pcno==0 ? cno : pcno;
						if(old_cno===c_no) category = "<span disabled>"+category+"</span>";
						else {
							category = categories[c_no];
							old_cno = c_no;
						}
						newCell.innerHTML = "\
						<div class='colA' nowrap>"+category+"</div>\
						<div class='col1'><input type='checkbox' name='bno[]'"+(mval=='yes'? ' checked' : '')+(b_used=='no'? ' disabled' : '')+" value="+b_no+"></div>\
						<div class='colB' nowrap>"+b_name+"</div>"
					}
					// �޴��� ���������� ������ ���
					else {
						var pcmval = classObj.get_nodeValue(item, 'pcmval');
						newCell.innerHTML = "\
						<div class='col1'><input type='checkbox' name='bno[]'"+(pcmval=='yes'? ' checked' : '')+(b_used=='no'? ' disabled' : '')+" value="+b_no+"></div>\
						<div class='col2' nowrap>"+b_name+"</div>"
					}
					newCell.innerHTML += "\
					<div class='col3'><input type='text' name='subject_length["+b_no+"]' value='"+subject_length+"' required hname='�������' option='number' maxlength='3' class='require'></div>\
					<div class='col4'><input type='text' name='article_rows["+b_no+"]' value='"+article_rows+"' required hname='�Խù� ��' option='number' maxlength='2' class='require'></div>\
					<div class='col5'><input type='text' name='image_width["+b_no+"]' value='"+image_width+"' required hname='����ũ��' option='number' maxlength='3' class='require'> <font>��</font> <input type='text' name='image_height["+b_no+"]' value='"+image_height+"' required hname='����ũ��' option='number' maxlength='3' class='require'></div>\
					<div class='col6'><input type='hidden' name='print_style["+b_no+"]' value='"+print_style+"' required nofocus message='�Խ����� ��� ���¸� �����Ͽ� �ֽʽÿ�.'><a onClick='rankup_board.set_print_style("+i+")'>"+classObj.printStyles[print_style]+"</a></div>";

					// �ؽ�Ʈ������ ��� �̹��� ������ ��Ȱ��ȭ
					if(print_style=="text") {
						var inputs = newCell.getElementsByTagName('input');
						inputs[inputs.length-3].className = inputs[inputs.length-2].className = "disable";
						inputs[inputs.length-3].readOnly = inputs[inputs.length-2].readOnly = true;
						inputs[inputs.length-3].removeAttribute("required");
						inputs[inputs.length-2].removeAttribute("required");
					}
					// �̺�Ʈ �ο�
					Event.observe(newCell, 'click', classObj.select_board_item);
					// ����Ʈ ���Ž�
					if(classObj.boardSelObject!=null && bno==classObj.boardNo) {
						newCell.bgColor = classObj.selColor;
						classObj.boardSelObject = newCell;
					}
				}
			}
		}
	});
}

// �Խù� ����
RANKUP_BOARD.prototype.check_all = function(val, img_obj) {
	var nos = document.getElementsByName("no[]");
	for(var i=0, j=0; i<nos.length; i++) {
		var item = nos[i];
		if(item.disabled==true) continue;
		item.checked = val;
	}
	if(img_obj!==undefined) {
		var img_src = ((img_obj.src.indexOf('all.gif')!==-1&&val===true)||val===true) ? img_obj.src.replace(/all.gif/, "cancel.gif") : img_obj.src.replace(/cancel.gif/, "all.gif");
		img_obj.src = img_src;
	}
}

// ���õ� �Խù� ��������
RANKUP_BOARD.prototype.get_check_all = function(no_join) {
	var items = new Array();
	var nos = document.getElementsByName("no[]");
	for(var i=0, j=0; i<nos.length; i++) {
		var item = nos[i];
		if(item.checked==true) items.push(item.value);
	}
	return no_join!=undefined ? items : items.join("__");
}

// ȸ������ ���� - 2009.09.09 added
RANKUP_BOARD.prototype.get_member_infos = function(uid, name) {
	var classObj = this;
	var url = "./multiProcess.ajax.html?mode=member_info&uid="+uid+"&name="+name;
	new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(transport) {
			var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
			if(resultData.getAttribute('result')=="success") {
				var info = resultData.getElementsByTagName('info')[0];
				var level = info.getElementsByTagName('level')[0].getAttribute('no');
				var level_name = info.getElementsByTagName('level')[0].firstChild.nodeValue;
				classObj.member = uid;
				$('author_nick').update(info.getElementsByTagName('nickname')[0].firstChild.nodeValue+" ("+uid+")");
				$('author_level').update(level_name+" ("+level+")");
				$('author_points').update(info.getElementsByTagName('point')[0].firstChild.nodeValue);
				$('author_join').update(info.getElementsByTagName('wdate')[0].firstChild.nodeValue);
				$('author_articles').update(info.getElementsByTagName('anums')[0].firstChild.nodeValue);
				$('author_comments').update(info.getElementsByTagName('cnums')[0].firstChild.nodeValue);
				// ȸ������ ���̱�
				classObj.change_display('authorInfoTable', true);
			}
		}
	});
}

// ��� ���� - 2009.09.09 added
RANKUP_BOARD.prototype.comment_delete = function(no) {
	var nos = (no==undefined) ? this.get_check_all() : no;
	if(nos.length<1) {
		alert("�����Ͻ� ����� �����Ͽ� �ֽʽÿ�."+SPACE);
		return false;
	}
	if(!confirm("�����Ͻ� ����� �����Ͻðڽ��ϱ�?"+SPACE)) return false;
	var url = "./multiProcess.ajax.html?mode=delete_comments&id="+this.board_id+"&nos="+nos;
	new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(transport) {
			var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
			eval(resultData.firstChild.nodeValue);
		}
	});
}

// �⺻���� �̸����� - ��Ų, ���� - 2009.08.31 added
RANKUP_BOARD.prototype.preview_setting = function(form) {
	Wysiwyg.submit_start(); // 2009.10.06 fixed
	var values = Form.serialize(form).toQueryParams();
	values.no = 0;
	values.board_id = '_sample_';
	values.board_name = '�Խ��ǻ���';
	values.uval = '';
	values.mode = 'preview_board';
	new Ajax.Request('./multiProcess.ajax.html', {
		parameters: $H(values).toQueryString(),
		onSuccess: function(trans) {
			// �������� ����
			var board_wnd = window.open(classObj.index_url +"/index.html?id=_sample_", "preview_board");
			board_wnd.focus();
		}
	});
}

// ���������� �̸�����
RANKUP_BOARD.prototype.preview_main = function() {
	var url = (this.setCategory!=="main") ? this.index_url+"/index.html?pcno="+this.setCategory : domain+"main/index.html";
	var board_wnd = window.open(url, "preview_board");
	board_wnd.focus();
}