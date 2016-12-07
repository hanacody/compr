/**
 * 제품관리
 *@author: kurokisi
 *@authDate: 2012.01.19
 *@note: rankup_frame.js 에서 동적로딩
 */
var product = {
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
		category: {
			title: '제품 분류 선택',
			width: '300px',
			height: '167px',
			content: '\
					<table cellpadding="3" cellspacing="0" border="0" align="center">\
					<tr>\
						<td width="60"><img src="../../Libs/_images/ic_dot1.gif" /> 1차분류</td>\
						<td>\
							<select id="ocate1" name="ocate1" style="width:190px" onChange="product.load_cates(this.value, 2)">\
							<option value="">-1차분류선택(필수)-</option>\
							</select>\
						</td>\
					</tr>\
					<tr>\
						<td><img src="../../Libs/_images/ic_dot1.gif" /> 2차분류</td>\
						<td>\
							<select id="ocate2" name="ocate2" style="width:190px">\
							<option value="">-2차분류선택(옵션)-</option>\
							</select>\
						</td>\
					</tr>\
					</table>',
			button: '<a onClick="product.apply_cates()"><img src="../../Libs/_images/btn_apply.gif" align="absmiddle" /></a>'
		},
		product: {
			title: '제품 선택',
			width: '300px',
			height: '383px',
			content: '\
					<table width="260" cellpadding="3" cellspacing="0" border="0" align="center" style="table-layout:fixed">\
					<tr>\
						<td width="60"><img src="../../Libs/_images/ic_dot1.gif" /> 1차분류</td>\
						<td>\
							<select id="ocate1" name="ocate1" style="width:190px" onChange="product.load_cates(this.value, 2);product.load_products()">\
							<option value="">-1차분류선택-</option>\
							</select>\
						</td>\
					</tr>\
					<tr>\
						<td><img src="../../Libs/_images/ic_dot1.gif" /> 2차분류</td>\
						<td>\
							<select id="ocate2" name="ocate2" style="width:190px" onChange="product.load_products()">\
							<option value="">-2차분류선택-</option>\
							</select>\
						</td>\
					</tr>\
					<tr>\
						<td colspan="2">\
							<input type="hidden" id="oproduct" name="oproduct" value="" />\
							<ul id="oproduct_list"></ul>\
						</td>\
					</tr>\
					</table>',
			button: '<a onClick="product.apply_products()"><img src="../../Libs/_images/btn_apply.gif" align="absmiddle" /></a>',
			item: new Template('<li onClick="product.sel_products(this, #{no})">#{title}</li>')
		}
	},
	fetch_value: function(values) {
		var value = $H(values).inspect();
		return value.replace(/\'/g, '"').substring(7, value.length-1);
	},
	open_cates: function(el) {
		this.item = $(el);
		var frame = $(this.frame);
		if(frame==null) new Insertion.After(this.item, this.dialog.frame.evaluate(this.dialog.category));
		this.defaults = this.item.value ? eval('('+ this.item.value +')') : {cate1:'', cate2:''};
		this.load_cates('', 1, true);
		if(frame!=null) frame.show();
	},
	apply_cates: function() {
		if($F('ocate1').blank()) {
			alert('1차분류를 선택하여 주십시오.');
			return false;
		}
		this.item.value = this.fetch_value({cate1:$F('ocate1'), cate2:$F('ocate2')});
		$(this.frame).hide();
	},
	load_cates: function(no, step, init) {
		var self = this;
		var spot = $('ocate'+step);
		spot.options.length = 1;
		if(step==1 || no) {
			proc.parameters({mode:'load_category', kind:'product', step:step, no:no});
			proc.process(function(trans) {
				var items = trans.responseXML.getElementsByTagName('item');
				$A(items).each(function(item, index) {
					var infos = {};
					$w('no name used').each(function(field) {
						if(item.getElementsByTagName(field)[0]!=null) {
							var value = item.getElementsByTagName(field)[0].firstChild.nodeValue;
							if(field.match(/no/i)) self.cates[value] = item; // keep
							infos[field] = value;
						}
					});
					// draw
					var row = spot.options.length;
					spot.options[row] = new Option(infos.name, infos.no);
					if(init===true && infos.no==self.defaults['cate'+step]) spot.options[row].selected = true;
				});
				if(step==1) self.load_cates(self.defaults['cate'+step], 2, init);
			}, false, domain +'rankup_module/rankup_category');
		}
	},
	open_products: function(el) {
		this.item = $(el);
		var frame = $(this.frame);
		if(frame==null) new Insertion.After(this.item, this.dialog.frame.evaluate(this.dialog.product));
		this.defaults = this.item.value ? eval('('+ this.item.value +')') : {cate1:'', cate2:'', no:''};
		if(this.item.value) this.load_products(true);
		else $('oproduct_list').update();
		this.load_cates('', 1, true);
		if(frame!=null) frame.show();
	},
	load_products: function(init) {
		var self = this, cate1 = $F('ocate1'), cate2 = $F('ocate2');
		if(init) cate1 = this.defaults.cate1, cate2 = this.defaults.cate2;
		proc.parameters({mode:'load_products', cate1:cate1, cate2:cate2});
		proc.process(function(trans) {
			var spot = $('oproduct_list'); spot.update('');
			var values = trans.responseText.evalJSON();
			$A(values).each(function(value) {
				var html = self.dialog.product.item.evaluate(value);
				var items = spot.select('li');
				items.length ? new Insertion.After(items.last(), html) : spot.update(html);
				if(init && self.defaults.no==value.no) {
					self.sel_products(spot.select('li').last(), value.no);
				}
			});
		}, false, domain +'product/option');
	},
	sel_products: function(el, no) {
		el = $(el); // fix
		$('oproduct').value = no;
		if(this.sel_product!=null) {
			this.sel_product.removeClassName('hover');
		}
		el.addClassName('hover');
		this.sel_product = el;
	},
	apply_products: function() {
		if($F('oproduct').blank()) {
			alert('제품을 선택하여 주십시오.');
			return false;
		}
		this.item.value = $('oproduct').value;
		this.item.value = this.fetch_value({cate1: $F('ocate1'), cate2: $F('ocate2'), no: $F('oproduct')});
		$(this.frame).hide();
	}
}