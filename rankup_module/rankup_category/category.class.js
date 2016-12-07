/**
 * 랭크업 카테고리 클래스 v2.0
 *@author: kurokisi
 *@authDate: 2011.08.18
 */
var category = {
	version: 'v2.0 r110818',
	frame: null, // object
	kind: null, // category kind
	purl: null, // proc url
	max_steps: 3, // integer
	sel_objects: {}, // hash table {1: object, 2: object, 3: object}
	handler: null, // function
	items: [], // array : xml node
	initialize: function(frame, init, kind, purl) {
		this.frame = $(frame);
		if(kind!=undefined) this.kind = kind;
		for(var step=1; step<=this.max_steps; step++) {
			var obj = this.frame.select('*[id="step'+ step +'"]');
			if(['', null].include(obj)) {
				this.max_steps = step-1;
				break;
			}
		}
		if(purl!=undefined) this.purl = purl;
		this.load('', 1, init);
	},
	template: new Template('<span><dl class="#{class}" no="#{no}" depth="#{depth}" parents="#{parents}" rank="#{rank}" has_child="#{has_child}" used="#{used}" onClick="category.select(event)"><dd class="cbox"><input type="checkbox" name="nos[]" value="#{no}"></dd><dt>#{name}</dt><dd class="used">#{used}</dd><dd class="child">#{has_child}</dd></dl></span>'),
	select: function(event, el) {
		if(el==undefined) el = Event.element(event);
		while(!el.nodeName.match(/dl/i)) el = $(el).up();

		var step = parseInt(el.getAttribute('depth'));
		if(!([undefined, null].include(this.sel_objects[step]))) {
			var prev_no = this.sel_objects[step].getAttribute('no');
			if(prev_no==el.getAttribute('no') && !this.sel_objects[step+1]) return;
			this.sel_objects[step].removeClassName('hover');
		}
		el.addClassName('hover');
		this.sel_objects[step] = el;
		if(step<this.max_steps) { // load
			var no = el.getAttribute('no');
			var next_step = parseInt(step, 10)+1;
			this.load(no, next_step);
		}
		if(typeof this.handler == 'function') this.handler(); // call
	},
	reset: function(step) { // container reset
		for(var i=this.max_steps; i>=step; i--) {
			var spot = $('step'+ i).select('li[class="body"]')[0];
			this.sel_objects[i] = null; // sel objects reset
			if(step<i) spot.innerHTML = ''; //@note: 매끄러운 처리를 위해 해당 step container 는 결과 수신시 리셋
		}
	},
	load: function(no, step, init) {
		var self = this;
		this.reset(step); // container reset
		proc.parameters({mode: 'load_category', kind: this.kind, step: step, no: no});
		proc.process(function(trans) {
			var spot = $('step'+ step).select('li[class="body"]')[0];
			spot.innerHTML = '';
			var items = trans.responseXML.getElementsByTagName('item');
			$A(items).each(function(item, index) {
				var infos = {};
				$w('no name depth parents has_child rank used').each(function(field) {
					if(item.getElementsByTagName(field)[0]!=null) {
						var value = item.getElementsByTagName(field)[0].firstChild.nodeValue;
						if(field.match(/has_child/i)) value = (value=='yes' && step<self.max_steps) ? '▶' : '';
						if(field.match(/no/i)) self.items[value] = item; // keep
						infos[field] = value;
					}
				});
				// draw
				var tmpl = self.template.evaluate(infos);
				index ? new Insertion.After($A(spot.select('span')).last(), tmpl) : spot.innerHTML = tmpl;
			});
			if(init==true) {
				self.select(null, spot.select('dl')[0]);
			}
		}, false, (this.purl!=null) ? this.purl : '.');
	},
	update: function(no) {
		var self = this;
		proc.parameters({mode: 'load_category', no: no});
		proc.process(function(trans) {
			self.items[no] = trans.responseXML.getElementsByTagName('item')[0];
		}, false);
	}
}