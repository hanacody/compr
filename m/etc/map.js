/**
 * 네이버 지도2.0 클래스 for Mobile
 *@author: kurokisi
 *@authDate: 2011.11.02
 */
var nmap = {
	frame: null, // object
	mapKey: null, // string
	encoding: 'euc-kr', // { utf-8 | euc-kr }
	coord: 'latlng', // { tm128 | latlng }
	oMap: null,
	oMarker: null,
	oIcon: null,
	initialize: function(frame, map_key, nonclick) {
		this.frame = $(frame);
		this.mapKey = map_key;
		var points = [$F('mapx'), $F('mapy')];
		this.configs = {
			point : this.get_point(points[0]?points[0]:126.9773356, points[1]?points[1]:37.5675451), // seoul city
			zoom : 11,
			enableWheelZoom : true,
			enableDragPan : true,
			enableDblClickZoom : false,
			mapMode : 0,
			activateTrafficMap : false,
			activateBicycleMap : false,
			minMaxLevel : [1, 14],
			size : new nhn.api.map.Size(this.frame.getDimensions().width-8, this.frame.getDimensions().height-8)
		}
		this.map_init(nonclick);
	},
	map_init: function(nonclick) {
		// marking icon setting
		var icon = this.frame.select('img')[0];
		if(icon) {
			var dimensions = icon.getDimensions();
			with(dimensions) {
				var oSize = new nhn.api.map.Size(width, height);
				var oOffset = new nhn.api.map.Size(width/2, height);
			}
			this.oIcon = new nhn.api.map.Icon(icon.src, oSize, oOffset);
		}
		this.frame.update('');

		// map initialize
		this.oMap = new nhn.api.map.Map(this.frame, this.configs);

		// zoom
		var oSlider = new nhn.api.map.ZoomControl();
		this.oMap.addControl(oSlider);
		oSlider.setPosition({ top : 10, left : 10 });

		// controller
		var oMapTypeBtn = new nhn.api.map.MapTypeBtn();
		this.oMap.addControl(oMapTypeBtn);
		oMapTypeBtn.setPosition({ top: 10, right : 10 });

		if(nonclick!=true) {
			// click event append
			this.oMap.attach('click', this.handler.bind(this));
		}
		// set marker
		var points = [$F('mapx'), $F('mapy')];
		if(points[0] && points[1]) this.set(this.get_point(points[0], points[1]), true);
	},
	handler: function(oCustomEvent) {
		var oTarget = oCustomEvent.target;
		if(oTarget instanceof nhn.api.map.Marker) return;
		this.set(oCustomEvent.point);
	},
	set: function(oPoint, centering) {
		if(this.oMarker!=null && this.oMarker.getVisible()) this.oMap.removeOverlay(this.oMarker);
		this.oMarker = new nhn.api.map.Marker(this.oIcon);
		this.oMarker.setPoint(oPoint);
		this.oMap.addOverlay(this.oMarker);
		if(centering==true) this.oMap.setCenter(oPoint);
		// keep
		$w('x y').each(function(key) {
			var item = $('map'+ key);
			if(item) item.value = oPoint[key];
		});
	},
	get_point: function(x, y) {
		return new nhn.api.map.LatLng(y, x);
	}
}
