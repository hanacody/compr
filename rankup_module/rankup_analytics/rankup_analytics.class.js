/**
 * 로그분석 클래스
 *@author: kurokisi
 *@authDate: 2011.10.11
 */
var rankup_analytics = {
	frame: null,
	initialize: function(frame) {
		this.frame = frame;
		this.load();
	},
	load: function() {
		var infos = {};
		var kinds = [];
		$A($(this.frame).select('div[class="chart"]')).each(function(item) {
			infos[item.id] = {
				id: item.id,
				width: item.getStyle('width').replace('px', ''),
				height: item.getStyle('height').replace('px', '')
			}
			$w('kind shape recent').each(function(attr) {
				var value = item.getAttribute(attr);
				if(attr=='kind') kinds.push(value);
				infos[item.id][attr] = value;
			});
		});
		$A(kinds).each(function(kind) {
			var info = infos[kind+'_chart'];
			if(info.recent=='yes') this.draw(info);
			else {
				var self = this;
				proc.parameters({ mode: 'load', shape: info.shape, kind: kind, sdate: $F('sdate'), edate: $F('edate') });
				proc.process(function(trans) { self.draw(info) }, false);
			}
		}, this);
	},
	draw: function(info) {
		with(info) {
			var data_file = encodeURIComponent('./data/'+ shape +'.php?kind='+ kind +'&sdate='+ $F('sdate') +'&edate='+ $F('edate'));
			var chart = '<scr'+'ipt type="text/javascript"> swfobject.embedSWF("../rankup_chart/open-flash-chart.swf", "'+ id +'", "'+ width +'", "'+ height +'", "9.0.0", "expressInstall.swf", {"data-file":"'+ data_file +'" } ); </scr'+'ipt>';
			$(id).update(chart);
		}
	}
}