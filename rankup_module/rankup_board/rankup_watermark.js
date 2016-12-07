/*
 * ��ũ�� ���͸�ũ v1.0
 * @author: kurokisi
 */
var rankup_watermark = {
	initialize: function() {
		this.version = '1.0 r090921';
		this.dialog = null;
		var values = arguments;
		var This = this;
		$w('location transcolor').each(function(object, index) {
			var items = document.getElementsByName('watermark_'+ object);
			$A(items).each(function(item) {
				if(item.value==values[index]) {
					if(!index) setTimeout(function() { This.apply_wmark(item) }, 500);
					item.checked = true;
					throw $break;
				}
			});
		});
		this.frame = $('watermark_frame');
	},
	// ���͸�ũ �̹��� ÷��
	post_wmark: function(el) {
		if(el.getAttribute('filter')!=null) {
			var filters = el.getAttribute('filter').toLowerCase().split(',');
			var ext = el.value.toLowerCase().split('.').last();
			if(filters.include(ext)==false) {
				el.parentNode.innerHTML = el.parentNode.innerHTML;
				return alerts('÷���� �� ���� ���������Դϴ�.');
			}
		}
		if(this.registFrm==null) this.registFrm = document.registFrm;
		var form = this.registFrm;
		var mode = form.mode.value;
		var encType = form.encoding;
		form.mode.value = "post_watermark";
		form.encoding = "multipart/form-data"; // ���ڵ� ���� - ����÷�� ����
		form.submit();

		// ���ڵ� ���� : application/x-www-form-urlencoded
		form.encoding = encType;
		form.mode.value = mode;
		el.parentNode.innerHTML = el.parentNode.innerHTML;
	},
	// ���͸�ũ �̸�����
	draw_wmark: function(file, dimension) {
		var wmark = $('watermark_image').getElementsByTagName('img')[0];
		$('on_watermark').value = file;
		wmark.src = domain +'PEG/watermark/'+ file +'?dummy='+ String(Math.random()).substr(2);;
		wmark.width = dimension[0];
		wmark.height = dimension[1];
		this.apply_wmark(); // �ռ��̹��� ����
	},
	// ���͸�ũ ��ġ ����
	open_location: function(frame) {
		this.dialog = $(frame);
		this.dialog.show();
	},
	// ���͸�ũ �ݿ� - ������ / ���� / ��ġ
	apply_wmark: function(el, initialize) {
		if(typeof(el)=='object') $('watermark_location_text').value = el.parentNode.getAttribute('title');
		if(initialize==true) return false;
		// ���� �̸�����
		if(this.dialog!=null) {
			var This = this;
			setTimeout(function() { This.dialog.hide() }, 0);
		}
		// ���� �ִ��� üũ
		try {
			$w('opacity margin').each(function(item) {
				if(['', null].include($F('watermark_'+ item))) { throw false }
			});
		}
		catch(checksum) {
			if(checksum==false) return false;
		}
		// �̸����� ���͸�ũ ����
		new Ajax.Request('./multiProcess.ajax.html', {
			parameters: 'mode=preview_watermark&'+ Form.serialize(this.frame),
			onSuccess: function(trans) {
				//if(!trans.responseText.blank()) trans.responseText.match(/<script/i) ? trans.responseText.evalScripts() : alert(trans.responseText);
				if(!trans.responseText.blank()) trans.responseText.match(/<script/i) ? trans.responseText.evalScripts() : '';
				else {
					var wmark = $('result_image').getElementsByTagName('img')[0];
					wmark.src = wmark.src.split('?').first() + '?dummy='+ String(Math.random()).substr(2);
				}
			}
		});
	},
	// ���͸�ũ ������ �̹�������
	preview_wmark: {
		show: function(el) {
			this.frame = $('wmark_preview_frame');
			var new_image = new Image();
			new_image.src = $(el).select('img')[0].src;
			new_image.style.border = '1px #dedede solid';
			this.frame.update('');
			this.frame.appendChild(new_image);
			etc_setting.screen_blind(true);
			this.frame.show();
			var margin = {
				top: Prototype.Browser.IE ? document.body.scrollTop : 0,
				left: Prototype.Browser.IE ? document.body.scrollLeft : 0
			}
			this.frame.setStyle({
				top: margin.top+(document.body.offsetHeight/2)-(this.frame.offsetHeight/2),
				left: margin.left+(document.body.offsetWidth/2)-(this.frame.offsetWidth/2)
			});
		},
		hide: function() {
			etc_setting.screen_blind(false);
			this.frame.hide();
		}
	}
}