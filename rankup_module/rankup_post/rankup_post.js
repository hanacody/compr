/**
 * �����ȣ �˻�
 */
var rankup_post = {
	handler: null,
	// Ŭ���� �ʱ⼳��
	initialize: function() {
		this.selDong = null;
		this.selDongmyun = null;
		this.selPost = null;
		this.selZipcode = null;
		this.selAddress = null;
		this.postList = $('postList');
		this.postList.update('�˻��Ͻ� ���̸��� �Է��Ͽ� �ֽʽÿ�.');
	},
	// �ּҰ˻�
	search_post: function(dong) {
		var self = this;
		this.selZipcode = null; // �����ȣ ����
		this.postList.update('<b style="color:#FF6600">\''+dong+'\'</b> ���� �����ȣ�� ��ȸ�ϰ� �ֽ��ϴ�.');
		var url = domain +'rankup_module/rankup_post/multiProcess.ajax.html?mode=search_post&off_map&dongmyun='+ encodeURI(dong);
		new Ajax.Request(url, {
			method: 'get',
			onSuccess: function(trans) {
				self.postList.update();
				var new_header = document.createElement("div");
				new_header.innerHTML = "<b style='color:#FF6600'>'"+dong+"'</b> ���� �����ȣ ��ȸ����Դϴ�.";
				self.postList.appendChild(new_header);
				var items = trans.responseXML.getElementsByTagName('item');
				for(var i=0; i<items.length; i++) {
					var item = items[i];
					var zipcode = item.getElementsByTagName('zipcode')[0].firstChild.nodeValue;
					var sido = item.getElementsByTagName('sido')[0].firstChild.nodeValue;
					var sigugun = item.getElementsByTagName('sigugun')[0].firstChild.nodeValue;
					var dongmyun = item.getElementsByTagName('dongmyun')[0].firstChild.nodeValue;
					var bunji = item.getElementsByTagName('bunji')[0].firstChild;
					bunji = bunji!==null ? bunji.nodeValue : '';
					var address = sido+' '+sigugun+' '+dongmyun;
					var new_div = document.createElement('div');
					new_div.style.margin = '4px 0px 4px 0px';
					new_div.innerHTML = "<a onClick=\"rankup_post.select_post(this, '"+zipcode+"', '"+address+"')\">["+zipcode+"]<span style='margin-left:6px'>"+sido+" "+sigugun+" "+dongmyun+" "+bunji+"</span></a></td></tr>";
					self.postList.appendChild(new_div);
				}
			}
		});
	},
	// �����ȣ ����
	select_post: function(el, zipcode, address) {
		if(this.selPost!=null) this.selPost.className = '';
		this.selPost = el;
		this.selPost.className = 'selPost';
		this.selZipcode = zipcode;
		this.selAddress = address;
		this.apply_post(); // 2010.11.16 added
	},
	// �����ȣ �ݿ�
	apply_post: function() {
		if(this.selZipcode==null) {
			alert('�����ȣ�� �����Ͽ� �ֽʽÿ�.');
			$('dong').focus();
			return false;
		}
		if(this.zone) { // 2012.04.10 fixed
			this.zone.select('input[id="zipcode"]')[0].value = this.selZipcode;
			this.zone.select('input[id="addrs1"]')[0].value = this.selAddress;
			this.zone.select('input[id="addrs2"]')[0].focus();
		}
		else {
			$('zipcode').value = this.selZipcode;
			$('addrs1').value = this.selAddress;
			$('addrs2').focus();
		}
		// �ڵ鷯
		if(this.handler!=null) eval(this.handler);
		this.close_post();
	},
	// �ּҰ˻�â ����
	open_post: function(obj, spot, handler, zone) {
		blind.draw();
		this.frame = $(obj);
		this.frame.show();
		this.handler = handler || null;
		this.zone = $(zone) || null; // 2012.04.10 added

		if(spot==undefined) spot = 'post_spot';
		var offset = $(spot).positionedOffset();
		this.frame.setStyle({
			left: offset.left + 'px',
			top: offset.top + 'px'
		});

		this.initialize();
		setTimeout(function() {
			$('dong').value = '';
			$('dong').focus()
		}, 0);
		$esc.add('rankup_post.close_post()');
	},
	// �ּҰ˻�â �ݱ�
	close_post: function() {
		this.frame.hide();
		blind.remove();
		$esc.remove('rankup_post.close_post()');
	}
}