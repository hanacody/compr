<?php
include_once "../_php/rankup_basic.class.php";
include_once 'kcaptcha.php';

$captcha = new KCAPTCHA();

if($_REQUEST[session_name()]){
	$_SESSION['captcha_keystring'] = $captcha->getKeyString();
}
?>