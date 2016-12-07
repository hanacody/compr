/**
 * �̱��� ī�װ� Ŭ����
 *@author: kurokisi
 *@authDate: 2011.09.06
 */
var category = {
	version: '1.0 r110906',
	frame: null,
	template: null, // ���������� prototype.js - Template �� ����
	initialize: function(frame) {
		this.frame = $(frame);
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
			}
		}
	},
	add: function() {
		if(this.template==null) {
			alert('���ø��� ���ǵ��� �ʾҽ��ϴ�.');
			return false;
		}
		var items = this.frame.select('tr');
		var values = {identity: Math.random().toString().substr(2)}; // �ĺ��ڵ�
		if(items.length) new Insertion.After(items.last(), this.template.evaluate(values));
		else this.frame.update(this.template.evaluate(values));
	},
	update: function(infos) {
		$H(infos).each(function(item) {
			var el = this.frame.select('input[value="'+ item.key +'"]')[0];
			if(el) {
				el.up().select('input').each(function(input) {
					if(['no[]', 'nos[]'].include(input.name)) input.value = item.value;
					else input.value = '';
				});
			}
		}, this);
		alert('����Ǿ����ϴ�.');
	},
	del: function(el) {
		if(el==undefined) {
			var items = checker.get(true);
			if(items.items.blank()) {
				alert('�����Ͻ� �׸��� �����Ͽ� �ֽʽÿ�.');
				return false;
			}
			var nos = [], objects = [];
			$A(items.objects).each(function(el) {
				while(!el.nodeName.match(/tr/i)) el = $(el).up();
				var no = el.select('input[name="nos[]"]')[0].value;
				if(no) nos.push(no);
				objects.push(el);
			});
			nos = nos.join('__');
			if(!nos) $A(objects).each(function(obj) { obj.remove() });
		}
		else {
			while(!el.nodeName.match(/tr/i)) el = $(el).up();
			var nos = el.select('input[name="nos[]"]')[0].value;
			if(!nos) el.remove();
		}
		if(nos) {
			if(!confirm('�����Ͻ� �׸��� �����Ͻðڽ��ϱ�?')) return false;
			proc.parameters({mode: 'del', type: 'single', nos: nos});
			proc.process(function(trans) {
				if(!trans.responseText.blank()) proc.response(trans);
				else {
					if(objects) $A(objects).each(function(obj) { obj.remove() });
					else el.remove();
					alert('�����Ǿ����ϴ�.');
				}
			}, false, domain +'rankup_module/rankup_category');
		}
	}
}