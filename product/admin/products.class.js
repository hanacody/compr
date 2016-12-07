/**
 * 제품관리
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
		get: function(el) { // object 리턴
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
					alert('이동할 수 없습니다.');
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
					alert('이동할 수 없습니다.');
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
			alert('삭제하실 제품을 선택하여 주십시오.');
			return false;
		}
		if(!confirm('선택하신 상품을 삭제하시겠습니까?')) return false;
		proc.parameters({mode: 'del_product', nos:nos});
		proc.process(function(trans) {
			alert('삭제되었습니다.');
			location.reload();
		}, false);
	},
	view: function(el, kind) {
		var nos = checker.get();
		var kind_text = (kind=='main_view') ? '메인출력' : '출력';
		if(!nos.length) {
			alert('['+ kind_text +'설정]을 변경하실 제품을 선택하여 주십시오.');
			el.value = '';
			return false;
		}
		if(!confirm('선택하신 제품의 ['+ kind_text +'설정]을 변경하시겠습니까?')) {
			el.value = '';
			return false;
		}
		proc.parameters({mode: 'set_view', kind:kind, nos:nos, view:el.value});
		proc.process(function(trans) {
			alert('적용되었습니다.');
			location.reload();
		}, false);
	}
}
