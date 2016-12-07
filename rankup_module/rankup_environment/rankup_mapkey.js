/*
 * 지도키관리 클래스 v1.0
 * @author: kurokisi
 */
var rankup_mapkey = {
	initialize: function(frame, items) {
		this.version = '1.1 r110923';
		this.selectKey = null;
		this.frame = $(frame);
		this.form = this.frame.select('form')[0];
		this.items = $(items);
		this.append_event();
	},
	// 지도키 관리기 열기
	open: function() {
		blind.draw();
		this.frame.show();
		position.center(this.frame);
		if(this.selectKey!=null) {
			this.selectKey.removeAttribute('selected');
			this.selectKey.className = 'attachNormalItem';
			this.selectKey = null;
		}
		this.form.reset();
	},
	// 지도키 관리기 닫기
	close: function() {
		this.frame.hide();
		blind.remove();
	},
	// 지도키 선택 - 수정/삭제시
	select_mapkey: function(event) {
		var el = Event.element(event);
		while(!el.nodeName.match(/li/i)) el = $(el).up();

		if(el===this.selectKey) return false;
		if(this.selectKey!=null) {
			this.selectKey.removeAttribute('selected');
			this.selectKey.className = 'attachNormalItem';
		}
		var items = el.getElementsByTagName('div');

		this.form.mapurl.value = items[0].firstChild.nodeValue.replace(/URL : /, '');
		this.form.mapkey.value = items[1].firstChild.nodeValue.replace(/지도키 : /, '');
		this.form.url.value = this.form.mapurl.value; // keep

		el.className = 'attachSelectItem';
		el.setAttribute('selected', 'selected');
		this.selectKey = el;
	},
	template: new Template('<li onMouseOver="rankup_mapkey.toggle_className(event)" onMouseOut="rankup_mapkey.toggle_className(event)" onClick="rankup_mapkey.select_mapkey(event)"><div>URL : #{mapurl}</div><div>지도키 : #{mapkey}</div></li>'),
	// 지도키등록처리
	save: function() {
		var self = this;
		if(!validate(this.form)) return false;
		if(!confirm('지도키를 저장하시겠습니까?')) return false;
		proc.parameters({ mode: 'save_mapkey' }, this.form);
		proc.process(function(trans) {
			if(!trans.responseText.blank()) proc.response(trans);
			else {
				alert('저장되었습니다.');
				var tmpl = self.template.evaluate({ mapurl: $F('mapurl'), mapkey: $F('mapkey') });
				if($F('url').blank()) { // 추가
					var items = $A(self.items.select('li'));
					if(items.length) new Insertion.After(items.last(), tmpl);
					else self.items.update(tmpl);
				}
				else { // 갱신
					new Insertion.After(self.selectKey, tmpl);
					self.selectKey.remove();
				}
				self.form.reset();
			}
		}, false);
	},
	// 지도키삭제
	del: function() {
		var self = this;
		if($F('url').blank()) {
			alert('삭제하실 지도키를 선택하여 주십시오.');
			return false;
		}
		if(!confirm('선택하신 지도키를 삭제하시겠습니까?')) return false;
		proc.parameters({ mode: 'del_mapkey', url: $F('url') });
		proc.process(function(trans) {
			if(!trans.responseText.blank()) proc.response(trans);
			else {
				self.form.reset();
				$(self.selectKey).remove();
				alerts('삭제되었습니다.');
			}
		});
	},
	// 상위 개체 반환
	get_parent: function(el, parent) {
		var pattern = eval('/'+ parent +'/i');
		while(!el.nodeName.match(pattern)) el = $(el).up();
		return el;
	},
	// 아이템 마우스오버/아웃시
	toggle_className: function(event) {
		var el = this.get_parent(Event.element(event), 'li');
		el.className = (event.type!='mouseover') ? el.getAttribute('selected')!=null ? 'attachSelectItem' : 'attachNormalItem' : el.getAttribute('selected')!=null ? 'attachSHoverItem' : 'attachHoverItem';
	},
	// 지도키 아이템에 이벤트 할당
	append_event: function() {
		$A(this.items.select('li')).each(function(item) {
			Event.observe(item, 'mouseover', this.toggle_className.bind(this));
			Event.observe(item, 'mouseout', this.toggle_className.bind(this));
			Event.observe(item, 'click', this.select_mapkey.bind(this));
		}, this);
	}
}
