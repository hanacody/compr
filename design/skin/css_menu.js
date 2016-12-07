/**
 * CSS Menu 클래스
 *@author: kurokisi
 *@authDate: 2011.09.26
 */
var css_menu = {
	frame: null, // object
	hover: {mm: null, sm: null},
	on: false,
	rollover: false,
	initialize: function(frame) {
		this.frame = $(frame);
		var self = this;
		Event.observe(this.frame, 'mouseover', function() { self.on = true });
		Event.observe(this.frame, 'mouseout', function() {
			self.on = false;
			setTimeout(function() { if(!self.on) self.reset() }, 500);
		});
		this.reset();
	},
	over: function(el) { // 마우스 오버시
		el = $(el);
		var pid = el.getAttribute('pid');
		if(this.hover.mm && pid!=this.hover.mm.getAttribute('pid')) {
			this.hover.mm.removeClassName('hover'); // reset
			if(this.rollover) this.hover.mm.innerHTML = this.hover.mm.getAttribute('off');
		}
		if(!el.hasClassName('hover')) {
			if(this.rollover) el.innerHTML = el.getAttribute('on');
			el.addClassName('hover');
		}
		this.hover.mm = el;
		var sm = this.frame.select('ul[class="sm s'+ pid +'"]')[0];
		if(this.hover.sm) this.hover.sm.hide();
		if(sm) {
			this.hover.sm = sm;
			sm.show();
		}

	},
	reset: function() { // 메뉴영역을 벗어난 경우
		if(this.on) return;
		if(this.hover.mm) this.hover.mm.removeClassName('hover');
		if(this.hover.sm) this.hover.sm.hide();
		$A(this.frame.select('li[hover="hover"]')).each(function(node) {
			if(node.up().hasClassName('mm')) {
				if(this.rollover) node.update(node.getAttribute('on'));
				this.over(node);
				throw $break;
			}
		}, this);
		if(!this.hover.mm && !this.hover.sm) {
			$A(this.frame.select('ul[class~="sm"]')).each(function(sm) { sm.hide() });
			this.hover.sm = null;
		}
	}
}