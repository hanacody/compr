/**
 * 플로터
 */
var floater = {
	frame: null,
	baseY: 0, // y축 기본여백
	cycle: 10, // 재귀호출간격
	pro: 10, // 개체이동속도
	initialize: function(frame, baseY, pro, cycle) {
		this.frame = $(frame); // 개체
		this.frame.style.position = 'absolute';
		if(baseY) this.baseY = baseY;
		if(pro) this.pro = pro;
		if(cycle) this.cycle = cycle;

		this.move();
	},
	move: function () {
		Position.prepare();
		var frameTop = this.frame.style.top ? parseInt(this.frame.style.top,10) : this.frame.offsetTop;
		var docTop = (Position.deltaY>this.baseY) ? Position.deltaY : this.baseY;
		var moveY = Math.ceil(Math.abs(frameTop - docTop) / this.pro);
		this.frame.style.top = (frameTop<docTop) ? frameTop + moveY + 'px' : frameTop - moveY + 'px';

		var self = this;
		setTimeout(function() { self.move() }, this.cycle);
	}
}

// 스팸방지 보안코드 이미지 갱신 - 2010.10.13 added
var confirm_code_reset = function() {
	var keystring = $('keystring');
	if(keystring) {
		keystring.value = '';
		var confirm_image = $('confirm_image').select('img')[0];
		var url = confirm_image.src.split('?');
		confirm_image.src = url[0]+ '?dummy='+ Math.random();
	}
}

/**
 * Common initialized
 *@Author: kurokisi
 *@AuthDate: 2010.11.23
 */
var proc = {
	url: '.',
	proc: '/proc.ajax.php',
	method: 'post',
	parameters: function(params, form) {
		if(typeof(params)=='string' && params.blank()) params = {};
		this.params = (form==undefined) ? $H(params) : $H(params).merge(Form.serialize(form, true));
	},
	process: function(proc, debug, url) {
		var self = this;
		if(url==undefined) url = this.url;
		if(debug==true) alert('[url] '+ url + this.proc +'\n[parameters] '+ this.params.toQueryString());
		new Ajax.Request(url + this.proc, {
			method: this.method,
			parameters: this.params,
			onSuccess: function(trans) {
				if(debug==true) alert('[responseText] : '+ trans.responseText);
				if(typeof(proc)=='function') proc(trans);
				else if(!trans.responseText.blank()) self.proc.response(trans);
			},
			onFailure: function(trans) {
				alert('[failure-responseText] '+ trans.responseText);
			}
		});
	},
	response: function(trans) {
		if(trans.responseText.blank()) return;
		trans.responseText.match(/<script/i) ? trans.responseText.evalScripts() : alert(trans.responseText);
	},
	isXML: function(trans) { // 2011.12.20 added
		try { return (trans.responseXML.firstChild.nodeType < Node.DOCUMENT_NODE) }
		catch(e) { return false }
	}
}
// 선택
var checker = {
	initialize: function(spot, selector, top, bottom) {
		this.selector = selector;
		this.objects = {
			spot: $(spot),
			top: $(top==undefined ? 'checker_top' : top),
			bottom: $(bottom==undefined ? 'checker_bottom' : bottom),
			status: {'false': 'btn_select_all', 'true': 'btn_select_cancel'}
		}
	},
	all: function(all) { // 선택반전
		with(this.objects) {
			if(all==undefined) all = top.checked = !top.checked;
			if(bottom!=null) bottom.innerHTML = bottom.innerHTML.replace(eval('/'+status[!all]+'/'), status[all]);
		}
		$A($(this.objects.spot.parentNode).select(this.selector)).each(function(item) { if(item.disabled==false) item.checked = all });
	},
	get: function(extend) { // 선택 값 반환
		var items = [], objects = [];
		$A($(this.objects.spot.parentNode).select(this.selector)).each(function(item) {
			if(item.checked==true) {
				objects.push(item);
				items.push(item.value);
			}
		});
		return (extend==true) ? {items: items.join('__'), objects: objects} : items.uniq().join('__');
	}
}
// 레이어 위치
var position = {
	center: function(frame) { // frame: string
		this.set(frame, 'c', 'c');
	},
	set: function(frame, x, y) {
		frame = $(frame);
		Position.prepare();
		var dms = frame.getDimensions();
		var top = 0, left = 0, marginTop = 0, marginLeft = 0;
		switch(x) {
			case 'l': left = 0; marginLeft = Position.deltaX; break;
			case 'c': left = '50%'; marginLeft = Position.deltaX -(dms.width/2); break;
			case 'r': left = '100%'; marginLeft = Position.deltaX - dms.width; break;
		}
		switch(y) {
			case 't': top = 0; marginTop = Position.deltaY; break;
			case 'c': top = '50%'; marginTop = Position.deltaY - (dms.height/2); break;
			case 'b': top = '100%'; marginTop = Position.deltaY - dms.height; break;
		}
		frame.setStyle({ top: top, left: left, marginTop: marginTop +'px', marginLeft: marginLeft +'px' });
	}
}
// 블라인드
var blind = {
	frame: null,
	draw: function(opacity, zIndex) { // 그리기
		with(document) {
			this.frame = createElement('div');
			body.appendChild(this.frame);
			opacity = (opacity!=undefined) ? opacity : 50;
			$(this.frame).setStyle({
				position: 'absolute',
				backgroundColor: 'black',
				width: parseInt(body.scrollWidth),
				height: parseInt(body.clientHeight), //parseInt(body.scrollHeight),
				filter: 'alpha(opacity='+ opacity +')',
				opacity: (opacity/100),
				zIndex: (zIndex!=undefined) ? zIndex : 1,
				left: 0,
				top: 0
			});
			try {
				this.resizing();
				Event.observe(window, 'resize', this.resizing.bind(this));
			}
			catch(e) {
				//FF 3.6.13 error
				//alert(e.message);
			}
		}
	},
	resizing: function() { // 윈도우 크기 변경시 처리
		with(document) {
			var x1 = parseInt(body.offsetWidth);
			var x2 = parseInt(body.scrollWidth);
			var gap = (x1>x2) ? x1 - x2 : 0;
			var width = body.clientWidth;
			var height = body.clientHeight;
			$(this.frame).setStyle({ width: parseInt(body.offsetWidth) - gap +'px', height: parseInt(body.scrollHeight) +'px' });
		}
	},
	remove: function() { // 블라인드 제거
		Event.stopObserving(window, 'resize', this.resizing.bind(this));
		if(this.frame) this.frame.remove();
	}
}
// 폼 전송 - 2011.08.19 added
var $form = {
	hashes: {}, // optional
	handler: null, // function - optional
	debug: false, // boolean - debugging mode
	blind: false, // blind
	url: '.',
	waiting: '<div id="sending_box"><p>처리중</p><div>잠시만 기다려 주십시오</div></div>',
	submit: function(el, frame, confirmMsg) {
		if(typeof wysiwyg_Class == 'function') Wysiwyg.submit_start();
		if(!validate(Form.getElements(frame))) return false;
		if(confirmMsg!=undefined && !confirm(confirmMsg)) return false;
		if(el.nodeName.match(/form/i)) this.waiting = '';
		if(this.waiting) {
			var wrap = $(el).up(), content = wrap.innerHTML;
			wrap.update(this.waiting);
		}
		if(this.blind==true) blind.draw();
		var self = this;
		proc.parameters(this.hashes, frame);
		proc.process(function(trans) {
			if(typeof self.handler == 'function') self.handler(trans);
			if(self.blind==true) blind.remove();
			if(self.waiting) wrap.update(content);
		}, this.debug, this.url);
	}
}
/**
 * Escaper
 *@authDate: 2012.02.08
 *@usage: Escape.add('class.close()');
 *@usage: Escape.remove('class.close()'); // manual option!
 */
var $esc = {
	stacks: [],
	listening: false,
	add: function(handler) {
		if(!this.stacks.length) {
			Event.observe(document, 'keyup', this.listener);
			this.listening = true;
		}
		if(!this.stacks.include(handler)) this.stacks.push(handler);
	},
	remove: function(handler) {
		this.stacks = this.stacks.without(handler);
		this.kill();
	},
	listener: function(event) {
		if(event.keyCode!=Event.KEY_ESC) return;
		if($esc.stacks.length) { eval($esc.stacks.pop()), $esc.kill() }
	},
	kill: function() {
		if(!this.stacks.length && this.listening) {
			Event.stopObserving(document, 'keyup', this.listener);
			this.listening = false;
		}
	}
}

// 월의 마지막 날 반영
var draw_day = function(y, m, d) {
	y = $(y).value, m = parseInt($(m).value, 10), d = $(d), d.options.length = 1;
	if(!y || !m) return;

	var days = $w('_ 31 28 31 30 31 30 31 31 30 31 30 31');
	if((y%4)==0 && ((y%100)!=0 || (y%400)==0)) days[2] = 29; // 윤달
	$R(1, days[m]).each(function(day) { d.options[d.options.length] = new Option(day+'일', day.toPaddedString(2)) }); // draw
}

var init = {}
