/**
 * ��ǰ����
 */
var product = {
	load_cates: function(no, step, spot) {
		var self = this;
		spot = $(spot);
		spot.options.length = 1;
		if(step==1 || no) {
			proc.parameters({mode:'load_category', kind:'product', step:step, no:no});
			proc.process(function(trans) {
				var items = trans.responseXML.getElementsByTagName('item');
				$A(items).each(function(item, index) {
					var infos = {};
					$w('no name used').each(function(field) {
						if(item.getElementsByTagName(field)[0]!=null) {
							var value = item.getElementsByTagName(field)[0].firstChild.nodeValue;
							infos[field] = value;
						}
					});
					// draw
					spot.options[spot.options.length] = new Option(infos.name, infos.no);
				});
			}, false, domain +'rankup_module/rankup_category');
		}
	},
	direction: {
		get: function(el) { // object ����
			el = $(el);
			while(!el.nodeName.match(/tr/i)) el = $(el).up();
			var spot = el.up();
			var next = el.next();
			var prev = el.previous();
			return {
				item: el,
				no: el.select('input[name="no[]"]')[0].value,
				spot: spot, // element
				next: (next==undefined) ? null : next,
				prev: (prev==undefined) ? null : prev
			}
		},
		up: function(el) {
			var obj = this.get(el);
			with(obj) {
				if(prev==null) {
					alert('�̵��� �� �����ϴ�.');
					return false;
				}
				new Insertion.Before(prev, '<tr>'+ item.innerHTML +'</tr>');
				item.remove();
				this.save('up', no);
			}
		},
		down: function(el) {
			var obj = this.get(el);
			with(obj) {
				if(next==null) {
					alert('�̵��� �� �����ϴ�.');
					return false;
				}
				new Insertion.After(next, '<tr>'+ item.innerHTML +'</tr>');
				item.remove();
				this.save('down', no);
			}
		},
		save: function(kind, no) {
			proc.parameters({mode: 'set_direction', kind: kind, no: no});
			proc.process();
		}
	},
	del: function(no) {
		var nos = no || checker.get();
		if(!nos.length) {
			alert('�����Ͻ� ��ǰ�� �����Ͽ� �ֽʽÿ�.');
			return false;
		}
		if(!confirm('�����Ͻ� ��ǰ�� �����Ͻðڽ��ϱ�?')) return false;
		proc.parameters({mode: 'del_product', nos:nos});
		proc.process(function(trans) {
			alert('�����Ǿ����ϴ�.');
			location.reload();
		}, false);
	},
	view: function(el, kind) {
		var nos = checker.get();
		var kind_text = (kind=='main_view') ? '�������' : '���';
		if(!nos.length) {
			alert('['+ kind_text +'����]�� �����Ͻ� ��ǰ�� �����Ͽ� �ֽʽÿ�.');
			el.value = '';
			return false;
		}
		if(!confirm('�����Ͻ� ��ǰ�� ['+ kind_text +'����]�� �����Ͻðڽ��ϱ�?')) {
			el.value = '';
			return false;
		}
		proc.parameters({mode: 'set_view', kind:kind, nos:nos, view:el.value});
		proc.process(function(trans) {
			alert('����Ǿ����ϴ�.');
			location.reload();
		}, false);
	}
}
