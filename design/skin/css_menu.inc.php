<?php
/**
 * 글로벌 네비게이션 바(GNB)
 */
?>
<script type="text/javascript" src="<?=$base_url.SKIN?>css_menu.js"></script>
<style type="text/css">
<?php
if(rankup_util::ie_version()=='6.0') echo "@import url('".$base_url.SKIN."css_menu_ie6.css');";
else echo "@import url('".$base_url.SKIN."css_menu.css');";
?>
@import url('<?=$base_url?>design/top/gnb.css');
</style>
<div id="css_gnb_frame">
<?php
/**
 * CSS 메뉴 구현
 *@note: design/skin/css_menu.css 파일 - CSS 메뉴 기본 스타일
 *@note: design/top/gnb.css 파일 - 관리자에서 설정
 */
include_once 'css_menu.class.php';
$css_menu = new css_menu;
?>
	<div class="mm_wrap">
		<?php
		// 1차 메뉴
		echo $css_menu->draw_gnb(1, array(
			'wrap' => '<ul class="mm">{:content:}</ul>',
			'entry' => '<li pid="{:pid:}" off="{:base_name:}" on="{:extra_name:}" class="{:on_first:}{:on_hover:}" hover="{:on_hover:}" onMouseOver="css_menu.over(this)" onClick="css_menu.click({:pid:}, true);">{:base_name:}</li>',
			'on_first' => 'first ',
			'on_hover' => 'hover'
		));
		?>
	</div>
	<?php
	// 2차 메뉴
	echo $css_menu->draw_gnb(2, array(
		'wrap' => '<ul class="sm s{:parent:}" style="display:{:display:}">{:content:}</ul>',
		'entry' => '<li class="{:on_first:}{:on_hover:}" hover="{:on_hover:}" onClick="menu_handler({:pid:})">{:base_name:}</li>',
		'on_first' => 'first ',
		'on_hover' => 'hover'
	));
	unset($css_menu);
	?>
</div>
<script type="text/javascript"> css_menu.initialize('css_gnb_frame') </script>