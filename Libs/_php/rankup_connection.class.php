<?php
##디비 커넥션을 위한 클래스
##단독 클래스로, 이 클래스를 이용하여 디비 커넥션을 수행
global $license_key;
$license_key= "";
if(!class_exists("rankup_connection")):
class rankup_connection{
	var $db_host		= "localhost";
	var $db_name		= "new_compr";
	var $db_id			= "new_compr";
	var $db_passwd	= "@com@crm.co.kr.";
	var $server_path = "/home/compr/public_html/"; // 서버 절대 경로
	var $server_addr = "121.254.171.111"; // 서버 아이피 정보
	var $db_conn; // 데이터 베이스 커넥션이 이루어 졌을때,반환되는 connection ID
	var $ts_guide = '<span style="font-size:9pt;font-family:verdana"><b style="color:red">Fatal error) %s 변경되었거나 올바르지 않아 구동이 중단되었습니다. 랭크업에 문의하시기 바랍니다. (☎.1544-6862)<br>　　　　　　 영업일·업무시간 외에는 기술지원이 불가능한 점 양해 말씀 드립니다. (<a href="http://rankup.co.kr/" target="_blank">http://rankup.co.kr</a>)</b><br><br><b>▼ 영업일·업무시간안내</b><br>　 월~금 : 오전9시 ~ 오후6시<br>　 토 : 오전9시 ~ 오후4시<br><br><b style="color:#3366FF">▼ 서버정보 변경하기</b> <span id="guide">(다음 정보들을 랭크업 홈페이지에서 변경하십시오)<br>　 ① 서버아이피 : %s<br>　 ② 절대경로 : %s<br>　 ③ DB 호스트: <br>　 ④ DB 명 : <br>　 ⑤ DB 사용자(ID) : <br>　 ⑥ DB 비밀번호 : <br><br>☞ <a href="http://rankup.co.kr/mypage/solution.html?myMode=solution_mode" target="_blank">정보변경 바로가기</a></span></span>';
	// 생성자로, 설치 정보가 올바르게 입력되었는지만 체크
	function rankup_connection() {
		//설치시 주석 해제
		//$this->check_connect_info();
		// DB 비밀번호를 변경할 수 있도록 외부설정을 제공
		@include "db_passwd.inc.php";
		if(!empty($db_passwd)) $this->db_passwd = $db_passwd;
	}
	//서버정보를 체크한다.
	function check_connect_info($check_confirm = true) {
		global $license_key;
		ob_start();
		@include "license_key.inc";
		$license_datas = ob_get_clean();
		$_infos = explode("^|^", $this->decrypt_md5(base64_decode(trim($license_datas)), $license_key));
		// 설치정보 체크
		if(getEnv('SERVER_ADDR')!=$_infos[1] || __FILE__!=$_infos[0]."Libs/_php/rankup_connection.class.php" || empty($_infos[2]) || empty($_infos[3]) || empty($_infos[4]) || empty($_infos[5])) {
			$send_data = "&license_key=".$license_key;
			if($check_confirm) {
				$datas = $this->rankup_server_infos($send_data);
				$this->connection_write($datas);
				$this->check_connect_info(false);
			}
			else die(sprintf($this->ts_guide, '설치정보(서버IP,경로)가', $_SERVER['SERVER_ADDR'], $_SERVER['DOCUMENT_ROOT']."/"));
		} else {
			$this->db_host = $_infos[2];
			$this->db_id = $_infos[3];
			$this->db_name = $_infos[4];
			$this->db_passwd = $_infos[5];
		}
	}
	//랭크업서버 정보 가져오기
	function rankup_server_infos($postdata) {
		$da = @fsockopen('rankup.co.kr', 80, $errno, $errstr, 3);
		if(!is_resource($da)) {
			return "설정정보가 올바르지 않습니다.";
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
	//라이센스정보 파일에 저장
	function connection_write($datas) {
		global $base_dir;
		$file_name = $base_dir."Libs/_php/license_key.inc";
		if(is_file($file_name)) {
			$_fp = @fopen($file_name, "w");
			$licens_datas = @fwrite($_fp, $datas);
			fclose($_fp);
		}
	}
	//복호화
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
	// 실제로 데이터 베이스 커넥션을 이루고, 이루어진 커넥션을 리턴.
	function connection(){
		$this->db_conn = @mysql_connect($this->db_host,$this->db_id,$this->db_passwd);
		if(!$this->db_conn) die(sprintf($this->ts_guide, 'DB정보가', $_SERVER['SERVER_ADDR'], $_SERVER['DOCUMENT_ROOT']."/"));
		if(!@mysql_select_db($this->db_name, $this->db_conn)) die(sprintf($this->ts_guide, 'DB명이', $_SERVER['SERVER_ADDR'], $_SERVER['DOCUMENT_ROOT']."/"));
		mysql_query("set names euckr");
		return $this->db_conn;
	}
}
endif;
?>
