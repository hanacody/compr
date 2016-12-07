<?php
/**
 * 메인 플래시용 XML 데이터 생성
 */
function make_main_flash_data() {
	global $design, $base_dir;
	$entry = array(
		'entry' => array(
			1 => array('
			<root>
				<containerWidth>{:container_width:}</containerWidth>
				<containerHeight>{:container_height:}</containerHeight>
				<images type="{:image_motion:}">
					{:images:}
				</images>
				{:imageList:}
				<contents type="{:text_motion:}">
					<top>
						<text><![CDATA[{:top_text:}]]></text>
						<size>{:top_text_size:}</size>
						<color>{:top_text_color:}</color>
					</top>
					<middle>
						<text><![CDATA[{:middle_text:}]]></text>
						<size>{:middle_text_size:}</size>
						<color>{:middle_text_color:}</color>
					</middle>
					<bottom>
						<text><![CDATA[{:bottom_text:}]]></text>
						<size>{:bottom_text_size:}</size>
						<color>{:bottom_text_color:}</color>
					</bottom>
					<posi>
						<top>{:text_container_top:}</top>
						<left>{:text_container_left:}</left>
					</posi>
					<bgImage>{:bgImage:}</bgImage>
					<transparent>{:text_container_opacity:}</transparent>
				</contents>
			</root>'
			)
		),
		'on_images' => '<src>../{:folder:}{:file_name:}</src>',
		'on_imageList' => '
			<imageList type="{:image_list_kind:}">
				<posi>
					<top>{:image_list_top:}</top>
					<left>{:image_list_left:}</left>
				</posi>
				<transparent>{:image_list_opacity:}</transparent>
			</imageList>',
		'on_bgImage' => '../{:folder:}{:file_name:}'
	);
	$rows = $design->get_settings('main_visual_design');
	$nodes = fetch_contents(array($rows), $entry, '_mf');

	// EUC-KR 인코딩 처리
	if(rankup_util::check_unicode($nodes)) {
		$nodes = rankup_util::euckr($nodes);
	}

	// make file
	$fp = fopen($base_dir.'design/xml/main_flash.xml', 'w');
	fwrite($fp, '<?xml version="1.0" encoding="euc-kr" ?>'.$nodes);
	fclose($fp);
}
function _mf($bind) {
	global $base_dir, $base_url;
	extract($bind);
	$attach = new attachment('main_flash');
	// 갤러리 이미지
	$folder = $attach->configs['save']['folder'];
	$files = glob($base_dir.$folder.'mfbg.*');
	if(is_array($files)) {
		$_images = array();
		foreach($files as $file) {
			$file_name = basename($file);
			array_push($_images, fetch_skin(compact('folder', 'file_name'), $on_images));
		}
		$rows['images'] = implode('', $_images);
		if($rows['image_list_use']=='yes' && $rows['images']) {
			$rows['imageList'] = fetch_skin($rows, $on_imageList);
		}
	}
	// 문구색상
	foreach(array('top_text_color', 'middle_text_color', 'bottom_text_color') as $key) {
		$rows[$key] = str_replace('#', '0x', $rows[$key]);
	}
	// 문구전체 배경 이미지
	$folder = $attach->presets['text_container_bg']['save']['folder'];
	$_files = glob($base_dir.$folder.'mftbg.*');
	if(is_array($_files)) {
		$file_name = basename(array_pop($_files));
		$rows['bgImage'] = fetch_skin(compact('folder', 'file_name'), $on_bgImage);
	}
	return array($rows, $skin);
}


/**
 * 메인비쥬얼 스타일 생성
 */
function make_visual_bg_style() {
	global $design, $base_dir, $base_url;
	$styles = array();
	$mv_rows = $design->get_settings('main_visual_bg');
	switch($mv_rows['bg_type']) {
		case 'none': break;
		case 'color':
			switch($mv_rows['visual_bg_type']) {
				case 'solid':
					$styles = array('#visual_frame {', sprintf('	background-color: %s;', $mv_rows['visual_bg_scolor']), '}');
					break;
				case 'gradient':
					$colors = array($mv_rows['visual_bg_gcolor1'], $mv_rows['visual_bg_gcolor2']);
					$gtype = ($mv_rows['visual_bg_gtype']=='height') ? array(0, 'top', '0% 100%') : array(1, 'left', '100% 0%');
					$styles = array(
						'#visual_frame {',
							sprintf('	filter: progid:DXImageTransform.Microsoft.Gradient(GradientType=%d, StartColorStr=%s, EndColorStr=%s);', $gtype[0], $colors[0], $colors[1]),
							sprintf('	background: -moz-linear-gradient(%s, %s, %s);', $gtype[1], $colors[0], $colors[1]),
							sprintf('	background: -webkit-gradient(linear, 0%% 0%%, %s, from(%s), to(%s));', $gtype[2], $colors[0], $colors[1]),
						'}'
					);
					break;
			}
			break;
		case 'skin':
		case 'image':
			if($mv_rows['bg_type']=='skin') $bg = $base_url.'design/main/bg_'.$mv_rows['visual_bg_skin'].'.jpg';
			else $bg = $base_url.'design/main/'.$mv_rows['visual_bg'];
			$styles = $bg ? array('#visual_frame {', sprintf('	background: url(%s) repeat center center;', $bg), '}') : array();
			break;
	}

	$fp = fopen($base_dir.'design/main/visual.css', 'w');
	fwrite($fp, implode("\n", $styles));
	fclose($fp);
}


/**
 * 상단GNB 플래시용 XML 데이터 생성
 */
function make_top_menu_data() {
	global $design, $frame, $base_dir, $base_url;
	$rows = $design->get_settings('top_menu_design');
	$nodes = fetch_contents(array($rows), array(
		'entry' => array(
			1 => '
			<setting>
				<containerWidth>{:container_width:}</containerWidth>
				<containerHeight>{:container_height:}</containerHeight>
				<depth1MenuSpace>{:menu_item_space:}</depth1MenuSpace>
				<depth2MenuSpace>{:submenu_item_space:}</depth2MenuSpace>
				<depth2MenuStartPositionXList>{:submenu_pos:}</depth2MenuStartPositionXList>
				<bgType>
					<depth1 type="{:menu_bg_type:}">
						<solid>
							<bgColor>{:menu_bg_scolor:}</bgColor>
						</solid>
						<gradient>
							<bgGradientStartColor>{:menu_bg_gcolor1:}</bgGradientStartColor>
							<bgGradientEndColor>{:menu_bg_gcolor2:}</bgGradientEndColor>
							<bgGradientType>{:menu_bg_gtype:}</bgGradientType>
						</gradient>
						<image>
							<url>{:on_menu_bg:}</url>
						</image>
					</depth1>
					<depth2 type="{:submenu_bg_type:}">
						<solid>
							<bgColor>{:submenu_bg_scolor:}</bgColor>
						</solid>
						<gradient>
							<bgGradientStartColor>{:submenu_bg_gcolor1:}</bgGradientStartColor>
							<bgGradientEndColor>{:submenu_bg_gcolor2:}</bgGradientEndColor>
							<bgGradientType>{:submenu_bg_gtype:}</bgGradientType>
						</gradient>
					</depth2>
				</bgType>
				<depth1MenuTextOnColor>{:menu_text_oncolor:}</depth1MenuTextOnColor>
				<depth1MenuTextOffColor>{:menu_text_offcolor:}</depth1MenuTextOffColor>
				<depth2MenuTextOnColor>{:submenu_text_oncolor:}</depth2MenuTextOnColor>
				<depth2MenuTextOffColor>{:submenu_text_offcolor:}</depth2MenuTextOffColor>
			</setting>'
		),
		'on_menu_bg' => $base_url.'{:folder:}{:file_name:}',
	), '_tm');

	$datas = $frame->query("select * from $frame->frame_table where depth=1 and used='yes' and use_gnb='yes' order by bundle, position");
	$menus = fetch_contents($datas, array(
		'entry' => array(
			1 => '
			<depth1>
				<label>{:base_name:}</label>
				<label2>{:extra_name:}</label2>
				<url target="javascript"><![CDATA[menu_handler({:no:})]]></url>
				{:on_childs:}
			</depth1>'
		),
		'on_childs' => array(
			'entry' => array(
				1=> '
				<depth2>
					<label>{:base_name:}</label>
					<url target="javascript"><![CDATA[menu_handler({:no:})]]></url>
				</depth2>'
			)
		)
	), '_tm2');

	// EUC-KR 인코딩 처리
	if(rankup_util::check_unicode($nodes)) $nodes = rankup_util::euckr($nodes);
	if(rankup_util::check_unicode($menus)) $menus = rankup_util::euckr($menus);

	// make file
	$fp = fopen($base_dir.'design/xml/gnb.xml', 'w');
	fwrite($fp, '<?xml version="1.0" encoding="euc-kr" ?><root>'.$nodes.'<menu>'.$menus.'</menu></root>');
	fclose($fp);

	// PIDS 데이터 생성
	make_pids_data();
}
function _tm($bind) {
	global $base_dir, $base_url;
	extract($bind);
	$rows['submenu_pos'] = implode(',', array_values($rows['submenu_pos']));
	// 메뉴 배경 이미지
	if($rows['menu_bg']) {
		$attach = new attachment('menu_bg');
		$folder = $attach->configs['save']['folder'];
		$file_name = $rows['menu_bg'];
		if(is_file($base_dir.$folder.$file_name)) {
			$rows['on_menu_bg'] = fetch_skin(compact('folder', 'file_name'), $on_menu_bg);
		}
	}
	// 문구색상
	foreach(array('menu_bg_scolor', 'menu_bg_gcolor1', 'menu_bg_gcolor2', 'submenu_bg_scolor', 'submenu_bg_gcolor1', 'submenu_bg_gcolor2', 'menu_text_oncolor', 'menu_text_offcolor', 'submenu_text_oncolor', 'submenu_text_offcolor') as $key) {
		$rows[$key] = str_replace('#', '0x', $rows[$key]);
	}
	return array($rows, $skin);
}
function _tm2($bind) {
	global $frame;
	extract($bind);
	$rows['rank'] = $rank;
	// 하위메뉴 처리
	if($rows['depth']==1 && $rows['has_child']=='yes') {
		$datas = $frame->query("select * from $frame->frame_table where parents='$rows[no]' and depth=2 and used='yes' and use_gnb='yes' order by bundle, position"); // use_gnb='yes' - 2012.05.09 fixed
		$rows['on_childs'] = fetch_contents($datas, $on_childs, '_tm2');
	}
	return array($rows, $skin);
}

// MENU 데이터 및 PIDS.inc 파일 생성
function make_pids_data() {
	global $frame, $base_dir, $base_url;

	unlink($base_dir.'design/top/pids.inc');
	$pp = fopen($base_dir.'design/top/pids.inc', 'a');
	fwrite($pp, "<?php\n// 메뉴 구현용 데이터 - 중요!\n"); // bof

	$rank = 0;
	$datas = $frame->query("select * from $frame->frame_table where depth=1 order by bundle, position");
	while($rows = $frame->fetch($datas)) write_pids($pp, $rows, ++$rank);
	fwrite($pp, "?>"); // eof
	fclose($pp);
}
function write_pids($pp, $rows, $rank) {
	global $frame;

	$parent = array_pop(explode(',', $rows['parents']));
	fwrite($pp, sprintf("\$pids[%d] = array('no' => %d, 'rank' => %d, 'depth'=> %d, 'parent' => '%s', 'base_name' => '%s', 'extra_name' => '%s', 'has_child' => '%s', 'access_level' => '%s', 'page_type' => '%s', 'module' => '%s', 'component' => '%s', 'options' => '%s', 'link' => '%s', 'url' => '%s', 'used' => '%s', 'use_gnb' => '%s', 'use_lnb' => '%s');\n", $rows['no'], $rows['no'], $rank, $rows['depth'], $parent, addslashes($rows['base_name']), addslashes($rows['extra_name']), $rows['has_child'], $rows['access_level'], $rows['page_type'], $rows['module'], $rows['component'], str_replace("'", '"', $rows['options']), $rows['link'], $rows['url'], $rows['used'], $rows['use_gnb'], $rows['use_lnb']));

	// 하위메뉴 처리
	if($rows['has_child']=='yes') {
		$_rank = 0;
		$depth = $rows['depth'] + 1;
		$parents = $rows['parents'] ? $rows['parents'].','.$rows['no'] : $rows['no']; // 3차 메뉴까지 처리함
		$datas = $frame->query("select * from $frame->frame_table where parents='$parents' and depth=$depth order by bundle, position");
		while($_rows = $frame->fetch($datas)) write_pids($pp, $_rows, ++$_rank);
	}
}



/**
 * 사이트배경 스타일 생성
 */
function make_site_bg_style() {
	global $design, $base_dir, $base_url;
	$styles = array();
	$ds_rows = $design->get_settings('site_design');
	switch($ds_rows['bg_type']) {
		case 'none': break;
		case 'color':
			switch($ds_rows['site_bg_type']) {
				case 'solid':
					$styles = array('body {', sprintf('	background-color: %s;', $ds_rows['site_bg_scolor']), '}');
					break;
				case 'gradient':
					$colors = array($ds_rows['site_bg_gcolor1'], $ds_rows['site_bg_gcolor2']);
					$gtype = ($ds_rows['site_bg_gtype']=='height') ? array(0, 'top', '0% 100%') : array(1, 'left', '100% 0%');
					$styles = array(
						'body {',
							sprintf('	filter: progid:DXImageTransform.Microsoft.Gradient(GradientType=%d, StartColorStr=%s, EndColorStr=%s);', $gtype[0], $colors[0], $colors[1]),
							sprintf('	background: -moz-linear-gradient(%s, %s, %s);', $gtype[1], $colors[0], $colors[1]),
							sprintf('	background: -webkit-gradient(linear, 0%% 0%%, %s, from(%s), to(%s));', $gtype[2], $colors[0], $colors[1]),
						'}'
					);
					break;
			}
			break;
		case 'skin':
		case 'upload':
			if($ds_rows['bg_type']=='skin') {
				$bg = $base_url.'design/site/bg_'.$ds_rows['site_bg_skin'].'.png';
				$repeat = 'no-repeat';
			}
			else {
				$bg = $base_url.'design/site/'.$ds_rows['site_bg'];
				$repeat = 'repeat';
			}
			$styles = $bg ? array('body {', sprintf('	background: url(%s) %s top center;', $bg, $repeat), '}') : array();
			break;
	}

	// 사이트 대표색(테마)
	$styles[] = fetch_skin(array('color'=> $ds_rows['site_theme_color']), implode("\n", array(
		'',
		'/*background 대표테마색*/',
		'.img_bgc{background-color:{:color:}}',
		'',
		'/*탭 bgcolor 대표테마색*/',
		'.prd_tabs li{background-color:{:color:}}',
		'.tab_choice{background-color:{:color:}!important}',
		'',
		'/*좌측메뉴 타이틀+라인 대표테마색*/',
		'.l_tit,.l_tit2{color:{:color:}}',
		'.text_menu{border-bottom:2px solid {:color:}}',
		'',
		'/*서브우측컨텐츠 타이틀+블릿+라인 대표테마색*/',
		'#con_top_box{border-bottom:1px solid {:color:}}',
		'.tit_bullet{background-color:{:color:};border: 1px solid {:color:};_background:{:color:}}',
		'',
		'/*서브 컨텐츠 타이틀+블릿색상 대표테마색*/',
		'.info_title{color:{:color:}}',
		'.bullet_img{background-color:{:color:}}',
		'',
		'/*메인 배경색 및 타이틀*/',
		'.col_basic{background-color:{:color:}}',
		'.tab_wrap li{color:{:color:}}',
		'.con_tit{color:{:color:}}',
		'',
		'/* 갤러리 ABCD형 사진 대표테마색*: background 변경 시 이미지사이즈 변경가능 */',
		'.pic_bgc{background-color:{:color:}}',
		'',
		'/*게시판 상단 배경색+타이틀명 대표테마색*/',
		'.t_bgcolor td{background-color:{:color:}}',
		'.subtitle{color:{:color:}}',
		'.Form_box {border:{:color:} 2px solid}',
		'.Form_top {border-top:{:color:} 2px solid}',
		'.Form_top2 {border-top:{:color:} 2px solid}'
	)));

	// 메인비쥬얼 하단 컨테이너
	include_once $base_dir.'Libs/_php/palette.class.php';
	$p = new palette($ds_rows['site_theme_color'], 20);
	$colors = array($p->color, $p->stops['+2']);
	$gtype = array(0, 'top', '0% 100%');
	$styles[] = implode("\n", array(
		'',
		'/* 메인비쥬얼 하단 컨테이너 색상 */',
		'#main_container {',
			sprintf('	filter: progid:DXImageTransform.Microsoft.Gradient(GradientType=%d, StartColorStr=%s, EndColorStr=%s);', $gtype[0], $colors[0], $colors[1]),
			sprintf('	background: -moz-linear-gradient(%s, %s, %s);', $gtype[1], $colors[0], $colors[1]),
			sprintf('	background: -webkit-gradient(linear, 0%% 0%%, %s, from(%s), to(%s));', $gtype[2], $colors[0], $colors[1]),
			sprintf('	background: -o-gradient(linear, 0%% 0%%, %s, from(%s), to(%s));', $gtype[2], $colors[0], $colors[1]),
		'}'
	));

	$fp = fopen($base_dir.'design/site/site.css', 'w');
	fwrite($fp, implode("\n", $styles));
	fclose($fp);
}



/**
 * 메인게시판 스타일 생성
 */
function make_board_tab_style() {
	global $design, $base_dir;
	$ds_rows = $design->get_settings('main_board_design');
	foreach(range(1, 3) as $num) { // 메인탭은 총3개로 구성 됨
		$tab = 'tab'.$num;
		$styles .= implode("\n", array(
			'#board_frame ul.tab_wrap li.'.$tab.' {',
				sprintf('	background-color: %s;', $ds_rows[$tab]['bg_offcolor']),
				sprintf('	color: %s;', $ds_rows[$tab]['text_offcolor']),
			'}',
			'#board_frame ul.tab_wrap li.'.$tab.':hover, #board_frame ul.tab_wrap li.'.$tab.'.selected {',
				sprintf('	background-color: %s;', $ds_rows[$tab]['bg_oncolor']),
				sprintf('	color: %s;', $ds_rows[$tab]['text_oncolor']),
			'}'
		));
	}
	$fp = fopen($base_dir.'design/main/tab.css', 'w');
	fwrite($fp, $styles);
	fclose($fp);
}



/**
 * GNB CSS 메뉴 스타일 생성
 */
function make_css_menu_style() {
	global $design, $base_dir, $base_url;
	$ds_rows = $design->get_settings('top_menu_design');
	switch($ds_rows['menu_bg_type']) {
		case 'image': // 메뉴BG 가 있는경우 메뉴사이트 만큼
			$attach = new attachment('menu_bg');
			$folder = $base_url.$attach->configs['save']['folder'];
			$ds_rows['on_menu_bg'] = sprintf('	background: url(%s%s) no-repeat 0 0;', $folder, $ds_rows['menu_bg']);
		case 'none':
			$mm_wrap = array(
				'	/* none */',
				'	border: 1px transparent solid;',
				'	background-color: transparent;'
			);
			break;
		case 'solid':
			$mm_wrap = array(
				'	/* solid */',
				'	border: 1px #000 solid;',
				sprintf('	background-color: %s;', $ds_rows['menu_bg_scolor'])
			);
			break;
		case 'gradient':
			$colors = array($ds_rows['menu_bg_gcolor1'], $ds_rows['menu_bg_gcolor2']);
			$gtype = ($ds_rows['menu_bg_gtype']=='height') ? array(0, 'top', '0% 100%') : array(1, 'left', '100% 0%');
			$mm_wrap = array(
				'	/* gradient */',
				'	border: 1px #000 solid;',
				sprintf('	filter: progid:DXImageTransform.Microsoft.Gradient(GradientType=%d, StartColorStr=%s, EndColorStr=%s);', $gtype[0], $colors[0], $colors[1]),
				sprintf('	background: -moz-linear-gradient(%s, %s, %s);', $gtype[1], $colors[0], $colors[1]),
				sprintf('	background: -webkit-gradient(linear, 0%% 0%%, %s, from(%s), to(%s));', $gtype[2], $colors[0], $colors[1]),
			);
			break;
	}
	$ds_rows['mm_wrap'] = implode("\n", $mm_wrap);

	switch($ds_rows['submenu_bg_type']) {
		case 'none':
			$sm_wrap = array(
				'	/* none */',
				'	border: 1px transparent solid;',
				'	background-color: transparent;'
			);
			break;
		case 'solid':
			$sm_wrap = array(
				'	/* solid */',
				'	border: 1px #000 solid;',
				sprintf('	background-color: %s;', $ds_rows['submenu_bg_scolor'])
			);
			break;
		case 'gradient':
			$colors = array($ds_rows['submenu_bg_gcolor1'], $ds_rows['submenu_bg_gcolor2']);
			$gtype = ($ds_rows['submenu_bg_gtype']=='height') ? array(0, 'top', '0% 100%') : array(1, 'left', '100% 0%');
			$sm_wrap = array(
				'	/* gradient */',
				'	border: 1px #000 solid;',
				sprintf('	filter: progid:DXImageTransform.Microsoft.Gradient(GradientType=%d, StartColorStr=%s, EndColorStr=%s);', $gtype[0], $colors[0], $colors[1]),
				sprintf('	background: -moz-linear-gradient(%s, %s, %s);', $gtype[1], $colors[0], $colors[1]),
				sprintf('	background: -webkit-gradient(linear, 0%% 0%%, %s, from(%s), to(%s));', $gtype[2], $colors[0], $colors[1]),
			);
			break;
	}
	$ds_rows['sm_wrap'] = implode("\n", $sm_wrap);

	$sm_margins = array();
	foreach($ds_rows['submenu_pos'] as $parent=>$margin) {
		array_push($sm_margins, sprintf('#css_gnb_frame ul.sm.s%d { margin-left: %dpx; }', $parent, $margin));
	}
	$ds_rows['sm_margins'] = implode("\n", $sm_margins);

	$styles = fetch_contents(array($ds_rows), array(
		'entry' => array(
			1 => implode("\n", array(
				'/* gnb frame */',
				'#css_gnb_frame {',
				'	width: {:container_width:}px;',
				'	height: {:container_height:}px;',
				'{:on_menu_bg:}',
				'}',
				'/* main-menu */',
				'#css_gnb_frame div.mm_wrap {',
				'{:mm_wrap:}',
				'}',
				'#css_gnb_frame ul.mm li {',
				'	margin-left: {:menu_item_space:}px;',
				'	color: {:menu_text_offcolor:};',
				'}',
				'#css_gnb_frame ul.mm li:hover, #css_gnb_frame ul.mm li.hover {',
				'	color: {:menu_text_oncolor:};',
				'}',
				'/* sub-menu */',
				'#css_gnb_frame ul.sm {',
				'{:sm_wrap:}',
				'}',
				'#css_gnb_frame ul.sm li {',
				'	margin-left: {:submenu_item_space:}px;',
				'	color: {:submenu_text_offcolor:};',
				'}',
				'#css_gnb_frame ul.sm li:hover, #css_gnb_frame ul.sm li.hover {',
				'	color: {:submenu_text_oncolor:};',
				'}',
				'/* sub-menu margin */',
				'{:sm_margins:}'
			))
		)
	));
	$fp = fopen($base_dir.'design/top/gnb.css', 'w');
	fwrite($fp, $styles);
	fclose($fp);
}



/**
 * LNB CSS 메뉴 스타일 생성
 */
function make_css_lnb_style() {
	global $design, $base_dir, $base_url;
	$ds_rows = $design->get_settings('left_menu_design');
	$styles = fetch_contents(array($ds_rows), array(
		'entry' => array(
			1 => implode("\n", array(
				'/* second-menu */',
				'.lmenu p.secon_m{border:5px solid {:sm_off_bordercolor:};background-color:{:sm_off_bgcolor:};color:{:sm_off_color:}}',
				'.lmenu p.hover{font-weight:bold;border:5px solid {:sm_on_bordercolor:};background-color:{:sm_on_bgcolor:};color:{:sm_on_color:}}',
				'/* third-menu */',
				'.third_m {background-color:{:tm_off_bgcolor:};color:{:tm_off_color:}}',
				'.third_m dd{border:2px solid {:tm_off_bordercolor:};font-size:11px}',
				'dd.hover{background:{:tm_on_bgcolor:};color:{:tm_on_color:};border:2px solid {:tm_on_bordercolor:}}'
			))
		)
	));
	$fp = fopen($base_dir.'design/left/lnb.css', 'w');
	fwrite($fp, $styles);
	fclose($fp);
}
?>