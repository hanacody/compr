<?php
if(is_file("../../Libs/_php/rankup_basic.class.php")) include_once "../../Libs/_php/rankup_basic.class.php";

/*
2009-11-25일 created

[ 파일설명 ]
우편번호 정보들을 디비로 밀어넣는 작업을 합니다.

[ 작업법 ]
1.파일을 올립니다. [ txt파일로 텝방식 ]
2.rankup.co.kr/rankup_module/rankup_post/post_dbchange.php?mode=dbchange 실행합니다.
*/
class post_change extends rankup_util {

	var $base_url, $base_dir;

	var $table = "rankup_zipcode";
	var $db_name, $db_id, $db_passwd, $db_host;

// : 생성자
	function post_change() {
		global $base_url, $base_dir;
		$this->base_url = $base_url;
		$this->base_dir = $base_dir;
		parent::rankup_util();
		$rankup_conn = new rankup_connection();
		$this->db_name = $rankup_conn->db_name;
		$this->db_id = $rankup_conn->db_id;
		$this->db_passwd = $rankup_conn->db_passwd;
		$this->db_host = $rankup_conn->db_host;
	}

// : txt, tab별로 분리된것.
	function post_create() {
@mysql_query("CREATE TABLE `$this->table` (
`ZIPCODE` varchar(7) default NULL,
`SIDO` varchar(4) default NULL,
`GUGUN` varchar(15) default NULL,
`DONG` varchar(52) default NULL,
`BUNJI` varchar(17) default NULL
)");
	}

// : 우편번호 변경하기.
	function post_db_change($filename) {
		// : 파일이 있을때만 실행합니다.
		if(is_file($filename)) {
			$post = file($filename);
			if(is_array($post)) {
				echo $file='db_zipcode_'.date("YmdHis").'.txt';
				$backup_url = exec("touch {$this->base_dir}rankup_module/rankup_post/backup/$file");
				mysql_query("drop table $this->table");
				$create = $this->post_create();
				echo '<pre>';
				foreach($post as $key=>$val) {
					if($key===0) continue;
					$record = explode("\t", $val);
					$insert = mysql_query("insert into $this->table set ZIPCODE='$record[0]', SIDO='$record[1]', GUGUN='$record[2]', DONG='$record[3]', BUNJI='$record[4]'");
					if(!$insert) exit;
				}
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
}

$post_change = new post_change();

switch($_REQUEST['mode']) {
	// : 디비 변환하기
	case "dbchange":
		$post_change->post_db_change($base_dir."rankup_module/rankup_post/zipcode_20090929(1).txt");
		break;
	// : 파일업로드 형식 디비 변환하기
	case "insertdb":
		$_postfile = $_FILES['postfile'];
		if ($_postfile['error'] == 1) {
			echo "
			<script type='text/javascript'>
				alert('업로드된 파일은 php.ini에서 정의한 upload_max_filesize를 초과하였습니다.');
				parent.location.reload();
			</script>";
		}
		if(!empty($_postfile['tmp_name'])) {
			$_ext = array_pop(explode(".", strtolower($_postfile['name'])));
			if(!empty($_ext)) $_ext = ".$_ext";
			$post_name = "db_zipcode__".date("YmdHis").$_ext;
			move_uploaded_file($_postfile['tmp_name'], $base_dir."RAD/PEG/".$post_name);
		}
		$result = $post_change->post_db_change($base_dir."RAD/PEG/".$post_name);
		if($result)
		{
			echo "
			<script type='text/javascript'>
				alert('성공적으로 업데이트되었습니다.');
				parent.location.reload();
			</script>";
		}
		else
		{
			echo "
			<script type='text/javascript'>
				alert('업데이트에 실패하였습니다. 다시 시도해주세요.');
				parent.location.reload();
			</script>";
		}
		break;
}
?>