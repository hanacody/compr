/**
 * 랭크업 모바일 프레임 클래스 v1.0
 *@author: kurokisi
 *@authDate: 2011.10.18
 */
var rankup_frame = {
	version: '1.0 r110809',
	frame: null,
	spot: null,
	options: {},
	lowest_level: { 7: '비회원' },
	initialize: function(frame, spot) {
		this.frame = $(frame); // object
		this.spot = $(spot); // object
	},
	direction: {
		spoit: function(obj) {
			return {
				spot: obj,
				code: 't'+obj.getAttribute('no'),
				level: parseInt(obj.id.replace(/d/g,''))
			}
		},
		get: function(el) { // object 리턴
			while(!el.nodeName.match(/tr/i)) el = $(el).up();
			var no = el.getAttribute('no');
			var spot = el.up();
			var code = 't'+ no;
			var level = parseInt(el.id.replace(/d/g, ''));
			var next = el.next();
			var prev = el.previous();
			if(next!=null) {
				var _next = this.spoit(next);
				if(level>_next.level) next = null;
				else {
					if(level!=_next.level) {
						while(1) {
							next = next.next();
							if(next==null) break;
							_next = this.spoit(next);
							if(level>_next.level) { next = null; break; }
							if(level==_next.level) break;
						}
					}
					if(level==_next.level) {
						var branches = $(spot).select('tr[class~="'+ _next.code +'"]');
						if(branches.length>1) {
							next = $A(branches).last();
							if(next!=null) _next = this.spoit(next);
						}
					}
				}
			}
			if(prev!=null) {
				var _prev = this.spoit(prev);
				if(level>_prev.level) prev = null;
				else if(level!=_prev.level) {
					while(1) {
						prev = prev.previous();
						if(prev==null) break;
						_prev = this.spoit(prev);
						if(level>_prev.level) { prev = null; break; }
						if(level==_prev.level) break;
					}
				}
			}
			return {
				no: no, // string
				spot: spot, // element
				code: code, // string
				level: level, // int
				next: (next==undefined) ? {spot: null, code: null, level: null} : _next, // hash
				prev: (prev==undefined) ? {spot: null, code: null, level: null} : _prev // hash
			}
		},
		up: function(el) {
			var obj = this.get(el);
			if(obj.prev.level==null) {
				alert('이동할 수 없습니다.');
				return false;
			}
			// moving
			var branches = $(obj.spot).select('tr[class~="'+ obj.code +'"]');
			$A(branches).each(function(branch) {
				new Insertion.Before(obj.prev.spot, '<tr no="'+ branch.getAttribute('no') +'" class="'+ branch.className +'" id="'+ branch.id +'">'+ branch.innerHTML +'</tr>');
				branch.remove();
			});
			// saving
			proc.parameters({mode: 'set_direction', kind: 'up', no: obj.no});
			proc.process();
		},
		down: function(el) {
			var obj = this.get(el);
			if(obj.next.level==null) {
				alert('이동할 수 없습니다.');
				return false;
			}
			// moving
			var branches = $(obj.spot).select('tr[class~="'+ obj.code +'"]');
			$A(branches).reverse().each(function(branch) {
				new Insertion.After(obj.next.spot, '<tr no="'+ branch.getAttribute('no') +'" class="'+ branch.className +'" id="'+ branch.id +'">'+ branch.innerHTML +'</tr>');
				branch.remove();
			});
			// saving
			proc.parameters({mode: 'set_direction', kind: 'down', no: obj.no});
			proc.process();
		}
	},
	del: function(el) {
		var self = this;
		if(!confirm('선택하신 메뉴를 삭제하시겠습니까?\n\n[알림] 하위 메뉴도 함께 삭제되며 복구되지 않으니 주의하십시오.')) return false;
		while(!el.nodeName.match(/tr/i)) el = $(el).up();
		var no = el.getAttribute('no');
		proc.parameters({mode: 'del_frame', no: no});
		proc.process(function(trans) {
			if(!trans.responseText.blank()) proc.response(trans);
			else {
				alert('삭제되었습니다.');
				$A(el.up().select('tr[class~="t'+ no +'"]')).each(function(branch) { branch.remove() });
			}
		}, false);
	},
	open: function(el, kind) { // 등록/수정폼 로드
		if(kind==undefined) kind = 'new';
		var no = '', depth = '', self = this;
		if(el!=undefined) {
			while(!el.nodeName.match(/tr/i)) el = $(el).up();
			no = el.getAttribute('no');
			depth = el.getAttribute('id');
			depth = depth ? depth.replace(/d/g, '') : '';
		}
		$w('gnb_frame').each(function(frame) {
			var state = ((kind=='new' && depth>=1) || (kind=='edit' && depth>1));
			$(frame)[state?'hide':'show']();
			$A($(frame).select('input')).each(function(item) { item.disabled = state });
		});
		$w('no kind depth').each(function(field) {
			this.frame.select('input[name="'+ field +'"]')[0].value = eval(field);
		}, this);

		this.set_link(); //

		// reset
		proc.parameters({mode: 'load', kind: kind, no: no});
		proc.process(function(trans) {
			var infos = {};
			var component = (kind=='edit') ? trans.responseXML.getElementsByTagName('component')[0].firstChild.nodeValue : '';
			$w('base_name target access_level page_type module options page_body_content link url used').each(function(field) {
				var value = (kind=='edit') ? trans.responseXML.getElementsByTagName(field)[0].firstChild.nodeValue : '';
				if(field=='options') self.options.value = value.replace(/\"/g, '&quot;'); // 2012.05.11 fixed
				else {
					infos[field] = value;
					var items = self.frame.select('*[name="'+ field +'"]');
					switch(items[0].nodeName.toLowerCase()) {
						case 'input':
							if(!items[0].type.match(/checkbox|radio/i)) items[0].value = infos[field];
							else {
								var values = infos[field].split(','); // string  or  array
								$A(items).each(function(item) {
									if(values.include(item.value)) {
										item.checked = true;
										// radio item
										if(field=='page_type') change_frame(item);
									}
								});
							}
							break;
						case 'select':
							switch(items[0].name) {
								case 'access_level':
									if(!infos[field]) infos[field] = $H(self.lowest_level).keys(); // 7 : lowest level
									items[0].value = infos[field];
									break;
								case 'module':
									items[0].value = infos[field];
									// 컴포넌트 로드
									self.load_components(infos[field], 'component', component);
									break;
								case 'link':
									items[0].value = infos[field];
									self.check_url($('link'));
									break;
							}
							break;
						case 'textarea':
							var wrap = $(items[0]).up();
							wrap.select('textarea')[0].value = infos[field]; // 2012.05.29 added
							wrap.select('iframe')[0].contentWindow.document.body.innerHTML = infos[field];
							break;
					}
				}
			});
			blind.draw();
			self.frame.show();
			position.center(self.frame);
			// 첫 입력항목에 포커스
			var obj = self.frame.select('input[name="base_name"]')[0];
			obj.focus(), obj.value = obj.value;
		}, false);
	},
	close: function() {
		change_frame($('page_type_ready')); // crazy IE!!
		this.frame.hide();
		blind.remove();
	},
	template: {
		entry: new Template('\
		<tr no="#{no}" class="#{parents}t#{no}" id="d#{depth}">\
			<td><a onClick="rankup_frame.direction.up(this)"><img src="../../Libs/_images/btn_order_up.gif" align="absmiddle" hspace="1"></a><a onClick="rankup_frame.direction.down(this)"><img src="../../Libs/_images/btn_order_down.gif" align="absmiddle" hspace="1"></a></td>\
			<td><span>[#{depth}차]</span></td>\
			<td align="left"><p>#{base_name}</p></td>\
			<td>#{used_text}</td>\
			<td>[#{page_type_text}]</td>\
			<td>#{on_access_level}</td>\
			<td align="left">\
				<a onClick="rankup_frame.open(this, \'edit\')"><img src="../../rankup_module/rankup_builder/img/btn_edit_s.jpg" align="absmiddle" alt="수정" /></a><a onClick="rankup_frame.del(this)"><img src="../../rankup_module/rankup_builder/img/btn_del_s.jpg" align="absmiddle" hspace="1" alt="삭제" /></a><a onClick="menu_handler(#{no})"><img src="../../rankup_module/rankup_builder/img/btn_preview_s.jpg" align="absmiddle" hspace="5" alt="미리보기" /></a>#{on_button}\
			</td>\
		</tr>'),
		on_button: '<a onClick="rankup_frame.open(this)"><img src="../../rankup_module/rankup_builder/img/btn_regist_s.jpg" align="absmiddle" alt="하위메뉴등록" /></a>'
	},
	save: function() {
		if(typeof wysiwyg_Class == 'function') Wysiwyg.submit_start();
		if(!validate(Form.getElements(this.frame))) return false;
		if(!confirm('설정하신 내용을 저장하시겠습니까?')) return false;
		var self = this;
		proc.parameters({mode: 'save_frame'}, this.frame);
		proc.process(function(trans) {
			var infos = {};
			var tmpl = self.template.entry;
			$w('no depth base_name parents used').each(function(field) {
				infos[field] = trans.responseXML.getElementsByTagName(field)[0].firstChild.nodeValue;
			});
			$A(self.frame.select('input[name="page_type"]')).each(function(item) { // 페이지 형태
				if(item.checked) {
					infos['page_type_text'] = item.next().innerHTML.replace(/[a-zA-Z]/g, '');
					throw $break;
				}
			});
			infos['used_text'] = (infos['used']=='yes') ? '사용함' : '<font color="#ff6600">사용안함</font>';
			var item = self.frame.select('select[name="access_level"]')[0];
			var level_text = $A(item.options[item.options.selectedIndex].text.split(/l/g)).shift(); // 접근권한
			infos['on_access_level'] = (item.value==$H(self.lowest_level).keys()) ? $H(self.lowest_level).values() : level_text; // lowest level
			infos['on_button'] = (infos.depth>1) ? '' : self.template.on_button; // 등록버튼

			alert('저장되었습니다.');

			// draw
			var spot = self.spot.select('tr[no="'+infos.no+'"]')[0];
			if(spot) { // edit
				new Insertion.After(spot, tmpl.evaluate(infos));
				spot.remove();
			}
			else { // new
				// 처리할게 많아 reload!
				location.reload();
			}
			self.close();
		}, false);
	},
	set_link: function() {
		var link = $('link');
		link.options.length = 1;
		var bgColors = {1: '#e6f1fb', 2: '#fff'}; // 배경색
		$A(this.spot.select('tr')).each(function(item) {
			var no = item.getAttribute('no');
			var depth = item.select('span')[0].innerHTML;
			var name = item.select('p')[0].innerHTML;
			link.options[link.options.length] = new Option(depth +' '+ name, no);
			$($A(link.options).last()).setStyle({backgroundColor: bgColors[depth.replace(/[^0-9]/g, '')], color: 'black'});
		});
	},
	check_url: function(el) {
		$(el).next()[el.value.blank()?'show':'hide']();
	},
	load_components: function(module, dest, value) { // 컴포넌트 로드
		$('option_html').update(); // 옵션 제거
		dest = $(dest), dest.options.length = 1;
		if(!module) return;

		var self = this;
		proc.parameters({mode: 'load_components', module: module});
		proc.process(function(trans) {
			var items = trans.responseXML.getElementsByTagName('item');
			$A(items).each(function(item) {
				var infos = {}
				$w('key name init html js').each(function(field) {
					infos[field] = item.getElementsByTagName(field)[0].firstChild.nodeValue;
				});
				self.options[infos.key] = {html: new Template(infos.html), js: infos.js} // 컴포넌트 옵션 저장
				dest.options[dest.options.length] = new Option(infos.name, infos.key);
				if(value!=undefined) {
					if(infos.key==value) {
						dest.options[dest.options.length-1].selected = true;
						self.load_options(infos.key);
					}
				}
			}, this);
		}, false);
	},
	load_options : function(component) { // 컴포넌트 옵션 로드
		var self = this;
		var ohtml = $('option_html'), ojs = $('option_js');
		ohtml.update();
		if(component && this.options[component]) {
			with(this.options[component]) {
				ohtml.update(html.evaluate({on_option:self.options.value})); // 옵션추가
				if(js && ojs.src != domain + js) ojs.src = domain + js; // 핸들러 로드
			}
		}
	}
}