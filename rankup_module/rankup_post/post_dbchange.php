<?php
if(is_file("../../Libs/_php/rankup_basic.class.php")) include_once "../../Libs/_php/rankup_basic.class.php";

/*
2009-11-25�� created

[ ���ϼ��� ]
�����ȣ �������� ���� �о�ִ� �۾��� �մϴ�.

[ �۾��� ]
1.������ �ø��ϴ�. [ txt���Ϸ� �ܹ�� ]
2.rankup.co.kr/rankup_module/rankup_post/post_dbchange.php?mode=dbchange �����մϴ�.
*/
class post_change extends rankup_util {

	var $base_url, $base_dir;

	var $table = "rankup_zipcode";
	var $db_name, $db_id, $db_passwd, $db_host;

// : ������
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

// : txt, tab���� �и��Ȱ�.
	function post_create() {
@mysql_query("CREATE TABLE `$this->table` (
`ZIPCODE` varchar(7) default NULL,
`SIDO` varchar(4) default NULL,
`GUGUN` varchar(15) default NULL,
`DONG` varchar(52) default NULL,
`BUNJI` varchar(17) default NULL
)");
	}

// : �����ȣ �����ϱ�.
	function post_db_change($filename) {
		// : ������ �������� �����մϴ�.
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
	// : ��� ��ȯ�ϱ�
	case "dbchange":
		$post_change->post_db_change($base_dir."rankup_module/rankup_post/zipcode_20090929(1).txt");
		break;
	// : ���Ͼ��ε� ���� ��� ��ȯ�ϱ�
	case "insertdb":
		$_postfile = $_FILES['postfile'];
		if ($_postfile['error'] == 1) {
			echo "
			<script type='text/javascript'>
				alert('���ε�� ������ php.ini���� ������ upload_max_filesize�� �ʰ��Ͽ����ϴ�.');
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
				alert('���������� ������Ʈ�Ǿ����ϴ�.');
				parent.location.reload();
			</script>";
		}
		else
		{
			echo "
			<script type='text/javascript'>
				alert('������Ʈ�� �����Ͽ����ϴ�. �ٽ� �õ����ּ���.');
				parent.location.reload();
			</script>";
		}
		break;
}
?>