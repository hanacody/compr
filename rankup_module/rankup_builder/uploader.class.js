/**
 * 파일업로더 v1.0
 *@author: kurokisi
 *@authDate: 2011.09.30
 */
var uploader = {
	frame: null,
	form: null,
	timer: null,
	initialize: function(frame, form) {
		this.frame = $(frame);
		this.form = $(form);
	},
	post: attachment.post,
	post_reset: attachment.reset,
	open: function(kind, name, selector, pid) {
		this.selector = selector;
		this.pid = pid;
		blind.draw();
		this.frame.show();
		position.center(this.frame);
		// form setting
		this.frame.select('input[name="kind"]')[0].value = kind;
		$('base_name').update(name);

		// preview draw
		var item = this.form.select('input[name="'+ selector +'"]')[0];
		var button = $(item).up().select('a').last();
		if(button!=null) {
			$('uploader_preview').update(this.fetch(button).html);
		}
	},
	close: function() {
		$('uploader_preview').update('');
		this.frame.hide();
		blind.remove();
	},
	draw: function(infos) {
		infos.domain = domain;
		with(infos) {
			var html = '';
			if(type.match(/image/i)) html = this.template.image.evaluate(infos);
			else if(type.match(/flash/i)) html = this.template.flash.evaluate(infos);
			$('uploader_preview').update(html);
		}
		this.infos = infos; // keep
	},
	apply: function() {
		with(this.infos) {
			var item = this.form.select('input[name="'+this.selector +'"]')[0];
			item.value = name;
			//
			var item_widths = this.form.select('input[name="widths['+ this.pid +']"]')[0];
			var item_heights = this.form.select('input[name="heights['+ this.pid +']"]')[0];
			if(item_widths) item_widths.value = width;
			if(item_heights) item_heights.value = height;
		}
		this.preview_button(item, this.infos);
		this.close();
	},
	template: {
		image: new Template('<img src="#{domain}#{folder}#{name}" align="absmiddle" />'),
		flash: new Template('<embed src="#{domain}#{folder}#{name}" width="#{width}" height="#{height}" wmode="transparent"></embed>'),
		button: new Template('<a onMouseOver="uploader.preview(this)" onMouseOut="uploader.preview_close()" folder="#{folder}" name="#{name}"><img src="./img/btn_img_view.gif" align="absmiddle" /></a>')
	},
	fetch: function(el) {
		var infos = { domain: domain };
		$w('folder name').each(function(attr) {
			infos[attr] = el.getAttribute(attr);
		});
		if(infos.name.match(/\.swf/i)) {
			var type = 'flash';
			var spot = $(el).up();
			infos.width = spot.select('input')[0].value;
			infos.height = spot.select('input')[1].value;
			var html = this.template.flash;
		}
		else {
			var type = 'image';
			var html = this.template.image;
		}
		return {
			type: type,
			html: html.evaluate(infos)
		}
	},
	preview: function(el) {
		var frame = $('viewer');
		if(frame!=null) {
			frame.remove();
			if(this.timer!=null) clearTimeout(this.timer);
		}
		// draw
		var item = this.fetch(el);
		new Insertion[(item.type=='image')?'After':'Before'](el, '<span id="viewer">'+ item.html +'</span>');
		frame = $('viewer'); // set margin
		if(item.type=='flash') frame.style.marginLeft -= (frame.offsetWidth + 5);
	},
	preview_close: function() {
		this.timer = setTimeout(function() {
			try { $('viewer').remove() } // 프레임 전환 대비
			catch(e) { }
		}, 1000);
	},
	preview_button: function(item, infos) {
		var button = $(item).next();
		if(button!=null) button.remove();
		new Insertion.After(item, this.template.button.evaluate(infos));
	}
}