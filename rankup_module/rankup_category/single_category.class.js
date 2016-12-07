/**
 * 싱글형 카테고리 클래스
 *@author: kurokisi
 *@authDate: 2011.09.06
 */
var category = {
	version: '1.0 r110906',
	frame: null,
	template: null, // 페이지에서 prototype.js - Template 로 정의
	initialize: function(frame) {
		this.frame = $(frame);
	},
	direction: {
		get: function(el) { // object 리턴
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
					alert('이동할 수 없습니다.');
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
					alert('이동할 수 없습니다.');
					return false;
				}
				new Insertion.After(next, '<tr>'+ item.innerHTML +'</tr>');
				item.remove();
			}
		}
	},
	add: function() {
		if(this.template==null) {
			alert('템플릿이 정의되지 않았습니다.');
			return false;
		}
		var items = this.frame.select('tr');
		var values = {identity: Math.random().toString().substr(2)}; // 식별코드
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
		alert('저장되었습니다.');
	},
	del: function(el) {
		if(el==undefined) {
			var items = checker.get(true);
			if(items.items.blank()) {
				alert('삭제하실 항목을 선택하여 주십시오.');
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
			if(!confirm('선택하신 항목을 삭제하시겠습니까?')) return false;
			proc.parameters({mode: 'del', type: 'single', nos: nos});
			proc.process(function(trans) {
				if(!trans.responseText.blank()) proc.response(trans);
				else {
					if(objects) $A(objects).each(function(obj) { obj.remove() });
					else el.remove();
					alert('삭제되었습니다.');
				}
			}, false, domain +'rankup_module/rankup_category');
		}
	}
}