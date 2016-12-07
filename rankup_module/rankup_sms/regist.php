<?php
include_once "../../Libs/_php/rankup_basic.class.php";
include_once './rankup_sms_config.class.php';

$rankup_control->check_admin($rankup_admin->is_admin(),1);
$rankup_sms_config = new rankup_sms_config();

$type=$_POST['type'];
$mode = $_POST['mode'];
if(!$type) $type = $_GET['type'];


## ȯ�漳�� ���� ###########################################################################
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


## �ʱ�ȭ #################################################################################
elseif($type=='init') :

	$result = $rankup_sms_config->init();

	if($result)
		echo "<script>alert('�ʱ�ȭ�Ǿ����ϴ�.');location.replace('./sms_config.html');</script>";
	else
		echo "<script>alert('�ʱ�ȭ�� �����Ͽ����ϴ�..');history.go(-1);</script>";


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


## ��ü sms �߼��� ��� ######################################################################
elseif($mode=='send_all') :
	include_once './rankup_sms.class.php';
	if($_POST['agency']) {
		$_POST['msg'] = iconv("UTF-8", "CP949", $_POST['msg']);
		$_POST['agency'] = iconv("UTF-8", "CP949", $_POST['agency']);
		$_POST['msg'] = str_replace("{��ü}", $_POST['agency'], $_POST['msg']);
		$_POST['msg'] = iconv("CP949", "UTF-8", $_POST['msg']);
	}
	$rankup_sms = new rankup_sms($mode);
	$use_sms = $rankup_sms->queryR("select use_sms from rankup_sms_config");
	if($use_sms != 'yes') :
		echo "���� sms ������ ������� �������� �Ǿ� �ֽ��ϴ�.";
		exit;
	endif;
	//�̺κп� ������ �޽����� �����ؾ� ��.
	//id�� name�� �⺻������ ����. ���Ŀ� ������ �κе��� �ٽ� ����
	$rankup_sms->set_merge(array("id"=>"{���̵�}","name"=>"{�̸�}"), "send_all");
	$rankup_sms->set_value("user_id", "administrator"); // SMS�ַ�Ƕ����� �߰� :: ������ ����
	$result = $rankup_sms->send_post($rankup_sms->data);
	//echo $result;
	//$pos = strpos($result,"result:ok");
	//$pos2 = strpos($result, "result:invalid_info");

	if(eregi("result:invalid_info",$result)) :
		echo "�Է��Ͻ� ������ ��ġ ���� �ʽ��ϴ�.";
	elseif(eregi("result:empty_info",$result)) :
		echo "������ �����Ǿ����ϴ�.";
	elseif(eregi("result:empty_money",$result)) :
		echo "���̹� �Ӵϸ� �����ϱ�� �ٶ��ϴ�.";
	elseif(eregi("result:ok",$result) || !eregi("result:0",$result)) : // SMS �ַ�� ������ ������ �κ�
		echo "��ü sms�� �߼۵Ǿ����ϴ�.";
	elseif(eregi("result:limit_nums",$result)) :
		echo "�Ϸ� ���۷��� �ʰ� �Ͽ����ϴ�.\n��ũ���� ���� �Ͻñ� �ٶ��ϴ�.(1544-6862)";
	elseif(eregi("result:limit_allow",$result)) :
		echo "���� sms������ �ź� �Ǿ����ϴ�.\n��ũ���� ���� �Ͻñ� �ٶ��ϴ�.(1544-6862)";
	else :
		echo "no";
	endif;



## �ѻ�����Ը� �߼��ϴ� ��� ##################################################################
elseif($mode=='send_sms_one'):
	include_once './rankup_sms.class.php';


	$user = $_POST['user'];
	$rankup_sms = new rankup_sms($mode,$user);	//ȸ���̳� �����ڸ� �����ϵ��� �����Ǿ��� �����Ƿ�, �����ڿ��� ����Ҷ����� 'admin'�̶�� ���� �Ѱܾ� �Ѵ�.


	$phone123 = $_POST['phone123'];	//�޴� ��� ��ȭ ��ȣ
	$txtMessage = addslashes($_POST['txtMessage']);	//�޽���
	if(iconv("UTF-8","UTF-8",$txtMessage) == $txtMessage){
		$txtMessage = iconv("UTF-8", "euc-kr",$txtMessage);
	}
	$reqnumber = $_POST['reqnumber'];	//ȸ�� ��ȣ
	$calltype= $_POST['calltype'];	//���� ���� ���

	if($calltype == 2) {	//���� ���� ���� , �ð��� ����.
		$yy = $rankup_control->getParam("yy");
		$mm = $rankup_control->getParam("mm");
		$dd = $rankup_control->getparam("dd");
		$h = $rankup_control->getParam("h");
		$m = $rankup_control->getParam("m");

		$reserve_date = $yy.'-'.$mm.'-'.$dd.' '.$h.':'.$m.':00';
	}

	$rankup_sms->set_value($phone123, $name,$txtMessage, $reserve_date);
	$rankup_sms->callback = $reqnumber;
	//$result = $rankup_sms->send_msg();//������ �� �޼ҵ带 ����ϸ鼭 ������ �ϳ�, �޼ҵ尡 ��� ������� ������� �����Ƿ�, ���� �κп��� Ǯ�� ��ģ��.

	$post_data = '&type=send_sms&client_id='.$rankup_sms->id.'&client_pw='.$rankup_sms->passwd.'&domain='.$rankup_sms->domain.'&page='.$rankup_sms->page.'&callback='.$rankup_sms->callback.'&phone='.$rankup_sms->phone;
	$post_data .= '&msg='.$rankup_sms->msg.'&reqdate='.$rankup_sms->reqdate.'&user_id=administrator'; // ������ �����̱� ������ administrator ����

	$result = $rankup_sms->send_post($post_data);		//��� ���� ����Ѵ�.

	if(preg_match("/result:ok/",$result)) echo "sms�� �߼۵Ǿ����ϴ�.";
	else echo "sms �߼��� �����Ͽ����ϴ�.";
	exit;

endif;
?>