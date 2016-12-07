/**
 * 등록폼 관리
 *@author: kurokisi
 *@authDate: 2012.03.14
 *@note: rankup_frame.js 에서 동적로딩
 */
var fbuilder = {
	frame: 'option_frame',
	max_steps: 2,
	cates: {}, // keep categories
	dialog: {
		frame: new Template('\
			<div id="option_frame" style="position:absolute;width:#{width};height:#{height};border:1px #000 solid;background-color:#fff;overflow:hidden">\
				<div class="title" style="background-color:#336699">\
					<div style="padding:10px;font-weight:bold;color:#fff;letter-spacing:-1px">≡ #{title} ≡</div>\
					<div style="position:absolute;width:5px;right:10px;margin-top:-24px"><a onClick="$(\'option_frame\').hide()" style="color:#fff">×</a></div>\
				</div>\
				<div class="content" style="margin:20px 0;text-align:center">\
					#{content}\
				</div>\
				<div style="text-align:center;border-top:1px #000 solid;background-color:#f7f7f7;padding:6px">\
					#{button}\
					<a onClick="$(\'option_frame\').hide()"><img src="../../Libs/_images/btn_close.gif" align="absmiddle" /></a>\
				</div>\
			</div>'
		),
		form: {
			title: '등록폼 선택',
			width: '300px',
			height: '320px',
			content: '\
					<table width="260" cellpadding="3" cellspacing="0" border="0" align="center" style="table-layout:fixed">\
					<tr>\
						<td >\
							<input type="hidden" id="oform" name="oform" value="" />\
							<ul id="oform_list"></ul>\
						</td>\
					</tr>\
					</table>',
			button: '<a onClick="fbuilder.apply_forms()"><img src="../../Libs/_images/btn_apply.gif" align="absmiddle" /></a>',
			item: new Template('<li onClick="fbuilder.sel_forms(this, #{fno})">#{form_name}</li>')
		}
	},
	fetch_value: function(values) {
		var value = $H(values).inspect();
		return value.replace(/\'/g, '"').substring(7, value.length-1);
	},
	open_forms: function(el) {
		this.item = $(el);
		var frame = $(this.frame);
		if(frame==null) new Insertion.After(this.item, this.dialog.frame.evaluate(this.dialog.form));
		this.defaults = this.item.value ? eval('('+ this.item.value +')') : {fno:''};
		this.load_forms();
		if(frame!=null) frame.show();
	},
	load_forms: function() {
		var self = this;
		proc.parameters({mode:'load_forms'});
		proc.process(function(trans) {
			var spot = $('oform_list'); spot.update('');
			var values = trans.responseText.evalJSON();
			$A(values).each(function(value) {
				var html = self.dialog.form.item.evaluate(value);
				var items = spot.select('li');
				items.length ? new Insertion.After(items.last(), html) : spot.update(html);
				if(self.defaults.fno==value.fno) {
					self.sel_forms(spot.select('li').last(), value.fno);
				}
			});
		}, false, domain +'board/option');
	},
	sel_forms: function(el, fno) {
		el = $(el); // fix
		$('oform').value = fno;
		if(this.sel_fbuilder!=null) {
			this.sel_fbuilder.removeClassName('hover');
		}
		el.addClassName('hover');
		this.sel_fbuilder = el;
	},
	apply_forms: function() {
		if($F('oform').blank()) {
			alert('등록폼를 선택하여 주십시오.');
			return false;
		}
		this.item.value = $('oform').value;
		this.item.value = this.fetch_value({fno: $F('oform')});
		$(this.frame).hide();
	}
}