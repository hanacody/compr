/**
 * SITEMAP
 *@author: kurokisi
 *@authDate: 2012.03.20
 */
var sitemap = {
	frame: null, // object
	hover: {sm: null, tm: null},
	on: false,
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
	// 마우스 오버시
	over: function(el) {
		el = $(el); // IE7 lt fix
		var pid = el.getAttribute('pid');
		if(el.nodeName.match(/dd/i)) el = this.frame.select('p[pid="'+ el.getAttribute('parent') +'"]')[0];
		if(this.hover.sm && pid!=this.hover.sm.getAttribute('pid')) { // reset
			if(this.hover.tm) this.hover.tm.removeClassName('hover');
			this.hover.sm.removeClassName('hover');
		}
		this.hover.sm = el, el.addClassName('hover');
		var tm = this.frame.select('dd[pid="'+ pid +'"]')[0];
		if(tm) this.hover.tm = tm, tm.addClassName('hover');
	},
	// 메뉴영역을 벗어난 경우
	reset: function() {
		if(this.on) return;
		if(this.hover.sm) this.hover.sm.removeClassName('hover');
		if(this.hover.tm) this.hover.tm.removeClassName('hover');
		$A(this.frame.select('*[hover="hover"]')).each(function(node) {
			if(node.hasClassName('secon_m')) this.over(node);
			else this.hover.tm = node, node.addClassName('hover');
		}, this);
	}
}