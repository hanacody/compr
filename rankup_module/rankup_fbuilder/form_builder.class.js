/**
 * FORM Builder 클래스
 *@author: kurokisi
 *@authDate: 2012.02.08
 */
var builder = {
	fields: [], // Array
	initialize: function(container) {
		this.frame.container = $(container);
		this.load();
	},

	get_id: function() {
		return new Date().getTime().toString().substr(4, 8);
	},

	load: function() {
		var self =this;
		proc.parameters({mode: 'load_values', no:$F('fno')});
		proc.process(function(trans) {
			var values = trans.responseText.evalJSON();
			$A(values).each(function(value) {
				self.frame.add(value);
				self.frame.focus(self.frame.all().last());
				value.fields.each(function(field) {
					self.field.add(field);
					self.fields[field.no] = field;
				});
			});
		}, false);
	},

	template: {
		frame: new Template('\
		<div class="group" onMouseDown="builder.frame.focus(this)">\
			<input type="hidden" name="gno[]" value="#{gno}" />\
			<table cellpadding="5" cellspacing="0" border="1" bordercolor="#d7d7d7" class="table1 list_top">\
				<col width="30" />\
				<col width="55" />\
				<col width="40" />\
				<col width="120" />\
				<col width="120" />\
				<col />\
				<col width="50" />\
				<col width="50" />\
				<thead>\
				<tr class="blue_bg">\
					<td colspan="2"><b>폼그룹 이름</b></td>\
					<td colspan="6">\
						<input type="text" id="group_name" name="group_name[]" value="#{group_name}" maxlength="20" class="simpleform" style="width:275px" />\
						<span class="tip">+ 등록폼 상단 타이틀로 출력됩니다.</span>\
					</td>\
				</tr>\
				<tr class="gray_bg" align="center">\
					<td><input type="checkbox" id="checker_top" onClick="builder.checker.all(this.checked)" /></td>\
					<td>순서</td>\
					<td>필수</td>\
					<td>필드명</td>\
					<td>필드형태</td>\
					<td>상세설정</td>\
					<td>힌트</td>\
					<td>출력</td>\
				</tr>\
				</thead>\
				<tbody align="center" valign="top"></tbody>\
			</table>\
		</div>'
		),
		field: new Template('\
			<tr onMouseDown="builder.field.focus(this)">\
				<td>\
					<input type="checkbox" name="no[]" value="#{no}" #{disabled} />\
					<input type="hidden" name="sno" value="#{sno}" />\
					<input type="hidden" name="identity" value="#{identity}" />\
					<input type="hidden" name="fixed" value="#{fixed}" />\
					<input type="hidden" name="view" value="#{view}" />\
					<textarea name="hint" style="display:none">#{hint}</textarea>\
				</td>\
				<td style="padding-top:6px"><a onClick="builder.direction.up(this)"><img src="../../Libs/_images/btn_order_up.gif" hspace="1"></a><a onClick="builder.direction.down(this)"><img src="../../Libs/_images/btn_order_down.gif" hspace="1"></a></td>\
				<td><input type="checkbox" name="required" #{required} #{fixed} onClick="builder.fixed(this)" /></td>\
				<td><input type="text" name="field_name" required hname="필드명" value="#{field_name}" maxlength="20" class="simpleform w100"></td>\
				<td style="padding-top:6px">\
					<select name="field_type" required hname="필드형태" #{fixed} _defalut="#{field_type}" class="w100" onChange="if(!builder.fixed(this)) builder.field.draw(this)">\
					<option value="">필드형태</option>\
					<optgroup label="===기본형태==="></optgroup>\
					<option value="text">텍스트필드</option>\
					<option value="textarea">텍스트에어리어</option>\
					<option value="radio">라디오버튼</option>\
					<option value="checkbox">체크박스</option>\
					<option value="select">셀렉트박스</option>\
					<optgroup label="===가공형태==="></optgroup>\
					<option value="phone">연락처</option>\
					<option value="jumin">주민등록번호</option>\
					<option value="email">이메일</option>\
					<option value="addrs">주소</option>\
					<option value="homepage">홈페이지</option>\
					<option value="attach">첨부파일</option>\
					<option value="calendar">날짜입력</option>\
					<option value="dimension">면적입력</option>\
					</select>\
				</td>\
				<td align="left" valign="middle">#{settings}</td>\
				<td><a onClick="builder.field.hint.open(this)">#{hint_text}</a></td>\
				<td><a onClick="if(builder.fixed(this)) builder.field.view(this)" #{fixed}>#{view_text}</a></td>\
			</tr>'
		),
		entries: {
			none: '<center><span class="tip">필드형태를 선택하여 주십시오.</span></center>',
			text: new Template('\
				가로 <input type="text" name="width" hname="가로크기" required option="number" minval="1" maxval="1000" value="#{width}" maxlength="4" unit=" px" class="simpleform size4"> px,\
				최대글자수 <input type="text" name="maxlength" hname="글자수" required option="number" minval="1" maxval="255" value="#{maxlength}" maxlength="3" unit=" byte" class="simpleform size3"> byte'
			),
			textarea: new Template('\
				가로 <input type="text" name="width" hname="가로크기" required option="number" minval="1" maxval="1000" value="#{width}" maxlength="4" unit="px" class="simpleform size4" /> px,\
				세로 <input type="text" name="height" hname="세로크기" required option="number" minval="1" maxval="1000" value="#{height}" maxlength="4" unit="px" class="simpleform size4" /> px,\
				<input type="checkbox" name="editor" #{editor} id="#{identity}_editor"><label for="#{identity}_editor">위지윅사용</label>'
			),
			// radio, checkbox, select
			multiselect: {
				item: new Template('<div class="item"><input type="text" name="value[]" hname="항목값" required value="#{value}" maxlength="20" class="simpleform" /><p>#{button}</p></div>'),
				button: {
					add: '<a onClick="builder.item.add(this)"><img src="../../Libs/_images/btn_add_s.gif" /></a>',
					del: '<a onClick="builder.item.del(this)"><img src="../../Libs/_images/btn_delete_s.gif" /></a>'
				}
			},
			phone: new Template('\
				<select name="option">\
				<option value="phones">일반전화 + 휴대전화</option>\
				<option value="phone">일반전화</option>\
				<option value="hphone">휴대전화</option>\
				</select> 만 등록허용'
			),
			addrs: new Template('<input type="checkbox" name="search" id="addrs_#{identity}" #{search} /><label for="addrs_#{identity}">우편번호검색 사용</label>'),
			attach: new Template('\
				파일크기 <input type="text" name="limit_size" value="#{limit_size}" maxlength="3" class="simpleform size4" /> MB<br />\
				<div style="margin-top:1px">확장자명 <input type="text" name="allow_ext" value="#{allow_ext}" maxlength="200" class="simpleform size15" /> <span class="tip">콤마(,)로구분</span></div>'
			),
			calendar: new Template('\
				<select name="kind" />\
				<option value="single">단일날짜</option>\
				<option value="dual">기간날짜</option>\
				</select>'
			)
		},
		tools: new Template('\
		<div id="tool_frame">\
			<a onClick="builder.checker.all()" id="checker_bottom"><img src="../../Libs/_images/btn_select_all.gif" align="absmiddle" /></a>\
			<a onClick="builder.field.del()"><img src="../../Libs/_images/btn_select_delete.gif" align="absmiddle" /></a>\
			<a onClick="builder.field.add()"><img src="../../Libs/_images/btn_form_add.gif" align="absmiddle" /></a>\
		</div>'
		),
		view_texts: {
			no: '<font class="no">미출력</font>',
			yes: '<font class="yes">출력</font>'
		},
		hint_texts: ['<font class="invisible">없음</font>', '<font class="visible">있음</font>']
	},

	frame: {
		container: null,
		selected: null,
		focus: function(el) {
			if(this.selected) {
				if(this.selected==el) return;
				this.selected.removeClassName('on');
				this.selected.select('div[id="tool_frame"]')[0].remove(); // tool remove
			}
			el.addClassName('on');
			this.selected = el;
			new Insertion.After(el.select('table')[0], builder.template.tools.evaluate({}));
			// checker initialize
			builder.checker.initialize(el, 'input[name="no[]"]');
			this.gno = el.select('input[name="gno[]"]')[0].value;
		},
		add: function(values) {
			var html = builder.template.frame.evaluate(values||{gno: ''});
			var spot = this.selected ? this.selected : this.all().last();
			spot ? new Insertion.After(spot, html) : this.container.update(html);
			if(values==undefined) {
				this.focus(spot ? spot.next() : this.all().last());
				builder.field.add({identity: builder.get_id()});
			}
			// gno reset - 2012.04.23 fixed
			this.container.select('input[name="gno[]"]').each(function(group, index) { group.value = index+1 });
		},
		all: function() {
			return this.container.select('div[class~="group"]');
		}
	},

	field: {
		selected: null,
		focus: function(el) {
			if(this.selected) {
				if(this.selected==el) return;
				this.selected.removeClassName('on');
			}
			el.addClassName('on');
			this.selected = el;
			this.no = el.select('input[name="no[]"]')[0].value;
		},
		add: function(field) {
			field = field || {identity:builder.get_id(), view:'yes'}

			// default
			if(!$w('yes no').include(field.view)) field.view = 'yes';
			field.view_text = builder.template.view_texts[field.view];
			field.hint_text = field.hint ? builder.template.hint_texts[1] : builder.template.hint_texts[0];

			if(field.required=='yes') field.required = 'checked';
			field['settings'] = this.fetch(field.field_type, field.values);
			var html = builder.template.field.evaluate(field);
			var frame = builder.frame.selected.select('tbody')[0];
			var items = frame.select('tr');
			items.length ? new Insertion.After(items.last(), html) : frame.update(html);

			// SELECT 값 지정
			var current = frame.select('tr').last();
			current.select('select[name="field_type"]')[0].value = field.field_type || ''; // 필드형태
			if(field.field_type=='phone') current.select('select[name="option"]')[0].value = field.values.option; // 연락처
			if(field.field_type=='calendar') current.select('select[name="kind"]')[0].value = field.values.kind; // 기간
		},
		fetch: function(type, values) {
			var html = '';
			var entries = builder.template.entries;
			if(!type) html = entries.none;
			else {
				values.identity = builder.get_id();
				switch(type) {
					case 'textarea': if(values.editor=='yes') values.editor = 'checked';
					case 'addrs': if(values.search=='yes') values.search = 'checked';
					case 'text': case 'phone': case 'attach': case 'calendar':
						if(type=='phone') values.option = values.option || 'phones';
						html = entries[type].evaluate(values);
						break;
					case 'radio': case 'checkbox': case 'select':
						with(entries.multiselect) {
							if(values.items && values.items.length) {
								$A(values.items).each(function(value, index) {
									html += item.evaluate({
										value: value,
										button: index ? button.del : button.add
									});
								});
							}
							else {
								var value = {button: button.add}
								html = item.evaluate(value);
							}
						}
						break;
					case 'email': case 'homepage': case 'jumin': case 'dimension':
						html = '';
						break;
					default:
						html = '미정';
				}
			}
			return html;
		},
		draw: function(el) {
			el = $(el); // IE fix
			var field = builder.fields[this.no], values = {};
			var similar = $w('radio checkbox select');
			var value = {width:100, maxlength:10, height:200, fields:[]} // default value
			if(field && (field.field_type==el.value || (similar.include(field.field_type) && similar.include(el.value)))) value = field.values;
			$(el.up().next()).update(this.fetch(el.value, value));

			// SELECT 값 지정
			if(el.value=='phone' && field && field.values.option) el.up().next().select('select[name="option"]')[0].value = field.values.option; // 연락처
		},
		del: function() {
			var checked = builder.checker.get(true);
			if(!checked.objects.length) {
				alert('삭제할 항목을 선택하여 주십시오.');
				return false;
			}
			if(!confirm('선택하신 항목을 삭제하시겠습니까?')) return false;
			if(!checked.items) this.remover(checked);
			else {
				var self = this;
				proc.parameters({mode: 'del_fields', fno:$F('fno'), nos:checked.items});
				proc.process(function(trans) {
					if(!trans.responseText.blank()) proc.response(trans);
					else self.remover(checked);
				}, false);
			}
		},
		remover: function(checked) {
			$A(checked.objects).each(function(obj) {
				while(!obj.nodeName.match(/tr/i)) obj = obj.up();
				obj.remove();
			});
			// 프레임 체크
			var items = builder.frame.selected.select('tbody')[0].select('tr');
			if(!items.length) {
				if(!confirms('폼그룹에 항목이 없습니다. 폼그룹도 삭제하시겠습니까?')) return false;
				with(builder) {
					var container = frame.selected.previous();
					frame.selected.remove();
					frame.selected = null;
					frame.focus(container);
				}
			}
			alert('삭제되었습니다.');
		},
		view: function(el) {
			var oView = this.selected.select('input[name="view"]')[0];
			var view = (oView.value=='yes') ? 'no' : 'yes';
			$(el).update(builder.template.view_texts[view]);
			oView.value = view;
		},
		hint: {
			open: function(el) {
				blind.draw();
				this.element = $(el);
				this.frame = $('dialog');
				this.frame.show();
				position.center(this.frame);
				var textarea = this.frame.select('textarea')[0];
				textarea.focus();
				textarea.value = builder.field.no ? builder.fields[builder.field.no].hint : '';
				$esc.add('builder.field.hint.close()');
			},
			close: function() {
				this.frame.select('form')[0].reset();
				this.frame.hide();
				blind.remove();
				$esc.remove('builder.field.hint.close()');
			},
			apply: function() {
				var hint = this.frame.select('textarea')[0].value.stripTags();
				builder.field.selected.select('textarea[name="hint"]')[0].value = hint;
				if(builder.field.no) builder.fields[builder.field.no].hint = hint; // update
				this.element.update(builder.template.hint_texts[hint?1:0]);
				this.close();
			}
		}
	},

	direction: {
		get: function(el) {
			while(!el.nodeName.match(/tr/i)) el = $(el).up();
			var next = el.next();
			var prev = el.previous();
			return {
				item: el,
				no: el.select('input[name="no[]"]')[0].value,
				next: (next==undefined) ? null : next,
				prev: (prev==undefined) ? null : prev
			}
		},
		up: function(el) {
			var obj = this.get(el);
			var checkboxes = [], selects = [];
			with(obj) {
				if(prev==null) {
					alert('이동할 수 없습니다.');
					return false;
				}
				new Insertion.Before(prev, '<tr onMouseDown="builder.field.focus(this)">'+ item.innerHTML +'</tr>');

				var new_item = prev.previous();
				builder.field.selected = null;
				builder.field.focus(new_item);

				this.relocate(new_item, item);
				item.remove();
			}
		},
		down: function(el) {
			var obj = this.get(el);
			var checkboxes = [], selects = [];
			with(obj) {
				if(next==null) {
					alert('이동할 수 없습니다.');
					return false;
				}
				new Insertion.After(next, '<tr onMouseDown="builder.field.focus(this)">'+ item.innerHTML +'</tr>');

				var new_item = next.next();
				builder.field.selected = null;
				builder.field.focus(new_item);

				this.relocate(new_item, item);
				item.remove();
			}
		},
		relocate: function(new_item, old_item) {
			var checkboxes = old_item.select('input[type="checkbox"]'), selects = old_item.select('select');
			new_item.select('input[type="checkbox"]').each(function(ifield, index) { ifield.checked = checkboxes[index].checked }, this);
			new_item.select('select').each(function(ifield, index) { ifield.value = selects[index].getValue() }, this);
		}
	},

	item: {
		spoit: function(el, node) {
			while(!el.nodeName.match(eval('/'+ node +'/i'))) el = $(el).up();
			return el;
		},
		add: function(el) {
			with(builder.template.entries.multiselect) {
				var html = item.evaluate({value: '', button: button.del});
			}
			var spot = this.spoit(el, 'td').select('div').last();
			new Insertion.After(spot, html);
		},
		del: function(el) {
			this.spoit(el, 'div').remove();
		}
	},

	checker: {
		initialize: function(frame, selector) {
			this.selector = selector;
			this.objects = {
				spot: frame,
				top: frame.select('*[id="checker_top"]')[0],
				bottom: frame.select('*[id="checker_bottom"]')[0],
				status: {'false': 'btn_select_all', 'true': 'btn_select_cancel'}
			}
		},
		all: function(all) {
			with(this.objects) {
				if(all==undefined) all = top.checked = !top.checked;
				if(bottom!=null) bottom.innerHTML = bottom.innerHTML.replace(eval('/'+status[!all]+'/'), status[all]);
			}
			$A($(this.objects.spot).select(this.selector)).each(function(item) { if(item.disabled==false) item.checked = all });
		},
		get: function(extend) {
			var items = [], objects = [];
			$A($(this.objects.spot).select(this.selector)).each(function(item) {
				if(item.checked==true) {
					objects.push(item);
					items.push(item.value);
				}
			});
			return (extend==true) ? {items: items.uniq().join('__'), objects: objects} : items.uniq().join('__');
		}
	},

	fixed: function(el) {
		if(el.nodeName.match(/a/i)) { // A tag
			if(el.getAttribute('fixed')==null) return true;
			alert('이 항목은 기본항목으로 변경할 수 없습니다.');
		}
		else if(el.getAttribute('fixed')!=null) {
			if(el.nodeName.match(/select/i)) el.value = el.getAttribute('_defalut');
			else if(el.type.match(/checkbox/i)) el.checked = true;
			alert('이 항목은 기본항목으로 변경할 수 없습니다.');
			return true;
		}
		return false;
	},

	save: {
		begin: function(fno) {
			this.fno = fno;
			this.iforms = builder.frame.all();
			this.form(0);
		},
		form: function(iform) {
			this.ifields = this.iforms[iform].select('tbody')[0].select('tr');
			this.field(iform, 0);
		},
		field: function(iform, ifield) {
			var self = this, form = this.ifields[ifield], gno = this.iforms[iform].select('input[name="gno[]"]')[0].value;
			proc.parameters({mode:'save_fields', fno:this.fno, gno:gno, rank:parseInt(ifield)+1}, $(form));
			proc.process(function(trans) {
				var json = trans.responseText.evalJSON();
				$w('no sno identity').each(function(field) { form.select('input[name="'+(field=='no' ? 'no[]' : field)+'"]')[0].value = json[field] });
				if(self.ifields.length>++ifield) self.field(iform, ifield);
				else if(self.iforms.length>++iform) self.form(iform);
			}, false);
		}
	},

	preview: function() {
		var fno = $F('fno');
		if(fno.blank()) {
			alert('등록폼이 생성되지 않았습니다. 먼저 설정사항을 저장하시기 바랍니다.');
			return false;
		}
		var pop = window.open('../../board/write.html?fno='+ fno, 'pop', 'top=0,left=0,menubar=yes,toolbar=yes,resizable=yes,scrollbars=yes');
		pop.focus();
	}
}