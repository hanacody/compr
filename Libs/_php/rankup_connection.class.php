<?php
##��� Ŀ�ؼ��� ���� Ŭ����
##�ܵ� Ŭ������, �� Ŭ������ �̿��Ͽ� ��� Ŀ�ؼ��� ����
global $license_key;
$license_key= "";
if(!class_exists("rankup_connection")):
class rankup_connection{
	var $db_host		= "localhost";
	var $db_name		= "new_compr";
	var $db_id			= "new_compr";
	var $db_passwd	= "@com@crm.co.kr.";
	var $server_path = "/home/compr/public_html/"; // ���� ���� ���
	var $server_addr = "121.254.171.111"; // ���� ������ ����
	var $db_conn; // ������ ���̽� Ŀ�ؼ��� �̷�� ������,��ȯ�Ǵ� connection ID
	var $ts_guide = '<span style="font-size:9pt;font-family:verdana"><b style="color:red">Fatal error) %s ����Ǿ��ų� �ùٸ��� �ʾ� ������ �ߴܵǾ����ϴ�. ��ũ���� �����Ͻñ� �ٶ��ϴ�. (��.1544-6862)<br>������������ �����ϡ������ð� �ܿ��� ��������� �Ұ����� �� ���� ���� �帳�ϴ�. (<a href="http://rankup.co.kr/" target="_blank">http://rankup.co.kr</a>)</b><br><br><b>�� �����ϡ������ð��ȳ�</b><br>�� ��~�� : ����9�� ~ ����6��<br>�� �� : ����9�� ~ ����4��<br><br><b style="color:#3366FF">�� �������� �����ϱ�</b> <span id="guide">(���� �������� ��ũ�� Ȩ���������� �����Ͻʽÿ�)<br>�� �� ���������� : %s<br>�� �� ������ : %s<br>�� �� DB ȣ��Ʈ: <br>�� �� DB �� : <br>�� �� DB �����(ID) : <br>�� �� DB ��й�ȣ : <br><br>�� <a href="http://rankup.co.kr/mypage/solution.html?myMode=solution_mode" target="_blank">�������� �ٷΰ���</a></span></span>';
	// �����ڷ�, ��ġ ������ �ùٸ��� �ԷµǾ������� üũ
	function rankup_connection() {
		//��ġ�� �ּ� ����
		//$this->check_connect_info();
		// DB ��й�ȣ�� ������ �� �ֵ��� �ܺμ����� ����
		@include "db_passwd.inc.php";
		if(!empty($db_passwd)) $this->db_passwd = $db_passwd;
	}
	//���������� üũ�Ѵ�.
	function check_connect_info($check_confirm = true) {
		global $license_key;
		ob_start();
		@include "license_key.inc";
		$license_datas = ob_get_clean();
		$_infos = explode("^|^", $this->decrypt_md5(base64_decode(trim($license_datas)), $license_key));
		// ��ġ���� üũ
		if(getEnv('SERVER_ADDR')!=$_infos[1] || __FILE__!=$_infos[0]."Libs/_php/rankup_connection.class.php" || empty($_infos[2]) || empty($_infos[3]) || empty($_infos[4]) || empty($_infos[5])) {
			$send_data = "&license_key=".$license_key;
			if($check_confirm) {
				$datas = $this->rankup_server_infos($send_data);
				$this->connection_write($datas);
				$this->check_connect_info(false);
			}
			else die(sprintf($this->ts_guide, '��ġ����(����IP,���)��', $_SERVER['SERVER_ADDR'], $_SERVER['DOCUMENT_ROOT']."/"));
		} else {
			$this->db_host = $_infos[2];
			$this->db_id = $_infos[3];
			$this->db_name = $_infos[4];
			$this->db_passwd = $_infos[5];
		}
	}
	//��ũ������ ���� ��������
	function rankup_server_infos($postdata) {
		$da = @fsockopen('rankup.co.kr', 80, $errno, $errstr, 3);
		if(!is_resource($da)) {
			return "���������� �ùٸ��� �ʽ��ϴ�.";
		}
		else {
			$salida ="POST /RAD/solution_log/license_confirm.php HTTP/1.0\r\n";
			$salida.="Host: rankup.co.kr\r\n";
			$salida.="User-Agent: PHP Script\r\n";
			$salida.="Content-Type: application/x-www-form-urlencoded\r\n";
			$salida.="Content-Length: ".strlen($postdata)."\r\n\r\n";
			$salida.=$postdata;
			fwrite($da, $salida);
			while(!feof($da)) $get.=fgets($da, 128);
			$pattern="/^.*\r\n\r\n/s";
			$result=preg_replace($pattern,'',$get);
			return $result;
		}
	}
	//���̼������� ���Ͽ� ����
	function connection_write($datas) {
		global $base_dir;
		$file_name = $base_dir."Libs/_php/license_key.inc";
		if(is_file($file_name)) {
			$_fp = @fopen($file_name, "w");
			$licens_datas = @fwrite($_fp, $datas);
			fclose($_fp);
		}
	}
	//��ȣȭ
	function bytexor($a,$b,$ilimit) {
		$c="";
		for($i=0;$i<$ilimit;$i++) {
			$c .= $a{$i}^$b{$i};
		}
		return $c;
	}
	function decrypt_md5($msg,$key) {
		$string="";
		$buffer="";
		$key2="";
		while($msg){
			$key2=pack("H*",md5($key.$key2.$buffer));
			$dec_limit=strlen(substr($msg,0,16));
			$buffer=$this->bytexor(substr($msg,0,16),$key2,$dec_limit);
			$string.=$buffer;
			$msg=substr($msg,16);
		}
		return($string);
	}
	// ������ ������ ���̽� Ŀ�ؼ��� �̷��, �̷���� Ŀ�ؼ��� ����.
	function connection(){
		$this->db_conn = @mysql_connect($this->db_host,$this->db_id,$this->db_passwd);
		if(!$this->db_conn) die(sprintf($this->ts_guide, 'DB������', $_SERVER['SERVER_ADDR'], $_SERVER['DOCUMENT_ROOT']."/"));
		if(!@mysql_select_db($this->db_name, $this->db_conn)) die(sprintf($this->ts_guide, 'DB����', $_SERVER['SERVER_ADDR'], $_SERVER['DOCUMENT_ROOT']."/"));
		mysql_query("set names euckr");
		return $this->db_conn;
	}
}
endif;
?>
