/*
 * ��ũ�� �Խ��� ����� Ŭ���� Ȯ�� - 2009.09.09 added
 */
var RANKUP_BOARD_EX = Class.create({
	// �ʱ⼳��
	initialize: function() {
		this.version = 'v1.0 r090909';
		this.board_id = rankup_board.board_id;
		this.board_url = rankup_board.board_url;
		this.index_url = rankup_board.index_url;
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
	get_check_all: function(no_join) {
		var items = new Array();
		var nos = document.getElementsByName("ano[]");
		for(var i=0, j=0; i<nos.length; i++) { 
			var item = nos[i];
			if(item.checked==true) items.push(item.value);
		}
		return no_join!=undefined ? items : items.join("__");
	},
	// �Խù� �̵�
	articles_move: function() {
		var anos = this.get_check_all();
		if(!anos.length) {
			alert("�̵���ų �Խù��� �����Ͽ� �ֽʽÿ�."+SPACE);
			return false;
		}
		var move_bid = $('move_bid');
		if(move_bid.value.blank()) return doError(move_bid, NO_CHECK);
		if(this.board_id==move_bid.value) {
			alert("���� �Խ������δ� �Խù��� �̵���ų �� �����ϴ�."+SPACE);
			return false;
		}
		var move_cno = $('move_cno');
		if(move_cno.getAttribute('required')!=null && move_cno.value.blank()) return doError(move_cno, NO_CHECK);
		var category = move_cno.value ? " '"+ move_cno.options[move_cno.selectedIndex].text +"'�з���" : '����';
		if(!confirm("�����Ͻ� �Խù��� '"+ move_bid.options[move_bid.selectedIndex].text +"'�Խ���"+ category +" �̵��Ͻðڽ��ϱ�?"+SPACE)) return false;
		var url = this.board_url+"multiProcess.ajax.html?mode=move_articles&id="+this.board_id+"&anos="+anos+"&move_bid="+move_bid.value+"&move_cno="+move_cno.value;
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
				eval(resultData.firstChild.nodeValue);
			}
		});
	},
	// �Խù� �з�
	board_categories: function(el, obj) {
		if(el.value.blank()) {
			obj.options.length = 0;
			obj.options[0] = new Option('�з� ����', '');
			return;
		}
		var url =this.board_url+"multiProcess.ajax.html?mode=board_categories&id="+el.value;
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				var items = transport.responseXML.getElementsByTagName('item');
				obj.options.length = 0;
				if(!items.length) {
					obj.options[0] = new Option('�з� ����', '');
					obj.removeAttribute('required');
				}
				else {
					obj.options[0] = new Option('�з� ����', '');
					obj.setAttribute('required', 'required')
					$A(items).each(function(item) {
						obj.options[obj.options.length] = new Option(item.firstChild.nodeValue, item.getAttribute('cno'));
					});
				}
			}
		});
	}
});

var rankup_board_ex = new RANKUP_BOARD_EX;