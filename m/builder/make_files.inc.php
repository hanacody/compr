<?php
/**
 * PIDS.inc 파일 생성
 */
function make_pids_data() {
	global $frame, $mobile;

	unlink($mobile->m_dir.'design/top/pids.inc');
	$pp = fopen($mobile->m_dir.'design/top/pids.inc', 'a');
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
	fwrite($pp, sprintf("\$pids[%d] = array('no' => %d, 'rank' => %d, 'depth'=> %d, 'parent' => '%s', 'base_name' => '%s', 'has_child' => '%s', 'access_level' => '%s', 'page_type' => '%s', 'module' => '%s', 'component' => '%s', 'options' => '%s', 'link' => '%s', 'url' => '%s', 'used' => '%s', 'use_gnb' => '%s');\n", $rows['no'], $rows['no'], $rank, $rows['depth'], $parent, addslashes($rows['base_name']), $rows['has_child'], $rows['access_level'], $rows['page_type'], $rows['module'], $rows['component'], str_replace("'", '"', $rows['options']), $rows['link'], $rows['url'], $rows['used'], $rows['use_gnb']));

	// 하위메뉴 처리
	if($rows['has_child']=='yes') {
		$_rank = 0;
		$depth = $rows['depth'] + 1;
		$parents = $rows['parents'] ? $rows['parents'].','.$rows['no'] : $rows['no'];
		$datas = $frame->query("select * from $frame->frame_table where parents='$parents' and depth=$depth order by bundle, position");
		while($_rows = $frame->fetch($datas)) write_pids($pp, $_rows, ++$_rank);
	}
}


/**
 * 모바일 메인배경 스타일 생성
 */
function make_main_bg_style($ds_rows='') {
	global $mobile, $design;
	if(!$ds_rows) $ds_rows = $design->get_settings('main_design');
	switch($ds_rows['bg_type']) {
		case 'none': $styles = array(); break;
		case 'color':
			switch($ds_rows['main_bg_type']) {
				case 'solid':
					$styles = array('body {', sprintf('	background: %s;', $ds_rows['main_bg_scolor']), '}');
					break;
				case 'gradient':
					$colors = array($ds_rows['main_bg_gcolor1'], $ds_rows['main_bg_gcolor2']);
					$gtype = ($ds_rows['main_bg_gtype']=='height') ? array(0, 'top', '0% 100%') : array(1, 'left', '100% 0%');
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
			if($ds_rows['bg_type']=='skin') $bg = $mobile->m_domain.'design/site/bg_'.$ds_rows['main_bg_skin'].'.gif';
			else $bg = $mobile->m_domain.'design/main/'.$ds_rows['main_bg'];
			$styles = $bg ? array('body {', sprintf('	background: url(%s) no-repeat center bottom;', $bg), '}') : array();
			break;
	}
	// 퀵메뉴 아이콘 간격
	$quick_styles = array('', '.qn li {', sprintf('	padding-top: %dpx;', $ds_rows['row_interval']), sprintf('	padding-left: %dpx;', $ds_rows['col_interval']), '}');

	$fp = fopen($mobile->m_dir.'design/main/main.css', 'w');
	fwrite($fp, implode("\n", $styles).implode("\n", $quick_styles));
	fclose($fp);
}


/**
 * 모바일 사이트배경 스타일 생성
 */
function make_site_bg_style($ds_rows='') {
	global $mobile, $design;
	if(!$ds_rows) $ds_rows = $design->get_settings('site_design');
	switch($ds_rows['bg_type']) {
		case 'none': $styles = array(); break;
		case 'color':
			switch($ds_rows['site_bg_type']) {
				case 'solid':
					$styles = array('body {', sprintf('	background: %s;', $ds_rows['site_bg_scolor']), '}');
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
			if($ds_rows['bg_type']=='skin') $bg = $mobile->m_domain.'design/site/bg_'.$ds_rows['site_bg_skin'].'.gif';
			else $bg = $mobile->m_domain.'design/site/'.$ds_rows['site_bg'];
			$styles = $bg ? array('body {', sprintf('	background: url(%s) center bottom;', $bg), '}') : array();
			break;
	}
	$fp = fopen($mobile->m_dir.'design/site/site.css', 'w');
	fwrite($fp, implode("\n", $styles));
	fclose($fp);
}


/**
 * GNB 스타일 생성
 */
function make_css_gnb_style($ds_rows='') {
	global $mobile, $design;
	if(!$ds_rows) $ds_rows = $design->get_settings('site_design');
	$off = new palette($ds_rows['menu_off_bgcolor']);
	$on = new palette($ds_rows['menu_on_bgcolor']);
	$ic = new palette($ds_rows['menu_on_bgcolor'], 20); // icon
	$gc = new palette($ds_rows['menu_on_bgcolor'], 5); // gallery
	$styles = fetch_contents(array($ds_rows), array(
		'entry' => array(
			1 => implode("\n", array(
				'/* nav bgcolor */',
				'.nav {background-color:{:nav_bgcolor:};border-bottom:solid 1px {:frame_color:}}',
				'',
				'/* menu_wrap bgcolor */',
				'#gnb{border-bottom:solid 1px {:frame_color:}}',
				'.gbm .gbi{border-top:solid 1px {:frame_color:}}',
				'.gbf .gbi,.gbm .gbi{height:{:menu_height:}px;line-height:{:menu_height:}px;border-right:solid 1px {:frame_color:}}',
				'',
				'/* menu on/off color */',
				'.gbf .gbi,.gbm .gbi,.flip{',
					sprintf('	background:%s;color:{:menu_off_color:};', $off->color),
					sprintf('	background:-webkit-gradient(linear,0 0,0 100%%,from(%s),to(%s));', $off->stops['+1'], $off->stops['-1']),
					sprintf('	-webkit-box-shadow:inset 1px 1px %s,inset -1px -1px %s;', $off->stops['+2'], $off->stops['-2']),
					sprintf('	-moz-box-shadow:inset 1px 1px %s,inset -1px -1px %s;', $off->stops['+2'], $off->stops['-2']),
				'}',
				'.gbf .gbi.on,.gbm .gbi.on{',
					sprintf('	background:%s;color:{:menu_on_color:};', $on->color),
					sprintf('	background:-webkit-gradient(linear,0 0,0 100%%,from(%s),to(%s));', $on->stops['+1'], $on->stops['-1']),
					sprintf('	-webkit-box-shadow:inset 1px 1px %s,inset -1px -1px %s;', $on->stops['+2'], $on->stops['-2']),
					sprintf('	-moz-box-shadow:inset 1px 1px %s,inset -1px -1px %s;', $on->stops['+2'], $on->stops['-2']),
				'}',
				'/* common frame color set */',
				sprintf('.box th {background:%s;}', $off->stops['-2']),
				sprintf('.paging .cur {color:%s;}', $off->stops['-2']),
				sprintf('span.ic1 {background:%s;border:1px solid %s}', $ic->stops['+2'], $ic->stops['+1']), /* span icon1 */
				sprintf('a.submit {background-color:%s}', $off->stops['+2']),
				'/* gallery title-bar */',
				'.gc .gt {',
				sprintf('	background-color:%s;', $gc->color),
				sprintf('	background:-webkit-gradient(linear,0 0,0 100%%,from(%s),to(%s));', $gc->stops['+1'], $gc->stops['-1']),
				sprintf('	-webkit-box-shadow:inset 1px 1px %s,inset -1px -1px %s', $gc->stops['+2'], $gc->stops['-2']),
				sprintf('	-moz-box-shadow:inset 1px 1px %s,inset -1px -1px %s', $gc->stops['+2'], $gc->stops['-2']),
				'}'
			))
		)
	));
	// 메뉴 텍스트 이미지 적용
	if($ds_rows['menu_type']=='image') {
		global $base_dir, $attach;
		$_styles = array('');
		$folder = $attach->configs['save']['folder'];
		foreach($ds_rows['gnb_texts'] as $pid=>$file_name) {
			$file = $base_dir.$folder.$file_name;
			if(!is_file($file)) continue;
			list($width, $height) = getimagesize($file);
			if($height>$ds_rows['menu_height']) $height = 20; // default height
			$mtop = ($ds_rows['menu_height'] - $height) / 2;
			$_styles[] = sprintf('.gbi%d span {background:url(%s) no-repeat 0 0;height:%dpx;margin-top:%dpx;vertical-align:top;text-indent:-99em}', $pid, $file_name, $height, $mtop);
		}
		$_styles = implode("\n", $_styles);
	}
	$fp = fopen($mobile->m_dir.'design/top/frame.css', 'w');
	fwrite($fp, $styles.$_styles);
	fclose($fp);
}

?>
