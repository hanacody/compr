<?php
/**
 * 컬러픽커 출력
 */
function color_picker($name, $value='', $handler='', $attributes='') {
	if($handler) $handler = ','.$handler;
	return sprintf('<input type="text" id="%s" name="%s"%s value="%s" onKeyUp="color_picker.setRGB(this,false%s)" onBlur="color_picker.setRGB(this,true%s)" maxlength="7" class="color" /><p id="%s_prev" class="colorPreview" onClick="color_picker.open(this%s)"></p>', $name, $name, $attributes, $value, $handler, $handler, $name, $handler);
}
?>

<script type="text/javascript" src="<?=$base_url?>Libs/_picker/yui/utilities.js"></script>
<script type="text/javascript" src="<?=$base_url?>Libs/_picker/yui/slider.js"></script>
<script type="text/javascript" src="<?=$base_url?>Libs/_picker/yui/colorpicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$base_url?>Libs/_picker/yui/colorpicker.css">
<script type="text/javascript" src="<?=$base_url?>Libs/_picker/color_picker.class.js"></script>
<link rel="stylesheet" type="text/css" href="<?=$base_url?>Libs/_picker/color_picker.css">
<div id="color_picker_panel" style="visibility:hidden">
	<iframe src="about:blank" style="position:absolute;width:226px;height:216px;margin:-5px;border:#ededed 2px solid;z-index99;" frameborder="0"></iframe>
	<div id="yui-picker-panel">
		<div class="yui-picker" id="yui-picker"></div>
	</div>
	<div style="position:relative;width:100%;text-align:center;padding:0px;margin:0px;border:red 0px dotted;z-index:102;">
		<a onClick="color_picker.close()"><img src="<?=$base_url?>Libs/_picker/img/btn_apply.gif" align="absmiddle"></a>
		<a onClick="color_picker.cancel()"><img src="<?=$base_url?>Libs/_picker/img/btn_cancel.gif" align="absmiddle"></a>
	</div>
</div>
