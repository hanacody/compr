/**
 * 달력
 */
var calendar = {
	initialize: function() {
		this.toDay = new Date();
		this.selYear = Prototype.Browser.IE ? this.toDay.getYear() : this.toDay.getYear()+1900;
		this.selMonth = this.toDay.getMonth();
		this.selDay = this.toDay.getDate();
		this.lastDays = new Array(31,28,31,30,31,30,31,31,30,31,30,31);
		this.calendarBody = 'calendarBody';
		this.selComponent = null;
		this.compYear = null;
		this.compMonth = null;
		this.compDay = null;
		this.drawStatus = false;
		this.minDate = null; // 하한선
		this.maxDate = null; // 상한선
	},
	// 달력 범위 설정
	get_calendar_range: function(mode) {
		var calendar_options = '';
		switch(mode) {
			case "year":
				this.toDay = new Date();
				var now_year = Prototype.Browser.IE ? this.toDay.getYear() : this.toDay.getYear()+1900;
				if(this.minDate==null) {
					var min_year = parseInt(now_year, 10)-1;
					this.minDate = new String(min_year)+'-01-01';
				}
				else {
					var date_infos = this.minDate.split('-');
					var min_year = date_infos[0];
				}
				if(this.maxDate==null) {
					var max_year = parseInt(now_year, 10)+1;
					this.maxDate = new String(max_year)+"-12-31";
				}
				else {
					var date_infos = this.maxDate.split('-');
					var max_year = date_infos[0];
				}
				for(var year=max_year; year>=min_year; year--) calendar_options += "<option value='"+year+"'>"+year+"년</option>";
				break;
			case "month":
				for(var month=1; month<=12; month++) {
					var num = new String(month);
					num = num.length==1 ? 0+num : num;
					calendar_options += "<option value='"+month+"'>"+num+"월</option>";
				}
				break;
		}
		return calendar_options;
	},
	// 달력 그리기
	draw_calendar: function(el, base) {
		this.selComponent = el;
		this.minDate = el.getAttribute('mindate');
		this.maxDate = el.getAttribute('maxdate');
		if(this.maxDate!=null && base==undefined && el.value=='') base = {'value':this.maxDate};

		if($('calendar_div')!=null) this.remove_calendar();
		this.drawStatus = false;

		var width = Prototype.Browser.IE ? 160 : 148;
		var new_div = "\
		<div id='calendar_div' style='position:absolute;z-index:200'>\
			<div style='width:"+width+"px;position:relative;border:#cdcdcd 1px solid;top:0px;left:0px;padding:4px;background-color:#f8f8f8;'>\
				<table cellpadding='0' cellspacing='0' border='0' style='margin-bottom:4px;'>\
					<tr>\
						<td style='padding-right:3px;'><select style='width:72px' onChange=\"calendar.change_date_II(this)\">"+this.get_calendar_range('year')+"</select></td>\
						<td style='padding-right:3px;'><select style='width:55px' onChange=\"calendar.change_date_II(this)\">"+this.get_calendar_range('month')+"</select></td>\
						<td align='right'><a onClick='calendar.remove_calendar()'><img src='"+ base_url +"/images/btn_close_s.gif' align='absmiddle'></a></td>\
					</tr>\
				</table>\
				<span style='display:none'><button onClick=\"calendar.change_date('-1 year')\" style=\"background:url('"+ base_url +"/images/btn_prev.gif');width:13px;height:16px;border:0;\"></button> <input type='text' size='3' readOnly style='letter-spacing:0px;background-color:#f8f8f8;'> <button onClick=\"calendar.change_date('1 year')\" style=\"background:url('"+ base_url +"/images/btn_next.gif');width:13px;height:16px;border:0;\"></button>\
				&nbsp;&nbsp;<button onClick=\"calendar.change_date('-1 month')\" style=\"background:url('"+ base_url +"/images/btn_prev.gif');width:13px;height:16px;border:0;\"></button> <input type='text' size='1' readOnly style='background-color:#f8f8f8;'> <button onClick=\"calendar.change_date('1 month')\" style=\"background:url('"+ base_url +"/images/btn_next.gif');width:13px;height:16px;border:0;\"></button></span>\
				<table cellpadding='0' cellspacing='1' bgcolor='#C9C9C9' border='0' style='margin-top:3px;' width='100%'>\
					<tr bgcolor='#f4f8fb' align='center' valign='bottom'>\
						<td height='16'><font color='red'>일</font></td>\
						<td>월</td>\
						<td>화</td>\
						<td>수</td>\
						<td>목</td>\
						<td>금</td>\
						<td><font color='#3366cc'>토</font></td>\
					</tr>\
					<tbody bgcolor='white' align='center' id='"+this.calendarBody+"'>\
					</tbody>\
				</table>\
			</div>\
		</div>";
		new Insertion.After(this.selComponent, new_div);
		(base==null||base==undefined) ? this.change_date(el.value) : this.change_date(base.value);
		$('calendar_div').setStyle({marginTop: '4px'});
	},
	change_date_II: function(el) {
		var info_selects = $('calendar_div').getElementsByTagName('select');
		this.change_date(info_selects[0].value+'-'+info_selects[1].value+'-'+this.selDay);
	},
	change_date: function(date) { // date { 1 year | -1 year | -1 month | 1 month }
		if(date=='') {
			this.toDay = new Date();
			this.selYear = Prototype.Browser.IE ? this.toDay.getYear() : this.toDay.getYear()+1900;
			this.selMonth = this.toDay.getMonth();
			this.selDay = this.toDay.getDate();
			this.compYear = this.compMonth = this.compDay = '';
		}
		else {
			switch(date) {
				case "-1 year": this.selYear -= 1; break;
				case "1 year": this.selYear += 1; break;
				case "-1 month":
					if(this.selMonth!=0) this.selMonth -= 1;
					else { // 0 : 1월
						this.selYear -= 1;
						this.selMonth = 11;
					}
					break;
				case "1 month":
					if(this.selMonth!=11) this.selMonth += 1;
					else { // 11 : 12월
						this.selYear += 1;
						this.selMonth = 0;
					}
					break;
				default: // 날짜값을 받은 경우 : 2008-09-12
					var date_infos = date.split('-');
					with(Math) {
						this.compYear = floor(date_infos[0]);
						this.compMonth = floor(date_infos[1])-1;
						this.compDay = floor(date_infos[2]);
					}
					if(this.drawStatus===true && this.compYear==this.selYear && this.compMonth==this.selMonth && this.compDay==this.selDay) return true;
					this.selYear = this.compYear;
					this.selMonth = this.compMonth;
					this.selDay = this.compDay;
			}
		}
		var calendar_body = $(this.calendarBody);
		this.toDay = new Date(this.selYear, this.selMonth, this.selDay);
		this.lastDays[1] = (this.selYear%4)==0 && ((this.selYear%100)!=0 || (this.selYear%400)==0) ? 29 : 28;
		var info_inputs = $('calendar_div').getElementsByTagName('input');
		var info_selects = $('calendar_div').getElementsByTagName('select');
		info_inputs[0].value = info_selects[0].value = this.selYear;
		info_inputs[1].value = info_selects[1].value = this.selMonth+1;

		calendar_body.update(); // 초기화

		var first_day_cell = 0;
		var first_cell = ((this.toDay.getDay()+7)-(this.selDay-1)%7)%7;
		for(var row=0, day=1; row<6; row++) {
			var new_row = calendar_body.insertRow(calendar_body.rows.length);
			for(var cell=0; cell<7; cell++) {
				if(cell == first_cell) first_day_cell += 1;
				var new_cell = new_row.insertCell(cell);
				switch(cell) {
					case 0: new_cell.style.color = '#ff0000'; break;
					case 6: new_cell.style.color = '#3366cc'; break;
				}
				if(first_day_cell>=1 && day<=this.lastDays[this.selMonth]) {
					new_cell.innerHTML = day;
					new_cell.style.cursor = 'pointer';
					if(day++==this.compDay && this.compMonth==this.selMonth && this.compYear==this.selYear) {
						new_cell.className = 'selectCell';
						new_cell.setAttribute('selected', 'true');
						this.selCell = new_cell;
					}
					else new_cell.className = 'normalCell';
					Event.observe(new_cell, 'mouseover', this.toggle_className);
					Event.observe(new_cell, 'mouseout', this.toggle_className);
					Event.observe(new_cell, 'click', this.apply_date.bind(this));
				}
				else {
					new_cell.innerHTML = '';
					new_cell.style.backgroundColor = '#F5F5F5';
				}
				new_cell.height = '20px';
				new_row.appendChild(new_cell);
			}
		}
		this.drawStatus = calendar_body.rows.length==6;
	},
	// 날짜 변경 - 단축키
	set_date: function(mode, dest, base) {
		if(base==undefined || base=='') {
			// 오늘을 기준으로 날짜 계산
			var toDay = new Date();
			var baseYear = Prototype.Browser.IE ? toDay.getYear() : toDay.getYear()+1900;
			var baseMonth = toDay.getMonth();
			var baseDay = toDay.getDate();
			var base_date = baseYear+"-"+baseMonth+"-"+baseDay;
		}
		else {
			var base_date = base.value;
		}
		var self = this;
		proc.parameters({mode: mode, base_date: basedate});
		proc.process(function(trans) {
			var dest_date = trans.responseXML.getElementsByTagName('resultData')[0].firstChild.nodeValue;
			if(['today','yesterday'].include(mode)) dest.value = base.value = dest_date;
			else dest.value = dest_date;
			if(self.selComponent===dest) self.change_date(dest.value);
		}, true, base_url +'/libs/scripts');
	},
	// 분류 설정시 아이템 오버/아웃시에 사용
	toggle_className: function(event) {
		this.className = event.type!='mouseover' ? this.getAttribute('selected')!=null ? 'selectCell' : 'normalCell' : this.getAttribute('selected')!=null ? 'shoverCell' : 'hoverCell';
	},
	// 달력박스 감추기
	hidden_calendar: function() {
		$('calendar_div').hide();
	},
	// 달력개체 제거
	remove_calendar: function() {
		$('calendar_div').remove();
	},
	// 선택한 날짜값 반환
	apply_date: function(event) {
		this.selMonth += 1;
		var el = Event.element(event);
		var year = this.selYear;
		var month = new String(this.selMonth);
		var day = el.innerHTML;
		if(month.length==1) month = '0'+month;
		if(day.length==1) day = '0'+day;
		var selDate = year+'-'+month+'-'+day;
		if(selDate<this.minDate || selDate>this.maxDate) {
			this.selMonth--; // fix
			return alerts('날짜는 '+ this.minDate +' 부터 '+ this.maxDate +' 까지 입력가능합니다.');
		}
		else {
			this.selComponent.value = selDate;
			this.hidden_calendar();
		}
	}
}
