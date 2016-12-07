/*
 * 랭크업 게시판 사용자 클래스 확장 - 2009.09.09 added
 */
var RANKUP_BOARD_EX = Class.create({
	// 초기설정
	initialize: function() {
		this.version = 'v1.0 r090909';
		this.board_id = rankup_board.board_id;
		this.board_url = rankup_board.board_url;
		this.index_url = rankup_board.index_url;
	},
	// 게시물 선택
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
	// 선택된 게시물 가져오기
	get_check_all: function(no_join) {
		var items = new Array();
		var nos = document.getElementsByName("ano[]");
		for(var i=0, j=0; i<nos.length; i++) { 
			var item = nos[i];
			if(item.checked==true) items.push(item.value);
		}
		return no_join!=undefined ? items : items.join("__");
	},
	// 게시물 이동
	articles_move: function() {
		var anos = this.get_check_all();
		if(!anos.length) {
			alert("이동시킬 게시물을 선택하여 주십시오."+SPACE);
			return false;
		}
		var move_bid = $('move_bid');
		if(move_bid.value.blank()) return doError(move_bid, NO_CHECK);
		if(this.board_id==move_bid.value) {
			alert("같은 게시판으로는 게시물을 이동시킬 수 없습니다."+SPACE);
			return false;
		}
		var move_cno = $('move_cno');
		if(move_cno.getAttribute('required')!=null && move_cno.value.blank()) return doError(move_cno, NO_CHECK);
		var category = move_cno.value ? " '"+ move_cno.options[move_cno.selectedIndex].text +"'분류로" : '으로';
		if(!confirm("선택하신 게시물을 '"+ move_bid.options[move_bid.selectedIndex].text +"'게시판"+ category +" 이동하시겠습니까?"+SPACE)) return false;
		var url = this.board_url+"multiProcess.ajax.html?mode=move_articles&id="+this.board_id+"&anos="+anos+"&move_bid="+move_bid.value+"&move_cno="+move_cno.value;
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				var resultData = transport.responseXML.getElementsByTagName('resultData')[0];
				eval(resultData.firstChild.nodeValue);
			}
		});
	},
	// 게시물 분류
	board_categories: function(el, obj) {
		if(el.value.blank()) {
			obj.options.length = 0;
			obj.options[0] = new Option('분류 선택', '');
			return;
		}
		var url =this.board_url+"multiProcess.ajax.html?mode=board_categories&id="+el.value;
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(transport) {
				var items = transport.responseXML.getElementsByTagName('item');
				obj.options.length = 0;
				if(!items.length) {
					obj.options[0] = new Option('분류 없음', '');
					obj.removeAttribute('required');
				}
				else {
					obj.options[0] = new Option('분류 선택', '');
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