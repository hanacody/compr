<?php
## 랭크업 멀티게시판 관리자 클래스
class rankup_board_admin extends rankup_board {
	var $version = "v2.1 r090623"; // 게시판 개발 버전
	function rankup_board_admin($board_id='') {
		parent::rankup_board($board_id);
	}

	// 워터마크 설정
	function set_wm_settings($datas) {
		unset($datas['x'], $datas['y'], $datas['mode']);
		if($this->chkRes($datas)) {
			$_val['item_value'] = serialize($datas);
			$values = $this->change_query_string($_val);
			if(!isset($this->wm_settings['use_watermark'])) $this->query("insert $this->setting_table set item_name='thumb_configs', $values");
			else $this->query("update $this->setting_table set $values where item_name='thumb_configs'");
			// 워터마크 변경
			if($datas['on_watermark']) {
				foreach(glob($this->base_dir.'rankup_module/rankup_board/watermark/watermark.*') as $old_wmark) unlink($old_wmark);
				$new_wmark = $this->base_dir.'rankup_module/rankup_board/watermark/'.$datas['on_watermark'];
				rename($new_wmark, str_replace('_junk_.', '', $new_wmark));
			}
		}
		return true;
	}

	// 게시판 생성
	function create_board($board_id) {
		if(empty($board_id)) return false;
		// 게시판/댓글 테이블 생성
		@include "scheme/rankup_board_scheme.inc.html";
		foreach($_BOARD_TABLES as $scheme_name=>$create_query) {
			$table_name = str_replace("scheme", $board_id, $scheme_name);
			$check_table = $this->queryR("show tables like '$table_name'");
			if($check_table!==$table_name) $this->query(str_replace("{:board_id:}", $board_id, $create_query));
		}
		// 테이블 분할 정보 추가
		$check_board = $this->queryR("select bid from $this->division_table where bid='$board_id'");
		if($check_board!==$board_id) $this->query("insert $this->division_table set bid='$board_id'");

		// 첨부파일을 저장할 폴더 생성
		$path_info = pathinfo(__FILE__);
		$attach_dir = $path_info['dirname']."/attach/".$board_id;
		if(!is_dir($attach_dir)) {
			mkdir($attach_dir);
			@chmod($attach_dir, 0777);
		}
		return true;
	}

	// 게시판 아이디 중복 체크
	function verify_board($board_id) {
		$result = $this->queryR("show tables like '$this->board_prefix$board_id'");
		if($board_id!='best' && empty($result)) { // 2009.08.03 fixed
			$script_code = "
			$('board_id').value = '$board_id';
			alert('\'$board_id\' 게시판 아이디는 사용하실 수 있습니다.'+SPACE);
			var board_name = $('board_name');
			board_name.select();
			board_name.focus();";
		}
		else {
			$script_code = "
			$('board_id').value = '';
			alert('입력하신 게시판 아이디는 이미 사용중 이거나 다른 테이블명과 중복되어 사용하실 수 없습니다.'+SPACE);
			var boardId = $('boardId');
			boardId.select();
			boardId.focus();";
		}
		return $script_code;
	}

	// 게시판 삭제
	function delete_board($board_id, $cno='') {
		if(empty($board_id)) return false;
		// 게시판/댓글 테이블 스키마
		@include "scheme/rankup_board_scheme.inc.html";
		foreach($_BOARD_TABLES as $scheme_name=>$create_query) {
			$table_name = str_replace("scheme", $board_id, $scheme_name);
			//테이블의 존재 유무 -- 없으면 스킵 한다. 때때로 아래 정보가 남아 있는 경우가 발생한다.
			$table_exists = $this->queryR("show tables like '$table_name'");
			if($table_exists) $this->query("drop table $table_name");
		}
		// 테이블 분할 정보 삭제
		$this->query("delete from $this->division_table where bid='$board_id'");
		if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->division_table");

		// 환경 테이블에서 게시판 정보 삭제
		$previous_board_no = $this->queryR("select no from $this->bconfig_table where id='$board_id'");
		$this->query("delete from $this->bconfig_table where id='$board_id'");
		if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->bconfig_table");

		// 카테고리 bval, mbno 값 갱신
		if(!empty($cno)) {
			// 해당 카테고리에 게시판이 존재하지 않을 경우를 처리
			$board_no = $this->queryR("select no from $this->bconfig_table where cno=$cno order by rank");
			if(empty($board_no)) $this->query("update $this->category_table set bval='no', mbno=NULL where no=$cno");
			else $this->query("update $this->category_table set mbno=$board_no where no=$cno and mbno=$previous_board_no"); // mbno 변경
		}

		// 주간 베스트 삭제
		$this->query("delete from $this->weekly_best_table where bid='$board_id'");
		if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->weekly_best_table");
		// 조회수 베스트 삭제
		$this->query("delete from $this->hit_best_table where bid='$board_id'");
		if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->hit_best_table");
		// 댓글수 베스트 삭제
		$this->query("delete from $this->comment_best_table where bid='$board_id'");
		if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->comment_best_table");
		if($this->board_extension===true) {
			// 주간 댓글수 베스트 삭제
			$this->query("delete from $this->weekly_cbest_table where bid='$board_id'");
			if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->weekly_cbest_table");
			// 신고글 관리 에서 삭제
			$this->query("delete from $this->report_table where bid='$board_id'");
			if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->report_table");
		}
		// 신규 게시물 삭제
		$this->query("delete from $this->new_article_table where bid='$board_id'");
		if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->new_article_table");

		// 첨부파일 삭제
		$path_info = pathinfo(__FILE__);
		$attach_dir = $path_info['dirname']."/attach/".$board_id;
		$this->remove_directory($attach_dir); // rankup_util.class.php 에 정의
		return true;
	}

	// 게시판 정보 저장
	function regist_board($datas) {
		$DML = $datas['no']==='' ? "insert" : "update"; // 2009.08.31 modified

		$_val['name'] = str_replace("\"", "&quot;", $datas['board_name']); // 게시판 이름
		$_val['skin'] = $datas['board_skin'];							// 스킨 폴더 이름
		$_val['style'] = $datas['board_style'];						// 게시판 스타일 - 게시판 or 갤러리

		// 게시판 미사용시 메인페이지/메뉴메인에도 함께 반영
		if($datas['board_use']=="on") $_val['uval'] = "yes";
		else $_val['uval'] = $_val['mval'] = $_val['pcmval'] = "no";

		$_val['slayout'] = serialize(array(
			"board_width" => $datas['board_width'],				// 게시판 가로크기
			"subject_length" => $datas['subject_length'],		// 목록 제목글자 제한
			"use_condense" => $datas['use_condense'],			// 글 줄임기호 사용
			"article_rows" => $datas['article_rows']				// 페이지당 게시물 수
		));
		$_val['scontent'] = $this->wysiwyg_result_func(stripslashes($datas['board_content']));	// 게시물 기본 내용
		$_val['sfunction'] = serialize(array(
			"use_category" => $datas['use_category'],			// 분류 기능
			"use_duplicate_hit" => $datas['use_duplicate_hit'],	// 중복조회 기능
			"use_comment" => $datas['use_comment'],			// 댓글 기능
			"use_reply" => $datas['use_reply'],						// 답글 기능
			"use_vote" => $datas['use_vote'],						// 추천/반대 기능
			"use_only_good" => $datas['use_only_good'],		// 추천기능만 사용
			"use_report" => $datas['use_report'],					// 신고 기능
			"use_secret" => $datas['use_secret'],					// 비밀글 기능
			"use_print" => $datas['use_print'],					// 인쇄 기능
			"use_writer" => $datas['use_writer'],				// 글작성자/닉네임/아이디 설정
			"use_snssend" => $datas['use_snssend'],			// sns 글보내기 사용여부
			"use_articledel" => $datas['use_articledel'],		// 게시물바로삭제
			"use_watermark" => $datas['use_watermark'],		// 워터마크 사용여부
			"sheader_file" => $datas['board_header_file'], //게시판 상단에 출력할 파일
			"sfooter_file" => $datas['board_footer_file'] // 게시판 하단에 출력할 파일
		));
		$_val['soption'] = serialize(array(
			"use_hit_best" => $datas['use_hit_best'],				// 조회수 BEST 출력
			"hit_best_num" => $datas['hit_best_num'],			// 조회수 BEST 출력 갯수 - 2009.08.31 added
			"use_new_icon" => $datas['use_new_icon'],			// new 아이콘 출력
			"recent_time" => $datas['recent_time'],				// 최근 게시물로 설정할 시간
			"use_attach_icon" => $datas['use_attach_icon'],	// 첨부파일 아이콘 출력
			"use_reply_icon" => $datas['use_reply_icon'],		// 답글 아이콘 출력
			"use_near_article" => $datas['use_near_article'],	// 이전글/다음글 출력
			"use_detail_list" => $datas['use_detail_list'],			// 상세페이지 목록 출력
		));
		$_val['sattach'] = serialize(array(
			"use_attach" => $datas['use_attach'],					// 첨부파일 사용
			"use_detail_attach" => $datas['use_detail_attach'],	// 첨부파일 출력
			"attach_nums" => $datas['attach_nums'],				// 첨부파일 갯수
			"attach_size" => $datas['attach_size'],				// 첨부파일 최대 크기
			"attach_extension" => $datas['attach_extension']	// 첨부파일 확장자
		));
		$_val['sgallery'] = serialize(array(
			"thumb_width" => $datas['thumb_width'],				// 목록 최대 가로크기
			"thumb_height" => $datas['thumb_height'],			// 목록 최대 세로크기
			"picture_width" => $datas['picture_width'],			// 이미지 최대 가로크기
			"thumb_nums" => $datas['thumb_nums']				// 줄당 이미지 수
		));
		$_val['sfilter'] = $datas['board_filter'];						// 단어 필터
		$_val['sblock'] = $datas['ip_block'];							// 아이피블럭

		switch(strtolower($DML)) {
			// 신규 게시판 생성
			case "insert":
				$_val['id'] = $datas['board_id'];						// 게시판 아이디
				$_val['cno'] = $datas['cno'];							// 카테고리 번호
				$_val['pcno'] = empty($datas['pcno']) ? $datas['cno'] : $datas['pcno']; // 상위 카테고리 번호
				$_val['rank'] = $datas['rank'];							// 순위

				$_val['scategory'] = serialize(array());				// 게시판 카테고리 기본값 입력
				$_val['spermission'] = serialize(array(				// 게시판 권한 기본값 입력
					"list_level" => 7,										// 리스트 접근 권한 : (7: 비회원)
					"read_level" => 7,										// 게시물 읽기 권한 : (7: 비회원)
					"write_level" => 5,									// 게시물 쓰기 권한 : (5: 회원)
					"comment_level" => 5,								// 코멘트 쓰기 권한 : (5: 회원)
					"reply_level" => 5,									// 답변글 쓰기 권한 : (5: 회원)
					"delete_level" => 1,									// 게시물 삭제 권한 : (1: 운영자)
					"notice_level" => 1,									// 공지글 쓰기 권한 : (1: 운영자)
					"secret_level" => 1									// 비밀글 읽기 권한 : (1: 운영자)
				));
				$_val['spoint'] = serialize(array());					// 게시판 포인트 기본값 입력

				$_val['smlayout'] = serialize(array(					// 메인페이지 기본값 입력
					"subject_length" => 40,								// 제목길이
					"article_rows" => 5,									// 게시물수
					"image_width" => '',									// 이미지 가로크기
					"image_height" => '',									// 이미지 세로크기
					"print_style" => "text"								// 출력형태
				));
				$_val['spcmlayout'] = serialize(array(				// 메뉴메인페이지 기본값 입력
					"subject_length" => 40,								// 제목길이
					"article_rows" => 5,									// 게시물수
					"image_width" => '',									// 이미지 가로크기
					"image_height" => '',									// 이미지 세로크기
					"print_style" => "text"								// 출력형태
				));

				// 생성한 게시판을 관리테이블에 등록
				$this->create_board($datas['board_id']);
				break;

			// 게시판 정보 업데이트 - 2009.10.06 fixed
			case "update":
				if($datas['no']) $addWhere = " where no=$datas[no]";
				else if($datas['board_id']) $addWhere = " where id='$datas[board_id]'";
				break;
		}
		$values = $this->change_query_string($_val);
		$result = $this->query("$DML $this->bconfig_table set $values$addWhere");
		if($DML=="insert" && mysql_affected_rows()) {
			$board_no = mysql_insert_id();
			$this->query("update $this->category_table set bval='yes' where no=$datas[cno]"); // 게시판 존재한다고 기록
			$this->query("update $this->category_table set mbno=$board_no where no=$_val[pcno] and mbno is NULL"); // 메인게시판 설정
		}
		return $result;
	}

	// 형태별 게시판 설정사항 수정
	function board_type_config_setting($datas) {

		// 게시판 미사용시 메인페이지/메뉴메인에도 함께 반영
		if($datas['board_use']=="on") $_val['uval'] = "yes";
		else $_val['uval'] = $_val['mval'] = $_val['pcmval'] = "no";

		$_val['slayout'] = serialize(array(
			"board_width" => $datas['board_width'],				// 게시판 가로크기
			"subject_length" => $datas['subject_length'],		// 목록 제목글자 제한
			"use_condense" => $datas['use_condense'],			// 글 줄임기호 사용
			"article_rows" => $datas['article_rows']				// 페이지당 게시물 수
		));
		$_val['scontent'] = $this->wysiwyg_result_func(stripslashes($datas['board_content'])); // 게시물 기본 내용
		$_val['sfunction'] = serialize(array(
			"use_category" => $datas['use_category'],			// 분류 기능
			"use_duplicate_hit" => $datas['use_duplicate_hit'],	// 중복조회 기능
			"use_comment" => $datas['use_comment'],			// 댓글 기능
			"use_reply" => $datas['use_reply'],						// 답글 기능
			"use_vote" => $datas['use_vote'],						// 추천/반대 기능
			"use_only_good" => $datas['use_only_good'],		// 추천기능만 사용
			"use_report" => $datas['use_report'],					// 신고 기능
			"use_secret" => $datas['use_secret'],					// 비밀글 기능
			"use_print" => $datas['use_print'],					// 인쇄 기능
			"use_writer" => $datas['use_writer'],				// 글작성자/닉네임/아이디 설정
			"use_snssend" => $datas['use_snssend'],			// sns 글보내기 사용여부
			"use_articledel" => $datas['use_articledel'],		// 게시물바로삭제
			"use_watermark" => $datas['use_watermark'],		// 워터마크 사용여부
			"sheader_file" => $datas['board_header_file'], //게시판 상단에 출력할 파일
			"sfooter_file" => $datas['board_footer_file'] // 게시판 하단에 출력할 파일
		));
		$_val['soption'] = serialize(array(
			"use_hit_best" => $datas['use_hit_best'],				// 조회수 BEST 출력
			"hit_best_num" => $datas['hit_best_num'],			// 조회수 BEST 출력 갯수 - 2009.08.31 added
			"use_new_icon" => $datas['use_new_icon'],			// new 아이콘 출력
			"recent_time" => $datas['recent_time'],				// 최근 게시물로 설정할 시간
			"use_attach_icon" => $datas['use_attach_icon'],	// 첨부파일 아이콘 출력
			"use_reply_icon" => $datas['use_reply_icon'],		// 답글 아이콘 출력
			"use_near_article" => $datas['use_near_article'],	// 이전글/다음글 출력
			"use_detail_list" => $datas['use_detail_list'],			// 상세페이지 목록 출력
		));
		$_val['sattach'] = serialize(array(
			"use_attach" => $datas['use_attach'],					// 첨부파일 사용
			"use_detail_attach" => $datas['use_detail_attach'],	// 첨부파일 출력
			"attach_nums" => $datas['attach_nums'],				// 첨부파일 갯수
			"attach_size" => $datas['attach_size'],				// 첨부파일 최대 크기
			"attach_extension" => $datas['attach_extension']	// 첨부파일 확장자
		));
		$_val['sgallery'] = serialize(array(
			"thumb_width" => $datas['thumb_width'],				// 목록 최대 가로크기
			"thumb_height" => $datas['thumb_height'],			// 목록 최대 세로크기
			"picture_width" => $datas['picture_width'],			// 이미지 최대 가로크기
			"thumb_nums" => $datas['thumb_nums']				// 줄당 이미지 수
		));
		$_val['sfilter'] = $datas['board_filter'];						// 단어 필터
		$_val['sblock'] = $datas['ip_block'];							// 아이피블럭
		$addWhere = " where style='$datas[board_style]'"; //타입별로만 수정
		$values = $this->change_query_string($_val);
		$result = $this->query("update $this->bconfig_table set $values$addWhere");
		return $result;
	}

	// 게시판 데이터 XML 형태로 반환
	function formalize_board_xml_data($datas, $smpoint=null) {
		// 회원가입/로그인 포인트
		if($smpoint!==null) $smpoint = @unserialize($smpoint);

		// 게시판별 포인트 설정
		foreach($datas as $rows) {
			$slayout = @unserialize($rows['slayout']);
			$sfunction = @unserialize($rows['sfunction']);
			$soption = @unserialize($rows['soption']);

			unset($scategories);
			$categories = @unserialize($rows['scategory']);
			if($this->check_resource($categories)) { // 2009.09.18 modified
				$scategory = $this->sort_scategory($categories);
				foreach($scategory as $rank=>$vals) $scategories .= "<category no='$vals[cno]' anum='$vals[anum]'><![CDATA[{$vals[name]}]]></category>";
			}

			$spermission = @unserialize($rows['spermission']);
			$spoint = @unserialize($rows['spoint']);

			$sattach = @unserialize($rows['sattach']);
			$sgallery = @unserialize($rows['sgallery']);

			$xml_data .= "
			<item no='$rows[no]'>
				<!-- basic -->
				<board_id>$rows[id]</board_id>
				<board_name><![CDATA[{$rows[name]}]]></board_name>
				<cno>$rows[cno]</cno>
				<pcno>$rows[pcno]</pcno>
				<anum>$rows[anum]</anum>
				<rank>$rows[rank]</rank>
				<board_skin>$rows[skin]</board_skin>
				<board_style>$rows[style]</board_style>
				<board_use>$rows[uval]</board_use>
				<!-- layout -->
				<board_width>$slayout[board_width]</board_width>
				<subject_length>$slayout[subject_length]</subject_length>
				<use_condense>$slayout[use_condense]</use_condense>
				<article_rows>$slayout[article_rows]</article_rows>
				<board_content><![CDATA[{$rows[scontent]}]]></board_content>
				<!-- function -->
				<use_category>$sfunction[use_category]</use_category>
				<use_duplicate_hit>$sfunction[use_duplicate_hit]</use_duplicate_hit>
				<use_comment>$sfunction[use_comment]</use_comment>
				<use_reply>$sfunction[use_reply]</use_reply>
				<use_vote>$sfunction[use_vote]</use_vote>
				<use_only_good>$sfunction[use_only_good]</use_only_good>
				<use_report>$sfunction[use_report]</use_report>
				<use_secret>$sfunction[use_secret]</use_secret>
				<use_print>$sfunction[use_print]</use_print>
				<use_writer>$sfunction[use_writer]</use_writer>
				<use_snssend>$sfunction[use_snssend]</use_snssend>
				<use_articledel>$sfunction[use_articledel]</use_articledel>
				<use_watermark>$sfunction[use_watermark]</use_watermark>
				<board_header_file><![CDATA[{$sfunction[sheader_file]}]]></board_header_file>
				<board_footer_file><![CDATA[{$sfunction[sfooter_file]}]]></board_footer_file>
				<!-- option -->
				<use_hit_best>$soption[use_hit_best]</use_hit_best>
				<hit_best_num>$soption[hit_best_num]</hit_best_num>
				<use_new_icon>$soption[use_new_icon]</use_new_icon>
				<recent_time>$soption[recent_time]</recent_time>
				<use_attach_icon>$soption[use_attach_icon]</use_attach_icon>
				<use_reply_icon>$soption[use_reply_icon]</use_reply_icon>
				<use_near_article>$soption[use_near_article]</use_near_article>
				<use_detail_list>$soption[use_detail_list]</use_detail_list>
				<!-- category -->
				<categories>
					$scategories
				</categories>
				<!-- permission -->
				<permission>
					<list_level>$spermission[list_level]</list_level>
					<read_level>$spermission[read_level]</read_level>
					<write_level>$spermission[write_level]</write_level>
					<delete_level>$spermission[delete_level]</delete_level>
					<comment_level>$spermission[comment_level]</comment_level>
					<reply_level>$spermission[reply_level]</reply_level>
					<notice_level>$spermission[notice_level]</notice_level>
					<secret_level>$spermission[secret_level]</secret_level>
				</permission>
				<!-- point -->
				<point>
					<join_point><![CDATA[{$smpoint[join_point]}]]></join_point>
					<login_point><![CDATA[{$smpoint[login_point]}]]></login_point>
					<write_point><![CDATA[{$spoint[write_point]}]]></write_point>
					<read_point><![CDATA[{$spoint[read_point]}]]></read_point>
					<comment_point><![CDATA[{$spoint[comment_point]}]]></comment_point>
					<reply_point><![CDATA[{$spoint[reply_point]}]]></reply_point>
					<vote_point><![CDATA[{$spoint[vote_point]}]]></vote_point>
					<upload_point><![CDATA[{$spoint[upload_point]}]]></upload_point>
					<download_point><![CDATA[{$spoint[download_point]}]]></download_point>
				</point>
				<!-- attach -->
				<use_attach>$sattach[use_attach]</use_attach>
				<use_detail_attach>$sattach[use_detail_attach]</use_detail_attach>
				<attach_nums>$sattach[attach_nums]</attach_nums>
				<attach_size>$sattach[attach_size]</attach_size>
				<attach_extension>$sattach[attach_extension]</attach_extension>
				<!-- gallery -->
				<thumb_width>$sgallery[thumb_width]</thumb_width>
				<thumb_height>$sgallery[thumb_height]</thumb_height>
				<picture_width>$sgallery[picture_width]</picture_width>
				<thumb_nums>$sgallery[thumb_nums]</thumb_nums>
				<!-- etc -->
				<board_filter><![CDATA[{$rows[sfilter]}]]></board_filter>
				<ip_block><![CDATA[{$rows[sblock]}]]></ip_block>
			</item>";
		}
		$xml_data = "
		<board>$xml_data
		</board>";
		return $xml_data;
	}

	// 게시판 리스트 추출
	function get_board($cno, $xml=true) {
		$smpoint = $this->board_extension===true ? $this->queryR("select smpoint from $this->sconfig_table") : null;
		$datas = $this->queryFetchRows("select * from $this->bconfig_table where cno=$cno order by rank");
		return $xml===true ? $this->formalize_board_xml_data($datas, $smpoint) : $datas;
	}

	// 게시판 설정값 XML 형태로 반환
	function formalize_setting_xml_data($board_datas, $main_datas=null) {
		foreach($board_datas as $rows) {
			$smlayout = unserialize($rows['smlayout']);
			$spcmlayout = unserialize($rows['spcmlayout']);
			$xml_board_item .= "
			<item no='$rows[no]' anum='$rows[anum]'>
				<board_id>$rows[id]</board_id>
				<board_name><![CDATA[".str_replace("&", "&amp;", $rows['name'])."]]></board_name>
				<cno>$rows[cno]</cno>
				<pcno>$rows[pcno]</pcno>
				<uval>$rows[uval]</uval>
				<mval>$rows[mval]</mval>
				<pcmval>$rows[pcmval]</pcmval>
				<smlayout>
					<subject_length>$smlayout[subject_length]</subject_length>
					<article_rows>$smlayout[article_rows]</article_rows>
					<print_style>$smlayout[print_style]</print_style>
					<image_width>$smlayout[image_width]</image_width>
					<image_height>$smlayout[image_height]</image_height>
				</smlayout>
				<spcmlayout>
					<subject_length>$spcmlayout[subject_length]</subject_length>
					<article_rows>$spcmlayout[article_rows]</article_rows>
					<print_style>$spcmlayout[print_style]</print_style>
					<image_width>$spcmlayout[image_width]</image_width>
					<image_height>$spcmlayout[image_height]</image_height>
				</spcmlayout>
			</item>";
		}
		if($main_datas!=null) { // 2009.08.28 modified
			$sprint = $main_datas['sprint'];
			$xml_main_item = "
			<main no='$main_datas[no]'>
				<mboard no='$main_datas[mbno]'>".str_replace("&", "&amp;", $main_datas['mbname'])."</mboard>
				<lskin>$main_datas[lskin]</lskin>
				<mskin>$main_datas[mskin]</mskin>
				<mbnum>$main_datas[mbnum]</mbnum>
				<mval>$main_datas[mval]</mval>
				<wbest>$sprint[wbest]</wbest>
				<hcbest>$sprint[hcbest]</hcbest>
				<narticle>$sprint[narticle]</narticle>
				<wbest_num>$sprint[wbest_num]</wbest_num>
				<hcbest_num>$sprint[hcbest_num]</hcbest_num>
				<narticle_num>$sprint[narticle_num]</narticle_num>
			</main>";
		}
		$xml_data = "
		<setting>$xml_main_item$xml_board_item
		</setting>";
		return $xml_data;
	}


##########################################################################
## 카테고리 부분 : 인덱스 : 카테고리, category
##########################################################################

	// 메뉴(카테고리) 입력 - 메뉴 생성/수정시 사용
	function regist_category($datas) {
		$DML = in_array($datas['no'], array('', "undefined")) ? "insert" : "update";
		switch($DML) {
			case "insert":
				$_val['pno'] = $datas['pno'];
				$_val['mskin'] = $this->get_board_skins("main", "gray"); // 특정 색상의 스킨을 하나만 받아옴
				$_val['lskin'] = $this->get_board_skins("left", "gray"); // 특정 색상의 스킨을 하나만 받아옴
				// 2009.08.28 added
				$_val['mbnum'] = 2;					// 한줄에 출력할 게시판 수
				$_val['sprint'] = serialize(array(
					"wbest" => 'yes',					// 이번주 베스트
					"hcbest" => 'yes',					// 조회수/댓글수 베스트
					"narticle" => 'yes',				// 신규 게시물
					"wbest_num" => 5,				// 이번주 베스트
					"hcbest_num" => 5,				// 조회수/댓글수 베스트
					"narticle_num" => 5				// 신규 게시물
				));
				if(!empty($datas['pno']) && $datas['cval']=="no") $this->update_category(array("cval"=>"yes"), $datas['pno']);
				break;
			case "update":
				$addWhere = " where no=$datas[no]";
				break;
		}
		$_val['content'] = str_replace("\"", "&quot;", stripslashes($datas['content']));
		$values = $this->change_query_string($_val);
		$this->query("$DML $this->category_table set $values$addWhere");
		return $DML=="insert" ? mysql_insert_id() : $datas['no'];
	}

	// 메뉴(카테고리) 설정 업데이트 - 메인게시판 출력 설정시 사용
	function update_category($datas, $no) {
		$values = $this->change_query_string($datas);
		return $this->query("update $this->category_table set $values where no=$no");
	}

	// 메뉴(카테고리) 재설정
	function reset_category() {
		$nos = $this->getParam("no");
		$views = $this->getParam("view");
		for($i=0; $i<count($nos); $i++) {
			$no = $nos[$i];
			$_val['rank'] = $i+1;
			$_val['pval'] = ($views[$no]=="on") ? "yes" : "no";
			$values = $this->change_query_string($_val);
			$result[$i] = $this->query("update $this->category_table set $values where no=$no");
			// 카테고리 사용여부에 따른 메인게시판/메뉴페이지 출력여부 초기화 - 2009.07.22 fixed
			if($_val['pval']=='no') $this->query("update $this->bconfig_table set mval='no', pcmval='no' where cno=$no or pcno=$no"); // 2010.07.05 fixed
		}
		return (array_sum($result)==count($nos));
	}

	// 메뉴(카테고리) 삭제
	function delete_category($datas) {
		$nos = str_replace("__", ",", $datas['no']);

		// 카테고리에 포함된 게시판 삭제
		$boards = $this->queryFetchRows("select id from $this->bconfig_table where (cno in($nos) or (cno!=pcno and pcno in($nos)))");
		if($this->check_resource($boards)) foreach($boards as $board) $this->delete_board($board['id']);

		// 카테고리 정보 삭제
		$result = $this->query("delete from $this->category_table where (no in($nos) or (no!=pno and pno in($nos)))");
		if(mysql_affected_rows() && $datas['pno'] && $datas['items']==1) $this->update_category(array("cval"=>"no"), $datas['pno']);

		if($this->optimizer) $this->query("optimize table $this->bconfig_table, $this->category_table");
		return $result;
	}

	// 메뉴(카테고리) 출력 - 메인게시판 관리에서 사용
	function print_category() {
		$datas = $this->get_category(array("pno"=>0), false, false);
		if(!$this->check_resource($datas)) return '';
		$count = 1;
		$max_cols = 7;
		$bolder = " style='font-weight:bolder'";
		$_style = in_array($_GET['category'], array('', "main")) ? $bolder : '';
		// 메인페이지 설정 링크 추가
		if($this->use_main_board) $_categories = "<td$_style><img src='./img/ic_arrow1.gif' align='absmiddle'> <a href='./main.html?category=main'>메인페이지</a></td>";
		foreach($datas as $rows) {
			$_style = $_GET['category']==$rows['no'] ? $bolder : '';
			$_categories .= "<td$_style><img src='./img/ic_arrow1.gif' align='absmiddle'> <a href='./main.html?category=$rows[no]' cno='$rows[no]'>$rows[content]</a></td>";
			if(!(++$count%$max_cols)) {
				$categories .= "<tr>$_categories</tr>";
				unset($_categories);
			}
		}
		if(isset($_categories)) {
			$_tds = str_repeat("<td>&nbsp;</td>", $max_cols-($count%$max_cols));
			$categories .= "<tr>$_categories$_tds</tr>";
		}
		return $categories;
	}

	// 메뉴(카테고리) 데이터 XML 형태로 반환
	function formalize_category_xml_data($datas) {
		foreach($datas as $rows) {
			$xml_data .= "
			<item no='$rows[no]'>
				<pno>$rows[pno]</pno>
				<mbno><![CDATA[{$rows[mbno]}]]></mbno>
				<content><![CDATA[{$rows[content]}]]></content>
				<mval>$rows[mval]</mval>
				<bval>$rows[bval]</bval>
				<cval>$rows[cval]</cval>
				<uval>$rows[uval]</uval>
				<pval>$rows[pval]</pval>
			</item>";
		}
		$xml_data = "
		<category>$xml_data
		</category>";
		return $xml_data;
	}

	// 메뉴(카테고리) 추출
	function get_category($datas, $all=true, $xml=true) {
		$addWhere = ($all===true) ? '' : " and pval='yes' and uval='yes' and dval='no'";
		$datas = $this->queryFetchRows("select * from $this->category_table where pno=$datas[pno] $addWhere order by rank");
		return $xml===true ? $this->formalize_category_xml_data($datas) : $datas;
	}

	// 메인페이지 카테고리 추출
	function get_main_category() {
		if($this->board_extension===true) return "main";
		else $category = $this->queryR("select no from $this->category_table where pno=0 and uval='yes' and dval='no' order by rank limit 0,1");
		if(empty($category)) $this->popup_msg_js("게시판이 존재하지 않습니다. 게시판이 생성되어 있는지 확인하시기 바랍니다.", "BACK");
		else return $category;
	}

	// 회원 등급/포인트 설정
	function get_member_level_points($option=false, $in7th=true) { // $in7th : 7등급 포함 여부 - 회원 등급/포인트 설정시에만 사용
		// rankup_board 테이블에서 회원 등급 참조
		if($this->board_extension===true || $this->use_extend_level===true) {
			if($this->use_extend_level) list($levels, $sm_infos) = extend_level_point($this); // 2011.10.01 added
			else {
				$sm_infos = $this->queryFetch("select smlevel, smpoint from $this->sconfig_table");
				$levels = unserialize($sm_infos['smlevel']);
			}
			if(!$this->check_resource($levels)) $levels = array(7=>"비회원", 6=>"준회원", 5=>"정회원", 4=>"우수회원", 3=>"특별회원", 2=>"부운영자", 1=>"운영자", "join_level"=>"6");
		}
		else $levels = array(7=>"비회원", 5=>"회원", 1=>"운영자");
		if($option===true) {
			foreach($levels as $key=>$val) {
				if($key==="join_level" || $in7th===false && $key==7) continue;
				$options .= "<option value='$key'>$val</option>";
			}
			return ($in7th===true) ? $options : array($levels, $options, unserialize($sm_infos['smpoint']));
		}
		return $levels;
	}

	// 게시판 스킨 추출
	function get_board_skins($skin_dir="board", $color='') { // $color = "gray"
		$skin_dir = $this->board_dir."skin/$skin_dir/";
		if(is_dir($skin_dir) && $dh = opendir($skin_dir)) {
			while(($dir = readdir($dh)) !== false) {
				if(in_array($dir, array(".", "..")) || strtoupper(filetype($skin_dir.$dir))!="DIR") continue;
				$options .= "<optgroup style='background-color:black;color:white;' label='".strtolower($dir)."'></optgroup>";
				if($_dh = opendir($skin_dir.$dir)) {
					while(($_dir = readdir($_dh)) !== false) {
						if(in_array($_dir, array(".", "..")) || strtoupper(filetype($skin_dir.$dir."/".$_dir))!="DIR") continue;
						$options .= "<option value='$dir/$_dir'>{$dir}_$_dir</option>";
						if($color!=="" && $color===$_dir) return "$dir/$_dir";
					}
				}
			}
		}
		if(empty($options)) $options = "<option value=''>스킨없음</option>";
		return $options;
	}

	// 게시판 목록 구성 - 관리자 - 2009.09.09 added
	function get_category_boards($datas, $is_comment=false) {
		$category_datas = $this->queryFetchRows("select * from $this->category_table where pno=0 and pval='yes' and uval='yes' and dval='no' order by rank");
		if($this->check_resource($category_datas)) {
			$max_cols = 4;
			$_width = " width='".floor(100/$max_cols)."%'";
			foreach($category_datas as $category_rows) {
				$board_datas = $this->queryFetchRows("select c.no, c.id, c.name, c.anum from $this->bconfig_table as c, $this->category_table as m2, $this->category_table m1 where c.pcno=$category_rows[no] and m1.no = c.pcno and m2.no=c.cno and m2.pval='yes' order by m1.rank, m2.pno, m2.rank, m1.pno, c.rank"); // 관리자가 설정한 순서대로 정렬된다.
				if($this->check_resource($board_datas)) {
					$count = 0;
					$board_options .= "<optgroup label=\"$category_rows[content]\" style='background-color:#343434;color:white'></optgroup>";
					foreach($board_datas as $board_rows) {
						if(empty($datas['id'])) $datas['id'] = $board_rows['id'];
						if($is_comment==false) $nums = "($board_rows[anum])";
						else {
							$nums = $this->queryR("select sum(cnum) from $this->board_prefix$board_rows[id]");
							$nums = "($nums)";
						}
						$bolder_style = ($datas['id']==$board_rows['id']) ? " style='font-weight:bolder'" : '';
						$board_item .= " <nobr><img src='./img/ic_dot1.gif' align='absmiddle' vspace='8'> <a href='?id=$board_rows[id]' class='bottom_cate'$bolder_style>$board_rows[name]</a>$nums</nobr>";
						$bg_style = ($datas['id']==$board_rows['id']) ? " style='background-color:#dedede;color:#999'" : '';
						$board_options .= "<option value='$board_rows[id]'$bg_style>$board_rows[name]</option>";
					}
					if(!empty($category_items)) {
						$category_items .= "
						<tr>
							<td height='1' colspan='2' style='border-bottom:1px #c9ddec solid;font-size:1px;padding:0;line-height:0'>&nbsp;</td>
						</tr>";
					}
					$category_items .= "
					<tr>
						<td width='100' style='padding-top:7px'><img src='./img/ic_arrow1.gif' align='absmiddle'> $category_rows[content]</td>
						<td style='padding-top:7px' bgcolor='white'>$board_item</td>
					</tr>";
					unset($board_item);
				}
			}
		}
		return array($category_items, $board_options, $datas['id']);
	}

	// 게시판 분류 - 관리자 - 2009.09.09 added
	function get_board_categories($datas) {
		$categories = unserialize($this->board_configs['scategory']);
		if($this->check_resource($categories)) {
			$scategory = $this->sort_scategory($categories); // 2009.09.18 added
			foreach($scategory as $rank=>$rows) {
				$nodes .= "<item cno='$rows[cno]' anum='$rows[anum]'><![CDATA[{$rows[name]}]]></item>";
			}
		}
		return $nodes;
	}

	// 게시판 분류 순위 - 관리자 - 2009.09.18 added
	function reset_category_rank($datas) {
		if($this->check_resource($datas['rank'])) {
			// 순위 정리
			$ranks = array();
			foreach($datas['rank'] as $rank=>$cno) $ranks[$cno] = $rank+1;
			// 순위 갱신
			$scategory = array();
			$categories = $this->get_board_config($datas['bno'], "scategory");
			$categories = @unserialize($categories);
			foreach($categories as $cno=>$rows) {
				$scategory[$cno] = array(
					"name" => $rows['name'],
					"anum" => $rows['anum'],
					"rank" => $ranks[$cno]
				);
			}
			$_val['scategory'] = serialize($scategory);
			$values = $this->change_query_string($_val);
			$this->query("update $this->bconfig_table set $values where no=$datas[bno]");
		}
	}

	// 게시물 이동 - 관리자 - 2009.09.09 added
	function move_articles($datas) {
		// 정상적인 접근인지 체크
		if(!empty($datas['anos']) && !empty($_SERVER['HTTP_REFERER'])) {
			// 게시물이동 및 파일이동, 댓글 이동
			// regist_article() 후 첨부파일 이동
			$this->point_locked = true;
			foreach(explode("__", $datas['anos']) as $ano) {
				$board_infos = $this->queryFetch("select * from $this->board_table where no=$ano");
				$this->move_article($board_infos, $datas['move_bid'], $datas['move_cno']); // 댓글 포함
			}
			return "alert('게시물이 이동되었습니다.'+SPACE); document.location.reload();";
		}
		else $this->popup_msg_js("정상적인 접근이 아닙니다.", "BACK");
	}

	// 게시판 정보 변경 - 2009.09.09 added
	function change_board($board_id) {
		$this->board_configs = $this->get_board_config($board_id);
		$this->board_table = $this->board_prefix.$this->board_configs['id'];
		$this->board_comment_table = $this->comment_prefix.$this->board_configs['id'];
		$this->board_id = $this->board_configs['id'];
	}

	// 게시물 이동 처리 - 관리자 - 2009.09.09 added
	function move_article($datas, $move_bid, $move_cno='') {
		$board_id = $this->board_id;

		// 댓글 데이터 로드
		$comment_datas = $this->queryFetchRows("select * from $this->board_comment_table where ano=$datas[no] order by no");

		// 게시판 정보 변경
		$this->change_board($move_bid);

		// 1. 게시물 이동 - 2012.05.15 modify
		$near_article = $this->queryFetch("select no, dno, sno, nano, pano from $this->board_table where sno>$this->notice_sno and wdate <= '$datas[wdate]' order by sno limit 1");
		if(empty($near_article)) $near_article = $this->queryFetch("select no, dno, sno, nano, pano from $this->board_table order by sno desc limit 1");
		if(is_array($near_article)) $next_sno = !empty($near_article['sno']) ? $near_article['sno']>$this->notice_sno ? $near_article['sno']-1 : -1 : -1;
		else $next_sno = -1;

		// 이웃하는 글 설정
		if(empty($near_article['no'])) $near_article['no'] = 0;
		if($next_sno<$near_article['sno']) list($_val['nano'], $_val['pano']) = array($near_article['no'], $near_article['pano']);
		else list($_val['nano'], $_val['pano']) = array($near_article['nano'], $near_article['no']);
		if($_val['nano']==null) $_val['nano'] = 0;

		// 등록
		$_val['sno'] = $next_sno;
		$_val['cno'] = $move_cno ? $move_cno : null; // 분류
		$_val['dno'] = $this->increase_division($near_article['dno']); // 디비전 증가  cf. decrease_division();
		$_val['uip'] = $datas['uip'];
		$_val['uid'] = $datas['uid'];
		$_val['unick'] = $datas['unick'];
		$_val['upass'] = $datas['upass'];
		$_val['subject'] = $datas['subject'];
		$_val['content'] = $datas['content'];
		$_val['voter'] = $datas['voter'];
		$_val['sval'] = $datas['sval'];
		$_val['nval'] = 'no';
		$_val['dval'] = $datas['dval'];
		$_val['cnum'] = $datas['cnum'];
		$_val['dnum'] = $datas['dnum'];
		$_val['gnum'] = $datas['gnum'];
		$_val['bnum'] = $datas['bnum'];
		$_val['hnum'] = $datas['hnum'];
		$_val['wdate'] = $datas['wdate'];
		$_val['mdate'] = $datas['mdate'];
		if($datas['attach']) {
			$_val['attach'] = $datas['attach'];
			// 첨부파일(썸네일 포함) 이동 및 본문 수정
			$attach_infos = unserialize($datas['attach']);
			if($this->check_resource($attach_infos)) {
				preg_match_all('/<img\s+.*?src="([^"]+)"[^>]*>/is', $_val['content'], $imgs);
				$attach_dir = $this->board_dir.'attach/';
				foreach($attach_infos as $attach) {
					$file = $attach_dir.$board_id.'/'.$attach['sname'];
					if(is_file($file)) { // 첨부파일 이동
						rename($file, $attach_dir.$this->board_id.'/'.$attach['sname']);
						// 썸네일 이동
						$thumb_file = $attach_dir.$board_id.'/thumb_'.$attach['sname'];
						if(is_file($thumb_file)) rename($thumb_file, $attach_dir.$this->board_id.'/thumb_'.$attach['sname']);
					}
					// 본문에 이미지가 삽입된 경우 본문 수정
					if(!$this->check_resource($imgs[1])) continue;
					foreach($imgs[1] as $key=>$img) {
						$comp = "/attach/$board_id/$attach[sname]";
						if(ereg($comp, $img)!==false) {
							$_img = str_replace($comp, "/attach/$this->board_id/$attach[sname]", $img);
							$_val['content'] = str_replace($img, $_img, $_val['content']);
							unset($imgs[1][$key]);
						}
					}
				}
			}
		}

		$values = $this->change_query_string($_val);
		$this->query("insert $this->board_table set $values$addWhere");
		$article_no = mysql_insert_id();

		//등록한글 위로는 sno 값 증가 - 2012.05.15 add
		if($near_article[no]) $this->query("update $this->board_table set sno = sno-1 where no > $near_article[no] and no != $article_no");

		// 이웃하는 게시물(이전/다음 글) 갱신
		$_datas = array("no"=>$article_no, "sno"=>$_val['sno'], "pano"=>0, "nano"=>$near_article['no']);
		$this->change_near_article($_datas, $near_article);
		$this->update_board(array("cmd"=>"set_anum", "plus_mode"=>true)); // 게시물 수 갱신

		// 분류기능 사용중일 경우 카테고리 anum 갱신
		if($move_cno) {
			$scategory = @unserialize($this->board_configs['scategory']);
			$scategory[$move_cno]['anum'] += 1;
			$_sVal['scategory'] = serialize($scategory);
			$values = $this->change_query_string($_sVal);
			$this->query("update $this->bconfig_table set $values where id='$this->board_id'");
		}

		// 내 게시물 정보 갱신
		if($this->board_extension===true && $datas['uid']) {
			$_wVal['pcno'] = $this->board_configs['pcno'];
			$_wVal['bid'] = $this->board_id;
			$_wVal['ano'] = $article_no;
			$values = $this->change_query_string($_wVal);
			$this->query("update $this->my_article_table set $values where bid='$this->board_id' and ano=$datas[no]"); // 2010.06.30 fixed
		}

		// 최근 게시물에 등록 - 상위메뉴(pcno) 당 5개씩만 관리
		$new_datas = $this->queryFetchRows("select no from $this->new_article_table where pcno=".$this->board_configs['pcno']." order by no");
		$new_rows = current($new_datas);
		if(count($new_datas)>=5 && $this->is_demo===false) { // 1번째(등록한지 가장 오래된) 레코드 제거
			$this->query("delete from $this->new_article_table where no=$new_rows[no]");
			if($this->optimizer===true && mysql_affected_rows()) $this->query("optimize table $this->new_article_table");
		}
		$_xVal['pcno'] = $this->board_configs['pcno'];
		$_xVal['bid'] = $this->board_id;
		$_xVal['adno'] = $_val['dno'];
		$_xVal['ano'] = $article_no;
		$_xVal['awdate'] = $_val['wdate'];
		$values = $this->change_query_string($_xVal);
		if($_val['dval'] == "no") $this->query("insert $this->new_article_table set $values");

		// 조회수 베스트 및 주간 베스트 갱신
		$_bVal['pcno'] = $this->board_configs['pcno'];
		$_bVal['bid'] = $this->board_id;
		$_bVal['adno'] = $_val['dno'];
		$_bVal['ano'] = $article_no;
		$values = $this->change_query_string($_bVal);
		if($_val['dval'] == "no") $this->query("update $this->hit_best_table set $values where bid='$board_id' and ano=$datas[no]");
		if($_val['dval'] == "no") $this->query("update $this->weekly_best_table set $values where bid='$board_id' and ano=$datas[no]");

		// 2. 댓글 이동
		if($this->check_resource($comment_datas)) {
			foreach($comment_datas as $crows) {
				$_yVal['ano'] = $article_no;
				$_yVal['uip'] = $crows['uip'];
				$_yVal['uid'] = $crows['uid'];
				$_yVal['unick'] = $crows['unick'];
				$_yVal['upasswd'] = $crows['upasswd'];
				$_yVal['icon'] = $crows['icon'];
				$_yVal['content'] = $crows['content'];
				$_yVal['wdate'] = $crows['wdate'];
				$values = $this->change_query_string($_yVal);
				$this->query("insert $this->board_comment_table set $values");
				$comment_no = mysql_insert_id();

				// 내 댓글 갱신
				if($this->board_extension===true && $crows['uid']) {
					$_zVal['pcno'] = $this->board_configs['pcno'];
					$_zVal['bid'] = $this->board_id;
					$_zVal['ano'] = $article_no;
					$_zVal['cno'] = $comment_no;
					$values = $this->change_query_string($_zVal);
					$this->query("update $this->my_comment_table set $values where bid='$board_id' and no=$crows[no]");
				}
			}
			// 댓글 베스트 갱신
			$_aVal['pcno'] = $this->board_configs['pcno'];
			$_aVal['bid'] = $this->board_id;
			$_aVal['adno'] = $_val['dno'];
			$_aVal['ano'] = $article_no;
			$values = $this->change_query_string($_aVal);
			if($_val['dval'] == "no") $this->query("update $this->comment_best_table set $values where bid='$board_id' and ano=$datas[no]");

			// 주간 댓글 베스트 갱신
			if($this->board_extension===true) {
				$_cVal['bid'] = $this->board_id;
				$_cVal['ano'] = $article_no;
				$values = $this->change_query_string($_cVal);
				if($_val['dval'] == "no") $this->query("update $this->weekly_cbest_table set $values where bid='$board_id' and ano=$datas[no]");
			}
		}

		// 게시판 정보 복원
		$this->change_board($board_id);
		$this->delete_article($datas, true);
		return true;
	}

	// 게시판 코멘트 목록 - 2009.09.09 added
	function get_board_comments($datas, $limit=15) {
		if(empty($datas['id'])) return false;
		if($this->board_table==null) $this->rankup_board($datas['id']); // 환경설정

		if(empty($datas['page'])) $datas['page'] = 1;
		$stpos = $datas['page']>1 ? ($datas['page']-1)*$limit : 0;

		// 검색기간
		if($datas['use_date']=="on") {
			if(!empty($datas['sdate'])) {
				if(!empty($datas['edate'])) $addWhere .= " and date_format(wdate, '%Y-%m-%d') between '$datas[sdate]' and '$datas[edate]'";
				else $addWhere .= " and date_format(wdate, '%Y-%m-%d')>='$datas[sdate]'";
			}
			else if(!empty($datas['edate'])) $addWhere .= " and date_format(wdate, '%Y-%m-%d')<='$datas[edate]'";
		}
		// 검색어
		if(!empty($datas['skey'])) {
			switch($datas['smode']) {
				case "content": // 내용 검색
					$addWhere .= " and content like '%$datas[skey]%'";
					break;
				case "author": // 작성자 검색
					$addWhere .= " and unick like '%$datas[skey]%'";
					break;
				case "uid": // 아이디 검색
					$addWhere .= " and uid like '%$datas[skey]%'";
					break;
			}
		}
		$total_comments = $this->queryR("select count(no) from $this->board_comment_table where no$addWhere");
		$paging_button = $this->print_paging($total_comments, array($limit, 10), 'page', $this->board_url.'skin/board/basic/gray/');
		$comment_datas = $this->queryFetchRows("select no, ano, uid, unick, icon, content, date_format(wdate, '%Y-%m-%d') as wdate from $this->board_comment_table where no$addWhere order by no desc limit $stpos, $limit");
		if($this->check_resource($comment_datas)) {
			foreach($comment_datas as $rows) {
				$member_info_link = empty($rows['uid']) ? '' : "(<a href='{$this->base_url}rankup_module/rankup_member/member_detail.html?uid=$rows[uid]'>$rows[uid]</a>)";
				if(!empty($comment_contents)) {
					$comment_contents .= "
					<tr height='15'><td></td></tr>";
				}
				$rows['wdate'] = str_replace('-', '.', $rows['wdate']);
				$comment_contents .=
				"<tr>
					<td width='80%'>
						<input type='checkbox' name='no[]' value='$rows[no]' align='absmiddle'>
						<b>$rows[unick]$member_info_link</b>&nbsp; <span class='gray_s'>$rows[wdate]</span> &nbsp; <a onClick='rankup_board.comment_delete($rows[no])'><img src='./img/btn_delete_s.gif' align='absmiddle'></a></td>
					<td align='right' width='20%'><a href='./index.html?id=$datas[id]&no=$rows[ano]'><img src='./img/btn_view_sentence.gif'></a></td>
				</tr>
				<tr><td height='3'></td></tr>
				<tr>
					<td background='./img/dot_line_gray.gif' colspan='2' height='1'></td>
				</tr>
				<tr bgcolor='#f4f4f4'>
					<td colspan='2' height='30' class='content'><img src='".$this->board_url."icon/face_$rows[icon].gif' align='absmiddle' class='icon'>&nbsp; ".nl2br($rows['content'])."</td>
				</tr>
				<tr>
					<td background='./img/dot_line_gray.gif' colspan='2' height='1'></td>
				</tr>";
			}
		}
		if(empty($comment_contents)) {
			$comment_contents = "
			<tr disabled>
				<td colspan='2'><input type='checkbox'> 댓글이 존재하지 않습니다.</td>
			</tr>";
		}
		return array($total_comments, $comment_contents, $paging_button);
	}

	// 댓글 선택삭제 - 관리자
	function delete_comments($datas) {
		// 정상적인 접근인지 체크
		if(!empty($datas['nos']) && !empty($_SERVER['HTTP_REFERER'])) {
			$comment_datas = $this->queryFetchRows("select no, ano, uid, wdate from $this->board_comment_table where no in(".str_replace("__", ",", $datas['nos']).")");
			foreach($comment_datas as $comment_infos) $this->_delete_comment($comment_infos); // 댓글 삭제 루틴을 직접 호출
			return "alert('댓글이 삭제되었습니다.'+SPACE); document.location.reload();";
		}
		else $this->popup_msg_js("정상적인 접근이 아닙니다.", "BACK");
	}

}
?>