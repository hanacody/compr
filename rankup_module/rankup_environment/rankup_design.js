/*
 * ��ũ�� ����Ʈ������ Ŭ����
 * @author: kurokisi
 */
var SITE_DESIGN = function() {
	this.designMode = 2;
	this.baseDesign = 1;
	this.picker_items = new Array("menu_bg_color", "menu_item_color");
	this.picker = null;
	this.target = null;
	this.preview = null;
	this.selectAttach = null;
}
// �޴� ������ ���� ���
SITE_DESIGN.prototype.change_menu_item_design = function(el) {
	if(el.value=="text") {
		$('menu_item_design_image').style.display = "none";
		//$('menu_item_design_text').style.display = "block";
	}
	else {
		//$('menu_item_design_text').style.display = "none";
		$('menu_item_design_image').style.display = "block";
	}
}
// ��� �� ������ ����Ʈ ��ŷ
SITE_DESIGN.prototype.screen_blind = function(mode) {
	var blind = $('screenBlindDiv');
	if(mode===true) {
		var _height = parseInt(document.body.clientHeight,10);
		if(parseInt(document.body.scrollHeight,10)>_height) _height = parseInt(document.body.scrollHeight,10);
		blind.style.height = _height + "px";
		blind.style.marginTop = "-" + parseInt(document.body.scrollTop,10) + "px";
		this.change_display("screenBlindDiv", true);

		// SELECT ��ü �����
		this.sb_selects = document.getElementsByTagName("select");
		for(var i=0; i<this.sb_selects.length; i++) this.sb_selects[i].style.visibility = "hidden";
	}
	else {
		this.change_display("screenBlindDiv", false);
		if(this.sb_selects!=null) for(var i=0; i<this.sb_selects.length; i++) this.sb_selects[i].style.visibility = "visible";
	}
}
// ���̾� ���
SITE_DESIGN.prototype.change_display = function(el, val) {
	if(val===true||val===false) {
		var obj = $(el);
		obj.style.display = val ? "block" : "none";
		if(val===false) {
			if(el!=="screenBlindDiv") this.screen_blind(false);
			return false;
		}
		// ������ �߾ӿ� ����
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
// �̹��� ������
SITE_DESIGN.prototype.open_image_manager = function(menu, option) {
	var classObj = this;
	if(option==undefined) option = '';
	var item_box = $('image_item_box');
	this.selectAttach = null;
	item_box.update('');
	this.change_display('imageManagerTable', true);
	var form = document.imageManagerFrm;
	form.reset();
	form.menu.value = menu;	// �޴� ����
	form.option.value = option; // �ɼ� ����
	var url = "./multiProcess.ajax.html?mode=load_image&menu="+menu+"&option="+option;
	new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(transport) {
			var img_name = option ? "hover_menu" : "normal_menu";
			var items = transport.responseXML.getElementsByTagName("item");
			for(var i=0; i<items.length; i++) {
				var item = items[i];
				var fname = item.getElementsByTagName('fname')[0].firstChild.nodeValue;
				var fwidth = item.getElementsByTagName('fwidth')[0].firstChild.nodeValue;
				var fheight = item.getElementsByTagName('fheight')[0].firstChild.nodeValue;
				var ftype = item.getElementsByTagName('ftype')[0].firstChild.nodeValue;
				// ������ ����
				var new_item = document.createElement("li");
				var new_img = new Image();
				new_img.src = fname;
				new_img.align = "absmiddle";
				if(fname.indexOf(img_name+".")==-1) {
					new_img.className = "img_disabled";
					new_item.className = "attachNormalItem";
				}
				else {
					new_img.className = "img_enabled";
					new_item.className = "attachSelectItem";
					new_item.setAttribute("selected", "selected");
					classObj.selectAttach = new_item;
				}
				new_item.setAttribute("fwidth", fwidth);
				new_item.setAttribute("fheight", fheight);
				new_item.setAttribute("fname", fname);
				new_item.setAttribute("ftype", ftype);
				// ���̱�
				new_item.appendChild(new_img);
				item_box.appendChild(new_item);
				// �̺�Ʈ �Ҵ�
				Event.observe(new_item, 'mouseover', classObj.toggle_className);
				Event.observe(new_item, 'mouseout', classObj.toggle_className);
				Event.observe(new_img, 'click', classObj.select_item);
			}
		}
	});
}
// �޴� ��� ��Ų ������
SITE_DESIGN.prototype.open_skin_manager = function(menu) {
	var classObj = this;
	this.selectAttach = null;
	var form = document.registFrm;
	var obj_id = "menuSkinManagerTable";
	var box_id = "menu_skin_item_box";
	var menu_value = form.menu_bg_skin.value;
	if(menu!=="menu_bg") {
		menu_value = form.top_search_skin.value;
		obj_id = "searchSkinManagerTable";
		box_id = "search_skin_item_box";
	}
	this.change_display(obj_id, true);
	// �̺�Ʈ �Ҵ�
	$$("#"+box_id+" li").each(function(li) {
		var img_item = li.getElementsByTagName('img')[0];
		if(menu_value===li.getAttribute('no')) {
			img_item.className = "img_enabled";
			li.className = "attachSelectItem";
			li.setAttribute("selected", "selected");
			classObj.selectAttach = li;
		}
		else img_item.className = "img_disabled";
		Event.observe(li, 'mouseover', classObj.toggle_className);
		Event.observe(li, 'mouseout', classObj.toggle_className);
		Event.observe(li, 'click', classObj.select_item);
	});
}
// �޴� ��� ��Ų ����
SITE_DESIGN.prototype.select_skin = function(menu) {
	var form = document.registFrm;
	var skin_no = this.selectAttach.getAttribute('no');
	var obj_id = "menuSkinManagerTable";
	if(menu=="menu_bg") form.menu_bg_skin.value = skin_no;
	else {
		form.top_search_skin.value = skin_no;
		obj_id = "searchSkinManagerTable";
	}
	$('skin_prev_'+menu).update('');
	var new_img = new Image();

	var image = this.selectAttach.getElementsByTagName('img')[0];
	new_img.style.width = image.width;
	new_img.style.height = image.height;
	new_img.src = image.src;
	$('skin_prev_'+menu).appendChild(new_img);

	this.change_display(obj_id, false);
}
// ÷������ ���ε�
SITE_DESIGN.prototype.attach_post = function(el) {
	if(el.getAttribute("filter")!=null) {
		var filters = el.getAttribute("filter").toLowerCase().split(",");
		var exts = el.value.toLowerCase().split(".");
		if(in_array(exts[exts.length-1], filters)==false) {
			alerts("÷���� �� ���� ���������Դϴ�.");
			document.body.focus();
			return false;
		}
	}
	document.imageManagerFrm.submit();
}
// ������ ���콺����/�ƿ���
SITE_DESIGN.prototype.toggle_className = function(event) {
	//this.className = event.type!="mouseover" ? this.getAttribute("selected")!=null ? "attachSelectItem" : "attachNormalItem" : this.getAttribute("selected")!=null ? "attachSHoverItem" : "attachHoverItem";
	this.getElementsByTagName('img')[0].className = event.type!="mouseover" ? this.getAttribute("selected")!=null ? "img_enabled" : "img_disabled" : this.getAttribute("selected")!=null ? "img_enabled" : "img_hovered";
}
// �̹��� �߰�
SITE_DESIGN.prototype.attach_draw = function(file_name, info) {
	if(this.selectAttach!=null) {
		this.selectAttach.removeAttribute("selected");
		this.selectAttach.className = "attachNormalItem";
		this.selectAttach.getElementsByTagName('img')[0].className = "img_disabled";
	}
	// ������ ����
	var new_item = document.createElement("li");
	new_item.className = "attachSelectItem";
	new_item.setAttribute("selected", "selected");
	this.selectAttach = new_item;

	new_item.setAttribute("fname", file_name);
	new_item.setAttribute("fwidth", info['width']);
	new_item.setAttribute("fheight", info['height']);
	new_item.setAttribute("ftype", info['type']);
	// ���̱�
	$('image_item_box').appendChild(new_item);
	var new_img = new Image();
	new_img.src = file_name;
	new_img.className = "img_enabled";
	new_item.appendChild(new_img);
	// �̺�Ʈ �Ҵ�
	Event.observe(new_item, 'mouseover', this.toggle_className);
	Event.observe(new_item, 'mouseout', this.toggle_className);
	Event.observe(new_img, 'click', this.select_item);
}
// �̹��� ����
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
// �̹��� ����
SITE_DESIGN.prototype.attach_delete = function() {
	if(this.selectAttach==null) return alerts("�����Ͻ� �̹����� �����Ͽ� �ֽʽÿ�.");
	if(!confirms("�����Ͻ� �̹����� �����Ͻðڽ��ϱ�?")) return false;
	var classObj = this;
	var menu = document.imageManagerFrm.menu.value;
	var option = document.imageManagerFrm.option.value;
	var filenames = this.selectAttach.getAttribute('fname').split("\/"+menu+"\/");
	var url = "./multiProcess.ajax.html?mode=delete_image&menu="+menu+"&option="+option+"&file="+filenames[1];
	new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(transport) {
			var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
			alerts(resultData.firstChild.nodeValue);
			if(resultData.getAttribute('result')=="success") {
				// ������ ��� ����
				$('image_item_box').removeChild(classObj.selectAttach);
				classObj.selectAttach = null;
				var img_name = option ? "hover_menu" : "normal_menu";
				if(filenames[1].indexOf(img_name+".")!=-1) option ? $('image_prev_'+menu+'_hover').update('') : $('image_prev_'+menu).update('');
			}
		}
	});
}
// �̹��� ����
SITE_DESIGN.prototype.apply_image = function() {
	if(this.selectAttach==null) return alerts("�޴� �̹����� �����Ͻ� �̹����� �����Ͽ� �ֽʽÿ�.");
	if(!confirms("�����Ͻ� �̹����� �޴� �̹����� ����Ͻðڽ��ϱ�?")) return false;
	var classObj = this;
	var menu = document.imageManagerFrm.menu.value;
	var option = document.imageManagerFrm.option.value;
	var filenames = this.selectAttach.getAttribute('fname').split("\/"+menu+"\/");
	var url = "./multiProcess.ajax.html?mode=apply_image&menu="+menu+"&option="+option+"&file="+filenames[1];
	new Ajax.Request(url, {
		method: 'get',
		onSuccess: function(transport) {
			var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
			if(resultData.getAttribute('result')=="success") {
				classObj.change_display('imageManagerTable', false);
				option ? $('image_prev_'+menu+'_hover').update('') : $('image_prev_'+menu).update('');
				var img_name = option ? "hover_menu" : "normal_menu";
				var new_img = new Image();
				var ext = filenames[1].split(".");
				new_img.style.width = classObj.selectAttach.getAttribute('fwidth');
				new_img.style.height = classObj.selectAttach.getAttribute('fheight');
				new_img.src = filenames[0]+"/"+menu+"/"+img_name+"."+ext[1];
				option ? $('image_prev_'+menu+'_hover').appendChild(new_img) : $('image_prev_'+menu).appendChild(new_img);
			}
			alerts(resultData.firstChild.nodeValue);
		}
	});
}