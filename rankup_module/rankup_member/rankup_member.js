/*
 * 회원관리 클래스
 * @author: kurokisi
 */
var rankup_member = {
	initialize: function(pkind) {
		this.version = '1.0 r091109';
		if(pkind) this.pkind = pkind;
	},
	// Ajax 처리
	procedure: {
		procurl: './multiProcess.ajax.html',
		process: function(proc, debug) {
			if(debug==true) alert('[parameters] : '+ this.params.toQueryString());
			new Ajax.Request(this.procurl, {
				parameters: $H(this.params).toQueryString(),
				onSuccess: function(trans) {
					if(debug==true) alert('[responseText] : '+ trans.responseText);
					if(typeof(proc)=='function') proc(trans);
					else if(!trans.responseText.blank()) trans.responseText.match(/<script/i) ? trans.responseText.evalScripts() : alerts(trans.responseText);
				},
				onFailure: function(trans) { alert(trans.responseText) }
			});
		},
		parameters: function(This, params) { // 파라메터 설정
			this.params = ['object'].include(typeof(params)) ? $H(This.sparams).merge(params) : This.sparams;
		}
	},
	// 선택
	checker: {
		initialize: function(spot, top, bottom) {
			this.objects = {
				spot: $(spot),
				top: $(top==undefined ? 'checker_top' : top),
				bottom: $(bottom==undefined ? 'checker_bottom' : bottom),
				status: {'false': 'select_all.gif', 'true': 'all_cancel.gif'}
			}
		},
		all: function(all) { // 선택반전
			with(this.objects) {
				if(all==undefined) all = top.checked = !top.checked;
				if(bottom!=null) bottom.innerHTML = bottom.innerHTML.replace(eval('/'+status[!all]+'/'), status[all]);
			}
			$A($(this.objects.spot.parentNode).select('input[name="uids[]"]')).each(function(item) { if(item.disabled==false) item.checked = all });
		},
		get: function(This, extend) { // 선택 값 반환
			var items = [], objects = [];
			$A($(this.objects.spot.parentNode).select('input[name="uids[]"]')).each(function(item) {
				if(item.checked==true) {
					objects.push(item);
					items.push(item.value);
				}
			});
			return (extend==true) ? {items: items.join('__'), objects: objects} : items.join('__');
		}
	},
	// 회원삭제
	delete_member: function(uid) {
		var uids = (uid==undefined) ? this.checker.get(this) : uid;
		if(!uids.length) return alerts('삭제하실 회원을 선택하여 주십시오.');
		if(!confirms('선택하신 회원을 삭제하시겠습니까?')) return false;
		var This = this;
		this.procedure.parameters(this, {mode:'delete_member', pkind: this.pkind, uids:uids});
		this.procedure.process(function(trans) {
			if(trans.responseText.blank()) {
				alerts(This.pkind=='secession' ? '삭제되었습니다.' : '탈퇴신청 되었습니다.');
				document.location.reload();
			}
		}, false);
	},
	// 메모보기
	memo_open: function(el, uid) {
		this.memo.object = el;
		this.memo.open(this, uid);
	},
	memo_save: function(form) { this.memo.save(this) },
	memo: {
		initialize: function(frame) {
			this.frame = frame; // string
		},
		open: function(This, uid) {
			var nThis = this;
			This.procedure.parameters(This, {mode: 'load_memo', uid: uid, dummy: String(Math.random()).substr(2)});
			This.procedure.process(function(trans) {
				var memo = {
					editor: $('iframememo').contentWindow.document.body,
					content: $('memo'),
					uid: $('uid')
				}
				$(nThis.frame).show();
				memo.editor.focus();
				memo.editor.innerHTML = memo.content.value = trans.responseXML.getElementsByTagName('memo')[0].firstChild.nodeValue;
				memo.uid.value = uid;
				with(document) {
					var margin = {
						top: Prototype.Browser.IE ? body.scrollTop : 0,
						left: Prototype.Browser.IE ? body.scrollLeft : 0
					};
					$(nThis.frame).setStyle({
						top: margin.top + (body.offsetHeight/2) - ($(nThis.frame).offsetHeight/2),
						left: margin.left + (body.offsetWidth/2) - ($(nThis.frame).offsetWidth/2)
					});
				}
			}, false);
		},
		close: function() {
			$(this.frame).hide();
		},
		save: function(This) {
			Wysiwyg.submit_start(); // 위지윅
			if(!validate(Form.getElements(this.frame))) return false;
			if(!confirms('입력하신 메모를 저장하시겠습니까?')) return false;
			var nThis = this;
			This.procedure.parameters(This, $H({mode: 'save_memo'}).merge(Form.serialize(this.frame).toQueryParams()));
			This.procedure.process(function(trans) {
				if(trans.responseText.blank()) {
					$(nThis.object).setStyle({
						color: $F('memo') ? 'red' : ''
					});
					alerts('저장되었습니다.');
					nThis.close();
				}
			}, false);
		}
	},
	// 탈퇴이유보기
	open_secession: function(el, uid) {
		this.procedure.parameters(this, {mode:'load_secession', uid:uid});
		this.procedure.process(function(trans) {
			if($('reason_frame')!=null) $('reason_frame').remove();
			var reason = trans.responseXML.getElementsByTagName('secession')[0].firstChild.nodeValue;
			while(!el.nodeName.match(/tr/i)) el = $(el).up();
			var secession = '\
				<tr id="reason_frame">\
					<td colspan="11" align="left" style="padding:15px"><div style="width:100%;margin-bottom:8px;padding:4px;border-bottom:#DEDEDE 1px dotted;color:#3399CC"><b><a href="./member_detail.html?uid='+ uid +'\">'+ uid +'</a> 님의 탈퇴이유입니다.</b></div>'+ reason +'</td>\
				</tr>';
			new Insertion.After(el, secession);
		}, false);
	},
	// 문자발송
	send_sms: function(uid) {
		var sms = window.open(domain +'rankup_module/rankup_sms/send_sms_one.html?uid='+uid, 'vpWin', 'width=154,height=400,left=200,top=200');
		sms.focus();
	},
	// 엑셀다운
	excel_download: function() {
		if(!confirms('현재 목록을 엑셀파일로 저장하시겠습니까?')) return false;
		$('multiProcessFrame').src= './multiProcess.ajax.html?mode=ExcelDown&where='+ encodeURIComponent(this.where);
	},
	// 회원종류 변경
	change_kind: function(el) {
		var uids = this.checker.get(this);
		if(!uids.length) {
			alerts('회원종류를 변경하실 회원을 선택하여 주십시오.');
			el.value = '';
			return false;
		}
		var kind_text = (el.value=='personal') ? '개인회원' : '기업회원';
		if(!confirms('선택하신 회원을 "'+ kind_text +'"으로 변경하시겠습니까?')) {
			el.value = '';
			return false;
		}
		this.procedure.parameters(this, {mode: 'change_kind', kind: el.value, uids: uids});
		this.procedure.process(function(trans) {
			if(trans.responseText.blank()) {
				alerts(kind_text +'으로 변경되었습니다.');
				document.location.reload();
			}
		}, false);
	},
	// 회원등급 변경 - 일반회원 - 2010.12.22 added
	change_level: function(el) {
		var uids = this.checker.get(this);
		if(!uids.length) {
			alerts('회원등급을 변경하실 회원을 선택하여 주십시오.');
			el.value = '';
			return false;
		}
		var kind_text = el.options[el.options.selectedIndex].text;
		if(!confirms('선택하신 회원을 "'+ kind_text +'"으로 변경하시겠습니까?')) {
			el.value = '';
			return false;
		}
		this.procedure.parameters(this, {mode: 'change_level', level: el.value, uids: uids});
		this.procedure.process(function(trans) {
			if(trans.responseText.blank()) {
				alerts(kind_text +'으로 변경되었습니다.');
				document.location.reload();
			}
		}, false);
	}
}