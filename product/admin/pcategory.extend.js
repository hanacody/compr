/**
 * 제품카테고리 클래스
 *@author: kurokisi
 *@authDate: 2011.09.08
 *@note: category extends
 */

var pcategory = (function(object) {
	// 데이터 로드 핸들러
	object.handler = function() {
		//@Do Nothing...
	}

	// 순위
	object.direction = {
		get: function(el) {
			while(!el.nodeName.match(/div/i)) el = $(el).up();
			try {
				var step = el.getAttribute('step');
				var spot = $('step'+ step).select('li[class="body"]')[0];
				var item = object.sel_objects[step];
				if(!item) return false; // terminated

				var first = spot.select('dl')[0];
				var next = item.up().next();
				var prev = item.up().previous();
				var last = $A(spot.select('dl')).last();
				return {
					no: item.getAttribute('no'),
					rank: item.getAttribute('rank'),
					parents: item.getAttribute('parents'),
					step: step,
					item: item,
					spot: spot,
					first: (first===item) ? null : first,
					next: (next==undefined) ? null : next.select('dl')[0],
					prev: (prev==undefined) ? null : prev.select('dl')[0],
					last: (last===item) ? null : last
				}
			}
			catch(e) {
				alert(e.message);
			}
		},
		mover: function(el, kind) {
			var obj = this.get(el);
			if(obj==false) {
				alert('이동시킬 항목을 선택하여 주십시오.');
				return false;
			}
			with(obj) {
				if(eval(kind)==null) {
					alert('이동할 수 없습니다.');
					return false;
				}
				// moving
				new Insertion[['first', 'prev'].include(kind)?'Before':'After'](eval(kind).up(), '<span>'+ item.up().innerHTML +'</span>');
				item.up().remove();
				object.sel_objects[step] = spot.select('dl[no="'+ no +'"]')[0];
				this.save(obj, eval(kind)); // apply
			}
		},
		first: function(el) {
			this.mover(el, 'first');
		},
		up: function(el) {
			this.mover(el, 'prev');
		},
		down: function(el) {
			this.mover(el, 'next');
		},
		last: function(el) {
			this.mover(el, 'last');
		},
		save: function(obj, item) {
			proc.parameters({ mode: 'save_rank', kind: object.kind, no: obj.no, tno: item.getAttribute('no')});
			proc.process(function(trans) { }, false, domain +'rankup_module/rankup_category');
		}
	}

	// 열기
	object.open = function() {
		$esc.add('pcategory.close()');
		this.rframe = $('regist_frame');
		blind.draw();
		this.rframe.show();
		position.center(this.rframe);
		this.rframe.select('input[name="item"]')[0].focus();
	}

	// 닫기
	object.close = function() {
		this.rframe.hide();
		blind.remove();
		// 초기화
		$A(this.rframe.select('input')).each(function(item) {
			if(item.type.match(/radio/i)) this.rframe.select('input[name="'+item.name+'"]')[0].checked = true;
			else if(item.type.match(/checkbox/i)) item.checked = false;
			else item.value = '';
		}, this)
		$esc.remove('pcategory.close()');
	}

	// ENTER
	object.enter = function(event) {
		var el = Event.element(event);
		if(event.keyCode==Event.KEY_RETURN) this.save(el);
	}

	// 정보 추출
	object.spoit = function(el) {
		while(!el.nodeName.match(/div/i)) el = $(el).up();
		return {
			el: el,
			step: el.getAttribute('step'),
			spot: $(el.getAttribute('spot'))
		}
	}

	// 추가
	object.add = function(el) {
		var obj = this.spoit(el);
		this.open();
		this.step = obj.step; // keep
		$('depth').value = obj.step;
		if(obj.step==1) {
			$('no').value = '';
		}
		else {
			var item = this.sel_objects[obj.step-1];
			var parents = item.getAttribute('parents');
			$('no').value = parents ? parents : item.getAttribute('no');
		}
	}

	// 수정
	object.modify = function(el) {
		var obj = this.spoit(el);
		this.step = obj.step; // keep
		var item = this.sel_objects[obj.step];
		if(item==null) {
			alert('수정하실 항목을 선택하여 주십시오.');
			return false;
		}
		this.open();
		$('no').value = item.getAttribute('no');
		$('item').value = item.select('dt')[0].innerHTML;
		$('used_'+ item.getAttribute('used')).checked = true;
	}

	// 저장
	object.save = function(el) {
		var self = this;
		var step = this.step;
		var depth = $F('depth');
		if(!confirm('입력하신 내용을 저장하시겠습니까?')) return false;
		proc.parameters({ mode: 'save', type: 'multi', kind: this.kind }, this.rframe);
		proc.process(function(trans) {
			alert('저장되었습니다.');
			// draw
			var infos = {};
			if(!depth) infos['class'] = 'hover'; // 수정시
			var item = trans.responseXML.getElementsByTagName('item')[0];
			$w('no name depth parents has_child rank used').each(function(field) {
				if(item.getElementsByTagName(field)[0]!=null) {
					var value = item.getElementsByTagName(field)[0].firstChild.nodeValue;
					if(field.match(/has_child/i)) value = (value=='yes' && step<self.max_steps) ? '▶' : '';
					if(field.match(/no/i)) self.items[value] = item; // keep
					infos[field] = value;
				}
			});
			var tmpl = self.template.evaluate(infos);
			var spot = $('step'+ step).select('li[class="body"]')[0];

			if(depth) { // 추가시
				if(!$A(spot.select('span')).length) spot.innerHTML = tmpl;
				else new Insertion.After($A(spot.select('span')).last(), tmpl);
				if(step>1) { // has_child
					var pspot = self.sel_objects[step-1];
					if(!pspot.getAttribute('has_child')) pspot.select('dd[class="child"]')[0].update('▶');
				}
			}
			else { // 수정시
				var _item = self.sel_objects[step]; // object dt
				var no = _item.getAttribute('no');
				new Insertion.After(_item.up(), tmpl);
				_item.up().remove();
				self.sel_objects[step] = spot.select('dl[no="'+ no +'"]')[0];
			}
			self.close();
		}, false, domain +'rankup_module/rankup_category');
	}

	// 삭제
	object.del = function(el) {
		var obj = this.spoit(el);
		var sel = eval('checker'+ obj.step).get(true);
		if(sel.items.blank()) {
			alert('삭제하실 항목을 체크하여 주십시오.');
			return false;
		}
		if(!confirm('체크하신 항목을 삭제하시겠습니까?')) return false;
		var self = this;
		proc.parameters({ mode: 'del', type: 'multi', kind: this.kind, nos: sel.items});
		proc.process(function(trans) {
			if(!trans.responseText.blank()) proc.response(trans);
			else {
				alert('삭제되었습니다.');
				with(obj) {
					$A(sel.objects).each(function(item) {
						while(!item.nodeName.match(/span/i)) item = item.up();
						item.remove();
					});
					if(step>1 && !spot.select('span').length) { // has_child
						self.sel_objects[step-1].up().select('dd[class="child"]')[0].update('');
					}
					self.reset(step); // reset
				}
			}
		}, false, domain +'rankup_module/rankup_category');
	}

	return object;
})(category);
