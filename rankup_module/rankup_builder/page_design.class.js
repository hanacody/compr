/**
 * ������ ������ Ŭ����
 *@author: Kurokisi
 *@authDate: 2011.08.18
 *@note: category extend
 */

var page_design = (function(object) {

	object.handler = function() {
		var step = 0, step_texts = [];
		for(var i=1; i<=this.max_steps; i++) {
			if(!this.sel_objects[i]) break;
			step_texts.push(this.sel_objects[i].select('dt')[0].firstChild.nodeValue);
			step++;
		}
		if(step==0) return; // terminate!
		$('step_path_text').update(step_texts.join(' > ')); // page title hint
		step_texts.pop(); // last value discard

		var parent_path_text = step_texts.join(' > '); // page top image hint
		if(!parent_path_text) $('parent_path_hint').hide();
		else {
			$('parent_path_hint').show();
			$('parent_path_text').update(parent_path_text);
		}
		var no = this.sel_objects[step].getAttribute('no');
		$('no').value = no;
		var item = this.items[no];
		$w('page_top_img page_title_type page_title_img page_top_content page_bottom_content has_child').each(function(node) {
			var value = item.getElementsByTagName(node)[0].firstChild.nodeValue;
			switch(node) {
				case 'page_top_img': // ������ ��� �̹���
					var el = $('image_box_frame').select('dt')[0].select('div[name="'+ value +'"]')[0];
					image_box.set(el), image_box.apply();
					break;

				case 'page_title_type': // ������ ���� ����
					change_frame($('page_title_type_'+ value));
					break;

				case 'page_title_img': // ������ ���� �̹���
					$('on_page_title_img').value = ''; // �ʱ�ȭ
					var folder = 'design/page/';
					var html = value ? '<img src="'+ domain + folder + value +'" align="absmiddle" />' : '';
					$('page_title_preview').update(html);
					break;

				case 'page_top_content': // ������ ��� ������
				case 'page_bottom_content': // ������ �ϴ� ������
					var spot = $(node).up();
					var container = spot.select('iframe')[0];
					if(!container) $(node).value = value; // �������� ���õǱ� ���̸� textarea �� ���� ���� - FF error fixed
					else {
						try { // IE error - runtime error mute!
							container.contentWindow.document.body.innerHTML = value;
						}
						catch(e) { }
					}
					break;
			}
		});
	}

	return object;
})(category);
