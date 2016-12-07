/**
 * 컬러픽커 클래스
 *@author: kurokisi
 *@authDate: 2011.08.12
 *@updateDate: 2012.02.02, 2012.05.07
 */

var color_picker = {
	version: 'v1.0 r120507',
	panel: null, // object
	content: null, // object
	picker: null, // class object
	target: null,
	handler: null, // function
	sColor: '',
	sRGB: '',
	keepRGB: null,
	onRuning: false, // boolean
	// 초기화
	initialize: function(content) {
		this.panel = $('color_picker_panel'); // 고정
		this.content = $(content);
		this.picker = new YAHOO.widget.ColorPicker('yui-picker-panel', {
			showcontrols: false,
			showhsvcontrols: false,
			showhexcontrols: false,
			animate: false
		});
		var self = this;
		this.picker.on('rgbChange', function(p_oEvent) { // 컬러픽커 활성화
			self.sColor = '#'+ this.get('hex');
			self.sRGB = this.get(this.OPT.RED) +','+ this.get(this.OPT.GREEN) +','+ this.get(this.OPT.BLUE);
			with(self.target) {
				preview.setAttribute('rgb', self.sRGB);
				preview.setStyle({backgroundColor: self.sColor});
				obj.value = self.sColor;
				if(typeof self.handler == 'function') self.handler(self);
			}
		});
		// reset
		$A(this.content.select('*[class~="color"]')).each(function(item) {
			this.target = { obj: item, preview: item.next() }
			if(!item.value.blank()) this.set_preview(this.target.preview, item.value);
		}, this);
	},
	// 입력받은 RGB 값으로 세팅 - 2012.05.07 fixed
	setRGB: function(el, out, handler) {
		if(this.onRuning) return false;
		var hexColor = '#'+ el.value.replace(/#/g, '');
		var preview = $(el).next();
		if((!out && hexColor.length<7) || out && !el.value) {
			if(!el.value) this.set_preview(preview, '#FFFFFF');
			return false;
		}
		try {
			this.set_preview(preview, hexColor);
			el.value = hexColor.toUpperCase();
			if(handler!=undefined) handler(el);
		}
		catch(e) {
			this.onRuning = true;
			alert('입력하신 색상값이 올바르지 않습니다.');
			this.set_preview(preview, '#FFFFFF');
			this.onRuning = false;
			el.value = '';
			el.focus();
			return false;
		}
	},
	// 프리뷰 설정 - 2012.05.07 added
	set_preview: function(preview, hexColor) {
		preview.setStyle({backgroundColor: hexColor});
		preview.setAttribute('rgb', YAHOO.util.Color.hex2rgb(hexColor.replace(/#/g,'')).join(','));
	},
	// 컬러픽커 열기
	open: function(el, handler) {
		if(this.target!=null) this.cancel();
		this.target = { obj: $(el).previous(), preview: $(el) }
		if(handler!=undefined) this.handler = handler;
		var RGB = this.target.preview.getAttribute('rgb');
		RGB = (RGB!=null) ? eval('['+ RGB +']') : [255,255,255];
		this.keepRGB = RGB;
		this.picker.setValue(RGB, false);

		this.panel.style.visibility = 'visible';
		var pointer = Event.pointer(event);
		this.panel.setStyle({left: pointer.x + 20, top: pointer.y});
	},
	cancel: function() {
		// 색상 복원
		if(this.keepRGB!=null) {
			this.picker.setValue(this.keepRGB, false);
			var hexColor = '#'+ this.picker.get('hex');
			with(this.target) {
				this.set_preview(this.target.preview, hexColor);
				obj.value = hexColor;
			}
		}
		this.close();
	},
	close: function() {
		this.panel.style.visibility = 'hidden';
		this.target = null;
	}
}