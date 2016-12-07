/**
 * 글작성자 인증
 */
var vconfirm = {
	frame: null,
	initialize: function(frame) {
		this.frame = $(frame);
	},
	open: function(el, no) {
		this.frame.show();
		blind.draw();
		this.frame.select('input[name="no"]')[0].value = no;
		var offset = $(el).positionedOffset();
		this.frame.setStyle({
			left: offset.left + el.getWidth() + 5 + 'px',
			top: offset.top + 'px'
		});
		$('confirm_number').focus();
		$esc.add('vconfirm.close()');
	},
	close: function() {
		blind.remove();
		this.frame.select('form')[0].reset();
		this.frame.hide();
		$esc.remove('vconfirm.close()');
	}
}
