/**
 * FORM Builder Ŭ����
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
					<td colspan="2"><b>���׷� �̸�</b></td>\
					<td colspan="6">\
						<input type="text" id="group_name" name="group_name[]" value="#{group_name}" maxlength="20" class="simpleform" style="width:275px" />\
						<span class="tip">+ ����� ��� Ÿ��Ʋ�� ��µ˴ϴ�.</span>\
					</td>\
				</tr>\
				<tr class="gray_bg" align="center">\
					<td><input type="checkbox" id="checker_top" onClick="builder.checker.all(this.checked)" /></td>\
					<td>����</td>\
					<td>�ʼ�</td>\
					<td>�ʵ��</td>\
					<td>�ʵ�����</td>\
					<td>�󼼼���</td>\
					<td>��Ʈ</td>\
					<td>���</td>\
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
				<td><input type="text" name="field_name" required hname="�ʵ��" value="#{field_name}" maxlength="20" class="simpleform w100"></td>\
				<td style="padding-top:6px">\
					<select name="field_type" required hname="�ʵ�����" #{fixed} _defalut="#{field_type}" class="w100" onChange="if(!builder.fixed(this)) builder.field.draw(this)">\
					<option value="">�ʵ�����</option>\
					<optgroup label="===�⺻����==="></optgroup>\
					<option value="text">�ؽ�Ʈ�ʵ�</option>\
					<option value="textarea">�ؽ�Ʈ�����</option>\
					<option value="radio">������ư</option>\
					<option value="checkbox">üũ�ڽ�</option>\
					<option value="select">����Ʈ�ڽ�</option>\
					<optgroup label="===��������==="></optgroup>\
					<option value="phone">����ó</option>\
					<option value="jumin">�ֹε�Ϲ�ȣ</option>\
					<option value="email">�̸���</option>\
					<option value="addrs">�ּ�</option>\
					<option value="homepage">Ȩ������</option>\
					<option value="attach">÷������</option>\
					<option value="calendar">��¥�Է�</option>\
					<option value="dimension">�����Է�</option>\
					</select>\
				</td>\
				<td align="left" valign="middle">#{settings}</td>\
				<td><a onClick="builder.field.hint.open(this)">#{hint_text}</a></td>\
				<td><a onClick="if(builder.fixed(this)) builder.field.view(this)" #{fixed}>#{view_text}</a></td>\
			</tr>'
		),
		entries: {
			none: '<center><span class="tip">�ʵ����¸� �����Ͽ� �ֽʽÿ�.</span></center>',
			text: new Template('\
				���� <input type="text" name="width" hname="����ũ��" required option="number" minval="1" maxval="1000" value="#{width}" maxlength="4" unit=" px" class="simpleform size4"> px,\
				�ִ���ڼ� <input type="text" name="maxlength" hname="���ڼ�" required option="number" minval="1" maxval="255" value="#{maxlength}" maxlength="3" unit=" byte" class="simpleform size3"> byte'
			),
			textarea: new Template('\
				���� <input type="text" name="width" hname="����ũ��" required option="number" minval="1" maxval="1000" value="#{width}" maxlength="4" unit="px" class="simpleform size4" /> px,\
				���� <input type="text" name="height" hname="����ũ��" required option="number" minval="1" maxval="1000" value="#{height}" maxlength="4" unit="px" class="simpleform size4" /> px,\
				<input type="checkbox" name="editor" #{editor} id="#{identity}_editor"><label for="#{identity}_editor">���������</label>'
			),
			// radio, checkbox, select
			multiselect: {
				item: new Template('<div class="item"><input type="text" name="value[]" hname="�׸�" required value="#{value}" maxlength="20" class="simpleform" /><p>#{button}</p></div>'),
				button: {
					add: '<a onClick="builder.item.add(this)"><img src="../../Libs/_images/btn_add_s.gif" /></a>',
					del: '<a onClick="builder.item.del(this)"><img src="../../Libs/_images/btn_delete_s.gif" /></a>'
				}
			},
			phone: new Template('\
				<select name="option">\
				<option value="phones">�Ϲ���ȭ + �޴���ȭ</option>\
				<option value="phone">�Ϲ���ȭ</option>\
				<option value="hphone">�޴���ȭ</option>\
				</select> �� ������'
			),
			addrs: new Template('<input type="checkbox" name="search" id="addrs_#{identity}" #{search} /><label for="addrs_#{identity}">�����ȣ�˻� ���</label>'),
			attach: new Template('\
				����ũ�� <input type="text" name="limit_size" value="#{limit_size}" maxlength="3" class="simpleform size4" /> MB<br />\
				<div style="margin-top:1px">Ȯ���ڸ� <input type="text" name="allow_ext" value="#{allow_ext}" maxlength="200" class="simpleform size15" /> <span class="tip">�޸�(,)�α���</span></div>'
			),
			calendar: new Template('\
				<select name="kind" />\
				<option value="single">���ϳ�¥</option>\
				<option value="dual">�Ⱓ��¥</option>\
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
			no: '<font class="no">�����</font>',
			yes: '<font class="yes">���</font>'
		},
		hint_texts: ['<font class="invisible">����</font>', '<font class="visible">����</font>']
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

			// SELECT �� ����
			var current = frame.select('tr').last();
			current.select('select[name="field_type"]')[0].value = field.field_type || ''; // �ʵ�����
			if(field.field_type=='phone') current.select('select[name="option"]')[0].value = field.values.option; // ����ó
			if(field.field_type=='calendar') current.select('select[name="kind"]')[0].value = field.values.kind; // �Ⱓ
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
						html = '����';
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

			// SELECT �� ����
			if(el.value=='phone' && field && field.values.option) el.up().next().select('select[name="option"]')[0].value = field.values.option; // ����ó
		},
		del: function() {
			var checked = builder.checker.get(true);
			if(!checked.objects.length) {
				alert('������ �׸��� �����Ͽ� �ֽʽÿ�.');
				return false;
			}
			if(!confirm('�����Ͻ� �׸��� �����Ͻðڽ��ϱ�?')) return false;
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
			// ������ üũ
			var items = builder.frame.selected.select('tbody')[0].select('tr');
			if(!items.length) {
				if(!confirms('���׷쿡 �׸��� �����ϴ�. ���׷쵵 �����Ͻðڽ��ϱ�?')) return false;
				with(builder) {
					var container = frame.selected.previous();
					frame.selected.remove();
					frame.selected = null;
					frame.focus(container);
				}
			}
			alert('�����Ǿ����ϴ�.');
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
					alert('�̵��� �� �����ϴ�.');
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
					alert('�̵��� �� �����ϴ�.');
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
			alert('�� �׸��� �⺻�׸����� ������ �� �����ϴ�.');
		}
		else if(el.getAttribute('fixed')!=null) {
			if(el.nodeName.match(/select/i)) el.value = el.getAttribute('_defalut');
			else if(el.type.match(/checkbox/i)) el.checked = true;
			alert('�� �׸��� �⺻�׸����� ������ �� �����ϴ�.');
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
			alert('������� �������� �ʾҽ��ϴ�. ���� ���������� �����Ͻñ� �ٶ��ϴ�.');
			return false;
		}
		var pop = window.open('../../board/write.html?fno='+ fno, 'pop', 'top=0,left=0,menubar=yes,toolbar=yes,resizable=yes,scrollbars=yes');
		pop.focus();
	}
}