/**
 * IMAGE·BOX v1.0
 *@author: kurokisi
 *@authDate: 2011.08.22
 */

var image_box = {
	version: 'v1.0 r 110822',
	frame: null, // object
	_default: null, // object
	selected: null, // object
	handler: null, // function
	initialize: function(frame, handler) {
		this.frame = $(frame);
		this.handler = handler;
		var el = this.frame.select('dt')[0].select('div[class="selected"]')[0];
		this.set(el);
	},
	set: function(el) {
		if(el) {
			this._default = el;
			this.selector(el);
		}
		else {
			this._default = null;
			if(this.selected!=null) this.selected.removeClassName('selected');
			this.selected = null;
		}
	},
	open: function() {
		blind.draw();
		this.frame.show();
		position.center(this.frame);
	},
	close: function() {
		blind.remove();
		this.frame.hide();
		if(this._default!=null && this._default!=this.selected) { // reset default
			if(this.selected!=null) this.selected.removeClassName('selected');
			this._default.addClassName('selected');
			this.selected = this._default;
		}
	},
	post: attachment.post,
	post_reset: attachment.reset,
	template: new Template('<div class="hover" onMouseOver="$(this).addClassName(\'hover\')" onMouseOut="$(this).removeClassName(\'hover\')" onClick="image_box.selector(this)" onDblClick="image_box.apply()" name="#{name}"><img src="#{domain}#{folder}#{name}" align="absmiddle"></div>'),
	draw: function(infos) {
		infos.domain = domain;
		var tmpl = this.template.evaluate(infos);
		var spot = this.frame.select('dt')[0];
		var items = spot.select('div');
		if(!items.length) spot.innerHTML = tmpl;
		else new Insertion.After($A(items).last(), tmpl);
		spot.scrollTop = spot.scrollHeight;
	},
	selector: function(el) {
		if(this.selected!=null) this.selected.removeClassName('selected');
		while(!el.nodeName.match(/div/i)) el = $(el).up();
		el.addClassName('selected');
		this.selected = el;
	},
	apply: function(fix) {
		if(typeof this.handler == 'function') this.handler(this, fix);
	},
	del: function(notice) {
		if(this.selected==null) {
			alert('삭제하실 이미지를 선택하여 주십시오.');
			return false;
		}
		if(notice==undefined) notice = '';
		if(!confirm(notice +'선택하신 이미지를 삭제하시겠습니까?')) return false;
		var self = this;
		var kind = this.frame.select('input[name="kind"]')[0];
		var name = this.selected.getAttribute('name');
		var index = this.frame.select('input[name="index"]')[0];
		var no = index ? index.value : '';
		proc.parameters({mode: 'del_attach', kind: kind.value, name: name, index: no});
		proc.process(function(trans) {
			if(!trans.responseText.blank()) proc.response(trans);
			else {
				var obj = self.frame.select('dt')[0].select('div[name="'+ name +'"]')[0];
				if(obj) {
					obj.remove();
					self.set(), self.apply(true);
				}
				alert('삭제되었습니다.');
			}
		}, false, domain +'rankup_module/rankup_builder');
	}
}
