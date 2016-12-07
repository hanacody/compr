<?php
/**
 * 좌측 네비게이션 바(LNB)
 */
include_once $base_dir.'rankup_module/rankup_builder/attachment.class.php';
$ls_rows = $design->get_settings('left_menu_design');

// 기본메뉴
if($ls_rows['lnb_type']=='basic') {
?>
		<style type="text/css"> @import url('<?=$base_url?>design/left/lnb.css'); </style>
		<div id="left_menu">
			<?php
			include_once $base_dir.SKIN.'css_menu.class.php';
			$css_menu = new css_menu;

			// 서브메뉴 타이틀
			if($ls_rows['lnb_title_type']=='image') { // image 형
				$attach = new attachment('lnb_title');
				$lnb_title_entry = $attach->preview($ls_rows['lnb_titles'][$gen->parent['no']], array('image' => '<span><img src="{:folder:}{:name:}" /></span>'));
			}
			else { // text 형
				$lnb_title_entry = '<div class="text_menu"><span class="l_tit">{:base_name:}</span><span class="l_tit2">{:extra_name:}</span></div>';
			}

			// LNB draw
			echo $css_menu->draw_lnb(array(
				// 1차메뉴
				'entry' => '
				<div class="l_tit_box">
					<h2>'.$lnb_title_entry.'</h2>
				</div>
				<div class="h20"></div>
				<div class="l_menu_box">
					<ul id="css_lnb_frame" class="lmenu">
						{:on_child:}
					</ul>
				</div>',

				// 2차메뉴
				'child_entry' => array(
					'entry' => '
						<li><p pid="{:pid:}" class="secon_m {:on_hover:}" hover="{:on_hover:}" onMouseOver="css_lnb.over(this)" onClick="menu_handler({:pid:})">{:base_name:}</p></li>
						{:on_child:}',
					'on_hover' => 'hover',
					'on_child' => '<li sub="{:parent:}" style="display:none"><dl class="third_m">{:on_child:}</dl></li>',

					// 3차 메뉴
					'child_entry' => array(
						'entry' => '
							<dd parent="{:parent:}" pid="{:pid:}" class="{:on_hover:}" hover="{:on_hover:}" onMouseOver="css_lnb.over(this)" onClick="menu_handler({:pid:})">{:base_name:}</dd>',
						'on_hover' => 'hover'
					)
				)
			));
			?>
		</div><!-- left_menu End -->
		<div class="clear"></div>
		<script type="text/javascript" src="<?=$base_url.SKIN?>css_lnb.js"></script>
		<script type="text/javascript"> css_lnb.initialize('css_lnb_frame') </script>
<?php
}

// 사용자제작 플래시
else {
	$attach = new attachment('lnb_flash');
	$lnb_flash = $ls_rows['lnb_flashes'][$gen->parent['no']];
	echo $attach->preview($lnb_flash['file'], array(
		'image' => '<img src="{:folder:}{:name:}" align="absmiddle" />',
		'flash' => sprintf(
			"<script>flashDraw('lnb','{:folder:}{:name:}','%s','%s','%s','%s')</script>",
				$lnb_flash['width'],
				$lnb_flash['height'],
				'transparent',
				''
			)
	));
}
?>