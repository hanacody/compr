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
		position.center(this.frame);
		$('confirm_number').focus();
	},
	close: function() {
		blind.remove();
		this.frame.select('form')[0].reset();
		this.frame.hide();
	}
}
