<?php
include_once "../../Libs/_php/rankup_basic.class.php";
include_once "../../Libs/_php/rankup_cooperation.class.php";
$rankup_control->check_admin();

$type = $rankup_control->getParam('type');//�̸����� ���� �з�(��(callcenter), ���޾�ü(cooperation)
$mode = $rankup_control->getParam('mode');	//�̸����� ����������, ���� �̸����� Ȯ���� �������� ����
$no = $rankup_control->getParam('no');

if($type=='cooperation') $info=$rankup_control->get_cooperation_list($no); //���޾�ü
else if($type=='callcenter') $info=$rankup_control->get_callcenter_list($no); //�ݼ���
else if($type=='consult') {
	include_once $base_dir."Libs/_php/rankup_estate_admin.class.php";
	$rankup_estate_admin = new rankup_estate_admin;
	$info = $rankup_estate_admin->init_consult_form($no); // �Ź��Ƿ�
	$info['email'] = strip_tags($info['email']);
}

/*-----------------------------------------------------------------------
���� �����̸� �̸����� ������ ���ٴ� ��� �޽��� �����ִ� �κ�.
------------------------------------------------------------------------*/
$is_demo = false; // =rankup_basic::is_demo();
if(!$is_demo) $demovalue = "<input type=image src=./img/b_mail_send.gif border='0'>";
else $demovalue = "<img src=./img/b_mail_send.gif onclick=\"javascript:alert('���Ĺ����� ������ �߼��Ҽ� �ֽ��ϴ�.')\">";

/*-----------------------------------------------------------------------
action=$PHP_SELF�̹Ƿ� �ϴ��� �̸����� ������ ���� �����ش�
submit�� �Ǹ� $mode�� ���� send�� �ٲ�� ����, �ٸ� ������ �����Ѵ�.
------------------------------------------------------------------------*/
if(!$mode) {

$rankup_control->print_admin_head('���� ������');
?>
<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0>
<script type='text/javascript' language='javascript' src='<?php echo $base_url?>Libs/_js/form.js'></script>
<script type='text/javascript' language='javascript' src='<?php echo $base_url.$wysiwyg_dir;?>wysiwyg.js'></script>
<table width='500' height="100%" border='0' cellpadding='0' cellspacing='0' align="center">
  <tr>
    <td><img src='./img/mail_send_bg01.gif'></td>
  </tr>
  <tr height="100%" valign="top">
    <td background='./img/mail_send_bg02.gif'>
      <table border=0 cellpadding=3 cellspacing=1 width=450 align='center'>
        <form name="sendFrm" action="formmail.php" method="POST" onSubmit="return (Wysiwyg.submit_start()&&validate(this))">
          <input type=hidden name=mode value=send>
	      <input type=hidden name=no value="<?= $no?>">
		  <input type=hidden name=type value="<?= $type?>">
          <tr>
            <td width="120">
              <div><img src='<?=$base_url?>Libs/_images/ic_dot1.gif'> ���� �̸��� �ּ�</div>
            </td>
            <td width="330">
			  <input type='hidden' name='to_name' value='<?= $info['name'];?>'>
              <input type='text' name='to' maxlength='40' size='40' required hname="���� �̸��� �ּ�" option="email" class='simpleform' value='<?= $info['email'];?>'>
            </td>
          </tr>
          <tr>
            <td>
              <div><img src='<?=$base_url?>Libs/_images/ic_dot1.gif'> �����»�� �̸�</div>
            </td>
            <td>
              <input type='text' name='name' maxlength='40' size='40' required hname="������ ��� �̸�" class='simpleform' value="<?=$config_info['site_name']?>">
            </td>
          </tr>
          <tr>
            <td>
              <div><img src='<?=$base_url?>Libs/_images/ic_dot1.gif'> �����»�� �̸���</div>
            </td>
            <td>
              <input type='text' name='mail' maxlength='40' size='40' required hname="�����»�� �̸���" option="email" class='simpleform' value="<?=$config_info['email']?>">
            </td>
          </tr>
          <tr>
            <td>
              <div><img src='<?=$base_url?>Libs/_images/ic_dot1.gif'> �̸��� ����</div>
            </td>
            <td>
              <input type='text' name='subject' maxlength='50' size='40' required hname="�̸��� ����" class='simpleform' >
            </td>
          </tr>
          <tr>
            <td colspan=2>
                <textarea name='contents' type="editor" rows='5' style="width:450px;height:260px" required hname="����" nofocus></textarea>
            </td>
          </tr>
          <tr>
            <td colspan=2 >
              <div align="center">
                <table width='200' border='0' cellspacing='5' cellpadding='0'>
                  <tr align="center">
                    <td>
	                    <?= $demovalue;?>
					</td>
                    <td>
						<a href='JavaScript:window.close();'> <img src='./img/b_mail_close.gif' border='0'> </a>
					</td>
                  </tr>
                </table>
              </div>
            </td>
          </tr>
        </form>
      </table>
    </td>
  </tr>
  <tr>
    <td><img src='./img/mail_send_bg03.gif'></td>
  </tr>
</table>
</body>
</html>
<?
/*-----------------------------------------------------------------------
�����̸��� ������ Ȯ���ϴ� ��. �����ϱ� ��ư�� ����.
------------------------------------------------------------------------*/
}
else if($mode=='show') {

$rankup_control->print_admin_head('���� ���� ����');
?>
<body style="margin:0px">
<script type='text/javascript' language='javascript' src='../../wysiwyg/wysiwyg.js'></script>
<table width='500' height="100%" border='0' cellpadding='0' cellspacing='0'>
<tr>
<td><img src='./img/mail_view.gif'></td>
</tr>
<tr height="100%" valign="top">
	<td background='./img/mail_send_bg02.gif'>
	<table border=0 cellpadding=3 cellspacing=2 width="450" align='center'>
	<tr>
		<td width="120">
			<div><img src='<?=$base_url?>Libs/_images/ic_dot1.gif'> ���� �̸��� �ּ�</div>
		</td>
		<td width="330">
			<input type='hidden' name='to_name' value='<?= $info['name'];?>'>
			<input type='text' name='to' maxlength='40' size='40' class='simpleform'  value='<?= $info['email']; ?>' readonly>
		</td>
	</tr>
	<tr>
		<td>
			<div><img src='<?=$base_url?>Libs/_images/ic_dot1.gif'> �����»�� �̸�</div>
		</td>
		<td>
			<input type='text' name='name' maxlength='40' size='40' class='simpleform'  value='<?= $info['sender']; ?>' readonly>
		</td>
	</tr>
	<tr>
		<td>
			<div><img src='<?=$base_url?>Libs/_images/ic_dot1.gif'> �����»�� �̸���</div>
		</td>
		<td>
			<input type='text' name='mail' maxlength='40' size='40' class='simpleform'  value='<?= $info['sender_mail']; ?>' readonly>
		</td>
	</tr>
	<tr>
		<td>
		<div><img src='<?=$base_url?>Libs/_images/ic_dot1.gif'> ���� ����</div>
		</td>
		<td>
			<input type='text' name='subject' maxlength='50' class='simpleform'  size='40' value='<?=  stripslashes($info['sender_subject']); ?>' readonly>
		</td>
	</tr>
	<tr>
		<td colspan=2>
			<div>
			<textarea name='contents' rows='5' type="editor" style="width:450px;height:260px" readonly><?=$info['sender_content']?></textarea>
			</div>
		</td>
	</tr>
	<tr>
		<td colspan=2 >
		<div align=center>
		<a href='JavaScript:window.close();'> <img src='./img/b_mail_close.gif' border='0'> </a>
	</div>
		</td>
	</tr>
	</table>
	</td>
</tr>
<tr>
<td><img src='./img/mail_send_bg03.gif'></td>
</tr>
</table>
</body>
</html>

<?php
}	//�̸��� ���� ���� ��

/*-----------------------------------------------------------------------
�̸����� ���� ������ ����. �Է��� ������ ��Ȯ�ϴٸ� �̸����� ������, ������ ���̽��� ������Ʈ �Ѵ�.
------------------------------------------------------------------------*/
else if($mode == 'send') {

	include_once $base_dir.$wysiwyg_dir."wysiwyg_Class.php";

	$to=$rankup_control->getParam('to');
	$mail=$rankup_control->getParam('mail');
	$subject=$rankup_control->getParam('subject');
	$contents=$rankup_control->getParam('contents');
	$to_name=$rankup_control->getParam('to_name'); // �߰� 2008.04.16 - ��ũ�� ������
	$name=$rankup_control->getParam('name');
	$no=$rankup_control->getParam('no');

	if(($msg=$rankup_control->make_valid_domain($to))) $rankup_control->popup_msg_js($msg);
	if(($msg=$rankup_control->make_valid_domain($mail))) $rankup_control->popup_msg_js($msg);
	if(!$subject) $rankup_control->popup_msg_js($rankup_control->empty_subject);
	if(!$contents) $rankup_control->popup_msg_js($rankup_control->empty_content);

	$contents = $Wysiwyg->wysiwyg_result_func($contents);
	$name = str_replace(":", '', stripslashes($name));
	$str_name = $name;
	$site_name = str_replace(":", '', $config_info['site_name']); // ���Ź��� ���͸�
	if(!$rankup_control->check_unicode($site_name)) $site_name = iconv('CP949', 'UTF-8', $site_name);
	$site_name = '=?UTF-8?B?'.base64_encode($site_name).'?=';
	if(!$rankup_control->check_unicode($name)) $name = iconv('CP949', 'UTF-8', $name);
	$name = '=?UTF-8?B?'.base64_encode($name).'?=';
	$domain = $config_info['domain'];
	$mailheaders = "From: $name<$mail>\r\nReply-to: $site_name<$mail>\r\nContent-type: text/html;charset=euc-kr";

	$main = "
	<html>
	<head><title></title>
	<link rel='stylesheet' href='{$domain}Libs/_style/rankup_style.css' type='text/css'>
	</head>
	<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0>
	<div align=center>
	  <table width='500' border='0' cellpadding='0' cellspacing='0'>
		<tr>
		  <td><img src='{$domain}rankup_module/rankup_environment/img/mail_view.gif'></td>
		</tr>
		<tr>
		  <td background='{$domain}rankup_module/rankup_environment/img/mail_send_bg02.gif'>
			<table width=90% border=0 bgcolor='dddddd' cellspacing=1 cellpadding=8 align='center'>
			  <tr>
				<td height=30 bgcolor='#f7f7f7'>
				 <b><font color='#5894CF'>�̸�:</b> $str_name <br>
				  <b>���� : $mail</b></td>
			  </tr>
			  <tr bgcolor='#f7f7f7'>
				<td align=left><b>�� �� : <font color=##0066CC>$subject</b></td>
			  </tr>
			  <tr bgcolor='#FFFFFF'>
				<td align=left>$contents</td>
			  </tr>
			</table>
		  </td>
		</tr>
		<tr>
		  <td><img src='{$domain}rankup_module/rankup_environment/img/mail_send_bg03.gif'></td>
		</tr>
	  </table>
	</div>
	</body>
	</html>";

	$rankup_control->send_mail($to, $subject, $main, $mailheaders);

	/*-----------------------------------------------------------------------
	���� �߼� ���θ� ��� �ִ´�.
	------------------------------------------------------------------------*/
	if($type=='consult') $result = $rankup_estate_admin->update_consult();
	else $result=$rankup_control->update_mail_content($name,$mail,$subject,$contents,$no);

	/*-----------------------------------------------------------------------
	������ �Ϸ�Ǹ�, ���Ϲ߼� �Ϸ� �޽����� �����ش�.
	------------------------------------------------------------------------*/
	if($result) {

		echo"
		<title>���Ϻ�����</title>
		<link rel='stylesheet' href='".$base_url."Libs/_style/rankup_style.css' type='text/css'>
		<body leftmargin=0 topmargin=0 marginwidth=0 marginheight=0 background='./img/mail_send_bg.gif'>
		<table width='500' border='0' cellpadding='0' cellspacing='0'>
		  <tr>
			<td><img src='./img/mail_send_bg01.gif'></td>
		  </tr>
		  <tr>
			<td background='./img/mail_send_bg02.gif'>
			<table align='center'>
			<tr><td align='center' height='50'>
			 <div align='center'><b>  $to_name < $to >�Կ��� ������ �߼��Ͽ����ϴ�.</div>
			</td></tr>
			<tr><td align='center'>
			<a href='JavaScript:opener.location.reload();self.close();'><img src='./img/b_mail_close.gif' border='0'></a></td></tr></table>
			</td>
		  </tr>
		 <tr>
			<td><img src='./img/mail_send_bg03.gif'></td>
		  </tr>
		</table>";

	}
	else {
		echo '<script>alert(\'���� ���� ����\');self.close();</script>';
	}
}

?>