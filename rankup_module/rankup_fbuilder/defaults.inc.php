<?php
/**
 * 등록폼 기본설정
 *@note: 수정 금지!!
 */

// 테이블 스키마
$this->table_scheme = "
CREATE TABLE IF NOT EXISTS `%s` (
  `no` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(20) NULL,
  `item_no` int(10) unsigned NULL,
  `regist_time` datetime NOT NULL,
  `regist_ip` varchar(39) NOT NULL,
  `answered_title` text NULL,
  `answered_body` text NULL,
  `answered_time` datetime NULL,
  `status` enum('request','hold','answered') NOT NULL default 'request',
  `hit` int(11) NOT NULL default '1',
  `memo` text NULL,
  PRIMARY KEY (`no`)
) ENGINE=MyISAM AUTO_INCREMENT=1";

// 등록폼 기본항목
$this->defaults[0] = array(
	'gno' => 1, // 절대수정금지!
	'group_name' => '',
	'fields' => array(
		array(
			'no' => '',
			'identity' => 'subject',
			'fixed' => 'fixed',
			'disabled' => 'disabled',
			'required' => 'checked',
			'field_name' => '제목',
			'field_type' => 'text',
			'values' => array(
				'width' => 400,
				'maxlength' => 30
			),
			'hint' => '',
			'view' => 'yes'
		),
		array(
			'no' => '',
			'identity' => 'name',
			'fixed' => 'fixed',
			'disabled' => 'disabled',
			'required' => 'checked',
			'field_name' => '이름',
			'field_type' => 'text',
			'values' => array(
				'width' => 200,
				'maxlength' => 10
			),
			'hint' => '',
			'view' => 'yes'
		),
		array(
			'no' => '',
			'identity' => 'phone',
			'fixed' => 'fixed',
			'disabled' => 'disabled',
			'required' => 'checked',
			'field_name' => '연락처',
			'field_type' => 'phone',
			'values' => array(
				'value' => 'phones'
			),
			'hint' => '',
			'view' => 'yes'
		),
		array(
			'no' => '',
			'identity' => 'email',
			'fixed' => 'fixed',
			'disabled' => 'disabled',
			'required' => 'checked',
			'field_name' => '이메일',
			'field_type' => 'email',
			'values' => array(),
			'hint' => '',
			'view' => 'yes'
		)
	)
);

// 등록항목 정의
$this->field_entires = array(
	'on_required' => ' required',
	'on_editor' => ' type="editor" nofocus',
	'on_readonly' => ' readOnly', // 2012.04.10 added
	'field_items' => array(
		'text' => '<input type="text" name="{:field:}"{:on_required:} hname="{:field_name:}" value="{:value:}" style="width:{:width:}px" maxlength="{:maxlength:}" class="simpleform" />',
		'textarea' => '<textarea{:on_editor:} name="{:field:}"{:on_required:} hname="{:field_name:}" style="width:{:width:}px;height:{:height:}px">{:value:}</textarea>',
		'radio' => array('', ' <nobr><input type="radio" name="{:field:}"{:on_required:} hname="{:field_name:}"{:checked:} value="{:value:}" id="{:field:}_{:row:}" class="input_box2" /><label for="{:field:}_{:row:}">{:value:}</label></nobr>', ''),
		'checkbox' => array('', ' <nobr><input type="checkbox" name="{:field:}[]"{:on_required:} hname="{:field_name:}"{:checked:} value="{:value:}" id="{:field:}_{:row:}" class="input_box2" /><label for="{:field:}_{:row:}">{:value:}</label></nobr>', ''),
		'select' => array('<select name="{:field:}"{:on_required:} hname="{:field_name:}"><option value="">-{:field_name:}선택-</option>', '<option value="{:value:}"{:selected:}>{:value:}</option>', '</select>'),

		/* 가공형태 */
		'phone' => '<input type="text" name="{:field:}"{:on_required:} hname="{:field_name:}" option="{:option:}" value="{:value:}" style="width:150px" maxlength="50" class="simpleform" />',
		'jumin' => '<input type="text" name="{:field:}[]"{:on_required:} hname="{:field_name:}" option="jumin" value="{:jumin1:}" style="width:80px" maxlength="6" glue="-" span="2" class="simpleform" onKeyUp="if(this.value.length==6) $(this.next()).focus()" /> - <input type="password" name="{:field:}[]" value="{:jumin2:}" style="width:100px" maxlength="7" class="simpleform" />',
		'email' => '<input type="text" name="{:field:}"{:on_required:} hname="{:field_name:}" option="email" value="{:value:}" style="width:250px" maxlength="50" class="simpleform" />',
		'homepage' => '<input type="text" name="{:field:}"{:on_required:} hname="{:field_name:}" default="http://" option="homepage" value="{:value:}" style="width:450px" maxlength="200" class="simpleform" />',
		'addrs' => array(
			0 => '<div id="zone_{:field:}">',
			1 => '<input type="text" id="zipcode" name="{:field:}[]"{:on_required:}{:on_readonly:} hname="우편번호" value="{:zipcode:}" size="10" maxlength="10" class="simpleform" />',
			2 => ' <a onClick="rankup_post.open_post(\'zipcode_frame\', null, null, \'zone_{:field:}\')"><img src="../rankup_module/rankup_member/img/btn_post.gif" align="absbottom" class="input_box3" /></a><span id="post_spot"></span>',
			3 => '
				<div style="margin-top:3px"><input type="text" id="addrs1" name="{:field:}[]"{:on_required:}{:on_readonly:} hname="{:field_name:}" value="{:addrs1:}" style="width:500px" maxlength="100" class="simpleform" /></div>
				<div style="margin-top:3px"><input type="text" id="addrs2" name="{:field:}[]" hname="{:field_name:}" value="{:addrs2:}" style="width:300px" maxlength="100" class="simpleform" /></div>',
			4 => '</div>'
		),
		'attach' => '
			<span><input type="file" name="_attach_" onChange="$write.post(this, \'{:field:}\')" /></span>
			<input type="hidden" id="{:field:}" name="{:field:}"{:on_required:} hname="{:field_name:}" />
			<input type="hidden" face="{:field:}" name="face" value="{:field:}" disabled />
			<input type="hidden" face="{:field:}" name="mode" value="post_attach" disabled />
			<input type="hidden" face="{:field:}" name="kind" value="fbuilder" disabled />
			<input type="hidden" face="{:field:}" name="handler" value="$write.draw" disabled />
			<span id="{:field:}_preview"></span>',

		'calendar' => array(
			'single' => '
				<span style="float:left"><input type="text" id="{:field:}{:identity}" name="{:field:}[]"{:on_required:} hname="{:field_name:}" mindate="1900-01-01" value="{:value:}" maxlength="10" readOnly onClick="rankup_calendar.draw_calendar(this)" class="calendar" style="vertical-align:top;margin-right:4px" /><a onClick="rankup_calendar.draw_calendar($(this).previous())"><img src="../Libs/_images/btn_calendar.png" alt="달력" style="vertical-align:top;padding-top:1px" /></a></span>
				<br style="clear:both" />',
			'dual' => '
				<span style="float:left"><input type="text" id="sdate" name="{:field:}[]"{:on_required:} hname="{:field_name:}" mindate="1900-01-01" maxlength="10" readOnly onClick="rankup_calendar.draw_calendar(this)" class="calendar" style="vertical-align:top;margin-right:4px" /><a onClick="rankup_calendar.draw_calendar($(this).previous())"><img src="../Libs/_images/btn_calendar.png" alt="달력" style="vertical-align:top;padding-top:1px" /></a> ~&nbsp;</span>
				<span style="float:left"><input type="text" id="edate" name="{:field:}[]"{:on_required:} hname="{:field_name:}" mindate="1900-01-01" maxlength="10" readOnly onClick="rankup_calendar.draw_calendar(this)" class="calendar" style="vertical-align:top;margin-right:4px" /><a onClick="rankup_calendar.draw_calendar($(this).previous())"><img src="../Libs/_images/btn_calendar.png" alt="달력" style="vertical-align:top;padding-top:1px" /></a></span>
				<br style="clear:both" />'
		),
		'dimension' => '
			<input type="text" name="{:field:}[]"{:on_required:} hname="{:field_name:}(평방미터)" value="{:square:}" size="6" maxlength="10" class="simpleform" onKeyUp="$(this).next().value = (this.value/3.3058).toFixed(1)" /> ㎡ ≒
			<input type="text" name="{:field:}[]"{:on_required:} hname="{:field_name:}(평)" value="{:pyeong:}" size="6" maxlength="10" class="simpleform" onKeyUp="$(this).previous().value = (this.value*3.3058).toFixed(1)" /> 평'
	)
);
?>