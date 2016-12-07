/**
 * ATTACHMENT v1.0
 *@author: kurokisi
 *@authDate: 2011.08.19
 */

var attachment = {
	version: 'v1.0 r110819',
	face: null,
	spot: null,
	html: null,
	post: function(el, face, action) {
		var form = el.form;
		var keepAction = form.action;
		var encType = form.encoding;
		this.face = face || 'attach';
		var configs = $(form).select('input[face="'+ this.face +'"]');
		$A(configs).each(function(config) { config.disabled = false });
		var files = $(form).select('input[type="file"]');
		$A(files).each(function(file) { if(file!==el) { file.disabled = true } });

		form.action = (action==undefined) ? './proc.ajax.php' : action;
		form.encoding = 'multipart/form-data';
		form.target = 'post_frame';
		form.method = 'POST';
		form.submit();

		// reset
		$w('method target').each(function(attr) { form.removeAttribute(attr) });
		form.action = keepAction;
		form.encoding = encType; // application/x-www-form-urlencoded
		$A(configs).each(function(config) { config.disabled = true });
		$A(files).each(function(file) { if(file!==el) { file.disabled = false } });
		this.spot = $(el).up(); // keep
		this.html = this.spot.innerHTML; // keep
		this.spot.innerHTML = '<span style="letter-spacing:-1px;color:#ff3300;margin-top:4px;position:absolute;">파일을 업로드하는 중입니다.</span><input type="file" style="visibility:hidden" disabled>';
	},
	reset: function() {
		this.spot.innerHTML = this.html;
	}
}
