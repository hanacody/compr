// ��ũ�� �Խ��� ����� Ŭ����
var RANKUP_BOARD = Class.create({
	// �ʱ⼳��
	initialize: function() {
		this.version = 'v2.1 r100617';
		this.comment_items = null;
		this.board_url = domain+"rankup_module/rankup_board/";
		this.index_url = null;
		this.registFrm = null;
		this.selectAttach = null;
		this.board_id = null;
		this.no = null;
		this.attach_max_width = null;
		this.attach_max_nums = null;
		this.clickObject = null;
		this.showLayer = null;
		this.editor_frame = null;
		this.beforeNo = null;
		this.nowNo = null;
		this.vote_kinds = {good:'��õ', bad:'�ݴ�'};
	},
	//�Խù� �μ�
	article_print : function(no) {
		window.open(this.board_url+"article_print.html?no="+no+"&id="+this.board_id, "article_print", "width=800, height=700, scrollbars=yes,status=no,toolbars=no");
	},
	// �Խù� �б�
	article_view: function(no, passwd) {
		var url = this.index_url+"/index.html?cmd=view_article&id="+this.board_id+"&ano="+no+"&passwd="+passwd;
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
				if(resultData.firstChild.nodeValue.indexOf('alert')!=-1) eval(resultData.firstChild.nodeValue);
				else document.location.href = resultData.firstChild.nodeValue;
			}
		});
	},
	// �Խù� ����
	article_modify: function(click_obj, passwd) {
		var classObj = this;
		var url = this.index_url+"/index.html?cmd=modify_article&id="+this.board_id+"&ano="+this.no+"&passwd="+passwd;
		var classObj = this;
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
				eval(resultData.firstChild.nodeValue);
			}
		});
	},
	// �Խù� ����
	article_delete: function(click_obj, passwd) {
		if(passwd!==undefined && !confirm("�Խù��� �����Ͻðڽ��ϱ�?")) return false;
		var classObj = this;
		var url = this.index_url+"/index.html?cmd=delete_article&id="+this.board_id+"&ano="+this.no+"&passwd="+passwd;
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
				eval(resultData.firstChild.nodeValue);
			}
		});
	},
	// �Խù� ����
	article_submit: function(form) {
		var copy_result = "";
		if(validate(form)) {
			if (confirm("�ۼ��Ͻ� ������ HTML�ҽ��� Ŭ�����忡 ���� �Ͻðڽ��ϱ�?\n\n�ٽ� �ۼ��� �ҽ�â�� �ٿ��ֱ�(Ctrl + V) �ϽǼ� �ֽ��ϴ�.")) {
				if(browser.kind == "IE") {
					copy_result= window.clipboardData.setData("Text",form.content.value);
				} else {
					netscape.security.PrivilegeManager.enablePrivilege('UniversalXPConnect');

					var clip = Components.classes['@mozilla.org/widget/clipboard;1'].createInstance(Components.interfaces.nsIClipboard);
					if (!clip) return;

					var trans = Components.classes['@mozilla.org/widget/transferable;1'].createInstance(Components.interfaces.nsITransferable);
					if (!trans) return;
					trans.addDataFlavor('text/unicode');

					var str = new Object();
					var len = new Object();

					var str = Components.classes["@mozilla.org/supports-string;1"].createInstance(Components.interfaces.nsISupportsString);

					var copytext=form.content.value;

					str.data=copytext;

					trans.setTransferData("text/unicode",str,copytext.length*2);

					var clipid=Components.interfaces.nsIClipboard;

					if (!clip) return false;

					coy_result = clip.setData(trans,null,clipid.kGlobalClipboard);
				}
				if (copy_result) alert('Ŭ�����忡 ����Ǿ����ϴ�.');
			} else {
				return true;
			}
		} else {
			return false;
		}
	},
	// �Խù� ����
	check_all: function(val, img_obj) {
		var nos = document.getElementsByName("ano[]");
		for(var i=0, j=0; i<nos.length; i++) {
			var item = nos[i];
			if(item.disabled==true) continue;
			item.checked = val;
		}
		if(img_obj!==undefined) {
			var img_src = ((img_obj.src.indexOf('all.gif')!==-1&&val===true)||val===true) ? img_obj.src.replace(/all.gif/, "cancel.gif") : img_obj.src.replace(/cancel.gif/, "all.gif");
			img_obj.src = img_src;
		}
	},
	// ���õ� �Խù� ��������
	get_check_all: function() {
		var items = new Array();
		var nos = document.getElementsByName("ano[]");
		for(var i=0, j=0; i<nos.length; i++) {
			var item = nos[i];
			if(item.checked==true) items.push(item.value);
		}
		return items.join("__");
	},
	// �Խù� ������ ����
	articles_delete: function() {
		var anos = this.get_check_all();
		if(!anos.length) {
			alert("�����Ͻ� �Խù��� �����Ͽ� �ֽʽÿ�.");
			return false;
		}
		if(!confirm("�����Ͻ� �Խù��� �����Ͻðڽ��ϱ�?")) return false;
		var classObj = this;
		var url = this.index_url+"/index.html?cmd=delete_articles&id="+this.board_id+"&anos="+anos;
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
				eval(resultData.firstChild.nodeValue);
			}
		});
	},
	// �Խù� ��õ
	article_vote: function(kind, key) {
		if(key!==undefined && !confirm("�Խù��� "+this.vote_kinds[kind]+"�Ͻðڽ��ϱ�?")) return false;
		var url = this.index_url+"/index.html?cmd=vote_article&id="+this.board_id+"&ano="+this.no+"&kind="+kind+"&key="+key;
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
				eval(resultData.firstChild.nodeValue);
			}
		});
	},
	// �з��� �˻�
	search_category: function(el) {
		var url = document.location.href.replace(/&scategory=[0-9]{0,3}/g, '');
		//url ������ ���̵� ������ ������ �ʰ� ������ ���̵� ���δ�.
		if(url.match(/id/)) {
			document.location.href= url+"&scategory="+el.value;
		} else {
			document.location.href= url+"&scategory="+el.value+"&id="+this.board_id;
		}
	},

	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// ÷������ ���ε� - �̸����� ����
	attach_post: function(el) {
		if(el.getAttribute("filter")!=null) {
			var filters = el.getAttribute("filter").toLowerCase().split(",");
			var exts = el.value.toLowerCase().split(".");
			if(in_array(exts[exts.length-1], filters)==false) {
				alert("÷���� �� ���� ���������Դϴ�.");
				document.body.focus();
				return false;
			}
		}
		if(this.registFrm==null) this.registFrm = document.articleRegistFrm;
		var form = this.registFrm;
		var mode = form.mode.value;
		var encType = form.encoding;
		form.target = "multiProcessFrame";
		form.mode.value = "post_attached";
		form.encoding = "multipart/form-data"; // ���ڵ� ���� - ����÷�� ����
		form.submit();

		// ���ڵ� ���� : application/x-www-form-urlencoded
		form.encoding = encType;
		form.mode.value = mode;
		form.removeAttribute("target");
		el.parentNode.innerHTML = el.parentNode.innerHTML;
	},
	// �÷��� OBJECT �ٿ� �ֱ�
	append_flash_object: function(obj, m, w, h) {
		var item = "<OBJECT id='flash_item' classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codeBase='http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0' width='"+w+"' height='"+h+"' align='middle' border='0'> <param name='allowScriptAccess' value='always' /> <param name='allowFullScreen' value='true'> <param name='movie' value='"+m+"' /> <param name='quality' value='high' /> <param name='wmode' value='transparent' /> <param name='bgcolor' value='#000000' /> <embed id='banner_item' src="+m+" quality='high' bgcolor='#000000' width='"+w+"' height='"+h+"' align='middle' allowScriptAccess='always' border='0' allowFullScreen='true' quality='high' wmode='transparent' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer' /></OBJECT>";
		obj.innerHTML += item;
	},
	// ������ ���콺����/�ƿ���
	toggle_className: function(event) {
		this.className = event.type!="mouseover" ? this.getAttribute("selected")!=null ? "attachSelectItem" : "attachNormalItem" : this.getAttribute("selected")!=null ? "attachSHoverItem" : "attachHoverItem";
	},
	// ÷������ �ε� - �����ÿ��� ���
	attach_load: function() {
		var attach_items = null;
		var classObj = this;
		var url = this.index_url+"/index.html?cmd=load_attach&id="+this.board_id+"&ano="+this.no;
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				var resultData = transport.responseXML.getElementsByTagName("resultData")[0];
				var configData = resultData.getElementsByTagName('attach')[0];
				classObj.attach_max_width = configData.getAttribute('max_width');
				classObj.attach_max_nums = configData.getAttribute('max_nums');
				attach_items = resultData.getElementsByTagName("item");

				var obj = $('div_attach_item');
				document.getElementsByName('attach')[0].disabled = attach_items.length>=classObj.attach_max_nums;
				if(attach_items.length) {
					for(var i=0; i<attach_items.length; i++) {
						var item = attach_items[i];
						var fname = item.getElementsByTagName('fname')[0].firstChild.nodeValue;
						var ftype = item.getElementsByTagName('ftype')[0].firstChild.nodeValue;
						var fwidth = item.getElementsByTagName('fwidth')[0].firstChild.nodeValue;
						var fheight = item.getElementsByTagName('fheight')[0].firstChild.nodeValue;
						var fsize = item.getElementsByTagName('fsize')[0].firstChild.nodeValue;
						var fwh_size = (fwidth!='' && fheight!='') ? fwidth+" �� "+fheight : '';
						var new_item = document.createElement("li");
						new_item.className = "attachNormalItem";
						new_item.setAttribute("fwidth", fwidth);
						new_item.setAttribute("fheight", fheight);
						new_item.setAttribute("fname", fname);
						new_item.setAttribute("ftype", ftype);
						obj.appendChild(new_item);
						if(in_array(ftype, new Array("jpg", "gif", "bmp", "png"))) {
							var new_img = new Image();
							new_img.style.width = "110px";
							new_img.style.height = "100%";
							new_img.style.border = "#999999 1px solid";
							new_img.src = domain + fname;
							new_item.appendChild(new_img);
							new_item.style.padding = "0px";
							Event.observe(new_img, 'click', classObj.attach_item_preview);
						}
						else {
							new_item.innerHTML = ftype.toUpperCase()+"<br>("+fsize+")<br>"+fwh_size;
							Event.observe(new_item, 'click', classObj.attach_item_preview);
						}
						Event.observe(new_item, 'mouseover', classObj.toggle_className);
						Event.observe(new_item, 'mouseout', classObj.toggle_className);
					}
					new_item.className = "attachSelectItem";
					new_item.setAttribute("selected", "selected");
					// ÷������ �̸�����
					classObj.attach_item_preview(new_item, true);
				}
			}
		});
	},
	// ÷������ ǥ��
	attach_draw: function(file_name, info) {
		// ������ �߰�
		var new_item = document.createElement("li");
		new_item.className = "attachSelectItem";
		new_item.setAttribute("selected", "selected");
		new_item.setAttribute("fname", file_name);
		new_item.setAttribute("fwidth", info['width']);
		new_item.setAttribute("fheight", info['height']);
		new_item.setAttribute("ftype", info['type']);
		$('div_attach_item').appendChild(new_item);
		if(in_array(info['type'], new Array("jpg", "gif", "bmp", "png"))) {
			var new_img = new Image();
			new_img.style.width = "110px";
			new_img.style.height = "100%";
			new_img.style.border = "#999999 1px solid";
			new_img.src = domain + file_name;
			new_item.appendChild(new_img);
			new_item.style.padding = "0px";
			Event.observe(new_img, 'click', this.attach_item_preview);
			var This = this;
			//setTimeout(function() { This.add_content('content'); }, 0); // 2009.08.28 added
		}
		else {
			new_item.innerHTML = info['text'];
			Event.observe(new_item, 'click', this.attach_item_preview);
		}
		Event.observe(new_item, 'mouseover', this.toggle_className);
		Event.observe(new_item, 'mouseout', this.toggle_className);

		// ÷������ �̸�����
		this.attach_item_preview(new_item, true);
		document.getElementsByName('attach')[0].disabled = $('div_attach_item').getElementsByTagName('li').length>=this.attach_max_nums;
	},
	// ���þ����� �̸����� - �� Ŭ���� �������� ���� ���
	attach_item_preview: function(event, draw) {
		var fileObj = event.type=="click" ? Event.element(event) : event;
		if(fileObj.tagName.indexOf("IMG")!=-1) fileObj = fileObj.parentNode;
		if(draw!=true && fileObj===rankup_board.selectAttach) return false;

		var url = domain + fileObj.getAttribute('fname'); // domain : rankup_basic.class.php ���� ����
		var preview = $('div_attach_preview');
		preview.innerHTML = '';
		switch(fileObj.getAttribute('ftype')) {
			case "swf":
				rankup_board.append_flash_object(preview, url, "100%", "100%");
				break;
			case "gif": case "jpg": case "bmp": case "png":
				var img = new Image();
				img.style.width = img.style.height = "100%";
				img.src = url;
				preview.appendChild(img);
				break;
		}
		if(rankup_board.selectAttach!=null) {
			rankup_board.selectAttach.removeAttribute("selected");
			rankup_board.selectAttach.className = "attachNormalItem";
		}
		fileObj.className = "attachSelectItem";
		fileObj.setAttribute("selected", "selected");
		rankup_board.selectAttach = fileObj;
	},
	// ÷������ ����
	attach_delete: function() {
		if(this.selectAttach==null) {
			alert("�����Ͻ� ÷�������� �����Ͽ� �ֽʽÿ�.");
			return false;
		}
		if(!confirm("�����Ͻ� ������ �����Ͻðڽ��ϱ�?")) return false;
		var classObj = this;
		var url = this.index_url+"/index.html?cmd=delete_attach&id="+this.board_id+"&ano="+this.no+"&file="+this.selectAttach.getAttribute('fname');
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
				alert(resultData.firstChild.nodeValue);
				if(resultData.getAttribute('result')=="success") {
					// ������ ��� ����
					$('div_attach_item').removeChild(classObj.selectAttach);
					$('div_attach_preview').update('');
					classObj.selectAttach = null;
					document.getElementsByName('attach')[0].disabled = $('div_attach_item').getElementsByTagName('li').length>=classObj.attach_max_nums;
				}
			}
		});
	},
	// ÷������ ������ ���� -  2009.08.28 fixed
	add_content: function(content) { // content : object_id
		if(this.selectAttach==null) {
			alert("������ ������ ÷�������� �����Ͽ� �ֽʽÿ�.");
			return false;
		}
		var obj = this.selectAttach;
		if(!in_array(obj.getAttribute('ftype'), Array('gif', 'jpg', 'bmp', 'png', 'swf'))) {
			alert("������ ������ �� ���� ���� �����Դϴ�.");
			return false;
		}
		var contentObj = $("iframe"+content).contentWindow;
		switch(obj.getAttribute('ftype')) {
			case "gif": case "jpg": case "bmp": case "png":
				contentObj.focus();
				var width_prefix = parseInt(obj.getAttribute('fwidth'), 10)>this.attach_max_width ? "\" width=\""+this.attach_max_width : '';
				contentObj.document.execCommand('InsertImage', false, domain+obj.getAttribute('fname')+width_prefix); // �̹��� width ����
				break;
			// �÷��� ���� ���� ���� �����ؾ���
			case "swf":
				contentObj.focus();
				var width = obj.getAttribute('fwidth');
				var height = obj.getAttribute('fheight');
				if(width>this.attach_max_width) { // ������ ����
					height = Math.ceil(height / (width / parseInt(this.attach_max_width, 10)));
					width = this.attach_max_width;
				}
				this.append_flash_object(contentObj.document.body, domain+obj.getAttribute('fname'), width, height);
				break;
		}
	},
// ÷������ ������ ���� -  2009.08.28 fixed
	add_content: function(content) { // content : object_id
		if(this.selectAttach==null) {
			alert("������ ������ ÷�������� �����Ͽ� �ֽʽÿ�.");
			return false;
		}
		var obj = this.selectAttach;
		if(!in_array(obj.getAttribute('ftype'), Array('gif', 'jpg', 'bmp', 'png', 'swf'))) {
			alert("������ ������ �� ���� ���� �����Դϴ�.");
			return false;
		}
		//�ش� ������ ������ ����������
		this.editor_frame = $("iframe"+content).contentWindow;
		$('align_frame').show(); // ���̾� ���̱�
		$('image_caption').value = '';
		$('caption_style').value = '';
		$('image_caption').className = '';
		document.getElementsByName('image_align')[0].checked = true;
	},
	// radio / checkbox ���õ� ������ ��ȯ - 2010.06.25 added
	getChecked: function(el) {
		return $(this.registFrm).getInputs('radio', el).find(function(element) {
			return element.checked;
		});
	},
	// ÷������ ������ ���� - 2009.07.06 added - 2010.06.25 upgraded
	apply_content: function() {
		if(this.registFrm==null) this.registFrm = document.articleRegistFrm;
		$('align_frame').hide();
		var obj = this.selectAttach;
		var align = $F(this.getChecked('image_align'));
		var image = {
			src: domain + obj.getAttribute('fname'),
			type: obj.getAttribute('ftype'),
			width: parseInt(obj.getAttribute('fwidth'), 10),
			height: parseInt(obj.getAttribute('fheight'), 10)
		}
		// ������ ����
		if(image.width>this.attach_max_width) {
			image.height = Math.ceil(image.height / (image.width / parseInt(this.attach_max_width, 10)));
			image.width = this.attach_max_width;
		}
		// ĸ��ó�� - 2010.06.25 added
		var caption_info = { value: $F('image_caption'), style: $F('caption_style'), valign: $F(this.getChecked('caption_valign')) };
		var caption = { value: '<div id="#community_caption" class="'+ caption_info.style +'">'+ (caption_info.valign.match(/top/i) ? '��' : '��') +caption_info.value +'</div>', top: '', bottom: '' };
		if(!caption_info.value.blank()) caption[caption_info.valign] = caption.value;

		var content = '<img src="'+ image.src +'" width="'+ image.width +'" align="absmiddle">';
		//URL �Է� üũ 
		if($F('board_image_link') && $F('board_image_link') != "http://" && $F('board_image_link') != null) {
			content = "<a href='"+$F('board_image_link')+"' target='"+$F('board_image_target')+"'>"+content+"</a>";
		}
		if(image.type=='swf') content = '<OBJECT id="flash_item" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codeBase="http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,28,0" width="'+ image.width +'" height="'+ image.height +'" align="middle" border="0"> <param name="allowScriptAccess" value="always" /> <param name="allowFullScreen" value="true"> <param name="movie" value="'+ image.src +'" /> <param name="quality" value="high" /> <param name="wmode" value="opacque" /> <param name="bgcolor" value="#000000" /> <embed id="banner_item" src="+m+" quality="high" bgcolor="#000000" width="'+ image.width +'" height="'+ image.height +'" align="middle" allowScriptAccess="always" border="0" allowFullScreen="true" quality="high" wmode="opacque" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" /></OBJECT>';
		var html = '<table id="community_image" class="'+ align +'" align="'+ align +'" width="'+ image.width +'"><tr><td>'+ caption.top + content + caption.bottom +'</td></tr></table>';
		this.execCommand('inserthtml', html);
	},
	// Ŀ����ġ ���� - 2010.06.25 added
	keep_selRange: function() {
		this.editor_frame.focus();
		this.keepRange = this.getRange(true);
		try { if(Prototype.Browser.IE && this.keepRange!=null && !this.keepRange.text.blank()) this.selRange = this.keepRange; }
		catch(e) { }
	},
	// Ŀ����ġ ���� ��ȯ - 2010.06.25 added
	getRange: function(all) {
		try { var selRange = (Prototype.Browser.IE) ? this.editor_frame.document.selection.createRange() : this.editor_frame.getSelection().getRangeAt(0); }
		catch(e) { }
		if(all==true && Prototype.Browser.IE && this.selRange!=null) {
			if(this.selRange.length) { // anchor ����
				this.editor_frame.document.selection.clear();
				selRange = this.selRange = this.getRange();
			}
			if(!this.selRange.text.blank()) selRange = this.selRange;
			this.selRange = null;
		}
		return selRange;
	},
	// �����Ϳ����� html ���� - 2010.06.25 added
	execCommand: function(cmd, value) {
		this.editor_frame.focus();
		// ���� html �� ������ ���
		if(['inserthtml'].include(cmd)) {
			var tmp_selRange = this.getRange(true);
			var selRange = (this.keepRange!=null) ? this.keepRange : this.getRange(true);
			var wrapTags = (value.match(/{:content:}/)) ? value.split(/{:content:}/) : null;
			if(Prototype.Browser.IE) {
				try {
					if(!tmp_selRange.text.blank()) selRange = tmp_selRange;
					if(wrapTags==null) {
						return selRange.pasteHTML(value.replace(/{:content:}/g, selRange.text ? selRange.htmlText : '&nbsp;'));
					}
					else {
						var html = selRange.htmlText;
						if(html.blank()) html = '<P>&nbsp;</P>';

						html = "<span id='we_spot_begin'></span>"+ wrapTags[0] + html + wrapTags[1] +"<span id='we_spot_end'></span>";
						selRange.pasteHTML(html);

						var begin = this.editor_frame.document.getElementById('we_spot_begin');
						var end = this.editor_frame.document.getElementById('we_spot_end');
						selRange.moveToElementText(begin);

						var xRange = this.editor_frame.document.body.createTextRange();
						xRange.moveToElementText(end);
						selRange.setEndPoint('EndToEnd', xRange);

						this.selRange = selRange.select();

						begin.parentNode.removeChild(begin);
						end.parentNode.removeChild(end);
						return true;
					}
				}
				catch(e) {
					return alerts('�����Ͻ� �̹����� ������ ������ �� �����ϴ�. ��� ������ Ȯ���Ͻð� �ٽ� �õ��Ͻñ� �ٶ��ϴ�.');
				}
			}
			else { // etc
				if(wrapTags==null) {
					var dummy = this.editor_frame.document.createElement('p');
					dummy.appendChild(selRange.cloneContents());
					var html = value.replace(/{:content:}/g, dummy.innerHTML ? dummy.innerHTML : '&nbsp;');
					selRange.deleteContents();
					return selRange.insertNode(selRange.createContextualFragment(html));
				}
				else {
					var dummy = $(this.editor_frame).document.createElement('p');
					dummy.appendChild(selRange.cloneContents());

					var html = dummy.innerHTML;
					if(html.blank()) html = '<P>&nbsp;</P>';

					html = "<span id='we_spot_begin'></span>"+ wrapTags[0] + html + wrapTags[1] +"<span id='we_spot_end'></span>";
					selRange.deleteContents();
					selRange.insertNode(selRange.createContextualFragment(html));

					var begin = $(this.editor_frame).document.getElementById('we_spot_begin');
					var end = $(this.editor_frame).document.getElementById('we_spot_end');

					this.selRange.setStartAfter(begin);
					this.selRange.setEndBefore(end);

					var selection = this.editor_frame.getSelection();
					selection.removeAllRanges();
					selection.addRange(selRange);

					begin.parentNode.removeChild(begin);
					end.parentNode.removeChild(end);
					return true;
				}
			}
		}
		else {
			return this.editor_frame.document.execCommand(cmd, false, value);
		}
	},
	//��б� ��� üũ
	check_mantoman_style: function(el, val) {
		if(val == "mantoman") {
			el.checked = true;
			alert("��б��� �����Ҽ� �����ϴ�.");
		}
	},
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// ���Թ��� �����ڵ� �̹��� ���� - 2010.06.17 added
	confirm_code_reset: function() {
		var keystring = $('keystring');
		if(keystring != null) {
			keystring.value = '';
			var confirm_image = $('confirm_image').select('img')[0];
			var url = confirm_image.src.split('?');
			confirm_image.src = url[0]+ '?dummy='+ Math.random();
		}
	},
	// �ڸ�Ʈ �ε�
	comment_load: function() {
		var classObj = this;
		var url = this.index_url+"/index.html?cmd=load_comment&id="+this.board_id+"&ano="+this.no;
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				$('div_comment_articles').update('');
				var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
				classObj.comment_items = resultData.getElementsByTagName('item');
				for(var i=0; i<classObj.comment_items.length; i++) {
					var item = classObj.comment_items[i];
					var comment_no = item.getAttribute('no');
					var comment_pno = item.getAttribute('pno');
					var remove = item.getAttribute('remove');
					var reply_icon = item.getElementsByTagName('reply_icon')[0].firstChild.nodeValue;
					var comment_icon = item.getElementsByTagName('icon')[0].firstChild.nodeValue;
					var comment_author = item.getElementsByTagName('nickname')[0].firstChild.nodeValue;
					var comment_content = item.getElementsByTagName('content')[0].firstChild.nodeValue;
					var reply_button = ( comment_pno==0 && (eval($('commentRegistFrame')))!=null ) ? "<div class='tool' title='���' onClick=\"rankup_board.comment_reply("+comment_no+")\">R</div>" : "";
					if(item.getElementsByTagName('uip')[0].firstChild.nodeValue) comment_content += "<span class='ip'>( IP: "+item.getElementsByTagName('uip')[0].firstChild.nodeValue+" )</span>";
					var comment_wdate = item.getElementsByTagName('wdate')[0].firstChild.nodeValue;
					comment_icon = comment_icon ? " <img src='"+classObj.board_url+"icon/face_"+comment_icon+".gif' align='absmiddle'>" : '';
					var reply_class = reply_icon ? 'reply' : '';
					var newDiv = document.createElement('div');
					newDiv.id = "div_comment_item";
					if(remove=="no"){
						newDiv.innerHTML = "\
						<ul class='"+ reply_class +"'>\
							<li class='right'>\
								<div class='toolbox'>\
									<div class='tool' title='����' onClick=\"rankup_board.comment_delete("+comment_no+")\">��</div>\
									<div class='tool' title='����' onClick=\"rankup_board.comment_modify("+comment_no+")\">M</div>\
									"+reply_button+"\
								</div>\
								<div class='wdate'>"+comment_wdate+"&nbsp;</div>\
							</li>\
							<li class='left'>\
								<div class='icon "+ reply_class +"'>"+reply_icon+comment_icon+"</div>\
								<div class='author'>"+comment_author+"</div>\
								<div class='content'>"+comment_content+"</div>\
							</li>\
						</ul>";
					}
					else {
						newDiv.innerHTML = "\
						<ul>\
							<li class='right'>\
							</li>\
							<li class='left'>\
								<div class='content' style='padding:2px 5px 0 5px;'><span disabled>������ ����Դϴ�.</span></div>\
							</li>\
						</ul>";
					}
					if(comment_pno==0) {
						var replyDiv = document.createElement('div');
						var replyViewDiv = document.createElement('div');
						replyDiv.id = "div_comment_reply_"+ comment_no;
						replyViewDiv.id = "div_comment_reply_view_"+ comment_no;
						$('div_comment_articles').appendChild(newDiv);
						$('div_comment_articles').appendChild(replyViewDiv);
						$('div_comment_articles').appendChild(replyDiv);
					}
					else {
						$('div_comment_reply_view_'+comment_pno).appendChild(newDiv);
					}
					if(i%2) {
						newDiv.className = "even";
						replyDiv.className = "even";
					}
				}
			}
		});
	},
	// �ڸ�Ʈ ǥ��
	comment_draw: function(info) {
		var form = document.commentRegistFrm;
		var comment_pno = info.pno;
		var reply_icon = info.reply_icon;
		var comment_icon = form.icon.value ? " <img src='"+this.board_url+"icon/face_"+form.icon.value+".gif' align='absmiddle' />" : '';
		var comment_content = document.all ? form.content.value.replace(/\r\n/gi, "<br>") : form.content.value.replace(/\n/gi, "<br>");
		var reply_button = (!comment_pno && (eval($('commentRegistFrame')))!=null ) ? "<div class='tool' title='���' onClick=\"rankup_board.comment_reply("+info.no+")\">R</div>" : '';
		if(info.uip) comment_content += "<span class='ip'>( IP: "+info.uip+" )</span>";
		var newDiv = document.createElement('div');
		newDiv.id = "div_comment_item";
		newDiv.innerHTML = "\
		<ul>\
			<li class='right' style='width:142px; padding-right: 5px;' nowrap>\
				<div class='toolbox'>\
					<div class='tool' title='����' onClick=\"rankup_board.comment_delete("+info.no+")\">��</div>\
					<div class='tool' title='����' onClick=\"rankup_board.comment_modify("+info.no+")\">M</div>\
					"+reply_button+"\
				</div>\
				<div class='wdate'>"+info.wdate+"&nbsp;</div>\
			</li>\
			<li class='left'>\
				<div class='icon'>"+reply_icon+comment_icon+"</div>\
				<div class='author'>"+info.writer+"</div>\
				<div class='content'>"+comment_content+"</div>\
			</li>\
		</ul>";
		if($('div_comment_articles').select("div[id=div_comment_item]").length%2) newDiv.className = "even";
		var cnumObj = $('div_comment_nums').getElementsByTagName('span')[0];
		cnumObj.innerHTML = parseInt(cnumObj.innerHTML, 10) + 1; // ��� �� ����

		this.confirm_code_reset(); // 2010.06.17 added
		form.reset();

		if(comment_pno==0){
			var replyDiv = document.createElement('div');
			var replyViewDiv = document.createElement('div');
			replyDiv.id = "div_comment_reply_"+info.no;
			replyViewDiv.id = "div_comment_reply_view_"+info.no;
			$('div_comment_articles').appendChild(newDiv);
			$('div_comment_articles').appendChild(replyViewDiv);
			$('div_comment_articles').appendChild(replyDiv);
			$('pno').value=comment_pno;
		}
		else {
			$('div_comment_reply_view_'+comment_pno).appendChild(newDiv);
			rankup_board.comment_form_initialize();
			$('pno').value=comment_pno;
		}
	},
	// �ڸ�Ʈ ����
	comment_delete: function(no, passwd) {
		try {
			var click_obj = Event.element(event);
			this.clickObject = click_obj;
		}
		catch(e) {
			var click_obj = this.clickObject;
			this.clickObject = null;
		}
		var classObj = this;
		if(passwd!==undefined && !confirm("����� �����Ͻðڽ��ϱ�?")) return false;
		var url = this.index_url+"/index.html?cmd=delete_comment&id="+this.board_id+"&ano="+this.no+"&passwd="+passwd+"&no="+no;
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
				eval(resultData.firstChild.nodeValue);
			}
		});
	},
	// �ڸ�Ʈ ���� - 2009.09.09 added
	comment_modify: function(no, passwd) {
		try {
			var click_obj = Event.element(event);
			this.clickObject = click_obj;
		}
		catch(e) {
			var click_obj = this.clickObject;
		}
		var classObj = this;
		var url = this.index_url+"/index.html?cmd=modify_comment&id="+this.board_id+"&ano="+this.no+"&passwd="+passwd+"&no="+no;
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
				eval(resultData.firstChild.nodeValue);
			}
		});
	},
	// ��ۿ� ��� - 2011.08.09 added
	comment_reply: function(no, passwd) {
		try {
			var click_obj = Event.element(event);
			this.clickObject = click_obj;
		}
		catch(e) {
			var click_obj = this.clickObject;
		}
		var classObj = this;
		var url = this.index_url+"/index.html?cmd=comment_reply&id="+this.board_id+"&ano="+this.no+"&no="+no;
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
				eval(resultData.firstChild.nodeValue);
			}
		});
	},
	// �ڸ�Ʈ ���� - 2009.09.09 added
	comment_apply: function(frame) {
		var classObj = this;
		var comment = $(frame).getElementsBySelector('textarea[name="comment"]')[0];
		if(!isBlank(comment)) return false;
		if(!confirm("����� �����Ͻðڽ��ϱ�?")) return false;
		new Ajax.Request(this.index_url+"/index.html", {
			parameters: Form.serialize(frame),
			onSuccess: function(transport) {
				if(!transport.responseText.blank()) transport.responseText.match(/<script/i) ? transport.responseText.evalScripts() : alert(transport.responseText);
				else {
					alert("����� ���������� �����Ǿ����ϴ�.");
					var parent_obj = classObj.clickObject.parentNode;
					while(!parent_obj.tagName.match(/li/i)) parent_obj = parent_obj.parentNode;
					parent_obj = parent_obj.parentNode;
					var icon = $(parent_obj).select('div[class~=icon]')[0].getElementsByTagName('img')[0];
					if(icon.src.split(/icon\//i).last().replace(/face_|\.gif/gi, '')=="icon_re") icon = parent_obj.getElementsByTagName('img')[1];
					var icon_infos = icon.src.split(/icon\//i);
					icon_infos[1] = 'face_'+ $('float_icon').value + '.gif';
					icon.src = icon_infos.join('/icon/');
					var content = $(parent_obj).getElementsBySelector('div[class="content"]')[0];
					var pattern = /(<span class=ip>\( IP:.* \)<\/span>|<span class="ip">\( IP:.* \)<\/span>)/gi; //����������üũ
					var ip_view = "";
					if(pattern.test(content.innerHTML)) ip_view = RegExp.$1;
					content.innerHTML = document.all ? $('float_comment').value.replace(/\r\n/gi, "<br>") + ip_view : $('float_comment').value.replace(/\n/gi, "<br>") + ip_view;
					classObj.clickObject = null;
					$(frame).hide();
				}
			}
		});
	},
	// �ڸ�Ʈ �� �ʱ�ȭ - 2011.09.06 added
	comment_form_initialize: function(){
		var form = document.commentRegistFrm;
		form.reset();
		this.hide_icon();
		this.confirm_code_reset();
		$('div_comment_icon').getElementsByTagName('img')[0].src = this.board_url+"icon/face_1.gif";
	},
	// �ڸ�Ʈ �� ��ο�(���+���) - 2011.08.09 added
	comment_reply_form: function(no, obj){
		var comment_area = $("div_comment_reply_"+no);
		var before_comment_area = $("div_comment_reply_"+this.beforeNo);
		var commentRegistHTML;
		if(this.beforeNo==no) return false;
		rankup_board.comment_form_initialize();
		if(no=="bottom") {
			commentRegistHTML = document.createElement('div');
			commentRegistHTML.id = 'commentRegistFrame';
			commentRegistHTML.innerHTML = before_comment_area.innerHTML;
			if(no=="bottom") obj.innerHTML = before_comment_area.innerHTML;
			else obj.parentNode.parentNode.parentNode.parentNode.appendChild(commentRegistHTML);
			before_comment_area.innerHTML='';
			before_comment_area.hide();
			$("view_form").hide();
			$("reply_bottom").show();
			$('reply_margin').hide();
			$('pno').value = 0;
			this.beforeNo = 'bottom';
		}
		else {
			if(this.beforeNo && this.beforeNo!=no && this.beforeNo!="bottom") {
				commentRegistHTML = before_comment_area.innerHTML;
				before_comment_area.innerHTML='';
				before_comment_area.hide();
			}
			else if(!this.beforeNo) {
				commentRegistHTML = $("commentRegistFrame").innerHTML;
				$("commentRegistFrame").remove();
			}
			else if(this.beforeNo=="bottom") {
				commentRegistHTML = $("reply_bottom").innerHTML;
				$("reply_bottom").innerHTML = '';
				$("reply_bottom").hide();
			}
			comment_area.innerHTML=commentRegistHTML;
			comment_area.show();
			$('reply_margin').show();
			$('view_form').show();
			$('pno').value = no;
			this.beforeNo = no;
		}
		this.nowNo = no;
	},
	// �ڸ�Ʈ �� ��ο� - 2009.09.09 added
	comment_form: function(no, obj) {
		this.hide_icon();
		var frame = $('float_comment_frame');
		if(frame!=null) frame.parentNode.removeChild(frame);
		var parent_obj = obj.parentNode;
		while(!parent_obj.id.match(/div_comment_item/i)) parent_obj = parent_obj.parentNode;
		var width = parseInt(parent_obj.offsetWidth) - 10;
		var face = $(parent_obj).select('div[class~=icon]')[0].getElementsByTagName('img')[0].src.split(/icon\//i).last().replace(/face_|\.gif/gi, '');
		if(face=="icon_re") face = $(parent_obj).select('div[class~=icon]')[0].getElementsByTagName('img')[1].src.split(/icon\//i).last().replace(/face_|\.gif/gi, '');
		var comment = $(parent_obj).getElementsBySelector('div[class="content"]')[0].innerHTML;
		comment = document.all ? comment.replace(/<br>/gi, '\n') : comment.replace(/<br>/gi, '\r\n');
		comment = comment.replace(/(<span class=ip>\( IP:.* \)<\/span>|<span class="ip">\( IP:.* \)<\/span>)/gi, ''); //ip ����
		var new_frame = document.createElement('div');
		new_frame.style.backgroundColor = '#ffffff';
		new_frame.style.padding = '0';
		new_frame.id = 'float_comment_frame';
		new_frame.innerHTML = "\
			<input type='hidden' name='mode' value='apply_comment'>\
			<input type='hidden' name='id' value='"+ this.board_id +"'>\
			<input type='hidden' name='no' value='"+ no +"'>\
			<input type='hidden' id='float_icon' name='icon' value='"+ face +"'>\
			<div>\
				<div style='font-weight:bold;padding:5px;background-color:#f7f7f7;border:1px #f0f0f0 solid;'>��ۼ���</div>\
				<div style='margin-top:8px;'>\
					<ul style='list-style:none;margin:0;padding:0;border:0;'>\
						<li style='float:left;width:40px'>\
							<div id='float_comment_icon_box' style='height:20px;border:1px #777 solid;padding:2px 2px;background-color:white;'></div>\
							<div id='float_comment_icon' onClick=\"rankup_board.select_icon(true)\"><img src='"+ domain +"rankup_module/rankup_board/icon/face_"+ face +".gif' align='absmiddle'><span style='font-size:5pt;margin-left:4px;color:#555555'>��</span></div>\
						</li>\
						<li style='float:left;width:87%;'><textarea id='float_comment' name='comment' onClick=\"rankup_board.hide_icon()\" style='width:100%;height:100px;margin-left:4px;padding:4px;font-family:gulim;overflow-y:scroll' required hname='����' class='enable'>"+ comment +"</textarea></li>\
					</ul>\
					<div style='clear:both;width:100%;margin-top:5px;text-align:center'><a onClick=\"rankup_board.comment_apply('float_comment_frame')\"><img src='"+ this.board_url +"img/bt_reply_input.gif' align='absmiddle' hspace='2'></a><a onClick=\"$('float_comment_frame').hide()\"><img src='"+ this.board_url +"img/bt_reply_cancel.gif' align='absmiddle' hspace='2'></a></div>\
				</div>\
			</div>";
		parent_obj.appendChild(new_frame);
		frame = $('float_comment_frame');
		frame.setStyle({
			position: 'absolute',
			clear: 'both',
			zIndex: 2,
			width: width +'px',
			border: '#777777 1px solid',
			backgroundColor: '#ffffff',
			padding: '10px 5px',
			marginLeft: '0px'
		});
	},
	// �ڸ�Ʈ ������ �ݱ� - 2009.09.09 added
	hide_icon: function() {
		$w('div float').each(function(item) {
			var obj = $(item +'_comment_icon_box');
			if(obj!=null) obj.hide();
		});
	},
	// ������ ���ñ� - 2009.09.09 modified
	select_icon: function(floating) {
		this.hide_icon();
		var obj = $((floating==true ? 'float' : 'div') +'_comment_icon_box');
		if(!obj.innerHTML) {
			for(var no=1; no<=10; no++) {
				var newLi = document.createElement('li');
				newLi.innerHTML = "<img src='"+this.board_url+"icon/face_"+no+".gif' no='"+no+"' align='absmiddle'>";
				obj.appendChild(newLi);
			}
		}
		if(obj.innerHTML) {
			Event.observe(document.commentRegistFrm, 'focusin', function() { obj.style.display = "none" });
			$$('#'+ obj.id +' li').each(function(li) {
				Event.observe(li, 'click', function(el) {
					var _obj = Event.element(el);
					if(_obj.src==undefined) return;
					if(floating==true) {
						$('float_comment_icon').getElementsByTagName('img')[0].src = _obj.src;
						$('float_icon').value = _obj.getAttribute('no');
					}
					else {
						$('div_comment_icon').getElementsByTagName('img')[0].src = _obj.src;
						$('icon').value = _obj.getAttribute('no');
					}
					obj.style.display = "none";
				});
			});
		}
		obj.style.display = "block";
		if(this.nowNo && this.nowNo!="bottom"){
			obj.style.width = '235px';
			obj.style.marginLeft = '39px';
		}else
			obj.style.marginLeft = '0px';
	},
	// ��й�ȣ �Է±�
	scanf_passwd: function(no, click_obj, func_name, board_id) {
		var obj = $('div_scanf_passwd');
		if(obj!=null) obj.parentNode.removeChild(obj);
		obj = document.createElement("span");
		obj.id = "div_scanf_passwd";
		obj.style.width = "186px"; // ��������
		obj.style.marginTop = "-21px";
		obj.style.marginRight = "-3px";
		obj.style.marginBottom = "0px";
		obj.style.border = "#777777 1px solid";
		obj.style.padding = "4px";
		obj.style.paddingRight = "0px";
		obj.style.backgroundColor = "white";
		switch(func_name) { // ����Ÿ��
			case "article_view": // ��б� ����
				obj.style.position = "absolute";
				if(click_obj.innerHTML.toLowerCase().indexOf('img')!=-1) {
					obj.style.marginTop = "0px";
					obj.style.marginLeft = "-186px";
				}
				else {
					obj.style.marginTop = "-9px";
					obj.style.marginLeft = "4px";
				}
				if(board_id!==undefined && board_id!='') this.board_id = board_id;
				break;
			case "article_delete": // �Խù� ����
			case "article_modify": // �Խù� ����
				obj.style.position = "absolute";
				obj.style.marginTop = "0px";
				obj.style.marginLeft = "-186px";
				break;
			case "comment_delete":
				obj.style.position = "absolute";
				obj.style.marginTop = "-5px";
				obj.style.marginLeft = "-128px";
				break;
			case "comment_modify": // ��� ���� - 2009.09.09 added
				obj.style.position = "absolute";
				obj.style.marginTop = "-5px";
				obj.style.marginLeft = "-143px";
				break;
			case "comment_reply": // ��ۿ� ��� - 2011.08.09 added
				obj.style.position = "absolute";
				obj.style.marginTop = "-5px";
				obj.style.marginLeft = "-77px";
				break;
		}
		var classObj = this;
		setTimeout(function() {
			obj.innerHTML = "<span style='float:left'><input type='password' style='width:100px;margin:0px;padding:2px;background-color:white;border:#D3D3D3 1px solid;' hname='��й�ȣ' align='absmiddle'></span><span style='float:left'><img src='"+classObj.board_url+"img/bt_reply_input.gif' onClick=\"var pwdObj = this.parentNode.parentNode.getElementsByTagName('input')[0]; if(isBlank(pwdObj)&&isValidUserpw(pwdObj)) rankup_board."+func_name+"("+no+", pwdObj.value)\" style='cursor:pointer;margin:0px;margin-top:1px;margin-left:3px' align='absmiddle'></span><span style='float:left'><img src='"+classObj.board_url+"img/bt_reply_cancel.gif' onClick=\"var spObj = $('div_scanf_passwd'); spObj.parentNode.removeChild(spObj)\" style='cursor:pointer;margin:0px;margin-top:1px;margin-left:3px' align='absmiddle'></span>";
			click_obj.parentNode.insertBefore(obj, click_obj.previousSibling);
			obj.getElementsByTagName('input')[0].focus();
		},0);
	}
});

var rankup_board = new RANKUP_BOARD;
