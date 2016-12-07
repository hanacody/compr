<?php
include_once "../../Libs/_php/rankup_basic.class.php";
include_once './rankup_sms_config.class.php';

$rankup_control->check_admin($rankup_admin->is_admin(),1);
$rankup_sms_config = new rankup_sms_config();

$type=$_POST['type'];
$mode = $_POST['mode'];
if(!$type) $type = $_GET['type'];


## 환경설정 저장 ###########################################################################
if($type=='config') :
	$use_sms = $_POST['use_sms'];
	//$_POST=array_walk($_POST,array($rankup_sms_config,'strip_slash'));

	/*
	if($use_sms =='yes'){
		$client_id=$_POST['client_id'];
		$client_pw=$_POST['client_pw'];
		$callback=$_POST['callback'];
		$sleeping_mode=$_POST['sleeping_mode'];
		$sleeping_mode_time=$_POST['sleeping_mode_time'];

		$info=array("use_sms"=>'yes','client_id'=>$client_id,'client_pw'=>$client_pw,'callback'=>$callback,'sleeping_mode'=>$sleeping_mode,'sleeping_mode_time'=>$sleeping_mode_time);
	} else {
		$info = array('use_sms'=>'no');
	}
	*/

	$info=array("use_sms"=>$use_sms,'client_id'=>$client_id,'client_pw'=>$client_pw,'callback'=>$callback,'sleeping_mode'=>$sleeping_mode,'sleeping_mode_time'=>$sleeping_mode_time,'send_sms'=>$send_sms,'send_call'=>$send_call, 'send_authentic'=>$send_authentic);



	//$info = array_walk($info,array($rankup_sms_config,'add_slash'));

	$result = $rankup_sms_config->set_sms_config($info);

	if($result)
		echo 'yes';
	else
		echo 'no';

## @@@@@@@ ###########################################################################
elseif($type=='message') :
	$name=$_POST['name'];
	$use=$_POST['use'];
	$message=addslashes(stripslashes($_POST['message']));

	if(!strcmp($message,iconv('UTF-8','UTF-8',$message)))
		$message = iconv('UTF-8','euc-kr',$message);

	$name2 = "{$name}_msg";
	$info=array($name=>$use,$name2=>$message);
	$result = $rankup_sms_config->set_sms_config($info);
	//echo $result;exit;
	if($result) :
		echo 'yes';
	else :
		echo 'no';
	endif;


## 초기화 #################################################################################
elseif($type=='init') :

	$result = $rankup_sms_config->init();

	if($result)
		echo "<script>alert('초기화되었습니다.');location.replace('./sms_config.html');</script>";
	else
		echo "<script>alert('초기화가 실패하였습니다..');history.go(-1);</script>";


## @@@@@@@ ###########################################################################
elseif($type=='communication') :
	$client_id;
	$client_pw;
	$domain;
	$page;

?>
	<form name=send action = 'http://sms.rankup.co.kr/member_asp/reply_client.php' method='post'>
	<input type='hidden' name='client_id' value=$client_id>
	<input type='hidden' name='client_pw' value=$client_pw>
	<input type='hidden' name='domain' value=$domain>
	<input type='hidden' name='page' value=$page>
	</form>
	<script>document.send.submit();
	</script>

<?php


## 전체 sms 발송의 경우 ######################################################################
elseif($mode=='send_all') :
	include_once './rankup_sms.class.php';
	if($_POST['agency']) {
		$_POST['msg'] = iconv("UTF-8", "CP949", $_POST['msg']);
		$_POST['agency'] = iconv("UTF-8", "CP949", $_POST['agency']);
		$_POST['msg'] = str_replace("{업체}", $_POST['agency'], $_POST['msg']);
		$_POST['msg'] = iconv("CP949", "UTF-8", $_POST['msg']);
	}
	$rankup_sms = new rankup_sms($mode);
	$use_sms = $rankup_sms->queryR("select use_sms from rankup_sms_config");
	if($use_sms != 'yes') :
		echo "현재 sms 설정이 사용하지 않음으로 되어 있습니다.";
		exit;
	endif;
	//이부분에 가공할 메시지를 선택해야 함.
	//id와 name은 기본적으로 가공. 추후에 가공할 부분들은 다시 연결
	$rankup_sms->set_merge(array("id"=>"{아이디}","name"=>"{이름}"), "send_all");
	$rankup_sms->set_value("user_id", "administrator"); // SMS솔루션때문에 추가 :: 관리자 고정
	$result = $rankup_sms->send_post($rankup_sms->data);
	//echo $result;
	//$pos = strpos($result,"result:ok");
	//$pos2 = strpos($result, "result:invalid_info");

	if(eregi("result:invalid_info",$result)) :
		echo "입력하신 정보가 일치 하지 않습니다.";
	elseif(eregi("result:empty_info",$result)) :
		echo "정보가 누락되었습니다.";
	elseif(eregi("result:empty_money",$result)) :
		echo "사이버 머니를 충전하기시 바랍니다.";
	elseif(eregi("result:ok",$result) || !eregi("result:0",$result)) : // SMS 솔루션 때문에 수정된 부분
		echo "전체 sms가 발송되었습니다.";
	elseif(eregi("result:limit_nums",$result)) :
		echo "하루 전송량을 초과 하였습니다.\n랭크업에 문의 하시기 바랍니다.(1544-6862)";
	elseif(eregi("result:limit_allow",$result)) :
		echo "현재 sms전송이 거부 되었습니다.\n랭크업에 문의 하시기 바랍니다.(1544-6862)";
	else :
		echo "no";
	endif;



## 한사람에게만 발송하는 경우 ##################################################################
elseif($mode=='send_sms_one'):
	include_once './rankup_sms.class.php';


	$user = $_POST['user'];
	$rankup_sms = new rankup_sms($mode,$user);	//회원이나 관리자만 전송하도록 설정되어져 있으므로, 관리자에서 사용할때에는 'admin'이라는 값을 넘겨야 한다.


	$phone123 = $_POST['phone123'];	//받는 사람 전화 번호
	$txtMessage = addslashes($_POST['txtMessage']);	//메시지
	if(iconv("UTF-8","UTF-8",$txtMessage) == $txtMessage){
		$txtMessage = iconv("UTF-8", "euc-kr",$txtMessage);
	}
	$reqnumber = $_POST['reqnumber'];	//회신 번호
	$calltype= $_POST['calltype'];	//예약 전송 사용

	if($calltype == 2) {	//예약 전송 사용시 , 시간을 지정.
		$yy = $rankup_control->getParam("yy");
		$mm = $rankup_control->getParam("mm");
		$dd = $rankup_control->getparam("dd");
		$h = $rankup_control->getParam("h");
		$m = $rankup_control->getParam("m");

		$reserve_date = $yy.'-'.$mm.'-'.$dd.' '.$h.':'.$m.':00';
	}

	$rankup_sms->set_value($phone123, $name,$txtMessage, $reserve_date);
	$rankup_sms->callback = $reqnumber;
	//$result = $rankup_sms->send_msg();//원래는 이 메소드를 출력하면서 끝나야 하나, 메소드가 어떠한 결과값을 출력하지 않으므로, 밑의 부분에서 풀어 헤친다.

	$post_data = '&type=send_sms&client_id='.$rankup_sms->id.'&client_pw='.$rankup_sms->passwd.'&domain='.$rankup_sms->domain.'&page='.$rankup_sms->page.'&callback='.$rankup_sms->callback.'&phone='.$rankup_sms->phone;
	$post_data .= '&msg='.$rankup_sms->msg.'&reqdate='.$rankup_sms->reqdate.'&user_id=administrator'; // 관리자 전송이기 때문에 administrator 고정

	$result = $rankup_sms->send_post($post_data);		//결과 값을 출력한다.

	if(preg_match("/result:ok/",$result)) echo "sms가 발송되었습니다.";
	else echo "sms 발송이 실패하였습니다.";
	exit;

endif;
?>