<?php

// 갤러리 플래시용 XML 데이터 생성
function make_gallery_flash_data($no) {
	global $gallery, $base_dir;
	if(!$no) return;
	$entry = array(
		'entry' => array(
			1 => array('
			<root>
				<containerWidth>726</containerWidth>
				<containerHeight>500</containerHeight>
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
	$rows = $gallery->get_gallery($no);
	$nodes = fetch_contents(array($rows['settings']), $entry, '_gf');

	// EUC-KR 인코딩 처리
	if(rankup_util::check_unicode($nodes)) {
		$nodes = rankup_util::euckr($nodes);
	}

	// make file
	$fp = fopen($base_dir.'design/xml/gallery_'.$no.'.xml', 'w');
	fwrite($fp, '<?xml version="1.0" encoding="euc-kr" ?>'.$nodes);
	fclose($fp);
}
function _gf($bind) {
	global $base_dir, $base_url;
	extract($bind);
	// 갤러리 이미지
	$attach = new attachment('gallery');
	if($rows['attach']) {
		$folder = $attach->configs['save']['folder'];
		$images = array();
		foreach(explode(',', $rows['attach']) as $file_name) {
			if(is_file($base_dir.$folder.$file_name)) {
				array_push($images, fetch_skin(compact('folder', 'file_name'), $on_images));
			}
		}
		$rows['images'] = implode('', $images);
		if($rows['image_list_use']=='yes' && $rows['images']) {
			$rows['imageList'] = fetch_skin($rows, $on_imageList);
		}
	}
	// 문구색상
	foreach(array('top_text_color', 'middle_text_color', 'bottom_text_color') as $key) {
		$rows[$key] = str_replace('#', '0x', $rows[$key]);
	}
	// 문구전체 배경 이미지
	$folder = $attach->presets['gtext_container_bg']['save']['folder'];
	$file_name = $rows['text_container_bg'];
	if($file_name && is_file($base_dir.$folder.$file_name)) {
		$rows['bgImage'] = fetch_skin(compact('folder', 'file_name'), $on_bgImage);
	}
	return array($rows, $skin);
}
?>