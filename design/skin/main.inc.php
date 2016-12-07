<?php
/**
 * 홈페이지 디자인
 */
include_once '../rankup_module/rankup_builder/attachment.class.php';
?>

	<!-- RANKUP_POPUP TEMPLATE_V2 -->
	<script type="text/javascript" src="../Libs/_js/rankup_popup.js"></script>
	<div id="popupTemplate" style="display:none;font-size:9pt;">
	<iframe src="about:blank" style="position:absolute;width:1px;height:1px" frameborder="0"></iframe>
	<table  onmousedown="rankup_popup.div_move_check(1, event, '{:popup_id:}')" style="cursor:move;visibility:hidden;position:absolute" border="0" cellpadding="1" cellspacing="1" bgcolor="#cacaca" align="center">
	<tr>
		<td bgcolor="#ffffff">
			<table width="100%" border="0" cellpadding="3" cellspacing="0" bgcolor="#f1f1f1">
			<tr>
				<td style="font-weight:bolder;color:black;padding:5px 2px 0 4px">{:popup_title:}</b></td>
			</tr>
			<tr valign="top"><td id="popup_content{:no:}" style="cursor:default"></td></tr>
			<tr>
				<td align="right" background="../rankup_module/rankup_popup/img/dp_background.gif">
					<table border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td nowrap><input type="checkbox" name="chkbox{:no:}" id="chkbox_id{:no:}" onFocus="this.blur()" style="cursor:pointer;"></td>
						<td nowrap><label for="chkbox_id{:no:}" style="padding:2px 8px 0 0;height:14px;font-size:9pt;cursor:pointer" onFocus="this.blur()">오늘하루 그만보기</label></td>
						<td nowrap><a href="javascript:rankup_popup.closeWin({:no:})"><img src="../rankup_module/rankup_popup/img/dp_bclose.gif" border="0"></a></td>
					</tr>
					</table>
				</td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
	</div>
	<!-- RANKUP_POPUP TEMPLATE_V2 -->
	<script type="text/javascript"> rankup_popup.initialize("popupTemplate") </script>

<?php
/**
 * 메인 플래시
 */
$ms_rows = $design->get_settings('main_visual_design');
if($ms_rows['container_width']>$config_info['site_width']) {
	$ms_content_gap = floor(($ms_rows['container_width'] - $config_info['site_width']) / 2);
	$ms_style = sprintf('height:%dpx;margin: 0 -%dpx', $ms_rows['container_height'], $ms_content_gap); // 2012.05.09 fixed
}
else {
	$ms_style = sprintf('width:%dpx;height:%dpx;', $ms_rows['container_width'], $ms_rows['container_height']);
}

/**
 * 회원제사용시 - 아웃로그인 추가
 */
if($config_info['membership_use']=='yes') {
	include_once 'outlogin.inc.php';
}
?>
	<style type="text/css"> @import url('../design/main/visual.css'); </style>
	<div id="visual_frame">
		<center>
			<div class="visual_twrap"></div>
			<div class="visual_f" style="<?=$ms_style?>">
				<?php
				// 기본 플래시 - 갤러리
				$ms_rows = $design->get_settings('main_visual_design');
				if($ms_rows['flash_type']=='basic') {
					scripts(sprintf(
						"flashDraw('%s','%s','%s','%s','%s','%s')",
							'gallery',
							'../design/gallery.swf',
							$ms_rows['container_width'],
							$ms_rows['container_height'],
							'transparent',
							'xmlPath=../design/xml/main_flash.xml?dummy='.rand()
						)
					);
				}
				else {
					// 사용자 제작
					$attach = new attachment('main_flash');
					echo $attach->preview($ms_rows['main_flash'], array(
						'image' => '<img src="{:folder:}{:name:}" align="absmiddle" />',
						'flash' => "<script>flashDraw('gallery', '{:folder:}{:name:}', '{:width:}', '{:height:}', 'transparent', '')</script>"
					));
				}
				unset($ms_rows);
				?>
			</div>
			<div class="visual_bwrap"></div>
		</center>
	</div><!-- visual_frame End -->

<?php
// 메인게시판 디자인
$mb_rows = $design->get_settings('main_board_design');
include_once '../Libs/_php/rankup_board_mini.class.php';
$board = new rankup_board_mini;
?>

	<style type="text/css"> @import url('../design/main/tab.css'); </style>
	<div id="main_container">

		<div id="main_t_box">
			<div id="board_frame">
				<h2 class="hidden">공지사항 뉴스 문의 </h2>
				<ul class="tab_wrap"><!-- notice_tab -->
					<?php
					// 게시판 탭 출력
					$boards = array();
					foreach(array('tab1', 'tab2', 'tab3') as $tab) {
						if($mb_rows[$tab]['board']) array_push($boards, $mb_rows[$tab]['board']);
					}
					if(count($boards)) {
						foreach($boards as $index=>$mb_board) {
							$line = (count($boards)-1>$index) ? '<img src="../design/skin/img/main/b_line.png" alt="라인" class="line_tab" />' : '';
							echo $board->get_board_names($mb_board, '<li class="tab'.($index+1).'" onClick="tab.click(this)" bid="{:id:}">{:name:}'.$line.'</li>');
						}
					}
					?>
				</ul>

				<div class="more"><a onClick="tab.more()"><img src="../design/skin/img/main/btn_more.png" alt="더보기" /></a></div>
				<?php
				// 게시판 탭 내용 출력
				foreach(array('tab1', 'tab2', 'tab3') as $tab) {
					if(!$mb_rows[$tab]['board']) continue;
					$display = $articles ? ' style="display: none"' : '';
					$articles = $board->print_recent_articles($mb_rows[$tab]['board'], array(
						'times' => $mb_rows[$tab]['limits'],
						'entry' => array(
							0 => '<li></li>',
							1 => '<li><span class="subject"><a href="../board/index.html?id='.$mb_rows[$tab]['board'].'&no={:no:}">{:subject:}</a></span><span class="wdate">{:wdate:}</span></li>'
						),
						'subject_length' => $mb_rows[$tab]['length'],
						'date_format' => 'Y.m.d'
					));
					echo sprintf('<ul class="article" id="%s_frame"%s>%s</ul><div class="clear"></div>', $tab, $display, $articles);
				}
				?>
			</div><!-- board_frame End -->

			<div id="gallery_frame">
				<h2>
					<?php
					// 갤러리형 게시판 제목 출력
					$attach = new attachment('gallery_title');
					$gallery_title = $attach->preview($mb_rows['gallery_title'], array(
						'image' => sprintf('<img src="{:folder:}{:name:}" alt="%s" />', $gallery_title)
					));
					if(!$gallery_title) $gallery_title = $board->get_board_names($mb_rows['gallery_board'], '{:name:}');
					echo $gallery_title;
					?>
				</h2>
				<div class="more"><a href="../board/index.html?id=<?=$mb_rows['gallery_board']?>"><img src="../design/skin/img/main/btn_more.png" alt="더보기" /></a></div>
				<?php
				// 갤러리형 게시물 출력
				echo $board->print_recent_articles($mb_rows['gallery_board'], array(
					'times' => 3,
					'entry_wrap' => array('<ul class="gall_img">', '</ul>'),
					'entry' => array(
						0 => '<li></li>',
						1 => '
							<li class="{:on_end:}">
								<dt><a href="../board/index.html?id='.$mb_rows['gallery_board'].'&no={:no:}">{:on_photo:}</a></dt>
								<dd class="tit_bg"><img src="../design/skin/img/main/main_i_bg.png" alt="갤러리이미지" /></dd>
								<dd class="tit_txt"><a href="../board/index.html?id='.$mb_rows['gallery_board'].'&no={:no:}">{:subject:}</a></dd>
							</li>'
					),
					'on_photo' => '<img src="{:photo:}" />',
					'non_photo' => $base_url.SKIN.'img/no_img.gif',
					'on_end' => ' end'
				));
				?>
				<div class="clear"></div>
			</div><!-- gallery_frame End -->

			<div id="product_frame">
				<h2><img src="../design/skin/img/main/tit_product.png" alt="제품" /></h2>
				<div class="more"><a href="../product/list.html"><img src="../design/skin/img/main/btn_more.png" alt="더보기" /></a></div>

				<div id="product_items">
				<?php
				// 제품 롤링
				include_once '../product/class/product.class.php';
				$attach = new attachment('product', $base_dir.'product/');
				$product = new product;
				echo $product->print_main_contents(array(
					'entry' => array(
						1 => '
						<dl class="main_items">
							<dt class="p_img"><a href="../product/view.html?pid={:mpid:}&cate1={:cate1:}&cate2={:cate2:}&no={:no:}"><img src="../{:save_folder:}{:filename:}" style="width:115px;height:85px"></a></dt>
							<dd class="p_info">
								<p class="p_name"><a href="../product/view.html?pid={:mpid:}&cate1={:cate1:}&cate2={:cate2:}&no={:no:}">{:title:}</a></p>
								<p class="p_text"><a href="../product/view.html?pid={:mpid:}&cate1={:cate1:}&cate2={:cate2:}&no={:no:}">{:comment:}</a></p>
							</dd>
						</dl>',
					),
					'save_folder' => $attach->presets['product']['save']['folder']
				));
				?>
				</div><!-- preview_items End -->
			</div>
			<div class="clear"></div>
		</div><!-- main_t_box End -->
		<script type="text/javascript">
		//<![CDATA[
		var tab = {
			sel: null,
			bid: null,
			click: function(el) {
				this.bid = el.getAttribute('bid');
				while(!el.nodeName.match(/li/i)) el = $(el).up();
				if(this.sel!=null) {
					this.sel.removeClassName('selected');
					$(this.sel.className +'_frame').hide();
				}
				this.sel = $(el);
				$(el.className +'_frame').show();
				el.addClassName('selected');
			},
			more: function() {
				location.href = domain+'board/index.html?id='+ this.bid;
			}
		}
		tab.click($('board_frame').select('li[class~="tab1"]')[0]);
		scroller.initialize('product_items', 5000);
		//]]>
		</script>

		<div id="main_b_box"><!--  메인하단 배너박스  -->
<?php
// 메인배너 1
$main_banner1 = $rankup_control->print_banner('main1');
if($main_banner1) {
?>
			<div class="main_banner1">
				<?=$main_banner1?>
			</div>
<?php
}

// 메인배너 2
$main_banner2 = $rankup_control->print_banner('main2');
if($main_banner2) {
?>
			<div class="main_banner2">
				<?=$main_banner2?>
			</div>
<?php
}

// 메인배너 3
$main_banner3 = $rankup_control->print_banner('main3');
if($main_banner3) {
?>
			<div class="main_banner3">
				<?=$main_banner3?>
			</div>
<?php
}
unset($main_banner1, $main_banner2, $main_banner3);
?>
		</div> <!-- main_b_box End -->
	</div><!-- main_container End -->

<?php
// 메인하단
$mb_content = $design->get_settings('main_bottom_content');
echo sprintf('<div class="main_bottom_content fbg_col">%s</div>', $mb_content);
unset($mb_content);
?>