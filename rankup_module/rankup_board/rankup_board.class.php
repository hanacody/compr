<?php
## 랭크업 멀티게시판 통합 클래스
class rankup_board extends rankup_util {
	var $version = "v2.1 r100618"; // 게시판 개발 버전
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $bconfig_table = "rankup_board_config"; // 게시판 별 환경설정 테이블(접근 권한, 분류(serialize),)
	var $setting_table = "rankup_etcconfig_setting"; // 게시판 etc 셋팅
	var $division_table = "rankup_board_division"; // 게시물 분할번호 관리 테이블
	var $category_table = "rankup_board_category"; // 게시판 카테고리
	var $hit_best_table = "rankup_board_hit_best"; // 조회수 베스트 테이블(게시판별 10개씩만 관리)
	var $comment_best_table = "rankup_board_comment_best"; // 댓글수 베스트 테이블(게시판별 10개씩만 관리)
	var $weekly_best_table = "rankup_board_weekly_best"; // 주간 베스트 테이블
	var $new_article_table = "rankup_board_new_article"; // 신규 게시물(게시판별 10개씩만 관리)
	var $weekly_cbest_table = "rankup_board_weekly_cbest"; // 주간 댓글 베스트 테이블
	var $report_table = "rankup_article_report"; // 불량 게시물 신고 테이블
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $division_num = 100000; // 10만개 단위 분할
	var $notice_sno = -2000000000; // -20억
	var $optimizer = true; // 테이블 최적화 모드 활성화 여부
	var $use_main_board = true; // 메인게시판 사용여부
	var $use_board_menu = true; // 게시판 메뉴 사용여부 - 2011.10.06 added
	var $display_subject = true; // 게시판제목 출력 여부 - 2011.10.06 added
	var $thumbnail_width = 200; // 썸네일 가로사이즈
	var $thumbnail_height = null; // 썸네일 세로사이즈(null : 가로 비율에 맞춤)
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $board_prefix = "rankup_board_";
	var $comment_prefix = "rankup_board_comment_";
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $sconfig_table = "rankup_siteconfig"; // 솔루션 기본환경설정 테이블(디자인,포인트,등급설정)
	var $member_table = "rankup_member"; // 회원 테이블(기본정보)
	var $member_extend_table = "rankup_member_extend"; // 회원 확장테이블(닉네임,불량회원,등급,포인트설정)
	var $admin_session_id = "admin_session_id"; // 관리자 세션 ID
	var $member_session_id = "niceId";	// 회원 세션 ID
	var $member_uid_field = "uid"; // 회원 아이디 필드명
	var $member_passwd_field = "passwd"; // 회원 비밀번호 필드명
	var $member_name_field = "name"; // 회원 이름 필드명
	var $editor_name = "wysiwyg"; // 위지윅 에디터 폴더명
	var $index_name = "board"; // 게시판 인덱스파일이 위치하는 폴더명
	var $etc_file_name = "include"; // 게시판 ETC 파일이 위치하는 폴더명
	var $include_js_class = false; // rankup_basic::include_js_class() 사용여부 - 2009.10.08 added
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $member_badness = "no"; // 불량 회원 여부
	var $member_level = 7; // 회원 등급(7: 비회원, 5: 정회원, 1: 운영자)
	var $member_id = ""; // 로그인 중인 회원 아이디
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $confirm_used = true; // 스팸방지코드 사용여부 - 2010.06.17 added
	var $granted_messages = array( // 2010.08.12 added
		'list_level' => "죄송합니다. 이 게시판은 '%s' 이상 이용이 가능합니다.",
		'read_level' => "죄송합니다. 이 게시판은 '%s' 이상 조회가 가능합니다.",
		'write_level' => "죄송합니다. 이 게시판은 '%s' 이상 글쓰기가 가능합니다.",
		'delete_level' => "죄송합니다. 이 게시판은 '%s' 이상 삭제가 가능합니다.",
		'comment_level' => "죄송합니다. 이 게시판은 '%s' 이상 댓글쓰기가 가능합니다.",
		'reply_level' => "죄송합니다. 이게시판은 '%s' 이상 답변글 쓰기가 가능합니다.",
		'notice_level' => "죄송합니다. 이 게시판은 '%s' 이상 공지글 쓰기가 가능합니다.",
		'secret_level' => "죄송합니다. 이 게시판은 '%s' 이상 비밀글 조회가 가능합니다."
	);
	var $deny_nonmember = "\\n먼저, 회원으로 로그인하시기 바랍니다."; // 회원 권한체크시 비회원용 메시지 - 2010.08.12 added
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $index_url = "";
	var $wysiwyg_url = "";
	var $wysiwyg_dir = "";
	var $base_url = "";
	var $base_dir = "";
	var $board_url = "";
	var $board_dir = "";
	var $skin_url = "";
	var $skin_dir = "";
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $board_extension = false;
	var $board_configs = null;
	var $board_table = null;
	var $board_comment_table = null;
	var $board_name = null;
	var $board_id = null;
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $spermission = null;
	var $slayout = null;
	var $sfunction = null;
	var $soption = null;
	var $sattach = null;
	var $spoint = null;
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	var $wm_settings = array();
	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// 게시판 클래스 초기화
	function rankup_board($board_id='') {
		parent::rankup_util();
		$this->base_url = rankup_basic::base_url();
		$this->base_dir = rankup_basic::base_dir();
		$this->board_url = $this->base_url."rankup_module/rankup_board/";
		$this->board_dir = $this->base_dir."rankup_module/rankup_board/";
		// 솔루션별 환경 설정 적용
		include $this->base_dir."Libs/_php/board_setting.inc.php";
		$this->index_url = $this->base_url.$this->index_name; // 인덱스 경로 설정
		$this->etc_file_dir = $this->base_dir.$this->index_name."/".$this->etc_file_name."/"; //게시판 ETC FILE 경로설정
		$this->wysiwyg_url = $this->base_url.$this->editor_name."/"; // 위지윅 경로 설정
		$this->wysiwyg_dir = $this->base_dir.$this->editor_name."/"; // 위지윅 경로 설정
		$this->check_config_tables(); // 기본환경 테이블 체크
		$this->get_wm_settings(); //워터마크 설정
		if(!empty($board_id)) {
			$check_table = $this->queryR("show tables like '$this->board_prefix$board_id'");
			if(empty($check_table)) $this->popup_msg_js("요청하신 '$board_id' 게시판이 존재하지 않습니다.", "BACK");
			$this->board_configs = $this->get_board_config($board_id);
			if(!$this->is_admin() && $this->board_configs['uval']=="no") { // 2009.08.31 fixed
				$this->popup_msg_js("요청하신 '$board_id' 게시판이 존재하지 않습니다.", "BACK");
			}
			$this->check_iptables(); // 블럭된 아이피 인지 체크
			$this->skin_url = $this->board_url."skin/board/".$this->board_configs['skin']."/"; // img 태그 경로지정시
			$this->skin_dir = $this->board_dir."skin/board/".$this->get_skin_dir()."/"; // skin 파일 로드시
			$this->board_table = $this->board_prefix.$this->board_configs['id'];
			$this->board_comment_table = $this->comment_prefix.$this->board_configs['id'];
			$this->board_name = $this->board_configs['name'];
			$this->board_id = $this->board_configs['id'];
			$this->add_referers('id='.$this->board_id); // 관리자 게시물관리 패치 - 2010.05.31 fixed
			$this->spermission = unserialize($this->board_configs['spermission']); // 권한설정
			$this->slayout = unserialize($this->board_configs['slayout']); // 기본 레이아웃
			$this->sfunction = unserialize($this->board_configs['sfunction']); // 보조기능설정
			$this->soption = unserialize($this->board_configs['soption']); // 선택사항
			$this->sattach = unserialize($this->board_configs['sattach']); // 파일첨부
			$this->spoint = unserialize($this->board_configs['spoint']); // 포인트
			// 파일첨부 기능 사용시 임시 폴더(쿠키 이용) 체크
			if($this->sattach['use_attach']=="on") $this->clear_junk_files();
		}
		$this->member_level = $this->check_member_level(); // 회원 레벨 체크
		// 메인페이지 / 메뉴페이지 레이아웃 설정 로드 - 2009.08.31 added
		$this->main_layout = $this->get_main_layout($_GET['pcno']);
	}

	// 레퍼러 정보 추가 - 2010.05.31 added
	function add_referers($param='') {
		if(empty($param)) return;
		list($url, $query) = explode('?', $_SERVER['HTTP_REFERER']);
		parse_str($query, $queries);
		parse_str($param, $params);
		$referers = @http_build_query(array_merge($queries, $params));
		$_SERVER['HTTP_REFERER'] = $url.'?'.$referers;
	}

	// 회원 등급 체크
	function check_member_level() {
		$this->member_id = $_SESSION[$this->member_session_id];
		if($this->use_extend_level) return extend_level($this); // 2011.10.01 added
		else {
			$fields = array();
			$table_check = $this->queryR("show tables like '".$this->member_extend_table."'");
			if($table_check) $field_datas = $this->queryFetchRows("desc ".$this->member_extend_table);
			else $field_datas = false;
			if($this->check_resource($field_datas)) foreach($field_datas as $rows) $fields[] = $rows['Field'];
			$this->board_extension = in_array("nickname", $fields) && in_array("badness", $fields) && in_array("point", $fields) && in_array("level", $fields);
			// 포인트 사용유무
			if(!empty($this->member_id)) {
				if($this->board_extension===true) {
					$this->smpoint = @unserialize($this->queryR("select smpoint from $this->sconfig_table"));
					$member_infos = $this->queryFetch("select nickname, badness, level, point from $this->member_extend_table where $this->member_uid_field='$this->member_id'");
					$this->member_badness = $member_infos['badness']; // 불량회원
					$this->member_point = $member_infos['point']; // 회원 포인트
					$this->member_name = $member_infos['nickname']; // 회원이름 - 닉네임
					return $member_infos['level'];
				}
				else {
					$member_infos = $this->queryFetch("select $this->member_name_field from $this->member_table where $this->member_uid_field='$this->member_id'");
					$this->member_name = $member_infos[$this->member_name_field]; // 회원이름
				}
			}
		}
		return empty($this->member_id) ? 7 : 5; // 7 비회원, 5 회원
	}

	// 회원 비밀번호 리턴
	function get_member_passwd($uid) {
		return $this->queryR("select $this->member_passwd_field from $this->member_table where $this->member_uid_field='$uid'");
	}

	// 회원 로그인 여부 리턴 - 필요시 보완
	function is_member() {
		return !empty($_SESSION[$this->member_session_id]);
	}

	// 관리자 로그인 여부 - 2009.09.18 added
	function is_administrator() {
		return !empty($_SESSION[$this->admin_session_id]);
	}

	// 운영자 로그인 여부 리턴 - 필요시 보완
	function is_admin() {
		return (!empty($_SESSION[$this->admin_session_id]) || $this->member_level==1);
	}

	//바로삭제/대기삭제 판단
	function is_del() {
		//운영자 로그인일경우 바로 삭제
		if($this->sfunction['use_articledel'] == "now" || $this->is_admin()) return true;
		else return false;
	}

	// 게시판 환경 테이블 체크
	function check_config_tables() {
		@include "scheme/rankup_board_scheme.inc.html";
		foreach($_BOARD_CONFIG_TABLES as $table_name=>$create_query) {
			$check_table = $this->queryR("show tables like '$table_name'");
			if($check_table===$table_name) continue;
			$this->query($create_query);
		}
		return true;
	}

	//테이블이 없을경우 설정
	function check_etc_tables() {
		$check_table = $this->queryR("show tables like '$this->setting_table'");
		if($check_table === $this->setting_table) return false;
		$this->query("CREATE TABLE `rankup_etcconfig_setting` (
		`item_name` varchar(30) NOT NULL default '',
		`item_value` text NOT NULL,
		UNIQUE KEY `item_name` (`item_name`)) TYPE=MyISAM");
	}

	// 워터마크 설정반환
	function get_wm_settings() {
		$this->check_etc_tables();
		$this->wm_settings = array();
		$datas = $this->query("select * from $this->setting_table where item_name='thumb_configs'");
		while($rows = $this->fetch($datas)) {
			$this->wm_settings = unserialize($rows['item_value']);
		}
	}
	// 게시판 환경 설정 추출 - 범용
	function get_board_config($board, $fields='*') { // $board = { id  |  no }
		$addWhere = !is_numeric($board) ? is_array($board) ? "id in(".@implode(",", $board).")" : "id='$board'" : "no=$board";
		if(is_array($board)) return $this->queryFetchRows("select $fields from $this->bconfig_table where $addWhere");
		else {
			$result = $this->queryFetch("select $fields from $this->bconfig_table where $addWhere");
			return ($fields=='*' || count(explode(",", $fields))>1) ? $result : array_pop($result);
		}
	}

	// 회원등급 텍스트 반환 - 2010.08.12 added
	function get_granted_level_text($branch) {
		$level = $this->spermission[$branch];
		if($this->board_extension===true || $this->use_extend_level===true) {
			$levels = unserialize($this->queryR("select smlevel from $this->sconfig_table"));
			if(!is_array($level_texts)) $levels = array(7=>"비회원", 6=>"준회원", 5=>"정회원", 4=>"우수회원", 3=>"특별회원", 2=>"부운영자", 1=>"운영자", "join_level"=>"6");
		}
		else {
			$levels = array(7=>"비회원", 5=>"회원", 1=>"운영자");
		}
		return $levels[$level];
	}

	// 권한 텍스트 반환 - 2010.08.12 added
	function get_granted_messages($branch) {
		$login_message = $this->member_id ? "" : $this->deny_nonmember;
		$message = sprintf($this->granted_messages[$branch].$login_message, $this->get_granted_level_text($branch));
		return $message;
	}

	// 메뉴별 설정값 반환 - 2009.08.31 added
	function get_main_layout($category='') {
		if($category=='main') {
			$datas = $this->queryFetch("select smlayout from $this->sconfig_table");
			$datas = @unserialize($datas['smlayout']);
		}
		else {
			if(!$category) $category = $this->queryR("select no from $this->category_table where pno=0 order by rank limit 1");
			$datas = $this->queryFetch("select no, mbno, mskin, lskin, mbnum, sprint, mval from $this->category_table where no=$category");
			$datas['sprint'] = @unserialize($datas['sprint']);
		}
		return $datas;
	}

	// 게시판 설정 추출 - 2009.08.31 modified
	function get_setting($category, $xml=true) {
		// 메뉴별 설정값 추가
		if($xml==true) {
			$main_datas = $this->get_main_layout($category); // 2009.08.31 modified
			if($category!=="main" && $main_datas['mbno']) $main_datas['mbname'] = $this->queryR("select name from $this->bconfig_table where no=$main_datas[mbno]");
		}
		$addWhere = $category=="main" ? '' : " and c.pcno=$category";
		$board_datas = $this->queryFetchRows("select c.no, c.id, c.name, c.cno, c.pcno, c.anum, c.uval, c.mval, c.pcmval, c.smlayout, c.spcmlayout, c.rank from $this->category_table as m1, $this->category_table as m2, $this->bconfig_table as c where (m1.no=c.pcno and m1.pval='yes' and m2.no=c.cno and m2.pval='yes')$addWhere order by m1.rank, m2.pno, m2.rank, m1.pno, c.rank");
		return $xml===true ? $this->formalize_setting_xml_data($board_datas, $main_datas) : $board_datas;
	}


##########################################################################
## 템플릿 페이지 구성 및 프로세스
##########################################################################
	// 템플릿 페이지 로드
	function get_board_template($tpl_name, $type="board") {
		$cache_file = $this->board_dir."skin/$type/".$tpl_name;
		if(!is_file($cache_file)) {
			echo "<b style='color:red;font-size:9pt;'>Fatal error: 손상된 스킨이거나 규칙이 올바르지 않습니다.</b>";
			exit;
		}
		return $cache_file;
	}

	// 스킨 경로 리턴
	function get_skin_dir($name='') {
		if(empty($name)) $name = $this->board_configs['skin'];
		return array_shift(explode("/", $name));
	}

	// 템플릿 조각파일 로드
	function get_template_item($tpl_file) {
		$tpl_fp = @fopen($tpl_file, 'r');
		if(!is_resource($tpl_fp)) {
			echo "<b style='color:red;font-size:9pt;'>Fatal error: 손상된 스킨이거나 규칙이 올바르지 않습니다. $tpl_file</b>";
			exit;
		}
		while(!feof($tpl_fp)) $tpl_buffer .= fgets($tpl_fp, 4096);
		if(empty($tpl_buffer)) {
			echo "<b style='color:red;font-size:9pt;'>Fatal error: 손상된 스킨이거나 규칙이 올바르지 않습니다.</b>";
			exit;
		}
		return $tpl_buffer;
	}

##########################################################################
## 사용자페이지 - 메인페이지 부분
##########################################################################
	// 접근차단된 아이피 인지 체크
	function check_iptables() {
		if(in_array($_SERVER['REMOTE_ADDR'], explode(",",$this->board_configs['sblock']))) $this->popup_msg_js("고객님의 아이피는 차단목록에 설정되어 있어 게시판을 이용하실 수가 없습니다.", "BACK");
		return true;
	}

	// 페이징 출력 - $_GET 값 참조 - 2009.06.22 added
	function print_paging($total_records='', $division=array(15, 10), $key='page') {
		if(empty($total_records)) return '&nbsp;';

		if(is_array($division)) list($limits, $grouping) = $division;
		else {
			$limits = $this->slayout['article_rows'];
			if(!$grouping) $grouping = 10;
		}

		$first_page = 1;
		$last_page = ceil($total_records/$limits);
		//if($last_page<=$first_page) return '&nbsp;';
		// 페이징 설정
		$pattern = array(
			'format' => "%d", // 페이지 문자 출력형태
			'space' => " | " // 페이지 간 구분자
		);
		$icons = array(
			'first' => "<img src='{$this->skin_url}bt_prev_last.gif' align=''>",
			'previous' => "<img src='{$this->skin_url}bt_prev.gif' align='' hspace='3'>",
			'next' => "<img src='{$this->skin_url}bt_next.gif' align=''>",
			'last' => "<img src='{$this->skin_url}bt_next_last.gif' align='' hspace='3'>"
		);

		// 페이지 변수 정의
		$open_page = $_GET[$key];
		if(empty($open_page)) $open_page = 1;
		$now_grouping = ceil($open_page/$grouping);
		$last_grouping = ceil($last_page/$grouping);
		$min_page = ($now_grouping-1)*$grouping+1;
		$max_page = ($now_grouping*$grouping >= $last_page) ? $last_page : $now_grouping*$grouping;
		$prev_page = ($min_page==$first_page) ? 0 : $min_page-1;
		$next_page = ($max_page==$last_page) ? $last_page : $max_page+1;

		// 페이징 구성
		$pages = array();
		$contents = array($icons['first'], $icons['previous'], '', $icons['next'], $icons['last']);
		if($now_grouping!=$first_page) $contents[0] = "<a href='".$this->parameters("$key=1")."'>$icons[first]</a>";
		if($now_grouping!=$last_grouping) $contents[4] = "<a href='".$this->parameters("$key=$last_page")."'>$icons[last]</a>";
		foreach(range($prev_page, $next_page) as $page) {
			$params = $this->parameters("$key=$page");
			$num = sprintf($pattern['format'], $page);
			switch($page) {
				case $prev_page: if($now_grouping!=$first_page) $contents[1] = "<a href='$params'>$icons[previous]</a>"; break;
				case $open_page: $pages[] = "<font class='on'>$num</font>"; break;
				case $next_page:
					if($now_grouping!=$last_grouping) $contents[3] = "<a href='$params'>$icons[next]</a>";
					else $pages[] = "<a href='$params'>$num</a>";
					break;
				default: $pages[] = "<a href='$params'>$num</a>";
			}
		}
		$contents[2] = "<span class='num'>".@join($pattern['space'], $pages)."</span>";
		ksort($contents);
		return @join('', $contents);
	}

	// 접근 권한 체크
	function check_granted($branch) {
		// member_level = 1 : 운영자 -> 모든권한을 갖는다.
		return ((!empty($this->member_level) && $this->spermission[$branch]>=$this->member_level) || $this->member_level===1 || $this->is_admin());
	}

	// 메인 페이지 구성
	function get_main_contents($pcno='') {
		$this->main_layout = $this->get_main_layout($pcno); // 2009.09.21 fixed
		if($pcno==="main") unset($pcno);
		else {
			if(preg_match('/[^0-9]/', $pcno)) $this->popup_msg_js("잘못 된 요청입니다.", "BACK");
			if(!empty($pcno)) $addWhere = " no=$pcno and";
			else {
				$category_infos = $this->queryFetchObject("select no, mbno, mval, mskin, mbnum, sprint from $this->category_table where pno=0 and uval='yes' and pval='yes' and dval='no' order by rank limit 0,1");
				if(empty($category_infos->no)) $this->popup_msg_js("게시판이 존재하지 않습니다.", "BACK");
				else $pcno = $category_infos->no;
			}
		}
		// 메인페이지 구성이 아닌경우 - 즉, 메뉴 메인페이지 구성인 경우
		if(!empty($pcno)) {
			// 메뉴 설정값 로드
			if(!is_object($category_infos)) $category_infos = $this->queryFetchObject("select mbno, mval, mskin, mbnum, sprint, content from $this->category_table where$addWhere uval='yes' and pval='yes' and dval='no'");
			$category_infos->sprint = @unserialize($category_infos->sprint); // 출력설정

			// 카테고리 제목을 타이틀에 반영하기 위해 추가 - 2009.03.06
			$this->subject = $category_infos->content;

			// 메인페이지를 사용하지 않을 경우 메인 게시판을 뿌려줌
			if($category_infos->mval=="no") {
				if(empty($category_infos->mbno)) $this->popup_msg_js("메인페이지가 설정되어 있지 않습니다.", "BACK"); // 메인게시판이 설정되지 않았을 경우
				$board_id = $this->queryR("select id from $this->bconfig_table where no=$category_infos->mbno");
				$this->rankup_board($board_id);
				return $this->get_board_articles(array("id"=>$board_id));
			}
		}
		// 메인페이지 구성 - 2009.08.31 modified
		else if($this->check_resource($this->main_layout)) {
			$category_infos->mskin = $this->main_layout['mskin'];
			$category_infos->mbnum = $this->main_layout['mbnum'];
			$category_infos->sprint = $this->main_layout['sprint'];
		}
		// 게시판 순서대로 쿼리 - 2009.04.14 fixed
		$addWhere = empty($pcno) ? "c.mval='yes' and c.uval='yes'" : "c.pcno=$pcno and c.pcmval='yes' and c.uval='yes'"; // 2010.06.21 fixed
		$board_datas = $this->queryFetchRows("select c.no, c.id, c.name, c.smlayout, c.spcmlayout, c.sfunction, c.spermission, c.soption from $this->bconfig_table as c LEFT OUTER JOIN $this->category_table as m1 ON m1.no=c.pcno and m1.pval='yes' LEFT OUTER JOIN $this->category_table as m2 ON m2.no=c.cno and m2.pval='yes' where $addWhere order by m1.rank, m2.pno, m2.rank, m1.pno, c.rank");
		if($this->check_resource($board_datas)) {
			$column_count = 0;
			foreach($board_datas as $board_infos) {
				// 컬럼 스타일 지정
				$_main_article_contents .= $this->get_main_articles($pcno, $board_infos, $category_infos, $column_count);
				if(!(++$column_count%$category_infos->mbnum)) {
					if($main_article_contents) $main_article_contents .= "<tr><td height='15'></td></tr>"; // 2009.11.13 fixed
					$main_article_contents .= "
					<tr valign='top'>
						$_main_article_contents
					</tr>";
					unset($_main_article_contents);
				}
			}
			if(isset($_main_article_contents)) {
				$_tds = str_repeat("<td>&nbsp;</td>", $category_infos->mbnum-($column_count%$category_infos->mbnum));
				if($main_article_contents) $main_article_contents .= "<tr><td height='15'></td></tr>"; // 2009.11.13 fixed
				$main_article_contents .= "
				<tr valign='top'>
					$_main_article_contents$_tds
				</tr>";
				unset($_main_article_contents);
			}
		}
		// 스킨 URL
		$this->skin_url = $this->board_url."skin/main/".$category_infos->mskin."/";
		ob_start();
		include $this->get_board_template($this->get_skin_dir($category_infos->mskin)."/".(empty($pcno)?"main":"menu")."_page.tpl.html", "main");
		return ob_get_clean();
	}

	// 메이페이지에 출력할 게시판 내용
	function get_main_articles($pcno, $board_infos, $category_infos, $column=0) {
		$board_table = $this->board_prefix.$board_infos['id'];
		$slayout = empty($pcno) ? unserialize($board_infos['smlayout']) : unserialize($board_infos['spcmlayout']);
		if(!is_array($slayout) || !count($slayout)) return '';
		$skin_url = $this->board_url."skin/main/".$category_infos->mskin."/";
		$skin_dir = $this->board_dir."skin/main/".$this->get_skin_dir($category_infos->mskin)."/";
		switch($slayout['print_style']) {
			case "both":
				$image_width = $slayout['image_width'];
				$image_height = $slayout['image_height'];
				$attach_dir = $this->board_dir."attach/".$board_infos['id']."/";
				$attach_url = $this->board_url."attach/".$board_infos['id']."/";
			case "text":
				$tpl_buffer = $this->get_template_item($skin_dir."__text.tpl");
				break;
			case "image":
				$tpl_buffer = $this->get_template_item($skin_dir."__image.tpl");
				$image_width = $slayout['image_width'];
				$image_height = $slayout['image_height'];
				$attach_dir = $this->board_dir."attach/".$board_infos['id']."/";
				$attach_url = $this->board_url."attach/".$board_infos['id']."/";
				$column_count = 0;
				break;
		}
		// dno 처리하는 부분 추가 @#########
		$board_datas = $this->queryFetchRows("select no, uid, subject, cnum, attach, sval, dval, wdate from $board_table where dno>=1 and sno>$this->notice_sno and dval='no' order by sno, gno limit $slayout[article_rows]");
		if($this->check_resource($board_datas)) {

			// 게시판 링크 생성
			parse_str($_SERVER['QUERY_STRING'], $query_infos);
			unset($query_infos['no'], $query_infos['pcno']); // 게시물 번호/상위메뉴 번호는 제거
			$query_infos['id'] = $board_infos['id']; // 게시판 id 재설정
			$board_links = http_build_query($query_infos); // php5 이상, rankup_basic.class.php 에 정의됨

			$this->spermission = @unserialize($board_infos['spermission']); // 비밀글 처리를 위해
			$this->soption = @unserialize($board_infos['soption']); // 아이콘 처리를 위해 - 2009.01.19 fixed
			foreach($board_datas as $rows) {
				$article_link = "href=\"$this->index_url/index.html?$board_links&no=$rows[no]\"";
				// 비밀글일 경우
				if($rows['sval']=="yes" && !$this->is_seeable($rows['no']) && !$this->check_granted("secret_level") && (empty($rows['uid']) || (!empty($rows['uid']) && $this->member_id!==$rows['uid']))) {
					$article_link .= " onClick=\"rankup_board.scanf_passwd($rows[no], this, 'article_view', '$board_infos[id]'); return false;\"";
				}

				// 아이콘 처리 - 첨부파일 / NEW 아이콘
				$attach_icon = ($this->sattach['use_attach']=="on" && $this->soption['use_attach_icon']=="on" && !empty($rows['attach'])) ? "<img src='".$this->board_url."icon/icon_file.gif' align='absmillde'> " : '';
				$new_icon = ($this->soption['use_new_icon']=="on" && date("Y-m-d H:i:s", strtotime("-{$this->soption['recent_time']} hour"))<=$rows['wdate']) ? " <img src='".$this->board_url."icon/icon_new.gif' align='absmillde'>" : '';
				$secret_icon = $rows['sval']=="yes" ? " <img src='".$this->board_url."icon/icon_secret.gif' align='absmiddle'> " : '';

				// 게시물 제목
				if($rows['dval']=="yes") {
					$subject = "<strike>삭제된 게시물 입니다.</strike>";
					$article_link = "href=\"$this->index_url/index.html?$board_links&no=$rows[no]\" style=\"color:#cdcdcd\"";
					if(!$this->is_admin()) $article_link .= " onClick=\"alert('삭제된 게시물은 조회할 수 없습니다.'+SPACE); return false;\"";
				}
				else $subject = $this->str_cut($rows['subject'], $slayout['subject_length'], '');

				$subject = "$secret_icon<a $article_link>".$subject."</a>$new_icon"; // 2009.01.19 fixed
				$cnum = $rows['cnum']>0 ? "[{$rows[cnum]}]" : '';
				switch($slayout['print_style']) {
					// 혼합형
					case "both":
						if(count(unserialize($rows['attach'])) && empty($thumbnail)) {
							list($thumbnail_file, $_width, $_height, $_type) = $this->get_thumbnail_image($rows['attach'], $attach_dir);
							if(empty($thumbnail_file)) {
								$thumbnail = "
								<table width='$image_width' height='$image_height' cellspacing='0' cellpadding='0' bgcolor='white'>
								<tr align='center'><td><a $article_link onFocus='blur()'><img src='{$this->board_url}img/no_thumb.gif'></a></td></tr>
								</table>";
							}
							else {
								$thumbnail = "<a $article_link><img src='$attach_url$thumbnail_file' width='$image_width' height='$image_height' align='absmiddle'></a>";
							}
							$thumb_subject = "<a $article_link>$rows[subject]</a>$new_icon";
							continue;
						}
					// 텍스트형
					case "text":
						$main_article_contents .= str_replace(
							array("{:board_skin:}", "{:thumbnail:}", "{:subject:}", "{:cnum:}"),
							array($skin_url, $thumbnail, $subject, $cnum),
							$tpl_buffer);
						break;
					// 이미지형
					case "image":
						list($thumbnail_file, $_width, $_height, $_type) = $this->get_thumbnail_image($rows['attach'], $attach_dir);
						if(empty($thumbnail_file)) {
							$thumbnail = "
							<table width='$image_width' height='$image_height' cellspacing='0' cellpadding='0' bgcolor='white'>
							<tr align='center'><td><a $article_link onFocus='blur()'><img src='{$this->board_url}img/no_thumb.gif'></a></td></tr>
							</table>";
						}
						else if(is_file($attach_dir.$thumbnail_file)) $thumbnail = "<a $article_link><img src='$attach_url$thumbnail_file' width='$image_width' height='$image_height' align='absmiddle'></a>";
						$_main_article_contents .= str_replace(
							array("{:board_skin:}", "{:thumbnail:}", "{:subject:}", "{:cnum:}"),
							array($skin_url, $thumbnail, $subject, $cnum),
							$tpl_buffer);
						if(!(++$column_count%$slayout['article_rows'])) {
							$main_article_contents .= "<tr>$_main_article_contents</tr>";
							unset($_main_article_contents);
						}
						break;
				}
			} // @eo  foreach($board_datas as $rows)

			// 빈셀 채우기 - 이미지형일 경우에만 적용 됨
			if(isset($_main_article_contents)) {
				$_tds = str_repeat("<td><table width='$image_width' height='$image_height' cellspacing='0' cellpadding='0' bgcolor='white'><tr align='center'><td>&nbsp;</td></tr></table></td>", $slayout['article_rows']-($column_count%$slayout['article_rows']));
				$main_article_contents .= "<tr>$_main_article_contents$_tds</tr>";
				unset($_main_article_contents);
			}
		}

		// 게시판 이름에 링크 설정
		$board_infos['name'] = "<a href='$this->index_url/index.html?id=$board_infos[id]' class='main_board_title'>$board_infos[name]</a>";

		// 컬럼 스타일 지정 - 한줄에 게시판을 1개/2개 출력할 경우 td 스타일 지정
		$column_style = $category_infos->mbnum==2 ? !($column%2) ? " style='padding-right:7px;'" : " style='padding-left:7px;'" : " colspan='2'";
		// 스킨 URL
		$this->skin_url = $this->board_url."skin/main/".$category_infos->mskin."/";
		ob_start();
		include $this->get_board_template($this->get_skin_dir($category_infos->mskin)."/article_list.tpl.html", "main");
		return ob_get_clean();
	}

	// BEST 게시물 구성 - 기생루틴 - 2009.10.28 fixed
	function _get_formalize_articles($rows, $string=true) {
		$board_table = $this->board_prefix.$rows['bid'];
		$board_datas = $this->queryFetch("select b.uid, b.subject, b.cno, b.cnum, b.sval, b.dval, c.sfunction, c.scategory, c.spermission from $board_table as b, $this->bconfig_table as c where c.id='$rows[bid]' and c.uval='yes' and b.no=$rows[ano]");
		if(!empty($board_datas['cno'])) {
			$sfunction = @unserialize($board_datas['sfunction']);
			if($sfunction['use_category']=="on") {
				$scategory = @unserialize($board_datas['scategory']);
				$category = $scategory[$board_datas['cno']]['name'];
			}
		}
		$category = empty($category) ? '' : "[$category] ";
		$cnum = $board_datas['cnum']>0 ? " <span class='num'>[{$board_datas[cnum]}]</span>" : '';

		// 게시물 링크 생성
		parse_str($_SERVER['QUERY_STRING'], $query_infos);
		unset($query_infos['no'], $query_infos['pcno']); // 게시물 번호/상위메뉴 번호는 제거
		$query_infos['id'] = $rows['bid']; // 게시판 id 재설정
		$board_links = http_build_query($query_infos); // php5 이상, rankup_basic.class.php 에 정의됨
		$article_link = "href=\"$this->index_url/index.html?$board_links&no=$rows[ano]\"";

		// 글로벌 권한 보관 - 2009.10.28 added
		$keep_spermission = $this->spermission;

		// 비밀글일 경우
		$this->spermission = @unserialize($board_datas['spermission']);
		if($board_datas['sval']=="yes" && !$this->is_seeable($rows['ano']) && !$this->check_granted("secret_level") && (empty($board_datas['uid']) || (!empty($board_datas['uid']) && $this->member_id!==$board_datas['uid']))) {
			$article_link .= " onClick=\"rankup_board.scanf_passwd($rows[ano], this, 'article_view', '$rows[bid]'); return false;\"";
		}
		$secret_icon = $board_datas['sval']=="yes" ? " <img src='".$this->board_url."icon/icon_secret.gif' align='absmiddle'> " : '';

		// 게시물 제목
		if($board_datas['dval']=="yes") {
			$subject = "<strike>삭제된 게시물 입니다.</strike>";
			$article_link = "href=\"$this->index_url/index.html?$board_links&no=$rows[ano]\" style=\"color:#cdcdcd\"";
			if(!$this->is_admin()) $article_link .= " onClick=\"alert('삭제된 게시물은 조회할 수 없습니다.'+SPACE); return false;\"";
		}
		else $subject= $board_datas['subject'];
		$subject = "$category$secret_icon<a $article_link>$subject</a>";

		// 글로벌 권한 복구 - 2009.10.28 added
		$this->spermission = $keep_spermission;

		return ($string===true) ? $subject.$cnum : array($subject, $cnum);
	}

	// BEST 게시물
	function get_best_articles($mode, $pcno=false) { // pcno : 메인페이지의 경우 상위 메뉴중에서 베스트를 뽑는다.
		$addWhere = $pcno!==false? "b.pcno=$pcno" : "b.bid='$this->board_id'";
		switch($mode) {
			// 주간 베스트
			case "weekly_best":
				$limit = $this->main_layout['sprint']['wbest_num']; // 2009.08.31 added
				$best_datas = $this->queryFetchRows("select b.bid, b.adno, b.ano, sum(b.hnum) as hnums from $this->weekly_best_table as b, $this->bconfig_table as c where b.pcno=$pcno and b.bid=c.id and c.uval='yes' group by b.bid, b.ano order by hnums desc limit 0, $limit");
				if($this->check_resource($best_datas)) foreach($best_datas as $rows) $result .= "<tr><td nowrap><img src='".$this->skin_url."icon_arrow.gif'>".$this->_get_formalize_articles($rows)."</td></tr>";
				if(empty($result)) $result = "<tr><td nowrap><img src='".$this->skin_url."icon_arrow.gif'>등록된 베스트 게시물이 없습니다.</td></tr>";
				break;

			// 조회수 베스트
			case "hit_best":
				if($pcno!==false) $limit = $this->main_layout['sprint']['hcbest_num']; // 메인페이지 - 2009.08.31 added
				else $limit = $this->soption['hit_best_num']; // 게시판목록/상세페이지
				$best_datas = $this->queryFetchRows("select b.bid, b.adno, b.ano, b.ahnum from $this->hit_best_table as b, $this->bconfig_table as c where $addWhere and b.bid=c.id and c.uval='yes' group by b.bid, b.ano order by b.ahnum desc limit 0, $limit");
				if($this->check_resource($best_datas)) foreach($best_datas as $rows) $result .= "<tr><td nowrap style='overflow:hidden;text-overflow:ellipsis;'><img src='".$this->skin_url."icon_arrow.gif'>".$this->_get_formalize_articles($rows)."</td></tr>";
				if(empty($result)) $result = "<tr><td nowrap><img src='".$this->skin_url."icon_arrow.gif'>등록된 베스트 게시물이 없습니다.</td></tr>";
				break;

			// 댓글 베스트
			case "comment_best":
				$limit = $this->main_layout['sprint']['hcbest_num']; // 2009.08.31 added
				$best_datas = $this->queryFetchRows("select b.bid, b.adno, b.ano, b.acnum from $this->comment_best_table as b, $this->bconfig_table as c where $addWhere and b.bid=c.id and c.uval='yes' order by b.acnum desc limit 0, $limit");
				if($this->check_resource($best_datas)) foreach($best_datas as $rows) $result .= "<tr><td nowrap style='overflow:hidden;text-overflow:ellipsis;'><img src='".$this->skin_url."icon_arrow.gif'>".$this->_get_formalize_articles($rows)."</td></tr>";
				if(empty($result)) $result = "<tr><td nowrap><img src='".$this->skin_url."icon_arrow.gif'>등록된 베스트 게시물이 없습니다.</td></tr>";
				break;

			// 주간 댓글수 베스트
			case "weekly_cbest":
				if($this->board_extension!==true) return '';
				$limit = $this->main_layout['sprint']['wcbest_num']; // 2009.08.31 modified
				$best_datas = $this->queryFetchRows("select b.uid, sum(b.cnum) as cnums, m.name from $this->weekly_cbest_table as b, $this->member_table as m, $this->bconfig_table as c where m.uid=b.uid and b.bid=c.id and c.uval='yes' group by b.uid order by cnums desc limit 0, $limit");
				if($this->check_resource($best_datas)) {
					$count = 0;
					foreach($best_datas as $rows) {
						$result .= "<tr><td nowrap><img src='".$this->base_url."images/rank_num".sprintf("%02d", ++$count).".gif'> $rows[name]</td><td align='center'>$rows[cnums]</td></tr>";
					}
				}
				break;
		}
		return $result;
	}

	// 신규 게시물
	function get_new_articles($category_infos, $pcno='') {
		$limit = $this->main_layout['sprint']['narticle_num']; // 2009.08.31 added
		$f_addWhere = !in_array($pcno, array('', null)) ? "n.pcno=$pcno and" : '';
		$new_datas = $this->queryFetchRows("select n.bid, n.ano from $this->new_article_table as n, $this->bconfig_table as c where $f_addWhere n.bid=c.id and c.uval='yes' order by n.no desc limit 0, $limit");
		if($this->check_resource($new_datas)) {
			$skin_url = $this->board_url."skin/main/".$category_infos->mskin."/";
			$skin_dir = $this->board_dir."skin/main/".$this->get_skin_dir($category_infos->mskin)."/";
			$tpl_buffer = $this->get_template_item($skin_dir."__text.tpl");
			foreach($new_datas as $rows) {
				list($subject, $cnum) = $this->_get_formalize_articles($rows, false);
				$result .= str_replace(
					array("{:board_skin:}", "{:subject:}", "{:cnum:}"),
					array($skin_url, $subject, $cnum),
					$tpl_buffer);
			}
		}
		if(empty($result)) $result = "<tr><td nowrap>등록된 게시물이 없습니다.</td></tr>";
		return $result;
	}


##########################################################################
## 사용자페이지 - 게시판 부분
##########################################################################
	// 게시판 설정 업데이트
	function update_board($datas) {
		switch($datas['cmd']) {
			// 게시판 순위 재설정
			case "set_direction":
				foreach($_POST['rank'] as $rank => $bno) {
					$_val['rank'] = $rank+1;
					$values = $this->change_query_string($_val);
					$this->query("update $this->bconfig_table set $values where no=$bno");
				}
				return true;
				break;

			// 게시판 사용여부 설정 - 2009.08.28 added
			case "set_used":
				$this->query("update $this->bconfig_table set uval='$datas[use]' where id='$datas[id]'");
				return true;
				break;

			// 게시물 수 재설정 - 게시물 등록/삭제시 사용
			case "set_anum":
				if($datas['plus_mode']===true) return $this->query("update $this->bconfig_table set anum=anum+1 where id='$this->board_id'");
				else return $this->query("update $this->bconfig_table set anum=if(anum=0, 0, anum-1) where id='$this->board_id'");
				break;

			// 메인페이지 구성 재설정
			case "set_layout":
				// 메인페이지 구성
				if($datas['category']=="main") {
					if($this->check_resource($datas['subject_length'])) {
						foreach(array_keys($datas['subject_length']) as $bno) {
							$_val['mval'] = @in_array($bno, $datas['bno']) ? "yes" : "no";		// 출력여부
							$_val['smlayout'] = serialize(array(
								"subject_length" => $datas['subject_length'][$bno],				// 제목길이
								"article_rows" => $datas['article_rows'][$bno],						// 게시물수
								"image_width" => $datas['image_width'][$bno],						// 이미지 가로크기
								"image_height" => $datas['image_height'][$bno],					// 이미지 세로크기
								"print_style" => $datas['print_style'][$bno]							// 출력형태
							));
							$values = $this->change_query_string($_val);
							$this->query("update $this->bconfig_table set $values where no=$bno");
						}
					}
					// 메인페이지 환경 설정
					$_xVal['smlayout'] = serialize(array(
						"mskin" => $datas['main_skin'],				// 메인페이지 스킨
						"mbnum" => $datas['mbnum'],					// 한줄에 출력할 게시판 수
						"sprint" => array(
							"narticle" => $datas['narticle'],				// 신규 게시물
							"narticle_num" => $datas['narticle_num']	// 신규 게시물 출력갯수 - 2009.08.28 added
						)
					));
					$values = $this->change_query_string($_xVal);
					$this->query("update $this->sconfig_table set $values");
				}
				// 메뉴별 메인페이지 구성
				else {
					// 메인페이지 사용여부 - pcmval, mbno --> $this->category_table 에 저장
					if($datas['pcmval']=="yes") {
						foreach(array_keys($datas['subject_length']) as $bno) {
							$_val['pcmval'] = @in_array($bno, $datas['bno']) ? "yes" : "no";	// 출력여부
							$_val['spcmlayout'] = serialize(array(
								"subject_length" => $datas['subject_length'][$bno],				// 제목길이
								"article_rows" => $datas['article_rows'][$bno],						// 게시물수
								"image_width" => $datas['image_width'][$bno],						// 이미지 가로크기
								"image_height" => $datas['image_height'][$bno],					// 이미지 세로크기
								"print_style" => $datas['print_style'][$bno]							// 출력형태
							));
							$values = $this->change_query_string($_val);
							$this->query("update $this->bconfig_table set $values where no=$bno");
						}
						$_xVal['lskin'] = $datas['left_skin'];			// 좌측 스킨
						$_xVal['mskin'] = $datas['main_skin'];		// 메인페이지 스킨
						$_xVal['mbnum'] = $datas['mbnum'];		// 한줄에 출력할 게시판 수
						$_xVal['sprint'] = serialize(array(
							"wbest" => $datas['wbest'],				// 이번주 베스트
							"hcbest" => $datas['hcbest'],				// 조회수/댓글수 베스트
							"narticle" => $datas['narticle'],			// 신규 게시물
							// 출력갯수 - 2009.08.28 added
							"wbest_num" => $datas['wbest_num'],		// 이번주 베스트
							"hcbest_num" => $datas['hcbest_num'],	// 조회수/댓글수 베스트
							"narticle_num" => $datas['narticle_num']	// 신규 게시물
						));
					}
					else {
						$_xVal['lskin'] = $datas['left_skin'];			// 좌측 스킨
						$_xVal['mbno'] = $datas['mbno'];				// 메인게시판
					}
					$_xVal['mval'] = $datas['pcmval'];
					$values = $this->change_query_string($_xVal);
					$this->query("update $this->category_table set $values where no=$datas[category]");
				}
				return true;
				break;

			// 게시판 분류 재설정
			case "set_category":
				// 기존값과 비교
				$scategory = $this->get_board_config($datas['bno'], "scategory");
				$scategory = @unserialize($scategory);
				if(!is_array($scategory)) $scategory = array();
				$_scategory = $this->sort_scategory($scategory);
				$rank = 1;
				$rank += $this->check_resource($_scategory) ? max(array_keys($_scategory)) : 0;
				$datas['canum'] = in_array($datas['cno'], array_keys($scategory)) ? $scategory[$datas['cno']]['anum'] : 0;
				if(empty($datas['cname'])) unset($scategory[$datas['cno']]); // 분류 삭제
				else { // 등록/수정
					$scategory[$datas['cno']] = array(
						"name" => $datas['cname'],						// 분류명
						"anum" => $datas['canum'],						// 등록된 게시물 수
						"rank" => $rank										// 순위 - 2009.09.18 added
					);
				}
				$_val['scategory'] = serialize($scategory);
				break;

			// 권한 재설정
			case "set_permission":
				$_val['spermission'] = serialize(array(
					"list_level" => $datas['list_level'],					// 리스트 접근 권한
					"read_level" => $datas['read_level'],				// 게시물 읽기 권한
					"write_level" => $datas['write_level'],				// 게시물 쓰기 권한
					"delete_level" => $datas['delete_level'],			// 게시물 삭제 권한
					"comment_level" => $datas['comment_level'],	// 코멘트 쓰기 권한
					"reply_level" => $datas['reply_level'],				// 답변글 쓰기 권한
					"notice_level" => $datas['notice_level'],			// 공지글 쓰기 권한
					"secret_level" => $datas['secret_level']			// 비밀글 읽기 권한
				));
				break;

			// 포인트 재설정
			case "set_point":
				// 게시판 별 설정
				$_val['spoint'] = serialize(array(
					"write_point" => @in_array("write", array_values($datas['event'])) ? $datas['minus']['write']=="on" ? -(abs($datas['write_point'])) : abs($datas['write_point']) : '', // 게시물 쓰기
					"read_point" => @in_array("read", array_values($datas['event'])) ? $datas['minus']['read']=="on" ? -(abs($datas['read_point'])) : abs($datas['read_point']) : '', // 게시물 읽기
					"comment_point" => @in_array("comment", array_values($datas['event'])) ? $datas['minus']['comment']=="on" ? -(abs($datas['comment_point'])) : abs($datas['comment_point']) : '', // 코멘트 쓰기
					"reply_point" => @in_array("reply", array_values($datas['event'])) ? $datas['minus']['reply']=="on" ? -(abs($datas['reply_point'])) : abs($datas['reply_point']) : '', // 답변글 쓰기
					"vote_point" => @in_array("vote", array_values($datas['event'])) ? $datas['minus']['vote']=="on" ? -(abs($datas['vote_point'])) : abs($datas['vote_point']) : '', // 게시물 추천
					"upload_point" => @in_array("upload", array_values($datas['event'])) ? $datas['minus']['upload']=="on" ? -(abs($datas['upload_point'])) : abs($datas['upload_point']) : '', // 파일 업로드
					"download_point" => @in_array("download", array_values($datas['event'])) ? $datas['minus']['download']=="on" ? -(abs($datas['download_point'])) : abs($datas['download_point']) : '' // 파일 다운로드
				));
				break;

			// 메뉴내 게시판 이동
			case "move_board":
				$datas['bno'] = explode("__", $datas['datas']);
				$_val['pcno'] = $datas['pcno'];
				$_val['cno'] = $datas['cno'];
				if(empty($datas['cno'])) $_val['cno'] = $datas['pcno']; // 상위카테고리와 동일하게 설정

				$rank = $this->queryR("select rank from $this->bconfig_table where cno=$_val[cno] order by rank desc limit 1");
				$_val['rank'] = $rank+1; // 순위를 모두 동일하게 지정

				$values = $this->change_query_string($_val);
				$addWhere = count($datas['bno'])>1 ? "no in (".@implode(",", $datas['bno']).")" : "no=$datas[datas]";
				$this->query("update $this->bconfig_table set $values where $addWhere");

				// 이동하는 쪽 카테고리(메뉴)에 bval 이 no 인경우 처리
				$menu_infos = $this->queryFetch("select bval, mbno, mskin, lskin from $this->category_table where no=$_val[cno]");
				if($menu_infos['bval']=="no") {
					$_mbno = count($datas['bno'])>1 ? array_shift($datas['bno']) : $datas['datas'];
					$this->query("update $this->category_table set bval='yes', mbno=$_mbno where no=$_val[cno] and bval='no'");
				}

				// 해당 카테고리에 게시판이 존재하지 않을 경우 bval, mbno 초기화
				$board_no = $this->queryR("select no from $this->bconfig_table where cno=$datas[prev_cno] order by rank");
				if(empty($board_no)) $this->query("update $this->category_table set bval='no', mbno=NULL where no=$datas[prev_cno]");
				else {
					$addWhere = count($datas['bno'])>1 ? " and mbno in (".@implode(",", $datas['bno']).")" : " and mbno=$datas[datas]";
					$this->query("update $this->category_table set mbno=$board_no where no=$datas[prev_cno]$addWhere"); // mbno 변경
				}
				return true;
				break;

			// 분류내 게시물 이동
			case "move_articles":
				$cnos = explode("__", $datas['datas']);
				$board_datas = $this->get_board_config($datas['bno'], "id, scategory");
				// 등록된 게시물 수 초기화
				$scategory = @unserialize($board_datas['scategory']);
				foreach($scategory as $key=>$val) if(in_array($key, $cnos)) $scategory[$key]['anum'] = 0;

				@include "scheme/rankup_board_scheme.inc.html";
				$board_table_name = str_replace("scheme", $board_datas['id'], array_shift(array_keys($_BOARD_TABLES)));
				$this->query("update $board_table_name set cno=$datas[cno] where cno in(".@implode(",", $cnos).")");

				// 분류내 게시물 수 리카운트
				$scategory[$datas['cno']]['anum'] = $this->queryR("select count(cno) from $board_table_name where cno=$datas[cno]");
				$_val['scategory'] = serialize($scategory);
				break;
		}
		$values = $this->change_query_string($_val);
		$addWhere = is_array($datas['bno']) ? "no in (".@implode(",", $datas['bno']).")" : "no=$datas[bno]";
		return $this->query("update $this->bconfig_table set $values where $addWhere");
	}

	// 좌측 메뉴 출력
	function print_left_menu($skin='') {
		$pcno = $this->board_configs['pcno'] ? $this->board_configs['pcno'] : $_GET['pcno'];
		if(in_array($pcno, array('', "main"))) unset($pcno);
		if($this->board_extension===true && empty($pcno)) return false;
		else if(empty($pcno)) {
			$pcno = $this->queryR("select no from $this->category_table where pno=0 and dval='no' order by rank limit 0,1");
			if(empty($pcno)) $this->popup_msg_js("게시판이 존재하지 않습니다.", "BACK");
		}
		if(empty($pcno)) return false; // pcno 가 없는 경우 좌측메뉴 비활성화

		$category_infos = $this->queryFetchObject("select no, content, mbno, mval, lskin, mbnum from $this->category_table where no=$pcno and uval='yes' and dval='no'");
		if(empty($category_infos->lskin)) $this->popup_msg_js("게시판 좌측 메뉴의 스킨이 설정되지 않았습니다.", "BACK");
		$left_skin_url = $this->board_url."skin/left/".$category_infos->lskin."/";
		$left_skin_dir = $this->board_dir."skin/left/".$this->get_skin_dir($category_infos->lskin)."/";

		$board_datas = $this->queryFetchRows("select c.no, c.id, c.name, c.cno, c.pcno, c.soption from $this->category_table as m1, $this->category_table as m2, $this->bconfig_table as c where c.pcno=$pcno and c.uval='yes' and (m1.no=c.pcno and m1.pval='yes' and m2.no=c.cno and m2.pval='yes') order by m1.rank, m2.pno, m2.rank, m1.pno, c.rank");
		if($this->check_resource($board_datas)) {
			$tpl_item_buffer = $this->get_template_item($left_skin_dir."__item.tpl");
			$left_menu_contents = '';
			$sub_menu_division = str_replace("{:left_menu_skin:}", $left_skin_url, $this->get_template_item($left_skin_dir."__division.tpl"));
			foreach($board_datas as $board_infos) {
				// new icon - 2010.06.23 added
				$new_icon = '';
				$soptions = @unserialize($board_infos['soption']);
				if($soptions['use_new_icon']=="on") {
					$_new_articles = $this->queryR("select count(no) from ".$this->board_prefix.$board_infos['id']." where wdate>=date_sub(curdate(), interval ".$soptions['recent_time']." hour)");
					if($_new_articles) $new_icon = " <img src='".$this->board_url."icon/icon_new.gif' align='absmillde'>";
				}
				$board_name = "<a href='$this->index_url/index.html?id=$board_infos[id]'>$board_infos[name]</a>".$new_icon;
				// 메뉴가 바뀌면 분할
				if($old_menu_cno != $board_infos['cno']) {
					if(!empty($left_menu_contents)) $left_menu_contents .= $sub_menu_division;
					if($old_menu_cno) {
						if($board_infos['cno']!=$board_infos['pcno']) $menu_name = $this->queryR("select content from $this->category_table where no=$old_menu_cno");
						ob_start();
						include $this->get_board_template($this->get_skin_dir($category_infos->lskin)."/menu_item.tpl.html", "left");
						$left_menu_contents .= ob_get_clean();
						unset($board_item_contents, $menu_name);
					}
					$old_menu_cno = $board_infos['cno'];
				}
				$board_item_contents .= str_replace(
					array("{:left_menu_skin:}", "{:board_name:}"),
					array($left_skin_url, $board_name),
					$tpl_item_buffer);
			}
			if(isset($board_item_contents)) {
				if(!empty($left_menu_contents)) $left_menu_contents .= $sub_menu_division;
				if($old_menu_cno && $board_infos['cno']!=$board_infos['pcno']) $menu_name = $this->queryR("select content from $this->category_table where no=$old_menu_cno");
				ob_start();
				include $this->get_board_template($this->get_skin_dir($category_infos->lskin)."/menu_item.tpl.html", "left");
				$left_menu_contents .= ob_get_clean();
				unset($board_item_contents);
			}
		}
		// 메뉴 링크 설정
		$category_name = "<a href='$this->index_url/index.html?pcno=$category_infos->no'>$category_infos->content</a>";
		ob_start();
		include $this->get_board_template($this->get_skin_dir($category_infos->lskin)."/left_menu.tpl.html", "left");
		return ob_get_clean();
	}

	// 게시판 분류 정렬 - 관리자 - 2009.09.18 added
	function sort_scategory($categories) {
		$scategory = array();
		foreach($categories as $cno=>$rows) {
			if(!$rows['name']) continue;
			$scategory[$rows['rank']] = array(
				'cno' => $cno,
				'anum' => $rows['anum'],
				'name' => $rows['name']
			);
		}
		ksort($scategory);
		return $scategory;
	}

	// 썸네일 이미지 추출
	function get_thumbnail_image($attach, $attach_dir) {
		$thumbnail_file = '';
		$attaches = @unserialize($attach);
		if(!$this->check_resource($attaches)) return false;
		foreach($attaches as $file) {
			if(!is_file($attach_dir.$file['sname'])) continue;
			list($image_width, $image_height, $image_type) = @getimagesize($attach_dir.$file['sname']);
			if(!$image_width || !$image_height || !in_array($image_type, array(1,2,3,6))) continue; // jpg, png 만 썸네일
			$thumbnail_file = is_file($attach_dir."thumb_".$file['sname']) ? "thumb_".$file['sname'] : $file['sname'];
			break;
		}
		return array($thumbnail_file, $image_width, $image_height, $image_type);
	}

	//글작성자 회원정보 반환
	function get_writer_infos($datas) {
		global $config_info;
		if(is_object($datas)) { //데이터를 호출하는 위치에 따라 변형해서 사용
			$uid = $datas->uid;
			$unick = $datas->unick;
		} else {
			$uid= $datas['uid'];
			$unick = $datas['unick'];
		}
		if(empty($uid)) {		//비회원인경우
			$return_data = $unick;
		} else if($uid == "_admin_") {		//비회원인경우
			$return_data = $unick;
		} else {
			if($this->sfunction['use_writer'] == "uid") $return_data = $uid ? $uid : $unick;
			else $return_data = $unick;
		}
		return $return_data;
	}

	// 리스트 구성 - 공용 - 2009.09.18 modified
	function formalize_board_articles($datas, $view_page=false) { // view_page : 상세페이지 인지 여부
		if($this->sfunction['use_category']=="on") {
			$categories = @unserialize($this->board_configs['scategory']);
			if($this->check_resource($categories)) {
				$scategory = $this->sort_scategory($categories); // 2009.09.18 added
				$scategories = "<option value=''>전체분류</option>";
				foreach($scategory as $rank=>$val) {
					$_selected = $datas['scategory']==$val['cno'] ? " selected" : '';
					$scategories .= "<option value='$val[cno]'$_selected>$val[name]</option>";
				}
			}
			else $scategories = "<option value=''>분류없음</option>";
			$article_column = "<select name='scategory' onChange=\"rankup_board.search_category(this)\">".$scategories."</select>";
			if($this->board_configs['style']!='webzin') {
				$article_column = "<td class='board_title' width='120' background='".$this->skin_url."title_bg.gif' align='left'>$article_column</td>";
			}
		}
		if(!$this->board_configs['style']) $this->board_configs['style'] = "normal";
		if(in_array($this->board_configs['style'], array('gallery', 'webzin'))) { // 2009.06.22 fixed
			$sgallery = unserialize($this->board_configs['sgallery']);
			$gallery_cell_width = $sgallery['thumb_nums']>1 ? 100/$sgallery['thumb_nums'] : 3;
			$board_category_view = $this->sfunction['use_category']=="on" ? "block" : "none";
			$attach_dir = $this->board_dir."attach/".$this->board_id."/";
			$attach_url = $this->board_url."attach/".$this->board_id."/";
			$column_count = 0;
		}

		// 삭제권한이 있을 경우 하단에 선택삭제 버튼 설정
		if($view_page===false && $this->check_granted("delete_level")) {
			$sdelete_button = "<a onClick=\"rankup_board.check_all($('sAllButton').src.indexOf('bt_sall.gif')!=-1, $('sAllButton'))\"><img id='sAllButton' src='".$this->board_url."img/bt_sall.gif' align=''></a> <a onClick=\"rankup_board.articles_delete()\"><img src='".$this->board_url."img/bt_sdel.gif' align=''></a>";
		}

		// 템플릿 조각파일 로드
		if($this->board_configs['style'] == "mantoman") $tpl_buffer = $this->get_template_item($this->skin_dir."__normal.tpl");
		else $tpl_buffer = $this->get_template_item($this->skin_dir."__".$this->board_configs['style'].".tpl");

		if(empty($datas['page'])) $datas['page'] = 1;
		$spos = $datas['page']>1 ? ($datas['page']-1)*$this->slayout['article_rows'] : 0;

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
		// 검색어가 존재할 경우
		if(!empty($datas['skey'])) {
			switch($datas['smode']) {
				case "both": // 제목+본문 검색
					$addWhere .= " and (subject like '%$datas[skey]%' or content like '%$datas[skey]%')";
					break;
				case "subject": // 제목 검색
					$addWhere .= " and (subject like '%$datas[skey]%')";
					break;
				case "author": // 작성자 검색
					$addWhere .= " and (unick like '%$datas[skey]%')";
					break;
			}
		}
		// 분류를 선택한 경우
		if($this->sfunction['use_category']=="on" && $this->check_resource($categories) && !empty($datas['scategory'])) $addWhere .= " and cno=$datas[scategory]";

		// 정렬 값이 존재할 경우
		if(!empty($datas['asort'])) {
			if(empty($datas['skey'])) { // dno 추가 @###################
				//
			}
		}
		switch($datas['asort']) {
			case "hit": $orderBy = " hnum desc"; break;
			case "hot": $orderBy = " cnum desc"; break;
			//case "vote": $orderBy = " vnum desc"; break;
			case "good": $orderBy = " gnum desc"; break;
			case "bad": $orderBy = " bnum desc"; break;
			case "recent": unset($datas['asort']); // $orderBy = " wdate desc"; break; // 정렬방식 초기화 개념으로 변경
			default: $orderBy = " sno, gno";
		}

		// 갤러리형태 일때에는 삭제대기중인 게시물도 제외 -- 관리자가 아닌경우만 제외
		if($this->board_configs['style']=="gallery" && !$this->is_admin()) $addWhere .= " and dval='no'";

		// 게시판 링크 생성
		parse_str($_SERVER['QUERY_STRING'], $query_infos);
		$query_infos['id'] = $this->board_id; // 게시판 id 재설정
		unset($query_infos['pcno']); // 메뉴 번호 제거
		$_SERVER['QUERY_STRING'] = http_build_query($query_infos); // 2009.10.14 fixed
		$_SERVER['REQUEST_URI'] = "$this->index_url/index.html?".http_build_query($query_infos); // 페이징 기본설정
		unset($query_infos['no']); // 게시물 번호 제거
		$board_links = http_build_query($query_infos);

		//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
		$total_articles = $this->queryR("select count(no) from $this->board_table where no$addWhere");
		$paging_button = $this->print_paging($total_articles, array($this->slayout['article_rows'], 10), 'page');
		$anum = $total_articles-($spos);

		$board_datas = $this->queryFetchRows("select no,sno,gno,pno,subject,cno,uid,unick,content,attach,cnum,hnum,gnum,bnum,wdate,sval,dval from $this->board_table where no$addWhere order by $orderBy limit $spos, ".$this->slayout['article_rows']);
		if($this->check_resource($board_datas)) {
			foreach($board_datas as $rows) {
				$rows['unick'] = $this->get_writer_infos($rows);
				$_category = $this->sfunction['use_category']=="on" ? $categories[$rows['cno']]['name'] : '';
				$article_link = "href=\"./index.html?$board_links&no=$rows[no]\""; // $this->index_url - 2009.09.09 without

				//비밀글일경우 최초등록자글의 정보를 가지고온다
				if($rows['pno']>0 && $rows['sval']=="yes") $read_writer = $this->queryR("select uid from $this->board_table where sno = $rows[sno] and pno = 0 and uid='$this->member_id'");
				else $read_writer = null;

				// 비밀글일 경우
				if($rows['sval']=="yes" && !$this->is_seeable($rows['no']) && !$this->check_granted("secret_level") && (empty($rows['uid']) || (!empty($rows['uid']) && $this->member_id!==$rows['uid']))) {
					if($read_writer == null || $read_writer !== $this->member_id) {
						$article_link .= " onClick=\"rankup_board.scanf_passwd($rows[no], this, 'article_view'); return false;\"";
					}
				}

				// 아이콘 처리 - 첨부파일 / NEW 아이콘
				$attach_icon = ($this->sattach['use_attach']=="on" && $this->soption['use_attach_icon']=="on" && !empty($rows['attach'])) ? "<img src='".$this->board_url."icon/icon_file.gif' align='absmillde'> " : '';
				$new_icon = ($this->soption['use_new_icon']=="on" && date("Y-m-d H:i:s", strtotime("-{$this->soption['recent_time']} hour"))<=$rows['wdate']) ? " <img src='".$this->board_url."icon/icon_new.gif' align='absmillde'>" : '';
				$secret_icon = $rows['sval']=="yes" ? " <img src='".$this->board_url."icon/icon_secret.gif' align='absmiddle'> " : '';

				// 게시물 작성 일시
				list($_date, $_time) = explode(" ", $rows['wdate']);
				$rows['wdate'] = ($_date == date("Y-m-d")) ? $_time : $_date;

				// 게시물 제목
				if($rows['dval']=="yes") {
					$subject = "<strike>삭제된 게시물 입니다.</strike>";
					$article_link = "href=\"./index.html?$board_links&no=$rows[no]\" style=\"color:#cdcdcd\""; // $this->index_url - 2009.09.09 without
					if(!$this->is_admin()) $article_link .= " onClick=\"alert('삭제된 게시물은 조회할 수 없습니다.'+SPACE); return false;\"";
				}
				else {
					$subject= $this->str_cut($rows['subject'], $this->slayout['subject_length'], ($this->slayout['use_condense']=="on" ? "..." : ''));
					// 검색어 색상 지정 - str_cut 으로 검색어 자체가 잘려 나가는 경우는 제외
					if(!empty($datas['skey']) && in_array($datas['smode'], array("both", "subject"))) {
						$subject = str_replace($datas['skey'], "<font color='#FF6600'>$datas[skey]</font>", $subject);
					}
				}

				// 첨부파일 중 썸네일이 가능한 파일로 추출
				$thumbnail = '';
				list($thumbnail_file, $_width, $_height, $_type) = $this->get_thumbnail_image($rows['attach'], $attach_dir);
				if(!empty($thumbnail_file)) {
					// 가로 대비 축소
					if($_width>$sgallery['thumb_width']) {
						$_height = $_height / ($_width / $sgallery['thumb_width']);
						$_width = $sgallery['thumb_width'];
					}
					// 세로 대비 축소
					if($_height>$sgallery['thumb_height']) {
						$_width = $_width / ($_height / $sgallery['thumb_height']);
						$_height = $sgallery['thumb_height'];
					}
					$thumbnail_file = $attach_url.$thumbnail_file;
					// 썸네일 이미지 구성
					$thumbnail = "<a $article_link><img src='$thumbnail_file' width='$_width' height='$_height' align='absmiddle'></a>";
				}
				else if(in_array($this->board_configs['style'], array('gallery', 'webzin'))) {
					$thumbnail_file = $this->board_url."img/no_thumb.gif";
					$_width = $sgallery['thumb_width'];
					$_height = $sgallery['thumb_height'];
					// 썸네일 이미지 구성
					$thumbnail = "
					<div style='width:{$_width}px;height:{$_height}px;background-color:white;'>
						<table width='100%' height='100%' cellspacing='0' cellpadding='0'>
						<tr align='center'><td><a $article_link onFocus='blur()'><img src='$thumbnail_file'></a></td></tr>
						</table>
					</div>";
				}

				// 일반형 / 웹진형일 경우
				if(in_array($this->board_configs['style'], array('normal','mantoman', 'webzin'))) {
					$_anum = $anum;

					// 현재 보고 있는 게시물의 경우
					if($datas['no'] == $rows['no']) $_anum = "<b style='color:black'>☞</b>";

					// 답글의 경우 - 게시물 제목 왼쪽에 여백추가
					if(empty($datas['asort'])) { // 정렬방식이 지정된 경우 에는 들여쓰기 하지 않음
						if($rows['pno']==0) $reply_icon = '';
						else {
							$reply_icon = $this->soption['use_reply_icon']=="on" ? " <img src='".$this->board_url."icon/icon_re.gif' align='absmillde'>" : "└";
							$reply_icon = "<span style='margin-left:".(10*$rows['pno']).";margin-right:4px;'>$reply_icon</span>";
						}
					}
					// 댓글수
					$cnum = $rows['cnum']>0 ? " [{$rows[cnum]}] " : '';

					// 삭제권한이 있을 경우 번호 란을 체크박스로 변경
					if($view_page===false && $this->check_granted("delete_level")) $_anum = "<input onFocus='blur()' type='checkbox' name='ano[]' value='$rows[no]' class='scheckbox'>";

					// 일반형일 경우 - 썸네일 제거
					if(in_array($this->board_configs['style'], array('normal', 'mantoman'))) {
						// 게시판 분류
						if(!empty($_category)) $_category = "<td align='left'>$_category</td>";
						else if($this->sfunction['use_category']=="on") $_category = "<td align='left'>&nbsp;</td>";

						// 게시물 제목 구성
						$subject = "$reply_icon$secret_icon$attach_icon<a $article_link>".$subject."</a>$cnum$new_icon";

						// 공지글 처리
						if($rows['sno']<=$this->notice_sno) {
							$_anum = "<b>공지</b>";
							$subject = "<b>$subject</b>";
							if(!empty($_category)) $_category = "<td>&nbsp;</td>";
							// 정렬하지 않을경우에만 배경색 적용
							if(empty($datas['asort'])) $notice_class = " class='notice'";
						}
						else $notice_class = '';
						// 추천수
						if($this->sfunction['use_vote']=="on") {
							$_gnum = "<td>$rows[gnum]</td>";
							if($this->sfunction['use_only_good']!="on") $_bnum = "<td>$rows[bnum]</td>";
						}
						$article_contents .= str_replace(
							array("{:board_skin:}", "{:notice_class:}", "{:anum:}", "{:category:}", "{:subject:}", "{:author:}", "{:gnum:}", "{:bnum:}", "{:hnum:}", "{:wdate:}"),
							array($this->skin_url, $notice_class, $_anum, $_category, $subject, $rows['unick'], $_gnum, $_bnum, $rows['hnum'], $rows['wdate']),
							$tpl_buffer);
					}
					else { // 웹진형

						// 게시물 제목 구성
						$subject = "$secret_icon$attach_icon<a $article_link>".$subject."</a>$cnum$new_icon";

						// 공지글 처리
						if($rows['sno']<=$this->notice_sno) {
							$thumbnail = $content = $_category = $wz_article = '';
							$_anum = "<b>공지</b>";
							$subject = "<b>$subject</b>";
							$content_class = " style='display:none'";

							// 정렬하지 않을경우에만 배경색 적용
							if(empty($datas['asort'])) $notice_class = " class='notice'";
						}
						else {
							$wz_article = " class='wz_article'";
							$notice_class = $content_class = $_gnum = $_bnum = '';
							$_category = str_replace('&nbsp;', '', $_category);
							if($_category) $_category = "[$_category]";

							if($rows['dval']=="yes") { // 삭제된 경우
								$thumbnail = '';
								$content_class = " style='display:none'";
							}
							else $content = "<a $article_link style='color:#999999'>".$this->str_cut(strip_tags($rows['content']), 200, '...')."</a>";

							// 추천수
							if($rows['dval']!="yes" && $this->sfunction['use_vote']=="on") {
								$_gnum = "추천: $rows[gnum]";
								if($this->sfunction['use_only_good']!="on") list($_gnum, $_bnum) = array("<nobr>[ $_gnum,", "반대: $rows[bnum] ]</nobr>");
								else $_gnum = "<nobr>[ $_gnum ]</nobr>";
							}
						}
						$article_contents .= str_replace(
							array("{:board_skin:}", "{:notice_class:}", "{:wz_article:}",  "{:anum:}", "{:thumbnail:}", "{:reply_icon:}", "{:category:}", "{:subject:}", "{:content:}", "{:content_class:}", "{:author:}", "{:gnum:}", "{:bnum:}", "{:hnum:}", "{:wdate:}"),
							array($this->skin_url, $notice_class, $wz_article, $_anum, $thumbnail, $reply_icon, $_category, $subject, $content, $content_class, $rows['unick'], $_gnum, $_bnum, $rows['hnum'], $rows['wdate']),
							$tpl_buffer);
					}
				}
				// 갤러리형일 경우
				else {
					if(empty($_category)) $_category = "<span style='font-size:1px;'>&nbsp;</span>";

					// 댓글수
					$cnum = $rows['cnum']>0 ? " <span style='font-family:Arial;font-size:6pt;color:#acacac;'>{$rows[cnum]}</span> " : '';

					// 삭제권한이 있을 경우 번호 란을 체크박스로 변경
					if($view_page===false && $this->check_granted("delete_level")) $scheckbox = "<input onFocus='blur()' type='checkbox' name='ano[]' value='$rows[no]' class='scheckbox2'> ";

					// 게시물 제목 구성
					$subject = "$secret_icon<a $article_link>".$subject."</a>$cnum";
					$_article_contents .= str_replace(
						array("{:board_skin:}", "{:gallery_width:}", "{:category:}", "{:thumbnail:}", "{:subject:}", "{:author:}", "{:hnum:}", "{:wdate:}"),
						array($this->skin_url, $sgallery['thumb_width']+10, $_category, $thumbnail, $subject, $rows['unick'], $rows['hnum'], $scheckbox.$rows['wdate'].$new_icon),
						$tpl_buffer);

					if(!(++$column_count%$sgallery['thumb_nums'])) {
						$article_contents .= "
						<tr>$_article_contents</tr>";
						unset($_article_contents);
					}
				}
				$anum--;

			} // @eo  foreach($board_datas as $rows)
		}
		// 갤러리형 빈셀 채우기
		if(isset($_article_contents)) {
			$_tds = str_repeat("<td>&nbsp;</td>", $sgallery['thumb_nums']-($column_count%$sgallery['thumb_nums']));
			$article_contents .= "
			<tr>$_article_contents$_tds</tr>";
			unset($_article_contents);
		}
		return array($article_column, $article_contents, $paging_button, $gallery_cell_width, $board_category_view, $scategories, $sdelete_button);
	}

	// 해당 게시판 게시물 목록 구성
	function get_board_articles($datas) {
		// 접근 권한체크
		if(!$this->check_granted("list_level")) $this->popup_msg_js($this->get_granted_messages('list_level'), "BACK");

		list($article_column, $article_contents, $paging_button, $gallery_cell_width, $board_category_view, $scategories, $sdelete_button) = $this->formalize_board_articles($datas);
		// 게시판명을 타이틀에 반영하기 위해 추가 - 2009.03.06
		$this->subject = $this->board_name;

		// 링크 설정
		parse_str($_SERVER['QUERY_STRING'], $query_infos);
		unset($query_infos['no'], $query_infos['pcno']); // 게시물 번호, 상위 카테고리 번호는 제거
		$query_infos['id'] = $datas['id']; // 게시판 아이디 지정
		$board_links = http_build_query($query_infos); // php5 이상, rankup_basic.class.php 에 정의됨

		// 정렬 방식 링크 설정
		unset($query_infos['asort']);
		$sort_button_link = http_build_query($query_infos);

		ob_start();
		include $this->get_board_template($this->get_skin_dir()."/article_list.tpl.html");
		return ob_get_clean();
	}

	// 게시물 상세정보 로드
	function get_article($datas) {
		global $config_info;
		//SNS 기능을 사용하기 위해 인클루드
		include_once $this->base_dir."Libs/_php/rankup_sns.class.php";
		$rankup_sns = new rankup_sns;
		// 게시물 상세정보
		$board_infos = $this->queryFetchObject("select * from $this->board_table where no=$datas[no]");

		//내가 쓴글에 답글이 달린경우 -- 2009.11.27
		if($board_infos->pno > 0 && $board_infos->sval=="yes") $read_writer = $this->queryR("select uid from $this->board_table where sno = $board_infos->sno and pno = 0 and uid='$this->member_id'");
		else $read_writer = null;

		// 접근 권한체크
		if(!$this->is_member() || ($this->is_member() && $board_infos->uid!==$this->member_id && $this->member_id !== $read_writer)) {
			if(!$this->is_seeable($datas['no']) && !$this->check_granted("read_level")) $this->popup_msg_js($this->get_granted_messages('read_level'), "BACK");
		}
		if(empty($board_infos->no)) $this->popup_msg_js("요청하신 게시물은 존재 하지 않습니다.", "BACK");
		if($board_infos->dval=="yes" && !$this->is_admin()) $this->popup_msg_js("요청하신 게시물은 삭제된 게시물입니다.", "BACK");
		if($board_infos->sval=="yes" && !$this->is_seeable($datas['no'])) { // 2009.09.17 fixed
			if(!$this->member_id || ($this->member_id && $board_infos->uid!==$this->member_id && $this->member_id !== $read_writer)) {
				if(!$this->check_granted("secret_level")) $this->popup_msg_js($this->get_granted_messages('secret_level'), "BACK");
			}
		}

		// 게시물 제목을 타이틀에 반영하기 위해 추가 - 2009.03.06 added
		$this->subject = $board_infos->subject;

		//상세정보 이미지 크기 자동적용
		$sgallery = unserialize($this->board_configs['sgallery']);
		$view_imgae_width = $sgallery['picture_width'] < $this->slayout['board_width'] ? $sgallery['picture_width'] : $this->slayout['board_width']-40; //가장적당한 크기 게시판 크기에서 -40 정도
		$board_infos->content = $this->prefix_contents($board_infos->content, $view_imgae_width);

		// 다른 게시물이면 임시허가 세션 제거
		if($_SESSION[$_COOKIE['rbUser']]!==$this->board_id.$datas['no']) unset($_SESSION[$_COOKIE['rbUser']]);

		/*// 게시물 작성 시간 노출
		list($_date, $_time) = explode(" ", $board_infos->wdate);
		$board_infos->wdate = ($_date==date("Y-m-d")) ? $_time : $_date;
		*/
		$board_infos->wdate = array_shift(explode(" ", $board_infos->wdate));

		// 조회수 갱신 - 관리자가 아닌경우에만 처리
		if($this->sfunction['use_duplicate_hit']=="on") $this->increase_readcount($board_infos, true);
		else if(!$this->is_admin()) $this->increase_readcount($board_infos);

		// 쪽지보내기 / 회원정보 보기 버튼 추가
		$board_infos->author = $board_infos->unick;
		$board_infos->unick = $this->get_writer_infos($board_infos);
		if($this->board_extension===true && !empty($board_infos->uid) && $this->member_id!==$board_infos->uid) {
			// 작성자정보 버튼
			$board_infos->unick .= " <a onClick=\"rankup_board.get_author_info('$board_infos->uid')\"><img src='".$this->board_url."img/icon_info.gif' align='absmiddle' style='margin-top:-3px;' alt='작성자정보'></a>";
			// 쪽지보내기 버튼
			if($this->is_member()) $board_infos->unick .= " <a onClick=\"rankup_board.send_message('$_author', '$board_infos->uid')\"><img src='".$this->board_url."img/icon_msg.gif' align='absmiddle' style='margin-top:-3px;' alt='쪽지보내기'></a>";
		}

		// 이전글 / 다음글 로드 - 검색조건에 따른 작업 예외 처리 @#######
		if($this->soption['use_near_article']=="on") {
			/*// 정렬/검색조건이 있을 경우
			if(!in_array($datas['asort'], array('', 'recent'))) {
				// 이전글/다음글 찾기
				if(empty($datas['page'])) $datas['page'] = 1;
				$spos = $datas['page']>1 ? ($datas['page']-1)*$this->slayout['article_rows'] : 0;
				// 검색어가 존재할 경우
				if(!empty($datas['skey'])) {
					switch($datas['smode']) {
						case "both": // 제목+본문 검색
							$addWhere .= " and (subject like '%$datas[skey]%' or content like '%$datas[skey]%')";
							break;
						case "subject": // 제목 검색
							$addWhere .= " and (subject like '%$datas[skey]%')";
							break;
						case "author": // 작성자 검색
							$addWhere .= " and (unick like '%$datas[skey]%')";
							break;
					}
				}
				// 분류를 선택한 경우
				if($this->sfunction['use_category']=="on" && $this->check_resource($categories) && !empty($datas['scategory'])) $addWhere .= " and cno=$datas[scategory]";
				// 정렬 값이 존재할 경우
				switch($datas['asort']) {
					case "hit": $orderBy = " hnum desc"; break;
					case "hot": $orderBy = " cnum desc"; break;
					case "vote": $orderBy = " vnum desc"; break;
					case "recent": unset($datas['asort']); // $orderBy = " wdate desc"; break; // 정렬방식 초기화 개념으로 변경
					default: $orderBy = " sno, gno";
				}
				$this->queryFetchRows("select no, uid, subject, cnum, sval, dval from $this->board_table where no=$board_infos->nano");
			}
			*/
			if($board_infos->nano>0) {
				$next_article_infos = $this->queryFetch("select no, uid, subject, cnum, sval, dval from $this->board_table where no=$board_infos->nano");
				$next_article = $this->get_near_article($board_infos, $next_article_infos);
			}
			if($board_infos->pano>0) {
				$previous_article_infos = $this->queryFetch("select no, uid, subject, cnum, sval, dval from $this->board_table where no=$board_infos->pano");
				$previous_article = $this->get_near_article($board_infos, $previous_article_infos);
			}
		}
		// 첨부파일 사용여부
		if($this->sattach['use_attach']=="on") {
			$board_infos->attach = unserialize($board_infos->attach);
			if($this->check_resource($board_infos->attach)) {
				$attach_dir = $this->board_dir."attach/".$this->board_id."/";
				foreach($board_infos->attach as $key=>$file) {
					$file_size = array_pop($this->get_file_size($attach_dir.$file['sname']));
					if(!empty($attach_files)) $attach_files .= ", ";
					$attach_files .= "<span class='colA'><a href='$this->index_url/index.html?cmd=download&id=$this->board_id&ano=$datas[no]&fid=$key&fname=".urlencode($file['oname'])."'>$file[oname]</a></span><span class='colB'>($file_size)</span><span class='colC'>Download: $file[dnum]</span>";
				}
			}
		}
		/*// 검색어 색상 지정
		if(!empty($datas['skey']) && in_array($datas['smode'], array("both", "subject"))) {
			$board_infos->subject = str_replace($datas['skey'], "<font color='#FF6600'>$datas[skey]</font>", $board_infos->subject);
		}*/

		// 게시판 링크 설정
		parse_str($_SERVER['QUERY_STRING'], $query_infos);
		unset($query_infos['no']); // 게시물 번호는 제거
		$board_links = http_build_query($query_infos); // php5 이상, rankup_basic.class.php 에 정의됨

		// 상세 페이지 내 게시물 목록 사용
		if($this->soption['use_detail_list']=="on") {
			list($article_column, $article_contents, $paging_button, $gallery_cell_width, $board_category_view, $scategories) = $this->formalize_board_articles($datas, true);
		}
		ob_start();
		include $this->get_board_template($this->get_skin_dir()."/article_view.tpl.html");
		return ob_get_clean();
	}

	// 이전글/다음글 링크 생성
	function get_near_article($board_infos, $near_infos) {
		// 게시판 링크 생성
		parse_str($_SERVER['QUERY_STRING'], $query_infos);
		unset($query_infos['no']); // 게시물 번호는 제거
		$board_links = http_build_query($query_infos); // php5 이상, rankup_basic.class.php 에 정의됨

		$near_link = "href=\"$this->index_url/index.html?$board_links&no=$near_infos[no]\"";
		$near_subject = $near_infos['subject'];
		if($near_infos['sval']=="yes") {
			$near_secret_icon = "<img src='".$this->board_url."icon/icon_secret.gif' align='absmiddle'> ";
			if(!$this->check_granted("secret_level") && (empty($near_infos['uid']) || (!empty($near_infos['uid']) && $near_infos['uid']!=$this->member_id))) {
				$near_link = "href=\"$this->index_url/index.html?$board_links&no=$near_infos[no]\" onClick=\"rankup_board.scanf_passwd($near_infos[no], this, 'article_view'); return false;\"";
			}
		}
		if($near_infos['dval']=="yes") {
			$near_link = "href=\"$this->index_url/index.html?$board_links&no=$near_infos[no]\" style=\"color:#cdcdcd\"";
			if(!$this->is_admin()) $near_link .= " onClick=\"alert('삭제된 게시물은 조회할 수 없습니다.'+SPACE); return false;\"";
			$near_subject = "<strike>삭제된 게시물입니다.</strike>";
		}
		$near_cnum =  $near_infos['cnum']>0 ? " <span class=\"pre_next_num\">[".$near_infos['cnum']."]</span>" : '';
		return $near_article = $near_secret_icon."<a $near_link>$near_subject</a>$near_cnum";
	}

	// 게시물의 모드별(게시물목록/상세페이지/등록폼/수정폼 표시) 페이지 구성
	function get_board_contents($datas, $simple_mode=false) {
		// 불량회원인지 체크 - 접근차단 설정시 주석 해제
		//if($this->member_badness=="yes") $this->popup_msg_js("고객님은 불량회원으로 게시판 접근이 차단되었습니다.", $this->base_url."main/index.html");

		$this->simple_mode = $simple_mode; // 2009.09.09 added
		if($this->simple_mode==true) {
			$this->slayout['board_width'] = 750;
			//$this->board_configs['style'] = 'normal';
			$this->soption['use_hit_best'] = '';
			$this->soption['use_near_article'] = '';
			$this->soption['use_detail_list'] = '';

			// 관리자페이지에 로그인 중 게시물 작성시 회원로그인 정보 죽이기 - 2009.09.21 added
			if($this->is_administrator() && $datas['mode']=='write' && !$datas['no']) {
				$_SESSION[$this->member_session_id] = '';
				$this->member_name = $this->member_id = '';
				$this->member_level = 7;
			}
		}

		switch($datas['mode']) {
			// 등록/수정페이지
			case "write":
				// 불량회원인지 체크
				if(!$this->is_admin() && $this->member_badness=="yes") $this->popup_msg_js("고객님은 불량회원으로 게시물을 작성하실 수 없습니다.", $this->base_url."main/index.html");

				// 접근권한 체크
				if(!empty($datas['no']) && !$this->is_seeable($datas['no'])) $this->popup_msg_js("게시물 수정 권한이 없습니다.", "BACK");
				else {
					/// 답글권한
					if(!empty($datas['pano'])) {
						if($this->sfunction['use_reply']=="no") $this->popup_msg_js("'".$this->board_name."' 에는 답글을 등록할 수 없습니다.", "BACK");
						else if(!$this->check_granted("reply_level")) $this->popup_msg_js($this->get_granted_messages('reply_level'), "BACK");
					}
					else if(empty($datas['no']) && !$this->check_granted("write_level")) $this->popup_msg_js($this->get_granted_messages("write_level"), "BACK");
				}
				// 정크파일 체크
				$this->clear_junk_files(true);

				// 수정/답글일 경우 - 게시물 정보 로드
				$board_infos->unick = $this->member_name;
				$board_infos->content = $this->board_configs['scontent']; // 본문 기본값 // 2009.05.19 fixed
				if(!empty($datas['no']) || !empty($datas['pano'])) {
					$ano = !empty($datas['no']) ? $datas['no'] : $datas['pano'];
					$board_infos = $this->queryFetchObject("select no,subject,cno,uid,unick,content,sval,nval,dval from $this->board_table where no=$ano");
					if(!empty($datas['no']) && $board_infos->dval=="yes") $this->popup_msg_js("삭제된 게시물입니다.", "BACK");
					if(!empty($datas['pano'])) { // 답글 작성시
						// 삭제글에 답글을 작성할 경우
						if(!$this->is_replyable($board_infos)) $this->popup_msg_js("삭제된 게시물에는 답글을 등록할 수 없습니다.", "BACK");
						$board_infos->unick = $this->member_name; // 작성자 명 비우기
						$board_infos->subject = "[re] ".$board_infos->subject;
						$board_infos->content = "<br><div disabled style='width:100%;padding:8px;border:#dedede 1px dotted;background-color:#f7f7f7;margin-top:5px;'>".$board_infos->content."</div>";
						// 답글 입력형태로 변경
						$board_infos->pano = $board_infos->no;
						unset($board_infos->no);
					}
				}
				if($this->sfunction['use_category']) {
					$categories = @unserialize($this->board_configs['scategory']);
					if($this->check_resource($categories)) {
						$scategory = $this->sort_scategory($categories); // 2009.09.18 added
						$scategories = "<option value=''>분류선택</option>";
						foreach($scategory as $rank=>$val) {
							$_selected = $board_infos->cno==$val['cno'] ? " selected" : '';
							$scategories .= "<option value='$val[cno]'$_selected>$val[name]</option>";
						}
					}
					else $scategories = "<option value=''>분류없음</option>";
					$category = "<select name='category' required hname='분류'>".$scategories."</select>";
				}

				// 금지 단어 필터링
				$antifilter = !empty($this->board_configs['sfilter']) ? " antifilter=\"".$this->board_configs['sfilter']."\"" : '';

				// 첨부파일 필터링
				if(!empty($this->sattach['attach_extension'])) $filefilter = " filter=\"".str_replace(" ", '', $this->sattach['attach_extension'])."\"";
				ob_start();
				include $this->get_board_template($this->get_skin_dir()."/article_regist.tpl.html");
				return ob_get_clean();
				break;

			// 게시물목록/상세페이지
			default:
				if(empty($datas['no'])) $contents = $this->get_board_articles($datas);
				else $contents = $this->get_article($datas);
		}
		return $contents;
	}

	// 조회수 증가 처리
	function increase_readcount($board_infos, $force_mode=false) {
		// 글주인이 읽을경우 리턴
		// 게시물 작성시 아이피와 현재 아이피가 같을 경우 리턴
		// 쿠키가 존재할 경우 리턴
		$board_cookies = $_COOKIE[$this->board_id];
		// ▽ || $_SERVER['REMOTE_ADDR']==$board_infos->uip 조건 제거
		if($force_mode===false && ($board_cookies[$board_infos->no]==true || ($this->is_member() && $this->member_id==$board_infos->uid))) return false;
		$result = $this->query("update $this->board_table set hnum=hnum+1 where no=$board_infos->no");
		//공지글이면 쿠기생성후 리턴한다.
		if($board_infos->nval == "yes") {
			// 게시물 쿠키 생성
			setcookie("$this->board_id"."[$board_infos->no]", true, strtotime(date("Y-m-d 00:00:00")." +1 day"), "/"); // 자정에 만료
			return $result;
		}
		if($result) {
			// 조회수 베스트 테이블에 입력 - 게시판별 10개 단위
			$best_datas = $this->queryFetchRows("select no, ano, ahnum from $this->hit_best_table where bid='$this->board_id' order by ahnum desc limit 10");
			$best_rows = end($best_datas); // 마지막 레코드

			if($best_rows['ahnum']<$board_infos->hnum+1 || count($best_datas)<10) {
				$this->query("update $this->hit_best_table set ahnum=".($board_infos->hnum+1)." where ano=$board_infos->no and bid='$this->board_id'");
				if(!mysql_affected_rows()) {
					if(count($best_datas)>=10) {
						$this->query("delete from $this->hit_best_table where no=$best_rows[no]");
						if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->hit_best_table");
					}
					$_val['pcno'] = $this->board_configs['pcno'];
					$_val['bid'] = $this->board_id;
					$_val['adno'] = $board_infos->dno;
					$_val['ano'] = $board_infos->no;
					$_val['ahnum'] = $board_infos->hnum+1;
					$values = $this->change_query_string($_val);
					$this->query("insert $this->hit_best_table set $values");
				}
			}

			// 주간 베스트 테이블에 입력 - 전체 7일치만 관리
			// 일주일이 지난 데이터는 삭제
			$this->query("delete from $this->weekly_best_table where wdate<date_sub(curdate(), interval 7 day)");
			if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->weekly_best_table");

			// 주간 베스트 테이블에 조회수 갱신
			$this->query("update $this->weekly_best_table set hnum=hnum+1 where wdate=curdate() and pcno=".$this->board_configs['pcno']." and ano=$board_infos->no and bid='$this->board_id'");
			if(!mysql_affected_rows()) {
				$_xVal['pcno'] = $this->board_configs['pcno'];
				$_xVal['bid'] = $this->board_id;
				$_xVal['adno'] = $board_infos->dno;
				$_xVal['ano'] = $board_infos->no;
				$_xVal['hnum'] = 1;
				$_xVal['wdate'] = date("Y-m-d");
				$values = $this->change_query_string($_xVal);
				$this->query("insert $this->weekly_best_table set $values");
			}
			// 게시물 쿠키 생성
			setcookie("$this->board_id"."[$board_infos->no]", true, strtotime(date("Y-m-d 00:00:00")." +1 day"), "/"); // 자정에 만료
		}
		return $result;
	}

	// 쿼리 결과를 객체로 반환
	function queryFetchObject($query) {
		$result = $this->query($query);
		$rows = @mysql_fetch_object($result);
		$this->stripslashes($rows); // 쿼리값 리턴시 stripslashes 적용 - 2008.06.09 추가
		@mysql_free_result($result);
		return $rows;
	}

	// 위지윅 넘겨받은 값 가공
	function wysiwyg_result_func($content='') { // $content : stripslashes() 한 값
		$rbUser = $this->get_discern_name();
		preg_match_all('/<img\s+.*?src="([^"]+)"[^>]*>/is', $content, $imgs);
		foreach($imgs[1] as $key=>$img) {
			// 이미지 파일 PEG 폴더로 이동
			if(strpos($img, $this->wysiwyg_url."PEG_temp/")!==false) {
				$_name = basename($img);
				$tmp_file = $this->wysiwyg_dir."PEG_temp/".$_name;
				$save_file = $this->wysiwyg_dir."PEG/".$_name;
				if(is_file($tmp_file)) {
					@copy($tmp_file, $save_file);
					@unlink($tmp_file);
				}
			}
			// 이미지에 걸린 PEG_temp 를 PEG 로 변경 및 절대경로 제거 - 2008.06.09
			$_info = parse_url($img); // scheme : http  or  https
			$_info['scheme'] = empty($_info['scheme']) ? '' : $_info['scheme']."://";
			$_img = str_replace($img, str_replace(array($_info['scheme'].$_SERVER['HTTP_HOST'], "/PEG_temp/", "_junk_.{$rbUser}."), array('', "/PEG/", ''), $img), $imgs[1][$key]);
			if(strpos(strtolower($_img), "border")===false) $_img = eregi_replace(" src", " border=\"0\" src", $_img); // border='0' 추가
			$content = str_replace($imgs[1][$key], $_img, $content);
		}

		// 정크파일 링크 제거
		$content = str_replace("_junk_.{$rbUser}.", '', $content);
		return $content;
	}

	// 본문 이미지 사이즈 조절 - 2010.05.24 added
	function prefix_contents($content='', $prefix_size=685) {
		preg_match_all('/(<img\s+.*?src="([^"]+)"[^>]*)>/is', $content, $images); //이미지정보
		preg_match_all('/(<table id=community_image\s+.*?width=[\'"]?+([0-9%]{1,})[\'"]?+[^>]*)>/is', $content, $tables);//이미지를 감싸는 테이블정보
		foreach($images[0] as $key=>$image) {
			list($width, $height) = @getimagesize($_SERVER[DOCUMENT_ROOT].$images[2][$key]);
			if($width > $prefix_size) {
				$_dimensions = 'width:'.$prefix_size.'px;'; //추가할 스타일 속성
				if(stristr($tables[0][$key], "community_image") !== false) {
					list($new_table, $table_width) = array($tables[0][$key], $tables[2][$key]);
					$new_table = str_ireplace(array('width', 'height'), array('_width', '_height'), $new_table);
					if(stristr($tables[0][$key], 'style')!==false) {
						preg_match_all('/style="([^"]+)"[^"]/is', $new_table, $table_styles);
						$new_table = str_replace($table_styles[1][0], $_dimensions.$table_styles[1][0], $new_table);
					}
					else $new_table = str_replace(array('/>', '>'), ' style="'.$_dimensions.'">', $new_table);
					$content = str_replace($tables[0][$key], $new_table, $content);
				}
				list($new_image, $image_url) = array($image, $images[2][$key]);
				$new_image = str_ireplace(array('width', 'height'), array('_width', '_height'), $new_image); // width & height reset
				if(stristr($image, 'style')!==false) {
					preg_match_all('/style="([^"]+)"[^"]/is', $new_image, $styles);
					$new_image = str_replace($styles[1][0], $_dimensions.$styles[1][0], $new_image);
				}
				else $new_image = str_replace(array('/>', '>'), ' style="'.$_dimensions.'">', $new_image);
				$content = str_replace($image, $new_image, $content);
			}
		}
		return $content;
	}

	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// 게시물 입력
	function regist_article($datas) {
		// 접근권한 체크
		if(empty($datas['no'])) {
			if(!$this->check_granted("write_level")) $this->popup_msg_js($this->get_granted_messages("write_level"), "BACK");
			else if(!empty($datas['pano'])) {
				if($this->sfunction['use_reply']=="no") $this->popup_msg_js("'".$this->board_name."' 에는 답글을 등록할 수 없습니다.", "BACK");
				else if(!$this->check_granted("reply_level")) $this->popup_msg_js($this->get_granted_messages("reply_level"), "BACK");
			}
			if($datas['is_notice']=="on" && !$this->check_granted("notice_level")) $this->popup_msg_js($this->get_granted_messages("notice_level"), "BACK");
		}
		else { // 수정모드일 경우
			$before_datas = $this->queryFetch("select no, cno, sno, nano, pano, uid, upass, attach, sval, nval from $this->board_table where no=$datas[no]");
			// 수정권한 체크 ( 관리자/회원아이디/비회원-비밀번호 체크 )
			if(!$this->is_admin() && (
				(!empty($before_datas['uid']) && ($this->member_id!=$before_datas['uid'] && $datas['passwd']!==$this->get_member_passwd($before_datas['uid'])))
				|| (empty($before_datas['uid']) && $datas['passwd']!=$before_datas['upass']))
			) $this->popup_msg_js("게시물 수정 권한이 없습니다.", "BACK");

			// 공지글 수정권한 체크
			if($before_datas['nval']=="yes" && !$this->check_granted("notice_level")) $this->popup_msg_js("공지글 수정 권한이 없습니다.", "BACK");

			// 이웃하는 글정보를 수정할 필요가 있는지 체크
			$change_near_articles = (($before_datas['nval']=="yes" && empty($datas['is_notice'])) || ($before_datas['nval']=="no" && $datas['is_notice']=="on"));
		}

		// 보안코드가 맞지 않는 경우 - 2010.06.17 added
		if(!$this->is_admin() && empty($this->member_id) && $this->confirm_used && !$this->check_confirm_code($_POST['keystring'])) {
			$this->popup_msg_js("입력하신 스팸방지 코드가 일치하지 않습니다.", "BACK");
		}

		if(empty($datas['pano'])) {
			// 공지글 일때
			if($datas['is_notice']=="on") {
				$_sno = $this->queryR("select min(sno) from $this->board_table");
				$near_article = $this->queryFetch("select no, dno, sno, nano, pano from $this->board_table where sno=$_sno");
				if(is_array($near_article)) $next_sno = $near_article['sno']>$this->notice_sno ? $this->notice_sno-1 : $near_article['sno']-1;
				else $next_sno = $this->notice_sno-1;

				$_val['nval'] = "yes";
				$_val['pano'] = 0;
				$_val['gno'] = 0;
				$_val['pno'] = 0;
			}
			else {
				$near_article = $this->queryFetch("select no, dno, sno, nano, pano from $this->board_table where sno>$this->notice_sno order by sno limit 1");
				if(empty($near_article)) $near_article = $this->queryFetch("select no, dno, sno, nano, pano from $this->board_table order by sno desc limit 1");
				if(is_array($near_article)) $next_sno = !empty($near_article['sno']) ? $near_article['sno']>$this->notice_sno ? $near_article['sno']-1 : -1 : -1;
				else $next_sno = -1;

				$_val['nval'] = "no";
				if($before_datas['nval']=="yes") { // 공지글을 해제한 경우
					$_val['gno'] = 0;
					$_val['pno'] = 0;
				}
			}
			// 이웃하는 글 설정
			if(empty($datas['no']) || $change_near_articles) {
				if(empty($near_article['no'])) $near_article['no'] = 0;
				if($next_sno<$near_article['sno']) {
					$_val['nano'] = $near_article['no'];
					$_val['pano'] = $near_article['pano'];
				}
				else {
					$_val['nano'] = $near_article['nano'];
					$_val['pano'] = $near_article['no'];
				}
				if($_val['nano']==null) $_val['nano'] = 0;
			}
		}
		// 답글일 경우
		else {
			$near_article = $this->queryFetch("select no, cno, sno, gno, pno, nano, pano, uid, sval, nval from $this->board_table where no=$datas[pano]");

			$child_articles = $this->queryFetchRows("select no from $this->board_table where sno=$near_article[sno] and gno>$near_article[gno]");
			foreach($child_articles as $child_rows) $this->query("update $this->board_table set gno=gno+1 where no=$child_rows[no]");

			$next_sno = $near_article['sno'];
			$_val['nano'] = $near_article['nano'];
			$_val['pano'] = $near_article['no'];
			$_val['gno'] = $near_article['gno']+1;
			$_val['pno'] = $near_article['pno']+1;
		}

		// 공통 항목 지정
		$_val['subject'] = str_replace("\"", "&quot;", stripslashes($datas['subject']));
		$_val['content'] = $this->wysiwyg_result_func(stripslashes($datas['content']));
		$_val['uip'] = $_SERVER['REMOTE_ADDR'];
		$_val['sval'] = $datas['is_secret']=="on" ? "yes" : "no";
		// 분류를 사용할 경우에만 카테고리 값 설정
		if($this->sfunction['use_category']=="on") $_val['cno'] = $datas['category'];

		// 신규등록
		if(empty($datas['no'])) {
			$DML = "insert";
			$_val['sno'] = $next_sno;
			$_val['dno'] = $this->increase_division($near_article['dno']); // 디비전 증가  cf. decrease_division();
			//로그인아이디가 존재할 경우 아이디 입력 로그인정보 없이 관리자 로그인일경우 _admin_ 값 입력
			if($_SESSION[$this->member_session_id]) {
				$_val['uid'] = $this->member_badness!=="yes" ? $_SESSION[$this->member_session_id] : '';
			} else if($this->is_admin()) {
				$_val['uid'] = "_admin_";
			} else {
				$_val['uid'] = "";
			}
			$_val['upass'] = $datas['passwd'];
			if($this->member_name) $_val['unick'] = $this->member_name; // 2009.08.28 modified
			else $_val['unick'] = str_replace("\"", "&quot;", stripslashes($datas['nickname']));
			$_val['wdate'] = date("Y-m-d H:i:s");
		}
		else {
			$DML = "update";
			// 게시물 순번이 바뀌는 상황인 경우
			$_val['sno'] = $change_near_articles ? $next_sno : $before_datas['sno'];
			$_val['mdate'] = date("Y-m-d H:i:s");
			$addWhere = " where no=$datas[no]";
		}

		// 첨부파일이 존재할 경우
		if($this->sattach['use_attach']=="on" && !empty($datas['on_attached'])) {
			// 구분자 : 파일단위 구분자(,) 파일명 구분자(:)
			// 기존 첨부파일 정보
			$attached = empty($before_datas['attach']) ? array() : @unserialize($before_datas['attach']);
			$attach_files = explode(',', $datas['on_attached']);
			$rbUser = $this->get_discern_name(); //
			$attach_dir = $this->board_dir."attach/".$this->board_id."/";
			foreach($attach_files as $file) {
				$_file = explode(":", $file);
				$_file[2] = str_replace("_junk_.$rbUser.", '', $_file[1]);
				if(!is_file($attach_dir.$_file[1])) continue;
				// 임시 첨부파일 이름 변경
				rename($attach_dir.$_file[1], $attach_dir.$_file[2]);
				$attached[] = array("oname"=>$_file[0], "sname"=>$_file[2], "dnum"=>0);

				// 새로 첨부하는 파일 목록 저장 - 포인트 적용시 사용
				$sattached_files[count($sattached_files)] = $_file[2];

				// 썸네일 파일이 있다면 이름 변경
				$file_infos = explode(".", $_file[1]);
				$file_infos[2] = "thumb_".$file_infos[2];
				$thumb_file = @implode(".", $file_infos);
				if(!is_file($attach_dir.$thumb_file)) continue;
				rename($attach_dir.$thumb_file, $attach_dir.str_replace("_junk_.$rbUser.", '', $thumb_file));
			}
			$_val['attach'] = serialize($attached);
		}

		$values = $this->change_query_string($_val);
		$this->query("$DML $this->board_table set $values$addWhere");

		// 이웃하는 게시물(이전/다음 글) 갱신
		if($DML=="insert") {
			$article_no = mysql_insert_id();
			$_datas = array("no"=>$article_no, "sno"=>$_val['sno'], "pano"=>0, "nano"=>$near_article['no']);
			$this->change_near_article($_datas, $near_article);
			$this->update_board(array("cmd"=>"set_anum", "plus_mode"=>true)); // 게시물 수 갱신

			// 최근 게시물에 등록 ##
			if($article_no) {
				// 최근 게시물은 상위메뉴(pcno) 당 5개씩만 관리
				$new_datas = $this->queryFetchRows("select no from $this->new_article_table where pcno=".$this->board_configs['pcno']." order by no");
				$new_rows = current($new_datas);
				if(count($new_datas)>=5) { // 1번째(등록한지 가장 오래된) 레코드 제거
					$this->query("delete from $this->new_article_table where no=$new_rows[no]");
					if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->new_article_table");
				}
				$_xVal['pcno'] = $this->board_configs['pcno'];
				$_xVal['bid'] = $this->board_id;
				$_xVal['adno'] = $_val['dno'];
				$_xVal['ano'] = $article_no;
				$_xVal['awdate'] = $_val['wdate'];
				$values = $this->change_query_string($_xVal);
				$this->query("insert $this->new_article_table set $values");
			}
		}
		else {
			if($this->board_extension===true) {
				// 수정시 신고글 관리 테이블에도 적용
				$_yVal['asubject'] = $_val['subject'];
				$_yVal['aunick'] = $_val['unick'];
				$values = $this->change_query_string($_yVal);
				$this->query("update $this->report_table set $values where bid='$this->board_id' and ano=$datas[no]");
			}
			if($change_near_articles) {
				$before_datas['sno'] = $_val['sno'];
				$this->change_near_article($before_datas, $near_article);
			}
		}
		// 분류기능 사용중일 경우 카테고리 anum 갱신
		if($this->sfunction['use_category']=="on") $this->change_category_anum($_val['cno'], $before_datas['cno']);
		return true;
	}

	// 이웃하는 게시물 번호 설정
	function change_near_article($datas, $near_datas=false) {
		if($datas['pano']!=0) $this->query("update $this->board_table set nano=$datas[nano] where nano=$datas[no]"); // 이전글 설정
		if($datas['nano']!=0) $this->query("update $this->board_table set pano=$datas[pano] where pano=$datas[no]"); // 다음글 설정
		// sno 변경 후 근접하게 되는 게시물 정보가 넘어온 경우 - 이전글/다음글 설정
		if(is_array($near_datas)) {
			if($datas['sno']<$near_datas['sno']) {
				// 이웃하는 게시물의 이전글이 존재할 경우 - 해당게시물의 다음글을 대상게시물로 설정
				if($near_datas['pano']!=0) $this->query("update $this->board_table set nano=$datas[no] where no=$near_datas[pano]");
				// 이웃하는 게시물의 이전글을 대상게시물로 설정
				$this->query("update $this->board_table set pano=$datas[no] where no=$near_datas[no]");
			}
			else {
				// 이웃하는 게시물의 다음글이 존재할 경우 - 해당게시물의 이전글을 대상게시물로 설정
				if($near_datas['nano']!=0) $this->query("update $this->board_table set pano=$datas[no] where no=$near_datas[nano]");
				// 이웃하는 게시물의 다음글을 대상게시물로 설정
				$this->query("update $this->board_table set nano=$datas[no] where no=$near_datas[no]");
			}
		}
	}

	// 분할 값 갱신 - 게시물 등록시 사용
	function increase_division($dno) {
		if(empty($dno)) $dno = 1;
		$banum = $this->queryR("select banum from $this->division_table where bid='$this->board_id' and division=$dno");
		if($banum+1>$this->division_num) {
			$_val['bid'] = $this->board_id;
			$_val['division'] = $dno+1;
			$_val['banum'] = 1;
			$this->query("insert $this->division_table set $values");
			$division = $_val['division'];
		}
		else {
			$this->query("update $this->division_table set banum=banum+1 where bid='$this->board_id' and division=$dno");
			$division = $dno;
		}
		return $division;
	}

	// 게시물 선택삭제
	function delete_articles($datas) {
		// 정상적인 접근인지 체크
		$_referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($_referer_infos['query'], $referer_infos);
		if(!empty($datas['anos']) && !empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id']) {
			if($this->check_granted("delete_level")) {
				$board_datas = $this->queryFetchRows("select no, cno, dno, pano, nano, attach, dval from $this->board_table where no in(".str_replace("__", ",", $datas['anos']).")");
				foreach($board_datas as $board_infos) $this->delete_article($board_infos, $this->is_del());
				return "alert('게시물이 삭제되었습니다.'+SPACE); document.location.href='./index.html?".str_replace('&', "&amp;", $_referer_infos['query'])."';"; // $this->index_url - 2009.09.09 without
			}
			else return "alert('게시물 삭제권한이 없습니다.'+SPACE);";
		}
		else $this->popup_msg_js("정상적인 접근이 아닙니다.", "BACK");
	}

	// 게시물 삭제 - 2009.09.09 modified
	function delete_article($board_infos, $real_mode=false) { // real_mode : 최종삭제
		// 출력중인 게시물을 삭제하는 경우 - 메인페이지 노출되지 않도록 정보 삭제
		if($board_infos['dval']== "no" || $real_mode == true) {
			// 주간 베스트 삭제
			$this->query("delete from $this->weekly_best_table where bid='$this->board_id' and ano=$board_infos[no]");
			if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->weekly_best_table");
			// 조회수 베스트 삭제
			$this->query("delete from $this->hit_best_table where bid='$this->board_id' and ano=$board_infos[no]");
			if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->hit_best_table");
			// 댓글수 베스트 삭제
			$this->query("delete from $this->comment_best_table where bid='$this->board_id' and ano=$board_infos[no]");
			if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->comment_best_table");
			// 신규 게시물 삭제
			$this->query("delete from $this->new_article_table where bid='$this->board_id' and ano=$board_infos[no]");
			if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->new_article_table");
			if($this->board_extension===true) {
				// 주간 댓글수 베스트 삭제
				$this->query("delete from $this->weekly_cbest_table where bid='$this->board_id' and ano=$board_infos[no]");
				if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->weekly_cbest_table");
				// 신고글 관리 에서 삭제
				$this->query("delete from $this->report_table where bid='$this->board_id' and ano=$board_infos[no]");
				if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->report_table");
			}
			// 임시삭제 - 삭제된 상태로만 변경
			$this->query("update $this->board_table set dval='yes' where no=$board_infos[no]");
		}
		// 최종삭제시
		if($real_mode==true) {
			// 이웃하는 게시물 재연결
			// 첨부 파일 제거
			// 게시물 수 다운카운트
			// BEST 테이블에서 게시물 제거
			// new_article 테이블에서 게시물 제거
			// 회원 게시물 관리 테이블에서 게시물 수 다운카운트 - 커뮤니티솔루션에서만 적용
			$this->change_near_article($board_infos);
			$attaches = unserialize($board_infos['attach']);
			if($this->check_resource($attaches)) {
				$attach_dir = $this->board_dir."attach/".$this->board_id."/";
				foreach($attaches as $file) {
					if(!is_file($attach_dir.$file['sname'])) continue;
					@unlink($attach_dir.$file['sname']); // 파일삭제
					// 썸네일 삭제
					$thumb_file = "thumb_".$file['sname'];
					if(is_file($attach_dir.$thumb_file)) @unlink($attach_dir.$thumb_file);
				}
			}
			// 게시물 삭제
			$this->query("delete from $this->board_table where no=$board_infos[no]");
			if(mysql_affected_rows()) {
				if($this->optimizer) $this->query("optimize table $this->board_table");

				// 게시판 환경 테이블 해당 게시판의 게시물 수 다운카운트
				if($board_infos['cno']) $this->change_category_anum($board_infos['cno'], '', true); // 분류 업데이트 - 차감
				$this->update_board(array("cmd"=>"set_anum", "plus_mode"=>false)); // 게시물 수 갱신

				// 분할 테이블 해당 게시판의 게시물 수 다운카운트
				$this->query("update $this->division_table set banum=if(banum=0, 0, banum-1) where bid='$this->board_id' and division=$board_infos[dno]");

				// 댓글 삭제
				$this->query("delete from $this->board_comment_table where ano=$board_infos[no]");
				if($this->optimizer===true && mysql_affected_rows()) $this->query("optimize table $this->board_comment_table");
			}
		}
	}

	// 분류내 게시물수 갱신 - 게시물 등록/수정/삭제시 사용
	function change_category_anum($cno, $before_cno, $delete_mode=false) {
		if($this->board_configs!==null) $scategory = @unserialize($this->board_configs['scategory']);
		else {
			$scategory = $this->get_board_config($this->board_id, "scategory");
			$scategory = @unserialize($scategory);
		}
		if($cno===$before_cno) return true;
		if($delete_mode===true) $scategory[$cno]['anum'] -= 1;
		else {
			$scategory[$cno]['anum'] += 1;
			if(!empty($before_cno) && $cno!=$before_cno) $scategory[$before_cno]['anum'] -= 1;
		}
		$_val['scategory'] = serialize($scategory);
		$values = $this->change_query_string($_val);
		$this->query("update $this->bconfig_table set $values where id='$this->board_id'");
		return true;
	}

	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// 게시물 스크랩을 사용할 수 있는지 여부 - 회원만 가능 / 본인글은 안됨
	function is_scrapable($board_infos) {
		return ($this->board_extension===true && $board_infos->sval=="no" && $board_infos->dval=="no" && (!$this->is_member() || ($this->is_member() && $this->member_id!=$board_infos->uid)));
	}

	// 게시물 추천기능을 사용할 수 있는지 여부 - 회원만 가능 / 본인글은 안됨
	function is_votable($board_infos, $check_bad=false) { // $check_bad 인자값 추가 - 2008.12.31
		if($check_bad==true) return ($this->sfunction['use_only_good']!="on");
		return ($this->sfunction['use_vote']=="on" && $board_infos->nval=="no" && $board_infos->sval=="no" && (!$this->is_member() || ($this->is_member() && $this->member_id!=$board_infos->uid)));
	}

	// 게시물 신고 기능을 사용할 수 있는지 여부 - 회원만 가능 / 본인글은 안됨
	function is_reportable($board_infos) {
		return ($this->sfunction['use_report']=="on" && $this->board_extension===true && (!$this->is_member() || ($this->is_member() && $this->member_id!=$board_infos->uid)));
	}

	// 게시물  프린트 기능을 사용할 수 있는지 여부
	function is_printable() {
		return $this->sfunction['use_print']=="on";
	}

	// 게시물을 읽을 수 있는 세션이 존재하는지 체크 ( is_readable )
	function is_seeable($ano) {
		return ($_SESSION[$_COOKIE['rbUser']]===$this->board_id.$ano || $this->is_admin());
	}

	// 게시물을 작성할 수 있는지 여부
	function is_registable() {
		return $this->check_granted("write_level");
	}

	// 답글을 작성할 수 있는지 여부
	function is_replyable($board_infos) {
		if($board_infos->dval=="yes") return false;
		return ($board_infos->nval=="no" && $this->sfunction['use_reply']=="on" && $this->check_granted("reply_level"));
	}

	// 게시물을 수정할 수 있는지 여부
	function is_modifiable($board_infos) {
		return $board_infos->dval=="no";
	}

	//게시물 등록권한 운영자 인지 확인
	function check_admin_registable() {
		if($this->spermission["write_level"] == 1 && $this->member_level == 1 || $this->is_admin()) return true;
		else if($this->spermission["write_level"] == 1 && $this->member_level != 1) return false;
		else return true;
	}

	// 비밀번호 입력받을지 여부
	function is_scanpass($board_infos) { // member_level = 7 : 비회원
		if($this->is_member() && $this->member_badness==="yes" && $this->is_admin()) return true;
		else return ((!empty($board_infos->no) && !$this->is_admin() && ($this->member_level==7 || (!empty($board_infos->uid) && $board_infos->uid!==$this->member_id) || $this->is_member() && empty($board_infos->uid))) || (empty($board_infos->no) && $this->member_level==7));
	}

	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// 게시물 추천/반대 - 2008.12.30 변경
	function vote_article($datas) {
		// 정상적인 접근인지 체크
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		if(!empty($datas['ano']) && !empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id'] && $referer_infos['no']==$datas['ano']) {
			if(!$this->is_member()) return "alert('로그인 후 다시 시도하시기 바랍니다.'+SPACE);";

			// 추천한적이 있는지 체크
			$vote_infos = $this->queryFetch("select FIND_IN_SET('$this->member_id', voter) as is_voted, voter from $this->board_table where no=$datas[ano]");
			if(!empty($vote_infos['is_voted'])) return "alert('이미 투표하신 게시물입니다.'+SPACE);";
			else {
				if($datas['key']!==$this->get_discern_name()) return "rankup_board.article_vote('$datas[kind]', '".$this->get_discern_name()."');";
				// 스크랩 처리
				$_val['voter'] = empty($vote_infos['voter']) ? $this->member_id : $vote_infos['voter'].",".$this->member_id;
				$values = $this->change_query_string($_val);
				$apply_field = ($datas['kind']=="good") ? "gnum=gnum+1" : "bnum=bnum+1";
				$this->query("update $this->board_table set $values, $apply_field where no=$datas[ano]");
				return mysql_affected_rows() ? "alert('성공적으로 투표되었습니다.'+SPACE);" : "alert('게시물 투표가 실패하였습니다.'+SPACE);";
			}
		}
		else $this->popup_msg_js("정상적인 접근이 아닙니다.", "BACK");
	}

	// 게시물을 읽을수 있는지 비밀번호 체크
	function view_article($datas) {
		// 정상적인 접근인지 체크
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		if(!empty($referer_infos['pcno']) || (!empty($datas['ano']) && !empty($_SERVER['HTTP_REFERER']))) {
			$board_infos = $this->queryFetch("select no, sno, gno, pno, uid, upass, sval from $this->board_table where no=$datas[ano]");

			// 비밀글 관련 패치
			$secret_article_view = false;
			if($board_infos['pno']>0 && $board_infos['sval']=="yes") {
				$parent_ano = $this->queryR("select pano from $this->board_table where sno='$board_infos[sno]' and pno='$board_infos[pno]' order by gno limit 0, 1");
				$prev_article_infos = $this->queryFetch("select uid, upass from $this->board_table where no=$parent_ano");
				if(empty($prev_article_infos['uid'])) {
					if($datas['passwd']===$prev_article_infos['upass']) $secret_article_view = true;
				}
				else if($datas['passwd']===$this->get_member_passwd($prev_article_infos['uid'])) {
					$secret_article_view = true;
				}
			}

			if($secret_article_view===true || (empty($board_infos['uid']) && $board_infos['upass']==$datas['passwd']) ||
				(!empty($board_infos['uid']) && $datas['passwd']===$this->get_member_passwd($board_infos['uid']))) {
				unset($referer_infos['pcno']);
				$referer_infos['no'] = $datas['ano'];
				$referer_infos['id'] = $datas['id'];
				$referers = http_build_query($referer_infos); // php5 이상, rankup_basic.class.php 에 정의됨
				// 1회용 세션으로 게시물에 접근할 수 있도록 처리
				$_SESSION[$_COOKIE['rbUser']] = $this->board_id.$datas['ano'];
				return "$this->index_url/index.html?".str_replace('&', "&amp;", $referers);
			}
			else return "alert('비밀번호가 일치하지 않습니다.'+SPACE);";
		}
		else $this->popup_msg_js("정상적인 접근이 아닙니다.", "BACK");
	}

	// 게시물 작성자인지 비밀번호 체크
	function verify_author($datas) {
		// 정상적인 접근인지 체크
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		if(!empty($datas['ano']) && !empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id'] && $referer_infos['no']==$datas['ano']) {
			$board_infos = $this->queryFetch("select no, dno, cno, uid, upass, nano, pano, attach, dval from $this->board_table where no=$datas[ano]");
			if($this->is_admin() ||
				($datas['cmd']=="delete_article" && $this->check_granted("delete_level")) ||
				(empty($board_infos['uid']) && $board_infos['upass']===$datas['passwd']) ||
				(!empty($board_infos['uid']) && ($board_infos['uid']===$this->member_id || $datas['passwd']===$this->get_member_passwd($board_infos['uid'])))) {
				switch($datas['cmd']) {
					// 게시물 삭제 처리
					case "delete_article":
						if($datas['passwd']==="undefined") return "rankup_board.article_delete('', 'article_delete');";
						$this->delete_article($board_infos, $this->is_del()); // 게시물 dval='yes' 로만 변경
						unset($referer_infos['no']);
						$referers = http_build_query($referer_infos);
						return "alert('성공적으로 삭제되었습니다.'+SPACE); document.location.href=\"./index.html?".str_replace('&', "&amp;", $referers)."\";"; // $this->index_url - 2009.09.09 without
						break;

					// 게시물 수정
					case "modify_article":
						// 1회용 세션으로 게시물에 접근할 수 있도록 처리
						$referer_infos['mode'] = "write";
						$referer_infos['no'] = $datas['ano'];
						$referers = http_build_query($referer_infos); // php5 이상, rankup_basic.class.php 에 정의됨
						$_SESSION[$_COOKIE['rbUser']] = $this->board_id.$datas['ano']; // 임시 세션 생성
						return "document.location.href=\"./index.html?".str_replace('&', "&amp;", $referers)."\";"; // $this->index_url - 2009.09.09 without
						break;
				}
			}
			else {
				// 실행할 함수명 설정
				$command = $datas['cmd']=="delete_article" ? "article_delete" : "article_modify";
				if(in_array($datas['passwd'], array('', "undefined"))) return "rankup_board.scanf_passwd($datas[ano], click_obj, '$command');";
				else return "alert('비밀번호가 일치하지 않습니다.'+SPACE); var scanf_passwd = $('div_scanf_passwd').getElementsByTagName('input')[0]; scanf_passwd.select(); scanf_passwd.focus();";
			}
		}
		else $this->popup_msg_js("정상적인 접근이 아닙니다.", "BACK");
	}

	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// 아스키 코드 제거 : 2008.10.22 추가
	function ASCII_code_filtering($string) {
		$pattern = '/[^A-Za-z0-9\.,\&\(\)_\-\s\x{0080}-\x{ffef}]+/u';
		//$pattern = '/^[\s\x{0000}-\x{007F}]+/u'; // PHP 4.3.x 에서 패턴길이 문제로 간소화 - 2008.12.03
		if($this->check_unicode($string)) return preg_replace($pattern, "", $string);
		else return iconv("UTF-8", "CP949", preg_replace($pattern, "", iconv("CP949", "UTF-8", $string)));
	}

	// 댓글 로드 : 2008.10.22 수정
	function get_comment_articles($datas) {
		if($this->sfunction['use_comment']!="on") return false;
		$comment_datas = $this->queryFetchRows("select no, pno, cnum, remove, unick, uip, icon, content, wdate from $this->board_comment_table where ano=$datas[ano] order by no");
		foreach($comment_datas as $rows) {
			list($date, $time) = explode(" ", $rows['wdate']);
			$wdate = $date==date("Y-m-d") ? $time : $date;
			if($this->is_admin()) $uip = $rows['uip'];
			if($rows['pno']<1) $reply_icon = '';
			else $reply_icon = "<img src='".$this->board_url."icon/icon_re.gif' align='absmillde' />";
			$result .= "
			<item no='$rows[no]' pno='$rows[pno]' remove='$rows[remove]'>
				<reply_icon><![CDATA[".$reply_icon."]]></reply_icon>
				<icon>$rows[icon]</icon>
				<nickname><![CDATA[".$rows['unick']."]]></nickname>
				<uip><![CDATA[".$uip."]]></uip>
				<content><![CDATA[".nl2br($rows['content'])."]]></content>
				<wdate>$wdate</wdate>
			</item>";
		}
		return $result;
	}
	// 댓글 등록
	function regist_comment($datas) {
		if(empty($datas['no']) || empty($datas['nickname'])) return false;

		// 불량회원인지 체크 - 2009.08.12 added
		if($this->member_badness=="yes") $this->popup_msg_js("고객님은 불량회원으로 댓글작성이 제한되었습니다.", $this->base_url."main/index.html");

		// 접근 권한 체크
		if(!$this->check_granted('comment_level')) $this->popup_msg_js($this->get_granted_messages("comment_level"), "BACK");

		// 보안코드가 맞지 않는 경우 - 2010.06.17 added
		if(!$this->is_admin() && empty($this->member_id) && $this->confirm_used && !$this->check_confirm_code($_POST['keystring'])) {
			$this->popup_msg_js("입력하신 스팸방지 코드가 일치하지 않습니다.", "VOID");
		}

		$_val['ano'] = $datas['no'];
		$_val['pno'] = $datas['pno'];
		$_val['uip'] = $_SERVER['REMOTE_ADDR'];
		$_val['uid'] = $this->member_id;
		$_val['unick'] = str_replace("\"", "&quot;", stripslashes($datas['nickname']));
		$_val['upasswd'] = $datas['passwd'];
		$_val['icon'] = $datas['icon'];
		$_val['content'] = stripslashes($datas['content']);
		$_val['wdate'] = date("Y-m-d H:i:s");
		$values = $this->change_query_string($_val);
		$this->query("insert $this->board_comment_table set $values");
		$comment_no = mysql_insert_id();
		//글작성자 아이디/닉네임
		$writer_info = $this->get_writer_infos($_val, true);
		if($comment_no) {
			// 포인트 적용
			if($point_check===true) $this->apply_point($this->member_id, "comment", $datas['no'], $comment_no);
			// 내 댓글에 등록
			if($this->board_extension===true && $this->is_member()) {
				$_wVal['uid'] = $this->member_id;
				$_wVal['pcno'] = $this->board_configs['pcno'];
				$_wVal['bid'] = $this->board_id;
				$_wVal['ano'] = $datas['no'];
				$_wVal['cno'] = $comment_no;
				$_wVal['wdate'] = $_val['wdate'];
				$values = $this->change_query_string($_wVal);
				$this->query("insert $this->my_comment_table set $values");
			}

			$this->query("update $this->board_table set cnum=cnum+1 where no=$datas[no]");
			//댓글의 댓글일경우 부모댓글수 cnum증가
			if($datas[pno]>0) $this->query("update $this->board_comment_table set cnum=cnum+1 where no=$datas[pno]");
			unset($_val, $values);

			// 댓글 베스트 테이블에 입력 - 게시판별 10개 단위
			// 현재의 댓글 수
			$article_datas = $this->queryFetch("select no, dno, cnum, nval from $this->board_table where no=$datas[no]");
			$best_datas = $this->queryFetchRows("select no, ano, acnum from $this->comment_best_table where bid='$this->board_id' order by acnum desc limit 10");
			$best_rows = end($best_datas); // 마지막 레코드
			//해당 글이 공지사항이 아니면 입력한다.
			if($article_datas['nval'] != "yes") {
				if($best_rows['acnum']<$article_datas['cnum'] || count($best_datas)<10) {
					$this->query("update $this->comment_best_table set acnum=".$article_datas['cnum']." where ano=$datas[no] and bid='$this->board_id'");
					if(!mysql_affected_rows()) {
						if(count($best_datas)>=10) {
							$this->query("delete from $this->comment_best_table where no=$best_rows[no]");
							if($this->optimizer===true && mysql_affected_rows()) $this->query("optimize table $this->comment_best_table");
						}
						$_val['pcno'] = $this->board_configs['pcno'];
						$_val['bid'] = $this->board_id;
						$_val['adno'] = $article_datas['dno'];
						$_val['ano'] = $article_datas['no'];
						$_val['acnum'] = $article_datas['cnum'];
						$values = $this->change_query_string($_val);
						$this->query("insert $this->comment_best_table set $values");
					}
				}
				if($this->is_member() && $this->board_extension===true) {
					// 주간 댓글 베스트 테이블에 입력 - 전체 7일치만 관리
					// 일주일이 지난 데이터는 삭제
					$this->query("delete from $this->weekly_cbest_table where wdate<date_sub(curdate(), interval 7 day)");
					if($this->optimizer===true && mysql_affected_rows()) $this->query("optimize table $this->weekly_cbest_table");
					// 주간 댓글 베스트 테이블에 조회수 갱신
					$this->query("update $this->weekly_cbest_table set cnum=cnum+1 where wdate=curdate() and ano=$article_datas[no] and uid='$this->member_id'");
					if(!mysql_affected_rows()) {
						$_xVal['bid'] = $this->board_id;
						$_xVal['uid'] = $this->member_id;
						$_xVal['ano'] = $article_datas['no'];
						$_xVal['cnum'] = 1;
						$_xVal['wdate'] = date("Y-m-d");
						$values = $this->change_query_string($_xVal);
						$this->query("insert $this->weekly_cbest_table set $values");
					}
				}
			}
			//댓글댓글일 경우 답아이콘 추가
			if($datas['pno']<1) $reply_icon = '';
			else $reply_icon = "<img src='".$this->board_url."icon/icon_re.gif' align='absmillde' />";
		}
		return $this->is_admin() ? array($comment_no, date("H:i:s"), $writer_info, $_SERVER['REMOTE_ADDR'], $datas['pno'], $reply_icon) : array($comment_no, date("H:i:s"), $writer_info, "", $datas['pno'], $reply_icon);
	}
	/*
	function regist_comment($datas) {
		if(empty($datas['no']) || empty($datas['nickname'])) return false;

		// 접근 권한 체크
		if(!$this->check_granted('comment_level')) $this->popup_msg_js($this->get_granted_messages("comment_level"), "BACK");

		// 보안코드가 맞지 않는 경우 - 2010.06.17 added
		if(!$this->is_admin() && empty($this->member_id) && $this->confirm_used && !$this->check_confirm_code($_POST['keystring'])) {
			$this->popup_msg_js("입력하신 스팸방지 코드가 일치하지 않습니다.", "VOID");
		}

		$_val['ano'] = $datas['no'];
		$_val['uip'] = $_SERVER['REMOTE_ADDR'];
		$_val['uid'] = $this->member_id;
		$_val['unick'] = str_replace("\"", "&quot;", stripslashes($datas['nickname']));
		$_val['upasswd'] = $datas['passwd'];
		$_val['icon'] = $datas['icon'];
		$_val['content'] = stripslashes($datas['content']);
		$_val['wdate'] = date("Y-m-d H:i:s");
		$values = $this->change_query_string($_val);
		$this->query("insert $this->board_comment_table set $values");
		$comment_no = mysql_insert_id();
		//글작성자 아이디/닉네임
		$writer_info = $this->get_writer_infos($_val);
		if($comment_no) {
			// 댓글 수 증가
			$this->query("update $this->board_table set cnum=cnum+1 where no=$datas[no]");
			unset($_val, $values);

			// 댓글 베스트 테이블에 입력 - 게시판별 10개 단위
			// 현재의 댓글 수
			$article_datas = $this->queryFetch("select no, dno, cnum, nval from $this->board_table where no=$datas[no]");
			$best_datas = $this->queryFetchRows("select no, ano, acnum from $this->comment_best_table where bid='$this->board_id' order by acnum desc limit 10");
			$best_rows = end($best_datas); // 마지막 레코드
			//본문이 공지사항이 아니면
			if($article_datas['nval'] != "yes") {
				if($best_rows['acnum']<$article_datas['cnum'] || count($best_datas)<10) {
					$this->query("update $this->comment_best_table set acnum=".$article_datas['cnum']." where ano=$datas[no] and bid='$this->board_id'");
					if(!mysql_affected_rows()) {
						if(count($best_datas)>=10) {
							$this->query("delete from $this->comment_best_table where no=$best_rows[no]");
							if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->comment_best_table");
						}
						$_val['pcno'] = $this->board_configs['pcno'];
						$_val['bid'] = $this->board_id;
						$_val['adno'] = $article_datas['dno'];
						$_val['ano'] = $article_datas['no'];
						$_val['acnum'] = $article_datas['cnum'];
						$values = $this->change_query_string($_val);
						$this->query("insert $this->comment_best_table set $values");
					}
				}

				if($this->is_member() && $this->board_extension===true) {
					// 주간 댓글 베스트 테이블에 입력 - 전체 7일치만 관리
					// 일주일이 지난 데이터는 삭제
					$this->query("delete from $this->weekly_cbest_table where wdate<date_sub(curdate(), interval 7 day)");
					if($this->optimizer && mysql_affected_rows()) $this->query("optimize table $this->weekly_cbest_table");

					// 주간 댓글 베스트 테이블에 조회수 갱신
					$this->query("update $this->weekly_cbest_table set cnum=cnum+1 where wdate=curdate() and ano=$article_datas[no] and uid='$this->member_id'");
					if(!mysql_affected_rows()) {
						$_xVal['bid'] = $this->board_id;
						$_xVal['uid'] = $this->member_id;
						$_xVal['ano'] = $article_datas['no'];
						$_xVal['cnum'] = 1;
						$_xVal['wdate'] = date("Y-m-d");
						$values = $this->change_query_string($_xVal);
						$this->query("insert $this->weekly_cbest_table set $values");
					}
				}
			}
		}
		return $this->is_admin() ? array($comment_no, date("H:i:s"), $writer_info, $_SERVER['REMOTE_ADDR']) : array($comment_no, date("H:i:s"), $writer_info, "");
	}
*/
	// 댓글 수정 - Ajax - 2009.09.09 added
	function apply_comment($datas) {
		if(!$datas['no']) return false;
		$_val['icon'] = $datas['icon'];
		$_val['content'] = stripslashes($datas['comment']);
		$values = $this->change_query_string($_val);
		$this->query("update $this->board_comment_table set $values where no=$datas[no]");
	}

	// 댓글 수정 - 2009.09.09 added
	function modify_comment($datas) {
		// 댓글 수정권한 체크
		// 로그인 회원의 경우 자신이 등록한 코멘트인지 체크 - 바로 수정
		// 관리자인 경우 바로 수정
		// 그외의 경우에는 비밀번호 창 띄움
		// 정상적인 접근인지 체크
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		if(!empty($datas['ano']) && !empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id'] && $referer_infos['no']==$datas['ano']) {

			$comment_infos = $this->queryFetch("select no, ano, uid, upasswd, wdate from $this->board_comment_table where no=$datas[no] and ano=$datas[ano]");
			if($this->is_admin() ||
				(empty($comment_infos['uid']) && $comment_infos['upasswd']===$datas['passwd']) ||
				(!empty($comment_infos['uid']) && ($comment_infos['uid']===$this->member_id || $datas['passwd']===$this->get_member_passwd($comment_infos['uid'])))) {

				if($datas['passwd']==="undefined") return "rankup_board.comment_modify($datas[no], 'comment_modify');";
				else return "rankup_board.comment_form($datas[no], click_obj); $('div_scanf_passwd').hide();"; // 수정폼 로드
			}
			else {
				if(in_array($datas['passwd'], array('', "undefined"))) return "rankup_board.scanf_passwd($datas[no], click_obj, 'comment_modify');";
				else return "alert('비밀번호가 일치하지 않습니다.'+SPACE); var scanf_passwd = $('div_scanf_passwd').getElementsByTagName('input')[0]; scanf_passwd.select(); scanf_passwd.focus();";
			}
		}
		else $this->popup_msg_js("정상적인 접근이 아닙니다.", "BACK");
	}

// 댓글에 댓글 - 2011.08.16 added
   function comment_reply($datas) {
		//
		//
		// 관리자인 경우 바로 등록
		// 정상적인 접근인지 체크
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		if(!empty($datas['ano']) && !empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id'] && $referer_infos['no']==$datas['ano']) {

			$comment_infos = $this->queryFetch("select no, ano, uid, upasswd, wdate from $this->board_comment_table where no=$datas[no] and ano=$datas[ano]");
			return "rankup_board.comment_reply_form($datas[no], click_obj);"; // 수정폼 로드
		}
		else $this->popup_msg_js("정상적인 접근이 아닙니다.", "BACK");
	}


	// 댓글 삭제처리 - 기생루틴 - 2009.09.14 move-in
    function _delete_comment($datas) {
		//댓글-댓글에 대한 처리 포함
		if($datas[cnum]<=0) $this->query("delete from $this->board_comment_table where no=$datas[no] and ano=$datas[ano]");
		else $this->query("UPDATE $this->board_comment_table set content='삭제된 덧글입니다.', remove='yes' where no=$datas[no] and ano=$datas[ano]");
		if($datas[pno]>0){
			$this->query("UPDATE $this->board_comment_table set cnum=cnum-1 where no=$datas[pno]");
			$parent_infos = $this->queryFetch("select cnum, remove from $this->board_comment_table where no=$datas[pno];");
			if($parent_infos[cnum]==0 && $parent_infos[remove]=="yes")
			$this->query("delete from $this->board_comment_table where no=$datas[pno]");
		}
		if(!mysql_affected_rows()) return false;
		$this->query("update $this->board_table set cnum=if(cnum=0, 0, cnum-1) where no=$datas[ano]");

		// 댓글 베스트 테이블에도 적용
		// 현재 게시물에 코멘트가 없으면 comment_best_table 에서 제거
		$cnums = $this->queryR("select cnum from $this->board_table where no=$datas[ano]");
		if($cnums>0) $this->query("update $this->comment_best_table set acnum=if(acnum=0, 0, acnum-1) where ano=$datas[ano] and bid='$this->board_id'");
		else {
			$this->query("delete from $this->comment_best_table where ano=$datas[ano]");
			if($this->optimizer===true && mysql_affected_rows()) $this->query("optimize table $this->comment_best_table");
		}
		// 주간 댓글 베스트 다운카운트
		if(!empty($datas['uid']) && $this->board_extension===true) {
			$this->query("update $this->weekly_cbest_table set cnum=if(cnum=0, 0, cnum-1) where bid='$this->board_id' and uid='$datas[uid]' and ano=$datas[ano]");
			if($this->optimizer===true && mysql_affected_rows()) $this->query("optimize table $this->weekly_cbest_table");

			// 회원 댓글 테이블에서도 삭제
			$this->query("delete from $this->my_comment_table where bid='$this->board_id' and uid='$datas[uid]' and ano=$datas[ano]");
			if($this->optimizer===true && mysql_affected_rows()) $this->query("optimize table $this->my_comment_table");
		}
		return true;
	}
	// 댓글 삭제
    function delete_comment($datas) {
		// 댓글 삭제권한 체크
		// 로그인 회원의 경우 자신이 등록한 코멘트인지 체크 - 바로 삭제
		// 관리자인 경우 바로 삭제
		// 그외의 경우에는 비밀번호 창 띄움
		// 정상적인 접근인지 체크
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		if(!empty($datas['ano']) && !empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id'] && $referer_infos['no']==$datas['ano']) {
			$comment_infos = $this->queryFetch("select no, ano, pno, cnum, remove, uid, upasswd, wdate from $this->board_comment_table where no=$datas[no] and ano=$datas[ano]");
			if($this->is_admin() ||
				(empty($comment_infos['uid']) && $comment_infos['upasswd']===$datas['passwd']) ||
				(!empty($comment_infos['uid']) && ($comment_infos['uid']===$this->member_id || $datas['passwd']===$this->get_member_passwd($comment_infos['uid'])))) {
				$obj_name = ($comment_infos['pno']>0) ? "div_comment_reply_view_".$comment_infos['pno'] : "div_comment_articles";

				if($datas['passwd']==="undefined") return "rankup_board.comment_delete($datas[no], 'comment_delete');";
				if($this->_delete_comment($comment_infos)){
					return "alert('정상적으로 삭제되었습니다.'+SPACE); parent.rankup_board.comment_load(); var cnumObj = $('div_comment_nums').getElementsByTagName('span')[0]; cnumObj.innerHTML = parseInt(cnumObj.innerHTML, 10) - 1; parent.rankup_board.comment_reply_form('bottom', $('reply_bottom'));";
				}
			}
			else {
				if(in_array($datas['passwd'], array('', "undefined"))) return "rankup_board.scanf_passwd($datas[no], click_obj, 'comment_delete');";
				else return "alert('비밀번호가 일치하지 않습니다.'+SPACE); var scanf_passwd = $('div_scanf_passwd').getElementsByTagName('input')[0]; scanf_passwd.select(); scanf_passwd.focus();";
			}
		}
		else $this->popup_msg_js("정상적인 접근이 아닙니다.", "BACK");
	}

	//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
	// 정크파일 식별코드
	function get_discern_name() {
		$rbUser = $_COOKIE['rbUser'];
		if(empty($rbUser)) {
			$rbUser = base64_encode($this->uniqueTimeStamp());
			@setCookie("rbUser", $rbUser, time()+86400, "/"); // 1일 짜리 쿠키생성
		}
		return $rbUser;
	}

	// 임시 업로드 파일 제거
	function clear_junk_files($write_mode=false) {
		// 등록한지 오래된 파일 삭제 : 삭제기준 3시간 전
		$attach_dir = $this->board_dir."attach/".$this->board_id."/";
		if(is_dir($attach_dir) && $dh=opendir($attach_dir)) {
			// 정크파일 식별코드
			$rbUser = $this->get_discern_name();
			while(($file = readdir($dh)) !== false) {
				if(in_array($file, array(".", "..")) || strtoupper(filetype($attach_dir.$file))=="DIR") continue;
				$file_names = explode(".", $file);
				if($file_names[0]!="_junk_") continue; // junk 파일이 아니면 스킵
				if($write_mode && $file_names[1]==$rbUser) @unlink($attach_dir.$file); // 회원이 올렸던 임시 파일이면 삭제
				else if(filectime($attach_dir.$file)<=strtotime("-3 hours")) @unlink($attach_dir.$file); // 3시간이 지난 파일 삭제
			}
			closedir($dh);
		}
	}

	// 썸네일 만들기
	function make_thumbnail($source_file, $dest_file, $width=null, $height=null) {
		ini_set("memory_limit", "80M"); // 메모리 제한을 여유 있게 설정

		list($image_width, $image_height, $image_type) = getimagesize($source_file);
		if($image_type==1) $source = imagecreatefromgif($source_file);
		else if($image_type==2) $source = imagecreatefromjpeg($source_file);
		else if($image_type==3) $source = imagecreatefrompng($source_file);
		else return;

		$thumb_width = $thumb_height = null;
		if($width!=null && $image_width>$width) {
			$thumb_width = $width;
			$thumb_height = $image_height / ($image_width/$width);
		}
		if($height!=null) {
			if($thumb_height!=null) {
				if($thumb_height>$height) {
					$thumb_width = $thumb_width / ($thumb_height/$height);
					$thumb_height = $height;
				}
			}
			else if($image_height>$height) {
				$thumb_height = $height;
				$thumb_width = $image_width / ($image_height/$height);
			}
		}
		if($thumb_width==null||$thumb_height==null) {
			if($source_file!=$dest_file) @copy($source_file, $dest_file);
		}
		else {
			$new_source = imagecreatetruecolor($thumb_width, $thumb_height);
			imagecopyresampled($new_source, $source, 0, 0, 0, 0, $thumb_width, $thumb_height, $image_width, $image_height);
			if($image_type==1) imagegif($new_source, $dest_file);
			else if($image_type==2) imagejpeg($new_source, $dest_file);
			else if($image_type==3) imagepng($new_source, $dest_file);
			imagedestroy($new_source);
		}
		return array_pop(explode("/", $dest_file));
	}

	// 워터마크 적용 - 2010.10.22 added
	function append_watermark($canvasImage) {
		if($this->wm_settings['use_watermark']!='yes') return false;
		if($this->sfunction['use_watermark']!='on') return false;
		$watermark_image = $this->base_dir."rankup_module/rankup_board/watermark/".$this->get_watermark_image($this->base_dir."rankup_module/rankup_board/watermark/");
		$this->watermark_image($canvasImage,$watermark_image,$this->wm_settings['watermark_location'],$this->wm_settings['watermark_transcolor'],$this->wm_settings['watermark_opacity'],$this->wm_settings['watermark_margin']);
	}

	// 첨부파일 - 미리보기
	function post_attached($local_file, $make_thumb=false) {
		if(empty($local_file['tmp_name'])) return false;

		$ext = array_pop(explode(".", strtolower($local_file['name'])));
		if(!empty($ext)) $ext = ".$ext";

		// 정크파일 네이밍 : _junk_.userCookie.unique_time_stamp.extension
		$file_name = $this->uniqueTimeStamp();
		$junk_file_name = "_junk_.".$this->get_discern_name().".".$file_name.$ext;
		$remote_file = $this->board_dir."attach/".$this->board_id."/".$junk_file_name;

		move_uploaded_file($local_file['tmp_name'], $remote_file); // 정크파일 저장

		// 파일 정보
		$infos = getimagesize($remote_file);

		// 썸네일파일 네이밍 : _junk_.userCookie.tmb_.file_unique_time_stamp.extension
		if($make_thumb===true && in_array($infos[2], array(2,3))) { // jpg, png 썸네일
			$junk_thumb_file_name = "_junk_.".$this->get_discern_name().".thumb_".$file_name.$ext;
			$remote_thumb_file = $this->board_dir."attach/".$this->board_id."/".$junk_thumb_file_name;

			// 갤러리형 - 썸네일 정보 참조 - 2010.04.28 fixed
			if($this->board_configs['sgallery']) $sgallery = @unserialize($this->board_configs['sgallery']);
			if($sgallery['thumb_width'] && $sgallery['thumb_height']) list($thumb_width, $thumb_height) = array($sgallery['thumb_width'], $sgallery['thumb_height']);
			else list($thumb_width, $thumb_height) = array($this->thumbnail_width, $this->thumbnail_height);
			$this->make_thumbnail($remote_file, $remote_thumb_file, $thumbnail_width, $thumbnail_height);
		}

		//워터마크 적용
		$this->append_watermark($remote_file);

		$infos[2] = $this->get_extension($remote_file); // 확장자명
		$infos[4] = array_pop($this->get_file_size($remote_file));
		$board_url = ($this->base_url!=="/") ? str_replace($this->base_url, '', $this->board_url) : substr($this->board_url, 1);
		return array("name"=>$board_url."attach/".$this->board_id."/".$junk_file_name, "infos"=>$infos);
	}

	// 첨부파일 삭제
	function delete_attach($datas) {
		if(in_array($datas['file'], array('', 'undefined'))) return false;
		// 삭제권한이 있는지 체크
		if(!$this->check_granted("write_level")) return false;

		// 본문 수정시 파일삭제 요청이 정상적인지를 체크
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		$is_junk = @array_shift(explode(".", $datas['file']))=="_junk_"; // 정크파일 유무
		if(!empty($datas['ano']) && !$is_junk && !empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id'] && $referer_infos['no']==$datas['ano'] && $referer_infos['mode']=="write") {
			// 해당 게시물의 attach 필드 값을 갱신
			$board_infos = $this->queryFetch("select no, uid, attach from $this->board_table where no=$datas[ano]");
			$attaches = unserialize($board_infos['attach']);
			if($this->check_resource($attaches)) {
				$file_name = basename($datas['file']);
				foreach($attaches as $key=>$val) {
					if($val['sname']!=$file_name) continue;
					unset($attaches[$key]);
					$_val['attach'] = serialize($attaches);
					$values = $this->change_query_string($_val);
					$this->query("update $this->board_table set $values where no=$datas[ano]"); // 정보 갱신
					if(is_file($this->base_dir.$datas['file'])) {
						@unlink($this->base_dir.$datas['file']); // 파일삭제
						$file_infos = explode(".", $file_name);
						// 썸네일 삭제
						if($file_infos[0]=="_junk_") $file_infos[2] = "thumb_".$file_infos[2];
						else $file_infos[0] = "thumb_".$file_infos[0];
						$thumb_file = @implode(".", $file_infos);
						if(is_file($this->base_dir.$thumb_file)) @unlink($this->base_dir.$thumb_file);
					}
					break;
				}
			}
		}
		// 수정글이 아닌경우 작성자가 첨부했던 정크파일만 삭제
		else if(file_exists($this->base_dir.$datas['file'])) {
			$file_names = explode(".", array_pop(explode("/", $datas['file'])));
			if($file_names[0]=="_junk_" && $file_names[1]==$this->get_discern_name()) {
				@unlink($this->base_dir.$datas['file']);
				// 썸네일 삭제 - 2009.01.13 fixed
				$file_names[2] = "thumb_".$file_names[2];
				$thumb_file = $this->board_dir."attach/".$this->board_id."/".implode(".", $file_names);
				if(is_file($thumb_file)) @unlink($thumb_file);
			}
		}
		return true;
	}

	// 첨부파일 아이템 리턴
	function load_attach($datas) {
		// 정상적인 요청인지를 체크
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		if(!empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id'] && $referer_infos['mode']=="write") {
			$sgallery = unserialize($this->board_configs['sgallery']);
			if(!empty($datas['ano']) && $referer_infos['no']==$datas['ano']) {
				$attach_dir = $this->board_dir."attach/".$this->board_id."/";
				$board_url = ($this->base_url!=="/") ? str_replace($this->base_url, '', $this->board_url) : substr($this->board_url, 1);
				$attach_url = $board_url."attach/".$this->board_id."/";
				$attaches = $this->queryR("select attach from $this->board_table where no=$datas[ano]");
				$attaches = unserialize($attaches);
				if($this->check_resource($attaches)) {
					foreach($attaches as $key=>$val) {
						// 실제 파일이 존재하지 않는 파일은 제거
						if(!is_file($attach_dir.$val['sname'])) $infos = array();
						else $infos = getimagesize($attach_dir.$val['sname']);
						$infos[2] = $this->get_extension($attach_dir.$val['sname']); // 확장자명
						$infos[4] = array_pop($this->get_file_size($attach_dir.$val['sname']));
						$item .= "
						<item key='$key'>
							<fname><![CDATA[{$attach_url}{$val[sname]}]]></fname>
							<fwidth><![CDATA[{$infos[0]}]]></fwidth>
							<fheight><![CDATA[{$infos[1]}]]></fheight>
							<ftype>$infos[2]</ftype>
							<fsize>$infos[4]</fsize>
						</item>";
					}
				}
			}
			$item = "
			<attach max_width='".$sgallery['picture_width']."' max_nums='".$this->sattach['attach_nums']."' />$item";
			return $item;
		}
		return false;
	}

	// 첨부파일 다운로드
	function download_attach($datas) {
		// 게시물 상세보기 권한 체크
		if(!$this->check_granted("read_level")) $this->popup_msg_js("게시물 다운로드 권한이 없습니다.", "VOID");

		// 정상적인 요청인지를 체크
		$referer_infos = parse_url($_SERVER['HTTP_REFERER']);
		parse_str($referer_infos['query'], $referer_infos);
		if(!empty($datas['ano']) && !empty($_SERVER['HTTP_REFERER']) && $referer_infos['id']==$datas['id'] && $referer_infos['no']==$datas['ano']) {
			$article_infos = $this->queryFetch("select uid, attach from $this->board_table where no=$datas[ano]");
			$attaches = unserialize($article_infos['attach']);
			if($this->check_resource($attaches)) {
				foreach($attaches as $key=>$val) {
					if($key!=$datas['fid'] || $val['oname']!=$datas['fname']) continue;
					$attach_dir = $this->board_dir."attach/".$this->board_id."/";
					if(is_file($attach_dir.$val['sname'])) {
						$this->down_file($attach_dir.$val['sname'], $val['oname']); // 파일다운로드
						// 이하는 다운로드가 완료되면 처리됨 - 단, 파일사이즈가 작을경우에도 처리됨
						$attaches[$key]['dnum'] += 1; // 다운로드 횟수 증가
						$_val['attach'] = serialize($attaches);
						$values = $this->change_query_string($_val);
						$this->query("update $this->board_table set $values where no=$datas[ano]"); // 정보 갱신
					}
					break;
				}
			}
			// 정상적으로 파일이 다운로드 되었다면 이부분까지 실행되지 않는다.
			$this->popup_msg_js("요청하신 파일('$datas[fname]')이 존재하지 않습니다.", "VOID");
		}
		else $this->popup_msg_js("게시물 상세페이지에서 다운로드 하시기 바랍니다.", "BACK");
	}
}
?>