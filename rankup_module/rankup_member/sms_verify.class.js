/**
 * 휴대폰인증
 *@author: kurokisi
 *@authDate: 2012.03.08
 */
var sms_verify = {
	on: false,
	frame: null,
	fields: [],
	verified: false,
	initialize: function(frame, fields) { // fields : array
		this.frame = $(frame);
		this.form = $(frame).select('form')[0];
		this.fields = fields || $w('hphone1 hphone2 hphone3');
		this.fields.each(function(field) { $(field).readOnly = true }); // readonly
	},
	open: function(spot) {
		if(this.verified==true) {
			alert('이미 휴대폰인증을 하셨습니다.');
			return false;
		}
		blind.draw();
		this.on = true;
		this.frame.show();
		this.form.reset();
		this.form.select('input')[0].focus();
		var offset = $(spot).positionedOffset();
		this.frame.setStyle({
			left: offset.left +'px',
			top: offset.top +'px'
		});
		$esc.add('sms_verify.close()');
	},
	close: function() {
		this.on = false;
		this.end_time = null;
		$('limit_times').update(this.spot_html);
		this.frame.hide();
		blind.remove();
		$esc.remove('sms_verify.close()');
	},
	send: function() {
		if(!validate(this.form)) return false;
		if(!confirm('휴대전화로 인증번호를 받으시겠습니까?')) return false;
		this.end_time = null;
		var self = this;
		proc.parameters({mode:'send_vnumber'}, this.form.name);
		proc.process(function(trans) {
			if(!trans.responseText.blank()) proc.response(trans);
			else {
				alert('인증번호가 전송되었습니다.');
				self.countdown();
			}
		}, false);
	},
	check: function() {
		if(!validate(this.form)) return false;
		var vnumber = $F('verify_number');
		if(vnumber.blank()) {
			alert('인증번호를 입력하여 주십시오.');
			return false;
		}
		var self = this;
		proc.parameters({mode:'check_vnumber', vnumber:vnumber});
		proc.process(function(trans) {
			if(!trans.responseText.blank()) proc.response(trans);
			else {
				alert('인증되었습니다.');
				self.form.select('input').each(function(item, index) {
					if(index>2) throw $break;
					var field = $(self.fields[index]);
					field.value = item.value;
				});
				self.verified = true;
				self.close();
			}
		}, false);
	},
	countdown: function(time) {
		var spot = $('limit_times');
		if(this.on==false) {
			spot.innerHTML = this.spot_html;
			return false;
		}
		var now = new Date();
		if(this.end_time==null) {
			this.spot_html = spot.innerHTML;
			this.end_time = (now.getTime() / 1000).floor() + (time||180);
		}
		var timer = this.end_time - (now.getTime() / 1000).floor();
		var minute = (timer / 60).floor(), second = (timer % 60).floor();
		spot.innerHTML = minute.toPaddedString(2) +':'+ second.toPaddedString(2);

		if(timer>0) {
			var self = this;
			setTimeout(function() { self.countdown() }, 1000);
		}
		else {
			alert('입력시간이 지났습니다.');
			spot.innerHTML = this.spot_html;
			this.close();
		}
	}
}