<?php
/**
 * ��� ���� ������
 */

// ��ܸ޴� ������ �ε�
$tm_rows = $design->get_settings('top_menu_design');
?>
<body>

<div id="body_wrap" class="thema_1">
	<div id="header">
		<div id="top_box">
			<div id="top_frame">
				<div class="logo">
					<?php
					// ��ܷΰ�
					echo $rankup_control->print_logo(1);
					?>
				</div>
				<div class="right_box">
					<ul class="right">
						<?php
						// ȸ��������
						if($config_info['membership_use']=='yes') {
							if(!$member_info['uid']) {
								echo '<li class="first"><a href="'.$base_url.'rankup_module/rankup_member/login.html"><img src="'.$base_url.SKIN.'img/t_login.png" alt="�α���" /></a></li>';
								echo '<li><a href="'.$base_url.'rankup_module/rankup_member/join_intro.html"><img src="'.$base_url.SKIN.'img/t_join.png" alt="ȸ������" /></a></li>';
								echo '<li><a href="'.$base_url.'rankup_module/rankup_member/find_login_info.html"><img src="'.$base_url.SKIN.'img/t_mem_find.png" alt="id/pwã��" /></a></li>';
							}
							else {
								echo '<li class="first"><a onClick="logout()"><img src="'.$base_url.SKIN.'img/t_logout.png" class="t_out" alt="�α׾ƿ�" /></a></li>';
								echo '<li><a href="'.$base_url.'mypage/index.html"><img src="'.$base_url.SKIN.'img/t_mypage.png" alt="����������" /></a></li>';
							}
						}
						?>
						<li<? if($config_info['membership_use']!='yes') echo ' class="first"' ?>><a href="<?=$base_url?>etc/sitemap.html"><img src="<?=$base_url.SKIN?>img/t_sitemap.png" alt="����Ʈ��" /></a></li>
						<li><a href="javascript:void(0)" onclick="rankup_favorite('<?php echo addslashes($config_info['domain'])?>', '<?php echo addslashes($config_info['bookmark'])?>')"><img src="<?=$base_url.SKIN?>img/t_fav.png" alt="���ã���߰�" /></a></li>
					</ul>
				</div><!-- right_box End -->
				<div class="clear"></div>
			</div><!-- top_frame End -->

			<div id="gnb_frame">
				<div class="gnb_wrap">
					<?php
					/**
					 * GNB Draw
					 */
					// ����� ���ε� �޴�
					if($tm_rows['gnb_type']=='upload') {
						include_once $base_dir.'rankup_module/rankup_builder/attachment.class.php';
						$attach = new attachment('top_flash');
						echo $attach->preview($tm_rows['top_flash'], array(
							'image' => '<img src="{:folder:}{:name:}" align="absmiddle" />',
							'flash' => sprintf(
								"<script>flashDraw('gnb','{:folder:}{:name:}','%s','%s','%s','%s')</script>",
									$tm_rows['container_width'],
									$tm_rows['container_height'],
									'transparent',
									'xmlPath='.$base_url.'design/xml/gnb.xml&one='.$_SESSION['one'].'&two='.$_SESSION['two'].'&dummy='.rand()
								)
						));
						unset($attach);
					}
					// �⺻ �÷��� �޴�
					else if($tm_rows['gnb_type']=='basic') {
						// 2012.05.09 added
						list($gnb_one, $gnb_two) = array($_SESSION['one'], $_SESSION['two']);
						if(isset($gen)) {
							$gnb_one = $gen->get_gnb($gen->infos['no'], 1);
							$gnb_two = $gen->get_gnb($gen->infos['no'], 2);
						}
						scripts(sprintf(
							"flashDraw('%s','%s','%s','%s','%s','%s')",
								'gnb',
								$base_url.'design/gnb.swf',
								$tm_rows['container_width'],
								$tm_rows['container_height'],
								'transparent',
								'xmlPath='.$base_url.'design/xml/gnb.xml&one='.$gnb_one.'&two='.$gnb_two.'&dummy='.rand()
							)
						);
					}
					// �⺻ css �޴�
					else if($tm_rows['gnb_type']=='html') {
						include_once 'css_menu.inc.php';
					}
					?>
				</div><!-- gnb_wrap End -->
			</div><!-- gnb_frame End -->
		</div><!-- top_box End -->
	</div><!-- header End -->
	<div class="clear"></div>

<?php
unset($tm_rows);

/**
 * ���������� ������
 */
if(isset($gen)) {

	// ���������� ����̹���
	$td_style = '';
	$td_rows = $gen->top_design();
	if($td_rows['content']) {
		if($td_rows['width']>$config_info['site_width']) {
			$td_style = sprintf(';margin: 0 -%dpx', floor(($td_rows['width'] - $config_info['site_width']) / 2));
		}
		echo '
		<div id="sub_visual_frame">
			<div class="s_visual_twrap"></div>
			<div class="img_visual">'.sprintf('<div style="position:relative;z-index:0;width:%dpx%s">%s</div>', $td_rows['width'], $td_style, $td_rows['content']).'</div>
			<div class="s_visual_bwrap"></div>
			</div>
		</div><!-- sub_visual_frame End -->';
	}
	unset($td_rows, $td_style);

?>

	<div id="content_wrap">

<?php
// �����޴��ΰ� �ʿ���� ������ ��ŵ
if($_GET['pid'] && $gen->pids[$_GET['pid']] && $gen->parent['use_lnb']=='yes') {
	$contents_class = 'content_right';
?>
		<div id="left_wrap">
			<?php
			/**
			 * ���� �׺���̼� ��(LNB)
			 */
			include_once $base_dir.SKIN.'css_lnb.inc.php';

			// ���ڻ�㼭�� �ε� - 2012.03.21 added
			if($config_info['letter_consult_use']=='yes') {
				include_once $base_dir.'rankup_module/rankup_lconsult/lconsult.inc.php';
			}

			// �����������
			$sub_left_banner = $rankup_control->print_banner('sub_left');
			if($sub_left_banner) echo '<div class="banner_frame">'.$sub_left_banner.'</div>';

			?>
		</div><!-- left_wrap End -->
<?php
}
else {
	// �����޴��� ������ content_full ó��
	$contents_class = 'content_full';
}
?>

		<div id="contents" class="<?=$contents_class?>">
			<div id="con_top_box">
				<h3 class="h_title">
					<?php
					// ������Ÿ��Ʋ ���
					echo $gen->title(array('<span class="sub_img"><img src="{:title_image:}" alt="{:base_name:}" /></span>', '<div class="sub_h3"><span class="sub_tit tit_bullet"/>&nbsp;</span> {:base_name:}</div>'));
					?>
				</h3>
				<p class="con_navi"><img src="<?=$base_url.SKIN?>img/b_home.png" alt="Ȩ" />home<?php echo $gen->location(' > {:base_name:}') ?> > <span class="on_navi"><?=$gen->page_title?></span></p>
			</div><!-- con_top_box End -->

			<div id="p_content_box">
				<?php
				// ��(3���޴�) ���
				$third_menus = $gen->third_menus(array(
					'entry' => '<li class="{:on_first:}{:on_hover:}"><a onClick="menu_handler({:no:})">{:base_name:}</a></li>',
					'on_first' => 'first_tab',
					'on_hover' => ' tab_choice'
				));
				if($third_menus) echo '<ul class="third_tabs">'.$third_menus.'</ul>';

				// ��������� ������
				$top_content = $gen->top_content();
				if($top_content) echo '<div class="mb20">'.$top_content.'</div>';
				?>
<?php
}
?>