/*
 * ����Ű���� Ŭ���� v1.0
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
	// ����Ű ������ ����
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
	// ����Ű ������ �ݱ�
	close: function() {
		this.frame.hide();
		blind.remove();
	},
	// ����Ű ���� - ����/������
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
		this.form.mapkey.value = items[1].firstChild.nodeValue.replace(/����Ű : /, '');
		this.form.url.value = this.form.mapurl.value; // keep

		el.className = 'attachSelectItem';
		el.setAttribute('selected', 'selected');
		this.selectKey = el;
	},
	template: new Template('<li onMouseOver="rankup_mapkey.toggle_className(event)" onMouseOut="rankup_mapkey.toggle_className(event)" onClick="rankup_mapkey.select_mapkey(event)"><div>URL : #{mapurl}</div><div>����Ű : #{mapkey}</div></li>'),
	// ����Ű���ó��
	save: function() {
		var self = this;
		if(!validate(this.form)) return false;
		if(!confirm('����Ű�� �����Ͻðڽ��ϱ�?')) return false;
		proc.parameters({ mode: 'save_mapkey' }, this.form);
		proc.process(function(trans) {
			if(!trans.responseText.blank()) proc.response(trans);
			else {
				alert('����Ǿ����ϴ�.');
				var tmpl = self.template.evaluate({ mapurl: $F('mapurl'), mapkey: $F('mapkey') });
				if($F('url').blank()) { // �߰�
					var items = $A(self.items.select('li'));
					if(items.length) new Insertion.After(items.last(), tmpl);
					else self.items.update(tmpl);
				}
				else { // ����
					new Insertion.After(self.selectKey, tmpl);
					self.selectKey.remove();
				}
				self.form.reset();
			}
		}, false);
	},
	// ����Ű����
	del: function() {
		var self = this;
		if($F('url').blank()) {
			alert('�����Ͻ� ����Ű�� �����Ͽ� �ֽʽÿ�.');
			return false;
		}
		if(!confirm('�����Ͻ� ����Ű�� �����Ͻðڽ��ϱ�?')) return false;
		proc.parameters({ mode: 'del_mapkey', url: $F('url') });
		proc.process(function(trans) {
			if(!trans.responseText.blank()) proc.response(trans);
			else {
				self.form.reset();
				$(self.selectKey).remove();
				alerts('�����Ǿ����ϴ�.');
			}
		});
	},
	// ���� ��ü ��ȯ
	get_parent: function(el, parent) {
		var pattern = eval('/'+ parent +'/i');
		while(!el.nodeName.match(pattern)) el = $(el).up();
		return el;
	},
	// ������ ���콺����/�ƿ���
	toggle_className: function(event) {
		var el = this.get_parent(Event.element(event), 'li');
		el.className = (event.type!='mouseover') ? el.getAttribute('selected')!=null ? 'attachSelectItem' : 'attachNormalItem' : el.getAttribute('selected')!=null ? 'attachSHoverItem' : 'attachHoverItem';
	},
	// ����Ű �����ۿ� �̺�Ʈ �Ҵ�
	append_event: function() {
		$A(this.items.select('li')).each(function(item) {
			Event.observe(item, 'mouseover', this.toggle_className.bind(this));
			Event.observe(item, 'mouseout', this.toggle_className.bind(this));
			Event.observe(item, 'click', this.select_mapkey.bind(this));
		}, this);
	}
}
